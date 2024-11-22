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
        $this->setting        = $this->setting_model->get()[0];
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->load->model(array('gateway_ins_model'));
    }


   public function index()
    {
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
       
        $this->load->view('onlineappointment/skrill/index', $data);
    }

    public function pay() {
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
           $this->load->view('onlineappointment/skrill/index', $data);
        } else {
            $data['total'] =number_format((float)($total), 2, '.', '');;
            $data['currency_name'] = $data['currency'];
            $data['name'] = $payment_details['name'];

            $payment_data['pay_to_email'] =$this->pay_method->api_email;
            $payment_data['transaction_id'] ='A'.time();
            $payment_data['return_url'] =base_url().'onlineappointment/skrill/success';
            $payment_data['cancel_url'] =base_url().'onlineappointment/skrill/cancel';
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
            'type'=>'appointment',
            'online_appointment_id'=>$appointment_id,
            'module_type'=>'appointment',
            'payment_status'=>'processing',
            );
            
            $transactionid = $payment_data['transaction_id'];

            $payment_section = $this->config->item('payment_section');
            $save_record = array(
                'amount'                 => $total,
                'patient_id'             => $payment_details['patient_id'],
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Skrill TXN ID: " . $transactionid ,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => 1,
            );

            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
            $save_record["gateway_ins_id"] = $gateway_ins_id;

            $this->gateway_ins_model->add_transactions_processing($save_record);

            $this->session->set_userdata("skrill_payment_id",$payment_data['transaction_id']);
   
            $this->load->view('onlineappointment/skrill/pay', $data);
        }
    }
 
    public function success(){
        $appointment_id = $this->session->userdata('appointment_id');
        $skrill_payment_id  = $this->session->userdata('skrill_payment_id');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($skrill_payment_id,'skrill');

        if($parameter_data['payment_status']=='success'){
            redirect(base_url("payment/appointmentsuccess/".$appointment_id));
        }elseif(($parameter_data['payment_status']=='-1') || ($parameter_data['payment_status']=='-2')){
            $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
            redirect(site_url('payment/paymentfailed'));
        }
    }

    public function cancel(){
        redirect(site_url('payment/paymentfailed'));
    }
}