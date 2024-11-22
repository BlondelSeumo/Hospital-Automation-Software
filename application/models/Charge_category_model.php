<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Charge_category_model extends MY_model
{

    public function valid_charge_category($str)
    {
        $id   = $this->input->post('id');
        $name = $this->input->post('name');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_category_exists($id, $name)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function getChargeCategory($id = null)
    {
        $this->db->select('charge_categories.*,charge_type_master.charge_type');
        $this->db->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id');

        if (!empty($id)) {
            $this->db->where("charge_categories.id", $id);
            $query = $this->db->get('charge_categories');
            return $query->row_array();
        } else {
            $query = $this->db->get("charge_categories");
            return $query->result_array();
        }
    }

    public function check_category_exists($id, $name)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'name' => $name);
            $query = $this->db->where($data)->get('charge_categories');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('name', $name);
            $query = $this->db->get('charge_categories');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function addChargeCategory($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        if (isset($data['id']) && $data['id'] > 0) {
            $this->db->where('id', $data['id']);
            $this->db->update('charge_categories', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Charge Categories id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('charge_categories', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Charge Categories id " . $insert_id;
            $action    = "Insert";
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

    public function getDatatableAllRecord()
    {
        $this->datatables
            ->select('charge_categories.*,charge_type_master.charge_type')
            ->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id')
            ->searchable('charge_categories.name,charge_type_master.charge_type')
            ->orderable('charge_categories.name,charge_type_master.charge_type,description')
            ->sort('charge_categories.id', 'desc')
            ->from('charge_categories');
        return $this->datatables->generate('json');
    }

    public function getall()
    {
        $this->datatables->select('id,name,description,charge_type');
        $this->datatables->from('charge_categories');
        $this->datatables->add_column('view', '<a href="' . site_url('admin/chargecategory/edit/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Edit"> <i class="fa fa-pencil"></i></a><a href="' . site_url('admin/chargecategory/delete/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Delete">
                                                        <i class="fa fa-remove"></i>
                                                    </a>', 'id');
        return $this->datatables->generate();
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        $this->db->where("id", $id)->delete("charge_categories");
        $message   = DELETE_RECORD_CONSTANT . " On Charge Categories id " . $id;
        $action    = "Delete";
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

    public function getCategoryByModule($module_shortcode)
    {
        $result = $this->db->select("charge_categories.*")
            ->join("charge_type_module", "charge_type_module.charge_type_master_id=charge_categories.charge_type_id")
            ->where("charge_type_module.module_shortcode", $module_shortcode)
            ->get("charge_categories")
            ->result_array();
        return $result;

    }

}
