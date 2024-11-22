<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Complaint_model extends MY_Model
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
        
        $this->db->insert('complaint', $data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Complaint id " . $insert_id;
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
            return $insert_id;
        }            
    }

    public function image_add($complaint_id, $image)
    {
        $array = array('id' => $complaint_id);
        $this->db->set('image', $image);
        $this->db->where($array);
        $this->db->update('complaint');
    }

    public function complaint_list($id = null)
    {
        $this->db->select('complaint.*,complaint_type.complaint_type')
        ->join('complaint_type','complaint.complaint_type_id = complaint_type.id')
        ->from('complaint');
        if ($id != null) {
            $this->db->where('complaint.id', $id);
        } else {
            $this->db->order_by('complaint.id', "desc");
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getAllcomplaintRecord()
    {
        $this->datatables
            ->select('complaint.*,complaint_type.complaint_type')
            ->join('complaint_type','complaint_type.id = complaint.complaint_type_id','left')
            ->searchable('complaint.id,complaint_type.complaint_type,complaint.source,complaint.name,complaint.contact,complaint.email,complaint.date,complaint.description,complaint.note')
            ->orderable('complaint.id,complaint_type.complaint_type,complaint.source,complaint.name,complaint.contact,complaint.date,')
            ->sort('complaint.date', 'desc')
            ->from('complaint');
        return $this->datatables->generate('json');
    } 

    public function image_delete($id, $img_name)
    {
        $file = "./uploads/front_office/complaints/" . $img_name;
        unlink($file);
        $this->db->where('id', $id);
        $this->db->delete('complaint');
        $controller_name = $this->uri->segment(2);
    }

    public function compalaint_update($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('complaint', $data);
        
        $message = UPDATE_RECORD_CONSTANT . " On Complaint id " . $id;
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
        $this->db->delete('complaint');
        
        $message = DELETE_RECORD_CONSTANT . " On Complaint id " . $id;
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

    public function getComplaintType()
    {
        $this->db->select('*');
        $this->db->from('complaint_type');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getComplaintSource()
    {
        $this->db->select('*');
        $this->db->from('source');
        $query = $this->db->get();
        return $query->result_array();
    }
}
