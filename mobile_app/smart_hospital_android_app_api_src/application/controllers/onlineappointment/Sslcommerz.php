<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sslcommerz extends Admin_Controller {
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
        $this->load->view('onlineappointment/sslcommerz/sslcommerz', $data);
	}

    public function pay()
    {
        $data = array();
        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $amount          = $this->session->userdata('payment_amount');
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
     
        if ($this->form_validation->run()==false) {
            $this->load->view('onlineappointment/sslcommerz/sslcommerz', $data);
        }else{ 
         $payment_details=$this->session->userdata('params');
        if ($this->session->has_userdata('params')) {
            $api_publishable_key = ($this->payment_method->api_publishable_key);
            $api_secret_key = ($this->payment_method->api_secret_key);
            $data['api_publishable_key'] = $api_publishable_key;
            $data['api_secret_key'] = $api_secret_key; 
        }
        $requestData=array();
            $CURLOPT_POSTFIELDS=array(
            'store_id'=>$this->payment_method->api_publishable_key,
            'store_passwd'=>$this->payment_method->api_password,
            'total_amount'=>$amount,
            'currency'=>$this->setting[0]['currency'],
            'tran_id'=>abs(crc32(uniqid())),
            'success_url'=>base_url().'onlineappointment/sslcommerz/success',
            'fail_url'=>base_url().'onlineappointment/sslcommerz/fail',
            'cancel_url'=>base_url().'onlineappointment/sslcommerz/cancel',
            'cus_name'=>    $payment_details['name'],
            'cus_email'=>   $this->input->post('email'),
            'cus_add1'=>    "Dhaka",
            'cus_phone'=>   $this->input->post('phone'),
            'cus_city'=>'',
            'cus_country'=>'',
            'multi_card_name'=>'mastercard,visacard,amexcard,internetbank,mobilebank,othercard ',
            'shipping_method'=>'NO',
            'product_name'=>'test',
            'product_category'=>'Electronic',
            'product_profile'=>'general'
        );
            $string="";
            foreach ($CURLOPT_POSTFIELDS as $key => $value) {
                $string.=$key.'='.$value."&";
                if($key=='product_profile'){
                $string.=$key.'='.$value;
                }
            } 
            //echo $string;die;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$string");

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $response=json_decode($result);
        
        header("Location: $response->GatewayPageURL");
        }
    }
 
	
    public function success(){
        if ($_POST['status'] == 'VALID') {
            $transactionid = $_POST['val_id']; 
	        $session_data = $this->session->userdata("params");
            $appointment_id   = $session_data['appointment_id'];
            $patient_id  = $session_data['patient_id'];
            $charge_id  = $this->session->userdata('charge_id');
            $amount                             = $this->session->userdata('payment_amount');
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'transaction_id' => $transactionid,
                'payment_type'   => 'Online',
                'payment_mode'   => 'Sslcommerz',
                'note'           => "Payment deposit through Sslcommerz TXN ID: " . $transactionid,
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
                'note'                   => "Online fees deposit through Sslcommerz TXN ID: " . $transactionid,
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

