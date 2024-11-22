<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Walkingm extends Patient_Controller
{

    public $pay_method = "";
    public $amount     = 0;

    public function __construct()
    {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting    = $this->setting_model->get()[0];
        $this->load->library(array('walkingm_lib', 'mailsmsconf'));
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
        $this->load->view('patient/onlineappointment/walkingm/index', $data);
    }

    public function pay()
    {

        $this->form_validation->set_rules('email', $this->lang->line('walkingm_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('walkingm_password'), 'trim|required|xss_clean');
        $params = $this->session->userdata('params');
        if ($this->form_validation->run() == false) {
            $data['api_error'] = "";
            $appointment_id   = $this->session->userdata('appointment_id');
            $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
            $data['setting']  = $this->setting;
            $charge = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
            $this->session->set_userdata('payment_amount', isset($charge->standard_charge)?$charge->standard_charge:0);
            $total          = isset($charge->standard_charge)?$charge->standard_charge:0;
            $data['amount'] = $total;
            $this->load->view('patient/onlineappointment/walkingm/index', $data);
        } else {
            $payment_array['payer']      = "Walkingm";
            $appointment_id   = $this->session->userdata('appointment_id');
            $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
            $charge = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
            $payment_array['amount']     = isset($charge->standard_charge)?$charge->standard_charge:0;
            $payment_array['currency']   = $this->setting["currency"];
            $payment_array['successUrl'] = base_url() . "patient/onlineappointment/walkingm/success";
            $payment_array['cancelUrl']  = base_url() . "patient/onlineappointment/walkingm/cancel";
            $response                    = $this->walkingm_lib->walkingm_login($_POST['email'], $_POST['password'], $payment_array);

            if ($response != "") {
                $appointment_id   = $this->session->userdata('appointment_id');
                $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
                $data['setting']  = $this->setting;
                $this->session->set_userdata('payment_amount', isset($charge->standard_charge)?$charge->standard_charge:0);
                $total             = isset($charge->standard_charge)?$charge->standard_charge:0;
                $data['api_error'] = $response;
                $data['amount']    = $total;
                $this->load->view('patient/onlineappointment/walkingm/index', $data);
            }
        }
    }
 
    public function success()
    {
        $data             = array();
        $response         = base64_decode($_SERVER["QUERY_STRING"]);
        $payment_response = json_decode($response);
       
        $data             = array();
        if ($response != '' && $payment_response->status = 200) {
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
                'payment_mode'   => 'WalkingM',
                'note'           => "Payment deposit through WalkingM TXN ID: " . $transactionid,
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
        } else { 
            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }
    }

    public function cancel()
    {
        redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
    }

}
