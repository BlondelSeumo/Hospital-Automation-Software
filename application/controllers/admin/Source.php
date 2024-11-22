<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Source extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->form_validation->set_rules('source', $this->lang->line('source'), 'required');
        if ($this->form_validation->run() == false) {
            $data['source_list'] = $this->source_model->source_list();
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/sourceview', $data);
            $this->load->view('layout/footer');
        } else {
            $source = array(
                'source'      => $this->input->post('source'),
                'description' => $this->input->post('description'),
            );
            $this->source_model->add($source);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/source');
        }
    }

    /* this funtion is used to save multiple purpose */
    public function add_multiple_source() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('source_'.$row_value, $this->lang->line('source'), 'trim|required');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array('source' => form_error('source_'.$row_value));
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
                    'source' => $this->input->post('source_'.$row_values),
                    'description' => $this->input->post('description_'.$row_values));
                $this->source_model->add($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message= $this->lang->line("please_add_at_least_one_source");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}

    public function edit1($source_id)
    {
        if (!$this->rbac->hasPrivilege('setup_front_office', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('source', $this->lang->line('source'), 'required');
        if ($this->form_validation->run() == false) {
            $data['source_list'] = $this->source_model->source_list();
            $data['source_data'] = $this->source_model->source_list($source_id);
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/sourceeditview', $data);
            $this->load->view('layout/footer');
        } else {

            $source = array(
                'source'      => $this->input->post('source'),
                'description' => $this->input->post('description'),
            );
            $this->source_model->update($source_id, $source);
            $this->session->set_flashdata('msg', '<div class="alert alert-success"> ' . $this->lang->line('source_updated_successfully') . '</div>');
            redirect('admin/source');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id');
        if (!$this->rbac->hasPrivilege('setup_front_office', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('source', $this->lang->line('source'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('source'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $source = array(
                'source'      => $this->input->post('source'),
                'description' => $this->input->post('description'),
            );
            $this->source_model->update($id, $source);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }

        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('setup_front_office', 'can_delete')) {
            access_denied();
        }
        $this->source_model->delete($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

    public function get_data($id)
    {
        $result = $this->source_model->source_list($id);
        echo json_encode($result);
    }

}
