<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sslcommerz extends Patient_Controller
{

    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting = $this->setting_model->get()[0];
        $this->load->model(array('onlineappointment_model','charge_model'));
    }

    public function index() {
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
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
        $this->load->view('patient/onlineappointment/sslcommerz/index', $data);
    } 

	public function pay(){
		$appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
		$amount = $this->session->userdata('payment_amount');
		$requestData=array();
		$CURLOPT_POSTFIELDS=array(
        	'store_id'=>$this->pay_method->api_publishable_key,
        	'store_passwd'=>$this->pay_method->api_password,
        	'total_amount'=>$amount,
        	'currency'=>$this->setting['currency'],
        	'tran_id'=>abs(crc32(uniqid())),
        	'success_url'=>base_url().'patient/onlineappointment/sslcommerz/success',
        	'fail_url'=>base_url().'patient/onlineappointment/sslcommerz/fail',
        	'cancel_url'=>base_url().'patient/onlineappointment/sslcommerz/cancel',
        	'cus_name'=>$appointment_data->name,
        	'cus_email'=>!empty($appointment_data->email) ? $appointment_data->email : "example@email.com",
        	'cus_add1'=>!empty($appointment_data->permanent_address) ? $appointment_data->permanent_address : "Dhaka",
        	'cus_phone'=>!empty($appointment_data->mobileno) ? $appointment_data->mobileno : "01711111111",
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

		public function success(){

		if ($_POST['status'] == 'VALID') {
            $patient_data  = $this->session->userdata('patient');
            $patient_id  = $patient_data['patient_id'];
            $amount = $this->session->userdata('payment_amount');
            $appointment_id = $this->session->userdata('appointment_id');
            $charge_id = $this->session->userdata('charge_id');
            $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
			$transactionid = $_POST['val_id']; 
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'payment_type'   => 'Online',
                'transaction_id'=>$transactionid,
                'payment_mode'   => 'SSLCommerz',
                'note'           => "Payment deposit through SSLCommerz TXN ID: " . $transactionid,
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
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );
            
            $return_detail                      = $this->onlineappointment_model->paymentSuccess($payment_data,$transaction_array);
          redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail['insert_id']));
        } else { 

            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }

		}

		public function fail(){
		 redirect(base_url("patient/onlineappointment/checkout"));
		}
		
		public function cancel(){
		 redirect(base_url("patient/onlineappointment/checkout"));
		}


   
}

