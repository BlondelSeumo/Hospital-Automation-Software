<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vehicle extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->search_type    = $this->config->item('search_type');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->charge_type    = $this->customlib->getChargeMaster();
        $this->load->model("transaction_model");
        $this->config->load("image_valid");
        $data["charge_type"]        = $this->charge_type;
        $this->patient_login_prefix = "pat";
        $this->load->helper('customfield_helper');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Transport');
        $this->session->set_userdata('sub_menu', 'vehicle/index');
        $data['title'] = $this->lang->line('add_vehicle');
        $this->load->view('layout/header');
        $this->load->view('admin/vehicle/search', $data);
        $this->load->view('layout/footer');
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required');
        $this->form_validation->set_rules('vehicle_model', $this->lang->line('vehicle_model'), 'required');
        $this->form_validation->set_rules('vehicle_type', $this->lang->line('vehicle_type'), 'required');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'vehicle_no'    => form_error('vehicle_no'),
                'vehicle_model' => form_error('vehicle_model'),
                'vehicle_type'  => form_error('vehicle_type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $manufacture_year = $this->input->post('manufacture_year');
            $data             = array(
                'vehicle_no'     => $this->input->post('vehicle_no'),
                'vehicle_model'  => $this->input->post('vehicle_model'),
                'driver_name'    => $this->input->post('driver_name'),
                'driver_licence' => $this->input->post('driver_licence'),
                'driver_contact' => $this->input->post('driver_contact'),
                'vehicle_type'   => $this->input->post('vehicle_type'),
                'note'           => $this->input->post('note'),
            );
            ($manufacture_year != "") ? $data['manufacture_year'] = $manufacture_year : '';
            $this->vehicle_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_view')) {
            access_denied();
        }
        $id          = $this->input->post("id");
        $listVehicle = $this->vehicle_model->getDetails($id);
        echo json_encode($listVehicle);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required');
        $this->form_validation->set_rules('vehicle_model', $this->lang->line('vehicle_model'), 'required');
        $this->form_validation->set_rules('vehicle_type', $this->lang->line('vehicle_type'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'vehicle_no'    => form_error('vehicle_no'),
                'vehicle_model' => form_error('vehicle_model'),
                'vehicle_type'  => form_error('vehicle_type'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $id               = $this->input->post('id');
            $manufacture_year = $this->input->post('manufacture_year');

            $data = array(
                'id'             => $id,
                'vehicle_no'     => $this->input->post('vehicle_no'),
                'vehicle_model'  => $this->input->post('vehicle_model'),
                'driver_name'    => $this->input->post('driver_name'),
                'driver_licence' => $this->input->post('driver_licence'),
                'driver_contact' => $this->input->post('driver_contact'),
                'vehicle_type'   => $this->input->post('vehicle_type'),
                'note'           => $this->input->post('note'),
            );

            ($manufacture_year != "") ? $data['manufacture_year'] = $manufacture_year : '';

            $this->vehicle_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_delete')) {
            access_denied();
        }
        $this->vehicle_model->remove($id);
        $array = array('status' => 'success', 'error' => '', 'msg' => $this->lang->line('delete_message'));
        echo json_encode($array);
    }

    public function addCallAmbulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_add')) {
            access_denied();
        }
        $custom_fields = $this->customfield_model->getByBelong('ambulance_call');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[ambulance_call][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $this->form_validation->set_rules('patient_id', $this->lang->line('patient_name'), 'required');
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_model'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line("charge_category"), 'trim|required');
        $this->form_validation->set_rules('code', $this->lang->line("charge_name"), 'trim|required');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard_charge'), 'trim|required');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required');
        $this->form_validation->set_rules(
            'payment_amount',
            $this->lang->line('payment_amount'),
            array(
                'required', 'xss_clean', 'valid_amount',
                array('check_exists', array($this->vehicle_model, 'validate_paymentamount')),
            )
        );

        if ($this->input->post('payment_mode') == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'vehicle_no'         => form_error('vehicle_no'),
                'date'               => form_error('date'),
                'payment_amount'     => form_error('payment_amount'),
                'patient_id'         => form_error('patient_id'),
                'charge_category_id' => form_error('charge_category_id'),
                'code'               => form_error('code'),
                'standard_charge'    => form_error('standard_charge'),
                'net_amount'         => form_error('net_amount'),
                'chekque_no'         => form_error('cheque_no'),
                'cheque_date'        => form_error('cheque_date'),
                'document'           => form_error('document'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                       = $custom_fields_value['id'];
                        $custom_fields_name                                                     = $custom_fields_value['name'];
                        $error_msg2["custom_fields[ambulance_call][" . $custom_fields_id . "]"] = form_error("custom_fields[ambulance_call][" . $custom_fields_id . "]");
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

            $date              = $this->input->post("date");
            $patient_id        = $this->input->post('patient_id');
            $case_reference_id = $this->input->post('case_reference_id');
            $data              = array(
                'patient_id'        => $patient_id,
                'vehicle_id'        => $this->input->post('vehicle_no'),
                'driver'            => $this->input->post('driver'),
                'amount'            => $this->input->post('total'),
                'net_amount'        => $this->input->post('net_amount'),
                'charge_id'         => $this->input->post('code'),
                'standard_charge'   => $this->input->post("standard_charge"),
                'tax_percentage'    => $this->input->post("tax_percentage"),
                'note'              => $this->input->post('note'),
                'discount_percentage'  => $this->input->post("discount_percent"),
                'discount'          => $this->input->post("discount"),
                'generated_by'      => $this->customlib->getLoggedInUserID(),
                'date'              => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
            );
            if ($case_reference_id != '') {
                $data['case_reference_id'] = $case_reference_id;
            } else {
                $data['case_reference_id'] = null;
            }
            $chequedate      = $this->input->post('cheque_date');
            $cheque_date     = $this->customlib->dateFormatToYYYYMMDD($chequedate);
            $payment_section = $this->config->item('payment_section');

            $transaction_data = array(
                'patient_id'        => $patient_id,
                'section'           => $payment_section['ambulance'],
                'amount'            => $this->input->post('payment_amount'),
                'type'              => 'payment',
                'case_reference_id' => $data['case_reference_id'],
                'payment_mode'      => $this->input->post('payment_mode'),
                'payment_date'      => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );

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

            $insert_id = $this->vehicle_model->addCallAmbulance($data, $transaction_data);

            $custom_field_post  = $this->input->post("custom_fields[ambulance_call]");
            $custom_value_array = array();

            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[ambulance_call][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }
            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));

            $charge_details  = $this->notificationsetting_model->getchargeDetails($this->input->post('code'));
            $vehicle_details = $this->Notificationsetting_model->getvehiclemodelnoDetails($this->input->post('vehicle_no'));

            $event_data = array(
                'patient_id'    => $patient_id,
                'vehicle_model' => $vehicle_details['vehicle_model'],
                'driver_name'   => $this->input->post('driver'),
                'date'          => $date,
                'charge_name'   => $charge_details['name'],
                'tax'           => $this->input->post('tax'),
                'net_amount'    => $this->input->post('net_amount'),
                'paid_amount'   => $this->input->post('payment_amount'),
            );

            $this->system_notification->send_system_notification('create_ambulance_call', $event_data);
        }
        echo json_encode($array);
    }

    public function getBillDetails($id)
    {
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details         = $this->printing_model->get('', 'ambulance');
        $data['print_details'] = $print_details;
        $result                = $this->vehicle_model->getBillDetails($id);
        $data['result']        = $result;
        $data['fields']        = $this->customfield_model->get_custom_fields('ambulance_call');
        $data['print_fields']  = $this->customfield_model->get_custom_fields('ambulance_call', '', 1);
        $this->load->view('admin/vehicle/printBill', $data);
    }

    public function getcallambulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Transport');
        $this->session->set_userdata('sub_menu', 'vehicle/getcallambulance');
        $data['title']           = $this->lang->line('add_vehicle');
        $data['fields']          = $this->customfield_model->get_custom_fields('ambulance_call', 1);
        $patients                = $this->patient_model->getPatientListall();
        $data["patients"]        = $patients;
        $vehiclelist             = $this->vehicle_model->get();
        $data['vehiclelist']     = $vehiclelist;
        $data["payment_mode"]    = $this->payment_mode;
        $data['charge_category'] = $this->charge_category_model->getCategoryByModule("ambulance");
        $data["bloodgroup"]      = $this->bloodbankstatus_model->get_product(null, 1);
        $categoryName            = $this->pathology_category_model->getcategoryName();
        $data["categoryName"]    = $categoryName;
        $this->load->view('layout/header');
        $this->load->view('admin/vehicle/ambulance_call', $data);
        $this->load->view('layout/footer');
    }

    public function getambulancecallDatatable()
    {
        $dt_response = $this->vehicle_model->getAllambulancecallRecord();		
        $fields      = $this->customfield_model->get_custom_fields('ambulance_call', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $balance = $value->net_amount - $value->paid_amount;
                $row     = array();
                //====================================
                $action = "<div class='rowoptionview rowview-btn-top'>";

                if ($this->rbac->hasPrivilege('ambulance_partial_payment', 'can_add') || $this->rbac->hasPrivilege('ambulance_partial_payment', 'can_view') || $this->rbac->hasPrivilege('ambulance_partial_payment', 'can_delete')) {
                    $action .= "<a href='javascript:void(0)'  data-caseid='' data-module='ambulance'  data-record-id='" . $value->id . "' data-case-id='" . $value->case_reference_id . "' data-patient-id='" . $value->patient_id . "' data-balance-amount='" . $balance . "'class='btn btn-default btn-xs add_payment' data-toggle='tooltip' title='" . $this->lang->line('add_payment') . "' ><i class='fa fa-plus'></i></a>";
                }

                if ($this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
                    $action .= "<a href='#'  onclick='viewDetailBill(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip'  title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
                }

                if ($this->rbac->hasPrivilege('ambulance_call', 'can_delete')) {
                    $action .= "<a href='#' onclick='delete_bill(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip'  title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $action .= "</div'>";

                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $value->id;
                $row[] = $value->case_reference_id;
                $row[] = $value->patient . " (" . $value->patient_id . ")" . $action;
                $row[] = $value->vehicle_no;
                $row[] = $value->vehicle_model;
                $row[] = $value->driver;
                $row[] = $value->mobileno;
                $row[] = $value->patient_address;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);

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
				if($value->discount){
					$row[]     = $value->discount." (".$value->discount_percentage." %)";
				}else{
					$row[]     = '';
				}
				
                $row[]     = $value->net_amount;
                $row[]     = $value->paid_amount;
                $row[]     = amountFormat($balance);
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

    public function getvehicleDatatable()
    {
        $dt_response = $this->vehicle_model->getAllvehicleRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $note_value = $value->note;

                $action = "<div class='rowoptionview rowview-btn-top'>";

                if ($this->rbac->hasPrivilege('ambulance', 'can_edit')) {
                    $action .= "<span class='medium-tooltip'><a href='#' onclick='getRecord(" . $value->id . "),refreshmodal()' class='btn btn-default btn-xs'  data-toggle='tooltip' data-placement='top' title='" . $this->lang->line('edit_ambulance') . "'><i class='fa fa-pencil'></i></a></span>";
                }
                if ($this->rbac->hasPrivilege('ambulance', 'can_delete')) {
                    $action .= "<a href='#' onclick='delete_bill(" . $value->id . ")'
                   class='btn btn-default btn-xs' data-placement='top' data-toggle='tooltip'  title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $action .= "</div>";

                $first_action = "<a href='#' class='detail_popover'  data-toggle='popover' title=''>";
                $firstaction  = "<div class='fee_detail_popover' style='display:none'>";
                if ($value->note = "") {
                    $firstaction = "<p href='#'  class='text text-danger'   title=''>" . $this->lang->line('no_description') . "</p>";
                } else {
                    $firstaction = "<p href='#'  class='text text-danger'   title=''>" . $this->lang->line('note') . "</p>";
                }
                $firstaction = "</div>";
                // =============================

                $row[]     = $first_action . $value->vehicle_no . "</a>" . $firstaction;
                $row[]     = $value->vehicle_model . $action;
                $row[]     = $value->manufacture_year;
                $row[]     = $value->driver_name;
                $row[]     = $value->driver_licence;
                $row[]     = $value->driver_contact;
                $row[]     = $note_value;
                $row[]     = $value->vehicle_type;
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

    public function editCall()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }

        $id                              = $this->input->get("id");
        $listCall                        = $this->vehicle_model->getCallDetails($id);
        $date                            = $this->customlib->YYYYMMDDHisTodateFormat($listCall['date'], $this->time_format);
        $listCall["date"]                = $date;
        $listCall['custom_fields_value'] = display_custom_fields('ambulance_call', $id);
        echo json_encode($listCall);
    }

    public function updatecallambulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_edit')) {
            access_denied();
        }
        $custom_fields = $this->customfield_model->getByBelong('ambulance_call');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];
                    $this->form_validation->set_rules("custom_fields[ambulance_call][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
                }
            }
        }
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient_name'), 'required');
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line("charge_category"), 'trim|required');
        $this->form_validation->set_rules('code', $this->lang->line("charge_name"), 'trim|required');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard_charge'), 'trim|required');
        $this->form_validation->set_rules('payment_amount', $this->lang->line('payment_amount'), 'trim|valid_amount');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required');
        if ($this->input->post('payment_mode') == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'         => form_error('patient_id'),
                'vehicle_no'         => form_error('vehicle_no'),
                'date'               => form_error('date'),
                'amount'             => form_error('amount'),
                'charge_category_id' => form_error('charge_category_id'),
                'code'               => form_error('code'),
                'standard_charge'    => form_error('standard_charge'),
                'chekque_no'         => form_error('cheque_no'),
                'cheque_date'        => form_error('cheque_date'),
                'document'           => form_error('document'),
                'total'              => form_error('total'),
            );
			
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                = $custom_fields_value['id'];
                        $custom_fields_name                                              = $custom_fields_value['name'];
                        $error_msg2["custom_fields[ambulance_call][" . $custom_fields_id . "]"] = form_error("custom_fields[ambulance_call][" . $custom_fields_id . "]");
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
            $id                = $this->input->post('id');
            $date              = $this->input->post('date');
            $patient_id        = $this->input->post("patient_id");
            $custom_field_post = $this->input->post("custom_fields[ambulance_call]");
            $data              = array(
                'id'                 => $id,
                'patient_id'         => $patient_id,
                'address'            => $this->input->post('address'),
                'driver'             => $this->input->post('driver_name'),
                'amount'             => $this->input->post('total'),
                'charge_category_id' => $this->input->post("charge_category_id"),
                'charge_id'          => $this->input->post('code'),
                'net_amount'         => $this->input->post('net_amount'),
                'note'               => $this->input->post('note'),
                'standard_charge'    => $this->input->post("standard_charge"),
                'tax_percentage'     => $this->input->post("tax_percentage"),
                'discount_percentage'     => $this->input->post("discount_percent_edit"),
                'discount'     => $this->input->post("discount_edit"),
                'date'               => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
            );

            if ($this->input->post("case_reference_id") != "") {
                $data['case_reference_id'] = $this->input->post("case_reference_id");
                $transaction_data          = array('case_reference_id' => $this->input->post("case_reference_id"));
            }

            $chequedate       = $this->input->post('cheque_date');
            $cheque_date      = $this->customlib->dateFormatToYYYYMMDD($chequedate);
            $payment_section  = $this->config->item('payment_section');
            $transaction_data = array(
                'patient_id'   => $patient_id,
                'section'      => $payment_section['ambulance'],
                'amount'       => $this->input->post('payment_amount'),
                'type'         => 'payment',
                'payment_mode' => $this->input->post('payment_mode'),
                'payment_date' => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'received_by'  => $this->customlib->getLoggedInUserID(),
            );
			
            if ($this->input->post("case_reference_id") != "") {
                $transaction_data['case_reference_id'] = $this->input->post("case_reference_id");
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

            $this->vehicle_model->addCallAmbulance($data, $transaction_data);
            $custom_field_post = $this->input->post("custom_fields[ambulance_call]");

            if (!empty($custom_field_post)) {
                $custom_value_array = [];
                $custom_value_array = array();
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[ambulance_call][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }

                $this->customfield_model->updateRecord($custom_value_array, $id, 'ambulance_call');
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'), 'id' => $id);
        }
        echo json_encode($array);
    }

    public function deleteCallAmbulance($id)
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_delete')) {
            access_denied();
        }
        $this->vehicle_model->delete($id);
        redirect('admin/Vehicle/getcallambulance');
    }

    public function getVehicleDetail()
    {
        $id     = $this->input->post('id');
        $result = $this->vehicle_model->getDetails($id);
        echo json_encode($result);
    }

    public function ambulancereport()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/ambulance');
        $this->session->set_userdata('subsub_menu', 'reports/ambulance/ambulancereport');
        $custom_fields       = $this->customfield_model->get_custom_fields('ambulance_call', '', '', 1);
        $staffsearch         = $this->patient_model->getstaffAmbulancebill();
        $data['staffsearch'] = $staffsearch;
        $data["searchlist"]  = $this->search_type;
        $data["fields"]      = $custom_fields;
        $data['vehiclelist'] = $this->vehicle_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/vehicle/ambulancereport', $data);
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
                'search_type'    => $this->input->post('search_type'),
                'collect_staff'  => $this->input->post('collect_staff'),
                'vehicle_number' => $this->input->post('vehicle_number'),
                'date_from'      => $this->input->post('date_from'),
                'date_to'        => $this->input->post('date_to'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function ambulancereports()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['collect_staff'] = $this->input->post('collect_staff');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $start_date              = '';
        $end_date                = '';

        $condition['vehicle_number'] = $this->input->post('vehicle_number');
        $fields                      = $this->customfield_model->get_custom_fields('ambulance_call', '', '', 1);
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

        $condition['generated_staff'] = $this->input->post('collect_staff');

        $reportdata   = $this->transaction_model->ambulancecallRecordReport($condition);
        $reportdata   = json_decode($reportdata);
        $dt_data      = array();
        $total_amount = 0;
        $total_balance = 0;
        $total_paid = 0;
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $row = array();
                $balance_amount = $value->net_amount - $value->paid_amount;
                $total_balance += $balance_amount;
                $total_amount += $value->net_amount;
                $total_paid += $value->paid_amount;

                $row[] = $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $value->id;
                $row[] = $value->patient_name . " (" . $value->patient_id . ")";
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->mobileno;
                $row[] = $value->vehicle_no;
                $row[] = $value->vehicle_model;
                $row[] = $value->driver;
                $row[] = $value->address;

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
                $row[]     = number_format((int) $value->paid_amount, 2, '.', '');
                $row[]     = number_format($value->net_amount - $value->paid_amount, 2, '.', '');
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

            if (!empty($fields)) {
                foreach ($fields as $fields_key => $fields_value) {
                    $footer_row[] = "";
                }
            }

            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . (number_format($total_amount, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . (number_format($total_paid, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . (number_format($total_balance, 2, '.', '')) . "<br/>";

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

    public function getAmbulanceCallTransaction()
    {
        $billing_id                     = $this->input->post('id');
        $data['balance_amount']         = $this->input->post('balance_amount');
        $data['case_id']                = $this->input->post('case_id');
        $data['patient_id']             = $this->input->post('patient_id');
        $data['ambullance_call_detail'] = $this->vehicle_model->getBillDetailsAmbulance($billing_id);
        $transaction                    = $this->transaction_model->ambulanceCallPayments($billing_id);
        $data["billing_id"]             = $billing_id;
        $data["payment_mode"]           = $this->payment_mode;
        $data['transaction']            = $transaction;
        $page                           = $this->load->view("admin/vehicle/_getAmbulanceCallTransactions", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function partialbill()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('payment_date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules(
            'payment_amount',
            $this->lang->line('payment_amount'),'required|xss_clean|valid_amount|callback_check_amount_exceed',
        );

        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required');
        if ($_POST['payment_mode'] == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        }

        $case_id=$this->input->post('patient_referance_case_id');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'amount'       => form_error('payment_amount'),
                'payment_mode' => form_error('payment_mode'),
                'payment_date' => form_error('payment_date'),
                'cheque_date'  => form_error('cheque_date'),
                'cheque_no'    => form_error('cheque_no'),
                'document'     => form_error('document'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $picture       = "";
            $bill_date     = $this->input->post("payment_date");
            $payment_array = array(
                'amount'            => $this->input->post('payment_amount'),
                'type'              => 'payment',
                'patient_id'        => $this->input->post('patient_id'),
                'ambulance_call_id' => $this->input->post('billing_id'),
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note'),
                'payment_date'      => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->customlib->getHospitalTimeFormat()),
                'received_by'       => $this->customlib->getLoggedInUserID(),
                'payment_mode'      => $this->input->post('payment_mode'),
            );

            if ($case_id != "") {
                $payment_array['case_reference_id'] = $case_id;
            }

            $insert_id = $this->transaction_model->add($payment_array);

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
                $data['id']              = $insert_id;
                $data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $data['cheque_no']       = $this->input->post('cheque_no');
                $data['attachment']      = $attachment;
                $data['attachment_name'] = $attachment_name;
                $this->transaction_model->add($data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function check_amount_exceed(){
        $billing_id=$this->input->post('billing_id');
        $ambullance_call_detail = $this->vehicle_model->getBillDetailsAmbulance($billing_id);
       
        if($this->input->post('payment_amount') !="" && $ambullance_call_detail['net_amount'] < ($ambullance_call_detail['paid_amount']+$this->input->post('payment_amount')))
        {
            $this->form_validation->set_message('check_amount_exceed', $this->lang->line('amount_should_not_be_greater_than_balance') . ' ' . ($ambullance_call_detail['net_amount']-$ambullance_call_detail['paid_amount']));
            return FALSE;
        }else{
            return TRUE;
        }
    }

    public function printTransaction()
    {
        $print_details         = $this->printing_model->get('', 'paymentreceipt');
        $id                    = $this->input->post('id');
        $charge                = array();
        $transaction           = $this->transaction_model->ambulanceCallPaymentByTransactionId($id);
        $data['print_details'] = $print_details;
        $data['transaction']   = $transaction;
        $page = $this->load->view('admin/vehicle/_printTransaction', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
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
}
