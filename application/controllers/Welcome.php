<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends Front_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->config('form-builder');
        $this->load->library(array('mailer', 'form_builder'));
        $this->load->library('captchalib');
        $this->config->load("mailsms");
        $this->load->library('mailsmsconf');
        $this->load->library('system_notification');
        $this->load->model('prefix_model');
        $this->load->helper('custom');
        $this->notification            = $this->config->item('notification');
        $this->notificationurl         = $this->config->item('notification_url');
        $this->patient_notificationurl = $this->config->item('patient_notification_url');
        $this->load->library('Ajax_pagination');
        $this->hospital_details       = $this->setting_model->getSetting();
        $this->banner_content         = $this->config->item('ci_front_banner_content');
        $this->perPage                = 12;
        $ban_notice_type              = $this->config->item('ci_front_notice_content');
        $this->data['banner_notices'] = $this->cms_program_model->getByCategory($ban_notice_type, array('start' => 0, 'limit' => 5));
        $this->time_format            = $this->customlib->getHospitalTimeFormat();
        $this->sch_setting_detail     = $this->setting_model->getSetting();

        date_default_timezone_set($this->hospital_details->timezone);

        $this->load->model("holiday_model");

    }

    public function index()
    {
        $menu_list                = $this->cms_menu_model->getBySlug('main-menu');
        $this->data['main_menus'] = $this->cms_menuitems_model->getMenus($menu_list['id']);
        reset($this->data['main_menus']);
        $first_key                   = key($this->data['main_menus']);
        $home_page_slug              = $this->data['main_menus'][$first_key]['page_slug'];
        $patientpanel                = $this->customlib->patientpanel();
        $this->data['patientpanel']  = $patientpanel;
        $setting                     = $this->frontcms_setting_model->get();
        $this->data['active_menu']   = $home_page_slug;
        $this->data['page_side_bar'] = $setting->is_active_sidebar;
        $home_page                   = $this->config->item('ci_front_home_page_slug');
        $result                      = $this->cms_program_model->getByCategory($this->banner_content);
        $this->data['page']          = $this->cms_page_model->getBySlug($home_page_slug);

        if (!empty($result)) {
            $this->data['banner_images'] = $this->cms_program_model->front_cms_program_photos($result[0]['id']);
        }

        $this->load_theme('home');
    }   

    public function page($slug)
    {
        $patientpanel               = $this->customlib->patientpanel();
        $this->data['patientpanel'] = $patientpanel;
        $page                       = $this->cms_page_model->getBySlug($slug);
        if (!$page) {
            $this->data['page'] = $this->cms_page_model->getBySlug('404-page');
        } else {
            $this->data['page'] = $this->cms_page_model->getBySlug($slug);
        }
        if ($page['is_homepage']) {
            redirect('frontend');
        }
        $this->data['active_menu']       = $slug;
        $this->data['page_side_bar']     = $this->data['page']['sidebar'];
        $this->data['page_content_type'] = "";
        if (!empty($this->data['page']['category_content'])) {
            $content_array = $this->data['page']['category_content'];
            reset($content_array);
            $first_key            = key($content_array);
            $totalRec             = count($this->cms_program_model->getByCategory($content_array[$first_key]));
            $config['target']     = '#postList';
            $config['base_url']   = base_url() . 'welcome/ajaxPaginationData';
            $config['total_rows'] = $totalRec;
            $config['per_page']   = $this->perPage;
            $config['link_func']  = 'searchFilter';
            $this->ajax_pagination->initialize($config);
            //get the posts data
            $this->data['page']['category_content'][$first_key] = $this->cms_program_model->getByCategory($content_array[$first_key], array('limit' => $this->perPage));
            $this->data['page_content_type']                    = $content_array[$first_key];
            //load the view
        }
        $this->data['page_form'] = false;
        if (strpos($page['description'], '[form-builder:') !== false) {
            $this->data['page_form'] = true;
            $start                   = '[form-builder:';
            $end                     = ']';
            $form_name               = $this->customlib->getFormString($page['description'], $start, $end);
            $form                    = $this->config->item($form_name);
            $this->data['form_name'] = $form_name;
            $this->data['form']      = $form;
            if (!empty($form)) {
                foreach ($form as $form_key => $form_value) {
                    if (isset($form_value['validation'])) {                       
                        $display_string = strtolower(preg_replace('/[^A-Za-z0-9\-]/', ' ', $form_value['id']));                       
                        $this->form_validation->set_rules($form_value['id'],  $this->lang->line($display_string), $form_value['validation']);
                    }
                }
                if ($this->form_validation->run() == false) {

                } else {
                    $setting          = $this->frontcms_setting_model->get();
                    $response_message = $form['email_title']['mail_response'];
                    $record           = $this->input->post();

                    if ($record['form_name'] == 'contact_us') {
                        $cont_data = array(
                            'name'    => $this->input->post('name'),
                            'source'  => 'Online',
                            'email'   => $this->input->post('email'),
                            'purpose' => $this->input->post('subject'),
                            'date'    => date('Y-m-d'),
                            'note'    => $this->input->post('description'),
                        );
                        $visitor_id = $this->visitors_model->add($cont_data);
                    }

                    if ($record['form_name'] == 'complain') {
                        $complaint_data = array(                           
                            'complaint_type_id' => 1,
                            'source'         => 'Online',
                            'name'           => $this->input->post('name'),
                            'email'          => $this->input->post('email'),
                            'contact'        => $this->input->post('contact'),
                            'date'           => date('Y-m-d'),
                            'description'    => $this->input->post('description'),
                        );

                        $complaint_id = $this->complaint_model->add($complaint_data);
                    }

                    $email_subject = $record['email_title'];
                    $mail_body     = "";
                    unset($record['email_title']);
                    unset($record['submit']);
                    foreach ($record as $fetch_k_record => $fetch_v_record) {
                        $mail_body .= ucwords($fetch_k_record) . ": " . $fetch_v_record;
                        $mail_body .= "<br/>";
                    }
                    if (!empty($setting) && $setting->contact_us_email != "") {
                        $this->mailer->send_mail($setting->contact_us_email, $email_subject, $mail_body);
                    }

                    $this->session->set_flashdata('msg', $response_message);
                    redirect('page/' . $slug, 'refresh');
                }
            }
        }
        $this->load_theme('pages/page');
    }

    public function ajaxPaginationData()
    {
        $page              = $this->input->post('page');
        $page_content_type = $this->input->post('page_content_type');
        if (!$page) {
            $offset = 0;
        } else {
            $offset = $page;
        }
        $data['page_content_type'] = $page_content_type;
        //total rows count
        $totalRec = count($this->cms_program_model->getByCategory($page_content_type));
        //pagination configuration
        $config['target']     = '#postList';
        $config['base_url']   = base_url() . 'welcome/ajaxPaginationData';
        $config['total_rows'] = $totalRec;
        $config['per_page']   = $this->perPage;
        $config['link_func']  = 'searchFilter';
        $this->ajax_pagination->initialize($config);
        //get the posts data
        $data['category_content'] = $this->cms_program_model->getByCategory($page_content_type, array('start' => $offset, 'limit' => $this->perPage));
        //load the view
        $this->load->view('themes/turquoise_blue/pages/ajax-pagination-data', $data, false);
    }

    public function read($slug)
    {
        $this->data['active_menu']    = $this->lang->line('home');
        $page                         = $this->cms_program_model->getBySlug($slug);
        $this->data['page_side_bar']  = $page['sidebar'];
        $this->data['featured_image'] = $page['feature_image'];
        $this->data['page']           = $page;        
        $patientpanel               = $this->customlib->patientpanel();
        $this->data['patientpanel'] = $patientpanel;        
        $this->load_theme('pages/read');
    }

    public function check_captcha($captcha)
    {
        if ($captcha == "") {
            $this->form_validation->set_message('check_captcha', $this->lang->line("please_enter_captcha"));
            return false;
        }
        if ($captcha != $this->session->userdata('captchaCode')):
            $this->form_validation->set_message('check_captcha', $this->lang->line("incorrect_captcha"));
            return false;
        else:
            return true;
        endif;
    }

    public function userlogin($username = null, $password = null)
    {
        if ($username != null && $password != null) {
            $login_post = array(
                'username' => $username,
                'password' => $password,
            );
            goto register_login;
        }
        $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|xss_clean');
        if ($this->captchalib->is_captcha('appointment')) {
            $this->form_validation->set_rules('captcha_login', 'Captcha', 'trim|required|callback_check_captcha');
        }
        if ($this->form_validation->run() == false) {
            $jsons = array(
                'username' => form_error("username"),
                'password' => form_error("password"),
            );
            if ($this->captchalib->is_captcha('appointment')) {
                $jsons['captcha'] = form_error('captcha_login');
            }
            $json_array = array('status' => '0', 'error' => $jsons);
            echo json_encode($json_array);
            die;
        } else {
            $login_post = array(
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
            );
            register_login:
            $login_details  = $this->user_model->checkLogin($login_post);
            $setting_result = $this->setting_model->get();
            if (isset($login_details) && !empty($login_details)) {
                $user = $login_details[0];
                if ($user->is_active == "yes") {
                    if ($user->role == "patient") {
                        $result = $this->user_model->read_user_information($user->id);
                    }
                    if ($result[0]->lang_id != 0) {
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
                                'mobileno'        => $result[0]->mobileno,
                                'email'           => $result[0]->email,
                                'date_format'     => $setting_result[0]['date_format'],
                                'currency_symbol' => $setting_result[0]['currency_symbol'],
                                'timezone'        => $setting_result[0]['timezone'],
                                'sch_name'        => $setting_result[0]['name'],
                                'language'        => array('lang_id' => $setting_result[0]['lang_id'], 'language' => $setting_result[0]['language']),
                                'is_rtl'          => $lang_data['is_rtl'],
                                'time_format'     => $check_time_format,
                                'theme'           => $setting_result[0]['theme'],
                                'image'           => $result[0]->image,
                                'prefix'          => $prefix_array,
								'superadmin_restriction'           => $setting_result[0]['superadmin_restriction'],
                            );
                            $this->session->set_userdata('patient', $session_data);
                            $this->customlib->setUserLog($result[0]->username, $result[0]->role);
                        }
                    } else {
                        $jsons = array(
                            'incorrect_credentials' => $this->lang->line("account_suspended"),
                        );
                        $json_array = array('status' => '0', 'error' => $jsons);
                        echo json_encode($json_array);
                        die;
                    }
                } else {
                    $jsons = array(
                        'incorrect_credentials' => $this->lang->line("administrator_message"),
                    );
                    $json_array = array('status' => '0', 'error' => $jsons);
                    echo json_encode($json_array);
                    die;
                }
            } else {
                $jsons = array(
                    'incorrect_credentials' => $this->lang->line("invalid_username_or_password"),
                );
                $json_array = array('status' => '0', 'error' => $jsons);
                echo json_encode($json_array);
                die;
            }
        }
    }

    public function appointment()
    {
        $this->load->model("onlineappointment_model");
        $patientpanel               = $this->customlib->patientpanel();
        $this->data['patientpanel'] = $patientpanel;
        $this->config->load("payroll");
        $yesno_condition               = $this->config->item('yesno_condition');
        $this->data['yesno_condition'] = $yesno_condition;
        $setting                       = $this->frontcms_setting_model->get();
        $this->load->helper('customfield_helper');
        $this->data['page_side_bar'] = $setting->is_active_sidebar;
        $this->data['active_menu']   = $this->lang->line("appointment");
        $this->data['gender']        = $this->customlib->getGender();
        $this->data['page']          = array('title' => $this->lang->line("appointment"), 'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '');        
        $specialist                  = $this->staff_model->getSpecialist();
        $this->data["specialist"]    = $specialist;        
        $params                      = $this->input->post("doctor") . ',' . $this->input->post("shift") . ',' . $this->customlib->dateFormatToYYYYMMDD($this->input->post("date")) . ',' . $this->input->post("global_shift");
        $custom_fields               = $this->customfield_model->getByBelongPatientPanel('appointment');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[appointment][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $this->form_validation->set_rules('doctor', $this->lang->line("doctor"), "trim|required|xss_clean");
        $this->form_validation->set_rules('specialist', $this->lang->line("specialist"), "trim|required|xss_clean");
        $this->form_validation->set_rules('date', $this->lang->line("date"), "trim|required|xss_clean");
        $this->form_validation->set_rules('shift', $this->lang->line("slot"), "trim|required|xss_clean");
        $this->form_validation->set_rules('global_shift', $this->lang->line("shift"), "trim|required|xss_clean");
        $this->form_validation->set_rules('slot', $this->lang->line("available_slot"), 'trim|required|callback_check_slot[' . $params . ']');
        $this->form_validation->set_rules('message', $this->lang->line("message"), 'trim|required');
     

        if ($this->form_validation->run() == false) {
            if (empty($this->input->post())) {
                $this->load_theme('form/appointment', $this->config->item('front_layout'));
            } else {
                $msg = array(
                    'date'       => form_error('date'),
                    'specialist' => form_error('specialist'),
                    'doctor'     => form_error('doctor'),
                    'global_shift' => form_error('global_shift'),
                    'shift'      => form_error('shift'),
                    'message'    => form_error('message'),
                    'slot'       => form_error('slot'),
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
                $json_array = array('status' => '0', 'error' => $error_msg);
                echo json_encode($json_array);
            }
        } else {
            $doctor       = $this->input->post("doctor");
            $shift        = $this->input->post("shift");
            $slots        = $this->customlib->getSlotByDoctorShift($doctor, $shift);
            $slot         = $slots[$this->input->post("slot")];
            $getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($this->input->post("doctor"), $this->input->post("global_shift"), date("l", strtotime($this->input->post('date'))));
            $date         = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date'));
            $current_time = date('Y-m-d H:i');

            $appointments   = $this->onlineappointment_model->checkAppointmentsSlotBooked($this->input->post("doctor"), $getDoctorShiftTimeId->id,  $date." ".date("H:i:s", strtotime($slot)), $current_time,0);

            if (!empty($appointments)) {
              echo   json_encode(['status'=>2,'msg'=>'Appointment not available, Please try after some time.']);
          
            }else{
                if ($this->input->post("patient_type") == "new patient") {
                    $this->register();
                    $date         = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date'));
                    $shift        = $this->input->post("shift");
                    $doctor       = $this->input->post("doctor");
                    $global_shift = $this->input->post("global_shift");
                    $slots        = $this->customlib->getSlotByDoctorShift($doctor, $shift);
                    $slot         = $slots[$this->input->post("slot")];
                    $live_consult = $this->input->post('live_consult');                
                    $day          = date("l", strtotime($this->input->post('date')));    
                    $getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($doctor, $global_shift, $day);
                
                    $date_create=date("Y-m-d H:i:s");
                    $appointment  = array(
                        "patient_id"         => $this->session->userdata("patient")["patient_id"],
                        "specialist"         => $this->input->post("specialist"),
                        "doctor"             => $this->input->post('doctor'),
                        'doctor_shift_time_id'           => $getDoctorShiftTimeId->id,
                        "live_consult"       => $live_consult,
                        "is_queue"           => 0,
                        "date"               => $date." ".date("H:i:s", strtotime($slot)),
                        "message"            => $this->input->post('message'),                    
                        "appointment_status" => "pending",
                        "Source"             => "Online",
                        "priority"           => 1,
                        'created_time'=>$date_create,                
                        'rejected_time'=>date('Y-m-d H:i:s',strtotime('+10 minutes',strtotime($date_create))),
                      
    
                    );
                    $date_create=date("Y-m-d H:i:s");
                    
                    $appointment = $this->security->xss_clean($appointment);
                    $insert_id   = $this->onlineappointment_model->addAppointment($appointment);                    
                    /* insert custom field start */
    
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
    
                    /* insert custom field end*/
                    $patient_name = $this->session->userdata("patient")["name"];
                    $mobileno     = $this->session->userdata("patient")["mobileno"];
                    $email        = $this->session->userdata("patient")["email"];                
                    $doctor       = $this->input->post('doctor');
                    $time         = date("H:i:s", strtotime($slot));
                    $patient_id   = $this->session->userdata("patient")["patient_id"];
                    
                    $this->session->set_flashdata("success_msg",$this->lang->line("success_message"));
                    
                    $json_array = array('status' => '1', 'msg' => "Appointment Booked");
                    echo json_encode($json_array);
                } else {
                    if (empty($this->session->userdata("patient"))) {
                        $this->userlogin();
                    }
                    $session_data = $this->session->userdata("patient");
                    $date         = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date'));
                    $shift        = $this->input->post("shift");
                    $doctor       = $this->input->post("doctor");
                    $global_shift = $this->input->post("global_shift");
                    $slots        = $this->customlib->getSlotByDoctorShift($doctor, $shift);
                    $live_consult = $this->input->post('live_consult');
                    $slot         = $slots[$this->input->post("slot")];
                    $time         = date("H:i:s", strtotime($slot));                
                    $day          = date("l", strtotime($this->input->post('date')));           
                    $getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($doctor, $global_shift, $day);             
                
                    $appointment  = array(
                        "patient_id"         => $this->session->userdata("patient")["patient_id"],
                        "specialist"         => $this->input->post('specialist'),
                        "doctor"             => $this->input->post('doctor'),
                       'doctor_shift_time_id'  => $getDoctorShiftTimeId->id,
                        "date"               => $date." ".$time,
                        "live_consult"       => $live_consult,
                        "message"            => $this->input->post('message'),
                        "is_queue"           => 0,                   
                        "appointment_status" => "pending",
                        "source"             => "Online",
                        'priority'           => $this->input->post('priority'),
                    );
                    $patient_name = $this->session->userdata("patient")["name"];
                    $mobileno     = $this->session->userdata("patient")["mobileno"];
                    $email        = $this->session->userdata("patient")["email"];
                    
                    $doctor       = $this->input->post('doctor');
                    $appointment = $this->security->xss_clean($appointment);
                    $insert_id   = $this->onlineappointment_model->addAppointment($appointment);
    
                    /* insert custom field start */
    
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
    
                    /* insert custom field end*/
                    $patient_id = $this->session->userdata("patient")["patient_id"];                
                    $date_appoint = $this->customlib->dateFormatToYYYYMMDDHis($date." ".$time, $this->customlib->getHospitalTimeFormat());
                    $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('doctor'));
                    $event_data     = array(
                        'appointment_date' => $this->customlib->YYYYMMDDHisTodateFormat($date." ".$time, $this->customlib->getHospitalTimeFormat()),
                        'patient_id'       => $this->session->userdata("patient")["patient_id"],
                        'doctor_id'        => $this->input->post('doctor'),
                        'doctor_name'      => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                        'message'          => $this->input->post('message'),
                    );
    
                    $this->system_notification->send_system_notification('notification_appointment_created', $event_data);                
                    $this->session->set_flashdata("success_msg",$this->lang->line("success_message"));
                    $json_array = array('status' => '1', 'msg' => "Appointment Booked");
                    echo json_encode($json_array);
                }
            }


        
        }
    }

    public function register()
    {
        $this->form_validation->set_rules('patient_name', $this->lang->line('patient_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|numeric|xss_clean');
        if ($this->captchalib->is_captcha('appointment')) {
            $this->form_validation->set_rules('captcha_register', 'Captcha', 'trim|required|callback_check_captcha');
        }

        if ($this->form_validation->run() == false) {
            $json = array(
                'patient_name' => form_error('patient_name'),
                'email'        => form_error('email'),
                'gender'       => form_error('gender'),
                'phone'        => form_error('phone'),
            );
            if ($this->captchalib->is_captcha('appointment')) {
                $json['captcha'] = form_error('captcha_register');
            }
            $json_array = array('status' => '0', 'error' => $json);
            echo json_encode($json_array);
            die;
        } else {
            $dobdate = $this->input->post('dob');
            if ($dobdate == "") {
                $dob = "";
            } else {
                $dob = $this->customlib->dateFormatToYYYYMMDD($dobdate);
            }
            $email    = $this->input->post('email');
            $mobileno = $this->input->post('phone');

            if (($mobileno != "") && ($email != "")) {
                $result = $this->patient_model->checkmobileemail($mobileno, $email);
                if ($result == 1) {
                    $jsons = array(
                        'phone_email_exist' => $this->lang->line('mobile_email_already_exist'),
                    );
                    $json_array = array('status' => '0', 'error' => $jsons);
                    echo json_encode($json_array);
                    die;
                }
            }
            if ($mobileno != "") {
                $result = $this->patient_model->checkmobilenumber($mobileno);
                if ($result == 1) {
                    $jsons = array(
                        'mobile_exist' => $this->lang->line('mobile_already_exist'),
                    );
                    $json_array = array('status' => '0', 'error' => $jsons);
                    echo json_encode($json_array);
                    die;
                }
            }
            if ($email != "") {
                $result = $this->patient_model->checkemail($email);
                if ($result == 1) {
                    $jsons = array(
                        'email_exist' => $this->lang->line('email_already_exist'),
                    );
                    $json_array = array('status' => '0', 'error' => $jsons);
                    echo json_encode($json_array);
                    die;
                }
            }
            $patient_data = array(
                'patient_name' => $this->input->post('patient_name'),
                'mobileno'     => $this->input->post('phone'),
                'email'        => $this->input->post('email'),
                'gender'       => $this->input->post('gender'),
                'is_active'    => 'yes',
            );
            $patient_data       = $this->security->xss_clean($patient_data);
            $insert_id          = $this->patient_model->add_front_patient($patient_data);            
            $scan_type          = $this->sch_setting_detail->scan_code_type; 
            $this->customlib->generatebarcode($insert_id,$scan_type);

            $user_password      = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
            $username           = "pat" . $insert_id;
            $data_patient_login = array(
                'username' => $username,
                'password' => $user_password,
                'user_id'  => $insert_id,
                'role'     => 'patient',
            );
            $data_patient_login = $this->security->xss_clean($data_patient_login);
            $this->user_model->add($data_patient_login);
            $array          = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'id' => $insert_id);
            $sender_details = array('id' => $insert_id, 'credential_for' => 'patient', 'username' => $username, 'password' => $user_password, 'contact_no' => $this->input->post('phone'), 'email' => $this->input->post('email'));

            $this->mailsmsconf->mailsms('login_credential', $sender_details);
            $this->userlogin($username, $user_password);
        }
    }

    public function check_slot($slot, $params)
    {
        if ($slot == '') {
            $this->form_validation->set_message('check_slot', $this->lang->line("available_slots_field_is_required"));
            return false;
        }
        list($doctor_id, $shift, $date, $global_shift) = explode(',', $params);
        $appointments                                  = $this->onlineappointment_model->getAppointments($doctor_id, $shift, $date);
        $time                                          = $this->customlib->getSlotByDoctorShift($doctor_id, $shift);
        $array                                         = array_column($appointments, 'time');
        if ($slot != '' && $doctor_id != '' && $shift != '' && $date != '') {
            if (count($time) > $slot) {
                $shift_time = date("H:i:s", strtotime($time[$slot]));
                if (in_array($shift_time, $array)) {
                    $this->form_validation->set_message('check_slot', $this->lang->line('this_slot_is_already_booked'));
                    return false;
                } else {
                    return true;
                }
            }
        }
    }

    public function getShiftById(){
        $shift_id = $this->input->post("id");
        $date = $this->customlib->dateFormatToYYYYMMDD($this->input->post("date"));
        $this->load->model('onlineappointment_model');
        $shift = $this->onlineappointment_model->getShiftById($shift_id);
        
        $end_time = $date." ".$shift['end_time'];
        $end_time = date("Y-m-d H:i:s" ,strtotime($end_time));
        $current_time = date("Y-m-d H:i:s");
        if($current_time>$end_time){
            echo json_encode(array("status" => 1));
        }else{
            echo json_encode(array("status" => 0));
        }
    }

    public function show_404()
    {
        $setting                     = $this->frontcms_setting_model->get();
        $this->data['page_side_bar'] = $setting->is_active_sidebar;
        $this->data['active_menu']   = 'show_404';
        $this->data['page']          = array('title' => '', 'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '');       
        $patientpanel                = $this->customlib->patientpanel();
        $this->data['patientpanel']  = $patientpanel;        
        $this->load_theme_form('show_404');
    }    
    
    public function chackvalidation()
    {       
        $custom_fields               = $this->customfield_model->getByBelongPatientPanel('appointment');
        $this->form_validation->set_rules("message", $this->lang->line('message'), 'trim|required|xss_clean');
        
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {            
            if ($custom_fields_value['validation']) {               
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];      
                $this->form_validation->set_rules("custom_fields[appointment][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }       
        
        if ($this->form_validation->run() == false) {           
            $msg  = array("message" => form_error('message'));     
            if (!empty($custom_fields)) {                   
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id   = $custom_fields_value['id'];
                            
                        $error_msg2["custom_fields[appointment][" . $custom_fields_id . "]"] = form_error("custom_fields[appointment][" . $custom_fields_id . "]");
                    }
                }
            }
            
            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }
                 
            $data = array('status' => 'fail', 'error' => $error_msg, 'message' => '');  
            
        } else {             
            $data = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($data);
    }


	public function annual_calendar(){
		$this->data['getholidays'] 	= $this->holiday_model->getholidays(1);
		$this->data['getactivity'] 	= $this->holiday_model->getActivity(1);
		$this->data['getvacation'] 	= $this->holiday_model->getVacation(1);
		 
		$patientpanel                = $this->customlib->patientpanel();
        $this->data['patientpanel']  = $patientpanel;	 
		 
		$setting                       = $this->frontcms_setting_model->get();
		$this->load->helper('customfield_helper');
		$this->data['page_side_bar'] = $setting->is_active_sidebar;
		$this->data['active_menu']   = $this->lang->line("annual_calendar");
		$this->data['page']          = array('title' => $this->lang->line("annual_calendar"), 'meta_title' => '', 'meta_keyword' => '', 'meta_description' => ''); 		
		
		$this->load_theme('pages/annual_calendar', $this->config->item('front_layout'));

	}







}
