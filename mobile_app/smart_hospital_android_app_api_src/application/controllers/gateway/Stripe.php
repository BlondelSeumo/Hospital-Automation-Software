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
        $this->payment_method = $this->paymentsetting_model->get();
        $this->load->library(array('stripe_payment'));
    }

    public function index(){

        $data = array();
        $payment_details=$this->session->userdata("params");
       
        $pay_method     = $this->paymentsetting_model->getActiveMethod();
        $data['amount'] =$payment_details['total'];
        $data['api_publishable_key']=$pay_method->api_publishable_key;
        $data['currency_name']=$this->setting[0]['currency'];
        $this->form_validation->set_rules("email", "Email", "trim|required");
        $this->form_validation->set_rules("mobileno", "Phone", "trim|required");
        $this->form_validation->set_rules("amount", "Amount", "trim|required|callback_valid_amount");
        if($this->form_validation->run() == false){
           
            $data['api_error']=array();
            $payment_details=$this->session->userdata("params");
            $data['session_params']=$payment_details;
            $data['setting'] = $this->setting;
            $params = array(
                "testmode"         => "on",
                "private_live_key" => "sk_live_xxxxxxxxxxxxxxxxxxxxx",
                "public_live_key"  => "pk_live_xxxxxxxxxxxxxxxxxxxxx",
                "private_test_key" => "sk_test_YLQh86Az2IdcuqfQQOx47yam",
                "public_test_key"  => "pk_test_nYHEZ1mJ8FpaoXV4KVxQs7qR",
            );
            $data['params'] = $params;

            $this->load->view('payment/stripe/pay', $data);
        }else{
            $pay_method     = $this->paymentsetting_model->getActiveMethod();
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
                                            'amount'      => $this->input->post('amount') * 100,
                                            "currency"    => $invoice_array->currency_name,
                                            "source"      => $_POST['stripeToken'],
                                            "description" => $description,
                                        )
                                    );
                                    if ($response->status == 'succeeded') {
                                        $transactionid                 = $response->balance_transaction;
                                        $payment_data = $this->session->userdata('params');
                                        $payment_data['transactionid'] = $transactionid;
                                        $save_record = array(
                                            'case_reference_id' => $payment_data["case_reference_id"],
                                            'type' => "payment",
                                            'amount'        => $this->input->post("amount"),
                                            'payment_mode' => 'Online',
                                            'payment_date' => date('Y-m-d H:i:s'),
                                            'note'         => "Online fees deposit through Stripe TXN ID: " . $transactionid,
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
                                        if ($insert_id) {
                                            $invoice_detail = json_decode($insert_id);
                                            redirect("payment/successinvoice/" . $insert_id, "refresh");
                                            die;
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

                        $this->load->view('payment/stripe/pay', $data);
                    }
                  }
            }else{
                $this->session->set_flashdata('error', 'Oops! Something went wrong');
                $this->load->view('payment/error');
            }
        }
    }

    public function create_payment_intent()
    {
       
        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);
        
        $this->stripe_payment->PaymentIntent($jsonObj);
    }
    
    public function create_customer()
    {
        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);
        $user_detail = $this->session->userdata('params');

        $jsonObj->fullname = $user_detail['name'];
        $jsonObj->email = $user_detail['email'];
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
                $payment_data = $this->session->userdata('params');
                                        $payment_data['transactionid'] = $transactionid;
                                        $save_record = array(
                                            'case_reference_id' => $payment_data["case_reference_id"],
                                            'type' => "payment",
                                            'amount'        => $this->input->post("amount"),
                                            'payment_mode' => 'Online',
                                            'payment_date' => date('Y-m-d H:i:s'),
                                            'note'         => "Online fees deposit through Stripe TXN ID: " . $transactionid,
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
               
          
                    echo json_encode(['status'=>1,'msg' => 'Transaction successful.','return_url'=>base_url("payment/successinvoice/" . $insert_id)]);

                //=====================================



            } else {
                http_response_code(500);
                echo json_encode(['status'=>0,'msg' => 'Transaction has been failed!','return_url'=>base_url('patient/pay/paymentfailed/')]);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status'=>0,'msg' => $return_response['error']]);
        }
    }
 public function valid_amount($str) {

    
      
      // if (!preg_match("/^\d+(\.\d{1,2})?$/", $str)) 
      if (!preg_match("/^(0*[1-9][0-9]*(\.[0-9]+)?|0+\.[0-9]*[1-9][0-9]*)$/", $str)) 
      {
          $this->form_validation->set_message('valid_amount', 'Invalid {field}.');
                return FALSE;
      }
    
      return TRUE;
    }
}
