<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Paytm extends Patient_Controller
{

    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting = $this->setting_model->get()[0];
        $this->load->library('paytm_lib');
        $this->load->model(array('onlineappointment_model','charge_model'));
    }

    public function index() {
        
        $data = array();
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        
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
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;

        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $amount= $total;
        $data['amount'] = $amount;
        $paytmParams = array();
        $ORDER_ID = time();
        $CUST_ID = time();

        $paytmParams = array(
            "MID" => $this->pay_method->api_publishable_key,
            "WEBSITE" => $this->pay_method->paytm_website,
            "INDUSTRY_TYPE_ID" => $this->pay_method->paytm_industrytype,
            "CHANNEL_ID" => "WEB",
            "ORDER_ID" => $ORDER_ID,
            "CUST_ID" => $appointment_id,
            "TXN_AMOUNT" => $data['amount'],
            "CALLBACK_URL" => base_url() . "patient/onlineappointment/paytm/complete",
        );

        $paytmChecksum = $this->paytm_lib->getChecksumFromArray($paytmParams, $this->pay_method->api_secret_key);
        $paytmParams["CHECKSUMHASH"] = $paytmChecksum;
        $transactionURL              = 'https://securegw.paytm.in/order/process';//for sand-box
        
        $data['paytmParams'] = $paytmParams;
        $data['transactionURL'] = $transactionURL;
        $this->load->view("patient/onlineappointment/paytm/index", $data);
    }

    public function complete()
    {

        $paytmChecksum = "";
        $paramList = array();
        $isValidChecksum = "FALSE";
        $paramList = $_POST;
        $paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : "";
        $isValidChecksum = $this->paytm_lib->verifychecksum_e($paramList, $this->pay_method->api_secret_key, $paytmChecksum);
        $patient_data  = $this->session->userdata('patient');
        $patient_id  = $patient_data['patient_id'];
        if ($isValidChecksum == "TRUE") {

            if ($_POST["STATUS"] == "TXN_SUCCESS") {
            $appointment_id = $this->session->userdata('appointment_id');
            $charge_id  = $this->session->userdata('charge_id');
            $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
            $amount = $this->session->userdata('payment_amount');
            $transactionid=$_POST['TXNID'];
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'payment_type'   => 'Online',
                'transaction_id'=>  $transactionid,
                'payment_mode'   => 'Paytm',
                'note'           => "Payment deposit through Paytm TXN ID: " . $transactionid,
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
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );
            
            $return_detail                      = $this->onlineappointment_model->paymentSuccess($payment_data,$transaction_array);
            redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail));
               
            } else {
                redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
            }
        } else {
            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }

    }

}

?>
