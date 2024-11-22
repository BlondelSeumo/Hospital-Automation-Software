<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bulkmessage extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("bulkmessage_model");
        $this->load->library('mailsmsconf');
        $this->load->library('Customlib');
        $this->load->library('datatables');
        $this->load->model("patient_model");       
    }

    public function index()
    {
		$this->session->set_userdata('top_menu', 'Messaging');
        $this->session->set_userdata('sub_menu', 'bulkmessage/index');
        $this->load->view("layout/header");			
		$data['notificationtype'] = $this->customlib->bulkmailnotificationtype();
        $this->load->view('admin/bulkmessage/index',$data);
        $this->load->view("layout/footer");
    }

    public function getcredentialdatatable()
    {
        $dt_response = $this->patient_model->getAllcredentialRecord();
 
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                
                //====================
                $first_action = '<input type="checkbox" name="patient[]" value="'.$value->uid.'">';
                $row[] = $first_action;       
                $row[] = $value->id;
                $row[] = $value->patient_name;
                $row[] = $value->email;
                $row[] = $value->mobileno;
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


    /*  This function is used to send bulk mail to patient  */
    public function sendbulkmail()
    {
        $this->form_validation->set_rules('patient[]', $this->lang->line('patient'), 'trim|required|xss_clean');
		$this->form_validation->set_rules('notification_type',$this->lang->line('notification_type'),'trim|required|xss_clean');
       
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient[]'         => form_error('patient[]'),              
                'notification_type'         => form_error('notification_type'),              
            );
            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        } else {

            $patient          	= $this->input->post('patient');
            $notification_type  = $this->input->post('notification_type');
			 
            foreach ($patient as $patient_id) {
				 
                $patient_detail_byid = $this->user_model->read_user_information($patient_id);			 
					
					$patient_detail = array(
						'patient_id'          => $patient_detail_byid[0]->user_id,
						'username'            => $patient_detail_byid[0]->username,
						'password'            => $patient_detail_byid[0]->password,
						'email'               => $patient_detail_byid[0]->email,
						'display_name'        => composePatientName($patient_detail_byid[0]->patient_name, $patient_detail_byid[0]->user_id),
						'mobileno'            => $patient_detail_byid[0]->mobileno, 
						'gender'              => $patient_detail_byid[0]->gender, 
						'dob'                 => $patient_detail_byid[0]->dob, 
						'id'                  => $patient_detail_byid[0]->user_id,
						'credential_for'      => 'patient',                  
					);			 

					$usertype = "patient";
					$verification_code = $this->enc_lib->encrypt(uniqid(mt_rand()));                
					$resetPassLink  = site_url('user/resetpassword') . '/' . $usertype . "/" . $verification_code;               
					$sender_details = array('id' => $patient_detail_byid[0]->user_id, 'email' => $patient_detail_byid[0]->email); 				
					$send_for       = 'forgot_password';
					$chk_mail_sms   = $this->customlib->sendMailSMS($send_for);
					$body           = $this->forgotPasswordBody($sender_details, $resetPassLink, $chk_mail_sms['template']);
				
				if($notification_type == 1){
					
					echo  $this->mailsmsconf->mailsms('login_credential', $patient_detail);
					
				}elseif($notification_type == 2){
					
					if ($chk_mail_sms['mail']) {
						$this->mailer->send_mail($patient_detail_byid[0]->email, $chk_mail_sms['subject'], $body);
					}
				
				}elseif($notification_type == 3){
					
					if ($chk_mail_sms['mail']) {
						$this->mailer->send_mail($patient_detail_byid[0]->email, $chk_mail_sms['subject'], $body);
					}
				
					echo  $this->mailsmsconf->mailsms('login_credential', $patient_detail);
					
				}
                   
            }

            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('message_sent_successfully'));
        }
        echo json_encode($array);
    }
	
	
	public function forgotPasswordBody($sender_details, $resetPassLink, $template)
    {
		
            $patient = $this->patient_model->patientProfileDetails($sender_details['id']);             
            $sender_details['resetpasslink'] = $resetPassLink;
            $sender_details['display_name']  = $patient['patient_name'];
        

        foreach ($sender_details as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }

}