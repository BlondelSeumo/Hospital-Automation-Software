<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Medicineunit extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('datatables');
        $this->load->model('pharmacy_model');       
    }

    public function index()
    {
		if (!$this->rbac->hasPrivilege('medicine_unit', 'can_view')) {
            access_denied();
        }
    	$this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'medicine/index');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/medicineunit');
        $data['unitname'] = $this->pharmacy_model->getpharmacyunit();
		$this->load->view("layout/header");
        $this->load->view("admin/pharmacy/medicine_unit",$data);
        $this->load->view("layout/footer");
    }

    public function addunit(){

 		$id = $this->input->post("id"); 		
 		$this->form_validation->set_rules('unit_name', $this->lang->line('unit_name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'unit_name' => form_error('unit_name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $unitname = $this->input->post("unit_name");
            $unittype = 'pharmacy';

            if (!empty($id)) { //edit unit               
                $data  = array('unit_name' => $unitname, 'unit_type' => $unittype, 'id' => $id);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else { // add unit
                $data  = array('unit_name' => $unitname, 'unit_type' => $unittype);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }
            $insert_id = $this->pharmacy_model->addunit($data);
        }
        echo json_encode($array);
    }

    public function deletepharmacyunit($id)
    {        
        $this->pharmacy_model->deletepharmacyunit($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

    public function medicine_company()
    {
		if (!$this->rbac->hasPrivilege('medicine_company', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'medicine/index');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/medicineunit/medicine_company');
        $data['company'] = $this->pharmacy_model->getcomapnyname();
        $this->load->view("layout/header");
        $this->load->view("admin/pharmacy/medicine_company",$data);
        $this->load->view("layout/footer");
    }

    public function add_company(){
        $id = $this->input->post("id");               
        $company_name=$this->form_validation->set_rules('company_name', $this->lang->line('company_name'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'company_name' => form_error('company_name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {    
            $company_name = $this->input->post("company_name");    
            if (!empty($id)) {              
                $data  = array('company_name' => "$company_name",'id' => $id);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {  
                $data  = array('company_name' => "$company_name");
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }
            $insert_id = $this->pharmacy_model->add_company($data);
        }
        echo json_encode($array);
    }


 /* this funtion is used to save multiple company */
    public function add_multiple_company() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('company_name_'.$row_value, $this->lang->line('company_name'), 'trim|required');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array(
                        'name' => form_error('company_name_'.$row_value),
                    );
                    $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');  
                    echo json_encode($json_array);
                    return false;          
                }
            } 
            //validation to check each input keeps the value or not

            // save the multiple data 
            $total_rows   = $this->input->post('total_rows');  
            foreach ($total_rows as $row_key => $row_values) {                 
                $data  = array('company_name' => $this->input->post('company_name_'.$row_values));
                $insert_id = $this->pharmacy_model->add_company($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message= $this->lang->line("please_add_at_least_one_company_name");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}

    public function deletecompany($id)
    {        
        $this->pharmacy_model->deletecompany($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

    public function medicine_group()
    {
		if (!$this->rbac->hasPrivilege('medicine_group', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'medicine/index');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/medicineunit/medicine_group');
        $data['get_medicine_group'] = $this->pharmacy_model->get_medicine_group();
        $this->load->view("layout/header");
        $this->load->view("admin/pharmacy/medicine_group",$data);
        $this->load->view("layout/footer");
    }

	public function add_medicine_group(){
        $id = $this->input->post("id");               
        $group_name=$this->form_validation->set_rules('group_name', $this->lang->line('medicine_group'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'group_name' => form_error('group_name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {    
            $group_name = $this->input->post("group_name");    
            if (!empty($id)) {              
                $data  = array('group_name' => "$group_name",'id' => $id);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {  
                $data  = array('group_name' => "$group_name");
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }
            $insert_id = $this->pharmacy_model->add_medicine_group($data);
        }
        echo json_encode($array);
    }

 /* this funtion is used to save multiple Group*/
    public function add_multiple_group() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('group_name_'.$row_value, $this->lang->line('medicine_group'), 'trim|required');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array(
                        'name' => form_error('group_name_'.$row_value),
                    );
                    $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');  
                    echo json_encode($json_array);
                    return false;          
                }
            } 
            //validation to check each input keeps the value or not

            // save the multiple data 
            $total_rows   = $this->input->post('total_rows');  
            foreach ($total_rows as $row_key => $row_values) {                 
                $data  = array('group_name' => $this->input->post('group_name_'.$row_values));
                $insert_id = $this->pharmacy_model->add_medicine_group($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message=	$this->lang->line("please_add_at_least_one_medicine_group");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}

    public function deletegroup($id)
    {        
        $this->pharmacy_model->deletegroup($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

	/* this funtion is used to save multiple pharmacy unit */
    public function add_multiple_pharmacyunit() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('name_'.$row_value, $this->lang->line('unit_name'), 'trim|required');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array(
                        'name' => form_error('name_'.$row_value),
                    );
                    $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');  
                    echo json_encode($json_array);
                    return false;          
                }
            } 
            //validation to check each input keeps the value or not

            // save the multiple data 
            $total_rows   = $this->input->post('total_rows');  
            foreach ($total_rows as $row_key => $row_values) {                  
                $unittype = 'pharmacy';
                $data  = array('unit_name' => $this->input->post('name_' . $row_values), 'unit_type' => $unittype);
                $insert_id = $this->pharmacy_model->addunit($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message=	$this->lang->line("please_add_at_least_one_unit_name");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}

}    