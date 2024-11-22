<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Prefix extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('custom');
        $this->load->model('prefix_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('prefix_setting', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'prefix/index');
        $data['title']         = $this->lang->line('sms_config_list');
        $prefix_result         = $this->prefix_model->get();
        $data['prefix_result'] = $prefix_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/prefix/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('referral_person', 'can_edit')) {
            access_denied();
        }
        $prefix_result = $this->prefix_model->get();

        foreach ($prefix_result as $prefix_key => $prefix_value) {
            $this->form_validation->set_rules($prefix_value->type, $prefix_value->type, 'trim|required|xss_clean|callback__prefixRegex');
        }

        if ($this->form_validation->run() == false) {
            $msg = array();
            foreach ($prefix_result as $prefix_key => $prefix_value) {
                $msg[$prefix_value->type] = form_error($prefix_value->type);
            }

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $update_array         = array();
            $check_array          = array();
            $update_session_array = array();
            foreach ($prefix_result as $prefix_key => $prefix_value) {
                $check_array[]                             = $this->input->post($prefix_value->type);
                $update_array[]                            = array('type' => $prefix_value->type, 'prefix' => $this->input->post($prefix_value->type));
                $update_session_array[$prefix_value->type] = $this->input->post($prefix_value->type);
            }
            if (!has_duplicate_array($check_array)) {
                if ($this->prefix_model->update($update_array)) {
                    $session_data           = $this->session->userdata('hospitaladmin');
                    $session_data['prefix'] = $update_session_array;
                    $this->session->set_userdata('hospitaladmin', $session_data);
                }
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $msg   = array('duplicate' => $this->lang->line('prefix_must_be_unique_value'));
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }

        }

        echo json_encode($array);
    }

    public function _prefixRegex($userName)
    {
        if (preg_match('/^[A-Za-z]+$/', $userName)) {
            return true;
        } else {
            $this->form_validation->set_message('_prefixRegex', $this->lang->line('only_alphabetic_characters_allowed'));
            return false;
        }
    }

}
