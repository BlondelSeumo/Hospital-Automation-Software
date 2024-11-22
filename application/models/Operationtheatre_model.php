<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Operationtheatre_model extends MY_Model {

    public function add_patient($patient_data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('patients', $patient_data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Patients id " . $insert_id;
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
            return $record_id;
        }        
    }

    public function add($operation)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('operation', $operation);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Operation id " . $insert_id;
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
            return $record_id;
        }        
    }

    public function operation_list($id = null)
    {
        $this->db->select("operation.*,operation_category.category")->from('operation')->join("operation_category","operation_category.id=operation.category_id","left");
        if ($id != null) {
            $this->db->where('operation.id', $id);
        } else {
            $this->db->order_by('operation.operation');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getoperationbycategory($id)
    {
        $this->db->select("operation.*,operation_category.category")->from('operation')->join("operation_category","operation_category.id=operation.category_id")->where("operation_category.id",$id);
        $query = $this->db->get();
        return $query->result_array();        
    }

    public function delete_operation($id = null)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('operation');        
        $message = DELETE_RECORD_CONSTANT . " On Operation id " . $id;
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

    public function delete_category($id = null)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('operation_category');        
        $message = DELETE_RECORD_CONSTANT . " On Operation Categoryid " . $id;
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

    public function updateoperation($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('operation', $data);
        
        $message = UPDATE_RECORD_CONSTANT . " On Operation id " . $id;
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

    public function insert($data,$tbl,$id=null)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if($id!=""){
            $this->db->where('id', $id);
            $this->db->update($tbl, $data);            
            $message = UPDATE_RECORD_CONSTANT . " On " .$tbl. " id " . $id;
            $action = "Update";
            $record_id = $id;
            $this->log($message, $record_id, $action);            
        }else{
            $this->db->insert($tbl, $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On " .$tbl. " id " . $insert_id;
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

    public function category_list($id = null)
    {
        $this->db->select()->from('operation_category');
        if ($id != null) {
            $this->db->where('operation_category.id', $id);
        } else {
            $this->db->order_by('operation_category.category');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }
    public function update_patient($patient_data) {
        
        $query = $this->db->where('id', $patient_data['id'])
                ->update('patients', $patient_data);
                
    }

    public function operation_detail($data) 
    {
            $this->db->trans_start(); # Starting Transaction
            $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //======================Code Start==============================  
            $this->db->insert('operation_theatre', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Operation Theatre id " . $insert_id;
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

    public function getBillDetails($id) {
        $this->db->select('operation_theatre.*,patients.patient_name,staff.name as doctorname,staff.surname as doctorsurname');
        $this->db->join('patients', 'patients.id = operation_theatre.patient_id');
        $this->db->join('staff', 'staff.id = operation_theatre.consultant_doctor', "inner");
        $this->db->where('operation_theatre.id', $id);
        $query = $this->db->get('operation_theatre');
        $result = $query->row_array();
        $generated_by = $result["generated_by"];
        $staff_query = $this->db->select("staff.name,staff.surname")
                ->where("staff.id", $generated_by)
                ->get("staff");
        $staff_result = $staff_query->row_array();
        $result["generated_byname"] = $staff_result["name"] . $staff_result["surname"];
        return $result;
    }

    public function getBillDetailsOt($id) {
        $this->db->select('operation_theatre.*,patients.patient_name,staff.name as doctor_name,staff.surname as doctor_surname');
        $this->db->join('patients', 'patients.id = operation_theatre.patient_id');
        $this->db->join('staff', 'staff.id = operation_theatre.consultant_doctor', "inner");
        $this->db->where('operation_theatre.id', $id);
        $query = $this->db->get('operation_theatre');
        return $query->row_array();
    }

    function getMaxId() {
        $query = $this->db->select('max(id) as bill_no')->get("operation_theatre");
        $result = $query->row_array();
        return $result["bill_no"];
    }

    public function getAllBillDetails($id) {
        $query = $this->db->select('operation_theatre.*')
                ->where('operation_theatre.id', $id)
                ->get('operation_theatre');
        return $query->result_array();
    }

    public function getAllBillDetailsOt($id) {
        $query = $this->db->select('operation_theatre.*')
                ->where('operation_theatre.id', $id)
                ->get('operation_theatre');
        return $query->result_array();
    }

    public function update_operation_detail($data) 
    {   
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('operation_theatre', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Operation Theatre id " . $data['id'];
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
        }
     
    }

    public function searchFullText($limit=100,$start="") {
        $userdata = $this->customlib->getUserData();
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        if ($doctor_restriction == 'enabled') {
            if ($userdata["role_id"] == 3) {
                $this->db->where('operation_theatre.consultant_doctor', $userdata['id']);
            }   
        }
        $this->db->select('operation_theatre.*,patients.id as pid,patients.patient_unique_id,patients.patient_name,patients.gender,patients.mobileno,staff.id as staff_id,staff.name,staff.surname,charges.id as cid,charges.charge_category,charges.code,charges.description,charges.standard_charge')->from('operation_theatre');
        $this->db->join('patients', 'operation_theatre.patient_id=patients.id', "inner");
        $this->db->join('staff', 'staff.id = operation_theatre.consultant_doctor', "inner");
        $this->db->join('charges', 'operation_theatre.charge_id = charges.id');
        $this->db->where('patients.is_active', 'yes');
        $this->db->order_by('operation_theatre.id', 'desc');
        $this->db->limit($limit,$start);
        $query = $this->db->get();
        $result = $query->result_array();
        foreach ($result as $key => $value) {
            $generated_by = $value["generated_by"];
            $staff_query = $this->db->select("staff.name,staff.surname")
                    ->where("staff.id", $generated_by)
                    ->get("staff");
            $staff_result = $staff_query->row_array();
            $result[$key]["generated_byname"] = $staff_result["name"] . $staff_result["surname"];
        }
        return $result;
    }

    public function getAllotRecord()
    {
        $i = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('operationtheatre', 1);
        $custom_field_column_array= array();

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                  array_push($custom_field_column_array, 'table_custom_' . $i.'.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'operation_theatre.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable = implode(',', $field_var_array);
        $custom_field_column = implode(',', $custom_field_column_array);
        $this->datatables
            ->select('operation_theatre.*,patients.id as pid,patients.patient_name,patients.gender,patients.mobileno,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,charges.id as cid,charges.charge_category_id,charges.description,charges.standard_charge,'.$field_variable)
            ->join('patients', 'operation_theatre.patient_id=patients.id', "inner")
            ->join('staff', 'staff.id = operation_theatre.consultant_doctor', "inner")
            ->join('charges', 'operation_theatre.charge_id = charges.id')
            ->searchable('operation_theatre.id,patients.patient_name,patients.patient_unique_id,patients.gender,patients.mobileno,operation_theatre.operation_name,operation_theatre.operation_type,staff.name,operation_theatre.date,operation_theatre.apply_charge,'.$custom_field_column)
            ->orderable('operation_theatre.id,patients.patient_name,patients.patient_unique_id,patients.gender,patients.mobileno,operation_theatre.operation_name,operation_theatre.operation_type,staff.name,operation_theatre.date,'.$custom_field_column)
            ->sort('operation_theatre.id', 'desc')
            ->where('patients.is_active', 'yes')
            ->from('operation_theatre');
        return $this->datatables->generate('json');
    } 

    public function searchFullTextPat($patient_id) {
        $this->db->select('operation_theatre.*,patients.gender,patients.mobileno,patients.patient_unique_id,patients.patient_name,staff.name,staff.surname');
        $this->db->join('patients', 'operation_theatre.patient_id = patients.id');
        $this->db->join('staff', "staff.id = operation_theatre.consultant_doctor");
        $this->db->where('operation_theatre.patient_id', $patient_id);
        $query = $this->db->get('operation_theatre');
        $result = $query->result_array();
        return $result;
    }

    public function getDetails($id) {
        $this->db->select('operation_theatre.*,patients.id as pid,patients.patient_unique_id,patients.patient_name,patients.admission_date,patients.gender,patients.age,patients.month,patients.patient_type,patients.guardian_name,patients.mobileno,patients.address,patients.is_active,staff.name,staff.surname,charges.id as cid,charges.charge_category_id,charges.name,charges.standard_charge,charges.description,organisation.organisation_name')->from('operation_theatre');
        $this->db->join('patients', 'operation_theatre.patient_id=patients.id', "inner");
        $this->db->join('staff', 'staff.id = operation_theatre.consultant_doctor', "inner");
        $this->db->join('organisation', 'organisation.id = patients.organisation', "left");
        $this->db->join('charges', 'operation_theatre.charge_id = charges.id');
        $this->db->where('patients.is_active', 'yes');
        $this->db->where('operation_theatre.patient_id', $id);
        $this->db->or_where('patients.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getotDetails($id) {
        $this->db->select('operation_theatre.*,operation_category.id as category_id')->from('operation_theatre');
        $this->db->join('operation', 'operation_theatre.operation_id=operation.id', "left");
        $this->db->join("operation_category","operation_category.id=operation.category_id","left");
        $this->db->where('operation_theatre.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getopdoperationDetails($opdid,$patient_panel= null) {
        if($patient_panel == 'patient'){            
            $custom_fields             = $this->customfield_model->get_custom_fields('operationtheatre','','','', 1);
        }else{
            $custom_fields             = $this->customfield_model->get_custom_fields('operationtheatre', 1);
        }
        
        $custom_field_column_array = array();
        $field_var_array = array();
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'operation_theatre.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->db->select('operation_theatre.*,operation.operation,operation_category.category,patients.id as pid,patients.patient_name,patients.gender,patients.age,patients.month,patients.patient_type,patients.mobileno,patients.is_active'.$field_variable)->from('operation_theatre');
        $this->db->join('opd_details', 'opd_details.id=operation_theatre.opd_details_id', "left");
        $this->db->join('patients', 'patients.id=opd_details.patient_id', "left");
        $this->db->join('operation', 'operation_theatre.operation_id=operation.id', "left");
        $this->db->join("operation_category","operation_category.id=operation.category_id","left");
        $this->db->where('operation_theatre.opd_details_id', $opdid);
        $query = $this->db->get();
        return $query->result_array();
    }

     public function getipdoperationDetails($ipdid, $patient_panel = null) {
        if($patient_panel == 'patient'){
          $custom_fields             = $this->customfield_model->get_custom_fields('operationtheatre', '','','', 1);
        }else{
          $custom_fields             = $this->customfield_model->get_custom_fields('operationtheatre', 1);  
        }
        
        $custom_field_column_array = array();
        $field_var_array = array();
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'operation_theatre.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->db->select('operation_theatre.*,operation.operation,operation_category.category,patients.id as pid,patients.patient_name,patients.gender,patients.age,patients.month,patients.patient_type,patients.mobileno,patients.is_active'.$field_variable)->from('operation_theatre');
        $this->db->join('opd_details', 'opd_details.id=operation_theatre.opd_details_id', "left");
        $this->db->join('patients', 'patients.id=opd_details.patient_id', "left");
        $this->db->join('operation', 'operation_theatre.operation_id=operation.id', "left");
        $this->db->join("operation_category","operation_category.id=operation.category_id","left");
        $this->db->where('operation_theatre.ipd_details_id', $ipdid);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getopdipdDetails($id, $patient_type) {
        if ($patient_type == 'Inpatient') {
            $query = $this->db->where("patient_id", $id)->get("ipd_details");
            $result = $query->row_array();
            return $result['ipd_no'];
        }
    } 

    public function getOtPatientDetails($otid) {
        $this->db->select('operation_theatre.*,patients.id as pid,patients.patient_unique_id,patients.patient_name,patients.admission_date,patients.gender,patients.age,patients.month,patients.patient_type,patients.guardian_name,patients.mobileno,patients.organisation,patients.blood_group,patients.known_allergies,patients.note,patients.marital_status,patients.guardian_address,patients.is_active,patients.email,patients.address,patients.dob,staff.name,staff.surname,charges.id as cid,charges.charge_category_id,charges.name,charges.standard_charge,charges.description')->from('operation_theatre');
        $this->db->join('patients', 'operation_theatre.patient_id=patients.id', "inner");
        $this->db->join('staff', 'staff.id = operation_theatre.consultant_doctor', "inner");
        $this->db->join('charges', 'operation_theatre.charge_id = charges.id');
        $this->db->where('operation_theatre.id', $otid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete($id) 
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        $this->db->where('id', $id);
        $this->db->delete('operation_theatre');
        $message = DELETE_RECORD_CONSTANT . " On  Operation Theatre  id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id,'operationtheatre'); 
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

    public function add_ot_consultantInstruction($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("ot_consultant_register", $data);
            $message = UPDATE_RECORD_CONSTANT . " On OT Consultant Register id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);            
        } else {
            $this->db->insert("ot_consultant_register", $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On OT Consultant Register id " . $insert_id;
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

    public function getConsultantBatch($patient_id) 
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('otconsultinstruction', 1);

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'ot_consultant_register.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable = implode(',', $field_var_array);
        $this->db->select('ot_consultant_register.*,patients.id as pid,patients.patient_name,staff.name,staff.id as staff_id,staff.surname,'.$field_variable);
        $this->db->join('patients', 'ot_consultant_register.patient_id=patients.id', "inner");
        $this->db->join('staff', 'staff.id = ot_consultant_register.cons_doctor', "inner");
        $this->db->where('ot_consultant_register.patient_id', $patient_id);
        $this->db->order_by('ot_consultant_register.date');
        $query = $this->db->get('ot_consultant_register');
        $result = $query->result();
        $i = 0;
        foreach ($result as $key => $value) {
            $result[$i]->consultant = 'yes';
            $userdata = $this->customlib->getUserData();
            $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
            if ($doctor_restriction == 'enabled') {
                if ($userdata["role_id"] == 3) {
                    if ($userdata["id"] == $value->staff_id) {
                        
                    } else {
                        $result[$i]->consultant = 'not_applicable';
                    }
                }
            }
            $i++;
        }
        return $result;
    }

    public function getConsultantBatchOt($patient_id) {
        $this->db->select('ot_consultant_register.*,patients.id as pid,patients.patient_name,staff.name,staff.id as staff_id,staff.surname');
        $this->db->join('patients', 'ot_consultant_register.patient_id=patients.id', "inner");
        $this->db->join('staff', 'staff.id = ot_consultant_register.cons_doctor', "inner");
        $this->db->where('ot_consultant_register.patient_id', $patient_id);
        $this->db->order_by('ot_consultant_register.date');
        $query = $this->db->get('ot_consultant_register');
        $result = $query->result();
        return $result;
    }

    public function getChargeCategory() {
        $query = $this->db->select('charge_categories.*')                
                ->get('charge_categories');
        return $query->result_array();
    }

    public function deleteConsultant($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("ot_consultant_register");
        $message = DELETE_RECORD_CONSTANT . " On OT Consultant Register id " . $id;
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

    public function otdetails($ot_id) {
        $custom_fields             = $this->customfield_model->get_custom_fields('operationtheatre');
        $custom_field_column_array = array();
        $field_var_array = array();
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
               array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'operation_theatre.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->db->select('operation_theatre.*,operation.operation,operation_category.category,staff.name,staff.surname,staff.employee_id'.$field_variable);
        $this->db->join('staff', 'staff.id=operation_theatre.consultant_doctor');
        $this->db->join('operation', 'operation_theatre.operation_id=operation.id', "left");
        $this->db->join("operation_category","operation_category.id=operation.category_id","left");
        $this->db->where('operation_theatre.id', $ot_id);
        $query = $this->db->get('operation_theatre');
        return $query->row();
    }

    public function otdetailsforprint($ot_id,$patient_panel = null) {
        if($patient_panel == 'patient'){
            $custom_fields             = $this->customfield_model->get_custom_fields('operationtheatre', '','','',1);
        }else{
            $custom_fields             = $this->customfield_model->get_custom_fields('operationtheatre', '',1);
        }
        
        $custom_field_column_array = array();
        $field_var_array = array();
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
               array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'operation_theatre.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->db->select('operation_theatre.*,operation.operation,operation_category.category,staff.name,staff.surname,staff.employee_id,opd_details.patient_id as `opd_patient_id`,opd_patient.patient_name as `opd_patient_name`, ipd_patient.patient_name as `ipd_patient_name`,ipd_details.patient_id as `ipd_patient_id`,ipd_details.case_reference_id as ipd_case_id,opd_details.case_reference_id as opd_case_id'.$field_variable);
        $this->db->join('staff', 'staff.id=operation_theatre.consultant_doctor');
        $this->db->join('operation', 'operation_theatre.operation_id=operation.id', "left");
        $this->db->join("operation_category","operation_category.id=operation.category_id","left");
        $this->db->join('ipd_details', 'ipd_details.id=operation_theatre.ipd_details_id', "left");
        $this->db->join('opd_details', 'opd_details.id=operation_theatre.opd_details_id', "left");
        $this->db->join('patients as `opd_patient`', 'opd_patient.id=opd_details.patient_id', "left");
        $this->db->join('patients as `ipd_patient`', 'ipd_patient.id=ipd_details.patient_id', "left");         
        $this->db->where('operation_theatre.id', $ot_id);
        $query = $this->db->get('operation_theatre');
        return $query->row();
    }
}
?>