<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Radio extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("datatables");
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('system_notification');
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->search_type    = $this->config->item('search_type');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->charge_type    = $this->customlib->getChargeMaster();
        $data["charge_type"]  = $this->charge_type;
        $this->config->load('image_valid');
        $this->patient_login_prefix = "pat";
        $this->load->helper('custom');
        $this->load->helper('customfield_helper');
        $this->load->model(array('prefix_model', 'transaction_model'));
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function radiologyDetails()
    {
        $id             = $this->input->post("radiology_id");
        $result         = $this->radio_model->getDetails($id);
        $data['result'] = $result;
        $page           = $this->load->view("admin/radio/_radiologyDetails", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function createRadiologyTest()
    {
        $data                    = array();
        $categoryName            = $this->lab_model->getlabName();
        $data["categoryName"]    = $categoryName;
        $data['charge_category'] = $this->charge_category_model->getCategoryByModule("radiology");
        $parametername           = $this->lab_model->getradioparameter();
        $data["parametername"]   = $parametername;
        $page                    = $this->load->view("admin/radio/_createRadiologyTest", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function editRadiologyTest()
    {
        $data                    = array();
        $total_rows              = 1;
        $id                      = $this->input->post("id");
        $result                  = $this->radio_model->getDetails($id);
        $data['result']          = $result;
        $data['charge_category'] = $this->charge_category_model->getCategoryByModule("radiology");
        $data["categoryName"]    = $this->lab_model->getlabName();
        $parametername           = $this->lab_model->getradioparameter();
        $data["parametername"]   = $parametername;
        if (!empty($result->radiology_parameter)) {
            $total_rows = count($result->radiology_parameter);
        }
        $page = $this->load->view("admin/radio/_editRadiologyTest", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'total_rows' => $total_rows));
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('radiology_test', 'can_add')) {
            access_denied();
        }

        $custom_fields = $this->customfield_model->getByBelong('radiologytest');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[radiologytest][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $total_rows = $this->input->post('total_rows');
        if (!isset($total_rows)) {
            $this->form_validation->set_rules(
                'no_records',
                $this->lang->line('no_records'),
                'trim|required|xss_clean',
                array('required' => $this->lang->line('no_parameter_selected'))
            );
        }

        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_key => $row_value) {
                $parameter_name  = $this->input->post('parameter_name_' . $row_value);
                $reference_range = $this->input->post('reference_range_' . $row_value);
                $radio_unit      = $this->input->post('radio_unit_' . $row_value);

                if ($parameter_name == "") {
                    $this->form_validation->set_rules('parameter_name', $this->lang->line('test_parameter_name'), 'trim|required|xss_clean');
                }
                if ($reference_range == "") {
                    $this->form_validation->set_rules('reference_range', $this->lang->line('reference_range'), 'trim|required|xss_clean');
                }
                if ($radio_unit == "") {
                    $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'trim|required|xss_clean');
                }
            }
        }

        $this->form_validation->set_rules('test_name', $this->lang->line('test_name'), 'callback_radiology_test|xss_clean');
        $this->form_validation->set_rules('radiology_category_id', $this->lang->line('category_name'), 'required|xss_clean');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line('charge_category'), 'required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|xss_clean');
        $this->form_validation->set_rules('code', $this->lang->line('charge_name'), 'required|xss_clean');
        $this->form_validation->set_rules('tax', $this->lang->line('tax'), 'required|xss_clean');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard_charge'), 'required|xss_clean');
        $this->form_validation->set_rules('report_days', $this->lang->line('report_days'), 'required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'no_records'            => form_error('no_records'),
                'parameter_name'        => form_error('parameter_name'),
                'reference_range'       => form_error('reference_range'),
                'unit'                  => form_error('unit'),
                'test_name'             => form_error('test_name'),
                'short_name'            => form_error('short_name'),
                'radiology_category_id' => form_error('radiology_category_id'),
                'charge_category_id'    => form_error('charge_category_id'),
                'code'                  => form_error('code'),
                'amount'                => form_error('amount'),
                'tax'                   => form_error('tax'),
                'standard_charge'       => form_error('standard_charge'),
                'report_days'       	=> form_error('report_days'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                      = $custom_fields_value['id'];
                        $custom_fields_name                                                    = $custom_fields_value['name'];
                        $error_msg2["custom_fields[radiologytest][" . $custom_fields_id . "]"] = form_error("custom_fields[radiologytest][" . $custom_fields_id . "]");
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
            $parameter_id           = $this->input->post('parameter_name');
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
                        'radiology_id'           => 0,
                        'radiology_parameter_id' => $this->input->post('parameter_name_' . $row_value),
                    );
                } else {
                    $update_inserted_array[]  = $chk_new;
                    $update_parameter_array[] = array(
                        'id'                     => $chk_new,
                        'radiology_id'           => $id,
                        'radiology_parameter_id' => $this->input->post('parameter_name_' . $row_value),
                    );
                }
            }
            $deleted_parameter_array = array_diff($prev_inserted_array, $update_inserted_array);

            $radiology = array(
                'test_name'             => $this->input->post('test_name'),
                'short_name'            => $this->input->post('short_name'),
                'test_type'             => $this->input->post('test_type'),
                'radiology_category_id' => $this->input->post('radiology_category_id'),
                'sub_category'          => $this->input->post('sub_category'),
                'report_days'           => $this->input->post('report_days'),
                'charge_id'             => $this->input->post('code'),
            );

            if ($id > 0) {
                $radiology['id'] = $id;
            }
            $insert_id          = $this->radio_model->add($radiology, $insert_parameter_array, $update_parameter_array, $deleted_parameter_array);
            $custom_field_post  = $this->input->post("custom_fields[radiologytest]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[radiologytest][" . $key . "]");
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
                    $this->customfield_model->updateRecord($custom_value_array, $id, 'radiologytest');
                } else {
                    $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                }
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function radiology_test()
    {
        $id         = $this->input->post('id');
        $test_name  = $this->input->post('test_name');
        $short_name = $this->input->post('short_name');

        if ($test_name == "" && $short_name != "") {
            $this->form_validation->set_message('radiology_test', $this->lang->line('the_test_name_field_is_required'));
            return false;
        }
        if ($short_name == "" && $test_name != "") {
            $this->form_validation->set_message('radiology_test', $this->lang->line('the_short_name_field_is_required'));
            return false;
        }
        if ($short_name == "" && $test_name == "") {
            $this->form_validation->set_message('radiology_test', $this->lang->line('the_test_name_and_short_name_required'));
            return false;
        }
        if ($test_name != '' && $short_name != '') {
            $count = $this->radio_model->test_uniqe($test_name, $short_name, $id);
            if ($count > 0) {
                $this->form_validation->set_message('radiology_test', $this->lang->line('test_name_and_short_name_already_exist'));
                return false;
            }
        } else {
            return true;
        }
    }

    public function parameterview($id)
    {
        $parametername         = $this->radio_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail                = $this->radio_model->getparameterDetails($id);
        $data['detail']        = $detail;
        $this->load->view("admin/radio/parameterview", $data);
    }

    public function parameterdetails($id, $valueid = '')
    {
        $parametername         = $this->radio_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail                = $this->radio_model->getparameterDetailsforpatient($valueid);
        $data['detail']        = $detail;
        $this->load->view("admin/radio/parameterdetails", $data);
    }

    public function editparameter($id)
    {
        $parametername         = $this->radio_model->getpathoparameter();
        $data["parametername"] = $parametername;
        $detail                = $this->radio_model->getparameterDetails($id);
        $data['detail']        = $detail;
        $this->load->view("admin/radio/editparameter", $data);
    }

    public function patientDetails()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }
        $id   = $this->input->post("id");
        $data = $this->patient_model->patientDetails($id);
        echo json_encode($data);
    }

    public function addBill()
    {
        $custom_fields = $this->customfield_model->getByBelong('radiology');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[radiology][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $transaction_data     = array();
        $radiology_billing_id = $this->input->post('radiology_billing_id');
        $prescription_no      = $this->input->post('prescription_no');

        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('discount', $this->lang->line('discount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patientid', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tax', $this->lang->line('tax'), 'trim|required|xss_clean');

        if ($radiology_billing_id == '') {
            $this->form_validation->set_rules(
                'amount',
                $this->lang->line('amount'),
                array(
                    'trim', 'required', 'xss_clean', 'valid_amount',
                    array('check_exists', array($this->radio_model, 'validate_paymentamount')),
                )
            );

            if ($this->input->post("payment_mode") == "Cheque") {
                $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required');
                $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required');
                $this->form_validation->set_rules('document', $this->lang->line("document"), 'callback_handle_doc_upload[document]');
            }
        }

        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');

        $total_rows = $this->input->post('total_rows');
        if (!isset($total_rows) && !isset($radiology) && !isset($radiology)) {
            $this->form_validation->set_rules(
                'no_records',
                $this->lang->line('no_records'),
                'trim|required|xss_clean',
                array('required' => $this->lang->line('no_test_selected'))
            );
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
                $this->form_validation->set_rules(
                    'duplicate_test',
                    ' ',
                    'trim|required|xss_clean',
                    array('required' => $this->lang->line('duplicate_test_name_found'))
                );
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'no_records'     => form_error('no_records'),
                'patientid'      => form_error('patientid'),
                'test_name'      => form_error('test_name'),
                'reportdate'     => form_error('reportdate'),
                'date'           => form_error('date'),
                'net_amount'     => form_error('net_amount'),
                'total'          => form_error('total'),
                'discount'       => form_error('discount'),
                'amount'         => form_error('amount'),
                'duplicate_test' => form_error('duplicate_test'),
                'tax'            => form_error('tax'),
            );

            if ($radiology_billing_id == '') {
                if ($this->input->post("payment_mode") == "Cheque") {
                    $msg['cheque_no']   = form_error('cheque_no');
                    $msg['cheque_date'] = form_error('cheque_date');
                    $msg['document']    = form_error('document');
                }
            }
			
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                  = $custom_fields_value['id'];
                        $custom_fields_name                                                = $custom_fields_value['name'];
                        $error_msg2["custom_fields[radiology][" . $custom_fields_id . "]"] = form_error("custom_fields[radiology][" . $custom_fields_id . "]");
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
            if (empty($case_reference_id)) {
                $case_reference_id = null;
            }
			
            if (empty($doctor_id)) {
                $doctor_id = null;
            }

            if ($prescription_no != "") {
                $prescription_prefix = splitPrefixType($prescription_no);
                $prescription_no     = splitPrefixID($prescription_no);
            } else {
                $prescription_no = null;
            }

            $is_tpa = $this->input->post('is_tpa');
			
            if($is_tpa=="" || $is_tpa==0){
                $organisation_id    = null;
                $insurance_id       = null;
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
                'date'                      => ($bill_date),
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
			
            $custom_field_post  = $this->input->post("custom_fields[radiology]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[radiology][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
			
            if ($radiology_billing_id > 0) {
                $data['id'] = $radiology_billing_id;
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
                        'radiology_bill_id' => 0,
                        'radiology_id'      => $this->input->post('test_name_' . $row_value),
                        'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($this->input->post('reportdate_' . $row_value)),
                        'patient_id'        => $patient_id,
                        'apply_charge'      => $this->input->post('amount_' . $row_value),
                        'tax_percentage'    => $this->input->post('taxpercent_' . $row_value),
                        'generated_by'      => $this->customlib->getLoggedInUserID(),
                    );
                    $insert_array[] = $report;
                } else if ($test_report_id > 0) {
                    $report = array(
                        'id'             => $test_report_id,
                        'radiology_id'   => $this->input->post('test_name_' . $row_value),
                        'reporting_date' => $this->customlib->dateFormatToYYYYMMDD($this->input->post('reportdate_' . $row_value)),
                        'patient_id'     => $patient_id,
                        'apply_charge'   => $this->input->post('amount_' . $row_value),
                        'tax_percentage' => $this->input->post('taxpercent_' . $row_value),
                    );
                    $prev_reports_update_array[] = $test_report_id;
                    $update_array[]              = $report;
                }
            }

            if ($radiology_billing_id == '') {
                $cheque_date      = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date"));
                $payment_section  = $this->config->item('payment_section');
                $transaction_data = array(
                    'patient_id'        => $patient_id,
                    'case_reference_id' => $case_reference_id,
                    'section'           => $payment_section['radiology'],
                    'amount'            => $this->input->post('amount'),
                    'type'              => 'payment',
                    'ipd_id'            => $this->input->post('ipdid'),
                    'payment_mode'      => $this->input->post('payment_mode'),
                    'note'              => $this->input->post('note'),
                    'payment_date'      => $bill_date,
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
            }
            $array_delete = array_diff($prev_reports_array, $prev_reports_update_array);
            $inserted     = $this->radio_model->addBill($data, $insert_array, $update_array, $array_delete, $radiology_billing_id, $transaction_data);

            if ($radiology_billing_id > 0) {
                if (!empty($custom_fields)) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[radiology][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $inserted,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                    $this->customfield_model->updateRecord($custom_value_array, $inserted, 'radiology');
                }
            } else {
                if (!empty($custom_value_array)) {
                    $this->customfield_model->insertRecord($custom_value_array, $inserted);
                }
            }
            if ($inserted) {

                $patient_name   = $this->notificationsetting_model->getpatientDetails($patient_id);
                $doctor_details = $this->notificationsetting_model->getstaffDetails($doctor_id);

                $event_data = array(
                    'patient_id' => $patient_id,
                    'case_id'    => $case_reference_id,
                    'bill_no'    => $this->customlib->getSessionPrefixByType('radiology_billing') . $this->input->post('bill_no'),
                    'date'       => $this->customlib->YYYYMMDDTodateFormat($bill_date),
                    'total'      => $this->input->post('total'),
                    'discount'   => number_format((float) $this->input->post('discount'), 2, '.', ''),
                    'tax'        => number_format((float) $this->input->post('tax'), 2, '.', ''),
                    'net_amount' => $this->input->post('net_amount'),
                    'paid'       => $this->input->post('amount'),
                );

                if ($doctor_id != "") {
                    $doctor_details            = $this->notificationsetting_model->getstaffDetails($doctor_id);
                    $event_data['doctor_name'] = composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']);
                    $event_data['doctor_id']   = $doctor_id;
                } else {
                    $event_data['doctor_name'] = $doctor_name;
                    $event_data['doctor_id']   = $doctor_id;
                }

                $this->system_notification->send_system_notification('radiology_investigation', $event_data);

                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $inserted);
            } else {
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('something_went_wrong'));
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
            $prescription_no         = $this->input->post('prescription_no');
            $date                    = $this->input->post('date');
            $radiology_tests         = $this->radio_model->getradiotestDetails();
            $data["radiology_tests"] = $radiology_tests;
            $data["date"]            = $this->customlib->dateFormatToStrtotime($date);
            $data["payment_mode"]    = $this->payment_mode;
            $doctors                 = $this->staff_model->getStaffbyrole(3);
            $data["doctors"]         = $doctors;
            $case_reference_id       = "";
            $prefixes                = $this->prefix_model->getByCategory(array('ipd_prescription', 'opd_prescription'));
            $total_rows              = 0;
            $patient_id              = 0;
            $patient_name            = "";
            $prefix_type             = "";
            $prescription_prefix     = splitPrefixType($prescription_no);
            $prescription_no         = splitPrefixID($prescription_no);
            if (!empty($prefixes)) {
                $prefix_type = findPrefixType($prefixes, $prescription_prefix);
            }
    
            $prescription_test         = $this->prescription_model->getPrescriptionTestsByCategory($prescription_no, $prefix_type, 'radiology_id'); 
            
            $record_available = 0;
            if (!empty($prescription_test)) {
                $patient_name      = $prescription_test->patient_name;
                $patient_id        = $prescription_test->patient_id;
                $total_rows        = count($prescription_test->tests);
                $case_reference_id = $prescription_test->case_reference_id;
                $record_available  = 1;            
    
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
		
				$data['prescription_test'] = $prescription_test;
				$data['is_tpa']            =  $is_tpa ;
		
				$page             = $this->load->view("admin/radio/_prescriptionBill", $data, true);
		
				echo json_encode(array('status' => 1, 'page' => $page, 'patient_id' => $patient_id, 'total_rows' => $total_rows, 'patient_name' => $patient_name, 'record_available' => $record_available, 'case_reference_id' => $case_reference_id,'tpa_status'=>$tpa_status,	'msg'=> $this->lang->line('your_tpa_validity_has_expired_on').' '.$this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']).', '. $this->lang->line('so_standard_charges_has_been_applied')	));
				
			}else{
				
				echo json_encode(array('status' => 1, 'patient_id' => 0 ));
				
			}
        }       
    }

    public function addpatient()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_add')) {
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

    public function search()
    {
        if (!$this->rbac->hasPrivilege('radiology_test', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'radiology');
        $data                    = array();
        $data['fields']          = $this->customfield_model->get_custom_fields('radiologytest', 1);
        $categoryName            = $this->lab_model->getlabName();
        $data["categoryName"]    = $categoryName;
        $data['charge_category'] = $this->radio_model->getChargeCategory();
        $parametername           = $this->lab_model->getradioparameter();
        $data["parametername"]   = $parametername;
        $this->load->view('layout/header');
        $this->load->view('admin/radio/search', $data);
        $this->load->view('layout/footer');
    }

    public function getparameterdetails()
    {
        $id     = $this->input->get_post('id');
        $result = $this->lab_model->getradioparameter($id);
        echo json_encode($result);
    }

    public function getDetails()
    {
        if (!$this->rbac->hasPrivilege('radiology_test', 'can_view')) {
            access_denied();
        }
        $id                            = $this->input->post("radiology_id");
        $result                        = $this->radio_model->getDetails($id);
        $result['custom_fields_value'] = display_custom_fields('radiology', $id);
        $cutom_fields_data             = get_custom_table_values($id, 'radiology');
        $result['field_data']          = $cutom_fields_data;
        echo json_encode($result);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('radiology_test', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('test_name', $this->lang->line('test_name'), 'required');
        $this->form_validation->set_rules('short_name', $this->lang->line('short_name'), 'required');
        $this->form_validation->set_rules('test_type', $this->lang->line('test_type'), 'required');
        $this->form_validation->set_rules('radiology_category_id', $this->lang->line('category_name'), 'required');
        $this->form_validation->set_rules('charge_category_id', $this->lang->line('charge_category'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'test_name'             => form_error('test_name'),
                'short_name'            => form_error('short_name'),
                'test_type'             => form_error('test_type'),
                'radiology_category_id' => form_error('radiology_category_id'),
                'charge_category_id'    => form_error('charge_category_id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id                         = $this->input->post('id');
            $charge_category_id         = $this->input->post('charge_category_id');
            $id                         = $this->input->post('id');
            $charge_category_id         = $this->input->post('charge_category_id');
            $pre_radiology_parameter_id = $this->input->post("previous_radiology_parameter_id[]");
            $pre_radiology_id           = $this->input->post("previous_radiology_id");
            $pre_parameter_id           = $this->input->post("pre_parameter_id[]");
            $previous_parameter_id      = $this->input->post("previous_parameter_id[]");
            $new_parameter_id           = $this->input->post("new_parameter_id[]");
            $parameter_id               = $this->input->post("parameter_name[]");
            $insert_data                = array();
            $radiology                  = array(
                'id'                    => $id,
                'test_name'             => $this->input->post('test_name'),
                'short_name'            => $this->input->post('short_name'),
                'test_type'             => $this->input->post('test_type'),
                'radiology_category_id' => $this->input->post('radiology_category_id'),
                'sub_category'          => $this->input->post('sub_category'),
                'report_days'           => $this->input->post('report_days'),
                'charge_id'             => $charge_category_id,
            );

            $delete_arr = array();
            foreach ($previous_parameter_id as $pkey => $pvalue) {
                if (in_array($pvalue, $new_parameter_id)) {
                } else {
                    $delete_arr[] = array('id' => $pvalue);
                }
            }

            $i = 0;
            $j = 0;
            foreach ($parameter_id as $key => $value) {
                if (!empty($pre_radiology_parameter_id)) {
                    if (array_key_exists($i, $pre_radiology_parameter_id)) {
                        $detail = array(
                            'parameter_id' => $parameter_id[$i],
                            'id'           => $pre_radiology_parameter_id[$i],
                        );
                        $data[] = $detail;
                    } else {
                        $j++;
                        $insert_detail = array(
                            'radiology_id' => $pre_radiology_id,
                            'parameter_id' => $parameter_id[$i],
                        );
                        $insert_data[] = $insert_detail;
                    }
                } else {

                    $insert_detail = array(
                        'radiology_id' => $id,
                        'parameter_id' => $parameter_id[$i],
                    );
                    $insert_data[] = $insert_detail;
                }
                $i++;
            }

            $k         = $i - $j;
            $s         = 1;
            $condition = "";
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    if ($s == $k) {
                        $coma = '';
                    } else {
                        $coma = ',';
                    }
                    $condition .= "(" . $value['parameter_id'] . "," . $value['id'] . ")" . $coma;
                    $s++;
                }
            }

            if (!empty($data)) {
                $this->radio_model->updateparameter($condition);
            }
            if (!empty($insert_data)) {
                $this->radio_model->addparameter($insert_data);
            }
            if (!empty($delete_arr)) {
                $this->radio_model->delete_parameter($delete_arr);
            }
            $this->radio_model->update($radiology);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (!$this->rbac->hasPrivilege('radiology_test', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->radio_model->delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }
	
    public function deleteRadiologyBill()
    {
        $id = $this->input->post('id');
        if (!$this->rbac->hasPrivilege('radiology_test', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->radio_model->deleteRadiologyBill($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function getRadiology()
    {
        if (!$this->rbac->hasPrivilege('radiology_test', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post('radiology_id');
        $result = $this->radio_model->getRadiology($id);        
        echo json_encode($result);
    }

    public function testReportBatch()
    {
        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('radiology_id', $this->lang->line('radiology_id'), 'required');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'required');
        $this->form_validation->set_rules('reporting_date', $this->lang->line('reporting_date'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'radiology_id'   => form_error('radiology_id'),
                'patient_id'     => form_error('patient_id'),
                'reporting_date' => form_error('reporting_date'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $bill_no = $this->radio_model->getMaxId();
            if (empty($bill_no)) {
                $bill_no = 0;
            }
            $bill           = $bill_no + 1;
            $id             = $this->input->post('radiology_id');
            $patient_id     = $this->input->post('patient_id');
            $reporting_date = $this->input->post("reporting_date");

            $report_batch = array(
                'bill_no'           => $bill,
                'radiology_id'      => $id,
                'patient_id'        => $patient_id,
                'customer_type'     => $this->input->post('customer_type'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($reporting_date),
                'description'       => $this->input->post('description'),
                'generated_by'      => $this->customlib->getLoggedInUserID(),
                'apply_charge'      => $this->input->post('apply_charge'),
            );
            $insert_id       = $this->radio_model->testReportBatch($report_batch);
            $paramet_details = $this->radio_model->getparameterBypathology($id);
            foreach ($paramet_details as $pkey => $pvalue) {
                # code...

                $paramet_insert_array = array(
                    'radiology_report_id' => $insert_id,
                    'parameter_id'   => $pvalue["parameter_id"],
                );

                $insert_into_parameter = $this->radio_model->addParameterforPatient($paramet_insert_array);
            }

            if (isset($_FILES["radiology_report"]) && !empty($_FILES['radiology_report']['name'])) {
                $fileInfo = pathinfo($_FILES["radiology_report"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["radiology_report"]["tmp_name"], "./uploads/radiology_report/" . $img_name);
                $data_img = array('id' => $insert_id, 'radiology_report' => $img_name);
                $this->radio_model->testReportBatch($data_img);
            }
            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getReportDetails($id, $parameter_id = '')
    {
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details            = $this->printing_model->get('', 'radiology');
        $data['print_details']    = $print_details;
        $result                   = $this->radio_model->getBillDetails($id);
        $data['result']           = $result;
        $detail                   = $this->radio_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parameterdetails         = $this->radio_model->getparameterDetailsforpatient($id);
        $data['parameterdetails'] = $parameterdetails;
        $this->load->view('admin/radio/printReport', $data);
    }

    public function getBillDetails()
    {
        $print_details         = $this->printing_model->get('', 'radiology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $data['bill_prefix'] = $this->customlib->getSessionPrefixByType('radiology_billing');
        $data['fields']      = $this->customfield_model->get_custom_fields('radiology', '', 1);
        $result              = $this->radio_model->getRadiologyBillByID($id);

        if ($result->ipd_prescription_basic_id != '') {
            $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($result->ipd_prescription_basic_id);
            if ($ipd_opd->ipd_id != '') {
                $data['prescription']   =   $this->customlib->getSessionPrefixByType('ipd_no') . $ipd_opd->ipd_id;
            } else {
                $data['prescription']   =    $this->customlib->getSessionPrefixByType('opd_no') . $ipd_opd->visit_details_id;
            }
        } else {
            $data['prescription']   = '';
        }

        $data['result'] = $result;
        $page           = $this->load->view('admin/radio/_getBillDetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getradiobilldetails()
    {
        if (!$this->rbac->hasPrivilege('radiology', 'can_view')) {
            access_denied();
        }
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $id                    = $this->input->post("id");
        $print_details         = $this->printing_model->get('', 'radiology');
        $data['print_details'] = $print_details;
        $result                = $this->radio_model->getradioBillDetails($id);
        $data['result']        = $result;
        $detail                = $this->radio_model->getAllradioBillDetails($id);
        $data['detail']        = $detail;
        $action_details        = "";

        if ($this->rbac->hasPrivilege('radiology', 'can_view')) {
            $action_details .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-toggle='tooltip' class='print_bill' data-record-id='" . $id . "' data-original-title='" . $this->lang->line('print') . "'><i class='fa fa-print'></i></a>";
        }

        if ($this->rbac->hasPrivilege('radiology', 'can_edit')) {
            $action_details .= "<a href='#' class='edit_bill' data-record-id='" . $id . "' data-toggle='tooltip'  data-original-title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
        }

        if ($this->rbac->hasPrivilege('radiology', 'can_delete')) {
            $action_details .= "<a data-record-id='" . $id . "'  href='#'  data-toggle='tooltip'  data-original-title='" . $this->lang->line('delete') . "' class='delete-record'><i class='fa fa-trash'></i></a>";
        }

        $page = $this->load->view('admin/radio/printBill', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $action_details));
    }

    public function gettestreportbatch()
    {
        if (!$this->rbac->hasPrivilege('radiology_bill', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'radiology');
        $id                         = $this->input->post("radiology_id");
        $radiologist                = $this->staff_model->getStaffbyrole(6);
        $data["radiologist"]        = $radiologist;
        $data['radiologist_select'] = '';
        $testlist                   = $this->radio_model->getradiotestDetails();
        $data["testlist"]           = $testlist;
        $patients                   = $this->patient_model->getPatientListall();
        $data["patients"]           = $patients;
        $data["bloodgroup"]         = $this->bloodbankstatus_model->get_product(null, 1);
        $data['fields']             = $this->customfield_model->get_custom_fields('radiology', 1);
        $data['organisation']       = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/radio/reportDetail', $data);
        $this->load->view('layout/footer');
    }

    public function updatecollection()
    {
        $this->form_validation->set_rules('radiology_report_id', $this->lang->line('Report_id'), 'required');
        $this->form_validation->set_rules('collected_by', $this->lang->line('collected_by'), 'required');
        $this->form_validation->set_rules('collected_date', $this->lang->line('collected_date'), 'required');
        $this->form_validation->set_rules('radiology_center', $this->lang->line('radiology_center'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'radiology_report_id' => form_error('radiology_report_id'),
                'collected_by'        => form_error('collected_by'),
                'collected_date'      => form_error('collected_date'),
                'radiology_center'    => form_error('radiology_center'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date           = $this->input->post('collected_date');
            $collected_date = $this->customlib->dateFormatToYYYYMMDD($date);
            $payment_array  = array(
                'id'                    => $this->input->post('radiology_report_id'),
                'collection_specialist' => $this->input->post('collected_by'),
                'radiology_center'      => $this->input->post('radiology_center'),
                'collection_date'       => $collected_date,
            );

            $this->radio_model->updateTestReport($payment_array);

            $radiology_detail             = $this->radio_model->getRadiologyReportByID($this->input->post('radiology_report_id'));
            $test_detail                  = $this->notificationsetting_model->getRadiologyBillReportByID($radiology_detail['radiology_bill_id']);
            $sample_collected_person_name = $this->notificationsetting_model->getstaffDetails($this->input->post('collected_by'));
            $doctor_details               = $this->notificationsetting_model->getstaffDetails($test_detail['doctor_id']);

            $event_data = array(
                'patient_id'                   => $radiology_detail['patient_id'],
                'case_id'                      => $test_detail['case_reference_id'],
                'bill_no'                      => $this->customlib->getSessionPrefixByType('radiology_billing') . $radiology_detail['radiology_bill_id'],
                'collected_date'               => $this->customlib->YYYYMMDDTodateFormat($this->input->post('collected_date')),
                'test_name'                    => $test_detail['test_name'],
                'sample_collected_person_name' => composeStaffNameByString($sample_collected_person_name['name'], $sample_collected_person_name['surname'], $sample_collected_person_name['employee_id']),
                'radiology_center'             => $this->input->post('radiology_center'),
                'expected_date'                => $this->customlib->YYYYMMDDTodateFormat($test_detail['reporting_date']),
            );

            if ($this->input->post('collected_by') != "") {
                $event_data['doctor_id']   = $this->input->post('collected_by');
                $event_data['doctor_name'] = composeStaffNameByString($sample_collected_person_name['name'], $sample_collected_person_name['surname'],$sample_collected_person_name['employee_id']);
                $event_data['role_id']     = $sample_collected_person_name['role_id'];
            }

            $this->system_notification->send_system_notification('radiology_sample_collection', $event_data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function updatereportparam()
    {
        $this->form_validation->set_rules('radiology_report_id', $this->lang->line('report_id'), 'required');
        $this->form_validation->set_rules('approved_by', $this->lang->line('approved_by'), 'required');
        $this->form_validation->set_rules('approve_date', $this->lang->line('approve_date'), 'required');
        $this->form_validation->set_rules('attachment_report', $this->lang->line('upload_report'), 'callback_handle_doc_upload[attachment_report]');
        $rows = $this->input->post('radiology_parameterdetails');
        if (isset($rows) && !empty($rows)) {
            foreach ($rows as $row_key => $row_value) {
                $input_fields              = $this->input->post('radiology_parameter_' . $row_value);
                $pathology_reference_range = $this->input->post('radiology_reference_range_' . $row_value);
                if ($input_fields == "") {
                    $this->form_validation->set_rules('radiology_parameter', $this->lang->line('radiology_parameter'), 'required');
                }                
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'radiology_report_id'         => form_error('radiology_report_id'),
                'approved_by'                 => form_error('approved_by'),
                'approve_date'                => form_error('approve_date'),
                'radiology_parameter'         => form_error('radiology_parameter'),
                'radiology_parameter_invalid' => form_error('radiology_parameter_invalid'),
                'attachment_report'           => form_error('attachment_report'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $insert_array        = array();
            $update_array        = array();
            $approved_by         = $this->input->post('approved_by');
            $rows                = $this->input->post('radiology_parameterdetails');
            $radiology_report_id = $this->input->post('radiology_report_id');
            $test_result         = $this->input->post('test_result');

            if (!empty($rows)) {
                foreach ($rows as $row_key => $row_value) {
                    $prev_id = $this->input->post('prev_id_' . $row_value);
                    if ($prev_id == 0) {
                        $insert_array[] = array(
                            'radiology_report_id'          => $radiology_report_id,
                            'radiology_parameterdetail_id' => $row_value,
                            'radiology_report_value'       => $this->input->post('radiology_parameter_' . $row_value),
                        );
                    } else {
                        $update_array[] = array(
                            'id'                           => $prev_id,
                            'radiology_report_id'          => $radiology_report_id,
                            'radiology_parameterdetail_id' => $row_value,
                            'radiology_report_value'       => $this->input->post('radiology_parameter_' . $row_value),
                        );
                    }
                }
                $delete_array = array();
                $approve_date = $this->customlib->dateFormatToYYYYMMDD($this->input->post('approve_date'));
                $this->radio_model->addParameterforPatient($radiology_report_id, $approved_by, $approve_date, $insert_array, $update_array, $delete_array, $test_result);
            }
            //==========
            if (isset($_FILES["attachment_report"]) && !empty($_FILES["attachment_report"]['name'])) {
                $fileInfo        = pathinfo($_FILES["attachment_report"]["name"]);
                $attachment_name = $_FILES["attachment_report"]['name'];
                $img_name        = $radiology_report_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["attachment_report"]["tmp_name"], "./uploads/radiology_report/" . $img_name);
                $data_img = array('id' => $radiology_report_id, 'radiology_report' => 'uploads/radiology_report/' . $img_name, 'report_name' => $attachment_name);
                $this->radio_model->updateTestReport($data_img);
            }
            //==========

            $radiology_detail = $this->radio_model->getRadiologyReportByID($this->input->post('radiology_report_id'));
            $test_detail      = $this->notificationsetting_model->getRadiologyBillReportByID($radiology_detail['radiology_bill_id']);
            $approved_by      = $this->notificationsetting_model->getstaffDetails($this->input->post('approved_by'));

            $sample_collected_person_name = $this->notificationsetting_model->getstaffDetails($this->input->post('collected_id'));
            $event_data                   = array(
                'patient_id'                   => $radiology_detail['patient_id'],
                'case_id'                      => $test_detail['case_reference_id'],
                'bill_no'                      => $this->customlib->getSessionPrefixByType('radiology_billing') . $radiology_detail['radiology_bill_id'],
                'collected_date'               => $this->customlib->YYYYMMDDTodateFormat($this->input->post('collected_date')),
                'test_name'                    => $test_detail['test_name'],
                'doctor_id'                    => $this->input->post('approved_by'),
                'doctor_name'                  => $this->input->post('collected_by'),
                'role_id'                      => $sample_collected_person_name['role_id'],
                'sample_collected_person_name' => $this->input->post('collected_by'),
                'radiology_center'             => $this->input->post('radiology_center'),
                'expected_date'                => $this->customlib->YYYYMMDDTodateFormat($test_detail['reporting_date']),
                'approved_by'                  => composeStaffNameByString($approved_by['name'], $approved_by['surname'], $approved_by['employee_id']),
                'approved_date'                => $this->customlib->YYYYMMDDTodateFormat($this->input->post('approve_date')),
            );

            $this->system_notification->send_system_notification('radiology_test_report', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['file']['tmp_name'])) {
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

    public function getRadiologyReportDetail()
    {
        $id             = $this->input->post('id');
        $data['id']     = $id;
        $staff          = $this->staff_model->getStaffbyrole(3);
        $data["staff"]  = $staff;
        $result         = $this->radio_model->getPatientRadiologyReportDetails($id);
        $data['result'] = $result;
        $page           = $this->load->view('admin/radio/_getRadiologyReportDetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function assigntestradio()
    {
        $testlist             = $this->radio_model->getradiotestDetails();
        $data["testlist"]     = $testlist;
        $patients             = $this->patient_model->getPatientListall();
        $data["patients"]     = $patients;
        $doctors              = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]      = $doctors;
        $data['payment_mode'] = $this->payment_mode;
        $page                 = $this->load->view("admin/radio/_assigntestradio", $data, true);
        $result               = $this->radio_model->getBillNo();
        $id                   = $result["id"];
        if (!empty($result["id"])) {
            $bill_no = $id + 1;
        } else {
            $bill_no = 1;
        }
        echo json_encode(array('status' => 1, 'page' => $page, 'bill_no' => $bill_no));
    }

    public function getedittestradio()
    {
        if (!$this->rbac->hasPrivilege('radiology', 'can_view')) {
            access_denied();
        }
        $id               = $this->input->post('id');
        $testlist         = $this->radio_model->getradiotestDetails();
        $data["testlist"] = $testlist;
        $patients         = $this->patient_model->getPatientListall();
        $data["patients"] = $patients;
        $doctors          = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]  = $doctors;
        $page             = $this->load->view("admin/radio/_edittestradio", $data, true);
        $result           = $this->radio_model->getBillNo();
        $id               = $result["id"];
        if (!empty($result["id"])) {
            $bill_no = $id + 1;
        } else {
            $bill_no = 1;
        }

        echo json_encode(array('status' => 1, 'page' => $page, 'bill_no' => $bill_no));
    }

    public function getradiologybillDatatable()
    {
        $dt_response = $this->radio_model->getAllradiologybillRecord();
        $fields      = $this->customfield_model->get_custom_fields('radiology', 1);
        $dt_response = json_decode($dt_response);

        $dt_data = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row            = array();
                $balance_amount = ($value->net_amount) - ($value->paid_amount);
                //====================================
                $action = "<div class='rowoptionview rowview-btn-top'>";

                $action .= "<a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs view_detail' data-toggle='tooltip' title='" . $this->lang->line('view_reports') . "' ><i class='fa fa-reorder'></i></a>";
                if ($this->rbac->hasPrivilege('radiology_partial_payment', 'can_view')) {
                    $action .= "<a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs add_payment' data-toggle='tooltip' title='" . $this->lang->line('add_view_payments') . "' ><i class='fa fa-money'></i></a>";
                }
                if ($value->case_reference_id > 0) {
                    $case_id = $value->case_reference_id;
                } else {
                    $case_id = '';
                }
                $action .= "</div>";

                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('radiology_billing') . $value->id . $action;
                $row[] = $case_id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date);
                $row[] = $value->patient_name . " (" . $value->pid . ")";
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $value->note;
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
                $row[] = $value->discount.' ('.$value->discount_percentage.'%)';
                 
                $row[] = $value->net_amount;
                $row[] = $value->paid_amount;
                $row[] = number_format($balance_amount, 2);

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

    public function getPatientRadiologyDetails()
    {
        $id                  = $this->input->post('id');
        $data['id']          = $id;
        $result              = $this->radio_model->getRadiologyBillByID($id);

        if ($result->ipd_prescription_basic_id != '') {
            $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($result->ipd_prescription_basic_id);
            if (!empty($ipd_opd)) {
                if ($ipd_opd->ipd_id != '') {
                    $data['prescription']   =   $this->customlib->getSessionPrefixByType('ipd_prescription') . $ipd_opd->id;
                } else {
                    $data['prescription']   =    $this->customlib->getSessionPrefixByType('opd_prescription') . $ipd_opd->id;
                }
            } else {
                $data['prescription']   = '';
            }
        } else {
            $data['prescription']   = '';
        }

        $data['bill_prefix'] = $this->customlib->getSessionPrefixByType('radiology_billing');
        $is_bill             = $this->input->post('is_bill');
        if (isset($is_bill)) {
            $data['is_bill'] = false;
        } else {
            $data['is_bill'] = true;
        }
        $data['fields'] = $this->customfield_model->get_custom_fields('radiology');
        $data['result'] = $result;
        $page           = $this->load->view('admin/radio/_getPatientRadiologyDetails', $data, true);
        $actions        = "";
        if (isset($is_bill)) {
			
            $actions .= "<a href='javascript:void(0)' data-loading-text='" . $this->lang->line('please_wait') . "' class='print_radiology_bill' data-toggle='tooltip' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
			
        } else {

            $actions .= "<a href='javascript:void(0)' data-placement='bottom' data-loading-text='" . $this->lang->line('please_wait') . "' class='print_parameter' data-toggle='tooltip' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('print_test_report') . "'><i class='fa fa-reorder'></i></a>";
            $actions .= "<a href='javascript:void(0)' data-placement='bottom' data-loading-text='" . $this->lang->line('please_wait') . "' class='print_bill' data-toggle='tooltip' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
            if ($this->rbac->hasPrivilege('radiology_bill', 'can_edit')) {
                $actions .= "<a href='javascript:void(0)' data-loading-text='" . $this->lang->line('please_wait') . "' class='edit_radiology' data-toggle='tooltip' data-record-id=\"" . $id . "\"   data-placement='bottom' data-original-title='" . $this->lang->line('edit_radiology') . "'><i class='fa fa-pencil'></i></a>";
            }
            if ($this->rbac->hasPrivilege('radiology_bill', 'can_delete')) {
                $actions .= "<a href='javascript:void(0)' data-placement='bottom' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='delete_radiology' data-toggle='tooltip' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
            }
			
        }
        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
    }

    public function getReportCollectionDetail()
    {
        $id = $this->input->post('id');
        $radiology_center = "In-House Radiology Lab";
        $report           = $this->radio_model->getRadiologyReportByID($id);

        if (!empty($report)) {
            if ($report['radiology_center'] == "") {
                $report['radiology_center'] = $radiology_center;
            }
            if ($report['collection_date'] == "") {
                $report['collection_date'] = date('Y-m-d');
            }
        }

        echo json_encode(array('status' => 1, 'report' => $report));
    }

    public function getradiologytestDatatable()
    {
        $dt_response = $this->radio_model->getAllradiologytest();
        $fields      = $this->customfield_model->get_custom_fields('radiologytest', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-btn-top'>";

                if ($this->rbac->hasPrivilege('radiology_test', 'can_view')) {
                    $action .= "<a href='#' data-toggle='tooltip' onclick='viewDetail(" . $value->id . ")'  class='btn btn-default btn-xs' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
                }
                $action .= "</div'>";
                //==============================
                $row[] = $value->test_name . "</a> " . $action;
                $row[] = $value->short_name;
                $row[] = $value->test_type;
                $row[] = $value->lab_name;
                $row[] = $value->sub_category;
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
                $row[]     = amountFormat($value->standard_charge);
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
    
    public function gettestradiodetails()
    {
        $id     = $this->input->post('id');
        $patient_id = $this->input->post('patient_id');
        $is_tpa_valid = true;
        if ($this->input->post('is_tpa') == 1 && $patient_id != "") {
            $result = $this->radio_model->getRadiologyChargeByPatient($id, $patient_id);
            if ($result && !IsNullOrEmptyString($result->insurance_validity)) {
                if (strtotime($result->insurance_validity) >= strtotime(date('Y-m-d'))) {
                   
                    $result->standard_charge = $result->org_charge;
                } else {
                    $is_tpa_valid = false;
                }
            }
        } else {
            $result = $this->radio_model->getRadiologyChargeByPatient($id, $patient_id);
        }

        if (!$result) {
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('no_charge_has_configured_for_selected_category')));
        } elseif ($result && !$is_tpa_valid) {
            echo json_encode(array('status' => 2, 'result' => $result, 'msg' => $this->lang->line('your_tpa_validity_has_expired_on').' '.$this->customlib->YYYYMMDDTodateFormat($result->insurance_validity).', '. $this->lang->line('so_standard_charges_has_been_applied')));
        } else {
            echo json_encode(array('status' => 1, 'result' => $result));
        }
    }

    public function getRadiologyReport()
    {
        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_view')) {
            access_denied();
        }
        $id                       = $this->input->post('id');
        $result                   = $this->radio_model->getRadiologyReport($id);
        $result['reporting_date'] = $this->customlib->YYYYMMDDTodateFormat($result['reporting_date']);
        echo json_encode($result);
    }

    public function updateTestReport()
    {
        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_edit')) {
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

            $report_batch = array(
                'id'                => $id,
                'patient_name'      => $this->input->post('patient_name'),
                'patient_id'        => $this->input->post('patient_id_radio'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($reporting_date),
                'description'       => $this->input->post('description'),
                'apply_charge'      => $this->input->post('apply_charge'),
            );
            $this->radio_model->updateTestReport($report_batch);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

            if (!empty($_FILES['radiology_report']['name'])) {
                $config['upload_path']   = 'uploads/radiology_report/';
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['file_name']     = $_FILES['radiology_report']['name'];
                //Load upload library and initialize configuration
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('radiology_report')) {
                    $uploadData = $this->upload->data();
                    $picture    = $uploadData['file_name'];
                    $data_img   = array('id' => $id, 'radiology_report' => $picture);
                    $this->radio_model->updateTestReport($data_img);
                }
            }
        }
        echo json_encode($array);
    }

    public function parameteraddvalue()
    {
        if (!$this->rbac->hasPrivilege('radiology_test', 'can_edit')) {
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
                'patient_id'        => $this->input->post('patient_id_radio'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($reporting_date),
                'description'       => $this->input->post('description'),
                'apply_charge'      => $this->input->post('apply_charge'),
            );

            $parameter_id    = $this->input->post('parameter_id[]');
            $parameter_value = $this->input->post('parameter_value[]');
            $i               = 0;
            $parameter_array = array();
            if (!empty($parameter_id)) {
                foreach ($parameter_id as $pkey => $pvalue) {
                    $parameter_value_arr = array(
                        'id'                     => $pvalue,
                        'radiology_report_id'    => $id,
                        'radiology_report_value' => $parameter_value[$i],
                    );

                    $this->radio_model->addparametervalue($parameter_value_arr);
                    $i++;
                }
            }

            if (!empty($_FILES['radiology_report']['name'])) {
                $config['upload_path']   = 'uploads/radiology_report/';
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['file_name']     = $_FILES['radiology_report']['name'];
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                $fileInfo = pathinfo($_FILES["radiology_report"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                $fileInfo = pathinfo($_FILES["radiology_report"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                $data_img = array('id' => $id, 'radiology_report' => $img_name);
                $this->radio_model->updateTestReport($data_img);
                move_uploaded_file($_FILES["radiology_report"]["tmp_name"], "./uploads/radiology_report/" . $img_name);
            }

            $this->radio_model->updateTestReport($report_batch);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function download($doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/radiology_report/" . $doc;
        $data     = file_get_contents($filepath);
        force_download($doc, $data);
    }

    public function deleteTestReport($id)
    {
        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_delete')) {
            access_denied();
        }
        $this->radio_model->deleteTestReport($id);
    }

    public function deletetestbill($id)
    {
        if (!$this->rbac->hasPrivilege('add_radio_patient_test_report', 'can_delete')) {
            access_denied();
        }
        $this->radio_model->deletetestbill($id);
    }

    public function radiologyReport()
    {
        if (!$this->rbac->hasPrivilege('radiology_test', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/radiology');
        $this->session->set_userdata('subsub_menu', 'reports/radio/radiologyreport');
        $custom_fields        = $this->customfield_model->get_custom_fields('radiology', '', '', 1);
        $staffsearch          = $this->patient_model->getstaffbytransactionbill();
        $data['staffsearch']  = $staffsearch;
        $data["searchlist"]   = $this->search_type;
        $data['fields']       = $custom_fields;
        $data['testlist']     = $this->radio_model->getradiotestDetails();
        $radiologist          = $this->staff_model->getStaffbyrole(6);
        $data["radiologist"]  = $radiologist;
        $categoryName         = $this->lab_model->getlabName();
        $data["categoryName"] = $categoryName;
        $this->load->view('layout/header');
        $this->load->view('admin/radio/radiologyReport', $data);
        $this->load->view('layout/footer');
    }

    public function checkvalidation()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type'   => form_error('search_type'),
                'collect_staff' => form_error('collect_staff'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'           => $this->input->post('search_type'),
                'collect_staff'         => $this->input->post('collect_staff'),
                'date_from'             => $this->input->post('date_from'),
                'date_to'               => $this->input->post('date_to'),
                'test_name'             => $this->input->post('test_name'),
                'radiology_category_id' => $this->input->post('radiology_category_id'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function radiologyreports()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['collect_staff'] = $this->input->post('collect_staff');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $start_date              = "";
        $end_date                = "";

        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();

        $condition['collect_staff']         = $this->input->post('collect_staff');
        $condition['test_name']             = $this->input->post('test_name');
        $condition['radiology_category_id'] = $this->input->post('radiology_category_id');

        $fields = $this->customfield_model->get_custom_fields('radiology', '', '', 1);
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

        $reportdata    = $this->transaction_model->radiologybillreportsRecord($condition);
        $reportdata    = json_decode($reportdata);
        $dt_data       = array();
        $total_charge  = 0;
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $standard_charge    =   (($value->standard_charge * $value->tax_percentage) / 100) + $value->standard_charge;
                $total_charge += $standard_charge;
                $row   = array();
                $row[] = $this->customlib->getSessionPrefixByType($value->module_no) . $value->module_id;
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = $value->lab_name;
                $row[] = $value->test_name . " (" . $value->short_name . ")";
                $row[] = $value->note;
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
                $row[]     = number_format($standard_charge, 2);
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

    public function getRadiologyTransaction()
    {
        $radiology_billing_id  = $this->input->post('id');
        $is_bill               = $this->input->post('is_bill');
        $radiology_transaction = $this->transaction_model->radiologyPayments($radiology_billing_id);
        if (isset($is_bill)) {
            $data['is_bill'] = true;
            $data['form_id'] = "add_radio_partial_payment";
        } else {
            $data['is_bill'] = false;
            $data['form_id'] = "add_partial_payment";
        }
        $radio_billing         = $this->radio_model->getRadiologyBillByID($radiology_billing_id);
        $data['radio_billing'] = $radio_billing;

        $data["radiology_billing_id"]  = $radiology_billing_id;
        $data["payment_mode"]          = $this->payment_mode;
        $data['radiology_transaction'] = $radiology_transaction;
        $page                          = $this->load->view("admin/radio/_getRadiologyTransaction", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function partialbill()
    {
        if (!$this->rbac->hasPrivilege('radiology_partial_payment', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('payment_date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|valid_amount');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required');

        if ($this->input->post('payment_mode') == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line("document"), 'callback_handle_doc_upload[document]');
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
            //======
            $radiology_billing_id     = $this->input->post('radiology_billing_id');
            $radiology_billing_detail = $this->transaction_model->radiologyTotalPayments($radiology_billing_id);
            $net_amount    = $radiology_billing_detail->net_amount;
            $amount_paying = $this->input->post('amount');
            $total_paid    = $radiology_billing_detail->total_paid;
            //==========

            if ($net_amount >= ($total_paid + $amount_paying)) {

                $picture         = "";
                $bill_date       = $this->input->post("payment_date");
                $payment_section = $this->config->item('payment_section');
                $payment_array   = array(
                    'amount'               => $this->input->post('amount'),
                    'type'                 => 'payment',
                    'patient_id'           => $this->input->post('patient_id'),
                    'section'              => $payment_section['radiology'],
                    'radiology_billing_id' => $this->input->post('radiology_billing_id'),
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
                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $fileInfo        = pathinfo($_FILES["document"]["name"]);
                    $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                    $attachment_name = $_FILES["document"]["name"];
                    move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
                }
                $cheque_date = $this->input->post("cheque_date");
                if ($this->input->post('payment_mode') == "Cheque") {
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

    public function printPatientReportDetail()
    {
        $print_details         = $this->printing_model->get('', 'radiology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
        $data['id']            = $id;
        $result                = $this->radio_model->getPatientRadiologyReportDetails($id);
        $data['bill_prefix']   = $this->customlib->getSessionPrefixByType('radiology_billing');
        $data['result']        = $result;
        $page                  = $this->load->view('admin/radio/_printPatientReportDetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function editradiology()
    {
        $id                          = $this->input->post('id');
        $radiology_data              = $this->radio_model->getRadiologyBillByID($id);
        $data["radiology_data"]      = $radiology_data;
        $testlist                    = $this->radio_model->getradiotestDetails();
        $data["testlist"]            = $testlist;
        $patients                    = $this->patient_model->getPatientList();
        $data["patients"]            = $patients;
        $patient_names               = array_column($patients, 'patient_name', 'id');
        $doctors                     = $this->staff_model->getStaffbyrole(3);
        $data['custom_fields_value'] = display_custom_fields('radiology', $id);
        $data["doctors"]             = $doctors;
        $data["payment_mode"]        = $this->payment_mode;
        $page                        = $this->load->view("admin/radio/_editradiology", $data, true);
        $total_rows                  = count($radiology_data->radiology_report);
        $case_reference_id           = $radiology_data->case_reference_id;
        $patient_id                  = $radiology_data->patient_id;
        $bill_no                     = $radiology_data->id;
        $date                        = $radiology_data->date;
        $organisation_id             = $radiology_data->organisation_id;

        if($organisation_id!=null || $organisation_id!=0 || $organisation_id!=""){
            $tpa_apply_status=1;
        }else{
            $tpa_apply_status=0;
        }
 
        echo json_encode(array('status' => 1, 'page' => $page, 'bill_no' => $bill_no, 'radiology_date' => $date, 'total_rows' => $total_rows, 'case_reference_id' => $case_reference_id, 'patient_id' => $patient_id, 'patient_name' => $patient_names[$patient_id] . " (" . $patient_id . ")",'tpa_apply_status'=>$tpa_apply_status));
    }

    public function printTransaction()
    {
        $print_details         = $this->printing_model->get('', 'paymentreceipt');
        $id                    = $this->input->post('id');
        $charge                = array();
        $transaction           = $this->transaction_model->radiologyPaymentByTransactionId($id);
        $data['print_details'] = $print_details;
        $data['transaction']   = $transaction;
        $page                  = $this->load->view('admin/radio/_printTransaction', $data, true);
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

    public function printtestparameterdetail()
    {
        $print_details         = $this->printing_model->get('', 'radiology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
        $data['id']            = $id;
        $data['head_result']   = $this->radio_model->getRadiologyBillByID($id);
        $result                = $this->radio_model->printtestparameterdetail($id);
        $data['bill_prefix']   = $this->customlib->getSessionPrefixByType('radiology_billing');
        $data['result']        = $result;
        $page                  = $this->load->view('admin/radio/_printtestparameterdetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function downloadReport($report_id)
    {
        $report = $this->radio_model->getPatientRadiologyReportDetails($report_id);
        $this->load->helper('download');
        $filepath    = $report->radiology_report;
        $report_name = $report->report_name;
        $data        = file_get_contents($filepath);
        force_download($report_name, $data);
    }
}
