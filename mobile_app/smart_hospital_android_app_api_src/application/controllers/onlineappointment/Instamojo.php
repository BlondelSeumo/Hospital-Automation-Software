<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Instamojo extends Admin_Controller {
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
        $this->load->view('onlineappointment/instamojo/instamojo', $data);
	}

    public function pay(){
        $data = array();
        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $amount          = $this->session->userdata('payment_amount');
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
     
        if ($this->form_validation->run()==false) {
            $data['amount'] = $amount;
            $this->load->view('onlineappointment/instamojo/instamojo', $data);
        }else{
            $payment_details=$this->session->userdata('params');
            if ($this->session->has_userdata('params')) {
                $api_publishable_key = ($this->payment_method->api_publishable_key);
                $api_secret_key = ($this->payment_method->api_secret_key);
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key'] = $api_secret_key; 
            }
            $ch = curl_init();
            // for test https://test.instamojo.com/api/1.1/payment-requests/
            // for live https://www.instamojo.com/api/1.1/payment-requests/
            curl_setopt($ch, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payment-requests/');
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                        array("X-Api-Key:$api_secret_key",
                              "X-Auth-Token:$api_publishable_key"));
            $payload = Array(
                'purpose'       => 'Bill Payment',
                'amount'        => $amount,
                'phone'         => $_POST['phone'],
                'buyer_name'    => $payment_details['name'],
                'redirect_url'  => base_url().'onlineappointment/instamojo/success',
                'send_email'    => false,
                'webhook'       => base_url().'webhooks/insta_webhook',
                'send_sms'      => false,
                'email'         => $_POST['email'],
                'allow_repeated_payments' => false
            );

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            $response = curl_exec($ch);
            curl_close($ch); 
            $json = json_decode($response, true);
            if($json['success']){
                $url = $json['payment_request']['longurl']; 
                header( "Location: $url" );
            }else{
                $data = array();
                $data['session_params'] = $this->session->userdata('params');
                $data['setting'] = $this->setting;
                $json = json_decode($response, true);
                $data['amount'] = $amount;
                $data['api_error']=$json['message'];
                $this->load->view('onlineappointment/instamojo/instamojo', $data);
            }
        }
    }
 
	
    public function success(){
        $session_data = $this->session->userdata("params");
        $appointment_id   = $session_data['appointment_id'];
        $patient_id  = $session_data['patient_id'];
        $charge_id  = $this->session->userdata('charge_id');
        if ($_GET['payment_status'] == 'Credit') {
            $amount                             = $this->session->userdata('payment_amount');
            $transactionid                      = $_GET['payment_id'];
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'transaction_id' => $transactionid,
                'payment_type'   => 'Online',
                'payment_mode'   => 'Instamojo',
                'note'           => "Payment deposit through Instamojo TXN ID: " . $transactionid,
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
                'note'                   => "Online fees deposit through Instamojo TXN ID: " . $transactionid,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );

            $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
            if($status){
                redirect(base_url("payment/appointmentsuccess/".$appointment_id));
            }
        }else{
            redirect(site_url('payment/paymentfailed'));
        }
    }
}

