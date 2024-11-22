<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Referralperson extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("referral_person_model");
        $this->load->library("form_validation");
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('referral_person', 'can_add')) {
            access_denied();
        }

        $data              = array();
        $commission_amount = "";
        $module_commission = $this->input->post("module_commission");
        $referral_type_id  = $this->input->post("referral_type_id");

        foreach ($referral_type_id as $key => $value) {
            if (empty($commission_amount)) {
                $commission_amount = $module_commission[$key];
            }
        }

        $this->form_validation->set_rules("name", $this->lang->line('referrer_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("category", $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("referrer_contact", $this->lang->line('referrer_contact'), 'trim|numeric|xss_clean');
        $this->form_validation->set_rules("person_name", $this->lang->line('person_name'), 'trim|xss_clean');
        $this->form_validation->set_rules("person_phone", $this->lang->line('person_phone'), 'trim|numeric|xss_clean');
        $this->form_validation->set_rules("address", $this->lang->line("address"), 'trim|xss_clean');
        $this->form_validation->set_rules("person_id", $this->lang->line('person_id'), 'trim|xss_clean');
        $this->form_validation->set_rules("commission", $this->lang->line('standard_commission'), 'trim|xss_clean|valid_amount');
        if (empty($commission_amount)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('commission_for_modules_amounts_are_required')));
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                "name"             => form_error('name'),
                "category"         => form_error('category'),
                "commission"       => form_error('commission'),
                "referrer_contact" => form_error('referrer_contact'),
                "person_phone"     => form_error('person_phone'),
                "no_records"       => form_error("no_records"),
            );
            $data = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $name         = $this->input->post("name");
            $category_id  = $this->input->post("category");
            $contact      = $this->input->post("referrer_contact");
            $person_name  = $this->input->post("person_name");
            $person_phone = $this->input->post("person_phone");
            $address      = $this->input->post("address");

            $person = array(
                "name"                => $name,
                "category_id"         => $category_id,
                "contact"             => $contact,
                "person_name"         => $person_name,
                "person_phone"        => $person_phone,
                "address"             => $address,
                "standard_commission" => $this->input->post("commission"),
            );

            $person_id = $this->referral_person_model->add($person);
            $i         = 0;
            if (!empty($referral_type_id)) {
                foreach ($referral_type_id as $type_id) {
                    $person_commission = array(
                        "referral_person_id" => $person_id,
                        "referral_type_id"   => $type_id,
                        "commission"         => $module_commission[$i],
                    );
                    $i++;
                    $this->referral_person_model->add_commission($person_commission);

                }
            }

            $data = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('referral_person', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->referral_person_model->delete($id);
        }
        echo json_encode(array('msg' => $this->lang->line('delete_message')));
    }

    public function get($id)
    {
        $data = $this->referral_person_model->get($id);
        echo json_encode($data);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('referral_person', 'can_edit')) {
            access_denied();
        }
        $data              = array();
        $commission_amount = "";
        $module_commission = $this->input->post("module_commission");
        $referral_type_id  = $this->input->post("referral_type_id");

        foreach ($referral_type_id as $key => $value) {
            if (empty($commission_amount)) {
                $commission_amount = $module_commission[$key];
            }
        }
        $this->form_validation->set_rules("name", $this->lang->line('referrer_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("category", $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("referrer_contact", $this->lang->line('referrer_contact'), 'trim|numeric|xss_clean');
        $this->form_validation->set_rules("person_name", $this->lang->line('person_name'), 'trim|xss_clean');
        $this->form_validation->set_rules("person_phone", $this->lang->line('person_phone'), 'trim|numeric|xss_clean');
        $this->form_validation->set_rules("address", $this->lang->line("address"), 'trim|xss_clean');
        $this->form_validation->set_rules("person_id", $this->lang->line('person_id'), 'trim|xss_clean');
        if (empty($commission_amount)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('commission_for_modules_amounts_are_required')));
        }
        if ($this->form_validation->run() == false) {
            $msg = array(
                "name"             => form_error('name'),
                "category"         => form_error('category'),
                "referrer_contact" => form_error('referrer_contact'),
                "person_phone"     => form_error('person_phone'),
                "category"         => form_error('category'),
                "no_records"       => form_error("no_records"),
            );
            $data = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $person_id    = $this->input->post("person_id");
            $name         = $this->input->post("name");
            $category     = $this->input->post("category");
            $contact      = $this->input->post("referrer_contact");
            $person_name  = $this->input->post("person_name");
            $person_phone = $this->input->post("person_phone");
            $address      = $this->input->post("address");
            $person       = array(
                "id"                  => $person_id,
                "name"                => $name,
                "category_id"         => $category,
                "contact"             => $contact,
                "person_name"         => $person_name,
                "person_phone"        => $person_phone,
                "address"             => $address,
                "standard_commission" => $this->input->post("commission"),
            );
            $this->referral_person_model->update($person);
            $i = 0;
            foreach ($referral_type_id as $type_id) {
                $person_commission = array(
                    "referral_person_id" => $person_id,
                    "referral_type_id"   => $type_id,
                    "commission"         => $module_commission[$i],
                );
                $this->referral_person_model->update_person_commission($person_commission);
                $i++;
            }

            $data = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($data);

    }

}
