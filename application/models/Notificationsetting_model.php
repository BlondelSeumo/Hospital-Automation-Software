<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificationsetting_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        $this->db->select()->from('notification_setting');
        if ($id != null) {
            $this->db->where('notification_setting.id', $id);
        } else {
            $this->db->order_by('notification_setting.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }  

    public function get_system_notification($id=null){
        $this->db->select()->from('system_notification_setting');
        if ($id != null) {
            $this->db->where('system_notification_setting.id', $id);
        } else {
            $this->db->order_by('system_notification_setting.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }

    public function update($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well        
        //=======================Code Start===========================
        $this->db->where('id', $data['id']);
        $this->db->update('notification_setting', $data); 
        
        $message = UPDATE_RECORD_CONSTANT . " On Notification Setting id " . $data['id'];
        $action = "Update";
        $record_id = $data['id'];
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
    
    public function updatebatch($update_array)
    {
        //=======================Code Start===========================
        if (isset($update_array) && !empty($update_array)) {
            $this->db->update_batch('notification_setting', $update_array, 'id');
        }
        //======================Code End==============================
    }

     public function notificationupdatebatch($update_array)
    {
        //=======================Code Start===========================
        if (isset($update_array) && !empty($update_array)) {
            $this->db->update_batch('system_notification_setting', $update_array, 'id');
        }
        //======================Code End==============================
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->select()->from('notification_setting');
        $this->db->where('notification_setting.type', $data['type']);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $result = $q->row();
            $this->db->where('id', $result->id);
            $this->db->update('notification_setting', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Notification Setting id " . $result->id;
            $action = "Update";
            $record_id = $result->id;
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('notification_setting', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Notification Setting id " . $insert_id;
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
 
    public function update_system_notification($data){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $data['id']);
        $this->db->update('system_notification_setting', $data);
        $message = UPDATE_RECORD_CONSTANT . " On System Notification Setting id " . $data['id'];
        $action = "Update";
        $record_id = $data['id'];
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

    public function getSystemNotification_byevent($event){
        return $this->db->select('*')->from('system_notification_setting')->where('event',$event)->get()->row_array();
    }

    public function getpatientDetails($id){
        return  $this->db->select('patients.*')->from('patients')->where('id',$id)->get()->row_array();
    }

    public function getstaffDetails($id){
        return  $this->db->select('staff.id,staff.name,staff.surname,staff.employee_id,roles.id as role_id')->from('staff')->join("staff_roles", "staff_roles.staff_id = staff.id", "left")->join("roles", "staff_roles.role_id = roles.id", "left")->where('staff.id',$id)->get()->row_array();
    }
    
    public function getmedicineDetails($id){
        return  $this->db->select('medicine_name')->from('pharmacy')->where('id',$id)->get()->row_array();
    }

    public function getpathologyDetails($id){
        return  $this->db->select('test_name,short_name')->from('pathology')->where('id',$id)->get()->row_array();
    }

    public function getradiologyDetails($id){
        return  $this->db->select('test_name,short_name')->from('radio')->where('id',$id)->get()->row_array();
    }

    public function getmedicinedoseDetails($id){
        return  $this->db->select('dosage,medicine_category,unit.unit_name as unit')->from('medicine_dosage')->join('medicine_category','medicine_category.id=medicine_dosage.medicine_category_id')->join('unit','unit.id=medicine_dosage.units_id')->where('medicine_dosage.id',$id)->get()->row_array();
    }

    public function getmedicinecategoryDetails($id){
        return  $this->db->select('medicine_category')->from('medicine_category')->where('id',$id)->get()->row_array();
    }

    public function getPathologyBillReportByID($id){
        return $this->db->select('pathology.test_name,pathology_report.reporting_date,pathology_billing.case_reference_id,pathology_billing.doctor_id')->from('pathology')->join('pathology_report','pathology_report.pathology_id=pathology.id','left')->join('pathology_billing','pathology_billing.id=pathology_report.pathology_bill_id','left')->where('pathology_report.pathology_bill_id',$id)->get()->row_array();
    }

    public function getRadiologyBillReportByID($id){
        return $this->db->select('radio.test_name,radiology_report.reporting_date,radiology_billing.case_reference_id,radiology_billing.doctor_id')->from('radio')->join('radiology_report','radiology_report.radiology_id=radio.id','left')->join('radiology_billing','radiology_billing.id=radiology_report.radiology_bill_id','left')->where('radiology_report.radiology_bill_id',$id)->get()->row_array();
    }

    public function getchargeDetails($id){
        return  $this->db->select('name')->from('charges')->where('id',$id)->get()->row_array();
    }

    public function getdonorDetails($id){
        return  $this->db->select('blood_bank_products.name as blood_group_name,blood_donor.donor_name,contact_no,blood_donor_cycle.bag_no')->from('blood_bank_products')->join('blood_donor','blood_donor.blood_bank_product_id=blood_bank_products.id','left')->join('blood_donor_cycle','blood_donor_cycle.blood_bank_product_id=blood_bank_products.id','left')->where('blood_bank_products.id',$id)->get()->row_array();
    }

    public function getconferenceDetails($id){
        return  $this->db->select('conferences.staff_id,conferences.patient_id,conferences.ipd_id,conferences.title,conferences.date,conferences.duration,conferences.visit_details_id,conferences.purpose')->from('conferences')->where('conferences.id',$id)->get()->row_array();
    }

    public function getconferencestaffDetails($id){
        return  $this->db->select('staff.id as consult_doctor,staff.name,staff.surname,staff.employee_id,roles.name as user_type,roles.id as role_id')->from('conference_staff')->join('staff','staff.id=conference_staff.staff_id','left')->join("staff_roles", "staff_roles.staff_id = staff.id", "left")->join("roles", "staff_roles.role_id = roles.id", "left")->where('conference_staff.conference_id',$id)->get()->result_array();
    }

    public function getreferraltypeDetails($id){
        return  $this->db->select('name,prefixes_type')->from('referral_type')->where('id',$id)->get()->row_array();
    }

    public function getreferralpersonDetails($id){
        return  $this->db->select('name')->from('referral_person')->where('id',$id)->get()->row_array();
    }

    public function getvehiclemodelnoDetails($id){
        return  $this->db->select('vehicle_model')->from('vehicles')->where('id',$id)->get()->row_array();
    }

    public function getleavetypesDetails($id){
        return  $this->db->select('type')->from('leave_types')->where('id',$id)->get()->row_array();
    }

    public function getbagDetails($id){
        return  $this->db->select('blood_donor_cycle.bag_no,blood_donor_cycle.volume,charge_units.unit')->from('blood_donor_cycle')->join('charge_units','charge_units.id=blood_donor_cycle.unit','left')->where('blood_donor_cycle.id',$id)->get()->row_array();
    } 

    public function getblooddonorByID($id){
        return  $this->db->select('blood_bank_products.name as blood_group_name,blood_donor.donor_name,blood_donor.contact_no')->from('blood_donor')->join('blood_bank_products','blood_bank_products.id=blood_donor.blood_bank_product_id','left')->where('blood_donor.id',$id)->get()->row_array();
    }

    public function getstaffidByID($id){
        return  $this->db->select('staff_id')->from('staff_roles')->where('role_id',$id)->get()->result_array();
    }

    public function getstaffDetailsByrole($id){
        return  $this->db->select('staff.id,staff.name,staff.surname,staff.employee_id,roles.id as role_id')->from('staff')->join("staff_roles", "staff_roles.staff_id = staff.id", "left")->join("roles", "staff_roles.role_id = roles.id", "left")->where('roles.id',$id)->get()->result_array();
    }
}