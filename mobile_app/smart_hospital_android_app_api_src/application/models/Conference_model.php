<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Conference_model extends CI_Model
{     

    public function getconfrencebypatient($patient_id = null)
    {
        $this->db->select('conferences.*,patients.id as pid,patients.patient_name,patients.id as patient_unique_id,for_create.name as create_for_name,for_create.surname as create_for_surname,for_create.employee_id as create_for_employee_id,for_create_role.name as create_for_role_name,create_by.name as create_by_name,create_by.surname as create_by_surname,create_by.employee_id as create_by_employee_id,create_by_role.name as create_by_role_name')->from('conferences');
        $this->db->join('patients', 'patients.id = conferences.patient_id');
        $this->db->join('staff as for_create', 'for_create.id = conferences.staff_id');
        $this->db->join('staff_roles', 'staff_roles.staff_id = for_create.id');
        $this->db->join('roles as for_create_role', 'for_create_role.id = staff_roles.role_id');
        $this->db->join('staff as create_by', 'create_by.id = conferences.created_id');
        $this->db->join('staff_roles as staff_create_by_roles', 'staff_create_by_roles.staff_id = create_by.id');
        $this->db->join('roles as create_by_role', 'create_by_role.id = staff_create_by_roles.role_id');
        $this->db->where('conferences.patient_id', $patient_id);
        $this->db->order_by('DATE(`conferences`.`date`)', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function getconfrencebyipd($ipd_id)
    {
        $this->db->select('conferences.*,ipd_details.id as ipdid,patients.patient_name,patients.id as patient_unique_id,for_create.name as create_for_name,for_create.surname as create_for_surname,for_create.employee_id as create_for_employee_id,for_create_role.name as create_for_role_name,create_by.name as create_by_name,create_by.surname as create_by_surname,create_by.employee_id as create_by_employee_id,create_by_role.name as create_by_role_name')->from('conferences');
        $this->db->join('ipd_details', 'conferences.ipd_id = ipd_details.id');
        $this->db->join('patients', 'patients.id = ipd_details.patient_id');
        $this->db->join('staff as for_create', 'for_create.id = conferences.staff_id');
        $this->db->join('staff_roles', 'staff_roles.staff_id = for_create.id');
        $this->db->join('roles as for_create_role', 'for_create_role.id = staff_roles.role_id');
        $this->db->join('staff as create_by', 'create_by.id = conferences.created_id');
        $this->db->join('staff_roles as staff_create_by_roles', 'staff_create_by_roles.staff_id = create_by.id');
        $this->db->join('roles as create_by_role', 'create_by_role.id = staff_create_by_roles.role_id');       
        $this->db->where('conferences.ipd_id', $ipd_id);       
        $this->db->order_by('DATE(`conferences`.`date`)', 'DESC');
        $query = $this->db->get();
        $result =    $query->result();
        
        foreach($result as $key => $value){            
            $return_response = json_decode($value->return_response);
            $join_url   =   $return_response->join_url;
            $result[$key]->join_url = $join_url; 
        }
        return $result;
    }
}
