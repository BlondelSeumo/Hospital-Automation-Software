<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pesapal extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

	public function __construct()
	{ 
		parent::__construct();
 
    $this->setting = $this->setting_model->get();
    $this->payment_method = $this->paymentsetting_model->getActiveMethod();
    $this->load->library('pesapal_lib');
	}


    public function index()
    { 
        $payment_details=$this->session->userdata("params");
      
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;

        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');

        if ($this->form_validation->run()==false) {
            $this->load->view('payment/pesapal/index', $data);

        }else{
            
       
        if ($this->session->has_userdata('params')) {
            $api_publishable_key = ($this->payment_method->api_publishable_key);
            $api_secret_key = ($this->payment_method->api_secret_key);
            $data['api_publishable_key'] = $api_publishable_key;
            $data['api_secret_key'] = $api_secret_key;
            
        }
        $payment_details['post_amount']=$this->input->post('amount');
        $this->session->set_userdata("params", $payment_details);

        $token = $params = NULL;
        $consumer_key = $api_publishable_key;                  
        $consumer_secret =  $api_secret_key;
        $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
        $iframelink = 'https://www.pesapal.com/API/PostPesapalDirectOrderV4';     
        $amount = number_format($_POST['amount'], 2);
        $desc = "Bill payment hospital";
        $type = 'MERCHANT'; 
        $reference = time();
        $first_name = $data['session_params']['name']; 
        $last_name = ''; 
        $email = $_POST['email'];
        $phonenumber = $_POST['phone']; 
        $callback_url = base_url('gateway/pesapal/success'); 
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
        $this->load->view('payment/pesapal/pay', $data);
        }

       
    }

 
	
    public function success(){
        $reference = null;
        $pesapal_tracking_id = null;
        if(isset($_GET['pesapal_merchant_reference'])){
            $reference = $_GET['pesapal_merchant_reference'];
        }
        if(isset($_GET['pesapal_transaction_tracking_id'])){
            $pesapal_tracking_id = $_GET['pesapal_transaction_tracking_id'];
        }

        $consumer_key = ($this->pay_method->api_publishable_key);
        $consumer_secret = ($this->pay_method->api_secret_key);
        $statusrequestAPI = 'https://www.pesapal.com/api/querypaymentstatus';
        $pesapalTrackingId=$_GET['pesapal_transaction_tracking_id'];
        $pesapal_merchant_reference=$_GET['pesapal_merchant_reference'];

        if($pesapalTrackingId!=''){

            $token = $params = NULL;
            
            $consumer = new OAuthConsumer($consumer_key, $consumer_secret);
            $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
            $request_status = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $statusrequestAPI, $params);
            $request_status->set_parameter("pesapal_merchant_reference", $pesapal_merchant_reference);
            $request_status->set_parameter("pesapal_transaction_tracking_id",$pesapalTrackingId);
            $request_status->sign_request($signature_method, $consumer, $token);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request_status);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            if(defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True'){
              $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
              curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
              curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
              curl_setopt ($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);

            }
            $response = curl_exec($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $raw_header  = substr($response, 0, $header_size - 4);
            $headerArray = explode("\r\n\r\n", $raw_header);
            $header      = $headerArray[count($headerArray) - 1];
            $elements = preg_split("/=/",substr($response, $header_size));
            $status = $elements[1];
            if($status=='COMPLETED'){
                if ($this->session->has_userdata('params')) {
                    $payment_data = $this->session->userdata('params');
                }
                $transactionid=$pesapal_tracking_id;
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'        => $payment_data['post_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Pesapal TXN ID: " . $transactionid,
                    'patient_id'  => $payment_data['patient_id'],
                );
                if($payment_data['type'] == "opd"){
                    $save_record["opd_id"] = $payment_data['id'];
                }elseif($payment_data['type'] == "ipd"){
                    $save_record["ipd_id"] = $payment_data['id'];
                }elseif($payment_data['type'] == "pharmacy"){
                    $save_record["pharmacy_bill_basic_id"] = $payment_data['id'];
                }elseif($payment_data['type'] == "pathology"){
                    $save_record["pathology_billing_id"] = $payment_data['id'];
                }elseif($payment_data['type'] == "radiology"){
                    $save_record["radiology_billing_id"] = $payment_data['id'];
                }elseif($payment_data['type'] == "blood_bank"){
                    $save_record["blood_issue_id"] = $payment_data['id'];
                }elseif($payment_data['type'] == "ambulance"){
                    $save_record["ambulance_call_id"] = $payment_data['id'];
                }
                $insert_id = $this->webservice_model->insertOnlinePaymentInTransactions($save_record);
            }else{
                redirect(site_url('payment/paymentfailed'));
            }
        }
    }
}

