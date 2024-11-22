<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Blooddonor_model extends MY_Model
{

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('blood_donor', $data);
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " For Blood Donor id " . $insert_id;
        $action    = "Insert";
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
            //return $return_value;
        }
        return $insert_id;
    }

    public function searchFullText()
    {
        $query = $this->db->order_by('id', 'desc')->get('blood_donor');
        return $query->result_array();
    }

    public function getAlldonorRecord()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('donor', 1);
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'blood_donor.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $this->datatables
            ->select('blood_donor.*,blood_bank_products.name as blood_group,blood_bank_products.id as blood_bank_product_id' . $field_variable)
            ->join('blood_bank_products', 'blood_bank_products.id=blood_donor.blood_bank_product_id')
            ->searchable('blood_donor.donor_name,blood_donor.gender,blood_donor.contact_no,blood_donor.father_name,blood_donor.address' . $field_variable)
            ->orderable('blood_donor.donor_name,blood_donor.date_of_birth,blood_bank_products.name,blood_donor.gender,blood_donor.contact_no,blood_donor.father_name,blood_donor.address' . $custom_field_column)
            ->sort('blood_donor.id', 'desc')
            ->from('blood_donor');
        return $this->datatables->generate('json');

    }

    public function getDetails($id)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('donor');
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'blood_donor.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $this->db->select('blood_donor.*,blood_bank_products.id as blood_group,blood_bank_products.name as blood_group_name' . $field_variable);
        $this->db->join('blood_bank_products', 'blood_donor.blood_bank_product_id=blood_bank_products.id');
        $this->db->where('blood_donor.id', $id);
        $query = $this->db->get('blood_donor');
        return $query->row_array();
    }

    public function update($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('blood_donor', $data);
            $message   = UPDATE_RECORD_CONSTANT . " For Blood Donor id " . $data['id'];
            $action    = "Update";
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
    }

    public function delete($id)
    {
        $this->db->where("id", $id)->delete('blood_issue');
    }

    public function getBloodBank($id = null)
    {
        $query = $this->db->get('blood_donor');
        return $query->result_array();
    }

    public function deleteBloodDonor($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("blood_donor_id", $id)->delete("blood_donor_cycle");
        $this->db->where("id", $id)->delete("blood_donor");
        $message   = DELETE_RECORD_CONSTANT . " Where  Blood Donor id " . $id;
        $action    = "Delete";
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
            //return $return_value;
        }
    }

    public function getDonorBloodgroup($donor_id)
    {
        $query = $this->db->where("blood_donor.id", $donor_id)->get("blood_donor");
        return $query->row_array();
    }

    public function getBloodDonor($blood_group_id = null)
    {
        $this->db->select("blood_donor.id,donor_name,blood_bank_products.name as blood_group")->join('blood_bank_products', 'blood_bank_products.id=blood_donor.blood_bank_product_id');
        if ($blood_group_id !== null) {
            $this->db->where('blood_donor.blood_bank_product_id', $blood_group_id);
        }

        $result = $this->db->get("blood_donor")->result_array();
        return $result;
    }

}
