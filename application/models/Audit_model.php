<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Audit_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();

    }

    public function get($limit = null, $offset = null)
    {
        $this->db->select('logs.*, CONCAT_WS("",staff.name,staff.surname," (",staff.employee_id,")") as name')->from('logs');
        $this->db->join('staff', 'staff.id = logs.user_id');
        $this->db->order_by('logs.id', 'asc');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getAllRecord($start_date = null, $end_date = null)
    {
        $query="  date_format(logs.created_at ,'%Y-%m-%d') >='". $start_date."'and date_format(logs.created_at ,'%Y-%m-%d') <= '".$end_date."'";
        $this->datatables
            ->select('logs.*, CONCAT_WS("",staff.name,staff.surname," (",staff.employee_id,")") as name')
            ->join('staff', 'staff.id = logs.user_id')           
            ->where($query)
            ->searchable('message, name, ip_address, action, platform, agent')
            ->orderable('message, name, ip_address, action, platform, agent')            
            ->sort('logs.id', 'desc')
            ->from('logs');
            
        return $this->datatables->generate('json');
    }

    public function count()
    {
        $query = $this->db->select('count(*) as total')->get('logs')->row_array();
        return $query['total'];
    }

    public function delete($id = null)
    {
        if ($id == null) {
            $this->db->empty_table('logs');
        } else {
            $this->db->where('id', $id)->delete('logs');
        }
    }

}
