<?php

class Chargetype_model extends MY_Model
{
    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $this->db->insert('charge_type_master', $data);
        
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Charge Type Master id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
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

    public function get($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('charge_type_master');
            return $query->row();
        } else {
            $query = $this->db->get("charge_type_master");
            return $query->result();
        }
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->where("is_default", 'no')->delete('charge_type_master');
        
        $message = DELETE_RECORD_CONSTANT . " On Charge Type Master id " . $id;
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

    public function updateChargeTypeModule($charge_data){
        $result = $this->db->get_where("charge_type_module",array("charge_type_master_id" => $charge_data["charge_type_master_id"] , "module_shortcode" => $charge_data["module_shortcode"]))->row();
        if(!empty($result)){
            $this->db->delete("charge_type_module",array("charge_type_master_id" => $charge_data["charge_type_master_id"] , "module_shortcode" => $charge_data["module_shortcode"]));
        }
        else{
            $this->db->insert("charge_type_module",$charge_data);
        }
    }

    public function getChargeModuleData($module){
        foreach($module as $module_shortcode => $module_name){
            $result_array = $this->db->select("charge_type_master_id")->where("module_shortcode",$module_shortcode)->get("charge_type_module")->result_array();
            $result[$module_shortcode] = array_column($result_array,"charge_type_master_id");
        }        
        return $result;  
    }

    public function getChargeTypeByModule($module_shortcode,$appointment=null){

        $this->db->where("module_shortcode",$module_shortcode);
        if($appointment!=""){
            $this->db->or_where("module_shortcode",$appointment);
        }

        $result = $this->db->select("charge_type_master.*")
            ->join("charge_type_master","charge_type_master.id = charge_type_module.charge_type_master_id")
            
            ->get("charge_type_module")
            ->result();
        return $result;
    }

    public function addChargeModuleData($module_data){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $this->db->insert("charge_type_module",$module_data);        
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Charge Type Module id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
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
	
    public function updatechargetype($data,$id){
        $this->db->where('charge_type_master.id',$id);
        $this->db->update('charge_type_master',$data);
      
    }
}
