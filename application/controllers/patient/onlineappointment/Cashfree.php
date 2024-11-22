<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cashfree extends Patient_Controller
{

    public $pay_method     = array();
    public $patient_data;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->setting        = $this->setting_model->get()[0];
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
        $this->load->view('patient/onlineappointment/cashfree/index', $data);

    }

    public function pay()
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
        $payment_amount=$this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
        $data['error']="";
        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
            
        }else{
        $amount =number_format((float)($total), 2, '.', '');
        $customer_id="Reference_id_".$appointment_id;
        $order_id="order_".time().mt_rand(100,999);
        $setting             = $this->setting;
        $currency=$setting['currency'];
   
        $redirectUrl=base_url()."patient/onlineappointment/cashfree/success?order_id={order_id}&order_token={order_token}";

        $my_array=array(
            "order_id"=>$order_id,
            "order_amount"=>$amount,
            "order_currency"=>$currency,
            "customer_details"=>array(
            "customer_id"=>$customer_id,
            "customer_name"=>$appointment_data->name,
            "customer_email"=>$_POST['email'],
            "customer_phone"=>$_POST['phone'],
            ),
            "order_meta"=> array(
            "return_url"=> $redirectUrl,
            "notify_url"=> base_url() .'webhooks/cashfree',
            "payment_methods"=> ""
            )
        );
        
        $new_arrya=(object)$my_array;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://sandbox.cashfree.com/pg/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($new_arrya));

            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'X-Api-Version: 2021-05-21';
            $headers[] = 'X-Client-Id: '.$this->pay_method->api_publishable_key;
            $headers[] = 'X-Client-Secret: '.$this->pay_method->api_secret_key;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $json=json_decode($result);

            if (isset($json->order_status) && $json->order_status="ACTIVE") {
                $url = $json->payment_link;
                header("Location: $url");
            } else {

           

            $data['error']=$json->message;
           
           
        }
    }
  
     
            $this->load->view('patient/onlineappointment/cashfree/index', $data);
            
    }

    /**
     * This is a callback function for movies payment completion
     */
    public function success()
    {
        $amount  = $this->session->userdata('payment_amount');
        $appointment_id  = $this->session->userdata('appointment_id');
        $charge_id  = $this->session->userdata('charge_id');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://sandbox.cashfree.com/pg/orders/'.$_GET['order_id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-Api-Version: 2021-05-21';
        $headers[] = 'X-Client-Id: '.$this->pay_method->api_publishable_key;
        $headers[] = 'X-Client-Secret: '.$this->pay_method->api_secret_key;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $response_data=json_decode($result);

       if (isset($response_data->order_status) && $response_data->order_status=="PAID") {

            $transactionid                      = $_GET['order_id'];
            $payment_section = $this->config->item('payment_section');
            $gateway_response['appointment_id'] = $appointment_id;
            $gateway_response['paid_amount']    = $amount;
            $gateway_response['transaction_id'] = $transactionid;
            $gateway_response['charge_id']      = $charge_id;
            $gateway_response['payment_mode']   = 'Cashfree';
            $gateway_response['payment_type']   = 'Online';
            $gateway_response['note']           = "Payment deposit through Cashfree TXN ID: " . $transactionid;
            $gateway_response['date']           = date("Y-m-d H:i:s");

            $transaction_array = array(
                'amount'                 => $amount,
                'patient_id'             => $this->customlib->getPatientSessionUserID(),
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Cashfree TXN ID: " . $transactionid ,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => 1,
            );

            $return_detail = $this->onlineappointment_model->paymentSuccess($gateway_response,$transaction_array);

            redirect(base_url("patient/onlineappointment/checkout/successinvoice/$appointment_id"));

        } else {

            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }

    }
}