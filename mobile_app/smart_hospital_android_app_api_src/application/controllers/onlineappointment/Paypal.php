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
        $this->payment_method = $this->paymentsetting_model->getActiveMethod();

    }
    public function index()
    {
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
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        $this->load->view('onlineappointment/paypal/index', $data);
    }

    public function pay(){
        $data = array();
        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $amount          = $this->session->userdata('payment_amount');
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
     
        if ($this->form_validation->run()==false) {
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
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
            $this->load->view('onlineappointment/paypal/index', $data);
        }else{
            $payment_details=$this->session->userdata('params');
            if ($this->session->has_userdata('params')) {
                $api_publishable_key = ($this->payment_method->api_publishable_key);
                $api_secret_key = ($this->payment_method->api_secret_key);
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key'] = $api_secret_key; 
            }
            if ($this->input->server('REQUEST_METHOD') == 'POST') {
                if ($this->session->has_userdata('params')) {
                    $setting               = $this->setting[0];
                    $params                = $this->session->userdata('params');               
                    $data                  = array();
                    $data['total']         = $amount;
                    $data['productinfo']   = "bill payment smart hospital";
                    $data['symbol']        = $setting['currency_symbol'];
                    $data['currency_name'] = $setting['currency'];
                    $data['name']          = $params['name'];
                    $data['patient_id']    = $params['patient_id'];
                    $data['id']            = $params['appointment_id'];
                    $data['phone']         = $_POST['phone'];
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

    //paypal successpayment
    public function getsuccesspayment(){ 
        $session_data = $this->session->userdata("params");
        $appointment_id   = $session_data['appointment_id'];
        $patient_id  = $session_data['patient_id'];
        $charge_id  = $this->session->userdata('charge_id');
        $response              = $this->paypal_payment->success($data);
        $paypalResponse = $response->getData();       
        if ($response->isSuccessful()) {
            $purchaseId = $_GET['PayerID'];
            if (isset($paypalResponse['PAYMENTINFO_0_ACK']) && $paypalResponse['PAYMENTINFO_0_ACK'] === 'Success') {
                if ($purchaseId) {
                    $amount                             = $this->session->userdata('payment_amount');
                    $transactionid     = $paypalResponse['PAYMENTINFO_0_TRANSACTIONID'];
                    $payment_data = array(
                        'appointment_id' => $appointment_id,
                        'paid_amount'    => $amount,
                        'charge_id'      => $charge_id,
                        'transaction_id' => $transactionid,
                        'payment_type'   => 'Online',
                        'payment_mode'   => 'Paypal',
                        'note'           => "Payment deposit through Paypal TXN ID: " . $transactionid,
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
                        'note'                   => "Online fees deposit through Paypal TXN ID: " . $transactionid,
                        'payment_date'           => date('Y-m-d H:i:s'),
                        'received_by'            => '',
                    );

                    $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
                    if($status){
                       redirect(base_url("payment/appointmentsuccess/".$appointment_id));
                    }
                }
            }
        } elseif ($response->isRedirect()) {
            $response->redirect();
        } else {
             redirect('payment/paymentfailed','refresh');
        }
    }
}
