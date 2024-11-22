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
        $this->load->view('onlineappointment/billplz/billplz', $data);
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
            $this->load->view('onlineappointment/billplz/billplz', $data);
        }else{ 
            $payment_details=$this->session->userdata('params');
            if ($this->session->has_userdata('params')) {
                $api_publishable_key = ($this->payment_method->api_publishable_key);
                $api_secret_key = ($this->payment_method->api_secret_key);
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key'] = $api_secret_key; 
            }
            $data['return_url']  = base_url().'onlineappointment/billplz/success';
            $data['total']       = $amount;
            $data['productinfo'] = "bill payment smart hospital";
            $parameter           = array(
                'title'       => $payment_details['name'],
                'description' => $data['productinfo'],
                'amount'      => $amount * 100,
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
            if ($_GET['billplz']['paid'] == 'true') {
                $session_data = $this->session->userdata("params");
                $appointment_id   = $session_data['appointment_id'];
                $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
                $patient_id  = $session_data['patient_id'];
                $charge_id  = $this->session->userdata('charge_id');
                $amount     = $this->session->userdata('payment_amount');
                $transactionid = $_GET['billplz']['id'];
                $payment_data = array(
                    'appointment_id' => $appointment_id,
                    'paid_amount'    => $amount,
                    'charge_id'      => $charge_id,
                    'transaction_id' => $transactionid,
                    'payment_type'   => 'Online',
                    'payment_mode'   => 'Billplz',
                    'note'           => "Payment deposit through Billplz TXN ID: " . $transactionid,
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
                    'note'                   => "Online fees deposit through Billplz TXN ID: " . $transactionid,
                    'payment_date'           => date('Y-m-d H:i:s'),
                    'received_by'            => '',
                );

                $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
                if($status){
                    redirect(base_url("payment/appointmentsuccess/".$appointment_id));
                }
            } else {
                redirect(site_url('payment/paymentfailed'));
            }
        }else {
            redirect(site_url('payment/paymentfailed'));
        }
    }
}
