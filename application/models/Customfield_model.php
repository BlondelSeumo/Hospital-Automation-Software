<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class customfield_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($id = null)
    {
        $this->db->select()->from('custom_fields');
        if ($id != null) {
            $this->db->where('custom_fields.id', $id);
        } else {
            $this->db->order_by('custom_fields.belong_to', 'asc');
            $this->db->order_by('custom_fields.weight', 'asc');            
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result_array();
        }
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('custom_fields');        
        $message = DELETE_RECORD_CONSTANT . " On Custom Fields id " . $id;
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

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('custom_fields', $data);            
            $message = UPDATE_RECORD_CONSTANT . " On Custom Fields id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);            
        } else {
            $this->db->insert('custom_fields', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Custom Fields id " . $insert_id;
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
            //return $return_value;
        }
        return $record_id;
    }

    public function updateorder($data)
    {
        $this->db->update_batch('custom_fields', $data, 'id');
    }

    public function getByBelong($belong_to)
    {
        $this->db->from('custom_fields');
        $this->db->where('belong_to', $belong_to);
        $this->db->order_by('custom_fields.belong_to', 'asc');        
        $query  = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    
    public function getByBelongPatientPanel($belong_to)
    {
        $this->db->from('custom_fields');
        $this->db->where('belong_to', $belong_to);
        $this->db->where('custom_fields.visible_on_patient_panel',1);
        $this->db->order_by('custom_fields.belong_to', 'asc');        
        $query  = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function insertRecord($custom_value_array, $insert_id)
    {        
        foreach ($custom_value_array as $insert_key => $insert_value) {
            $custom_value_array[$insert_key]['belong_table_id'] = $insert_id;
        }
        $this->db->insert_batch('custom_field_values', $custom_value_array);        
    }

    public function updateRecord($custom_value_array, $id, $belong_to)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start========================== 
        foreach ($custom_value_array as $custom_value_key => $custom_value_value) {
            
            $this->db->where('belong_table_id', $id);
            $this->db->where('custom_field_id', $custom_value_value['custom_field_id']);
            $q = $this->db->get('custom_field_values');
            if ($q->num_rows() > 0) {
                $results = $q->row();
                $this->db->where('id', $results->id);
                $this->db->update('custom_field_values', $custom_value_value);
                $message = UPDATE_RECORD_CONSTANT . " On Custom Field Values id " . $results->id;
                $action = "Update";
                $record_id = $results->id;
                $this->log($message, $record_id, $action);            
            } else {
                $this->db->insert('custom_field_values', $custom_value_value);
                $insert_id = $this->db->insert_id();
                $message = INSERT_RECORD_CONSTANT . " On Custom Field Values id " . $insert_id;
                $action = "Insert";
                $record_id = $insert_id;
                $this->log($message, $record_id, $action);
            }
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

    public function get_custom_fields($belongs_to, $display_table = null, $display_print = null, $display_report = null, $display_patient_panel = null)
    {
        $this->db->from('custom_fields');
        $this->db->where('belong_to', $belongs_to);

        if ($display_table !=null) {
            $this->db->where('visible_on_table', $display_table);
        }

        if ($display_print !=null) {
            $this->db->where('visible_on_print', $display_print);
        }

        if ($display_report !=null) {
            $this->db->where('visible_on_report', $display_report);
        }
        if ($display_patient_panel !=null) {
            $this->db->where('visible_on_patient_panel', $display_patient_panel);
        }
        
        $this->db->order_by('belong_to', 'asc');
            $this->db->order_by('weight', 'asc');
        $query  = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function delete_custom_fieldRecord($belong_table_id,$belong_to){
        $this->db->query("DELETE custom_field_values FROM custom_field_values INNER JOIN custom_fields ON custom_fields.id=custom_field_values.custom_field_id WHERE custom_field_values.belong_table_id = ".$this->db->escape($belong_table_id)." and custom_fields.belong_to=".$this->db->escape($belong_to));
        ; 
    }
}
