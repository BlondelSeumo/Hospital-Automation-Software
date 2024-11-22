<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Holiday_model extends MY_model {

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id']!='') {           
            $this->db->where('id', $data['id']);
            $this->db->update('annual_calendar', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  holiday master id " . $data['id'];
            $action    = "Update";
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
                //return $return_value;
            }
        } else {            
            $this->db->insert('annual_calendar', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  holiday master  id " . $id;
            $action    = "Insert";
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
            return $id;
        }

    }

    public function get($id = null,$holidaylist = null) 
    {
		$userdata           = $this->customlib->getUserData();
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
		
        $this->db->select("staff.name,staff.surname,staff.employee_id,annual_calendar.id,annual_calendar.holiday_type,annual_calendar.from_date,annual_calendar.to_date,annual_calendar.description,annual_calendar.is_active,annual_calendar.created_by,annual_calendar.created_at,annual_calendar.updated_at,annual_calendar.front_site,staff_roles.role_id");
        $this->db->from('annual_calendar');
        $this->db->join('staff', "staff.id = annual_calendar.created_by", "left");
        $this->db->join('staff_roles', "staff_roles.staff_id = staff.id");
           
        if ($id != null) {
            $this->db->where("annual_calendar.id", $id);
        } 
        if ($holidaylist != null) {
            $this->db->where("annual_calendar.holiday_type", $holidaylist);
        } 
		
		if ($doctor_restriction == 'enabled') {
            if ($userdata["role_id"] == 3) {
                $this->db->where('annual_calendar.created_by', $userdata['id']);
            }
        }
		
        $query = $this->db->get();
		
		if ($id != null) {
             return $query->row_array();
		}else{
				return $query->result_array();
		}
		
       
    }

    public function delete_holiday($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('annual_calendar');
        $message   = DELETE_RECORD_CONSTANT . " On Holiday Master id " . $id;
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
            //return $return_value;
        }
    }

    // public function getpublicEvents(){ 
        // $query = $this->db->where("event_type", 'public')->get("events");
        // return $query->result_array(); 
    // }

    public function getHolidays($front_site = null){  
		if($front_site){
			$this->db->where("front_site", 1);
		}
        $query = $this->db->where("holiday_type", 1)->get("annual_calendar");
        return $query->result_array();
    }

    public function getActivity($front_site = null){  
		if($front_site){
			$this->db->where("front_site", 1);
		}
        $query = $this->db->where("holiday_type", 2)->get("annual_calendar");
        return $query->result_array();
    }

    public function getVacation($front_site = null){ 
		if($front_site){
			$this->db->where("front_site", 1);
		}	
        $query = $this->db->where("holiday_type", 3)->get("annual_calendar");
        return $query->result_array();
    }


}
?>
