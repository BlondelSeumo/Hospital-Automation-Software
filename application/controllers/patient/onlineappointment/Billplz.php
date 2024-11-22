       <?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Billplz extends Patient_Controller
{

    public $pay_method = "";
    public $amount = 0;

    function __construct() {
        parent::__construct();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting = $this->setting_model->get()[0];
        $this->load->config("payroll");
        $this->load->library(array('billplz_lib','mailsmsconf'));
        $this->load->model(array('onlineappointment_model','charge_model'));
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
        $this->load->view('patient/onlineappointment/billplz/index', $data);
    } 

    public function pay(){
        $data['return_url']  = base_url() . 'patient/onlineappointment/billplz/complete';
        $amount = $this->session->userdata('payment_amount');
        $data['total']       = $amount;
        $data['productinfo'] = $this->lang->line('online_payment');
        $parameter           = array(
            'title'       => $this->lang->line('online_payment'),
            'description' => $data['productinfo'],
            'amount'      =>  $data['total'] * 100,
        );

        $optional = array(
            'fixed_amount'   => 'true',
            'fixed_quantity' => 'true',
            'payment_button' => 'pay',
            'redirect_uri'   => $data['return_url'],
            'photo'          => '',
            'split_header'   => false,
            'split_payments' => array(
                ['split_payments[][email]' => ''],
                ['split_payments[][fixed_cut]' => '0'],
                ['split_payments[][variable_cut]' => ''],
                ['split_payments[][stack_order]' => '0'],
            ),
        );

        $api_key = $this->pay_method->api_secret_key;
        $pay_data = $this->billplz_lib->payment($parameter, $optional, $api_key);
      
		if($pay_data){
			$appointment_id = $this->session->userdata('appointment_id');
			$appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
            $charge = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
			$data['setting'] = $this->setting;
			$this->session->set_userdata('payment_amount',isset($charge->standard_charge)?$charge->standard_charge:0);
			$total = isset($charge->standard_charge)?$charge->standard_charge:0;
			$data['amount'] = $total;
			$pay_data = json_decode($pay_data);
           
            if(!empty($pay_data)){
            $data['api_error'] = $pay_data->error->message;
            }else{
            $data['api_error'] = "Somthing Went Wrong";
            }
			
			$this->load->view('patient/onlineappointment/billplz/index', $data);
		}
    }

    public function complete() {
    	$amount = $this->session->userdata('payment_amount');
        $appointment_id  = $this->session->userdata('appointment_id');
        $charge_id  = $this->session->userdata('charge_id');
        $data   = array();
        if ($_GET['billplz']['paid'] == 'true') {
            $payment_section = $this->config->item('payment_section');
            $transactionid                      = $_GET['billplz']['id'];
            $gateway_response['appointment_id'] = $appointment_id; 
            $gateway_response['paid_amount']    = $amount;
            $gateway_response['transaction_id'] = $transactionid;
            $gateway_response['charge_id']      = $charge_id;
            $gateway_response['payment_mode']   = 'Billplz';
            $gateway_response['payment_type']   = 'Online';
            $gateway_response['note']           = "Payment deposit through Billplz TXN ID: " . $transactionid;
            $gateway_response['date']           = date("Y-m-d H:i:s");

            $transaction_array = array(
                'amount'                 => $amount,
                'patient_id'             => $this->customlib->getPatientSessionUserID(),
                'section'                => $payment_section['appointment'],
                'type'                   => 'payment',
                'appointment_id'         => $appointment_id,
                'payment_mode'           => "Online",
                'note'                   => "Online fees deposit through Billplz TXN ID: " . $transactionid ,
                'payment_date'           => date('Y-m-d H:i:s'),
                'received_by'            => 1,
            );

            $return_detail = $this->onlineappointment_model->paymentSuccess($gateway_response,$transaction_array);

            redirect(base_url("patient/onlineappointment/checkout/successinvoice/$appointment_id"));
        } else {
            redirect(base_url("patient/onlineappointment/checkout/paymentfailed"));
        }
    }

}
