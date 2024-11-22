<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Chargetype extends Admin_Controller
{
    public function index()
    {
        if (!$this->rbac->hasPrivilege('charge_type', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/chargetype/index');
        $this->session->set_userdata('sub_menu', 'charges/index');
        $this->config->load("payroll");
        $charge_type         = $this->customlib->getChargeMaster();
        $result              = $this->setting_model->getChargeMaster();
        $arr                 = array();
        $data["charge_type"] = $charge_type;
        $data['resultlist']  = $result;
        $data['schedule']    = $this->organisation_model->get();
        $data['charge_type_modules'] = $this->customlib->chargeTypeModule();
        $data['module_data'] = $this->chargetype_model->getChargeModuleData($data['charge_type_modules']);       
        $this->load->view("layout/header");
        $this->load->view("admin/charges/chargeType", $data);
        $this->load->view("layout/footer");
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('charge_type', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('charge_type', $this->lang->line('charge_type'), 'required');
        $charge_modules = $this->input->post("charge_module");
        if(empty($charge_modules) || $charge_modules == ""){
            $this->form_validation->set_rules('charge_module', $this->lang->line('module'), 'required');
        }
        if ($this->form_validation->run() == false) {
            $msg = array(
                'charge_type' => form_error('charge_type'),
                'module'    => form_error('charge_module')
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array(
                'charge_type' => $this->input->post('charge_type'),
                'is_default'  => 'no',
                'is_active'   => 'yes',
            );

            $insert_id  = $this->chargetype_model->add($data);
            $charge_modules = $this->input->post("charge_module");
            foreach($charge_modules as $module_shortcode){
                $module_data = array(
                    "charge_type_master_id" => $insert_id,
                    "module_shortcode" => $module_shortcode,
                );
                $this->chargetype_model->addChargeModuleData($module_data);
            }
            $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('charge_type', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('editchargetype', $this->lang->line('charge_type'), 'required');
        
        if ($this->form_validation->run() == false) {
            $msg = array(
                'editchargetype' => form_error('editchargetype'),
                
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id= $this->input->post('editchargeid') ;
            $data = array(
                'charge_type' => $this->input->post('editchargetype'),
                'is_active'   => 'yes',
                
            );

           $this->chargetype_model->updatechargetype($data,$id);
            
            $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('charge_type', 'can_delete')) {
            access_denied();
        }

        $result = $this->chargetype_model->delete($id);
        echo json_encode(array("status"=>"success","message"=>$this->lang->line("delete_message")));
    }

    public function updateChargeTypeModule(){
        $charge_type = $this->input->post("charge_type");
        $module_shortcode = $this->input->post("module_shortcode");

        $module_data = array(
            "charge_type_master_id" => $charge_type,
            "module_shortcode" => $module_shortcode,
        );
        $this->chargetype_model->updateChargeTypeModule($module_data);
        echo json_encode(array("status"=>"success","message"=>$this->lang->line("success_message")));
    }

    public function getchargetype()
    {
         $id         = $this->input->post('id');
         $result     = $this->chargetype_model->get($id);
         $json_array = array('status' => '1', 'error' => '', 'result' => $result);
         echo json_encode($json_array);
    }
    

}
