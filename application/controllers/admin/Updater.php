<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Updater extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('string');
    }

    public function index($chk = null, $ver = null)
    {
        $data = array();

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'admin/updater/index');
        $current_version = $this->customlib->getAppVersion();
        if ($chk == "") {
            $fn_response     = $this->checkup();
            $res_json        = json_decode($fn_response);
            $data['version'] = $res_json->version;

        } else {
            if ($ver != "") {
                $current_version = $ver;
            }
            if (!$this->session->flashdata('message') && !$this->session->flashdata('error')) {

                $fn_response     = $this->checkup();
                $res_json        = json_decode($fn_response);
                $data['version'] = $res_json->version;
            } else {
                if ($this->session->has_userdata('version')) {
                    $fn_response     = $this->checkup();
                    $res_json        = json_decode($fn_response);
                    $data['version'] = $res_json->version;

                }
            }
        }
        $data['current_version'] = $current_version;
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->auth->clear_messages();
            $this->auth->clear_error();
            $rsp_ver = $this->auth->autoupdate();
            $this->session->set_flashdata('message', $this->auth->messages());
            $this->session->set_flashdata('error', $this->auth->error());
            if ($rsp_ver) {

                redirect('admin/updater/index/' . random_string('alpha', 16) . "/" . $rsp_ver, 'refresh');
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/updater/index', $data);
        $this->load->view('layout/footer', $data);

    }

    public function checkup()
    {
        $version  = "";
        $response = $this->auth->checkupdate();

        if ($response) {
            $result = json_decode($response);

            if ($this->session->has_userdata('version')) {
                $version = $this->session->userdata('version');
                $version = $version['version'];

            }
            $this->session->set_flashdata('message', $this->auth->messages());
        } else {
            $this->session->set_flashdata('error', $this->auth->error());
        }
        return json_encode(array('version' => $version));
    }
}
