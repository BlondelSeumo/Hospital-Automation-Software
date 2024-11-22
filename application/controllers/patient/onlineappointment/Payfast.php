<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payfast extends Patient_Controller
{
    public $pay_method = "";

    function __construct() {
        parent::__construct();
        $this->config->load("payroll");
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->setting        = $this->setting_model->get()[0];
        $this->load->model(array('onlineappointment_model','charge_model','gateway_ins_model'));
    }
 
    public function index()
    {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting'] = $this->setting;
        $charges_array = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
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
        $total = $charge;
        $data['amount'] = $total;
        $data['currency']    = $data['setting']['currency'];

        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
            $data['amount'] = $total;
            $data['standard_charge']=$standard_charge;
            $data['tax_amount']=$tax;
            $this->load->view('patient/onlineappointment/payfast/index', $data);
        }else{

            $cartTotal = $total;// This amount needs to be sourced from your application
            $payfast_data = array(
            'merchant_id' => $this->pay_method->api_publishable_key,
            'merchant_key' => $this->pay_method->api_secret_key,
            'return_url' => base_url().'patient/onlineappointment/payfast/success',
            'cancel_url' => base_url().'patient/onlineappointment/payfast/cancel',
            'notify_url' => base_url().'patient/gateway_ins/payfast',
            'name_first' => $appointment_data->name,
            'email_address'=>$_POST['email'],
            'm_payment_id' => time().rand(99,999).time(), //Unique payment ID to pass through to notify_url
            'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
            'item_name' => 'reference_id#'.$appointment_id,
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
                'patient_id'             => $this->customlib->getPatientSessionUserID(),
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
            $testingMode = true;
            $pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
            
            $htmlForm = '<form action="https://'.$pfHost.'/eng/process" method="post" name="pay_now">';
            foreach($payfast_data as $name=> $value)
            {
            $htmlForm .= '<input name="'.$name.'" type="hidden" value=\''.$value.'\' />';
            }
            $htmlForm .= '</form>';
            $data['htmlForm']= $htmlForm;
            
            $this->load->view('patient/onlineappointment/payfast/pay', $data);
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

            redirect(base_url("patient/onlineappointment/checkout/successinvoice/$appointment_id"));
           
        }else{
           redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }

    }

    public function cancel(){
        redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
    }
}