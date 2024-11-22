<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mollie extends Admin_Controller
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
        $data['error']=array();
        $data['session_params']=$payment_details;
        $data['amount']=$payment_details['total'];
        
        $this->load->view('payment/mollie/index', $data);
    }

    public function pay()
    {
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['error']=array();
        $data['session_params']=$payment_details;
        $result = array();
        $data['currency']            = $setting['currency'];

        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
     
        if ($this->form_validation->run()==false) {
            $data['amount']=$payment_details['total'];
            $this->load->view('payment/mollie/index', $data);
        }else{
            
            $payment_details['post_amount']=$this->input->post('amount');
            $this->session->set_userdata("params", $payment_details);

            $amount = number_format((float)($payment_details['post_amount']), 2, '.', '');
            $api =' '.$this->pay_method->api_publishable_key;
            $order = time();
            
            $currency = $data['currency'];
            $redirectUrl = base_url()."gateway/mollie/complete";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "amount[currency]=".$currency."&amount[value]=".$amount."&description=#".$order."&redirectUrl=".$redirectUrl);

            $headers = array();
            $headers[] = 'Authorization: Bearer'.$api;
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $json = json_decode($result, true);

            if ($json['status']=='open') {
                $url = $json['_links']['checkout']['href'];
                $this->session->set_userdata("mollie_payment_id", $json['id']);
                header("Location: $url");
            }else {

            $data = array();
            $json = json_decode($result, true);
            $error = array();
            $data['api_error']=array();

            $payment_details = $this->session->userdata('params');
            $data['session_params']=$payment_details;
            $data['amount'] = $payment_details['post_amount'];
            $data['error']=$json['detail'];
          
            $this->load->view('payment/mollie/index', $data);
        }
        }
    }

    /**
     * This is a callback function for movies payment completion
     */
    public function complete()
    {
        $api=' '.$this->pay_method->api_publishable_key;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments/'.$this->session->userdata('mollie_payment_id'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer'.$api;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $json=json_decode($result);
        if ($json->status=='paid') {

            $payment_data = $this->session->userdata('params');
            $transactionid                      = $json->id; 
 
            $save_record = array(
                'case_reference_id' => $payment_data["case_reference_id"],
                'type' => "payment",
                'amount'        => $payment_data['post_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Mollie TXN ID: " . $transactionid,
                'patient_id'  => $payment_data['patient_id'],
            );
            if($payment_data['type'] == "opd"){
                $save_record["opd_id"] = $payment_data['id'];
            }elseif($payment_data['type'] == "ipd"){
                $save_record["ipd_id"] = $payment_data['id'];
            }elseif($payment_data['type'] == "pharmacy"){
                $save_record["pharmacy_bill_basic_id"] = $payment_data['id'];
            }elseif($payment_data['type'] == "pathology"){
                $save_record["pathology_billing_id"] = $payment_data['id'];
            }elseif($payment_data['type'] == "radiology"){
                $save_record["radiology_billing_id"] = $payment_data['id'];
            }elseif($payment_data['type'] == "blood_bank"){
                $save_record["blood_issue_id"] = $payment_data['id'];
            }elseif($payment_data['type'] == "ambulance"){
                $save_record["ambulance_call_id"] = $payment_data['id'];
            }
            $insert_id = $this->webservice_model->insertOnlinePaymentInTransactions($save_record);
            redirect(base_url("payment/successinvoice/"));

        } else {
           redirect(base_url('payment/paymentfailed'));
        }
    }
}