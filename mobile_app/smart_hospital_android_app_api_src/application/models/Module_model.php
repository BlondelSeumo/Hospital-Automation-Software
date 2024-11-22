<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Module_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }

    public function get($id = null)
    {
        $this->db->select("id,name,short_code,is_active")->from('permission_patient');
        if ($id != null) {
            $this->db->where('permission_patient.id', $id);
        } else {
            $this->db->order_by('permission_patient.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

}
