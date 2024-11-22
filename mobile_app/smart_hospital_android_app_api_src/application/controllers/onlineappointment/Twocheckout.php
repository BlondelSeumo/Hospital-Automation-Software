<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Twocheckout extends Admin_Controller {

    public $api_config = "";
    public $pay_method = array();

    public function __construct() {
        parent::__construct();
        $this->setting        = $this->setting_model->get()[0];
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->load->model(array('gateway_ins_model'));
    }
  
    public function index() {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
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

        $payment_details=$this->session->userdata("params");
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $this->load->view('onlineappointment/twocheckout/index', $data);
    }
 
    public function pay(){
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
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

        $payment_details=$this->session->userdata("params");
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['currency']            = $this->setting['currency'];

        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');

        if ($this->form_validation->run() == false) {
            $data['amount'] = $total;
            $this->load->view('onlineappointment/twocheckout/index', $data);
        } else {
            $data['currency']=$data['currency'];
            $data['amount'] =number_format((float)($total), 2, '.', '');
            $data['api_config']=$this->pay_method;

            $this->load->view('onlineappointment/twocheckout/index', $data);
        } 
    }

    public function success(){
        
        $payment_data = $this->session->userdata('payment_data');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($payment_data['transaction_id'],'twocheckout');
        
        if($parameter_data['payment_status']=='success'){
            redirect(base_url("patient/pay/successinvoice/"));
        }elseif($parameter_data['payment_status']=='fail'){
            $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
           redirect(base_url("patient/pay/paymentfailed/"));
        }
    }

}