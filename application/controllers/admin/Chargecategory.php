<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Chargecategory extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('datatables');
        $this->charge_type = $this->customlib->getChargeMaster();
    }

    public function charges()
    {
        if (!$this->rbac->hasPrivilege('charge_category', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/chargecategory/charges');
        $this->session->set_userdata('sub_menu', 'charges/index');
        $chargecategoryid    = $this->input->post("chargecategoryid");
        $data['charge_type'] = $this->chargetype_model->get();
        $this->load->view("layout/header");
        $this->load->view("admin/charges/chargeCategory", $data);
        $this->load->view("layout/footer");
    }

    public function getDatatable()
    {
        $dt_response = $this->charge_category_model->getDatatableAllRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $charge_key => $charge_value) {
                $row = array();

                $action = "<div class='rowoptionview mt-mius0'>";
                if ($charge_value->is_default != "yes") {
                    if ($this->rbac->hasPrivilege('charge_category', 'can_edit')) {

                        $action .= "<a  href='javascript:void(0)' class='btn btn-default btn-xs edit_record edit_charge_modal' data-loading-text='" . $this->lang->line('please_wait') . "' data-toggle='tooltip' data-record-id=" . $charge_value->id . " title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                    }
                    if ($this->rbac->hasPrivilege('charge_category', 'can_delete')) {
                        $action .= "<a class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='delete_recordById(\"admin/chargecategory/delete/" . $charge_value->id . "\", \"" . $this->lang->line('delete_message') . "\")' data-original-title='" . $this->lang->line('delete') . "'> <i class='fa fa-trash'></i></a>";
                    }
                }
                $action .= "</div>";
                $row[]     = $charge_value->name;
                $row[]     = $charge_value->charge_type;
                $row[]     = $charge_value->description;
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
        if (!$this->rbac->hasPrivilege('charge_category', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array('required',
                array('check_exists', array($this->charge_category_model, 'valid_charge_category')),
            )
        );
        $this->form_validation->set_rules('description', $this->lang->line('description'), 'required');
        $this->form_validation->set_rules('charge_type', $this->lang->line('charge_type'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'        => form_error('name'),
                'description' => form_error('description'),
                'charge_type' => form_error('charge_type'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $name           = $this->input->post("name");
            $description    = $this->input->post("description");
            $charge_type_id = $this->input->post("charge_type");
            $id             = $this->input->post("id");

            $data = array(
                'name'           => $name,
                'description'    => $description,
                'charge_type_id' => $charge_type_id,
                'id'             => $id,
            );

            $this->charge_category_model->addChargeCategory($data);
            if ($id > 0) {
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }
        }
        echo json_encode($array);
    }

    public function get()
    {
        if (!$this->rbac->hasPrivilege('charge_category', 'can_view')) {
            access_denied();
        }
        header('Content-Type: application/json');
        echo $this->charge_category_model->getall();
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('charge_category', 'can_delete')) {
            access_denied();
        }
        $this->charge_category_model->delete($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

    public function get_data()
    {
        $id         = $this->input->post('id');
        $result     = $this->charge_category_model->getChargeCategory($id);
        $json_array = array('status' => '1', 'error' => '', 'result' => $result);
        echo json_encode($json_array);
    }
}
