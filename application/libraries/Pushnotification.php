<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class Pushnotification
{

    public $CI;

    //com.qdocs.smartschool
    private $fcmUrl = 'https://fcm.googleapis.com/v1/projects/smart-hospital-510ee/messages:send';
    private $key_file_path = APPPATH . "third_party/firebase_notification_key.json";
    private $scope = "https://www.googleapis.com/auth/firebase.messaging";
    private $token;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function send($tokens, $msg, $action = "")
    {		 
        $creadentials = new ServiceAccountCredentials('https://www.googleapis.com/auth/firebase.messaging', json_decode(file_get_contents($this->key_file_path), true));
        $this->token = $creadentials->fetchAuthToken(HttpHandlerFactory::build());
        $data = [
            'token' => $tokens,
            'title' => $msg['title'],
            'body' => $msg['body'],
            'action' => $action,
            'sound' => 'mySound',
        ];

        return $this->to($data);
    }

    public function to($data)
    {		
        $headers = [
            'Authorization: Bearer ' . $this->token['access_token'],
            'Content-Type: application/json'
        ];

        $fields = [
            'message' => [
                'token' => $data['token'],
                'data' => [
                    'title' => $data['title'],
                    'body' => $data['body']
                ]
            ]
        ];
 
        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
 
        if (!$result) {
            $error = curl_error($ch);
            $info = curl_getinfo($ch);
            die("cURL request failed, error = {$error}; info = " . print_r($info, true));
        }
        if (curl_errno($ch)) {
            echo 'error:' . curl_error($ch);
        } else {
            $tt = json_decode($result, true);           
            return $result;
        }

        curl_close($ch);
    }
}
