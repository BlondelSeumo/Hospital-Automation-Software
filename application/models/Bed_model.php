<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bed_model extends MY_Model
{

    public function bedcategorie($table_name)
    {
        $this->db->select('id, name');
        $this->db->from($table_name);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function valid_bed($str)
    {
        $name = $this->input->post('name');
        if ($this->check_floor_exists($name)) {
            $this->form_validation->set_message('check_exists', 'Bed already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_floor_exists($name)
    {
        $bedid = $this->input->post("bedid");
        if ($bedid != 0) {
            $data  = array('name' => $name, 'id !=' => $bedid);
            $query = $this->db->where($data)->get('bed');

            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('name', $name);
            $query = $this->db->get('bed');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function bedNoType()
    {
        $query = $this->db->select('bed.*,bed_type.id as btid,bed_type.name as bed_type')
            ->join('bed_type', 'bed.bed_type_id = bed_type.id')
            ->where('bed.is_active', 'yes')
            ->get('bed');
        return $query->result_array();
    }

    public function bed_list($id = null)
    {
        $data = array();
        $this->db->select('bed.*, bed_type.name as bed_type_name,floor.name as floor_name, bed_group.name as bedgroup,bed_group.id as bedgroupid,patients.id as pid,patients.is_active as patient_status,patients.id as patient_unique_id,patients.patient_name,patients.gender,patients.guardian_name,patients.mobileno,ipd_details.date,ipd_details.id as ipd_details_id,ipd_details.bed, ipd_details.discharged as ipd_discharged, staff.name as staff,staff.surname,staff.employee_id')->from('bed');
        $this->db->join('bed_type', 'bed.bed_type_id = bed_type.id', 'left');
        $this->db->join('bed_group', 'bed.bed_group_id = bed_group.id', 'left');
        $this->db->join('floor', 'floor.id = bed_group.floor', 'left');
        $this->db->join('ipd_details', 'bed.id = ipd_details.bed', 'left');
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', 'left');
        $this->db->join('patients', 'patients.id = ipd_details.patient_id', 'left');
        $this->db->order_by('bed.id', 'asc');
        if ($id) {
            $this->db->where('bed.id', $id);
        } else {
            $this->db->order_by('bed.id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            $result = $query->row_array();
        } else {
            $result = $query->result_array();
        }

        if (!empty($result)) {
            foreach ($result as $key => $value) {
                if ($value["pid"]) {
                    if (($value['patient_status'] == 'yes') && ($value['ipd_discharged'] == 'no')) {
                        $data[] = $value;
                    } elseif (($value['is_active'] == 'yes')) {
                        $val        = $value['bed'];
                        $data[$val] = $value;
                    }
                } else {
                    $data[] = $value;
                }
            }
        }

        return $data;
    }

    public function bed_active()
    {        
        $result = $this->db->select("bed.id, bed.name, bed.is_active,patients.id as pid,patients.is_active as patient_status,patients.patient_name,patients.gender,patients.guardian_name,patients.mobileno,ipd_details.bed as bid,ipd_details.discharged as ipd_discharged")
            ->join("ipd_details", "ipd_details.bed = bed.id", "left")
            ->join("patients", "patients.id=ipd_details.patient_id")
            ->where("bed.is_active", "yes")
            ->group_by("bed.id")
            ->get("bed")
            ->result_array();
        return $result;
    }
    
    public function bed_listsearch($id = null)
    {
        $data = array();
        $this->db->select('bed.*, bed_type.name as bed_type_name,floor.name as floor_name, bed_group.name as bedgroup,bed_group.id as bedgroupid')->from('bed');
        $this->db->join('bed_type', 'bed.bed_type_id = bed_type.id');
        $this->db->join('bed_group', 'bed.bed_group_id = bed_group.id');
        $this->db->join('floor', 'floor.id = bed_group.floor');
        $this->db->order_by('bed.id', 'asc');
        if ($id != null) {
            $this->db->where('bed.id', $id);
        } else {
            $this->db->order_by('bed.id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            $result = $query->row_array();
        } else {
            $result = $query->result_array();
        }
        return $result;
    }

    public function getBedDetails($id)
    {
        $data = array();
        $this->db->select('bed.*, bed_type.name as bed_type_name,floor.name as floor_name, bed_group.name as bedgroup,bed_group.id as bedgroupid')->from('bed');
        $this->db->join('bed_type', 'bed.bed_type_id = bed_type.id');
        $this->db->join('bed_group', 'bed.bed_group_id = bed_group.id');
        $this->db->join('floor', 'floor.id = bed_group.floor');
        $this->db->where("bed.id", $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function savebed($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("bed", $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Bed id " . $data['id'];
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
        } else {
            $this->db->insert("bed", $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Bed id " . $insert_id;
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
                return $record_id;
            }
            return $insert_id;
        }
    }

    public function getbedbybedgroup($bed_group, $active = '', $bed_id = '')
    {
        $this->db->select('bed.*, bed_type.name as bed_type_name,bed_group.name as bedgroup ')->from('bed');
        $this->db->join('bed_type', 'bed.bed_type_id = bed_type.id');
        $this->db->join('bed_group', 'bed.bed_group_id = bed_group.id');
        if (!empty($active)) {
            $this->db->where('bed.is_active', $active);
        }
        $this->db->where('bed.bed_group_id', $bed_group);
        if (!empty($bed_id)) {
            $this->db->or_where('bed.id', $bed_id);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("bed");
        $message   = DELETE_RECORD_CONSTANT . " On Bed id " . $id;
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

    public function getBedStatus()
    {
        $this->db->select('bed.*, bed_type.name as bed_type_name,bed_group.name as bedgroup,floor.name as floor_name')->from('bed');
        $this->db->join('bed_type', 'bed.bed_type_id = bed_type.id');
        $this->db->join('bed_group', 'bed.bed_group_id = bed_group.id');
        $this->db->join('floor', 'floor.id = bed_group.floor');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function checkbed($bedid)
    {
        $query  = $this->db->select('bed.is_active')->where("id", $bedid)->get("bed");
        $result = $query->row_array();
        if ($result['is_active'] == 'yes') {
            return true;
        } else {
            return false;
        }
    }

    public function saveBedHistory($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("patient_bed_history", $data);
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On Patient bed history id " . $insert_id;
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

    public function updateBedHistory($bed_history)
    {
        $this->db->update("patient_bed_history", $bed_history, array("is_active" => "yes", "case_reference_id" => $bed_history['case_reference_id']));
    }

    public function getBedHistory($case_reference_id)
    {
        $result = $this->db->select("bed_group.name as bed_group,bed.name as bed,patient_bed_history.from_date,patient_bed_history.to_date, patient_bed_history.is_active")
            ->join("bed_group", "bed_group.id=patient_bed_history.bed_group_id", "left")
            ->join("bed", "bed.id=patient_bed_history.bed_id", "left")
            ->where("case_reference_id", $case_reference_id)
            ->get("patient_bed_history")
            ->result();
        return $result;
    }

    public function updateBedHistoryStatus($case_reference_id)
    {
        $this->db->update("patient_bed_history", array("is_active" => "no"), array("case_reference_id" => $case_reference_id));
    }

}
