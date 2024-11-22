<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Gateway_ins_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function add_gateway_ins_response($gateway_ins_response){
     	$this->db->insert('gateway_ins_response',$gateway_ins_response);
     	return $this->db->insert_id();
    }
 
    public function get($unique_id,$gateway_name){
     	return $this->db->select('gateway_ins.unique_id,gateway_ins.id as gateway_ins_id,gateway_ins.parameter_details,transactions_processing.*')->from('gateway_ins')->join('transactions_processing','`transactions_processing`.`gateway_ins_id`=gateway_ins.id')->where(array('gateway_ins.gateway_name'=>$gateway_name,'gateway_ins.unique_id'=>$unique_id))->get()->row_array();
    }

    public function get_gateway_ins($unique_id,$gateway_name){
        return $this->db->select('*')->from('gateway_ins')->where(array('gateway_ins.gateway_name'=>$gateway_name,'gateway_ins.unique_id'=>$unique_id))->get()->row_array();
    }

    public function add_gateway_ins($gateway_ins){
     	$this->db->insert('gateway_ins',$gateway_ins);
     	return $this->db->insert_id();
    }

    public function add_transactions_processing($gateway_ins){
        $this->db->insert('transactions_processing',$gateway_ins);
        return $this->db->insert_id();
    }

    public function update_gateway_ins($gateway_ins){
     	$this->db->where('id', $gateway_ins['id']);
     	$this->db->update('gateway_ins',$gateway_ins);
     	return $gateway_ins['id'];
    }

    public function get_gateway_details($type){
        $this->db->select('*')->from('payment_settings');
        $this->db->where('payment_type', $type);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_statusByUnique_id($unique_id,$gateway_name){     	
     	return $this->db->select('*')->from('gateway_ins')->where(array('gateway_ins.gateway_name'=>$gateway_name,'gateway_ins.unique_id'=>$unique_id))->get()->row_array();
    }

    public function deleteBygateway_ins_id($id){        
        $this->db->where('gateway_ins_id', $id);
        $this->db->delete('transactions_processing');
    }

    public function getAppointmentDetails($appointment_id)
    {
        $this->db->select("shift_details.charge_id, patients.email, patients.patient_name as name,patients.mobileno,patients.id as patient_id,appointment.doctor,appointment.doctor_shift_time_id,appointment.date");
        $this->db->join("appointment","appointment.doctor=shift_details.staff_id","left");
        $this->db->join("patients","patients.id=appointment.patient_id","left");
        $this->db->where("appointment.id",$appointment_id);
        $query  = $this->db->get("shift_details");
        $result = $query->row();
        return $result;
    }

    public function paymentSuccess($payment_data, $transaction)
    {
        $this->db->insert("appointment_payment",$payment_data);
        $insert_id=$this->db->insert_id();
        $data = array('appointment_status' => 'approved');
        $this->db->insert("transactions",$transaction);
        $this->db->update("appointment", $data,"id=".$payment_data['appointment_id']);
        return $insert_id;
    }
}