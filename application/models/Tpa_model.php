<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tpa_model extends MY_Model
{ 

    public function add($data)
    {
        $this->db->insert_batch("organisations_charges", $data);
    }

    public function addcharge($data)
    {
        $this->db->insert_batch("tpa_doctorcharges", $data);
    }

    public function charge($org_id, $ch_type)
    {
        $sql   = "SELECT * FROM charges WHERE id not in (SELECT charge_id from `organisations_charges` WHERE organisations_charges.org_id = " . $org_id . " ) and charges.charge_type = '" . $ch_type . "'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function org_charge($org_id, $charge_type_master_id)
    {
        $this->db->select('organisations_charges.*, charges.description, charges.standard_charge,charges.charge_category_id, charges.name as charge_name,charge_categories.name as charge_category');
        $this->db->join('charges', 'charges.id=organisations_charges.charge_id', 'inner');
        $this->db->join('charge_categories', 'charge_categories.id=charges.charge_category_id', 'inner');
        $this->db->join('charge_type_master', 'charge_type_master.id=charge_categories.charge_type_id', 'inner');
        $this->db->where('charge_type_master.id', $charge_type_master_id);
        $this->db->where('organisations_charges.org_id', $org_id);
        $query = $this->db->get('organisations_charges');
        return $query->result_array();
    }

    public function org_chargedatatable($org_id, $charge_type_master_id)
    {         
        if($charge_type_master_id != '' ){
        $this->datatables->where('charge_type_master.id', $charge_type_master_id);
        }
        $this->datatables
            ->select('organisations_charges.*, charges.description, charges.standard_charge,charges.charge_category_id, charges.name as charge_name,charge_categories.name as charge_category,charge_type_master.charge_type')
        ->from('organisations_charges')
        ->join('charges', 'charges.id=organisations_charges.charge_id', 'inner')
        ->join('charge_categories', 'charge_categories.id=charges.charge_category_id', 'inner')
        ->join('charge_type_master', 'charge_type_master.id=charge_categories.charge_type_id', 'left')
        ->where('organisations_charges.org_id', $org_id)
       ->searchable('charge_type_master.charge_type,charge_categories.name,charges.name,charges.description,standard_charge,org_charge')
        ->orderable('charge_type_master.charge_type,charge_categories.name,charges.name,charges.description,standard_charge,org_charge');
        return $this->datatables->generate('json'); 
    }

    public function get_org_charge($id)
    {
        $this->db->select('charges.*,organisations_charges.id as org_charge_id, organisations_charges.org_charge,`charge_type_master`.`charge_type`,charge_categories.name as charge_category');
        $this->db->join('charges', 'charges.id=organisations_charges.charge_id', 'inner')
        ->join('charge_categories', 'charge_categories.id=charges.charge_category_id', 'inner')
        ->join('charge_type_master', 'charge_type_master.id=charge_categories.charge_type_id', 'left');
        $this->db->where('organisations_charges.id', $id);
        $query = $this->db->get('organisations_charges');
        return $query->row_array();
    }

    public function edit_org($id, $charge)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('organisations_charges', $charge);
        $message = UPDATE_RECORD_CONSTANT . " On Organisations Charges id " . $id;
        $action = "Update";
        $record_id = $id;
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

    public function edit_orgtpa($id, $charge)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('tpa_doctorcharges', $charge);
        $message = UPDATE_RECORD_CONSTANT . " On Tpa Doctor Charges id " . $id;
        $action = "Update";
        $record_id = $id;
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
        $this->db->where('id', $id);
        $this->db->delete('organisations_charges');
        $message = DELETE_RECORD_CONSTANT . " On Organisations Charges id " . $id;
        $action = "Delete";
        $record_id = $id;
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
