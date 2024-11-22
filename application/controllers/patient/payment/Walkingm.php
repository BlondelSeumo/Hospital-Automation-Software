<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Walkingm extends Patient_Controller
{
    public $pay_method = "";
    public $amount     = 0;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('Customlib');
        $this->load->library('walkingm_lib');
        $this->load->model("patient_model");
        $this->patient_data   = $this->session->userdata('patient');
        $this->payment_method = $this->paymentsetting_model->get();
        $this->pay_method     = $this->paymentsetting_model->getActiveMethod();
        $this->marital_status = $this->config->item('marital_status');
        $this->payment_mode   = $this->config->item('payment_mode');
        $this->blood_group    = $this->config->item('bloodgroup');
        $this->setting        = $this->setting_model->get();
    }

    public function index()
    {
        $setting             = $this->setting[0];
        $data                = array();
        $id                  = $this->patient_data['patient_id'];
        $data["id"]          = $id;
        $data['productinfo'] = $this->lang->line('online_payment');
        if ($this->session->has_userdata('payment_data')) {
            $payment_data                = $this->session->userdata('payment_data');
            $api_publishable_key         = ($this->pay_method->api_publishable_key);
            $api_secret_key              = ($this->pay_method->api_secret_key);
            $data['api_publishable_key'] = $api_publishable_key;
            $data['api_secret_key']      = $api_secret_key;
            $data['case_reference_id']   = $payment_data['case_reference_id'];
            $data['amount']              = $payment_data['deposit_amount'];
            $data["payment_type"]        = $payment_data['payment_for'];
            $data['currency']            = $setting['currency'];
            $data['hospital_name']       = $setting['name'];
            $data['image']               = $setting['image'];
            $data["patient_data"]        = $this->patient_model->patientDetails($id);
            $this->load->view("layout/patient/header");
            $this->load->view("patient/payment/walkingm/index", $data);
            $this->load->view("layout/patient/footer");
        }
    }

    public function pay()
    {
        $this->form_validation->set_rules('email', $this->lang->line('walkingm_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('walkingm_password'), 'trim|required|xss_clean');
        $params = $this->session->userdata('params');
        if ($this->form_validation->run() == false) {
            $setting             = $this->setting[0];
            $data                = array();
            $id                  = $this->patient_data['patient_id'];
            $data["id"]          = $id;
            $data['productinfo'] = $this->lang->line('online_payment');
            if ($this->session->has_userdata('payment_data')) {
                $payment_data                = $this->session->userdata('payment_data');
                $api_publishable_key         = ($this->pay_method->api_publishable_key);
                $api_secret_key              = ($this->pay_method->api_secret_key);
                $data['api_publishable_key'] = $api_publishable_key;
                $data['api_secret_key']      = $api_secret_key;
                $data['case_reference_id']   = $payment_data['case_reference_id'];
                $data['amount']              = $payment_data['deposit_amount'];
                $data["payment_type"]        = $payment_data['payment_for'];
                $data['currency']            = $setting['currency'];
                $data['hospital_name']       = $setting['name'];
                $data['image']               = $setting['image'];
                $this->load->view("layout/patient/header");
                $this->load->view("patient/payment/walkingm/index", $data);
                $this->load->view("layout/patient/footer");
            }
        } else {
            $payment_data                = $this->session->userdata('payment_data');
            $setting                     = $this->setting[0];
            $payment_array['payer']      = "Walkingm";
            $payment_array['amount']     = $payment_data['deposit_amount'];
            $payment_array['currency']   = $setting["currency"];
            $payment_array['successUrl'] = base_url() . "patient/payment/walkingm/success";
            $payment_array['cancelUrl']  = base_url() . "patient/payment/walkingm/cancel";
            $response                    = $this->walkingm_lib->walkingm_login($_POST['email'], $_POST['password'], $payment_array);

            if ($response != "") {
                $data                = array();
                $id                  = $this->patient_data['patient_id'];
                $data["id"]          = $id;
                $data['productinfo'] = $this->lang->line('online_payment');
                if ($this->session->has_userdata('payment_data')) {
                    $payment_data                = $this->session->userdata('payment_data');
                    $api_publishable_key         = ($this->pay_method->api_publishable_key);
                    $api_secret_key              = ($this->pay_method->api_secret_key);
                    $data['api_publishable_key'] = $api_publishable_key;
                    $data['api_secret_key']      = $api_secret_key;
                    $data['case_reference_id']   = $payment_data['case_reference_id'];
                    $data['amount']              = $payment_data['deposit_amount'];
                    $data["payment_type"]        = $payment_data['payment_for'];
                    $data['currency']            = $setting['currency'];
                    $data['hospital_name']       = $setting['name'];
                    $data['image']               = $setting['image'];
                    $data['api_error']           = $response;
                    $this->load->view("layout/patient/header");
                    $this->load->view("patient/payment/walkingm/index", $data);
                    $this->load->view("layout/patient/footer");
                }
            }
        }
    }

    public function success()
    {
        $data             = array();
        $response         = base64_decode($_SERVER["QUERY_STRING"]);
        $payment_response = json_decode($response);

        if ($response != '' && $payment_response->status = 200) {
            $txn_id       = $payment_response->transaction_id;
            $payment_data = $this->session->userdata('payment_data');

            $save_record = array(
                'case_reference_id' => $payment_data["case_reference_id"],
                'type'              => "payment",
                'amount'            => $payment_data['deposit_amount'],
                'payment_mode'      => 'Online',
                'payment_date'      => date('Y-m-d H:i:s'),
                'note'              => "Online fees deposit through Walkingm TXN ID: " . $txn_id,
                'patient_id'        => $this->patient_data['patient_id'],
            );
            if ($payment_data['payment_for'] == "opd") {
                $save_record["opd_id"] = $payment_data['id'];
            } elseif ($payment_data['payment_for'] == "ipd") {
                $save_record["ipd_id"] = $payment_data['id'];
            } elseif ($payment_data['payment_for'] == "pharmacy") {
                $save_record["pharmacy_bill_basic_id"] = $payment_data['id'];
            } elseif ($payment_data['payment_for'] == "pathology") {
                $save_record["pathology_billing_id"] = $payment_data['id'];
            } elseif ($payment_data['payment_for'] == "radiology") {
                $save_record["radiology_billing_id"] = $payment_data['id'];
            } elseif ($payment_data['payment_for'] == "blood_bank") {
                $save_record["blood_donor_cycle_id"] = $payment_data["donor_cycle_id"];
                $save_record["blood_issue_id"]       = $payment_data['id'];
            } elseif ($payment_data['payment_for'] == "ambulance") {
                $save_record["ambulance_call_id"] = $payment_data['id'];
            }
            $insert_id = $this->payment_model->insertOnlinePaymentInTransactions($save_record);

            redirect(base_url("patient/pay/successinvoice/"));
        } else {
            redirect(base_url('patient/pay/paymentfailed'));
        }
    }

    public function cancel()
    {
        redirect(base_url("patient/pay/paymentfailed"));
    }

}
