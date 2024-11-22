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
        $this->load->library('Enc_lib');
        $this->patient_data   = $this->session->userdata('patient');
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->setting        = $this->setting_model->get();
    }

    public function index() {

        $payment_data = $this->session->userdata('payment_data');
        $data['amount'] = $payment_data['deposit_amount'];
        $data['case_reference_id']   = $payment_data['case_reference_id'];
        $this->load->view("layout/patient/header");
        $this->load->view('patient/payment/cashfree/index', $data);
        $this->load->view("layout/patient/footer");
    } 
 
    
    public function pay()
    {
        $insta_apikey    = $this->pay_method->api_secret_key;
        $insta_authtoken = $this->pay_method->api_publishable_key;
        $payment_data = $this->session->userdata('payment_data');
        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
             $payment_data = $this->session->userdata('payment_data');
        $data['amount'] = $payment_data['deposit_amount'];
        $data['case_reference_id']   = $payment_data['case_reference_id'];
        $this->load->view("layout/patient/header");
        $this->load->view('patient/payment/cashfree/index', $data);
        $this->load->view("layout/patient/footer");
        }else{
        $amount =number_format((float)($payment_data['deposit_amount']), 2, '.', '');
        $customer_id="Reference_id_".$this->patient_data['patient_id'];
        $order_id="order_".time().mt_rand(100,999);
        $setting             = $this->setting[0];
        $currency=$setting['currency'];
   
        $redirectUrl=base_url()."patient/payment/cashfree/success?order_id={order_id}&order_token={order_token}";

        $my_array=array(
            "order_id"=>$order_id,
            "order_amount"=>$amount,
            "order_currency"=>$currency,
            "customer_details"=>array(
            "customer_id"=>$customer_id,
            "customer_name"=>$this->patient_data['name'],
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
            $data['amount'] = $payment_data['deposit_amount'];
            $data['error']=$json->message;
            
            $this->load->view("layout/patient/header");
            $this->load->view('patient/payment/cashfree/index', $data);
            $this->load->view("layout/patient/footer");
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
            $payment_data = $this->session->userdata('payment_data');
            $transactionid                      = $_GET['order_id'];

            $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $payment_data['deposit_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Cashfree TXN ID: " . $transactionid,
                    'patient_id'   => $this->patient_data['patient_id'],
                );
                if($payment_data['payment_for'] == "opd"){
                    $save_record["opd_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "ipd"){
                    $save_record["ipd_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "pharmacy"){
                    $save_record["pharmacy_bill_basic_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "pathology"){
                    $save_record["pathology_billing_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "radiology"){
                    $save_record["radiology_billing_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "blood_bank"){
                    $save_record["blood_donor_cycle_id"] = $payment_data["donor_cycle_id"];
                    $save_record["blood_issue_id"] = $payment_data['id'];
                }elseif($payment_data['payment_for'] == "ambulance"){
                    $save_record["ambulance_call_id"] = $payment_data['id'];
                }
                $insert_id = $this->payment_model->insertOnlinePaymentInTransactions($save_record);
             
            redirect(base_url("patient/pay/successinvoice/"));

        } else {

            redirect(base_url("patient/pay/paymentfailed/"));
        }

    }
}