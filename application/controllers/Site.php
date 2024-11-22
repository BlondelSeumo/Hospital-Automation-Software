<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Site extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->check_installation();
        if ($this->config->item('installed') == true) {
            $this->db->reconnect();
        }
        $this->load->model(array('onlineappointment_model', 'prefix_model'));
        $this->load->library('Auth');
        $this->load->library('Enc_lib');
        $this->load->library('mailer');
        $this->load->config('ci-blog');
        $this->load->library('captchalib');
        $this->mailer;
    }

    private function check_installation()
    {
        if ($this->uri->segment(1) !== 'install') {
            $this->load->config('migration');
            if ($this->config->item('installed') == false && $this->config->item('migration_enabled') == false) {
                redirect(base_url() . 'install/start');
            } else {
                if (is_dir(APPPATH . 'controllers/install')) {
                    echo '<h3>Delete the install folder from application/controllers/install</h3>';
                    die;
                }
            }
        }
    }

    public function login()
    {
        if ($this->auth->logged_in()) {
            $this->auth->is_logged_in(true);
        }

        $data               = array();
        $data['title']      = 'Login';
        $notice_content     = $this->config->item('ci_front_notice_content');
        $notices            = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
        $data['notice']     = $notices;
        $is_captcha         = $this->captchalib->is_captcha('login');
        $data["is_captcha"] = $is_captcha;
        $setting_result        = $this->setting_model->get();
        $data['sch_name']=$setting_result[0]['name'];
        if ($is_captcha) {
            $this->form_validation->set_rules('captcha', 'Captcha', 'trim|required|callback_check_captcha');
        }
        $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            if($is_captcha){
                $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
            }
            $this->load->view('admin/login', $data);
        } else {

            $login_post = array(
                'email'    => $this->input->post('username'),
                'password' => $this->input->post('password'),
            );

            
            $result                = $this->staff_model->checkLogin($login_post);
            $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];

            if (!empty($result->language_id)) {
                $lang_array = array('lang_id' => $result->language_id, 'language' => $result->language);               
            } else {
                $lang_array = array('lang_id' => $setting_result[0]['lang_id'], 'language' => $setting_result[0]['language']);
            }
            if (!empty($result->language_id)) {
                $lang_data   = $this->language_model->get($result->lang_id);
            }else{
                $lang_data   = $this->language_model->get(4);
            }
            if ($result) {

                $prefix_array = $this->prefix_model->getPrefixArray();

                if ($result->is_active) { 
                    $time_format = $setting_result[0]['time_format'];
                    if ($time_format == '12-hour') {
                        $check_time_format = false;
                    } else {
                        $check_time_format = true;
                    }
                    
                    $session_data = array(
                        'id'                     => $result->id,
                        'username'               => $result->name . ' ' . $result->surname,
                        'email'                  => $result->email,
                        'roles'                  => $result->roles,
                        'date_format'            => $setting_result[0]['date_format'],
                        'currency_symbol'        => $setting_result[0]['currency_symbol'],
                        'start_month'            => $setting_result[0]['start_month'],
                        'timezone'               => $setting_result[0]['timezone'],
                        'sch_name'               => $setting_result[0]['name'],
                        'language'               => $lang_array,
                        'is_rtl'                 => $lang_data['is_rtl'],
                        'doctor_restriction'     => $setting_result[0]['doctor_restriction'],
                        'superadmin_restriction' => $setting_result[0]['superadmin_restriction'],
                        'theme'                  => $setting_result[0]['theme'],
                        'base_url'              => 	$setting_result[0]['base_url'],
                        'folder_path'            => $setting_result[0]['folder_path'],
                        'time_format'            => $check_time_format,
                        'prefix'                 => $prefix_array,
                        'db_array'               => ['base_url'               => $setting_result[0]['base_url'],
                             'folder_path'            => $setting_result[0]['folder_path'],
                             'db_group'=>'default'
                            ],
                    );

                    $this->session->set_userdata('hospitaladmin', $session_data);
                    $role      = $this->customlib->getStaffRole();
                    $role_name = json_decode($role)->name;
                    $this->customlib->setUserLog($this->input->post('username'), $role_name);

                    if (isset($_SESSION['redirect_to'])) {
                        redirect($_SESSION['redirect_to']);
                    } else {
                        redirect('admin/admin/dashboard');
                    }
                } else {
                    $data['error_message'] = $this->lang->line('your_account_is_disabled_please_contact_to_administrator');
                    $this->load->view('admin/login', $data);
                }
            } else {
                $data['error_message'] = $this->lang->line('invalid_username_or_password');
                $this->load->view('admin/login', $data);
            }
        }
    }

    public function logout()
    {
        $admin_session   = $this->session->userdata('hospitaladmin');
        $patient_session = $this->session->userdata('patient');
        $this->auth->logout();
        if ($admin_session) {
            redirect('site/login');
        } else if ($patient_session) {
            redirect('site/userlogin');
        } else {
            redirect('site/userlogin');
        }
    }

    public function forgotpassword()
    {
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->load->view('admin/forgotpassword');
        } else {
            $email  = $this->input->post('email');
            $result = $this->staff_model->getByEmail($email);
            if ($result && $result->email != "") {
                $verification_code = $this->enc_lib->encrypt(uniqid(mt_rand()));
                $update_record     = array('id' => $result->id, 'verification_code' => $verification_code);
                $this->staff_model->add($update_record);
                $name           = $result->name;
                $resetPassLink  = base_url('admin/resetpassword') . "/" . $verification_code;
                $send_for       = 'forgot_password';
                $usertype       = 'staff';
                $chk_mail_sms   = $this->customlib->sendMailSMS($send_for);
                $sender_details = array('id' => $result->id, 'email' => $email);
                $body           = $this->forgotPasswordBody($usertype, $sender_details, $resetPassLink, $chk_mail_sms['template']);

                if ($chk_mail_sms['mail']) {
                    $result_new = $this->mailer->send_mail($result->email, $chk_mail_sms['subject'], $body);
                }
                $this->session->set_flashdata('message', $this->lang->line('recover_message'));
                redirect('site/login', 'refresh');
            } else {
                $data = array(
                    'error_message' => $this->lang->line('invalid_email'),
                );
            }
            $this->load->view('admin/forgotpassword', $data);
        }
    }

    //reset password - final step for forgotten password
    public function admin_resetpassword($verification_code = null)
    {
        if (!$verification_code) {
            show_404();
        }
        $user = $this->staff_model->getByVerificationCode($verification_code);
        if ($user) {
            //if the code is valid then display the password reset form
            $this->form_validation->set_rules('password', $this->lang->line('password'), 'required');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required|matches[password]');
            if ($this->form_validation->run() == false) {
                $data['verification_code'] = $verification_code;
                //render
                $this->load->view('admin/admin_resetpassword', $data);
            } else {
                // finally change the password
                $password      = $this->input->post('password');
                $update_record = array(
                    'id'                => $user->id,
                    'password'          => $this->enc_lib->passHashEnc($password),
                    'verification_code' => "",
                );

                $change = $this->staff_model->update($update_record);
                if ($change) {
                    //if the password was successfully changed
                    $this->session->set_flashdata('message', $this->lang->line('reset_message'));
                    redirect('site/login', 'refresh');
                } else {
                    $this->session->set_flashdata('message', $this->lang->line('worning_message'));
                    redirect('admin_resetpassword/' . $verification_code, 'refresh');
                }
            }
        } else {
            //if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->lang->line('invalid_link'));
            redirect("site/forgotpassword", 'refresh');
        }
    }

    //reset password - final step for forgotten password
    public function resetpassword($role = null, $verification_code = null)
    {
        if (!$role || !$verification_code) {
            show_404();
        }

        $user = $this->user_model->getUserByCodeUsertype($role, $verification_code);

        if ($user) {
            //if the code is valid then display the password reset form
            $this->form_validation->set_rules('password', $this->lang->line('password'), 'required');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required|matches[password]');
            if ($this->form_validation->run() == false) {
                $data['role']              = $role;
                $data['verification_code'] = $verification_code;
                //render
                $this->load->view('resetpassword', $data);
            } else {

                // finally change the password

                $update_record = array(
                    'id'                => $user->user_tbl_id,
                    'password'          => $this->input->post('password'),
                    'verification_code' => "",
                );

                $change = $this->user_model->changeStatus($update_record);
                if ($change) {
                    //if the password was successfully changed
                    $this->session->set_flashdata('message', $this->lang->line('reset_message'));
                    redirect('site/userlogin', 'refresh');
                } else {
                    $this->session->set_flashdata('message', $this->lang->line('worning_message'));
                    redirect('user/resetpassword/' . $role . '/' . $verification_code, 'refresh');
                }
            }
        } else {
            //if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->lang->line('invalid_link'));
            redirect("site/ufpassword", 'refresh');
        }
    }

    public function ufpassword()
    {
        $this->form_validation->set_rules('username', $this->lang->line('email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('user', $this->lang->line('user_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('ufpassword');
        } else {
            $email    = $this->input->post('username');
            $usertype = $this->input->post('user');
            $result   = $this->user_model->forgotPassword($usertype, $email);

            if ($result && $result->email != "") {
                $verification_code = $this->enc_lib->encrypt(uniqid(mt_rand()));
                $update_record     = array('id' => $result->user_tbl_id, 'verification_code' => $verification_code);
                $this->user_model->changeStatus($update_record);
                if ($usertype == "patient") {
                    $name = $result->patient_name;
                } else {
                    $name = $result->patient_name;
                }
                $resetPassLink  = site_url('user/resetpassword') . '/' . $usertype . "/" . $verification_code;
                $send_for       = 'forgot_password';
                $chk_mail_sms   = $this->customlib->sendMailSMS($send_for);
                $sender_details = array('id' => $result->id, 'email' => $email);
                $body           = $this->forgotPasswordBody($usertype, $sender_details, $resetPassLink, $chk_mail_sms['template']);

                if ($chk_mail_sms['mail']) {
                    $result = $this->mailer->send_mail($result->email, $chk_mail_sms['subject'], $body);
                }
                $this->session->set_flashdata('message', $this->lang->line('recover_message'));
                redirect('site/userlogin', 'refresh');
            } else {
                $data = array(
                    'error_message' => $this->lang->line('invalid_user_email'),
                );
            }
            $this->load->view('ufpassword', $data);
        }
    }

    public function forgotPasswordBody($usertype, $sender_details, $resetPassLink, $template)
    {
        if ($usertype == "patient") {
            $patient = $this->patient_model->patientProfileDetails($sender_details['id']);             
            $sender_details['resetpasslink'] = $resetPassLink;
            $sender_details['display_name']  = $patient['patient_name'];
        }
        if ($usertype == "staff") {
            $staff = $this->staff_model->get($sender_details['id']);           
            $sender_details['resetpasslink'] = $resetPassLink;
            $sender_details['display_name']  = $staff['name'] . " " . $staff['surname'];
        }

        foreach ($sender_details as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }

    public function getpatientDetails()
    {
        $id     = $this->input->post("patient_id");
        $result = $this->appointment_model->getpatientDetails($id);
        $array  = array('status' => 0, 'result' => array());

        if ($result) {
            $array = array('status' => 1, 'result' => $result);
        }
        echo json_encode($array);
    }

    public function getdoctor()
    {
        $spec_id       = $this->input->post('id');
        $active        = $this->input->post('active');
        $result        = $this->staff_model->getdoctorbyspecilist($spec_id);
        $doctors_array = array();
        foreach ($result as $doctor) {
            $doctor_array = array(
                "id"   => $doctor['id'],
                "name" => composeStaffNameByString($doctor['name'], $doctor['surname'], $doctor['employee_id']),
            );
            array_push($doctors_array, $doctor_array);
        }
        echo json_encode($doctors_array);
    }

    public function userlogin()
    {
        $patientpanel = $this->customlib->patientpanel();
        $setting_result        = $this->setting_model->get();
        if ($patientpanel == 'disabled') {
            redirect('site/login');
        }

        if ($this->auth->user_logged_in()) {
            $this->auth->user_redirect();
        }
        $data           = array();
        $data['title']  = 'Login';
        $notice_content = $this->config->item('ci_front_notice_content');
        $notices        = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
        $data['name'] = $setting_result[0]['name'];

        $data['notice'] = $notices;
        $is_captcha         = $this->captchalib->is_captcha('userlogin');
        $data["is_captcha"] = $is_captcha;
        if ($is_captcha) {
            $this->form_validation->set_rules('captcha', 'Captcha', 'trim|required|callback_check_captcha');
        }

        $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            if($is_captcha){
                $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
            }
            $this->load->view('userlogin', $data);
        } else {
            $login_post = array(
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
            );
            $login_details         = $this->user_model->checkLogin($login_post);
            

            $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
            if (isset($login_details) && !empty($login_details)) {
                $user = $login_details[0];
                if ($user->is_active == "yes") {
                    if ($user->role == "patient") {
                        $result = $this->user_model->read_user_information($user->id);

                    }

                    // if ($result[0]->lang_id != 0) {
					if (!empty($result[0]->lang_id)) {
                        $lang_array = array('lang_id' => $result['0']->lang_id, 'language' => $result['0']->language);                        
                    } else {
                        $lang_array = array('lang_id' => $setting_result[0]['lang_id'], 'language' => $setting_result[0]['language']);                        
                    }
                    
                    if (!empty($result[0]->lang_id)) {
                        $lang_data   = $this->language_model->get($result[0]->lang_id);
                    }else{
                        $lang_data   = $this->language_model->get(4);
                    }
           
                    $prefix_array = $this->prefix_model->getPrefixArray();
                    if ($result != false) {

                        if ($result[0]->role == "patient") {

                            $time_format = $setting_result[0]['time_format'];
                            if ($time_format == '12-hour') {
                                $check_time_format = false;
                            } else {
                                $check_time_format = true;
                            }

                            $session_data = array(
                                'id'              => $result[0]->id,
                                'patient_id'      => $result[0]->user_id,
                                'patient_type'    => $result[0]->patient_type,
                                'role'            => $result[0]->role,
                                'username'        => $result[0]->username,
                                'name'            => $result[0]->patient_name,
                                'gender'          => $result[0]->gender,
                                'email'           => $result[0]->email,
                                'mobileno'        => $result[0]->mobileno,
                                'date_format'     => $setting_result[0]['date_format'],
                                'currency_symbol' => $setting_result[0]['currency_symbol'],
                                'timezone'        => $setting_result[0]['timezone'],
                                'sch_name'        => $setting_result[0]['name'],
                                'language'        => array('lang_id' => $setting_result[0]['lang_id'], 'language' => $setting_result[0]['language']),
                                'is_rtl'          => $lang_data['is_rtl'],
                                'theme'           => $setting_result[0]['theme'],
                                'superadmin_restriction'           => $setting_result[0]['superadmin_restriction'],
                                'time_format'     => $check_time_format,
                                'image'           => $result[0]->image,
                                'prefix'          => $prefix_array,
                            );

                            $this->session->set_userdata('patient', $session_data);
                            $this->customlib->setUserLog($result[0]->username, $result[0]->role);
                            redirect('patient/dashboard');
                        }
                    } else {
                        $data['error_message'] = $this->lang->line('account_suspended');
                        $this->load->view('userlogin', $data);
                    }
                } else {
                    $data['error_message'] = $this->lang->line('your_account_is_disabled_please_contact_to_administrator');
                    $this->load->view('userlogin', $data);
                }
            } else {
                $data['error_message'] = $this->lang->line('invalid_username_or_password');
                $this->load->view('userlogin', $data);
            }
        }
    }

    public function check_captcha($captcha)
    {
        if ($captcha != $this->session->userdata('captchaCode')):
            $this->form_validation->set_message('check_captcha', $this->lang->line("incorrect_captcha"));
            return false;
        else:
            return true;
        endif;
    }

    public function refreshCaptcha()
    {
        $captcha = $this->captchalib->generate_captcha();
        echo $captcha['image'];
    }

    public function getDoctorShift()
    {
        $shift_data = array();
        $doctor     = $this->input->post("doctor");
        $shift      = $this->onlineappointment_model->getShiftByDoctor($doctor);
        $days       = $this->customlib->getDaysname();

        foreach ($days as $day) {
            $i = 0;
            foreach ($shift as $shift_key => $shift_value) {
                if ($day == $shift_value->day) {
                    $shift_data[$day][$i]["start_time"] = $shift_value->start_time;
                    $shift_data[$day][$i]["end_time"]   = $shift_value->end_time;
                    $i++;
                }
            }
        }
        echo json_encode($shift_data);
    }

    public function getShift()
    {
        $dates        = $this->input->post("date");
        $date         = $this->customlib->dateFormatToYYYYMMDD($dates);
        $doctor       = $this->input->post("doctor");
        $global_shift = $this->input->post("global_shift");
        $day          = date("l", strtotime($date));        
        $getDoctorGlobalShiftId = $this->onlineappointment_model->getDoctorGlobalShiftId($doctor, $global_shift); 
        $shift        = $this->onlineappointment_model->getShiftdata($doctor, $day, $getDoctorGlobalShiftId['id']);        
        echo json_encode($shift);
    }

    public function getSlotByShift()
    {
        $data           = array();
        $data["result"] = array();
        $shift          = $this->input->post("shift");
        $doctor_id      = $this->input->post("doctor");
        $global_shift   = $this->input->post("global_shift");
        $date           = $this->customlib->dateFormatToYYYYMMDD($this->input->post("date"));
        $day            = date("l", strtotime($date));
        
        $getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($doctor_id, $global_shift, $day);      
        
        $appointments   = $this->onlineappointment_model->getAppointments($doctor_id, $getDoctorShiftTimeId->id, $date);         
        
        $array_of_time  = $this->customlib->getSlotByDoctorShift($doctor_id, $shift);
 
        $this->load->model("charge_model");
        $class = "";
        foreach ($array_of_time as $time) {
            if (!empty($appointments)) {
                foreach ($appointments as $appointment) {                
                   
                    if (date("H:i:s", strtotime($appointment->date)) == date("H:i:s", strtotime($time))) {                       
                        $class  = "row badge badge-pill badge-danger-soft";
                        $filled = "filled";
                        break;
                    } else {
                        $class  = "row badge badge-pill badge-success-soft";
                        $filled = "";
                    }
                }

                array_push($data["result"], array("time" => $this->customlib->getHospitalTime_FormatFrontCMS($time), "class" => $class, "filled" => $filled));
            } else {
                array_push($data["result"], array("time" => $this->customlib->getHospitalTime_FormatFrontCMS($time), "class" => "row badge badge-pill badge-success-soft"));
            }
        }
        $doctor_data               = $this->staff_model->getProfile($doctor_id);
        $data["doctor_name"]       = $doctor_data["name"] . " " . $doctor_data["surname"]. "  (" . $doctor_data["employee_id"].")";
        $data["doctor_speciality"] = $this->staff_model->getStaffSpeciality($doctor_id);
        $shift_details             = $this->onlineappointment_model->getShiftDetails($doctor_id);
        $charge_details            = $this->charge_model->getChargeDetailsById($shift_details['charge_id']);
        $currency_symbol           = $this->setting_model->get()[0]["currency_symbol"];
        $data["fees"]              = isset($charge_details->standard_charge) ? $currency_symbol . $charge_details->standard_charge : "";
        $data["duration"]          = $shift_details["consult_duration"];
        if (!empty($doctor_data['image'])) {
            $data['image'] = base_url("uploads/staff_images/" . $doctor_data['image']);
        } else {
            $data['image'] = base_url("uploads/staff_images/no_image.png");
        }

        echo json_encode($data);
    }

    public function getGlobalShift($id)
    {
        $shift = $this->onlineappointment_model->globalShift();
        if ($status == false) {
            echo json_encode($shift);
        }
    }

    public function doctorShiftById()
    {
        $doctor_id = $this->input->post("doctor_id");
        $shift     = $this->onlineappointment_model->doctorShiftById($doctor_id);
        echo json_encode($shift);
    }
    
    public function download_content($id)
    {
        $this->load->helper('file'); // Load file helper
        $content = $this->uploadcontent_model->get($id);
        $this->media_storage->filedownload($content->img_name, $content->dir_path);
    }
	
    
    public function share($key)
    {
        $data               = array();
        $id                 = $this->enc_lib->dycrypt($key);
        
        $data['share_data'] = $this->sharecontent_model->getShareContentWithDocuments($id);
        
        $this->load->view('share', $data);

    }
    
}
