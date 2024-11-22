<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payu extends Patient_Controller
{

    public $payment_method = array();
    public $pay_method     = array();
    public $patient_data;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('Customlib');
        $this->patient_data   = $this->session->userdata('patient');
        $this->payment_method = $this->paymentsetting_model->get();
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
    }

    public function index()
    {
        $posted               = array();
        $data                 = array();
        $id                   = $this->patient_data['patient_id'];
        $data["id"]           = $id;
        $data['productinfo'] = $this->lang->line('online_payment');
        if ($this->session->has_userdata('payment_data')) {
            $payment_data                  = $this->session->userdata('payment_data');
            $amount = $data['amount']          = $payment_data['deposit_amount'];
            $data['MERCHANT_KEY']    = $this->pay_method->api_secret_key;
            $SALT                    = $this->pay_method->salt;    
            $txnid                      = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
            $payment_data["txn_id"] = $txnid;
            $this->session->set_userdata("payment_data",$payment_data);
            //payumoney details
            $customer_name    = $this->patient_data["name"];
            $customer_emial   = "";
            $customer_mobile  = "";
            $customer_address  = "";
            $product_info = 'Online Fees Payment';
            $MERCHANT_KEY = $this->pay_method->api_secret_key;
            $SALT         = $this->pay_method->salt;

            //optional udf values
            $udf1 = '';
            $udf2 = '';
            $udf3 = '';
            $udf4 = '';
            $udf5 = '';

            $hashstring = $MERCHANT_KEY . '|' . $txnid . '|' . $amount . '|' . $product_info . '|' . $customer_name . '|' . $customer_emial . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $SALT;
            $hash       = strtolower(hash('sha512', $hashstring));

            $success = base_url('patient/payment/payu/success');
            $fail    = base_url('patient/payment/payu/success');
            $cancel  = base_url('patient/payment/payu/success');
            $data    = array(
                'mkey'                      => $MERCHANT_KEY,
                'tid'                       => $txnid,
                'hash'                      => $hash,
                'amount'                    => $amount,
                'name'                      => $customer_name,
                'productinfo'               => $product_info,
                'action'                    => "https://secure.payu.in/_payment", //for live change action  https://secure.payu.in
                'sucess'                    => $success,
                'failure'                   => $fail,
                'cancel'                    => $cancel,
            );
            $data['case_reference_id']   = $payment_data['case_reference_id'];

            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/payu/payu", $data);
            $this->load->view("layout/patient/footer");
        }
    } 

    public function checkout()
    {

        $this->form_validation->set_rules('firstname', $this->lang->line('customer_name'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'firstname' => form_error('firstname'),
                'phone'     => form_error('phone'),
                'email'     => form_error('email'),
                'amount'    => form_error('amount'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            $array = array('status' => 'success', 'error' => '');
            echo json_encode($array);
        }
    }
    
    public function success()
    {
      
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $payment_data = $this->session->userdata('payment_data');

            if ($this->input->post('status') == "success") {
                $mihpayid      = $this->input->post('mihpayid');
                $transactionid = $this->input->post('txnid');
                $txn_id        = $payment_data['txn_id'];
               
                if ($txn_id == $transactionid) {
                    $save_record = array(
                        'case_reference_id' => $payment_data["case_reference_id"],
                        'type' => "payment",
                        'amount'  => $payment_data['deposit_amount'],
                        'payment_mode' => 'Online',
                        'payment_date' => date('Y-m-d H:i:s'),
                        'note'         => "Online fees deposit through Payu TXN ID: " . $txn_id,
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
                        $save_record["blood_issue_id"] = $payment_data['id'];
                    }elseif($payment_data['payment_for'] == "ambulance"){
                        $save_record["ambulance_call_id"] = $payment_data['id'];
                    }
                    $insert_id = $this->payment_model->insertOnlinePaymentInTransactions($save_record);


                    redirect(base_url("patient/pay/successinvoice/"));
                } else {
                    redirect(base_url('patient/pay/paymentfailed'));
                }
            } else {
                redirect(base_url('patient/pay/paymentfailed'));
            }
        }
    }
}
