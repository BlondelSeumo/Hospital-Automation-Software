<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Paystack extends Patient_Controller
{

    public $pay_method = "";
    public $amount     = 0;

    public function __construct()
    {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting    = $this->setting_model->get()[0];
        $this->load->model(array('onlineappointment_model', 'charge_model'));
    }

    public function index()
    {
        $appointment_id   = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting']  = $this->setting;
        $charges_array = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
        $tax = 0;
        $standard_charge = 0;
        if (isset($charges_array->standard_charge)) {
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge * $charges_array->percentage / 100);
            $tax = ($charges_array->standard_charge * $charges_array->percentage / 100);
            $standard_charge = $charges_array->standard_charge;
        } else {
            $charge = 0;
            $tax = 0;
            $standard_charge = 0;
        }
        $data['standard_charge'] = $standard_charge;
        $data['tax_amount'] = $tax;
        $this->session->set_userdata('payment_amount', $charge);
        $this->session->set_userdata('charge_id', $appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
        $this->load->view('patient/onlineappointment/paystack/index', $data);
    }

    public function pay()
    {
        $appointment_id   = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $amount           = $this->session->userdata('payment_amount') * 100;
        $ref              = time() . "02";
        $callback_url     = base_url() . 'patient/onlineappointment/paystack/complete/' . $ref;
        $postdata         = array('email' => $appointment_data->email, 'amount' => $amount, "reference" => $ref, "callback_url" => $callback_url);
        $url              = "https://api.paystack.co/transaction/initialize";
        $ch               = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata)); //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $headers = [
            'Authorization: Bearer ' . $this->pay_method->api_secret_key,
            'Content-Type: application/json',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $request = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($request, true);

        if ($result['status']) {

            $redir = $result['data']['authorization_url'];
            header("Location: " . $redir);
        } else {
            $appointment_id   = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting']  = $this->setting;
        $charges_array = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
        $tax = 0;
        $standard_charge = 0;
        if (isset($charges_array->standard_charge)) {
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge * $charges_array->percentage / 100);
            $tax = ($charges_array->standard_charge * $charges_array->percentage / 100);
            $standard_charge = $charges_array->standard_charge;
        } else {
            $charge = 0;
            $tax = 0;
            $standard_charge = 0;
        }
        $data['standard_charge'] = $standard_charge;
        $data['tax_amount'] = $tax;
        $this->session->set_userdata('payment_amount', $charge);
        $this->session->set_userdata('charge_id', $appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
       
            $data['api_error'] = $result['message'];
            $this->load->view('patient/onlineappointment/paystack/index', $data);
        }
    }

    public function complete($ref)
    {
        $patient_data  = $this->session->userdata('patient');
        $patient_id  = $patient_data['patient_id'];
        $appointment_id   = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $amount           = $this->session->userdata('payment_amount');
        $charge_id  = $this->session->userdata('charge_id');
        $result = array();
        $url    = 'https://api.paystack.co/transaction/verify/' . $ref;
        $ch     = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            ['Authorization: Bearer ' . $this->pay_method->api_secret_key]
        );
        $request = curl_exec($ch);
        curl_close($ch);

        if ($request) {
            $result = json_decode($request, true);

            if ($result) {
                if ($result['data']) {
                    //something came in
                    if ($result['data']['status'] == 'success') {

                        $transactionid                      = $ref;
                        $payment_data = array(
                            'appointment_id' => $appointment_id,
                            'paid_amount'    => $amount,
                            'charge_id'      => $charge_id,
                            'payment_type'   => 'Online',
                            'transaction_id' => $transactionid,
                            'payment_mode'   => 'Paystack',
                            'note'           => "Payment deposit through Paystack TXN ID: " . $transactionid,
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
                            'note'                   => "Payment deposit through Paystack TXN ID: " . $transactionid,
                            'payment_date'           => date('Y-m-d H:i:s'),
                            'received_by'            => '',
                        );


                        $return_detail                      = $this->onlineappointment_model->paymentSuccess($payment_data, $transaction_array);
                        redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail));
                    } else {
                        // the transaction was not successful, do not deliver value'
                        //uncomment this line to inspect the result, to check why it failed.
                        redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
                    }
                } else {

                    redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
                }
            } else {

                //die("Something went wrong while trying to convert the request variable to json. Uncomment the print_r command to see what is in the result variable.");
                redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
            }
        } else {
            //die("Something went wrong while executing curl. Uncomment the var_dump line above this line to see what the issue is. Please check your CURL command to make sure everything is ok");
            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }
    }
}
