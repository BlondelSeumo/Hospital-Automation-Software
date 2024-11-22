<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Skrill extends Patient_Controller
{
    public $payment_method = array();
    public $pay_method     = array();
    public $patient_data;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->patient_data   = $this->session->userdata('patient');
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->setting        = $this->setting_model->get();
        $this->load->model(array('gateway_ins_model'));
    }


   public function index()
    {
        $data['api_error'] = array();
        $session_data = $this->session->userdata('payment_data');
        $data['amount'] = $session_data['deposit_amount'];
        $data['case_reference_id']   = $session_data['case_reference_id'];
        $data['error']=array();
        $this->load->view("layout/patient/header");
        $this->load->view('patient/payment/skrill/index', $data);
        $this->load->view("layout/patient/footer");
    }

    public function pay() {
        $session_data = $this->session->userdata('payment_data');
        $setting             = $this->setting[0];
        $currency=$setting['currency'];
        $data['case_reference_id']   = $session_data['case_reference_id'];
        $data['api_error'] = array();

        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['amount'] = $session_data['deposit_amount'];
            
            $this->load->view("layout/patient/header");
            $this->load->view('patient/payment/skrill/index', $data);
            $this->load->view("layout/patient/footer");
        } else {
            $data['total'] =number_format((float)($session_data['deposit_amount']), 2, '.', '');;
            $data['currency_name'] = $currency;
            $data['name'] = $this->patient_data['name'];

            $payment_data['pay_to_email'] =$this->pay_method->api_email;
            $payment_data['transaction_id'] ='A'.time();
            $payment_data['return_url'] =base_url().'patient/payment/skrill/success';
            $payment_data['cancel_url'] =base_url().'patient/payment/skrill/cancel';
            $payment_data['status_url'] =base_url().'patient/gateway_ins/skrill';
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
            'online_appointment_id'=>null,
            'module_type'=>'patient_bill',
            'payment_status'=>'processing',
            );
            
            $transactionid = $payment_data['transaction_id'];

            $save_record = array(
                'case_reference_id' => $session_data["case_reference_id"],
                'type' => "payment",
                'amount'  => $session_data['deposit_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Skrill TXN ID: " . $transactionid,
                'patient_id'   => $this->patient_data['patient_id'],
            );

            if($session_data['payment_for'] == "opd"){
                    $save_record["opd_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "ipd"){
                $save_record["ipd_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "pharmacy"){
                $save_record["pharmacy_bill_basic_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "pathology"){
                $save_record["pathology_billing_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "radiology"){
                $save_record["radiology_billing_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "blood_bank"){
                $save_record["blood_donor_cycle_id"] = $session_data["donor_cycle_id"];
                $save_record["blood_issue_id"] = $session_data['id'];
            }elseif($session_data['payment_for'] == "ambulance"){
                $save_record["ambulance_call_id"] = $session_data['id'];
            }

            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
            $save_record["gateway_ins_id"] = $gateway_ins_id;

            $this->gateway_ins_model->add_transactions_processing($save_record);

            $this->session->set_userdata("skrill_payment_id",$payment_data['transaction_id']);
   
            $this->load->view('patient/payment/skrill/pay', $data);
            
        }
    }
 
    public function success(){
        $skrill_payment_id  = $this->session->userdata('skrill_payment_id');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($skrill_payment_id,'skrill');

        if($parameter_data['payment_status']=='success'){
            redirect(base_url("patient/pay/successinvoice/"));
        }elseif(($parameter_data['payment_status']=='-1') || ($parameter_data['payment_status']=='-2')){
            $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
            redirect(base_url("patient/pay/paymentfailed/"));
        }
    }
	
	public function cancel(){
        
            redirect(base_url("patient/pay/paymentfailed/"));
       
    }
}