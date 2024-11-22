<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('users', $data);            
            $record_id=$record_id;            
        } else {
            $this->db->insert('users', $data);
            $record_id = $this->db->insert_id();            
        }
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

    public function updateUser($id, $status)
    {
        $query     = $this->db->select("users.id")->where("user_id", $id)->get("users");
        $result    = $query->row_array();            
        $user_data['is_active'] = $status;       
        $this->db->where('user_id', $id);
        $this->db->update('users', $user_data);
    }   

    public function checkLogin($data)
    {
        $this->db->select('id, username, password,role,is_active');
        $this->db->from('users');
        $this->db->where('username', $data['username']);
        $this->db->where('password', ($data['password']));
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }   

    public function read_user_information($users_id)
    {
        $this->db->select('users.*,patients.dob,patients.patient_name,patients.image,patients.guardian_name,patients.patient_type,languages.id as lang_id,languages.language,patients.gender,patients.mobileno,patients.email');
        $this->db->from('users');
        $this->db->join('patients', 'patients.id = users.user_id');
        $this->db->join('languages', 'patients.lang_id = languages.id', 'left');
        $this->db->where('users.id', $users_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function checkOldUsername($data)
    {
        $this->db->where('id', $data['user_id']);
        $this->db->where('username', $data['username']);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkOldPass($data)
    {
        $this->db->where('id', $data['user_id']);
        $this->db->where('password', $data['current_pass']);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkUserNameExist($data)
    {
        $this->db->where('role', $data['role']);
        $this->db->where('username', $data['new_username']);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }  

    public function changeStatus($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $this->db->where('id', $data['id']);
        $this->db->update('users', $data);
        
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }      
    }  
    
    public function read_user()
    {
        $this->db->select('*');
        $this->db->from('users');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }   

    public function getUserByEmail($table, $role, $email)
    {
        $this->db->select($table . '.*,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`');
        $this->db->from($table);
        $this->db->join('users', 'users.user_id = ' . $table . '.id', 'left');
        $this->db->where('users.role', $role);
        if ($role == 'patient') {
            $this->db->where($table . '.email', $email);
        } 
        $query = $this->db->get();
        if ($email != null) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function getUserValidCode($table, $role, $code)
    {
        $this->db->select($table . '.*,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`');
        $this->db->from($table);
        $this->db->join('users', 'users.user_id = ' . $table . '.id', 'left');
        $this->db->where('users.role', $role);
        $this->db->where('users.verification_code', $code);
        $query = $this->db->get();
        if ($code != null) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function forgotPassword($usertype, $email)
    {
        $result = false;
        if ($usertype == 'patient') {
            $table  = "patients";
            $role   = "patient";
            $result = $this->getUserByEmail($table, $role, $email);
        }
        return $result;
    }

    public function getUserByCodeUsertype($usertype, $code)
    {
        $result = false;
        if ($usertype == 'patient') {
            $table  = "patients";
            $role   = "patient";
            $result = $this->getUserValidCode($table, $role, $code);
        }
        return $result;
    }

    public function reset_password($usertype, $code, $password)
    {
        echo "string";
        exit();
    }

}
