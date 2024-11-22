<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Casereference_model extends CI_Model
{
    public function getPatientByCase($case_id)
    {
        $sql = "SELECT patient_id,patients.patient_name
    FROM (
    SELECT patient_id FROM ipd_details WHERE case_reference_id =$case_id
    UNION
    SELECT patient_id FROM opd_details WHERE case_reference_id =$case_id
      ) as case_patient INNER JOIN patients on case_patient.patient_id=patients.id";
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function get($caseid){
		$sql="select case_reference_id as `id` from (SELECT case_reference_id FROM `ipd_details` UNION SELECT case_reference_id FROM `opd_details`) t WHERE case_reference_id like '".$this->db->escape_str($caseid)."%'ORDER by case_reference_id ASC";
		$query = $this->db->query($sql);
        return $query->result_array();
    }

}
