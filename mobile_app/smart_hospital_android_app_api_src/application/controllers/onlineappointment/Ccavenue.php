<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ccavenue extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

	public function __construct()
	{ 
		parent::__construct();

        $this->load->library('Ccavenue_crypto');
        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->getActiveMethod();
	}
 
	public function index()
	{ 
        $payment_details  = $this->session->userdata("params");
        $appointment_id   = $payment_details['appointment_id'];
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $data['setting']  = $this->setting;
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
        }else{
            $charge=0;
        }
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        $this->load->view('onlineappointment/ccavenue/ccavenue', $data);
	}

    public function pay()
    {
        $data = array();
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $amount          = $this->session->userdata('payment_amount');
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
     
        if ($this->form_validation->run()==false) {
                    $payment_details  = $this->session->userdata("params");
        $appointment_id   = $payment_details['appointment_id'];
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $data['setting']  = $this->setting;
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
        }else{
            $charge=0;
        }
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        $this->load->view('onlineappointment/ccavenue/ccavenue', $data);
        }else{
            $payment_details=$this->session->userdata('params');
            $payment_details['post_amount']=$this->input->post('amount');
            $this->session->set_userdata("params", $payment_details);
            if ($this->session->has_userdata('params')) {
                $api_publishable_key = $this->payment_method->api_publishable_key;
                $api_secret_key = $this->payment_method->api_secret_key;
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key'] = $api_secret_key;
            }
        
            $details['tid']=abs(crc32(uniqid()));
            $details['merchant_id']=$api_secret_key;
            $details['order_id']=abs(crc32(uniqid()));
            $details['amount']=number_format((float)($payment_details['post_amount']), 2, '.', '');
            $details['currency']='INR';
            $details['redirect_url']=base_url('onlineappointment/ccavenue/success'); 
            $details['cancel_url']  =base_url('onlineappointment/ccavenue/cancel');
            $details['language'] = "EN";
            $details['billing_name']     = $payment_details['name'];
            $details['billing_email']= $this->input->post("email");
            $details['billing_tel']= $this->input->post("phone");
            $merchant_data="";
            foreach ($details as $key => $value){
                $merchant_data.=$key.'='.$value.'&';
            } 
            $data['encRequest'] = $this->ccavenue_crypto->encrypt($merchant_data,$this->payment_method->salt);
            $data['access_code'] = $this->payment_method->api_publishable_key;
            $this->load->view('onlineappointment/ccavenue/pay', $data);
        }
    }
 
	
    public function success(){
        $session_data = $this->session->userdata("params");
        $appointment_id   = $session_data['appointment_id'];
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $patient_id  = $session_data['patient_id'];
        $charge_id  = $this->session->userdata('charge_id');
        $pay_method = $this->paymentsetting_model->getActiveMethod();
        $encResponse=$_POST["encResp"];  
        $rcvdString=$this->ccavenue_crypto->decrypt($encResponse,$pay_method->salt);  
        $order_status=array();
        $decryptValues=explode('&', $rcvdString);
        $dataSize=sizeof($decryptValues);
        for($i = 0; $i < $dataSize; $i++) 
        {
            $information=explode('=',$decryptValues[$i]);
            $status[$information[0]]=$information[1];
        }
        if($status['order_status']=="Success")
        { 
            $transactionid = $status['tracking_id'];
            $bank_ref_no = $status['bank_ref_no'];
	        if ($this->session->has_userdata('params')) {
                $payment_data = $this->session->userdata('params');
                $data['amount'] = $payment_data['post_amount'];                
            }
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'transaction_id' => $transactionid,
                'payment_type'   => 'Online',
                'payment_mode'   => 'Ccavenue',
                'note'           => "Payment deposit through Ccavenue TXN ID: " . $transactionid,
                'date'           => date("Y-m-d H:i:s"),
            ); 
            $payment_section = $this->config->item('payment_section');
            $transaction_array = array(
                'amount'                 => $amount,
                'patient_id'             => $patient_id,
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Ccavenue TXN ID: " . $transactionid,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );

            $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
            if($status){
                 redirect(base_url("payment/appointmentsuccess/".$appointment_id));
            }
        }else if($status['order_status']==="Aborted"){
            echo "<br>We will keep you posted regarding the status of your order through e-mail";
        }
        else if($status['order_status']==="Failure")
        {
            redirect(base_url("onlineappointment/paymentfailed"));
        }
        else
        {
            echo "<br>Security Error. Illegal access detected";
        
        }
    }
}

