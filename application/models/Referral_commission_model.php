<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Referral_commission_model extends MY_Model
{

    public function get_commission()
    {
        $this->db->select('category.*')->from('referral_category category');
        $query             = $this->db->get();
        $referral_category = $query->result();

        if (!empty($referral_category)) {
            foreach ($referral_category as $referral_category_key => $referral_category_value) {
                $referral_category[$referral_category_key]->referral_commission = $this->get_referral_commission($referral_category_value->id);
            }
        }
        return $referral_category;
    }

    public function get_referral_commission($id)
    {
        $commission = $this->db->select("type.name, commission.commission")
        ->join("referral_type type", "commission.referral_type_id = type.id")
        ->where('referral_category_id', $id)->order_by("referral_type_id")
        ->get("referral_commission commission")->result();
        return $commission;
    }

    public function add($commission)
    {        
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('referral_commission', $commission);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Referral Commission id " . $insert_id;
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

    public function get_by_category($category_id)
    {
        $commission = $this->db->select()
        ->where('referral_category_id', $category_id)
        ->order_by("referral_type_id")
        ->get("referral_commission")->result_array();
        return $commission;
    }

    public function get($id)
    {
        $commission = $this->db->select()->where('id', $id)->get("referral_commission")->row_array();
        return $commission;
    }

    public function update($commission)
    {
        $this->db->trans_begin();
        $this->db->where("referral_category_id", $commission["referral_category_id"]
        )->where("referral_type_id", $commission["referral_type_id"])
        ->update("referral_commission", $commission);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
            return $this->db->insert_id();
        }
    }

    public function delete($category_id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================        
        $this->db->where('referral_category_id', $category_id)->delete('referral_commission');        
        $message = DELETE_RECORD_CONSTANT . " On Referral Commission where Referral Category id " . $category_id;
        $action = "Delete";
        $record_id = $category_id;
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

    public function get_type_by_category($category_id)
    {
        $commission = $this->db->select("referral_type_id")
        ->where('referral_category_id', $category_id)
        ->order_by("referral_type_id")
        ->get("referral_commission")->result_array();
        return $commission;
    }

}
