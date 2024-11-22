<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Paypal extends Admin_Controller
{
    var $setting;
    var $payment_method;
    public function __construct()
    {
        parent::__construct();
        $this->load->library('paypal_payment');
        $this->load->model(array('auth_model',  'setting_model', 'user_model', 'webservice_model','module_model','paymentsetting_model'));
        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();

    }
    public function index()
    {
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        $this->load->view('payment/paypal/index', $data);
    }

    public function pay(){
        $this->form_validation->set_rules("email", "email", "trim|required");
        $this->form_validation->set_rules("phone", "Phone", "trim|required");
        $this->form_validation->set_rules("amount", "Amount", "trim|required");
        if($this->form_validation->run() == false){
            $data = array();
            $data['api_error']=array();
            $payment_details=$this->session->userdata("params");
            $data['session_params']=$payment_details;
            $data['setting'] = $this->setting;
            $this->load->view('payment/paypal/index', $data);
        }else{
            if ($this->input->server('REQUEST_METHOD') == 'POST') {
                if ($this->session->has_userdata('params')) {
                    $setting               = $this->setting[0];
                    $params                = $this->session->userdata('params');               
                    $data                  = array();
                    $data['total']         = $this->input->post('amount');
                    $data['productinfo']   = "bill payment smart hospital";
                    $data['symbol']        = $setting['currency_symbol'];
                    $data['currency_name'] = $setting['currency'];
                    $data['name']          = $params['name'];
                    $data['patient_id']    = $params['patient_id'];
                    $data['id']        = $params['id'];
                    $data['phone']         = $_POST['phone'];
                    $params['post_amount']=$this->input->post('amount');
                    $this->session->set_userdata("params", $params);
                    $response              = $this->paypal_payment->payment($data);
                    if ($response->isSuccessful()) {

                    } elseif ($response->isRedirect()) {
                        $response->redirect();
                    } else {
                        echo $response->getMessage();
                    }
                }
            }
        }
    }

    public function getsuccesspayment(){ 
        $data                  = array();
        $payment_data                = $this->session->userdata('params');
        $data                  = array();
        $data['total']         = $payment_data['post_amount'];
        $data['productinfo']   = "bill payment smart hospital";
        $data['symbol']        = $setting['currency_symbol'];
        $data['currency_name'] = $setting['currency'];
        $data['name']          = $payment_data['name'];
        $data['patient_id']    = $payment_data['patient_id'];
        $data['id']        = $payment_data['id'];
        $data['phone']         = $_POST['phone'];
        $response              = $this->paypal_payment->success($data);
        $paypalResponse = $response->getData();       
        if ($response->isSuccessful()) {
            $purchaseId = $_GET['PayerID'];
            if (isset($paypalResponse['PAYMENTINFO_0_ACK']) && $paypalResponse['PAYMENTINFO_0_ACK'] === 'Success') {
                if ($purchaseId) {
                    $payment_data     = $this->session->userdata('params');
                    $transactionid     = $paypalResponse['PAYMENTINFO_0_TRANSACTIONID'];
                    $save_record = array(
                        'case_reference_id' => $payment_data["case_reference_id"],
                        'type' => "payment",
                        'amount'        => $payment_data['post_amount'],
                        'payment_mode' => 'Online',
                        'payment_date' => date('Y-m-d H:i:s'),
                        'note'         => "Online fees deposit through Paypal TXN ID: " . $transactionid,
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
                }
            }
        } elseif ($response->isRedirect()) {
            $response->redirect();
        } else {
             redirect('payment/paymentfailed','refresh');
        }
    }
}
