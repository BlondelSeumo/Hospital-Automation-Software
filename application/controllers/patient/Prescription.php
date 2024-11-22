<?php

class Prescription extends Patient_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('Customlib');
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
    }

    public function getPrescription($visitid)
    {
       
        $result     = $this->prescription_model->getPrescriptionByVisitID($visitid);
        
        $data["print_details"] = $this->printing_model->getheaderfooter('opdpre');
        $data["result"]        = $result;
        $data["id"]            = $visitid;
        $data["opd_id"]        = $result->opd_detail_id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
            $data['fields_prescription']   =  $this->customfield_model->get_custom_fields('prescription', '', 1, '', 1); 
        } else {
            $data["print"] = 'no';
            $data['fields_prescription']   =  $this->customfield_model->get_custom_fields('prescription', '', '', '', 1); 
        }
        $this->load->view("patient/prescription", $data);
    }
 
    public function getIPDPrescription($id, $ipdid)
    {
        $result                = $this->prescription_model->getPrescriptionByTable($id,'ipd_prescription');
        $data["print_details"] = $this->printing_model->getheaderfooter('ipdpres');
        $data["result"]        = $result;
        $data["id"]            = $id;
        $data["ipdid"]         = $ipdid;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
            $data['fields_prescription']   =  $this->customfield_model->get_custom_fields('prescription', '', 1, '', 1); 
        } else {
            $data["print"] = 'no';
            $data['fields_prescription']   =  $this->customfield_model->get_custom_fields('prescription', '', '', '', 1); 
        }
        $this->load->view("patient/ipdprescription", $data);
    }

    public function downloadprescription($prescription_id)
    {
        $result                = $this->prescription_model->getPrescriptionbyprescriptionid($prescription_id);
        $data["result"]        = $result[0];
        $this->load->helper('download');
        $filepath    = "./uploads/prescription_document/" . $result[0]['attachment'];
        $report_name = $result[0]['attachment_name'];
        $data        = file_get_contents($filepath);
        force_download($report_name, $data);
    }


}
