<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Referral extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('referral_category_model');
        $this->load->model('referral_person_model');
        $this->load->model('referral_commission_model');
        $this->load->model('referral_payment_model');
        $this->load->model('patient_model');
        $this->load->helper('customfield_helper');
        $this->load->library('datatables');
        $this->load->helper('custom');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function category()
    {
        if (!$this->rbac->hasPrivilege('referral_category', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/referral/category');
        $this->session->set_userdata('sub_menu', 'admin/referral/commission');
        $data['category'] = $this->referral_category_model->get_category();
        $this->load->view('layout/header');
        $this->load->view('admin/referral/category', $data);
        $this->load->view('layout/footer');
    }

    public function person()
    {
        if (!$this->rbac->hasPrivilege('referral_person', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'referral_payment');
        $data['category'] = $this->referral_category_model->get_category();
        $data['person']   = $this->referral_person_model->get_person();
        $data['type']     = $this->referral_category_model->get_type();
        $this->load->view('layout/header');
        $this->load->view('admin/referral/person', $data);
        $this->load->view('layout/footer');
    }

    public function commission()
    {        
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/referral/commission');
        $this->session->set_userdata('sub_menu', 'admin/referral/commission');
        $data["commission"] = $this->referral_commission_model->get_commission();
        $data['category']   = $this->referral_category_model->get_category();
        $data['type']       = $this->referral_category_model->get_type();
        $this->load->view('layout/header');
        $this->load->view('admin/referral/commission', $data);
        $this->load->view('layout/footer');
    }

    public function payment()
    {
        if (!$this->rbac->hasPrivilege('referral_payment', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'referral_payment');
        $data["patients"] = $this->patient_model->getPatientListall();
        $data['type']     = $this->referral_category_model->get_type();
        $data['person']   = $this->referral_person_model->get_person();
        $data['payment']  = $this->referral_payment_model->get_payment();
        $this->load->view('layout/header');
        $this->load->view('admin/referral/payment', $data);
        $this->load->view('layout/footer');
    }

    public function report()
    {
        if (!$this->rbac->hasPrivilege('referral_payment', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/finance');
        $this->session->set_userdata('subsub_menu', 'reports/referral/report');

        $data["patients"] = $this->patient_model->getPatientListall();
        $data['type']     = $this->referral_category_model->get_type();
        $data['person']   = $this->referral_person_model->get_person();
        $this->load->view('layout/header');
        $this->load->view('admin/referral/report', $data);
        $this->load->view('layout/footer');
    }

    public function checkvalidation()
    {
        $param = array(
            'payee'         => $this->input->post('payee'),
            'patient_type'  => $this->input->post('patient_type'),
            'patient'       => $this->input->post('patient'),
            'date'          => $this->input->post('date'),
        );

        $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        echo json_encode($json_array);
    }

    public function referral_report()
    {
        $payee        = $this->input->post('payee');
        $patient_type = $this->input->post('patient_type');
        $patient      = $this->input->post('patient');
     
        $date = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date'));       
        
        $reportdata   = $this->report_model->referralRecord($payee, $patient_type, $patient, $date);    
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        
        $reportdata   = json_decode($reportdata);
        $dt_data      = array();
        $total_bill   = 0;
        $total_amount = 0;
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $total_bill += $value->bill_amount;
                $total_amount += $value->amount;

                $row       = array();
                $row[]     = $value->name;
                $row[]     = composePatientName($value->patient_name, $value->patient_id);
                $row[]     = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->customlib->getHospitalTimeFormat());  
                $row[]     = $value->prefix . $value->billing_id;                
                $row[]     = $value->percentage;
                $row[]     = $value->bill_amount;
                $row[]     = $value->amount;
                $dt_data[] = $row;
            }

            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_bill, 2, '.', '')) . "<br/>";
            
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_amount, 2, '.', '')) . "<br/>";

            $dt_data[] = $footer_row;
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
