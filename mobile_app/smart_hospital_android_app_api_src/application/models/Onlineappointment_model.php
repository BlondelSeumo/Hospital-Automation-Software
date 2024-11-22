<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Onlineappointment_model extends CI_Model
{
    public function doctorShiftById($doctor_id)
    {
        $this->db->select("g.id,g.name");
        $this->db->join("global_shift as g", "dg.global_shift_id=g.id", "left");
        $this->db->where("dg.staff_id", $doctor_id);
        $query  = $this->db->get("doctor_global_shift as dg");
        $result = $query->result_array();
        return $result;
    }
	
	public function getShiftdata($doctor, $day, $doctor_global_shift_id)
    {
        $this->db->select("id,staff_id as doctor_id,date_format(start_time,'%h:%i %p') as start_time ,date_format(end_time,'%h:%i %p') as end_time");
        $this->db->where("staff_id", $doctor);
        $this->db->where("doctor_global_shift_id", $doctor_global_shift_id);
        $this->db->where("day", $day);
        $query  = $this->db->get("doctor_shift_time");
        $result = $query->result();
        return $result;
    }
    
    public function getSlotByDoctorShift($doctor_id, $shift, $day)
    {   
        $this->db->select("id,staff_id as doctor_id,date_format(start_time,'%h:%i %p') as start_time ,date_format(end_time,'%h:%i %p') as end_time");
        $this->db->where("staff_id", $doctor_id);
        $this->db->where("global_shift_id", $shift);
        $this->db->where("day", $day);
        $query  = $this->db->get("doctor_shift");
        $result = $query->result();
        return $result;
    }
    
    public function getShiftById($id)
    {
        $this->db->select("start_time, end_time");
        $this->db->where("id", $id);
        $query  = $this->db->get("doctor_shift_time");
        $result = $query->row_array();
        return $result;		 
    }
    
    public function getShiftDetails($doctor)
    {
        $this->db->select("consult_duration,charge_id");
        $this->db->where("staff_id", $doctor);
        $query  = $this->db->get("shift_details");
        $result = $query->row_array();
        return $result;
    }
    
    public function slotByDoctorShift($doctor_id, $shift)
    {      
        $shift             = $this->onlineappointment_model->getShiftById($shift);
        $starttime         = $shift["start_time"];
        $endtime           = $shift["end_time"];
        $shift_details     = $this->onlineappointment_model->getShiftDetails($doctor_id);
        $duration          = $shift_details['consult_duration'];
        $array_of_time     = array();
        $start_time        = strtotime($starttime);
        $end_time          = strtotime($endtime);
        
        $add_mins          = $duration * 60;
        while ($start_time < $end_time) {
            $array_of_time[] = date("h:i a", $start_time);
            $start_time += $add_mins;
        }
        return $array_of_time;
    }  
    
    public function getDoctorGlobalShiftId($doctor_id,$shift_id)
    {
        $this->db->select("doctor_global_shift.*");
        $this->db->where("global_shift_id", $shift_id); 
        $this->db->where("staff_id", $doctor_id); 
        $query  = $this->db->get("doctor_global_shift");
        $result = $query->row_array();
        return $result;
    } 
	
	public function getDoctorShiftTimeId($doctor, $global_shift_id, $day)
    {        
        $this->db->select("doctor_shift_time.id");
        $this->db->join("doctor_shift_time","doctor_shift_time.doctor_global_shift_id=doctor_global_shift.id","left");
        $this->db->where("doctor_global_shift.staff_id", $doctor);
        $this->db->where("doctor_global_shift.global_shift_id", $global_shift_id);
        $this->db->where("doctor_shift_time.day", $day);       
        $query  = $this->db->get("doctor_global_shift");
        $result = $query->row();
        return $result;
    }
	
	public function getAppointments($doctor_id, $shift_id, $date)
    {
        $this->db->select("date");
        $this->db->where("doctor", $doctor_id);
        $this->db->where("doctor_shift_time_id", $shift_id);
        $this->db->where("appointment_status", "approved");
        $this->db->where("date_format(date,'%Y-%m-%d')", $date);
        $query         = $this->db->get("appointment");
        return $result = $query->result();
    }
	
}
