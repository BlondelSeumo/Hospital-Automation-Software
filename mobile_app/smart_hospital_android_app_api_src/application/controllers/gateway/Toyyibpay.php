<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Toyyibpay extends Admin_Controller {

    public $api_config = "";
    public $pay_method = array();

    public function __construct() {
        parent::__construct();
        $this->setting = $this->setting_model->get();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->load->model(array('gateway_ins_model'));
    }
  
    public function index() {
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
       
        $this->load->view('payment/toyyibpay/index', $data);
    }
 
    public function pay(){
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $result = array();
        $data['currency']            = $setting['currency'];

        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->load->view('payment/toyyibpay/index', $data);
        } else {
            $payment_details['post_amount']=$this->input->post('amount');
            $this->session->set_userdata("params", $payment_details);

            $data['name'] = $payment_details['name'];
            $amount =number_format((float)($payment_details['post_amount']), 2, '.', ''); 
            $payment_data = array(
                'userSecretKey'=>$this->pay_method->api_secret_key,
                'categoryCode'=>$this->pay_method->api_signature,
                'billName'=>'Patient Bill',
                'billDescription'=>'Patient Bill',
                'billPriceSetting'=>1,
                'billPayorInfo'=>1,
                'billAmount'=>$payment_details['post_amount'],
                'billReturnUrl'=>base_url().'gateway/toyyibpay/success',
                'billCallbackUrl'=>base_url().'gateway_ins/toyyibpay',
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
              curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');  
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
                    'online_appointment_id'=>NULL,
                    'module_type'=>'patient_bill',
                    'payment_status'=>'processing',
                );

                $transactionid  = $payment_data['billExternalReferenceNo'];

                $save_record = array(
                    'case_reference_id' => $payment_details["case_reference_id"],
                    'type' => "payment",
                    'amount'        => $payment_details['post_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Toyyibpay TXN ID: " . $transactionid,
                    'patient_id'  => $payment_details['patient_id'],
                );
                if($payment_details['type'] == "opd"){
                    $save_record["opd_id"] = $payment_details['id'];
                }elseif($payment_details['type'] == "ipd"){
                    $save_record["ipd_id"] = $payment_details['id'];
                }elseif($payment_details['type'] == "pharmacy"){
                    $save_record["pharmacy_bill_basic_id"] = $payment_details['id'];
                }elseif($payment_details['type'] == "pathology"){
                    $save_record["pathology_billing_id"] = $payment_details['id'];
                }elseif($payment_details['type'] == "radiology"){
                    $save_record["radiology_billing_id"] = $payment_details['id'];
                }elseif($payment_details['type'] == "blood_bank"){
                    $save_record["blood_issue_id"] = $payment_details['id'];
                }elseif($payment_details['type'] == "ambulance"){
                    $save_record["ambulance_call_id"] = $payment_details['id'];
                }

                $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
                $save_record["gateway_ins_id"] = $gateway_ins_id;

                $this->gateway_ins_model->add_transactions_processing($save_record);

                $this->session->set_userdata("toyyibpay_payment_id",$payment_data['billExternalReferenceNo']);
          
                if((isset($obj->status) && $obj->status=='error')){
                    $result=$obj->msg;  
                    $data['api_error'] = $result;
                    $data['amount'] =number_format((float)($payment_details['post_amount']), 2, '.', '');
                    $data['case_reference_id']   = $payment_details['case_reference_id'];

                    $this->load->view('payment/toyyibpay/index', $data);
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
            redirect(base_url("payment/successinvoice/"));
        }elseif($parameter_data['payment_status']=='fail'){
            $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
            redirect(site_url('payment/paymentfailed'));
        }
    }

}