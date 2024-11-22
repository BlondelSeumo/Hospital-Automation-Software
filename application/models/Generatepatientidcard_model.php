<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Generatepatientidcard_model extends CI_model {

    function __construct() {
        parent::__construct();
       
    }

    public function getpatientidcard() {
        $this->db->select('*');
        $this->db->from('patient_id_card');
        $query = $this->db->get();
        return $query->result();
    }

    public function getidcardbyid($idcard) {
        $this->db->select('*');
        $this->db->from('patient_id_card');
        $this->db->where('id', $idcard);
        $query = $this->db->get();
        return $query->result();
    }

}

?>