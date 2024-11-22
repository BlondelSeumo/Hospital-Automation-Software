<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Jazzcash extends Patient_Controller
{

    public $payment_method = array();
    public $pay_method     = array();
    public $user_data;
    public $setting;
 
    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('Customlib');
        $this->load->model("patient_model");
        $this->patient_data   = $this->session->userdata('patient');
        $this->payment_method = $this->paymentsetting_model->get();
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->setting        = $this->setting_model->get();
        date_default_timezone_set("Asia/Karachi");
    }

    public function index()
    {
        $setting             = $this->setting[0];
        $data                = array();
        $id                  = $this->patient_data['patient_id'];
        $data["id"]          = $id;
        $data['productinfo'] = $this->lang->line('online_payment'); 
        if ($this->session->has_userdata('payment_data')) {
            $payment_data =  $this->session->userdata("payment_data");
            $data['amount']              = $payment_data['deposit_amount'];
            $data["payment_type"]        = 'ipd';
            $data['currency']            = $setting['currency'];
            $data['case_reference_id']   = $payment_data['case_reference_id'];
            $data['hospital_name']       = $setting['name'];
            $data['image']               = $setting['image'];
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/jazzcash/jazzcash", $data);
            $this->load->view("layout/patient/footer");
        }
    }

    public function pay()
    {
        $payment_data                                = $this->session->userdata('payment_data');
        $data['total']                         = $payment_data['deposit_amount'];
        $data['pp_MerchantID']                 = $this->pay_method->api_secret_key;
        $data['pp_Password']                   = $this->pay_method->api_password;
        $data['currency_code']                 = $this->setting[0]['currency'];
        $data['ExpiryTime']                    = date('YmdHis', strtotime("+3 hours"));
        $data['TxnDateTime']                   = date('YmdHis', strtotime("+0 hours"));
        $data['TxnRefNumber']                  = "T" . date('YmdHis');
        $input_para["pp_Version"]              = "2.0";
        $input_para["pp_IsRegisteredCustomer"] = "Yes";
        $input_para["pp_TxnType"]              = "MPAY";
        $input_para["pp_TokenizedCardNumber"]  = "";
        $input_para["pp_CustomerID"]           = time();
        $input_para["pp_CustomerEmail"]        = '';
        $input_para["pp_CustomerMobile"]       = "";
        $input_para["pp_MerchantID"]           = $data['pp_MerchantID'];
        $input_para["pp_Language"]             = "EN";
        $input_para["pp_SubMerchantID"]        = "";
        $input_para["pp_Password"]             = $data['pp_Password'];
        $input_para["pp_TxnRefNo"]             = $data['TxnRefNumber'];
        $input_para["pp_Amount"]               = $data['total'] * 100;
        $input_para["pp_DiscountedAmount"]     = "";
        $input_para["pp_DiscountBank"]         = "";
        $input_para["pp_TxnCurrency"]          = "PKR";
        $input_para["pp_TxnDateTime"]          = $data['TxnDateTime'];
        $input_para["pp_TxnExpiryDateTime"]    = $data['ExpiryTime'];
        $input_para["pp_BillReference"]        = time();
        $input_para["pp_Description"]          = "bill payment smart hospital";
        $input_para["pp_ReturnURL"]            = base_url() . 'patient/payment/jazzcash/complete';
        $input_para["pp_SecureHash"]           = "0123456789";
        $input_para["ppmpf_1"]                 = "1";
        $input_para["ppmpf_2"]                 = "2";
        $input_para["ppmpf_3"]                 = "3";
        $input_para["ppmpf_4"]                 = "4";
        $input_para["ppmpf_5"]                 = "5";
        $data['payment_data']                  = $input_para;
        $this->load->view("patient/payment/jazzcash/jazzcashPay", $data);
    }

    public function complete()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            if ($_POST['pp_ResponseCode'] == '000') {
                $txn_id = $_POST['pp_TxnRefNo'];
                $payment_data = $this->session->userdata('payment_data');
                
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $payment_data['deposit_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Jazzcash TXN ID: " . $txn_id,
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
				$setting             = $this->setting[0];
				$data                = array();
				$id                  = $this->patient_data['patient_id'];
				$data["id"]          = $id;
				$data['productinfo'] = $this->lang->line('online_payment');
				if ($this->session->has_userdata('payment_data')) {
					$payment_data =  $this->session->userdata("payment_data");
					$data['amount']              = $payment_data['deposit_amount'];
					$data["payment_type"]        = 'ipd';
					$data['currency']            = $setting['currency'];
					$data['case_reference_id']   = $payment_data['case_reference_id'];
					$data['hospital_name']       = $setting['name'];
					$data['image']               = $setting['image'];
					$data['api_error'] 			 = $_POST['pp_ResponseMessage'];
					$this->load->view("layout/patient/header");
					$this->load->view("patient/payment/jazzcash/jazzcash", $data);
					$this->load->view("layout/patient/footer");
				}
            }
        }
    }

}
