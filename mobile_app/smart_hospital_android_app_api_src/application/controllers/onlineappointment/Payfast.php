<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payfast extends Admin_Controller
{
    public $api_config = "";
    public $pay_method = array();

    function __construct() {
        parent::__construct();
        $this->setting        = $this->setting_model->get()[0];
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->load->model(array('gateway_ins_model'));
    }
 
    public function index()
    {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
        $tax=0;
        $standard_charge=0;
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
            $tax=($charges_array->standard_charge*$charges_array->percentage/100);
            $standard_charge=$charges_array->standard_charge;
        }else{
            $charge=0;
            $tax=0;
            $standard_charge=0;
        } 
        $data['standard_charge']=$standard_charge;
        $data['tax_amount']=$tax;
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;

        $payment_details=$this->session->userdata("params");
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['currency']            = $this->setting['currency'];

        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
        
        if ($this->form_validation->run() == false) {
            $data['amount'] = $total;
            $this->load->view('onlineappointment/payfast/index', $data);
        }else{
            
            $cartTotal = $total;// This amount needs to be sourced from your application
            $payfast_data = array(
            'merchant_id' => $this->pay_method->api_publishable_key,
            'merchant_key' => $this->pay_method->api_secret_key,
            'return_url' => base_url().'onlineappointment/payfast/success',
            'cancel_url' => base_url().'onlineappointment/payfast/cancel',
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
            'type'=>'appointment',
            'online_appointment_id'=>$appointment_id,
            'module_type'=>'appointment',
            'payment_status'=>'processing',
            );

            $transactionid  = $payfast_data['m_payment_id'];
            $payment_section = $this->config->item('payment_section');
            $save_record = array(
                'amount'                 => $total,
                'patient_id'             => $payment_details['patient_id'],
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Payfast TXN ID: " . $transactionid ,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => 1,
            );

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
            
            $this->load->view('onlineappointment/payfast/pay', $data);
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
        $appointment_id = $this->session->userdata('appointment_id');
        $payfast_payment_id  = $this->session->userdata('payfast_payment_id');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($payfast_payment_id,'payfast');

        if($parameter_data['payment_status']!='CANCELLED'){
            if($parameter_data['payment_status']=='COMPLETE'){
                $gateway_response['paid_status']= 1;
            }else{
                $gateway_response['paid_status']= 2;
            }
            redirect(base_url("payment/appointmentsuccess/".$appointment_id));  
        }else{
           redirect(site_url('payment/paymentfailed'));
        }

    }

    public function cancel(){
        redirect(site_url('payment/paymentfailed'));
    }
}