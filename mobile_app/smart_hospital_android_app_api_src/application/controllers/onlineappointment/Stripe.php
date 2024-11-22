<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/stripe/init.php';
class Stripe extends Admin_Controller
{
    var $setting;
    var $payment_method;
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('auth_model',  'setting_model', 'user_model', 'webservice_model','module_model','paymentsetting_model'));
        $this->setting = $this->setting_model->get();
        $this->load->library(array('stripe_payment'));
        $this->payment_method = $this->paymentsetting_model->get();
    }

    public function index(){
    	
        $payment_details  = $this->session->userdata("params");
        $appointment_id   = $payment_details['appointment_id'];
         $pay_method     = $this->paymentsetting_model->getActiveMethod();
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
        }else{
            $charge=0;
        }
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data = array();
        $data['amount'] = $total;
        
        $data['api_publishable_key']=$pay_method->api_publishable_key;
        $data['currency_name']=$this->setting[0]['currency'];
       
        if ($pay_method->payment_type == "stripe") {
            if ($this->session->has_userdata('params')) {
                if ($pay_method->api_secret_key != "" && $pay_method->api_publishable_key != "") {
                    $session_params  = $this->session->userdata('params');
                    $data['setting'] = $this->setting;
                    $data['session_params']=$session_params;
                    $invoice_array        = $session_params['invoice'];
                    $payment_detail_array = $session_params['payment_detail'];
                    $params = array(
                        "testmode"         => "on",
                        "private_live_key" => "sk_live_xxxxxxxxxxxxxxxxxxxxx",
                        "public_live_key"  => "pk_live_xxxxxxxxxxxxxxxxxxxxx",
                        "private_test_key" => "sk_test_YLQh86Az2IdcuqfQQOx47yam",
                        "public_test_key"  => "pk_test_nYHEZ1mJ8FpaoXV4KVxQs7qR",
                    );
                    if ($params['testmode'] == "on") {
                \Stripe\Stripe::setApiKey($params['private_test_key']);
                $pubkey = $params['public_test_key'];
                    } else {
                        \Stripe\Stripe::setApiKey($params['private_live_key']);
                        $pubkey = $params['public_live_key'];
                    }
                    if ($this->input->server('REQUEST_METHOD') == 'POST') {
                        if (isset($_POST['stripeToken'])) {
                            $invoiceid   = abs(crc32(uniqid())); // Invoice ID
                            $description = "Fees Payment";
                            try {
                                $response = \Stripe\Charge::create(
                                    array(
                                        'amount'      => ($total) * 100,
                                        "currency"    => $invoice_array->currency_name,
                                        "source"      => $_POST['stripeToken'],
                                        "description" => $description,
                                    )
                                );
                                if ($response->status == 'succeeded') {
                                    $transactionid                 = $response->balance_transaction;
                                    $session_data = $this->session->userdata("params");
                                    $appointment_id   = $session_data['appointment_id'];
                                    $patient_id  = $session_data['patient_id'];
                                    $charge_id  = $this->session->userdata('charge_id');
                                    $amount                             = $this->session->userdata('payment_amount');
                                    $payment_data = array(
                                        'appointment_id' => $appointment_id,
                                        'paid_amount'    => $amount,
                                        'charge_id'      => $charge_id,
                                        'transaction_id' => $transactionid,
                                        'payment_type'   => 'Online',
                                        'payment_mode'   => 'Stripe',
                                        'note'           => "Payment deposit through Stripe TXN ID: " . $transactionid,
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
                                        'note'                   => "Online fees deposit through Stripe TXN ID: " . $transactionid,
                                        'payment_date'           => date('Y-m-d H:i:s'),
                                        'received_by'            => '',
                                    );

                                    $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
                                    if ($status) {
                                       redirect(base_url("payment/appointmentsuccess/".$appointment_id));
                                    } else {

                                    }
                                }
                            } catch (\Stripe\Error\Card $e) {
                                // Since it's a decline, \Stripe\Error\Card will be caught
                                $body    = $e->getJsonBody();
                                $err     = $body['error'];
                                $error[] = $err['message'];
                            } catch (\Stripe\Error\RateLimit $e) {
                                // Too many requests made to the API too quickly
                                $error[] = $e->getMessage();
                            } catch (\Stripe\Error\InvalidRequest $e) {
                                // Invalid parameters were supplied to Stripe's API
                                $error[] = $e->getMessage();
                            } catch (\Stripe\Error\Authentication $e) {
                                // Authentication with Stripe's API failed
                                // (maybe you changed API keys recently)
                                $error[] = $e->getMessage();
                            } catch (\Stripe\Error\ApiConnection $e) {
                                // Network communication with Stripe failed
                                $error[] = $e->getMessage();
                            } catch (\Stripe\Error\Base $e) {
                                // Display a very generic error to the user, and maybe send
                                // yourself an email
                                $error[] = $e->getMessage();
                            } catch (Exception $e) {
                                // Something else happened, completely unrelated to Stripe
                                $error[] = $e->getMessage();
                            }
                            $this->session->set_flashdata('error', $error);
                            redirect(site_url('payment/paymentfailed'));
                        }
                    }
                    $data['params'] = $params;
                    $this->load->view('onlineappointment/stripe/pay', $data);
                }
              }
        }else{
            $this->session->set_flashdata('error', 'Oops! Something went wrong');
            $this->load->view('payment/error');
        }
    }
    public function create_payment_intent()
    {
       
        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);
        
        $this->stripe_payment->PaymentIntent($jsonObj );
    }
    public function create_customer()
    {
        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);

        $payment_details  = $this->session->userdata("params");

        $jsonObj->fullname = $payment_details['name'];
        $jsonObj->email = $payment_details['email'];
        $this->stripe_payment->AddCustomer($jsonObj);
    }





    
    public function insert_payment()
    {

        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);
        $return_response = $this->stripe_payment->InsertTransaction($jsonObj);
        if ($return_response['status']) {
            $payment = $return_response['payment'];
            
            // If transaction was successful
            if (!empty($payment) && $payment->status == 'succeeded') {
                $transactionid = $payment->id;
                //=============================
                $payment_section = $this->config->item('payment_section');
                $appointment_id = $this->session->userdata('appointment_id');
                $charge_id = $this->session->userdata('charge_id');
               
                $session_data = $this->session->userdata("params");
                $appointment_id   = $session_data['appointment_id'];
                $patient_id  = $session_data['patient_id'];
                $charge_id  = $this->session->userdata('charge_id');
                $amount                             = $this->session->userdata('payment_amount');
                $payment_data = array(
                    'appointment_id' => $appointment_id,
                    'paid_amount'    => $amount,
                    'charge_id'      => $charge_id,
                    'transaction_id' => $transactionid,
                    'payment_type'   => 'Online',
                    'payment_mode'   => 'Stripe',
                    'note'           => "Payment deposit through Stripe TXN ID: " . $transactionid,
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
                    'note'                   => "Online fees deposit through Stripe TXN ID: " . $transactionid,
                    'payment_date'           => date('Y-m-d H:i:s'),
                    'received_by'            => '',
                );

                $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
          
                    echo json_encode(['status'=>1,'msg' => 'Transaction successful.','return_url'=>base_url("payment/appointmentsuccess/" . $appointment_id)]);

                //=====================================



            } else {
                http_response_code(500);
                echo json_encode(['status'=>0,'msg' => 'Transaction has been failed!','return_url'=>base_url('patient/onlineappointment/checkout/paymentfailed')]);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status'=>0,'msg' => $return_response['error']]);
        }
    }
}
