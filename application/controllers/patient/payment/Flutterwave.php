<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Flutterwave extends Patient_Controller
{
 
    public $payment_method = array();
    public $pay_method     = array();
    public $user_data;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('Customlib');
        $this->load->model("patient_model");
        $this->patient_data   = $this->session->userdata('patient');
        $this->payment_method = $this->paymentsetting_model->get();
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->setting        = $this->setting_model->get();
        date_default_timezone_set("Asia/Karachi");
    }

    public function index()
    {
        $setting             = $this->setting[0];
        $data                = array();
        $id                  = $this->patient_data['patient_id'];
        $data["id"]          = $id;
        $data['productinfo'] = $this->lang->line('online_payment');
        if ($this->session->has_userdata('payment_data')) {
            $payment_data                = $this->session->userdata('payment_data');
            $api_publishable_key         = ($this->pay_method->api_publishable_key);
            $api_secret_key              = ($this->pay_method->api_secret_key);
            $data['api_publishable_key'] = $api_publishable_key;
            $data['api_secret_key']      = $api_secret_key;
            $data['amount']              = $payment_data['deposit_amount'];
            $data['case_reference_id']   = $payment_data['case_reference_id'];
            $data["payment_type"]        = $payment_data['payment_for'];
            $data['currency']            = $setting['currency'];
            $data['hospital_name']       = $setting['name'];
            $data['image']               = $setting['image'];
            $data["patient_data"]        = $this->patient_model->patientDetails($id);
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/flutterwave/flutterwave", $data);
            $this->load->view("layout/patient/footer");
        }
    }

    public function pay()
    {
        $this->form_validation->set_rules("email", $this->lang->line("email"),"trim|required");
        if($this->form_validation->run() == false){
            $this->index();
        }else{
            $payment_data                = $this->session->userdata('payment_data');
            $curl   = curl_init();

            $customer_email = $this->input->post("email");

            $currency = $this->setting[0]['currency'];
            $txref    = "rave" . uniqid(); // ensure you generate unique references per transaction.
            // get your public key from the dashboard.
            $PBFPubKey    = $this->pay_method->api_publishable_key;
            $redirect_url = base_url() . 'patient/payment/flutterwave/complete'; // Set your own redirect URL
            curl_setopt_array($curl, array(
                CURLOPT_URL            => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => "POST",
                CURLOPT_POSTFIELDS     => json_encode([
                    'amount'         => $payment_data['deposit_amount'],
                    'customer_email' => $customer_email,
                    'currency'       => $currency,
                    'txref'          => $txref,
                    'PBFPubKey'      => $PBFPubKey,
                    'redirect_url'   => $redirect_url,
                ]),
                CURLOPT_HTTPHEADER     => [
                    "content-type: application/json",
                    "cache-control: no-cache",
                ],
            ));

            $response = curl_exec($curl);
            $err      = curl_error($curl);

            if ($err) {
                // there was an error contacting the rave API
                die('Curl returned error: ' . $err);
            }

            $transaction = json_decode($response);

           if ($transaction->status=='error' || (!$transaction->data && !$transaction->data->link)) {
                // there was an error from the API
               
                $setting             = $this->setting[0];
        $data                = array();
        $id                  = $this->patient_data['patient_id'];
        $data["id"]          = $id;
        $data['productinfo'] = $this->lang->line('online_payment');
        if ($this->session->has_userdata('payment_data')) {
            $payment_data                = $this->session->userdata('payment_data');
            $api_publishable_key         = ($this->pay_method->api_publishable_key);
            $api_secret_key              = ($this->pay_method->api_secret_key);
            $data['api_publishable_key'] = $api_publishable_key;
            $data['api_secret_key']      = $api_secret_key;
            $data['amount']              = $payment_data['deposit_amount'];
            $data['case_reference_id']   = $payment_data['case_reference_id'];
            $data["payment_type"]        = $payment_data['payment_for'];
            $data['currency']            = $setting['currency'];
            $data['hospital_name']       = $setting['name'];
            $data['image']               = $setting['image'];
            $data["patient_data"]        = $this->patient_model->patientDetails($id);
            $data['error']               =$transaction->message;
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/flutterwave/flutterwave", $data);
            $this->load->view("layout/patient/footer");
            }
}else{
   // redirect to page so User can pay

            header('Location: ' . $transaction->data->link); 
}
            
        }
    }

    public function complete() {
        $details = $this->paymentsetting_model->getActiveMethod();
        $api_secret_key = $details->api_secret_key;
       if (isset($_GET['txref'])) {
        $ref = $_GET['txref'];
        $query = array(
            "SECKEY" => $api_secret_key,
            "txref" => $ref
        );

        $data_string = json_encode($query);
                
        $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                              
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($ch);
  
        $resp = json_decode($response, true);
       
        if($resp['data']['code']=='NO_TX'){
             redirect(base_url("patient/pay/paymentfailed"));
        }else{
           $paymentStatus = $resp['data']['status'];
        $chargeResponsecode = $resp['data']['chargecode'];
        $chargeAmount = $resp['data']['amount'];
        $chargeCurrency = $resp['data']['currency'];
        $txn_id= $resp['data']['txref'];
        
        $payment_data = $this->session->userdata('payment_data');

        $amount=$payment_data["deposit_amount"];
        if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($chargeAmount == $amount)  && ($chargeCurrency == $this->setting[0]['currency'])) {
          // transaction was successful...
          // please check other things like whether you already gave value for this ref
          // if the email matches the customer who owns the product etc
          //Give Value and return to Success page
            $save_record = array(
                'case_reference_id'=> $payment_data["case_reference_id"],
                'type'=> "payment",
                'amount'=> $payment_data['deposit_amount'],
                'payment_mode' => 'Online',
                'payment_date'=> date('Y-m-d H:i:s'),
                'note'=> "Online fees deposit through Flutterwave TXN ID: " . $txn_id,
                 'patient_id'        => $this->patient_data['patient_id'],
            );
            if($payment_data['payment_for'] == "opd"){
                $save_record["opd_id"] = $payment_data['id'];
            }elseif($payment_data['payment_for'] == "ipd"){
                $save_record["ipd_id"] = $payment_data['id'];
            }elseif($payment_data['payment_for'] == "pharmacy"){
                $save_record["pharmacy_bill_basic_id"] = $payment_data['id'];
            }elseif($payment_data['payment_for'] == "pathology"){
                $save_record["pathology_billing_id"] = $payment_data['id'];
            }elseif($payment_data['payment_for'] == "radiology"){
                $save_record["radiology_billing_id"] = $payment_data['id'];
            }elseif($payment_data['payment_for'] == "blood_bank"){
                $save_record["blood_donor_cycle_id"] = $payment_data["donor_cycle_id"];
                $save_record["blood_issue_id"] = $payment_data['id'];
            }elseif($payment_data['payment_for'] == "ambulance"){
                $save_record["ambulance_call_id"] = $payment_data['id'];
            }
            $insert_id = $this->payment_model->insertOnlinePaymentInTransactions($save_record);
            redirect(base_url("patient/pay/successinvoice/"));
             
        } else {
            redirect(base_url("patient/pay/paymentfailed"));
        } 
        }
        
    }
        else {
      die('No reference supplied');
    }
    }

}
