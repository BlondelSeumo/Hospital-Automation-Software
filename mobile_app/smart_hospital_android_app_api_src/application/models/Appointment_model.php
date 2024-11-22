<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Appointment_model extends CI_Model
{

    public function getDetailsAppointment($id)
    {    
        $this->db->select('appointment.*,appointment_payment.paid_amount,appointment_payment.standard_amount,appointment_payment.discount_percentage, appointment_queue.position as appointment_serial_no, `department`.`department_name`,appointment_payment.note as payment_note,visit_details.opd_details_id,transactions.id as transaction_id ,transactions.payment_mode,transactions.cheque_date , transactions.cheque_no, transactions.amount, transactions.attachment, appoint_priority.appoint_priority,staff.name,staff.surname,staff.employee_id,patients.mobileno as patient_mobileno,patients.email as patient_email,patients.patient_name as patients_name,patients.gender as patients_gender,patients.age,patients.day,patients.month,global_shift.name as global_shift_name,doctor_shift_time.start_time,doctor_shift_time.end_time,doctor_global_shift.global_shift_id as shift_id,doctor_shift_time.id as slot_id');		
		
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
		
        $this->db->where('appointment.id', $id);
        $query = $this->db->get('appointment');
        $result = $query->row_array();       
               
        if($result['payment_note'] == ''){
           $result['payment_note'] = '';
        }if($result['global_shift_name'] == ''){
           $result['global_shift_name'] = '';
        }if($result['payment_mode'] == ''){
           $result['payment_mode'] = '';
        }if($result['cheque_date'] == ''){
           $result['cheque_date'] = '';
        }if($result['cheque_no'] == ''){
           $result['cheque_no'] = '';
        }if($result['attachment'] == ''){
           $result['attachment'] = '';
        }if($result['appoint_priority'] == ''){
           $result['appoint_priority'] = '';
        }if($result['amount'] == ''){
           $result['amount'] = '';
        }if($result['case_reference_id'] == ''){
           $result['case_reference_id'] = '';
        }if($result['appointment_serial_no'] == ''){
           $result['appointment_serial_no'] = '';
        }
                    
        $customfield =  $this->customfield_model->getcustomfieldswithvalue($result['id'],'appointment');           
        $result ['customfield'] = $customfield;       
            
        return $result;            
    }   
}
