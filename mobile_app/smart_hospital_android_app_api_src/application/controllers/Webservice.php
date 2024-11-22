<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Webservice extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mailer');
        $this->load->library('system_notification');
        $this->load->library(array('customlib', 'enc_lib', 'role'));
        $this->patient_data = $this->session->userdata('patient');
        $this->load->model(array('auth_model', 'setting_model', 'user_model', 'webservice_model', 'module_model', 'customfield_model', 'radiology_model', 'transaction_model', 'onlineappointment_model', 'bloodbank_model', 'notification_model', 'conference_model', 'calendar_model', 'pharmacy_model', 'pathology_model', 'operationtheatre_model', 'prescription_model', 'bed_model', 'timeline_model', 'charge_model', 'appointment_model', 'vital_model', 'sharecontent_model', 'holiday_model', 'antenatal_model')); 
		$this->sch_setting_detail     = $this->setting_model->getSetting();
    }

    public function verifyUrl()
    {
        $data             = $this->webservice_model->getappdetails();
        $data['site_url'] = base_url();
        echo json_encode($data);
    }

    public function logout()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $response = $this->auth_model->logout();
            json_output($response['status'], $response);
        }
    }

//============================ Patient Profile =================================================

    public function getPatientProfile()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                if ($check_auth_client == true) {
                    $response = $this->auth_model->auth();
                    if ($response['status'] == 200) {
                        $params    = json_decode(file_get_contents('php://input'), true);
                        $patientId = $params['patientId'];
                        $resp      = $this->webservice_model->getPatientProfile($patientId);
                        
                        if($resp[0]['address'] ==''){
                            $resp[0]['address'] = '';
                        }
                        if($resp[0]['guardian_name'] ==''){
                            $resp[0]['guardian_name'] = '';
                        }
                        if($resp[0]['image'] ==''){
                            $resp[0]['image'] = '';
                        }					
						
						$resp[0]['barcode'] = "/uploads/patient_id_card/barcodes/" . $patientId . ".png";
						$resp[0]['qrcode'] = "/uploads/patient_id_card/qrcode/" . $patientId . ".png";
                        
                        json_output($response['status'], $resp);
                    }
                }
            }
        }
    }

//========================= live consult Details ====================================================

    public function getliveconsult()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                if ($check_auth_client == true) {
                    $response = $this->auth_model->auth();
                    if ($response['status'] == 200) {
                        $params      = json_decode(file_get_contents('php://input'), true);
                        $patient_id  = $params['patient_id'];
                        $liveconsult = array();
                        $liveconsult = $this->conference_model->getconfrencebypatient($patient_id);

                        if (!empty($liveconsult)) {
                            foreach ($liveconsult as $lc_key => $lc_value) {
                                $live_url                           = json_decode($lc_value->return_response);
                                $liveconsult[$lc_key]->{'join_url'} = $live_url->join_url;
                                unset($lc_value->return_response);
                            }
                        }

                        $data["liveconsult"] = $liveconsult;
                        json_output($response['status'], $data);
                    }
                }
            }
        }
    }

//========================= Patient OPD Details ====================================================

    public function getOPDDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                if ($check_auth_client == true) {
                    $response = $this->auth_model->auth();
                    if ($response['status'] == 200) {
                        $params        = json_decode(file_get_contents('php://input'), true);
                        $patient_id    = $params['patient_id'];
                        $investigation = array();
                        $result        = array();
                        $timeline_list = array();
						
                        if (!empty($patient_id)) {
                            $result                = $this->webservice_model->getOPDDetails($patient_id);
                            $timeline_list         = $this->webservice_model->getPatientTimeline($patient_id, $timeline_status = 'yes');
                            $investigation_details = $this->webservice_model->allinvestigationbypatientid($patient_id);
                            $treatmenthistory      = $this->webservice_model->getopdtreatmenthistory($patient_id);           
                        }
                        
                        $data["result"]                = $result;
                        $data["timeline_list"]         = $timeline_list;
                        $data["investigation_details"] = $investigation_details;
                        $data["treatmenthistory"]      = $treatmenthistory;
                        $data['patientdetails']        = $this->patient_model->getpatientoverview($patient_id);
                        $data['vital_list'] 		   = $vital_list = $this->vital_model->getvitallist();					 
                
						foreach($vital_list as $vital_list_value){
							
							$vital_id = $vital_list_value['id'];
							$name = $vital_list_value['name'];
								
							$vital_messure_date = $this->vital_model->getpatientvitaldate($patient_id,$vital_id);
							$datewisevital =array();
							
							foreach($vital_messure_date as $key => $vital_messure_date){
								$vital_date = $this->vital_model->getpatientsvitaldetails($patient_id,$vital_id,$vital_messure_date['messure_date']);								
								$datewisevital[] = $vital_date;								
							}
									
							$patient_vital_list[][$name] = $datewisevital;						 
							
						}			
					
						if(!empty($patient_vital_list)){
							$data['patient_vital_list']	=	$patient_vital_list;
						}else{
							$data['patient_vital_list']	=	'';
						}	
				
                        json_output($response['status'], $data);
                    }
                }
            }
        }
    }

    public function getOPDVisitDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                if ($check_auth_client == true) {
                    $response = $this->auth_model->auth();
                    if ($response['status'] == 200) {
                        $params            = json_decode(file_get_contents('php://input'), true);
                        $opd_id          = $params['opd_id'];
                        $visit_details     = array();
                        $charges           = array();
                        $payment           = array();
                        $investigation     = array();
                        $timeline          = array();
                        $medication_data        = array();
                        $operation_theatre = array();
                        $conferences       = array();

                        if ($opd_id) {
                            $result = $this->webservice_model->getDetails($opd_id);
							 
                            $visit_details = $this->webservice_model->getVisitRechekup($opd_id);
                            $charges = $this->webservice_model->getOPDCharges($opd_id);
                            $payment = $this->webservice_model->getOPDPayment($opd_id);
                            if (!empty($result['case_reference_id'])) {
                                $investigation = $this->webservice_model->getallinvestigation($result['case_reference_id']);
                                $data['patientdetails']     = $this->patient_model->getpatientoverviewbycaseid($result['case_reference_id'],$opd_id);
                                $data['graph'] = $this->transaction_model->opd_bill_paymentbycase_id($result['case_reference_id']);                                 
                            } else {
                                $investigation = '';
                                $data['patientdetails']     = '';
                                $data['graph']     = '';
                            }
                            $timeline          = $this->webservice_model->getPatientTimeline($result['patient_id']);
                            $medication        = $this->webservice_model->getmedicationdetailsbydateopd($opd_id);
                            $operation_theatre = $this->webservice_model->getopdoperationDetails($opd_id); 
                            $getVisitDetailsid = $this->webservice_model->getVisitDetailsid($opd_id);       
                            $conferences       = $this->webservice_model->getconfrencebyvisitid($getVisitDetailsid);
                            //============  
                            
                            foreach ($medication as $medication_key => $medication_value) {     
                                $medicine_array = array();
            
                                foreach ($medication_value['dose_list'] as $dose_key => $dose_value) {   
                                    
                                    $find = null;
            
                                    if (!empty($medicine_array)) {
                                        $find = searchForId($dose_value['pharmacy_id'], $medicine_array,'pharmacy_id');
                                    }
            
                                    if (is_null($find)) {
                                        $medicine_array[] = array(
                                            'pharmacy_id'   => $dose_value['pharmacy_id'],
                                            'medicine_name' => $dose_value['medicine_name'],
                                            'doses'         => array(array('date' => $dose_value['date'], 'time' => $dose_value['time'], 'remark' => $dose_value['remark'], 'medicine_dosage' => $dose_value['medicine_dosage'], 'unit' => $dose_value['unit'])),
                                        );
                                    } else {
                                        $medicine_array[$find]['doses'][] = array('date' => $dose_value['date'], 'time' => $dose_value['time'], 'remark' => $dose_value['remark'], 'medicine_dosage' => $dose_value['medicine_dosage'], 'unit' => $dose_value['unit']);
                                    }
                                }
            
                                $final_medicines                    = array();
                                $final_medicines['medicine_date']   = $medication_value['date'];
                                $final_medicines['medicine_day']   = date('l',strtotime($medication_value['date']));
                                
                                $final_medicines['medicine']        = $medicine_array;
                                $medication_data[] = $final_medicines;            
                            }
                            //============
                        }
                      
                        $data['visit_details']        = $visit_details;
                        $data["charges_detail"]       = $charges;
                        $data["payment_detail"]       = $payment;
                        $data["investigation_detail"] = $investigation;
                        $data["timeline_detail"]      = $timeline;
                        $data["medication_detail"]    = $medication_data;
                        $data["ot_detail"]            = $operation_theatre;
						
						foreach ($conferences as $conferences_key => $conferences_value) {  
						
							$return_response = isJSON($conferences_value->return_response) ? json_decode($conferences_value->return_response):false;						 
							$conferences[$conferences_key]->join_url = $return_response->join_url;
						 
						}
						
                        $data["conferences_detail"]   = $conferences; 
                        
						$data['vital_list'] 		= 	$vital_list = $this->vital_model->getvitallist();					 
                
						foreach($vital_list as $vital_list_value){
					
							$vital_id = $vital_list_value['id'];
							$name = $vital_list_value['name'];
						
							$vital_messure_date = $this->vital_model->getpatientvitaldate($result['patient_id'],$vital_id);
							$datewisevital =array();
					
							foreach($vital_messure_date as $key => $vital_messure_date){
								$vital_date = $this->vital_model->getpatientsvitaldetails($result['patient_id'],$vital_id,$vital_messure_date['messure_date']);								
								$datewisevital[] = $vital_date;								
							}
							
							$patient_vital_list[][$name] = $datewisevital;
						}			
			 
						if(!empty($patient_vital_list)){
							$data['patient_vital_list']	=	$patient_vital_list;
						}else{
							$data['patient_vital_list']	=	'';
						}	
				
                        json_output($response['status'], $data);
                    }
                }
            }
        }
    }

    public function getopdantenatal()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params         = json_decode(file_get_contents('php://input'), true);
                    $visitid        = $params['visitid'];
                    $result         = $this->antenatal_model->getopdantenatal($visitid);
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getopdprescription()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params         = json_decode(file_get_contents('php://input'), true);
                    $visitid        = $params['visitid'];
                    $result         = $this->webservice_model->getPrescriptionByVisitID($visitid);
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getopdvisitprescription()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();

                if ($response['status'] == 200) {
                    $params         = json_decode(file_get_contents('php://input'), true);
                    $opdid          = $params['opd_id'];
                    $visit_id       = $params['visit_id'];
                    $result         = $this->webservice_model->getopdprescription($opdid, $visit_id);
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }    
    
    public function addopdtimeline()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params             = json_decode(file_get_contents('php://input'), true);
                    $patient_id         = $this->input->post('patient_id');
                    $timeline_title     = $this->input->post('timeline_title');
                    $timeline_date      = $this->input->post('timeline_date');
                    $timeline_desc      = $this->input->post('timeline_desc');
                    $user_id            = $this->input->post('user_id');            
                        
                        $timeline      = array(
                            'title'                => $timeline_title,
                            'timeline_date'        => $timeline_date,
                            'description'          => $timeline_desc,
                            'date'                 => date('Y-m-d'),
                            'status'               => 'yes',
                            'patient_id'           => $patient_id,
                            'generated_users_type' => 'patient',
                            'generated_users_id'   => $user_id,
                        );

                        $id = $this->timeline_model->add_patient_timeline($timeline);             
                        
                        $upload_path = $this->config->item('upload_path') . "/patient_timeline/";

                        if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                            $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                            $img_name = $id . '.' . $fileInfo['extension'];
                            move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $upload_path . $img_name);
                            $data = array('id' => $id, 'document' => $img_name);
                            $this->timeline_model->add_patient_timeline($data);
                        }           
            
                        $array = array('status' => 'success', 'error' => '', 'message' => 'Data Inserted Successfully');
                   
                    json_output(200, $array);
                }
            }
        }
    }
    
    public function updateopdtimeline()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $id         = $this->input->post('id');
                    $patient_id         = $this->input->post('patient_id');
                    $timeline_title     = $this->input->post('timeline_title');
                    $timeline_date      = $this->input->post('timeline_date');
                    $timeline_desc      = $this->input->post('timeline_desc');
                    $user_id            = $this->input->post('user_id');            
                        
                        $timeline      = array(
                            'id'                   => $id,
                            'title'                => $timeline_title,
                            'timeline_date'        => $timeline_date,
                            'description'          => $timeline_desc,
                            'date'                 => date('Y-m-d'),
                            'status'               => 'yes',
                            'patient_id'           => $patient_id,
                            'generated_users_type' => 'patient',
                            'generated_users_id'   => $user_id,

                        );

                        $this->timeline_model->add_patient_timeline($timeline);             
                        
                        $upload_path = $this->config->item('upload_path') . "/patient_timeline/";

                        if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                            $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                            $img_name = $id . '.' . $fileInfo['extension'];
                            move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $upload_path . $img_name);
                            $data = array('id' => $id, 'document' => $img_name);
                            $this->timeline_model->add_patient_timeline($data);
                        }           
            
                        $array = array('status' => 'success', 'error' => '', 'message' => 'Data Updated Successfully');
                   
                    json_output(200, $array);
                }
            }
        }
    } 
    
    public function deleteopdtimeline()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params        = json_decode(file_get_contents('php://input'), true);
                    $id = $params['id'];  
                    if (!empty($id)) {
                        $result = $this->timeline_model->deleteopdtimeline($id);
                    }
                    json_output($response['status'], $result);
                }
            }
        }
    }
   
//===================================== Pharmacy ================================================

    public function getPharmacyDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();

                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $patient_id = $params['patient_id'];
                    if ($patient_id) {
                        $result = $this->pharmacy_model->getBillDetailsPharma($patient_id);
                    }
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getPharmacyMedicineDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), true);
                    $billid = $params['billid'];
                    if ($billid) {
                        $detail = $this->webservice_model->getAllBillDetailsPharma($billid);
                    }
                    $data['detail'] = $detail;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getPayment()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), true);

                    $patient_id  = $params['patient_id'];
                    $bill_id     = $params['bill_id'];
                    $module_type = $params['module_type'];

                    if ($module_type == 'pharmacy') {
                        $payment_details = $this->transaction_model->pharmacypaymentbybillid($bill_id, $patient_id);
                    } else if ($module_type == 'pathology') {
                        $payment_details = $this->transaction_model->pathologypaymentbybillid($bill_id, $patient_id);
                    } else if ($module_type == 'radiology') {
                        $payment_details = $this->transaction_model->radiologypaymentbybillid($bill_id, $patient_id);
                    } else if ($module_type == 'ambulance') {
                        $payment_details = $this->transaction_model->ambulancepaymentbybillid($bill_id, $patient_id);
                    } else if ($module_type == 'blood_bank') {
                        $payment_details = $this->transaction_model->bloodissuepaymentbybillid($bill_id, $patient_id);
                    }

                    foreach($payment_details as $key => $value){
                        if(!empty($value['note'])){
                            $payment_details[$key]['note']  =   $value['note'];
                        }else{
                            $payment_details[$key]['note']  =  '';
                        }
                    }
                    
                    $data['module'] = $module_type;
                    $data['detail'] = $payment_details;

                    json_output($response['status'], $data);
                }
            }
        }
    }

//===================================Pathology==========================================================

    public function getPathologyDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $patient_id = $params['patient_id'];
                    if ($patient_id) {
                        $result = $this->pathology_model->getBillDetailsPatho($patient_id);
                    }
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getPathologyParameterDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params    = json_decode(file_get_contents('php://input'), true);
                    $report_id = $params['reportid'];
                    if ($report_id) {
                        $parameterdetails = $this->webservice_model->getparameterDetailspatho($report_id);
                    }
                    $data['pathology_parameter'] = $parameterdetails;
                    json_output($response['status'], $data);
                }
            }
        }
    }

//====================Radiology=======================================================================

    public function getRadiologyDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $patient_id = $params['patient_id'];
                    if ($patient_id) {
                        $result = $this->radiology_model->getBillDetailsRadio($patient_id);
						foreach($result as $key => $value){
							$ipd_opd = $this->radiology_model->getIpdPrescriptionBasic($value['ipd_prescription_basic_id']);
							
							if(!empty($ipd_opd)){
								if($ipd_opd->ipd_id != ''){             
									$result[$key]['type']   =   "IPD";  
								}else{
									$result[$key]['type']   =   "OPD";  
								} 
							}else{
								$result[$key]['type']   = '';
							}
						}
                    }
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getRadiologyParameterDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params    = json_decode(file_get_contents('php://input'), true);
                    $report_id = $params['report_id'];
                    if ($report_id) {
                        $result = $this->radiology_model->getparameterDetailsradio($report_id);							
							$ipd_opd = $this->radiology_model->getIpdPrescriptionBasic($result->ipd_prescription_basic_id);
							
							if(!empty($ipd_opd)){
								if($ipd_opd->ipd_id != ''){             
									$result->type   =   "IPD";  
								}else{
									$result->type   =   "OPD";  
								} 
							}else{
								$result->type   = '';
							}
						 
                    }
                    $data['radiology_parameter'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }

//=========================== OT ====================================================================

    public function getOTDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params    = json_decode(file_get_contents('php://input'), true);
                    $patientId = $params['patientId'];
                    if ($patientId) {
                        $result = $this->webservice_model->getBillDetailsOt($patientId);
                    }
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }

//=================================== Ambulance ====================================================

    public function getAmbulanceDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $patient_id = $params['patient_id'];
					
                    if ($patient_id) {
                        $result = $this->webservice_model->getBillDetailsAmbulance($patient_id);
                    }
                   
                    $data['result'] = $result;                   
                    json_output($response['status'], $data);
                }
            }
        }
    }

//=================================== Blood Bank ================================================

    public function getbloodbankDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $patient_id = $params['patient_id'];
                    if ($patient_id) {
                        $bloodissue     = $this->bloodbank_model->getbloodissue($patient_id);
                        $bloodcomponent = $this->bloodbank_model->getcomponent($patient_id);                       
                    }
                    $data['bloodissue']     = $bloodissue;
                    $data['bloodcomponent'] = $bloodcomponent;
                    json_output($response['status'], $data);
                }
            }
        }
    }

//================================= Appointment =================================================

    public function getAppointment()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params         = json_decode(file_get_contents('php://input'), true);
                    $patient_id     = $params['patient_id'];
                    $result         = $this->webservice_model->getAppointment($patient_id);
					
					foreach($result as $key => $value){
						if($value['appointment_serial_no']==''){
							$result[$key]['appointment_serial_no']      = '';                        
						}
					}
					
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }
    
    public function getAppointmentDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params         = json_decode(file_get_contents('php://input'), true);
                    $id     = $params['appointment_id'];             
                    
                    $result = $this->appointment_model->getDetailsAppointment($id);
                    if ($result['appointment_status'] == 'approved') {
                        $prefixes   =   $this->webservice_model->getprefixes('appointment') ;
                        $result['appointment_no'] = $prefixes->prefix . $id;            
                    }
					
                    if($result['appointment_status']=='approved'){
                        $result['transaction_id']      = $result['transaction_id'];
                        $result['payment_mode']  = $result['payment_mode'];
                    }else{
                        $result['transaction_id']  = "";
                    } 
					
					if($result['start_time'] == ''){
                        $result['start_time']	= '';                         
                    }
					
					if($result['end_time'] == ''){
						$result['end_time']		= '';                         
                    }				
                   
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function addAppointmentFront() 
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'result' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();

            if ($check_auth_client == true) {
                $params             = json_decode(file_get_contents('php://input'), true);
                $appointment_date   	= $params['date']; //======== YYYY/MM/DD
                $appointment_time   	= $params['time'];
                $appointment_doctor 	= $params['doctor'];
                $appointment_msg    	= $params['message'];
                $live_consult       	= $params['live_consult'];
                $specialist         	= $params['specialist'];                
                $global_shift   		= $params['global_shift'];
                $patient_type       	= $params['patient_type'];
				$appointment_status 	= 'pending';
                $source             	= 'Online';
				
                $this->form_validation->set_data($params);
                $this->form_validation->set_error_delimiters('', '');
        
				$custom_fields = $this->customfield_model->getcustomfields('appointment');
				foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
		
					if ($custom_fields_value['validation']) {
						$custom_fields_id   = $custom_fields_value['id'];
						$custom_fields_name   = $custom_fields_value['name'];					
						$this->form_validation->set_rules("custom_fields[".$custom_fields_key."][field_value]", $custom_fields_name, 'trim|required');
					}
				}

                $this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
                $this->form_validation->set_rules('doctor', 'Doctor', 'trim|required|xss_clean');
                $this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');
                $error_msg2=array();
                if ($this->form_validation->run() == false) {

                    if ($patient_type == 'new') {
                        $msg = array(
                            'date'           => form_error('date'),
                            'doctor'         => form_error('doctor'),
                            'message'        => form_error('message'),
                            'patient_name'   => form_error('patient_name'),
                            'patient_email'  => form_error('patient_email'),
                            'patient_gender' => form_error('patient_gender'),
                            'patient_phone'  => form_error('patient_phone'),
                        );
                    } elseif ($patient_type == 'old') {
                        $msg = array(
                            'date'    => form_error('date'),
                            'doctor'  => form_error('doctor'),
                            'message' => form_error('message'),
                        );
                    }

					if (!empty($custom_fields)) {
						foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
							if ($custom_fields_value['validation']) {
								$custom_fields_id     = $custom_fields_value['id'];
								$custom_fields_name   = $custom_fields_value['name'];
		
								$error_msg2["custom_field_".$custom_fields_id] = form_error("custom_fields[".$custom_fields_key."][field_value]");
							}
						}
						}
		
					if (!empty($error_msg2)) {
						$error_msg = array_merge($msg, $error_msg2);
					} else {
						$error_msg = $msg;
					}

                    $array = array('status' => '0', 'result' => 'Data not Inserted','data' => $error_msg);
                } else {

                    if ($patient_type == 'new') {
                        $patient_email = $params['patient_email'];
                        $patient_phone = $params['patient_phone'];

                        if (($patient_phone != "") && ($patient_email != "")) {
                            $result = $this->patient_model->checkmobileemail($patient_phone, $patient_email);
                            if ($result == 1) {
                                $jsons = array(
                                    'phone_email_exist' => 'Mobile Email Already Exist',
                                );
                                $json_array = array('status' => '0', 'data' => $jsons);
                                echo json_encode($json_array);
                                die;
                            }
                        }
                        if ($patient_phone != "") {
                            $result = $this->patient_model->checkmobilenumber($patient_phone);
                            if ($result == 1) {
                                $jsons = array(
                                    'mobile_exist' => 'Mobile Already Exist',
                                );
                                $json_array = array('status' => '0', 'data' => $jsons);
                                echo json_encode($json_array);
                                die;
                            }
                        }
                        if ($patient_email != "") {
                            $result = $this->patient_model->checkemail($patient_email);
                            if ($result == 1) {
                                $jsons = array(
                                    'email_exist' => 'Email Already Exist',
                                );
                                $json_array = array('status' => '0', 'data' => $jsons);
                                echo json_encode($json_array);
                                die;
                            }
                        }

                        $patient_data = array(
                            'patient_name' => $params['patient_name'],
                            'mobileno'     => $params['patient_phone'],
                            'email'        => $params['patient_email'],
                            'gender'       => $params['patient_gender'],
                            'is_active'    => 'yes',
                        );

                        $patient_data  = $this->security->xss_clean($patient_data);
                        $insert_id     = $this->patient_model->add_front_patient($patient_data);						
                        $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

                        $username           = "pat" . $insert_id;
                        $data_patient_login = array(
                            'username' => $username,
                            'password' => $user_password,
                            'user_id'  => $insert_id,
                            'role'     => 'patient',
                        );
                        $data_patient_login = $this->security->xss_clean($data_patient_login);
                        $this->user_model->add($data_patient_login);
						
						$scan_type= $this->sch_setting_detail->scan_code_type;
						$this->customlib->generatebarcode($insert_id,$scan_type);//generate barcode and qrcode
			
						$valid = 1;
                    } elseif ($patient_type == 'old') {

                        $oldpatient = $this->auth_model->patientlogin($params['username'], $params['password']);
						 
						if (isset($oldpatient) && !empty($oldpatient)) {
							$insert_id          = $oldpatient['user_id'];
							$data_patient_login = array(
								'username' => $params['username'],
								'password' => $params['password'],
							);
							$valid = 1;
						} else {							 
							$array = array('status' => '0', 'result' => 'invalid username or password','data' => '');						 
							$valid = 0;							
						}
                    }

					if($valid != 0){
					
					$day          = date("l", strtotime($appointment_date));					
					$getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($appointment_doctor, $global_shift, $day); 
					
                    $appointment = array(
                        'patient_id'         => $insert_id,
                        'date'               => $appointment_date . " " . $appointment_time,
                        'doctor'             => $appointment_doctor,
                        'specialist'         => $specialist,
                        'message'            => $appointment_msg,
                        'live_consult'       => $live_consult,
                        'doctor_shift_time_id'       => $getDoctorShiftTimeId->id,
                        'appointment_status' => 'pending',
                        'source' => $source,
                        'is_queue' => 0,
                    );

                    $post_custom_field=array();
                    if(isset($params['custom_fields']) && !empty($params['custom_fields'])){
						$post_custom_field=$params['custom_fields'];
                    }
                   
                    $this->webservice_model->addAppointment($appointment,$post_custom_field);                    
                    $doctor_details = $this->webservice_model->getstaffDetails($appointment_doctor);
                    
                        $event_data     = array(
                            'appointment_date' => $this->customlib->YYYYMMDDHisTodateFormat($appointment_date." ".$appointment_time, $this->customlib->getHospitalTimeFormat()),
                            'patient_id'       => $insert_id,
                            'doctor_id'        => $appointment_doctor,
                            'doctor_name'      => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                            'message'          => $appointment_msg,
                        );

                        $this->system_notification->send_system_notification('notification_appointment_created', $event_data);
                        
                    $array = array('status' => 'success', 'error' => '', 'result' => 'Data Inserted Successfully');
                    $array = array('status' => '1','result' => 'Data Inserted Successfully','data' => $data_patient_login);
				}
                }
                json_output(200, $array);
            }
        }
    }

    public function addAppointment()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'result' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params             = json_decode(file_get_contents('php://input'), true);
                    $patientId          = $params['patientId'];
                    $appointment_date   = $params['date']; //======== YYYY/MM/DD
                    $appointment_time   = date('H:i', strtotime($params['time']));				
					
                    $appointment_doctor = $params['doctor'];
                    $appointment_msg    = $params['message'];
                    $live_consult       = $params['live_consult'];
                    $specialist         = $params['specialist']; 
                    $global_shift       = $params['global_shift'];
                    $patient_type       = $params['patient_type'];
                    $priority       	= $params['priority'];

                    $this->form_validation->set_data($params);
                    $this->form_validation->set_error_delimiters('', '');
                    $this->form_validation->set_rules('date', 'Date', 'required');
                    $this->form_validation->set_rules('doctor', 'Doctor', 'required');
                    $this->form_validation->set_rules('message', 'Message', 'required');
					
					$day          = date("l", strtotime($appointment_date));
					$getDoctorShiftTimeId = $this->onlineappointment_model->getDoctorShiftTimeId($appointment_doctor, $global_shift, $day); 
					
                    if ($this->form_validation->run() == false) {
                        $msg = array(
                            'date'    => form_error('date'),
                            'doctor'  => form_error('doctor'),
                            'message' => form_error('message'),

                        );
                        $array = array('status' => 'fail', 'error' => $msg, 'result' => '');
                    } else {
                        $appointment = array(
                            'patient_id'         => $patientId,
                            'date'               => $appointment_date . " " . $appointment_time,
                            'doctor'             => $appointment_doctor,
                            'message'            => $appointment_msg,
                            'live_consult'       => $live_consult,
                            'live_consult_link'       => 0,
                            'specialist'        => $specialist,
                            'doctor_shift_time_id'  => $getDoctorShiftTimeId->id, 
                            'appointment_status' => 'pending',
                            'is_queue' => 0,
                            'source' =>'Online',
                            'priority' => $priority,
                        );

                        $this->webservice_model->addAppointment($appointment);
                        $doctor_details = $this->webservice_model->getstaffDetails($appointment_doctor);
                    
                        $event_data     = array(
                            'appointment_date' => $this->customlib->YYYYMMDDHisTodateFormat($appointment_date." ".$appointment_time, $this->customlib->getHospitalTimeFormat()),
                            'patient_id'       => $patientId,
                            'doctor_id'        => $appointment_doctor,
                            'doctor_name'      => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                            'message'          => $appointment_msg,
                        );

                        $this->system_notification->send_system_notification('notification_appointment_created', $event_data);               
                
                        $array = array('status' => 'success', 'error' => '', 'result' => 'Data Inserted Successfully');
                    }
                    json_output(200, $array);
                }
            }
        }
    }

    public function deleteAppointment()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params        = json_decode(file_get_contents('php://input'), true);
                    $appointmentId = $params['appointmentId'];
                    if (!empty($appointmentId)) {
                        $result = $this->webservice_model->deleteAppointment($appointmentId);
                    }
                    json_output($response['status'], $result);
                }
            }
        }
    }

    public function doctorshiftbyid()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $params         = json_decode(file_get_contents('php://input'), true);
                $doctor_id      = $params['doctor_id'];
                $result         = $this->onlineappointment_model->doctorShiftById($doctor_id);
                $data['result'] = $result;
                json_output($response['status'] = 200, $data);
            }
        }
    }
	
    public function getslot()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), true);

                $dates          = $params['date'];
                $date           = date('Y-m-d', strtotime($dates));
                $doctor         = $params['doctor_id'];
                $global_shift   = $params['global_shift'];
                
                $getDoctorGlobalShiftId = $this->onlineappointment_model->getDoctorGlobalShiftId($doctor, $global_shift);			
               
                $day            = date("l", strtotime($date));
                $result         = $this->onlineappointment_model->getShiftdata($doctor, $day,  $getDoctorGlobalShiftId['id']);
				 
                $data['result'] = $result;
                json_output($response['status'] = 200, $data);
            }
        }
    }
	
	public function getslotbyshift()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), true);

                $doctor_id    	= $params['doctor_id'];
                $shift        	= $params['shift'];
                $date         	= $params['date'];
                $global_shift   = $params['global_shift'];				
                $day          	= date("l", strtotime($date));			
							
                $array_of_time   		= $this->onlineappointment_model->slotByDoctorShift($doctor_id, $shift);
                $appointments 			= $this->onlineappointment_model->getAppointments($doctor_id, $shift, $date);
				
                $result       = array();		 
				 
				foreach ($array_of_time as $time) {
					if (!empty($appointments)) {
						foreach ($appointments as $appointment) {						 
							if (date("H:i:s", strtotime($appointment->date)) == date("H:i:s", strtotime($time))) {			 
								$filled = "1";
								break;
							} else {								 
								$filled = "0";
							}
						}		
						array_push($result, array("time" => $this->customlib->getHospitalTime_FormatFrontCMS($time), "filled" => $filled));
					} else {
						array_push($result, array("time" => $this->customlib->getHospitalTime_FormatFrontCMS($time), "filled" => "0"));
					}
				}

                $data['result'] = $result;
                json_output($response['status'] = 200, $data);
            }
        }
    }

//============================================== Hospital Details ===========================

    public function getHospitalDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                if ($check_auth_client == true) {
                    $response = $this->auth_model->auth();
                    if ($response['status'] == 200) {
                        $params              = json_decode(file_get_contents('php://input'), true);
                        $setting_result      = $this->setting_model->get();
                        $data['settinglist'] = $setting_result;
                        json_output($response['status'], $data);
                    }
                }
            }
        }
    }

//============================================ Notification Details ===============================

    public function getNotifications()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params       = json_decode(file_get_contents('php://input'), true);
                    $patient_id   = $params['patient_id'];
                    $resp         = $this->notification_model->getNotifications($patient_id);
                    $data['resp'] = $resp;
                    json_output($response['status'], $data);
                }
            }
        }
    }

//============================================================================================

    public function readNotifications()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params          = json_decode(file_get_contents('php://input'), true);
                    $patientId       = $params['patientId'];
                    $notification_id = $params['notificationId'];
                    $data = array('notification_id' => $notification_id,
                        'receiver_id'                   => $patientId,
                        'is_active'                     => 'no',
                        'date'                          => date("Y-m-d H:i:s"),
                    );
                    $this->webservice_model->updateReadNotification($data);
                    $array = array('status' => 'success', 'error' => '', 'message' => 'Data Updated Successfully');
                    json_output(200, $array);
                }
            }
        }
    }

//=========================  Specialist Details ====================================================

    public function getSpecialist()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params             = json_decode(file_get_contents('php://input'), true);
                    $specialist         = $this->webservice_model->getSpecialist();
                    $data['specialist'] = $specialist;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getSpecialistfront()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $params             = json_decode(file_get_contents('php://input'), true);
                $specialist         = $this->webservice_model->getSpecialist();
                $data['specialist'] = $specialist;
                json_output($response['status'] = 200, $data);

            }
        }
    }
//=========================  Doctor Details ========================================================

    public function getDoctor()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params          = json_decode(file_get_contents('php://input'), true);
                    $specialist      = $params['specialistID'];
                    $doctors         = $this->webservice_model->getStaff($specialist, 3);
                    $data['doctors'] = $doctors;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getDoctorfront()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $params          = json_decode(file_get_contents('php://input'), true);
                $specialist      = $params['specialistID'];
                $doctors         = $this->webservice_model->getDoctorfront($specialist, 1);
                $data['doctors'] = $doctors;
                json_output($response['status'] = 200, $data);

            }
        }
    }
	
//===================================== Forgot Password ============================================

    public function forgotpassword()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $_POST = json_decode(file_get_contents("php://input"), true);
            $this->form_validation->set_error_delimiters('', '');
            $this->form_validation->set_data($_POST);
            $this->form_validation->set_rules('site_url', 'URL', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required');
            $this->form_validation->set_rules('usertype', 'User Type', 'trim|required');
            if ($this->form_validation->run() == false) {
                $errors = validation_errors();
            }

            if (isset($errors)) {
                $respStatus = 400;
                $errors     = array(
                    'email'    => form_error('email'),
                    'usertype' => form_error('usertype'),
                    'site_url' => form_error('site_url'),
                );
                $resp = array('status' => 400, 'message' => $errors);
            } else {
                $email    = $this->input->post('email');
                $usertype = $this->input->post('usertype');
                $site_url = $this->input->post('site_url');

                $result = $this->user_model->forgotPassword($usertype, $email);

                if ($result && $result->email != "") {
                    $template = $this->setting_model->getTemplate('forgot_password');
                    if (!empty($template) && $template->is_mail && $template->template != "") {
                        $verification_code = $this->enc_lib->encrypt(uniqid(mt_rand()));
                        $update_record     = array('id' => $result->user_tbl_id, 'verification_code' => $verification_code);
                        $this->user_model->updateVerCode($update_record);
                        if ($usertype == "patient") {
                            $name = $result->patient_name;
                        }
                        $resetPassLink = $site_url . 'user/resetpassword' . '/' . $usertype . "/" . $verification_code;
                        $body          = $this->forgotPasswordBody($name, $resetPassLink, $template->template);
                        $body_array    = json_decode($body);
                        if (!empty($this->mail_config)) {

                            $result = $this->mailer->send_mail($email, $body_array->subject, $body_array->body);
                            if ($result) {
                                $respStatus = 200;
                                $resp       = array('status' => 200, 'message' => "Please check your email to recover your password");
                            } else {
                                $respStatus = 200;
                                $resp       = array('status' => 200, 'message' => "Sending of message failed, Please contact to Admin.");
                            }
                        }
                    } else {
                        $respStatus = 200;
                        $resp       = array('status' => 200, 'message' => "Sending of message failed, Please contact to Admin.");
                    }

                } else {
                    $respStatus = 401;
                    $resp       = array('status' => 401, 'message' => "Invalid Email or User Type");
                }
            }
            json_output($respStatus, $resp);
        }
    }

    public function forgotPasswordBody($name, $resetPassLink, $template)
    {
        $sender_details['resetpasslink'] = $resetPassLink;
        $sender_details['display_name']  = $name;

        foreach ($sender_details as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        //===============
        $subject = "Password Update Request";
        $body    = $template;
        //======================
        return json_encode(array('subject' => $subject, 'body' => $body));
    }

//=========================  Events And Task Details ================================================

    public function calendarevent()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $date_list                   = array();
                    $params                      = json_decode(file_get_contents('php://input'), true);
                    $patient_id                  = $params['patient_id'];
                    $user_id                    = $params['user_id'];
                    $date_from                   = $params['date_from'];
                    $date_to                     = $params['date_to'];
                    $patient_login               = $this->webservice_model->getUserLoginDetails($patient_id);
                    $user_role_id                = $patient_login['id'];
                    $resp                        = array();
					
                    $resp['appointment']         = $appointmentcount         = $this->webservice_model->getAppointmentbydate($patient_id, $date_from, $date_to);
                    $resp['appointmentcount']    = count($appointmentcount);					
					
					$resp['holiday']  = $holidaycount         = $this->holiday_model->getHolidaybyDate($date_from, $date_to);				 
                    $resp['holidaycount']    = count($holidaycount);					
					
                    $incomplete_task             = $this->calendar_model->todaysTaskCount($user_id);          
                    $resp['incomplete_task']     = count($incomplete_task);
                    $getNotificationsThisMonth   = $this->calendar_model->getNotificationsThisMonth($patient_id);                  
                    $resp['notifications_count'] = sizeof($getNotificationsThisMonth);
                    $resp['public_events']       = $this->calendar_model->getPublicEvents($user_role_id, $date_from, $date_to);

                    foreach ($resp['public_events'] as $ev_tsk_value) {
                        $evt_array = array();
                        $holiday_date_list = array();
                        if ($ev_tsk_value->event_type == "public") {
                            $start = strtotime($ev_tsk_value->start_date);
                            $end   = strtotime($ev_tsk_value->end_date);

                            for ($st = $start; $st <= $end; $st += 86400) {
                                if ($st >= strtotime($date_from) && $st <= strtotime($date_to)) {
                                    $date_list[date('Y-m-d', $st)] = date('Y-m-d', $st);
                                    $evt_array[]                   = date('Y-m-d', $st);
                                }
                            }

                            $ev_tsk_value->events_lists = implode(",", $evt_array);
                        } elseif ($ev_tsk_value->event_type == "task") {

                            $date_list[date('Y-m-d', strtotime($ev_tsk_value->start_date))] = date('Y-m-d', strtotime($ev_tsk_value->start_date));
                            $evt_array[]                                                    = date('Y-m-d', strtotime($ev_tsk_value->start_date));
                            $ev_tsk_value->events_lists                                     = implode(",", $evt_array);

                        }
                    }
					
					$resp['date_lists'] = implode(",", $date_list);
					 
                    foreach ($resp['appointment'] as $appointment_value) {
                        $appointment_date_list[date('Y-m-d', strtotime($appointment_value['date']))] = date('Y-m-d', strtotime($appointment_value['date']));
                    }
					
					foreach ($resp['holiday'] as $holiday_value) {
						
							$from_date = strtotime($holiday_value['from_date']);
							$to_date   = strtotime($holiday_value['to_date']);			
							for ($std = $from_date; $std <= $to_date; $std += 86400) {
								if ($std >= ($from_date) && $std <= ($to_date)) {                                 
									$holiday_date_list[] =  date('Y-m-d', $std);					
								}
							}                        
                    }
                   
                    if (!empty($appointment_date_list)) {
                        $resp['appointment_date_list'] = implode(",", $appointment_date_list);
                    } else {
                        $resp['appointment_date_list'] = "";
                    }
					
					if (!empty($holiday_date_list)) {
                        $resp['holiday_date_list'] = implode(",", $holiday_date_list);
                    } else {
                        $resp['holiday_date_list'] = "";
                    }

                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getTask()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params    = json_decode(file_get_contents('php://input'), true);
                    $patientId = $params['patientId'];
                    $result    = $this->webservice_model->getUser($patientId);
                    if (!empty($result)) {
                        $tasklist         = $this->webservice_model->getTaskEvent($result['id'], 0);
                        $data["tasklist"] = $tasklist;
                    }

                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getTaskEdit()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params           = json_decode(file_get_contents('php://input'), true);
                    $eventId          = $params['eventId'];
                    $tasklist         = $this->webservice_model->getTaskbyId($eventId);
                    $data["tasklist"] = $tasklist;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function addTask()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params      = json_decode(file_get_contents('php://input'), true);
                    $patientId   = $params['patientId'];
                    $taskdate    = $params['task_date']; //======== YYYY/MM/DD
                    $tasktitle   = $params['task_title'];
                    $event_type  = 'task';
                    $event_color = '#000';

                    $this->form_validation->set_data($params);
                    $this->form_validation->set_error_delimiters('', '');
                    $this->form_validation->set_rules('task_date', 'Date', 'required');
                    $this->form_validation->set_rules('task_title', 'Task title', 'required');
                    $eventdata = array();
                    if ($this->form_validation->run() == false) {
                        $msg = array(
                            'task_date'  => form_error('task_date'),
                            'task_title' => form_error('task_title'),
                        );
                        $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
                    } else {

                        $result    = $this->webservice_model->getUser($patientId);
                        $eventdata = array('event_title' => $tasktitle,
                            'start_date'                     => $taskdate,
                            'end_date'                       => $taskdate,
                            'event_type'                     => $event_type,
                            'event_color'                    => $event_color,
                            'event_for'                      => $result['id'],
                        );

                        $this->webservice_model->addTask($eventdata);

                        $array = array('status' => 'success', 'error' => '', 'message' => 'Data Inserted Successfully');
                    }
                    json_output(200, $array);
                }
            }
        }
    }

    public function updateTask()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params    = json_decode(file_get_contents('php://input'), true);
                    $eventId   = $params['eventId'];
                    $taskdate  = $params['task_date']; //======== YYYY/MM/DD
                    $tasktitle = $params['task_title'];
                    $this->form_validation->set_data($params);
                    $this->form_validation->set_error_delimiters('', '');
                    $this->form_validation->set_rules('task_date', 'Date', 'required');
                    $this->form_validation->set_rules('task_title', 'Task title', 'required');
                    if ($this->form_validation->run() == false) {
                        $msg = array(
                            'task_date'  => form_error('task_date'),
                            'task_title' => form_error('task_title'),
                        );
                        $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
                    } else {
                        $eventdata = array('event_title' => $tasktitle,
                            'start_date'                     => $taskdate,
                            'end_date'                       => $taskdate,
                            'id'                             => $eventId,
                        );
                        $this->webservice_model->addTask($eventdata);
                        $array = array('status' => 'success', 'error' => '', 'message' => 'Data Updated Successfully');
                    }
                    json_output(200, $array);
                }
            }
        }
    }

    public function checkCompleteTask()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params    = json_decode(file_get_contents('php://input'), true);
                    $eventId   = $params['eventId'];
                    $eventdata = array('is_active' => 'yes', 'id' => $eventId);
                    $this->webservice_model->addTask($eventdata);
                    $array = array('status' => 'success', 'error' => '', 'message' => 'Data check Successfully');
                }
                json_output(200, $array);

            }
        }
    }

    public function uncheckCompleteTask()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params    = json_decode(file_get_contents('php://input'), true);
                    $eventId   = $params['eventId'];
                    $eventdata = array('is_active' => 'no', 'id' => $eventId);
                    $this->webservice_model->addTask($eventdata);
                    $array = array('status' => 'success', 'error' => '', 'message' => 'Data uncheck Successfully');
                }
                json_output(200, $array);
            }
        }
    }

    public function deleteTask()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();

                if ($response['status'] == 200) {
                    $params  = json_decode(file_get_contents('php://input'), true);
                    $eventId = $params['eventId'];
                    if (!empty($eventId)) {
                        $result = $this->webservice_model->deleteTask($eventId);
                    }
                    json_output($response['status'], $result);
                }
            }
        }
    }

//===================================== Module Status =========================================

    public function getModuleStatus()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $resp['module_list'] = $this->module_model->get();
                    json_output($response['status'], $resp);
                }
            }
        }
    }

//===================================== payment ==========================================

    public function patientbillpayment()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $data       = array();
                    $pay_method = $this->paymentsetting_model->getActiveMethod();
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $patientId  = $params['patientId'];
                    $ipdno      = $params['ipdno'];
                    if (!empty($ipdno)) {
                        $resultipdid = $this->webservice_model->getIpdno($ipdno);
                        $ipdid       = $resultipdid['id'];
                    }
                    $paymentDetails          = $this->webservice_model->paymentDetails($patientId, $ipdid);
                    $totalcharges            = $this->webservice_model->getTotalCharges($patientId, $ipdid);
                    $paymentpaid             = $this->webservice_model->getPaidTotal($patientId, $ipdid);
                    $patient_due_fee         = $totalcharges['charge'] - $paymentpaid['paid_amount'];
                    $billdetails             = $this->webservice_model->getIpdDetails($patientId, $ipdid);
                    $data['pay_method']      = empty($pay_method) ? 0 : 1;
                    $data["totalcharges"]    = $totalcharges;
                    $data['totalpaid']       = $paymentpaid;
                    $data['patient_due_fee'] = $patient_due_fee;
                    $data["payment_details"] = $paymentDetails;
                    $data["billdetails"]     = $billdetails;

                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function paymenturl()
    {
        $data['payUrl'] = site_url() . "payment/index/";
        echo json_encode($data);
    }

    public function getcustomfields()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $params                = json_decode(file_get_contents('php://input'), true);
                $belong_to             = $params['belong_to'];
                $custom_fields         = $this->customfield_model->getcustomfields($belong_to);
                $data['custom_fields'] = $custom_fields;
                json_output($response['status'] = 200, $data);

            }
        }
    }

//===================================== Patient IPD ==========================================

    public function patientipddetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $params     = json_decode(file_get_contents('php://input'), true);
                $patient_id = $params['patient_id'];
                $ipd_id = $params['ipd_id'];
                
                    if(empty($ipd_id)){
                        $resultlist    = $this->patient_model->getipdpatientdetails($patient_id);                  
                       if(!empty($resultlist)){
                        $ipd_id        = $resultlist['ipdid'];  
                       } else{
                        $ipd_id        = '';   
                       }                       
                    }        
                
                $result = $this->patient_model->getIpdDetails($ipd_id);       
                $case_reference_id  = $this->patient_model->getReferenceByIpdId($ipd_id);                 
                $data['medication'] = array();
                $medication         = $this->patient_model->getmedicationdetailsbydate($ipd_id); 
                
                foreach ($medication as $medication_key => $medication_value) {     
                    $medicine_array = array();
                    foreach ($medication_value['dose_list'] as $dose_key => $dose_value) {                       
                        
                        $find = null;

                        if (!empty($medicine_array)) {
                            $find = searchForId($dose_value['pharmacy_id'], $medicine_array,'pharmacy_id');
                        }
						if($dose_value['unit']){$unit = $dose_value['unit'];}else{$unit = '';}
                        if (is_null($find)) {
							
							$created_by = $dose_value['staff_name'].' '.$dose_value['staff_surname'].'('.$dose_value['staff_employee_id'].")";							
							
                            $medicine_array[] = array(
                                'pharmacy_id'   => $dose_value['pharmacy_id'],
                                'medicine_name' => $dose_value['medicine_name'],
                                'doses'         => array(array('date' => $dose_value['date'], 'time' => $dose_value['time'], 'remark' => $dose_value['remark'], 'medicine_dosage' => $dose_value['medicine_dosage'], 'unit' => $unit, 'created_by' => $created_by)),
                            );
                        } else {
                            $medicine_array[$find]['doses'][] = array('date' => $dose_value['date'], 'time' => $dose_value['time'], 'remark' => $dose_value['remark'], 'medicine_dosage' => $dose_value['medicine_dosage'], 'unit' => $unit, 'created_by' => $created_by);
                        }
                    }

                    $final_medicines                    = array();
                    $final_medicines['medicine_date']   = $medication_value['date'];
                    $final_medicines['medicine_day']   = date('l',strtotime($medication_value['date']));
                    
                    $final_medicines['medicine']        = $medicine_array;
                    $data['medication'][] = $final_medicines;
                }

                $data['prescription']        = $this->prescription_model->getipdprescription($ipd_id);          
                $data['consultant_register'] = $this->patient_model->getpatientconsultant($ipd_id);         
                
                if (!empty($case_reference_id)) {
                    $data['investigations']      = $this->patient_model->getallinvestigation($case_reference_id);
                    $data['bed_history']         = $this->bed_model->getBedHistory($case_reference_id);
                } else {
                    $data['investigations']      = [];
                    $data['bed_history']         = [];
                }               
                
                if(!empty($ipd_id)){
                    $data['operation_theatre']   	= $this->operationtheatre_model->getipdoperationdetails($ipd_id);
                    $data['charges']             	= $this->charge_model->getCharges($ipd_id);
                    $data['payments']            	= $this->transaction_model->ipdpatientpayments($ipd_id);
                    $data['live_consultation']   	= $this->conference_model->getconfrencebyipd($ipd_id);
                    $data['nurse_note']          	= $this->patient_model->getdatanursenote($ipd_id);           
                    $data['time_line']           	= $this->timeline_model->getPatientTimeline($patient_id);
                    $data['treatment_history']   	= $this->patient_model->getipdtreatmenthistory($patient_id); 
					$data['obstetric_history'] 		= $this->antenatal_model->getobstetrichistory($patient_id);
					$data['postnatal_history'] 		= $this->antenatal_model->getpostnatal($patient_id);
					$data['antenatallist'] 			= $this->antenatal_model->getantenatallist($patient_id);
                }else{
                   $data['operation_theatre']   	=   array();
                   $data['charges']             	=   array();
                   $data['payments']            	=   array();
                   $data['live_consultation']   	=   array();
                   $data['nurse_note']          	=   array();
                   $data['time_line']           	=   array();
                   $data['treatment_history']   	=   array();
				   $data['obstetric_history'] 		= 	array();
				   $data['postnatal_history'] 		= 	array();
				   $data['antenatallist'] 			= 	array();
                } 
                
                 $data['doctors_ipd']   =    $this->patient_model->getDoctorsipd($ipd_id);
                 if(!empty($result)){
                    $data['result']        = $result;
                }else{
                    $data['result']        = array();
                }
                
                $data['credit_limit'] = 0;
                $data['used_credit_limit'] = 0;
                $data['balance_credit_limit'] = 0;
                $data['graph']               =   array();
                $data['donut_graph_percentage']  = 0;                       
                $data['balance_credit_limit']    = 0;
                        
                if (!empty($case_reference_id)) {
                 $data['graph']               = $this->transaction_model->ipd_bill_paymentbycase_id($case_reference_id);
                 if ($data['result']['ipdcredit_limit'] > 0) {
                    $data['credit_limit']    = $data['result']['ipdcredit_limit'];
                    if($data['graph']['my_balance']>=$data['credit_limit']){                         
                        $data['used_credit_limit']       = $data['credit_limit'];
                    }else{                      
                        $credit_limit_percentage = (($data['graph']['my_balance'] / $data['credit_limit'])*100);
                        $data['donut_graph_percentage']  = number_format(((100-$credit_limit_percentage)), 2);
                        $data['balance_credit_limit']    = ($data['credit_limit'] - $data['graph']['my_balance']);
                        $data['used_credit_limit']       = $data['graph']['my_balance'];
                    }
                 }  
                } 
					
				$data['vital_list'] 		= 	$vital_list = $this->vital_model->getvitallist();					 
                
				foreach($vital_list as $vital_list_value){
					
					$vital_id = $vital_list_value['id'];
					$name = $vital_list_value['name'];
						
					$vital_messure_date = $this->vital_model->getpatientvitaldate($patient_id,$vital_id);
					$datewisevital =array();
					
					foreach($vital_messure_date as $key => $vital_messure_date){
						$vital_date = $this->vital_model->getpatientsvitaldetails($patient_id,$vital_id,$vital_messure_date['messure_date']);								
						$datewisevital[] = $vital_date;								
					}
							
					$patient_vital_list[][$name] = $datewisevital;
				}			
			 
				if(!empty($patient_vital_list)){
					$data['patient_vital_list']	=	$patient_vital_list;
				}else{
					$data['patient_vital_list']	=	'';
				}			
			
                json_output($response['status'] = 200, $data);
            }
        }
    }

    public function getipdprescription()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();

                if ($response['status'] == 200) {
                    $params          = json_decode(file_get_contents('php://input'), true);
                    $prescription_no = $params['prescription_no'];
                    $result          = $this->webservice_model->getipdprescriptionbyid($prescription_no);
					 
                    $data['result']  = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getipdoperationdetail()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params         = json_decode(file_get_contents('php://input'), true);
                    $operation_id   = $params['operation_id'];
                    $result         = $this->operationtheatre_model->getipdoperationdetail($operation_id);
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }
    
    public function patientipdlist()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST'){
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        }else{
            $check_auth_client = $this->auth_model->check_auth_client();
            if($check_auth_client == true){
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params =   json_decode(file_get_contents('php://input'), true);
                    $patient_id = $params['patient_id'];
                    $result         = $this->patient_model->patientipdlist($patient_id);
                    $i              = 0;
                
                    foreach ($result as $key => $value) {
                        $charges = $this->patient_model->getCharges($value["id"]);
                        if(!empty($charges['charge'])){
                            $result[$i]["charges"] = $charges['charge'];
                        }else{
                            $result[$i]["charges"] = '';
                        }
                        
                        $payment = $this->patient_model->getPayment($value["id"]);
                        if(!empty($payment['payment'])){
                            $result[$i]["payment"] = $payment['payment'];
                        }else{
                            $result[$i]["payment"] = '';
                        }
                        $i++;
                    }
                    
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }                
            }
        }
    }
    
    public function getinvestigationparameter()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST'){
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        }else{
            $check_auth_client = $this->auth_model->check_auth_client();
            if($check_auth_client == true){
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params =   json_decode(file_get_contents('php://input'), true);
                    
                    $record_id = $params['record_id'];
                    $type = $params['type'];
                    
                    if ($type == 'pathology') {
                        $result         = $this->pathology_model->getPatientPathologyReportDetails($record_id);
                    }else{
                        $result         = $this->radiology_model->getPatientRadiologyReportDetails($record_id);
                    }     
                    
                    $data['result'] = $result;
                    json_output($response['status'], $data);
                }                
            }
        }
    }

    public function deletesystemnotifications()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_decode(400, array('status' => 400, 'message' => 'Bad request'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params        = json_decode(file_get_contents('php://input'), true);
                    $patient_id = $params['patient_id'];
                    
                    $result = $this->webservice_model->deletesystemnotifications($patient_id);
                    
                    json_output($response['status'], $result);
                }
            }
        }
    } 
	
	public function getsharecontentlist(){
		$method = $this->input->server('REQUEST_METHOD');
		if ($method != 'POST'){
			json_output(400, array('status' => 400, 'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->auth_model->check_auth_client();
			if ($check_auth_client == true){
				$response = $this->auth_model->auth();
				if ($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'),true);
					$patient_id = $params['patient_id'];
					$contentlist = array();
					$contentlist = $this->sharecontent_model->getPatientShareList($patient_id);					 
					foreach($contentlist as $key => $value){						 
						$content_result = $this->sharecontent_model->getShareContentDocumentsByID($value->id);
						if(!empty($content_result)){
							$contentlist[$key]->content = $content_result;
						}else{
							$contentlist[$key]->content = array();
						}
					}
					$data['contentlist'] = $contentlist;
					json_output($response['status'], $data);
					
				}			
			}
		}
	}
	
	public function getPatientVitalByPatientAndVitalid()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $params                	= json_decode(file_get_contents('php://input'), true);
                $patient_id             = $params['patient_id'];
                $vital_id             	= $params['vital_id'];
                $patient_vital         	= $this->vital_model->getpatientvitaldate($patient_id,$vital_id);
                $data['patient_vital'] 	= $patient_vital;
                json_output($response['status'] = 200, $data);

            }
        }
    }
	
	public function getPatientCurrentVital()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $params                	= json_decode(file_get_contents('php://input'), true);
                $patient_id             = $params['patient_id'];
                $patient_vital         	= $this->vital_model->getcurrentvitals($patient_id);
                $data['patient_vital'] 	= $patient_vital;
                json_output($response['status'] = 200, $data);

            }
        }
    }

    
}
