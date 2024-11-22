<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Paytm extends Patient_Controller
{

    public $payment_method = array();
    public $pay_method     = array();
    public $patient_data;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('Customlib');
        $this->load->library('Paytm_lib');
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
        $data = array();
        if ($this->session->has_userdata('payment_data')) {
            $payment_data = $this->session->userdata("payment_data");
            $api_publishable_key     = ($this->pay_method->api_publishable_key);
            $api_secret_key          = ($this->pay_method->api_secret_key);
            $data['key_id']          = $api_publishable_key;
            $data['amount']          = $payment_data['deposit_amount'];
        }
        $setting = $this->setting[0];
        $data['currency']     = $setting['currency'];
        $data['name']         = $this->patient_data['name'];
        $id                   = $this->patient_data['id'];
        $data["payment_type"] = 'ipd';
        $data['case_reference_id']   = $payment_data['case_reference_id'];
        $data['title']        = $this->lang->line('online_payment'); 
        $posted               = $_POST;
        $paytmParams          = array();
        $ORDER_ID             = time();
        $CUST_ID              = time();
        $paytmParams          = array(
            "MID"              => $api_publishable_key,
            "WEBSITE"          => $this->pay_method->paytm_website,
            "INDUSTRY_TYPE_ID" => $this->pay_method->paytm_industrytype,
            "CHANNEL_ID"       => "WEB",
            "ORDER_ID"         => $ORDER_ID,
            "CUST_ID"          => $id,
            "TXN_AMOUNT"       => $data['amount'],
            "CALLBACK_URL"     => base_url() . "patient/payment/paytm/paytm_response",
        );
        $paytmChecksum               = $this->paytm_lib->getChecksumFromArray($paytmParams, $this->pay_method->api_secret_key);
        $paytmParams["CHECKSUMHASH"] = $paytmChecksum;
        //$transactionURL              = 'https://securegw-stage.paytm.in/order/process';//for sand-box
        $transactionURL              = 'https://securegw.paytm.in/order/process';// for live
        $data['paytmParams']    = $paytmParams;
        $data['transactionURL'] = $transactionURL;
        $this->load->view("layout/patient/header");
        $this->load->view('patient/payment/paytm/paytm', $data);
        $this->load->view("layout/patient/footer");
    }


    public function paytm_response()
    { 

        $paytmChecksum   = "";
        $paramList       = array();
        $isValidChecksum = "FALSE";
        $paramList       = $_POST;
        $paytmChecksum   = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : "";

        $isValidChecksum = $this->paytm_lib->verifychecksum_e($paramList, $this->pay_method->api_secret_key, $paytmChecksum);
       
        if ($isValidChecksum == "TRUE") {
            if ($_POST["STATUS"] == "TXN_SUCCESS") {
                $transactionid = $_POST['TXNID'];
                $payment_data = $this->session->userdata('payment_data');
                
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $payment_data['deposit_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Paytm TXN ID: " . $transactionid,
                    'patient_id'   => $this->patient_data['patient_id'],
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
                redirect(base_url("patient/pay/paymentfailed"));
            }   
         }else {
            redirect(base_url("patient/pay/paymentfailed"));
        }  
    }
}
