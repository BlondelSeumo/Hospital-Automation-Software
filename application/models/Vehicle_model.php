<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vehicle_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('vehicles', $data);
            $message   = UPDATE_RECORD_CONSTANT . " For Ambulance id " . $data['id'];
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
            $this->db->insert('vehicles', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Ambulance id " . $insert_id;
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

    public function get($id = null)
    {
        $this->db->select()->from('vehicles');
        if ($id != null) {
            $this->db->where('vehicles.id', $id);
        } else {
            $this->db->order_by('vehicles.id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result_array();
        }
    }

    public function getDetails($id)
    {
        $query = $this->db->select('vehicles.*')->where('id', $id)->get('vehicles');
        return $query->row_array();
    }

    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('vehicles');
        $message   = DELETE_RECORD_CONSTANT . " Where Ambulance id " . $id;
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

    public function addCallAmbulance($data, $transaction)
    {
        if (isset($data['id']) && $data['id'] != '') {

            $this->db->where('id', $data['id']);
            $this->db->update('ambulance_call', $data);
            $message   = UPDATE_RECORD_CONSTANT . " For Ambulance Call id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);

            if (isset($transaction) && !empty($transaction)) {

                $this->db->where('ambulance_call_id', $record_id);
                $q = $this->db->get('transactions');

                if ($q->num_rows() > 0) {

                    $this->db->where('id', $q->row()->id);
                    $this->db->update('transactions', $transaction);

                } else {

                    $transaction['ambulance_call_id'] = $record_id;
                    $trans_id                         = $this->transaction_model->add($transaction);
                    $this->db->where('id', $record_id);
                    $this->db->update('ambulance_call', array('transaction_id' => $trans_id));
                }

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
        } else {         

            $this->db->insert('ambulance_call', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Ambulance Call id " . $insert_id;
            $action    = "Insert";
            $record_id = $insert_id;           
            if (isset($transaction) && !empty($transaction)) {
                $transaction['ambulance_call_id'] = $record_id;
                 $trans_id =$this->transaction_model->add($transaction);               

                $this->db->where('id', $insert_id);
                $this->db->update('ambulance_call', array('transaction_id' => $trans_id));
            }

            return $insert_id;
        }

    }

    public function getBillDetails($id)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('ambulance_call');

        $custom_field_column_array = array();
        $field_var_array = array();
         $i                         = 1;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'ambulance_call.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);

        $this->db->select('ambulance_call.*,IFNULL((SELECT SUM(transactions.amount) FROM transactions WHERE transactions.ambulance_call_id = ambulance_call.id), 0) as total_paid,charges.name as charge_name,charge_categories.name as charge_category_name,vehicles.vehicle_no,vehicles.vehicle_model,patients.patient_name as patientname,staff.name,staff.surname,staff.employee_id' . $field_variable);
        $this->db->join('vehicles', 'vehicles.id = ambulance_call.vehicle_id');
        $this->db->join('charges', 'charges.id = ambulance_call.charge_id', 'left');
        $this->db->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'left');
        $this->db->join('staff', 'staff.id = ambulance_call.generated_by');
        $this->db->join('patients', 'patients.id = ambulance_call.patient_id');
        $this->db->where('ambulance_call.id', $id);
        $query = $this->db->get('ambulance_call');
        return $query->row_array();
    }

    public function getMaxId()
    {
        $query  = $this->db->select('max(id) as bill_no')->get("ambulance_call");
        $result = $query->row_array();
        return $result["bill_no"];
    }

    public function getAllBillDetails($id)
    {
        $query = $this->db->select('ambulance_call.*')
            ->where('ambulance_call.id', $id)
            ->get('ambulance_call');
        return $query->row();
    }

    public function getCallAmbulance()
    {
        $i               = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('ambulance_call', 1);
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'ambulance_call.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable = implode(',', $field_var_array);
        $query          = $this->db->select('ambulance_call.*,vehicles.vehicle_no,vehicles.vehicle_model,patients.patient_name as patient,patients.mobileno,patients.address as patient_address,staff.name,staff.surname,' . $field_variable)
            ->join('vehicles', 'vehicles.id = ambulance_call.vehicle_id')
            ->join('patients', 'patients.id = ambulance_call.patient_name')
            ->join('staff', 'staff.id = ambulance_call.generated_by')
            ->get('ambulance_call');
        return $query->result_array();
    }

    public function getAllambulancecallRecord()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('ambulance_call', 1);
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'ambulance_call.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $this->datatables
            ->select('ambulance_call.*,(SELECT IFNULL(SUM(transactions.amount),0) from transactions WHERE transactions.ambulance_call_id=ambulance_call.id ) as paid_amount,vehicles.vehicle_no,vehicles.vehicle_model,patients.patient_name as patient,patients.id as patient_id,patients.mobileno,patients.address as patient_address,staff.name,staff.surname' . $field_variable)
            ->join('vehicles', 'vehicles.id = ambulance_call.vehicle_id')
            ->join('patients', 'patients.id = ambulance_call.patient_id')
            ->join('staff', 'staff.id = ambulance_call.generated_by')	  
			 
            ->searchable('ambulance_call.id,ambulance_call.case_reference_id,patients.patient_name,patients.id,vehicles.vehicle_no,vehicles.vehicle_model,ambulance_call.driver,patients.mobileno,patients.address,ambulance_call.date' . $custom_field_column . ',ambulance_call.discount,ambulance_call.discount_percentage')
            ->orderable('ambulance_call.id,ambulance_call.case_reference_id,patients.patient_name,patients.id,vehicles.vehicle_no,vehicles.vehicle_model,ambulance_call.driver,patients.mobileno,patients.address,ambulance_call.date' . $custom_field_column . ',ambulance_call.discount,ambulance_call.discount_percentage')	
			
            ->sort('ambulance_call.id', 'desc')
            ->from('ambulance_call');
        return $this->datatables->generate('json');
    }

    public function getAllvehicleRecord()
    {
        $this->datatables
            ->select('vehicles.id,vehicles.vehicle_no,vehicles.vehicle_model,vehicles.manufacture_year,vehicles.vehicle_type,vehicles.driver_name,vehicles.driver_licence,vehicles.driver_contact,vehicles.note')
            ->searchable('vehicles.vehicle_no,vehicles.Vehicle_model,vehicles.manufacture_year,vehicles.driver_name,vehicles.driver_licence,vehicles.driver_contact,vehicles.vehicle_type')
            ->orderable('vehicles.vehicle_no,vehicles.Vehicle_model,vehicles.manufacture_year,vehicles.driver_name,vehicles.driver_licence,vehicles.driver_contact,vehicles.vehicle_type')
            ->sort('vehicles.id', 'desc')
            ->from('vehicles');
        return $this->datatables->generate('json');
    }

    public function getCallAmbulancepat($patient_id)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('ambulance_call', '', '', '', 1);
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'ambulance_call.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);

        $query = $this->db->select('ambulance_call.*,IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.ambulance_call_id=ambulance_call.id ),0) as `paid_amount`, vehicles.vehicle_no,vehicles.vehicle_model,vehicles.driver_name,vehicles.driver_contact,patients.patient_name as patient,patients.mobileno,patients.address' . $field_variable)
            ->join('transactions', 'transactions.ambulance_call_id = ambulance_call.id','left')
            ->join('vehicles', 'vehicles.id = ambulance_call.vehicle_id', 'left')
            ->join('patients', 'patients.id = ambulance_call.patient_id', 'left')
            ->where('ambulance_call.patient_id', $patient_id)
            ->group_by('ambulance_call.id')
            ->get('ambulance_call');
        return $query->result_array();
    }

    public function getBillDetailsAmbulance($id)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('ambulance_call', '', '', '', 1);
        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'ambulance_call.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);
        $query               = $this->db->select('ambulance_call.*,IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.ambulance_call_id=ambulance_call.id ),0) as `paid_amount`, staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,charge_categories.name as charge_category_name,charges.name as charge_name,vehicles.id,vehicles.vehicle_no,vehicles.vehicle_model,vehicles.driver_name,vehicles.driver_contact,patients.patient_name as patient,patients.mobileno,patients.address,staff_roles.role_id as staff_roles_id,' . $field_variable)
            ->join('vehicles', 'vehicles.id = ambulance_call.vehicle_id')
            ->join('patients', 'patients.id = ambulance_call.patient_id')
            ->join('staff', 'staff.id = ambulance_call.generated_by')
            ->join('staff_roles', 'staff_roles.staff_id = staff.id')
            ->join('charges', 'charges.id = ambulance_call.charge_id', 'left')
            ->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'left')
            ->where('ambulance_call.id', $id)
            ->get('ambulance_call');
        return $query->row_array();
    }

    public function getCallDetails($id)
    {
        $query = $this->db->select('ambulance_call.*,charge_categories.id as charge_category_id,transactions.cheque_date,transactions.cheque_no,transactions.payment_mode,transactions.amount as `payment_amount`')->join('charges', 'charges.id = ambulance_call.charge_id', 'left')->join('charge_categories', 'charge_categories.id = charges.charge_category_id', 'left')
            ->join('transactions', 'transactions.ambulance_call_id = ambulance_call.id', 'left')
            ->where('ambulance_call.id', $id)->get('ambulance_call');
        return $query->row_array();
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('ambulance_call');
		
		$this->db->delete("transactions", array("ambulance_call_id" => $id));
		
        $message   = DELETE_RECORD_CONSTANT . " Where Ambulance Call id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id, 'ambulance');
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

    public function getAmbulanceCallRecord($case_id)
    {
        $this->datatables
            ->select_sum('transactions.amount', 'paid_amount')
            ->select("ambulance_call.id, vehicles.vehicle_no,ambulance_call.net_amount, ambulance_call.date, ambulance_call.discount, ambulance_call.discount_percentage")
            ->join("vehicles", "ambulance_call.vehicle_id=vehicles.id")
            ->join('transactions', 'transactions.ambulance_call_id = ambulance_call.id', 'LEFT')
            ->searchable("ambulance_call.id, ambulance_call.vehicle_id,ambulance_call.net_amount, ambulance_call.date, ambulance_call.id")
            ->orderable("ambulance_call.id, ambulance_call.vehicle_id,ambulance_call.net_amount, ambulance_call.date, ambulance_call.id")
            ->where("ambulance_call.case_reference_id", $case_id)
            ->sort("ambulance_call.id", "desc")
            ->group_by('transactions.ambulance_call_id')
            ->from("ambulance_call");
        return $this->datatables->generate('json');
    }

    public function validate_paymentamount()
    {
        $payment_amount = $this->input->post('payment_amount');
        $net_amount     = $this->input->post('net_amount');
        if ($payment_amount > $net_amount) {

            $this->form_validation->set_message('check_exists', 'Amount should not be greater than balance ' . $net_amount);
            return false;
        } else {
            return true;
        }
    }

}
