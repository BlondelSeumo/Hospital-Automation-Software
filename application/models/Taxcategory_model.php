<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
class Taxcategory_model extends MY_Model
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
        if (isset($data['id']) && $data['id']!='') {
            $this->db->where('id', $data['id']);
            $this->db->update('tax_category', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Tax Category id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('tax_category', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Tax Category id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
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
	
    public function valid_tax_category($str)
    {       
        $name = $this->input->post('name');
        if ($this->check_name_exists($name)) {
            $this->form_validation->set_message('check_exists', 'Name already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_name_exists($name)
    {
        $id = $this->input->post("id");
        if ($id != 0) {
            $data  = array('name' => $name, 'id !=' => $id);
            $query = $this->db->where($data)->get('tax_category');

            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('name', $name);
            $query = $this->db->get('tax_category');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
 
    public function getDatatableAllRecord()
    {
        $this->datatables
            ->select('*')
            ->searchable('tax_category.name,tax_category.percentage')
            ->orderable('tax_category.name,tax_category.percentage')
            ->sort('tax_category.id','desc')
            ->from('tax_category');
        return $this->datatables->generate('json');
    } 

    public function get($id=null){
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('tax_category');
            return $query->row_array();
        } else {
            $query = $this->db->get("tax_category");
            return $query->result_array();
        }
    }

    public function delete_taxcategory($id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('tax_category');            
        $message = DELETE_RECORD_CONSTANT . " On Tax Category id " . $id;
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
            return true;
        }           
    }
}
