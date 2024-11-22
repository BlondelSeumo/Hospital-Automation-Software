<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Referralcommission extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("referral_commission_model");
        $this->load->library("form_validation");
    }

    public function add()
    {
        $data = array();
        $this->form_validation->set_rules("category", $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("commission", $this->lang->line('commission'), 'numeric');
        if ($this->form_validation->run() == false) {
            $msg = array(
                "category"   => form_error('category'),
                "commission" => form_error('commission'),
            );
            $data = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $category_id       = $this->input->post('category');
            $module_commission = $this->input->post('module_commission');
            $referral_type_id  = $this->input->post('referral_type_id');
            $i                 = 0;

            $commission      = $this->referral_commission_model->get_type_by_category($category_id);
            $commission_type = array_column($commission, 'referral_type_id');
            if (!empty($commission)) {
                foreach ($referral_type_id as $type_id) {
                    $commission_data = array(
                        "commission"           => $module_commission[$i],
                        "referral_type_id"     => $type_id,
                        "referral_category_id" => $category_id,
                    );
                    $i++;
                    if (in_array($type_id, $commission_type, true)) {                        
                        $this->referral_commission_model->update($commission_data);
                    } else {                        
                        $this->referral_commission_model->add($commission_data);
                    }
                }
            } else {
                if (!empty($referral_type_id)) {
                    
                    foreach ($referral_type_id as $type_id) {
                        $commission_data = array(
                            "commission"           => $module_commission[$i],
                            "referral_type_id"     => $type_id,
                            "referral_category_id" => $category_id,
                        );
                        $i++;
                        $this->referral_commission_model->add($commission_data);
                    }
                }
            }
            $data = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($data);
    }

    public function get_by_category($id)
    {
        $data = $this->referral_commission_model->get_by_category($id);
        echo json_encode($data);
    }

    public function delete($category_id)
    {								
        if (!$this->rbac->hasPrivilege('referral_commission', 'can_delete')) {
            access_denied();
        }
        if (!empty($category_id)) {
            $this->referral_commission_model->delete($category_id);
            echo json_encode(array("status" => 1, "message" => $this->lang->line("delete_message")));
        } else {
            redirect("admin/referral/commission");
        }
    }
}
