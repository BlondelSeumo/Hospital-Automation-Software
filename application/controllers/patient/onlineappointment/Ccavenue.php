<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ccavenue extends Patient_Controller
    {
    
        public $pay_method = "";
        public $amount = 0;
    
        function __construct() {
            parent::__construct();
            $this->pay_method = $this->paymentsetting_model->getActiveMethod();
            $this->setting = $this->setting_model->get()[0];
            $this->load->library('Ccavenue_crypto');
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
        $data['amount'] = $total;;
        $this->load->view('patient/onlineappointment/ccavenue/index', $data);
    } 

    public function pay()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $amount = $this->session->userdata('payment_amount');
            $details['tid']          = abs(crc32(uniqid()));
            $details['merchant_id']  = $this->pay_method->api_secret_key;
            $details['order_id']     = abs(crc32(uniqid()));
            $details['amount']       = number_format($amount);
            $details['currency']     = $this->setting['currency'];
            $details['redirect_url'] = base_url('patient/onlineappointment/ccavenue/complete');
            $details['cancel_url']   = base_url('patient/onlineappointment/ccavenue/cancel');
            $details['language']     = "EN";
            $details['billing_name'] = "title";
            $merchant_data           = "";
            foreach ($details as $key => $value) {
                $merchant_data .= $key . '=' . $value . '&';
            }
            $data['encRequest']  = $this->ccavenue_crypto->encrypt($merchant_data, $this->pay_method->salt);
            $data['access_code'] = $this->pay_method->api_publishable_key;
            $this->load->view('patient/onlineappointment/ccavenue/pay', $data);
        } else {
            redirect(base_url('patient/onlineappointment/checkout'));
        }
    }

    public function success()
    {
        
        $status     = array();
        $rcvdString = "";
        $total_amount   = $this->session->userdata('payment_amount');
        $appointment_id = $this->session->userdata('appointment_id');
        $charge_id  = $this->session->userdata('charge_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        if (!empty($total_amount)) {

            $encResponse = $_POST["encResp"];
            $rcvdString  = $this->ccavenue_crypto->decrypt($encResponse, $this->pay_method->salt);

            if ($rcvdString !== '') {

                $decryptValues = explode('&', $rcvdString);
                $dataSize      = sizeof($decryptValues);
                for ($i = 0; $i < $dataSize; $i++) {
                    $information             = explode('=', $decryptValues[$i]);
                    $status[$information[0]] = $information[1];
                }
            }
            if (!empty($status)) {
                if ($status['order_status'] == "Success") {
                    $payment_section = $this->config->item('payment_section');
                    $tracking_id = $status['tracking_id'];
                    $bank_ref_no = $status['bank_ref_no'];
                    $transactionid                      = $_GET['payment_id'];
                    $gateway_response['appointment_id'] = $appointment_id;
                    $gateway_response['charge_id']      = $charge_id;
                    $gateway_response['paid_amount']    = $total_amount;
                    $gateway_response['transaction_id'] = $tracking_id;
                    $gateway_response['payment_mode']   = 'CCAvenue';
                    $gateway_response['payment_type']   = 'Online';
                    $gateway_response['note']           = "Online fees deposit through CCAvenue. TXN ID: " . $tracking_id . " Bank Ref. No.: " . $bank_ref_no;
                    $gateway_response['date']           = date("Y-m-d H:i:s");

                    $transaction_array = array(
                        'amount'                 => $amount,
                        'patient_id'             => $this->customlib->getPatientSessionUserID(),
                        'section'                => $payment_section['appointment'],
                        'type'                   => 'payment',
                        'appointment_id'         => $appointment_id,
                        'payment_mode'           => "Online",
                        'payment_date'           => date('Y-m-d H:i:s'),
                        'received_by'            => '',
                    );
                    $return_detail = $this->onlineappointment_model->paymentSuccess($gateway_response,$transaction_array);
                    redirect(base_url("patient/onlineappointment/checkout/successinvoice/"));
                } else if ($status['order_status'] === "Aborted") {
                    echo "<br>We will keep you posted regarding the status of your order through e-mail";

                } else if ($status['order_status'] === "Failure") {
                    redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));} else {
                    echo "<br>Security Error. Illegal access detected";

                }
            }

        } else {

        }
    }

    public function cancel()
    {

    }

}
