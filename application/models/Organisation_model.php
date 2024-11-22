<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Organisation_model extends MY_Model
{
    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('organisation', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Organisation id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
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
        } else {
            $this->db->insert('organisation', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " For Organisation id " . $insert_id;
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
                //return $return_value;
            }
            return $insert_id;
        }
       
    }

    public function get($id = null)
    {
        $this->db->select()->from('organisation');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getAlltpaRecord()
    {
        $this->datatables
            ->select('organisation.*')
            ->searchable('organisation.id,organisation.organisation_name,organisation.code,organisation.contact_no,organisation.address,organisation.contact_person_name,organisation.contact_person_name')
            ->orderable('organisation.id,organisation.organisation_name,organisation.code,organisation.contact_no,organisation.address,organisation.contact_person_name,organisation.contact_person_name')
            ->sort('organisation.id', 'desc')
            ->from('organisation');
        return $this->datatables->generate('json');
    } 

    public function delete($id)
    {   
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('organisation');
        $message = DELETE_RECORD_CONSTANT . " Where Organisation id " . $id;
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
            //return $return_value;
        }
    }

    public function Charge($ch_type)
    {
        $this->db->select(' charges.id , charges.standard_charge, schedule_charge_category.schedule');
        $this->db->join('schedule_charge_category', 'schedule_charges.schedule_charge_id = schedule_charge_category.id', 'left');
        $this->db->join('charges', 'schedule_charges.charge_id = charges.id', 'left');
        $this->db->where('charges.charge_type', $ch_type);
        $query = $this->db->get('schedule_charges');
        return $query->result_array();
    }

}
