<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Onlineappointment_model extends MY_Model
{
    public function getShiftdata($doctor, $day, $doctor_global_shift_id)
    {
        $this->db->select("id,staff_id as doctor_id,date_format(start_time,'%h:%i %p') as start_time ,date_format(end_time,'%h:%i %p') as end_time");
        $this->db->where("staff_id", $doctor);
        $this->db->where("doctor_global_shift_id", $doctor_global_shift_id);
        $this->db->where("day", $day);
        $query  = $this->db->get("doctor_shift_time");
        $result = $query->result();
        return $result;
    }

    public function getShiftByDoctor($doctor)
    {
        $this->db->select("staff_id as doctor_id,day,start_time,end_time");
        $this->db->where("staff_id", $doctor);
        $query  = $this->db->get("doctor_shift_time");
        $result = $query->result();
        return $result;
    }

    public function add($delete_array, $insert_array, $update_array)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        if (!empty($delete_array)) {
            $this->db->where_in('id', $delete_array);
            $this->db->delete('doctor_shift_time');
        }
        if (isset($update_array) && !empty($update_array)) {
            $this->db->update_batch('doctor_shift_time', $update_array, 'id');
        }
        if (isset($insert_array) && !empty($insert_array)) {
            $this->db->insert_batch('doctor_shift_time', $insert_array);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function releaseAppointment()
    {
       $current_date=date('Y-m-d H:i:s');
    
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->where("source", 'online');
        $this->db->where("appointment_status", 'pending');
        $this->db->where("rejected_time <=",$current_date);
        $this->db->delete('appointment');
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function getShiftById($id)
    {
        $this->db->select("start_time, end_time");
        $this->db->where("id", $id);
        $query  = $this->db->get("doctor_shift_time");
        $result = $query->row_array();
        return $result;
    }

    public function getShiftDetails($doctor)
    {
        $this->db->select("consult_duration,charge_id");
        $this->db->where("staff_id", $doctor);
        $query  = $this->db->get("shift_details");
        $result = $query->row_array();
        return $result;
    } 

    public function addAppointment($appointment)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert("appointment", $appointment);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $insert_id;
        }
    }

    public function getAppointments($doctor_id, $shift_id, $date)
    {
        $this->db->select("date");
        $this->db->where("doctor", $doctor_id);
        $this->db->where("doctor_shift_time_id", $shift_id);
        $this->db->where("appointment_status", "approved");
        $this->db->where("date_format(date,'%Y-%m-%d')", $date);
        $query         = $this->db->get("appointment");
        return $result = $query->result();
    }

    public function getAppointmentsBySlot($doctor_id, $shift_id, $date, $slot)
    {
        $this->db->select("date");
        $this->db->where("doctor", $doctor_id);
        $this->db->where("doctor_shift_time_id", $shift_id);
        $this->db->where("appointment_status", "approved");
        $this->db->where("date_format(date,'%Y-%m-%d')", $date);
        $query         = $this->db->get("appointment");
        return $result = $query->result();
    }

    public function checkAppointmentsSlotBooked($doctor, $doctor_shift_time_id, $appointment_date, $current_time,$id = 0)
    {
        $sql = "SELECT `appointment`.* FROM `appointment` WHERE (`doctor` = ".$doctor." AND `doctor_shift_time_id` = ".$doctor_shift_time_id."   AND `date` = ".$this->db->escape($appointment_date)."  AND `appointment_status` = 'approved') or ( `doctor` = ".$doctor." AND `doctor_shift_time_id` = ".$doctor_shift_time_id."   AND `date` = ".$this->db->escape($appointment_date)."  AND `appointment_status` = 'pending' and  DATE_FORMAT(rejected_time, '%Y-%m-%d %H:%i') <= ".$this->db->escape($current_time)." and appointment.id != ".$id.")";   
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getDocData($doctor)
    {
        $this->db->select("consult_duration,charge_id");
        $this->db->where("staff_id", $doctor);
        $query  = $this->db->get("shift_details");
        $result = $query->row_array();
        return $result;
    }

    public function addShiftDetails($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("shift_details", $data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Shift Details id " . $insert_id;
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

    public function updateShiftDetails($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("staff_id", $data["staff_id"]);
        $this->db->update("shift_details", $data);
        $message = UPDATE_RECORD_CONSTANT . " On Shift Details id " . $data["staff_id"];
        $action = "Update";
        $record_id = $data["staff_id"];
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

    public function getPatientSchedule($doctor_id, $date)
    {
        if ($doctor_id != "null") {
            $this->datatables->where("appointment.doctor", $doctor_id);
        }
        if ($date != "null") {
            $this->datatables->where("date_format(appointment.date,'%Y-%m-%d')", $date);
        }
        $this->datatables
            ->select("patients.id,patients.patient_name,patients.mobileno,patients.email,appointment.date,appointment.source")
            ->searchable("patients.patient_name,patients.mobileno,patients.email,appointment.date,appointment.source")
            ->orderable("patients.patient_name,patients.mobileno,'',patients.email,appointment.date")
            ->join("patients", "appointment.patient_id=patients.id")
            ->where("appointment.appointment_status", "approved")
            ->from("appointment");
        return $this->datatables->generate('json');
    }

    public function getPatientOnline($doctor_id, $date, $shift, $isqueue = 0)
    {
        $query = $this->db
            ->select("appointment.id as appointment_id,patients.id,patients.patient_name,patients.mobileno,patients.email,appointment.date,appointment.date,appointment.source")
            ->join("patients", "appointment.patient_id=patients.id")
            ->where("appointment.appointment_status", "approved")
            ->where("appointment.doctor", $doctor_id)
            ->where("date_format(appointment.date,'%Y-%m-%d')", $date)
            ->where("appointment.doctor_shift_time_id", $shift)
            ->where("appointment.source", "Online")
            ->where("is_queue", $isqueue)
            ->order_by("appointment.date")
            ->get("appointment");
        $result = $query->result_array();
        return $result;
    }

    public function getPatientOffline($doctor_id, $date, $shift, $isqueue = 0)
    {
        $query = $this->db
            ->select("appointment.id as appointment_id,patients.id,patients.patient_name,patients.mobileno,patients.email,appointment.date, appointment.source")
            ->join("patients", "appointment.patient_id=patients.id")
            ->where("appointment.appointment_status", "approved")
            ->where("appointment.doctor", $doctor_id)
            ->where("date_format(appointment.date,'%Y-%m-%d')", $date)
            ->where("appointment.source", "Offline")
            ->where("appointment.doctor_shift_time_id", $shift)
            ->where("is_queue", $isqueue)
            ->order_by("appointment.date")
            ->get("appointment");
        $result = $query->result_array();
        return $result;
    }

    public function globalShift()
    {
        $query = $this->db
            ->select("id,name,date_format(start_time,'%h:%i %p') as start_time ,date_format(end_time,'%h:%i %p') as end_time")
            ->get("global_shift");
        $result = $query->result_array();
        return $result;
    }

    public function globalShiftlist()
    {
        $query = $this->db
            ->select("id,name,start_time,end_time")
            ->get("global_shift");
        $result = $query->result_array();
        return $result;
    }

    public function getGlobalShift($id)
    {
        $this->db->select("id,name,date_format(start_time,'%h:%i %p') as start_time ,date_format(end_time,'%h:%i %p') as end_time");
        $this->db->where("id", $id);
        $query  = $this->db->get("global_shift");
        $result = $query->row_array();
        return $result;
    }

    public function deleteQueue($where_in)
    {
        $this->db->where_in("appointment_id", $where_in);
        $this->db->delete("appointment_queue");
        $this->db->where_in("id", $where_in);
        $this->db->set("is_queue", 0);
        $this->db->update("appointment");
    }

    public function getAppointmentFromQueue($doctor, $date, $shift)
    {
        $this->db->select("appointment_id");
        $this->db->where("staff_id", $doctor);
        $this->db->where("date_format(date,'%Y-%m-%d')", $date);
        $this->db->where("shift_id", $shift);
        $query  = $this->db->get("appointment_queue");
        $result = $query->result_array();
        return $result;
    }

    public function addGlobalShift($shift)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================        
        $this->db->insert("global_shift", $shift);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Global Shift id " . $insert_id;
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

    public function updateGlobalShift($shift)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================        

        $this->db->where("id", $shift["id"]);
        $this->db->update("global_shift", $shift);

        $message = UPDATE_RECORD_CONSTANT . " On Global Shift id " . $shift["id"];
        $action = "Update";
        $record_id = $shift["id"];
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

    public function deleteGlobalShift($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        $this->db->where('id', $id)->delete('global_shift');
        $message = DELETE_RECORD_CONSTANT . " On Global Shift id " . $id;
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

    public function doctorglobalShift()
    {
        $this->db->select("doctor_global_shift.id,staff.name as first_name,staff.surname,global_shift.name");
        $this->db->join("global_shift", "global_shift.id=doctor_global_shift.global_shift_id", "left");
        $this->db->join("staff", "doctor_global_shift.staff_id=staff.id", "left");
        $query  = $this->db->get("doctor_global_shift");
        $result = $query->result_array();
        return $result;
    }

    public function globalDoctorShift()
    {
        $this->db->select("staff.id,staff.name as first_name,staff.surname,staff.employee_id");
        $this->db->join("staff_roles", "staff_roles.staff_id = staff.id", "left");
        $this->db->where("staff_roles.role_id", "3");
        $this->db->where("staff.is_active", "1");
        $query  = $this->db->get("staff");
        $result = $query->result_array();
        return $result;
    }

    public function getGlobalDoctorShift($doctor_id)
    {
        $this->db->select("global_shift.id,global_shift.name");
        $this->db->where("staff_id", $doctor_id);
        $this->db->join("global_shift", "global_shift.id=doctor_global_shift.global_shift_id", "left");
        $query  = $this->db->get("doctor_global_shift");
        $result = $query->result_array();
        return $result;
    }

    public function getGlobalDoctorShifts($doctor_id)
    {
        $this->db->select("staff_id as doctor_id,global_shift_id");
        $this->db->join("global_shift as g", "dg.global_shift_id=g.id", "left");
        $this->db->where("dg.staff_id", $doctor_id);
        $query = $this->db->get("doctor_global_shift as dg");
        $shift = $query->result_array();
        return $shift;
    }

    public function getDoctorGlobalShift($id)
    {
        $this->db->select();
        $this->db->join("global_shift as g", "dg.global_shift_id=g.id", "left");
        $this->db->where("dg.id", $id);
        $query = $this->db->get("doctor_global_shift as dg");
        $shift = $query->row_array();
        return $shift;
    }

    public function editDoctorGlobalShift($insert_data, $delete_data)
    {
        if (!empty($insert_data)) {
            $this->db->insert("doctor_global_shift", $insert_data);
        }
        if (!empty($delete_data)) {
            $this->db->where("staff_id", $delete_data["staff_id"]);
            $this->db->where("global_shift_id", $delete_data["global_shift_id"]);
            $this->db->delete("doctor_global_shift");
        }
    }

    public function doctorShiftById($doctor_id)
    {
        $this->db->select("g.id,g.name,dg.id as doctor_global_shift_id");
        $this->db->join("global_shift as g", "dg.global_shift_id=g.id", "left");
        $this->db->where("dg.staff_id", $doctor_id);
        $query  = $this->db->get("doctor_global_shift as dg");
        $result = $query->result_array();
        return $result;
    }

    public function insertQueuePositions($insert_data, $update_data)
    {
        $this->db->insert_batch("appointment_queue", $insert_data);
        $this->db->update_batch("appointment", $update_data, "id");
    }

    public function updateQueue($queueData)
    {
        $status = $this->db->update_batch("appointment_queue", $queueData, "id");
        return $status;
    }

    public function getLastQueuePosition($doctor_id, $date, $shift_id)
    {
        $this->db->select_max("position");
        $this->db->join("appointment", "appointment.id=appointment_queue.appointment_id");
        $this->db->where("appointment.doctor", $doctor_id);
        $this->db->where("appointment_queue.shift_id", $shift_id);
        $this->db->where("date_format(appointment.date,'%Y-%m-%d')", $date);
        $query  = $this->db->get("appointment_queue");
        $result = $query->row_array();
        return $result;
    }

    public function getPatientQueue($doctor_id, $date, $shift)
    {
        if ($date != "") {
            $this->db->where("date_format(appointment.date,'%Y-%m-%d')", $date);
        }
        if ($shift != "") {
            $this->db->where("appointment_queue.shift_id", $shift);
        }

        $query = $this->db
            ->select("appointment_queue.id as queue_id, appointment_queue.position, appointment.id as appointment_id,patients.id as patient_unique_id,patients.patient_name,patients.mobileno,patients.email,appointment.date, appointment.source")
            ->join("appointment", "appointment.id=appointment_queue.appointment_id")
            ->join("patients", "appointment.patient_id=patients.id")
            ->where("appointment.doctor", $doctor_id)
            ->order_by("appointment_queue.position", "asc")
            ->get("appointment_queue");
        $result = $query->result_array();
        return $result;
    }

    public function checkAppointmentExists($appointment_id)
    {
        $this->db->select("appointment.*, patients.email, patients.patient_name as name,patients.mobileno,patients.id as patient_id");
        $this->db->join("doctor_shift_time", "doctor_shift_time.id=appointment.doctor_shift_time_id");
        $this->db->join("patients", "patients.id=appointment.patient_id");
        $this->db->where("appointment.id", $appointment_id);
        $query  = $this->db->get("appointment");
        $result = $query->row();
        return $result;
    }

    public function getAppointmentDetails($appointment_id)
    {
        $this->db->select("shift_details.charge_id,shift_details.id as shift_id, patients.email, patients.patient_name as name,patients.mobileno,patients.id as patient_id,appointment.doctor,appointment.doctor_shift_time_id,appointment.date");
        $this->db->join("appointment", "appointment.doctor=shift_details.staff_id", "left");
        $this->db->join("patients", "patients.id=appointment.patient_id", "left");
        $this->db->where("appointment.id", $appointment_id);
        $query  = $this->db->get("shift_details");
        $result = $query->row();
        return $result;
    }

    public function paymentSuccess($payment_data, $transaction)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert("appointment_payment", $payment_data);
        $insert_id = $this->db->insert_id();
        $data = array('appointment_status' => 'approved');
        $this->db->insert("transactions", $transaction);
        $this->db->update("appointment", $data, "id=" . $payment_data['appointment_id']);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $payment_data['appointment_id'];
        }
    }

    public function getDoctorGlobalShiftId($doctor_id, $shift_id)
    {
        $this->db->select("doctor_global_shift.*");
        $this->db->where("global_shift_id", $shift_id);
        $this->db->where("staff_id", $doctor_id);
        $query  = $this->db->get("doctor_global_shift");
        $result = $query->row_array();
        return $result;
    }

    public function getDoctorShiftTimeId($doctor, $global_shift_id, $day)
    {
        $this->db->select("doctor_shift_time.id");
        $this->db->join("doctor_shift_time", "doctor_shift_time.doctor_global_shift_id=doctor_global_shift.id", "left");
        $this->db->where("doctor_global_shift.staff_id", $doctor);
        $this->db->where("doctor_global_shift.global_shift_id", $global_shift_id);
        $this->db->where("doctor_shift_time.day", $day);
        $query  = $this->db->get("doctor_global_shift");
        $result = $query->row();
        return $result;
    }

    public function getDoctorShiftTime($doctor, $global_shift_id)
    {
        $this->db->select("doctor_shift_time.id");
        $this->db->join("doctor_shift_time", "doctor_shift_time.doctor_global_shift_id=doctor_global_shift.id", "left");
        $this->db->where("doctor_global_shift.staff_id", $doctor);
        $this->db->where("doctor_global_shift.global_shift_id", $global_shift_id);
        $query  = $this->db->get("doctor_global_shift");
        $result = $query->result_array();
        return $result;
    }
}
