<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bedtype extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/setup/bedtype');
        $this->session->set_userdata('sub_menu', 'bed');
        $data['bedtype_list'] = $this->bedtype_model->bedtype_list();
        $this->load->view('layout/header');
        $this->load->view('setup/bedtype', $data);
        $this->load->view('layout/footer');
    }

    public function add()
    {
        $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array('required',
                array('check_exists', array($this->bedtype_model, 'valid_bed_type')),
            )
        );
        if ($this->form_validation->run() == false) {

            $msg = array(
                'name' => form_error('name'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $bedtype = array('name' => $this->input->post('name'));
            $this->bedtype_model->savebed($bedtype);           
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getdata($id)
    {
        $data['bedtype_data'] = $this->bedtype_model->bedtype_list($id);
        $this->load->view('setup/bedtype/edit', $data);
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

            $bedtype = array(
                'name' => $this->input->post('name'),
                'id'   => $id,
            );

            $this->bedtype_model->savebed($bedtype);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function delete_bedgroup($id)
    {
        $this->bedtype_model->delete_bedgroup($id);
        redirect('admin/setup/bed/bedgroup');
    }

    public function delete($id)
    {
        $this->bedtype_model->delete($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

}
