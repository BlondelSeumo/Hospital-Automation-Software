<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Symptoms extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('symptoms_model');
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'symptoms/index');
        $this->session->set_userdata('sub_sidebar_menu', 'setup/symptoms/symptoms_head');
        $data['title']              = $this->lang->line('symptoms_head_list');
        $symptoms_result            = $this->symptoms_model->get();
        $data['symptomsresult']     = $symptoms_result;
        $symptomsresult             = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptomsresult;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/symptoms/symptomsList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function symptomstype()
    {
        if (!$this->rbac->hasPrivilege('symptoms_type', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'symptoms/index');
        $this->session->set_userdata('sub_sidebar_menu', 'setup/symptoms/symptoms_type');
        $data['title']          = $this->lang->line('symptoms_type_list');
        $symptoms_result        = $this->symptoms_model->getsymtype();
        $data['symptomsresult'] = $symptoms_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/symptoms/symptomstypeList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function view($id)
    {

        $data['title']    = $this->lang->line('symptoms_head_list');
        $category         = $this->symptoms_model->get($id);
        $data['category'] = $category;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/symptoms/symptomsShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('symptoms_head', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('symptoms_head_list');
        $this->symptoms_model->remove($id);
        echo json_encode(array("status" => 1, "msg" => $this->lang->line('delete_message')));
    }

    public function deletesymtype($id)
    {
        if (!$this->rbac->hasPrivilege('symptoms_type', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('symptoms_type_list');
        $this->symptoms_model->removesymtype($id);
        echo json_encode(array("status" => 1, "msg" => $this->lang->line('delete_message')));
    }

    public function create()
    {

        $data['title']        = $this->lang->line('add_symptoms_head');
        $category_result      = $this->symptoms_model->get();
        $data['categorylist'] = $category_result;
        $this->form_validation->set_rules('symptoms', $this->lang->line('symptoms_head'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/symptoms/symptomsList', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'symptoms_title' => $this->input->post('symptoms_title'),
                'description'    => $this->input->post('description'),
            );
            $this->symptoms_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('income_head_added_successfully') . '</div>');
            redirect('admin/symptoms/index');
        }
    }

    public function add()
    {
        $this->form_validation->set_rules('symptoms_title', $this->lang->line('symptoms_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('type', $this->lang->line('symptoms_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'symptoms_title' => form_error('symptoms_title'),
                'type'           => form_error('type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $data = array(
                'symptoms_title' => $this->input->post('symptoms_title'),
                'type'           => $this->input->post('type'),
                'description'    => $this->input->post('description'),
            );
            $this->symptoms_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('new_income_head_successfully_inserted'));
        }

        echo json_encode($array);
    }   

    /* this funtion is used to save multiple Symotoms*/
    public function add_multiple_symadd() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('symptoms_type_'.$row_value, $this->lang->line('symptoms_type'), 'trim|required');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array(
                        'name' => form_error('symptoms_type_'.$row_value),
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
                $data  = array('symptoms_type' => $this->input->post('symptoms_type_'.$row_values));
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
                $this->symptoms_model->addsymtype($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message=	$this->lang->line("please_add_at_least_one_symptoms_type");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}
    public function edit()
    {
        $id = $this->input->post('symptoms_id');
        $this->form_validation->set_rules('symptoms_id', $this->lang->line('symptoms_head'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'symptoms_id' => form_error('symptoms_id'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $data = array(
                'id'             => $id,
                'symptoms_title' => $this->input->post('symptoms_title'),
                'type'           => $this->input->post('type'),
                'description'    => $this->input->post('description'),
            );
            $this->symptoms_model->add($data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('symptoms_head_successfully_updated'));
        }

        echo json_encode($array);
    }

    public function editsymtype()
    {
        if (!$this->rbac->hasPrivilege('symptoms_type', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('symptoms_id');
        $this->form_validation->set_rules('symptoms_id', $this->lang->line('symptoms_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('symptoms_type', $this->lang->line('symptoms_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'symptoms_id' => form_error('symptoms_id'),
                'symptoms_type' => form_error('symptoms_type'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $data = array(
                'id'            => $id,
                'symptoms_type' => $this->input->post('symptoms_type'),
            );
            $this->symptoms_model->addsymtype($data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('symptoms_type_successfully_updated'));
        }

        echo json_encode($array);
    }

    public function get_data($id)
    {
        $symptoms_result = $this->symptoms_model->get($id);
        echo json_encode($symptoms_result);
    }

    public function getsymtype_data($id)
    {
        $symptoms_result = $this->symptoms_model->getsymtype($id);
        echo json_encode($symptoms_result);
    }

}
