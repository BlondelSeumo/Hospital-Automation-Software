<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Checkout extends Patient_Controller
{
    public $pay_method;
    public $setting;

    public function __construct()
    {
        parent::__construct();
        $this->setting = $this->setting_model->get()[0];
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->load->library('system_notification');
         $this->load->library('mailsmsconf');
        $this->load->model(array('appointment_model','transaction_model','charge_model','staff_model','onlineappointment_model','conference_model'));
    }

    public function index($appointment_id)
    {
        $appointment_id = $appointment_id;       
        $status = $this->customlib->isAppointmentBooked($appointment_id);    
     

        $return_msg=json_decode($status);

        if(!$return_msg->status){
        
            $this->session->set_flashdata('msg', '<div class="alert alert-info text-left">' . $return_msg->msg . '</div>');
            $this->appointment_model->deleteAppointment($appointment_id);
            redirect('patient/dashboard/appointment');
        
        }else{
            $this->session->set_userdata("appointment_id",$appointment_id);

        $appointment_data = $this->onlineappointment_model->getAppointmentDetails($appointment_id);
        $data['setting'] = $this->setting;
        $charges_array = $this->charge_model->getChargeDetailsById($appointment_data->charge_id);
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
        $this->session->set_userdata('payment_amount',$charge);
        $this->session->set_userdata('charge_id',$appointment_data->charge_id);
            $data = array();
            if (!empty($this->pay_method)) {
                if ($this->pay_method->payment_type == "payu") {
                    redirect(base_url("patient/onlineappointment/payu"));
                } elseif ($this->pay_method->payment_type == "stripe") {
                    redirect(base_url("patient/onlineappointment/stripe"));
                } elseif ($this->pay_method->payment_type == "ccavenue") {
                    redirect(base_url("patient/onlineappointment/ccavenue"));
                } elseif ($this->pay_method->payment_type == "paypal") {
                    redirect(base_url("patient/onlineappointment/paypal"));
                } elseif ($this->pay_method->payment_type == "instamojo") {
                    redirect(base_url("patient/onlineappointment/instamojo"));
                } elseif ($this->pay_method->payment_type == "paytm") {
                    redirect(base_url("patient/onlineappointment/paytm"));
                } elseif ($this->pay_method->payment_type == "razorpay") {
                    redirect(base_url("patient/onlineappointment/razorpay"));
                } elseif ($this->pay_method->payment_type == "paystack") {
                    redirect(base_url("patient/onlineappointment/paystack"));
                } elseif ($this->pay_method->payment_type == "midtrans") {
                    redirect(base_url("patient/onlineappointment/midtrans"));
                }elseif ($this->pay_method->payment_type == "ipayafrica") {
                    redirect(base_url("patient/onlineappointment/ipayafrica"));
                }elseif ($this->pay_method->payment_type == "jazzcash") {
                    redirect(base_url("patient/onlineappointment/jazzcash"));
                }elseif ($this->pay_method->payment_type == "pesapal") {
                    redirect(base_url("patient/onlineappointment/pesapal"));
                }elseif ($this->pay_method->payment_type == "flutterwave") {
                    redirect(base_url("patient/onlineappointment/flutterwave"));
                }elseif ($this->pay_method->payment_type == "billplz") {
                    redirect(base_url("patient/onlineappointment/billplz"));
                }elseif ($this->pay_method->payment_type == "sslcommerz") {
                    redirect(base_url("patient/onlineappointment/sslcommerz"));
                }elseif ($this->pay_method->payment_type == "walkingm") {
                    redirect(base_url("patient/onlineappointment/walkingm"));
                }elseif($this->pay_method->payment_type == "mollie"){
                    redirect(base_url("patient/onlineappointment/mollie"));
                }elseif($this->pay_method->payment_type == "cashfree"){
                    redirect(base_url("patient/onlineappointment/cashfree"));
                }elseif($this->pay_method->payment_type == "payfast"){
                    redirect(base_url("patient/onlineappointment/payfast"));
                }elseif($this->pay_method->payment_type == "toyyibpay"){
                    redirect(base_url("patient/onlineappointment/toyyibpay"));
                }elseif($this->pay_method->payment_type == "twocheckout"){
                    redirect(base_url("patient/onlineappointment/twocheckout"));
                }elseif($this->pay_method->payment_type == "skrill"){
                    redirect(base_url("patient/onlineappointment/skrill"));
                }elseif($this->pay_method->payment_type == "payhere"){
                    redirect(base_url("patient/onlineappointment/payhere"));
                }elseif($this->pay_method->payment_type == "onepay"){
                    redirect(base_url("patient/onlineappointment/onepay"));
                }
            }
        } 
    }
    public function successinvoice($appointment_id){
        $appointment_details = $this->appointment_model->getDetails($appointment_id);
        $transaction_data = $this->transaction_model->getTransactionByAppointmentId($appointment_id);
        $appointment_payment = $this->appointment_model->getPaymentByAppointmentId($appointment_id);
        $charges = $this->charge_model->getChargeByChargeId($appointment_payment->charge_id);  
        $apply_charge = $charges['standard_charge'] + ($charges['standard_charge']*($charges['percentage']/100));

        $opd_details = array(
            'patient_id'   => $appointment_details['patient_id'],
        );
        $visit_details = array(
            'appointment_date'  => $appointment_details['date'],
            'opd_details_id'    => 0,
            'cons_doctor'       => $appointment_details['doctor'],
            'patient_charge_id' => null,
            'transaction_id'    => $transaction_data->id,
            'can_delete'        => 'no',
        );
        $staff_data = $this->staff_model->getStaffByID($appointment_details['doctor']);
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
        $visit_details = $this->appointment_model->moveToOpd($opd_details,$visit_details,$charge,$appointment_id);

	if($visit_details){

    // ===============
            $setting_result  = $this->setting_model->getzoomsetting();
            $opdduration     = $setting_result->opd_duration;

			if ($appointment_details['live_consult'] = 'yes') {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'         => $appointment_details['doctor'],
                    'visit_details_id' => $visit_details['visit_details_id'],
                    'title'            => 'Online consult for Checkup ID ' . $visit_details['opd_id'],
                    'date'             => $appointment_details['date'],
                    'duration'         => $opdduration,
                    'created_id'       => NULL,
                    'password'         => '',
                    'api_type'         => $api_type,
                    'host_video'       => 1,
                    'client_video'     => 1,
                    'purpose'          => 'consult',
                    'timezone'         => $this->customlib->getTimeZone(),
                    'live_consult_link'         => 0,
                );
				
                $insert_array['return_response'] = NULL;
                $conferenceid   = $this->conference_model->add($insert_array);
                $sender_details = array('patient_id' =>$appointment_details['patient_id'], 'conference_id' => $conferenceid, 'contact_no' => $appointment_details['mobileno'], 'email' => $this->input->post('email'));
                $this->mailsmsconf->mailsms('live_consult', $sender_details);       
                
            }
			//=====================
		}

        $doctor_details =$this->notificationsetting_model->getstaffDetails($appointment_details['doctor']);
        $event_data=array(
            'appointment_date'=> $this->customlib->YYYYMMDDHisTodateFormat(date("Y-m-d H:i:s"), $this->customlib->getHospitalTimeFormat()),
            'patient_id'=>$appointment_details['patient_id'],
            'doctor_id'=>$appointment_details['doctor'],
            'doctor_name'=>composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
            'message'=>$appointment_details['message'],
        );

        $event_data['appointment_status']= $this->lang->line('approved');
        $this->system_notification->send_system_notification('appointment_approved',$event_data);
        $this->load->view("patient/onlineappointment/success_invoice");
    }

    public function paymentfailed(){
        $this->load->view("patient/onlineappointment/payment_failed");
    }     

}
 