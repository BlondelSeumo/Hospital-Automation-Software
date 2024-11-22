<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class General_call_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('general_calls', $data);
        
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On General Calls id " . $insert_id;
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

    public function getAllgeneralcallRecord()
    {
        $this->datatables
            ->select('general_calls.*')
            ->searchable('general_calls.name,general_calls.contact,general_calls.date,general_calls.description,general_calls.  follow_up_date,general_calls.call_duration,general_calls.note,general_calls.call_type')
            ->orderable('general_calls.name,general_calls.contact,general_calls.date,general_calls.description,general_calls.  follow_up_date,general_calls.call_duration,general_calls.note,general_calls.call_type')
            ->sort('general_calls.date', 'desc')
            ->from('general_calls');
        return $this->datatables->generate('json');
    } 

    public function call_list($id = null)
    {
        $this->db->select()->from('general_calls');
        if ($id != null) {
            $this->db->where('general_calls.id', $id);
        } else {
            $this->db->order_by('general_calls.id');
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
        $this->db->delete('general_calls');
        
        $message = DELETE_RECORD_CONSTANT . " On General Calls id " . $id;
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

    public function call_update($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('general_calls', $data);
        
        $message = UPDATE_RECORD_CONSTANT . " On General Calls id " . $id;
        $action = "Update";
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
