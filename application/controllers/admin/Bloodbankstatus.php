<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bloodbankstatus extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->config("payroll");
        $this->payment_mode = $this->config->item('payment_mode');
        $this->load->model("unittype_model");
        $this->load->helper('customfield_helper');
    }    
    
    public function getBloodBankChargeById()
    {
        $id     = $this->input->post('charge_id');      
        $patient_id=$this->input->post('patient_id');
        $is_tpa_valid=true;
        $display_tpa_charge=false;
        if($this->input->post('is_tpa') == 1 && $patient_id != ""){          
            $result = $this->bloodissue_model->bloodBankChargeById($id, $patient_id);            
            if($result && !IsNullOrEmptyString($result['insurance_validity'])){
                if(strtotime($result['insurance_validity']) < strtotime(date('Y-m-d'))){                 
                      $is_tpa_valid=false;  
                }else{                   
                     $display_tpa_charge=true;                     
                }              
            }
        }else{
            $result = $this->bloodissue_model->bloodBankChargeById($id);
        }

        if(!$result){
			
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('no_charge_has_configured_for_selected_category')));		
			
        }elseif($result && $is_tpa_valid==false){          
			
			echo json_encode(array('status' => 2, 'display_tpa_charge'=>$display_tpa_charge, 'result' => $result,'msg'=> $this->lang->line('your_tpa_validity_has_expired_on').' '.$this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']).', '. $this->lang->line('so_standard_charges_has_been_applied') ));			
			
        }else{
            echo json_encode(array('status' => 1,'display_tpa_charge'=>$display_tpa_charge, 'result' => $result));
        }
    }

    public function index()
    {       
        $this->session->set_userdata('top_menu', 'blood_bank');
        $data['title'] = 'Blood Bank';
        $bloodstack    = array();
        $bloodGroup    = $this->bloodbankstatus_model->getBloodGroup(null, 1);
        foreach ($bloodGroup as $key => $value) {
            $value['total']           = $this->bloodbankstatus_model->getBloodbankStatusByid($value['id']);
            $bloodstack[$value['id']] = $value;
        }
        $data["payment_mode"] = $this->payment_mode;
        $data["bloodgroup"]   = $this->bloodbankstatus_model->get_product('', 1);
        $data["charge_type"]  = $this->chargetype_model->get();
        $data["blood_donor"]  = $this->blooddonor_model->getBloodDonor();
        $data["components"]   = $this->bloodbankstatus_model->get_product(null, 2);
        $data["patients"]     = $this->patient_model->getPatientListall();
        $data['unit_type']    = $this->unittype_model->get();
        $data["bloodGroup"]   = $bloodstack;

        $this->load->view("layout/header");
        $this->load->view("admin/bloodbank/bloodbankstatus", $data);
        $this->load->view("layout/footer");
    } 

    public function status()
    {
        if (!$this->rbac->hasPrivilege('blood_bank_product', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'blood_bank');
        $this->session->set_userdata('sub_menu', 'admin/bloodbankstatus/status');
        $bloodgroupid       = $this->input->post("bloodgroupid");
        $bloodGroup         = $this->bloodbankstatus_model->getBloodGroup();
        $data["bloodGroup"] = $bloodGroup;
        $this->form_validation->set_rules('blood_group', $this->lang->line('blood_group'), 'required');
        $this->form_validation->set_rules('status', $this->lang->line('status'), 'required');
        $data["title"] = "Edit Blood Bank Status";
        if ($this->form_validation->run()) {
            $bloodGroup   = $this->input->post("blood_group");
            $bloodgroupid = $this->input->post("id");
            $status       = $this->input->post("status");
            if (empty($bloodgroupid)) {
                if (!$this->rbac->hasPrivilege('blood_bank_product', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('blood_bank_product', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($bloodgroupid)) {
                $data = array('blood_group' => $bloodGroup, 'status' => $status, 'id' => $bloodgroupid);
            } else {
                $data = array('blood_group' => $bloodGroup, 'status' => $status);
            }
            $insert_id  = $this->bloodbankstatus_model->addBloodGroup($data);
            $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        } else {
            $msg = array('blood_group' => form_error('blood_group'),
                'status'                   => form_error('status'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        }
        echo json_encode($json_array);
    }

    public function get()
    {
        header('Content-Type: application/json');
        echo $this->bloodbankstatus_model->getall();
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('blood_bank_product', 'can_edit')) {
            access_denied();
        }
        $result = $this->bloodbankstatus_model->getBloodGroup($id);
        echo json_encode($result);
    }

    public function getAvailabelBloodByGroup()
    {
        $bloodgroup     = $this->input->post('bloodgroup');
        $data['result'] = $this->blood_donorcycle_model->getBatchByBloodGroup($bloodgroup);
        $page           = $this->load->view('admin/bloodbank/_getAvailabelBloodByGroup', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getBloodListTable($id)
    {
        $data["blood_group_id"] = $id;
        $data["blood_data"]     = $this->blood_donorcycle_model->getBloodGroupData($id);
        $data["component_data"] = $this->blood_donorcycle_model->getBloodComponentData($id);
        $this->load->view("admin/bloodbank/_bloodListTable", $data);
    }

}