<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Onepay extends Patient_Controller
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
            $payment_data                      = $this->session->userdata('payment_data');
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
            $this->load->view("patient/payment/onepay/index", $data);
            $this->load->view("layout/patient/footer");
        }
    }

    public function pay()
    {
            $patient_detail = $this->session->userdata('patient');
            if ($this->session->has_userdata('payment_data')) {
                $id                          = $this->patient_data['patient_id'];
                $insta_apikey    = $this->pay_method->api_secret_key;
                $insta_authtoken = $this->pay_method->api_publishable_key;
                $payment_data = $this->session->userdata('payment_data');
                $data['amount']              = $payment_data['deposit_amount'];
            }
            $payment_data=array(
        'AVS_City' => '',
        'AVS_Country' =>'',
        'AVS_PostCode' => '',
        'AVS_StateProv' => '',
        'AVS_Street01' => '',
        'AgainLink' => urlencode($_SERVER['HTTP_REFERER']),
        'Title' => '',
        'display' => '',
        'vpc_AccessCode' => $this->pay_method->salt,
        'vpc_Amount' => $data['amount']*100,
        'vpc_Command' => 'pay',
        'vpc_Customer_Email' => '',
        'vpc_Customer_Id' => '',
        'vpc_Customer_Phone' => '',
        'vpc_Locale' => 'en',
        'vpc_MerchTxnRef' => date('YmdHis') . rand(),
        'vpc_Merchant' => $this->pay_method->api_publishable_key,
        'vpc_OrderInfo' => 'JSECURETEST01',
        'vpc_ReturnURL' => base_url() . 'user/gateway/onepay/success',
        'vpc_SHIP_City' => '',
        'vpc_SHIP_Country' => '',
        'vpc_SHIP_Provice' => '',
        'vpc_SHIP_Street01' => '',
        'vpc_TicketNo' => $_SERVER ['REMOTE_ADDR'],
        'vpc_Version' => '2');
        $vpcURL="https://mtf.onepay.vn/paygate/vpcpay.op?";
        foreach($payment_data as $key => $value) {
            if (strlen($value) > 0) {
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }

                if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
                    $md5HashData .= $key . "=" . $value . "&";
                }
            }
        }

        $md5HashData = rtrim($md5HashData, "&");

        if (strlen($SECURE_SECRET) > 0) {

            $vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$SECURE_SECRET)));
        }


        
            if ($vpcURL) {
                
                header("Location: ".$vpcURL);
            } else {
				$setting             = $this->setting[0];
				$data                = array();
				$id                  = $this->patient_data['patient_id'];
				$data["id"]          = $id;
                $payment_data                      = $this->session->userdata('payment_data');
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
				
                $data['api_error'] = array();
				 $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/onepay/index", $data);
            $this->load->view("layout/patient/footer");
            }
    }


    public function success($payment_type = '')
    {
        if ($_GET['payment_status'] == 'Credit') {
            if ($this->session->has_userdata('payment_data')) {
                $payment_data = $this->session->has_userdata('payment_data');
                $data['amount'] = $payment_data['deposit_amount'];
            }
            $transactionid = $_GET['payment_id'];

            $payment_data = $this->session->userdata('payment_data');
                
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $payment_data['deposit_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Instamojo TXN ID: " . $transactionid,
                    'patient_id'  => $this->patient_data['patient_id'],
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
    } 
}