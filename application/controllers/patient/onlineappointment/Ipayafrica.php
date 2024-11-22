<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ipayafrica extends Patient_Controller
{ 

    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting = $this->setting_model->get()[0];
        $this->load->model(array('onlineappointment_model','charge_model'));
    }
 
    public function index()
    {
        $data                = array();
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
        $setting             = $this->setting;
        
        $data['setting'] = $this->setting;
        $total_amount = $total;
        $data['amount'] = $total_amount;
        $data['productinfo'] = $this->lang->line('online_payment');
        $total                       = 0;
        $api_publishable_key         = ($this->pay_method->api_publishable_key);
        $api_secret_key              = ($this->pay_method->api_secret_key);
        $data['api_publishable_key'] = $api_publishable_key;
        $data['api_secret_key']      = $api_secret_key;
        $amount                      = $total_amount;
        $data['total']               = $amount;
        $data['currency']            = $setting["currency"];
        $customer_email = $appointment_data->email;
        $customer_phone = $appointment_data->mobileno;
        $fields                      = array(
            "live" => "1",
            "oid"  => uniqid(),
            "inv"  => time(),
            "ttl"  => $amount,
            "tel"  => $customer_phone,
            "eml"  => $customer_email,
            "vid"  => ($this->pay_method->api_publishable_key),
            "curr" => $this->setting["currency"],
            "p1"   => "airtel",
            "p2"   => "",
            "p3"   => "",
            "p4"   => $amount,
            "cbk"  => base_url() . 'patient/onlineappointment/ipayafrica/complete',
            "cst"  => "1",
            "crl"  => "2",
        );

            $datastring = $fields['live'] . $fields['oid'] . $fields['inv'] . $fields['ttl'] . $fields['tel'] . $fields['eml'] . $fields['vid'] . $fields['curr'] . $fields['p1'] . $fields['p2'] . $fields['p3'] . $fields['p4'] . $fields['cbk'] . $fields['cst'] . $fields['crl'];

            $hashkey                = ($this->pay_method->api_secret_key);
            $generated_hash         = hash_hmac('sha1', $datastring, $hashkey);
            $data['fields']         = $fields;
            $data['generated_hash'] = $generated_hash;
            $this->load->view('patient/onlineappointment/ipayafrica/index', $data);
    }

 
    public function complete() {
    	$amount = $this->session->userdata('payment_amount');
        $appointment_id = $this->session->userdata('appointment_id');
        $charge_id  = $this->session->userdata('charge_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $patient_data  = $this->session->userdata('patient');
        $patient_id  = $patient_data['patient_id'];
        $data   = array();
        if (!empty($_GET['status'])) {

            $transactionid = $_GET['txncd'];
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'payment_type'   => 'Online',
                'transaction_id'=>$transactionid,
                'payment_mode'   => 'Ipayafrica',
                'note'           => "Payment deposit through Ipayafrica TXN ID: " . $transactionid,
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


            $return_detail               = $this->onlineappointment_model->paymentSuccess($payment_data,$transaction_array);
            redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail));
        } else {
            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }
    }
}