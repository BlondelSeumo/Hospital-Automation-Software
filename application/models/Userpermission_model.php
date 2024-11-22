<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Userpermission_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getUserPermission($user_id = null)
    {
        $where = "";
        $query = "SELECT permissions.*,IF(user_permissions.id > 0,1,0) as `user_permissions_id` FROM permissions left JOIN user_permissions on user_permissions.permission_id=permissions.id and user_permissions.staff_id=$user_id";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getInsertBatch($insert_array, $staff_id, $delete_arrary = array())
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        if (!empty($insert_array)) {
            $this->db->insert_batch('user_permissions', $insert_array);
        }
        
        if (!empty($delete_arrary)) {
            $this->db->where('staff_id', $staff_id);
            $this->db->where_in('permission_id', $delete_arrary);
            $this->db->delete('user_permissions');
            
            $message = DELETE_RECORD_CONSTANT . " On User Permissions where staff_id " . $staff_id;
            $action = "Delete";
            $record_id = $id;
            $this->log($message, $record_id, $action);        
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $staff_id;
        }
    }

}
