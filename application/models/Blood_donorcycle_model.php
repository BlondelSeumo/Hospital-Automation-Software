<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Blood_donorcycle_model extends MY_Model
{

    public function add($data, $transaction_data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //======================Code Start==============================
        $this->db->insert('blood_donor_cycle', $data);

        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On Blood Donor  id " . $insert_id;
        $action    = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        if (!empty($transaction_data)) {
            $transaction_data['blood_donor_cycle_id'] = $insert_id;
            $this->transaction_model->add($transaction_data);
            $transation_id = $this->db->insert_id();
        }

        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
        return $insert_id;

    }

    public function getDonorBloodBatch($blood_donor_id)
    {
        $this->db->select('blood_donor_cycle.*, blood_donor.id as blood_donor_id, blood_donor.created_at as donate_date,charge_units.unit as unit_name,charge_categories.name as charge_category_name,charges.charge_category_id,charges.standard_charge,charges.name as `charge_name`,transactions.amount,`transactions`.`attachment`,`transactions`.`attachment_name`,`transactions`.`payment_mode`,`transactions`.`cheque_no`,`transactions`.`cheque_date`,`transactions`.`payment_date`,transactions.id as tran_id');
        $this->db->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id', 'inner');
        $this->db->join('charge_units', 'blood_donor_cycle.unit = charge_units.id', 'left');
        $this->db->join('charges', 'blood_donor_cycle.charge_id = charges.id', 'inner');
        $this->db->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner');
        $this->db->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id');
        $this->db->join("transactions", 'transactions.blood_donor_cycle_id = blood_donor_cycle.id');
        $this->db->where('blood_donor.id', $blood_donor_id);
        $query = $this->db->get('blood_donor_cycle');
        return $query->result();        
    }

    public function getBatchByBloodGroup($blood_bank_product_id)
    {
        $this->db->select('blood_donor_cycle.*, blood_donor.id as blood_donor_id,blood_donor.donor_name,blood_donor.gender,charge_categories.name as charge_category_name,charges.charge_category_id,charges.standard_charge,charges.name as `charge_name`,charge_units.unit as `charge_unit`');
        $this->db->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id', 'inner');
        $this->db->join('charges', 'blood_donor_cycle.charge_id = charges.id', 'inner');
        $this->db->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner');
        $this->db->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id');
        $this->db->join('charge_units', 'blood_donor_cycle.unit = charge_units.id', "left");
        $this->db->where('blood_donor_cycle.available', 1);
        $this->db->where('blood_donor.blood_bank_product_id', $blood_bank_product_id);
        $query = $this->db->get('blood_donor_cycle');
        return $query->result();
    }

    public function get_componentBybloodId($blood_groupid)
    {
        $sql = "select blood_bank_products.* from blood_donor inner join blood_donor_cycle on blood_donor.id=blood_donor_cycle.blood_donor_id join blood_donor_cycle as bcd on blood_donor_cycle.id=bcd.blood_donor_cycle_id join blood_bank_products on blood_bank_products.id=bcd.blood_bank_product_id where  blood_donor.blood_bank_product_id=" . $this->db->escape($blood_groupid) . " group by blood_bank_products.id ORDER BY blood_bank_products.`id` ASC";
        $query = $this->db->query($sql);        
        return $query->result_array();
    }

    public function deleteCycle($id)
    {
        $this->db->where("id", $id)->delete('blood_donor_cycle');
    }

    public function valid_check_exists($str)
    {
        $bag_no = $this->input->post('bag_no');
        $id     = $this->input->post('id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_data_exists($bag_no, $id)) {
            $this->form_validation->set_message('check_exists', 'Bag No already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_data_exists($bag_no, $id)
    {
        $this->db->where('bag_no', $bag_no);
        $this->db->where('id !=', $id);
        $query = $this->db->get('blood_donor_cycle');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getBloodGroupData($id)
    {
        $result = $this->db->select("blood_donor_cycle.id as bag_id,blood_donor_cycle.bag_no,blood_donor_cycle.lot,blood_donor_cycle.volume,charge_units.unit,blood_donor_cycle.institution,blood_bank_products.id as product_id")
            ->join("blood_donor", "blood_donor.id = blood_donor_cycle.blood_donor_id", "left")
            ->join("blood_bank_products", "blood_bank_products.id = blood_donor.blood_bank_product_id", "left")
            ->join("charge_units", "blood_donor_cycle.unit = charge_units.id", "left")
            ->where(array("blood_bank_products.id" => $id, "available" => 1))
            ->get("blood_donor_cycle")
            ->result_array();
        return $result;
    }
 
    public function getComponentBagNosIssue($blood_bank_product_id,$component_blood_bank_product_id)
    {
        $this->db->select('blood_donor_cycle.*');
        $this->db->join('blood_donor_cycle as bg','bg.id=blood_donor_cycle.blood_donor_cycle_id','inner');
        $this->db->join('blood_donor','bg.blood_donor_id=blood_donor.id','inner');
        $this->db->where('blood_donor_cycle.available', 1);
        $this->db->where('blood_donor.blood_bank_product_id', $blood_bank_product_id);
        $this->db->where('blood_donor_cycle.blood_bank_product_id', $component_blood_bank_product_id);
        $query = $this->db->get('blood_donor_cycle');
        return $query->result();
    }

    public function getBloodComponentData($id)
    {
        $result = $this->db->select("bda.*,blood_bank_products.name,blood_bank_products.id as product_id,charge_units.unit,blood_donor.blood_bank_product_id as bloodid")
            ->join("blood_bank_products", "blood_bank_products.id = bda.blood_bank_product_id", "left")
            ->join("blood_donor_cycle bdb", "bda.blood_donor_cycle_id=bdb.id", "left")
            ->join("blood_donor", "blood_donor.id = bdb.blood_donor_id", "left")
            ->join("charge_units", "bda.unit = charge_units.id", "left")
            ->where(array("blood_donor.blood_bank_product_id" => $id, "bda.available" => 1))
            ->get("blood_donor_cycle bda")
            ->result_array();
        return $result;
    }

}
