<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notification extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->patient_notification = array('add_bad_stock', 'add_bag_stock', 'purchase_medicine', 'add_component_of_blood', 'live_meeting_add', 'live_meeting_start', 'add_referral_payment', 'generate_staff_id_card', 'add_death_record', 'staff_enabale_disable', 'staff_generate_payroll', 'staff_leave', 'staff_leave_status', 'add_medicine', 'add_payroll_payment');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('notice_board', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Messaging');
        $this->session->set_userdata('sub_menu', 'notification/index');
        $data['title']   = $this->lang->line('notifications');
        $notifications   = $this->notification_model->get();
        $userdata        = $this->customlib->getUserData();
        $user_role       = json_decode($this->customlib->getStaffRole());
        $role_id         = $userdata["role_id"];
        $user_id         = $userdata["id"];
        $data["user_id"] = $user_id;

        $notification_status = false;
        if (!empty($notifications)) {
            foreach ($notifications as $key => $value) {
                $created_by_name = $this->notification_model->getcreatedByName($value["created_id"]);
                $roles           = $value["roles"];
                $arr             = explode(",", $roles);
                if ($user_role->name == "Super Admin") {
                    $rname                                            = $this->notification_model->getRole($arr);
                    $data['notificationlist'][$key]                   = $notifications[$key];
                    $data['notificationlist'][$key]["role_name"]      = $rname;
                    $data['notificationlist'][$key]["createdby_name"] = $created_by_name["name"] . " " . $created_by_name["surname"];
                    $notification_status                              = true;
                } elseif ((in_array($role_id, $arr)) && ($value["created_id"] == $user_id)) {
                    $notification_status                              = true;
                    $rname                                            = $this->notification_model->getRole($arr);
                    $data['notificationlist'][$key]                   = $notifications[$key];
                    $data['notificationlist'][$key]["role_name"]      = $rname;
                    $data['notificationlist'][$key]["createdby_name"] = $created_by_name["name"] . " " . $created_by_name["surname"];
                } elseif ((in_array($role_id, $arr)) && (date($this->customlib->getHospitalDateFormat()) >= $this->customlib->YYYYMMDDTodateFormat($value['publish_date']))) {
                    $notification_status                              = true;
                    $rname                                            = $this->notification_model->getRole($arr);
                    $data['notificationlist'][$key]                   = $notifications[$key];
                    $data['notificationlist'][$key]["role_name"]      = $rname;
                    $data['notificationlist'][$key]["createdby_name"] = $created_by_name["name"] . " " . $created_by_name["surname"];
                }

            }
        }
        if (!$notification_status) {
            $data['notificationlist'] = array();
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/notification/notificationList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('notice_board', 'can_add')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Messaging');
        $this->session->set_userdata('sub_menu', 'notification/index');
        $data['title']      = $this->lang->line('add_notification');
        $data['title_list'] = $this->lang->line('notification_list');
        $data['roles']      = $this->role_model->get();
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('message', $this->lang->line('message'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('notice_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('publish_date', $this->lang->line('publish_on'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('visible[]', $this->lang->line('message_to'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

        } else {
            $patient     = "No";
            $staff       = "No";
            $staff_roles = array();
            $visible     = $this->input->post('visible');
            foreach ($visible as $key => $value) {
                $staff_roles[] = array('role_id' => $value, 'send_notification_id' => '');
                $staff         = "Yes";
            }
            $data = array(
                'message'         => $this->input->post('message'),
                'title'           => $this->input->post('title'),
                'date'            => $this->customlib->dateFormatToYYYYMMDD($this->input->post('date')), 
                'created_by'      => 'admin',
                'created_id'      => $this->customlib->getStaffID(),
                'visible_patient' => $patient,
                'visible_staff'   => $staff,
                'publish_date'    => $this->customlib->dateFormatToYYYYMMDD($this->input->post('publish_date')), 
            );
            $this->notification_model->insertBatch($data, $staff_roles);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/notification/add');
        }
        $data['roles'] = $this->role_model->get();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/notification/notificationAdd', $data);
        $this->load->view('layout/footer', $data);
    }

    public function edit($id)
    {
        $userdata         = $this->customlib->getUserData();
        $user_id          = $userdata["id"];
        $usernotification = $this->notification_model->get($id);
        if (!$this->rbac->hasPrivilege('notice_board', 'can_edit')) {
            if ($usernotification['created_id'] != $user_id) {
                access_denied();
            }
        }
        $data['id']           = $id;
        $notification         = $this->notification_model->get($id);
        $data['notification'] = $notification;
        $data['roles']        = $this->role_model->get();
        $data['title']        = $this->lang->line('edit_notification');
        $data['title_list']   = $this->lang->line('notification_list');
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('message', $this->lang->line('message'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('notice_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('publish_date', $this->lang->line('publish_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('visible[]', $this->lang->line('message_to'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

        } else {
            $patient     = "No";
            $staff       = "No";           
            $prev_roles  = $this->input->post('prev_roles');
            $visible     = $this->input->post('visible');
            $staff_roles = array();
            $inst_staff  = array();
            foreach ($visible as $key => $value) {

                if ($value == "patient") {
                    $patient = "Yes";
                } else if (is_numeric($value)) {
                    $inst_staff[]  = $value;
                    $staff_roles[] = array('role_id' => $value, 'send_notification_id' => '');
                    $staff         = "Yes";
                }
            }

            $to_be_del    = array_diff($prev_roles, $inst_staff);
            $to_be_insert = array_diff($inst_staff, $prev_roles);
            $insert       = array();
            if (!empty($to_be_insert)) {

                foreach ($to_be_insert as $to_insert_key => $to_insert_value) {
                    $insert[] = array('role_id' => $to_insert_value, 'send_notification_id' => '');
                }
            }

            $data = array(
                'id'              => $id,
                'message'         => $this->input->post('message'),
                'title'           => $this->input->post('title'),
                'date'            => $this->customlib->dateFormatToYYYYMMDD($this->input->post('date')), 
                'created_by'      => 'admin',
                'created_id'      => 1,
                'visible_patient' => $patient,
                'visible_staff'   => $staff,
                'publish_date'    => $this->customlib->dateFormatToYYYYMMDD($this->input->post('publish_date')), 
            );
            $this->notification_model->insertBatch($data, $insert, $to_be_del);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('notification_added_successfully') . '</div>');
            redirect('admin/notification/index');
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/notification/notificationEdit', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        $userdata         = $this->customlib->getUserData();
        $user_id          = $userdata["id"];
        $usernotification = $this->notification_model->get($id);
        if ((!$this->rbac->hasPrivilege('notice_board', 'can_edit'))) {
            if ($usernotification['created_id'] != $user_id) {
                access_denied();
            }
        }
        $this->notification_model->remove($id);
        redirect('admin/notification');
    }

    public function setting()
    {
        if (!$this->rbac->hasPrivilege('notification_setting', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'notification/setting');
        $data                     = array();
        $data['title']            = $this->lang->line('email_config_list');
        $notificationlist         = $this->notificationsetting_model->get();
        $data['notificationlist'] = $notificationlist;
        $this->form_validation->set_rules('email_type', $this->lang->line('email_type'), 'required');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $ids          = $this->input->post('ids');
            $update_array = array();
            foreach ($ids as $id_key => $id_value) {
                $array = array(
                    'id'      => $id_value,
                    'is_mail' => 0,
                    'is_sms'  => 0,
                );
                $mail         = $this->input->post('mail_' . $id_value);
                $sms          = $this->input->post('sms_' . $id_value);
                $notification = $this->input->post('notification_' . $id_value);
                if (isset($mail)) {
                    $array['is_mail'] = $mail;
                }
                if (isset($sms)) {
                    $array['is_sms'] = $sms;
                }
                if (isset($notification)) {
                    $array['is_notification'] = $notification;
                    $array['is_mobileapp']    = $notification;
                } else {
                    $array['is_notification'] = 0;
                    $array['is_mobileapp']    = 0;
                }

                $update_array[] = $array;

            }

            $this->notificationsetting_model->updatebatch($update_array);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/notification/setting');
        }

        $data['title'] = $this->lang->line('email_config_list');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/notification/setting', $data);
        $this->load->view('layout/footer', $data);
    }

    public function notification_setting()
    {
        if (!$this->rbac->hasPrivilege('system_notification_setting', 'can_edit')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'notification/setting');
        $data                     = array();
        $data['title']            = $this->lang->line('email_config_list');
        $notificationlist         = $this->notificationsetting_model->get_system_notification();
        $data['notificationlist'] = $notificationlist;

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $ids          = $this->input->post('ids');
            $update_array = array();
            foreach ($ids as $id_key => $id_value) {
                $array = array(
                    'id'         => $id_value,
                    'is_active'  => 0,
                    'is_staff'   => 0,
                    'is_patient' => 0,

                );
                $event   = $this->input->post('event_' . $id_value);
                $staff   = $this->input->post('staff_' . $id_value);
                $patient = $this->input->post('patient_' . $id_value);

                if (isset($event)) {
                    $array['is_active'] = $event;
                }

                if (isset($staff)) {
                    $array['is_staff'] = $staff;
                }

                if (isset($patient)) {
                    $array['is_patient'] = $patient;
                }

                $update_array[] = $array;

            }

            $this->notificationsetting_model->notificationupdatebatch($update_array);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/notification/system_notification_setting');
        }

        $data['title'] = $this->lang->line('email_config_list');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/notification/setting', $data);
        $this->load->view('layout/footer', $data);
    }

    public function gettemplate()
    {
        $id             = $this->input->post('id');
        $data['record'] = $this->notificationsetting_model->get($id);
        $template       = $this->load->view('admin/notification/gettemplate', $data, true);
        $response       = array('status' => 1, 'template' => $template);
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function savetemplate()
    {
        $response = array();
        $this->form_validation->set_rules('temp_id', $this->lang->line('template_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('template_message', $this->lang->line('template_message'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'temp_id'          => form_error('temp_id'),
                'template_message' => form_error('template_message'),
            );
            $response = array('status' => 0, 'error' => $data);

        } else {

            $data_update = array(
                'id'          => $this->input->post('temp_id'),
                'template'    => $this->input->post('template_message'),
                'template_id' => $this->input->post('template_id'),
                'subject'     => $this->input->post('template_subject'),
            );

            $this->notificationsetting_model->update($data_update);
            $response = array('status' => 1, 'message' => $this->lang->line('update_message'));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function read()
    {
        $array           = array('status' => "fail", 'msg' => $this->lang->line('something_went_wrong'));
        $notification_id = $this->input->post('notice');
        if ($notification_id != "") {
            $staffid = $this->customlib->getStaffID();
            $data    = $this->notification_model->updateStatusforStaff($notification_id, $staffid);
            $array   = array('status' => "success", 'data' => $data, 'msg' => $this->lang->line('delete_success_message'));
        }

        echo json_encode($array);
    }

    public function system_notification_setting()
    {
        if (!$this->rbac->hasPrivilege('system_notification_setting', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'notification/system_notification_setting');
        $data                     = array();
        $data['title']            = $this->lang->line('email_config_list');
        $notificationlist         = $this->notificationsetting_model->get_system_notification();
        $data['notificationlist'] = $notificationlist;

        $this->form_validation->set_rules('email_type', $this->lang->line('email_type'), 'required');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $ids          = $this->input->post('ids');
            $update_array = array();
            foreach ($ids as $id_key => $id_value) {
                $array = array(
                    'id'      => $id_value,
                    'is_mail' => 0,
                    'is_sms'  => 0,
                );
                $mail         = $this->input->post('mail_' . $id_value);
                $sms          = $this->input->post('sms_' . $id_value);
                $notification = $this->input->post('notification_' . $id_value);

                if (isset($mail)) {
                    $array['is_mail'] = $mail;
                }

                if (isset($sms)) {
                    $array['is_sms'] = $sms;
                }

                if (isset($notification)) {
                    $array['is_notification'] = $notification;
                    $array['is_mobileapp']    = $notification;
                } else {
                    $array['is_notification'] = 0;
                    $array['is_mobileapp']    = 0;
                }

                $update_array[] = $array;

            }

            $this->notificationsetting_model->updatebatch($update_array);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/notification/system_notification_setting');
        }

        $data['is_patient_notification'] = $this->patient_notification;
        $data['title']                   = $this->lang->line('email_config_list');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/notification/system_notification_setting', $data);
        $this->load->view('layout/footer', $data);
    }

    public function save_system_notification()
    {
        $response = array();
        $this->form_validation->set_rules('template_subject', $this->lang->line('subject'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('staff_message', $this->lang->line('staff_message'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'template_subject' => form_error('template_subject'),
                'staff_message'    => form_error('staff_message'),
                'patient_message'  => form_error('patient_message'),
            );
            $response = array('status' => 0, 'error' => $data);

        } else {

            $data_update = array(
                'id'              => $this->input->post('temp_id'),
                'subject'         => $this->input->post('template_subject'),
                'staff_message'   => $this->input->post('staff_message'),
                'patient_message' => $this->input->post('patient_message'),
            );

            $this->notificationsetting_model->update_system_notification($data_update);
            $response = array('status' => 1, 'message' => $this->lang->line('update_message'));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function getsystem_notification_setting()
    {
        $id                              = $this->input->post('id');
        $data['record']                  = $this->notificationsetting_model->get_system_notification($id);
        $data['is_patient_notification'] = $this->patient_notification;
        $template                        = $this->load->view('admin/notification/getsystem_notification', $data, true);
        $response                        = array('status' => 1, 'template' => $template);
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }
}
