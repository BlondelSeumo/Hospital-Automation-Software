<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Operationtheatre extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->config->load("mailsms");
        $this->notification            = $this->config->item('notification');
        $this->notificationurl         = $this->config->item('notification_url');
        $this->patient_notificationurl = $this->config->item('patient_notification_url');
        $this->config->load("image_valid");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('datatables');
        $this->load->model('transaction_model');
        $this->marital_status       = $this->config->item('marital_status');
        $this->payment_mode         = $this->config->item('payment_mode');
        $this->search_type          = $this->config->item('search_type');
        $this->blood_group          = $this->config->item('bloodgroup');
        $this->charge_type          = $this->customlib->getChargeMaster();
        $data["charge_type"]        = $this->charge_type;
        $this->patient_login_prefix = "pat";
        $this->load->library('customlib');
        $this->load->helper('customfield_helper');
        $this->load->library('system_notification');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function getBillDetails($id)
    {
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details         = $this->printing_model->get('', 'ot');
        $data['print_details'] = $print_details;
        $result                = $this->operationtheatre_model->getBillDetails($id);
        $data['result']        = $result;
        $detail                = $this->operationtheatre_model->getAllBillDetails($id);
        $data['detail']        = $detail;
        $this->load->view('admin/operationtheatre/printBill', $data);
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function addpatient()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload', 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
                'file' => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $check_patient_id = $this->patient_model->getMaxId();

            if (empty($check_patient_id)) {
                $check_patient_id = 1000;
            }

            $patient_id   = $check_patient_id + 1;
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
            $insert_id          = $this->patient_model->add_patient($patient_data);
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

    public function add()
    {
        $custom_fields = $this->customfield_model->getByBelong('operationtheatre');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[operationtheatre][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $this->form_validation->set_rules('date', $this->lang->line('operation_date'), 'required');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'required');
        $this->form_validation->set_rules('operation_category', $this->lang->line('operation_category'), 'required');
        $this->form_validation->set_rules('operation_name', $this->lang->line('operation_name'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(

                'operation_category' => form_error('operation_category'),
                'operation_name'     => form_error('operation_name'),
                'date'               => form_error('date'),
                'consultant_doctor'  => form_error('consultant_doctor'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                         = $custom_fields_value['id'];
                        $custom_fields_name                                                       = $custom_fields_value['name'];
                        $error_msg2["custom_fields[operationtheatre][" . $custom_fields_id . "]"] = form_error("custom_fields[operationtheatre][" . $custom_fields_id . "]");
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

            $patientname          = $this->input->post('patientname');
            $opd_ipd_patient_type = $this->input->post('opd_ipd_patient_type');
            $custom_field_post    = $this->input->post("custom_fields[operationtheatre]");
            $opd_id               = $this->input->post('opdid');
            $date                 = $this->input->post("date");
            $operation_detail     = array(
                'opd_details_id'    => $opd_id,
                'operation_id'      => $this->input->post('operation_name'),
                'date'              => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'ass_consultant_1'  => $this->input->post('ass_consultant_1'),
                'ass_consultant_2'  => $this->input->post('ass_consultant_2'),
                'anesthetist'       => $this->input->post('anesthetist'),
                'anaethesia_type'   => $this->input->post('anaethesia_type'),
                'ot_technician'     => $this->input->post('ot_technician'),
                'ot_assistant'      => $this->input->post('ot_assistant'),
                'remark'            => $this->input->post('ot_remark'),
                'result'            => $this->input->post('ot_result'),
                'operation_type'    => $this->input->post('operation_type'),
                'generated_by'      => $this->customlib->getLoggedInUserID(),
            );

            $insert_id          = $this->operationtheatre_model->operation_detail($operation_detail);
            $array              = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'id' => $insert_id);
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[operationtheatre][" . $key . "]");
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
            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["ot"];

            $url = $url_link . '/' . $insert_id; 
            $operation_details = $this->operationtheatre_model->otdetails($insert_id);
            $patient_details   = $this->patient_model->get_patientidbyopdid($opd_id);

            $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('consultant_doctor'));
            $event_data     = array(
                'patient_id'     => $patient_details['patient_id'],
                'opd_no'         => $this->customlib->getSessionPrefixByType('opd_no') . $opd_id,
                'case_id'        => $patient_details['case_reference_id'],
                'operation_name' => $operation_details->operation,
                'operation_date' => $date,                
				'doctor_id'      => $this->input->post('consultant_doctor'),
                'doctor_name'    => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
            );

            $this->system_notification->send_system_notification('add_opd_operation', $event_data);
        }
        echo json_encode($array);
    }

    public function addipdot()
    {
        $custom_fields = $this->customfield_model->getByBelong('operationtheatre');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[operationtheatre][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $this->form_validation->set_rules('date', $this->lang->line('operation_date'), 'required');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line("consultant_doctor"), 'trim|required');
        $this->form_validation->set_rules('operation_name', $this->lang->line('operation_name'), 'required');
        $this->form_validation->set_rules('operation_category', $this->lang->line('operation_category'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'operation_category' => form_error("operation_category"),
                'date'               => form_error('date'),
                'operation_name'     => form_error('operation_name'),
                'consultant_doctor'  => form_error("consultant_doctor"),

            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                         = $custom_fields_value['id'];
                        $custom_fields_name                                                       = $custom_fields_value['name'];
                        $error_msg2["custom_fields[operationtheatre][" . $custom_fields_id . "]"] = form_error("custom_fields[operationtheatre][" . $custom_fields_id . "]");
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

            $patientname          = $this->input->post('patientname');
            $opd_ipd_patient_type = $this->input->post('opd_ipd_patient_type');
            $custom_field_post    = $this->input->post("custom_fields[operationtheatre]");
            $ipd_id               = $this->input->post('ipdid');
            $date                 = $this->input->post("date");

            $operation_detail = array(
                'ipd_details_id'    => $ipd_id,
                'operation_id'      => $this->input->post('operation_name'),
                'date'              => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'operation_type'    => $this->input->post('operation_type'),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'ass_consultant_1'  => $this->input->post('ass_consultant_1'),
                'ass_consultant_2'  => $this->input->post('ass_consultant_2'),
                'anesthetist'       => $this->input->post('anesthetist'),
                'anaethesia_type'   => $this->input->post('anaethesia_type'),
                'ot_technician'     => $this->input->post('ot_technician'),
                'ot_assistant'      => $this->input->post('ot_assistant'),
                'generated_by'      => $this->customlib->getLoggedInUserID(),
                'remark'            => $this->input->post('ot_remark'),
                'result'            => $this->input->post('ot_result'),
            );

            $insert_id = $this->operationtheatre_model->operation_detail($operation_detail);
            $array     = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'id' => $insert_id);

            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[operationtheatre][" . $key . "]");
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
            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["ot"];
            $doctor_list       = $this->patient_model->getDoctorsipd($ipd_id);
            $operation_details = $this->operationtheatre_model->otdetails($insert_id);
            $patient_details   = $this->patient_model->get_patientidbyIpdId($ipd_id);

            $doctor_details    = $this->notificationsetting_model->getstaffDetails($this->input->post('consultant_doctor'));
            $consultant_doctor = $this->patient_model->get_patientidbyIpdId($ipd_id);

            $consultant_doctorarray[] = array('consult_doctor' => $this->input->post('consultant_doctor'), 'name' => $doctor_details['name'] . " " . $doctor_details['surname'] . "(" . $doctor_details['employee_id'] . ")");

            $consultant_doctorarray[] = array('consult_doctor' => $consultant_doctor['cons_doctor'], 'name' => $consultant_doctor['doctor_name'] . " " . $consultant_doctor['doctor_surname'] . "(" . $consultant_doctor['doctor_employee_id'] . ")");
            foreach ($doctor_list as $key => $value) {
                $consultant_doctorarray[] = array('consult_doctor' => $value['consult_doctor'], 'name' => $value['ipd_doctorname'] . " " . $value['ipd_doctorsurname'] . "(" . $value['employee_id'] . ")");
            }
            
            $date   =   $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format);
            $date   =   $this->customlib->YYYYMMDDHisTodateFormat($date, $this->customlib->getHospitalTimeFormat());

            $event_data = array(
                'patient_id'     => $patient_details['patient_id'],
                'ipd_no'         => $this->customlib->getSessionPrefixByType('ipd_no') . $ipd_id,
                'case_id'        => $this->input->post('case_id'),
                'operation_name' => $operation_details->operation,
                'operation_date' => $this->customlib->YYYYMMDDHisTodateFormat($date, $this->customlib->getHospitalTimeFormat()),                             
                'doctor_name'    => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
            );

            $this->system_notification->send_system_notification('add_ipd_operation', $event_data, $consultant_doctorarray);
        }

        echo json_encode($array);
    }

    public function test()
    {
        $doctors         = $this->staff_model->getStaffbyrole(3);
        $data["doctors"] = $doctors;
        $this->load->view('layout/header');
        $this->load->view('admin/operationtheatre/test.php', $data);
        $this->load->view('layout/footer');
    }

    public function otsearch($id = '')
    {
        if (!$this->rbac->hasPrivilege('ot_patient', 'can_view')) {
            access_denied();
        }

        $ot_data         = $this->session->flashdata('ot_data');
        $data['ot_data'] = $ot_data;

        $this->session->set_userdata('top_menu', 'operation_theatre');
        if (!empty($id)) {
            $data["id"] = $id;
        }

        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $symptoms_result            = $this->symptoms_model->get();
        $data['symptomsresult']     = $symptoms_result;
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $patients                   = $this->patient_model->getPatientListall();
        $data["patients"]           = $patients;
        $userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata['role_id'];
        $doctorid                   = "";
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }
        $data["doctor_select"]   = $doctorid;
        $data["disable_option"]  = $disable_option;
        $data['charge_category'] = $this->operationtheatre_model->getChargeCategory();
        $data['organisation']    = $this->organisation_model->get();
        $data['fields']          = $this->customfield_model->get_custom_fields('operationtheatre', 1);
        $this->load->view('layout/header');
        $this->load->view('admin/operationtheatre/otsearch', $data);
        $this->load->view('layout/footer');
    }

    public function getotDatatable()
    {
        $dt_response = $this->operationtheatre_model->getAllotRecord();
        $fields      = $this->customfield_model->get_custom_fields('operationtheatre', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                if (!empty($value->apply_charge)) {
                    $charge = $value->apply_charge;
                }
                $row = array();
                //====================================
                $action = "<div class='rowoptionview'>";
                if ($this->rbac->hasPrivilege('ot_consultant_instruction', 'can_add')) {
                    $action .= "<a href='#' onclick='add_instruction(" . $value->id . "," . $value->pid . "),refreshmodal()' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('consultant_instruction') . "'><i class='fa fa-user-md'></i></a>";
                }

                if ($this->rbac->hasPrivilege('ot_patient', 'can_view')) {
                    $action .= "<a href='#' onclick='viewDetail(" . $value->pid . "," . $value->id . ")'
                   class='btn btn-default btn-xs'  data-toggle='tooltip'  title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";
                }

                if ($this->rbac->hasPrivilege('ot_patient', 'can_view')) {
                    $action .= "<a href='#'  onclick='viewDetailBill(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip'  title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";
                }

                $action .= "</div>";
                $first_action = "<a href='#'   onclick='viewDetail(" . $value->pid . ")' data-toggle='tooltip' title='" . $this->lang->line('details') . "'  href=''>";
                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('operation_theater_billing') . $value->id . $action;
                $row[] = $first_action . $value->patient_name . "</a>";
                $row[] = $value->pid;
                $row[] = $value->gender;
                $row[] = $value->mobileno;
                $row[] = $value->operation_name;
                $row[] = $value->operation_type;
                $row[] = $value->name . " " . $value->surname . " (" . $value->employee_id . ")";
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
                $row[]     = $value->apply_charge;
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
        if (!$this->rbac->hasPrivilege('ot_patient', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post("patient_id");
        $ot_id  = $this->input->post("ot_id");
        $result = $this->operationtheatre_model->getDetails($id);
        if ($result['symptoms']) {
            $result['symptoms'] = nl2br($result['symptoms']);
        }

        if (($result['patient_type'] == 'Inpatient') || ($result['patient_type'] == 'Outpatient')) {
            $opd_ipd_no           = $this->operationtheatre_model->getopdipdDetails($id, $result['patient_type']);
            $result['opd_ipd_no'] = $opd_ipd_no;
        }
        $result['admission_date'] = date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['admission_date']));
        $result['date']           = date($this->customlib->getHospitalDateFormat(true, false), strtotime($result['date']));

        $cutom_fields_data    = get_custom_table_values($ot_id, 'operationtheatre');
        $result['field_data'] = $cutom_fields_data;
        echo json_encode($result);
    }

    public function getotDetails()
    {
        $id                            = $this->input->post("id");
        $result                        = $this->operationtheatre_model->getotDetails($id);
        $result['otdate']              = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('operationtheatre', $id);
        echo json_encode($result);
    }

    public function getOtPatientDetails()
    {
        if (!$this->rbac->hasPrivilege('ot_patient', 'can_view')) {
            access_denied();
        }
        $id                       = $this->input->post("id");
        $result                   = $this->operationtheatre_model->getOtPatientDetails($id);
        $result['admission_date'] = date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['admission_date']));
        $result['date']           = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['dob'] = $this->customlib->YYYYMMDDHisTodateFormat($result['dob'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('operationtheatre', $id);

        echo json_encode($result);
    }

    public function update()
    {
        $custom_fields = $this->customfield_model->getByBelong('operationtheatre');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];

                    $this->form_validation->set_rules("custom_fields[operationtheatre][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');

                }
            }
        }

        $this->form_validation->set_rules('eoperation_category', $this->lang->line('operation_category'), 'required');
        $this->form_validation->set_rules('eoperation_name', $this->lang->line('operation_name'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'eoperation_category' => form_error('eoperation_category'),
                'date'                => form_error('date'),
                'operation_name'      => form_error('eoperation_name'),
                'consultant_doctor'   => form_error('consultant_doctor'),

            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                         = $custom_fields_value['id'];
                        $custom_fields_name                                                       = $custom_fields_value['name'];
                        $error_msg2["custom_fields[operationtheatre][" . $custom_fields_id . "]"] = form_error("custom_fields[operationtheatre][" . $custom_fields_id . "]");
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

            $date = $this->input->post("date");

            $otid              = $this->input->post('otid');
            $custom_field_post = $this->input->post("custom_fields[operationtheatre]");
            $operation_detail  = array(
                'id'                => $otid,
                'operation_id'      => $this->input->post('eoperation_name'),
                'date'              => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'consultant_doctor' => $this->input->post('consultant_doctor'),
                'ass_consultant_1'  => $this->input->post('ass_consultant_1'),
                'ass_consultant_2'  => $this->input->post('ass_consultant_2'),
                'anesthetist'       => $this->input->post('anesthetist'),
                'anaethesia_type'   => $this->input->post('anaethesia_type'),
                'ot_technician'     => $this->input->post('ot_technician'),
                'ot_assistant'      => $this->input->post('ot_assistant'),
                'result'            => $this->input->post('eot_result'),
                'remark'            => $this->input->post('eot_remark'),

            );

            $this->operationtheatre_model->update_operation_detail($operation_detail);

            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[operationtheatre][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $otid,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $otid, 'patient');
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!empty($id)) {
            $this->operationtheatre_model->delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function add_ot_consultant_instruction()
    {
        if (!$this->rbac->hasPrivilege('ot_consultant_instruction', 'can_add')) {
            access_denied();
        }

        $custom_fields = $this->customfield_model->getByBelong('otconsultinstruction');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[otconsultinstruction][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $this->form_validation->set_rules('date', $this->lang->line('applied_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('doctor', $this->lang->line('consultant'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('instruction', $this->lang->line('instruction'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('insdate', $this->lang->line('instruction_date'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'        => form_error('date'),
                'doctor'      => form_error('doctor'),
                'instruction' => form_error('instruction'),
                'insdate'     => form_error('insdate'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                             = $custom_fields_value['id'];
                        $custom_fields_name                                                           = $custom_fields_value['name'];
                        $error_msg2["custom_fields[otconsultinstruction][" . $custom_fields_id . "]"] = form_error("custom_fields[otconsultinstruction][" . $custom_fields_id . "]");
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
            $date        = $this->input->post('date');
            $ins_date    = $this->input->post('insdate');
            $patient_id  = $this->input->post('patient_id');
            $doctor      = $this->input->post('doctor');
            $instruction = $this->input->post('instruction');
            $data_array  = array(
                'date'        => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'patient_id'  => $patient_id,
                'ins_date'    => date('Y-m-d', $this->customlib->datetostrtotime($ins_date)),
                'cons_doctor' => $doctor,
                'instruction' => $instruction,
            );

            $insert_id          = $this->operationtheatre_model->add_ot_consultantInstruction($data_array);
            $custom_field_post  = $this->input->post("custom_fields[otconsultinstruction]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[otconsultinstruction][" . $key . "]");
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
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getConsultantBatch()
    {
        $id             = $this->input->post("patient_id");
        $data["id"]     = $id;
        $result         = $this->operationtheatre_model->getConsultantBatch($id);
        $data["result"] = $result;
        $data['fields'] = $this->customfield_model->get_custom_fields('otconsultinstruction', 1);
        $this->load->view('admin/operationtheatre/patientConsultantDetail', $data);
    }

    public function OtReport()
    {
        if (!$this->rbac->hasPrivilege('ot_report', 'can_view')) {
            access_denied();
        }
        $doctorlist         = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist'] = $doctorlist;
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/ot');
        $this->session->set_userdata('subsub_menu', 'reports/operationtheatre/otreport');

        $staffsearch            = $this->staff_model->getStaffbyrole(3);      
        $data['staffsearch']    = $staffsearch;
        $data["searchlist"]     = $this->search_type;
        $data['fields']         = $this->customfield_model->get_custom_fields('operationtheatre', '', '', 1);
        $data['operation_list'] = $this->operationtheatre_model->operation_list();
        $data['categorylist']   = $this->operationtheatre_model->category_list();
        $this->load->view('layout/header');
        $this->load->view('admin/operationtheatre/otReport', $data);
        $this->load->view('layout/footer');
    }

    public function checkvalidation()
    {
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type' => form_error('search_type'),

            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'        => $this->input->post('search_type'),
                'consultant_doctor'      => $this->input->post('consultant_doctor'),
                'date_from'          => $this->input->post('date_from'),
                'date_to'            => $this->input->post('date_to'),
                'operation_category' => $this->input->post('operation_category'),
                'operation_name'     => $this->input->post('operation_name'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function otreports()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['consultant_doctor'] = $this->input->post('consultant_doctor');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $condition['consultant_doctor']      = $this->input->post('consultant_doctor');
        $condition['operation_category'] = $this->input->post('operation_category');
        $condition['operation_name']     = $this->input->post('operation_name');
        $start_date                      = '';
        $end_date                        = '';
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

        $fields     = $this->customfield_model->get_custom_fields('operationtheatre', '', '', 1);
        $reportdata = $this->transaction_model->otreportsRecord($condition);
        $reportdata = json_decode($reportdata);

        $dt_data = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                if ($value->opd_details_id) {
                    $opd_no = $this->customlib->getSessionPrefixByType('opd_no') . $value->opd_details_id;
                } else {
                    $opd_no = "";
                }
                if ($value->ipd_details_id) {
                    $ipd_no = $this->customlib->getSessionPrefixByType('ipd_no') . $value->ipd_details_id;
                } else {
                    $ipd_no = "";
                }

                $row   = array();
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $this->customlib->getSessionPrefixByType('operation_theater_reference_no') . $value->id;
                $row[] = $opd_no;
                $row[] = $ipd_no;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $value->ass_consultant_1;
                $row[] = $value->operation;
                $row[] = $value->category;
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
                $row[]     = $value->result;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function deleteConsultant($id)
    {
        if (!empty($id)) {
            $this->operationtheatre_model->deleteConsultant($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function otdetails()
    {
        $ot_id                                  = $this->input->post("ot_id");
        $data['otdetails']                      = $this->operationtheatre_model->otdetails($ot_id);
        $data['fields']                         = $this->customfield_model->get_custom_fields('operationtheatre');
        $data['operation_theater_reference_no'] = $this->customlib->getSessionPrefixByType('operation_theater_reference_no');
        $page                                   = $this->load->view("admin/operationtheatre/_otdetails", $data, true);

        $actions = "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_ot_bill'  data-toggle='tooltip' data-record-id=\"" . $ot_id . "\"   data-original-title='" . $this->lang->line('print') . "'><i class='fa fa-print'></i></a>";

        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
    }

    /* Function to list operation name */
    public function index()
    {  	
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'operation_theatre/index');
		$this->session->set_userdata('sub_sidebar_menu', 'admin/operationtheatre/index');	
        $data['category_list']  = $this->operationtheatre_model->category_list();
        $data['operation_list'] = $this->operationtheatre_model->operation_list();
        $this->load->view('layout/header');
        $this->load->view('admin/operationtheatre/operation', $data);
        $this->load->view('layout/footer');
    }

    /* this funtion is used to save multiple add_multiple_addoperation */
    public function add_multiple_addoperation() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('operation_name_'.$row_value, $this->lang->line('operation_name'), 'trim|required');        
                $this->form_validation->set_rules('category_'.$row_value, $this->lang->line('category'), 'trim|required');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array(
                        'operation_name' => form_error('operation_name_'.$row_value),
                        'category' => form_error('category_'.$row_value),
                    );
                    $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');  
                    echo json_encode($json_array);
                    return false;          
                }
            } 
            //validation to check each input keeps the value or not

            // save the multiple data 
            $total_rows   = $this->input->post('total_rows');  
            foreach ($total_rows as $row_key => $row_values) {                 
              
            $data = array(
                'operation'   => $this->input->post('operation_name_'.$row_values),
                'category_id' => $this->input->post('category_'.$row_values),
                'is_active'   => 'yes',
                'created_at'  => date('Y-m-d'),
            );

                $this->operationtheatre_model->add($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message=	$this->lang->line("please_add_at_least_one_operation_name");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}


    public function getoperation($id)
    {
        $result = $this->operationtheatre_model->operation_list($id);
        echo json_encode($result);
    }

    public function getoperationbycategory()
    {
        $id     = $this->input->post('id');
        $result = $this->operationtheatre_model->getoperationbycategory($id);
        echo json_encode($result);
    }

    public function edit()
    {
        $this->form_validation->set_rules('edit_operation_name', $this->lang->line('operation_name'), 'required');
        $this->form_validation->set_rules('edit_category', $this->lang->line('category'), 'required');
        $id = $this->input->post('id');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'name'     => form_error('edit_operation_name'),
                'category' => form_error('edit_category'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $operation = array(
                'operation'   => $this->input->post('edit_operation_name'),
                'category_id' => $this->input->post('edit_category'),
            );

            $this->operationtheatre_model->updateoperation($id, $operation);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }

        echo json_encode($array);
    }

    public function deleteoperation()
    {
        $id = $_REQUEST['id'];
        $this->operationtheatre_model->delete_operation($id);
    }

    public function print_otdetails()
    {
        $print_details         = $this->printing_model->get('', 'ot');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
        $data['otdetails']     = $this->operationtheatre_model->otdetailsforprint($id);
        $data['fields']        = $this->customfield_model->get_custom_fields('operationtheatre', '', 1);
        $type                  = "operationtheatre";
        $action                = '<a href="javascript:void(0);" class=" print_dischargecard" data-toggle="tooltip" title="" data-module_type="' . $type . '"  data-recordId="' . $id . '" data-original-title=""><i class="fa fa-print"></i> </a>';
        $page                  = $this->load->view('admin/operationtheatre/_printotdetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'action' => $action));
    }

    /* Function to list category */
    public function category()
    { 
        if (!$this->rbac->hasPrivilege('operation_category', 'can_view')) {
            access_denied();
        } 

		$this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'operation_theatre/index');
		$this->session->set_userdata('sub_sidebar_menu', 'admin/operationtheatre/category');

        $data['category_list'] = $this->operationtheatre_model->category_list();
        $this->load->view('layout/header');
        $this->load->view('admin/operationtheatre/category', $data);
        $this->load->view('layout/footer');

    }
   
    public function getcategory($id)
    {
        $result = $this->operationtheatre_model->category_list($id);
        echo json_encode($result);
    }

    public function deletecategory()
    {
        $id = $_REQUEST['id'];
        $this->operationtheatre_model->delete_category($id);
    }


    /* Function is used to add operation name */
    public function addmultiplecategory() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('category_'.$row_value, $this->lang->line('operation_category'), 'trim|required');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array(
                        'name' => form_error('category_'.$row_value),
                    );
                    $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');  
                    echo json_encode($json_array);
                    return false;          
                }
            } 
            //validation to check each input keeps the value or not

            // save the multiple data 
            $total_rows   = $this->input->post('total_rows');  
            foreach ($total_rows as $row_key => $row_values) {                   
                $id   =  $this->input->post('id_' . $row_values) ;     
                $data = array('category' =>  $this->input->post('category_' . $row_values), 'is_active' => 'yes', 'created_at' => date('Y-m-d'));
                $this->operationtheatre_model->insert($data, 'operation_category', $id);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message=	$this->lang->line("please_add_at_least_one_operation_category");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}

    /* Function is used to add operation name */
    public function editcategory()
    {
        $this->form_validation->set_rules('category_name', $this->lang->line('operation_category'), 'required');
        $id = $this->input->post('id');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('category_name'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            if ($id != "") {
                $data = array('category' => $this->input->post('category_name'));
            } else {
                $data = array('category' => $this->input->post('category_name'), 'is_active' => 'yes', 'created_at' => date('Y-m-d'));
            }

            $this->operationtheatre_model->insert($data, 'operation_category', $id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

}
