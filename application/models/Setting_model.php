<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
class Setting_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getMysqlVersion() {
        $mysqlVersion = $this->db->query('SELECT VERSION() as version')->row();
        return $mysqlVersion;
    }

    public function getSqlMode() {
        $sqlMode = $this->db->query('SELECT @@sql_mode as mode')->row();
        return $sqlMode;
    }
	
    public function get($id = null)
    {
        $this->db->select('sch_settings.id,sch_settings.start_month,sch_settings.lang_id,sch_settings.languages,sch_settings.doctor_restriction,sch_settings.superadmin_restriction,sch_settings.mini_logo,sch_settings.app_logo,sch_settings.is_rtl,sch_settings.cron_secret_key, sch_settings.timezone,sch_settings.name,sch_settings.email,sch_settings.phone,languages.language,sch_settings.address,sch_settings.dise_code,sch_settings.date_format,sch_settings.time_format,sch_settings.currency,sch_settings.currency_symbol,sch_settings.credit_limit,sch_settings.opd_record_month,sch_settings.opd_record_month,sch_settings.image,sch_settings.theme,sch_settings.mobile_api_url,sch_settings.app_primary_color_code,sch_settings.app_secondary_color_code,patient_panel,sch_settings.scan_code_type,sch_settings.folder_path,sch_settings.base_url');
        $this->db->from('sch_settings');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id');
        if ($id != null) {
            $this->db->where('sch_settings.id', $id);
        } else {
            $this->db->order_by('sch_settings.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            $result = $query->result_array();
            return $result;
        }
    }

    public function getHospitalDetail($id = null)
    {
        $this->db->select('sch_settings.id,sch_settings.zoom_api_key,sch_settings.zoom_api_secret,sch_settings.lang_id,sch_settings.is_rtl,sch_settings.timezone,sch_settings.name,sch_settings.email,sch_settings.phone,languages.language,sch_settings.address,sch_settings.dise_code,sch_settings.date_format,sch_settings.time_format,sch_settings.currency,sch_settings.currency_symbol,sch_settings.image,sch_settings.credit_limit,sch_settings.opd_record_month,sch_settings.theme,sch_settings.base_url,sch_settings.folder_path,sch_settings.mini_logo,sch_settings.mobile_api_url,sch_settings.app_primary_color_code,sch_settings.app_secondary_color_code,sch_settings.app_logo,sch_settings.doctor_restriction,sch_settings.superadmin_restriction,sch_settings.patient_panel,sch_settings.scan_code_type,sch_settings.biometric,sch_settings.biometric_device'
        );
        $this->db->from('sch_settings');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id');
        $this->db->order_by('sch_settings.id');
        $query = $this->db->get();
        return $query->row();
    }

    public function getSetting()
    {
        $this->db->select('sch_settings.id,sch_settings.lang_id,sch_settings.is_rtl,sch_settings.doctor_restriction,sch_settings.superadmin_restriction,sch_settings.cron_secret_key,sch_settings.timezone,sch_settings.name,sch_settings.email,sch_settings.phone,languages.language,languages.short_code as language_code,sch_settings.address,sch_settings.dise_code,sch_settings.date_format,sch_settings.time_format,sch_settings.currency,sch_settings.currency_symbol,sch_settings.image,sch_settings.app_logo,sch_settings.credit_limit,sch_settings.credit_limit,sch_settings.opd_record_month,sch_settings.theme,sch_settings.mobile_api_url,sch_settings.app_primary_color_code,sch_settings.app_logo,sch_settings.mini_logo,sch_settings.image,sch_settings.app_secondary_color_code,sch_settings.patient_panel,sch_settings.scan_code_type'
        );
        $this->db->from('sch_settings');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id');
        $this->db->order_by('sch_settings.id');
        $query = $this->db->get();
        return $query->row();
    }

    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('sch_settings');
        $message = DELETE_RECORD_CONSTANT . " On Settings id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getzoomsetting()
    {
        $this->db->select('*');
        $this->db->from('zoom_settings');
        $this->db->order_by('zoom_settings.id');
        $query = $this->db->get();
        return $query->row();
    }

    public function addzoomdetails($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $q = $this->db->get('zoom_settings');
        if ($q->num_rows() > 0) {
            $results = $q->row();
            $this->db->where('id', $results->id);
            $this->db->update('zoom_settings', $data);            
            $message = UPDATE_RECORD_CONSTANT . " On Zoom Settings id " . $results->id;
            $action = "Update";
            $record_id = $results->id;
            $this->log($message, $record_id, $action);
            
        } else {
            $this->db->insert('zoom_settings', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Zoom Settings id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
        
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
         
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('sch_settings', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Settings id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('sch_settings', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Settings id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
        
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }            
    }

    public function getStartMonth()
    {
        $session_result = $this->get();
        return $session_result[0]['start_month'];
    }

    public function getCurrentSessiondata()
    {
        $session_result = $this->get();
        return $session_result[0];
    }

    public function getCurrency()
    {
        $session_result = $this->get();
        return $session_result[0]['currency'];
    }

    public function getCurrencySymbol()
    {
        $session_result = $this->get();
        return $session_result[0]['currency_symbol'];
    }

    public function getCurrentHospitalName()
    {
        $session_result = $this->get();
        return $session_result[0]['name'];
    }

    public function getDateYmd()
    {
        return date('Y-m-d');
    }

    public function getDateDmy()
    {
        return date('d-m-Y');
    }

    public function add_cronsecretkey($data, $id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $this->db->where("id", $id)->update("sch_settings", $data);        
        $message = UPDATE_RECORD_CONSTANT . " On Settings id " . $id;
        $action = "Update";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getLogoImage()
    {
        $query = $this->db->select('image,mini_logo')->get('sch_settings');
        return $query->row_array();
    }

    public function getTitleName()
    {
        $query = $this->db->select('name')->get('sch_settings');
        return $query->row_array();
    }

    public function getLanguage()
    {
        $query = $this->db->select('languages.language,languages.short_code')->join('languages', 'languages.id = sch_settings.lang_id')->get('sch_settings');
        return $query->row_array();
    }
    
    public function getHospitalDetails()
    {
        $query = $this->db->get('sch_settings');
        return $query->row_array();
    }
    
    public function get_stafflang($id)
    {
        $data = $this->db->select('staff.lang_id')->from('staff')->where('id', $id)->get()->row_array();
        return $data;
    }

    public function get_patientlang($id)
    {
        $data = $this->db->select('patients.lang_id')->from('patients')->where('id', $id)->get()->row_array();
        return $data;
    }

    public function getChargeMaster()
    {
        $data = $this->db->select('charge_type_master.*')->from('charge_type_master')->where('is_active', 'yes')->get()->result_array();
        return $data;
    }
}
