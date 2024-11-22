<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Twocheckout extends Patient_Controller {

     public $api_config = "";

    public function __construct() {
        parent::__construct();
        $this->patient_data   = $this->session->userdata('patient');
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->setting        = $this->setting_model->get();
        $this->load->model(array('gateway_ins_model'));
    }
  
    public function index() {
        $data = array();
        $payment_data = $this->session->userdata('payment_data');
        $data['amount'] = $payment_data['deposit_amount'];
        $data['case_reference_id']   = $payment_data['case_reference_id'];
        $data['error']=array();
        $this->load->view("layout/patient/header");
        $this->load->view('patient/payment/twocheckout/index', $data);
        $this->load->view("layout/patient/footer");
    }
 
    public function pay(){
        $session_data = $this->session->userdata('payment_data');
        $data['setting'] = $this->setting[0];
        $data['api_error'] = array();
        $data['case_reference_id']   = $session_data['case_reference_id'];

        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $data['amount'] =number_format((float)($session_data['deposit_amount']), 2, '.', '');
        
            $this->load->view("layout/patient/header");
            $this->load->view('patient/payment/twocheckout/index', $data);
            $this->load->view("layout/patient/footer");

        } else {
            $data['currency']=$data['setting']['currency'];
            $data['amount'] =number_format((float)($session_data['deposit_amount']), 2, '.', '');
            $data['api_config']=$this->pay_method;

            $this->load->view("layout/patient/header");
            $this->load->view('patient/payment/twocheckout/pay', $data);
            $this->load->view("layout/patient/footer");
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