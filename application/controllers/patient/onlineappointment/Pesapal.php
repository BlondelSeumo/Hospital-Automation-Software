<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pesapal extends Patient_Controller
{

    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting = $this->setting_model->get()[0];
        $this->load->library(array('pesapal_lib'));
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
        $this->load->view('patient/onlineappointment/pesapal/index', $data);
    } 

	public function pay()
    {
    	$appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $amount = $this->session->userdata('payment_amount');
        $data['amount']   = $amount;
        $token            = $params            = null;
        $consumer_key     = $this->pay_method->api_publishable_key;
        $consumer_secret  = $this->pay_method->api_secret_key;
        $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
        $iframelink       = 'https://www.pesapal.com/API/PostPesapalDirectOrderV4';
        $amount           = number_format($data['amount'], 2);
        $desc             = "Bill payment hospital";
        $type             = 'MERCHANT';
        $reference        = time();
        $first_name       = $appointment_data->name;
        $last_name        = "";
        $email            = $appointment_data->email;
        $phonenumber      = $appointment_data->mobileno;
        $callback_url     = base_url('patient/onlineappointment/pesapal/complete');
        $post_xml         = "<?xml version=\"1.0\" encoding=\"utf-8\"?><PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchemainstance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Amount=\"" . $amount . "\" Description=\"" . $desc . "\" Type=\"" . $type . "\" Reference=\"" . $reference . "\" FirstName=\"" . $first_name . "\" LastName=\"" . $last_name . "\" Email=\"" . $email . "\" PhoneNumber=\"" . $phonenumber . "\" xmlns=\"http://www.pesapal.com\" />";
        $post_xml         = htmlentities($post_xml);
        $consumer         = new OAuthConsumer($consumer_key, $consumer_secret);
        $iframe_src       = OAuthRequest::from_consumer_and_token($consumer, $token, "GET",
            $iframelink, $params);
        $iframe_src->set_parameter("oauth_callback", $callback_url);
        $iframe_src->set_parameter("pesapal_request_data", $post_xml);
        $iframe_src->sign_request($signature_method, $consumer, $token);
        $consumer   = new OAuthConsumer($consumer_key, $consumer_secret);
        $iframe_src = OAuthRequest::from_consumer_and_token($consumer, $token, "GET",
            $iframelink, $params);
        $iframe_src->set_parameter("oauth_callback", $callback_url);
        $iframe_src->set_parameter("pesapal_request_data", $post_xml);
        $iframe_src->sign_request($signature_method, $consumer, $token);
        $data['iframe_src'] = $iframe_src;
        $this->load->view("patient/onlineappointment/pesapal/pay", $data);
    }
      
	public function complete()
    {

        $reference           = null;
        $pesapal_tracking_id = null;

        if (isset($_GET['pesapal_merchant_reference'])) {
            $reference = $_GET['pesapal_merchant_reference'];
        }

        if (isset($_GET['pesapal_transaction_tracking_id'])) {
            $pesapal_tracking_id = $_GET['pesapal_transaction_tracking_id'];
        }

        $consumer_key               = $this->pay_method->api_publishable_key;
        $consumer_secret            = $this->pay_method->api_secret_key;
        $statusrequestAPI           = 'https://www.pesapal.com/api/querypaymentstatus';
        $pesapalTrackingId          = $_GET['pesapal_transaction_tracking_id'];
        $pesapal_merchant_reference = $_GET['pesapal_merchant_reference'];

        if ($pesapalTrackingId != '') {

            $token = $params = null;

            $consumer = new OAuthConsumer($consumer_key, $consumer_secret);

            $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

            $request_status = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $statusrequestAPI, $params);

            $request_status->set_parameter("pesapal_merchant_reference", $pesapal_merchant_reference);

            $request_status->set_parameter("pesapal_transaction_tracking_id", $pesapalTrackingId);

            $request_status->sign_request($signature_method, $consumer, $token);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $request_status);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_HEADER, 1);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            if (defined('CURL_PROXY_REQUIRED')) {
                if (CURL_PROXY_REQUIRED == 'True') {

                    $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;

                    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);

                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

                    curl_setopt($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);

                }
            }

            $response    = curl_exec($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $raw_header  = substr($response, 0, $header_size - 4);
            $headerArray = explode("\r\n\r\n", $raw_header);
            $header      = $headerArray[count($headerArray) - 1];
            $elements    = preg_split("/=/", substr($response, $header_size));
            $status      = $elements[1];
            if ($status == 'COMPLETED') {
            $appointment_id = $this->session->userdata('appointment_id');
            $charge_id = $this->session->userdata('charge_id');
            $amount                             = $this->session->userdata('payment_amount');
            $patient_data  = $this->session->userdata('patient');
            $patient_id  = $patient_data['patient_id'];
            $transactionid= $pesapal_tracking_id;
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'transaction_id'=>$transactionid,
                'payment_type'   => 'Online',
                'payment_mode'   => 'Pesapal',
                'note'           => "Payment deposit through Pesapal TXN ID: " . $transactionid,
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

                redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail['insert_id']));
            } else {
                redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
            }
        }

        curl_close($ch);
    }
   
}

