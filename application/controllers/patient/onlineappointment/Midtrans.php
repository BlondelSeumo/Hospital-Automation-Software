<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Midtrans extends Patient_Controller
{

    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting = $this->setting_model->get()[0];
        $this->load->library(array('midtrans_lib'));
        $this->load->model(array('onlineappointment_model','charge_model'));
    }


    public function index() {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data = array();
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
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
       
        $transaction = array(
                'enabled_payments' => array('credit_card'),
                'transaction_details' => array(
                'order_id' => time(),
                'gross_amount' => round($total), // no decimal allowed
            ),
        );
   
        $data['amount']=$transaction['transaction_details']['gross_amount'];
        $data['return_url'] = base_url() . "patient/onlineappointment/midtrans/complete";
        $snapToken = $this->midtrans_lib->getSnapToken($transaction, $this->pay_method->api_secret_key);
        $data['snap_Token'] = $snapToken;
 
        $this->load->view('patient/onlineappointment/midtrans/index', $data);
    }

    public function complete()
    { 
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $patient_data  = $this->session->userdata('patient');
        $patient_id  = $patient_data['patient_id'];
        $charge_id  = $this->session->userdata('charge_id');        
        $amount = $this->session->userdata('payment_amount');
        $response                           = json_decode($_POST['result_data']);

            $transactionid= $response->transaction_id;
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'payment_type'   => 'Online',
                'transaction_id'=>$transactionid,
                'payment_mode'   => 'Midtrans',
                'note'           => "Payment deposit through Midtrans TXN ID: " . $transactionid,
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
        echo json_encode(array("status"=>"success",'appointment_id'=>$return_detail));
    }
}
