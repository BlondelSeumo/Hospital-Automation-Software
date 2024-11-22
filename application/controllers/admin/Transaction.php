<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Transaction extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('transaction_model'));
        $this->load->library("datatables");
    }

    public function printTransaction()
    {
        $print_details         = $this->printing_model->get('', 'paymentreceipt');
        $id                    = $this->input->post('id');
        $transaction           = $this->transaction_model->getTransaction($id);
        $data['transaction']   = $transaction;
        $data['print_details'] = $print_details;
        $page                  = $this->load->view('admin/transaction/_printTransaction', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function deleteByID()
    {
        $id          = $this->input->post('id');
        $transaction = $this->transaction_model->delete($id);
        $array       = array('status' => 'success', 'message' => $this->lang->line('delete_message'));
        echo json_encode($array);
    }

    public function download($trans_id)
    {
        $transaction = $this->transaction_model->getTransaction($trans_id);
        $this->load->helper('download');
        $filepath    = "./uploads/payment_document/" . $transaction->attachment;
        $report_name = $transaction->attachment_name;
        $data        = file_get_contents($filepath);
        force_download($report_name, $data);
    }

    public function transactionreport()
    {
        if (!$this->rbac->hasPrivilege('daily_transaction_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/finance');
        $this->session->set_userdata('subsub_menu', 'reports/transaction/dailytransactionreport');

        $data['title'] = 'title';
        $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date_from' => form_error('date_from'),
                'date_to'   => form_error('date_to'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date_from = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date_from'));
            $date_to   = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date_to'));

            $reportdata = $this->transaction_model->getTransactionBetweenDate($date_from, $date_to, 'payment');
            $start_date = strtotime($date_from);
            $end_date   = strtotime($date_to);
            $date_array = array();
            for ($i = $start_date; $i <= $end_date; $i += 86400) {
                $date_array[date('Y-m-d', $i)] = array('amount' => 0, 'online_transaction' => 0, 'offline_transaction' => 0, 'total_transaction' => 0);
            }

            if (!empty($reportdata)) {
                foreach ($reportdata as $key => $value) {

                    $date_array[date('Y-m-d', strtotime($value->payment_date))]['amount']            = $date_array[date('Y-m-d', strtotime($value->payment_date))]['amount'] + $value->amount;
                    $date_array[date('Y-m-d', strtotime($value->payment_date))]['total_transaction'] = $date_array[date('Y-m-d', strtotime($value->payment_date))]['total_transaction'] + 1;

                    if ($value->payment_mode == "Online") {
                        $date_array[date('Y-m-d', strtotime($value->payment_date))]['online_transaction'] = $date_array[date('Y-m-d', strtotime($value->payment_date))]['online_transaction'] + $value->amount;
                    } else {
                        $date_array[date('Y-m-d', strtotime($value->payment_date))]['offline_transaction'] = $date_array[date('Y-m-d', strtotime($value->payment_date))]['offline_transaction'] + $value->amount;
                    }
                }
            }

            $dt_data = array();
            foreach ($date_array as $dt_key => $dt_value) {
                $row                        = array();
                $row['date']                = $dt_key;
                $row['total_transaction']   = $dt_value['total_transaction'];
                $row['online_transaction']  = $dt_value['online_transaction'];
                $row['offline_transaction'] = $dt_value['offline_transaction'];
                $row['amount']              = $dt_value['amount'];
                $dt_data[]                  = $row;
            }

            $data['result'] = $dt_data;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/transaction/transactionreport', $data);
        $this->load->view('layout/footer', $data);
    } 


    public function processingtransactionreport()
    {
        if (!$this->rbac->hasPrivilege('daily_transaction_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/finance');
        $this->session->set_userdata('subsub_menu', 'reports/transaction/processingtransactionreport');

        $data['title'] = 'title';
        $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date_from' => form_error('date_from'),
                'date_to'   => form_error('date_to'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date_from = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date_from'));
            $date_to   = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date_to'));

            $reportdata = $this->transaction_model->getTransactionBetweenDate($date_from, $date_to, 'payment');
            $start_date = strtotime($date_from);
            $end_date   = strtotime($date_to);
            $date_array = array();
            for ($i = $start_date; $i <= $end_date; $i += 86400) {
                $date_array[date('Y-m-d', $i)] = array('amount' => 0, 'online_transaction' => 0, 'offline_transaction' => 0, 'total_transaction' => 0);
            }

            if (!empty($reportdata)) {
                foreach ($reportdata as $key => $value) {

                    $date_array[date('Y-m-d', strtotime($value->payment_date))]['amount']            = $date_array[date('Y-m-d', strtotime($value->payment_date))]['amount'] + $value->amount;
                    $date_array[date('Y-m-d', strtotime($value->payment_date))]['total_transaction'] = $date_array[date('Y-m-d', strtotime($value->payment_date))]['total_transaction'] + 1;

                    if ($value->payment_mode == "Online") {
                        $date_array[date('Y-m-d', strtotime($value->payment_date))]['online_transaction'] = $date_array[date('Y-m-d', strtotime($value->payment_date))]['online_transaction'] + $value->amount;
                    } else {
                        $date_array[date('Y-m-d', strtotime($value->payment_date))]['offline_transaction'] = $date_array[date('Y-m-d', strtotime($value->payment_date))]['offline_transaction'] + $value->amount;
                    }
                }
            }

            $dt_data = array();
            foreach ($date_array as $dt_key => $dt_value) {
                $row                        = array();
                $row['date']                = $dt_key;
                $row['total_transaction']   = $dt_value['total_transaction'];
                $row['online_transaction']  = $dt_value['online_transaction'];
                $row['offline_transaction'] = $dt_value['offline_transaction'];
                $row['amount']              = $dt_value['amount'];
                $dt_data[]                  = $row;
            }

            $data['result'] = $dt_data;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/transaction/processingtransactionreport', $data);
        $this->load->view('layout/footer', $data);
    } 

    public function gettransactionbydate()
    {
        if (!$this->rbac->hasPrivilege('transaction_report', 'can_view')) {
            access_denied();
        }
        $date          = $this->input->post('date');
        $data['title'] = 'title';
        $result         = $this->transaction_model->getTransactionBetweenDate($date, $date, 'payment');
        $data['result'] = $result;
        $page           = $this->load->view('admin/transaction/_gettransactionbydate', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function processingtransaction(){
        $dt_response = $this->transaction_model->getAllprocessingtransactionRecord();
        
        $dt_response = json_decode($dt_response);

        $dt_data = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                $row[] = $value->patient_name .' ('. $value->patients_id .')';
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->payment_date);
                $row[] = $value->case_reference_id;
                if(!empty($value->opd_id)){
                $row[]=$this->customlib->getSessionPrefixByType('opd_no').$value->opd_id;
                }else{
                $row[]='';
                }
                if(!empty($value->ipd_id)){
                $row[]=$this->customlib->getSessionPrefixByType('opd_no').$value->ipd_id;
                }else{
                $row[]='';
                }
                if(!empty($value->pharmacy_bill_basic_id)){
                $row[]=$this->customlib->getSessionPrefixByType('pharmacy_billing').$value->pharmacy_bill_basic_id;
                }else{
                $row[]='';
                }
                if(!empty($value->pathology_billing_id)){
                $row[]=$this->customlib->getSessionPrefixByType('pathology_billing').$value->pathology_billing_id;
                }else{
                $row[]='';
                }
                if(!empty($value->radiology_billing_id)){
                $row[]=$this->customlib->getSessionPrefixByType('radiology_billing').$value->radiology_billing_id;
                }else{
                $row[]='';
                }
                if(!empty($value->blood_donor_cycle_id)){
                $row[]=$this->customlib->getSessionPrefixByType('blood_bank_billing').$value->blood_donor_cycle_id;
                }else{
                $row[]='';
                }
                if(!empty($value->blood_issue_id)){
                $row[]=$this->customlib->getSessionPrefixByType('blood_bank_billing').$value->blood_issue_id;
                }else{
                $row[]='';
                }
                if(!empty($value->ambulance_call_id)){
                $row[]=$this->customlib->getSessionPrefixByType('ambulance_call_billing').$value->ambulance_call_id;
                }else{
                $row[]='';
                }
                if(!empty($value->appointment_id)){
                $row[]=$this->customlib->getSessionPrefixByType('appointment').$value->appointment_id;
                }else{
                $row[]='';
                }

                 $row[] = $value->amount;
                $row[] = $this->lang->line(strtolower($value->payment_mode));
               $row[] = $value->note;

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
}
