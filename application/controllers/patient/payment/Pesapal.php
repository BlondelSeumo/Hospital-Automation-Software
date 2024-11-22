<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pesapal extends Patient_Controller
{
    public $payment_method = array();
    public $pay_method     = array();
    public $patient_data;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('Customlib');
        $this->load->library('pesapal_lib');
        $this->patient_data   = $this->session->userdata('patient');
        $this->payment_method = $this->paymentsetting_model->get();
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->setting        = $this->setting_model->get();
    }

    public function index()
    {
            $setting             = $this->setting[0];
            $data                = array();
            $id                  = $this->patient_data['patient_id'];
            $data["id"]          = $id;
            $data['productinfo'] = $this->lang->line('online_payment');
       
            if ($this->session->has_userdata('payment_data')) {

                $payment_data                      = $this->session->userdata('payment_data');
                $api_publishable_key         = ($this->pay_method->api_publishable_key);
                $api_secret_key              = ($this->pay_method->api_secret_key);
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key']      = $api_secret_key;
                $data['case_reference_id']   = $payment_data['case_reference_id'];
                $data['amount']              = $payment_data['deposit_amount'];
                $data['currency']            = $setting['currency'];
                $data['hospital_name']       = $setting['name'];
                $data['image']               = $setting['image'];
                $data["patient_data"]        = $this->patient_model->patientDetails($id);
            }

           $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/pesapal/pesapal", $data);
            $this->load->view("layout/patient/footer");
    }


    public function pay()
    {
        $this->form_validation->set_rules("email",$this->lang->line("email"),"trim|required");
        $this->form_validation->set_rules("phone",$this->lang->line("phone"),"trim|required");
        if($this->form_validation->run() == false){
            $this->index();
        }else{
            $payment_data = $this->session->userdata('payment_data');
            $data['amount']   = $payment_data["deposit_amount"];
            $token            = $params            = null;
            $consumer_key     = $this->pay_method->api_publishable_key;
            $consumer_secret  = $this->pay_method->api_secret_key;
            $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
            $iframelink       = 'https://www.pesapal.com/API/PostPesapalDirectOrderV4';
            $amount           = number_format($data['amount'], 2);
            $desc             = $this->lang->line('online_payment');
            $type             = 'MERCHANT';
            $reference        = time();
            $first_name       = $this->patient_data["name"];
            $last_name        = "";
            $email            = $_POST['email'];
            $phonenumber      = $_POST['phone'];
            $callback_url     = base_url('patient/payment/pesapal/success');
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
            $this->load->view("patient/payment/pesapal/pesapal_pay", $data);
        }
    }


    public function success()
    {
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



            if($pesapalTrackingId!='')

            {
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
               if(defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True')

               {

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
     
        if ($status=='COMPLETED') {
            $transactionid = $pesapal_tracking_id;
            $payment_data = $this->session->userdata('payment_data');
                
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $payment_data['deposit_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Pesapal TXN ID: " . $transactionid,
                    'patient_id'   => $this->patient_data['patient_id'],
                );
                if($payment_data['payment_for'] == "opd"){
                    $save_record["opd_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "ipd"){
                    $save_record["ipd_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "pharmacy"){
                    $save_record["pharmacy_bill_basic_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "pathology"){
                    $save_record["pathology_billing_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "radiology"){
                    $save_record["radiology_billing_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "blood_bank"){
                    $save_record["blood_donor_cycle_id"] = $payment_data["donor_cycle_id"];
                    $save_record["blood_issue_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "ambulance"){
                    $save_record["ambulance_call_id"] = $payment_data['id'];
                }
                $insert_id = $this->payment_model->insertOnlinePaymentInTransactions($save_record);
            redirect(base_url("patient/pay/successinvoice/"));

        } else {
            redirect(base_url("patient/pay/paymentfailed"));
        }
    }
}
}