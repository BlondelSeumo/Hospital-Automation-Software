<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Toyyibpay extends Patient_Controller {

    public $api_config = "";

    public function __construct() {
        parent::__construct();
        $this->patient_data   = $this->session->userdata('patient');
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->setting        = $this->setting_model->get();
        $this->load->model(array('gateway_ins_model'));
    }
  
    public function index() {
        $data = array();
        $payment_data = $this->session->userdata('payment_data');
        $data['amount'] = $payment_data['deposit_amount'];
        $data['case_reference_id']   = $payment_data['case_reference_id'];
        $data['error']=array();
        $this->load->view("layout/patient/header");
        $this->load->view('patient/payment/toyyibpay/index', $data);
        $this->load->view("layout/patient/footer");
    }
 
    public function pay(){
        $session_data = $this->session->userdata('payment_data');
        $data['setting'] = $this->setting[0];
        $data['api_error'] = array();
        $result = array();
        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $data['amount'] =number_format((float)($session_data['deposit_amount']), 2, '.', '');
            $data['case_reference_id']   = $session_data['case_reference_id'];

            $this->load->view("layout/patient/header");
            $this->load->view('patient/payment/toyyibpay/index', $data);
            $this->load->view("layout/patient/footer");
        } else {

            $data['name'] = $this->patient_data['name'];
            
            $amount =number_format((float)($session_data['deposit_amount']), 2, '.', ''); 
            $payment_data = array(
                'userSecretKey'=>$this->pay_method->api_secret_key,
                'categoryCode'=>$this->pay_method->api_signature,
                'billName'=>'Patient Bill',
                'billDescription'=>'Patient Bill',
                'billPriceSetting'=>1,
                'billPayorInfo'=>1,
                'billAmount'=>$session_data['deposit_amount'],
                'billReturnUrl'=>base_url().'patient/payment/toyyibpay/success',
                'billCallbackUrl'=>base_url().'patient/gateway_ins/toyyibpay',
                'billExternalReferenceNo' => time().rand(99,999),
                'billTo'=>$data['name'],
                'billEmail'=>$_POST['email'],
                'billPhone'=>$_POST['phone'],
                'billSplitPayment'=>0,
                'billSplitPaymentArgs'=>'',
                'billPaymentChannel'=>'0',
                'billContentEmail'=>'Thank you for fees submission!',
                'billChargeToCustomer'=>1
              );  

              $curl = curl_init();
              curl_setopt($curl, CURLOPT_POST, 1);
              curl_setopt($curl, CURLOPT_URL, 'https://dev.toyyibpay.com/index.php/api/createBill');  
              curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($curl, CURLOPT_POSTFIELDS, $payment_data);

              $result = curl_exec($curl);
              $info = curl_getinfo($curl);  
              curl_close($curl);
              $obj = json_decode($result);

            if (!empty($obj)) {
                $ins_data=array(
                    'unique_id'=>$payment_data['billExternalReferenceNo'],
                    'parameter_details'=>json_encode($payment_data),
                    'gateway_name'=>'toyyibpay',
                    'type'=>'patient_bill',
                    'online_appointment_id'=>null,
                    'module_type'=>'patient_bill',
                    'payment_status'=>'processing',
                );

                $transactionid  = $payment_data['billExternalReferenceNo'];

                $save_record = array(
                    'case_reference_id' => $session_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $amount,
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Toyyibpay TXN ID: " .$transactionid,
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

                $this->session->set_userdata("toyyibpay_payment_id",$payment_data['billExternalReferenceNo']);
          
                if((isset($obj->status) && $obj->status=='error')){
                    $result=$obj->msg;  
                    $data['api_error'] = $result;
                    $data['amount'] =number_format((float)($session_data['deposit_amount']), 2, '.', '');
                    $data['case_reference_id']   = $session_data['case_reference_id'];

                    $this->load->view("layout/patient/header");
                    $this->load->view('patient/payment/toyyibpay/index', $data);
                    $this->load->view("layout/patient/footer");
                    
                }else{
                  $url = "https://dev.toyyibpay.com/".$obj[0]->BillCode;
                    header("Location: $url");
                }
            }
        }
    }

    public function success(){
        $toyyibpay_payment_id = $this->session->userdata('toyyibpay_payment_id');
        $payment_data = $this->session->userdata('payment_data');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($toyyibpay_payment_id,'toyyibpay');

        if($parameter_data['payment_status']=='success'){
             redirect(base_url("patient/pay/successinvoice/"));
        }elseif($parameter_data['payment_status']=='fail'){
            $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
            redirect(base_url("patient/pay/paymentfailed/"));
        }
    }

}