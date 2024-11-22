<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends CI_Model {

        public function __construct() {
                parent::__construct();
                $CI =& get_instance();
       
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
        
        public function ipdpatientpayments($ipd_id)
        {
            $query = $this->db->select('transactions.*,patients.id as pid,patients.note as pnote')
                ->join("ipd_details", "ipd_details.id = transactions.ipd_id")
                ->join("patients", "patients.id = ipd_details.patient_id")
                ->where("transactions.ipd_id", $ipd_id)
                ->order_by("transactions.id", "desc")
                ->get("transactions");
            $result = $query->result_array();
			foreach ($result as $key => $value) {	
				if ($value["amount"] == '') {	
					$result[$key]['amount'] = '0.0';					 
				}
			}
	
			return $result;
        }
        
        public function ipd_bill_paymentbycase_id($case_id){
            
                $ipd_bill_payment['ipd']['bill']=$this->db->select('sum(amount) as total_bill')->from('ipd_details')->join('patient_charges','patient_charges.ipd_id=ipd_details.id')->where('ipd_details.case_reference_id',$case_id)->get()->row_array();                
                if($ipd_bill_payment['ipd']['bill']['total_bill'] == ''){
                        $ipd_bill_payment['ipd']['bill']['total_bill'] =0;
                }
                
                $ipd_bill_payment['ipd']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'ipd_id !='=>'NULL'))->get()->row_array();
                if($ipd_bill_payment['ipd']['payment']['total_payment'] == ''){
                        $ipd_bill_payment['ipd']['payment']['total_payment'] =0;
                }
                
                $ipd_bill_payment['pharmacy']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('pharmacy_bill_basic')->where('pharmacy_bill_basic.case_reference_id',$case_id)->get()->row_array();
                if($ipd_bill_payment['pharmacy']['bill']['total_bill'] == ''){
                        $ipd_bill_payment['pharmacy']['bill']['total_bill'] =0;
                }
                
                $ipd_bill_payment['pharmacy']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pharmacy_bill_basic_id !='=>'NULL'))->get()->row_array();
                if($ipd_bill_payment['pharmacy']['payment']['total_payment'] == ''){
                        $ipd_bill_payment['pharmacy']['payment']['total_payment'] =0;
                }
                
                $ipd_bill_payment['pharmacy']['payment_refund']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pharmacy_bill_basic_id !='=>'NULL','type'=>'refund'))->get()->row_array();
                if($ipd_bill_payment['pharmacy']['payment_refund']['total_payment'] == ''){
                        $ipd_bill_payment['pharmacy']['payment_refund']['total_payment'] =0;
                }
                
                $ipd_bill_payment['pathology']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('pathology_billing')->where('pathology_billing.case_reference_id',$case_id)->get()->row_array();
                if($ipd_bill_payment['pathology']['bill']['total_bill'] == ''){
                        $ipd_bill_payment['pathology']['bill']['total_bill'] =0;
                }
                
                $ipd_bill_payment['pathology']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pathology_billing_id !='=>'NULL'))->get()->row_array();
                if($ipd_bill_payment['pathology']['payment']['total_payment'] == ''){
                        $ipd_bill_payment['pathology']['payment']['total_payment'] =0;
                }
                
                $ipd_bill_payment['radiology']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('radiology_billing')->where('radiology_billing.case_reference_id',$case_id)->get()->row_array();
                if($ipd_bill_payment['radiology']['bill']['total_bill'] == ''){
                        $ipd_bill_payment['radiology']['bill']['total_bill'] =0;
                }
                
                $ipd_bill_payment['radiology']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'radiology_billing_id !='=>'NULL'))->get()->row_array();
                if($ipd_bill_payment['radiology']['payment']['total_payment'] == ''){
                        $ipd_bill_payment['radiology']['payment']['total_payment'] =0;
                }
                
                $ipd_bill_payment['blood_bank']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('blood_issue')->where('blood_issue.case_reference_id',$case_id)->get()->row_array();
                if($ipd_bill_payment['blood_bank']['bill']['total_bill'] == ''){
                        $ipd_bill_payment['blood_bank']['bill']['total_bill'] =0;
                }
                
                $ipd_bill_payment['blood_bank']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'blood_issue_id !='=>'NULL'))->get()->row_array();
                if($ipd_bill_payment['blood_bank']['payment']['total_payment'] == ''){
                        $ipd_bill_payment['blood_bank']['payment']['total_payment'] =0;
                }
                
                $ipd_bill_payment['ambulance']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('ambulance_call')->where('ambulance_call.case_reference_id',$case_id)->get()->row_array();
                if($ipd_bill_payment['ambulance']['bill']['total_bill'] == ''){
                        $ipd_bill_payment['ambulance']['bill']['total_bill'] =0;
                }
                
                $ipd_bill_payment['ambulance']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'ambulance_call_id !='=>'NULL'))->get()->row_array();
                if($ipd_bill_payment['ambulance']['payment']['total_payment'] == ''){
                        $ipd_bill_payment['ambulance']['payment']['total_payment'] =0;
                }
                
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
    
    public function calculate_balance($bill_amount,$payment_amount)
    {
        return ($bill_amount-$payment_amount);
    } 
    
    public function opd_bill_paymentbycase_id($case_id)
    {
        $opd_bill_payment['opd']['bill']=$this->db->select('sum(amount) as total_bill')->from('opd_details')->join('patient_charges','patient_charges.opd_id=opd_details.id')->where('opd_details.case_reference_id',$case_id)->get()->row_array();
        if($opd_bill_payment['opd']['bill']['total_bill'] == ''){
                $opd_bill_payment['opd']['bill']['total_bill'] =0; 
        }                
        
        $opd_bill_payment['opd']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'opd_id !='=>'NULL'))->get()->row_array();
        if($opd_bill_payment['opd']['payment']['total_payment'] == ''){
                $opd_bill_payment['opd']['payment']['total_payment'] =0; 
        }
        
        $opd_bill_payment['pharmacy']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('pharmacy_bill_basic')->where('pharmacy_bill_basic.case_reference_id',$case_id)->get()->row_array();
        if($opd_bill_payment['pharmacy']['bill']['total_bill'] == ''){
                $opd_bill_payment['pharmacy']['bill']['total_bill'] =0; 
        }
        
        $opd_bill_payment['pharmacy']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pharmacy_bill_basic_id !='=>'NULL'))->get()->row_array();
        if($opd_bill_payment['pharmacy']['payment']['total_payment'] == ''){
                $opd_bill_payment['pharmacy']['payment']['total_payment'] =0; 
        }
        
        $opd_bill_payment['pharmacy']['payment_refund']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pharmacy_bill_basic_id !='=>'NULL','type'=>'refund'))->get()->row_array();
        if($opd_bill_payment['pharmacy']['payment_refund']['total_payment'] == ''){
                $opd_bill_payment['pharmacy']['payment_refund']['total_payment'] =0; 
        }
        
        $opd_bill_payment['pathology']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('pathology_billing')->where('pathology_billing.case_reference_id',$case_id)->get()->row_array();
        if($opd_bill_payment['pathology']['bill']['total_bill'] == ''){
                $opd_bill_payment['pathology']['bill']['total_bill'] =0; 
        }

        $opd_bill_payment['pathology']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pathology_billing_id !='=>'NULL'))->get()->row_array();
        if($opd_bill_payment['pathology']['payment']['total_payment'] == ''){
                $opd_bill_payment['pathology']['payment']['total_payment'] =0; 
        }
        
        $opd_bill_payment['radiology']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('radiology_billing')->where('radiology_billing.case_reference_id',$case_id)->get()->row_array();
        if($opd_bill_payment['radiology']['bill']['total_bill'] == ''){
                $opd_bill_payment['radiology']['bill']['total_bill'] =0; 
        }
        
        $opd_bill_payment['radiology']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'radiology_billing_id !='=>'NULL'))->get()->row_array();
        if($opd_bill_payment['radiology']['payment']['total_payment'] == ''){
                $opd_bill_payment['radiology']['payment']['total_payment'] =0; 
        }
        
        $opd_bill_payment['blood_bank']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('blood_issue')->where('blood_issue.case_reference_id',$case_id)->get()->row_array();
        if($opd_bill_payment['blood_bank']['bill']['total_bill'] == ''){
                $opd_bill_payment['blood_bank']['bill']['total_bill'] =0; 
        }
        
        $opd_bill_payment['blood_bank']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'blood_issue_id !='=>'NULL'))->get()->row_array();
        if($opd_bill_payment['blood_bank']['payment']['total_payment'] == ''){
                $opd_bill_payment['blood_bank']['payment']['total_payment'] =0; 
        }
        
        $opd_bill_payment['ambulance']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('ambulance_call')->where('ambulance_call.case_reference_id',$case_id)->get()->row_array();
        if($opd_bill_payment['ambulance']['bill']['total_bill'] == ''){
                $opd_bill_payment['ambulance']['bill']['total_bill'] =0; 
        }
        
        $opd_bill_payment['ambulance']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'ambulance_call_id !='=>'NULL'))->get()->row_array(); 
        if($opd_bill_payment['ambulance']['payment']['total_payment'] == ''){
                $opd_bill_payment['ambulance']['payment']['total_payment'] =0; 
        }
        
        $opd_bill_payment['opd']['opd_bill_payment_ratio']=cal_percentage($opd_bill_payment['opd']['payment']['total_payment'],$opd_bill_payment['opd']['bill']['total_bill']);
        
        $pharmacy_payment=$opd_bill_payment['pharmacy']['payment']['total_payment']-$opd_bill_payment['pharmacy']['payment_refund']['total_payment'];
        
        $opd_bill_payment['pharmacy']['pharmacy_bill_payment_ratio']=cal_percentage($pharmacy_payment,$opd_bill_payment['pharmacy']['bill']['total_bill']);
        
        $opd_bill_payment['pathology']['pathology_bill_payment_ratio']=cal_percentage($opd_bill_payment['pathology']['payment']['total_payment'],$opd_bill_payment['pathology']['bill']['total_bill']);
        
        $opd_bill_payment['radiology']['radiology_bill_payment_ratio']=cal_percentage($opd_bill_payment['radiology']['payment']['total_payment'],$opd_bill_payment['radiology']['bill']['total_bill']);
        
        $opd_bill_payment['blood_bank']['blood_bank_bill_payment_ratio']=cal_percentage($opd_bill_payment['blood_bank']['payment']['total_payment'],$opd_bill_payment['blood_bank']['bill']['total_bill']);
        
        $opd_bill_payment['ambulance']['ambulance_bill_payment_ratio']=cal_percentage($opd_bill_payment['ambulance']['payment']['total_payment'],$opd_bill_payment['ambulance']['bill']['total_bill']);
        return $opd_bill_payment;
    }
}   