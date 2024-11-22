<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Unittype extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('unittype_model');
        $this->load->library('datatables');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('unit_type', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/unittype/index');
        $this->session->set_userdata('sub_menu', 'charges/index');
        $medicinecategoryid = $this->input->post("medicinecategoryid");
        $data["title"]      = $this->lang->line('add_medicine_category');
        $this->load->view("layout/header");
        $this->load->view("admin/unittype/index", $data);
        $this->load->view("layout/footer");

    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('unit_type', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules(
            'unit', $this->lang->line('unit'), array('required',
                array('check_exists', array($this->unittype_model, 'valid_unit_type')),
            )
        );

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('unit'),
            );
            $array = array('status' => '0', 'error' => $msg, 'message' => '');
        } else {
            $insert_data = array(
                'unit' => $this->input->post("unit"),
                'id'   => $this->input->post("id"),
            );

            $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('update_message'));
            $insert_id = $this->unittype_model->add($insert_data);
        }
        echo json_encode($array);
    }

    public function getdatatable()
    {
        $dt_response = $this->unittype_model->getAllRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row    = array();
                $action = "";

                if ($this->rbac->hasPrivilege('unit_type', 'can_edit')) {
                    $action .= "<a href='#' class='btn btn-default btn-xs edit_unittype edit_unit_type_modal' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-toggle='tooltip' data-record-id='" . $value->id . "'  title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('unit_type', 'can_delete')) {
                    $action .= "<a  data-record-id='" . $value->id . "' class='btn btn-default btn-xs delect_record' data-toggle='tooltip'   title='" . $this->lang->line('delete') . "'><i class='fa fa-remove'></i></a>";
                }

                $row[] = $value->unit;
                $row[] = $action;

                //====================
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

    public function getByUnitId()
    {

        $id     = $this->input->post('id');
        $result = $this->unittype_model->get($id);
        $array  = array('status' => 1, 'result' => $result);
        echo json_encode($array);
    }

    public function delete()
    {
        if (!$this->rbac->hasPrivilege('unit_type', 'can_delete')) {
            access_denied();
        }
        $id     = $this->input->post('id');
        $result = $this->unittype_model->delete($id);
        $array  = array('status' => 1, 'result' => $result, 'message' => $this->lang->line('delete_message'));
        echo json_encode($array);
    }

}
