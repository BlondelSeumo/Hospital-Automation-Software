<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ipayafrica extends Admin_Controller {
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
      //  print_r($payment_details);die;
        $setting = $this->setting[0];
        $data = array(); 
        $data['api_error']=array();
        $data['session_params']=$payment_details;
        $data['productinfo'] = "bill payment smart hospital";
        $data['setting'] = $this->setting;
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');

        if ($this->form_validation->run()==false) {
             
        
        $this->load->view('payment/ipayafrica/index', $data);

        }else{
            
       
        if ($this->session->has_userdata('params')) {
            $api_publishable_key = ($this->payment_method->api_publishable_key);
            $api_secret_key = ($this->payment_method->api_secret_key);
            $data['api_publishable_key'] = $api_publishable_key;
            $data['api_secret_key'] = $api_secret_key;
            
        }

          $fields = array("live"=> "1",
                    "oid"=> $payment_details['id'].time(),
                    "inv"=> time(),
                    "ttl"=> $_POST['amount'],
                    "tel"=> $_POST['phone'],
                    "eml"=> $_POST['email'],
                    "vid"=> $api_publishable_key,
                    "curr"=> $payment_details['invoice']->symbol,
                    "p1"=> "airtel",
                    "p2"=> "",
                    "p3"=> "",
                    "p4"=> $_POST['amount'],
                    "cbk"=> base_url().'gateway/ipayafrica/success',
                    "cst"=> "1",
                    "crl"=> "2"
                    );
            $payment_details['post_amount']=$this->input->post('amount');
            $this->session->set_userdata("params", $payment_details);
            $datastring =  $fields['live'].$fields['oid'].$fields['inv'].$fields['ttl'].$fields['tel'].$fields['eml'].$fields['vid'].$fields['curr'].$fields['p1'].$fields['p2'].$fields['p3'].$fields['p4'].$fields['cbk'].$fields['cst'].$fields['crl'];

            $hashkey =$api_secret_key;
            $generated_hash = hash_hmac('sha1',$datastring , $hashkey);
            $data['fields']=$fields;
            $data['generated_hash']=$generated_hash;
            $this->load->view("payment/ipayafrica/pay", $data);
        }

       
	}


 
	
    public function success(){
        if(!empty($_GET['status'])){
    	   if ($this->session->has_userdata('params')) {
                $payment_data = $this->session->userdata('params');
                $data['amount'] = $payment_data['post_amount'];
            } 
            $transactionid = $_GET['txncd'];
            $save_record = array(
                'case_reference_id' => $payment_data["case_reference_id"],
                'type'              => "payment",
                'amount'            => $payment_data['post_amount'],
                'payment_mode'      => 'Online',
                'payment_date'      => date('Y-m-d H:i:s'),
                'note'              => "Online fees deposit through Ipayafrica TXN ID: " . $transactionid,
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
            redirect(site_url('payment/paymentfailed'));
        }
    }
}

