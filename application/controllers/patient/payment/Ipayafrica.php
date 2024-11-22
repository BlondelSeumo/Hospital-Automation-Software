<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Ipayafrica extends Patient_Controller
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

            $payment_data                     = $this->session->userdata('payment_data');            
            $api_publishable_key         = ($this->pay_method->api_publishable_key);
            $api_secret_key              = ($this->pay_method->api_secret_key);
            $data['api_publishable_key'] = $api_publishable_key;
            $data['api_secret_key']      = $api_secret_key;
            $data['case_reference_id']   = $payment_data['case_reference_id'];
            $data['amount']              = $payment_data['deposit_amount'];
            $data['currency']            = $setting['currency'];
            $data['hospital_name']       = $setting['name'];
            $data['image']               = $setting['image'];
            $data["patient_data"]        = $this->patient_model->patientDetails($id);
        }
        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'required');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'required');
        if ($this->form_validation->run() == false) {
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/ipayafrica/index", $data);
            $this->load->view("layout/patient/footer");
         } else {

            $fields = array("live"=> "1",
                "oid"=> $id.time(),
                "inv"=> time(),
                "ttl"=> $data['amount'],
                "tel"=> $_POST["phone"],
                "eml"=> $_POST["email"],
                "vid"=> ($this->pay_method->api_publishable_key),
                "curr"=> $data['currency'],
                "p1"=> "airtel",
                "p2"=> "",
                "p3"=> "",
                "p4"=> $data['amount'],
                "cbk"=> base_url().'patient/payment/ipayafrica/success',
                "cst"=> "1",
                "crl"=> "2"
                );
        
                $datastring =  $fields['live'].$fields['oid'].$fields['inv'].$fields['ttl'].$fields['tel'].$fields['eml'].$fields['vid'].$fields['curr'].$fields['p1'].$fields['p2'].$fields['p3'].$fields['p4'].$fields['cbk'].$fields['cst'].$fields['crl'];

                $hashkey =($this->pay_method->api_secret_key);
                $generated_hash = hash_hmac('sha1',$datastring , $hashkey);
                $data['fields']=$fields;
                $data['generated_hash']=$generated_hash;

            $this->load->view("patient/payment/ipayafrica/pay",$data);
        }
    }
 
    public function success()
    {
        if(!empty($_GET['status'])){
            $transactionid = $_GET['txncd'];
            $payment_data = $this->session->userdata('payment_data');
                
                $save_record = array(
                    'case_reference_id' => $payment_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $payment_data['deposit_amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Ipayafrica TXN ID: " . $transactionid,
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
        }else{
             redirect(base_url("patient/pay/paymentfailed"));
        }
     
    }
}