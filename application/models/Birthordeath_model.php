<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Birthordeath_model extends MY_Model
{

    public function getDetails($id)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('birth_report', '', 1, '');
        $custom_field_column_array = array();

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'birth_report.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable = implode(',', $field_var_array);
        $this->db->select('birth_report.*,patients.patient_name,patients.id as patient_id,' . $field_variable);
        $this->db->join('patients', 'patients.id = birth_report.patient_id', 'inner');
        $this->db->where('birth_report.id', $id);
        $query = $this->db->get('birth_report');
        return $query->row_array();
    }

    public function getDeDetailsbycaseId($case_id)
    {
        return $this->db->select('id')->from('death_report')->where('case_reference_id', $case_id)->get()->row_array();
    }

    public function getBirthDetails()
    {
        $this->db->order_by('id', 'desc');
        $this->db->select('birth_report.*,patients.patient_name');
        $this->db->join('patients', 'patients.id=birth_report.mother_name');
        $query = $this->db->get("birth_report");
        return $query->result_array();
    }

    public function changeStatus($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $data["id"])->update("custom_fields", $data);

        $message   = UPDATE_RECORD_CONSTANT . " On Custom Fields id " . $data['id'];
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
	
    //new
    public function getAllbirthRecord()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('birth_report', 1);
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'birth_report.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);

        $this->datatables
            ->select('birth_report.id,birth_report.child_name,birth_report.gender, birth_report.birth_date,birth_report.father_name, birth_report.birth_report,patients.patient_name,patients.id as mother_id,birth_report.case_reference_id' . $field_variable)
            ->searchable('birth_report.id,birth_report.case_reference_id,birth_report.child_name,birth_report.gender,birth_report.birth_date,patients.patient_name,birth_report.father_name' . $custom_field_column.',birth_report.id')
            ->orderable('birth_report.id,birth_report.case_reference_id,birth_report.child_name,birth_report.gender,birth_report.birth_date,patients.patient_name,birth_report.father_name' . $custom_field_column.',birth_report.id') 	
			
            ->join('patients', 'patients.id=birth_report.patient_id', 'left')
            ->sort('birth_report.id', 'desc')
            ->from('birth_report');
        return $this->datatables->generate('json');
    }

    public function getAlldeathRecord()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('death_report', 1);
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'death_report.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);

        $this->datatables
            ->select('death_report.id,patients.gender, death_report.attachment,death_report.attachment_name,death_report.death_date,death_report.guardian_name,death_report.case_reference_id, death_report.death_report,patients.patient_name,patients.id as `patientid`' . $field_variable)
            
            ->searchable('death_report.id,death_report.case_reference_id,patients.patient_name,death_report.guardian_name,patients.gender,death_report.death_date' . $custom_field_column.',death_report.id')
                        
            ->orderable('death_report.id,death_report.case_reference_id,patients.patient_name,death_report.guardian_name,patients.gender,death_report.death_date' . $custom_field_column.',death_report.id')           
            
            ->join('patients', 'patients.id=death_report.patient_id', 'left')
            ->sort('death_report.id', 'desc')
            ->from('death_report');
        return $this->datatables->generate('json');
    }

    public function getDetailsCustom($id)
    {
        $query = $this->db->select('custom_fields.*')->where('id', $id)->get('custom_fields');
        return $query->row_array();
    }

    public function getDeDetails($id)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('death_report', '', 1);
        $custom_field_column_array = array();

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'death_report.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable = implode(',', $field_var_array);
        $this->db->select('death_report.*,patients.patient_name,patients.id as patient_id,patients.gender,patients.address,' . $field_variable);
        $this->db->join('patients', 'patients.id = death_report.patient_id');
        $this->db->where('death_report.id', $id);
        $query = $this->db->get('death_report');
        return $query->row_array();
    }

    public function getDeDetailsCustom($id)
    {
        $query = $this->db->select('custom_fields.*')->where('id', $id)->get('custom_fields');
        return $query->row_array();
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('birth_report');
        $message   = DELETE_RECORD_CONSTANT . " Where Birth Report  id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id, 'birth_report');
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
    }

    public function deletecustom($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $query = $this->db->where('id', $id)
            ->delete('custom_fields');
        $message   = DELETE_RECORD_CONSTANT . " On Custom Fields id " . $id;
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
            return $return_value;
        }
    }

    public function deletedeath($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('death_report');
        $message   = DELETE_RECORD_CONSTANT . " Where Death Report  id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id, 'death_report');
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
    }

    public function getBirthDetailsCustom()
    {
        $this->db->order_by('id', 'desc');
        $this->db->select('custom_fields.*');
        $this->db->where('belong_to', 'birth_report');
        $query = $this->db->get("custom_fields");
        return $query->result_array();
    }

    public function getDeathDetailsCustom()
    {
        $this->db->order_by('id', 'desc');
        $this->db->select('custom_fields.*');
        $this->db->where('belong_to', 'death_report');
        $query = $this->db->get("custom_fields");
        return $query->result_array();
    }

    public function getBirthData($id)
    {
        $this->db->select('birth_report.*');
        $this->db->where('birth_report.id', $id);
        $query = $this->db->get("birth_report");
        return $query->row_array();
    }

    public function getDeathDetails()
    {
        $this->db->order_by('id', 'desc');
        $this->db->select('death_report.*,patients.patient_name,patients.gender');
        $this->db->join('patients', 'patients.id = death_report.patient');
        $query = $this->db->get("death_report");
        return $query->result_array();
    }

    public function addDeathdata($data)
    {

        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('death_report', $data);
            $message   = UPDATE_RECORD_CONSTANT . " For Death Report id " . $data['id'];
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
            $this->db->insert('death_report', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Death Report id " . $insert_id;
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
    }

    public function addBirthdata($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('birth_report', $data);
            $message   = UPDATE_RECORD_CONSTANT . " For Birth Report id " . $data['id'];
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
            $this->db->insert('birth_report', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Birth Report id " . $insert_id;
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
    }

}
