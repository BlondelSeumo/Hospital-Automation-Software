<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Specialist extends Admin_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->library('datatables');
        $this->load->helper('file');
        $this->config->load("payroll");
        $this->load->model('specialist_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('specialist', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'hr/index');
        $this->form_validation->set_rules(
            'type', $this->lang->line('specialist_name'), array('required',
                array('check_exists', array($this->specialist_model, 'valid_specialist')),
            )
        );

        $data["title"] = $this->lang->line('add_specialist');
        if ($this->form_validation->run()) {
            $type             = $this->input->post("type");
            $specialisttypeid = $this->input->post("specialisttypeid");
            $status           = $this->input->post("status");
            if (empty($specialisttypeid)) {
                if (!$this->rbac->hasPrivilege('specialist', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('specialist', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($specialisttypeid)) {
                $data = array('specialist_name' => $type, 'is_active' => 'yes', 'id' => $specialisttypeid);
            } else {
                $data = array('specialist_name' => $type, 'is_active' => 'yes');
            }
            $insert_id = $this->specialist_model->addspecialistType($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect("admin/specialist");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/staff/specialistType", $data);
            $this->load->view("layout/footer");
        }
    }

    public function add()
    {
        $this->form_validation->set_rules(
            'type', $this->lang->line('specialist_name'), array('required',
                array('check_exists', array($this->specialist_model, 'valid_specialist')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $type      = $this->input->post("type");
            $data      = array('specialist_name' => $type, 'is_active' => 'yes');
            $insert_id = $this->specialist_model->addspecialistType($data);
            $array     = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function get()
    {
        //get product data and encode to be JSON object
        header('Content-Type: application/json');
        echo $this->specialist_model->getall();
    }

    public function getspecialistdatatable()
    {
        $dt_response = $this->specialist_model->getAllspecialistRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row    = array();
                $action = '';
                //====================================
                if ($this->rbac->hasPrivilege('specialist', 'can_edit')) {

                    $action = "<a href='#' data-toggle='tooltip' onclick='get(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='#editmyModal' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('specialist', 'can_delete')) {

                    $action .= "<a href='#' onclick='deleterecord(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }
                //==============================
                $row[]     = $value->specialist_name;
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
        $result = $this->specialist_model->getspecialistType($id);
        echo json_encode($result);
    }

    public function edit()
    {
        $this->form_validation->set_rules(
            'type', $this->lang->line('specialist_name'), array('required',
                array('check_exists', array($this->specialist_model, 'valid_specialist')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $specialisttypeid = $this->input->post("specialisttypeid");
            $type             = $this->input->post("type");
            $data             = array('specialist_name' => $type, 'is_active' => 'yes', 'id' => $specialisttypeid);
            $this->specialist_model->addspecialistType($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function specialistdelete($id)
    {
        if (!$this->rbac->hasPrivilege('specialist', 'can_delete')) {
            access_denied();
        }

        $this->specialist_model->deletespecialist($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }
}
