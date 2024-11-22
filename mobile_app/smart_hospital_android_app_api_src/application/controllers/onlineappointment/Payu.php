<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Payu extends Admin_Controller
{
     var $setting;
     var $payment_method;
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('auth_model',  'setting_model', 'user_model', 'webservice_model','module_model','paymentsetting_model'));
        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();

    }

    public function index(){
        $pay_method     = $this->paymentsetting_model->getActiveMethod();
        if ($pay_method->payment_type == "payu") { 
            if ($this->session->has_userdata('params')) {
                $data = array();
                $payment_details  = $this->session->userdata("params");
                $appointment_id   = $payment_details['appointment_id'];
                $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
                $data['setting']  = $this->setting;
                $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
                if(isset($charges_array->standard_charge)){
                    $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
                }else{
                    $charge=0;
                }
                $this->session->set_userdata('payment_amount',$charge);
                $this->session->set_userdata('charge_id',$appointment_data->charge_id);
                $total = $charge;
                $data['amount'] = $total;
                if ($pay_method->api_secret_key != "" && $pay_method->salt != "") {    
                    $session_params         = $this->session->userdata('params');
                    $data['session_params'] = $session_params;
                    // Merchant key here as provided by Payu
                    $data['MERCHANT_KEY'] = $pay_method->api_secret_key;
                    // Merchant Salt as provided by Payu
                    $SALT = $pay_method->salt;
                    // End point - change to https://secure.payu.in for LIVE mode
                    $PAYU_BASE_URL  = "https://secure.payu.in";
                    $data['action'] = '';
                    $data['surl']   = base_url('onlineappointment/payu/success');
                    $data['furl']   = base_url('onlineappointment/payu/success');
                    $data['productinfo'] = "Online Payment For Smart Hospital";
                    $posted = array();
                    if (!empty($_POST)) {
                        foreach ($_POST as $key => $value) {
                            $posted[$key] = $value;
                        }
                    }
                    $data['posted']    = $posted;
                    $data['formError'] = 0;
                    if (empty($posted['txnid'])) {
                        // Generate random transaction id
                        $data['txnid'] = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
                    } else {
                        $data['txnid'] = $posted['txnid'];
                    }
                    $session_params['txn_id'] = $data['txnid'];
                    $this->session->set_userdata("params", $session_params);
                    $data['hash'] = '';
                    $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
                    if (empty($posted['hash']) && sizeof($posted) > 0) {
                        if (
                            empty($posted['key'])
                            || empty($posted['txnid'])
                            || empty($posted['firstname'])
                            || empty($posted['email'])
                            || empty($posted['phone'])
                            
                            || empty($posted['surl'])
                            || empty($posted['furl'])
                            || empty($posted['service_provider'])
                        ) {
                            $formError = 1;
                        } else {
                            $hashVarsSeq = explode('|', $hashSequence);
                            $hash_string = '';
                            foreach ($hashVarsSeq as $hash_var) {
                                if($hash_var != "amount"){
                                    $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
                                    $hash_string .= '|';
                                }else{
                                    $hash_string .= $total;
                                    $hash_string .= '|';
                                }
                            }
                            $hash_string .= $SALT;
                            $data['hash']   = strtolower(hash('sha512', $hash_string));
                            $data['action'] = $PAYU_BASE_URL . '/_payment';
                        }
                    } elseif (!empty($posted['hash'])) {
                        $data['hash']   = $posted['hash'];
                        $data['action'] = $PAYU_BASE_URL . '/_payment';
                    }
                    $this->load->view('onlineappointment/payu/index', $data);
                }
            }
        }else{
            $this->session->set_flashdata('error', 'Oops! Something went wrong');
            $this->load->view('payment/error');
        }
    } 

  

    public function success(){
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $payment_data = $this->session->userdata('params');
            $mihpayid      = $this->input->post('mihpayid');
            $transactionid = $this->input->post('txnid');
            $txn_id        = $payment_data['txn_id'];
            if ($txn_id == $transactionid) {
                if ($this->session->has_userdata('params')) {
                $session_data = $this->session->userdata("params");
                $amount       = $this->session->userdata('payment_amount');
                $appointment_id   = $session_data['appointment_id'];
                $patient_id  = $session_data['patient_id'];
                $charge_id  = $this->session->userdata('charge_id');
            }
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'transaction_id' => $transactionid,
                'payment_type'   => 'Online',
                'payment_mode'   => 'Ipayafrica',
                'note'           => "Payment deposit through Ipayafrica TXN ID: " . $transactionid,
                'date'           => date("Y-m-d H:i:s"),
            ); 
            $payment_section = $this->config->item('payment_section');
            $transaction_array = array(
                'amount'                 => $amount,
                'patient_id'             => $patient_id,
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Ipayafrica TXN ID: " . $transactionid,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );

            $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
            if($status){
                redirect(base_url("payment/appointmentsuccess/".$appointment_id));
            }
            }else {

                redirect('payment/paymentfailed', 'refresh');
            }
        }
    }

}
