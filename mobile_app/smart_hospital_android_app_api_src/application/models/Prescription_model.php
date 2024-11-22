<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Prescription_model extends CI_Model
{
    
    public function getIpdPrescription($ipdid)
    {
        $query = $this->db->select('ipd_prescription_basic.*')
            ->join('ipd_prescription_details', 'ipd_prescription_basic.id = ipd_prescription_details.basic_id','left')
            ->where("ipd_prescription_basic.ipd_id", $ipdid)
            ->group_by("ipd_prescription_basic.id")
            ->get('ipd_prescription_basic');
        return $query->result_array();
    }
    
}
