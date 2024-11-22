<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Zoom_conference extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('mailsmsconf');
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->load->model(array('conference_model', 'conferencehistory_model'));
        $this->conference_setting = $this->setting_model->getzoomsetting();
        $this->load->helper('customfield_helper');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
        $this->config->load("payroll");
        $this->search_type = $this->config->item('search_type');
        $this->opd_ipd     = $this->config->item("opd_ipd");
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('setting', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'conference/zoom_api_setting');
        $data          = array();
        $data['title'] = $this->lang->line('zoom_setting');
        $setting       = $this->setting_model->getzoomsetting();
        if (empty($setting)) {
            $setting                  = new stdClass();
            $setting->zoom_api_key    = "";
            $setting->zoom_api_secret = "";
        }

        $staff    = $this->staff_model->get($this->customlib->getStaffID());
		$role     = json_decode($this->customlib->getStaffRole());		
		
		if ($role->id != 2) {
            $params = array(
                'zoom_api_key'    => "",
                'zoom_api_secret' => "",
            );
        } else {
            
            if ($setting->use_doctor_api) {
                if ($staff['zoom_api_key'] != "" && $staff['zoom_api_secret'] != "") {
                    $api_type = "self";
                }

                $params = array(
                    'zoom_api_key'    => $staff['zoom_api_key'],
                    'zoom_api_secret' => $staff['zoom_api_secret'],
                );
            } else {
                $params = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
            }
        }
		
        $data['title'] = $this->lang->line('email_config_list');
        $data['setting'] = $setting;

        $this->load->library('zoom_api', $params);
        
        $oAuthURL = $this->zoom_api->oAuthUrl();
        $data['oAuthURL'] = $oAuthURL;					  
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
           
                $data_insert = array(
                'id'              => $this->input->post('id'),
                'zoom_api_key'    => $this->input->post('zoom_api_key'),
                'zoom_api_secret' => $this->input->post('zoom_api_secret'),
                'use_doctor_api'  => $this->input->post('use_doctor_api'),
                'use_zoom_app'    => $this->input->post('use_zoom_app'),
                'opd_duration'    => $this->input->post('opd_duration'),
                'ipd_duration'    => $this->input->post('ipd_duration'),
            );
            $this->setting_model->addzoomdetails($data_insert);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/zoom_conference');         
		} 

            $this->load->view('layout/header', $data);
            $this->load->view('admin/conference/index', $data);
            $this->load->view('layout/footer', $data);
    }
   
    public function generatetoken()
    {
        $role     = json_decode($this->customlib->getStaffRole());
        $api_type = 'global';
        $staff    = $this->staff_model->get($this->customlib->getStaffID());
	
		if ($role->id != 2) {
            $params = array(
                'zoom_api_key'    => "",
                'zoom_api_secret' => "",
            );
        } else {
             $zoomsetting       =    $this->setting_model->getzoomsetting();
            if ($zoomsetting->use_doctor_api) {
                if ($staff['zoom_api_key'] != "" && $staff['zoom_api_secret'] != "") {
                    $api_type = "self";
                }

                $params = array(
                    'zoom_api_key'    => $staff['zoom_api_key'],
                    'zoom_api_secret' => $staff['zoom_api_secret'],
                );
            } else {
                $params = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
            }
        }

        $this->load->library('zoom_api', $params);
        $oAuthURL = $this->zoom_api->token($_GET['code']);

        if ($oAuthURL['status']) {
            $this->session->unset_userdata('zoom_access_token');
            $this->session->set_userdata('zoom_access_token', $oAuthURL['token']);
            redirect('admin/zoom_conference');
        } else {
            echo $oAuthURL['msg'];
        }
    }	
    
    public function getopdipd()
    {
        $opd_ipd    = $this->input->post('opdipd_group');
        $patient_id = $this->input->post('patient_id');

        if ($opd_ipd == 'opd') {
            $result = $this->patient_model->getOpd($patient_id);
        } elseif ($opd_ipd == 'ipd') {
            $result = $this->patient_model->getIpd($patient_id);
        }

        echo json_encode($result);
    }

/*
This Function used for Live Consultation Page
 */
    public function consult()
    {
        if (!$this->rbac->hasPrivilege('live_consultation', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'conference');
        $this->session->set_userdata('sub_menu', 'conference/live_consult');
        $data                       = array();
        $role                       = json_decode($this->customlib->getStaffRole());
        $patient                    = $this->patient_model->getpatient();
        $data['patientlist']        = $patient;
        $patients                   = $this->patient_model->getPatientListall();
        $data["patients"]           = $patients;
        $data['role']               = $role;
        $staff_id                   = $this->customlib->getStaffID();
        $data['logged_staff_id']    = $staff_id;
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata["role_id"];
        $data["doctors"]            = $doctors;
        $conference_setting         = $this->setting_model->getzoomsetting();
        $data['conference_setting'] = $conference_setting;
        $doctorid                   = "";
        $data['opd_ipd']            = $this->opd_ipd;
        $data["bloodgroup"]         = $this->bloodbankstatus_model->get_product(null, 1);
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }
        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $this->load->view('layout/header');
        if ($role->id == 3) {
            $this->load->view('admin/conference/consult', $data);
        } else {
            $roles         = $this->role_model->get();
            $data['roles'] = $roles;
            $this->load->view('admin/conference/staffconsult', $data);
        }
        $this->load->view('layout/footer');
    }

/*
This function used for get Consultation record For datatable
 */
    public function getconsultdatatable()
    {
        $staff_id        = $this->customlib->getStaffID();		
        $logged_staff_id = $staff_id;
        $dt_response     = $this->conference_model->getAllconsultRecord();
		 
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
      
            foreach ($dt_response->data as $key => $value) {
                $return_response = json_decode($value->return_response);           

                $row             = array();
                //====================================

                if ($value->status == 0) {
                    $selected0 = 'selected="selected"';
                } else {
                    $selected0 = "";
                }

                if ($value->status == 1) {
                    $selected1 = 'selected="selected"';
                } else {
                    $selected1 = "";
                }

                if ($value->status == 2) {
                    $selected2 = 'selected="selected"';
                } else {
                    $selected2 = "";
                }
                $action_delete = "";
                $form          = "<form class='chgstatus_form' method='POST' action='" . site_url() . "admin/zoom_conference/chgstatus' ><input type='hidden' name='conference_id' value='$value->id'><select class='form-control w-120-px chgstatus_dropdown' name='chg_status'><option value='0' $selected0  >" . $this->lang->line('awaited') . "</option><option value='1' $selected1 >" . $this->lang->line('cancelled') . "</option><option value='2' $selected2 >" . $this->lang->line('finished') . "</option></select></form>";

                if ($value->status == 0) {
                    $action_button = "<a href='#'  data-target='#modal-chkstatus' data-toggle='modal' class='btn btn-xs label-success starsuccessbtn' data-id=" . $value->id . " ><span class=' font-w-normal'><i class='fa fa-video-camera'></i> " . $this->lang->line('start') . "</span></a>";
                } else {
                    $action_button = "";
                }
                
                if ($value->api_type != 'self') {
                    if ($this->rbac->hasPrivilege('live_consultation', 'can_delete')) {                     
                        $action_delete = "<a href='#' onclick=delete_recordById('admin/zoom_conference/delete/" . $value->id . "/" . $return_response->id . "')  data-target='' data-toggle='tooltip' title=" . $this->lang->line('delete') . " class='btn btn-default btn-xs' ><i class='fa fa-remove'></i></a>";
                    }
                } else {
                    $action_delete = "";
                }
				
                $name        = ($value->create_for_surname == "") ? $value->create_for_name : $value->create_for_name . " " . $value->create_for_surname;
                $created_for = $name . " (" . $value->create_for_role_name . ": " . $value->create_for_employee_id . ")";

                $pname        = ($value->patient_name == "") ? $value->patient_name : $value->patient_name;
                $patient_name = $pname . " (" . $value->pid . ")";

                //==============================
                $row[] = $value->title;
                $row[] = $value->description;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);

                if($value->api_type!=""){
                    $row[] = $this->lang->line($value->api_type);
                }   else{
                    $row[] = "";
                } 
                
                if ($value->created_id == $logged_staff_id) {
                    $row[] = $this->lang->line('self');
                } else {
                    $name  = ($value->create_by_surname == "") ? $value->create_by_name : $value->create_by_name . " " . $value->create_by_surname;
                    $row[] = $name . " (" . $value->create_by_role_name . ": " . $value->create_by_employee_id . ")";
                }
                $row[] = $created_for;
                $row[] = $patient_name;
                $row[] = $form;
                $row[] = $action_button . $action_delete;
                //====================

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

    public function join($type, $id)
    {
        $zoom_api_key    = "";
        $zoom_api_secret = "";
        if ($type == "consult") {
            $leaveUrl = "admin/zoom_conference/consult";
        } elseif ($type == "meeting") {
            $leaveUrl = "admin/zoom_conference/meeting";
        }
        $live = $this->conference_model->getdata($id);
        
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
        if ($type == "meeting") {
            $data['host'] = ($live->create_by_surname == "") ? $live->create_by_name . " (" . $live->create_by_employee_id .")" : $live->create_by_name . " " . $live->create_by_surname . " (" . $live->create_by_employee_id;
            $staff_id     = $this->customlib->getStaffID();
            if ($live->created_id != $staff_id) {
                $data_insert = array(
                    'conference_id' => $id,
                    'staff_id'      => $staff_id,
                );
                $this->conferencehistory_model->updatehistory($data_insert, 'staff');
            }
        } elseif ($type == "consult") {
            $data['host'] = ($live->create_for_surname == "") ? $live->create_for_name . " (" . $live->create_by_employee_id .")" : $live->create_for_name . " " . $live->create_for_surname . " (" . $live->create_by_employee_id .")";
        }
        $data['name'] = $this->customlib->getAdminSessionUserName();
        $this->load->view('admin/conference/join', $data);
    }

    public function getcredential()
    {
        $response                    = array();
        $staff                       = $this->staff_model->get($this->customlib->getStaffID());
        $response['zoom_api_key']    = $staff['zoom_api_key'];
        $response['zoom_api_secret'] = $staff['zoom_api_secret'];
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
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
            $live                       = $this->conference_model->getdata($conference_id);         
         
            $data['conference_setting'] = $this->conference_setting;            
            $staff_id                = $this->customlib->getStaffID();
            $data['logged_staff_id'] = $staff_id;
            $data['live']            = $live;
            $data['live_url']        = json_decode($live->return_response);
            $data['page']            = $this->load->view('admin/conference/_livestatus', $data, true);
            $array                   = array('status' => '1', 'page' => $data['page']);

            $conference_details = $this->notificationsetting_model->getconferenceDetails($conference_id);

            if ($conference_details['purpose'] == 'meeting') {

                $staff_conference_details = $this->notificationsetting_model->getconferencestaffDetails($conference_id);
                $metting_date = $this->customlib->YYYYMMDDHisTodateFormat($conference_details['date'], $this->customlib->getHospitalTimeFormat());

                $staff_list_array = array();
                if (!empty($staff_conference_details)) {
                    foreach ($staff_conference_details as $staff_conference_details_key => $staff_conference_details_value) {
                        $name = $staff_conference_details_value["name"] . " " . $staff_conference_details_value["surname"];
                        $staff_list = $name . " (" . $staff_conference_details_value['user_type'] . " : " . $staff_conference_details_value['employee_id'] . ")";
                        if (!empty($staff_list)) {
                            $staff_list_array[] = $staff_list;
                        }
                    }
                }

                if (!empty($staff_list_array)) {
                    $staff_list_array = implode(", ", $staff_list_array);
                }

                $event_data = array(
                    'meeting_title'            => $conference_details['title'],
                    'meeting_date'             => $metting_date,
                    'meeting_duration_minutes' => $conference_details['duration'],
                    'staff_list'               => $staff_list_array,
                );

                $this->system_notification->send_system_notification('live_meeting_start', $event_data, $staff_conference_details);

            } else {

                $doctor_details = $this->notificationsetting_model->getstaffDetails($conference_details['staff_id']);
            
                if (!empty($conference_details['visit_details_id'])) {
                    $opd_id = $this->patient_model->getVisitDetailsid($conference_details['visit_details_id']);
                        
                    $event_data = array(
                        'consultation_title'            => $conference_details['title'],
                        'patient_id'                    => $conference_details['patient_id'],
                        'consultation_date'             => $this->customlib->YYYYMMDDHisTodateFormat($conference_details['date'], $this->customlib->getHospitalTimeFormat()),
                        'doctor_name'                   => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                        'doctor_id'                     => $conference_details['staff_id'],
                        'consultation_duration_minutes' => $conference_details['duration'],
                        'opd_no'                        => $this->customlib->getSessionPrefixByType('opd_no') . $conference_details['visit_details_id'],
                        'checkup_id'                    => $this->customlib->getSessionPrefixByType('checkup_id') . $conference_details['visit_details_id'],
                    );

                    $this->system_notification->send_system_notification('live_opd_consultation_start', $event_data);
                } else {

                    $event_data = array(
                        'consultation_title'            => $conference_details['title'],
                        'patient_id'                    => $conference_details['patient_id'],
                        'consultation_date'             => $this->customlib->YYYYMMDDHisTodateFormat($conference_details['date'], $this->customlib->getHospitalTimeFormat()),
                        'doctor_name'                   => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                        'doctor_id'                     => $conference_details['staff_id'],
                        'consultation_duration_minutes' => $conference_details['duration'],
                        'ipd_no'                        => $this->customlib->getSessionPrefixByType('ipd_no') . $conference_details['ipd_id'],
                    );

                    $this->system_notification->send_system_notification('live_ipd_consultation_start', $event_data);

                }
            }
            echo json_encode($data);
            //=====
        }
    }

    public function delete($id, $zoom_id)
    {
        $result          = $this->conference_model->getdelete($id);
        $return_response = array();
        if (empty($result)) {
            $this->session->set_flashdata('msg', '<div class="alert alert-error text-left">' . $this->lang->line("something_went_wrong") . '</div>');            
            $return_response = array('msg' => $this->lang->line("something_went_wrong"), 'status' => 0);
        }

        if ($result->api_type == 'global') {
            $params = array(
                'zoom_api_key'    => "",
                'zoom_api_secret' => "",
            );
        } else {
            $staff = $this->staff_model->get($this->customlib->getStaffID());
            if ($staff['zoom_api_key'] == "" && $staff['zoom_api_secret'] == "") {
                $this->session->set_flashdata('msg', '<div class="alert alert-error text-left">' . $this->lang->line("You_have_created_by_your_own_account_api_credential_not_exists") . '</div>');
                $return_response = array('msg' => $this->lang->line("You_have_created_by_your_own_account_api_credential_not_exists"), 'status' => 0);
            }
            $params = array(
                'zoom_api_key'    => $staff['zoom_api_key'],
                'zoom_api_secret' => $staff['zoom_api_secret'],
            );
        }
        $this->load->library('zoom_api', $params);
        $response = $this->zoom_api->deleteMeeting($zoom_id);
         
        $data['title'] = $this->lang->line('delete_conference');
        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">' . $this->lang->line('delete_message') . '</div>');
        $this->conference_model->remove($id);
        $return_response = array('msg' => $this->lang->line('delete_message'), 'status' => 1);
         
        echo json_encode($return_response);
    }

    public function delete_consult($id, $zoom_id=NULL, $force_delete = NULL)
    {
        $result = $this->conference_model->getdelete($id);
        if (empty($result)) {
            $this->session->set_flashdata('msg', '<div class="alert alert-error text-left">' . $this->lang->line("something_went_wrong") . '</div>');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }

		if (!is_null($force_delete)) {
            $data['title'] = 'Delete Conference';
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
            $this->conference_model->remove($id);
        } else {			
			if ($result->api_type == 'global') {
				$params = array(
					'zoom_api_key'    => "",
					'zoom_api_secret' => "",
				);
			} else {
				$staff = $this->staff_model->get($this->customlib->getStaffID());
				if ($staff['zoom_api_key'] == "" && $staff['zoom_api_secret'] == "") {
					$this->session->set_flashdata('msg', '<div class="alert alert-error text-left">' . $this->lang->line("You_have_created_by_your_own_account_api_credential_not_exists") . '</div>');
					redirect($_SERVER['HTTP_REFERER'], 'refresh');
				}
				$params = array(
					'zoom_api_key'    => $staff['zoom_api_key'],
					'zoom_api_secret' => $staff['zoom_api_secret'],
				);
			}
			
			$this->load->library('zoom_api', $params);
			$response = $this->zoom_api->deleteMeeting($zoom_id);		
		
			if (!$response['status']) {
				$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $response['message'] . ' if you want to force delete from your application  <a href="' . site_url('admin/zoom_conference/delete_consult/' . $id . '/' . $zoom_id . '/force_delete') . '"> Click Here </a></div>');
			} else {
				$data['title'] = $this->lang->line('delete_conference');
				$this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
				$this->conference_model->remove($id);
			}		
		}	
        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function addcredential()
    {
        $response = array();
        if ($this->input->post('button') == "save") {
            $this->form_validation->set_rules('zoom_api_key', $this->lang->line('zoom_api_key'), 'required|trim|xss_clean');
            $this->form_validation->set_rules('zoom_api_secret', $this->lang->line('zoom_api_secret'), 'required|trim|xss_clean');
            if ($this->form_validation->run() == false) {
                $data = array(
                    'zoom_api_key'    => form_error('zoom_api_key'),
                    'zoom_api_secret' => form_error('zoom_api_secret'),
                );
                $response = array('status' => 0, 'error' => $data);
            } else {
                $insert_array = array(
                    'id'              => $this->customlib->getStaffID(),
                    'zoom_api_key'    => $this->input->post('zoom_api_key'),
                    'zoom_api_secret' => $this->input->post('zoom_api_secret'),
                );
                $insert_id = $this->staff_model->update($insert_array);
                $response  = array('status' => 1, 'message' => $this->lang->line('success_message'));
            }
        } else {
            $insert_array = array(
                'id'              => $this->customlib->getStaffID(),
                'zoom_api_key'    => null,
                'zoom_api_secret' => null,
            );
            $insert_id = $this->staff_model->update($insert_array);
            $response  = array('status' => 1, 'message' => $this->lang->line('update_message'));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function addByOther()
    {
        $response     = array();
        $ipdid        = "";
        $opdid        = "";
        $visitid      = "";
        $select_group = "";
        $this->form_validation->set_rules('date', $this->lang->line('consultation_date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('staff_id', $this->lang->line('consultant_doctor'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('host_video', $this->lang->line('host_video'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('client_video', $this->lang->line('client_video'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('duration', $this->lang->line('consultation_duration_minutes'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'required|trim|xss_clean');

        if (!$this->session->has_userdata('zoom_access_token')) {
            $this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
        }
		
        if ($this->form_validation->run() == false) {
            $data = array(
                'date'         => form_error('date'),
                'staff_id'     => form_error('staff_id'),
                'host_video'   => form_error('host_video'),
                'client_video' => form_error('client_video'),
                'duration'     => form_error('duration'),
                'patient_id'   => form_error('patient_id'),
                'zoom_live'   	=> form_error('zoom_live'),
            );
            $response = array('status' => 0, 'error' => $data);
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
        } else {
            //=======
            $api_type = 'global';
            $params   = array(
                'zoom_api_key'    => "",
                'zoom_api_secret' => "",
            );
            $this->load->library('zoom_api', $params);
            $select_group = $this->input->post('select_group');
            if ($select_group == 'opd') {
                $visitid = $this->input->post('visit_id');
            } else {
                $visitid = null;
            }

            if ($select_group == 'ipd') {
                $ipdid = $this->input->post('opdipd_id');
            } else {
                $ipdid = null;
            }

            $insert_array = array(
                'staff_id'         => $this->input->post('staff_id'),
                'patient_id'       => $this->input->post('patient_id'),
                'title'            => $this->input->post('title'),
                'visit_details_id' => $visitid,
                'ipd_id'           => $ipdid,
                'date'             => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date'), $this->time_format),
                'duration'         => $this->input->post('duration'),
                'password'         => $this->input->post('password'),
                'created_id'       => $this->customlib->getStaffID(),
                'api_type'         => $api_type,
                'purpose'          => 'consult',
                'host_video'       => $this->input->post('host_video'),
                'client_video'     => $this->input->post('client_video'),
                'description'      => $this->input->post('description'),
                'timezone'         => $this->customlib->getTimeZone(),
            );

            $response = $this->zoom_api->createAMeeting($insert_array);

            if ($response['status']) {
                if (isset($response['data'])) {
                    $insert_array['return_response'] = json_encode($response['data']);
                    $conferenceid                    = $this->conference_model->add($insert_array);
                    $sender_details                  = array('patient_id' => $this->input->post('patient_id'), 'conference_id' => $conferenceid, 'contact_no' => $this->input->post('mobileno'), 'email' => $this->input->post('email'));
                    $this->mailsmsconf->mailsms('live_consult', $sender_details);
                    $response = array('status' => 1, 'message' => $this->lang->line('success_message'));
                } else {
                    $response = array('status' => 0, 'error' => array($response['message']));
                }

            } else {
                $response = array('status' => 0, 'error' => array('Something went wrong.'));
            }

            $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('staff_id'));
            
            if ($select_group == $this->lang->line('opd')) {

                $event_data = array(
                    'consultation_title'            => $this->input->post('title'),
                    'patient_id'                    => $this->input->post('patient_id'),
                    'consultation_date'             => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date'), $this->time_format),
                    'doctor_id'                     => $this->input->post('staff_id'),
                    'doctor_name'                   => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                    'consultation_duration_minutes' => $this->input->post('duration'),
                    'opd_no'                        => $this->customlib->getSessionPrefixByType('opd_no') . $ipdid,
                    'checkup_id'                    => $this->customlib->getSessionPrefixByType('checkup_id') . $visitid,
                );

                $this->system_notification->send_system_notification('live_opd_consultation_add', $event_data);
            } else if ($select_group == $this->lang->line('ipd')) {
                $event_data = array(
                    'consultation_title'            => $this->input->post('title'),
                    'patient_id'                    => $this->input->post('patient_id'),
                    'consultation_date'             => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date'), $this->time_format),
                    'doctor_id'                     => $this->input->post('staff_id'),
                    'doctor_name'                   => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                    'consultation_duration_minutes' => $this->input->post('duration'),
                    'ipd_no'                        => $this->customlib->getSessionPrefixByType('ipd_no') . $ipdid,
                );

                $this->system_notification->send_system_notification('live_ipd_consultation_add', $event_data);

            } else {
				
                $date   =   $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date'), $this->time_format);
                $date   =   $this->customlib->YYYYMMDDHisTodateFormat($date, $this->customlib->getHospitalTimeFormat());

                $event_data = array(
                    'consultation_title'            => $this->input->post('title'),
                    'patient_id'                    => $this->input->post('patient_id'),
                    'consultation_date'             => $this->customlib->YYYYMMDDHisTodateFormat($this->input->post('date'), $this->time_format),
                    'doctor_id'                     => $this->input->post('staff_id'),
                    'doctor_name'                   => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                    'consultation_duration_minutes' => $this->input->post('duration'),
                );

                $this->system_notification->send_system_notification('patient_consultation_add', $event_data);
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
        }
    }

/*
This Function is used for get Live meeting
 */
    public function meeting()
    {
        if (!$this->rbac->hasPrivilege('live_meeting', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'conference');
        $this->session->set_userdata('sub_menu', 'conference/live_meeting');
        $data                    = array();
        $role                    = json_decode($this->customlib->getStaffRole());
        $data['role']            = $role;
        $data['logged_staff_id'] = $this->customlib->getStaffID();
        $data['staffList']       = $this->staff_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/conference/meeting', $data);
        $this->load->view('layout/footer');
    }

/*
This Function is used for get Live meeting for datatable list
 */
    public function getmeetingdatatable()
    {
        $role            = json_decode($this->customlib->getStaffRole());
        $data['role']    = $role;
        $logged_staff_id = $this->customlib->getStaffID();

        if ($role->id == 7) {
            $dt_response = $this->conference_model->getAllmeetingRecord();
        } else {
            $dt_response = $this->conference_model->getAllmeetingRecord($logged_staff_id);
        }

        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $return_response = json_decode($value->return_response);
                $row             = array();
                //====================================
                if ($value->created_id == $logged_staff_id) {
                    if ($value->status == 0) {
                        $selected0 = 'selected="selected"';
                    } else {
                        $selected0 = "";
                    }

                    if ($value->status == 1) {
                        $selected1 = 'selected="selected"';
                    } else {
                        $selected1 = "";
                    }

                    if ($value->status == 2) {
                        $selected2 = 'selected="selected"';
                    } else {
                        $selected2 = "";
                    }
                    $form = "<form class='chgstatus_form' method='POST' action='" . site_url() . "admin/zoom_conference/chgstatusmeeting' ><input type='hidden' name='conference_id' value='$value->id'><select class='form-control chgstatus_dropdown w-120-px' name='chg_status'><option value='0' $selected0  >" . $this->lang->line('awaited') . "</option><option value='1' $selected1 >" . $this->lang->line('cancelled') . "</option><option value='2' $selected2 >" . $this->lang->line('finished') . "</option></select></form>";
                } else {
                    if ($value->status == 0) {
                        $selectedvalue = "<span class='label label-warning font-w-normal'>" . $this->lang->line('awaited') . "</span>";
                    } elseif ($value->status == 1) {
                        $selectedvalue = "<span class='label label-default'>" . $this->lang->line('cancelled') . "</span>";
                    } else {
                        $selectedvalue = "<span class='label label-success'>" . $this->lang->line('finished') . "</span>";
                    }
                    $form = $selectedvalue;
                }

                if ($value->status == 0) {
                    if ($value->created_id == $logged_staff_id) {
                        $label_display = $this->lang->line('start');
                        $label_type    = 'label-success';
                    } else {
                        $label_display = $this->lang->line('join');
                        $label_type    = 'label-success';
                    }

                    $action_button = "<a href='#' data-target='#modal-chkstatus' data-toggle='modal' class='btn btn-xs label-success starsuccessbtn' data-id=" . $value->id . " ><span class='font-w-normal' " . $label_type . "'><i class='fa fa-video-camera'></i> " . $label_display . "</span></a>";
                } else {
                    $action_button = "";
                }
                $action_delete = "";
                if ($value->created_id == $logged_staff_id) {
                    if ($this->rbac->hasPrivilege('live_meeting', 'can_delete')) {
                        $action_delete = "<a href='#' onclick=delete_recordById('admin/zoom_conference/delete/" . $value->id . "/" . $return_response->id . "')  data-target='' data-toggle='tooltip' title=" . $this->lang->line('delete') . " class='btn btn-default btn-xs' ><i class='fa fa-remove'></i></a>";
                    }
                } else {
                    $action_delete = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                //==============================
                $row[] = $value->title;
                $row[] = $value->description;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->duration;
                $row[] = $this->lang->line($value->api_type);
                if ($value->created_id == $logged_staff_id) {
                    $row[] = $this->lang->line('self');
                } else {
                    $row[] = $value->create_by_name . " " . $value->create_by_surname . " (" . $value->create_by_employee_id .")";
                }
                $row[] = $form;
                $row[] = $action_button . ' ' . $action_delete;

                //====================

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

/*
This Function is used for add meeting for staff

 */
    public function addMeeting()
    {
        $response = array();
        $this->form_validation->set_rules('title', $this->lang->line('meeting_title'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('meeting_date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('duration', $this->lang->line('meeting_duration_minutes'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('host_video', $this->lang->line('host_video'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('staff[]', $this->lang->line('staff'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('client_video', $this->lang->line('client_video'), 'required|trim|xss_clean');
        if (!$this->session->has_userdata('zoom_access_token')) {
            $this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
        }                                         
        if ($this->form_validation->run() == false) {
            $data = array(
                'title'        => form_error('title'),
                'date'         => form_error('date'),
                'staff[]'      => form_error('staff[]'),
                'host_video'   => form_error('host_video'),
                'client_video' => form_error('client_video'),
                'password'     => form_error('password'),
                'duration'     => form_error('duration'),
                 'zoom_live'     => form_error('zoom_live')                                         
            );

            $response = array('status' => 0, 'error' => $data);

        } else {
            //=======
            $api_type = 'global';
            $params   = array(
                'zoom_api_key'    => "",
                'zoom_api_secret' => "",
            );

            $this->load->library('zoom_api', $params);
            //============
            $insert_array = array(
                'title'        => $this->input->post('title'),
                'date'         => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date'), $this->time_format),
                'duration'     => $this->input->post('duration'),
                'password'     => $this->input->post('password'),
                'created_id'   => $this->customlib->getStaffID(),
                'api_type'     => $api_type,
                'host_video'   => $this->input->post('host_video'),
                'client_video' => $this->input->post('client_video'),
                'description'  => $this->input->post('description'),
                'purpose'      => 'meeting',
                'timezone'     => $this->customlib->getTimeZone(),
            );

            $response = $this->zoom_api->createAMeeting($insert_array);
            $staff    = $this->input->post('staff[]');

            if ($response['status']) {
                if (isset($response['data'])) {
                    $insert_array['return_response'] = json_encode($response['data']);
                    $this->conference_model->addmeeting($insert_array, $staff);
                    $staff_mail_sms_list = $this->conference_model->getAllStaffByArray($staff);
                    $staff_list_array = array();
                    if (!empty($staff_mail_sms_list)) {
                        $sender_details = array();
                        foreach ($staff_mail_sms_list as $staff_mail_sms_list_key => $staff_mail_sms_list_value) {
                            $sender_details[] = array(
                                'title'       => $this->input->post('title'),
                                'date'        => $this->input->post('date'),
                                'duration'    => $this->input->post('duration'),
                                'employee_id' => $staff_mail_sms_list_value->employee_id,
                                'department'  => $staff_mail_sms_list_value->department,
                                'designation' => $staff_mail_sms_list_value->designation,
                                'name'        => ($staff_mail_sms_list_value->surname == "") ? $staff_mail_sms_list_value->name : $staff_mail_sms_list_value->name . " " . $staff_mail_sms_list_value->surname,
                                'contact_no'  => $staff_mail_sms_list_value->contact_no,
                                'email'       => $staff_mail_sms_list_value->email,
                            );

                            $name = $staff_mail_sms_list_value->name . " " . $staff_mail_sms_list_value->surname;
                            $staff_list         = $name . " (" . $staff_mail_sms_list_value->role . " : " . $staff_mail_sms_list_value->employee_id . ")";
                            $staff_list_array[] = $staff_list;
                        }

                        $this->mailsmsconf->mailsms('live_meeting', $sender_details);
                    }

                    if (!empty($staff_list_array)) {
                        $staff_list_array = implode(", ", $staff_list_array);
                    }

                    $event_data = array(
                        'meeting_title'            => $this->input->post('title'),
                        'meeting_date'             => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date'), $this->time_format),
                        'meeting_duration_minutes' => $this->input->post('duration'),
                        'staff_list'               => $staff_list_array,
                    );

                    $this->system_notification->send_system_notification('live_meeting_add', $event_data, $staff_mail_sms_list);

                    $response = array('status' => 1, 'message' => $this->lang->line('success_message'));
                } else {
                    $response = array('status' => 0, 'error' => array($response->message));
                }

            } else {
                $response = array('status' => 0, 'error' => array('Something went wrong.'));
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

/*
This Function is used for check the status of Live Consultation
 */
    public function chgstatus()
    {
        $response = array();
        $this->form_validation->set_rules('conference_id', $this->lang->line('zoom_api_key'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('chg_status', $this->lang->line('zoom_api_secret'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'conference_id' => form_error('conference_id'),
                'chg_status'    => form_error('chg_status'),
            );
            $response = array('status' => 0, 'error' => $data);
        } else {
            $insert_array = array(
                'status' => $this->input->post('chg_status'),
            );
            $insert_id = $this->conference_model->update($this->input->post('conference_id'), $insert_array);
            $response  = array('status' => 1, 'message' => $this->lang->line('update_message'));
        }
        redirect("admin/zoom_conference/consult");
    }

    public function changeconsultation()
    {
        $response = array();
        $this->form_validation->set_rules('conference_id', $this->lang->line('zoom_api_key'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('chg_status', $this->lang->line('zoom_api_secret'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'conference_id' => form_error('conference_id'),
                'chg_status'    => form_error('chg_status'),
            );
            $response = array('status' => 0, 'error' => $data);
        } else {
            $insert_array = array(
                'status' => $this->input->post('chg_status'),
            );
            $insert_id = $this->conference_model->update($this->input->post('conference_id'), $insert_array);
            $response  = array('status' => 1, 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($response);
    }

/*
This Function is used for check the status of Live Meeting
 */
    public function chgstatusmeeting()
    {
        $response = array();
        $this->form_validation->set_rules('conference_id', $this->lang->line('zoom_api_key'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('chg_status', $this->lang->line('zoom_api_secret'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'conference_id' => form_error('conference_id'),
                'chg_status'    => form_error('chg_status'),
            );
            $response = array('status' => 0, 'error' => $data);
        } else {
            $insert_array = array(
                'status' => $this->input->post('chg_status'),
            );
            $insert_id = $this->conference_model->update($this->input->post('conference_id'), $insert_array);
            $response  = array('status' => 1, 'message' => $this->lang->line('update_message'));
        }
        redirect("admin/zoom_conference/meeting");
    }

    public function meeting_report()
    {
        if (!$this->rbac->hasPrivilege('live_meeting_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/live_consultation');
        $this->session->set_userdata('subsub_menu', 'reports/live_consultation/meeting_report');

        $data                    = array();
        $staff_id                = $this->customlib->getStaffID();
        $data['logged_staff_id'] = $staff_id;
        $data['opd_ipd']         = $this->opd_ipd;
        $data['searchlist']      = $this->search_type;
        $data['meetingList']     = $this->conferencehistory_model->getmeeting();
        $data['stafflist']       = $this->staff_model->getall('', 1);
        $this->load->view('layout/header');
        $this->load->view('admin/conference/meeting_report', $data);
        $this->load->view('layout/footer');
    }

    public function consult_report()
    {
        if (!$this->rbac->hasPrivilege('live_consultation_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/live_consultation');
        $this->session->set_userdata('subsub_menu', 'reports/live_consultation/consult_report');

        $data                    = array();
        $staff_id                = $this->customlib->getStaffID();
        $data['logged_staff_id'] = $staff_id;
        $data['opd_ipd']         = $this->opd_ipd;
        $data["searchlist"]      = $this->search_type;
        $data['consultList']     = $this->conferencehistory_model->getconsult();
        $data['stafflist']       = $this->staff_model->getall('', 1);
        $this->load->view('layout/header');
        $this->load->view('admin/conference/consult_report', $data);
        $this->load->view('layout/footer');
    }

    public function liveconsultationreport()
    {
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $start_date            = '';
        $end_date              = '';
        $fields                = $this->customfield_model->get_custom_fields('ipd', '', '', 1);
        if ($search['search_type'] == 'period') {
            $data['start_date'] = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $data['end_date']   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);

        } else {
            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }
            $data['start_date'] = $dates['from_date'];
            $data['end_date']   = $dates['to_date'];
        }

        $data['created_by']  = $this->input->post('created_by');
        $data['module_type'] = $this->input->post('select_module');
        $dt_response = $this->conference_model->getliveconsultreports($data);
        
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $action = "<div class='rowoptionview'>";
                $action = "<button type='button' class='btn btn-default btn-xs viewer-list pull-right' id='load'  data-recordid='" . $value->id . "' title= '" . $this->lang->line('join') . "' data-loading-text='looading..'><i class='fa fa-list'></i></button>";

                $action .= "</div'>";
                $staff_id = $this->customlib->getStaffID();
                if ($value->created_id == $staff_id) {
                    $created_by = $this->lang->line('self');
                } else {
                    $created_by = $value->create_by_name . " " . $value->create_by_surname . "  (" . $value->create_by_employee_id.")";
                }

                $module_type = '';

                if ($value->visit_details_id != '') {
                    $module_type = $this->customlib->getSessionPrefixByType('opd_no') . $value->visit_details_id;
                } elseif ($value->ipd_id != '') {
                    $module_type = $this->customlib->getSessionPrefixByType('ipd_no') . $value->ipd_id;
                }

                //==============================
                $row[]     = $module_type;
                $row[]     = $value->title;
                $row[]     = composePatientName($value->patient_name, $value->patient_id);
                $row[]     = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->customlib->getHospitalTimeFormat());
                $row[]     = $this->lang->line($value->api_type);
                $row[]     = $created_by;
                $row[]     = $value->total_viewers;
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

    public function livemeetingreport()
    {
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $start_date            = '';
        $end_date              = '';
        $fields                = $this->customfield_model->get_custom_fields('ipd', '', '', 1);
        if ($search['search_type'] == 'period') {

            $data['start_date'] = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $data['end_date']   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);

        } else {

            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }
            $data['start_date'] = $dates['from_date'];
            $data['end_date']   = $dates['to_date'];

        }

        $data['created_by'] = $this->input->post('created_by');
        $dt_response        = $this->conference_model->getlivemeetingreports($data);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $action = "<div class='rowoptionview'>";
                $action = "<button type='button' class='btn btn-default btn-xs viewer-list' id='load'  data-recordid='" . $value->id . "' title= '" . $this->lang->line('join') . "' data-loading-text='looading..'><i class='fa fa-list'></i></button>";
                $action .= "</div'>";

                $staff_id = $this->customlib->getStaffID();
                if ($value->created_id == $staff_id) {
                    $created_by = $this->lang->line('self');
                } else {
                    $created_by = $value->create_by_name . " " . $value->create_by_surname . "  (" . $value->create_by_employee_id.")";
                }

                //==============================
                $row[]     = $value->title;
                $row[]     = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->customlib->getHospitalTimeFormat());
                $row[]     = $this->lang->line($value->api_type);
                $row[]     = $created_by;
                $row[]     = $value->total_viewers;
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
            $staff_id    = $this->customlib->getStaffID();
            $data_insert = array(
                'conference_id' => $this->input->post('id'),
                'staff_id'      => $staff_id,
            );
            $this->conferencehistory_model->updatehistory($data_insert, 'staff');
            $array = array('status' => 1, 'error' => '');
            echo json_encode($array);
        }
    }

    public function getViewerList()
    {
        $recordid     = $this->input->post('recordid');
        $type         = $this->input->post('type');
        $data['type'] = 'staff';

        if (isset($type)) {
            $data['type']         = $type;
            $data['viewerDetail'] = $this->conferencehistory_model->getLivePatient($recordid);
        } else {
            $data['viewerDetail'] = $this->conferencehistory_model->getMeetingStaff($recordid);
        }

        $data['page'] = $this->load->view('admin/conference/_partialviewerlist', $data, true);
        echo json_encode($data);
    }

    public function checkvalidation()
    {        
        $param = array(
            'search_type'   => $this->input->post('search_type'),
            'created_by'    => $this->input->post('created_by'),
            'date_from'     => $this->input->post('date_from'),
            'date_to'       => $this->input->post('date_to'),
            'select_module' => $this->input->post('select_module'),
        );

        $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));      
        echo json_encode($json_array);
    }
}
