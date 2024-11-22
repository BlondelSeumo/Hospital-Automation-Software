<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Antenatal extends Patient_Controller
{

    public function __construct()
    {
        parent::__construct();  
 
        $this->load->model('antenatal_model');
        $this->load->model('printing_model');    
        $this->load->library('Customlib');    
        $this->load->model('customfield_model');    
     
    }

    public function getantenatalprescription($visitid)
    {
        $result                = $this->antenatal_model->getprescription($visitid);
        $data["result"]        = $result;
        $data["id"]            = $visitid;
        $data["print_details"] = $this->printing_model->get('', 'opd_antenatal_finding');
        $data['fields_prescription']   =  $this->customfield_model->get_custom_fields('antenatal', '',''); 
        $data['prefix_opd_no']   =  $this->customlib->getPatientSessionPrefixByType('opd_no'); 
        $data['prefix_checkup_id']   =  $this->customlib->getPatientSessionPrefixByType('checkup_id'); 
       
        $this->load->view("patient/antenatalfinding", $data);
    }

    public function getobstetrichistory()
    {
        $id = $this->input->post('id');
        $data['result'] = $this->antenatal_model->getobstetrichistorybyid($id);
        $page = $this->load->view('patient/_viewobstetrichistory', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printobstetrichistory()
    {
        $id = $this->input->post('id');
        $data['result'] = $this->antenatal_model->getobstetrichistorybyid($id);       
        $data['print_details']   = $this->printing_model->get('', 'obstetric_history');
        $page           = $this->load->view('patient/_printobstetrichistory', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getipdantenatalfindings($antenatal_id)
    {

        $result                = $this->antenatal_model->getipdprescription($antenatal_id);
        $data["result"]        = $result;
        $data["id"]            = $antenatal_id;
        $this->load->view("patient/ipdantenatalfinding", $data);
    }


}
