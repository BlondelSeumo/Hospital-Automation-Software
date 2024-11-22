<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mollie extends Admin_Controller
{
    public $api_config = "";
    public $pay_method = array();

    public function __construct()
    {
        parent::__construct();
        $this->setting        = $this->setting_model->get()[0];
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
    }

    public function index() {

        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
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

        $payment_details=$this->session->userdata("params");
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $this->load->view('onlineappointment/mollie/index', $data);
    }

    public function pay()
    {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
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

        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
     
        if ($this->form_validation->run()==false) {
            $data['amount'] = $total;
            $this->load->view('onlineappointment/mollie/index', $data);
        }else{
            $amount = number_format((float)($total), 2, '.', '');
            $api =' '.$this->pay_method->api_publishable_key;
            $order=time();
            
            $currency = $this->setting['currency'];
            $redirectUrl = base_url()."onlineappointment/mollie/complete";

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
            $data['api_error']=array();
            $payment_details=$this->session->userdata("params");
            $data['session_params']=$payment_details;
            $data['amount'] = $total;
            $data['api_error']=$json['detail'];
          
            $this->load->view('onlineappointment/mollie/index', $data);

        }
        }
    }

    /**
     * This is a callback function for movies payment completion
     */
    public function complete()
    {
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

            $payment_data = $this->session->userdata('params');
            $amount  = $this->session->userdata('payment_amount');
            $appointment_id  = $this->session->userdata('appointment_id');
            $charge_id  = $this->session->userdata('charge_id');
            $transactionid                      = $json->id;

            $payment_section = $this->config->item('payment_section');
            $save_record['appointment_id'] = $appointment_id; 
            $save_record['paid_amount']    = $amount;
            $save_record['transaction_id'] = $transactionid;
            $save_record['charge_id']      = $charge_id;
            $save_record['payment_mode']   = 'Mollie';
            $save_record['payment_type']   = 'Online';
            $save_record['note']           = "Payment deposit through Mollie TXN ID: " . $transactionid;
            $save_record['date']           = date("Y-m-d H:i:s");

            $transaction_array = array(
                'amount'                 => $amount,
                'patient_id'             => $payment_data['patient_id'],
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Mollie TXN ID: " . $transactionid ,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => 1,
            );

            $this->webservice_model->paymentSuccess($save_record,$transaction_array);
            redirect(base_url("payment/appointmentsuccess/".$appointment_id));
        } else {
           redirect(site_url('payment/paymentfailed'));
        }
    }
}