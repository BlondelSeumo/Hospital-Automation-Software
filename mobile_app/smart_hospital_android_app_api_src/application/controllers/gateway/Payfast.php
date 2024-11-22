<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payfast extends Admin_Controller
{

    public $api_config = "";
    public $pay_method = array();

    function __construct() {
        parent::__construct();
        $this->setting = $this->setting_model->get();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->load->model(array('gateway_ins_model'));
    }
 
    public function index()
    {
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['currency']            = $setting['currency'];
        $data['amount']=$payment_details['total'];
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
        
        if ($this->form_validation->run() == false) {
            $this->load->view('payment/payfast/index', $data);
        }else{
            $payment_details['post_amount']=$this->input->post('amount');
            $this->session->set_userdata("params", $payment_details);
            $cartTotal = $payment_details['post_amount'];// This amount needs to be sourced from your application
            $payfast_data = array(
            'merchant_id' => $this->pay_method->api_publishable_key,
            'merchant_key' => $this->pay_method->api_secret_key,
            'return_url' => base_url().'gateway/payfast/success',
            'cancel_url' => base_url().'gateway/payfast/cancel',
            'notify_url' => base_url().'gateway_ins/payfast',
            'name_first' => $payment_details['name'],
            'email_address'=>$_POST['email'],
            'm_payment_id' => time().rand(99,999).time(), //Unique payment ID to pass through to notify_url
            'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
            'item_name' => 'reference_id#'.$payment_details['patient_id'],
            );
           
            $signature = $this->generateSignature($payfast_data,$this->pay_method->salt);
            $payfast_data['signature'] = $signature;           
            $ins_data=array(
            'unique_id'=>$payfast_data['m_payment_id'],
            'parameter_details'=>json_encode($payfast_data),
            'gateway_name'=>'payfast',
            'type'=>'patient_bill',
            'online_appointment_id'=>NULL,
            'module_type'=>'patient_bill',
            'payment_status'=>'processing',
            );

            $transactionid  = $payfast_data['m_payment_id'];
            $save_record = array(
                'case_reference_id' => $payment_details["case_reference_id"],
                'type' => "payment",
                'amount'        => $payment_details['post_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Payfast TXN ID: " . $transactionid,
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

            $this->session->set_userdata("payfast_payment_id",$payfast_data['m_payment_id']);
            // If in testing mode make use of either sandbox.payfast.co.za or www.payfast.co.za
            $testingMode = false;
            $pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
            
            $htmlForm = '<form action="https://'.$pfHost.'/eng/process" method="post" name="pay_now">';
            foreach($payfast_data as $name=> $value)
            {
            $htmlForm .= '<input name="'.$name.'" type="hidden" value=\''.$value.'\' />';
            }
            $htmlForm .= '</form>';
            $data['htmlForm']= $htmlForm;
            
            $this->load->view('payment/payfast/pay', $data);
        }
    }

    public  function generateSignature($data, $passPhrase = null) {
        // Create parameter string
        $pfOutput = '';
        foreach( $data as $key => $val ) {
            if($val !== '') {
                $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
            }
        }
        // Remove last ampersand
        $getString = substr( $pfOutput, 0, -1 );
        if( $passPhrase !== null ) {
            $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
        }
        return md5( $getString );
    }
 
    public function success() {
        $payfast_payment_id  = $this->session->userdata('payfast_payment_id');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($payfast_payment_id,'payfast');

        if($parameter_data['payment_status']!='CANCELLED'){
            if($parameter_data['payment_status']=='COMPLETE'){
                $gateway_response['paid_status']= 1;
            }else{
                $gateway_response['paid_status']= 2;
            }
            
            redirect(base_url("payment/successinvoice/"));
           
        }else{
           redirect(site_url('payment/paymentfailed'));
        }

    }

    public function cancel(){
        redirect(site_url('payment/paymentfailed'));
    }
}