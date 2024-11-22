<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class CCAvenue extends Patient_Controller
{

    public $payment_method = array();
    public $pay_method     = array();
    public $user_data;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('Customlib');
        $this->load->library("ccavenue_crypto");
        $this->load->model("patient_model");
        $this->patient_data   = $this->session->userdata('patient');
        $this->payment_method = $this->paymentsetting_model->get();
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->setting        = $this->setting_model->get();
    }

    public function index()
    {
        $setting             = $this->setting[0];
        $data                = array();
        $id                  = $this->patient_data['patient_id'];
        $data["id"]          = $id;
        $data['productinfo'] = $this->lang->line('online_payment');
        if ($this->session->has_userdata('payment_data')) {
            $payment_data                = $this->session->userdata('payment_data');
            $api_publishable_key         = ($this->pay_method->api_publishable_key);
            $api_secret_key              = ($this->pay_method->api_secret_key);
            $data['api_publishable_key'] = $api_publishable_key;
            $data['api_secret_key']      = $api_secret_key;
            $data['case_reference_id']   = $payment_data['case_reference_id'];
            $data['amount']              = $payment_data['deposit_amount'];
            $data["payment_type"]        = $payment_data['payment_for'];
            $data['currency']            = $setting['currency'];
            $data['hospital_name']       = $setting['name'];
            $data['image']               = $setting['image'];
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/ccavenue/ccavenue", $data);
            $this->load->view("layout/patient/footer");
        }
    }

    public function pay()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $payment_data                = $this->session->userdata('payment_data');
            $details['tid']          = abs(crc32(uniqid()));
            $details['merchant_id']  = $this->pay_method->api_secret_key;
            $details['order_id']     = abs(crc32(uniqid()));
            $details['amount']       = number_format($payment_data['deposit_amount']);
            $details['currency']     = $this->setting[0]['currency'];
            $details['redirect_url'] = base_url('patient/payment/ccavenue/complete');
            $details['cancel_url']   = base_url('patient/payment/ccavenue/cancel');
            $details['language']     = "EN";
            $details['billing_name'] = $this->patient_data['name'];
            $merchant_data           = "";
            
            foreach ($details as $key => $value) {
                $merchant_data .= $key . '=' . $value . '&';
            }
            $data['encRequest']  = $this->ccavenue_crypto->encrypt($merchant_data, $this->pay_method->salt);
            $data['access_code'] = $this->pay_method->api_publishable_key;
            $this->load->view('patient/payment/ccavenue/pay', $data);
        } else {
            redirect(base_url('user/user/dashboard'));
        }
    }

    public function complete()
    {

        $status     = array();
        $rcvdString = "";
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            if (!empty($pay_data)) {

                $encResponse = $_POST["encResp"];
                $rcvdString  = $this->ccavenue_crypto->decrypt($encResponse, $this->pay_method->salt);

                if ($rcvdString !== '') {

                    $decryptValues = explode('&', $rcvdString);
                    $dataSize      = sizeof($decryptValues);
                    for ($i = 0; $i < $dataSize; $i++) {
                        $information             = explode('=', $decryptValues[$i]);
                        $status[$information[0]] = $information[1];
                    }
                }

                if (!empty($status)) {
                    if ($status['order_status'] == "Success") {
                        $txn_id = $status['tracking_id'];
                        $bank_ref_no = $status['bank_ref_no'];
                        $payment_data = $this->session->userdata('payment_data');
                        $save_record = array(
                            'case_reference_id' => $payment_data["case_reference_id"],
                            'type' => "payment",
                            'amount'  => $payment_data['deposit_amount'],
                            'payment_mode' => 'Online',
                            'payment_date' => date('Y-m-d H:i:s'),
                            'note'         => "Online fees deposit through Ccavenue TXN ID: " . $txn_id,
                            'patient_id'        => $this->patient_data['patient_id'],
                        );
                        if($payment_data['payment_for'] == "opd"){
                            $save_record["opd_id"] = $payment_data['id'];
                        }elseif($payment_data['payment_for'] == "ipd"){
                            $save_record["ipd_id"] = $payment_data['id'];
                        }elseif($payment_data['payment_for'] == "pharmacy"){
                            $save_record["pharmacy_bill_basic_id"] = $payment_data['id'];
                        }elseif($payment_data['payment_for'] == "pathology"){
                            $save_record["pathology_billing_id"] = $payment_data['id'];
                        }elseif($payment_data['payment_for'] == "radiology"){
                            $save_record["radiology_billing_id"] = $payment_data['id'];
                        }elseif($payment_data['payment_for'] == "blood_bank"){
                            $save_record["blood_donor_cycle_id"] = $payment_data["donor_cycle_id"];
                            $save_record["blood_issue_id"] = $payment_data['id'];
                        }elseif($payment_data['payment_for'] == "ambulance"){
                            $save_record["ambulance_call_id"] = $payment_data['id'];
                        }
                        $insert_id = $this->payment_model->insertOnlinePaymentInTransactions($save_record);
        
                        redirect(base_url("patient/pay/successinvoice/"));
                    } else {
                        redirect(base_url('patient/pay/paymentfailed'));
                    }
                } else {
                    redirect(base_url('patient/pay/paymentfailed'));
                }
            }
        }

    }

}
