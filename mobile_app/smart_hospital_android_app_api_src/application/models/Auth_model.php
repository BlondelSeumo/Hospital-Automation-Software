<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{

    public $client_service               = "smarthospital";
    public $auth_key                     = "hospitalAdmin@";
    public $security_authentication_flag = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('user_model', 'setting_model', 'patient_model'));
    }

    public function check_auth_client()
    {
        $client_service = $this->input->get_request_header('Client-Service', true);
        $auth_key       = $this->input->get_request_header('Auth-Key', true);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
        }
    }

    public function login($username, $password, $app_key)
    {
        $this->db->select('id, username, password,role,is_active');
        $this->db->from('users');
        $this->db->where('username', $username);
        $this->db->where('password', $password);
        $this->db->limit(1);
        $q = $this->db->get();

        if ($q->num_rows() == 0) {
            return array('status' => 401, 'message' => 'Invalid Username or Password');
        } else {
            $q = $q->row();

            if ($q->is_active == "yes") {
                if ($q->role == "patient") {

                    $result = $this->user_model->read_user_information($q->id);
                    if ($result != false) {
                        $setting_result = $this->setting_model->get();

                        if ($result->role == "patient") {

                            $last_login = date('Y-m-d H:i:s');
                            $token      = $this->getToken();
                            $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));
                            $this->db->trans_start();
                            $this->db->insert('users_authentication', array('users_id' => $q->id, 'token' => $token, 'expired_at' => $expired_at));

                            $updateData = array(
                                'app_key' => $app_key,
                            );

                            $this->db->where('id', $result->user_id);
                            $this->db->update('patients', $updateData);

                            $session_data = array(
                                'id'                => $result->id,
                                'patient_id'        => $result->user_id,
                                'patient_unique_id' => $result->id,
                                'role'              => $result->role,
                                'username'          => $result->patient_name,
                                'mobile'            => $result->mobileno,
                                'email'             => $result->email,
                                'gender'            => $result->gender,
                                'address'           => $result->address,
                                'date_format'       => $setting_result[0]['date_format'],
                                'time_format'       => $setting_result[0]['time_format'],
                                'currency_symbol'   => $setting_result[0]['currency_symbol'],
                                'timezone'          => $setting_result[0]['timezone'],
                                'image'             => $result->image,
                            );
                            $this->session->set_userdata('patients', $session_data);
                            if ($this->db->trans_status() === false) {
                                $this->db->trans_rollback();

                                return array('status' => 500, 'message' => 'Internal server error.');
                            } else {
                                $this->db->trans_commit();
                                return array('status' => 200, 'message' => 'Successfully login.', 'id' => $q->id, 'token' => $token, 'role' => $q->role, 'record' => $session_data);
                            }
                        }
                    }
                }
            } else {
                return array('status' => 200, 'message' => 'Your account is disabled please contact to administrator');
            }
        }
    }

    public function getToken($randomIdLength = 10)
    {
        $token = '';
        do {
            $bytes = rand(1, $randomIdLength);
            $token .= str_replace(
                ['.', '/', '='], '', base64_encode($bytes)
            );
        } while (strlen($token) < $randomIdLength);
        return $token;
    }

    public function logout()
    {
        $users_id = $this->input->get_request_header('User-ID', true);
        $token    = $this->input->get_request_header('Authorization', true);
        $this->session->unset_userdata('patient');
        $this->session->sess_destroy();
        $this->db->where('users_id', $users_id)->where('token', $token)->delete('users_authentication');
        return array('status' => 200, 'message' => 'Successfully logout.');
    }

    public function auth()
    {
        if ($this->security_authentication_flag) {
            $users_id = $this->input->get_request_header('User-ID', true);
            $token    = $this->input->get_request_header('Authorization', true);
            $q        = $this->db->select('expired_at')->from('users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
            if ($q == "") {
                return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
            } else {
                if ($q->expired_at < date('Y-m-d H:i:s')) {
                    return json_output(401, array('status' => 401, 'message' => 'Your session has been expired.'));
                } else {
                    $updated_at = date('Y-m-d H:i:s');
                    $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));
                    $this->db->where('users_id', $users_id)->where('token', $token)->update('users_authentication', array('expired_at' => $expired_at, 'updated_at' => $updated_at));
                    return array('status' => 200, 'message' => 'Authorized.');
                }
            }
        } else {
            return array('status' => 200, 'message' => 'Authorized.');
        }
    }
    
    public function patientlogin($username, $password)
    {
        $this->db->select('user_id');
        $this->db->from('users');
        $this->db->where('username', $username);
        $this->db->where('password', $password);
        $this->db->limit(1);
        $q = $this->db->get();
        
		
        if ($q->num_rows() == 1) {
            return $q->row_array();
        } else {
            return false;
        }
    }

}
