<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Appointment_model extends MY_Model
{

//========================================================================================
    public function add($appointment)
    {
        $this->db->insert('appointment', $appointment);
        // return $this->db->insert_id();
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On Appointment Created " . $insert_id;

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
            //return $return_value;
        }
        return $insert_id;

    }

//=========================================================================================
    public function searchFullText()
    {
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $userdata           = $this->customlib->getUserData();
        $role_id            = $userdata['role_id'];
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $user_id  = $userdata["id"];
                $doctorid = $user_id;
                $this->db->where('appointment.doctor', $user_id);
            }
        }
        $this->db->select('appointment.*,staff.name, IFNULL(patients.patient_name, appointment.patient_name) as patient_name,IFNULL(patients.gender, appointment.gender) as gender, IFNULL(patients.email, appointment.email) as email, IFNULL(patients.mobileno, appointment.mobileno) as mobileno,staff.surname');
        $this->db->join('staff', 'appointment.doctor = staff.id', "inner");
        $this->db->join('patients', 'appointment.patient_id = patients.id', "left");
        $this->db->where('`appointment`.`doctor`=`staff`.`id`');
        $this->db->order_by('`appointment`.`date`', 'desc');
        $query = $this->db->get('appointment');
        return $query->result_array();
    }

    public function getAllappointmentRecord()
    {

        $userdata           = $this->customlib->getUserData();
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
       
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('appointment', 1);
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'appointment.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }
         if ($doctor_restriction == 'enabled') {
            if ($userdata["role_id"] == 3) {
                $this->datatables->where('appointment.doctor', $userdata['id']);
            }
        }
        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $this->datatables
            ->select('appointment.appointment_status,appointment.id,appointment.date,appointment.source,appointment_payment.paid_amount,appointment_payment.discount_percentage,staff.id as sid,staff.name,patients.id as pid, patients.patient_name as patient_name,patients.gender as gender, patients.email as email, patients.mobileno as mobileno,staff.surname,staff.employee_id,appoint_priority.appoint_priority as priorityname,appointment.live_consult' . $field_variable)  
            ->join('appointment_payment', "appointment_payment.appointment_id=appointment.id")
            ->join('staff', 'appointment.doctor = staff.id', "inner")
            ->join('patients', 'appointment.patient_id = patients.id', "left")
            ->join('appoint_priority', 'appoint_priority.id = appointment.priority', "left")
            ->searchable('patients.patient_name,appointment_payment.paid_amount,appointment.id,appointment.date,patients.mobileno,patients.gender,staff.name,appointment.source,appoint_priority.appoint_priority,appointment.live_consult' . $custom_field_column)	
            ->orderable('patients.patient_name,appointment.id,appointment.date,patients.mobileno,patients.gender,staff.name,appointment.source,appoint_priority.appoint_priority,appointment.live_consult' . $custom_field_column . ', appointment_payment.discount_percentage,appointment_payment.paid_amount')			
            ->sort('appointment.date', 'desc')
            ->from('appointment');
        return $this->datatables->generate('json');
    }
//==========================================================================================

    public function getMaxId()
    {
        $query  = $this->db->select('max(id) as maxid')->get("`appointment`");
        $result = $query->row_array();
        return $result["maxid"];
    }

//==========================================================================================
    public function getDetails($id)
    {
        $this->db->select('appointment.*,staff.name,staff.surname,patients.patient_name as patient_name,patients.gender as gender, patients.email as email, patients.mobileno as mobileno,appoint_priority.appoint_priority');
        $this->db->join('staff', 'appointment.doctor = staff.id', "left");
        $this->db->join('patients', 'appointment.patient_id = patients.id', "left");
        $this->db->join('appoint_priority', 'appoint_priority.id = appointment.priority', "left");
        $this->db->where('appointment.id', $id);
        $query = $this->db->get('appointment');
        return $query->row_array();
    }

    public function getDetailsFornotification($id)
    {
        $this->db->select('appointment.*,appointment.id as appointment_no,staff.name as staff_name,staff.surname as staff_surname,patients.gender as gender, patients.email as email, patients.mobileno as mobileno,appoint_priority.appoint_priority');
        $this->db->join('staff', 'appointment.doctor = staff.id', "left");
        $this->db->join('patients', 'appointment.patient_id = patients.id', "left");
        $this->db->join('appoint_priority', 'appoint_priority.id = appointment.priority', "left");
        $this->db->where('appointment.id', $id);
        $query = $this->db->get('appointment');
        return $query->row_array();
    }

    public function getDetailsAppointment($id,$is_patient=null){

		$i=0 ;
		if($is_patient==1){
			$custom_fields             = $this->customfield_model->get_custom_fields('appointment','','','',1);
		}else{
			$custom_fields             = $this->customfield_model->get_custom_fields('appointment');
		}
    
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->db->join('custom_field_values as ' . $tb_counter, 'appointment.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }  
         
        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array); 
    
        $this->db->select('appointment.*,blood_bank_products.name as blood_group,appointment_payment.paid_amount,appointment_payment.standard_amount,appointment_payment.discount_percentage, appointment_queue.position as appointment_serial_no, `department`.`department_name`,appointment_payment.note as payment_note,visit_details.opd_details_id,transactions.received_by,transactions.id as transaction_id ,transactions.payment_mode,transactions.cheque_date , transactions.cheque_no, transactions.amount, transactions.attachment, appoint_priority.appoint_priority,staff.name,staff.surname,staff.employee_id,patients.mobileno as patient_mobileno,patients.email as patient_email,patients.patient_name as patients_name,patients.gender as patients_gender,patients.age,patients.day,patients.month,global_shift.name as global_shift_name,doctor_shift_time.start_time,doctor_shift_time.end_time,doctor_global_shift.global_shift_id as shift_id,doctor_shift_time.id as slot_id,'.$field_variable); 

        $this->db->join('transactions', 'appointment.id = transactions.appointment_id', "left");  
        $this->db->join('staff', 'appointment.doctor = staff.id', "left");
        $this->db->join('department', 'department.id = staff.department_id', "left");
        $this->db->join('appoint_priority', 'appoint_priority.id = appointment.priority', "left");
        $this->db->join('patients', 'appointment.patient_id = patients.id', "left");        
        $this->db->join('doctor_shift_time', 'doctor_shift_time.id = appointment.doctor_shift_time_id', 'left');       
        $this->db->join('doctor_global_shift', 'doctor_global_shift.id = doctor_shift_time.doctor_global_shift_id', 'left');        
        $this->db->join('global_shift', 'global_shift.id = doctor_global_shift.global_shift_id', 'left');       
        $this->db->join('visit_details', 'visit_details.id = appointment.visit_details_id', 'left');
        $this->db->join("appointment_payment","appointment_payment.appointment_id=appointment.id","left");
        $this->db->join('appointment_queue', 'appointment_queue.appointment_id = appointment.id', "left");
         $this->db->join('blood_bank_products', '`patients`.`blood_bank_product_id` = blood_bank_products.id', "left");
        $this->db->where('appointment.id', $id);
        $query = $this->db->get('appointment');
        return $query->row_array();
    }

//=========================================================================================
    public function update($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('appointment', $data);
            $message   = UPDATE_RECORD_CONSTANT . "On Appointment Updated " . $data['id'];
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
        }
    }

    //=========================================================================================
    public function updateAppointment($data, $payment_data, $transaction_data, $opd_details, $visit_details, $charge)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('appointment', $data);
            $this->db->update("appointment_payment", $payment_data, array("appointment_id" => $payment_data['appointment_id']));
            $this->db->update("transactions", $transaction_data, array("appointment_id" => $transaction_data['appointment_id']));
            $this->db->update("opd_details", $opd_details, array("id" => $opd_details['id']));
            $this->db->update("visit_details", $visit_details, array("id", $visit_details['id']));
            $this->db->update("patient_charges", $charge, array("opd_id" => $opd_details['id']));
            $message   = UPDATE_RECORD_CONSTANT . "On Appointment Updated " . $data['id'];
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
        }
    }

//=========================================================================================
    public function frontDelete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('appointment');
        $message   = DELETE_RECORD_CONSTANT . " On appointment id " . $id;
        $action    = "Delete";
        $record_id = $id;
        //$this->log($message, $record_id, $action);
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

//=========================================================================================
    public function delete($id, $visit_details_id, $opd_id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->delete("appointment", array("id" => $id));        
        $this->db->delete("appointment_payment", array("appointment_id", $id));        
        $this->db->delete("visit_details", array("id" => $visit_details_id));        
        $this->db->delete("transactions", array("appointment_id" => $id));        
        $this->db->delete("patient_charges", array("opd_id" => $opd_id));        
        $this->db->delete("opd_details", array("id" => $opd_id));
        
        $message   = DELETE_RECORD_CONSTANT . " On Appointment Deleted id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id, 'appointment');
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

    public function deleteAppointment($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->delete("appointment", array("id" => $id));
        $message   = DELETE_RECORD_CONSTANT . " On Appointment Deleted id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id, 'appointment');
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

//=========================================================================================
    public function getAppointment($id = null)
    {
        $query = $this->db->order_by('id', 'desc')->get('appointment');
        return $query->result_array();
    }

//=========================================================================================
    public function status($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->update("appointment", $data);
        $message   = UPDATE_RECORD_CONSTANT . " On Appointment id " . $id;
        $action    = "Update";
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

    public function move($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->update("appointment", $data);
        $message   = UPDATE_RECORD_CONSTANT . " On Appointment id " . $id;
        $action    = "Update";
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

    public function getpatientDetails($id)
    {
        $query = $this->db->select('patients.*')
            ->where('patients.patient_unique_id', $id)
            ->get('patients');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }

    public function getappointbypat($id)
    {
        $query = $this->db->select('appointment.*')
            ->where('patient_id', $id)
            ->get('appointment');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function saveAppointmentPayment($payment_data, $transaction)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert("appointment_payment", $payment_data);
        $this->db->insert("transactions", $transaction);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function moveToOpd($opd_details, $visit_details, $charges, $appointment_id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert('case_references', array('id' => null));
        $case_id                          = $this->db->insert_id();
        $opd_details['case_reference_id'] = $case_id;
        $this->db->insert('opd_details', $opd_details);
        $opd_id            = $this->db->insert_id();
        $charges['opd_id'] = $opd_id;
        $this->db->insert('patient_charges', $charges);
        $patient_charge_id                  = $this->db->insert_id();
        $visit_details['opd_details_id']    = $opd_id;
        $visit_details['patient_charge_id'] = $patient_charge_id;
        $this->db->insert('visit_details', $visit_details);
        $visit_details_id                      = $this->db->insert_id();
        $transaction_data['case_reference_id'] = $case_id;
        $transaction_data['opd_id']            = $opd_id;
        $this->db->update("transactions", $transaction_data, array("appointment_id" => $appointment_id));
        $appointment_data['case_reference_id'] = $case_id;
        $appointment_data['visit_details_id']  = $visit_details_id;
        $this->db->update("appointment", $appointment_data, array("id" => $appointment_id));
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return ['opd_id'=>$opd_id,'visit_details_id'=>$visit_details_id];
        }
    }

    public function getPaymentByAppointmentId($appointment_id)
    {
        $result = $this->db->select('appointment_payment.*')
            ->where('appointment_id', $appointment_id)
            ->get('appointment_payment')
            ->row();
        return $result;
    }
	
	public function updateappointmentpayment($appointment_id, $data)
    {             
        $this->db->where('appointment_id', $appointment_id);
        $this->db->update('appointment_payment', $data);       
    }
}
