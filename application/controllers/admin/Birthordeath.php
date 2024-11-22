<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Birthordeath extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->config->load("image_valid");
        $this->load->library("datatables");
        $this->load->library('form_validation');
        $this->load->library('Customlib');
        $this->load->library('system_notification');
        $this->load->helper('customfield_helper');
        $this->search_type = $this->config->item('search_type');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'birthordeath');
        $this->session->set_userdata('sub_menu', 'birthordeath/index');
        $this->session->set_userdata('subsub_menu', ''); 
        
        $this->load->helper('customfield_helper');
        $data['fields']         = $this->customfield_model->get_custom_fields('birth_report', 1);
        $patients               = $this->patient_model->getchildMother();
        $data["patients"]       = $patients;
        $data["disable_option"] = false;
        $this->load->view("layout/header");
        $this->load->view("admin/birthordeath/birthReport", $data);
        $this->load->view("layout/footer");
    }

    public function getpatientBycaseId($case_reference_id)
    {
        $patient = $this->patient_model->getDetailsByCaseId($case_reference_id);
        $status = 0;

        if (!empty($patient['patient_id'])) {

            if ($patient['gender'] == 'Female') {
                $status        = 1;
                $patient_id    = $patient['patient_id'];
                $patient_name  = composePatientName($patient['patient_name'], $patient_id);
                $guardian_name = $patient['guardian_name'];
                $message       = $this->lang->line("patient_is_femal");
            } else {
                $status        = 1;
                $patient_id    = $patient['patient_id'];
                $patient_name  = composePatientName($patient['patient_name'], $patient_id);
                $guardian_name = $patient['guardian_name'];
                $message       = $this->lang->line("patient_is_male");
            }
        } else {
            $status        = 0;
            $patient_id    = 0;
            $patient_name  = "";
            $guardian_name = "";
            $message       = $this->lang->line("patient_not_found");
        }

        echo json_encode(array('status' => $status, 'patient_id' => $patient_id, 'patient_name' => $patient_name, 'gender' => $patient['gender'], 'message' => $message, 'guardian_name' => $guardian_name));
    }

    public function getdeathpatientBycaseId($case_reference_id)
    {
        $patient = $this->patient_model->getDetailsByCaseId($case_reference_id);
        $status  = 0;

        if (!empty($patient['patient_id'])) {

            $status        = 1;
            $patient_id    = $patient['patient_id'];
            $patient_name  = composePatientName($patient['patient_name'], $patient_id);
            $guardian_name = $patient['guardian_name'];
            $message       = "";

        } else {
            $status        = 0;
            $patient_id    = 0;
            $patient_name  = "";
            $guardian_name = "";
            $message       = $this->lang->line("patient_not_found");
        }

        echo json_encode(array('status' => $status, 'patient_id' => $patient_id, 'patient_name' => $patient_name, 'gender' => $patient['gender'], 'message' => $message, 'guardian_name' => $guardian_name));
    }

    public function getbirthDatatable()
    {
        $dt_response = $this->birthordeath_model->getAllbirthRecord();
        $fields      = $this->customfield_model->get_custom_fields('birth_report', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================

                $column_first = "<div class='rowoptionview rowview-mt-19'>";
                $column_first .= "<a href='#' onclick='viewDetail(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('birth_record', 'can_edit')) {
                    $column_first .= "<a href='#' onclick='getRecord(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('birth_record', 'can_delete')) {
                    $column_first .= "<a class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='delete_bill(" . $value->id . ")' data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }
                if($value->gender){
                    $gender = $this->lang->line(strtolower($value->gender));
                }else{
                    $gender = '';
                }

                $column_first .= "</div'>";
                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('birth_record_reference_no') . $value->id;
                $row[] = $value->case_reference_id;
                $row[] = $value->child_name . $column_first;
                $row[] = $gender;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->birth_date, $this->time_format);
                $row[] = ($value->patient_name != "") ? $value->patient_name . " (" . $value->mother_id . ")" : "";
                $row[] = $value->father_name;

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
                $row[]     = $value->birth_report;
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

    public function getdeathDatatable()
    {

        $dt_response = $this->birthordeath_model->getAlldeathRecord();
        $fields      = $this->customfield_model->get_custom_fields('death_report', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row      = array();
                $document = "";
                //====================================

                $column_first = "<div class='rowoptionview rowview-mt-19'>";
                $column_first .= "<a href='#' onclick='viewDetail(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('death_record', 'can_edit')) {
                    $column_first .= "<a href='#' onclick='getRecord(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('death_record', 'can_delete')) {
                    $column_first .= "<a class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='delete_bill(" . $value->id . ")' data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                if ($value->attachment != "") {
                    $column_first .= "<a class='btn btn-default btn-xs' data-toggle='tooltip' title='' href=" . site_url('admin/birthordeath/download_deathrecord/' . $value->id) . " data-original-title='" . $this->lang->line('attachment') . "'><i class='fa fa-download'></i></a>";
                }
                
                if($value->gender){
                    $gender =   $this->lang->line(strtolower($value->gender));
                }else{
                    $gender =   '';
                }

                $column_first .= "</div'>";
                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('death_record_reference_no') . $value->id;
                $row[] = $value->case_reference_id;
                $row[] = $value->patient_name . " (" . $value->patientid . ")" . $column_first;
                $row[] = $value->guardian_name;
                $row[] = $gender;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->death_date, $this->time_format);

                //==============================

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

                $row[]     = $value->death_report;
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

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_edit')) {
            access_denied();
        }
        $id = $this->input->get("id");
        $this->load->helper('customfield_helper');
        $birthrecord                        = $this->birthordeath_model->getDetails($id);
        $birthrecord["birth_date"]          = $this->customlib->YYYYMMDDHisTodateFormat($birthrecord['birth_date'], $this->time_format);        
        $birthrecord['custom_fields_value'] = display_custom_fields('birth_report', $id);        
        $cutom_fields_data                  = get_custom_table_values($id, 'birth_report');
        $birthrecord['field_data']          = $cutom_fields_data;
        echo json_encode($birthrecord);
    }

    public function getBirthdata()
    {
        $id = $this->input->post("id");
        $this->load->helper('customfield_helper');
        $custom_fields_data        = get_custom_table_values($id, 'birth_report');
        $birthrecord               = $this->birthordeath_model->getDetails($id);
        $birthrecord["birth_date"] = $this->customlib->YYYYMMDDHisTodateFormat($birthrecord['birth_date'], $this->time_format);
        $birthrecord["gender"]      =  $this->lang->line(strtolower($birthrecord['gender']));        
        $birthrecord['field_data'] = $custom_fields_data;
        echo json_encode($birthrecord);
    }

    public function getBirthprintDetails($id)
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_view')) {
            access_denied();
        }
        $print_details         = $this->printing_model->get('', 'birth');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $data['fields'] = $this->customfield_model->get_custom_fields('birth_report', '', 1, '');
        $result         = $this->birthordeath_model->getDetails($id);
        $data['result'] = $result;

        $this->load->view('admin/birthordeath/printBirth', $data);
    }

    public function getDeathprintDetails($id)
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_view')) {
            access_denied();
        }

        $print_details         = $this->printing_model->get('', 'death');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }
        $data["prefix"] = $this->customlib->getSessionPrefixByType('death_record_reference_no');
        $data['fields'] = $this->customfield_model->get_custom_fields('death_report', '', 1);
        $result         = $this->birthordeath_model->getDeDetails($id);
        $data['result'] = $result;
        $this->load->view('admin/birthordeath/printDeath', $data);
    }

    public function deathreport()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/birth_death');
        $this->session->set_userdata('subsub_menu', 'admin/birthordeath/deathreport'); 
        
        $custom_fields = $this->customfield_model->get_custom_fields('death_report', '', '', 1);

        $data["searchlist"] = $this->search_type;
        $data["fields"]     = $custom_fields;
        $data['gender']     = $this->customlib->getGender_Patient();

        $this->load->view('layout/header');
        $this->load->view('admin/deathreport/deathreport', $data);
        $this->load->view('layout/footer');
    }

    public function birthreport()
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/birth_death');
        $this->session->set_userdata('subsub_menu', 'admin/birthordeath/birthreport');       
        
        $custom_fields      = $this->customfield_model->get_custom_fields('birth_report', '', '', 1);
        $data["searchlist"] = $this->search_type;
        $data["fields"]     = $custom_fields;
        $data["genderlist"] = $this->customlib->getGender_Patient();
        $this->load->view('layout/header');
        $this->load->view('admin/birthreport/birthreport', $data);
        $this->load->view('layout/footer');
    }

    public function checkvalidation()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type' => form_error('search_type'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type' => $this->input->post('search_type'),
                'date_from'   => $this->input->post('date_from'),
                'date_to'     => $this->input->post('date_to'),
                'gender'      => $this->input->post('gender'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function birthreports()
    {
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $gender                = $this->input->post('gender');
        $start_date            = '';
        $end_date              = '';
        $fields                = $this->customfield_model->get_custom_fields('birth_report', '', '', 1);
        if ($search['search_type'] == 'period') {

            $start_date = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);

        } else {

            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }

            $start_date = $dates['from_date'];
            $end_date   = $dates['to_date'];

        }

        $reportdata = $this->report_model->birthRecord($start_date, $end_date, $gender);

        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $row   = array();
                $row[] = $this->customlib->getSessionPrefixByType('birth_record_reference_no') . $value->id;
                $row[] = $value->case_reference_id;
                $row[] = $value->child_name;
                $row[] = $this->lang->line(strtolower($value->gender));
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->birth_date, $this->time_format);
                $row[] = $value->weight;
                $row[] = composePatientName($value->patient_name, $value->mother_id);
                $row[] = $value->father_name;
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
                $row[]     = $value->birth_report;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function deathreports()
    {
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $start_date            = '';
        $end_date              = '';
        $gender                = $this->input->post('gender');
        $fields                = $this->customfield_model->get_custom_fields('death_report', '', '', 1);
        if ($search['search_type'] == 'period') {
            $start_date = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);
        } else {
            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }
            $start_date = $dates['from_date'];
            $end_date   = $dates['to_date'];
        }

        $reportdata = $this->report_model->deathRecord($start_date, $end_date, $gender);
        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $row = array();

                $row[] = $this->customlib->getSessionPrefixByType('death_record_reference_no') . $value->id;
                $row[] = $value->case_reference_id;
                $row[] = $value->guardian_name;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->death_date, $this->time_format);
                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = $this->lang->line(strtolower($value->gender));
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
                $row[] = $value->death_report;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getDeathdata()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_view')) {
            access_denied();
        }
        $id = $this->input->post("id");
        $this->load->helper('customfield_helper');
        $cutom_fields_data         = get_custom_table_values($id, 'death_report');
        $deathrecord               = $this->birthordeath_model->getDeDetails($id);
        $deathrecord["prefix"]     = $this->customlib->getSessionPrefixByType('death_record_reference_no');
        $deathrecord["death_date"] = $this->customlib->YYYYMMDDHisTodateFormat($deathrecord['death_date'], $this->time_format);        
        $deathrecord["gender"] = $this->lang->line(strtolower($deathrecord['gender']));        
        $deathrecord['field_data'] = $cutom_fields_data;
        echo json_encode($deathrecord);
    }

    public function editDeath()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_view')) {
            access_denied();
        }
        $id = $this->input->post("id");
        $this->load->helper('customfield_helper');
        $deathrecord = $this->birthordeath_model->getDeDetails($id);
        $deathrecord["death_date"]          = $this->customlib->YYYYMMDDHisTodateFormat($deathrecord['death_date'], $this->time_format);
        $deathrecord['custom_fields_value'] = display_custom_fields('death_report', $id);
        $cutom_fields_data                  = get_custom_table_values($id, 'death_report');
        $deathrecord['field_data']          = $cutom_fields_data;

        echo json_encode($deathrecord);
    }

    public function death()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'birthordeath');
        $this->session->set_userdata('sub_menu', 'birthordeath/death');
        $this->session->set_userdata('subsub_menu', '');        

        $patients         = $this->patient_model->getPatientListall();
        $data["patients"] = $patients;
        $data['fields']   = $this->customfield_model->get_custom_fields('death_report', 1);
        $this->load->view("layout/header");
        $this->load->view("admin/birthordeath/deathReport", $data);
        $this->load->view("layout/footer");
    }

    public function addDeathdata()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('patient', $this->lang->line('patient_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('case_id', $this->lang->line('case_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('death_date', $this->lang->line('death_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_name', $this->lang->line('guardian_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        $custom_fields = $this->customfield_model->getByBelong('death_report');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[death_report][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient'       => form_error('patient'),
                'case_id'       => form_error('case_id'),
                'death_date'    => form_error('death_date'),
                'guardian_name' => form_error('guardian_name'),
                'document'      => form_error('document'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[death_report][" . $custom_fields_id . "]"] = form_error("custom_fields[death_report][" . $custom_fields_id . "]");
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

            $custom_field_post  = $this->input->post("custom_fields[death_report]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[death_report][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
            $deathdate         = $this->input->post('death_date');
            $death_date        = $this->customlib->dateFormatToYYYYMMDDHis($deathdate, $this->time_format);
            $case_reference_id = $this->input->post('case_id');
            if (empty($case_reference_id)) {
                $case_reference_id = null;
            }
            $death_data = array(
                'patient_id'        => $this->input->post('patient'),
                'guardian_name'     => $this->input->post('guardian_name'),
                'case_reference_id' => $case_reference_id,
                'death_date'        => $death_date,
                'death_report'      => $this->input->post('death_report'),
                'is_active'         => 'yes',
            );
            $insert_id = $this->birthordeath_model->addDeathdata($death_data);
            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $insert_id);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/death_image/" . $attachment);
                $data_img = array('id' => $insert_id, 'attachment' => $attachment, 'attachment_name' => $attachment_name);
                $this->birthordeath_model->addDeathdata($data_img);
            }

            //update death status in patient table
            $patient_data = array('id' => $this->input->post('patient'), 'is_dead' => 'yes');
            $this->patient_model->add($patient_data);

            $event_data = array(
                'patient_id' => $this->input->post('patient'),
                'death_date' => $this->customlib->YYYYMMDDHisTodateFormat($death_date, $this->time_format),
                'case_id'    => $case_reference_id,
            );

            $this->system_notification->send_system_notification('add_death_record', $event_data);
        }
        echo json_encode($array);
    }

    public function addBirthdata()
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_add')) {
            access_denied();
        }

        $custom_fields = $this->customfield_model->getByBelong('birth_report');

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[birth_report][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $this->form_validation->set_rules('child_name', $this->lang->line('child_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('mother', $this->lang->line('mother_name'), 'required');
        $this->form_validation->set_rules('contact', $this->lang->line('phone'), 'numeric');
        $this->form_validation->set_rules('birth_date', $this->lang->line('birth_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('weight', $this->lang->line('weight'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('first_img', $this->lang->line('image'), 'callback_handle_upload[first_img]');
        $this->form_validation->set_rules('second_img', $this->lang->line('image'), 'callback_handle_upload[second_img]');
        $this->form_validation->set_rules('child_img', $this->lang->line('image'), 'callback_handle_upload[child_img]');
        $this->form_validation->set_rules('document', $this->lang->line('image'), 'callback_handle_doc_upload[document]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'child_name' => form_error('child_name'),
                'birth_date' => form_error('birth_date'),
                'first_img'  => form_error('first_img'),
                'second_img' => form_error('second_img'),
                'child_img'  => form_error('child_img'),
                'document'   => form_error('document'),
                'mother'     => form_error('mother'),
                'child_name' => form_error('child_name'),
                'document'   => form_error('document'),
                'gender'     => form_error('gender'),
                'weight'     => form_error('weight'),
                'contact'    => form_error('contact'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[birth_report][" . $custom_fields_id . "]"] = form_error("custom_fields[birth_report][" . $custom_fields_id . "]");
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
            $custom_field_post = $this->input->post("custom_fields[birth_report]");

            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[birth_report][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            $birthdate         = $this->input->post('birth_date');
            $birth_date        = $this->customlib->dateFormatToYYYYMMDDHis($birthdate, $this->time_format);
            $ref_year          = date('Y', strtotime($birthdate));
            $case_reference_id = null;
            if ($this->input->post('case_id') != "") {
                $case_reference_id = $this->input->post('case_id');
            }
            $birth_data = array(
                'case_reference_id' => $case_reference_id,
                'child_name'        => $this->input->post('child_name'),
                'birth_date'        => $birth_date,
                'weight'            => $this->input->post('weight'),
                'patient_id'        => $this->input->post('mother_name'),
                'contact'           => $this->input->post('contact'),
                'birth_report'      => $this->input->post('birth_report'),
                'father_name'       => $this->input->post('father_name'),
                'gender'            => $this->input->post('gender'),
                'address'           => $this->input->post('address'),
                'is_active'         => 'yes',
            );
            $insert_id = $this->birthordeath_model->addBirthdata($birth_data);
            if ($insert_id) {

                if (!empty($custom_value_array)) {
                    $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                }                

                if (isset($_FILES["first_img"]) && !empty($_FILES['first_img']['name'])) {
                    $uploaddir = './uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo    = pathinfo($_FILES["first_img"]["name"]);
                    $first_title = 'mother_pic';
                    $filename    = "mother_pic" . $insert_id . '.' . $fileInfo['extension'];
                    $img_name    = $uploaddir . $filename;
                    $mother_pic  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    move_uploaded_file($_FILES["first_img"]["tmp_name"], $img_name);
                } else {
                    $mother_pic = "uploads/patient_images/no_image.png";
                }

                if (isset($_FILES["second_img"]) && !empty($_FILES['second_img']['name'])) {
                    $uploaddir = './uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo    = pathinfo($_FILES["second_img"]["name"]);
                    $first_title = 'father_pic';
                    $filename    = "father_pic" . $insert_id . '.' . $fileInfo['extension'];
                    $img_name    = $uploaddir . $filename;
                    $father_pic  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    move_uploaded_file($_FILES["second_img"]["tmp_name"], $img_name);
                } else {
                    $father_pic = "uploads/patient_images/no_image.png";
                }

                if (isset($_FILES["child_img"]) && !empty($_FILES['child_img']['name'])) {
                    $uploaddir = './uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo    = pathinfo($_FILES["child_img"]["name"]);
                    $first_title = 'child_img';
                    $filename    = "child_img" . $insert_id . '.' . $fileInfo['extension'];
                    $img_name    = $uploaddir . $filename;
                    $child_pic   = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    move_uploaded_file($_FILES["child_img"]["tmp_name"], $img_name);
                } else {
                    $child_pic = "uploads/patient_images/no_image.png";
                }

                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $uploaddir = './uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo    = pathinfo($_FILES["document"]["name"]);
                    $first_title = 'document';
                    $filename    = "document" . $insert_id . '.' . $fileInfo['extension'];
                    $img_name    = $uploaddir . $filename;
                    $document    = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    move_uploaded_file($_FILES["document"]["tmp_name"], $img_name);
                } else {
                    $document = "";
                }

                $data_img = array('id' => $insert_id, 'mother_pic' => $mother_pic, 'father_pic' => $father_pic, 'document' => $document, 'child_pic' => $child_pic);
                $this->birthordeath_model->addBirthdata($data_img);
            }

            $event_data = array(
                'mother_id'  => $this->input->post('mother_name'),
                'child_name' => $this->input->post('child_name'),
                'birth_date' => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('birth_date'), $this->customlib->getHospitalTimeFormat()),
                'case_id'    => $this->input->post('case_id'),
            );

            $this->system_notification->send_system_notification('add_birth_record', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function download($file)
    {
        $this->load->helper('download');
        $filepath = base_url() . $file . "/birth_image/" . $this->uri->segment(6) . "/" . $this->uri->segment(7);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(7);
        force_download($name, $data);
    }

    public function download_deathrecord($id)
    {

        $death = $this->birthordeath_model->getDeDetails($id);
        $this->load->helper('download');
        $filepath    = "./uploads/death_image/" . $death['attachment'];
        $report_name = $death['attachment_name'];
        $data        = file_get_contents($filepath);
        force_download($report_name, $data);
    }

    public function image_upload()
    {
        $image_validate = $this->config->item('image_validate');
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['file']['tmp_name'])) {
                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('image_upload', 'Error While Uploading patient Image');
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('image_upload', 'Extension Error While Uploading patient Image');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('image_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('image_upload', "File Type / Extension Error Uploading Image");
                return false;
            }
            return true;
        }
        return true;
    }

    public function handle_upload($str, $var)
    {
        $image_validate = $this->config->item('image_validate');
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {
            $file_type         = $_FILES[$var]['type'];
            $file_size         = $_FILES[$var]["size"];
            $file_name         = $_FILES[$var]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'Error While Uploading Image');
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'Extension Error While Uploading Image');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', "File Type / Extension Error Uploading Image");
                return false;
            }

            return true;
        }
        return true;
    }

    public function update_birth()
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_edit')) {
            access_denied();
        }
        $custom_fields = $this->customfield_model->getByBelong('birth_report');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];

                    $this->form_validation->set_rules("custom_fields[birth_report][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');

                }
            }
        }
        $this->form_validation->set_rules('child_name', $this->lang->line('child_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('mother', $this->lang->line('mother_name'), 'required');
        $this->form_validation->set_rules('birth_date', $this->lang->line('birth_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', $this->lang->line('gender'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('weight', $this->lang->line('weight'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('mother_pic', $this->lang->line('image'), 'callback_handle_upload[mother_pic]');
        $this->form_validation->set_rules('father_pic', $this->lang->line('image'), 'callback_handle_upload[father_pic]');
        $this->form_validation->set_rules('child_img', $this->lang->line('image'), 'callback_handle_upload[child_img]');
        $this->form_validation->set_rules('document', $this->lang->line('image'), 'callback_handle_upload[document]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'child_name' => form_error('child_name'),
                'birth_date' => form_error('birth_date'),
                'mother_pic' => form_error('mother_pic'),
                'father_pic' => form_error('father_pic'),
                'child_img'  => form_error('child_img'),
                'document'   => form_error('document'),
                'mother'     => form_error('mother'),
                'gender'     => form_error('gender'),
                'weight'     => form_error('weight'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[birth_report][" . $custom_fields_id . "]"] = form_error("custom_fields[birth_report][" . $custom_fields_id . "]");
                    }
                }
                if (!empty($error_msg2)) {
                    $error_msg = array_merge($msg, $error_msg2);
                } else {
                    $error_msg = $msg;
                }
                $json_array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');
            }
        } else {
            $id = $this->input->post('id');
            $custom_fieldvalue_array = $this->input->post("custom_field_value");
            $custom_field_post       = $this->input->post("custom_fields[birth_report]");
            $ddata                   = array();
            $custom_value_array      = array();

            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[birth_report][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'birth_report');
            }
            $birthdate         = $this->input->post('birth_date');
            $birth_date        = $this->customlib->dateFormatToYYYYMMDDHis($birthdate, $this->time_format);
            $case_reference_id = $this->input->post('case_id');
            if (empty($case_reference_id)) {
                $case_reference_id = null;
            }
            $birth_data = array(
                'id'                => $id,
                'case_reference_id' => $case_reference_id,
                'child_name'        => $this->input->post('child_name'),
                'birth_date'        => $birth_date,
                'weight'            => $this->input->post('weight'),
                'patient_id'        => $this->input->post('mother_name'),
                'contact'           => $this->input->post('contact'),
                'birth_report'      => $this->input->post('birth_report'),
                'father_name'       => $this->input->post('father_name'),
                'address'           => $this->input->post('address'),
                'gender'            => $this->input->post('gender'),
                'is_active'         => 'yes',
            );
            $insert_id = $this->birthordeath_model->addBirthdata($birth_data);
            if (!empty($id)) {

                if (isset($_FILES["mother_pic"]) && !empty($_FILES['mother_pic']['name'])) {
                    $uploaddir = './uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo    = pathinfo($_FILES["mother_pic"]["name"]);
                    $first_title = 'mother_pic';
                    $filename    = "mother_pic" . $insert_id . '.' . $fileInfo['extension'];
                    $img_name    = $uploaddir . $filename;
                    $mother_pic  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    move_uploaded_file($_FILES["mother_pic"]["tmp_name"], $img_name);
                    $data_img = array('id' => $id, 'mother_pic' => $mother_pic);
                    $this->birthordeath_model->addBirthdata($data_img);
                }

                if (isset($_FILES["father_pic"]) && !empty($_FILES['father_pic']['name'])) {
                    $uploaddir = './uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo    = pathinfo($_FILES["father_pic"]["name"]);
                    $first_title = 'father_pic';
                    $filename    = "father_pic" . $insert_id . '.' . $fileInfo['extension'];
                    $img_name    = $uploaddir . $filename;
                    $father_pic  = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    move_uploaded_file($_FILES["father_pic"]["tmp_name"], $img_name);
                    $data_img = array('id' => $id, 'father_pic' => $father_pic);
                    $this->birthordeath_model->addBirthdata($data_img);
                }

                if (isset($_FILES["child_img"]) && !empty($_FILES['child_img']['name'])) {
                    $uploaddir = './uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo    = pathinfo($_FILES["child_img"]["name"]);
                    $first_title = 'child_img';
                    $filename    = "child_img" . $insert_id . '.' . $fileInfo['extension'];
                    $img_name    = $uploaddir . $filename;
                    $child_pic   = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    move_uploaded_file($_FILES["child_img"]["tmp_name"], $img_name);
                    $data_img = array('id' => $id, 'child_pic' => $child_pic);
                    $this->birthordeath_model->addBirthdata($data_img);
                }

                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $uploaddir = './uploads/birth_image/' . $insert_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo    = pathinfo($_FILES["document"]["name"]);
                    $first_title = 'document';
                    $filename    = "document" . $insert_id . '.' . $fileInfo['extension'];
                    $img_name    = $uploaddir . $filename;
                    $document    = 'uploads/birth_image/' . $insert_id . '/' . $filename;
                    move_uploaded_file($_FILES["document"]["tmp_name"], $img_name);
                    $data_img = array('id' => $id, 'document' => $document);
                    $this->birthordeath_model->addBirthdata($data_img);
                }
            }
            $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_added_successfully'));
        }
        echo json_encode($json_array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('birth_record', 'can_delete')) {
            access_denied();
        }
        $result = $this->birthordeath_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/birthordeath');
    }

    public function deletedeath($id)
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_delete')) {
            access_denied();
        }
        $result = $this->birthordeath_model->deletedeath($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/birthordeath');
    }

    public function update_death()
    {
        if (!$this->rbac->hasPrivilege('death_record', 'can_edit')) {
            access_denied();
        }
        $array         = array();
        $custom_fields = $this->customfield_model->getByBelong('death_report');
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                if ($custom_fields_value['validation']) {
                    $custom_fields_id   = $custom_fields_value['id'];
                    $custom_fields_name = $custom_fields_value['name'];

                    $this->form_validation->set_rules("custom_fields[death_report][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');

                }
            }
        }
        $patient_type = $this->customlib->getPatienttype();
        $this->form_validation->set_rules('epatient', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('case_id', $this->lang->line('case_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('guardian_name', $this->lang->line('guardian_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('death_date', $this->lang->line('death_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_image_upload');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'epatient'      => form_error('epatient'),
                'guardian_name' => form_error('guardian_name'),
                'file'          => form_error('file'),
                'case_id'       => form_error('case_id'),
                'death_date'    => form_error('death_date'),
            );

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[death_report][" . $custom_fields_id . "]"] = form_error("custom_fields[death_report][" . $custom_fields_id . "]");
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
            $id                      = $this->input->post('id');
            $custom_field_post       = $this->input->post("custom_fields[death_report]");
            $custom_fieldvalue_array = $this->input->post("custom_field_value");
            $ddata                   = array();

            if (!empty($custom_fields)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[death_report][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => $id,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
                $this->customfield_model->updateRecord($custom_value_array, $id, 'death_report');
            }

            $deathdate  = $this->input->post('death_date');
            $death_date = $this->customlib->dateFormatToYYYYMMDDHis($deathdate, $this->time_format);
            $death_data = array(
                'id'                => $id,
                'patient_id'        => $this->input->post('epatient'),
                'guardian_name'     => $this->input->post('guardian_name'),
                'death_date'        => $death_date,
                'case_reference_id' => $this->input->post('case_id'),
                'death_report'      => $this->input->post('death_report'),
                'is_active'         => 'yes',
            );
            $this->birthordeath_model->addDeathdata($death_data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));

            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/death_image/" . $attachment);
                $data_img = array('id' => $id, 'attachment' => $attachment, 'attachment_name' => $attachment_name);
                $this->birthordeath_model->addDeathdata($data_img);

            }

            //update death status in patient table
            $patient_data = array('id' => $this->input->post('patient'), 'is_dead' => 'yes');
            $this->patient_model->add($patient_data);
        }

        echo json_encode($array);
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

}