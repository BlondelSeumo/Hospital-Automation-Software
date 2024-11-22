<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Prescription_model extends MY_Model
{
    public function getPatientPrescription($id)
    {
        $query = $this->db->select("opd_prescription_basic.*,opd_details.symptoms,opd_details.appointment_date,opd_details.refference,opd_details.cons_doctor,opd_details.id as opd_id")->join("visit_details", "visit_details.id=opd_prescription_basic.visit_details_id")->join("opd_details", "visit_details.opd_details_id = opd_details.id")->where("opd_details.patient_id", $id)->get("opd_prescription_basic");
        return $query->result_array();
    }

    public function get($id)
    {
        $query = $this->db->select("opd_details.*,patients.*,staff.name,staff.surname,staff.local_address,visit_details.opd_details_id,ipd_prescription_basic.id as presid")->join('visit_details', 'visit_details.id=opd_prescription_basic.visit_details_id and ipd_prescription_basic.ipd_id=0')->join("opd_details", "visit_details.opd_details_id = opd_details.id")->join("patients", "patients.id = opd_details.patient_id")->join("staff", "staff.id = opd_details.cons_doctor")->where("ipd_prescription_basic.visit_details_id", $id)->get("opd_prescription_basic");
        return $query->row_array();
    }

    public function getvisit($id)
    {
        $query = $this->db->select("visit_details.opd_details_id,`visit_details.id` as visit_id,`visit_details.cons_doctor`,`visit_details.case_type`,`visit_details.appointment_date`,`visit_details.symptoms`,`visit_details.bp`,`visit_details.height`,`visit_details.weight`,`visit_details.pulse`,`visit_details.temperature`,`visit_details.respiration`,`visit_details.known_allergies`,`visit_details.casualty`,`visit_details.refference`,`visit_details.date`,`visit_details.note`,`visit_details.amount`,`visit_details.tax`,`visit_details.note_remark`,`visit_details.payment_mode`,`visit_details.header_note`,`visit_details.footer_note`,`visit_details.generated_by`,`visit_details.discharged`,`visit_details.live_consult`,patients.*,staff.name,staff.surname,staff.local_address,ipd_prescription_basic.id as presid")->join("visit_details", "visit_details.id = ipd_prescription_basic.visit_details_id and ipd_prescription_basic.ipd_id=0 ")->join("opd_details", "visit_details.opd_details_id = opd_details.id")->join("patients", "patients.id = opd_details.patient_id")->join("staff", "staff.id = visit_details.cons_doctor")->where("ipd_prescription_basic.visit_details_id", $id)->get("ipd_prescription_basic");
        return $query->row_array();
    }

    public function getmanual($visitid)
    {
        $query  = $this->db->select("visit_details.*,opd_details.id as opdid,patients.id as patientid ,patients.patient_name,patients.id as patient_unique_id,patients.age,patients.month,patients.day,patients.gender,patients.address,patients.blood_group,staff.name,staff.surname,staff.employee_id,staff.local_address,patients.as_of_date")->join("opd_details", "opd_details.id = visit_details.opd_details_id", "left")->join("patients", "patients.id = opd_details.patient_id", 'left')->join("staff", "staff.id = visit_details.cons_doctor")->where("visit_details.id", $visitid)->get("visit_details");
        $result = $query->row_array();
        return $result;
    }

    public function check_prescription($id, $visitid)
    {
        $this->db->select('prescription.*');
        $this->db->where('prescription.opd_details_id', $id);
        $this->db->where('prescription.visit_id', $visitid);
        $query = $this->db->get('prescription');
        return $query->num_rows();
    }

    public function update_prescription($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $data['id'])->update("prescription", $data);
        $message = UPDATE_RECORD_CONSTANT . " On Prescription id " . $data['id'];
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

    public function update_prescription_test($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('prescription_id', $data['prescription_id'])->update("opd_prescription_test", $data);
        $message = UPDATE_RECORD_CONSTANT . " On Opd Prescription Test Where Prescription id " . $data['prescription_id'];
        $action = "Update";
        $record_id = $data['prescription_id'];
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

    public function update_ipdprescription($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $data['id'])->update("ipd_prescription_details", $data);
        $message = UPDATE_RECORD_CONSTANT . " On Ipd Prescription Details id " . $data['id'];
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

    public function delete_prescription($delete_arr)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        foreach ($delete_arr as $key => $value) {
            $id = $value["id"];
            $this->db->where("id", $id)->delete("prescription");
            $message = DELETE_RECORD_CONSTANT . " On Prescription id " . $id;
            $action = "Delete";
            $record_id = $id;
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
            return true;
        }
    }

    public function delete_ipdprescription($delete_arr)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        foreach ($delete_arr as $key => $value) {
            $id = $value["id"];
            $this->db->where("id", $id)->delete("ipd_prescription_details");
            $message = DELETE_RECORD_CONSTANT . " On Ipd Prescription Details id " . $id;
            $action = "Delete";
            $record_id = $id;
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
            return true;
        }
    }
 
    public function deletePrescription($opdid, $visit_id = '')
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        if ($visit_id > 0) {
            
            $this->db->where("opd_details_id", $opdid)->where("visit_id", $visit_id)->delete("prescription");
            $message = DELETE_RECORD_CONSTANT . " On Prescription where Opd Details id " . $opdid;
            $action = "Delete";
            $record_id = $opdid;
            $this->log($message, $record_id, $action);
        
        } else {
            
            $this->db->where("opd_details_id", $opdid)->delete("prescription");
            $message = DELETE_RECORD_CONSTANT . " On Prescription where Opd Details id " . $opdid;
            $action = "Delete";
            $record_id = $opdid;
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
            return true;
        }
    }

    public function deleteopdPrescription($prescription_id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query= $this->db->where("id", $prescription_id)->delete("ipd_prescription_basic");
        $message = DELETE_RECORD_CONSTANT . " On Ipd Prescription Basic id " . $prescription_id;
        $action = "Delete";
        $record_id = $prescription_id;
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

    public function deleteipdPrescription($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("ipd_prescription_details");
        $message = DELETE_RECORD_CONSTANT . " On Ipd Prescription Details id " . $id;
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

    public function add_ipdprescription($ipd_basic_array, $insert_ipd_prescription_details, $update_ipd_prescription_details, $deleted_medicines, $pathology, $radiology,$delete_pathology, $delete_radiology, $ipd_prescription_basic_id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 0s1. If you wish can remove as well

        if (isset($ipd_basic_array['id']) && $ipd_basic_array['id'] != 0) {
            $ipd_prescription_basic_id = $ipd_basic_array['id'];
            $this->db->where('id', $ipd_basic_array['id'])
                ->update('ipd_prescription_basic', $ipd_basic_array);
                
        } else {

            $this->db->insert("ipd_prescription_basic", $ipd_basic_array);
            $ipd_prescription_basic_id = $this->db->insert_id();            
            $message = INSERT_RECORD_CONSTANT . " On Ipd Prescription Basic id " . $ipd_prescription_basic_id;
            $action = "Insert";
            $this->log($message, $ipd_prescription_basic_id, $action);
            
        }

        if (!empty($deleted_medicines)) {
            $this->db->where_in('id', $deleted_medicines);
            $this->db->delete('ipd_prescription_details');
        }

        if (!empty($insert_ipd_prescription_details)) {
            foreach ($insert_ipd_prescription_details as $key => $value) {
                $insert_ipd_prescription_details[$key]['basic_id'] = $ipd_prescription_basic_id;
            }

            $this->db->insert_batch("ipd_prescription_details", $insert_ipd_prescription_details);
        }
        
        if (!empty($update_ipd_prescription_details)) {
            $this->db->update_batch('ipd_prescription_details', $update_ipd_prescription_details, 'id');
        }
        
        if (!empty($delete_pathology)) {
            $this->db->where('ipd_prescription_basic_id', $ipd_prescription_basic_id);
            $this->db->where_in('pathology_id', $delete_pathology);
            $this->db->delete('ipd_prescription_test');
        }

        if (!empty($pathology)) {
            $pathology_array = array();
            foreach ($pathology as $pathology_key => $pathology_value) {
                $pathology_array[] = array(
                    'ipd_prescription_basic_id' => $ipd_prescription_basic_id,
                    'pathology_id'              => $pathology_value,
                );
            }
            $this->db->insert_batch("ipd_prescription_test", $pathology_array);
        }
		
        if (!empty($delete_radiology)) {
            $this->db->where('ipd_prescription_basic_id', $ipd_prescription_basic_id);
            $this->db->where_in('radiology_id', $delete_radiology);
            $this->db->delete('ipd_prescription_test');
        }

        if (!empty($radiology)) {
            $radiology_array = array();
            foreach ($radiology as $radiology_key => $radiology_value) {
                $radiology_array[] = array(
                    'ipd_prescription_basic_id' => $ipd_prescription_basic_id,
                    'radiology_id'              => $radiology_value,
                );
            }
            $this->db->insert_batch("ipd_prescription_test", $radiology_array);
        }
         
        $this->db->trans_complete(); # Completing transaction
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $ipd_prescription_basic_id;
        }
    }

    public function add_ipdprescription_test($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['prescription_id'])) {
            
            $this->db->where('prescription_id', $data['prescription_id']);
            $this->db->update('ipd_prescription_test', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Ipd Prescription Test where Prescription id " . $data['prescription_id'];
            $action = "Update";
            $record_id = $data['prescription_id'];
            $this->log($message, $record_id, $action);
            
        } else {
            
            $this->db->insert('ipd_prescription_test', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Ipd Prescription Test id " . $insert_id;
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

    public function getPrescriptiontestopd($id)
    {
        $pathology = $this->db->select('ipd_prescription_test.pathology_id')
            ->where(array('ipd_prescription_test.ipd_prescription_basic_id' => $id, 'radiology_id' => null))
            ->get('ipd_prescription_test');
        $pathology_data = $pathology->result_array();

        $radiology = $this->db->select('ipd_prescription_test.radiology_id')
            ->where(array('ipd_prescription_test.ipd_prescription_basic_id' => $id, 'pathology_id' => null))
            ->get('ipd_prescription_test');
        $radiology_data = $radiology->result_array();

        return array('pathology_data' => $pathology_data, 'radiology_data' => $radiology_data);
    }
 
    public function prescription_note($id)
    {
        $prescription_note = $this->db->select('ipd_prescription_basic.*')
            ->where(array('ipd_prescription_basic.visit_details_id' => $id))
            ->get('ipd_prescription_basic');
        $radiology_data = $prescription_note->row_array();
        return $radiology_data;
    }

    public function getIpdPrescription($ipdid)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('prescription', 1);
        $custom_field_column_array = array();

        $field_var_array = array();
       if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'ipd_prescription_basic.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);

        $query = $this->db->select('ipd_prescription_basic.*,staff_generated.name as staff_name,staff_generated.surname as staff_surname,staff_generated.employee_id as staff_employee_id,staff_priscribe_by.name as priscribe_by_name,staff_priscribe_by.surname as priscribe_by_surname,staff_priscribe_by.employee_id as priscribe_by_employee_id,'. $field_variable)
            ->join('ipd_prescription_details', 'ipd_prescription_basic.id = ipd_prescription_details.basic_id','left')        
            ->join("staff as staff_generated", "staff_generated.id = ipd_prescription_basic.generated_by","left")
            ->join("staff as staff_priscribe_by", "staff_priscribe_by.id = ipd_prescription_basic.prescribe_by","left")   
            ->where("ipd_prescription_basic.ipd_id", $ipdid)
            ->group_by("ipd_prescription_basic.id")
            ->get('ipd_prescription_basic');
        return $query->result_array();
    }

    public function getopdvisitPrescription($visitid)
    {
        $query = $this->db->select('ipd_prescription_basic.*')
            ->join('ipd_prescription_details', 'ipd_prescription_basic.id = ipd_prescription_details.basic_id')
            ->where("ipd_prescription_basic.visit_details_id", $visitid)
            ->group_by("ipd_prescription_basic.id")
            ->get('ipd_prescription_basic');
        return $query->result_array();
    }

    public function getIPD($id)
    {
        $query = $this->db->select("ipd_details.*,patients.*,staff.name,staff.surname,staff.local_address,ipd_prescription_basic.ipd_id,ipd_prescription_basic.id as presid,ipd_prescription_basic.date as presdate,ipd_prescription_basic.header_note,ipd_prescription_basic.footer_note")->join("ipd_details", "ipd_prescription_basic.ipd_id = ipd_details.id")->join("patients", "patients.id = ipd_details.patient_id")->join("staff", "staff.id = ipd_details.cons_doctor")->where("ipd_prescription_basic.id", $id)->get("ipd_prescription_basic");
        return $query->row_array();
    }

    public function getPrescriptionByTable($id, $table_type)
    {
        $custom_fields             = $this->customfield_model->get_custom_fields('prescription', 1);
      
        $custom_field_column_array = array();

        $field_var_array = array();
        $i=1;
       if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'ipd_prescription_basic.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        if ($table_type == "ipd_prescription") {
            $query = $this->db->select("ipd_details.*,blood_bank_products.name as blood_group_name,patients.*,staff_generated.name as staff_name,staff_generated.surname as staff_surname,staff_generated.employee_id as staff_employee_id,staff.name,staff.surname,staff.employee_id,staff.local_address,ipd_prescription_basic.ipd_id,ipd_prescription_basic.id as prescription_id,ipd_prescription_basic.attachment,ipd_prescription_basic.date as presdate,ipd_prescription_basic.header_note,ipd_prescription_basic.footer_note,ipd_prescription_basic.finding_description,ipd_prescription_basic.is_finding_print,staff.id as staff_id,staff_priscribe_by.name as priscribe_by_name,staff_priscribe_by.surname as priscribe_by_surname,staff_priscribe_by.employee_id as priscribe_by_employee_id,ipd_prescription_basic.prescribe_by ". $field_variable)->join("ipd_details", "ipd_prescription_basic.ipd_id = ipd_details.id");
            $this->db->join("patients", "patients.id = ipd_details.patient_id");
            $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id',"left");
            $this->db->join("staff", "staff.id = ipd_details.cons_doctor","left");
            $this->db->join("staff as staff_generated", "staff_generated.id = ipd_prescription_basic.generated_by","left");
            $this->db->join("staff as staff_priscribe_by", "staff_priscribe_by.id = ipd_prescription_basic.prescribe_by","left");
            $this->db->where("ipd_prescription_basic.id", $id);
            $query = $this->db->get("ipd_prescription_basic");

            if ($query->num_rows() > 0) {
                $result            = $query->row();
                $result->medicines = $this->getPrescriptionMedicinesByBasicID($result->prescription_id);
                $result->tests     = $this->getPrescriptionTestsByBasicID($result->prescription_id);
                return $result;
            } 
        } elseif ($table_type == "opd_prescription") {
            $query = $this->db->select("opd_details.*,patients.*,staff.name,staff.surname,staff.local_address,ipd_prescription_basic.ipd_id,ipd_prescription_basic.id as prescription_id,ipd_prescription_basic.attachment,ipd_prescription_basic.date as presdate,ipd_prescription_basic.header_note,ipd_prescription_basic.footer_note,ipd_prescription_basic.finding_description,ipd_prescription_basic.is_finding_print,visit_details.id as visit_details_id ". $field_variable);
            $this->db->join("visit_details", "visit_details.id = ipd_prescription_basic.visit_details_id");
            $this->db->join("opd_details", "opd_details.id = visit_details.opd_details_id");
            $this->db->join("patients", "patients.id = opd_details.patient_id");
            $this->db->join("staff", "staff.id = visit_details.cons_doctor","left");
            $this->db->where("ipd_prescription_basic.id", $id);
            $query = $this->db->get("ipd_prescription_basic");
            if ($query->num_rows() > 0) {
                $result            = $query->row();
                $result->medicines = $this->getPrescriptionMedicinesByBasicID($result->prescription_id);
                $result->tests     = $this->getPrescriptionTestsByBasicID($result->prescription_id);
                return $result;
            }
        } else {
            return false;
        }
        return false;
    } 

    public function get_opd_prescription_basic_id($id){
        return $this->db->select('ipd_prescription_basic.id')->from('ipd_prescription_basic')->join('visit_details','visit_details.id=ipd_prescription_basic.visit_details_id')->join('opd_details','visit_details.opd_details_id=opd_details.id')->where('opd_details',$id)->get()->row_array();
    }

    public function getPrescriptionMedicinesByBasicID($id) 
    {
        $query = $this->db->select('`ipd_prescription_basic`.*,pharmacy.id as pharmacy_id,pharmacy.medicine_name,medicine_category.id as medicine_category_id,medicine_category.medicine_category,ipd_prescription_details.instruction,ipd_prescription_details.dose_interval_id,ipd_prescription_details.dose_duration_id,dose_duration.name as dose_duration_name,dose_interval.name as dose_interval_name,medicine_dosage.dosage,unit.unit_name as unit,ipd_prescription_details.dosage as dosage_id,ipd_prescription_details.id as ipd_prescription_detail_id')
            ->join("ipd_prescription_basic", "ipd_prescription_basic.id = ipd_prescription_details.basic_id")
            ->join("pharmacy", "ipd_prescription_details.pharmacy_id = pharmacy.id")
            ->join("medicine_category", "medicine_category.id=pharmacy.medicine_category_id")
            ->join("medicine_dosage", "medicine_dosage.id=ipd_prescription_details.dosage","left")
            ->join("unit", "unit.id=medicine_dosage.units_id","left")
            ->join("dose_interval", "dose_interval.id=ipd_prescription_details.dose_interval_id",'left')
            ->join("dose_duration", "dose_duration.id=ipd_prescription_details.dose_duration_id",'left')
            ->where("ipd_prescription_details.basic_id", $id)
            ->get("ipd_prescription_details");
        $result = $query->result();      
        return $result;
    }

    public function getPrescriptionTestsByBasicID($id, $test_category = null)
    {
        $this->db->select('ipd_prescription_test.*,pathology.test_name,pathology.short_name,pathology.report_days,pathology.charge_id,charges.standard_charge,charges.name as `charge_name`,pathology.test_name,radio.test_name as `radio_test_name`,radio.short_name as `radio_short_name`,radio.report_days as `radio_report_days`,radio.charge_id as `radio_charge_id`,radio_charge.standard_charge as `radio_standard_charge`,radio_charge.name as `radio_charge_name`');
        $this->db->join("pathology", "ipd_prescription_test.pathology_id = pathology.id", 'left');
        $this->db->join("radio", "ipd_prescription_test.radiology_id = radio.id", 'left');
        $this->db->join("charges", "pathology.charge_id = charges.id", 'left');
        $this->db->join("charges as `radio_charge`", "radio.charge_id = radio_charge.id", 'left');
        $this->db->where("ipd_prescription_test.ipd_prescription_basic_id", $id);
        $query = $this->db->get('ipd_prescription_test');
        return $query->result();
    }

    public function getPrescriptionTestsByCategory($id, $table_type, $test_category)
    {
        if ($table_type == "ipd_prescription") {
            $query = $this->db->select("ipd_details.*,patients.*,staff.name,staff.surname,staff.local_address,ipd_prescription_basic.ipd_id,ipd_prescription_basic.id as prescription_id,ipd_prescription_basic.date as presdate,ipd_prescription_basic.header_note,ipd_prescription_basic.footer_note")->join("ipd_details", "ipd_prescription_basic.ipd_id = ipd_details.id");
            $this->db->join("patients", "patients.id = ipd_details.patient_id");
            $this->db->join("staff", "staff.id = ipd_details.cons_doctor");
            $this->db->where("ipd_prescription_basic.id", $id);
            $query = $this->db->get("ipd_prescription_basic");

            if ($query->num_rows() > 0) {
                $result = $query->row();
                $result->tests = $this->getPrescriptionTest($result->prescription_id, $test_category,$table_type);
                return $result;
            }

        } elseif ($table_type == "opd_prescription") {

            $query = $this->db->select("opd_details.*,patients.*,staff.name,staff.surname,staff.local_address,ipd_prescription_basic.ipd_id,ipd_prescription_basic.id as prescription_id,ipd_prescription_basic.date as presdate,ipd_prescription_basic.header_note,ipd_prescription_basic.footer_note");
            $this->db->join("visit_details", "visit_details.id = ipd_prescription_basic.visit_details_id");
            $this->db->join("opd_details", "opd_details.id = visit_details.opd_details_id");
            $this->db->join("patients", "patients.id = opd_details.patient_id");
            $this->db->join("staff", "staff.id = visit_details.cons_doctor");
            $this->db->where("ipd_prescription_basic.id", $id);
            $query = $this->db->get("ipd_prescription_basic");

            if ($query->num_rows() > 0) {
                $result = $query->row();
                $result->tests = $this->getPrescriptionTest($result->prescription_id, $test_category,$table_type);
                return $result;
            }
        } else {
            return false;
        }
        return false;
    }

    public function getPrescriptionTest($id, $test_category = null, $table_type = null)
    {
        if ($table_type == "ipd_prescription") {
       
        $this->db->select('patients.organisation_id,organisations_charges.*,ipd_prescription_test.*,pathology.test_name,pathology.short_name,pathology.report_days,pathology.charge_id,charges.standard_charge,charges.name as `charge_name`,pathology.test_name,tax_category.percentage as tax,radio.test_name as `radio_test_name`,radio.short_name as `radio_short_name`,radio.report_days as `radio_report_days`,radio_tax.percentage as radiology_tax,radio.charge_id as `radio_charge_id`,radio_charge.standard_charge as `radio_standard_charge`,radio_charge.name as `radio_charge_name`,radio_org_charge.org_id as radio_org_charge_org_id,radio_org_charge.charge_id as radio_org_charge_charge_id,radio_org_charge.org_charge as radio_org_charge_org_charge');
        $this->db->where("ipd_prescription_test.$test_category !=", null);
        $this->db->join("pathology", "ipd_prescription_test.pathology_id = pathology.id", 'left');
        $this->db->join("radio", "ipd_prescription_test.radiology_id = radio.id", 'left');
        $this->db->join("charges", "pathology.charge_id = charges.id", 'left');
        $this->db->join("ipd_prescription_basic", "ipd_prescription_basic.id = ipd_prescription_test.ipd_prescription_basic_id", 'left');
        $this->db->join("ipd_details", "ipd_details.id = ipd_prescription_basic.ipd_id", 'left');
        $this->db->join("patients", "patients.id = ipd_details.patient_id", 'left');
        $this->db->join("tax_category", "charges.tax_category_id = tax_category.id", 'left');
        $this->db->join("charges as `radio_charge`", "radio.charge_id = radio_charge.id", 'left');
        $this->db->join("tax_category as radio_tax", "radio_charge.tax_category_id = radio_tax.id", 'left');
        $this->db->join("organisations_charges", "organisations_charges.charge_id = charges.id and patients.organisation_id=organisations_charges.org_id", 'left');
        $this->db->join("organisations_charges as radio_org_charge", "radio_org_charge.charge_id = radio_charge.id and patients.organisation_id=radio_org_charge.org_id", 'left');  

        $this->db->where("ipd_prescription_test.ipd_prescription_basic_id", $id);
        $query = $this->db->get('ipd_prescription_test');
        return $query->result();

        }else  if ($table_type == "opd_prescription") {
        
        $this->db->select('patients.organisation_id,organisations_charges.*,ipd_prescription_test.*,pathology.test_name,pathology.short_name,pathology.report_days,pathology.charge_id,charges.standard_charge,charges.name as `charge_name`,pathology.test_name,tax_category.percentage as tax,radio.test_name as `radio_test_name`,radio.short_name as `radio_short_name`,radio.report_days as `radio_report_days`,radio_tax.percentage as radiology_tax,radio.charge_id as `radio_charge_id`,radio_charge.standard_charge as `radio_standard_charge`,radio_charge.name as `radio_charge_name`,radio_org_charge.org_id as radio_org_charge_org_id,radio_org_charge.charge_id as radio_org_charge_charge_id,radio_org_charge.org_charge as radio_org_charge_org_charge');

        $this->db->where("ipd_prescription_test.$test_category !=", null);
        $this->db->join("pathology", "ipd_prescription_test.pathology_id = pathology.id", 'left');
        $this->db->join("radio", "ipd_prescription_test.radiology_id = radio.id", 'left');
        $this->db->join("charges", "pathology.charge_id = charges.id", 'left');       
        $this->db->join("ipd_prescription_basic", "ipd_prescription_basic.id = ipd_prescription_test.ipd_prescription_basic_id", 'left');       
        $this->db->join("visit_details", "visit_details.id = ipd_prescription_basic.visit_details_id");
        $this->db->join("opd_details", "opd_details.id = visit_details.opd_details_id");     
        $this->db->join("patients", "patients.id = opd_details.patient_id", 'left');        
        $this->db->join("tax_category", "charges.tax_category_id = tax_category.id", 'left');
        $this->db->join("charges as `radio_charge`", "radio.charge_id = radio_charge.id", 'left');
        $this->db->join("tax_category as radio_tax", "radio_charge.tax_category_id = radio_tax.id", 'left');
        $this->db->join("organisations_charges", "organisations_charges.charge_id = charges.id and patients.organisation_id=organisations_charges.org_id", 'left');  
        $this->db->join("organisations_charges as radio_org_charge", "radio_org_charge.charge_id = radio_charge.id and patients.organisation_id=radio_org_charge.org_id", 'left');  

        $this->db->where("ipd_prescription_test.ipd_prescription_basic_id", $id);
        $query = $this->db->get('ipd_prescription_test');
        return $query->result();
        }        
    }   

    public function getPrescriptionByVisitID($visitid)
    {
        $custom_fields             = $this->customfield_model->get_custom_fields('prescription');
        $custom_field_column_array = array();
        $field_var_array = array();
        $i=1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'ipd_prescription_basic.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);

        $query = $this->db->select("opd_details.*,visit_details.id as visitid,visit_details.known_allergies as any_allergies,visit_details.weight,visit_details.height,visit_details.pulse,visit_details.temperature,visit_details.symptoms,visit_details.bp,patients.*,blood_bank_products.name as blood_group_name,staff.name,staff.surname,staff.employee_id,staff.local_address,ipd_prescription_basic.ipd_id,ipd_prescription_basic.id as prescription_id,ipd_prescription_basic.date as presdate,ipd_prescription_basic.header_note,ipd_prescription_basic.footer_note,ipd_prescription_basic.finding_description,ipd_prescription_basic.attachment,ipd_prescription_basic.is_finding_print,prescription_generate.name as generated_by_name,prescription_generate.surname as generated_by_surname,prescription_generate.employee_id as generated_by_employee_id,prescribe_by.name as prescribe_by_name,prescribe_by.surname as prescribe_by_surname,prescribe_by.employee_id as prescribe_by_employee_id, opd_details.id as opd_detail_id,staff.employee_id as doctor_id ". $field_variable);
        $this->db->join("visit_details", "visit_details.id = ipd_prescription_basic.visit_details_id","left");
        $this->db->join("opd_details", "opd_details.id = visit_details.opd_details_id");
        $this->db->join("patients", "patients.id = opd_details.patient_id");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id',"left");
        $this->db->join("staff", "staff.id = visit_details.cons_doctor");
        $this->db->join("staff as prescription_generate", "prescription_generate.id = ipd_prescription_basic.generated_by");
        $this->db->join("staff as prescribe_by", "prescribe_by.id = ipd_prescription_basic.prescribe_by");
        $this->db->where("ipd_prescription_basic.visit_details_id", $visitid);
        $query = $this->db->get("ipd_prescription_basic");

        if ($query->num_rows() > 0) {
            $result            = $query->row();
            $result->medicines = $this->getPrescriptionMedicinesByBasicID($result->prescription_id);
            $result->tests     = $this->getPrescriptionTestsByBasicID($result->prescription_id);
            return $result;

        }
        return false;
    }

    public function getPrescriptionByOPD($visitid)
    {
        $query = $this->db->select('ipd_prescription_basic.*,staff.id as staff_id,pharmacy.medicine_category_id,pharmacy.medicine_name,pharmacy.id as pharmacy_id,ipd_prescription_details.dose_duration_id,ipd_prescription_details.dose_interval_id,dose_interval.name as dose_interval_name,dose_duration.name as dose_duration_name,medicine_dosage.dosage,medicine_dosage.id as dosage_id,medicine_category.medicine_category,ipd_prescription_details.instruction')->join("visit_details", "ipd_prescription_basic.visit_details_id = visit_details.id")->join("opd_details", "visit_details.opd_details_id = opd_details.id")->join("ipd_prescription_details", "ipd_prescription_details.basic_id = ipd_prescription_basic.id")->join("pharmacy", "pharmacy.id = ipd_prescription_details.pharmacy_id")->join("medicine_dosage", "medicine_dosage.id = ipd_prescription_details.dosage")->join("dose_interval", "dose_interval.id = ipd_prescription_details.dose_interval_id")->join("dose_duration", "dose_duration.id = ipd_prescription_details.dose_duration_id")->join("medicine_category", "medicine_category.id = pharmacy.medicine_category_id")->join("staff", "staff.id = opd_details.cons_doctor", 'left')->where("ipd_prescription_basic.visit_details_id", $visitid)->get("ipd_prescription_basic");
        //medicine_dosage
        $result = $query->result_array();

        $i = 0;
        foreach ($result as $key => $value) {
            $visit_details_id = $value["visit_details_id"];
            $check            = $this->db->where("visit_details_id", $visit_details_id)->get('ipd_prescription_basic');
            if ($check->num_rows() > 0) {
                $result[$i]['prescription'] = 'yes';
            } else {
                $result[$i]['prescription'] = 'no';
                $userdata                   = $this->customlib->getUserData();
                if ($this->session->has_userdata('hospitaladmin')) {
                    $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                    if ($doctor_restriction == 'enabled') {
                        if ($userdata["role_id"] == 3) {
                            if ($userdata["id"] == $value["staff_id"]) {

                            } else {
                                $result[$i]['prescription'] = 'not_applicable';
                            }
                        }
                    }
                }
            }
            $i++;
        }
        return $result;
    } 

    public function deletemedicine($prescription_detail_id)
    {
       $this->db->where('id', $prescription_detail_id);
       $this->db->delete('ipd_prescription_details');
    }

    public function getPrescriptionbyprescriptionid($id)
    {
        $query = $this->db->select("ipd_prescription_basic.*")->where("ipd_prescription_basic.id", $id)->get("ipd_prescription_basic");
        return $query->result_array();
    }

}