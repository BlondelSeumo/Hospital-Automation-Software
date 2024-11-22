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
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";   
        $data['api_error']=array();
        $data['setting'] = $this->setting;  
        $this->load->view('payment/paystack/paystack', $data);  
	}

    public function pay(){
        $data = array();
        $payment_details=$this->session->userdata("params");
        $setting = $this->setting[0];
        $data = array();
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
     
        if ($this->form_validation->run()==false) {
             
            $this->load->view('payment/paystack/paystack', $data);

        }else{
            
         if(isset($data)) {
                $payment_details['post_amount']=$this->input->post('amount');
                $this->session->set_userdata("params", $payment_details);
                $result = array();
                $amount = $_POST['amount']*100;
                $ref = time();
                $callback_url = base_url().'gateway/paystack/success/'.$ref;
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
                    $this->load->view('payment/paystack/paystack', $data);
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
                        if($this->session->has_userdata('params')) {
                            $payment_data = $this->session->userdata('params');
                            $id = $payment_data['id'];
                            $data['amount'] = $result['data']['amount'];
                        }
                        $transactionid=$ref;
                        $save_record = array(
                            'case_reference_id' => $payment_data["case_reference_id"],
                            'type'              => "payment",
                            'amount'            => $payment_data['post_amount'],
                            'payment_mode'      => 'Online',
                            'payment_date'      => date('Y-m-d H:i:s'),
                            'note'              => "Online fees deposit through Paystack TXN ID: " . $transactionid,
                            'patient_id'        => $payment_data['patient_id'],
                        );
                        if($payment_data['type'] == "opd"){
                            $save_record["opd_id"] = $payment_data['id'];
                        }elseif($payment_data['type'] == "ipd"){
                            $save_record["ipd_id"] = $payment_data['id'];
                        }elseif($payment_data['type'] == "pharmacy"){
                            $save_record["pharmacy_bill_basic_id"] = $payment_data['id'];
                        }elseif($payment_data['type'] == "pathology"){
                            $save_record["pathology_billing_id"] = $payment_data['id'];
                        }elseif($payment_data['type'] == "radiology"){
                            $save_record["radiology_billing_id"] = $payment_data['id'];
                        }elseif($payment_data['type'] == "blood_bank"){
                            $save_record["blood_issue_id"] = $payment_data['id'];
                        }elseif($payment_data['type'] == "ambulance"){
                            $save_record["ambulance_call_id"] = $payment_data['id'];
                        }
                        $insert_id = $this->webservice_model->insertOnlinePaymentInTransactions($save_record);
                        redirect(base_url("payment/successinvoice/" . $insert_id));
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

