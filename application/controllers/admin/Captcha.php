<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Captcha extends Admin_Controller
{
    public $custom_fields_list = array();
    public function __construct()
    {
        parent::__construct();
        $this->load->model('captcha_model');
        $this->load->library('captchalib');
        $this->load->helper("customfield_helper");
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('captcha_setting', 'can_view')) { 
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'admin/captcha/index');
        $data['inserted_fields'] = $this->captcha_model->getSetting();
        $data['fields']          = get_captcha_editable_fields();
        $this->load->view('layout/header');
        $this->load->view('admin/captcha/index', $data);
        $this->load->view('layout/footer');
    }

    public function changeStatus()
    {
        if (!$this->rbac->hasPrivilege('captcha_setting', 'can_edit')) { 
            access_denied();
        }
        $data = array(
            'name'   => $this->input->post('name'),
            'status' => $this->input->post('status'),
        );
        $this->captcha_model->update_status($data);

    }

}