<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pathology_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pathology_category_model');
    }

    public function add($data, $insert_parameter_array, $update_parameter_array, $deleted_parameter_array)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //======================Code Start==============================
        if (isset($data['id']) && $data['id'] > 0) {
            $this->db->where('id', $data['id']);
            $this->db->update('pathology', $data);
            $insert_id = $data['id'];

        } else {
            $this->db->insert('pathology', $data);
            $insert_id = $this->db->insert_id();
        }

        if (!empty($insert_parameter_array)) {
            foreach ($insert_parameter_array as $params_key => $params_value) {
                $insert_parameter_array[$params_key]['pathology_id'] = $insert_id;
            }
            $this->db->insert_batch('pathology_parameterdetails', $insert_parameter_array);
        }
		
        if (!empty($update_parameter_array)) {
            $this->db->update_batch('pathology_parameterdetails', $update_parameter_array, 'id');
        }
		
        if (!empty($deleted_parameter_array)) {
            $this->db->where_in('id', $deleted_parameter_array);
            $this->db->delete('pathology_parameterdetails');
        }

        $message   = INSERT_RECORD_CONSTANT . " On pathology Test id " . $insert_id;
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
            return $insert_id;
        }
    }

    public function addparameter($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('pathology_parameterdetails', $data);
        } else {
            $this->db->insert_batch('pathology_parameterdetails', $data);
            return $this->db->insert_id();
        }
    }

    public function getpatientPathologyYearCounter($patient_id,$year)
    {
		$sql= "SELECT count(*) as `total_visits`,Year(date) as `year` FROM `pathology_billing` WHERE YEAR(date) >= ".$this->db->escape($year)." AND patient_id=".$this->db->escape($patient_id)." GROUP BY  YEAR(date)";
		$query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getBillNo()
    {
        $query = $this->db->select("max(id) as id")->get('pathology_billing');
        return $query->row_array();
    }

    public function addBill($data, $addReports, $updateReports, $deleteReports, $pathology_billing_id, $transcation_data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //======================Code Start==============================

        if (isset($data['id']) && $data['id'] > 0) {
            $this->db->where('id', $data['id']);
            $this->db->update('pathology_billing', $data);
            $inserted = $data['id'];
        } else {
            $this->db->insert("pathology_billing", $data);
            $inserted = $this->db->insert_id();
        }

        if (!empty($addReports)) {
            foreach ($addReports as $report_key => $report_value) {
                $addReports[$report_key]['pathology_bill_id'] = $inserted;
            }
            $this->db->insert_batch('pathology_report', $addReports);
        }
		
        if (!empty($updateReports)) {
            $this->db->update_batch('pathology_report', $updateReports, 'id');
        }

        if (!empty($deleteReports) && $pathology_billing_id > 0) {
            $this->db->where_in('id', $deleteReports);
            $this->db->where('pathology_bill_id', $pathology_billing_id);
            $this->db->delete('pathology_report');
        }
  
        if (isset($transcation_data) && !empty($transcation_data)) {
            $transcation_data['pathology_billing_id'] = $inserted;
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
            return $inserted;
        }

    }
   
    public function delete_parameter($delete_arr)
    {
        foreach ($delete_arr as $key => $value) {
            $id = $value["id"];
            $this->db->where("id", $value["id"])->delete("pathology_parameterdetails");
        }
    }

    public function updateparameter($condition)
    {
        $SQL = "INSERT INTO pathology_parameterdetails
                    (parameter_id, id)
                    VALUES
                    " . $condition . "
                    ON DUPLICATE KEY UPDATE
                    parameter_id=VALUES(parameter_id)";
        $query = $this->db->query($SQL);
    }

    public function getparameter($id)
    {
        $this->db->select('pathology_parameterdetails.*');
        $this->db->where('pathology_parameterdetails.id', $id);
        $query = $this->db->get('pathology_parameterdetails');
        return $query->row_array();
    }
	
    public function totalPatientPathology($patient_id)
    {
        $query = $this->db->select('count(pathology_billing.patient_id) as total')
            ->where('patient_id', $patient_id)
            ->get('pathology_billing');
        return $query->row_array();
    }
	
    public function getAllpathologyRecord()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('pathology', 1);
        $custom_field_column_array = array();

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'pathology_report.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable      = implode(',', $field_var_array);
        $custom_field_column = implode(',', $custom_field_column_array);
        $this->datatables
            ->select('pathology_report.*, pathology.id as pathologyid,pathology.test_name, pathology.short_name,staff.name,staff.surname,charges.id as cid,charges.charge_category_id,charges.standard_charge,patients.patient_name,' . $field_variable)
            ->join('pathology', 'pathology_report.pathology_id = pathology.id', 'inner')
            ->join('staff', 'staff.id = pathology_report.consultant_doctor', "left")
            ->join('charges', 'charges.id = pathology.charge_id')
            ->join('patients', 'patients.id = pathology_report.patient_id')
            ->searchable('pathology_report.bill_no,pathology_report.reporting_date,patients.patient_name,pathology.test_name,pathology.short_name,staff.name,' . $custom_field_column)
            ->orderable('pathology_report.bill_no,pathology_report.reporting_date,pathology_report.patient_id,pathology.test_name,pathology.short_name,staff.name,pathology_report.description,' . $custom_field_column)
            ->sort('pathology_report.id', 'desc')
            ->from('pathology_report');
        return $this->datatables->generate('json');
    }
 
    public function getAllpathologybillRecord()
    {
        $custom_fields             = $this->customfield_model->get_custom_fields('pathology', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
         $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'pathology_billing.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->datatables
            ->select('pathology_billing.*,( SELECT IFNULL(SUM(transactions.amount),0) from transactions WHERE transactions.pathology_billing_id=pathology_billing.id ) as paid_amount,patients.patient_name,patients.id as pid,staff.name,staff.surname,staff.employee_id'.$field_variable)
            ->join('patients', 'patients.id = pathology_billing.patient_id', 'left')
            ->join('staff', 'staff.id = pathology_billing.doctor_id', 'left')
            ->searchable('pathology_billing.id,`pathology_billing`.`case_reference_id`,pathology_billing.date,patients.patient_name,pathology_billing.doctor_id,pathology_billing.total'.$custom_field_column.',pathology_billing.discount,pathology_billing.tax,pathology_billing.net_amount')
            ->orderable('pathology_billing.id,`pathology_billing`.`case_reference_id`,pathology_billing.date,patients.patient_name,pathology_billing.doctor_id,pathology_billing.total'.$custom_field_column.',pathology_billing.discount,pathology_billing.tax,pathology_billing.net_amount')         
            ->sort('pathology_billing.id', 'desc')
            ->from('pathology_billing');
        return $this->datatables->generate('json');
    }

    public function getpathologybillByCaseId($case_id)
    {
        $this->datatables
            ->select('pathology_billing.*,sum(transactions.amount) as paid_amount,patients.patient_name,patients.id as patient_unique_id,staff.name,staff.surname,staff.employee_id')
            ->join('patients', 'patients.id = pathology_billing.patient_id', 'left')
            ->join('staff', 'staff.id = pathology_billing.doctor_id', 'left')
            ->join('transactions', 'transactions.pathology_billing_id = pathology_billing.id', 'left')
            ->searchable('pathology_billing.id,pathology_billing.date,patients.patient_name')
            ->orderable('pathology_billing.id,pathology_billing.date,patients.patient_name,pathology_billing.doctor_id,pathology_billing.note,pathology_billing.total,pathology_billing.discount,pathology_billing.tax,pathology_billing.net_amount')
            ->group_by('transactions.pathology_billing_id')
            ->sort('pathology_billing.id', 'desc')
            ->where('pathology_billing.case_reference_id',$case_id)
            ->from('pathology_billing');
        return $this->datatables->generate('json');
    }

    public function getpathologyByCaseId($case_id)
    {   
        $query=$this->db->select('pathology_billing.*,IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.pathology_billing_id=pathology_billing.id),0) as `amount_paid`,patients.patient_name,patients.id as patient_id')
            ->join('patients', 'patients.id = pathology_billing.patient_id', 'left')
            ->where('pathology_billing.case_reference_id', $case_id)           
          ->get('pathology_billing');
        return $query->result();
    }

    public function getPathologyBillAmountByCaseId($case_id)
    {
		$sql= "SELECT sum(net_amount) as `net_amount`,IFNULL((SELECT sum(amount) FROM `transactions` WHERE pathology_billing_id in (SELECT pathology_billing.id FROM `pathology_billing` WHERE case_reference_id=".$this->db->escape($case_id).") and section = 'Pathology'),0) as `paid_amount` FROM `pathology_billing` WHERE case_reference_id=".$this->db->escape($case_id);
        $query = $this->db->query($sql);
        return $query->row();
	}   

    public function getAllpathologytest()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('pathologytest', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'pathology.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->datatables
            ->select('pathology.*,pathology_category.id as category_id,pathology_category.category_name,charges.standard_charge,tax_category.percentage' . $field_variable)
            ->join('pathology_category', 'pathology.pathology_category_id = pathology_category.id', 'left')
            ->join('charges', 'pathology.charge_id = charges.id', 'left')
            ->searchable('pathology.test_name,pathology.short_name,pathology.test_type,pathology_category.category_name,pathology.sub_category,pathology.method,pathology.report_days' . $custom_field_column)
           ->join('tax_category', 'tax_category.id = charges.tax_category_id', 'left')
            ->orderable('pathology.test_name,pathology.short_name,pathology.test_type,pathology_category.category_name,pathology.sub_category,pathology.method,pathology.report_days,charges.standard_charge' . $custom_field_column.',percentage,charges.standard_charge')
            ->sort('pathology.id', 'desc')
            ->from('pathology');
        return $this->datatables->generate('json');
    }
    
    public function getPathologyChargeByPatient($id,$patient_id=null)
    {      
        $sql    = "SELECT * from (SELECT `pathology`.*, `pathology_category`.`id` as `category_id`, `pathology_category`.`category_name` as `pathology_category_name`, `charges`.`name` as `charge_name`, `charges`.`charge_category_id`, `charges`.`standard_charge`, `charges`.`description`, `charge_categories`.`name` as `charge_category_name`, `tax_category`.`name` as `apply_tax`, `tax_category`.`percentage` as `tax`,organisations_charges.org_id,organisations_charges.org_charge,IFNULL(patients.organisation_id,0) as temp_col,patients.insurance_validity 
         FROM `pathology` 
        LEFT JOIN `pathology_category` ON `pathology`.`pathology_category_id` = `pathology_category`.`id` 
        LEFT JOIN `charges` ON `pathology`.`charge_id` = `charges`.`id` 
        LEFT JOIN `tax_category` ON `charges`.`tax_category_id` = `tax_category`.`id` 
        LEFT JOIN `charge_categories` ON `charge_categories`.`id` = `charges`.`charge_category_id` 
        JOIN `organisations_charges` ON `organisations_charges`.`charge_id` = `charges`.`id` 
        LEFT join patients on `patients`.`organisation_id` = `organisations_charges`.`org_id` and patients.id= ".$this->db->escape($patient_id)."
        JOIN `charge_type_master` ON `charge_categories`.`charge_type_id` = `charge_type_master`.`id` 
        WHERE  `pathology`.`id` = ".$this->db->escape($id).") d where d.temp_col = (SELECT CASE WHEN COUNT(*) = 1 THEN IFNULL(patients.organisation_id,0) ELSE COUNT(*) END AS 'Updated City' FROM `patients` where patients.id=".$this->db->escape($patient_id).")";
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
        $custom_fields             = $this->customfield_model->get_custom_fields('pathology', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'pathology.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable      = implode(',', $field_var_array);
        $custom_field_column = implode(',', $custom_field_column_array);
        $this->db->select('pathology.*,pathology_category.id as category_id,pathology_category.category_name as `pathology_category_name`, charges.id as charge_id, charges.name as `charge_name`, charges.charge_category_id, charges.standard_charge, charges.description,charge_categories.name as `charge_category_name`,tax_category.name as apply_tax,tax_category.percentage as tax,' . $field_variable);
        $this->db->join('pathology_category', 'pathology.pathology_category_id = pathology_category.id', 'left');
        $this->db->join('charges', 'pathology.charge_id = charges.id', 'left');
        $this->db->join('tax_category', 'charges.tax_category_id = tax_category.id', 'left');
        $this->db->join('charge_categories', 'charge_categories.id = charges.charge_category_id','left');
        $this->db->join("charge_type_master", 'charge_categories.charge_type_id = charge_type_master.id');
        $this->db->where('pathology.id', $id);
        $this->db->order_by('pathology.id', 'desc');
        $query = $this->db->get('pathology');
        if ($query->num_rows() > 0) {
            $result                          = $query->row();
            $result->{'pathology_parameter'} = $this->Pathology_category_model->getPathlogyParamsById($result->id);
            return $result;
        }

        return false;
    }

    public function getpathotestDetails()
    {
        $this->db->select('pathology.*,pathology_category.id as category_id,pathology_category.category_name,charges.id as charge_id, charges.name, charges.charge_category_id, charges.standard_charge, charges.description,tax_category.percentage as tax');
        $this->db->join('pathology_category', 'pathology.pathology_category_id = pathology_category.id', 'left');
        $this->db->join('charges', 'pathology.charge_id = charges.id', 'left');
        $this->db->join('tax_category', 'charges.tax_category_id = tax_category.id', 'left');
        $this->db->order_by('pathology.id', 'desc');
        $query = $this->db->get('pathology');
        return $query->result_array();
    }

    public function gettestpathodetails($id)
    {
        $this->db->select('pathology.*,pathology_category.id as category_id,pathology_category.category_name,charges.id as charge_id, charges.name, charges.charge_category_id, charges.standard_charge, charges.description');
        $this->db->join('pathology_category', 'pathology.pathology_category_id = pathology_category.id', 'left');
        $this->db->join('charges', 'pathology.charge_id = charges.id', 'left');
        $this->db->where('pathology.id', $id);
        $this->db->order_by('pathology.id', 'desc');
        $query = $this->db->get('pathology');
        return $query->row_array();
    }

    public function update($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('pathology', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Pathology Test id " . $data['id'];
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

    public function getPathologyBillByID($id, $patient_panel = null)
    { 
		$i = 1;
        if($patient_panel == 'patient'){
            $custom_fields = $this->customfield_model->get_custom_fields('pathology','','','', 1);
        }else{
            $custom_fields = $this->customfield_model->get_custom_fields('pathology');
        }
        $custom_field_column_array= array();
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
              $this->db->join('custom_field_values as ' . $tb_counter, 'pathology_billing.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);

		$query = $this->db->select('pathology_billing.*,blood_bank_products.name as blood_group_name,IFNULL((SELECT SUM(amount) FROM transactions WHERE pathology_billing_id=pathology_billing.id),0) as total_deposit,patients.patient_name,patients.id as patient_unique_id,patients.dob,patients.age,patients.month,patients.day,patients.gender,patients.blood_group,patients.mobileno,patients.email,patients.address,staff.name,staff.surname,staff.employee_id,transactions.payment_mode,transactions.amount,transactions.cheque_no,transactions.cheque_date,transactions.note as `transaction_note`,staff_roles.role_id as staff_roles_id,org.organisation_name,pathology_billing.insurance_validity,pathology_billing.insurance_id'.$field_variable)
            ->join('patients', 'pathology_billing.patient_id = patients.id')
            ->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left')
            ->join('staff', 'staff.id = pathology_billing.generated_by')
            ->join('staff_roles', 'staff_roles.staff_id = staff.id')
            ->join('transactions', 'transactions.id = pathology_billing.transaction_id','left')
            ->join('organisation', 'organisation.id = patients.organisation_id','left')
            ->join('organisation as org', 'org.id = pathology_billing.organisation_id','left')
            ->where("pathology_billing.id", $id)
            ->get('pathology_billing');
        if ($query->num_rows() > 0) {
            $result                       = $query->row();
            $result->{'pathology_report'} = $this->getReportByBillId($result->id);
            return $result;
        }
        return false;
    }
 
    public function getReportByBillId($id)
    {
        $custom_fields             =$this->customfield_model->get_custom_fields('pathologytest');
        $custom_field_column_array = array();
        $field_var_array = array();
        $this->db->join('pathology', 'pathology_report.pathology_id = pathology.id');
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'pathology.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable      = implode(',', $field_var_array);

        $query = $this->db->select('pathology_report.*,pathology.test_name,pathology.short_name,pathology.report_days,pathology.id as pid,pathology.charge_id as cid,staff.name,staff.surname,charges.charge_category_id,charges.name,charges.standard_charge,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`,'.$field_variable)
            ->join('pathology_billing', 'pathology_report.pathology_bill_id = pathology_billing.id')            
            ->join('staff', 'staff.id = pathology_billing.doctor_id', "left")
            ->join('staff as collection_specialist_staff', 'collection_specialist_staff.id = pathology_report.collection_specialist', "left")
            ->join('staff as approved_by_staff', 'approved_by_staff.id = pathology_report.approved_by', "left")
            ->join('charges', 'pathology.charge_id = charges.id')
            ->where('pathology_report.pathology_bill_id', $id)
            ->get('pathology_report');
        return $query->result();
    } 

    public function getPatientPathologyReportDetails($id)
    {
        $query = $this->db->select('pathology_report.*,pathology.test_name,pathology.short_name,pathology.report_days,pathology.id as pid,pathology.charge_id as charge_id,pathology_billing.case_reference_id,pathology_billing.id as bill_no,pathology_billing.patient_id,pathology_billing.doctor_name,charges.charge_category_id,charges.name as `charge_name`,charges.standard_charge,patients.patient_name as `patient_name`,patients.id as patient_unique_id,patients.age,patients.dob,patients.month,patients.day,patients.gender,patients.blood_group,patients.mobileno,patients.email,patients.address,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,collection_specialist_staff.id as `collection_specialist_staff_id`,  approved_staff_list.name as `approved_by_staff_name`,approved_staff_list.surname as `approved_by_staff_surname`,approved_staff_list.employee_id as `approved_by_staff_employee_id`,approved_staff_list.id as `approved_by_staff_id`')
            ->join('pathology_billing', 'pathology_report.pathology_bill_id = pathology_billing.id')
            ->join('patients', 'pathology_report.patient_id = patients.id')
            ->join('pathology', 'pathology_report.pathology_id = pathology.id')
            ->join('staff as collection_specialist_staff', 'collection_specialist_staff.id = pathology_report.collection_specialist', "left")
            ->join('staff as approved_staff_list', 'approved_staff_list.id = pathology_report.approved_by', "left")
            ->join('charges', 'pathology.charge_id = charges.id')
            ->where('pathology_report.id', $id)
            ->get('pathology_report');

        if ($query->num_rows() > 0) {
            $result                          = $query->row();
            foreach($result as $row){                
                $result->{'pathology_parameter'} = $this->getPatientPathologyReportParameterDetails($result->id);
            }           
            return $result;
        }
        return false;
    }

    public function getPatientPathologyReportParameterDetails($pathology_report_id)
    {
        $sql    = "SELECT pathology_parameterdetails.*,pathology_report.pathology_result,pathology_parameter.parameter_name,pathology_parameter.description,pathology_parameter.reference_range,unit.unit_name,IFNULL(pathology_report_parameterdetails.id,0) as `pathology_report_parameterdetail_id`,pathology_report_parameterdetails.pathology_report_id,pathology_report_parameterdetails.pathology_parameterdetail_id,pathology_report_parameterdetails.pathology_report_value,pathology_parameter.range_from,pathology_parameter.range_to FROM `pathology_report` INNER join pathology_parameterdetails on pathology_parameterdetails.pathology_id=pathology_report.pathology_id INNER JOIN pathology_parameter on pathology_parameterdetails.pathology_parameter_id=pathology_parameter.id INNER JOIN unit on pathology_parameter.unit=unit.id LEFT join pathology_report_parameterdetails on pathology_report_parameterdetails.pathology_parameterdetail_id=pathology_parameterdetails.id and pathology_report_parameterdetails.pathology_report_id=pathology_report.id WHERE pathology_report.id =" . $pathology_report_id;
        $query  = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }

    public function getMaxId()
    {
        $query  = $this->db->select('max(id) as bill_no')->get("pathology_report");
        $result = $query->row_array();
        return $result["bill_no"];
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('pathology');
        $this->db->where("pathology_id", $id)->delete('pathology_parameterdetails');
        $message   = DELETE_RECORD_CONSTANT . " On  Pathology Test  id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id,'pathologytest'); 
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

    public function deletePathologyBill($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('pathology_billing');

		$this->db->delete("transactions", array("pathology_billing_id" => $id)); 
		
        $message   = DELETE_RECORD_CONSTANT . " On  Pathology Bill  id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id,'pathology'); 
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

    public function getPathology($id = null)
    {
        if (!empty($id)) {
            $this->db->where("pathology.id", $id);
        }
        $query = $this->db->select('pathology.*,charges.charge_category_id,charges.name,charges.standard_charge')->join('charges', 'pathology.charge_id = charges.id')->get('pathology');
        if (!empty($id)) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getpathologytest($id = null)
    {
        if (!empty($id)) {
            $this->db->where("pathology.id", $id);
        }
        $query = $this->db->select('pathology.*')->get('pathology');
        if (!empty($id)) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getPathologyReportByID($id)
    {
        $query = $this->db->select('pathology_report.*,pathology.id as pid,pathology.charge_id as cid,charges.charge_category_id,charges.name,charges.standard_charge,patients.patient_name')
            ->join('patients', 'pathology_report.patient_id = patients.id')
            ->join('pathology', 'pathology_report.pathology_id = pathology.id')
            ->join('charges', 'pathology.charge_id = charges.id')
            ->where("pathology_report.id", $id)
            ->get('pathology_report');
        return $query->row_array();
    }

    public function getPathologyparameterReport($id)
    {
        $query = $this->db->select('pathology_report.*,pathology.id as pid,pathology.charge_id as cid,staff.name,staff.surname,charges.charge_category_id,charges.name,charges.standard_charge,patients.patient_name')
            ->join('patients', 'pathology_report.patient_id = patients.id')
            ->join('pathology', 'pathology_report.pathology_id = pathology.id')
            ->join('charges', 'pathology.charge_id = charges.id')
            ->join('staff', 'staff.id = pathology_report.consultant_doctor', "left")
            ->where("pathology_report.id", $id)
            ->get('pathology_report');
        return $query->row_array();
    }

    public function testReportBatch($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('pathology_report', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Pathology Test Report id " . $data['id'];
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
            $this->db->insert('pathology_report', $data);

            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Pathology Test Report id " . $insert_id;
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

    public function addparametervalue($parametervalue)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($parametervalue["id"])) {
            $this->db->where("id", $parametervalue["id"])->update('pathology_report_parameterdetails', $parametervalue);            
            $message = UPDATE_RECORD_CONSTANT . " On Pathology Report Parameter Details id " . $parametervalue["id"];
            $action = "Update";
            $record_id = $parametervalue["id"];
            $this->log($message, $record_id, $action);            
        } else {
            $this->db->insert('pathology_parameterdetails', $parametervalue);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Pathology Parameter Details id " . $insert_id;
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

    public function updateTestReport($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('pathology_report', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Pathology Report id " . $data['id'];
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

    public function getTestReportBatch($pathology_id)
    {
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option     = false;
        $userdata           = $this->customlib->getUserData();
        $role_id            = $userdata['role_id'];
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $doctorid = $userdata['id'];
                $this->db->where("pathology_report.consultant_doctor", $doctorid);
            }
		}

        $this->db->select('pathology_report.*, pathology.id as pid,pathology.test_name,pathology.short_name,staff.name,staff.surname,charges.id as cid,charges.charge_category_id,charges.standard_charge,patients.patient_name');
        $this->db->join('pathology', 'pathology_report.pathology_id = pathology.id', 'inner');
        $this->db->join('staff', 'staff.id = pathology_report.consultant_doctor', "left");
        $this->db->join('charges', 'charges.id = pathology.charge_id');
        $this->db->join('patients', 'patients.id = pathology_report.patient_id');
        $this->db->where("patients.is_active", "yes");
        $this->db->order_by('pathology_report.id', 'desc');
        $query  = $this->db->get('pathology_report');
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

    public function getTestReportBatchPatho($patient_id)
    {
        $i = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('pathology','','','', 1);
        $custom_field_column_array= array();
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'pathology_billing.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->db->select('pathology_billing.*,sum(transactions.amount) as paid_amount,patients.patient_name,patients.id as pid,staff.name,staff.surname,staff.employee_id'.$field_variable);
        $this->db->join('patients', 'patients.id = pathology_billing.patient_id', 'left');
        $this->db->join('staff', 'staff.id = pathology_billing.doctor_id', 'left');
        $this->db->join('transactions', 'transactions.pathology_billing_id = pathology_billing.id',"left");
        $this->db->group_by('pathology_billing.id');
        $this->db->where('pathology_billing.patient_id', $patient_id);
        $this->db->order_by('pathology_billing.id', 'desc');
        $query = $this->db->get('pathology_billing');
        return $query->result();
    }

    public function deleteTestReport($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('pathology_report');
        $message   = DELETE_RECORD_CONSTANT . " On  Pathology Report  id " . $id;
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

    public function pathologyReport()
    {
        $this->db->select('pathology_report.*, pathology.id, pathology.short_name,staff.name,staff.surname,charges.id as cid,charges.charge_category_id,charges.standard_charge');
        $this->db->join('pathology', 'pathology_report.pathology_id = pathology.id', 'inner');
        $this->db->join('staff', 'staff.id = pathology_report.consultant_doctor', "inner");
        $this->db->join('charges', 'charges.id = pathology.charge_id');
        $query = $this->db->get('pathology_report');
        return $query->result_array();
    }

    public function searchPathologyReport($date_from, $date_to)
    {
        $this->db->select('pathology_report.*, pathology.id, pathology.short_name,staff.name,staff.surname,charges.id as cid,charges.charge_category_id,charges.standard_charge');
        $this->db->join('pathology', 'pathology_report.pathology_id = pathology.id', 'inner');
        $this->db->join('staff', 'staff.id = pathology_report.consultant_doctor', "inner");
        $this->db->join('charges', 'charges.id = pathology.charge_id');
        $this->db->where('pathology_report.reporting_date >=', $date_from);
        $this->db->where('pathology_report.reporting_date <=', $date_to);
        $query = $this->db->get("pathology_report");
        return $query->result_array();
    }
    
    public function addParameterforPatient($pathology_report_id, $approved_by,$approve_date, $insert_parameter_array, $update_parameter_array, $deleted_parameter_array,$test_result=null)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //======================Code Start==============================

        $this->db->where('id', $pathology_report_id);
        $this->db->update('pathology_report', array('parameter_update' => $approve_date, 'approved_by' => $approved_by,'pathology_result'=>$test_result));

        if (!empty($deleted_parameter_array)) {
            $this->db->where_not_in('id', $deleted_parameter_array);
            $this->db->where('pathology_report_id', $pathology_report_id);
            $this->db->delete('pathology_report_parameterdetails');            
            $message = DELETE_RECORD_CONSTANT . " On Pathology Report Parameter Details id " . $deleted_parameter_array;
            $action = "Delete";
            $record_id = $deleted_parameter_array;
            $this->log($message, $record_id, $action);        
        }

        if (!empty($insert_parameter_array)) {
            $this->db->insert_batch('pathology_report_parameterdetails', $insert_parameter_array);
        }

        if (!empty($update_parameter_array)) {
            $this->db->update_batch('pathology_report_parameterdetails', $update_parameter_array, 'id');
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

    public function test_uniqe($test_name,$short_name,$id){
      if($id!=''){
        $this->db->where_not_in('id',$id);
      }
      $query = $this->db->select('test_name')
            ->where("test_name", $test_name)
             ->where("short_name", $short_name)
            ->get('pathology');
        return $query->num_rows();
    }

    public function validate_paymentamount()
    {
       $payment_amount = $this->input->post('amount') ;
        $net_amount = $this->input->post('net_amount') ;
        if($payment_amount > $net_amount ){

            $this->form_validation->set_message('check_exists', 'Amount should not be greater than balance '. $net_amount );
            return false;
        }else{
            return true;
        }        
    }

    public function gettestparameterdetails($id)
    {
        $query = $this->db->select('pathology_report.*,pathology.test_name,pathology.short_name,pathology.report_days,pathology.id as pid,pathology.charge_id as charge_id,pathology_billing.case_reference_id, pathology_report.id as test_id, pathology_billing.id as bill_no,pathology_billing.patient_id,pathology_billing.doctor_name,charges.charge_category_id,charges.name as `charge_name`,charges.standard_charge,patients.patient_name as `patient_name`,patients.id as patient_unique_id,patients.age,patients.dob,patients.month,patients.day,patients.gender,patients.blood_group,patients.mobileno,patients.email,patients.address,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,collection_specialist_staff.id as `collection_specialist_staff_id`')
           ->join('pathology_report', 'pathology_report.pathology_bill_id = pathology_billing.id')
            ->join('patients', 'pathology_report.patient_id = patients.id')
            ->join('pathology', 'pathology_report.pathology_id = pathology.id')
            ->join('staff as collection_specialist_staff', 'collection_specialist_staff.id = pathology_report.collection_specialist', "left")
            ->join('charges', 'pathology.charge_id = charges.id')
            ->where('pathology_billing.id', $id)
            ->get('pathology_billing');

        if ($query->num_rows() > 0) {
            $result                          = $query->result_array();
            foreach($result as $row){
                $test_result[$row['id']]=$row;
                $test_result[$row['id']]['pathology_parameter']= $this->getPatientPathologyReportParameterDetails($row['test_id']);                
            }          
            return $test_result;
        }
        return false;
    }     
}
