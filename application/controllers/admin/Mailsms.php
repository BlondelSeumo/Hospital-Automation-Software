<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mailsms extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('smsgateway');
        $this->load->library('mailsmsconf');
        $this->load->library('datatables');
        $this->config->load("payroll");
        $this->search_type = $this->config->item('search_type');
        $this->mailer;
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/log');
        $this->session->set_userdata('subsub_menu', 'reports/log/mailsms');

        $data['title'] = $this->lang->line('add_mail_sms');

        $select     = 'messages.*';
        $join       = array();
        $table_name = "messages";

        $search_type = $this->input->post("search_type");
        if (isset($search_type)) {
            $search_type = $this->input->post("search_type");
        } else {
            $search_type = "this_month";
        }

        if (empty($search_type)) {
            $search_type = "";
            $listMessage = $this->report_model->getReport($table_name,$select, $join);
        } else {
            $search_table     = "messages";
            $search_column    = "created_at";
            $additional       = array();
            $additional_where = array();
            $listMessage      = $this->report_model->searchReport($table_name,$select, $join,  $search_type, $search_table, $search_column);
        }
        $data['listMessage'] = $listMessage;
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;

        $this->load->view('layout/header');
        $this->load->view('admin/mailsms/index', $data);
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
                'search_type' => $this->input->post('search_type'),
                'date_from'   => $this->input->post('date_from'),
                'date_to'     => $this->input->post('date_to'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function mailsmsreports()
    {
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $start_date            = '';
        $end_date              = '';
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
        $reportdata = $this->report_model->mailsmsRecord($start_date, $end_date);

        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {

            foreach ($reportdata->data as $key => $value) {

                $date_time = $this->customlib->YYYYMMDDHisTodateFormat($value->created_at, $this->time_format);
                if ($value->send_mail) {
                    $sendmail = "<i class='fa fa-check-square-o'></i><span class='hide'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $sendmail = "";
                }

                if ($value->send_sms) {
                    $sendsms = "<i class='fa fa-check-square-o'></i><span class='hide'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $sendsms = "";
                }

                if ($value->is_group) {
                    $isgroup = "<i class='fa fa-check-square-o'></i><span class='hide'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $isgroup = "";
                }

                if ($value->is_individual) {
                    $isindividual = "<i class='fa fa-check-square-o '></i><span class='hide'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $isindividual = "";
                }
                $row   = array();
                $row[] = $value->title;
                $row[] = $date_time;
                $row[]     = $sendmail;
                $row[]     = $sendsms;
                $row[]     = $isgroup;
                $row[]     = $isindividual;
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

    public function search()
    {
        $keyword       = $this->input->get_post('keyword');
        $category      = $this->input->get_post('category');
        $exp           = explode('-', $category);
        $category_name = $exp[0];
        $result        = array();
        if ($keyword != "" and $category != "") {
            if ($category_name == "Staff") {
                $category_role = $exp[1];
                $result        = $this->staff_model->searchNameLike($keyword, $category_role);
            } elseif ($category_name == "Patient") {
                $result = $this->patient_model->searchPatientNameLike($keyword);
            } else {

            }
        }

        echo json_encode($result);
    }

    public function compose()
    {
        if (!$this->rbac->hasPrivilege('email_sms', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Messaging');
        $this->session->set_userdata('sub_menu', 'notification/index');
        $data['title'] = $this->lang->line('add_sms');
        $userdata      = $this->customlib->getUserData();
        $carray        = array();
        if (!empty($data["resultlist"])) {
            foreach ($data["resultlist"] as $ckey => $cvalue) {
                $carray[] = $cvalue["id"];
            }
        }
        $data['roles'] = $this->role_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/mailsms/compose', $data);
        $this->load->view('layout/footer');
    }

    public function composemail()
    {
        if (!$this->rbac->hasPrivilege('email_sms', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Messaging');
        $this->session->set_userdata('sub_menu', 'notification/index');
        $data['title'] = $this->lang->line('add_mail');
        $userdata      = $this->customlib->getUserData();
        $carray        = array();

        if (!empty($data["resultlist"])) {
            foreach ($data["resultlist"] as $ckey => $cvalue) {
                $carray[] = $cvalue["id"];
            }
        }

        $data['roles'] = $this->role_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/mailsms/composeemail', $data);
        $this->load->view('layout/footer');
    }

    public function edit($id)
    {
        $data['title']       = $this->lang->line('add_vehicle');
        $data['id']          = $id;
        $editvehicle         = $this->vehicle_model->get($id);
        $data['editvehicle'] = $editvehicle;
        $listVehicle         = $this->vehicle_model->get();
        $data['listVehicle'] = $listVehicle;
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header');
            $this->load->view('admin/mailsms/edit', $data);
            $this->load->view('layout/footer');
        } else {
            $manufacture_year = $this->input->post('manufacture_year');
            $data             = array(
                'id'             => $this->input->post('id'),
                'vehicle_no'     => $this->input->post('vehicle_no'),
                'vehicle_model'  => $this->input->post('vehicle_model'),
                'driver_name'    => $this->input->post('driver_name'),
                'driver_licence' => $this->input->post('driver_licence'),
                'driver_contact' => $this->input->post('driver_contact'),
                'note'           => $this->input->post('note'),
            );
            ($manufacture_year != "") ? $data['manufacture_year'] = $manufacture_year : '';
            $this->vehicle_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('transport_updated_successfully') . '</div>');
            redirect('admin/mailsms/index');
        }
    }

    public function delete($id)
    {
        $data['title'] = $this->lang->line('fees_master_list');
        $this->vehicle_model->remove($id);
        redirect('admin/mailsms/index');
    }

    public function send_individual_mail()
    {      
        $this->form_validation->set_rules('individual_title', $this->lang->line('title'), 'required');
        $this->form_validation->set_rules('individual_message', $this->lang->line('message'), 'required');
        $this->form_validation->set_rules('user_list', $this->lang->line('recipient'), 'required');
        $this->form_validation->set_rules('individual_send_by', $this->lang->line('send_through'), 'required');
        if ($this->form_validation->run()) {
            $userlisting = json_decode($this->input->post('user_list'));
            $user_array  = array();
            foreach ($userlisting as $userlisting_key => $userlisting_value) {
                $array = array(
                    'category' => $userlisting_value[0]->category,
                    'user_id'  => $userlisting_value[0]->record_id,
                    'email'    => $userlisting_value[0]->email,
                    'mobileno' => $userlisting_value[0]->mobileno,
                    'app_key'  => $userlisting_value[0]->app_key,

                );
                $user_array[] = $array;
            }

            $sms_mail = $this->input->post('individual_send_by');
            if ($sms_mail == "sms") {
                $send_sms  = 1;
                $send_mail = 0;
            } else {
                $send_sms  = 0;
                $send_mail = 1;
            }

            $message       = $this->input->post('individual_message');
            $message_title = $this->input->post('individual_title');
            $data          = array(
                'is_individual' => 1,
                'title'         => $message_title,
                'message'       => $message,
                'send_mail'     => $send_mail,
                'send_sms'      => $send_sms,
                'user_list'     => json_encode($user_array),
            );

            $this->messages_model->add($data);

            if (!empty($user_array)) {

                if ($send_mail) {
                    if (!empty($this->mail_config)) {
                        foreach ($user_array as $user_mail_key => $user_mail_value) {
                            if ($user_mail_value['email'] != "") {
                                $this->mailer->send_mail($user_mail_value['email'], $message_title, $message, $_FILES);
                            }
                        }
                    }
                }

                if ($send_sms) {
                    foreach ($user_array as $user_mail_key => $user_mail_value) {
                        if ($user_mail_value['mobileno'] != "") {
                            $this->smsgateway->sendSMS($user_mail_value['mobileno'], strip_tags($message));
                        }
                    }
                }

            }
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('message_sent_successfully')));
        } else {

            $data = array(
                'individual_title'     => form_error('individual_title'),
                'individual_message'   => form_error('individual_message'),
                'individual_send_by[]' => form_error('individual_send_by[]'),
                'user_list'            => form_error('user_list'),
            );

            echo json_encode(array('status' => 1, 'msg' => $data));
        }
    }

    public function send_individual_sms()
    {      
        $this->form_validation->set_rules('individual_title', $this->lang->line('title'), 'required');
        $this->form_validation->set_rules('individual_message', $this->lang->line('message'), 'required');
        $this->form_validation->set_rules('user_list', $this->lang->line('recipient'), 'required');
        $this->form_validation->set_rules('individual_send_by[]', $this->lang->line('send_through'), 'required');
        $template_id = $this->input->post('individual_template_id');
        if ($this->form_validation->run()) {
            $userlisting = json_decode($this->input->post('user_list'));

            $user_array = array();
            foreach ($userlisting as $userlisting_key => $userlisting_value) {
                $array = array(
                    'category' => $userlisting_value[0]->category,
                    'user_id'  => $userlisting_value[0]->record_id,
                    'email'    => $userlisting_value[0]->email,
                    'mobileno' => $userlisting_value[0]->mobileno,
                    'app_key'  => $userlisting_value[0]->app_key,
                );
                $user_array[] = $array;
            }

            $sms_mail       = $this->input->post('individual_send_by[]');
            $send_mail      = in_array('mail', $sms_mail) ? 1 : 0;
            $send_sms       = in_array('sms', $sms_mail) ? 1 : 0;
            $send_mobileapp = in_array('push', $sms_mail) ? 1 : 0;
            $message        = $this->input->post('individual_message');
            $message_title  = $this->input->post('individual_title');
            $data           = array(
                'is_individual' => 1,
                'title'         => $message_title,
                'message'       => $message,
                'send_mail'     => $send_mail,
                'send_sms'      => $send_sms,
                'user_list'     => json_encode($user_array),
                'template_id'   => $template_id,
            );

            $this->messages_model->add($data);
            if (!empty($user_array)) {

                if ($send_mail) {
                    if (!empty($this->mail_config)) {
                        foreach ($user_array as $user_mail_key => $user_mail_value) {
                            if ($user_mail_value['email'] != "") {
                                $this->mailer->send_mail($user_mail_value['email'], $message_title, $message);
                            }
                        }
                    }
                }

                if ($send_sms) {

                    foreach ($user_array as $user_mail_key => $user_mail_value) {

                        if ($user_mail_value['mobileno'] != "") {

                            $this->smsgateway->sendSMS($user_mail_value['mobileno'], strip_tags($message), $template_id);
                        }
                    }
                }

                if ($send_mobileapp) {
                    foreach ($user_array as $user_mail_key => $user_mail_value) {
                        $push_array = array(
                            'title' => $message_title,
                            'body'  => $message);
                        if ($user_mail_value['app_key'] != "") {
                            $this->pushnotification->send($user_mail_value['app_key'], $push_array, "mail_sms");
                        }
                    }
                }
            }
            echo json_encode(array('status' => 0, 'msg' => "Message sent successfully"));
        } else {

            $data = array(
                'individual_title'     => form_error('individual_title'),
                'individual_message'   => form_error('individual_message'),
                'individual_send_by[]' => form_error('individual_send_by[]'),
                'user_list'            => form_error('user_list'),
            );

            echo json_encode(array('status' => 1, 'msg' => $data));
        }
    }

    public function send_group_sms()
    {
        $this->form_validation->set_rules('group_title', $this->lang->line('title'), 'required');
        $this->form_validation->set_rules('group_message', $this->lang->line('message'), 'required');
        $this->form_validation->set_rules('user[]', $this->lang->line('message_to'), 'required');
        $this->form_validation->set_rules('group_send_by[]', $this->lang->line('send_through'), 'required');
        if ($this->form_validation->run()) {
            $user_array     = array();
            $template_id    = $this->input->post('group_template_id');
            $sms_mail       = $this->input->post('group_send_by[]');
            $send_mail      = in_array('mail', $sms_mail) ? 1 : 0;
            $send_sms       = in_array('sms', $sms_mail) ? 1 : 0;
            $send_mobileapp = in_array('push', $sms_mail) ? 1 : 0;
            $message        = $this->input->post('group_message');
            $message_title  = $this->input->post('group_title');
            $data           = array(
                'is_group'    => 1,
                'title'       => $message_title,
                'message'     => $message,
                'send_sms'    => $send_sms,
                'send_mail'   => $send_mail,
                'template_id' => $template_id,
                'group_list'  => json_encode(array()),
            );

            $this->messages_model->add($data);
            $userlisting = $this->input->post('user[]');

            foreach ($userlisting as $users_key => $users_value) {
                if ($users_value == "patient") {
                    $patient_array = $this->patient_model->getPatientEmail();
                    if (!empty($patient_array)) {
                        foreach ($patient_array as $patient_key => $patient_value) {
                            $array = array(
                                'user_id'  => $patient_value['id'],
                                'email'    => $patient_value['email'],
                                'mobileno' => $patient_value['mobileno'],
                                'app_key'  => $patient_value['app_key'],

                            );
                            $user_array[] = $array;
                        }
                    }

                } else if (is_numeric($users_value)) {

                    $staff = $this->staff_model->getEmployeeByRoleID($users_value);
                    if (!empty($staff)) {
                        foreach ($staff as $staff_key => $staff_value) {
                            $array = array(
                                'user_id'  => $staff_value['id'],
                                'email'    => $staff_value['email'],
                                'mobileno' => $staff_value['contact_no'],
                            );
                            $user_array[] = $array;
                        }
                    }
                }
            }

            if (!empty($user_array)) {

                if ($send_mail) {
                    if (!empty($this->mail_config)) {
                        foreach ($user_array as $user_mail_key => $user_mail_value) {
                            if ($user_mail_value['email'] != "") {
                                $this->mailer->send_mail($user_mail_value['email'], $message_title, $message);
                            }
                        }
                    }
                }

                if ($send_sms) {
                    foreach ($user_array as $user_mail_key => $user_mail_value) {
                        if ($user_mail_value['mobileno'] != "") {
                            $this->smsgateway->sendSMS($user_mail_value['mobileno'], strip_tags($message), $template_id);
                        }
                    }
                }

                if ($send_mobileapp) {
                    foreach ($user_array as $user_mail_key => $user_mail_value) {
                        $push_array = array(
                            'title' => $message_title,
                            'body'  => $this->customlib->getLimitChar($message));
                        if ($user_mail_value['app_key'] != "") {

                            $this->pushnotification->send($user_mail_value['app_key'], $push_array, "mail_sms");
                        }
                    }
                }
            }

            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('success_message')));
        } else {

            $data = array(
                'group_title'     => form_error('group_title'),
                'group_message'   => form_error('group_message'),
                'group_send_by[]' => form_error('group_send_by[]'),
                'user[]'          => form_error('user[]'),
            );

            echo json_encode(array('status' => 1, 'msg' => $data));
        }
    }

    public function send_group_mail()
    {
        $this->form_validation->set_rules('group_title', $this->lang->line('title'), 'required');
        $this->form_validation->set_rules('group_message', $this->lang->line('message'), 'required');
        $this->form_validation->set_rules('user[]', $this->lang->line('message_to'), 'required');
        if ($this->form_validation->run()) {
            $user_array = array();
            $sms_mail   = $this->input->post('group_send_by');
            if ($sms_mail == "sms") {
                $send_sms  = 1;
                $send_mail = 0;
            } else {
                $send_sms  = 0;
                $send_mail = 1;
            }
            $message       = $this->input->post('group_message');
            $message_title = $this->input->post('group_title');
            $data          = array(
                'is_group'   => 1,
                'title'      => $message_title,
                'message'    => $message,
                'send_mail'  => $send_mail,
                'send_sms'   => $send_sms,
                'group_list' => json_encode(array()),
                'created_at' => date("Y-m-d H:i:s"),
            );

            $this->messages_model->add($data);
            $userlisting = $this->input->post('user[]');
            foreach ($userlisting as $users_key => $users_value) {
                if ($users_value == "patient") {
                    $patient_array = $this->patient_model->getPatientEmail();
                    if (!empty($patient_array)) {
                        foreach ($patient_array as $patient_key => $patient_value) {
                            $array = array(
                                'user_id'  => $patient_value['id'],
                                'email'    => $patient_value['email'],
                                'mobileno' => $patient_value['mobileno'],
                                'app_key'  => $patient_value['app_key'],

                            );
                            $user_array[] = $array;
                        }
                    }

                } else if (is_numeric($users_value)) {

                    $staff = $this->staff_model->getEmployeeByRoleID($users_value);
                    if (!empty($staff)) {
                        foreach ($staff as $staff_key => $staff_value) {
                            $array = array(
                                'user_id'  => $staff_value['id'],
                                'email'    => $staff_value['email'],
                                'mobileno' => $staff_value['contact_no'],
                            );
                            $user_array[] = $array;
                        }
                    }
                }
            }

            if (!empty($user_array)) {

                if ($send_mail) {
                    if (!empty($this->mail_config)) {
                        foreach ($user_array as $user_mail_key => $user_mail_value) {
                            if ($user_mail_value['email'] != "") {
                                $this->mailer->send_mail($user_mail_value['email'], $message_title, $message, $_FILES);
                            }
                        }
                    }
                }

                if ($send_sms) {
                    foreach ($user_array as $user_mail_key => $user_mail_value) {
                        if ($user_mail_value['mobileno'] != "") {
                            $this->smsgateway->sendSMS($user_mail_value['mobileno'], strip_tags($message));
                        }
                    }
                }
            }

            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('success_message')));
        } else {

            $data = array(
                'group_title'     => form_error('group_title'),
                'group_message'   => form_error('group_message'),
                'group_send_by[]' => form_error('group_send_by[]'),
                'user[]'          => form_error('user[]'),
            );

            echo json_encode(array('status' => 1, 'msg' => $data));
        }
    }

}
