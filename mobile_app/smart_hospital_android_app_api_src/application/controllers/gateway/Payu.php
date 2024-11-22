<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Payu extends Admin_Controller
{
     var $setting;
     var $payment_method;
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('auth_model',  'setting_model', 'user_model', 'webservice_model','module_model','paymentsetting_model'));
        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();

    }

    public function index(){
        $pay_method     = $this->paymentsetting_model->getActiveMethod();
        if ($pay_method->payment_type == "payu") { 
            if ($this->session->has_userdata('params')) {
                if ($pay_method->api_secret_key != "" && $pay_method->salt != "") {    
                    $data                   = array();
                    $session_params         = $this->session->userdata('params');
                    $data['session_params'] = $session_params;
                    // Merchant key here as provided by Payu
                    $data['MERCHANT_KEY'] = $session_params['key'];
                    // Merchant Salt as provided by Payu
                    $SALT = $pay_method->salt;
                    // End point - change to https://secure.payu.in for LIVE mode
                    $PAYU_BASE_URL  = "https://secure.payu.in";
                    $data['action'] = '';
                    $data['surl']   = base_url('gateway/payu/success');
                    $data['furl']   = base_url('gateway/payu/success');
                    $data['productinfo'] = "Online Payment For Smart Hospital";
                    $posted = array();
                    if (!empty($_POST)) {
                        foreach ($_POST as $key => $value) {
                            $posted[$key] = $value;
                        }
                    }
                    //print_r($posted);die;
                    $data['posted']    = $posted;
                    $data['formError'] = 0;
                    if (empty($posted['txnid'])) {
                        // Generate random transaction id
                        $data['txnid'] = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
                    } else {
                        $data['txnid'] = $posted['txnid'];
                    }
                    $session_params['txn_id'] = $data['txnid'];
                    $this->session->set_userdata("params", $session_params);
                    $data['hash'] = '';
                    $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
                    if (empty($posted['hash']) && sizeof($posted) > 0) {
                        if (
                            empty($posted['key'])
                            || empty($posted['txnid'])
                            || empty($posted['amount'])
                            || empty($posted['firstname'])
                            || empty($posted['email'])
                            || empty($posted['phone'])
                            
                            || empty($posted['surl'])
                            || empty($posted['furl'])
                            || empty($posted['service_provider'])
                        ) {
                            $formError = 1;
                        } else {
                            $hashVarsSeq = explode('|', $hashSequence);
                            $hash_string = '';
                            foreach ($hashVarsSeq as $hash_var) {
                                $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
                                $hash_string .= '|';
                            }
                            $hash_string .= $SALT;
                            $data['hash']   = strtolower(hash('sha512', $hash_string));
                            $data['action'] = $PAYU_BASE_URL . '/_payment';
                        }
                    } elseif (!empty($posted['hash'])) {
                        $data['hash']   = $posted['hash'];
                        $data['action'] = $PAYU_BASE_URL . '/_payment';
                    }
                    $this->load->view('payment/payu/index', $data);
                }
            }
        }else{
            $this->session->set_flashdata('error', 'Oops! Something went wrong');
            $this->load->view('payment/error');
        }
    } 

  

    public function success(){
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $payment_data = $this->session->userdata('params');
            $mihpayid      = $this->input->post('mihpayid');
            $transactionid = $this->input->post('txnid');
            $txn_id        = $payment_data['txn_id'];
            if ($txn_id == $transactionid) {
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'        => $this->input->post('amount'),
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through PayU TXN ID: " . $transactionid,
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
                redirect(base_url("payment/successinvoice/" . $insert_id));
            }else {

                redirect('payment/paymentfailed', 'refresh');
            }
        }
    }

}
