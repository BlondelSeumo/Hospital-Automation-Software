<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Appoint_priority_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add($appoint_priority)
    {
        $this->db->insert('appoint_priority', $appoint_priority);
    }

    public function appoint_priority_list($id = null)
    {
        $this->db->select()->from('appoint_priority');
        if ($id != null) {
            $this->db->where('appoint_priority.id', $id);
        } else {
            $this->db->order_by('appoint_priority.id');
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
        $this->db->where('id', $id);
        $this->db->delete('appoint_priority');
        $message   = DELETE_RECORD_CONSTANT . " On Appoint Priority id " . $id;
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
            return true;
            //return $return_value;
        }
    }

    public function update($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('appoint_priority', $data);
        $message   = DELETE_RECORD_CONSTANT . " On Appoint Priority id " . $id;
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
            return true;
            //return $return_value;
        }
    }
}
