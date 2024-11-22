<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ccavenue extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

	public function __construct()
	{ 
		parent::__construct();

        $this->load->library('Ccavenue_crypto');
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
        $this->load->view('payment/ccavenue/ccavenue', $data);
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
            $this->load->view('payment/ccavenue/ccavenue', $data);
        }else{   
            $payment_details=$this->session->userdata('params');
            $payment_details['post_amount']=$this->input->post('amount');
            $this->session->set_userdata("params", $payment_details);
            if ($this->session->has_userdata('params')) {
                $api_publishable_key = $this->payment_method->api_publishable_key;
                $api_secret_key = $this->payment_method->api_secret_key;
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key'] = $api_secret_key;
            }
        
            $details['tid']=abs(crc32(uniqid()));
            $details['merchant_id']=$api_secret_key;
            $details['order_id']=abs(crc32(uniqid()));
            $details['amount']=number_format((float)($payment_details['post_amount']), 2, '.', '');
            $details['currency']='INR';
            $details['redirect_url']=base_url('gateway/ccavenue/success'); 
            $details['cancel_url']=base_url('gateway/ccavenue/cancel');
            $details['language'] = "EN";
            $details['billing_name']     = $payment_details['name'];
            $details['billing_email']= $this->input->post("email");
            $details['billing_tel']= $this->input->post("phone");
            $merchant_data="";
            foreach ($details as $key => $value){
                $merchant_data.=$key.'='.$value.'&';
            } 
            $data['encRequest'] = $this->ccavenue_crypto->encrypt($merchant_data,$this->payment_method->salt);
            $data['access_code'] = $this->payment_method->api_publishable_key;
            $this->load->view('payment/ccavenue/pay', $data);
        }
    }
 
	
    public function success(){
        $pay_method = $this->paymentsetting_model->getActiveMethod();
        $encResponse=$_POST["encResp"];  
        $rcvdString=$this->ccavenue_crypto->decrypt($encResponse,$pay_method->salt);  
        $order_status=array();
        $decryptValues=explode('&', $rcvdString);
        $dataSize=sizeof($decryptValues);
        for($i = 0; $i < $dataSize; $i++) 
        {
            $information=explode('=',$decryptValues[$i]);
            $status[$information[0]]=$information[1];
        }
        if($status['order_status']=="Success")
        { 
            $transactionid = $status['tracking_id'];
            $bank_ref_no = $status['bank_ref_no'];
	        if ($this->session->has_userdata('params')) {
                $payment_data = $this->session->userdata('params');
                $data['amount'] = $payment_data['post_amount'];                
            }
            $save_record = array(
                'case_reference_id' => $payment_data["case_reference_id"],
                'type' => "payment",
                'amount'        => $payment_data['post_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Ccavenue TXN ID: " . $transactionid." and Bank Reference Number: ".$bank_ref_no,
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
        }else if($status['order_status']==="Aborted"){
            echo "<br>We will keep you posted regarding the status of your order through e-mail";
        }
        else if($status['order_status']==="Failure")
        {
            redirect(base_url("payment/paymentfailed"));
        }
        else
        {
            echo "<br>Security Error. Illegal access detected";
        
        }
    }
}

