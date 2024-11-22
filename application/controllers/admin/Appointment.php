<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Appointment extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->config->load("mailsms");
        $this->notification            = $this->config->item('notification');
        $this->notificationurl         = $this->config->item('notification_url');
        $this->yesno_condition         = $this->config->item('yesno_condition');
        $this->patient_notificationurl = $this->config->item('patient_notification_url');
        $this->search_type             = $this->config->item('search_type');
        $this->load->library('mailsmsconf');
        $this->load->library('Enc_lib');
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->load->model(array('appoint_priority_model', 'onlineappointment_model', 'transaction_model', 'conference_model'));
        $this->appointment_status = $this->config->item('appointment_status');
        $this->load->helper('customfield_helper');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
        $this->config->load('image_valid');
        $this->payment_mode = $this->config->item('payment_mode');
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('appointment', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'appointment');
        $app_data                      = $this->session->flashdata('app_data');
        $data['app_data']              = $app_data;
        $doctors                       = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]               = $doctors;
        $data["appointment_status"]    = $this->appointment_status;
        $data["yesno_condition"]       = $this->yesno_condition;
        $userdata                      = $this->customlib->getUserData();
        $role_id                       = $userdata['role_id'];
        $data["bloodgroup"]            = $this->bloodbankstatus_model->get_product(null, 1);
        $doctorid                      = "";
        $data['appoint_priority_list'] = $this->appoint_priority_model->appoint_priority_list();
        $doctor_restriction            = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option                = false;

        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }

        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $data['fields']         = $this->customfield_model->get_custom_fields('appointment', 1);
        $data['payment_mode']   = $this->payment_mode;
        $data['organisation']    = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/appointment/index', $data);
        $this->load->view('layout/footer');
    }

    public function add()
    {
        $custom_fields = $this->customfield_model->getByBelong('appointment');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[appointment][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $this->form_validation->set_rules('date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('doctorid', $this->lang->line('doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('doctor_fees'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('global_shift', $this->lang->line('shift'), 'trim|required');
        $this->form_validation->set_rules('slot', $this->lang->line('slot'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('priority', $this->lang->line('priority'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('appointment_status', $this->lang->line('appointment_status'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('discount_percentage', $this->lang->line('discount_percentage'), 'trim|required|xss_clean');

        if ($this->input->post("payment_mode") == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required');
            $this->form_validation->set_rules('document', $this->lang->line("document"), 'callback_handle_doc_upload[document]');
        }

        $consult      = $this->input->post('live_consult');
        if ($consult == 'yes') {
            if (!$this->session->has_userdata('zoom_access_token')) {
                $this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'         => form_error('patient_id'),
                'doctor'             => form_error('doctorid'),
                'amount'             => form_error('amount'),
                'global_shift'       => form_error('global_shift'),
                'date'               => form_error('date'),
                'slot'               => form_error('slot'),
                'appointment_status' => form_error('appointment_status'),
                'cheque_no'          => form_error('cheque_no'),
                'cheque_date'        => form_error('cheque_date'),
                'document'           => form_error('document'),
                'priority'           => form_error('priority'),
                'discount_percentage'           => form_error('discount_percentage'),
                'zoom_live'           => form_error('zoom_live'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[appointment][" . $custom_fields_id . "]"] = form_error("custom_fields[appointment][" . $custom_fields_id . "]");
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
            $staff_id     = $this->customlib->getLoggedInUserID();
            $date         = $this->input->post('date');
            $date_appoint = $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format);
            $patient_id   = $this->input->post('patient_id');

            if ($consult == '') {
                $consult = 'no';
            }
            $cheque_date  = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date"));

            $doctor =   $this->input->post('doctorid');

            $getstaffdetails = $this->staff_model->getstaff($doctor);

            if ($getstaffdetails['specialist']) {
                $specialist =  $getstaffdetails['specialist'];
            } else {
                $specialist =  '';
            }

            $date   =   date("Y-M-d ", strtotime($date_appoint));
            $day    =   date("l", strtotime($date));

            $getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($doctor, $this->input->post('global_shift'), $day);

            $appointment = array(
                'patient_id'         => $patient_id,
                'date'               => $date_appoint,
                'priority'           => $this->input->post('priority'),
                'doctor'             => $doctor,
                'message'            => $this->input->post('message'),
                'doctor_shift_time_id'           => $getDoctorShiftTimeId->id,
                'is_queue'           => 0,
                'live_consult'       => $consult,
                'source'             => 'Offline',
                'appointment_status' => $this->input->post('appointment_status'),
                'specialist' => $specialist,
                'doctor_global_shift_id' => $this->input->post('global_shift'),
            );

            $insert_id = $this->appointment_model->add($appointment);
            $amount_paid = ($this->input->post('amount') - calculatePercent($this->input->post('amount'), $this->input->post('discount_percentage')));
            $payment_data = array(
                'appointment_id' => $insert_id,
                'standard_amount'    => $this->input->post('amount'),
                'paid_amount'    => $amount_paid,
                'charge_id'      => $this->input->post('charge_id'),
                'discount_percentage' => $this->input->post('discount_percentage'),
                'payment_type'   => 'Offline',
                'date'           => date("Y-m-d H:i:s"),
            );
            $payment_section   = $this->config->item('payment_section');
            $transaction_array = array(
                'amount'         => $amount_paid,
                'patient_id'     => $patient_id,
                'section'        => $payment_section['appointment'],
                'type'           => 'payment',
                'appointment_id' => $insert_id,
                'payment_mode'   => $this->input->post("payment_mode"),
                'payment_date'   => date('Y-m-d H:i:s'),
                'received_by'    => $staff_id,
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
                $transaction_array['cheque_date']     = $cheque_date;
                $transaction_array['cheque_no']       = $this->input->post('cheque_no');
                $transaction_array['attachment']      = $attachment;
                $transaction_array['attachment_name'] = $attachment_name;
            }

            $this->appointment_model->saveAppointmentPayment($payment_data, $transaction_array);

            /* OPD Insert Code*/
            $appointment_id      = $insert_id;
            $appointment_details = $this->appointment_model->getDetails($appointment_id);
            $transaction_data    = $this->transaction_model->getTransactionByAppointmentId($appointment_id);
            $appointment_payment = $this->appointment_model->getPaymentByAppointmentId($appointment_id);
            $charges             = $this->charge_model->getChargeByChargeId($appointment_payment->charge_id);
            $apply_charge        = $charges['standard_charge'] + ($charges['standard_charge'] * ($charges['percentage'] / 100));
            $opd_details         = array(
                'patient_id'   => $appointment_details['patient_id'],
                'generated_by' => $this->customlib->getStaffID(),
            );
            $visit_details = array(
                'appointment_date'  => $appointment_details['date'],
                'opd_details_id'    => 0,
                'cons_doctor'       => $appointment_details['doctor'],
                'generated_by'      => $this->customlib->getLoggedInUserID(),
                'patient_charge_id' => null,
                'transaction_id'    => $transaction_data->id,
                'can_delete'        => 'no',
                'live_consult'      => $consult,
            );
            $staff_data = $this->staff_model->getStaffByID($appointment_details['doctor']);
            $staff_name = composeStaffName($staff_data);
            $charge     = array(
                'opd_id'          => 0,
                'date'            => date('Y-m-d H:i:s'),
                'charge_id'       => $appointment_payment->charge_id,
                'qty'             => 1,
                'apply_charge'    => $charges['standard_charge'],
                'standard_charge' => $charges['standard_charge'],
                'discount_percentage' => $appointment_payment->discount_percentage,
                'amount'          => $appointment_payment->paid_amount,
                'created_at'      => date('Y-m-d H:i:s'),
                'note'            => null,
                'tax'             => $charges['percentage'],
            );
            $opd_visit_details = $this->appointment_model->moveToOpd($opd_details, $visit_details, $charge, $appointment_id);

            /* OPD Insert Code*/
            $visit_detail = $this->patient_model->getVisitDetailByid($opd_visit_details['visit_details_id']);
            $setting_result   = $this->setting_model->getzoomsetting();
            $opdduration      = $setting_result->opd_duration;

            if ($consult == 'yes') {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );

                $title = 'Online consult for ' . $this->customlib->getSessionPrefixByType('opd_no') . $visit_detail->opd_details_id . " Checkup ID " . $visit_detail->id;
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'         => $this->input->post('doctorid'),
                    'visit_details_id' => $visit_detail->id,
                    'title'            => $title,
                    'date'             => $date_appoint,
                    'duration'         => $opdduration,
                    'created_id'       => $this->customlib->getStaffID(),
                    'password'         => random_string(),
                    'api_type'         => $api_type,
                    'host_video'       => 1,
                    'client_video'     => 1,
                    'purpose'          => 'consult',
                    'timezone'         => $this->customlib->getTimeZone(),
                    'patient_id'         => $patient_id,
                );

                $response = $this->zoom_api->createAMeeting($insert_array);

                if ($response['status']) {

                    if (isset($response['data'])) {
                        $insert_array['return_response'] = json_encode($response['data']);
                        $this->conference_model->add($insert_array);
                    }
                }
            }

            $custom_field_post  = $this->input->post("custom_fields[appointment]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[appointment][" . $key . "]");
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

            $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('doctorid'));
            $event_data     = array(
                'appointment_date' => $this->customlib->YYYYMMDDHisTodateFormat($date_appoint, $this->customlib->getHospitalTimeFormat()),
                'patient_id'       => $patient_id,
                'doctor_id'        => $this->input->post('doctorid'),
                'doctor_name'      => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                'message'          => $this->input->post('message'),
                'appointment_status' =>  $this->input->post('appointment_status'),
            );

            $sender_details = array('patient_id' => $appointment_details["patient_id"], 'appointment_id' => $appointment_id);

            if ($this->input->post('appointment_status') == 'approved') {
                $this->mailsmsconf->mailsms('appointment_approved', $sender_details);
                $this->system_notification->send_system_notification('notification_appointment_created', $event_data);
                $this->system_notification->send_system_notification('appointment_approved', $event_data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'patient_id' => $appointment_details['patient_id'], 'appointment_id' => $appointment_id);
        }
        echo json_encode($array);
    }

    public function printAppointmentBill()
    {
        $print_details         = $this->printing_model->get('', 'appointment');
        $data["print_details"] = $print_details;
        $id     = $this->input->post("appointment_id");
        $result = $this->appointment_model->getDetailsAppointment($id);

        $result['appointment_no'] = $this->customlib->getSessionPrefixByType('appointment') . $id;

        if ($result['start_time']) {
            if ($this->time_format) {
                $result["doctor_shift_name"]       = date('G:i:s', strtotime($result['start_time'])) . " - " . date('G:i:s', strtotime($result['end_time']));
            } else {
                $result["doctor_shift_name"]       = date('g:i A', strtotime($result['start_time'])) . " - " . date('g:i A', strtotime($result['end_time']));
            }
        } else {
            $result["doctor_shift_name"]       = '';
        }

        $result["patients_name"]       = composePatientName($result['patients_name'], $result['patient_id']);
        $result["edit_live_consult"]   = $this->lang->line($result['live_consult']);
        $result["live_consult"]        = $result['live_consult'];
        $result["date"]                = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('appointment', $id);
        $cutom_fields_data             = get_custom_table_values($id, 'appointment');
        $data['field_data']          = $cutom_fields_data;
        $result['patients_gender']     = $result['patients_gender'];
        $result['transaction_id']      = $this->customlib->getSessionPrefixByType('transaction_id') . $result['transaction_id'];
        $data['appointment_id']        = $id;
        $data['fields']                = $this->customfield_model->get_custom_fields('appointment');
        $data['result']                = $result;
        $page = $this->load->view('patient/printAppointment', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }
	
    /*
    This Function is Used to Update Records
     */
    public function update()
    {
        $custom_fields = $this->customfield_model->getByBelong('appointment');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];

                    $this->form_validation->set_rules("custom_fields[appointment][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
                }
            }
        }
        $this->form_validation->set_rules('date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('doctor', $this->lang->line('doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('doctor_fees'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('message', $this->lang->line('message'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('global_shift', $this->lang->line('shift'), 'trim|required');
        $this->form_validation->set_rules('slot', $this->lang->line('slot'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'         => form_error('patient_id'),
                'doctor'             => form_error('doctorid'),
                'amount'             => form_error('amount'),
                'global_shift'       => form_error('global_shift'),
                'date'               => form_error('date'),
                'slot'               => form_error('slot'),
                'message'            => form_error('message'),
                'appointment_status' => form_error('appointment_status'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[appointment][" . $custom_fields_id . "]"] = form_error("custom_fields[appointment][" . $custom_fields_id . "]");
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
            $id                  = $this->input->post('id');
            $appointment_details = $this->appointment_model->getDetails($id);
            $date                = $this->input->post('date');
            $custom_field_post   = $this->input->post("custom_fields[appointment]");
            $consult             = $this->input->post('live_consult');
            $appointment_payment = $this->appointment_model->getPaymentByAppointmentId($id);
            $charges             = $this->charge_model->getChargeByChargeId($appointment_payment->charge_id);
            $apply_charge        = $charges['standard_charge'] + ($charges['standard_charge'] * ($charges['percentage'] / 100));

            $appointment = array(
                'id'              => $id,
                'patient_id'      => $this->input->post('patient_id'),
                'date'            => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'priority'        => $this->input->post('priority'),
                'doctor'          => $this->input->post('doctor'),
                'message'         => $this->input->post('message'),
                'global_shift_id' => $this->input->post('global_shift'),
                'shift_id'        => $this->input->post('slot'),
                'is_queue'        => 0,
                'live_consult'    => $consult,
            );
            $payment_data = array(
                'appointment_id' => $id,
                'paid_amount'    => $this->input->post('amount'),
                'charge_id'      => $this->input->post('charge_id'),
                'payment_type'   => 'Offline',
                'date'           => date("Y-m-d H:i:s"),
            );
            $payment_section   = $this->config->item('payment_section');
            $transaction_array = array(
                'amount'         => $this->input->post("amount"),
                'patient_id'     => $this->input->post('patient_id'),
                'section'        => $payment_section['appointment'],
                'type'           => 'payment',
                'appointment_id' => $id,
                'payment_mode'   => "Offline",
                'payment_date'   => date('Y-m-d H:i:s'),
                'received_by'    => $this->customlib->getLoggedInUserID(),
            );
            $visit_data  = $this->patient_model->getVisitdataDetails($appointment_details['visit_details_id']);
            $opd_details = array(
                'id'           => $visit_data['opdid'],
                'patient_id'   => $appointment_details['patient_id'],
                'generated_by' => $this->customlib->getStaffID(),
            );
            $visit_details = array(
                'id'               => $appointment_details['visit_details_id'],
                'appointment_date' => date("Y-m-d H:i:s"),
                'opd_details_id'   => $visit_data['opdid'],
                'cons_doctor'      => $appointment_details['doctor'],
                'generated_by'     => $this->customlib->getLoggedInUserID(),
                'can_delete'       => 'no',
            );
            $staff_data = $this->staff_model->getStaffByID($appointment_details['doctor']);
            $staff_name = composeStaffName($staff_data);
            $charge     = array(
                'date'            => date('Y-m-d'),
                'charge_id'       => $appointment_payment->charge_id,
                'qty'             => 1,
                'apply_charge'    => $apply_charge,
                'standard_charge' => $charges['standard_charge'],
                'amount'          => $appointment_payment->paid_amount,
                'created_at'      => date('Y-m-d H:i:s'),
                'note'            => $staff_name,
                'tax'             => $charges['percentage'],
            );

            $this->appointment_model->updateAppointment($appointment, $payment_data, $transaction_array, $opd_details, $visit_details, $charge);
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[appointment][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'appointment');
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function status($id)
    {
        $data = array('appointment_status' => 'approved');
        $this->appointment_model->status($id, $data);
        $appointment_details = $this->appointment_model->getDetails($id);
        $date_appoint        = $appointment_details['date'];

        $doctor_details = $this->notificationsetting_model->getstaffDetails($appointment_details["doctor"]);

        $event_data = array(
            'appointment_date'   => $this->customlib->YYYYMMDDHisTodateFormat($date_appoint, $this->time_format),
            'patient_id'         => $appointment_details["patient_id"],
            'doctor_id'          => $appointment_details["doctor"],
            'doctor_name'        => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
            'message'            => $appointment_details["message"],
            'appointment_status' => $this->lang->line($appointment_details["appointment_status"]),
        );

        $this->system_notification->send_system_notification('appointment_approved', $event_data);
        $sender_details = array('patient_id' => $appointment_details["patient_id"], 'appointment_id' => $id, 'contact_no' => $appointment_details["mobileno"], 'email' => $appointment_details["email"]);
        $this->mailsmsconf->mailsms('appointment_approved', $sender_details);
        redirect('admin/appointment/index');
    }

    public function search()
    {
        $this->session->set_userdata('top_menu', 'front_office');
        $app_data                      = $this->session->flashdata('app_data');
        $data['app_data']              = $app_data;
        $doctors                       = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]               = $doctors;
        $patients                      = $this->patient_model->getPatientListall();
        $data["patients"]              = $patients;
        $data["appointment_status"]    = $this->appointment_status;
        $userdata                      = $this->customlib->getUserData();
        $role_id                       = $userdata['role_id'];
        $data["yesno_condition"]       = $this->yesno_condition;
        $doctorid                      = "";
        $data['appoint_priority_list'] = $this->appoint_priority_model->appoint_priority_list();
        $doctor_restriction            = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option                = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }
        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $data['fields']         = $this->customfield_model->get_custom_fields('appointment', 1);
        $this->load->view('layout/header');
        $this->load->view('admin/appointment/search.php', $data);
        $this->load->view('layout/footer');
    }

    /*
    This Function is Used to get appointment records for datatable
     */
    public function getappointmentdatatable()
    {
        $dt_response = $this->appointment_model->getAllappointmentRecord();

        $fields      = $this->customfield_model->get_custom_fields('appointment', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $label = "";
                if ($value->appointment_status == "approved") {
                    $label  = "class='label label-success'";
                    $status = $this->customlib->getSessionPrefixByType('appointment') . $value->id;
                } else if ($value->appointment_status == "pending") {
                    $label  = "class='label label-warning'";
                    $status = $this->lang->line($value->appointment_status);
                } else if ($value->appointment_status == "cancel") {
                    $label  = "class='label label-danger'";
                    $status = $this->lang->line($value->appointment_status);
                }

                $action = "<div class='rowoptionview rowview-btn-top'>";
                $action .= "<a href='#' data-toggle='tooltip' title='" . $this->lang->line('show') . "' class='btn btn-default btn-xs'   data-target='#viewModal' onclick='viewDetail(" . $value->id . ")'>  <i class='fa fa-reorder'></i> </a>";
                $action .= "<a href='#'  class='btn btn-default btn-xs' data-toggle='tooltip'  onclick='printAppointment(" . $value->id . ")' data-original-title=" . $this->lang->line('print') . "><i class='fa fa-print'></i></a>";
                if ($this->rbac->hasPrivilege('reschedule', 'can_view')) {
                    $action .= " <a href='#' data-toggle='tooltip' title='" . $this->lang->line('reschedule') . "' class='btn btn-default btn-xs'   data-target='#rescheduleModal' onclick='viewreschedule(" . $value->id . ",1)'>  <i class='fa fa-calendar'></i> </a>";
                }
                if ($value->appointment_status == 'pending') {
                    if ($value->source != 'Online') {
                        if ($this->rbac->hasPrivilege('appointment_approve', 'can_view')) {
                            $action .= "<span class='large-tooltip'><a href='#' class='btn btn-default btn-xs'  data-toggle='tooltip' title='' data-target='#rescheduleModal' onclick='viewreschedule(" . $value->id . ",2)' data-original-title='" . $this->lang->line('approve_appointment') . "'><i class='fa fa-check' aria-hidden='true'></i></a></span>";
                        }
                    }
                }

                $action .= "</div>";
                $first_action = "<a  href='" . base_url() . 'admin/patient/profile/' . $value->pid . "'  title=''>";
                $appoint_no = "<a  href='" . base_url() . 'admin/patient/profile/' . $value->pid . "'  title=''>" . $status . "</a>";
				
                if (!empty($value->live_consult)) {
                    $live_consult = $this->lang->line(strtolower($value->live_consult));
                } else {
                    $live_consult = '';
                };

                if ($value->gender) {
                    $gender = $this->lang->line(strtolower($value->gender));
                } else {
                    $gender = '';
                }

                //==============================
                $row[] = $first_action . composePatientName($value->patient_name, $value->pid) . "</a>" . $action;
                $row[] =  $appoint_no;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->mobileno;
                $row[] = $gender;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $this->lang->line(strtolower($value->source));
                $row[] = $value->priorityname;
                if ($this->module_lib->hasActive('live_consultation')) {
                    $row[] = $live_consult;
                }
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
                $row[]     = $value->discount_percentage;
                $row[]     = $value->paid_amount;
                $row[]     = "<small " . $label . ">" . $this->lang->line($value->appointment_status) . "</small>";
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
        $id             = $this->input->post("appointment_id");
        $result         = $this->appointment_model->getDetails($id);
        $result["date"] = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        echo json_encode($result);
    }

    public function getDetailsAppointment()
    {
        $id     = $this->input->get("appointment_id");
        $result = $this->appointment_model->getDetailsAppointment($id);

        $result['appointment_no'] = $this->customlib->getSessionPrefixByType('appointment') . $id;

        if ($result['start_time']) {
            if ($this->time_format) {
                $result["doctor_shift_name"]       = date('G:i:s', strtotime($result['start_time'])) . " - " . date('G:i:s', strtotime($result['end_time']));
            } else {
                $result["doctor_shift_name"]       = date('g:i A', strtotime($result['start_time'])) . " - " . date('g:i A', strtotime($result['end_time']));
            }
        } else {
            $result["doctor_shift_name"]       = '';
        }

        $result["patients_name"]       = composePatientName($result['patients_name'], $result['patient_id']);
        $result["edit_live_consult"]   = $this->lang->line($result['live_consult']);
        $result["live_consult"]        = $result['live_consult'];
        $result["date"]                = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('appointment', $id);
        $cutom_fields_data             = get_custom_table_values($id, 'appointment');
        $result['field_data']          = $cutom_fields_data;
        $result['patients_gender']     = $this->lang->line(strtolower($result['patients_gender']));
        $result['amount']              = amountFormat($result['paid_amount']);
        $result['discount_percentage']              = $result['discount_percentage'];

        if ($result['payment_mode']) {
            $result['payment_mode']        = $this->lang->line(strtolower($result['payment_mode']));
        } else {
            $result['payment_mode']        = '';
        }

        $result['cheque_no']           = $result['cheque_no'];
        if ($result['cheque_date']) {
            $result['cheque_date']         = $this->customlib->YYYYMMDDTodateFormat($result['cheque_date']);
        } else {
            $result['cheque_date']         =  '';
        }

        $result['attachment']          = $result['attachment'];
        $transaction_id                = $result['transaction_id'];
        if ($result['transaction_id'] != "") {
            $result['transaction_id']      =  $this->customlib->getSessionPrefixByType('transaction_id') . $result['transaction_id'];
        } else {
            $result['transaction_id'] = "";
        }

        $result['department_name']     = $result['department_name'];
        $result['age']                 = $result['age'];
        $result['day']                 = $result['day'];
        $result['month']               = $result['month'];

        if ($result['appointment_status']) {
            $result['appointmentstatus']  = $this->lang->line(strtolower($result['appointment_status']));
        } else {
            $result['appointmentstatus']  =  '';
        }

        if ($result['source']) {
            $result['source']  = $this->lang->line(strtolower($result['source']));
        } else {
            $result['source']  = '';
        }

        $result['patient_age']         = $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']);

        if ($result['attachment'] != "") {
            $result["doc"] = "<a href='" . site_url('admin/transaction/download/') . $transaction_id . "' class='btn btn-default btn-xs'  title=" . $this->lang->line('download') . "><i class='fa fa-download'></i></a>";
        } else {
            $result["doc"] = "";
        }

        $staff_data = $this->staff_model->getstaff($result['received_by']);
        $staff_name = $staff_data["name"] . " " . $staff_data["surname"] . " (" . $staff_data["employee_id"] . ")";
        $result['received_by']  = $staff_name;
        echo json_encode($result);
    }

    public function getappDetails($id)
    {
        $result         = $this->appointment_model->getDetails($id);
        $result["date"] = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        echo json_encode($result);
    }

    /*
This Function is Used to Delete created Appointment patient
 */
    public function delete($id)
    {
        if (!empty($id)) {
            $appointment_details = $this->appointment_model->getDetails($id);
            $visit_details_id    = $appointment_details['visit_details_id'];
            $visit_data          = $this->patient_model->getvisitDetailsByVisitId($visit_details_id);
            $opd_id              = $visit_data['opdid'];

            $this->appointment_model->delete($id, $visit_details_id, $opd_id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }
    /*
This Function is Used to move patient from appointment to other module

 */
    public function move($id)
    {
        $appointment_details = $this->appointment_model->getDetails($id);
        $patient_name        = $appointment_details['patient_name'];
        $gender              = $appointment_details['gender'];
        $email               = $appointment_details['email'];
        $phone               = $appointment_details['mobileno'];
        $doctor              = $appointment_details['doctor'];
        $note                = $appointment_details['message'];
        $appointment_date    = $appointment_details['date'];
        $amount              = $appointment_details['amount'];
        $live_consult        = $appointment_details['live_consult'];

        $check_patient_id = $this->patient_model->getMaxId();
        if (empty($check_patient_id)) {
            $check_patient_id = 1000;
        }
        $patient_id   = $check_patient_id + 1;
        $patient_data = array(
            'patient_name'      => $patient_name,
            'mobileno'          => $phone,
            'email'             => $email,
            'gender'            => $gender,
            'patient_unique_id' => $patient_id,
            'note'              => $note,
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
        if (isset($insert_id)) {
            $check_opd_id = $this->patient_model->getMaxOPDId();
            $opdnoid      = $check_opd_id + 1;
            $opd_data = array(
                'appointment_date' => $appointment_date,
                'opd_no'           => 'OPDN' . $opdnoid,
                'cons_doctor'      => $doctor,
                'patient_id'       => $insert_id,
                'amount'           => $amount,
                'live_consult'     => $live_consult,
            );
            $opd_id = $this->patient_model->add_opd($opd_data);

            if (isset($opd_id)) {
                $this->appointment_model->delete($id);
            }
        }

        redirect('admin/appointment/search');
    }

    public function moveipd()
    {
        $custom_fields = $this->customfield_model->getByBelong('ipd');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[ipd][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $this->form_validation->set_rules('bed_no', $this->lang->line('bed_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'appointment_date'  => form_error('appointment_date'),
                'bed_no'            => form_error('bed_no'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'opd_id'            => form_error('opd_id'),

            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                            = $custom_fields_value['id'];
                        $custom_fields_name                                          = $custom_fields_value['name'];
                        $error_msg2["custom_fields[ipd][" . $custom_fields_id . "]"] = form_error("custom_fields[ipd][" . $custom_fields_id . "]");
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

            $appointment_id      = $this->input->post('appointment_id');
            $appointment_details = $this->appointment_model->getDetails($appointment_id);
            $ipd_details         = array(
                'patient_id'      => $appointment_details['patient_id'],
                'bed'             => $this->input->post('bed_no'),
                'bed_group_id'    => $this->input->post('bed_group_id'),
                'height'          => $this->input->post('height'),
                'weight'          => $this->input->post('weight'),
                'pulse'           => $this->input->post('pulse'),
                'temperature'     => $this->input->post('temperature'),
                'respiration'     => $this->input->post('respiration'),
                'bp'              => $this->input->post('bp'),
                'case_type'       => $this->input->post('case'),
                'casualty'        => $this->input->post('casualty'),
                'symptoms'        => $this->input->post('symptoms'),
                'known_allergies' => $this->input->post('symptoms'),
                'date'            => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('appointment_date'), $this->time_format),
                'refference'      => $this->input->post('refference'),
                'cons_doctor'     => $this->input->post('consultant_doctor'),
                'live_consult'    => $this->input->post('live_consult'),
                'discharged'      => 'no',
            );

            $bed_history = array(
                "bed_group_id" => $this->input->post("bed_group_id"),
                "bed_id"       => $this->input->post("bed_no"),
                "from_date"    => date("Y-m-d H:i:s"),
                "is_active"    => "yes",
            );

            $ipd_id = $this->appointment_model->moveToIpd($ipd_details, $bed_history, $appointment_id);
            if ($ipd_id) {
                $array = array('status' => 'success', 'message' => $this->lang->line('success_message'), 'ipd_id' => $ipd_id);
            } else {
                $msg   = array('no_insert' => $this->lang->line('something_went_wrong'));
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }
        }
        echo json_encode($array);
    }

    public function getpatientDetails()
    {
        $id     = $this->input->post("patient_id");
        $result = $this->appointment_model->getpatientDetails($id);
        echo json_encode($result);
    }

    public function checkvalidation()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->input->post('search_type') == 'period') {
            $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
        }

        if ($this->form_validation->run() == false) {
            if ($this->input->post('search_type') == 'period') {
                $msg1 = array(
                    'date_from' => form_error('date_from'),
                    'date_to' => form_error('date_to'),
                );
            }

            $msg2 = array(
                'search_type' => form_error('search_type'),
            );

            if ($this->input->post('search_type') == 'period') {
                $msg = array_merge($msg1, $msg2);
            } else {
                $msg = $msg2;
            }

            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'      => $this->input->post('search_type'),
                'collect_staff'    => $this->input->post('collect_staff'),
                'date_from'        => $this->input->post('date_from'),
                'date_to'          => $this->input->post('date_to'),
                'shift'            => $this->input->post('shift'),
                'priority'         => $this->input->post('priority'),
                'appointment_type' => $this->input->post('appointment_type'),
                'appointment_status' => $this->input->post('appointment_status'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function appointmentreport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/appointment');
        $this->session->set_userdata('subsub_menu', 'reports/appointment/appointmentreport');

        $doctorlist                    = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist']            = $doctorlist;
        $custom_fields                 = $this->customfield_model->get_custom_fields('appointment', '', '', 1);
        $data['fields']                = $custom_fields;
        $data['appoint_priority_list'] = $this->appoint_priority_model->appoint_priority_list();
        $data['appointment_type']      = $this->config->item('appointment_type');
        $data["searchlist"]            = $this->search_type;
        $data["appointment_status"]    = $this->appointment_status;

        $this->load->view('layout/header');
        $this->load->view('admin/appointment/appointmentReport', $data);
        $this->load->view('layout/footer');
    }

    public function appointmentreports()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['collect_staff'] = $this->input->post('collect_staff');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $shift                   = $this->input->post('shift');
        $priority                = $this->input->post('priority');
        $appointment_type        = $this->input->post('appointment_type');
        $appointment_status      = $this->input->post('appointment_status');
        $start_date              = '';
        $end_date                = '';
        $fields                  = $this->customfield_model->get_custom_fields('appointment', '', '', 1);
        if ($search['search_type'] == 'period') {

            $start_date = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);
        } else {

            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }

            $start_date = $dates['from_date'];
            $end_date   = $dates['to_date'];
        }

        $reportdata  = $this->report_model->appointmentRecord($start_date, $end_date, $search['collect_staff'], $shift, $priority, $appointment_type, $appointment_status);
        $reportdata  = json_decode($reportdata);
        $dt_data     = array();
        $paid_amount = 0;
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $paid_amount += $value->paid_amount;

                if ($value->appointment_status == "approved") {
                    $label = "class='label label-success'";
                } else if ($value->appointment_status == "pending") {
                    $label = "class='label label-warning'";
                } else if ($value->appointment_status == "cancel") {
                    $label = "class='label label-danger'";
                } else {
                    $label = "class=' '";
                }
                $row = array();

                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->mobileno;
                $row[] = $this->lang->line(strtolower($value->gender));
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $this->lang->line(strtolower($value->source));
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
                $row[]     = $value->discount_percentage;
                if ($value->paid_amount) {
                    $row[]     = $value->paid_amount;
                } else {
                    $row[]     = '0.00';
                }

                $row[]     = "<small " . $label . " >" . $this->lang->line($value->appointment_status) . "</small>";
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
            $footer_row[] = "<b>" . $currency_symbol . (number_format($paid_amount, 2, '.', '')) . "<br/>";
            $footer_row[] = "";
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

    public function getDoctorFees()
    {
        $doctor_id      = $this->input->post("doctor_id");
        $shift_details  = $this->onlineappointment_model->getShiftDetails($doctor_id);

        if (!empty($shift_details)) {
            $charge_details = $this->charge_model->getChargeDetailsById($shift_details['charge_id']);
            echo json_encode(
                array(
                    'status' => 1,
                    "fees"      => isset($charge_details->standard_charge) ? amountFormat($charge_details->standard_charge + ($charge_details->standard_charge * $charge_details->percentage / 100)) : "",
                    "charge_id" => $shift_details['charge_id']
                )
            );
        } else {
            echo  json_encode(['status' => 0]);
        }
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

    public function reschedule()
    {
        $custom_fields = $this->customfield_model->getByBelong('appointment');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];
                    $this->form_validation->set_rules("custom_fields[appointment][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
                }
            }
        }
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('rglobal_shift', $this->lang->line('shift'), 'trim|required');
        $this->form_validation->set_rules('rslot', $this->lang->line('slot'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('edit_appointment_status', $this->lang->line('status'), 'trim|required|xss_clean');

        $consult      = $this->input->post('live_consult');
        if ($consult == 'yes') {
            if (!$this->session->has_userdata('zoom_access_token')) {
                $this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'appointment_date' => form_error('appointment_date'),
                'rglobal_shift'    => form_error('rglobal_shift'),
                'rslot'            => form_error('rslot'),
                'edit_appointment_status'            => form_error('edit_appointment_status'),
                'zoom_live'            => form_error('zoom_live'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[appointment][" . $custom_fields_id . "]"] = form_error("custom_fields[appointment][" . $custom_fields_id . "]");
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
            $appointment_id = $this->input->post('appointment_id');
            $date   =  $this->input->post('appointment_date');
            $day          = date("l", strtotime($date));

            $getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($this->input->post('rdoctor_id'), $this->input->post('rglobal_shift'), $day);

            $appointment    = array(
                'id'              => $appointment_id,
                'date'            => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'priority'        => $this->input->post('priority'),
                'doctor_shift_time_id' => $this->input->post('rslot'),
                'message'         => $this->input->post('message'),
                'live_consult'    => $this->input->post('live_consult'),
                'appointment_status'    => $this->input->post('edit_appointment_status'),
                'doctor_global_shift_id'    => $this->input->post('rglobal_shift'),
            );

            $this->appointment_model->update($appointment);

            /*status code */
            $appointment_details = $this->appointment_model->getDetails($appointment_id);
            $transaction_data    = $this->transaction_model->getTransactionByAppointmentId($appointment_id);
            $appointment_payment = $this->appointment_model->getPaymentByAppointmentId($appointment_id);


          
                $charges             = $this->charge_model->getChargeByChargeId($appointment_payment->charge_id);
                $apply_charge        = $charges['standard_charge'] + ($charges['standard_charge'] * ($charges['percentage'] / 100));
                $opd_details         = array(
                    'patient_id'   => $appointment_details['patient_id'],
                    'generated_by' => $this->customlib->getStaffID(),
                );
                $consult      = $this->input->post('live_consult');
                $visit_details = array(
                    'appointment_date'  => $appointment_details['date'],
                    'opd_details_id'    => 0,
                    'cons_doctor'       => $appointment_details['doctor'],
                    'generated_by'      => $this->customlib->getLoggedInUserID(),
                    'patient_charge_id' => null,
                    'transaction_id'    => $transaction_data->id,
                    'can_delete'        => 'no',
                    'live_consult'      => $consult,
                );
                $staff_data = $this->staff_model->getStaffByID($appointment_details['doctor']);
                $staff_name = composeStaffName($staff_data);
                $charge     = array(
                    'opd_id'          => 0,
                    'date'            => date('Y-m-d H:i:s'),
                    'charge_id'       => $appointment_payment->charge_id,
                    'qty'             => 1,
                    'apply_charge'    => $charges['standard_charge'],
                    'standard_charge' => $charges['standard_charge'],
                    'amount'          => $appointment_payment->paid_amount,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'note'            => null,
                    'tax'             => $charges['percentage'],
                );

                $doctor_fees   =    $this->input->post('doctor_fees');
                
                $amount_paid = ($this->input->post('doctor_fees') - calculatePercent($this->input->post('doctor_fees'), $this->input->post('discount_percentage')));
                $appointment_fees=[             
                'standard_amount'    => $doctor_fees,
                'paid_amount'    => $amount_paid,
                'discount_percentage' => $this->input->post('discount_percentage')
                ];
                $this->appointment_model->updateappointmentpayment($appointment_id, $appointment_fees);

                if ($appointment_details['visit_details_id'] == '' && $this->input->post('live_consult') == "approved") {
                    $opd_visit_id = $this->appointment_model->moveToOpd($opd_details, $visit_details, $charge, $appointment_id);

                    /* OPD Insert Code*/

                    $visit_detail = $this->patient_model->getVisitDetailByid($opd_visit_id);
                    $setting_result   = $this->setting_model->getzoomsetting();
                    $opdduration      = $setting_result->opd_duration;
                    if ($consult == 'yes') {
                        $api_type = 'global';
                        $params   = array(
                            'zoom_api_key'    => "",
                            'zoom_api_secret' => "",
                        );

                        $title = 'Online consult for ' . $this->customlib->getSessionPrefixByType('opd_no') . $visit_detail->opd_details_id . " Checkup ID " . $visit_detail->id;
                        $this->load->library('zoom_api', $params);
                        $insert_array = array(
                            'staff_id'         => $appointment_details['doctor'],
                            'visit_details_id' => $visit_detail->id,
                            'title'            => $title,
                            'date'             => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('appointment_date'), $this->time_format),
                            'duration'         => $opdduration,
                            'created_id'       => $this->customlib->getStaffID(),
                            'password'         => random_string(),
                            'api_type'         => $api_type,
                            'host_video'       => 1,
                            'client_video'     => 1,
                            'purpose'          => 'consult',
                            'timezone'         => $this->customlib->getTimeZone(),
                        );

                        $response = $this->zoom_api->createAMeeting($insert_array);

                        if (!empty($response)) {
                            if (isset($response->id)) {
                                $insert_array['return_response'] = json_encode($response);
                                $this->conference_model->add($insert_array);
                            }
                        }
                    }
                }

            /* end status code */
            $custom_field_post = $this->input->post("custom_fields[appointment]");
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[appointment][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $appointment_id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $appointment_id, 'appointment');
            }

            $sender_details = array('patient_id' => $appointment_details["patient_id"], 'appointment_id' => $appointment_id);
            $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('rdoctor_id'));
            $appointment_date = $this->input->post('appointment_date');
            $date             = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);

            $event_data     = array(
                'appointment_date' => $this->customlib->YYYYMMDDHisTodateFormat($date, $this->time_format),
                'patient_id'       => $appointment_details["patient_id"],
                'doctor_id'        => $this->input->post('rdoctor_id'),
                'doctor_name'      => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                'message'          => $this->input->post('message'),
                'appointment_status' =>  $this->input->post('edit_appointment_status'),
            );

            $sender_details = array('patient_id' => $appointment_details["patient_id"], 'appointment_id' => $appointment_id);

            if ($this->input->post('edit_appointment_status') == 'approved') {
                $this->mailsmsconf->mailsms('appointment_approved', $sender_details);
                $this->system_notification->send_system_notification('notification_appointment_created', $event_data);
                $this->system_notification->send_system_notification('appointment_approved', $event_data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }
}
