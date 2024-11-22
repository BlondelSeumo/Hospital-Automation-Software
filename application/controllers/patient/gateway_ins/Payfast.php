<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payfast extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('gateway_ins_model'));
    }

     public function index(){
        
        // Tell PayFast that this page is reachable by triggering a header 200
        header( 'HTTP/1.0 200 OK' );
        flush();

        define( 'SANDBOX_MODE', true );
        $pfHost = SANDBOX_MODE ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
        // Posted variables from ITN
        $pfData = $_POST;

        // Strip any slashes in data
        foreach( $pfData as $key => $val ) {
            $pfData[$key] = stripslashes( $val );
        }

        // Convert posted variables to a string
        foreach( $pfData as $key => $val ) {
            if( $key !== 'signature' ) {
                $pfParamString .= $key .'='. urlencode( $val ) .'&';
            } else {
                break;
            }
        }
 
        $pfParamString = substr( $pfParamString, 0, -1 );
        $response="notify sent";
        $para_amount=$this->gateway_ins_model->get_gateway_ins($pfData['m_payment_id'],'payfast');
        $PayFast_details=$this->gateway_ins_model->get_gateway_details('payfast');
        $posted_parameter=json_decode($para_amount['parameter_details']);
        $get_statusByUnique_id=$this->gateway_ins_model->get_statusByUnique_id($pfData['m_payment_id'],'payfast');
        $check1 = $this->pfValidSignature($pfData, $pfParamString,$PayFast_details->salt);
        $check2 = $this->pfValidIP();
        $check3 = $this->pfValidPaymentData($posted_parameter->amount, $pfData);
        $check4 = $this->pfValidServerConfirmation($pfParamString, $pfHost);

        if($check1 && $check2 && $check4 && $check3){
        if($pfData['payment_status']=='COMPLETE'){
            $response = $pfData['payment_status'];

            if($para_amount['module_type']=='patient_bill'){                
                $get_processing_data = $this->gateway_ins_model->get($pfData['m_payment_id'],'payfast');
                $transactionid = $pfData['m_payment_id'];
                $save_record = array(
                    'case_reference_id' => $get_processing_data["case_reference_id"],
                    'type' => "payment",
                    'amount'  => $get_processing_data['amount'],
                    'payment_mode' => 'Online',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'note'         => "Online fees deposit through Payfast TXN ID: " . $transactionid,
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
                $this->payment_model->insertOnlinePaymentInTransactions($save_record);
                $this->gateway_ins_model->deleteBygateway_ins_id($get_processing_data["gateway_ins_id"]);
            }

            if($para_amount['module_type']=='appointment'){

                $get_processing_data = $this->gateway_ins_model->get($pfData['m_payment_id'],'payfast');
               $appointment_data = $this->gateway_ins_model->getAppointmentDetails($get_processing_data['appointment_id']);
                $transactionid = $pfData['m_payment_id'];
                $save_record['appointment_id'] = $get_processing_data['appointment_id'];
                $save_record['paid_amount']    = $get_processing_data['amount'];
                $save_record['transaction_id'] = $transactionid;
                $save_record['charge_id']      = $appointment_data->charge_id;
                $save_record['payment_mode']   = 'Payfast';
                $save_record['payment_type']   = 'Online';
                $save_record['note']           = "Payment deposit through Payfast TXN ID: " . $transactionid;
                $save_record['date']           = date("Y-m-d H:i:s");

                $transaction_array = array(
                    'amount'                 => $get_processing_data['amount'],
                    'patient_id'             => $get_processing_data['patient_id'],
                    'section'                => $get_processing_data['section'],
                    'type'                   => 'payment',
                    'appointment_id'         => $get_processing_data['appointment_id'],
                    'payment_mode'           => "Online",
                    'note'                   => "Patient Bill deposit through Payfast TXN ID: " . $transactionid ,
                    'payment_date'           => date('Y-m-d H:i:s'),
                    'received_by'            => 1,
                );

                $this->gateway_ins_model->paymentSuccess($save_record,$transaction_array);
                $this->gateway_ins_model->deleteBygateway_ins_id($get_processing_data["gateway_ins_id"]);
            }
        }
       
        $gateway_ins_response=json_encode($_POST);
        $gateway_ins_add=array('gateway_ins_id'=>$get_statusByUnique_id['id'],'posted_data'=>$gateway_ins_response,'response
        '=>$response);

        $this->gateway_ins_model->add_gateway_ins_response($gateway_ins_add);
        $this->gateway_ins_model->update_gateway_ins(array('id'=>$get_statusByUnique_id['id'],'payment_status'=>$response));
    }
}
 
    public function pfValidIP() {
    // Variable initialization
        $validHosts = array(
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
            );

        $validIps = [];

        foreach( $validHosts as $pfHostname ) {
            $ips = gethostbynamel( $pfHostname );
            if( $ips !== false )
                $validIps = array_merge( $validIps, $ips );
        }

        // Remove duplicates
        $validIps = array_unique( $validIps );
        $referrerIp = gethostbyname(parse_url($_SERVER['HTTP_REFERER'])['host']);
        if( in_array( $referrerIp, $validIps, true ) ) {
            return true;
        }
        return false;
    }

    public  function pfValidSignature( $pfData, $pfParamString, $pfPassphrase = null ) {
    // Calculate security signature
    if($pfPassphrase === null) {
        $tempParamString = $pfParamString;
    } else {
        $tempParamString = $pfParamString.'&passphrase='.urlencode( $pfPassphrase );
    }

    $signature = md5( $tempParamString );
    return ( $pfData['signature'] === $signature );
    }

    public function pfValidPaymentData( $cartTotal, $pfData ) {
    return !(abs((float)$cartTotal - (float)$pfData['amount_gross']) > 0.01);
    }

    public function pfValidServerConfirmation($pfParamString, $pfHost = 'sandbox.payfast.co.za', $pfProxy = null ) {
            // Use cURL (if available)
            if( in_array( 'curl', get_loaded_extensions(), true ) ) {
                // Variable initialization
                $url = 'https://'. $pfHost .'/eng/query/validate';

                // Create default cURL object
                $ch = curl_init();
            
                // Set cURL options - Use curl_setopt for greater PHP compatibility
                // Base settings
                curl_setopt( $ch, CURLOPT_USERAGENT, NULL );  // Set user agent
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );      // Return output as string rather than outputting it
                curl_setopt( $ch, CURLOPT_HEADER, false );             // Don't include header in output
                curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
                curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
                
                // Standard settings
                curl_setopt( $ch, CURLOPT_URL, $url );
                curl_setopt( $ch, CURLOPT_POST, true );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $pfParamString );
                if( !empty( $pfProxy ) )
                    curl_setopt( $ch, CURLOPT_PROXY, $pfProxy );
            
                // Execute cURL
                $response = curl_exec( $ch );
                curl_close( $ch );
                if ($response === 'VALID') {
                    return true;
                }
            }
            return false;
        }

}
