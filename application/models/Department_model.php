<?php

class Department_model extends MY_model {

    public function valid_department($str) {
        $type = $this->input->post('type');
        $id = $this->input->post('departmenttypeid');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_department_exists($type, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function getall() 
	{
        $this->datatables->select('id,department_name,is_active');
        $this->datatables->from('department');
        if ($this->rbac->hasPrivilege('department', 'can_edit')) {
			$edit = '<a onclick="get($1)" class="btn btn-default btn-xs" data-target="#editmyModal" data-toggle="tooltip" title="" data-original-title=' . $this->lang->line('edit') . '> <i class="fa fa-pencil"></i></a>';            
        }else{
            $edit= '';
        }

        if ($this->rbac->hasPrivilege('department', 'can_delete')) {
			$delete = '<a  class="btn btn-default btn-xs" onclick="deleterecord($1)" data-toggle="tooltip" title=""  data-original-title=' . $this->lang->line('delete') . '><i class="fa fa-trash"></i></a>';
        }else{
			$delete = '';
        }

        $this->datatables->add_column('view', $edit.$delete, 'id,is_active');
        return $this->datatables->generate();
    }

    public function getAlldepartmentRecord()
    {
        $this->datatables
            ->select('department.*')
            ->searchable('department.department_name')
            ->orderable('department.department_name' )
            ->sort('department.id', 'desc')
            ->where('department.is_active', 'yes')
            ->from('department');
        return $this->datatables->generate('json');
    }

    function check_department_exists($name, $id) {
        if ($id != 0) {
            $data = array('id != ' => $id, 'department_name' => $name);
            $query = $this->db->where($data)->get('department');
            if ($query->num_rows() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            $this->db->where('department_name', $name);
            $query = $this->db->get('department');
            if ($query->num_rows() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    function deleteDepartment($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("department");        
        $message = DELETE_RECORD_CONSTANT . " On Department id " . $id;
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

    function getDepartmentType($id = null) {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('department');
            return $query->row_array();
        } else {
            $query = $this->db->get("department");
            return $query->result_array();
        }
    }

    public function addDepartmentType($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('department', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Department id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);            
        } else {
            $this->db->insert('department', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Department id " . $insert_id;
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
?>