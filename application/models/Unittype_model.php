<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Unittype_model extends MY_model
{
    public function get($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('charge_units');
            return $query->row();
        } else {
            $query = $this->db->get("charge_units");
            return $query->result();
        }
    }

    public function valid_unit_type($str)
    {
        $unit = $this->input->post('unit');
        $id   = $this->input->post('id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_category_exists($unit, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_category_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'unit' => $name);
            $query = $this->db->where($data)->get('charge_units');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('unit', $name);
            $query = $this->db->get('charge_units');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] > 0 ) {
            $this->db->where('id', $data['id']);
            $this->db->update('charge_units', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Charge Units id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            unset($data["id"]); 
            $this->db->insert('charge_units', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Charge Units id " . $insert_id;
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

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("charge_units");
        
        $message = DELETE_RECORD_CONSTANT . " On Charge Units id " . $id;
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

    public function getAllRecord()
    {
        $this->datatables
            ->select('charge_units.*')
            ->searchable('charge_units.unit')
            ->orderable('charge_units.unit')
            ->sort('charge_units.id', 'desc')
            ->from('charge_units');
        return $this->datatables->generate('json');
    }

}
