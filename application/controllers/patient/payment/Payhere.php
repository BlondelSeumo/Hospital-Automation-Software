<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payhere extends Patient_Controller
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
        $this->load->model("gateway_ins_model");
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
            $this->load->view("patient/payment/payhere/index", $data);
            $this->load->view("layout/patient/footer");
        }
    }

    public function pay()
    {
        $setting             = $this->setting[0];
            $patient_detail =$this->session->userdata('patient');

            if ($this->session->has_userdata('payment_data')) {
                $id                          = $this->patient_data['patient_id'];
                $insta_apikey    = $this->pay_method->api_secret_key;
                $insta_authtoken = $this->pay_method->api_publishable_key;
                $payment_data =$session_data=  $this->session->userdata('payment_data');
                $data['amount']              = $payment_data['deposit_amount'];
            }
           
        $amount =$data['amount']; 
        $htmlform=array(
            'merchant_id'=>$this->pay_method->api_publishable_key,
            'return_url'=>base_url().'user/gateway/payhere/success',
            'cancel_url'=>base_url().'user/gateway/payhere/cancel',
            'notify_url'=>base_url().'gateway_ins/payhere',
            'order_id'=>time().rand(99,999),
            'items'=>'Student Fees',
            'currency'=>$setting['currency'],
            'amount'=>$amount,
            'first_name'=>$this->patient_data['name'],
            'last_name'=>'',
            'email'=>"",
            'phone'=>"",
            'address'=>'',
            'city'=>'',
            'country'=>''
        );

        $data['htmlform']=$htmlform;
        $data['params']['transaction_id']=$htmlform['order_id'];
         $ins_data=array(
            'unique_id'=>$htmlform['order_id'],
            'parameter_details'=>json_encode($htmlform),
            'gateway_name'=>'skrill',
            'type'=>'patient_bill',
            'online_appointment_id'=>null,
            'module_type'=>'patient_bill',
            'payment_status'=>'processing',
            );
            
            $transactionid = $htmlform['order_id'];

            $save_record = array(
                'case_reference_id' => $session_data["case_reference_id"],
                'type' => "payment",
                'amount'  => $session_data['deposit_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Skrill TXN ID: " . $transactionid,
                'patient_id'   => $this->patient_data['patient_id'],
            );

            if($session_data['payment_for'] == "opd"){
                    $save_record["opd_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "ipd"){
                $save_record["ipd_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "pharmacy"){
                $save_record["pharmacy_bill_basic_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "pathology"){
                $save_record["pathology_billing_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "radiology"){
                $save_record["radiology_billing_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "blood_bank"){
                $save_record["blood_donor_cycle_id"] = $session_data["donor_cycle_id"];
                $save_record["blood_issue_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "ambulance"){
                $save_record["ambulance_call_id"] = $session_data['id'];
            }

            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
            $save_record["gateway_ins_id"] = $gateway_ins_id;

            $this->gateway_ins_model->add_transactions_processing($save_record);
             $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/payhere/pay", $data);
            $this->load->view("layout/patient/footer");
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