<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Onepay extends Patient_Controller
{
    public $pay_method = "";

    function __construct() {
        parent::__construct();
        $this->config->load("payroll");
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->setting        = $this->setting_model->get()[0];
        $this->load->model(array('onlineappointment_model','charge_model','gateway_ins_model'));
    }
 
    public function index()
    {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting'] = $this->setting;
        $charges_array = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
        $tax=0;
        $standard_charge=0;
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
            $tax=($charges_array->standard_charge*$charges_array->percentage/100);
            $standard_charge=$charges_array->standard_charge;
        }else{
            $charge=0;
            $tax=0;
            $standard_charge=0;
        } 
        $data['standard_charge']=$standard_charge;
        $data['tax_amount']=$tax;
        $total = $charge;
        $data['amount'] = $total;
        $data['currency']    = $data['setting']['currency'];

        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
            $data['amount'] = $total;
            $data['standard_charge']=$standard_charge;
            $data['tax_amount']=$tax;
            $this->load->view('patient/onlineappointment/onepay/index', $data);
        }else{

            $cartTotal = $total;// This amount needs to be sourced from your application
            
           
            $appendAmp = 0;
        $SECURE_SECRET =$this->pay_method->api_signature;
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
        'vpc_Amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' )*100,
        'vpc_Command' => 'pay',
        'vpc_Customer_Email' => '',
        'vpc_Customer_Id' => '',
        'vpc_Customer_Phone' => '',
        'vpc_Locale' => 'en',
        'vpc_MerchTxnRef' => date('YmdHis') . rand(),
        'vpc_Merchant' => $this->pay_method->api_publishable_key,
        'vpc_OrderInfo' => 'JSECURETEST01',
        'vpc_ReturnURL' => base_url() . 'patient/onlineappointment/onepay/success',
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


        header("Location: ".$vpcURL);
        }
    }

    public  function generateSignature($data, $passPhrase = null) {
        // Create parameter string
        $pfOutput = '';
        foreach( $data as $key => $val ) {
            if($val !== '') {
                $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
            }
        }
        // Remove last ampersand
        $getString = substr( $pfOutput, 0, -1 );
        if( $passPhrase !== null ) {
            $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
        }
        return md5( $getString );
    }
 
    public function success() {
        $appointment_id = $this->session->userdata('appointment_id');
        $payfast_payment_id  = $this->session->userdata('payfast_payment_id');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($payfast_payment_id,'payfast');

        if($parameter_data['payment_status']!='CANCELLED'){
            if($parameter_data['payment_status']=='COMPLETE'){
                $gateway_response['paid_status']= 1;
            }else{
                $gateway_response['paid_status']= 2;
            }

            redirect(base_url("patient/onlineappointment/checkout/successinvoice/$appointment_id"));
           
        }else{
           redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }

    }

    public function cancel(){
        redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
    }
}