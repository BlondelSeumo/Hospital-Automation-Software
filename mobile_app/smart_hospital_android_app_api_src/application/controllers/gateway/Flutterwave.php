<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Flutterwave extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

	public function __construct()
	{ 
		parent::__construct();
        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->getActiveMethod();
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
        $this->load->view('payment/flutterwave/flutterwave', $data);
	}

    public function pay()
    {
        $data = array();
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
     
        if ($this->form_validation->run()==false) {
             
        
            $this->load->view('payment/flutterwave/flutterwave', $data);

        }else{
            
        $payment_details=$this->session->userdata('params');
        $payment_details['post_amount']=$this->input->post('amount');
        $this->session->set_userdata("params", $payment_details);
      
    
       $curl   = curl_init();

        $customer_email = $this->input->post("email");

        $currency = $this->setting[0]['currency'];
        $txref    = "rave" . uniqid(); // ensure you generate unique references per transaction.
        // get your public key from the dashboard.
        $PBFPubKey    = $this->payment_method->api_publishable_key;
        $redirect_url = base_url() . 'gateway/flutterwave/success'; // Set your own redirect URL
        curl_setopt_array($curl, array(
            CURLOPT_URL            => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode([
                'amount'         =>$this->input->post('amount'),
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

          if (!$transaction->data && !$transaction->data->link) {
            // there was an error from the API
            print_r('API returned error: ' . $transaction->message);
redirect(base_url('payment/paymentfailed'));
        }elseif(isset($transaction->status) && ($transaction->status=='error')){
  print_r('API returned error: ' . $transaction->message);
redirect(base_url('payment/paymentfailed'));
}

        // redirect to page so User can pay

        header('Location: ' . $transaction->data->link);
        }
    }
 
	 
    public function success(){
        $api_secret_key = $this->payment_method->api_secret_key;
        $payment_data = $this->session->userdata('params');
            if (isset($_GET['txref'])) {
                $ref = $_GET['txref'];
                $amount=$payment_data['post_amount']; //Get the correct amount of your product
                $currency = $payment_data['invoice']->currency_name;; //Correct Currency from Server
                $query = array(
                    "SECKEY" => $api_secret_key,
                    "txref" => $ref
                );
            $data_string = json_encode($query); 

            $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                              
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $response       = curl_exec($ch);
            $header_size    = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header         = substr($response, 0, $header_size);
            $body           = substr($response, $header_size);
            curl_close($ch);

            $resp = json_decode($response, true);
            $paymentStatus = $resp['data']['status'];
            $chargeResponsecode = $resp['data']['chargecode'];
            $chargeAmount = $resp['data']['amount'];
            $chargeCurrency = $resp['data']['currency'];
            $txid= $resp['data']['txref'];
            if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($chargeAmount == $amount)  && ($chargeCurrency == $currency)) {
                $transactionid = $txid;  
                $payment_data = $this->session->userdata('params');
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type'              => "payment",
                    'amount'            => $payment_data['post_amount'],
                    'payment_mode'      => 'Online',
                    'payment_date'      => date('Y-m-d H:i:s'),
                    'note'              => "Online fees deposit through Flutterwave TXN ID: " . $transactionid,
                    'patient_id'        => $payment_data['patient_id'],
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
                redirect(base_url("payment/successinvoice/" . $insert_id));
            } else {
               redirect(site_url('payment/paymentfailed'));
            }
        }else {
            die('No reference supplied');
        }
    }
}

