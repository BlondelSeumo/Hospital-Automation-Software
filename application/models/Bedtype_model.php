<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bedtype_model extends MY_Model
{

    public function valid_bed_type($str)
    {
        $name = $this->input->post('name');
        if ($this->check_bed_type_exists($name)) {
            $this->form_validation->set_message('check_exists', 'Bed Type already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_bed_type_exists($name)
    {
        if ($name != 0) {
            $data  = array('name' => $name);
            $query = $this->db->where($data)->get('bed_type');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('name', $name);
            $query = $this->db->get('bed_type');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function savebed($data)
    {
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("bed_type", $data);
        } else {
            $this->db->insert("bed_type", $data);
        }
    }

    public function bedtype_list($id = null)
    {
        $this->db->select()->from('bed_type');
        if ($id != null) {
            $this->db->where('bed_type.id', $id);
        } else {
            $this->db->order_by('bed_type.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("bed_type");

        $message   = DELETE_RECORD_CONSTANT . " On Bed Type id " . $id;
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

}
