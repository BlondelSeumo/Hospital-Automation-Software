<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payfast extends Patient_Controller
{

    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->patient_data   = $this->session->userdata('patient');
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->setting        = $this->setting_model->get();
        $this->load->model(array('gateway_ins_model'));
    }
 
    public function index()
    {
        $payment_data = $this->session->userdata('payment_data');
        $setting             = $this->setting[0];
        $data                = array();
        $data['setting'] = $setting;
        $data['case_reference_id']   = $payment_data['case_reference_id'];
        $data['amount'] = $payment_data['deposit_amount'];
        $data['currency']            = $setting['currency'];

        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
           
            $this->load->view("layout/patient/header");
            $this->load->view('patient/payment/payfast/index', $data);
            $this->load->view("layout/patient/footer");
        }else{
            $cartTotal = $payment_data['deposit_amount'];// This amount needs to be sourced from your application
            $payfast_data = array(
            'merchant_id' => $this->pay_method->api_publishable_key,
            'merchant_key' => $this->pay_method->api_secret_key,
            'return_url' => base_url().'patient/payment/payfast/success',
            'cancel_url' => base_url().'patient/payment/payfast/cancel',
            'notify_url' => base_url().'patient/gateway_ins/payfast',
            'name_first' => $this->patient_data['name'],
            'email_address'=>$_POST['email'],
            'm_payment_id' => time().rand(99,999).time(), //Unique payment ID to pass through to notify_url
            'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
            'item_name' => 'reference_id#'.$this->patient_data['patient_id'],
            );
           
            $signature = $this->generateSignature($payfast_data,$this->pay_method->salt);
            $payfast_data['signature'] = $signature;           
            $ins_data=array(
            'unique_id'=>$payfast_data['m_payment_id'],
            'parameter_details'=>json_encode($payfast_data),
            'gateway_name'=>'payfast',
            'type'=>'patient_bill',
            'online_appointment_id'=>null,
            'module_type'=>'patient_bill',
            'payment_status'=>'processing',
            );

            $transactionid  = $payfast_data['m_payment_id'];
            $save_record = array(
                'case_reference_id' => $payment_data["case_reference_id"],
                'type' => "payment",
                'amount'  => $payment_data['deposit_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Payfast TXN ID: " . $transactionid,
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

            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
            $save_record["gateway_ins_id"] = $gateway_ins_id;

            $this->gateway_ins_model->add_transactions_processing($save_record);

            $this->session->set_userdata("payfast_payment_id",$payfast_data['m_payment_id']);
            // If in testing mode make use of either sandbox.payfast.co.za or www.payfast.co.za
            $testingMode = true;
            $pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
            
            $htmlForm = '<form action="https://'.$pfHost.'/eng/process" method="post" name="pay_now">';
            foreach($payfast_data as $name=> $value)
            {
            $htmlForm .= '<input name="'.$name.'" type="hidden" value=\''.$value.'\' />';
            }
            $htmlForm .= '</form>';
            $data['htmlForm']= $htmlForm;
            
            $this->load->view('patient/payment/payfast/pay', $data);
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
            
            redirect(base_url("patient/pay/successinvoice/"));
           
        }else{
           redirect(base_url("patient/pay/paymentfailed/"));
        }

    }

    public function cancel(){
        redirect(base_url("patient/pay/paymentfailed/"));
    }
}