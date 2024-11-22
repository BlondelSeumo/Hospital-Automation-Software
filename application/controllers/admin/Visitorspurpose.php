<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Visitorspurpose extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('setup_front_office', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'admin/visitorspurpose');
        $this->form_validation->set_rules('visitors_purpose', $this->lang->line('visitors_purpose'), 'required');
        if ($this->form_validation->run() == false) {
            $data['visitors_purpose_list'] = $this->visitors_purpose_model->visitors_purpose_list();
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/visitorspurposeview', $data);
            $this->load->view('layout/footer');
        } else {
            $visitors_purpose = array(
                'visitors_purpose' => $this->input->post('visitors_purpose'),
                'description'      => $this->input->post('description'),
            );
            $this->visitors_purpose_model->add($visitors_purpose);
            $this->session->set_flashdata('msg', '<div class="alert alert-success"> ' . $this->lang->line('visitors_purpose_added_successfully') . '</div>');
            redirect('admin/visitorspurpose');
        }
    }

    /* this funtion is used to save multiple purpose */
    public function add_multiple_purpose() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('visitors_purpose_'.$row_value, $this->lang->line('purpose'), 'trim|required');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array('visitors_purpose' => form_error('visitors_purpose_'.$row_value));
                    $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');  
                    echo json_encode($json_array);
                    return false;          
                }
            } 
            //validation to check each input keeps the value or not

            // save the multiple data 
            $total_rows   = $this->input->post('total_rows');  
            foreach ($total_rows as $row_key => $row_values) {                 
                $data  = array(
                    'visitors_purpose' => $this->input->post('visitors_purpose_'.$row_values),
                    'description' => $this->input->post('description_'.$row_values));
                $this->visitors_purpose_model->add($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message= $this->lang->line("please_add_at_least_one_purpose");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}


    public function edit()
    {
        $this->form_validation->set_rules('visitors_purpose', $this->lang->line('purpose'), 'required');
        $id = $this->input->post('id');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'name' => form_error('visitors_purpose'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $visitors_purpose = array(
                'visitors_purpose' => $this->input->post('visitors_purpose'),
                'description'      => $this->input->post('description'),
            );

            $this->visitors_purpose_model->update($id, $visitors_purpose);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function edit1($visitors_purpose_id)
    {
        if (!$this->rbac->hasPrivilege('setup_front_office', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('visitors_purpose', $this->lang->line('visitors_purpose'), 'required');

        if ($this->form_validation->run() == false) {
            $data['visitors_purpose_list'] = $this->visitors_purpose_model->visitors_purpose_list();
            $data['visitors_purpose_data'] = $this->visitors_purpose_model->visitors_purpose_list($visitors_purpose_id);
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/visitorspurposeeditview', $data);
            $this->load->view('layout/footer');
        } else {
            $visitors_purpose = array(
                'visitors_purpose' => $this->input->post('visitors_purpose'),
                'description'      => $this->input->post('description'),
            );
            $this->visitors_purpose_model->update($visitors_purpose_id, $visitors_purpose);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('visitors_purpose_updated_successfully') . '</div>');
            redirect('admin/visitorspurpose');
        }
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('setup_front_office', 'can_delete')) {
            access_denied();
        }
        $this->visitors_purpose_model->delete($id);
        echo json_encode(array("status" => 1,"msg" => $this->lang->line('delete_message')));
    }

    public function get_data($id)
    {
        $result = $this->visitors_purpose_model->visitors_purpose_list($id);
        echo json_encode($result);
    }

}
