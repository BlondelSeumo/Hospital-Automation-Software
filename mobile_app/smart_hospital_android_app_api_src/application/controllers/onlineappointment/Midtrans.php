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
        $this->load->view('onlineappointment/midtrans/midtrans', $data);
    }

    public function pay()
    {
        $data = array();
        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $amount          = $this->session->userdata('payment_amount');
        $data['session_params'] = $payment_details;
        $this->form_validation->set_rules('email', 'Email', 'trim');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');
     
        if ($this->form_validation->run()==false) {
            $this->load->view('onlineappointment/midtrans/midtrans', $data);
        }else{
        $transaction = array(
            'transaction_details' => array(
                'order_id'     => time(),
                'gross_amount' => round($amount), // no decimal allowed   
            ),
        );
        $snapToken=$this->midtrans_lib->getSnapToken($transaction,$this->payment_method->api_secret_key);
        $data['snap_Token'] = $snapToken;
        $this->load->view('onlineappointment/midtrans/pay', $data);
        }
    }
 
    public function success(){
    	$session_data = $this->session->userdata("params");
        $appointment_id   = $session_data['appointment_id'];
        $patient_id  = $session_data['patient_id'];
        $charge_id  = $this->session->userdata('charge_id');
        $payment_data = $this->session->userdata('params');
        $amount                             = $this->session->userdata('payment_amount');
        $response=json_decode($_POST['result_data']);
        $transactionid=$response->transaction_id;
        $payment_data = array(
            'appointment_id' => $appointment_id,
            'paid_amount'    => $amount,
            'charge_id'      => $charge_id,
            'transaction_id' => $transactionid,
            'payment_type'   => 'Online',
            'payment_mode'   => 'Midtrans',
            'note'           => "Payment deposit through Midtrans TXN ID: " . $transactionid,
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
            'note'                   => "Online fees deposit through Midtrans TXN ID: " . $transactionid,
            'payment_date'           => date('Y-m-d H:i:s'),
            'received_by'            => '',
        );

        $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
        if($status){
            echo json_encode(array("status" => "success","appointment_id"=>$appointment_id));
        }	
	}
}

