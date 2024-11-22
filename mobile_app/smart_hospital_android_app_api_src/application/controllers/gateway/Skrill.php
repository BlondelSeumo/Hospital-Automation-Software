<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Skrill extends Admin_Controller
{
    public $api_config = "";
    public $pay_method = array();

    public function __construct()
    {
        parent::__construct();
        $this->setting = $this->setting_model->get();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->load->model(array('gateway_ins_model'));
    }


   public function index()
    {
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
       
        $this->load->view('payment/skrill/index', $data);
    }

    public function pay() {
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $result = array();
        $data['currency']  = $setting['currency'];

        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');

        if ($this->form_validation->run() == false) {
           $this->load->view('payment/skrill/index', $data);
        } else {
            $payment_details['post_amount']=$this->input->post('amount');
            $this->session->set_userdata("params", $payment_details);

            $data['total'] =number_format((float)($payment_details['post_amount']), 2, '.', '');;
            $data['currency_name'] = $data['currency'];
            $data['name'] = $payment_details['name'];

            $payment_data['pay_to_email'] =$this->pay_method->api_email;
            $payment_data['transaction_id'] ='A'.time();
            $payment_data['return_url'] =base_url().'gateway/skrill/success';
            $payment_data['cancel_url'] =base_url().'gateway/skrill/cancel';
            $payment_data['status_url'] =base_url().'gateway_ins/skrill';
            $payment_data['language'] ='EN';
            $payment_data['merchant_fields'] ='customer_number,session_id';
            $payment_data['customer_number'] ='C'.time();
            $payment_data['session_ID'] ='A3D'.time();;
            $payment_data['pay_from_email'] =$_POST['email'];
            $payment_data['amount2_description'] ='';
            $payment_data['amount2'] ='';
            $payment_data['amount3_description'] ='';
            $payment_data['amount3'] ='';
            $payment_data['amount4_description'] ='';
            $payment_data['amount4'] ='';
            $payment_data['amount'] =$data['total'];
            $payment_data['currency'] =$data['currency_name'];
            $payment_data['firstname'] =$data['name'];
            $payment_data['lastname'] ='';
            $payment_data['address'] ='';
            $payment_data['postal_code'] ='';
            $payment_data['city'] ='';
            $payment_data['country'] ='';
            $payment_data['detail1_description'] ='';
            $payment_data['detail1_text'] ='';
            $payment_data['detail2_description'] ='';
            $payment_data['detail2_text'] ='';
            $payment_data['detail3_description'] ='';
            $payment_data['detail3_text'] ='';
            
            $data['form_fields']=$payment_data;

            $ins_data=array(
            'unique_id'=>$payment_data['transaction_id'],
            'parameter_details'=>json_encode($payment_data),
            'gateway_name'=>'skrill',
            'type'=>'patient_bill',
            'online_appointment_id'=>NULL,
            'module_type'=>'patient_bill',
            'payment_status'=>'processing',
            );
            
            $transactionid = $payment_data['transaction_id'];

            $save_record = array(
                'case_reference_id' => $payment_details["case_reference_id"],
                'type' => "payment",
                'amount'        => $payment_details['post_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Skrill TXN ID: " . $transactionid,
                'patient_id'  => $payment_details['patient_id'],
            );
            if($payment_details['type'] == "opd"){
                $save_record["opd_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "ipd"){
                $save_record["ipd_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "pharmacy"){
                $save_record["pharmacy_bill_basic_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "pathology"){
                $save_record["pathology_billing_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "radiology"){
                $save_record["radiology_billing_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "blood_bank"){
                $save_record["blood_issue_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "ambulance"){
                $save_record["ambulance_call_id"] = $payment_details['id'];
            }

            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
            $save_record["gateway_ins_id"] = $gateway_ins_id;

            $this->gateway_ins_model->add_transactions_processing($save_record);

            $this->session->set_userdata("skrill_payment_id",$payment_data['transaction_id']);
   
            $this->load->view('payment/skrill/pay', $data);
        }
    }
 
    public function success(){
        $skrill_payment_id  = $this->session->userdata('skrill_payment_id');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($skrill_payment_id,'skrill');

        if($parameter_data['payment_status']=='success'){
            redirect(base_url("payment/successinvoice/"));
        }elseif(($parameter_data['payment_status']=='-1') || ($parameter_data['payment_status']=='-2')){
            $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
            redirect(site_url('payment/paymentfailed'));
        }
    }

    public function cancel(){
        redirect(site_url('payment/paymentfailed'));
    }
}