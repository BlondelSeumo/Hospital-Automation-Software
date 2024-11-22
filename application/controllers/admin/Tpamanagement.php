<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tpamanagement extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library("datatables");
        $this->search_type = $this->config->item('search_type');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'tpa_management');
        $data['title']      = $this->lang->line('tpa_management');
        $data['resultlist'] = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/tpamanagement/index', $data);
        $this->load->view('layout/footer');
    }

    public function gettpadatatable()
    {
        $dt_response = $this->organisation_model->getAlltpaRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action1 = "";
                $action2 = "";
                $action3 = "";
                
                if ($this->rbac->hasPrivilege('organisation', 'can_delete')) {
                    $action1 = "<a href='#' onclick=delete_recordById('admin/tpamanagement/delete/" . $value->id . "') class='btn btn-default btn-xs pull-right'  data-toggle='tooltip' title='' data-target='#editmyModal'  data-original-title=" . $this->lang->line('delete') . "><i class='fa fa-trash'></i></a>";
                }
                
                if ($this->rbac->hasPrivilege('organisation', 'can_view')) {
                    $action3 = "<a href=" . base_url() . 'admin/tpa/master/' . $value->id . " class='btn btn-default btn-xs pull-right' data-toggle='tooltip' title=''  data-original-title=" . $this->lang->line('organization_profile') . "><i class='fa fa-reorder' aria-hidden='true'></i></a>";
                }

                if ($this->rbac->hasPrivilege('organisation', 'can_edit')) {
                    $action2 = "<a href='#' onclick=get_orgdata('" . $value->id . "') class='btn btn-default btn-xs pull-right' data-toggle='tooltip' title='' data-target='#editmyModal'  data-original-title=" . $this->lang->line('edit') . "><i class='fa fa-pencil'></i></a>";
                }

                
                
                //==============================
                $row[]     = $value->organisation_name ."<div class='rowoptionview rowview-mt-19'>". $action1 . $action2 . $action3 ."</div>";
                $row[]     = $value->code;
                $row[]     = $value->contact_no;
                $row[]     = $value->address;
                $row[]     = $value->contact_person_name;
                $row[]     = $value->contact_person_phone;
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

    public function add_oragnisation()
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'required');
        $this->form_validation->set_rules('contact_number', $this->lang->line('contact_no'), 'required');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'name'           => form_error('name'),
                'code'           => form_error('code'),
                'contact_number' => form_error('contact_number'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $organigation = array(
                'organisation_name'    => $this->input->post('name'),
                'code'                 => $this->input->post('code'),
                'contact_no'           => $this->input->post('contact_number'),
                'address'              => $this->input->post('address'),
                'contact_person_name'  => $this->input->post('contact_person_name'),
                'contact_person_phone' => $this->input->post('contact_person_phone'),
            );
            $this->organisation_model->add($organigation);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function get_data($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_view')) {
            access_denied();
        }
        $org   = $this->organisation_model->get($id);
        $array = array(
            'id'                     => $org['id'],
            'ename'                  => $org['organisation_name'],
            'ecode'                  => $org['code'],
            'econtact_number'        => $org['contact_no'],
            'eaddress'               => $org['address'],
            'econtact_persion_name'  => $org['contact_person_name'],
            'econtact_persion_phone' => $org['contact_person_phone'],
        );
        echo json_encode($array);
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('ename', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('ecode', $this->lang->line('code'), 'required');
        $this->form_validation->set_rules('econtact_number', $this->lang->line('contact_number'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1' => form_error('ename'),
                'e2' => form_error('ecode'),
                'e3' => form_error('econtact_number'),
                'e4' => form_error('eaddress'),
                'e5' => form_error('econtact_persion_name'),
                'e6' => form_error('econtact_persion_phone'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $organigation = array(
                'id'                   => $this->input->post('org_id'),
                'organisation_name'    => $this->input->post('ename'),
                'code'                 => $this->input->post('ecode'),
                'contact_no'           => $this->input->post('econtact_number'),
                'address'              => $this->input->post('eaddress'),
                'contact_person_name'  => $this->input->post('econtact_persion_name'),
                'contact_person_phone' => $this->input->post('econtact_persion_phone'),
            );
            $this->organisation_model->add($organigation);
            $array = array('status' => 'suucess', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('organisation', 'can_delete')) {
            access_denied();
        }
        $this->organisation_model->delete($id);
        $json_array = json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
        echo $json_array;
    }

    public function checkvalidation()
    {

        $param = array(
            'search_type'     => $this->input->post('search_type'),
            'organisation'    => $this->input->post('organisation'),
            'constant_id'     => $this->input->post('constant_id'),
            'date_from'       => $this->input->post('date_from'),
            'date_to'         => $this->input->post('date_to'),
            'case_id'         => $this->input->post('case_id'),
            'charge_category' => $this->input->post('charge_category'),
            'charge_id'       => $this->input->post('charge_id'),
        );

        $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        echo json_encode($json_array);
    }
    public function tpareport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/tpa');
        $this->session->set_userdata('subsub_menu', 'reports/tpamanagement/tpareport');

        $doctorlist                  = $this->staff_model->getEmployeeByRoleID(3);
        $data['doctorlist']          = $doctorlist;
        $data['organisation']        = $this->organisation_model->get();
        $data["searchlist"]          = $this->search_type;
        $data['opd_charge_category'] = $this->charge_category_model->getCategoryByModule("opd");
        $data['ipd_charge_category'] = $this->charge_category_model->getCategoryByModule("ipd");
        $data['charge_category']     = array_merge($data['opd_charge_category'], $data['ipd_charge_category']);

        $this->load->view('layout/header', $data);
        $this->load->view('admin/tpamanagement/tpareport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function tpareports()
    {
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $start_date            = '';
        $end_date              = '';

        if ($search['search_type'] == 'period') {
            $start_date = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);
        } else {
            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
                $start_date          = $dates['from_date'];
                $end_date            = $dates['to_date'];
            }
        }

        $search_array['start_date']      = $start_date;
        $search_array['end_date']        = $end_date;
        $search_array['constant_id']     = $this->input->post('constant_id');
        $search_array['organisation']    = $this->input->post('organisation');
        $search_array['case_id']         = $this->input->post('case_id');
        $search_array['charge_category'] = $this->input->post('charge_category');
        $search_array['charge_id']       = $this->input->post('charge_id');

        $reportdata = $this->report_model->tpareportsRecords($search_array);
        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $tax        = "(" . $value->tax . "%)";
                $tax_amount = amountFormat(($value->apply_charge * $value->tax) / 100);

                $row       = array();
                $row[]     = $this->customlib->getSessionPrefixByType($value->prefixno) . $value->id;
                $row[]     = $value->case_reference_id;
                $row[]     = strtoupper($value->reference);
                $row[]     = $value->organisation_name;
                $row[]     = composePatientName($value->patient_name, $value->patient_id);
                $row[]     = $this->customlib->YYYYMMDDHisTodateFormat($value->date);
                $row[]     = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[]     = $value->charge_name;
                $row[]     = $value->charge_category_name;
                $row[]     = $value->charge_type;
                $row[]     = $value->standard_charge;
                $row[]     = $value->apply_charge;
                $row[]     = $value->tpa_charge;
                $row[]     = $tax . ' ' . $tax_amount;
                $row[]     = $value->amount;
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

}
