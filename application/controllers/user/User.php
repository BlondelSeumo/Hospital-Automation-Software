<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User extends Patient_Controller
{
    public $setting;
    public $payment_method;

    public function __construct()
    {
        parent::__construct();
        $this->payment_method = $this->paymentsetting_model->getActiveMethod();
        $this->patient_data   = $this->session->userdata('patient');
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->appointment_status = $this->config->item('appointment_status');
        $this->marital_status     = $this->config->item('marital_status');
        $this->payment_mode       = $this->config->item('payment_mode');
        $this->search_type        = $this->config->item('search_type');
        $this->blood_group        = $this->config->item('bloodgroup');
        $this->charge_type        = $this->customlib->getChargeMaster();
        $data["charge_type"]      = $this->charge_type;
    }

    public function changepass()
    {
        $data['title'] = 'Change Password';
        $this->form_validation->set_rules('current_pass', $this->lang->line('current_password'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_pass', $this->lang->line('new_password'), 'trim|required|xss_clean|matches[confirm_pass]');
        $this->form_validation->set_rules('confirm_pass', $this->lang->line('confirm_password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $sessionData            = $this->session->userdata('patient');
            $data['id']       = $sessionData['id'];
            $data['username'] = $sessionData['username'];
            $this->load->view('layout/patient/header', $data);
            $this->load->view('user/change_password', $data);
            $this->load->view('layout/patient/footer', $data);
        } else {
            $sessionData = $this->session->userdata('patient');
            $data_array  = array(
                'current_pass' => ($this->input->post('current_pass')),
                'new_pass'     => ($this->input->post('new_pass')),
                'user_id'      => $sessionData['id'],
                'user_name'    => $sessionData['username'],
            );
            $newdata = array(
                'id'       => $sessionData['id'],
                'password' => $this->input->post('new_pass'),
            );
            $query1 = $this->user_model->checkOldPass($data_array);
            
            if ($query1) {
                $query2 = $this->user_model->changeStatus($newdata);
                if ($query2) {
                    $this->session->set_flashdata('success_msg', $this->lang->line('success_message'));
                    $this->load->view('layout/patient/header', $data);
                    $this->load->view('user/change_password', $data);
                    $this->load->view('layout/patient/footer', $data);
                }
            } else {
                $this->session->set_flashdata('error_msg', $this->lang->line('invalid_current_password'));
                $this->load->view('layout/patient/header', $data);
                $this->load->view('user/change_password', $data);
                $this->load->view('layout/patient/footer', $data);
            }
        }
    }

    public function changeusername()
    {
        $sessionData   = $this->session->userdata('patient');
        $data['title'] = 'Change Username';
        $this->form_validation->set_rules('current_username', $this->lang->line('current_username'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_username', $this->lang->line('new_username'), 'trim|required|xss_clean|matches[confirm_username]');
        $this->form_validation->set_rules('confirm_username', $this->lang->line('confirm_password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
 
        } else {
            $data_array = array(
                'username'     => $this->input->post('current_username'),
                'new_username' => $this->input->post('new_username'),
                'role'         => $sessionData['role'],
                'user_id'      => $sessionData['id'],
            );
            $newdata = array(
                'id'       => $sessionData['id'],
                'username' => $this->input->post('new_username'),
            );
            $is_valid = $this->user_model->checkOldUsername($data_array);

            if ($is_valid) {
                $is_exists = $this->user_model->checkUserNameExist($data_array);
                if (!$is_exists) {
                    $is_updated = $this->user_model->changeStatus($newdata);
                    if ($is_updated) {
                        $this->session->set_flashdata('success_msg', $this->lang->line('success_message'));
                        redirect('user/user/changeusername');
                    }
                } else {
                    $this->session->set_flashdata('error_msg', $this->lang->line('username_already_exists'));
                }
            } else {
                $this->session->set_flashdata('error_msg', $this->lang->line('invalid_username'));
            }
        }
        $this->data['id']       = $sessionData['id'];
        $this->data['username'] = $sessionData['username'];
        $this->load->view('layout/patient/header', $data);
        $this->load->view('user/change_username', $data);
        $this->load->view('layout/patient/footer', $data);
    }

   

}
