<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sslcommerz extends Patient_Controller
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
            $payment_data = $this->session->userdata("payment_data");
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
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/sslcommerz/index", $data);
            $this->load->view("layout/patient/footer");
        }
    }


    public function pay()
    {
        $this->form_validation->set_rules("email", $this->lang->line("email"),"trim|required");
        $this->form_validation->set_rules("phone", $this->lang->line("phone"),"trim|required");
        $this->form_validation->set_rules("address", $this->lang->line("address"),"trim|required");
        if($this->form_validation->run() == false){
            $this->index();
        }else{
            $payment_data = $this->session->userdata("payment_data");
            $requestData=array();
            $CURLOPT_POSTFIELDS=array(
                'store_id'=>$this->pay_method->api_publishable_key,
                'store_passwd'=>$this->pay_method->api_password,
                'total_amount'=>$payment_data['deposit_amount'],
                'currency'=>$this->setting[0]["currency"],
                'tran_id'=>abs(crc32(uniqid())),
                'success_url'=>base_url().'patient/payment/sslcommerz/success',
                'fail_url'=>base_url().'patient/payment/sslcommerz/fail',
                'cancel_url'=>base_url().'patient/payment/sslcommerz/cancel',
                'cus_name'=>$this->patient_data['name'],
                'cus_email'=> $this->input->post('email'),
                'cus_add1'=> $this->input->post('address'),
                'cus_phone'=> $this->input->post('phone'),
                'cus_city'=>'',
                'cus_country'=>'',
                'multi_card_name'=>'mastercard,visacard,amexcard,internetbank,mobilebank,othercard ',
                'shipping_method'=>'NO',
                'product_name'=>'test',
                'product_category'=>'Electronic',
                'product_profile'=>'general'
            );
                $string="";
                foreach ($CURLOPT_POSTFIELDS as $key => $value) {
                    $string.=$key.'='.$value."&";
                    if($key=='product_profile'){
                    $string.=$key.'='.$value;
                    }
                } 
                //echo $string;die;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "$string");

            $headers = array();
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $response=json_decode($result);
            
            header("Location: $response->GatewayPageURL");
                $json = json_decode($response, true);
                if ($json['success']) {
                    $url   = $json['payment_request']['longurl'];
                    $array = array('status' => 'success', 'error' => '', 'location' => $url);
                } else {

                    foreach ($json['message'] as $key => $value) {
                        $error[] = $value[0] . "<br>";
                    }
                    $array = array('status' => 'fail', 'error' => $error);
                }
            echo json_encode($array);
        }
    }

    public function success($payment_type = '')
    {   
        if ($_POST['status'] == 'VALID') {
            $transactionid = $_POST['val_id']; 
            $payment_data = $this->session->userdata("payment_data");
            $save_record = array(
                'case_reference_id' => $payment_data["case_reference_id"],
                'type' => "payment",
                'amount'  => $payment_data['deposit_amount'],
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Sslcommerz TXN ID: " . $transactionid,
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
            redirect(base_url("patient/pay/paymentfailed"));
        }
    }
}