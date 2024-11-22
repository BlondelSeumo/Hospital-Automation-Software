<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Wardtype extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/setup/wardtype');
        $this->session->set_userdata('sub_menu', 'bed');
        $data['wardtype_list'] = $this->Wardtype_Model->wardtype_list();
        $this->load->view('layout/header');
        $this->load->view('setup/Wardtype', $data);
        $this->load->view('layout/footer');
    }

    public function add()
    {
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'name' => form_error('name'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $Wardtype = array('name' => $this->input->post('name'));
            $this->Wardtype_Model->saveWardtype($Wardtype);
            $msg   = $this->lang->line('ward_type_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function getdata($id)
    {
        $data['wardtype_data'] = $this->Wardtype_Model->wardtype_list($id);
        $this->load->view('setup/editwardtypemodalview/edit', $data);
    }

    public function edit($id)
    {
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $Wardtype = array(
                'name' => $this->input->post('name'),
                'id'   => $id,
            );

            $this->Wardtype_Model->saveWardtype($Wardtype);
            $msg   = $this->lang->line('ward_type_updated_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

}
