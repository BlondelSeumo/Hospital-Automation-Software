<?php

class Antenatal_model extends CI_Model {
	
	public function getobstetrichistory($patient_id){

        $this->db->select("*")->from("obstetric_history")->where("obstetric_history.patient_id",$patient_id);
        $query = $this->db->get();
        return $query->result_array();
    }
	
	public function getpostnatal($patient_id){

	   $this->db->select("postnatal_examine.*")->from("postnatal_examine")->where("postnatal_examine.patient_id",$patient_id);
       $query = $this->db->get();
       $result = $query->result_array();      
       return $result;
    }
	
	public function getantenatallist($patient_id){

       $query =$this->db->query("select primary_examine.id as primary_id,primary_examine.visit_details_id,primary_examine.ipdid,primary_examine.bleeding,primary_examine.headache,primary_examine.pain,primary_examine.constipation,primary_examine.urinary_symptoms,primary_examine.vomiting,primary_examine.cough,primary_examine.vaginal,primary_examine.oedema,primary_examine.discharge,primary_examine.haemoroids,primary_examine.weight,primary_examine.height,primary_examine.general_condition,primary_examine.finding_remark,primary_examine.pelvic_examination,primary_examine.sp,antenatal_examine.uter_size,antenatal_examine.uterus_size,antenatal_examine.presentation_position,antenatal_examine.brim_presentation,antenatal_examine.foeta_heart,antenatal_examine.blood_pressure,antenatal_examine.antenatal_Oedema,antenatal_examine.antenatal_weight,antenatal_examine.urine_sugar,antenatal_examine.urine,antenatal_examine.remark,opd_details.id as opd_detail_id,primary_examine.date, 'opd' as status,antenatal_examine.id as antenatal_examine_id,antenatal_examine.next_visit as next_visit from primary_examine left join visit_details on visit_details.id = primary_examine.visit_details_id left join antenatal_examine on visit_details.id = antenatal_examine.visit_details_id left join opd_details on opd_details.id = visit_details.opd_details_id where opd_details.patient_id = '".$patient_id."'
       union all 
       select primary_examine.id as primary_id,primary_examine.visit_details_id,primary_examine.ipdid,primary_examine.bleeding,primary_examine.headache,primary_examine.pain,primary_examine.constipation,primary_examine.urinary_symptoms,primary_examine.vomiting,primary_examine.cough,primary_examine.vaginal,primary_examine.oedema,primary_examine.discharge,primary_examine.haemoroids,primary_examine.weight,primary_examine.height,primary_examine.general_condition,primary_examine.finding_remark,primary_examine.pelvic_examination,primary_examine.sp,antenatal_examine.uter_size,antenatal_examine.uterus_size,antenatal_examine.presentation_position,antenatal_examine.brim_presentation,antenatal_examine.foeta_heart,antenatal_examine.blood_pressure,antenatal_examine.antenatal_Oedema,antenatal_examine.antenatal_weight,antenatal_examine.urine_sugar,antenatal_examine.urine,antenatal_examine.remark, null as opd_detail_id,primary_examine.date,'ipd' as status,antenatal_examine.id as antenatal_examine_id,antenatal_examine.next_visit as next_visit from primary_examine   inner join antenatal_examine on  primary_examine.id = antenatal_examine.primary_examine_id inner join ipd_details on  ipd_details.id = primary_examine.ipdid where ipd_details.patient_id = '".$patient_id."' order by date desc");
	  
            $result = $query->result_array();         
       
            foreach($result as  $key => $valuee){   
                if(empty($valuee['ipdid'])){
                    $result[$key]['ipdid'] = '';
                }if(empty($valuee['opd_detail_id'])){
                    $result[$key]['opd_detail_id'] = '';
                }if(empty($valuee['visit_details_id'])){
                    $result[$key]['visit_details_id'] = '';
                }	
				$customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['primary_id'],'antenatal');            
                $result[$key]['customfield'] = $customfield;  
            }           
            
        return $result;       
        
    }
	
	public function getopdantenatal($visit_detail_id)
    {
        $this->db->select("patients.*,primary_examine.*,antenatal_examine.*,blood_bank_products.name as blood_group ,primary_examine.date as antenatal_date,primary_examine.weight as antenatal_weight,primary_examine.height as antenatal_height, visit_details.*,primary_examine.id as antenatal_id,antenatal_examine.id as anteexam_id ");
        $this->db->from("antenatal_examine");
        $this->db->join("primary_examine", "primary_examine.id = antenatal_examine.primary_examine_id","inner");
        $this->db->join("visit_details", "visit_details.id = primary_examine.visit_details_id","left");
        $this->db->join("opd_details", "opd_details.id = visit_details.opd_details_id");
        $this->db->join("patients", "patients.id = opd_details.patient_id");
        $this->db->join("blood_bank_products", "blood_bank_products.id = patients.blood_bank_product_id","left");
        $this->db->where('antenatal_examine.visit_details_id',$visit_detail_id);
        $query = $this->db->get();
        $result = $query->row();
		
			if(empty($result->weight)){
                 $result->weight = '';
			}
			if(empty($result->height)){
                 $result->height = '';
			}
			if(empty($result->date)){
                 $result->date = '';
			}

			$customfield =  $this->customfield_model->getcustomfieldswithvalue($result->antenatal_id,'antenatal');            
            $result->customfield = $customfield;
				
        return $result ;
    }
	
}