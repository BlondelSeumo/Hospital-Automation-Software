<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Medicinecategory extends Admin_Controller
{

    public function medicine()
    {        
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'medicine/index');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/medicinecategory/medicine');
        $medicinecategoryid       = $this->input->post("medicinecategoryid");
        $data["title"]            = $this->lang->line('add_medicine_category');
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $this->form_validation->set_rules(
            'medicine_category', $this->lang->line('medicine_category'), array('required',
                array('check_exists', array($this->medicine_category_model, 'valid_medicine_category')),
            )
        );
        if ($this->form_validation->run()) {
            $medicineCategory   = $this->input->post("medicine_category");
            $medicinecategoryid = $this->input->post("id");
            if (empty($medicinecategoryid)) {
                if (!$this->rbac->hasPrivilege('medicine_category', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('medicine_category', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($medicinecategoryid)) {
                $data = array('medicine_category' => $medicineCategory, 'id' => $medicinecategoryid);
            } else {
                $data = array('medicine_category' => $medicineCategory);
            }

            $insert_id = $this->medicine_category_model->addMedicineCategory($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/medicinecategory/medicine");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/pharmacy/medicine_category", $data);
            $this->load->view("layout/footer");
        }
    }

    public function supplier()
    {
        if (!$this->rbac->hasPrivilege('medicine_supplier', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'medicine/index');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/medicinecategory/supplier');
        $medicinecategoryid       = $this->input->post("medicinecategoryid");
        $data["title"]            = $this->lang->line('add_supplier');
        $supplierCategory         = $this->medicine_category_model->getSupplierCategoryPat();
        $data["supplierCategory"] = $supplierCategory;
        $this->form_validation->set_rules(
            'supplier_category', $this->lang->line('supplier_name'), array('required',
                array('check_exists', array($this->medicine_category_model, 'valid_supplier_category')),
            )
        );
        if ($this->form_validation->run()) {
            $supplierCategory   = $this->input->post("supplier_category");
            $suppliercategoryid = $this->input->post("id");
            if (empty($suppliercategoryid)) {
                if (!$this->rbac->hasPrivilege('supplier_category', 'can_add')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('supplier_category', 'can_edit')) {
                    access_denied();
                }
            }
            if (!empty($suppliercategoryid)) {
                $data = array('supplier_category' => $supplierCategory, 'id' => $suppliercategoryid);
            } else {

                $data = array('supplier_category' => $supplierCategory);
            }

            $insert_id = $this->medicine_category_model->addMedicineCategory($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/medicinecategory/supplier");
        } else {
            $this->load->view("layout/header");
            $this->load->view("admin/pharmacy/supplier_category", $data);
            $this->load->view("layout/footer");
        }
    }

    public function add()
    {       
        
        $medicinecategoryid = $this->input->post("medicinecategoryid");       
        
        $this->form_validation->set_rules(
            'medicine_category', $this->lang->line('category_name'), array('required',
                array('check_exists', array($this->medicine_category_model, 'valid_medicine_category')),
            )
        );       
        
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('medicine_category'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $medicineCategory = $this->input->post("medicine_category");
            if (!empty($medicinecategoryid)) {
                $data  = array('medicine_category' => $medicineCategory, 'id' => $medicinecategoryid);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {
                $data  = array('medicine_category' => $medicineCategory);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }
            $insert_id = $this->medicine_category_model->addMedicineCategory($data);
        }
        echo json_encode($array);
    }

    public function addsupplier()
    {	
        $suppliercategoryid = $this->input->post("suppliercategoryid");
        $this->form_validation->set_rules(
            'supplier_category', $this->lang->line('supplier_name'), array('required',
                array('check_exists', array($this->medicine_category_model, 'valid_supplier_category')),
            )
        );
        if ($this->form_validation->run() == false) {
            $msg = array(
                'supplier_category'       => form_error('supplier_category'),
                'contact'                 => form_error('contact'),
                'supplier_person'         => form_error('supplier_person'),
                'supplier_person_contact' => form_error('supplier_person_contact'),
                'address'                 => form_error('address'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $supplierCategory      = $this->input->post("supplier_category");
            $contact               = $this->input->post('contact');
            $supplierperson        = $this->input->post('supplier_person');
            $supplierpersoncontact = $this->input->post('supplier_person_contact');
            $supplierdruglicence   = $this->input->post('supplier_drug_licence');
            $address               = $this->input->post('address');
            if (!empty($suppliercategoryid)) {
                $data = array('supplier'  => $supplierCategory,
                    'id'                      => $suppliercategoryid,
                    'contact'                 => $contact,
                    'supplier_person'         => $supplierperson,
                    'supplier_person_contact' => $supplierpersoncontact,
                    'supplier_drug_licence'   => $supplierdruglicence,
                    'address'                 => $address,
                );
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
            } else {
                $data = array('supplier'  => $supplierCategory,
                    'contact'                 => $contact,
                    'supplier_person'         => $supplierperson,
                    'supplier_person_contact' => $supplierpersoncontact,
                    'supplier_drug_licence'   => $supplierdruglicence,
                    'address'                 => $address,
                );
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            }
            $insert_id = $this->medicine_category_model->addSupplierCategory($data);
        }
        echo json_encode($array);
    }

    public function get()
    {
        //get product data and encode to be JSON object
        header('Content-Type: application/json');
        echo $this->medicine_category_model->getall();
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_category', 'can_view')) {
            access_denied();
        }
        $result                   = $this->medicine_category_model->getMedicineCategory($id);
        $data["result"]           = $result;
        $data["title"]            = $this->lang->line('edit_category');
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $this->load->view("layout/header");
        $this->load->view("admin/pharmacy/medicine_category", $data);
        $this->load->view("layout/footer");
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_category', 'can_delete')) {
            access_denied();
        }
        $this->medicine_category_model->delete($id);
        redirect('admin/medicinecategory/medicine');
    }

    public function deletesupplier($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_category', 'can_delete')) {
            access_denied();
        }

        $this->medicine_category_model->deletesupplier($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

    public function get_data($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_category', 'can_view')) {
            access_denied();
        }
        $result = $this->medicine_category_model->getMedicineCategory($id);
        echo json_encode($result);
    }

    public function get_datasupplier($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_category', 'can_view')) {
            access_denied();
        }
        $result = $this->medicine_category_model->getSupplierCategory($id);
        echo json_encode($result);
    }

}
