<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Department extends Admin_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->library('datatables');
        $this->load->helper('file');
        $this->config->load("payroll");
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'hr/index');
        $this->form_validation->set_rules(
            'type', $this->lang->line('department_name'), array('required',
                array('check_exists', array($this->department_model, 'valid_department')),
            )
        );

        $data["title"] = $this->lang->line('add_department');
        if ($this->form_validation->run()) {
            $type             = $this->input->post("type");
            $departmenttypeid = $this->input->post("departmenttypeid");
            $status           = $this->input->post("status");
            if (empty($departmenttypeid)) {
                if (!$this->rbac->hasPrivilege('department', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('department', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($departmenttypeid)) {
                $data = array('department_name' => $type, 'is_active' => 'yes', 'id' => $departmenttypeid);
            } else {
                $data = array('department_name' => $type, 'is_active' => 'yes');
            }
            $insert_id = $this->department_model->addDepartmentType($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/department");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/staff/departmentType", $data);
            $this->load->view("layout/footer");
        }
    }

    public function add()
    {
        $this->form_validation->set_rules(
            'type', $this->lang->line('department_name'), array('required',
                array('check_exists', array($this->department_model, 'valid_department')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $type      = $this->input->post("type");
            $data      = array('department_name' => $type, 'is_active' => 'yes');
            $insert_id = $this->department_model->addDepartmentType($data);
            $array     = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function get()
    {
        //get product data and encode to be JSON object
        header('Content-Type: application/json');
        echo $this->department_model->getall();
    }

    public function getdepartmentdatatable()
    {
        $dt_response = $this->department_model->getAlldepartmentRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================

                $action = '';
                if ($this->rbac->hasPrivilege('department', 'can_edit')) {

                    $action = "<a href='#' data-toggle='tooltip' onclick='get(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='#editmyModal' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('department', 'can_delete')) {

                    $action .= "<a href='#' onclick='deleterecord(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                //==============================
                $row[]     = $value->department_name;
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

    public function get_data($id)
    {
        $result = $this->department_model->getDepartmentType($id);
        echo json_encode($result);
    }

    public function edit()
    {
        $this->form_validation->set_rules(
            'type', $this->lang->line('department_name'), array('required',
                array('check_exists', array($this->department_model, 'valid_department')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $departmenttypeid = $this->input->post("departmenttypeid");
            $type             = $this->input->post("type");
            $data             = array('department_name' => $type, 'is_active' => 'yes', 'id' => $departmenttypeid);
            $this->department_model->addDepartmentType($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function departmentdelete($id)
    {
        $this->department_model->deleteDepartment($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }
}
