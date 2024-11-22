<?php

class Patient_model extends CI_Model {

    public function add($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('patients', $data);
        } else {
            $this->db->insert('patients', $data);
            return $this->db->insert_id();
        }
    }

    public function add_patient($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('patients', $data);
        } else {
            $this->db->insert('patients', $data);
            return $this->db->insert_id();
        }
    }

    public function valid_patient($id) {

        $this->db->select('ipd_details.patient_id,patients.discharged,patients.id as pid');
        $this->db->join('patients', 'patients.id=ipd_details.patient_id');
        $this->db->where('patient_id', $id);
        $this->db->where('patients.discharged', 'no');
        $query = $this->db->get('ipd_details');

        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function doctCharge($doctor) {

        $query = $this->db->where("doctor", $doctor)->get("consult_charges");
        return $query->row_array();
    }

    public function doctortpaCharge($doctor, $organisation = "") {

        $result = array();
        $first_query = $this->db->where("consult_charges.doctor", $doctor)
                ->get("consult_charges");
        $first_result = $first_query->row_array();
        $charge_id = $first_result["id"];
        $result = $first_result;

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

    public function doctName($doctor) {

        $query = $this->db->where("id", $doctor)->get("staff");
        return $query->row_array();
    }

    public function patientDetails($id) {

        $query = $this->db->where("id", $id)->get("patients");
        return $query->row_array();
    }

    public function doctorDetails($id) {

        $query = $this->db->where("id", $id)->get("staff");
        return $query->row_array();
    }

    public function supplierDetails($id) {

        $query = $this->db->where("id", $id)->get("supplier_category");
        return $query->row_array();
    }

    public function add_opd($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('opd_details', $data);
        } else {
            $this->db->insert('opd_details', $data);
            return $this->db->insert_id();
        }
    }

    public function add_ipd($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('ipd_details', $data);
        } else {
            $this->db->insert('ipd_details', $data);
            return $this->db->insert_id();
        }
    }

      public function add_disch_summary($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('discharged_summary', $data);
        } else {
            $this->db->insert('discharged_summary', $data);
            return $this->db->insert_id();
        }
    }

    public function addipd($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('ipd_prescription_basic', $data);
        } else {
            $this->db->insert('ipd_prescription_basic', $data);

            return $this->db->insert_id();
        }
    } 

    public function searchAll($searchterm) {
        $this->db->select('patients.*')
                ->from('patients')
                ->like('patients.patient_name', $searchterm)
                ->or_like('patients.guardian_name', $searchterm)
                ->or_like('patients.patient_type', $searchterm)
                ->or_like('patients.address', $searchterm)
                ->or_like('patients.patient_unique_id', $searchterm)
                ->order_by('patients.id', 'desc');

        $query = $this->db->get();

        $result = $query->result_array();
        $info = array();
        $data = array();
        $url = array();
        $info_data = array('OPD', 'IPD', 'Radiology', 'Pathology', 'Pharmacy', 'Operation Theatre');
        $info_url = array();
        foreach ($result as $key => $value) {

            if ($value['is_active'] == 'yes') {
                $id = $value["id"];

                $info_url[0] = base_url() . 'admin/patient/profile/' . $value['id'] . "/" . $value['is_active'];
                $info_url[1] = base_url() . 'admin/patient/ipdprofile/' . $value['id'];
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
                        $url[$i] = $info_url[$i];
                    } else {
                        unset($data[$i]);
                        unset($url[$i]);
                    }
                }
                $result[$key]['info'] = $data;
                $result[$key]['url'] = $url;
            } else {
                unset($result[$key]);
            }
        }

        return $result;
    }

    public function searchAlldisable($searchterm) {
        $this->db->select('patients.*')
                ->from('patients')
                ->like('patients.patient_name', $searchterm)
                ->or_like('patients.guardian_name', $searchterm)
                ->or_like('patients.patient_type', $searchterm)
                ->or_like('patients.address', $searchterm)
                ->or_like('patients.patient_unique_id', $searchterm)
                ->order_by('patients.id', 'desc');

        $query = $this->db->get();

        $result = $query->result_array();
        $info = array();
        $data = array();
        $url = array();
        $info_data = array('OPD', 'IPD', 'Radiology', 'Pathology', 'Pharmacy', 'Operation Theatre');
        $info_url = array();
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
                        $url[$i] = $info_url[$i];
                    } else {
                        unset($data[$i]);
                        unset($url[$i]);
                    }
                }
                $result[$key]['info'] = $data;
                $result[$key]['url'] = $url;
            } else {
                unset($result[$key]);
            }
        }

        return $result;
    }
 
    public function checkpatientipd($patient_type) {
        $this->db->where('patient_id', $patient_type);
        $query = $this->db->get('ipd_details');
        if ($query->num_rows() > 0)
            return TRUE;
        else
            return FALSE;
    }

    public function checkpatientopd($patient_type) {
        $this->db->where('patient_id', $patient_type);
        $query = $this->db->get('opd_details');
        if ($query->num_rows() > 0)
            return TRUE;
        else
            return FALSE;
    }

    public function checkpatientpharma($patient_type) {
        $this->db->where('patient_id', $patient_type);
        $query = $this->db->get('pharmacy_bill_basic');
        if ($query->num_rows() > 0)
            return TRUE;
        else
            return FALSE;
    }

    public function checkpatientot($patient_type) {
        $this->db->where('patient_id', $patient_type);
        $query = $this->db->get('operation_theatre');
        if ($query->num_rows() > 0)
            return TRUE;
        else
            return FALSE;
    }

    public function getPatientListall() {
        $this->db->select('patients.*')->from('patients');
        $this->db->where('patients.is_active', 'yes');
        $this->db->order_by('patients.patient_name', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    } 

    public function getsymptoms($id) {
        $this->db->select('symptoms.*')->from('symptoms');
        $this->db->where('symptoms.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getBlooddonarListall() {
        $this->db->select('blood_donor.*')->from('blood_donor');
        $this->db->order_by('blood_donor.donor_name', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getPatientListallPat() {
        $this->db->select('patients.*')->from('patients');
        $this->db->order_by('patients.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }
 
    public function getPatientList() {
        $this->db->select('patients.*,users.username,users.id as user_tbl_id,users.is_active as user_tbl_active')
                ->join('users', 'users.user_id = patients.id')
                ->from('patients');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getpatientDetails($id) {
        $this->db->select('patients.*,blood_bank_products.name as blood_group_name')
        ->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left')
        ->from('patients')
        ->where('patients.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

      public function getpatientbyUniqueid($uid) {
        $this->db->select('patients.id')->from('patients')->where('patients.patient_unique_id', $uid);
        $query = $this->db->get();
        return $this->db->query();
    }    

    public function searchFullText_old($opd_month, $searchterm, $carray = null,$limit=100,$start="") {

        $last_date = date("Y-m-01 23:59:59.993", strtotime("-" . $opd_month . " month"));
        $userdata = $this->customlib->getUserData();       
        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];        
        $this->db->select('opd_details.*,patients.id as pid,patients.patient_name,patients.patient_unique_id,patients.guardian_name,patients.gender,patients.mobileno,patients.is_ipd,staff.name,staff.surname')->from('opd_details');
        $this->db->join('patients',"patients.id=opd_details.patient_id","LEFT");
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "LEFT");
        $this->db->group_start();
        $this->db->like('patients.patient_name', $searchterm);
        $this->db->or_like('patients.guardian_name', $searchterm);
        $this->db->group_end();
        $this->db->order_by('max(opd_details.appointment_date)', 'desc');
        $this->db->group_by('opd_details.patient_id');
        $this->db->limit($limit,$start);
       
        if ($doctor_restriction == 'enabled') {
            if ($userdata["role_id"] == 3) {
               $this->db->where('opd_details.cons_doctor', $userdata['id']);
             }
        }
        $query = $this->db->get();
        $result = $query->result_array();        
        return $result;
    }


    public function searchByMonth($opd_month, $searchterm, $carray = null) {

        $data = array();
        $first_date = date('Y-m' . '-01', strtotime("-" . $opd_month . " month"));
        $last_date = date('Y-m' . '-' . date('t', strtotime($first_date)) . ' 23:59:59.993');

        $this->db->select('patients.*')->from('patients');
        $this->db->where('patients.is_active', 'yes');
        $this->db->group_start();
        $this->db->like('patients.patient_name', $searchterm);
        $this->db->or_like('patients.guardian_name', $searchterm);
        $this->db->group_end();
        $this->db->order_by('patients.id', 'desc');

        $query = $this->db->get();
        $result = $query->result_array();
        foreach ($result as $key => $value) {
            $consultant_data = $this->getConsultant($value["id"], $opd_month);

            if (!empty($consultant_data)) {

                $result[$key]['name'] = $consultant_data[0]["name"];
                $result[$key]['surname'] = $consultant_data[0]["surname"];
            }
        }

        return $result;
    }

    public function getConsultant($patient_id, $opd_month) {
        $first_date = date('Y-m' . '-01', strtotime("-" . $opd_month . " month"));
        $last_date = date('Y-m' . '-' . date('t', strtotime($first_date)) . ' 23:59:59.993');

        $opd_query = $this->db->select('opd_details.appointment_date,opd_details.case_type,staff.name,staff.surname')
                ->join('staff', 'staff.id = opd_details.cons_doctor', "inner")
                ->where('opd_details.appointment_date >', $first_date)
                ->where('opd_details.appointment_date <', $last_date)
                ->where('opd_details.patient_id', $patient_id)
                ->limit(1)
                ->get('opd_details');
        $result = $opd_query->result_array();

        return $result;
    }

    public function totalVisit($patient_id) {
        $query = $this->db->select('count(opd_details.patient_id) as total_visit')
                ->where('patient_id', $patient_id)
                ->get('opd_details');
        return $query->row_array();
    }

    public function lastVisit($patient_id) {
        $query = $this->db->select('max(opd_details.appointment_date) as last_visit')
                ->where('patient_id', $patient_id)
                ->get('opd_details');
        return $query->row_array();
    }

    public function lastVisitopdno($patient_id) {
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

    public function getMaxPatientId() {
        $query = $this->db->select('max(patients.id) as patient_id')
                ->where('patients.is_active', 'yes')
                ->get('patients');
        $result = $query->row_array();
        return $result["patient_id"];
    }

    public function patientProfile($id, $active = 'yes') {

        $query = $this->db->where("id", $id)->get("patients");
        $result = $query->row_array();
        $data = array();
        $opd_query = $this->db->where('patient_id', $id)->get('opd_details');
        $ipd_query = $this->db->where('patient_id', $id)->get('ipd_details');
        if ($opd_query->num_rows() > 0) {
            $data = $this->getDetails($id);
            $data["patient_type"] = 'Outpatient';
        } else if ($ipd_query->num_rows() > 0) {
            $data = $this->getIpdDetails($id, $active);
            $data["patient_type"] = 'Inpatient';
        }
        return $data;
    }

     public function patientProfileDetails($id, $active = 'yes') {

        $query = $this->db->where("id", $id)->get("patients");
        $result = $query->row_array();
        return $result;
    }

     public function patientProfileType($id, $ptypeno) {

        $query = $this->db->where("id", $id)->get("patients");
        $result = $query->row_array();
        $data = array();
        $opd_query = $this->db->where('opd_details.patient_id', $id)->where('opd_details.opd_no',$ptypeno)->get('opd_details');
        $ipd_query = $this->db->where('patient_id', $id)->where('ipd_details.ipd_no',$ptypeno)->get('ipd_details');
        if ($opd_query->num_rows() > 0) {
            $data = $this->getDetails($id);
            $data["patient_type"] = 'Outpatient';
        } else if ($ipd_query->num_rows() > 0) {
            $data = $this->getIpdDetailsptype($id);
            $data["patient_type"] = 'Inpatient';
        }
        return $data;
    }   

    public function getDetails($id, $opdid=NULL) {
        $this->db->select('opd_details.*,blood_bank_products.name as blood_group_name,visit_details. casualty,visit_details.symptoms,visit_details.known_allergies,visit_details.refference,visit_details.case_type,patients.id as pid,patients.patient_name,patients.age,patients.month,patients.day,patients.image,patients.mobileno,patients.email,patients.gender,patients.dob,patients.marital_status,patients.blood_group,patients.address,patients.guardian_name,patients.month,patients.known_allergies,patients.marital_status,staff.name,staff.surname,discharge_card.discharge_date')->from('opd_details');
        $this->db->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left");
        $this->db->join('discharge_card', 'opd_details.id = discharge_card.opd_details_id', "left");
        $this->db->join('staff', 'staff.id = visit_details.cons_doctor', "left");
        $this->db->join('patients', 'patients.id = opd_details.patient_id', "left");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left');
        $this->db->where('opd_details.id', $opdid);
        $query  = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function addImport($patient_data) {
        $this->db->insert('patients', $patient_data);
        return $this->db->insert_id();
    }

    public function getIpdDetails($ipdid) {
        $this->db->select('patients.*,blood_bank_products.name as blood_group_name,ipd_details.patient_old,ipd_details.id as ipdid,ipd_details.patient_id,discharge_card.    discharge_date,ipd_details.date,ipd_details.date,ipd_details.case_type,ipd_details.id as ipdid,ipd_details.casualty,ipd_details.height,ipd_details.weight,ipd_details.organisation_id,ipd_details.bp,ipd_details.cons_doctor,ipd_details.refference,ipd_details.known_allergies as ipdknown_allergies,ipd_details.case_reference_id,ipd_details.credit_limit as ipdcredit_limit,ipd_details.symptoms,ipd_details.discharged as ipd_discharge,ipd_details.bed,ipd_details.bed_group_id,ipd_details.note as ipdnote,ipd_details.bed,ipd_details.bed_group_id,ipd_details.payment_mode,ipd_details.credit_limit,ipd_details.pulse,ipd_details.temperature,ipd_details.respiration,ipd_details.   organisation_id,staff.id as staff_id,staff.name,staff.surname,staff.image as doctor_image,staff.employee_id,organisation.organisation_name,bed.name as bed_name,bed.id as bed_id,bed_group.name as bedgroup_name,floor.name as floor_name')->from('ipd_details');
        $this->db->join('patients', 'patients.id = ipd_details.patient_id', "left");
        $this->db->join('blood_bank_products', 'blood_bank_products.id = patients.blood_bank_product_id','left');
        $this->db->join('discharge_card', 'ipd_details.id = discharge_card.ipd_details_id', "left");
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "inner");
        $this->db->join('organisation', 'organisation.id = ipd_details.organisation_id', "left");
        $this->db->join('bed', 'ipd_details.bed = bed.id', "left");
        $this->db->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left");
        $this->db->join('floor', 'floor.id = bed_group.floor', "left");
        $this->db->where('ipd_details.id', $ipdid);
        $query = $this->db->get();
        return $query->row_array();
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

    public function getIpdDetailsptype($id) {
        $this->db->select('patients.*,ipd_details.patient_id,ipd_details.date,ipd_details.case_type,ipd_details.ipd_no,ipd_details.id as ipdid,ipd_details.casualty,ipd_details.height,ipd_details.weight,ipd_details.bp,ipd_details.cons_doctor,ipd_details.refference,ipd_details.known_allergies,ipd_details.amount,ipd_details.credit_limit as ipdcredit_limit,ipd_details.symptoms,ipd_details.discharged as ipd_discharge,ipd_details.tax,ipd_details.bed,ipd_details.bed_group_id,ipd_details.note as ipdnote,ipd_details.bed,ipd_details.bed_group_id,')->from('patients');
        $this->db->join('ipd_details', 'patients.id = ipd_details.patient_id', "left");
        $this->db->where('patients.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getIpdnotiDetails($id) {
        $this->db->select('ipd_details.*,')->from('ipd_details');
        $this->db->where('ipd_details.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getsummaryDetails($id) {
        $this->db->select('discharged_summary.*,patients.patient_name,patients.id as patientid,patients.patient_unique_id,patients.age,patients.gender,patients.address,ipd_details.date,ipd_details.discharged_date');
        $this->db->join('patients', 'discharged_summary.patient_id = patients.id');
        $this->db->join('ipd_details', 'discharged_summary.ipd_id = ipd_details.id');        
        $this->db->where('discharged_summary.id', $id);
        $query = $this->db->get('discharged_summary');
        return $query->row_array();
    }   

    public function getOpdnotiDetails($id) {
        $this->db->select('opd_details.*,')->from('opd_details');
        $this->db->where('opd_details.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getOpdpresnotiDetails($id) {
        $this->db->select('opd_details.*,')->from('opd_details');
        $this->db->where('opd_details.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getPatientId() {
        $this->db->select('patients.*,opd_details.appointment_date,opd_details.case_type,opd_details.id as opdid,opd_details.casualty,opd_details.cons_doctor,opd_details.refference,opd_details.known_allergies,opd_details.amount,opd_details.symptoms,opd_details.tax,opd_details.payment_mode')->from('patients');
        $this->db->join('opd_details', 'patients.id = opd_details.patient_id', "inner");
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "inner");
        $this->db->where('patients.is_active', 'yes');
        $query = $this->db->get();
        return $query->result_array();
    }

     public function getpatientidbyipd($ipdid) {
        $this->db->select('ipd_details')->from('ipd_details');
        $this->db->where('ipd_details.id', $ipdid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getOPDetails($id, $opdid = null) {
        if (!empty($opdid)) {
            $this->db->where("opd_details.id", $opdid);
        }
        $this->db->select('opd_details.*,patients.organisation,patients.old_patient,staff.id as staff_id,staff.name,staff.surname,consult_charges.standard_charge,opd_patient_charges.apply_charge')->from('opd_details');
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "inner");
        $this->db->join('patients', 'patients.id = opd_details.patient_id', "inner");
        $this->db->join('consult_charges', 'consult_charges.doctor=opd_details.cons_doctor', 'left');
        $this->db->join('opd_patient_charges', 'opd_details.id=opd_patient_charges.opd_id', 'left');
        $this->db->where('opd_details.patient_id', $id);        
        $this->db->order_by('opd_details.id', 'desc');
        $query = $this->db->get();      
        if (!empty($opdid)) {
            return $query->row_array();
        } else {

            $result = $query->result_array();

            $i = 0;
            foreach ($result as $key => $value) {
                $opd_id = $value["id"];
                $check = $this->db->where("opd_id", $opd_id)->where("visit_id", 0)->get('prescription');
                if ($check->num_rows() > 0) {
                    $result[$i]['prescription'] = 'yes';
                } else {
                    $result[$i]['prescription'] = 'no';
                    $userdata = $this->customlib->getUserData();
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

    public function geteditDiagnosis($id) {

        $this->db->select('diagnosis.*,patients.patient_name,patients.patient_name')->from('diagnosis');
        $this->db->join('patients', 'patients.id = diagnosis.patient_id');
        $this->db->where('diagnosis.id', $id);
        $query = $this->db->get();

        return $query->row_array();
    }

      public function getopddetailspres($id) {

        $this->db->select('opd_details.*,patients.organisation,patients.old_patient,staff.id as staff_id,staff.name,staff.surname')->from('opd_details');
        $this->db->join('patients', 'patients.id = opd_details.patient_id');
        $this->db->join('staff', 'staff.id = opd_details.cons_doctor', "inner");
        $this->db->where('opd_details.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    function add_diagnosis($data) {
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("diagnosis", $data);
        } else {
            $this->db->insert("diagnosis", $data);
            return $this->db->insert_id();
        }
    }

    function getDiagnosisDetails($id) {

        $query1 = $this->db->select('diagnosis.*,diagnosis.id as diagnosis')
                ->join('patients', 'patients.id = diagnosis.patient_id', "inner")
                ->where("patient_id", $id)
                ->get("diagnosis");
        $result1 = $query1->result_array();

        $query2 = $this->db->select('pathology_report.reporting_date as report_date,pathology_report.id,pathology_report.patient_id as patient_id,pathology_report.pathology_report as document,pathology.test_name as report_type,pathology_report.description')
                ->join('pathology', 'pathology.id = pathology_report.pathology_id', "inner")
                ->join('patients', 'patients.id = pathology_report.patient_id', "inner")
                ->where("pathology_report.patient_id", $id)
                ->get("pathology_report");
        $result2 = $query2->result_array();
        $query3 = $this->db->select('radiology_report.reporting_date as report_date,radiology_report.id,radiology_report.patient_id as patient_id,radiology_report.radiology_report as document,radio.test_name as report_type,radiology_report.description')
                ->join('radio', 'radio.id = radiology_report.radiology_id', "inner")
                ->join('patients', 'patients.id = radiology_report.patient_id', "inner")
                ->where("radiology_report.patient_id", $id)
                ->get("radiology_report");
        $result3 = $query3->result_array();
        return array_merge($result1, $result2, $result3);
    }

    public function deleteIpdPatientDiagnosis($id) {
        $this->db->where('id', $id)
                ->delete('diagnosis');
        $this->db->where('id', $id)
                ->delete('pathology_report');
        $this->db->where('id', $id)
                ->delete('radiology_report');
    }

    function add_prescription($data_array) {
        $this->db->insert_batch("prescription", $data_array);
    }

    function add_ipdprescription($data_array) {
        $this->db->insert_batch("ipd_prescription_details", $data_array);
    }

    function getMaxId() {
        $query = $this->db->select('max(patient_unique_id) as patient_id')->get("patients");
        $result = $query->row_array();
        return $result["patient_id"];
    }

    function getMaxOPDId() {
        $query = $this->db->select('max(id) as patient_id')->get("opd_details");
        $result = $query->row_array();
        return $result["patient_id"];
    }

    function getMaxIPDId() {
        $query = $this->db->select('max(id) as ipdid')->get("ipd_details");
        $result = $query->row_array();
        return $result["ipdid"];
    }

    function search_ipd_patients($searchterm, $active = 'yes', $discharged = 'no', $patient_id = '') {
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
        $this->db->select('patients.*,bed.name as bed_name,bed_group.name as bedgroup_name, floor.name as floor_name,ipd_details.date,ipd_details.id as ipdid,ipd_details.credit_limit as ipdcredit_limit,ipd_details.case_type,ipd_details.ipd_no,staff.name,staff.surname
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

    public function patientipddetails($patient_id) {
        $this->db->select('patients.*,bed.name as bed_name,bed_group.name as bedgroup_name, floor.name as floor_name,ipd_details.date,ipd_details.id as ipdid,ipd_details.case_type,ipd_details.ipd_no,staff.name,staff.surname
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

    function add_consultantInstruction($data) {
        $this->db->insert_batch("consultant_register", $data);
    }

    public function deleteIpdPatientConsultant($id) {
        $query = $this->db->where('id', $id)
                ->delete('consultant_register');
    }
    
    function getpatientconsultant($ipd_id) {
        
        $this->db->select('consultant_register.*,staff.name,staff.surname,staff.employee_id');
        $this->db->join('staff', 'staff.id = consultant_register.cons_doctor', "inner");
        $this->db->where("ipd_id", $ipd_id);
        $query= $this->db->get("consultant_register");
       
        $result = $query->result_array();         
       
        foreach($result as  $key => $valuee){                  
            $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'ipdconsultinstruction');            
            $result[$key]['customfield'] = $customfield;                     
        } 
            
        return $result;
        
    }

    public function ipdCharge($code, $orgid) {
        if (!empty($orgid)) {
            $this->db->select('charges.*,organisations_charges.id as org_charge_id, organisations_charges.org_id, organisations_charges.org_charge ');
            $this->db->join('organisations_charges', 'charges.id = organisations_charges.charge_id');
            $this->db->where('organisations_charges.org_id', $orgid);
             $this->db->where('charges.id', $code);
        $query = $this->db->get('charges');
        if($query->num_rows() == 0){
              $this->db->where('charges.id', $code);
        $query = $this->db->get('charges');
        }
        }else{
             $this->db->where('charges.id', $code);
        $query = $this->db->get('charges');
        }

        return $query->row_array();
    }

    public function getDataAppoint($id) {
        $query = $this->db->where('patients.id', $id)->get('patients');
        return $query->row_array();
    }

    public function search($id) {
        $this->db->select('appointment.*,staff.id as sid,staff.name,staff.surname,patients.id as pid,patients.patient_unique_id');
        $this->db->join('staff', 'appointment.doctor = staff.id', "inner");
        $this->db->join('patients', 'appointment.patient_id = patients.id', 'inner');
        $this->db->where('`appointment`.`doctor`=`staff`.`id`');
        $this->db->where('appointment.patient_id = patients.id');
        $this->db->where('appointment.patient_id=' . $id);
        $query = $this->db->get('appointment');
        return $query->result_array();
    }

    public function getOpdPatient($opd_ipd_no) {
        $query = $this->db->select('opd_details.patient_id,opd_details.opd_no,patients.id as pid,patients.patient_name,patients.age,patients.guardian_name,patients.guardian_address,patients.admission_date,patients.gender,staff.name as doctorname,staff.surname')
                ->join('patients', 'opd_details.patient_id = patients.id')
                ->join('staff', 'staff.id = opd_details.cons_doctor', "inner")
                ->where('opd_no', $opd_ipd_no)
                ->get('opd_details');
        return $query->row_array();
    }

    public function getIpdPatient($opd_ipd_no) {
        $query = $this->db->select('ipd_details.patient_id,ipd_details.ipd_no,patients.id as pid,patients.patient_name,patients.age,patients.guardian_name,patients.guardian_address,patients.admission_date,patients.gender,staff.name as doctorname,staff.surname')
                ->join('patients', 'ipd_details.patient_id = patients.id')
                ->join('staff', 'staff.id = ipd_details.cons_doctor', "inner")
                ->where('ipd_no', $opd_ipd_no)
                ->get('ipd_details');
        return $query->row_array();
    }

    public function getAppointmentDate() {
        $query = $this->db->select('opd_details.appointment_date')->get('opd_details');
    }

    public function deleteOPD($opdid) {
        $this->db->where("id", $opdid)->delete("opd_details");
    }

    public function deleteOPDPatient($id) {
        $this->db->where("patient_id", $id)->delete("opd_details");
    }

    public function deletePatient($id) {       
         $query = $this->db->select('bed.id')
                        ->join('ipd_details', 'ipd_details.bed = bed.id')
                        ->where("ipd_details.patient_id", $id)->where("ipd_details.discharged",'no')->get('bed');

        $result = $query->row_array();
        $bed_id = $result["id"];
        if($bed_id){
        $this->db->where("id", $bed_id)->update('bed', array('is_active' => 'yes'));
        $this->db->where("patient_id", $id)->delete("ipd_details");
        }
        $this->db->where("id", $id)->delete("patients");
        
    }

     public function getOPDCharges($patient_id, $opdid = '') {
        $query = $this->db->select("sum(apply_charge) as charge")->where("patient_id", $patient_id)->where("opd_id", $opdid)->get("opd_patient_charges");
        return $query->row_array();
    }

     public function getOPDvisitCharges($patient_id, $opdid = '') {
        $query = $this->db->select("sum(amount) as vamount")->where("patient_id", $patient_id)->where("opd_id", $opdid)->get("visit_details");
        return $query->row_array();
    }

     public function getOPDbill($patient_id, $opdid = '') {
        $query = $this->db->select("sum(net_amount) as billamount")->where("patient_id", $patient_id)->where("opd_id", $opdid)->get("opd_billing");
        return $query->row_array();
    }

     public function getopdPayment($patient_id, $opdid = '') {
        $query = $this->db->select("sum(paid_amount) as opdpayment")->where("patient_id", $patient_id)->where("opd_id", $opdid)->get("opd_payment");
        return $query->row_array();
    }

    public function patientCredentialReport() {
        $query = $this->db->select('patients.*,users.id as uid,users.user_id,users.username,users.password')
                ->join('users', 'patients.id = users.user_id')
                ->get('patients');
        return $query->result_array();
    }

    public function getPaymentDetail($patient_id) {
        $SQL = 'select patient_charges.amount_due,payment.amount_deposit from (SELECT sum(paid_amount) as `amount_deposit` FROM `payment` WHERE patient_id=' . $this->db->escape($patient_id) . ') as payment ,(SELECT sum(apply_charge) as `amount_due` FROM `patient_charges` WHERE patient_id=' . $this->db->escape($patient_id) . ') as patient_charges';
        $query = $this->db->query($SQL);
        return $query->row();
    }

      public function getPaymentDetailpatient($ipd_id) {
        $SQL = 'select patient_charges.amount_due,payment.amount_deposit from (SELECT sum(paid_amount) as `amount_deposit` FROM `payment` WHERE ipd_id=' . $this->db->escape($ipd_id) . ') as payment ,(SELECT sum(apply_charge) as `amount_due` FROM `patient_charges` WHERE ipd_id=' . $this->db->escape($ipd_id) . ') as patient_charges';
        $query = $this->db->query($SQL);
        return $query->row();
    }

    public function getIpdBillDetails($id, $ipdid) {
        $query = $this->db->where("patient_id", $id)->where("ipd_id", $ipdid)->get("ipd_billing");
        return $query->row_array();
    }

    public function getDepositAmountBetweenDate($start_date, $end_date) {
        $opd_query = $this->db->select('*')->get('opd_details');
        $bloodbank_query = $this->db->select('*')->get('blood_issue');
        $pharmacy_query = $this->db->select('*')->get('pharmacy_bill_basic');

        $opd_result = $opd_query->result();
        $bloodbank_result = $bloodbank_query->result();
        $result_value = $opd_result;

        $return_array = array();
        if (!empty($result_value)) {
            $st_date = strtotime($start_date);
            $ed_date = strtotime($end_date);
            foreach ($result_value as $key => $value) {
                $return = $this->findObjectById($result_value, $st_date, $ed_date);

                if (!empty($return)) {
                    foreach ($return as $r_key => $r_value) {
                        $a = array();
                        $a['amount'] = $r_value->amount;
                        $a['date'] = $r_value->appointment_date;
                        $a['amount_discount'] = 0;
                        $a['amount_fine'] = 0;
                        $a['description'] = '';
                        $a['payment_mode'] = $r_value->payment_mode;
                        $a['inv_no'] = $r_value->patient_id;
                        $return_array[] = $a;
                    }
                }
            }
        }

        return $return_array;
    }

    function findObjectById($array, $st_date, $ed_date) {

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

    public function getEarning($field, $module, $search_field = '', $search_value = '', $search = '') {

        $search_arr = array();
        foreach ($search as $key => $value) {
            $key = $module . "." . $key;
            $search_arr[$key] = $value;
        }       
        if ((!empty($search_field)) && (!empty($search_value))) {

            $this->db->where($search_field, $search_value);
        }
        if (!empty($search_arr)) {

            $this->db->where($search_arr);
        }

        if ($module == 'ipd_billing') {
            $this->db->join("ipd_details", "ipd_billing.ipd_id = ipd_details.id");
        }

        if ($module == 'payment') {
            $this->db->join("ipd_details", "payment.ipd_id = ipd_details.id");
        }


        if ($module == 'opd_details') {
            $this->db->join("patients", "patients.id = opd_details.patient_id");
        }

        if ($module == 'pharmacy_bill_basic') {
            $this->db->join("patients", "patients.id = pharmacy_bill_basic.patient_id");
        }
        if ($module == 'ambulance_call') {
            $this->db->join("patients", "patients.id = ambulance_call.patient_name");
        }

        $query = $this->db->select('sum(' . $field . ') as amount')->get($module);

        $result = $query->row_array();
        return $result["amount"];
    }

    public function getPathologyEarning($search = '') {
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

    public function getRadiologyEarning($search = '') {
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

    public function getOTEarning($search = '') {
        if (!empty($search)) {

            $this->db->where($search);
        }

        $query = $this->db->select('sum(operation_theatre.apply_charge) as amount')
                ->join('operation_theatre', 'operation_theatre.charge_id = charges.id')              
                ->get('charges');
        $result = $query->row_array();

        return $result["amount"];
    }

    public function deleteIpdPatient($id) {
        $query = $this->db->select('bed.id')
                        ->join('ipd_details', 'ipd_details.bed = bed.id')
                        ->where("ipd_details.patient_id", $id)->get('bed');

        $result = $query->row_array();
        $bed_id = $result["id"];
        $this->db->where("id", $bed_id)->update('bed', array('is_active' => 'yes'));       
        $this->db->where("patient_id", $id)->delete('ipd_details');
        $this->db->where("patient_id", $id)->delete('patient_charges');
        $this->db->where("patient_id", $id)->delete('payment');
        $this->db->where("patient_id", $id)->delete('ipd_billing');        
    }

    public function getIncome($date_from, $date_to) {
        $object = new stdClass();

        $query1 = $this->getEarning($field = 'amount', $module = 'opd_details', $search_field = '', $search_value = '', $search = array('appointment_date >=' => $date_from, 'appointment_date <=' => $date_to));
        $amount1 = $query1;

        $query2 = $this->getEarning($field = 'paid_amount', $module = 'payment', $search_field = '', $search_value = '', $search = array('date >=' => $date_from, 'date <=' => $date_to));
        $amount2 = $query2;

        $query3 = $this->getEarning($field = 'net_amount', $module = 'pharmacy_bill_basic', $search_field = '', $search_value = '', $search = array('date >=' => $date_from, 'date <=' => $date_to));
        $amount3 = $query3;

        $query4 = $this->getEarning($field = 'amount', $module = 'blood_issue', $search_field = '', $search_value = '', $search = array('date_of_issue >=' => $date_from, 'date_of_issue <=' => $date_to . " 23:59:59.993"));
        $amount4 = $query4;

        $query5 = $this->getEarning($field = 'amount', $module = 'ambulance_call', $search_field = '', $search_value = '', $search = array('created_at >=' => $date_from, 'created_at <=' => $date_to));
        $amount5 = $query5;

        $query6 = $this->getPathologyEarning(array('pathology_report.reporting_date >=' => $date_from, 'pathology_report.reporting_date <=' => $date_to));
        $amount6 = $query6;

        $query7 = $this->getRadiologyEarning(array('radiology_report.reporting_date >=' => $date_from, 'radiology_report.reporting_date <=' => $date_to));
        $amount7 = $query7;

        $query8 = $this->getOTEarning(array('operation_theatre.date >=' => $date_from, 'operation_theatre.date <=' => $date_to));
        $amount8 = $query8;

        $query9 = $this->getEarning($field = 'amount', $module = 'income', $search_field = '', $search_value = '', $search = array('date >=' => $date_from, 'date <=' => $date_to));
        $amount9 = $query9;
        $query10 = $this->getEarning($field = 'net_amount', $module = 'ipd_billing', $search_field = '', $search_value = '', $search = array('date >=' => $date_from, 'date <=' => $date_to));
        $amount10 = $query10;

        $query11 = $this->getEarning($field = 'net_amount', $module = 'opd_billing', $search_field = '', $search_value = '', $search = array('date >=' => $date_from, 'date <=' => $date_to));
        $amount11 = $query11;

        $query12 = $this->getEarning($field = 'paid_amount', $module = 'opd_payment', $search_field = '', $search_value = '', $search = array('date >=' => $date_from, 'date <=' => $date_to));
        $amount12 = $query12;

        $amount = $amount1 + $amount2 + $amount3 + $amount4 + $amount5 + $amount6 + $amount7 + $amount8 + $amount9 + $amount10 + $amount11 + $amount12;

        $object->amount = $amount;
        return $object;
    }

    public function getBillInfo($id) {
        $query = $this->db->select('staff.name,staff.surname,staff.employee_id,ipd_billing.date as discharge_date')
                ->join('ipd_billing', 'staff.id = ipd_billing.generated_by')
                ->where('ipd_billing.patient_id', $id)
                ->get('staff');
        $result = $query->row_array();
        return $result;
    }

    public function getopdBillInfo($id, $visitid) {
        $query = $this->db->select('staff.name,staff.surname,staff.employee_id,opd_billing.date as discharge_date')
                ->join('opd_billing', 'staff.id = opd_billing.generated_by')
                ->where('opd_billing.patient_id', $id)
                ->where('opd_billing.opd_id', $visitid)
                ->get('staff');
        $result = $query->row_array();
        return $result;
    }

     public function getBillstatus($id, $visitid) {
        $query = $this->db->select('opd_billing.*,visit_details.amount as visitamount,opd_details.amount as amount,patients.id')
                ->join('patients','patients.id=opd_billing.patient_id')
                ->join('opd_details','opd_details.patient_id = opd_billing.id',"left")
                ->join('visit_details','visit_details.patient_id = opd_billing.patient_id',"left")
                ->where('opd_billing.patient_id', $id)
                ->where('opd_billing.opd_id', $visitid)
                ->get('opd_billing');
        $result = $query->row_array();
        return $result;
    }

    public function getStatus($id) {
        $query = $this->db->where("id", $id)->get("patients");
        $result = $query->row_array();
        return $result;
    }

    public function searchPatientNameLike($searchterm) {
        $this->db->select('patients.*')->from('patients');
        $this->db->group_start();
        $this->db->like('patients.patient_name', $searchterm);
        $this->db->group_end();
        $this->db->where('patients.is_active', 'yes');
        $this->db->order_by('patients.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getPatientEmail() {

        $query = $this->db->select("patients.email,patients.id,patients.mobileno,patients.app_key")
                ->join("users", "patients.id = users.user_id")
                ->where("users.role", "patient")
                ->where("patients.is_active", "yes")
                ->get("patients");
        return $query->result_array();
    }

    public function updatebed($data) {
        $this->db->where('ipd_no', $data["ipd_no"])
                ->update('ipd_details', $data);
    }

    public function getVisitDetails($id, $visitid) {
        $query = $this->db->select('opd_details.*,staff.name,staff.surname')
                ->join('patients', 'opd_details.patient_id = patients.id')
                ->join('staff', 'opd_details.cons_doctor = staff.id')
                ->where(array('opd_details.patient_id' => $id, 'opd_details.id' => $visitid))
                ->get('opd_details');
        return $query->row_array();
    }

    public function getpatientDetailsByVisitId($id, $visitid) {
        $query = $this->db->select('visit_details.*,visit_details.amount as apply_charge, opd_details.id as opdid, staff.name,staff.surname,patients.age,patients.month,patients.patient_name,patients.gender,patients.email,patients.mobileno,patients.address,patients.marital_status,patients.blood_group,patients.dob,patients. patient_unique_id')
                ->join('patients', 'visit_details.patient_id = patients.id')
                ->join('opd_details', 'visit_details.opd_no = opd_details.opd_no')
                ->join('staff', 'opd_details.cons_doctor = staff.id')
                ->where(array('opd_details.patient_id' => $id, 'visit_details.id' => $visitid))
                ->get('visit_details');
        $result = $query->row_array();
        if (!empty($result)) {
            $generated_by = $result["generated_by"];
            $staff_query = $this->db->select("staff.name,staff.surname")
                    ->where("staff.id", $generated_by)
                    ->get("staff");
            $staff_result = $staff_query->row_array();
            $result["generated_byname"] = $staff_result["name"] . " " . $staff_result["surname"];
        }

        return $result;
    }

    public function addvisitDetails($opd_data) {
        if (isset($opd_data["id"])) {
            $this->db->where("id", $opd_data["id"])->update("visit_details", $opd_data);
        } else {
            $this->db->insert("visit_details", $opd_data);
        }
    }

    public function getVisitDetailsByOPD($id, $visitid) {
        $query = $this->db->select('visit_details.*,opd_details.id as opdid, staff.name,staff.surname')
                ->join('patients', 'visit_details.patient_id = patients.id')
                ->join('opd_details', 'visit_details.opd_no = opd_details.opd_no')
                ->join('staff', 'opd_details.cons_doctor = staff.id')
                ->where(array('opd_details.patient_id' => $id, 'visit_details.opd_id' => $visitid))
                ->get('visit_details');    
        $result = $query->result_array();

        $i = 0;
        foreach ($result as $key => $value) {
            $opd_id = $value["id"];
            $check = $this->db->where("visit_id", $opd_id)->get('prescription');
            if ($check->num_rows() > 0) {
                $result[$i]['prescription'] = 'yes';
            } else {
                $result[$i]['prescription'] = 'no';
                $userdata = $this->customlib->getUserData();
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

    public function deleteVisit($id) {
        $this->db->where("id", $id)->delete("visit_details");
    }

    public function printVisitDetails($patient_id, $visitid) {

        $query = $this->db->select("patients.*,opd_details.id as opdid,organisation.organisation_name,opd_details.amount as apply_charge, opd_details.id as opdid,opd_details.appointment_date,opd_details.symptoms,opd_details.case_type,opd_details.casualty,opd_details.note_remark,staff.name,staff.surname")
                ->join('opd_details', 'patients.id = opd_details.patient_id')
                ->join('staff', 'staff.id = opd_details.cons_doctor')
                ->join('organisation', 'organisation.id = patients.organisation', 'left')
                ->where("patients.id", $patient_id)
                ->where("opd_details.id", $visitid)
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
    
    public function add_front_patient($data)
    {        
        $this->db->insert('patients', $data);
        $insert_id = $this->db->insert_id();        
        return $insert_id;        
    }
    
    // -------------------------- IPD -------------------------------------    
        
    public function getCharges($ipd_id)
    {
        $query = $this->db->select("sum(apply_charge) as charge")->where("ipd_id", $ipd_id)->get("patient_charges");
        return $query->row_array();
    }

    public function getPayment($patient_id, $ipdid = '')
    {
        $query = $this->db->select("sum(transactions.amount) as payment")->where("ipd_id", $ipdid)->get("transactions");
        return $query->row_array();
    } 
	
	public function getipdtreatmenthistory($patient_id)
    {             
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
    
    public function getReferenceByIpdId($id)
    {
        $result = $this->db
            ->select("case_reference_id")
            ->where("id", $id)
            ->get("ipd_details")
            ->row();
         if(!empty($result->case_reference_id)){   
            return $result->case_reference_id;
         }else{
             return false;
         }
    }    
    
    public function getdatanursenote($ipd_id)
    {         
        $query          = $this->db->select('nurse_note.*,staff.name,staff.surname,staff.employee_id')->join('staff', 'staff.id = nurse_note.staff_id', "LEFT")->where("nurse_note.ipd_id", $ipd_id)->get("nurse_note");
        $result         = $query->result_array();
        
            foreach($result as  $key => $valuee){                 
                $customfield =  $this->customfield_model->getcustomfieldswithvalue($valuee['id'],'ipdnursenote');    
                $result[$key]['customfield'] = $customfield;                 
                $notecomment    = $this->patient_model->getnurenotecomment($ipd_id, $valuee['id']);
                $result[$key]['staffcomment'] = $notecomment;
            }          
            
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
    
    public function getallinvestigation($case_reference_id)
    {       
        $query = $this->db->query("select pathology_report.id as report_id,pathology_report.pathology_bill_id,pathology.test_name,pathology.short_name,pathology.report_days,pathology.id as pid,pathology.charge_id as cid,staff.name,staff.surname,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`, 'pathology' as type, pathology_report.pathology_center as test_center, pathology_report.collection_date,pathology_report.reporting_date,pathology_report.parameter_update from pathology_billing inner join pathology_report on pathology_report.pathology_bill_id = pathology_billing.id inner join pathology on pathology_report.pathology_id = pathology.id left join staff on staff.id = pathology_billing.doctor_id left join staff as collection_specialist_staff on collection_specialist_staff.id = pathology_report.collection_specialist left join staff as approved_by_staff on approved_by_staff.id = pathology_report.approved_by where pathology_billing.case_reference_id= ".$case_reference_id . " 
        union all 
        select radiology_report.id as report_id, radiology_report.radiology_bill_id,radio.test_name,radio.short_name,radio.report_days,radio.id as pid,radio.charge_id as cid,staff.name,staff.surname,collection_specialist_staff.name as `collection_specialist_staff_name`,collection_specialist_staff.surname as `collection_specialist_staff_surname`,collection_specialist_staff.employee_id as `collection_specialist_staff_employee_id`,approved_by_staff.name as `approved_by_staff_name`,approved_by_staff.surname as `approved_by_staff_surname`,approved_by_staff.employee_id as `approved_by_staff_employee_id`, 'radiology' as type,radiology_report.radiology_center as test_center,radiology_report.collection_date,radiology_report.reporting_date,radiology_report.parameter_update  from radiology_billing inner join radiology_report on radiology_report.radiology_bill_id = radiology_billing.id inner join radio on radiology_report.radiology_id = radio.id left join staff on staff.id = radiology_report.consultant_doctor left join staff as collection_specialist_staff on collection_specialist_staff.id = radiology_report.collection_specialist left join staff as approved_by_staff on approved_by_staff.id = radiology_report.approved_by where radiology_billing.case_reference_id=".$case_reference_id." "  );
        $result = $query->result_array();
        
        foreach($result as $key => $result_value){
            if($result_value['collection_specialist_staff_name'] == ''){
                $result[$key]['collection_specialist_staff_name'] = '';
            }
            if($result_value['collection_specialist_staff_surname'] == ''){
                $result[$key]['collection_specialist_staff_surname'] = '';
            }
            if($result_value['collection_specialist_staff_employee_id'] == ''){
                $result[$key]['collection_specialist_staff_employee_id'] = '';
            }
            if($result_value['approved_by_staff_name'] == ''){
                $result[$key]['approved_by_staff_name'] = '';
            }
            if($result_value['approved_by_staff_surname'] == ''){
                $result[$key]['approved_by_staff_surname'] = '';
            }
            if($result_value['approved_by_staff_employee_id'] == ''){
                $result[$key]['approved_by_staff_employee_id'] = '';
            }
        }
        return $result ;
    }
    
    public function getmedicationdetailsbydate($ipdid)
    {
        $this->db->select('medication_report.pharmacy_id,medication_report.date,pharmacy.   medicine_category_id');
        $this->db->join('pharmacy', 'pharmacy.id = medication_report.pharmacy_id', 'left');
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
                        $medication            = array();
                        $result_medication[$i]['name']        = $m_value['medicine_name'];
                        $result_medication[$i]['dose_list'][] = $m_value;
                    }
                }
                $i++;
            }
        }

        return $result_medication;
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
    
    public function patientipdlist($patient_id)
    {
        $this->db->select('ipd_details.*,patients.patient_name,patients.gender,patients.mobileno,staff.name,staff.surname,staff.employee_id,bed_group.name as bedgroup_name,bed.name as bed_name,floor.name as floor_name')->from('ipd_details');
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
    
    public function getipdpatientdetails($patient_id)
    {        
        $this->db->select('patients.*,bed.name as bed_name,bed_group.name as bedgroup_name, floor.name as floor_name,ipd_details.date,ipd_details.id as ipdid,ipd_details.credit_limit as ipdcredit_limit,ipd_details.case_type,staff.name,staff.surname
              ')->from('patients');
        $this->db->join('ipd_details', 'patients.id = ipd_details.patient_id', "inner");
        $this->db->join('staff', 'staff.id = ipd_details.cons_doctor', "inner");
        $this->db->join('bed', 'ipd_details.bed = bed.id', "left");
        $this->db->join('bed_group', 'ipd_details.bed_group_id = bed_group.id', "left");
        $this->db->join('floor', 'floor.id = bed_group.floor', "left");
         $this->db->where("patients.id", $patient_id);
        $this->db->where('patients.is_active', 'yes');
        $this->db->where('ipd_details.discharged', 'no');        
        $this->db->order_by('ipd_details.id', "desc");
        $query = $this->db->get();       
        return $query->row_array();        
    }  
      
    public function getpatientopddetails($patientid, $visitid = '')
    {       
        $this->db->select('opd_details.id as opdid,opd_details.case_reference_id,sum(transactions.amount) as payamount,visit_details.*,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,patients.id as pid,patients.patient_name,patients.age,patients.month,patients.day,patients.dob,patients.gender')->from('opd_details');
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
                }

                $i++;
            }
            return $result;
        }
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
            ->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left")->join('staff', 'staff.id = visit_details.cons_doctor', "inner")->join('patients', 'patients.id = opd_details.patient_id', "inner")->join('consult_charges', 'consult_charges.doctor=visit_details.cons_doctor', 'left')
            ->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left')->order_by('visit_details.id', 'desc')->where('opd_details.patient_id', $patient_id)
            ->where('opd_details.discharged', 'yes')->group_by('visit_details.opd_details_id', '')->limit(5)->from('opd_details')->get()->result_array();

        $patient_details['patient']['visitdetails'] = $this->db
        ->select('opd_details.case_reference_id,opd_details.id as opd_id,opd_details.patient_id as patientid,opd_details.is_ipd_moved,max(visit_details.id) as visit_id,visit_details.appointment_date,visit_details.refference,visit_details.symptoms,patients.id as pid,patients.patient_name,staff.id as staff_id,staff.name,staff.surname,staff.employee_id,consult_charges.standard_charge,patient_charges.apply_charge' )
        ->join('visit_details', 'opd_details.id = visit_details.opd_details_id', "left")->join('staff', 'staff.id = visit_details.cons_doctor', "inner")->join('patients', 'patients.id = opd_details.patient_id', "inner")->join('consult_charges', 'consult_charges.doctor=visit_details.cons_doctor', 'left')
        ->join('patient_charges', 'opd_details.id=patient_charges.opd_id', 'left')->order_by('visit_details.id', 'desc')->where('opd_details.patient_id', $patient_id)
        ->where('opd_details.discharged', 'no')->group_by('visit_details.opd_details_id', '')->order_by('visit_details.opd_details_id', 'desc')
        ->limit(5)->from('opd_details')->get()->result_array();
            return $patient_details ;
     
    }

    public function getpatientoverviewbycaseid($case_reference_id,$opd_id){

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
        $patient_details['patient']['case_reference_id'] = $case_reference_id ;
        $patient_details['patient']['opd_id'] = $opd_id ;
      
       return $patient_details;    
      }  
    
}

?>