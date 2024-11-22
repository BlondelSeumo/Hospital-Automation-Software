<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Room extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/setup/room');
        $this->session->set_userdata('sub_menu', 'bed');
        $data['roomtype_list'] = $this->Roomtype_Model->roomtypelist();
        $data['floor_list']    = $this->floor_model->floor_list();
        $data['dept_list']     = $this->Ward_Model->getdepartment();
        $data['room_list']     = $this->Room_Model->roomlist();
        $this->load->view('layout/header');
        $this->load->view('setup/Room', $data);
        $this->load->view('layout/footer');
    }

    public function add()
    {
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('floor_id', $this->lang->line('floor_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('roomtype_id', $this->lang->line('room_type_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('dep_id', $this->lang->line('department_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'name'         => form_error('name'),
                'floor_id'     => form_error('floor_id'),
                'room_type_id' => form_error('roomtype_id'),
                'dep_id'       => form_error('dep_id'),
                'description'  => form_error('description'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $room = array(
                'name'         => $this->input->post('name'),
                'room_type_id' => $this->input->post('roomtype_id'),
                'floor_id'     => $this->input->post('floor_id'),
                'dep_id'       => $this->input->post('dep_id'),
                'description'  => $this->input->post('description'),
            );

            $this->Room_Model->saveroom($room);
            $msg   = $this->lang->line('room_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function getdata($id)
    {
        $data['roomtype_list'] = $this->Roomtype_Model->roomtypelist();
        $data['room_data']     = $this->Room_Model->roomlist($id);
        $data['floor_list']    = $this->floor_model->floor_list();
        $data['dept_list']     = $this->Ward_Model->getdepartment();
        $this->load->view('setup/room/editroom', $data);
    }

    public function edit($id)
    {

        $this->form_validation->set_rules('roomtype_id', $this->lang->line('room_type_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('floor_id', $this->lang->line('floor_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('department_id', $this->lang->line('department_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'name'        => form_error('name'),
                'roomtype_id' => form_error('roomtype_id'),
                'floor_id'    => form_error('floor_id'),
                'dep_id'      => form_error('department_id'),
                'description' => form_error('description'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $room = array(
                'id'           => $id,
                'name'         => $this->input->post('name'),
                'room_type_id' => $this->input->post('roomtype_id'),
                'floor_id'     => $this->input->post('floor_id'),
                'dep_id'       => $this->input->post('department_id'),
                'description'  => $this->input->post('description'),
            );

            $this->Room_Model->saveroom($room);
            $msg   = $this->lang->line('room_updated_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }
}
