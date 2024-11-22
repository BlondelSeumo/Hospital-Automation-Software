<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Smsgateway
{

    private $_CI;
    private $hospital_setting;

    public function __construct()
    {

        $this->_CI = &get_instance();
        $this->_CI->load->model('setting_model');
        $this->_CI->load->model('staff_model');
        $this->_CI->load->model('appointment_model');
        $this->_CI->load->model('smsconfig_model');
        $this->_CI->load->model('payment_model');
        $this->hospital_setting = $this->_CI->setting_model->get();
    }

    public function sendSMS($send_to, $msg, $template_id)
    {        
       
        $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();
		
        if (!empty($sms_detail)) {
            if ($sms_detail->type == 'clickatell') {
                $params = array(
                    'apiToken' => $sms_detail->api_id,
                );
                $this->_CI->load->library('clickatell', $params);
                try {
                    $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                    foreach ($result['messages'] as $message) {

                    }
                    return true;
                } catch (Exception $e) {
                    return true;
                }
            } else if ($sms_detail->type == 'twilio') {
                $params = array(
                    'mode'        => 'sandbox',
                    'account_sid' => $sms_detail->api_id,
                    'auth_token'  => $sms_detail->password,
                    'api_version' => '2010-04-01',
                    'number'      => $sms_detail->contact,
                );

                $this->_CI->load->library('twilio', $params);
                $from     = $sms_detail->contact;
                $to       = $send_to;
                $message  = $msg;
                $response = $this->_CI->twilio->sms($from, $to, $message); 
                if ($response->IsError) {
                    return true;
                } else {
                    return true;
                }

            } else if ($sms_detail->type == 'msg_nineone') {
              
                $params = array(
                    'authkey'  => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'templateid' => $template_id
                ); 
               
                $this->_CI->load->library('msgnineone', $params);
                $this->_CI->msgnineone->sendSMS($send_to, $msg,$template_id);
            } else if ($sms_detail->type == 'smscountry') {
                $params = array(
                    'username' => $sms_detail->username,
                    'senderid' => $sms_detail->senderid,
                    'password' => $sms_detail->password,
                );
                $this->_CI->load->library('smscountry', $params);
                $this->_CI->smscountry->sendSMS($send_to, $msg,$template_id); 
            } else if ($sms_detail->type == 'text_local') {
                $to     = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'hash'     => $sms_detail->password,
                );
                $this->_CI->load->library('textlocalsms', $params);
                $this->_CI->textlocalsms->sendSms(array($to), $msg, $sms_detail->senderid);
            }else if ($sms_detail->type == 'bulk_sms') {
                $to = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'password' => $sms_detail->password,
                );

                $this->_CI->load->library('bulk_sms_lib', $params);
                $this->_CI->bulk_sms_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'mobireach') {
                $to = $send_to;
                $params = array(
                    'authkey' => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'routeid' => $sms_detail->api_id,
                );
                $this->_CI->load->library('mobireach_lib', $params);
                $this->_CI->mobireach_lib->sendSms(array($to), $msg);
 
            } else if ($sms_detail->type == 'nexmo') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_secret' => $sms_detail->authkey,
                );
                $this->_CI->load->library('nexmo_lib', $params);
                $this->_CI->nexmo_lib->sendSms($to, $msg);

            } else if ($sms_detail->type == 'africastalking') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_username' => $sms_detail->username,
                );
                $this->_CI->load->library('africastalking_lib', $params);
                $this->_CI->africastalking_lib->sendSms($to, $msg);

            } else if ($sms_detail->type == 'custom') {
                 $params = array(
                    'templateid' => $template_id,
                    
                );
                $this->_CI->load->library('customsms',$params);
                $from    = $sms_detail->contact;
                $to      = $send_to;
                $message = $msg;
               
                $this->_CI->customsms->sendSMS($to, $message);
            } else {

            }
        }
        return true;
    }

    public function sentLiveconsultSMS($id, $send_to, $conference_id, $template,$template_id)
    {
        $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();

        if (!empty($sms_detail)) {
        $msg        = $this->getPatientLiveconsultContent($id, $conference_id, $template, $sms_detail->type);
            if ($sms_detail->type == 'clickatell') {
                $params = array(
                    'apiToken' => $sms_detail->api_id,
                );
                $this->_CI->load->library('clickatell', $params);
                try {
                    $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                    foreach ($result['messages'] as $message) {
                    }
                    return true;
                } catch (Exception $e) {
                    return true;
                }
            } else if ($sms_detail->type == 'twilio') {
                $params = array(
                    'mode'        => 'sandbox',
                    'account_sid' => $sms_detail->api_id,
                    'auth_token'  => $sms_detail->password,
                    'api_version' => '2010-04-01',
                    'number'      => $sms_detail->contact,
                );

                $this->_CI->load->library('twilio', $params);
                $from     = $sms_detail->contact;
                $to       = $send_to;
                $message  = $msg;
                $response = $this->_CI->twilio->sms($from, $to, $message);
                if ($response->IsError) {
                    return true;
                } else {
                    return true;
                }

            } else if ($sms_detail->type == 'msg_nineone') {
                $params = array(
                    'authkey'  => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'templateid'=> $template_id ,
                );
                $this->_CI->load->library('msgnineone', $params);
                $this->_CI->msgnineone->sendSMS($send_to, $msg,$template_id);
            } else if ($sms_detail->type == 'smscountry') {
                $params = array(
                    'username' => $sms_detail->username,
                    'senderid' => $sms_detail->senderid,
                    'password' => $sms_detail->password,
                );
                $this->_CI->load->library('smscountry', $params);
                $this->_CI->smscountry->sendSMS($send_to, $msg);
            } else if ($sms_detail->type == 'text_local') {
                $to     = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'hash'     => $sms_detail->password,
                );
                $this->_CI->load->library('textlocalsms', $params);
                $this->_CI->textlocalsms->sendSms(array($to), $msg, $sms_detail->senderid);
            }else if ($sms_detail->type == 'bulk_sms') {
                $to = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'password' => $sms_detail->password,
                );

                $this->_CI->load->library('bulk_sms_lib', $params);
                $this->_CI->bulk_sms_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'mobireach') {
                $to = $send_to;
                $params = array(
                    'authkey' => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'routeid' => $sms_detail->api_id,
                );
                $this->_CI->load->library('mobireach_lib', $params);
                $this->_CI->mobireach_lib->sendSms(array($to), $msg);
 
            } else if ($sms_detail->type == 'nexmo') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_secret' => $sms_detail->authkey,
                );
                $this->_CI->load->library('nexmo_lib', $params);
                $this->_CI->nexmo_lib->sendSms($to, $msg);

            } else if ($sms_detail->type == 'africastalking') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_username' => $sms_detail->username,
                );
                $this->_CI->load->library('africastalking_lib', $params);
                $this->_CI->africastalking_lib->sendSms($to, $msg);

            }  else if ($sms_detail->type == 'custom') {
                $this->_CI->load->library('customsms');
                $from    = $sms_detail->contact;
                $to      = $send_to;
                $message = $msg;
                $this->_CI->customsms->sendSMS($to, $message);
            } else {

            }
        }
        return true;
    }

    public function sentRegisterSMSOPD($id, $send_to, $ptypeno, $template, $subject,$template_id,$appointment_date)
    {
        
        $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();
        if (!empty($sms_detail)) {
        $msg        = $this->getPatientRegistrationContentOPD($id, $ptypeno, $template, $sms_detail->type,$appointment_date);
            if ($sms_detail->type == 'clickatell') {
                $params = array(
                    'apiToken' => $sms_detail->api_id,
                );
                $this->_CI->load->library('clickatell', $params);
                try {
                    $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                    foreach ($result['messages'] as $message) {

                    }
                    return true;
                } catch (Exception $e) {
                    return true;
                }
            } else if ($sms_detail->type == 'twilio') {

                $params = array(
                    'mode'        => 'sandbox',
                    'account_sid' => $sms_detail->api_id,
                    'auth_token'  => $sms_detail->password,
                    'api_version' => '2010-04-01',
                    'number'      => $sms_detail->contact,
                );

                $this->_CI->load->library('twilio', $params);
                $from     = $sms_detail->contact;
                $to       = $send_to;
                $message  = $msg;
                $response = $this->_CI->twilio->sms($from, $to, $message);
                if ($response->IsError) {
                    return true;
                } else {
                    return true;
                }

            } else if ($sms_detail->type == 'msg_nineone') {
                $params = array(
                    'authkey'  => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'templateid'=>$template_id,
                );
                
                $this->_CI->load->library('msgnineone', $params);
                $this->_CI->msgnineone->sendSMS($send_to, $msg,$template_id);
            } else if ($sms_detail->type == 'smscountry') {
                $params = array(
                    'username' => $sms_detail->username,
                    'senderid' => $sms_detail->senderid,
                    'password' => $sms_detail->password,

                );
                $this->_CI->load->library('smscountry', $params);
                $this->_CI->smscountry->sendSMS($send_to, $msg);
            } else if ($sms_detail->type == 'text_local') {
                $to     = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'hash'     => $sms_detail->password,
                );
                $this->_CI->load->library('textlocalsms', $params);
                $this->_CI->textlocalsms->sendSms(array($to), $msg, $sms_detail->senderid);
            }else if ($sms_detail->type == 'bulk_sms') {
                $to = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'password' => $sms_detail->password,
                );

                $this->_CI->load->library('bulk_sms_lib', $params);
                $this->_CI->bulk_sms_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'mobireach') {
                $to = $send_to;
                $params = array(
                    'authkey' => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'routeid' => $sms_detail->api_id,
                );
                $this->_CI->load->library('mobireach_lib', $params);
                $this->_CI->mobireach_lib->sendSms(array($to), $msg);
 
            } else if ($sms_detail->type == 'nexmo') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_secret' => $sms_detail->authkey,
                );
                $this->_CI->load->library('nexmo_lib', $params);
                $this->_CI->nexmo_lib->sendSms($to, $msg);

            } else if ($sms_detail->type == 'africastalking') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_username' => $sms_detail->username,
                );
                $this->_CI->load->library('africastalking_lib', $params);
                $this->_CI->africastalking_lib->sendSms($to, $msg);

            }  else if ($sms_detail->type == 'custom') {
                $this->_CI->load->library('customsms');
                $from    = $sms_detail->contact;
                $to      = $send_to;
                $message = $msg;
                $this->_CI->customsms->sendSMS($to, $message);
            } else {

            }
        }
        return true;
    }

    public function sentRegisterSMSIPD($id, $send_to, $ptypeno, $template,$subject, $template_id,$ipdid)
    {
        $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();       
      
        if (!empty($sms_detail)) {
        $msg        = $this->getPatientRegistrationContentIPD($id, $ipdid, $template, $sms_detail->type);
            if ($sms_detail->type == 'clickatell') {
                $params = array(
                    'apiToken' => $sms_detail->api_id,
                );
                $this->_CI->load->library('clickatell', $params);
                try {
                    $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                    foreach ($result['messages'] as $message) {

                    }
                    return true;
                } catch (Exception $e) {
                    return true;
                }
            } else if ($sms_detail->type == 'twilio') {

                $params = array(
                    'mode'        => 'sandbox',
                    'account_sid' => $sms_detail->api_id,
                    'auth_token'  => $sms_detail->password,
                    'api_version' => '2010-04-01',
                    'number'      => $sms_detail->contact,
                );

                $this->_CI->load->library('twilio', $params);
                $from     = $sms_detail->contact;
                $to       = $send_to;
                $message  = $msg;
                $response = $this->_CI->twilio->sms($from, $to, $message);
                if ($response->IsError) {
                    return true;
                } else {
                    return true;
                }

            } else if ($sms_detail->type == 'msg_nineone') {
                $params = array(
                    'authkey'  => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'templateid'=>$template_id ,
                );
                
                $this->_CI->load->library('msgnineone', $params);
                $this->_CI->msgnineone->sendSMS($send_to, $msg,$template_id);
            } else if ($sms_detail->type == 'smscountry') {
                $params = array(
                    'username' => $sms_detail->username,
                    'senderid' => $sms_detail->senderid,
                    'password' => $sms_detail->password,
                );
                $this->_CI->load->library('smscountry', $params);
                $this->_CI->smscountry->sendSMS($send_to, $msg);
            } else if ($sms_detail->type == 'text_local') {
                $to     = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'hash'     => $sms_detail->password,
                );
               
                $this->_CI->load->library('textlocalsms', $params);
                $this->_CI->textlocalsms->sendSms(array($to), $msg, $sms_detail->senderid);
            }else if ($sms_detail->type == 'bulk_sms') {
                $to = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'password' => $sms_detail->password,
                );

                $this->_CI->load->library('bulk_sms_lib', $params);
                $this->_CI->bulk_sms_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'mobireach') {
                $to = $send_to;
                $params = array(
                    'authkey' => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'routeid' => $sms_detail->api_id,
                );
                $this->_CI->load->library('mobireach_lib', $params);
                $this->_CI->mobireach_lib->sendSms(array($to), $msg);
 
            } else if ($sms_detail->type == 'nexmo') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_secret' => $sms_detail->authkey,
                );
               
                $this->_CI->load->library('nexmo_lib', $params);
                $this->_CI->nexmo_lib->sendSms($to, $msg);

            } else if ($sms_detail->type == 'africastalking') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_username' => $sms_detail->username,
                );
                $this->_CI->load->library('africastalking_lib', $params);
                $this->_CI->africastalking_lib->sendSms($to, $msg);

            }  else if ($sms_detail->type == 'custom') {
                $this->_CI->load->library('customsms');
                $from    = $sms_detail->contact;
                $to      = $send_to;
                $message = $msg;
                $this->_CI->customsms->sendSMS($to, $message);
            } else {

            }
        }
        return true;
    }

    public function sentDischargedSMS($details,$template, $template_id)
    {
        $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();
        $to = $details['mobileno'];
        if (!empty($sms_detail)) {
        $msg        = $this->getPatientDischargedContent($details, $template,$sms_detail->type);
        
            if ($sms_detail->type == 'clickatell') {
                $params = array(
                    'apiToken' => $sms_detail->api_id,
                );
                $this->_CI->load->library('clickatell', $params);
                try {
                    $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                    foreach ($result['messages'] as $message) {

                    }
                    return true;
                } catch (Exception $e) {
                    return true;
                }
            } else if ($sms_detail->type == 'twilio') {
                $params = array(
                    'mode'        => 'sandbox',
                    'account_sid' => $sms_detail->api_id,
                    'auth_token'  => $sms_detail->password,
                    'api_version' => '2010-04-01',
                    'number'      => $sms_detail->contact,
                );

                $this->_CI->load->library('twilio', $params);
                $from     = $sms_detail->contact;
                $to       = $send_to;
                $message  = $msg;
                $response = $this->_CI->twilio->sms($from, $to, $message);
                if ($response->IsError) {
                    return true;
                } else {
                    return true;
                }
            } else if ($sms_detail->type == 'msg_nineone') {
                $send_to = $to ;
                $params = array(
                    'authkey'  => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'templateid'=>$template_id,
                );
                $this->_CI->load->library('msgnineone', $params);
                $this->_CI->msgnineone->sendSMS($send_to, $msg,$template_id);
            } else if ($sms_detail->type == 'smscountry') {
                $params = array(
                    'username' => $sms_detail->username,
                    'senderid' => $sms_detail->senderid,
                    'password' => $sms_detail->password,
                );
                $this->_CI->load->library('smscountry', $params);
                $this->_CI->smscountry->sendSMS($send_to, $msg);
            } else if ($sms_detail->type == 'text_local') {
                $to     = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'hash'     => $sms_detail->password,
                );
                $this->_CI->load->library('textlocalsms', $params);
                $this->_CI->textlocalsms->sendSms(array($to), $msg, $sms_detail->senderid);
            }else if ($sms_detail->type == 'bulk_sms') {
                $to = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'password' => $sms_detail->password,
                );
                $this->_CI->load->library('bulk_sms_lib', $params);
                $this->_CI->bulk_sms_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'mobireach') {
                $to = $send_to;
                $params = array(
                    'authkey' => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'routeid' => $sms_detail->api_id,
                );
                $this->_CI->load->library('mobireach_lib', $params);
                $this->_CI->mobireach_lib->sendSms(array($to), $msg);
 
            } else if ($sms_detail->type == 'nexmo') {
                
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_secret' => $sms_detail->authkey,
                );
                $this->_CI->load->library('nexmo_lib', $params);
                $this->_CI->nexmo_lib->sendSms($to, $msg);

            } else if ($sms_detail->type == 'africastalking') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_username' => $sms_detail->username,
                );
                $this->_CI->load->library('africastalking_lib', $params);
                $this->_CI->africastalking_lib->sendSms($to, $msg);

            }  else if ($sms_detail->type == 'custom') {
                $this->_CI->load->library('customsms');
                $from    = $sms_detail->contact;
                $to      = $send_to;
                $message = $msg;
                $this->_CI->customsms->sendSMS($to, $message);
            } else {

            }
        }
        return true;
    }

    public function sentopdDischargedSMS($details, $template,$template_id)
    {
        $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();
       
        if (!empty($sms_detail)) {
        $msg        = $this->getopdPatientDischargedContent($details, $template,$sms_detail->type);
        $send_to    = $details['mobileno'];
            if ($sms_detail->type == 'clickatell') {
                $params = array(
                    'apiToken' => $sms_detail->api_id,
                );
                $this->_CI->load->library('clickatell', $params);
                try {
                    $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                    foreach ($result['messages'] as $message) {

                    }
                    return true;
                } catch (Exception $e) {
                    return true;
                }
            } else if ($sms_detail->type == 'twilio') {
                $params = array(
                    'mode'        => 'sandbox',
                    'account_sid' => $sms_detail->api_id,
                    'auth_token'  => $sms_detail->password,
                    'api_version' => '2010-04-01',
                    'number'      => $sms_detail->contact,
                );

                $this->_CI->load->library('twilio', $params);
                $from     = $sms_detail->contact;
                $to       = $send_to;
                $message  = $msg;
                $response = $this->_CI->twilio->sms($from, $to, $message);
                if ($response->IsError) {
                    return true;
                } else {
                    return true;
                }

            } else if ($sms_detail->type == 'msg_nineone') {
                $params = array(
                    'authkey'  => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'templateid' => $template_id ,
                );
               
                $this->_CI->load->library('msgnineone', $params);
                $this->_CI->msgnineone->sendSMS($send_to, $msg,$template_id);
            } else if ($sms_detail->type == 'smscountry') {
                $params = array(
                    'username' => $sms_detail->username,
                    'senderid' => $sms_detail->senderid,
                    'password' => $sms_detail->password,
                );
                $this->_CI->load->library('smscountry', $params);
                $this->_CI->smscountry->sendSMS($send_to, $msg);
            } else if ($sms_detail->type == 'text_local') {
                $to     = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'hash'     => $sms_detail->password,
                );
                $this->_CI->load->library('textlocalsms', $params);
                $this->_CI->textlocalsms->sendSms(array($to), $msg, $sms_detail->senderid);
            }else if ($sms_detail->type == 'bulk_sms') {
                $to = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'password' => $sms_detail->password,
                );

                $this->_CI->load->library('bulk_sms_lib', $params);
                $this->_CI->bulk_sms_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'mobireach') {
                $to = $send_to;
                $params = array(
                    'authkey' => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'routeid' => $sms_detail->api_id,
                );
                $this->_CI->load->library('mobireach_lib', $params);
                $this->_CI->mobireach_lib->sendSms(array($to), $msg);
 
            } else if ($sms_detail->type == 'nexmo') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_secret' => $sms_detail->authkey,
                );
                $this->_CI->load->library('nexmo_lib', $params);
                $this->_CI->nexmo_lib->sendSms($to, $msg);

            } else if ($sms_detail->type == 'africastalking') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_username' => $sms_detail->username,
                );
                $this->_CI->load->library('africastalking_lib', $params);
                $this->_CI->africastalking_lib->sendSms($to, $msg);

            }  else if ($sms_detail->type == 'custom') {
                $this->_CI->load->library('customsms');
                $from    = $sms_detail->contact;
                $to      = $send_to;
                $message = $msg;
                $this->_CI->customsms->sendSMS($to, $message);
            } else {

            }
        }
        return true;
    }

    public function sentAppointmentConfirmation($sender_details, $send_to, $appointment_id, $template, $template_id)
    {
        $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();

        if (!empty($sms_detail)) {
        $msg        = $this->getAppointmentConfirmationContent($sender_details, $appointment_id, $template,$sms_detail->type);
        
            if ($sms_detail->type == 'clickatell') {
                $params = array(
                    'apiToken' => $sms_detail->api_id,
                );
                $this->_CI->load->library('clickatell', $params);
                try {
                    $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                    foreach ($result['messages'] as $message) {

                    }
                    return true;
                } catch (Exception $e) {
                    return true;
                }
            } else if ($sms_detail->type == 'twilio') {

                $params = array(
                    'mode'        => 'sandbox',
                    'account_sid' => $sms_detail->api_id,
                    'auth_token'  => $sms_detail->password,
                    'api_version' => '2010-04-01',
                    'number'      => $sms_detail->contact,
                );
                $this->_CI->load->library('twilio', $params);

                $from     = $sms_detail->contact;
                $to       = $send_to;
                $message  = $msg;
                $response = $this->_CI->twilio->sms($from, $to, $message);

                if ($response->IsError) {
                    return true;
                } else {
                    return true;
                }

            } else if ($sms_detail->type == 'msg_nineone') {
                $params = array(
                    'authkey'  => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'templateid'=> $template_id
                );
                $this->_CI->load->library('msgnineone', $params);
                $this->_CI->msgnineone->sendSMS($send_to, $msg,$template_id);
            } else if ($sms_detail->type == 'smscountry') {
                $params = array(
                    'username' => $sms_detail->username,
                    'senderid' => $sms_detail->senderid,
                    'password' => $sms_detail->password,
                );
                $this->_CI->load->library('smscountry', $params);
                $this->_CI->smscountry->sendSMS($send_to, $msg);
            } else if ($sms_detail->type == 'text_local') {
                $to     = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'hash'     => $sms_detail->password,
                );
                $this->_CI->load->library('textlocalsms', $params);
                $this->_CI->textlocalsms->sendSms(array($to), $msg, $sms_detail->senderid);
            }else if ($sms_detail->type == 'bulk_sms') {
                $to = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'password' => $sms_detail->password,
                );
                $this->_CI->load->library('bulk_sms_lib', $params);
                $this->_CI->bulk_sms_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'mobireach') {
                $to = $send_to;
                $params = array(
                    'authkey' => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'routeid' => $sms_detail->api_id,
                );
                $this->_CI->load->library('mobireach_lib', $params);
                $this->_CI->mobireach_lib->sendSms(array($to), $msg);
 
            } else if ($sms_detail->type == 'nexmo') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_secret' => $sms_detail->authkey,
                );
                $this->_CI->load->library('nexmo_lib', $params);
                $this->_CI->nexmo_lib->sendSms($to, $msg);

            } else if ($sms_detail->type == 'africastalking') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_username' => $sms_detail->username,
                );
                $this->_CI->load->library('africastalking_lib', $params);
                $this->_CI->africastalking_lib->sendSms($to, $msg);

            }  else if ($sms_detail->type == 'custom') {
                $this->_CI->load->library('customsms');
                $from    = $sms_detail->contact;
                $to      = $send_to;
                $message = $msg;
                $this->_CI->customsms->sendSMS($to, $message);
            } else {

            }
        }
        return true;
    }

    public function sentOnlineMeetingStaffSMS($detail, $template,$template_id)
    {
        $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();
        if (!empty($sms_detail)) {

            foreach ($detail as $staff_key => $staff_value) {
                $send_to = $staff_key;
                if ($send_to != "") {
                    $msg = $this->getOnlineMeetingStaffContent($detail[$staff_key], $template,$sms_detail->type );
                   

                    $subject = "Live Meeting";
                    if ($sms_detail->type == 'clickatell') {
                        $params = array(
                            'apiToken' => $sms_detail->api_id,
                        );
                        $this->_CI->load->library('clickatell', $params);

                        try {
                            $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                            foreach ($result['messages'] as $message) {

                            }
                            return true;
                        } catch (Exception $e) {
                            return false;
                        }
                    } else if ($sms_detail->type == 'twilio') {

                        $params = array(
                            'mode'        => 'sandbox',
                            'account_sid' => $sms_detail->api_id,
                            'auth_token'  => $sms_detail->password,
                            'api_version' => '2010-04-01',
                            'number'      => $sms_detail->contact,
                        );

                        $this->_CI->load->library('twilio', $params);

                        $from     = $sms_detail->contact;
                        $to       = $send_to;
                        $message  = $msg;
                        $response = $this->_CI->twilio->sms($from, $to, $message);

                        if ($response->IsError) {
                            return false;
                        } else {
                            return true;
                        }
                    } else if ($sms_detail->type == 'msg_nineone') {

                        $params = array(
                            'authkey'  => $sms_detail->authkey,
                            'senderid' => $sms_detail->senderid,
                            'templateid'=>$template_id,
                        );
                        $this->_CI->load->library('msgnineone', $params);
                        $this->_CI->msgnineone->sendSMS($send_to, $msg,$template_id);
                    } else if ($sms_detail->type == 'smscountry') {
                        $params = array(
                            'username' => $sms_detail->username,
                            'senderid' => $sms_detail->senderid,
                            'password' => $sms_detail->password,
                        );
                        $this->_CI->load->library('smscountry', $params);
                        $this->_CI->smscountry->sendSMS($send_to, $msg);
                    } else if ($sms_detail->type == 'text_local') {
                        $params = array(
                            'username' => $sms_detail->username,
                            'hash'     => $sms_detail->password,
                        );
                        $this->_CI->load->library('textlocalsms', $params);
                        $this->_CI->textlocalsms->sendSms(array($send_to), $msg, $sms_detail->senderid);
                    }else if ($sms_detail->type == 'bulk_sms') {
                $to = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'password' => $sms_detail->password,
                );

                $this->_CI->load->library('bulk_sms_lib', $params);
                $this->_CI->bulk_sms_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'mobireach') {
                $to = $send_to;
                $params = array(
                    'authkey' => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'routeid' => $sms_detail->api_id,
                );
                $this->_CI->load->library('mobireach_lib', $params);
                $this->_CI->mobireach_lib->sendSms(array($to), $msg);
 
            } else if ($sms_detail->type == 'nexmo') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_secret' => $sms_detail->authkey,
                );
                $this->_CI->load->library('nexmo_lib', $params);
                $this->_CI->nexmo_lib->sendSms($to, $msg);

            } else if ($sms_detail->type == 'africastalking') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_username' => $sms_detail->username,
                );
                $this->_CI->load->library('africastalking_lib', $params);
                $this->_CI->africastalking_lib->sendSms($to, $msg);

            }  else if ($sms_detail->type == 'custom') {
                        $this->_CI->load->library('customsms');
                        $from    = $sms_detail->contact;
                        $to      = $send_to;
                        $message = $msg;
                        $this->_CI->customsms->sendSMS($to, $message);
                    } else {

                    }
                }
            }
        }
    }
    public function sendLoginCredential($chk_mail_sms, $sender_details, $template, $template_id,$send_for)
    {
        $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();
        if(!empty($sms_detail)){

        $msg        = $this->getLoginCredentialContent($sender_details['credential_for'], $sender_details, $template,$sms_detail->type);

        $send_to = $sender_details['contact_no'];
        if (!empty($sms_detail)) {
            if ($sms_detail->type == 'clickatell') {

                $params = array(
                    'apiToken' => $sms_detail->api_id,
                );
                $this->_CI->load->library('clickatell', $params);
                try {
                    $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                    foreach ($result['messages'] as $message) {

                    }
                    return true;
                } catch (Exception $e) {
                    return true;
                }
            } else if ($sms_detail->type == 'twilio') {

                $params = array(
                    'mode'        => 'sandbox',
                    'account_sid' => $sms_detail->api_id,
                    'auth_token'  => $sms_detail->password,
                    'api_version' => '2010-04-01',
                    'number'      => $sms_detail->contact,
                );

                $this->_CI->load->library('twilio', $params);

                $from     = $sms_detail->contact;
                $to       = $send_to;
                $message  = $msg;
                $response = $this->_CI->twilio->sms($from, $to, $message);

                if ($response->IsError) {
                    return true;
                } else {
                    return true;
                }
            } else if ($sms_detail->type == 'msg_nineone') {
                $params = array(
                    'authkey'  => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,                    
                    'send_for' => $send_for
                );
                $this->_CI->load->library('msgnineone', $params);
               $this->_CI->msgnineone->sendSMS($send_to, $msg,$template_id);
            } else if ($sms_detail->type == 'smscountry') {
                $params = array(
                    'username' => $sms_detail->username,
                    'senderid' => $sms_detail->senderid,
                    'password' => $sms_detail->password,
                );
                $this->_CI->load->library('smscountry', $params);
                $this->_CI->smscountry->sendSMS($send_to, $msg);
            } else if ($sms_detail->type == 'text_local') {
                $params = array(
                    'username' => $sms_detail->username,
                    'hash'     => $sms_detail->password,
                );
                $to = $send_to ;
                $this->_CI->load->library('textlocalsms', $params);
                $this->_CI->textlocalsms->sendSms(array($to), $msg, $sms_detail->senderid);
            }else if ($sms_detail->type == 'bulk_sms') {
                $to = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'password' => $sms_detail->password,
                );
                $this->_CI->load->library('bulk_sms_lib', $params);
                $this->_CI->bulk_sms_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'mobireach') {
                $to = $send_to;
                $params = array(
                    'authkey' => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'routeid' => $sms_detail->api_id,
                );
                $this->_CI->load->library('mobireach_lib', $params);
                $this->_CI->mobireach_lib->sendSms(array($to), $msg);
 
            } else if ($sms_detail->type == 'nexmo') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_secret' => $sms_detail->authkey,
                );
                $this->_CI->load->library('nexmo_lib', $params);
                $this->_CI->nexmo_lib->sendSms($to, $msg);
            } else if ($sms_detail->type == 'africastalking') {
                $to = $send_to;
                $params = array(
                    'from' => $sms_detail->senderid,
                    'api_key' => $sms_detail->api_id,
                    'api_username' => $sms_detail->username,
                );
                $this->_CI->load->library('africastalking_lib', $params);
                $this->_CI->africastalking_lib->sendSms($to, $msg);

            }  else if ($sms_detail->type == 'custom') {
                $this->_CI->load->library('customsms');
                $from    = $sms_detail->contact;
                $to      = $send_to;
                $message = $msg;
                $this->_CI->customsms->sendSMS($to, $message);
            } else {

            }
        }
        
        }
        return true;
    }

    public function getOnlineMeetingStaffContent($staff_detail, $template, $sms_detail_type)
    {
        foreach ($staff_detail as $key => $value) {

            if($sms_detail_type=='msg_nineone' || $sms_detail_type=='textlocalsms'){
              
                if(strlen($value)>30){
                    $value = substr($value,0,29);
                }
            }
            
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }
        return $template;
    }

    public function getPatientRegistrationContentOPD($id, $ptypeno, $template, $sms_detail_type, $appointment_date)
    {
        $opdid   = $this->_CI->patient_model->getopdidbyopdno($ptypeno);
        $patient = $this->_CI->patient_model->getDetails($opdid['opdid']);

        $patient['appointment_date'] = $appointment_date ;
        $patient['opdno']            = $ptypeno ;
        foreach ($patient as $key => $value) {

            if($sms_detail_type=='msg_nineone' || $sms_detail_type=='textlocalsms')
            {

                if(strlen($value)>30){
                      $value = substr($value,0,29);
                }
            }
            
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;

        }
        return $template;
    }

    public function getPatientRegistrationContentIPD($id, $ipdid, $template,$sms_detail_type)
    {

        $patient = $this->_CI->patient_model->getIpdDetails($ipdid);
        $patient['ipd_no'] = $ipdid ;

        foreach ($patient as $key => $value) {

            if($sms_detail_type=='msg_nineone' || $sms_detail_type=='textlocalsms'){
                if(strlen($value)>30){
                    $value = substr($value,0,29);
                }
            }
          
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }
        return $template;
    }

    public function getPatientLiveconsultContent($id, $conference_id,$template,$sms_detail_type)
    {
        $conference = $this->_CI->conference_model->getconference($conference_id);
        foreach ($conference as $key => $value) {

            if($sms_detail_type=='msg_nineone' || $sms_detail_type=='textlocalsms'){
              
                if(strlen($value)>30){
                    $value = substr($value,0,29);
                }
            }
			
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;

        }
        return $template;
    }

    public function getPatientDischargedContent($details, $template, $sms_detail_type)
    {

        foreach ($details as $key => $value) {

            if($sms_detail_type=='msg_nineone' || $sms_detail_type=='textlocalsms'){
              
                if(strlen($value)>30){
                    $value = substr($value,0,29);
                }                 
                 
            }
          
           $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }
        return $template;
    }

    public function getopdPatientDischargedContent($details, $template,$sms_detail_type)
    { 
        foreach ($details as $key => $value) {

             if($sms_detail_type=='msg_nineone' || $sms_detail_type=='textlocalsms'){
              
                if(strlen($value)>30){
                    $value = substr($value,0,29);
                }
            }
            
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }
        return $template;
    }

    public function getAppointmentConfirmationContent($sender_details, $appointment_id, $template,$sms_detail_type)
    {
        $result = $this->_CI->appointment_model->getDetailsFornotification($appointment_id);
        $result['patient_name'] = $sender_details['patient_name'];

        foreach ($result as $key => $value) {
             if($sms_detail_type=='msg_nineone' || $sms_detail_type=='textlocalsms'){
              
                if(strlen($value)>30){
                    $value = substr($value,0,29);
                }
            }
           
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }
        return $template;
    }

    public function getLoginCredentialContent($credential_for, $sender_details, $template,$sms_detail_type)
    {
        if ($credential_for == "patient") {
            $patient                        = $this->_CI->patient_model->patientProfileDetails($sender_details['id']);          
            $sender_details['url']          = site_url();
            $sender_details['display_name'] = $patient['patient_name'];
        } elseif ($credential_for == "staff") {
            $staff                          = $this->_CI->staff_model->get($sender_details['id']);
            $sender_details['url']          = site_url('site/login');
            $sender_details['display_name'] = $staff['name'] . " " . $staff['surname'];
        }

        foreach ($sender_details as $key => $value) {

            if($sms_detail_type=='msg_nineone' || $sms_detail_type=='textlocalsms'){
              
                if(strlen($value)>30){
                    $value = substr($value,0,29);
                }
            }
           
            $template = $value ? str_replace('{{' . $key . '}}', $value, $template) : $template;
        }

        return $template;
    }
 
}
