<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ward extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/setup/ward');
        $this->session->set_userdata('sub_menu', 'bed');
        $data['ward_list']  = $this->Ward_Model->ward_list();
        $data['floor_list'] = $this->floor_model->floor_list();
        $data['dept_list']  = $this->Ward_Model->getdepartment();
        $this->load->view('layout/header');
        $this->load->view('setup/Ward', $data);
        $this->load->view('layout/footer');
    }

    public function add()
    {
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('department_id', $this->lang->line('department_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('floor_id', $this->lang->line('floor_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'          => form_error('name'),
                'department_id' => form_error('department_id'),
                'floor_id'      => form_error('floor_id'),
                'description'   => form_error('description'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $ward = array(
                'name'        => $this->input->post('name'),
                'dep_id'      => $this->input->post('department_id'),
                'floor_id'    => $this->input->post('floor_id'),
                'description' => $this->input->post('description'),
            );
            $this->Ward_Model->saveWard($ward);
            $msg   = $this->lang->line('ward_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function edit($id)
    {

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('department_id', $this->lang->line('department_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('floor_id', $this->lang->line('floor_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'          => form_error('name'),
                'department_id' => form_error('department_id'),
                'floor_id'      => form_error('floor_id'),
                'description'   => form_error('description'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $ward = array(
                'id'          => $id,
                'name'        => $this->input->post('name'),
                'dep_id'      => $this->input->post('department_id'),
                'floor_id'    => $this->input->post('floor_id'),
                'description' => $this->input->post('description'),
            );
            $this->Ward_Model->saveWard($ward);
            $msg   = $this->lang->line('ward_updated_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function getdata($id)
    {
        $data['ward_data']  = $this->Ward_Model->ward_list($id);
        $data['floor_list'] = $this->floor_model->floor_list();
        $data['dept_list']  = $this->Ward_Model->getdepartment();
        $this->load->view('setup/editWardModel', $data);
    }

    public function delete($id)
    {
        if (!empty($id)) {
            $this->Ward_Model->delete($id);
        }
        redirect('admin/setup/ward');
    }

}
