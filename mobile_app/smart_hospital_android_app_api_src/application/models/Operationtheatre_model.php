<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Operationtheatre_model extends CI_Model {

    public function getipdoperationdetails($ipdid) {
        $this->db->select('operation_theatre.*,operation.operation,operation_category.category,patients.id as pid,patients.patient_name,patients.gender,patients.age,patients.month,patients.patient_type,patients.mobileno,patients.is_active')->from('operation_theatre');
        $this->db->join('opd_details', 'opd_details.id=operation_theatre.opd_details_id', "left");
        $this->db->join('patients', 'patients.id=opd_details.patient_id', "left");
        $this->db->join('operation', 'operation_theatre.operation_id=operation.id', "left");
        $this->db->join("operation_category","operation_category.id=operation.category_id","left");
        $this->db->where('operation_theatre.ipd_details_id', $ipdid);  
        $query  = $this->db->get();
        $result = $query->result_array();         
       
        foreach($result as  $key => $valuee){                  
            $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'operationtheatre'); 
            $result[$key]['customfield'] = $customfield;                     
        }
            
        return $result;        
    }
    
    public function getipdoperationdetail($operation_id) {        
        $this->db->select('operation.*,operation_theatre.*,operation_category.id as category_id,operation_category.category as category_name, staff.name as name, staff.surname as surname, staff.employee_id as employee_id')->from('operation_theatre');
        $this->db->join('operation', 'operation_theatre.operation_id=operation.id', "left");
        $this->db->join("operation_category","operation_category.id=operation.category_id","left");        
        $this->db->join('staff', 'staff.id=operation_theatre.consultant_doctor', "left");        
        $this->db->where('operation_theatre.id', $operation_id);
        $query = $this->db->get();
        return $query->row_array();         
    }
}
?>