<?php

defined('BASEPATH') or exit('No direct script access allowed');

use \Firebase\JWT\JWT;

require_once APPPATH . 'third_party/omnipay/vendor/autoload.php';

class Zoom_api
{
    public $CI;
    private $zoom_api_key    = '';
    private $zoom_api_secret = '';

    public function __construct($parameters = array())
    {
        $this->REDIRECT_URI = base_url() . 'admin/zoom_conference/generatetoken';
        $this->REDIRECT_STAFF_URI = base_url() . 'admin/zoom_conference/stafftoken';
        $this->CLIENT = new \GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);

        $this->CI = &get_instance();
        if (!empty($parameters)) {
            $this->zoom_api_key    = $parameters['zoom_api_key'];
            $this->zoom_api_secret = $parameters['zoom_api_secret'];
            if ($this->zoom_api_key == "" && $this->zoom_api_secret == "") {
                $setting_result        = $this->CI->setting_model->getzoomsetting();
                $this->zoom_api_key    = $setting_result->zoom_api_key;
                $this->zoom_api_secret = $setting_result->zoom_api_secret;
            }
        }
    }

    public function token($code, $redirect = "Admin")
    {
        $msg = [];

        try {

            $response = $this->CLIENT->request('POST', '/oauth/token', [
                "headers" => [
                    "Authorization" => "Basic " . base64_encode($this->zoom_api_key . ':' . $this->zoom_api_secret)
                ],
                'form_params' => [
                    "grant_type" => "authorization_code",
                    "code" => $code,
                    "redirect_uri" => ($redirect == "Admin") ? $this->REDIRECT_URI : $this->REDIRECT_STAFF_URI
                ],
            ]);

            $token = json_decode($response->getBody()->getContents(), true);
            $msg = ['status' => 1, 'msg' => "Access token generated successfully.", 'token' => json_encode($token)];
        } catch (Exception $e) {
            $msg = ['status' => 0, 'msg' => 'Access token invalid or missing'];
        }

        return $msg;
    }

    public function getRefreshToken()
    {        
        if(!empty($this->CI->session->zoom_access_token)){
          $arr_token = json_decode($this->CI->session->zoom_access_token);
        $refreshToken = $arr_token->refresh_token;

        try {
            $response = $this->CLIENT->request('POST', '/oauth/token', [
                "headers" => [
                    "Authorization" => "Basic " . base64_encode($this->zoom_api_key . ':' . $this->zoom_api_secret)
                ],
                'form_params' => [
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refreshToken
                ],
            ]);
            $response_token = json_decode($response->getBody()->getContents(), true);
            $this->CI->session->unset_userdata('zoom_access_token');
            $this->CI->session->set_userdata('zoom_access_token', json_encode($response_token));

            return true;
        } catch (\Exception $e) {
            echo 'Failed during refresh token ' . $e->getMessage();
            return false;
        }  
    }else{
        return false;
    }
        
    }

    public function oAuthUrl()
    {
        return "https://zoom.us/oauth/authorize?response_type=code&client_id={$this->zoom_api_key}&redirect_uri={$this->REDIRECT_URI}";
    }

    public function getAccessToken()
    {
      if($this->CI->session->zoom_access_token){
        $arr_token = json_decode($this->CI->session->zoom_access_token);
        return $accessToken = $arr_token->access_token;
    }else{
        return false;
    }
        
    } 

    public function createAMeeting($data = [], $user_id = 'me')
    {        
        $post_time           = $data['date'];
        $start_time          = gmdate("Y-m-d\TH:i:s", strtotime($post_time));
        $createAMeetingArray = array();
        if (!empty($data['alternative_host_ids'])) {
            if (count($data['alternative_host_ids']) > 1) {
                $alternative_host_ids = implode(",", $data['alternative_host_ids']);
            } else {
                $alternative_host_ids = $data['alternative_host_ids'][0];
            }
        }
        $createAMeetingArray['topic']      = $data['title'];
        $createAMeetingArray['agenda']     = !empty($data['agenda']) ? $data['agenda'] : "";
        $createAMeetingArray['type']       = !empty($data['type']) ? $data['type'] : 2; //Scheduled
        $createAMeetingArray['start_time'] = $start_time;
        $createAMeetingArray['timezone']   = $data['timezone'];
        $createAMeetingArray['password']   = !empty($data['password']) ? $data['password'] : "";
        $createAMeetingArray['duration']   = !empty($data['duration']) ? $data['duration'] : 60;
        $createAMeetingArray['settings']   = array(
            'join_before_host'  => !empty($data['join_before_host']) ? true : false,
            'host_video'        => !empty($data['host_video']) ? true : false,
            'participant_video' => !empty($data['client_video']) ? true : false,
            'mute_upon_entry'   => !empty($data['option_mute_participants']) ? true : false,
            'enforce_login'     => !empty($data['option_enforce_login']) ? true : false,
            'auto_recording'    => !empty($data['option_auto_recording']) ? $data['option_auto_recording'] : "none",
            'alternative_hosts' => isset($alternative_host_ids) ? $alternative_host_ids : "",
        );

        try {
            $response = $this->CLIENT->request('POST', "/v2/users/{$user_id}/meetings", [
                "headers" => [
                    "Authorization" => "Bearer " . $this->getAccessToken()
                ],
                'json' => $createAMeetingArray
            ]);

            if ($response->getStatusCode() == 201) {
                return array('status' => true, 'data' => json_decode($response->getBody(), true));
            }

            throw new Exception("Not able to find error");
        } catch (\Exception $e) {
            if ($e->getCode() == 401 && $this->getRefreshToken()) {
                return $this->createAMeeting($json, $user_id = 'me');
            }
            if ($e->getCode() == 300) {
                return array('status' => false, 'message' => 'Invalid enforce_login_domains, separate multiple domains by semicolon. A maximum of {rateLimitNumber} meetings can be created/updated for a single user in one day.');
            }
            if ($e->getCode() == 404) {
                return array('status' => false, 'message' => 'User {userId} not exist or not belong to this account.');
            }
            if ($e->getCode() != 401) {
                return array('status' => false, 'message' => $e->getMessage());
            }
            return array('status' => false, 'message' => 'Not able to refresh token');
        }
    }
	
    public function deleteMeeting($meeting_id = '', $query = [])
    {       
        try {
            $response = $this->CLIENT->request('DELETE', "/v2/meetings/{$meeting_id}", [
                "headers" => [
                    "Authorization" => "Bearer " . $this->getAccessToken()
                ],
                'query' => $query
            ]);

            if ($response->getStatusCode() == 204) {
                return array('status' => true, 'message' => 'Meeting deleted.');
            }
            throw new Exception("Not able to find error");
        } catch (\Exception $e) {
            if ($e->getCode() == 401 && $this->getRefreshToken()) {
                return $this->deleteMeeting($meeting_id, $query);
            }
            if ($e->getCode() == 400) {
                return array('status' => false, 'message' => 'User does not belong to this account or dont have access');
            }
            if ($e->getCode() == 404) {
                return array('status' => false, 'message' => 'Meeting with this '.$meeting_id.' is not found or has expired.');
            }
            if ($e->getCode() != 401) {
                return array('status' => false, 'message' => $e->getMessage());
            }
            return array('status' => false, 'message' => 'Not able to refresh token');
        }
    }

    public function getMeeting($meetingId, $user_id = 'me', $query = [])
    {      
        try {
            $response = $this->CLIENT->request('GET', "/v2/meetings/" . $meetingId, [
                "headers" => [
                    "Authorization" => "Bearer ".$this->getAccessToken()
                ],
                'query' => $query
            ]);
         
            return array('status' => true, 'data' => json_decode($response->getBody(), true));
          } catch (\Exception $e) {
            if( $e->getCode() == 401 && $this->getRefreshToken()) {
              return $this->getMeeting($meetingId,$user_id, $query);
            } else {
              return array('status' => false, 'message' => $e->getMessage());
            }
          }
    }
}
