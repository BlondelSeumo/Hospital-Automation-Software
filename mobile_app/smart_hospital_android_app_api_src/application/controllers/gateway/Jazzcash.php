<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jazzcash extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

	public function __construct()
	{ 
		parent::__construct();
 
    $this->setting = $this->setting_model->get();
    $this->payment_method = $this->paymentsetting_model->getActiveMethod();
    date_default_timezone_set("Asia/Karachi");
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
        $this->load->view('payment/jazzcash/jazzcash', $data);
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
             
        
            $this->load->view('payment/jazzcash/jazzcash', $data);

        }else{
            
        $payment_details=$this->session->userdata('params');
        $payment_details['post_amount']=$this->input->post('amount');
        $this->session->set_userdata("params", $payment_details);
        $data['total']                         = $this->input->post('amount');
        $data['pp_MerchantID']                 = $this->payment_method->api_secret_key;
        $data['pp_Password']                   = $this->payment_method->api_password;
        $data['currency_code']                 = $this->setting[0]['currency'];
        $data['ExpiryTime']                    = date('YmdHis', strtotime("+3 hours"));
        $data['TxnDateTime']                   = date('YmdHis', strtotime("+0 hours"));
        $data['TxnRefNumber']                  = "T" . date('YmdHis');
        $input_para["pp_Version"]              = "2.0";
        $input_para["pp_IsRegisteredCustomer"] = "Yes";
        $input_para["pp_TxnType"]              = "MPAY";
        $input_para["pp_TokenizedCardNumber"]  = "";
        $input_para["pp_CustomerID"]           = time();
        $input_para["pp_CustomerEmail"]        = '';
        $input_para["pp_CustomerMobile"]       = "";
        $input_para["pp_MerchantID"]           = $data['pp_MerchantID'];
        $input_para["pp_Language"]             = "EN";
        $input_para["pp_SubMerchantID"]        = "";
        $input_para["pp_Password"]             = $data['pp_Password'];
        $input_para["pp_TxnRefNo"]             = $data['TxnRefNumber'];
        $input_para["pp_Amount"]               = $data['total'] * 100;
        $input_para["pp_DiscountedAmount"]     = "";
        $input_para["pp_DiscountBank"]         = "";
        $input_para["pp_TxnCurrency"]          = 'PKR';
        $input_para["pp_TxnDateTime"]          = $data['TxnDateTime'];
        $input_para["pp_TxnExpiryDateTime"]    = $data['ExpiryTime'];
        $input_para["pp_BillReference"]        = time();
        $input_para["pp_Description"]          = "bill payment smart hospital";
        $input_para["pp_ReturnURL"]            = base_url().'gateway/jazzcash/success';
        $input_para["pp_SecureHash"]           = "0123456789";
        $input_para["ppmpf_1"]                 = "1";
        $input_para["ppmpf_2"]                 = "2";
        $input_para["ppmpf_3"]                 = "3";
        $input_para["ppmpf_4"]                 = "4";
        $input_para["ppmpf_5"]                 = "5";
        $data['payment_data']                  = $input_para;
        $this->load->view("payment/jazzcash/pay", $data);
        }
    }
 
	
    public function success(){
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $session_data = $this->session->userdata('payment_amount');
            if ($_POST['pp_ResponseCode'] == '000') {
                $transactionid = $_POST['pp_TxnRefNo'];   
                $payment_data = $this->session->userdata('params');
                $save_record = array(
                        'case_reference_id' => $payment_data["case_reference_id"],
                        'type' => "payment",
                        'amount'        => $payment_data['post_amount'],
                        'payment_mode' => 'Online',
                        'payment_date' => date('Y-m-d H:i:s'),
                        'note'         => "Online fees deposit through Jazzcash TXN ID: " . $transactionid,
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
            } else {
                redirect(base_url('patient/pay/paymentfailed'));
            }
        }
    }
}

