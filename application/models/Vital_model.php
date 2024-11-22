<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vital_model extends MY_Model
{

    public function add($data)
    {
        $id = null;
        if (isset($data['id']) && $data['id'] != null) {
            $this->db->where('id', $data['id']);
            $this->db->update('vitals', $data);
            $id = $data['id'];
        } else {
            unset($data["id"]);
            $this->db->insert('vitals', $data);
            $insert_id = $this->db->insert_id();
            $id = $insert_id;
        }
        return $id ;
    }
    
    public function getDatatableAllRecord()
    {        
        $this->datatables
            ->select('vitals.*,')
            ->searchable('vitals.id,vitals.name,vitals.reference_range,vitals.unit')
            ->orderable('vitals.id,vitals.name,vitals.reference_range,vitals.unit')
            ->sort('vitals.id', 'asc')       
            ->from('vitals');
        return $this->datatables->generate('json');
    }

    public function getDetails($id)
    {
        $this->db->select('vitals.*');
        $this->db->where('vitals.id', $id);
        $query = $this->db->get('vitals');       
        return $query->row_array();
    }
    
    public function getvitallist()
    {
        $this->db->select('vitals.*');
        $query = $this->db->get('vitals');
        return $query->result_array();
    }
    
    public function getpatientsvital($patient_id,$vital_id,$messure_date)
    {
        $this->db->select('patients_vitals.reference_range as patient_range,patients_vitals.id as id,patients_vitals.messure_date');
        $this->db->from("patients_vitals");
        $this->db->join("vitals","vitals.id=patients_vitals.vital_id");
        $this->db->where('patients_vitals.patient_id', $patient_id);
        $this->db->where('patients_vitals.vital_id', $vital_id);
        $this->db->like('patients_vitals.messure_date', $messure_date);		
        $this->db->order_by("vital_id","desc");
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getpatientvitaldate($patient_id)
    {
        $this->db->select('patients_vitals.messure_date');
        $this->db->from("patients_vitals");         
        $this->db->where('patients_vitals.patient_id', $patient_id);
        $this->db->group_by("date_format(patients_vitals.messure_date,'%Y-%m-%d')");
		$this->db->order_by("patients_vitals.messure_date","desc");
        $query = $this->db->get();
        return $query->result_array();
    }	
    
    public function delete($id)
    {
        $query = $this->db->where('id', $id)
            ->delete('vitals');
        $message   = DELETE_RECORD_CONSTANT . " On Vital id " . $id;
    }

    public function addpatientvital($data)
    {
        $id = null;
        if (isset($data['id']) && $data['id'] != null) {
            $this->db->where('id', $data['id']);
            $this->db->update('patients_vitals', $data);
            $id = $data['id'];
        } else {
            unset($data["id"]);
            $this->db->insert('patients_vitals', $data);            
            $insert_id = $this->db->insert_id();
            $id = $insert_id;           
        }
      
        return $id ;
    }
    
    public function getpatientvitalbyvitalid($id)
    {
        $this->db->select('patients_vitals.*');
        $this->db->from("patients_vitals");
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function getcurrentvitals($id)
    {        		
		$query = $this->db->query("select vitals.*,patients_vitals.reference_range as patient_range,patients_vitals.id as idd,patients_vitals.messure_date,patients_vitals.vital_id as patient_vital_id from  patients_vitals join  vitals on vitals.id = (SELECT MAX(patients_vitals.vital_id) FROM patients_vitals p2 WHERE p2.vital_id = vitals.id ) where patients_vitals.patient_id = '".$id."' group by patient_vital_id");   
       
        return $query->result_array();
    }
    
    public function delete_patient_vital($id)
    {
        $this->db->where('id',$id);
        $this->db->delete("patients_vitals");
    }    
   
    
}
