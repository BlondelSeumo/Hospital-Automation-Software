<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Paypal extends Patient_Controller
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
        $this->load->library('paypal_payment');
        $this->load->model("patient_model");
        $this->patient_data   = $this->session->userdata('patient');
        $this->payment_method = $this->paymentsetting_model->get();
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->setting        = $this->setting_model->get();
    }

    public function index()
    {

        if ($this->session->has_userdata('payment_data')) {
            $setting             = $this->setting[0];
            $payment_data                      = $this->session->userdata('payment_data');
            $api_publishable_key         = ($this->pay_method->api_publishable_key);
            $api_secret_key              = ($this->pay_method->api_secret_key);
            $data['api_publishable_key'] = $api_publishable_key;
            $data['api_secret_key']      = $api_secret_key;
            $data['case_reference_id']   = $payment_data['case_reference_id'];
            $data['amount']              = $payment_data['deposit_amount'];
            $data["payment_type"]        = $payment_data['payment_for'];
            $data['currency']            = $setting['currency'];
            $data['hospital_name']       = $setting['name'];
            $data['image']               = $setting['image'];
           
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/paypal/paypal", $data);
            $this->load->view("layout/patient/footer");
        }
    }

    public function opdpay()
    {
        if ($this->session->has_userdata('payment_data')) {
            $setting                 = $this->setting[0];
            $data                    = array();
            $id                      = $this->patient_data['patient_id'];
            $amount                  = $this->session->userdata('payment_data');
            $record_id                   = $amount['record_id'];
            $charges                 = $this->charge_model->getOPDCharges($id, $record_id);
            $data["charges"]         = $charges;
            $paymentDetails          = $this->payment_model->opdpaymentDetails($id);
            $paid_amount             = $this->payment_model->getOPDPaidTotal($id, $record_id);
            $data["paid_amount"]     = $paid_amount["paid_amount"];
            $balance_amount          = $this->payment_model->getOPDBalanceTotal($id);
            $data["balance_amount"]  = $balance_amount["balance_amount"];
            $data["payment_details"] = $paymentDetails;
            $data['amount']          = $amount['deposit_amount'];
            $data["payment_type"]    = 'opd';
            $data["id"]              = $id;
            $result                  = $this->patient_model->getDetails($record_id);
            $data['patient']         = $result;
            $data['currency']        = $setting['currency'];
            $data['hospital_name']   = $setting['name'];
            $data['image']           = $setting['image'];
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/paypal/paypal", $data);
            $this->load->view("layout/patient/footer");
        }
    }

    public function checkout()
    {
       
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            if ($this->session->has_userdata('payment_data')) {
                $setting               = $this->setting[0];
                $id                    = $this->patient_data['patient_id'];
                $data["id"]            = $id;

                $params                = $this->session->userdata('payment_data');
              
                $data                  = array();
                $data['total']         = $params['deposit_amount'];
                $data['productinfo']   = $this->lang->line('online_payment');
                $data['symbol']        = $setting['currency_symbol'];
                $data['currency_name'] = $setting['currency'];
                $data['name']          = $this->patient_data['name'];
                $data['patient_id']    = $this->patient_data['patient_id'];
                $data['ipd_id']        = "";
                $data['phone']         = $this->patient_data['mobileno'];
                $response              = $this->paypal_payment->payment($data);
                if ($response->isSuccessful()) {

                } elseif ($response->isRedirect()) {
                    $response->redirect();
                } else {
                    $setting             = $this->setting[0];
                    $payment_data                      = $this->session->userdata('payment_data');
                    $api_publishable_key         = ($this->pay_method->api_publishable_key);
                    $api_secret_key              = ($this->pay_method->api_secret_key);
                    $data['api_publishable_key'] = $api_publishable_key;
                    $data['api_secret_key']      = $api_secret_key;
                    $data['case_reference_id']   = $payment_data['case_reference_id'];
                    $data['amount']              = $payment_data['deposit_amount'];
                    $data["payment_type"]        = $payment_data['payment_for'];
                    $data['currency']            = $setting['currency'];
                    $data['hospital_name']       = $setting['name'];
                    $data['image']               = $setting['image'];
                    $data['api_error'] = $response->getMessage();
                    $this->load->view("layout/patient/header");
                    $this->load->view("patient/payment/paypal/paypal", $data);
                    $this->load->view("layout/patient/footer");
                }
            }
        }
    }

    public function getsuccesspayment()
    {
        $data                   = array();
        $setting                = $this->setting[0];
        $id                     = $this->patient_data['patient_id'];
        $result                 = $this->patient_model->getIpdDetails($id);
        $params                 = $this->session->userdata('payment_data');
        $data['total']          = $params['deposit_amount'];
        $data['symbol']         = $setting['currency_symbol'];
        $data['currency_name']  = $setting['currency'];
        $data['name']           = $result['patient_name'];
        $data['guardian_phone'] = $result['mobileno'];
        $data['productinfo']    = $this->lang->line('online_payment');
        $response               = $this->paypal_payment->success($data);
        $type                   = $this->input->post('payment_type');
        $paypalResponse         = $response->getData();
        if ($response->isSuccessful()) { 
            $purchaseId = $_GET['PayerID'];
            if (isset($paypalResponse['PAYMENTINFO_0_ACK']) && $paypalResponse['PAYMENTINFO_0_ACK'] === 'Success') {
                if ($purchaseId) {
                    $params = $this->session->userdata('payment_data');
                    $ref_id = $paypalResponse['PAYMENTINFO_0_TRANSACTIONID'];
            if ($this->session->has_userdata('payment_data')) {
                $payment_data = $this->session->has_userdata('payment_data');
                $data['amount'] = $payment_data['deposit_amount'];
            }
            $transactionid = $ref_id;

            $payment_data = $this->session->userdata('payment_data');
                
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $payment_data['deposit_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Paypal TXN ID: " . $transactionid,
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
                }
            }
        } elseif ($response->isRedirect()) {
            $response->redirect();
        } else {
            redirect(base_url("patient/pay/paymentfailed"));
        }
    }
}
