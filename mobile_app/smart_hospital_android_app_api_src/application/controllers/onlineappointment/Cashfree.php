<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cashfree extends Admin_Controller
{
    public $api_config = "";
    public $pay_method = array();

    public function __construct()
    {
        parent::__construct();
        $this->setting = $this->setting_model->get();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
    }

    public function index() {

        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
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

        $payment_details=$this->session->userdata("params");
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $this->load->view('onlineappointment/cashfree/index', $data);
    } 
 
    
    public function pay()
    {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
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

        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');

        $setting = $this->setting[0];
        $result = array();
        $data['currency'] = $setting['currency'];

        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
     
        if ($this->form_validation->run() == false) {
            $data['amount'] = $total;
            $this->load->view('onlineappointment/cashfree/index', $data);
        }else{
            $amount = number_format((float)($total), 2, '.', '');
            
            $order_id = "order_".time().mt_rand(100,999);
            $currency = $data['currency'];
            $customer_id = "Reference_id_".$order_id;
            $redirectUrl=base_url()."onlineappointment/cashfree/success?order_id={order_id}&order_token={order_token}";

            $my_array=array(
                "order_id"=>$order_id,
                "order_amount"=>$amount,
                "order_currency"=>$currency,
                "customer_details"=>array(
                "customer_id"=>$customer_id,
                "customer_name"=>$payment_details['name'],
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

                curl_setopt($ch, CURLOPT_URL, 'https://api.cashfree.com/pg/orders');
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
                $data = array();
                $payment_details = $this->session->userdata('params');
                $data['session_params']=$payment_details;
                $data['amount'] = $total;
                $data['api_error']=array();
                $data['api_error']=$json->message;
              
                $this->load->view('onlineappointment/cashfree/index', $data);
            }
        }
    }

    /**
     * This is a callback function for movies payment completion
     */
    public function success()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.cashfree.com/pg/orders/'.$_GET['order_id']);
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
        $gateway_response=json_decode($result);

       if (isset($gateway_response->order_status) && $gateway_response->order_status=="PAID") {
            $payment_data = $this->session->userdata('params');
            $amount  = $this->session->userdata('payment_amount');
            $appointment_id  = $this->session->userdata('appointment_id');
            $charge_id  = $this->session->userdata('charge_id');

            $transactionid                      = $_GET['order_id'];

            $payment_section = $this->config->item('payment_section');
            $save_record['appointment_id'] = $appointment_id; 
            $save_record['paid_amount']    = $amount;
            $save_record['transaction_id'] = $transactionid;
            $save_record['charge_id']      = $charge_id;
            $save_record['payment_mode']   = 'Cashfree';
            $save_record['payment_type']   = 'Online';
            $save_record['note']           = "Payment deposit through Cashfree TXN ID: " . $transactionid;
            $save_record['date']           = date("Y-m-d H:i:s");

            $transaction_array = array(
                'amount'                 => $amount,
                'patient_id'             => $payment_data['patient_id'],
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Cashfree TXN ID: " . $transactionid ,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => 1,
            );

            $this->webservice_model->paymentSuccess($save_record,$transaction_array);
            redirect(base_url("payment/appointmentsuccess/".$appointment_id));
        } else {
            redirect(site_url('payment/paymentfailed'));
        }

    }
}