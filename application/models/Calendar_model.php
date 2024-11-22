<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Calendar_model extends MY_Model
{

    public function saveEvent($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"])) {
            
            $this->db->where("id", $data["id"])->update("events", $data);
            $insert_id = $this->db->insert_id();
        } else {
            
            $this->db->insert("events", $data);
            $insert_id = $this->db->insert_id();
            
        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
        # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }


     
    public function getEventsById($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get("events");
            return $query->row_array();
        } else {
            $query = $this->db->get("events");
            return $query->result_array();
        }
    }    


    public function getEvents(){  
        return $data=$this->db->query("select a.event_title,a.id,a.event_type, a.start_date, a.end_date,a.event_description,a.event_color,a.event_for,a.role_id from events a
        UNION ALL
        select 0 as title,annual_calendar.id,annual_calendar.holiday_type as event_type, annual_calendar.from_date as start_date, 
        annual_calendar.to_date as end_date, annual_calendar.description as event_description, annual_calendar.holiday_color as event_color,0 as event_for,null as role_id from annual_calendar where annual_calendar.holiday_type!=0")->result_array();   
    }
    
    
    /*
    public function getPatientEvents($id = null)
    {
        $cond  = "event_type = 'public' or event_type = 'task' ";
        $query = $this->db->where($cond)->get("events");
        return $query->result_array();
    } */


    public function getPatientEvents($id = null)
    {
        return $data=$this->db->query("select a.event_title,a.id,a.event_type, a.start_date, a.end_date,a.event_description,a.event_color,a.event_for,a.role_id,null as front_site from events a where a.event_type = 'public' or a.event_type = 'task'
        UNION ALL
        select 0 as title,annual_calendar.id,annual_calendar.holiday_type as event_type, annual_calendar.from_date as start_date, 
        annual_calendar.to_date as end_date, annual_calendar.description as event_description, annual_calendar.holiday_color as event_color,0 as event_for,null as role_id,annual_calendar.front_site from annual_calendar where annual_calendar.holiday_type!=0 and annual_calendar.front_site=1")->result_array();   
    }

    public function deleteEvent($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("events");        
        $record_id = $id;
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

    public function getTask($id, $role_id,$limit = null, $offset = null)
    {
        $query = $this->db->where(array('event_type' => 'task', 'event_for' => $id, 'role_id' => $role_id))->order_by("is_active,start_date", "asc")->limit($limit, $offset)->get("events");
        return $query->result_array();
    }

    public function countrows($id, $role_id)
    {
        $query = $this->db->where(array("event_type" => "task", 'event_for' => $id, 'role_id' => $role_id))->get("events");
        return $query->num_rows();
    }

    public function countincompleteTask($id)
    {
        $query = $this->db->where("event_type", "task")->where("is_active", "no")->where("event_for", $id)->where("start_date", date("Y-m-d"))->get("events");
        return $query->num_rows();
    }

    public function getincompleteTask($id)
    {
        $query = $this->db->where("event_type", "task")->where("is_active", "no")->where("event_for", $id)->where("start_date", date("Y-m-d"))->order_by("start_date", "asc")->get("events");
        return $query->result_array();
    }
}
