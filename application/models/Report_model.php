<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Report_model extends CI_Model
{

    public function getReport($table_name, $select, $join = array(),  $where = array(), $additional_where = array())
    {
        if (empty($additional_where)) {
            $additional_where = array(" 1 = 1");
        }

        if (!empty($join)) {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $additional_where);
        } else {
            $query = "select " . $select . " from " . $table_name . " where" . implode(" and ", $additional_where);
        }

        $res = $this->db->query($query);
        return $res->result_array();
    }

    public function searchReport($table_name, $select, $join,  $search_type, $search_table, $search_column, $additional_where = array(), $where = array())
    {
        if ($search_type == 'period') {
            $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                echo form_error();
            } else {
                $from_date = $this->input->post('date_from');
                $to_date   = $this->input->post('date_to');
                $date_from = date("Y-m-d", $this->customlib->datetostrtotime($from_date));
                $date_to   = date("Y-m-d 23:59:59.993", $this->customlib->datetostrtotime($to_date));
                $where     = array($search_table . "." . $search_column . " >=  '" . $date_from . "' ", $search_table . "." . $search_column . " <=  '" . $date_to . "'");
            }
        } else if ($search_type == 'today') {
            $today        = strtotime('today 00:00:00');
            $first_date   = date('Y-m-d ', $today);
            $search_today = 'date(' . $search_table . '.' . $search_column . ')';
            $where        = array($search_today . " = '" . $first_date . "'");
        } else if ($search_type == 'this_week') {
            $this_week_start = strtotime('-1 week monday 00:00:00');
            $this_week_end   = strtotime('sunday 23:59:59');
            $first_date      = date('Y-m-d H:i:s', $this_week_start);
            $last_date       = date('Y-m-d H:i:s', $this_week_end);
            $where           = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_week') {
            $last_week_start = strtotime('-2 week monday 00:00:00');
            $last_week_end   = strtotime('-1 week sunday 23:59:59');
            $first_date      = date('Y-m-d H:i:s', $last_week_start);
            $last_date       = date('Y-m-d H:i:s', $last_week_end);
            $where           = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'this_month') {
            $first_date = date('Y-m-01');
            $last_date  = date('Y-m-t 23:59:59.993');
            $where      = array($search_table . "." . $search_column . " >= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_month') {
            $month      = date("m", strtotime("-1 month"));
            $first_date = date('Y-' . $month . '-01');
            $last_date  = date('Y-' . $month . '-' . date('t', strtotime($first_date)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_3_month') {
            $month      = date("m", strtotime("-2 month"));
            $first_date = date('Y-' . $month . '-01');
            $firstday   = date('Y-' . 'm' . '-01');
            $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_6_month') {
            $month      = date("m", strtotime("-5 month"));
            $first_date = date('Y-' . $month . '-01');
            $firstday   = date('Y-' . 'm' . '-01');
            $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_12_month') {
            $first_date = date('Y-m' . '-01', strtotime("-11 month"));
            $firstday   = date('Y-' . 'm' . '-01');
            $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)) . ' 23:59:59.993');
            $where      = array($search_table . "." . $search_column . ">= '" . $first_date . "'", $search_table . "." . $search_column . "<= '" . $last_date . "'");
        } else if ($search_type == 'last_year') {
            $search_year = date('Y', strtotime("-1 year"));
            $where       = array("year(" . $search_table . "." . $search_column . ") = '" . $search_year . "'");
        } else if ($search_type == 'this_year') {
            $search_year = date('Y');
            $where       = array("year(" . $search_table . "." . $search_column . ") = '" . $search_year . "'");
        } else if ($search_type == 'all time') {
            $where = array();
        }
        if (empty($additional_where)) {
            $additional_where = array('1 = 1');
        }

        if (!empty($where)) {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where) . " and " . implode(" and ", $additional_where) . " order by " . $search_table . "." . $search_column;
        } else {
            $query = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode("  and ", $additional_where) . " order by " . $search_table . "." . $search_column;
        }

        $res = $this->db->query($query);
        return $res->result_array();
    }

    public function appointmentRecord($start_date, $end_date, $collect_staff = "", $shift = "", $appointment_priority = "", $appointment_type = "", $appointment_status = "")
    {

        $custom_fields             = $this->customfield_model->get_custom_fields('appointment', '', '', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        $custom_join = NULL;
        $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $custom_join .= ('LEFT JOIN custom_field_values as ' . $tb_counter . ' ON appointment.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id . " ");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $condition = "";
        $condition1 = "";

        if ($collect_staff != "") {
            $condition .= " and appointment.doctor =" . $collect_staff;
        }

        if ($appointment_priority != "") {
            $condition .= " and appointment.priority =" . $appointment_priority;
        }
        if ($appointment_type != "") {
            $condition .= " and appointment.source = '" . $appointment_type . "'";
        }
        if ($appointment_status != "") {
            $condition .= " and appointment.appointment_status = '" . $appointment_status . "'";
        }

        if ($shift != ""  ) {
            $doctor_shift_time_id  =    $this->onlineappointment_model->getDoctorShiftTime($collect_staff, $shift);		
			
			if($doctor_shift_time_id[0]['id']) {
			 
            $condition1 .= " and (";
            foreach ($doctor_shift_time_id as $key => $value) {				 
                if ($key != 0) {
                    $condition1 .= " or appointment.doctor_shift_time_id = " . $value['id'] . "";
                } else {
                    $condition1 .= " appointment.doctor_shift_time_id = " . $value['id'] . "";
                }				 
            }
            $condition1 .= ")"; 
			}
			 
        }

        $sql = "select appointment.*,patients.mobileno,patients.email,patients.gender,appointment_payment.paid_amount,appointment_payment.discount_percentage,patients.patient_name,patients.id as `patient_id`,staff.name,staff.surname,staff.employee_id " . $field_variable . " from appointment 
		join appointment_payment on appointment_payment.appointment_id = appointment.id
        JOIN patients on patients.id = appointment.patient_id		
        LEFT JOIN staff on staff.id = appointment.doctor " . $custom_join . " 
        where date_format(appointment.date,'%Y-%m-%d') >='" . $start_date . "'and date_format(appointment.date,'%Y-%m-%d') <= '" . $end_date . "'" . $condition . " " . $condition1;

        $this->datatables->query($sql)
            ->searchable('patients.patient_name,appointment_payment.paid_amount,appointment.patient_id,appointment.date,patients.mobileno,patients.gender,staff.name,appointment.appointment_status' . $custom_field_column)
            ->orderable('patients.patient_name,appointment.date,patients.mobileno,patients.gender,staff.name,appointment.source' . $custom_field_column . ', appointment_payment.paid_amount,appointment.appointment_status')
            // ->sort('date_format(appointment.date, "%m/%e/%Y")', 'desc')
            ->sort('date_format(appointment.date, "%Y-%m-%d")', 'desc')
            ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }

    public function referralRecord($payee, $patient_type, $patient, $date)
    {
        $search = "";

        if ($payee != '') {
            $search .= " and person.id=" . $payee;
        }

        if ($patient_type != '') {
            $search .= " and type.id=" . $patient_type;
        }

        if ($patient != '') {
            $search .= " and patients.id=" . $patient;
        }

        if ($date != '') {
            $search .= " and date_format(payment.date,'%Y-%m-%d ') ='" . $date . "' ";
        }

        $sql = "SELECT `payment`.`billing_id`, `payment`.`id`, `person`.`name`, `patients`.`patient_name`, `patients`.`id` as `patient_id`, `type`.`name` as `type`, `payment`.`bill_amount`, `payment`.`percentage`, `payment`.`amount`, `prefixes`.`prefix`, payment.date FROM `referral_payment` `payment` LEFT JOIN `referral_type` `type` ON `type`.`id`=`payment`.`referral_type` INNER JOIN `prefixes` ON `type`.`prefixes_type`=`prefixes`.`type` JOIN `referral_person` `person` ON `person`.`id`=`payment`.`referral_person_id` LEFT JOIN `patients` ON `patients`.`id`=`payment`.`patient_id` where 0=0 " . $search;
        $this->datatables->query($sql)

            ->searchable('person.name,patient_name,date,billing_id,percentage,bill_amount,amount')
            ->orderable('person.name,patient_name,date,billing_id,percentage,bill_amount,amount')
            ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }

    public function birthRecord($start_date, $end_date, $gender = null)
    {
        $custom_fields             = $this->customfield_model->get_custom_fields('birth_report', '', '', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        $custom_join = NULL;
        $i                         = 1;
        $condition = "";

        if (isset($gender) && $gender != "") {
            $condition = " and birth_report.gender = '" . $gender . "' ";
        }

        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $custom_join .= ('LEFT JOIN custom_field_values as ' . $tb_counter . ' ON birth_report.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id . " ");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);

        $sql = "select birth_report.*,patients.id as mother_id,patients.patient_name" . $field_variable . " from birth_report LEFT JOIN patients on patients.id = birth_report.patient_id " . $custom_join . " where 0=0 " . $condition . " and  date_format(birth_report.birth_date,'%Y-%m-%d') >='" . $start_date . "'and date_format(birth_report.birth_date,'%Y-%m-%d') <= '" . $end_date . "'";
        $this->datatables->query($sql)
            ->searchable('birth_report.id,birth_report.case_reference_id,birth_report.id,birth_report.child_name,birth_report.birth_date,birth_report.weight,patients.patient_name,birth_report.father_name ' . $custom_field_column)
            ->orderable('birth_report.id,birth_report.case_reference_id,birth_report.child_name,birth_report.gender,birth_report.birth_date,birth_report.weight,patients.patient_name,birth_report.father_name ' . $custom_field_column)
            ->sort('date_format(birth_report.birth_date, "%m/%e/%Y")', 'desc')
            ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }

    public function deathRecord($start_date, $end_date, $gender)
    {

        $custom_fields = $this->customfield_model->get_custom_fields('death_report', '', '', 1);
        $i = 1;
        $custom_field_column_array = array();
        $field_var_array = array();
        $custom_join = NULL;
        $i = 1;
        $condition = "";

        if (isset($gender) && $gender != "") {
            $condition .= "and patients.gender = '" . $gender . "' ";
        }

        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $custom_join .= ('LEFT JOIN custom_field_values as ' . $tb_counter . ' ON death_report.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id . " ");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $sql = "select death_report.*,patients.patient_name,patients.id as `patient_id`,patients.gender " . $field_variable . " from death_report LEFT JOIN patients on patients.id = death_report.patient_id " . $custom_join . " where 0=0 " . $condition . " and date_format(death_report.death_date,'%Y-%m-%d') >='" . $start_date . "' and date_format(death_report.death_date,'%Y-%m-%d') <= '" . $end_date . "'";
        $this->datatables->query($sql)
            ->searchable('death_report.id,death_report.death_date,patients.patient_name,case_reference_id,death_report.guardian_name' . $custom_field_column)
            ->orderable('death_report.id,case_reference_id,guardian_name,death_report.death_date,patients.patient_name,patients.gender' . $custom_field_column)
            ->sort('date_format(death_report.death_date, "%m/%e/%Y")', 'desc')
            ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }

    public function mailsmsRecord($start_date, $end_date)
    {

        $sql = "select messages.* from messages where date_format(messages.created_at,'%Y-%m-%d') >='" . $start_date . "'and date_format(messages.created_at,'%Y-%m-%d') <= '" . $end_date . "'";
        $this->datatables->query($sql)
            ->searchable('messages.id,messages.created_at,"","","",""')
            ->orderable('messages.id,messages.created_at,"","","",""')
            ->sort('date_format(messages.created_at, "%m/%e/%Y")', 'desc')
            ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }

    public function payrollreportsRecord($start_date, $end_date)
    {
        $sql = "select staff.id as staff_id,staff.employee_id,staff.name,staff.surname,roles.name as user_type,roles.id as role_id,staff.surname,staff_designation.designation,department.department_name as department,staff_payslip.* from staff LEFT JOIN staff_payslip ON staff_payslip.staff_id=staff.id LEFT JOIN staff_designation ON staff.staff_designation_id = staff_designation.id LEFT JOIN department ON staff.department_id = department.id JOIN staff_roles ON staff_roles.staff_id = staff.id JOIN roles ON staff_roles.role_id = roles.id where date_format(staff_payslip.payment_date,'%Y-%m-%d') >='" . $start_date . "'and date_format(staff_payslip.payment_date,'%Y-%m-%d') <= '" . $end_date . "'";
        $this->datatables->query($sql)
            ->searchable('staff.name,roles.name,staff_designation.designation,month,year,payment_date,payment_mode,basic,total_allowance,total_deduction,tax,net_salary')
            ->orderable('staff.name,roles.name,staff_designation.designation,month,year,payment_date,payment_mode,basic,total_allowance,total_deduction,null,tax,net_salary')
            ->sort('date_format(staff.date_of_joining, "%m/%e/%Y")', 'desc')
            ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }

    public function expmedicinereportsRecords($start_date, $end_date, $condition = null)
    {
        $query = "";

        if ($condition['supplier'] != "") {
            $query .= " and medicine_supplier.id ='" . $condition['supplier'] . "' ";
        }
        if ($condition['medicine_category'] != "") {
            $query .= " and medicine_category_id ='" . $condition['medicine_category'] . "' ";
        }

        $sql = "select medicine_batch_details.*,pharmacy.medicine_name,pharmacy.medicine_company,pharmacy.medicine_group,medicine_category.medicine_category,supplier_bill_basic.supplier_id,medicine_supplier.supplier from medicine_batch_details JOIN pharmacy ON medicine_batch_details.pharmacy_id = pharmacy.id JOIN medicine_category ON pharmacy.medicine_category_id = medicine_category.id JOIN supplier_bill_basic ON medicine_batch_details.supplier_bill_basic_id = supplier_bill_basic.id inner JOIN  medicine_supplier  on medicine_supplier.id=supplier_bill_basic.supplier_id where 0=0 " . $query . " and date_format(medicine_batch_details.expiry,'%Y-%m-%d') >='" . $start_date . "' and date_format(medicine_batch_details.expiry,'%Y-%m-%d') <= '" . $end_date . "'";
        $this->datatables->query($sql)
            ->searchable('pharmacy.medicine_name,medicine_batch_details.batch_no,pharmacy.medicine_company,medicine_category.medicine_category,medicine_group,supplier,medicine_batch_details.expiry')
            ->orderable('pharmacy.medicine_name,medicine_batch_details.batch_no,pharmacy.medicine_company,medicine_category.medicine_category,medicine_group,supplier,medicine_batch_details.expiry')
            ->sort('medicine_batch_details.expiry', 'desc')
            ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }

    public function tpareportsRecords($search_array)
    {

        $ipd_query_string = "";
        $opd_query_string = "";
        if ($search_array['start_date'] != '' && $search_array['end_date'] != '') {
            $ipd_query_string .= " and date_format(ipd_details.date,'%Y-%m-%d') >=" . $this->db->escape($search_array['start_date']) . " and date_format(ipd_details.date,'%Y-%m-%d') <=" . $this->db->escape($search_array['end_date']);
            $opd_query_string .= " and date_format(visit_details.appointment_date,'%Y-%m-%d') >=" . $this->db->escape($search_array['start_date']) . " and date_format(visit_details.appointment_date,'%Y-%m-%d') <=" . $this->db->escape($search_array['end_date']);
        }

        if ($search_array['constant_id'] != '') {
            $ipd_query_string .= " and ipd_details.cons_doctor=" . $this->db->escape($search_array['constant_id']);
            $opd_query_string .= " and visit_details.cons_doctor=" . $this->db->escape($search_array['constant_id']);
        }

        if ($search_array['organisation'] != '') {
            $ipd_query_string .= " and ipd_details.organisation_id=" . $this->db->escape($search_array['organisation']);
            $opd_query_string .= " and visit_details.organisation_id=" . $this->db->escape($search_array['organisation']);
        }

        if ($search_array['case_id'] != '') {
            $ipd_query_string .= " and ipd_details.case_reference_id=" . $this->db->escape($search_array['case_id']);
            $opd_query_string .= " and opd_details.case_reference_id=" . $this->db->escape($search_array['case_id']);
        }

        if ($search_array['charge_id'] != '') {
            $ipd_query_string .= " and charges.id =" . $this->db->escape($search_array['charge_id']);
            $opd_query_string .= " and charges.id =" . $this->db->escape($search_array['charge_id']);
        }

        if ($search_array['charge_category'] != '') {
            $ipd_query_string .= " and charge_categories.id=" . $this->db->escape($search_array['charge_category']);
            $opd_query_string .= " and charge_categories.id=" . $this->db->escape($search_array['charge_category']);
        }

        $sql = "select ipd_details.id,ipd_details.date,'ipd' reference,ipd_details.organisation_id,'ipd_no' prefixno,patients.patient_name,patients.id as patient_id,patient_charges.standard_charge,patient_charges.tpa_charge,patient_charges.tax,patient_charges.apply_charge,patient_charges.amount,charges.name as charge_name,charge_categories.name as charge_category_name,charge_type_master.charge_type as charge_type,organisation.organisation_name,staff.name,staff.surname,staff.employee_id, ipd_details.case_reference_id from ipd_details inner join patients on ipd_details.patient_id=patients.id inner join staff on staff.id=ipd_details.cons_doctor inner join patient_charges on patient_charges.ipd_id=ipd_details.id join charges on charges.id=patient_charges.charge_id join charge_categories on charge_categories.id=charges.charge_category_id join charge_type_master on charge_type_master.id=charge_categories.charge_type_id join organisation on organisation.id=ipd_details.organisation_id where ipd_details.organisation_id!='' " . $ipd_query_string . " UNION ALL select visit_details.id,visit_details.appointment_date as date,'opd' reference,visit_details.organisation_id,'checkup_id' prefixno,patients.patient_name,patients.id as patient_id,patient_charges.standard_charge,patient_charges.tpa_charge,patient_charges.tax,patient_charges.apply_charge,patient_charges.amount,charges.name as charge_name,charge_categories.name as charge_category_name,charge_type_master.charge_type as charge_type,organisation.organisation_name,staff.name,staff.surname,staff.employee_id,opd_details.case_reference_id from visit_details inner join staff on staff.id=visit_details.cons_doctor inner join opd_details on visit_details.opd_details_id=opd_details.id inner join patients on opd_details.patient_id=patients.id inner join patient_charges on patient_charges.opd_id=opd_details.id join charges on charges.id=patient_charges.charge_id join charge_categories on charge_categories.id=charges.charge_category_id join charge_type_master on charge_type_master.id=charge_categories.charge_type_id join organisation on organisation.id=visit_details.organisation_id  where visit_details.organisation_id!=''  " . $opd_query_string . " ";
        $this->datatables->query($sql)
            ->searchable('case_reference_id,organisation_name,patients.id,patient_name,charge_categories.name,charges.name,charge_categories.name,charge_type_master.charge_type,patient_charges.standard_charge,apply_charge,tpa_charge,tax,amount')
            ->orderable('id,case_reference_id,reference,organisation_name,patient_name,date,name,charge_name,charge_category_name,charge_type,standard_charge,apply_charge,tpa_charge,tax,amount')
            ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }

    public function userlogreportRecord($usertype, $start_date, $end_date)
    {
        $condition = "";
        if ($usertype == 'patient') {
            $condition = " and userlog.role ='patient'";
        } elseif ($usertype == 'staff') {
            $condition = " and userlog.role != 'patient'";
        } elseif ($usertype == 'all') {
            $condition = " and userlog.role != 'NULL'";
        }

        $sql = "select userlog.* from userlog where date_format(userlog.login_datetime,'%Y-%m-%d') >='" . $start_date . "'and date_format(userlog.login_datetime,'%Y-%m-%d') <= '" . $end_date . "'  " . $condition . " ";
        $this->datatables->query($sql)
            ->searchable('userlog.user,userlog.role,userlog.ipaddress,login_datetime')
            ->orderable('userlog.user,userlog.role,userlog.ipaddress,login_datetime')
            ->sort('date_format(userlog.login_datetime, "%m/%e/%Y")', 'desc')
            ->query_where_enable(TRUE);
        return $this->datatables->generate('json');
    }

    public function searchincomegroup($start_date = null, $end_date = null, $head_id = null)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('income', '', '', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'income.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $this->datatables
            ->select('income.id,income.name,income.invoice_no,income.date,income.amount, income_head.income_category,income.amount,income_head.id as head_id' . $field_variable)
            ->searchable('income_head.income_category,income.id,income.name,income.date,income.invoice_no,income.amount' . $custom_field_column)
            ->orderable('income_head.income_category,income.id,income.name,income.date,income.invoice_no' . $custom_field_column)
            ->join('income_head', 'income.inc_head_id = income_head.id')
            ->where('income.date >=', $start_date)
            ->where('income.date <=', $end_date)
            ->from('income');

        if ($head_id != null) {
            $this->datatables->where('income.inc_head_id', $head_id);
        }
        $this->datatables->sort('income.inc_head_id', 'desc');
        return $this->datatables->generate('json');
    }

    public function getAllpathologybillRecord($id)
    {
        $query = $this->db
            ->select('pathology_billing.*,( SELECT IFNULL(SUM(transactions.amount),0) from transactions WHERE transactions.pathology_billing_id=pathology_billing.id ) as paid_amount,patients.patient_name,patients.id as pid,staff.name,staff.surname,staff.employee_id')
            ->join('patients', 'patients.id = pathology_billing.patient_id', 'left')
            ->join('staff', 'staff.id = pathology_billing.doctor_id', 'left')
            ->where('patients.id', $id);
        $result = $this->db->get('pathology_billing');
        return $result->result_array();
    }
}
