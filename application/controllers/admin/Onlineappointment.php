<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Onlineappointment extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("staff_model");
        $this->load->model(array("onlineappointment_model", "charge_category_model"));
        $this->load->library("datatables");
        $this->time_format = $this->customlib->getHospitalTimeFormat();
        $this->load->library("customlib");
    }

    public function index()
    {        
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/onlineappointment');
        $this->session->set_userdata('sub_menu', 'admin/onlineappointment');
        $this->load->view('layout/header');
        $data['doctors']         = $this->staff_model->getStaffbyrole(3);
        $doctor                  = $this->input->post("doctor");
        $data['charge_category'] = $this->charge_category_model->getCategoryByModule("appointment");
        $this->form_validation->set_rules("doctor", $this->lang->line("doctor"), "trim|required|xss_clean");
        $this->form_validation->set_rules("shift", $this->lang->line("shift"), "trim|required|xss_clean");

        if ($this->form_validation->run() == false) {
            $this->load->view('admin/onlineappointment/index', $data);
            $this->load->view('layout/footer');
        } else {
            $data["days"]               = $this->customlib->getDaysname();
            $doc_data                   = $this->onlineappointment_model->getDocData($doctor);
            $data['charge_id']          = isset($doc_data['charge_id']) ? $doc_data['charge_id'] : "";
            $charges                    = $this->charge_model->getChargeByChargeId($data['charge_id']);
            $data['charge_category_id'] = isset($charges['charge_category_id']) ? $charges['charge_category_id'] : "";
            $data['charge']             = $this->charge_model->getchargeDetails($data['charge_category_id']);
            $charges                    = $this->charge_model->getChargeByChargeId($data['charge_id']);
            $data['standard_charge']    = isset($charges['standard_charge']) ? $charges['standard_charge'] : 0;
            $data['percentage']         = isset($charges['percentage']) ? $charges['percentage'] : 0;
            $data['appointment_charge'] = $data['standard_charge'] + ($data['standard_charge'] * $data['percentage'] / 100);
            $data['duration']           = isset($doc_data['consult_duration']) ? $doc_data['consult_duration'] : "";
            $this->load->view('admin/onlineappointment/index', $data);
            $this->load->view('layout/footer');
        }
    }

    public function getShiftdata()
    {
        if (!$this->rbac->hasPrivilege('online_appointment_slot', 'can_view')) {
            access_denied();
        }
        $data                = array();
        $data['total_count'] = 1;
        $day                 = $this->input->post('day');
        $doctor_id           = $this->input->post("doctor");
        $shift               = $this->input->post("shift");

        $getDoctorGlobalShiftId = $this->onlineappointment_model->getDoctorGlobalShiftId($doctor_id, $shift);        
        $prev_record = $this->onlineappointment_model->getShiftdata($doctor_id, $day, $getDoctorGlobalShiftId['id']);

        if (empty($prev_record)) {
            $data['prev_record'] = array();
        } else {
            $data['total_count'] = count($prev_record);
            $data['prev_record'] = $prev_record;
        }
        $data['day']    = $day;
        $data['doctor'] = $doctor_id;
        $data['shift']  = $shift;

        $data['html'] = $this->load->view('admin/onlineappointment/addrow', $data, true);
        echo json_encode($data);
    }

    public function saveDoctorShift()
    {
        if (!$this->rbac->hasPrivilege('online_appointment_slot', 'can_edit')) {
            access_denied();
        }
        $json = array();
        $this->form_validation->set_rules('day', $this->lang->line('days'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consult_time', $this->lang->line('consultation_duration'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('charge_id', $this->lang->line('charge_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('doctor', $this->lang->line("doctor"), 'trim|required|xss_clean');
        $this->form_validation->set_rules('shift', $this->lang->line('shift'), 'trim|required|xss_clean');
        $total_rows = $this->input->post("total_row");
        if (!empty($total_rows)) {
            foreach ($this->input->post('total_row') as $key => $value) {
                $this->form_validation->set_rules('time_from_' . $value, 'Time From', 'trim|required|xss_clean');
                $this->form_validation->set_rules('time_to_' . $value, 'Time To', 'trim|required|xss_clean');
            }
        }

        if (!$this->form_validation->run()) {
            $json = array(
                'day'          => form_error('day'),
                'doctor'       => form_error('doctor'),
                'shift'        => form_error('shift'),
                'consult_time' => form_error('consult_time'),
                'charge_id'    => form_error('charge_id'),
            );
            if (!empty($total_rows)) {
                foreach ($this->input->post('total_row') as $key => $value) {
                    $json['time_from_' . $value] = form_error('time_from_' . $value);
                    $json['time_to_' . $value]   = form_error('time_to_' . $value);
                }
            }
            $json_array = array('status' => '0', 'error' => $json);
        } else {

            /************************* Time Validation Code Start ******************************/
            $shift_id           = $this->input->post("shift");
            $global_shift       = $this->onlineappointment_model->getGlobalShift($shift_id);
            $global_shift_start = date("H:i:s", strtotime($global_shift["start_time"]));
            $global_shift_end   = date("H:i:s", strtotime($global_shift["end_time"]));
            if (!empty($total_rows)) {
                foreach ($total_rows as $total_key => $total_value) {
                    $first_start = date("H:i:s", strtotime($this->input->post('time_from_' . $total_value)));
                    $first_end   = date("H:i:s", strtotime($this->input->post('time_to_' . $total_value)));
                    if ($first_start >= $first_end) {
                        echo json_encode(array("status" => 3));
                        return;
                    }
                    if ($first_start < $global_shift_start || $first_end > $global_shift_end) {
                        echo json_encode(array("status" => 5));
                        return;
                    }
                    foreach ($total_rows as $total_key1 => $total_value1) {
                        if ($total_key < $total_key1) {
                            $second_start = date("H:i:s", strtotime($this->input->post('time_from_' . $total_value1)));
                            $second_end   = date("H:i:s", strtotime($this->input->post('time_to_' . $total_value1)));
                            if ($second_start >= $second_end) {
                                echo json_encode(array("status" => 3, "shift_one" => $total_value, "shift_two" => $total_value1));
                                return;
                            }
                            if ($first_start <= $second_end && $second_start <= $first_end) {
                                echo json_encode(array("status" => 4, "shift_one" => $total_value, "shift_two" => $total_value1));
                                return;
                            }
                        } else {
                            continue;
                        }
                    }
                }
            }
            /************************* Time Validation Code End ******************************/

            $consult_fee  = $this->input->post('consult_fee');
            $consult_time = $this->input->post('consult_time');
            $charge_id    = $this->input->post('charge_id');
            $day          = $this->input->post('day');
            $doctor_id    = $this->input->post('doctor');
            $total_row    = $this->input->post('total_row');
            $insert_array = array();
            $update_array = array();
            $old_input    = array();
            $prev_array   = $this->input->post('prev_array');
            if (isset($prev_array)) {
                foreach ($prev_array as $prev_arr_key => $prev_arr_value) {
                    $old_input[] = $prev_arr_value;
                }
            }
            $preserve_array = array();
            if (isset($total_row)) {
                foreach ($total_row as $total_key => $total_value) {
                    $prev_id = $this->input->post('prev_id_' . $total_value);
                    
                     $getDoctorGlobalShiftId =   $this->onlineappointment_model->getDoctorGlobalShiftId($doctor_id, $shift_id);
                     
                    if ($prev_id == 0) {
                        $insert_array[] = array(
                            'day'             => $day,
                            'staff_id'        => $doctor_id,
                            'doctor_global_shift_id' => $getDoctorGlobalShiftId['id'],
                            'start_time'      => date("H:i:s", strtotime($this->input->post('time_from_' . $total_value))),
                            'end_time'        => date("H:i:s", strtotime($this->input->post('time_to_' . $total_value))),
                        );
                    } else {
                        $preserve_array[] = $prev_id;                
                        
                        $update_array[]   = array(
                            'id'              => $prev_id,
                            'staff_id'        => $doctor_id,                      
                            'doctor_global_shift_id' => $getDoctorGlobalShiftId['id'],                            
                            'day'             => $day,
                            'start_time'      => date("H:i:s", strtotime($this->input->post('time_from_' . $total_value))),
                            'end_time'        => date("H:i:s", strtotime($this->input->post('time_to_' . $total_value))),
                        );
                    }
                }
            }

            $delete_array = array_diff($old_input, $preserve_array);

            $insert_array = $this->security->xss_clean($insert_array);
            $update_array = $this->security->xss_clean($update_array);

            $result        = $this->onlineappointment_model->add($delete_array, $insert_array, $update_array);
            $shift_details = array(
                "staff_id"         => $doctor_id,
                "consult_duration" => $consult_time,
                "charge_id"        => $charge_id,
            );
            $prev_shift = $this->onlineappointment_model->getShiftDetails($doctor_id);

            $prev_shift = $this->security->xss_clean($prev_shift);

            if (!empty($prev_shift)) {
                $this->onlineappointment_model->updateShiftDetails($shift_details);
            } else {
                $this->onlineappointment_model->addShiftDetails($shift_details);
            }
			
            if ($result) {
                $json_array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $json_array = array('status' => '2', 'error' => '', 'message' => $this->lang->line('something_went_wrong'));
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json_array));
    }

    public function patientSchedule()
    {
        if (!$this->rbac->hasPrivilege('doctor_wise_appointment', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'appointment');
        $this->load->view('layout/header');
        $doctors         = $this->staff_model->getStaffbyrole(3);
        $data['doctors'] = $doctors;
        $this->form_validation->set_rules('doctor', $this->lang->line("doctor"), 'trim|required|xss_clean');     

        if ($this->form_validation->run() == false) {
            $this->load->view('admin/onlineappointment/patientSchedule', $data);
            $this->load->view('layout/footer');
        } else {
            $doctors         = $this->staff_model->getStaffbyrole(3);
            $data['doctors'] = $doctors;
            $doctor_id       = $this->input->post("doctor");
            $date            = $this->input->post("date");
            if ($doctor_id == '') {
                $doctor_id = "null";
            }
            if ($date == '') {
                $date = "null";
            }
            $data['doctor_id'] = $doctor_id;
            $data['date']      = $date;
            $this->load->view('admin/onlineappointment/patientSchedule', $data);
            $this->load->view('layout/footer');
        }
    }

    public function getPatientSchedule()
    {
        $doctor_id = $this->input->get("doctor");
        $date      = $this->input->get("date");
        if ($date != "null") { 
            $date = $this->customlib->dateFormatToYYYYMMDD($date);
        }
        $dt_response = $this->onlineappointment_model->getPatientSchedule($doctor_id, $date);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $column_first = '<a href="#" data-toggle="popover" class="detail_popover">' . $value->patient_name . " (" . $value->id . ") " . '</a>';

                //==============================

                $row[] = $column_first;
                $row[] = $value->mobileno;
                $row[] = $value->date != '' ? date("h:i A", strtotime($value->date)) : "Offline";
                $row[] = $value->email;
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);//$this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format); 
                $row[]     = $this->lang->line(strtolower($value->source));
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

    public function getdoctor()
    {
        $spec_id = $this->input->post('id');
        $active  = $this->input->post('active');
        $result  = $this->staff_model->getdoctorbyspecilist($spec_id);
        echo json_encode($result);
    }

    public function patientQueue()
    {
        if (!$this->rbac->hasPrivilege('patient_queue', 'can_view')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'appointment');

        $data   = array();
        $queue  = array();
        $submit = $this->input->post("submit");
        if (isset($submit)) {
            $this->form_validation->set_rules('doctor', $this->lang->line('doctor'), 'trim|required|xss_clean');
        
            if ($this->form_validation->run() == false) {
                $data["resultlist"] = array();
            } else {
                $doctor       = $this->input->post("doctor");
                $date         = $this->input->post("date");
                $date         = $this->customlib->dateFormatToYYYYMMDD($date);
                $shift        = $this->input->post("slot");
                $global_shift = $this->input->post("global_shift");
                
                if ($submit == "regenerate") {
                    $this->deleteQueue($doctor, $date, $shift);
                }
                
                if($global_shift!="" && $shift!="" && $date!="" ){
                
                    $day          = date("l", strtotime($date));
                    $getDoctorShiftTimeId      = $this->onlineappointment_model->getDoctorShiftTimeId($doctor, $global_shift, $day);
                    $online_data      = $this->onlineappointment_model->getPatientOnline($doctor, $date, $getDoctorShiftTimeId->id);
                    $offline_data     = $this->onlineappointment_model->getPatientOffline($doctor, $date, $getDoctorShiftTimeId->id);
                    $array_of_time    = $this->customlib->getSlotByDoctorShift($doctor, $shift);                
                
                    $online_time      = array_column($online_data, 'time');
                    $iterator_online  = 0;
                    $iterator_offline = 0;
                    
                    foreach ($array_of_time as $time_key => $time_value) {
                        if ($iterator_online < count($online_data)) {
                            if (in_array(date("H:i:s", strtotime($time_value)), $online_time)) {
                                array_push($queue, $online_data[$iterator_online]);
                                $iterator_online++;
                            } else {
                                if ($iterator_offline < count($offline_data)) {
                                    $offline_data[$iterator_offline]["time"] = $time_value;
                                    array_push($queue, $offline_data[$iterator_offline]);
                                    $iterator_offline++;
                                }
                            }
                        } elseif ($iterator_offline < count($offline_data)) {
                            $offline_data[$iterator_offline]["time"] = $time_value;
                            array_push($queue, $offline_data[$iterator_offline]);
                            $iterator_offline++;
                        }
                    }

                    $appointments         = array_column($queue, "appointment_id");
                    $insert_array         = array();
                    $update_array         = array();
                    $where_in             = array();
                    $queue_position       = $this->onlineappointment_model->getLastQueuePosition($doctor, $date, $shift);
                    $prev_queue_postition = $queue_position['position'];
                    if (!empty($appointments)) {
                        foreach ($appointments as $a_key => $a_value) {
                            $appointment_queue = array(
                                "appointment_id" => $a_value,
                                "position"       => ++$prev_queue_postition,
                                "staff_id"       => $doctor,
                                "shift_id"       => $shift,
                                "date"           => $date,
                            );
                            $update_appointment = array(
                                "id"       => $a_value,
                                "is_queue" => 1,
                            );
    
                            array_push($insert_array, $appointment_queue);
                            array_push($update_array, $update_appointment);
                        }
                        $this->onlineappointment_model->insertQueuePositions($insert_array, $update_array);
                    }
                }

                $queue              = $this->onlineappointment_model->getPatientQueue($doctor, $date, $shift);
                 
                $data["resultlist"] = $queue;
                $data["shift"]      = $shift;
            }
        }
        
        $doctors         = $this->staff_model->getStaffbyrole(3);
        $data['doctors'] = $doctors;
        $this->load->view('layout/header');
        $this->load->view('admin/onlineappointment/patientQueue', $data);
        $this->load->view('layout/footer');
    }

    public function deleteQueue($doctor, $date, $shift)
    {
        $appointments = $this->onlineappointment_model->getAppointmentFromQueue($doctor, $date, $shift);
        if (!empty($appointments)) {
            $appointemnt_id = array_column($appointments, "appointment_id");
            $this->onlineappointment_model->deleteQueue($appointemnt_id);
        }
    }

    public function globalShift()
    {
        if (!$this->rbac->hasPrivilege('online_appointment_shift', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/onlineappointment/globalshift');
        $this->session->set_userdata('sub_menu', 'admin/onlineappointment');
        
        if($this->time_format){
            $shift         = $this->onlineappointment_model->globalShiftlist();
        }else{
            $shift         = $this->onlineappointment_model->globalShift();
        }
        
        $data["shift"] = $shift;
        $this->load->view('layout/header');
        $this->load->view('admin/onlineappointment/globalShift', $data);
        $this->load->view('layout/footer');
    }

    public function getGlobalShift($id)
    {
        $shift = $this->onlineappointment_model->getGlobalShift($id);
        echo json_encode($shift);
    }

    public function addGlobalShift()
    {
        if (!$this->rbac->hasPrivilege('online_appointment_shift', 'can_add')) {
            access_denied();
        }
        $data = array();
        $this->form_validation->set_rules("name", $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("time_from", $this->lang->line('time_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("time_to", $this->lang->line('time_to'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg  = array("name" => form_error('name'), "time_from" => form_error('time_from'), "time_to" => form_error('time_to'));
            $data = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $name      = $this->input->post("name");
            $time_from = date("H:i:s", strtotime($this->input->post("time_from")));
            $time_to   = date("H:i:s", strtotime($this->input->post("time_to")));
            if ($time_from < $time_to) {
                $shift = array(
                    "name"       => $name,
                    "start_time" => $time_from,
                    "end_time"   => $time_to,
                );
                $shift = $this->security->xss_clean($shift);
                $this->onlineappointment_model->addGlobalShift($shift);
                $data = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $data = array('status' => 'invalid', 'error' => '', 'message' => $this->lang->line('time_from_should_be_greater_then_time_to'));
            }
        }
        echo json_encode($data);
    }

    public function updateGlobalShift()
    {
        if (!$this->rbac->hasPrivilege('online_appointment_shift', 'can_edit')) {
            access_denied();
        }
        $data  = array();
        $shift = array();
        $this->form_validation->set_rules("name", $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("time_from", $this->lang->line('time_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules("time_to", $this->lang->line('time_to'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg  = array("name" => form_error('name'));
            $data = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $time_from = date("H:i:s", strtotime($this->input->post("time_from")));
            $time_to   = date("H:i:s", strtotime($this->input->post("time_to")));
            if ($time_from < $time_to) {
                $shift = array(
                    "id"         => $this->input->post('shiftid'),
                    "name"       => $this->input->post('name'),
                    "start_time" => $time_from,
                    "end_time"   => $time_to,
                );
                $shift = $this->security->xss_clean($shift);
                $this->onlineappointment_model->updateGlobalShift($shift);
                $data = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $data = array('status' => 'invalid', 'error' => '', 'message' => $this->lang->line('time_from_should_be_greater_then_time_to'));
            }
        }
        echo json_encode($data);

    }

    public function doctorGlobalShift()
    {
        if (!$this->rbac->hasPrivilege('online_appointment_doctor_shift', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/onlineappointment/doctorglobalshift');
        $this->session->set_userdata('sub_menu', 'admin/onlineappointment');
        $shift = $this->onlineappointment_model->globalDoctorShift();
        foreach ($shift as $shift_key => $shift_value) {
            $shift[$shift_key]["doctor_shift"] = $this->onlineappointment_model->getGlobalDoctorShift($shift_value["id"]);
        }
        $data['shift']        = $shift;
        $doctors              = $this->staff_model->getStaffbyrole(3);
        $data['doctor']       = $doctors;
        $global_shift         = $this->onlineappointment_model->globalShift();
        $data["global_shift"] = $global_shift;
        $this->load->view('layout/header');
        $this->load->view('admin/onlineappointment/doctorGlobalShift', $data);
        $this->load->view('layout/footer');
    }

    public function getDoctorGlobalShfit($id)
    {
        $shift = $this->onlineappointment_model->getDoctorGlobalShift($id);
        echo json_encode($shift);
    }

    public function getGlobalDoctorShifts($doctor_id)
    {
        $shift = $this->onlineappointment_model->getGlobalDoctorShifts($doctor_id);
        echo json_encode($shift);
    }

    public function editDoctorGlobalShfit()
    {
        if (!$this->rbac->hasPrivilege('online_appointment_doctor_shift', 'can_edit')) {
            access_denied();
        }
        $doctor_id    = $this->input->post("doctor_id");
        $shift_id     = $this->input->post("shift_id");
        $status       = $this->input->post("status");
        $insert_array = array();
        $delete_array = array();
        if ($status == 1) {
            $insert_array = array(
                "staff_id"        => $doctor_id,
                "global_shift_id" => $shift_id,
            );
        } elseif ($status == 0) {
            $delete_array = array(
                "staff_id"        => $doctor_id,
                "global_shift_id" => $shift_id,
            );
        }
        $insert_array = $this->security->xss_clean($insert_array);
        $this->onlineappointment_model->editDoctorGlobalShift($insert_array, $delete_array);
        echo json_encode(array("status" => "success", "message" => $this->lang->line('doctor_shift_updated_successfully')));
    }

    public function doctorShiftById()
    {
        $doctor_id = $this->input->post("doctor_id");
        $shift     = $this->onlineappointment_model->doctorShiftById($doctor_id);
        echo json_encode($shift);
    }

    public function sortQueue()
    {
        if (!$this->rbac->hasPrivilege('patient_queue', 'can_edit')) {
            access_denied();
        }
        $position  = $this->input->post("position");
        $queueData = array();
        $data      = array();
        $i         = 1;
        foreach ($position as $position_key => $position_value) {
            $data = array(
                "id"       => $position_value,
                "position" => $i,
            );
            array_push($queueData, $data);
            $i++;
        }
        if ($this->onlineappointment_model->updateQueue($queueData)) {
            echo json_encode(array("status" => "success", "message" => $this->lang->line("success_message")));
        } else {
            echo json_encode(array("status" => "error", "message" => $this->lang->line("no_change_was_made")));
        }
    }

    public function getShift()
    {
        $dates        = $this->input->post("date");
        $date         = $this->customlib->dateFormatToYYYYMMDD($dates);
        $doctor       = $this->input->post("doctor");
        $global_shift = $this->input->post("global_shift");
        $day          = date("l", strtotime($date));        
        $getDoctorGlobalShiftId = $this->onlineappointment_model->getDoctorGlobalShiftId($doctor, $global_shift);        
        $shift        = $this->onlineappointment_model->getShiftdata($doctor, $day, $getDoctorGlobalShiftId['id']);
       
        echo json_encode($shift);
    }

    public function getShiftById(){
        $shift_id = $this->input->post("id");
        $date = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post("date"));
        $shift = $this->onlineappointment_model->getShiftById($shift_id);        
        $end_time = date("Y-m-d",strtotime($date))." ".$shift['end_time'];
        $end_time = date("Y-m-d H:i:s" ,strtotime($end_time));
        $current_time = date("Y-m-d H:i:s");
        if($current_time>$end_time){
            echo json_encode(array("status" => 1));
        }else{
            echo json_encode(array("status" => 0));
        }
    }

    public function deleteglobalshift($id)
    {
        if (!$this->rbac->hasPrivilege('online_appointment_slot', 'can_delete')) {
            access_denied();
        }

        $this->onlineappointment_model->deleteGlobalShift($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }
}
