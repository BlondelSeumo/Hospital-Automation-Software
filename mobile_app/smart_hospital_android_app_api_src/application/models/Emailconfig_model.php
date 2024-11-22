<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Emailconfig_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getActiveEmail()
    {
        $this->db->select()->from('email_config');
        $this->db->where('is_active', 'yes');
        $query = $this->db->get();
        return $query->row();
    }

}
