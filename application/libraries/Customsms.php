

<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customsms {

    private $_CI;
    var $AUTH_KEY = ""; //your AUTH_KEY here
    var $senderId = ""; //your senderId here
    var $routeId = ""; //your routeId here
    var $smsContentType = ""; //your smsContentType here

    function __construct($array) {
        $this->_CI = & get_instance();
        $this->template_id = $array['templateid']; // your dlt template registered id
    }

    function sendSMS($to, $message) {
        $content = 'AUTH_KEY=' . rawurlencode($this->AUTH_KEY) .
                '&message=' . rawurlencode($message) .
                '&senderId=' . rawurlencode($this->senderId) .
                '&routeId=' . rawurlencode($this->routeId) .
                '&mobileNos=' . rawurlencode($to) .
                '&smsContentType=' . rawurlencode($this->smsContentType);
        $ch = curl_init('https://yourapiurl.com' . $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}
?>