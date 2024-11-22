<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bloodissue_model extends MY_Model
{
    public function add($data, $transaction)
    {
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('blood_issue', $data);
            $record_id = $data['id'];
        } else {
            $this->db->insert('blood_issue', $data);
            $record_id = $this->db->insert_id();
        }

        $this->db->update("blood_donor_cycle", array("available" => 0), array("id" => $data['blood_donor_cycle_id']));
        //======================Code End==============================
        if (isset($transaction) && !empty($transaction)) {
            $transaction['blood_issue_id'] = $record_id;
            $this->transaction_model->add($transaction);
        }
        return $record_id;
    }


    public function bloodBankChargeById($id, $patient_id = null)
    {
        $sql    = "SELECT * from (SELECT `charges`.*, `organisations_charges`.`id` as `org_charge_id`, `organisations_charges`.`org_id`, `organisations_charges`.`org_charge`, IFNULL(tax_category.percentage, 0) as `percentage`,IFNULL(patients.organisation_id,0) as temp_col,patients.insurance_validity  FROM `charges` JOIN `organisations_charges` ON `organisations_charges`.`charge_id` = `charges`.`id` LEFT join patients on `patients`.`organisation_id` = `organisations_charges`.`org_id` and patients.id= " . $this->db->escape($patient_id) . " LEFT JOIN `tax_category` ON `tax_category`.`id` = `charges`.`tax_category_id` WHERE `charges`.`id` =  ".$this->db->escape($id).") d where d.temp_col = (SELECT CASE WHEN COUNT(*) = 1 THEN IFNULL(patients.organisation_id,0) ELSE COUNT(*) END AS 'Updated City' FROM `patients` where patients.id=" . $this->db->escape($patient_id) . ")";       
        $query  = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return $result;
        }
        return false;
        
    }

    public function searchFullText()
    {
        $this->db->select('blood_issue.*,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor.blood_group');
        $this->db->join('patients', 'patients.id = blood_issue.patient_id');
        $this->db->join('blood_donor', 'blood_issue.donor_name = blood_donor.id');
        $this->db->order_by('blood_issue.id', 'desc');
        $query = $this->db->get('blood_issue');
        return $query->result_array();
    }

    public function getpatientBloodYearCounter($patient_id, $year)
    {
        $sql = "SELECT count(*) as `total_visits`,Year(date_of_issue) as `year` FROM `blood_issue` WHERE YEAR(date_of_issue) >= " . $this->db->escape($year) . " AND patient_id=" . $this->db->escape($patient_id) . " GROUP BY  YEAR(date_of_issue)";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function totalPatientBloodIssue($patient_id)
    {
        $query = $this->db->select('count(blood_issue.patient_id) as total')
            ->where('patient_id', $patient_id)
            ->get('blood_issue');
        return $query->row_array();
    }

    public function getAllbloodissueRecord()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('blood_issue', 1);
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) { 
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'blood_issue.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $this->datatables
            ->select('blood_issue.*,IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.blood_issue_id= blood_issue.id ),0) as `paid_amount`,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit' . $field_variable)
            ->join('patients', 'patients.id = blood_issue.patient_id')
            ->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id')
            ->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id')
            ->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id')			
            ->searchable('blood_issue.id,blood_issue.case_reference_id,blood_issue.date_of_issue,patients.patient_name, blood_bank_products.name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_issue.amount,net_amount' . $custom_field_column)
            ->orderable('blood_issue.id,blood_issue.date_of_issue,patients.patient_name,blood_bank_products.name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no' . $custom_field_column . ',net_amount,paid_amount')		
            ->sort('blood_issue.id', 'desc')
            ->from('blood_issue');
        return $this->datatables->generate('json');
    }

    public function getpatientbloodissueRecord($patient_id)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('blood_issue', 1);
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'blood_issue.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $this->datatables
            ->select('blood_issue.*,IFNULL(sum(transactions.amount),0) as paid_amount,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit' . $field_variable)
            ->join('patients', 'patients.id = blood_issue.patient_id')
            ->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id')
            ->join('transactions', 'transactions.blood_issue_id = blood_issue.id', 'left')
            ->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id')
            ->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id')
            ->searchable('blood_issue.id,blood_issue.date_of_issue,patients.patient_name, blood_bank_products.name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_issue.amount,net_amount' . $custom_field_column)
            ->orderable('blood_issue.id,blood_issue.date_of_issue,patients.patient_name,blood_bank_products.name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no' . $custom_field_column . ',net_amount,paid_amount')
            ->group_by('transactions.blood_issue_id')
            ->sort('blood_issue.id', 'desc')
            ->from('blood_issue')->where('patients.id', $patient_id);
        return $this->datatables->generate('json');
    }

    public function getAllcomponentissueRecord($start_date = null, $end_date = null, $staff_id = null, $blood_group = null, $blood_component = null, $amount_collected_by = null, $component_collect_by = null)
    {
        $custom_fields             = $this->customfield_model->get_custom_fields('component_issue');
        $custom_field_column_array = array();
        $field_var_array           = array();
        $custom_join               = null;
        $transaction_query_string  = "";
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $custom_join .= ('LEFT JOIN custom_field_values as ' . $tb_counter . ' ON blood_issue.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id . " ");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);

        $query_string = "";
        if ($start_date != '' && $end_date != '') {
            $query_string .= " and  DATE(blood_issue.date_of_issue) >=" . $this->db->escape($start_date) . " and DATE(blood_issue.date_of_issue) <=" . $this->db->escape($end_date);
        }
		
        if ($staff_id != '') {
            $query_string .= " and blood_issue.generated_by=" . $this->db->escape($staff_id);
        }
		
        if ($blood_group != '') {
            $query_string .= " and blood_group.id=" . $this->db->escape($blood_group);
        }
        if ($blood_component != '') {
            $query_string .= " and component.id=" . $this->db->escape($blood_component);
        }

        if ($amount_collected_by != '') {
            $transaction_query_string .= " and transactions.received_by=" . $this->db->escape($amount_collected_by);
        }
		
        if ($component_collect_by != '') {
            $query_string .= " and blood_issue.generated_by=" . $this->db->escape($component_collect_by);
        }

        $this->datatables
            ->query('select blood_issue.*,staff.name, staff.surname,staff.employee_id,IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.blood_issue_id= blood_issue.id and 1=1 ' . $transaction_query_string . ' ),0) as `paid_amount`,  blood_group.name as blood_group_name,component.name as component_name,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit ' . $field_variable . ' from blood_issue inner join blood_donor_cycle on blood_donor_cycle.id=blood_issue.blood_donor_cycle_id join blood_donor_cycle as bcd on blood_donor_cycle.blood_donor_cycle_id=bcd.id join blood_donor on blood_donor.id=bcd.blood_donor_id join blood_bank_products as component on component.id=blood_donor_cycle.blood_bank_product_id join blood_bank_products as blood_group on blood_group.id=blood_donor.blood_bank_product_id  join patients on patients.id = blood_issue.patient_id left join staff on staff.id = blood_issue.generated_by ' . $custom_join . ' where 0=0 ' . $query_string)
            ->orderable('blood_issue.id,case_reference_id,blood_issue.date_of_issue,patients.patient_name,blood_group.name,component.name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_issue.generated_by' . $custom_field_column . ',net_amount,paid_amount')
            ->searchable('blood_issue.id,case_reference_id,blood_issue.date_of_issue,patients.patient_name,blood_group.name,component.name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no' . $custom_field_column . ',net_amount')
            ->sort('blood_issue.id', 'desc')
            ->query_where_enable(true);
        return $this->datatables->generate('json');
    }

    public function getAllcomponents()
    {
        $this->datatables
            ->select('blood_donor_cycle.*,blood_bank_products.name,bbp.name as components_blood_group,cu.unit as unit_name')
            ->join('blood_bank_products', 'blood_bank_products.id = blood_donor_cycle.blood_bank_product_id and is_blood_group=2')
            ->join('blood_donor_cycle bdc', 'blood_donor_cycle.blood_donor_cycle_id = bdc.id', "left")
            ->join('blood_donor', 'blood_donor.id = bdc.blood_donor_id', 'left')
            ->join('blood_bank_products bbp', 'bbp.id = blood_donor.blood_bank_product_id', "left")
            ->join('charge_units cu', 'cu.id = blood_donor_cycle.unit', "left")
            ->searchable('blood_bank_products.name,bbp.name,blood_donor_cycle.bag_no,blood_donor_cycle.lot,blood_donor_cycle.institution,blood_donor_cycle.quantity')
            ->orderable('blood_bank_products.name,bbp.name,blood_donor_cycle.bag_no,blood_donor_cycle.lot,blood_donor_cycle.institution,blood_donor_cycle.quantity')
            ->sort('blood_donor_cycle.id', 'desc')
            ->from('blood_donor_cycle');
        return $this->datatables->generate('json');
    }

    public function getbloodissueRecord($case_id)
    {
        $this->datatables
            ->select_sum('transactions.amount', 'paid_amount')
            ->select('blood_issue.*,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no')
            ->join('patients', 'patients.id = blood_issue.patient_id')
            ->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id')
            ->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id')
            ->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id')
            ->join('transactions', 'transactions.blood_issue_id = blood_issue.id', 'LEFT')
            ->searchable('blood_issue.id,blood_issue.date_of_issue,patients.patient_name, blood_bank_products.name as blood_group,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no')
            ->orderable('blood_issue.id,blood_issue.date_of_issue,patients.patient_name, blood_bank_products.name as blood_group,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no')
            ->where('blood_issue.case_reference_id', $case_id)
            ->sort('blood_issue.id', 'desc')
            ->group_by('transactions.blood_issue_id')
            ->from('blood_issue');
        return $this->datatables->generate('json');
    }

    public function getbloodissueByCaseId($case_id)
    {
        $query = $this->db->select('blood_issue.*,patients.patient_name,patients.id as patient_id')
            ->join('patients', 'patients.id = blood_issue.patient_id', 'left')
            ->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id')
            ->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id')
            ->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id')
            ->where('blood_issue.case_reference_id', $case_id)
            ->get('blood_issue');
        return $query->result();
    }

    public function getDetail($id)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('blood_issue');
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->db->join('custom_field_values as ' . $tb_counter, 'blood_issue.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);   

        $this->db->select('blood_issue.*,organisation.organisation_name,blood_issue.insurance_id,blood_issue.insurance_validity,IFNULL((SELECT SUM(amount) FROM transactions WHERE blood_issue_id=blood_issue.id),0) as total_deposit, blood_bank_products.id as blood_group_id,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit,blood_donor_cycle.quantity,blood_donor_cycle.donate_date,blood_donor_cycle.lot,charge_type_master.id as charge_type_id,charge_type_master.charge_type,charge_categories.name as charge_category_name,charges.charge_category_id,charge_units.unit as unit_name' . $field_variable);
        $this->db->join('patients', 'patients.id = blood_issue.patient_id', "left");
        $this->db->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id', "left");
        $this->db->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id', "left");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id', "left");
        $this->db->join('charges', 'blood_issue.charge_id = charges.id', 'inner');
        $this->db->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner');
        $this->db->join('charge_units', 'charge_units.id = blood_donor_cycle.unit', 'left');
        $this->db->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id', "left");        
        $this->db->join("organisation", 'organisation.id = blood_issue.organisation_id', "left");
        $this->db->where('blood_issue.id', $id);
        $query = $this->db->get('blood_issue');
        return $query->row_array();
    }

    public function getBloodIssuePatient($id)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('blood_issue');

        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->db->join('custom_field_values as ' . $tb_counter, 'blood_issue.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
		
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
		
        $this->db->select('blood_issue.*,IFNULL((SELECT SUM(amount) FROM transactions WHERE blood_issue_id=blood_issue.id),0) as total_deposit, blood_bank_products.id as blood_group_id,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit,blood_donor_cycle.quantity,blood_donor_cycle.donate_date,blood_donor_cycle.lot,charge_type_master.id as charge_type_id,charge_type_master.charge_type,charge_categories.name as charge_category_name,charges.charge_category_id,charge_units.unit as unit_name,organisation.organisation_name,patients.insurance_id,patients.insurance_validity' . $field_variable);
        $this->db->join('patients', 'patients.id = blood_issue.patient_id', "left");
        $this->db->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id', "left");
        $this->db->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id', "left");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id', "left");
        $this->db->join('charges', 'blood_issue.charge_id = charges.id', 'inner');
        $this->db->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'inner');
        $this->db->join('charge_units', 'charge_units.id = blood_donor_cycle.unit', 'left');
        $this->db->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id', "left");        
        $this->db->join("organisation", 'organisation.id = patients.organisation_id', "left");
        $this->db->where('blood_issue.id', $id);
        $query = $this->db->get('blood_issue');
        return $query->row_array();
    }

    public function getcomponentDetail($id)
    {
        $custom_fields             = $this->customfield_model->get_custom_fields('component_issue');
        $custom_field_column_array = array();
        $field_var_array           = array();
        $custom_join               = null;
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $custom_join .= ('LEFT JOIN custom_field_values as ' . $tb_counter . ' ON blood_issue.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id . " ");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $sql = "select blood_issue.*,organisation.organisation_name,blood_issue.insurance_id,blood_issue.insurance_validity,transactions.id as transaction_id, charge_categories.name as charge_categorie_name,IFNULL( sum(transactions.amount),0) as paid_amount,blood_group.name as blood_group_name,blood_group.id as blood_group_id,component.name as component_name,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit,blood_donor_cycle.donate_date " . $field_variable . " from blood_issue inner join blood_donor_cycle on blood_donor_cycle.id=blood_issue.blood_donor_cycle_id join blood_donor_cycle as bcd on blood_donor_cycle.blood_donor_cycle_id=bcd.id and bcd.blood_donor_cycle_id=0 join blood_donor on blood_donor.id=bcd.blood_donor_id join blood_bank_products as component on component.id=blood_donor_cycle.blood_bank_product_id join blood_bank_products as blood_group on blood_group.id=blood_donor.blood_bank_product_id join patients on patients.id = blood_issue.patient_id left join transactions on transactions.blood_issue_id = blood_issue.id left join charges on charges.id=blood_issue.charge_id left join charge_categories on charge_categories.id=charges.charge_category_id left join organisation on organisation.id=blood_issue.organisation_id " . $custom_join . " where blood_issue.id=" . $this->db->escape($id) . " ";

        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function getcomponenteditDetail($id)
    {
        $sql   = "select blood_issue.*,sum(transactions.amount) as paid_amount,blood_group.name as blood_group_name,component.name as component_name,component.id as component_id,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit,`blood_donor_cycle`.`donate_date`,charge_categories.charge_type_id,charges.charge_category_id,blood_group.id as blood_group_id from blood_issue inner join blood_donor_cycle on blood_donor_cycle.id=blood_issue.blood_donor_cycle_id join blood_donor_cycle as bcd on blood_donor_cycle.blood_donor_cycle_id=bcd.id join blood_donor on blood_donor.id=bcd.blood_donor_id join blood_bank_products as component on component.id=blood_donor_cycle.blood_bank_product_id join blood_bank_products as blood_group on blood_group.id=blood_donor.blood_bank_product_id join patients on patients.id = blood_issue.patient_id left join transactions on transactions.blood_issue_id = blood_issue.id join charges on charges.id=blood_issue.charge_id join charge_categories on charge_categories.id=charges.charge_category_id where blood_issue.id=" . $this->db->escape($id) . " group by transactions.blood_issue_id";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function getBillDetailsBloodbank($id)
    {
        $query = $this->db->select('blood_issue.*,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no')
            ->join('patients', 'patients.id = blood_issue.patient_id')
            ->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id')
            ->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id')
            ->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id')
            ->where('blood_issue.id', $id)
            ->get('blood_issue');
        return $query->row_array();
    }
	
    public function getMaxId()
    {
        $query  = $this->db->select('max(id) as bill_no')->get("blood_issue");
        $result = $query->row_array();
        return $result["bill_no"];
    }
	
    public function getAllBillDetails($id)
    {
        $query = $this->db->select('blood_issue.*')
            ->where('blood_issue.id', $id)
            ->get('blood_issue');
        return $query->result_array();
    }
	
    public function update($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('blood_issue', $data);
            $message   = UPDATE_RECORD_CONSTANT . " For Blood Issue id " . $data['id'];
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
        $this->db->delete('blood_issue');
		
		$this->db->delete("transactions", array("blood_issue_id" => $id)); 

        $message   = DELETE_RECORD_CONSTANT . " Where Blood Issue id " . $id;
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
	
    public function getBloodIssue($id = null)
    {
        $query = $this->db->select('blood_issue.*,staff.name,staff.surname')
            ->join('staff', 'staff.id = blood_issue.generated_by')
            ->get('blood_issue');
        return $query->result_array();
    }

    public function getcomponentissuerecordById($patient_id, $start_date = null, $end_date = null, $staff_id = null)
    {
        $custom_fields             = $this->customfield_model->get_custom_fields('component_issue', '', '','', 1);
        $custom_field_column_array = array();
        $field_var_array           = array();
        $custom_join               = null;
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $custom_join .= ('LEFT JOIN custom_field_values as ' . $tb_counter . ' ON blood_issue.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id . " ");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);

        $query_string = "";
        if ($start_date != '' && $end_date != '') {
            $query_string .= " and  blood_issue.date_of_issue >=" . $this->db->escape($start_date) . " and blood_issue.date_of_issue <=" . $this->db->escape($end_date);
        }
        if ($staff_id != '') {
            $query_string .= " and transactions.received_by=" . $this->db->escape($staff_id);
        }       

        $this->datatables
            ->query('select blood_issue.*,staff.name, staff.surname,staff.employee_id,IFNULL( (SELECT sum(transactions.amount) from transactions WHERE transactions.blood_issue_id= blood_issue.id and 1=1 and blood_issue.patient_id = ' . $patient_id . '  ),0) as `paid_amount`,  blood_group.name as blood_group_name,component.name as component_name,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit ' . $field_variable . ' from blood_issue inner join blood_donor_cycle on blood_donor_cycle.id=blood_issue.blood_donor_cycle_id             
            join blood_donor_cycle as bcd on blood_donor_cycle.blood_donor_cycle_id=bcd.id 
            join blood_donor on blood_donor.id=bcd.blood_donor_id 
            join blood_bank_products as component on component.id=blood_donor_cycle.blood_bank_product_id 
            join blood_bank_products as blood_group on blood_group.id=blood_donor.blood_bank_product_id  
            join patients on patients.id = blood_issue.patient_id left 
            join staff on staff.id = blood_issue.generated_by ' . $custom_join . ' where blood_issue.patient_id = ' . $patient_id . ' and  0=0 ' . $query_string)            
            ->orderable('blood_issue.id,blood_issue.date_of_issue,patients.patient_name,blood_group.name,component.name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_issue.generated_by' . $custom_field_column . ',net_amount,paid_amount')
            ->searchable('blood_issue.id,blood_issue.date_of_issue,patients.patient_name,blood_group.name,component.name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no' . $custom_field_column . ',net_amount')
            ->sort('blood_issue.id', 'desc')
            ->query_where_enable(true);
        return $this->datatables->generate('json');
       
    }
}
