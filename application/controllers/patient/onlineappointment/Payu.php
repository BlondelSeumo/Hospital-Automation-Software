<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payu extends Patient_Controller
{

    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting = $this->setting_model->get()[0];
        $this->load->model(array('onlineappointment_model','charge_model'));
    }

    public function index()
    {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $charges_array = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
        $tax=0;
        $standard_charge=0;
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
            $tax=($charges_array->standard_charge*$charges_array->percentage/100);
            $standard_charge=$charges_array->standard_charge;
        }else{
            $charge=0;
            $tax=0;
            $standard_charge=0;
        } 
        $data['standard_charge']=$standard_charge;
        $data['tax_amount']=$tax;
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
        $txnid                      = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
        //payumoney details
        $amount           = $total;
        $customer_name    = $appointment_data->name;
        $customer_emial   = $appointment_data->email;
        $customer_mobile  = $appointment_data->mobileno;
        $customer_address  = "";
        $product_info = $this->lang->line('online_payment');
        $MERCHANT_KEY = $this->pay_method->api_secret_key;
        $SALT         = $this->pay_method->salt;

        //optional udf values
        $udf1 = '';
        $udf2 = '';
        $udf3 = '';
        $udf4 = '';
        $udf5 = '';

        $hashstring = $MERCHANT_KEY . '|' . $txnid . '|' . $amount . '|' . $product_info . '|' . $customer_name . '|' . $customer_emial . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $SALT;
        $hash       = strtolower(hash('sha512', $hashstring));

        $success = base_url('patient/onlineappointment/payu/success');
        $fail    = base_url('patient/onlineappointment/payu/success');
        $cancel  = base_url('patient/onlineappointment/payu/success');
        $data    = array(
            'mkey'                      => $MERCHANT_KEY,
            'tid'                       => $txnid,
            'hash'                      => $hash,
            'amount'                    => $amount,
            'name'                      => $customer_name,
            'productinfo'               => $product_info,
            'mailid'                    => $customer_emial,
            'phoneno'                   => $customer_mobile,
            'address'                   => $customer_address,
            'action'                    => "https://secure.payu.in/_payment", //for live change action  https://secure.payu.in
            'sucess'                    => $success,
            'failure'                   => $fail,
            'cancel'                    => $cancel,
        );
        $data['standard_charge']=$standard_charge;
        $data['tax_amount']=$tax;
        $data['setting']      = $this->setting;
      
        $this->load->view('patient/onlineappointment/payu/index', $data);
    }

    public function checkout()
    {

        $this->form_validation->set_rules('firstname', $this->lang->line('customer_name'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('phone', $this->lang->line('mobile_no'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'required|valid_email|trim|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'firstname' => form_error('firstname'),
                'phone'     => form_error('phone'),
                'email'     => form_error('email'),
                'amount'    => form_error('amount'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            $array = array('status' => 'success', 'error' => '');
            echo json_encode($array);
        }
    }

    public function success()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $amount = $this->session->userdata('payment_amount');
            if ($this->input->post('status') == "success") {
                $mihpayid      = $this->input->post('mihpayid');
                $transactionid = $this->input->post('txnid');
                if ($txn_id == $transactionid) {
                    $appointment_id = $this->session->userdata('appointment_id');
                    $charge_id = $this->session->userdata('charge_id');
                    $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
                    $gateway_response['appointment_id'] = $appointment_id;
                    $gateway_response['charge_id']      = $charge_id;  
                    $gateway_response['paid_amount']    = $amount;
                    $gateway_response['transaction_id'] = $transactionid;
                    $gateway_response['payment_mode']   = 'Payu';
                    $gateway_response['payment_type']   = 'Online';
                    $gateway_response['note']           = "Payment deposit through Payu TXN ID: " . $transactionid;
                    $gateway_response['date']           = date("Y-m-d H:i:s");
                    $payment_section = $this->config->item('payment_section');
                    $transaction_array = array(
                        'amount'                 => $amount,
                        'patient_id'             => $this->customlib->getPatientSessionUserID(),
                        'section'                => $payment_section['appointment'],
                        'type'                   => 'payment',
                        'appointment_id'         => $appointment_id,
                        'payment_mode'           => "Online",
                        'note'                   => "Online fees deposit through Payu TXN ID: " . $transactionid ,
                        'payment_date'           => date('Y-m-d H:i:s'),
                        'received_by'            => 1,
                    );
                    $return_detail    = $this->onlineappointment_model->paymentSuccess($gateway_response,$transaction_array);
                    redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail['insert_id']));
                } else {
                    redirect(base_url('patient/onlineappointment/checkout/paymentfailed'));
                }
            }else {
                redirect(base_url('patient/onlineappointment/checkout/paymentfailed'));
            }
        }
    }

}
