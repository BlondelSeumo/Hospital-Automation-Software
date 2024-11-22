<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Captcha_model extends MY_Model
{

    public function getSetting()
    {
        $this->db->select('*');
        $this->db->from('captcha');
        $query = $this->db->get();
        return $query->result();
    }

    public function getStatus($name)
    {
        $this->db->select('*');
        $this->db->where("name", $name);
        $this->db->from('captcha');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function update_status($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('name', $data["name"]);
        $this->db->update('captcha', $data);
        $message = UPDATE_RECORD_CONSTANT . " On Captcha where name " . $data['name'];
        $action = "Update";
        $record_id = $data['name'];
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

}
