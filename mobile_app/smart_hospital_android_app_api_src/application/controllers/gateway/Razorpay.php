<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Razorpay extends Admin_Controller {
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
        $this->load->view('payment/razorpay/razorpay', $data);
    }

    public function pay(){
        $data = array();
        $payment_details=$this->session->userdata("params");
        $hospital_data=$this->setting;
        $logourl=str_replace('api/', '', base_url());
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            =>$logourl.'app',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_HTTPHEADER     => array(
                "authorization: Bearer sY6xc8tAS7Wj8-MXyXxheg",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $res_arr=json_decode($response);
        }
   
        $data['hospital_data']=$hospital_data[0];
        $setting = $this->setting[0];
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        
     
        if ($this->form_validation->run()==false) {
             
            $this->load->view('payment/razorpay/razorpay', $data);

        }else{
           
            $data['key_id']=$this->payment_method->api_publishable_key;

            $data['total']=$_POST['amount']*100;
            $data['currency'] = $data['hospital_data']['currency'];
            $data['name'] = $payment_details['name'];
            $data['theme_color']=$data['hospital_data']['app_primary_color_code'];
            $data['title'] = 'Bill Payment Smart Hospital';  
            
   
            $data['image']=$res_arr->site_url."uploads/hospital_content/logo/".$res_arr->app_logo;
          
            $this->load->view('payment/razorpay/pay', $data);

        }
    }  
  
    
    public function success($amount){
        if(isset($_POST['razorpay_payment_id']) && $_POST['razorpay_payment_id']!=''){     
        $payment_details=$this->session->userdata("params");        
         
            $transactionid=$_POST['razorpay_payment_id'];
            $save_record = array(
                'case_reference_id' => $payment_details["case_reference_id"],
                'type' => "payment",
                'amount'        => $amount/100,
                'payment_mode' => 'Online',
                'payment_date' => date('Y-m-d H:i:s'),
                'note'         => "Online fees deposit through Razorpay TXN ID: " . $transactionid,
                'patient_id'  => $payment_details['patient_id'],
            );
            if($payment_details['type'] == "opd"){
                $save_record["opd_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "ipd"){
                $save_record["ipd_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "pharmacy"){
                $save_record["pharmacy_bill_basic_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "pathology"){
                $save_record["pathology_billing_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "radiology"){
                $save_record["radiology_billing_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "blood_bank"){
                $save_record["blood_issue_id"] = $payment_details['id'];
            }elseif($payment_details['type'] == "ambulance"){
                $save_record["ambulance_call_id"] = $payment_details['id'];
            }
            $insert_id = $this->webservice_model->insertOnlinePaymentInTransactions($save_record);
            redirect(base_url("payment/successinvoice/" . $insert_id));
        }else{ 
             redirect(base_url("payment/paymentfailed"));
        }
    }

    
}

