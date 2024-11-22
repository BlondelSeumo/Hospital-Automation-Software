<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Radio_model extends MY_Model
{
    public function add($data, $insert_parameter_array, $update_parameter_array, $deleted_parameter_array)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //======================Code Start==============================
        if (isset($data['id']) && $data['id'] > 0) {
            $this->db->where('id', $data['id']);
            $this->db->update('radio', $data);
            $insert_id = $data['id'];
            $message = UPDATE_RECORD_CONSTANT . " On Radio id " . $insert_id;
            $action = "Update";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('radio', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Radio id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }

        if (!empty($insert_parameter_array)) {
            foreach ($insert_parameter_array as $params_key => $params_value) {
                $insert_parameter_array[$params_key]['radiology_id'] = $insert_id;
            }
            $this->db->insert_batch('radiology_parameterdetails', $insert_parameter_array);
        }
        if (!empty($update_parameter_array)) {
            $this->db->update_batch('radiology_parameterdetails', $update_parameter_array, 'id');
        }
        if (!empty($deleted_parameter_array)) {
            $this->db->where_in('id', $deleted_parameter_array);
            $this->db->delete('radiology_parameterdetails');
        }

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


    public function getpatientRadiologyYearCounter($patient_id, $year)
    {
        $sql = "SELECT count(*) as `total_visits`,Year(date) as `year` FROM `radiology_billing` WHERE YEAR(date) >= " . $this->db->escape($year) . " AND patient_id=" . $this->db->escape($patient_id) . " GROUP BY  YEAR(date)";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


    public function getAllradiologyRecord()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('radiology', 1);
        $custom_field_column_array = array();

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'radiology_report.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable      = implode(',', $field_var_array);
        $custom_field_column = implode(',', $custom_field_column_array);

        $this->datatables
            ->select('radiology_report.*, radio.id as rid,radio.test_name, radio.short_name,staff.name,staff.surname,charges.id as cid,charges.charge_category_id,charges.standard_charge,patients.patient_name,' . $field_variable)
            ->join('radio', 'radiology_report.radiology_id = radio.id', 'inner')
            ->join('staff', 'staff.id = radiology_report.consultant_doctor', "left")
            ->join('charges', 'charges.id = radio.charge_id')
            ->join('patients', 'patients.id = radiology_report.patient_id')
            ->searchable('radiology_report.bill_no,radiology_report.reporting_date,patients.patient_name,radio.test_name,radio.short_name,staff.name,' . $custom_field_column)
            ->orderable('radiology_report.bill_no,radiology_report.reporting_date,radiology_report.patient_id,radio.test_name,radio.short_name,staff.name,radiology_report.description,' . $custom_field_column)
            ->sort('radiology_report.id', 'desc')
            ->from('radiology_report');
        return $this->datatables->generate('json');
    }

    public function getAllradiologytest()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('radiologytest', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'radio.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $this->datatables
            ->select('radio.*,lab.id as category_id,lab.lab_name,charges.standard_charge,tax_category.percentage' . $field_variable)
            ->join('lab', 'radio.radiology_category_id = lab.id', 'left')
            ->join('charges', 'radio.charge_id = charges.id', 'left')
            ->join('tax_category', 'tax_category.id = charges.tax_category_id', 'left')
            ->searchable('radio.test_name,radio.short_name,radio.test_type,lab.lab_name,radio.sub_category,radio.report_days' . $custom_field_column . ',tax_category.percentage,charges.standard_charge')
            ->orderable('radio.test_name,radio.short_name,radio.test_type,lab.lab_name,radio.sub_category,radio.report_days' . $custom_field_column . ',tax_category.percentage,charges.standard_charge')
            ->sort('radio.id', 'desc')
            ->where('radio.radiology_category_id = lab.id')
            ->from('radio');
        return $this->datatables->generate('json');
    }

    public function getAllradiologybillRecord()
    {
        $custom_fields             = $this->customfield_model->get_custom_fields('radiology', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'radiology_billing.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $this->datatables
            ->select('radiology_billing.*,(SELECT IFNULL(SUM(transactions.amount),0) from transactions WHERE transactions.radiology_billing_id=radiology_billing.id ) as paid_amount,patients.patient_name,patients.id as pid,staff.name,staff.surname,staff.employee_id' . $field_variable)
            ->join('patients', 'patients.id = radiology_billing.patient_id', 'left')
            ->join('staff', 'staff.id = radiology_billing.doctor_id', 'left')
            ->searchable('radiology_billing.id,radiology_billing.case_reference_id,radiology_billing.date,patients.patient_name,radiology_billing.doctor_id,radiology_billing.note' . $custom_field_column . ',radiology_billing.net_amount,(SELECT SUM(transactions.amount) from transactions WHERE transactions.radiology_billing_id=radiology_billing.id ) as paid_amount')
            ->orderable('radiology_billing.id,radiology_billing.case_reference_id,radiology_billing.date,patients.patient_name,radiology_billing.doctor_id,radiology_billing.note' . $custom_field_column . ',radiology_billing.net_amount, paid_amount')
            ->sort('radiology_billing.id', 'desc')
            ->from('radiology_billing');
        return $this->datatables->generate('json');
    }

    public function getradiologybillByCaseId($case_id)
    {
        $this->datatables
            ->select('radiology_billing.*,sum(transactions.amount) as paid_amount,patients.patient_name,patients.id as patient_unique_id,staff.name,staff.surname,staff.employee_id')
            ->join('patients', 'patients.id = radiology_billing.patient_id', 'left')
            ->join('transactions', 'transactions.radiology_billing_id = radiology_billing.id', 'left')
            ->join('staff', 'staff.id = radiology_billing.doctor_id', 'left')
            ->searchable('radiology_billing.id,radiology_billing.case_reference_id,radiology_billing.date,patients.patient_name')
            ->orderable('radiology_billing.id,radiology_billing.case_reference_id,radiology_billing.date,patients.patient_name,radiology_billing.doctor_id,radiology_billing.note,radiology_billing.net_amount,paid_amount')
            ->group_by('transactions.radiology_billing_id')
            ->sort('radiology_billing.id', 'desc')
            ->where('radiology_billing.case_reference_id', $case_id)
            ->from('radiology_billing');
        return $this->datatables->generate('json');
    }

    public function getRadiologyBillAmountByCaseId($case_id)
    {
        $sql = "SELECT sum(net_amount) as `net_amount`,IFNULL((SELECT sum(amount) FROM `transactions` WHERE radiology_billing_id in (SELECT radiology_billing.id FROM `radiology_billing` WHERE case_reference_id=" . $this->db->escape($case_id) . ") and section = 'Radiology'),0) as `paid_amount` FROM `radiology_billing` WHERE case_reference_id=" . $this->db->escape($case_id);
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function getradiologyByCaseId($case_id)
    {
        $query = $this->db->select('radiology_billing.*,IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.radiology_billing_id=radiology_billing.id),0) as `amount_paid`,patients.patient_name,patients.id as patient_id')
            ->join('patients', 'patients.id = radiology_billing.patient_id', 'left')
            ->where('radiology_billing.case_reference_id', $case_id)
            ->get('radiology_billing');
        return $query->result();
    }

    public function getradiotestDetails()
    {
        $this->db->select('radio.*,lab.id as category_id,lab.lab_name as category_name,charges.id as charge_id, charges.name, charges.charge_category_id, charges.standard_charge, charges.description');
        $this->db->join('lab', 'radio.radiology_category_id = lab.id', 'left');
        $this->db->join('charges', 'radio.charge_id = charges.id', 'left');
        $this->db->order_by('radio.id', 'desc');
        $query = $this->db->get('radio');
        return $query->result_array();
    }

    public function totalPatientRadiology($patient_id)
    {
        $query = $this->db->select('count(radiology_billing.patient_id) as total')
            ->where('patient_id', $patient_id)
            ->get('radiology_billing');
        return $query->row_array();
    }
	
    public function getRadiologyBillByID($id, $patient_panel = null)
    {
        if ($patient_panel == 'patient') {
            $custom_fields = $this->customfield_model->get_custom_fields('radiology', '', '', '', 1);
        } else {
            $custom_fields = $this->customfield_model->get_custom_fields('radiology');
        }

        $custom_field_column_array = array();
        $field_var_array = array();
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'radiology_billing.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable      = implode(',', $field_var_array);
        $query = $this->db->select('radiology_billing.*,blood_bank_products.name as blood_group_name,IFNULL((SELECT SUM(amount) FROM transactions WHERE radiology_billing_id=radiology_billing.id),0) as total_deposit,patients.patient_name,patients.id as patient_unique_id,patients.age, patients.month, patients.day,patients.gender,patients.dob,patients.blood_group,patients.mobileno,patients.email,patients.address,staff.employee_id,staff.name,staff.surname,staff.employee_id,transactions.payment_mode,transactions.amount,transactions.cheque_no,transactions.cheque_date,transactions.note as `transaction_note`,staff_roles.role_id as staff_roles_id,org.organisation_name,radiology_billing.insurance_validity,radiology_billing.insurance_id,' . $field_variable)
            ->join('patients', 'radiology_billing.patient_id = patients.id')
            ->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id', 'left')
            ->join('staff', 'staff.id = radiology_billing.generated_by')
            ->join('staff_roles', 'staff_roles.staff_id = staff.id')
            ->join('transactions', 'transactions.id = radiology_billing.transaction_id', 'left')
            ->join('organisation', 'organisation.id = patients.organisation_id', 'left')
            ->join('organisation as org', 'org.id = radiology_billing.organisation_id','left')
            ->where("radiology_billing.id", $id)
            ->get('radiology_billing');
        if ($query->num_rows() > 0) {
            $result                       = $query->row();
            $result->{'radiology_report'} = $this->getReportByBillId($result->id);
            return $result;
        }
        return false;
    }

    public function getReportByBillId($id)
    {
        $query = $this->db->select('radiology_report.*,radio.test_name,radio.short_name,radio.report_days,radio.id as pid,radio.charge_id as cid,staff.name,staff.surname,charges.charge_category_id,charges.name,charges.standard_charge,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`')
            ->join('radiology_billing', 'radiology_report.radiology_bill_id = radiology_billing.id')
            ->join('radio', 'radiology_report.radiology_id = radio.id')
            ->join('staff', 'staff.id = radiology_report.consultant_doctor', "left")
            ->join('staff as collection_specialist_staff', 'collection_specialist_staff.id = radiology_report.collection_specialist', "left")
            ->join('staff as approved_by_staff', 'approved_by_staff.id = radiology_report.approved_by', "left")
            ->join('charges', 'radio.charge_id = charges.id')
            ->where('radiology_report.radiology_bill_id', $id)
            ->get('radiology_report');
        return $query->result();
    }

    public function getRadiologyReportByID($id)
    {
        $query = $this->db->select('radiology_report.*,radio.id as pid,radio.charge_id as cid,charges.charge_category_id,charges.name,charges.standard_charge,patients.patient_name')
            ->join('patients', 'radiology_report.patient_id = patients.id')
            ->join('radio', 'radiology_report.radiology_id = radio.id')
            ->join('charges', 'radio.charge_id = charges.id')
            ->where("radiology_report.id", $id)
            ->get('radiology_report');
        return $query->row_array();
    }

    public function addBill($data, $addReports, $updateReports, $deleteReports, $pathology_billing_id, $transcation_data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //======================Code Start==============================

        if (isset($data['id']) && $data['id'] > 0) {
            $this->db->where('id', $data['id']);
            $this->db->update('radiology_billing', $data);
            $id = $data['id'];
            $message = UPDATE_RECORD_CONSTANT . " On Radiology Billing id " . $id;
            $action = "Update";
            $record_id = $id;
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert("radiology_billing", $data);
            $id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Radiology Billing id " . $id;
            $action = "Insert";
            $record_id = $id;
            $this->log($message, $record_id, $action);
        }

        if (!empty($addReports)) {
            foreach ($addReports as $report_key => $report_value) {
                $addReports[$report_key]['radiology_bill_id'] = $id;
            }
            $this->db->insert_batch('radiology_report', $addReports);
        }
		
        if (!empty($updateReports)) {
            $this->db->update_batch('radiology_report', $updateReports, 'id');
        }

        if (!empty($deleteReports) && $pathology_billing_id > 0) {
            $this->db->where_in('id', $deleteReports);
            $this->db->where('radiology_bill_id', $pathology_billing_id);
            $this->db->delete('radiology_report');
        }

        if (isset($transcation_data) && !empty($transcation_data)) {
            $transcation_data['radiology_billing_id'] = $id;
            $this->transaction_model->add($transcation_data);
        }
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $id;
        }
    }

    public function getBillNo()
    {
        $query = $this->db->select("max(id) as id")->get('radiology_billing');
        return $query->row_array();
    }

    public function addparameter($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('radiology_parameterdetails', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Radiology Parameter Details id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert_batch('radiology_parameterdetails', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Radiology Parameter Details id " . $insert_id;
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

    public function delete_parameter($delete_arr)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        foreach ($delete_arr as $key => $value) {
            $id = $value["id"];
            $this->db->where("id", $value["id"])->delete("radiology_parameterdetails");
            $message = DELETE_RECORD_CONSTANT . " On Radiology Paramete Details id " . $id;
            $action = "Delete";
            $record_id = $id;
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
            return true;
        }
    }

    public function getpathoparameter($id = null)
    {
        if (!empty($id)) {
            $this->db->select('radiolog_parameter.*,unit.unit_name');
            $this->db->from('radiolog_parameter');
            $this->db->join('unit', 'radiolog_parameter.unit = unit.id', 'left');
            $this->db->where("radiolog_parameter.id", $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->select('radiology_parameter.*,unit.unit_name');
            $this->db->from('radiology_parameter');
            $this->db->join('unit', 'radiology_parameter.unit = unit.id', 'left');
            $this->db->join('radio', 'radiology_parameter.id = radio.radiology_parameter_id', 'left');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function getparameterDetails($id)
    {
        $query = $this->db->select('radiology_parameterdetails.*,radiology_parameter.parameter_name,radiology_parameter.reference_range,radiology_parameter.unit,unit.unit_name')
            ->join('radiology_parameter', 'radiology_parameter.id = radiology_parameterdetails.parameter_id')
            ->join('unit', 'unit.id = radiology_parameter.unit')
            ->where('radiology_parameterdetails.radiology_id', $id)
            ->get('radiology_parameterdetails');
        return $query->result_array();
    }

    public function getparameterDetailsforpatient($report_id)
    {
        $query = $this->db->select('radiology_report_parameterdetails.*,radiology_parameter.parameter_name,radiology_parameter.reference_range,radiology_parameter.unit,unit.unit_name')
            ->join('radiology_parameter', 'radiology_parameter.id = radiology_report_parameterdetails.parameter_id')
            ->join('unit', 'unit.id = radiology_parameter.unit')
            ->where("radiology_report_parameterdetails.radiology_report_id", $report_id)
            ->get('radiology_report_parameterdetails');
        return $query->result_array();
        echo $this->db->last_query();
    }

    public function update($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('radio', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Radio id " . $data['id'];
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
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('radio');
        $this->db->where("radiology_id", $id)->delete('radiology_parameterdetails');
        $message   = DELETE_RECORD_CONSTANT . " On  Radiology Paramete Details where radiology id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id, 'radiologytest');
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

    public function deleteRadiologyBill($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('radiology_billing');
		
		$this->db->delete("transactions", array("radiology_billing_id" => $id)); 

        $message   = DELETE_RECORD_CONSTANT . " On Radiology Billing id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id, 'radiology');
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

    public function getRadiology($id = null)
    {
        if (!empty($id)) {
            $this->db->where("radio.id", $id);
        }
        $query = $this->db->select('radio.*,charges.charge_category_id,charges.name,charges.standard_charge')->join('charges', 'radio.charge_id = charges.id')->order_by('radio.id', 'desc')->get('radio');
        if (!empty($id)) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getradiologytest($id = null)
    {
        if (!empty($id)) {
            $this->db->where("radio.id", $id);
        }
        $query = $this->db->select('radio.*,')->order_by('radio.id', 'desc')->get('radio');
        if (!empty($id)) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getBillDetails($id)
    {
        $this->db->select('radiology_report.*,radio.test_name,radio.short_name,radio.report_days,patients.patient_name,patients.id,patients.age,patients.gender,patients.blood_group,patients.mobileno,patients.email,patients.address,staff.name as doctorname,staff.surname as doctorsurname');
        $this->db->where('radiology_report.id', $id);
        $this->db->join('radio', 'radio.id = radiology_report.radiology_id');
        $this->db->join('patients', 'patients.id = radiology_report.patient_id');
        $this->db->join('staff', 'staff.id = radiology_report.consultant_doctor', 'left');
        $query        = $this->db->get('radiology_report');
        $result       = $query->row_array();
        $generated_by = $result["generated_by"];
        $staff_query  = $this->db->select("staff.name,staff.surname")
            ->where("staff.id", $generated_by)
            ->get("staff");
        $staff_result               = $staff_query->row_array();
        $result["generated_byname"] = $staff_result["name"] . $staff_result["surname"];
        return $result;
    }

    public function getRadiologyChargeByPatient($id,$patient_id=null)
    {
        $sql    = "SELECT * from (SELECT `radio`.*, `lab`.`id` as `category_id`, `lab`.`lab_name`, `charges`.`name`, `charges`.`charge_category_id`, `charges`.`standard_charge`, `charges`.`description`, `charges`.`name` as `charge_name`, `charge_categories`.`name` as `charge_category_name`, `tax_category`.`name` as `apply_tax`, `tax_category`.`percentage`,organisations_charges.org_id,organisations_charges.org_charge,IFNULL(patients.organisation_id,0) as temp_col,patients.insurance_validity   FROM `radio` LEFT JOIN `lab` ON `radio`.`radiology_category_id` = `lab`.`id` 
        LEFT JOIN `charges` ON `radio`.`charge_id` = `charges`.`id` 
        JOIN `charge_categories` ON `charge_categories`.`id` = `charges`.`charge_category_id` 
        JOIN `tax_category` ON `tax_category`.`id` = `charges`.`tax_category_id` 
        JOIN `charge_type_master` ON `charge_categories`.`charge_type_id` = `charge_type_master`.`id`
        JOIN `organisations_charges` ON `organisations_charges`.`charge_id` = `charges`.`id` 
        LEFT join patients on `patients`.`organisation_id` = `organisations_charges`.`org_id` and patients.id= ".$this->db->escape($patient_id)."
        WHERE `radio`.`id` = ".$this->db->escape($id).") d where d.temp_col = (SELECT CASE WHEN COUNT(*) = 1 THEN IFNULL(patients.organisation_id,0) ELSE COUNT(*) END AS 'Updated City' FROM `patients` where patients.id=".$this->db->escape($patient_id).")";
        $query  = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result;
        }

        return false;
    }

    public function getDetails($id)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('radiology', 1);
        $custom_field_column_array = array();

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'radio.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable      = implode(',', $field_var_array);
        $custom_field_column = implode(',', $custom_field_column_array);
        $this->db->select('radio.*,lab.id as category_id,lab.lab_name, charges.id as charge_id, charges.name, charges.charge_category_id, charges.standard_charge, charges.description,charges.name as `charge_name`,charge_categories.name as `charge_category_name`,tax_category.name as apply_tax,tax_category.percentage,' . $field_variable);
        $this->db->join('lab', 'radio.radiology_category_id = lab.id', 'left');
        $this->db->join('charges', 'radio.charge_id = charges.id', 'left');
        $this->db->join('charge_categories', 'charge_categories.id = charges.charge_category_id');
        $this->db->join('tax_category', 'tax_category.id = charges.tax_category_id');
        $this->db->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id');
        $this->db->where('radio.id', $id);
        $this->db->order_by('radio.id', 'desc');
        $query = $this->db->get('radio');
        if ($query->num_rows() > 0) {
            $result                          = $query->row();
            $result->{'radiology_parameter'} = $this->getRadiologyParamsById($result->id);
            return $result;
        }
        return false;
    }

    public function getRadiologyParamsById($id)
    {
        $this->db->select('radiology_parameterdetails.*,radiology_parameter.parameter_name,radiology_parameter.reference_range,radiology_parameter.unit,unit.unit_name');
        $this->db->join('radiology_parameter', 'radiology_parameter.id = radiology_parameterdetails.radiology_parameter_id');
        $this->db->join('unit', 'unit.id = radiology_parameter.unit');
        $this->db->where('radiology_id', $id);
        $query = $this->db->get('radiology_parameterdetails');
        return $query->result();
    }

    public function getradioBillDetails($id)
    {
        $this->db->select('radiology_billing.*,sum(transactions.amount) as total_deposit,patients.patient_name,patients.patient_unique_id,patients.age,patients.gender,patients.blood_group,patients.mobileno,patients.email,patients.address');
        $this->db->where('radiology_billing.id', $id);
        $this->db->join('patients', 'patients.id = radiology_billing.patient_id', "left");
        $this->db->join('transactions', 'radiology_billing.id = transactions.radiology_billing_id', "left");
        $query        = $this->db->get('radiology_billing');
        $result       = $query->row_array();
        $generated_by = $result["generated_by"];
        $staff_query  = $this->db->select("staff.name,staff.surname")
            ->where("staff.id", $generated_by)
            ->get("staff");
        $staff_result               = $staff_query->row_array();
        $result["generated_byname"] = $staff_result["name"] . $staff_result["surname"];
        return $result;
    }

    public function updateparameter($condition)
    {
        $SQL = "INSERT INTO radiology_parameterdetails
                    (parameter_id, id)
                    VALUES
                    " . $condition . "
                    ON DUPLICATE KEY UPDATE
                    parameter_id=VALUES(parameter_id)";
        $query = $this->db->query($SQL);
    }

    public function getMaxId()
    {
        $query  = $this->db->select('max(id) as bill_no')->get("radiology_report");
        $result = $query->row_array();
        return $result["bill_no"];
    }

    public function getAllBillDetails($id)
    {
        $query = $this->db->select('radiology_report.*,radio.test_name,radio.short_name,radio.report_days,radio.charge_id')
            ->join('radio', 'radio.id = radiology_report.radiology_id')
            ->where('radiology_report.id', $id)
            ->get('radiology_report');
        return $query->result_array();
    }

    public function getAllradioBillDetails($id)
    {
        $query = $this->db->select('radiology_report.*,radio.test_name,radio.short_name,radio.report_days,radio.charge_id')
            ->join('radio', 'radio.id = radiology_report.radiology_id')
            ->join('radiology_billing', 'radiology_report.radiology_bill_id = radiology_billing.id', 'left')
            ->where('radiology_report.radiology_bill_id', $id)
            ->get('radiology_report');
        return $query->result_array();
    }

    public function testReportBatch($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('radiology_report', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Radiology Report id " . $data['id'];
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
            $this->db->insert('radiology_report', $data);

            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Radiology Report id " . $insert_id;
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

    public function getRadiologyReport($id)
    {
        $query = $this->db->select('radiology_report.*,radio.id as pid,radio.charge_id as cid,staff.name,staff.surname,charges.charge_category_id,charges.name,charges.standard_charge')
            ->join('radio', 'radiology_report.radiology_id = radio.id')
            ->join('charges', 'radio.charge_id = charges.id')
            ->join('staff', 'staff.id = radiology_report.consultant_doctor', "left")
            ->where("radiology_report.id", $id)
            ->get('radiology_report');
        return $query->row_array();
    }

    public function updateTestReport($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('radiology_report', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Radiology Report id " . $data['id'];
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
    }

    public function addparametervalue($parametervalue)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($parametervalue["id"])) {
            $this->db->where("id", $parametervalue["id"])->update('radiology_report_parameterdetails', $parametervalue);

            $message = UPDATE_RECORD_CONSTANT . " On Radiology Report Parameter Dtails id " . $parametervalue["id"];
            $action = "Update";
            $record_id = $parametervalue["id"];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('radiology_parameterdetails', $parametervalue);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Radiology Parameter Details id " . $insert_id;
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

    public function getTestReportBatch($radiology_id)
    {
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option     = false;
        $userdata           = $this->customlib->getUserData();
        $role_id            = $userdata['role_id'];
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $doctorid = $userdata['id'];
                $this->db->where("radiology_report.consultant_doctor", $doctorid);
            }
        }

        $this->db->select('radiology_report.*, radio.id as rid,radio.test_name, radio.short_name,staff.name,staff.surname,charges.id as cid,charges.charge_category_id,charges.standard_charge,patients.patient_name');
        $this->db->join('radio', 'radiology_report.radiology_id = radio.id', 'inner');
        $this->db->join('staff', 'staff.id = radiology_report.consultant_doctor', "left");
        $this->db->join('charges', 'charges.id = radio.charge_id');
        $this->db->join('patients', 'patients.id = radiology_report.patient_id');
        $this->db->where("patients.is_active", "yes");
        $this->db->order_by('radiology_report.id', 'desc');
        $query  = $this->db->get('radiology_report');
        $result = $query->result();
        foreach ($result as $key => $value) {
            $generated_by = $value->generated_by;
            $staff_query  = $this->db->select("staff.name,staff.surname")
                ->where("staff.id", $generated_by)
                ->get("staff");
            $staff_result                   = $staff_query->row_array();
            $result[$key]->generated_byname = $staff_result["name"] . $staff_result["surname"];
        }
        return $result;
    }

    public function getPatientRadiologyReportDetails($id)
    {
        $query = $this->db->select('radiology_report.*,radio.test_name,radio.short_name,radio.report_days,radio.id as pid,radio.charge_id as charge_id,radiology_report.radiology_bill_id,radiology_billing.doctor_name,radiology_billing.case_reference_id,radiology_billing.patient_id,charges.charge_category_id,charges.name as `charge_name`,charges.standard_charge,patients.patient_name as `patient_name`,patients.id as patient_unique_id,patients.age,patients.month,patients.day,patients.gender,patients.blood_group,patients.mobileno,patients.email,patients.address,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,collection_specialist_staff.id as `collection_specialist_staff_id`')
            ->join('radiology_billing', 'radiology_report.radiology_bill_id = radiology_billing.id')
            ->join('patients', 'radiology_report.patient_id = patients.id')
            ->join('radio', 'radiology_report.radiology_id = radio.id')
            ->join('staff as collection_specialist_staff', 'collection_specialist_staff.id = radiology_report.collection_specialist', "left")
            ->join('charges', 'radio.charge_id = charges.id')
            ->where('radiology_report.id', $id)
            ->get('radiology_report');

        if ($query->num_rows() > 0) {
            $result                          = $query->row();
            $result->{'radiology_parameter'} = $this->getPatientRadiologyReportParameterDetails($result->id);
            return $result;
        }
        return false;
    }

    public function getPatientRadiologyReportParameterDetails($radiology_report_id)
    {
        $sql    = "SELECT radiology_parameterdetails.*,radiology_parameter.range_from,radiology_parameter.range_to,radiology_report.radiology_result,radiology_parameter.parameter_name,radiology_parameter.description,radiology_parameter.reference_range,unit.unit_name,IFNULL(radiology_report_parameterdetails.id,0) as `radiology_report_parameterdetail_id`,radiology_report_parameterdetails.radiology_report_id,radiology_report_parameterdetails.radiology_parameterdetail_id,radiology_report_parameterdetails.radiology_report_value FROM `radiology_report` INNER join radiology_parameterdetails on radiology_parameterdetails.radiology_id=radiology_report.radiology_id INNER JOIN radiology_parameter on radiology_parameterdetails.radiology_parameter_id=radiology_parameter.id INNER JOIN unit on radiology_parameter.unit=unit.id LEFT join radiology_report_parameterdetails on radiology_report_parameterdetails.radiology_parameterdetail_id=radiology_parameterdetails.id and radiology_report_parameterdetails.radiology_report_id=radiology_report.id WHERE radiology_report.id =" . $radiology_report_id;
        $query  = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }

    public function getTestReportBatchRadio($patient_id)
    {
        $i = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('radiology', '', '', '', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'radiology_billing.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $this->db->select('radiology_billing.*,sum(transactions.amount)as paid_amount,patients.patient_name,patients.id as pid,staff.name,staff.surname,staff.employee_id' . $field_variable);
        $this->db->join('patients', 'patients.id = radiology_billing.patient_id', 'left');
        $this->db->join('staff', 'staff.id = radiology_billing.doctor_id', 'left');
        $this->db->join('transactions', 'transactions.radiology_billing_id = radiology_billing.id', 'left');
        $this->db->group_by('radiology_billing.id');
        $this->db->where('radiology_billing.patient_id', $patient_id);
        $this->db->order_by('radiology_billing.id', 'desc');
        $query = $this->db->get('radiology_billing');
        return $query->result();
    }

    public function deleteTestReport($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('radiology_report');
        $message   = DELETE_RECORD_CONSTANT . " On Radiology Report id " . $id;
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
            //return $return_value;
        }
    }

    public function deletetestbill($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id)->delete('radiology_billing');
        $this->db->where('radiology_bill_id', $id)->delete('radiology_report');
        $message   = DELETE_RECORD_CONSTANT . " On Radiology Report where Radiology Bill Id " . $id;
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
            //return $return_value;
        }
    }

    public function getChargeCategory()
    {
        $query = $this->db->select('charge_categories.*')
            ->get('charge_categories');
        return $query->result_array();
    }

    public function getparameterBypathology($id)
    {
        $query = $this->db->select('radiology_parameterdetails.parameter_id')
            ->where('radiology_id', $id)
            ->get('radiology_parameterdetails');
        return $query->result_array();
    }

    public function addParameterforPatient($radiology_report_id, $approved_by, $approve_date, $insert_parameter_array, $update_parameter_array, $deleted_parameter_array, $test_result = null)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //======================Code Start==============================

        $this->db->where('id', $radiology_report_id);
        $this->db->update('radiology_report', array('parameter_update' => $approve_date, 'approved_by' => $approved_by, 'radiology_result' => $test_result));

        if (!empty($deleted_parameter_array)) {
            $this->db->where_not_in('id', $deleted_parameter_array);
            $this->db->where('radiology_report_id', $radiology_report_id);
            $this->db->delete('radiology_report_parameterdetails');

            $message = DELETE_RECORD_CONSTANT . " On Radiology Report Parameter Details where radiology report id " . $radiology_report_id;
            $action = "Delete";
            $record_id = $radiology_report_id;
            $this->log($message, $record_id, $action);
        }

        if (!empty($insert_parameter_array)) {
            $this->db->insert_batch('radiology_report_parameterdetails', $insert_parameter_array);
        }

        if (!empty($update_parameter_array)) {
            $this->db->update_batch('radiology_report_parameterdetails', $update_parameter_array, 'id');
        }

        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    public function test_uniqe($test_name, $short_name, $id)
    {
        if ($id != '') {
            $this->db->where_not_in('id', $id);
        }
        $query = $this->db->select('test_name')
            ->where("test_name", $test_name)
            ->where("short_name", $short_name)
            ->get('radio');

        return $query->num_rows();
    }

    public function validate_paymentamount()
    {
        $payment_amount = $this->input->post('amount');
        $net_amount = $this->input->post('net_amount');
        if ($payment_amount > $net_amount) {
            $this->form_validation->set_message('check_exists', 'Amount should not be greater than balance ' . $net_amount);
            return false;
        } else {
            return true;
        }
    }

    public function printtestparameterdetail($id)
    {
        $query = $this->db->select('radiology_report.*,radiology_report.id as test_id,radio.test_name,radio.short_name,radio.report_days,radio.id as pid,radio.charge_id as charge_id,radiology_report.radiology_bill_id,radiology_billing.doctor_name,radiology_billing.case_reference_id,radiology_billing.patient_id,charges.charge_category_id,charges.name as `charge_name`,charges.standard_charge,patients.patient_name as `patient_name`,patients.id as patient_unique_id,patients.age,patients.month,patients.day,patients.gender,patients.blood_group,patients.mobileno,patients.email,patients.address,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,collection_specialist_staff.id as `collection_specialist_staff_id`')
            ->join('radiology_billing', 'radiology_report.radiology_bill_id = radiology_billing.id')
            ->join('patients', 'radiology_report.patient_id = patients.id')
            ->join('radio', 'radiology_report.radiology_id = radio.id')
            ->join('staff as collection_specialist_staff', 'collection_specialist_staff.id = radiology_report.collection_specialist', "left")
            ->join('charges', 'radio.charge_id = charges.id')
            ->where('radiology_billing.id', $id)
            ->get('radiology_report');

        if ($query->num_rows() > 0) {
            $result    = $query->result_array();
            foreach ($result as $row) {
                $test_result[$row['id']] = $row;
                $test_result[$row['id']]['radiology_parameter'] = $this->getPatientRadiologyReportParameterDetails($row['test_id']);
            }
            return $test_result;
        }
        return false;
    }
}
