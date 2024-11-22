<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Toyyibpay extends Admin_Controller {

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

        $this->load->view('onlineappointment/toyyibpay/index', $data);
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
            $this->load->view('onlineappointment/toyyibpay/index', $data);
        } else {
            $data['name'] = $payment_details['name'];
            $amount =number_format((float)($total), 2, '.', ''); 
            $payment_data = array(
                'userSecretKey'=>$this->pay_method->api_secret_key,
                'categoryCode'=>$this->pay_method->api_signature,
                'billName'=>'Appointment',
                'billDescription'=>'Appointment Bill',
                'billPriceSetting'=>1,
                'billPayorInfo'=>1,
                'billAmount'=>$total,
                'billReturnUrl'=>base_url().'onlineappointment/toyyibpay/success',
                'billCallbackUrl'=>base_url().'gateway_ins/toyyibpay',
                'billExternalReferenceNo' => time().rand(99,999),
                'billTo'=>$data['name'],
                'billEmail'=>$_POST['email'],
                'billPhone'=>$_POST['phone'],
                'billSplitPayment'=>0,
                'billSplitPaymentArgs'=>'',
                'billPaymentChannel'=>'0',
                'billContentEmail'=>'Thank you for fees submission!',
                'billChargeToCustomer'=>1
              );  

              $curl = curl_init();
              curl_setopt($curl, CURLOPT_POST, 1);
              curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');  
              curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($curl, CURLOPT_POSTFIELDS, $payment_data);

              $result = curl_exec($curl);
              $info = curl_getinfo($curl);  
              curl_close($curl);
              $obj = json_decode($result);

            if (!empty($obj)) {
                $ins_data=array(
                    'unique_id'=>$payment_data['billExternalReferenceNo'],
                    'parameter_details'=>json_encode($payment_data),
                    'gateway_name'=>'toyyibpay',
                    'type'=>'appointment',
                    'online_appointment_id'=>$appointment_id,
                    'module_type'=>'appointment',
                    'payment_status'=>'processing',
                );

                $transactionid  = $payment_data['billExternalReferenceNo'];

                $payment_section = $this->config->item('payment_section');
                $save_record = array(
                    'amount'                 => $total,
                    'patient_id'             => $payment_details['patient_id'],
                    'section'                => $payment_section['appointment'],
                    'type'                   => 'payment',
                    'appointment_id'         => $appointment_id,
                    'payment_mode'           => "Online",
                    'note'                   => "Online fees deposit through Toyyibpay TXN ID: " . $transactionid ,
                    'payment_date'           => date('Y-m-d H:i:s'),
                    'received_by'            => 1,
                );

                $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
                $save_record["gateway_ins_id"] = $gateway_ins_id;

                $this->gateway_ins_model->add_transactions_processing($save_record);

                $this->session->set_userdata("toyyibpay_payment_id",$payment_data['billExternalReferenceNo']);
          
                if((isset($obj->status) && $obj->status=='error')){
                    $result=$obj->msg;  
                    $data['api_error'] = $result;
                   
                    $data['amount'] =number_format((float)($total), 2, '.', '');

                    $this->load->view('onlineappointment/toyyibpay/index', $data);
                }else{
                  $url = "https://dev.toyyibpay.com/".$obj[0]->BillCode;
                    header("Location: $url");
                }
            }
        }
    }

    public function success(){
        $appointment_id = $this->session->userdata('appointment_id');
        $toyyibpay_payment_id = $this->session->userdata('toyyibpay_payment_id');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($toyyibpay_payment_id,'toyyibpay');

        if($parameter_data['payment_status']=='success'){
           redirect(base_url("payment/appointmentsuccess/".$appointment_id));
        }elseif($parameter_data['payment_status']=='fail'){
            $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
            redirect(site_url('payment/paymentfailed'));
        }
    }

}