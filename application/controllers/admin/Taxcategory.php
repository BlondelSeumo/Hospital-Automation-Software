<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Taxcategory extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('taxcategory_model');
        $this->load->library('datatables');
    }
    public function index()
    {
        if (!$this->rbac->hasPrivilege('tax_category', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/taxcategory/index');
        $this->session->set_userdata('sub_menu', 'charges/index');
        $this->config->load("payroll");
        $this->load->view("layout/header");
        $this->load->view("admin/charges/taxCategory");
        $this->load->view("layout/footer");
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('tax_category', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array('required',
                array('check_exists', array($this->taxcategory_model, 'valid_tax_category')),
            )
        );
        $this->form_validation->set_rules('percentage', $this->lang->line('percentage'), 'trim|required|valid_tax|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'       => form_error('name'),
                'percentage' => form_error('percentage'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            ///=========================
            $insert_data = array(
                'id'         => $this->input->post('id'),
                'name'       => $this->input->post('name'),
                'percentage' => $this->input->post('percentage'),
            );

            $this->taxcategory_model->add($insert_data);
            //==================
            $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function getDatatable()
    {
        $dt_response = $this->taxcategory_model->getDatatableAllRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $tax_key => $tax_value) {

                $row    = array();
                $action = "<div class='rowoptionview rowview-mt-19'>";
                if ($this->rbac->hasPrivilege('tax_category', 'can_edit')) {
                    $action .= "<a  href='javascript:void(0)' class='btn btn-default btn-xs edit_record' data-loading-text='" . $this->lang->line('please_wait') . "' data-toggle='tooltip' data-record-id=" . $tax_value->id . "  title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('tax_category', 'can_delete')) {
                    $action .= "<a class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='delete_recordById(\"admin/taxcategory/delete/" . $tax_value->id . "\", \"" . $this->lang->line('delete_message') . "\")' data-original-title='" . $this->lang->line('delete') . "'> <i class='fa fa-trash'></i></a>";
                }
                $action .= "</div>";

                $row[]     = $tax_value->name . $action;
                $row[]     = $tax_value->percentage;
                $row[]     = '';
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

    public function getDetails()
    {
        $id = $this->input->post('tax_id');
        echo json_encode($this->taxcategory_model->get($id));
    }

    public function delete($id)
    {
        $this->taxcategory_model->delete_taxcategory($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }
}
