<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class customfield_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getcustomfields($belongs_to)
    {
        $this->db->from('custom_fields');
        $this->db->where('belong_to', $belongs_to);      
        $this->db->where('visible_on_patient_panel', 1);      
        $this->db->order_by("custom_fields.weight", "asc");
        $query = $this->db->get();
        return $query->result_array();
    }   
    
    public function getcustomfieldswithvalue($opdid,$belongs_to)
    {    
        $customfieldslist = $this->customfield_model->getcustomfields($belongs_to); 
        if(!empty($customfieldslist)){
            foreach($customfieldslist as  $key => $value){             
                $result1[$key]['fieldname']  =  $value['name'];
                $resultadata = $this->customfield_model->chackdata($value['id'],$opdid);
 
                if(!empty($resultadata)){
                    $result1[$key]['fieldvalue']  =  $resultadata->field_value;
                }else{
                    $result1[$key]['fieldvalue']  =   '';
                }                
            }             
            
            return $result1;    
        } else{
            return array(); 
        }       
    }
    
    public function chackdata($belongs_to,$opdid)
    {        
        $this->db->select('custom_field_values.field_value');
        $this->db->from('custom_field_values');
        $this->db->where('custom_field_values.custom_field_id', $belongs_to); 
        $this->db->where('custom_field_values.belong_table_id', $opdid);       
        $query = $this->db->get();
        $result = $query->row();            
        return $result;        
    }    

}
