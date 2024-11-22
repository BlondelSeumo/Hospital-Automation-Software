<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pathology_model extends CI_Model {
 
    public function getBillDetailsPatho($id)
    {
        $this->db->select('pathology_billing.*,sum(transactions.amount) as paid_amount,patients.patient_name,patients.id as pid,staff.name,staff.surname,staff.employee_id,org.organisation_name');
        $this->db->join('patients', 'patients.id = pathology_billing.patient_id', 'left');
        $this->db->join('staff', 'staff.id = pathology_billing.doctor_id', 'left');
        $this->db->join('transactions', 'transactions.pathology_billing_id = pathology_billing.id','left');		
		$this->db->join('organisation', 'organisation.id = patients.organisation_id','left');
        $this->db->join('organisation as org', 'org.id = pathology_billing.organisation_id','left');			
        $this->db->group_by('pathology_billing.id');
        $this->db->where('pathology_billing.patient_id', $id); 
        $this->db->order_by('pathology_billing.id', 'desc');
        $query = $this->db->get('pathology_billing');
        $result = $query->result_array();         
       
            foreach($result as  $key => $valuee){  
                if(empty($valuee['case_reference_id'])){
                    $result[$key]['case_reference_id'] = '';
                }if(empty($valuee['paid_amount'])){
                    $result[$key]['paid_amount'] = '';
                }if(empty($valuee['organisation_name'])){
                    $result[$key]['organisation_name'] = '';
                }if(empty($valuee['insurance_id'])){
                    $result[$key]['insurance_id'] = '';
                }if(empty($valuee['insurance_validity'])){
                    $result[$key]['insurance_validity'] = '';
                }
                $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'pathology');    
                $result[$key]['customfield'] = $customfield;              
            }           
            
        return $result;
    }

    public function getTotalPathologyPayment($patholgy_id){
        $query = $this->db->select("sum(amount) as paid_amount")
            ->join("pathology_billing", "pathology_billing.id = transactions.pathology_billing_id")
            ->where("transactions.pathology_billing_id", $patholgy_id)
            ->get("transactions");
        return $query->row_array();
    }

    public function getTotalPathologyCharge($pathology_id){
        $query = $this->db->select("net_amount")
            ->where('pathology_billing.id', $pathology_id)
            ->get('pathology_billing');
        return $query->row_array();
    }
    
    public function getPatientPathologyReportDetails($id)
    {
        $query = $this->db->select('pathology_report.*,pathology.test_name,pathology.short_name,pathology.report_days,pathology.id as pid,pathology.charge_id as charge_id,pathology_billing.case_reference_id,pathology_billing.id as bill_no,pathology_billing.patient_id,pathology_billing.doctor_name,charges.charge_category_id,charges.name as `charge_name`,charges.standard_charge,patients.patient_name as `patient_name`,patients.id as patient_unique_id,patients.age,patients.dob,patients.month,patients.day,patients.gender,patients.blood_group,patients.mobileno,patients.email,patients.address,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,collection_specialist_staff.id as `collection_specialist_staff_id`')
            ->join('pathology_billing', 'pathology_report.pathology_bill_id = pathology_billing.id')
            ->join('patients', 'pathology_report.patient_id = patients.id')
            ->join('pathology', 'pathology_report.pathology_id = pathology.id')
            ->join('staff as collection_specialist_staff', 'collection_specialist_staff.id = pathology_report.collection_specialist', "left")
            ->join('charges', 'pathology.charge_id = charges.id')
            ->where('pathology_report.id', $id)
            ->get('pathology_report');

        if ($query->num_rows() > 0) {
            $result                          = $query->row();
            foreach($result as $row){ 
                if($result->pathology_report == ''){
                    $result->pathology_report = '';
                }
                if($result->pathology_result == ''){
                    $result->pathology_result = '';
                }
                $result->{'parameter'} = $this->getPatientPathologyReportParameterDetails($result->id);
            } 
            return $result;
        }
        return false;
    }

    public function getPatientPathologyReportParameterDetails($pathology_report_id)
    {
        $sql    = "SELECT pathology_parameterdetails.*,pathology_report.pathology_result,pathology_parameter.parameter_name,pathology_parameter.description,pathology_parameter.reference_range,unit.unit_name,IFNULL(pathology_report_parameterdetails.id,0) as `pathology_report_parameterdetail_id`,pathology_report_parameterdetails.pathology_report_id,pathology_report_parameterdetails.pathology_parameterdetail_id,pathology_report_parameterdetails.pathology_report_value FROM `pathology_report` INNER join pathology_parameterdetails on pathology_parameterdetails.pathology_id=pathology_report.pathology_id INNER JOIN pathology_parameter on pathology_parameterdetails.pathology_parameter_id=pathology_parameter.id INNER JOIN unit on pathology_parameter.unit=unit.id LEFT join pathology_report_parameterdetails on pathology_report_parameterdetails.pathology_parameterdetail_id=pathology_parameterdetails.id and pathology_report_parameterdetails.pathology_report_id=pathology_report.id WHERE pathology_report.id =" . $pathology_report_id;
        $query  = $this->db->query($sql);
        $result = $query->result();
            foreach($result as $key => $value){  
                if($value->pathology_report_value ==''){;
                    $result[$key]->pathology_report_value = '';
                }
            } 
        return $result;
    }   
    
}   