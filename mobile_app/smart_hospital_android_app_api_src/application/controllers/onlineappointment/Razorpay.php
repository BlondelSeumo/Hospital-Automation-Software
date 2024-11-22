<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Razorpay extends Admin_Controller {
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
        $this->load->view('onlineappointment/razorpay/razorpay', $data);
    }

    public function pay(){
        $data = array();
        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $amount          = $this->session->userdata('payment_amount');
        $this->form_validation->set_rules('email', 'Email', 'trim');
        $this->form_validation->set_rules('phone', 'Phone', 'trim');
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
        $this->load->view('onlineappointment/razorpay/razorpay', $data);
        }else{
            $logourl=str_replace('api/', '', base_url());
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL            =>$logourl.'app',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "POST",
                CURLOPT_HTTPHEADER     => array(
                    "authorization: Bearer sY6xc8tAS7Wj8-MXyXxheg",
                    "content-type: application/json",
                ),
            ));
            $response = curl_exec($curl);
            $err      = curl_error($curl);
            curl_close($curl);
            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                $res_arr=json_decode($response);
            }
            $data['key_id']=$this->payment_method->api_publishable_key;
            $data['total']=$amount*100;
            $data['currency'] = $payment_details['invoice']->currency_name;
            $data['name'] = $payment_details['name'];
            $data['title'] = 'Bill Payment Smart Hospital';  
            $data['image']=$res_arr->site_url."uploads/hospital_content/logo/".$res_arr->app_logo;
            $this->load->view('onlineappointment/razorpay/pay', $data);
        }
    }  
  
    
    public function success($amount){
        if(isset($_POST['razorpay_payment_id']) && $_POST['razorpay_payment_id']!=''){         
            $transactionid=$_POST['razorpay_payment_id'];
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
                'payment_mode'   => 'Razorpay',
                'note'           => "Payment deposit through Razorpay TXN ID: " . $transactionid,
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
                'note'                   => "Online fees deposit through Razorpay TXN ID: " . $transactionid,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );

            $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
            if($status){
                redirect(base_url("payment/appointmentsuccess/".$appointment_id));
            }
        }else{ 
             redirect(base_url("payment/paymentfailed"));
        }
    }

    
}

