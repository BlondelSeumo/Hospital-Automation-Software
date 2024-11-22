<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Billplz extends Patient_Controller
{

    public $payment_method = array();
    public $pay_method     = array();
    public $user_data;
    public $setting;
 
    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('Customlib');
        $this->load->library('billplz_lib');
        $this->load->model("patient_model");
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
            $data["payment_type"]        = $payment_data['payment_for'];
            $data['currency']            = $setting['currency'];
            $data['hospital_name']       = $setting['name'];
            $data['image']               = $setting['image'];
            $data['api_error']="";
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/billplz/billplz", $data);
            $this->load->view("layout/patient/footer");
        }
    }

    public function pay()
    {
        $payment_data            = $this->session->userdata('payment_data');
        $data['return_url']  = base_url() . 'patient/payment/billplz/complete';
        $data['total']       = $payment_data['deposit_amount'];
        $data['productinfo'] = $this->lang->line('online_payment');
        $parameter           = array(
            'title'       => $this->patient_data['name'],
            'description' => $data['productinfo'],
            'amount'      => $payment_data['deposit_amount'] * 100,
        );

        $optional = array( 
            'fixed_amount'   => 'true',
            'fixed_quantity' => 'true',
            'payment_button' => 'pay',
            'redirect_uri'   => $data['return_url'],
            'photo'          => '',
            'split_header'   => false,
            'split_payments' => array(
                ['split_payments[][email]' => $this->pay_method->api_email],
                ['split_payments[][fixed_cut]' => '0'],
                ['split_payments[][variable_cut]' => ''],
                ['split_payments[][stack_order]' => '0'],
            ),
        );

        $api_key = $this->pay_method->api_secret_key;
        $pay_data = $this->billplz_lib->payment($parameter, $optional, $api_key);
        
		if($pay_data){
			$setting             = $this->setting[0];
			$data                = array();
			$pay_data = json_decode($pay_data);
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
				$data["payment_type"]        = $payment_data['payment_for'];
				$data['currency']            = $setting['currency'];
				$data['hospital_name']       = $setting['name'];
				$data['image']               = $setting['image'];
                if(isset($pay_data->error->message)){
                    $data['api_error'] = $pay_data->error->message;
                }else{
                    $data['api_error']="Authentication failed";
                }
                
				$this->load->view("layout/patient/header");
				$this->load->view("patient/payment/billplz/billplz", $data);
				$this->load->view("layout/patient/footer");
			}
		}
    }

    public function complete()
    {
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            $session_data = $this->session->userdata('payment_data');
            if ($_GET['billplz']['paid'] == 'true') {
                $txn_id = $_GET['billplz']['id'];
                $payment_data = $this->session->userdata('payment_data');
                
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $payment_data['deposit_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Billplz TXN ID: " . $txn_id,
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
                redirect(base_url('patient/pay/paymentfailed'));
            }
        }
    }

}