<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Timeline_model extends CI_Model
{

    public function getPatientTimeline($id)
    {        
        $this->db->where("status", "yes");        
        $query = $this->db->where("patient_id", $id)->order_by("timeline_date", "desc")->get("patient_timeline");
        return $query->result_array();
    }
    
    public function add_patient_timeline($data)
    {         
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("patient_timeline", $data);
            $record_id = $data["id"];
        } else {
            $this->db->insert("patient_timeline", $data);
            $record_id = $this->db->insert_id();            
        }   
        return $record_id;
       
    }   
    
    public function deleteopdtimeline($id)
    {
        $query = $this->db->where("id", $id)->delete("patient_timeline"); 
        if ($this->db->affected_rows() > 0) {
            return $json_array = array('status' => 'success', 'error' => '', 'message' => 'Data Deleted Successfull');
        } else {
            return $json_array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
    }
    
}
