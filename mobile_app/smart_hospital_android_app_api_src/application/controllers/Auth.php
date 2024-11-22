<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('setting_model');
    }

    public function login()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
           
            if ($check_auth_client == true) {
                $params   = json_decode(file_get_contents('php://input'), true);
                $username = $params['username'];
                $password = $params['password'];
                $app_key  = $params['deviceToken'];
                $response = $this->auth_model->login($username, $password, $app_key);                
                json_output($response['status'], $response);

            }
        }
    }
	
	public function getpatientpanelstatus()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params        = json_decode(file_get_contents('php://input'), true);                  
                    
                    $result['patient_panel'] = $this->setting_model->getpatientpanelstatus();
                    
                    json_output($response['status'], $result);
                }
            }
        }
    } 
	

}
