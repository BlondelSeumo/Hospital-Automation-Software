<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Instamojo extends Patient_Controller
{ 

    public $pay_method = "";
    public $amount     = 0;

    public function __construct()
    {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting    = $this->setting_model->get()[0];
        $this->load->model(array('onlineappointment_model','charge_model'));
    }

    public function index()
    {
        $appointment_id   = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting']  = $this->setting;
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
        $data['amount'] = $total;
        $this->load->view('patient/onlineappointment/instamojo/index', $data);
    }

    public function pay()
    {
        $insta_apikey    = $this->pay_method->api_secret_key;
        $insta_authtoken = $this->pay_method->api_publishable_key;
        $appointment_id  = $this->session->userdata('appointment_id');
        $buyer_data      = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $amount          = $this->session->userdata('payment_amount');
        $ch              = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payment-requests/'); // for live https://www.instamojo.com/api/1.1/payment-requests/
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Api-Key:$insta_apikey",
            "X-Auth-Token:$insta_authtoken"));
        $payload = array(
            'purpose'                 => 'bill payment hospital',
            'amount'                  => $amount,
            'phone'                   => '',
            'buyer_name'              => $buyer_data->name,
            'redirect_url'            => base_url() . 'patient/onlineappointment/instamojo/complete',
            'send_email'              => false,
            'webhook'                 => '',
            'send_sms'                => false,
            'email'                   => $buyer_data->email,
            'allow_repeated_payments' => false,
        );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($response, true);
 
        if ($json['success']) {
            $url = $json['payment_request']['longurl'];
            header("Location: $url");
        } else {
          $appointment_id   = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting']  = $this->setting;
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
        $data['amount'] = $total;
            $json = json_decode($response, true);
            $data['api_error'] = $json['message'];
            $this->load->view('patient/onlineappointment/instamojo/index', $data);
        }
    }

    /**
     * This is a callback function for movies payment completion
     */ 
    public function complete()
    {
        $appointment_id   = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);

        $patient_data  = $this->session->userdata('patient');
        $patient_id  = $patient_data['patient_id'];
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
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );



           
            $return_detail                      = $this->onlineappointment_model->paymentSuccess($payment_data,$transaction_array);
           
       
             redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail));
        } else {
   
            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }
 
    }

}
