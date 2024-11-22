<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Paypal extends Patient_Controller
{

    public $pay_method = "";
    public $amount     = 0;

    public function __construct()
    {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting    = $this->setting_model->get()[0];
        $this->load->library(array('paypal_payment', 'mailsmsconf'));
        $this->load->model(array('onlineappointment_model','charge_model'));
    }

    public function index()
    {
        $appointment_id   = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting']  = $this->setting;
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

        $this->load->view('patient/onlineappointment/paypal/index', $data);
    }

    public function checkout()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            if ($this->session->has_userdata('payment_amount')) {
                $setting                        = $this->setting;
                $appointment_id                 = $this->session->userdata('appointment_id');
                $reference                      = $this->session->userdata('reference');
                $appointment_data               = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
                $charge = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
                $total                          = isset($charge->standard_charge)?$charge->standard_charge:0;
                $data["id"]                     = $reference;
                $data                           = array();
                $data['total']                  = $total;
				$data['productinfo'] = $this->lang->line('online_payment');
                $data['symbol']                 = $setting["currency_symbol"];
                $data['currency_name']          = $setting["currency"];
                $data['guardian_phone']         = "";
                $data['student_fees_master_id'] = "";
                $data['fee_groups_feetype_id']  = "";
                $data['phone']                  = "";
                $data['ipd_id']                 = "";
                $data['patient_id']             = $appointment_data->patient_id;
                $data['name']                   = $appointment_data->name;
                $response                       = $this->paypal_payment->payment($data);
                if ($response->isSuccessful()) {

                } elseif ($response->isRedirect()) {
                    $response->redirect();
                } else {
                    $appointment_id   = $this->session->userdata('appointment_id');
                    $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
                    $charges_array = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
                    $data['setting']  = $this->setting;
                    $this->session->set_userdata('payment_amount', isset($charges_array->standard_charge)?$charges_array->standard_charge:0);
                    $total          = isset($charges_array->standard_charge)?$charges_array->standard_charge:0;
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
                    $data['amount'] = $total;
                    $data['api_error'] =  $response->getMessage();
                    $this->load->view('patient/onlineappointment/paypal/index', $data); 
                }
            }
        }
    }

    //paypal successpayment
    public function getsuccesspayment()
    {

        $data                   = array();

        $response                       = $this->paypal_payment->success($data, "patient");

        $paypalResponse = $response->getData();
        if ($response->isSuccessful()) {
            $purchaseId = $_GET['PayerID'];

            if (isset($paypalResponse['PAYMENTINFO_0_ACK']) && $paypalResponse['PAYMENTINFO_0_ACK'] === 'Success') {
                if ($purchaseId) {

            $ref_id     = $paypalResponse['PAYMENTINFO_0_TRANSACTIONID'];
            $patient_data  = $this->session->userdata('patient');
            $patient_id  = $patient_data['patient_id'];
            $amount = $this->session->userdata('payment_amount');
            $appointment_id = $this->session->userdata('appointment_id');
            $charge_id = $this->session->userdata('charge_id');
            $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
            $transactionid       = $payment_response->transaction_id;
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'payment_type'   => 'Online',
                'transaction_id'=>$transactionid,
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
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => '',
            );
            
            $return_detail               = $this->onlineappointment_model->paymentSuccess($payment_data,$transaction_array);
          redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail['insert_id']));
                }
            }
        } elseif ($response->isRedirect()) {
            $response->redirect();
        } else {
            redirect(base_url("students/payment/paymentfailed"));
        }
    }

}
