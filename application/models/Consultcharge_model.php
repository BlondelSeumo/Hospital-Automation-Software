<?php

class Consultcharge_model extends MY_Model
{

    public function add_charges($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("charges", $data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Charges id " . $insert_id;
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

    public function addconsultcharges($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("consult_charges", $data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Consult Charges id " . $insert_id;
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
            //return $return_value;
        }
        return $insert_id;
    }

    public function add_schedule_charge($data)
    {
        $this->db->insert_batch("schedule_charges", $data);
    }

    public function getChargeCategory($charge_type)
    {
        $query = $this->db->where("charge_type", $charge_type)->get("charge_categories");
        return $query->result_array();
    }

    public function searchFullText()
    {
        $query = $this->db->order_by('id', 'desc')->get('charges');
        return $query->result_array();
    }

    public function getDetails($id, $organisation = "")
    {
        $this->db->select('charges.*,organisations_charges.org_charge');
        $this->db->join('organisations_charges', 'charges.id = organisations_charges.charge_id ', 'left');
        $this->db->where('charges.id', $id);
        if (!empty($organisation)) {
            $this->db->where('organisations_charges.org_id', $organisation);
        }
        $query = $this->db->get('charges');
        return $query->row_array();
    }

    public function getScheduleChargeBatch($charges_id)
    {
        $query = $this->db->where('id', $charges_id)->get('charges');
        return $query->row_array();
    }

    public function getAllScheduleCharges($charges_id)
    {
        $query = $this->db->select('schedule_charges.* , schedule_charge_category.id as schid, schedule_charge_category.schedule')
            ->join('schedule_charges', 'schedule_charge_category.id = schedule_charges.schedule_charge_id', 'left')
            ->get('schedule_charge_category');
        return $query->result_array();
    }

    public function getOrganisationCharges($charge_id)
    {
        $query = $this->db->select('organisations_charges.*,organisation.organisation_name,organisation.id as org_id')
            ->join('organisation', 'organisation.id = organisations_charges.org_id', 'left')
            ->where('organisations_charges.charge_id', $charge_id)
            ->get('organisations_charges');
        return $query->result_array();
    }

    public function get_chargedoctorfee()
    {
        $this->db->order_by('id', 'desc');
        $this->db->select('consult_charges.*,staff.name,staff.surname,staff.department,department.department_name');
        $this->db->join('staff', 'consult_charges.doctor = staff.id ', 'INNER');
        $this->db->join('department', 'staff.department=department.id', 'INNER');
        $query = $this->db->get("consult_charges");
        return $query->result_array();
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db->where('id', $id)
            ->delete('consult_charges');
        
        $message = DELETE_RECORD_CONSTANT . " On Consult Charges id " . $id;
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

    public function update_charges($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db->where('id', $data['id'])
            ->update('charges', $data);
            
        $message = UPDATE_RECORD_CONSTANT . " On Charges id " . $data['id'];
        $action = "Update";
        $record_id = $data['id'];
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

    public function update_schedule_charge($schedule_data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db->where('id', $schedule_data['id'])
            ->update('schedule_charges', $schedule_data);
            
        $message = UPDATE_RECORD_CONSTANT . " On Schedule Charges id " . $schedule_data['id'];
        $action = "Update";
        $record_id = $schedule_data['id'];
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

    public function add_ipdcharges($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("patient_charges", $data);
        
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Patient Charges id " . $insert_id;
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
            //return $return_value;
        }
        return $insert_id;
    }

    public function getCharges($id)
    {
        $query = $this->db->select('patient_charges.*,patients.id as pid,charges.charge_type,charges.charge_category,charges.standard_charge,organisations_charges.id as oid,organisations_charges.org_charge')
            ->join('patients', 'patient_charges.patient_id = patients.id', 'inner')
            ->join('charges', 'patient_charges.charge_id = charges.id', 'inner')
            ->join('organisations_charges', 'patient_charges.org_charge_id = organisations_charges.id', 'left')
            ->where('patient_charges.patient_id', $id)
            ->get('patient_charges');
        return $query->result_array();
    }

    public function deleteIpdPatientCharge($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db->where('id', $id)
            ->delete('patient_charges');
        
        $message = DELETE_RECORD_CONSTANT . " On Patient Charges id " . $id;
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

    public function getchargeDetails($charge_category)
    {
        $query = $this->db->where("charge_category", $charge_category)->get("charges");
        return $query->result_array();
    }

}
