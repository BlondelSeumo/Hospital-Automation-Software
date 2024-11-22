<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Payment extends Admin_Controller
{
    public $payment_method;
    public $pay_method;
    public $setting;

    public function __construct()
    { 
        parent::__construct();
        $this->load->model(array('auth_model',  'setting_model', 'user_model', 'webservice_model','module_model','paymentsetting_model', 'pathology_model', 'radiology_model', 'bloodbank_model', 'onlineappointment_model'));
        $this->load->library('customlib');
        $this->load->library('system_notification');
        $this->payment_method = $this->paymentsetting_model->get();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->setting        = $this->setting_model->get();
        $this->payment_tables = array(
            "opd" => "opd_details",
            "ipd" => "ipd_details",
            "pharmacy" =>"pharmacy_bill_basic",
            "pathology" => "pathology_billing",
            "radiology" => "radiology_billing",
            "ambulance" => "ambulance_call",
            "blood_bank" => "blood_issue",
        );
    }
    public function index($type,$patient_id,$id)
    { 
        
        $this->session->unset_userdata("params");
        if (!empty($this->payment_method)) {
            $patient_record = $this->webservice_model->getpatientDetails($patient_id);
            if($type=='ipd'){
                $result_charge  = $this->webservice_model->getTotalCharges($id);
               
                $result_paid    = $this->webservice_model->getPaidTotal($id);
                $amount_balance = $result_charge['charge'] - $result_paid['paid_amount'];
                if($amount_balance < 0){
                    $amount_balance = '0';
                } 
            }elseif($type=='opd'){
                $payment_data = $this->webservice_model->getTotalOPDPayment($id);
                $patient_charges = $this->webservice_model->getTotalOpdCharge($id);

                $amount_balance = ($patient_charges["charge"] - $payment_data["paid_amount"]);
                
                 if($amount_balance < 0){
                    $amount_balance = '0';
                }
            }elseif($type=='pharmacy'){
                $payment_data = $this->webservice_model->getTotalPharmacyPayment($id);
                $patient_charges = $this->webservice_model->getTotalPharmacyCharge($id);
                $amount_balance = ($patient_charges["net_amount"] - $payment_data["paid_amount"]);
                if($amount_balance < 0){
                    $amount_balance = '0';
                }
            }elseif($type=='pathology'){
                $payment_data = $this->pathology_model->getTotalPathologyPayment($id);
                $patient_charges = $this->pathology_model->getTotalPathologyCharge($id);
                $amount_balance = ($patient_charges["net_amount"] - $payment_data["paid_amount"]);
                if($amount_balance < 0){
                    $amount_balance = '0';
                }
            }elseif($type=='radiology'){
                $payment_data = $this->radiology_model->getTotalRadiologyPayment($id);
                $patient_charges = $this->radiology_model->getTotalRadiologyCharge($id);
                $amount_balance = ($patient_charges["net_amount"] - $payment_data["paid_amount"]);
                if($amount_balance < 0){
                    $amount_balance = '0';
                }
            }elseif($type=='ambulance'){
                $payment_data = $this->webservice_model->getTotalAmbulancePayment($id);
                $patient_charges = $this->webservice_model->getTotalAmbulanceCharge($id);
                $amount_balance = ($patient_charges["net_amount"] - $payment_data["paid_amount"]);
                if($amount_balance < 0){
                    $amount_balance = '0';
                }
            }elseif($type=='blood_bank'){
                $payment_data = $this->bloodbank_model->getTotalBloodBankPayment($id);
                $patient_charges = $this->bloodbank_model->getTotalBloodBankCharge($id);
                $amount_balance = ($patient_charges["net_amount"] - $payment_data["paid_amount"]);
                if($amount_balance < 0){
                    $amount_balance = '0';
                }
            }

                $pay_method     = $this->paymentsetting_model->getActiveMethod();
                $payment_mode   = 'Online';
                $page                = new stdClass();
                $page->symbol        = $this->setting[0]['currency_symbol'];
                $page->currency_name = $this->setting[0]['currency'];
                $params              = array(
                    'key'                    => $pay_method->api_secret_key,
                    'api_publishable_key'    => $pay_method->api_publishable_key,
                    'invoice'                => $page,
                    'total'                  => $amount_balance,
                    'patient_id'             => $patient_id,
                    'id'                     => $id,
                    'email'                  => $patient_record['email'],
                    'mobileno'               => $patient_record['mobileno'],
                    'name'                   => $patient_record['patient_name'],
                    'payment_detail'         => $payment_mode,
                    'type'                   => $type,
                );

                $reference = $this->webservice_model->getCaseReferenceId($params["id"],$this->payment_tables[$params["type"]]);

                $params["case_reference_id"] = $reference["case_reference_id"];

                $this->session->set_userdata("params", $params);
                if ($pay_method->payment_type == "billplz") {
                    redirect(base_url("gateway/billplz"));
                }elseif($pay_method->payment_type == "ccavenue"){
                    redirect(base_url("gateway/ccavenue"));
                }elseif($pay_method->payment_type == "flutterwave"){
                    redirect(base_url("gateway/flutterwave"));
                }elseif($pay_method->payment_type == "instamojo"){
                    redirect(base_url("gateway/instamojo"));
                }elseif($pay_method->payment_type == "ipayafrica"){
                    redirect(base_url("gateway/ipayafrica"));
                }elseif($pay_method->payment_type == "jazzcash"){
                    redirect(base_url("gateway/jazzcash"));
                }elseif($pay_method->payment_type == "midtrans"){
                    redirect(base_url("gateway/midtrans"));
                }elseif($pay_method->payment_type == "paypal"){
                    redirect(base_url("gateway/paypal"));
                }elseif($pay_method->payment_type == "paytm"){
                    redirect(base_url("gateway/paytm"));
                }elseif($pay_method->payment_type == "paystack"){
                    redirect(base_url("gateway/paystack"));
                }elseif($pay_method->payment_type == "payu"){
                    redirect(base_url("gateway/payu"));
                }elseif($pay_method->payment_type == "pesapal"){
                    redirect(base_url("gateway/pesapal"));
                }elseif($pay_method->payment_type == "razorpay"){
                    redirect(base_url("gateway/razorpay"));
                }elseif($pay_method->payment_type == "sslcommerz"){
                    redirect(base_url("gateway/sslcommerz"));
                }elseif($pay_method->payment_type == "stripe"){
                    redirect(base_url("gateway/stripe"));
                }elseif($pay_method->payment_type == "walkingm"){
                    redirect(base_url("gateway/walkingm"));
                }elseif($pay_method->payment_type == "mollie"){
                    redirect(base_url("gateway/mollie"));
                }elseif($pay_method->payment_type == "cashfree"){
                    redirect(base_url("gateway/cashfree"));
                }elseif($pay_method->payment_type == "payfast"){
                    redirect(base_url("gateway/payfast"));
                }elseif($pay_method->payment_type == "toyyibpay"){
                    redirect(base_url("gateway/toyyibpay"));
                }elseif($pay_method->payment_type == "skrill"){
                    redirect(base_url("gateway/skrill"));
                }elseif($pay_method->payment_type == "twocheckout"){
                    redirect(base_url("gateway/twocheckout"));
                }elseif($pay_method->payment_type == "payhere"){
                    redirect(base_url("gateway/payhere"));
                }elseif($pay_method->payment_type == "onepay"){
                    redirect(base_url("gateway/onepay"));
                }
            }else{
                    $this->session->set_flashdata('error', 'Oops! An error occurred with this payment, Please contact to administrator');
                    $this->load->view('payment/error');
            }

    }



    public function paymentfailed()
    {
        $data = array();
        $this->load->view('payment/paymentfailed', $data);
    }

    public function successinvoice($invoice_id ="" ) {
       $student_details= $this->session->userdata('params');
     
        $data['title'] = 'Invoice';
        $setting_result = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $this->load->view('payment/invoice', $data);
    }


    public function appointment($patient_id,$appointment_id)
    {
        $appointment_id = $appointment_id;
        $patient_record = $this->webservice_model->getpatientDetails($patient_id);
        $status = $this->customlib->isAppointmentBooked($appointment_id);
        if($status){
            $this->webservice_model->deleteAppointment($appointment_id);
            echo "Slot Already Booked";
            return;
        }else{
            $this->session->set_userdata("appointment_id",$appointment_id);

        $appointment_data = $this->webservice_model->getAppointmentDetails($appointment_id);
        $data['setting'] = $this->setting;
        $charges_array = $this->webservice_model->getChargeDetailsById($appointment_data->charge_id);
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


        $payment_mode   = 'Online';
        $page                = new stdClass();
        $page->symbol        = $this->setting[0]['currency_symbol'];
        $page->currency_name = $this->setting[0]['currency'];

        $params  = array(
            'key'                    => $this->api_secret_key,
            'api_publishable_key'    => $this->api_publishable_key,
            'invoice'                => $page,
            'patient_id'             => $patient_id,
            'appointment_id'         => $appointment_id,
            'email'                  => $patient_record['email'],
            'mobileno'               => $patient_record['mobileno'],
            'name'                   => $patient_record['patient_name'],
            'payment_detail'         => $payment_mode,
        );

        $this->session->set_userdata('params',$params);
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
            $data = array();
            if (!empty($this->pay_method)) {
                if ($this->pay_method->payment_type == "payu") {
                    redirect(base_url("onlineappointment/payu"));
                } elseif ($this->pay_method->payment_type == "stripe") {
                    redirect(base_url("onlineappointment/stripe"));
                } elseif ($this->pay_method->payment_type == "ccavenue") {
                    redirect(base_url("onlineappointment/ccavenue"));
                } elseif ($this->pay_method->payment_type == "paypal") {
                    redirect(base_url("onlineappointment/paypal"));
                } elseif ($this->pay_method->payment_type == "instamojo") {
                    redirect(base_url("onlineappointment/instamojo"));
                } elseif ($this->pay_method->payment_type == "paytm") {
                    redirect(base_url("onlineappointment/paytm"));
                } elseif ($this->pay_method->payment_type == "razorpay") {
                    redirect(base_url("onlineappointment/razorpay"));
                } elseif ($this->pay_method->payment_type == "paystack") {
                    redirect(base_url("onlineappointment/paystack"));
                } elseif ($this->pay_method->payment_type == "midtrans") {
                    redirect(base_url("onlineappointment/midtrans"));
                }elseif ($this->pay_method->payment_type == "ipayafrica") {
                    redirect(base_url("onlineappointment/ipayafrica"));
                }elseif ($this->pay_method->payment_type == "jazzcash") {
                    redirect(base_url("onlineappointment/jazzcash"));
                }elseif ($this->pay_method->payment_type == "pesapal") {
                    redirect(base_url("onlineappointment/pesapal"));
                }elseif ($this->pay_method->payment_type == "flutterwave") {
                    redirect(base_url("onlineappointment/flutterwave"));
                }elseif ($this->pay_method->payment_type == "billplz") {
                    redirect(base_url("onlineappointment/billplz"));
                }elseif ($this->pay_method->payment_type == "sslcommerz") {
                    redirect(base_url("onlineappointment/sslcommerz"));
                }elseif ($this->pay_method->payment_type == "walkingm") {
                    redirect(base_url("onlineappointment/walkingm"));
                }elseif($this->pay_method->payment_type == "mollie"){
                    redirect(base_url("onlineappointment/mollie"));
                }elseif($this->pay_method->payment_type == "cashfree"){
                    redirect(base_url("onlineappointment/cashfree"));
                }elseif($this->pay_method->payment_type == "payfast"){
                    redirect(base_url("onlineappointment/payfast"));
                }elseif($this->pay_method->payment_type == "toyyibpay"){
                    redirect(base_url("onlineappointment/toyyibpay"));
                }elseif($this->pay_method->payment_type == "twocheckout"){
                    redirect(base_url("onlineappointment/twocheckout"));
                }elseif($this->pay_method->payment_type == "skrill"){
                    redirect(base_url("onlineappointment/skrill"));
                }elseif($this->pay_method->payment_type == "onepay"){
                    redirect(base_url("onlineappointment/onepay"));
                }elseif($this->pay_method->payment_type == "payhere"){
                    redirect(base_url("onlineappointment/payhere"));
                }
            }
        } 
    }

    public function appointmentsuccess($appointment_id){
        $appointment_details = $this->webservice_model->getoppDetails($appointment_id);
        
        $transaction_data = $this->webservice_model->getTransactionByAppointmentId($appointment_id);
        $appointment_payment = $this->webservice_model->getPaymentByAppointmentId($appointment_id);
        $charges = $this->webservice_model->getChargeByChargeId($appointment_payment->charge_id);  
        $apply_charge = $charges['standard_charge'] + ($charges['standard_charge']*($charges['percentage']/100));
        $opd_details = array(
            'patient_id'   => $appointment_details['patient_id'],
        );
        $visit_details = array(
            'appointment_date'  => date("Y-m-d H:i:s"),
            'opd_details_id'    => 0,
            'cons_doctor'       => $appointment_details['doctor'],
            'patient_charge_id' => null,
            'transaction_id'    => $transaction_data->id,
            'can_delete'        => 'no',
        );
        $staff_data = $this->webservice_model->getStaffByID($appointment_details['doctor']);
        $staff_name = composeStaffName($staff_data);
        $charge     = array(
            'opd_id'          => 0,
            'date'            => date('Y-m-d H:i:s'),
            'charge_id'       => $appointment_payment->charge_id,
            'qty'             => 1,
            'apply_charge'    => $charges['standard_charge'],
            'standard_charge' => $charges['standard_charge'],
            'amount'          => $appointment_payment->paid_amount,
            'created_at'      => date('Y-m-d H:i:s'),
            'note'            => $staff_name,               
            'tax'             => $charges['percentage'],
        );
        $status = $this->webservice_model->moveToOpd($opd_details,$visit_details,$charge,$appointment_id);

        // $doctor_details =$this->notificationsetting_model->getstaffDetails($appointment_details['doctor']);
        // $event_data=array(
        //     'appointment_date'=> $this->customlib->YYYYMMDDHisTodateFormat(date("Y-m-d H:i:s"), $this->customlib->getHospitalTimeFormat()),
        //     'patient_id'=>$appointment_details['patient_id'],
        //     'doctor_id'=>$appointment_details['doctor'],
        //     'doctor_name'=>composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
        //     'message'=>$appointment_details['message'],
        // );

        // $event_data['appointment_status']= $this->lang->line('approved');
        //$this->system_notification->send_system_notification('appointment_approved',$event_data);
        $this->load->view("onlineappointment/invoice");
    }

}
