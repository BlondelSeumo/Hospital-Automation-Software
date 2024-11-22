<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Midtrans extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

	public function __construct()
	{
		parent::__construct();
 
    $this->setting = $this->setting_model->get();
    $this->load->library('Midtrans_lib');
	}


	public function index()
	{
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        $this->load->view('payment/midtrans/midtrans', $data);
    }

    public function pay()
    {
        $data = array();
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        if ($this->form_validation->run()==false) {  
            $this->load->view('payment/midtrans/midtrans', $data);
        }else{
        $transaction = array(
            'transaction_details' => array(
                'order_id'     => time(),
                'gross_amount' => round($_POST['amount']), // no decimal allowed   
            ),
        );
        $payment_details['post_amount']= round($_POST['amount']);
        $this->session->set_userdata("params", $payment_details);
        $snapToken=$this->midtrans_lib->getSnapToken($transaction,$payment_details['key']);
        $data['snap_Token'] = $snapToken;
        $this->load->view('payment/midtrans/pay', $data);
        }
    }
 
    public function success(){
    	$response=json_decode($_POST['result_data']);
		$payment_id=$response->transaction_id;
	    if ($this->session->has_userdata('params')) {
            $amount = $this->session->userdata('params');
        }
        $transactionid=$payment_id;
        $payment_data = $this->session->userdata('params');
        $save_record = array(
            'case_reference_id' => $payment_data["case_reference_id"],
            'type' => "payment",
            'amount'        => $payment_data['post_amount'],
            'payment_mode' => 'Online',
            'payment_date' => date('Y-m-d H:i:s'),
            'note'         => "Online fees deposit through Midtrans TXN ID: " . $transactionid,
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
        $array=array('insert_id'=>$insert_id);
        echo json_encode($array);	
	}
}

