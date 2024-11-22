<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Lab extends Admin_Controller
{

    public function addlab()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'addlab/index');
        $lab_id = $this->input->post("lab_id");

        $labName         = $this->lab_model->getlabName();
        $data["labName"] = $labName;
        $this->form_validation->set_rules(
            'lab_name', $this->lang->line('category_name'), array('required',
                array('check_exists', array($this->lab_model, 'valid_parameter_name')),
            )
        );
        $data["title"] = $this->lang->line('add_lab');
        if ($this->form_validation->run()) {
            $labName = $this->input->post("lab_name");
            $lab_id  = $this->input->post("id");
            if (empty($lab_id)) {
                if (!$this->rbac->hasPrivilege('lab', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('lab', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($lab_id)) {
                $data = array('lab_name' => $labName, 'id' => $lab_id);
            } else {
                $data = array('lab_name' => $labName);
            }

            $insert_id = $this->lab_model->addLabName($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/lab/addlab");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/radio/lab", $data);
            $this->load->view("layout/footer");
        }
    }
	
    public function unit()
    {
        if (!$this->rbac->hasPrivilege('radiology_unit', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'addlab/index');
        $unit_id          = $this->input->post("unit_id");
        $unitname         = $this->lab_model->getunit();
        $data["unitname"] = $unitname;
        $this->form_validation->set_rules(
            'unit_name', $this->lang->line('unit_name'), array('required',
                array('check_exists', array($this->lab_model, 'valid_unit_name')),
            )
        );
        $data["title"] = $this->lang->line('add_unit');
        if ($this->form_validation->run()) {
            $unit_name = $this->input->post("unit_name");
            $unit_id   = $this->input->post("id");
            if (empty($unit_id)) {

                if (!$this->rbac->hasPrivilege('unit_name', 'can_add')) {
                    access_denied();
                }
            } else {

                if (!$this->rbac->hasPrivilege('unit_name', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($unit_id)) {
                $data = array('unit_name' => $unit_name, 'id' => $unit_id);
            } else {
                $data = array('unit_name' => $unit_name);
            }

            $insert_id = $this->lab_model->addunit($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/pathologycategory/addCategory");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/radio/unit", $data);
            $this->load->view("layout/footer");
        }
    }

    public function get_dataunit($id)
    {
        if (!$this->rbac->hasPrivilege('radiology_unit', 'can_view')) {
            access_denied();
        }
        $result = $this->lab_model->getunit($id);
        echo json_encode($result);
    }

    public function deleteunit($id)
    {
        if (!$this->rbac->hasPrivilege('radiology_unit', 'can_delete')) {
            access_denied();
        }
        $this->lab_model->deleteunit($id);
        redirect('admin/lab/unit');
    }

    public function addunit()
    {
        if (!$this->rbac->hasPrivilege('radiology_unit', 'can_add')) {
            access_denied();
        }
        $unit_id = $this->input->post("unit_id");

        $this->form_validation->set_rules(
            'unit_name', $this->lang->line('unit_name'), array('required',
                array('check_exists', array($this->lab_model, 'valid_unit_name')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'unit_name' => form_error('unit_name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $unitname = $this->input->post("unit_name");
            $unittype = 'radio';
            if (!empty($unit_id)) {
                if (!$this->rbac->hasPrivilege('radiology_unit', 'can_edit')) {
                    access_denied();
                }
                $data  = array('unit_name' => $unitname, 'unit_type' => $unittype, 'id' => $unit_id);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {
                if (!$this->rbac->hasPrivilege('radiology_unit', 'can_add')) {
                    access_denied();
                }
                $data  = array('unit_name' => $unitname, 'unit_type' => $unittype);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }
            $insert_id = $this->lab_model->addunit($data);
        }
        echo json_encode($array);
    }

    public function radioparameter()
    {
        if (!$this->rbac->hasPrivilege('radiology_parameter', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'addlab/index');
        $lab_id                = $this->input->post("lab_id");
        $parameterName         = $this->lab_model->getradioparameter();
        $data["parameterName"] = $parameterName;
        $unitname              = $this->lab_model->getunit();
        $data["unitname"]      = $unitname;
        $this->form_validation->set_rules(
            'parameter_name', $this->lang->line('parameter_name'), array('required',
                array('check_exists', array($this->lab_model, 'valid_lab_name')),
            )
        );
        $data["title"] = "Add Lab";
        if ($this->form_validation->run()) {
            $labName = $this->input->post("lab_name");
            $lab_id  = $this->input->post("id");
            if (empty($lab_id)) {
                if (!$this->rbac->hasPrivilege('lab', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('lab', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($lab_id)) {
                $data = array('lab_name' => $labName, 'id' => $lab_id);
            } else {
                $data = array('lab_name' => $labName);
            }

            $insert_id = $this->lab_model->addLabName($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/lab/radioparameter");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/radio/radioparameter", $data);
            $this->load->view("layout/footer");
        }
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('radiology_category', 'can_add')) {
            access_denied();
        }
        $labName = $this->input->post("lab_name");
        $lab_id  = $this->input->post("lab_id");
        $this->form_validation->set_rules(
            'lab_name', $this->lang->line('category_name'), array('required',
                array('check_exists', array($this->lab_model, 'valid_lab_name')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('lab_name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $medicineCategory = $this->input->post("medicine_category");
            if (!empty($lab_id)) {
                $data  = array('lab_name' => $labName, 'id' => $lab_id);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $data  = array('lab_name' => $labName);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            }

            $insert_id = $this->lab_model->addLabName($data);
        }
        echo json_encode($array);
    }

    public function addparameter()
    {
        if (!$this->rbac->hasPrivilege('radiology_parameter', 'can_add')) {
            access_denied();
        }
        $parametername          = $this->input->post("parameter_name");
        $referencerange         = $this->input->post("from_reference_range")."-".$this->input->post("to_reference_range");
        $parameter_id           = $this->input->post("parameter_id");
        $description            = $this->input->post("description");
        $unit                   = $this->input->post("unit");
        $from_reference_range   = $this->input->post("from_reference_range");
        $to_reference_range     = $this->input->post("to_reference_range");

        $this->form_validation->set_rules(
            'parameter_name', $this->lang->line('parameter_name'), array('required',
                array('check_exists', array($this->lab_model, 'valid_parameter_name')),
            )
        );

       $this->form_validation->set_rules('from_reference_range', $this->lang->line('reference_range'), 'required');
       $this->form_validation->set_rules('to_reference_range', $this->lang->line('reference_range'), 'required');
       $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'parameter_name'  => form_error('parameter_name'),
                'reference_range' => form_error('from_reference_range'),
                'reference_range' => form_error('to_reference_range'),
                'unit'            => form_error('unit'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            if (!empty($parameter_id)) {
                $data  = array('parameter_name' => $parametername, 'id' => $parameter_id, 'reference_range' => $referencerange, 'description' => $description, 'unit' => $unit,'range_from'=>$from_reference_range,'range_to'=>$to_reference_range);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $data  = array('parameter_name' => $parametername, 'reference_range' => $referencerange, 'description' => $description, 'unit' => $unit,'range_from'=>$from_reference_range,'range_to'=>$to_reference_range);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            }
            $insert_id = $this->lab_model->addparameter($data);
        }
        echo json_encode($array);
    }

    public function get()
    {
        //get product data and encode to be JSON object
        header('Content-Type: application/json');
        echo $this->lab_model->getall();
    }

    public function edit($id)
    {
        $result          = $this->lab_model->getLabName($id);
        $data["result"]  = $result;
        $data["title"]   = $this->lang->line('edit_lab_name');
        $labName         = $this->lab_model->getLabName();
        $data["labName"] = $labName;
        $this->load->view("layout/header");
        $this->load->view("admin/radio/lab", $data);
        $this->load->view("layout/footer");
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('radiology_category', 'can_delete')) {
            access_denied();
        }
        $this->lab_model->delete($id);
        redirect('admin/lab/addlab');
    }

    public function delete_parameter($id)
    {
        if (!$this->rbac->hasPrivilege('radiology_parameter', 'can_delete')) {
            access_denied();
        }
        $this->lab_model->delete_parameter($id);
        redirect('admin/lab/radioparameter');
    }

    public function get_data($id)
    {
        if (!$this->rbac->hasPrivilege('radiology_category', 'can_view')) {
            access_denied();
        }
        $result = $this->lab_model->getLabName($id);
        echo json_encode($result);
    }

    public function get_parameterdata($id)
    {
        if (!$this->rbac->hasPrivilege('radiology_parameter', 'can_view')) {
            access_denied();
        }
        $result = $this->lab_model->getradioparameter($id);
        echo json_encode($result);
    }

}
