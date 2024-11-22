<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dispatch_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function insert($table, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert($table, $data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On " . $table . " id " . $insert_id;
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
            //return $return_value;
        }
        return $insert_id;
    }

    public function image_add($type, $dispatch_id, $image)
    {        
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $array = array('id' => $dispatch_id, 'type' => $type);
        $this->db->set('image', $image);
        $this->db->where($array);
        $this->db->update('dispatch_receive');
        $message = UPDATE_RECORD_CONSTANT . " On Dispatch Receive id " . $dispatch_id;
        $action = "Update";
        $record_id = $dispatch_id;
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

    public function dispatch_list()
    {
        $this->db->select('*');
        $this->db->where('type', 'dispatch');
        $this->db->from('dispatch_receive');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    public function getAlldispatchRecord()
    {
        $this->datatables
            ->select('dispatch_receive.*')
            ->searchable('dispatch_receive.id,dispatch_receive.reference_no,dispatch_receive.to_title,dispatch_receive.address,dispatch_receive.note,dispatch_receive.from_title,dispatch_receive.date,dispatch_receive.type')
            ->orderable('dispatch_receive.from_title,dispatch_receive.reference_no,dispatch_receive.to_title,dispatch_receive.date')
            ->sort('dispatch_receive.date', 'desc')
            ->where('type', 'dispatch')
            ->from('dispatch_receive');
        return $this->datatables->generate('json');
    } 
    
    public function receive_list()
    {
        $this->db->select('*');
        $this->db->where('type', 'receive');
        $this->db->order_by('id', 'desc');
        $this->db->from('dispatch_receive');
        $query = $this->db->get();
        return $query->result();
    }

    public function getAllreceiveRecord()
    {
        $this->datatables
            ->select('dispatch_receive.*')
            ->searchable('dispatch_receive.id,dispatch_receive.reference_no,dispatch_receive.to_title,dispatch_receive.address,dispatch_receive.note,dispatch_receive.from_title,dispatch_receive.date,dispatch_receive.type')
            ->orderable('dispatch_receive.from_title,dispatch_receive.reference_no,dispatch_receive.to_title,dispatch_receive.address,dispatch_receive.note,dispatch_receive.date')
            ->sort('dispatch_receive.date', 'desc')
            ->where('type', 'receive')
            ->from('dispatch_receive');
        return $this->datatables->generate('json');
    }     

    public function dis_rec_data($id, $type)
    {
        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $this->db->from('dispatch_receive');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function recevie_data($id)
    {
        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->from('dispatch_receive');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function update_dispatch($table, $id, $type, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $this->db->update($table, $data);
        
        $message = UPDATE_RECORD_CONSTANT . " On "  . $table . " id " . $id;
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

    public function image_update($type, $id, $img_name)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->set('image', $img_name);
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $this->db->update('dispatch_receive');
        
        $message = UPDATE_RECORD_CONSTANT . " On Dispatch Receive id " . $id;
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

    public function image_delete($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if($data['image']!=null){
        $file = "./uploads/front_office/dispatch_receive/" . $data['image'];
        unlink($file);
        }

        $this->db->where('id', $data['id']);
        $this->db->delete('dispatch_receive');
        
        $message = DELETE_RECORD_CONSTANT . " On Dispatch Receive id " . $data['id'];
        $action = "Delete";
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

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('dispatch_receive');
        $controller_name = $this->uri->segment(2);
        
        $message = DELETE_RECORD_CONSTANT . " On Dispatch Receive id " . $id;
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
