<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Referral_person_model extends MY_Model
{
    public function get_person()
    {
        $this->db->select("person.name as name,person.id as person_id,category.name as category_name,category.id as category_id, person.contact, person.person_name, person.person_phone, person.address");
        $this->db->join("referral_category category", "category.id=person.category_id", "left");
        $this->db->order_by("person.id",'desc');
        $query  = $this->db->get("referral_person person");
        $person = $query->result();
        foreach ($person as $key => $value) {
            $person[$key]->commission = $this->getCommission($person[$key]->person_id);
        }
        return $person;
    }

    public function getCommission($person_id)
    {
        $commission = $this->db->select("type.name, commission.commission")
        ->join("referral_type type", "commission.referral_type_id = type.id")
        ->where('commission.referral_person_id', $person_id)
        ->order_by("referral_type_id")
        ->get("referral_person_commission commission")->result();
        return $commission;
    }

    public function add($person)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        $this->db->insert('referral_person', $person);
        
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Referral Person id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
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

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        $this->db->where('id', $id)->delete('referral_person');
        $this->db->where('referral_person_id', $id)->delete("referral_person_commission");
        
        $message1 = DELETE_RECORD_CONSTANT . " On Referral Person id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message1, $record_id, $action);
        
        $message2 = DELETE_RECORD_CONSTANT . " On Referral Person Commission where Referral Person id " . $id;        
        $this->log($message2, $record_id, $action);
        
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

    public function get($id)
    {
        $person               = $this->db->select()->where('id', $id)->get("referral_person")->row_array();
        $person["commission"] = $this->getPersonCommission($person['id']);
        return $person;
    }

    public function getPersonCommission($person_id)
    {
        $commission = $this->db->select("referral_type_id,commission")->where("referral_person_id", $person_id)->get("referral_person_commission")->result_array();
        return $commission;
    }

    public function update($person)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $person['id'])->update("referral_person", $person);

        $message = UPDATE_RECORD_CONSTANT . " On Referral Person id " . $person['id'];
        $action = "Update";
        $record_id = $person['id'];
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

    public function getCategory($person)
    {
        $this->db->select("category_id");
        $this->db->where("id", $person);
        $query  = $this->db->get("referral_person");
        $result = $query->row_array();
        return $result["category_id"];
    }

    public function add_commission($person_commission)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        $this->db->insert("referral_person_commission", $person_commission);        
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Referral Person Commission id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
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

    public function update_person_commission($commission)
    {       
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        $this->db->where("referral_person_id", $commission['referral_person_id'])->where("referral_type_id", $commission["referral_type_id"])->update('referral_person_commission', $commission);
        
        $message = UPDATE_RECORD_CONSTANT . " On Referral Person Commission where Referral Person id " . $commission['referral_person_id'];
        $action = "Update";
        $record_id = $commission['referral_person_id'];
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
