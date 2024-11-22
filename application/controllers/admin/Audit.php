<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Audit extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('audit_model');
        $this->load->library('datatables');
        $this->config->load("payroll");
        $this->search_type = $this->config->item('search_type');
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function index($offset = 0)
    {

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/log');
        $this->session->set_userdata('subsub_menu', 'reports/log/audit');
        $data['title']      = $this->lang->line('audit_trail_report');
        $data['title_list'] = $this->lang->line('audit_trail_list');
        $data["searchlist"] = $this->search_type;        
        $this->load->view('layout/header');
        $this->load->view('admin/audit/index', $data);
        $this->load->view('layout/footer');
    }

    public function getDatatable()
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
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
            }

            $start_date = $dates['from_date'];
            $end_date   = $dates['to_date'];
        }

        $audit = $this->audit_model->getAllRecord($start_date, $end_date);
        $audit = json_decode($audit);

        $dt_data = array();
        if (!empty($audit->data)) {
            foreach ($audit->data as $key => $value) {

                $date = $this->customlib->YYYYMMDDHisTodateFormat($value->time, $this->customlib->getHospitalTimeFormat());

                $row   = array();
                $row[] = $value->message;
                $row[] = $value->name;
                $row[] = $value->ip_address;
                $row[] = $value->action;
                $row[] = $value->platform;
                $row[] = $value->agent;
                $row[] = $date;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($audit->draw),
            "recordsTotal"    => intval($audit->recordsTotal),
            "recordsFiltered" => intval($audit->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function deleteall()
    {
        $this->audit_model->delete();
        $return = array('status' => 1, 'msg' => $this->lang->line('data_deleted_successfully'));
        echo json_encode($return);
    }

     public function checkvalidation()
    {

        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type'  => form_error('search_type'),

            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type'  => $this->input->post('search_type'),
                'date_from'    => $this->input->post('date_from'),
                'date_to'      => $this->input->post('date_to'),

            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

}
