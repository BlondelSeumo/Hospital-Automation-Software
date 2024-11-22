<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pathology extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->load->config('image_valid');
        $this->marital_status       = $this->config->item('marital_status');
        $this->payment_mode         = $this->config->item('payment_mode');
        $this->search_type          = $this->config->item('search_type');
        $this->blood_group          = $this->config->item('bloodgroup');
        $this->charge_type          = $this->customlib->getChargeMaster();
        $this->payment_mode         = $this->config->item('payment_mode');
        $data["charge_type"]        = $this->charge_type;
        $this->patient_login_prefix = "pat";
        $this->load->model(array('prefix_model', 'transaction_model'));
        $this->load->helper('customfield_helper');
        $this->load->helper('custom');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'pathology');
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;

        $data["title"]    = $this->lang->line('pathology');
        $doctors          = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]  = $doctors;
        $patients         = $this->patient_model->getPatientListall();
        $data["patients"] = $patients;
        $data['fields']   = $this->customfield_model->get_custom_fields('pathologytest', 1);

        $this->load->view('layout/header');
        $this->load->view('admin/pathology/search', $data);
        $this->load->view('layout/footer');
    }

    public function createPathologyTest()
    {
        $data                    = array();
        $data['charge_category'] = $this->charge_category_model->getCategoryByModule("pathology");
        $categoryName            = $this->pathology_category_model->getcategoryName();
        $data["categoryName"]    = $categoryName;
        $parametername           = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]   = $parametername;
        $page                    = $this->load->view("admin/pathology/_createPathologyTest", $data, true);

        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function editPathologyTest()
    {
        $data           = array();
        $total_rows     = "";
        $id             = $this->input->get("id");
        $result         = $this->pathology_model->getDetails($id);
        $data['result'] = $result;
 
        $data['charge_category'] = $this->charge_category_model->getCategoryByModule("pathology");
        $categoryName            = $this->pathology_category_model->getcategoryName();
        $data["categoryName"]    = $categoryName;
        $parametername           = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]   = $parametername;
        if (!empty($result->pathology_parameter)) {
            $total_rows = count($result->pathology_parameter);
        }
     
        $page = $this->load->view("admin/pathology/_editPathologyTest", $data, true);
      
        echo json_encode(array('status' => 1, 'page' => $page, 'total_rows' => $total_rows));
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_add')) {
            access_denied();
        }
        $custom_fields = $this->customfield_model->getByBelong('pathologytest');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[pathologytest][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $check_duplicate_test = array();

        $total_rows = $this->input->post('total_rows');
        if (!isset($total_rows)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('no_parameter_selected')));
        }

        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_key => $row_value) {
                $parameter_name  = $this->input->post('parameter_name_' . $row_value);
                $reference_range = $this->input->post('reference_range_' . $row_value);
                $patho_unit      = $this->input->post('patho_unit_' . $row_value);

                if ($reference_range == "") {
                    $this->form_validation->set_rules('reference_range', $this->lang->line('reference_range'), 'trim|required|xss_clean');
                }
				
                if ($patho_unit == "") {
                    $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'trim|required|xss_clean');
                }

                if ($parameter_name == "") {
                    $this->form_validation->set_rules('parameter_name', $this->lang->line('test_parameter_name'), 'trim|required|xss_clean');
                } else {
                    $check_duplicate_test[] = $parameter_name;
                }
            }
        }

        if (!empty($check_duplicate_test)) {

            if (has_duplicate_array($check_duplicate_test)) {
                $this->form_validation->set_rules('duplicate_test', $this->lang->line("duplicate_parameter_name"), 'trim|required|xss_clean', array('required' => 'The %s not allowed.'));
            }
        }

        $this->form_validation->set_rules('test_name', $this->lang->line('test_name'), 'callback_pathology_test|xss_clean');
        $this->form_validation->set_rules('pathology_category_id', $this->lang->line('category_name'), 'required|xss_clean');
        $this->form_validation->set_rules('code', $this->lang->line('charge_name'), 'required|xss_clean');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard_charge'), 'required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|xss_clean');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line('charge_category'), 'required|xss_clean');
        $this->form_validation->set_rules('report_days', $this->lang->line('report_days'), 'required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'no_records'            => form_error('no_records'),
                'test_name'             => form_error('test_name'),
                'short_name'            => form_error('short_name'),
                'pathology_category_id' => form_error('pathology_category_id'),
                'parameter_name'        => form_error('parameter_name'),
                'charge_category_id'    => form_error('charge_category_id'),
                'code'                  => form_error('code'),
                'reference_range'       => form_error('reference_range'),
                'unit'                  => form_error('unit'),
                'standard_charge'       => form_error('standard_charge'),
                'amount'                => form_error('amount'),
                'duplicate_test'        => form_error('duplicate_test'),
                'report_days'        => form_error('report_days'),
            );
			
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                      = $custom_fields_value['id'];
                        $custom_fields_name                                                    = $custom_fields_value['name'];
                        $error_msg2["custom_fields[pathologytest][" . $custom_fields_id . "]"] = form_error("custom_fields[pathologytest][" . $custom_fields_id . "]");
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

            $id                     = $this->input->post('id');
            $insert_parameter_array = array();
            $update_parameter_array = array();
            $prev_inserted_array    = $this->input->post('prev_inserted');
            if (!isset($prev_inserted_array)) {
                $prev_inserted_array = array();
            }
            $update_inserted_array = array();
            $total_rows            = $this->input->post('total_rows');
            foreach ($total_rows as $row_key => $row_value) {
                $chk_new = $this->input->post('inserted_id_' . $row_value);
                if ($chk_new == 0) {
                    $insert_parameter_array[] = array(
                        'pathology_id'           => 0,
                        'pathology_parameter_id' => $this->input->post('parameter_name_' . $row_value),
                    );
                } else {
                    $update_inserted_array[]  = $chk_new;
                    $update_parameter_array[] = array(
                        'id'                     => $chk_new,
                        'pathology_id'           => $id,
                        'pathology_parameter_id' => $this->input->post('parameter_name_' . $row_value),
                    );
                }
            }
			
            $deleted_parameter_array = array_diff($prev_inserted_array, $update_inserted_array);

            $parameter_id = $this->input->post('parameter_name');
            $pathology    = array(
                'test_name'             => $this->input->post('test_name'),
                'short_name'            => $this->input->post('short_name'),
                'test_type'             => $this->input->post('test_type'),
                'pathology_category_id' => $this->input->post('pathology_category_id'),
                'sub_category'          => $this->input->post('sub_category'),
                'report_days'           => $this->input->post('report_days'),
                'method'                => $this->input->post('method'),
                'charge_id'             => $this->input->post('code'),

            );
            if ($id > 0) {
                $pathology['id'] = $id;
            }
            $insert_id = $this->pathology_model->add($pathology, $insert_parameter_array, $update_parameter_array, $deleted_parameter_array);

            $custom_field_post  = $this->input->post("custom_fields[pathologytest]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[pathologytest][" . $key . "]");
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
                if ($id > 0) {
                    $this->customfield_model->updateRecord($custom_value_array, $id, 'pathologytest');
                } else {
                    $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function pathology_test()
    {
        $id         = $this->input->post('id');
        $test_name  = $this->input->post('test_name');
        $short_name = $this->input->post('short_name');

        if ($test_name == "" && $short_name != "") {
            $this->form_validation->set_message('pathology_test', $this->lang->line('the_test_name_field_is_required'));
            return false;
        }
        if ($short_name == "" && $test_name != "") {
            $this->form_validation->set_message('pathology_test', $this->lang->line('the_short_name_field_is_required'));
            return false;
        }
        if ($short_name == "" && $test_name == "") {
            $this->form_validation->set_message('pathology_test', $this->lang->line('the_test_name_and_short_name_required'));
            return false;
        }
        if ($test_name != '' && $short_name != '') {
            $count = $this->pathology_model->test_uniqe($test_name, $short_name, $id);
            if ($count > 0) {
                $this->form_validation->set_message('pathology_test', $this->lang->line('test_name_and_short_name_already_exist'));
                return false;
            }
        } else {
            return true;
        }
    }
	
    public function addBill()
    {
        $custom_fields = $this->customfield_model->getByBelong('pathology');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[pathology][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $transaction_data     = array();
        $pathology_billing_id = $this->input->post('pathology_billing_id');
        $prescription_no      = $this->input->post('prescription_no');

        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patientid', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('discount', $this->lang->line('discount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tax', $this->lang->line('tax'), 'trim|required|xss_clean');
        if ($pathology_billing_id == '0') {
           
            $this->form_validation->set_rules(
                'amount', $this->lang->line('amount'), array('trim', 'required', 'xss_clean', 'valid_amount',
                    array('check_exists', array($this->pathology_model, 'validate_paymentamount')),
                )
            );
            if ($this->input->post("payment_mode") == "Cheque") {
                $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required');
                $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required');
                $this->form_validation->set_rules('document', $this->lang->line('documents'), 'callback_handle_doc_upload[document]');
            }
        }

        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');

        $total_rows = $this->input->post('total_rows');
        if (!isset($total_rows) && !isset($pathology) && !isset($radiology)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('no_test_selected')));
        }
        $check_duplicate_test = array();
        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_key => $row_value) {

                $test_name              = $this->input->post('test_name_' . $row_value);
                $reportdate             = $this->input->post('reportdate_' . $row_value);
                $check_duplicate_test[] = $test_name;

                if ($test_name == "") {
                    $this->form_validation->set_rules('test_name', $this->lang->line('test_name'), 'trim|required|xss_clean');
                }
                if ($reportdate == "") {
                    $this->form_validation->set_rules('reportdate', $this->lang->line('report_date'), 'trim|required|xss_clean');
                }
            }
        }
		
        if (!empty($check_duplicate_test)) {
            if (has_duplicate_array($check_duplicate_test)) {
                $this->form_validation->set_rules('duplicate_test', $this->lang->line("duplicate_test"), 'trim|required|xss_clean', array('required' => 'The %s not allowed.'));
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'no_records'     => form_error('no_records'),
                'patientid'      => form_error('patientid'),
                'discount'       => form_error('discount'),
                'tax'            => form_error('tax'),
                'test_name'      => form_error('test_name'),
                'reportdate'     => form_error('reportdate'),
                'amount'         => form_error('amount'),
                'duplicate_test' => form_error('duplicate_test'),
                'document'       => form_error('document'),
                'date'           => form_error('date'),
                'net_amount'     => form_error('net_amount'),
                'total'          => form_error('total'),
            );

            if ($pathology_billing_id == '0') {
                if ($this->input->post("payment_mode") == "Cheque") {
                    $msg['cheque_no']   = form_error('cheque_no');
                    $msg['cheque_date'] = form_error('cheque_date');
                }
            }

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                  = $custom_fields_value['id'];
                        $custom_fields_name                                                = $custom_fields_value['name'];
                        $error_msg2["custom_fields[pathology][" . $custom_fields_id . "]"] = form_error("custom_fields[pathology][" . $custom_fields_id . "]");
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
          
            $patient_id        = $this->input->post('patientid');
            $bill_date         = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date'));
            $doctor_name       = $this->input->post('doctor_name');
            $doctor_id         = $this->input->post('consultant_doctor');
            $case_reference_id = $this->input->post('case_reference_id');
            if (empty($doctor_id)) {
                $doctor_id = null;
            }
			
            if (empty($case_reference_id)) {
                $case_reference_id = null;
            }

            if ($prescription_no != "") {
                $prescription_prefix = splitPrefixType($prescription_no);
                $prescription_no     = splitPrefixID($prescription_no);
            } else {
                $prescription_no = null;
            }

            $is_tpa = $this->input->post('is_tpa');
            if($is_tpa=="" || $is_tpa==0){
                $organisation_id = null;
                $insurance_id = null;
                $insurance_validity = null;
            }else{           
                $organisation_id    = $this->input->post('organisation_id');
                $insurance_id       = $this->input->post('insurance_id');
                $insurance_validity = $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity'));
            }
			
            if (empty($organisation_id)) {
                $organisation_id = null;
            }

            $data = array(
                'date'                      => $bill_date,
                'patient_id'                => $patient_id,
                'doctor_name'               => $doctor_name,
                'doctor_id'                 => $doctor_id,
                'case_reference_id'         => $case_reference_id,
                'ipd_prescription_basic_id' => $prescription_no,
                'total'                     => $this->input->post('total'),
                'discount'                  => $this->input->post('discount'),
                'discount_percentage'       => $this->input->post('discount_percent'),
                'tax'                       => $this->input->post('tax'),
                'net_amount'                => $this->input->post('net_amount'),
                'note'                      => $this->input->post('note'),
                'generated_by'              => $this->customlib->getLoggedInUserID(),
                'organisation_id'           => $organisation_id,
                'insurance_validity'        => $insurance_validity,
                'insurance_id'              => $insurance_id,
            );

            $custom_field_post  = $this->input->post("custom_fields[pathology]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[pathology][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            if ($pathology_billing_id > 0) {
                $data['id'] = $pathology_billing_id;
            }

            $total_rows   = $this->input->post('total_rows');
            $prev_reports = $this->input->post('prev_reports');

            $insert_array              = array();
            $update_array              = array();
            $prev_reports_array        = array();
            $prev_reports_update_array = array();
			
            if (isset($prev_reports)) {
                $prev_reports_array = $prev_reports;
            }

            foreach ($total_rows as $row_key => $row_value) {
                $test_report_id = $this->input->post('inserted_id_' . $row_value);
                if ($test_report_id == 0) {
                    $report = array(
                        'pathology_bill_id' => 0,
                        'patient_id'        => $patient_id,
                        'pathology_id'      => $this->input->post('test_name_' . $row_value),
                        'tax_percentage'    => $this->input->post('taxpercent_' . $row_value),
                        'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($this->input->post('reportdate_' . $row_value)),
                        'apply_charge'      => $this->input->post('amount_' . $row_value),
                    );
                    $insert_array[] = $report;
                } else if ($test_report_id > 0) {
                    $report = array(
                        'id'             => $test_report_id,
                        'patient_id'     => $patient_id,
                        'pathology_id'   => $this->input->post('test_name_' . $row_value),
                        'tax_percentage' => $this->input->post('taxpercent_' . $row_value),
                        'reporting_date' => $this->customlib->dateFormatToYYYYMMDD($this->input->post('reportdate_' . $row_value)),
                        'apply_charge'   => $this->input->post('amount_' . $row_value),
                    );
                    $prev_reports_update_array[] = $test_report_id;
                    $update_array[]              = $report;
                }
            }

            if ($pathology_billing_id == '0') {

                $cheque_date     = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date"));
                $payment_section = $this->config->item('payment_section');

                $transaction_data = array(
                    'pathology_billing_id' => 0,
                    'patient_id'           => $patient_id,
                    'case_reference_id'    => $case_reference_id,
                    'section'              => $payment_section['pathology'],
                    'amount'               => $this->input->post('amount'),
                    'type'                 => 'payment',
                    'ipd_id'               => $this->input->post('ipdid'),
                    'payment_mode'         => $this->input->post('payment_mode'),
                    'payment_date'         => $bill_date,
                    'received_by'          => $this->customlib->getLoggedInUserID(),
                );
				
                if ($this->input->post('payment_mode') == "Cheque") {
                    $transaction_data['cheque_date'] = $cheque_date;
                    $transaction_data['cheque_no']   = $this->input->post('cheque_no');
                    if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                        $fileInfo        = pathinfo($_FILES["document"]["name"]);
                        $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                        $attachment_name = $_FILES["document"]["name"];
                        move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
                        $transaction_data['attachment']      = $attachment;
                        $transaction_data['attachment_name'] = $attachment_name;
                    }
                }
            }

            $array_delete = array_diff($prev_reports_array, $prev_reports_update_array);
            $inserted     = $this->pathology_model->addBill($data, $insert_array, $update_array, $array_delete, $pathology_billing_id, $transaction_data);

            if ($pathology_billing_id > 0) {
                if (!empty($custom_fields)) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[pathology][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $inserted,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                    $this->customfield_model->updateRecord($custom_value_array, $inserted, 'pathology');
                }

            } else {
                if (!empty($custom_value_array)) {
                    $this->customfield_model->insertRecord($custom_value_array, $inserted);
                }
            }

            if ($inserted) {
                $patientlist = $this->notificationsetting_model->getpatientDetails($patient_id);

                $event_data = array(
                    'patient_id'  => $patient_id,
                    'case_id'     => $this->input->post('case_reference_id'),
                    'bill_no'     => $this->input->post('bill_no'),
                    'date'        => $this->customlib->YYYYMMDDTodateFormat($this->input->post('date')),
                    'total'       => $this->input->post('total'),
                    'discount'    => $this->input->post('discount'),
                    'tax'         => $this->input->post('tax'),
                    'net_amount'  => $this->input->post('net_amount'),
                    'paid_amount' => $this->input->post('amount'),
                );

                if ($doctor_id != "") {
                    $doctor_details            = $this->notificationsetting_model->getstaffDetails($doctor_id);
                    $event_data['doctor_name'] = composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']);
                    $event_data['doctor_id']   = $doctor_id;
                } else {
                    $event_data['doctor_name'] = $doctor_name;
                    $event_data['doctor_id']   = $doctor_id;
                }

                $this->system_notification->send_system_notification('pathology_investigation', $event_data);

                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $inserted);
            } else {
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('something_went_wrong'), 'insert_id' => $inserted);
            }

        }
        echo json_encode($array);
    }

    public function addpatient()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $check_patient_id = $this->patient_model->getMaxId();
            if (empty($check_patient_id)) {
                $check_patient_id = 1000;
            }

            $patient_id = $check_patient_id + 1;

            $patient_data = array(
                'patient_name'      => $this->input->post('name'),
                'mobileno'          => $this->input->post('contact'),
                'marital_status'    => $this->input->post('marital_status'),
                'email'             => $this->input->post('email'),
                'gender'            => $this->input->post('gender'),
                'guardian_name'     => $this->input->post('guardian_name'),
                'blood_group'       => $this->input->post('blood_group'),
                'address'           => $this->input->post('address'),
                'known_allergies'   => $this->input->post('known_allergies'),
                'patient_unique_id' => $patient_id,
                'note'              => $this->input->post('note'),
                'age'               => $this->input->post('age'),
                'month'             => $this->input->post('month'),
                'is_active'         => 'yes',
            );
            $insert_id = $this->patient_model->add_patient($patient_data);

            $user_password      = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
            $data_patient_login = array(
                'username' => $this->patient_login_prefix . $insert_id,
                'password' => $user_password,
                'user_id'  => $insert_id,
                'role'     => 'patient',
            );
            $this->user_model->add($data_patient_login);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }
        }
        echo json_encode($array);
    }

    public function prescriptionBill()
    {
        $this->form_validation->set_rules('prescription_no', $this->lang->line('prescription_no'), 'required|xss_clean');
        if ($this->form_validation->run() == false) {     
            $msg = array(
                'prescription_no'            => form_error('prescription_no'),               
            );
         
            $array = array('status' => 0, 'error' =>$msg, 'message' => '');
            echo json_encode($array);
        } else {
            $prescription_no = $this->input->post('prescription_no');
            $date            = $this->input->post('date');
            $pathology_tests = $this->pathology_model->getpathotestDetails();    
            $data["pathology_tests"] = $pathology_tests;
            $data["date"]            = $this->customlib->dateFormatToStrtotime($date);
            $data["payment_mode"]    = $this->payment_mode;
            $doctors                 = $this->staff_model->getStaffbyrole(3);
            $data["doctors"]         = $doctors;
            $prefixes                = $this->prefix_model->getByCategory(array('ipd_prescription', 'opd_prescription'));
            $total_rows              = 1;
            $patient_id              = 0;
            $patient_name            = "";
            $total_tests_rows        = 0;
            $prefix_type             = "";
            $case_reference_id       = "";
            $prescription_prefix     = splitPrefixType($prescription_no);
            $prescription_no         = splitPrefixID($prescription_no);
            if (!empty($prefixes)) {
                $prefix_type = findPrefixType($prefixes, $prescription_prefix);
            }
    
            $prescription_test = $this->prescription_model->getPrescriptionTestsByCategory($prescription_no, $prefix_type, 'pathology_id');    
    
            if (!empty($prescription_test)) {
                $id      = $prescription_test->id;
                $patient_name      = $prescription_test->patient_name;
                $patient_id        = $prescription_test->patient_id;
                $total_rows        = count($prescription_test->tests);
                $case_reference_id = $prescription_test->case_reference_id;     
				$is_tpa= $this->input->post('is_tpa');
    
				if($patient_id != 0){
					
					$result            = $this->patient_model->getpatientDetails($patient_id);
					if(empty($result['organisation_id'])){
						$data['organisation_id']        =  null;
					}else{
						$data['organisation_id']        =  $result['organisation_id'] ;
					}
					$data['insurance_id']           =  $result['insurance_id'] ;
					$data['insurance_validity']     =  $result['insurance_validity'];
					$tpa_status=0;
					if(!empty($result['organisation_id'])){
					if($is_tpa==1){
						if(strtotime($result['insurance_validity']) < strtotime(date('Y-m-d'))){
							$tpa_status=1;
						}else{
							$tpa_status=0;
						}
					}
					}
				}            
    
				$data['prescription_test']      =  $prescription_test;
				$data['is_tpa']                 =   $is_tpa;            
    
				$page = $this->load->view("admin/pathology/_prescriptionBill", $data, true);  
    
				echo json_encode(array('status' => 1, 'page' => $page, 'patient_id' => $patient_id, 'total_rows' => $total_rows, 'patient_name' => $patient_name, 'case_reference_id' => $case_reference_id,'tpa_status'=>$tpa_status, 'msg'=> $this->lang->line('your_tpa_validity_has_expired_on').' '.$this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']).', '. $this->lang->line('so_standard_charges_has_been_applied')));
				}else{
				echo json_encode(array('status' => 1, 'patient_id' => 0 ));
				}
        }   
    }

    public function editparameter($id)
    {
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail                = $this->pathology_category_model->getparameterDetails($id);
        $data['detail']        = $detail;
        $this->load->view("admin/pathology/editparameter", $data);
    }

    public function parameterview($id, $value_id = '')
    {
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail                = $this->pathology_category_model->getparameterDetails($id, $value_id);
        $data['detail']        = $detail;
        $this->load->view("admin/pathology/parameterview", $data);
    }

    public function parameterdetails($id, $value_id = '')
    {
        $parametername         = $this->pathology_category_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail                = $this->pathology_category_model->getparameterDetailsforpatient($value_id);
        $data['detail'] = $detail;
        $this->load->view("admin/pathology/parameterdetails", $data);
    }

    public function getparameterdetails()
    {
        $id     = $this->input->get_post('id');
        $result = $this->pathology_category_model->getpathoparameter($id);
        echo json_encode($result);
    }

    public function gettestpathodetails()
    {
        $id     = $this->input->post('id');
        $patient_id=$this->input->post('patient_id');
        $is_tpa_valid=true;
        if($this->input->post('is_tpa') == 1 && $patient_id != ""){          
            $result = $this->pathology_model->getPathologyChargeByPatient($id,$patient_id);            
            if($result && !IsNullOrEmptyString($result->insurance_validity)){
                if(strtotime($result->insurance_validity) >= strtotime(date('Y-m-d'))){
                    $result->standard_charge= $result->org_charge;
                }else{
                    $is_tpa_valid=false;  
                }              
            }
        }else{
            $result = $this->pathology_model->getPathologyChargeByPatient($id);
        }

        if(!$result){
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('no_charge_has_configured_for_selected_category')));
        }elseif($result && !$is_tpa_valid){
            echo json_encode(array('status' => 2, 'result' => $result,'msg'=> $result,'msg'=> $this->lang->line('your_tpa_validity_has_expired_on').' '.$this->customlib->YYYYMMDDTodateFormat($result->insurance_validity).', '. $this->lang->line('so_standard_charges_has_been_applied')));
        }else{
            echo json_encode(array('status' => 1, 'result' => $result));
        }       
    }

    public function pathologyDetails()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }
        $id             = $this->input->post("pathology_id");
        $result         = $this->pathology_model->getDetails($id);
        $data['result'] = $result;
        $page = $this->load->view("admin/pathology/_pathologyDetails", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_edit')) {
            access_denied();
        }
        $custom_fields = $this->customfield_model->getByBelong('pathology');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];
                    $this->form_validation->set_rules("custom_fields[pathology][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
                }
            }
        }
        $this->form_validation->set_rules('test_name', $this->lang->line('test_name'), 'required');
        $this->form_validation->set_rules('short_name', $this->lang->line('short_name'), 'required');
        $this->form_validation->set_rules('test_type', $this->lang->line('test_type'), 'required');
        $this->form_validation->set_rules('pathology_category_id', $this->lang->line('category_name'), 'required');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'required');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line('charge_category'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'test_name'             => form_error('test_name'),
                'short_name'            => form_error('short_name'),
                'test_type'             => form_error('test_type'),
                'pathology_category_id' => form_error('pathology_category_id'),
                'code'                  => form_error('code'),
                'charge_category_id'    => form_error('charge_category_id'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                  = $custom_fields_value['id'];
                        $custom_fields_name                                                = $custom_fields_value['name'];
                        $error_msg2["custom_fields[pathology][" . $custom_fields_id . "]"] = form_error("custom_fields[pathology][" . $custom_fields_id . "]");
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

            $id                         = $this->input->post('id');
            $charge_category_id         = $this->input->post('charge_category_id');
            $pre_pathology_parameter_id = $this->input->post("previous_pathology_parameter_id[]");
            $pre_pathology_id           = $this->input->post("previous_pathology_id");
            $pre_parameter_id           = $this->input->post("previous_parameter_id[]");
            $new_parameter_id           = $this->input->post("new_parameter_id[]");
            $parameter_id               = $this->input->post("parameter_name[]");
            $custom_field_post          = $this->input->post("custom_fields[pathology]");
            $insert_data                = array();
            $pathology                  = array(
                'id'                    => $id,
                'test_name'             => $this->input->post('test_name'),
                'short_name'            => $this->input->post('short_name'),
                'test_type'             => $this->input->post('test_type'),
                'pathology_category_id' => $this->input->post('pathology_category_id'),
                'sub_category'          => $this->input->post('sub_category'),
                'report_days'           => $this->input->post('report_days'),
                'method'                => $this->input->post('method'),
                'charge_id'             => $this->input->post('code'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[pathology][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'pathology');
            }

            $i = 0;
            $j = 0;
            foreach ($parameter_id as $key => $value) {
                if (array_key_exists($i, $pre_pathology_parameter_id)) {
                    $detail = array(
                        'parameter_id' => $parameter_id[$i],
                        'id'           => $pre_pathology_parameter_id[$i],
                    );
                    $data[] = $detail;
                } else {
                    $j++;
                    $insert_detail = array(
                        'pathology_id' => $id,
                        'parameter_id' => $parameter_id[$i],
                    );
                    $insert_data[] = $insert_detail;
                }
                $i++;
            }

            $k         = $i - $j;
            $s         = 1;
            $condition = "";
            foreach ($data as $key => $value) {
                if ($s == $k) {
                    $coma = '';
                } else {
                    $coma = ',';
                }
                $condition .= "(" . $value['parameter_id'] . "," . $value['id'] . ")" . $coma;
                $s++;
            }

            $delete_arr = array();
            foreach ($pre_parameter_id as $pkey => $pvalue) {
                if (in_array($pvalue, $new_parameter_id)) {

                } else {
                    $delete_arr[] = array('id' => $pvalue);
                }
            }

            $this->pathology_model->updateparameter($condition);

            if (!empty($insert_data)) {
                $this->pathology_model->addparameter($insert_data);
            }

            if (!empty($delete_arr)) {
                $this->pathology_model->delete_parameter($delete_arr);
            }

            $this->pathology_model->update($pathology);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->pathology_model->delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => 'Something went wrong');
        }
        echo json_encode($array);
    }

    public function deletebill()
    {
        $id = $this->input->post('id');
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->pathology_model->deletePathologyBill($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => 'Something went wrong');
        }
        echo json_encode($array);
    }

    public function getPathology()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }

        $id     = $this->input->post('pathology_id');
        $result = $this->pathology_model->getPathology($id);
        echo json_encode($result);
    }

    public function getPathologyReport()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }
        $id                       = $this->input->post('id');
        $result                   = $this->pathology_model->getPathologyReport($id);
        $result['reporting_date'] = date($this->customlib->getHospitalDateFormat(), strtotime($result['reporting_date']));
        echo json_encode($result);
    }

    public function getPathologyparameterReport()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }
        $id                       = $this->input->post('id');
        $result                   = $this->pathology_model->getPathologyparameterReport($id);
        $result['reporting_date'] = $this->customlib->YYYYMMDDTodateFormat($result['reporting_date']);
        echo json_encode($result);
    }

    public function updateTestReport()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required');
        $this->form_validation->set_rules('apply_charge', $this->lang->line('applied_charge'), 'required');
        $this->form_validation->set_rules('pathology_report', $this->lang->line('file'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'id'           => form_error('id'),
                'patient_name' => form_error('patient_name'),
                'apply_charge' => form_error('apply_charge'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $reporting_date = $this->input->post("reporting_date");

            $id           = $this->input->post('id');
            $report_batch = array(
                'id'                => $id,
                'patient_name'      => $this->input->post('patient_name'),
                'patient_id'        => $this->input->post('patient_id_patho'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($reporting_date),
                'description'       => $this->input->post('description'),
                'apply_charge'      => $this->input->post('apply_charge'),
            );

            $this->pathology_model->updateTestReport($report_batch);

            if (!empty($_FILES['pathology_report']['name'])) {
                $config['upload_path']   = 'uploads/pathology_report/';
                $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
                $config['file_name']     = $_FILES['pathology_report']['name'];

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('pathology_report')) {
                    $uploadData = $this->upload->data();
                    $picture    = $uploadData['file_name'];
                } else {
                    $picture = "";
                }

                $data_img = array('id' => $id, 'pathology_report' => $picture);
                $this->pathology_model->updateTestReport($data_img);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function parameteraddvalue()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'id' => form_error('id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $id             = $this->input->post('id');
            $reporting_date = $this->input->post("reporting_date");
            $report_batch   = array(
                'id'                => $id,
                'patient_id'        => $this->input->post('patient_id_patho'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($reporting_date),
                'description'       => $this->input->post('description'),
                'apply_charge'      => $this->input->post('apply_charge'),
            );

            $parameter_id    = $this->input->post('parameter_id[]');
            $parameter_value = $this->input->post('parameter_value[]');
            $par_id          = $this->input->post('parid[]');
            $pathology_id    = $this->input->post('pathologyid');
            $update_id       = $this->input->post('update_id[]');
            $preport_id      = $this->input->post('preport_id[]');

            $i               = 0;
            $parameter_array = array();
            foreach ($update_id as $pkey => $pvalue) {
                $parameter_value_arr = array(
                    'id'                     => $pvalue,
                    'pathology_report_id'    => $preport_id[$i],
                    'pathology_report_value' => $parameter_value[$i],
                );

                $this->pathology_model->addparametervalue($parameter_value_arr);
                $i++;
            }

            if (!empty($_FILES['pathology_report']['name'])) {
                $config['upload_path']   = 'uploads/pathology_report/';
                $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
                $config['file_name']     = $_FILES['pathology_report']['name'];
                $fileInfo                = pathinfo($_FILES["pathology_report"]["name"]);
                $img_name                = $id . '.' . $fileInfo['extension'];

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                move_uploaded_file($_FILES["pathology_report"]["tmp_name"], "./uploads/pathology_report/" . $img_name);

                $data_img = array('id' => $id, 'pathology_report' => $img_name);
                $this->pathology_model->updateTestReport($data_img);
            }

            $this->pathology_model->updateTestReport($report_batch);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function testReportBatch()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'required');
        $this->form_validation->set_rules('pathology_id', $this->lang->line('pathology_id'), 'required');
        $this->form_validation->set_rules('apply_charge', $this->lang->line('applied_charge'), 'required');
        $this->form_validation->set_rules('pathology_report', $this->lang->line('file'), 'callback_handle_upload');
        $this->form_validation->set_rules('reporting_date', $this->lang->line('reporting_date'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'       => form_error('patient_id'),
                'pathology_id'     => form_error('pathology_id'),
                'apply_charge'     => form_error('apply_charge'),
                'reporting_date'   => form_error('reporting_date'),
                'pathology_report' => form_error('pathology_report'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id             = $this->input->post('pathology_id');
            $patient_id     = $this->input->post('patient_id');
            $reporting_date = $this->input->post("reporting_date");

            $report_batch = array(
                'pathology_id'      => $id,
                'patient_id'        => $patient_id,
                'customer_type'     => $this->input->post('customer_type'),
                'patient_name'      => $this->input->post('patient_name'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($reporting_date),
                'description'       => $this->input->post('description'),
                'apply_charge'      => $this->input->post('apply_charge'),
                'generated_by'      => $this->customlib->getLoggedInUserID(),
                'pathology_report'  => '',
            );

            $insert_id = $this->pathology_model->testReportBatch($report_batch);

            if (isset($_FILES["pathology_report"]) && !empty($_FILES['pathology_report']['name'])) {
                $fileInfo = pathinfo($_FILES["pathology_report"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["pathology_report"]["tmp_name"], "./uploads/pathology_report/" . $img_name);
                $data_img = array('id' => $insert_id, 'pathology_report' => $img_name);
                $this->pathology_model->testReportBatch($data_img);
            }

            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function handle_upload_document()
    {
        $image_validate = $this->config->item('file_validate');

        if (isset($_FILES["document"]) && !empty($_FILES["document"]['name'])) {

            $file_type         = $_FILES["document"]['type'];
            $file_size         = $_FILES["document"]["size"];
            $file_name         = $_FILES["document"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);

            $allowed_mime_type = $image_validate['allowed_mime_type'];

            if ($files = @getimagesize($_FILES['document']['tmp_name'])) {
                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload_document', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload_document', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload_document', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload_document', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function handle_upload( )
    {
        $image_validate = $this->config->item('file_validate');       
        if (isset($_FILES["file"]) && !empty($_FILES["file"]['name'])) {           

            $file_type = $_FILES["file"]['type'];
            $file_size = $_FILES["file"]["size"];
            $file_name = $_FILES["file"]["name"];
            
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mtype = finfo_file($finfo, $_FILES['file']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mtype, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($file_size > $image_validate['upload_size']) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                return false;
            }

            return true;
        } 
    } 

    public function gettestreportbatch()
    {
        if (!$this->rbac->hasPrivilege('pathology_bill', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'pathology');
        $id                  = $this->input->post("id");
        $patients            = $this->patient_model->getPatientListall();
        $data["patients"]    = $patients;
        $pathologist         = $this->staff_model->getStaffbyrole(5);
        $data["pathologist"] = $pathologist;
        $testlist            = $this->pathology_model->getpathotestDetails();
        $data["testlist"]    = $testlist;
        $data["bloodgroup"]  = $this->bloodbankstatus_model->get_product(null, 1);
        $data['fields']      = $this->customfield_model->get_custom_fields('pathology', 1);
        $data['organisation']   = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/pathology/reportDetail', $data);
        $this->load->view('layout/footer');
    }

    public function getpathologybillDatatable()
    {
        $dt_response = $this->pathology_model->getAllpathologybillRecord();
		 
        $fields      = $this->customfield_model->get_custom_fields('pathology', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row            = array();
                $balance_amount = ($value->net_amount) - ($value->paid_amount);
                //====================================

                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs view_detail' data-toggle='tooltip' title='" . $this->lang->line('view_reports') . "' ><i class='fa fa-reorder'></i></a>";
                if ($this->rbac->hasPrivilege('pathology_partial_payment', 'can_view')) {
                    $action .= " <a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' data-record-caseid='" . $value->case_reference_id . "' class='btn btn-default btn-xs add_payment' data-toggle='tooltip' title='" . $this->lang->line('add_view_payments') . "' ><i class='fa fa-money'></i></a>";
                }
                if ($value->case_reference_id > 0) {
                    $case_id = $value->case_reference_id;
                } else {
                    $case_id = '';
                }

                $action .= "</div>";

                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('pathology_billing') . $value->id . $action;
                $row[] = $case_id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->patient_name . " (" . $value->pid . ")";
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);

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
                $row[]     = $value->discount ." (". $value->discount_percentage ."%)"  ;
                $row[]     = $value->net_amount;
                $row[]     = $value->paid_amount;
                $row[]     = number_format($balance_amount, 2);
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

    public function getpathologytestDatatable()
    {
        $dt_response = $this->pathology_model->getAllpathologytest();
        $fields      = $this->customfield_model->get_custom_fields('pathologytest', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================

                $action = "<div class='rowoptionview rowview-mt-19'>";

                if ($this->rbac->hasPrivilege('pathology_test', 'can_view')) {
                    $action .= "<a href='#' data-toggle='tooltip' onclick='viewDetail(" . $value->id . ")'  class='btn btn-default btn-xs' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
                }
                $action .= "</div'>";

                //==============================
                $row[] = $value->test_name . "</a> " . $action;
                $row[] = $value->short_name;
                $row[] = $value->test_type;
                $row[] = $value->category_name;
                $row[] = $value->sub_category;
                $row[] = $value->method;
                $row[] = $value->report_days;

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
                $row[]     = $value->percentage;
                $row[]     = $value->standard_charge;
                $row[]     = amountFormat($value->standard_charge + calculatePercent($value->standard_charge, $value->percentage));
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

    public function getPatientPathologyDetails()
    {
        $is_bill    = $this->input->post('is_bill');
        $id         = $this->input->post('id');
        $data['id'] = $id;
        $result     = $this->pathology_model->getPathologyBillByID($id);
  
        if($result->ipd_prescription_basic_id != ''){
            $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($result->ipd_prescription_basic_id);            
            if(!empty($ipd_opd)){
                if($ipd_opd->ipd_id != ''){             
                $data['prescription']   =   $this->customlib->getSessionPrefixByType('ipd_prescription').$ipd_opd->id;  
            }else{
                $data['prescription']   =   $this->customlib->getSessionPrefixByType('opd_prescription').$ipd_opd->id ; 
            } 
            }else{
                $data['prescription']   = '';
            }       
                   
        }else{
            $data['prescription']   = '';
        }
        
        $data['result'] = $result;
        if (isset($is_bill)) {
            $data['is_bill'] = false;
        } else {
            $data['is_bill'] = true;
        }
        $data['bill_prefix']           = $this->customlib->getSessionPrefixByType('pathology_billing');
        $data['fields']                = $this->customfield_model->get_custom_fields('pathology');
        $data['pathology_test_fields'] = $this->customfield_model->get_custom_fields('pathologytest');
       
        $page                          = $this->load->view('admin/pathology/_getPatientPathologyDetails', $data, true);
        $actions                       = "";

        if (isset($is_bill)) {
            $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_pathology_bill' data-toggle='tooltip' data-placement='bottom'  data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
        } else {
            $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_parameter' data-toggle='tooltip' data-placement='bottom'  data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('print_test_report') . "'><i class='fa fa-reorder'></i></a>";
            $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_bill' data-toggle='tooltip' data-record-id=\"" . $id . "\"  data-placement='bottom'  data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
            if ($this->rbac->hasPrivilege('pathology_bill', 'can_edit')) {
                $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='edit_pathology' data-toggle='tooltip' data-placement='bottom' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('edit_pathology') . "'><i class='fa fa-pencil'></i></a>";
            }if ($this->rbac->hasPrivilege('pathology_bill', 'can_delete')) {
                $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='delete_pathology' data-toggle='tooltip' data-placement='bottom' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('delete_pathology') . "'><i class='fa fa-trash'></i></a>";
            }
        }

        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
    }

    public function getBillDetails()
    {
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
		$print   				= $this->input->get('print');
		 
        $data['id']            = $id;		
		
        if (isset($print)) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result = $this->pathology_model->getPathologyBillByID($id);
     
        if($result->ipd_prescription_basic_id != ''){
            $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($result->ipd_prescription_basic_id);          
           
            if(!empty($ipd_opd)){
                if($ipd_opd->ipd_id != ''){             
                    $data['prescription']   =   $this->customlib->getSessionPrefixByType('ipd_no').$ipd_opd->ipd_id;  
                }else{
                    $data['prescription']   =   $this->customlib->getSessionPrefixByType('opd_no').$ipd_opd->visit_details_id ; 
                } 
            }else{
                $data['prescription']   = '';
            }
            
        }else{
            $data['prescription']   = '';
        }

        $data['fields']      = $this->customfield_model->get_custom_fields('pathology', '', 1);
        $data['bill_prefix'] = $this->customlib->getSessionPrefixByType('pathology_billing');

        $data['result'] = $result;

        $page = $this->load->view('admin/pathology/_getBillDetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getPathologyReportDetail()
    {
        $id             = $this->input->post('id');
        $data['id']     = $id;
        $staff          = $this->staff_model->getStaffbyrole(3);
        $data["staff"]  = $staff;
        $result         = $this->pathology_model->getPatientPathologyReportDetails($id);
        $data['result'] = $result;

        $page = $this->load->view('admin/pathology/_getPathologyReportDetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function assigntestpatho()
    {
        $id                   = $this->input->post('id');
        $testlist             = $this->pathology_model->getpathotestDetails();
        $data["testlist"]     = $testlist;
        $patients             = $this->patient_model->getPatientListall();
        $data["patients"]     = $patients;
        $doctors              = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]      = $doctors;
        $data["payment_mode"] = $this->payment_mode;
        $page                 = $this->load->view("admin/pathology/_assigntestpatho", $data, true);
        $result               = $this->pathology_model->getBillNo();
        $id                   = $result["id"];
        if (!empty($result["id"])) {
            $bill_no = $id + 1;
        } else {
            $bill_no = 1;
        }

        echo json_encode(array('status' => 1, 'page' => $page, 'bill_no' => $this->customlib->getSessionPrefixByType('pathology_billing') . $bill_no, 'total_rows' => 1));
    }

    public function editpathology()
    {
        $id                     = $this->input->post('id');
        $pathology_data         = $this->pathology_model->getPathologyBillByID($id);		
        $data["pathology_data"] = $pathology_data;
        $testlist                    = $this->pathology_model->getpathotestDetails();
        $data["testlist"]            = $testlist;
        $patients                    = $this->patient_model->getPatientListall();
        $data["patients"]            = $patients;
        $patient_names               = array_column($patients, 'patient_name', 'id');
        $doctors                     = $this->staff_model->getStaffbyrole(3);
        $data['custom_fields_value'] = display_custom_fields('pathology', $id);
        $data["doctors"]             = $doctors;
        $data["payment_mode"]        = $this->payment_mode;               
        $page                        = $this->load->view("admin/pathology/_editpathology", $data, true);        
        $total_rows                  = count($pathology_data->pathology_report);
        $case_reference_id           = $pathology_data->case_reference_id;
        $patient_id                  = $pathology_data->patient_id;
        $date                        = $pathology_data->date;
        $bill_no                     = $pathology_data->id;		
		
        $organisation_id             = $pathology_data->organisation_id;

        if($organisation_id!=null || $organisation_id!=0 || $organisation_id!=""){
            $tpa_apply_status=1;
        }else{
            $tpa_apply_status=0;
        }

        echo json_encode(array('status' => 1, 'page' => $page, 'bill_prefix' => $this->customlib->getSessionPrefixByType('pathology_billing'), 'bill_no' => $bill_no, 'pathology_date' => $date, 'total_rows' => $total_rows, 'case_reference_id' => $case_reference_id, 'patient_id' => $patient_id, 'patient_name' => $patient_names[$patient_id] . " (" . $patient_id . ")",'tpa_apply_status'=>$tpa_apply_status));
    }

    public function getReportDetails($id, $parameter_id)
    {
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result                   = $this->pathology_model->getBillDetails($id);
        $data['result']           = $result;
        $detail                   = $this->pathology_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parametername            = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]    = $parametername;
        $parameterdetails         = $this->pathology_category_model->getparameterDetailsforpatient($id);
        $data['parameterdetails'] = $parameterdetails;

        $this->load->view('admin/pathology/printReport', $data);
    }

    public function deleteTestReport($id)
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_delete')) {
            access_denied();
        }
        $this->pathology_model->deleteTestReport($id);
    }

    public function pathologyReport()
    {
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pathology');
        $this->session->set_userdata('subsub_menu', 'reports/pathology/pathologyreport');

        $custom_fields = $this->customfield_model->get_custom_fields('pathology', '', '', 1);
        $staffsearch          = $this->patient_model->getstaffbytransactionbill();
        $data['staffsearch']  = $staffsearch;
        $data["searchlist"]   = $this->search_type;
        $data['fields']       = $custom_fields;
        $data["testlist"]     = $this->pathology_model->getpathotestDetails();
        $pathologist          = $this->staff_model->getStaffbyrole(5);
        $data["pathologist"]  = $pathologist;
        $categoryName         = $this->pathology_category_model->getcategoryName();
        $data["categoryName"] = $categoryName;
        $this->load->view('layout/header');
        $this->load->view('admin/pathology/pathologyReport', $data);
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
                'search_type'   => $this->input->post('search_type'),
                'collect_staff' => $this->input->post('collect_staff'),
                'date_from'     => $this->input->post('date_from'),
                'date_to'       => $this->input->post('date_to'),
                'test_name'     => $this->input->post('test_name'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function pathologyreports()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['collect_staff'] = $this->input->post('collect_staff');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $start_date              = '';
        $end_date                = '';
        
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        
        $condition['collect_staff'] = $this->input->post('collect_staff');
        $condition['test_name']     = $this->input->post('test_name');
        $fields = $this->customfield_model->get_custom_fields('pathology', '', '', 1);
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

        $reportdata = $this->transaction_model->pathologybillreportsRecord($condition);
       
        $reportdata    = json_decode($reportdata);
        $dt_data       = array();
        $total_balance = 0;
       
        $total_charge  = 0;
        if (!empty($reportdata->data)) {
             
            foreach ($reportdata->data as $key => $value) {
                
                $apply_charge = ( ( $value->apply_charge * $value->tax_percentage ) / 100 + $value->apply_charge ) ;
               
                $total_charge += $apply_charge;
                
                if (!empty($value->patient_id)) {
                    $patient_id = " (" . $value->patient_id . ")";
                } else {
                    $patient_id = "";
                }                

                $row   = array();
                $row[] = $this->customlib->getSessionPrefixByType($value->module_no) . $value->module_id;
                
                if($value->payment_date){
                    $row[] = $this->customlib->YYYYMMDDTodateFormat($value->payment_date);
                }else{
                    $row[] = '';
                }
                
                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = $value->category_name;
                $row[] = $value->test_name . " (" . $value->short_name . ")";
                $row[] = $value->doctor_name;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
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
                $row[] = number_format($apply_charge, 2);
                 
                
                $dt_data[] = $row;
            }

            $footer_row   = array();
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
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_charge, 2, '.', '')) . "<br/>"; 
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

    public function getPathologyTransaction()
    {
        $pathology_billing_id  = $this->input->post('id');
        $pathology_transaction = $this->transaction_model->pathologyPayments($pathology_billing_id);
        $is_bill               = $this->input->post('is_bill');
        if (isset($is_bill)) {
            $data['is_bill'] = true;
            $data['form_id'] = "add_pathopartial_payment";
        } else {
            $data['is_bill'] = false;
            $data['form_id'] = "add_partial_payment";
        }
        $data["pathology_billing_id"]    = $pathology_billing_id;
        $data["payment_mode"]            = $this->payment_mode;
        $data['pathology_transaction']   = $pathology_transaction;
        $pathology_billing               = $this->pathology_model->getPathologyBillByID($pathology_billing_id);
		 
        $data['pathology_billing']       = $pathology_billing;
        $data['pathology_total_payment'] = $this->transaction_model->pathologyTotalPayments($pathology_billing_id)->total_paid;
 
        $page = $this->load->view("admin/pathology/_getPathologyTransaction", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function partialbill()
    {
        if (!$this->rbac->hasPrivilege('pathology_partial_payment', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('payment_date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|valid_amount');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required');
        if ($this->input->post('payment_mode') == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_upload_document');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'payment_date' => form_error('payment_date'),
                'amount'       => form_error('amount'),
                'payment_mode' => form_error('payment_mode'),
                'cheque_no'    => form_error('cheque_no'),
                'cheque_date'  => form_error('cheque_date'),
                'document'     => form_error('document'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $pathology_billing_id     = $this->input->post('pathology_billing_id');
            $pathology_billing_detail = $this->transaction_model->pathologyTotalPayments($pathology_billing_id);
            $net_amount               = $pathology_billing_detail->net_amount;
            $amount_paying            = $this->input->post('amount');
            $total_paid               = $pathology_billing_detail->total_paid;

            if ($net_amount >= ($total_paid + $amount_paying)) {
                $picture         = "";
                $bill_date       = $this->input->post("payment_date");
                $payment_section = $this->config->item('payment_section');
                $payment_array   = array(
                    'amount'               => $this->input->post('amount'),
                    'type'                 => 'payment',
                    'patient_id'           => $this->input->post('patient_id'),
                    'section'              => $payment_section['pathology'],
                    'pathology_billing_id' => $this->input->post('pathology_billing_id'),
                    'payment_mode'         => $this->input->post('payment_mode'),
                    'note'                 => $this->input->post('note'),
                    'payment_date'         => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->customlib->getHospitalTimeFormat()),
                    'received_by'          => $this->customlib->getLoggedInUserID(),
                );

                if (!empty($this->input->post('case_reference_id')) && $this->input->post('case_reference_id') != "") {
                    $payment_array['case_reference_id'] = $this->input->post('case_reference_id');
                }

                $attachment      = "";
                $attachment_name = "";

                $cheque_date = $this->input->post("cheque_date");
                if ($this->input->post('payment_mode') == "Cheque") {

                    $payment_array['cheque_date'] = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                    $payment_array['cheque_no']   = $this->input->post('cheque_no');

                    if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                        $fileInfo        = pathinfo($_FILES["document"]["name"]);
                        $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                        $attachment_name = $_FILES["document"]["name"];
                        move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
                        $payment_array['attachment']      = $attachment;
                        $payment_array['attachment_name'] = $attachment_name;
                    }
                }

                $this->transaction_model->add($payment_array);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $array = array('status' => 'fail', 'error' => array('amount_invalid' => $this->lang->line('amount_should_not_be_greater_than_balance') . ' ' . amountFormat($net_amount - $total_paid)), 'message' => '');
            }
        }
        echo json_encode($array);
    }

    public function printTransaction()
    {
        $print_details         = $this->printing_model->get('', 'paymentreceipt');
        $id                    = $this->input->post('id');
        $charge                = array();
        $transaction           = $this->transaction_model->pathologyPaymentByTransactionId($id);
        $data['transaction']   = $transaction;
        $data['print_details'] = $print_details;
        $page                  = $this->load->view('admin/pathology/_printTransaction', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printPatientReportDetail()
    {
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
        $data['id']            = $id;
        $result                = $this->pathology_model->getPatientPathologyReportDetails($id);
        $data['result']        = $result;
        $page                  = $this->load->view('admin/pathology/_printPatientReportDetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getReportCollectionDetail()
    {
        $id     = $this->input->post('id');
        $charge = array();
        $pathology_center = $this->lang->line('in_house_pathology_lab');
        $report           = $this->pathology_model->getPathologyReportByID($id);
        if (!empty($report)) {
            if ($report['pathology_center'] == "") {
                $report['pathology_center'] = $pathology_center;
            }
            if ($report['collection_date'] == "") {
                $report['collection_date'] = date('Y-m-d');
            }
        }
        echo json_encode(array('status' => 1, 'report' => $report));
    }

    public function updatecollection()
    {
        $this->form_validation->set_rules('pathology_report_id', $this->lang->line('report_id'), 'required');
        $this->form_validation->set_rules('collected_by', $this->lang->line('sample_collected_person_name'), 'required');
        $this->form_validation->set_rules('collected_date', $this->lang->line('collected_date'), 'required');
        $this->form_validation->set_rules('pathology_center', $this->lang->line('pathology_center'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'pathology_report_id' => form_error('pathology_report_id'),
                'collected_by'        => form_error('collected_by'),
                'collected_date'      => form_error('collected_date'),
                'pathology_center'    => form_error('pathology_center'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $doctor_id     = "";
            $doctor_name   = "";
            $payment_array = array(
                'id'                    => $this->input->post('pathology_report_id'),
                'collection_specialist' => $this->input->post('collected_by'),
                'pathology_center'      => $this->input->post('pathology_center'),
                'collection_date'       => $this->customlib->dateFormatToYYYYMMDD($this->input->post('collected_date')),
            );
            $this->pathology_model->updateTestReport($payment_array);
            $reportdetails = $this->pathology_model->getPathologyReportByID($this->input->post('pathology_report_id'));

            $test_detail                  = $this->notificationsetting_model->getPathologyBillReportByID($this->input->post('pathology_bill_id'));
            $sample_collected_person_name = $this->notificationsetting_model->getstaffDetails($this->input->post('collected_by'));
            
            $event_data = array(
                'patient_id'                   => $reportdetails['patient_id'],
                'case_id'                      => $test_detail['case_reference_id'],
                'bill_no'                      => $this->customlib->getSessionPrefixByType('pathology_billing') . $reportdetails['pathology_bill_id'],
                'collected_date'               => $this->customlib->YYYYMMDDTodateFormat($this->input->post('collected_date')),
                'test_name'                    => $test_detail['test_name'],
                'sample_collected_person_name' => composeStaffNameByString($sample_collected_person_name['name'], $sample_collected_person_name['surname'], $sample_collected_person_name['employee_id']),
                'pathology_center'             => $this->input->post('pathology_center'),
                'expected_date'                => $this->customlib->YYYYMMDDTodateFormat($test_detail['reporting_date']),
            );
            if ($this->input->post('collected_by') != "") {

                $event_data['doctor_id']   = $this->input->post('collected_by');
                $event_data['doctor_name'] = composeStaffNameByString($sample_collected_person_name['name'], $sample_collected_person_name['surname'], $sample_collected_person_name['employee_id']);
                $event_data['role_id']     = $sample_collected_person_name['role_id'];
            }

            $this->system_notification->send_system_notification('pathology_sample_collection', $event_data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function updatereportparam()
    {
        $this->form_validation->set_rules('pathology_report_id', $this->lang->line('report_id'), 'required');
        $this->form_validation->set_rules('approved_by', $this->lang->line('approved_by'), 'required');
        $this->form_validation->set_rules('approve_date', $this->lang->line('approve_date'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload');

        $rows = $this->input->post('pathology_parameterdetails');
        if (isset($rows) && !empty($rows)) {
            foreach ($rows as $row_key => $row_value) {
                $input_fields              = $this->input->post('pathology_parameter_' . $row_value);
                $pathology_reference_range = $this->input->post('pathology_reference_range_' . $row_value);
                if ($input_fields == "") {
                    $this->form_validation->set_rules('pathology_parameter', $this->lang->line('pathology_parameter'), 'required');
                } 
               
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'pathology_report_id'         => form_error('pathology_report_id'),
                'approved_by'                 => form_error('approved_by'),
                'approve_date'                => form_error('approve_date'),
                'file'                        => form_error('file'),
                'pathology_parameter'         => form_error('pathology_parameter'),
                'pathology_parameter_invalid' => form_error('pathology_parameter_invalid'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $insert_array        = array();
            $update_array        = array();
            $approved_by         = $this->input->post('approved_by');
            $rows                = $this->input->post('pathology_parameterdetails');
            $pathology_report_id = $this->input->post('pathology_report_id');
            $test_result         = $this->input->post('test_result');

            $approve_date = $this->customlib->dateFormatToYYYYMMDD($this->input->post('approve_date'));
            if (!empty($rows)) {
                foreach ($rows as $row_key => $row_value) {
                    $prev_id = $this->input->post('prev_id_' . $row_value);
                    if ($prev_id == 0) {
                        $insert_array[] = array(
                            'pathology_report_id'          => $pathology_report_id,
                            'pathology_parameterdetail_id' => $row_value,
                            'pathology_report_value'       => $this->input->post('pathology_parameter_' . $row_value),
                        );
                    } else {
                        $update_array[] = array(
                            'id'                           => $prev_id,
                            'pathology_report_id'          => $pathology_report_id,
                            'pathology_parameterdetail_id' => $row_value,
                            'pathology_report_value'       => $this->input->post('pathology_parameter_' . $row_value),
                        );
                    }
                }
                $delete_array = array();
                $this->pathology_model->addParameterforPatient($pathology_report_id, $approved_by, $approve_date, $insert_array, $update_array, $delete_array, $test_result);
            }

            //==========
            if (isset($_FILES["file"]) && !empty($_FILES["file"]['name'])) {
                $fileInfo        = pathinfo($_FILES["file"]["name"]);
                $attachment_name = $_FILES["file"]['name'];
                $img_name        = $pathology_report_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/pathology_report/" . $img_name);
                $data_img = array('id' => $pathology_report_id, 'pathology_report' => 'uploads/pathology_report/' . $img_name, 'report_name' => $attachment_name);
                $this->pathology_model->updateTestReport($data_img);
            }
            //==========

            $reportdetails                = $this->pathology_model->getPathologyReportByID($this->input->post('pathology_report_id'));
            $test_detail                  = $this->notificationsetting_model->getPathologyBillReportByID($this->input->post('pathology_bill_id'));
            $approved_by                  = $this->notificationsetting_model->getstaffDetails($this->input->post('approved_by'));            
            $sample_collected_person_name = $this->notificationsetting_model->getstaffDetails($this->input->post('collected_id'));

            $event_data = array(
                'patient_id'                   => $reportdetails['patient_id'],
                'case_id'                      => $test_detail['case_reference_id'],
                'bill_no'                      => $this->customlib->getSessionPrefixByType('pathology_billing') . $reportdetails['pathology_bill_id'],
                'collected_date'               => $this->input->post('collected_date'),
                'test_name'                    => $test_detail['test_name'],
                'doctor_id'                    => $this->input->post('approved_by'),
                'doctor_name'                  => $this->input->post('collected_by'),
                'sample_collected_person_name' => $this->input->post('collected_by'),
                'pathology_center'             => $this->input->post('pathalogy_center'),
                'expected_date'                => $this->customlib->YYYYMMDDTodateFormat($test_detail['reporting_date']),
                'approved_by'                  => composeStaffNameByString($approved_by['name'], $approved_by['surname'], $approved_by['employee_id']),
                'approve_date'                 => $this->customlib->YYYYMMDDTodateFormat($approve_date),
                'role_id'                      => $sample_collected_person_name['role_id'],
            );

            $this->system_notification->send_system_notification('pathology_test_report', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function downloadReport($report_id)
    {
        $report = $this->pathology_model->getPatientPathologyReportDetails($report_id);
        $this->load->helper('download');
        $filepath    = $report->pathology_report;
        $report_name = $report->report_name;
        $data        = file_get_contents($filepath);
        force_download($report_name, $data);
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

    public function printtestparameterdetail()
    {
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
        $data['id']            = $id;
        $result                = $this->pathology_model->gettestparameterdetails($id);
        $data['head_result']   = $this->pathology_model->getPathologyBillByID($id);
        $data['result']        = $result;
        $page                  = $this->load->view('admin/pathology/_printtestparameterdetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

}
