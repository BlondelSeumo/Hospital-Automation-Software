<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Charge_model extends MY_Model
{

    public function add($data, $organisation_charges)
    {
        $id = null;
        if (isset($data['id']) && $data['id'] != null) {
            $this->db->where('id', $data['id']);
            $this->db->update('charges', $data);
            $id = $data['id']; 
        } else {
            unset($data["id"]);
            $this->db->insert('charges', $data);
            $insert_id = $this->db->insert_id();
            $id = $insert_id;           
        }

        if (!empty($organisation_charges)) {
            foreach ($organisation_charges as $org_chg_key => $org_chg_value) {
                $org_chg_value['charge_id'] = $id;
                $this->db->where('charge_id', $org_chg_value['charge_id']);
                $this->db->where('org_id', $org_chg_value['org_id']);
                $q = $this->db->get('organisations_charges');               
              
                if ($q->num_rows() > 0) {
                    $update_id = $q->row()->id;
                    $this->db->where('id', $update_id);
                    $this->db->update('organisations_charges', $org_chg_value); 
                } else {
                    $this->db->insert('organisations_charges', $org_chg_value);                    
                }
            }
        }
    }

    public function addconsultcharges($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("consult_charges", $data);
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On Consult Charges id " . $insert_id;
        $action    = "Insert";
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
        $this->db->select('charge_categories.*,charge_type_master.charge_type');
        $this->db->where("charge_categories.charge_type_id", $charge_type);
        $this->db->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id');
        $query = $this->db->get("charge_categories");
        return $query->result_array();
    }

    public function searchFullText()
    {
        $this->db->select('charges.*,charge_type_master.charge_type as `charge_type_name`,charge_categories.name as `charge_category_name`,charge_units.unit');
        $this->db->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner');
        $this->db->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id');
        $this->db->join('charge_units', 'charge_units.id = charges.charge_unit_id', 'left');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('charges');
        return $query->result_array();
    }

    public function getDatatableAllRecord()
    {
        $this->datatables
            ->select('charges.*,charge_type_master.charge_type as `charge_type_name`,charge_categories.name as `charge_category_name`,charge_units.unit,tax_category.percentage')
            ->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner')
            ->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id')
            ->join("tax_category", 'tax_category.id = charges.tax_category_id', 'left')
            ->join('charge_units', 'charge_units.id = charges.charge_unit_id')
            
            ->searchable('charges.name,charge_categories.name,charge_type_master.charge_type,charge_units.unit,tax_category.percentage,charges.standard_charge')
            ->orderable('charges.name,charge_categories.name,charge_type_master.charge_type,charge_units.unit,tax_category.percentage,charges.standard_charge')
            
            ->sort('charges.id', 'desc')
            ->from('charges');
        return $this->datatables->generate('json');
    }

    public function getDetails($id, $organisation = "")
    {
        $this->db->select('charges.*,charge_type_master.id as `charge_type_master_id`,charge_type_master.charge_type as `charge_type_name`,charge_categories.name as `charge_category_name`,charge_units.unit,charge_units.id as `charge_unit_id`,tax_category.percentage');
        $this->db->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner');
        $this->db->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id');
        $this->db->join("tax_category", 'charges.tax_category_id = tax_category.id', 'left');
        $this->db->join('charge_units', 'charge_units.id = charges.charge_unit_id');
        $this->db->where('charges.id', $id);
        $query = $this->db->get('charges');
        if ($query->num_rows() > 0) {
            $result                           = $query->row();
            $result->{'organisation_charges'} = $this->getOrganisationCharges($result->id);
            return $result;
        }
    }

    public function getDetailsTpadoctor($id, $organisation = "")
    {
        $this->db->select('consult_charges.*,tpa_doctorcharges.org_charge,staff.name,staff.surname');
        $this->db->join('tpa_doctorcharges', 'consult_charges.id = tpa_doctorcharges.charge_id ', 'left');
        $this->db->join('staff', 'staff.id = consult_charges.doctor', "inner");
        $this->db->where('consult_charges.id', $id);
        if (!empty($organisation)) {
            $this->db->where('tpa_doctorcharges.org_id', $organisation);
        }
        $query = $this->db->get('consult_charges');
        return $query->row_array();
    }

    public function get_chargedoctorfee()
    {
        $this->db->order_by('id', 'desc');
        $this->db->select('consult_charges.*,staff.name,staff.surname');
        $this->db->join('staff', 'consult_charges.doctor = staff.id ', 'INNER');
        $query = $this->db->get("consult_charges");
        return $query->result_array();
    }

    public function getScheduleChargeBatch($charges_id)
    {
        $query = $this->db->where('id', $charges_id)->get('charges');
        return $query->row_array();
    }

    public function getScheduleChargeBatchTpadoctor($charges_id)
    {
        $query = $this->db->where('id', $charges_id)->get('consult_charges');
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
        $sql   = "SELECT organisations_charges.id,organisations_charges.org_charge,organisations_charges.org_charge,organisation.organisation_name,organisation.id as org_id FROM organisations_charges RIGHT OUTER JOIN organisation ON organisations_charges.org_id = organisation.id AND organisations_charges.charge_id = " . $charge_id . " ORDER BY organisation.id";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getpatientchargesbycaseid($case_id)
    {
        $sql   = "select patient_charges.*,charge_type_master.id as `charge_type_master_id`,charge_categories.name as charge_category_name,charges.charge_category_id,charges.name as `charge_name`,charge_units.unit from patient_charges inner JOIN charges on charges.id=patient_charges.charge_id INNER JOIN charge_categories on charge_categories.id=charges.charge_category_id INNER join charge_type_master on charge_type_master.id=charge_categories.charge_type_id  INNER join charge_units on charge_units.id =charges.charge_unit_id WHERE patient_charges.id in (select patient_charges.id from patient_charges INNER JOIN opd_details on opd_details.id=patient_charges.opd_id WHERE opd_details.case_reference_id=" . $this->db->escape($case_id) . " UNION select patient_charges.id from patient_charges INNER JOIN ipd_details on ipd_details.id=patient_charges.ipd_id WHERE ipd_details.case_reference_id=" . $this->db->escape($case_id) . ") order by patient_charges.id asc";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getOrganisationChargesTpadoctor($charge_id)
    {
        $query = $this->db->query("SELECT tpa_doctorcharges.id,tpa_doctorcharges.org_charge,organisation.organisation_name,organisation.id as org_id FROM tpa_doctorcharges RIGHT OUTER JOIN organisation ON tpa_doctorcharges.org_id = organisation.id AND tpa_doctorcharges.charge_id = '$charge_id' ORDER BY organisation.id");
        return $query->result_array();
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db->where('id', $id)
            ->delete('charges');

        $message   = DELETE_RECORD_CONSTANT . " On Charges id " . $id;
        $action    = "Delete";
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

    public function deletedoctorcharge($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $queery = $this->db->where('id', $id)
            ->delete('consult_charges');

        $message   = DELETE_RECORD_CONSTANT . " On Consult Charges id " . $id;
        $action    = "Delete";
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

        $message   = UPDATE_RECORD_CONSTANT . " On Charges id " . $data['id'];
        $action    = "Update";
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

    public function update_consultcharges($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db->where('id', $data['id'])
            ->update('consult_charges', $data);

        $message   = UPDATE_RECORD_CONSTANT . " On Consult Charges id " . $data['id'];
        $action    = "Update";
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

        $message   = UPDATE_RECORD_CONSTANT . " On Schedule Charges id " . $schedule_data['id'];
        $action    = "Update";
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

    public function add_charges($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] > 0) {

            $query = $this->db->where('id', $data['id'])
                ->update('patient_charges', $data);

            $message   = UPDATE_RECORD_CONSTANT . " On Patient Charges id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert("patient_charges", $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Patient Charges id " . $insert_id;
            $action    = "Insert";
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
            //return $return_value;
        }
        return $record_id;
    }

    public function getChargeById($id)  
    {   
        $query = $this->db->select('patient_charges.*,charge_categories.name as charge_category_name,charges.charge_category_id,charges.standard_charge,charges.name as `charge_name`,charge_units.unit,charge_type_master.id as `charge_type_master_id`,ipd_details.patient_id as `ipd_patient_id`,ipd_patient.patient_name as `ipd_patient_name`,opd_details.patient_id as `opd_patient_id`,opd_patient.patient_name as `opd_patient_name`,opd_details.case_reference_id as `opd_case_reference_id`,ipd_details.case_reference_id as `ipd_case_reference_id`,tax_category.name as apply_tax,tax_category.percentage,staff.employee_id')
            ->join('opd_details', 'patient_charges.opd_id = opd_details.id', 'left')
            ->join('patients as opd_patient', 'opd_details.patient_id = opd_patient.id', 'left')
            ->join('ipd_details', 'patient_charges.ipd_id = ipd_details.id', 'left')
            ->join('patients as ipd_patient', 'ipd_details.patient_id = ipd_patient.id', 'left')
            ->join('charges', 'patient_charges.charge_id = charges.id', 'inner')
            ->join('tax_category', 'charges.tax_category_id = tax_category.id', 'left')
            ->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner')
            ->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id')
            ->join('charge_units', 'charges.charge_unit_id = charge_units.id', 'left')
            ->join('visit_details', 'opd_details.id = visit_details.opd_details_id',"left")
            ->join('staff', 'staff.id = visit_details.cons_doctor',"left")
            ->where('patient_charges.id', $id)
            ->get('patient_charges');

        return $query->row();
    }

    public function getCharges($ipdid)
    {
        $query = $this->db->select('patient_charges.*,ipd_details.patient_id,charge_categories.name as charge_category_name,charge_type_master.charge_type,charges.charge_category_id,charges.standard_charge,charge_units.unit,charges.name')
            ->join('ipd_details', 'patient_charges.ipd_id = ipd_details.id')
            ->join('patients', 'ipd_details.patient_id = patients.id')
            ->join('charges', 'patient_charges.charge_id = charges.id', 'inner')
            ->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner')
            ->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id')
            ->join('charge_units', 'charges.charge_unit_id = charge_units.id', 'left')
            ->where('patient_charges.ipd_id', $ipdid)
            ->order_by('patient_charges.id', 'desc')			
            ->get('patient_charges');
        return $query->result_array();
    }

    public function getopdCharges($opdid)
    {
        $query = $this->db->select('patient_charges.*,charge_categories.name as charge_category_name,charge_type_master.charge_type,charges.charge_category_id,charges.standard_charge,charge_units.unit,charges.name,organisations_charges.org_charge,opd_details.patient_id')
            ->join('opd_details', 'patient_charges.opd_id = opd_details.id')
            ->join('patients', 'opd_details.patient_id = patients.id')
            ->join('charges', 'patient_charges.charge_id = charges.id')
            ->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner')
            ->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id')
            ->join('charge_units', 'charges.charge_unit_id = charge_units.id', 'left')
            ->join('visit_details', 'visit_details.opd_details_id = opd_details.id', "left")
            ->join('organisations_charges', 'organisations_charges.charge_id = charges.id and patients.organisation_id=organisations_charges.org_id', "left")
            ->where('patient_charges.opd_id', $opdid)
            ->group_by('patient_charges.id')
            ->get('patient_charges');
        return $query->result_array();
    }

    public function getopdChargesbyCaseId($case_id)
    {
        $query = $this->db->select('patient_charges.*,charge_categories.name as charge_category_name,charge_type_master.charge_type,charges.charge_category_id,charges.standard_charge,organisations_charges.id as oid,organisations_charges.org_charge,charge_units.unit,charges.name,opd_details.id as opd_id,opd_details.case_reference_id')
            ->join('opd_details', 'patient_charges.opd_id = opd_details.id')
            ->join('visit_details', 'visit_details.opd_details_id = opd_details.id')
            ->join('patients', 'opd_details.patient_id = patients.id')
            ->join('charges', 'patient_charges.charge_id = charges.id')
            ->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner')
            ->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id')
            ->join('charge_units', 'charges.charge_unit_id = charge_units.id', 'left')
            ->join('organisations_charges', 'visit_details.organisation_id = organisations_charges.id', 'left')
            ->where('opd_details.case_reference_id', $case_id)
            ->get('patient_charges');
        return $query->result_array();
    }

    public function getipdChargesbyCaseId($case_id)
    {
        $query = $this->db->select('patient_charges.*,ipd_details.patient_id,charge_categories.name as charge_category_name,charge_type_master.charge_type,charges.charge_category_id,charges.standard_charge,organisations_charges.id as oid,organisations_charges.org_charge,charge_units.unit,charges.name,ipd_details.id as ipd_id,ipd_details.case_reference_id')
            ->join('ipd_details', 'patient_charges.ipd_id = ipd_details.id')
            ->join('patients', 'ipd_details.patient_id = patients.id')
            ->join('charges', 'patient_charges.charge_id = charges.id', 'inner')
            ->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner')
            ->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id')
            ->join('charge_units', 'charges.charge_unit_id = charge_units.id', 'left')
            ->join('organisations_charges', 'ipd_details.organisation_id = organisations_charges.id', 'left')
            ->where('ipd_details.case_reference_id', $case_id)
            ->get('patient_charges');
        return $query->result_array();
    }

    public function getipdDischargeChargesbyCaseId($case_id)
    {
        $query = $this->db->select('patient_charges.*')
            ->join('ipd_details', 'patient_charges.ipd_id = ipd_details.id')
            ->join('patients', 'ipd_details.patient_id = patients.id')
            ->join('charges', 'patient_charges.charge_id = charges.id', 'inner')
            ->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner')
            ->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id')
            ->join('charge_units', 'charges.charge_unit_id = charge_units.id', 'left')
            ->join('organisations_charges', 'ipd_details.organisation_id = organisations_charges.id', 'left')
            ->where('ipd_details.case_reference_id', $case_id)
            ->get('patient_charges');
        return $query->result_array();
    }

    public function getAllchargesRecord($id, $visitid)
    {
        $this->datatables
            ->select('opd_patient_charges.*,patients.id as pid,charges.charge_type,charges.charge_category,charges.standard_charge,organisations_charges.id as oid,organisations_charges.org_charge')
            ->join('patients', 'opd_patient_charges.patient_id = patients.id', 'inner')
            ->join('charges', 'opd_patient_charges.charge_id = charges.id', 'inner')
            ->join('organisations_charges', 'opd_patient_charges.org_charge_id = organisations_charges.id', 'left')
            ->searchable('opd_patient_charges.date,charges.charge_type,charges.charge_category,charges.standard_charge,organisations_charges.org_charge')
            ->orderable('opd_patient_charges.date,charges.charge_type,charges.charge_category,charges.standard_charge,organisations_charges.org_charge')
            ->sort("organisations_charges.id", "desc")
            ->where('opd_patient_charges.patient_id', $id)
            ->where('opd_patient_charges.opd_details_id', $visitid)
            ->from('opd_patient_charges');
        $result = $this->datatables->generate('json');
        return $result;
    }

    public function deleteIpdPatientCharge($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db->where('id', $id)
            ->delete('patient_charges');
        $message   = DELETE_RECORD_CONSTANT . " On Patient Charges id " . $id;
        $action    = "Delete";
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
        $this->db->select('charges.*,charge_categories.name as `charge_category_name`');
        $this->db->where("charge_category_id", $charge_category);
        $this->db->join('charge_categories', 'charge_categories.id = charges.charge_category_id');
        $this->db->join('tax_category', 'tax_category.id = charges.tax_category_id', 'left');
        $query = $this->db->get("charges");
        return $query->result_array();
    }

    public function check_data_exists($standard_charge, $id, $staff_id)
    {
        if ($staff_id != 0) {
            $data  = array('id != ' => $staff_id, 'doctor' => $id);
            $query = $this->db->where($data)->get('consult_charges');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('doctor', $id);
            $query = $this->db->get('consult_charges');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function valid_doctor_id($str)
    {
        $standard_charge = $this->input->post('standard_charge');
        $id              = $this->input->post('doctor');
        $staff_id        = $this->input->post('editid');
        if (!isset($id)) {
            $id = 0;
        }
        if (!isset($staff_id)) {
            $staff_id = 0;
        }
        if ($this->check_data_exists($standard_charge, $id, $staff_id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function deleteOpdPatientCharge($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db->where('id', $id)
            ->delete('patient_charges');
        $message   = DELETE_RECORD_CONSTANT . " On Patient Charges id " . $id;
        $action    = "Delete";
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

    public function getChargeByChargeId($id)
    {
        return $this->db->select('charges.*,tax_category.percentage')
            ->from('charges')
            ->join('tax_category', 'tax_category.id=charges.tax_category_id')
            ->where('charges.id', $id)
            ->get()->row_array();
    }

    public function getChargeDetailsById($id)
    {
        $result = $this->db->select("charges.standard_charge,tax_category.percentage")
            ->join('tax_category', 'tax_category.id = charges.tax_category_id', 'LEFT')
            ->where("charges.id", $id)
            ->get("charges")
            ->row();
        return $result;
    }
}
