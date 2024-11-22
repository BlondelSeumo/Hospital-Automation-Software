<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mollie extends Patient_Controller
{

    public $pay_method     = array();
    public $patient_data;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->patient_data   = $this->session->userdata('patient');
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->setting        = $this->setting_model->get();
    }

    public function index() {

        $payment_data = $this->session->userdata('payment_data');
        $data['amount'] = $payment_data['deposit_amount'];
        $data['case_reference_id']   = $payment_data['case_reference_id'];
        $data['error']=array();
        $this->load->view("layout/patient/header");
        $this->load->view('patient/payment/mollie/index', $data);
        $this->load->view("layout/patient/footer");
    }

    public function pay()
    {
            $payment_data = $this->session->userdata('payment_data');
            $amount =number_format((float)($payment_data['deposit_amount']), 2, '.', '');
            $api=' '.$this->pay_method->api_publishable_key;
            $order=time();
            $setting             = $this->setting[0];
            $currency=$setting['currency'];
            $redirectUrl=base_url()."patient/payment/mollie/complete";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "amount[currency]=".$currency."&amount[value]=".$amount."&description=#".$order."&redirectUrl=".$redirectUrl);

            $headers = array();
            $headers[] = 'Authorization: Bearer'.$api;
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $json = json_decode($result, true);
            
            if ($json['status']=='open') {
                $url = $json['_links']['checkout']['href'];
                $this->session->set_userdata("mollie_payment_id", $json['id']);
                header("Location: $url");
            }else {

            $data = array();
            $json = json_decode($result, true);
           
            $error = array();

           
            $data['error']=$json['detail'];
             $payment_data = $this->session->userdata('payment_data');
        $data['amount'] = $payment_data['deposit_amount'];
        $data['case_reference_id']   = $payment_data['case_reference_id'];
      
        $this->load->view("layout/patient/header");
        $this->load->view('patient/payment/mollie/index', $data);
        $this->load->view("layout/patient/footer");
        }
    }

    /**
     * This is a callback function for movies payment completion
     */
    public function complete()
    {
        $payment_data  = $this->session->userdata('payment_data');
        $api=' '.$this->pay_method->api_publishable_key;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments/'.$this->session->userdata('mollie_payment_id'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer'.$api;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $json=json_decode($result);
        if ($json->status=='paid') {

            $payment_data  = $this->session->userdata('payment_data');
            $transactionid                      = $json->id; 

            $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $payment_data['deposit_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Mollie TXN ID: " . $transactionid,
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

            redirect(base_url("patient/pay/paymentfailed/"));
        }

    }

}