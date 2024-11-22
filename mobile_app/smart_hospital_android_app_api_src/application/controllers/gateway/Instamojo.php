<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Instamojo extends Admin_Controller {
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
        $this->load->view('payment/instamojo/instamojo', $data);
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
             
        
            $this->load->view('payment/instamojo/instamojo', $data);

        }else{
            
        $payment_details=$this->session->userdata('params');
        $payment_details['post_amount']=$this->input->post('amount');
        $this->session->set_userdata("params", $payment_details);
        if ($this->session->has_userdata('params')) {


            $api_publishable_key = ($this->payment_method->api_publishable_key);
            $api_secret_key = ($this->payment_method->api_secret_key);
            $data['api_publishable_key'] = $api_publishable_key;
            $data['api_secret_key'] = $api_secret_key;
            
        }
    
        $ch = curl_init();
        // for test https://test.instamojo.com/api/1.1/payment-requests/
        // for live https://www.instamojo.com/api/1.1/payment-requests/
        curl_setopt($ch, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payment-requests/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
                    array("X-Api-Key:$api_secret_key",
                          "X-Auth-Token:$api_publishable_key"));
        $payload = Array(
            'purpose' => 'Bill Payment',
            'amount' =>$_POST['amount'],
            'phone' => $_POST['phone'],
            'buyer_name' => $payment_details['name'],
            'redirect_url' => base_url().'gateway/instamojo/success',
            'send_email' => false,
            'webhook' => base_url().'webhooks/insta_webhook',
            'send_sms' => false,
            'email' => $_POST['email'],
            'allow_repeated_payments' => false
        );

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch); 
        $json = json_decode($response, true);
    if($json['success']){
          
    $url = $json['payment_request']['longurl']; 
header( "Location: $url" );
}else{

        $json = json_decode($response, true);
        $data['api_error']=$json['message'];
         $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
       

        $data['session_params']=$payment_details;
       
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        $this->load->view('payment/instamojo/instamojo', $data);
}
        }
    }
 
	
    public function success(){
        if($_GET['payment_status']=='Credit'){
        	if ($this->session->has_userdata('params')) {
                $payment_data = $this->session->userdata('params');
                $data['amount'] = $payment_data['post_amount'];            
            }
            $transactionid=$_GET['payment_id'];
            $payment_data = $this->session->userdata('params');
            $save_record = array(
                'case_reference_id' => $payment_data["case_reference_id"],
                'type' => "payment",
                'amount'        => $payment_data['post_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Instamojo TXN ID: " . $transactionid,
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
            redirect(base_url("payment/successinvoice/" . $insert_id));
        }else{
            redirect(site_url('payment/paymentfailed'));
        }
    }
}

