<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bedgroup_model extends MY_Model
{
    public function get_bedgroup($id = null)
    {
        $this->db->select('bed_group.*,floor.name as floor_name')->from('bed_group')
            ->join('floor', 'bed_group.floor = floor.id');
        if ($id != null) {
            $this->db->where('bed_group.id', $id);
        } else {
            $this->db->order_by('bed_group.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function valid_bed_group($str)
    {
        $name = $this->input->post('name');
        if ($this->check_bed_group_exists($name)) {
            $this->form_validation->set_message('check_exists', $this->lang->line('bed') . " " . $this->lang->line('group') . " " . $this->lang->line('record_already_exists'));
            return false;
        } else {
            return true;
        }
    }

    public function check_bed_group_exists($name)
    {
        if ($name != 0) {
            $data  = array('name' => $name);
            $query = $this->db->where($data)->get('bed_group');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('name', $name);
            $query = $this->db->get('bed_group');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function add_bed_group($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("bed_group", $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Bed Group id " . $data['id'];
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
        } else {
            $this->db->insert("bed_group", $data);

            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Bed Group id " . $insert_id;
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
                return $record_id;
            }
            return $insert_id;
        }
    }

    public function bedgroup_list($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->select("bed_group.*,floor.name as floor_name")->join("floor", "bed_group.floor = floor.id")->where("bed_group.id", $id)->get("bed_group");
            return $query->row_array();
        } else {
            $query = $this->db->select("bed_group.*,floor.name as floor_name")->join("floor", "bed_group.floor = floor.id")->get("bed_group");
            return $query->result_array();
        }
    }

    public function delete_bedgroup($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("bed_group");
        $message   = DELETE_RECORD_CONSTANT . " On bed group id " . $id;
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
            return $record_id;
        }
    }

    public function bedGroupFloor()
    {
        $query = $this->db->select('bed_group.*,floor.id as fid,floor.name as floor_name')
            ->join('floor', 'bed_group.floor = floor.id', "left")
            ->get('bed_group');
        return $query->result_array();
    }
}
