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

        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['amount']=$payment_details['total'];
        $this->load->view('payment/cashfree/index', $data);
    } 
 
    
    public function pay()
    {
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $result = array();
        $data['currency']            = $setting['currency'];

        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
     
        if ($this->form_validation->run() == false) {
            $data['amount']=$payment_details['total'];
            $this->load->view('payment/cashfree/index', $data);
        }else{
            $payment_details['post_amount']=$this->input->post('amount');
            $this->session->set_userdata("params", $payment_details);
            $amount = number_format((float)($payment_details['post_amount']), 2, '.', '');
            $customer_id = "Reference_id_".$payment_details['name'];
            $order_id = "order_".time().mt_rand(100,999);
            $currency = $data['currency'];
            $customer_id = "Reference_id_".$order_id;
            $redirectUrl=base_url()."gateway/cashfree/success?order_id={order_id}&order_token={order_token}";

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
                $data['amount'] = $payment_details['post_amount'];
                $data['api_error']=array();
                $data['api_error']=$json->message;
              
                $this->load->view('payment/cashfree/index', $data);
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
            $payment_details = $this->session->userdata('params');
            $transactionid                      = $_GET['order_id'];

            $save_record = array(
                'case_reference_id' => $payment_details["case_reference_id"],
                'type' => "payment",
                'amount'        => $payment_details['post_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Cashfree TXN ID: " . $transactionid,
                'patient_id'  => $payment_details['patient_id'],
            );
            if($payment_details['type'] == "opd"){
                $save_record["opd_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "ipd"){
                $save_record["ipd_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "pharmacy"){
                $save_record["pharmacy_bill_basic_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "pathology"){
                $save_record["pathology_billing_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "radiology"){
                $save_record["radiology_billing_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "blood_bank"){
                $save_record["blood_issue_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "ambulance"){
                $save_record["ambulance_call_id"] = $payment_details['id'];
            }
            $insert_id = $this->webservice_model->insertOnlinePaymentInTransactions($save_record);
            redirect(base_url("payment/successinvoice/"));
        } else {
            redirect(site_url('payment/paymentfailed'));
        }

    }
}