<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paystack extends Admin_Controller {
    public $api_config = "";
    public $payment_method = array();
    public $pay_method = array();

    public function __construct()
    {
        parent::__construct();
 
    $this->setting = $this->setting_model->get();
            $this->payment_method = $this->paymentsetting_model->getActiveMethod();
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
        $this->load->view('onlineappointment/paystack/paystack', $data);
	}

    public function pay(){
        $data = array();
        $payment_details=$this->session->userdata("params");
        $data = array();
        $data['api_error']=array();
        $payment_amount          = $this->session->userdata('payment_amount');
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
        if ($this->form_validation->run()==false) {
            $this->load->view('onlineappointment/paystack/paystack', $data);
        }else{
         if(isset($data)) {
                $result = array();
                $amount = $payment_amount*100;
                $ref = time();
                $callback_url = base_url().'onlineappointment/paystack/success/'.$ref;
                $postdata =  array('email' => $_POST['email'], 'amount' => $amount,"reference" => $ref,"callback_url" => $callback_url);
                //print_r($postdata);die;
                $url = "https://api.paystack.co/transaction/initialize";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postdata));  //Post Fields
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $headers = [
                    'Authorization: Bearer '.$this->payment_method->api_secret_key,
                    'Content-Type: application/json',
                ];
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $request = curl_exec ($ch);
                curl_close ($ch);
               
                if ($request) {
                    $result = json_decode($request, true);
                }

               if ($result['status']) {
                  
                    $redir = $result['data']['authorization_url'];
                 header("Location: ".$redir);
                }else{

                    $data['session_params'] = $this->session->userdata('params');
                    $data['setting'] = $this->setting;
                    $data['api_error']=$result['message'];
                    $this->load->view('onlineappointment/paystack/paystack', $data);
                }
            }
        }
    }
 
	
    public function success($ref){
        $result = array();
        $url = 'https://api.paystack.co/transaction/verify/'.$ref;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$this->payment_method->api_secret_key]);
        $request = curl_exec($ch);
        curl_close($ch);
        
        if ($request) {
            $result = json_decode($request, true);
            if($result){
                if($result['data']){
                    //something came in
                    if($result['data']['status'] == 'success'){
                        $session_data = $this->session->userdata("params");
                        $appointment_id   = $session_data['appointment_id'];
                        $patient_id  = $session_data['patient_id'];
                        $charge_id  = $this->session->userdata('charge_id');
                        $amount   = $this->session->userdata('payment_amount');
                        $transactionid = $ref;
                        $payment_data = array(
                            'appointment_id' => $appointment_id,
                            'paid_amount'    => $amount,
                            'charge_id'      => $charge_id,
                            'transaction_id' => $transactionid,
                            'payment_type'   => 'Online',
                            'payment_mode'   => 'Paystack',
                            'note'           => "Payment deposit through Paystack TXN ID: " . $transactionid,
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
                            'note'                   => "Online fees deposit through Paystack TXN ID: " . $transactionid,
                            'payment_date'           => date('Y-m-d H:i:s'),
                            'received_by'            => '',
                        );

                        $status  = $this->webservice_model->paymentSuccess($payment_data,$transaction_array);
                        if($status){
                            redirect(base_url("payment/appointmentsuccess/".$appointment_id));
                        }
                    }else{
                        // the transaction was not successful, do not deliver value'
                        // print_r($result);  //uncomment this line to inspect the result, to check why it failed.
                        redirect(base_url("payment/paymentfailed"));
                    }
                }else{
                    //echo $result['message'];
                   redirect(base_url("payment/paymentfailed"));
                }
            }else{
                //print_r($result);
                //die("Something went wrong while trying to convert the request variable to json. Uncomment the print_r command to see what is in the result variable.");
                redirect(base_url("payment/paymentfailed"));
            }
        }else{
            //var_dump($request);
            //die("Something went wrong while executing curl. Uncomment the var_dump line above this line to see what the issue is. Please check your CURL command to make sure everything is ok");
        redirect(base_url("payment/paymentfailed"));
        }
	}
}

