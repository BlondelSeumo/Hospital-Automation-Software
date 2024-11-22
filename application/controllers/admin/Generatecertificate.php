<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Generatecertificate extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->library('datatables');
        $this->load->model('certificate_model');
        $this->load->model('generatecertificate_model');
        $this->load->library('system_notification');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('generate_certificate', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatecertificate');
        $certificateList         = $this->certificate_model->getpatientcertificate();
        $data['certificateList'] = $certificateList;
        $patientlist             = $this->patient_model->getPatientListall();
        $data['patientlist']     = $patientlist;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/certificate/generatecertificate', $data);
        $this->load->view('layout/footer', $data);
    }

    public function search()
    {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $certificateList         = $this->certificate_model->getpatientcertificate();
        $data['certificateList'] = $certificateList;
        $data['module']          = $this->input->post('module');
        $data['patient_status']  = $this->input->post('patient_status');
        $data['certificate']     = $this->input->post('certificate_id');
        $button                  = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/certificate/generatecertificate', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $module         = $this->input->post('module');
            $patient_status = $this->input->post('patient_status');
            $search         = $this->input->post('search');
            $certificate    = $this->input->post('certificate_id');
            $this->load->view('layout/header', $data);
            $this->load->view('admin/certificate/generatecertificate', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    public function checkvalidation()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('module', $this->lang->line('module'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('certificate_id', $this->lang->line('certificate_template'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'module'         => form_error('module'),
                'certificate_id' => form_error('certificate_id'),

            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'module'         => $this->input->post('module'),
                'certificate_id' => $this->input->post('certificate_id'),
                'patient_status' => $this->input->post('patient_status'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function getgeneratedatatable()
    {
        $modules = $this->input->post('module');
        $status  = $this->input->post('patient_status');
        if ($modules == 'opd') {
            $dt_response = $this->patient_model->getAllOpdPatientforcertificate($status);
        }

        if ($modules == 'ipd') {
            $dt_response = $this->patient_model->getAllIpdPatientforcertificate($status);
        }

        $dt_response = json_decode($dt_response);
        $dt_data     = array();

        //====================================
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();

                $checkbox_fields = "<input type='checkbox' class='checkbox center-block'  name='check' data-patient_id='" . $value->id . "' value='" . $value->id . "'>";

                if ($value->module == 'opd') {
                    $moduleno = $this->customlib->getSessionPrefixByType('checkup_id') . $value->checkup_id;
                } elseif ($value->module == 'ipd') {
                    $moduleno = $this->customlib->getSessionPrefixByType('ipd_no') . $value->id;

                }
                //====================================
                $row[] = $checkbox_fields;
                $row[] = $moduleno;
                $row[] = $value->patient_name . " (" . $value->patient_id . ")";
                $row[] = $this->lang->line(strtolower($value->gender));
                $row[] = $value->mobileno;
                $row[] = $this->lang->line($value->discharged);
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

    public function generatemultiple()
    {
        $patienttid          = $this->input->post('data');
        $patient_array       = json_decode($patienttid);
        $certificate_id      = $this->input->post('certificate_id');
        $module_status       = $this->input->post('module_status');
        $data                = array();
        $pat_arr             = array();
        $data['sch_setting'] = $this->setting_model->get();
        $data['certificate'] = $this->generatecertificate_model->getcertificatebyid($certificate_id, $module_status);

        foreach ($patient_array as $key => $value) {
            $pat_arr[] = $value->patient_id;
        }
        if ($module_status == "opd") {
            $data['patients'] = $this->patient_model->getPatientsByArrayopd($pat_arr);
           
            foreach($data['patients'] as $opd){
                
                $event_data = array(
                    'patient_id'       => $opd->patient_id,
                    'opd_ipd_no'       => $this->lang->line($opd->module) . $opd->id,
                    'certificate_name' => $data['certificate'][0]->certificate_name,
                );            
                
                $this->system_notification->send_system_notification('patient_certificate_generate', $event_data);
                
            }
            
        } elseif ($module_status == "ipd") {
            $data['patients'] = $this->patient_model->getPatientsByArrayipd($pat_arr);
            
            foreach($data['patients'] as $ipd){
                
                $event_data = array(
                    'patient_id'       => $ipd->patient_id,
                    'opd_ipd_no'       => $this->lang->line($ipd->module) . $ipd->id,
                    'certificate_name' => $data['certificate'][0]->certificate_name,
                );
                
                $this->system_notification->send_system_notification('patient_certificate_generate', $event_data);
                
            }
            
        }       
 
        $certificates = $this->load->view('admin/certificate/printcertificate', $data, true);
        echo $certificates;
    } 

}
