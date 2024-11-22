<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Users extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('datatables');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('users', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'users/index');
        $staffList         = $this->staff_model->getAll();
        $data['staffList'] = $staffList;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/users/userList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function changeStatus()
    {
        $id     = $this->input->post('id');
        $status = $this->input->post('status');
        $role   = $this->input->post('role');
        $data   = array('id' => $id, 'is_active' => $status);
        if ($role != "staff") {
            $result = $this->user_model->changeStatus($data);
        } else {
            if ($status == "yes") {
                $data['is_active'] = 1;
            } else {
                $data['is_active'] = 0;
            }
            $result = $this->staff_model->update($data);
        }

        if ($result) {
            $response = array('status' => 1, 'msg' => $this->lang->line('success_message'));
            echo json_encode($response);
        }
    }

    public function getUsersDatatable()
    {
        $dt_response = $this->patient_model->getAllPatientList();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $checkbox_status = $value->user_tbl_active == "yes" ? "checked='checked'" : "";
                $action          = '<div class="material-switch" ><input id="patient' . $value->user_tbl_id . '" name="someSwitchOption" type="checkbox" data-role="patient" class="chk" data-rowid="' . $value->user_tbl_id . '" value="checked" ' . $checkbox_status . ' /><label for="patient' . $value->user_tbl_id . '" class="label-success"></label></div>';

                //==============================
                $row[]     = $value->id;
                $row[]     = $value->patient_name;
                $row[]     = $value->username;
                $row[]     = $value->mobileno;
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

    public function getStaffDatatable()
    {
        $dt_response = $this->patient_model->getAllPatientList();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $checkbox_status = $value->user_tbl_active == "yes" ? "checked='checked'" : "";
                $action          = '<div class="material-switch"><input id="patient' . $value->user_tbl_id . '" name="someSwitchOption001" type="checkbox" data-role="patient" class="chk" data-rowid="' . $value->user_tbl_id . '" value="checked" ' . $checkbox_status . ' /><label for="patient"' . $value->user_tbl_id . '" class="label-success"></label></div>';

                //==============================
                $row[]     = $value->id;
                $row[]     = $value->patient_name;
                $row[]     = $value->username;
                $row[]     = $value->mobileno;
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

}
