<?php

class Leavetypes_model extends MY_model
{

    public function __construct()
    {
        $this->current_date = $this->setting_model->getDateYmd();
    }

    public function addLeaveType($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('leave_types', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Leave Types id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('leave_types', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Leave Types id " . $insert_id;
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

    public function getAllleavetypesRecord()
    {
        $this->datatables
            ->select('leave_types.*')
            ->searchable('leave_types.type')
            ->orderable('leave_types.type' )
            ->sort('leave_types.id', 'desc')
            ->where('leave_types.is_active', 'yes')
            ->from('leave_types');
        return $this->datatables->generate('json');
    }

    public function getLeaveType()
    {
        $query = $this->db->get('leave_types');
        return $query->result_array();
    }

    public function deleteLeaveType($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('leave_types');
        $message = DELETE_RECORD_CONSTANT . " On Leave Types id " . $id;
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

    public function valid_leave_type($str)
    {
        $type = $this->input->post('type');
        $id   = $this->input->post('leavetypeid');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_data_exists($type, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_data_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'type' => $name);
            $query = $this->db->where($data)->get('leave_types');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {

            $this->db->where('type', $name);
            $query = $this->db->get('leave_types');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

}
