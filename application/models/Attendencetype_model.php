<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Attendencetype_model extends CI_Model
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
       
    //setting page  start   
    public function getScheduleTypeStaffAttendance()
    {
        $this->db->select()->from('staff_attendance_type');
        $this->db->where('for_schedule', 1);
        $this->db->order_by('id');
        $query = $this->db->get();
        return $query->result();
    }
    //setting page end

    //staff attendave page start

    public function getStaffAttendanceType($id = null){
        $this->db->select('staff_attendance_type.*')->from('staff_attendance_type');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('is_active', 'yes');
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

	//staff attendave page start

}
