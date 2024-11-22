<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mailgateway
{

    private $_CI;

    public function __construct()
    {
        $this->_CI = &get_instance();
        $this->_CI->load->model('setting_model');
        $this->_CI->load->model('appointment_model');
        $this->_CI->load->library('mailer');
        $this->_CI->load->model('payment_model');
        $this->_CI->mailer;
        $this->hospital_setting = $this->_CI->setting_model->get();
    }

    public function sentRegisterMailOPD($id, $send_to, $opdid, $template, $subject, $appointment_date)
    {
        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $msg = $this->getPatientRegistrationContentOPD($id, $opdid, $template,$appointment_date);
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }

    public function sentRegistrationNotificationOPD($id,$opdid,$template,$appointment_date,$subject)
    {
        $patient_result = $this->_CI->patient_model->getpatientDetails($id);
        $msg = $this->getPatientRegistrationContentOPD($id, $opdid, $template,$appointment_date);
        $push_array = array(
            'title' => $subject,
            'body' => $msg
        );
         if ($patient_result['app_key'] != "") {
             $this->_CI->pushnotification->send($patient_result['app_key'], $push_array, "mail_sms");
         }
    }

    public function sentRegisterMailIPD($id, $send_to, $ipdid, $template, $subject)
    {
        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $msg = $this->getPatientRegistrationContentIPD($id, $ipdid, $template);
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }

    public function sentRegistrationNotificationIPD($id,$ipdid,$template,$subject) {
        $patient_result = $this->_CI->patient_model->getpatientDetails($id);
        $msg = $this->getPatientRegistrationContentIPD($id, $ipdid, $template);
        $push_array = array(
            'title' => $subject,
            'body' => $msg
        );        
      
         if ($patient_result['app_key'] != "") {
             $this->_CI->pushnotification->send($patient_result['app_key'], $push_array, "mail_sms");
         }
    }

    public function sentLiveconsultMail($id, $send_to, $conference_id, $template, $subject)
    {
        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $msg = $this->getPatientLiveConsultContent($id, $conference_id, $template);
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }
   
    public function sentLiveconsultNotification($id,$conference_id,$template,$subject) 
    {
        $patient_result = $this->_CI->patient_model->getpatientDetails($id);
        $msg = $this->getPatientLiveConsultContent($id, $conference_id, $template);
        $push_array = array(
            'title' => $subject,
            'body' => $msg
        );
         if ($patient_result['app_key'] != "") {
             $this->_CI->pushnotification->send($patient_result['app_key'], $push_array, "mail_sms");
         }
    }

    public function sentOnlineMeetingStaffMail($detail, $template, $subject)
    {
        if (!empty($this->_CI->mail_config)) {
            foreach ($detail as $staff_key => $staff_value) {
                $send_to = $staff_key;
                if ($send_to != "") {
                    $msg = $this->getOnlineMeetingStaffContent($staff_value, $template);
                    $this->_CI->mailer->send_mail($send_to, $subject, $msg);
                }
            }
        }
    }

    public function sentDischargedMail($details,$template, $subject)
    {          
        $send_to = $details['email'];
        if (!empty($this->_CI->mail_config)) {
            $msg = $this->getPatientDischargedContent($details,$template);
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }         
    }

    public function sentDischargedNotificationIPD($details,$template, $subject) 
    {        
        $patient_result = $this->_CI->patient_model->getpatientDetails($details['patient_id']);
        $msg = $this->getPatientDischargedContent($details,$template);
        $push_array = array(
            'title' =>  $subject,
            'body' => $msg
        );
         if ($patient_result['app_key'] != "") {
             $this->_CI->pushnotification->send($patient_result['app_key'], $push_array, "mail_sms");
         }
    }

    public function sentopdDischargedMail($details, $template, $subject)
    {         
        if (!empty($this->_CI->mail_config)) {
            $send_to = $details['email'];
            $msg = $this->getopdPatientDischargedContent($details, $template);
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }       
    }

    public function sentDischargedNotificationOPD($details,$template,$subject) 
    {
        $patient_result = $this->_CI->patient_model->getpatientDetails($details['patient_id']);        
        $msg = $this->getopdPatientDischargedContent($details, $template);
        $push_array = array(
            'title' => $subject,
            'body' => $msg
        );
        if ($patient_result['app_key'] != "") {
             $this->_CI->pushnotification->send($patient_result['app_key'], $push_array, "mail_sms");
        }
    }

    public function sentAppointmentConfirmation($sender_details, $send_to, $appointment_id, $template, $subject)
    {      
        if (!empty($this->_CI->mail_config)) {
            $msg = $this->getAppointmentConfirmationContent($sender_details, $appointment_id, $template);
         
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }         
    }

    public function sentAppointmentConfirmationNotification($sender_details,$appointment_id,$template,$subject) 
    {
        $patient_result = $this->_CI->patient_model->getpatientDetails($sender_details['patient_id']);
        $msg = $this->getAppointmentConfirmationContent($sender_details, $appointment_id, $template);
        $push_array = array(
            'title' => $subject,
            'body' => $msg
        );       
       
         if ($patient_result['app_key'] != "") {
             $this->_CI->pushnotification->send($patient_result['app_key'], $push_array, "mail_sms");
         }
    }

    public function sendLoginCredential($chk_mail_sms, $sender_details, $template, $subject)
    {
        $msg     = $this->getLoginCredentialContent($sender_details['credential_for'], $sender_details, $template);
        $send_to = $sender_details['email'];
        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }

    public function getPatientRegistrationContentOPD($id, $opdid, $template,$appointment_date)
    {        
        $patient = $this->_CI->patient_model->getDetails($opdid);
        $patient['opdno'] = $this->_CI->customlib->getSessionPrefixByType('opd_no').$opdid;
        $patient['appointment_date'] = $appointment_date ;       
        foreach ($patient as $key => $value) {           
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }
      
        return $template;
    }

    public function getPatientRegistrationContentIPD($id, $ipdid, $template)
    {        
        $patient = $this->_CI->patient_model->getIpdDetails($ipdid);
        $patient['ipd_no'] = $this->_CI->customlib->getSessionPrefixByType('ipd_no').$ipdid;
        foreach ($patient as $key => $value) {            
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }
        return $template;
    }

    public function getPatientLiveConsultContent($id, $conference_id, $template)
    {
        $conference = $this->_CI->conference_model->getconference($conference_id);
        foreach ($conference as $key => $value) {           
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }
        return $template;
    }

    public function getPatientDischargedContent($details,$template)
    {        
        foreach ($details as $key => $value) {           
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }
        return $template;
    }

    public function getopdPatientDischargedContent($details, $template)
    {  
        if(!empty($details)){
            foreach ($details as $key => $value) {                
                $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
            }
        }        
        return $template;
    }

    public function getAppointmentConfirmationContent($sender_details, $appointment_id, $template)
    {
        $patient = $this->_CI->appointment_model->getDetailsFornotification($appointment_id);
        $patient['patient_name'] = $sender_details['patient_name'];
        foreach ($patient as $key => $value) {            
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }
        return $template;
    }

    public function getOnlineMeetingStaffContent($staff_detail, $template)
    {
        foreach ($staff_detail as $key => $value) {            
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }
        return $template;
    }

    public function getLoginCredentialContent($credential_for, $sender_details, $template)
    {
        if ($credential_for == "patient") {
            $patient                        = $this->_CI->patient_model->patientProfileDetails($sender_details['id']);
            $sender_details['url']          = site_url('site/userlogin');
            $sender_details['display_name'] = $patient['patient_name'];
        } elseif ($credential_for == "staff") {
            $staff                          = $this->_CI->staff_model->get($sender_details['id']);
            $sender_details['url']          = site_url('site/login');
            $sender_details['display_name'] = $staff['name'] . " " . $staff['surname'];
        }
		
        foreach ($sender_details as $key => $value) {
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }

        return $template;
    }
  
}
