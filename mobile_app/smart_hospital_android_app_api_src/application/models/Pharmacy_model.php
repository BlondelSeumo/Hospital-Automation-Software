<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pharmacy_model extends CI_Model {
 
    public function getBillDetailsPharma($id)
    {        
        $this->db->select('pharmacy_bill_basic.*,IFNULL((select sum(amount) as amount_paid from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="payment" ),0) as paid_amount, IFNULL((select sum(amount) as refund from transactions WHERE transactions.pharmacy_bill_basic_id=pharmacy_bill_basic.id and transactions.type="refund" ),0) as refund_amount,patients.patient_name,patients.id as pid,staff.name,staff.surname,staff.id as staff_id,staff.employee_id' );
        $this->db->join('patients', 'patients.id = pharmacy_bill_basic.patient_id');
        $this->db->where('pharmacy_bill_basic.patient_id', $id);
        $this->db->join('staff', 'pharmacy_bill_basic.generated_by = staff.id');
        $query = $this->db->get('pharmacy_bill_basic');
        $result = $query->result_array();         
       
            foreach($result as  $key => $valuee){ 

                    if(empty($valuee['case_reference_id'])){
                       $result[$key]['case_reference_id'] = '';
                    }
                    $result[$key]['paid_amount'] = $valuee['paid_amount'] - $valuee['refund_amount'];
                    
                $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'pharmacy');     
                $result[$key]['customfield'] = $customfield;              
            }           
            
        return $result;  
    }
    
}   