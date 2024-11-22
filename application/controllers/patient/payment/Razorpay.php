<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Razorpay extends Patient_Controller
{
    public $payment_method = array();
    public $pay_method     = array();
    public $patient_data;
    public $setting;

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
        $this->setting        = $this->setting_model->get();
    }

    public function index()
    {
        $setting             = $this->setting[0];
        $data                = array();
        $id                  = $this->patient_data['patient_id'];
        $data["id"]          = $id;
        $data['productinfo'] = $this->lang->line('online_payment');
        if ($this->session->has_userdata('payment_data')) {
            $payment_data = $this->session->userdata("payment_data");

            $data['total'] = $payment_data['deposit_amount'] * 100;
            $data['key_id'] = $this->pay_method->api_publishable_key;
            $data['merchant_order_id'] = time() . "01";
            $data['txnid'] = time() . "02";
            $data['name'] = $this->patient_data['name'];
            $data['title'] =  $data['productinfo'];
            $data['case_reference_id']   = $payment_data['case_reference_id'];
            $data['amount']              = $payment_data['deposit_amount'];
            $data['currency']            = $setting['currency'];
            $data['hospital_name']       = $setting['name'];
            $data['image']               = $setting['image'];
            $data['return_url'] = base_url("patient/payment/razorpay/callback");
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/razorpay/razorpay", $data);
            $this->load->view("layout/patient/footer");
        }
    }

    public function callback()
    {
        $transactionid = $_POST['razorpay_payment_id'];

        $payment_data = $this->session->userdata('payment_data');
                
        $save_record = array(
            'case_reference_id' => $payment_data["case_reference_id"],
            'type' => "payment",
            'amount'  => $payment_data['deposit_amount'],
            'payment_mode' => 'Online',
            'payment_date' => date('Y-m-d H:i:s'),
            'note'         => "Online fees deposit through Razorpay TXN ID: " . $transactionid,
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
