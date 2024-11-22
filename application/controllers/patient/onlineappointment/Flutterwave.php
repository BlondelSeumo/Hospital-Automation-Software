<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Flutterwave extends Patient_Controller
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
        $this->load->view('patient/onlineappointment/flutterwave/index', $data);
    } 

    public function pay() {
      $amount = $this->session->userdata('payment_amount');
      $curl   = curl_init();
      $appointment_id = $this->session->userdata('appointment_id');
      $currency       = $this->setting['currency'];
      $customer_email = $this->onlineappointment_model->getAppointmentDetails($appointment_id)->email;
      $txref          = "rave" . uniqid(); // ensure you generate unique references per transaction.
      // get your public key from the dashboard.
      $PBFPubKey    = $this->pay_method->api_publishable_key;
      $redirect_url = base_url() . 'patient/onlineappointment/flutterwave/complete'; // Set your own redirect URL

      curl_setopt_array($curl, array(
          CURLOPT_URL            => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_CUSTOMREQUEST  => "POST",
          CURLOPT_POSTFIELDS     => json_encode([
              'amount'         => $amount,
              'customer_email' => $customer_email,
              'currency'       => $currency,
              'txref'          => $txref,
              'PBFPubKey'      => $PBFPubKey,
              'redirect_url'   => $redirect_url,
          ]),
          CURLOPT_HTTPHEADER     => [
              "content-type: application/json",
              "cache-control: no-cache",
          ],
      ));

      $response = curl_exec($curl);
      $err      = curl_error($curl);

      if ($err) {
          // there was an error contacting the rave API
          die('Curl returned error: ' . $err);
      }

      $transaction = json_decode($response);
      
      if ($transaction->status=='error' || (!$transaction->data && !$transaction->data->link)) {
          // there was an error from the API
          print_r('API returned error: ' . $transaction->message);
      }else{
         // redirect to page so User can pay

      header('Location: ' . $transaction->data->link);
      }

     
    }
 
    public function complete()
    {
        $details        = $this->paymentsetting_model->getActiveMethod();
        $api_secret_key = $details->api_secret_key;
        $appointment_id = $this->session->userdata('appointment_id');
        $charge_id  = $this->session->userdata('charge_id');
        $patient_data  = $this->session->userdata('patient');
        $patient_id  = $patient_data['patient_id'];
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        if (isset($_GET['txref']) && $_GET['cancelled'] != 'true') {
            $ref      = $_GET['txref'];
            $amount = $this->session->userdata('payment_amount');
            $currency = $this->setting['currency']; //Correct Currency from Server

            $query = array(
                "SECKEY" => $api_secret_key,
                "txref"  => $ref,
            );

            $data_string = json_encode($query);

            $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

            $response = curl_exec($ch);

            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header      = substr($response, 0, $header_size);
            $body        = substr($response, $header_size);

            curl_close($ch);

            $resp = json_decode($response, true);
             if($resp['data']['code']=='NO_TX'){
              redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }else{
            $paymentStatus      = $resp['data']['status'];
            $chargeResponsecode = $resp['data']['chargecode'];
            $chargeAmount       = $resp['data']['amount'];
            $chargeCurrency     = $resp['data']['currency'];

            if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($chargeAmount == $amount) && ($chargeCurrency == $currency)) {
              $transactionid     = $ref;
              $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'payment_type'   => 'Online',
                'payment_mode'   => 'Flutterwave',
                'transaction_id'=>$transactionid,
                'note'           => "Payment deposit through Flutterwave TXN ID: " . $transactionid,
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


            $return_detail = $this->onlineappointment_model->paymentSuccess($payment_data,$transaction_array);
            redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail['insert_id']));
            } else {
                redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
            }
          }
        } else {
           redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }
    }

}
