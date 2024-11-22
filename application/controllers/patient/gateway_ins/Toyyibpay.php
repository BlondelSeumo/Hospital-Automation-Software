<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Toyyibpay extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('gateway_ins_model'));
    }

     public function index(){
 
        $refno=$_POST['refno'];
        $order_id=$_POST['order_id'];
        $status=$_POST['status'];
        if($status==1){
            $para_amount=$this->gateway_ins_model->get_gateway_ins($order_id,'toyyibpay');
           
            if($para_amount['module_type']=='patient_bill'){
                $get_processing_data = $this->gateway_ins_model->get($order_id,'toyyibpay');
                $transactionid = $order_id;

                $save_record = array(
                    'case_reference_id' => $get_processing_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $get_processing_data['amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Toyyibpay TXN ID: " . $transactionid,
                    'patient_id'   => $get_processing_data['patient_id'],
                    'opd_id'   => $get_processing_data['opd_id'],
                    'ipd_id'   => $get_processing_data['ipd_id'],
                    'pharmacy_bill_basic_id'   => $get_processing_data['pharmacy_bill_basic_id'],
                    'pathology_billing_id'   => $get_processing_data['pathology_billing_id'],
                    'radiology_billing_id'   => $get_processing_data['radiology_billing_id'],
                    'blood_donor_cycle_id'   => $get_processing_data['blood_donor_cycle_id'],
                    'blood_issue_id'   => $get_processing_data['blood_issue_id'],
                    'ambulance_call_id'   => $get_processing_data['ambulance_call_id'],
                );
                $insert_id = $this->payment_model->insertOnlinePaymentInTransactions($save_record);

                if($insert_id){
                    $response="success"; 
                    $this->gateway_ins_model->deleteBygateway_ins_id($para_amount['id']);  
                }else{
                    $response="quiry_failed"; 
                }
            }

            if($para_amount['module_type']=='appointment'){

               $get_processing_data = $this->gateway_ins_model->get($order_id,'toyyibpay');
               $transactionid = $order_id;
               $appointment_data = $this->gateway_ins_model->getAppointmentDetails($get_processing_data['appointment_id']);

                $save_record['appointment_id'] = $get_processing_data['appointment_id'];
                $save_record['paid_amount']    = $get_processing_data['amount'];
                $save_record['transaction_id'] = $transactionid;
                $save_record['charge_id']      = $appointment_data->charge_id;
                $save_record['payment_mode']   = 'Toyyibpay';
                $save_record['payment_type']   = 'Online';
                $save_record['note']           = "Payment deposit through Toyyibpay TXN ID: " . $transactionid;
                $save_record['date']           = date("Y-m-d H:i:s");

                $transaction_array = array(
                    'amount'                 => $get_processing_data['amount'],
                    'patient_id'             => $get_processing_data['patient_id'],
                    'section'                => $get_processing_data['section'],
                    'type'                   => 'payment',
                    'appointment_id'         => $get_processing_data['appointment_id'],
                    'payment_mode'           => "Online",
                    'note'                   => "Online fees deposit through Toyyibpay TXN ID: " . $transactionid ,
                    'payment_date'           => date('Y-m-d H:i:s'),
                    'received_by'            => 1,
                );

                $insert_id = $this->gateway_ins_model->paymentSuccess($save_record,$transaction_array);

                if($insert_id){
                    $response="success"; 
                    $this->gateway_ins_model->deleteBygateway_ins_id($para_amount['id']);  
                }else{
                    $response="quiry_failed"; 
                }
            }
        }

        $gateway_ins_response=json_encode($_POST);
        $gateway_ins_add=array('gateway_ins_id'=>$para_amount['id'],'posted_data'=>$gateway_ins_response,'response
        '=>$response);

        $this->gateway_ins_model->add_gateway_ins_response($gateway_ins_add);

        $this->gateway_ins_model->update_gateway_ins(array('id'=>$para_amount['id'],'payment_status'=>$response));
    }
}