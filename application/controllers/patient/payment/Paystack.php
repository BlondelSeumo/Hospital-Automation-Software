<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Paystack extends Patient_Controller
{

    public $payment_method = array();
    public $pay_method     = array();
    public $patient_data;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('Customlib');
        $this->patient_data   = $this->session->userdata('patient');
        $this->payment_method = $this->paymentsetting_model->get();
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->setting        = $this->setting_model->get();
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
            $data['case_reference_id']   = $payment_data['case_reference_id'];
            $data['amount']              = $payment_data['deposit_amount'];
            $data["payment_type"]        = 'ipd';
            $data['currency']            = $setting['currency'];
            $data['hospital_name']       = $setting['name'];
            $data['image']               = $setting['image'];
            $data["patient_data"]        = $this->patient_model->patientDetails($id);
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/paystack/paystack", $data);
            $this->load->view("layout/patient/footer");
        }
    }
 
    public function pay()
    {
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'required');
        if ($this->form_validation->run() == false) {
            $this->index();
        } else {
                $payment_data = $this->session->userdata('payment_data');
                $data["amount"] = $payment_data["deposit_amount"];
                $result       = array();
                $amount       = $data['amount'] * 100;
                $ref          = time();
                $callback_url = base_url() . 'patient/payment/paystack/verify_payment/' . $ref;
                $postdata     = array('email' => $_POST['email'], 'amount' => $amount, "reference" => $ref, "callback_url" => $callback_url);
                $url          = "https://api.paystack.co/transaction/initialize";
                $ch           = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata)); //Post Fields
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $headers = [
                    'Authorization: Bearer ' . $this->pay_method->api_secret_key,
                    'Content-Type: application/json',
                ];
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $request = curl_exec($ch);
                curl_close($ch);
                $result = json_decode($request, true);
                if ($result['status']) {

                    $redir = $result['data']['authorization_url'];
                    header("Location: " . $redir);
                }else{
					$setting             = $this->setting[0];
					$data                = array();
					$id                  = $this->patient_data['patient_id'];
					$data["id"]          = $id;
					$data['productinfo'] = $this->lang->line('online_payment');
					if ($this->session->has_userdata('payment_data')) {
						$payment_data                = $this->session->userdata('payment_data');
                       
                        $data['patient_data']=$this->session->userdata('patient');
						$api_publishable_key         = ($this->pay_method->api_publishable_key);
						$api_secret_key              = ($this->pay_method->api_secret_key);
						$data['api_publishable_key'] = $api_publishable_key;
						$data['api_secret_key']      = $api_secret_key;
						$data['case_reference_id']   = $payment_data['case_reference_id'];
						$data['amount']              = $payment_data['deposit_amount'];
						$data["payment_type"]        = 'ipd';
						$data['currency']            = $setting['currency'];
						$data['hospital_name']       = $setting['name'];
						$data['image']               = $setting['image'];
						$data['api_error']			 = $result['message'];	
						$this->load->view("layout/patient/header");
						$this->load->view("patient/payment/paystack/paystack", $data);
						$this->load->view("layout/patient/footer");
					}	
				}
            }
    }

    public function verify_payment($ref, $type = 'ipd')
    {
        $result = array();
        $url    = 'https://api.paystack.co/transaction/verify/' . $ref;
        $ch     = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->pay_method->api_secret_key]
        );
        $request = curl_exec($ch);
        curl_close($ch);
        if ($request) {
            $result = json_decode($request, true);
            if ($result) {
                if ($result['data']) {
                    //something came in
                    if ($result['data']['status'] == 'success') {
                        $transactionid = $ref;
                        $payment_data = $this->session->userdata('payment_data');
                
                        $save_record = array(
                            'case_reference_id' => $payment_data["case_reference_id"],
                            'type' => "payment",
                            'amount'  => $payment_data['deposit_amount'],
                            'payment_mode' => 'Online',
                            'payment_date' => date('Y-m-d H:i:s'),
                            'note'         => "Online fees deposit through Paystack TXN ID: " . $transactionid,
                            'patient_id'   => $this->patient_data['patient_id'],
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
                        // the transaction was not successful, do not deliver value'
                        //uncomment this line to inspect the result, to check why it failed.
                        redirect(base_url("patient/pay/paymentfailed"));
                    }
                } else {
                    redirect(base_url("patient/pay/paymentfailed"));
                }

            } else {
                //die("Something went wrong while trying to convert the request variable to json. Uncomment the print_r command to see what is in the result variable.");
                redirect(base_url("patient/pay/paymentfailed"));
            }
        } else {
            //die("Something went wrong while executing curl. Uncomment the var_dump line above this line to see what the issue is. Please check your CURL command to make sure everything is ok");
            redirect(base_url("patient/pay/paymentfailed"));
        }
    }

}
