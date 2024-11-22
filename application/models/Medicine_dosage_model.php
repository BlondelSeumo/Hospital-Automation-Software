<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Medicine_dosage_model extends MY_model
{

    public function addMedicineDosage($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('medicine_dosage', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Medicine Dosage id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('medicine_dosage', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Medicine Dosage id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
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

    public function add_interval($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id']!=='') {
            $this->db->where('id', $data['id']);
            $this->db->update('dose_interval', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Dose Interval id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('dose_interval', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Dose Interval id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
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

    public function add_duration($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id']!=='') {
            $this->db->where('id', $data['id']);
            $this->db->update('dose_duration', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Dose Duration id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('dose_duration', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Dose Duration id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
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
     public function getIntervalDosage($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('dose_interval');
            return $query->row_array();
        } else {
            $query = $this->db->get("dose_interval");
            return $query->result_array();
        }
    }

    public function getDurationDosage($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('dose_duration');
            return $query->row_array();
        } else {
            $query = $this->db->get("dose_duration");
            return $query->result_array();
        }
    }
    public function getMedicineDosage($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->select('medicine_dosage.*,medicine_category.medicine_category,unit.unit_name as unit')
                ->join('medicine_category', 'medicine_dosage.medicine_category_id = medicine_category.id','left')
                ->join('unit', 'unit.id = medicine_dosage.units_id')
                ->where('medicine_dosage.id', $id)
                ->get('medicine_dosage');
            return $query->row_array();
        } else {
            $query = $this->db->select('medicine_dosage.*,medicine_category.medicine_category,unit.unit_name as unit','left')
                ->join('medicine_category', 'medicine_dosage.medicine_category_id = medicine_category.id','left')
                 ->join('unit', 'unit.id = medicine_dosage.units_id')
                ->get('medicine_dosage');
            return $query->result_array();
        }
    }

    public function getCategoryDosages()
    {
		$query = $this->db->select('medicine_dosage.*,medicine_category.medicine_category,unit.unit_name as unit')
                ->join('medicine_category', 'medicine_dosage.medicine_category_id = medicine_category.id')
                ->join('unit', 'unit.id = medicine_dosage.units_id','left')
                ->get('medicine_dosage');
            $result=$query->result();
            $medicine_array=array();
            if(!empty($result)){
				foreach ($result as $result_key => $result_value) {
					$medicine_array[$result_value->medicine_category_id][]=$result_value;
				}
            }
        return $medicine_array;        
    }

    public function getDosageByMedicine($medicine)
    {

    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("medicine_dosage");
        $message = DELETE_RECORD_CONSTANT . " On Medicine Dosage id " . $id;
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

    public function deletemedicationdosage($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("medication_report");
        $message = DELETE_RECORD_CONSTANT . " On Medicine Report id " . $id;
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

    public function get_doseIntervallist()
    {
        $this->datatables
            ->select('*')
            ->searchable('name')
            ->orderable('name')
            ->sort('id', 'desc')
            ->from('dose_interval');
        return $this->datatables->generate('json');
    }
    
    public function get_intervalbyid($id){
        return $this->db->select('*')->from('dose_interval')->where('id',$id)->get()->row_array();
    }

    public function delete_doseInterval($id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("dose_interval");
        $message = DELETE_RECORD_CONSTANT . " On Dose Interval id " . $id;
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

    public function get_dosedurationlist()
    {
        $this->datatables
            ->select('*')
            ->searchable('name')
            ->orderable('name')
            ->sort('id', 'desc')
            ->from('dose_duration');
        return $this->datatables->generate('json');
    }
    
    public function get_durationbyid($id){
        return $this->db->select('*')->from('dose_duration')->where('id',$id)->get()->row_array();
    }

    public function delete_doseduration($id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("dose_duration");
        $message = DELETE_RECORD_CONSTANT . " On Dose Duration id " . $id;
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
}
