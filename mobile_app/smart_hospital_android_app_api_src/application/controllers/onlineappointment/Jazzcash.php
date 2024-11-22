<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jazzcash extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

	public function __construct()
	{ 
		parent::__construct();
 
    $this->setting = $this->setting_model->get();
    $this->payment_method = $this->paymentsetting_model->getActiveMethod();
    date_default_timezone_set("Asia/Karachi");
	}
  

	public function index()
	{ 
        $payment_details  = $this->session->userdata("params");
        $appointment_id   = $payment_details['appointment_id'];
        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $data['setting']  = $this->setting;
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
        if(isset($charges_array->standard_charge)){
            $charge = $charges_array->standard_charge + ($charges_array->standard_charge*$charges_array->percentage/100);
        }else{
            $charge=0;
        }
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
        $total = $charge;
        $data['amount'] = $total;
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        $this->load->view('onlineappointment/jazzcash/jazzcash', $data);
	}

    public function pay()
    {
        $data = array();
        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $amount          = $this->session->userdata('payment_amount');
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
     
        if ($this->form_validation->run()==false) {
            $this->load->view('onlineappointment/jazzcash/jazzcash', $data);
        }else{
            $payment_details=$this->session->userdata('params');
            $payment_details['post_amount']=$this->input->post('amount');
            $this->session->set_userdata("params", $payment_details);
            $data['total']                         = $this->session->userdata('payment_amount');
            $data['pp_MerchantID']                 = $this->payment_method->api_secret_key;
            $data['pp_Password']                   = $this->payment_method->api_password;
            $data['currency_code']                 = $this->setting[0]['currency'];
            $data['ExpiryTime']                    = date('YmdHis', strtotime("+3 hours"));
            $data['TxnDateTime']                   = date('YmdHis', strtotime("+0 hours"));
            $data['TxnRefNumber']                  = "T" . date('YmdHis');
            $input_para["pp_Version"]              = "2.0";
            $input_para["pp_IsRegisteredCustomer"] = "Yes";
            $input_para["pp_TxnType"]              = "MPAY";
            $input_para["pp_TokenizedCardNumber"]  = "";
            $input_para["pp_CustomerID"]           = time();
            $input_para["pp_CustomerEmail"]        = '';
            $input_para["pp_CustomerMobile"]       = "";
            $input_para["pp_MerchantID"]           = $data['pp_MerchantID'];
            $input_para["pp_Language"]             = "EN";
            $input_para["pp_SubMerchantID"]        = "";
            $input_para["pp_Password"]             = $data['pp_Password'];
            $input_para["pp_TxnRefNo"]             = $data['TxnRefNumber'];
            $input_para["pp_Amount"]               = $data['total'] * 100;
            $input_para["pp_DiscountedAmount"]     = "";
            $input_para["pp_DiscountBank"]         = "";
            $input_para["pp_TxnCurrency"]          = 'PKR';
            $input_para["pp_TxnDateTime"]          = $data['TxnDateTime'];
            $input_para["pp_TxnExpiryDateTime"]    = $data['ExpiryTime'];
            $input_para["pp_BillReference"]        = time();
            $input_para["pp_Description"]          = "bill payment smart hospital";
            $input_para["pp_ReturnURL"]            = base_url().'onlineappointment/jazzcash/success';
            $input_para["pp_SecureHash"]           = "0123456789";
            $input_para["ppmpf_1"]                 = "1";
            $input_para["ppmpf_2"]                 = "2";
            $input_para["ppmpf_3"]                 = "3";
            $input_para["ppmpf_4"]                 = "4";
            $input_para["ppmpf_5"]                 = "5";
            $data['payment_data']                  = $input_para;
            $this->load->view("payment/jazzcash/pay", $data);
        }
    }
 
	
    public function success(){
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $session_data = $this->session->userdata('payment_amount');
            if ($_POST['pp_ResponseCode'] == '000') {
                $session_data = $this->session->userdata("params");
                $appointment_id   = $session_data['appointment_id'];
                $patient_id  = $session_data['patient_id'];
                $charge_id  = $this->session->userdata('charge_id');
                $transactionid = $_POST['pp_TxnRefNo'];  
                $amoun      = $this->session->userdata('payment_amount'); 
                $payment_data = array(
                    'appointment_id' => $appointment_id,
                    'paid_amount'    => $amount,
                    'charge_id'      => $charge_id,
                    'transaction_id' => $transactionid,
                    'payment_type'   => 'Online',
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
                    'note'                   => "Online fees deposit through Jazzcash TXN ID: " . $transactionid,
                    'payment_date'           => date('Y-m-d H:i:s'),
                    'received_by'            => '',
                );

                $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
                if($status){
                     redirect(base_url("payment/appointmentsuccess/".$appointment_id));
                }
            } else {
                redirect(base_url('payment/paymentfailed'));
            }
        }
    }
}

