<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Antenatal extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
		$this->load->library('Customlib');
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->load->model('prefix_model');
        $this->opd_prefix = $this->prefix_model->getByCategory(array('opd_no'))[0]->prefix;
        $this->load->model('finding_model');
        $this->load->model('antenatal_model');
        $this->load->helper('customfield_helper');
        $this->opd_prefix          = $this->customlib->getSessionPrefixByType('opd_no');
        $this->time_format         = $this->customlib->getHospitalTimeFormat();
        $this->load->library('system_notification');  
    }

    public function addobstetric()
    {
        $data['patient_id']       = $this->input->post('patient_id');
        $data["genderlist"]       = $this->customlib->getGender_Patient();
        $page                     = $this->load->view('admin/patient/_addobstretichistory', $data, true); 
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function add_obstetric()
    {     
        $this->form_validation->set_rules('place_of_delivery',$this->lang->line('place_of_delivery'),'required');

     if($this->form_validation->run()==false){
         
        $error = array('place_of_delivery' => form_error('place_of_delivery'));
        $array = array('status'=>0,'message'=>'', 'error'=>$error);

     }else{
         
        $patient_id = $this->input->post('patient_id');
        $medicine=0;
        $insert_obstetric         = array();
        $update_obstetric         = array();
        $obstetric_id = $this->input->post("obstetric_id");
        if (isset($obstetric_id)) {
         
            $update_obstetric        = array(
                'id'                        => $obstetric_id,
                'patient_id'                => $patient_id,
                'place_of_delivery'         => $this->input->post("place_of_delivery"),
                'pregnancy_duration'        => $this->input->post("pragnancy_duration"),
                'pregnancy_complications'   => $this->input->post("pragnancy_complications"),
                'birth_weight'              => $this->input->post("birth_weight"),
                'gender'                    => $this->input->post("gender"),
                'infant_feeding'            => $this->input->post("feeding"),
                'alive_dead'                => $this->input->post("alive_or_dead"),               
                'previous_medical_history'  => $this->input->post("previous_history"),
                'special_instruction'       => $this->input->post("special_instruction"),
                'date' => $this->customlib->dateFormatToYYYYMMDD($this->input->post("date")),
            );

            if($this->input->post("alive_or_dead")=='dead'){                
                $update_obstetric['death_cause'] = $this->input->post("cause");
            }else{               
                $update_obstetric['death_cause'] = "";
            }

        } else {
            $insert_obstetric = array(               
                'patient_id'                => $patient_id,
                'place_of_delivery'         => $this->input->post("place_of_delivery"),
                'pregnancy_duration'        => $this->input->post("pragnancy_duration"),
                'pregnancy_complications'   => $this->input->post("pragnancy_complications"),
                'birth_weight'              => $this->input->post("birth_weight"),
                'gender'                    => $this->input->post("gender"),
                'infant_feeding'            => $this->input->post("feeding"),
                'alive_dead'                => $this->input->post("alive_or_dead"),
                'previous_medical_history'  => $this->input->post("previous_history"),
                'special_instruction'       => $this->input->post("special_instruction"),
                'date' => $this->customlib->dateFormatToYYYYMMDD($this->input->post("date")),
            );

            if($this->input->post("alive_or_dead")=='dead'){                
                $insert_obstetric['death_cause'] = $this->input->post("cause");
            }else{                
                $insert_obstetric['death_cause'] = "";
            }
        } 

        $this->antenatal_model->add_obstetrichistory($insert_obstetric, $update_obstetric);
        if (isset($obstetric_id)==false) {
            //send system notification for obstetric
            $ipd_id=$this->input->post('obstetric_ipdid');
            $patient_detail    = $this->patient_model->get_patientidbyIpdId($ipd_id);            
            $date=date('Y-m-d_H-i');
            $event_data = array(
                'patient_id'            => $patient_id,               
                'date'                  => $this->customlib->YYYYMMDDHisTodateFormat($date, $this->customlib->getHospitalTimeFormat()),
                'ipd_no'                => $this->customlib->getSessionPrefixByType('ipd_no') . $ipd_id,
                'case_id'               => $patient_detail['case_reference_id'],
                'doctor_id'             => $patient_detail['cons_doctor'],
                'doctor_name'           => composeStaffNameByString($patient_detail['doctor_name'], $patient_detail['doctor_surname'], $patient_detail['doctor_employee_id']),
            );
            $this->system_notification->send_system_notification('add_ipd_previous_obstetric_history', $event_data);            
            //send system notification for obstetric
        }       

        $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'), 'visitid' => '');
     }        
     
       echo json_encode($array);
    }

    public function editobstetrichistory()
    {
        $id = $this->input->post('id');
        $data["genderlist"]       = $this->customlib->getGender_Patient();
        $data['obstetric_history_value'] = $this->antenatal_model->getobstetrichistorybyid($id);
        $page = $this->load->view('admin/patient/_editobstetrichistory', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function addantenatalprescription()
    { 
        $data = array();

        $data['visit_detail_id']       = $_REQUEST['visit_detail_id'];
        $data['ipdid']                 = $_REQUEST['ipdid'];
        $data['patient_id']            = $_REQUEST['patient_id'];

        $page                     = $this->load->view('admin/patient/_addantenatalfinding', $data, true); 
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function add_antenatalprescription(){ 

        $this->form_validation->set_rules('antenatal_date',$this->lang->line('date'), 'required');
        $custom_fields = $this->customfield_model->getByBelong('antenatal');
        $edit_antenatal_id = $this->input->post('edit_antenatal_id');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[antenatal][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

       if($this->form_validation->run()== false){ 

        $error = array(
            'antenatal_date' => form_error('antenatal_date'),
            );            
        
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[antenatal][" . $custom_fields_id . "]"] = form_error("custom_fields[antenatal][" . $custom_fields_id . "]");
                    }
                }
            }

            if (!empty($error_msg2)) {
                $error_msg = array_merge($error, $error_msg2);
            } else {
                $error_msg = $error;
            }           
            
        $json_array = array('status'=>0,'message'=>'','error'=>$error_msg);

       }else{

            $antenatal_id = $this->input->post('antenatal_id');
            $anteexam_id = $this->input->post('anteexam_id');
                $data = array(
                    'id'               => $antenatal_id,
                    'ipdid'            => $this->input->post('ipdid'),
                    'visit_details_id' => $this->input->post('visit_detail_id'),
                    'bleeding'         => $this->input->post("bleeding"),
                    'headache'         => $this->input->post("headache"),
                    'pain'             => $this->input->post("pain"),
                    'constipation'     => $this->input->post("constipation"),
                    'urinary_symptoms' => $this->input->post("urinary_symptoms"),
                    'vomiting'         => $this->input->post("vomiting"),
                    'cough'            => $this->input->post("cough"),
                    'vaginal'          => $this->input->post("vaginal"),
                    'oedema'           => $this->input->post("oedema"),
                    'discharge'        => $this->input->post("discharge"),
                    'haemoroids'       => $this->input->post("haemoroids"),
                    'weight'           => $this->input->post("weight"),
                    'height'           => $this->input->post("height"),
                    'general_condition'   => $this->input->post("condition"),
                    'finding_remark'      => $this->input->post("special_finding_remarks"),
                    'pelvic_examination'  => $this->input->post("pelvic_examination"),
                    'sp'                  => $this->input->post("sp"),
                );
                
            if($this->input->post("antenatal_date")!=""){
                $data['date'] = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post("antenatal_date"), $this->time_format);               
            }

            $primary_examine_id = $this->antenatal_model->addantenatalprescription($data);

            $antenatal_data = array(
                'id'                  => $anteexam_id ,
                'primary_examine_id'  => $primary_examine_id,
                'visit_details_id'    => $this->input->post('visit_detail_id'),
                 'ipdid'              => $this->input->post('ipdid'),
                'uter_size'           => $this->input->post("uter_size"),
                'uterus_size'         => $this->input->post("uterus_size"),
                'presentation_position' => $this->input->post("presentation_position"),
                'brim_presentation'   => $this->input->post("presentation_brim"),
                'foeta_heart'         => $this->input->post("foeta_heart"),
                'blood_pressure'      => $this->input->post("blood_pressure"),
                'antenatal_Oedema'    => $this->input->post("antenatal_oedema"),
                'antenatal_weight'    => $this->input->post("antenatal_weight"),
                'urine_sugar'         => $this->input->post("urine"),
                'urine'               => $this->input->post("urine_aaibumen"),
                'remark'              => $this->input->post("remark"),
                'next_visit'          => $this->input->post("next_visit"),
            );       

            $examine_id = $this->antenatal_model->addantenatalexamine($antenatal_data);

            $custom_field_post  = $this->input->post("custom_fields[antenatal]");
            $custom_value_array = array();

            if (!empty($custom_field_post) ) {
                
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[antenatal][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
					
					if($edit_antenatal_id>0 ){						 
						$array_custom['belong_table_id'] = $edit_antenatal_id;
					}else{
						$array_custom['belong_table_id'] = 0; 
					}
					
                    $array_custom     = array(                        
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
       
            if (!empty($custom_value_array) && $edit_antenatal_id==0) {
                $this->customfield_model->insertRecord($custom_value_array, $examine_id);
            }
            
            if($edit_antenatal_id>0 ){
                if (!empty($custom_value_array)) {                  
                    $this->customfield_model->updateRecord($custom_value_array, $edit_antenatal_id, '');                  
                }
            }

            if($edit_antenatal_id=="" || $edit_antenatal_id==0){
                //send system notification for antenatal
                $date=date('Y-m-d');
                $opdid=$this->input->post('id');                
                $event_data = array(
                    'patient_id'  => $this->input->post('patient_id'),   
                    'opd_no'      => $this->customlib->getSessionPrefixByType('opd_no') . $opdid,
                    'case_id'     => $this->input->post('case_reference_id'),
                    'date'        => $this->customlib->YYYYMMDDTodateFormat($date, $this->customlib->getHospitalTimeFormat()),
                );

                $this->system_notification->send_system_notification('add_opd_antenatal', $event_data);            
                //send system notification for antenatal
            }     

            $json_array = array('status'=>1,'message'=>$this->lang->line('success_message'),'error'=>'');

		}
        echo json_encode($json_array) ;

    }

    public function getantenatalprescription($visitid, $module)
    {		 
        $result                = $this->antenatal_model->getprescription($visitid);
        $data["result"]        = $result;
        $data["id"]            = $visitid;
		
		if($module == 'opd'){
			$data["print_details"] = $this->printing_model->get('', 'opd_antenatal_finding');
		}elseif($module == 'ipd'){
			$data["print_details"] = $this->printing_model->get('', 'ipd_antenatal_finding');
		}
		
        $data['fields_prescription']   =  $this->customfield_model->get_custom_fields('antenatal', '',''); 
        $data['module']   =  $module;
       
        $this->load->view("admin/patient/antenatalfinding", $data);
    }

    public function getipdantenatalfindings($antenatal_id)
    {
        $result                = $this->antenatal_model->getipdprescription($antenatal_id);
        $data["result"]        = $result;
        $data["id"]            = $antenatal_id;
        $this->load->view("admin/patient/ipdantenatalfinding", $data);
    }

    public function printantenatalprescription()
    {
        $visitid                 = $this->input->get('visitid');
        $result                  = $this->antenatal_model->getprescription($visitid);
        $data["result"]          = $result;
        $data["print_details"]   = $this->printing_model->get('', 'opd_antenatal_finding');
        $data['fields_prescription']   =  $this->customfield_model->get_custom_fields('antenatal', '','');       
        $data["id"]              = $visitid;
        $data["opd_id"]          = $result->opd_details_id;
       
        $page           = $this->load->view('admin/patient/_printantenatalfinding', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function editantenatalprescription()
    {        
        $antenatal_id = $this->input->post('antenatal_id');
        $result                  = $this->antenatal_model->getprescriptionbyid($antenatal_id);		
        $data['fields_prescription']   =  $this->customfield_model->get_custom_fields('antenatal', '','');         
        $data["result"]          = $result;        
        $data['time_format']        = $this->customlib->getHospitalTimeFormat();       
        $page = $this->load->view('admin/patient/_editantenatalfinding', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getobstetrichistory()
    {
        $id = $this->input->post('id');
        $data['result'] = $this->antenatal_model->getobstetrichistorybyid($id);
        $page = $this->load->view('admin/patient/_viewobstetrichistory', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printobstetrichistory()
    {
        $id = $this->input->post('id');
        $data['result'] = $this->antenatal_model->getobstetrichistorybyid($id);       
        $data['print_details']   = $this->printing_model->get('', 'obstetric_history');
        $page           = $this->load->view('admin/patient/_printobstetrichistory', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function deleteobstetrichistory($id)
    {
        $this->antenatal_model->deleteobstetrichistory($id);
        $json = array('status' => 'success', 'error' => '', 'msg' => $this->lang->line('delete_message'));
        echo json_encode($json);
    }

    public function add_postnatal()
    {
        $this->form_validation->set_rules('labor_time', $this->lang->line('labor_time'),'required');
        $this->form_validation->set_rules('delivery_time', $this->lang->line('delivery_time'),'required');

        if($this->form_validation->run() == false ){
            $error = array(
                'labor_time' => form_error('labor_time'),
                'delivery_time' => form_error('delivery_time'));
             $array = array('status' => 0, 'error' => $error, 'message' => '', 'visitid' => '');
        }else{
            $patient_id = $this->input->post('patient_id');
     
            $data        = array(               
                'patient_id'            => $patient_id,
                'labor_time'            => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post("labor_time"), $this->time_format),               
                'delivery_time'         => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post("delivery_time"), $this->time_format),                
                'routine_question'      => $this->input->post("routine_question"),
                'general_remark'        => $this->input->post("general_remark"),                
            );
            if($this->input->post('postnatal_id')!=""){
                $data['id'] = $this->input->post('postnatal_id');
            }
          
            $this->antenatal_model->addpostnatal($data);

            $postnatal_id=$this->input->post('postnatal_id');
            if($postnatal_id==""){
                 //send system notification for postnatal
                $ipd_id=$this->input->post('postnatal_ipd_id');
                $patient_detail    = $this->patient_model->get_patientidbyIpdId($ipd_id);            
                $date=date('Y-m-d');
                $event_data = array(
                    'patient_id'            => $patient_id,
                    'delivery_time'         => $this->input->post("delivery_time"), 
                    'labor_time'            => $this->input->post("labor_time"), 
                    'date'                  => $this->customlib->YYYYMMDDHisTodateFormat($date, $this->customlib->getHospitalTimeFormat()),
                    'ipd_no'                => $this->customlib->getSessionPrefixByType('ipd_no') . $ipd_id,
                    'case_id'               => $patient_detail['case_reference_id'],
                    'doctor_id'             => $patient_detail['cons_doctor'],
                    'doctor_name'           => composeStaffNameByString($patient_detail['doctor_name'], $patient_detail['doctor_surname'], $patient_detail['doctor_employee_id']),
                );
                $this->system_notification->send_system_notification('add_ipd_postnatal_history', $event_data);            
                //send system notification for postnatal
            }           

           $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'), 'visitid' => '');
        }       
       
       echo json_encode($array);
    }
    
    public function getpostnatal($id){
        $result = $this->antenatal_model->getpostnatalbyid($id);
        $result['labour_time'] = $this->customlib->YYYYMMDDHisTodateFormat($result['labor_time'], $this->time_format);
        $result['delivery_time'] = $this->customlib->YYYYMMDDHisTodateFormat($result['delivery_time'], $this->time_format);
       
        $data = array('status'=>1,'data'=>$result);
       echo json_encode($data);
    }

    public function deletepostnatal($id)
    {
        $this->antenatal_model->deletepostnatal($id);
        $json = array('status' => 'success', 'error' => '', 'msg' => $this->lang->line('delete_message'));
        echo json_encode($json);
    }

    public function deleteantenatal($visit_id)
    {
        $this->antenatal_model->deleteantenatal($visit_id);
        $json = array('status' => 'success', 'error' => '', 'msg' => $this->lang->line('delete_message'));
        echo json_encode($json);
    }

    public function deleteipdantenatal($id)
    {
        $this->antenatal_model->deleteipdantenatal($id);
        $json = array('status' => 'success', 'error' => '', 'msg' => $this->lang->line('delete_message'));
        echo json_encode($json);
    }

    public function editipdantenatalprescription()
    {        
        $antenatal_id = $this->input->post('antenatal_id');
        $result       = $this->antenatal_model->getipdprescription($antenatal_id);        
        $result->antenatal_date             = $this->customlib->YYYYMMDDHisTodateFormat($result->antenatal_date, $this->time_format);
		
        $data["result"]          = $result;
        $page = $this->load->view('admin/patient/_editipdantenatalfinding', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function addipdantenatalfinding()
    { 
        $data = array();
        $data['visit_detail_id']    = $_REQUEST['visit_detail_id'];
        $data['ipdid']              = $_REQUEST['ipdid'];
        $page                       = $this->load->view('admin/patient/_addipdantenatalfinding', $data, true); 
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function add_ipdantenatalprescription(){ 
		 
        $this->form_validation->set_rules('antenatal_date',$this->lang->line('date'), 'required' );        
        $custom_fields = $this->customfield_model->getByBelong('antenatal');
        
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[antenatal][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        
		if($this->form_validation->run()== false){

            $error = array(
                'antenatal_date' => form_error('antenatal_date'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[antenatal][" . $custom_fields_id . "]"] = form_error("custom_fields[antenatal][" . $custom_fields_id . "]");
                    }
                }
            }

            if (!empty($error_msg2)) {
                $error_msg = array_merge($error, $error_msg2);
            } else {
                $error_msg = $error;
            } 
            
            $json_array = array('status'=>0,'message'=>'','error'=>$error_msg);

       }else{

            $antenatal_id = $this->input->post('antenatal_id');
            $anteexam_id = $this->input->post('anteexam_id');
			
                $data = array(
                    'id'               => $antenatal_id,
                    'ipdid'            => $this->input->post('ipdid'),
                    'visit_details_id' => null,
                    'bleeding'         => $this->input->post("bleeding"),
                    'headache'         => $this->input->post("headache"),
                    'pain'             => $this->input->post("pain"),
                    'constipation'     => $this->input->post("constipation"),
                    'urinary_symptoms' => $this->input->post("urinary_symptoms"),
                    'vomiting'         => $this->input->post("vomiting"),
                    'cough'            => $this->input->post("cough"),
                    'vaginal'          => $this->input->post("vaginal"),
                    'oedema'           => $this->input->post("Oedema"),
                    'discharge'        => $this->input->post("discharge"),
                    'haemoroids'       => $this->input->post("haemoroids"),
                    'weight'           => $this->input->post("weight"),
                    'height'           => $this->input->post("height" ),
                    'general_condition'   => $this->input->post("condition"),
                    'finding_remark'      => $this->input->post("special_finding_remarks"),
                    'pelvic_examination'  => $this->input->post("pelvic_examination"),
                    'sp'                  => $this->input->post("sp"),
                );
				
				$antenatal_date = $this->input->post('antenatal_date');
                if($antenatal_date !=""){			
					$data['date'] = $this->customlib->dateFormatToYYYYMMDDHis($antenatal_date, $this->time_format);				
				}
 
            $primary_examine_id = $this->antenatal_model->addipdantenatalprescription($data);          

            $antenatal_data = array(
                'id'                  => $anteexam_id ,
                'primary_examine_id' =>  $primary_examine_id,
                'visit_details_id'    => $this->input->post('visit_detail_id'),
                 'ipdid'              => $this->input->post('ipdid'),
                'uter_size'           => $this->input->post("uter_size"),
                'uterus_size'         => $this->input->post("uterus_size"),
                'presentation_position' => $this->input->post("presentation_position"),
                'brim_presentation'   => $this->input->post("presentation_brim"),
                'foeta_heart'         => $this->input->post("foeta_heart"),
                'blood_pressure'      => $this->input->post("blood_pressure"),
                'antenatal_Oedema'    => $this->input->post("antenatal_oedema"),
                'antenatal_weight'    => $this->input->post("antenatal_weight"),
                'urine_sugar'         => $this->input->post("urine"),
                'urine'               => $this->input->post("urine_aaibumen"),
                'remark'              => $this->input->post("remark"),
                'next_visit'          => $this->input->post("next_visit"),
            );             
                   
            $this->antenatal_model->addipdantenatalexamine($antenatal_data);
			
			$custom_field_post  = $this->input->post("custom_fields[antenatal]");
            $custom_value_array = array();

            if (!empty($custom_field_post) ) {
                
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[antenatal][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
					
					if($antenatal_id>0 ){						 
						$array_custom['belong_table_id'] = $antenatal_id;
					}else{
						// $array_custom['belong_table_id'] = 0; 
					}
					
                    $array_custom     = array(                        
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }           
            
            if($antenatal_id>0 ){
                if (!empty($custom_value_array)) {                  
                    $this->customfield_model->updateRecord($custom_value_array, $antenatal_id, 'antenatal');                  
                }
            }else{
				if (!empty($custom_value_array)) { 
					$this->customfield_model->insertRecord($custom_value_array, $primary_examine_id);
				}
			}
			
            if($antenatal_id==""){
                //send system notification for antenatal
                $date=date('Y-m-d');
                $ipd_id=$this->input->post('ipdid');
                $patient_detail    = $this->patient_model->get_patientidbyIpdId($ipd_id);
                $event_data = array(
                    'patient_id'  => $patient_detail['patient_id'],
                    'ipd_no'      => $this->customlib->getSessionPrefixByType('ipd_no') . $ipd_id,
                    'case_id'     => $patient_detail['case_reference_id'],
                    'doctor_id'   => $patient_detail['cons_doctor'],
                    'date'        => $this->customlib->YYYYMMDDTodateFormat($date, $this->customlib->getHospitalTimeFormat()),
                    'doctor_name' => composeStaffNameByString($patient_detail['doctor_name'], $patient_detail['doctor_surname'], $patient_detail['doctor_employee_id']),
                );
                $this->system_notification->send_system_notification('add_ipd_antenatal', $event_data);            
                //send system notification for antenatal
            }           

            $json_array = array('status'=>1,'message'=>$this->lang->line('success_message'),'error'=>'');
       }
        echo json_encode($json_array) ;
    }

   public function printipdantenatalprescription()
    {
        $ipdid                 = $this->input->get('ipdid');
        $data["print_details"]   = $this->printing_model->get('', 'ipd_antenatal_finding');
        $result                  = $this->antenatal_model->getipdprescription($ipdid);
        $data["result"]          = $result;
        $page           = $this->load->view('admin/patient/_printipdantenatalfinding', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }
}
