<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paytm extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

    public function __construct()
    {
        parent::__construct();
 
    $this->setting = $this->setting_model->get();
            $this->payment_method = $this->paymentsetting_model->getActiveMethod();
             $this->load->library('Paytm_lib');
    }

    public function index()
    {
        $payment_details=$this->session->userdata("params");

        $setting = $this->setting[0];
        $data = array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
             
        $data['api_error']=array();
        
        $data['setting'] = $this->setting;


        $this->load->view('payment/paytm/paytm', $data);
        

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
        $this->form_validation->set_rules('email', 'Email', 'trim');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');    
        if ($this->form_validation->run()==false) {
             
            $this->load->view('payment/paytm/paytm', $data);

        }else{
            
         $posted = $_POST;
        $paytmParams = array();
        $ORDER_ID=time();
        $CUST_ID=time();
    
        $paytmParams = array(
            "MID" => $this->payment_method->api_publishable_key,
            "WEBSITE" =>$this->payment_method->paytm_website,
            "INDUSTRY_TYPE_ID" => $this->payment_method->paytm_industrytype,
            "CHANNEL_ID" => "WEB",
            "ORDER_ID" => $ORDER_ID,
            "CUST_ID" =>$payment_details['patient_id'],
            "TXN_AMOUNT" => $_POST['amount'],
            "CALLBACK_URL" => base_url()."gateway/Paytm/success",
        );
        $paytmChecksum = $this->paytm_lib->getChecksumFromArray($paytmParams, $this->payment_method->api_secret_key);
        $paytmParams["CHECKSUMHASH"] = $paytmChecksum;
        //$transactionURL              = 'https://securegw-stage.paytm.in/order/process';
        $transactionURL              = 'https://securegw.paytm.in/order/process';
      
        $data = array();
        $data['paytmParams']    = $paytmParams;
        $data['transactionURL'] = $transactionURL;
       
        $this->load->view('payment/paytm/pay', $data);
        }
    }
 
     
    public function success(){
        $paytmChecksum  = "";
        $paramList      = array();
        $isValidChecksum= "FALSE";
        $paramList = $_POST;
        $paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : "";
        $isValidChecksum = $this->paytm_lib->verifychecksum_e($paramList, $this->payment_method->api_secret_key, $paytmChecksum); 
        if($isValidChecksum == "TRUE") {
            if ($_POST["STATUS"] == "TXN_SUCCESS") {     
                if ($this->session->has_userdata('params')) {
                    $payment_data = $this->session->userdata('params');           
                    $data['amount'] = $_POST['TXNAMOUNT'];
                }
                $transactionid=$_POST['TXNID'];
                $save_record = array(
                        'case_reference_id' => $payment_data["case_reference_id"],
                        'type'              => "payment",
                        'amount'            => $data['amount'],
                        'payment_mode'      => 'Online',
                        'payment_date'      => date('Y-m-d H:i:s'),
                        'note'              => "Online fees deposit through Paytm TXN ID: " . $transactionid,
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
            }else {
                 redirect(base_url("payment/paymentfailed"));
            }
        }else {
           redirect(base_url("payment/paymentfailed"));
        }
    }

    
}

