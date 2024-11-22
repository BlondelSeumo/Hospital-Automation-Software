<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class dashboard extends Patient_Controller
{
    public $setting;
    public $payment_method;
    public $patient_data;

    public function __construct()
    {
        parent::__construct();
        $this->payment_method = $this->paymentsetting_model->getActiveMethod();
        $this->patient_data   = $this->session->userdata('patient');
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->appointment_status = $this->config->item('appointment_status');
        $this->marital_status     = $this->config->item('marital_status');
        $this->yesno_condition    = $this->config->item('yesno_condition');
        $this->payment_mode       = $this->config->item('payment_mode');
        $this->search_type        = $this->config->item('search_type');
        $this->blood_group        = $this->config->item('bloodgroup');
        $this->load->model('conferencehistory_model');
        $this->load->model('conference_model');
        $this->load->model('customfield_model');
        $this->load->model('transaction_model');
        $this->load->model('appoint_priority_model');
        $this->load->model('finding_model');
        $this->load->model('vital_model');
        $this->load->model('antenatal_model');
        $this->load->helper('customfield_helper');
        $this->load->library('Customlib');
        $this->load->helper('custom');
        $this->load->library('datatables');
        $this->config->load("image_valid");
        $this->load->library('system_notification');
        $this->load->library('mailsmsconf');
        $this->charge_type         = $this->customlib->getChargeMaster();
        $data["charge_type"]       = $this->charge_type;
        $this->conference_setting  = $this->setting_model->getzoomsetting();
        $this->time_format         = $this->customlib->getHospitalTimeFormat();
        $this->recent_record_count = 5;
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/patient/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/patient/footer', $data);
    }

    public function findingchart()
    {
        $patient_id     = $this->customlib->getPatientSessionUserID();
        $finding_types  = $this->finding_model->getAllFinding();
        $colors=rand_color();

        $findings_array = array();
        if (!empty($finding_types)) {

            foreach ($finding_types as $finding_type_key => $finding_type_value) {
               
                $total_counts     = $this->finding_model->getFindingCountbyPatients($patient_id, $finding_type_value['name']);
                $findings_array[] = array(
                    'total_counts' => $total_counts,
                    'finding_name' => $finding_type_value['name'],
                );

            }
            usort($findings_array, 'sortInnerData');
        }
        $finding_types   = [];
        $backgroundColor = [];
        $data            = [];
        $top_ten_array   = array_slice($findings_array, 0, 10);
        if (!empty($top_ten_array)) {
            foreach ($top_ten_array as $array_key => $array_value) {            
                $finding_types[]   = $array_value['finding_name'];
                $backgroundColor[] = $colors[$array_key];
                $data[]            = $array_value['total_counts'];
            }
        }       

        $datasets = [
            array(
                "backgroundColor" => $backgroundColor,
                "data"            => $data,
            ),
        ];

        $array = array(
            'labels'  => $finding_types,
            'dataset' => $datasets,
        );

        echo json_encode($array);
    }

    public function symptomchart()
    {
        $patient_id      = $this->customlib->getPatientSessionUserID();
        $symptoms        = $this->symptoms_model->get();
        $labels          = [];
        $data            = [];
        $backgroundColor = [];
        $symptoms_array  = array();
        $colors=rand_color();
        if (!empty($symptoms)) {
            foreach ($symptoms as $symptom_key => $symptom_value) {
                $total_counts     = $this->symptoms_model->getSymptomCountbyPatients($patient_id, $symptom_value['symptoms_title']);
                $symptoms_array[] = array(
                    'total_counts'   => $total_counts,
                    'symptoms_title' => $symptom_value['symptoms_title'],
                );
            }

            usort($symptoms_array, 'sortInnerData');
        }

        $top_ten_array = array_slice($symptoms_array, 0, 10);

        if (!empty($top_ten_array)) {
            foreach ($top_ten_array as $array_key => $array_value) {
                $labels[] = $array_value['symptoms_title'];
                $data[]            = $array_value['total_counts'];
                $backgroundColor[] = $colors[$array_key];;
            }
        }

        $datasets = [
            array(
                "backgroundColor" => $backgroundColor,
                "data"            => $data,
            ),
        ];

        $array = array(
            'labels'  => $labels,
            'dataset' => $datasets,
        );

        echo json_encode($array);
    }
  
    public function yearchart(){ 
        $patient_id        = $this->customlib->getPatientSessionUserID();
        $patient_data      = $this->patient_model->getpatientbyid($patient_id);
        $patient_created   = $patient_data['created_at'];
        $create_year       = date('Y', (strtotime($patient_created)-60*60*24*365));
        $current_year      = date('Y');
        $opd_visits        = $this->patient_model->getpatientOPDYearCounter($patient_id, $create_year);
        $ipd_visits        = $this->patient_model->getpatientIPDYearCounter($patient_id, $create_year);
        $pharmacy_visits   = $this->pharmacy_model->getpatientPharmacyYearCounter($patient_id, $create_year);
        $pathology_visits  = $this->pathology_model->getpatientPathologyYearCounter($patient_id, $create_year);
        $radiology_visits  = $this->radio_model->getpatientRadiologyYearCounter($patient_id, $create_year);
        $bloodissue_visits = $this->bloodissue_model->getpatientBloodYearCounter($patient_id, $create_year);      
        $ambulance_visits  = $this->ambulance_model->getpatientAmbulanceYearCounter($patient_id, $create_year);
        $year_range        = range($create_year, $current_year, 1);
        $empty_array       = array_fill(0, count($year_range), 0);
        $datasets          = [
            
        ];

       if ($this->module_lib->hasPatientActive('opd')) {
            $opd_data = array();
            if (!empty($opd_visits)) {               
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $opd_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $opd_visits, 'year');
                        $total_visits = $opd_visits[$result_key]['total_visits'];
                    }
                    $opd_data[] = $total_visits;
                }          
            }
            if(empty($opd_data)){
                $opd_data=$empty_array;
            }
            $datasets[] = array(           
                'data'        => $opd_data,
                'label'       => $this->lang->line('opd'),
                'borderColor' => "#438FFF",
                'fill'        => false,
            );
        }
        
        if ($this->module_lib->hasPatientActive('pharmacy')) {
            $pharmacy_data = array();
            if (!empty($pharmacy_visits)) {            
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $pharmacy_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $pharmacy_visits, 'year');
                        $total_visits = $pharmacy_visits[$result_key]['total_visits'];
                    }
                    $pharmacy_data[] = $total_visits;
                }         
            }
            if(empty($pharmacy_data)){
                $pharmacy_data=$empty_array;
            }
            $datasets[] = array(            
                'data'        => $pharmacy_data,
                'label'       => $this->lang->line('pharmacy'),
                'borderColor' => "#016E51",
                'fill'        => false,
            );
        }

        if ($this->module_lib->hasPatientActive('pathology')) {
            $pathology_data = array();
            if (!empty($pathology_visits)) {            
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $pathology_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $pathology_visits, 'year');
                        $total_visits = $pathology_visits[$result_key]['total_visits'];
                    }
                    $pathology_data[] = $total_visits;
                }        
            }
            if(empty($pathology_data)){
                $pathology_data=$empty_array;
            }
            $datasets[] = array(           
                'data'        => $pathology_data,
                'label'       => $this->lang->line('pathology'),
                'borderColor' => "#A80000",
                'fill'        => false,
            );
        }

        if ($this->module_lib->hasPatientActive('radiology')) {
            $radiology_data = array();
            if (!empty($radiology_visits)) {            
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $radiology_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $radiology_visits, 'year');
                        $total_visits = $radiology_visits[$result_key]['total_visits'];
                    }
                    $radiology_data[] = $total_visits;
                }
            }
            if(empty($radiology_data)){
                $radiology_data=$empty_array;
            }
            $datasets[] = array(            
                'data'        => $radiology_data,
                'label'       => $this->lang->line('radiology'),
                'borderColor' => "#12239E",
                'fill'        => false,
            );
        }

        if ($this->module_lib->hasPatientActive('blood_bank')) {
            $bloodissue_data = array();
            if (!empty($bloodissue_visits)) {                 
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $bloodissue_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $bloodissue_visits, 'year');
                        $total_visits = $bloodissue_visits[$result_key]['total_visits'];
                    }
                    $bloodissue_data[] = $total_visits;
                }              
            }
            if(empty($bloodissue_data)){
                $bloodissue_data=$empty_array;
            }
            $datasets[] = array(               
                'data'        => $bloodissue_data,
                'label'       => $this->lang->line('blood_bank'),
                'borderColor' => "#D82C20",
                'fill'        => false,
            );
        }

        if ($this->module_lib->hasPatientActive('ambulance')) {
            $ambulance_data = array();
            if (!empty($ambulance_visits)) {                
                foreach ($year_range as $year_key => $year_value) {
                    $total_visits = 0;
                    if (!is_null(searchForKeyData($year_value, $ambulance_visits, 'year'))) {
                        $result_key = searchForKeyData($year_value, $ambulance_visits, 'year');
                        $total_visits = $ambulance_visits[$result_key]['total_visits'];
                    }
                    $ambulance_data[] = $total_visits;
                }                
            }
            if(empty($ambulance_data)){
                $ambulance_data=$empty_array;
            }                
            $datasets[] = array(            
                'data'        => $ambulance_data,
                'label'       => $this->lang->line('ambulance'),
                'borderColor' => "#FFA500",
                'fill'        => false,
            );
         }

        $array = array(
            'labels'  => $year_range,
            'dataset' => $datasets,
        );

        echo json_encode($array);
    }
    
    public function index()
    {
        $this->session->set_userdata('top_menu', 'dashboard');
        $data                      	= array();
        $patient_id                	= $this->customlib->getPatientSessionUserID();
        $patient_detail        		= $this->patient_model->getpatientDetails($patient_id);
        $total_visits              	= $this->patient_model->totalVisit($patient_id);
        $total_ipd                 	= $this->patient_model->totalPatientIPD($patient_id);
        $total_pharmacy            	= $this->pharmacy_model->totalPatientPharmacy($patient_id);
        $total_pathology           	= $this->pathology_model->totalPatientPathology($patient_id);
        $total_radiology           	= $this->radio_model->totalPatientRadiology($patient_id);
        $total_blood_issue         	= $this->bloodissue_model->totalPatientBloodIssue($patient_id);
        $total_ambulance           	= $this->ambulance_model->totalPatientAmbulance($patient_id);
        $data['insurance_validity'] = $patient_detail ['insurance_validity'];
        $data['total_ambulance']   	= $total_ambulance;
        $data['total_blood_issue'] 	= $total_blood_issue;
        $data['total_radiology']   	= $total_radiology;
        $data['total_pathology']   	= $total_pathology;
        $data['total_pharmacy']    	= $total_pharmacy;
        $data['total_ipd']         	= $total_ipd;
        $data['total_visits']      	= $total_visits;
        $this->load->view("layout/patient/header");
        $this->load->view("patient/dashboard", $data);
        $this->load->view("layout/patient/footer");
    }

    public function profile()
    {
        $this->session->set_userdata('top_menu', 'profile');
        $id              = $this->patient_data['patient_id'];
        $data["id"]      = $id;
        $doctors         = $this->staff_model->getStaffbyrole(3);
        $data["doctors"] = $doctors;
        $result          = array();
        $opd_details     = array();
        $timeline_list   = array();
        if (!empty($id)) {
            $result        = $this->patient_model->getpatientDetails($id);
            $opd_details   = $this->patient_model->getpatientopddetails($id);           
            $timeline_list = $this->timeline_model->getPatientTimeline($id, $timeline_status = 'yes');
            $prescription_details = $this->prescription_model->getopdvisitPrescription($id);
        }

        foreach ($opd_details as $key => $opdvalue) {
            $data['opdconferences'] = $this->conference_model->getconfrencebyopd($opdvalue['staff_id'], $id);
        }
        $data["result"]              = $result;
        $data["prescription_detail"] = $prescription_details;
        $data["opd_details"]         = $opd_details;
        $data["timeline_list"]       = $timeline_list;
        $data['fields']              = $this->customfield_model->get_custom_fields('opd', '', '', '', 1);
        $data['investigations']      = $this->patient_model->allinvestigationbypatientid($id);
        $data['recent_record_count'] = 5;
        $data['patientdetails'] = $this->patient_model->getpatientoverview($id);
        $data['timeformat']     = $this->time_format;
        $data['patientcurrentvital']    = $this->vital_model->getcurrentvitals($id);					
		$data['patient_vital_date']	=	$patient_vital_date = 	$this->vital_model->getpatientvitaldate($id);			
        $data['vital_list']     = $vital_list = $this->vital_model->getvitallist();
 
		foreach($patient_vital_date as $patient_vital_date_result){
			
			if($patient_vital_date_result['messure_date']){
				$messure_date	=	date('Y-m-d', strtotime($patient_vital_date_result['messure_date']));	 
			}else{
				$messure_date	= '';
			}
			
			foreach($vital_list as $vital_list_result){				
				$vital_id = $vital_list_result['id'];				
				$patient_vital[$messure_date][$vital_id]  = $this->vital_model->getpatientsvital($id,$vital_id,$messure_date);				 
			}
		}			
		 
		if(!empty($patient_vital)){
			$data['patientvital']	=	$patient_vital;
		}else{
			$data['patientvital']	=	'';
		}
        
        $this->load->view("layout/patient/header");
        $this->load->view("patient/profile", $data);
        $this->load->view("layout/patient/footer");
    }

    public function download($doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/pathology_report/" . $doc;
        $data     = file_get_contents($filepath);
        force_download($doc, $data);
    }

    public function downloadPathologyReport($report_id)
    {
        $report = $this->pathology_model->getPatientPathologyReportDetails($report_id);
        $this->load->helper('download');
        $filepath    = $report->pathology_report;
        $report_name = $report->report_name;
        $data        = file_get_contents($filepath);
        force_download($report_name, $data);
    }

    public function downloadRadiologyReport($report_id)
    {
        $report = $this->radio_model->getPatientRadiologyReportDetails($report_id);
        $this->load->helper('download');
        $filepath    = $report->radiology_report;
        $report_name = $report->report_name;
        $data        = file_get_contents($filepath);
        force_download($report_name, $data);
    }

    public function printPatientPathologyReportDetail()
    {
        $id                    = $this->input->post('id');
        $data['id']            = $id;
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $result                = $this->pathology_model->getPatientPathologyReportDetails($id);
        $data['bill_prefix'] = $this->customlib->getPatientSessionPrefixByType('pathology_billing');
        $data['result']      = $result;

        $page = $this->load->view('patient/pathology/_printPatientReportDetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printPatientRadiologyReportDetail()
    {
        $id                    = $this->input->post('id');
        $data['id']            = $id;
        $print_details         = $this->printing_model->get('', 'radiology');
        $data['print_details'] = $print_details;
        $data['bill_prefix'] = $this->customlib->getPatientSessionPrefixByType('radiology_billing');
        $result              = $this->radio_model->getPatientRadiologyReportDetails($id);
        $data['result']      = $result;

        $page = $this->load->view('patient/radiology/_printPatientReportDetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getDetails()
    {
        $id = $this->input->post("patient_id");
        $visitid = $this->input->post("visitid");
        $result  = $this->patient_model->getpatientOpdDetails($id, $visitid);
        $result['age'] = $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']);

        if ($result['symptoms']) {
            $result['symptoms'] = nl2br($result['symptoms']);
        }
        $appointment_date           = $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date'], $this->time_format);
        $result["appointment_date"] = $appointment_date;
        $result["patient_name"]     = composePatientName($result['patient_name'], $result['patient_id']);
        $result["doctor_name"]      = composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']);
        echo json_encode($result);
    }

    public function getopdDetails()
    {
        $data           = array();
        $visitid        = $this->input->post("visit_id");
        $result         = $this->patient_model->getopdvisitDetailsbyvisitid($visitid);
        $data['fields'] = $this->customfield_model->get_custom_fields('opd', '', '', '', 1);
        $data['result'] = $result;
        $page           = $this->load->view("patient/_getopdDetails", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function patient_discharge()
    {
        $case_id                = $this->input->post('case_reference_id');
        $data['case_id']        = $case_id;
        $patient                = $this->patient_model->getDetailsByCaseId($case_id);
        $type                   = $this->input->post('module_type');
        $data['result']         = $patient;
        $data['discharge_card'] = $this->patient_model->get_dischargeCard(array('case_reference_id' => $case_id));
        $page = $this->load->view('patient/bill/_patient_discharge', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function print_dischargecard()
    {
        $print_details = $this->printing_model->get('', 'paymentreceipt');
        $id            = $this->input->post('id');
        $case_id       = $this->input->post('case_id');
        $patient       = $this->patient_model->getDetailsByCaseId($case_id);

        $data['print_details'] = $print_details;
        $data['case_id']       = $case_id;
        $data['result']        = $patient;
        $type                  = $this->input->post('module_type');
        if ($type == 'bill') {
            $data['discharge_card'] = $this->patient_model->get_dischargeCard(array('ipd_details_id' => $patient['ipdid']));
        } elseif ($type == 'ipd') {
            $data['discharge_card'] = $this->patient_model->get_dischargeCard(array('ipd_details_id' => $patient['ipdid']));
        } elseif ($type == 'opd') {
            $data['discharge_card'] = $this->patient_model->get_dischargeCard(array('opd_details_id' => $patient['opdid']));
        }

        $data['deathrecord']   = array();
        $data['patient_id']    = $patient['patient_id'];
        $data['guardian_name'] = $patient['guardian_name'];
        if (!empty($data['discharge_card']) && $data['discharge_card']['discharge_status'] == '1') {
            $death_record = $this->birthordeath_model->getDeDetailsbycaseId($case_id);
            $id           = $death_record['id'];
            $this->load->helper('customfield_helper');
            $cutom_fields_data         = get_custom_table_values($id, 'death_report');
            $deathrecord               = $this->birthordeath_model->getDeDetails($id);
            $deathrecord["death_date"] = $this->customlib->YYYYMMDDHisTodateFormat($deathrecord['death_date'], $this->time_format);
            $deathrecord['field_data'] = $cutom_fields_data;
            if ($deathrecord['guardian_name'] != '') {
                $data['guardian_name'] = $deathrecord['guardian_name'];
            }
            $data['deathrecord'] = $deathrecord;
        }
        $page = $this->load->view('patient/bill/_printDischargeCard', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getvisitDetails()
    {
        $visitid                    = $this->input->post("visitid");
        $result                     = $this->patient_model->getopdvisitDetailsbyvisitid($visitid);
        $result['opd_no']           = $this->customlib->getPatientSessionPrefixByType('opd_no') . $result['opdid'];
        $result['appointment_date'] = date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['appointment_date']));
        $result['doctor_name']      = composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']);
        $result['patient_name']     = composePatientName($result['patient_name'], $result['patient_id']);
        $result['age']              = $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']);
        echo json_encode($result);
    }

    public function getopdrecheckupDetails()
    {       
        $data           = array();
        $visitid        = $this->input->post("visit_id");
        $result         = $this->patient_model->getopdvisitrecheckupDetailsbyvisitid($visitid);
        $data['fields'] = $this->customfield_model->get_custom_fields('opdrecheckup');
        $data['result'] = $result;
        $page           = $this->load->view("patient/_getopdrecheckupDetails", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function appointment()
    {

        if (!$this->module_lib->hasPatientActive('my_appointments')) {
            access_denied_patient();
        }

        $this->session->set_userdata('top_menu', 'myprofile');
        $id                         = $this->patient_data['patient_id']; 
        $data["id"]                 = $id;
        $result                     = $this->patient_model->getDataAppoint($id);
        $data["result"]             = $result;
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $data["yesno_condition"]    = $this->yesno_condition;
        $specialist                 = $this->staff_model->getSpecialist();
        $data["specialist"]         = $specialist;
        $data["appointment_status"] = $this->appointment_status;
        $resultlist         = $this->patient_model->search($id);
        
        foreach($resultlist as $key => $value){
            $specialist = $this->staff_model->getProfile($value['doctor']);           
            $str_arr = explode (",", $specialist['specialist']); 
            $specialistname = array();
            foreach($str_arr as $value){            
                $specialistname[] = $this->specialist_model->getspecialistType($value);
            }             
            $resultlist[$key]['staff_specialist_name'] = $specialistname;
        }
        
        $data['resultlist'] = $resultlist;        
        $data['fields']             = $this->customfield_model->get_custom_fields('appointment', '', '', '', 1);
        $data['payment_method']     = $this->payment_method;
        $data['appoint_priority_list'] = $this->appoint_priority_model->appoint_priority_list();
        $this->load->view("layout/patient/header"); 
        $this->load->view("patient/appointment", $data);
        $this->load->view("layout/patient/footer");
    }
 
    public function getdoctor()
    {
        $spec_id = $this->input->post('id');
        $active  = $this->input->post('active');
        $result  = $this->staff_model->getdoctorbyspecilist($spec_id);
        echo json_encode($result);
    }

    public function bloodBankStatus()
    {
        $data['bloodGroup'] = $this->bloodbankstatus_model->getBloodGroup();
        $this->load->view("layout/patient/header");
        $this->load->view("patient/bloodBankStatus", $data);
        $this->load->view("layout/patient/footer");
    }

    public function bloodbank()
    {
        if (!$this->module_lib->hasPatientActive('blood_bank')) {
            access_denied_patient();
        }
        $this->session->set_userdata('top_menu', 'blood_bank');
        $patient_id                = $this->patient_data['patient_id'];
        $data["id"]                = $patient_id;
        $data['result']            = $this->patient_model->getpatientDetails($patient_id);
        $data['fields']            = $this->customfield_model->get_custom_fields('component_issue', '', '', '', 1);
        $data['blood_issuefields'] = $this->customfield_model->get_custom_fields('blood_issue', '', '', '', 1);
        $data['resultlist']        = $this->bloodbankstatus_model->getBloodbank($patient_id);
        $this->load->view("layout/patient/header");
        $this->load->view("patient/bloodbank", $data); 
        $this->load->view("layout/patient/footer");
    }

    public function liveconsult()
    {
        if (!$this->module_lib->hasPatientActive('live_consultation')) { 
            access_denied_patient();
        }
        $this->session->set_userdata('top_menu', 'live_consult');
        $patient_id          = $this->patient_data['patient_id'];
        $data["id"]          = $patient_id;
        $data['conferences'] = $this->conference_model->getconfrencebypatient($patient_id);
        $this->load->view("layout/patient/header");
        $this->load->view("patient/liveconsult", $data);
        $this->load->view("layout/patient/footer");
    }

    public function pharmacyvalidate()
    {
        $this->form_validation->set_rules('deposit_amount', $this->lang->line('amount'), 'required|valid_amount|trim|xss_clean|greater_than[0]');
        $this->form_validation->set_rules('payment_for', $this->lang->line('payment_for'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'deposit_amount' => form_error('deposit_amount'),
                'payment_for'    => form_error('payment_for'),
            );
            $array = array('status' => 0, 'error' => $data);
        } else {
            $array = array('status' => 1);
        }
        echo json_encode($array);
    }

    public function paymentvalidate()
    {
        $this->form_validation->set_rules('deposit_amount', $this->lang->line('amount'), 'required|valid_amount|trim|xss_clean|greater_than[0]');
        $this->form_validation->set_rules('payment_for', $this->lang->line('payment_for'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'deposit_amount' => form_error('deposit_amount'),
                'payment_for'    => form_error('payment_for'),
            );
            $array = array('status' => 0, 'error' => $data);
        } else {
            $array = array('status' => 1);
        }
        echo json_encode($array);
    }

    public function pathologyvalidate()
    {
        $this->form_validation->set_rules('deposit_amount', $this->lang->line('amount'), 'required|valid_amount|trim|xss_clean|greater_than[0]');
        $this->form_validation->set_rules('payment_for', $this->lang->line('payment_for'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'deposit_amount' => form_error('deposit_amount'),
                'payment_for'    => form_error('payment_for'),
            );
            $array = array('status' => 0, 'error' => $data);
        } else {
            $array = array('status' => 1);
        }
        echo json_encode($array);
    }

    public function radiovalidate()
    {
        $this->form_validation->set_rules('deposit_amount', $this->lang->line('amount'), 'required|valid_amount|trim|xss_clean|greater_than[0]');
        $this->form_validation->set_rules('payment_for', $this->lang->line('payment_for'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'deposit_amount' => form_error('deposit_amount'),
                'payment_for'    => form_error('payment_for'),
            );
            $array = array('status' => 0, 'error' => $data);
        } else {
            $array = array('status' => 1);
        }
        echo json_encode($array);
    }

    public function getlivestatus()
    {
        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'id' => form_error('id'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $conference_id              = $this->input->post('id');
            $live                       = $this->conference_model->get($conference_id);
            $data['conference_setting'] = $this->conference_setting;
            if ($live->api_type == "global") {
                $zoomsetting = $this->setting_model->getzoomsetting();
                if (!empty($zoomsetting)) {
                    $zoom_api_key    = $zoomsetting->zoom_api_key;
                    $zoom_api_secret = $zoomsetting->zoom_api_secret;
                }
            } else {
                $staff           = $this->staff_model->get($live->created_id);
                $zoom_api_key    = $staff['zoom_api_key'];
                $zoom_api_secret = $staff['zoom_api_secret'];
            }
            $params = array(
                'zoom_api_key'    => $zoom_api_key,
                'zoom_api_secret' => $zoom_api_secret,
            );
            $this->load->library('zoom_api', $params);
           
            $meetingID               = json_decode($live->return_response)->id;           
            $api_Response            = $this->zoom_api->getMeeting($meetingID);
            $data['api_Response']    = $api_Response;           
            $data['live']            = $live;
            $data['live_url']        = json_decode($live->return_response);
            $data['page']            = $this->load->view('patient/_livestatus', $data, true);
            $array                   = array('status' => '1', 'page' => $data['page']);
            echo json_encode($data);           
        }
    }

    public function join($id)
    {
        $zoom_api_key    = "";
        $zoom_api_secret = "";
        $leaveUrl        = "patient/dashboard/liveconsult";
        $live            = $this->conference_model->get($id);
        if ($live->api_type == "global") {
            $zoomsetting = $this->setting_model->getzoomsetting();
            if (!empty($zoomsetting)) {
                $zoom_api_key    = $zoomsetting->zoom_api_key;
                $zoom_api_secret = $zoomsetting->zoom_api_secret;
            }
        } else {
            $staff           = $this->staff_model->get($live->created_id);
            $zoom_api_key    = $staff['zoom_api_key'];
            $zoom_api_secret = $staff['zoom_api_secret'];
        }
        $meetingID                = json_decode($live->return_response)->id;
        $data['zoom_api_key']     = $zoom_api_key;
        $data['zoom_api_secret']  = $zoom_api_secret;
        $data['meetingID']        = $meetingID;
        $data['meeting_password'] = $live->password;
        $data['leaveUrl']         = $leaveUrl;
        $data['title']            = $live->title;
        $data['host']             = ($live->create_for_surname == "") ? $live->create_for_name : $live->create_for_name . " " . $live->create_for_surname;
        $data['name']             = $this->customlib->getPatientSessionUserName();
        $patient_id               = $this->customlib->getPatientSessionUserID();
        $data_insert              = array(
            'conference_id' => $id,
            'patient_id'    => $patient_id,
        );

        $this->conferencehistory_model->updatehistory($data_insert, 'patient');
        $this->load->view('patient/join', $data);
    }

    public function add_history()
    {
        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'id' => form_error('id'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $patient_id  = $this->customlib->getPatientSessionUserID();
            $data_insert = array(
                'conference_id' => $this->input->post('id'),
                'patient_id'    => $patient_id,
            );
            $this->conferencehistory_model->updatehistory($data_insert, 'patient');
            $array = array('status' => 1, 'error' => '');
            echo json_encode($array);
        }
    }

    public function getBillDetailsBloodbank($id)
    {
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details         = $this->printing_model->get('', 'bloodbank');
        $data["print_details"] = $print_details;
        $result                = $this->bloodbankstatus_model->getBillDetailsBloodbank($id);
        $data['result']        = $result;
        $this->load->view('patient/printBillBloodbank', $data);
    }

    public function bookAppointment()
    {
        $custom_fields = $this->customfield_model->getByBelong('appointment');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[appointment][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $this->form_validation->set_rules('date', $this->lang->line("date"), 'required');
        $this->form_validation->set_rules('message', $this->lang->line("message"), 'required');
        $this->form_validation->set_rules('doctor', $this->lang->line("doctor"), 'required');
        $this->form_validation->set_rules('live_consult', $this->lang->line("live_consultation"), 'required');
        $this->form_validation->set_rules('appointment_status', $this->lang->line("appointment_status"), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'               => form_error('date'),
                'patient_name'       => form_error('patient_name'),
                'mobileno'           => form_error('mobileno'),
                'doctor'             => form_error('doctor'),
                'message'            => form_error('message'),
                'appointment_status' => form_error('appointment_status'),
                'live_consult'       => form_error('live_consult'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[appointment][" . $custom_fields_id . "]"] = form_error("custom_fields[appointment][" . $custom_fields_id . "]");
                    }
                }
            }

            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }
            $array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');
           
        } else {
            $patient_id   = $this->input->post('patient_id');
            $patient_name = $this->input->post('patient_name');
            $gender       = $this->input->post('gender');
            $email        = $this->input->post('email');
            $mobileno     = $this->input->post('mobileno');
            $date         = $this->input->post('date');
            $appointment  = array(
                'patient_id'         => $patient_id,
                'date'               => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
                'patient_name'       => $patient_name,
                'gender'             => $gender,
                'email'              => $email,
                'mobileno'           => $mobileno,
                'doctor'             => $this->input->post('doctor'),
                'message'            => $this->input->post('message'),
                'live_consult'       => $this->input->post('live_consult'),
                'source'             => 'Online',
                'appointment_status' => $this->input->post('appointment_status'),
            );
			
            $insert_id         = $this->appointment_model->add($appointment);
            $custom_field_post = $this->input->post("custom_fields[appointment]");
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[appointment][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
			
            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }
			
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function bill()
    {
        if (!$this->module_lib->hasPatientActive('pharmacy')) {
            access_denied_patient();
        }
		
        $this->session->set_userdata('top_menu', 'pharmacy');
        $patient_id         = $this->patient_data['patient_id'];
        $data["id"]         = $patient_id;
        $data['resultlist'] = $this->pharmacy_model->getBillBasicPatient($patient_id);
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategoryPat();
        $data['medicineName']     = $this->pharmacy_model->getMedicineNamePat();
        $patients                 = $this->patient_model->getPatientListallPat();
        $data["patients"]         = $patients;
        $print_details            = $this->printing_model->get('', 'pharmacy');
        $data["print_details"]    = $print_details;
        $data["marital_status"]   = $this->marital_status;
        $data["blood_group"]      = $this->blood_group;
        $data['fields']           = $this->customfield_model->get_custom_fields('pharmacy', '', '', '', 1);
        $this->load->view('layout/patient/header');
        $this->load->view('patient/pharmacyBill', $data);
        $this->load->view('layout/patient/footer');
    }

    public function ambulance()
    {
        if (!$this->module_lib->hasPatientActive('ambulance')) {
            access_denied_patient();
        }
		
        $this->session->set_userdata('top_menu', 'ambulance');
        $patient_id         = $this->patient_data['patient_id'];
        $data["id"]         = $patient_id;
        $data['fields']     = $this->customfield_model->get_custom_fields('ambulance_call', '', '', '', 1);
        $resultlist         = $this->vehicle_model->getCallAmbulancepat($patient_id);
        $data['resultlist'] = $resultlist;
        $this->load->view('layout/patient/header');
        $this->load->view('patient/ambulance', $data);
        $this->load->view('layout/patient/footer');
    }

    public function getBillDetailsAmbulance($id)
    {
        $data['id'] = $id;

        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $data['superadmin_restriction'] = $this->patient_data['superadmin_restriction']; 
        $data['fields']        = $this->customfield_model->get_custom_fields('ambulance_call', '', '', '', 1);
        $print_details         = $this->printing_model->get('', 'ambulance');
        $data["print_details"] = $print_details;
        $result                = $this->vehicle_model->getBillDetailsAmbulance($id);
        $data['result']        = $result;
        $this->load->view('patient/printBillAmbulance', $data);
    }

    public function patientipddetails()
    {
        if (!$this->module_lib->hasPatientActive('ipd')) {
            access_denied_patient();
        }
        $patient_id = $this->patient_data['patient_id'];
        $data['resultlist'] = $this->patient_model->getipdpatientdetails($patient_id);
        $i                  = 0;
        foreach ($data['resultlist'] as $key => $value) {
            $charges                           = $this->patient_model->getCharges($value["id"]);
            $data['resultlist'][$i]["charges"] = $charges['charge'];
            $payment                           = $this->patient_model->getPayment($value["id"]);
            $data['resultlist'][$i]["payment"] = $payment['payment'];
            $i++;
        }
        $data['organisation'] = $this->organisation_model->get();
        $this->load->view("layout/patient/header");
        $this->load->view('patient/patientipddetails.php', $data);
        $this->load->view("layout/patient/footer");
    }

    public function getpatientidbyipd()
    {
        $ipdid          = $this->input->post('ipdid');
        $result         = $this->patient_model->getpatientidbyipd($ipdid);
        $data["result"] = $result;
    }

    public function ipdprofile($ipdid = '', $pres_id = '')
    {
        $this->session->set_userdata('top_menu', 'ipdprofile');
        $id = $this->patient_data['patient_id'];
        if ($ipdid == '') {
            $ipdresult = $this->patient_model->search_ipd_patients($searchterm = '', $active = 'yes', $discharged = 'no', $id);
            $ipdid     = $ipdresult["ipdid"];
        }
        $ipdnpres_data          = $this->session->flashdata('ipdnpres_data');
        $data['ipdnpres_data']  = $ipdnpres_data;
        $data['payment_method'] = $this->payment_method;
        $data["id"]             = $id;
        $data["ipdid"]          = $ipdid;
        $user_id                = $this->customlib->getUsersID();
        $data['logged_user_id'] = $user_id;
        $data["marital_status"] = $this->marital_status;
        $data["payment_mode"]   = $this->payment_mode;
        $data["bloodgroup"]     = $this->blood_group;
        $data['organisation']   = $this->organisation_model->get();
        $doctors                = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]        = $doctors;
        $result                 = array();
        $diagnosis_details      = array();
        $opd_details            = array();
        $timeline_list          = array();
        $charges                = array();
        $case_reference_id      = $this->patient_model->getReferenceByIpdId($ipdid);
        $data['ipdconferences'] = $this->conference_model->getconfrencebyipd($doctors, $id, $ipdid);

        if (!empty($id)) {
            $status = $this->patient_model->getStatus($id);            
            $result = $this->patient_model->getIpdDetails($ipdid, $status["is_active"]);            
            if (!empty($result)) {

                $timeline_list        = $this->timeline_model->getPatientTimeline($id, $timeline_status = 'yes');
                $prescription_details = $this->prescription_model->getIpdPrescription($ipdid);
                $consultant_register  = $this->patient_model->getPatientConsultant($id, $ipdid);
                $charges              = $this->charge_model->getCharges($ipdid);
                $paymentDetails                    = $this->transaction_model->IPDPatientPayments($ipdid);
                $data['medicationreport_overview'] = $this->patient_model->getmedicationdetailsbydate_overview($ipdid);
                $paid_amount                       = $this->payment_model->getPaidTotal($id, $ipdid);
                $data["paid_amount"]               = $paid_amount["paid_amount"];
                $nurse_note                        = $this->patient_model->getdatanursenote($id, $ipdid);
                $data['nurse_note']                = $nurse_note;
                foreach ($nurse_note as $key => $nurse_note_value) {
                    $notecomment                        = $this->patient_model->getnurenotecomment($ipdid, $nurse_note_value['id']);
                    $nursenote[$nurse_note_value['id']] = $notecomment;
                }
                if (!empty($nursenote)) {
                    $data["nursenote"] = $nursenote;
                }
                $data['fields_nurse']        = $this->customfield_model->get_custom_fields('ipdnursenote', '', '', '', 1);
                $data["payment_details"]     = $paymentDetails;
                $data["consultant_register"] = $consultant_register;
                $data["result"]              = $result;
                $data['time_format']         = $this->time_format;
                $data['is_discharge']        = $this->customlib->checkDischargePatient($data['result']['ipd_discharge']); 
                $data["prescription_detail"] = $prescription_details;
                $data["opd_details"]         = $opd_details;
                $data["timeline_list"]       = $timeline_list;
                $data["charge_type"]         = $this->charge_type;
                $data["charges"]             = $charges;
                $max_dose                    = $this->patient_model->getMaxByipdid($ipdid);
                $medicationreport            = $this->patient_model->getmedicationdetailsbydate($ipdid);
                $data['max_dose']            = $max_dose->max_dose;
                $data["medication"]          = $medicationreport;
                $operation_theatre           = $this->operationtheatre_model->getipdoperationDetails($ipdid, 'patient');
                $data['operation_theatre']   = $operation_theatre;
                $data['fields_ot']           = $this->customfield_model->get_custom_fields('operationtheatre', '', '', '', 1);
                $data['bed_history']         = $this->bed_model->getBedHistory($case_reference_id);
                $doctors_ipd                 = $this->patient_model->getDoctorsipd($ipdid);
                $data['discharge_card']      = $this->patient_model->get_dischargeCard(array('case_reference_id' => $case_reference_id));
                $data['fields_consultant']   = $this->customfield_model->get_custom_fields('ipdconsultinstruction', '', '', '', 1);
                $data['fields_prescription']   =  $this->customfield_model->get_custom_fields('prescription', '', '', '', 1); 
                $data["doctors_ipd"]         = $doctors_ipd;
                $data['investigations']      = $this->patient_model->getallinvestigation($result['case_reference_id']);
                $data['graph']               = $this->transaction_model->ipd_bill_paymentbycase_id($case_reference_id);
                $credit_limit_percentage     = 0;
                
                $data['donut_graph_percentage']  = 0;
                if ($data['result']['ipdcredit_limit'] > 0) {
                    $data['credit_limit']    = $data['result']['ipdcredit_limit'];
                    if($data['graph']['my_balance']>=$data['credit_limit']){
                        $data['donut_graph_percentage']  = 0;                       
                        $data['balance_credit_limit']    = 0;
                        $data['used_credit_limit']       = $data['credit_limit'];
                    }else{                      
                        $credit_limit_percentage = (($data['graph']['my_balance'] / $data['credit_limit'])*100);
                        $data['donut_graph_percentage']  = number_format(((100-$credit_limit_percentage)), 2);
                        $data['balance_credit_limit']    = ($data['credit_limit'] - $data['graph']['my_balance']);
                        $data['used_credit_limit']       = $data['graph']['my_balance'];
                    }                    
                } else {
                    $data['credit_limit'] = 0;
                    $data['used_credit_limit'] = 0;
                    $data['balance_credit_limit'] = 0;
                }                
                $data['getipdoverviewtreatment'] = $this->patient_model->getipdoverviewtreatment($id);
            } else {
                redirect('patient/dashboard/patientipddetails');
                $data = array();
            }
        }
        $data['recent_record_count'] = $this->recent_record_count;
		$data['patientcurrentvital']    = $this->vital_model->getcurrentvitals($id);				
		$data['patient_vital_date']	=	$patient_vital_date = 	$this->vital_model->getpatientvitaldate($id);			
        $data['vital_list']     = $vital_list = $this->vital_model->getvitallist();
 
		foreach($patient_vital_date as $patient_vital_date_result){
			if($patient_vital_date_result['messure_date']){
				$messure_date	=	date('Y-m-d', strtotime($patient_vital_date_result['messure_date']));	 
			}else{
				$messure_date	= '';
			}
			foreach($vital_list as $vital_list_result){				
				$vital_id = $vital_list_result['id'];				
				$patient_vital[$messure_date][$vital_id]  = $this->vital_model->getpatientsvital($id,$vital_id,$messure_date);						 
			}
		}			
		 
		if(!empty($patient_vital)){
			$data['patientvital']	=	$patient_vital;
		}else{
			$data['patientvital']	=	'';
		}		
				
        //---
        $data['obstetric_history'] = $this->antenatal_model->getobstetrichistory($id);
        $data['postnatal_history'] = $this->antenatal_model->getpostnatal($id);
        $data['antenatallist'] = $this->antenatal_model->getantenatallist($id);
        //---
        
        $this->load->view("layout/patient/header");
        $this->load->view("patient/ipdProfile", $data);
        $this->load->view("layout/patient/footer");
    } 

    public function getBillDetails()
    {
        $id                    = $this->input->get('id');
        $print                 = $this->input->get('print');
        $print_details         = $this->printing_model->get('', 'pharmacy');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($print)) {
            $data["print"] = true;
        } else {
            $data["print"] = false;
        }

        $data['fields'] = $this->customfield_model->get_custom_fields('pharmacy', '', '', '', 1);
        $result         = $this->pharmacy_model->getBillDetails($id);
        $data['result'] = $result;
        $bill_no        = $result['id'];
        $patient_id     = $result['patient_id'];
        $detail         = $this->pharmacy_model->getAllBillDetails($id);
        $data['detail'] = $detail;
        $action_details = "";
        $ipd_prescription_basic_id = $result['ipd_prescription_basic_id'];        
        $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($ipd_prescription_basic_id);   
        if($ipd_prescription_basic_id!=""){
            if($ipd_opd->ipd_id != ''){             
              $data['prescription']   =   $this->customlib->getpatientSessionPrefixByType('ipd_prescription').$ipd_opd->id ;   
            }else{
                $data['prescription']   =   $this->customlib->getpatientSessionPrefixByType('opd_prescription').$ipd_opd->id ; 
            }   
        }else{
            $data['prescription']   ="" ;
        }    
        
        $data['superadmin_restriction'] = $this->patient_data['superadmin_restriction'];        
        $action_details .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-toggle='tooltip' class='print_bill' data-record-id='" . $id . "' data-original-title='" . $this->lang->line('print') . "'><i class='fa fa-print'></i></a>";

        $page = $this->load->view('patient/pharmacy/_getBillDetailsPharma', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $action_details));
    }

    public function PrintBillDetailsPathology()
    {
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result              = $this->pathology_model->getPathologyBillByID($id);
        $data['bill_prefix'] = $this->customlib->getPatientSessionPrefixByType('pathology_billing');
        $data['result']      = $result;
        $data['fields']      = $this->customfield_model->get_custom_fields('pathology', '', '', '', 1);
        $page                = $this->load->view('patient/pathology/_getBillDetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function PrintBillDetailsRadiology()
    {
        $print_details         = $this->printing_model->get('', 'radiology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $data['fields']      = $this->customfield_model->get_custom_fields('radiology', '', '', '', 1);
        $data['bill_prefix'] = $this->customlib->getPatientSessionPrefixByType('radiology_billing');
        $result              = $this->radio_model->getRadiologyBillByID($id, 'patient');
        $data['result']      = $result;
        $page                = $this->load->view('patient/radiology/_getBillDetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function add_patient_timeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('document'), 'callback_handle_doc_upload[timeline_doc]');
        $title = $this->input->post("timeline_title");

        if ($this->form_validation->run() == false) {

            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $timeline_date = $this->input->post('timeline_date');
            $patient_id    =$this->patient_data['patient_id'];
            $user_id       = $this->customlib->getUsersID();
            $timeline      = array(
                'title'                => $this->input->post('timeline_title'),
                'timeline_date'        => $this->customlib->dateFormatToYYYYMMDD($timeline_date),
                'description'          => $this->input->post('timeline_desc'),
                'date'                 => date('Y-m-d'),
                'status'               => 'yes',
                'patient_id'           => $patient_id,
                'generated_users_type' => 'patient',
                'generated_users_id'   => $user_id,
            );

            $id = $this->timeline_model->add_patient_timeline($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = './uploads/patient_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }
                $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                $document = basename($_FILES['timeline_doc']['name']);

                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name);
            } else {
                $document = "";
                $img_name = "";
            }

            $upload_data = array('id' => $id, 'document' => $img_name);
            $this->timeline_model->add_patient_timeline($upload_data);
            $msg   = $this->lang->line('timeline_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function edit_patient_timeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('document'), 'callback_handle_doc_upload[timeline_doc]');
        $title = $this->input->post("timeline_title");

        if ($this->form_validation->run() == false) {

            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $patientid     = $this->patient_data['patient_id'];
            $timelineid    = $this->input->post('timeline_id');
            $timeline_date = $this->input->post('timeline_date');
            $date          = $this->customlib->dateFormatToYYYYMMDD($timeline_date);
            $user_id       = $this->customlib->getUsersID();

            $timeline = array(
                'id'                   => $timelineid,
                'title'                => $this->input->post('timeline_title'),
                'timeline_date'        => $date,
                'description'          => $this->input->post('timeline_desc'),
                'status'               => 'yes',
                'date'                 => date('Y-m-d'),
                'patient_id'           => $patientid,
                'generated_users_type' => 'patient',
                'generated_users_id'   => $user_id,
            );

            $this->timeline_model->add_patient_timeline($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = './uploads/patient_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }
                $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                $document = basename($_FILES['timeline_doc']['name']);
                $img_name = $timelineid . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name);

                $upload_data = array('id' => $timelineid, 'document' => $img_name);
                $this->timeline_model->add_patient_timeline($upload_data);
            }

            $msg   = $this->lang->line('timeline_edit_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function editTimeline()
    {
        $id     = $this->input->post("id");
        $result = $this->timeline_model->geteditTimeline($id);
        echo json_encode($result);
    }

    public function delete_patient_timeline($id)
    {
        if (!empty($id)) {
            $this->timeline_model->delete_patient_timeline($id);
        }
    }

    /**
     * This function is used to validate document for upload
     **/
    public function handle_doc_upload($str, $var)
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = $image_validate["allowed_extension"];
            $allowed_mime_type = $image_validate["allowed_mime_type"];
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES[$var]['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('file_type_extension_error_uploading_document'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('extension_error_while_uploading_document'));
                    return false;
                }
				
                if ($file_size > 2097152) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('file_size_shoud_be_less_than') . "2MB");
                    return false;
                }				
            } else {
                $this->form_validation->set_message('handle_doc_upload', $this->lang->line('error_while_uploading_document'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function getsummaryDetails()
    {
        $id                    = $this->input->post("id");
        $ipdid                 = $this->input->post("ipdid");
        $data['id']            = $id;
        $print_details         = $this->printing_model->get('', 'summary');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $result         = $this->patient_model->getIpdDetails($ipdid);
        $data['result'] = $result;
        $this->load->view('patient/printSummary', $data);
    }

    public function getsummaryopdDetails()
    {
        $id                    = $this->input->post("id");
        $opdid                 = $this->input->post("opdid");
        $data['id']            = $id;
        $print_details         = $this->printing_model->get('', 'summary');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $result         = $this->patient_model->getDetails($id, $opdid);
        $data['result'] = $result;
        $this->load->view('patient/printopdSummary', $data);
    }

    public function printCharge()
    {
        $type                  = $this->input->post('type');
        $print_details         = $this->printing_model->get('', $type);
        $id                    = $this->input->post('id');
        $charge                = $this->charge_model->getChargeById($id);
        $data['print_details'] = $print_details;
        $data['charge']        = $charge;		 
        $page                  = $this->load->view('patient/_printCharge', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printTransaction()
    {
        $print_details = $this->printing_model->get('', 'paymentreceipt');
        $id            = $this->input->post('id');
        $module_type   = $this->input->post('module_type');
        if ($module_type == 'opd') {
            $transaction = $this->transaction_model->opdPaymentByTransactionId($id);
        } else { 
            $transaction = $this->transaction_model->ipdPaymentByTransactionId($id);
        }
        $data['transaction']   = $transaction;
        $data['print_details'] = $print_details;
        $page = $this->load->view('patient/transaction/_printTransaction', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getDetailsOt()
    {
        $id     = $this->input->post("patient_id");
        $result = $this->operationtheatre_model->getDetails($id);
        if (($result['patient_type'] == 'Inpatient') || ($result['patient_type'] == 'Outpatient')) {
            $opd_ipd_no           = $this->operationtheatre_model->getopdipdDetails($id, $result['patient_type']);
            $result['opd_ipd_no'] = $opd_ipd_no;
        }
        $result['admission_date'] = date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['admission_date']));
        $result['date']           = date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date']));
        echo json_encode($result);
    }

    public function getBillDetailsOt($id)
    {
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details         = $this->printing_model->get('', 'ot');
        $data["print_details"] = $print_details;
        $result                = $this->operationtheatre_model->getBillDetailsOt($id);
        $data['result']        = $result;
        $detail                = $this->operationtheatre_model->getAllBillDetailsOt($id);
        $data['detail']        = $detail;
        $this->load->view('patient/printBillOt', $data);
    }

    public function otsearch()
    {
        $this->session->set_userdata('top_menu', 'operation_theatre');
        $patient_id              = $this->patient_data['patient_id'];
        $data["id"]              = $patient_id;
        $doctors                 = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]         = $doctors;
        $patients                = $this->patient_model->getPatientListallPat();
        $data["patients"]        = $patients;
        $userdata                = $this->customlib->getUserData();
        $role_id                 = $userdata['role_id'];
        $data['charge_category'] = $this->operationtheatre_model->getChargeCategory();
        $data['resultlist']      = $this->operationtheatre_model->searchFullTextPat($patient_id);
        $data['organisation']    = $this->organisation_model->get();
        $this->load->view('layout/patient/header');
        $this->load->view('patient/otsearch.php', $data);
        $this->load->view('layout/patient/footer');
    }

    public function radioreport()
    {
        if (!$this->module_lib->hasPatientActive('radiology')) {
            access_denied_patient();
        }
        $this->session->set_userdata('top_menu', 'radiology');
        $patient_id      = $this->patient_data['patient_id'];
        $data["id"]      = $patient_id;
        $data['fields']  = $this->customfield_model->get_custom_fields('radiology', '', '', '', 1);
        $doctors         = $this->staff_model->getStaffbyrole(3);
        $data["doctors"] = $doctors;
        $result          = $this->radio_model->getTestReportBatchRadio($patient_id);
        $data["result"]  = $result;
        $this->load->view('layout/patient/header');
        $this->load->view('patient/radioBill', $data);
        $this->load->view('layout/patient/footer');
    }

    public function getBillDetailsPatho($id, $parameter_id)
    {
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details            = $this->printing_model->get('', 'pathology');
        $data["print_details"]    = $print_details;
        $result                   = $this->pathology_model->getBillDetails($id);
        $data['result']           = $result;
        $detail                   = $this->pathology_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parametername            = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]    = $parametername;
        $parameterdetails         = $this->pathology_category_model->getparameterDetailsforpatient($id);
        $data['parameterdetails'] = $parameterdetails;
        $this->load->view('patient/printBillPatho', $data);
    }

    public function getReportDetailsPatho($id, $parameter_id)
    {
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details            = $this->printing_model->get('', 'pathology');
        $data["print_details"]    = $print_details;
        $result                   = $this->pathology_model->getBillDetails($id);
        $data['result']           = $result;
        $detail                   = $this->pathology_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parametername            = $this->pathology_category_model->getpathoparameter();
        $data["parametername"]    = $parametername;
        $parameterdetails         = $this->pathology_category_model->getparameterDetailsforpatient($id);
        $data['parameterdetails'] = $parameterdetails;
        $this->load->view('patient/printReportPatho', $data);
    }

    public function getBillDetailsRadio($id, $parameter_id)
    {
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details            = $this->printing_model->get('', 'radiology');
        $data["print_details"]    = $print_details;
        $result                   = $this->radio_model->getBillDetails($id);
        $data['result']           = $result;
        $detail                   = $this->radio_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parametername            = $this->radio_model->getpathoparameter();
        $data["parametername"]    = $parametername;
        $parameterdetails         = $this->radio_model->getparameterDetailsforpatient($id);
        $data['parameterdetails'] = $parameterdetails;
        $this->load->view('patient/printBillRadio', $data);
    }

    public function getReportDetailsRadio($id, $parameter_id)
    {
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $print_details            = $this->printing_model->get('', 'radiology');
        $data["print_details"]    = $print_details;
        $result                   = $this->radio_model->getBillDetails($id);
        $data['result']           = $result;
        $detail                   = $this->radio_model->getAllBillDetails($id);
        $data['detail']           = $detail;
        $parametername            = $this->radio_model->getpathoparameter();
        $data["parametername"]    = $parametername;
        $parameterdetails         = $this->radio_model->getparameterDetailsforpatient($id);
        $data['parameterdetails'] = $parameterdetails;
        $this->load->view('patient/printReportRadio', $data);
    }

    public function search()
    {
        if (!$this->module_lib->hasPatientActive('pathology')) {
            access_denied_patient();
        }
        $this->session->set_userdata('top_menu', 'pathology');
        $patient_id      = $this->patient_data['patient_id'];
        $data["id"]      = $patient_id;
        $doctors         = $this->staff_model->getStaffbyrole(3);
        $data["doctors"] = $doctors;
        $result          = $this->pathology_model->getTestReportBatchPatho($patient_id);
        $data["result"]  = $result;
        $data['fields']  = $this->customfield_model->get_custom_fields('pathology', '', '', '', 1);
        $this->load->view('layout/patient/header');
        $this->load->view('patient/pathologyBill', $data);
        $this->load->view('layout/patient/footer');
    }

    public function getPatientPathologyDetails()
    {
        $id                  = $this->input->post('id');
        $data['id']          = $id;
        $result              = $this->pathology_model->getPathologyBillByID($id);       
        $data['result']      = $result;

        if($result->ipd_prescription_basic_id != ''){
            $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($result->ipd_prescription_basic_id); 
           
            if(!empty($ipd_opd)){
                if($ipd_opd->ipd_id != ''){             
                $data['prescription']   =   $this->customlib->getPatientSessionPrefixByType('ipd_prescription').$ipd_opd->id;  
            }else{
                $data['prescription']   =   $this->customlib->getPatientSessionPrefixByType('opd_prescription').$ipd_opd->id ; 
            } 
            }else{
                $data['prescription']   = '';
            }    
                   
        }else{
            $data['prescription']   = '';
        }
        
        $data['fields']      = $this->customfield_model->get_custom_fields('pathology', '', '', '', 1);
        $data['bill_prefix'] = $this->customlib->getPatientSessionPrefixByType('pathology_billing');
        $data['superadmin_restriction'] = $this->patient_data['superadmin_restriction'];         
        $page                = $this->load->view('patient/pathology/_getPatientPathologyDetails', $data, true);
        $actions             = "";
        $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_parameter' data-toggle='tooltip' data-placement='bottom'  data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('print_test_report') . "'><i class='fa fa-reorder'></i></a>";
        $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_bill' data-toggle='tooltip' data-placement='bottom' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line("print_bill") . "'><i class='fa fa-print'></i></a>";
        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
    }

    public function getPatientRadiologyDetails()
    {
        $id             = $this->input->post('id');
        $data['id']     = $id;
        $result         = $this->radio_model->getRadiologyBillByID($id, 'patient');
        $data['result'] = $result;
         if($result->ipd_prescription_basic_id != ''){
            $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($result->ipd_prescription_basic_id); 
           
            if(!empty($ipd_opd)){
                if($ipd_opd->ipd_id != ''){             
					$data['prescription']   =   $this->customlib->getPatientSessionPrefixByType('ipd_prescription').$ipd_opd->id;  
				}else{
					$data['prescription']   =   $this->customlib->getPatientSessionPrefixByType('opd_prescription').$ipd_opd->id ; 
				} 
            }else{
                $data['prescription']   = '';
            }       
                   
        }else{
            $data['prescription']   = '';
        }
        $data['superadmin_restriction'] = $this->patient_data['superadmin_restriction']; 
        $data['fields'] = $this->customfield_model->get_custom_fields('radiology', '', '', '', 1);
        $page           = $this->load->view('patient/radiology/_getPatientRadiologyDetails', $data, true);
        $actions        = "";

        $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_parameter' data-toggle='tooltip' data-placement='bottom'  data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('print_test_report') . "'><i class='fa fa-reorder'></i></a>";
        
        $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_bill' data-placement='bottom' data-toggle='tooltip' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line("print_bill") . "'><i class='fa fa-print'></i></a>";
        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
    }

    public function ipdBill()
    {
        $id                      = $this->input->post("patient_id");
        $ipdid                   = $this->input->post("ipdid");
        $data['total_amount']    = $this->input->post("total_amount");
        $data['discount']        = $this->input->post("discount");
        $data['other_charge']    = $this->input->post("other_charge");
        $data['gross_total']     = $this->input->post("gross_total");
        $data['tax']             = $this->input->post("tax");
        $data['net_amount']      = $this->input->post("net_amount");
        $data["print_details"]   = $this->printing_model->get('', 'ipd');
        $status                  = $this->patient_model->getStatus($id);
        $result                  = $this->patient_model->getIpdDetails($id, $ipdid, $status["is_active"]);
        $charges                 = $this->charge_model->getCharges($id, $ipdid);
        $paymentDetails          = $this->payment_model->paymentDetails($id, $ipdid);
        $paid_amount             = $this->payment_model->getPaidTotal($id, $ipdid);
        $balance_amount          = $this->payment_model->getBalanceTotal($id, $ipdid);
        $data["paid_amount"]     = $paid_amount["paid_amount"];
        $data["balance_amount"]  = $balance_amount["balance_amount"];
        $data["payment_details"] = $paymentDetails;
        $data["charges"]         = $charges;
        $data["result"]          = $result;
        $this->load->view("patient/ipdBill", $data);
    }

    public function getConsultantBatch()
    {
        $patient_id     = $this->patient_data['patient_id'];
        $data["id"]     = $patient_id;
        $result         = $this->operationtheatre_model->getConsultantBatchOt($patient_id);
        $data["result"] = $result;
        $this->load->view('patient/patientConsultantDetail', $data);
    }

    public function visitdetails($opd_details_id)
    {        
        if (!empty($opd_details_id)) {
            $timeline_list  = array();
            $investigations = array();
            $result         = $this->patient_model->getDetails($opd_details_id);
            $data['result'] = $result;

            $visit_details         = $this->patient_model->getVisitDetails($opd_details_id, 'patient');
            $data['visit_details'] = $visit_details;
            $billstatus             = $this->patient_model->getBillstatus($opd_details_id);
            $data["billstatus"]     = $billstatus;
            $data['opd_details_id'] = $opd_details_id;

            if (!empty($result)) {
                $timeline_list = $this->timeline_model->getPatientTimeline($result['pid'], $timeline_status = 'yes');
            }

            $data["timeline_list"] = $timeline_list;

            $paymentDetails           = $this->transaction_model->OPDPatientPayments($opd_details_id);
            $data["payment_details"]  = $paymentDetails;
            $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
            $data['medicineName']     = $this->pharmacy_model->getMedicineName();
            $charges                  = $this->charge_model->getopdCharges($opd_details_id);
            $data["charges_detail"]   = $charges;
            $paid_amount              = $this->payment_model->getOPDPaidTotal($opd_details_id);
            $max_dose                 = $this->patient_model->getMaxByopdid($opd_details_id);
            $data['max_dose']          = $max_dose->max_dose;
            $medicationreport          = $this->patient_model->getmedicationdetailsbydateopd($opd_details_id);
            $data["medication"]        = $medicationreport;
            $data["billpaid_amount"]   = 0;
            $operation_theatre         = $this->operationtheatre_model->getopdoperationDetails($opd_details_id, 'patient');
            $data['operation_theatre'] = $operation_theatre;
            $doctors                   = $this->staff_model->getStaffbyrole(3);
            $data["doctors"]           = $doctors;
            $doctorid                  = "";
            $data["doctor_select"]     = $doctorid;
            $data["marital_status"]    = $this->marital_status;
            $data["payment_mode"]      = $this->payment_mode;
            $data["bloodgroup"]        = $this->blood_group;
            $data["charge_type"]       = $this->charge_type;
            $data["dosage"]            = array();
             $getVisitDetailsid        = $this->patient_model->getVisitDetailsid($opd_details_id);

            if (!empty($getVisitDetailsid)) {
                $data['visitconferences'] = $this->conference_model->getconfrencebyvisitid($getVisitDetailsid);              
            }
            
            $data['fields_ot'] = $this->customfield_model->get_custom_fields('operationtheatre', '', '', '', 1);
            $data['fields']    = $this->customfield_model->get_custom_fields('opdrecheckup', '', '', '', 1);

            if (!empty($result)) {
                $data['investigations'] = $this->patient_model->getallinvestigation($result['case_reference_id']);
            }

            $data['patientdetails']            = $this->patient_model->getpatientoverviewbycaseid($result['case_reference_id']);
            $data['medicationreport_overview'] = $this->patient_model->getmedicationdetailsbydate_opdoverview($opd_details_id);

            $data['graph']               = $this->transaction_model->opd_bill_paymentbycase_id($result['case_reference_id']);
            $data['recent_record_count'] = 5;
            $data['superadmin_restriction'] = $this->patient_data['superadmin_restriction'];         
            
            $data['patient_id']    = $result['pid'];           
            $data['patientcurrentvital']    = $this->vital_model->getcurrentvitals($result['pid']);
					
			$data['patient_vital_date']	=	$patient_vital_date = 	$this->vital_model->getpatientvitaldate($result['pid']);			
			$data['vital_list']     = $vital_list = $this->vital_model->getvitallist();
	
			foreach($patient_vital_date as $patient_vital_date_result){
				
				$messure_date	=	date('Y-m-d', strtotime($patient_vital_date_result['messure_date']));	 
				
				foreach($vital_list as $vital_list_result){				
					$vital_id = $vital_list_result['id'];				
					$patient_vital[$messure_date][$vital_id]  = $this->vital_model->getpatientsvital($result['pid'],$vital_id,$messure_date);					
				}
			}			
			
			if(!empty($patient_vital)){
				$data['patientvital']	=	$patient_vital;
			}else{
				$data['patientvital']	=	'';
			}		
            
            $this->load->view("layout/patient/header");
            $this->load->view("patient/visitDetails", $data);
            $this->load->view("layout/patient/footer");
        } 
    }

    public function getOPDBill()
    {
        $id                      = $this->patient_data['patient_id'];
        $data["id"]              = $id;
        $data['total_amount']    = $this->input->post("total_amount");
        $data['discount']        = $this->input->post("discount");
        $data['other_charge']    = $this->input->post("other_charge");
        $data['gross_total']     = $this->input->post("gross_total");
        $data['tax']             = $this->input->post("tax");
        $data['net_amount']      = $this->input->post("net_amount");
        $visit_id                = $this->input->post("visit_id");
        $data['visit_id']        = $visit_id;
        $status                  = $this->input->post("status");
        $result                  = $this->patient_model->getDetails($id);
        $charges                 = $this->charge_model->getOPDCharges($id, $visit_id);
        $paymentDetails          = $this->payment_model->opdPaymentDetailspat($id);
        $paid_amount             = $this->payment_model->getOPDPaidTotalPat($id);
        $balance_amount          = $this->payment_model->getOPDBalanceTotal($id);
        $billstatus              = $this->patient_model->getBillstatus($id, $visit_id);
        $data["billstatus"]      = $billstatus;
        $data["paid_amount"]     = $paid_amount["paid_amount"];
        $data["balance_amount"]  = $balance_amount["balance_amount"];
        $data["payment_details"] = $paymentDetails;
        $data["charges"]         = $charges;
        $data["result"]          = $result;
        $this->load->view("patient/opdBill", $data);
    }

    public function download_patient_timeline($timeline_id, $doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/patient_timeline/" . $doc;
        $data     = file_get_contents($filepath);
        $name     = $doc;
        force_download($name, $data);
    }

    public function report_download($doc)
    {
        $this->load->helper('download');
        $filepath = "./" . $this->uri->segment(4) . "/" . $this->uri->segment(5) . "/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

    public function radio_download($doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/radiology_report/" . $doc;
        $data     = file_get_contents($filepath);
        force_download($doc, $data);
    }

    public function getIpdDetails()
    {
        $ipdid  = $this->input->post("ipdid");
        $result = $this->patient_model->getIpdDetails($ipdid);
        if ($result['symptoms']) {
            $result['symptoms'] = nl2br($result['symptoms']);
        }
        $result['date']        = date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date']));
        $result['patient_age'] = $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']);
        $result['doctor_name'] = composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']);
        $result['bed_group']   = $result['bedgroup_name'] . '-' . $result['floor_name'];
        $data['fields']        = $this->customfield_model->get_custom_fields('patient', '', '', '', 1);
        $data['result']        = $result;

        $page = $this->load->view("patient/ipdPatientDetail", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getIpdsummaryDetails()
    {

    }

    public function deleteappointment($id)
    {
        if ($id != '') {
            $this->appointment_model->frontDelete($id);
            $json_array = array('status' => '1', 'error' => '', 'msg' => $this->lang->line('delete_message')); 
        } else {
            $json_array = array('status' => '0', 'error' => '', 'message' => '');
        }
        echo json_encode($json_array);
    }

    public function user_language($lang_id)
    {
        $language_name = $this->db->select('languages.language,languages.is_rtl')->from('languages')->where('id', $lang_id)->get()->row_array();
        $patient       = $this->session->userdata('patient');
        if (!empty($patient)) {
            $this->session->unset_userdata('patient');
        }
        $language_array         = array('lang_id' => $lang_id, 'language' => $language_name['language']);
        $patient['language']    = $language_array;
        $patient['is_rtl']      = $language_name['is_rtl'];       
        
        $this->session->set_userdata('patient', $patient);
        $session         = $this->session->userdata('patient');        
        $id              = $session['patient_id'];
        $data['lang_id'] = $lang_id;
        $language_result = $this->language_model->set_patientlang($id, $data);
    }

    public function otdetails()
    {
        $ot_id                                  = $this->input->post("ot_id");
        $data['otdetails']                      = $this->operationtheatre_model->otdetails($ot_id);
        $data['fields']                         = $this->customfield_model->get_custom_fields('operationtheatre', '', '', '', 1);
        $data['operation_theater_reference_no'] = $this->customlib->getPatientSessionPrefixByType('operation_theater_reference_no');
        $page                                   = $this->load->view("admin/operationtheatre/_otdetails", $data, true);

        $actions = "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_ot_bill'  data-toggle='tooltip' data-record-id=\"" . $ot_id . "\"   data-original-title='" . $this->lang->line('print') . "'><i class='fa fa-print'></i></a>";

        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
    }

    public function print_otdetails()
    {
        $print_details         = $this->printing_model->get('', 'ot');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
        $data['otdetails']     = $this->operationtheatre_model->otdetailsforprint($id, 'patient');
        $data['fields']        = $this->customfield_model->get_custom_fields('operationtheatre', '', '', '', 1);
        $type                  = "operationtheatre";
        $action                = '<a href="javascript:void(0);" class=" print_dischargecard" data-toggle="tooltip" title="" data-module_type="' . $type . '"  data-recordId="' . $id . '" data-original-title=""><i class="fa fa-print"></i> </a>';
        $page                  = $this->load->view('patient/_printotdetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'action' => $action));
    }

    public function getinvestigationparameter()
    {
        $lab = $_REQUEST['lab'];
        if ($lab == 'pathology') {

            $actions        = "";
            $id             = $_REQUEST['id'];
            $result         = $this->pathology_model->getPatientPathologyReportDetails($id);
            $data['result'] = $result;
            $page           = $this->load->view('patient/pathology/_labinvestigations', $data, true);
            
            if($result->pathology_report != ""){
                $actions = "<a href='".base_url().'patient/dashboard/downloadPathologyReport/'.$id. "'  data-toggle='tooltip'  data-original-title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
            }

            $actions.= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_bill' data-toggle='tooltip' data-record-id=\"" . $id . "\" data-type-id='" . $lab . "'  data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>&nbsp; ";

            echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));

        } else {
            $actions               = "";
            $print_details         = $this->printing_model->get('', 'radiology');
            $data['print_details'] = $print_details;

            $id             = $_REQUEST['id'];
            $result         = $this->radio_model->getPatientRadiologyReportDetails($id);
            $data['result'] = $result;

            $page = $this->load->view('patient/radiology/_labinvestigations', $data, true);

            if($result->radiology_report != ""){
                $actions= "<a href='".base_url().'patient/dashboard/downloadRadiologyReport/'.$id. "'  data-original-title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
            }

            $actions.= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_bill' data-toggle='tooltip' data-record-id=\"" . $id . "\"  data-type-id='" . $lab . "' data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>&nbsp; ";

            echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
        }
    }

    public function printpathoparameter()
    {
        $lab = $_REQUEST['lab'];
        if ($lab == 'pathology') {
            $print_details         = $this->printing_model->get('', 'pathology');
            $data['print_details'] = $print_details;
            $id                    = $this->input->post('id');
            $data['id']            = $id;
            if (isset($_POST['print'])) {
                $data["print"] = 'yes';
            } else {
                $data["print"] = 'no';
            }

            $result         = $this->pathology_model->getPatientPathologyReportDetails($id);
            $data['fields'] = $this->customfield_model->get_custom_fields('pathology', 1);
            $data['result'] = $result;
            $page           = $this->load->view('patient/pathology/_printlabinvestigations', $data, true);
            echo json_encode(array('status' => 1, 'page' => $page));
        } else {
            $actions               = "";
            $print_details         = $this->printing_model->get('', 'radiology');
            $data['print_details'] = $print_details;
            $id                    = $_REQUEST['id'];
            $result                = $this->radio_model->getPatientRadiologyReportDetails($id);
            $data['result']        = $result;
            $page                  = $this->load->view('patient/radiology/_printlabinvestigations', $data, true);
            echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
        }
    }

    public function getopdtreatmenthistory()
    {
        $patientid   = $this->uri->segment(4);
        $dt_response = $this->patient_model->getopdtreatmenthistory($patientid);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $opd_id           = $value->opd_id;
                $visit_details_id = $value->visit_id;
                $check            = $this->db->where("visit_details_id", $visit_details_id)->get('ipd_prescription_basic');
                if ($check->num_rows() > 0) {
                    $result[$key]['prescription'] = 'yes';
                } else {
                    $result[$key]['prescription'] = 'no';
                    $userdata                     = $this->customlib->getUserData();
                    if ($this->session->has_userdata('hospitaladmin')) {
                        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                        if ($doctor_restriction == 'enabled') {
                            if ($userdata["role_id"] == 3) {
                                if ($userdata["id"] == $value["staff_id"]) {

                                } else {
                                    $result[$key]['prescription'] = 'not_applicable';
                                }
                            }
                        }
                    }
                }

                $action = "<div class=''>";

                $action .= "<a href='javascript:void(0)' data-loading-text='" . $this->lang->line('please_wait') . "' data-opd-id=" . $opd_id . " data-record_id=" . $visit_details_id . " class='btn btn-default btn-xs get_opd_detail'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";
                $action .= "</div>";                
                                                    
                $first_action = "<a href=" . base_url() . 'patient/dashboard/visitdetails/' . $opd_id . ">";
                
                if($value->symptoms){ 
                    $symptoms   =   nl2br($value->symptoms); 
                }else{
                    $symptoms   =   ''; 
                }
                
                //==============================
                $row[]     = $first_action . $this->customlib->getPatientSessionPrefixByType("opd_no") . $opd_id . "</a>";
                $row[]     = $value->case_reference_id;                
                $row[]     = date($this->customlib->getHospitalDateFormat(true, true), strtotime($value->appointment_date));             
                $row[]     = $symptoms;
                $row[]     = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[]     = $action;
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }
	
    public function getipdtreatmenthistory($id)
    {
        $dt_response = $this->patient_model->getipdtreatmenthistory($id);
        $fields      = $this->customfield_model->get_custom_fields('ipd', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
               
                $id                = $value->id;
                $ipdid             = $value->ipdid;
                $discharge_details = $this->patient_model->getIpdBillDetails($id, $ipdid);
                $action            = "<div class='rowoptionview'>";              

                $action .= "<a href=" . base_url() . 'admin/patient/ipdprofile/' . $value->ipdid . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";               

                $action .= "</div'>";
                $first_action = "<a href=" . base_url() . 'admin/patient/ipdprofile/' . $value->ipdid . ">";               

                $row[] = $this->customlib->getPatientSessionPrefixByType('ipd_no') . $value->ipdid;
                $row[] = $value->symptoms;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $value->bed_name . "-" . $value->bedgroup_name . "-" . $value->floor_name;                
               
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getDetailsAppointment()
    {
        $id     = $this->input->post("appointment_id");
        $result = $this->appointment_model->getDetailsAppointment($id);  
         
         
        if ($result['appointment_status'] == 'approved') {
            $result['appointment_no'] = $this->customlib->getPatientSessionPrefixByType('appointment') . $id;
        }
		
		if($result['start_time']){
            if($this->time_format){
                $result["doctor_shift_name"]       = date('G:i:s', strtotime($result['start_time'])) ." - ". date('G:i:s', strtotime($result['end_time'])) ;
            }else{
                $result["doctor_shift_name"]       = date('g:i A', strtotime($result['start_time'])) ." - ". date('g:i A', strtotime($result['end_time'])) ;
            }  
        }else{
            $result["doctor_shift_name"]       = '';
        }

        $result["patients_name"]       = composePatientName($result['patients_name'], $result['patient_id']);
        $result["edit_live_consult"]   = $this->lang->line($result['live_consult']);
        $result["live_consult"]        = $result['live_consult'];
        $result["date"]                = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('appointment', $id);
        $cutom_fields_data             = get_custom_table_values($id, 'appointment');
        $result['field_data']          = $cutom_fields_data;
        $result['patients_gender']     = $this->lang->line(strtolower($result['patients_gender']));
        $transaction_id                = $result['transaction_id'];
        
        if($result['appointment_status']=='approved'){
            if($result['transaction_id']!=""){
             $result['transaction_id']      = $this->customlib->getPatientSessionPrefixByType('transaction_id').$result['transaction_id'];
             }else{
                $result['transaction_id']  = "";
             }
             $result['payment_mode']  = $this->lang->line(strtolower($result['payment_mode']));
         }
         
        $result['department_name']     = $result['department_name'];
        $result['age']                  = $result['age'];
        $result['day']                  = $result['day'];
        $result['month']                = $result['month'];
        $result['blood_group']          = $result['blood_group'];
        $result['patient_age']          = $this->customlib->getPatientAge($result['age'],$result['month'],$result['day']);
        
        if($result['cheque_date']){
            $result["cheque_date"]          = $this->customlib->YYYYMMDDTodateFormat($result['cheque_date']);
        }else{
            $result["cheque_date"]          = '';
        }

        if ($result['attachment'] != "") {
            $result["doc"] = "<a href='" . site_url('patient/dashboard/downloadreceipt/') . $transaction_id . "' class='btn btn-default btn-xs'  title=" . $this->lang->line('download') . "><i class='fa fa-download'></i></a>";
        } else {
            $result["doc"] = "";
        }       
        
        $result['appointment_status']  = $this->lang->line(strtolower($result['appointment_status']));
        
        if($result['source']){
            $result['source']  = $this->lang->line(strtolower($result['source']));
        }else{
            $result['source']  = '';
        }       

        echo json_encode($result);
    }

    public function printAppointmentBill()
    {
        $print_details         = $this->printing_model->get('', 'appointment');
        $data["print_details"] = $print_details;
        $id     = $this->input->post("appointment_id");
        $is_patient            = 1;
        
        $result = $this->appointment_model->getDetailsAppointment($id,$is_patient);
        if ($result['appointment_status'] == 'approved') {
            $result['appointment_no'] = $this->customlib->getPatientSessionPrefixByType('appointment') . $id;
        }else{
            $result['appointment_no']="";
        }
        
        if($result['start_time']){
            if($this->time_format){
                $result["doctor_shift_name"]       = date('G:i:s', strtotime($result['start_time'])) ." - ". date('G:i:s', strtotime($result['end_time'])) ;
            }else{
                $result["doctor_shift_name"]       = date('g:i A', strtotime($result['start_time'])) ." - ". date('g:i A', strtotime($result['end_time'])) ;
            }  
        }else{
            $result["doctor_shift_name"]       = '';
        }

        $result["patients_name"]       = composePatientName($result['patients_name'], $result['patient_id']);
        $result["edit_live_consult"]   = $this->lang->line($result['live_consult']);
        $result["live_consult"]        = $result['live_consult'];
        $result["date"]                = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('appointment', $id);
        $cutom_fields_data             = get_custom_table_values($id, 'appointment');
        $result['field_data']          = $cutom_fields_data;
        $result['patients_gender']     = $result['patients_gender'];
        $data['fields']                = $this->customfield_model->get_custom_fields('appointment','','1','',1);
        
        if($result['appointment_status']=='approved'){
             $result['transaction_id']      = $this->customlib->getPatientSessionPrefixByType('transaction_id').$result['transaction_id'];
        }else{
            $result['transaction_id']  = "";
        }
        $data['result']                = $result;
        
        if($result['appointment_status']){
            $result['appointmentstatus']  = $this->lang->line(strtolower($result['appointment_status']));
        }else{
            $result['appointmentstatus']  =  '';
        }
        
        if($result['source']){
            $result['source']  = $this->lang->line(strtolower($result['source']));
        }else{
            $result['source']  = '';
        }
        
        $page = $this->load->view('patient/printAppointment', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getcomponentissueDatatable($patient_id)
    {
        $fields = $this->customfield_model->get_custom_fields('component_issue', '', '','', 1);
        $dt_response = $this->bloodissue_model->getcomponentissuerecordById($patient_id,'', '', '');
         
        $dt_response = json_decode($dt_response);

        $dt_data = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $prefix         = $this->customlib->getPatientSessionPrefixByType('blood_bank_billing') . $value->id;
                $balance_amount = $value->net_amount - $value->paid_amount;
                $action = "";
                 $action .= "<a href='#' data-record-id='" . $value->id . "' class='btn btn-default btn-xs viewcomponentDetail' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
                $action .= "<a href='#' data-record-id='" . $value->id . "' class='btn btn-default view_payment btn-xs' data-toggle='tooltip' data-module_type='blood_bank' title='" . $this->lang->line('view_payments') . "' ><i class='fa fa-money'></i></a>";
                $action .= '<a href="#" data-record-id="' . $value->id . '" class="printcomponentIssueBill btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="' . $this->lang->line('print') . '"><i class="fa fa-print"></i></a>';

                $action .= '<a  class=" btn btn-primary btn-xs" onclick="payModal(' . $value->id . ',' . $balance_amount . ')" autocomplete="off">' . $this->lang->line('pay') . '</a>';
               
                //==============================
                $row[] = $prefix ;
                $row[] = $value->case_reference_id ;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->date_of_issue, false);
                $row[] = $value->patient_name . " (" . $value->patient_id . ")";
                $row[] = $value->blood_group_name;
                $row[] = $value->component_name;
                $row[] = $this->lang->line(strtolower($value->gender));
                $row[] = $value->donor_name;
                $row[] = $this->customlib->bag_string($value->bag_no, $value->volume, $value->unit);
                //====================

                if (!empty($fields)) {

                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";
                        }
                        $row[] = $display_field;
                    }
                }
                //====================
                $row[]     = $value->net_amount;
                $row[]     = $value->paid_amount;
                $row[]     = amountFormat($balance_amount);
                $row[]     =  $action;
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function printBloodIssueBill()
    {
        $print_details               = $this->printing_model->get('', 'bloodbank');
        $id                          = $this->input->post('id');
        $charge                      = array();
        $blood_issues_detail         = $this->bloodissue_model->getDetail($id);
        $transaction                 = $this->transaction_model->bloodbankPaymentByTransactionId($id);
        $data['print_details']       = $print_details;
        $data['blood_issues_detail'] = $blood_issues_detail;
        $data['transactions']        = $this->transaction_model->BloodBankPayments($id);
        $prefix                      = $this->customlib->getPatientSessionPrefixByType('blood_bank_billing');
        $data['prefix']              = $prefix;
        $data['fields']              = $this->customfield_model->get_custom_fields('blood_issue', '', '', '', 1);
        $page                        = $this->load->view('patient/_printBloodIssueBill', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printcomponentIssueBill()
    {
        $print_details               = $this->printing_model->get('', 'bloodbank');
        $id                          = $this->input->post('id');
        $charge                      = array();
        $blood_issues_detail         = $this->bloodissue_model->getcomponentDetail($id);
        $transaction                 = $this->transaction_model->bloodbankPaymentByTransactionId($id);
        $data['print_details']       = $print_details;
        $data['blood_issues_detail'] = $blood_issues_detail;
        $data['transactions']        = $this->transaction_model->BloodBankPayments($id);
        $prefix                      = $this->customlib->getPatientSessionPrefixByType('blood_bank_billing');
        $data['prefix']              = $prefix;
        $data['fields']              = $this->customfield_model->get_custom_fields('component_issue', '', '', '', 1);
        $page                        = $this->load->view('patient/_printcomponentIssueBill', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getbloodissueDatatable($patient_id)
    {
        $fields      = $this->customfield_model->get_custom_fields('blood_issue',"","","",1);
        $dt_response = $this->bloodissue_model->getpatientbloodissueRecord($patient_id);

        $dt_response = json_decode($dt_response);
        $dt_data     = array();

        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $balance_amount = $value->net_amount - $value->paid_amount;
                $row            = array();
                //====================================
                
                $action="";
                $action .= "<a href='#' data-record-id='" . $value->id . "' class='btn btn-default btn-xs viewDetail' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
                $action .= "<a href='#' data-record-id='" . $value->id . "' class='btn btn-default view_payment btn-xs' data-toggle='tooltip' data-module_type='blood_bank' title='" . $this->lang->line('view_payments') . "' ><i class='fa fa-money'></i></a>";

                $action .= "<a href='#' data-record-id='" . $value->id . "' class='btn btn-default btn-xs printIssueBill' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-print'></i></a>";
                $action .= '<a  class="btn btn-primary btn-xs" onclick="payModal(' . $value->id . ',' . $balance_amount . ')" autocomplete="off">' . $this->lang->line('pay') . '</a>';
              
                //==============================
                $row[] = $this->customlib->getPatientSessionPrefixByType('blood_bank_billing') . $value->id ;
                $row[] = $value->case_reference_id ;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->date_of_issue, false);
                $row[] = $value->patient_name . " (" . $value->patient_id . ")";
                $row[] = $value->blood_group;
                $row[] = $this->lang->line(strtolower($value->gender)); 
                $row[] = $value->donor_name;

                $row[] = $this->customlib->bag_string($value->bag_no, $value->volume, $value->unit);
                //====================
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $value->{"$fields_value->name"};

                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $value->{"$fields_value->name"} . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";
                        }
                        $row[] = $display_field;
                    }
                }
                //====================
                $row[]     = $value->net_amount;
                $row[]     = $value->paid_amount;
                $row[]     = amountFormat($balance_amount);
                $row[]     = $action ;
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getpayment()
    {
        $patient_id      = $this->patient_data['patient_id'];
        $id              = $this->input->post('id');
        $module_type     = $this->input->post('module_type');
        $payment_details = array();

        if ($module_type == 'pharmacy') {
            $payment_details = $this->transaction_model->pharmacypaymentbybillid($id, $patient_id);
        } else if ($module_type == 'pathology') {
            $payment_details = $this->transaction_model->pathologypaymentbybillid($id, $patient_id);
        } else if ($module_type == 'radiology') {
            $payment_details = $this->transaction_model->radiologypaymentbybillid($id, $patient_id);
        } else if ($module_type == 'ambulance') {
            $payment_details = $this->transaction_model->ambulancepaymentbybillid($id, $patient_id);
        } else if ($module_type == 'blood_bank') {
            $payment_details = $this->transaction_model->bloodissuepaymentbybillid($id, $patient_id);
        }

        $data['module']          = $module_type;
        $data['payment_details'] = $payment_details;

        $page = $this->load->view('patient/_view_payments', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printbilltransaction()
    {
        $transaction   = "";
        $print_details = $this->printing_model->get('', 'paymentreceipt');
        $id            = $this->input->post('id');
        $module_type   = $this->input->post('module_type');

        if ($module_type == 'pharmacy') {
            $transaction = $this->transaction_model->pharmacyPaymentByTransactionId($id);
        } else if ($module_type == 'pathology') {
            $transaction = $this->transaction_model->pathologyPaymentByTransactionId($id);
        } else if ($module_type == 'radiology') {
            $transaction = $this->transaction_model->radiologyPaymentByTransactionId($id);
        } else if ($module_type == 'ambulance') {
            $transaction = $this->transaction_model->ambulanceCallPaymentByTransactionId($id);
        } else if ($module_type == 'blood_bank') {
            $transaction = $this->transaction_model->bloodbankPaymentByTransactionId($id);
        }

        $data['transaction']   = $transaction;
        $data['print_details'] = $print_details;
        $page                  = $this->load->view('patient/transaction/_printTransaction', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function updateStatus()
    {
        $notification_id = $this->input->post("id");
        $userid          = $this->patient_data['patient_id'];
        $data            = array('notification_id' => $notification_id,
            'receiver_id'                              => $userid,
            'is_active'                                => 'no',
            'date'                                     => date("Y-m-d H:i:s"),
        );
        $this->notification_model->updateReadNotification($data);
    }

	public function downloadreceipt($trans_id)
    {
        $transaction = $this->transaction_model->getTransaction($trans_id);
        $this->load->helper('download');
        $filepath    = "./uploads/payment_document/" . $transaction->attachment;
        $report_name = $transaction->attachment_name;
        $data        = file_get_contents($filepath);
        force_download($report_name, $data);
    }
    
    public function addappointment()
    {
        $this->load->model("onlineappointment_model");
        $patientpanel               = $this->customlib->patientpanel();
        $this->data['patientpanel'] = $patientpanel;
        $this->config->load("payroll");
        $yesno_condition               = $this->config->item('yesno_condition');
        $this->data['yesno_condition'] = $yesno_condition;
        $setting                       = $this->frontcms_setting_model->get();
        $this->load->helper('customfield_helper');
        $this->data['page_side_bar'] = $setting->is_active_sidebar;
        $this->data['active_menu']   = $this->lang->line("appointment");
        $this->data['gender']        = $this->customlib->getGender();
        $this->data['page']          = array('title' => $this->lang->line("appointment"), 'meta_title' => '', 'meta_keyword' => '', 'meta_description' => '');
        $doctors                     = $this->staff_model->getEmployeeByRoleID(3);
        $this->data["doctors"]       = $doctors;
        $specialist                  = $this->staff_model->getSpecialist();
        $this->data["specialist"]    = $specialist;
        $global_shift                = $this->onlineappointment_model->doctorGlobalShift();
        $this->data["global_shift"]  = $global_shift;
        $params                      = $this->input->post("doctor") . ',' . $this->input->post("shift") . ',' . $this->customlib->dateFormatToYYYYMMDD($this->input->post("date")) . ',' . $this->input->post("global_shift");
        $custom_fields               = $this->customfield_model->getByBelongPatientPanel('appointment');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[appointment][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $this->form_validation->set_rules('doctor', $this->lang->line("doctor"), "trim|required|xss_clean");
        $this->form_validation->set_rules('specialist', $this->lang->line("specialist"), "trim|required|xss_clean");
        $this->form_validation->set_rules('date', $this->lang->line("date"), "trim|required|xss_clean");
        $this->form_validation->set_rules('shift', $this->lang->line("slot"), "trim|required|xss_clean");
        $this->form_validation->set_rules('global_shift', $this->lang->line("shift"), "trim|required|xss_clean");
        $this->form_validation->set_rules('slot', $this->lang->line("available_slot"), 'trim|required|callback_check_slot[' . $params . ']');
        $this->form_validation->set_rules('message', $this->lang->line("message"), 'trim|required');

        if ($this->form_validation->run() == false) {            
                $msg = array(
                    'date'       => form_error('date'),
                    'specialist' => form_error('specialist'),
                    'doctor'     => form_error('doctor'),
                    'global_shift' => form_error('global_shift'),
                    'shift'      => form_error('shift'),
                    'message'    => form_error('message'),
                    'slot'       => form_error('slot'),
                );

                if (!empty($custom_fields)) {
                    foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                        if ($custom_fields_value['validation']) {
                            $custom_fields_id                                                    = $custom_fields_value['id'];
                            $custom_fields_name                                                  = $custom_fields_value['name'];
                            $error_msg2["custom_fields[appointment][" . $custom_fields_id . "]"] = form_error("custom_fields[appointment][" . $custom_fields_id . "]");
                        }
                    }
                }
                if (!empty($error_msg2)) {
                    $error_msg = array_merge($msg, $error_msg2);
                } else {
                    $error_msg = $msg;
                }
                $json_array = array('status' => '0', 'error' => $error_msg);
                echo json_encode($json_array);
            
        } else {        
                
                if (empty($this->session->userdata("patient"))) {
                    $this->userlogin();
                }
                $session_data = $this->session->userdata("patient");
                $date         = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date'));
                $shift        = $this->input->post("shift");
                $doctor       = $this->input->post("doctor");
                $global_shift = $this->input->post("global_shift");
                $slots        = $this->customlib->getSlotByDoctorShift($doctor, $shift);
                $live_consult = $this->input->post('live_consult');
                if($live_consult == ''){
                    $live_consult ='no';
                }
            
                $slot         = $slots[$this->input->post("slot")];
                $time         = date("H:i:s", strtotime($slot));
                $day          = date("l", strtotime($date));
                $date_create=date("Y-m-d H:i:s");
                $getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($doctor, $global_shift, $day); 
                $current_time = date('Y-m-d H:i');
                $appointment  = array(
                    "patient_id"         	=> $this->session->userdata("patient")["patient_id"],
                    "specialist"         	=> $this->input->post('specialist'),
                    "doctor"             	=> $this->input->post('doctor'),                   
                    'doctor_shift_time_id'  => $getDoctorShiftTimeId->id,                   
                    "date"               	=> $date." ".$time,
                    "live_consult"       	=> $live_consult,
                    "message"            	=> $this->input->post('message'),
                    "is_queue"           	=> 0,                    
                    "appointment_status" 	=> "pending",
                    "live_consult_link" 	=> 0,
                    "source"             	=> "Online",
                    'priority'           	=> $this->input->post('priority'),
                    'created_time'=>$date_create,                
                    'rejected_time'=>date('Y-m-d H:i:s',strtotime('+10 minutes',strtotime($date_create)))
                );

                $appointments   = $this->onlineappointment_model->checkAppointmentsSlotBooked($this->input->post("doctor"), $getDoctorShiftTimeId->id,  $date." ".$time, $current_time,0);           

                if (!empty($appointments)) {
                  echo   json_encode(['status'=>2,'msg'=>'Appointment not available, Please try after some time.']);
              
                }else{
                   
                    $patient_name = $this->session->userdata("patient")["name"];
                    $mobileno     = $this->session->userdata("patient")["mobileno"];
                    $email        = $this->session->userdata("patient")["email"];
                    $doctor       = $this->input->post('doctor');
                    $appointment = $this->security->xss_clean($appointment);
                    $insert_id   = $this->onlineappointment_model->addAppointment($appointment);
    
                    /* insert custom field start */
    
                    $custom_field_post  = $this->input->post("custom_fields[appointment]");
                    $custom_value_array = array();
                    if (!empty($custom_field_post)) {
                        foreach ($custom_field_post as $key => $value) {
                            $check_field_type = $this->input->post("custom_fields[appointment][" . $key . "]");
                            $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                            $array_custom     = array(
                                'belong_table_id' => 0,
                                'custom_field_id' => $key,
                                'field_value'     => $field_value,
                            );
                            $custom_value_array[] = $array_custom;
                        }
                    }
                    if (!empty($custom_value_array)) {
                        $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                    }
    
                    /* insert custom field end*/
                    $patient_id = $this->session->userdata("patient")["patient_id"];
                    
                    $date_appoint = $this->customlib->dateFormatToYYYYMMDDHis($date." ".$time, $this->customlib->getHospitalTimeFormat());
                    $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('doctor'));
                    $event_data     = array(
                        'appointment_date' => $this->customlib->YYYYMMDDHisTodateFormat($date." ".$time, $this->customlib->getHospitalTimeFormat()),
                        'patient_id'       => $this->session->userdata("patient")["patient_id"],
                        'doctor_id'        => $this->input->post('doctor'),
                        'doctor_name'      => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                        'message'          => $this->input->post('message'),
                    );
    
                    $this->system_notification->send_system_notification('notification_appointment_created', $event_data);					
					
					// $sender_details = array('patient_name' => $patient_name, 'doctor' => $doctor, 'date' => $date, 'time' => $time, 'contact_no' => $mobileno, 'email' => $email,'patient_id'=>$patient_id,'appointment_id'=>$insert_id);					
                    // $this->mailsmsconf->mailsms('appointment_approved', $sender_details);					
					
                    $this->session->set_flashdata("success_msg",$this->lang->line("success_message"));
                    $json_array = array('status' => 1, 'msg' => "Appointment Booked");
                    echo json_encode($json_array);  
                }                          
        }
    }
    
    public function check_slot($slot, $params)
    {
        if ($slot == '') {
            $this->form_validation->set_message('check_slot', $this->lang->line("available_slots_field_is_required"));
            return false;
        }
        list($doctor_id, $shift, $date, $global_shift) = explode(',', $params);
        $appointments                                  = $this->onlineappointment_model->getAppointments($doctor_id, $shift, $date);
        $time                                          = $this->customlib->getSlotByDoctorShift($doctor_id, $shift);
        $array                                         = array_column($appointments, 'time');
        if ($slot != '' && $doctor_id != '' && $shift != '' && $date != '') {
            if (count($time) > $slot) {
                $shift_time = date("H:i:s", strtotime($time[$slot]));
                if (in_array($shift_time, $array)) {
                    $this->form_validation->set_message('check_slot', $this->lang->line('this_slot_is_already_booked'));
                    return false;
                } else {
                    return true;
                }
            }
        }
    }

    public function getBloodIssueDetail()
    {        
        $id                  = $this->input->post("blood_issue_id");
        $data['result']      = $this->bloodissue_model->getBloodIssuePatient($id);
        $data['fields']      = $this->customfield_model->get_custom_fields('blood_issue');
        $data['bill_prefix'] = $this->customlib->getpatientSessionPrefixByType('blood_bank_billing');
        $action              = "";
        $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $id . "' class='printIssueBill'  data-toggle='tooltip' title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";
        $page = $this->load->view('patient/_getBloodIssueDetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'action' => $action));
    }

    public function getComponentIssueDetail()
    {        
        $id             = $this->input->post("blood_issue_id");
        $data['result'] = $this->bloodissue_model->getcomponentDetail($id);
        $data['prefix'] = $this->customlib->getpatientSessionPrefixByType('blood_bank_billing');
        $data['fields'] = $this->customfield_model->get_custom_fields('component_issue');
        $action         = "";
        $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $id . "' class='printcomponentIssueBill'  data-toggle='tooltip' title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";
        $page = $this->load->view('patient/_getcomponentIssueDetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'action' => $action));
    }

    public function printbill()
    { 
        $opd_id                   = $this->input->post('opd_id');
        $opddata                  = $this->patient_model->getVisitDetailsbyopdid($opd_id);
        $data['blood_group_name'] = $opddata['blood_group_name'];
        $data["print_details"]    = $this->printing_model->get('', 'opd');
        $data["result"]           = $opddata;
         
        if (!empty($opddata)) {
            $patient_charge_id = $opddata['patient_charge_id'];
            $charge            = $this->charge_model->getChargeById($patient_charge_id);
            $data['charge']    = $charge;
            if (!empty($opddata['transaction_id'])) {
                $transaction         = $this->transaction_model->getTransaction($opddata['transaction_id']);
                $data['transaction'] = $transaction;
            }
        }
        $page = $this->load->view('patient/_printbill', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));

    }

    public function printtestparameterdetail()
    {
        $print_details         = $this->printing_model->get('', 'pathology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
        $data['id']            = $id;
        $result                = $this->pathology_model->gettestparameterdetails($id);
        $data['head_result']   = $this->pathology_model->getPathologyBillByID($id);
        $data['result']        = $result;
        $data['bill_prefix'] = $this->customlib->getPatientSessionPrefixByType('pathology_billing');
        $page                  = $this->load->view('patient/pathology/_printtestparameterdetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printradiotestparameterdetail()
    {
        $print_details         = $this->printing_model->get('', 'radiology');
        $data['print_details'] = $print_details;
        $id                    = $this->input->post('id');
        $data['id']            = $id;
        $data['head_result']   = $this->radio_model->getRadiologyBillByID($id);
        $result                = $this->radio_model->printtestparameterdetail($id);
        $data['bill_prefix']   = $this->customlib->getPatientSessionPrefixByType('radiology_billing');
        $data['result']        = $result;
        $page                  = $this->load->view('patient/radiology/_printtestparameterdetail', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }
	
	public function contentlist()
    {
        $this->session->set_userdata('top_menu', 'content_list');
		$patient_id        = $this->customlib->getPatientSessionUserID();
		$data['superadmin_restriction'] = $this->patient_data['superadmin_restriction']; 
        $data['contentlist'] = $this->sharecontent_model->getPatientShareList($patient_id);  
		$data['timeformat']     = $this->time_format;
        $this->load->view("layout/patient/header");
        $this->load->view("patient/contentlist", $data);
        $this->load->view("layout/patient/footer");
    }
	
	public function viewcontent($id)
    {
        $this->session->set_userdata('top_menu', 'content_list'); 
		$data['superadmin_restriction'] = $this->patient_data['superadmin_restriction'];         
		$data['content']    			= $this->sharecontent_model->getShareContentWithDocuments($id);	
		$data['timeformat']     = $this->time_format;
        $this->load->view("layout/patient/header");
        $this->load->view("patient/viewcontent", $data);
        $this->load->view("layout/patient/footer");
    }
    
}
