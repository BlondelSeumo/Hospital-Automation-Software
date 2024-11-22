<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Generatepatientidcard extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->model(array('Generatepatientidcard_model'));
        $this->load->library('datatables');
        $this->sch_setting_detail = $this->setting_model->getSetting();

    }

    public function index()
    {

        if (!$this->rbac->hasPrivilege('generate_patient_id_card', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatepatientidcard');
        $idcardlist         = $this->Generatepatientidcard_model->getpatientidcard();
        $data['idcardlist'] = $idcardlist;
        $patients           = $this->patient_model->getPatientListall();
        $data["patients"]   = $patients;
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id_card', $this->lang->line('id_card_template'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {
            $search = $this->input->post('search');
            $data['searchby']        = "filter";
            $patient                 = $this->input->post('patient_id');
            $data['patient']         = $patient;
            $patient_id_card         = $this->input->post('patient_id_card');
            $data['patient_id_card'] = $patient_id_card;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/patientidcard/generatepatientidcard', $data);
        $this->load->view('layout/footer', $data);
    }
  
    public function checkpatientidcardvalidation()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id_card', $this->lang->line('id_card_template'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'      => form_error('patient_id'),
                'patient_id_card' => form_error('patient_id_card'),

            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'patient_id'      => $this->input->post('patient_id'),
                'patient_id_card' => $this->input->post('patient_id_card'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function getpatientlistdatatable()
    {
        $patient = $this->input->post('patient_id');
        $idcard  = $this->input->post('patient_id_card');

        $idcardResult = $this->Generatepatientidcard_model->getidcardbyid($idcard);
        if ($patient == 'all') {
            $dt_response = $this->patient_model->getpatientallforidcard();
        } else {
            $dt_response = $this->patient_model->getpatientbyidforidcard($patient);
        }

        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================

                $action = "<input type='checkbox' class='checkbox center-block' data-patient_id='" . $value->id . "'  name='check' id='check' value='" . $value->id . "'>";

                $action .= "<input type='hidden' name='patient_id' id='patient_id' value='" . $value->id . "'>
                <input type='hidden' name='id_card_id' id='id_card_id' value='" . $idcardResult[0]->id . "'>";
                //==============================
                $row[]     = $action;
                $row[]     = $value->patient_name . " (" . $value->id . ")";
                $row[]     = $value->age;
                $row[]     = $this->lang->line(strtolower($value->gender));
                $row[]     = $value->mobileno;
                $row[]     = $value->guardian_name;
                $row[]     = $value->address;
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

    public function generatemultiple()
    {
        $patientid               = $this->input->post('data');
        $patient_array           = json_decode($patientid);
        $idcard                  = $this->input->post('patient_id_card');
        $data                    = array();
        $results                 = array();
        $pat_arr                 = array();
        $data['sch_setting']     = $this->setting_model->get();
        $data['patient_id_card'] = $this->Generatepatientidcard_model->getidcardbyid($idcard);
        $scan_type               = $this->sch_setting_detail->scan_code_type;

        foreach ($patient_array as $key => $value) {
            $pat_arr[] = $value->patient_id;
        }
        $patients = $this->patient_model->getpatientsByArray($pat_arr);

        foreach ($patients as $key => $patients_value) {
             $patients[$key]->barcode = $this->customlib->generatebarcode($patients_value->id,$scan_type);
        }

        $data['patients']        = $patients;
        $id_cards = $this->load->view('admin/patientidcard/generatemultiple', $data, true);
        echo $id_cards;
    }


}
