<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Floor extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/setup/floor');
        $this->session->set_userdata('sub_menu', 'bed');
        $data['floor'] = $this->floor_model->floor_list();
        $this->load->view('layout/header');
        $this->load->view('setup/floor', $data);
        $this->load->view('layout/footer');
    }

    public function add()
    {
        $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array('required',
                array('check_exists', array($this->floor_model, 'valid_floor')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $floor = array(
                'name'        => $this->input->post('name'),
                'description' => $this->input->post('description'),
            );
            $this->floor_model->savefloor($floor); 
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getDataByid($id)
    {
        $data['floor_data'] = $this->floor_model->floor_list($id);
        $this->load->view('setup/editFloorModal', $data);
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
            $floor = array(
                'id'          => $id,
                'name'        => $this->input->post('name'),
                'description' => $this->input->post('description'),
            );

            $this->floor_model->savefloor($floor); 
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        $this->floor_model->delete($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

}
