<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paytm extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

    public function __construct()
    {
        parent::__construct();
 
    $this->setting = $this->setting_model->get();
            $this->payment_method = $this->paymentsetting_model->getActiveMethod();
             $this->load->library('Paytm_lib');
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
        $this->load->view('onlineappointment/paytm/paytm', $data);
    }

    public function pay()
    {
        $data = array();
        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $amount          = $this->session->userdata('payment_amount');
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('email', 'Email', 'trim');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');
     
        if ($this->form_validation->run()==false) {
            $this->load->view('onlineappointment/paytm/paytm', $data);
        }else{
            $posted = $_POST;
            $paytmParams = array();
            $ORDER_ID=time();
            $CUST_ID=time();
        
            $paytmParams = array(
                "MID" => $this->payment_method->api_publishable_key,
                "WEBSITE" =>$this->payment_method->paytm_website,
                "INDUSTRY_TYPE_ID" => $this->payment_method->paytm_industrytype,
                "CHANNEL_ID" => "WEB",
                "ORDER_ID" => $ORDER_ID,
                "CUST_ID" =>$payment_details['patient_id'],
                "TXN_AMOUNT" => $amount,
                "CALLBACK_URL" => base_url()."onlineappointment/paytm/success",
            );
            $paytmChecksum = $this->paytm_lib->getChecksumFromArray($paytmParams, $this->payment_method->api_secret_key);
            $paytmParams["CHECKSUMHASH"] = $paytmChecksum;
            //$transactionURL              = 'https://securegw-stage.paytm.in/order/process';
            $transactionURL              = 'https://securegw.paytm.in/order/process';
            $data = array();
            $data['paytmParams']    = $paytmParams;
            $data['transactionURL'] = $transactionURL;
           
            $this->load->view('onlineappointment/paytm/pay', $data);
        }
    }
 
     
    public function success(){
        $paytmChecksum  = "";
        $paramList      = array();
        $isValidChecksum= "FALSE";
        $paramList = $_POST;
        $paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : "";
        $isValidChecksum = $this->paytm_lib->verifychecksum_e($paramList, $this->payment_method->api_secret_key, $paytmChecksum); 
        if($isValidChecksum == "TRUE") {
            if ($_POST["STATUS"] == "TXN_SUCCESS") { 
                if ($this->session->has_userdata('params')) {
                    $payment_data = $this->session->userdata('params');           
                    $data['amount'] = $_POST['TXNAMOUNT'];
                }
                $session_data = $this->session->userdata("params");
                $appointment_id   = $session_data['appointment_id'];
                $patient_id  = $session_data['patient_id'];
                $charge_id  = $this->session->userdata('charge_id');
                $amount                             = $this->session->userdata('payment_amount');
                $transactionid = $_POST['TXNID'];    
                $payment_data = array(
                    'appointment_id' => $appointment_id,
                    'paid_amount'    => $amount,
                    'charge_id'      => $charge_id,
                    'transaction_id' => $transactionid,
                    'payment_type'   => 'Online',
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
                    'note'                   => "Online fees deposit through Paytm TXN ID: " . $transactionid,
                    'payment_date'           => date('Y-m-d H:i:s'),
                    'received_by'            => '',
                );

                $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
                if($status){
                   redirect(base_url("payment/appointmentsuccess/".$appointment_id));
                }
            }else {
                 redirect(base_url("payment/paymentfailed"));
            }
        }else {
           redirect(base_url("payment/paymentfailed"));
        }
    }

    
}

