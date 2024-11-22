<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Prefix_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        $this->db->select()->from('prefixes');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id asc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }

    public function getPrefixArray()
    {
        $this->db->select()->from('prefixes');
        $this->db->order_by('id asc');
        $query        = $this->db->get();
        $prefix_array = $query->result();
        $return_array = array();
        if (!empty($prefix_array)) {
            foreach ($prefix_array as $prefix_key => $prefix_value) {
                $return_array[$prefix_value->type] = $prefix_value->prefix;
            }
        }
        return $return_array;
    }

    public function getByCategory($category = array())
    {
        $this->db->select()->from('prefixes');
        if (!empty($category)) {
            $this->db->where_in('type', $category);
            $this->db->order_by('id asc');
            $query = $this->db->get();
            return $query->result();
        }
        return false;
    }

    public function update($data)
    {
        $this->db->update_batch('prefixes', $data, 'type');
        return true;
    }

}
