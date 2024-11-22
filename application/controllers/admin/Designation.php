<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Designation extends Admin_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->helper('file');
        $this->config->load("payroll");
        $this->load->library('datatables');
    }

    public function designation()
    {

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'hr/index');
        $designation         = $this->designation_model->get();
        $data["title"]       = $this->lang->line('add_designation');
        $data["designation"] = $designation;
        $this->form_validation->set_rules(
            'type', $this->lang->line('designation_name'), array('required',
                array('check_exists', array($this->designation_model, 'valid_designation')),
            )
        );
        if ($this->form_validation->run()) {
            $type          = $this->input->post("type");
            $designationid = $this->input->post("designationid");
            $status        = $this->input->post("status");
            if (empty($designationid)) {
                if (!$this->rbac->hasPrivilege('designation', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('designation', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($designationid)) {
                $data = array('designation' => $type, 'is_active' => 'yes', 'id' => $designationid);
            } else {

                $data = array('designation' => $type, 'is_active' => 'yes');
            }
            $insert_id = $this->designation_model->addDesignation($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/designation/designation");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/staff/designation", $data);
            $this->load->view("layout/footer");
        }
    }

    public function getdesignationdatatable()
    {
        $dt_response = $this->designation_model->getAlldesignationRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row    = array();
                $action = '';
                //====================================
                if ($this->rbac->hasPrivilege('designation', 'can_edit')) {

                    $action = "<a href='#' data-toggle='tooltip' onclick='get(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='#editmyModal' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('designation', 'can_delete')) {

                    $action .= "<a href='#' onclick='deleterecord(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }
                //==============================
                $row[]     = $value->designation;
                $row[]     = $action;
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function add()
    {
        $this->form_validation->set_rules(
            'type', $this->lang->line('name'), array('required',
                array('check_exists', array($this->designation_model, 'valid_designation')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $type      = $this->input->post("type");
            $data      = array('designation' => $type, 'is_active' => 'yes');
            $insert_id = $this->designation_model->addDesignation($data);
            $array     = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function edit()
    {
        $this->form_validation->set_rules(
            'type', $this->lang->line('name'), array('required',
                array('check_exists', array($this->designation_model, 'valid_designation')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('type'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id   = $this->input->post('designationid');
            $type = $this->input->post("type");
            $data = array('designation' => $type, 'is_active' => 'yes', 'id' => $id);
            $this->designation_model->addDesignation($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function designationdelete($id)
    {
        $this->designation_model->deleteDesignation($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

    public function get_data($id)
    {
        $result = $this->designation_model->get($id);
        echo json_encode($result);
    }

}
