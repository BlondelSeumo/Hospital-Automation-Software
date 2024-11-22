<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Medicine_category_model extends MY_model
{

    public function valid_medicine_category($str)
    {
        $medicine_category = $this->input->post('medicine_category');
        $id                = $this->input->post('medicinecategoryid');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_category_exists($medicine_category, $id)) {
            
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function valid_medicine_name($str)
    {
        $medicine_name = $this->input->post('medicine_name');
        $id            = $this->input->post('id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_name_exists($medicine_name, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_name_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'medicine_name' => $name);
            $query = $this->db->where($data)->get('pharmacy');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('medicine_name', $name);
            $query = $this->db->get('pharmacy');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function valid_supplier_category($str)
    {
        $supplier_category = $this->input->post('supplier_category');
        $id                = $this->input->post('suppliercategoryid');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_category_existssupplier($supplier_category, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function getMedicineCategory($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('medicine_category');
            return $query->row_array();
        } else {
            $query = $this->db->get("medicine_category");
            return $query->result_array();
        }
    }

    public function getSupplierCategory($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('medicine_supplier');
            return $query->row_array();
        } else {
            $query = $this->db->get("medicine_supplier");
            return $query->result_array();
        }
    }

    public function getMedicineCategoryPat($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('medicine_category');
            return $query->row_array();
        } else {
            $query = $this->db->get("medicine_category");
            return $query->result_array();
        }
    }

    public function getSupplierCategoryPat($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('medicine_supplier');
            return $query->row_array();
        } else {
            $query = $this->db->get("medicine_supplier");
            return $query->result_array();
        }
    }

    public function check_category_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'medicine_category' => $name);
            $query = $this->db->where($data)->get('medicine_category');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('medicine_category', $name);
            $query = $this->db->get('medicine_category');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function check_category_existssupplier($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'supplier' => $name);
            $query = $this->db->where($data)->get('medicine_supplier');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('supplier', $name);
            $query = $this->db->get('medicine_supplier');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
 
    public function addMedicineCategory($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('medicine_category', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Medicine Category id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('medicine_category', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Medicine Category id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
        
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function addSupplierCategory($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('medicine_supplier', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Medicine Supplier id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('medicine_supplier', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Medicine Supplier id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
        
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getall()
    {
        $this->datatables->select('id,medicine_category');
        $this->datatables->from('medicine_category');
        $this->datatables->add_column('view', '<a href="' . site_url('admin/medicinecategory/edit/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Edit"> <i class="fa fa-pencil"></i></a><a href="' . site_url('admin/medicinecategory/delete/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Delete">
                                                        <i class="fa fa-remove"></i>
                                                    </a>', 'id');
        return $this->datatables->generate();
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("medicine_category");
        $message = DELETE_RECORD_CONSTANT . " On Medicine Category id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }
 
    public function deletesupplier($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("medicine_supplier");
        $message = DELETE_RECORD_CONSTANT . " On Medicine Category id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }
}
