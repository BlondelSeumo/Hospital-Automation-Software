<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Razorpay extends Patient_Controller
{

    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting = $this->setting_model->get()[0];
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
        $data['amount'] = $total;
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['name'] = $appointment_data->name;
        $data['merchant_order_id'] = time() . "01";
        $data['txnid'] = time() . "02";
        $data['title'] = 'Hospital Payment';
        $data['return_url'] = site_url() . 'patient/onlineappointment/razorpay/complete';
        $data['total'] = $total * 100;
        $data['key_id'] = $this->pay_method->api_publishable_key;
        $data['currency_code'] = $this->setting["currency"];

        $this->load->view('patient/onlineappointment/razorpay/index', $data);
    }

    public function complete() {
        $patient_data  = $this->session->userdata('patient');
        $patient_id  = $patient_data['patient_id'];
    	$amount = $this->session->userdata('payment_amount');
        $appointment_id = $this->session->userdata('appointment_id');
        $charge_id = $this->session->userdata('charge_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data   = array();
        if (isset($_POST['razorpay_payment_id'])) {

            $transactionid= $_POST['razorpay_payment_id'];
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'payment_type'   => 'Online',
                'transaction_id'=>$transactionid,
                'payment_mode'   => 'Razorpay',
                'note'           => "Payment deposit through Razorpay TXN ID: " . $transactionid,
                'date'           => date("Y-m-d H:i:s"),
            ); 
            $payment_section = $this->config->item('payment_section');

            $transaction_array = array(
                'amount'                 => $amount,
                'patient_id'             => $patient_id,
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Offline",
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );

 
            $return_detail                      = $this->onlineappointment_model->paymentSuccess($payment_data,$transaction_array);
            echo $appointment_id;
        } else {
            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }
    }

}
