<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bloodbank extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->load->model('unittype_model');
        $this->marital_status       = $this->config->item('marital_status');
        $this->payment_mode         = $this->config->item('payment_mode');
        $this->search_type          = $this->config->item('search_type');
        $this->blood_group          = $this->bloodbankstatus_model->get_product(null, null);
        $this->charge_type          = $this->customlib->getChargeMaster();
        $data["charge_type"]        = $this->charge_type;
        $this->agerange             = $this->config->item('agerange');
        $this->patient_login_prefix = "pat";
        $this->load->model(array('transaction_model'));
        $this->load->helper('customfield_helper');
        $this->load->helper('custom');
        $this->config->load('image_valid');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('blood_donor', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('donor_name', $this->lang->line('donor_name'), 'required');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'required');
        $this->form_validation->set_rules('blood_group', $this->lang->line('blood_group'), 'required');
        $this->form_validation->set_rules('date_of_birth', $this->lang->line('date_of_birth'), 'required');
        $custom_fields = $this->customfield_model->getByBelong('donor');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[donor][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        if ($this->form_validation->run() == false) {
            $msg = array(
                'donor_name'    => form_error('donor_name'),
                'date_of_birth' => form_error('date_of_birth'),
                'blood_group'   => form_error('blood_group'),
                'gender'        => form_error('gender'),
                'father_name'   => form_error('father_name'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                              = $custom_fields_value['id'];
                        $custom_fields_name                                            = $custom_fields_value['name'];
                        $error_msg2["custom_fields[donor][" . $custom_fields_id . "]"] = form_error("custom_fields[donor][" . $custom_fields_id . "]");
                    }
                }
            }
            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }
            $array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');
        } else {
            $blooddonor = array(
                'donor_name'            => $this->input->post('donor_name'),
                'date_of_birth'         => $this->customlib->dateFormatToYYYYMMDD($this->input->post('date_of_birth')),
                'blood_bank_product_id' => $this->input->post('blood_group'),
                'gender'                => $this->input->post('gender'),
                'father_name'           => $this->input->post('father_name'),
                'address'               => $this->input->post('address'),
                'contact_no'            => $this->input->post('contact_no'),
            );
            $insert_id          = $this->blooddonor_model->add($blooddonor);
            $custom_field_post  = $this->input->post("custom_fields[donor]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[donor][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $insert_id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function addIssuecomponent()
    {
        if (!$this->rbac->hasPrivilege('blood_issue', 'can_add')) {
            access_denied();
        }

        $id = $this->input->post('id');
        $this->form_validation->set_rules('date_of_issue', $this->lang->line('issue_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('reference', $this->lang->line('reference_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('bag_no', $this->lang->line('bag_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('charge_id', $this->lang->line('charge_name'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge_category'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('discount', $this->lang->line('discount'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('discount_percent', $this->lang->line('discount_percentage'), 'required|numeric');
        if (!isset($id)) {

            $this->form_validation->set_rules(
                'payment_amount', $this->lang->line('payment_amount'), array('required', 'xss_clean', 'valid_amount',
                    array('check_exists', array($this->bloodbankstatus_model, 'validate_paymentamount')),
                )
            );
            if ($this->input->post('payment_mode') == "Cheque") {
                $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_upload');
            }
        }

        $custom_fields = $this->customfield_model->getByBelong('component_issue');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[component_issue][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        if ($this->form_validation->run() == false) {

            $msg = array(
                'date_of_issue'    => form_error('date_of_issue'),
                'patient_id'       => form_error('patient_id'),
                'reference'        => form_error('reference'),
                'bag_no'           => form_error('bag_no'),
                'total'            => form_error('total'),
                'net_amount'       => form_error('net_amount'),
                'tax'              => form_error('tax'),
                'tax_percentage'   => form_error('tax_percentage'),
                'discount'         => form_error('discount'),
                'discount_percent' => form_error('discount_percent'),
                'charge_id'        => form_error('charge_id'),
                'charge_category'  => form_error('charge_category'),
            );

            if (!isset($id)) {
                $msg['cheque_no']      = form_error('cheque_no');
                $msg['cheque_date']    = form_error('cheque_date');
                $msg['payment_amount'] = form_error('payment_amount');
                $msg['document']       = form_error('document');
            }

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                        = $custom_fields_value['id'];
                        $custom_fields_name                                                      = $custom_fields_value['name'];
                        $error_msg2["custom_fields[component_issue][" . $custom_fields_id . "]"] = form_error("custom_fields[component_issue][" . $custom_fields_id . "]");
                    }
                }
            }

            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }
            $array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');
        } else {

            $case_reference_id = $this->input->post('case_reference_id');
            $issue_date        = $this->input->post('date_of_issue');
            $patient_id        = $this->input->post('patient_id');
            $is_tpa = $this->input->post('is_tpa');
            if($is_tpa=="" || $is_tpa==0){
                $organisation_id    = null;
                $insurance_validity = null;
                $insurance_id       = null;
            }else{           
                $organisation_id    = $this->input->post('organisation_id');
                $insurance_id    = $this->input->post('insurance_id');
                $insurance_validity = $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity'));
            }
            if (empty($organisation_id)) {
                $organisation_id = null;
            }
            $bloodissue        = array(
                'date_of_issue'        => $this->customlib->dateFormatToYYYYMMDDHis($issue_date, $this->time_format),
                'patient_id'           => $patient_id,
                'technician'           => $this->input->post('technician'),
                'reference'            => $this->input->post('reference'),
                'blood_donor_cycle_id' => $this->input->post('bag_no'),
                'generated_by'         => $this->session->userdata('hospitaladmin')['id'],
                'remark'               => $this->input->post('note'),
                'charge_id'            => $this->input->post('charge_id'),
                'standard_charge'      => $this->input->post('standard_charge'),
                'amount'               => $this->input->post('total'),
                'net_amount'           => $this->input->post('net_amount'),
                'tax_percentage'       => $this->input->post('tax_percentage'),
                'remark'               => $this->input->post('note'),
                'discount_percentage'  => $this->input->post('discount_percent'),
                'organisation_id'      => $organisation_id,
                'insurance_id'         => $insurance_id,
                'insurance_validity'   => $insurance_validity,
            );

            if ($case_reference_id != '') {
                $bloodissue['case_reference_id'] = $case_reference_id;
            }

            $chequedate       = $this->input->post('cheque_date');
            $cheque_date      = $this->customlib->dateFormatToYYYYMMDD($chequedate);
            $payment_section  = $this->config->item('payment_section');
            $transaction_data = array(
                'amount'       => $this->input->post('payment_amount'),
                'patient_id'   => $this->input->post('patient_id'),
                'section'      => $payment_section['blood_bank'],
                'type'         => 'payment',
                'payment_mode' => $this->input->post('payment_mode'),
                'payment_date' => $this->customlib->dateFormatToYYYYMMDDHis($issue_date, $this->time_format),
                'received_by'  => $this->session->userdata('hospitaladmin')['id'],
            );

            if (!empty($this->input->post('case_reference_id')) && $this->input->post('case_reference_id') != "") {
                $transaction_data['case_reference_id'] = $this->input->post('case_reference_id');
            }

            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }

            if ($this->input->post('payment_mode') == "Cheque") {
                $transaction_data['cheque_date']     = $cheque_date;
                $transaction_data['cheque_no']       = $this->input->post('cheque_no');
                $transaction_data['attachment']      = $attachment;
                $transaction_data['attachment_name'] = $attachment_name;
            }

            if (isset($id)) {
                $bloodissue['id'] = $id;
                $transaction_data = array();
            }

            $insert_id           = $this->bloodissue_model->add($bloodissue, $transaction_data);
            $blood_issue_details = $this->bloodissue_model->getcomponentDetail($insert_id);
            $custom_field_post   = $this->input->post("custom_fields[component_issue]");
            $custom_value_array  = array();
            if (!empty($custom_field_post)) {
                if ($id > 0) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[component_issue][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $id,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                } else {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[component_issue][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $insert_id,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                }
            }

            if (!empty($custom_value_array)) {
                if ($id > 0) {
                    $this->customfield_model->updateRecord($custom_value_array, $id, 'component_issue');
                } else {
                    $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                }
            }

            $charge_details = $this->notificationsetting_model->getchargeDetails($this->input->post('charge_id'));

			$date_issue	=	$this->customlib->dateFormatToYYYYMMDDHis($issue_date, $this->time_format);

            $event_data = array(
                'case_id'        => $case_reference_id,
                'patient_id'     => $patient_id,
                'bill_no'        => $this->customlib->getSessionPrefixByType('blood_bank_billing') . $insert_id,
                'issue_date'     => $this->customlib->YYYYMMDDHisTodateFormat($date_issue, $this->customlib->getHospitalTimeFormat()),
                'reference_name' => $this->input->post('reference'),
                'blood_group'    => $blood_issue_details['blood_group_name'],
                'component'      => $blood_issue_details['component_name'],
                'bag'            => $this->customlib->bag_string($blood_issue_details['bag_no'], $blood_issue_details['volume'], $blood_issue_details['unit']),
                'charge_name'    => $charge_details['name'],
                'total'          => number_format((float) $this->input->post('total'), 2, '.', ''),
                'discount'       => number_format((float) $this->input->post('discount_percent'), 2, '.', ''),
                'tax'            => $this->input->post('tax_percentage'),
                'net_amount'     => number_format((float) $this->input->post('net_amount'), 2, '.', ''),
            );

            $this->system_notification->send_system_notification('component_issue', $event_data);
            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function component_issue()
    {
        if (!$this->rbac->hasPrivilege('blood_issue', 'can_view')) {
            access_denied();
        }
        $doctors              = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]      = $doctors;
        $patients             = $this->patient_model->getPatientListall();
        $data["patients"]     = $patients;
        $data["payment_mode"] = $this->payment_mode;
        $data["charge_type"]  = $this->chargetype_model->get();
        $data["bloodgroup"]   = $this->bloodbankstatus_model->get_product('', 1);
        $result               = $this->bloodissue_model->getBloodIssue();
        $data['fields']       = $this->customfield_model->get_custom_fields('component_issue', 1);
        $data['result']       = $result;
        $data['organisation']   = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/bloodbank/component_issue', $data);
        $this->load->view('layout/footer');
    }
 
    public function search()
    {
        if (!$this->rbac->hasPrivilege('blood_donor', 'can_view')) {
            access_denied();
        }
        $data["payment_mode"] = $this->payment_mode;
        $data["bloodgroup"]   = $this->bloodbankstatus_model->get_product('', 1);
        $data["charge_type"]  = $this->chargetype_model->get();
        $data['unit_type']    = $this->unittype_model->get();
        $data['fields']       = $this->customfield_model->get_custom_fields('donor', 1);
        $this->load->view('layout/header');
        $this->load->view('admin/bloodbank/search', $data);
        $this->load->view('layout/footer');
    }

    public function getdonordatatable()
    {
        $fields      = $this->customfield_model->get_custom_fields('donor', 1);
        $dt_response = $this->blooddonor_model->getAlldonorRecord();
        $dt_response = json_decode($dt_response);

        $dt_data = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================

                $action = "<div class='rowoptionview rowview-mt-19'>";
                if ($this->rbac->hasPrivilege('blood_stock', 'can_add')) {
                    $action .= "<a href='#'  onclick='addDonorBlood(" . $value->id . "," . $value->blood_bank_product_id . ")' class='btn btn-default btn-xs addDonorBlood'  data-toggle='tooltip' title='" . $this->lang->line('add_bag_stock') . "'><i class='fa fa-plus-square' aria-hidden='true'></i></a>";
                }

                if ($this->rbac->hasPrivilege('blood_donor', 'can_view')) {
                    $action .= "<a href='#' onclick='viewDetail(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('donor_blood_show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";
                }

                $action .= "</div>";

                if ($this->rbac->hasPrivilege('blood_donor', 'can_view')) {
                    $first_action = "<a onclick='viewDetail(" . $value->id . ")' >";
                }
                //==============================
                $row[] = $first_action . $value->donor_name . "</a>" . $action;
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date_of_birth);
                $row[] = $value->blood_group;
                $row[] = $this->lang->line(strtolower($value->gender));
                $row[] = $value->contact_no;
                $row[] = $value->father_name;
                $row[] = $value->address;

                foreach ($fields as $fields_key => $fields_value) {

                    $custom_name   = $fields_value->name;
                    $display_field = $value->$custom_name;
                    if ($fields_value->type == "link") {
                        $display_field = "<a href=" . $value->$custom_name . " target='_blank'>" . $value->$custom_name . "</a>";

                    }
                    $row[] = $display_field;
                }
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getDetails()
    {
        if (!$this->rbac->hasPrivilege('blood_donor', 'can_view')) {
            access_denied();
        }
        $id                      = $this->input->get("blood_donor_id");
		
		
        $result                  = $this->blooddonor_model->getDetails($id);
        $result['age']           = $this->customlib->getAgeBydob($result['date_of_birth']);
		$result['custom_fields_value'] = display_custom_fields('donor', $id);   
        $result['dateofbirth']   = $this->customlib->YYYYMMDDTodateFormat($result['date_of_birth'], $this->customlib->getHospitalTimeFormat());
		

        echo json_encode($result);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('blood_donor', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('date_of_birth', $this->lang->line('date_of_birth'), 'required');
        $this->form_validation->set_rules('donor_name', $this->lang->line('donor_name'), 'required');
        $this->form_validation->set_rules('blood_group', $this->lang->line('blood_group'), 'required');
        $custom_fields = $this->customfield_model->getByBelong('donor');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[donor][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date_of_birth' => form_error('date_of_birth'),
                'donor_name'    => form_error('donor_name'),
                'age'           => form_error('age'),
                'blood_group'   => form_error('blood_group'),
                'gender'        => form_error('gender'),
                'father_name'   => form_error('father_name'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                              = $custom_fields_value['id'];
                        $custom_fields_name                                            = $custom_fields_value['name'];
                        $error_msg2["custom_fields[donor][" . $custom_fields_id . "]"] = form_error("custom_fields[donor][" . $custom_fields_id . "]");
                    }
                }
            }
            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }
            $array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');
        } else {
            $id         = $this->input->post('id');
            $blooddonor = array(
                'id'                    => $id,
                'donor_name'            => $this->input->post('donor_name'),
                'date_of_birth'         => $this->customlib->dateFormatToYYYYMMDD($this->input->post('date_of_birth')),
                'blood_bank_product_id' => $this->input->post('blood_group'),
                'gender'                => $this->input->post('gender'),
                'father_name'           => $this->input->post('father_name'),
                'address'               => $this->input->post('address'),
                'contact_no'            => $this->input->post('contact_no'),
            );
 
            $this->blooddonor_model->update($blooddonor);
            $custom_field_post  = $this->input->post("custom_fields[donor]");
            $custom_value_array = array();
            if ($custom_field_post) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[donor][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                if (!empty($custom_value_array)) {
                    $this->customfield_model->updateRecord($custom_value_array, $id, 'donor');
                }
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('blood_donor', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->blooddonor_model->deleteBloodDonor($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function getbatchbybloodgroup()
    {
        $bloodgroup = $this->input->post('bloodgroup');
        $batch_list = $this->blood_donorcycle_model->getBatchByBloodGroup($bloodgroup);
        $array      = array('status' => 1, 'batch_list' => $batch_list);
        echo json_encode($array);
    }

    public function getComponentBagNosIssue()
    {
        $bloodgroup = $this->input->post('blood_group_id');
        $component_id = $this->input->post('component_id');
        $batch_list = $this->blood_donorcycle_model->getComponentBagNosIssue($bloodgroup,$component_id);        
        $array      = array('status' => 1, 'batch_list' => $batch_list);
        echo json_encode($array);
    }

    public function getBloodBank()
    {
        if (!$this->rbac->hasPrivilege('blood_donor', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post('blood_donor_id');
        $result = $this->blooddonor_model->getBloodBank($id);
        echo json_encode($result);
    }

    public function addIssue()
    {
        if (!$this->rbac->hasPrivilege('blood_issue', 'can_add')) {
            access_denied();
        } 
        
        $id = $this->input->post('id');
        $this->form_validation->set_rules('date_of_issue', $this->lang->line('issue_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('reference', $this->lang->line('reference_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('bag_no', $this->lang->line('bag'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('charge_id', $this->lang->line('charge_name'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge_category'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('discount', $this->lang->line('discount'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('discount_percent', $this->lang->line('discount_percentage'), 'required|numeric');
        if (!isset($id)) {

            $this->form_validation->set_rules(
                'payment_amount', $this->lang->line('payment_amount'), array('trim', 'required', 'xss_clean', 'valid_amount',
                    array('check_exists', array($this->bloodbankstatus_model, 'validate_paymentamount')),
                )
            );

            if ($this->input->post('payment_mode') == "Cheque") {
                $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_upload');
            }
        }

        $custom_fields = $this->customfield_model->getByBelong('blood_issue');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[blood_issue][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'date_of_issue'    => form_error('date_of_issue'),
                'patient_id'       => form_error('patient_id'),
                'reference'        => form_error('reference'),
                'bag_no'           => form_error('bag_no'),
                'total'            => form_error('total'),
                'net_amount'       => form_error('net_amount'),
                'tax'              => form_error('tax'),
                'tax_percentage'   => form_error('tax_percentage'),
                'discount'         => form_error('discount'),
                'discount_percent' => form_error('discount_percent'),
                'charge_id'        => form_error('charge_id'),
                'charge_category'  => form_error('charge_category'),
            );
            if (!isset($id)) {
                $msg['cheque_no']      = form_error('cheque_no');
                $msg['cheque_date']    = form_error('cheque_date');
                $msg['payment_amount'] = form_error('payment_amount');
                $msg['document']       = form_error('document');
            }
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[blood_issue][" . $custom_fields_id . "]"] = form_error("custom_fields[blood_issue][" . $custom_fields_id . "]");
                    }
                }
            }
            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }
            $array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');
        } else {
            $case_reference_id = $this->input->post('case_reference_id');
            $patient_id        = $this->input->post('patient_id');        
            
            $issue_date        = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date_of_issue'), $this->time_format);

            $is_tpa = $this->input->post('is_tpa');
            if($is_tpa=="" || $is_tpa==0){
                $organisation_id = null;
                $insurance_validity = null;
                $insurance_id = null;
            }else{           
                $organisation_id    = $this->input->post('organisation_id');
                $insurance_id       = $this->input->post('insurance_id');
                $insurance_validity = $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity'));
            }
            if (empty($organisation_id)) {
                $organisation_id = null;
            }            
            
            $bloodissue        = array(
                'date_of_issue'        => $issue_date,
                'patient_id'           => $patient_id,
                'technician'           => $this->input->post('technician'),
                'hospital_doctor'      => $this->input->post('consultant_doctor'),
                'reference'            => $this->input->post('reference'),
                'blood_donor_cycle_id' => $this->input->post('bag_no'),
                'generated_by'         => $this->session->userdata('hospitaladmin')['id'],
                'remark'               => $this->input->post('note'),
                'charge_id'            => $this->input->post('charge_id'),
                'standard_charge'      => $this->input->post('standard_charge'),
                'amount'               => $this->input->post('total'),
                'net_amount'           => $this->input->post('net_amount'),
                'tax_percentage'       => $this->input->post('tax_percentage'),
                'discount_percentage'  => $this->input->post('discount_percent'),
                'organisation_id'      => $organisation_id,
                'insurance_id'         => $insurance_id,
                'insurance_validity'   => $insurance_validity,
            );

            if ($case_reference_id != '') {
                $bloodissue['case_reference_id'] = $case_reference_id;                
            }

            $chequedate       = $this->input->post('cheque_date');
            $payment_section  = $this->config->item('payment_section');
            $cheque_date      = $this->customlib->dateFormatToYYYYMMDD($chequedate);
            $transaction_data = array(
                'patient_id'   => $patient_id,
                'section'      => $payment_section['blood_bank'],
                'amount'       => $this->input->post('payment_amount'),
                'type'         => 'payment',
                'payment_mode' => $this->input->post('payment_mode'),
                'note'         => $this->input->post('note'),
                'payment_date' => $issue_date,
                'received_by'  => $this->session->userdata('hospitaladmin')['id'],
            );            
           
			if ($case_reference_id != '') {
                $transaction_data['case_reference_id'] = $case_reference_id;
            }
            
            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }

            if ($this->input->post('payment_mode') == "Cheque") {
                $transaction_data['cheque_date']     = $cheque_date;
                $transaction_data['cheque_no']       = $this->input->post('cheque_no');
                $transaction_data['attachment']      = $attachment;
                $transaction_data['attachment_name'] = $attachment_name;
            }

            if (isset($id)) {
                $bloodissue['id'] = $id;
                $transaction_data = array();
            }
            $insert_id          = $this->bloodissue_model->add($bloodissue, $transaction_data);
            $custom_field_post  = $this->input->post("custom_fields[blood_issue]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                if ($id > 0) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[blood_issue][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $id,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                } else {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[blood_issue][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $insert_id,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                }
            }

            if (!empty($custom_value_array)) {
                if ($id > 0) {
                    $this->customfield_model->updateRecord($custom_value_array, $id, 'blood_issue');
                } else {
                    $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                }
            }

            $charge_details = $this->notificationsetting_model->getchargeDetails($this->input->post('charge_id'));
            $issue_details  = $this->bloodissue_model->getDetail($insert_id);

            $event_data = array(
                'case_id'        => $case_reference_id,
                'patient_id'     => $patient_id,
                'bill_no'        => $this->customlib->getSessionPrefixByType('blood_bank_billing') . $insert_id,
                'issue_date'     => $this->input->post('date_of_issue'),
                'reference_name' => $this->input->post('reference'),
                'blood_group'    => $issue_details['blood_group'],
                'bag'            => $issue_details['bag_no'] . " (" . $issue_details['volume'] . " " . $issue_details['unit_name'] . ")",
                'charge_name'    => $charge_details['name'],
                'total'          => $this->input->post('total'),
                'discount'       => $this->input->post('discount_percent'),
                'tax'            => $this->input->post('tax_percentage'),
                'net_amount'     => $this->input->post('net_amount'),
            );

            $this->system_notification->send_system_notification('blood_issue', $event_data);
            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {

            $file_type         = $_FILES["document"]['type'];
            $file_size         = $_FILES["document"]["size"];
            $file_name         = $_FILES["document"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['document']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
				
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function editIssueBlood()
    {
        $data                    = array();
        $id                      = $this->input->post('id');
        $doctors                 = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]         = $doctors;
        $patients                = $this->patient_model->getPatientListall();
        $data["patients"]        = $patients;
        $data["payment_mode"]    = $this->payment_mode;
        $data["charge_type"]     = $this->chargetype_model->get();
        $data["stockbloodgroup"] = $this->bloodbankstatus_model->get_stock_bloodgroup();
        $data['result']          = $this->bloodissue_model->getDetail($id);
        $page                    = $this->load->view('admin/bloodbank/_editissueblood', $data, true);
        $organisation_id         = $data['result']['organisation_id'];
        echo json_encode(array('status' => 1, 'case_id' => $data['result']['case_reference_id'], 'page' => $page,'organisation_id'=>$organisation_id));
    }

    public function editIssuecomponent()
    {
        $data                    = array();
        $id                      = $this->input->post('id');
        $doctors                 = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]         = $doctors;
        $patients                = $this->patient_model->getPatientListall();
        $data["patients"]        = $patients;
        $data["payment_mode"]    = $this->payment_mode;
        $data["charge_type"]     = $this->chargetype_model->get();
        $data["stockbloodgroup"] = $this->bloodbankstatus_model->get_stock_bloodgroup();
        $data['result']          = $this->bloodissue_model->getcomponenteditDetail($id);
        $organisation_id         = $data['result']['organisation_id'];
        $page                    = $this->load->view('admin/bloodbank/_editissuecomponent', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page,'organisation_id'=>$organisation_id));
    }

    public function issueblood()
    {
        $data                    = array();
        $doctors                 = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]         = $doctors;
        $patients                = $this->patient_model->getPatientListall();
        $data["patients"]        = $patients;
        $data["payment_mode"]    = $this->payment_mode;
        $data["charge_type"]     = $this->chargetype_model->get();
        $data["stockbloodgroup"] = $this->bloodbankstatus_model->get_stock_bloodgroup();
        $page                    = $this->load->view('admin/bloodbank/_issueblood', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function issuecomponent()
    {
        $data                    = array();
        $doctors                 = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]         = $doctors;
        $patients                = $this->patient_model->getPatientListall();
        $data["patients"]        = $patients;
        $data["payment_mode"]    = $this->payment_mode;
        $data["charge_type"]     = $this->chargetype_model->get();
        $data["stockbloodgroup"] = $this->bloodbankstatus_model->get_stock_bloodgroup();
        $data["blood_component"] = $this->bloodbankstatus_model->get_product(null, 2);
        $page                    = $this->load->view('admin/bloodbank/_issuecomponent', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function issuebloodFront()
    {
        $data                    = array();
        $doctors                 = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]         = $doctors;
        $patients                = $this->patient_model->getPatientListall();
        $data["patients"]        = $patients;
        $data["payment_mode"]    = $this->payment_mode;
        $data["charge_type"]     = $this->chargetype_model->get();
        $data["stockbloodgroup"] = $this->bloodbankstatus_model->get_stock_bloodgroup();
        $data["payment_mode"]    = $this->payment_mode;
        $page                    = $this->load->view('admin/bloodbank/_issuebloodFront', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    } 

    public function issuecomponentfront()
    {
        $data                 = array();
        $doctors              = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]      = $doctors;
        $patients             = $this->patient_model->getPatientListall();
        $data["patients"]     = $patients;
        $data["payment_mode"] = $this->payment_mode;
        $data["charge_type"]  = $this->chargetype_model->get();
        $data["bloodgroup"]   = $this->bloodbankstatus_model->get_product(null, 2);
        $data["payment_mode"] = $this->payment_mode;
        $page                 = $this->load->view('admin/bloodbank/_issuecomponentFront', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function issue()
    {
        if (!$this->rbac->hasPrivilege('blood_issue', 'can_view')) {
            access_denied();
        }
        $doctors              = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]      = $doctors;
        $patients             = $this->patient_model->getPatientListall();
        $data["patients"]     = $patients;
        $data["payment_mode"] = $this->payment_mode;
        $data["charge_type"]  = $this->chargetype_model->get();
        $data["bloodgroup"]   = $this->bloodbankstatus_model->get_product('', 1);
        $data['fields']       = $this->customfield_model->get_custom_fields('blood_issue', 1); 
        $result               = $this->bloodissue_model->getBloodIssue();
        $data['result']       = $result;
        $data['organisation']  = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/bloodbank/bloodissue', $data);
        $this->load->view('layout/footer');
    }

    public function getbloodissueDatatable()
    {
        $fields      = $this->customfield_model->get_custom_fields('blood_issue', 1);
        $dt_response = $this->bloodissue_model->getAllbloodissueRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();

        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='#' data-record-id='" . $value->id . "' class='btn btn-default btn-xs viewDetail' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";

                $action .= "<a href='javascript:void(0)'  data-caseid='' data-module='blood_bank' data-record-id='" . $value->id . "' class='btn btn-default btn-xs add_payment' data-toggle='tooltip' title='" . $this->lang->line('add_payment') . "' ><i class='fa fa-plus'></i></a>";

                if ($this->rbac->hasPrivilege('blood_issue', 'can_delete')) {
                    $action .= "<a  class='btn btn-default btn-xs delete_blood_issue' data-toggle='tooltip' title='" . $this->lang->line('delete_payment') . "' data-record-id='" . $value->id . "'  data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $action .= "</div>";
                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value->id . $action;
                $row[] = $value->case_reference_id;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->date_of_issue, false);
                $row[] = $value->patient_name . " (" . $value->patient_id . ")";
                $row[] = $value->blood_group;
                $row[] = $this->lang->line(strtolower($value->gender));   
                $row[] = $value->donor_name;
                $row[] = $this->customlib->bag_string($value->bag_no, $value->volume, $value->unit);
                //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";

                        }
                        $row[] = $display_field;
                    }
                }
                //====================
                $row[]     = $value->net_amount;
                $row[]     = $value->paid_amount;
                $row[]     = amountFormat($value->net_amount - $value->paid_amount);
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getcomponentissueDatatable()
    {
        $fields      = $this->customfield_model->get_custom_fields('component_issue', 1);
        $dt_response = $this->bloodissue_model->getAllcomponentissueRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='#' data-record-id='" . $value->id . "' class='btn btn-default btn-xs viewDetail'  data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('blood_bank_partial_payment', 'can_view')) {
                    $action .= "<a href='javascript:void(0)'  data-caseid='' data-module='blood_bank' data-record-id='" . $value->id . "' class='btn btn-default btn-xs add_payment' data-toggle='tooltip' title='" . $this->lang->line('add_payment') . "' ><i class='fa fa-plus'></i></a>";
                }
                if ($this->rbac->hasPrivilege('issue_component', 'can_delete')) {
                    $action .= "<a  class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='delete_recordById(\"" . 'admin/bloodbank/deleteIssue/' . "$value->id\", \"" . $this->lang->line('delete_message') . "\")' data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }
                $action .= "</div>";
                $prefix = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value->id . $action;

                //==============================
                $row[] = $prefix;
                $row[] = $value->case_reference_id;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->date_of_issue, false);
                $row[] = $value->patient_name . " (" . $value->patient_id . ")";
                $row[] = $value->blood_group_name;
                $row[] = $value->component_name;
                $row[] = $this->lang->line(strtolower($value->gender)); 
                $row[] = $value->donor_name;
                $row[] = $this->customlib->bag_string($value->bag_no, $value->volume, $value->unit);
                //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";

                        }
                        $row[] = $display_field;
                    }
                }
                //====================
                $row[]     = $value->net_amount;
                $row[]     = $value->paid_amount;
                $row[]     = amountFormat($value->net_amount - $value->paid_amount);
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getBillDetails($id)
    {
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details         = $this->printing_model->get('', 'bloodbank');
        $data["print_details"] = $print_details;
        $data['result']        = $this->bloodbankstatus_model->getBillDetailsBloodbank($id);
        $this->load->view('admin/bloodbank/printBill', $data);
    }

     public function getComponentBillDetails($id)
    {
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details         = $this->printing_model->get('', 'bloodbank');
        $data["print_details"] = $print_details; 
        $data['result']=$this->bloodissue_model->getcomponentDetail($id);
        $data['result']['blood_group']=$data['result']['blood_group_name'];
        $this->load->view('admin/bloodbank/printBill', $data);
    }

    public function getBloodIssueDetail()
    {
        if (!$this->rbac->hasPrivilege('blood_issue', 'can_view')) {
            access_denied();
        }
        $id                  = $this->input->post("blood_issue_id");
        $data['result']      = $this->bloodissue_model->getDetail($id);
        $data['fields']      = $this->customfield_model->get_custom_fields('blood_issue');
        $data['bill_prefix'] = $this->customlib->getSessionPrefixByType('blood_bank_billing');
        $action              = "";
        $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $id . "' class='printIssueBill'  data-toggle='tooltip' title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";
        if ($this->rbac->hasPrivilege('blood_issue', 'can_edit')) {

            $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $id . "' class='edit_blood_issue' data-toggle='tooltip' title='" . $this->lang->line('edit') . "' ><i class='fa fa-pencil'></i></a>";

        }
        if ($this->rbac->hasPrivilege('blood_issue', 'can_delete')) {

            $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $id . "' class='delete_blood_issue' data-toggle='tooltip' title='" . $this->lang->line('delete') . "' ><i class='fa fa-trash'></i></a>";
        }

        $page = $this->load->view('admin/bloodbank/_getBloodIssueDetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'action' => $action));
    }

    public function getComponentIssueDetail()
    {
        if (!$this->rbac->hasPrivilege('blood_issue', 'can_view')) {
            access_denied();
        }
        $id             = $this->input->post("blood_issue_id");
        $data['result'] = $this->bloodissue_model->getcomponentDetail($id);        
        $data['prefix'] = $this->customlib->getSessionPrefixByType('blood_bank_billing');
        $data['fields'] = $this->customfield_model->get_custom_fields('component_issue');
        $action         = "";
        $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $id . "' class='printcomponentIssueBill'  data-toggle='tooltip' title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";
        if ($this->rbac->hasPrivilege('issue_component', 'can_edit')) {
            $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $id . "' class='edit_component_issue' data-toggle='tooltip' title='" . $this->lang->line('edit') . "' ><i class='fa fa-pencil'></i></a>";
        }
        if ($this->rbac->hasPrivilege('issue_component', 'can_delete')) {
            $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $id . "' class='delete_blood_issue' data-toggle='tooltip' title='" . $this->lang->line('delete') . "' ><i class='fa fa-trash'></i></a>";
        }
        $page = $this->load->view('admin/bloodbank/_getcomponentIssueDetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'action' => $action));
    }

    public function updateIssue()
    {
        if (!$this->rbac->hasPrivilege('blood_issue', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('date_of_issue', $this->lang->line('issue_date'), 'required');
        $this->form_validation->set_rules('recieve_to', $this->lang->line('receive_to'), 'required');
        $this->form_validation->set_rules('doctor', $this->lang->line('doctor_name'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required');
        $this->form_validation->set_rules('donor_name', $this->lang->line('donor_name'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'date_of_issue' => form_error('date_of_issue'),
                'recieve_to'    => form_error('recieve_to'),
                'doctor'        => form_error('doctor'),
                'amount'        => form_error('amount'),
                'donor_name'    => form_error('donor_name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id         = $this->input->post('id');
            $issue_date = $this->input->post("date_of_issue");
            $patient_id = $this->input->post('recieve_to');
            $bloodissue = array(
                'id'            => $id,
                'date_of_issue' => $this->customlib->dateFormatToYYYYMMDDHis($issue_date, $this->time_format),
                'recieve_to'    => $patient_id,
                'doctor'        => $this->input->post('doctor'),
                'technician'    => $this->input->post('technician'),
                'amount'        => $this->input->post('amount'),
                'donor_name'    => $this->input->post('donor_name'),
                'lot'           => $this->input->post('lot'),
                'bag_no'        => $this->input->post('bag_no'),
                'remark'        => $this->input->post('remark'),
            );
            $this->bloodissue_model->update($bloodissue);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function deleteIssue($id)
    {
        if (!$this->rbac->hasPrivilege('blood_issue', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->bloodissue_model->delete($id);
            $array = array('status' => 1, 'error' => '', 'msg' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 0, 'error' => '', 'msg' => $this->lang->line('something_went_wrong'));
        }
        echo json_encode($array);
    }

    public function getBloodIssue()
    {
        if (!$this->rbac->hasPrivilege('blood_issue', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post('bloodissue_id');
        $result = $this->bloodissue_model->getBloodIssue($id);
        echo json_encode($result);
    }

    public function donorCycle()
    {
        if (!$this->rbac->hasPrivilege('blood_donor', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('blood_donor_id', $this->lang->line('blood_donor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('bag_no', $this->lang->line('bag_no'), array('required', array('check_exists', array($this->blood_donorcycle_model, 'valid_check_exists'))));
        $this->form_validation->set_rules('donate_date', $this->lang->line('donate_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('charge_id', $this->lang->line('charge_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tax', $this->lang->line('tax'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard_charge'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('qty', $this->lang->line('qty'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_amount', $this->lang->line('payment_amount'), 'trim|required|valid_amount|xss_clean');
        if ($_POST['payment_mode'] == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line("document"), 'callback_handle_doc_upload[document]');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'blood_donor_id'   => form_error('blood_donor_id'),
                'donate_date'      => form_error('donate_date'),
                'bag_no'           => form_error('bag_no'),
                'quantity'         => form_error('quantity'),
                'charge_category'  => form_error('charge_category'),
                'charge_id'        => form_error('charge_id'),
                'tax'              => form_error('tax'),
                'standard_charge'  => form_error('standard_charge'),
                'qty'              => form_error('qty'),
                'apply_charge'     => form_error('apply_charge'),
                'net_amount'       => form_error('net_amount'),
                'discount_percent' => form_error('discount_percent'),
                'tax_percentage'   => form_error('tax_percentage'),
                'payment_amount'   => form_error('payment_amount'),
                'cheque_no'        => form_error('cheque_no'),
                'cheque_date'      => form_error('cheque_date'),
                'document'         => form_error('document'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id          = $this->input->post('blood_donor_id');
            $donate_date = $this->input->post('donate_date');
            $charge_id   = $this->input->post('charge_id');
            $donor_cycle = array(
                'blood_donor_id'        => $id,
                'blood_bank_product_id' => $this->input->post('blood_bank_product'),
                'institution'           => $this->input->post('institution'),
                'lot'                   => $this->input->post('lot'),
                'bag_no'                => $this->input->post('bag_no'),
                'volume'                => $this->input->post('volume'),
                'unit'                  => $this->input->post('unit'),
                'quantity'              => $this->input->post('quantity'),
                'donate_date'           => $this->customlib->dateFormatToYYYYMMDD($donate_date),
                'charge_id'             => $this->input->post('charge_id'),
                'standard_charge'       => $this->input->post('standard_charge'),
                'quantity'              => $this->input->post('qty'),
                'apply_charge'          => $this->input->post('total'),
                'amount'                => $this->input->post('net_amount'),
                'institution'           => $this->input->post('institution'),
                'note'                  => $this->input->post('note'),
                'discount_percentage'   => $this->input->post('discount_percent'),
                'tax_percentage'        => $this->input->post('tax_percentage'),
            );
            $payment_section  = $this->config->item('payment_section');
            $transaction_data = array(
                'amount'       => $this->input->post('payment_amount'),
                'section'      => $payment_section['blood_bank'],
                'type'         => 'payment',
                'payment_mode' => $this->input->post('payment_mode'),
                'payment_date' => $this->customlib->dateFormatToYYYYMMDDHis($donate_date),
                'received_by'  => $this->session->userdata('hospitaladmin')['id'],

            );

            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }
            $cheque_date = $this->input->post("cheque_date");
            if ($this->input->post('payment_mode') == "Cheque") {
                $transaction_data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $transaction_data['cheque_no']       = $this->input->post('cheque_no');
                $transaction_data['attachment']      = $attachment;
                $transaction_data['attachment_name'] = $attachment_name;
            }

            $this->blood_donorcycle_model->add($donor_cycle, $transaction_data);
            $charge_detail             = $this->notificationsetting_model->getchargeDetails($this->input->post('charge_id'));
            $blood_bank_product_detail = $this->notificationsetting_model->getblooddonorByID($this->input->post('blood_donor_id'));

            $event_data = array(
                'donor_name'  => $blood_bank_product_detail['donor_name'],
                'blood_group' => $blood_bank_product_detail['blood_group_name'],
                'contact_no'  => $blood_bank_product_detail['contact_no'],
                'donate_date' => $this->customlib->YYYYMMDDTodateFormat($this->input->post('donate_date')),
                'bag'         => $this->input->post('bag_no'),
                'charge_name' => $charge_detail['name'],
                'total'       => $this->input->post('total'),
                'discount'    => $this->input->post('discount_percent'),
                'tax'         => $this->input->post('tax_percentage'),
                'net_amount'  => $this->input->post('net_amount'),
            );

            $this->system_notification->send_system_notification('add_bag_stock', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getDonorBloodBatch()
    {
        if (!$this->rbac->hasPrivilege('blood_donor', 'can_view')) {
            access_denied();
        }

        $id                  = $this->input->post("blood_donor_id");
        $data["id"]          = $id;
        $data["blood_donor"] = $this->blooddonor_model->getDetails($id);
        $data['fields']      = $this->customfield_model->get_custom_fields('donor');
        $result              = $this->blood_donorcycle_model->getDonorBloodBatch($id);
        $data["result"]      = $result;
        $page                = $this->load->view('admin/bloodbank/_donorbloodbatch', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function blooddonorreport()
    {
        if (!$this->rbac->hasPrivilege('blood_donor_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/bloodbank');
        $this->session->set_userdata('subsub_menu', 'reports/bloodbank/blooddonorreport');
        $data["searchlist"]      = $this->search_type;
        $data["stockbloodgroup"] = $this->bloodbankstatus_model->get_stock_bloodgroup();
         $data['fields']      = $this->customfield_model->get_custom_fields('donor',1,'',1,'');  
		 
		 
        $this->load->view('layout/header');
        $this->load->view('admin/bloodbank/blooddonorreport', $data);
        $this->load->view('layout/footer');
    }

    public function checkvalidation()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type' => form_error('search_type'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'          => $this->input->post('search_type'),
                'amount_collected_by'  => $this->input->post('amount_collected_by'),
                'component_collect_by' => $this->input->post('component_collect_by'),
                'blood_collected_by'   => $this->input->post('blood_collected_by'),
                'blood_group'          => $this->input->post('blood_group'),
                'blood_donor'          => $this->input->post('blood_donor'),
                'date_from'            => $this->input->post('date_from'),
                'date_to'              => $this->input->post('date_to'),
                'blood_group'          => $this->input->post('blood_group'),
                'blood_component'      => $this->input->post('blood_component'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function checkvalidationblooddonor()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type' => form_error('search_type'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type' => $this->input->post('search_type'),
                'blood_group' => $this->input->post('blood_group'),
                'blood_donor' => $this->input->post('blood_donor'),
                'date_from'   => $this->input->post('date_from'),
                'date_to'     => $this->input->post('date_to'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function bloodIssueReport()
    {
        if (!$this->rbac->hasPrivilege('blood_issue_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/bloodbank');
        $this->session->set_userdata('subsub_menu', 'reports/bloodbank/bloodissuereport');
        $staffsearch             = $this->patient_model->getstaffbloodissuebill();
        $data['fields']          = $this->customfield_model->get_custom_fields('blood_issue', '', '', 1);
        $data['staffsearch']     = $staffsearch;
        $data["searchlist"]      = $this->search_type;
        $data["stockbloodgroup"] = $this->bloodbankstatus_model->get_stock_bloodgroup();
        $this->load->view('layout/header');
        $this->load->view('admin/bloodbank/bloodissuereport', $data);
        $this->load->view('layout/footer');
    }

    public function bloodbankreports()
    {
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        $search['search_type']            = $this->input->post('search_type');
        $search['date_from']              = $this->input->post('date_from');
        $search['date_to']                = $this->input->post('date_to');
        $start_date                       = '';
        $end_date                         = '';
        $condition['amount_collected_by'] = $this->input->post('amount_collected_by');
        $condition['blood_collected_by']  = $this->input->post('blood_collected_by');
        $condition['blood_group']         = $this->input->post('blood_group');
        $condition['blood_donor']         = $this->input->post('blood_donor');

        if ($search['search_type'] == 'period') {

            $condition['start_date'] = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $condition['end_date']   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);

        } else {

            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }

            $condition['start_date'] = $dates['from_date'];
            $condition['end_date']   = $dates['to_date'];
        }
        

        $fields     = $this->customfield_model->get_custom_fields('blood_issue', '', '', 1);
		
		 
        $reportdata = $this->transaction_model->bloodissuebillRecordSearch($condition); 
        $dt_response = json_decode($reportdata);
        $dt_data     = array();

        if (!empty($dt_response->data)) {
            $total_balance = 0;
            $total_paid    = 0;
            $total_charge  = 0;
            foreach ($dt_response->data as $key => $value) {

                $total_balance += (amountFormat($value->net_amount - $value->paid_amount));
                $total_paid += $value->paid_amount;
                $total_charge += $value->net_amount;
                $row   = array();
                $row[] = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value->id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date_of_issue, $this->time_format);
                $row[] = $value->patient_name . " (" . $value->patient_id . ")";
                $row[] = $value->blood_group;
                $row[] = $this->lang->line(strtolower($value->gender)) ;
                $row[] = $value->donor_name;
                $row[] = $this->customlib->bag_string($value->bag_no, $value->volume, $value->unit);
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = composeStaffNameByString($value->blood_collected_by_name, $value->blood_collected_by_surname, $value->blood_collected_by_employee_id);

                foreach ($fields as $fields_key => $fields_value) {

                    $custom_name   = $fields_value->name;
                    $display_field = $value->$custom_name;
                    if ($fields_value->type == "link") {
                        $display_field = "<a href=" . $value->$custom_name . " target='_blank'>" . $value->$custom_name . "</a>";

                    }
                    $row[] = $display_field;
                }

                $row[]     = amountFormat($value->net_amount, 2, '.', '') ;
                $row[]     = amountFormat($value->paid_amount, 2, '.', '') ; 
                $row[]     = amountFormat($value->net_amount - $value->paid_amount);
                $dt_data[] = $row;
            }
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            
            foreach ($fields as $fields_key => $fields_value) {                 
                $footer_row[] = '';
            }
                
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_charge, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_paid, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_balance, 2, '.', '')) . "<br/>";
            $dt_data[]    = $footer_row;
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function blooddonorreports()
    {
		 $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        $search['search_type']    = $this->input->post('search_type');
        $search['date_from']      = $this->input->post('date_from');
        $search['date_to']        = $this->input->post('date_to');
        $start_date               = '';
        $end_date                 = '';
        $condition['blood_group'] = $this->input->post('blood_group');
        $condition['blood_donor'] = $this->input->post('blood_donor');

        if ($search['search_type'] == 'period') {
            $condition['start_date'] = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $condition['end_date']   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);
        } else {
            if (isset($search['search_type']) && $search['search_type'] != '') {

                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }
            $condition['start_date'] = $dates['from_date'];
            $condition['end_date']   = $dates['to_date'];
        }

        $reportdata = $this->transaction_model->blooddonorRecord($condition);
	 
        $reportdata = json_decode($reportdata);
        $fields      = $this->customfield_model->get_custom_fields('donor', 1, '', 1, ''); 
        
        $dt_data = array();
        if (!empty($reportdata->data)) {
            $apply_charge = 0;
            $total_paid    = 0;
            $total_amount  = 0;
            
            foreach ($reportdata->data as $key => $value) {
                 
                $total_paid += $value->paid_amount;
                $total_amount += $value->amount;
                $apply_charge += $value->apply_charge;
                
                $row             = array();
                $discount_amount = calculatePercent($value->apply_charge, $value->discount_percentage);
                $row[]           = $value->blood_group;
                $row[]           = $this->customlib->bag_string($value->bag_no, $value->volume, $value->unit_name);
                $row[]           = $value->donor_name;
                $row[]           = $this->customlib->getAgeBydob($value->date_of_birth);
                $row[]           = $this->customlib->YYYYMMDDTodateFormat($value->donate_date);
                 //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";
                        }
                        $row[] = $display_field;
                    }
                }
                //====================
                $row[]           = $value->apply_charge;
                $row[]           = $discount_amount." (" . $value->discount_percentage . "%) ";
                $row[]           = calculatePercent(($value->apply_charge - $discount_amount), $value->tax_percentage)." (" . $value->tax_percentage . "%) ";
                $row[]           = $value->amount;
                $row[]           = $value->paid_amount;
                $dt_data[]       = $row;
            }
            
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = ""; 
             //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                       
                        $footer_row[] = ""; 
                    }
                }
                //====================        
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';            
            $footer_row[] = "<b>" . $currency_symbol . (number_format($apply_charge, 2, '.', '')) . "<br/>";
            $footer_row[] = "";
            $footer_row[] = "";          
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_paid, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_amount, 2, '.', '')) . "<br/>";
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function componentIssueReport()
    {
        if (!$this->rbac->hasPrivilege('component_issue_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/bloodbank');
        $this->session->set_userdata('subsub_menu', 'reports/bloodbank/componentissuereport');

        $staffsearch             = $this->patient_model->getstaffbloodissuebill();
        $data['staffsearch']     = $staffsearch;
        $data["searchlist"]      = $this->search_type;
        $data['fields']          = $this->customfield_model->get_custom_fields('component_issue', '', '', 1);
        $data["stockbloodgroup"] = $this->bloodbankstatus_model->get_stock_bloodgroup();
        $this->load->view('layout/header');
        $this->load->view('admin/bloodbank/componentissuereport', $data);
        $this->load->view('layout/footer');
    }

    public function getComponentIssueReport()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['collect_staff'] = $this->input->post('collect_staff');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $start_date              = '';
        $end_date                = '';

        if ($search['search_type'] == 'period') {

            $start_date = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);

        } else {

            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates = $this->customlib->get_betweendate($search['search_type']);

                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }

            $start_date = $dates['from_date'];
            $end_date   = $dates['to_date'];

        }
        $dt_response = $this->bloodissue_model->getAllcomponentissueRecord($start_date, $end_date, $search['collect_staff']);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================

                $prefix = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value->id;

                //==============================
                $row[]     = $prefix;
                $row[]     = $this->customlib->dateyyyymmddToDateTimeformat($value->date_of_issue, false);
                $row[]     = $value->patient_name . " (" . $value->patient_id . ")";
                $row[]     = $value->blood_group_name;
                $row[]     = $value->component_name;
                $row[]     = $value->gender;
                $row[]     = $value->donor_name;
                $row[]     = $this->customlib->bag_string($value->bag_no, $value->volume, $value->unit);
                $row[]     = $value->net_amount;
                $row[]     = $value->paid_amount;
                $row[]     = amountFormat($value->net_amount - $value->paid_amount);
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function deleteDonorCycle($id)
    {
        if (!empty($id)) {
            $this->blood_donorcycle_model->deleteCycle($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function getDonorBloodgroup()
    {
        $donor_id = $this->input->post("donor_id");
        $result   = $this->blooddonor_model->getDonorBloodgroup($donor_id);
        echo json_encode($result);
    }

    public function products()
    {
        if (!$this->rbac->hasPrivilege('blood_bank_product', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/bloodbank/products');
        $this->session->set_userdata('sub_menu', 'admin/bloodbank');
        $data['unit_type'] = $this->unittype_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/bloodbank/products', $data);
        $this->load->view('layout/footer');
    }

    public function getproductlist()
    {
        $dt_response = $this->bloodbankstatus_model->getDatatableAllproducts();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row    = array();
                $action = "<div class='rowoptionview rowview-mt-19'>";
                if ($this->rbac->hasPrivilege('blood_bank_product', 'can_edit')) {
                    $action .= "<a href='javascript:void(0)' class='btn btn-default btn-xs edit_record' data-loading-text='" . $this->lang->line('loading') . "' data-toggle='tooltip' data-record-id=" . $value->id . "  title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('blood_bank_product', 'can_delete')) {
                    $action .= " <a class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='delete_recordById(\"" . 'admin/bloodbank/delete_product/' . "$value->id\", \"" . $this->lang->line('delete_message') . "\")' data-original-title='" . $this->lang->line('delete') . "'> <i class='fa fa-trash'></i></a>";
                }
                $action .= "</div>";

                $row[]     = $value->name . $action;
                $row[]     = $this->customlib->getblood_bank_type($value->is_blood_group);
                $row[]     = '';
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function add_product()
    {
        if (!$this->rbac->hasPrivilege('blood_bank_product', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array('required',
                array('check_exists', array($this->bloodbankstatus_model, 'valid_product')),
            )
        );
        $this->form_validation->set_rules(
            'type', $this->lang->line('type'), array('required'));

        if ($this->form_validation->run() == false) {
            $msg = array(
                'type'         => form_error('type'),
                'check_exists' => form_error('name'),
                'volume'       => form_error('volume'),
                'unit'         => form_error('unit'),
            );

            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            ///=========================
            $insert_data = array(
                'id'             => $this->input->post('id'),
                'name'           => $this->input->post('name'),
                'is_blood_group' => $this->input->post('type'),
            );

            $this->bloodbankstatus_model->add_product($insert_data);

            //==================

            $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
        }
        echo json_encode($json_array);
    }

    public function getproductDetails()
    {
        $id = $this->input->post('id');
        echo json_encode($this->bloodbankstatus_model->get_product($id));
    }

    public function delete_product($id)
    {
        if (!$this->rbac->hasPrivilege('blood_bank_product', 'can_delete')) {
            access_denied();
        }
        $this->bloodbankstatus_model->delete_product($id);
        echo json_encode(array("status" => 1, "msg" => $this->lang->line("delete_message")));
    }

    public function getBloodbankTransaction()
    {
        $billing_id                 = $this->input->post('id');
        $data['patient_id']         = $this->input->post('patient_id');
        $transaction_type           = $this->input->post('transaction_type'); 
        
        if($transaction_type=='blood_component'){
            $data['blood_issue_detail'] = $this->bloodissue_model->getComponentDetail($billing_id);           
            $data['blood_issue_detail']['blood_group']=$data['blood_issue_detail']['blood_group_name'];
            $data['blood_issue_detail']['total_deposit']=$data['blood_issue_detail']['paid_amount'];            
        }else{
            $data['blood_issue_detail'] = $this->bloodissue_model->getDetail($billing_id);  
        }
        
        $transaction                = $this->transaction_model->bloodbankPayments($billing_id);    
    
        $data["billing_id"]         = $billing_id;
        $data["payment_mode"]       = $this->payment_mode;
        $data['transaction']        = $transaction;
        
        $page   = $this->load->view("admin/bloodbank/_getBloodbankTransaction", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function partialbill()
    {
        $this->form_validation->set_rules('payment_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|valid_amount|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'trim|required|xss_clean');
        if ($_POST['payment_mode'] == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line('attach_document'), 'callback_handle_document[document]');
        }
        if ($this->form_validation->run() == false) {
            $msg = array(
                'amount'       => form_error('amount'),
                'payment_mode' => form_error('payment_mode'),
                'payment_date' => form_error('payment_date'),
                'cheque_date'  => form_error('cheque_date'),
                'cheque_no'    => form_error('cheque_no'),
                'document'     => form_error('document'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $billing_id           = $this->input->post('billing_id');
            $blood_billing_detail = $this->transaction_model->bloodIssueTotalPayments($billing_id);
           
            $amount_paying        = $this->input->post('amount');
            $total_paid           = $blood_billing_detail->total_paid;
            $net_amount           = $blood_billing_detail->net_amount;
            if ($net_amount >= ($total_paid + $amount_paying)) {
                $picture         = "";
                $bill_date       = $this->input->post("payment_date");
                $payment_section = $this->config->item('payment_section');
                $payment_array   = array(
                    'amount'         => $this->input->post('amount'),
                    'patient_id'     => $this->input->post('patient_id'),
                    'section'        => $payment_section['blood_bank'],
                    'type'           => 'payment',
                    'blood_issue_id' => $billing_id,
                    'payment_mode'   => $this->input->post('payment_mode'),
                    'note'           => $this->input->post('note'),
                    'payment_date'   => $this->customlib->dateFormatToYYYYMMDDHis($bill_date),
                    'received_by'    => $this->session->userdata('hospitaladmin')['id'],
                );

                $attachment      = "";
                $attachment_name = "";
                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $fileInfo        = pathinfo($_FILES["document"]["name"]);
                    $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                    $attachment_name = $_FILES["document"]["name"];
                    move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
                }
                $cheque_date = $this->input->post("cheque_date");
                if ($this->input->post('payment_mode') == "Cheque") {
                    $payment_array['blood_issue_id']  = $billing_id;
                    $payment_array['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                    $payment_array['cheque_no']       = $this->input->post('cheque_no');
                    $payment_array['attachment']      = $attachment;
                    $payment_array['attachment_name'] = $attachment_name;
                }

                $this->transaction_model->add($payment_array);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $array = array('status' => 'fail', 'error' => array('amount_invalid' => $this->lang->line('amount_should_not_be_greater_than_balance') . ' ' . amountFormat($net_amount - $total_paid)), 'message' => '');
            }

        }
        echo json_encode($array);
    }

    public function handle_document($str, $var)
    {
        $image_validate    = $this->config->item('image_validate');
        $file_type         = $_FILES[$var]['type'];
        $file_size         = $_FILES[$var]["size"];
        $file_name         = $_FILES[$var]["name"];
        $allowed_extension = $image_validate['allowed_extension'];
        $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_mime_type = $image_validate['allowed_mime_type'];
        if (!empty($file_name)) {
            if ($files = @getimagesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_document', $this->lang->line('error_while_uploading_file'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_document', $this->lang->line('extension_error_while_uploading_file'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_document', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_document', $this->lang->line('file_type_extension_error_uploading_file'));
                return false;
            }
            return true;
        }
    }

    public function printTransaction()
    {
        $print_details         = $this->printing_model->get('', 'paymentreceipt');
        $id                    = $this->input->post('id');
        $charge                = array();
        $transaction           = $this->transaction_model->bloodbankPaymentByTransactionId($id);
        $data['print_details'] = $print_details;
        $data['transaction']   = $transaction;
        $page                  = $this->load->view('admin/bloodbank/_printTransaction', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printDonorTransaction()
    {
        $print_details         = $this->printing_model->get('', 'bloodbank');
        $transaction_id        = $this->input->post('transaction_id');
        $charge                = array();
        $transaction           = $this->transaction_model->donorPaymentByTransactionId($transaction_id);
        $data['print_details'] = $print_details;
        $data['transaction']   = $transaction;
        $donor_id              = $this->input->post('donor_id');
        $result                = $this->blooddonor_model->getDetails($donor_id);
        $result['age']         = $this->customlib->getAgeBydob($result['date_of_birth']);
        $result['dateofbirth'] = $this->customlib->YYYYMMDDTodateFormat($result['date_of_birth']);
        $data['result']        = $result;
        $page                  = $this->load->view('admin/bloodbank/_printDonorTransaction', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printBloodIssueBill()
    {
        $print_details               = $this->printing_model->get('', 'bloodbank');
        $id                          = $this->input->post('id');
        $charge                      = array();
        $blood_issues_detail         = $this->bloodissue_model->getDetail($id);
        $transaction                 = $this->transaction_model->bloodbankPaymentByTransactionId($id);
        $data['print_details']       = $print_details;
        $data['blood_issues_detail'] = $blood_issues_detail;
        $prefix                      = $this->customlib->getSessionPrefixByType('blood_bank_billing');
        $data['prefix']              = $prefix;
        $data['transactions']        = $this->transaction_model->BloodBankPayments($id);
        $data['fields']              = $this->customfield_model->get_custom_fields('blood_issue', '', 1);
        $page                        = $this->load->view('admin/bloodbank/_printBloodIssueBill', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printcomponentIssueBill()
    {
        $print_details               = $this->printing_model->get('', 'bloodbank');
        $id                          = $this->input->post('id');
        $charge                      = array();
        $blood_issues_detail         = $this->bloodissue_model->getcomponentDetail($id);
        $transaction                 = $this->transaction_model->bloodbankPaymentByTransactionId($id);
        $data['print_details']       = $print_details;
        $data['blood_issues_detail'] = $blood_issues_detail;
        $data['bill_prefix']         = $this->customlib->getSessionPrefixByType('blood_bank_billing');
        $data['transactions']        = $this->transaction_model->BloodBankPayments($id);
        $data['fields']              = $this->customfield_model->get_custom_fields('component_issue', '', 1);
        $page                        = $this->load->view('admin/bloodbank/_printcomponentIssueBill', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function components()
    {
        if (!$this->rbac->hasPrivilege('blood_bank_components', 'can_view')) {
            access_denied();
        }
        $data["payment_mode"] = $this->payment_mode;
        $data["bloodgroup"]   = $this->bloodbankstatus_model->get_product(null, 1);
        $data["components"]   = $this->bloodbankstatus_model->get_product(null, 2);
        $data["charge_type"]  = $this->chargetype_model->get();
        $data['unit_type']    = $this->unittype_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/bloodbank/components', $data);
        $this->load->view('layout/footer');
    }

    public function getcomponets()
    {
        $dt_response = $this->bloodissue_model->getAllcomponents();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                if ($this->rbac->hasPrivilege('blood_bank_components', 'can_delete')) {
                    $action .= "<a class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='deleterecord(" . $value->id . ")' data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $action .= "</div>";
                //==============================
                $row[] = $value->name . $action;
                $row[] = $value->components_blood_group;
                $row[]     = $this->customlib->bag_string($value->bag_no, $value->volume, $value->unit_name);
                $row[]     = $value->lot;
                $row[]     = $value->institution;
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function addcomponents()
    {
        if (!$this->rbac->hasPrivilege('blood_bank_components', 'can_add')) {
            access_denied();
        }
        $components = $this->bloodbankstatus_model->get_product(null, 2);
        if (isset($_POST['select']) && !empty($_POST['select'])) {
            foreach ($_POST['select'] as $key => $value) {
                $bag_no = $this->input->post('bag_no_' . $value);
                $lot    = $this->input->post('lot_' . $value);
                if ($bag_no == "") {
                    $this->form_validation->set_rules('deatils', $this->lang->line('components'), 'trim|required|xss_clean',
                        array('required' => $this->lang->line('component_details_required')));
                }
                if ($lot == "") {
                    $this->form_validation->set_rules('deatils', $this->lang->line('components'), 'trim|required|xss_clean',
                        array('required' => $this->lang->line('component_details_required')));
                }
            }
        } else {
            $this->form_validation->set_rules('no_record', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('please_select_anyone_component')));
        }

        $this->form_validation->set_rules('blood_bank_product_id', $this->lang->line('blood_group'), 'required');
        $this->form_validation->set_rules('blood_donor_cycle_id', $this->lang->line('bag'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'blood_bank_product_id' => form_error('blood_bank_product_id'),
                'blood_donor_cycle_id'  => form_error('blood_donor_cycle_id'),
                'deatils'               => form_error('deatils'),
                'no_record'             => form_error('no_record'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            foreach ($_POST['select'] as $key => $value) {
                $bag_no      = $this->input->post('bag_no_' . $value);
                $lot         = $this->input->post('lot_' . $value);
                $quantity    = $this->input->post('quantity_' . $value);
                $bag_volume  = $this->input->post('volume_' . $value);
                $bag_unit    = $this->input->post('unit_' . $value);
                $donor_cycle = array(
                    'blood_donor_cycle_id'  => $this->input->post('blood_donor_cycle_id'),
                    'blood_bank_product_id' => $value,
                    'institution'           => $this->input->post('institution_' . $value),
                    'lot'                   => $lot,
                    'bag_no'                => $bag_no,
                    'quantity'              => 1,
                );
                if ($bag_volume != "") {
                    $donor_cycle["volume"] = $bag_volume;
                }
                if ($bag_unit != "") {
                    $donor_cycle["unit"] = $bag_unit;
                }

                $this->blood_donorcycle_model->add($donor_cycle, array());
                $donor_details     = $this->notificationsetting_model->getdonorDetails($this->input->post('blood_bank_product_id'));
                $component_details = $this->notificationsetting_model->getdonorDetails($value);
                $bag_details       = $this->notificationsetting_model->getbagDetails($this->input->post('blood_donor_cycle_id'));

                $event_data = array(
                    'blood_group'    => $donor_details['blood_group_name'],
                    'bag'            => $bag_details['bag_no'] . ' (' . $bag_details['volume'] . " " . $bag_details['unit'] . ')',
                    'component_name' => $component_details['blood_group_name'],
                    'component_bag'  => $bag_no,
                );

                $this->system_notification->send_system_notification('add_component_of_blood', $event_data);

            }
            $this->bloodbankstatus_model->updatestockbyid($this->input->post('blood_donor_cycle_id'));
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function deleteComponent($id)
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_delete')) {
            access_denied();
        }
        $this->bloodbankstatus_model->deleteComponent($id);
        $array = array('status' => 'success', 'error' => '', 'msg' => $this->lang->line('success_message'));
        echo json_encode($array);
    }

    public function getdonorDetails()
    {
        $id                    = $this->input->post('id');
        $data['fields']        = $this->customfield_model->get_custom_fields('donor', '', 1);
        $result                = $this->blooddonor_model->getDetails($id);
        $result['age']         = $this->customlib->getAgeBydob($result['date_of_birth']);
        $result['dateofbirth'] = $this->customlib->YYYYMMDDTodateFormat($result['date_of_birth']);
        $data['result']        = $result;
        $print_details         = $this->printing_model->get('', 'bloodbank');
        $data['print_details'] = $print_details;
        $data['bloodbatch']    = $this->blood_donorcycle_model->getDonorBloodBatch($id);
        
        $page                  = $this->load->view('admin/bloodbank/_printdonor', $data, true);
        echo json_encode($page);
    }

    public function get_componentBybloodId()
    {
        $id        = $this->input->post('id');
        $component = $this->blood_donorcycle_model->get_componentBybloodId($id);
        echo json_encode($component);
    }

    /**
     * This function is used to validate document for upload
     **/
    public function handle_doc_upload($str, $var)
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = $image_validate["allowed_extension"];
            $allowed_mime_type = $image_validate["allowed_mime_type"];
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES[$var]['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('file_type_extension_error_uploading_document'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('extension_error_while_uploading_document'));
                    return false;
                }
                if ($file_size > 2097152) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('file_size_shoud_be_less_than') . "2MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_doc_upload', $this->lang->line('error_while_uploading_document'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function getcomponentissuereportDatatable()
    {
		$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        $search['search_type']             = $this->input->post('search_type');
        $search['collect_staff']           = $this->input->post('collect_staff');
        $condition['date_from']            = $this->input->post('date_from');
        $condition['date_to']              = $this->input->post('date_to');
        $condition['blood_group']          = $this->input->post('blood_group');
        $condition['blood_component']      = $this->input->post('blood_component');
        $condition['amount_collected_by']  = $this->input->post('amount_collected_by');
        $condition['component_collect_by'] = $this->input->post('component_collect_by');
        $start_date                        = '';
        $end_date                          = '';

        if ($search['search_type'] == 'period') {

            $start_date = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date_from'));
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date_to'));

        } else {

            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates = $this->customlib->get_betweendate($search['search_type']);

                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }

            $start_date = $dates['from_date'];
            $end_date   = $dates['to_date'];

        }

        $fields      = $this->customfield_model->get_custom_fields('component_issue', '', '', 1);
      
        $dt_response = $this->bloodissue_model->getAllcomponentissueRecord($start_date, $end_date, $search['collect_staff'], $condition['blood_group'], $condition['blood_component'], $condition['amount_collected_by'], $condition['component_collect_by']);
       
        $dt_response = json_decode($dt_response);

        $dt_data = array();
        if (!empty($dt_response->data)) {
            $total_balance = 0;
            $total_paid    = 0;
            $total_charge  = 0;
            foreach ($dt_response->data as $key => $value) {
                
                $total_balance += (amountFormat($value->net_amount - $value->paid_amount));
                $total_paid += $value->paid_amount;
                $total_charge += $value->net_amount;
                
                $row    = array();
                $prefix = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value->id;
                $row[]  = $prefix;
                $row[]  = $this->customlib->dateyyyymmddToDateTimeformat($value->date_of_issue, false);
                $row[]  = $value->patient_name . " (" . $value->patient_id . ")";
                $row[]  = $value->blood_group_name;
                $row[]  = $value->component_name;
                $row[]  = $this->lang->line(strtolower($value->gender));
                $row[]  = $value->donor_name;
                $row[]  = $this->customlib->bag_string($value->bag_no, $value->volume, $value->unit);
                $row[]  = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";
                        }
                        $row[] = $display_field;
                    }
                }
                //====================
                $row[]     = $value->net_amount;
                $row[]     = $value->paid_amount;
                $row[]     = amountFormat($value->net_amount - $value->paid_amount);
                $dt_data[] = $row;
            }
            
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            if (!empty($fields)) {
                foreach ($fields as $fields_key => $fields_value) {                     
                    $footer_row[] = '';
                }
            }
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_charge, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_paid, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_balance, 2, '.', '')) . "<br/>";
            $dt_data[]    = $footer_row;
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function get_donor_list($blood_group_id)
    {
        $result = $this->blooddonor_model->getBloodDonor($blood_group_id);
        echo json_encode($result);
    }
}