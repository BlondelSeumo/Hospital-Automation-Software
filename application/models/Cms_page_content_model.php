<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cms_page_content_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->config('ci-blog');
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($id = null)
    {
        $this->db->select()->from('front_cms_page_contents');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getContentByPage($page_id = null)
    {
        $this->db->select()->from('front_cms_page_contents');
        $this->db->where('page_id', $page_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('front_cms_page_contents');
        
        $message = DELETE_RECORD_CONSTANT . " On Front Cms Page Contents id " . $id;
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

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('front_cms_page_contents', $data);            
            $message = UPDATE_RECORD_CONSTANT . " On Front Cms Page Contents id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
            
        } else {
            $this->db->insert('front_cms_page_contents', $data);            
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Front Cms Page Contents id " . $insert_id;
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
                return $insert_id;
            }            
    }

    public function batch_insert($data)
    {
        $this->db->insert_batch('front_cms_page_contents', $data);
    }

    public function insertOrUpdate($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $this->db->where('page_id', $data['page_id']);
        $q = $this->db->get('front_cms_page_contents');
        if ($q->num_rows() > 0) {
            $this->db->where('page_id', $data['page_id']);
            $this->db->update('front_cms_page_contents', $data);            
            $message = UPDATE_RECORD_CONSTANT . " On Front Cms Page Contents id " . $data['page_id'];
            $action = "Update";
            $record_id = $data['page_id'];
            $this->log($message, $record_id, $action);
            
        } else {
            $this->db->insert('front_cms_page_contents', $data);            
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Front Cms Page Contents id " . $insert_id;
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

    public function deleteByPageID($page_id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('page_id', $page_id);
        $this->db->delete('front_cms_page_contents');        
        $message = DELETE_RECORD_CONSTANT . " On Front Cms Page Contents id " . $page_id;
        $action = "Delete";
        $record_id = $page_id;
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
