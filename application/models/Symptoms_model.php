<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Symptoms_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        if (!empty($id)) {
            $this->db->select('symptoms.*,symptoms_classification.symptoms_type');
            $this->db->from('symptoms');
            $this->db->join('symptoms_classification', 'symptoms_classification.id = symptoms.type', 'left');
            $this->db->where("symptoms.id", $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->select('symptoms.*,symptoms_classification.symptoms_type,symptoms_classification.id as symptoms_classification_id');
            $this->db->from('symptoms');
            $this->db->join('symptoms_classification', 'symptoms_classification.id = symptoms.type', 'left');
            $query = $this->db->get();
            return $query->result_array();
        }
    }


    public function getSymptomCountbyPatients($patient_id,$finding)
    {
		$sql= "SELECT  count(*) as `total_count` FROM (SELECT visit_details.id FROM visit_details INNER JOIN opd_details on opd_details.id=visit_details.opd_details_id WHERE symptoms LIKE '%".$finding."%' and opd_details.patient_id= ".$this->db->escape($patient_id)." Union SELECT ipd_details.id FROM ipd_details WHERE symptoms LIKE '%".$finding."%' AND ipd_details.patient_id =".$this->db->escape($patient_id).") as a";  
        $query = $this->db->query($sql);
        $result= $query->row();
        return $result->total_count;
    }

    public function getsymtype($id = null)
    {
        $this->db->select()->from('symptoms_classification');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getbysys($sys_id)
    {
        $this->db->select()->from('symptoms');
        $this->db->where('type', $sys_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function getbysysarr($sys_id)
    {
        $this->db->select()->from('symptoms');
        $this->db->where_in('type', $sys_id);
        $query = $this->db->get();
        return $query->result();
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
        $this->db->delete('symptoms');
        $message = DELETE_RECORD_CONSTANT . " On Symptoms id " . $id;
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

    public function removesymtype($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('symptoms_classification');
        $message = DELETE_RECORD_CONSTANT . " On Symptoms Classification id " . $id;
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
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('symptoms', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Symptoms id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('symptoms', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Symptoms id " . $insert_id;
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

    public function addsymtype($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('symptoms_classification', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Symptoms Classification id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('symptoms_classification', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Symptoms Classification id " . $insert_id;
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

}
