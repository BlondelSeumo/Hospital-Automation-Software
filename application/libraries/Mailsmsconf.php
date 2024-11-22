<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mailsmsconf
{

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load("mailsms");
        $this->CI->load->library('smsgateway');
        $this->CI->load->library('mailgateway');
        $this->CI->load->library('customlib');
        $this->CI->load->library('pushnotification');
        $this->CI->load->model('notificationsetting_model');
        $this->config_mailsms = $this->CI->config->item('mailsms');
    }

    public function mailsms($send_for, $sender_details, $date = null)
    {       
        $chk_mail_sms = $this->CI->customlib->sendMailSMS($send_for);
        $currency_symbol = $this->CI->customlib->getHospitalCurrencyFormat();
      
        if(isset($sender_details['patient_id']) && $sender_details['patient_id']!=""){
              $patient_data=$this->CI->notificationsetting_model->getpatientDetails($sender_details['patient_id']);
              $sender_details['email']              = $patient_data['email'];
              $sender_details['patient_name']       = composePatientName($patient_data['patient_name'], $sender_details['patient_id']);
              $sender_details['mobileno']           = $patient_data['mobileno'];
              $sender_details['dob']                = $patient_data['dob'];
               $sender_details['gender']            = $patient_data['gender'];
              $sender_details['currency_symbol']    = $currency_symbol;
        }

        if (!empty($chk_mail_sms)) {
            if (($send_for == "opd_patient_registration") ) {
                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {
                    $this->CI->mailgateway->sentRegisterMailOPD($sender_details['patient_id'], $sender_details['email'], $sender_details['opd_details_id'], nl2br($chk_mail_sms['template']), $chk_mail_sms['subject'],$sender_details['appointment_date']);
                }
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] != "") {

                    $this->CI->smsgateway->sentRegisterSMSOPD($sender_details['patient_id'], $sender_details['contact_no'], $sender_details['opd_details_id'], $chk_mail_sms['template'], $chk_mail_sms['subject'],$chk_mail_sms['template_id'],$sender_details['appointment_date']);
                }
                if ($chk_mail_sms['mobileapp']) {
                    $this->CI->mailgateway->sentRegistrationNotificationOPD($sender_details['patient_id'],$sender_details['opd_details_id'], $chk_mail_sms['template'],$sender_details['appointment_date'], $chk_mail_sms['subject']);                    
                }

            } elseif ($send_for == "ipd_patient_registration") {

                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {
                    $this->CI->mailgateway->sentRegisterMailIPD($sender_details['patient_id'], $sender_details['email'], $sender_details['ipdid'], nl2br($chk_mail_sms['template']), $chk_mail_sms['subject']); 
                }
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] != "") {
               
                    $this->CI->smsgateway->sentRegisterSMSIPD($sender_details['patient_id'], $sender_details['contact_no'], $sender_details['ipdid'], $chk_mail_sms['template'], $chk_mail_sms['subject'],$chk_mail_sms['template_id'],$sender_details['ipdid']);
                }
                if ($chk_mail_sms['mobileapp']) {
                    $this->CI->mailgateway->sentRegistrationNotificationIPD($sender_details['patient_id'],$sender_details['ipdid'],$chk_mail_sms['template'], $chk_mail_sms['subject']);                    
                }
            } elseif ($send_for == "ipd_patient_discharged") {

                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {
                    $this->CI->mailgateway->sentDischargedMail($sender_details,nl2br($chk_mail_sms['template']), $chk_mail_sms['subject']);
                }
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] != "") {
                  
                    $this->CI->smsgateway->sentDischargedSMS($sender_details, $chk_mail_sms['template'], $chk_mail_sms['template_id']);
                }
                if ($chk_mail_sms['mobileapp']) {
                   
                    $this->CI->mailgateway->sentDischargedNotificationIPD($sender_details,$chk_mail_sms['template'], $chk_mail_sms['subject']);
                    
                }
            } elseif ($send_for == "opd_patient_discharged") {
                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {
                   
                    $this->CI->mailgateway->sentopdDischargedMail($sender_details, nl2br($chk_mail_sms['template']), $chk_mail_sms['subject']);
                }
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] != "") {
                    $this->CI->smsgateway->sentopdDischargedSMS($sender_details, $chk_mail_sms['template'], $chk_mail_sms['template_id']);
                }
                if ($chk_mail_sms['mobileapp']) {
                    $this->CI->mailgateway->sentDischargedNotificationOPD($sender_details,$chk_mail_sms['template'], $chk_mail_sms['subject']);
                    
                }
            } elseif ($send_for == "appointment_approved") {
               
                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {
                    $this->CI->mailgateway->sentAppointmentConfirmation($sender_details, $sender_details['email'], $sender_details['appointment_id'], nl2br($chk_mail_sms['template']), $chk_mail_sms['subject']);
                }
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] != "") {
                    $this->CI->smsgateway->sentAppointmentConfirmation($sender_details, $sender_details['mobileno'], $sender_details['appointment_id'], $chk_mail_sms['template'], $chk_mail_sms['template_id']);
                }
                if ($chk_mail_sms['mobileapp']) {
                    $this->CI->mailgateway->sentAppointmentConfirmationNotification($sender_details,$sender_details['appointment_id'],$chk_mail_sms['template'], $chk_mail_sms['subject']);
                    
                }
            } elseif ($send_for == "login_credential") {

                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {

                    $this->CI->mailgateway->sendLoginCredential($chk_mail_sms, $sender_details, nl2br($chk_mail_sms['template']), $chk_mail_sms['subject']);
                }
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] != "") {
                    $this->CI->smsgateway->sendLoginCredential($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['template_id'],$send_for);
                }

            } else if ($send_for == "live_consult") {
                if ($chk_mail_sms['mail']) {
                    $this->CI->mailgateway->sentLiveconsultMail($sender_details['patient_id'], $sender_details['email'], $sender_details['conference_id'], nl2br($chk_mail_sms['template']), $chk_mail_sms['subject']);
                }
                if ($chk_mail_sms['sms']) {
                    $this->CI->smsgateway->sentLiveconsultSMS($sender_details['patient_id'], $sender_details['contact_no'], $sender_details['conference_id'], $chk_mail_sms['template'], $chk_mail_sms['template_id']);
                }
                if ($chk_mail_sms['mobileapp']) {
                    $this->CI->mailgateway->sentLiveconsultNotification($sender_details['patient_id'],$sender_details['conference_id'],$chk_mail_sms['template'], $chk_mail_sms['subject']);
                }
            } elseif ($send_for == "live_meeting") {

                $this->sendMeeting($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject'],$chk_mail_sms['template_id']);
            } 
            
            else {

            }
        }
    }

    public function sendMeeting($chk_mail_sms, $staff_details, $template, $subject,$template_id)
    {
        $staff_sms_list   = array();
        $staff_email_list = array();

        if ($chk_mail_sms['mail'] or $chk_mail_sms['sms']) {

            if (!empty($staff_details)) {
                foreach ($staff_details as $staff_key => $staff_value) {

                    if ($staff_value['email'] != "") {
                        $staff_email_list[$staff_value['email']] = array(
                            'title'       => $staff_value['title'],
                            'date'        => $staff_value['date'],
                            'duration'    => $staff_value['duration'],
                            'employee_id' => $staff_value['employee_id'],
                            'department'  => $staff_value['department'],
                            'designation' => $staff_value['designation'],
                            'name'        => $staff_value['name'],
                            'contact_no'  => $staff_value['contact_no'],
                            'email'       => $staff_value['email'],
                        );
                    }

                    if ($staff_value['contact_no'] != "") {
                        $staff_sms_list[$staff_value['contact_no']] = array(
                            'title'       => $staff_value['title'],
                            'date'        => $staff_value['date'],
                            'duration'    => $staff_value['duration'],
                            'employee_id' => $staff_value['employee_id'],
                            'department'  => $staff_value['department'],
                            'designation' => $staff_value['designation'],
                            'name'        => $staff_value['name'],
                            'contact_no'  => $staff_value['contact_no'],
                            'email'       => $staff_value['email'],
                        );
                    }
                }
                if ($chk_mail_sms['mail']) {
                    if ($staff_email_list) {
                        $this->CI->mailgateway->sentOnlineMeetingStaffMail($staff_email_list, $template, $subject);
                    }
                } 

                if ($chk_mail_sms['sms']) {

                    if ($staff_sms_list) {
                        $this->CI->smsgateway->sentOnlineMeetingStaffSMS($staff_sms_list, $template,$template_id);
                    }
                }
            }
        }
    }

}
