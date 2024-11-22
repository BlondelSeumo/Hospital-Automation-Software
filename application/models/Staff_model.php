<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Staff_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        $this->db->select('staff.*,roles.name as user_type,roles.id as role_id')->from('staff')->join("staff_roles", "staff_roles.staff_id = staff.id", "left")->join("roles", "staff_roles.role_id = roles.id", "left");

        if ($id != null) {
            $this->db->where('staff.id', $id);
        } else {
            $this->db->where('staff.is_active', 1);
            $this->db->order_by('staff.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            $result = $query->row_array();
        } else {
            $result = $query->result_array();
            if ($this->session->has_userdata('hospitaladmin')) {
                $superadmin_rest = $this->session->userdata['hospitaladmin']['superadmin_restriction'];
                if ($superadmin_rest == 'disabled') {
                    $search     = in_array(7, array_column($result, 'role_id'));
                    $search_key = array_search(7, array_column($result, 'role_id'));

                    if (!empty($search)) {
                        unset($result[$search_key]);
                        $result = array_values($result);
                    }
                }
            }
        }
        return $result;
    }

    public function getstaff($staff_id)
    {
        $this->db->select('staff.*');
        $this->db->from('staff');
        $this->db->where('staff.id', $staff_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getStaffByID($staff_id)
    {
        $this->db->select('staff.*');
        $this->db->from('staff');
        $this->db->where('staff.id', $staff_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getAll($id = null, $is_active = null)
    {
        $this->db->select("staff.*,staff_designation.designation,department.department_name as department, roles.id as role_id, roles.name as role");
        $this->db->from('staff');
        $this->db->join('staff_designation', "staff_designation.id = staff.staff_designation_id", "left");
        $this->db->join('staff_roles', "staff_roles.staff_id = staff.id", "left");
        $this->db->join('roles', "roles.id = staff_roles.role_id", "left");
        $this->db->join('department', "department.id = staff.department_id", "left");
        if ($id != null) {
            $this->db->where('staff.id', $id);
        } else {
            if ($is_active != null) {
                $this->db->where('staff.is_active', $is_active);
            }
            $this->db->order_by('staff.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function add($data)
    {   
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('staff', $data);
            $message = UPDATE_RECORD_CONSTANT . " For Staff id " . $data['id'];
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
            $this->db->insert('staff', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Staff id " . $insert_id;
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

    public function update($data)
    {
        $this->db->where('id', $data['id']);
        $query = $this->db->update('staff', $data);        
        $message = UPDATE_RECORD_CONSTANT . " On Staff id " . $data['id'];
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
            return true;
        } 
    }

    public function getByVerificationCode($ver_code)
    {
        $condition = "verification_code =" . "'" . $ver_code . "'";
        $this->db->select('*');
        $this->db->from('staff');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function batchInsert($data, $roles = array(), $leave_array = array())
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert('staff', $data);
        $staff_id          = $this->db->insert_id();
        $roles['staff_id'] = $staff_id;
        $this->db->insert_batch('staff_roles', array($roles));
        if (!empty($leave_array)) {
            foreach ($leave_array as $key => $value) {
                $leave_array[$key]['staff_id'] = $staff_id;
            }
            $this->db->insert_batch('staff_leave_details', $leave_array);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $staff_id;
        }
    }

    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('staff');

        $this->db->where('staff_id', $id);
        $this->db->delete('staff_payslip');

        $this->db->where('staff_id', $id);
        $this->db->delete('staff_leave_request');

        $this->db->where('staff_id', $id);
        $this->db->delete('staff_attendance');

        $this->db->where('staff_id', $id);
        $this->db->delete('staff_roles');

        $message = DELETE_RECORD_CONSTANT . "On Staff, Staff Payslip, Staff Leave Request, Staff Attendance, Staff Roles  Where staff id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->customfield_model->delete_custom_fieldRecord($id,'staff'); 
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
    }

    public function add_staff_leave_details($data2)
    {        
        if (isset($data2['id'])) {
            $this->db->where('id', $data2['id']);
            $this->db->update('staff_leave_details', $data2);            
            $insert_id = $data2['id'];            
        } else {
            $this->db->insert('staff_leave_details', $data2);
            $insert_id = $this->db->insert_id();            
        }        
        return $insert_id;        
    }

    public function getPayroll($id = null)
    {
        $this->db->select()->from('staff_payroll');
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

    public function getLeaveType($id = null)
    {
        $this->db->select()->from('leave_types');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('is_active', 'yes');
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function valid_employee_id($str)
    {
        $name     = $this->input->post('name');
        $id       = $this->input->post('employee_id');
        $staff_id = $this->input->post('editid');
        if (!isset($id)) {
            $id = 0;
        }
        if (!isset($staff_id)) {
            $staff_id = 0;
        }

        if ($this->check_data_exists($name, $id, $staff_id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_data_exists($name, $id, $staff_id)
    {
        if ($staff_id != 0) {
            $data  = array('id != ' => $staff_id, 'employee_id' => $id);
            $query = $this->db->where($data)->get('staff');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('employee_id', $id);
            $query = $this->db->get('staff');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function valid_email_id($str)
    {
        $email    = $this->input->post('email');
        $id       = $this->input->post('employee_id');
        $staff_id = $this->input->post('editid');
        if (!isset($id)) {
            $id = 0;
        }
        if (!isset($staff_id)) {
            $staff_id = 0;
        }

        if ($this->check_email_exists($email, $id, $staff_id)) {
            $this->form_validation->set_message('check_exists', 'Email already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_email_exists($email, $id, $staff_id)
    {
        if ($staff_id != 0) {
            $data  = array('id != ' => $staff_id, 'email' => $email);
            $query = $this->db->where($data)->get('staff');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('email', $email);
            $query = $this->db->get('staff');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getStaffRole($id = null)
    {
        $this->db->select('roles.id,roles.name as type')->from('roles');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $this->db->where("is_active", "yes");
        $query = $this->db->get();
        if ($id != null) {
            $result = $query->row_array();
        } else {
            $result = $query->result_array();
            if ($this->session->has_userdata('hospitaladmin')) {
                $superadmin_rest = $this->session->userdata['hospitaladmin']['superadmin_restriction'];
                if ($superadmin_rest == 'disabled') {
                    $search     = in_array(7, array_column($result, 'id'));
                    $search_key = array_search(7, array_column($result, 'id'));
                    if (!empty($search)) {
                        unset($result[$search_key]);
                    }
                }
            }
        }
        return $result;
    }

    public function count_leave($month, $year, $staff_id)
    {
        $query1 = $this->db->select('sum(leave_days) as tl')->where(array('month(leave_from)' => $month, 'year(leave_from)' => $year, 'staff_id' => $staff_id, 'status' => 'approve'))->get("staff_leave_request");
        return $query1->row_array();
    }

    public function alloted_leave($staff_id)
    {
        $query2 = $this->db->select('sum(alloted_leave) as alloted_leave')->where(array('staff_id' => $staff_id))->get("staff_leave_details");
        return $query2->result_array();
    }

    public function allotedLeaveType($id)
    {
        $query = $this->db->select('staff_leave_details.*,leave_types.type')->where(array('staff_id' => $id))->join("leave_types", "staff_leave_details.leave_type_id = leave_types.id")->get("staff_leave_details");
        return $query->result_array();
    }

    public function getAllotedLeave($staff_id)
    {
        $query = $this->db->select('*')->join("leave_types", "staff_leave_details.leave_type_id = leave_types.id")->where("staff_id", $staff_id)->get("staff_leave_details");
        return $query->result_array();
    }

    public function getEmployee($role, $active = 1)
    {	
		$i                         = 1;
		$custom_fields             = $this->customfield_model->get_custom_fields('staff', 1);
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'staff.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable      = implode(',', $field_var_array);
		
        $query = $this->db->select("staff.*,staff_designation.designation,department.department_name as department,roles.name as user_type,".$field_variable)->join('staff_designation', "staff_designation.id = staff.staff_designation_id", "left")->join('staff_roles', "staff_roles.staff_id = staff.id", "left")->join('roles', "roles.id = staff_roles.role_id", "left")->join('department', "department.id = staff.department_id", "left")->where("staff.is_active", $active)->where("roles.id", $role)->get("staff");
        return $query->result_array();
    }

    public function getstaffbyroleidforidcard($role,$active)
    {
        $this->datatables
            ->select('staff.*,staff_designation.designation as designation,department.department_name as department,roles.name as user_type')
            ->join('staff_designation', "staff_designation.id = staff.staff_designation_id", "LEFT")
            ->join('staff_roles', "staff_roles.staff_id = staff.id", "LEFT")
            ->join('roles', "roles.id = staff_roles.role_id", "LEFT")
            ->join('department', "department.id = staff.department_id", "LEFT")
            ->searchable('staff.employee_id,staff.name,staff_designation.designation,department.department_name,staff.father_name,staff.mother_name,staff.local_address,staff.contact_no,dob')
            ->orderable('staff.id,staff.employee_id,staff.name,staff_designation.designation,department.department_name,staff.father_name,staff.mother_name,staff.date_of_joining,staff.local_address,staff.contact_no,dob')
            ->sort('staff.id', 'desc')
            ->where('staff.is_active', $active)
            ->where('roles.id', $role)
            ->from('staff');
        return $this->datatables->generate('json');
    }

    public function getEmployeeByRoleID($role, $active = 1)
    {
        $query = $this->db->select("staff.*,staff_designation.designation,department.department_name as department, roles.id as role_id, roles.name as role")->join('staff_designation', "staff_designation.id = staff.staff_designation_id", "left")->join('staff_roles', "staff_roles.staff_id = staff.id", "left")->join('roles', "roles.id = staff_roles.role_id", "left")->join('department', "department.id = staff.department_id", "left")->where("staff.is_active", $active)->where("roles.id", $role)->order_by('staff.name')->get("staff");
        return $query->result_array();
    }

    public function getdoctorbyspecilist($spec_id,$active = 1)
    {
        $query = $this->db->select("staff.*")->join('staff_roles', "staff_roles.staff_id = staff.id", "left")->join('roles', "roles.id = staff_roles.role_id", "left")->like('specialist', $spec_id)->where("staff.is_active", $active)->where("roles.id", 3)->order_by("staff.name")->get("staff");
        return $query->result_array();
    }

    public function getStaffDesignation()
    {
        $query = $this->db->select('*')->where("is_active", "yes")->get("staff_designation");
        return $query->result_array();
    }

    public function getDepartment()
    {
        $query = $this->db->select('*')->where("is_active", "yes")->get('department');
        return $query->result_array();
    }

    public function getStaffSpeciality($id)
    {
        $query = $this->db->query("SELECT t2.id,t2.specialist_name FROM staff as t1 LEFT JOIN specialist as t2 ON find_in_set(t2.id, t1.specialist) where t1.id=$id");
        return $query->result();
    }
 
    public function getSpecialist()
    {
        $query = $this->db->select('*')->where("is_active", "yes")->get('specialist');
        return $query->result_array();
    }

    public function getLeaveRecord($id)
    {
        $query = $this->db->select('leave_types.type,leave_types.id as lid,staff.name,staff.id as staff_id,staff.surname,roles.name as user_type,staff.employee_id,roles.id as role_id,staff_leave_request.*,apply_staff.employee_id as applier_employee_id,apply_staff.name as applier_name,apply_staff.surname as applier_surname,updated_by_staff.employee_id as updated_by_employee_id,updated_by_staff.name as updated_by_name,updated_by_staff.surname as updated_by_surname')->join("leave_types", "leave_types.id = staff_leave_request.leave_type_id")->join("staff", "staff.id = staff_leave_request.staff_id")->join("staff_roles", "staff.id = staff_roles.staff_id")->join("roles", "staff_roles.role_id = roles.id")->join("staff as updated_by_staff", "updated_by_staff.id = staff_leave_request.status_updated_by",'left')->join("staff as apply_staff", "apply_staff.id = staff_leave_request.applied_by",'left')->where("staff_leave_request.id", $id)->get("staff_leave_request");
        return $query->row();
    }

    public function deleteleave($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete('staff_leave_request');
        $message = DELETE_RECORD_CONSTANT . " On Staff Leave Request id " . $id;
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

    public function getStaffId($empid)
    {
        $data  = array('employee_id' => $empid);
        $query = $this->db->select('id')->where($data)->get("staff");
        return $query->row_array();
    }

    public function getProfile($id)
    {
        $this->db->select('staff.*,staff_designation.designation as designation,staff_roles.role_id, department.department_name as department,roles.name as user_type,specialist.specialist_name');
        $this->db->join("staff_designation", "staff_designation.id = staff.staff_designation_id", "left");
        $this->db->join("department", "department.id = staff.department_id", "left");
        $this->db->join("staff_roles", "staff_roles.staff_id = staff.id", "left");
        $this->db->join("roles", "staff_roles.role_id = roles.id", "left");
        $this->db->join("specialist", "staff.specialist = specialist.id", "left");        
        $this->db->where("staff.id", $id);
        $this->db->from('staff');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getstaffProfile($staffid, $id)
    {
        $query = $this->db->select("staff.id as staffid,staff.name,staff.surname,department.department_name as department,staff_designation.designation,staff.employee_id,staff_payslip.*")->join("staff", "staff.id = staff_payslip.staff_id")->join("staff_designation", "staff.staff_designation_id = staff_designation.id", 'left')->join("department", "staff.department_id = department.id", "left")->where("staff_payslip.id", $id)->get("staff_payslip");
        return $query->row_array();
    }

    public function searchFullText($searchterm, $active)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('staff', 1);
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'staff.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable      = implode(',', $field_var_array);
        $this->db->select('staff.*, staff_designation.designation as designation`, department.department_name as department,roles.name as user_type,roles.id as role_id,'.$field_variable)->from('staff');
        $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id', "LEFT");
        $this->db->join('staff_designation', "staff_designation.id=staff.staff_designation_id", "LEFT");
        $this->db->join('roles', 'roles.id = staff_roles.role_id', "LEFT");
        $this->db->join('department', 'department.id = staff.department_id', "LEFT");
        $this->db->where('staff.is_active',$active);
        if($searchterm){
        $this->db->group_start();
        $this->db->like('staff.employee_id', $searchterm);
        $this->db->or_like('staff.email', $searchterm);
        $this->db->or_like('staff.name', $searchterm);
        $this->db->or_like('staff.gender', $searchterm);
        $this->db->or_like('staff.surname', $searchterm);
        $this->db->or_like('staff_designation.designation', $searchterm);
        $this->db->or_like('department.department_name', $searchterm);       
        $this->db->or_like('staff.blood_group', $searchterm);  
        $this->db->or_like('staff.local_address', $searchterm);
        $this->db->or_like('staff.contact_no', $searchterm);
        $this->db->or_like('roles.name', $searchterm);
        $this->db->group_end();
        }
        $query  = $this->db->get();
        $result = $query->result_array();
        if ($this->session->has_userdata('hospitaladmin')) {
            $superadmin_rest = $this->session->userdata['hospitaladmin']['superadmin_restriction'];
            if ($superadmin_rest == 'disabled') {
                $search     = in_array(7, array_column($result, 'role_id'));
                $search_key = array_search(7, array_column($result, 'role_id'));
                if (!empty($search)) {
                    unset($result[$search_key]);
                }
            }
        }
        return $result;
    } 

    public function searchByEmployeeId($employee_id)
    {
        $this->db->select('*');
        $this->db->from('staff');
        $this->db->like('staff.employee_id', $employee_id);
        $this->db->like('staff.is_active', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getStaffDoc($id)
    {
        $this->db->select('*');
        $this->db->from('staff_documents');
        $this->db->where('staff_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function count_attendance($year, $staff_id, $att_type)
    {
        $query = $this->db->select('count(*) as attendence')->where(array('staff_id' => $staff_id, 'year(date)' => $year, 'staff_attendance_type_id' => $att_type))->get("staff_attendance");
        return $query->row()->attendence;
    }

    public function getStaffPayroll($id)
    {
        $this->db->select('*');
        $this->db->from('staff_payslip');
        $this->db->where('staff_id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function doc_delete($id, $doc, $file)
    {
        if ($doc == 1) {
            $data = array('resume' => '');
        } else
        if ($doc == 2) {
            $data = array('joining_letter' => '');
        } else
        if ($doc == 3) {
            $data = array('resignation_letter' => '');
        } else
        if ($doc == 4) {
            $data = array('other_document_name' => '', 'other_document_file' => '');
        }
        unlink(BASEPATH . "uploads/staff_documents/" . $file);
        $this->db->where('id', $id)->update("staff", $data);
    }

    public function getLeaveDetails($id)
    {
        $query = $this->db->select('staff_leave_details.alloted_leave,staff_leave_details.id as altid,leave_types.type,leave_types.id')->join("leave_types", "staff_leave_details.leave_type_id = leave_types.id", "inner")->where("staff_leave_details.staff_id", $id)->get("staff_leave_details");
        return $query->result_array();
    }

    public function disablestaff($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $data  = array('is_active' => 0);
        $query = $this->db->where("id", $id)->update("staff", $data);
        $message = UPDATE_RECORD_CONSTANT . " On Staff id " . $id;
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

    public function enablestaff($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $data  = array('is_active' => 1);
        $query = $this->db->where("id", $id)->update("staff", $data);
        $message = UPDATE_RECORD_CONSTANT . " On Staff id " . $id;
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

    public function getByEmail($email)
    {
        $this->db->select('staff.*,languages.language,languages.id as language_id');
        $this->db->from('staff')->join('languages', 'languages.id=staff.lang_id', 'left');
        $this->db->where('email', $email);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function checkLogin($data)
    {
        $record = $this->getByEmail($data['email']);
        if ($record) {
            $pass_verify = $this->enc_lib->passHashDyc($data['password'], $record->password);
            if ($pass_verify) {
                $roles = $this->staffroles_model->getStaffRoles($record->id);
                $record->roles = array($roles[0]->name => $roles[0]->role_id);
                return $record;
            }
        }
        return false;
    }

    public function getStaffbyrole($id)
    {
        if ($this->session->has_userdata('hospitaladmin')) {
            $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
            $userdata           = $this->customlib->getUserData();
           $role_id            = $userdata['role_id'];
            if ($doctor_restriction == 'enabled') {
                if ($role_id == $id) {
                    $user_id  = $userdata["id"];
                    $doctorid = $user_id;
                    $this->db->where('staff.id', $user_id);
                }
            }
        }

        $this->db->select('staff.id,staff.name,staff.surname,staff.employee_id,staff_designation.designation as designation,staff_roles.role_id, department.department_name as department,roles.name as user_type');
        $this->db->join("staff_designation", "staff_designation.id = staff.staff_designation_id", "left");
        $this->db->join("department", "department.id = staff.department_id", "left");
        $this->db->join("staff_roles", "staff_roles.staff_id = staff.id", "left");
        $this->db->join("roles", "staff_roles.role_id = roles.id", "left");
        $this->db->where("staff_roles.role_id", $id);
        $this->db->where("staff.is_active", "1");
        $this->db->order_by("staff.name", 'asc');
        $this->db->from('staff');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getStaffipd($id, $doc)
    {
        if ($this->session->has_userdata('hospitaladmin')) {
            $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
            $userdata           = $this->customlib->getUserData();
            $role_id            = $userdata['role_id'];
            if ($doctor_restriction == 'enabled') {
                if ($role_id == 3) {

                    $user_id  = $userdata["id"];
                    $doctorid = $user_id;
                    $this->db->where('staff.id', $user_id);
                }
            }
        }

        $this->db->select('staff.*,staff_designation.designation as designation,staff_roles.role_id, department.department_name as department,roles.name as user_type');
        $this->db->join("staff_designation", "staff_designation.id = staff.staff_designation_id ", "left");
        $this->db->join("department", "department.id = staff.department_id", "left");
        $this->db->join("staff_roles", "staff_roles.staff_id = staff.id", "left");
        $this->db->join("roles", "staff_roles.role_id = roles.id", "left");
        $this->db->where("staff_roles.role_id", $id);
        $this->db->where("staff.is_active", "1");
        $this->db->where_not_in('staff.id', $doc);
        $this->db->from('staff');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function searchNameLike($searchterm, $role)
    {
        $this->db->select('staff.*')->from('staff');
        $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id');
        $this->db->group_start();
        $this->db->like('staff.name', $searchterm);
        $this->db->group_end();
        $this->db->where('staff_roles.role_id', $role);
        $this->db->where('staff.is_active', '1');
        $this->db->order_by('staff.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_role($role_data)
    {        
        $this->db->where("staff_id", $role_data["staff_id"])->update("staff_roles", $role_data);        
    }

    public function import_check_data_exists($name, $id)
    {
        $this->db->where('employee_id', $id);
        $query = $this->db->get('staff');
        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function import_check_email_exists($name, $id)
    {
        $this->db->where('email', $name);
        $query = $this->db->get('staff');
        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_staff_emp_id($id = null)
    {
        $this->db->select('employee_id')->from('staff');
        if ($id != null) {
            $this->db->where('id', $id);
        }         
        $query      = $this->db->get();
        $staffempid = $query->result_array();
        return $staffempid[0]['employee_id'];
    }


}