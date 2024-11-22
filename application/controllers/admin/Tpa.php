<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tpa extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library("datatables");
        $this->charge_type = $this->customlib->getChargeMaster();
    }

    public function master($id)
    {
        if (!$this->rbac->hasPrivilege('tpa_charges', 'can_view')) {
            access_denied();
        }
        $data["charge_type"] = $this->setting_model->getChargeMaster();;
        $data['result'] = $this->organisation_model->get($id);
        $data['title']  = $this->lang->line('tpa_master');
        $this->load->view('layout/header');
        $this->load->view('admin/tpamanagement/tpamasters', $data);
        $this->load->view('layout/footer');
    }

    public function checkvalidation()
    {
        $param = array(
            'charge_type'           => $this->input->post('charge_type'),
            'charge_type_master_id' => $this->input->post('charge_type_master_id'),
        );

        $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        echo json_encode($json_array);
    }

    public function tpadetails()
    {
        $charge_type_master_id = $this->input->post('charge_type_master_id');
        $charge_type_id        = $this->input->post('charge_type');
        $reportdata            = $this->tpa_model->org_chargedatatable($charge_type_master_id, $charge_type_id);

        $reportdata = json_decode($reportdata);

        $dt_data = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $action = "<div class='rowoptionview rowview-btn-top'>";
                if ($this->rbac->hasPrivilege('tpa_charges', 'can_edit')) {
                    $action .= '<a class="btn btn-default btn-xs"  data-toggle="tooltip" title="' . $this->lang->line('edit') . '" onclick="get_org_charge(' . $value->id . ')" ><i class="fa fa-pencil"></i></a>';
                }
                if ($this->rbac->hasPrivilege('tpa_charges', 'can_delete')) {
                    $action .= '<a class="btn btn-default btn-xs"  onclick="delete_orgById(' . $value->id . ')" data-toggle="tooltip" title="' . $this->lang->line('delete') . '" ><i class="fa fa-trash"></i></a>';
                }
                $action .= "</div>";
                $row       = array();
                $row[]     = $value->charge_type . $action;
                $row[]     = $value->charge_category;
                $row[]     = $value->charge_name;
                $row[]     = $value->description;
                $row[]     = $value->standard_charge;
                $row[]     = $value->org_charge;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function add($id)
    {
        if (!$this->rbac->hasPrivilege('charges', 'can_add')) {
            access_denied();
        }
        $check_value = 0;

        $Charge_type = $this->input->post('charge_type');
        if (isset($_POST['other_charge'])) {
            foreach ($_POST['other_charge'] as $key => $value) {
                $check_value = 1;
                if (empty($_POST['org_othcharge_' . $value])) {
                    $msg['e' . $value] = "The Organisation Charge Field  " . $value . " Required";
                    $array             = array('status' => 'fail', 'error' => $msg, 'message' => '');
                } else {
                    $charge        = $value;
                    $org_othcharge = $_POST['org_othcharge_' . $value];
                    $data          = array('org_id' => $id, 'charge_type' => $Charge_type, 'charge_id' => $charge, 'org_charge' => $org_othcharge);
                    $data_array[]  = $data;
                    $array         = array('status' => 'success', 'error' => '', 'message' => 'Successfully Inserted');
                }
            }
        }

        if ($check_value == "0") {
            $msg['eerror'] = $this->lang->line('the_charges_field_required');
            $array         = array('status' => 'fail', 'error' => $msg, 'message' => '');
        }

        if ($array['status'] == "success") {
            $this->tpa_model->add($data_array);
        }

        echo json_encode($array);
    }

    public function get_org_charge($id)
    {
        $res = $this->tpa_model->get_org_charge($id);
        echo json_encode($res);
    }

    public function edit_org()
    {
        $this->form_validation->set_rules('org_charge', $this->lang->line('tpa_charge'), 'required|xss_clean|valid_amount|greater_than[0]');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'charge' => form_error('org_charge'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id                   = $this->input->post('org_charge_id');
            $charge['org_charge'] = $this->input->post('org_charge');
            $this->tpa_model->edit_org($id, $charge);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('tpa_charge_successfully_updated'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        $this->tpa_model->delete($id);
        echo json_encode(array('msg' => $this->lang->line('delete_message')));
    }
}
