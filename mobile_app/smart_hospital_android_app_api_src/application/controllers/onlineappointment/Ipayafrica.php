<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ipayafrica extends Admin_Controller {
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
	{  $payment_details=$this->session->userdata("params"); 
        $data = array(); 
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
              
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
        if ($this->form_validation->run()==false) {
            
            $data['api_error']=array();
            $data['session_params']=$payment_details;
            $data['productinfo'] = "bill payment smart hospital";
            $data['setting'] = $this->setting;
            $this->load->view('onlineappointment/ipayafrica/index', $data);
        }else{
            if ($this->session->has_userdata('params')) {
                $api_publishable_key = ($this->payment_method->api_publishable_key);
                $api_secret_key = ($this->payment_method->api_secret_key);
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key'] = $api_secret_key;
            }
            $amount          = $data['amount'];
            $fields = array(
                "live"=> "1",
                "oid"=> $payment_details['appointment_id'].time(),
                "inv"=> time(),
                "ttl"=> $amount,
                "tel"=> $_POST['phone'],
                "eml"=> $_POST['email'],
                "vid"=> $api_publishable_key,
                "curr"=>$payment_details['invoice']->symbol,
                "p1"=> "airtel",
                "p2"=> "",
                "p3"=> "",
                "p4"=> $amount,
                "cbk"=> base_url().'onlineappointment/ipayafrica/success',
                "cst"=> "1",
                "crl"=> "2"
            );
            $datastring =  $fields['live'].$fields['oid'].$fields['inv'].$fields['ttl'].$fields['tel'].$fields['eml'].$fields['vid'].$fields['curr'].$fields['p1'].$fields['p2'].$fields['p3'].$fields['p4'].$fields['cbk'].$fields['cst'].$fields['crl'];

            $hashkey =$api_secret_key;
            $generated_hash = hash_hmac('sha1',$datastring , $hashkey);
            $data['fields']=$fields;
            $data['generated_hash']=$generated_hash;
            
            $this->load->view("payment/ipayafrica/pay", $data);
        }

       
	}


 
	
    public function success(){
        if(!empty($_GET['status'])){
    	   if ($this->session->has_userdata('params')) {
                $session_data = $this->session->userdata("params");
                $amount       = $this->session->userdata('payment_amount');
                $appointment_id   = $session_data['appointment_id'];
                $patient_id  = $session_data['patient_id'];
                $charge_id  = $this->session->userdata('charge_id');
            }
            $transactionid = $_GET['txncd'];
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'transaction_id' => $transactionid,
                'payment_type'   => 'Online',
                'payment_mode'   => 'Ipayafrica',
                'note'           => "Payment deposit through Ipayafrica TXN ID: " . $transactionid,
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
                'note'                   => "Online fees deposit through Ipayafrica TXN ID: " . $transactionid,
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

