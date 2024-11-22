<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Referral_payment_model extends MY_Model
{

    public function get_payment()
    {
        $this->db->select("payment.billing_id,payment.id, person.name, patients.patient_name,patients.id as patient_id, type.name as type, payment.bill_amount, payment.percentage, payment.amount,prefixes.prefix");
        $this->db->join("referral_type type", "type.id=payment.referral_type", "left");
        $this->db->join("prefixes", "type.prefixes_type=prefixes.type", "inner");
        $this->db->join("referral_person person", "person.id=payment.referral_person_id");
        $this->db->join("patients", "patients.id=payment.patient_id", "left");
        $query   = $this->db->get("referral_payment payment");
        $payment = $query->result_array();
        return $payment;
    }
 
    public function add($payment)
    {        
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $this->db->insert('referral_payment', $payment);
        
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Referral Payment id " . $insert_id;
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

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id)->delete('referral_payment');        
        $message = DELETE_RECORD_CONSTANT . " On Referral Payment id " . $id;
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

    public function get($id)
    {
        $payment = $this->db->select()->where('id', $id)->get("referral_payment")->row_array();
        return $payment;
    }

    public function update($payment)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start=========================== 

        $this->db->where('id', $payment['id'])->update("referral_payment", $payment);
        
        $message = UPDATE_RECORD_CONSTANT . " On Referral Payment id " . $payment['id'];
        $action = "Update";
        $record_id = $payment['id'];
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

    public function get_commission($payee, $type)
    {
        $this->db->select("commission.commission");
        $this->db->where(array("referral_person_id" => $payee, "referral_type_id" => $type));
        $query  = $this->db->get("referral_person_commission commission");
        $result = $query->row_array();
        return $result["commission"];
    }

    public function getPatientBill($patient_id)
    {
        $this->db->select("amount");
        $this->db->where("patient_name", $patient_id);
        $query  = $this->db->get("ambulance_call");
        $result = $query->row_array();
        return $result["amount"];
    }

    public function get_opdBillNo($patient_id){
        return $this->db->select('opd_details.id as bill_no,opd_details.case_reference_id as case_id,(select prefixes.prefix from prefixes where prefixes.type="opd_no") as prefixe_name,(select prefixes.prefix from prefixes where prefixes.type="opd_no") as prefixe_name')->from('opd_details')->where(array('patient_id'=>$patient_id,'discharged'=>'no'))->get()->result_array();
    }

    public function get_ipdBillNo($patient_id){
        return $this->db->select('ipd_details.id as bill_no,ipd_details.case_reference_id as case_id,(select prefixes.prefix from prefixes where prefixes.type="ipd_no") as prefixe_name')->from('ipd_details')->where(array('patient_id'=>$patient_id,'discharged'=>'no'))->get()->result_array();
    }

    public function get_pharmacyBillNo($patient_id){
        return $this->db->select('pharmacy_bill_basic.id as bill_no,pharmacy_bill_basic.case_reference_id as case_id,(select prefixes.prefix from prefixes where prefixes.type="pharmacy_billing") as prefixe_name')->from('pharmacy_bill_basic')->where(array('patient_id'=>$patient_id))->get()->result_array();
    }

    public function get_pathologyBillNo($patient_id){
        return $this->db->select('pathology_billing.id as bill_no,pathology_billing.case_reference_id as case_id,(select prefixes.prefix from prefixes where prefixes.type="pathology_billing") as prefixe_name')->from('pathology_billing')->where(array('patient_id'=>$patient_id))->get()->result_array();
    }

    public function get_radiologyBillNo($patient_id){
        return $this->db->select('radiology_billing.id as bill_no,radiology_billing.case_reference_id as case_id,(select prefixes.prefix from prefixes where prefixes.type="radiology_billing") as prefixe_name')->from('radiology_billing')->where(array('patient_id'=>$patient_id))->get()->result_array();
    }

    public function get_bloodbankBillNo($patient_id){
        return $this->db->select('blood_issue.id as bill_no,blood_issue.case_reference_id as case_id,(select prefixes.prefix from prefixes where prefixes.type="blood_bank_billing") as prefixe_name')->from('blood_issue')->where(array('patient_id'=>$patient_id))->get()->result_array();
    }

    public function get_ambulanceBillNo($patient_id){
        return $this->db->select('ambulance_call.id as bill_no,ambulance_call.case_reference_id as case_id,(select prefixes.prefix from prefixes where prefixes.type="ambulance_call_billing") as prefixe_name')->from('ambulance_call')->where(array('patient_id'=>$patient_id))->get()->result_array();
    }
 
    public function get_opdBillAmount($bill_no){
        return $this->db->select('sum(`amount`) as total_bill')->from('patient_charges')->where('opd_id',$bill_no)->group_by('opd_id')->get()->row_array();
    }

    public function get_ipdBillAmount($bill_no){
       return $this->db->select('sum(`amount`) as total_bill')->from('patient_charges')->where('ipd_id',$bill_no)->group_by('ipd_id')->get()->row_array();
    }

    public function get_pharmacyBillAmount($bill_no){
        return $this->db->select('net_amount as total_bill')->from('pharmacy_bill_basic')->where(array('id'=>$bill_no))->get()->row_array();
    }

    public function get_pathologyBillAmount($bill_no){
       
       return $this->db->select('pathology_billing.net_amount as total_bill')->from('pathology_billing')->where('id',$bill_no)->get()->row_array();
    }
    
    public function get_radiologyBillAmount($bill_no){
         return $this->db->select('radiology_billing.net_amount as total_bill')->from('radiology_billing')->where('id',$bill_no)->get()->row_array();
    }
    
    public function get_bloodbankBillAmount($bill_no){
        return $this->db->select('net_amount as total_bill')->from('blood_issue')->where(array('id'=>$bill_no))->get()->row_array();
    }

    public function get_ambulanceBillAmount($bill_no){
        return $this->db->select('net_amount as total_bill')->from('ambulance_call')->where(array('id'=>$bill_no))->get()->row_array();
    }

    public function check_billid($billing_id){
        $query = $this->db->query('select billing_id from referral_payment where billing_id='.$this->db->escape($billing_id)); 
        return $query->num_rows();
    }
}
