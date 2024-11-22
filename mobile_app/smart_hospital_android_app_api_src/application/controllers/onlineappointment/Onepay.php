<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Onepay extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

	public function __construct()
	{ 
		parent::__construct();
        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->getActiveMethod();

	}
 
	public function index()
	{ 

        $payment_details  = $this->session->userdata("params");
        $appointment_id   = $payment_details['appointment_id'];
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        
        $data['setting']  = $this->setting;
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
        }else{
            $charge=0;
        }
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        
        $this->load->view('onlineappointment/onepay/index', $data);
	}

    public function pay(){
        $data = array();
        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $amount          = $this->session->userdata('payment_amount');
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
     
        if ($this->form_validation->run()==false) {
            $payment_details  = $this->session->userdata("params");
        $appointment_id   = $payment_details['appointment_id'];
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        
        $data['setting']  = $this->setting;
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
        }else{
            $charge=0;
        }
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        
        $this->load->view('onlineappointment/onepay/index', $data);
        }else{
            $payment_details=$this->session->userdata('params');
            if ($this->session->has_userdata('params')) {
                $api_publishable_key = ($this->payment_method->api_publishable_key);
                $api_secret_key = ($this->payment_method->api_secret_key);
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key'] = $api_secret_key; 
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
        'vpc_AccessCode' => $this->payment_method->salt,
        'vpc_Amount' => $amount*100,
        'vpc_Command' => 'pay',
        'vpc_Customer_Email' => '',
        'vpc_Customer_Id' => '',
        'vpc_Customer_Phone' => '',
        'vpc_Locale' => 'en',
        'vpc_MerchTxnRef' => date('YmdHis') . rand(),
        'vpc_Merchant' => $this->payment_method->api_publishable_key,
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
               
        $payment_details  = $this->session->userdata("params");
        $appointment_id   = $payment_details['appointment_id'];
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        
        $data['setting']  = $this->setting;
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
        }else{
            $charge=0;
        }
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        
        $this->load->view('onlineappointment/onepay/index', $data);
            }
        }
    }
 
	
    public function success___(){
        $session_data = $this->session->userdata("params");
        $appointment_id   = $session_data['appointment_id'];
        $patient_id  = $session_data['patient_id'];
        $charge_id  = $this->session->userdata('charge_id');
        if ($_GET['payment_status'] == 'Credit') {
            $amount                             = $this->session->userdata('payment_amount');
            $transactionid                      = $_GET['payment_id'];
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'transaction_id' => $transactionid,
                'payment_type'   => 'Online',
                'payment_mode'   => 'Instamojo',
                'note'           => "Payment deposit through Instamojo TXN ID: " . $transactionid,
                'date'           => date("Y-m-d H:i:s"),
            ); 
            $payment_section = $this->config->item('payment_section');
            $transaction_array = array(
                'amount'                 => $amount,
                'patient_id'             => $patient_id,
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Instamojo TXN ID: " . $transactionid,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );

            $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
            if($status){
                redirect(base_url("payment/appointmentsuccess/".$appointment_id));
            }
        }else{
            redirect(site_url('payment/paymentfailed'));
        }
    }

                   public function success()
    {
         $session_data = $this->session->userdata("params");
        $appointment_id   = $session_data['appointment_id'];
        $patient_id  = $session_data['patient_id'];
        $charge_id  = $this->session->userdata('charge_id');
$SECURE_SECRET = $this->api_config->api_signature;
$vpc_Txn_Secure_Hash = $_GET["vpc_SecureHash"];
$vpc_MerchTxnRef = $_GET["vpc_MerchTxnRef"];
$vpc_AcqResponseCode = $_GET["vpc_AcqResponseCode"];
unset($_GET["vpc_SecureHash"]);
$errorExists = false;
if (strlen($SECURE_SECRET) > 0 && $_GET["vpc_TxnResponseCode"] != "7" && $_GET["vpc_TxnResponseCode"] != "No Value Returned") {
    ksort($_GET);
    $md5HashData = "";
    foreach ($_GET as $key => $value) {
        if ($key != "vpc_SecureHash" && (strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
            $md5HashData .= $key . "=" . $value . "&";
        }
    }

    $md5HashData = rtrim($md5HashData, "&");
    if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$SECURE_SECRET)))) {
        $hashValidated = "CORRECT";
    } else {
        $hashValidated = "INVALID HASH";
    }
} else {

    $hashValidated = "INVALID HASH";
}

$txnResponseCode = $this->null2unknown($_GET["vpc_TxnResponseCode"]);

$verType = array_key_exists("vpc_VerType", $_GET) ? $_GET["vpc_VerType"] : "No Value Returned";
$verStatus = array_key_exists("vpc_VerStatus", $_GET) ? $_GET["vpc_VerStatus"] : "No Value Returned";
$token = array_key_exists("vpc_VerToken", $_GET) ? $_GET["vpc_VerToken"] : "No Value Returned";
$verSecurLevel = array_key_exists("vpc_VerSecurityLevel", $_GET) ? $_GET["vpc_VerSecurityLevel"] : "No Value Returned";
$enrolled = array_key_exists("vpc_3DSenrolled", $_GET) ? $_GET["vpc_3DSenrolled"] : "No Value Returned";
$xid = array_key_exists("vpc_3DSXID", $_GET) ? $_GET["vpc_3DSXID"] : "No Value Returned";
$acqECI = array_key_exists("vpc_3DSECI", $_GET) ? $_GET["vpc_3DSECI"] : "No Value Returned";
$authStatus = array_key_exists("vpc_3DSstatus", $_GET) ? $_GET["vpc_3DSstatus"] : "No Value Returned";

$errorTxt = "";

if ($txnResponseCode == "7" || $txnResponseCode == "No Value Returned" || $errorExists) {
    $errorTxt = "Error ";
}

$transStatus = "";
if($hashValidated=="CORRECT" && $txnResponseCode=="0"){
    $transStatus = "success";
    $params = $this->session->userdata('params');

            $payment_id = $_GET["vpc_MerchTxnRef"];
if ($this->session->has_userdata('params')) {
               $amount                             = $this->session->userdata('payment_amount');
            $transactionid                      = $_GET['payment_id'];
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'transaction_id' => $transactionid,
                'payment_type'   => 'Online',
                'payment_mode'   => 'Onepay',
                'note'           => "Payment deposit through Onepay TXN ID: " . $transactionid,
                'date'           => date("Y-m-d H:i:s"),
            ); 
            $payment_section = $this->config->item('payment_section');
            $transaction_array = array(
                'amount'                 => $amount,
                'patient_id'             => $patient_id,
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Onepay TXN ID: " . $transactionid,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );

            $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
            if($status){
                redirect(base_url("payment/appointmentsuccess/".$appointment_id));
            }
                   
            }elseif ($hashValidated=="INVALID HASH" && $txnResponseCode=="0"){
                $transStatus = "pending";
            }else {
                $transStatus = "fail";
                $this->fail();
            }

    }
}
    public function fail()
    {

        redirect(base_url('payment/paymentfailed'));

    }
    
    public function cancel()
    {

       redirect(base_url('payment/paymentfailed'));

    }


// If input is null, returns string "No Value Returned", else returns input
public function null2unknown($data)
{
    if ($data == "") {
        return "No Value Returned";
    } else {
        return $data;
    }
}
}
