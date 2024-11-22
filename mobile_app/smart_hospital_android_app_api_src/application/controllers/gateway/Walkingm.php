<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Walkingm extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();
        $this->load->library('Walkingm_lib');
        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->getActiveMethod();
    }

    public function index() {
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        $this->load->view('payment/walkingm/walkingm', $data);
    }

    public function pay() {

        
        $data = array();
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
        $this->form_validation->set_rules('walkingm_email', 'Walkingm Email', 'trim|required');
        $this->form_validation->set_rules('walking_password', 'Walkingm Password', 'trim|required');
        if ($this->form_validation->run()==false) {
            $this->load->view('payment/walkingm/walkingm', $data);

        }else{
            $payment_details=$this->session->userdata('params');
            $payment_details['post_amount']=$this->input->post('amount');
            $invoice_array        = $session_params['invoice'];
            $this->session->set_userdata("params", $payment_details);
            if ($this->session->has_userdata('params')) {


                $api_publishable_key = ($this->payment_method->api_publishable_key);
                $api_secret_key = ($this->payment_method->api_secret_key);
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key'] = $api_secret_key;
                
            }
            $amount= $payment_details['post_amount'];
            $payment_array['payer']="Walkingm";
            $payment_array['amount']=$amount;
            $payment_array['currency']= $invoice_array->currency_name;
            $payment_array['successUrl']= base_url()."gateway/walkingm/success";
            $payment_array['cancelUrl']=base_url()."gateway/walkingm/cancel";
            $response= $this->walkingm_lib->walkingm_login($_POST['email'],$_POST['password'],$payment_array);

          if($response!=""){
           
            $data = array();
            $data['params'] = $this->session->userdata('params');
            $data['setting'] = $this->setting;
            $data['api_error'] = $response;
            $this->load->view('payment/walkingm/index', $data);
          }
           

            
            } 
        }
    

    public function success() {
        $response= base64_decode($_SERVER["QUERY_STRING"]);
        $payment_response=json_decode($response);
        
        if ($response != '' && $payment_response->status=200) {
            $transactionid = $payment_response->transaction_id;
            if ($transactionid) {
                $params = $this->session->userdata('params');
                $payment_data = $this->session->userdata('params');
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'        => $payment_data['post_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Instamojo TXN ID: " . $transactionid,
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
                redirect(base_url("payment/successinvoice/" . $insert_id));
                }
        } else {
            redirect(base_url("payment/paymentfailed"));
        }
    }

      public function cancel() {
        redirect(base_url("payment/paymentfailed"));
      }

}
