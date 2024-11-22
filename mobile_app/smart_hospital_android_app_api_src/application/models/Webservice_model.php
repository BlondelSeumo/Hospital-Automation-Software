<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Webservice_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $CI = &get_instance();
        $CI->load->model('setting_model');
        $CI->load->model('paymentsetting_model');
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function getappdetails()
    {
        $this->db->select('sch_settings.mobile_api_url as url,sch_settings.app_primary_color_code,sch_settings.app_secondary_color_code,sch_settings.image as app_logo,languages.short_code as lang_code')->from('sch_settings');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id', "left");
        $q      = $this->db->get();
        $result = $q->row_array();
        return $result;
    }

//================================Patient_model=============================================

    public function getPatientProfile($user_id)
    {
        $this->db->select('patients.*')->from('patients');
        $this->db->where('patients.id', $user_id);
        $q = $this->db->get();             
        if ($q->num_rows() == 0) {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'Profile Not Found!');
        } else {
            $data['success'] = 1;
            $data    = $q->result_array();            
            return $data;
        }
    }

    public function getCaseReferenceId($id,$table_name){
        $result = $this->db->select("case_reference_id")
        ->where("id",$id)
        ->get($table_name)
        ->row_array();
        return $result;
    }

    public function getpatientDetails($id)
    {
        $this->db->select('patients.*')->from('patients')->where('patients.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getDataAppoint($id)
    {
        $query = $this->db->where('patients.id', $id)->get('patients');
        return $query->row_array();
    }

    public function getUser($id)
    {
        $query = $this->db->where('users.user_id', $id)->where('users.is_active', 'yes')->get('users');
        return $query->row_array();
    }

     public function getStaffByID($staff_id)
    {
        $this->db->select('staff.*');
        $this->db->from('staff');
        $this->db->where('staff.id', $staff_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getChargeByChargeId($id)
    {
        return $this->db->select('charges.*,tax_category.percentage')
            ->from('charges')
            ->join('tax_category', 'tax_category.id=charges.tax_category_id')
            ->where('charges.id', $id)
            ->get()->row_array();
    }

    public function getUserLoginDetails($patient_id)
    {
        $sql   = "SELECT users.* FROM users WHERE user_id =" . $patient_id . " and role = 'patient'";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function getDetailsbilling($id, $opdid = '')
    {
        $this->db->select('patients.*,opd_details.appointment_date,opd_details.case_type,opd_details.id as opdid,opd_details.casualty,opd_details.cons_doctor,opd_details.generated_by as generated_id,opd_details.refference,opd_details.opd_no,opd_details.known_allergies as opdknown_allergies,IF(opd_details.amount IS NULL,0,opd_details.amount) as opdamount,opd_details.height,opd_details.weight,opd_details.bp,opd_details.symptoms,opd_details.tax,opd_details.payment_mode,opd_details.note_remark,opd_details.discharged,opd_details.pulse,opd_details.temperature,opd_details.respiration,opd_details.opd_no,opd_details.live_consult,IF(opd_billing.status IS NULL,"",opd_billing.status) as status,IF(opd_billing.gross_total IS NULL,0,opd_billing.gross_total) as gross_total,IF(opd_billing.discount IS NULL,0,opd_billing.discount) as discount,opd_billing.date as discharge_date,IF(opd_billing.tax IS NULL,0,opd_billing.tax) as tax,IF(opd_billing.net_amount IS NULL,0,opd_billing.net_amount) as net_amount,opd_billing.total_amount,IF(opd_billing.other_charge IS NULL,0,opd_billing.other_charge) as other_charge,opd_billing.generated_by,opd_billing.id as bill_id,organisation.organisation_name,organisation.id as orgid,staff.id as staff_id,staff.name,staff.surname,consult_charges.standard_charge,opd_patient_charges.apply_charge,IF(visit_details.amount IS NULL,0,visit_details.amount) as visitamount,visit_details.id as visitid')->from('patients');
        $this->db->join('opd_details', 'patients.id = opd_details.patient_id', "left");
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "left");
        $this->db->join('organisation', 'organisation.id = patients.organisation', "left");
        $this->db->join('opd_billing', 'opd_details.id = opd_billing.opd_id', "left");
        $this->db->join('consult_charges', 'consult_charges.doctor=opd_details.cons_doctor', 'left');
        $this->db->join('opd_patient_charges', 'opd_details.id=opd_patient_charges.opd_id', 'left');
        $this->db->join('visit_details', 'visit_details.opd_id=opd_details.id', 'left');
        $this->db->where('patients.is_active', 'yes');
        $this->db->where('patients.id', $id);
        if ($opdid != null) {
            $this->db->where('opd_details.id', $opdid);
        }
        $query  = $this->db->get();
        $result = $query->row_array();
        if (!empty($result)) {
            $result['consultant_charges'] = $result['opdamount'] + $result['visitamount'];
            $charge                       = $this->getOPDchargesTotal($id, $opdid);
            $result['total_charges']      = $charge['apply_charge'];
            $payment                      = $this->getOPDPaidTotal($id, $opdid);
            $result['total_payment']      = $payment['paid_amount'];
            if ($result['status'] != 'paid') {
                $result['gross_total_amount'] = $charge['apply_charge'] - $payment['paid_amount'];
                $result['net_payable_amount'] = $charge['apply_charge'] - $payment['paid_amount'];
            }
        }
        return $result;
    }

    public function getOPDchargesTotal($id, $opdid)
    {
        $query = $this->db->select("IFNULL(sum(apply_charge), '0') as apply_charge")->where("opd_patient_charges.patient_id", $id)->where("opd_patient_charges.opd_id", $opdid)->get("opd_patient_charges");
        return $query->row_array();
    }

    public function getOPDPaidTotal($id, $opdid)
    {
        $query = $this->db->select("IFNULL(sum(paid_amount), '0') as paid_amount")->where("opd_payment.patient_id", $id)->where("opd_payment.opd_id", $opdid)->get("opd_payment");
        return $query->row_array();
    }

    public function getopdSummaryDetails($patientid, $ipdid)
    {
        $query = $this->db->select('discharged_summary_opd.*')
            ->where("discharged_summary_opd.opd_id", $ipdid)
            ->get("discharged_summary_opd");
        $result = $query->row_array();
        if (!empty($result)) {
            return $result;
        } else {
            return $result = "";
        }
    }

//===================== OPD Details =================================================

    public function getOPDDetails($patientid)
    {          
        $this->db->select('opd_details.id as opdid,opd_details.case_reference_id,sum(transactions.amount) as payamount,visit_details.*,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,patients.id as pid,patients.patient_name,patients.age,patients.month,patients.day,patients.dob,patients.gender,ipd_prescription_basic.id as prescription,visit_details.id as visitid')->from('opd_details');
        $this->db->join('visit_details', 'visit_details.opd_details_id=opd_details.id');
        $this->db->join('ipd_prescription_basic', 'visit_details.id=ipd_prescription_basic.visit_details_id', 'left');
        $this->db->join('transactions', 'transactions.opd_id=visit_details.opd_details_id', 'left');
        $this->db->join('staff', 'staff.id = visit_details.cons_doctor', "left");
        $this->db->join('patients', 'patients.id = opd_details.patient_id', "left");
        $this->db->where('opd_details.patient_id', $patientid);
        $this->db->where('opd_details.discharged', 'no');
        $this->db->group_by('opd_details.id', '');
        $this->db->order_by('opd_details.id', 'desc');
        $query  = $this->db->get();
        $result = $query->result_array();         
       
        foreach($result as  $key => $valuee){                  
            $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['opdid'],'opd');            
            $result[$key]['customfield'] = $customfield;  
            
            if($valuee['prescription'] != '' ){
                $result[$key]['prescription'] =   1;
            }else{
                $result[$key]['prescription'] =   0;
            }
            if($valuee['refference'] == '' ){
                $result[$key]['refference'] =  '';
            }
            if($valuee['symptoms'] == '' ){
                $result[$key]['symptoms'] =  '';
            }                             
        }           
            
        return $result;
    }   
    
    public function getVisitRechekup($visitid)
    {           
        $query = $this->db->select('visit_details.*,organisations_charges.org_charge,opd_details.id as opdid,staff.name,staff.surname,staff.employee_id')
            ->join('opd_details', 'opd_details.id = visit_details.opd_details_id')
            ->join('organisations_charges', 'organisations_charges.id = visit_details.organisation_id',"left")
            ->join('patients', 'opd_details.patient_id = patients.id')
            ->join('staff', 'visit_details.cons_doctor = staff.id')
            ->where(array('visit_details.opd_details_id' => $visitid))
            ->get('visit_details'); 

        $result = $query->result_array();

        $i      = 0;
        foreach ($result as $key => $value) {
            $visit_id = $value["id"];
            $check = $this->db->where("visit_details_id", $visit_id)->get('ipd_prescription_basic');
            if ($check->num_rows() > 0) {
                $result[$i]['prescription'] = 'yes';
            } else {
                $result[$i]['prescription'] = 'no';                 
            }
            $i++;
            
            if($value['symptoms'] == ''){
                $result[$key]['symptoms'] = '';  
            }
            if($value['refference'] == ''){
                $result[$key]['refference'] = '';  
            }
        }            
       
        foreach($result as  $key => $valuee){                  
            $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'opdrecheckup');    
            $result[$key]['customfield'] = $customfield;              
        }     
        
        return $result;        
    }

    public function getVisitDetails($id, $visitid)
    {
        $query = $this->db->select('opd_details.*,staff.name,staff.surname')
            ->join('patients', 'opd_details.patient_id = patients.id')
            ->join('staff', 'opd_details.cons_doctor = staff.id')
            ->where(array('opd_details.patient_id' => $id, 'opd_details.id' => $visitid))
            ->get('opd_details');
        return $query->result_array();
    }

    public function getVisitDetailsByOPD($id, $visitid)
    {
        $query = $this->db->select('visit_details.*,opd_details.id as opdid, staff.name,staff.surname')
            ->join('patients', 'visit_details.patient_id = patients.id')
            ->join('opd_details', 'visit_details.opd_no = opd_details.opd_no')
            ->join('staff', 'opd_details.cons_doctor = staff.id')
            ->where(array('opd_details.patient_id' => $id, 'visit_details.opd_id' => $visitid))
            ->get('visit_details');
        $result = $query->result_array();

        $i = 0;
        foreach ($result as $key => $value) {
            $opd_id = $value["id"];
            $check  = $this->db->where("visit_id", $opd_id)->get('prescription');
            if ($check->num_rows() > 0) {
                $result[$i]['prescription'] = 'yes';
            } else {
                $result[$i]['prescription'] = 'no';
            }
            $i++;
        }
        return $result;
    }

//===============================OPD  Prescription Details ========================================

    public function getopdprescription($id, $visitid = '')
    {        
        $query = $this->db->select("opd_details.*,visit_details.id as visitid,visit_details.known_allergies as any_allergies,visit_details.weight,visit_details.height,visit_details.pulse,visit_details.temperature,visit_details.symptoms,visit_details.bp,patients.*,blood_bank_products.name as blood_group_name,staff.name,staff.surname,staff.employee_id,staff.local_address,ipd_prescription_basic.ipd_id,ipd_prescription_basic.id as prescription_id,ipd_prescription_basic.date as presdate,ipd_prescription_basic.header_note,ipd_prescription_basic.footer_note,ipd_prescription_basic.finding_description,ipd_prescription_basic.is_finding_print,prescription_generate.name as generated_by_name,prescription_generate.surname as generated_by_surname,prescription_generate.employee_id as generated_by_employee_id,prescribe_by.name as prescribe_by_name,prescribe_by.surname as prescribe_by_surname,prescribe_by.employee_id as prescribe_by_employee_id, opd_details.id as opd_detail_id,staff.employee_id as doctor_id,ipd_prescription_basic.attachment,ipd_prescription_basic.attachment_name");
        $this->db->join("visit_details", "visit_details.id = ipd_prescription_basic.visit_details_id","left");
        $this->db->join("opd_details", "opd_details.id = visit_details.opd_details_id");
        $this->db->join("patients", "patients.id = opd_details.patient_id");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id',"left");
        $this->db->join("staff", "staff.id = visit_details.cons_doctor");
        $this->db->join("staff as prescription_generate", "prescription_generate.id = ipd_prescription_basic.generated_by");
        $this->db->join("staff as prescribe_by", "prescribe_by.id = ipd_prescription_basic.prescribe_by");
        $this->db->where("ipd_prescription_basic.visit_details_id", $visitid);
        $query = $this->db->get("ipd_prescription_basic");

        if ($query->num_rows() > 0) {
            $result            = $query->row();
            $result->medicines = $this->getPrescriptionMedicinesByBasicID($result->prescription_id);            
            $tests     = $this->getPrescriptionTestsByBasicID($result->prescription_id);              
                
                $result->pathology  = array();
                $result->radiology  = array();
                foreach($tests as $test_value){
                    if($test_value->pathology_id != ''){
                        $result->pathology[]  = $test_value;                        
                    }else{
                        $result->radiology[]  = $test_value;
                    }
                } 
            
            return $result;
        }
        return false;
    }

    public function getpres($id)
    {
        $query = $this->db->select("opd_details.*,patients.*,staff.name,staff.surname,staff.local_address,prescription.opd_id,prescription.id as presid")->join("opd_details", "prescription.opd_id = opd_details.id")->join("patients", "patients.id = opd_details.patient_id")->join("staff", "staff.id = opd_details.cons_doctor")->where("prescription.opd_id", $id)->get("prescription");
        return $query->row_array();
    }

    public function getpresvisit($id)
    {
        $query = $this->db->select("visit_details.*,patients.*,staff.name,staff.surname,staff.local_address,prescription.opd_id,prescription.id as presid")->join("visit_details", "prescription.visit_id = visit_details.id")->join("patients", "patients.id = visit_details.patient_id")->join("staff", "staff.id = visit_details.cons_doctor")->where("prescription.visit_id", $id)->get("prescription");
        return $query->row_array();
    }

//===============================IPD Details==================================================

    public function getBillInfo($id)
    {
        $query = $this->db->select('staff.name,staff.surname,staff.employee_id,ipd_billing.date as discharge_date')
            ->join('ipd_billing', 'staff.id = ipd_billing.generated_by')
            ->where('ipd_billing.patient_id', $id)
            ->get('staff');
        $result = $query->result_array();
        return $result;
    } 

    public function getIpdDetails($id,$ipdid)
    {
        $this->db->select('patients.*,blood_bank_products.name as blood_group_name,ipd_details.patient_old,ipd_details.id as ipdid,ipd_details.patient_id,discharge_card.    discharge_date,ipd_details.date,ipd_details.date,ipd_details.case_type,ipd_details.id as ipdid,ipd_details.casualty,ipd_details.height,ipd_details.weight,ipd_details.organisation_id,ipd_details.bp,ipd_details.cons_doctor,ipd_details.refference,ipd_details.known_allergies as ipdknown_allergies,ipd_details.case_reference_id,ipd_details.credit_limit as ipdcredit_limit,ipd_details.symptoms,ipd_details.discharged as ipd_discharge,ipd_details.bed,ipd_details.bed_group_id,ipd_details.note as ipdnote,ipd_details.bed,ipd_details.bed_group_id,ipd_details.payment_mode,ipd_details.credit_limit,ipd_details.pulse,ipd_details.temperature,ipd_details.respiration,ipd_details.   organisation_id,staff.id as staff_id,staff.name,staff.surname,staff.image as doctor_image,staff.employee_id,organisation.organisation_name,bed.name as bed_name,bed.id as bed_id,bed_group.name as bedgroup_name,floor.name as floor_name')->from('ipd_details');
        $this->db->join('patients', 'patients.id = ipd_details.patient_id', "left");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left');
        $this->db->join('discharge_card', 'ipd_details.id = discharge_card.ipd_details_id', "left");
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "inner");
        $this->db->join('organisation', 'organisation.id = ipd_details.organisation_id', "left");
        $this->db->join('bed', 'ipd_details.bed = bed.id', "left");
        $this->db->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left");
        $this->db->join('floor', 'floor.id = bed_group.floor', "left");
        $this->db->where('patients.id', $id);
        $this->db->where('ipd_details.id', $ipdid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getopdno($opdno)
    {
        $this->db->select('opd_details.*')->from('opd_details');
        $this->db->where('opd_details.opd_no', $opdno);
        $query  = $this->db->get();
        $result = $query->row_array();
        return $result;
    }     

//===========================organisation TPA ==================================================

    public function get($id = null)
    {
        $this->db->select()->from('organisation');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    } 

//==============================Prescription_model=================================================
      
    public function getIpdPrescription($ipdid, $ipdno)
    {
        $query = $this->db->select('ipd_prescription_basic.*')
            ->join('ipd_prescription_details', 'ipd_prescription_basic.id = ipd_prescription_details.basic_id')
            ->where("ipd_prescription_basic.ipd_id", $ipdid)
            ->group_by("ipd_prescription_basic.id")
            ->get('ipd_prescription_basic');
        $result = $query->result_array();
        $i      = 0;
        foreach ($result as $key => $value) {
            $result[$key]['ipd_id']      = strip_tags(str_replace(PHP_EOL, '', $ipdno));
            $result[$key]['header_note'] = strip_tags(str_replace(PHP_EOL, '', $value['header_note']));
            $result[$key]['footer_note'] = strip_tags(str_replace(PHP_EOL, '', $value['footer_note']));
            $i++;
        }
        return $result;
    } 

//=====================================Payment_model================================================
    
    public function paymentDetails($id, $ipdid)
    {
        $query = $this->db->select('payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = payment.patient_id", "left")->where("payment.patient_id", $id)->where("payment.ipd_id", $ipdid)
            ->get("payment");
        return $query->result_array();
    }  

    public function getPaymentipd($patient_id, $ipdid = '')
    {
        $query = $this->db->select("IFNULL(sum(paid_amount),'0') as payment")->where("patient_id", $patient_id)->where("ipd_id", $ipdid)->get("payment");
        return $query->row_array();
    }  

    public function getPaidTotal($ipdid)
    {
        $query = $this->db->select("IFNULL(sum(amount), '0') as paid_amount")->where("transactions.ipd_id", $ipdid)->get("transactions");
        return $query->row_array();
    }

    public function addPayment($data)
    {
        $this->db->insert("payment", $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function paymentByID($id)
    {
        $query = $this->db->select('payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = payment.patient_id")->where("payment.id", $id)
            ->get("payment");
        return $query->row();
    }

    public function insertOnlinePaymentInTransactions($transaction_data){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("transactions",$transaction_data);
        
        $insert_id = $this->db->insert_id();
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }            
    }

//======================================Charges_model==========================================

    public function getOPDCharges($visitid)
    {       
        $query = $this->db->select('patient_charges.*,charge_categories.name as charge_category_name,charge_type_master.charge_type,charges.charge_category_id,charges.standard_charge,charge_units.unit,charges.name,organisations_charges.org_charge,opd_details.patient_id')
            ->join('opd_details', 'patient_charges.opd_id = opd_details.id')
            ->join('patients', 'opd_details.patient_id = patients.id')
            ->join('charges', 'patient_charges.charge_id = charges.id')
            ->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner')
            ->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id')
            ->join('charge_units', 'charges.charge_unit_id = charge_units.id', 'left')
            ->join('visit_details', 'visit_details.opd_details_id = opd_details.id', "left")
            ->join('organisations_charges', 'organisations_charges.id = visit_details.organisation_id', "left")
            ->where('patient_charges.opd_id', $visitid)
            ->get('patient_charges');

        return $query->result_array();
    } 

    public function getTotalCharges($ipdid) {
        $query = $this->db->select("IFNULL(sum(apply_charge+(apply_charge*tax/100)),'0') as charge")->where("ipd_id", $ipdid)->get("patient_charges");
        return $query->row_array();
    }

    public function getTotalOpdCharge($visitid) {
        $query = $this->db->select("IFNULL(sum(amount),'0') as charge")
                ->where('patient_charges.opd_id', $visitid)
                ->get('patient_charges');
        return $query->row_array();
    }

    public function getOPDPayment($visit_id)
    {
        $query = $this->db->select('transactions.*,patients.id as pid,patients.note as pnote')
            ->join("opd_details", "opd_details.id = transactions.opd_id and opd_details.case_reference_id=transactions.case_reference_id")
            ->join("patients", "patients.id = opd_details.patient_id")
            ->where("transactions.opd_id", $visit_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        $result = $query->result_array();
        
        foreach($result as  $key => $valuee){  
            if(empty($valuee['cheque_no'])){
                $result[$key]['cheque_no'] = '';
            }
            if(empty($valuee['cheque_date'])){
                $result[$key]['cheque_date'] = '';
            }
            
            if(empty($valuee['note'])){
                $result[$key]['note'] = '';
            }  
                      
        } 
        return $result;
        
    } 

    public function getTotalOPDPayment($visit_id)
    {
        $query = $this->db->select("sum(amount) as paid_amount")
            ->join("opd_details", "opd_details.id = transactions.opd_id")
            ->where("transactions.opd_id", $visit_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->row_array();
    } 

//=========================Pharmacy Model========================================================   

    public function getAllBillDetailsPharma($id)
    {		
		$sql = "SELECT pharmacy_bill_detail.*,medicine_batch_details.expiry,medicine_batch_details.pharmacy_id,medicine_batch_details.batch_no,medicine_batch_details.tax,pharmacy.medicine_name,pharmacy.unit,pharmacy.id as `medicine_id`,pharmacy.medicine_category_id,medicine_category.medicine_category,unit.unit_name as `unit_name` FROM `pharmacy_bill_detail` INNER JOIN medicine_batch_details on medicine_batch_details.id=pharmacy_bill_detail.medicine_batch_detail_id INNER JOIN pharmacy on pharmacy.id= medicine_batch_details.pharmacy_id 
        LEFT JOIN unit on unit.id= pharmacy.unit  INNER JOIN medicine_category on medicine_category.id= pharmacy.medicine_category_id WHERE pharmacy_bill_basic_id =" . $this->db->escape($id);
        $query = $this->db->query($sql);
        return $query->result_array();
    }	

    public function getTotalPharmacyPayment($pharmacy_id)
    {
        $query = $this->db->select("sum(amount) as paid_amount")
            ->join("pharmacy_bill_basic", "pharmacy_bill_basic.id = transactions.pharmacy_bill_basic_id")
            ->where("transactions.pharmacy_bill_basic_id", $pharmacy_id)
            ->get("transactions");
        return $query->row_array();
    } 

    public function getTotalPharmacyCharge($pharmacy_id){
        $query = $this->db->select("net_amount")
                ->where('pharmacy_bill_basic.id', $pharmacy_id)
                ->get('pharmacy_bill_basic');
        return $query->row_array();
    }

//=====================Pathology model============================================================

    public function getAllBillDetailsPatho($id)
    {
        $query = $this->db->select('pathology_report.*,pathology.test_name,pathology.short_name,pathology.report_days,pathology.charge_id')
            ->join('pathology', 'pathology.id = pathology_report.pathology_id')
            ->where('pathology_report.patient_id', $id)
            ->get('pathology_report');
        return $query->result_array();
    }

    public function getparameterDetailspatho($id)
    {      
        $query          = $this->db->select('pathology_billing.*,blood_bank_products.name as blood_group_name,IFNULL((SELECT SUM(amount) FROM transactions WHERE pathology_billing_id=pathology_billing.id),0) as total_deposit,patients.patient_name,patients.id as patient_unique_id,patients.dob,patients.age,patients.month,patients.day,patients.gender,patients.blood_group,patients.mobileno,patients.email,patients.address,staff.name,staff.surname,staff.employee_id,transactions.payment_mode,transactions.amount,transactions.cheque_no,transactions.cheque_date,transactions.note as `transaction_note`')
            ->join('patients', 'pathology_billing.patient_id = patients.id')
            ->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id', 'left')
            ->join('staff', 'staff.id = pathology_billing.generated_by')
            ->join('transactions', 'transactions.id = pathology_billing.transaction_id', 'left')
            ->where("pathology_billing.id", $id)
            ->get('pathology_billing');
        if ($query->num_rows() > 0) {
            $result                       = $query->row();
            $result->{'pathology_report'} = $this->getReportByBillId($result->id);           
            
            return $result;
        }
        return false;
    }

    public function getReportByBillId($id)
    {
        $custom_fields = $this->customfield_model->getcustomfields('pathologytest');
        $custom_field_column_array = array();
        $field_var_array           = array();
        $this->db->join('pathology', 'pathology_report.pathology_id = pathology.id');
        $i = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'pathology.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable = implode(',', $field_var_array);

        $query = $this->db->select('pathology_report.*,pathology.test_name,pathology.short_name,pathology.report_days,pathology.id as pid,pathology.charge_id as cid,staff.name,staff.surname,charges.charge_category_id,charges.name,charges.standard_charge,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`,' . $field_variable)
            ->join('pathology_billing', 'pathology_report.pathology_bill_id = pathology_billing.id')
            ->join('staff', 'staff.id = pathology_billing.doctor_id', "left")
            ->join('staff as collection_specialist_staff', 'collection_specialist_staff.id = pathology_report.collection_specialist', "left")
            ->join('staff as approved_by_staff', 'approved_by_staff.id = pathology_report.approved_by', "left")
            ->join('charges', 'pathology.charge_id = charges.id')
            ->where('pathology_report.pathology_bill_id', $id)
            ->get('pathology_report');
        
        $result = $query->result_array();         
      
        foreach($result as  $key => $valuee){  
            if(empty($valuee['collection_date'])){
                $result[$key]['collection_date'] = '';
            }
            if(empty($valuee['approved_by_staff_name'])){
                $result[$key]['approved_by_staff_name'] = '';
            }
            if(empty($valuee['approved_by_staff_surname'])){
                $result[$key]['approved_by_staff_surname'] = '';
            } 
            if(empty($valuee['approved_by_staff_employee_id'])){
                $result[$key]['approved_by_staff_employee_id'] = '';
            } 
            if(empty($valuee['pathology_report'])){
                $result[$key]['pathology_report'] = '';
            } 
            if(empty($valuee['pathology_result'])){
                $result[$key]['pathology_result'] = '';
            }                
        }           
        return $result;        
    }

//================================ Radiology model ==================================================

    public function getAllBillDetailsRadio($id)
    {
        $query = $this->db->select('radiology_report.*,radio.test_name,radio.short_name,radio.report_days,radio.charge_id')
            ->join('radio', 'radio.id = radiology_report.radiology_id')
            ->where('radiology_report.id', $id)
            ->get('radiology_report');
        return $query->result_array();
    }

//==================================== operationtheatre_model ========================================

    public function getBillDetailsOt($id)
    {
        $this->db->select('operation_theatre.*,patients.patient_name,patients.patient_unique_id,staff.name as doctor_name,staff.surname as doctor_surname');
        $this->db->join('patients', 'patients.id = operation_theatre.patient_id');
        $this->db->join('staff', 'staff.id = operation_theatre.consultant_doctor', "inner");
        $this->db->where('operation_theatre.patient_id', $id);
        $this->db->order_by("operation_theatre.bill_no", "desc");
        $query = $this->db->get('operation_theatre');
        return $query->result_array();
    }

    public function getAllBillDetailsOt($id)
    {
        $query = $this->db->select('operation_theatre.*')
            ->where('operation_theatre.id', $id)
            ->get('operation_theatre');
        return $query->result_array();
    }

//===================================Ambulance========================================================

    public function getBillDetailsAmbulance($patient_id)
    {               
		$query = $this->db->select('ambulance_call.*,IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.ambulance_call_id=ambulance_call.id ),0) as `paid_amount`, vehicles.vehicle_no,vehicles.vehicle_model,vehicles.driver_name,vehicles.driver_contact,patients.patient_name as patient,patients.mobileno,patients.address,charges.name as charge_name,charge_categories.name as charge_category_name')
            ->join('transactions', 'transactions.ambulance_call_id = ambulance_call.id','left')
            ->join('vehicles', 'vehicles.id = ambulance_call.vehicle_id', 'left')
            ->join('patients', 'patients.id = ambulance_call.patient_id', 'left')
			->join('charges', 'charges.id = ambulance_call.charge_id', 'left')
            ->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'left')
			
            ->where('ambulance_call.patient_id', $patient_id)
            ->group_by('ambulance_call.id')
            ->get('ambulance_call');        
		
        $result = $query->result_array();    
	 
         if(!empty($result)){   
            foreach($result as  $key => $valuee){ 
            
                if(!empty($valuee['id'])){
                    if(empty($valuee['case_reference_id'])){
                        $result[$key]['case_reference_id'] = '';
                    }
                    if(empty($valuee['paid_amount'])){
                        $result[$key]['paid_amount'] = '';
                    }
                }
            
                $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'ambulance_call');    
                $result[$key]['customfield'] = $customfield;              
            }    
       
            return $result;
         }else{
            return array();
         }
    }

    public function getTotalAmbulancePayment($ambulance_call_id){
        $query = $this->db->select("sum(transactions.amount) as paid_amount")
            ->join("ambulance_call", "ambulance_call.id = transactions.ambulance_call_id")
            ->where("transactions.ambulance_call_id", $ambulance_call_id)
            ->get("transactions");
        return $query->row_array();
    }

    public function getTotalAmbulanceCharge($ambulance_call_id){
        $query = $this->db->select("net_amount")
            ->where('ambulance_call.id', $ambulance_call_id)
            ->get('ambulance_call');
        return $query->row_array();
    }

//=================================== Appointment Model ===========================================

    public function getAppointment($id)
    {        
         
        $this->db->select('appointment.*,specialist.specialist_name,staff.id as sid,staff.name,staff.surname,staff.employee_id,patients.id as pid,appoint_priority.appoint_priority as priorityname,appointment_queue.position as appointment_serial_no');
        $this->db->join('staff', 'appointment.doctor = staff.id', "inner");
        $this->db->join('patients', 'appointment.patient_id = patients.id', 'inner');
        $this->db->join('specialist', 'specialist.id = appointment.specialist', 'left');
        $this->db->join('appoint_priority', 'appoint_priority.id = appointment.priority', "left");		
		$this->db->join('appointment_queue', 'appointment_queue.appointment_id = appointment.id', "left");		
        $this->db->where('`appointment`.`doctor`=`staff`.`id`');
        $this->db->where('appointment.patient_id = patients.id');
        $this->db->where('appointment.patient_id=' . $id);
        $this->db->order_by("appointment.id","desc");
        $query = $this->db->get('appointment');
        
        $result = $query->result_array();         
       
            foreach($result as  $key => $valuee){                  
                $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'appointment');  
                $result[$key]['customfield'] = $customfield;
                if($valuee['specialist_name'] == ''){
                    $result[$key]['specialist_name'] = ''; 
                }
                if($valuee['priorityname'] == ''){
                    $result[$key]['priorityname'] = ''; 
                }
                if($valuee['priority'] == ''){
                    $result[$key]['priority'] = ''; 
                }
            }           
            
        return $result;
    }

    public function getAppointmentbydate($id, $date_from, $date_to)
    {
        $this->db->select('appointment.*,staff.id as sid,staff.name,staff.surname,patients.id as pid');
        $this->db->join('staff', 'appointment.doctor = staff.id', "LEFT");
        $this->db->join('patients', 'appointment.patient_id = patients.id', 'LEFT');
        $this->db->where('appointment.patient_id=' . $id);
        $this->db->where('date between "' . $date_from . '" AND "' . $date_to . ' 23:59:59"');
        $query  = $this->db->get('appointment');
        $result = $query->result_array();
        foreach ($result as $key => $value) {
            $result[$key]['date_list'] = date('Y-m-d', strtotime($value['date']));
        }
        return $result;
    }

    public function addAppointment($data,$post_custom_field=array())
    {
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("appointment", $data);
        } else {
            $this->db->insert("appointment", $data);
             $insert_id=  $this->db->insert_id();
            if(!empty($post_custom_field)){
                foreach ($post_custom_field as $custom_key => $custom_value) {
                   $post_custom_field[$custom_key]['belong_table_id']= $insert_id;
                }
                $this->db->insert_batch('custom_field_values', $post_custom_field); 
            }

        }
    }

    public function deleteAppointment($id)
    {
        $query = $this->db->where("id", $id)
            ->where("appointment.appointment_status", 'pending')
            ->delete('appointment');
        if ($this->db->affected_rows() > 0) {
            return $json_array = array('status' => 'success', 'error' => '', 'message' => 'Data Deleted Successfully');
        } else {
            return $json_array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
    }

//======================================== Notification Model ==================================   

    public function updateReadNotification($data)
    {
        $this->db->insert("read_systemnotification", $data);
    }

//==================================== Get Staff ================================================

    public function getStaff($spec_id,$id)
    {
        $this->db->select('staff.*,staff_designation.designation as designation,staff_roles.role_id, department.department_name as department,roles.name as user_type');
        $this->db->join("staff_designation", "staff_designation.id = staff.designation", "left");
        $this->db->join("department", "department.id = staff.department", "left");
        $this->db->join("staff_roles", "staff_roles.staff_id = staff.id", "left");
        $this->db->join("roles", "staff_roles.role_id = roles.id", "left");
        $this->db->where("staff_roles.role_id", $id);
        $this->db->where("staff.is_active", "1");
        $this->db->where("staff.specialist", $spec_id);
        $this->db->from('staff');
        $query = $this->db->get();
        return $query->result_array();
    }

//=================================== Get Specialist ==============================================

    public function getDoctorfront($spec_id, $active = 1)
    {
        $query = $this->db->select("staff.*")->join('staff_roles', "staff_roles.staff_id = staff.id", "left")->join('roles', "roles.id = staff_roles.role_id", "left")->like('specialist', $spec_id)->where("staff.is_active", $active)->where("roles.id", 3)->get("staff");
        return $query->result_array();
    }

    public function getSpecialist()
    {
        $this->db->select('specialist.id, specialist.specialist_name,specialist.is_active');
        $this->db->from('specialist');
        $query = $this->db->get();
        return $query->result_array();
    }
//================================ Get Live Consult OPD ==========================================

    public function getconfrencebyopd($staff_id = null, $patient_id = null, $opdid = null)
    {
        $this->db->select('conferences.*,patients.id as pid,patients.patient_name,patients.patient_unique_id,opd_details.id as opdid,opd_details.opd_no,for_create.name as create_for_name,for_create.surname as create_for_surname,for_create.employee_id as create_for_employee_id,for_create_role.name as create_for_role_name,create_by.name as create_by_name,create_by.surname as create_by_surname,create_by.employee_id as create_by_employee_id,create_by_role.name as create_by_role_name')->from('conferences');
        $this->db->join('patients', 'patients.id = conferences.patient_id');
        $this->db->join('opd_details', 'conferences.opd_id = opd_details.id');
        $this->db->join('staff as for_create', 'for_create.id = conferences.staff_id');
        $this->db->join('staff_roles', 'staff_roles.staff_id = for_create.id');
        $this->db->join('roles as for_create_role', 'for_create_role.id = staff_roles.role_id');
        $this->db->join('staff as create_by', 'create_by.id = conferences.created_id');
        $this->db->join('staff_roles as staff_create_by_roles', 'staff_create_by_roles.staff_id = create_by.id');
        $this->db->join('roles as create_by_role', 'create_by_role.id = staff_create_by_roles.role_id');
        $this->db->where('conferences.patient_id', $patient_id);
        if ($opdid != "") {
            $this->db->where('conferences.opd_id', $opdid);
        }
        $this->db->order_by('DATE(`conferences`.`date`)', 'DESC');
        $query = $this->db->get();
        return $query->result();
    } 
    
    //======================================== Get Events AND Task ==============================

    public function getPatientEvents($id = null)
    {
        $cond  = "event_type = 'public' or event_type = 'task' ";
        $query = $this->db->where($cond)->get("events");
        return $query->result_array();
    }

    public function getEvents($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get("events");
            return $query->row_array();
        } else {
            $query = $this->db->get("events");
            return $query->result_array();
        }
    }

    public function getTaskEvent($id, $role_id)
    {        
        $query = $this->db->where(array('event_type' => 'task', 'event_for' => $id, 'role_id' => NULL))->order_by("is_active,start_date", "asc")->get("events");
        return $query->result_array();        
    }

    public function getTaskbyId($id)
    {
        $query = $this->db->where("id", $id)->where("event_type", "task")->get("events");
        return $query->row_array();
    }

    public function addTask($data)
    {
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("events", $data);
        } else {
            $this->db->insert("events", $data);
        }
    }

    public function deleteTask($id)
    {
        $query = $this->db->where("id", $id)->delete('events');
        if ($this->db->affected_rows() > 0) {
            return $json_array = array('status' => 'success', 'error' => '', 'message' => 'Data Deleted Successfull');
        } else {
            return $json_array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
    }

    public function getOPDBalanceTotal($id)
    {
        $query = $this->db->select("IFNULL(sum(balance_amount),'0') as balance_amount")->where("opd_payment.patient_id", $id)->get("opd_payment");
        return $query->row_array();
    }

    public function getOpdPaymentDetailpatient($opd_id)
    {
        $SQL   = 'select patient_charges.amount_due,opd_payment.amount_deposit from (SELECT sum(paid_amount) as `amount_deposit` FROM `opd_payment` WHERE opd_details_id=' . $this->db->escape($opd_id) . ') as opd_payment ,(SELECT sum(apply_charge) as `amount_due` FROM `patient_charges` WHERE opd_details_id=' . $this->db->escape($opd_id) . ') as patient_charges';
        $query = $this->db->query($SQL);
        return $query->row();
    }

    public function addOPDPayment($data)
    {
        $this->db->insert("opd_payment", $data);
        return $this->db->insert_id();
    }

    public function opdpaymentByID($id)
    {
        $query = $this->db->select('opd_payment.*,patients.id as pid,patients.note as pnote')
            ->join("patients", "patients.id = opd_payment.patient_id")->where("opd_payment.id", $id)
            ->get("opd_payment");
        return $query->row();
    }
    
    public function getprefixes($type)
    {
        $this->db->select()->from('prefixes');
        $this->db->where('type', $type);
        $query = $this->db->get();
        return $query->row();
    }

    public function allinvestigationbypatientid($patient_id)
    {
        $query = $this->db->query("select pathology_report.id as report_id,pathology_report.pathology_bill_id,pathology.test_name,pathology.short_name,pathology.report_days,pathology.id as pid,pathology.charge_id as cid,staff.name,staff.surname,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`, 'pathology' as type, pathology_report.pathology_center as test_center, pathology_report.collection_date,pathology_report.reporting_date,pathology_report.parameter_update,pathology_billing.case_reference_id from pathology_billing inner join pathology_report on pathology_report.pathology_bill_id = pathology_billing.id inner join pathology on pathology_report.pathology_id = pathology.id left join staff on staff.id = pathology_billing.doctor_id left join staff as collection_specialist_staff on collection_specialist_staff.id = pathology_report.collection_specialist left join staff as approved_by_staff on approved_by_staff.id = pathology_report.approved_by where pathology_billing.patient_id= " . $patient_id . "
            union all
            select radiology_report.id as report_id, radiology_report.radiology_bill_id,radio.test_name,radio.short_name,radio.report_days,radio.id as pid,radio.charge_id as cid,staff.name,staff.surname,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`, 'radiology' as type,radiology_report.radiology_center as test_center,radiology_report.collection_date,radiology_report.reporting_date,radiology_report.parameter_update,radiology_billing.case_reference_id  from radiology_billing inner join radiology_report on radiology_report.radiology_bill_id = radiology_billing.id inner join radio on radiology_report.radiology_id = radio.id left join staff on staff.id = radiology_report.consultant_doctor left join staff as collection_specialist_staff on collection_specialist_staff.id = radiology_report.collection_specialist left join staff as approved_by_staff on approved_by_staff.id = radiology_report.approved_by where radiology_billing.patient_id=" . $patient_id . " ");
        $result = $query->result_array();
        foreach($result as $key => $value){
            if($value['collection_specialist_staff_name'] == ''){
                $result[$key]['collection_specialist_staff_name']       = '';
            }
            if($value['collection_specialist_staff_name'] == ''){
                $result[$key]['collection_specialist_staff_surname']    = '';
            }
            if($value['collection_specialist_staff_employee_id'] == ''){
                $result[$key]['collection_specialist_staff_employee_id']= '';
            }
            if($value['parameter_update'] == ''){
                $result[$key]['parameter_update']= '';
            }
            if($value['approved_by_staff_name'] == ''){
                $result[$key]['approved_by_staff_name']= '';
            }
            if($value['approved_by_staff_surname'] == ''){
                $result[$key]['approved_by_staff_surname']= '';
            }
            if($value['approved_by_staff_employee_id'] == ''){
                $result[$key]['approved_by_staff_employee_id']= '';
            }
            if($value['case_reference_id'] == ''){
                $result[$key]['case_reference_id']= '';
            }
        }       
        return $result;

    }
	
	public function getopdtreatmenthistory($patientid)
    {
        $this->db
            ->select('opd_details.case_reference_id,opd_details.id as opd_id,opd_details.patient_id as patientid,opd_details.is_ipd_moved,max(visit_details.id) as visit_id,visit_details.appointment_date,visit_details.refference,visit_details.symptoms,patients.id as pid,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge,' )
            ->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left")
            ->join('staff', 'staff.id = visit_details.cons_doctor', "inner")
            ->join('patients', 'patients.id = opd_details.patient_id', "inner")
            ->join('consult_charges', 'consult_charges.doctor=visit_details.cons_doctor', 'left')
            ->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left')            
            ->order_by('visit_details.id', 'desc')
            ->where('opd_details.patient_id', $patientid)
            ->group_by('visit_details.opd_details_id', '')
            ->from('opd_details');
		$query = $this->db->get();
        $result = $query->result_array();
        return $result ;
         
    }
	
	public function getPrescriptionMedicinesByBasicID($id)
    {
        $query = $this->db->select('`ipd_prescription_basic`.*,pharmacy.id as pharmacy_id,pharmacy.medicine_name,medicine_category.id as medicine_category_id,medicine_category.medicine_category,ipd_prescription_details.instruction,ipd_prescription_details.dose_interval_id,ipd_prescription_details.dose_duration_id,dose_duration.name as dose_duration_name,dose_interval.name as dose_interval_name,medicine_dosage.dosage,unit.unit_name as unit,,ipd_prescription_details.dosage as dosage_id,ipd_prescription_details.id as ipd_prescription_detail_id')
            ->join("ipd_prescription_basic", "ipd_prescription_basic.id = ipd_prescription_details.basic_id")
            ->join("pharmacy", "ipd_prescription_details.pharmacy_id = pharmacy.id")
            ->join("medicine_category", "medicine_category.id=pharmacy.medicine_category_id")
            ->join("medicine_dosage", "medicine_dosage.id=ipd_prescription_details.dosage", "left")           
			->join("unit", "unit.id=medicine_dosage.units_id","left")
            ->join("dose_interval", "dose_interval.id=ipd_prescription_details.dose_interval_id", 'left')
            ->join("dose_duration", "dose_duration.id=ipd_prescription_details.dose_duration_id", 'left') 
            ->where("ipd_prescription_details.basic_id", $id)
            ->get("ipd_prescription_details");
        $result = $query->result();
        
        foreach($result as $key => $value){
            if($value->dose_duration_name == ''){
                $result[$key]->dose_duration_name =   '';
            }if($value->dose_interval_name == ''){
                $result[$key]->dose_interval_name =   '';
            }
        }
        
        return $result;
    }
	
    public function getPrescriptionByVisitID($visitid)  
    {
        $query = $this->db->select("opd_details.*,visit_details.id as visitid,visit_details.known_allergies as any_allergies,visit_details.weight,visit_details.height,visit_details.pulse,visit_details.temperature,visit_details.symptoms,visit_details.bp,patients.*,blood_bank_products.name as blood_group_name,staff.name,staff.surname,staff.employee_id,staff.local_address,ipd_prescription_basic.ipd_id,ipd_prescription_basic.id as prescription_id,ipd_prescription_basic.date as presdate,ipd_prescription_basic.header_note,ipd_prescription_basic.footer_note,ipd_prescription_basic.finding_description,ipd_prescription_basic.is_finding_print,prescription_generate.name as generated_by_name,prescription_generate.surname as generated_by_surname,prescription_generate.employee_id as generated_by_employee_id,prescribe_by.name as prescribe_by_name,prescribe_by.surname as prescribe_by_surname,prescribe_by.employee_id as prescribe_by_employee_id, opd_details.id as opd_detail_id,staff.employee_id as doctor_id, ipd_prescription_basic.attachment as ipd_prescription_attachment,ipd_prescription_basic.attachment_name as ipd_prescription_attachment_name");
        $this->db->join("visit_details", "visit_details.id = ipd_prescription_basic.visit_details_id", "left");
        $this->db->join("opd_details", "opd_details.id = visit_details.opd_details_id");
        $this->db->join("patients", "patients.id = opd_details.patient_id");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id', "left");
        $this->db->join("staff", "staff.id = visit_details.cons_doctor");
        $this->db->join("staff as prescription_generate", "prescription_generate.id = ipd_prescription_basic.generated_by");
        $this->db->join("staff as prescribe_by", "prescribe_by.id = ipd_prescription_basic.prescribe_by");
        $this->db->where("ipd_prescription_basic.visit_details_id", $visitid); 
        $query = $this->db->get("ipd_prescription_basic");
        if ($query->num_rows() > 0) {
            $result            = $query->row();
            $result->medicines = $this->getPrescriptionMedicinesByBasicID($result->prescription_id);            
            $tests     =  $this->getPrescriptionTestsByBasicID($result->prescription_id); 

			if($result->symptoms == null){
				$result->symptoms = '';
			}else{
				$result->symptoms = $result->symptoms;
			}			
			
                  $result->pathology = []; 
                    $result->radiology = []; 
                foreach($tests as $test_value){
                    if($test_value->pathology_id != ''){
                        $result->pathology[]  = $test_value;                        
                    }else{
                        $result->radiology[]  = $test_value;
                    }
                } 
            
            return $result;
        }
        return false;
    }    

    public function getPrescriptionTestsByBasicID($id, $test_category = null)
    {
        $this->db->select('ipd_prescription_test.*,pathology.test_name,pathology.short_name,pathology.report_days,pathology.charge_id,charges.standard_charge,charges.name as `charge_name`,pathology.test_name,radio.test_name as `radio_test_name`,radio.short_name as `radio_short_name`,radio.report_days as `radio_report_days`,radio.charge_id as `radio_charge_id`,radio_charge.standard_charge as `radio_standard_charge`,radio_charge.name as `radio_charge_name`');
        $this->db->join("pathology", "ipd_prescription_test.pathology_id = pathology.id", 'left');
        $this->db->join("radio", "ipd_prescription_test.radiology_id = radio.id", 'left');
        $this->db->join("charges", "pathology.charge_id = charges.id", 'left');
        $this->db->join("charges as `radio_charge`", "radio.charge_id = radio_charge.id", 'left');
        $this->db->where("ipd_prescription_test.ipd_prescription_basic_id", $id);
        $query = $this->db->get('ipd_prescription_test');
        return $query->result();
    }

    public function getallinvestigation($case_reference_id)
    {
        $query = $this->db->query("select pathology_report.id as report_id,pathology_report.pathology_bill_id as bill_id,pathology.test_name,pathology.short_name,pathology.report_days,pathology.id as pid,pathology.charge_id as cid,staff.name,staff.surname,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`, 'pathology' as type, pathology_report.pathology_center as test_center, pathology_report.collection_date,pathology_report.reporting_date,pathology_report.parameter_update from pathology_billing inner join pathology_report on pathology_report.pathology_bill_id = pathology_billing.id inner join pathology on pathology_report.pathology_id = pathology.id left join staff on staff.id = pathology_billing.doctor_id left join staff as collection_specialist_staff on collection_specialist_staff.id = pathology_report.collection_specialist left join staff as approved_by_staff on approved_by_staff.id = pathology_report.approved_by where pathology_billing.case_reference_id= " . $case_reference_id . "
            union all
            select radiology_report.id as report_id,radiology_report.radiology_bill_id as bill_id,radio.test_name,radio.short_name,radio.report_days,radio.id as pid,radio.charge_id as cid,staff.name,staff.surname,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`, 'radiology' as type,radiology_report.radiology_center as test_center,radiology_report.collection_date,radiology_report.reporting_date,radiology_report.parameter_update  from radiology_billing inner join radiology_report on radiology_report.radiology_bill_id = radiology_billing.id inner join radio on radiology_report.radiology_id = radio.id left join staff on staff.id = radiology_report.consultant_doctor left join staff as collection_specialist_staff on collection_specialist_staff.id = radiology_report.collection_specialist left join staff as approved_by_staff on approved_by_staff.id = radiology_report.approved_by where radiology_billing.case_reference_id=" . $case_reference_id . " ");
        $result = $query->result_array();
        foreach($result as $key => $value){
               if($value['approved_by_staff_name'] == ''){
                   $result[$key]['approved_by_staff_name'] = '';
               } 
               if($value['approved_by_staff_surname'] == ''){
                   $result[$key]['approved_by_staff_surname'] = '';
               } 
               if($value['approved_by_staff_employee_id'] == ''){
                   $result[$key]['approved_by_staff_employee_id'] = '';
               } 
               if($value['parameter_update'] == ''){
                   $result[$key]['parameter_update'] = '';
               } 
               if($value['collection_specialist_staff_name'] == ''){
                   $result[$key]['collection_specialist_staff_name'] = '';
               }  
               if($value['collection_specialist_staff_surname'] == ''){
                   $result[$key]['collection_specialist_staff_surname'] = '';
               }   
               if($value['collection_specialist_staff_employee_id'] == ''){
                   $result[$key]['collection_specialist_staff_employee_id'] = '';
               }   
               if($value['approved_by_staff_name'] == ''){
                   $result[$key]['approved_by_staff_name'] = '';
               }  
               if($value['approved_by_staff_surname'] == ''){
                   $result[$key]['approved_by_staff_surname'] = '';
               }  
               if($value['approved_by_staff_employee_id'] == ''){
                   $result[$key]['approved_by_staff_employee_id'] = '';
               } 
        }
        
        return $result;
    }
    
    public function getmedicationdetailsbydateopd($opdid)
    {
        $this->db->select('medication_report.pharmacy_id,medication_report.date,pharmacy.   medicine_category_id');
        $this->db->join('pharmacy', 'pharmacy.id = medication_report.pharmacy_id', 'inner');
        $this->db->where("medication_report.opd_details_id", $opdid);
        $this->db->group_by('medication_report.date');
        $this->db->order_by('medication_report.date', 'desc');
        $query             = $this->db->get('medication_report');
        $result_medication = $query->result_array();

        if (!empty($result_medication)) {
            $i = 0;
            foreach ($result_medication as $key => $value) {
                $date = $value['date'];
                $return = $this->getmedicationbydateopd($date, $opdid);
                if (!empty($return)) {
                    foreach ($return as $m_key => $m_value) {               
                      $result_medication[$i]['dose_list'][] = $m_value;
                    }
                }
                $i++;
            }
        } 

        return $result_medication;
    }
	
	public function getmedicationbydateopd($date, $opdid)
    {
        $query = $this->db->select("medication_report.*,pharmacy.medicine_name,pharmacy.medicine_category_id,medicine_dosage.dosage as medicine_dosage,unit.unit_name as unit, staff.name as staff_name, staff.surname as staff_surname,staff.employee_id as staff_employee_id")
            ->join('staff', 'staff.id = medication_report.generated_by', 'left')
            ->join('pharmacy', 'pharmacy.id = medication_report.pharmacy_id', 'left')
            ->join('medicine_dosage', 'medicine_dosage.id = medication_report.medicine_dosage_id', 'left')
            ->join('unit', 'medicine_dosage.units_id = unit.id', 'left')
            ->where("medication_report.date", $date)
            ->where("medication_report.opd_details_id", $opdid)
            ->get("medication_report");
        $result = $query->result_array();
        return $result;
    }
    
    public function getopdoperationDetails($opdid)
    {        
        $this->db->select('operation_theatre.*,operation.operation,operation_category.category,patients.id as pid,patients.patient_name,patients.gender,patients.age,patients.month,patients.patient_type,patients.mobileno,patients.is_active')->from('operation_theatre');
        $this->db->join('opd_details', 'opd_details.id=operation_theatre.opd_details_id', "left");
        $this->db->join('patients', 'patients.id=opd_details.patient_id', "left");
        $this->db->join('operation', 'operation_theatre.operation_id=operation.id', "left");
        $this->db->join("operation_category", "operation_category.id=operation.category_id", "left");
        $this->db->where('operation_theatre.opd_details_id', $opdid);
        $query = $this->db->get();
        $result     =   $query->result_array();
            foreach($result as  $key => $valuee){                 
                $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'operationtheatre');    
                $result[$key]['customfield'] = $customfield;              
            }     
        return $result;
    }

	public function getconfrencebyvisitid($visitid )
    {
        $this->db->select('conferences.*,for_create.name as `create_for_name`,for_create.surname as `create_for_surname,create_by.name as `create_by_name`,create_by.surname as `create_by_surname,for_create.employee_id as `for_create_employee_id`,for_create_role.name as `for_create_role_name`,create_by_role.name as `create_by_role_name`,create_by.employee_id as `create_by_employee_id`,patients.patient_name,patients.id as `patientid`, staff_create_by_roles.role_id as staff_create_by_role_id')->from('conferences');
        $this->db->join('staff as for_create', 'for_create.id = conferences.staff_id');
        $this->db->join('staff as create_by', 'create_by.id = conferences.created_id','left');
        $this->db->join('staff_roles', 'staff_roles.staff_id = for_create.id');
        $this->db->join('roles as `for_create_role`', 'for_create_role.id = staff_roles.role_id');
        $this->db->join('staff_roles as staff_create_by_roles', 'staff_create_by_roles.staff_id = create_by.id','left');
        $this->db->join('roles as `create_by_role`', 'create_by_role.id = staff_create_by_roles.role_id','left');
        $this->db->join('visit_details', 'visit_details.id = conferences.visit_details_id','left');
        $this->db->join('opd_details', 'opd_details.id = visit_details.opd_details_id','left');
        $this->db->join('patients', 'patients.id = opd_details.patient_id','left');
        $this->db->where_in('conferences.visit_details_id', $visitid);
        $this->db->order_by('DATE(`conferences`.`date`)', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function getVisitDetailsid($opd_details_id){
        $visit_array=array();
        $visit_id= $this->db->select('id')->from('visit_details')->where('opd_details_id',$opd_details_id)->get()->result_array();
        if(!empty($visit_id)){
            foreach ($visit_id as $key => $value) {
                $visit_array[]=$value['id'];           
            }   
        }        
        return $visit_array;
    }
    //======================================Timeline_model===============================================

    public function getPatientTimeline($id)
    {
        $query = $this->db->where("patient_id", $id)->order_by("timeline_date", "desc")->where("status", "yes")->get("patient_timeline");
        return $query->result_array();
    }
    
    // ====================================================
    public function getipdprescriptionbyid($prescription_no)
    {        
        $this->db->select("ipd_details.*,blood_bank_products.name as blood_group_name,patients.*,staff_generated.name as staff_name,staff_generated.surname as staff_surname,staff_generated.employee_id as staff_employee_id,staff.name,staff.surname,staff.employee_id,staff.local_address,ipd_prescription_basic.ipd_id,ipd_prescription_basic.id as prescription_id,ipd_prescription_basic.date as presdate,ipd_prescription_basic.header_note,ipd_prescription_basic.footer_note,ipd_prescription_basic.finding_description,ipd_prescription_basic.is_finding_print,staff.id as staff_id,staff_priscribe_by.name as priscribe_by_name,staff_priscribe_by.surname as priscribe_by_surname,staff_priscribe_by.employee_id as priscribe_by_employee_id,ipd_prescription_basic.prescribe_by,ipd_prescription_basic.attachment,ipd_prescription_basic.attachment_name");
        $this->db->join("ipd_details", "ipd_prescription_basic.ipd_id = ipd_details.id");
        $this->db->join("patients", "patients.id = ipd_details.patient_id");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id',"left");
        $this->db->join("staff", "staff.id = ipd_details.cons_doctor","left");
        $this->db->join("staff as staff_generated", "staff_generated.id = ipd_prescription_basic.generated_by","left");
        $this->db->join("staff as staff_priscribe_by", "staff_priscribe_by.id = ipd_prescription_basic.prescribe_by","left");
        $this->db->where("ipd_prescription_basic.id", $prescription_no);
        $query = $this->db->get("ipd_prescription_basic");        
        $result = $query->row();       
       
            if (!empty($result)) {
                $result            = $query->row();                
                $result->medicines = $this->getPrescriptionMedicinesByBasicID($result->prescription_id);         
                $tests     = $this->getPrescriptionTestsByBasicID($result->prescription_id); 
				$result->pathology  = array();   
				$result->radiology  = array();
                foreach($tests as $test_value){					  
					
                    if($test_value->pathology_id != ''){
                        $result->pathology[]  = $test_value;                        
                    }else{
                        $result->radiology[]  = $test_value;
                    }
                } 
            }
            
        return $result;
    }
    
    public function deletesystemnotifications($patient_id)
    {          
        $query =  $this->db->where('receiver_id', $patient_id)->delete('system_notification');
        if ($this->db->affected_rows() > 0) {
            return $json_array = array('status' => 'success', 'error' => '', 'message' => 'Data Deleted Successfully');
        } else {
            return $json_array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
    } 
    
    public function getstaffDetails($id){
        return  $this->db->select('staff.id,staff.name,staff.surname,staff.employee_id,roles.id as role_id')->from('staff')->join("staff_roles", "staff_roles.staff_id = staff.id", "left")->join("roles", "staff_roles.role_id = roles.id", "left")->where('staff.id',$id)->get()->row_array();
    }
    
    public function getSystemNotification_byevent($event){
        return $this->db->select('*')->from('system_notification_setting')->where('event',$event)->get()->row_array();
    }

   
    public function getAppointmentDetails($appointment_id)
    {
        $this->db->select("shift_details.charge_id, patients.email, patients.patient_name as name,patients.mobileno,patients.id as patient_id,appointment.doctor,appointment.doctor_shift_time_id,appointment.date");
        $this->db->join("appointment","appointment.doctor=shift_details.staff_id","left");
        $this->db->join("patients","patients.id=appointment.patient_id","left");
        $this->db->where("appointment.id",$appointment_id);
        $query  = $this->db->get("shift_details");
        $result = $query->row();
        return $result;
    }

    public function getChargeDetailsById($id)
    {
        $result = $this->db->select("charges.standard_charge,tax_category.percentage")
            ->join('tax_category', 'tax_category.id = charges.tax_category_id', 'LEFT')
            ->where("charges.id", $id)
            ->get("charges")
            ->row();
        return $result;
    }

    public function getAppointmentsBySlot($doctor_id, $shift_id, $date, $slot)
    {
        $this->db->select("date");
        $this->db->where("doctor", $doctor_id);
        $this->db->where("doctor_shift_time_id", $shift_id);       
        $this->db->where("appointment_status", "approved");
        $this->db->where("date_format(date,'%Y-%m-%d')", $date);
        $query         = $this->db->get("appointment");
        return $result = $query->result();
    }

    public function paymentSuccess($payment_data, $transaction)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert("appointment_payment",$payment_data);
        $insert_id=$this->db->insert_id();
        $data = array('appointment_status' => 'approved');
        $this->db->insert("transactions",$transaction);
        $this->db->update("appointment", $data,"id=".$payment_data['appointment_id']);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $payment_data['appointment_id'];
        } 
    }

     public function getoppDetails($id)
    {
         $this->db->select('appointment.*,staff.name,staff.surname,patients.patient_name as patient_name,patients.gender as gender, patients.email as email, patients.mobileno as mobileno,appoint_priority.appoint_priority,patients.id as patient_id');
         $this->db->join('staff', 'appointment.doctor = staff.id', "left");
         $this->db->join('patients', 'appointment.patient_id = patients.id', "left");
         $this->db->join('appoint_priority', 'appoint_priority.id = appointment.priority', "left");
         $this->db->where('appointment.id', $id);
         $query = $this->db->get('appointment');
         return $query->row_array();
     }
	
	public function getDetails($opdid)
    {
        $this->db->select('opd_details.*,blood_bank_products.name as blood_group_name,visit_details. casualty,visit_details.is_antenatal,visit_details.symptoms,visit_details.known_allergies,visit_details.refference,visit_details.case_type,patients.id as pid,patients.patient_name,patients.age,patients.month,patients.day,patients.image,patients.mobileno,patients.email,patients.gender,patients.dob,patients.marital_status,patients.blood_group,patients.address,patients.guardian_name,patients.month,patients.known_allergies,patients.marital_status,staff.name,staff.surname,staff.employee_id,discharge_card.discharge_date,organisation.organisation_name,organisation.code,patients.insurance_id,patients.insurance_validity,patients.organisation_id')->from('opd_details');
        $this->db->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left");
        $this->db->join('discharge_card', 'opd_details.id = discharge_card.opd_details_id', "left");
        $this->db->join('staff', 'staff.id = visit_details.cons_doctor', "left");
        $this->db->join('patients', 'patients.id = opd_details.patient_id', "left");
        $this->db->join('organisation', 'organisation.id = patients.organisation_id', "left");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left');
        $this->db->where('opd_details.id', $opdid);
        $query  = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function getTransactionByAppointmentId($appointment_id){
        $result = $this->db->select("*")
            ->where("transactions.appointment_id",$appointment_id)
            ->get("transactions")
            ->row();
        return $result;
    }

    public function getPaymentByAppointmentId($appointment_id)
    {
        $result = $this->db->select('appointment_payment.*')
            ->where('appointment_id', $appointment_id)
            ->get('appointment_payment')
            ->row();
        return $result;
    }

    public function moveToOpd($opd_details, $visit_details, $charges, $appointment_id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->insert('case_references', array('id' => null));
        $case_id                          = $this->db->insert_id();
        $opd_details['case_reference_id'] = $case_id;
        $this->db->insert('opd_details', $opd_details);
        $opd_id            = $this->db->insert_id();
        $charges['opd_id'] = $opd_id;
        $this->db->insert('patient_charges', $charges);
        $patient_charge_id                  = $this->db->insert_id();
        $visit_details['opd_details_id']    = $opd_id;
        $visit_details['patient_charge_id'] = $patient_charge_id;
        $this->db->insert('visit_details', $visit_details);
        $visit_details_id                      = $this->db->insert_id();
        $transaction_data['case_reference_id'] = $case_id;
        $transaction_data['opd_id']            = $opd_id;
        $this->db->update("transactions", $transaction_data, array("appointment_id" => $appointment_id));
        $appointment_data['case_reference_id'] = $case_id;
        $appointment_data['visit_details_id']  = $visit_details_id;
        $this->db->update("appointment", $appointment_data, array("id" => $appointment_id));
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $visit_details_id;
        }
    }
}