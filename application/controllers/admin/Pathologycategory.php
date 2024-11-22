<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pathologycategory extends Admin_Controller
{

    public function addcategory()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'addCategory/index');
        $pathology_category_id = $this->input->post("pathology_category_id");

        $categoryName         = $this->pathology_category_model->getcategoryName();
        $data["categoryName"] = $categoryName;
        $this->form_validation->set_rules(
            'category_name', $this->lang->line('category_name'), array('required',
                array('check_exists', array($this->pathology_category_model, 'valid_category_name')),
            )
        );
        $data["title"] = $this->lang->line('add_pathology_categories');
        if ($this->form_validation->run()) {
            $categoryName          = $this->input->post("category_name");
            $pathology_category_id = $this->input->post("id");
            if (empty($pathology_category_id)) {

                if (!$this->rbac->hasPrivilege('pathology_category', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('pathology_category', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($pathology_category_id)) {
                $data = array('category_name' => $categoryName, 'id' => $pathology_category_id);
            } else {
                $data = array('category_name' => $categoryName);
            }

            $insert_id = $this->pathology_category_model->addCategoryName($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/pathologycategory/addCategory");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/pathology/category", $data);
            $this->load->view("layout/footer");
        }
    }

    public function pathoparameter()
    {
        if (!$this->rbac->hasPrivilege('pathology_parameter', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'addCategory/index');

        $pathology_parameter_id = $this->input->post("pathology_parameter_id");

        $parameterName         = $this->pathology_category_model->getpathoparameter();
        $data["parameterName"] = $parameterName;
        $unitname              = $this->pathology_category_model->getunit();
        $data["unitname"]      = $unitname;

        $this->form_validation->set_rules(
            'parameter_name', $this->lang->line('parameter_name'), array('required',
                array('check_exists', array($this->pathology_category_model, 'valid_parameter_name')),
            )
        );
        $data["title"] = "Add Pathology parameter";
        if ($this->form_validation->run()) {
            $parameter_name = $this->input->post("parameter_name");

            $pathology_parameter_id = $this->input->post("id");
            if (empty($pathology_category_id)) {

                if (!$this->rbac->hasPrivilege('parameter_name', 'can_add')) {
                    access_denied();
                }
            } else {

                if (!$this->rbac->hasPrivilege('parameter_name', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($pathology_category_id)) {
                $data = array('parameter_name' => $parameter_name, 'id' => $pathology_parameter_id);
            } else {
                $data = array('parameter_name' => $parameter_name);
            }

            $insert_id = $this->pathology_category_model->addCategoryName($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/pathologycategory/addCategory");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/pathology/pathoparameter", $data);
            $this->load->view("layout/footer");
        }
    }

    public function unit()
    {
        if (!$this->rbac->hasPrivilege('pathology_unit', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'addCategory/index');

        $unit_id = $this->input->post("unit_id");

        $unitname         = $this->pathology_category_model->getunit();
        $data["unitname"] = $unitname;
        $this->form_validation->set_rules(
            'unit_name', $this->lang->line('unit_name'), array('required',
                array('check_exists', array($this->pathology_category_model, 'valid_unit_name')),
            )
        );
        $data["title"] = "Add Unit";
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
                $data = array('unit_name' => $unit_name, 'id' => $pathology_unit_id);
            } else {
                $data = array('unit_name' => $unit_name);
            }

            $insert_id = $this->pathology_category_model->addunit($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/pathologycategory/addCategory");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/pathology/unit", $data);
            $this->load->view("layout/footer");
        }
    }

    public function add()
    {
        $pathology_category_id = $this->input->post("pathology_category_id");
        $this->form_validation->set_rules(
            'category_name', $this->lang->line('category_name'), array('required',
                array('check_exists', array($this->pathology_category_model, 'valid_category_name')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('category_name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $categoryName = $this->input->post("category_name");
            if (!empty($pathology_category_id)) {
                if (!$this->rbac->hasPrivilege('pathology_category', 'can_edit')) {
                    access_denied();
                }
                $data  = array('category_name' => $categoryName, 'id' => $pathology_category_id);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {
                if (!$this->rbac->hasPrivilege('pathology_category', 'can_add')) {
                    access_denied();
                }
                $data  = array('category_name' => $categoryName);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }
            $insert_id = $this->pathology_category_model->addCategoryName($data);
        }
        echo json_encode($array);
    }

    public function addparameter()
    {
        if (!$this->rbac->hasPrivilege('pathology_parameter', 'can_add')) {
            access_denied();
        }
        $pathology_parameter_id = $this->input->post("pathology_parameter_id");
        $this->form_validation->set_rules(
            'parameter_name', $this->lang->line('parameter_name'), array('required',
                array('check_exists', array($this->pathology_category_model, 'valid_parameter_name')),
            )
        );
        
        $this->form_validation->set_rules('from_reference_range', $this->lang->line('reference_range'), 'required');
        $this->form_validation->set_rules('to_reference_range', $this->lang->line('reference_range'), 'required');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'required');
		
        if ($this->form_validation->run() == false) {
            $msg = array(
                'parameter_name'  => form_error('parameter_name'),
                'unit'            => form_error('unit'),
                'reference_range' => form_error('from_reference_range'),
                'reference_range' => form_error('to_reference_range'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $parametername          = $this->input->post("parameter_name");
            $referencerange         = $this->input->post("from_reference_range").'-'.$this->input->post("to_reference_range");
            $unit                   = $this->input->post("unit");
            $description            = $this->input->post("description");
            $from_reference_range   = $this->input->post("from_reference_range");
            $to_reference_range     = $this->input->post("to_reference_range");

            if (!empty($pathology_parameter_id)) {
                if (!$this->rbac->hasPrivilege('pathology_parameter', 'can_edit')) {
                    access_denied();
                }
                $data  = array('parameter_name' => $parametername, 'reference_range' => $referencerange, 'unit' => $unit, 'description' => $description, 'id' => $pathology_parameter_id,'range_from'=>$from_reference_range,'range_to'=>$to_reference_range);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {
                if (!$this->rbac->hasPrivilege('pathology_parameter', 'can_add')) {
                    access_denied();
                }
                $data  = array('parameter_name' => $parametername, 'reference_range' => $referencerange, 'unit' => $unit, 'description' => $description,'range_from'=>$from_reference_range,'range_to'=>$to_reference_range);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }
            $insert_id = $this->pathology_category_model->addparameter($data);
        }
        echo json_encode($array);
    }

    public function addunit()
    {
        if (!$this->rbac->hasPrivilege('pathology_unit', 'can_add')) {
            access_denied();
        }
        $unit_id = $this->input->post("unit_id");
        $this->form_validation->set_rules(
            'unit_name', $this->lang->line('unit_name'), array('required',
                array('check_exists', array($this->pathology_category_model, 'valid_unit_name')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'unit_name' => form_error('unit_name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $unitname = $this->input->post("unit_name");
            $unittype = 'patho';

            if (!empty($unit_id)) {
                if (!$this->rbac->hasPrivilege('pathology_unit', 'can_edit')) {
                    access_denied();
                }
                $data  = array('unit_name' => $unitname, 'unit_type' => $unittype, 'id' => $unit_id);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {
                if (!$this->rbac->hasPrivilege('pathology_unit', 'can_add')) {
                    access_denied();
                }
                $data  = array('unit_name' => $unitname, 'unit_type' => $unittype);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }
            $insert_id = $this->pathology_category_model->addunit($data);
        }
        echo json_encode($array);
    }

    public function get()
    {
        //get product data and encode to be JSON object
        if (!$this->rbac->hasPrivilege('pathology_category', 'can_view')) {
            access_denied();
        }
        header('Content-Type: application/json');
        echo $this->lab_model->getall();
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('pathology_category', 'can_view')) {
            access_denied();
        }
        $result               = $this->pathology_category_model->getCategoryName($id);
        $data["result"]       = $result;
        $data["title"]        = $this->lang->line('edit_category_name');
        $categoryName         = $this->pathology_category_model->getCategoryName();
        $data["categoryName"] = $categoryName;
        $this->load->view("layout/header");
        $this->load->view("admin/pathology/category", $data);
        $this->load->view("layout/footer");
    }

    public function editparameter($id)
    {
        if (!$this->rbac->hasPrivilege('pathology_parameter', 'can_edit')) {
            access_denied();
        }
        $result                = $this->pathology_category_model->getpathoparameter($id);
        $data["result"]        = $result;
        $data["title"]         = $this->lang->line('edit_parameter');
        $parameterName         = $this->pathology_category_model->getpathoparameter();
        $data["parameterName"] = $parameterName;
        $this->load->view("layout/header");
        $this->load->view("admin/pathology/pathoparameter", $data);
        $this->load->view("layout/footer");
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('pathology_category', 'can_delete')) {
            access_denied();
        }
        $this->pathology_category_model->delete($id);
         $this->session->set_flashdata('msg', '<div class="alert alert-success">' .$this->lang->line('delete_message') . '</div>');
        redirect('admin/pathologycategory/addCategory');
    }

    public function deleteunit($id)
    {
        if (!$this->rbac->hasPrivilege('pathology_unit', 'can_delete')) {
            access_denied();
        }
        $this->pathology_category_model->deleteunit($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' .$this->lang->line('delete_message') . '</div>');
        redirect('admin/pathologycategory/unit');
    }

    public function deleteparameter($id)
    {
        if (!$this->rbac->hasPrivilege('pathology_parameter', 'can_delete')) {
            access_denied();
        }
        $this->pathology_category_model->deleteparameter($id);
        redirect('admin/pathologycategory/pathoparameter');
    }

    public function get_data($id)
    {
        if (!$this->rbac->hasPrivilege('pathology_category', 'can_view')) {
            access_denied();
        }
        $result = $this->pathology_category_model->getCategoryName($id);
        echo json_encode($result);
    }

    public function get_dataunit($id)
    {
        if (!$this->rbac->hasPrivilege('pathology_unit', 'can_view')) {
            access_denied();
        }
        $result = $this->pathology_category_model->getunit($id);
        echo json_encode($result);
    }

    public function get_data_parameter($id)
    {
        if (!$this->rbac->hasPrivilege('pathology_category', 'can_view')) {
            access_denied();
        }
        $result = $this->pathology_category_model->getpathoparameter($id);
        echo json_encode($result);
    }

}
