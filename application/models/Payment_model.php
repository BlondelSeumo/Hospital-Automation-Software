<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payment_model extends MY_Model
{

    public function addPayment($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("payment", $data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Payment id " . $insert_id;
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
            return $insert_id;            
        }
    }

    public function deleteIpdPatientPayment($id)
    {
        $query = $this->db->where('id', $id)
            ->delete('payment');
    }
 
    public function deleteOpdPatientPayment($id)
    {
        $query = $this->db->where('id', $id)
            ->delete('opd_payment');
    }

    public function paymentDetails($id, $ipdid = '')
    {
        $query = $this->db->select('payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = payment.patient_id")->where("payment.patient_id", $id)->where("payment.ipd_id", $ipdid)
            ->get("payment");
        return $query->result_array();
    }

    public function opdpaymentDetails($id, $opdid = '')
    {
        $query = $this->db->select('opd_payment.*,patients.id as pid,patients.note as pnote')
            ->join("opd_details", "opd_details.id = opd_payment.opd_details_id")->join("patients", "patients.id = opd_details.patient_id")->where("patients.id", $id)->where("opd_payment.opd_details_id", $opdid)
            ->get("opd_payment");
        return $query->result_array();
    }

    public function getAllpaymentRecord($id, $opdid='')
    {
        $this->datatables
            ->select('opd_payment.*,patients.id as pid,patients.note as pnote')
            ->join('patients', 'patients.id = opd_payment.patient_id')
            ->searchable('')
            ->orderable('')
            ->sort("opd_payment.id", "desc")
            ->where('opd_payment.patient_id', $id)
            ->where('opd_payment.opd_details_id', $opdid)
            ->from('opd_payment');
        $result = $this->datatables->generate('json');
        return $result ;
    }

    public function opdPaymentDetailspat($id)
    {
        $query = $this->db->select('opd_payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = opd_payment.patient_id")->where("opd_payment.patient_id", $id)
            ->get("opd_payment");
        return $query->result_array();
    }

    public function paymentByID($id)
    {
        $query = $this->db->select('payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = payment.patient_id")->where("payment.id", $id)
            ->get("payment");
        return $query->row();
    }

    public function opdpaymentByID($id)
    {
        $query = $this->db->select('opd_payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = opd_payment.patient_id")->where("opd_payment.id", $id)
            ->get("opd_payment");
        return $query->row();
    }

    public function getOPDBalanceTotal($id)
    {
        $query = $this->db->select("IFNULL(sum(balance_amount),'0') as balance_amount")->join('opd_details','opd_details.id=opd_payment.opd_details_id')->where("opd_details.patient_id", $id)->get("opd_payment");
        return $query->row_array();
    }

    public function getPaidTotal($id, $ipdid = '')
    {
        $query = $this->db->select("IFNULL(sum(amount), '0') as paid_amount")->where("transactions.ipd_id", $ipdid)->get("transactions");
        return $query->row_array();
    }

    public function getopdbilling($id, $opdid = '')
    {
        $query = $this->db->select("IFNULL(sum(net_amount), '0') as billing_amount")->where("opd_billing.patient_id", $id)->where("opd_billing.opd_details_id", $opdid)->get("opd_billing");
        return $query->row_array();
    }

    public function getambulancepaidtotal($id, $ipdid = '')
    {
        $query = $this->db->select("IFNULL(sum(paid), '0') as paid_amount")->where("ambulance_billing.ambulancecall_id", $id)->get("ambulance_billing");
        return $query->row_array();
    }

    public function getotpaidtotal($id)
    {
        $query = $this->db->select("IFNULL(sum(paid), '0') as paid_amount")->where("operation_theatre_billing.operation_id", $id)->get("operation_theatre_billing");
        return $query->row_array();
    }

    public function getbloodissuepaidtotal($id, $ipdid = '')
    {
        $query = $this->db->select("IFNULL(sum(paid), '0') as paid_amount")->where("blood_issue_billing.bloodissue_id", $id)->get("blood_issue_billing");
        return $query->row_array();
    } 

    public function getOPDPaidTotal($id)
    {
        $query = $this->db->select("IFNULL(sum(transactions.amount), '0') as paid_amount")->join('opd_details','opd_details.id=transactions.opd_id')->join('patients','patients.id=opd_details.patient_id')->where("transactions.opd_id", $id)->get("transactions");
        return $query->row_array();        
    }

    public function getOPDbillpaid($id)
    {
       $query = $this->db->select("IFNULL(sum(net_amount), '0') as billpaid_amount")->where("opd_details.patient_id", $id)->join("opd_details","opd_details.id=opd_billing.opd_details_id")->where("opd_billing.opd_details_id", $visitid)->get("opd_billing");
        return $query->row_array();        
    }

    public function getOPDPaidTotalPat($id)
    {
        $query = $this->db->select("IFNULL(sum(paid_amount), '0') as paid_amount")->where("opd_payment.patient_id", $id)->get("opd_payment");
        return $query->row_array();
    }

    public function getChargeTotal($id, $ipdid)
    {
        $query = $this->db->select("IFNULL(sum(apply_charge), '0') as apply_charge")
            ->join('patients', 'patient_charges.patient_id = patients.id', 'inner')
            ->join('charges', 'patient_charges.charge_id = charges.id', 'inner')
            ->join('organisations_charges', 'patient_charges.org_charge_id = organisations_charges.id', 'left')
            ->where('patient_charges.patient_id', $id)
            ->where('patient_charges.ipd_id', $ipdid)
            ->get('patient_charges');
        return $query->row_array();
    }

    public function add_bill($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("ipd_billing", $data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Ipd Billing id " . $insert_id;
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

    public function add_opdbill($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("opd_billing", $data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Opd Billing id " . $insert_id;
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

    public function revertBill($patient_id, $bill_id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $bill_id)->delete("ipd_billing");
        $message = DELETE_RECORD_CONSTANT . " On Ipd Billing id " . $bill_id;
        $action = "Delete";
        $record_id = $bill_id;
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

    public function valid_amount($amount)
    {
        if ($amount <= 0) {
            $this->form_validation->set_message('check_exists', 'The payment amount must be greater than 0');
            return false;
        } else {
            return true;
        }
    }

    public function addOPDPayment($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("opd_payment", $data);
            $message = UPDATE_RECORD_CONSTANT . " On Opd Payment id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);            
        } else {
            $this->db->insert("opd_payment", $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Opd Payment id " . $insert_id;
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

    public function amount_validation()
    {
        $amount = $this->input->post('amount');
        if (!empty($amount)) {
           if($amount == 0.0){
                $this->form_validation->set_message('check_validation', $this->lang->line('enter').' '.$this->lang->line('valid').' '.$this->lang->line('amount'));
                return false; 
           }else{
                return true; 
           }
        } else {
            $this->form_validation->set_message('check_validation', $this->lang->line('amount_field_is_required'));
            return false;
        }
    }

    public function insertOnlinePaymentInTransactions($transaction_data){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("transactions",$transaction_data);
        
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Transactions id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
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

    public function getCaseReferenceId($id,$table_name){
        $result = $this->db->select("case_reference_id")
        ->where("id",$id)
        ->get($table_name)
        ->row_array();
        return $result;
    }

    public function validate_paymentamount()
    {
       $payment_amount = $this->input->post('deposit_amount') ;
        $net_amount    = $this->input->post('net_amount') ;
        if($payment_amount > $net_amount ){
            $this->form_validation->set_message('check_exists', $this->lang->line('amount_should_not_be_greater_than_balance') . ' ' . $net_amount );
            return false;
        }else{
            return true;
        }        
    }
}