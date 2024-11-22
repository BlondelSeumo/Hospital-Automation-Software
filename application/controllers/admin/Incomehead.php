<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Incomehead extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('income_head', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'finance/index');
        $data['title']        = $this->lang->line('income_head_list');
        $category_result      = $this->incomehead_model->get();
        $data['categorylist'] = $category_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/incomehead/incomeheadList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('income_head', 'can_view')) {
            access_denied();
        }
        $data['title']    = $this->lang->line('income_head_list');
        $category         = $this->incomehead_model->get($id);
        $data['category'] = $category;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/incomehead/incomeheadShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('income_head', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('income_head_list');
        $this->incomehead_model->remove($id);
        echo json_encode(array("status" => 1, "msg" => $this->lang->line("delete_message")));
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('income_head', 'can_add')) {
            access_denied();
        }
        $data['title']        = $this->lang->line('add_income_head');
        $category_result      = $this->incomehead_model->get();
        $data['categorylist'] = $category_result;
        $this->form_validation->set_rules('incomehead', $this->lang->line('income_head'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/incomehead/incomeheadList', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'income_category' => $this->input->post('incomehead'),
                'description'     => $this->input->post('description'),
            );
            $this->incomehead_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('income_head_added_successfully') . '</div>');
            redirect('admin/incomehead/index');
        }
    }

    /* this funtion is used to save multiple income head */
    public function add_multiple_income_head() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('incomehead_'.$row_value, $this->lang->line('income_head'), 'trim|required');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array('incomehead' => form_error('incomehead_'.$row_value));
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
                    'income_category' => $this->input->post('incomehead_'.$row_values),
                    'description' => $this->input->post('description_'.$row_values));
                 $this->incomehead_model->add($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message= $this->lang->line("please_add_at_least_one_income_head");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}

    public function edit1($id)
    {
        if (!$this->rbac->hasPrivilege('income_head', 'can_edit')) {
            access_denied();
        }
        $data['title']        = $this->lang->line('edit_income_head');
        $category_result      = $this->incomehead_model->get();
        $data['categorylist'] = $category_result;
        $data['id']           = $id;
        $category             = $this->incomehead_model->get($id);
        $data['incomehead']   = $category;
        $this->form_validation->set_rules('incomehead', $this->lang->line('income_head'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/incomehead/incomeheadEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'              => $id,
                'income_category' => $this->input->post('incomehead'),
                'description'     => $this->input->post('description'),
            );
            $this->incomehead_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('income_head_updated_successfully') . '</div>');
            redirect('admin/incomehead/index');
        }
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('income_head', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('income_id');
        $this->form_validation->set_rules('incomehead', $this->lang->line('income_head'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('expensehead'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array(
                'id'              => $id,
                'income_category' => $this->input->post('incomehead'),
                'description'     => $this->input->post('description'),
            );

            $this->incomehead_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('expense_head_successfully_updated'));
        }

        echo json_encode($array);
    }

    public function get_data($id)
    {
        $category_result = $this->incomehead_model->get($id);
        echo json_encode($category_result);
    }

}
