<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Walkingm extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();
        $this->load->library('Walkingm_lib');
        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->getActiveMethod();
    }

    public function index() {
        $payment_details  = $this->session->userdata("params");
        $appointment_id   = $payment_details['appointment_id'];
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $data['setting']  = $this->setting;
        $data['session_params']=$payment_details;
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
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        $this->load->view('onlineappointment/walkingm/walkingm', $data);
    }

    public function pay() {
        $data = array();
        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $amount          = $this->session->userdata('payment_amount');
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
        $this->form_validation->set_rules('walkingm_email', 'Walkingm Email', 'trim|required');
        $this->form_validation->set_rules('walking_password', 'Walkingm Password', 'trim|required');
        if ($this->form_validation->run()==false) {
            $this->load->view('onlineappointment/walkingm/walkingm', $data);
        }else{
            $payment_details=$this->session->userdata('params');
            if ($this->session->has_userdata('params')) {
                $api_publishable_key = ($this->payment_method->api_publishable_key);
                $api_secret_key = ($this->payment_method->api_secret_key);
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key'] = $api_secret_key; 
            }
            $payment_array['payer']="Walkingm";
            $payment_array['amount']=$amount;
            $payment_array['currency']= $this->setting[0]['currency'];
            $payment_array['successUrl']= base_url()."onlineappointment/walkingm/success";
            $payment_array['cancelUrl']=base_url()."onlineappointment/walkingm/cancel";
            $response= $this->walkingm_lib->walkingm_login($_POST['email'],$_POST['password'],$payment_array);

          if($response!=""){
           
            $data['params'] = $this->session->userdata('params');
            $data['setting'] = $this->setting;
            $data['api_error'] = $response;
            $this->load->view('onlineappointment/walkingm/walkingm', $data);
          }
           

            
            } 
        }
    

    public function success() {
      $response= base64_decode($_SERVER["QUERY_STRING"]);
        $payment_response=json_decode($response);
        
        if ($response != '' && $payment_response->status=200) {
                    $transactionid = $payment_response->transaction_id;
            if($transactionid){
                 $session_data = $this->session->userdata("params");
            $appointment_id   = $session_data['appointment_id'];
            $patient_id  = $session_data['patient_id'];
            $charge_id  = $this->session->userdata('charge_id');
            $amount                             = $this->session->userdata('payment_amount');
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'transaction_id' => $transactionid,
                'payment_type'   => 'Online',
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
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Razorpay TXN ID: " . $transactionid,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );

            $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
            if($status){
                redirect(base_url("payment/appointmentsuccess/".$appointment_id));
            }
            }
        }else{
            redirect(base_url("payment/paymentfailed"));
        }
    }

      public function cancel() {
        redirect(base_url("payment/paymentfailed"));
      }

}
