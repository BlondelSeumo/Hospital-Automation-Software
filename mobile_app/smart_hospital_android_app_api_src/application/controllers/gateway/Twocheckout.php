<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Twocheckout extends Admin_Controller {

    public $api_config = "";
    public $pay_method = array();

    public function __construct() {
        parent::__construct();
        $this->setting = $this->setting_model->get();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->load->model(array('gateway_ins_model'));
    }
  
    public function index() {
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['amount']=$payment_details['total'];
        
        $this->load->view('payment/twocheckout/index', $data);
    }
 
    public function pay(){
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $result = array();
        $data['currency']            = $setting['currency'];
        $data['amount']=$payment_details['total'];
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->load->view('payment/twocheckout/index', $data);
        } else {
            $payment_details['post_amount']=$this->input->post('amount');
            $this->session->set_userdata("params", $payment_details);

            $data['currency']=$data['currency'];
            $data['amount'] =number_format((float)($payment_details['post_amount']), 2, '.', '');
            $data['api_config']=$this->pay_method;

            $this->load->view('payment/twocheckout/pay', $data);
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