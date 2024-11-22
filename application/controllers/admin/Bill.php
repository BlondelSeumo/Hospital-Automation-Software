<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bill extends Admin_Controller
{
    public $marital_status;
    public $payment_mode;
    public $yesno_condition;
    public $blood_group;
    public $opd_prefix;
    public $appointment_status;

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->config->load("image_valid");
        $this->load->library('Customlib');
        $this->load->helper('url');
        $this->marital_status     = $this->config->item('marital_status');
        $this->payment_mode       = $this->config->item('payment_mode');
        $this->yesno_condition    = $this->config->item('yesno_condition');
        $this->blood_group        = $this->config->item('bloodgroup');
        $this->notificationurl    = $this->config->item('notification_url');
        $this->opd_prefix         = $this->customlib->getSessionPrefixByType('opd_no');
        $this->appointment_status = $this->config->item('appointment_status');
        $this->load->model(array('charge_model', 'patient_model', 'appoint_priority_model', 'onlineappointment_model', 'transaction_model', 'conference_model', 'transaction_model', 'casereference_model'));

        $this->load->library('datatables');
        $this->payment_mode = $this->config->item('payment_mode');
        $this->load->model("transaction_model");
        $this->load->helper('customfield_helper');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
        $this->load->library('system_notification');
        $this->load->library('mailsmsconf');
    }

    public function index($case_id)
    {
        $this->session->set_userdata('top_menu', 'bill');
        $data["payment_mode"] = $this->payment_mode;
        $data['case_id']=$case_id;
        $this->load->view("layout/header");
        $this->load->view("admin/bill/index", $data);
        $this->load->view("layout/footer");
    } 
 
    public function dashboard()
    {
        $this->session->set_userdata('top_menu', 'bill');
        $this->form_validation->set_rules('case_id', $this->lang->line('case_id'), 'required|trim|xss_clean|numeric');
        $data['error_message']="";
        if ($this->form_validation->run() == false) {
            
        } else {
            $patient = $this->patient_model->getDetailsByCaseId($this->input->post('case_id'));
        if (!empty($patient['patient_id'])) {
            redirect('admin/bill/index/'.$this->input->post('case_id'));
        } else {
            $data['error_message']=$this->lang->line('no_record_found');
        }
            
        }

        $this->load->view("layout/header");
        $this->load->view("admin/bill/dashboard",$data);
        $this->load->view("layout/footer");
    }

    public function get()
    {
        $this->form_validation->set_rules('case_id', $this->lang->line('case_id'), 'required|trim|xss_clean|numeric');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'case_id' => form_error('case_id'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $array = array('status' => 'success', 'error' => '', 'case_id' => $_POST['case_id']);
        }
        echo json_encode($array);
    }

    public function getopd($case_id)
    {
        $charges                = $this->charge_model->getopdChargesbyCaseId($case_id);
        $data["charges_detail"] = $charges;
        $page                   = $this->load->view('admin/bill/_opd_charges', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getipd($case_id)
    {
        $charges                = $this->charge_model->getipdChargesbyCaseId($case_id);
        $data["charges_detail"] = $charges;
        $page                   = $this->load->view('admin/bill/_ipd_charges', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    } 

    public function getpharmacy($case_id)
    {
        $dt_response = $this->pharmacy_model->getAllpharmacybillByCaseIdSql($case_id);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
               
                $row            = array();
                $balance_amount = ($value->net_amount) - ($value->amt_payment-$value->amt_refund);
                //====================================
                $action = "<div class='rowoptionview'>";
                if ($balance_amount > 0) {
                    if ($this->rbac->hasPrivilege('pharmacy_billing_payment', 'can_view')) {
                        $action .= "<a href='#'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs add_pharmacypayment' data-toggle='tooltip' title='" . $this->lang->line('add_view_payments') . "' ><i class='fa fa-money'></i></a>";
                    }
                }
                $action .= "<a href='#' onclick='viewPharmacyDetail(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";

                $action .= "<div>";

                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('pharmacy_billing') . $value->id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->customlib->getHospitalTimeFormat());
                $row[] = $value->doctor_name;
				$row[] = $value->discount . " (" . $value->discount_percentage . "%)";
                $row[] = amountFormat($value->net_amount);
                $row[] = amountFormat($value->amt_payment);
                $row[] = amountFormat($value->amt_refund);
                $row[] = amountFormat($balance_amount);
                $row[] = $action;
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

    public function getpathology($case_id)
    {

        $dt_response = $this->pathology_model->getpathologybillByCaseId($case_id);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row            = array();
                $balance_amount = ($value->net_amount) - ($value->paid_amount);
                //====================================

                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='javascript:void(0)' data-loading-text='" . $this->lang->line('please_wait') . "' class='print_bill' data-toggle='tooltip' data-record-id=\"" . $value->id . "\"   data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
                $action .= "</div>";
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs view_pathology_detail' data-toggle='tooltip' title='" . $this->lang->line('view_reports') . "' ><i class='fa fa-reorder'></i></a>";
                if ($balance_amount > 0) {
                    if ($this->rbac->hasPrivilege('pathology_billing_payment', 'can_view')) {
                        $action .= "<a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs add_pathology_payment' data-toggle='tooltip' title='" . $this->lang->line('add_view_payments') . "' ><i class='fa fa-money'></i></a>";
                    }
                }
                if ($value->case_reference_id > 0) {
                    $case_id = $value->case_reference_id;
                } else {
                    $case_id = '';
                }

                $action .= "</div>";

                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('pathology_billing') . $value->id . $action;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->customlib->getHospitalTimeFormat());
                $row[] = $value->doctor_name;
                $row[] = $value->note;
                $row[] = $value->tax;
                $row[] = $value->discount.' ('.$value->discount_percentage.'%)';
                $row[] = amountFormat($value->net_amount);
                $row[] = amountFormat($value->paid_amount);
                $row[] = amountFormat($balance_amount);
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

    public function getradiology($case_id)
    {

        $dt_response = $this->radio_model->getradiologybillByCaseId($case_id);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row            = array();
                $balance_amount = ($value->net_amount) - ($value->paid_amount);
                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";

                $action .= "<a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs view_radiodetail' data-toggle='tooltip' title='" . $this->lang->line('view_reports') . "' ><i class='fa fa-reorder'></i></a>";
                if ($balance_amount > 0) {
                    if ($this->rbac->hasPrivilege('radiology_billing_payment', 'can_view')) {
                        $action .= "<a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs add_radio_payment' data-toggle='tooltip' title='" . $this->lang->line('add_view_payments') . "' ><i class='fa fa-money'></i></a>";
                    }
                }

                $action .= "</div>";

                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('radiology_billing') . $value->id . $action;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->customlib->getHospitalTimeFormat());
                $row[] = $value->doctor_name;
                $row[] = $value->note;
                $row[] = $value->discount." (".$value->discount_percentage." %)";
				
				$tax_per = ($value->tax/$value->total)*100 ;
				
                $row[] = $value->tax . " (".  $tax_per .'%)' ;
                $row[] = amountFormat($value->net_amount);
                $row[] = amountFormat($value->paid_amount);
                $row[] = amountFormat($balance_amount);
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

    public function getbloodbank($case_id)
    {
        $dt_response = $this->bloodissue_model->getbloodissueRecord($case_id);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";

                $action .= "<a href='#' onclick='printbloodbankData(" . $value->id . ")' class='btn btn-default btn-xs print_blood_issue'  data-toggle='tooltip' title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";
                if ($this->rbac->hasPrivilege('blood_bank_billing_payment', 'can_view')) {
                    $action .= "<a href='javascript:void(0)'  data-caseid='' data-module='blood_bank'  data-record-id='" . $value->id . "' data-patient-id='" . $value->patient_id . "' class='btn btn-default btn-xs add_bloodbankpayment' data-toggle='tooltip' title='" . $this->lang->line('add_view_payments') . "' ><i class='fa fa-plus'></i></a>";
                }

                $action .= "</div>";
                
                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value->id . $action;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date_of_issue, $this->time_format);

                $row[]     = composePatientName($value->patient_name,$value->patient_id);
                $row[]     = $value->blood_group;
                $row[]     = $value->gender;
                $row[]     = $value->donor_name;
                $row[]     = $value->bag_no;
                $row[]     = amountFormat($value->net_amount);
                $row[]     = amountFormat($value->paid_amount);
                $row[]     = amountFormat($value->net_amount - $value->paid_amount);
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

    public function getambulance($case_id)
    {
        $dt_response = $this->vehicle_model->getAmbulanceCallRecord($case_id);

        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";

                $action .= "<a href='#' onclick='printAmbulanceData(" . $value->id . ")'
                       class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";
                if ($this->rbac->hasPrivilege('ambulance_billing_payment', 'can_view')) {
                    $action .= "<a href='javascript:void(0)'  data-caseid='' data-module='ambulance'  data-record-id='" . $value->id . "' class='btn btn-default btn-xs add_ambulancecallpayment' data-toggle='tooltip' title='" . $this->lang->line('add_payment') . "' ><i class='fa fa-plus'></i></a>";
                }

                $action .= "</div>";
                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('ambulance_call_billing') . $value->id . $action;
                $row[] = $value->vehicle_no;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);

                if($value->discount){
					$row[]     = $value->discount." (".$value->discount_percentage." %)";
				}else{
					$row[]     = '';
				}
				
                $row[]     = amountFormat($value->net_amount);
                $row[]     = amountFormat($value->paid_amount);
                $row[]     = amountFormat($value->net_amount - $value->paid_amount);
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
    
    
    public function getDetailsByCaseId($case_id)
    {
        $patient = $this->patient_model->getDetailsByCaseId($case_id);

        if (!array_filter($patient)) {
            $data['result'] = '';
        } else {
            $data['result'] = $patient;
        }

        $data['case_id'] = $case_id;
        if (!empty($patient)) {
            $status = 1;
        } else {
            $status = 0;
        }

        $page = $this->load->view('admin/bill/_patient_details', $data, true);
        echo json_encode(array('status' => $status, 'page' => $page));
    }

    public function makepayment()
    {
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_date', $this->lang->line('payment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'trim|required|xss_clean');
        if ($_POST['payment_mode'] == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line('documents'), 'callback_handle_doc_upload[document]');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'amount'       => form_error('amount'),
                'payment_mode' => form_error('payment_mode'),
                'payment_date' => form_error('payment_date'),
                'cheque_date'  => form_error('cheque_date'),
                'cheque_no'    => form_error('cheque_no'),
                'document'     => form_error('document'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $payment_date = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post("payment_date"), $this->time_format);
            $cheque_date  = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date"));
            $amount       = $this->input->post('amount');
            $patient      = $this->patient_model->getDetailsByCaseId($this->input->post('case_reference_id'));
            $patient_id   = $patient['patient_id'];
            $module_data  = array(
                'opd'        => array('opd_id', $this->input->post('module_id')),
                'ipd'        => array('ipd_id', $this->input->post('module_id')),
                'pharmacy'   => array('pharmacy_bill_basic_id', $this->input->post('module_id')),
                'radiology'  => array('radiology_billing_id', $this->input->post('module_id')),
                'pathology'  => array('pathology_billing_id', $this->input->post('module_id')),
                'blood_bank' => array('blood_issue_id', $this->input->post('module_id')),
            );

            $data = array(
                'case_reference_id' => $this->input->post('case_reference_id'),
                'amount'            => $amount,
                'type'              => 'payment',
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note'),
                'payment_date'      => $payment_date,
                'received_by'       => $this->customlib->getLoggedInUserID(),
                'patient_id'        => $patient_id,
                'section'           => $this->input->post('module_name'),
            );

            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }
			
            $cheque_date = $this->input->post("cheque_date");
            if ($this->input->post('payment_mode') == "Cheque") {

                $data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $data['cheque_no']       = $this->input->post('cheque_no');
                $data['attachment']      = $attachment;
                $data['attachment_name'] = $attachment_name;
            }
			
            $data[$module_data[$this->input->post('module_name')][0]] = $module_data[$this->input->post('module_name')][1];
            $insert_id                                                = $this->transaction_model->add($data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
        }

        echo json_encode($array);
    }

    public function printCharge()
    {		 
        switch ($this->input->post("type")) {
            case 'opd':
                $billing_header = "opd";
                break;
            case 'ipd':
                $billing_header = "ipd";
                break;
        }
        $print_details = $this->printing_model->get('', $billing_header);
	
        $id            = $this->input->post('id');
        $charge        = array();
        $charge        = $this->charge_model->getChargeById($id);
	  
        $data['print_details'] = $print_details;
        $data['charge']        = $charge;

        $page = $this->load->view('admin/patient/_printCharge', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getpayment()
    {
        $case_id                 = $this->input->post('case_id');
        $module_type             = $this->input->post('module_type');
        $id                      = $this->input->post('id');
        $payment_details         = array();
        $payment_details         = $this->transaction_model->getPatientPaymentsByCaseId($case_id, $module_type, $id);
        $data['module']          = $module_type;
        $data['payment_details'] = $payment_details;
        $page                    = $this->load->view('admin/bill/_view_payments', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function printTransaction()
    {
        $case_id       = $this->input->post('case_id');
        $module_type   = $this->input->post('module_type');
        $id            = $this->input->post('id');
        $print_details = $this->printing_model->get('', 'paymentreceipt');
        $transaction   = $this->transaction_model->allPaymentByCaseId($case_id);

        $data['charge_details']           = array();
        $all_moduledata['bill']['result'] = array();
        if ($module_type == 'ipd_opd') {
            $data['charge_details'] = $this->transaction_model->get_ipdopdchargebycaseId($case_id);
            foreach ($transaction as $key => $value) {
                if (($value['ipd_id'] != '') && ($value['ipd_id'] != 0)) {
                    $all_moduledata['bill']['result'][] = $value;
                } elseif (($value['opd_id'] != '') && ($value['opd_id'] != 0)) {
                    $all_moduledata['bill']['result'][] = $value;
                }
            }
        }

        $data['all_paymets'] = $all_moduledata;
        $patient               = $this->patient_model->getDetailsByCaseId($case_id);
        $data['patient']       = $patient;
        $data['module_type']   = $module_type;
        $data['print_details'] = $print_details;
        $data['transaction']   = $transaction;
        $data['case_id']       = $case_id;
        $page                  = $this->load->view('admin/bill/_printTransaction', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function patient_bill()
    {
        $this->session->set_userdata('top_menu', 'bill');

        $data['pathology_bill_prefix']  = $this->customlib->getSessionPrefixByType('pathology_billing');
        $data['radiology_bill_prefix']  = $this->customlib->getSessionPrefixByType('radiology_billing');
        $data['blood_bank_bill_prefix'] = $this->customlib->getSessionPrefixByType('blood_bank_billing');
        $data['pharmacy_bill_prefix']   = $this->customlib->getSessionPrefixByType('pharmacy_billing');
        $data['transaction_prefix']     = $this->customlib->getSessionPrefixByType('transaction_id');
        $case_id                  = $this->input->post('case_reference_id');
        $data['opd_data']         = $this->charge_model->getopdChargesbyCaseId($case_id);
        $data['ipd_data']         = $this->charge_model->getipdChargesbyCaseId($case_id);
        $data['pharmacy_data']    = $this->pharmacy_model->getpharmacybillByCaseId($case_id);
        $data['radiology_data']   = $this->radio_model->getradiologyByCaseId($case_id);
        $data['pathology_data']   = $this->pathology_model->getpathologyByCaseId($case_id);
        $data['bloodissue_data']  = $this->bloodissue_model->getbloodissueByCaseId($case_id);
        $data['transaction_data'] = $this->transaction_model->getTransactionByCaseId($case_id);
        $data['refund_data']      = $this->transaction_model->getRefundByCaseId($case_id);
        $patient                  = $this->patient_model->getDetailsByCaseId($case_id);
        $data['patient']          = $patient;
        $page                     = $this->load->view('admin/bill/_patient_bill', $data, true);
        $modal_action             = "<a href='javascript:void(0);' data-case-id=" . $case_id . " class='print_bill d-inline' title=".$this->lang->line('print')."><i class='fa fa-print'></i></a>";
        echo json_encode(array('status' => 1, 'page' => $page, 'modal_action' => $modal_action));
    }

    public function print_patient_bill()
    {
        $this->session->set_userdata('top_menu', 'bill');

        $data['pathology_bill_prefix']  = $this->customlib->getSessionPrefixByType('pathology_billing');
        $data['radiology_bill_prefix']  = $this->customlib->getSessionPrefixByType('radiology_billing');
        $data['blood_bank_bill_prefix'] = $this->customlib->getSessionPrefixByType('blood_bank_billing');
        $data['pharmacy_bill_prefix']   = $this->customlib->getSessionPrefixByType('pharmacy_billing');
        $data['transaction_prefix']     = $this->customlib->getSessionPrefixByType('transaction_id');
        $case_id         = $this->input->post('case_id');
        $data['case_id'] = $case_id;

        $print_details         = $this->printing_model->get('', 'bill');
        $data['print_details'] = $print_details;

        $patient         = $this->patient_model->getDetailsByCaseId($case_id);
        $data['patient'] = $patient;

        $data['opd_data']         = $this->charge_model->getopdChargesbyCaseId($case_id);
        $data['ipd_data']         = $this->charge_model->getipdChargesbyCaseId($case_id);
        $data['pharmacy_data']    = $this->pharmacy_model->getpharmacybillByCaseId($case_id);
        $data['radiology_data']   = $this->radio_model->getradiologyByCaseId($case_id);
        $data['pathology_data']   = $this->pathology_model->getpathologyByCaseId($case_id);
        $data['bloodissue_data']  = $this->bloodissue_model->getbloodissueByCaseId($case_id);
        $data['transaction_data'] = $this->transaction_model->getTransactionByCaseId($case_id);
        $page                     = $this->load->view('admin/bill/_print_patient_bill', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function generate_bill()
    {
        $case_id               = $this->input->post('case_id');
        $module_type           = $this->input->post('module_type');
        $id                    = $this->input->post('id');
        $print_details         = $this->printing_model->get('', 'paymentreceipt');
        $patient               = $this->patient_model->getDetailsByCaseId($case_id);
        $data['patient']       = $patient;
        $data['print_details'] = $print_details;
        $data['case_id']       = $case_id;
        $transaction           = $this->transaction_model->ipdopdPaymentByCaseId($case_id);
        $data['paid_amount']   = $transaction;
        $refund                = $this->transaction_model->getopdIpdrefundbyCaseId($case_id);
        if (!empty($refund)) {
            $data['refund'] = $refund['amount'];
        } else {
            $data['refund'] = 0;
        }

        $data['charge_details'] = $this->transaction_model->get_ipdopdchargebycaseId($case_id);
        $data['module_type']    = $module_type;
        $page                   = $this->load->view('admin/bill/_generatebill', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function generate_bill_result()
    {
        $case_id                = $this->input->post('case_id');
        $module_type            = $this->input->post('module_type');
        $id                     = $this->input->post('id');
        $print_details          = $this->printing_model->get('', 'paymentreceipt');
        $patient                = $this->patient_model->getDetailsByCaseId($case_id);
        $data['patient']        = $patient;
        $data['print_details']  = $print_details;
        $data['case_id']        = $case_id;
        $transaction            = $this->transaction_model->ipdopdPaymentByCaseId($case_id);
        $data['paid_amount']    = $transaction;
        $data['charge_details'] = $this->transaction_model->get_ipdopdchargebycaseId($case_id);

        $data['module_type'] = $module_type;
        $refund              = $this->transaction_model->getopdIpdrefundbyCaseId($case_id);
        if (!empty($refund)) {
            $data['refund'] = $refund['amount'];
        } else {
            $data['refund']   = 0;
            $refund['amount'] = 0;
        }

        $data['discharge_card'] = $this->patient_model->get_dischargeCard(array('case_reference_id' => $case_id));
        $page                   = $this->load->view('admin/bill/_generatebill_result', $data, true);

        if ($data['charge_details'][0]['tax'] > 0) {
            $tax = (($data['charge_details'][0]['apply_charge'] * $data['charge_details'][0]['tax']) / 100);
        } else {
            $tax = 0;
        }

        if ($refund['amount'] > 0) {
            $due_amount = amountFormat(($data['charge_details'][0]['amount'] - $data['paid_amount']['total_pay']) - $refund['amount']);
        } else {
            $due_amount = amountFormat($data['charge_details'][0]['amount'] - $data['paid_amount']['total_pay']);
        }

        if ($data['charge_details'][0]['ipd_id'] != '') {
            $event_data = array(
                'patient_id' => $patient['patient_id'],
                'ipd_no'     => $this->customlib->getSessionPrefixByType('ipd_no') . $data['charge_details'][0]['ipd_id'],
                'case_id'    => $case_id,
                'net_amount' => $data['charge_details'][0]['apply_charge'],
                'total'      => $data['charge_details'][0]['amount'],
                'tax'        => $tax,
                'paid'       => $data['paid_amount']['total_pay'],
                'due'        => $due_amount,
            );
            $this->system_notification->send_system_notification('add_ipd_generate_bill', $event_data);
        }

        if ($data['charge_details'][0]['opd_id'] != '') {
            $event_data = array(
                'patient_id' => $patient['patient_id'],
                'opd_id'     => $this->customlib->getSessionPrefixByType('opd_no') . $data['charge_details'][0]['opd_id'],
                'case_id'    => $case_id,
                'net_amount' => $data['charge_details'][0]['apply_charge'],
                'total'      => $data['charge_details'][0]['amount'],
                'tax'        => $tax,
                'paid'       => $data['paid_amount']['total_pay'],
                'due'        => $due_amount,
            );
            $this->system_notification->send_system_notification('add_opd_generate_bill', $event_data);
        }

        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getBloodbankTransaction()
    {
        $billing_id                 = $this->input->post('id');
        $data['patient_id']         = $this->input->post('patient_id');
        $data['blood_issue_detail'] = $this->bloodissue_model->getDetail($billing_id);
        $transaction                = $this->transaction_model->bloodbankPayments($billing_id);
        $data["billing_id"]         = $billing_id;
        $data["payment_mode"]       = $this->payment_mode;
        $data['transaction']        = $transaction;
        $page                       = $this->load->view("admin/bill/_getBloodbankTransaction", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getRadiologyTransaction()
    {
        $radiology_billing_id          = $this->input->post('id');
        $radiology_transaction         = $this->transaction_model->radiologyPayments($radiology_billing_id);
        $data["radiology_billing_id"]  = $radiology_billing_id;
        $data["payment_mode"]          = $this->payment_mode;
        $data['radiology_transaction'] = $radiology_transaction;
        $page                          = $this->load->view("admin/bill/_getRadiologyTransaction", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getrefund($case_id)
    {
        $data['case_id']      = $case_id;
        $patient              = $this->patient_model->getDetailsByCaseId($case_id);
        $data['opd_id']       = $patient['opdid'];
        $data['ipd_id']       = $patient['ipdid'];
        $refund               = $this->transaction_model->getopdIpdrefundbyCaseId($case_id);
        if(!empty($refund )){
            $data['id']           = $refund['id'];        
            $data["refund"]       = $refund;
        }else{
            $data['id']           = '';        
            $data["refund"]       = '';            
        }
        $data["payment_mode"] = $this->payment_mode;
        $page                 = $this->load->view('admin/bill/_opdipd_refund', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));

    }

    public function add_refund()
    {       
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_date', $this->lang->line('payment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'trim|required|xss_clean');
        if ($_POST['payment_mode'] == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'amount'       => form_error('amount'),
                'payment_mode' => form_error('payment_mode'),
                'payment_date' => form_error('payment_date'),
                'cheque_date'  => form_error('cheque_date'),
                'cheque_no'    => form_error('cheque_no'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $payment_date = $this->customlib->dateFormatToYYYYMMDD($this->input->post("payment_date"));
            $cheque_date  = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date"));
            $amount       = $this->input->post('amount');

            $data = array(
                'case_reference_id' => $this->input->post('case_reference_id'),
                'id'            	=> $this->input->post('id'),               
                'amount'            => $amount,
                'type'              => 'refund',
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note'),
                'payment_date'      => $payment_date,
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );

            if($this->input->post('ipd_id')!=""){
                $data['ipd_id'] = $this->input->post('ipd_id');
            }
            if($this->input->post('opd_id')!=""){
                $data['opd_id'] = $this->input->post('opd_id');
            }

            $insert_id = $this->transaction_model->add($data);
            $img_name  = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo = pathinfo($_FILES["document"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $img_name);
                $data_img = array('id' => $insert_id, 'document' => $img_name);
            }

            if ($this->input->post('payment_mode') == "Cheque") {
                $data['id']          = $insert_id;
                $data['cheque_date'] = $cheque_date;
                $data['cheque_no']   = $this->input->post('cheque_no');
                $data['attachment']  = $img_name;
                $this->transaction_model->add($data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));

        }

        echo json_encode($array);
    }

    public function patient_discharge($case_id)
    {
        $data['case_id']        = $case_id;
		$type                   = $this->input->post('module_type');
        $patient                = $this->patient_model->getDetailsByCaseId($case_id,$type);
        
        $data['discharge_card'] = $this->patient_model->get_dischargeCard(array('case_reference_id' => $case_id));
		
		if($type != 'ipd'){
			$data['opd_id']         = $patient['opdid'];
			$data['ipd_id']         = '';
		}else{
			$data['opd_id']         = '';
			$data['ipd_id']         = $patient['ipdid'];
		}
        
		
        $data['guardian_name']  = "";
        $data['deathrecord']    = array();
        $data['patient_id']     = $patient['patient_id'];
        $data['guardian_name']  = $patient['guardian_name'];        
        
        if (!empty($data['discharge_card']) && $data['discharge_card']['discharge_status'] == '1') {
            $death_record = $this->birthordeath_model->getDeDetailsbycaseId($case_id);
            $id           = $death_record['id'];
            $this->load->helper('customfield_helper');
            $cutom_fields_data = get_custom_table_values($id, 'discharge_card');
            $deathrecord       = $this->birthordeath_model->getDeDetails($id);
            if (!empty($deathrecord)) {
                $deathrecord["death_date"] = $this->customlib->YYYYMMDDHisTodateFormat($deathrecord['death_date'], $this->time_format);
                $deathrecord['field_data'] = $cutom_fields_data;
                $data['guardian_name']     = $deathrecord['guardian_name'];
            }
            $data['deathrecord'] = $deathrecord;
        }
        $data['death_status'] = $this->customlib->discharge_status();   
        
        $page                 = $this->load->view('admin/bill/_patient_discharge', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function add_discharge()
    {
        $this->form_validation->set_rules('discharge_date', $this->lang->line('discharge_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('death_status', $this->lang->line('discharge_status'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');

        if ($_POST['death_status'] == "1") {
            $this->form_validation->set_rules('death_date', $this->lang->line('death_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('guardian_name', $this->lang->line('guardian_name'), 'trim|required|xss_clean');
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

        } elseif ($_POST['death_status'] == "2") {
            $this->form_validation->set_rules('referral_date', $this->lang->line('referral_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('hospital_name', $this->lang->line('hospital_name'), 'trim|required|xss_clean');
        }
        if ($this->form_validation->run() == false) {
            $msg = array(
                'discharge_date' => form_error('discharge_date'),
                'death_status'   => form_error('death_status'),
                'death_date'     => form_error('death_date'),
                'referral_date'  => form_error('referral_date'),
                'hospital_name'  => form_error('hospital_name'),
                'guardian_name'  => form_error('guardian_name'),
                'document'       => form_error('document'),
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

            $discharge_date = $this->input->post("discharge_date");

            if ($discharge_date != "") {
                $discharge_date = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('discharge_date'), $this->time_format);
            } else {
                $discharge_date != "";
            }

            $death_date = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post("death_date"), $this->time_format);
            $refer_date = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post("referral_date"), $this->time_format);
            $opd_id     = $this->input->post('opd_id');
            $ipd_id     = $this->input->post('ipd_id');

            $data = array(
                'id'                  => $this->input->post('id'),
                'case_reference_id'   => $this->input->post('case_reference_id'),
                'discharge_status'    => $this->input->post('death_status'),
                'operation'           => $this->input->post('operation'),
                'diagnosis'           => $this->input->post('diagnosis'),
                'investigations'      => $this->input->post('investigations'),
                'treatment_home'      => $this->input->post('treatment_home'),
                'note'                => $this->input->post('note'),
                'discharge_date'      => $discharge_date,
                'death_date'          => $death_date,
                'refer_date'          => $refer_date,
                'refer_to_hospital'   => $this->input->post('hospital_name'),
                'reason_for_referral' => $this->input->post('reason_for_referral'),
                'discharge_by'        => $this->customlib->getLoggedInUserID(),
            );

            if ($opd_id != '') {
                $data['opd_details_id'] = $opd_id;
            }

            if ($ipd_id != '') {
                $data['ipd_details_id'] = $ipd_id;
            }

            $insert_id = $this->patient_model->add_discharge($data);
            if ($ipd_id != '') {
                $bed_history_data['case_reference_id'] = $this->input->post('case_reference_id');
                $bed_history_data['to_date']           = $discharge_date;
                $bed_history_data['is_active']         = "no";
                $this->bed_model->updateBedHistory($bed_history_data);
            }
            if ($_POST['death_status'] == "1") {
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
                $deathrecord_id = $this->input->post('deathrecord_id');
                $death_data     = array(
                    'id'                => $deathrecord_id,
                    'patient_id'        => $this->input->post('patient_id'),
                    'guardian_name'     => $this->input->post('guardian_name'),
                    'case_reference_id' => $this->input->post('case_reference_id'),
                    'death_date'        => $death_date,
                    'death_report'      => $this->input->post('death_report'),
                    'is_active'         => 'yes',
                );

                $attachment      = "";
                $attachment_name = "";
                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $fileInfo        = pathinfo($_FILES["document"]["name"]);
                    $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                    $attachment_name = $_FILES["document"]["name"];
                    move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/death_image/" . $attachment);
                    $death_data['attachment']      = $attachment;
                    $death_data['attachment_name'] = $attachment_name;
                }

                $insert_id = $this->birthordeath_model->addDeathdata($death_data);

                //update death status in patient table
                $patient_data = array('id' => $this->input->post('patient_id'), 'is_dead' => 'yes');
                $this->patient_model->add($patient_data);

                if (!empty($custom_value_array) && $deathrecord_id != '') {
                    $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                }

                $event_data = array(
                    'patient_id' => $this->input->post('patient_id'),
                    'case_id'    => $this->input->post('case_reference_id'),
                    'death_date' => $this->customlib->YYYYMMDDHisTodateFormat($this->input->post("death_date"), $this->time_format),
                );

                $this->system_notification->send_system_notification('add_death_record', $event_data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));

            $doctor_list  = $this->patient_model->getDoctorsipd($ipd_id);
            $death_status = $this->customlib->discharge_status($this->input->post('death_status'));

            $total_amount   = 0;
            $case_id        = $this->input->post('case_reference_id');
            $transaction    = $this->transaction_model->ipdopdPaymentByCaseId($case_id);
            $charge_details = $this->transaction_model->get_ipdopdchargebycaseId($case_id);
            foreach ($charge_details as $key => $value) {
                $total_amount += amountFormat($value['amount']);
            }

            $paid_amount    = amountFormat($transaction['total_pay']);
            $balance_amount = amountFormat($total_amount - $paid_amount);

            if ($opd_id != '') {
                $event_data = array(
                    'patient_id'       => $this->input->post('patient_id'),
                    'opd_no'           => $this->customlib->getSessionPrefixByType('opd_no') . $opd_id,
                    'case_id'          => $this->input->post('case_reference_id'),
                    'discharge_date'   => $this->customlib->YYYYMMDDHisTodateFormat($discharge_date, $this->time_format),
                    'discharge_status' => $death_status,
                );

                $this->system_notification->send_system_notification('add_opd_discharge_patient', $event_data);
                $sender_details = array('patient_id' => $this->input->post('patient_id'), 'total_amount' => $total_amount, 'paid_amount' => $paid_amount, 'balance_amount' => $balance_amount, 'opd_no' => $opd_id);
                $this->mailsmsconf->mailsms('opd_patient_discharged', $sender_details);
            }

            if ($ipd_id != '') {
                $event_data = array(
                    'patient_id'       => $this->input->post('patient_id'),
                    'ipd_no'           => $this->customlib->getSessionPrefixByType('ipd_no') . $ipd_id,
                    'case_id'          => $this->input->post('case_reference_id'),
                    'discharge_date'   => $this->customlib->YYYYMMDDHisTodateFormat($discharge_date, $this->time_format),
                    'discharge_status' => $death_status,
                );

                $this->system_notification->send_system_notification('add_ipd_discharge_patient', $event_data, $doctor_list);
                $sender_details = array('patient_id' => $this->input->post('patient_id'), 'total_amount' => $total_amount, 'paid_amount' => $paid_amount, 'balance_amount' => $balance_amount); 
                $this->mailsmsconf->mailsms('ipd_patient_discharged', $sender_details);
            }
        }

        echo json_encode($array);
    }

    public function print_dischargecard()
    {
        $print_details          = $this->printing_model->get('', 'discharge_card');
        $id                     = $this->input->post('id');
        $case_id                = $this->input->post('case_id');
        $patient                = $this->patient_model->getDetailsByCaseId($case_id);
        $data['print_details']  = $print_details;
        $data['case_id']        = $case_id;
        $data['result']         = $patient;
        $type                   = $this->input->post('module_type');
        $data['discharge_card'] = $this->patient_model->get_dischargeCard(array('case_reference_id' => $case_id));
        $data['deathrecord']    = array();
        $data['patient_id']     = $patient['patient_id'];
        $data['guardian_name']  = $patient['guardian_name'];
        $download               = "";
        if (!empty($data['discharge_card']) && $data['discharge_card']['discharge_status'] == '1') {
            $death_record = $this->birthordeath_model->getDeDetailsbycaseId($case_id);
            $id           = $death_record['id'];

            $deathrecord = $this->birthordeath_model->getDeDetails($id);
            if ($deathrecord['guardian_name'] != '') {
                $cutom_fields_data = get_custom_table_values($id, 'discharge_card');
                $deathrecord["death_date"] = $this->customlib->YYYYMMDDHisTodateFormat($deathrecord['death_date'], $this->time_format);
                $deathrecord['field_data'] = $cutom_fields_data;
                $data['guardian_name']     = $deathrecord['guardian_name'];
            }
            $data['deathrecord'] = $deathrecord;
        }
        if ((!empty($deathrecord)) && $deathrecord['attachment'] != "") {
            $download = '&nbsp;<a title= ' . $this->lang->line('download') . ' href=' . site_url('admin/birthordeath/download_deathrecord/' . $deathrecord['id']) . '   class="" data-recordId=""  data-original-title=""><i class="fa fa-download"></i> </a>';
        }

        $action = '<a href="javascript:void(0);" class=" print_dischargecard" data-toggle="tooltip" title=' . $this->lang->line('print') . ' data-module_type="' . $type . '" data-case_id="' . $case_id . '" data-recordId="' . $id . '" data-original-title=""><i class="fa fa-print"></i> </a>' . $download;
        $page   = $this->load->view('admin/bill/_printDischargeCard', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'action' => $action));
    }

    public function printBillReport()
    {
        $case_reference_id = $this->input->post("case_reference_id");
        $patient           = $this->patient_model->getDetailsByCaseId($case_reference_id);

        $opd_data                    = $this->patient_model->getPatientChargePaymentOPD($case_reference_id);
        $ipd_data                    = $this->patient_model->getPatientChargePaymentIPD($case_reference_id);
        $pharmacy_data               = $this->patient_model->getPatientChargePaymentPharmacy($case_reference_id);
        $pathology_data              = $this->patient_model->getPatientChargePaymentPathology($case_reference_id);
        $radiology_data              = $this->patient_model->getPatientChargePaymentRadiology($case_reference_id);
        $ambulance_data              = $this->patient_model->getPatientChargePaymentAmbulance($case_reference_id);
        $bloodbank_data              = $this->patient_model->getPatientChargePaymentBloodBank($case_reference_id);
        $data["charge_payment_data"] = array_merge($opd_data, $ipd_data, $pharmacy_data, $pathology_data, $radiology_data, $ambulance_data, $bloodbank_data);
        $page                        = $this->load->view("admin/bill/_print_bill_report", $data, true);
        echo json_encode(array('status' => 1, 'patient_name' => composePatientName($patient['patient_name'], $patient['patient_id']), 'page' => $page));
    }

    public function getAmbulanceCallTransaction()
    {
        $billing_id                    = $this->input->post('id');
        $patient_referance_case_id     = $this->input->post('patient_referance_case_id');        
        $transaction    = $this->transaction_model->ambulanceCallPayments($billing_id);
        $total_payment  = $this->vehicle_model->getBillDetailsAmbulance($billing_id);
        $balance_amount = $total_payment['net_amount'] - $total_payment['paid_amount'];

        $data["patient_referance_case_id"]     = $patient_referance_case_id;
        $data["balance_amount"] = $balance_amount;
        $data["patient_id"]     = $total_payment['patient_id'];
        $data["billing_id"]     = $billing_id;
        $data["payment_mode"]   = $this->payment_mode;
        $data['transaction']    = $transaction;
        $data['time_format']    = $this->time_format;
        $data['ambullance_call_detail'] =$total_payment;
        $page                   = $this->load->view("admin/bill/_getambulanceTransaction", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function print_ambulance_Transaction()
    {
        $print_details              = $this->printing_model->get('', 'paymentreceipt');
        $id                         = $this->input->post('id');
        $charge                     = array();
        $data['transaction_prefix'] = $this->customlib->getSessionPrefixByType('transaction_id');
        $transaction                = $this->transaction_model->ambulanceCallPaymentByTransactionId($id);
        $data['print_details']      = $print_details;
        $data['transaction']        = $transaction;
        $page                       = $this->load->view("admin/bill/print_ambulance_Transaction", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function discharge_revert()
    {
        $discharge_module       = $this->input->post('module_type');
        $case_id                = $this->input->post('case_id');
        $data['discharge_card'] = $this->patient_model->get_dischargeCard(array('case_reference_id' => $case_id));

        if ($data['discharge_card']['discharge_status'] != '') {
            $death_status = $this->customlib->discharge_status($data['discharge_card']['discharge_status']);
        } else {
            $death_status = '';
        }
        
        $revert_date = date('Y-m-d H:i:s');
        
        if ($discharge_module == 'ipd') {
            $discharge_data = $this->patient_model->get_patientbed($data['discharge_card']['ipd_details_id']);

            if ($data['discharge_card']['ipd_details_id'] != '') {
                $patientid = $this->patient_model->get_patientidbyIpdId($data['discharge_card']['ipd_details_id']);
                $patientid = $patientid['patient_id'];
            } else {
                $patientid = '';
            }

            if ($discharge_data['is_active'] == 'no') {
                $array = array('status' => 'fail', 'message' => $this->lang->line('previous_bed_already_alloted_to_another_patient'), 'data' => '');
            } else {
                $discharge_data['opd_details_id'] = $data['discharge_card']['opd_details_id'];
                $array                            = array('status' => 'success', 'message' => '', 'data' => $discharge_data);

                $patient_details = $this->patient_model->get_patientidbyIpdId($ipd_details_id);
                 
                $event_data     = array(
                    'revert_date'     => $this->customlib->YYYYMMDDHisTodateFormat($revert_date, $this->customlib->getHospitalTimeFormat()),
                    'patient_id'      => $patientid['patient_id'],
                    'ipd_no'          => $this->customlib->getSessionPrefixByType('ipd_no').$data['discharge_card']['ipd_details_id'],
                    'case_id'         => $case_id,
                    'bed_group'       => $discharge_data['bed_group_name'],
                    'bed_no'          => $discharge_data['bed_name'],
                    'revert_reason'   => $this->input->post("discharge_revert_reason"),
                );

                $this->system_notification->send_system_notification('ipd_patient_discharge_revert', $event_data);
            }

        } else {
            if (!empty($data['discharge_card']['ipd_details_id'])) {
                $array = array('status' => 'fail', 'message' => $this->lang->line('please_discharge_revert_from_ipd_then_it_will_be_revert_from_opd'), 'data' => '');

            } else {
                if (!empty($data['discharge_card']['opd_details_id'])) {
                    $this->patient_model->opd_discharge_revert($data['discharge_card']['opd_details_id']);
                    $this->patient_model->remove_dischargeCard($data['discharge_card']);
                    $array = array('status' => 'success', 'message' => $this->lang->line('patient_has_been_discharged_from_opd'));

                    if ($data['discharge_card']['opd_details_id'] != '') {
                        $patientid = $this->patient_model->get_patientidbyopdid($data['discharge_card']['opd_details_id']);
                        $patientid = $patientid['patient_id'];
                    } else {
                        $patientid = '';
                    }

                    $event_data = array(
                        'revert_date'     => $this->customlib->YYYYMMDDHisTodateFormat($revert_date, $this->customlib->getHospitalTimeFormat()),
                        'patient_id'       => $patientid,
                        'opd_no'           => $this->customlib->getSessionPrefixByType('opd_no') . $data['discharge_card']['opd_details_id'],
                        'discharge_status' => $death_status,
                        'discharge_date'   => $this->customlib->YYYYMMDDHisTodateFormat($data['discharge_card']['discharge_date'], $this->customlib->getHospitalTimeFormat()),
                        'case_id'          => $case_id,
                    );

                    $this->system_notification->send_system_notification('opd_patient_discharge_revert', $event_data);
                }
            }

        }
        echo json_encode($array);
    }

    public function discharged_bed_revert()
    {
        $this->form_validation->set_rules('bed_no', $this->lang->line('bed_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('discharge_revert_reason', $this->lang->line('revert_reason'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'bed_no'                  => form_error('bed_no'),
                'discharge_revert_reason' => form_error('discharge_revert_reason'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $ipd_details_id    = $this->input->post('ipd_details_id');
            $opd_details_id    = $this->input->post('opd_details_id');
            $case_reference_id = $this->patient_model->getReferenceByIpdId($ipd_details_id);
            $patient_details = $this->patient_model->get_patientidbyIpdId($ipd_details_id);
            $bed_data          = array('id' => $this->input->post('bed_no'), 'is_active' => 'no');
            $this->bed_model->savebed($bed_data);
            $this->bed_model->updateBedHistoryStatus($case_reference_id);
            $bed_history = array(
                "case_reference_id" => $case_reference_id,
                "bed_group_id"      => $this->input->post("bed_group_id"),
                "bed_id"            => $this->input->post("bed_no"),
                "from_date"         => date("Y-m-d H:i:s"),
                "is_active"         => "yes",
                "revert_reason"     => $this->input->post("discharge_revert_reason"),
            );

            $this->bed_model->saveBedHistory($bed_history);
            $this->patient_model->ipd_discharge_revert($ipd_details_id);
            $this->patient_model->opd_discharge_revert($opd_details_id);
            $discharge_data = $this->patient_model->get_dischargeCard(array('case_reference_id' => $case_reference_id));
            $this->patient_model->remove_dischargeCard($discharge_data);
            
            $patient_bed_data = $this->patient_model->get_patientbed($this->input->post('ipd_details_id'));
            $revert_date = date('Y-m-d H:i:s'); 
            $event_data     = array(
                'revert_date'     => $this->customlib->YYYYMMDDHisTodateFormat($revert_date, $this->customlib->getHospitalTimeFormat()),
                'patient_id'      => $patient_details['patient_id'],
                'ipd_no'          => $this->customlib->getSessionPrefixByType('ipd_no').$this->input->post('ipd_details_id'),
                'case_id'         => $case_reference_id,
                'bed_group'       => $patient_bed_data['bed_group_name'],
                'bed_no'          => $patient_bed_data['bed_name'],
                'revert_reason'   => $this->input->post("discharge_revert_reason"),
            );

            $this->system_notification->send_system_notification('ipd_patient_discharge_revert', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
        }

        echo json_encode($array);
    }

//=============pathology=====================
    public function pathology()
    {
        if (!$this->rbac->hasPrivilege('pathology_billing', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'bill');
        $id                  = $this->input->post("id");
        $patients            = $this->patient_model->getPatientListall();
        $data["patients"]    = $patients;
        $pathologist         = $this->staff_model->getStaffbyrole(3);
        $data["pathologist"] = $pathologist;
        $testlist            = $this->pathology_model->getpathotestDetails();
        $data["testlist"]    = $testlist;
        $data["bloodgroup"]  = $this->bloodbankstatus_model->get_product(null, 1);
        $data['fields']      = $this->customfield_model->get_custom_fields('pathology', 1);
        $data['organisation']   = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/bill/pathology/pathology', $data);
        $this->load->view('layout/footer');
    }

    public function getPatientPathologyDetails()
    {
        $is_bill    = $this->input->post('is_bill');
        $id         = $this->input->post('id');
        $data['id'] = $id;
        $result     = $this->pathology_model->getPathologyBillByID($id);

        $data['result'] = $result;
        if (isset($is_bill)) {
            $data['is_bill'] = false;
        } else {
            $data['is_bill'] = true;
        }
        $data['bill_prefix']           = $this->customlib->getSessionPrefixByType('pathology_billing');
        $data['fields']                = $this->customfield_model->get_custom_fields('pathology');
        $data['pathology_test_fields'] = $this->customfield_model->get_custom_fields('pathologytest');
        $page                          = $this->load->view('admin/bill/pathology/_getPatientPathologyDetails', $data, true);
        $actions                       = "";

        if (isset($is_bill)) {
            $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_pathology_bill' data-toggle='tooltip' data-placement='bottom'  data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
        } else {
            $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_bill' data-toggle='tooltip' data-record-id=\"" . $id . "\"  data-placement='bottom'  data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
            $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='edit_pathology' data-toggle='tooltip' data-placement='bottom' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('edit_pathology') . "'><i class='fa fa-pencil'></i></a>";
            $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='delete_pathology' data-toggle='tooltip' data-placement='bottom' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('delete_pathology') . "'><i class='fa fa-trash'></i></a>";
        }

        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
    }

    public function getPathologyTransaction()
    {
        $pathology_billing_id  = $this->input->post('id');
        $pathology_transaction = $this->transaction_model->pathologyPayments($pathology_billing_id);
        $is_bill               = $this->input->post('is_bill');
        if (isset($is_bill)) {
            $data['is_bill'] = true;
            $data['form_id'] = "add_pathopartial_payment";
        } else {
            $data['is_bill'] = false;
            $data['form_id'] = "add_partial_payment";
        }
        $data["pathology_billing_id"]    = $pathology_billing_id;
        $data["payment_mode"]            = $this->payment_mode;
        $data['pathology_transaction']   = $pathology_transaction;
        $pathology_billing               = $this->pathology_model->getPathologyBillByID($pathology_billing_id);
        $data['pathology_billing']       = $pathology_billing;
        $data['pathology_total_payment'] = $this->transaction_model->pathologyTotalPayments($pathology_billing_id)->total_paid;
        $page                            = $this->load->view("admin/bill/pathology/_getPathologyTransaction", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function editpathology()
    {
        $id                          = $this->input->post('id');
        $pathology_data              = $this->pathology_model->getPathologyBillByID($id);
        $data["pathology_data"]      = $pathology_data;
        $testlist                    = $this->pathology_model->getpathotestDetails();
        $data["testlist"]            = $testlist;
        $patients                    = $this->patient_model->getPatientListall();
        $data["patients"]            = $patients;
        $patient_names               = array_column($patients, 'patient_name', 'id');
        $doctors                     = $this->staff_model->getStaffbyrole(3);
        $data['custom_fields_value'] = display_custom_fields('pathology', $id);
        $data["doctors"]             = $doctors;
        $data["payment_mode"]        = $this->payment_mode;
        $page                        = $this->load->view("admin/bill/pathology/_editpathology", $data, true);
        $total_rows                  = count($pathology_data->pathology_report);
        $case_reference_id           = $pathology_data->case_reference_id;
        $patient_id                  = $pathology_data->patient_id;
        $date                        = $pathology_data->date;
        $bill_no                     = $pathology_data->id;
        $organisation_id             = $pathology_data->organisation_id;

        if($organisation_id!=null || $organisation_id!=0 || $organisation_id!=""){
            $tpa_apply_status=1;
        }else{
            $tpa_apply_status=0;
        }

        echo json_encode(array('status' => 1, 'page' => $page, 'bill_prefix' => $this->customlib->getSessionPrefixByType('pathology_billing'), 'bill_no' => $bill_no, 'pathology_date' => $date, 'total_rows' => $total_rows, 'case_reference_id' => $case_reference_id, 'patient_id' => $patient_id,'patient_name' => $patient_names[$patient_id] . " (" . $patient_id . ")","organisation_id"=>$organisation_id,'tpa_apply_status'=>$tpa_apply_status));       
    }

    public function add_pathology_bill()
    {        
        $custom_fields = $this->customfield_model->getByBelong('pathology');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[pathology][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
		
        $transaction_data     = array();
        $pathology_billing_id = $this->input->post('pathology_billing_id');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patientid', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('discount', $this->lang->line('discount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tax', $this->lang->line('tax'), 'trim|required|xss_clean');
        if ($pathology_billing_id == '0') {
            $this->form_validation->set_rules(
                'amount', $this->lang->line('amount'), array('trim', 'required', 'xss_clean', 'valid_amount',
                    array('check_exists', array($this->pathology_model, 'validate_paymentamount')),
                )
            );
			
            if ($this->input->post("payment_mode") == "Cheque") {
                $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required');
                $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required');
                $this->form_validation->set_rules('document', $this->lang->line('documents'), 'callback_handle_doc_upload[document]');
            }
        }

        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $total_rows = $this->input->post('total_rows');
        if (!isset($total_rows) && !isset($pathology) && !isset($radiology)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('no_test_selected')));
        }
        $check_duplicate_test = array();
        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_key => $row_value) {

                $test_name              = $this->input->post('test_name_' . $row_value);
                $reportdate             = $this->input->post('reportdate_' . $row_value);
                $check_duplicate_test[] = $test_name;

                if ($test_name == "") {
                    $this->form_validation->set_rules('test_name', $this->lang->line('test_name'), 'trim|required|xss_clean');
                }
                if ($reportdate == "") {
                    $this->form_validation->set_rules('reportdate', $this->lang->line('report_date'), 'trim|required|xss_clean');
                }

            }
        }
        if (!empty($check_duplicate_test)) {
            if (has_duplicate_array($check_duplicate_test)) {
                $this->form_validation->set_rules('duplicate_test', $this->lang->line("duplicate_test"), 'trim|required|xss_clean', array('required' => 'The %s not allowed.'));
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'no_records'     => form_error('no_records'),
                'patientid'      => form_error('patientid'),
                'discount'       => form_error('discount'),
                'tax'            => form_error('tax'),
                'test_name'      => form_error('test_name'),
                'reportdate'     => form_error('reportdate'),
                'amount'         => form_error('amount'),
                'duplicate_test' => form_error('duplicate_test'),
                'document'       => form_error('document'),
                'date'           => form_error('date'),
                'net_amount'     => form_error('net_amount'),
                'total'          => form_error('total'),
            );

            if ($pathology_billing_id == '0') {

                if ($this->input->post("payment_mode") == "Cheque") {
                    $msg['cheque_no']   = form_error('cheque_no');
                    $msg['cheque_date'] = form_error('cheque_date');
                }
            }

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                  = $custom_fields_value['id'];
                        $custom_fields_name                                                = $custom_fields_value['name'];
                        $error_msg2["custom_fields[pathology][" . $custom_fields_id . "]"] = form_error("custom_fields[pathology][" . $custom_fields_id . "]");
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
            $patient_id        = $this->input->post('patientid');
            $bill_date         = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date'));
            $doctor_name       = $this->input->post('doctor_name');
            $doctor_id         = $this->input->post('consultant_doctor');
            $case_reference_id = $this->input->post('case_reference_id');
            if (empty($doctor_id)) {
                $doctor_id = null;
            }
            if (empty($case_reference_id)) {
                $case_reference_id = null;
            }

             $is_tpa = $this->input->post('is_tpa');
            if($is_tpa=="" || $is_tpa==0){
                $organisation_id    = null;
                $insurance_id       = null;
                $insurance_validity = null;
            }else{           
                $organisation_id    = $this->input->post('organisation_id');
                $insurance_id       = $this->input->post('insurance_id');
                $insurance_validity = $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity'));
            }
            if (empty($organisation_id)) {
                $organisation_id = null;
            }

            $data = array(
                'date'                => $bill_date,
                'patient_id'          => $patient_id,
                'doctor_name'         => $doctor_name,
                'doctor_id'           => $doctor_id,
                'case_reference_id'   => $case_reference_id,
                'total'               => $this->input->post('total'),
                'discount'            => $this->input->post('discount'),
                'discount_percentage' => $this->input->post('discount_percent'),
                'tax'                 => $this->input->post('tax'),
                'net_amount'          => $this->input->post('net_amount'),
                'note'                => $this->input->post('note'),
                'generated_by'        => $this->customlib->getLoggedInUserID(),
                'organisation_id'     => $organisation_id,
                'insurance_validity'  => $insurance_validity,
                'insurance_id'        => $insurance_id,
            );

            $custom_field_post  = $this->input->post("custom_fields[pathology]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[pathology][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            if ($pathology_billing_id > 0) {
                $data['id'] = $pathology_billing_id;
            }

            $total_rows                = $this->input->post('total_rows');
            $prev_reports              = $this->input->post('prev_reports');
            $insert_array              = array();
            $update_array              = array();
            $prev_reports_array        = array();
            $prev_reports_update_array = array();
            if (isset($prev_reports)) {

                $prev_reports_array = $prev_reports;
            }

            foreach ($total_rows as $row_key => $row_value) {
                $test_report_id = $this->input->post('inserted_id_' . $row_value);
                if ($test_report_id == 0) {
                    $report = array(
                        'pathology_bill_id' => 0,
                        'patient_id'        => $patient_id,
                        'pathology_id'      => $this->input->post('test_name_' . $row_value),
                        'tax_percentage'    => $this->input->post('taxpercent_' . $row_value),
                        'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($this->input->post('reportdate_' . $row_value)),
                        'apply_charge'      => $this->input->post('amount_' . $row_value),
                    );
                    $insert_array[] = $report;
                } else if ($test_report_id > 0) {
                    $report = array(
                        'id'             => $test_report_id,
                        'patient_id'     => $patient_id,
                        'pathology_id'   => $this->input->post('test_name_' . $row_value),
                        'tax_percentage' => $this->input->post('taxpercent_' . $row_value),
                        'reporting_date' => $this->customlib->dateFormatToYYYYMMDD($this->input->post('reportdate_' . $row_value)),
                        'apply_charge'   => $this->input->post('amount_' . $row_value),
                    );
                    $prev_reports_update_array[] = $test_report_id;
                    $update_array[]              = $report;
                }
            }

            if ($pathology_billing_id == '0') {

                $cheque_date      = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date"));
                $payment_section  = $this->config->item('payment_section');
                $transaction_data = array(
                    'pathology_billing_id' => 0,
                    'patient_id'           => $patient_id,
                    'case_reference_id'    => $case_reference_id,
                    'section'              => $payment_section['pathology'],
                    'amount'               => $this->input->post('amount'),
                    'type'                 => 'payment',
                    'ipd_id'               => $this->input->post('ipdid'),
                    'payment_mode'         => $this->input->post('payment_mode'),
                    'payment_date'         => $bill_date,
                    'received_by'          => $this->customlib->getLoggedInUserID(),
                );
                if ($this->input->post('payment_mode') == "Cheque") {
                    $transaction_data['cheque_date'] = $cheque_date;
                    $transaction_data['cheque_no']   = $this->input->post('cheque_no');
                    if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                        $fileInfo        = pathinfo($_FILES["document"]["name"]);
                        $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                        $attachment_name = $_FILES["document"]["name"];
                        move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
                        $transaction_data['attachment']      = $attachment;
                        $transaction_data['attachment_name'] = $attachment_name;
                    }
                }
            }

            $array_delete = array_diff($prev_reports_array, $prev_reports_update_array);
            $inserted     = $this->pathology_model->addBill($data, $insert_array, $update_array, $array_delete, $pathology_billing_id, $transaction_data);

            if ($pathology_billing_id > 0) {
                if (!empty($custom_fields)) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[pathology][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $inserted,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                    $this->customfield_model->updateRecord($custom_value_array, $inserted, 'pathology');
                }
            } else {
                if (!empty($custom_value_array)) {
                    $this->customfield_model->insertRecord($custom_value_array, $inserted);
                }
            }
            if ($inserted) {
                $patientlist    = $this->notificationsetting_model->getpatientDetails($patient_id);

                $event_data     = array(
                    'patient_id'  => $patient_id,
                    'case_id'     => $this->input->post('case_reference_id'),
                    'bill_no'     => $this->input->post('bill_no'),
                    'date'        => $this->customlib->YYYYMMDDTodateFormat($this->input->post('date')),                    
                    'total'       => $this->input->post('total'),
                    'discount'    => $this->input->post('discount'),
                    'tax'         => $this->input->post('tax'),
                    'net_amount'  => $this->input->post('net_amount'),
                    'paid_amount' => $this->input->post('amount'),
                );

                if ($doctor_id != "") {
                    $doctor_details            = $this->notificationsetting_model->getstaffDetails($doctor_id);
                    $event_data['doctor_name'] = composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']);
                    $event_data['doctor_id']   = $doctor_id;
                } else {
                    $event_data['doctor_name'] = $doctor_name;
                    $event_data['doctor_id']   = $doctor_id;
                }

                $this->system_notification->send_system_notification('pathology_investigation', $event_data);

                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $inserted);
            } else {
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('something_went_wrong'), 'insert_id' => $inserted);
            }

        }
        echo json_encode($array);
    }

    public function delete_pathology_bill()
    {
        $id = $this->input->post('id');
        if (!$this->rbac->hasPrivilege('pathology_test', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->pathology_model->deletePathologyBill($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => 'Something went wrong');
        }
        echo json_encode($array);
    }

    public function partial_pathology_bill()
    {
        if (!$this->rbac->hasPrivilege('pathology_bill', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('payment_date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|valid_amount');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required');
        if ($this->input->post('payment_mode') == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_upload_document');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'payment_date' => form_error('payment_date'),
                'amount'       => form_error('amount'),
                'payment_mode' => form_error('payment_mode'),
                'cheque_no'    => form_error('cheque_no'),
                'cheque_date'  => form_error('cheque_date'),
                'document'     => form_error('document'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $pathology_billing_id     = $this->input->post('pathology_billing_id');
            $pathology_billing_detail = $this->transaction_model->pathologyTotalPayments($pathology_billing_id);
            $net_amount               = $pathology_billing_detail->net_amount;
            $amount_paying            = $this->input->post('amount');
            $total_paid               = $pathology_billing_detail->total_paid;

            if ($net_amount >= ($total_paid + $amount_paying)) {
                $picture         = "";
                $bill_date       = $this->input->post("payment_date");
                $payment_section = $this->config->item('payment_section');
                $payment_array   = array(
                    'amount'               => $this->input->post('amount'),
                    'type'                 => 'payment',
                    'patient_id'           => $this->input->post('patient_id'),
                    'section'              => $payment_section['pathology'],
                    'pathology_billing_id' => $this->input->post('pathology_billing_id'),
                    'payment_mode'         => $this->input->post('payment_mode'),
                    'note'                 => $this->input->post('note'),
                    'payment_date'         => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->customlib->getHospitalTimeFormat()),
                    'received_by'          => $this->customlib->getLoggedInUserID(),
                );

                if (!empty($this->input->post('case_reference_id')) && $this->input->post('case_reference_id') != "") {
                    $payment_array['case_reference_id'] = $this->input->post('case_reference_id');
                }

                $attachment      = "";
                $attachment_name = "";

                $cheque_date = $this->input->post("cheque_date");
                if ($this->input->post('payment_mode') == "Cheque") {

                    $payment_array['cheque_date'] = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                    $payment_array['cheque_no']   = $this->input->post('cheque_no');

                    if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                        $fileInfo        = pathinfo($_FILES["document"]["name"]);
                        $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                        $attachment_name = $_FILES["document"]["name"];
                        move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
                        $payment_array['attachment']      = $attachment;
                        $payment_array['attachment_name'] = $attachment_name;
                    }
                }

                $this->transaction_model->add($payment_array);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $array = array('status' => 'fail', 'error' => array('amount_invalid' => $this->lang->line('amount_should_not_be_greater_than_balance') . ' ' . amountFormat($net_amount - $total_paid)), 'message' => '');

            }

        }
        echo json_encode($array);
    }

    //============end pathology=============================

    //============ radiology=============================

    public function radiology()
    {
        if (!$this->rbac->hasPrivilege('radiology_billing', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'bill');
        $id                         = $this->input->post("radiology_id");
        $radiologist                = $this->staff_model->getStaffbyrole(6);
        $data["radiologist"]        = $radiologist;
        $data['radiologist_select'] = '';
        $testlist                   = $this->radio_model->getradiotestDetails();
        $data["testlist"]           = $testlist;
        $patients                   = $this->patient_model->getPatientListall();
        $data["patients"]           = $patients;
        $data["bloodgroup"]         = $this->bloodbankstatus_model->get_product(null, 1);
        $data['fields']             = $this->customfield_model->get_custom_fields('radiology', 1);
        $this->load->view('layout/header');
        $this->load->view('admin/bill/radiology/radiology', $data);
        $this->load->view('layout/footer');
    }

    public function getPatientRadiologyDetails()
    {
        $id                  = $this->input->post('id');
        $data['id']          = $id;
        $result              = $this->radio_model->getRadiologyBillByID($id);
        $data['bill_prefix'] = $this->customlib->getSessionPrefixByType('radiology_billing');
        $is_bill             = $this->input->post('is_bill');
        if (isset($is_bill)) {
            $data['is_bill'] = false;
        } else {
            $data['is_bill'] = true;
        }

        $data['fields'] = $this->customfield_model->get_custom_fields('radiology');
        $data['result'] = $result;
        $page           = $this->load->view('admin/bill/radiology/_getPatientRadiologyDetails', $data, true);
        $actions        = "";
        if (isset($is_bill)) {
            $actions .= "<a href='javascript:void(0)' data-loading-text='" . $this->lang->line('please_wait') . "' class='print_radiology_bill' data-toggle='tooltip' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";

        } else {
            $actions .= "<a href='javascript:void(0)' data-loading-text='" . $this->lang->line('please_wait') . "' class='print_bill' data-placement='bottom' data-toggle='tooltip' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
            $actions .= "<a href='javascript:void(0)' data-loading-text='" . $this->lang->line('please_wait') . "' class='edit_radiology' data-placement='bottom' data-toggle='tooltip' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('edit_radiology') . "'><i class='fa fa-pencil'></i></a>";
            $actions .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='delete_radiology' data-placement='bottom' data-toggle='tooltip' data-record-id=\"" . $id . "\"   data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
        }
        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $actions));
    }

    public function getRadiologyTransactions()
    {
        $radiology_billing_id  = $this->input->post('id');
        $is_bill               = $this->input->post('is_bill');
        $radiology_transaction = $this->transaction_model->radiologyPayments($radiology_billing_id);
        if (isset($is_bill)) {
            $data['is_bill'] = true;
            $data['form_id'] = "add_radio_partial_payment";
        } else {
            $data['is_bill'] = false;
            $data['form_id'] = "add_partial_payment";
        }
        $radio_billing         = $this->radio_model->getRadiologyBillByID($radiology_billing_id);
        $data['radio_billing'] = $radio_billing;
        $data["radiology_billing_id"]  = $radiology_billing_id;
        $data["payment_mode"]          = $this->payment_mode;
        $data['radiology_transaction'] = $radiology_transaction;
        $page                          = $this->load->view("admin/bill/radiology/_getRadiologyTransactions", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function partial_radiology_bill()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('payment_date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|valid_amount');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required');

        if ($this->input->post('payment_mode') == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line("document"), 'callback_handle_doc_upload[document]');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'payment_date' => form_error('payment_date'),
                'amount'       => form_error('amount'),
                'payment_mode' => form_error('payment_mode'),
                'cheque_no'    => form_error('cheque_no'),
                'cheque_date'  => form_error('cheque_date'),
                'document'     => form_error('document'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $radiology_billing_id     = $this->input->post('radiology_billing_id');
            $radiology_billing_detail = $this->transaction_model->radiologyTotalPayments($radiology_billing_id);
            $net_amount               = $radiology_billing_detail->net_amount;
            $amount_paying            = $this->input->post('amount');
            $total_paid               = $radiology_billing_detail->total_paid;

            if ($net_amount >= ($total_paid + $amount_paying)) {

                $picture         = "";
                $bill_date       = $this->input->post("payment_date");
                $payment_section = $this->config->item('payment_section');
                $payment_array   = array(
                    'amount'               => $this->input->post('amount'),
                    'type'                 => 'payment',
                    'patient_id'           => $this->input->post('patient_id'),
                    'section'              => $payment_section['radiology'],
                    'radiology_billing_id' => $this->input->post('radiology_billing_id'),
                    'payment_mode'         => $this->input->post('payment_mode'),
                    'note'                 => $this->input->post('note'),
                    'payment_date'         => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->customlib->getHospitalTimeFormat()),
                    'received_by'          => $this->customlib->getLoggedInUserID(),
                );

                if (!empty($this->input->post('case_reference_id')) && $this->input->post('case_reference_id') != "") {
                    $payment_array['case_reference_id'] = $this->input->post('case_reference_id');
                }

                $attachment      = "";
                $attachment_name = "";
                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $fileInfo        = pathinfo($_FILES["document"]["name"]);
                    $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                    $attachment_name = $_FILES["document"]["name"];
                    move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
                }
				
                $cheque_date = $this->input->post("cheque_date");
                if ($this->input->post('payment_mode') == "Cheque") {
                    $payment_array['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                    $payment_array['cheque_no']       = $this->input->post('cheque_no');
                    $payment_array['attachment']      = $attachment;
                    $payment_array['attachment_name'] = $attachment_name;
                }

                $this->transaction_model->add($payment_array);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {

                $array = array('status' => 'fail', 'error' => array('amount_invalid' => $this->lang->line('amount_should_not_be_greater_than_balance') . ' ' . amountFormat($net_amount - $total_paid)), 'message' => '');
            }

        }
        echo json_encode($array);
    }

    public function editradiology()
    {
        $id                          = $this->input->post('id');
        $radiology_data              = $this->radio_model->getRadiologyBillByID($id);
        $data["radiology_data"]      = $radiology_data;
        $testlist                    = $this->radio_model->getradiotestDetails();
        $data["testlist"]            = $testlist;
        $patients                    = $this->patient_model->getPatientListall();
        $data["patients"]            = $patients;
        $patient_names               = array_column($patients, 'patient_name', 'id');
        $doctors                     = $this->staff_model->getStaffbyrole(3);
        $data['custom_fields_value'] = display_custom_fields('radiology', $id);
        $data["doctors"]             = $doctors;
        $data["payment_mode"]        = $this->payment_mode;
        $page                        = $this->load->view("admin/bill/radiology/_editradiology", $data, true);
        $total_rows                  = count($radiology_data->radiology_report);
        $case_reference_id           = $radiology_data->case_reference_id;
        $patient_id                  = $radiology_data->patient_id;
        $bill_no                     = $radiology_data->id;
        $date                        = $radiology_data->date;
        $organisation_id             = $radiology_data->organisation_id;

        if($organisation_id!=null || $organisation_id!=0 || $organisation_id!=""){
            $tpa_apply_status=1;
        }else{
            $tpa_apply_status=0;
        }

        echo json_encode(array( 'status' => 1, 'page' => $page, 'bill_no' => $bill_no, 'radiology_date' => $date, 'total_rows' => $total_rows, 'case_reference_id' => $case_reference_id, 'patient_id' => $patient_id, 'patient_name' => $patient_names[$patient_id] . " (" . $patient_id . ")","organisation_id"=>$organisation_id,'tpa_apply_status'=>$tpa_apply_status ));        
    }

    public function add_radiology_bill()
    {
        $custom_fields = $this->customfield_model->getByBelong('radiology');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[radiology][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $transaction_data     = array();
        $radiology_billing_id = $this->input->post('radiology_billing_id');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('discount', $this->lang->line('discount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patientid', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tax', $this->lang->line('tax'), 'trim|required|xss_clean');

        if ($radiology_billing_id == '') {
            $this->form_validation->set_rules(
                'amount', $this->lang->line('amount'), array('trim', 'required', 'xss_clean', 'valid_amount',
                    array('check_exists', array($this->radio_model, 'validate_paymentamount')),
                )
            );

            if ($this->input->post("payment_mode") == "Cheque") {
                $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required');
                $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required');
                $this->form_validation->set_rules('document', $this->lang->line("document"), 'callback_handle_doc_upload[document]');
            }
        }

        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');

        $total_rows = $this->input->post('total_rows');
        if (!isset($total_rows) && !isset($radiology) && !isset($radiology)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('no_test_selected')));
        }
        $check_duplicate_test = array();
        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_key => $row_value) {

                $test_name              = $this->input->post('test_name_' . $row_value);
                $reportdate             = $this->input->post('reportdate_' . $row_value);
                $check_duplicate_test[] = $test_name;
                if ($test_name == "") {
                    $this->form_validation->set_rules('test_name', $this->lang->line('test_name'), 'trim|required|xss_clean');
                }
                if ($reportdate == "") {
                    $this->form_validation->set_rules('reportdate', $this->lang->line('report_date'), 'trim|required|xss_clean');
                }
            }
        }

        if (!empty($check_duplicate_test)) {
            if (has_duplicate_array($check_duplicate_test)) {
                $this->form_validation->set_rules('duplicate_test', ' ', 'trim|required|xss_clean',
                    array('required' => $this->lang->line('duplicate_test_name_found')));
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'no_records'     => form_error('no_records'),
                'patientid'      => form_error('patientid'),
                'test_name'      => form_error('test_name'),
                'reportdate'     => form_error('reportdate'),
                'date'           => form_error('date'),
                'net_amount'     => form_error('net_amount'),
                'total'          => form_error('total'),
                'discount'       => form_error('discount'),
                'amount'         => form_error('amount'),
                'duplicate_test' => form_error('duplicate_test'),
                'tax'            => form_error('tax'),
            );

            if ($radiology_billing_id == '') {
                if ($this->input->post("payment_mode") == "Cheque") {
                    $msg['cheque_no']   = form_error('cheque_no');
                    $msg['cheque_date'] = form_error('cheque_date');
                    $msg['document']    = form_error('document');
                }
            }
			
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                  = $custom_fields_value['id'];
                        $custom_fields_name                                                = $custom_fields_value['name'];
                        $error_msg2["custom_fields[radiology][" . $custom_fields_id . "]"] = form_error("custom_fields[radiology][" . $custom_fields_id . "]");
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

            $patient_id        = $this->input->post('patientid');
            $bill_date         = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date'));
            $doctor_name       = $this->input->post('doctor_name');
            $doctor_id         = $this->input->post('consultant_doctor');
            $case_reference_id = $this->input->post('case_reference_id');
			
            if (empty($case_reference_id)) {
                $case_reference_id = null;
            }
			
            if (empty($doctor_id)) {
                $doctor_id = null;
            }

            $is_tpa = $this->input->post('is_tpa');
            if($is_tpa=="" || $is_tpa==0){
                $organisation_id    = null;
                $insurance_id       = null;
                $insurance_validity = null;
            }else{           
                $organisation_id    = $this->input->post('organisation_id');
                $insurance_id       = $this->input->post('insurance_id');
                $insurance_validity = $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity'));
            } 
			if (empty($organisation_id)) {
                $organisation_id = null;
            }

            $data = array(
                'date'                => ($bill_date),
                'patient_id'          => $patient_id,
                'doctor_name'         => $doctor_name,
                'doctor_id'           => $doctor_id,
                'case_reference_id'   => $case_reference_id,
                'total'               => $this->input->post('total'),
                'discount'            => $this->input->post('discount'),
                'discount_percentage' => $this->input->post('discount_percent'),
                'tax'                 => $this->input->post('tax'),
                'net_amount'          => $this->input->post('net_amount'),
                'note'                => $this->input->post('note'),
                'generated_by'        => $this->customlib->getLoggedInUserID(),
                'organisation_id'     => $organisation_id,
                'insurance_id'        => $insurance_id,
                'insurance_validity'  => $insurance_validity,
            );
			
            $custom_field_post  = $this->input->post("custom_fields[radiology]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[radiology][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
			
            if ($radiology_billing_id > 0) {
                $data['id'] = $radiology_billing_id;
            }

            $total_rows   = $this->input->post('total_rows');
            $prev_reports = $this->input->post('prev_reports');

            $insert_array              = array();
            $update_array              = array();
            $prev_reports_array        = array();
            $prev_reports_update_array = array();
            if (isset($prev_reports)) {
                $prev_reports_array = $prev_reports;
            }

            foreach ($total_rows as $row_key => $row_value) {
                $test_report_id = $this->input->post('inserted_id_' . $row_value);
                if ($test_report_id == 0) {
                    $report = array(
                        'radiology_bill_id' => 0,
                        'radiology_id'      => $this->input->post('test_name_' . $row_value),
                        'reporting_date'    => $this->customlib->dateFormatToYYYYMMDD($this->input->post('reportdate_' . $row_value)),
                        'patient_id'        => $patient_id,
                        'apply_charge'      => $this->input->post('amount_' . $row_value),
                        'tax_percentage'    => $this->input->post('taxpercent_' . $row_value),
                    );
                    $insert_array[] = $report;
                } else if ($test_report_id > 0) {
                    $report = array(
                        'id'             => $test_report_id,
                        'radiology_id'   => $this->input->post('test_name_' . $row_value),
                        'reporting_date' => $this->customlib->dateFormatToYYYYMMDD($this->input->post('reportdate_' . $row_value)),
                        'patient_id'     => $patient_id,
                        'apply_charge'   => $this->input->post('amount_' . $row_value),
                        'tax_percentage' => $this->input->post('taxpercent_' . $row_value),
                    );
                    $prev_reports_update_array[] = $test_report_id;
                    $update_array[]              = $report;
                }
            }

            if ($radiology_billing_id == '') {
                $cheque_date      = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date"));
                $payment_section  = $this->config->item('payment_section');
                $transaction_data = array(
                    'patient_id'        => $patient_id,
                    'case_reference_id' => $case_reference_id,
                    'section'           => $payment_section['radiology'],
                    'amount'            => $this->input->post('amount'),
                    'type'              => 'payment',
                    'ipd_id'            => $this->input->post('ipdid'),
                    'payment_mode'      => $this->input->post('payment_mode'),
                    'note'              => $this->input->post('note'),
                    'payment_date'      => $bill_date,
                    'received_by'       => $this->customlib->getLoggedInUserID(),
                );

                $attachment      = "";
                $attachment_name = "";
                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $fileInfo        = pathinfo($_FILES["document"]["name"]);
                    $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                    $attachment_name = $_FILES["document"]["name"];
                    move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
                }

                if ($this->input->post('payment_mode') == "Cheque") {
                    $transaction_data['cheque_date']     = $cheque_date;
                    $transaction_data['cheque_no']       = $this->input->post('cheque_no');
                    $transaction_data['attachment']      = $attachment;
                    $transaction_data['attachment_name'] = $attachment_name;
                }
            }
			
            $array_delete = array_diff($prev_reports_array, $prev_reports_update_array);
            $inserted = $this->radio_model->addBill($data, $insert_array, $update_array, $array_delete, $radiology_billing_id, $transaction_data);

            if ($radiology_billing_id > 0) {
                if (!empty($custom_fields)) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[radiology][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $inserted,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                    $this->customfield_model->updateRecord($custom_value_array, $inserted, 'radiology');
                }
            } else {
                if (!empty($custom_value_array)) {
                    $this->customfield_model->insertRecord($custom_value_array, $inserted);
                }
            }
            if ($inserted) {

                $patient_name   = $this->notificationsetting_model->getpatientDetails($patient_id);
                $doctor_details = $this->notificationsetting_model->getstaffDetails($doctor_id);
				if(!empty($doctor_details)){
					$doctor_name	=	composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']);
				}else{
					$doctor_name	= '';
				}
                $event_data = array(
                    'patient_id'  => $patient_id,
                    'case_id'     => $case_reference_id,
                    'bill_no'     => $this->input->post('doctorid'),
                    'date'        => $this->customlib->YYYYMMDDTodateFormat($bill_date),
                    'doctor_id'   => $doctor_id,
                    'doctor_name' => $doctor_name,
                    'total'       => $this->input->post('total'),
                    'discount'    => number_format((float) $this->input->post('discount'), 2, '.', ''),
                    'tax'         => number_format((float) $this->input->post('tax'), 2, '.', ''),
                    'net_amount'  => $this->input->post('net_amount'),
                    'paid'        => $this->input->post('amount'),
                );

                $this->system_notification->send_system_notification('radiology_investigation', $event_data);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $inserted);
            } else {
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('something_went_wrong'));
            }
        }
        echo json_encode($array);
    }

    //============end radiology=============================
	
    //==================opd========================
    public function opd()
    {
        if (!$this->rbac->hasPrivilege('opd_billing', 'can_view')) {
            access_denied();
        }

        $opd_data         = $this->session->flashdata('opd_data');
        $data['opd_data'] = $opd_data;
        $data["title"]    = $this->lang->line('opd_patient');
        $this->session->set_userdata('top_menu', 'bill');
        $setting                    = $this->setting_model->get();
        $data['setting']            = $setting;
        $opd_month                  = $setting[0]['opd_record_month'];
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["yesno_condition"]    = $this->yesno_condition;
        $data["bloodgroup"]         = $this->bloodbankstatus_model->get_product(null, 1);
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $patients                   = $this->patient_model->getPatientListall();
        $data["patients"]           = $patients;
        $userdata                   = $this->customlib->getUserData();
        $role_id                    = $userdata['role_id'];
        $symptoms_result            = $this->symptoms_model->get();
        $data['symptomsresult']     = $symptoms_result;
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $charge_category            = $this->charge_category_model->getCategoryByModule("opd");
        $data['charge_category']    = $charge_category;
        $doctorid                   = "";
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }

        $data['fields']         = $this->customfield_model->get_custom_fields('opd', 1);
        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $data['organisation']   = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/bill/opd/opd', $data);
        $this->load->view('layout/footer');
    }

    public function add_opd(){      

        $patient_type   = $this->customlib->getPatienttype();
        //Validation for custom fields
        $custom_fields  = $this->customfield_model->getByBelong('opd');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[opd][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        //Validation for custom fields

        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('applied_charge'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('charge_id', $this->lang->line('charge'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paid_amount', $this->lang->line('paid_amount'), 'trim|required|valid_amount|xss_clean');
        $this->form_validation->set_rules('discount_percentage', $this->lang->line('discount'), 'trim|required|valid_amount|xss_clean');

        $payment_mode = $this->input->post('payment_mode');
        if ($payment_mode == 'Cheque') {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        }
		
		$consult      = $this->input->post('live_consult');
		if($consult == 'yes'){
			if (!$this->session->has_userdata('zoom_access_token')) {
				$this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
			}
		}
		
        if ($this->form_validation->run() == false) {
            $msg = array(
                'appointment_date'    => form_error('appointment_date'),
                'consultant_doctor'   => form_error('consultant_doctor'),
                'patient_id'          => form_error('patient_id'),
                'amount'              => form_error('amount'),
                'charge_id'           => form_error('charge_id'),
                'paid_amount'         => form_error('paid_amount'),
                'cheque_no'           => form_error('cheque_no'),
                'cheque_date'         => form_error('cheque_date'),
                'document'            => form_error('document'),
                'discount_percentage' => form_error('discount_percentage'),
                'zoom_live' 		  => form_error('zoom_live'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $payment_section  = $this->config->item('payment_section');
            $doctor_id        = $this->input->post('consultant_doctor');
            $patient_id       = $this->input->post('patient_id');
            $password         = $this->input->post('password');
            $email            = $this->input->post('email');
            $mobileno         = $this->input->post('mobileno');
            $patient_name     = $this->input->post('patient_name');
            $appointment_date = $this->input->post('appointment_date');
            $isopd            = $this->input->post('is_opd');
            $appointmentid    = $this->input->post('appointment_id');
            $live_consult     = $this->input->post('live_consult');
            $date             = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);
            $opd_data = array(
                'patient_id'   => $patient_id,
                'generated_by' => $this->customlib->getStaffID(),
            );

            $custom_field_post  = $this->input->post("custom_fields[opd]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[opd][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            $this->opd_prefix = $this->customlib->getSessionPrefixByType('opd_no');
            $transaction_data = array(
                'case_reference_id' => 0,
                'opd_id'            => 0,
                'patient_id'        => $this->input->post('patient_id'),
                'amount'            => $this->input->post('paid_amount'),
                'type'              => 'payment',
                'section'           => $payment_section['opd'],
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note'),
                'payment_date'      => $date,
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );

            $attachment      = "";
            $attachment_name = "";
			
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }
			
            $cheque_date = $this->input->post("cheque_date");
            if ($this->input->post('payment_mode') == "Cheque") {
                $transaction_data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $transaction_data['cheque_no']       = $this->input->post('cheque_no');
                $transaction_data['attachment']      = $attachment;
                $transaction_data['attachment_name'] = $attachment_name;
            }
			
            $staff_data = $this->staff_model->getStaffByID($doctor_id);
            $staff_name = composeStaffName($staff_data);

            $is_tpa = $this->input->post('is_tpa');
            if($is_tpa=="" || $is_tpa==0){
                $organisation_id = null;
                $insurance_validity = null;
                $insurance_id = null;
            }else{           
                $organisation_id    = $this->input->post('organisation_id');
                $insurance_id       = $this->input->post('insurance_id');
                $insurance_validity = $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity'));
            }
			
            if (empty($organisation_id)) {
                $organisation_id = null;
            }

            $charge     = array(
                'opd_id'                          => 0,
                'date'                            => $date,
                'charge_id'                       => $this->input->post('charge_id'),
                'qty'                             => 1,
                'apply_charge'                    => $this->input->post('amount'),
                'standard_charge'                 => $this->input->post('standard_charge'),
                'tpa_charge'                      => $this->input->post('schedule_charge'),
                'amount'                          => $this->input->post('apply_amount'),
                'created_at'                      => date('Y-m-d'),
                'note'                            => $staff_name,
                'tax'                             => $this->input->post('percentage'),
                'discount_percentage'             => $this->input->post('discount_percentage'),
                'organisation_id'                 => $organisation_id,
                'insurance_validity'              => $insurance_validity,
                'insurance_id'                    => $insurance_id,
            );            

            $opd_visit_data = array(
                'appointment_date'  => $date,
                'opd_details_id'    => 0,              
                'symptoms'          => $this->input->post('symptoms'),
                'refference'        => $this->input->post('refference'),
                'cons_doctor'       => $this->input->post('consultant_doctor'),
                'casualty'          => $this->input->post('casualty'),
                'case_type'         => $this->input->post('case'),
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note'),
                'live_consult'      => $live_consult,
                'generated_by'      => $this->customlib->getLoggedInUserID(),
                'patient_charge_id' => 0,
                'transaction_id'    => 0,
                'can_delete'        => 'no',
                'known_allergies'   => $this->input->post('known_allergies'),
            );

            if($this->input->post('is_for_antenatal')!=""){
                $opd_visit_data['is_antenatal'] = $this->input->post('is_for_antenatal');
            }

            $patient_data = array('is_antenatal' => $this->input->post('is_for_antenatal'),'id'=> $patient_id);
            $this->patient_model->add($patient_data);       
 
            if ($this->input->post('symptoms_type') != "") {
                $opd_visit_data['symptoms_type'] = $this->input->post('symptoms_type');
            }

            $opdn_id          = $this->patient_model->add_opd($opd_data, $transaction_data, $charge, $opd_visit_data);
            $visit_details_id = $this->patient_model->getvisitminid($opdn_id);
            $notificationurl  = $this->notificationurl;
            $url_link         = $notificationurl["opd"];
            $setting_result   = $this->setting_model->getzoomsetting();
            $opdduration      = $setting_result->opd_duration;

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $opdn_id);
            }
            if ($live_consult == 'yes') {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );

                $title = 'Online consult for ' . $this->customlib->getSessionPrefixByType('opd_no') . $opdn_id . " Checkup ID " . $visit_details_id['visitid'];
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'         => $doctor_id,
                    'patient_id'       => $patient_id,
                    'visit_details_id' => $visit_details_id['visitid'],
                    'title'            => $title,
                    'date'             => $date,
                    'duration'         => $opdduration,
                    'created_id'       => $this->customlib->getStaffID(),
                    'password'         => $password,
                    'api_type'         => $api_type,
                    'host_video'       => 1,
                    'client_video'     => 1,
                    'purpose'          => 'consult',
                    'timezone'         => $this->customlib->getTimeZone(),
                );

                $response = $this->zoom_api->createAMeeting($insert_array);			 
				
                if ($response['status']) {
					 
                    if (isset($response['data'])) {
                        $insert_array['return_response'] = json_encode($response['data']);
                        $conferenceid                    = $this->conference_model->add($insert_array);

                        $sender_details = array('patient_id' => $patient_id, 'conference_id' => $conferenceid, 'contact_no' => $mobileno, 'email' => $email);
                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
                    }
                }
            }

            $url   = base_url() . $url_link . '/' . $patient_id . '/' . $opdn_id;
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'id' => $patient_id, 'opd_id' => $opdn_id);

            if ($this->session->has_userdata("appointment_id")) {
                $appointment_id = $this->session->userdata("appointment_id");
                $updateData     = array('id' => $appointment_id, 'is_opd' => 'yes');
                $this->appointment_model->update($updateData);
                $this->session->unset_userdata('appointment_id');
            }

            $doctor_details = $this->notificationsetting_model->getstaffDetails($this->input->post('consultant_doctor'));
            $event_data     = array(
                'patient_id'           => $patient_id,
                'symptoms_description' => $this->input->post('symptoms'),
                'any_known_allergies'  => $this->input->post('known_allergies'),
                'appointment_date'     => $this->customlib->YYYYMMDDHisTodateFormat($date, $this->time_format),
                'doctor_id'            => $this->input->post('consultant_doctor'),
                'doctor_name'          => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
            );

            $this->system_notification->send_system_notification('opd_visit_created', $event_data);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $patient_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $patient_id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }

            $sender_details = array('patient_id' => $patient_id, 'patient_name' => $patient_name, 'opd_details_id' => $opdn_id, 'contact_no' => $mobileno, 'email' => $email, 'appointment_date' => $appointment_date);
            $result         = $this->mailsmsconf->mailsms('opd_patient_registration', $sender_details);

        }
        echo json_encode($array);
    }

    public function getopddatatable()
    {
        $dt_response = $this->patient_model->getAllopdRecord();
        $fields      = $this->customfield_model->get_custom_fields('opd', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href=" . base_url() . 'admin/bill/patient_profile/' . $value->pid . " class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder' aria-hidden='true'></i></a>";
                $action .= "</div'>";
                $first_action = "<a href=" . base_url() . 'admin/bill/patient_profile/' . $value->pid . ">";
				
				if ($value->gender) {
                    $gender = $this->lang->line(strtolower($value->gender));
                } else {
                    $gender = '';
                }
				
                //==============================
                $row[] = $first_action . $value->patient_name . "</a>" . $action;
                $row[] = $value->patientid;
                $row[] = $value->guardian_name;
                $row[] = $gender;
                $row[] = $value->mobileno;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->last_visit, $this->time_format);

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
                $row[]  =  $value->total_visit;
                $row[]  =  $value->is_antenatal==1 ?  "Yes" :  "No";

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

    public function getopdvisitdatatable()
    {
        $patientid   = $this->uri->segment(4);
        $dt_response = $this->patient_model->getAllopdvisitRecord($patientid);
        $fields      = $this->customfield_model->get_custom_fields('opd', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $opd_id           = $value->opd_id;
                $visit_details_id = $value->visit_id;
                $check            = $this->db->where("visit_details_id", $visit_details_id)->get('ipd_prescription_basic');
                if ($check->num_rows() > 0) {
                    $result[$key]['prescription'] = 'yes';
                } else {
                    $result[$key]['prescription'] = 'no';
                    $userdata                     = $this->customlib->getUserData();
                    if ($this->session->has_userdata('hospitaladmin')) {
                        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                        if ($doctor_restriction == 'enabled') {
                            if ($userdata["role_id"] == 3) {
                                if ($userdata["id"] == $value["staff_id"]) {

                                } else {
                                    $result[$key]['prescription'] = 'not_applicable';
                                }
                            }
                        }
                    }
                }

                $action = "<div class='d-flex gap-1'>";
                if ($this->rbac->hasPrivilege('opd_print_bill', 'can_view')) {
                    $action .= "<a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-opd-id=" . $opd_id . " data-record-id=" . $visit_details_id . " class='btn btn-default btn-xs print_visit_bill'  data-toggle='tooltip' title='" . $this->lang->line('print_bill') . "'><i class='fa fa-print'></i></a>";
                }

                $action .= "<a href='javascript:void(0)' data-loading-text='" . $this->lang->line('please_wait') . "' data-opd-id=" . $opd_id . " data-record-id=" . $visit_details_id . " class='btn btn-default btn-xs get_opd_detail'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                $action .= "</div>";
                $first_action = "<a href=" . base_url() . 'admin/bill/opd_visit_detail/' . $value->pid . '/' . $opd_id . ">";
                    
                if($value->symptoms){
                    $symptoms   = nl2br($value->symptoms);
                }else{
                    $symptoms   = '';
                } 
                //==============================
                $row[] = $first_action . $this->opd_prefix . $opd_id . "</a>";
                $row[] = $value->case_reference_id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->appointment_date, $this->time_format);
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $value->refference;
                $row[] = $symptoms;
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

    public function patient_profile($id)
    {

        if (!$this->rbac->hasPrivilege('opd_patient', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu','bill');
        $data["marital_status"]     = $this->marital_status;
        $data["payment_mode"]       = $this->payment_mode;
        $data["yesno_condition"]    = $this->yesno_condition;
        $data["bloodgroup"]         = $this->blood_group;
        $data['medicineCategory']   = $this->medicine_category_model->getMedicineCategory();
        $category_dosage            = $this->medicine_dosage_model->getCategoryDosages();
        $data['category_dosage']    = $category_dosage;
        $data['medicineName']       = $this->pharmacy_model->getMedicineName();
        $symptoms_resulttype        = $this->symptoms_model->getsymtype();
        $data['symptomsresulttype'] = $symptoms_resulttype;
        $pathology                  = $this->pathology_model->getpathologytest();
        $data['pathology']          = $pathology;
        $radiology                  = $this->radio_model->getradiologytest();
        $data['radiology']          = $radiology;
        $data["id"]                 = $id;
        $doctors                    = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]            = $doctors;
        $userdata                   = $this->customlib->getUserData();
        $data['fields']             = $this->customfield_model->get_custom_fields('opd', 1);
        $role_id                    = $userdata['role_id'];
        $doctorid                   = "";
        $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option             = false;
        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }
        $nurseid                = "";
        $data["doctor_select"]  = $doctorid;
        $data["nurse_select"]   = $nurseid;
        $data["disable_option"] = $disable_option;
        $data['roles']          = $this->role_model->get();
        $result                 = array();
        $diagnosis_details      = array();
        $opd_details            = array();

        $timeline_list = array();
        if (!empty($id)) {
            $result            = $this->patient_model->getpatientDetails($id);
            $opd_details_id    = $this->patient_model->getopdmaxid($id);

            $timeline_list     = $this->timeline_model->getPatientTimeline($id, $timeline_status = '');
        }
        $data["result"]           = $result;
        $data["opd_details_id"]   = $opd_details_id;
        $staff_id                = $this->customlib->getStaffID();
        $data['logged_staff_id'] = $staff_id;
        $data["opd_details"]     = $opd_details;
        $data["timeline_list"]   = $timeline_list;
        $data['organisation']    = $this->organisation_model->get();
        $orgid                   = "";
        $data['org_select']      = $orgid;
        $charge_category         = $this->charge_category_model->getCategoryByModule("opd");
        $data['charge_category'] = $charge_category;
        $data['intervaldosage']  = $this->medicine_dosage_model->getIntervalDosage();
        $data['durationdosage']  = $this->medicine_dosage_model->getDurationDosage();
        $data['investigations']  = $this->patient_model->allinvestigationbypatientid($id);
        $this->load->view("layout/header");
        $this->load->view("admin/bill/opd/patient_profile", $data);
        $this->load->view("layout/footer");
    }
 
    public function opd_visit_detail($id, $opdid)
    {
        if (!empty($id)) {
            $result         = $this->patient_model->getDetails($opdid);
            $data['result'] = $result;
            $data["id"]     = $id;
            $data["opdid"]  = $opdid;
            $visit_max_id   = $this->patient_model->getvisitmaxid($opdid);
            $data['visitdata']          = $visit_max_id;
            $symptoms_resulttype        = $this->symptoms_model->getsymtype();
            $data['symptomsresulttype'] = $symptoms_resulttype;
            $doctors                    = $this->staff_model->getStaffbyrole(3);
            $data["doctors"]            = $doctors;
            $pathology                  = $this->pathology_model->getpathologytest();
            $data['pathology']          = $pathology;
            $radiology                  = $this->radio_model->getradiologytest();
            $data['radiology']          = $radiology;
            $medicationreport           = $this->patient_model->getmedicationdetailsbydateopd($opdid);
            $max_dose                   = $this->patient_model->getMaxByopdid($opdid);
            $data['max_dose']           = $max_dose->max_dose;
            $data["medication"]         = $medicationreport;
            $userdata                   = $this->customlib->getUserData();
            $role_id                    = $userdata['role_id'];
            $category_dosage            = $this->medicine_dosage_model->getCategoryDosages();
            $data['category_dosage']    = $category_dosage;
            $doctorid                   = "";
            $doctor_restriction         = $this->session->userdata['hospitaladmin']['doctor_restriction'];
            $disable_option             = false;
            if ($doctor_restriction == 'enabled') {
                if ($role_id == 3) {
                    $disable_option = true;
                    $doctorid       = $userdata['id'];
                }
            }
            $staff_id                  = $this->customlib->getStaffID();
            $data['logged_staff_id']   = $staff_id;
            $data['organisation']      = $this->organisation_model->get();
            $orgid                     = '';
            $data['org_select']        = $orgid;
            $data["doctor_select"]     = $doctorid;
            $data["disable_option"]    = $disable_option;
            $data["payment_mode"]      = $this->payment_mode;
            $data["yesno_condition"]   = $this->yesno_condition;
            $data["charge_type"]       = $this->chargetype_model->getChargeTypeByModule("opd");
            $operation_theatre         = $this->operationtheatre_model->getopdoperationDetails($opdid);
            $timeline_list             = $this->timeline_model->getPatientTimeline($id, $timeline_status = '');
            $data["timeline_list"]     = $timeline_list;
            $data['operation_theatre'] = $operation_theatre;
            $data['medicineCategory']  = $this->medicine_category_model->getMedicineCategory();
            $data['intervaldosage']    = $this->medicine_dosage_model->getIntervalDosage();
            $data['durationdosage']    = $this->medicine_dosage_model->getDurationDosage();
            $data['dosage']            = $this->medicine_dosage_model->getMedicineDosage();
            $data['medicineName']      = $this->pharmacy_model->getMedicineName();
            $charges                   = $this->charge_model->getopdCharges($opdid);            
            $paymentDetails            = $this->transaction_model->OPDPatientPayments($opdid);
            $data["charges_detail"]    = $charges;
            $data["payment_details"]   = $paymentDetails;
            $data['roles']             = $this->role_model->get();
            $getVisitDetailsid         = $this->patient_model->getVisitDetailsid($opdid);
            $data['fields']            = $this->customfield_model->get_custom_fields('opdrecheckup', 1);
            $data['ot_fields']         = $this->customfield_model->get_custom_fields('operationtheatre', 1);
            $data['opd_prefix']        = $this->opd_prefix;
            $charge_category           = $this->charge_category_model->getCategoryByModule("opd");
            $data['charge_category']   = $charge_category;
            $data['categorylist']      = $this->operationtheatre_model->category_list();
            $data["opd_data"]       = $this->patient_model->getPatientVisitDetails($id);
            $data['investigations'] = $this->patient_model->getallinvestigation($result['case_reference_id']);
            $data["bloodgroup"]     = $this->bloodbankstatus_model->get_product(null, 1);
            $data["marital_status"] = $this->marital_status;
            $data['is_discharge']   = $this->customlib->checkDischargePatient($data["result"]['discharged']);
            $this->load->view("layout/header");
            $this->load->view("admin/bill/opd/opd_visit_detail", $data);
            $this->load->view("layout/footer");
        }
    }
    
    public function getvisitdatatable($opdid)
    {
        $dt_response = $this->patient_model->getAllvisitRecord($opdid);
        $fields      = $this->customfield_model->get_custom_fields('opdrecheckup', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $opd_id           = $value->opd_id;
                $visit_details_id = $value->visit_id;
                $check = $this->db->where("visit_details_id", $visit_details_id)->get('ipd_prescription_basic');
                if ($check->num_rows() > 0) {
                    $result[$key]['prescription'] = 'yes';
                } else {
                    $result[$key]['prescription'] = 'no';
                    $userdata                     = $this->customlib->getUserData();
                    if ($this->session->has_userdata('hospitaladmin')) {
                        $doctor_restriction = $this->session->userdata['hospitaladmin']['doctor_restriction'];
                        if ($doctor_restriction == 'enabled') {
                            if ($userdata["role_id"] == 3) {
                                if ($userdata["id"] == $value["staff_id"]) {

                                } else {
                                    $result[$key]['prescription'] = 'not_applicable';
                                }
                            }
                        }
                    }
                }

                $action = "<div class=''>";

                $action .= "<a href='javascript:void(0)'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id=" . $visit_details_id . " class='btn btn-default btn-xs get_opd_detail'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                $action .= "</div>";
                //=====================
                $row[] = $this->customlib->getSessionPrefixByType('checkup_id') . $visit_details_id  ;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->appointment_date, $this->time_format);
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $value->refference;
                
                if($value->symptoms){
                    $row[] = nl2br($value->symptoms);
                }else{
                    $row[] ='';
                }
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

    public function add_revisit()
    {      
        $custom_fields = $this->customfield_model->getByBelong('opdrecheckup');  
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[opdrecheckup][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $payment_mode = $this->input->post('payment_mode');

        if ($payment_mode == 'Cheque') {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        }

        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean|valid_amount');
        $this->form_validation->set_rules('paid_amount', $this->lang->line('paid_amount'), 'trim|required|xss_clean|valid_amount');
        $this->form_validation->set_rules('charge_id', $this->lang->line('charge_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('consultant_doctor', $this->lang->line('consultant_doctor'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('discount_percentage', $this->lang->line('discount_percentage'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');	

		$consult      = $this->input->post('live_consult');
		if($consult == 'yes'){
			if (!$this->session->has_userdata('zoom_access_token')) {
				$this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
			}
		}
		
        if ($this->form_validation->run() == false) {
            $msg = array(
                'charge_id'         => form_error('charge_id'),
                'amount'            => form_error('amount'),
                'paid_amount'       => form_error('paid_amount'),
                'appointment_date'  => form_error('appointment_date'),
                'consultant_doctor' => form_error('consultant_doctor'),
                'cheque_no'         => form_error('cheque_no'),
                'cheque_date'       => form_error('cheque_date'),
                'document'          => form_error('document'),
                'discount_percentage'          => form_error('discount_percentage'),
                'zoom_live'         => form_error('zoom_live'),
            );
            $msg['cheque_no']       = form_error('cheque_no');
            $msg['cheque_date']     = form_error('cheque_date');
			
            //custom fields validation
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                     = $custom_fields_value['id'];
                        $custom_fields_name                                                   = $custom_fields_value['name'];
                        $error_msg2["custom_fields[opdrecheckup][" . $custom_fields_id . "]"] = form_error("custom_fields[opdrecheckup][" . $custom_fields_id . "]");
                    }
                }
            }

            if (!empty($error_msg2)) {
                $error_msg = array_merge($msg, $error_msg2);
            } else {
                $error_msg = $msg;
            }
            //custom fields validation

            $array = array('status' => 'fail', 'error' => $error_msg, 'message' => '');				
			
        } else {			 
				
            $check_patient_id = $this->patient_model->getMaxOPDId();
            if (empty($check_patient_id)) {
                $check_patient_id = 0;
            }
            $patient_id        = $this->input->post('patientid');
            $password          = $this->input->post('password');
            $email             = $this->input->post('email');
            $mobileno          = $this->input->post('mobileno');
            $opdn_id           = $check_patient_id + 1;
            $custom_field_post = $this->input->post("custom_fields[opd]");
            $appointment_date  = $this->input->post('appointment_date');
            $consult           = $this->input->post('live_consult');
            if ($consult) {
                $live_consult = $this->input->post('live_consult');
            } else {
                $live_consult = 'no';
            }
            $doctor_id = $this->input->post("consultant_doctor");
            $date      = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);
            $opd_data  = array(
                'patient_id'   => $patient_id,
                'generated_by' => $this->customlib->getLoggedInUserID(),
            );

            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[opd][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
            $payment_section = $this->config->item('payment_section');

            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }

            $cheque_date      = $this->input->post("cheque_date");
            $transaction_data = array(
                'case_reference_id' => 0,
                'opd_id'            => 0,
                'amount'            => $this->input->post('paid_amount'),
                'type'              => 'payment',
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note'),
                'payment_date'      => $date,
                'patient_id'        => $this->input->post('patientid'),
                'section'           => $payment_section['opd'],
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );

            if ($this->input->post('payment_mode') == "Cheque") {
                $transaction_data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $transaction_data['cheque_no']       = $this->input->post('cheque_no');
                $transaction_data['attachment']      = $attachment;
                $transaction_data['attachment_name'] = $attachment_name;
            }

            $staff_data = $this->staff_model->getStaffByID($doctor_id);
            $staff_name = composeStaffName($staff_data);

            $is_tpa = $this->input->post('is_tpa');
            if($is_tpa=="" || $is_tpa==0){
                $organisation_id = null;
                $insurance_validity = null;
                $insurance_id = null;
            }else{           
                $organisation_id = $this->input->post('organisation');
                $insurance_id = $this->input->post('insurance_id');
                $insurance_validity = $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity'));
            }
           
            if (empty($organisation_id)) {
                $organisation_id = null;
            }

            $charge     = array(
                'opd_id'                => 0,
                'date'                  => $date,
                'charge_id'             => $this->input->post('charge_id'),
                'qty'                   => 1,
                'apply_charge'          => $this->input->post('amount'),
                'standard_charge'       => $this->input->post('standard_charge'),
                'tpa_charge'            => $this->input->post('schedule_charge'),
                'amount'                => $this->input->post('apply_amount'),
                'tax'                   => $this->input->post('percentage'),
                'discount_percentage'   => $this->input->post('discount_percentage'),
                'created_at'            => date('Y-m-d'),
                'note'                  => $staff_name,
                'organisation_id'       => $organisation_id,
                'insurance_validity'    => $insurance_validity,
                'insurance_id'          => $insurance_id,
            );        
          
            $opd_visit_data = array(
                'appointment_date' => $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format),               
                'symptoms'         => $this->input->post('symptoms'),
                'known_allergies'  => $this->input->post('known_allergies'),
                'patient_old'      => $this->input->post('old_patient'),
                'refference'       => $this->input->post('refference'),
                'cons_doctor'      => $this->input->post('consultant_doctor'),
                'symptoms_type'    => $this->input->post('symptoms_type'),
                'casualty'         => $this->input->post('casualty'),
                'payment_mode'     => $this->input->post('payment_mode'),
                'note'             => $this->input->post('note_remark'),
                'live_consult'     => $live_consult,
                'can_delete'       => 'no',
                'generated_by'     => $this->customlib->getLoggedInUserID(),
            );
			
            if($this->input->post('is_for_antenatal')!=""){
                $opd_visit_data['is_antenatal'] = $this->input->post('is_for_antenatal') ;
            }

            $opdn_id         = $this->patient_model->add_opd($opd_data, $transaction_data, $charge, $opd_visit_data);
            $visit_max_id    = $this->patient_model->getvisitmaxid($opdn_id);
            $visitid         = $visit_max_id['visitid'];
            $notificationurl = $this->notificationurl;
            $url_link        = $notificationurl["opd"];
            $url             = base_url() . $url_link . '/' . $patient_id . '/' . $opdn_id;
            $setting_result  = $this->setting_model->getzoomsetting();
            $opdduration     = $setting_result->opd_duration;
            
            if ($live_consult != 'no') {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'         => $doctor_id,
                    'visit_details_id' => $visitid,
                    'visit_details_id' => $visitid,
                    'title'            => 'Online consult for Revisit OPDN' . $opdn_id,
                    'date'             => $date,
                    'duration'         => $opdduration,
                    'created_id'       => $this->customlib->getStaffID(),
                    'password'         => $password,
                    'api_type'         => $api_type,
                    'host_video'       => 1,
                    'client_video'     => 1,
                    'purpose'          => 'consult',
                    'timezone'         => $this->customlib->getTimeZone(),
                );
                $response         = $this->zoom_api->createAMeeting($insert_array);
                $appointment_date = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);
				
				if ($response['status']) {
					 
                    if (isset($response['data'])) {
                        $insert_array['return_response'] = json_encode($response['data']);
						 
                        $conferenceid   = $this->conference_model->add($insert_array);
                        $sender_details = array('patient_id' => $patient_id, 'conference_id' => $conferenceid, 'contact_no' => $mobileno, 'email' => $email);
                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
						 
                    }
                }                 
            }
            
            $sender_details = array('patient_id' => $patient_id, 'opd_details_id' => $opdn_id, 'contact_no' => $mobileno, 'email' => $email, 'appointment_date' => $appointment_date);
            $this->mailsmsconf->mailsms('opd_patient_registration', $sender_details);
            $array = array('status' => 'success', 'error' => '', 'id' => $opdn_id, 'message' => $this->lang->line('success_message'));
        }		
	 
        echo json_encode($array);
    }

    public function addvisitDetails()
    {
        $custom_fields = $this->customfield_model->getByBelong('opdrecheckup'); 

        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[opdrecheckup][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        $this->form_validation->set_rules('charge_id', $this->lang->line('charge'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('apply_amount', $this->lang->line('amount'), 'trim|required|xss_clean|valid_amount');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('applied_charge'), 'trim|required|xss_clean|valid_amount');
        $this->form_validation->set_rules('appointment_date', $this->lang->line('appointment_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('paid_amount', $this->lang->line('paid_amount'), 'trim|required|xss_clean|valid_amount');
		
		$consult      = $this->input->post('live_consult');
		if($consult == 'yes'){
			if (!$this->session->has_userdata('zoom_access_token')) {
				$this->form_validation->set_rules('zoom_live', $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting'), 'trim|required|xss_clean', array('required' => $this->lang->line('zoom_access_token_is_invalid_or_not_generated_from_zoom_setting')));
			}
		}
		
        if ($this->form_validation->run() == false) {
            $msg = array(
                'firstname'        => form_error('name'),
                'appointment_date' => form_error('appointment_date'),
                'amount'           => form_error('amount'),
                'charge_id'        => form_error('charge_id'),
                'apply_amount'     => form_error('apply_amount'),
                'paid_amount'      => form_error('paid_amount'),
                'zoom_live'        => form_error('zoom_live'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {
            $check_patient_id = $this->patient_model->getMaxOPDId();
            if (empty($check_patient_id)) {
                $check_patient_id = 0;
            }
            $opdn_id           = $check_patient_id + 1;
            $patient_id        = $this->input->post('id');
            $password          = $this->input->post('password');
            $custom_field_post = $this->input->post("custom_fields[opdrecheckup]");
            $appointment_date  = $this->input->post('appointment_date');
            $consult           = $this->input->post('live_consult');
            $doctor_id         = $this->input->post('consultant_doctor');
            $opd_id            = $this->input->post('opd_id');
            if ($consult) {
                $live_consult = $this->input->post('live_consult');
            } else {
                $live_consult = "no";
            }

            $date            = $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format);          
            $revisit_is_tpa  = $this->input->post('revisit_is_tpa');

        if($revisit_is_tpa=="" || $revisit_is_tpa==0){
            $organisation_id = null;
            $insurance_validity = null;
            $insurance_id = null;
        }else{           
            $organisation_id = $this->input->post('organisation');
            $insurance_validity = $this->input->post('insurance_validity');
            $insurance_id = $this->input->post('insurance_id');
        }
           
            if (empty($organisation_id)) {
                $organisation_id = null;
            }
            $opd_data = array(
                'appointment_date'  => $date,
                'opd_details_id'    => $opd_id,                
                'case_type'         => $this->input->post('revisit_case'),
                'symptoms'          => $this->input->post('symptoms'),
                'known_allergies'   => $this->input->post('known_allergies'),
                'refference'        => $this->input->post('refference'),
                'cons_doctor'       => $this->input->post('consultant_doctor'),
                'casualty'          => $this->input->post('casualty'),
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note_remark'),
                'live_consult'      => $live_consult,
                'patient_charge_id' => null,
                'transaction_id'    => null,
                'can_delete'        => 'yes',
                'generated_by'      => $this->customlib->getLoggedInUserID(),
            );
			
            if($this->input->post('is_for_antenatal')!=""){
                $opd_data['is_antenatal'] = $this->input->post('is_for_antenatal') ;
            }

            $payment_section  = $this->config->item('payment_section');
            $transaction_data = array(
                'case_reference_id' => $this->input->post('case_reference_id'),
                'opd_id'            => $this->input->post('opd_id'),
                'amount'            => $this->input->post('paid_amount'),
                'type'              => 'payment',
                'payment_mode'      => $this->input->post('payment_mode'),
                'note'              => $this->input->post('note'),
                'patient_id'        => $this->input->post('id'),
                'section'           => $payment_section['opd'],
                'payment_date'      => $date,
                'cheque_date'       => $date,
                'cheque_no'         => $this->input->post('cheque_no'),
                'received_by'       => $this->customlib->getLoggedInUserID(),
            );

            if ($this->input->post('payment_mode') == "Cheque") {
                $cheque_date                     = $this->customlib->dateFormatToYYYYMMDD($this->input->post("cheque_date"));
                $transaction_data['cheque_date'] = $cheque_date;
                $transaction_data['cheque_no']   = $this->input->post('cheque_no');
                if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                    $fileInfo        = pathinfo($_FILES["document"]["name"]);
                    $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                    $attachment_name = $_FILES["document"]["name"];
                    move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
                    $transaction_data['attachment']      = $attachment;
                    $transaction_data['attachment_name'] = $attachment_name;
                }
            }

            $staff_data = $this->staff_model->getStaffByID($doctor_id);
            $staff_name = composeStaffName($staff_data);
            $charge     = array(
                'opd_id'          => $this->input->post('opd_id'),
                'date'            => $date,
                'charge_id'       => $this->input->post('charge_id'),
                'qty'             => 1,
                'apply_charge'    => $this->input->post('amount'),
                'standard_charge' => $this->input->post('standard_charge'),
                'tpa_charge'      => $this->input->post('schedule_charge'),
                'amount'          => $this->input->post('apply_amount'),
                'created_at'      => date('Y-m-d'),
                'note'            => $staff_name,
                'organisation_id' => $organisation_id,
                'insurance_validity' => $insurance_validity,
                'insurance_id'    => $insurance_id,
            );

            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[opdrecheckup][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }
			
            $opdvisit_id = $this->patient_model->add_visit_recheckup($opd_data, $transaction_data, $charge);

            if (!empty($custom_value_array)) {
                $this->customfield_model->insertRecord($custom_value_array, $opdvisit_id);
            }

            $live_consult   = $this->input->post('live_consult');
            $doctor_id      = $this->input->post('consultant_doctor');
            $setting_result = $this->setting_model->getzoomsetting();
            $opdduration    = $setting_result->opd_duration;
            if ($live_consult = 'yes') {
                $api_type = 'global';
                $params   = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
                $this->load->library('zoom_api', $params);
                $insert_array = array(
                    'staff_id'         => $doctor_id,
                    'visit_details_id' => $opdvisit_id,
                    'title'            => 'Online consult for Checkup ID ' . $opdvisit_id,
                    'date'             => $this->customlib->dateFormatToYYYYMMDDHis($appointment_date, $this->time_format),
                    'duration'         => $opdduration,
                    'created_id'       => $this->customlib->getStaffID(),
                    'password'         => $password,
                    'api_type'         => $api_type,
                    'host_video'       => 1,
                    'client_video'     => 1,
                    'purpose'          => 'consult',
                    'timezone'         => $this->customlib->getTimeZone(),
                );
                $response = $this->zoom_api->createAMeeting($insert_array);

                if (!empty($response)) {
                    if (isset($response->id)) {
                        $insert_array['return_response'] = json_encode($response);
                        $conferenceid                    = $this->conference_model->add($insert_array);
                        $sender_details                  = array('patient_id' => $patient_id, 'conference_id' => $conferenceid, 'contact_no' => $this->input->post('contact'), 'email' => $this->input->post('email'));

                        $this->mailsmsconf->mailsms('live_consult', $sender_details);
                    }
                }
            }

            $sender_details = array('patient_id' => $patient_id, 'opd_no' => $this->customlib->getSessionPrefixByType('opd_no') . $opd_id, 'contact_no' => $this->input->post('contact'), 'email' => $this->input->post('email'));

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    //==========Blood Bank========

    public function issueblood()
    {
        if (!$this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) {
            access_denied();
        }
        $doctors              = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]      = $doctors;
        $patients             = $this->patient_model->getPatientListall();
        $data["patients"]     = $patients;
        $data["payment_mode"] = $this->payment_mode;
        $data["charge_type"]  = $this->chargetype_model->get();
        $data["bloodgroup"]   = $this->bloodbankstatus_model->get_product('', 1);
        $data['fields']       = $this->customfield_model->get_custom_fields('blood_issue', 1);
        $result               = $this->bloodissue_model->getBloodIssue();
        $data['result']       = $result;
        $this->load->view('layout/header');
        $this->load->view('admin/bill/bloodbank/bloodissue', $data);
        $this->load->view('layout/footer');
    }

    public function getbloodissueDatatable()
    {
        $fields      = $this->customfield_model->get_custom_fields('blood_issue', 1);
        $dt_response = $this->bloodissue_model->getAllbloodissueRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();

        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-btn-top'>";
                $action .= "<a href='#' data-record-id='" . $value->id . "' class='btn btn-default btn-xs viewDetail' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";

                $action .= "<a href='javascript:void(0)'  data-caseid='' data-module='blood_bank' data-record-id='" . $value->id . "' class='btn btn-default btn-xs add_payment' data-toggle='tooltip' title='" . $this->lang->line('add_payment') . "' ><i class='fa fa-plus'></i></a>";

                if ($this->rbac->hasPrivilege('blood_issue', 'can_delete')) {
                    $action .= "<a  class='btn btn-default btn-xs delete_blood_issue' data-toggle='tooltip' title='" . $this->lang->line('delete_payment') . "' data-record-id='" . $value->id . "'  data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $action .= "</div>";
				
				if ($value->gender) {
                    $gender = $this->lang->line(strtolower($value->gender));
                } else {
                    $gender = '';
                }
				
                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value->id . $action;
                $row[] = $value->case_reference_id;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->date_of_issue, false);
                $row[] = $value->patient_name . " (" . $value->patient_id . ")";
                $row[] = $value->blood_group;
                $row[] = $gender;
                $row[] = $value->donor_name;
                $row[] = $this->customlib->bag_string($value->bag_no, $value->volume, $value->unit);
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
                $row[]     = $value->net_amount;
                $row[]     = $value->paid_amount;
                $row[]     = amountFormat($value->net_amount - $value->paid_amount);
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

    public function allotblood()
    {
        $data                    = array();
        $doctors                 = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]         = $doctors;
        $patients                = $this->patient_model->getPatientListall();
        $data["patients"]        = $patients;
        $data["payment_mode"]    = $this->payment_mode;
        $data["charge_type"]     = $this->chargetype_model->get();
        $data["stockbloodgroup"] = $this->bloodbankstatus_model->get_stock_bloodgroup();
        $page                    = $this->load->view('admin/bill/bloodbank/_allotblood', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function save_blood_issue()
    {
        $id = $this->input->post('id');
        $this->form_validation->set_rules('date_of_issue', $this->lang->line('issue_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('reference', $this->lang->line('reference_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('bag_no', $this->lang->line('bag_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('charge_id', $this->lang->line('charges'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge_category'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('discount', $this->lang->line('discount'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('discount_percent', $this->lang->line('discount_percentage'), 'required|numeric');
        if (!isset($id)) {
            $this->form_validation->set_rules(
                'payment_amount', $this->lang->line('payment_amount'), array('trim', 'required', 'xss_clean', 'valid_amount',
                    array('check_exists', array($this->bloodbankstatus_model, 'validate_paymentamount')),
                )
            );

            if ($this->input->post('payment_mode') == "Cheque") {
                $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_upload');
            }
        }

        $custom_fields = $this->customfield_model->getByBelong('blood_issue');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[blood_issue][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'date_of_issue'    => form_error('date_of_issue'),
                'patient_id'       => form_error('patient_id'),
                'reference'        => form_error('reference'),
                'bag_no'           => form_error('bag_no'),
                'total'            => form_error('total'),
                'net_amount'       => form_error('net_amount'),
                'tax'              => form_error('tax'),
                'tax_percentage'   => form_error('tax_percentage'),
                'discount'         => form_error('discount'),
                'discount_percent' => form_error('discount_percent'),
                'charge_id'        => form_error('charge_id'),
                'charge_category'  => form_error('charge_category'),
            );
            if (!isset($id)) {
                $msg['cheque_no']      = form_error('cheque_no');
                $msg['cheque_date']    = form_error('cheque_date');
                $msg['payment_amount'] = form_error('payment_amount');
                $msg['document']       = form_error('document');
            }
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                    = $custom_fields_value['id'];
                        $custom_fields_name                                                  = $custom_fields_value['name'];
                        $error_msg2["custom_fields[blood_issue][" . $custom_fields_id . "]"] = form_error("custom_fields[blood_issue][" . $custom_fields_id . "]");
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
            $case_reference_id = $this->input->post('case_reference_id');
            $issue_date        = $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('date_of_issue'), $this->time_format);
            $patient_id        = $this->input->post('patient_id');
            $is_tpa            = $this->input->post('is_tpa');
            if($is_tpa=="" || $is_tpa==0){
                $organisation_id    = null;
                $insurance_validity = null;
                $insurance_id       = null;
            }else{           
                $organisation_id    = $this->input->post('organisation_id');
                $insurance_id       = $this->input->post('insurance_id');                
                $insurance_validity = $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity'));
            }
            if (empty($organisation_id)) {
                $organisation_id = null;                
            }
            
            $bloodissue        = array(
                'date_of_issue'        => $issue_date,
                'patient_id'           => $patient_id,
                'technician'           => $this->input->post('technician'),
                'hospital_doctor'      => $this->input->post('consultant_doctor'),
                'reference'            => $this->input->post('reference'),
                'blood_donor_cycle_id' => $this->input->post('bag_no'),
                'generated_by'         => $this->session->userdata('hospitaladmin')['id'],
                'remark'               => $this->input->post('note'),
                'charge_id'            => $this->input->post('charge_id'),
                'standard_charge'      => $this->input->post('standard_charge'),
                'amount'               => $this->input->post('total'),
                'net_amount'           => $this->input->post('net_amount'),
                'tax_percentage'       => $this->input->post('tax_percentage'),
                'discount_percentage'  => $this->input->post('discount_percent'),
                'organisation_id'      => $organisation_id,
                'insurance_id'         => $insurance_id,
                'insurance_validity'   => $insurance_validity,
            );

            if ($case_reference_id != '') {
                $bloodissue['case_reference_id'] = $case_reference_id;
            }

            $chequedate       = $this->input->post('cheque_date');
            $payment_section  = $this->config->item('payment_section');
            $cheque_date      = $this->customlib->dateFormatToYYYYMMDD($chequedate);
            $transaction_data = array(
                'patient_id'   => $patient_id,
                'section'      => $payment_section['blood_bank'],
                'amount'       => $this->input->post('payment_amount'),
                'type'         => 'payment',
                'payment_mode' => $this->input->post('payment_mode'),
                'note'         => $this->input->post('note'),
                'payment_date' => $issue_date,
                'received_by'  => $this->session->userdata('hospitaladmin')['id'],
            );
            if (!empty($this->input->post('case_reference_id')) && $this->input->post('case_reference_id') != "") {
                $payment_array['case_reference_id'] = $this->input->post('case_reference_id');
            }
            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }

            if ($this->input->post('payment_mode') == "Cheque") {
                $transaction_data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $transaction_data['cheque_no']       = $this->input->post('cheque_no');
                $transaction_data['attachment']      = $attachment;
                $transaction_data['attachment_name'] = $attachment_name;
            }

            if (isset($id)) {
                $bloodissue['id'] = $id;
                $transaction_data = array();
            }
            $insert_id          = $this->bloodissue_model->add($bloodissue, $transaction_data);
            $custom_field_post  = $this->input->post("custom_fields[blood_issue]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                if ($id > 0) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[blood_issue][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $id,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                } else {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[blood_issue][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $insert_id,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                }
            }

            if (!empty($custom_value_array)) {
                if ($id > 0) {
                    $this->customfield_model->updateRecord($custom_value_array, $id, 'blood_issue');
                } else {
                    $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                }
            }

            $charge_details = $this->notificationsetting_model->getchargeDetails($this->input->post('charge_id'));
            $issue_details  = $this->bloodissue_model->getDetail($insert_id);

            $event_data = array(
                'case_id'        => $case_reference_id,
                'patient_id'     => $patient_id,
                'bill_no'        => $this->customlib->getSessionPrefixByType('blood_bank_billing') . $insert_id,
                'issue_date'     => $issue_date,
                'reference_name' => $this->input->post('reference'),
                'blood_group'    => $issue_details['blood_group'],
                'bag'            => $issue_details['bag_no'] . " (" . $issue_details['volume'] . " " . $issue_details['unit_name'] . ")",
                'charge_name'    => $charge_details['name'],
                'total'          => $this->input->post('total'),
                'discount'       => $this->input->post('discount_percent'),
                'tax'            => $this->input->post('tax_percentage'),
                'net_amount'     => $this->input->post('net_amount'),
            );

            $this->system_notification->send_system_notification('blood_issue', $event_data);
            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }
    //============blood component==========
    public function issuecomponent()
    {
        if (!$this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) {
            access_denied();
        }
        $doctors              = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]      = $doctors;
        $patients             = $this->patient_model->getPatientListall();
        $data["patients"]     = $patients;
        $data["payment_mode"] = $this->payment_mode;
        $data["charge_type"]  = $this->chargetype_model->get();
        $data["bloodgroup"]   = $this->bloodbankstatus_model->get_product('', 1);
        $result               = $this->bloodissue_model->getBloodIssue();
        $data['fields']       = $this->customfield_model->get_custom_fields('component_issue', 1);
        $data['result']       = $result;
        $this->load->view('layout/header');
        $this->load->view('admin/bill/bloodbank/issuecomponent', $data);
        $this->load->view('layout/footer');
    }

    public function getcomponentissueDatatable()
    {
        $fields      = $this->customfield_model->get_custom_fields('component_issue', 1);
        $dt_response = $this->bloodissue_model->getAllcomponentissueRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='#' data-record-id='" . $value->id . "' class='btn btn-default btn-xs viewDetail'  data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('blood_bank_partial_payment', 'can_view')) {
                    $action .= "<a href='javascript:void(0)'  data-caseid='' data-module='blood_bank' data-record-id='" . $value->id . "' class='btn btn-default btn-xs add_payment' data-toggle='tooltip' title='" . $this->lang->line('add_payment') . "' ><i class='fa fa-plus'></i></a>";
                }
                if ($this->rbac->hasPrivilege('issue_component', 'can_delete')) {
                    $action .= "<a  class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='deleterecord(" . $value->id . ")' data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }
                $action .= "</div>";
                $prefix = $this->customlib->getSessionPrefixByType('blood_bank_billing') . $value->id . $action;

                //==============================
                $row[] = $prefix;
                $row[] = $value->case_reference_id;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($value->date_of_issue, false);
                $row[] = $value->patient_name . " (" . $value->patient_id . ")";
                $row[] = $value->blood_group_name;
                $row[] = $value->component_name;
                $row[] = $this->lang->line(strtolower($value->gender));
                $row[] = $value->donor_name;
                $row[] = $this->customlib->bag_string($value->bag_no, $value->volume, $value->unit);
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
                $row[]     = $value->net_amount;
                $row[]     = $value->paid_amount;
                $row[]     = amountFormat($value->net_amount - $value->paid_amount);
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
    public function allotcomponent()
    {
        $data                    = array();
        $doctors                 = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]         = $doctors;
        $patients                = $this->patient_model->getPatientListall();
        $data["patients"]        = $patients;
        $data["payment_mode"]    = $this->payment_mode;
        $data["charge_type"]     = $this->chargetype_model->get();
        $data["stockbloodgroup"] = $this->bloodbankstatus_model->get_stock_bloodgroup();
        $data["blood_component"] = $this->bloodbankstatus_model->get_product(null, 2);
        $page                    = $this->load->view('admin/bill/bloodbank/_allotcomponent', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function bloodbank_transactions()
    {
        $billing_id                 = $this->input->post('id');
        $data['patient_id']         = $this->input->post('patient_id');
        $data['blood_issue_detail'] = $this->bloodissue_model->getDetail($billing_id);
        $transaction                = $this->transaction_model->bloodbankPayments($billing_id);
        $data["billing_id"]         = $billing_id;
        $data["payment_mode"]       = $this->payment_mode;
        $data['transaction']        = $transaction;
        $page                       = $this->load->view("admin/bill/bloodbank/_bloodbank_transactions", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function save_issue_component()
    {
        $id = $this->input->post('id');
        $this->form_validation->set_rules('date_of_issue', $this->lang->line('issue_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('reference', $this->lang->line('reference_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('bag_no', $this->lang->line('bag_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('charge_id', $this->lang->line('charges'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge_category'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('discount', $this->lang->line('discount'), 'trim|required|xss_clean|numeric');
        $this->form_validation->set_rules('discount_percent', $this->lang->line('discount_percentage'), 'required|numeric');
        if (!isset($id)) {
            $this->form_validation->set_rules(
                'payment_amount', $this->lang->line('payment_amount'), array('required', 'xss_clean', 'valid_amount',
                    array('check_exists', array($this->bloodbankstatus_model, 'validate_paymentamount')),
                )
            );
            if ($this->input->post('payment_mode') == "Cheque") {
                $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_upload');
            }
        }

        $custom_fields = $this->customfield_model->getByBelong('component_issue');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[component_issue][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
		
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date_of_issue'    => form_error('date_of_issue'),
                'patient_id'       => form_error('patient_id'),
                'reference'        => form_error('reference'),
                'bag_no'           => form_error('bag_no'),
                'total'            => form_error('total'),
                'net_amount'       => form_error('net_amount'),
                'tax'              => form_error('tax'),
                'tax_percentage'   => form_error('tax_percentage'),
                'discount'         => form_error('discount'),
                'discount_percent' => form_error('discount_percent'),
                'charge_id'        => form_error('charge_id'),
                'charge_category'  => form_error('charge_category'),
            );

            if (!isset($id)) {
                $msg['cheque_no']      = form_error('cheque_no');
                $msg['cheque_date']    = form_error('cheque_date');
                $msg['payment_amount'] = form_error('payment_amount');
                $msg['document']       = form_error('document');
            }

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                        = $custom_fields_value['id'];
                        $custom_fields_name                                                      = $custom_fields_value['name'];
                        $error_msg2["custom_fields[component_issue][" . $custom_fields_id . "]"] = form_error("custom_fields[component_issue][" . $custom_fields_id . "]");
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

            $case_reference_id = $this->input->post('case_reference_id');
            $issue_date        = $this->input->post('date_of_issue');
            $patient_id        = $this->input->post('patient_id');
              $is_tpa = $this->input->post('is_tpa');
            if($is_tpa=="" || $is_tpa==0){
                $organisation_id    = null;
                $insurance_validity = null;
                $insurance_id       = null;
            }else{           
                $organisation_id    = $this->input->post('organisation_id');
                $insurance_id       = $this->input->post('insurance_id');
                $insurance_validity = $this->customlib->dateFormatToYYYYMMDD($this->input->post('insurance_validity'));
            }
            if (empty($organisation_id)) {
                $organisation_id = null;
            }
            $bloodissue        = array(
                'date_of_issue'        => $this->customlib->dateFormatToYYYYMMDDHis($issue_date, $this->time_format),
                'patient_id'           => $patient_id,
                'technician'           => $this->input->post('technician'),
                'reference'            => $this->input->post('reference'),
                'blood_donor_cycle_id' => $this->input->post('bag_no'),
                'generated_by'         => $this->session->userdata('hospitaladmin')['id'],
                'remark'               => $this->input->post('note'),
                'charge_id'            => $this->input->post('charge_id'),
                'standard_charge'      => $this->input->post('standard_charge'),
                'amount'               => $this->input->post('total'),
                'net_amount'           => $this->input->post('net_amount'),
                'tax_percentage'       => $this->input->post('tax_percentage'),
                'remark'               => $this->input->post('note'),
                'discount_percentage'  => $this->input->post('discount_percent'),
                'organisation_id'      => $organisation_id,
                'insurance_id'         => $insurance_id,
                'insurance_validity'   => $insurance_validity,
            );

            if ($case_reference_id != '') {
                $bloodissue['case_reference_id'] = $case_reference_id;
            }

            $chequedate       = $this->input->post('cheque_date');
            $cheque_date      = $this->customlib->dateFormatToYYYYMMDD($chequedate);
            $payment_section  = $this->config->item('payment_section');
            $transaction_data = array(
                'amount'       => $this->input->post('payment_amount'),
                'patient_id'   => $this->input->post('patient_id'),
                'section'      => $payment_section['blood_bank'],
                'type'         => 'payment',
                'payment_mode' => $this->input->post('payment_mode'),
                'payment_date' => $this->customlib->dateFormatToYYYYMMDD($issue_date),
                'received_by'  => $this->session->userdata('hospitaladmin')['id'],
            );

            if (!empty($this->input->post('case_reference_id')) && $this->input->post('case_reference_id') != "") {
                $transaction_data['case_reference_id'] = $this->input->post('case_reference_id');
            }

            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }

            if ($this->input->post('payment_mode') == "Cheque") {
                $transaction_data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $transaction_data['cheque_no']       = $this->input->post('cheque_no');
                $transaction_data['attachment']      = $attachment;
                $transaction_data['attachment_name'] = $attachment_name;
            }

            if (isset($id)) {
                $bloodissue['id'] = $id;
                $transaction_data = array();
            }

            $insert_id           = $this->bloodissue_model->add($bloodissue, $transaction_data);
            $blood_issue_details = $this->bloodissue_model->getcomponentDetail($insert_id);
            $custom_field_post   = $this->input->post("custom_fields[component_issue]");
            $custom_value_array  = array();
            if (!empty($custom_field_post)) {
                if ($id > 0) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[component_issue][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $id,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                } else {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[component_issue][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $insert_id,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                }
            }

            if (!empty($custom_value_array)) {
                if ($id > 0) {
                    $this->customfield_model->updateRecord($custom_value_array, $id, 'component_issue');
                } else {
                    $this->customfield_model->insertRecord($custom_value_array, $insert_id);
                }
            }

            $charge_details = $this->notificationsetting_model->getchargeDetails($this->input->post('charge_id'));

            $event_data = array(
                'case_id'        => $case_reference_id,
                'patient_id'     => $patient_id,
                'bill_no'        => $this->customlib->getSessionPrefixByType('blood_bank_billing') . $insert_id,
                'issue_date'     => $this->customlib->YYYYMMDDHisTodateFormat($issue_date, $this->time_format),
                'reference_name' => $this->input->post('reference'),
                'blood_group'    => $blood_issue_details['blood_group_name'],
                'component'      => $blood_issue_details['component_name'],
                'bag'            => $this->customlib->bag_string($blood_issue_details['bag_no'], $blood_issue_details['volume'], $blood_issue_details['unit']),
                'charge_name'    => $charge_details['name'],
                'total'          => number_format((float) $this->input->post('total'), 2, '.', ''),
                'discount'       => number_format((float) $this->input->post('discount_percent'), 2, '.', ''),
                'tax'            => $this->input->post('tax_percentage'),
                'net_amount'     => number_format((float) $this->input->post('net_amount'), 2, '.', ''),
            );

            $this->system_notification->send_system_notification('component_issue', $event_data);
            $array = array('status' => 'success', 'id' => $insert_id, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    } 

    //========Appointment==============

    public function appointment()
    {
        $this->session->set_userdata('top_menu', 'bill');
        $app_data                      = $this->session->flashdata('app_data');
        $data['app_data']              = $app_data;
        $doctors                       = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]               = $doctors;
        $patients                      = $this->patient_model->getPatientListall();
        $data["patients"]              = $patients;
        $data["appointment_status"]    = $this->appointment_status;
        $data["yesno_condition"]       = $this->yesno_condition;
        $userdata                      = $this->customlib->getUserData();
        $role_id                       = $userdata['role_id'];
        $data["bloodgroup"]            = $this->bloodbankstatus_model->get_product(null, 1);
        $doctorid                      = "";
        $data['appoint_priority_list'] = $this->appoint_priority_model->appoint_priority_list();
        $doctor_restriction            = $this->session->userdata['hospitaladmin']['doctor_restriction'];
        $disable_option                = false;

        if ($doctor_restriction == 'enabled') {
            if ($role_id == 3) {
                $disable_option = true;
                $doctorid       = $userdata['id'];
            }
        }

        $data["doctor_select"]  = $doctorid;
        $data["disable_option"] = $disable_option;
        $data['fields']         = $this->customfield_model->get_custom_fields('appointment', 1);
        $data['payment_mode']   = $this->payment_mode;        
        $data['organisation']       = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/bill/appointment/appointment', $data);
        $this->load->view('layout/footer');
    }

    public function getappointmentdatatable()
    {
        $dt_response = $this->appointment_model->getAllappointmentRecord();
       
        $fields      = $this->customfield_model->get_custom_fields('appointment', 1);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                $label = "";               
                
                if ($value->appointment_status == "approved") {
                    $label  = "class='label label-success'";
                    $status = $this->customlib->getSessionPrefixByType('appointment') . $value->id;
                } else if ($value->appointment_status == "pending") {
                    $label  = "class='label label-warning'";
                    $status = $this->lang->line($value->appointment_status);
                } else if ($value->appointment_status == "cancel") {
                    $label  = "class='label label-danger'";
                    $status = $this->lang->line($value->appointment_status);
                }               

                $action = "<div class='rowoptionview rowview-btn-top'>";
                $action .= "<a href='#' data-toggle='tooltip' title='" . $this->lang->line('show') . "' class='btn btn-default btn-xs'   data-target='#viewModal' onclick='viewDetail(" . $value->id . ")'>  <i class='fa fa-reorder'></i> </a>";
                $action .="<a href='#'  class='btn btn-default btn-xs' data-toggle='tooltip'  onclick='printAppointment(" . $value->id .")' data-original-title=".$this->lang->line('print')."><i class='fa fa-print'></i></a>";

                $action .= " <a href='#' data-toggle='tooltip' title='" . $this->lang->line('reschedule') . "' class='btn btn-default btn-xs'   data-target='#rescheduleModal' onclick='viewreschedule(" . $value->id . ",1)'>  <i class='fa fa-calendar'></i> </a>";

                if ($value->appointment_status == 'pending') {
                    if ($value->source != 'Online') {
                        if ($this->rbac->hasPrivilege('appointment_approve', 'can_view')) {                           
                            
                            $action .= "<span class='large-tooltip'><a href='#' class='btn btn-default btn-xs'  data-toggle='tooltip' title='' data-target='#rescheduleModal' onclick='viewreschedule(" . $value->id . ",2)' data-original-title='" . $this->lang->line('approve_appointment') . "'><i class='fa fa-check' aria-hidden='true'></i></a></span>";                            

                        }
                    }
                }

                $action .= "</div>";
                $first_action = "<a  href='javascript:void(0)' data-toggle='tooltip'  data-target='#viewModal' title=''  onclick='viewDetail(" . $value->id . ")'>";

                if (!empty($value->live_consult)) {$live_consult = $this->lang->line($value->live_consult);} else { $live_consult = '';};
				
				if ($value->gender) {
                    $gender = $this->lang->line(strtolower($value->gender));
                } else {
                    $gender = '';
                }
				
                //==============================
                $row[] = $first_action . composePatientName($value->patient_name, $value->pid) . "</a>" . $action;
                $row[] = $status;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->mobileno;
                $row[] = $gender;
                $row[] = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[] = $this->lang->line(strtolower($value->source));
                $row[] = $value->priorityname;
                if ($this->module_lib->hasActive('live_consultation')) {
                    $row[] = $live_consult;
                }
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
                $row[]     = $value->discount_percentage;
                $row[]     = $value->paid_amount;
                $row[]     = "<small " . $label . ">" . $this->lang->line($value->appointment_status) . "</small>";
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

    public function get_appointment_detail()
    {
        $id     = $this->input->get("appointment_id");
        $result = $this->appointment_model->getDetailsAppointment($id);   
 
        $result['appointment_no'] = $this->customlib->getSessionPrefixByType('appointment') . $id;        
        if($result['start_time']){
            if($this->time_format){
                $result["doctor_shift_name"]       = date('G:i:s', strtotime($result['start_time'])) ." - ". date('G:i:s', strtotime($result['end_time'])) ;
            }else{
                $result["doctor_shift_name"]       = date('g:i A', strtotime($result['start_time'])) ." - ". date('g:i A', strtotime($result['end_time'])) ;
            }  
        }else{
            $result["doctor_shift_name"]       = '';
        }        
        $result["patients_name"]       = composePatientName($result['patients_name'], $result['patient_id']);
        $result["edit_live_consult"]   = $this->lang->line($result['live_consult']);
        $result["live_consult"]        = $result['live_consult'];
        $result["date"]                = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        $result['custom_fields_value'] = display_custom_fields('appointment', $id);
        $cutom_fields_data             = get_custom_table_values($id, 'appointment');
        $result['field_data']          = $cutom_fields_data;		
		
		if ($result['patients_gender']) {
                  $result['patients_gender'] = $this->lang->line(strtolower($result['patients_gender']));
        } else {
                  $result['patients_gender'] = '';
        }         
		
        $result['amount']              = amountFormat($result['paid_amount']);
        
        if($result['payment_mode']){
            $result['payment_mode']        = $this->lang->line(strtolower($result['payment_mode']));
        }else{
            $result['payment_mode']        = '';
        }
        
        $result['cheque_no']           = $result['cheque_no'];
        if($result['cheque_date']){
            $result['cheque_date']         = $this->customlib->YYYYMMDDTodateFormat($result['cheque_date']);
        }else{
            $result['cheque_date']         =  '';
        }
		
        $result['attachment']          = $result['attachment'];
        $transaction_id                = $result['transaction_id'];
        if($result['transaction_id']!=""){
            $result['transaction_id']      =  $this->customlib->getSessionPrefixByType('transaction_id').$result['transaction_id'];
        }else{
            $result['transaction_id']="";
        }
        
        $result['department_name']     = $result['department_name'];
        $result['age']                 = $result['age'];
        $result['day']                 = $result['day'];
        $result['month']               = $result['month'];
        
        if($result['appointment_status']){
            $result['appointmentstatus']  = $this->lang->line(strtolower($result['appointment_status']));
        }else{
            $result['appointmentstatus']  =  '';
        }
        
        if($result['source']){
            $result['source']  = $this->lang->line(strtolower($result['source']));
        }else{
            $result['source']  = '';
        }      
        
        $result['patient_age']         = $this->customlib->getPatientAge($result['age'],$result['month'],$result['day']);

        if ($result['attachment'] != "") {
            $result["doc"] = "<a href='" . site_url('admin/transaction/download/') . $transaction_id . "' class='btn btn-default btn-xs'  title=" . $this->lang->line('download') . "><i class='fa fa-download'></i></a>";
        } else {
            $result["doc"] = "";
        }	  
				
        echo json_encode($result);
    }

//=========================

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

    public function getcaseid(){
        $caseidlist = $this->casereference_model->get($this->input->get('caseid'));
        echo json_encode($caseidlist);
    }

}