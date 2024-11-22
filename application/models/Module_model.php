<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Module_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getPermission()
    {
        $query = $this->db->where("system", 0)->order_by('sort_order', 'asc')->get("permission_group");
        return $query->result_array();
    }

    public function getPatientPermission()
    {
        $query = $this->db->where("system", 0)->order_by('sort_order', 'asc')->get("permission_patient");
        return $query->result_array();
    }

    public function changeStatus($data, $data_patient)
    {
        $this->db->trans_start();
        $this->db->where("short_code", $data["short_code"])->update("permission_group", $data);
        $this->db->where("permission_group_short_code", $data_patient["permission_group_short_code"])->update("permission_patient", $data_patient);
        $this->db->trans_complete();
    }
    
    public function changePatientStatus($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $data["id"])->update("permission_patient", $data);
        $message = UPDATE_RECORD_CONSTANT . " On Permission Patient id " . $data['id'];
        $action = "Update";
        $record_id = $data['id'];
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

    public function getPermissionByModulename($module_name)
    {
        $sql   = "select is_active from permission_group where short_code='" . $module_name . "'";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function get($id = null)
    {
        $this->db->select()->from('permission_group');
        if ($id != null) {
            $this->db->where('permission_group.id', $id);
        } else {
            $this->db->order_by('permission_group.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result();
        }
    }

    public function getPatientModule($id = null)
    {
        $this->db->select()->from('permission_patient');
        if ($id != null) {
            $this->db->where('permission_patient.id', $id);
        } else {
            $this->db->order_by('permission_patient.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result();
        }
    }

}
