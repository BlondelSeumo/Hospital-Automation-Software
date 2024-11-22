<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Conferencehistory_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function updatehistory($data, $type)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('conference_id', $data['conference_id']);
        if ($type == "patient") {
            $this->db->where('patient_id', $data['patient_id']);
        } elseif ($type == "staff") {
            $this->db->where('staff_id', $data['staff_id']);
        }
        $q = $this->db->get('conferences_history');
        if ($q->num_rows() > 0) {
            $row               = $q->row();
            $total_hit         = $row->total_hit + 1;
            $data['total_hit'] = $total_hit;
            $this->db->where('id', $row->id);
            $this->db->update('conferences_history', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Conferences History id " . $row->id;
            $action = "Update";
            $record_id = $row->id;
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('conferences_history', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Conferences History id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /*Optional*/

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    public function getmeeting()
    {
        $sql   = "SELECT conferences.*,(SELECT COUNT(*) FROM conferences_history WHERE conferences_history.conference_id=conferences.id) as `total_viewers`,`create_by`.`name` as `create_by_name`, `create_by`.`surname` as `create_by_surname`  FROM `conferences` JOIN `staff` as `create_by` ON `create_by`.`id` = `conferences`.`created_id` WHERE purpose='meeting' and status=2 ORDER BY DATE(`conferences`.`date`) DESC, `conferences`.`date` ASC";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getconsult()
    {
        $sql   = "SELECT conferences.*,(SELECT COUNT(*) FROM conferences_history WHERE conferences_history.conference_id=conferences.id) as `total_viewers`,`create_by`.`name` as `create_by_name`, `create_by`.`surname` as `create_by_surname`  FROM `conferences` JOIN `staff` as `create_by` ON `create_by`.`id` = `conferences`.`created_id` WHERE purpose='consult' and status=2 ORDER BY DATE(`conferences`.`date`) DESC, `conferences`.`date` ASC";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getMeetingStaff($conference_id)
    {
        $this->db->select('conferences_history.*,for_create.name as `create_for_name`,for_create.surname as `create_for_surname,roles.name as `role_name`,for_create.employee_id')->from('conferences_history');
        $this->db->join('staff as for_create', 'for_create.id = conferences_history.staff_id');
        $this->db->join('staff_roles', 'staff_roles.staff_id = for_create.id');
        $this->db->join('roles', 'roles.id = staff_roles.role_id');
        $this->db->where('conference_id', $conference_id);
        $this->db->order_by('conferences_history.id');
        $query = $this->db->get();
        return $query->result();
    }
  
    public function getLivePatient($conference_id)
    {
        $this->db->select('conferences_history.*,patients.id as pid,patients.patient_name,patients.id as patient_unique_id,patients.mobileno')->from('conferences_history');
        $this->db->where('conference_id', $conference_id);
        $this->db->join('patients', 'patients.id = conferences_history.patient_id');
        $this->db->order_by('conferences_history.id');
        $query = $this->db->get();
        return $query->result();
    }
}
