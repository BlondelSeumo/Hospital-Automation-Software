<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Leavetypes extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->config->load("payroll");
        $this->load->library('datatables');
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'hr/index');
        $data["title"]     = $this->lang->line('add_leave_type');
        $LeaveTypes        = $this->leavetypes_model->getLeaveType();
        $data["leavetype"] = $LeaveTypes;
        $this->load->view("layout/header");
        $this->load->view("admin/staff/leavetypes", $data);
        $this->load->view("layout/footer");
    }

    public function createLeaveType()
    {
        $this->form_validation->set_rules(
            'type', $this->lang->line('name'), array('required',
                array('check_exists', array($this->leavetypes_model, 'valid_leave_type')),
            )
        );
        $data["title"] = $this->lang->line('add_leave_type');
        if ($this->form_validation->run()) {

            $type        = $this->input->post("type");
            $leavetypeid = $this->input->post("leavetypeid");
            $status      = $this->input->post("status");
            if (empty($leavetypeid)) {
                if (!$this->rbac->hasPrivilege('leave_types', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('leave_types', 'can_edit')) {
                    access_denied();
                }
            }

            if (!empty($leavetypeid)) {
                $data = array('type' => $type, 'is_active' => 'yes', 'id' => $leavetypeid);
            } else {
                $data = array('type' => $type, 'is_active' => 'yes');
            }

            $insert_id = $this->leavetypes_model->addLeaveType($data);
            $array     = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        } else {
            $msg = array(
                'e1' => form_error('type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        }
        echo json_encode($array);
    }

    public function getleavetypesdatatable()
    {
        $dt_response = $this->leavetypes_model->getAllleavetypesRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                $action ="";
                //====================================
                if ($this->rbac->hasPrivilege('leave_types', 'can_edit')) {

                    $action .= "<a href='#' data-toggle='tooltip' onclick='get(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='#editmyModal' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('leave_types', 'can_delete')) {

                     $action .= "<a href='#' onclick='deleterecord(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }
                //==============================
                $row[] = $value->type;
                $row[] = $action;
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
    public function leaveedit()
    {
        $this->form_validation->set_rules(
            'type', $this->lang->line('leave_type'), array('required',
                array('check_exists', array($this->leavetypes_model, 'valid_leave_type')),
            )
        );
        $data["title"] = $this->lang->line('add_leave_type');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1' => form_error('type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $type        = $this->input->post("type");
            $leavetypeid = $this->input->post("leavetypeid");
            $status      = $this->input->post("status");
            if (empty($leavetypeid)) {
                if (!$this->rbac->hasPrivilege('leave_types', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('leave_types', 'can_edit')) {
                    access_denied();
                }
            }

            if (!empty($leavetypeid)) {
                $data = array('type' => $type, 'is_active' => 'yes', 'id' => $leavetypeid);
            } else {
                $data = array('type' => $type, 'is_active' => 'yes');
            }

            $insert_id = $this->leavetypes_model->addLeaveType($data);
            $array     = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }

        echo json_encode($array);
    }

    public function get_type($id)
    {
        $result = $this->staff_model->getLeaveType($id);
        echo json_encode($result);
    }

    public function leavedelete($id)
    {
        $this->leavetypes_model->deleteLeaveType($id);
        echo json_encode(array("status" => 1, "msg" => $this->lang->line("delete_message")));
    }

}
