<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Jazzcash extends Patient_Controller
{

    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting = $this->setting_model->get()[0];
        $this->load->model(array('onlineappointment_model','charge_model'));
        date_default_timezone_set("Asia/Karachi");

    }

    public function index() {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting'] = $this->setting;
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
        $this->load->view('patient/onlineappointment/jazzcash/index', $data);
    } 

    public function pay(){
        $data = array();
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $amount                         = $this->session->userdata('payment_amount');
        $data['total'] = $amount;
        $amount =number_format((float)($amount), 2, '.', '');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['name'] = $appointment_data->name;
        $data['title'] = 'Online Appointment Fees';
        $data['return_url'] = base_url() . 'patient/onlineappointment/jazzcash/complete';
        $data['pp_MerchantID'] = $this->pay_method->api_secret_key;
        $data['pp_Password'] = $this->pay_method->api_password;
        $data['currency_code'] = $this->setting['currency'];
		$data['ExpiryTime'] = date('YmdHis', strtotime("+3 hours"));
		$data['TxnDateTime'] = date('YmdHis', strtotime("+0 hours"));
		$data['TxnRefNumber'] = "T". date('YmdHis');
        $input_para["pp_Version"]="2.0";
        $input_para["pp_IsRegisteredCustomer"]="Yes";
        $input_para["pp_TxnType"]="MPAY";
        $input_para["pp_TokenizedCardNumber"]="";
        $input_para["pp_CustomerID"]=time();
        $input_para["pp_CustomerEmail"]="";
        $input_para["pp_CustomerMobile"]="";
        $input_para["pp_MerchantID"]=$data['pp_MerchantID'];
        $input_para["pp_Language"]="EN";
        $input_para["pp_SubMerchantID"]="";
        $input_para["pp_Password"]=$data['pp_Password'];
        $input_para["pp_TxnRefNo"]=$data['TxnRefNumber'];
        $input_para["pp_Amount"]=$amount*100;
        $input_para["pp_DiscountedAmount"]="";
        $input_para["pp_DiscountBank"]="";
        $input_para["pp_TxnCurrency"]="PKR";
        $input_para["pp_TxnDateTime"]=$data['TxnDateTime'];
        $input_para["pp_TxnExpiryDateTime"]=$data['ExpiryTime'];
        $input_para["pp_BillReference"]=time();
        $input_para["pp_Description"]=$data['title'];
        $input_para["pp_ReturnURL"]=$data['return_url'];
        $input_para["pp_SecureHash"]="0123456789";
        $input_para["ppmpf_1"]="1";
        $input_para["ppmpf_2"]="2";
        $input_para["ppmpf_3"]="3";
        $input_para["ppmpf_4"]="4";
        $input_para["ppmpf_5"]="5";
        $data['payment_data']=$input_para;
        $this->load->view('patient/onlineappointment/jazzcash/pay', $data);
    }

    public function complete()
    {
        $appointment_id = $this->session->userdata('appointment_id');
        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $patient_data  = $this->session->userdata('patient');
        $patient_id  = $patient_data['patient_id'];
        $charge_id  = $this->session->userdata('charge_id');
        $data = array();
        if ($_POST['pp_ResponseCode'] == '000') {
            $amount              = $this->session->userdata('payment_amount');
            $gateway_response['appointment_id']   = $appointment_id; 
            $transactionid                      = $_POST['pp_TxnRefNo'];
            $payment_data = array(
                'appointment_id' => $appointment_id,
                'paid_amount'    => $amount,
                'charge_id'      => $charge_id,
                'payment_type'   => 'Online',
                'transaction_id'=>$transactionid,
                'payment_mode'   => 'Jazzcash',
                'note'           => "Payment deposit through Jazzcash TXN ID: " . $transactionid,
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


            $return_detail = $this->onlineappointment_model->paymentSuccess($payment_data,$transaction_array);
            redirect(base_url("patient/onlineappointment/checkout/successinvoice/" . $return_detail['insert_id']));

        } elseif ($_POST['pp_ResponseCode'] == '112') {
            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        } else {
            $this->session->set_flashdata('msg', $_POST['pp_ResponseMessage']);
            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }
    }

}
