<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payhere extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

	public function __construct()
	{ 
		parent::__construct();
 
        $this->setting = $this->setting_model->get();
        $this->load->model("gateway_ins_model");
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
        $this->load->view('payment/payhere/index', $data);
	}
    public function pay() {
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $result = array();
        $data['currency']  = $setting['currency'];

        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');

        if ($this->form_validation->run() == false) {
           $this->load->view('payment/payhere/index', $data);
        } else {
            $payment_details['post_amount']=$this->input->post('amount');
            $this->session->set_userdata("params", $payment_details);

            $data['total'] =number_format((float)($payment_details['post_amount']), 2, '.', '');;
            $data['currency_name'] = $data['currency'];
            $data['name'] = $payment_details['name'];

            $htmlform=array(
            'merchant_id'=>$this->payment_method->api_publishable_key,
            'return_url'=>base_url().'payment/payhere/success',
            'cancel_url'=>base_url().'payment/payhere/cancel',
            'notify_url'=>base_url().'gateway_ins/payhere',
            'order_id'=>time().rand(99,999),
            'items'=>'Patient Fees',
            'currency'=>$setting['currency'],
            'amount'=>$data['total'],
            'first_name'=>$payment_details['name'],
            'last_name'=>'',
            'email'=>"",
            'phone'=>"",
            'address'=>'',
            'city'=>'',
            'country'=>''
        );

        $data['htmlform']=$htmlform;
        $data['params']['transaction_id']=$htmlform['order_id'];

            $ins_data=array(
            'unique_id'=>$data['params']['transaction_id'],
            'parameter_details'=>json_encode($htmlform),
            'gateway_name'=>'payhere',
            'type'=>'patient_bill',
            'online_appointment_id'=>null,
            'module_type'=>'patient_bill',
            'payment_status'=>'processing',
            );
            
            $transactionid = $data['params']['transaction_id'];

            $save_record = array(
                'case_reference_id' => $payment_details["case_reference_id"],
                'type' => "payment",
                'amount'        => $payment_details['post_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Payhere TXN ID: " . $transactionid,
                'patient_id'  => $payment_details['patient_id'],
            );
            if($payment_details['type'] == "opd"){
                $save_record["opd_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "ipd"){
                $save_record["ipd_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "pharmacy"){
                $save_record["pharmacy_bill_basic_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "pathology"){
                $save_record["pathology_billing_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "radiology"){
                $save_record["radiology_billing_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "blood_bank"){
                $save_record["blood_issue_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "ambulance"){
                $save_record["ambulance_call_id"] = $payment_details['id'];
            }

            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
            $save_record["gateway_ins_id"] = $gateway_ins_id;

            $this->gateway_ins_model->add_transactions_processing($save_record);

            $this->session->set_userdata("payhere_payment_id",$transactionid);
   
            $this->load->view('payment/payhere/pay', $data);
        }
    }
    public function pay__()
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
        curl_setopt($ch, CURLOPT_URL, 'https://test.instamojo.com/api/1.1/payment-requests/');
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
        $data = array();
        $data['session_params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $json = json_decode($response, true);
        $data['api_error']=$json['message'];
        $this->load->view('payment/instamojo/instamojo', $data);
}
        }
    }
 
	
    public function success(){
        if ($_GET['payment_status'] == 'Credit') {
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
                'note'         => "Online fees deposit through Payhere TXN ID: " . $transactionid,
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

    public function cancel(){
        redirect(site_url('payment/paymentfailed'));
    }
}

