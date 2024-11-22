<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mollie extends Patient_Controller
{

    public $pay_method     = array();
    public $patient_data;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->setting        = $this->setting_model->get()[0];
        $this->load->model(array('onlineappointment_model','charge_model'));
    }

    public function index() {

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
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
        $this->load->view('patient/onlineappointment/mollie/index', $data);

    }

    public function pay()
    {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $payment_amount = $this->session->userdata('payment_amount');
        $amount =number_format((float)($payment_amount), 2, '.', '');
        $api=' '.$this->pay_method->api_publishable_key;
        $order=time();
        
        $currency=$this->setting['currency'];
        $redirectUrl=base_url()."patient/onlineappointment/mollie/complete";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "amount[currency]=".$currency."&amount[value]=".$amount."&description=#".$order."&redirectUrl=".$redirectUrl);

        $headers = array();
        $headers[] = 'Authorization: Bearer'.$api;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $json = json_decode($result, true);
       
        if ($json['status']=='open') {
            $url = $json['_links']['checkout']['href'];
            $this->session->set_userdata("mollie_payment_id", $json['id']);
            header("Location: $url");
        }else {

            $data = array();
            $json = json_decode($result, true);
           
            $error = array();

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

            $data['amount'] = $amount;
            $data['error']=$json['detail'];
            
            $this->load->view('patient/onlineappointment/mollie/index', $data);
        }
    }

    /**
     * This is a callback function for movies payment completion
     */
    public function complete()
    {
        $amount  = $this->session->userdata('payment_amount');
        $appointment_id  = $this->session->userdata('appointment_id');
        $charge_id  = $this->session->userdata('charge_id');
        $api=' '.$this->pay_method->api_publishable_key;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments/'.$this->session->userdata('mollie_payment_id'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer'.$api;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $json=json_decode($result);
        if ($json->status=='paid') {

            $transactionid                      = $json->id; 
            $payment_section = $this->config->item('payment_section');
            $gateway_response['appointment_id'] = $appointment_id; 
            $gateway_response['paid_amount']    = $amount;
            $gateway_response['transaction_id'] = $transactionid;
            $gateway_response['charge_id']      = $charge_id;
            $gateway_response['payment_mode']   = 'Mollie';
            $gateway_response['payment_type']   = 'Online';
            $gateway_response['note']           = "Payment deposit through Mollie TXN ID: " . $transactionid;
            $gateway_response['date']           = date("Y-m-d H:i:s");

            $transaction_array = array(
                'amount'                 => $amount,
                'patient_id'             => $this->customlib->getPatientSessionUserID(),
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Mollie TXN ID: " . $transactionid ,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => 1,
            );

            $return_detail = $this->onlineappointment_model->paymentSuccess($gateway_response,$transaction_array);
             
            redirect(base_url("patient/onlineappointment/checkout/successinvoice/$appointment_id"));

        } else {

            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }

    }

}