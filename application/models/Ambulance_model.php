<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ambulance_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }  

    public function totalPatientAmbulance($patient_id)
    {
        $query = $this->db->select('count(ambulance_call.patient_id) as total')
            ->where('patient_id', $patient_id)
            ->get('ambulance_call');
        return $query->row_array();
    }

    public function getpatientAmbulanceYearCounter($patient_id,$year)
    {
		$sql= "SELECT count(*) as `total_visits`,Year(date) as `year` FROM `ambulance_call` WHERE YEAR(date) >= ".$this->db->escape($year)." AND patient_id=".$this->db->escape($patient_id)." GROUP BY  YEAR(date)";
		$query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getAmbulanceBillAmountByCaseId($case_id)
    {
		$sql= "SELECT sum(net_amount) as `net_amount`,IFNULL((SELECT sum(amount) FROM `transactions` WHERE ambulance_call_id in (SELECT ambulance_call.id FROM `ambulance_call` WHERE case_reference_id=".$this->db->escape($case_id).") and section = 'Ambulance'),0) as `paid_amount` FROM `ambulance_call` WHERE case_reference_id=".$this->db->escape($case_id);
        $query = $this->db->query($sql);
        return $query->row();
    }  

    public function getambulanceByCaseId($case_id)
    {
         $query =  $this->db         
            ->select("ambulance_call.id, vehicles.vehicle_no,ambulance_call.net_amount, ambulance_call.date,IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.ambulance_call_id=ambulance_call.id),0) as `amount_paid`")
            ->join("vehicles", "ambulance_call.vehicle_id=vehicles.id")
            ->where("ambulance_call.case_reference_id", $case_id)
            ->get("ambulance_call");
          return $query->result();
    }

}
