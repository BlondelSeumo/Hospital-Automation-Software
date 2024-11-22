<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Leaverequest_model extends MY_model
{

    public function staff_leave_request($id = null)
    {
        if ($id != null) {
            $this->db->where("staff_leave_request.staff_id", $id);
        }
        $query = $this->db->select('staff.name,staff.surname,staff.employee_id,staff_designation.designation,staff_leave_request.*,leave_types.type,approved_by.name as apply_by_name,approved_by.surname as apply_by_surname,approved_by.employee_id as apply_by_employee_id,staff_roles.role_id as apply_by_role_id')
        ->join("staff", "staff.id = staff_leave_request.staff_id")
        ->join("staff as approved_by", "approved_by.id = staff_leave_request.status_updated_by")
        ->join("staff_roles", "staff_roles.staff_id = approved_by.id")		
        ->join("staff_designation", "staff_designation.id = staff.staff_designation_id", "left")
        ->join("leave_types", "leave_types.id = staff_leave_request.leave_type_id")
        ->where("staff.is_active", "1")
        ->order_by("staff_leave_request.id", "desc")
        ->get("staff_leave_request");
        return $query->result_array();
    }
    
    public function getAllleaveapplyRecord($id = null)
    {  
        $this->datatables
            ->select('staff.name,staff.surname,staff.employee_id,staff_leave_request.*,leave_types.type,approved_by.name as apply_by_name,approved_by.surname as apply_by_surname,approved_by.employee_id as apply_by_employee_id,staff_roles.role_id')
            ->searchable('staff.name,leave_types.type,staff_leave_request.leave_from')
            ->orderable('staff.name,leave_types.type,staff_leave_request.leave_from,staff_leave_request.leave_days,staff_leave_request.date,staff_leave_request.status')
            ->join('staff', 'staff.id = staff_leave_request.staff_id')  
            ->join("staff as approved_by", "approved_by.id = staff_leave_request.status_updated_by", "left")
            ->join("staff_roles", "staff_roles.staff_id = staff_leave_request.status_updated_by", "left")
            ->join('leave_types', 'leave_types.id = staff_leave_request.leave_type_id')
            ->where('staff.is_active', '1')
            ->where('staff.id', $id)
            ->sort('staff_leave_request.id', 'desc')
            ->from('staff_leave_request');
        return $this->datatables->generate('json');
    }

    public function getAllleaverequestRecord()
    {  
        $this->datatables
            ->select('staff.name,staff.surname,staff.employee_id,staff_designation.designation,staff_leave_request.*,leave_types.type,apply_staff.name as apply_by_name,apply_staff.surname as apply_by_surname,apply_staff.employee_id as apply_by_employee_id')
            ->searchable('staff.name,leave_types.type,staff_leave_request.leave_from')
            ->orderable('staff.name,leave_types.type,staff_leave_request.leave_from,staff_leave_request.leave_days,staff_leave_request.date,staff_leave_request.status,staff_leave_request.approved_date')
            ->join('staff', 'staff.id = staff_leave_request.staff_id')
            ->join('staff as apply_staff', 'apply_staff.id = staff_leave_request.status_updated_by','left')
            ->join('staff_designation', 'staff_designation.id = staff.staff_designation_id','left')
            ->join('leave_types', 'leave_types.id = staff_leave_request.leave_type_id')
            ->where('staff.is_active', '1')
            ->sort('staff_leave_request.id', 'desc')
            ->from('staff_leave_request');
        return $this->datatables->generate('json');
    }

    public function user_leave_request($id = null)
    {
        $query = $this->db->select('staff.name,staff.surname,staff.employee_id,staff_leave_request.*,leave_types.type')->join("staff", "staff.id = staff_leave_request.staff_id")->join("leave_types", "leave_types.id = staff_leave_request.leave_type_id")->where("staff.is_active", "1")->where("staff.id", $id)->order_by("staff_leave_request.id", "desc")->get("staff_leave_request");
        return $query->result_array();
    }    

    public function allotedLeaveType($id)
    {
        $query = $this->db->select('staff_leave_details.*,leave_types.type,leave_types.id as typeid')->where(array('staff_id' => $id))->join("leave_types", "staff_leave_details.leave_type_id = leave_types.id")->get("staff_leave_details");
        return $query->result_array();
    }

    public function countLeavesData($staff_id, $leave_type_id)
    {
        $query1 = $this->db->select('sum(leave_days) as approve_leave')->where(array('staff_id' => $staff_id, 'status' => 'approve', 'leave_type_id' => $leave_type_id))->get("staff_leave_request");
        return $query1->row_array();
    }

    public function changeLeaveStatus($data, $staff_id)
    {
        $this->db->where("id", $staff_id)->update("staff_leave_request", $data);
    }

    public function getLeaveSummary()
    {
        $query = $this->db->select('*')->get("staff");
        return $query->result_array();
    }

    public function addLeaveRequest($data)
    {  
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('staff_leave_request', $data);
            $message = UPDATE_RECORD_CONSTANT . " For Staff Leave Request ".$this->lang->line($data['status'])." id " . $data['id'];
            $action = "Update";
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
            $this->db->insert('staff_leave_request', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Staff Leave Request ".$this->lang->line($data['status'])." id " .$insert_id;
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
    }

    public function myallotedLeaveType($id, $leave_type_id) {
        $query = $this->db->select('staff_leave_details.*,leave_types.type,leave_types.id as typeid')->where(array('staff_id' => $id, 'leave_types.id' => $leave_type_id))->join("leave_types", "staff_leave_details.leave_type_id = leave_types.id")->get("staff_leave_details");
        return $query->row_array();
    }
}
