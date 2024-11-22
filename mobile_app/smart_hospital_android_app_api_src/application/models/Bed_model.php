<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bed_model extends CI_Model
{

    public function getBedHistory($case_reference_id)
    {
        $result = $this->db->select("bed_group.name as bed_group,bed.name as bed,patient_bed_history.from_date,patient_bed_history.to_date, patient_bed_history.is_active")
            ->join("bed_group", "bed_group.id=patient_bed_history.bed_group_id", "left")
            ->join("bed", "bed.id=patient_bed_history.bed_id", "left")
            ->where("case_reference_id", $case_reference_id)
            ->get("patient_bed_history")
            ->result();            
            foreach($result as $key => $result_value){
                if($result_value->to_date == ''){
                    $result[$key]->to_date = '';
                }
            }
        
        return $result;
    } 

}
