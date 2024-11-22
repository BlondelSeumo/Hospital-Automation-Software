<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sslcommerz extends Admin_Controller {
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
        $this->load->view('payment/sslcommerz/sslcommerz', $data);
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
            $this->load->view('payment/sslcommerz/sslcommerz', $data);
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
        $requestData=array();
            $CURLOPT_POSTFIELDS=array(
            'store_id'=>$this->payment_method->api_publishable_key,
            'store_passwd'=>$this->payment_method->api_password,
            'total_amount'=>$this->input->post('amount'),
            'currency'=>$this->setting[0]['currency'],
            'tran_id'=>abs(crc32(uniqid())),
            'success_url'=>base_url().'gateway/sslcommerz/success',
            'fail_url'=>base_url().'gateway/sslcommerz/fail',
            'cancel_url'=>base_url().'gateway/sslcommerz/cancel',
            'cus_name'=>    $payment_details['name'],
            'cus_email'=>   $this->input->post('email'),
            'cus_add1'=>    "Dhaka",
            'cus_phone'=>   $this->input->post('phone'),
            'cus_city'=>'',
            'cus_country'=>'',
            'multi_card_name'=>'mastercard,visacard,amexcard,internetbank,mobilebank,othercard ',
            'shipping_method'=>'NO',
            'product_name'=>'test',
            'product_category'=>'Electronic',
            'product_profile'=>'general'
        );
            $string="";
            foreach ($CURLOPT_POSTFIELDS as $key => $value) {
                $string.=$key.'='.$value."&";
                if($key=='product_profile'){
                $string.=$key.'='.$value;
                }
            } 
            //echo $string;die;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$string");

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $response=json_decode($result);
        
        header("Location: $response->GatewayPageURL");
        }
    }
 
	
    public function success(){
        if ($_POST['status'] == 'VALID') {
	        if ($this->session->has_userdata('params')) {
                $payment_data = $this->session->userdata('params');
                $data['amount'] = $payment_data['post_amount'];
            }
            $transactionid = $_POST['val_id']; 
            $save_record = array(
                'case_reference_id' => $payment_data["case_reference_id"],
                'type' => "payment",
                'amount'        => $data['amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Sslcommerze TXN ID: " . $transactionid,
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

