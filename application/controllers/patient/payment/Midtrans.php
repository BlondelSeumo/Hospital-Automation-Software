<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Midtrans extends Patient_Controller
{
    public $api_config     = "";
    public $payment_method = array();
    public $pay_method     = array();

    public function __construct()
    {
        parent::__construct();
        $this->pay_method   = $this->paymentsetting_model->getActiveMethod();
        $this->patient_data = $this->session->userdata('patient');
        $this->setting      = $this->setting_model->get();
        $this->load->library('Midtrans_lib');
    }

    public function index()
    {
        $setting             = $this->setting[0];
        $data                = array();
        $id                  = $this->patient_data['patient_id'];
        $data["id"]          = $id;
        $data['productinfo'] = $this->lang->line('online_payment');
        if ($this->session->has_userdata('payment_data')) {
            $payment_data                  = $this->session->userdata('payment_data');
            $api_secret_key          = ($this->pay_method->api_secret_key);
            $data['api_secret_key']  = $api_secret_key;
            $data['case_reference_id']   = $payment_data['case_reference_id'];
            $data['amount']          = $payment_data['deposit_amount'];
            $data['currency']        = $setting['currency'];
            $data['hospital_name']   = $setting['name'];
            $data['image']           = $setting['image'];
        }

        $data['setting']   = $this->setting;
        $data['api_error'] = array();
        $enable_payments = array('credit_card');
        $transaction       = array(
                'enabled_payments' => $enable_payments,
                'transaction_details' => array(
                'order_id'     => time(),
                'gross_amount' => round($data['amount']), // no decimal allowed
            ),
        );

        $snapToken          = $this->midtrans_lib->getSnapToken($transaction, $api_secret_key);
        $data['snap_Token'] = $snapToken;
        $this->load->view("layout/patient/header");
        $this->load->view('patient/payment/midtrans/midtrans', $data);
        $this->load->view("layout/patient/footer");
    }

    public function success()
    { 
        $response   = json_decode($_POST['result_data']);
        $payment_id = $response->transaction_id;
        $transactionid = $payment_id;
        $payment_data = $this->session->userdata('payment_data');
                
        $save_record = array(
            'case_reference_id' => $payment_data["case_reference_id"],
            'type' => "payment",
            'amount'  => $payment_data['deposit_amount'],
            'payment_mode' => 'Online',
            'payment_date' => date('Y-m-d H:i:s'),
            'note'         => "Online fees deposit through Midtrans TXN ID: " . $transactionid,
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
        $array = array('insert_id' => $insert_id);
        echo json_encode($array);
    }
}
