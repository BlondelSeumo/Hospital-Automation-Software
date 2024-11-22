<?php

class Bloodbank_model extends CI_Model {

    public function getbloodissue($patient_id)
    {          
        $this->db->select('blood_issue.*,sum(transactions.amount) as paid_amount,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit,organisation.organisation_name,organisation.id as orgid')
            ->join('patients', 'patients.id = blood_issue.patient_id')
            ->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id')
            ->join('transactions', 'transactions.blood_issue_id = blood_issue.id', 'left')
            ->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id')
            ->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id')   
			->join('organisation', 'organisation.id = patients.organisation_id ', "left")
            ->group_by('transactions.blood_issue_id')
            ->order_by('blood_issue.id', 'desc')
            ->from('blood_issue')->where('patients.id', $patient_id);
            $query = $this->db->get();
            $result = $query->result_array();         
       
            foreach($result as  $key => $valuee){   
                if(empty($valuee['case_reference_id'])){
                    $result[$key]['case_reference_id'] = '';
                }
				if(empty($valuee['paid_amount'])){
                    $result[$key]['paid_amount'] = '0.00';
                }
				if(empty($valuee['organisation_id'])){
                    $result[$key]['organisation_name'] = '';
				}
				if(empty($valuee['organisation_id'])){
                    $result[$key]['organisation_id'] = '';
				}
				if(empty($valuee['insurance_validity'])){
                    $result[$key]['insurance_validity'] = '';
				}
				if(empty($valuee['insurance_id'])){
                    $result[$key]['insurance_id'] = '';
				}
                $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'blood_issue');            
                $result[$key]['customfield'] = $customfield;              
            }           
            
        return $result;        
    }  

    public function getcomponent($patient_id)
    {      
        $sql = "select blood_issue.*,staff.name, staff.surname,staff.employee_id,IFNULL( (SELECT sum(transactions.amount) from transactions WHERE transactions.blood_issue_id= blood_issue.id and 1=1 and blood_issue.patient_id = $patient_id ),0) as `paid_amount`,  blood_group.name as blood_group_name,component.name as component_name,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit,organisation.organisation_name,organisation.id as orgid		
		from blood_issue inner join blood_donor_cycle on blood_donor_cycle.id=blood_issue.blood_donor_cycle_id             
            join blood_donor_cycle as bcd on blood_donor_cycle.blood_donor_cycle_id=bcd.id 
            join blood_donor on blood_donor.id=bcd.blood_donor_id 
            join blood_bank_products as component on component.id=blood_donor_cycle.blood_bank_product_id 
            join blood_bank_products as blood_group on blood_group.id=blood_donor.blood_bank_product_id  
            join patients on patients.id = blood_issue.patient_id left 
            join organisation on organisation.id = patients.organisation_id  left 
            join staff on staff.id = blood_issue.generated_by where blood_issue.patient_id =$patient_id";
         
        $query = $this->db->query($sql);       
        $result = $query->result_array();         
       
        foreach($result as  $key => $valuee){ 
            if(empty($valuee['case_reference_id'])){
                    $result[$key]['case_reference_id'] = '';
            }
			if(empty($valuee['organisation_id'])){
                    $result[$key]['organisation_name'] = '';
            }			
			if(empty($valuee['organisation_id'])){
                   $result[$key]['organisation_id'] = '';
			}
			if(empty($valuee['insurance_validity'])){
                   $result[$key]['insurance_validity'] = '';
			}
			if(empty($valuee['insurance_id'])){
                   $result[$key]['insurance_id'] = '';
			}
            $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'component_issue');            
            $result[$key]['customfield'] = $customfield;              
        }           
            
        return $result;        
    }

    public function getTotalBloodBankPayment($blood_issue_id){
        $query = $this->db->select("sum(transactions.amount) as paid_amount")
            ->join("blood_issue", "blood_issue.id = transactions.blood_issue_id")
            ->where("transactions.blood_issue_id", $blood_issue_id)
            ->get("transactions");
        return $query->row_array();
    }

    public function getTotalBloodBankCharge($blood_issue_id){
        $query = $this->db->select("net_amount")
            ->where('blood_issue.id', $blood_issue_id)
            ->get('blood_issue');
        return $query->row_array();
    }

}

?>