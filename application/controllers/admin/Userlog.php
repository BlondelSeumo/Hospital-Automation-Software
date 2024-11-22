<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Userlog extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->search_type = $this->config->item('search_type');
        $this->load->library('datatables');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/log');
        $this->session->set_userdata('subsub_menu', 'reports/log/userlog');
        $data["searchlist"]   = $this->search_type;
        $data["userroletype"] = $this->customlib->get_useroletype();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/userlog/userlogList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function checkvalidation()
    {
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('userroletype', $this->lang->line('user_role_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type'  => form_error('search_type'),
                'userroletype' => form_error('userroletype'),

            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'  => $this->input->post('search_type'),
                'userroletype' => $this->input->post('userroletype'),
                'date_from'    => $this->input->post('date_from'),
                'date_to'      => $this->input->post('date_to'),

            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function deleteall()
    {
        $this->userlog_model->delete();
        $return = array('status' => 1, 'msg' => $this->lang->line('data_deleted_successfully'));
        echo json_encode($return);
    }

    public function userlogreports()
    {
        $start_date             = '';
        $end_date               = '';
        $search['search_type']  = $this->input->post('search_type');
        $search['userroletype'] = $this->input->post('userroletype');
        $search['date_from'] = $this->input->post('date_from');
        $search['date_to']   = $this->input->post('date_to');

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

        $reportdata = $this->report_model->userlogreportRecord($search['userroletype'], $start_date, $end_date);

        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                if($value->role == 'patient'){ $role = ucfirst($value->role); }else{ $role = $value->role; }

                $row   = array();
                $row[] = $value->user;
                $row[] = $role;
                $row[] = $value->ipaddress;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->login_datetime, $this->time_format);
                $row[] = $value->user_agent;

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

}
