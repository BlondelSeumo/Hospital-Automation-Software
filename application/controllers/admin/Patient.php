<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class patient extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->config->load("image_valid");
        $this->config->load("mailsms");
        $this->notification            = $this->config->item('notification');
        $this->notificationurl         = $this->config->item('notification_url');
        $this->patient_notificationurl = $this->config->item('patient_notification_url'); 
        $this->load->library('Enc_lib');
        $this->load->library('encoding_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('CSVReader');
        $this->load->library('Customlib');
        $this->load->library('system_notification');
        $this->load->library('datatables');
        $this->marital_status  = $this->config->item('marital_status');
        $this->payment_mode    = $this->config->item('payment_mode');
        $this->yesno_condition = $this->config->item('yesno_condition');
        $this->search_type     = $this->config->item('search_type');
        $this->blood_group     = $this->config->item('bloodgroup');
        $this->load->model(array('conference_model', 'transaction_model', 'casereference_model', 'patient_model', 'notificationsetting_model','antenatal_model','vital_model'));
        $this->load->model('finding_model');
        $this->charge_type          = $this->customlib->getChargeMaster();
        $data["charge_type"]        = $this->charge_type;
        $this->patient_login_prefix = "pat";
        $this->agerange             = $this->config->item('agerange');
        $this->load->helper('customfield_helper');
        $this->load->helper('custom');
        $this->opd_prefix          = $this->customlib->getSessionPrefixByType('opd_no');
        $this->blood_group         = $this->bloodbankstatus_model->get_product(null, 1);
        $this->time_format         = $this->customlib->getHospitalTimeFormat();
        $this->recent_record_count = 5;
        $this->sch_setting_detail  = $this->setting_model->getSetting();
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getCaseData()
    {
        $patient_id        = 0;
        $case_reference_id = $this->input->post('case_reference_id');
        $case_patient      = $this->casereference_model->getPatientByCase($case_reference_id);
        if (!empty($case_patient)) {
            $patient_id = $case_patient->patient_id;
        }
        echo json_encode(array('status' => 1, 'pateint_id' => $patient_id));
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_add')) {
            access_denied();
        }

        $patient_type = $this->customlib->getPatienttype();
        $custom_fields = $this->customfield_model->getByBelong('opd');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[opd][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('applied_charge'), 'trim|required|valid_amount|xss_clean');        
        $this->form_validation->set_rules('charge_id', $this->lang->line('charge'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paid_amount', $this->lang->line('paid_amount'), 'trim|required|valid_amount|xss_clean');

        $payment_mode = $this->input->post('payment_mode');
        if ($payment_mode == 'Cheque') {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        }
		
		$consult      = $this->input->post('live_consult');
		if($consult == 'yes'){
			if (!$this->session->has_userdata('zoom_access_token')) {
				$this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
			}
		}
		
        if ($this->form_validation->run() == false) {
            $msg = array(
                'appointment_date'  => form_error('appointment_date'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'patient_id'        => form_error('patient_id'),
                'amount'            => form_error('amount'),
                'charge_id'         => form_error('charge_id'),
                'paid_amount'       => form_error('paid_amount'),
                'cheque_no'         => form_error('cheque_no'),
                'cheque_date'       => form_error('cheque_date'),
                'document'          => form_error('document'),
                'zoom_live'         => form_error('zoom_live'),
            );
			
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                            = $custom_fields_value['id'];
                        $custom_fields_name                                          = $custom_fields_value['name'];
                        $error_msg2["custom_fields[opd][" . $custom_fields_id . "]"] = form_error("custom_fields[opd][" . $custom_fields_id . "]");
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
            $live_consult     = $this->input->post('live_consult');
            if($live_consult == ''){
                $live_consult     = 'no';
            }            
            
            $payment_section = $this->config->item('payment_section');
            $doctor_id        = $this->input->post('consultant_doctor');
            $patient_id       = $this->input->post('patient_id');
            $password         = $this->input->post('password');
            $email            = $this->input->post('email');
            $mobileno         = $this->input->post('mobileno');
            $patient_name     = $this->input->post('patient_name');
            $appointment_date = $this->input->post('appointment_date');
            $isopd            = $this->input->post('is_opd');
            $appointmentid    = $this->input->post('appointment_id');            
            $date             = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);
            $opd_data         = array(
                'patient_id'   => $patient_id,
                'generated_by' => $this->customlib->getStaffID(),
            );
            $custom_field_post  = $this->input->post("custom_fields[opd]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[opd][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            $this->opd_prefix = $this->customlib->getSessionPrefixByType('opd_no');

            $transaction_data = array(
                'case_reference_id' => 0,
                'opd_id'            => 0,
                'patient_id'        => $this->input->post('patient_id'),
                'amount'            => $this->input->post('paid_amount'),
                'type'              => 'payment',
                'section'           => $payment_section['opd'],
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note'),
                'payment_date'      => $date,
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
			
            $cheque_date = $this->input->post("cheque_date");
            if ($this->input->post('payment_mode') == "Cheque") {
                $transaction_data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $transaction_data['cheque_no']       = $this->input->post('cheque_no');
                $transaction_data['attachment']      = $attachment;
                $transaction_data['attachment_name'] = $attachment_name;
            }

            $staff_data = $this->staff_model->getStaffByID($doctor_id);
            $staff_name = composeStaffName($staff_data);
			
			if (empty($this->input->post('organisation_id'))) {
                $organisation_id = null;
            }else{
				$organisation_id = $this->input->post('organisation_id');
			}
			
            $charge     = array(
                'opd_id'               => 0,
                'date'                 => $date,
                'charge_id'            => $this->input->post('charge_id'),
                'qty'                  => 1,
                'apply_charge'         => $this->input->post('amount'),
                'standard_charge'      => $this->input->post('standard_charge'),
                'tpa_charge'           => $this->input->post('org_charge_amount'),
                'amount'               => $this->input->post('apply_amount'),
                'discount_percentage'  => $this->input->post('discount_percentage'),
                'created_at'           => date('Y-m-d'),
                'note'                 => '',
                'tax'                  => $this->input->post('percentage'),
                'organisation_id'      => $organisation_id,
                'insurance_id'         => $this->input->post('insurance_id'),
                'insurance_validity'   => $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity'))
            ); 
			
            $opd_visit_data = array(
                'appointment_date'  => $date,
                'opd_details_id'    => 0,                
                'symptoms'          => $this->input->post('symptoms'),
                'refference'        => $this->input->post('refference'),
                'cons_doctor'       => $this->input->post('consultant_doctor'),
                'casualty'          => $this->input->post('casualty'),
                'case_type'         => $this->input->post('case'),
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note'),
                'live_consult'      => $live_consult,
                'generated_by'      => $this->customlib->getLoggedInUserID(),
                'patient_charge_id' => null,
                'transaction_id'    => null,
                'can_delete'        => 'no',
                'known_allergies'   => $this->input->post('known_allergies'),                
            );

            if($this->input->post('is_for_antenatal')!=""){
                $opd_visit_data['is_antenatal'] = $this->input->post('is_for_antenatal');
            }

            $patient_data = array('is_antenatal' => $this->input->post('is_for_antenatal'),'id'=> $patient_id);
            $this->patient_model->add($patient_data);       
 
            if ($this->input->post('symptoms_type') != "") {
                $opd_visit_data['symptoms_type'] = $this->input->post('symptoms_type');
            }
            $opdn_id          = $this->patient_model->add_opd($opd_data, $transaction_data, $charge, $opd_visit_data);
            $visit_details_id = $this->patient_model->getvisitminid($opdn_id);
            $notificationurl  = $this->notificationurl;
            $url_link         = $notificationurl["opd"];
            $setting_result   = $this->setting_model->getzoomsetting();
            $opdduration      = $setting_result->opd_duration;

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $opdn_id);
            }
            if ($live_consult == 'yes') {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );

                $title = 'Online consult for ' . $this->customlib->getSessionPrefixByType('opd_no') . $opdn_id . " Checkup ID " . $this->customlib->getSessionPrefixByType('checkup_id') . $visit_details_id['visitid'];				

                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'         => $doctor_id,
                    'patient_id'         => $patient_id,
                    'visit_details_id' => $visit_details_id['visitid'],
                    'title'            => $title,
                    'date'             => $date,
                    'duration'         => $opdduration,
                    'created_id'       => $this->customlib->getStaffID(),
                    'password'         => $password,
                    'api_type'         => $api_type,
                    'host_video'       => 1,
                    'client_video'     => 1,
                    'purpose'          => 'consult',
                    'timezone'         => $this->customlib->getTimeZone(),				 
                );

                $response = $this->zoom_api->createAMeeting($insert_array);

                if ($response['status']) {
					 
                    if (isset($response['data'])) {
                        $insert_array['return_response'] = json_encode($response['data']);
                        $conferenceid                    = $this->conference_model->add($insert_array);

                        $sender_details = array('patient_id' => $patient_id, 'conference_id' => $conferenceid, 'contact_no' => $mobileno, 'email' => $email);
                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
                    }
                }
            }

            $url   = base_url() . $url_link . '/' . $patient_id . '/' . $opdn_id;
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'id' => $patient_id, 'opd_id' => $opdn_id);

            if ($this->session->has_userdata("appointment_id")) {
                $appointment_id = $this->session->userdata("appointment_id");
                $updateData     = array('id' => $appointment_id, 'is_opd' => 'yes');
                $this->appointment_model->update($updateData);
                $this->session->unset_userdata('appointment_id');
            }

            $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('consultant_doctor'));
            $event_data     = array(
                'patient_id'           => $patient_id,
                'symptoms_description' => $this->input->post('symptoms'),
                'any_known_allergies'  => $this->input->post('known_allergies'),
                'appointment_date'     => $this->customlib->YYYYMMDDHisTodateFormat($date, $this->time_format),
                'doctor_id'            => $this->input->post('consultant_doctor'),
                'doctor_name'          => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
            );

            $this->system_notification->send_system_notification('opd_visit_created', $event_data);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $patient_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $patient_id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }

            $sender_details = array('patient_id' => $patient_id, 'patient_name' => $patient_name, 'opd_details_id' => $opdn_id, 'contact_no' => $mobileno, 'email' => $email, 'appointment_date' => $appointment_date);
            $result         = $this->mailsmsconf->mailsms('opd_patient_registration', $sender_details);
        }
        echo json_encode($array);
    }

    public function getPatientType()
    {
        $opd_ipd_patient_type = $this->input->post('opd_ipd_patient_type');
        $opd_ipd_no           = $this->input->post('opd_ipd_no');
        if ($opd_ipd_patient_type == 'opd') {
            if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
                access_denied();
            }
            $result = $this->patient_model->getOpdPatient($opd_ipd_no);
        } elseif ($opd_ipd_patient_type == 'ipd') {
            if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
                access_denied();
            }
            $result = $this->patient_model->getIpdPatient($opd_ipd_no);
        }
        echo json_encode($result);
    }

    public function addmedicationdose()
    {
        if (!$this->rbac->hasPrivilege('ipd_medication', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name_id', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('dosage', $this->lang->line('dosage'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'medicine_category_id' => form_error('medicine_category_id'),
                'medicine_name_id'     => form_error('medicine_name_id'),
                'dosage'               => form_error('dosage'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $ipd_id      = $this->input->post("ipdid");
            $date        = $this->customlib->dateFormatToYYYYMMDD($this->input->post("date"));
            $time        = $this->input->post('time');
            $timeformat  = date("H:i:s", strtotime($time));
            $pharmacy_id = $this->input->post('medicine_name_id');
            $chekrecord  = $this->patient_model->checkmedicationdose($ipd_id, $pharmacy_id, $date, $timeformat);
            $data = array(
                'date'               => $date,
                'medicine_dosage_id' => $this->input->post('dosage'),
                'time'               => $timeformat,
                'pharmacy_id'        => $pharmacy_id,
                'ipd_id'             => $ipd_id,
                'remark'             => $this->input->post('remark'),
                'generated_by'       => $this->customlib->getLoggedInUserID(),
            );
            if ($chekrecord) {
                $msg = $this->lang->line('record_already_exists');
                $sts = 'fail';
            } else {
                $this->patient_model->addmedication($data);
                $sts = 'success';
                $msg = $this->lang->line('record_saved_successfully');
            }
            $patient_data      = $this->patient_model->get_patientidbyIpdId($this->input->post('ipdid'));
            $medicine_data     = $this->notificationsetting_model->getmedicineDetails($pharmacy_id);
            $medicinedose_data = $this->notificationsetting_model->getmedicinedoseDetails($this->input->post('dosage'));
            $doctor_list       = $this->patient_model->getDoctorsipd($ipd_id);
            $consultant_doctor = $this->patient_model->get_patientidbyIpdId($ipd_id);
            $doctor_details    = $this->notificationsetting_model->getstaffDetails($patient_data['cons_doctor']);

            $consultant_doctorarray[] = array('consult_doctor' => $patient_data['cons_doctor'], 'name' => $doctor_details['name'] . " " . $doctor_details['surname'] . "(" . $doctor_details['employee_id'] . ")");

            $consultant_doctorarray[] = array('consult_doctor' => $consultant_doctor['cons_doctor'], 'name' => $consultant_doctor['doctor_name'] . " " . $consultant_doctor['doctor_surname'] . "(" . $consultant_doctor['doctor_employee_id'] . ")");
            foreach ($doctor_list as $key => $value) {
                $consultant_doctorarray[] = array('consult_doctor' => $value['consult_doctor'], 'name' => $value['ipd_doctorname'] . " " . $value['ipd_doctorsurname'] . "(" . $value['employee_id'] . ")");
            }

            $event_data = array(
                'patient_id'        => $patient_data['patient_id'],
                'ipd_no'            => $this->customlib->getSessionPrefixByType('ipd_no') . $this->input->post('ipdid'),
                'case_id'           => $patient_data['case_reference_id'],
                'date'              => $this->customlib->YYYYMMDDTodateFormat($date),
                'time'              => $this->customlib->getHospitalTime_Format($time),
                'medicine_category' => $medicinedose_data['medicine_category'],
                'medicine_name'     => $medicine_data['medicine_name'],
                'dosage'            => $medicinedose_data['dosage'] . " " . $medicinedose_data['unit'],
                'doctor_name'       => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
            );

            $this->system_notification->send_system_notification('add_ipd_medication_dose', $event_data, $consultant_doctorarray);
            $array = array('status' => $sts, 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function addmedicationdoseopd()
    {
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('time', $this->lang->line('time'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name_id', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('dosage', $this->lang->line('dosage'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'time'                 => form_error('time'),
                'medicine_category_id' => form_error('medicine_category_id'),
                'medicine_name_id'     => form_error('medicine_name_id'),
                'dosage'               => form_error('dosage'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $opd_id      = $this->input->post("opdid");
            $date        = $this->customlib->dateFormatToYYYYMMDD($this->input->post("date"));
            $time        = $this->input->post('time');
            $timeformat  = date("H:i:s", strtotime($time));
            $pharmacy_id = $this->input->post('medicine_name_id');
            $chekrecord  = $this->patient_model->checkmedicationdoseopd($opd_id, $pharmacy_id, $date, $timeformat);

            $data = array(
                'date'               => $date,
                'medicine_dosage_id' => $this->input->post('dosage'),
                'time'               => $timeformat,
                'pharmacy_id'        => $pharmacy_id,
                'opd_details_id'     => $opd_id,
                'remark'             => $this->input->post('remark'),
                'generated_by'       => $this->customlib->getLoggedInUserID(),
            );

            if ($chekrecord) {
                $msg = $this->lang->line('record_already_exists');
                $sts = 'fail';
            } else {
                $this->patient_model->addmedication($data);
                $sts = 'success';
                $msg = $this->lang->line('record_saved_successfully');
            }
            $patient_data      = $this->patient_model->get_patientidbyopdid($this->input->post('opdid'));
            $medicine_data     = $this->notificationsetting_model->getmedicineDetails($pharmacy_id);
            $medicinedose_data = $this->notificationsetting_model->getmedicinedoseDetails($this->input->post('dosage'));
            $doctor_details    = $this->notificationsetting_model->getstaffDetails($patient_data['doctor_id']);
            $event_data        = array(
                'patient_id'        => $patient_data['patient_id'],
                'opd_no'            => $this->customlib->getSessionPrefixByType('opd_no') . $this->input->post('opdid'),
                'case_id'           => $patient_data['case_reference_id'],
                'date'              => $this->customlib->YYYYMMDDTodateFormat($date),
                'time'              => $this->customlib->getHospitalTime_Format($time),
                'medicine_category' => $medicinedose_data['medicine_category'],
                'medicine_name'     => $medicine_data['medicine_name'],
                'dosage'            => $medicinedose_data['dosage'] . " " . $medicinedose_data['unit'],
                'doctor_id'         => $patient_data['doctor_id'],
                'doctor_name'       => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
            );

            $this->system_notification->send_system_notification('add_opd_medication_dose', $event_data);
            $array = array('status' => $sts, 'error' => '', 'message' => $msg);
        }

        echo json_encode($array);
    }

    public function updatemedication()
    {
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('time', $this->lang->line('time'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name_id', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('dosage_id', $this->lang->line('dosage'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'time'                 => form_error('time'),
                'medicine_category_id' => form_error('medicine_category_id'),
                'medicine_name_id'     => form_error('medicine_name_id'),
                'dosage_id'            => form_error('dosage_id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $data = array(
                'id'                 => $this->input->post('medication_id'),
                'medicine_dosage_id' => $this->input->post('dosage_id'),
                'date'               => $this->customlib->dateFormatToYYYYMMDD($this->input->post("date")),
                'time'               => date("H:i:s", strtotime($this->input->post('time'))),
                'remark'             => $this->input->post('remark'),
                'pharmacy_id'        => $this->input->post('medicine_name_id'),
                'generated_by'       => $this->customlib->getLoggedInUserID(),
            );

            $this->patient_model->addmedication($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
        }

        echo json_encode($array);
    }

    public function getCharge()
    {
        $id                  = $this->input->post('id');
        $result              = $this->charge_model->getChargeById($id);       
		if($result->date){
			$result->date = $this->customlib->YYYYMMDDHisTodateFormat($result->date, $this->customlib->getHospitalTimeFormat());
        }else{
			$result->date = '';
		}
		 
        $array               = array('status' => 1, 'result' => $result, 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }

    public function add_revisit()
    {
        if (!$this->rbac->hasPrivilege('visit', 'can_add')) {
            access_denied();
        }

        $custom_fields = $this->customfield_model->getByBelong('opd');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[opd][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $payment_mode = $this->input->post('payment_mode');
		
		$consult      = $this->input->post('live_consult');
		if($consult == 'yes'){
			if (!$this->session->has_userdata('zoom_access_token')) {
				$this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
			}
		}
		
        if ($payment_mode == 'Cheque') {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        }

        $this->form_validation->set_rules('amount', $this->lang->line('applied_charge'), 'trim|required|xss_clean|valid_amount');
        $this->form_validation->set_rules('apply_amount', $this->lang->line('amount'), 'trim|required|xss_clean|valid_amount');
        $this->form_validation->set_rules('paid_amount', $this->lang->line('paid_amount'), 'trim|required|xss_clean|valid_amount');
        $this->form_validation->set_rules('charge_id', $this->lang->line('charge'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('visit_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'charge_id'         => form_error('charge_id'),
                'amount'            => form_error('amount'),
                'apply_amount'      => form_error('apply_amount'),
                'paid_amount'       => form_error('paid_amount'),
                'appointment_date'  => form_error('appointment_date'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'cheque_no'         => form_error('cheque_no'),
                'cheque_date'       => form_error('cheque_date'),
                'document'          => form_error('document'),
                'zoom_live'         => form_error('zoom_live'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                            = $custom_fields_value['id'];
                        $custom_fields_name                                          = $custom_fields_value['name'];
                        $error_msg2["custom_fields[opd][" . $custom_fields_id . "]"] = form_error("custom_fields[opd][" . $custom_fields_id . "]");
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
            $check_patient_id = $this->patient_model->getMaxOPDId();
            if (empty($check_patient_id)) {
                $check_patient_id = 0;
            }
            $patient_id        = $this->input->post('patientid');
            $password          = $this->input->post('password');
            $email             = $this->input->post('email');
            $mobileno          = $this->input->post('mobileno');
            $opdn_id           = $check_patient_id + 1;
            $custom_field_post = $this->input->post("custom_fields[opd]");
            $appointment_date  = $this->input->post('appointment_date');
            $consult           = $this->input->post('live_consult');
            if ($consult) {
                $live_consult = $this->input->post('live_consult');
            } else {
                $live_consult = 'no';
            }
            $doctor_id = $this->input->post("consultant_doctor");
            $date      = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);
            $opd_data  = array(
                'patient_id'   => $patient_id,
                'generated_by' => $this->customlib->getLoggedInUserID(),
            );

            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[opd][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
            $payment_section = $this->config->item('payment_section');

            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }

            $cheque_date = $this->input->post("cheque_date");

            $transaction_data = array(
                'case_reference_id' => 0,
                'opd_id'            => 0,
                'amount'            => $this->input->post('paid_amount'),
                'type'              => 'payment',
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note'),
                'payment_date'      => $date,
                'patient_id'        => $this->input->post('patientid'),
                'section'           => $payment_section['opd'],
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );

            if ($this->input->post('payment_mode') == "Cheque") {
                $transaction_data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $transaction_data['cheque_no']       = $this->input->post('cheque_no');
                $transaction_data['attachment']      = $attachment;
                $transaction_data['attachment_name'] = $attachment_name;
            }

            $staff_data = $this->staff_model->getStaffByID($doctor_id);
            $staff_name = composeStaffName($staff_data);

            $is_tpa = $this->input->post('is_tpa');
            if($is_tpa=="" || $is_tpa==0){
            $organisation_id = null;
            $insurance_validity = null;
            $insurance_id = null;
            }else{           
            $organisation_id = $this->input->post('organisation');
            $insurance_id    = $this->input->post('insurance_id');
            $insurance_validity = $this->input->post('insurance_validity');
            }
           
            if (empty($organisation_id)) {
                $organisation_id = null;
            }

            $charge     = array(
                'opd_id'          => 0,
                'date'            => $date,
                'charge_id'       => $this->input->post('charge_id'),
                'qty'             => 1,
                'apply_charge'    => $this->input->post('amount'),
                'standard_charge' => $this->input->post('standard_charge'),
                'tpa_charge'      => $this->input->post('schedule_charge'),
                'amount'          => $this->input->post('apply_amount'),
                'discount_percentage'=> $this->input->post('discount_percentage'),
                'created_at'      => date('Y-m-d'),
                'note'            => '',
                'tax'             => $this->input->post('percentage'),
                'organisation_id' => $organisation_id,
                'insurance_id'     => $insurance_id,
                'insurance_validity'  => $this->customlib->dateFormatToYYYYMMDD($insurance_validity),
            );
			
            $opd_visit_data = array(
                'appointment_date' => $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format),               
                'symptoms'         => $this->input->post('symptoms'),
                'known_allergies'  => $this->input->post('known_allergies'),
                'patient_old'      => $this->input->post('old_patient'),
                'refference'       => $this->input->post('refference'),
                'cons_doctor'      => $this->input->post('consultant_doctor'),
                'symptoms_type'    => $this->input->post('symptoms_type'),
                'casualty'         => $this->input->post('casualty'),
                'payment_mode'     => $this->input->post('payment_mode'),
                'note'             => $this->input->post('note_remark'),
                'live_consult'     => $live_consult,
                'can_delete'       => 'no',
                'generated_by'     => $this->customlib->getLoggedInUserID(),
            );
			
			$opd_visit_data['is_antenatal'] = 0;
           
            if($this->input->post('is_for_antenatal') != "" ){
                $opd_visit_data['is_antenatal'] = 1;
            }

            $opdn_id         = $this->patient_model->add_opd($opd_data, $transaction_data, $charge, $opd_visit_data);
			
            $patient_data = array('is_antenatal' => $this->input->post('is_for_antenatal'),'id'=> $patient_id);
            $this->patient_model->add($patient_data);
			
            $visit_max_id    = $this->patient_model->getvisitmaxid($opdn_id);
            $visitid         = $visit_max_id['visitid'];
            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["opd"];
            $url             = base_url() . $url_link . '/' . $patient_id . '/' . $opdn_id;
            $setting_result  = $this->setting_model->getzoomsetting();
            $opdduration     = $setting_result->opd_duration;
            if ($live_consult == "yes") {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'         => $doctor_id,
                    'visit_details_id' => $visitid,                   
                    'title'            => 'Online consult for Revisit OPDN' . $opdn_id,
                    'date'             => $date,
                    'duration'         => $opdduration,
                    'created_id'       => $this->customlib->getStaffID(),
                    'password'         => $password,
                    'api_type'         => $api_type,
                    'host_video'       => 1,
                    'client_video'     => 1,
                    'purpose'          => 'consult',
                    'timezone'         => $this->customlib->getTimeZone(),
                );
                $response         = $this->zoom_api->createAMeeting($insert_array);
                $appointment_date = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);
                 
				if ($response['status']) {
					 
                    if (isset($response['data'])) {
                        $insert_array['return_response'] = json_encode($response['data']);
						 
                        $conferenceid   = $this->conference_model->add($insert_array);
                        $sender_details = array('patient_id' => $patient_id, 'conference_id' => $conferenceid, 'contact_no' => $mobileno, 'email' => $email);
                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
						 
                    }
                }				
            }
            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $opdn_id);
            }

            $sender_details = array('patient_id' => $patient_id, 'opd_details_id' => $opdn_id, 'contact_no' => $mobileno, 'email' => $email, 'appointment_date' => $appointment_date);
            $this->mailsmsconf->mailsms('opd_patient_registration', $sender_details);
            $array = array('status' => 'success', 'error' => '', 'id' => $opdn_id, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getPatientId()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
        $result         = $this->patient_model->getPatientId();
        $data["result"] = $result;
        echo json_encode($result);
    }
	
/*
This Function is used to Get Symptoms Records
 */
    public function get_symptoms()
    {
        $result         = $this->symptoms_model->get();
        $data["result"] = $result;
        echo json_encode($result);
    }

/*
This Function is used to Get Doctor Charges
 */
    public function doctCharge()
    {
        if (!$this->rbac->hasPrivilege('doctor_charges', 'can_view')) {
            access_denied();
        }
        $doctor       = $this->input->post("doctor");
        $organisation = $this->input->post("organisation");
        $data         = $this->patient_model->doctortpaCharge($doctor, $organisation);

        echo json_encode($data);
    }

    public function bulk_delete()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('delete_id[]', 'delete_id', 'trim|required|xss_clean', array('required' => $this->lang->line('no_record_selected')));

        if ($this->form_validation->run() == false) {
            $msg = array(
                'delete_id' => form_error('delete_id[]'),
            );
            $return_array = array('status' => 0, 'error' => $msg);
        } else {            
            $patient = $this->input->post('delete_id');
            $this->patient_model->bulkdelete($patient);
            $return_array = array('status' => 1, 'error' => '', 'msg' => $this->lang->line('delete_message'));
        }

        echo json_encode($return_array);
    }

    public function doctortpaCharge()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }

        $doctor         = $this->input->post("doctor");
        $organisation   = $this->input->post("organisation");
        $result         = $this->patient_model->doctortpaCharge($doctor, $organisation);
        $data['result'] = $result;
        echo json_encode($result);
    }

    public function doctName()
    {
        $doctor = $this->input->post("doctor");
        $data   = $this->patient_model->doctName($doctor);
        echo json_encode($data);
    }

/*
This Function is used to Add Patient
 */
    public function addpatient()
    {
        $custom_fields = $this->customfield_model->getByBelong('patient');

        if ((int) $_POST['age']['day'] == 0 && (int) $_POST['age']['month'] == 0 && (int) $_POST['age']['year'] == 0) {
            $this->form_validation->set_rules('age', $this->lang->line('age'), 'trim|required|xss_clean|');
        }

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[patient][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        if($this->input->post('organisation_id') != ""){
            $this->form_validation->set_rules('insurance_id', $this->lang->line('tpa_id'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('validity', $this->lang->line('tpa_validity'), 'trim|required|xss_clean');
        }

        if($this->input->post('insurance_id') != ""){            
            $this->form_validation->set_rules('organisation_id', $this->lang->line('tpa'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('validity', $this->lang->line('tpa_validity'), 'trim|required|xss_clean');
        }

        if($this->input->post('validity') != ""){            
            $this->form_validation->set_rules('insurance_id', $this->lang->line('tpa_id'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('organisation_id', $this->lang->line('tpa'), 'trim|required|xss_clean');
        }

        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|xss_clean');
        $this->form_validation->set_rules('mobileno', $this->lang->line('phone'), 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('age[year]', $this->lang->line('year'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('age[month]', $this->lang->line('month'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('age[day]', $this->lang->line('day'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'name'       => form_error('name'),
                'age'        => form_error('age'),
                'age[year]'  => form_error('age[year]'),
                'age[month]' => form_error('age[month]'),
                'age[day]'   => form_error('age[day]'),
                'email'      => form_error('email'),
                'mobileno'   => form_error('mobileno'),
                'file'       => form_error('file'),
                'insurance_id'       => form_error('insurance_id'),
                'validity'           => form_error('validity'),
                'organisation_id'    => form_error('organisation_id'),
            );
        
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                = $custom_fields_value['id'];
                        $custom_fields_name                                              = $custom_fields_value['name'];
                        $error_msg2["custom_fields[patient][" . $custom_fields_id . "]"] = form_error("custom_fields[patient][" . $custom_fields_id . "]");
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

            $dobdate = $this->input->post('dob');
            if ($dobdate == "") {
                $dob = null;
            } else {
                $dob = $this->customlib->dateFormatToYYYYMMDD($dobdate);
            }

            $email    = $this->input->post('email');
            $mobileno = $this->input->post('mobileno');

            if (($mobileno != "") && ($email != "")) {
                $result = $this->patient_model->checkmobileemail($mobileno, $email);
                if ($result == 1) {
                    $msg   = array('numberemail' => $this->lang->line('mobile_number_and_email_already_exist'));
                    $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
                    echo json_encode($array);
                    die;
                }
            }

            if ($mobileno != "") {
                $result = $this->patient_model->checkmobilenumber($mobileno);
                if ($result == 1) {
                    $msg   = array('number' => $this->lang->line('mobile_number_already_exist'));
                    $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
                    echo json_encode($array);
                    die;
                }
            }

            if ($email != "") {
                $result = $this->patient_model->checkemail($email);
                if ($result == 1) {
                    $msg   = array('email' => $this->lang->line('email_already_exist'));
                    $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
                    echo json_encode($array);
                    die;
                }
            }

            $validity = $this->input->post("validity");
            if (!empty($validity)) {
                $validity = $this->customlib->dateFormatToYYYYMMDD($validity);
            } else {
                $validity = null;
            }
			
            $blood_bank_product_id = $this->input->post('blood_group');
            if (!empty($blood_bank_product_id)) {
                $blood_group = $blood_bank_product_id;
            } else {
                $blood_group = null;
            }
			
			$organisation_id = $this->input->post('organisation_id');
            if (!empty($organisation_id)) {
                $organisation_id = $organisation_id;
            } else {
                $organisation_id = null;
            }
			
            $patient_data = array(
                'patient_name'          => $this->input->post('name'),
                'mobileno'              => $this->input->post('mobileno'),
                'marital_status'        => $this->input->post('marital_status'),
                'email'                 => $this->input->post('email'),
                'gender'                => $this->input->post('gender'),
                'guardian_name'         => $this->input->post('guardian_name'),
                'blood_bank_product_id' => $blood_group,
                'address'               => $this->input->post('address'),
                'known_allergies'       => $this->input->post('known_allergies'),
                'insurance_id'          => $this->input->post('insurance_id'),
                'insurance_validity'    => $validity,
                'note'                  => $this->input->post('note'),
                'dob'                   => $dob,
                'age'                   => $this->input->post('age[year]'),
                'month'                 => $this->input->post('age[month]'),
                'day'                   => $this->input->post('age[day]'),                
                'as_of_date'           => date("Y-m-d"),
                'identification_number' => $this->input->post('identification_number'),
                'is_active'             => 'yes',
                'organisation_id'       => $organisation_id,               
            );

            $custom_field_post  = $this->input->post("custom_fields[patient]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[patient][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
            $insert_id = $this->patient_model->add_patient($patient_data);

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }
            if ($this->session->has_userdata("appointment_id")) {
                $appointment_id = $this->session->userdata("appointment_id");
                $updateData     = array('id' => $appointment_id, 'patient_id' => $insert_id);
                $this->appointment_model->update($updateData);
                $this->session->unset_userdata('appointment_id');
            }
            $user_password      = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
            $data_patient_login = array(
                'username' => $this->patient_login_prefix . $insert_id,
                'password' => $user_password,
                'user_id'  => $insert_id,
                'role'     => 'patient',
            );

            $this->user_model->add($data_patient_login);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'id' => $insert_id);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/' . $img_name);
            } else {
                $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/no_image.png');
            }
            $this->patient_model->add($data_img);
            $scan_type= $this->sch_setting_detail->scan_code_type;
            $this->customlib->generatebarcode($insert_id,$scan_type);//generate barcode and qrcode

            $sender_details = array('id' => $insert_id, 'credential_for' => 'patient', 'username' => $this->patient_login_prefix . $insert_id, 'password' => $user_password, 'contact_no' => $this->input->post('mobileno'), 'email' => $this->input->post('email'));

            $this->mailsmsconf->mailsms('login_credential', $sender_details);

        }
        echo json_encode($array);
    }

/*
This Function is used to File Validation For Image
 */
    public function handle_upload()
    {
        $image_validate = $this->config->item('image_validate');

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

/*
This Function is used to File Validation
 */
    public function handle_csv_upload()
    {
        $image_validate = $this->config->item('filecsv_validate');
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = filesize($_FILES['file']['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }
            return true;
        } else {
            $this->form_validation->set_message('handle_csv_upload', $this->lang->line('the_file_field_is_required'));
            return false;
        }
        return true;
    }

    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_patient_sample_file.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_patient_sample_file.csv';
        force_download($name, $data);
    }

/*
This Function is used to Import Multiple Patient Records
 */
    public function import()
    {
        if (!$this->rbac->hasPrivilege('patient_import', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'setup/patient');

        $fields         = array('patient_name', 'guardian_name', 'gender', 'age', 'month', 'day', 'marital_status', 'mobileno', 'email', 'address', 'note', 'known_allergies', 'identification_number', 'insurance_id', 'insurance_validity');
        $data["fields"] = $fields;
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_csv_upload');

        $data['blood_group'] = $this->blood_group;
        if ($this->form_validation->run() == false) {

            $this->load->view('layout/header');
            $this->load->view('admin/patient/import', $data);
            $this->load->view('layout/footer');
 
        } else { 

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext        = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $patient_bloodgroup = $this->input->post('blood_group');
				$scan_type= $this->sch_setting_detail->scan_code_type;
				
                if ($ext == 'csv') {
                    $file   = $_FILES['file']['tmp_name'];
                    $result = $this->csvreader->parse_file($file);

                    if (!empty($result)) {

                        $count = 0;
                        for ($i = 1; $i <= count($result); $i++) {

                            $patient_data[$i] = array();
                            $n                = 0;

                            foreach ($result[$i] as $key => $value) {
                                $patient_data[$i][$fields[$n]]             	= $this->encoding_lib->toUTF8($result[$i][$key]);
                                $patient_data[$i]['is_active']             	= 'yes';
                                $patient_data[$i]['blood_bank_product_id']  = $patient_bloodgroup;
                                $patient_data[$i]['image']                 	= 'uploads/patient_images/no_image.png';                                
                                $patient_name                              	= $patient_data[$i]["patient_name"];
                                $n++;
                            }

                            if (!empty($patient_name)) {								
                                $insert_id = $this->patient_model->addImport($patient_data[$i]);								
								$this->customlib->generatebarcode($insert_id,$scan_type);//generate barcode and qrcode								
                            }						
			
                            if (!empty($insert_id)) {
                                $data['csvData'] = $result;
                                $this->session->set_flashdata('patient_import_msg', '<div class="alert alert-success text-center">' . $this->lang->line('patients_imported_successfully') . '</div>');
                                $count++;
                                $this->session->set_flashdata('patient_import_msg', '<div class="alert alert-success text-center">Total ' . count($result) . ' ' . $this->lang->line('records_found_in_csv_file_total') . ' ' . $count . ' ' . $this->lang->line('records_imported_successfully') . '</div>');
                            } else {
                                $this->session->set_flashdata('patient_import_msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exists') . '</div>');
                            }

                            $user_password      = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
                            $data_patient_login = array(
                                'username' => $this->patient_login_prefix . $insert_id,
                                'password' => $user_password,
                                'user_id'  => $insert_id,
                                'role'     => 'patient',
                            );
                            $this->user_model->add($data_patient_login);
                        }
                    }
                }
                redirect('admin/patient/import');
            }
        }
    }

    public function check_medicine_exists($medicine_name, $medicine_category_id)
    {
        $this->db->where(array('medicine_category_id' => $medicine_category_id, 'medicine_name' => $medicine_name));
        $query = $this->db->join("medicine_category", "medicine_category.id = pharmacy.medicine_category_id")->get('pharmacy');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }

        $opd_data         = $this->session->flashdata('opd_data');
        $data['opd_data'] = $opd_data;
        $data["title"]    = $this->lang->line('opd_patient');
        $this->session->set_userdata('top_menu', 'OPD_Out_Patient');
        $setting                    = $this->setting_model->get();
        $data['setting']            = $setting;
        $opd_month                  = $setting[0]['opd_record_month'];
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["yesno_condition"]    = $this->yesno_condition;
        $data["bloodgroup"]         = $this->bloodbankstatus_model->get_product(null, 1);
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $patients                   = $this->patient_model->getPatientListall();
        $data["patients"]           = $patients;
        $userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata['role_id'];
        $symptoms_result            = $this->symptoms_model->get();
        $data['symptomsresult']     = $symptoms_result;
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $charge_category            = $this->charge_category_model->getCategoryByModule("opd");
        $data['charge_category']    = $charge_category;
        $data['medicineCategory']   = $this->medicine_category_model->getMedicineCategory();
        $category_dosage            = $this->medicine_dosage_model->getCategoryDosages();
        $data['category_dosage']    = $category_dosage;
        $data['intervaldosage']  = $this->medicine_dosage_model->getIntervalDosage();
        $data['durationdosage']  = $this->medicine_dosage_model->getDurationDosage();
        $doctorid                   = "";
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }

        $data['fields']         = $this->customfield_model->get_custom_fields('opd', 1);
        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $data['organisation']   = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/patient/search', $data);
        $this->load->view('layout/footer');
    }

    public function getopddatatable()
    { 
        $dt_response = $this->patient_model->getAllopdRecord();       
        $fields      = $this->customfield_model->get_custom_fields('opd', 1);
        $dt_response = json_decode($dt_response);       
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                if($value->is_antenatal==1){
                    $is_antenatal = $this->lang->line('yes');
                }else{
                    $is_antenatal = $this->lang->line('no');
                }
                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href=" . base_url() . 'admin/patient/profile/' . $value->pid . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";
                $action .= "</div'>";
                $first_action = "<a href=" . base_url() . 'admin/patient/profile/' . $value->pid . ">";
                if($value->gender){
                    $gender = $this->lang->line(strtolower($value->gender));
                }else{
                    $gender = '';
                }
                
                //==============================
                $row[] = $first_action . $value->patient_name . "</a>" . $action;
                $row[] = $value->patientid;
                $row[] = $value->guardian_name;
                $row[] = $gender;
                $row[] = $value->mobileno;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->last_visit, $this->time_format);                
				if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { 
                $row[]  = $is_antenatal;
				}
                $row[]     = $value->total_visit;
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

    public function getvisitwiseopddatatable()
    { 
        $patientid   = $this->uri->segment(4);
        $dt_response = $this->patient_model->getallopdvisits();
        $fields      = $this->customfield_model->get_custom_fields('opd', 1);
        $dt_response = json_decode($dt_response);

        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $opd_id           = $value->opd_id;
                $visit_details_id = $value->visit_id;
                $check            = $this->db->where("visit_details_id", $visit_details_id)->get('ipd_prescription_basic');
                if ($check->num_rows() > 0) {
                    $result[$key]['prescription'] = 'yes';
                } else {
                    $result[$key]['prescription'] = 'no';                     
                }

                $action = "<div class='white-space-nowrap'>";
                if ($this->rbac->hasPrivilege('opd_print_bill', 'can_view')) {
                    $action .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-opd-id=" . $opd_id . " data-record-id=" . $visit_details_id . " class='btn btn-default btn-xs print_visit_bill'  data-toggle='tooltip' title='" . $this->lang->line('print_bill') . "'><i class='fa fa-file'></i></a>";
                }
                if ($result[$key]['prescription'] == 'no') {
                    if ($this->rbac->hasPrivilege('prescription', 'can_add')) {
                        $action .= "<a href='#' onclick='getRecord_id(" . $visit_details_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('add_prescription') . "'><i class='fas fa-prescription'></i></a>";
                    }
                } elseif ($result[$key]['prescription'] == 'yes') {
                    if ($this->rbac->hasPrivilege('prescription', 'can_view')) {
                        $action .= "<a href='#' onclick='view_prescription(" . $visit_details_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('view_prescription') . "'><i class='fas fa-file-prescription'></i></a>";
                    }
                }
                if ($this->rbac->hasPrivilege('manual_prescription', 'can_view')) {
                    $action .= "<a href='#' onclick='viewmanual_prescription(" . $visit_details_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('manual_prescription') . "'><i class='fas fa fa-print'></i></a>";
                }
                $action .= "<a href='javascript:void(0)' data-loading-text='" . $this->lang->line('please_wait') . "' data-opd-id=" . $opd_id . " data-record-id=" . $visit_details_id . " class='btn btn-default btn-xs get_opd_detail'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";
                if (!$value->is_ipd_moved) {
                    if ($this->rbac->hasPrivilege('opd_move_patient_in_ipd', 'can_view')) {
                        $action .= "<a href='javascript:void(0)' data-toggle='tooltip'  data-original-title='" . $this->lang->line('move_in_ipd') . "' class='btn btn-default btn-xs move_opd' data-opd-id=" . $this->opd_prefix . $opd_id . " data-record-id=" . $visit_details_id . "><i class='fas fa-share-square'></i></a>";
                    }
                }

                $action .= "</div>";
                $first_action = "<a href=" . base_url() . 'admin/patient/visitdetails/' . $value->pid . '/' . $opd_id . ">";
                if($value->is_antenatal==1){
                    $is_antenatal = $this->lang->line('yes');
                }else{
                    $is_antenatal = $this->lang->line('no');
                }
                
                if($value->symptoms){
                    $symptoms = nl2br($value->symptoms);
                }else{
                    $symptoms = '';
                }
                //==============================
                $row[] = $first_action . $this->opd_prefix . $opd_id . "</a>";
                $row[] = $value->patient_name;
                $row[] = $value->case_reference_id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->appointment_date, $this->time_format);
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $value->refference;
                $row[] = $symptoms;
				if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { 
                $row[] = $is_antenatal;
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
                $row[]     = $action;
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

    public function opd_dischargedpatients()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
        $opd_data = $this->session->flashdata('opd_data');
        $data['fields'] = $this->customfield_model->get_custom_fields('opd', 1);
        $this->load->view('layout/header');
        $this->load->view('admin/patient/opddischargepatients.php', $data);
        $this->load->view('layout/footer');
    }
	
    public function getopddischargepatient()
    {
        $dt_response = $this->patient_model->getalldischargeopdRecord();
        $fields      = $this->customfield_model->get_custom_fields('opd', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = "";
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href=" . base_url() . 'admin/patient/profile/' . $value->pid . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";
                $action .= "</div'>";
                $first_action = "<a href=" . base_url() . 'admin/patient/profile/' . $value->pid . ">";
                //==============================
                $row[] = $first_action . $value->patient_name . "</a>" . $action;
                $row[] = $value->patientid;
                $row[] = $value->guardian_name;                 
                
                if($value->gender){
                    $row[]        = $this->lang->line(strtolower($value->gender));
                }else{
                    $row[]        = '';
                }               
                
                $row[] = $value->mobileno;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->last_visit, $this->time_format);

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
                $row[]     = $value->total_visit;
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

    public function getopdvisitdatatable()
    {
        $patientid   = $this->uri->segment(4);
        $dt_response = $this->patient_model->getAllopdvisitRecord($patientid);
        $fields      = $this->customfield_model->get_custom_fields('opd', 1);
        $dt_response = json_decode($dt_response);

        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $opd_id           = $value->opd_id;
                $visit_details_id = $value->visit_id;
                $check            = $this->db->where("visit_details_id", $visit_details_id)->get('ipd_prescription_basic');
                $check_antenatal            = $this->db->where("visit_details_id", $visit_details_id)->get('primary_examine');
             
                if($value->gender=='Female'){
                     if ($check_antenatal->num_rows() > 0) {
                        $result[$key]['antenatal_prescription'] = 'yes';
                    }else{
                         $result[$key]['antenatal_prescription'] = 'no';
                    }
                }
				
                if ($check->num_rows() > 0) {
                    $result[$key]['prescription'] = 'yes';
                } else {
                    $result[$key]['prescription'] = 'no';                    
                }

                $action = "<div class='white-space-nowrap'>";
                $antenatal_prescription="";
                if ($this->rbac->hasPrivilege('opd_print_bill', 'can_view')) {
                    $action .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-opd-id=" . $opd_id . " data-record-id=" . $visit_details_id . " class='btn btn-default btn-xs print_visit_bill'  data-toggle='tooltip' title='" . $this->lang->line('print_bill') . "'><i class='fas fa fa-print'></i></a>";
                }

                if ($result[$key]['prescription'] == 'no') {
                    if ($this->rbac->hasPrivilege('prescription', 'can_add')) {
                        $action .= "<a href='#' onclick='getRecord_id(" . $visit_details_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('add_prescription') . "'><i class='fas fa-prescription'></i></a>";
                    }
                } elseif ($result[$key]['prescription'] == 'yes') {
                    if ($this->rbac->hasPrivilege('prescription', 'can_view')) {
                        $action .= "<a href='#' onclick='view_prescription(" . $visit_details_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('view_prescription') . "'><i class='fas fa-file-prescription'></i></a>";
                    }
                }

                if ($this->rbac->hasPrivilege('manual_prescription', 'can_view')) {
                    $action .= "<a href='#' onclick='viewmanual_prescription(" . $visit_details_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('manual_prescription') . "'><i class='fa fa-file'></i></a>";
                }

                $action .= "<a href='javascript:void(0)' data-loading-text='" . $this->lang->line('please_wait') . "' data-opd-id=" . $opd_id . " data-record-id=" . $visit_details_id . " class='btn btn-default btn-xs get_opd_detail'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                if($value->is_antenatal==1 && $value->gender=='Female'){
                    if($result[$key]['antenatal_prescription']=='no'){						
						if ($this->rbac->hasPrivilege('opd_antenatal', 'can_add')) { 
							$action .= "<a href='#' onclick='addantenatal(" . $visit_details_id . "," . $value->case_reference_id . " )' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('add_antenatal_finding') . "'><img width='15' src='".base_url().'uploads/patient_images/pregnant.png'."'></a>";
						}						
                    }else{						
						if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { 
							$action .= "<a href='#' onclick='viewantenatal(" . $visit_details_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('view_antenatal_finding') . "'><img width='15' src='".base_url().'uploads/patient_images/ultrasound-machine.png'."'></i></a>";
						}						
                    }
				}                

                if (!$value->is_ipd_moved) {
                    if ($this->rbac->hasPrivilege('opd_move_patient_in_ipd', 'can_view')) {
                        $action .= "<a href='javascript:void(0)' data-toggle='tooltip'  data-original-title='" . $this->lang->line('move_in_ipd') . "' class='btn btn-default btn-xs move_opd' data-opd-id=" . $this->opd_prefix . $opd_id . " data-record-id=" . $visit_details_id . "><i class='fas fa-share-square'></i></a>";
                    }
                }

                $action .= "</div>";
                $first_action = "<a href=" . base_url() . 'admin/patient/visitdetails/' . $value->pid . '/' . $opd_id . ">";
                if($value->symptoms){
                    $symptoms = nl2br($value->symptoms);
                }else{
                    $symptoms = '';
                }
				
                //==============================
                $row[] = $first_action . $this->opd_prefix . $opd_id . "</a>";
                $row[] = $value->case_reference_id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->appointment_date, $this->time_format);
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $value->refference;
                $row[] = $symptoms;
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
                $row[]     = $action;
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

    public function moveIpdForm()
    {
        $visit_detail_id            = $this->input->post('visit_details_id');
        $data                       = array();
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $data['organisation']       = $this->organisation_model->get();
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $bedgroup_list              = $this->bedgroup_model->bedGroupFloor();
        $data["bedgroup_list"]      = $bedgroup_list;
        $setting                    = $this->setting_model->get();
        $data['setting']            = $setting;
        $data['opd_prefix']         = $this->opd_prefix;
        $data['patient']            = $this->patient_model->getopdvisitDetailsbyvisitid($visit_detail_id);       
        $page                       = $this->load->view('admin/patient/_moveIpdForm', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getvisitdatatable($opdid)
    {
        $dt_response = $this->patient_model->getAllvisitRecord($opdid);
        $fields      = $this->customfield_model->get_custom_fields('opdrecheckup', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();

                //====================================
                $opd_id           = $value->opd_id;
                $visit_details_id = $value->visit_id;
                $check = $this->db->where("visit_details_id", $visit_details_id)->get('ipd_prescription_basic');
                if ($check->num_rows() > 0) {
                    $result[$key]['prescription'] = 'yes';
                } else {
                    $result[$key]['prescription'] = 'no';
                    $userdata                     = $this->customlib->getUserData();
                    if ($this->session->has_userdata('hospitaladmin')) {
                        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                        if ($doctor_restriction == 'enabled') {                            
                            if ($userdata["role_id"] == 3) {
                                if ($userdata["id"] == $value->staff_id) {

                                } else {
                                    $result[$key]['prescription'] = 'not_applicable';
                                }
                            }
                        }
                    }
                }
				
                $check_antenatal            = $this->db->where("visit_details_id", $visit_details_id)->get('primary_examine');
               
                if($value->gender=='Female'){
                     if ($check_antenatal->num_rows() > 0) {
                        $result[$key]['antenatal_prescription'] = 'yes';
                    }else{
                        $result[$key]['antenatal_prescription'] = 'no';
                    }
                }

                $action = "<div class=''>";               

                if ($this->rbac->hasPrivilege('prescription', 'can_add')) {

                    if ($result[$key]['prescription'] == 'no') {
                        if ($this->customlib->checkDischargePatient($value->discharged)) {
                            $action .= "<a href='#'  onclick='getRecord_id(" . $value->visit_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('add_prescription') . "'><i class='fas fa-prescription'></i></a>";
                        }
                    } elseif ($result[$key]['prescription'] == 'yes') {
                        $action .= "<a href='#'  onclick='view_prescription(" . $visit_details_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('view_prescription') . "'><i class='fas fa-file-prescription'></i></a>";
                    }
                }

                if ($this->rbac->hasPrivilege('manual_prescription', 'can_view')) {
                    $action .= "<a href='#'  onclick='viewmanual_prescription(" . $visit_details_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('manual_prescription') . "'><i class='fas fa fa-print'></i></a>";
                }
				
                if($value->gender=='Female' && $value->is_antenatal==1){
                    if($result[$key]['antenatal_prescription']=='no'){
						if ($this->rbac->hasPrivilege('opd_antenatal', 'can_add')) {
							$action .= "<a href='#' onclick='addantenatal(" . $visit_details_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='".$this->lang->line('add_antenatal_finding')."'><img width='15' src='".base_url().'uploads/patient_images/pregnant.png'."'></a>";
						}
                    }else{
						if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) {
							$action .= "<a href='#' onclick='viewantenatal(" . $visit_details_id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='".$this->lang->line('view_antenatal_finding')."'><img width='15' src='".base_url().'uploads/patient_images/ultrasound-machine.png'."'></a>";
						}
                    }
                }
				
                $action .= "<a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id=" . $visit_details_id . " class='btn btn-default btn-xs get_opd_detail'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                $action .= "</div>";
                
                if($value->symptoms){
                    $symptoms = nl2br($value->symptoms);
                }else{
                    $symptoms = '';
                }
                
                //=====================
                $row[] = $this->customlib->getSessionPrefixByType('checkup_id') . $visit_details_id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->appointment_date, $this->time_format);
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $value->refference;
                $row[] = $symptoms;
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
                $row[]     = $action;
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

    public function getchargesdatatable()
    {
        $id      = $this->uri->segment(4);
        $visitid = $this->uri->segment(5);

        $dt_response = $this->charge_model->getAllchargesRecord($id, $visitid);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();

        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $action = "<div class=''>";

                if ($this->rbac->hasPrivilege('opd_charges', 'can_delete')) {
                    $action .= "<a href='#' onclick='deleterecord(" . $value->patient_id . ',' . $value->opd_id . ',' . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $action .= "</div>";
                //==============================

                $row[] = date($this->customlib->getHospitalDateFormat(true, true), strtotime($value->date));
                $row[] = $value->charge_type;
                $row[] = $value->charge_category;
                $row[] = $value->standard_charge;
                $row[] = $value->org_charge;
                $row[] = $value->apply_charge;
                $row[] = $action;
                //====================

                //====================
                $row[]     = $action;
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

    public function getpaymentdatatable()
    {
        $id          = $this->uri->segment(4);
        $opdid       = $this->uri->segment(5);
        $dt_response = $this->payment_model->getAllpaymentRecord($id, $opdid);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();

        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $action = "<div class=''>";

                if ($this->rbac->hasPrivilege('opd_charges', 'can_delete')) {
                    $action .= "<a href='#' onclick='deleterecord(" . $value->patient_id . ',' . $value->opd_details_id . ',' . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $action .= "</div>";
                //==============================

                $row[] = date($this->customlib->getHospitalDateFormat(true, true), strtotime($value->date));
                $row[] = $value->note;
                $row[] = $value->payment_mode;
                $row[] = $value->paid_amount;
                $row[] = $action;
                //====================

                //====================
                $row[]     = $action;
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

    public function getdiagnosisdatatable()
    {
        $pid                       = $this->input->post('pid');
        $opdid                     = $this->input->post('opdid');
        $diagnosis_details         = $this->patient_model->getDiagnosis($pid);
        $data['diagnosis_details'] = $diagnosis_details;
        $section_page              = $this->load->view('admin/patient/_datadiagnosis', $data);
    }

    public function opdvisit_search()
    {
        $pid   = $this->input->post('pid');
        $opdid = $this->input->post('opdid');

        $draw       = $_POST['draw'];
        $row        = $_POST['start'];
        $rowperpage = $_POST['length']; // Rows display per page

        $resultlist   = $this->patient_model->search_datatable($pid, $opdid);
        $total_result = $this->patient_model->search_datatable_count($pid, $opdid);

        $data = array();
        foreach ($resultlist as $result_key => $result_value) {
            $action = "<div class=''>";
            if ($this->rbac->hasPrivilege('prescription', 'can_add')) {

                if ($result_value['prescription'] == 'no') {
                    $action .= "<a href='#' onclick='getRecord_id(" . $result_value['id'] . "," . $opdid . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('add_prescription') . "'><i class='fas fa-prescription'></i></a>";
                } elseif ($result_value['prescription'] == 'yes') {
                    $userdata           = $this->customlib->getUserData();
                    $prescription       = "yes";
                    $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                    if ($doctor_restriction == 'enabled') {
                        if ($userdata["role_id"] == 3) {
                            if ($userdata["id"] == $result_value["staff_id"]) {

                            } else {
                                $prescription = 'not_applicable';
                            }
                        }
                    }
                    $action .= "<a href='#' onclick='view_prescription(" . $result_value['opdid'] . ',' . $result_value['opdid'] . ',' . $result_value['id'] . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('view') . ' ' . $this->lang->line('prescription') . "'><i class='fas fa-file-prescription'></i></a>";
                }
            }

            $action .= "<a href='#' onclick='getRecord(" . $result_value['patientid'] . ',' . $result_value['opdid'] . ',' . $result_value['id'] . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' ></a>";
            $action .= "</div'>";
            
                if($result_value['symptoms']){
                    $symptoms = nl2br($result_value['symptoms']);
                }else{
                    $symptoms = '';
                }
                
            $nestedData   = array();
            $nestedData[] = $result_value['id'];
            $nestedData[] = $this->customlib->YYYYMMDDHisTodateFormat($result_value['appointmentdate'], $this->time_format);            
            $nestedData[] = $result_value['name'] . " " . $result_value['surname'] . " (" . $result_value['employee_id'] . ")";
            $nestedData[] = $result_value['reference'];
            $nestedData[] = $symptoms;
            $nestedData[] = $action;
            $data[]       = $nestedData;
        }
        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format

    }

    public function getPartialsymptoms()
    {
        $sys_id              = $this->input->post('sys_id');
        $row_id              = $this->input->post('row_id');
        $sectionList         = $this->symptoms_model->getbysysarr($sys_id);
        $data['sectionList'] = $sectionList;
        $data['row_id']      = $row_id;
        $section_page        = $this->load->view('admin/patient/_getPartialsymptoms', $data, true);

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(array(
                'status' => 1,
                'record' => $section_page,
            )));
    }

    public function getPatientList()
    {
        $patients         = $this->patient_model->getPatientListall();
        $data["patients"] = $patients;
        echo json_encode($patients);
    }

    public function getsymptoms()
    {
        $id               = $this->input->post('id');
        $symptoms         = $this->patient_model->getsymptoms($id);
        $data["symptoms"] = $symptoms;
        echo json_encode($symptoms);
    }

    public function ipdsearch($bedid = '', $bedgroupid = '')
    {
        if (!$this->rbac->hasPrivilege('ipd_patient', 'can_view')) {
            access_denied();
        }

        $ipd_data         = $this->session->flashdata('ipd_data');
        $data['ipd_data'] = $ipd_data;
        $data['fields']   = $this->customfield_model->get_custom_fields('ipd', 1);
        if (!empty($bedgroupid)) {
            $data["bedid"]      = $bedid;
            $data["bedgroupid"] = $bedgroupid;
        }
        $this->session->set_userdata('top_menu', 'IPD_in_patient');
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["bloodgroup"]         = $this->bloodbankstatus_model->get_product(null, 1);
        $data['bed_list']           = $this->bed_model->bedNoType();
        $data['floor_list']         = $this->floor_model->floor_list();
        $data['bedlist']            = $this->bed_model->bed_list();
        $data['bedgroup_list']      = $this->bedgroup_model->bedGroupFloor();
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $patients                   = $this->patient_model->getPatientListall();
        $symptoms_result            = $this->symptoms_model->get();
        $data['symptomsresult']     = $symptoms_result;
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $data["patients"]           = $patients;
        $data["doctors"]            = $doctors;
        $data["yesno_condition"]    = $this->yesno_condition;
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
        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $setting                = $this->setting_model->get();
        $data['setting']        = $setting;
        $data['organisation']   = $this->organisation_model->get();

        $this->load->view('layout/header');
        $this->load->view('admin/patient/ipdsearch', $data);
        $this->load->view('layout/footer');
    }

    public function getipddatatable()
    {
        $dt_response = $this->patient_model->getAllipdRecord();
        
        $fields      = $this->customfield_model->get_custom_fields('ipd', 1);
        $userdata    = $this->customlib->getUserData();
        $role_id     = $userdata['role_id'];
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                
                if($value->is_antenatal==1){
                    $is_antenatal = $this->lang->line('yes');
                }else{
                    $is_antenatal = $this->lang->line('no');
                }
                
                $id                = $value->id;
                $ipdid             = $value->ipdid;
                $discharge_details = $this->patient_model->getIpdBillDetails($id, $ipdid);
                $action            = "<div class='rowoptionview rowview-mt-19'>";

                if ($this->rbac->hasPrivilege('ipd_patient', 'can_view')) {
                    $action .= "<a href=" . base_url() . 'admin/patient/ipdprofile/' . $value->ipdid . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";
                }

                if($value->gender){
                    $gender =  $this->lang->line(strtolower($value->gender));
                }else{
                    $gender =  '';
                }
                
                $action .= "</div'>";
                $first_action = "<a href=" . base_url() . 'admin/patient/ipdprofile/' . $value->ipdid . ">";
                //==============================
                $row[] = $first_action . $this->customlib->getSessionPrefixByType('ipd_no') . $value->ipdid . "</a>" . $action;
                $row[] = $value->case_reference_id;
                $row[] = composePatientName($value->patient_name, $value->id);              
                $row[] = $gender;
                $row[] = $value->mobileno;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $value->bed_name . "-" . $value->bedgroup_name . "-" . $value->floor_name;
				if ($this->rbac->hasPrivilege('ipd_antenatal', 'can_view')) { 
                $row[] = $is_antenatal;
				}
				
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";

                        }
                        $row[] = $display_field;
                    }
                }
                $row[] = amountFormat($value->ipdcredit_limit);
                //====================
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

    public function getdischargeddatatable()
    {
        $dt_response = $this->patient_model->getAlldischargedRecord();
        $fields      = $this->customfield_model->get_custom_fields('ipd', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();

        if (!empty($dt_response->data)) {

            foreach ($dt_response->data as $key => $value) {
                $total  = 0;
                $amount = 0;
                $tax    = 0;
                $row    = array();
                //====================================
                $id             = $value->id;
                $ipdid          = $value->ipdid;
                $charge_details = $this->charge_model->getipdDischargeChargesbyCaseId($value->case_reference_id);

                foreach ($charge_details as $charge_details_key => $charge_details_value) {
                    $total += $charge_details_value["apply_charge"];
                    $amount += $charge_details_value["amount"];
                    if ($charge_details_value["tax"] > 0) {
                        $tax = ($charge_details_value["apply_charge"] * $charge_details_value["tax"]) / 100;
                    }
                }

                $payment = $this->patient_model->getPayment($id, $ipdid);
                $action  = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a title=".$this->lang->line('view')." href=" . base_url() . 'admin/patient/ipdprofile/' . $value->ipdid . "><i class='fa fa-reorder' aria-hidden='true'></i></a>";
                $action .= "</div'>";
                $first_action = "<a href=" . base_url() . 'admin/patient/ipdprofile/' . $value->ipdid . ">";
                $row[]        = $first_action . $value->patient_name . "</a>" . $action;
                $row[]        = $value->id;
                $row[]        = $value->case_reference_id;
                
                if($value->gender){
                    $row[]        = $this->lang->line(strtolower($value->gender));
                }else{
                    $row[]        = '';
                }
                
                $row[]        = $value->mobileno;
                $row[]        = $value->name . " " . $value->surname . " (" . $value->employee_id . ")";
                $row[]        = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[]        = $this->customlib->YYYYMMDDHisTodateFormat($value->discharge_date, $this->time_format);
                $row[]        = amountFormat($tax);
                $row[]        = amountFormat($total);
                $row[]        = amountFormat($amount);
                $dt_data[]    = $row;
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

    public function ipddischargedreports()
    {
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $search['gender']      = $this->input->post('gender');
        $search['discharged']  = $this->input->post('discharged');        
        $start_date            = '';
        $end_date              = '';

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

        $condition['gender']         =  $this->input->post('gender');
        $condition['discharged']     =  $this->input->post('discharged');
        $condition['from_age']       =  $this->input->post('from_age');
        $condition['to_age']         =  $this->input->post('to_age');
        $condition['doctor']         =  $this->input->post('doctor');
        $condition['miscellaneous']  =  $this->input->post('miscellaneous');

        $reportdata = $this->transaction_model->ipddischargedreportRecord($condition); 
        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $status     = $this->customlib->discharge_status($value->discharge_status);
                $consultant = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                if ($value->doctors != "") {
                    $consultant = $consultant . ", " . $value->doctors;
                } else {
                    $consultant = $consultant;
                }

                $action = "";
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a target='_blank' href=" . site_url('admin/patient/ipdprofile/' . $value->id) . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";
                $action .= "</div'>";
                $first_action = "<a href=" . base_url() . 'admin/patient/ipdprofile/' . $value->id . ">";
                //==============================

                $row = array();

                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = $first_action . $this->customlib->getSessionPrefixByType('ipd_no') . $value->id . "</a>" . $action;
                $row[]     = $value->case_reference_id;
                
                if($value->gender){
                    $row[]     = $this->lang->line(strtolower($value->gender));
                }else{
                    $row[]     = '';
                }
                
                $row[]     = $value->mobileno;

                if($value->is_antenatal==1){
                    $antinatal_status=$this->lang->line('yes');
                }else if($value->is_antenatal==0){
                    $antinatal_status=$this->lang->line('no');
                }
                
                $row[]     = $antinatal_status;
                $row[]     = $consultant;
                $row[]     = $value->beds;
                $row[]     = $this->customlib->YYYYMMDDHisTodateFormat($value->date);
                $row[]     = $this->customlib->YYYYMMDDHisTodateFormat($value->discharge_date);
                $row[]     = $status;
                $row[]     = $value->admit_duration;
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

    public function opddischargedreports()
    {
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $search['gender']      = $this->input->post('gender');
        $start_date = '';
        $end_date   = '';

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

        $condition['gender']            = $this->input->post('gender');
        $condition['discharged']        = $this->input->post('discharged');
        $condition['from_age']          = $this->input->post('from_age');
        $condition['to_age']            = $this->input->post('to_age');
        $condition['doctor']            = $this->input->post('doctor');
        $condition['miscellaneous']     = $this->input->post('miscellaneous');
        $reportdata              = $this->transaction_model->opddischargedreportRecord($condition);
        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $status     = $this->customlib->discharge_status($value->discharge_status);
                $consultant = composeStaffNameByString($value->name, $value->surname, $value->employee_id);

                $action = "";
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a target='_blank' href=" . base_url('admin/patient/visitdetails/' . $value->patient_id . '/' . $value->id) . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";
                $action .= "</div'>";
                $first_action = "<a href=" . base_url() . 'admin/patient/visitdetails/' . $value->patient_id . '/' . $value->id . ">";
                //==============================

                $row = array();

                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = $first_action . $this->customlib->getSessionPrefixByType('opd_no') . $value->id . "</a>" . $action;
                $row[]     = $value->case_reference_id;
                
                if($value->gender){
                    $row[]     = $this->lang->line(strtolower($value->gender));
                }else{
                    $row[]     = '';
                }
                $row[]     = $value->mobileno;

                if($value->is_antenatal==1){
                    $antenatal_status=$this->lang->line("yes");
                }else{
                    $antenatal_status=$this->lang->line("no");
                }

                $row[]     = $antenatal_status;

                $row[]     = $consultant;
                $row[]     = $this->customlib->YYYYMMDDHisTodateFormat($value->appointment_date);
                $row[]     = $this->customlib->YYYYMMDDHisTodateFormat($value->discharge_date);
                $row[]     = $status;
                $row[]     = $value->admit_duration;
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
    public function discharged_patients()
    {
        if (!$this->rbac->hasPrivilege('discharged_patients', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'IPD_in_patient');
        $this->load->view('layout/header');
        $this->load->view('admin/patient/dischargedPatients');
        $this->load->view('layout/footer');
    }

    public function visitdetails($id, $opdid)
    {
       
        if (!empty($id)) {
            $result         = $this->patient_model->getDetails($opdid);       
            $data['result'] = $result;

            $data["id"]     = $id;
            $data["opdid"]  = $opdid;
            $visit_max_id   = $this->patient_model->getvisitmaxid($opdid);
            $visit_min_id   = $this->patient_model->getvisitminid($opdid);
            $data['visitminid']     = $visit_min_id['visitid'];
            $data['visitdata']          = $visit_max_id;
            $symptoms_resulttype        = $this->symptoms_model->getsymtype();
            $data['symptomsresulttype'] = $symptoms_resulttype;
            $doctors                    = $this->staff_model->getStaffbyrole(3);
            $data["doctors"]            = $doctors;
            $pathology                  = $this->pathology_model->getpathologytest();
            $data['pathology']          = $pathology;
            $radiology                  = $this->radio_model->getradiologytest();
            $data['radiology']          = $radiology;            
            $medicationreport           = $this->patient_model->getmedicationdetailsbydateopd($opdid);
            $max_dose                   = $this->patient_model->getMaxByopdid($opdid);
            $data['max_dose']           = $max_dose->max_dose;
            $data["medication"]         = $medicationreport;
            $userdata                   = $this->customlib->getUserData();
            $role_id                    = $userdata['role_id'];
            $category_dosage            = $this->medicine_dosage_model->getCategoryDosages();
            $data['category_dosage']    = $category_dosage;
            $doctorid                   = "";
            $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
            $disable_option             = false;
            if ($doctor_restriction == 'enabled') {
                if ($role_id == 3) {
                    $disable_option = true;
                    $doctorid       = $userdata['id'];
                }
            }
            $staff_id                    = $this->customlib->getStaffID();
            $data['logged_staff_id']     = $staff_id;
            $data['organisation']        = $this->organisation_model->get();
            $orgid                       = '';
            $data['org_select']          = $orgid;
            $data["doctor_select"]       = $doctorid;
            $data["disable_option"]      = $disable_option;
            $data["payment_mode"]        = $this->payment_mode;
            $data["yesno_condition"]     = $this->yesno_condition;
            $data["charge_type"]         = $this->chargetype_model->getChargeTypeByModule("opd","appointment");
            $data['recent_record_count'] = 5;
            $operation_theatre     = $this->operationtheatre_model->getopdoperationDetails($opdid);
            $timeline_list         = $this->timeline_model->getPatientTimeline($id, $timeline_status = ''); 
            $data["timeline_list"] = $timeline_list;
            $data['operation_theatre'] = $operation_theatre;
            $data['medicineCategory']  = $this->medicine_category_model->getMedicineCategory();
            $data['intervaldosage']    = $this->medicine_dosage_model->getIntervalDosage();
            $data['durationdosage']    = $this->medicine_dosage_model->getDurationDosage();
            $data['dosage']            = $this->medicine_dosage_model->getMedicineDosage();
            $data['medicineName']      = $this->pharmacy_model->getMedicineName();
            $charges                   = $this->charge_model->getopdCharges($opdid);            
            $paymentDetails            = $this->transaction_model->OPDPatientPayments($opdid);
            $data["charges_detail"]    = $charges;            
            $data["payment_details"]           = $paymentDetails;
            $data['roles']                     = $this->role_model->get();
            $getVisitDetailsid                 = $this->patient_model->getVisitDetailsid($opdid);
            $data['medicationreport_overview'] = $this->patient_model->getmedicationdetailsbydate_opdoverview($opdid);
            if (!empty($getVisitDetailsid)) {
                $data['visitconferences'] = $this->conference_model->getconfrencebyvisitid($getVisitDetailsid);                
            } else {
                $data['visitconferences'] = array();
            }

            $data['fields']          = $this->customfield_model->get_custom_fields('opdrecheckup', 1);
            $data['ot_fields']       = $this->customfield_model->get_custom_fields('operationtheatre', 1);
            $data['opd_prefix']      = $this->opd_prefix;
            $charge_category         = $this->charge_category_model->getCategoryByModule("opd");
            $data['charge_category'] = $charge_category;
            $data['categorylist']    = $this->operationtheatre_model->category_list();
            $data["opd_data"]       = $this->patient_model->getPatientVisitDetails($id);
            $data['investigations'] = $this->patient_model->getallinvestigation($result['case_reference_id']);
            $data["bloodgroup"]     = $this->bloodbankstatus_model->get_product(null, 1);
            $data["marital_status"] = $this->marital_status;
            $data['is_discharge']   = $this->customlib->checkDischargePatient($data["result"]['discharged']);
            $data['patientdetails'] = $this->patient_model->getpatientoverviewbycaseid($result['case_reference_id']);           
            $data['graph']               = $this->transaction_model->opd_bill_paymentbycase_id($result['case_reference_id']);
            $data['recent_record_count'] = 5;			
			$data['patientcurrentvital']    = $this->vital_model->getcurrentvitals($id);			
			$data['patient_vital_date']	=	$patient_vital_date = 	$this->vital_model->getpatientvitaldate($id);			
            $data['vital_list']     = $vital_list = $this->vital_model->getvitallist();
 
			foreach($patient_vital_date as $patient_vital_date_result){
				$messure_date	= '';
				if($patient_vital_date_result['messure_date']){
					$messure_date	=	date('Y-m-d', strtotime($patient_vital_date_result['messure_date']));	 
				}
				foreach($vital_list as $vital_list_result){				
					$vital_id = $vital_list_result['id'];				
					$patient_vital[$messure_date][$vital_id]  = $this->vital_model->getpatientsvital($id,$vital_id,$messure_date);	
						 
				}
			}			
			 
			if(!empty($patient_vital)){
				$data['patientvital']	=	$patient_vital;
			}else{
				$data['patientvital']	=	'';
			}

            $this->load->view("layout/header"); 
            $this->load->view("admin/patient/visitDetails", $data);
            $this->load->view("layout/footer");
        }
    }

    public function addlivelink()
	{
        $this->form_validation->set_rules('conference_id', $this->lang->line('conference'), 'trim|required|xss_clean');
    if (!$this->session->has_userdata('zoom_access_token')) {
         $this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
    }

    if ($this->form_validation->run() == false) {
        $msg = array(
            'zoom_live'            => form_error('zoom_live')
        );
       
        $array = array('status' => 0, 'error' => $msg, 'message' => '');
        echo json_encode($array);
    } else {
    
        $conference_id= $this->input->post('conference_id');
        $conference= $this->conference_model->getconference($conference_id);
        $api_type = 'global';
        $params   = array(
            'zoom_api_key'    => "",
            'zoom_api_secret' => "",
        );

        $this->load->library('zoom_api', $params);
        $insert_array = array(
            'staff_id'         => $conference->staff_id,
            'visit_details_id' => $conference->id,
            'title'            => $conference->title,
            'date'             => $this->customlib->dateFormatToYYYYMMDDHis($conference->date, $this->time_format), 
            'duration'         => $conference->duration,
            'created_id'       => $this->customlib->getStaffID(),
            'password'         => random_string(),
            'api_type'         => $api_type,
            'host_video'       => 1,
            'client_video'     => 1,
            'purpose'          => 'consult',
            'timezone'         => $this->customlib->getTimeZone(),
        );
        $response = $this->zoom_api->createAMeeting($insert_array);

        if ($response['status']) {
             
            if (isset($response['data'])) {
                $update_array['return_response'] = json_encode($response['data']);
                $update_array['id'] = $conference->id;
                $update_array['status'] = $conference->status;
                $update_array['live_consult_link'] = 1;
                $conferenceid   = $this->conference_model->update($conference->id, $update_array);               
            }
        }
        
        $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }     
    }
 
    public function addvisitDetails()
    {
        if (!$this->rbac->hasPrivilege('visit', 'can_add')) {
            access_denied();
        }
        $custom_fields = $this->customfield_model->getByBelong('opdrecheckup');  

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[opdrecheckup][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $this->form_validation->set_rules('charge_id', $this->lang->line('charge'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('apply_amount', $this->lang->line('amount'), 'trim|required|xss_clean|valid_amount');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('applied_charge'), 'trim|required|xss_clean|valid_amount');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paid_amount', $this->lang->line('paid_amount'), 'trim|required|xss_clean|valid_amount');
        
        if($this->input->post('payment_mode') == 'Cheque'){
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean|valid_amount');
        }
        
        $consult      = $this->input->post('live_consult');
		if($consult == 'yes'){
			if (!$this->session->has_userdata('zoom_access_token')) {
				$this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
			}
		}
		
        if ($this->form_validation->run() == false) {  
            $msg = array(
                'firstname'        => form_error('name'),
                'appointment_date' => form_error('appointment_date'),
                'amount'           => form_error('amount'),
                'charge_id'        => form_error('charge_id'),
                'apply_amount'     => form_error('apply_amount'),
                'paid_amount'      => form_error('paid_amount'),
                'consultant_doctor'      => form_error('consultant_doctor'),
                'zoom_live'      	=> form_error('zoom_live'),
            );
            
            $msg['cheque_no'] = form_error('cheque_no');
            $msg['cheque_date'] = form_error('cheque_date');            
            
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[opdrecheckup][" . $custom_fields_id . "]"] = form_error("custom_fields[opdrecheckup][" . $custom_fields_id . "]");
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
            $check_patient_id = $this->patient_model->getMaxOPDId();
            if (empty($check_patient_id)) {
                $check_patient_id = 0;
            }
            $opdn_id           = $check_patient_id + 1;
            $patient_id        = $this->input->post('id');
            $password          = $this->input->post('password');
            $custom_field_post = $this->input->post("custom_fields[opdrecheckup]");
            $appointment_date  = $this->input->post('appointment_date');
            $doctor_id         = $this->input->post('consultant_doctor');
            $opd_id            = $this->input->post('opd_id');
            if ($consult) {
                $live_consult = $this->input->post('live_consult');
            } else {
                $live_consult = "no";
            }

            $date            = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);
            $revisit_is_tpa = $this->input->post('revisit_is_tpa');
            if($revisit_is_tpa=="" || $revisit_is_tpa==0){
            $organisation_id = null;
            $insurance_validity = null;
            $insurance_id = null;
        }else{           
            $organisation_id = $this->input->post('organisation');
            $insurance_validity = $this->input->post('insurance_validity');
            $insurance_id = $this->input->post('insurance_id');
        }
           
            if (empty($organisation_id)) {
                $organisation_id = null;
            }
            $opd_data = array(
                'appointment_date'  => $date,
                'opd_details_id'    => $opd_id,                
                'case_type'         => $this->input->post('revisit_case'),
                'symptoms'          => $this->input->post('symptoms'),
                'known_allergies'   => $this->input->post('known_allergies'),
                'refference'        => $this->input->post('refference'),
                'cons_doctor'       => $this->input->post('consultant_doctor'),
                'casualty'          => $this->input->post('casualty'),
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note_remark'),
                'live_consult'      => $live_consult,
                'patient_charge_id' => null,
                'transaction_id'    => null,
                'can_delete'        => 'yes',
                'generated_by'      => $this->customlib->getLoggedInUserID(),
            );

            if($this->input->post('is_for_antenatal')!=""){
                $opd_data['is_antenatal'] = $this->input->post('is_for_antenatal') ;
            }

            $payment_section  = $this->config->item('payment_section');
            $transaction_data = array(
                'case_reference_id' => $this->input->post('case_reference_id'),
                'opd_id'            => $this->input->post('opd_id'),
                'amount'            => $this->input->post('paid_amount'),
                'type'              => 'payment',
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note'),
                'patient_id'        => $this->input->post('id'),
                'section'           => $payment_section['opd'],
                'payment_date'      => $date,
                'cheque_date'       => $date,
                'cheque_no'         => $this->input->post('cheque_no'),
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );

            if ($this->input->post('payment_mode') == "Cheque") {
                $cheque_date                     = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date"));
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

            $staff_data = $this->staff_model->getStaffByID($doctor_id);
            $staff_name = composeStaffName($staff_data);
            $charge     = array(
                'opd_id'          => $this->input->post('opd_id'),
                'date'            => $date,
                'charge_id'       => $this->input->post('charge_id'),
                'qty'             => 1,
                'apply_charge'    => $this->input->post('amount'),
                'standard_charge' => $this->input->post('standard_charge'),
                'tpa_charge'      => $this->input->post('schedule_charge'),
                'amount'          => $this->input->post('apply_amount'),
                'tax'          => $this->input->post('percentage'),
                'discount_percentage'=> $this->input->post('discount_percentage'),
                'created_at'      => date('Y-m-d'),
                'note'            => '',
                'organisation_id' => $organisation_id,
                'insurance_validity' => $insurance_validity,
                'insurance_id'    => $insurance_id,
            );

            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[opdrecheckup][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
            $opdvisit_id = $this->patient_model->add_visit_recheckup($opd_data, $transaction_data, $charge);

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $opdvisit_id);
            }
            
            $doctor_id      = $this->input->post('consultant_doctor');
            $setting_result = $this->setting_model->getzoomsetting();
            $opdduration    = $setting_result->opd_duration;			
			
            if ($live_consult = 'yes') {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'         => $doctor_id,
                    'visit_details_id' => $opdvisit_id,
                    'title'            => 'Online consult for Checkup ID ' . $opdvisit_id,
                    'date'             => $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format),
                    'duration'         => $opdduration,
                    'created_id'       => $this->customlib->getStaffID(),
                    'password'         => $password,
                    'api_type'         => $api_type,
                    'host_video'       => 1,
                    'client_video'     => 1,
                    'purpose'          => 'consult',
                    'timezone'         => $this->customlib->getTimeZone(),
                );
				
                $response = $this->zoom_api->createAMeeting($insert_array);                 
				
				if ($response['status']) {
					 
                    if (isset($response['data'])) {
                        $insert_array['return_response'] = json_encode($response['data']);
						 
                        $conferenceid   = $this->conference_model->add($insert_array);
						
						$sender_details = array('patient_id' => $patient_id, 'conference_id' => $conferenceid, 'contact_no' => $this->input->post('contact'), 'email' => $this->input->post('email'));
                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
						 
                    }
                }					
            }

            $sender_details = array('patient_id' => $patient_id, 'opd_no' => $this->customlib->getSessionPrefixByType('opd_no') . $opd_id, 'contact_no' => $this->input->post('contact'), 'email' => $this->input->post('email'));

            $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('consultant_doctor'));
            $event_data     = array(
                'patient_id'           => $patient_id,
                'symptoms_description' => $this->input->post('symptoms'),
                'any_known_allergies'  => $this->input->post('known_allergies'),
                'appointment_date'     => $this->customlib->YYYYMMDDHisTodateFormat($date, $this->time_format),
                'doctor_id'            => $this->input->post('consultant_doctor'),
                'doctor_name'          => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
            );
            $this->system_notification->send_system_notification('opd_new_checkup_created', $event_data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }
  
    public function profile($id)
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'OPD_Out_Patient');
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["yesno_condition"]    = $this->yesno_condition;
        $data["bloodgroup"]         = $this->blood_group;
        $data['medicineCategory']   = $this->medicine_category_model->getMedicineCategory();
        $category_dosage            = $this->medicine_dosage_model->getCategoryDosages();
        $data['category_dosage']    = $category_dosage;
        $data['medicineName']       = $this->pharmacy_model->getMedicineName();
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $pathology                  = $this->pathology_model->getpathologytest();
        $data['pathology']          = $pathology;
        $radiology                  = $this->radio_model->getradiologytest();
        $data['radiology']          = $radiology;
        $data["id"]                 = $id;
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $userdata                   = $this->customlib->getUserData();
        $data['fields']             = $this->customfield_model->get_custom_fields('opd', 1);
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
        $nurseid                = "";
        $data["doctor_select"]  = $doctorid;
        $data["nurse_select"]   = $nurseid;
        $data["disable_option"] = $disable_option;
        $data['roles']          = $this->role_model->get();
        $result                 = array();
        $diagnosis_details      = array();
        $opd_details            = array();

        $timeline_list = array();
        if (!empty($id)) {
            $result         = $this->patient_model->getpatientDetails($id);
            $opd_details_id = $this->patient_model->getopdmaxid($id);
            $timeline_list = $this->timeline_model->getPatientTimeline($id, $timeline_status = '');
        }
        $data["result"]         = $result;
        $data["opd_details_id"] = $opd_details_id;
        $staff_id                = $this->customlib->getStaffID();
        $data['logged_staff_id'] = $staff_id;
        $data["opd_details"]     = $opd_details;
        $data["timeline_list"]   = $timeline_list;
        $data['organisation']    = $this->organisation_model->get();
        $orgid                   = "";
        $data['org_select']      = $orgid;
        $charge_category         = $this->charge_category_model->getCategoryByModule("opd");
        $data['charge_category'] = $charge_category;
        $data['intervaldosage']  = $this->medicine_dosage_model->getIntervalDosage();
        $data['durationdosage']  = $this->medicine_dosage_model->getDurationDosage();
        $data['investigations']  = $this->patient_model->allinvestigationbypatientid($id);
        $data['timeformat']      = $this->time_format;
        $data['patientdetails'] = $this->patient_model->getpatientoverview($id);
        $data['recent_record_count'] = 5;
        $patient_id                = $id;
        $total_visits              = $this->patient_model->totalVisit($patient_id);
        $total_ipd                 = $this->patient_model->totalPatientIPD($patient_id);
        $total_pharmacy            = $this->pharmacy_model->totalPatientPharmacy($patient_id);
        $total_pathology           = $this->pathology_model->totalPatientPathology($patient_id);
        $total_radiology           = $this->radio_model->totalPatientRadiology($patient_id);
        $total_blood_issue         = $this->bloodissue_model->totalPatientBloodIssue($patient_id);
        $total_ambulance           = $this->ambulance_model->totalPatientAmbulance($patient_id);
        $data['total_ambulance']   = $total_ambulance;
        $data['total_blood_issue'] = $total_blood_issue;
        $data['total_radiology']   = $total_radiology;
        $data['total_pathology']   = $total_pathology;
        $data['total_pharmacy']    = $total_pharmacy;
        $data['total_ipd']         = $total_ipd;    
        $data['total_visits']      = $total_visits;
        $data['patient_id']        = $id;
        $data['obstetric_history'] = $this->antenatal_model->getobstetrichistory($patient_id);
        $data['postnatal_history'] = $this->antenatal_model->getpostnatal($patient_id);
        $data['antenatallist'] = $this->antenatal_model->getantenatallist($patient_id);		
		$data['patientcurrentvital']    = $this->vital_model->getcurrentvitals($id);			
		$data['patient_vital_date']	=	$patient_vital_date = 	$this->vital_model->getpatientvitaldate($id);			
        $data['vital_list']     = $vital_list = $this->vital_model->getvitallist();
 
		foreach($patient_vital_date as $patient_vital_date_result){			
			$messure_date	= '';
			if($patient_vital_date_result['messure_date']){
				$messure_date	=	date('Y-m-d', strtotime($patient_vital_date_result['messure_date']));	 
			}
			foreach($vital_list as $vital_list_result){				
				$vital_id = $vital_list_result['id'];				
				$patient_vital[$messure_date][$vital_id]  = $this->vital_model->getpatientsvital($id,$vital_id,$messure_date);					 
			}
		}			
		 
		if(!empty($patient_vital)){
			$data['patientvital']	=	$patient_vital;
		}else{
			$data['patientvital']	=	'';
		}		
			
        $this->load->view("layout/header");
        $this->load->view("admin/patient/profile", $data);
        $this->load->view("layout/footer");
    }

	public function yearchart()
    {
        $patient_id        = $this->input->post('patient_id');
        $patient_data      = $this->patient_model->getpatientbyid($patient_id);
        $patient_created   = $patient_data['created_at'];
        $create_year       = date('Y', (strtotime($patient_created)-60*60*24*365));
        $current_year      = date('Y');
        $opd_visits        = $this->patient_model->getpatientOPDYearCounter($patient_id, $create_year);
        $ipd_visits        = $this->patient_model->getpatientIPDYearCounter($patient_id, $create_year);
        $pharmacy_visits   = $this->pharmacy_model->getpatientPharmacyYearCounter($patient_id, $create_year);
        $pathology_visits  = $this->pathology_model->getpatientPathologyYearCounter($patient_id, $create_year);
        $radiology_visits  = $this->radio_model->getpatientRadiologyYearCounter($patient_id, $create_year);
        $bloodissue_visits = $this->bloodissue_model->getpatientBloodYearCounter($patient_id, $create_year);      
        $ambulance_visits  = $this->ambulance_model->getpatientAmbulanceYearCounter($patient_id, $create_year);
        $year_range        = range($create_year, $current_year, 1);
        $empty_array       = array_fill(0, count($year_range), 0);
        $datasets          = [
            
        ];

       if ($this->module_lib->hasActive('opd')) {
            $opd_data = array();
            if (!empty($opd_visits)) {               
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $opd_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $opd_visits, 'year');
                        $total_visits = $opd_visits[$result_key]['total_visits'];
                    }
                    $opd_data[] = $total_visits;
                }          
            }
            if(empty($opd_data)){
                $opd_data=$empty_array;
            }
            $datasets[] = array(           
                'data'        => $opd_data,
                'label'       => $this->lang->line('opd'),
                'borderColor' => "#438FFF",
                'fill'        => false,
            );
        }
        
        if ($this->module_lib->hasActive('pharmacy')) {
            $pharmacy_data = array();
            if (!empty($pharmacy_visits)) {            
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $pharmacy_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $pharmacy_visits, 'year');
                        $total_visits = $pharmacy_visits[$result_key]['total_visits'];
                    }
                    $pharmacy_data[] = $total_visits;
                }         
            }
            if(empty($pharmacy_data)){
                $pharmacy_data=$empty_array;
            }
            $datasets[] = array(            
                'data'        => $pharmacy_data,
                'label'       => $this->lang->line('pharmacy'),
                'borderColor' => "#016E51",
                'fill'        => false,
            );
        }

        if ($this->module_lib->hasActive('pathology')) {
            $pathology_data = array();
            if (!empty($pathology_visits)) {            
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $pathology_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $pathology_visits, 'year');
                        $total_visits = $pathology_visits[$result_key]['total_visits'];
                    }
                    $pathology_data[] = $total_visits;
                }        
            }
            if(empty($pathology_data)){
                $pathology_data=$empty_array;
            }
            $datasets[] = array(           
                'data'        => $pathology_data,
                'label'       => $this->lang->line('pathology'),
                'borderColor' => "#A80000",
                'fill'        => false,
            );
        }

        if ($this->module_lib->hasActive('radiology')) {
            $radiology_data = array();
            if (!empty($radiology_visits)) {            
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $radiology_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $radiology_visits, 'year');
                        $total_visits = $radiology_visits[$result_key]['total_visits'];
                    }
                    $radiology_data[] = $total_visits;
                }
            }
            if(empty($radiology_data)){
                $radiology_data=$empty_array;
            }
            $datasets[] = array(            
                'data'        => $radiology_data,
                'label'       => $this->lang->line('radiology'),
                'borderColor' => "#12239E",
                'fill'        => false,
            );
        }

        if ($this->module_lib->hasActive('blood_bank')) {
            $bloodissue_data = array();
            if (!empty($bloodissue_visits)) {                 
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $bloodissue_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $bloodissue_visits, 'year');
                        $total_visits = $bloodissue_visits[$result_key]['total_visits'];
                    }
                    $bloodissue_data[] = $total_visits;
                }              
            }
            if(empty($bloodissue_data)){
                $bloodissue_data=$empty_array;
            }
            $datasets[] = array(               
                'data'        => $bloodissue_data,
                'label'       => $this->lang->line('blood_bank'),
                'borderColor' => "#D82C20",
                'fill'        => false,
            );
        }

        if ($this->module_lib->hasActive('ambulance')) {
            $ambulance_data = array();
            if (!empty($ambulance_visits)) {                
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $ambulance_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $ambulance_visits, 'year');
                        $total_visits = $ambulance_visits[$result_key]['total_visits'];
                    }
                    $ambulance_data[] = $total_visits;
                }                
            }
            if(empty($ambulance_data)){
                $ambulance_data=$empty_array;
            }                
            $datasets[] = array(            
                'data'        => $ambulance_data,
                'label'       => $this->lang->line('ambulance'),
                'borderColor' => "#FFA500",
                'fill'        => false,
            );
         }

        $array = array(
            'labels'  => $year_range,
            'dataset' => $datasets,
        );

        echo json_encode($array);
    }
    
    
    public function ipdprofile($ipdid)  
    {
        if (!$this->rbac->hasPrivilege('ipd_patient', 'can_view')) {
            access_denied();
        }

        $patientid = $this->patient_model->getPatientbyipdid($ipdid);
        $id        = $patientid['pid'];

        if ($ipdid == '') {
            $ipdresult = $this->patient_model->search_ipd_patients($searchterm = '', $active = 'yes', $discharged = 'no', $id);
            $ipdid     = $ipdresult["ipdid"];
        }
        $this->session->set_userdata('top_menu', 'IPD_in_patient');
        $ipdnpres_data              = $this->session->flashdata('ipdnpres_data');
        $data['ipdnpres_data']      = $ipdnpres_data;
        $data['bed_list']           = $this->bed_model->bedNoType();
        $data['bedgroup_list']      = $this->bedgroup_model->bedGroupFloor();
        $data['medicineCategory']   = $this->medicine_category_model->getMedicineCategory();
        $data['intervaldosage']     = $this->medicine_dosage_model->getIntervalDosage();
        $data['durationdosage']     = $this->medicine_dosage_model->getDurationDosage();
        $data['dosage']             = $this->medicine_dosage_model->getMedicineDosage();
        $category_dosage            = $this->medicine_dosage_model->getCategoryDosages();
        $data['category_dosage']    = $category_dosage;
        $data['medicineName']       = $this->pharmacy_model->getMedicineName();
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $operation_theatre          = $this->operationtheatre_model->getipdoperationDetails($ipdid);
        $data['operation_theatre']  = $operation_theatre;
        $data["bloodgroup"]         = $this->blood_group;
        $patients                   = $this->patient_model->getPatientListall();
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $pathology                  = $this->pathology_model->getpathologytest();
        $data['pathology']          = $pathology;
        $radiology                  = $this->radio_model->getradiologytest();
        $data['radiology']          = $radiology;
        $data["patients"]           = $patients;
        $data['organisation']       = $this->organisation_model->get();
        $data["id"]                 = $id;
        $data["ipdid"]              = $ipdid;
        $data["patient_id"]         = $id;
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $nurse                      = $this->staff_model->getStaffbyrole(9);
        $data["nurse"]              = $nurse;
        $data["nurse_select"]       = $nurse;
        $doctors_ipd                = $this->patient_model->getDoctorsipd($ipdid);
        $data["doctors_ipd"]        = $doctors_ipd;
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
        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $result                 = array();
        $diagnosis_details      = array();
        $timeline_list          = array();
        $charges                = array();
        if (!empty($id)) {
            $result               = $this->patient_model->getIpdDetails($ipdid);
            $timeline_list        = $this->timeline_model->getPatientTimeline($id, $timeline_status = '');
            $prescription_details = $this->prescription_model->getIpdPrescription($ipdid);
            $consultant_register  = $this->patient_model->getPatientConsultant($id, $ipdid);
            $nurse_note = $this->patient_model->getdatanursenote($id, $ipdid);
            $max_dose                          = $this->patient_model->getMaxByipdid($ipdid);
            $medicationreport                  = $this->patient_model->getmedicationdetailsbydate($ipdid);
            $data['medicationreport_overview'] = $this->patient_model->getmedicationdetailsbydate_overview($ipdid);

            $data['max_dose'] = $max_dose->max_dose;
            foreach ($nurse_note as $key => $nurse_note_value) {
                $notecomment                        = $this->patient_model->getnurenotecomment($ipdid, $nurse_note_value['id']);
                $nursenote[$nurse_note_value['id']] = $notecomment;
            }
            if (!empty($nursenote)) {
                $data["nursenote"] = $nursenote;
            }
            $charges                     = $this->charge_model->getCharges($ipdid);
            $paymentDetails              = $this->transaction_model->IPDPatientPayments($ipdid);
            $paid_amount                 = $this->payment_model->getPaidTotal($id, $ipdid);
            $data["paid_amount"]         = $paid_amount["paid_amount"];
            $data["payment_details"]     = $paymentDetails;
            $data["consultant_register"] = $consultant_register;
            $data["nurse_note"]          = $nurse_note;
            $data["medication"]          = $medicationreport;
            $data["result"]              = $result;
            $data["prescription_detail"] = $prescription_details;
            $data["timeline_list"]       = $timeline_list;
            $data["charge_type"]         = $this->chargetype_model->getChargeTypeByModule("ipd");
            $data["charges"]             = $charges;
            $data['roles']               = $this->role_model->get();
        }
        $data['fields_consultant']   = $this->customfield_model->get_custom_fields('ipdconsultinstruction', 1);
        $data['fields_nurse']        = $this->customfield_model->get_custom_fields('ipdnursenote', 1);
        $doctorsipd                  = $this->staff_model->getStaffipd(3, $result['cons_doctor']);
        $data['fields_ot']           = $this->customfield_model->get_custom_fields('operationtheatre', 1);
        $data["doctorsipd"]          = $doctorsipd;
        $staff_id                    = $this->customlib->getStaffID();
        $data['logged_staff_id']     = $staff_id;
        $data['ipdconferences']      = $this->conference_model->getconfrencebyipd($doctorid, $id, $ipdid);
        $case_reference_id           = $this->patient_model->getReferenceByIpdId($ipdid);
        $data['bed_history']         = $this->bed_model->getBedHistory($case_reference_id);
        $data['operation_list']      = $this->operationtheatre_model->operation_list();
        $data['category_list']       = $this->operationtheatre_model->category_list();
        $data["ipd_data"]            = $this->patient_model->getPatientIpdVisitDetails($id);
        $data['investigations']      = $this->patient_model->getallinvestigation($result['case_reference_id']);
        $data['is_discharge']        = $this->customlib->checkDischargePatient($data['result']['ipd_discharge']);
        $data['time_format']         = $this->time_format;
        $data['graph']               = $this->transaction_model->ipd_bill_paymentbycase_id($case_reference_id);
        $data['recent_record_count'] = $this->recent_record_count;        
        $data['obstetric_history'] = $this->antenatal_model->getobstetrichistory($id);
        $data['postnatal_history'] = $this->antenatal_model->getpostnatal($id);
        $data['antenatallist'] = $this->antenatal_model->getantenatallist($id);
        
        $credit_limit_percentage     = 0;
                if ($data['result']['ipdcredit_limit'] > 0) {
                    $data['credit_limit']    = $data['result']['ipdcredit_limit'];
                    if($data['graph']['my_balance']>=$data['credit_limit']){
                        $data['donut_graph_percentage']  = '0';                       
                        $data['balance_credit_limit']    = 0;
                        $data['used_credit_limit']       = $data['credit_limit'];
                    }else{
                        $credit_limit_percentage = (($data['graph']['my_balance'] / $data['credit_limit'])*100);
                        $data['donut_graph_percentage']  = number_format(((100-$credit_limit_percentage)), 2);                        
                        $data['balance_credit_limit']    = ($data['credit_limit'] - $data['graph']['my_balance']);
                        $data['used_credit_limit']       = $data['graph']['my_balance'];
                    }                    
                } else {
                    $data['credit_limit'] = 0;
                    $data['used_credit_limit'] = 0;
                    $data['balance_credit_limit'] = 0;
                } 
        
        $data['getipdoverviewtreatment'] = $this->patient_model->getipdoverviewtreatment($id);
    	$data['patientcurrentvital']    = $this->vital_model->getcurrentvitals($id);			
		$data['patient_vital_date']	=	$patient_vital_date = 	$this->vital_model->getpatientvitaldate($id);			
        $data['vital_list']     = $vital_list = $this->vital_model->getvitallist();
 
		foreach($patient_vital_date as $patient_vital_date_result){
			
			$messure_date	= '';
			if($patient_vital_date_result['messure_date']){
				$messure_date	=	date('Y-m-d', strtotime($patient_vital_date_result['messure_date']));	 
			}
			
			foreach($vital_list as $vital_list_result){				
				$vital_id = $vital_list_result['id'];				
				$patient_vital[$messure_date][$vital_id]  = $this->vital_model->getpatientsvital($id,$vital_id,$messure_date);					 
			}
		}			
		 
		if(!empty($patient_vital)){
			$data['patientvital']	=	$patient_vital;
		}else{
			$data['patientvital']	=	'';
		}	
			
        $this->load->view("layout/header");
        $this->load->view("admin/patient/ipdprofile", $data); 
        $this->load->view("layout/footer");
    }

    public function getsummaryDetails($id)
    {
        if (!$this->rbac->hasPrivilege('discharge_summary', 'can_view')) {
            access_denied();
        }
        $print_details         = $this->printing_model->get('', 'summary');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $result         = $this->patient_model->getsummaryDetails($id);
        $data['result'] = $result;
        $this->load->view('admin/patient/printsummary', $data);
    }

    public function getopdsummaryDetails($id)
    {
        $print_details         = $this->printing_model->get('', 'summary');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $result         = $this->patient_model->getsummaryopdDetails($id);
        $data['result'] = $result;
        $this->load->view('admin/patient/printopdsummary', $data);
    }

    public function patientipddetails($patient_id)
    {
        $data['resultlist'] = $this->patient_model->patientipddetails($patient_id);
        $i                  = 0;
        foreach ($data['resultlist'] as $key => $value) {
            $charges                           = $this->patient_model->getCharges($value["id"]);
            $data['resultlist'][$i]["charges"] = $charges['charge'];
            $payment                           = $this->patient_model->getPayment($value["id"]);
            $data['resultlist'][$i]["payment"] = $payment['payment'];
            $i++;
        }
        $data['organisation'] = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/patient/patientipddetails.php', $data);
        $this->load->view('layout/footer');
    }

    public function deleteIpdPatientCharge()
    {
        if (!$this->rbac->hasPrivilege('charges', 'can_delete')) {
            access_denied();
        }
        $id = $this->input->post('id');
        $this->charge_model->deleteIpdPatientCharge($id);
        $return = array('status' => 1, 'msg' => $this->lang->line('patient_charges_deleted_successfully'));
        echo json_encode($return);
    }

    public function delete_doctors($pateint_id, $ipdid, $doctoripd_id)
    {
        $this->patient_model->deleteIpddoctor($doctoripd_id);
        echo json_encode(array('message' => $this->lang->line('data_deleted_successfully')));
    }

    public function deleteOpdPatientDiagnosis($id)
    {
        if (!$this->rbac->hasPrivilege('opd_diagnosis', 'can_delete')) {
            access_denied();
        }
        $this->patient_model->deleteIpdPatientDiagnosis($id);
    }

    public function deleteIpdPatientConsultant($id)
    {
        if (!$this->rbac->hasPrivilege('consultant_register', 'can_add')) {
            access_denied();
        }
        $this->patient_model->deleteIpdPatientConsultant($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('patient_consultant_deleted_successfully') . '</div>');
    }

    public function deleteIpdnursenote($id, $ipdid)
    {
        if (!$this->rbac->hasPrivilege('nurse_note', 'can_add')) {
            access_denied();
        }
        $this->patient_model->deleteIpdnursenote($id, $ipdid);
    }

    public function deletenursenotecomment($id)
    {
        if (!$this->rbac->hasPrivilege('nurse_note', 'can_add')) {
            access_denied();
        }
        $this->patient_model->deletenursenotecomment($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('nurse_note_deleted_successfully') . '</div>');
    }

    public function deleteIpdPatientDiagnosis($pateint_id)
    {
        if (!$this->rbac->hasPrivilege('ipd_diagnosis', 'can_delete')) {
            access_denied();
        }
        $this->patient_model->deleteIpdPatientDiagnosis($pateint_id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('patient_diagnosis_deleted_successfully') . '</div>');
        redirect('admin/patient/ipdprofile/' . $pateint_id . '#diagnosis');
    }

    public function deleteIpdPatientPayment($id)
    {
        $this->transaction_model->deletePayment($id);
    }

    public function deletePayment($id)
    {
        $this->transaction_model->deletePayment($id);
    }

    public function deleteOpdPatientCharge($id)
    {
        $this->charge_model->deleteOpdPatientCharge($id);
    }

    public function report_download($doc)
    {
        $this->load->helper('download');
        $filepath = "./" . $this->uri->segment(4) . "/" . $this->uri->segment(5) . "/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

    public function getopdDetails()
    {
        $data           = array();
        $visitid        = $this->input->post("visit_id");
        $opd_id         = $this->input->post('opd_id');
        $result         = $this->patient_model->getopdvisitDetailsbyvisitid($visitid);       
        $data['fields'] = $this->customfield_model->get_custom_fields('opd', '', '', '', '');
        $data['result'] = $result;
        $data['patient_id'] = $result['patient_id'];     
        $page           = $this->load->view("admin/patient/_getopdDetails", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'patient_id'=>$result['patient_id']));
    }

    public function getopdrecheckupDetails()
    {
        $data           = array();
        $visitid        = $this->input->post("visit_id");
        $result         = $this->patient_model->getopdvisitrecheckupDetailsbyvisitid($visitid);        
        $data['fields'] = $this->customfield_model->get_custom_fields('opdrecheckup');       
        $data['result'] = $result;       
        $page           = $this->load->view("admin/patient/_getopdrecheckupDetails", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page)); //, 'can_delete' => $can_delete
    }

    public function getopdDetailsSummary()
    {
        $id                         = $this->input->post("patient_id");
        $opdid                      = $this->input->post("opd_id");
        $visitid                    = $this->input->post("visitid");
        $result                     = $this->patient_model->getDetails($id, $opdid);
        $appointment_date           = date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['appointment_date']));
        $discharge_date             = date($this->customlib->getHospitalDateFormat(true, false), strtotime($result['discharge_date']));
        $result["appointment_date"] = $appointment_date;
        $result["discharge_date"]   = $discharge_date;
        echo json_encode($result);
    }    

    public function patientvisit()
	{
        $id = $this->input->post('id');        
        $data["patient_details"]        = $this->patient_model->patientDetails($id);        
        $data["opd_data"]        = $this->patient_model->getopdvisitreportdata($id);
        $data["ipd_data"]        = $this->patient_model->getipdvisitreportdata($id);
        $data["pharmacy_data"]   = $this->patient_model->getPatientPharmacyVisitDetails($id);
        $data["radiology_data"]  = $this->patient_model->getPatientRadiologyVisitDetails($id);
        $data["blood_bank_data"] = $this->patient_model->getPatientBloodBankVisitDetails($id);
        $data["ambulance_data"]  = $this->patient_model->getPatientAmbulanceVisitDetails($id);
        $data['pathology_data']  = $this->report_model->getAllpathologybillRecord($id); 
        $page = $this->load->view("admin/patient/_patientvisit", $data, true);
        echo json_encode($page);
    }

    public function getopdvisitdata()
    {
        $opdid      = $this->input->post("opdid");        
        $result = $this->patient_model->getVisitdataDetails($opdid);        
        $result['patients_name']      = composePatientName($result['patient_name'], $result['patientid']);        
        $result['patient_age']        = $this->customlib->getPatientAge($result['age'], $result['month'], $result['day'])." (".$this->lang->line('as_of_date').' '.$this->customlib->YYYYMMDDTodateFormat($result['as_of_date']).")";       
        $appointment_date             = $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date'], $this->time_format);        
        if($result['insurance_validity']){
            $result['insurance_validity'] = $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']);
        }else{
            $result['insurance_validity'] = '';            
        }
        $result['gender'] = $this->lang->line(strtolower($result['gender']));        
        if($result['marital_status']){
            $result['marital_status'] = $this->lang->line(strtolower($result['marital_status']));
        }else{
            $result['marital_status'] = '';
        }        
        $result["appointment_date"]   = $appointment_date;
        echo json_encode($result);
    }

    public function getvisitDetails()
    {
        $visitid                    = $this->input->post("visitid");
        $result                     = $this->patient_model->getvisitDetailsByVisitId($visitid); 
        $appointment_date           = $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date'], $this->time_format);
        $result["patients_name"]    = composePatientName($result['patient_name'], $result['patientid']);
        $result["patient_age"]      = $this->customlib->getPatientAge($result['age'], $result['month'], $result['day'])." (".$this->lang->line('as_of_date').' '.$this->customlib->YYYYMMDDTodateFormat($result['as_of_date']).")";
        $result["gender"]           = $this->lang->line(strtolower($result['gender'])) ;
        $result["marital_status"]   = $this->lang->line(strtolower($result['marital_status'])) ;       
        
        if($result['insurance_validity']){
            $result["tpa_validity"]     = $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']);
        }else{
            $result["tpa_validity"]     =  '';
        }
        $result["appointment_date"] = $appointment_date;
        echo json_encode($result);
    }

    public function getpatientDetails()
    {
        $id                    = $this->input->post("id");
        $result                = $this->patient_model->getpatientDetails($id);		 
        $result['patient_age'] = $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']);

        if (($result['insurance_validity'] == '') || ($result['insurance_validity'] == '0000-00-00') || ($result['insurance_validity'] == '1970-01-01')) {
            $result['insurance_validity'] = "";
        } else {
            $result['insurance_validity'] = $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']);
        }
        
        if($result['dob']){
            $result['dob']                 = $this->customlib->YYYYMMDDTodateFormat($result['dob']);
        }else{
            $result['dob']                 ='';
        }

        if($result['as_of_date']){
            $result['as_of_date']          = "(".$this->lang->line('as_of_date').' '.$this->customlib->YYYYMMDDTodateFormat($result['as_of_date']).")";
        }else{
            $result['as_of_date']          = '';
        }        
        
        $result['custom_fields_value'] = display_custom_fields('patient', $id);
        $cutom_fields_data             = get_custom_table_values($id, 'patient');
        $result['field_data']          = $cutom_fields_data;   
        
        if(file_exists("./uploads/patient_id_card/barcodes/$id.png") ){
            $result['getbarcode']          = base_url("./uploads/patient_id_card/barcodes/$id.png") ;  
        }else{
            $result['getbarcode']          = null;
        }
        if(file_exists("./uploads/patient_id_card/qrcode/$id.png")){
            $result['getqrcode']           = base_url("./uploads/patient_id_card/qrcode/$id.png") ;
        }else{
            $result['getqrcode']          = null;
        }		
		
        if(!empty($result['image'])){        
            $result['image']          = $result['image'];
        }else{
			 $result['image']           = ("./uploads/patient_images/no_image.png") ;
        }
        $result['gender'] = $this->lang->line(strtolower($result['gender']));
        $result['marital_status'] = $this->lang->line(strtolower($result['marital_status']));
		
        echo json_encode($result);
    }

    
    public function patientDetails()
    {
        $id                    = $this->input->post("id");
        $result                = $this->patient_model->getpatientDetails($id);
        $result['gender'] = $this->lang->line(strtolower($result['gender']));
        $result['patient_age'] = $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']);
        $result['patient_name_formatted'] = composePatientName($result['patient_name'],$result['id']);
        if (($result['insurance_validity'] == '') || ($result['insurance_validity'] == '0000-00-00') || ($result['insurance_validity'] == '1970-01-01')) {
            $result['insurance_validity'] = "";
        } else {
            $result['insurance_validity'] = $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']);
        }

        if($result['dob']){
            $result['dob']                 = $this->customlib->YYYYMMDDTodateFormat($result['dob']);
        }else{
            $result['dob']                 = '';
        }

        if($result['as_of_date']){
            $result['as_of_date']                 = "(".$this->lang->line('as_of_date').' '.$this->customlib->YYYYMMDDTodateFormat($result['as_of_date']).")";
        }else{
            $result['as_of_date']                 = '';
        }
		
		
        $result['custom_fields_value'] = display_custom_fields('patient', $id);
        $cutom_fields_data             = get_custom_table_values($id, 'patient');
        $result['field_data']          = $cutom_fields_data;

        if(file_exists("./uploads/patient_id_card/barcodes/$id.png")){
            $getbarcode=base_url("./uploads/patient_id_card/barcodes/$id.png");
        }else{
            $getbarcode=null;
        }
        if(file_exists("./uploads/patient_id_card/qrcode/$id.png")){
            $getqrcode=base_url("./uploads/patient_id_card/qrcode/$id.png");
        }else{
            $getqrcode=null;
        }

        $result['getbarcode']          =       $getbarcode;
        $result['getqrcode']           =       $getqrcode;

        echo json_encode($result);
    }

    public function getIpdDetails()
    {
        if (!$this->rbac->hasPrivilege('ipd_patient', 'can_view')) {
            access_denied();
        }
        $ipdid                         = $this->input->post("ipdid");
        $result                        = $this->patient_model->getIpdDetails($ipdid);        
        $result['date']                = $this->customlib->YYYYMMDDHisTodateFormat($result['date'],$this->customlib->getHospitalTimeFormat());       
        
        if($result['insurance_validity']){
        $result['einsurance_validity'] = $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity'],$this->customlib->getHospitalTimeFormat());
        }else{
        $result['einsurance_validity'] = '';    
        }
        
        $result['age']                 = $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']);
        $result['custom_fields_value'] = display_custom_fields('ipd', $ipdid);
        $cutom_fields_data             = get_custom_table_values($ipdid, 'ipd');
        $result['field_data']          = $cutom_fields_data;
     
        echo json_encode($result);
    }

    public function getVisitDetailsbyopdid()
    {
        $opdid  = $this->input->post("opdid");
        $result = $this->patient_model->getVisitsByOPDid($opdid);
        echo json_encode($result);
    }

    public function getMedicationDoseDetails()
    {
        $medication_id        = $this->input->post("medication_id");
        $result               = $this->patient_model->getmedicationbyid($medication_id);
        $result['date']       = $this->customlib->YYYYMMDDTodateFormat($result['date']);
        $result['dosagetime'] = $this->customlib->getHospitalTime_Format($result['time']);
        echo json_encode($result);
    }

    public function getMedicineDoseDetails()
    {
        $medicine_category_id = $this->input->post("medicine_category_id");
        $result               = $this->patient_model->getMedicineDose($medicine_category_id);
        echo json_encode($result);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_edit')) {
            access_denied();
        }
        if (isset($_POST['age'])) {
            if (count(array_filter($_POST['age'])) == 0) {
                $this->form_validation->set_rules('age', $this->lang->line('age'), 'trim|required|xss_clean|');
            }
        }

        $patient_type  = $this->customlib->getPatienttype();
        $custom_fields = $this->customfield_model->getByBelong('patient');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];

                    $this->form_validation->set_rules("custom_fields[patient][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
                }
            }
        }
       
        if($this->input->post('organisation_id') != ""){           
            $this->form_validation->set_rules('insurance_id', $this->lang->line('tpa_id'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('validity', $this->lang->line('tpa_validity'), 'trim|required|xss_clean');
        }
		
        if($this->input->post('insurance_id') != ""){            
            $this->form_validation->set_rules('organisation_id', $this->lang->line('tpa'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('validity', $this->lang->line('tpa_validity'), 'trim|required|xss_clean');
        }
		
        if($this->input->post('validity') != ""){            
            $this->form_validation->set_rules('insurance_id', $this->lang->line('tpa_id'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('organisation_id', $this->lang->line('tpa'), 'trim|required|xss_clean');
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
        $this->form_validation->set_rules('age[year]', $this->lang->line('year'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('age[month]', $this->lang->line('month'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('age[day]', $this->lang->line('day'), 'trim|required|xss_clean|numeric');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'              => form_error('name'),
                'age'               => form_error('age'),
                'age[year]'         => form_error('age[year]'),
                'age[month]'        => form_error('age[month]'),
                'age[day]'          => form_error('age[day]'),
                'file'              => form_error('file'),
                'insurance_id'      => form_error('insurance_id'),
                'validity'          => form_error('validity'),
                'organisation_id'   => form_error('organisation_id'),                
            );
			
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                = $custom_fields_value['id'];
                        $custom_fields_name                                              = $custom_fields_value['name'];
                        $error_msg2["custom_fields[patient][" . $custom_fields_id . "]"] = form_error("custom_fields[patient][" . $custom_fields_id . "]");
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

            $validity = $this->input->post("validity");
            if (!empty($validity)) {
                $validity = $this->customlib->dateFormatToYYYYMMDD($validity);
            } else {
                $validity = null;
            }
			
			$organisation_id = $this->input->post("organisation_id");
            if (!empty($organisation_id)) {
                $organisation_id = $organisation_id;
            } else {
                $organisation_id = null;
            }

            $id                = $this->input->post('updateid');
            $dobdate           = $this->input->post('dob');
            $custom_field_post = $this->input->post("custom_fields[patient]");
            $dob               = $this->customlib->dateFormatToYYYYMMDD($dobdate);
            $blood_group       = $this->input->post('blood_group');
            $patient_data      = array(
                'id'                    => $this->input->post('updateid'),
                'patient_name'          => $this->input->post('name'),
                'mobileno'              => $this->input->post('contact'),
                'marital_status'        => $this->input->post('marital_status'),
                'email'                 => $this->input->post('email'),
                'dob'                   => $dob,
                'gender'                => $this->input->post('gender'),
                'guardian_name'         => $this->input->post('guardian_name'),
                'address'               => $this->input->post('address'),
                'note'                  => $this->input->post('note'),
                'age'                   => $this->input->post('age[year]'),
                'month'                 => $this->input->post('age[month]'),
                'day'                   => $this->input->post('age[day]'),
                'insurance_id'          => $this->input->post('insurance_id'),
                'identification_number' => $this->input->post('identification_number'),
                'insurance_validity'    => $this->customlib->dateFormatToYYYYMMDD($this->input->post('validity')),
                'organisation_id'       => $organisation_id,
                'known_allergies'       => $this->input->post('known_allergies'), 
                'insurance_validity'    => $validity,
            );

            if ($blood_group != "") {
                $patient_data['blood_bank_product_id'] = $this->input->post('blood_group');
            }

            $this->patient_model->add($patient_data);
            // String of all alphanumeric character
            $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            // Shufle the $str_result and returns substring
            // of specified length
            $alfa_no = substr(str_shuffle($str_result), 0, 5);
            $array   = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));

            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[patient][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'patient');
            }

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $alfa_no . "_" . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }
        }

        echo json_encode($array);
    }

    public function deactivePatient()
    {
        $id = $this->input->post('id');

        $patient_data = array(
            'id'        => $id,
            'is_active' => 'no',
        );
        $chekpatient = $this->patient_model->checkpatientipddis($id);

        if ($chekpatient) {
            $msg = $this->lang->line('patient_already_in_ipd');
            $sts = 'fail';
        } else {
            $this->patient_model->add($patient_data);
            $this->user_model->updateUser($id, 'no');
            $sts = 'success';
            $msg = $this->lang->line('record_deactivate');
        }

        $array = array('status' => $sts, 'error' => '', 'message' => $msg);
        echo json_encode($array);
    }

    public function activePatient()
    {        
        $id = $this->input->post('activeid');

        $patientact_data = array(
            'id'        => $id,
            'is_active' => 'yes',
        );

        $this->patient_model->add_patient($patientact_data);
        $this->user_model->updateUser($id, 'yes');
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_active'));
        echo json_encode($array);
    }

    public function addipddoctor()
    {
        $this->form_validation->set_rules('doctorOpt[]', $this->lang->line('doctor_opt'), 'trim|required|xss_clean',
            array('required' => $this->lang->line('please_select_atleast_one_doctor')));

        if ($this->form_validation->run() == false) {
            $msg = array(
                'doctorOpt[]' => form_error('doctorOpt[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $doctors = $this->input->post('doctorOpt[]');
            $ipdid   = $this->input->post('ipdid_doctor');
            $i       = 0;
            foreach ($doctors as $key => $value) {
                $doctors_id = $doctors[$i];

                $data         = array('ipd_id' => $ipdid, 'consult_doctor' => $doctors_id);
                $data_array[] = $data;
                $i++;
            }
            $this->patient_model->delete_ipddoctor($ipdid);
            $this->patient_model->add_ipddoctor($data_array);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));

        }
        echo json_encode($array);
    }

    public function ipd_update()
    {
        if (!$this->rbac->hasPrivilege('ipd_patient', 'can_edit')) {
            access_denied();
        }
        $patient_type  = $this->customlib->getPatienttype();
        $custom_fields = $this->customfield_model->getByBelong('ipd');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];

                    $this->form_validation->set_rules("custom_fields[ipd][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
                }
            }
        }

        $this->form_validation->set_rules('cons_doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('admission_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('bed_no', $this->lang->line('bed_no'), 'trim|required');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patients_id'      => form_error('patients_id'),
                'cons_doctor'      => form_error('cons_doctor'),
                'appointment_date' => form_error('appointment_date'),
                'file'             => form_error('file'),
                'bed_no'           => form_error('bed_no'),
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
            $id                = $this->input->post('updateid');
            $appointment_date  = $this->input->post('appointment_date');
            $patientid         = $this->input->post('patient_id');
            $previous_bed_id   = $this->input->post('previous_bed_id');
            $current_bed_id    = $this->input->post('bed_no');
            $ipdid             = $this->input->post('ipdid');
            $case_reference_id = $this->patient_model->getReferenceByIpdId($ipdid);
            if ($previous_bed_id != $current_bed_id) {
                $beddata = array('id' => $previous_bed_id, 'is_active' => 'yes');
                $this->bed_model->savebed($beddata);
                $bed_history = array(
                    "case_reference_id" => $case_reference_id,
                    "to_date"           => date("Y-m-d H:i:s"),
                    "is_active"         => "no",
                );
                $this->bed_model->updateBedHistory($bed_history);               
            }
            $ipd_data = array(
                'id'              => $ipdid,
                'patient_id'      => $patientid,
                'date'            => $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format),
                'bed'             => $this->input->post('bed_no'),
                'bed_group_id'    => $this->input->post('bed_group_id'),                
                'case_type'       => $this->input->post('case_type'),
                'symptoms'        => $this->input->post('symptoms'),
                'known_allergies' => $this->input->post('known_allergies'),
                'patient_old'     => $this->input->post('old_patient'),
                'refference'      => $this->input->post('refference'),
                'cons_doctor'     => $this->input->post('cons_doctor'),
                'casualty'        => $this->input->post('casualty'),
                'note'            => $this->input->post('note'),
                'credit_limit'    => $this->input->post('credit_limit'),
            );
            $bed_data = array('id' => $this->input->post('bed_no'), 'is_active' => 'no');
            $this->bed_model->savebed($bed_data);           
           
            $ipd_id = $this->patient_model->add_ipd($ipd_data);
          
            $is_antenatal = 0;
            if($this->input->post('is_for_antenatal')){
                $is_antenatal = 1;
            }
            
            $update_parient_antenatal = array(
                    'id'      => $patientid,
                    'is_antenatal'      => $is_antenatal,                    
                );
            $this->patient_model->add($update_parient_antenatal);

             if ($previous_bed_id != $current_bed_id) {
                $bed_history = array(
                    "case_reference_id" => $case_reference_id,
                    "bed_group_id"      => $this->input->post("bed_group_id"),
                    "bed_id"            => $this->input->post("bed_no"),
                    "from_date"         => date("Y-m-d H:i:s"),
                    "is_active"         => "yes",
                );
                $this->bed_model->saveBedHistory($bed_history);
            }

            $custom_field_post  = $this->input->post("custom_fields[ipd]");
            $custom_value_array = array();
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[ipd][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $ipdid,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $ipdid, 'ipd');
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('patient_updated_successfully'));
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }
        }
        echo json_encode($array);
    }

    public function add_discharged_summary()
    {
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient_name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id' => form_error('patients_id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $patientid  = $this->input->post('patient_id');
            $updated_id = $this->input->post('updateid');
            $ipd_id     = $this->input->post('ipdid');
            if (!empty($updated_id)) {
                $summary_dataupdate = array(
                    'id'             => $updated_id,
                    'ipd_id'         => $ipd_id,
                    'patient_id'     => $patientid,
                    'note'           => $this->input->post('note'),
                    'diagnosis'      => $this->input->post('diagnosis'),
                    'operation'      => $this->input->post('operation'),
                    'investigations' => $this->input->post('investigations'),
                    'treatment_home' => $this->input->post('treatment_at_home'),
                );
                $summary_id = $this->patient_model->add_disch_summary($summary_dataupdate);
            } else {
                $summary_data = array(
                    'ipd_id'         => $ipd_id,
                    'patient_id'     => $patientid,
                    'note'           => $this->input->post('note'),
                    'diagnosis'      => $this->input->post('diagnosis'),
                    'operation'      => $this->input->post('operation'),
                    'investigations' => $this->input->post('investigations'),
                    'treatment_home' => $this->input->post('treatment_at_home'),
                );
                $summary_id = $this->patient_model->add_disch_summary($summary_data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('patient_updated_successfully'));
        }
        echo json_encode($array);
    }

    public function add_opddischarged_summary()
    {
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient_name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id' => form_error('patient_id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $patientid  = $this->input->post('patient_id');
            $updated_id = $this->input->post('updateid');
            $opd_id     = $this->input->post('opdid');

            if (!empty($updated_id)) {
                $summary_dataupdate = array(
                    'id'             => $updated_id,
                    'opd_details_id' => $opd_id,
                    'patient_id'     => $patientid,
                    'note'           => $this->input->post('note'),
                    'diagnosis'      => $this->input->post('diagnosis'),
                    'operation'      => $this->input->post('operation'),
                    'investigations' => $this->input->post('investigations'),
                    'treatment_home' => $this->input->post('treatment_at_home'),
                );
                $summary_id = $this->patient_model->add_dischopd_summary($summary_dataupdate);
            } else {
                $summary_data = array(
                    'opd_details_id' => $opd_id,
                    'patient_id'     => $patientid,
                    'note'           => $this->input->post('note'),
                    'diagnosis'      => $this->input->post('diagnosis'),
                    'operation'      => $this->input->post('operation'),
                    'investigations' => $this->input->post('investigations'),
                    'treatment_home' => $this->input->post('treatment_at_home'),
                );

                $summary_id = $this->patient_model->add_dischopd_summary($summary_data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('patient_updated_successfully'));
        }
        echo json_encode($array);
    }

    public function opd_detail_update()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_edit')) {
            access_denied();
        }
        $custom_fields     = $this->customfield_model->getByBelong('opd');
        $visit_custom_fields     = $this->customfield_model->getByBelong('opdrecheckup');
        $custom_field_post = $this->input->post("custom_fields[opd]");
        $visit_custom_field_post = $this->input->post("custom_fields[opdrecheckup]");
        $type              = $this->input->post("type");
        $set_fields        = "custom_fields[opd]";
        $set_visit_fields  = "custom_fields[opdrecheckup]";
        $insert_id         = $this->input->post('opdid');
        $table_value       = "opd";

        if($type =='opd'){
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id   = $custom_fields_value['id'];
                        $custom_fields_name = $custom_fields_value['name'];
                        $this->form_validation->set_rules($set_fields . "[" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');

                    }
                }
            }
        }
        if($type =='visit'){
             if (!empty($visit_custom_fields)) {
                foreach ($visit_custom_fields as $visit_custom_fields_key => $visit_custom_fields_value) {
                    if ($visit_custom_fields_value['validation']) {
                        $custom_fields_id   = $visit_custom_fields_value['id'];
                        $custom_fields_name = $visit_custom_fields_value['name'];
                        $this->form_validation->set_rules($set_visit_fields . "[" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');

                    }
                }
            }
        }  

        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');

        //for transaction data update
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean|valid_amount');
        $this->form_validation->set_rules('payment_date', $this->lang->line('payment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'trim|required|xss_clean');

        if ($_POST['payment_mode'] == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line("document"), 'callback_handle_doc_upload[document]');
        }

        if ($this->form_validation->run() == true) {
           
            $appointment_date = $this->input->post('appointment_date');
            $visitid          = $this->input->post("visitid");
            $patient_id       = $this->input->post("patient_id");          

            $visit_data = array(
                'id'               => $visitid,
                'appointment_date' => $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format),
                'case_type'        => $this->input->post('case'),
                'cons_doctor'      => $this->input->post('consultant_doctor'),
                'symptoms'         => $this->input->post('symptoms'),                
                'casualty'         => $this->input->post('casualty'),
                'patient_old'      => $this->input->post('old_patient'),
                'refference'       => $this->input->post('refference'),
                'note'             => $this->input->post('revisit_note'),
                'known_allergies'  => $this->input->post('known_allergies'), 
            );
            
            $visit_data['is_antenatal'] = 0;
           
            if($this->input->post('is_for_antenatal') != "" ){
                $visit_data['is_antenatal'] = 1;
            }

            $payment_date    = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post("payment_date"), $this->time_format);
            $amount          = $this->input->post('amount');
            $transaction_id  = $this->input->post('visit_transaction_id');

            $trasaction_data['payment_mode'] = $this->input->post('payment_mode');
            $trasaction_data['amount']  = $amount ;
            $trasaction_data['id']      = $this->input->post('visit_transaction_id') ;
            $trasaction_data['payment_date'] = $payment_date ;
            $trasaction_data['note'] = $this->input->post('note');          
            $this->transaction_model->add($trasaction_data);
            $attachment      = "";
            $attachment_name = "";

            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }
			
            if ($this->input->post('payment_mode') == "Cheque") {
                $cheque_date     = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date"));
                $data['id']              = $transaction_id;
                $data['cheque_date']     = $cheque_date;
                $data['cheque_no']       = $this->input->post('cheque_no');
                $data['attachment']      = $attachment;
                $data['attachment_name'] = $attachment_name;
                $this->transaction_model->add($data);
            }

            $opd_id = $this->patient_model->add_visit_recheckup($visit_data, $trasaction_data, array());

            if($type =='opd'){

                    if (!empty($custom_fields)) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[opd][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $insert_id,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                    $this->customfield_model->updateRecord($custom_value_array, $insert_id, 'opd');
                }
            }
            
            //for update vsit custom fields
           
            if($type=='visit'){           
                if (!empty($visit_custom_fields)) {
                    foreach ($visit_custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[opdrecheckup][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $visitid,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $visit_custom_value_array[] = $array_custom;
                    }

                    $this->customfield_model->updateRecord($visit_custom_value_array, $visitid, 'opdrecheckup');
               }
            }
            
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        } else {

            $msg = array(
                'appointment_date'  => form_error('appointment_date'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'edit_payment'      =>form_error('edit_payment'),
                'payment_mode'      => form_error('payment_mode'),
                'payment_date'      => form_error('payment_date'),
                'cheque_date'       => form_error('cheque_date'),
                'cheque_no'         => form_error('cheque_no'),
                'document'          => form_error('document'),
                'amount'          => form_error('amount'),
            );

            if($type=="opd"){
                if (!empty($custom_fields)) {
                    foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                        if ($custom_fields_value['validation']) {
                            $custom_fields_id                                        = $custom_fields_value['id'];
                            $custom_fields_name                                      = $custom_fields_value['name'];
                            $error_msg2[$set_fields . "[" . $custom_fields_id . "]"] = form_error($set_fields . "[" . $custom_fields_id . "]");
                        }
                    }
                }
                if (!empty($error_msg2)) {
                    $error_msg = array_merge($msg, $error_msg2);
                } else {
                    $error_msg = $msg;
                }
            }

            //for visit custom fields

            if($type=="visit"){

                    if (!empty($visit_custom_fields)) {
                    foreach ($visit_custom_fields as $visit_custom_fields_key => $visit_custom_fields_value) {
                        if ($visit_custom_fields_value['validation']) {
                            $custom_fields_id                                        = $visit_custom_fields_value['id'];
                            $custom_fields_name                                      = $visit_custom_fields_value['name'];
                            $error_msg3[$set_visit_fields . "[" . $custom_fields_id . "]"] = form_error($set_visit_fields . "[" . $custom_fields_id . "]");
                        }
                    }
                }
                if (!empty($error_msg3)) {
                    $error_msg = array_merge($msg, $error_msg3);
                } else {
                    $error_msg = $msg;
                }
            }
			
            $array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');
        }
        echo json_encode($array);
    }

    public function opd_details()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }

        $visitid = $this->input->post("visitid");
        $result  = $this->patient_model->getOPDetails($visitid);

        if (!empty($result['appointment_date'])) {
            $appointment_date           = $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date']);
            $result["appointment_date"] = $appointment_date;
        }
        $result['custom_fields_value'] = display_custom_fields('opd', $visitid);

        echo json_encode($result);
    }

    public function getopdvisitdetails()
    {
        $visitid = $this->input->get("visitid");
        if ((!empty($visitid))) {
            $result                        = $this->patient_model->getopdvisitDetailsbyvisitid($visitid);
           $result['custom_fields_value'] = display_custom_fields('opd', $result['opdid']);
        }
        if (!empty($result['appointment_date'])) {
            $appointment_date           = $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date']);
            $result["appointment_date"] = $appointment_date;
        }
        if ($result['cheque_date']!="") {
            $result['cheque_date']  = $this->customlib->YYYYMMDDTodateFormat($result['cheque_date']);
        }else{
            $result['cheque_date']="";
        }
        
        $result['payment_date']  = $this->customlib->YYYYMMDDHisTodateFormat($result['payment_date']);
 
        echo json_encode($result);
    }

    public function getvisitdetailsdata()
    {
        $visitid = $this->input->get("visitid");
        if ((!empty($visitid))) {
            $result                        = $this->patient_model->getopdvisitDetailsbyvisitid($visitid);
            $result['custom_fields_value'] = display_custom_fields('opdrecheckup', $visitid);
        }
        if (!empty($result['appointment_date'])) {
            $appointment_date           = $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date']);
            $result["appointment_date"] = $appointment_date;
        }
        if (!empty($result['cheque_date']!="")) {
            $result['cheque_date']  = $this->customlib->YYYYMMDDTodateFormat($result['cheque_date']);
        }
        
        $result['payment_date']  = $this->customlib->YYYYMMDDHisTodateFormat($result['payment_date']);
 
        echo json_encode($result);
    }

    public function editvisitdetails()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
        $id      = $this->input->post("recordid");
        $visitid = $this->input->post("visitid");
		
        if ((!empty($visitid))) {
            $result                        = $this->patient_model->getpatientDetailsByVisitId($id, $visitid);
            $result['custom_fields_value'] = display_custom_fields('opdrecheckup', $visitid);
        }

        if (!empty($result['appointment_date'])) {
            $appointment_date           = $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date'], $this->time_format);
            $result["appointment_date"] = $appointment_date;
        }

        echo json_encode($result);
    }

    public function editDiagnosis()
    {
        if (!$this->rbac->hasPrivilege('opd_diagnosis', 'can_edit')) {
            access_denied();
        }
        $id                    = $this->input->post("id");
        $result                = $this->patient_model->geteditDiagnosis($id);
        $result["report_date"] = $this->customlib->YYYYMMDDTodateFormat($result['report_date']);
        echo json_encode($result);
    }

    public function editTimeline()
    {
        if (!$this->rbac->hasPrivilege('ipd_timeline', 'can_edit')) {
            access_denied();
        }
        $id     = $this->input->post("id");
        $result = $this->timeline_model->geteditTimeline($id);
        $result['timeline_datetime'] = $this->customlib->YYYYMMDDHisTodateFormat($result['timeline_date'],$this->customlib->getHospitalTimeFormat(true,true));
        echo json_encode($result);
    }

    public function editNursenote()
    {
        if (!$this->rbac->hasPrivilege('nurse_note', 'can_edit')) {
            access_denied();
        }
        $id                            = $this->input->post("id");
        $result                        = $this->patient_model->getNursenote($id);
        $result['note_date']           = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('ipdnursenote', $id);
        echo json_encode($result);
    }

    public function editConsultantRegister()
    {
        if (!$this->rbac->hasPrivilege('consultant_register', 'can_edit')) {
            access_denied();
        }
        $id                            = $this->input->post("id");
        $result                        = $this->patient_model->getConsultantRegister($id);
        $result['ins_date']            = $this->customlib->YYYYMMDDTodateFormat($result['ins_date']);
        $result['date']                = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('ipdconsultinstruction', $id);
        echo json_encode($result);
    }

    public function editstaffTimeline()
    {
        if (!$this->rbac->hasPrivilege('staff_timeline', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post("id");
        $result = $this->timeline_model->geteditstaffTimeline($id);
        echo json_encode($result);
    }

    public function add_diagnosis()
    {
        $this->form_validation->set_rules('report_type', $this->lang->line('report_type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('report_date', $this->lang->line('report_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('report_document', $this->lang->line('image'), 'callback_handle_doc_upload[report_document]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'report_type'     => form_error('report_type'),
                'report_date'     => form_error('report_date'),
                'report_document' => form_error('report_document'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $reportdate = $this->input->post('report_date');

            $data = array(
                'report_type'   => $this->input->post("report_type"),
                'report_date'   => $this->customlib->dateFormatToYYYYMMDD($reportdate),
                'patient_id'    => $this->input->post("patient"),
                'report_center' => $this->input->post('report_center'),
                'description'   => $this->input->post("description"),
            );
            $insert_id = $this->patient_model->add_diagnosis($data);
            if (isset($_FILES["report_document"]) && !empty($_FILES['report_document']['name'])) {
                $fileInfo = pathinfo($_FILES["report_document"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["report_document"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'document' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add_diagnosis($data_img);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_added_successfully'));
        }
        echo json_encode($array);
    }

    public function update_diagnosis()
    {
        $this->form_validation->set_rules('report_type', $this->lang->line('report_type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('report_date', $this->lang->line('report_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('report_document', $this->lang->line('document'), 'callback_handle_doc_upload[report_document]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'report_type'     => form_error('report_type'),
                'report_date'     => form_error('report_date'),
                'report_document' => form_error('report_document'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $report_date = $this->input->post('report_date');
            $id          = $this->input->post('diagnosis_id');
            $patientid   = $this->input->post("diagnosispatient_id");
            $this->load->library('Customlib');
            $data = array(
                'id'            => $id,
                'report_type'   => $this->input->post("report_type"),
                'report_date'   => $this->customlib->dateFormatToYYYYMMDD($report_date),
                'patient_id'    => $patientid,
                'report_center' => $this->input->post("report_center"),
                'description'   => $this->input->post("description"),
            );
            $insert_id = $this->patient_model->add_diagnosis($data);
            if (isset($_FILES["report_document"]) && !empty($_FILES['report_document']['name'])) {
                $fileInfo = pathinfo($_FILES["report_document"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["report_document"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $id, 'document' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add_diagnosis($data_img);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_added_successfully'));
        }
        echo json_encode($array);
    }

    public function add_prescription()
    {
        if (!$this->rbac->hasPrivilege('ipd_prescription', 'can_add')) {
            access_denied();
        }
        $total_rows = $this->input->post('rows');
        $pathology  = $this->input->post('pathology');
        $radiology  = $this->input->post('radiology');
        if (!isset($total_rows) && !isset($pathology) && !isset($radiology)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('please_select_any_one')));
        }

        $this->form_validation->set_rules('ipd_no', $this->lang->line('ipd'), 'trim|required|xss_clean');

        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_key => $row_value) {

                $medicine_category = $this->input->post('medicine_cat_' . $row_value);
                $medicine_name     = $this->input->post('medicine_' . $row_value);
                $dosage            = $this->input->post('dosage_' . $row_value);

                if ($medicine_category == "") {
                    $this->form_validation->set_rules('medicine_category', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
                }
                if ($medicine_name == "") {
                    $this->form_validation->set_rules('medicine_name', $this->lang->line('medicine'), 'trim|required|xss_clean');
                }
                if ($dosage == "") {
                    $this->form_validation->set_rules('dosage', $this->lang->line('dosage'), 'required');
                }
            }
        }

        if ($this->form_validation->run() == false) {

            $msg = array(
                'no_records'        => form_error('no_records'),
                'medicine_category' => form_error('medicine_category'),
                'medicine_name'     => form_error('medicine_name'),
                'dosage'            => form_error('dosage'),
                'ipd_no'            => form_error('ipd_no'),

            );
            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {

            $pathology = $this->input->post('pathology');
            $radiology = $this->input->post('radiology');
            if (isset($pathology)) {

            } else {
                $pathology = array();
            }

            if (isset($radiology)) {

            } else {
                $radiology = array();
            }

            $total_rows = $this->input->post('rows');
            $medicines  = array();
            foreach ($total_rows as $row_key => $row_value) {
                $medicines[] = array(
                    'basic_id'    => 0,
                    'pharmacy_id' => $this->input->post("medicine_" . $row_value),
                    'dosage'      => $this->input->post("dosage_" . $row_value),
                    'instruction' => $this->input->post("instruction_" . $row_value));
            }

            $ipd_id          = $this->input->post('ipd_no');
            $header_note     = $this->input->post("header_note");
            $footer_note     = $this->input->post("footer_note");
            $ipd_no_value    = $this->input->post('ipd_no_value');
            $ipd_basic_array = array('ipd_id' => $ipd_id, 'header_note' => $header_note, 'footer_note' => $footer_note, 'date' => date("Y-m-d"));
            $basic_id        = $this->prescription_model->add_ipdprescription($ipd_basic_array, $medicines, $pathology, $radiology);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function add_ipdprescription()
    {
        $total_rows = $this->input->post('rows');
        $pathology  = $this->input->post('pathology');
        $radiology  = $this->input->post('radiology');
        $custom_fields = $this->customfield_model->getByBelong('prescription');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[prescription][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $this->form_validation->set_rules('ipd_id', $this->lang->line('ipd'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('prescribe_by', $this->lang->line('prescribe_by'), 'trim|required|xss_clean');
        
        $medicine=0;
        if (isset($total_rows) && !empty($total_rows)) {

            foreach ($total_rows as $row_key => $row_value) {
                $medicine_category = $this->input->post('medicine_cat_' . $row_value);
                $medicine_name     = $this->input->post('medicine_' . $row_value);
                $dosage            = $this->input->post('dosage_' . $row_value);

                if ($medicine_category != "") {

                    $medicine=1;

                    if ($medicine_name == "") {
                        $this->form_validation->set_rules('medicine_name', $this->lang->line('medicine'), 'trim|required|xss_clean');
                    }

                    if ($dosage == "") {
                        $this->form_validation->set_rules('dosage', $this->lang->line('dosage'), 'required');
                    }
                }
            }
        }

        if ($medicine==0 && !isset($pathology) && !isset($radiology)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('please_select_any_one')));
        }

        if ($this->form_validation->run() == false) {

            $msg = array(
                'no_records'        => form_error('no_records'),
                'medicine_category' => form_error('medicine_category'),
                'medicine_name'     => form_error('medicine_name'),
                'dosage'            => form_error('dosage'),
                'prescribe_by'      => form_error('prescribe_by'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                            = $custom_fields_value['id'];
                        $custom_fields_name                                          = $custom_fields_value['name'];
                        $error_msg2["custom_fields[prescription][" . $custom_fields_id . "]"] = form_error("custom_fields[prescription][" . $custom_fields_id . "]");
                    }
                }
            }

            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }

            $array = array('status' => '0', 'error' => $error_msg, 'message' => '');

        } else {

            $action                    	= 	$this->input->post('action');
            $ipd_prescription_basic_id 	= 	$this->input->post('ipd_prescription_basic_id');
            $prev_prescription_ids		=	array();   
            $_post_prev_medicine		=	$this->input->post('prev_medicine');
           
            $prev_medicine = '';
            if($_post_prev_medicine){
                 $prev_medicine= $_post_prev_medicine;
            }

            $prev_pathology = $this->input->post('prev_pathology');
            $prev_radiology = $this->input->post('prev_radiology');

            if (!isset($prev_pathology)) {
                $prev_pathology = array();
            }

            if (!isset($prev_radiology)) {
                $prev_radiology = array();
                # code...
            }

            $pathology = $this->input->post('pathology');
            $radiology = $this->input->post('radiology');

            if (!isset($pathology)) {
                $pathology = array();
            }

            if (!isset($radiology)) {
                $radiology = array();
            }

            $total_rows               = $this->input->post('rows');
            $insert_medicines         = array();
            $update_medicines         = array();
            $not_be_deleted_medicines = array();
            if ($medicine==1) {
              
                foreach ($total_rows as $row_key => $row_value) {
                    $ipd_prescription_detail_id = $this->input->post("ipd_prescription_detail_id_" . $row_value);
                    if (isset($ipd_prescription_detail_id)) {
                        $not_be_deleted_medicines[] = $ipd_prescription_detail_id;
                        $update_medicines[]         = array(
                            'id'               => $ipd_prescription_detail_id,
                            'pharmacy_id'      => $this->input->post("medicine_" . $row_value),
                            'dosage'           => $this->input->post("dosage_" . $row_value),
                            'dose_duration_id' => $this->input->post("duration_dosage_" . $row_value),
                            'dose_interval_id' => $this->input->post("interval_dosage_" . $row_value),
                            'instruction'      => $this->input->post("instruction_" . $row_value),
                        );
                    } else {
                        $insert_medicines[] = array(
                            'basic_id'         => null,
                            'pharmacy_id'      => $this->input->post("medicine_" . $row_value),
                            'dosage'           => $this->input->post("dosage_" . $row_value),
                            'dose_duration_id' => $this->input->post("duration_dosage_" . $row_value),
                            'dose_interval_id' => $this->input->post("interval_dosage_" . $row_value),
                            'instruction'      => $this->input->post("instruction_" . $row_value),
                        );
                    }
                }
            }

            $ipd_id              = $this->input->post('ipd_id');
            $header_note         = $this->input->post("header_note");
            $footer_note         = $this->input->post("footer_note");
            $ipd_no_value        = $this->input->post('ipd_no_value');
            $finding_description = $this->input->post('finding_description');
            $finding_print       = $this->input->post('finding_print');
            $ipd_basic_array     = array(
                'ipd_id'              => $ipd_id,
                'header_note'         => $header_note,
                'footer_note'         => $footer_note,
                'finding_description' => $finding_description,
                'is_finding_print'    => $finding_print,
                'date'                => date("Y-m-d"),
                'generated_by'        => $this->customlib->getStaffID(),
                'prescribe_by'        => $this->input->post('prescribe_by'),
            );

            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/prescription_document/" . $attachment);
                $ipd_basic_array['attachment']      = $attachment;
                $ipd_basic_array['attachment_name'] = $attachment_name;
            }

            $custom_field_post  = $this->input->post("custom_fields[prescription]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[prescription][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            if ($ipd_prescription_basic_id > 0) {
                $ipd_basic_array['id'] = $ipd_prescription_basic_id;

                if (!empty($custom_value_array)) {
                    $this->customfield_model->updateRecord($custom_value_array, $ipd_basic_array['id'], '');
                }
            }

            $delete_pathology = array_diff($prev_pathology, $pathology);
            $delete_radiology = array_diff($prev_radiology, $radiology);
            $insert_pathology = array_diff($pathology, $prev_pathology);
            $insert_radiology = array_diff($radiology, $prev_radiology);
        
            if($prev_medicine){
                $_medicines_delete = array_diff($prev_medicine, $not_be_deleted_medicines);
            }else{
                $_medicines_delete = '';
            }

            $basic_id       = $this->prescription_model->add_ipdprescription($ipd_basic_array, $insert_medicines, $update_medicines, $_medicines_delete, $insert_pathology, $insert_radiology, $delete_pathology, $delete_radiology, $ipd_prescription_basic_id);
            $patient_record = $this->patient_model->get_patientidbyIpdId($ipd_id);

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $basic_id);
            }

            $visible_module = $this->input->post('visible');
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'ipd_prescription_basic_id' => $basic_id);

            $doctor_list          = $this->patient_model->getDoctorsipd($ipd_id);
            $prescription_details = $this->prescription_model->getPrescriptionByTable($basic_id, 'ipd_prescription');

            $medicines_array = array();
            $radiology_array = array();
            $pathology_array = array();

            foreach ($prescription_details->medicines as $medicines_value) {
                $medicines_list    = $medicines_value->medicine_name;
                $medicines_array[] = $medicines_list;
            }

            $medicines_array = implode(',', $medicines_array);

            foreach ($insert_pathology as $insert_pathology_value) {
                $pathology_list    = $this->notificationsetting_model->getpathologyDetails($insert_pathology_value);
                $pathology_array[] = $pathology_list['test_name'] . ' (' . $pathology_list['short_name'] . ')';
            }

            $pathology_array = implode(',', $pathology_array);

            foreach ($insert_radiology as $insert_radiology_value) {
                $radiology_list    = $this->notificationsetting_model->getradiologyDetails($insert_radiology_value);
                $radiology_array[] = $radiology_list['test_name'] . ' (' . $radiology_list['short_name'] . ')';
            }
			
            $radiology_array = implode(',', $radiology_array);

            $prescribe_by_details = $this->notificationsetting_model->getstaffDetails($this->input->post('prescribe_by'));
            $generated_by_details = $this->notificationsetting_model->getstaffDetails($this->customlib->getStaffID());

            $staff_role_list_array = array();
            $notification_to_array = array();

            $notification_to = $this->input->post('visible');

            if(!empty($notification_to)){
                
                foreach ($notification_to as $notification_to_value) {
                $staff_role_list         = $this->notificationsetting_model->getstaffDetailsByrole($notification_to_value);
                $staff_role_list_array[] = $staff_role_list;
                }

                if (!empty($staff_role_list_array)) {
                    foreach ($staff_role_list_array as $staff_role_list_array_value) {
                        foreach ($staff_role_list_array_value as $staff_value) {
                            $staff_list              = $this->notificationsetting_model->getstaffDetails($staff_value['id']);
                            $notification_to_array[] = $staff_list;
                        }
                    }
                }
            }            

            $consultant_doctor        = $this->patient_model->get_patientidbyIpdId($ipd_id);
            $consultant_doctorarray[] = array('consult_doctor' => $consultant_doctor['cons_doctor'], 'role_id' => $consultant_doctor['role_id'], 'name' => composeStaffNameByString($consultant_doctor['doctor_name'], $consultant_doctor['doctor_surname'], $consultant_doctor['doctor_employee_id']));

            if (!empty($doctor_list)) {
                foreach ($doctor_list as $key => $value) {
                    $consultant_doctorarray[] = array('consult_doctor' => $value['consult_doctor'], 'role_id' => $value['role_id'], 'name' => composeStaffNameByString($value['ipd_doctorname'], $value['ipd_doctorsurname'], $value['employee_id']));
                }
            }

            if (!empty($notification_to_array)) {
                foreach ($notification_to_array as $key => $value) {
                    $consultant_doctorarray[] = array('consult_doctor' => $value['id'], 'role_id' => $value['role_id'], 'name' => composeStaffNameByString($value['name'], $value['surname'], $value['employee_id']));
                }
            }

            $event_data = array(
                'patient_id'          => $prescription_details->patient_id,
                'ipd_no'              => $this->customlib->getSessionPrefixByType('ipd_no') . $ipd_id,
                'prescription_no'     => $this->customlib->getSessionPrefixByType('ipd_prescription') . $basic_id,
                'finding_description' => $prescription_details->finding_description,
                'medicine'            => $medicines_array,
                'radilogy_test'       => $radiology_array,
                'pathology_test'      => $pathology_array,
                'priscribe_by'        => composeStaffNameByString($prescribe_by_details['name'], $prescribe_by_details['surname'], $prescribe_by_details['employee_id']),
                'generated_by'        => composeStaffNameByString($generated_by_details['name'], $generated_by_details['surname'], $generated_by_details['employee_id']),
            );

            $this->system_notification->send_system_notification('notification_ipd_prescription_created', $event_data, $consultant_doctorarray);
        }

        echo json_encode($array);
    }

    public function printbill()
    { 
        $opd_id                   = $this->input->post('opd_id');
        $opddata                  = $this->patient_model->getVisitDetailsbyopdid($opd_id,true);
      
        $data['blood_group_name'] = $opddata['blood_group_name'];
        $data["print_details"]    = $this->printing_model->get('', 'opd');
        $data["result"]           = $opddata;
        $data['opd_prefix']       = $this->customlib->getSessionPrefixByType('opd_no');
        $data['checkup_prefix']   = $this->customlib->getSessionPrefixByType('checkup_id');
        if (!empty($opddata)) {
            $patient_charge_id = $opddata['patient_charge_id'];
            $charge            = $this->charge_model->getChargeById($patient_charge_id);
            $data['charge']    = $charge;
            if (!empty($opddata['transaction_id'])) {
                $transaction         = $this->transaction_model->getTransaction($opddata['transaction_id']);
                $data['transaction'] = $transaction;
            }
        }
      
        $data['fields']    = $this->customfield_model->get_custom_fields('opd','',true,'', '');        
        $result["appointment_date"]                = $this->customlib->YYYYMMDDHisTodateFormat($opddata['appointment_date'], $this->time_format);
        
        $page = $this->load->view('admin/patient/_printbill', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));

    }

    public function add_opd_prescription()
    {
        $medicine_name_array = array();
        $radiology_test_name = array();
        $pathology_test_name = array();
        $total_rows          = $this->input->post('rows');
        $pathology           = $this->input->post('pathology');
        $radiology           = $this->input->post('radiology');
        $custom_fields = $this->customfield_model->getByBelong('prescription');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[prescription][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $this->form_validation->set_rules('visit_details_id', $this->lang->line("visit_details_id"), 'trim|required|xss_clean');
        $medicine=0;
        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_key => $row_value) {
                $medicine_category = $this->input->post('medicine_cat_' . $row_value);
                $medicine_name     = $this->input->post('medicine_' . $row_value);
                $dosage            = $this->input->post('dosage_' . $row_value);
                
                if ($medicine_category !== "") {
                    $medicine=1;

                    if ($medicine_name == "") {
                        $this->form_validation->set_rules('medicine_name', $this->lang->line('medicine'), 'trim|required|xss_clean');
                    }

                    if ($dosage == "") {
                        $this->form_validation->set_rules('dosage', $this->lang->line('dosage'), 'required');
                    }
                }
            }
        }

        if (($medicine==0) && !isset($pathology) && !isset($radiology)) {
            $this->form_validation->set_rules('no_records', $this->lang->line("no_records"), 'trim|required|xss_clean',
                array('required' => $this->lang->line("please_select_any_one")));
        }

        if ($this->form_validation->run() == false) {

            $msg = array(
                'no_records'        => form_error('no_records'),
                'medicine_category' => form_error('medicine_category'),
                'medicine_name'     => form_error('medicine_name'),
                'dosage'            => form_error('dosage'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                            = $custom_fields_value['id'];
                        $custom_fields_name                                          = $custom_fields_value['name'];
                        $error_msg2["custom_fields[prescription][" . $custom_fields_id . "]"] = form_error("custom_fields[prescription][" . $custom_fields_id . "]");
                    }
                }
            }

            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }
            $array = array('status' => 0, 'error' => $error_msg, 'message' => '');
        } else {

            $action                    = $this->input->post('action');
            $ipd_prescription_basic_id = $this->input->post('ipd_prescription_basic_id');
            $prev_prescription_ids=array();
   
            $_post_prev_medicine=$this->input->post('prev_medicine');
            if($_post_prev_medicine){
                 $prev_medicine= $_post_prev_medicine;
            }
            $prev_pathology = $this->input->post('prev_pathology');
            $prev_radiology = $this->input->post('prev_radiology');
            if (!isset($prev_pathology)) {
                $prev_pathology = array();
            }

            if (!isset($prev_radiology)) {
                $prev_radiology = array();
            }
			
            $pathology = $this->input->post('pathology');
            $radiology = $this->input->post('radiology');
            if (!isset($pathology)) {
                $pathology = array();
            }
			
            if (!isset($radiology)) {
                $radiology = array();
            }
            $total_rows               = $this->input->post('rows');
            $insert_medicines         = array();
            $update_medicines         = array();
            $not_be_deleted_medicines = array();
            if ($medicine==1) {
                foreach ($total_rows as $row_key => $row_value) {
                    $ipd_prescription_detail_id = $this->input->post("ipd_prescription_detail_id_" . $row_value);
                    if (isset($ipd_prescription_detail_id)) {
                        $not_be_deleted_medicines[] = $ipd_prescription_detail_id;
                        $update_medicines[]         = array(
                            'id'               => $ipd_prescription_detail_id,
                            'pharmacy_id'      => $this->input->post("medicine_" . $row_value),
                            'dosage'           => $this->input->post("dosage_" . $row_value),
                            'dose_interval_id' => $this->input->post("interval_dosage_" . $row_value),
                            'dose_duration_id' => $this->input->post("duration_dosage_" . $row_value),
                            'instruction'      => $this->input->post("instruction_" . $row_value),
                        );
                    } else {
                        $insert_medicines[] = array(
                            'basic_id'         => 0,
                            'pharmacy_id'      => $this->input->post("medicine_" . $row_value),
                            'dosage'           => $this->input->post("dosage_" . $row_value),
                            'dose_interval_id' => $this->input->post("interval_dosage_" . $row_value),
                            'dose_duration_id' => $this->input->post("duration_dosage_" . $row_value),
                            'instruction'      => $this->input->post("instruction_" . $row_value),
                        );
                        $medicine_data         = $this->notificationsetting_model->getmedicineDetails($this->input->post("medicine_" . $row_value));
                        $medicine_name_array[] = $medicine_data['medicine_name'];
                    }
                }
            }

            $visitid             = $this->input->post('visit_details_id');
            $header_note         = $this->input->post("header_note");
            $footer_note         = $this->input->post("footer_note");
            $ipd_no_value        = $this->input->post('ipd_no_value');
            $finding_description = $this->input->post('finding_description');
            $finding_print       = $this->input->post('finding_print');
            $opd_details         = $this->patient_model->get_patientidbyvisitid($visitid);
            
            $opd_basic_array = array(
                'visit_details_id'    => $visitid,
                'header_note'         => $header_note,
                'footer_note'         => $footer_note,
                'finding_description' => $finding_description,
                'is_finding_print'    => $finding_print,
                'date'                => date("Y-m-d"),
                'generated_by'        => $this->customlib->getStaffID(),
                'prescribe_by'        => $opd_details['doctor_id'],
            );

            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/prescription_document/" . $attachment);
                $opd_basic_array['attachment']      = $attachment;
                $opd_basic_array['attachment_name'] = $attachment_name;
            }

            $custom_field_post  = $this->input->post("custom_fields[prescription]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[prescription][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            if ($ipd_prescription_basic_id > 0) {
                $opd_basic_array['id'] = $ipd_prescription_basic_id;
                if (!empty($custom_value_array)) {
                    $this->customfield_model->updateRecord($custom_value_array, $opd_basic_array['id'], '');
                }
            }
            
            $_medicines_delete = array();
            $delete_pathology = array_diff($prev_pathology, $pathology);
            $delete_radiology = array_diff($prev_radiology, $radiology);
            $insert_pathology = array_diff($pathology, $prev_pathology);
            $insert_radiology = array_diff($radiology, $prev_radiology);

            if(!empty($_medicines_delete)){
                $_medicines_delete  = array_diff($prev_medicine, $not_be_deleted_medicines);
            }            

            $basic_id       = $this->prescription_model->add_ipdprescription($opd_basic_array, $insert_medicines, $update_medicines, $_medicines_delete, $insert_pathology, $insert_radiology, $delete_pathology, $delete_radiology, $ipd_prescription_basic_id);

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $basic_id);
            }
			
            $patient_record = $this->patient_model->get_patientidbyvisitid($visitid);
            $opd_id         = $patient_record['opd_details_id'];
            $visible_module = $this->input->post('visible');

            if (!empty($pathology)) {
                foreach ($pathology as $key => $value) {
                    $pathology_data        = $this->notificationsetting_model->getpathologyDetails($value);
                    $pathology_test_name[] = $pathology_data['test_name'] . "(" . $pathology_data['short_name'] . ")";
                }
            }

            if (!empty($radiology)) {
                foreach ($radiology as $key => $value) {
                    $radiology_data        = $this->notificationsetting_model->getradiologyDetails($value);
                    $radiology_test_name[] = $radiology_data['test_name'] . "(" . $radiology_data['short_name'] . ")";
                }
            }
            $medicine_var = "";
            if (!empty($medicine_name_array)) {
                $medicine_var = implode(",", $medicine_name_array);
            }
            $pathology_test_var = "";
            if (!empty($pathology_test_name)) {
                $pathology_test_var = implode(",", $pathology_test_name);
            }
            $radiology_test_var = "";
            if (!empty($radiology_test_name)) {
                $radiology_test_var = implode(",", $radiology_test_name);
            }

            $generated_by_details = $this->notificationsetting_model->getstaffDetails($this->customlib->getStaffID());
            $prescribe_by_details = $this->notificationsetting_model->getstaffDetails($opd_details['doctor_id']);

            $event_data = array(
                'prescription_no'     => $this->customlib->getSessionPrefixByType('opd_prescription') . $basic_id,
                'opd_no'              => $this->customlib->getSessionPrefixByType('opd_no') . $patient_record['opd_details_id'],
                'checkup_id'          => $this->customlib->getSessionPrefixByType('checkup_id') . $visitid,
                'finding_description' => $finding_description,
                'medicine'            => $medicine_var,
                'radilogy_test'       => $radiology_test_var,
                'pathology_test'      => $pathology_test_var,
                'prescribe_by'        => composeStaffNameByString($prescribe_by_details['name'], $prescribe_by_details['surname'], $prescribe_by_details['employee_id']),
                'generated_by'        => composeStaffNameByString($generated_by_details['name'], $generated_by_details['surname'], $generated_by_details['employee_id']),
                'patient_id'          => $patient_record['patient_id'],
            );

            if(!empty($visible_module))
            {
                $notification_array['visible_module'] = $visible_module;
               $this->system_notification->send_system_notification('notification_opd_prescription_created', $event_data, $notification_array);
            }
            
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'visitid' => $visitid);
        }
        echo json_encode($array);
    }

    public function update_ipdprescription()
    {
        if (!$this->rbac->hasPrivilege('prescription', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('ipd_id', $this->lang->line('ipd_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_cat[]', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'ipd_id'       => form_error('ipd_id'),
                'medicine_cat' => form_error('medicine_cat[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $ipd_id   = $this->input->post('ipd_id');
            $visit_id = $this->input->post('visit_id');
            $visible_module = $this->input->post("visible");
            $ipd_no_value = $this->input->post('ipd_no_value');

            if (!empty($ipd_id)) {
                $ipd_details = $this->patient_model->getipddetailspres($ipd_id);
            }

            $insert_id       = $ipd_details["patient_id"];
            $doctor_id       = $ipd_details["staff_id"];
            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["ipdpres"];
            $url             = base_url() . $url_link . '/' . $insert_id . '/' . $ipd_id;
            $this->ipdpresNotification($insert_id, $doctor_id, $ipd_id, $ipd_no_value, $url, $visible_module);
            $medicine         = $this->input->post("medicine[]");
            $medicine_cat     = $this->input->post("medicine_cat[]");
            $prescription_id  = $this->input->post("prescription_id[]");
            $previous_pres_id = $this->input->post("previous_pres_id[]");
            $dosage           = $this->input->post("dosage[]");
            $instruction      = $this->input->post("instruction[]");
            $pathology        = $this->input->post("pathology[]");
            $radiology        = $this->input->post("radiology[]");
            $pathology_id     = implode(",", $pathology);
            $radiology_id     = implode(",", $radiology);
            $header_note      = $this->input->post("header_note");
            $footer_note      = $this->input->post("footer_note");
            $data_array       = array();
            $delete_arr       = array();
            foreach ($previous_pres_id as $pkey => $pvalue) {
                if (in_array($pvalue, $prescription_id)) {

                } else {
                    $delete_arr[] = array('id' => $pvalue);
                }
            }

            $i = 0;
            foreach ($medicine as $key => $value) {
                $inst               = '';
                $do                 = '';
                $medicine_cat_value = '';
                if (!empty($dosage[$i])) {
                    $do = $dosage[$i];
                }
                if (!empty($instruction[$i])) {
                    $inst = $instruction[$i];
                }
                if (!empty($medicine_cat[$i])) {
                    $medicine_cat_value = $medicine_cat[$i];
                }
                if ($prescription_id[$i] == 0) {
                    $add_data = array('ipd_id' => $ipd_id, 'basic_id' => $visit_id, 'medicine' => $value, 'medicine_category_id' => $medicine_cat_value, 'dosage' => $do, 'instruction' => $inst);
                    $data_array[] = $add_data;
                } else {
                    $update_data = array('id' => $prescription_id[$i], 'medicine_category_id' => $medicine_cat_value, 'ipd_id' => $ipd_id, 'medicine' => $value, 'dosage' => $do, 'instruction' => $inst);
                    $this->prescription_model->update_ipdprescription($update_data);
                }
                $i++;
            }

            $ipd_array = array('id' => $visit_id, 'header_note' => $header_note, 'footer_note' => $footer_note);

            if (!empty($data_array)) {
                $this->patient_model->add_ipdprescription($data_array);
            }
            if (!empty($delete_arr)) {
                $this->prescription_model->delete_ipdprescription($delete_arr);
            }
            $this->patient_model->addipd($ipd_array);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('prescription_added_successfully'));
        }
        echo json_encode($array);
    }

    public function add_inpatient()
    {
        if (!$this->rbac->hasPrivilege('ipd_patient', 'can_add')) {
            access_denied();
        }
        $patient_type  = $this->customlib->getPatienttype();
        $custom_fields = $this->customfield_model->getByBelong('ipd');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[ipd][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|callback_valid_patient');
        $this->form_validation->set_rules('credit_limit', $this->lang->line('credit_limit'), 'trim|required');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('admission_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('bed_no', $this->lang->line('bed_number'), 'trim|required|xss_clean');		
		
		$consult      = $this->input->post('live_consult');
		if($consult == 'yes'){
			if (!$this->session->has_userdata('zoom_access_token')) {
				$this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
			}
		}
		
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'        => form_error('patient_id'),
                'credit_limit'      => form_error('credit_limit'),
                'appointment_date'  => form_error('appointment_date'),
                'bed_no'            => form_error('bed_no'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'zoom_live' 		=> form_error('zoom_live'),
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

            $appointment_date = $this->input->post('appointment_date');
            $patient_id       = $this->input->post('patient_id');
            $password         = $this->input->post('password');
            $email            = $this->input->post('email');
            $mobileno         = $this->input->post('mobileno');
            $patient_name     = $this->input->post('patient_name');             
            
            if ($this->module_lib->hasActive('live_consultation')) {
                $live_consult = $this->input->post('live_consult');
            } else {
                $live_consult = 'no';
            }
            
            $doctor_id        = $this->input->post('consultant_doctor');
            $date             = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);           
            $ipd_data = array(
                'date'            => $date,
                'bed'             => $this->input->post('bed_no'),
                'bed_group_id'    => $this->input->post('bed_group_id'),               
                'case_type'       => $this->input->post('case'),
                'symptoms'        => $this->input->post('symptoms'),
                'refference'      => $this->input->post('refference'),
                'cons_doctor'     => $this->input->post('consultant_doctor'),
                'patient_id'      => $patient_id,
                'credit_limit'    => $this->input->post('credit_limit'),
                'casualty'        => $this->input->post('casualty'),
                'discharged'      => 'no',
                'live_consult'    => $live_consult,
                'generated_by'    => $this->customlib->getLoggedInUserID(),
            );
            
             if($this->input->post('is_for_antenatal')!=""){
                $ipd_data['is_antenatal'] = $this->input->post('is_for_antenatal');
            }

            $patient_data = array('is_antenatal' => $this->input->post('is_for_antenatal'),'id'=> $patient_id);
            $this->patient_model->add($patient_data);

            $ipd_id            = $this->patient_model->add_ipd($ipd_data);
            $ipdno             = $this->customlib->getSessionPrefixByType('ipd_no') . $ipd_id;
            $case_reference_id = $this->patient_model->getReferenceByIpdId($ipd_id);
            $bed_data          = array('id' => $this->input->post('bed_no'), 'is_active' => 'no');
            $this->bed_model->savebed($bed_data);
            $bed_history = array(
                "case_reference_id" => $case_reference_id,
                "bed_group_id"      => $this->input->post("bed_group_id"),
                "bed_id"            => $this->input->post("bed_no"),
                "from_date"         => $date,
                "is_active"         => "yes",
            );
            $this->bed_model->saveBedHistory($bed_history);
            $setting_result = $this->setting_model->getzoomsetting();
            $ipdduration    = $setting_result->ipd_duration;

            if ($live_consult == 'yes') {

                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'     => $doctor_id,
                    'patient_id'     => $patient_id,
                    'ipd_id'       => $ipd_id,
                    'title'        => 'Online consult for ' . $ipdno,
                    'date'         => $date,
                    'duration'     => $ipdduration,
                    'created_id'   => $this->customlib->getStaffID(),
                    'api_type'     => $api_type,
                    'host_video'   => 1,
                    'client_video' => 1,
                    'purpose'      => 'consult',
                    'password'     => $password,
                    'timezone'     => $this->customlib->getTimeZone(),
                );
                $response = $this->zoom_api->createAMeeting($insert_array);
				
				 if ($response['status']) {
                    if (isset($response['data'])) {               
                        $insert_array['return_response'] = json_encode($response['data']);
                        $conferenceid                    = $this->conference_model->add($insert_array);
                        $sender_details                  = array('patient_id' => $patient_id, 'conference_id' => $conferenceid, 'contact_no' => $mobileno, 'email' => $email);

                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
                    }
                }
            }
			
            $custom_field_post  = $this->input->post("custom_fields[ipd]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[ipd][" . $key . "]");
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
                $this->customfield_model->insertRecord($custom_value_array, $ipd_id);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('patient_added_successfully'));
            if ($this->session->has_userdata("appointment_id")) {
                $appointment_id = $this->session->userdata("appointment_id");
                $updateData     = array('id' => $appointment_id, 'is_ipd' => 'yes');
                $this->appointment_model->update($updateData);
                $this->session->unset_userdata('appointment_id');
            }

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $patient_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $patient_id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }

            $sender_details = array('patient_id' => $patient_id, 'patient_name' => $patient_name, 'ipdid' => $ipd_id, 'contact_no' => $mobileno, 'email' => $email);
            $this->mailsmsconf->mailsms('ipd_patient_registration', $sender_details);

            $bed_details    = $this->bed_model->getBedDetails($this->input->post('bed_no'));
            $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('consultant_doctor'));
            $event_data     = array(
                'patient_id'           => $this->input->post('patient_id'),
                'symptoms_description' => $this->input->post('symptoms'),
                'bed_location'         => $bed_details['name'] . ' - ' . $bed_details['bedgroup'] . ' - ' . $bed_details['floor_name'],
                'admission_date'       => $this->customlib->YYYYMMDDHisTodateFormat($appointment_date, $this->customlib->getHospitalTimeFormat()),
                'doctor_id'            => $this->input->post('consultant_doctor'),
                'doctor_name'          => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
            );

            $this->system_notification->send_system_notification('ipd_visit_created', $event_data);
        }

        echo json_encode($array);
    }

    public function valid_patient()
    {
        $id = $this->input->post('patient_id');

        if ($id > 0) {
            $check_exists = $this->patient_model->valid_patient($id);
            if ($check_exists == true) {
                $this->form_validation->set_message('valid_patient', $this->lang->line('patient_already_in_ipd'));
                return false;
            }
        }
        return true;
    }

    public function add_consultant_instruction()
    {
        if (!$this->rbac->hasPrivilege('consultant_register', 'can_add')) {
            access_denied();
        }

        $custom_fields = $this->customfield_model->getByBelong('ipdconsultinstruction');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[ipdconsultinstruction][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $this->form_validation->set_rules('date', $this->lang->line('applied_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('instruction', $this->lang->line('instruction'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('insdate', $this->lang->line('consultant_date'), 'trim|required|xss_clean');
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
                        $custom_fields_id                                                              = $custom_fields_value['id'];
                        $custom_fields_name                                                            = $custom_fields_value['name'];
                        $error_msg2["custom_fields[ipdconsultinstruction][" . $custom_fields_id . "]"] = form_error("custom_fields[ipdconsultinstruction][" . $custom_fields_id . "]");
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
            $getdate     = $this->input->post('date');
            $ins_date    = $this->input->post('insdate');
            $patient_id  = $this->input->post('patient_id');
            $ipd_id      = $this->input->post('ipdid');
            $doctor      = $this->input->post('doctor');
            $instruction = $this->input->post('instruction');
            $date        = $this->customlib->dateFormatToYYYYMMDDHis($getdate);
            $data_array  = array(
                'date'        => $date,
                'ipd_id'      => $ipd_id,
                'ins_date'    => $this->customlib->dateFormatToYYYYMMDD($ins_date),
                'cons_doctor' => $doctor,
                'instruction' => $instruction,
            );
            $custom_field_post  = $this->input->post("custom_fields[ipdconsultinstruction]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[ipdconsultinstruction][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            $insert_id = $this->patient_model->add_consultantInstruction($data_array);
            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_added_successfully'));

            $doctor_list       = $this->patient_model->getDoctorsipd($ipd_id);
            $consultant_doctor = $this->patient_model->get_patientidbyIpdId($ipd_id);
            $doctor_details    = $this->notificationsetting_model->getstaffDetails($this->input->post('doctor'));

            $consultant_doctorarray[] = array('consult_doctor' => $this->input->post('doctor'), 'name' => $doctor_details['name'] . " " . $doctor_details['surname'] . "(" . $doctor_details['employee_id'] . ")");

            $consultant_doctorarray[] = array('consult_doctor' => $consultant_doctor['cons_doctor'], 'name' => $consultant_doctor['doctor_name'] . " " . $consultant_doctor['doctor_surname'] . "(" . $consultant_doctor['doctor_employee_id'] . ")");
            foreach ($doctor_list as $key => $value) {
                $consultant_doctorarray[] = array('consult_doctor' => $value['consult_doctor'], 'name' => $value['ipd_doctorname'] . " " . $value['ipd_doctorsurname'] . "(" . $value['employee_id'] . ")");
            }

            $event_data = array(
                'patient_id'       => $this->input->post('patient_id'),
                'ipd_no'           => $this->customlib->getSessionPrefixByType('ipd_no') . $ipd_id,
                'case_id'          => $this->input->post('case_id'),
                'instruction_date' => $this->customlib->YYYYMMDDTodateFormat($ins_date),
                'applied_date'     => $this->customlib->YYYYMMDDTodateFormat($date),
                'doctor_id'        => $this->input->post('doctor'),
                'doctor_name'      => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                'instruction'      => $this->input->post('instruction'),
            );
 
            $this->system_notification->send_system_notification('add_consultant_register', $event_data, $consultant_doctorarray);
        }
        echo json_encode($array);
    }

    public function update_consultant_instruction()
    {
        if (!$this->rbac->hasPrivilege('consultant_register', 'can_add')) {
            access_denied();
        }

        $custom_fields = $this->customfield_model->getByBelong('ipdconsultinstruction');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[ipdconsultinstruction][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $this->form_validation->set_rules('date', $this->lang->line('applied_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
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
                        $custom_fields_id                                                              = $custom_fields_value['id'];
                        $custom_fields_name                                                            = $custom_fields_value['name'];
                        $error_msg2["custom_fields[ipdconsultinstruction][" . $custom_fields_id . "]"] = form_error("custom_fields[ipdconsultinstruction][" . $custom_fields_id . "]");
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
            $id       = $this->input->post('instruction_id');
            $getdate  = $this->input->post('date');
            $ins_date = $this->input->post('insdate');
            $ipd_id      = $this->input->post('ipdid');
            $doctor      = $this->input->post('doctor');
            $instruction = $this->input->post('instruction');
            $date        = $this->customlib->dateFormatToYYYYMMDDHis($getdate, $this->customlib->getHospitalTimeFormat());
            $data_array  = array(
                'id'          => $id,
                'date'        => $date,
                'ipd_id'      => $ipd_id,
                'ins_date'    => $this->customlib->dateFormatToYYYYMMDD($ins_date),
                'cons_doctor' => $doctor,
                'instruction' => $instruction,
            );
            $insert_id          = $this->patient_model->add_consultantInstruction($data_array);
            $custom_field_post  = $this->input->post("custom_fields[ipdconsultinstruction]");
            $custom_value_array = array();
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[ipdconsultinstruction][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'ipdconsultinstruction');
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_added_successfully'));
        }
        echo json_encode($array);
    }

    public function add_nurse_note()
    {
        if (!$this->rbac->hasPrivilege('nurse_note', 'can_add')) {
            access_denied();
        }

        $custom_fields = $this->customfield_model->getByBelong('ipdnursenote');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[ipdnursenote][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('nurse', $this->lang->line('nurse'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('note', $this->lang->line('note'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('comment', $this->lang->line('comment'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'    => form_error('date'),
                'nurse'   => form_error('nurse'),
                'note'    => form_error('note'),
                'comment' => form_error('comment'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[ipdnursenote][" . $custom_fields_id . "]"] = form_error("custom_fields[ipdnursenote][" . $custom_fields_id . "]");
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
            $date       = $this->input->post('date');
            $nurse      = $this->input->post('nurse');
            $patient_id = $this->input->post('patient_id');
            $ipd_id     = $this->input->post('ipdid');
            $note       = $this->input->post('note');
            $comment    = $this->input->post('comment');            

            $data_array = array(
                'date'       => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'ipd_id'     => $ipd_id,
                'staff_id'   => $nurse,
                'note'       => $note,
                'comment'    => $comment,
                'updated_at' => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'created_by' => $this->customlib->getStaffID(),
            );

            $custom_field_post  = $this->input->post("custom_fields[ipdnursenote]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[ipdnursenote][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            $insert_id = $this->patient_model->add_nursenote($data_array);
            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_added_successfully'));

            $doctor_list       = $this->patient_model->getDoctorsipd($ipd_id);
            $patient_detail    = $this->patient_model->get_patientidbyIpdId($ipd_id);
            $operation_details = $this->operationtheatre_model->otdetails($insert_id);
            $nurse_detail      = $this->patient_model->getNursenote($insert_id);

            $consultant_doctorarray[] = array('consult_doctor' => $patient_detail['cons_doctor'], 'name' => $patient_detail['doctor_name'] . " " . $patient_detail['doctor_surname'] . "(" . $patient_detail['doctor_employee_id'] . ")");
            foreach ($doctor_list as $key => $value) {
                $consultant_doctorarray[] = array('consult_doctor' => $value['consult_doctor'], 'name' => $value['ipd_doctorname'] . " " . $value['ipd_doctorsurname'] . "(" . $value['employee_id'] . ")");
            }

            $event_data = array(
                'patient_id'  => $patient_detail['patient_id'],
                'ipd_no'      => $this->customlib->getSessionPrefixByType('ipd_no') . $ipd_id,
                'case_id'     => $patient_detail['case_reference_id'],
                'nurse_name'  => $nurse_detail['nurse_surname'],
                'nurse_id'    => $nurse,
                'note'        => $note,
                'comment'     => $comment,
                'date'        => $this->customlib->YYYYMMDDHisTodateFormat($date, $this->customlib->getHospitalTimeFormat()),
                'doctor_name' => composeStaffNameByString($patient_detail['doctor_name'], $patient_detail['doctor_surname'], $patient_detail['doctor_employee_id']),
            );

            $this->system_notification->send_system_notification('add_nurse_note', $event_data, $consultant_doctorarray);

        }
        echo json_encode($array);
    }

    public function updatenursenote()
    {
        $patient_type  = $this->customlib->getPatienttype();
        $custom_fields = $this->customfield_model->getByBelong('ipdnursenote');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];
                    $this->form_validation->set_rules("custom_fields[ipdnursenote][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
                }
            }
        }
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('nurse', $this->lang->line('nurse'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('note', $this->lang->line('note'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('comment', $this->lang->line('comment'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'date'    => form_error('date'),
                'nurse'   => form_error('nurse'),
                'note'    => form_error('note'),
                'comment' => form_error('comment'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[ipdnursenote][" . $custom_fields_id . "]"] = form_error("custom_fields[ipdnursenote][" . $custom_fields_id . "]");
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
            $nurse_noteid      = $this->input->post('nurseid');
            $date              = $this->input->post('date');
            $nurse             = $this->input->post('nurse');
            $note              = $this->input->post('note');
            $comment           = $this->input->post('comment');
            $custom_field_post = $this->input->post("custom_fields[ipdnursenote]");
            $date_format       = $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format);
            $data_array        = array(
                'id'       => $nurse_noteid,
                'date'     => $date_format,
                'staff_id' => $nurse,
                'note'     => $note,
                'comment'  => $comment,
            );

            $this->patient_model->add_nursenote($data_array);
            $custom_value_array = array();
            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[ipdnursenote][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $nurse_noteid,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $nurse_noteid, 'ipdnursenote');
            }
            $msg   = $this->lang->line('nurse_notes_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function addnursenotecomment()
    {
        $this->form_validation->set_rules('comment_staff', $this->lang->line('comment'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(

                'comment_staff' => form_error('comment_staff'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $nurse_noteid  = $this->input->post('nurseid');
            $comment_staff = $this->input->post('comment_staff');
            $date          = date("Y-m-d H:i:s");
            $userdata      = $this->customlib->getUserData();
            $staff_id      = $userdata['id'];
            $data_array    = array(
                'nurse_note_id'   => $nurse_noteid,
                'comment_staff'   => $comment_staff,
                'comment_staffid' => $staff_id,
                'created_at'      => $date,
            );
            $this->patient_model->add_nursenotecomment($data_array);
            $msg   = $this->lang->line('comment_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function getChargeById()
    {
        $id = $this->input->post('charge_id');      
        $patient_id=$this->input->post('patient_id');
        $is_tpa_valid=true;
        $display_tpa_charge=false;

        if($this->input->post('is_tpa') == 1 && $patient_id != ""){                
            $result = $this->patient_model->getPatientChargeById($id, $patient_id);            
            if($result && !IsNullOrEmptyString($result['insurance_validity']) && $result['patient_org_id'] >0 ){
                if(strtotime($result['insurance_validity']) < strtotime(date('Y-m-d'))){                
                    $is_tpa_valid=false;  
                }else{
                    $display_tpa_charge=true;
                }
            }
        }else{
            $result = $this->patient_model ->getPatientChargeById($id, $patient_id);
        }

        if(!$result){
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('no_charge_has_configured_for_selected_category')));
        }elseif($result && !$is_tpa_valid){
            echo json_encode(array('status' => 2, 'display_tpa_charge'=>$display_tpa_charge, 'result' => $result,'msg'=> $this->lang->line('your_tpa_validity_has_expired_on').' '.$this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']).', '. $this->lang->line('so_standard_charges_has_been_applied') ));
        }else{
            echo json_encode(array('status' => 1,'display_tpa_charge'=>$display_tpa_charge, 'result' => $result));
        }
    } 
 
    public function opd_report()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/opd');
        $this->session->set_userdata('subsub_menu', 'reports/patient/opd_report');

        $custom_fields      = $this->customfield_model->get_custom_fields('opd', '', '', 1);
        $doctorlist         = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist'] = $doctorlist;
        $doctors            = $this->staff_model->getStaffbyrole(3);
        $data['doctors']    = $doctors;
        $data['agerange']   = $this->agerange;        
        $staffopd               = $this->patient_model->getstaffbytransactionbill();
        $data['staffopd']       = $staffopd;
        $data["searchlist"]     = $this->search_type;
        $data['fields']         = $custom_fields;
        $data['gender']         = $this->customlib->getGender_Patient();
        $data['classification'] = $this->symptoms_model->getsymtype();

        $data['symptoms'] = $this->symptoms_model->get();

        if (!empty($data['symptoms'])) {
            foreach ($data['symptoms'] as $row) {
                $symptoms[$row['symptoms_classification_id']][] = $row;
            }
        }
		if(!empty($symptoms)){
			$data['symptoms'] = $symptoms;
		}else{
			$data['symptoms'] = '';
		}
        $data['findings'] = $this->finding_model->get();
        $data['category'] = $this->finding_model->getfindingcategory();

        if (!empty($data['findings'])) {
            foreach ($data['findings'] as $row) {
                $findings[$row['finding_category_id']][] = $row;
            }
        }
		if(!empty($findings)){
			$data['findings'] = $findings;
		}else{
			$data['findings'] = '';	
		}
        $this->load->view('layout/header');
        $this->load->view('admin/patient/opdReport', $data);
        $this->load->view('layout/footer');
    }

    public function opdreportbalance()
    {
        if (!$this->rbac->hasPrivilege('opd_balance_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/opd');
        $this->session->set_userdata('subsub_menu', 'reports/patient/opdreportbalance');

        $doctorlist             = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist']     = $doctorlist;
        $doctors                = $this->staff_model->getStaffbyrole(3);
        $data['doctors']        = $doctors;
        $patient_status         = $this->input->post("patient_status");
        $data["patient_status"] = $patient_status;
        $status                 = 'yes';
        $data['agerange']       = $this->agerange;
        $data["searchlist"]     = $this->search_type;
        $data['gender']         = $this->customlib->getGender_Patient();
        $data['discharged']     = $this->customlib->getdischargestatus();
        $this->load->view('layout/header');
        $this->load->view('admin/patient/opdReportbalance', $data);
        $this->load->view('layout/footer');
    }

    public function ipdReport()
    {
        if (!$this->rbac->hasPrivilege('ipd_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/ipd');
        $this->session->set_userdata('subsub_menu', 'reports/patient/ipdreport');

        $custom_fields = $this->customfield_model->get_custom_fields('ipd', '', '', 1);
        $doctorlist              = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist']      = $doctorlist;
        $staffipd                = $this->patient_model->getstaffbytransactionbill();
        $data['staffipd']        = $staffipd;
        $collect_staff           = $this->input->post("collect_staff");
        $data['staffipd_select'] = $collect_staff;
        $data['agerange']        = $this->agerange;
        $from_age                = $this->input->post('from_age');
        $to_age                  = $this->input->post('to_age');
        $data['from_age']        = $from_age;
        $data['to_age']          = $to_age;
        $status                  = 'no';
        $patient_status          = $this->input->post("patient_status");

        $data['classification'] = $this->symptoms_model->getsymtype();
        $data['symptoms']       = $this->symptoms_model->get();
        if (!empty($data['symptoms'])) {
            foreach ($data['symptoms'] as $row) {
                $symptoms[$row['symptoms_classification_id']][] = $row;
            }
        }

        $data['gender']   = $this->customlib->getGender_Patient();
		if(!empty($symptoms)){
			$data['symptoms'] = $symptoms;
		}else{
			$data['symptoms'] = '';
		}
        $data['findings'] = $this->finding_model->get();
        $data['category'] = $this->finding_model->getfindingcategory();

        if (!empty($data['findings'])) {
            foreach ($data['findings'] as $row) {
                $findings[$row['finding_category_id']][] = $row;
            }
        }

		if(!empty($findings)){
			$data['findings'] = $findings;
		}else{
			$data['findings'] = '';
		}       

        if (empty($patient_status)) {
            $patient_status = 'on_bed';
        }
        if ($patient_status == 'all') {
            $status = '';
        } else if ($patient_status == 'on_bed') {
            $status = 'yes';
        } else if ($patient_status == 'discharged') {
            $status = 'no';
        }
        $data["searchlist"] = $this->search_type;
        $data["fields"]     = $custom_fields;

        if (!empty($patient_status)) {
            $data['patient_status'] = $patient_status;
        } else {
            $data['patient_status'] = 'on_bed';
        }

        $this->load->view('layout/header');
        $this->load->view('admin/patient/ipdReport', $data);
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
                'search_type'        =>     $this->input->post('search_type'),
                'gender'             =>     $this->input->post('gender'),
                'doctor'             =>     $this->input->post('doctor'),
                'date_from'          =>     $this->input->post('date_from'),
                'date_to'            =>     $this->input->post('date_to'),
                'symptoms'           =>     $this->input->post('symptoms'),
                'findings'           =>     $this->input->post('findings'),
                'from_age'           =>     $this->input->post('from_age'),
                'to_age'             =>     $this->input->post('to_age'),
                'miscellaneous'      =>     $this->input->post('miscellaneous'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function checkvalidationsearchtype()
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
                'date_from'   => $this->input->post('date_from'),
                'date_to'     => $this->input->post('date_to'),
                'gender'      => $this->input->post('gender'),
                'doctor'      => $this->input->post('doctor'),
                'from_age'    => $this->input->post('from_age'),
                'to_age'      => $this->input->post('to_age'),
                'discharged'  => $this->input->post('discharged'),
                'miscellaneous'  => $this->input->post('miscellaneous'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function checkvalidationopdbalance()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
		
		if($this->input->post('search_type') == 'period'){
				$this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
				$this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
		}
		
        if ($this->form_validation->run() == false) {
			
			if($this->input->post('search_type') == 'period'){
				$msg1 = array(
					'date_to' => form_error('date_to'),
					'date_from' => form_error('date_from'),
				);
			}		
			
            $msg2 = array(
                'search_type' => form_error('search_type'),
            );
			
			if($this->input->post('search_type') == 'period'){
				$msg = array_merge($msg1, $msg2);
			}else{
				$msg = $msg2;
			}
			
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'    => $this->input->post('search_type'),
                'doctor'         => $this->input->post('doctor'),
                'from_age'       => $this->input->post('from_age'),
                'to_age'         => $this->input->post('to_age'),
                'date_from'      => $this->input->post('date_from'),
                'date_to'        => $this->input->post('date_to'),
                'gender'         => $this->input->post('gender'),
                'discharged'     => $this->input->post('discharged'),
                'miscellaneous'  => $this->input->post('miscellaneous'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function checkvalidationipdbalance()
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
                'patient_status' => $this->input->post('patient_status'),
                'from_age'       => $this->input->post('from_age'),
                'to_age'         => $this->input->post('to_age'),
                'date_from'      => $this->input->post('date_from'),
                'date_to'        => $this->input->post('date_to'),
                'gender'         => $this->input->post('gender'),
                'miscellaneous'  => $this->input->post('miscellaneous'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function ipdreports()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['collect_staff'] = $this->input->post('collect_staff');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $start_date              = '';
        $end_date                = '';
        $fields                  = $this->customfield_model->get_custom_fields('ipd', '', '', 1);
        if ($search['search_type'] == 'period') {
            $data['start_date'] = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $data['end_date']   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);
        } else {
            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }
            $data['start_date'] = $dates['from_date'];
            $data['end_date']   = $dates['to_date'];
        }

        $data['gender']   = $this->input->post('gender');
        $data['doctor']   = $this->input->post('doctor');
        $data['symptoms'] = $this->input->post('symptoms');
        $data['findings'] = $this->input->post('findings');
        $data['from_age'] = $this->input->post('from_age');
        $data['to_age']   = $this->input->post('to_age');
        $data['miscellaneous']   = $this->input->post('miscellaneous');;
        $reportdata = $this->transaction_model->ipdpatientreportsRecord($data);
        $reportdata = json_decode($reportdata);

        $dt_data = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $row   = array();
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[] = $this->customlib->getSessionPrefixByType($value->module_no) . $value->id;
                $row[] = composePatientName($value->patient_name, $value->patientid);
                $row[] = $this->customlib->getPatientAge($value->age, $value->month, $value->day);
                
                if($value->gender){
                    $row[] = $this->lang->line(strtolower($value->gender));
                }else{
                    $row[] = '';
                }                
                
                $row[] = $value->mobileno;
                $row[] = $value->guardian_name;
                $row[] = $value->name . " " . $value->surname . "(" . $value->employee_id . ")";
                $row[] = $value->symptoms;
                $row[] = $value->finding_description;

				if ($this->rbac->hasPrivilege('ipd_antenatal', 'can_view')) { 
					if($value->is_antenatal==1){
						$antinatal_status=$this->lang->line('yes');
					}else if($value->is_antenatal==0){
						$antinatal_status=$this->lang->line('no');
					}
					$row[] = $antinatal_status;
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

    public function opdreports()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['collect_staff'] = $this->input->post('collect_staff');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $start_date              = '';
        $end_date                = '';
        $fields                  = $this->customfield_model->get_custom_fields('opd', '', '', 1);
        if ($search['search_type'] == 'period') {

            $data['start_date'] = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $data['end_date']   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);

        } else {
            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }

            $data['start_date'] = $dates['from_date'];
            $data['end_date']   = $dates['to_date'];

        }

        $data['gender']               = $this->input->post('gender');
        $data['doctor']               = $this->input->post('doctor');
        $data['symptoms']             = $this->input->post('symptoms');
		$data['findings']             = $this->input->post('findings');		
        $data['from_age']             = $this->input->post('from_age');
        $data['to_age']               = $this->input->post('to_age');
        $data['miscellaneous']        = $this->input->post('miscellaneous');

        $reportdata = $this->transaction_model->opdpatientreportRecord($data);

        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $first_action = "<a href=" . base_url() . 'admin/patient/profile/' . $value->patientid .">";

                $row   = array();
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->appointment_date);
                $row[] = $first_action . $this->customlib->getSessionPrefixByType($value->module_no) . $value->id . "</a>";              
                $row[] = $this->customlib->getSessionPrefixByType('checkup_id') . $value->visit_id;
                $row[] = composePatientName($value->patient_name, $value->patientid);
                $row[] = $this->customlib->getPatientAge($value->age, $value->month, $value->day);                
                
                if($value->gender){
                    $row[]        = $this->lang->line(strtolower($value->gender));
                }else{
                    $row[]        = '';
                }
              
                $row[] = $value->mobileno;
				
				if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { 
					if($value->is_antenatal==1){
						$antenatal_status=$this->lang->line('yes');
					}else{
						$antenatal_status=$this->lang->line('no');
					}
					$row[] = $antenatal_status;
				}
				
                $row[] = $value->guardian_name;
                $row[] = $value->name . " " . $value->surname . "(" . $value->employee_id . ")";
                $row[] = $value->symptoms;
                $row[] = $value->finding_description;
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

    public function opdbalancereports()
    {
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $start_date            = '';
        $end_date              = '';
        $from_age              = $this->input->post('from_age');
        $to_age                = $this->input->post('to_age');
        $gender                = $this->input->post('gender');
        $discharged            = $this->input->post('discharged');

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

        $miscellaneous=$this->input->post('miscellaneous');

        $reportdata = $this->transaction_model->opdpatientbalanceRecord($start_date, $end_date, $from_age, $to_age, $gender, $discharged,$miscellaneous);

        $reportdata    = json_decode($reportdata);
        $dt_data       = array();
        $total_balance = 0;
        $total_paid    = 0;
        $total_charge  = 0;
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $total_balance += ($value->amount_charged - $value->amount_paid);
                $total_paid += $value->amount_paid;
                $total_charge += $value->amount_charged;
                $row       = array();
                $row[]     = $this->customlib->getSessionPrefixByType('opd_no') . $value->id;
                $row[]     = $value->patient_name . " (" . $value->patient_id . ")";
                $row[]     = $value->case_reference_id;
                $row[]     = $this->customlib->getPatientAge($value->age, $value->month, $value->day);
                                
                if($value->gender){
                    $row[]        = $this->lang->line(strtolower($value->gender));
                }else{
                    $row[]        = '';
                }
                
                $row[]     = $value->mobileno;

                if($value->is_antenatal==1){
                    $antenatal_status=$this->lang->line('yes');
                }else{
                    $antenatal_status=$this->lang->line('no');
                }

                $row[]     = $antenatal_status;
                $row[]     = $this->lang->line($value->discharged);
                $row[]     = $value->amount_charged;
                $row[]     = $value->amount_paid;
                $row[]     = amountFormat($value->amount_charged - $value->amount_paid);
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
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_charge, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_paid, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_balance, 2, '.', '')) . "<br/>";
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

    public function ipdbalancereports()
    {
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        
        $search['search_type']       = $this->input->post('search_type');
        $search['date_from']         = $this->input->post('date_from');
        $search['date_to']           = $this->input->post('date_to');
        $condition['patient_status'] = $this->input->post('patient_status');
        $condition['from_age']       = $this->input->post('from_age');
        $condition['to_age']         = $this->input->post('to_age');
        $condition['gender']         = $this->input->post('gender');
        $condition['miscellaneous']  = $this->input->post('miscellaneous');

        $start_date = '';
        $end_date   = '';

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

        $reportdata    = $this->transaction_model->ipdpatientbalanceRecord($condition);
        $reportdata    = json_decode($reportdata);
        $dt_data       = array();
        $total_balance = 0;
        $total_paid    = 0;
        $total_charge  = 0;
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $total_balance += ($value->amount_charged - $value->amount_paid);
                $total_paid    += $value->amount_paid;
                $total_charge  += $value->amount_charged;

                $row       = array();
                $row[]     = $this->customlib->getSessionPrefixByType('ipd_no') . $value->id;
                $row[]     = $value->case_reference_id;
                $row[]     = composePatientName($value->patient_name, $value->patient_id);
                $row[]     = $this->customlib->getPatientAge($value->age, $value->month, $value->day); 
                                
                if($value->gender){
                    $row[]        = $this->lang->line(strtolower($value->gender));
                }else{
                    $row[]        = '';
                }

                if($value->is_antenatal==1){
                    $antinatal_status=$this->lang->line('yes');
                }else if($value->is_antenatal==0){
                    $antinatal_status=$this->lang->line('no');
                }
                
                $row[]     = $antinatal_status;             
                $row[]     = $value->mobileno;
                $row[]     = $value->guardian_name;
                $row[]     = $this->lang->line($value->discharged);
                $row[]     = $this->lang->line($value->is_active);
                $row[]     = $value->amount_charged;
                $row[]     = $value->amount_paid;
                $row[]     = amountFormat($value->amount_charged - $value->amount_paid);
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
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_charge, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_paid, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_balance, 2, '.', '')) . "<br/>";
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

    public function ipdreportbalance()
    {
        if (!$this->rbac->hasPrivilege('ipd_balance_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/ipd');
        $this->session->set_userdata('subsub_menu', 'reports/patient/ipdreportbalance');

        $doctorlist         = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist'] = $doctorlist;
        $status             = 'no';
        $data['agerange']   = $this->agerange;
        $from_age           = $this->input->post('from_age');
        $to_age             = $this->input->post('to_age');
        $data['from_age']   = $from_age;
        $data['to_age']     = $to_age;
        $data["searchlist"] = $this->search_type;
        $data['gender']     = $this->customlib->getGender_Patient();
        $data['discharged'] = $this->customlib->getdischargestatus();

        $this->load->view('layout/header');
        $this->load->view('admin/patient/ipdReportbalance', $data);
        $this->load->view('layout/footer');
    }

    public function dischargepatientreport()
    {
        if (!$this->rbac->hasPrivilege('discharge_patient_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/ipd');
        $this->session->set_userdata('subsub_menu', 'reports/patient/dischargepatientreport');

        $doctorlist         = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist'] = $doctorlist;
        $data['agerange']   = $this->agerange;
        $data["searchlist"] = $this->search_type;
        $data['gender']     = $this->customlib->getGender_Patient();
        $data['discharged'] = $this->customlib->discharge_status();
        $this->load->view('layout/header');
        $this->load->view('admin/patient/dischargePatientReport', $data);
        $this->load->view('layout/footer');
    }

    public function opddischargepatientreport()
    {
        if (!$this->rbac->hasPrivilege('discharge_patient_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/opd');
        $this->session->set_userdata('subsub_menu', 'reports/patient/opddischargepatientReport');

        $doctorlist         = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist'] = $doctorlist;
        $data['agerange']   = $this->agerange;
        $data["searchlist"] = $this->search_type;
        $data['gender']     = $this->customlib->getGender_Patient();
        $data['discharged'] = $this->customlib->discharge_status();
        $this->load->view('layout/header');
        $this->load->view('admin/patient/opddischargepatientReport', $data);
        $this->load->view('layout/footer');
    }

    public function revertBill()
    {
        $patient_id = $this->input->post('patient_id');
        $bill_id    = $this->input->post('bill_id');
        $bed_id     = $this->input->post('bed_id');
        $ipd_id     = $this->input->post('ipdid');

        if ((!empty($patient_id)) && (!empty($bill_id))) {
            $patient_data = array('id' => $patient_id, 'discharged' => 'no');
            $this->patient_model->add($patient_data);
            $ipd_data = array('id' => $ipd_id, 'discharged' => 'no');
            $this->patient_model->add_ipd($ipd_data);
            $bed_data = array('id' => $bed_id, 'is_active' => 'no');
            $this->bed_model->savebed($bed_data);
            $revert = $this->payment_model->revertBill($patient_id, $bill_id);
            $array  = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('record_not_updated'));
        }
        echo json_encode($array);
    }

    public function deleteOPD()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_delete')) {
            access_denied();
        }
        $opdid      = $this->input->post('opdid');
        $patient_id = $this->input->post('patient_id');
        if (!empty($opdid)) {
            $return_result = $this->patient_model->deleteOPD($opdid, $patient_id);
            if (!is_bool($return_result)) {

                $array = array('status' => 'success', 'error' => '', 'total_remain' => $return_result, 'message' => $this->lang->line('delete_message'));
            } else {
                $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('something_went_wrong'));
            }
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function deletemedicationdosage()
    {
        if (!$this->rbac->hasPrivilege('ipd_medication', 'can_delete')) {
            access_denied();
        }
        $medication_id = $this->input->post('medication_id');
        if (!empty($medication_id)) {
            $this->medicine_dosage_model->deletemedicationdosage($medication_id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_delete')) {
            access_denied();
        }

        if (!empty($id)) {
            $this->patient_model->deletePatient($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('something_went_wrong'));
        }
        echo json_encode($array);
    }

    public function deletePatient()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_delete')) {
            access_denied();
        }
        $id = $this->input->post('delid');
        if (!empty($id)) {
            $this->patient_model->deletePatient($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function deleteOPDPatient()
    {
        if (!$this->rbac->hasPrivilege('opd_patient', 'can_delete')) {
            access_denied();
        }
        $id = $this->input->post('id');

        if (!empty($id)) {
            $this->patient_model->deleteOPDPatient($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function patientCredentialReport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/patient');
        $this->session->set_userdata('subsub_menu', 'reports/patient/patientcredentialreport');
        $data["searchlist"] = $this->search_type;    
        $this->load->view("layout/header");
        $this->load->view("admin/patient/patientcredentialreport",$data);
        $this->load->view("layout/footer");
    }

    public function getcredentialdatatable()
    {
        $start_date             = '';
        $end_date               = '';
        $search['search_type']  = $this->input->post('search_type');
        $search['userroletype'] = $this->input->post('userroletype');
        $search['date_from'] = $this->input->post('date_from');
        $search['date_to']   = $this->input->post('date_to');

        if ($search['search_type'] == 'period') {

            $start_date = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);

        } else {

            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
            }

            $start_date = $dates['from_date'];
            $end_date   = $dates['to_date'];
        }

        $dt_response = $this->patient_model->getAllcredentialRecord($start_date, $end_date);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================
                $row[] = $value->id;
                $row[] = $value->patient_name;
                $row[] = $value->username;
                $row[] = $value->password;
                //====================
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

    public function checkfiltervalidation()
    {
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type'  => form_error('search_type'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'  => $this->input->post('search_type'),
                'date_from'    => $this->input->post('date_from'),
                'date_to'      => $this->input->post('date_to'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }


    public function deleteIpdPatient()
    {
        $ipdid = $this->input->post('ipdid');
        if (!empty($ipdid)) {
            $this->patient_model->deleteIpdPatient($ipdid);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function getBedStatus()
    {
        $floor_list            = $this->floor_model->floor_list();
        $bedlist               = $this->bed_model->bed_list();
        $bedactive             = $this->bed_model->bed_active();
        $bedgroup_list         = $this->bedgroup_model->bedGroupFloor();
        $data["floor_list"]    = $floor_list;
        $data["bedlist"]       = $bedlist;
        $data["bedgroup_list"] = $bedgroup_list;
        $data['bedactive']     = $bedactive;
        $this->load->view("layout/bedstatusmodal", $data);
    }

    public function updateBed()
    {
        $this->form_validation->set_rules('bedgroup', $this->lang->line('bed_group'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('bedno', $this->lang->line('bed'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'bedgroup' => form_error('bedgroup'),
                'bedno'    => form_error('bedno'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array(
                'ipd_no'       => $this->input->post('ipd_no'),
                'bed_group_id' => $this->input->post('bedgroup'),
                'bed'          => $this->input->post('bedno'),
            );

            $this->patient_model->updatebed($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function moveopd()
    {
        $custom_fields = $this->customfield_model->getByBelong('ipd');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[ipd][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $this->form_validation->set_rules('opd_id', $this->lang->line('opd_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|callback_valid_patient');
        $this->form_validation->set_rules('bed_no', $this->lang->line('bed_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('credit_limit', $this->lang->line('credit_limit'), 'trim|required|xss_clean');
        
        $consult      = $this->input->post('live_consult');
		if($consult == 'yes'){
			if (!$this->session->has_userdata('zoom_access_token')) {
				$this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
			}
		}
		
        if ($this->form_validation->run() == false) {		   
		   
			$msg = array(
                'appointment_date'  => form_error('appointment_date'),
                'bed_no'            => form_error('bed_no'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'opd_id'            => form_error('opd_id'),
                'patient_id'         => form_error('patient_id'),
                'credit_limit'         => form_error('credit_limit'),
                'zoom_live'         => form_error('zoom_live'),
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

            $opd_id      = $this->input->post('opd_id');
            $opd_pateint = $this->patient_model->getDetails($opd_id);
          
            if($this->input->post('pulse')){
              $pulse = $this->input->post('pulse');
            }else{
              $pulse ='';
            }
          	
         	if($this->input->post('temperature')){
              $temperature = $this->input->post('temperature');
            }else{
              $temperature ='';
            }
          
          	if($this->input->post('respiration')){
              $respiration = $this->input->post('respiration');
            }else{
              $respiration ='';
            }

            $live_consult=$this->input->post('live_consult');
            
            $ipd_array   = array(
                'patient_id'        => $opd_pateint['patient_id'],
                'bed'               => $this->input->post('bed_no'),
                'bed_group_id'      => $this->input->post('bed_group_id'),
                'case_reference_id' => $opd_pateint['case_reference_id'],                
                'case_type'         => $this->input->post('case'), 
                'casualty'          => $this->input->post('casualty'), 
                'symptoms'          => $this->input->post('symptoms'), 
                'known_allergies'   => $this->input->post('symptoms'),
                'date'              => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('appointment_date'), $this->time_format), 
                'note'              => $this->input->post('note'),
                'credit_limit'      => $this->input->post('credit_limit'),
                'refference'        => $this->input->post('refference'), 
                'cons_doctor'       => $this->input->post('consultant_doctor'), 
                'live_consult'      => $live_consult,
                'discharged'        => 'no',
                'is_antenatal'      => $this->input->post('is_for_antenatal'),
                'generated_by'      => $this->customlib->getLoggedInUserID(),
            );

            $moved = $this->patient_model->move_opd_to_ipd($ipd_array);

            if ($moved) {
                
            if ($live_consult == 'yes') {
                $ipdno             = $this->customlib->getSessionPrefixByType('ipd_no') . $moved;
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
                $this->load->library('zoom_api', $params);
                $setting_result = $this->setting_model->getzoomsetting();
                $ipdduration    = $setting_result->ipd_duration;

                $insert_array = array(
                    'staff_id'     => $this->input->post('consultant_doctor'), 
                    'patient_id'     => $opd_pateint['patient_id'],
                    'ipd_id'       => $moved,
                    'title'        => 'Online consult for ' . $ipdno,
                    'date'         =>  $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('appointment_date'), $this->time_format), 
                    'duration'     => $ipdduration,
                    'created_id'   => $this->customlib->getStaffID(),
                    'api_type'     => $api_type,
                    'host_video'   => 1,
                    'client_video' => 1,
                    'purpose'      => 'consult',
                    'password'     =>'123456',
                    'timezone'     => $this->customlib->getTimeZone(),
                );
                $response = $this->zoom_api->createAMeeting($insert_array);
				
				 if ($response['status']) {
                    if (isset($response['data'])) {               
                        $insert_array['return_response'] = json_encode($response['data']);
                        $conferenceid                    = $this->conference_model->add($insert_array);
                        $sender_details                  = array('patient_id' => $opd_pateint['patient_id'], 'conference_id' => $conferenceid, 'contact_no' => $opd_pateint['mobileno'], 'email' =>$opd_pateint['email']);

                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
                    }
                }
            }

                $update_opd_data = array('id' => $opd_pateint['id'], 'is_ipd_moved' => 1);
                $move_insert_id  = $this->patient_model->add_opd($update_opd_data, [], [], []);
                $bed_history     = array(
                    "case_reference_id" => $opd_pateint['case_reference_id'],
                    "bed_group_id"      => $this->input->post("bed_group_id"),
                    "bed_id"            => $this->input->post("bed_no"),
                    "from_date"         => $ipd_array['date'],
                    "is_active"         => "yes",
                );
                $this->bed_model->saveBedHistory($bed_history);
                
                $bed_data          = array('id' => $this->input->post('bed_no'), 'is_active' => 'no');
                $this->bed_model->savebed($bed_data);
                
                $array              = array('status' => 'success', 'message' => $this->lang->line('success_message'), 'move_id' => $moved);
                $custom_field_post  = $this->input->post("custom_fields[ipd]");
                $custom_value_array = array();
                if (!empty($custom_field_post)) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[ipd][" . $key . "]");
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
                    $this->customfield_model->insertRecord($custom_value_array, $moved);
                }
                $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('consultant_doctor'));
                $event_data     = array(
                    'patient_id'           => $this->input->post('patient_id'),
                    'symptoms_description' => $this->input->post('symptoms'),
                    'any_known_allergies'  => $opd_pateint['known_allergies'],
                    'appointment_date'     => $this->customlib->YYYYMMDDHisTodateFormat($this->input->post('appointment_date'), $this->customlib->getHospitalTimeFormat()),
                    'doctor_id'            => $this->input->post('consultant_doctor'),
                    'doctor_name'          => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                );

                $this->system_notification->send_system_notification('move_in_ipd_from_opd', $event_data);

            } else {
                $msg   = array('no_insert' => $this->lang->line('something_went_wrong'));
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }

        }

        echo json_encode($array);
    }

    public function moveipd($id)
    {
        $appointment_details = $this->patient_model->getDetails($id);
        $patient_name        = $appointment_details['patient_name'];
        $patient_id          = $appointment_details['id'];
        $gender              = $appointment_details['gender'];
        $email               = $appointment_details['email'];
        $phone               = $appointment_details['mobileno'];
        $doctor              = $appointment_details['cons_doctor'];
        $note                = $appointment_details['note'];
        $orgid               = $appointment_details['orgid'];
        $live_consult        = $appointment_details['live_consult'];
        $appointment_date    = date($this->customlib->getHospitalDateFormat(true, true), strtotime($appointment_details['appointment_date']));
        $amount              = $appointment_details['amount'];
        $allergies           = $appointment_details['opdknown_allergies'];
        $symptoms            = strip_tags($appointment_details['symptoms']);

        $patient_data = array(
            'patient_id'       => $patient_id,
            'patient_name'     => $patient_name,
            'gender'           => $gender,
            'email'            => $email,
            'phone'            => $phone,
            'appointment_date' => $appointment_date,
            'known_allergies'  => $allergies,
            'cons_doctor'      => $doctor,
            'orgid'            => $orgid,
            'live_consult'     => $live_consult,
        );

        $data['ipd_data'] = $patient_data;
        $updateData       = array('id' => $patient_id, 'is_ipd' => 'yes');
        $this->appointment_model->update($updateData);
        $this->session->set_flashdata('ipd_data', $data);
        redirect("admin/patient/ipdsearch/");
    }

    public function deleteVisit($id)
    {
        $this->patient_model->deleteVisit($id);
        $json_array = array('status' => 'success');
        echo json_encode($json_array);
    }

    public function setagerange()
    {
        $from_age         = $_REQUEST['from_age'];
        $to_age           = $_REQUEST['to_age'];
        $data['from_age'] = $from_age;
        $data['to_age']   = $from_age;
        $data['agerange'] = $this->agerange;
        $this->load->view("admin/patient/_getagerange", $data);
    }

    public function printCharge()
    {
        $type                  = $this->input->post('type');
        $print_details         = $this->printing_model->get('', $type);
        $id                    = $this->input->post('id');
        $charge                = $this->charge_model->getChargeById($id);         
        $data['print_details'] = $print_details;
        $data['charge']        = $charge;
        $page                  = $this->load->view('admin/patient/_printCharge', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printVisit()
    {
        $print_details = $this->printing_model->get('', 'opd');

        $data['print_details'] = $print_details;
        $visit_detail_id       = $this->input->post('visit_detail_id');
        $charge                = array();
        $patient               = $this->patient_model->getopdvisitDetailsbyvisitid($visit_detail_id);
        $charge                = $this->charge_model->getChargeById($patient['patient_charge_id']);
        $transaction           = $this->transaction_model->getTransaction($patient['transaction_id']);
        $data['charge']        = $charge;
        $data['transaction']   = $transaction;
        $data['patient']       = $patient;
        $page = $this->load->view('admin/patient/_printVisit', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getpatientBycaseId($case_reference_id)
    {		 
        $patient = $this->patient_model->getDetailsByCaseId($case_reference_id);
 
        if (!empty($patient['patient_id'])) {
            $status       = 1;
            $patient_id   = $patient['patient_id'];
            $patient_name = $patient['patient_name'];
        } else {
            $status       = 0;
            $patient_id   = 0;
            $patient_name = "";
        }

        echo json_encode(array('status' => $status, 'patient_id' => $patient_id, 'patient_name' => $patient_name . " (" . $patient_id . ")"));

    }

    public function deletemedication()
    {
        $id = $this->input->post('id');
        $this->patient_model->deletemedicationByID($id);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo json_encode($array);
    }

    public function getPatientListAjax()
    {
        $search_term = $this->input->post("searchTerm");
        if (isset($search_term) && $search_term != '') {
            $result = $this->patient_model->getPatientListfilter($search_term);
            $data   = array();
            if (!empty($result)) {

                foreach ($result as $value) {
                    $data[] = array("id" => $value->id, "text" => $value->patient_name . " (" . $value->id . ")");
                }
            }
            echo json_encode($data);
        }
    }

    public function patientVisitReport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/patient');
        $this->session->set_userdata('subsub_menu', 'reports/patient/patientvisitreport');        
        
        $this->form_validation->set_rules(
            'patient_id', $this->lang->line('patient_id'), array(
                'required',
                array('check_patient_exists', array($this->patient_model, 'check_patient_exists')),
            )
        );       
        
        if ($this->form_validation->run() == false) {
            $data = array();
            $this->load->view("layout/header");
            $this->load->view("admin/patient/patientVisitReport", $data);
            $this->load->view("layout/footer");
        } else {

            $patient_id   = $this->input->post("patient_id");
            $patient_data = $this->notificationsetting_model->getpatientDetails($patient_id);
            if (!empty($patient_data)) {
                $data["patient_name"] = $patient_data['patient_name'];
                $data["patient_id"]   = $patient_id;
            }
            $data["opd_data"]        = $this->patient_model->getopdvisitreportdata($patient_id);
            $data["ipd_data"]        = $this->patient_model->getipdvisitreportdata($patient_id);
            $data["pharmacy_data"]   = $this->patient_model->getPatientPharmacyVisitDetails($patient_id);
            $data["radiology_data"]  = $this->patient_model->getPatientRadiologyVisitDetails($patient_id);
            $data["blood_bank_data"] = $this->patient_model->getPatientBloodBankVisitDetails($patient_id);
            $data["ambulance_data"]  = $this->patient_model->getPatientAmbulanceVisitDetails($patient_id);
            $data['pathology_data']  = $this->report_model->getAllpathologybillRecord($patient_id);

            $this->load->view("layout/header");
            $this->load->view("admin/patient/patientVisitReport", $data);
            $this->load->view("layout/footer");
        }
    }

    public function patientbillreport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/finance');
        $this->session->set_userdata('subsub_menu', 'reports/patient/patientbillreport');

        $this->form_validation->set_rules("case_reference_id", $this->lang->line("case_id"), "trim|required|numeric");

        unset($_SESSION['no_record']);
        if ($this->form_validation->run() == false) {
            $this->load->view("layout/header");
            $this->load->view("admin/patient/patientBillReport");
            $this->load->view("layout/footer");
        } else {
            $case_reference_id           = $this->input->post("case_reference_id");
            $opd_data                    = $this->patient_model->getPatientChargePaymentOPD($case_reference_id);
            $ipd_data                    = $this->patient_model->getPatientChargePaymentIPD($case_reference_id);
            $pharmacy_data               = $this->patient_model->getPatientChargePaymentPharmacy($case_reference_id);
            $pathology_data              = $this->patient_model->getPatientChargePaymentPathology($case_reference_id);
            $radiology_data              = $this->patient_model->getPatientChargePaymentRadiology($case_reference_id);
            $ambulance_data              = $this->patient_model->getPatientChargePaymentAmbulance($case_reference_id);
            $bloodbank_data              = $this->patient_model->getPatientChargePaymentBloodBank($case_reference_id);
            $data['total_refund_amount']           = $this->transaction_model->getTotalRefundAmountByCaseId($case_reference_id);
            $data["charge_payment_data"] = array_merge($opd_data, $ipd_data, $pharmacy_data, $pathology_data, $radiology_data, $ambulance_data, $bloodbank_data);
            if(empty($data["charge_payment_data"])){
                $this->session->set_flashdata('no_record', '<div class="alert alert-danger ">'.$this->lang->line("no_record_found").'</div>');
            }
            
            $this->load->view("layout/header");
            $this->load->view("admin/patient/patientBillReport", $data);
            $this->load->view("layout/footer");
        }
    }

    public function getpatientage()
    {
        $birth_date = $_REQUEST['birth_date'];
        $dob        = $this->customlib->dateFormatToYYYYMMDD($birth_date);
        $agr_array  = array();
        if (!empty($dob)) {
            
            $bday = new DateTime($dob); // Your date of birth
            $today = new Datetime('today');            
            $diff = $today->diff($bday);   
            
            $agr_array['year']  = $diff->y;
            $agr_array['month'] = $diff->m;
            $agr_array['day']   = $diff->d;
        }
        echo json_encode($agr_array);
    }

    public function findingbycategory()
    {       
        $id                   = $_REQUEST['finding_id'];       
        $data['finding_list'] = $this->finding_model->getbyfindingbyarr($id);
        $section_page         = $this->load->view('admin/patient/_getfindinglist', $data, true);

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(array(
                'status' => 1,
                'record' => $section_page,
            )));
    }

    public function getfinding()
    {
        $id             = $_REQUEST['head_id'];
        $finding_result = $this->finding_model->get($id);
        echo $finding_result['description'];
    }

    public function getinvestigationparameter()
    {
        $lab = $_REQUEST['lab'];
        if ($lab == 'pathology') {

            $actions        = "";
            $id             = $_REQUEST['id'];
            $result         = $this->pathology_model->getPatientPathologyReportDetails($id);
            $data['result'] = $result;
            $page           = $this->load->view('admin/pathology/_labinvestigations', $data, true);

            $actions = "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_bill' data-toggle='tooltip' data-record-id=\"" . $id . "\" data-type-id='" . $lab . "'  data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
			
			$test = $result->test_name.' ('.$result->short_name.')' ;
			
            echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions, 'test' => $test));

        } else {
            $actions               = "";
            $print_details         = $this->printing_model->get('', 'radiology');
            $data['print_details'] = $print_details;

            $id             = $_REQUEST['id'];
            $result         = $this->radio_model->getPatientRadiologyReportDetails($id);
            $data['result'] = $result;
            $page = $this->load->view('admin/radio/_labinvestigations', $data, true);

            $actions = "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_bill' data-toggle='tooltip' data-record-id=\"" . $id . "\"  data-type-id='" . $lab . "' data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
			
			$test = $result->test_name.' ('.$result->short_name.')' ;

            echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions, 'test' => $test));

        }
    }

    public function printpathoparameter()
    {
        $lab = $_REQUEST['lab'];
        if ($lab == 'pathology') {
            $print_details         = $this->printing_model->get('', 'pathology');
            $data['print_details'] = $print_details;
            $id                    = $this->input->post('id');
            $data['id']            = $id;
            if (isset($_POST['print'])) {
                $data["print"] = 'yes';
            } else {
                $data["print"] = 'no';
            }

            $result         = $this->pathology_model->getPatientPathologyReportDetails($id);
            $data['fields'] = $this->customfield_model->get_custom_fields('pathology', 1);
            $data['result'] = $result;
            $page           = $this->load->view('admin/pathology/_printlabinvestigations', $data, true);
            echo json_encode(array('status' => 1, 'page' => $page));
        } else {
            $actions               = "";
            $print_details         = $this->printing_model->get('', 'radiology');
            $data['print_details'] = $print_details;
            $id                    = $_REQUEST['id'];
            $result                = $this->radio_model->getPatientRadiologyReportDetails($id);
            $data['result']        = $result;
            $page                  = $this->load->view('admin/radio/_printlabinvestigations', $data, true);
            echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
        }
    }

    public function getopdtreatmenthistory()
    {
        $patientid   = $this->uri->segment(4);
        $dt_response = $this->patient_model->getopdtreatmenthistory($patientid);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $opd_id           = $value->opd_id;
                $visit_details_id = $value->visit_id;
                $check = $this->db->where("visit_details_id", $visit_details_id)->get('ipd_prescription_basic');
                if ($check->num_rows() > 0) {
                    $result[$key]['prescription'] = 'yes';
                } else {
                    $result[$key]['prescription'] = 'no';
                    $userdata                     = $this->customlib->getUserData();
                    if ($this->session->has_userdata('hospitaladmin')) {
                        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                        if ($doctor_restriction == 'enabled') {
                            if ($userdata["role_id"] == 3) {
                                if ($userdata["id"] == $value->staff_id) {

                                } else {
                                    $result[$key]['prescription'] = 'not_applicable';
                                }
                            }
                        }
                    }
                }

                $action = "<div class=''>";

                $action .= "<a href='javascript:void(0)' data-loading-text='" . $this->lang->line('please_wait') . "' data-opd-id=" . $opd_id . " data-record-id=" . $visit_details_id . " class='btn btn-default btn-xs get_opd_detail'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";
                $action .= "</div>";
                $first_action = "<a href=" . base_url() . 'admin/patient/visitdetails/' . $value->pid . '/' . $opd_id . ">";
                
                if($value->symptoms){
                    $symptoms = nl2br($value->symptoms);
                }else{
                    $symptoms = '';
                }
                
                //==============================
                $row[] = $first_action . $this->opd_prefix . $opd_id . "</a>";
                $row[] = $value->case_reference_id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->appointment_date, $this->time_format);
                $row[] = $symptoms;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[]     = $action;
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

    public function getipdtreatmenthistory($id)
    {
        $dt_response = $this->patient_model->getipdtreatmenthistory($id);
        $fields      = $this->customfield_model->get_custom_fields('ipd', 1);
        $userdata    = $this->customlib->getUserData();
        $role_id     = $userdata['role_id'];
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $id                = $value->id;
                $ipdid             = $value->ipdid;
                $discharge_details = $this->patient_model->getIpdBillDetails($id, $ipdid);
                $action            = "<div class='rowoptionview'>";

                if ($this->rbac->hasPrivilege('ipd_patient', 'can_view')) {
                    $action .= "<a href=" . base_url() . 'admin/patient/ipdprofile/' . $value->ipdid . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";
                }

                $action .= "</div'>";
                $first_action = "<a href=" . base_url() . 'admin/patient/ipdprofile/' . $value->ipdid . ">";
                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('ipd_no') . $value->ipdid;
                $row[] = $value->symptoms;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $value->bed_name . "-" . $value->bedgroup_name . "-" . $value->floor_name;
                //====================                
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

    /* for load patient visit details modal */
    public function getPatientPathologyDetails()
    {
        $actions     = "";
        $module_type = $this->input->post('module_name');

        if ($module_type == 'radiology') {
            $id                  = $this->input->post('id');
            $data['id']          = $id;
            $result              = $this->radio_model->getRadiologyBillByID($id);
            $data['bill_prefix'] = $this->customlib->getSessionPrefixByType('radiology_billing');
            $is_bill             = $this->input->post('is_bill');
            if (isset($is_bill)) {
                $data['is_bill'] = false;
            } else {
                $data['is_bill'] = true;
            }

            $data['fields'] = $this->customfield_model->get_custom_fields('radiology');
            $data['result'] = $result;
            $page           = $this->load->view('admin/patient/visitreport/_getPatientRadiologyDetails', $data, true);
            $actions        = "";
        } else if ($module_type == 'blood_issue') {
            $id                  = $this->input->post("id");
            $data['result']      = $this->bloodissue_model->getDetail($id);
            $data['fields']      = $this->customfield_model->get_custom_fields('blood_issue');
            $data['bill_prefix'] = $this->customlib->getSessionPrefixByType('blood_bank_billing');
            $page = $this->load->view('admin/patient/visitreport/_getBloodIssueDetail', $data, true);
        } else if ($module_type == 'component_issue') {
            $id             = $this->input->post("id");
            $data['result'] = $this->bloodissue_model->getcomponentDetail($id);
            $data['prefix'] = $this->customlib->getSessionPrefixByType('blood_bank_billing');
            $data['fields'] = $this->customfield_model->get_custom_fields('component_issue');
            $page = $this->load->view('admin/patient/visitreport/_getcomponentIssueDetail', $data, true);
        } else {
            $is_bill    = $this->input->post('is_bill');
            $id         = $this->input->post('id');
            $data['id'] = $id;
            $result     = $this->pathology_model->getPathologyBillByID($id);
            $data['result'] = $result;
            if (isset($is_bill)) {
                $data['is_bill'] = false;
            } else {
                $data['is_bill'] = true;
            }
            $data['bill_prefix']           = $this->customlib->getSessionPrefixByType('pathology_billing');
            $data['fields']                = $this->customfield_model->get_custom_fields('pathology');
            $data['pathology_test_fields'] = $this->customfield_model->get_custom_fields('pathologytest');
            $page                          = $this->load->view('admin/patient/visitreport/_getPatientPathologyDetails', $data, true);
        }

        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
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
        $this->load->view('admin/patient/visitreport/printBill', $data);
    }

    public function getpharmacybilldetails()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $id      = $this->input->get('id');
        $print   = $this->input->get('print');
        $is_bill = $this->input->get('is_bill');
        $is_bill = $this->input->get('is_bill');
        $print_details         = $this->printing_model->get('', 'pharmacy');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($print)) {
            $data["print"] = true;
            $check_print   = 'print';
        } else {
            $data["print"] = false;
            $check_print   = '';
        }

        if (isset($is_bill)) {
            $data["is_bill"] = false;
            $bill_print      = "print_pharmacy_bill";
        } else {
            $data["is_bill"] = true;
            $bill_print      = "print_bill";
        }

        if ($check_print == 'print') {
            $data['fields']      = $this->customfield_model->get_custom_fields('pharmacy', '', 1);
            $data['check_print'] = $check_print;
        } else {
            $data['fields']      = $this->customfield_model->get_custom_fields('pharmacy');
            $data['check_print'] = $check_print;
        }

        $result = $this->pharmacy_model->getBillDetails($id, $data['check_print']);

        $data['result'] = $result;
        $bill_no    = $result['id'];
        $patient_id = $result['patient_id'];
        $detail = $this->pharmacy_model->getAllBillDetails($id);
        $data['detail'] = $detail;
        $action_details = "";
        $page = $this->load->view('admin/patient/visitreport/_getBillDetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $action_details));
    }

    public function deletemedicine()
    {
        $prescription_detail_id = $this->input->post('prescription_detail_id');
        $this->prescription_model->deletemedicine($prescription_detail_id);       
    }

    public function getopdpaymentdetails(){

        $payment_id= $this->input->post('payment_id');
        $data= $this->transaction_model->getopdpaymentdetails($payment_id);
        $result['payment_mode'] = $data['payment_mode'];
        if($data['cheque_date']){
            $result['cheque_date']  = $this->customlib->YYYYMMDDTodateFormat($data['cheque_date']);
        }else{
            $result['cheque_date']  = '';
        }
        if($data['payment_date']){
            $result['payment_date']  = $this->customlib->YYYYMMDDHisTodateFormat($data['payment_date']);
        }else{
            $result['payment_date']  = '';
        }
        $result['cheque_no']    = $data['cheque_no'];
        $result['note']         = $data['note'];
        echo json_encode($result);
    }

    public function editpayment()
    {
        $this->form_validation->set_rules('amount',$this->lang->line('amount'),'required|trim|xss_clean');
        $this->form_validation->set_rules('payment_date', $this->lang->line('payment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'trim|required|xss_clean');       
        
        if ($_POST['payment_mode'] == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line("document"), 'callback_handle_doc_upload[document]');
        }

        if($this->form_validation->run()==false){
            $error =array(                
                'payment_mode' => form_error('payment_mode'),
                'payment_date' => form_error('payment_date'),
                'cheque_date'  => form_error('cheque_date'),
                'cheque_no'    => form_error('cheque_no'),
                'document'     => form_error('document'),
                'amount'     => form_error('amount'),
            );
            $arr = array('status'=>0,'message'=> '', 'error'=> $error);

        }else{

            $payment_id  = $this->input->post('edit_payment_id');
            $amount     = $this->input->post('amount');
            $payment_date    = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post("payment_date"), $this->time_format);
            $cheque_date     = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date"));
            $amount          = $amount;
            $payment_section = $this->config->item('payment_section');
            $trasaction_data['payment_mode'] = $this->input->post('payment_mode');
            $trasaction_data['amount']  = $amount ;
            $trasaction_data['id']      = $payment_id ;
            $trasaction_data['payment_date'] = $payment_date ;
            $trasaction_data['note'] = $this->input->post('note');
            $trasaction_data['cheque_date']     = null;
            $trasaction_data['cheque_no']       = null;
            $trasaction_data['attachment']      = null;
            $trasaction_data['attachment_name'] = null;

            $this->transaction_model->add($trasaction_data);

            $attachment      = "";
            $attachment_name = "";

            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }
			
            if ($this->input->post('payment_mode') == "Cheque") {
                $data['id']              = $payment_id;
                $data['cheque_date']     = $cheque_date;
                $data['cheque_no']       = $this->input->post('cheque_no');
                $data['attachment']      = $attachment;
                $data['attachment_name'] = $attachment_name;
                $this->transaction_model->add($data);
            }

            $arr = array('status'=>1,'message'=> $this->lang->line('update_message'),'error'=>'');
        }
       
        echo json_encode($arr);
    }

}
