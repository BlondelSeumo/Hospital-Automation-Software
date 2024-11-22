<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Referralpayment extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("referral_payment_model");
        $this->load->model("referral_person_model");
        $this->load->library("form_validation");
        $this->load->library('system_notification');
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('referral_payment', 'can_add')) {
            access_denied();
        }

        $data = array();
        $this->form_validation->set_rules("patient_id", $this->lang->line('patient'), 'required|trim|xss_clean');
        $this->form_validation->set_rules("payee", $this->lang->line('payee'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("percentage", $this->lang->line('commission_percentage'), 'required|trim|xss_clean');
        $this->form_validation->set_rules("commission_amount", $this->lang->line('commission_amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("patient_type", $this->lang->line('patient_type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("bill_amount", $this->lang->line('patient_bill_amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("bill_no", $this->lang->line('bill_no_case_id'), 'trim|required|xss_clean|callback_check_billid');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_id'        => form_error('patient_id'),
                'payee'             => form_error('payee'),
                'percentage'        => form_error('percentage'),
                'commission_amount' => form_error('commission_amount'),
                'percentage'        => form_error('percentage'),
                'patient_type'      => form_error('patient_type'),
                'bill_amount'       => form_error('bill_amount'),
                'bill_no'           => form_error('bill_no'),

            );
            $data = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $payment = array(
                "referral_person_id" => $this->input->post("payee"),
                "patient_id"         => $this->input->post("patient_id"),
                "referral_type"      => $this->input->post("patient_type"),
                "billing_id"         => $this->input->post("bill_no"),
                "bill_amount"        => $this->input->post("bill_amount"),
                "percentage"         => $this->input->post("percentage"),
                "amount"             => $this->input->post("commission_amount"),
                "date"               => date("Y-m-d H:i:s"),
            );

            $this->referral_payment_model->add($payment);
            $data = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

            $referral_type   = $this->notificationsetting_model->getreferraltypeDetails($this->input->post("patient_type"));
            $referral_person = $this->notificationsetting_model->getreferralpersonDetails($this->input->post("payee"));

            $event_data = array(
                'patient_id'            => $this->input->post("patient_id"),
                'patient_type'          => $this->lang->line($referral_type['name']),
                'bill_no'               => $this->customlib->getSessionPrefixByType($referral_type['prefixes_type']) . $this->input->post('bill_no'),
                'patient_bill_amount'   => number_format((float) $this->input->post("bill_amount"), 2, '.', ''),
                'payee'                 => $referral_person['name'],
                'commission_percentage' => $this->input->post("percentage"),
                'commission_amount'     => $this->input->post("commission_amount"),
            );

            $this->system_notification->send_system_notification('add_referral_payment', $event_data);
        }
        echo json_encode($data);
    }

    public function check_billid()
    {
        $billing_id = $this->input->post('bill_no');
        $check      = $this->referral_payment_model->check_billid($billing_id);
        if ($check > 0) {
            $this->form_validation->set_message('check_billid', $this->lang->line('referral_payment_already_generated_for_this_bill_no'));
            return false;
        } else {
            return true;
        }
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('referral_payment', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->referral_payment_model->delete($id);
            echo json_encode(array("status" => 1, "msg" => $this->lang->line("delete_message")));
        }
    }

    public function get($id)
    {
        $data = $this->referral_payment_model->get($id);
        echo json_encode($data);
    }

    public function update()
    {
        $data = array();
        $this->form_validation->set_rules("commission_percentage", $this->lang->line('commission_percentage'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("commission_amount", $this->lang->line('commission_amount'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                "commission_percentage" => form_error('commission_percentage'),
                "commission_amount"     => form_error('commission_amount'),
            );
            $data = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $payment = array(
                "id"         => $this->input->post('paymentid'),
                "percentage" => $this->input->post('commission_percentage'),
                "amount"     => $this->input->post('commission_amount'),
            );

            $this->referral_payment_model->update($payment);
            $data = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($data);
    }

    public function getCommission()
    {
        $type       = $this->input->post("type");
        $payee      = $this->input->post("payee");
        $percentage = $this->referral_payment_model->get_commission($payee, $type);
		 
        echo $percentage;
    }

    public function getBillNo()
    {
        $referral_type = $this->input->post('type');
        $patient_id    = $this->input->post('patient_id');
        if ($referral_type == 1) {
            //opd
            $result = $this->referral_payment_model->get_opdBillNo($patient_id);
        } elseif ($referral_type == 2) {
            //ipd
            $result = $this->referral_payment_model->get_ipdBillNo($patient_id);
        } elseif ($referral_type == 3) {
            //pharmacy
            $result = $this->referral_payment_model->get_pharmacyBillNo($patient_id);
        } elseif ($referral_type == 4) {
            //pathology
            $result = $this->referral_payment_model->get_pathologyBillNo($patient_id);
        } elseif ($referral_type == 5) {
            //radiology
            $result = $this->referral_payment_model->get_radiologyBillNo($patient_id);
        } elseif ($referral_type == 6) {
            //blood_bank
            $result = $this->referral_payment_model->get_bloodbankBillNo($patient_id);
        } elseif ($referral_type == 7) {
            //ambulance
            $result = $this->referral_payment_model->get_ambulanceBillNo($patient_id);
        }
        echo json_encode($result);
    }

    public function getBillAmount()
    {
        $referral_type = $this->input->post('type');
        $bill_no       = $this->input->post('bill_no');
        if ($referral_type == 1) {
            //opd
            $result = $this->referral_payment_model->get_opdBillAmount($bill_no);

        } elseif ($referral_type == 2) {
            //ipd
            $result = $this->referral_payment_model->get_ipdBillAmount($bill_no);
        } elseif ($referral_type == 3) {
            //pharmacy
            $result = $this->referral_payment_model->get_pharmacyBillAmount($bill_no);
        } elseif ($referral_type == 4) {
            //pathology
            $result = $this->referral_payment_model->get_pathologyBillAmount($bill_no);
        } elseif ($referral_type == 5) {
            //radiology
            $result = $this->referral_payment_model->get_radiologyBillAmount($bill_no);
        } elseif ($referral_type == 6) {
            //blood_bank
            $result = $this->referral_payment_model->get_bloodbankBillAmount($bill_no);
        } elseif ($referral_type == 7) {
            //ambulance
            $result = $this->referral_payment_model->get_ambulanceBillAmount($bill_no);
        }

        echo json_encode($result);
    }

}
