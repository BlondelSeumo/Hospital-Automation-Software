<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Referral_category_model extends MY_Model
{

    public function get_category()
    {
        $category = $this->db->select()->get("referral_category")->result_array();
        return $category;
    } 

    public function add($category)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $this->db->insert('referral_category', $category);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Referral Category id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
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

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $this->db->where('id', $id)->delete('referral_category');
        
        $message = DELETE_RECORD_CONSTANT . " On Referral Category id " . $id;
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

    public function get($id)
    {
        $category = $this->db->select()->where('id', $id)->get("referral_category")->row_array();
        return $category;
    }

    public function update($category)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start=========================== 

        $this->db->where('id', $category['id'])->update("referral_category", $category);        
        $message = UPDATE_RECORD_CONSTANT . " On Referral Category id " . $category['id'];
        $action = "Update";
        $record_id = $category['id'];
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

    public function get_type()
    {
        $type = $this->db->select()->get("referral_type")->result_array();
        return $type;
    }

}
