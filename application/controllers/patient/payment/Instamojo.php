<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Instamojo extends Patient_Controller
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
            $this->load->view("patient/payment/instamojo/instamojo", $data);
            $this->load->view("layout/patient/footer");
        }
    }

    public function pay()
    {
            $patient_detail = $this->session->userdata('patient');
            if ($this->session->has_userdata('payment_data')) {
                $id                          = $this->patient_data['patient_id'];
                $insta_apikey    = $this->pay_method->api_secret_key;
                $insta_authtoken = $this->pay_method->api_publishable_key;
                $payment_data = $this->session->userdata('payment_data');
                $data['amount']              = $payment_data['deposit_amount'];
            }
            $ch              = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payment-requests/'); // for live https://www.instamojo.com/api/1.1/payment-requests/
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Api-Key:$insta_apikey",
                    "X-Auth-Token:$insta_authtoken"));
            $payload = array(
                'purpose'                 => 'Bill Payment',
                'amount'                  => $data['amount'],
                'phone'                   => "",
                'buyer_name'              => $patient_detail['name'],
                'redirect_url'            => base_url() . 'patient/payment/instamojo/success/',
                'send_email'              => false,
                'webhook'                 => base_url() . 'webhooks/insta_webhook',
                'send_sms'                => false,
                'email'                   => "",
                'allow_repeated_payments' => false,
            );

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            $response = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($response, true);
            if ($json['success']) {
                $url = $json['payment_request']['longurl'];
                header("Location: $url");
            } else {
				$setting             = $this->setting[0];
				$data                = array();
				$id                  = $this->patient_data['patient_id'];
				$data["id"]          = $id;
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
				$json = json_decode($response, true);
                $data['api_error'] = $json['message'];
				$this->load->view("layout/patient/header");
				$this->load->view("patient/payment/instamojo/instamojo", $data);
				$this->load->view("layout/patient/footer");
            }
    }


    public function success($payment_type = '')
    {
        if ($_GET['payment_status'] == 'Credit') {
            if ($this->session->has_userdata('payment_data')) {
                $payment_data = $this->session->has_userdata('payment_data');
                $data['amount'] = $payment_data['deposit_amount'];
            }
            $transactionid = $_GET['payment_id'];

            $payment_data = $this->session->userdata('payment_data');
                
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $payment_data['deposit_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Instamojo TXN ID: " . $transactionid,
                    'patient_id'  => $this->patient_data['patient_id'],
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
            redirect(base_url("patient/pay/paymentfailed"));
        }
    } 
}