<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pay extends Patient_Controller {

    public $payment_method = array();
    public $pay_method = array();
    public $patient_data;

    public function __construct() {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('Customlib');
        $this->patient_data = $this->session->userdata('patient');
        $this->payment_method = $this->paymentsetting_model->get();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode = $this->config->item('payment_mode');
        $this->blood_group = $this->config->item('bloodgroup');
        $this->load->model("transaction_model");
        $this->payment_tables = array(
            "opd"        => "opd_details",
            "ipd"        => "ipd_details",
            "pharmacy"   => "pharmacy_bill_basic",
            "pathology"  => "pathology_billing",
            "radiology"  => "radiology_billing",
            "ambulance"  => "ambulance_call",
            "blood_bank" => "blood_issue",
        );
    }
 
    public function checkvalidate(){

        
        $this->form_validation->set_rules(
                'deposit_amount', $this->lang->line('amount'), array('trim', 'required', 'xss_clean','valid_amount',
                    array('check_exists', array($this->payment_model, 'validate_paymentamount')),
                )
            );

        if ($this->form_validation->run() == false) {
            $msg = array(
                'deposit_amount' => form_error('deposit_amount'),
            );
            $array = array('status' => 'fail', 'error' => $msg);
        } else {
                $payment_data["payment_for"] = $this->input->post("payment_for");
                $payment_data["deposit_amount"] = $this->input->post("deposit_amount");
                $payment_data["id"] = $this->input->post("id");
                $reference = $this->payment_model->getCaseReferenceId($payment_data["id"],$this->payment_tables[$payment_data["payment_for"]]);
                $payment_data["case_reference_id"] = $reference["case_reference_id"];
                if($this->input->post("payment_for") == "blood_bank"){
                $payment_data["donor_cycle_id"] = $this->input->post("donor_cycle_id");
                }
                $this->session->set_userdata('payment_data', $payment_data);
            $array = array('status' => 'success', 'error' => '');
        }
         echo json_encode($array);
    }

    public function index(){
       
        if(!empty($this->pay_method)){

            if ($this->session->has_userdata('payment_data')) {
                if ($this->pay_method->payment_type == "billplz") {
                    redirect(base_url("patient/payment/billplz"));
                }elseif($this->pay_method->payment_type == "ccavenue"){
                    redirect(base_url("patient/payment/ccavenue"));
                }elseif($this->pay_method->payment_type == "flutterwave"){
                    redirect(base_url("patient/payment/flutterwave"));
                }elseif($this->pay_method->payment_type == "instamojo"){
                    redirect(base_url("patient/payment/instamojo"));
                }elseif($this->pay_method->payment_type == "ipayafrica"){
                    redirect(base_url("patient/payment/ipayafrica"));
                }elseif($this->pay_method->payment_type == "jazzcash"){
                    redirect(base_url("patient/payment/jazzcash"));
                }elseif($this->pay_method->payment_type == "midtrans"){
                    redirect(base_url("patient/payment/midtrans"));
                }elseif($this->pay_method->payment_type == "paypal"){
                    redirect(base_url("patient/payment/paypal"));
                }elseif($this->pay_method->payment_type == "paytm"){
                    redirect(base_url("patient/payment/paytm"));
                }elseif($this->pay_method->payment_type == "paystack"){
                    redirect(base_url("patient/payment/paystack"));
                }elseif($this->pay_method->payment_type == "payu"){
                    redirect(base_url("patient/payment/payu"));
                }elseif($this->pay_method->payment_type == "pesapal"){
                    redirect(base_url("patient/payment/pesapal"));
                }elseif($this->pay_method->payment_type == "razorpay"){
                    redirect(base_url("patient/payment/razorpay"));
                }elseif($this->pay_method->payment_type == "sslcommerz"){
                    redirect(base_url("patient/payment/sslcommerz"));
                }elseif($this->pay_method->payment_type == "stripe"){
                    redirect(base_url("patient/payment/stripe"));
                }elseif($this->pay_method->payment_type == "walkingm"){
                    redirect(base_url("patient/payment/walkingm"));
                }elseif($this->pay_method->payment_type == "mollie"){
                    redirect(base_url("patient/payment/mollie"));
                }elseif($this->pay_method->payment_type == "cashfree"){
                    redirect(base_url("patient/payment/cashfree"));
                }elseif($this->pay_method->payment_type == "payfast"){
                    redirect(base_url("patient/payment/payfast"));
                }elseif($this->pay_method->payment_type == "toyyibpay"){
                    redirect(base_url("patient/payment/toyyibpay"));
                }elseif($this->pay_method->payment_type == "twocheckout"){
                    redirect(base_url("patient/payment/twocheckout"));
                }elseif($this->pay_method->payment_type == "skrill"){
                    redirect(base_url("patient/payment/skrill"));
                }elseif($this->pay_method->payment_type == "payhere"){
                    redirect(base_url("patient/payment/payhere"));
                }elseif($this->pay_method->payment_type == "onepay"){
                    redirect(base_url("patient/payment/onepay"));
                }
            }
        }else{
            redirect(base_url("patient/pay/nonepaymentgateway"));
        }
    }

    public function getPatientDetail($case_reference_id = null){
      
       $payment_for=$this->session->userdata('payment_data')['payment_for'];
        if($case_reference_id !== null && $case_reference_id !== '0'){
            $patient=$this->patient_model->getDetailsByCaseId($case_reference_id,$payment_for);
            
            $data['result']=$patient;
            $data['case_id']=$case_reference_id;
            if(!empty($patient)){
                $status=1;
            }else{
                $status=0;
            }
            
            $page = $this->load->view('patient/payment/_patient_details', $data, true);
            echo $page;
        }
        else{
            $patient = $this->patient_model->getpatientDetails($this->patient_data['patient_id']);

            $patient['patient_id'] = $this->patient_data['patient_id'];
            $data['result']=$patient;
            if(!empty($patient)){
                $status=1;
            }else{
                $status=0;
            }
            $page = $this->load->view('patient/payment/_patient_details', $data, true);
            echo $page;
        }
    }
 
    public function paymentfailed() {
        $data = array();
        $data['title'] = 'Invoice';
        $this->session->set_userdata('top_menu', 'dashboard');
        $setting_result = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $this->load->view("layout/patient/header");
        $this->load->view('patient/paymentfailed', $data);
        $this->load->view("layout/patient/footer");
    }

    public function successinvoice() {
        $data['title'] = 'Invoice';
        $setting_result = $this->setting_model->get();
        $data['settinglist'] = $setting_result;      
        $this->load->view('layout/patient/header', $data);
        $this->load->view('patient/invoice', $data);
        $this->load->view('layout/patient/footer', $data);
    }

    public function nonepaymentgateway() {
        $data['title'] = '';
        $setting_result = $this->setting_model->get();
        $data['settinglist'] = $setting_result;      
        $this->load->view('layout/patient/header', $data);
        $this->load->view('patient/nonePaymentgateway', $data);
        $this->load->view('layout/patient/footer', $data);
    }
}
?>