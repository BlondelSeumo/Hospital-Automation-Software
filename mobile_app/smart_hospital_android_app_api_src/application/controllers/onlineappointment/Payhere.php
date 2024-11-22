<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payhere extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

	public function __construct()
	{ 
		parent::__construct();
        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->getActiveMethod();
        $this->load->model(array('gateway_ins_model'));
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
        $this->load->view('onlineappointment/payhere/index', $data);
	}

    public function pay(){

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
        $data = array();
        $data['api_error']=array();
        $amount          = $this->session->userdata('payment_amount');
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');

        if ($this->form_validation->run()==false) {
            $data['amount'] = $total;
            $this->load->view('onlineappointment/payhere/index', $data);
        }else{
            $payment_details=$this->session->userdata('params');
            if ($this->session->has_userdata('params')) {
                $api_publishable_key = ($this->payment_method->api_publishable_key);
                $api_secret_key = ($this->payment_method->api_secret_key);
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key'] = $api_secret_key; 
            }
 $htmlform=array(
            'merchant_id'=>$this->payment_method->api_publishable_key,
            'return_url'=>base_url().'onlineappointment/payhere/success',
            'cancel_url'=>base_url().'onlineappointment/payhere/cancel',
            'notify_url'=>base_url().'gateway_ins/payhere',
            'order_id'=>time().rand(99,999),
            'items'=>'Student Fees',
            'currency'=>$this->setting[0]['currency'],
            'amount'=>$total,
            'first_name'=>$payment_details['name'],
            'last_name'=>'',
            'email'=>"",
            'phone'=>"",
            'address'=>'',
            'city'=>'',
            'country'=>''
        );

        $data['htmlform']=$htmlform;
        $data['params']['transaction_id']=$htmlform['order_id'];
        $ins_data=array(
            'unique_id'=>$data['params']['transaction_id'],
            'parameter_details'=>json_encode($htmlform),
            'gateway_name'=>'payhere',
            'type'=>'appointment',
            'online_appointment_id'=>$appointment_id,
            'module_type'=>'appointment',
            'payment_status'=>'processing',
            );
             $payment_section = $this->config->item('payment_section');
            $save_record = array(
                'amount'                 => $total,
                'patient_id'             => $payment_details['patient_id'],
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Payhere TXN ID: " . $data['params']['transaction_id'],
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => 1,
            );

            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
            $save_record["gateway_ins_id"] = $gateway_ins_id;

            $this->gateway_ins_model->add_transactions_processing($save_record);

            $this->session->set_userdata("payhere_payment_id",$data['params']['transaction_id']);
   
            $this->load->view('onlineappointment/payhere/pay', $data);
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

