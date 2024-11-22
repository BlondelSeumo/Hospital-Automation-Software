<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Specialist_model extends MY_model
{

    public function valid_specialist($str)
    {
        $type = $this->input->post('type');
        $id   = $this->input->post('specialisttypeid');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_specialist_exists($type, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function getall()
    {
        $this->datatables->select('id,specialist_name,is_active');
        $this->datatables->from('specialist');
        if ($this->rbac->hasPrivilege('specialist', 'can_edit')) {
            $edit = '<a onclick="get($1)" class="btn btn-default btn-xs" data-target="#editmyModal" data-toggle="tooltip" title="" data-original-title=' . $this->lang->line('edit') . '> <i class="fa fa-pencil"></i></a>';
        } else {
            $edit = '';
        }

        if ($this->rbac->hasPrivilege('specialist', 'can_delete')) {
            $delete = '<a  class="btn btn-default btn-xs" onclick="deleterecord($1)" data-toggle="tooltip" title=""  data-original-title=' . $this->lang->line('delete') . '><i class="fa fa-trash"></i></a>';
        } else {
            $delete = '';
        }

        $this->datatables->add_column('view', $edit . $delete, 'id,is_active');
        return $this->datatables->generate();
    }

    public function check_specialist_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'specialist_name' => $name);
            $query = $this->db->where($data)->get('specialist');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('specialist_name', $name);
            $query = $this->db->get('specialist');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function deletespecialist($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("specialist");
        $message = DELETE_RECORD_CONSTANT . " On Specialist id " . $id;
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

    public function getspecialistType($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('specialist');
            return $query->row_array();
        } else {
            $query = $this->db->get("specialist");
            return $query->result_array();
        }
    }

    public function getAllspecialistRecord()
    {
        $this->datatables
            ->select('specialist.*')
            ->searchable('specialist.specialist_name')
            ->orderable('specialist.specialist_name' )
            ->sort('specialist.id', 'desc')
            ->where('specialist.is_active', 'yes')
            ->from('specialist');
        return $this->datatables->generate('json');
    }

    public function addspecialistType($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('specialist', $data);           
            $message = UPDATE_RECORD_CONSTANT . " On Specialist id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);            
        } else {
            $this->db->insert('specialist', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Specialist id " . $insert_id;
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
}
