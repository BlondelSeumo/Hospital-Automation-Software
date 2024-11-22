<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Patient_model extends MY_Model
{

    public $column_order  = array('opd_details.appointment_date', 'staff.name', 'opd_details.refference', 'opd_details.symptoms'); //set column field database for datatable orderable
    public $column_search = array('opd_details.appointment_date', 'staff.name', 'opd_details.refference', 'opd_details.symptoms');

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('patients', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Patient id " . $data['id'];
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
            $this->db->insert('patients', $data);

            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Patient id " . $insert_id;
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
  
    public function getVisitDetailsid($opd_details_id){
        $visit_array=array();
        $visit_id= $this->db->select('id')->from('visit_details')->where('opd_details_id',$opd_details_id)->get()->result_array();
        if(!empty($visit_id)){
			foreach ($visit_id as $key => $value) {
				$visit_array[]=$value['id'];
			}   
        }
        
        return $visit_array;
    }

    public function getVisitDetailByid($id){        
        $result= $this->db->select('*')->from('visit_details')->where('id',$id)->get()->row();        
        return $result;
    }

    public function addmedication($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('medication_report', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Medication Report id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('medication_report', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Medication Report id " . $insert_id;
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

    public function add_front_patient($data)
    {       
        $this->db->insert('patients', $data);        
        $insert_id = $this->db->insert_id();         
        return $insert_id;           
    }

    public function add_patient($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('patients', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On Patient id " . $data['id'];
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
            $this->db->insert('patients', $data);
           
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Patient id " . $insert_id;
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

    public function valid_patient($id)
    {
        $this->db->select('ipd_details.patient_id,ipd_details.discharged,patients.id as pid,ipd_details.id as ipdid');
        $this->db->join('patients', 'patients.id=ipd_details.patient_id');
        $this->db->where('patient_id', $id);
        $this->db->where('ipd_details.discharged', 'no');
        $query = $this->db->get('ipd_details');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getNursenote($id)
    {
        $this->db->select('nurse_note.id as nid,nurse_note.date,nurse_note.note,nurse_note.comment,nurse_note.staff_id,nurse_note.ipd_id,patients.patient_name,staff.name as nurse_name,staff.surname as nurse_surname')->from('nurse_note');
        $this->db->join('ipd_details', 'ipd_details.id = nurse_note.ipd_id', "LEFT");
        $this->db->join('patients', 'patients.id = ipd_details.patient_id', "LEFT");
        $this->db->join('staff', 'staff.id = nurse_note.staff_id', "LEFT");
        $this->db->where('nurse_note.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getConsultantRegister($id)
    {
        $this->db->select('consultant_register.*,patients.patient_name,staff.name as staffname,staff.surname as staffsurname')->from('consultant_register');
        $this->db->join('ipd_details', 'ipd_details.id = consultant_register.ipd_id', "LEFT");
        $this->db->join('patients', 'patients.id = ipd_details.patient_id', "LEFT");
        $this->db->join('staff', 'staff.id = consultant_register.cons_doctor', "LEFT");
        $this->db->where('consultant_register.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getstaffsearch()
    {
        $select_staffsearch = 'transactions.received_by ,staff.id as staffid,staff.name as staffname,staff.surname as staffsurname,staff.employee_id';
        $groupby_staffsearch = 'transactions'; 
        $join_staffsearch   = array('LEFT JOIN staff ON transactions.received_by = staff.id');
        $tablename_staffsearch = "transactions";
        $where=" where received_by!='' ";    
        $query = "select staff.id as staffid,staff.name as staffname,staff.surname as staffsurname,staff.employee_id from transactions  " . implode(" ", $join_staffsearch) .$where. " GROUP BY " . ($groupby_staffsearch . ".received_by");
        $res = $this->db->query($query);
        return $res->result_array();
    }

    public function getstaffipdbilling()
    {
        $this->db->select('transactions.received_by'); 
        $this->db->group_by('transactions.received_by');
        $query = $this->db->get('transactions');
        return $query->result_array();
    }

    public function getstaffpharmacybill()
    {
        $this->db->select('pharmacy_bill_basic.generated_by,staff.id as staffid,staff.name as staffname, staff.surname as staffsurname,staff.employee_id');
        $this->db->join('staff', 'staff.id=pharmacy_bill_basic.generated_by', 'left');
        $this->db->group_by('pharmacy_bill_basic.generated_by');
        $query = $this->db->get('pharmacy_bill_basic');
        return $query->result_array();
    }

    public function getstaffotbill()
    {
        $this->db->select('operation_theatre.generated_by,staff.id as staffid,staff.name as staffname, staff.surname as staffsurname,staff.employee_id');
        $this->db->join('staff', 'staff.id=operation_theatre.generated_by', 'left');
        $this->db->group_by('operation_theatre.generated_by');
        $query = $this->db->get('operation_theatre');
        return $query->result_array();
    }

    public function getstaffbloodissuebill()
    {
        $this->db->select('blood_issue.generated_by,staff.id as staffid,staff.name as staffname, staff.surname as staffsurname,staff.employee_id');
        $this->db->join('staff', 'staff.id=blood_issue.generated_by', 'left');
        $this->db->group_by('blood_issue.generated_by');
        $query = $this->db->get('blood_issue');
        return $query->result_array();
    }

    public function getstaffAmbulancebill()
    {
        $this->db->select('ambulance_call.generated_by,staff.id as staffid,staff.name as staffname, staff.surname as staffsurname,staff.employee_id');
        $this->db->join('staff', 'staff.id=ambulance_call.generated_by', 'left');
        $this->db->group_by('ambulance_call.generated_by');
        $query = $this->db->get('ambulance_call');
        return $query->result_array();
    }

    public function getstaffPathologybill()
    {
        $this->db->select('pathology_report.approved_by,staff.id as staffid,staff.name as staffname, staff.surname as staffsurname,staff.employee_id');
        $this->db->join('staff', 'staff.id=pathology_report.approved_by', 'left');
        $this->db->group_by('pathology_report.approved_by');
        $query = $this->db->get('pathology_report');
        return $query->result_array();
    }

    public function getstaffRadiobill()
    {
        $this->db->select('radiology_report.generated_by,staff.id as staffid,staff.name as staffname, staff.surname as staffsurname,staff.employee_id');
        $this->db->join('staff', 'staff.id=radiology_report.generated_by', 'left');
        $this->db->group_by('radiology_report.generated_by');
        $query = $this->db->get('radiology_report');
        return $query->result_array();
    }
  
    public function getstaffbytransactionbill()
    {
        $this->db->select('transactions.received_by,staff.id as staffid,staff.name as staffname, staff.surname as staffsurname,staff.employee_id');
        $this->db->join('staff', 'staff.id=transactions.received_by');
        $this->db->group_by('transactions.received_by');
        $query = $this->db->get('transactions');
        return $query->result_array();
    }

    public function getPatientsByArrayopd($array)
    {
        $this->db->select('opd_details.id,visit_details.id as checkup_id,opd_details.patient_id,opd_details.discharged,"opd" as module,patients.id as pid,patients.patient_name,patients.guardian_name,patients.gender,patients.mobileno,patients.email,patients.dob,patients.image,patients.address,CONCAT_WS(" ",staff.name,staff.surname,"(",staff.employee_id,")") as doctorname');
        $this->db->join('visit_details', 'visit_details.opd_details_id = opd_details.id', "inner");
        $this->db->join('staff', 'staff.id = visit_details.cons_doctor', "inner");
        $this->db->join('patients', 'opd_details.patient_id = patients.id', "left");        
        $this->db->where_in('opd_details.id', $array);
        $query = $this->db->get('opd_details');
        return $query->result();
    }

    public function getPatientsByArrayipd($array)
    {
        $this->db->select('ipd_details.id,ipd_details.patient_id,ipd_details.discharged,"ipd" as module,patients.id as pid,patients.patient_name,patients.guardian_name,patients.gender,patients.mobileno,patients.email,patients.dob,patients.image,patients.address,staff.name as doctorname,staff.surname');
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "inner");
        $this->db->join('patients', 'ipd_details.patient_id = patients.id', "left");
        $this->db->where_in('ipd_details.id', $array);
        $query = $this->db->get('ipd_details');
        return $query->result();
    }

    public function getpatientsByArray($array)
    {
        $i               = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('patient');
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'patients.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable = implode(',', $field_var_array);
        $this->db->select('patients.*,blood_bank_products.name as blood_group,' . $field_variable)->from('patients');
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id', "left");
        $this->db->where_in('patients.id', $array);
        $this->db->order_by('patients.id');
        $query = $this->db->get();
        return $query->result();
    }

    public function doctCharge($doctor)
    {
        $query = $this->db->where("doctor", $doctor)->get("consult_charges");
        return $query->row_array();
    }

    public function doctortpaCharge($doctor, $organisation = "")
    {
        $result      = array();
        $first_query = $this->db->where("consult_charges.doctor", $doctor)
            ->get("consult_charges");
        $first_result = $first_query->row_array();
        $charge_id    = $first_result["id"];
        $result       = $first_result;
        if (!empty($organisation)) {
            $second_query = $this->db->select("tpa_doctorcharges.org_charge")
                ->where("charge_id", $charge_id)
                ->where("org_id", $organisation)
                ->get("tpa_doctorcharges");
            $second_result = $second_query->row_array();
            if ($second_query->num_rows() > 0) {
                $result["org_charge"] = $second_result["org_charge"];
            } else {
                $result["org_charge"] = $first_result["standard_charge"];
            }
        } else {
            $result["org_charge"] = '';
        }
        return $result;
    }

    public function doctName($doctor)
    {
        $query = $this->db->where("id", $doctor)->get("staff");
        return $query->row_array();
    }

    public function patientDetails($id)
    {
        $query = $this->db->where("id", $id)->get("patients");
        return $query->row_array();
    }

    public function getpatient()
    {
        $query = $this->db->where("patients.is_active", 'yes')->get("patients");
        return $query->row_array();
    }

    public function doctorDetails($id)
    {
        $query = $this->db->where("id", $id)->get("staff");
        return $query->row_array();
    }

    public function supplierDetails($id)
    {
        $query = $this->db->where("id", $id)->get("medicine_supplier");
        return $query->row_array();
    }

    public function add_opd($data, $transcation_data, $charge, $opd_visit_data)
    {
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('opd_details', $data);
            $opd_id = $data['id'] ;
           
        } else {

            //===================
            $this->db->insert('case_references', array('id' => null));
            $case_id = $this->db->insert_id();
            //===================
            $data['case_reference_id'] = $case_id;
            $this->db->insert('opd_details', $data);
            $insert_id                             = $this->db->insert_id();     
            $opd_id = $insert_id  ;    
            $transcation_data['case_reference_id'] = $case_id;
            $transcation_data['opd_id']            = $insert_id;
            $transation_id = $this->transaction_model->add($transcation_data);         
            $charge['opd_id'] = $insert_id;
            $this->db->insert("patient_charges", $charge);
            $patient_charge_id                   = $this->db->insert_id();
            $opd_visit_data['opd_details_id']    = $insert_id;
            $opd_visit_data['patient_charge_id'] = $patient_charge_id;
            $opd_visit_data['transaction_id']    = $transation_id;           
            $this->patient_model->addvisitDetails($opd_visit_data);         

        }
        return $opd_id ;
         
    }

    public function add_visit_recheckup($opd_visit_data, $transcation_data, $charge)
    {
       $this->db->trans_start(); # Starting Transaction
       $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($opd_visit_data['id']) && $opd_visit_data['id'] != '') {
            $this->db->where('id', $opd_visit_data['id']);
            $this->db->update('visit_details', $opd_visit_data);
            $message   = UPDATE_RECORD_CONSTANT . " On Visit Details id " . $opd_visit_data['id'];
            $action    = "Update";
            $record_id = $opd_visit_data['id'];
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

            $this->db->insert('visit_details', $opd_visit_data);

            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Visit Details id " . $insert_id;
            $action    = "Insert";

            $transation_id= $this->transaction_model->add($transcation_data);           
            $this->log($message, $insert_id, $action);
            $charge['opd_id'] = $opd_visit_data['opd_details_id'];
            $this->db->insert("patient_charges", $charge);
            $patient_charge_id = $this->db->insert_id();
            $update_array      = array(
                'id'                => $insert_id,
                'transaction_id'    => $transation_id,
                'patient_charge_id' => $patient_charge_id,
            );
            $this->db->where('id', $update_array['id']);
            $this->db->update('visit_details', $update_array);
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
    }

    public function add_opdvisit($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('visit_details', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On OPD Visit id " . $data['id'];
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
            $this->db->insert('visit_details', $data);

            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On OPD Visit id " . $insert_id;
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

    public function move_opd_to_ipd($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //===================
        $this->db->insert('ipd_details', $data);
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On IPD id " . $insert_id;
        $action    = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $update_opd_data = array('is_ipd_moved' => 1);
        $this->db->where('patient_id', $data['patient_id']);
        $this->db->where('case_reference_id', $data['case_reference_id']);
        $this->db->update('opd_details', $update_opd_data);
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

    public function add_ipd($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('ipd_details', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On IPD id " . $data['id'];
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
            //===================
            $this->db->insert('case_references', array('id' => null));
            $case_id = $this->db->insert_id();
            //===================
            $data['case_reference_id'] = $case_id;
            $this->db->insert('ipd_details', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On IPD id " . $insert_id;
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

    public function add_disch_summary($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('discharged_summary', $data);
        } else {
            $this->db->insert('discharged_summary', $data);
            return $this->db->insert_id();
        }
    }

    public function add_dischopd_summary($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('discharged_summary_opd', $data);
        } else {
            $this->db->insert('discharged_summary_opd', $data);
            return $this->db->insert_id();
        }
    }
 
    public function add_discharge($data)
    {
        if (!empty($data['ipd_details_id'])) {
            $status = array('discharged' => 'yes');
            $bed=$this->db->select('bed')->from('ipd_details')->where('id',$data['ipd_details_id'])->get()->row_array();
            $bed_status=array('is_active'=>'yes');
            $this->db->where('id', $bed['bed']);
            $this->db->update('bed', $bed_status);
            $this->db->where('id', $data['ipd_details_id']);
            $this->db->update('ipd_details', $status);
            $this->db->where(array('bed.id'=>$bed['bed'],'is_active'=>'yes'));
            $this->db->update('bed', $bed_status);
        }  

        if (!empty($data['opd_details_id'])) {
            $opd_status = array('discharged' => 'yes');
            $this->db->where('id', $data['opd_details_id']);
            $this->db->update('opd_details', $opd_status);            
        }
       
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('discharge_card', $data);
        } else {
            $this->db->insert('discharge_card', $data);
            return $this->db->insert_id();
        }
    }

    public function get_dischargeCard($card_data)
    {
        foreach ($card_data as $key => $value) {
            $this->db->where($key, $value);
        }
        
        $card = $this->db->select('*')->from('discharge_card')->get()->row_array();
        return $card;
    }

    public function addipd($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('ipd_prescription_basic', $data);
        } else {
            $this->db->insert('ipd_prescription_basic', $data);
            return $this->db->insert_id();
        }
    }

    public function searchAll($searchterm)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('patient', 1);

        $field_var_array = array();
        if (!empty($custom_fields)) { 
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'patients.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable = implode(',', $field_var_array);

        $this->db->select('patients.*,' . $field_variable)
            ->from('patients')
            ->like('patients.patient_name', $searchterm)
            ->or_like('patients.guardian_name', $searchterm)
            ->or_like('patients.patient_type', $searchterm)
            ->or_like('patients.address', $searchterm)
            ->or_like('patients.id', $searchterm)
            ->order_by('patients.id', 'desc');

        $query     = $this->db->get();
        $result    = $query->result_array();
        $info      = array();
        $data      = array();
        $url       = array();
        $info_data = array('OPD', 'IPD', 'Radiology', 'Pathology', 'Pharmacy', 'Operation Theatre');
        $info_url  = array();
        foreach ($result as $key => $value) {
            if ($value['is_active'] == 'yes') {
                $id          = $value["id"];
                $info_url[0] = base_url() . 'admin/patient/profile/' . $value['id'] . "/" . $value['is_active'];
                $info_url[1] = base_url() . 'admin/patient/ipdprofile/' . $value['id'];
                $info_url[2] = base_url() . 'admin/radio/getTestReportBatch';
                $info_url[3] = base_url() . 'admin/pathology/getTestReportBatch';
                $info_url[4] = base_url() . 'admin/pharmacy/bill';

                $info[0] = $this->db->where("patient_id", $id)->get("opd_details");
                $info[1] = $this->db->where("patient_id", $id)->get("ipd_details");
                $info[2] = $this->db->get("radiology_report");
                $info[3] = $this->db->get("pathology_report");
                $info[4] = $this->db->where("patient_id", $id)->get("pharmacy_bill_basic");

                for ($i = 0; $i < sizeof($info); $i++) {
                    if ($info[$i]->num_rows() > 0) {
                        $data[$i] = $info_data[$i];
                        $url[$i]  = $info_url[$i];
                    } else {
                        unset($data[$i]);
                        unset($url[$i]);
                    }
                }
                $result[$key]['info'] = $data;
                $result[$key]['url']  = $url;
            } else {
                unset($result[$key]);
            }
        }

        return $result;
    }

    // ===================== search datatable for Patient ==================================

    public function searchDataTablePatientRecord($searchterm)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('patient', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'patients.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->datatables 
            ->select('patients.*' . $field_variable)
            ->searchable('"",patients.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address,patients.is_dead' . $custom_field_column)
            ->orderable('"",patients.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address,patients.is_dead' . $custom_field_column)
            ->sort('patients.id', 'asc')
            ->where('patients.is_active', 'yes')
            ->group_start()
            ->like('patients.patient_name', $searchterm)
            ->or_like('patients.mobileno', $searchterm)
            ->or_like('patients.email', $searchterm)
            ->or_like('patients.address', $searchterm)
            ->or_like('patients.guardian_name', $searchterm)
            ->or_like('patients.identification_number', $searchterm)
            ->or_like('patients.known_allergies', $searchterm)
            ->or_like('patients.note', $searchterm)
            ->or_like('patients.insurance_id', $searchterm)
            ->group_end()
            ->from('patients');
        return $this->datatables->generate('json');
    }

    public function getAllpatientRecord()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('patient', 1);
        $custom_field_column_array = array();

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'patients.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable      = implode(',', $field_var_array);
        $custom_field_column = implode(',', $custom_field_column_array);
        $this->datatables
            ->select('patients.*,' . $field_variable)
            ->searchable('patients.id,patients.id as patient_unique_id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address,' . $custom_field_column)
            ->orderable('patients.id,patients.patient_unique_id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address,' . $custom_field_column)
            ->sort('patients.id', 'asc')
            ->where('patients.is_active', 'yes')
            ->from('patients');
        return $this->datatables->generate('json');
    }

    public function getAlldisablepatientRecord()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('patient', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'patients.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->datatables
            ->select('patients.*' . $field_variable)
            ->searchable('patients.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address' . $custom_field_column)
            ->orderable('patients.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address' . $custom_field_column)
            ->sort('patients.id', 'asc')
            ->where('patients.is_active', 'no')
            ->from('patients');
        return $this->datatables->generate('json');
    }

    public function searchAlldisable($searchterm)
    {
        $this->db->select('patients.*')
            ->from('patients')
            ->like('patients.patient_name', $searchterm)
            ->or_like('patients.guardian_name', $searchterm)
            ->or_like('patients.patient_type', $searchterm)
            ->or_like('patients.address', $searchterm)
            ->or_like('patients.patient_unique_id', $searchterm)
            ->order_by('patients.id', 'desc');
        $query     = $this->db->get();
        $result    = $query->result_array();
        $info      = array();
        $data      = array();
        $url       = array();
        $info_data = array('OPD', 'IPD', 'Radiology', 'Pathology', 'Pharmacy', 'Operation Theatre');
        $info_url  = array();
        foreach ($result as $key => $value) {
            if ($value['is_active'] == 'no') {
                $id = $value["id"];

                $info_url[0] = base_url() . 'admin/patient/profile/' . $value['id'] . "/" . $value['is_active'];
                $info_url[1] = base_url() . 'admin/patient/ipdprofile/' . $value['id'] . "/" . $value['is_active'];
                $info_url[2] = base_url() . 'admin/radio/getTestReportBatch';
                $info_url[3] = base_url() . 'admin/pathology/getTestReportBatch';
                $info_url[4] = base_url() . 'admin/pharmacy/bill';
                $info_url[5] = base_url() . 'admin/operationtheatre/otsearch';

                $info[0] = $this->db->where("patient_id", $id)->get("opd_details");
                $info[1] = $this->db->where("patient_id", $id)->get("ipd_details");
                $info[2] = $this->db->where("patient_id", $id)->get("radiology_report");
                $info[3] = $this->db->where("patient_id", $id)->get("pathology_report");
                $info[4] = $this->db->where("patient_id", $id)->get("pharmacy_bill_basic");
                $info[5] = $this->db->where("patient_id", $id)->get("operation_theatre");

                for ($i = 0; $i < sizeof($info); $i++) {
                    if ($info[$i]->num_rows() > 0) {
                        $data[$i] = $info_data[$i];
                        $url[$i]  = $info_url[$i];
                    } else {
                        unset($data[$i]);
                        unset($url[$i]);
                    }
                }
                $result[$key]['info'] = $data;
                $result[$key]['url']  = $url;
            } else {
                unset($result[$key]);
            }
        }

        return $result;
    }

    public function checkpatientipd($patient_type)
    {
        $this->db->where('patient_id', $patient_type);
        $query = $this->db->get('ipd_details');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkpatientipddis($pid)
    {
        $this->db->where('patient_id', $pid);
        $this->db->where('discharged', 'no');
        $query = $this->db->get('ipd_details');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkmedicationdose($ipd_id, $pharmacy_id, $date, $time)
    {
        $this->db->where('medication_report.ipd_id', $ipd_id);
        $this->db->where('medication_report.pharmacy_id', $pharmacy_id);
        $this->db->where('medication_report.date', $date);
        $this->db->where('medication_report.time', $time);
        $query = $this->db->get('medication_report');        
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkmedicationdoseopd($opd_id, $pharmacy_id, $date, $time)
    {
        $this->db->where('medication_report.opd_details_id', $opd_id);
        $this->db->where('medication_report.pharmacy_id', $pharmacy_id);
        $this->db->where('medication_report.date', $date);
        $this->db->where('medication_report.time', $time);
        $query = $this->db->get('medication_report');       
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkpatientopd($patient_type)
    {
        $this->db->where('patient_id', $patient_type);
        $query = $this->db->get('opd_details');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkpatientpharma($patient_type)
    {
        $this->db->where('patient_id', $patient_type);
        $query = $this->db->get('pharmacy_bill_basic');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkpatientot($patient_type)
    {
        $this->db->where('patient_id', $patient_type);
        $query = $this->db->get('operation_theatre');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getPatientListall()
    {
        $this->db->select('patients.*')->from('patients');
        $this->db->where('patients.is_active', 'yes');
        $this->db->order_by('patients.patient_name', 'asc');
        $query = $this->db->get();
        return $query->result_array();

        $userdata           = $this->customlib->getUserData();
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option     = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }
    }

    public function getchildMother()
    {
        $this->db->select('patients.*')->from('patients');
        $this->db->where('patients.is_active', 'yes');
        $this->db->where('patients.gender', 'Female');
        $this->db->order_by('patients.patient_name', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getpatientallforidcard()
    {
        $this->datatables
            ->select('patients.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address')
            ->searchable('patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name')
            ->orderable('patients.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address')
            ->sort('patients.id', 'desc')
            ->where('patients.is_active', 'yes')
            ->from('patients');
        return $this->datatables->generate('json');
    }

    public function getsymptoms($id)
    {
        $this->db->select('symptoms.*')->from('symptoms');
        $this->db->where('symptoms.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getBlooddonarListall()
    {
        $this->db->select('blood_donor.*')->from('blood_donor');
        $this->db->order_by('blood_donor.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getPatientListallPat()
    {
        $this->db->select('patients.*')->from('patients');
        $this->db->order_by('patients.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getPatientList()
    {
        $this->db->select('patients.*,users.username,users.id as user_tbl_id,users.is_active as user_tbl_active')
            ->join('users', 'users.user_id = patients.id')
            ->from('patients');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getpatientDetails($id)
    { 
        $this->db->select('patients.*,blood_bank_products.name as blood_group_name,organisation.organisation_name')
        ->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left')
        ->join('organisation', 'organisation.id = patients.organisation_id','left')
        ->from('patients')->where('patients.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getpatientbyid($id)
    {
        $this->db->select('patients.*')->from('patients')->where('patients.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getpatientOPDYearCounter($patient_id,$year)
    {
		$sql= "SELECT count(*) as `total_visits`,Year(appointment_date) as `year` FROM `visit_details` INNER JOIN opd_details on opd_details.id=visit_details.opd_details_id and opd_details.patient_id=".$this->db->escape($patient_id)." WHERE  YEAR(appointment_date) >= ".$this->db->escape($year)." and can_delete ='no' group by YEAR(appointment_date)";
		$query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getpatientIPDYearCounter($patient_id,$year)
    {
		$sql= "SELECT count(*) as `total_visits`,Year(date) as `year` FROM `ipd_details` WHERE YEAR(date) >= ".$this->db->escape($year)." AND patient_id=".$this->db->escape($patient_id)." GROUP BY  YEAR(date)";
		$query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getpatientbyidforidcard($id)
    {
        $this->datatables
            ->select('patients.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address')
            ->searchable('patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name')
            ->orderable('patients.id,patients.patient_name,patients.age,patients.gender,patients.mobileno,patients.guardian_name,patients.address')
            ->sort('patients.id', 'desc')
            ->where('patients.id', $id)
            ->from('patients');
        return $this->datatables->generate('json');
    }

    public function getpatientbyUniqueid($uid)
    {
        $this->db->select('patients.id')->from('patients')->where('patients.patient_unique_id', $uid);
        $query = $this->db->get();
        return $this->db->query();
    }

    public function searchFullText($opd_month, $searchterm, $carray = null, $limit = 100, $start = "")
    {
        $last_date          = date("Y-m-01 23:59:59.993", strtotime("-" . $opd_month . " month"));
        $userdata           = $this->customlib->getUserData();
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $this->db->select('opd_details.*,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.guardian_name,patients.gender,patients.mobileno,patients.is_ipd,staff.name,staff.surname')->from('opd_details');
        $this->db->join('patients', "patients.id=opd_details.patient_id", "LEFT");
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "LEFT");
        $this->db->group_start();
        $this->db->like('patients.patient_name', $searchterm);
        $this->db->or_like('patients.guardian_name', $searchterm);
        $this->db->group_end();
        $this->db->order_by('max(opd_details.appointment_date)', 'desc');
        $this->db->group_by('opd_details.patient_id');
        $this->db->limit($limit, $start);
        if ($doctor_restriction == 'enabled') {
            if ($userdata["role_id"] == 3) {
                $this->db->where('opd_details.cons_doctor', $userdata['id']);
            }
        }
        $query  = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    // ===================== search datatable for OPD ==================================

    public function getAllopdRecord()
    {
        $setting            = $this->setting_model->get();
        $opd_month          = $setting[0]['opd_record_month'];
        $userdata           = $this->customlib->getUserData();
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
       
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('opd', 1);
       
        $custom_field_column_array = array();
        $field_var_array = array();
       if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'opd_details.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->datatables
            ->select('opd_details.id as opdid,opd_details.case_reference_id,patients.id as pid,count(opd_details.patient_id) as total_visit,max(visit_details.appointment_date) as last_visit,patients.patient_name,patients.id as patientid,patients.guardian_name,patients.gender,patients.mobileno,patients.is_ipd,staff.name,staff.surname,staff.employee_id,patients.is_antenatal'. $field_variable )
            ->join('visit_details', "opd_details.id=visit_details.opd_details_id", "LEFT")
            ->join('patients', "patients.id=opd_details.patient_id", "LEFT")
            ->join('staff', 'staff.id = visit_details.cons_doctor', "LEFT")			
            ->searchable('patients.patient_name,patients.id,patients.guardian_name,patients.gender,patients.mobileno,staff.name,visit_details.appointment_date'. $custom_field_column .',opd_details.patient_id,patients.is_antenatal')
            ->orderable('patients.patient_name,patients.id,patients.guardian_name,patients.gender,patients.mobileno,staff.name,visit_details.appointment_date'. $custom_field_column .',opd_details.patient_id,patients.is_antenatal')		
            ->sort('max(visit_details.appointment_date)', 'desc')            
            ->group_by('patients.id')
            ->from('opd_details');

        return $this->datatables->generate('json');
    }

    public function getalldischargeopdRecord()
    {
        $setting            = $this->setting_model->get();
        $opd_month          = $setting[0]['opd_record_month'];
        $userdata           = $this->customlib->getUserData();
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        if ($doctor_restriction == 'enabled') {
            if ($userdata["role_id"] == 3) {
                $this->datatables->where('opd_details.doctor', $userdata['id']);
            }
        }
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('opd', 1);
        $custom_field_column_array = array();
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'opd_details.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->datatables
            ->select('opd_details.id as opdid,opd_details.case_reference_id,patients.id as pid,count(opd_details.patient_id) as total_visit,max(visit_details.appointment_date) as last_visit,patients.patient_name,patients.id as patientid,patients.guardian_name,patients.gender,patients.mobileno,patients.is_ipd,staff.name,staff.surname,staff.employee_id' . $field_variable)
            ->join('visit_details', "opd_details.id=visit_details.opd_details_id", "LEFT")
            ->join('patients', "patients.id=opd_details.patient_id", "LEFT")
            ->join('staff', 'staff.id = visit_details.cons_doctor', "LEFT")
            ->searchable('patients.patient_name,patients.id,patients.guardian_name,patients.gender,patients.mobileno,staff.name' . $custom_field_column)
            ->orderable('patients.patient_name,patients.id,patients.guardian_name,patients.gender,patients.mobileno,staff.name,MAX(visit_details.appointment_date)' . $custom_field_column)
            ->sort('max(visit_details.appointment_date)', 'desc')
             ->where('opd_details.discharged',"yes")
            ->group_by('patients.id')
            ->from('opd_details');
          
        return $this->datatables->generate('json');
    }

    // ===================== search datatable for patient credential ==================================

    public function getAllcredentialRecord($start_date = null, $end_date = null)
    {		 
		if($start_date != '' && $end_date != '' ){
			$query="  date_format(patients.created_at,'%Y-%m-%d') >='". $start_date."'and date_format(patients.created_at ,'%Y-%m-%d') <= '".$end_date."'";
			$this->datatables->where($query);
		}
		
        $this->datatables
            ->select('patients.*,users.id as uid,users.user_id,users.username,users.password')
            ->join('users', 'patients.id = users.user_id') 		
            ->searchable('patients.id,patients.patient_name,users.username')
            ->orderable('patients.id,patients.patient_name,users.username,patients.email,patients.mobileno,users.username,users.password')
            ->sort('patients.id', 'desc')
            ->from('patients');

        return $this->datatables->generate('json');
    }

    public function searchByMonth($opd_month, $searchterm, $carray = null)
    {
        $data       = array();
        $first_date = date('Y-m' . '-01', strtotime("-" . $opd_month . " month"));
        $last_date  = date('Y-m' . '-' . date('t', strtotime($first_date)) . ' 23:59:59.993');
        $this->db->select('patients.*')->from('patients');
        $this->db->where('patients.is_active', 'yes');
        $this->db->group_start();
        $this->db->like('patients.patient_name', $searchterm);
        $this->db->or_like('patients.guardian_name', $searchterm);
        $this->db->group_end();
        $this->db->order_by('patients.id', 'desc');
        $query  = $this->db->get();
        $result = $query->result_array();
        foreach ($result as $key => $value) {
            $consultant_data = $this->getConsultant($value["id"], $opd_month);
            if (!empty($consultant_data)) {
                $result[$key]['name']    = $consultant_data[0]["name"];
                $result[$key]['surname'] = $consultant_data[0]["surname"];
            }
        }

        return $result;
    }

    public function getConsultant($patient_id, $opd_month)
    {
        $first_date = date('Y-m' . '-01', strtotime("-" . $opd_month . " month"));
        $last_date  = date('Y-m' . '-' . date('t', strtotime($first_date)) . ' 23:59:59.993');
        $opd_query  = $this->db->select('opd_details.appointment_date,opd_details.case_type,staff.name,staff.surname')
            ->join('staff', 'staff.id = opd_details.cons_doctor', "inner")
            ->where('opd_details.appointment_date >', $first_date)
            ->where('opd_details.appointment_date <', $last_date)
            ->where('opd_details.patient_id', $patient_id)
            ->limit(1)
            ->get('opd_details');
        $result = $opd_query->result_array();
        return $result;
    }

    public function totalVisit($patient_id)
    {
        $query = $this->db->select('count(opd_details.patient_id) as total_visit')
            ->where('patient_id', $patient_id)
            ->get('opd_details');
        return $query->row_array();
    }

    public function totalPatientIPD($patient_id)
    {
        $query = $this->db->select('count(ipd_details.patient_id) as total')
            ->where('patient_id', $patient_id)
            ->get('ipd_details');
        return $query->row_array();
    }

    public function lastVisit($patient_id)
    {
        $query = $this->db->select('max(opd_details.appointment_date) as last_visit')
            ->where('patient_id', $patient_id)
            ->get('opd_details');
        return $query->row_array();
    }

    public function lastVisitopdno($patient_id)
    {
        $query = $this->db->select('max(opd_details.appointment_date) as lastvisit_date')
            ->where('patient_id', $patient_id)
            ->get('opd_details');
        $data = $query->row_array();

        if (!empty($data)) {
            $visitdate = $data["lastvisit_date"];
            $opd_query = $this->db->select("opd_details.opd_no as opdno")
                ->where("opd_details.appointment_date", $visitdate)
                ->get("opd_details");
            $result = $opd_query->row_array();
        }
        return $result;
    }

    public function getMaxPatientId()
    {
        $query = $this->db->select('max(patients.id) as patient_id')
            ->where('patients.is_active', 'yes')
            ->get('patients');
        $result = $query->row_array();
        return $result["patient_id"];
    }

    public function patientProfile($id, $active = 'yes')
    {
        $query     = $this->db->where("id", $id)->get("patients");
        $result    = $query->row_array();
        $data      = array();
        $opd_query = $this->db->where('patient_id', $id)->get('opd_details');
        $ipd_query = $this->db->where('patient_id', $id)->get('ipd_details');
        if ($opd_query->num_rows() > 0) {
            $data                 = $this->getDetails($id);
            $data["patient_type"] = 'Outpatient';
        } else if ($ipd_query->num_rows() > 0) {
            $data                 = $this->getIpdDetails($id, $active);
            $data["patient_type"] = 'Inpatient';
        }
        return $data;
    }

    public function patientProfileDetails($id, $active = 'yes')
    {
        $query  = $this->db->where("id", $id)->get("patients");
        $result = $query->row_array();
        return $result;
    }

    public function patientProfileType($id, $ptypeno)
    {
        $query     = $this->db->where("id", $id)->get("patients");
        $result    = $query->row_array();
        $data      = array();
        $opd_query = $this->db->where('opd_details.patient_id', $id)->where('opd_details.opd_no', $ptypeno)->get('opd_details');
        $ipd_query = $this->db->where('patient_id', $id)->where('ipd_details.ipd_no', $ptypeno)->get('ipd_details');
        if ($opd_query->num_rows() > 0) {
            $data                 = $this->getDetails($id);
            $data["patient_type"] = 'Outpatient';
        } else if ($ipd_query->num_rows() > 0) {
            $data                 = $this->getIpdDetailsptype($id);
            $data["patient_type"] = 'Inpatient';
        }
        return $data;
    }

    public function getopdDetails($id, $opdid = null)
    {
        $this->db->select('patients.*,opd_details.appointment_date,opd_details.case_type,opd_details.id as opdid,opd_details.casualty,opd_details.cons_doctor,opd_details.generated_by as generated_id,opd_details.refference,opd_details.known_allergies as opdknown_allergies,opd_details.amount as amount,opd_details.height,opd_details.weight,opd_details.bp,opd_details.symptoms,opd_details.tax,opd_details.payment_mode,opd_details.note_remark,opd_details.discharged,opd_details.pulse,opd_details.temperature,opd_details.respiration,opd_details.live_consult,opd_details.patient_id as pid,discharged_summary_opd.id as summary_id,discharged_summary_opd.note as summary_note,discharged_summary_opd.diagnosis as disdiagnosis,discharged_summary_opd.operation as disoperation,discharged_summary_opd.investigations as summary_investigations,discharged_summary_opd.treatment_home as summary_treatment_home,opd_billing.status,opd_billing.gross_total,opd_billing.discount,opd_billing.date as discharge_date,opd_billing.tax,opd_billing.net_amount,opd_billing.total_amount,opd_billing.other_charge,opd_billing.generated_by,opd_billing.id as bill_id,opd_billing.paymode as bill_paymode ,organisation.organisation_name,organisation.id as orgid,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge,visit_details.amount as visitamount,visit_details.id as visitid')->from('opd_details'); 
        $this->db->join('patients', 'patients.id = opd_details.patient_id', "left");
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "left");
        $this->db->join('organisation', 'organisation.id = patients.organisation', "left");
        $this->db->join('opd_billing', 'opd_details.id = opd_billing.opd_details_id', "left");
        $this->db->join('discharged_summary_opd', 'opd_details.id = discharged_summary_opd.opd_details_id', "left");
        $this->db->join('consult_charges', 'consult_charges.doctor=opd_details.cons_doctor', 'left');
        $this->db->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left');
        $this->db->join('visit_details', 'visit_details.opd_details_id=opd_details.id', 'left');
        $this->db->where('patients.is_active', 'yes');
        if ($opdid != null) {
            $this->db->where('visit_details.opd_details_id', $opdid);
        }
        $query  = $this->db->get();
        $result = $query->row_array();
        if (!empty($result)) {
            $generated_by = $result["generated_id"];
            $staff_query  = $this->db->select("staff.name,staff.surname,staff.employee_id")
                ->where("staff.id", $generated_by)
                ->get("staff");
            $staff_result               = $staff_query->row_array();
            $result["generated_byname"] = $staff_result["name"] . " " . $staff_result["surname"] . " (" . $staff_result["employee_id"] . ")";
        }
        return $result;
    }

    public function getVisitsByOPDid($opdid)
	{    
		$this->db->select('visit_details.*,organisation.organisation_name,opd_details.id as opdid,opd_details.case_reference_id,opd_details.patient_id,patients.patient_name,patients.id as patient_id,patients.age,patients.month,patients.day,patients.dob,patients.guardian_name,patients.gender,patients.marital_status,patients.mobileno,patients.email,patients.address,patients.insurance_id,patients.insurance_validity,patients.identification_number,patients.known_allergies,patients.image as patient_image,blood_bank_products.name as blood_group_name,staff.name,staff.surname,staff.employee_id,patients.id as `patient_id`')->from('visit_details');
        $this->db->join('opd_details', 'opd_details.id = visit_details.opd_details_id');
        $this->db->join('patients', 'patients.id = opd_details.patient_id');
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left');
        $this->db->join('staff', 'staff.id = visit_details.cons_doctor');
        $this->db->join('organisation', 'organisation.id = visit_details.organisation_id', 'left');
        $this->db->where('visit_details.opd_details_id', $opdid);
        $query  = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function getVisitDetailsbyopdid($opdid, $check_print = NULL)
    {
        $i               = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('opd','',true,'', '');
        $custom_field_column_array= array();
        
        $field_var_array = array();
        if($check_print == 'print'){        

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    $tb_counter = "table_custom_" . $i;
                    array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                    array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                    $this->db->join('custom_field_values as ' . $tb_counter, 'visit_details.opd_details_id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                    $i++;
                }
            }  
    
        }else{

        }
        
        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);

        $this->db->select('visit_details.*,appointment.id as appointment_no,appointment_queue.position as appointment_serial_no,department.department_name as department_name,organisation.organisation_name,opd_details.id as opdid,opd_details.case_reference_id,opd_details.patient_id,patients.patient_name,patients.id as patient_id,patients.age,patients.month,patients.day,patients.dob,patients.guardian_name,patients.gender,patients.marital_status,patients.mobileno,patients.email,patients.address,patients.insurance_id,patients.insurance_validity,patients.identification_number,patients.known_allergies,patients.image as patient_image,blood_bank_products.name as blood_group_name,staff.name,staff.surname,staff.employee_id,patients.id as `patient_id`,patients.as_of_date' . $field_variable)->from('visit_details');
        $this->db->join('opd_details', 'opd_details.id = visit_details.opd_details_id');
        $this->db->join('patients', 'patients.id = opd_details.patient_id');
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left');
        $this->db->join('staff', 'staff.id = visit_details.cons_doctor',"left");
        $this->db->join('appointment', 'appointment.visit_details_id = visit_details.id',"left");
        $this->db->join('appointment_queue', 'appointment_queue.appointment_id = appointment.id',"left");
        $this->db->join('department', 'department.id = staff.department_id', "left");
        $this->db->join('organisation', 'organisation.id = visit_details.organisation_id', 'left');
        $this->db->where('visit_details.opd_details_id', $opdid);
        $query  = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function getopdvisitDetailsbyvisitid($visitid)
    {
        $i               = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('opd','','','', '');
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'visit_details.opd_details_id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable = implode(',', $field_var_array);
        $this->db->select('visit_details.*,patients.organisation_id,visit_details.known_allergies as visit_known_allergies, transactions.payment_mode,transactions.amount,transactions.cheque_no,transactions.cheque_date,transactions.cheque_date,transactions.note as payment_note,transactions.payment_date,organisation.organisation_name,opd_details.id as opdid,opd_details.case_reference_id,opd_details.patient_id,patients.patient_name,patients.id as patient_id,patients.age,patients.month,patients.day,patients.dob,patients.guardian_name,patients.gender,patients.marital_status,patients.mobileno,patients.email,patients.address,patients.insurance_id,patients.insurance_validity,patients.identification_number,patients.known_allergies,patients.image as patient_image,blood_bank_products.name as blood_group_name,staff.name,staff.surname,staff.employee_id,patients.id as `patient_id`,patients.is_antenatal,visit_details.is_antenatal,patients.as_of_date,' . $field_variable)->from('visit_details');
        $this->db->join('opd_details', 'opd_details.id = visit_details.opd_details_id');
        $this->db->join('patients', 'patients.id = opd_details.patient_id');
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left');
        $this->db->join('staff', 'staff.id = visit_details.cons_doctor');
        $this->db->join('transactions', 'transactions.id = visit_details.transaction_id',"left");
        $this->db->join('organisation', 'organisation.id = patients.organisation_id', 'left');
        $this->db->where('visit_details.id', $visitid);
        $query  = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function getopdvisitrecheckupDetailsbyvisitid($visitid)
    {
        $i               = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('opdrecheckup');
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'visit_details.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }        
        
        $field_variable = implode(',', $field_var_array);
        $this->db->select('visit_details.*,,patients.as_of_date,visit_details.known_allergies as visit_known_allergies, opd_details.id as opdid,opd_details.case_reference_id,opd_details.patient_id,patients.patient_name,patients.age,patients.month,patients.day,patients.guardian_name,patients.gender,patients.marital_status,patients.mobileno,patients.email,patients.address,patients.blood_group,staff.name,staff.surname,staff.employee_id,blood_bank_products.name as blood_group_name,organisation.organisation_name,' . $field_variable)->from('visit_details');
        $this->db->join('opd_details', 'opd_details.id = visit_details.opd_details_id');
        $this->db->join('patients', 'patients.id = opd_details.patient_id');
        $this->db->join('blood_bank_products', 'patients.blood_bank_product_id = blood_bank_products.id', 'left');
        $this->db->join('staff', 'staff.id = visit_details.cons_doctor');
        $this->db->join('organisation', 'organisation.id = patients.organisation_id', 'left');
        $this->db->where('visit_details.id', $visitid);
        $query  = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function getopdDetailsbyopdid($opdid = null)
    {
        $this->db->select('opd_details.*,patients.patient_name,organisation.organisation_name,patients.age,patients.old_patient,patients.patient_unique_id,patients.guardian_name,patients.gender,patients.marital_status,patients.mobileno,patients.email,patients.address,patients.blood_group,staff.name,staff.surname')->from('opd_details');
        $this->db->join('patients', 'patients.id = opd_details.patient_id', "left");
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "left");
        $this->db->join('organisation', 'organisation.id = opd_details.organisation_id', "left");
        $this->db->where('opd_details.id', $opdid);
        $query  = $this->db->get();
        $result = $query->row_array();
        if (!empty($result)) {
            $generated_by = $result["generated_by"];
            $staff_query  = $this->db->select("staff.name,staff.surname,staff.employee_id")
                ->where("staff.id", $generated_by)
                ->get("staff");
            $staff_result               = $staff_query->row_array();
            $result["generated_byname"] = $staff_result["name"] . " " . $staff_result["surname"] . " (" . $staff_result["employee_id"] . ")";
        }
        return $result;
    }

    public function getDetails($opdid)
    {
        $this->db->select('opd_details.*,patients.as_of_date,blood_bank_products.name as blood_group_name,visit_details. casualty,visit_details.is_antenatal,visit_details.symptoms,visit_details.known_allergies,visit_details.refference,visit_details.case_type,patients.id as pid,patients.patient_name,patients.age,patients.month,patients.day,patients.image,patients.mobileno,patients.email,patients.gender,patients.dob,patients.marital_status,patients.blood_group,patients.address,patients.guardian_name,patients.month,patients.known_allergies,patients.marital_status,staff.name,staff.surname,staff.employee_id,discharge_card.discharge_date,organisation.organisation_name,organisation.code,patients.insurance_id,patients.insurance_validity,patients.organisation_id')->from('opd_details');
        $this->db->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left");
        $this->db->join('discharge_card', 'opd_details.id = discharge_card.opd_details_id', "left");
        $this->db->join('staff', 'staff.id = visit_details.cons_doctor', "left");
        $this->db->join('patients', 'patients.id = opd_details.patient_id', "left");
        $this->db->join('organisation', 'organisation.id = patients.organisation_id', "left");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left');
        $this->db->where('opd_details.id', $opdid);
        $query  = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function getDetailsopdnotification($id, $opdid = null)
    {
        $this->db->select('patients.*,opd_details.id as opdid,opd_details.generated_by as generated_id,opd_details.discharged,,staff.id as staff_id,staff.name,staff.surname,consult_charges.standard_charge,patient_charges.apply_charge,visit_details.id as visitid')->from('patients');
        $this->db->join('visit_details', 'visit_details.opd_details_id=opd_details.id', 'left');
        $this->db->join('opd_details', 'patients.id = opd_details.patient_id', "left");
        $this->db->join('staff', 'staff.id = visit_details.cons_doctor', "left");
        $this->db->join('organisation', 'organisation.id = patients.organisation', "left");
        $this->db->join('consult_charges', 'consult_charges.doctor=opd_details.cons_doctor', 'left');
        $this->db->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left');
        $this->db->where('patients.is_active', 'yes');
        $this->db->where('patients.id', $id);
        if ($opdid != null) {
            $this->db->where('opd_details.id', $opdid);
        }

        $query  = $this->db->get();
        $result = $query->row_array();
        if (!empty($result)) {
            $hospital_setting          = $this->setting_model->get();
            $result['currency_symbol'] = $hospital_setting[0]['currency_symbol'];
            $generated_by              = $result["generated_id"];
            $staff_query               = $this->db->select("staff.name,staff.surname")
                ->where("staff.id", $generated_by)
                ->get("staff");
            $staff_result               = $staff_query->row_array();
            $result["generated_byname"] = $staff_result["name"] . " " . $staff_result["surname"];
            $payment                    = $this->payment_model->getopdbilling($id, $opdid);
            $result['billing_amount']   = $payment['billing_amount'];
        }
        return $result;
    }

    public function addImport($patient_data)
    {
        $this->db->insert('patients', $patient_data);
        return $this->db->insert_id();
    }

    public function getIpdDetails($ipdid)
    {
        $i = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('patient','','','', 1);
        $custom_field_column_array= array();
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->db->join('custom_field_values as '.$tb_counter,'ipd_details.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);

        $this->db->select('patients.*,blood_bank_products.name as blood_group_name,ipd_details.patient_old,ipd_details.id as ipdid,ipd_details.patient_id,discharge_card.    discharge_date,ipd_details.date,ipd_details.date,ipd_details.case_type,ipd_details.id as ipdid,ipd_details.casualty,ipd_details.height,ipd_details.weight,ipd_details.bp,ipd_details.cons_doctor,ipd_details.refference,ipd_details.known_allergies as ipdknown_allergies,ipd_details.case_reference_id,ipd_details.credit_limit as ipdcredit_limit,ipd_details.symptoms,ipd_details.discharged as ipd_discharge,ipd_details.bed,ipd_details.bed_group_id,ipd_details.note as ipdnote,ipd_details.bed,ipd_details.bed_group_id,ipd_details.payment_mode,ipd_details.credit_limit,ipd_details.pulse,ipd_details.temperature,ipd_details.respiration,staff.id as staff_id,staff.name,staff.surname,staff.image as doctor_image,staff.employee_id,organisation.organisation_name,bed.name as bed_name,bed.id as bed_id,bed_group.name as bedgroup_name,floor.name as floor_name'.$field_variable)->from('ipd_details');
        $this->db->join('patients', 'patients.id = ipd_details.patient_id', "left");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left');
        $this->db->join('discharge_card', 'ipd_details.id = discharge_card.ipd_details_id', "left");
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "inner");
        $this->db->join('organisation', 'organisation.id = patients.organisation_id', "left");
        $this->db->join('bed', 'ipd_details.bed = bed.id', "left");
        $this->db->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left");
        $this->db->join('floor', 'floor.id = bed_group.floor', "left");
        $this->db->where('ipd_details.id', $ipdid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getIpdfornotification($id, $ipdid = '', $active = 'yes')
    {
        $this->db->select('patients.*,ipd_details.patient_id,ipd_details.date,ipd_details.discharged_date as ipd_dischargedate,ipd_details.case_type,ipd_details.ipd_no,ipd_details.id as ipdid,ipd_details.casualty,ipd_details.height,ipd_details.weight,ipd_details.bp,ipd_details.cons_doctor,ipd_details.refference,ipd_details.known_allergies as ipdknown_allergies,ipd_details.credit_limit as ipdcredit_limit,ipd_details.symptoms,ipd_details.discharged as ipd_discharge,ipd_details.tax,ipd_details.bed,ipd_details.bed_group_id,ipd_details.note as ipdnote,ipd_details.bed,ipd_details.bed_group_id,ipd_details.payment_mode,ipd_details.credit_limit,ipd_details.pulse,ipd_details.temperature,ipd_details.respiration,discharged_summary.id as summary_id,discharged_summary.note as summary_note,discharged_summary.diagnosis as disdiagnosis,discharged_summary.operation as disoperation,discharged_summary.investigations as summary_investigations,discharged_summary.treatment_home as summary_treatment_home,ipd_billing.status,ipd_billing.gross_total,ipd_billing.discount,ipd_billing.date as discharge_date,ipd_billing.tax,ipd_billing.net_amount,ipd_billing.total_amount,ipd_billing.other_charge,ipd_billing.generated_by,ipd_billing.id as bill_id,staff.id as staff_id,staff.name,staff.surname,organisation.organisation_name,bed.name as bed_name,bed.id as bed_id,bed_group.name as bedgroup_name,floor.name as floor_name')->from('patients');
        $this->db->join('ipd_details', 'patients.id = ipd_details.patient_id', "left");
        $this->db->join('ipd_billing', 'ipd_details.id = ipd_billing.ipd_id', "left");
        $this->db->join('discharged_summary', 'ipd_details.id = discharged_summary.ipd_id', "left");
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "inner");
        $this->db->join('organisation', 'organisation.id = patients.organisation', "left");
        $this->db->join('bed', 'ipd_details.bed = bed.id', "left");
        $this->db->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left");
        $this->db->join('floor', 'floor.id = bed_group.floor', "left");
        $this->db->where('patients.is_active', $active);
        $this->db->where('patients.id', $id);
        $this->db->where('ipd_details.id', $ipdid);
        $query  = $this->db->get();
        $result = $query->row_array();
        if (!empty($result)) {
            $hospital_setting          = $this->setting_model->get();
            $result['currency_symbol'] = $hospital_setting[0]['currency_symbol'];
            $charge                    = $this->payment_model->getChargeTotal($id, $ipdid);
            $result['charge_amount']   = $charge['apply_charge'];
            $payment                   = $this->payment_model->getPaidTotal($id, $ipdid);
            $result['paid_amount']     = $payment['paid_amount'];
        }
        return $result;
    }

    public function getIpdnotiDetails($id)
    {
        $this->db->select('ipd_details.*,')->from('ipd_details');
        $this->db->where('ipd_details.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getsummaryDetails($id)
    {
        $this->db->select('discharged_summary.*,patients.patient_name,patients.id as patientid,patients.id as patient_unique_id,patients.age,patients.month,patients.gender,patients.address,ipd_details.date,ipd_details.discharged_date');
        $this->db->join('patients', 'discharged_summary.patient_id = patients.id');
        $this->db->join('ipd_details', 'discharged_summary.ipd_id = ipd_details.id');
        $this->db->where('discharged_summary.id', $id);
        $query = $this->db->get('discharged_summary');
        return $query->row_array();
    }

    public function getsummaryopdDetails($id)
    {
        $this->db->select('discharged_summary_opd.*,patients.patient_name,patients.id as patientid,patients.id as patient_unique_id,patients.age,patients.month,patients.gender,patients.address,opd_billing.date as discharged_date,opd_details.id as opdid,opd_details.appointment_date');
        $this->db->join('patients', 'discharged_summary_opd.patient_id = patients.id');
        $this->db->join('opd_details', 'discharged_summary_opd.opd_details_id = opd_details.id');
        $this->db->join('opd_billing', 'discharged_summary_opd.opd_details_id = opd_billing.opd_details_id', 'inner');
        $this->db->where('discharged_summary_opd.id', $id);
        $query = $this->db->get('discharged_summary_opd');
        return $query->row_array();
    }

    public function getOpdnotiDetails($id)
    {
        $this->db->select('opd_details.*,')->from('opd_details');
        $this->db->where('opd_details.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getOpdpresnotiDetails($id)
    {
        $this->db->select('opd_details.*,')->from('opd_details');
        $this->db->where('opd_details.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getPatientId()
    {
        $this->db->select('patients.*,opd_details.appointment_date,opd_details.case_type,opd_details.id as opdid,opd_details.casualty,opd_details.cons_doctor,opd_details.refference,opd_details.known_allergies,opd_details.amount,opd_details.symptoms,opd_details.tax,opd_details.payment_mode')->from('patients');
        $this->db->join('opd_details', 'patients.id = opd_details.patient_id', "inner");
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "inner");
        $this->db->where('patients.is_active', 'yes');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getpatientidbyipd($ipdid)
    {
        $this->db->select('ipd_details')->from('ipd_details');
        $this->db->where('ipd_details.id', $ipdid);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function getopdidbyopdno($opdid)
    {
        $this->db->select('opd_details.id as opdid')->from('opd_details');
        $this->db->where('opd_details.id', $opdid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getipdidbyipdno($ipdid)
    {
        $this->db->select('ipd_details.id as ipdid')->from('ipd_details');
        $this->db->where('ipd_details.id', $ipdid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getOpd($id)
    {
        $this->db->select('opd_details.id')->from('opd_details');
        $this->db->where('opd_details.patient_id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getIpd($id)
    {
        $this->db->select('ipd_details.id')->from('ipd_details');
        $this->db->where('ipd_details.patient_id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getopdmaxid($id)
    {
        $this->db->select('max(opd_details.id) as opdid')->from('opd_details');
        $this->db->where('opd_details.patient_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getvisitmaxid($opdid)
    {
        $this->db->select('max(visit_details.id) as visitid,visit_details.organisation_id')->from('visit_details');
        $this->db->where('visit_details.opd_details_id', $opdid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getvisitminid($opdid)
    {
        $this->db->select('min(visit_details.id) as visitid')->from('visit_details');
        $this->db->where('visit_details.opd_details_id', $opdid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getOPDetails($opdid)
    {
        $userdata           = $this->customlib->getUserData();
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        if ($doctor_restriction == 'enabled') {
            if ($userdata["role_id"] == 3) {
                $this->db->where('opd_details.cons_doctor', $userdata['id']);
            }
        }
        if (!empty($opdid)) {
            $this->db->where("opd_details.id", $opdid);
        }
        $this->db->select('opd_details.*,visit_details.id as visit_id,patients.organisation,patients.old_patient,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge')->from('opd_details');
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "inner");
        $this->db->join('patients', 'patients.id = opd_details.patient_id', "inner");
        $this->db->join('consult_charges', 'consult_charges.doctor=opd_details.cons_doctor', 'left');
        $this->db->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left');
        $this->db->join('visit_details', 'visit_details.opd_details_id=opd_details.id', 'left');
        $this->db->group_by('opd_details.id', '');
        $this->db->order_by('opd_details.id', 'desc');
        $query = $this->db->get();

        if (!empty($opdid)) {
            return $query->row_array();
        } else {
            $result = $query->result_array();
            $i      = 0;
            foreach ($result as $key => $value) {
                $visit_details_id = $value["id"];
                $check            = $this->db->where("visit_details_id", $visit_details_id)->get('opd_prescription_basic');

                if ($check->num_rows() > 0) {

                    $result[$i]['prescription'] = 'yes';

                } else {

                    $result[$i]['prescription'] = 'no';
                    $userdata                   = $this->customlib->getUserData();
                    if ($this->session->has_userdata('hospitaladmin')) {
                        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                        if ($doctor_restriction == 'enabled') {
                            if ($userdata["role_id"] == 3) {
                                if ($userdata["id"] == $value["staff_id"]) {

                                } else {
                                    $result[$i]['prescription'] = 'not_applicable';
                                }
                            }
                        }
                    }
                }

                $i++;
            }
            return $result;
        }
    }

    public function getpatientopddetails($patientid, $visitid = '')
    {        
        $i               = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('opd','','','', 1);
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'opd_details.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        if (!empty($visitid)) {
            $this->db->where("visit_details.id", $visitid);
        }
        if (!empty($patientid)) {

        }

        $field_variable = implode(',', $field_var_array);
        $this->db->select('opd_details.id as opdid,opd_details.case_reference_id,sum(transactions.amount) as payamount,visit_details.*,max(visit_details.id) as visit_id,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,patients.id as pid,patients.patient_name,patients.age,patients.month,patients.day,patients.dob,patients.gender,' . $field_variable)->from('opd_details');
        $this->db->join('visit_details', 'visit_details.opd_details_id=opd_details.id');
        $this->db->join('transactions', 'transactions.opd_id=visit_details.opd_details_id', 'left');    
        $this->db->join('staff', 'staff.id = visit_details.cons_doctor', "left");
        $this->db->join('patients', 'patients.id = opd_details.patient_id', "left");
        $this->db->where('opd_details.patient_id', $patientid);
        $this->db->where('opd_details.discharged', 'no');
        $this->db->group_by('opd_details.id', '');
        $this->db->order_by('opd_details.id', 'desc');
        $query = $this->db->get();

        if (!empty($visitid)) {
            return $query->row_array();
        } else {
            $result = $query->result_array();
            $i      = 0;
            foreach ($result as $key => $value) {
                $visit_details_id = $value["id"];
                $check            = $this->db->where("visit_details_id", $visit_details_id)->get('ipd_prescription_basic');

                if ($check->num_rows() > 0) {
                    $result[$i]['prescription'] = 'yes';
                } else {
                    $result[$i]['prescription'] = 'no';
                    $userdata                   = $this->customlib->getUserData();
                    if ($this->session->has_userdata('hospitaladmin')) {
                        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                        if ($doctor_restriction == 'enabled') {
                            if ($userdata["role_id"] == 3) {
                                if ($userdata["id"] == $value["staff_id"]) {

                                } else {
                                    $result[$i]['prescription'] = 'not_applicable';
                                }
                            }
                        }
                    }
                }

                $i++;
            }
            return $result;

        }

    }

    public function getVisitDetailsByOPD($id)
    {
        $query = $this->db->select('visit_details.*,opd_details.id as opdid, staff.name,staff.surname')
            ->join('opd_details', 'visit_details.opd_details_id = opd_details.id')
            ->join('patients', 'opd_details.patient_id = patients.id')
            ->join('staff', 'opd_details.cons_doctor = staff.id')        
            ->get('visit_details');
        $result = $query->result_array();
        $i      = 0;
        foreach ($result as $key => $value) {
            $opd_id = $value["id"];
            $check  = $this->db->where("visit_details_id", $opd_id)->get('opd_prescription_basic');

            if ($check->num_rows() > 0) {
                $result[$i]['prescription'] = 'yes';
            } else {
                $result[$i]['prescription'] = 'no';
                $userdata                   = $this->customlib->getUserData();
                if ($this->session->has_userdata('hospitaladmin')) {
                    $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                    if ($doctor_restriction == 'enabled') {
                        if ($userdata["role_id"] == 3) {
                            if ($userdata["id"] == $value["staff_id"]) {

                            } else {
                                $result[$i]['prescription'] = 'not_applicable';
                            }
                        }
                    }
                }
            }
            $i++;
        }
        return $result;
    }

    public function getAllopdrechekupRecord($patientid, $opdid)
    {
        $this->datatables
            ->select('opd_details.*,patients.id as pid,patients.is_ipd,patients.organisation,patients.old_patient,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge')
            ->join('staff', 'staff.id = opd_details.cons_doctor', "inner")
            ->join('patients', 'patients.id = opd_details.patient_id', "LEFT")
            ->join('consult_charges', 'consult_charges.doctor=opd_details.cons_doctor', 'left')
            ->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left')
            ->searchable('opd_details.id')
            ->orderable('opd_details.id')
            ->sort('opd_details.id', 'desc')
            ->where('opd_details.patient_id', $patientid)
            ->where('opd_details.id', $opdid)
            ->group_by('opd_details.id', '')
            ->from('opd_details');
        $result = $this->datatables->generate('json');
        return $result;
    }

    public function getAllopdvisitRecord($patientid)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('opd', 1);
        $custom_field_column_array = array();

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'opd_details.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable      = implode(',', $field_var_array);
        $custom_field_column = implode(',', $custom_field_column_array);

        $this->datatables
            ->select('opd_details.case_reference_id,opd_details.id as opd_id,opd_details.patient_id as patientid,opd_details.is_ipd_moved,max(visit_details.id) as visit_id,visit_details.appointment_date,visit_details.refference,visit_details.symptoms,patients.id as pid,patients.patient_name,visit_details.is_antenatal,patients.gender,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge,' . $field_variable)
            ->join('visit_details', 'opd_details.id = visit_details.opd_details_id')
            ->join('staff', 'staff.id = visit_details.cons_doctor', "inner")
            ->join('patients', 'patients.id = opd_details.patient_id', "inner")
            ->join('consult_charges', 'consult_charges.doctor=visit_details.cons_doctor', 'left')
            ->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left')
            ->searchable('opd_details.id,opd_details.case_reference_id,visit_details.appointment_date,staff.name,visit_details.refference,visit_details.symptoms')
            ->orderable('opd_details.id,opd_details.case_reference_id,visit_details.appointment_date,staff.name,visit_details.refference,visit_details.symptoms,' . $custom_field_column)
            ->sort('visit_details.id', 'desc')
            ->where('opd_details.patient_id', $patientid)
            ->where('opd_details.discharged', 'no')
            ->group_by('visit_details.opd_details_id', '')
            ->from('opd_details');
        $result = $this->datatables->generate('json');
       
        return $result;
    }

    public function getAllvisitRecord($opdid)
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('opdrecheckup', 1);
        $custom_field_column_array = array();

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'visit_details.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->datatables
            ->select('visit_details.id as visit_id,visit_details.appointment_date,visit_details.refference,visit_details.symptoms,opd_details.id as opd_id,patients.id as pid,patients.patient_name,patients.gender,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,opd_details.discharged,visit_details.is_antenatal' . $field_variable)
            ->join('opd_details', 'opd_details.id = visit_details.opd_details_id', "left")
            ->join('staff', 'staff.id = visit_details.cons_doctor', "left")
            ->join('patients', 'patients.id = opd_details.patient_id', "left")
            ->searchable('visit_details.id,visit_details.appointment_date,staff.name,visit_details.refference,visit_details.symptoms')
            ->orderable('visit_details.id,visit_details.id,visit_details.appointment_date,staff.name,visit_details.refference,visit_details.symptoms,' . $custom_field_column)
            ->sort('visit_details.id', 'desc')
            ->where('visit_details.opd_details_id', $opdid)
            ->from('visit_details');
        $result = $this->datatables->generate('json');
        return $result;
    }

    public function getOPDDetailsforbill($id, $opdid = null)
    {
        if (!empty($opdid)) {
            $this->db->where("opd_details.id", $opdid);
        }
        $this->db->select('opd_details.*,patients.organisation,patients.old_patient,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,consult_charges.standard_charge,patient_charges.apply_charge,opd_payment.paid_amount,opd_billing.status')->from('opd_details');
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "inner");
        $this->db->join('patients', 'patients.id = opd_details.patient_id', "inner");
        $this->db->join('consult_charges', 'consult_charges.doctor=opd_details.cons_doctor', 'left');
        $this->db->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left');
        $this->db->join('opd_payment', 'opd_payment.opd_details_id=opd_details.id', 'left');
        $this->db->join('opd_billing', 'opd_billing.opd_details_id=opd_details.id', 'left');
        $this->db->where('opd_details.patient_id', $id);
        $this->db->group_by('opd_details.id', '');
        $this->db->order_by('opd_details.id', 'desc');
        $query = $this->db->get();
        if (!empty($opdid)) {
            return $query->row_array();
        } else {
            $result = $query->result_array();
            $i      = 0;
            foreach ($result as $key => $value) {
                $opd_id                = $value["id"];
                $check                 = $this->db->where("opd_details_id", $opd_id)->where("visit_id", 0)->get('prescription');
                $payment               = $this->getOPDPayment($value["patient_id"], $value["id"]);
                $charge                = $this->getOPDCharges($value["patient_id"], $value["id"]);
                $bill                  = $this->getOPDbill($value["patient_id"], $value["id"]);
                $result[$i]["payment"] = $payment['opdpayment'];
                $result[$i]["charges"] = $charge['charge'];
                $result[$i]["bill"]    = $bill['billamount'];
                $i++;
            }
            return $result;
        }
    }

    public function getIPDDetailsforbill($id, $opdid = null)
    {
        if (!empty($opdid)) {
            $this->db->where("ipd_details.id", $opdid);
        }
        $this->db->select('ipd_details.*,patients.organisation,patients.old_patient,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,patient_charges.apply_charge,payment.paid_amount,ipd_billing.status')->from('ipd_details');
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "inner");
        $this->db->join('patients', 'patients.id = ipd_details.patient_id', "inner");
        $this->db->join('patient_charges', 'ipd_details.id=patient_charges.ipd_id', 'left');
        $this->db->join('payment', 'payment.ipd_id=ipd_details.id', 'left');
        $this->db->join('ipd_billing', 'ipd_billing.ipd_id=ipd_details.id', 'left');
        $this->db->where('ipd_details.patient_id', $id);
        $this->db->group_by('ipd_details.id', '');
        $this->db->order_by('ipd_details.id', 'desc');
        $query = $this->db->get();
        if (!empty($opdid)) {
            return $query->row_array();
        } else {
            $result = $query->result_array();
            $i      = 0;
            foreach ($result as $key => $value) {
                $opd_id                = $value["id"];
                $check                 = $this->db->where("opd_details_id", $opd_id)->where("visit_id", 0)->get('prescription');
                $payment               = $this->getPayment($value["patient_id"], $value["id"]);
                $charge                = $this->getCharges($value["patient_id"], $value["id"]);
                $bill                  = $this->getIpdBillDetails($value["patient_id"], $value["id"]);
                $result[$i]["payment"] = $payment['payment'];
                $result[$i]["charges"] = $charge['charge'];
                $result[$i]["bill"]    = $bill['billamount'];
                $i++;
            }
            return $result;
        }
    }

    public function geteditDiagnosis($id)
    {
        $this->db->select('diagnosis.*,patients.patient_name,patients.patient_name')->from('diagnosis');
        $this->db->join('patients', 'patients.id = diagnosis.patient_id');
        $this->db->where('diagnosis.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getopddetailspres($id)
    {
        $this->db->select('opd_details.*,patients.organisation,patients.old_patient,staff.id as staff_id,staff.name,staff.surname')->from('opd_details');
        $this->db->join('patients', 'patients.id = opd_details.patient_id');
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "inner");
        $this->db->where('opd_details.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getopddetailsbill($id)
    {
        $this->db->select('opd_details.*,patients.organisation,patients.patient_name,patients.old_patient,staff.id as staff_id,staff.name,staff.surname')->from('opd_details');
        $this->db->join('patients', 'patients.id = opd_details.patient_id');
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "inner");
        $this->db->where('opd_details.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getipddetailspres($id)
    {
        $this->db->select('ipd_details.*,patients.organisation,patients.old_patient,staff.id as staff_id,staff.name,staff.surname')->from('ipd_details');
        $this->db->join('patients', 'patients.id = ipd_details.patient_id');
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "inner");
        $this->db->where('ipd_details.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function add_diagnosis($data)
    {
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("diagnosis", $data);
        } else {
            $this->db->insert("diagnosis", $data);
            return $this->db->insert_id();
        }
    } 

    public function deleteIpdPatientDiagnosis($id)
    {
        $this->db->where('id', $id)
            ->delete('diagnosis');
        $this->db->where('id', $id)
            ->delete('pathology_report');
        $this->db->where('id', $id)
            ->delete('radiology_report');
    }

    public function add_ipddoctor($data_array)
    {
        $this->db->insert_batch("ipd_doctors", $data_array);
    }

    public function add_ipdprescription($data_array)
    {
        $this->db->insert_batch("ipd_prescription_details", $data_array);
    }

    public function getMaxOPDId()
    {
        $query  = $this->db->select('max(id) as patient_id')->get("opd_details");
        $result = $query->row_array();
        return $result["patient_id"];
    }

    public function getMaxIPDId()
    {
        $query  = $this->db->select('max(id) as ipdid')->get("ipd_details");
        $result = $query->row_array();
        return $result["ipdid"];
    }

    public function search_ipd_patients($searchterm, $active = 'yes', $discharged = 'no', $patient_id = '', $limit = "", $start = "")
    {
        $userdata = $this->customlib->getUserData();
        if ($this->session->has_userdata('hospitaladmin')) {
            $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
            if ($doctor_restriction == 'enabled') {
                if ($userdata["role_id"] == 3) {
                    $this->db->where('ipd_details.cons_doctor', $userdata['id']);
                }
            }
        }

        if (!empty($patient_id)) {
            $this->db->where("patients.id", $patient_id);
        }
        $this->db->select('patients.*,bed.name as bed_name,bed_group.name as bedgroup_name, floor.name as floor_name,ipd_details.date,ipd_details.id as ipdid,ipd_details.credit_limit as ipdcredit_limit,ipd_details.case_type,staff.name,staff.surname
              ')->from('patients');
        $this->db->join('ipd_details', 'patients.id = ipd_details.patient_id', "inner");
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "inner");
        $this->db->join('bed', 'ipd_details.bed = bed.id', "left");
        $this->db->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left");
        $this->db->join('floor', 'floor.id = bed_group.floor', "left");
        $this->db->where('patients.is_active', $active);
        $this->db->where('ipd_details.discharged', $discharged);
        $this->db->group_start();
        $this->db->like('patients.patient_name', $searchterm);
        $this->db->or_like('patients.guardian_name', $searchterm);
        $this->db->group_end();
        $this->db->order_by('ipd_details.id', "desc");
        $query = $this->db->get();
        if (!empty($patient_id)) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function search_ipdpatients($patient_id)
    {
        $userdata = $this->customlib->getUserData();
        if ($this->session->has_userdata('hospitaladmin')) {
            $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
            if ($doctor_restriction == 'enabled') {
                if ($userdata["role_id"] == 3) {
                    $this->db->where('ipd_details.cons_doctor', $userdata['id']);
                }
            }
        }

        if (!empty($patient_id)) {
            $this->db->where("ipd_details.patient_id", $patient_id);
        }
        $this->db->select('ipd_details.*')->from('ipd_details');
        $this->db->order_by('ipd_details.id', "desc");
        $query = $this->db->get();
        if (!empty($patient_id)) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    // ===================== search datatable for IPD ==================================

    public function getAllipdRecord()
    {
        $userdata           = $this->customlib->getUserData();
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];        
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('ipd', 1);
        $custom_field_column_array = array();

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'ipd_details.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }
        if ($doctor_restriction == 'enabled') {
            if ($userdata["role_id"] == 3) {
                $this->datatables->where('ipd_details.cons_doctor', $userdata['id']);
				$this->datatables->or_where('ipd_doctors.consult_doctor', $userdata['id']);
            }
        }
        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $this->datatables
            ->select('patients.*,bed.name as bed_name,bed_group.name as bedgroup_name, floor.name as floor_name,ipd_details.date,ipd_details.id as ipdid,ipd_details.case_reference_id,ipd_details.credit_limit as ipdcredit_limit,ipd_details.case_type,staff.name,staff.surname,staff.employee_id' . $field_variable)
            ->join('patients', 'patients.id = ipd_details.patient_id', "inner")
			->join('ipd_doctors', 'ipd_doctors.ipd_id = ipd_details.id', "left")
            ->join('staff', 'staff.id = ipd_details.cons_doctor', "inner")
            ->join('bed', 'ipd_details.bed = bed.id', "left")
            ->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left")
            ->join('floor', 'floor.id = bed_group.floor', "left")
            ->searchable('patients.patient_name,ipd_details.id,ipd_details.case_reference_id,patients.id,patients.gender,patients.mobileno,staff.name,bed.name' . $custom_field_column)
            ->orderable('patients.patient_name,ipd_details.case_reference_id,ipd_details.id,patients.id,patients.gender,patients.mobileno,staff.name,bed.name' . $custom_field_column)
            ->sort('ipd_details.id', 'desc')
            ->where('patients.is_active', 'yes')
            ->where('ipd_details.discharged', 'no')
            ->group_by('ipd_details.id') 
            ->from('ipd_details');
        return $this->datatables->generate('json');

    }

    public function getAlldischargedRecord()
    {

        $userdata = $this->customlib->getUserData();
        if ($this->session->has_userdata('hospitaladmin')) {
            $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
            if ($doctor_restriction == 'enabled') {
                if ($userdata["role_id"] == 3) {
                    $this->datatables->where('ipd_details.cons_doctor', $userdata['id']);
                }
            }
        }
        $this->datatables
            ->select('patients.*,"0" charges,"0" othercharge,"0" tax,"0" discount,bed.name as bed_name,bed_group.name as bedgroup_name, floor.name as floor_name,ipd_details.date,ipd_details.id as ipdid,ipd_details.credit_limit as ipdcredit_limit,ipd_details.case_type,staff.name,staff.surname,staff.employee_id,ipd_details.case_reference_id,discharge_card.discharge_date
             ')
            ->join('ipd_details', 'patients.id = ipd_details.patient_id', "inner")
            ->join('staff', 'staff.id = ipd_details.cons_doctor', "left")
            ->join('bed', 'ipd_details.bed = bed.id', "left")
            ->join('discharge_card', 'ipd_details.id = discharge_card.ipd_details_id', "inner")
            ->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left")
            ->join('floor', 'floor.id = bed_group.floor', "left")
            ->searchable('patients.patient_name,patients.id,ipd_details.case_reference_id,patients.gender,patients.mobileno,staff.name,ipd_details.date,discharge_card.discharge_date')
            ->orderable('patients.patient_name,patients.id,ipd_details.case_reference_id,patients.gender,patients.mobileno,staff.name,ipd_details.date,discharge_card.discharge_date,"","" ')
            ->sort('ipd_details.id', 'desc')
            ->where('patients.is_active', 'yes')
            ->where('ipd_details.discharged', 'yes')
            ->from('patients');
        return $this->datatables->generate('json');

    }

    public function patientipddetails($patient_id)
    {
        $this->db->select('patients.*,bed_group.name as bedgroup_name, floor.name as floor_name,ipd_details.date,ipd_details.id as ipdid,ipd_details.case_type,ipd_details.ipd_no,staff.name,staff.surname
              ')->from('patients');
        $this->db->join('ipd_details', 'patients.id = ipd_details.patient_id', "inner");
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "inner");
        $this->db->join('bed', 'ipd_details.bed = bed.id', "left");
        $this->db->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left");
        $this->db->join('floor', 'floor.id = bed_group.floor', "left");
        $this->db->where('patients.id', $patient_id);
        $this->db->where('ipd_details.discharged', "yes");
        $this->db->order_by('ipd_details.id', "desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getipdpatientdetails($patient_id)
    {
        $this->db->select('ipd_details.*,patients.patient_name,patients.gender,patients.mobileno,staff.name,staff.surname,staff.employee_id as employee_id,bed_group.name as bedgroup_name,bed.name as bed_name,floor.name as floor_name')->from('ipd_details');
        $this->db->join('patients', 'patients.id = ipd_details.patient_id', "left");
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "left");
        $this->db->join('bed', 'ipd_details.bed = bed.id', "left");
        $this->db->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left");
        $this->db->join('floor', 'floor.id = bed_group.floor', "left");
        $this->db->where('ipd_details.patient_id', $patient_id);
        $this->db->where('ipd_details.discharged', "yes");
        $this->db->order_by('ipd_details.id', "desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getDoctorsipd($ipdid)
    {
        $this->db->select('ipd_doctors.*, staff.name as ipd_doctorname, staff.surname as ipd_doctorsurname,staff.employee_id,roles.id as role_id,staff.image')->from('ipd_doctors');
        $this->db->join('staff', 'staff.id = ipd_doctors.consult_doctor', "left");
        $this->db->join("staff_roles", "staff_roles.staff_id = staff.id", "left");
        $this->db->join("roles", "staff_roles.role_id = roles.id", "left");
        $this->db->where('ipd_doctors.ipd_id', $ipdid);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function add_consultantInstruction($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('consultant_register', $data);
        } else {
            $this->db->insert('consultant_register', $data);
            return $this->db->insert_id();
        }
    }

    public function add_nursenote($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('nurse_note', $data);
        } else {
            $this->db->insert('nurse_note', $data);
            return $this->db->insert_id();
        }
    }

    public function add_nursenotecomment($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('nurse_notes_comment', $data);
        } else {
            $this->db->insert('nurse_notes_comment', $data);
            return $this->db->insert_id();
        }
    }

    public function deleteIpdPatientConsultant($id)
    {
        $query = $this->db->where('id', $id)
            ->delete('consultant_register');
        $this->customfield_model->delete_custom_fieldRecord($id,'ipdconsultinstruction'); 
    }

    public function deleteIpdnursenote($id, $ipdid)
    {
        $query = $this->db->where('id', $id)
            ->delete('nurse_note');
        $this->deletecommentnursenote($id, $ipdid);
        $this->customfield_model->delete_custom_fieldRecord($id,'ipdnursenote'); 
    }

    public function deletecommentnursenote($id, $ipdid)
    {
        $query = $this->db->where('nurse_note_id', $id)->delete('nurse_notes_comment');
    }

    public function deletenursenotecomment($id)
    {
        $query = $this->db->where('id', $id)
            ->delete('nurse_notes_comment');
    }

    public function getPatientConsultant($id, $ipdid)
    {
        $i               = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('ipdconsultinstruction', 1);
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'consultant_register.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable = implode(',', $field_var_array);
        $this->db->select('consultant_register.*,staff.name,staff.surname,staff.employee_id,'. $field_variable);
        $this->db->join('staff', 'staff.id = consultant_register.cons_doctor', "inner");
        $this->db->where("ipd_id", $ipdid);
          $query= $this->db->get("consultant_register");
        return $query->result_array();
    }

    public function getdatanursenote($id, $ipdid)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('ipdnursenote', 1);

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'nurse_note.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable = implode(',', $field_var_array);
        $query          = $this->db->select('nurse_note.*,staff.name,staff.surname,staff.employee_id,cb.name as cb_name,cb.surname as cb_surname,cb.employee_id as cb_employee_id,' . $field_variable)->join('staff', 'staff.id = nurse_note.staff_id', "LEFT")->join('staff cb', 'cb.id = nurse_note.created_by', "LEFT")->where("nurse_note.ipd_id", $ipdid)->get("nurse_note");
        $result         = $query->result_array();

        return $result;
    }
    
    public function getmedicationdetailsbydate_overview($ipdid){
       $query = $this->db->select("medication_report.*,pharmacy.medicine_name,medicine_dosage.dosage as medicine_dosage,unit.unit_name as unit")
            ->join('pharmacy', 'pharmacy.id = medication_report.pharmacy_id', 'left')
            ->join('medicine_dosage', 'medicine_dosage.id = medication_report.medicine_dosage_id', 'left')
            ->join('unit', 'medicine_dosage.units_id = unit.id', 'left')
            ->where("medication_report.ipd_id", $ipdid)
            ->get("medication_report");
       return $result_medication = $query->result_array(); 
    }
	
    public function getmedicationdetailsbydate_opdoverview($opdid){
       $query = $this->db->select("medication_report.*,pharmacy.medicine_name,medicine_dosage.dosage as medicine_dosage,unit.unit_name as unit")
            ->join('pharmacy', 'pharmacy.id = medication_report.pharmacy_id', 'left')
            ->join('medicine_dosage', 'medicine_dosage.id = medication_report.medicine_dosage_id', 'left')
            ->join('unit', 'medicine_dosage.units_id = unit.id', 'left')
            ->where("medication_report.opd_details_id", $opdid)
            ->get("medication_report");
       return $result_medication = $query->result_array();
    }
	
    public function getmedicationdetailsbydate($ipdid)
    {
        $this->db->select('medication_report.pharmacy_id,medication_report.date,pharmacy.medicine_category_id,unit.unit_name as unit');
        $this->db->join('pharmacy', 'pharmacy.id = medication_report.pharmacy_id', 'left');
        $this->db->join('medicine_dosage', 'medicine_dosage.id = medication_report.medicine_dosage_id', 'left');
        $this->db->join('unit', 'unit.id = medicine_dosage.units_id', 'left');
        $this->db->where("medication_report.ipd_id", $ipdid);
        $this->db->group_by('medication_report.date');
        $this->db->order_by('medication_report.date', 'desc');
        $query             = $this->db->get('medication_report');
        $result_medication = $query->result_array();
        
        if (!empty($result_medication)) {
            $i = 0;
            foreach ($result_medication as $key => $value) {
                $date = $value['date'];
                $return = $this->getmedicationbydate($date, $ipdid);

                if (!empty($return)) {
                    foreach ($return as $m_key => $m_value) {
                        $medication                                                                     = array();
                        $result_medication[$i]['dosage'][$date][$m_value['pharmacy_id']]['name']        = $m_value['medicine_name'];
                        $result_medication[$i]['dosage'][$date][$m_value['pharmacy_id']]['dose_list'][] = $m_value;
                    }
                }
                $i++;
            }
        }

        return $result_medication;
    }

    public function deletemedicationByID($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('medication_report');
        $message   = DELETE_RECORD_CONSTANT . " On  Medication Report  id " . $id;
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

            return true;
        }
    }

    public function getmedicationdetailsbydateopd($opdid)
    {
        $this->db->select('medication_report.pharmacy_id,medication_report.date,pharmacy.medicine_category_id,unit.unit_name as unit');
        $this->db->join('pharmacy', 'pharmacy.id = medication_report.pharmacy_id', 'inner');
        $this->db->join('medicine_dosage', 'medication_report.medicine_dosage_id = medicine_dosage.id', 'left');   
        $this->db->join('unit', 'medicine_dosage.units_id = unit.id', 'left');     
        $this->db->where("medication_report.opd_details_id", $opdid);
        $this->db->group_by('medication_report.date');
        $this->db->order_by('medication_report.date', 'desc');
        $query             = $this->db->get('medication_report');
        $result_medication = $query->result_array();
        if (!empty($result_medication)) {
            $i = 0;
            foreach ($result_medication as $key => $value) {
                $date = $value['date'];
                $return = $this->getmedicationbydateopd($date, $opdid);
                if (!empty($return)) {
                    foreach ($return as $m_key => $m_value) {
                        $medication                                                                     = array();
                        $result_medication[$i]['dosage'][$date][$m_value['pharmacy_id']]['name']        = $m_value['medicine_name'];
                        $result_medication[$i]['dosage'][$date][$m_value['pharmacy_id']]['dose_list'][] = $m_value;
                    }
                }
                $i++;
            }
        }

        return $result_medication;

    }

    public function getMaxByipdid($ipd_id)
    {
        $SQL   = 'select max(counted) as max_dose from (SELECT COUNT(*) AS `counted` FROM `medication_report` WHERE medication_report.ipd_id=' . $this->db->escape($ipd_id) . ' group by `pharmacy_id`) t';
        $query = $this->db->query($SQL);
        return $query->row();
    }

    public function getMaxByopdid($opd_id)
    {
        $SQL   = 'select max(counted) as max_dose from (SELECT COUNT(*) AS `counted` FROM `medication_report` inner join pharmacy on pharmacy.id=medication_report.pharmacy_id WHERE medication_report.opd_details_id=' . $this->db->escape($opd_id) . ' group by `pharmacy_id`) t';
        $query = $this->db->query($SQL);
        return $query->row();
    }

    public function getmedicationbydate($date, $ipdid)
    {
        $query = $this->db->select("medication_report.*,pharmacy.medicine_name,pharmacy.medicine_category_id,medicine_dosage.dosage as medicine_dosage,staff.name as staff_name, staff.surname as staff_surname,staff.employee_id as staff_employee_id,unit.unit_name as unit ")
            ->join('staff', 'staff.id = medication_report.generated_by', 'left')
            ->join('pharmacy', 'pharmacy.id = medication_report.pharmacy_id', 'left')
            ->join('medicine_dosage', 'medicine_dosage.id = medication_report.medicine_dosage_id', 'left')
            ->join('unit', 'medicine_dosage.units_id = unit.id', 'left')
            ->where("medication_report.date", $date)
            ->where("medication_report.ipd_id", $ipdid)
            ->get("medication_report");
        $result = $query->result_array();
        return $result;
    }

    public function getmedicationbydateopd($date, $opdid)
    {
        $query = $this->db->select("medication_report.*,pharmacy.medicine_name,pharmacy.medicine_category_id,medicine_dosage.dosage as medicine_dosage,unit.unit_name as unit, staff.name as staff_name, staff.surname as staff_surname,staff.employee_id as staff_employee_id")
            ->join('staff', 'staff.id = medication_report.generated_by', 'left')
            ->join('pharmacy', 'pharmacy.id = medication_report.pharmacy_id', 'left')
            ->join('medicine_dosage', 'medicine_dosage.id = medication_report.medicine_dosage_id', 'left')
            ->join('unit', 'medicine_dosage.units_id = unit.id', 'left')
            ->where("medication_report.date", $date)
            ->where("medication_report.opd_details_id", $opdid)
            ->get("medication_report");
        $result = $query->result_array();
        return $result;
    }

    public function getMedicineDose($medicine_category_id)
    {
        $query = $this->db->select("medicine_dosage.*,unit.unit_name as unit")
            ->where("medicine_dosage.medicine_category_id", $medicine_category_id)
            ->join('unit', 'unit.id = medicine_dosage.units_id')
            ->get("medicine_dosage");
        $result = $query->result_array();
        return $result;
    }

    public function getmedicationbypharmacyid($id)
    {
        $query = $this->db->select("medication_report.*,pharmacy.medicine_name,medicine_dosage.dosage as medicinedosage")
            ->join('pharmacy', 'pharmacy.id = medication_report.pharmacy_id', 'left')
            ->join('medicine_dosage', 'medicine_dosage.id = medication_report.medicine_dosage_id', 'left')
            ->where("medication_report.id", $id)
            ->get("medication_report");
        $result = $query->row_array();
        return $result;
    }

    public function getmedicationbyid($id)
    {
        $query = $this->db->select("medication_report.*,pharmacy.medicine_category_id,pharmacy.medicine_name,medicine_dosage.dosage as medicinedosage")
            ->join('pharmacy', 'pharmacy.id = medication_report.pharmacy_id', 'left')
            ->join('medicine_dosage', 'medicine_dosage.id = medication_report.medicine_dosage_id', 'left')
            ->where("medication_report.id", $id)
            ->get("medication_report");
        $result = $query->row_array();
        return $result;
    }

    public function getnurenotecomment($ipdid, $nid)
    {
        $note_query = $this->db->select("nurse_notes_comment.*,staff.name as staffname ,staff.surname as staffsurname,staff.employee_id")->join('staff', 'staff.id = nurse_notes_comment.comment_staffid', "LEFT")
            ->where("nurse_notes_comment.nurse_note_id", $nid)
            ->get("nurse_notes_comment");
        $result = $note_query->result_array();
        return $result;
    }

    public function getPatientChargeById($id, $patient_id = null)
    {
      $sql    = "SELECT * from (SELECT `charges`.*, `organisations_charges`.`id` as `org_charge_id`, `organisations_charges`.`org_id`, `organisations_charges`.`org_charge`, IFNULL(tax_category.percentage, 0) as `percentage`,IFNULL(patients.organisation_id,0) as patient_org_id,patients.insurance_validity  FROM `charges` JOIN `organisations_charges` ON `organisations_charges`.`charge_id` = `charges`.`id` LEFT join patients on `patients`.`organisation_id` = `organisations_charges`.`org_id` and patients.id= " . $this->db->escape($patient_id) . " LEFT JOIN `tax_category` ON `tax_category`.`id` = `charges`.`tax_category_id` WHERE `charges`.`id` =  ".$this->db->escape($id).") d where d.patient_org_id = (SELECT CASE WHEN COUNT(*) = 1 THEN IFNULL(patients.organisation_id,0) ELSE COUNT(*) END AS 'Updated City' FROM `patients` where patients.id=" . $this->db->escape($patient_id) . ")";
       
        $query  = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $result = $query->row_array();

            return $result;
        }

        return false;
    }

    public function getChargeById($id, $orgid = 0)
    {
        $this->db->select('charges.*,organisations_charges.id as org_charge_id, organisations_charges.org_id, organisations_charges.org_charge,IFNULL(tax_category.percentage,0) as `percentage` ');
        $this->db->join('organisations_charges', 'charges.id = organisations_charges.charge_id and organisations_charges.org_id=' . $orgid, 'LEFT');
        $this->db->join('tax_category', 'tax_category.id = charges.tax_category_id', 'LEFT');
        $this->db->where('charges.id', $id);
        $query = $this->db->get('charges');
        return $query->row_array();
    }

    public function getDataAppoint($id)
    {
        $query = $this->db->where('patients.id', $id)->get('patients');
        return $query->row_array();
    }

    public function search($id)
    {
        $i               = 1;
        $custom_fields   = $this->customfield_model->get_custom_fields('appointment');
        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'appointment.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable = implode(',', $field_var_array);
        $this->db->select('appointment.*,specialist.specialist_name,staff.id as sid,staff.name,staff.surname,staff.employee_id,patients.id as pid,appoint_priority.appoint_priority as priorityname,' . $field_variable);
        $this->db->join('staff', 'appointment.doctor = staff.id', "inner");
        $this->db->join('patients', 'appointment.patient_id = patients.id', 'inner');
        $this->db->join('specialist', 'specialist.id = appointment.specialist', 'left');
        $this->db->join('appoint_priority', 'appoint_priority.id = appointment.priority', "left");
        $this->db->where('`appointment`.`doctor`=`staff`.`id`');
        $this->db->where('appointment.patient_id = patients.id');
        $this->db->where('appointment.patient_id=' . $id);
        $this->db->order_by("appointment.id","desc");
        $query = $this->db->get('appointment');
        return $query->result_array();
    }

    public function getOpdPatient($opd_ipd_no)
    {
        $query = $this->db->select('opd_details.*,opd_details.patient_id,patients.id as pid,patients.patient_name,patients.age,patients.guardian_name,patients.gender')
            ->join('patients', 'opd_details.patient_id = patients.id')
            ->where('opd_details.id', $opd_ipd_no)
            ->get('opd_details');
        return $query->row_array();
    }

    public function getOpdPatientforcertificate($patient_status)
    {
        $this->db->select('opd_details.id,opd_details.patient_id,opd_details.discharged,"opd" as module,patients.id as pid,patients.id as patient_unique_id,patients.patient_name,patients.age,patients.guardian_name,patients.gender,patients.mobileno,staff.name as doctorname,staff.surname');
        $this->db->join('visit_details', 'visit_details.opd_details_id = opd_details.id', "inner");
         $this->db->join('staff', 'staff.id = visit_details.cons_doctor', "left");
        $this->db->join('patients', 'opd_details.patient_id = patients.id', "left");
        $this->db->where('opd_details.discharged', $patient_status);
        $query = $this->db->get('opd_details');      
        return $query->result_array();
    }

    public function getAllOpdPatientforcertificate($patient_status)
    {
        if($patient_status !=""){
              $this->datatables->where('opd_details.discharged', $patient_status);
        }
        $this->datatables
            ->select('visit_details.id as checkup_id,opd_details.id,patients.id as patient_id,opd_details.discharged,"opd" as module,patients.patient_name,patients.guardian_name,patients.gender,patients.mobileno,staff.name as doctorname,staff.surname')
            ->join('visit_details', 'visit_details.opd_details_id = opd_details.id', 'inner')
            ->join('staff', 'staff.id = visit_details.cons_doctor', 'left')
            ->join('patients', 'opd_details.patient_id = patients.id', 'left')
            ->searchable('patients.patient_name,patients.gender,patients.mobileno,opd_details.discharged')
            ->orderable('null,patients.id,patients.patient_name,patients.gender,patients.mobileno,opd_details.discharged')
            ->where('patients.is_active', 'yes')
            ->sort('opd_details.id', 'desc')            
            ->from('opd_details');
        return $this->datatables->generate('json');
    }

    public function getIpdPatient($opd_ipd_no)
    {
        $query = $this->db->select('ipd_details.patient_id,ipd_details.ipd_no,patients.id as pid,patients.patient_name,patients.age,patients.guardian_name,patients.guardian_address,patients.admission_date,patients.gender,staff.name as doctorname,staff.surname')
            ->join('patients', 'ipd_details.patient_id = patients.id')
            ->join('staff', 'staff.id = ipd_details.cons_doctor', "inner")
            ->where('ipd_no', $opd_ipd_no)
            ->get('ipd_details');
        return $query->row_array();
    }

    public function getPatientbyipdid($ipdid)
    {
        $query = $this->db->select('ipd_details.patient_id,patients.id as pid,patients.patient_name')
            ->join('patients', 'ipd_details.patient_id = patients.id', 'left')
            ->where('ipd_details.id', $ipdid)
            ->get('ipd_details');
        return $query->row_array();
    }

    public function getIpdPatientforcertificate($patient_status)
    {
        $this->db->select('ipd_details.id,ipd_details.patient_id,ipd_details.ipd_no,ipd_details.discharged,"ipd" as module,patients.id as pid,patients.patient_unique_id,patients.patient_name,patients.age,patients.guardian_name,patients.guardian_address,patients.admission_date,patients.gender,patients.guardian_address,patients.patient_unique_id,patients.mobileno,staff.name as doctorname,staff.surname');
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "inner");
        $this->db->join('patients', 'ipd_details.patient_id = patients.id', "left");
        $this->db->where('ipd_details.discharged', $patient_status);
        $query = $this->db->get('ipd_details');
        return $query->result_array();
    }

    public function getAllIpdPatientforcertificate($patient_status)
    {
        if($patient_status !=""){
           $this->datatables->where('ipd_details.discharged', $patient_status);
        }

        $this->datatables
            ->select('ipd_details.id,ipd_details.patient_id,ipd_details.discharged,"ipd" as module,patients.id as pid,patients.patient_name,patients.age,patients.guardian_name,patients.gender,patients.mobileno,staff.name as doctorname,staff.surname')
            ->join('staff', 'staff.id = ipd_details.cons_doctor', 'inner')
            ->join('patients', 'ipd_details.patient_id = patients.id', 'left')
            ->searchable('ipd_details.id,patient_name,patients.gender,patients.mobileno,ipd_details.discharged')
            ->orderable('null,ipd_details.id,patient_name,patients.gender,patients.mobileno,ipd_details.discharged')
            ->sort('ipd_details.id', 'desc')
            ->where('patients.is_active', 'yes')
            ->from('ipd_details');
        return $this->datatables->generate('json');
    }

    public function getAppointmentDate()
    {
        $query = $this->db->select('opd_details.appointment_date')->get('opd_details');
    }

    public function deleteOPD($id,$patient_id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('opd_details');
		
		$this->db->delete("transactions", array("opd_id" => $id)); 

        $this->db->select('*');
        $this->db->where(['patient_id'=>$patient_id,'discharged'=>'no']);
        $query = $this->db->get('opd_details');
        $total_visits_remaining = $query->num_rows();

        $message   = DELETE_RECORD_CONSTANT . " On  OPD  id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id,'opd'); 
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $total_visits_remaining;
        }
    }

    public function deleteIpddoctor($doctoripd_id)
    {
        $this->db->where("id", $doctoripd_id)->delete("ipd_doctors");
    }

    public function deleteOPDPatient($id)
    {
        $this->db->where("id", $id)->delete("opd_details");
    }
 
    public function deletePatient($id)
    {
        $query = $this->db->select('bed.id')
            ->join('ipd_details', 'ipd_details.bed = bed.id')
            ->where("ipd_details.patient_id", $id)->where("ipd_details.discharged", 'no')->get('bed');
        $result = $query->row_array();
        if(!empty($result)){
            $bed_id = $result["id"];
			if ($bed_id) {
				$this->db->where("id", $bed_id)->update('bed', array('is_active' => 'yes'));
				$this->db->where("patient_id", $id)->delete("ipd_details");  
			}
        }
        
        $this->customfield_model->delete_custom_fieldRecord($id,'patient'); 
        $this->db->where("id", $id)->delete("patients");
    }

    public function getCharges($ipd_id)
    {
        $query = $this->db->select("sum(apply_charge) as charge")->where("ipd_id", $ipd_id)->get("patient_charges");
        return $query->row_array();
    }

    public function getOPDCharges($patient_id, $opdid = '')
    {
        $query = $this->db->select("sum(apply_charge) as charge")->join('opd_details', 'opd_details.id=patient_charges.opd_id')->where("details.patient_id", $patient_id)->where("opd_details_id", $opdid)->get("patient_charges");
        return $query->row_array();
    }

    public function getOPDvisitCharges($patient_id, $opdid = '')
    {
        $query = $this->db->select("sum(visit_details.amount) as vamount")->join('opd_details', 'opd_details.id=visit_details.opd_details_id')->where("opd_details.patient_id", $patient_id)->where("opd_details_id", $opdid)->get("visit_details");
        return $query->row_array();
    }

    public function getOPDbill($patient_id, $opdid = '')
    {
        $query = $this->db->select("sum(amount) as billamount")->join('opd_details', 'opd_details.id = transactions.opd_id', 'left')->where("opd_details.patient_id", $patient_id)->where("transactions.opd_id", $opdid)->get("transactions");
        return $query->row_array();
    }

    public function getPayment($patient_id, $ipdid = '')
    {
        $query = $this->db->select("sum(transactions.amount) as payment")->where("ipd_id", $ipdid)->get("transactions");
        return $query->row_array();
    }

    public function getopdPayment($patient_id, $opdid = '')
    {
        $query = $this->db->select("sum(paid_amount) as opdpayment")->join('opd_details', 'opd_details.id=opd_payment.opd_details_id')->where("opd_details.patient_id", $patient_id)->where("opd_details_id", $opdid)->get("opd_payment");
        return $query->row_array();
    }

    public function patientCredentialReport()
    {
        $query = $this->db->select('patients.*,users.id as uid,users.user_id,users.username,users.password')
            ->join('users', 'patients.id = users.user_id')->where("users.is_active", 'yes')
            ->get('patients');
        return $query->result_array();
    }

    public function getPaymentDetail($patient_id)
    {
        $SQL   = 'select patient_charges.amount_due,payment.amount_deposit from (SELECT sum(paid_amount) as `amount_deposit` FROM `payment` WHERE patient_id=' . $this->db->escape($patient_id) . ') as payment ,(SELECT sum(apply_charge) as `amount_due` FROM `patient_charges` WHERE patient_id=' . $this->db->escape($patient_id) . ') as patient_charges';
        $query = $this->db->query($SQL);
        return $query->row();
    }

    public function getPaymentDetailpatient($ipd_id)
    {
        $SQL   = 'select patient_charges.amount_due,payment.amount_deposit from (SELECT sum(paid_amount) as `amount_deposit` FROM `payment` WHERE ipd_id=' . $this->db->escape($ipd_id) . ') as payment ,(SELECT sum(apply_charge) as `amount_due` FROM `patient_charges` WHERE ipd_id=' . $this->db->escape($ipd_id) . ') as patient_charges';
        $query = $this->db->query($SQL);
        return $query->row();
    }

    public function getOpdPaymentDetailpatient($opd_id)
    {
        $SQL   = 'select patient_charges.amount_due,opd_payment.amount_deposit from (SELECT sum(paid_amount) as `amount_deposit` FROM `opd_payment` WHERE opd_details_id=' . $this->db->escape($opd_id) . ') as opd_payment ,(SELECT sum(apply_charge) as `amount_due` FROM `patient_charges` WHERE opd_details_id=' . $this->db->escape($opd_id) . ') as patient_charges';
        $query = $this->db->query($SQL);
        return $query->row();
    }

    public function getIpdBillDetails($id, $ipdid)
    {
        $query = $this->db->select("transactions.*")->where("transactions.ipd_id", $ipdid)->get("transactions");
        return $query->row_array();
    }

    public function getDepositAmountBetweenDate($start_date, $end_date)
    {
        $opd_query        = $this->db->select('*')->get('opd_details');
        $bloodbank_query  = $this->db->select('*')->get('blood_issue');
        $pharmacy_query   = $this->db->select('*')->get('pharmacy_bill_basic');
        $opd_result       = $opd_query->result();
        $bloodbank_result = $bloodbank_query->result();
        $result_value     = $opd_result;
        $return_array     = array();
        if (!empty($result_value)) {
            $st_date = strtotime($start_date);
            $ed_date = strtotime($end_date);
            foreach ($result_value as $key => $value) {
                $return = $this->findObjectById($result_value, $st_date, $ed_date);
                if (!empty($return)) {
                    foreach ($return as $r_key => $r_value) {
                        $a                    = array();
                        $a['amount']          = $r_value->amount;
                        $a['date']            = $r_value->appointment_date;
                        $a['amount_discount'] = 0;
                        $a['amount_fine']     = 0;
                        $a['description']     = '';
                        $a['payment_mode']    = $r_value->payment_mode;
                        $a['inv_no']          = $r_value->patient_id;
                        $return_array[]       = $a;
                    }
                }
            }
        }

        return $return_array;
    }

    public function findObjectById($array, $st_date, $ed_date)
    {
        $sarray = array();
        for ($i = $st_date; $i <= $ed_date; $i += 86400) {
            $find = date('Y-m-d', $i);
            foreach ($array as $row_key => $row_value) {
                $appointment_date = date("Y-m-d", strtotime($row_value->appointment_date));
                if ($appointment_date == $find) {
                    $sarray[] = $row_value;
                }
            }
        }
        return $sarray;
    }

    public function getPathologyEarning($search = '')
    {
        if (!empty($search)) {
            $this->db->where($search);
        }
        $query = $this->db->select('sum(pathology_report.apply_charge) as amount')
            ->join('pathology', 'pathology.charge_id = charges.id')
            ->join('pathology_report', 'pathology_report.pathology_id = pathology.id')
            ->get('charges');
        $result = $query->row_array();
        return $result["amount"];
    }

    public function getRadiologyEarning($search = '')
    {
        if (!empty($search)) {
            $this->db->where($search);
        }

        $query = $this->db->select('sum(radiology_report.apply_charge) as amount')
            ->join('radio', 'radio.charge_id = charges.id')
            ->join('radiology_report', 'radiology_report.radiology_id = radio.id')
            ->get('charges');
        $result = $query->row_array();
        return $result["amount"];
    }

    public function getOTEarning($search = '')
    {
        $search_arr = array();
        foreach ($search as $key => $value) {
            $key              = $key;
            $search_arr[$key] = $value;
        }
        if (!empty($search_arr)) {
            $this->db->where($search_arr);
        }

        $query = $this->db->select('sum(operation_theatre.apply_charge) as amount')
            ->join('operation_theatre', 'operation_theatre.charge_id = charges.id')
            ->join('patients', 'operation_theatre.patient_id = patients.id', 'inner')
            ->where('patients.is_active', 'yes')
            ->get('charges');
        $result = $query->row_array();
        return $result["amount"];
    }

    public function deleteIpdPatient($ipdid)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
		
		$query = $this->db->select('bed.id')
            ->join('ipd_details', 'ipd_details.bed = bed.id')
            ->where("ipd_details.id", $ipdid)->where("ipd_details.discharged", 'no')->get('bed');
        $result = $query->row_array();
		 
        if(!empty($result)){
            $bed_id = $result["id"];
			if ($bed_id) {
				$this->db->where("id", $bed_id)->update('bed', array('is_active' => 'yes'));
				 
			}
        }
		
		$this->db->where('id', $ipdid);
        $this->db->delete('ipd_details');		

        $message   = DELETE_RECORD_CONSTANT . " On IPD id " . $ipdid;
        $action    = "Delete";
        $record_id = $ipdid;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($ipdid,'ipd'); 
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

    public function getBillInfo($id)
    {
        $query = $this->db->select('staff.name,staff.surname,staff.employee_id,ipd_billing.date as discharge_date')
            ->join('ipd_billing', 'staff.id = ipd_billing.generated_by')
            ->where('ipd_billing.patient_id', $id)
            ->get('staff');
        $result = $query->row_array();
        return $result;
    }

    public function bulkdelete($patients)
    {
        if (!empty($patients)) {

           $this->db->trans_start();
            $update_array = array('is_active'=>'yes');
            $result= $this->db->select('bed')
                     ->where_in('patient_id', $patients)
                     ->get('ipd_details')
                     ->result_array();
          
			foreach($result as $row){                
				$this->db->where('id',$row['bed']);
				$this->db->update('bed',$update_array);
			}

            $this->db->where_in('id', $patients);
            $this->db->delete('patients');
            //delete from users
            $this->db->where_in('user_id', $patients);
            $this->db->where_in('role', 'patient');
            $this->db->delete('users');
            //delete from custom_field_value
            foreach ($patients as $key => $value) {
              $this->customfield_model->delete_custom_fieldRecord($value,'patient'); 
            }           
         
            $sql_patients = "DELETE from users WHERE id in (SELECT id from (SELECT users.*  FROM `users`) as a WHERE a.user_id IS NULL)";
            $query      = $this->db->query($sql_patients);

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function getBillstatus($id)
    {
        $query = $this->db->select('visit_details.*,sum(transactions.amount) as amount,patients.id')
            ->join('opd_details', 'opd_details.id = transactions.opd_id', "left")
            ->join('patients', 'patients.id=opd_details.patient_id', 'left')
            ->join('visit_details', 'visit_details.opd_details_id = opd_details.id', "left")       
            ->get('transactions');
        $result = $query->row_array();
        return $result;
    }

    public function getStatus($id)
    {
        $query  = $this->db->where("id", $id)->get("patients");
        $result = $query->row_array();
        return $result;
    }

    public function searchPatientNameLike($searchterm)
    {
        $this->db->select('patients.*, patients.id as patient_unique_id')->from('patients');
        $this->db->group_start();
        $this->db->like('patients.patient_name', $searchterm);
        $this->db->group_end();
        $this->db->where('patients.is_active', 'yes');
        $this->db->order_by('patients.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getPatientEmail()
    {
        $query = $this->db->select("patients.email,patients.id,patients.mobileno,patients.app_key")
            ->join("users", "patients.id = users.user_id")
            ->where("users.role", "patient")
            ->where("patients.is_active", "yes")
            ->get("patients");
        return $query->result_array();
    }

    public function updatebed($data)
    {
        $this->db->where('ipd_no', $data["ipd_no"])
            ->update('ipd_details', $data);
    }

    public function getVisitDetails($opdid,$patient_panel = null)
    {
        $i = 1;
        if($patient_panel == 'patient'){
            $custom_fields = $this->customfield_model->get_custom_fields('opdrecheckup', '','','', 1);
        }else{
            $custom_fields = $this->customfield_model->get_custom_fields('opdrecheckup', 1);
        }
        
        $custom_field_column_array = array();

        $field_var_array = array();
       if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name.'`');
                $this->datatables->join('custom_field_values as '.$tb_counter,'visit_details.id = '.$tb_counter.'.belong_table_id AND '.$tb_counter.'.custom_field_id = '.$custom_fields_value->id,"left");
                $i++;
            }
        }

        $field_variable = (empty($field_var_array))? "": ",".implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array))? "": ",".implode(',', $custom_field_column_array);
        $query = $this->db->select('visit_details.*,organisations_charges.org_charge,opd_details.id as opdid,staff.name,staff.surname,staff.employee_id,antenatal_examine.visit_details_id'.$field_variable)
            ->join('opd_details', 'opd_details.id = visit_details.opd_details_id')
            ->join('organisations_charges', 'organisations_charges.id = visit_details.organisation_id',"left")
            ->join('patients', 'opd_details.patient_id = patients.id')
            ->join('staff', 'visit_details.cons_doctor = staff.id')
            ->join('antenatal_examine', 'antenatal_examine.visit_details_id = visit_details.id',"left")
            ->where(array('visit_details.opd_details_id' => $opdid))
            ->get('visit_details');

        $result = $query->result_array();

        $i      = 0;
        foreach ($result as $key => $value) {

            $visit_id = $value["id"];
            $check = $this->db->where("visit_details_id", $visit_id)->get('ipd_prescription_basic');

            if ($check->num_rows() > 0) {
                $result[$i]['prescription'] = 'yes';
            } else {
                $result[$i]['prescription'] = 'no';
                $userdata                   = $this->customlib->getUserData();
                if ($this->session->has_userdata('hospitaladmin')) {
                    $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                    if ($doctor_restriction == 'enabled') {
                        if ($userdata["role_id"] == 3) {
                            if ($userdata["id"] == $value["staff_id"]) {

                            } else {
                                $result[$i]['prescription'] = 'not_applicable';
                            }
                        }
                    }
                }
            }
            $i++;
        }
        return $result;
    }

    public function getVisitdataDetails($visitid)
    {
        $query = $this->db->select('visit_details.*,patients.as_of_date,blood_bank_products.name as blood_group_name,opd_details.id as opdid,patients.id as patientid,patients.patient_name,patients.age,patients.month,patients.day,patients.guardian_name,patients.gender,patients.month,patients.mobileno,patients.image,patients.email,patients.dob,patients.address,patients.marital_status,patients.blood_group,patients.identification_number,patients.known_allergies as any_known_allergies,patients.note,patients.insurance_id,patients.insurance_validity,staff.name,staff.surname,staff.employee_id,organisation.organisation_name,patients.organisation_id')
            ->join('opd_details', 'opd_details.id = visit_details.opd_details_id', 'left')
            ->join('patients', 'opd_details.patient_id = patients.id', 'left')
            ->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left')
            ->join('staff', 'visit_details.cons_doctor = staff.id', 'left')
            ->join('organisation', 'organisation.id = patients.organisation_id', 'left')
            ->where(array('visit_details.opd_details_id' => $visitid))             
            ->get('visit_details');
        return $query->row_array();
    }

    public function getvisitDetailsByVisitId($visitid)
    {
        $query = $this->db->select('visit_details.*,patients.as_of_date,blood_bank_products.name as blood_group_name,opd_details.id as opdid,patients.id as patientid,patients.patient_name,patients.age,patients.month,patients.day,patients.guardian_name,patients.gender,patients.month,patients.mobileno,patients.image,patients.email,patients.dob,patients.address,patients.marital_status,patients.blood_group,patients.identification_number,patients.known_allergies as any_known_allergies,patients.note,patients.insurance_id,patients.insurance_validity,staff.name,staff.surname,staff.employee_id,organisation.id,organisation.organisation_name')
            ->join('opd_details', 'opd_details.id = visit_details.opd_details_id', 'left')
            ->join('patients', 'opd_details.patient_id = patients.id', 'left')
            ->join('organisation', 'organisation.id = patients.organisation_id', 'left')
            ->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left')
            ->join('staff', 'visit_details.cons_doctor = staff.id', 'left')             
            ->where(array('visit_details.id' => $visitid)) 
            ->get('visit_details');
        return $query->row_array();
    }

    public function getpatientDetailsByVisitId($id, $visitid)
    {
        $query = $this->db->select('visit_details.*,visit_details.amount as apply_charge, opd_details.id as opdid, staff.name,staff.surname,patients.age,patients.month,patients.patient_name,patients.gender,patients.email,patients.mobileno,patients.address,patients.marital_status,patients.blood_group,patients.dob,patients. patient_unique_id')
            ->join('opd_details', 'visit_details.opd_details_id = opd_details.id')
            ->join('patients', 'opd_details.patient_id = patients.id')
            ->join('staff', 'opd_details.cons_doctor = staff.id')
            ->where(array('opd_details.patient_id' => $id, 'visit_details.id' => $visitid))
            ->get('visit_details');
        $result = $query->row_array();
        if (!empty($result)) {
            $generated_by = $result["generated_by"];
            $staff_query  = $this->db->select("staff.name,staff.surname")
                ->where("staff.id", $generated_by)
                ->get("staff");
            $staff_result               = $staff_query->row_array();
            $result["generated_byname"] = $staff_result["name"] . " " . $staff_result["surname"];
        }
        return $result;
    }

    public function addvisitDetails($opd_data)
    {
        if (isset($opd_data["id"])) {
            $this->db->where("id", $opd_data["id"])->update("visit_details", $opd_data);
        } else {
            $this->db->insert("visit_details", $opd_data);
        }        
    }

    public function getopdvisitrecheckup($id, $opdid)
    {
        $table_name = '(SELECT visit_details.id as id,visit_details.opd_details_id as opdid,patients.id as patientid,visit_details.appointment_date as appointmentdate,visit_details.cons_doctor as doctorid,visit_details.refference as reference,visit_details.symptoms as symptoms FROM visit_details inner join opd_details on opd_details.id=visit_details.opd_details_id inner join patients on patients.id=opd_details.patient_id) AS recheckup';
        $select     = 'recheckup.*,patients.patient_name,staff.name,staff.surname';
        $join       = array('LEFT JOIN patients ON recheckup.patientid = patients.id', 'LEFT JOIN staff ON recheckup.doctorid = staff.id');
        $where      = array("recheckup.patientid=" . $id, "recheckup.opdid=" . $opdid);
        $query      = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where);
        $sql        = $this->db->query($query);
        $result     = $sql->result_array();

        $i = 0;
        foreach ($result as $key => $value) {

            $visit_id = $value["id"];

            $check = $this->db->where("visit_details_id", $visit_id)->get('opd_prescription_basic');

            if ($check->num_rows() > 0) {
                $result[$i]['prescription'] = 'yes';
            } else {
                $result[$i]['prescription'] = 'no';
                $userdata                   = $this->customlib->getUserData();
                if ($this->session->has_userdata('hospitaladmin')) {
                    $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                    if ($doctor_restriction == 'enabled') {
                        if ($userdata["role_id"] == 3) {
                            if ($userdata["id"] == $value["staff_id"]) {

                            } else {
                                $result[$i]['prescription'] = 'not_applicable';
                            }
                        }
                    }
                }
            }
            $i++;
        }

        return $result;

    }

    // ===================== search datatable for OPD ==================================

    public function search_datatable($id, $opdid)
    {
        $table_name = '(SELECT visit_details.id as id,visit_details.opd_details_id as opdid,patients.id as patientid,visit_details.appointment_date as appointmentdate,visit_details.cons_doctor as doctorid,visit_details.refference as reference,visit_details.symptoms as symptoms FROM visit_details join opd_details on opd_details.id=visit_details.opd_details_id join patients on patients.id=opd_details.patient_id) AS recheckup';
        $select     = 'recheckup.*,patients.patient_name,staff.name,staff.surname,staff.employee_id';
        $join       = array('LEFT JOIN patients ON recheckup.patientid = patients.id', 'LEFT JOIN staff ON recheckup.doctorid = staff.id');
        $where      = array("recheckup.patientid=" . $id, "recheckup.opdid=" . $opdid);
        $query      = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where);

        if (!empty($_POST['search']['value'])) {
            // if there is a search parameter
            $counter = true;
            $this->db->group_start();
            foreach ($this->column_search as $colomn_key => $colomn_value) {
                if ($counter) {
                    $this->db->like($colomn_value, $_POST['search']['value']);
                    $counter = false;
                }
                $this->db->or_like($colomn_value, $_POST['search']['value']);
            }
            $this->db->group_end();

        }
        $this->db->limit($_POST['length'], $_POST['start']);
        if (!isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        }
        $sql    = $this->db->query($query);
        $result = $sql->result_array();

        $i = 0;
        foreach ($result as $key => $value) {
            $opd_id   = $value["opdid"];
            $visit_id = $value["id"];
            $check    = $this->db->where("visit_details_id", $visit_id)->get('opd_prescription_basic');

            if ($check->num_rows() > 0) {
                $result[$i]['prescription'] = 'yes';
            } else {
                $result[$i]['prescription'] = 'no';
                $userdata                   = $this->customlib->getUserData();
                if ($this->session->has_userdata('hospitaladmin')) {
                    $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                    if ($doctor_restriction == 'enabled') {
                        if ($userdata["role_id"] == 3) {
                            if ($userdata["id"] == $value["staff_id"]) {

                            } else {
                                $result[$i]['prescription'] = 'not_applicable';
                            }
                        }
                    }
                }
            }
            $i++;
        }

        return $result;
    }

    public function search_datatable_count($id, $opdid)
    {
        $table_name = '(SELECT visit_details.id as id,visit_details.opd_details_id as opdid,patients.id as patientid,visit_details.appointment_date as appointmentdate,visit_details.cons_doctor as doctorid,visit_details.refference as reference,visit_details.symptoms as symptoms FROM visit_details join opd_details on opd_details.id=visit_details.opd_details_id join patients on patients.id=opd_details.patient_id) AS recheckup';
        $select     = 'recheckup.*,patients.patient_name,staff.name,staff.surname';
        $join       = array('LEFT JOIN patients ON recheckup.patientid = patients.id', 'LEFT JOIN staff ON recheckup.doctorid = staff.id');
        $where      = array("recheckup.patientid=" . $id, "recheckup.opdid=" . $opdid);
        $query      = "select " . $select . " from " . $table_name . " " . implode(" ", $join) . " where " . implode(" and ", $where);

        if (!empty($_POST['search']['value'])) {
            // if there is a search parameter
            $counter = true;
            $this->db->group_start();
            foreach ($this->column_search as $colomn_key => $colomn_value) {
                if ($counter) {
                    $this->db->like($colomn_value, $_POST['search']['value']);
                    $counter = false;
                }
                $this->db->or_like($colomn_value, $_POST['search']['value']);
            }
            $this->db->group_end();
        }

        $sql          = $this->db->query($query);
        $total_result = $this->db->count_all_results();
        return $total_result;
    }

    public function deleteVisit($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('visit_details');
        $message   = DELETE_RECORD_CONSTANT . " On  OPD Visit id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        $this->customfield_model->delete_custom_fieldRecord($id,'opdrecheckup'); 
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            // return $return_value;
        }

    }

    public function printVisitDetails($opdid)
    {
        $query = $this->db->select("patients.*,organisation.organisation_name,visit_details.id as opdid,visit_details.appointment_date,visit_details.symptoms,visit_details.case_type,visit_details.casualty,visit_details.note_remark,staff.name,staff.surname,transactions.amount as paid_amount")
            ->join('opd_details', 'patients.id = opd_details.patient_id')
            ->join('visit_details', 'visit_details.opd_details_id = opd_details.id', 'LEFT')
            ->join('staff', 'staff.id = visit_details.cons_doctor', 'LEFT')
            ->join('organisation', 'organisation.id = visit_details.organisation_id', 'left')
            ->join('patient_charges', 'patient_charges.opd_id = opd_details.id', 'left')
            ->join('transactions', 'transactions.opd_id = opd_details.id', 'left')      
            ->where("opd_details.id", $opdid)
            ->get("patients");

        return $query->row_array();
    }

    public function checkmobileemail($mobileno, $email)
    {
        $query = $this->db->query('select * from patients where mobileno= "' . $mobileno . '" and  email="' . $email . '"');
        $result = $query->result_array();
        if (!empty($result)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function checkmobilenumber($mobileno)
    {
        $query  = $this->db->query('select * from patients where mobileno= "' . $mobileno . '" ');
        $result = $query->result_array();
        if (!empty($result)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function checkemail($email)
    {
        $query  = $this->db->query('select * from patients where email= "' . $email . '"');
        $result = $query->result_array();
        if (!empty($result)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delete_ipddoctor($ipdid)
    {
        $this->db->where('ipd_id', $ipdid)
            ->delete('ipd_doctors');
    }    
    
    public function getDetailsByCaseId($case_id,$type = null)
    {
        $patient_details=array();
        $opd_details=array();
        $ipd_details=array();
		if($type != 'ipd'){
			$opd_details=$this->getDetailsopdByCaseId($case_id);
		}else{
			$ipd_details=$this->getDetailsipdByCaseId($case_id);
		}   
        
 
       if(!empty($ipd_details)){

            $patient_details['patient_id'] =$ipd_details['patient_id'];
            $patient_details['patient_name'] =$ipd_details['patient_name'];
            $patient_details['dob'] =$ipd_details['dob'];
            $patient_details['age'] =$ipd_details['age'];
            $patient_details['month'] =$ipd_details['month'];
            $patient_details['day'] =$ipd_details['day'];
            $patient_details['image'] =$ipd_details['image'];
            $patient_details['mobileno'] =$ipd_details['mobileno'];
            $patient_details['email'] =$ipd_details['email'];
            $patient_details['gender'] =$ipd_details['gender'];
            $patient_details['blood_group'] =$ipd_details['blood_group'];
            $patient_details['address'] =$ipd_details['address'];
            $patient_details['guardian_name'] =$ipd_details['guardian_name'];
            $patient_details['is_dead'] =$ipd_details['is_dead'];
            $patient_details['insurance_id'] =$ipd_details['insurance_id'];
            $patient_details['insurance_validity'] =$ipd_details['insurance_validity'];
            $patient_details['discharged'] =$ipd_details['discharged'];
            $patient_details['date'] =$ipd_details['date'];
            $patient_details['appointment_date'] =$ipd_details['date'];                   
            $patient_details['credit_limit'] =$ipd_details['credit_limit'];
            $patient_details['bed_name'] =$ipd_details['bed_name'];
            $patient_details['bed_id'] =$ipd_details['bed_id'];
            $patient_details['bedgroup_name'] =$ipd_details['bedgroup_name'];
            $patient_details['floor_name'] =$ipd_details['floor_name'];
            $patient_details['ipdid'] =$ipd_details['ipdid']; 
            $patient_details['opdid'] =''; 
            $patient_details['organisation_name'] =$ipd_details['organisation_name'];
            $patient_details['as_of_date'] =$ipd_details['as_of_date'];
       }       

       if(!empty($opd_details)){
    
            $patient_details['patient_id'] =$opd_details['patient_id'];
            $patient_details['patient_name'] =$opd_details['patient_name'];
            $patient_details['dob'] =$opd_details['dob'];
            $patient_details['age'] =$opd_details['age'];
            $patient_details['month'] =$opd_details['month'];
            $patient_details['day'] =$opd_details['day'];
            $patient_details['image'] =$opd_details['image'];
            $patient_details['mobileno'] =$opd_details['mobileno'];
            $patient_details['email'] =$opd_details['email'];
            $patient_details['gender'] =$opd_details['gender'];
            $patient_details['blood_group'] =$opd_details['blood_group'];
            $patient_details['address'] =$opd_details['address'];
            $patient_details['guardian_name'] =$opd_details['guardian_name'];
            $patient_details['is_dead'] =$opd_details['is_dead'];
            $patient_details['insurance_id'] =$opd_details['insurance_id'];
            $patient_details['insurance_validity'] =$opd_details['insurance_validity'];
            $patient_details['discharged'] =$opd_details['discharged'];
            $patient_details['date'] =$opd_details['appointment_date'];
            $patient_details['appointment_date'] =$opd_details['appointment_date'];      
            $patient_details['opdid'] =$opd_details['opdid']; 
            $patient_details['ipdid'] =''; 
            $patient_details['organisation_name'] =$opd_details['organisation_name'];
            $patient_details['as_of_date'] =$opd_details['as_of_date'];

       }
       return $patient_details;
    }

	public function getopdAmountByCaseId($case_id)
    {
		$query="SELECT opd_details.*,IFNULL((SELECT SUM(amount) FROM `patient_charges` WHERE opd_id=opd_details.id),0) as `charge_amounts` ,IFNULL((SELECT SUM(amount)  FROM `transactions` WHERE `patient_id` = opd_details.patient_id and section= 'OPD'),0) as `total_payments` FROM `opd_details` WHERE case_reference_id =".$this->db->escape($case_id);
        $result       = $this->db->query($query);
        
        return $result->row();
    }

	public function getipdAmountByCaseId($case_id)
    {
		$query="SELECT ipd_details.*,IFNULL((SELECT SUM(amount) FROM `patient_charges` WHERE ipd_id=ipd_details.id),0) as `charge_amounts` ,IFNULL((SELECT SUM(amount)  FROM `transactions` WHERE `patient_id` = ipd_details.patient_id and section= 'IPD'),0) as `total_payments` FROM `ipd_details` WHERE case_reference_id =".$this->db->escape($case_id);
        $result      = $this->db->query($query);
        
        return $result->row();
    }

    public function getDetailsopdByCaseId($case_id)
    {
        return $this->db->select('patients.id as patient_id,patients.patient_name,patients.dob,patients.age,patients.month,patients.day,patients.image,patients.mobileno,patients.email,patients.gender,patients.blood_group,patients.address,patients.guardian_name,patients.is_dead,patients.insurance_id,patients.insurance_validity,opd_details.id as opdid,opd_details.discharged,visit_details.appointment_date,organisation.organisation_name,patients.as_of_date')->from('patients')
            ->join('opd_details', 'opd_details.patient_id=patients.id', 'inner')
            ->join('organisation', 'organisation.id=patients.organisation_id', 'left')
            ->join('visit_details', 'visit_details.opd_details_id=opd_details.id', 'inner')
            ->where('opd_details.case_reference_id', $case_id)
            ->get()
            ->row_array();
    } 

    public function getDetailsipdByCaseId($case_id)
    {
        return $this->db->select('patients.id as patient_id,patients.patient_name,patients.dob,patients.age,patients.month,patients.day,patients.image,patients.mobileno,patients.email,patients.gender,patients.blood_group,patients.address,patients.guardian_name,patients.is_dead,patients.insurance_id,patients.insurance_validity,ipd_details.id as ipdid,`ipd_details`.`date`,ipd_details.discharged,ipd_details.credit_limit,ipd_details.date,bed.name as bed_name,bed.id as bed_id,bed_group.name as bedgroup_name,floor.name as floor_name,organisation.organisation_name,patients.as_of_date')->from('patients')
            ->join('ipd_details', 'ipd_details.patient_id=patients.id', 'inner')
            ->join('organisation', 'organisation.id=patients.organisation_id', 'left')
            ->join('bed', 'ipd_details.bed = bed.id', "left")
            ->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left")
            ->join('floor', 'floor.id = bed_group.floor', "left")
            ->where('ipd_details.case_reference_id', $case_id)
            ->get()
            ->row_array();
    }

    public function getmotherByCaseId($case_id)
    {
        return $this->db->select('patients.*,ipd_details.id as ipdid,opd_details.id as opdid,`ipd_details`.`date`,ipd_details.discharged,ipd_details.credit_limit,ipd_details.date,bed.name as bed_name,bed.id as bed_id,bed_group.name as bedgroup_name,floor.name as floor_name,visit_details.appointment_date')->from('patients')
                ->join('ipd_details', 'ipd_details.patient_id=patients.id', 'left')
                ->join('opd_details', 'opd_details.patient_id=patients.id', 'left')
                ->join('visit_details', 'visit_details.opd_details_id=opd_details.id', 'left')
                ->join('bed', 'ipd_details.bed = bed.id', "left")
                ->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left")
                ->join('floor', 'floor.id = bed_group.floor', "left")
                ->where('patients.gender', 'Female')
                ->where('ipd_details.case_reference_id', $case_id)
                ->or_where('opd_details.case_reference_id', $case_id)
                ->get()
                ->row_array();
        }
  
    public function getPatientListfilter($search_term)
    {
        $result = $this->db
            ->select("id,patient_name")
            ->group_start()
            ->where("id", $search_term)
            ->or_like("patient_name", $search_term)
            ->group_end()
            ->where("is_active","yes")
            ->where("is_dead!=","yes")
            ->get("patients")
            ->result();
        return $result;
    }

    public function getReferenceByIpdId($id)
    {
        $result = $this->db
            ->select("case_reference_id")
            ->where("id", $id)
            ->get("ipd_details")
            ->row();
        return $result->case_reference_id;
    }

    public function getPatientVisitDetails($patient_id)
    {
        $result = $this->db->select("p.patient_name,od.id as opd_id,od.case_reference_id,p.id as patient_id")
            ->join("opd_details od", "od.patient_id = p.id", "right")
            ->where("p.id", $patient_id)
            ->get("patients p")
            ->result();
        foreach ($result as $key => $value) {
            $result[$key]->amount   = $this->getOPDChargeAmount($value->opd_id);
            $result[$key]->payments = $this->getPatientOPDPayments($value->opd_id);
        }
        return $result;
    }

    public function getOPDChargeAmount($opd_id)
    {
        $amount = $this->db->select("sum(amount) as amount")
            ->where("opd_id", $opd_id)
            ->get("patient_charges")
            ->row();
        return $amount->amount;
    }

    public function getPatientIpdVisitDetails($patient_id)
    {
        $result = $this->db->select("p.patient_name,id.id as ipd_id,sum(pc.amount) as amount,id.case_reference_id")
            ->join("ipd_details id", "id.patient_id = p.id", "left")
            ->join("patient_charges pc", "pc.ipd_id=id.id", "left")
            ->where("p.id", $patient_id)
            ->group_by("id.id")
            ->get("patients p")
            ->result();
        foreach ($result as $key => $value) {
            $result[$key]->payments = $this->getPatientIPDPayments($value->ipd_id);
        }
        return $result;           
    }

    public function getPatientPharmacyVisitDetails($patient_id)
    {        
        $this->db
            ->select('pharmacy_bill_basic.*,IFNULL((select sum(amount) as amount_paid from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="payment" ),0) as paid_amount, IFNULL((select sum(amount) as refund from transactions WHERE transactions.pharmacy_bill_basic_id =pharmacy_bill_basic.id and transactions.type="refund" ),0) as refund_amount, patients.patient_name,patients.id as pid')
            ->join('patients', 'patients.id = pharmacy_bill_basic.patient_id', 'left')
           ->where('patients.id',$patient_id);
           $result= $this->db->get('pharmacy_bill_basic');
          return $result->result_array();
    }

    public function getPatientPathologyVisitDetails($patient_id)
    {
        $result = $this->db->select("p.patient_name,pb.id as bill_no,pb.net_amount as amount,pb.case_reference_id")
            ->join("pathology_billing pb", "pb.patient_id = p.id", "left")
            ->where("p.id", $patient_id)
            ->get("patients p")
            ->result();
        foreach ($result as $key => $value) {
            $result[$key]->payments = $this->getPatientPathologyPayments($value->bill_no);
        }
        return $result;
    }

    public function getPatientRadiologyVisitDetails($patient_id)
    {
        $this->datatables
            ->select('radiology_billing.*,(SELECT IFNULL(SUM(transactions.amount),0) from transactions WHERE transactions.radiology_billing_id=radiology_billing.id ) as paid_amount,patients.patient_name,patients.id as pid,staff.name,staff.surname,staff.employee_id')
            ->join('patients', 'patients.id = radiology_billing.patient_id', 'left')
            ->join('transactions', 'transactions.radiology_billing_id = radiology_billing.id', 'left')
            ->join('staff', 'staff.id = radiology_billing.doctor_id', 'left')
          ->where('patients.id',$patient_id);
           $result= $this->db->get('radiology_billing');
          return $result->result_array();
    }

    public function getPatientBloodBankVisitDetails($patient_id)
    {
        $blood_issue_query="SELECT `blood_issue`.*, IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.blood_issue_id= blood_issue.id ), 0) as `paid_amount`, `blood_bank_products`.`name` as `blood_group`, `patients`.`patient_name`, `patients`.`gender`, `blood_donor`.`donor_name`, `blood_donor_cycle`.`bag_no`, `blood_donor_cycle`.`volume`, `blood_donor_cycle`.`unit` FROM `blood_issue` JOIN `patients` ON `patients`.`id` = `blood_issue`.`patient_id` JOIN `blood_donor_cycle` ON `blood_donor_cycle`.`id` = `blood_issue`.`blood_donor_cycle_id` JOIN `blood_donor` ON `blood_donor_cycle`.`blood_donor_id` = `blood_donor`.`id` JOIN `blood_bank_products` ON `blood_bank_products`.`id` = `blood_donor`.`blood_bank_product_id` where `blood_issue`.`patient_id`=".$this->db->escape($patient_id)." ORDER BY `patients`.`patient_name` ASC, `blood_issue`.`id` ";
        $blood_issue_sql        = $this->db->query($blood_issue_query);
        $blood_issue_result     = $blood_issue_sql->result_array();
        $component_issue_query="select blood_issue.*,IFNULL((SELECT sum(transactions.amount) from transactions WHERE transactions.blood_issue_id= blood_issue.id ),0) as `paid_amount`,blood_group.name as blood_group_name,component.name as component_name,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.volume,blood_donor_cycle.unit from blood_issue inner join blood_donor_cycle on blood_donor_cycle.id=blood_issue.blood_donor_cycle_id join blood_donor_cycle as bcd on blood_donor_cycle.blood_donor_cycle_id=bcd.id join blood_donor on blood_donor.id=bcd.blood_donor_id join blood_bank_products as component on component.id=blood_donor_cycle.blood_bank_product_id join blood_bank_products as blood_group on blood_group.id=blood_donor.blood_bank_product_id join patients on patients.id = blood_issue.patient_id where `blood_issue`.`patient_id`=".$this->db->escape($patient_id)." ORDER BY patients.patient_name desc";
        $component_issue_sql        = $this->db->query($component_issue_query);
        $component_issue_result     = $component_issue_sql->result_array();
         return array('blood_issue'=>$blood_issue_result,'component_issue'=>$component_issue_result);
    }

    public function getPatientAmbulanceVisitDetails($patient_id)
    {      
        $this->db
            ->select('ambulance_call.*,(SELECT IFNULL(SUM(transactions.amount),0) from transactions WHERE transactions.ambulance_call_id=ambulance_call.id ) as paid_amount,vehicles.vehicle_no,vehicles.vehicle_model,patients.patient_name as patient,patients.id as patient_id,patients.mobileno,patients.address as patient_address,staff.name,staff.surname')
            ->join('vehicles', 'vehicles.id = ambulance_call.vehicle_id')
            ->join('patients', 'patients.id = ambulance_call.patient_id')
            ->join('staff', 'staff.id = ambulance_call.generated_by')
            ->where('patients.id',$patient_id);
          $result= $this->db->get('ambulance_call');
          return $result->result_array();
    } 

    public function getPatientChargePaymentOPD($case_reference_id)
    {
        $result = $this->db->select("'OPD' as module,patients.patient_name,patients.id as patient_id,opd_details.id as opd_id,sum(amount) as charge")
            ->join("patient_charges", "opd_details.id = patient_charges.opd_id", "left")
            ->join("patients", "patients.id = opd_details.patient_id", "left")
            ->where("opd_details.case_reference_id", $case_reference_id)
            ->get("opd_details")
            ->result();
        foreach ($result as $key => $value) {
            if ($value->opd_id != "") {
                $result[$key]->payments = $this->getPatientOPDPayments($value->opd_id);
            }
        }
        if ($value->opd_id != "") {
            return $result;
        }else{
            return array();
        }        
    }

    public function getPatientOPDPayments($opd_id)
    {
        $result = $this->db->select("amount,payment_mode,payment_date,cheque_no,cheque_date,attachment,attachment_name,id")
            ->where("opd_id", $opd_id)
            ->get("transactions")
            ->result();
        return $result;
    }

    public function getPatientChargePaymentIPD($case_reference_id)
    {
        $result = $this->db->select("'IPD' as module,patients.patient_name,patients.id as patient_id,ipd_details.id as ipd_id,sum(amount) as charge")
            ->join("patient_charges", "ipd_details.id = patient_charges.ipd_id", "left")
            ->join("patients", "patients.id = ipd_details.patient_id", "left")
            ->where("ipd_details.case_reference_id", $case_reference_id)
            ->get("ipd_details")
            ->result();
        foreach ($result as $key => $value) {
            if ($value->ipd_id != "") {
                $result[$key]->payments = $this->getPatientIPDPayments($value->ipd_id);
            }
        }
        if ($value->ipd_id != "") {
            return $result;
        }else{
            return array();
        }
    }

    public function getPatientIPDPayments($ipd_id)
    {
        $result = $this->db->select("amount,payment_mode,payment_date,cheque_no,cheque_date,attachment,attachment_name,id")
            ->where("ipd_id", $ipd_id)
            ->get("transactions")
            ->result();
        return $result;
    }

    public function getPatientChargePaymentPharmacy($case_reference_id)
    {
        $result = $this->db->select("'Pharmacy' as module,patients.patient_name,patients.id as patient_id,pharmacy_bill_detail.id as bill_no,pharmacy_bill_basic.id as bill_basic_id,pharmacy_bill_basic.net_amount as charge")
            ->join("patients", "patients.id = pharmacy_bill_basic.patient_id", "left")
            ->join("pharmacy_bill_detail", "pharmacy_bill_detail.pharmacy_bill_basic_id=pharmacy_bill_basic.id")
            ->where("pharmacy_bill_basic.case_reference_id", $case_reference_id)
            ->get("pharmacy_bill_basic")
            ->result();
        foreach ($result as $key => $value) {
            $result[$key]->payments = $this->getPatientPharmacyPayments($value->bill_basic_id);
        }
        return $result;
    }

    public function getPatientPharmacyPayments($bill_basic_id)
    {
        $result = $this->db->select("amount,payment_mode,payment_date,cheque_no,cheque_date")
            ->where("pharmacy_bill_basic_id", $bill_basic_id)
            ->get("transactions")
            ->result();
        return $result;
    }

    public function getPatientChargePaymentPathology($case_reference_id)
    {
        $result = $this->db->select("'Pathology' as module,patients.patient_name,patients.id as patient_id,pathology_billing.id as bill_no,pathology_billing.net_amount as charge")
            ->join("patients", "patients.id = pathology_billing.patient_id", "left")
            ->where("pathology_billing.case_reference_id", $case_reference_id)
            ->get("pathology_billing")
            ->result();
        foreach ($result as $key => $value) {
            $result[$key]->payments = $this->getPatientPathologyPayments($value->bill_no);
        }
        return $result;
    }

    public function getPatientPathologyPayments($pathology_billing)
    {
        $result = $this->db->select("amount,payment_mode,payment_date,cheque_no,cheque_date,id,attachment,attachment_name")
            ->where("pathology_billing_id", $pathology_billing)
            ->get("transactions")
            ->result();
        return $result;
    }

    public function getPatientChargePaymentRadiology($case_reference_id)
    {
        $result = $this->db->select("'Radiology' as module,patients.patient_name,patients.id as patient_id,radiology_billing.id as bill_no,radiology_billing.net_amount as charge")
            ->join("patients", "patients.id = radiology_billing.patient_id", "left")
            ->where("radiology_billing.case_reference_id", $case_reference_id)
            ->get("radiology_billing")
            ->result();
        foreach ($result as $key => $value) {
            $result[$key]->payments = $this->getPatientRadiologyPayments($value->bill_no);
        }
        return $result;
    }

    public function getPatientRadiologyPayments($radiology_billing)
    {
        $result = $this->db->select("amount,payment_mode,payment_date,cheque_no,cheque_date,id,attachment,attachment_name")
            ->where("radiology_billing_id", $radiology_billing)
            ->get("transactions")
            ->result();
        return $result;
    }

    public function getPatientChargePaymentAmbulance($case_reference_id)
    {
        $result = $this->db->select("'Ambulance' as module,patients.patient_name,patients.id as patient_id,ambulance_call.id as bill_no,ambulance_call.net_amount as charge")
            ->join("patients", "patients.id = ambulance_call.patient_id", "left")
            ->where("ambulance_call.case_reference_id", $case_reference_id)
            ->get("ambulance_call")
            ->result();
        foreach ($result as $key => $value) {
            $result[$key]->payments = $this->getPatientAmbulancePayments($value->bill_no);
        }
        return $result;
    }

    public function getPatientAmbulancePayments($ambulance_call_id)
    {
        $result = $this->db->select("amount,payment_mode,payment_date,cheque_no,cheque_date,id,attachment,attachment_name")
            ->where("ambulance_call_id", $ambulance_call_id)
            ->get("transactions")
            ->result();
        return $result;
    }

    public function getPatientChargePaymentBloodBank($case_reference_id)
    {
        $result = $this->db->select("'Blood Bank' as module,patients.patient_name,patients.id as patient_id,blood_issue.id as bill_no,blood_issue.net_amount as charge")
            ->join("patients", "patients.id = blood_issue.patient_id", "left")
            ->where("blood_issue.case_reference_id", $case_reference_id)
            ->get("blood_issue")
            ->result();
        foreach ($result as $key => $value) {
            $result[$key]->payments = $this->getPatientBloodBankPayments($value->bill_no);
        }
        return $result;
    }

    public function getPatientBloodBankPayments($blood_issue_id)
    {
        $result = $this->db->select("amount,payment_mode,payment_date,cheque_no,cheque_date,id,attachment,attachment_name")
            ->where("blood_issue_id", $blood_issue_id)
            ->get("transactions")
            ->result();
        return $result;
    }

    public function getAllPatientList()
    {
        $this->datatables
            ->select('patients.*,users.username,users.id as user_tbl_id,users.is_active as user_tbl_active')
            ->searchable('patients.id,patients.patient_name,users.username,patients.mobileno')
            ->orderable('patients.id,patients.patient_name,users.username,patients.mobileno')
            ->join('users', 'users.user_id = patients.id')
            ->from('patients');
        return $this->datatables->generate("json");
    }
    
    public function getallinvestigation($case_reference_id){
       
        $query = $this->db->query("select pathology_report.id as report_id,pathology_report.pathology_bill_id,pathology.test_name,pathology.short_name,pathology.report_days,pathology.id as pid,pathology.charge_id as cid,staff.name,staff.surname,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`, 'pathology' as type, pathology_report.pathology_center as test_center, pathology_report.collection_date,pathology_report.reporting_date,pathology_report.parameter_update from pathology_billing inner join pathology_report on pathology_report.pathology_bill_id = pathology_billing.id inner join pathology on pathology_report.pathology_id = pathology.id left join staff on staff.id = pathology_billing.doctor_id left join staff as collection_specialist_staff on collection_specialist_staff.id = pathology_report.collection_specialist left join staff as approved_by_staff on approved_by_staff.id = pathology_report.approved_by where pathology_billing.case_reference_id= ".$case_reference_id . " 
            union all 
            select radiology_report.id as report_id, radiology_report.radiology_bill_id,radio.test_name,radio.short_name,radio.report_days,radio.id as pid,radio.charge_id as cid,staff.name,staff.surname,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`, 'radiology' as type,radiology_report.radiology_center as test_center,radiology_report.collection_date,radiology_report.reporting_date,radiology_report.parameter_update  from radiology_billing inner join radiology_report on radiology_report.radiology_bill_id = radiology_billing.id inner join radio on radiology_report.radiology_id = radio.id left join staff on staff.id = radiology_report.consultant_doctor left join staff as collection_specialist_staff on collection_specialist_staff.id = radiology_report.collection_specialist left join staff as approved_by_staff on approved_by_staff.id = radiology_report.approved_by where radiology_billing.case_reference_id=".$case_reference_id." "  );
           $result = $query->result_array();
          return $result ;
    }

    public function allinvestigationbypatientid($patient_id){
       
        $query = $this->db->query("select pathology_report.id as report_id,pathology_report.pathology_bill_id,pathology.test_name,pathology.short_name,pathology.report_days,pathology.id as pid,pathology.charge_id as cid,staff.name,staff.surname,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`, 'pathology' as type, pathology_report.pathology_center as test_center, pathology_report.collection_date,pathology_report.reporting_date,pathology_report.parameter_update,pathology_billing.case_reference_id from pathology_billing inner join pathology_report on pathology_report.pathology_bill_id = pathology_billing.id inner join pathology on pathology_report.pathology_id = pathology.id left join staff on staff.id = pathology_billing.doctor_id left join staff as collection_specialist_staff on collection_specialist_staff.id = pathology_report.collection_specialist left join staff as approved_by_staff on approved_by_staff.id = pathology_report.approved_by where pathology_billing.patient_id= ".$patient_id . " 
            union all 
            select radiology_report.id as report_id, radiology_report.radiology_bill_id,radio.test_name,radio.short_name,radio.report_days,radio.id as pid,radio.charge_id as cid,staff.name,staff.surname,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`, 'radiology' as type,radiology_report.radiology_center as test_center,radiology_report.collection_date,radiology_report.reporting_date,radiology_report.parameter_update,radiology_billing.case_reference_id  from radiology_billing inner join radiology_report on radiology_report.radiology_bill_id = radiology_billing.id inner join radio on radiology_report.radiology_id = radio.id left join staff on staff.id = radiology_report.consultant_doctor left join staff as collection_specialist_staff on collection_specialist_staff.id = radiology_report.collection_specialist left join staff as approved_by_staff on approved_by_staff.id = radiology_report.approved_by where radiology_billing.patient_id=".$patient_id);
           $result = $query->result_array();
          return $result ;
    }

    public function getopdtreatmenthistory($patientid)
    {
        $this->datatables
            ->select('opd_details.case_reference_id,opd_details.id as opd_id,opd_details.patient_id as patientid,opd_details.is_ipd_moved,max(visit_details.id) as visit_id,visit_details.appointment_date,visit_details.refference,visit_details.symptoms,patients.id as pid,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge,' )
            ->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left")
            ->join('staff', 'staff.id = visit_details.cons_doctor', "inner")
            ->join('patients', 'patients.id = opd_details.patient_id', "inner")
            ->join('consult_charges', 'consult_charges.doctor=visit_details.cons_doctor', 'left')
            ->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left')
            ->searchable('opd_details.id,opd_details.case_reference_id,visit_details.appointment_date,staff.name,visit_details.refference,visit_details.symptoms')
            ->orderable('opd_details.id,opd_details.case_reference_id,visit_details.appointment_date,staff.name,visit_details.refference,visit_details.symptoms,')
            ->sort('visit_details.id', 'desc')
            ->where('opd_details.patient_id', $patientid)
            ->group_by('visit_details.opd_details_id', '')
            ->from('opd_details');
        $result = $this->datatables->generate('json');
        return $result;
    }

    public function getipdtreatmenthistory($patient_id)
    {
        $userdata           = $this->customlib->getUserData();        
        $this->datatables
            ->select('patients.*,bed.name as bed_name,bed_group.name as bedgroup_name, floor.name as floor_name,ipd_details.date,ipd_details.id as ipdid,ipd_details.case_reference_id,ipd_details.credit_limit as ipdcredit_limit,ipd_details.case_type,ipd_details.symptoms,staff.name,staff.surname,staff.employee_id')
            ->join('patients', 'patients.id = ipd_details.patient_id', "inner")
            ->join('staff', 'staff.id = ipd_details.cons_doctor', "inner")
            ->join('bed', 'ipd_details.bed = bed.id', "left")
            ->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left")
            ->join('floor', 'floor.id = bed_group.floor', "left")
            ->searchable('patients.patient_name,ipd_details.id,patients.id,patients.gender,patients.mobileno,staff.name,bed.name')
            ->orderable('patients.patient_name,ipd_details.case_reference_id,ipd_details.id,patients.id,patients.gender,patients.mobileno,staff.name,bed.name')
            ->sort('ipd_details.id', 'desc')
            ->where('ipd_details.patient_id', $patient_id)
            ->from('ipd_details');
        return $this->datatables->generate('json');

    }

    public function get_patientidbyIpdId($id){
        return $this->db->select('patient_id,case_reference_id,cons_doctor,staff.name as doctor_name,staff.surname as doctor_surname,staff.employee_id as doctor_employee_id,roles.id as role_id')->from('ipd_details')->join('staff','staff.id=ipd_details.cons_doctor')->join("staff_roles", "staff_roles.staff_id = staff.id", "left")->join("roles", "staff_roles.role_id = roles.id", "left")->where('ipd_details.id',$id)->get()->row_array();
    }

    public function get_patientidbyvisitid($id){
        return $this->db->select('patient_id,visit_details.opd_details_id,cons_doctor as doctor_id')->from('visit_details')->join('opd_details','opd_details.id=visit_details.opd_details_id')->where('visit_details.id',$id)->get()->row_array();
    }

    public function get_patientidbyopdid($id){
        return $this->db->select('patient_id,visit_details.opd_details_id,cons_doctor as doctor_id,case_reference_id')->from('visit_details')->join('opd_details','opd_details.id=visit_details.opd_details_id')->where('visit_details.opd_details_id',$id)->get()->row_array();
    }

    public function get_patientbed($ipd_details_id){
       return $this->db->select('ipd_details.bed_group_id,bed,bed.name as bed_name,bed_group.name as bed_group_name,bed.is_active')->from('ipd_details')->join('bed','bed.id=ipd_details.bed')->join('bed_group','bed_group.id=ipd_details.bed_group_id')->where('ipd_details.id',$ipd_details_id)->get()->row_array();
    }
 
    public function ipd_discharge_revert($ipd_details_id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $ipd_details_id);
        $this->db->update('ipd_details', array('discharged' => 'no'));
            
        $message = UPDATE_RECORD_CONSTANT . " On Ipd Details id " . $ipd_details_id;
        $action = "Update";
        $record_id = $ipd_details_id;
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

    public function opd_discharge_revert($opd_details_id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $opd_details_id);
        $this->db->update('opd_details', array('discharged' => 'no'));
        
        $message = UPDATE_RECORD_CONSTANT . " On Opd Details id " . $opd_details_id;
        $action = "Update";
        $record_id = $opd_details_id;
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
    
    public function remove_dischargeCard($discharge_data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $this->db->where('id', $discharge_data['id']);        
        $this->db->delete('discharge_card');  
        
        $message = DELETE_RECORD_CONSTANT . " On Discharge Card id " . $discharge_data['id'];
        $action = "Delete";
        $record_id = $discharge_data['id'];
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

    public function getopdvisitreportdata($patient_id)
    {
        $query = $this->db->select("opd_details.id,opd_details.case_reference_id,visit_details.id as visit_id,visit_details.symptoms,visit_details.appointment_date, ipd_prescription_basic.finding_description, patients.patient_name,patients.dob,patients.age,patients.month,patients.day,patients.gender,patients.mobileno,patients.guardian_name,patients.address,patients.id patientid,staff.name,staff.surname,staff.employee_id")
            ->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left")
            ->join('ipd_prescription_basic', 'ipd_prescription_basic.visit_details_id=visit_details.id', 'left')
            ->join('patients', 'patients.id = opd_details.patient_id', "left") 
             ->join('staff', 'staff.id = visit_details.cons_doctor', "left") 
            ->where('patients.id',$patient_id)
            ->get('opd_details');
        $result = $query->result_array();
        return $result;
    }

    public function getipdvisitreportdata($patient_id)
    {
        $query = $this->db->select("ipd_details.id,ipd_details.case_reference_id,ipd_details.symptoms,ipd_details.date, ipd_prescription_basic.finding_description, patients.patient_name,patients.dob,patients.age,patients.month,patients.day,patients.gender,patients.mobileno,patients.guardian_name,patients.address,patients.id patientid,staff.name,staff.surname,staff.employee_id")          
            ->join('ipd_prescription_basic', 'ipd_prescription_basic.ipd_id = ipd_details.id', 'left')
            ->join('patients', 'patients.id = ipd_details.patient_id', "left") 
             ->join('staff', 'staff.id = ipd_details.cons_doctor', "left") 
            ->where('patients.id',$patient_id)
            ->get('ipd_details');
        $result = $query->result_array();
        return $result;
    }

    public function getpatientallergy($patient_id)
    {
        $this->db->select('known_allergies')
        ->from('visit_details')
        ->join('opd_details',"opd_details.id=visit_details.opd_details_id")
         ->where('opd_details.patient_id',$patient_id)
        ->where('known_allergies!=',"")
        ->order_by("visit_details.id","desc")
        ->group_by('known_allergies')
        ->limit(5);
       $query= $this->db->get();
       $result = $query->result_array();
       return $result;
    }
	
    public function getpatientfindings($patient_id)
    {
        $this->db->select('finding_description')
        ->from('ipd_prescription_basic')
        ->join('visit_details',"visit_details.id=ipd_prescription_basic.visit_details_id")
        ->join('opd_details',"opd_details.id=visit_details.opd_details_id")
         ->where('opd_details.patient_id',$patient_id)
        ->where('finding_description!=',"")
        ->order_by("ipd_prescription_basic.id","desc")
        ->group_by('finding_description')
        ->limit(5);
       $query= $this->db->get();
       $result = $query->result_array();
       return $result;
    }

    public function getpatientsymptoms($patient_id)
    {
        $this->db->select('symptoms')
        ->from('visit_details')
        ->join('opd_details',"opd_details.id=visit_details.opd_details_id")
         ->where('opd_details.patient_id',$patient_id)
        ->where('symptoms!=',"")
        ->order_by("visit_details.id","desc")
        ->group_by('symptoms')
        ->limit(5);
       $query= $this->db->get();
       $result = $query->result_array();
       return $result;
    }

     public function getconsultantdoctor($patient_id)
    {
        $this->db->select('staff.name,staff.surname,staff.employee_id,staff.image')
        ->from("staff")
        ->join('visit_details',"visit_details.cons_doctor = staff.id ")
        ->join('opd_details',"opd_details.id=visit_details.opd_details_id")
         ->where('opd_details.patient_id',$patient_id)
        ->where('staff.name!=',"")
        ->order_by("visit_details.id","desc")
        ->group_by('staff.name')
        ->limit(5);
       $query= $this->db->get();
       $result = $query->result_array();
       return $result;
    }

    public function getlabinvestigation($patient_id)
    {
      $query=$this->db->query("select pathology.test_name,pathology.short_name 
            from pathology_billing 
            inner join pathology_report  on pathology_billing.id= pathology_report.pathology_bill_id
            inner join pathology  on pathology_report.pathology_id = pathology.id
             where pathology_billing.patient_id='".$patient_id."' 
             union all 
             select radio.test_name,radio.short_name 
            from radiology_billing 
            inner join radiology_report  on radiology_billing.id = radiology_report.radiology_bill_id
            inner join radio  on radiology_report.radiology_id =radio.id
             where radiology_billing.patient_id='".$patient_id."'  ");
      
       $result = $query->result_array();
       return $result;
    }

    public function getpatienthistory($patientid)
    {
        $this->db
            ->select('opd_details.case_reference_id,opd_details.id as opd_id,opd_details.patient_id as patientid,opd_details.is_ipd_moved,max(visit_details.id) as visit_id,visit_details.appointment_date,visit_details.refference,visit_details.symptoms,patients.id as pid,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge,' )
            ->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left")
            ->join('staff', 'staff.id = visit_details.cons_doctor', "inner")
            ->join('patients', 'patients.id = opd_details.patient_id', "inner")
            ->join('consult_charges', 'consult_charges.doctor=visit_details.cons_doctor', 'left')
            ->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left')           
            ->order_by('visit_details.id', 'desc')
            ->where('opd_details.patient_id', $patientid)
            ->where('opd_details.discharged', 'yes')
            ->group_by('visit_details.opd_details_id', '')
             ->limit(5)
            ->from('opd_details');
            $query = $this->db->get();
            $result = $query->result_array();
        return $result;
    }

    public function getpatientvisits($patientid)
    {
        $this->db
            ->select('opd_details.case_reference_id,opd_details.id as opd_id,opd_details.patient_id as patientid,opd_details.is_ipd_moved,max(visit_details.id) as visit_id,visit_details.appointment_date,visit_details.refference,visit_details.symptoms,patients.id as pid,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge' )
            ->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left")
            ->join('staff', 'staff.id = visit_details.cons_doctor', "inner")
            ->join('patients', 'patients.id = opd_details.patient_id', "inner")
            ->join('consult_charges', 'consult_charges.doctor=visit_details.cons_doctor', 'left')
            ->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left')           
            ->order_by('visit_details.id', 'desc')
            ->where('opd_details.patient_id', $patientid)
            ->where('opd_details.discharged', 'no')
            ->group_by('visit_details.opd_details_id', '')
             ->order_by('visit_details.opd_details_id', 'desc')
            ->limit(5)
            ->from('opd_details');
         $query = $this->db->get();
         $result = $query->result_array();
        return $result;
    }

    public function getpatientallergybycaseid($case_reference_id)
    {
        $this->db->select('known_allergies')
        ->from('opd_details')
        ->join('visit_details',"opd_details.id=visit_details.opd_details_id")
         ->where('opd_details.case_reference_id',$case_reference_id)
        ->where('known_allergies!=',"")
        ->order_by("visit_details.id","desc")
        ->group_by('known_allergies')
        ->limit(5);
       $query= $this->db->get();
       $result = $query->result_array();
       return $result;
    }

     public function getpatientfindingsbycaseid($case_reference_id)
    {
        $this->db->select('finding_description')
       ->from("opd_details")
       ->join('visit_details',"opd_details.id=visit_details.opd_details_id")
        ->join('ipd_prescription_basic',"visit_details.id=ipd_prescription_basic.visit_details_id")
        ->where('opd_details.case_reference_id',$case_reference_id)
       ->where('finding_description!=',"")
        ->order_by("ipd_prescription_basic.id","desc")
        ->group_by('finding_description')
        ->limit(5);
       $query= $this->db->get();
       $result = $query->result_array();
       return $result;
    }

    public function getpatientsymptomsbycaseid($case_reference_id)
    {
        $this->db->select('symptoms')
        ->from('opd_details')
        ->join('visit_details',"opd_details.id=visit_details.opd_details_id")
         ->where('opd_details.case_reference_id',$case_reference_id)
        ->where('symptoms!=',"")
        ->order_by("visit_details.id","desc")
        ->group_by('symptoms')
        ->limit(5);
       $query= $this->db->get();
       $result = $query->result_array();
       return $result;
    }

    public function getconsultantdoctorbycaseid($case_reference_id)
    {
        $this->db->select('staff.name,staff.surname,staff.employee_id,staff.image')
        ->from("opd_details")       
        ->join('visit_details',"opd_details.id=visit_details.opd_details_id")
         ->join('staff',"visit_details.cons_doctor = staff.id ")
         ->where('opd_details.case_reference_id',$case_reference_id)
        ->where('staff.name!=',"")
        ->order_by("visit_details.id","desc")
        ->group_by('staff.name')
        ->limit(5);
       $query= $this->db->get();
       $result = $query->result_array();
       return $result;
    }

    public function getlabinvestigationbycaseid($case_reference_id)
    {
      $query=$this->db->query("select pathology.test_name,pathology.short_name 
            from
            opd_details 
             left join  pathology_billing on opd_details.patient_id = pathology_billing.patient_id
            inner join pathology_report  on pathology_billing.id= pathology_report.pathology_bill_id
            inner join pathology  on pathology_report.pathology_id = pathology.id
             where opd_details.case_reference_id ='".$case_reference_id."' 
             union all 
             select radio.test_name,radio.short_name 
            from
            opd_details 
             left join  radiology_billing on opd_details.patient_id = radiology_billing.patient_id
            inner join radiology_report  on radiology_billing.id = radiology_report.radiology_bill_id
            inner join radio  on radiology_report.radiology_id =radio.id
             where opd_details.case_reference_id ='".$case_reference_id."'  ");      
      
       $result = $query->result_array();
       return $result;
    }

     public function ipd_bill_paymentbycase_id($case_id){
        $opd_bill_payment['opd']['bill']=$this->db->select('sum(amount) as total_bill')->from('opd_details')->join('patient_charges','patient_charges.ipd_id=opd_details.id')->where('opd_details.case_reference_id',$case_id)->get()->row_array();
        $opd_bill_payment['opd']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'opd_id !='=>'NULL'))->get()->row_array();
        $opd_bill_payment['pharmacy']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('pharmacy_bill_basic')->where('pharmacy_bill_basic.case_reference_id',$case_id)->get()->row_array();
        $opd_bill_payment['pharmacy']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pharmacy_bill_basic_id !='=>'NULL'))->get()->row_array();
        $opd_bill_payment['pharmacy']['payment_refund']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pharmacy_bill_basic_id !='=>'NULL','type'=>'refund'))->get()->row_array();
        $opd_bill_payment['pathology']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('pathology_billing')->where('pathology_billing.case_reference_id',$case_id)->get()->row_array();
        $opd_bill_payment['pathology']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'pathology_billing_id !='=>'NULL'))->get()->row_array();
        $opd_bill_payment['radiology']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('radiology_billing')->where('radiology_billing.case_reference_id',$case_id)->get()->row_array();
        $opd_bill_payment['radiology']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'radiology_billing_id !='=>'NULL'))->get()->row_array();
        $opd_bill_payment['blood_bank']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('blood_issue')->where('blood_issue.case_reference_id',$case_id)->get()->row_array();
        $opd_bill_payment['blood_bank']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'blood_issue_id !='=>'NULL'))->get()->row_array();
        $opd_bill_payment['ambulance']['bill']=$this->db->select('sum(net_amount) as total_bill')->from('ambulance_call')->where('ambulance_call.case_reference_id',$case_id)->get()->row_array();
        $opd_bill_payment['ambulance']['payment']=$this->db->select('sum(amount) as total_payment')->from('transactions')->where(array('case_reference_id'=>$case_id,'ambulance_call_id !='=>'NULL'))->get()->row_array();
        return $opd_bill_payment;
    } 

    public function getpatientoverview($patient_id){

        $patient_details['patient']['allergy'] = $this->db->select('known_allergies')->from('visit_details')->join('opd_details',"opd_details.id=visit_details.opd_details_id")
         ->where('opd_details.patient_id',$patient_id)->where('known_allergies!=',"")->order_by("visit_details.id","desc")->group_by('known_allergies') ->limit(5)->get()->result_array();       

        $patient_details['patient']['findings'] = $this->db->select('finding_description')->from('ipd_prescription_basic')->join('visit_details',"visit_details.id=ipd_prescription_basic.visit_details_id") ->join('opd_details',"opd_details.id=visit_details.opd_details_id")->where('opd_details.patient_id',$patient_id) ->where('finding_description!=',"") ->order_by("ipd_prescription_basic.id","desc")->group_by('finding_description')->limit(5)->get()->result_array();       

        $patient_details['patient']['symptoms'] = $this->db->select('symptoms')->from('visit_details') ->join('opd_details',"opd_details.id=visit_details.opd_details_id")
         ->where('opd_details.patient_id',$patient_id)->where('symptoms!=',"")->order_by("visit_details.id","desc")->group_by('symptoms')->limit(5)->get()->result_array();      
      
        $query=$this->db->query("select pathology.test_name,pathology.short_name  from pathology_billing  inner join pathology_report  on pathology_billing.id= pathology_report.pathology_bill_id  inner join pathology  on pathology_report.pathology_id = pathology.id
             where pathology_billing.patient_id='".$patient_id."'  union all  select radio.test_name,radio.short_name from radiology_billing  inner join radiology_report  on radiology_billing.id = radiology_report.radiology_bill_id  inner join radio  on radiology_report.radiology_id =radio.id  where radiology_billing.patient_id='".$patient_id."' limit 5  ");
         
        $result = $query->result_array();
        $patient_details['patient']['labinvestigation'] = $result ;

        $patient_details['patient']['doctor'] =  $this->db->select('staff.id,staff.name,staff.surname,staff.employee_id,staff.image')->from("staff")->join('visit_details',"visit_details.cons_doctor = staff.id ")->join('opd_details',"opd_details.id=visit_details.opd_details_id")->where('opd_details.patient_id',$patient_id)
        ->where('staff.name!=',"")->order_by("visit_details.id","desc")->group_by('staff.name')->limit(5)->get()->result_array();
         
        $patient_details['patient']['history'] = $this->db
            ->select('opd_details.case_reference_id,opd_details.id as opd_id,opd_details.patient_id as patientid,opd_details.is_ipd_moved,max(visit_details.id) as visit_id,visit_details.appointment_date,visit_details.refference,visit_details.symptoms,patients.id as pid,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge,' )
            ->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left")
            ->join('staff', 'staff.id = visit_details.cons_doctor', "inner")
            ->join('patients', 'patients.id = opd_details.patient_id', "inner")
            ->join('consult_charges', 'consult_charges.doctor=visit_details.cons_doctor', 'left')
            ->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left')
            ->order_by('visit_details.id', 'desc')
            ->where('opd_details.patient_id', $patient_id)
            ->group_by('visit_details.opd_details_id', '')
            ->limit(5)->from('opd_details')->get()->result_array();

        $patient_details['patient']['visitdetails'] = $this->db
        ->select('opd_details.case_reference_id,opd_details.id as opd_id,opd_details.patient_id as patientid,opd_details.is_ipd_moved,max(visit_details.id) as visit_id,visit_details.appointment_date,visit_details.refference,visit_details.symptoms,patients.id as pid,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge' )
        ->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left")->join('staff', 'staff.id = visit_details.cons_doctor', "inner")->join('patients', 'patients.id = opd_details.patient_id', "inner")->join('consult_charges', 'consult_charges.doctor=visit_details.cons_doctor', 'left')
        ->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left')->order_by('visit_details.id', 'desc')->where('opd_details.patient_id', $patient_id)
        ->where('opd_details.discharged', 'no')->group_by('visit_details.opd_details_id', '')->order_by('visit_details.opd_details_id', 'desc')
        ->limit(5)->from('opd_details')->get()->result_array();
            return $patient_details ;
     
    }

    public function getpatientoverviewbycaseid($case_reference_id){

        $patient_details['patient']['allergy']  =   $this->db->select('known_allergies')->from('opd_details')->join('visit_details',"opd_details.id=visit_details.opd_details_id")->where('opd_details.case_reference_id',$case_reference_id)->where('known_allergies!=',"")->order_by("visit_details.id","desc")->group_by('known_allergies')->limit(5)->get()->result_array();

        $patient_details['patient']['findings'] =  $this->db->select('finding_description')->from("opd_details")->join('visit_details',"opd_details.id=visit_details.opd_details_id")->join('ipd_prescription_basic',"visit_details.id=ipd_prescription_basic.visit_details_id")->where('opd_details.case_reference_id',$case_reference_id)->where('finding_description!=',"")
        ->order_by("ipd_prescription_basic.id","desc")->group_by('finding_description')->limit(5)->get()->result_array();

        $patient_details['patient']['symptoms'] =  $this->db->select('symptoms')->from('opd_details')->join('visit_details',"opd_details.id=visit_details.opd_details_id")
             ->where('opd_details.case_reference_id',$case_reference_id)->where('symptoms!=',"")->order_by("visit_details.id","desc")->group_by('symptoms')->limit(5)->get()->result_array();

        $patient_details['patient']['doctor'] =  $this->db->select('staff.id,staff.name,staff.surname,staff.employee_id,staff.image')->from("opd_details")->join('visit_details',"opd_details.id=visit_details.opd_details_id")
         ->join('staff',"visit_details.cons_doctor = staff.id ")->where('opd_details.case_reference_id',$case_reference_id)->where('staff.name!=',"")->order_by("visit_details.id","desc")->group_by('staff.name')->limit(5)->get()->result_array();

        $query = $this->db->query("select pathology.test_name,pathology.short_name   from opd_details  left join  pathology_billing on opd_details.patient_id = pathology_billing.patient_id   inner join pathology_report  on pathology_billing.id= pathology_report.pathology_bill_id   inner join pathology  on pathology_report.pathology_id = pathology.id
             where opd_details.case_reference_id ='".$case_reference_id."' 
             union all  select radio.test_name,radio.short_name   from opd_details   left join  radiology_billing on opd_details.patient_id = radiology_billing.patient_id  inner join radiology_report  on radiology_billing.id = radiology_report.radiology_bill_id  inner join radio  on radiology_report.radiology_id =radio.id   where opd_details.case_reference_id ='".$case_reference_id."'  ");

        $result = $query->result_array();
        $patient_details['patient']['labinvestigation'] = $result ;
      
       return $patient_details;    
    }

    public function getipdoverviewtreatment($patient_id)
    {
        $userdata           = $this->customlib->getUserData();        
        $this->db
            ->select('patients.*,bed.name as bed_name,bed_group.name as bedgroup_name, floor.name as floor_name,ipd_details.date,ipd_details.id as ipdid,ipd_details.case_reference_id,ipd_details.credit_limit as ipdcredit_limit,ipd_details.case_type,ipd_details.symptoms,staff.name,staff.surname,staff.employee_id')
            ->join('patients', 'patients.id = ipd_details.patient_id', "inner")
            ->join('staff', 'staff.id = ipd_details.cons_doctor', "inner")
            ->join('bed', 'ipd_details.bed = bed.id', "left")
            ->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left")
            ->join('floor', 'floor.id = bed_group.floor', "left")
            ->order_by('ipd_details.id', 'desc')
            ->where('ipd_details.patient_id', $patient_id)
            ->from('ipd_details');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result ;
    }

    public function getallopdvisits()
    {
        $i                         = 1;
        $custom_fields             = $this->customfield_model->get_custom_fields('opd', 1);
        $custom_field_column_array = array();

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->datatables->join('custom_field_values as ' . $tb_counter, 'opd_details.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }
        $field_variable      = implode(',', $field_var_array);
        $custom_field_column = implode(',', $custom_field_column_array);

        $this->datatables
            ->select('opd_details.case_reference_id,opd_details.id as opd_id,opd_details.patient_id as patientid,opd_details.is_ipd_moved,max(visit_details.id) as visit_id,visit_details.appointment_date,visit_details.refference,visit_details.symptoms,patients.id as pid,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge,visit_details.is_antenatal,' . $field_variable)
            ->join('visit_details', 'opd_details.id = visit_details.opd_details_id')
            ->join('staff', 'staff.id = visit_details.cons_doctor', "inner")
            ->join('patients', 'patients.id = opd_details.patient_id', "inner")
            ->join('consult_charges', 'consult_charges.doctor=visit_details.cons_doctor', 'left')
            ->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left')
            ->searchable('opd_details.id,opd_details.case_reference_id,visit_details.appointment_date,staff.name,visit_details.refference,visit_details.symptoms,patient_name,visit_details.is_antenatal')
            ->orderable('opd_details.id,patient_name,opd_details.case_reference_id,visit_details.appointment_date,staff.name,visit_details.refference,visit_details.symptoms,visit_details.is_antenatal,' . $custom_field_column)
            ->sort('visit_details.id', 'desc')
            ->group_by('visit_details.opd_details_id', '')
            ->from('opd_details');
        $result = $this->datatables->generate('json');
       
        return $result;
    }    
    
    public function check_patient_exists($str)
    {
        $email = $this->security->xss_clean($str);
        if ($email != "") {
            $id = $this->input->post('patient_id');            

            if ($this->patientDetails($id)) {
                return true;
            } else {               
                $this->form_validation->set_message('check_patient_exists', $this->lang->line('invalid_patient_id'));
                return false;
            }
        }
        return true;
    }
	
	public function getPatientIpdForPatientList($id)
    {
        $this->db->select('ipd_details.id as ipd_id')->from('ipd_details');
        $this->db->where('ipd_details.patient_id', $id); 
		$this->db->where('ipd_details.discharged', 'no');
        $query = $this->db->get();
        return $query->result_array();
    }
    
}
