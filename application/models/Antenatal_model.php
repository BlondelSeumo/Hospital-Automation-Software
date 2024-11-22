<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Antenatal_model extends MY_Model
{
    public function add_obstetrichistory($insert_history, $update_history)
    {
      
        if (!empty($insert_history)) {
            $this->db->insert("obstetric_history", $insert_history);
        }
        
        if (!empty($update_history)) {

            $id = $update_history['id'];

            unset($update_history['id']);
            $this->db->where('id',$id);
            $this->db->update('obstetric_history', $update_history);
        }
      
    }

    public function getobstetrichistory($patient_id){

        $this->db->select("*")->from("obstetric_history")->where("obstetric_history.patient_id",$patient_id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getobstetrichistorybyid($id){

        $this->db->select("obstetric_history.*,obstetric_history.gender as obstetric_gender, patients.patient_name,patients.id as patient_unique_id,patients.age,patients.month,patients.day,patients.gender,patients.address,blood_bank_products.name as blood_group,patients.mobileno,patients.email")->from("obstetric_history")->join("patients","patients.id=obstetric_history.patient_id","inner")->join("blood_bank_products", "blood_bank_products.id = patients.blood_bank_product_id","left")->where("obstetric_history.id",$id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function addantenatalprescription($data){

        if($data['id']!=""){
            $id = $data['id'];
             unset($data['id']);
            $this->db->where('visit_details_id',$data['visit_details_id']);
            $this->db->update('primary_examine',$data);
             return $id ;
        }else{
            unset($data['id']);
            $this->db->insert('primary_examine',$data);
            return $this->db->insert_id();
        }
    }

    public function addantenatalexamine($data){

       if($data['id']!=""){
         $id = $data['id'];
         unset($data['id']);

           $this->db->where('visit_details_id',$data['visit_details_id']);
           $this->db->update('antenatal_examine',$data);
           return $id ;
       }else{
            unset($data['id']);
            $this->db->insert('antenatal_examine',$data);
            return $this->db->insert_id();
        }
    }

    public function getprescription($visit_detail_id)
    {
        $custom_fields             = $this->customfield_model->get_custom_fields('antenatal');
      
        $custom_field_column_array = array();
        $field_var_array = array();
        $i=1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'antenatal_examine.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);

        $this->db->select("patients.*,primary_examine.*,antenatal_examine.*,blood_bank_products.name as blood_group ,primary_examine.date as antenatal_date,primary_examine.weight as antenatal_weight,primary_examine.height as antenatal_height, visit_details.*,primary_examine.id as antenatal_id,antenatal_examine.id as anteexam_id ".$field_variable);
        $this->db->from("antenatal_examine");
          $this->db->join("primary_examine", "primary_examine.id = antenatal_examine.primary_examine_id","inner");
           $this->db->join("visit_details", "visit_details.id = primary_examine.visit_details_id","left");
        $this->db->join("opd_details", "opd_details.id = visit_details.opd_details_id");
        $this->db->join("patients", "patients.id = opd_details.patient_id");
        $this->db->join("blood_bank_products", "blood_bank_products.id = patients.blood_bank_product_id","left");
        $this->db->where('antenatal_examine.visit_details_id',$visit_detail_id);
        $query = $this->db->get();
        $result = $query->row();
        return $result ;
    }

    public function getipdprescription($primary_id)
    {
            $this->db->select("patients.*,primary_examine.*,antenatal_examine.*,ipd_details.*,blood_bank_products.name as blood_group ,primary_examine.date as antenatal_date,primary_examine.weight as antenatal_weight,primary_examine.height as antenatal_height,primary_examine.id as antenatal_id,antenatal_examine.id as anteexam_id,ipd_details.id as ipdid");
            $this->db->from("primary_examine");
            $this->db->join("antenatal_examine", "antenatal_examine.primary_examine_id = primary_examine.id","inner");
            $this->db->join("ipd_details", "ipd_details.id = antenatal_examine.ipdid"); 
            $this->db->join("patients", "patients.id = ipd_details.patient_id","inner");
            $this->db->join("blood_bank_products", "blood_bank_products.id = patients.blood_bank_product_id","left");
            $this->db->where('primary_examine.id',$primary_id);
            $query = $this->db->get();
            $result = $query->row();
            return $result ;
    }
        
    public function getprescriptionbyid($id)
    {
        $custom_fields             = $this->customfield_model->get_custom_fields('antenatal');
        $custom_field_column_array = array();
        $field_var_array = array();
        $i=1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'antenatal_examine.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);       
        $this->db->select("patients.*,primary_examine.*,antenatal_examine.*,blood_bank_products.name as blood_group ,primary_examine.date as antenatal_date,primary_examine.weight as antenatal_weight,primary_examine.height as antenatal_height, visit_details.*,primary_examine.id as antenatal_id,antenatal_examine.id as anteexam_id,".$field_variable);
        $this->db->from("antenatal_examine");
        $this->db->join("primary_examine", "primary_examine.id = antenatal_examine.primary_examine_id","inner");
        $this->db->join("visit_details", "visit_details.id = primary_examine.visit_details_id","left");
        $this->db->join("opd_details", "opd_details.id = visit_details.opd_details_id");
        $this->db->join("patients", "patients.id = opd_details.patient_id");
        $this->db->join("blood_bank_products", "blood_bank_products.id = patients.blood_bank_product_id","left");
        $this->db->where('primary_examine.visit_details_id',$id);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result ;
    }

    public function deleteobstetrichistory($id){
        $this->db->where('id',$id);
        $this->db->delete("obstetric_history");
    }

    public function addpostnatal($data){
       if(isset($data['id']) && $data['id']!=""){
          $this->db->where('id',$data['id']);
          $this->db->update('postnatal_examine',$data);
       }else{
         
            $this->db->insert('postnatal_examine',$data);
       }
    }

    public function getpostnatal($patient_id){

      $this->db->select("postnatal_examine.*")->from("postnatal_examine")->where("postnatal_examine.patient_id",$patient_id);
       $query = $this->db->get();
       $result = $query->result_array();      
       return $result;
    }

    public function getpostnatalbyid($id){

      $this->db->select("postnatal_examine.*")->from("postnatal_examine")->where("postnatal_examine.id",$id);
       $query = $this->db->get();
       $result = $query->row_array();
       return $result;
    }

     public function deletepostnatal($id){
        $this->db->where('id',$id);
        $this->db->delete("postnatal_examine");
    }

    public function deleteantenatal($id){

        $this->db->where('visit_details_id',$id);
        $this->db->delete("primary_examine");
        $this->db->where('visit_details_id',$id);
        $this->db->delete("antenatal_examine");
    }

    public function getantenatallist($patient_id){

       $query =$this->db->query("select primary_examine.id as primary_id,primary_examine.visit_details_id,primary_examine.ipdid,primary_examine.bleeding,primary_examine.headache,primary_examine.pain,primary_examine.constipation,primary_examine.urinary_symptoms,primary_examine.vomiting,primary_examine.cough,primary_examine.vaginal,primary_examine.oedema,primary_examine.discharge,primary_examine.haemoroids,primary_examine.weight,primary_examine.height,primary_examine.general_condition,primary_examine.finding_remark,primary_examine.pelvic_examination,primary_examine.sp,antenatal_examine.uter_size,antenatal_examine.uterus_size,antenatal_examine.presentation_position,antenatal_examine.brim_presentation,antenatal_examine.foeta_heart,antenatal_examine.blood_pressure,antenatal_examine.antenatal_Oedema,antenatal_examine.antenatal_weight,antenatal_examine.urine_sugar,antenatal_examine.urine,antenatal_examine.remark,opd_details.id as opd_detail_id,primary_examine.date, 'opd' as status from primary_examine left join visit_details on visit_details.id = primary_examine.visit_details_id left join antenatal_examine on visit_details.id = antenatal_examine.visit_details_id left join opd_details on opd_details.id = visit_details.opd_details_id where opd_details.patient_id = '".$patient_id."'
       union all 
       select primary_examine.id as primary_id,primary_examine.visit_details_id,primary_examine.ipdid,primary_examine.bleeding,primary_examine.headache,primary_examine.pain,primary_examine.constipation,primary_examine.urinary_symptoms,primary_examine.vomiting,primary_examine.cough,primary_examine.vaginal,primary_examine.oedema,primary_examine.discharge,primary_examine.haemoroids,primary_examine.weight,primary_examine.height,primary_examine.general_condition,primary_examine.finding_remark,primary_examine.pelvic_examination,primary_examine.sp,antenatal_examine.uter_size,antenatal_examine.uterus_size,antenatal_examine.presentation_position,antenatal_examine.brim_presentation,antenatal_examine.foeta_heart,antenatal_examine.blood_pressure,antenatal_examine.antenatal_Oedema,antenatal_examine.antenatal_weight,antenatal_examine.urine_sugar,antenatal_examine.urine,antenatal_examine.remark, null as opd_detail_id,primary_examine.date,'ipd' as status from primary_examine   inner join antenatal_examine on  primary_examine.id = antenatal_examine.primary_examine_id inner join ipd_details on  ipd_details.id = primary_examine.ipdid where ipd_details.patient_id = '".$patient_id."' order by date desc");
       return $query->result_array();
        
    }

     public function addipdantenatalprescription($data){

        if($data['id']!=""){
            
          $id= $data['id'];
          unset($data['id']);
            $this->db->where('id',$id);
            $this->db->update('primary_examine',$data);
            return $id  ;
        }else{
            unset($data['id']);
            $this->db->insert('primary_examine',$data);
             return $this->db->insert_id();
        }       
    }

    public function addipdantenatalexamine($data){

       if($data['id']!=""){
         $id= $data['id'];
        unset($data['id']);
           $this->db->where('id',$id);
           $this->db->update('antenatal_examine',$data);
       }else{
            unset($data['id']);
            $this->db->insert('antenatal_examine',$data);
        }
    }

    public function deleteipdantenatal($id){

        $this->db->where('id',$id);
        $this->db->delete("primary_examine");
        $this->db->where('primary_examine_id',$id);
        $this->db->delete("antenatal_examine");
    }


}