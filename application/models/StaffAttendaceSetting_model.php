<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class StaffAttendaceSetting_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getRoleAttendanceSetting()
    {
        $sql = "SELECT roles.*,staff_attendence_schedules.role_id,staff_attendence_schedules.staff_attendence_type_id,staff_attendence_schedules.id as `staff_attendence_schedules`,entry_time_from,entry_time_to,staff_attendence_schedules.total_institute_hour,roles.name as `role_name`FROM `roles` LEFT JOIN staff_attendence_schedules on staff_attendence_schedules.role_id=roles.id";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function add($insert_array = [], $role_array = [])
    {
        if (!empty($role_array)) {
            $this->db->where_in('role_id', array_unique($role_array));
            $this->db->delete('staff_attendence_schedules');
        }
        if (!empty($insert_array)) {
            $this->db->insert_batch('staff_attendence_schedules', $insert_array);
        }
    }

    public function getRoleWiseAttendanceSetting($role_id)
    {
        if($role_id==null ||  $role_id=='select'){
            $sql = "SELECT staff_attendence_schedules.*,roles.name as `role_name` FROM `staff_attendence_schedules` INNER JOIN roles on roles.id=staff_attendence_schedules.role_id order by roles.id";
        }else{
        $sql = "SELECT staff_attendence_schedules.*,roles.name as `role_name` FROM `staff_attendence_schedules` INNER JOIN roles on roles.id=staff_attendence_schedules.role_id WHERE  roles.name=" . $this->db->escape($role_id)."  order by roles.id";
        }
        $query = $this->db->query($sql);        
        return $query->result_array();
    }

}
