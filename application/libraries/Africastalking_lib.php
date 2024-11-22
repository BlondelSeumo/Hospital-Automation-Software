<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
  
class Africastalking_lib {

    private $_CI;
    var $from; //your AUTH_KEY here
    var $api_key; //your senderId here
    var $api_username;
    function __construct($params) {
 
    	$this->from=$params['from'];
    	$this->api_key=$params['api_key'];
        $this->api_username=$params['api_username'];
        $this->_CI = & get_instance();        
    } 
     
    function sendSMS($to, $message) {
      
         $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.africastalking.com/version1/messaging');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "username=".$this->api_username."&to=".$to."&message=".$message."&from=".$this->from);

            $headers = array();
            $headers[] = 'Accept: application/json';
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $headers[] = 'Apikey:'.$this->api_key;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            }
            
            curl_close($ch);
            
       return $result;
    }

}
?>