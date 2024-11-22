<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vital_model extends CI_Model
{
	public function __construct()
    {
        parent::__construct();
        $CI = &get_instance();
         
    }
	
	public function getvitallist()
    {
        $this->db->select('vitals.*');
        $query = $this->db->get('vitals');
        return $query->result_array();
    }
	
	public function getpatientvitaldate($patient_id,$vital_id)
    {
        $this->db->select('patients_vitals.reference_range as patient_range,patients_vitals.id as id,patients_vitals.messure_date');
        $this->db->from("patients_vitals");         
        $this->db->where('patients_vitals.patient_id', $patient_id);
        $this->db->where('patients_vitals.vital_id', $vital_id);
        $this->db->group_by("date_format(patients_vitals.messure_date,'%Y-%m-%d')");
		$this->db->order_by("patients_vitals.messure_date","desc");
        $query = $this->db->get();
        return $query->result_array();
    }	
   
	public function getpatientsvitaldetails($patient_id,$vital_id,$messure_date)
    {
        $this->db->select('patients_vitals.reference_range as patient_range,patients_vitals.id as id,patients_vitals.messure_date');
        $this->db->from("patients_vitals");
        $this->db->join("vitals","vitals.id=patients_vitals.vital_id");
        $this->db->where('patients_vitals.patient_id', $patient_id);
        $this->db->where('patients_vitals.vital_id', $vital_id);
        $this->db->like('patients_vitals.messure_date', $messure_date);		
        $this->db->order_by("vital_id","desc");
        $query = $this->db->get();
        return $query->row_array();
    }	
   
	public function getpatientsvital($patient_id)
    {
        $this->db->select('vitals.id as vital_id,patients_vitals.reference_range as patient_range,patients_vitals.id as id,patients_vitals.messure_date');
        $this->db->from("patients_vitals");
        $this->db->join("vitals","vitals.id=patients_vitals.vital_id");
        $this->db->where('patients_vitals.patient_id', $patient_id);		
        $this->db->order_by("vital_id","desc");
        $query = $this->db->get();
        return $query->result_array();
    }
	
	public function getcurrentvitals($id)
    {        		
		$query = $this->db->query("select vitals.*,patients_vitals.reference_range as patient_range,patients_vitals.id as idd,patients_vitals.messure_date,patients_vitals.vital_id as patient_vital_id from  patients_vitals join  vitals on vitals.id = (SELECT MAX(patients_vitals.vital_id) FROM patients_vitals p2 WHERE p2.vital_id = vitals.id ) where patients_vitals.patient_id = '".$id."' group by patient_vital_id");   
       
        return $query->result_array();
    }
    
}
