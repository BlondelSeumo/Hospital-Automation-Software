<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Billplz extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

	public function __construct()
	{ 
		parent::__construct();
 
        $this->setting = $this->setting_model->get();
        $this->load->library('billplz_lib');
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
        $this->load->view('payment/billplz/billplz', $data);
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
             
        
            $this->load->view('payment/billplz/billplz', $data);

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
        $data['return_url']  = base_url().'gateway/billplz/success';
        $data['total']       = $payment_details['post_amount'];
        $data['productinfo'] = "bill payment smart hospital";
        $parameter           = array(
            'title'       => $payment_details['name'],
            'description' => $data['productinfo'],
            'amount'      => $payment_details['post_amount'] * 100,
        );

        $optional = array(
            'fixed_amount'   => 'true',
            'fixed_quantity' => 'true',
            'payment_button' => 'pay',
            'redirect_uri'   => $data['return_url'],
            'photo'          => '',
            'split_header'   => false,
            'split_payments' => array(
                ['split_payments[][email]' => $this->payment_method->api_email],
                ['split_payments[][fixed_cut]' => '0'],
                ['split_payments[][variable_cut]' => ''],
                ['split_payments[][stack_order]' => '0'],
            ),
        );

        $api_key = $this->payment_method->api_secret_key;
        $this->billplz_lib->payment($parameter, $optional, $api_key);
        }
    }
 
	 
public function success(){
    if ($this->input->server('REQUEST_METHOD') == 'GET') {
        if ($this->session->has_userdata('params')) {
            $payment_data = $this->session->userdata('params');
            $data['amount'] = $payment_data['post_amount'];
        }
        if ($_GET['billplz']['paid'] == 'true') {
            $transactionid = $_GET['billplz']['id'];
            $save_record = array(
                'case_reference_id' => $payment_data["case_reference_id"],
                'type' => "payment",
                'amount'        => $payment_data['post_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Billplz TXN ID: " . $transactionid,
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
            redirect(base_url('payment/paymentfailed'));
        }
    }
    }
}

