<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Referralcategory extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("referral_category_model");
        $this->load->library("form_validation");
    }    
    
    /* this funtion is used to save multiple referrel category */
    public function add_multiple_category() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('name_'.$row_value, $this->lang->line('name_'), 'trim|required');        
                
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
                $data  = array('name' => $this->input->post('name_'.$row_values));
            $this->referral_category_model->add($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message= $this->lang->line("please_add_at_least_one_name");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('referral_category', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->referral_category_model->delete($id);
            echo json_encode(array("status" => 1, "msg" => $this->lang->line("delete_message")));
        } else {
            redirect("admin/referral/category");
        }
    }

    public function get($id)
    {
        $data = $this->referral_category_model->get($id);
        echo json_encode($data);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('referral_category', 'can_edit')) {
            access_denied();
        }
        $data = array();
        $this->form_validation->set_rules("name", $this->lang->line('name'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg  = array("name" => form_error('name'));
            $data = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $category = array(
                "id"   => $this->input->post('categoryid'),
                "name" => $this->input->post('name'),
            );

            $this->referral_category_model->update($category);
            $data = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($data);

    }

}
