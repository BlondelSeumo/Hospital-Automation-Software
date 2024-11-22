<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get($id = null) {

        $this->db->select('sch_settings.*,languages.language');
       $this->db->join('languages', 'languages.id = sch_settings.lang_id');
        $this->db->from('sch_settings');
       
        if ($id != null) {
            $this->db->where('sch_settings.id', $id);
        } else {
            $this->db->order_by('sch_settings.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            $session_array = $this->session->has_userdata('session_array');
            $result = $query->result_array();            

            if ($session_array) {
                $session_array = $this->session->userdata('session_array');
                $result[0]['session_id'] = $session_array['session_id'];
                $result[0]['session'] = $session_array['session'];
            }

            return $result;
            
        }
    }
	
//====================================================================================================

    public function getSetting() {

        $this->db->select('sch_settings.*,languages.language'
        );
        $this->db->from('sch_settings');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id');
        $this->db->order_by('sch_settings.id');
        $query = $this->db->get();
        return $query->row();
    }
	
	public function getpatientpanelstatus() {
        $session_result = $this->get();
        return $session_result[0]['patient_panel'];
    }
	
    public function getCurrentSession() {
        $session_result = $this->get();
    }
	
    public function getCurrentSessionName() {
        $session_result = $this->get();
        return $session_result[0]['session'];
    }
	
    public function getStartMonth() {
        $session_result = $this->get();
        return $session_result[0]['start_month'];
    }
	
    public function getCurrentSessiondata() {
        $session_result = $this->get();
        return $session_result[0];
    }
	
    public function getCurrency() {
        $session_result = $this->get();
        return $session_result[0]['currency'];
    }
	
    public function getCurrencySymbol() {
        $session_result = $this->get();
        return $session_result[0]['currency_symbol'];
    }
	
    public function getDateYmd() {
        return date('Y-m-d');
    }
	
    public function getDateDmy() {
        return date('d-m-Y');
    }

    public function add_cronsecretkey($data, $id) {
        $this->db->where("id", $id)->update("sch_settings", $data);
    }

    public function getTemplate($type)
    {
        $this->db->select()->from('notification_setting');
        $this->db->where('notification_setting.type', $type);
        $query = $this->db->get();
        return $query->row();

    }
}
