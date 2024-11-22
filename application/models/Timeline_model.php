<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Timeline_model extends MY_Model
{

    public function add_staff_timeline($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("staff_timeline", $data);
            $message = UPDATE_RECORD_CONSTANT . " On Staff Timeline id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert("staff_timeline", $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Staff Timeline id " . $insert_id;
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

    public function getStaffTimeline($id, $status = '')
    {
        if (!empty($status)) {
            $this->db->where("status", $status);
        }
        $query = $this->db->where("staff_id", $id)->order_by("timeline_date", "asc")->get("staff_timeline");
        return $query->result_array();
    }

    public function geteditTimeline($id)
    {
        $this->db->select('patient_timeline.*,patients.patient_name,patients.patient_name')->from('patient_timeline');
        $this->db->join('patients', 'patients.id = patient_timeline.patient_id');
        $this->db->where('patient_timeline.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function geteditstaffTimeline($id)
    {
        $this->db->select('staff_timeline.*,staff.id,staff.name')->from('staff_timeline');
        $this->db->join('staff', 'staff.id = staff_timeline.staff_id');
        $this->db->where('staff_timeline.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getPatientTimeline($id, $status)
    {
        if (!empty($status)) {
            $this->db->where("status", $status);
        }
        $query = $this->db->where("patient_id", $id)->order_by("timeline_date", "desc")->get("patient_timeline");
        return $query->result_array();
    }


    public function delete_staff_timeline($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("staff_timeline");        
        $message = DELETE_RECORD_CONSTANT . " On Staff Timeline id " . $id;
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

    public function add_patient_timeline($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("patient_timeline", $data);
            $message = UPDATE_RECORD_CONSTANT . " On Patient Timeline id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert("patient_timeline", $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Patient Timeline id " . $insert_id;
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

    public function delete_patient_timeline($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("patient_timeline");
        $message = DELETE_RECORD_CONSTANT . " On Patient Timeline id " . $id;
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
}
