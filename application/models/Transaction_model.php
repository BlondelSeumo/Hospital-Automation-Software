<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Transaction_model extends MY_Model
{    
    public function pharmacyPaymentByTransactionId($transaction_id)
    {
        $query = $this->db->select('transactions.*,pharmacy_bill_basic.id as pharmacy_bill_basic_id,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.guardian_name,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address')
            ->join("pharmacy_bill_basic", "pharmacy_bill_basic.id = transactions.pharmacy_bill_basic_id")
            ->join("patients", "patients.id = pharmacy_bill_basic.patient_id")
            ->where("transactions.id", $transaction_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->row();

    }
	
    public function allPaymentByCaseId($case_id)
    {
        $query = $this->db->select('transactions.*')
            ->where("transactions.case_reference_id", $case_id)           
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->result_array();
    }
 
   public function ipdopdPaymentByCaseId($case_id)
    {
        $query = $this->db->select('sum(amount) as total_pay')
               ->group_start()     
                ->or_where('transactions.opd_id !=',null)
                ->or_where('transactions.ipd_id !=',null)
                ->group_end()   
                ->where("transactions.case_reference_id", $case_id)
                ->where("transactions.type", 'payment')         
                ->order_by("transactions.id", "desc")
                ->get("transactions");
        return $query->row_array();
    }

    public function radiologyPaymentByTransactionId($transaction_id)
    {
        $query = $this->db->select('transactions.*,radiology_billing.id as radiology_billing_id,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.age,patients.month,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address,staff.name,staff.surname,staff.employee_id')
            ->join("radiology_billing", "radiology_billing.id = transactions.radiology_billing_id")
            ->join("patients", "patients.id = radiology_billing.patient_id")
            ->join("staff", "staff.id = transactions.received_by",'LEFT')
            ->where("transactions.id", $transaction_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->row();
    }
    
    public function opdPaymentByTransactionId($transaction_id)
    {
        $query = $this->db->select('transactions.*,opd_details.id as opd_id,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.age,patients.month,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address')
            ->join("opd_details", "opd_details.id = transactions.opd_id")
            ->join("patients", "patients.id = opd_details.patient_id")
            ->where("transactions.id", $transaction_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->row();
    }

    public function ipdPaymentByTransactionId($transaction_id)
    {
        $query = $this->db->select('transactions.*,ipd_details.id as ipd_id,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.age,patients.month,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address')
            ->join("ipd_details", "ipd_details.id = transactions.ipd_id")
            ->join("patients", "patients.id = ipd_details.patient_id")
            ->where("transactions.id", $transaction_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->row();
    }

    public function pharmacyPayments($pharmacy_bill_basic_id)
    {
        $query = $this->db->select('transactions.*,pharmacy_bill_basic.id as pharmacy_bill_basic_id,patients.note as pnote')
            ->join("pharmacy_bill_basic", "pharmacy_bill_basic.id = transactions.pharmacy_bill_basic_id")
            ->join("patients", "patients.id = pharmacy_bill_basic.patient_id")
            ->where("transactions.pharmacy_bill_basic_id", $pharmacy_bill_basic_id)
            ->order_by("transactions.payment_date", "desc")
            ->get("transactions");
        return $query->result();
    }

    public function pathologyPayments($pathology_billing_id)
    {
        $query = $this->db->select('transactions.*,pathology_billing.id as pathology_billing_id,patients.note as pnote,pathology_billing.case_reference_id')
            ->join("pathology_billing", "pathology_billing.id = transactions.pathology_billing_id")
            ->join("patients", "patients.id = pathology_billing.patient_id")
            ->where("transactions.pathology_billing_id", $pathology_billing_id)
            ->order_by("transactions.payment_date", "desc")
            ->get("transactions");
        return $query->result();
    }

    public function pathologyTotalPayments($pathology_billing_id)
    {
        $query = $this->db->select('sum(amount) as total_paid, pathology_billing.*')
            ->join("transactions", "pathology_billing.id = transactions.pathology_billing_id","left")
            ->join("patients", "patients.id = pathology_billing.patient_id")
            ->where("pathology_billing.id", $pathology_billing_id)
            ->get("pathology_billing ");
        return $query->row();
    }  

    public function bloodIssueTotalPayments($blood_issue_id)
    {
        $query = $this->db->select('IFNULL(sum(transactions.amount),0) as total_paid, blood_issue.*')
                 ->join("transactions ", "blood_issue.id = transactions.blood_issue_id",'left')
            ->join("patients", "patients.id = blood_issue.patient_id")
            ->where("blood_issue.id", $blood_issue_id)
            ->get("blood_issue");
        return $query->row();
    }

    public function pharmacyTotalPayments($pharmacy_bill_basic_id)
    {
        
        $query = $this->db->select('IFNULL((select sum(amount) as amount_paid from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="payment" ),0) as total_paid, IFNULL((select sum(amount) as refund from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="refund" ),0) as refund_amount, pharmacy_bill_basic.*')
                 
            ->join("patients", "patients.id = pharmacy_bill_basic.patient_id")
            ->where("pharmacy_bill_basic.id", $pharmacy_bill_basic_id)           
            ->get("pharmacy_bill_basic");
        return $query->row();
    }

    public function radiologyPayments($radiology_billing_id)
    {
        $query = $this->db->select('transactions.*,radiology_billing.id as radiology_billing_id,patients.note as pnote')
            ->join("radiology_billing", "radiology_billing.id = transactions.radiology_billing_id")
            ->join("patients", "patients.id = radiology_billing.patient_id")
            ->where("transactions.radiology_billing_id", $radiology_billing_id)
            ->order_by("transactions.payment_date", "desc")
            ->get("transactions");
        return $query->result();
    }

    public function radiologyTotalPayments($radiology_billing_id)
    {
        $query = $this->db->select('IFNULL(sum(amount),0) as total_paid, radiology_billing.*')
            ->join("transactions", "radiology_billing.id = transactions.radiology_billing_id",'LEFT')
            ->join("patients", "patients.id = radiology_billing.patient_id")
            ->where("radiology_billing.id", $radiology_billing_id)
            ->order_by("transactions.payment_date", "desc")
            ->get("radiology_billing ");
        return $query->row();
    }

    public function bloodbankPayments($billing_id){
        $query = $this->db->select('transactions.*')
            ->where("transactions.blood_issue_id", $billing_id)
            ->order_by("transactions.payment_date", "desc")
            ->get("transactions");
        return $query->result();
    }

    public function getPaidAmountRadiology()
    {
        $query = $this->db->select('transactions.*,radiology_billing.id as radiology_billing_id,patients.note as pnote')
            ->join("radiology_billing", "radiology_billing.id = transactions.radiology_billing_id")
            ->join("patients", "patients.id = radiology_billing.patient_id")
            ->where("transactions.radiology_billing_id > 0")
            ->order_by("transactions.payment_date", "desc")
            ->get("transactions");
      
        return $query->result();
    }

    public function pathologyPaymentByTransactionId($transaction_id)
    {
        $query = $this->db->select('transactions.*,pathology_billing.id as pathology_billing_id,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.age,patients.month,patients.guardian_name,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address,staff.name,staff.surname,staff.employee_id')
            ->join("pathology_billing", "pathology_billing.id = transactions.pathology_billing_id")
            ->join("patients", "patients.id = pathology_billing.patient_id")
            ->join("staff", "staff.id = transactions.received_by",'LEFT')
            ->where("transactions.id", $transaction_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->row();
    }

    public function IPDPatientPayments($ipd_id)
    {
        $query = $this->db->select('transactions.*,patients.id as pid,patients.note as pnote')
            ->join("ipd_details", "ipd_details.id = transactions.ipd_id")
            ->join("patients", "patients.id = ipd_details.patient_id")
            ->where("transactions.ipd_id", $ipd_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->result_array();
    }

    public function OPDPatientPayments($opd_id)
    {
        $query = $this->db->select('transactions.*,patients.id as pid,patients.note as pnote')
            ->join("opd_details", "opd_details.id = transactions.opd_id and opd_details.case_reference_id=transactions.case_reference_id")
            ->join("patients", "patients.id = opd_details.patient_id")
            ->where("transactions.opd_id", $opd_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->result_array();
    }

    public function getPatientPaymentsByCaseId($case_id,$module_type,$id)
    {
        if($id!=='' && $id!==0){
            $this->db->where($module_type,$id);
        }

        $query = $this->db->select('transactions.*')
            ->where("transactions.case_reference_id", $case_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
            
        return $query->result_array();
    }
    
    public function deletePayment($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db->where('id', $id)
            ->delete('transactions');            
        $message = DELETE_RECORD_CONSTANT . " On Transactions id " . $id;
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

    public function getTransaction($id)
    {
        $sql = "SELECT transactions.*,opd_details.patient_id as `opd_patient_id`,opd_patient.patient_name as `opd_patient_name`, ipd_patient.patient_name as `ipd_patient_name`,ipd_details.patient_id as `ipd_patient_id` FROM `transactions` LEFT JOIN opd_details on opd_details.id=transactions.opd_id  LEFT JOIN ipd_details on ipd_details.id=transactions.ipd_id LEFT JOIN patients as `opd_patient` on opd_patient.id=opd_details.patient_id LEFT JOIN patients as `ipd_patient` on ipd_patient.id=ipd_details.patient_id WHERE transactions.id=" . $id;
        $query  = $this->db->query($sql);
        $result = $query->row();
        return $result;
    }

    public function getTransactionByCaseId($case_id)
    {
        $sql = "SELECT * FROM `transactions` WHERE case_reference_id =".$this->db->escape_str($case_id)." and type='payment'";
        $query  = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }

    public function getRefundByCaseId($case_id)
    {
        $sql = "SELECT * FROM `transactions` WHERE case_reference_id =".$this->db->escape_str($case_id)." and type='refund'";
        $query  = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }

    public function getTotalRefundAmountByCaseId($case_id)
    {
        $sql = "SELECT sum(amount) as payment_amount FROM `transactions` WHERE case_reference_id =".$this->db->escape_str($case_id)." and type='refund'";
        $query  = $this->db->query($sql);
        $result = $query->row();
        return $result;
    }

    public function getAlltransactionRecord()
    {
        $this->datatables
            ->select('transactions.*')
            ->searchable('transactions.id')
            ->orderable('transactions.id')
            ->sort('transactions.id', 'asc')
            ->from('transactions');
        return $this->datatables->generate('json');
    }
     public function getAllprocessingtransactionRecord()
    {
        $this->datatables
            ->select('transactions_processing.*,patients.patient_name,patients.id as patients_id')
            ->join('patients','patients.id=transactions_processing.patient_id','inner')
            ->searchable('transactions_processing.case_reference_id,transactions_processing.opd_id,transactions_processing.ipd_id,transactions_processing.pharmacy_bill_basic_id,transactions_processing.pathology_billing_id,transactions_processing.radiology_billing_id,transactions_processing.blood_donor_cycle_id,transactions_processing.blood_issue_id,transactions_processing.ambulance_call_id,transactions_processing.appointment_id,transactions_processing.amount,transactions_processing.payment_mode,transactions_processing.note,patients.patient_name')
            ->orderable('transactions_processing.case_reference_id,transactions_processing.opd_id,transactions_processing.ipd_id,transactions_processing.pharmacy_bill_basic_id,transactions_processing.pathology_billing_id,transactions_processing.radiology_billing_id,transactions_processing.blood_donor_cycle_id,transactions_processing.blood_issue_id,transactions_processing.ambulance_call_id,transactions_processing.appointment_id,transactions_processing.amount,transactions_processing.payment_mode,transactions_processing.note,patients.patient_name')
            ->sort('transactions_processing.id', 'asc')
            ->from('transactions_processing');
        return $this->datatables->generate('json');
    }
    public function transactionRecord($start_date, $end_date,$collect_staff=null) {

            $condition="" ;
            if($collect_staff!="")
            {
                $condition.=" and transactions.received_by=".$collect_staff  ;
            }

             $sql="select transactions.*, CASE 
                   WHEN (ipd_id IS NOT NULL ) THEN 'ipd_no'
                   WHEN (opd_id IS NOT NULL) THEN 'opd_no'       
                   WHEN (pharmacy_bill_basic_id IS NOT NULL) THEN 'pharmacy_billing'       
                   WHEN (pathology_billing_id IS NOT NULL) THEN 'pathology_billing'       
                   WHEN (radiology_billing_id IS NOT NULL) THEN 'radiology_billing'       
                   WHEN (blood_issue_id IS NOT NULL) THEN 'blood_bank_billing'       
                   WHEN (ambulance_call_id IS NOT NULL) THEN 'ambulance_call_billing'       
                END AS ward,
                CASE 
                   WHEN (ipd_id IS NOT NULL ) THEN ipd_id
                   WHEN (opd_id IS NOT NULL) THEN opd_id       
                   WHEN (pharmacy_bill_basic_id IS NOT NULL) THEN pharmacy_bill_basic_id       
                   WHEN (pathology_billing_id IS NOT NULL) THEN pathology_billing_id       
                   WHEN (radiology_billing_id IS NOT NULL) THEN radiology_billing_id       
                   WHEN (blood_issue_id IS NOT NULL) THEN blood_issue_id       
                   WHEN (ambulance_call_id IS NOT NULL) THEN ambulance_call_id       
                END AS reference,
                section,transactions.opd_id as module_id,patients.patient_name,patients.id as `patient_id`,'opd' head,'opd_no' module_no,staff.name,staff.surname,staff.employee_id from transactions LEFT JOIN ipd_details on ipd_details.id = transactions.ipd_id LEFT JOIN patients on patients.id = transactions.patient_id LEFT JOIN opd_details on opd_details.id = transactions.opd_id LEFT JOIN pharmacy_bill_basic on pharmacy_bill_basic.id = transactions. pharmacy_bill_basic_id LEFT JOIN pathology_billing on pathology_billing.id = transactions.pathology_billing_id LEFT JOIN radiology_billing on radiology_billing.id = transactions.radiology_billing_id LEFT JOIN blood_issue on blood_issue.id = transactions.blood_issue_id LEFT JOIN staff on staff.id = transactions.received_by where date_format(transactions.payment_date,'%Y-%m-%d') >='". $start_date."'and date_format(transactions.payment_date,'%Y-%m-%d') <= '".$end_date."' and  1=1 ".$condition." ";
               $this->datatables->query($sql) 
              ->searchable('transactions.id,patients.patient_name,patients.id,reference,transactions.payment_date,staff.name,staff.surname,staff.employee_id,transactions.type,transactions.payment_mode,transactions.case_reference_id,opd_id,ipd_id,pharmacy_bill_basic_id,pathology_billing_id,radiology_billing_id,transactions.blood_donor_cycle_id,blood_issue_id,ambulance_call_id,transactions.amount')
              ->orderable('transactions.id,transactions.payment_date,patients.patient_name,reference,ward,staff.name,transactions.type,transactions.payment_mode,transactions.amount')
              ->sort('transactions.payment_date','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }  

    //new running code
    public function opdpatientRecord($start_date, $end_date,$collect_staff=null) {

        $condition="";
        if($collect_staff!="")
        {
            $condition.=" and transactions.received_by=".$collect_staff  ;
        }
        
        $sql="select transactions.id,transactions.type,transactions.payment_mode,transactions.section,'opd' head,'opd_no' module_no,transactions.opd_id as module_id,transactions.payment_date,transactions.amount, patients.patient_name,patients.id as `patient_id`,staff.name,staff.surname,staff.employee_id from transactions LEFT JOIN opd_details on opd_details.id = transactions.opd_id LEFT JOIN patients on patients.id = opd_details.patient_id LEFT JOIN staff on staff.id = transactions.received_by where date_format(transactions.payment_date,'%Y-%m-%d') >='". $start_date."'and date_format(transactions.payment_date,'%Y-%m-%d') <= '".$end_date."' and transactions.opd_id is not null and 1=1 ".$condition ;
             $this->datatables->query($sql) 
              ->searchable('transactions.id,transactions.payment_date,patients.patient_name,section,staff.name,type,payment_mode,amount')
              ->orderable('transactions.id,transactions.payment_date,patients.patient_name,null,section,staff.name,type,payment_mode,amount')
              ->sort('transactions.payment_date','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    } 

    public function opdpatientreportRecord($data) {
       
        $custom_fields             = $this->customfield_model->get_custom_fields('opd','','',1);
        $custom_field_column_array = array();
        $field_var_array = array();
        $custom_join = NULL;
        $condition="";
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name."`");
                $custom_join .= (' LEFT JOIN custom_field_values as '.$tb_counter.' ON opd_details.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id);
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);        
        $search_custom_field_column=(implode(',', $custom_field_column_array));

        if(isset($data['gender']) && $data['gender']!=""){
            $condition.= "and patients.gender = '".$data['gender']."' " ;
        }
        
        if(isset($data['doctor']) && $data['doctor']!=""){
            $condition.= "and  staff.id = '".$data['doctor']."' " ;
        }
        
        if(isset($data['symptoms']) && $data['symptoms']!=""){
            $condition.= 'and  visit_details.symptoms like "%'.$data['symptoms'].'%" ' ;
        }
        
        if(isset($data['findings']) && $data['findings']!=""){
            $condition.= 'and  ipd_prescription_basic.finding_description like "%'.$data['findings'].'%"' ;
        }

        if(isset($data['from_age']) && $data['from_age']!="" ){          
            $condition.= " and patients.age >='".$data['from_age']."' " ;
        }

        if(isset($data['to_age']) && $data['to_age']!="" ){
             $condition.= " and patients.age <='".$data['to_age']."' " ;
        }

        if(isset($data['start_date']) && $data['start_date']!="" && isset($data['end_date']) && $data['end_date']!="" ){
            $start_date = $data['start_date'] ;
            $end_date = $data['end_date'] ;
            $condition.= " and  date_format(appointment_date,'%Y-%m-%d') >='". $start_date."' and date_format(appointment_date,'%Y-%m-%d') <= '".$end_date."' " ;
        }    

        if(isset($data['miscellaneous']) && $data['miscellaneous']!=""){
            if($data['miscellaneous']=="is_antenatal"){
                 $condition.= " and patients.is_antenatal=1  " ;
            }
           
        }  
       
        $sql="select opd_details.id,'opd_no' module_no,visit_details.id as visit_id,visit_details.symptoms,visit_details.appointment_date, ipd_prescription_basic.finding_description, patients.patient_name,patients.dob,patients.age,patients.month,patients.day,patients.gender,patients.mobileno,patients.guardian_name,patients.address,patients.is_antenatal,patients.id patientid,staff.name,staff.surname,staff.employee_id ".$field_variable." from opd_details 
        left join visit_details on  opd_details.id=visit_details.opd_details_id 
        left join ipd_prescription_basic on ipd_prescription_basic.visit_details_id = visit_details.id  
        left join patients on patients.id = opd_details.patient_id
        left join staff on staff.id = visit_details.cons_doctor"  .$custom_join ." where 0=0 ".$condition." " ;
             $this->datatables->query($sql) 
              ->searchable('patients.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address'.$custom_field_column)
              ->orderable('visit_details.appointment_date,opd_details.id,visit_details.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,staff.name,visit_details.symptoms,ipd_prescription_basic.finding_description'.$custom_field_column)
              ->sort('appointment_date','desc')
              ->query_where_enable(TRUE);

        return $this->datatables->generate('json');
    } 

    public function opdpatientbalanceRecord($start_date, $end_date,$from_age,$to_age,$gender,$discharged,$miscellaneous) {
       
       $condition="";
       if($from_age !=""){
         $condition.= " and patients.age >= '".$from_age."' ";
       }
	   
       if($to_age !=""){
         $condition.= " and patients.age <= '".$to_age."' ";
       }

       if($gender !="" ){
            $condition.=" and patients.gender= '".$gender."'  ";
       }
       if($discharged !="" ){
            $condition.=" and opd_details.discharged= '".$discharged."'  ";
       }

       if(isset($miscellaneous) && $miscellaneous!=""){
            if($miscellaneous=="is_antenatal"){
                 $condition.= " and patients.is_antenatal=1  " ;
            }           
       }  

        $sql="SELECT visit_details.appointment_date,opd_details.*,patients.id as patient_id,patients.is_antenatal,patients.patient_name,patients.guardian_name,patients.address,patients.gender,patients.dob,patients.age,patients.month,patients.day,patients.mobileno,patients.is_active,patients.age,patients.month,charge_amounts.amount_charged,transaction_amount.amount_paid FROM `opd_details` inner JOIN (select sum(amount) as amount_charged ,opd_id from patient_charges WHERE patient_charges.opd_id IS NOT NULL GROUP BY opd_id )  as charge_amounts on charge_amounts.opd_id=opd_details.id INNER JOIN (select sum(amount) as amount_paid ,opd_id from transactions WHERE transactions.opd_id IS NOT NULL GROUP BY opd_id) as transaction_amount on transaction_amount.opd_id=opd_details.id INNER JOIN patients ON opd_details.patient_id = patients.id inner join visit_details on visit_details.opd_details_id=opd_details.id where 0=0 ".$condition." and date_format(visit_details.appointment_date,'%Y-%m-%d') >='". $start_date."' and date_format(visit_details.appointment_date,'%Y-%m-%d') <= '".$end_date."'";
              $this->datatables->query($sql)
              ->searchable('patients.id,case_reference_id')
              ->orderable('opd_details.id,patients.patient_name,case_reference_id,patients.age,patients.gender,mobileno,patients.is_active,opd_details.discharged,charge_amounts.amount_charged,transaction_amount.amount_paid')
              ->sort('visit_details.appointment_date','desc')
              ->group_by('visit_details.opd_details_id', true)
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }    

    public function ipddischargedreportRecord($data) {

       $start_date = $data['start_date'];
       $end_date   = $data['end_date'];
       $gender     = $data['gender'];
       $discharged = $data['discharged'];

       $condition= "" ;

       if($gender !="" ){
            $condition.=" and patients.gender= '".$gender."'  ";
       }
       if($discharged !="" ){
            $condition.=" and discharge_card.discharge_status= '".$discharged."'  ";
       }

		if(isset($data['doctor']) && $data['doctor']!=""){
        $condition.= "and  staff.id = '".$data['doctor']."' " ;
        }
		
        if(isset($data['from_age']) && $data['from_age']!="" ){

        $condition.= " and patients.age >='".$data['from_age']."' " ;
        }

        if(isset($data['to_age']) && $data['to_age']!="" ){
         $condition.= " and patients.age <='".$data['to_age']."' " ;
        }
        
        if(isset($data['miscellaneous']) && $data['miscellaneous']!="" ){
            if($data['miscellaneous']=="is_antenatal"){
                $condition.= " and patients.is_antenatal=1 " ;
            }
        }

       $sql="select ipd_details.*,patients.is_antenatal,transactions.amount,discharge_card.discharge_date,discharge_card.discharge_status,patients.patient_name,patients.age, patients.month,patients.day,patients.gender,patients.mobileno,patients.guardian_name,patients.address,staff.name,patients.mobileno, patients.gender,staff.name,staff.surname,staff.employee_id,DATEDIFF(discharge_card.discharge_date,ipd_details.date) as admit_duration, IFNULL((select distinct(group_concat(staff.name,' ', staff.surname ) )
        from discharge_card ds
        left join ipd_doctors on ipd_doctors.ipd_id= ds.ipd_details_id left join staff on staff.id=ipd_doctors.consult_doctor
        where ds.ipd_details_id = discharge_card.ipd_details_id ),'') as doctors,

        IFNULL((select distinct(group_concat(bed.name ))
        from discharge_card as discard
        left join patient_bed_history  on patient_bed_history.case_reference_id = discard.case_reference_id
         left join bed on  (patient_bed_history.bed_group_id = bed.bed_group_id and patient_bed_history.bed_id = bed.id ) where discard.ipd_details_id = discharge_card.ipd_details_id group by patient_bed_history.id limit 1),'') as beds
        from discharge_card 
         inner JOIN ipd_details ON ipd_details.id = discharge_card.ipd_details_id
         left join patient_bed_history  on patient_bed_history.case_reference_id = discharge_card.case_reference_id
         left join bed on  (patient_bed_history.bed_group_id = bed.bed_group_id and patient_bed_history.bed_id = bed.id) 
        LEFT JOIN staff ON staff.id = ipd_details.cons_doctor
        LEFT JOIN patients ON patients.id = ipd_details.patient_id 
        LEFT JOIN transactions ON transactions.ipd_id = ipd_details.id 
         where 0=0 ".$condition."   and date_format(discharge_card.discharge_date,'%Y-%m-%d') >='". $start_date."' and date_format(discharge_card.discharge_date,'%Y-%m-%d') <= '".$end_date."' group by discharge_card.id  "; 

            $this->datatables->query($sql)
              ->searchable('patients.patient_name,ipd_details.id,ipd_details.case_reference_id')
              ->orderable('patients.patient_name,ipd_details.id,ipd_details.case_reference_id,patients.gender,patients.mobileno,staff.name,beds,ipd_details.date,discharge_card.discharge_date,discharge_card.discharge_status,admit_duration')
              ->sort('date_format(ipd_details.date, "%m/%e/%Y")','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    } 

    public function opddischargedreportRecord($data) {

       $start_date = $data['start_date'];
       $end_date   = $data['end_date'];
       $gender     = $data['gender'];
       $discharged = $data['discharged'];
       $condition= "" ;

        if($gender !="" ){
            $condition.=" and patients.gender= '".$gender."'  ";
        }
       
        if($discharged !="" ){
            $condition.=" and discharge_card.discharge_status= '".$discharged."'  ";
        }
       
        if(isset($data['doctor']) && $data['doctor']!=""){
            $condition.= "and  staff.id = '".$data['doctor']."' " ;
        }
        
        if(isset($data['from_age']) && $data['from_age']!="" ){
            $condition.= " and patients.age >='".$data['from_age']."' " ;
        }

        if(isset($data['to_age']) && $data['to_age']!="" ){
            $condition.= " and patients.age <='".$data['to_age']."' " ;
        }

         if(isset($data['miscellaneous']) && $data['miscellaneous']!=""){
            if($data['miscellaneous']=="is_antenatal"){
                 $condition.= " and patients.is_antenatal=1  " ;
            }           
       }   

        $sql="select opd_details.*,patients.is_antenatal,visit_details.appointment_date,discharge_card.discharge_date,discharge_card.discharge_status,patients.id as patient_id,patients.patient_name,patients.age, patients.month,patients.day,patients.gender,patients.mobileno,patients.guardian_name,patients.address,staff.name,patients.mobileno, patients.gender,staff.name,staff.surname,staff.employee_id,DATEDIFF(discharge_card.discharge_date,visit_details.appointment_date) as admit_duration
        from discharge_card 
        INNER JOIN opd_details ON opd_details.id = discharge_card.opd_details_id
        INNER JOIN visit_details ON opd_details.id = visit_details.opd_details_id
        LEFT JOIN staff ON staff.id = visit_details.cons_doctor
        LEFT JOIN patients ON patients.id = opd_details.patient_id 
         where 0=0 ".$condition."   and date_format(discharge_card.discharge_date,'%Y-%m-%d') >='". $start_date."' and date_format(discharge_card.discharge_date,'%Y-%m-%d') <= '".$end_date."' group by discharge_card.id  "; 

            $this->datatables->query($sql)
              ->searchable('patients.patient_name,opd_details.id,opd_details.case_reference_id,patients.gender,patients.mobileno,staff.name,visit_details.appointment_date,discharge_card.discharge_date,discharge_card.discharge_status')
              ->orderable('patients.patient_name,opd_details.id,opd_details.case_reference_id,patients.gender,patients.mobileno,staff.name,visit_details.appointment_date,discharge_card.discharge_date,discharge_card.discharge_status,admit_duration')
              ->sort('date_format(discharge_card.discharge_date, "%m/%e/%Y")','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }

    public function ipdpatientbalanceRecord($data) {
       $condition="  ";

       $start_date = $data['start_date'];
       $end_date   = $data['end_date'];
       $from_age   = $data['from_age'];
       $to_age     = $data['to_age'];
       $gender     = $data['gender'];
       $patient_status     = $data['patient_status'];

       if($from_age !="" && $to_age !=""){
            $condition=" and patients.age BETWEEN '".$from_age."' AND  '".$to_age."'  ";
       }

       if($patient_status != "all"){
            $condition.=" and discharged ='".$patient_status."'  ";
       }
       
       if($gender != ""){
            $condition.=" and patients.gender = '".$gender."'  ";
       }

       if(isset($data['miscellaneous']) && $data['miscellaneous']!=""){
            if($data['miscellaneous']=="is_antenatal"){
                $condition.= " and patients.is_antenatal=1  " ;   
            }          
        }

        $sql="SELECT ipd_details.*,patients.is_antenatal,patients.id as patient_id,patients.patient_name,patients.guardian_name,patients.address,patients.gender,patients.dob,patients.mobileno,patients.is_active,patients.age,patients.month,patients.day,patients.month,IFNULL(charge_amounts.amount_charged,0) as amount_charged,IFNULL(transaction_amount.amount_paid,0) as amount_paid  FROM `ipd_details` left JOIN (select sum(amount) as amount_charged ,ipd_id from patient_charges WHERE patient_charges.ipd_id IS NOT NULL GROUP BY ipd_id )  as charge_amounts on charge_amounts.ipd_id=ipd_details.id left JOIN (select sum(amount) as amount_paid ,ipd_id from transactions WHERE transactions.ipd_id IS NOT NULL GROUP BY ipd_id) as transaction_amount on transaction_amount.ipd_id=ipd_details.id INNER JOIN patients ON ipd_details.patient_id = patients.id where 0=0 ".$condition." and date_format(ipd_details.date,'%Y-%m-%d') >='". $start_date."' and date_format(ipd_details.date,'%Y-%m-%d') <= '".$end_date."'";
         $this->datatables->query($sql)
              ->searchable('ipd_details.id')
              ->orderable('ipd_details.id,ipd_details.case_reference_id,patients.patient_name,patients.age,patients.gender,patients.is_antenatal,patients.mobileno,patients.guardian_name,ipd_details.discharged,patients.is_active,charge_amounts.amount_charged,transaction_amount.amount_paid')
              ->sort('date_format(ipd_details.date, "%m/%e/%Y")','desc')
              ->query_where_enable(TRUE);

        return $this->datatables->generate('json');
    } 

    public function ipdpatientRecord($start_date, $end_date,$collect_staff=null) {

        $condition="" ;
        if($collect_staff!="")
        {
            $condition.=" and transactions.received_by=".$collect_staff  ;
        }
            
         $sql="select transactions.id,transactions.section,'ipd' head,'ipd_no' module_no,transactions.ipd_id as module_id,transactions.payment_date,transactions.type,transactions.payment_mode,transactions.amount, patients.patient_name,patients.id as patient_id,staff.name,staff.surname,staff.employee_id from transactions LEFT JOIN ipd_details on ipd_details.id = transactions.ipd_id LEFT JOIN patients on patients.id = ipd_details.patient_id LEFT JOIN staff on staff.id = transactions.received_by where date_format(transactions.payment_date,'%Y-%m-%d') >='". $start_date."'and date_format(transactions.payment_date,'%Y-%m-%d') <= '".$end_date."' and transactions.ipd_id is not null ".$condition ;
             $this->datatables->query($sql) 
              ->searchable('transactions.id,patients.patient_name,transactions.ipd_id')
              ->orderable('transactions.id,transactions.payment_date,patients.patient_name,transactions.ipd_id,head,transactions.received_by,transactions.type,transactions.payment_mode,transactions.amount')
              ->sort('transactions.payment_date','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }  
    
    public function ipdpatientreportsRecord($data) {
            
        $custom_fields               = $this->customfield_model->get_custom_fields('ipd','','',1);
        $custom_field_column_array   = array();
        $field_var_array             = array();
        $custom_join                 = NULL;
        $i                           = 1;
        $condition                   = "";       

        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $custom_join .= ('LEFT JOIN custom_field_values as '.$tb_counter.' ON ipd_details.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id." ");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
      
        if(isset($data['gender']) && $data['gender']!=""){
            $condition.= "and patients.gender = '".$data['gender']."' " ;
        }
        
        if(isset($data['doctor']) && $data['doctor']!=""){
            $condition.= "and  staff.id = '".$data['doctor']."' " ;
        }
        
        if(isset($data['symptoms']) && $data['symptoms']!=""){
            $condition.= 'and  ipd_prescription_basic.finding_description like "%'.$data['symptoms'].'%"' ;            
        }

        if(isset($data['findings']) && $data['findings']!=""){
            $condition.= 'and  ipd_prescription_basic.finding_description like "%'.$data['findings'].'%"' ;
        }

        if(isset($data['from_age']) && $data['from_age']!="" ){          
            $condition.= " and patients.age >='".$data['from_age']."' " ;
        }
       
        if(isset($data['to_age']) && $data['to_age']!="" ){
             $condition.= " and patients.age <='".$data['to_age']."' " ;
        }

        if(isset($data['start_date']) && $data['start_date']!="" && isset($data['end_date']) && $data['end_date']!="" ){
            $start_date = $data['start_date'] ;
            $end_date = $data['end_date'] ;
            $condition.= " and  date_format(ipd_details.date,'%Y-%m-%d') >='". $start_date."' and date_format(ipd_details.date,'%Y-%m-%d') <= '".$end_date."' " ;
        }

        if(isset($data['miscellaneous']) && $data['miscellaneous']!=""){
            if($data['miscellaneous']=="is_antenatal"){
                $condition.= " and  patients.is_antenatal=1 " ;   
            }          
        }
         
        $sql="select patients.is_antenatal,ipd_details.id,'ipd_no' module_no,ipd_details.symptoms,ipd_details.date, ipd_prescription_basic.finding_description, patients.patient_name,patients.dob,patients.age,patients.month,patients.day,patients.gender,patients.mobileno,patients.guardian_name,patients.address,patients.id patientid,staff.name,staff.surname,staff.employee_id ".$field_variable." from ipd_details 
        left join ipd_prescription_basic on ipd_prescription_basic.ipd_id = ipd_details.id  
        left join patients on patients.id = ipd_details.patient_id
        left join staff on staff.id = ipd_details.cons_doctor "  .$custom_join ." where 0=0 ".$condition." " ;
             $this->datatables->query($sql) 
              ->searchable('patients.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address,patients.is_antenatal'.$custom_field_column)
              ->orderable('ipd_details.date,ipd_details.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,staff.name,ipd_details.symptoms,ipd_prescription_basic.finding_description,patients.is_antenatal'.$custom_field_column)
              ->sort('date','desc')
              ->query_where_enable(TRUE);
              
        return $this->datatables->generate('json');
    }

    public function otreportsRecord($data) {

        $custom_fields = $this->customfield_model->get_custom_fields('operationtheatre', '','', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        $custom_join = NULL;
        $i = 1;

        $consultant_doctor        = $data['consultant_doctor'];        
        $start_date           = $data['start_date'];
        $end_date             = $data['end_date'];
        $condition            = "";

        if($data['operation_category'] && $data['operation_category']!="" ){
            $condition.= " and operation_category.id = '".$data['operation_category']."' " ;
        }
        
        if($data['operation_name'] && $data['operation_name']!="" ){
            $condition.= " and operation_theatre.operation_id = '".$data['operation_name']."' " ;
        }
        
        if($data['consultant_doctor'] && $data['consultant_doctor']!="" ){
            $condition.= " and operation_theatre.consultant_doctor = '".$consultant_doctor."' " ;
        }

        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $custom_join .= ('LEFT JOIN custom_field_values as '.$tb_counter.' ON operation_theatre.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id." ");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);            
         $sql="select operation.operation, operation_category.category, operation_theatre.*,staff.name,staff.surname,staff.employee_id ".$field_variable." from operation_theatre LEFT JOIN ipd_details on ipd_details.id = operation_theatre.ipd_details_id LEFT JOIN staff on staff.id = operation_theatre.consultant_doctor
            left join operation on operation_theatre.operation_id=operation.id 
            left join operation_category on operation_category.id=operation.category_id ".$custom_join." where 0=0 ".$condition." and date_format(operation_theatre.date,'%Y-%m-%d') >='".$start_date."'and date_format(operation_theatre.date,'%Y-%m-%d') <= '".$end_date."' " ;
             $this->datatables->query($sql) 
              ->searchable('operation_theatre.date,operation_theatre.id,operation_theatre.opd_details_id,operation_theatre.ipd_details_id,operation.operation, operation_category.category, staff.name,staff.surname,staff.employee_id,operation_theatre.consultant_doctor')
              ->orderable('operation_theatre.date,operation_theatre.id,operation_theatre.opd_details_id,operation_theatre.ipd_details_id,staff.name,operation_theatre.ass_consultant_1,operation.operation,operation_category.category,operation_theatre.consultant_doctor')
              ->sort('date_format(operation_theatre.date, "%m/%e/%Y")','desc')
              ->query_where_enable(TRUE);

        return $this->datatables->generate('json');
    } 
   
    public function pharmacybillRecord($start_date, $end_date, $collect_staff) {

            $condition="" ;
            if($collect_staff!="")
            {
                $condition.=" and transactions.received_by=".$collect_staff  ;
            }
  
			$sql="select transactions.id,transactions.type,transactions.payment_mode,transactions.section,'pharmacy' head,'pharmacy_billing' module_no,transactions.pharmacy_bill_basic_id as module_id,transactions.payment_date,transactions.amount, patients.patient_name,patients.id as patient_id,staff.name,staff.surname,staff.employee_id from transactions LEFT JOIN pharmacy_bill_basic on pharmacy_bill_basic.id = transactions.pharmacy_bill_basic_id LEFT JOIN patients on patients.id = pharmacy_bill_basic.patient_id LEFT JOIN staff on staff.id = transactions.received_by where date_format(transactions.payment_date,'%Y-%m-%d') >='". $start_date."'and date_format(transactions.payment_date,'%Y-%m-%d') <= '".$end_date."' and transactions.pharmacy_bill_basic_id is not null ".$condition;
            $this->datatables->query($sql) 
            ->searchable('transactions.id,patients.patient_name')
            ->orderable('transactions.id,patients.patient_name,transactions.pharmacy_bill_basic_id,head,transactions.payment_date,transactions.received_by,type,payment_mode,transactions.amount')
              ->sort('transactions.payment_date','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }  

    public function pharmacybillreportsRecord($start_date, $end_date,$searchdata=null) {
     
        $condition = "";
        $custom_fields             = $this->customfield_model->get_custom_fields('pharmacy','','',1);
        $custom_field_column_array = array();
        $field_var_array = array();
        $custom_join = NULL;
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $custom_join .= ('LEFT JOIN custom_field_values as '.$tb_counter.' ON pharmacy_bill_basic.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id." ");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $condition_payment_mode ="";

        if($searchdata['collect_staff']!=""){
            $condition = " and pharmacy_bill_basic.generated_by=".$searchdata['collect_staff'] ;
        }
       
        if($searchdata['from_age'] !=""){
            $condition.= " and patients.age >= '".$searchdata['from_age']."' ";
        }
        
        if($searchdata['to_age'] !=""){
            $condition.= " and patients.age <= '".$searchdata['to_age']."' ";
        }

        if($searchdata['gender'] !="" ){
            $condition.=" and patients.gender= '".$searchdata['gender']."'  ";
        }
        
        if($searchdata['payment_mode'] !="" ){
            $condition_payment_mode.=" and transactions.payment_mode= '".$searchdata['payment_mode']."'  ";
        }
        
        if($searchdata['doctor_name'] !="" ){
            $condition.=" and pharmacy_bill_basic.doctor_name= '".$searchdata['doctor_name']."'  ";
        } 
       
       $sql="SELECT `pharmacy_bill_basic`.*,ipd_prescription_basic.ipd_id,ipd_prescription_basic.visit_details_id, IFNULL((select sum(amount) as amount_paid from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type='payment' ".$condition_payment_mode." ), 0) as paid_amount, IFNULL((select sum(amount) as refund from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type='refund' ), 0) as refund_amount, `patients`.`patient_name`, `patients`.`id` as `pid`,patients.gender, patients.age,patients.month,patients.day,staff.name,staff.surname,staff.employee_id ".$field_variable." FROM `pharmacy_bill_basic` LEFT JOIN `patients` ON `patients`.`id` = `pharmacy_bill_basic`.`patient_id`  LEFT JOIN `ipd_prescription_basic` ON `ipd_prescription_basic`.`id` = `pharmacy_bill_basic`.`ipd_prescription_basic_id` LEFT JOIN staff on staff.id = pharmacy_bill_basic.generated_by  ".$custom_join." where DATE(pharmacy_bill_basic.date) >='". $start_date."' and DATE(pharmacy_bill_basic.date) <= '".$end_date."' ".$condition."";
             $this->datatables->query($sql) 
              ->searchable('pharmacy_bill_basic.id,patients.patient_name'.$custom_field_column)
              ->orderable('pharmacy_bill_basic.id,pharmacy_bill_basic.date,patients.patient_name,patients.age,patients.gender,pharmacy_bill_basic.ipd_prescription_basic_id,pharmacy_bill_basic.doctor_name,staff.name'.$custom_field_column.',pharmacy_bill_basic.net_amount,refund_amount,paid_amount')
              ->sort('pharmacy_bill_basic.date','desc')
              ->query_where_enable(TRUE);

        return $this->datatables->generate('json');
    }  

    public function pathologybillRecord($start_date, $end_date,$collect_staff) {
  
        $condition="" ;
        if($collect_staff!="")
        {
            $condition.=" and transactions.received_by=".$collect_staff  ;
        }

         $sql="select transactions.id,transactions.type,transactions.payment_mode,transactions.section,'pathology' head,'pathology_billing' module_no,transactions.pathology_billing_id as module_id,transactions.payment_date,transactions.amount, patients.patient_name, patients.id as `patient_id`,staff.name,staff.surname,staff.employee_id from transactions LEFT JOIN pathology_billing on pathology_billing.id = transactions.pathology_billing_id LEFT JOIN patients on patients.id = pathology_billing.patient_id LEFT JOIN staff on staff.id = transactions.received_by where date_format(transactions.payment_date,'%Y-%m-%d') >='". $start_date."'and date_format(transactions.payment_date,'%Y-%m-%d') <= '".$end_date."' and transactions.pathology_billing_id is not null ".$condition ;
             $this->datatables->query($sql) 
              ->searchable('transactions.id,patients.patient_name')
              ->orderable('transactions.id,patients.patient_name,transactions.pathology_billing_id,head,transactions.payment_date,transactions.received_by,type,payment_mode,transactions.amount')
              ->sort('transactions.payment_date','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    } 

    public function pathologybillreportsRecord($data) {
        
        $custom_fields             = $this->customfield_model->get_custom_fields('pathology','','',1);
        $custom_field_column_array = array();
        $field_var_array = array();
        $custom_join = NULL;
        $i                         = 1;

        $collect_staff = $data['collect_staff'];
        $test_name     = $data['test_name'];
        $start_date    = $data['start_date'];
        $end_date      = $data['end_date'];
        $condition     = "";

        if($data['test_name'] && $data['test_name']!="" ){
            $condition.= " and pathology_report.pathology_id = '".$data['test_name']."' " ;
        }
        if($data['collect_staff'] && $data['collect_staff']!="" ){
            $condition.= " and pathology_report.collection_specialist = '".$data['collect_staff']."' " ;
        }

        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as `' . $custom_fields_value->name."`");
                $custom_join .= ' LEFT JOIN custom_field_values as '.$tb_counter.' ON transactions.pathology_billing_id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id;
                $i++;
            }
        } 
      
        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array); 

         $sql="select pathology_report.tax_percentage,pathology_report.apply_charge, transactions.id,'pathology' head,'pathology_billing' module_no, pathology_category.category_name, pathology_billing.doctor_name, transactions.pathology_billing_id as module_id,transactions.payment_date,transactions.amount, patients.patient_name,patients.id as `patient_id`,staff.name,staff.surname,staff.employee_id,pathology.test_name,pathology.short_name,charges.standard_charge,( SELECT IFNULL(SUM(transactions.amount),0) from transactions WHERE transactions.pathology_billing_id=pathology_billing.id ) as paid_amount ".$field_variable." from pathology_billing 
             inner JOIN pathology_report on pathology_billing.id = pathology_report.pathology_bill_id 
               inner JOIN pathology on pathology.id = pathology_report.pathology_id
               inner join pathology_category on pathology_category.id = pathology.pathology_category_id
               inner join charges on pathology.charge_id = charges.id              
              LEFT JOIN transactions on pathology_billing.id = transactions.pathology_billing_id 
              LEFT JOIN patients on patients.id = pathology_billing.patient_id 
              LEFT JOIN staff on staff.id = pathology_report.collection_specialist ".$custom_join." 
              where 0=0 ".$condition." and  date_format(pathology_billing.date,'%Y-%m-%d')  >='". $start_date."'and date_format(pathology_billing.date,'%Y-%m-%d') <= '".$end_date."' " ;

             $this->datatables->query($sql) 
              ->searchable('transactions.id,patients.patient_name'.$custom_field_column)
              ->orderable('transactions.pathology_billing_id,transactions.payment_date,patients.patient_name,pathology.test_name,pathology_category.category_name, pathology_billing.doctor_name, staff.name ,pathology_billing.net_amount,( SELECT IFNULL(SUM(transactions.amount),0) from,pathology.test_name,pathology_billing.doctor_name,staff.name'.$custom_field_column.',pathology_billing.net_amount,( SELECT IFNULL(SUM(transactions.amount),0) from transactions WHERE transactions.pathology_billing_id=pathology_billing.id ) as paid_amount')
              ->sort('date_format(pathology_billing.date, "%Y-%m-%d")','desc')
              ->group_by('pathology_report.id')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }  
 
    public function radiologybillRecord($start_date, $end_date,$collect_staff) {

        $condition="" ;
        if($collect_staff!="")
        {
            $condition.=" and transactions.received_by=".$collect_staff  ;
        }
  
         $sql="select transactions.id,transactions.type,transactions.payment_mode,transactions.section,'radiology' head,'radiology_billing' module_no,transactions.radiology_billing_id as module_id,transactions.payment_date,transactions.amount, patients.patient_name,patients.id as patient_id,staff.name,staff.surname,staff.employee_id from transactions 
         LEFT JOIN radiology_billing on radiology_billing.id = transactions.radiology_billing_id 
         LEFT JOIN patients on patients.id = radiology_billing.patient_id 
         LEFT JOIN staff on staff.id = transactions.received_by 
         where date_format(transactions.payment_date,'%Y-%m-%d') >='". $start_date."'and date_format(transactions.payment_date,'%Y-%m-%d') <= '".$end_date."' and transactions.radiology_billing_id is not null ".$condition ;
             $this->datatables->query($sql) 
              ->searchable('transactions.id,patients.patient_name')
              ->orderable('transactions.id,patients.patient_name,transactions.radiology_billing_id,head,transactions.payment_date,transactions.received_by,type,payment_mode,transactions.amount')
              ->sort('transactions.payment_date','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }  

    public function getTransactionBetweenDate($start_date, $end_date,$transaction_type='all') {
        $this->db->select('transactions.*,staff.name,staff.surname,staff.employee_id')->from('transactions');    
        if ($transaction_type == "all") {
           
        }elseif ($transaction_type == "payment") {
           $this->db->where('transactions.type', $transaction_type);
        }elseif ($transaction_type == "refund") {
           $this->db->where('transactions.type', $transaction_type);
        }
        
        $this->db->join('staff', 'staff.id = transactions.received_by','left');
        $this->db->where('DATE(payment_date) >=', $start_date);
        $this->db->where('DATE(payment_date) <=', $end_date);
        $query = $this->db->get();
        return $query->result();
    }  

     public function radiologybillreportsRecord($data) {

            $collect_staff = $data['collect_staff'];
            $test_name     = $data['test_name'];
            $start_date    = $data['start_date'];
            $end_date      = $data['end_date'];
            $condition     = "";

            if($data['test_name'] && $data['test_name']!="" ){
                $condition.= " and radiology_report.radiology_id = '".$data['test_name']."' " ;
            }
            
            if($data['collect_staff'] && $data['collect_staff']!="" ){
                $condition.= " and radiology_report.collection_specialist = '".$data['collect_staff']."' " ;
            }
            
            if($data['radiology_category_id'] && $data['radiology_category_id']!="" ){
                $condition.= " and radio.radiology_category_id = '".$data['radiology_category_id']."' " ;
            }
        
            $custom_fields             = $this->customfield_model->get_custom_fields('radiology','','',1);
            $custom_field_column_array = array();
            $field_var_array = array();
            $custom_join = NULL;
            $i                         = 1;
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    $tb_counter = "table_custom_" . $i;
                    array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                    array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                    $custom_join .= (' LEFT JOIN custom_field_values as '.$tb_counter.' ON radiology_report.radiology_bill_id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id);
                    $i++;
                }
            }

            $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
            $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);  

            $sql="select  radiology_report.tax_percentage,lab.lab_name,'radiology' head,'radiology_billing' module_no,radiology_billing.patient_id,patients.patient_name,radiology_billing.note,radiology_billing.date,radiology_billing.doctor_name,radiology_report.radiology_bill_id as module_id,radiology_billing.net_amount,charges.standard_charge,staff.name,staff.surname,staff.employee_id,radio.test_name,radio.short_name".$field_variable." from radiology_billing 
             inner JOIN radiology_report on radiology_billing.id = radiology_report.radiology_bill_id 
               inner JOIN radio on radio.id = radiology_report.radiology_id
               inner join lab on lab.id = radio.radiology_category_id
               inner join charges on radio.charge_id = charges.id               
              
              LEFT JOIN patients on patients.id = radiology_billing.patient_id 
              LEFT JOIN staff on staff.id = radiology_report.collection_specialist ".$custom_join." 
              where 0=0 ".$condition." and  date_format(radiology_billing.date,'%Y-%m-%d')  >='". $start_date."'and date_format(radiology_billing.date,'%Y-%m-%d') <= '".$end_date."' " ;             
              $this->datatables->query($sql)  
              ->searchable('radiology_billing.date,patients.patient_name,lab.lab_name,radio.test_name,radiology_billing.note,radiology_billing.doctor_name,staff.name,charges.standard_charge,radiology_report.tax_percentage'.$custom_field_column)
              ->orderable('module_no,radiology_billing.date,patients.patient_name,lab.lab_name,radio.test_name,radiology_billing.note,radiology_billing.doctor_name,staff.name,head,charges.standard_charge,radiology_report.tax_percentage'.$custom_field_column)
              ->sort('radiology_billing.id','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    } 
   
    public function bloodissuebillRecordSearch($data) {  
            
        $i = 1;      
        $start_date           = $data['start_date'];
        $end_date             = $data['end_date'];
        $condition            = "";

        $custom_fields   = $this->customfield_model->get_custom_fields('blood_issue','','',1);
        $custom_field_column_array= array();
        $field_var_array = array();
           if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'blood_issue.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        } 

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        
        if(isset($data['blood_group']) && $data['blood_group']!="" ){
             $this->db->where('blood_bank_products.id',$data['blood_group']);
        }
        
        if(isset($data['blood_donor']) && $data['blood_donor']!="" ){
             $this->db->where('blood_donor.id',$data['blood_donor']);
        }
      
        if(isset($data['amount_collected_by']) && $data['amount_collected_by']!="" ){
             $this->db->where('transactions.received_by',$data['amount_collected_by']);
        }

        if(isset($data['blood_collected_by']) && $data['blood_collected_by']!="" ){
             $this->db->where('blood_issue.generated_by',$data['blood_collected_by']);
        }
        
        if(isset($data['start_date']) && $data['start_date']!="" ){
             $this->db->where('date_format(transactions.payment_date,"%Y-%m-%d") >= ',$start_date);
        }
        
        if(isset($data['end_date']) && $data['end_date']!="" ){
             $this->db->where('date_format(transactions.payment_date,"%Y-%m-%d") <= ',$end_date);
        }

         $this->datatables
            ->select('blood_issue.*,sum(transactions.amount) as paid_amount,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit,staff.name,staff.surname,staff.employee_id,blood_collected_by.name as blood_collected_by_name,blood_collected_by.surname as blood_collected_by_surname,transactions.section,transactions.type,transactions.payment_mode,transactions.payment_date,blood_collected_by.employee_id as blood_collected_by_employee_id'.$field_variable)
            ->join('patients', 'patients.id = blood_issue.patient_id')
            ->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id','left')
            ->join('transactions', 'transactions.blood_issue_id = blood_issue.id',"left")
            ->join('blood_donor','blood_donor_cycle.blood_donor_id = blood_donor.id','left')
            ->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id')
            ->join('staff','staff.id = transactions.received_by',"left") 
            ->join('staff as blood_collected_by','blood_collected_by.id = blood_issue.generated_by')			
            ->searchable('blood_issue.id,blood_issue.date_of_issue,patients.patient_name, blood_bank_products.name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_issue.amount,net_amount'.$field_variable)
            ->orderable('blood_issue.id,blood_issue.date_of_issue,patients.patient_name,blood_issue.patient_id,blood_group,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit,blood_collected_by_name,blood_collected_by_employee_id'.$field_variable,'net_amount,paid_amount')
            ->group_by('blood_issue.id')
            ->sort('blood_issue.id', 'desc')
            ->from('blood_issue');
        return $this->datatables->generate('json');
    } 
	
	public function bloodissuebillRecordReport($data) {
            
        $i = 1;      
        $start_date           = $data['start_date'];
        $end_date             = $data['end_date'];
        $condition            = "";

        $custom_fields   = $this->customfield_model->get_custom_fields('blood_issue','','',1);
        $custom_field_column_array= array();
        $field_var_array = array();
           if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'blood_issue.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        } 

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        
        if(isset($data['blood_group']) && $data['blood_group']!="" ){
             $this->db->where('blood_bank_products.id',$data['blood_group']);
        }
        
        if(isset($data['blood_donor']) && $data['blood_donor']!="" ){
             $this->db->where('blood_donor.id',$data['blood_donor']);
        }
      
        if(isset($data['amount_collected_by']) && $data['amount_collected_by']!="" ){
             $this->db->where('transactions.received_by',$data['amount_collected_by']);
        }

        if(isset($data['blood_collected_by']) && $data['blood_collected_by']!="" ){
             $this->db->where('blood_issue.generated_by',$data['blood_collected_by']);
        }
        
        if(isset($data['start_date']) && $data['start_date']!="" ){
             $this->db->where('date_format(transactions.payment_date,"%Y-%m-%d") >= ',$start_date);
        }
        
        if(isset($data['end_date']) && $data['end_date']!="" ){
             $this->db->where('date_format(transactions.payment_date,"%Y-%m-%d") <= ',$end_date);
        }

         $this->datatables
            ->select('blood_issue.id,transactions.amount,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit,staff.name,staff.surname,staff.employee_id,blood_collected_by.name as blood_collected_by_name,blood_collected_by.surname as blood_collected_by_surname,transactions.section,transactions.type,transactions.payment_mode,transactions.payment_date,blood_collected_by.employee_id as blood_collected_by_employee_id'.$field_variable)
            ->join('patients', 'patients.id = blood_issue.patient_id')
            ->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id','left')
            ->join('transactions', 'transactions.blood_issue_id = blood_issue.id',"left")
            ->join('blood_donor','blood_donor_cycle.blood_donor_id = blood_donor.id','left')           
            ->join('staff','staff.id = transactions.received_by',"left") 
            ->join('staff as blood_collected_by','blood_collected_by.id = blood_issue.generated_by')
            ->searchable('blood_issue.id,blood_issue.date_of_issue,patients.patient_name,  patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,transactions.amount,net_amount'.$field_variable)
            ->orderable('blood_issue.id,blood_issue.date_of_issue,patients.patient_name,blood_bank_products.name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,name,net_amount,paid_amount'.$field_variable)
            ->sort('blood_issue.id', 'desc')
            ->from('blood_issue');
        return $this->datatables->generate('json');
    }

    public function blooddonorRecord($data) {
        $i = 1; 
        $start_date           = $data['start_date'];
        $end_date             = $data['end_date'];
        $condition            = "";
        $custom_fields   = $this->customfield_model->get_custom_fields('donor','','',1);
        $custom_field_column_array= array();
        $field_var_array = array();
           if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'blood_donor_cycle.blood_donor_id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        } 

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        if($data['blood_group'] && $data['blood_group']!="" ){
             $this->datatables->where('blood_bank_products.id',$data['blood_group']);
        }
        if($data['blood_donor'] && $data['blood_donor']!="" ){
            // $this->datatables->where('blood_donor.id',$data['blood_donor']); 
        }

        $this->datatables
            ->select('blood_donor_cycle.*,blood_bank_products.name as blood_group, blood_donor.id as blood_donor_id,blood_donor.donor_name,blood_donor.gender,blood_donor.date_of_birth, blood_donor.created_at as donor_created_date,charge_units.unit as unit_name,charge_categories.name as charge_category_name,charges.charge_category_id,charges.standard_charge,charges.name as `charge_name`,transactions.amount as `paid_amount`,`transactions`.`attachment`,`transactions`.`attachment_name`,`transactions`.`payment_mode`,`transactions`.`cheque_no`,`transactions`.`cheque_date`,`transactions`.`payment_date`,transactions.id as tran_id'.$field_variable)
            ->join('blood_donor','blood_donor_cycle.blood_donor_id = blood_donor.id','inner')
            ->join('charge_units','blood_donor_cycle.unit = charge_units.id','left')
            ->join('charges','blood_donor_cycle.charge_id = charges.id','inner')
            ->join('charge_categories','charge_categories.id = charges.charge_category_id','left')
            ->join('charge_type_master','charge_categories.charge_type_id = charge_type_master.id','left') 
            ->join('transactions','transactions.blood_donor_cycle_id = blood_donor_cycle.id') 
            ->join('blood_bank_products','blood_bank_products.id = blood_donor.blood_bank_product_id') 
            ->where('date_format(blood_donor_cycle.donate_date,"%Y-%m-%d") >=',$start_date)
            ->where('date_format(blood_donor_cycle.donate_date,"%Y-%m-%d") <=',$end_date) 
			
			
			  
			
            ->searchable('blood_donor_cycle.donate_date, blood_donor.donor_name, blood_donor.date_of_birth, blood_bank_products.name, blood_donor.gender, blood_donor_cycle.lot, blood_donor_cycle.bag_no, blood_donor_cycle.quantity'.$field_variable)
			
            ->orderable('blood_group,blood_donor_cycle.donate_date,blood_bank_products.name,blood_donor_cycle.bag_no,blood_donor.donor_name, blood_donor.date_of_birth, blood_donor_cycle.apply_charge,blood_donor_cycle.discount_percentage,blood_donor_cycle.tax_percentage,blood_donor_cycle.amount,paid_amount'.$field_variable)
			
            ->sort('blood_donor_cycle.donate_date', 'desc')
            ->from('blood_donor_cycle');
       return $this->datatables->generate('json');
    }

    public function ambulancecallRecord($data) {
  
            $custom_fields             = $this->customfield_model->get_custom_fields('ambulance_call','','',1);
            $custom_field_column_array = array();
            $field_var_array           = array();
            $custom_join               = NULL;
            $i                         = 1;
            $start_date                = $data['start_date'];
            $end_date                  = $data['end_date'];
            $condition                 = "";

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    $tb_counter = "table_custom_" . $i;
                    array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                    array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                    $custom_join .= ('LEFT JOIN custom_field_values as '.$tb_counter.' ON ambulance_call.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id." ");
                    $i++;
                }
            }

            $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
            $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);

            if(isset($data['vehicle_number']) && $data['vehicle_number']!=""){
                $condition.= " and vehicles.id = ".$data['vehicle_number'] ;
            }
            
            if(isset($data['collect_staff']) && $data['collect_staff']!="" ){
                $condition.= " and transactions.received_by= ".$data['collect_staff'] ;
            }
            
            if(isset($data['generated_staff']) && $data['generated_staff']!="" ){
                $condition.= " and ambulance_call.generated_by= ".$data['generated_staff'] ;
            }
            
            if(isset($start_date) && $start_date!="" ){
                $condition.= " and date_format(transactions.payment_date,'%Y-%m-%d') >='". $start_date."' " ;
            }
            
            if(isset($end_date) && $end_date!="" ){
               $condition.= " and date_format(transactions.payment_date,'%Y-%m-%d') <='". $end_date."' " ;
            }

            $sql="select ambulance_call.*,ambulance_call.id as module_id,'Ambulance Call' head,'ambulance_call_billing' module_no,null as section, vehicles.vehicle_no,vehicles.vehicle_model,staff.employee_id,staff.name,staff.surname,patients.patient_name,patients.id as `patient_id`,patients.mobileno,patients.address,transactions.amount as amount,transactions.type,transactions.payment_mode,transactions.payment_date".$field_variable." from ambulance_call LEFT JOIN vehicles ON vehicles.id = ambulance_call.vehicle_id LEFT JOIN staff ON staff.id = ambulance_call.generated_by LEFT JOIN patients ON patients.id = ambulance_call.patient_id left JOIN transactions ON transactions.ambulance_call_id = ambulance_call.id   ".$custom_join." where 0=0 ".$condition."  " ;
            $this->datatables->query($sql) 
              ->searchable('ambulance_call.id,patients.patient_name,ambulance_call.date,ambulance_call.contact_no,vehicles.vehicle_no,vehicles.vehicle_model,ambulance_call.driver'.$custom_field_column)
              ->orderable('ambulance_call.id,patients.patient_name,ambulance_call.date,ambulance_call.contact_no,vehicles.vehicle_no,vehicles.vehicle_model,ambulance_call.driver,patients.address'.$custom_field_column.',net_amount,paid_amount')
              ->sort('ambulance_call.date','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    } 
	
	public function ambulancecallRecordReport($data) {
  
            $custom_fields             = $this->customfield_model->get_custom_fields('ambulance_call','','',1);
            $custom_field_column_array = array();
            $field_var_array           = array();
            $custom_join               = NULL;
            $i                         = 1;
            $start_date                = $data['start_date'];
            $end_date                  = $data['end_date'];
            $condition                 = "";

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    $tb_counter = "table_custom_" . $i;
                    array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                    array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                    $custom_join .= ('LEFT JOIN custom_field_values as '.$tb_counter.' ON ambulance_call.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id." ");
                    $i++;
                }
            }

            $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
            $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);

            if(isset($data['vehicle_number']) && $data['vehicle_number']!=""){
                $condition.= " and vehicles.id = ".$data['vehicle_number'] ;
            }
            
            if(isset($data['collect_staff']) && $data['collect_staff']!="" ){
                $condition.= " and transactions.received_by= ".$data['collect_staff'] ;
            }
            
            if(isset($data['generated_staff']) && $data['generated_staff']!="" ){
                $condition.= " and ambulance_call.generated_by= ".$data['generated_staff'] ;
            }
            
            if(isset($start_date) && $start_date!="" ){
                $condition.= " and date_format(ambulance_call.date,'%Y-%m-%d') >='". $start_date."' " ;
            }
            
            if(isset($end_date) && $end_date!="" ){
               $condition.= " and date_format(ambulance_call.date,'%Y-%m-%d') <='". $end_date."' " ;
            }

            $sql="select ambulance_call.*,ambulance_call.id as module_id,'Ambulance Call' head,'ambulance_call_billing' module_no,null as section, vehicles.vehicle_no,vehicles.vehicle_model,staff.employee_id,staff.name,staff.surname,patients.patient_name,patients.id as `patient_id`,patients.mobileno,patients.address,transactions.amount as paid_amount,transactions.type,transactions.payment_mode,transactions.payment_date".$field_variable." from ambulance_call LEFT JOIN vehicles ON vehicles.id = ambulance_call.vehicle_id LEFT JOIN staff ON staff.id = ambulance_call.generated_by LEFT JOIN patients ON patients.id = ambulance_call.patient_id left JOIN transactions ON transactions.ambulance_call_id = ambulance_call.id   ".$custom_join." where 0=0 ".$condition."  " ;
            $this->datatables->query($sql) 
              ->searchable('ambulance_call.id,patients.patient_name,ambulance_call.date,ambulance_call.contact_no,vehicles.vehicle_no,vehicles.vehicle_model,ambulance_call.driver'.$custom_field_column)
              ->orderable('ambulance_call.id,patients.patient_name,ambulance_call.date,ambulance_call.contact_no,vehicles.vehicle_no,vehicles.vehicle_model,ambulance_call.driver,patients.address'.$custom_field_column.',net_amount,paid_amount')
              ->sort('ambulance_call.date','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }
	
    public function incomeRecord($start_date, $end_date,$collect_staff=null) {

        $condition="";
        if(isset($collect_staff) && $collect_staff!=""){
            $condition.=" and income.generated_by=".$collect_staff ;
        }
  
         $sql="select income.id,income.id as module_id,'income' head, '' type, '' payment_mode,income.amount,income.date as payment_date, income.name as patient_name,staff.name,staff.surname,staff.employee_id,null as section from income  LEFT JOIN staff on staff.id = income.generated_by where 1=1 ".$condition." and income.date >='". $start_date."'and income.date <= '".$end_date."'   " ;
             $this->datatables->query($sql) 
              ->searchable('income.id,patients.patient_name')
              ->orderable('income.id,income.date,income.name,null,null,staff.name,null,null,income.amount')
              ->sort('income.date','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }

    public function incomereportRecord($start_date, $end_date) {
            $custom_fields             = $this->customfield_model->get_custom_fields('income','','',1);
            $custom_field_column_array = array();
            $field_var_array = array();
            $custom_join = "";
            $i                         = 1;
           if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $custom_join .= ('LEFT JOIN custom_field_values as '.$tb_counter.' ON income.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id." ");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);

         $sql="select income.id,income.invoice_no,income.name as invoice_name,income.amount,income.date as payment_date, income.name,income_head.income_category,staff.name,staff.surname,staff.employee_id ".$field_variable." from income LEFT JOIN income_head on income_head.id = income.inc_head_id LEFT JOIN staff on staff.id = income.generated_by ".$custom_join." where date_format(income.date,'%Y-%m-%d') >='". $start_date."'and date_format(income.date,'%Y-%m-%d') <= '".$end_date."'" ;
             $this->datatables->query($sql) 
              ->searchable('income.name,income.invoice_no,income_head.income_category,income.date '.$custom_field_column.',income.id')
              ->orderable('income.name,income.invoice_no,income_head.income_category,income.date '.$custom_field_column.',income.id')
              ->sort('date_format(income.date, "%m/%e/%Y")','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }  

    public function expensesRecord($start_date, $end_date,$collect_staff=null) {

        $condition="";
        if(isset($collect_staff) && $collect_staff!=""){
            $condition.=" and expenses.generated_by=".$collect_staff ;
        }
      
         $sql ="select expenses.id,expenses.id as module_id,'expenses' head,'' type, '' payment_mode,'' patient_name,'' pathology_id,expenses.amount,expenses.date as payment_date, expenses.name,staff.name,staff.surname,staff.employee_id, null as section from expenses  LEFT JOIN staff on staff.id = expenses.generated_by where 1=1 ".$condition." and expenses.date >='". $start_date."'and expenses.date <= '".$end_date."' ";
             $this->datatables->query($sql) 
              ->searchable('expenses.id')
              ->orderable('expenses.id,expenses.date,expenses.name,null,null,staff.name,null,null,expenses.amount')
              ->sort('expenses.date','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    } 

    //new function
    public function expensereportRecord($start_date, $end_date) {
        
        $custom_fields             = $this->customfield_model->get_custom_fields('expenses','','',1);
        $custom_field_column_array = array();
        $field_var_array = array();
        $custom_join = NULL;
        $i                         = 1;
           
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $custom_join .= ('LEFT JOIN custom_field_values as '.$tb_counter.' ON expenses.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id." ");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        
         $sql="select expenses.id,expenses.invoice_no,expenses.amount,expense_head.exp_category,expenses.date as payment_date, expenses.name as expense_name,staff.name,staff.surname ".$field_variable."  from expenses LEFT JOIN expense_head on expense_head.id = expenses.exp_head_id  LEFT JOIN staff on staff.id = expenses.generated_by ".$custom_join." where date_format(expenses.date,'%Y-%m-%d') >='". $start_date."'and date_format(expenses.date,'%Y-%m-%d') <= '".$end_date."'" ;
             $this->datatables->query($sql) 
              ->searchable('expenses.name,expenses.invoice_no,expense_head.exp_category,expenses.date'.$custom_field_column)
              ->orderable('expenses.name,expenses.invoice_no,expense_head.exp_category,expenses.date'.$custom_field_column)
              ->sort('date_format(expenses.date, "%m/%e/%Y")','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    } 
    
    public function payrollRecord($start_date, $end_date,$collect_staff=null) {

        $condition="";
        if(isset($collect_staff) && $collect_staff!=""){
            $condition.=" and staff_payslip.generated_by=".$collect_staff ;
        }
  
         $sql="select staff_payslip.id,staff_payslip.id as module_id,'payroll' head,'' type, '' payment_mode,staff.name,staff.surname,staff.employee_id,staff_payslip.payment_date,staff_payslip.net_salary as amount,staff_payslip.generated_by, CONCAT(st.name,' ',st.surname,' (',st.employee_id,')') as patient_name, null as section from staff_payslip LEFT JOIN staff on staff.id = staff_payslip.generated_by LEFT JOIN staff as st on st.id = staff_payslip.staff_id where 1=1 ".$condition." and  date_format(staff_payslip.payment_date,'%Y-%m-%d') >='". $start_date."' and  date_format(staff_payslip.payment_date,'%Y-%m-%d') <= '".$end_date."' " ;
             $this->datatables->query($sql) 
              ->searchable('staff_payslip.id,payment_date,st.name,payment_mode')
              ->orderable('staff_payslip.id,payment_date,patient_name,null,null,name,null,payment_mode,net_salary')
              ->sort('staff_payslip.payment_date','desc')
              ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    } 

    public function get_ipdopdchargebycaseId($case_id){
         return $query = $this->db->select('patient_charges.*,charge_categories.name as charge_category_name,charges.charge_category_id,charges.standard_charge,charges.name as `charge_name`,charge_units.unit,charge_type_master.id as `charge_type_master_id`,ipd_details.patient_id as `ipd_patient_id`,ipd_patient.patient_name as `ipd_patient_name`,opd_details.patient_id as `opd_patient_id`,opd_patient.patient_name as `opd_patient_name`,opd_details.case_reference_id as `opd_case_reference_id`,ipd_details.case_reference_id as `ipd_case_reference_id`,tax_category.name as apply_tax,tax_category.percentage')
            ->join('opd_details', 'patient_charges.opd_id = opd_details.id','left')
            ->join('patients as opd_patient', 'opd_details.patient_id = opd_patient.id','left')
            ->join('ipd_details', 'patient_charges.ipd_id = ipd_details.id','left')
            ->join('patients as ipd_patient', 'ipd_details.patient_id = ipd_patient.id','left')
            ->join('charges', 'patient_charges.charge_id = charges.id', 'inner')
             ->join('tax_category', 'charges.tax_category_id = tax_category.id', 'left')
            ->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner')
            ->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id')
            ->join('charge_units', 'charges.charge_unit_id = charge_units.id', 'left')           
            ->where('ipd_details.case_reference_id', $case_id)
            ->or_where('opd_details.case_reference_id', $case_id)
            ->get('patient_charges')->result_array();       
    }  

    public function add($data)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('transactions', $data);            
            $message = UPDATE_RECORD_CONSTANT . " On Transactions id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
            return  $record_id ;            
        } else {
            $this->db->insert('transactions', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Transactions id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
            return  $record_id ;
        }        
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db->where('id', $id)
            ->delete('transactions');
        
        $message = DELETE_RECORD_CONSTANT . " On Transactions id " . $id;
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

    public function bloodbankPaymentByTransactionId($transaction_id){
        $query = $this->db->select('transactions.*,blood_issue.id as blood_issue_id,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.age,patients.month,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address, staff.name, staff.surname,staff.employee_id')
            ->join("blood_issue", "blood_issue.id = transactions.blood_issue_id")
            ->join("patients", "patients.id = blood_issue.patient_id")
            ->join("staff" , "staff.id=transactions.received_by","left")
            ->where("transactions.id", $transaction_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->row();
    }

    public function donorPaymentByTransactionId($transaction_id){
        $query = $this->db->select('transactions.*,staff.name, staff.surname, staff.employee_id')
            ->join("staff" , "staff.id=transactions.received_by","left")
            ->where("transactions.id", $transaction_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->row();
    }
 
    public function getopdIpdrefundbyCaseId($case_id){
        $query = $this->db->select('*')
               ->group_start()     
            ->or_where('transactions.opd_id !=',null)
            ->or_where('transactions.ipd_id !=',null)
             ->group_end()   
             ->where("transactions.case_reference_id", $case_id) 
              ->where("transactions.type", 'refund')         
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->row_array();
    }

    public function ambulanceCallPayments($billing_id){
        $query = $this->db->select('transactions.*')
            ->where("transactions.ambulance_call_id", $billing_id)
            ->order_by("transactions.payment_date", "desc")
            ->get("transactions");
        return $query->result();
    }

    public function ambulanceCallPaymentByTransactionId($transaction_id)
    {
        $query = $this->db->select('transactions.*,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.age,patients.month,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id')
            ->join("ambulance_call", "ambulance_call.id = transactions.ambulance_call_id")
            ->join("patients", "patients.id = ambulance_call.patient_id")
            ->join("staff", "staff.id = transactions.received_by","left")            
            ->where("transactions.id", $transaction_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->row();
    }

    public function pharmacypaymentbybillid($bill_id,$patient_id)
    {
        $query = $this->db->select('transactions.*,pharmacy_bill_basic.id as pharmacy_bill_basic_id,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.guardian_name,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address')
            ->join("pharmacy_bill_basic", "pharmacy_bill_basic.id = transactions.pharmacy_bill_basic_id")
            ->join("patients", "patients.id = pharmacy_bill_basic.patient_id")
            ->where("pharmacy_bill_basic_id", $bill_id)
            ->where("transactions.patient_id", $patient_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
        return $query->result_array();
    }
    
    public function pathologypaymentbybillid($bill_id,$patient_id)
    {
        $query = $this->db->select('transactions.*,pathology_billing.id as pathology_billing_id,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.guardian_name,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address')
        ->join("pathology_billing", "pathology_billing.id = transactions.pathology_billing_id")
        ->join("patients", "patients.id = pathology_billing.patient_id")
        ->where("pathology_billing_id", $bill_id)
        ->where("transactions.patient_id", $patient_id)
        ->order_by("transactions.id", "desc")
        ->get("transactions");
        return $query->result_array();
    }

    public function radiologypaymentbybillid($bill_id,$patient_id)
    {
        $query = $this->db->select('transactions.*,radiology_billing.id as radiology_billing_id,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.guardian_name,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address')
            ->join("radiology_billing", "radiology_billing.id = transactions.radiology_billing_id")
            ->join("patients", "patients.id = radiology_billing.patient_id")
            ->where("radiology_billing_id", $bill_id)
             ->where("transactions.patient_id", $patient_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
            return $query->result_array();
    }

    public function ambulancepaymentbybillid($bill_id,$patient_id)
    {
        $query = $this->db->select('transactions.*,ambulance_call.id as ambulance_call_id,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.guardian_name,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address')
            ->join("ambulance_call", "ambulance_call.id = transactions.ambulance_call_id")
            ->join("patients", "patients.id = ambulance_call.patient_id")
            ->where("ambulance_call_id", $bill_id)
             ->where("transactions.patient_id", $patient_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
            return $query->result_array();
    }

    public function bloodissuepaymentbybillid($bill_id,$patient_id)
    { 
        $query = $this->db->select('transactions.*,blood_issue.id as blood_issue_id,patients.note as pnote,patients.id as patient_id,patients.patient_name,patients.guardian_name,patients.gender,patients.id as patient_unique_id,patients.mobileno,patients.email,patients.dob,patients.image,patients.address')
            ->join("blood_issue", "blood_issue.id = transactions.blood_issue_id")
            ->join("patients", "patients.id = blood_issue.patient_id")
            ->where("blood_issue_id", $bill_id)
             ->where("transactions.patient_id", $patient_id)
            ->order_by("transactions.id", "desc")
            ->get("transactions");
            return $query->result_array();
    }

    public function get_monthTransaction($where)
    {
        $result=$this->db->select('sum(amount) as total')->from('transactions');
        $this->db->where($where);
        $result=$this->db->get()->row_array();
        if($result['total']>0){
            return $result['total'];
        }else{
            return 0;
        }        
    }

     public function validate_paymentamount()
     {
        $final_amount=0 ;
        $amount = $this->input->post('amount');
        $payment_amount = $this->input->post('payment_amount');
        if(!empty($amount)){
            $final_amount = $amount;
        }else if(!empty($payment_amount)){
            $final_amount = $payment_amount;
        }

        $net_amount    = $this->input->post('net_amount') ;
        if($final_amount > $net_amount ){         
            $this->form_validation->set_message('check_exists', $this->lang->line('amount_should_not_be_greater_than_balance').' '. number_format((float)$net_amount, 2, '.', ''));
            return false;
        }else{          
            return true;
        }        
    } 
    
    public function getTransactionByAppointmentId($appointment_id){
        $result = $this->db->select("*")
            ->where("transactions.appointment_id",$appointment_id)
            ->get("transactions")
            ->row();
        return $result;
    }  
 
    public function ipd_bill_paymentbycase_id($case_id){
        $ipd_bill_payment['ipd']['bill']=$this->db->select('sum(amount) as total_bill')->from('ipd_details')->join('patient_charges','patient_charges.ipd_id=ipd_details.id')->where('ipd_details.case_reference_id',$case_id)->get()->row_array();
        $ipd_bill_payment['ipd']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'ipd_id !='=>'NULL'))->get()->row_array();
        $ipd_bill_payment['pharmacy']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('pharmacy_bill_basic')->where('pharmacy_bill_basic.case_reference_id',$case_id)->get()->row_array();
        $ipd_bill_payment['pharmacy']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pharmacy_bill_basic_id !='=>'NULL'))->get()->row_array();
        $ipd_bill_payment['pharmacy']['payment_refund']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pharmacy_bill_basic_id !='=>'NULL','type'=>'refund'))->get()->row_array();
        $ipd_bill_payment['pathology']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('pathology_billing')->where('pathology_billing.case_reference_id',$case_id)->get()->row_array();
        $ipd_bill_payment['pathology']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pathology_billing_id !='=>'NULL'))->get()->row_array();
        $ipd_bill_payment['radiology']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('radiology_billing')->where('radiology_billing.case_reference_id',$case_id)->get()->row_array();
        $ipd_bill_payment['radiology']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'radiology_billing_id !='=>'NULL'))->get()->row_array();
        $ipd_bill_payment['blood_bank']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('blood_issue')->where('blood_issue.case_reference_id',$case_id)->get()->row_array();
        $ipd_bill_payment['blood_bank']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'blood_issue_id !='=>'NULL'))->get()->row_array();
        $ipd_bill_payment['ambulance']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('ambulance_call')->where('ambulance_call.case_reference_id',$case_id)->get()->row_array();
        $ipd_bill_payment['ambulance']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'ambulance_call_id !='=>'NULL'))->get()->row_array();       
        $ipd_bill_payment['ipd']['ipd_bill_payment_ratio']=cal_percentage($ipd_bill_payment['ipd']['payment']['total_payment'],$ipd_bill_payment['ipd']['bill']['total_bill']);
        $pharmacy_payment=$ipd_bill_payment['pharmacy']['payment']['total_payment']-$ipd_bill_payment['pharmacy']['payment_refund']['total_payment'];
        $ipd_bill_payment['pharmacy']['pharmacy_bill_payment_ratio']=cal_percentage($pharmacy_payment,$ipd_bill_payment['pharmacy']['bill']['total_bill']);
        $ipd_bill_payment['pathology']['pathology_bill_payment_ratio']=cal_percentage($ipd_bill_payment['pathology']['payment']['total_payment'],$ipd_bill_payment['pathology']['bill']['total_bill']);
        $ipd_bill_payment['radiology']['radiology_bill_payment_ratio']=cal_percentage($ipd_bill_payment['radiology']['payment']['total_payment'],$ipd_bill_payment['radiology']['bill']['total_bill']);
        $ipd_bill_payment['blood_bank']['blood_bank_bill_payment_ratio']=cal_percentage($ipd_bill_payment['blood_bank']['payment']['total_payment'],$ipd_bill_payment['blood_bank']['bill']['total_bill']);
        $ipd_bill_payment['ambulance']['ambulance_bill_payment_ratio']=cal_percentage($ipd_bill_payment['ambulance']['payment']['total_payment'],$ipd_bill_payment['ambulance']['bill']['total_bill']);
        $ipd_bill_payment['ipd']['ipd_bill_balance']=$this->calculate_balance($ipd_bill_payment['ipd']['bill']['total_bill'],$ipd_bill_payment['ipd']['payment']['total_payment']);
        $ipd_bill_payment['pharmacy']['pharmacy_bill_balance']=$this->calculate_balance($ipd_bill_payment['pharmacy']['bill']['total_bill'],$pharmacy_payment);
        $ipd_bill_payment['pathology']['pathology_bill_balance']=$this->calculate_balance($ipd_bill_payment['pathology']['bill']['total_bill'],$ipd_bill_payment['pathology']['payment']['total_payment']);
        $ipd_bill_payment['radiology']['radiology_bill_balance']=$this->calculate_balance($ipd_bill_payment['radiology']['bill']['total_bill'],$ipd_bill_payment['radiology']['payment']['total_payment']);
        $ipd_bill_payment['blood_bank']['blood_bank_bill_balance']=$this->calculate_balance($ipd_bill_payment['blood_bank']['bill']['total_bill'],$ipd_bill_payment['blood_bank']['payment']['total_payment']);
        $ipd_bill_payment['ambulance']['ambulance_bill_balance']=$this->calculate_balance($ipd_bill_payment['ambulance']['bill']['total_bill'],$ipd_bill_payment['ambulance']['payment']['total_payment']);
        $ipd_bill_payment['my_balance']=$ipd_bill_payment['ipd']['ipd_bill_balance']+$ipd_bill_payment['pharmacy']['pharmacy_bill_balance']+$ipd_bill_payment['pathology']['pathology_bill_balance']+$ipd_bill_payment['radiology']['radiology_bill_balance']+$ipd_bill_payment['blood_bank']['blood_bank_bill_balance']+$ipd_bill_payment['ambulance']['ambulance_bill_balance'];
        return $ipd_bill_payment;
    } 
 
    public function opd_bill_paymentbycase_id($case_id){
        $opd_bill_payment['opd']['bill']=$this->db->select('sum(amount) as total_bill')->from('opd_details')->join('patient_charges','patient_charges.opd_id=opd_details.id')->where('opd_details.case_reference_id',$case_id)->get()->row_array();        
        $opd_bill_payment['opd']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'opd_id !='=>'NULL'))->get()->row_array();
        $opd_bill_payment['pharmacy']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('pharmacy_bill_basic')->where('pharmacy_bill_basic.case_reference_id',$case_id)->get()->row_array();
        $opd_bill_payment['pharmacy']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pharmacy_bill_basic_id !='=>'NULL'))->get()->row_array();
        $opd_bill_payment['pharmacy']['payment_refund']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pharmacy_bill_basic_id !='=>'NULL','type'=>'refund'))->get()->row_array();
        $opd_bill_payment['pathology']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('pathology_billing')->where('pathology_billing.case_reference_id',$case_id)->get()->row_array();
        $opd_bill_payment['pathology']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pathology_billing_id !='=>'NULL'))->get()->row_array();
        $opd_bill_payment['radiology']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('radiology_billing')->where('radiology_billing.case_reference_id',$case_id)->get()->row_array();
        $opd_bill_payment['radiology']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'radiology_billing_id !='=>'NULL'))->get()->row_array();
        $opd_bill_payment['blood_bank']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('blood_issue')->where('blood_issue.case_reference_id',$case_id)->get()->row_array();
        $opd_bill_payment['blood_bank']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'blood_issue_id !='=>'NULL'))->get()->row_array();
        $opd_bill_payment['ambulance']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('ambulance_call')->where('ambulance_call.case_reference_id',$case_id)->get()->row_array();
        $opd_bill_payment['ambulance']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'ambulance_call_id !='=>'NULL'))->get()->row_array();       
        $opd_bill_payment['opd']['opd_bill_payment_ratio']=cal_percentage($opd_bill_payment['opd']['payment']['total_payment'],$opd_bill_payment['opd']['bill']['total_bill']);
        $pharmacy_payment=$opd_bill_payment['pharmacy']['payment']['total_payment']-$opd_bill_payment['pharmacy']['payment_refund']['total_payment'];
        $opd_bill_payment['pharmacy']['pharmacy_bill_payment_ratio']=cal_percentage($pharmacy_payment,$opd_bill_payment['pharmacy']['bill']['total_bill']);
        $opd_bill_payment['pathology']['pathology_bill_payment_ratio']=cal_percentage($opd_bill_payment['pathology']['payment']['total_payment'],$opd_bill_payment['pathology']['bill']['total_bill']);
        $opd_bill_payment['radiology']['radiology_bill_payment_ratio']=cal_percentage($opd_bill_payment['radiology']['payment']['total_payment'],$opd_bill_payment['radiology']['bill']['total_bill']);
        $opd_bill_payment['blood_bank']['blood_bank_bill_payment_ratio']=cal_percentage($opd_bill_payment['blood_bank']['payment']['total_payment'],$opd_bill_payment['blood_bank']['bill']['total_bill']);
        $opd_bill_payment['ambulance']['ambulance_bill_payment_ratio']=cal_percentage($opd_bill_payment['ambulance']['payment']['total_payment'],$opd_bill_payment['ambulance']['bill']['total_bill']);
        return $opd_bill_payment;
    } 

    public function calculate_balance($bill_amount,$payment_amount)
    {
        return ($bill_amount-$payment_amount);
    }     

    public function getopdpaymentdetails()
    {
        $payment_id = $this->input->post('payment_id');
        $this->db->select('payment_mode,amount,cheque_no,cheque_date,note,payment_date')->from('transactions')->where('id',$payment_id);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;

    }        
    
}