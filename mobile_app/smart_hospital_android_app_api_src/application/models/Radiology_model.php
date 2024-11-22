<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Radiology_model extends CI_Model {


   public function __construct() {
        parent::__construct();
        $CI =& get_instance();        
       
    }

    public function getBillDetailsRadio($patient_id) 
    {     
        $this->db->select('radiology_billing.*,sum(transactions.amount)as paid_amount,patients.patient_name,patients.id as pid,staff.name,staff.surname,staff.employee_id,org.organisation_name');
        $this->db->join('patients', 'patients.id = radiology_billing.patient_id', 'left');
        $this->db->join('staff', 'staff.id = radiology_billing.doctor_id', 'left');
        $this->db->join('transactions','transactions.radiology_billing_id = radiology_billing.id','left');
		$this->db->join('organisation', 'organisation.id = patients.organisation_id','left');
        $this->db->join('organisation as org', 'org.id = radiology_billing.organisation_id','left');		
        $this->db->group_by('radiology_billing.id');
        $this->db->where('radiology_billing.patient_id', $patient_id);
        $this->db->order_by('radiology_billing.id', 'desc');
        $query = $this->db->get('radiology_billing');       
        
        $result = $query->result_array();      
       
            foreach($result as  $key => $valuee){                
                
                if(empty($valuee['case_reference_id'])){
                    $result[$key]['case_reference_id'] = '';
                }
                if(empty($valuee['paid_amount'])){
                    $result[$key]['paid_amount'] = '';
                }
                if(empty($valuee['transaction_id'])){
                    $result[$key]['transaction_id'] = '';
                }
                if(empty($valuee['name'])){
                    $result[$key]['name'] = '';
                }
                if(empty($valuee['surname'])){
                    $result[$key]['surname'] = '';
                }
                if(empty($valuee['employee_id'])){
                    $result[$key]['employee_id'] = '';
                }
                if(empty($valuee['insurance_id'])){
                    $result[$key]['insurance_id'] = '';
                }
                if(empty($valuee['insurance_validity'])){
                    $result[$key]['insurance_validity'] = '';
                }
                if(empty($valuee['organisation_name'])){
                    $result[$key]['organisation_name'] = '';
                }                
                
                $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'radiology');         
                $result[$key]['customfield'] = $customfield;              
            }           
            
        return $result;
    }
	
	public function getIpdPrescriptionBasic($ipd_prescription_basic_id)
    {
        $this->db->select('ipd_prescription_basic.*');
        $this->db->where('ipd_prescription_basic.id', $ipd_prescription_basic_id);
        $query = $this->db->get('ipd_prescription_basic');
        return $query->row();
    }
    
    public function getparameterDetailsradio($report_id){
       
        $query = $this->db->select('radiology_billing.*,blood_bank_products.name as blood_group_name,IFNULL((SELECT SUM(amount) FROM transactions WHERE radiology_billing_id=radiology_billing.id),0) as total_deposit,patients.patient_name,patients.id as patient_unique_id,patients.age, patients.month, patients.day,patients.gender,patients.dob,patients.blood_group,patients.mobileno,patients.email,patients.address,staff.employee_id,staff.name,staff.surname,staff.employee_id,transactions.payment_mode,transactions.amount,transactions.cheque_no,transactions.cheque_date,transactions.note as `transaction_note`')
            ->join('patients', 'radiology_billing.patient_id = patients.id')
            ->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left')
            ->join('staff', 'staff.id = radiology_billing.generated_by')
            ->join('transactions', 'transactions.id = radiology_billing.transaction_id','left')
            ->where("radiology_billing.id", $report_id)
            ->get('radiology_billing');

        if ($query->num_rows() > 0) {
            $result                       = $query->row();           
            
			if($result->ipd_prescription_basic_id == ''){
                $result->ipd_prescription_basic_id = '';
            }
			
            $result->{'radiology_report'} = $this->getRadioReportByBillId($result->id);
            return $result;
        }
        return false;                  
    }
    
    public function getRadioReportByBillId($id)
    {
        $query = $this->db->select('radiology_report.*,radio.test_name,radio.short_name,radio.report_days,radio.id as pid,radio.charge_id as cid,staff.name,staff.surname,charges.charge_category_id,charges.name,charges.standard_charge,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`')
            ->join('radiology_billing', 'radiology_report.radiology_bill_id = radiology_billing.id')
            ->join('radio', 'radiology_report.radiology_id = radio.id')
            ->join('staff', 'staff.id = radiology_report.consultant_doctor', "left")
            ->join('staff as collection_specialist_staff', 'collection_specialist_staff.id = radiology_report.collection_specialist', "left")
            ->join('staff as approved_by_staff', 'approved_by_staff.id = radiology_report.approved_by', "left")
            ->join('charges', 'radio.charge_id = charges.id')
            ->where('radiology_report.radiology_bill_id', $id)
            ->get('radiology_report');
         $result    =   $query->result();
        foreach($result as $key => $value){
            if($value->collection_specialist_staff_name == ''){
                $result[$key]->collection_specialist_staff_name = '';
            }
            if($value->collection_specialist_staff_surname == ''){
                $result[$key]->collection_specialist_staff_surname = '';
            }
            if($value->collection_specialist_staff_employee_id == ''){
                $result[$key]->collection_specialist_staff_employee_id = '';
            }
            if($value->approved_by_staff_name == ''){
                $result[$key]->approved_by_staff_name = '';
            }
            if($value->approved_by_staff_surname == ''){
                $result[$key]->approved_by_staff_surname = '';
            }
            if($value->approved_by_staff_employee_id == ''){
                $result[$key]->approved_by_staff_employee_id = '';
            }
            if($value->radiology_report == ''){
                $result[$key]->radiology_report = '';
            }            
        }
        return $result;        
    }

    public function getTotalRadiologyPayment($patholgy_id){
        $query = $this->db->select("sum(amount) as paid_amount")
            ->join("radiology_billing", "radiology_billing.id = transactions.radiology_billing_id")
            ->where("transactions.radiology_billing_id", $patholgy_id)
            ->get("transactions");
        return $query->row_array();
    }

    public function getTotalRadiologyCharge($pathology_id){
        $query = $this->db->select("net_amount")
            ->where('radiology_billing.id', $pathology_id)
            ->get('radiology_billing');
        return $query->row_array();
    }
    
    public function getPatientRadiologyReportDetails($id)
    {
        $query = $this->db->select('radiology_report.*,radio.test_name,radio.short_name,radio.report_days,radio.id as pid,radio.charge_id as charge_id,radiology_report.radiology_bill_id,radiology_billing.doctor_name,radiology_billing.case_reference_id,radiology_billing.patient_id,charges.charge_category_id,charges.name as `charge_name`,charges.standard_charge,patients.patient_name as `patient_name`,patients.id as patient_unique_id,patients.age,patients.month,patients.day,patients.gender,patients.blood_group,patients.mobileno,patients.email,patients.address,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,collection_specialist_staff.id as `collection_specialist_staff_id`')
            ->join('radiology_billing', 'radiology_report.radiology_bill_id = radiology_billing.id')
            ->join('patients', 'radiology_report.patient_id = patients.id')
            ->join('radio', 'radiology_report.radiology_id = radio.id')
            ->join('staff as collection_specialist_staff', 'collection_specialist_staff.id = radiology_report.collection_specialist', "left")
            ->join('charges', 'radio.charge_id = charges.id')
            ->where('radiology_report.id', $id)
            ->get('radiology_report');

        if ($query->num_rows() > 0) {
            $result                          = $query->row(); 
            foreach($result as $row){ 
                if($result->radiology_report == ''){
                    $result->radiology_report = '';
                }
                if($result->radiology_result == ''){
                    $result->radiology_result = '';
                } 
            } 
            $result->{'parameter'} = $this->getPatientRadiologyReportParameterDetails($result->id);   
            
            return $result;
        }
        return false;
    }

    public function getPatientRadiologyReportParameterDetails($radiology_report_id)
    {
        $sql    = "SELECT radiology_report.radiology_report, radiology_parameterdetails.*,radiology_parameter.parameter_name,radiology_parameter.description,radiology_parameter.reference_range,unit.unit_name,IFNULL(radiology_report_parameterdetails.id,0) as `radiology_report_parameterdetail_id`,radiology_report_parameterdetails.radiology_report_id,radiology_report_parameterdetails.radiology_parameterdetail_id,radiology_report_parameterdetails.radiology_report_value FROM `radiology_report` INNER join radiology_parameterdetails on radiology_parameterdetails.radiology_id=radiology_report.radiology_id INNER JOIN radiology_parameter on radiology_parameterdetails.radiology_parameter_id=radiology_parameter.id INNER JOIN unit on radiology_parameter.unit=unit.id LEFT join radiology_report_parameterdetails on radiology_report_parameterdetails.radiology_parameterdetail_id=radiology_parameterdetails.id and radiology_report_parameterdetails.radiology_report_id=radiology_report.id WHERE radiology_report.id =" . $radiology_report_id;
        $query  = $this->db->query($sql); 
        $result = $query->result();
        foreach($result as $key => $value){
            if($value->radiology_report_value == ''){
                $result[$key]->radiology_report_value ='';
            }
        }
        return $result;
    }   
    
    
}   