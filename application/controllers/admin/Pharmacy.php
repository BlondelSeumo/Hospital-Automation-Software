<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pharmacy extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');
        $this->load->library('CSVReader');
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->load->model(array('pharmacy_model', 'prefix_model', 'transaction_model'));
        $this->marital_status       = $this->config->item('marital_status');
        $this->payment_mode         = $this->config->item('payment_mode');
        $this->search_type          = $this->config->item('search_type');
        $this->blood_group          = $this->config->item('bloodgroup');
        $this->charge_type          = $this->customlib->getChargeMaster();
        $data["charge_type"]        = $this->charge_type;
        $this->patient_login_prefix = "pat";
        $this->config->load("image_valid");
        $this->load->helper('customfield_helper');
        $this->load->helper('custom');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
        $this->opd_prefix  = $this->prefix_model->getByCategory(array('opd_no'))[0]->prefix;
        $this->agerange    = $this->config->item('agerange');
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function partialbill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_partial_payment', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('payment_date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|valid_amount');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required');

        if ($this->input->post('payment_mode') == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        }

        if ($this->form_validation->run() == false) {
            $msg = array(
                'payment_date' => form_error('payment_date'),
                'amount'       => form_error('amount'),
                'payment_mode' => form_error('payment_mode'),
                'chekque_no'   => form_error('cheque_no'),
                'cheque_date'  => form_error('cheque_date'),
                'document'     => form_error('document'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $pharmacy_bill_basic_id = $this->input->post('pharmacy_bill_basic_id');
            $pharmacy_bill_basic    = $this->transaction_model->pharmacyTotalPayments($pharmacy_bill_basic_id);

            if (!empty($pharmacy_bill_basic)) {
                $net_amount = $pharmacy_bill_basic->net_amount;
                $total_paid = $pharmacy_bill_basic->total_paid;
            } else {
                $net_amount = 0;
                $total_paid = 0;
            }
            $amount_paying = $this->input->post('amount');
            $refund_amount = $this->input->post('refund_amount');

            if (($net_amount + $refund_amount) >= ($total_paid + $amount_paying)) {

                $picture         = "";
                $bill_date       = $this->input->post("payment_date");
                $payment_section = $this->config->item('payment_section');
                $payment_array   = array(
                    'amount'                 => $this->input->post('amount'),
                    'patient_id'             => $this->input->post('patient_id'),
                    'section'                => $payment_section['pharmacy'],
                    'type'                   => 'payment',
                    'pharmacy_bill_basic_id' => $this->input->post('pharmacy_bill_basic_id'),
                    'payment_mode'           => $this->input->post('payment_mode'),
                    'note'                   => $this->input->post('note'),
                    'payment_date'           => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->customlib->getHospitalTimeFormat()),
                    'received_by'            => $this->customlib->getLoggedInUserID(),
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
                $array = array('status' => 'fail', 'error' => array('amount_invalid' => $this->lang->line('amount_should_not_be_greater_than_balance') . ' ' . amountFormat(($net_amount + $refund_amount) - $total_paid)), 'message' => '');
            }
        }
        echo json_encode($array);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules(
            'medicine_name', $this->lang->line('medicine_name'), array('required',
                array('check_exists', array($this->medicine_category_model, 'valid_medicine_name')),
            )
        );
		
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine_category'), 'required');       
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'required');
        $this->form_validation->set_rules('unit_packing', $this->lang->line('unit_packing'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload', 'required'); 
        if ($this->form_validation->run() == false) {

            $msg = array(
                'medicine_name'        => form_error('medicine_name'),
                'medicine_category_id' => form_error('medicine_category_id'),
                'unit'                 => form_error('unit'),
                'unit_packing'         => form_error('unit_packing'),
                'file'                 => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {

            $pharmacy = array(
                'medicine_name'        => $this->input->post('medicine_name'),
                'medicine_category_id' => $this->input->post('medicine_category_id'),
                'medicine_company'     => $this->input->post('medicine_company'),
                'medicine_composition' => $this->input->post('medicine_composition'),
                'medicine_group'       => $this->input->post('medicine_group'),
                'unit'                 => $this->input->post('unit'),
                'min_level'            => $this->input->post('min_level'),
                'reorder_level'        => $this->input->post('reorder_level'),
                'vat'                  => $this->input->post('vat'),
                'unit_packing'         => $this->input->post('unit_packing'),
                'note'                 => $this->input->post('note'),
                'vat_ac'               => $this->input->post('vat_ac'),
                'rack_number'          => $this->input->post('rack_number'),
            );

            $insert_id = $this->pharmacy_model->add($pharmacy);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'medicine_image' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->update($data_img);
            }

            $category_name = $this->notificationsetting_model->getmedicinecategoryDetails($this->input->post('medicine_category_id'));

            $event_data = array(
                'medicine_name'        => $this->input->post('medicine_name'),
                'medicine_category'    => $category_name['medicine_category'],
                'medicine_company'     => $this->input->post('medicine_company'),
                'medicine_composition' => $this->input->post('medicine_composition'),
                'medicine_group'       => $this->input->post('medicine_group'),
                'unit'                 => $this->input->post('unit'),
                'unit_packing'         => $this->input->post('unit_packing'),
            );

            $this->system_notification->send_system_notification('add_medicine', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $resultlist               = $this->pharmacy_model->searchFullText();

        $i = 0;
        foreach ($resultlist as $value) {
            $pharmacy_id                 = $value['id'];
            $available_qty               = $this->pharmacy_model->totalQuantity($pharmacy_id);
            $totalAvailableQty           = $available_qty['total_qty'];
            $resultlist[$i]["total_qty"] = $totalAvailableQty;
            $i++;
        }

        $result             = $this->pharmacy_model->getPharmacy();
        $data['resultlist'] = $resultlist;
        $data['result']     = $result;
        $data['unitname']   = $this->pharmacy_model->getpharmacyunit();
        $data['company']    = $this->pharmacy_model->getcomapnyname();
        $data['get_medicine_group'] = $this->pharmacy_model->get_medicine_group();
        $this->load->view('layout/header');
        $this->load->view('admin/pharmacy/search', $data);
        $this->load->view('layout/footer');
    }

    public function bulk_delete()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('delete_id[]', 'delete_id', 'trim|required|xss_clean', array('required' => $this->lang->line('no_record_selected')));

        if ($this->form_validation->run() == false) {
            $msg = array(
                'delete_id' => form_error('delete_id[]'),
            );
            $return_array = array('status' => 0, 'error' => $msg);
        } else {
            $pharmacy = $this->input->post('delete_id');
            if(!empty($pharmacy)){
                foreach($pharmacy as $pharmacy_value){
                    $this->pharmacy_model->bulkdelete($pharmacy_value);
                }               
                $return_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('delete_message'));
            }else{
                $return_array = array('status' => 0, 'error' => $this->lang->line('something_went_wrong') , 'message' => '');
            }            
        }

        echo json_encode($return_array);

    }

    public function getPrescriptionById()
    {
        $prescription_no = $this->input->post('prescription_no');
        if ($prescription_no != "") {

            $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
            $data["medicineCategory"] = $medicineCategory;
            $doctors                  = $this->staff_model->getStaffbyrole(3);
            $data["doctors"]          = $doctors;
            $prefixes                 = $this->prefix_model->getByCategory(array('ipd_prescription', 'opd_prescription'));
            $data["payment_mode"]     = $this->payment_mode;
            $total_rows               = 0;
            $patient_id               = "";
            $prefix_type              = "";
            $case_reference_id        = "";

            $prescription_prefix = splitPrefixType($prescription_no);
            $prescription_no     = splitPrefixID($prescription_no);

            if (!empty($prefixes)) {
                $prefix_type = findPrefixType($prefixes, $prescription_prefix);
            }

            $prescription_data = $this->prescription_model->getPrescriptionByTable($prescription_no, $prefix_type);
            $data['prescription_data'] = $prescription_data;

            $page = $this->load->view("admin/pharmacy/_prescriptionBill", $data, true);
            if (!empty($prescription_data)) {
                $case_reference_id = $prescription_data->case_reference_id;
                $patient_id        = $prescription_data->patient_id;
                $patient_name      = $prescription_data->patient_name;
                $total_rows        = count($prescription_data->medicines);
                $return_array      = array('status' => 1, 'page' => $page, 'patient_id' => $patient_id, 'patient_name' => $patient_name, 'total_rows' => $total_rows, 'case_reference_id' => $case_reference_id);
            } else {
                $return_array = array('status' => 0, 'msg' => $this->lang->line('no_prescription_found'));
            }

            echo json_encode($return_array);
        } else {
            echo json_encode(array('status' => 0, 'msg' => $this->lang->line('no_prescription_found')));
        }
    }

    public function getpharmacyDatatable()
    {
        $dt_response = $this->pharmacy_model->getAllpharmacyRecord();
        $dt_response = json_decode($dt_response);
        
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                
                $result   =   $this->pharmacy_model->getAvailableQuantity($value->id);
                
                if(!empty($result['used_quantity'])){
                    $used_quantity  =   $result['used_quantity'];
                }else{
                    $used_quantity  =  0 ;
                }                   
                $row = array();
                $available_qty = ($value->total_qty - $used_quantity);
                //====================================
                $status = "";
                $status1 = "";
                if ($available_qty <= 0) {
                    $status = " <span class='text text-danger'> (" . $this->lang->line('out_of_stock') . ")</span>";
                } elseif ($available_qty > 0 && $available_qty < $value->min_level ) {
                    $status = " <span class='text text-warning'> (" . $this->lang->line('low_stock') . ")</span>"; 
                } 
                
                if ($available_qty <= $value->reorder_level ) {                   
                    $status1 = " <span class='text text-info'> (" . $this->lang->line('reorder') . ")</span>";
                }  
                
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='#' onclick='viewDetail(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
                if ($this->rbac->hasPrivilege('medicine_bad_stock', 'can_add')) {
                    $action .= "<a href='#' class='btn btn-default btn-xs' onclick='addbadstock(" . $value->id . ")' data-toggle='tooltip' title='" . $this->lang->line('add_bad_stock') . "' > <i class='fas fa-minus-square'></i> </a>";
                }
                
                $action .= "<div'>";
                $checkbox = "<input id='pharmacy' href='#' class='enable_delete'  type='checkbox' name='pharmacy[]' value='" . $value->id . "'>";
                //==============================
                $row[]     = $checkbox;
                $row[]     = $value->medicine_name . $action;
                $row[]     = $value->company_name;
                $row[]     = $value->medicine_composition;
                $row[]     = $value->medicine_category;
                $row[]     = $value->group_name;
                $row[]     = $value->unit_name;
                $row[]     = $available_qty . $status . $status1 ;
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

    public function bill_search()
    {
        $draw            = $_POST['draw'];
        $row             = $_POST['start'];
        $rowperpage      = $_POST['length']; // Rows display per page
        $columnIndex     = $_POST['order'][0]['column']; // Column index
        $columnName      = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        $where_condition = array();
        if (!empty($_POST['search']['value'])) {
            $where_condition = array('search' => $_POST['search']['value']);
        }
        $resultlist   = $this->pharmacy_model->searchbill_datatable($where_condition);
        $total_result = $this->pharmacy_model->searchbill_datatable_count($where_condition);
        $data         = array();
        foreach ($resultlist as $result_key => $result_value) {

            $nestedData = array();
            $action     = "<div class='rowoptionview rowview-mt-19'>";
            $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $result_value->id . "' class='btn btn-default btn-xs add_payment' data-toggle='tooltip' title='" . $this->lang->line('add_view_payments') . "' ><i class='fa fa-money'></i></a>";
            $action .= "<a href='#' onclick='viewDetail(" . $result_value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
            $action .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-record-id='" . $result_value->id . "' class='btn btn-default btn-xs print_bill' data-toggle='tooltip' title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";
            $action .= "<div>";

            $nestedData[] = $this->customlib->getSessionPrefixByType('pharmacy_billing') . $result_value->id;
            $nestedData[] = $result_value->case_reference_id;
            $nestedData[] = $result_value->date;
            $nestedData[] = $result_value->patient_name . $action;
            $nestedData[] = $result_value->doctor_name;
            $nestedData[] = $result_value->total;
            $data[]       = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($draw), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval($total_result), // total number of records
            "recordsFiltered" => intval($total_result), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data, // total data array
        );

        echo json_encode($json_data); // send data as json format

    }

    public function getpharmacybillDatatable()
    {
        $dt_response = $this->pharmacy_model->getAllpharmacybillRecord();
        $fields      = $this->customfield_model->get_custom_fields('pharmacy', 1);
        $dt_response = json_decode($dt_response);

        $dt_data = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
              
                $row            = array();
                $balance_amount = ($value->net_amount-($value->paid_amount-$value->refund_amount));
                // ====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                if ($this->rbac->hasPrivilege('pharmacy_partial_payment', 'can_view')) {
                    $action .= "<span class='medium-lr-tooltip'><a href='#' data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs add_payment tooltip-inner-2' data-toggle='tooltip' title='" . $this->lang->line('add_view_payments') . "' ><i class='fa fa-money'></i></a></span>";
                }
                $action .= "<a href='#' onclick='viewDetail(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('show') . "' ><i class='fa fa-reorder'></i></a>";
                $action .= "<a href='#'  data-loading-text='" . $this->lang->line('please_wait') . "' data-record-id='" . $value->id . "' class='btn btn-default btn-xs print_bill' data-toggle='tooltip' title='" . $this->lang->line('print') . "' ><i class='fa fa-print'></i></a>";

                if ($value->case_reference_id > 0) {
                    $case_id = $value->case_reference_id;
                } else {
                    $case_id = '';
                }
                $action .= "<div>";
                
                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('pharmacy_billing') . $value->id;
                $row[] = $case_id;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date, $this->time_format);
                $row[] = $value->patient_name . " (" . $value->pid . ")" . $action;
                $row[] = $value->doctor_name;
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
                $row[]     = $value->discount . " (" . $value->discount_percentage . "%)";
                $row[]     = $value->net_amount;
                $row[]     = number_format($value->paid_amount, 2, '.', '');
                $row[]     = number_format($value->refund_amount, 2, '.', '');         
                $row[]     = number_format((float) $balance_amount, 2, '.', '');
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
	
//======================================================================================
    public function handle_upload()
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
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function check_upload()
    {
        $image_validate = $this->config->item('image_validate');
        if (isset($_FILES["medicine_image"]) && !empty($_FILES['medicine_image']['name'])) {

            $file_type         = $_FILES["medicine_image"]['type'];
            $file_size         = $_FILES["medicine_image"]["size"];
            $file_name         = $_FILES["medicine_image"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['medicine_image']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('check_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('check_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('check_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('check_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function getindate()
    {
        $purchase_id           = $this->input->post("purchase_id");
        $result                = $this->pharmacy_model->getindate($purchase_id);
        $result['purchase_no'] = $this->customlib->getSessionPrefixByType('purchase_no') . $result['id'];

        echo json_encode($result);
    }

    public function getdate()
    {
        $id             = $this->input->post("id");
        $result         = $this->pharmacy_model->getdate($id);
        $result["date"] = $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->time_format);
        echo json_encode($result);
    }

    public function purchase()
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
            access_denied();
        }
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $supplierCategory         = $this->medicine_category_model->getSupplierCategory();
        $data["supplierCategory"] = $supplierCategory;
        $result                   = $this->pharmacy_model->getPharmacy();
        $data['result']           = $result;
        $data["payment_mode"]     = $this->payment_mode;
        $this->load->view('layout/header');
        $this->load->view('admin/pharmacy/purchase', $data);
        $this->load->view('layout/footer');
    }

    public function getpharmacypurchaseDatatable()
    {
        $dt_response = $this->pharmacy_model->getAllpharmacypurchaseRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();

                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='#' onclick='viewDetail(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('show') . "'  ><i class='fa fa-reorder'></i></a>";
                if (!empty($value->file)) {
                    $action .= "<a href=" . base_url() . 'admin/pharmacy/download/' . $value->file . " onclick='' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
                }
                $action .= "<div>";
                //==============================
                $row[] = $this->customlib->getSessionPrefixByType('purchase_no') . $value->id . $action;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date);
                $row[] = $value->invoice_no;
                $row[] = $value->supplier;
                $row[] = $value->total;
                $row[] = $value->tax;
                $row[] = $value->discount;
                $row[] = $value->net_amount;
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

    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_medicine_sample_file.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_medicine_sample_file.csv';
        force_download($name, $data);
    }
 
    public function import()
    {
        if (!$this->rbac->hasPrivilege('import_medicine', 'can_view')) {
            access_denied();
        }
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine_category'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_csv_upload');
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $fields                   = array('medicine_name', 'medicine_company', 'medicine_composition', 'medicine_group', 'unit', 'min_level', 'reorder_level', 'vat', 'unit_packing', 'note');
        $data["fields"]           = $fields;

        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_category_id' => form_error('medicine_category_id'),
                'file'                 => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            $this->load->view('layout/header');
            $this->load->view('admin/pharmacy/import', $data);
            $this->load->view('layout/footer');
        } else {
            $medicine_category_id = $this->input->post('medicine_category_id');
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

                if ($ext == 'csv') {
                    $file = $_FILES['file']['tmp_name'];
                    $result = $this->csvreader->parse_file($file);
                    if (!empty($result)) {
                        $count = 0;
                        for ($i = 1; $i <= count($result); $i++) {

                            $medicine_data[$i] = array();
                            $n                 = 0;
                            foreach ($result[$i] as $key => $value) {

                                $medicine_data[$i][$fields[$n]]            = $this->encoding_lib->toUTF8($result[$i][$key]);
                                $medicine_data[$i]['is_active']            = 'yes';
                                $medicine_data[$i]['medicine_category_id'] = $medicine_category_id;

                                $n++;
                            }

                            $medicine_name = $medicine_data[$i]["medicine_name"];
                            if (!empty($medicine_name)) {
                                if ($this->pharmacy_model->check_medicine_exists($medicine_name, $medicine_category_id)) {
                                    $this->session->set_flashdata('import_msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exists') . '</div>');

                                    $insert_id = "";
                                } else {
                                    $insert_id = $this->pharmacy_model->addImport($medicine_data[$i]);
                                }
                            }

                            if (!empty($insert_id)) {
                                $data['csvData'] = $result;
                                $this->session->set_flashdata('import_msg', '<div class="alert alert-success text-center">' . $this->lang->line('records_imported_successfully') . '</div>');
                                $count++;
                                $this->session->set_flashdata('import_msg', '<div class="alert alert-success text-center">Total ' . count($result) . ' ' . $this->lang->line('records_found_in_csv_file_total') . ' ' . $count . $this->lang->line('records_imported_successfully') . '</div>');
                            } else {
                                $this->session->set_flashdata('import_msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exists') . '</div>');
                            }
                        }
                    }
                }
                redirect('admin/pharmacy/import');
            }
        }
    }

    public function handle_csv_upload()
    {
        $image_validate = $this->config->item('filecsv_validate');
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = filesize($_FILES['file']['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_extension_not_allowed'), 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        } else {
            $this->form_validation->set_message('handle_csv_upload', $this->lang->line('the_file_field_is_required'));
            return false;
        }
        return true;
    }

    public function getDetails()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post("pharmacy_id");
        $result = $this->pharmacy_model->getDetails($id);
        echo json_encode($result);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('medicine_name', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id', $this->lang->line('medicine_category_id'), 'required');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'required');
        $this->form_validation->set_rules('unit_packing', $this->lang->line('unit_packing'), 'required');
        $this->form_validation->set_rules('medicine_image', $this->lang->line('image'), 'callback_check_upload');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_name'        => form_error('medicine_name'),
                'medicine_category_id' => form_error('medicine_category_id'),
                'unit'                 => form_error('unit'),
                'unit_packing'         => form_error('unit_packing'),
                'medicine_image'       => form_error('medicine_image'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id       = $this->input->post('id');
            $pharmacy = array(
                'id'                   => $id,
                'medicine_name'        => $this->input->post('medicine_name'),
                'medicine_category_id' => $this->input->post('medicine_category_id'),
                'medicine_company'     => $this->input->post('medicine_company'),
                'medicine_composition' => $this->input->post('medicine_composition'),
                'medicine_group'       => $this->input->post('medicine_group'),
                'unit'                 => $this->input->post('unit'),
                'min_level'            => $this->input->post('min_level'),
                'reorder_level'        => $this->input->post('reorder_level'),
                'vat'                  => $this->input->post('vat'),
                'unit_packing'         => $this->input->post('unit_packing'),
                'note'                 => $this->input->post('edit_note'),
                'vat_ac'               => $this->input->post('vat_ac'),
            );
            $this->pharmacy_model->update($pharmacy);
            if (isset($_FILES["medicine_image"]) && !empty($_FILES['medicine_image']['name'])) {
                $fileInfo = pathinfo($_FILES["medicine_image"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["medicine_image"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $id, 'medicine_image' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->update($data_img);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->pharmacy_model->delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function getPharmacy()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $id     = $this->input->post('pharmacy_id');
        $result = $this->pharmacy_model->getPharmacy($id);
        echo json_encode($result);
    }

    public function convertMonthToNumber($monthName)
    {
        return date('m', strtotime($monthName));
    }

    public function medicineBatch()
    {
        if (!$this->rbac->hasPrivilege('medicine batch details', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('pharmacy_id', $this->lang->line('pharmacy_id'), 'required');
        $this->form_validation->set_rules('expiry_date', $this->lang->line('expiry_date'), 'required');
        $this->form_validation->set_rules('batch_no', $this->lang->line('batch_no'), 'required');
        $this->form_validation->set_rules('packing_qty', $this->lang->line('packing_qty'), 'required|numeric');
        $this->form_validation->set_rules('quantity', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('mrp', $this->lang->line('mrp'), 'required|numeric');
        $this->form_validation->set_rules('sale_rate', $this->lang->line('sale_rate'), 'required|numeric');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'pharmacy_id'        => form_error('pharmacy_id'),
                'expiry_date'        => form_error('expiry_date'),
                'expiry_date_format' => form_error('expiry_date_format'),
                'batch_no'           => form_error('batch_no'),
                'packing_qty'        => form_error('packing_qty'),
                'quantity'           => form_error('quantity'),
                'mrp'                => form_error('mrp'),
                'sale_rate'          => form_error('sale_rate'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id          = $this->input->post('pharmacy_id');
            $inward_date = $this->input->post('inward_date');
            $expdate     = $this->input->post('expiry_date');

            $explore = explode("/", $expdate);
            $monthary     = $explore[0];
            $yearary      = $explore[1];
            $month        = $monthary;
            $month_number = $this->convertMonthToNumber($month);
            $insert_date  = $yearary . "-" . $month_number . "-01";

            $medicine_batch = array(
                'pharmacy_id'           => $id,
                'expiry_date'           => $this->input->post('expiry_date'),
                'expiry_date_format'    => $insert_date,
                'inward_date'           => $this->customlib->dateFormatToYYYYMMDD($inward_date),
                'batch_no'              => $this->input->post('batch_no'),
                'packing_qty'           => $this->input->post('packing_qty'),
                'purchase_rate_packing' => $this->input->post('purchase_rate_packing'),
                'quantity'              => $this->input->post('quantity'),
                'mrp'                   => $this->input->post('mrp'),
                'sale_rate'             => $this->input->post('sale_rate'),
                'amount'                => $this->input->post('amount'),
                'available_quantity'    => $this->input->post('quantity'),
            );
            $this->pharmacy_model->medicineDetail($medicine_batch);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getMedicineBatch()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $id                     = $this->input->post("pharmacy_id");
        $result                 = $this->pharmacy_model->getMedicineBatch($id);
        $data["result"]         = $result;
        $badstockresult         = $this->pharmacy_model->getMedicineBadStock($id);
        $data["badstockresult"] = $badstockresult;		 
        $this->load->view('admin/pharmacy/medicineDetail', $data);
    }

    public function addpatient()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $check_patient_id = $this->patient_model->getMaxId();
            if (empty($check_patient_id)) {
                $check_patient_id = 1000;
            }

            $patient_id = $check_patient_id + 1;

            $patient_data = array(
                'patient_name'      => $this->input->post('name'),
                'mobileno'          => $this->input->post('contact'),
                'marital_status'    => $this->input->post('marital_status'),
                'email'             => $this->input->post('email'),
                'gender'            => $this->input->post('gender'),
                'guardian_name'     => $this->input->post('guardian_name'),
                'blood_group'       => $this->input->post('blood_group'),
                'address'           => $this->input->post('address'),
                'known_allergies'   => $this->input->post('known_allergies'),
                'patient_unique_id' => $patient_id,
                'note'              => $this->input->post('note'),
                'age'               => $this->input->post('age'),
                'month'             => $this->input->post('month'),
                'is_active'         => 'yes',
            );
            $insert_id = $this->patient_model->add_patient($patient_data);
            $user_password      = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
            $data_patient_login = array(
                'username' => $this->patient_login_prefix . $insert_id,
                'password' => $user_password,
                'user_id'  => $insert_id,
                'role'     => 'patient',
            );
            $this->user_model->add($data_patient_login);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/patient_images/" . $img_name);
                $data_img = array('id' => $insert_id, 'image' => 'uploads/patient_images/' . $img_name);
                $this->patient_model->add($data_img);
            }
        }
        echo json_encode($array);
    }

    public function patientDetails()
    {
        if (!$this->rbac->hasPrivilege('patient', 'can_view')) {
            access_denied();
        }
        $id   = $this->input->post("id");
        $data = $this->patient_model->patientDetails($id);
        echo json_encode($data);
    }

    public function supplierDetails()
    {
        if (!$this->rbac->hasPrivilege('medicine_supplier', 'can_view')) {
            access_denied();
        }
        $id   = $this->input->post("id");
        $data = $this->patient_model->supplierDetails($id);
        echo json_encode($data);
    }

    public function bill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'pharmacy');
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]          = $doctors;
        $data['medicineCategory'] = $this->medicine_category_model->getMedicineCategory();
        $data['medicineName']     = $this->pharmacy_model->getMedicineName();
        $patients                 = $this->patient_model->getPatientListall();
        $data['fields']           = $this->customfield_model->get_custom_fields('pharmacy', 1);
        $data["patients"]         = $patients;
        $data["marital_status"]   = $this->marital_status;
        $data["bloodgroup"]       = $this->blood_group;
        $data["payment_mode"]     = $this->payment_mode;
        $data['organisation']   = $this->organisation_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/pharmacy/pharmacyBill', $data);
        $this->load->view('layout/footer');
    }

    public function get_medicine_name()
    {
        $medicine_category_id = $this->input->post("medicine_category_id");
        $data                 = $this->pharmacy_model->get_medicine_name($medicine_category_id);
        echo json_encode($data);
    }

    public function get_medicine_stockinfo()
    {
        $pharmacy_id = $this->input->post('pharmacy_id');
        $notic_data  = $this->pharmacy_model->get_medicine_stockinfo($pharmacy_id);
        $available_quantity =  $notic_data['total_qty']-$notic_data['used_quantity'];        
        $msg         = "";         
        
        if ($notic_data['total_qty'] <= 0) {
            $msg .= " <span class='dataTables_info text-danger'> " . $this->lang->line('out_of_stock') . "</span>";
        } elseif ($notic_data['total_qty'] <= $notic_data['min_level']) {
            $msg .= " <span class='dataTables_info text-danger'> " . $this->lang->line('low_stock') . "</span>";
        } else {
            $msg .= "<div style='font-size:12px' class='text-danger'>" . $this->lang->line('avl_qty') . ": " . $available_quantity. "</div>";            
        }        
        
        echo json_encode($msg);
    }

    public function get_medicine_dosage()
    {
        $medicine_category_id = $this->input->post("medicine_category_id");
        $data                 = $this->pharmacy_model->get_medicine_dosage($medicine_category_id);
        echo json_encode($data);
    }

    public function get_dosagename()
    {
        $dosage_id           = $this->input->post("dosage_id");
        $data                = $this->pharmacy_model->get_dosagename($dosage_id);
        $data['dosage_unit'] = $data['dosage'] . " " . $data['unit'];
        echo json_encode($data);
    }

    public function get_supplier_name()
    {
        if (!$this->rbac->hasPrivilege('supplier_category', 'can_view')) {
            access_denied();
        }
        $supplier_category_id = $this->input->post("supplier_category_id");
        $data                 = $this->pharmacy_model->get_supplier_name($supplier_category_id);
        echo json_encode($data);
    }

    public function addBill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_add')) {
            access_denied();
        }
        $duplicate_medicine = false;
        $medicines          = array();
        $prescription_no    = $this->input->post('prescription_no');

        $custom_fields = $this->customfield_model->getByBelong('pharmacy');
        $action_type   = $this->input->post('action_type');
        foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
            if ($custom_fields_value['validation']) {
                $custom_fields_id   = $custom_fields_value['id'];
                $custom_fields_name = $custom_fields_value['name'];
                $this->form_validation->set_rules("custom_fields[pharmacy][" . $custom_fields_id . "]", $custom_fields_name, 'trim|required');
            }
        }
        $this->form_validation->set_rules('bill_no', $this->lang->line('bill_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('net_amount', $this->lang->line('net_amount'), 'trim|required|xss_clean');
        if ($this->input->post('payment_mode') == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        }

        if ($action_type !== 'update') {
            $this->form_validation->set_rules(
                'payment_amount', $this->lang->line('payment_amount'), array('required', 'xss_clean',
                    array('check_exists', array($this->pharmacy_model, 'validate_paymentamount')),
                )
            );
        }

        $total_rows = $this->input->post('total_rows');
        if (!isset($total_rows) && !isset($pathology) && !isset($radiology)) {
            $this->form_validation->set_rules('no_records', $this->lang->line('no_records'), 'trim|required|xss_clean',
                array('required' => $this->lang->line('no_medicine_selected')));
        }

        $medication_details = array();

        if (isset($total_rows) && !empty($total_rows)) {
            foreach ($total_rows as $row_key => $row_value) {

                $medicine_category  = $this->input->post('medicine_category_id_' . $row_value);
                $medicine_name      = $this->input->post('medicine_name_id_' . $row_value);
                $batch_no           = $this->input->post('batch_no_id_' . $row_value);
                $expire_date        = $this->input->post('expire_date_' . $row_value);
                $quantity           = $this->input->post('quantity_' . $row_value);
                $available_quantity = $this->input->post('available_quantity_' . $row_value);
                $sale_price         = $this->input->post('sale_price_' . $row_value);
                $get_medicine_name = $this->notificationsetting_model->getmedicineDetails($medicine_name);
                if (!empty($get_medicine_name)) {
                    $medication_details[] = $get_medicine_name['medicine_name'] . ' (' . $batch_no . ')';
                }
                if ($quantity != "" && ($available_quantity < $quantity)) {
                    $this->form_validation->set_rules('over_quantity_demand', 'Order Quantity', 'required', array('required' => $this->lang->line('order_quantity_should_not_be_greater_than_available_quantity')));
                }
                if ($medicine_category == "") {
                    $this->form_validation->set_rules('medicine_category', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
                }
                if ($medicine_name == "") {
                    $this->form_validation->set_rules('medicine_name', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
                }
                if ($batch_no == "") {
                    $this->form_validation->set_rules('batch_no', $this->lang->line('batch_no'), 'required');
                } else {
                    $medicines[] = $batch_no;
                }
                if ($expire_date == "") {
                    $this->form_validation->set_rules('expire_date', $this->lang->line('expiry_date'), 'required');
                }
                if ($quantity == "") {
                    $this->form_validation->set_rules('quantity', $this->lang->line('quantity'), 'required|numeric');
                }
                if ($sale_price == "") {
                    $this->form_validation->set_rules('sale_price', $this->lang->line('sale_price'), 'required|numeric');
                }
                if ($sale_price == "") {
                    $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|numeric');
                }
            }
        }
		
        if (!empty($medicines)) {
            $duplicate = chkDuplicate($medicines);
            if (!empty($duplicate)) {
                $this->form_validation->set_rules('duplicate_medicine', $this->lang->line('duplicate_medicine'), 'required', array('required' => $this->lang->line('duplicate_medicines_found')));
            }
        }

        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'bill_no'              => form_error('bill_no'),
                'no_records'           => form_error('no_records'),
                'duplicate_medicine'   => form_error('duplicate_medicine'),
                'over_quantity_demand' => form_error('over_quantity_demand'),
                'medicine_category'    => form_error('medicine_category'),
                'medicine_name'        => form_error('medicine_name'),
                'batch_no'             => form_error('batch_no'),
                'expire_date'          => form_error('expire_date'),
                'quantity'             => form_error('quantity'),
                'sale_price'           => form_error('sale_price'),
                'amount'               => form_error('amount'),
                'patient_id'           => form_error('patient_id'),
                'net_amount'           => form_error('net_amount'),
                'payment_amount'       => form_error('payment_amount'),
                'cheque_no'            => form_error('cheque_no'),
                'cheque_date'          => form_error('cheque_date'),
                'document'             => form_error('document'),
            );
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                    if ($custom_fields_value['validation']) {
                        $custom_fields_id                                                 = $custom_fields_value['id'];
                        $custom_fields_name                                               = $custom_fields_value['name'];
                        $error_msg2["custom_fields[pharmacy][" . $custom_fields_id . "]"] = form_error("custom_fields[pharmacy][" . $custom_fields_id . "]");
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
            $payment_section        = $this->config->item('payment_section');
            $patient_id             = $this->input->post('patient_id');
            $bill_date              = $this->input->post("date");
            $bill_no                = $this->input->post('bill_no');
            $pharmacy_bill_basic_id = $this->input->post('pharmacy_bill_basic_id');
            $case_reference_id      = $this->input->post('case_reference_id');

            if (empty($case_reference_id)) {
                $case_reference_id = null;
            }

            if ($prescription_no != "") {
                $prescription_prefix = splitPrefixType($prescription_no);
                $prescription_no     = splitPrefixID($prescription_no);
            } else {
                $prescription_no = null;
            }

            $data['opd_prefix'] = $this->opd_prefix;
            $bill_detail        = array(
                'id'                        => $pharmacy_bill_basic_id,
                'case_reference_id'         => $case_reference_id,
                'date'                      => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                'patient_id'                => $patient_id,
                'customer_name'             => $this->input->post('customer_name'),
                'ipd_prescription_basic_id' => $prescription_no,
                'doctor_name'               => $this->input->post('doctor_name'),
                'total'                     => $this->input->post('total'),
                'discount'                  => $this->input->post('discount'),
                'tax'                       => $this->input->post('tax'),
                'net_amount'                => $this->input->post('net_amount'),
                'note'                      => $this->input->post('note'),
                'discount_percentage'       => $this->input->post('discount_percent'),
                'tax_percentage'            => $this->input->post('tax_percent'),
                'generated_by'              => $this->customlib->getLoggedInUserID(),
            );

            $custom_field_post  = $this->input->post("custom_fields[pharmacy]");
            $custom_value_array = array();
            if (!empty($custom_field_post)) {
                foreach ($custom_field_post as $key => $value) {
                    $check_field_type = $this->input->post("custom_fields[pharmacy][" . $key . "]");
                    $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                    $array_custom     = array(
                        'belong_table_id' => 0,
                        'custom_field_id' => $key,
                        'field_value'     => $field_value,
                    );
                    $custom_value_array[] = $array_custom;
                }
            }

            if ($action_type == 'update') {

                $insert_medicines = array();
                $update_medicines = array();

                $prev_array   = $this->input->post('previous_ids');
                $update_array = array();
                $total_rows   = $this->input->post('total_rows');
                if (isset($total_rows) && !empty($total_rows)) {
                    foreach ($total_rows as $row_key => $row_value) {
                        $inserted_id = $this->input->post('insert_id_' . $row_value);
                        if ($inserted_id == 0) {

                            $insert_medicines[] = array(
                                'pharmacy_bill_basic_id'   => 0,
                                'medicine_batch_detail_id' => $this->input->post('batch_no_id_' . $row_value),
                                'quantity'                 => $this->input->post('quantity_' . $row_value),
                                'sale_price'               => $this->input->post('sale_price_' . $row_value),
                            );

                        } elseif ($inserted_id != 0) {
                            $update_array[]     = $inserted_id;
                            $update_medicines[] = array(
                                'id'                       => $inserted_id,
                                'pharmacy_bill_basic_id'   => $pharmacy_bill_basic_id,
                                'medicine_batch_detail_id' => $this->input->post('batch_no_id_' . $row_value),
                                'quantity'                 => $this->input->post('quantity_' . $row_value),
                                'sale_price'               => $this->input->post('sale_price_' . $row_value),
                            );
                        }
                    }
                }

                $payment_amount = $this->input->post('payment_amount');
                $cheque_date    = $this->input->post('cheque_date');
                if (!empty($payment_amount)) {
                    $payment_array = array(
                        'amount'                 => $this->input->post('payment_amount'),
                        'type'                   => 'refund',
                        'case_reference_id'      => $case_reference_id,
                        'patient_id'             => $patient_id,
                        'section'                => $payment_section['pharmacy'],
                        'pharmacy_bill_basic_id' => $pharmacy_bill_basic_id,
                        'payment_mode'           => $this->input->post('payment_mode'),
                        'note'                   => $this->input->post('note'),
                        'payment_date'           => date('Y-m-d H:i:s'),
                        'received_by'            => $this->customlib->getLoggedInUserID(),
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
                        $payment_array['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                        $payment_array['cheque_no']       = $this->input->post('cheque_no');
                        $payment_array['attachment']      = $attachment;
                        $payment_array['attachment_name'] = $attachment_name;
                    }

                } else {
                    $payment_array = array();
                }

                $delete_result = array_diff($prev_array, $update_array);

                $is_inserted = $this->pharmacy_model->addBill($bill_detail, $insert_medicines, $update_medicines, $delete_result, $payment_array);

                if (!empty($custom_fields)) {
                    foreach ($custom_field_post as $key => $value) {
                        $check_field_type = $this->input->post("custom_fields[pharmacy][" . $key . "]");
                        $field_value      = is_array($check_field_type) ? implode(",", $check_field_type) : $check_field_type;
                        $array_custom     = array(
                            'belong_table_id' => $is_inserted,
                            'custom_field_id' => $key,
                            'field_value'     => $field_value,
                        );
                        $custom_value_array[] = $array_custom;
                    }
                    $this->customfield_model->updateRecord($custom_value_array, $is_inserted, 'pharmacy');
                }

                //====================
            } else {

                if (!empty($_FILES['document']['name'])) {
                    $config['upload_path']   = 'uploads/payment_document/';
                    $config['allowed_types'] = 'jpg|jpeg|png';
                    $config['file_name']     = $_FILES['document']['name'];
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('document')) {
                        $uploadData = $this->upload->data();
                        $picture    = $uploadData['file_name'];
                    } else {
                        $picture = '';
                    }
                } else {
                    $picture = '';
                }
                $payment_amount   = $this->input->post('payment_amount');
                $cheque_date      = $this->input->post('cheque_date');
                $insert_medicines = array();
                $total_rows       = $this->input->post('total_rows');
                if (isset($total_rows) && !empty($total_rows)) {
                    foreach ($total_rows as $row_key => $row_value) {
                        $insert_medicines[] = array(
                            'medicine_batch_detail_id' => $this->input->post('batch_no_id_' . $row_value),
                            'quantity'                 => $this->input->post('quantity_' . $row_value),
                            'sale_price'               => $this->input->post('sale_price_' . $row_value),
                        );
                    }
                }

                if ($payment_amount >= 0) {

                    $payment_array = array(
                        'amount'       => $this->input->post('payment_amount'),
                        'type'         => 'payment',
                        'patient_id'   => $patient_id,
                        'section'      => $payment_section['pharmacy'],
                        'payment_mode' => $this->input->post('payment_mode'),
                        'note'         => $this->input->post('note'),
                        'payment_date' => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                        'received_by'  => $this->customlib->getLoggedInUserID(),
                    );
					
                    if ($this->input->post('payment_mode') == "Cheque") {
                        $payment_array['cheque_date'] = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                        $payment_array['cheque_no']   = $this->input->post('cheque_no');
                        $payment_array['attachment']  = $picture;
                    }

                } else {
                    $payment_array = array();
                }

                $is_inserted = $this->pharmacy_model->addBill($bill_detail, $insert_medicines, array(), array(), $payment_array);

                if (!empty($custom_value_array)) {
                    $this->customfield_model->insertRecord($custom_value_array, $is_inserted);
                }
            }

            if ($is_inserted) {
                $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $is_inserted);

                $medication_details = implode(',', $medication_details);
                $due_amount         = $this->input->post('net_amount') - $this->input->post('payment_amount');

                $event_data = array(
                    'patient_id'       => $patient_id,
                    'case_id'          => $case_reference_id,
                    'bill_no'          => $this->customlib->getSessionPrefixByType('pharmacy_billing') . $bill_no,
                    'medicine_details' => $medication_details,
                    'doctor_name'      => $this->input->post('doctor_name'),
                    'total'            => $this->input->post('total'),
                    'discount'         => $this->input->post('discount'),
                    'tax'              => $this->input->post('tax'),
                    'net_amount'       => $this->input->post('net_amount'),
                    'paid'             => $this->input->post('payment_amount'),
                    'due_amount'       => number_format((float) $due_amount, 2, '.', ''),
                    'date'             => $this->customlib->YYYYMMDDHisTodateFormat($bill_date, $this->customlib->getHospitalTimeFormat()),
                );

                $this->system_notification->send_system_notification('pharmacy_generate_bill', $event_data);
            } else {
                $array = array('status' => 0, 'message' => $this->lang->line('something_went_wrong'));
            }
        }
        echo json_encode($array);
    }

    public function getBillDetails()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $id      = $this->input->get('id');
        $print   = $this->input->get('print');
        $is_bill = $this->input->get('is_bill');
        $is_bill = $this->input->get('is_bill');

        $print_details         = $this->printing_model->get('', 'pharmacy');
        $data["print_details"] = $print_details;
        $data['id']            = $id;
        if (isset($print)) {
            $data["print"] = true;
            $check_print   = 'print';
        } else {
            $data["print"] = false;
            $check_print   = '';
        }
        if (isset($is_bill)) {
            $data["is_bill"] = false;
            $bill_print      = "print_pharmacy_bill";
        } else {
            $data["is_bill"] = true;
            $bill_print      = "print_bill";
        }
        if ($check_print == 'print') {
            $data['fields']      = $this->customfield_model->get_custom_fields('pharmacy', '', 1);
            $data['check_print'] = $check_print;
        } else {
            $data['fields']      = $this->customfield_model->get_custom_fields('pharmacy');
            $data['check_print'] = $check_print;
        }
        $result = $this->pharmacy_model->getBillDetails($id, $data['check_print']);
        $data['result'] = $result;
        $bill_no    = $result['id'];
        $patient_id = $result['patient_id'];
        $ipd_prescription_basic_id = $result['ipd_prescription_basic_id'];  
        
        $ipd_opd = $this->pharmacy_model->getIpdPrescriptionBasic($ipd_prescription_basic_id);   
        if($ipd_prescription_basic_id!=""){
            if($ipd_opd->ipd_id != ''){             
              $data['prescription']   =   $this->customlib->getSessionPrefixByType('ipd_prescription').$ipd_opd->id ;   
            }else{
                $data['prescription']   =   $this->customlib->getSessionPrefixByType('opd_prescription').$ipd_opd->id ; 
            }   
        }else{
            $data['prescription']   ="" ;
        }           
        
        $detail = $this->pharmacy_model->getAllBillDetails($id);
      
        $data['detail'] = $detail;
        $action_details = "";
        if ($this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            $action_details .= "<a href='#'  data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-toggle='tooltip' class='" . $bill_print . "' data-record-id='" . $id . "' data-original-title='" . $this->lang->line('print') . "'><i class='fa fa-print'></i></a>";
        }

        if ($this->rbac->hasPrivilege('pharmacy_bill', 'can_edit')) {
            if ($data["is_bill"]) {
                $action_details .= "<a href='#' class='edit_bill' data-record-id='" . $id . "' data-prescription-id='" . $data['prescription']  . "'  data-toggle='tooltip'  data-original-title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
            }
        }

        if ($this->rbac->hasPrivilege('pharmacy_bill', 'can_delete')) {
            if ($data["is_bill"]) {
                $action_details .= "<a data-record-id='" . $id . "'  href='#'  data-toggle='tooltip'  data-original-title='" . $this->lang->line('delete') . "' class='delete-record'><i class='fa fa-trash'></i></a>";
            }
        }

        $page = $this->load->view('admin/pharmacy/_getBillDetails', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'actions' => $action_details));
    }

    public function getPharmacyTransaction()
    {
        $pharmacy_bill_basic_id         = $this->input->post('id');
        $pharmacy_bill_detail           = $this->pharmacy_model->getBillDetails($pharmacy_bill_basic_id);
      
        $balance_amount                 = (($pharmacy_bill_detail['net_amount']+$pharmacy_bill_detail['refund_amount']) - ($pharmacy_bill_detail['paid_amount']));
        $pharmacy_transaction           = $this->transaction_model->pharmacyPayments($pharmacy_bill_basic_id);
        $data["balance_amount"]         = amountFormat($balance_amount);
        $data["pharmacy_bill_basic_id"] = $pharmacy_bill_basic_id;
        $data["payment_mode"]           = $this->payment_mode;
        $data['pharmacy_transaction']   = $pharmacy_transaction;
        $data['pharmacy_bill_detail']   = $pharmacy_bill_detail;
        $is_bill                        = $this->input->post('is_bill');
        if (isset($is_bill)) {
            $data['view_delete'] = false;
        } else {
            $data['view_delete'] = true;
        }

        $page = $this->load->view("admin/pharmacy/_getPharmacyTransaction", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function createBill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $id                       = $this->input->post('id');
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $patients                 = $this->patient_model->getPatientListall();
        $data["patients"]         = $patients;
        $doctors                  = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]          = $doctors;
        $data["payment_mode"]     = $this->payment_mode;
        $result                   = $this->pharmacy_model->getBillNo();
        $id                       = $result["id"];
        if (!empty($result["id"])) {
            $bill_no = $id + 1;
        } else {
            $bill_no = 1;
        }

        $page = $this->load->view("admin/pharmacy/_createBill", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page, 'bill_no' => $bill_no));
    }

    public function editBill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $id                          = $this->input->get('id');
        $medicineCategory            = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"]    = $medicineCategory;
        $patients                    = $this->patient_model->getPatientListall();
        $data["patients"]            = $patients;
        $doctors                     = $this->staff_model->getStaffbyrole(3);
        $data["doctors"]             = $doctors;
        $bill                        = $this->pharmacy_model->getBillDetails($id);
       
        $data['bill']                = $bill;
        $detail                      = $this->pharmacy_model->getAllBillDetails($id);
        $data['detail']              = $detail;
        $data["payment_mode"]        = $this->payment_mode;
        $data['custom_fields_value'] = display_custom_fields('pharmacy', $id);       
        $page = $this->load->view("admin/pharmacy/_editBill", $data, true);
        $total_rows = count($detail); 
        echo json_encode(array('status' => 1, 'page' => $page, 'paid_amount' => $bill['paid_amount'],'refund_amount' => $bill['refund_amount'], 'patient_id' => $bill['patient_id'], 'patient_name' => $bill['patient_name'], 'bill_no' => $this->customlib->getSessionPrefixByType('pharmacy_billing').$bill['id'], 'date' => $bill['date'], 'case_reference_id' => $bill['case_reference_id'], 'total_rows' => $total_rows));
    }
 
    public function getSupplierDetails($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
            access_denied();
        }
        $data['id'] = $id;
        if (isset($_POST['print'])) {
            $data["print"] = 'yes';
        } else {
            $data["print"] = 'no';
        }

        $result         = $this->pharmacy_model->getSupplierDetails($id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getAllSupplierDetails($id);       
        $data['detail'] = $detail;
        $this->load->view('admin/pharmacy/printPurchase', $data);
    }

    public function download($file)
    {
        $this->load->helper('download');
        $filepath = "./uploads/medicine_images/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

    public function getQuantity()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $batch_no = $this->input->get('batch_no');
        $med_id   = $this->input->get('med_id');
        $data     = $this->pharmacy_model->getQuantity($batch_no, $med_id);
        echo json_encode($data);
    }

    public function getQuantityedit()
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        $batch_no = $this->input->get('batch_no');
        $data     = $this->pharmacy_model->getQuantityedit($batch_no);
        echo json_encode($data);
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
                'search_type'   => $this->input->post('search_type'),
                'collect_staff' => $this->input->post('collect_staff'),
                'gender'        => $this->input->post('gender'),
                'from_age'      => $this->input->post('from_age'),
                'to_age'        => $this->input->post('to_age'),
                'date_from'     => $this->input->post('date_from'),
                'date_to'       => $this->input->post('date_to'),
                'payment_mode'  => $this->input->post('payment_mode'),
                'doctor_name'  => $this->input->post('doctor_name'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function billreport()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pharmacy');
        $this->session->set_userdata('subsub_menu', 'reports/pharmacy/billreport');

        $custom_fields = $this->customfield_model->get_custom_fields('pharmacy', '', '', 1);
        $data['fields']       = $custom_fields;
        $staffsearch          = $this->patient_model->getstaffpharmacybill();
        $data['staffsearch']  = $staffsearch;
        $search_type          = "this_month";
        $data["searchlist"]   = $this->search_type;
        $data['agerange']     = $this->agerange;
        $data['gender']       = $this->customlib->getGender_Patient();
        $this->payment_mode   = $this->config->item('payment_mode');
        $data['payment_mode'] = $this->payment_mode;
        $data['pharmacydoctor'] = $this->pharmacy_model->getpharmacydoctor();       
        
        $this->load->view('layout/header');
        $this->load->view('admin/pharmacy/billReport', $data);
        $this->load->view('layout/footer');
    }

    public function pharmacyreports()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['collect_staff'] = $this->input->post('collect_staff');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $search['from_age']      = $this->input->post('from_age');
        $search['to_age']        = $this->input->post('to_age');
        $search['gender']        = $this->input->post('gender');
        $search['payment_mode']  = $this->input->post('payment_mode');
        $search['doctor_name']   = $this->input->post('doctor_name');
        
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        
        $start_date = '';
        $end_date   = '';
        $fields     = $this->customfield_model->get_custom_fields('pharmacy', '', '', 1);
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

        $reportdata = $this->transaction_model->pharmacybillreportsRecord($start_date, $end_date, $search);
 
        $reportdata = json_decode($reportdata);
        $dt_data    = array();

        $total_paid = 0;
        $total_net  = 0;
        $total_balance  = 0;
        $total_refund  = 0;
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $total_paid += $value->paid_amount;
                $total_net += $value->net_amount;
                $total_refund += $value->refund_amount;                
				
				$balance_amount = ($value->net_amount-($value->paid_amount-$value->refund_amount));
				$balance = number_format($balance_amount, 2, '.', '');				 
				 
                $total_balance+= $balance ; 
                $prescription_no = "";
                if ($value->ipd_id != "") {
                    $prescription_no = $this->customlib->getSessionPrefixByType('ipd_prescription') . $value->ipd_prescription_basic_id;
                } elseif ($value->visit_details_id != "") {
                    // code...
                    $prescription_no = $this->customlib->getSessionPrefixByType('opd_prescription') . $value->ipd_prescription_basic_id;
                }
                
                $action1 = "<a href='#' onclick='viewDetail(" . $value->id . ")'>";
                $action2 = "</a>";                
                $row   = array();              
                
                $row[] = $action1.$this->customlib->getSessionPrefixByType('pharmacy_billing') . $value->id.$action2;
                $row[] = $this->customlib->YYYYMMDDHisTodateFormat($value->date);
                $row[] = composePatientName($value->patient_name, $value->patient_id);
                $row[] = $this->customlib->getPatientAge($value->age, $value->month, $value->day);
                $row[] = $this->lang->line(strtolower($value->gender));
                $row[] = $prescription_no;
                $row[] = $value->doctor_name;
                $row[] = $value->name . " " . $value->surname . "(" . $value->employee_id . ")";
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
                $row[]     = $value->refund_amount;
                $row[]     = $value->paid_amount;
                $row[]     = $balance;
                $dt_data[] = $row;
            }

            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            
                if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        
                        $footer_row[] = '';
                        
                    }
                }
                
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_net, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_paid, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_refund, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_balance, 2, '.', '')) . "<br/>";

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

    public function editPharmaBill($id)
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_view')) {
            access_denied();
        }

        $id               = $this->input->post("id");
        $patients         = $this->patient_model->getPatientListall();
        $data["patients"] = $patients;
        $result           = $this->pharmacy_model->getBillDetails($id);
        $data['result']   = $result;
        echo json_encode($result);
    }

    public function editSupplierBill($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_view')) {
            access_denied();
        }
        $medicineCategory             = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"]     = $medicineCategory;
        $medicine_category_id         = $this->input->post("medicine_category_id");
        $data['medicine_category_id'] = $this->pharmacy_model->get_medicine_name($medicine_category_id);
        $data['medicine_category_id'] = $medicine_category_id;
        $supplierCategory             = $this->medicine_category_model->getSupplierCategory();
        $data["supplierCategory"]     = $supplierCategory;
        $supplier_category_id         = $this->input->post("supplier_category_id");
        $data['supplier_category_id'] = $this->pharmacy_model->get_supplier_name($supplier_category_id);
        $data['supplier_category_id'] = $supplier_category_id;

        $result         = $this->pharmacy_model->getSupplierDetails($id);
        $data['result'] = $result;
        $detail         = $this->pharmacy_model->getAllSupplierDetails($id);
        $data['detail'] = $detail;
        $this->load->view("admin/pharmacy/editSupplierBill", $data);
    }

    public function updateBill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('bill_no', $this->lang->line('bill_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('patient_id', $this->lang->line('patient'), 'trim|required');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('expire_date[]', $this->lang->line('expiry_date'), 'required');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch_no'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('sale_price[]', $this->lang->line('sale_price'), 'required|numeric');
        $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'bill_no'              => form_error('bill_no'),
                'date'                 => form_error('date'),
                'customer_name'        => form_error('customer_name'),
                'patient_id'           => form_error('patient_id'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'expire_date'          => form_error('expire_date[]'),
                'batch_no'             => form_error('batch_no[]'),
                'quantity'             => form_error('quantity[]'),
                'sale_price'           => form_error('sale_price[]'),
                'total'                => form_error('total'),
                'amount'               => form_error('amount[]'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $id               = $this->input->post('bill_basic_id');
            $bill_id          = $this->input->post("bill_detail_id[]");
            $previous_bill_id = $this->input->post("previous_bill_id[]");
            $bill_date        = $this->input->post("date");
            $data_array       = array();
            $delete_arr       = array();
            foreach ($previous_bill_id as $pkey => $pvalue) {
                if (in_array($pvalue, $bill_id)) {

                } else {
                    $delete_arr[] = array('id' => $pvalue);
                }
            }

            $data = array(
                'id'            => $id,
                'bill_no'       => $this->input->post('bill_no'),
                'patient_id'    => $this->input->post('patient_id'),
                'date'          => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                'customer_name' => $this->input->post('customer_name'),
                'customer_type' => $this->input->post('customer_type'),
                'doctor_name'   => $this->input->post('doctor_name'),
                'opd_ipd_no'    => $this->input->post('opd_ipd_no'),
                'total'         => $this->input->post('total'),
                'discount'      => $this->input->post('discount'),
                'note'          => $this->input->post('note'),
                'tax'           => $this->input->post('tax'),
                'net_amount'    => $this->input->post('net_amount'),
            );

            $this->pharmacy_model->addBill($data);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/pres_images/" . $img_name);
                $data_img = array('id' => $id, 'file' => 'uploads/pres_images/' . $img_name);
                $this->pharmacy_model->addBill($data_img);
            }

            if (!empty($id)) {
                $pharmacy_bill_basic_id = $id;
                $bill_detail_id         = $this->input->post('bill_detail_id');
                $medicine_batch_id      = $this->input->post('medicine_batch_id');
                $medicine_category_id   = $this->input->post('medicine_category_id');
                $medicine_name          = $this->input->post('medicine_name');
                $expiry_date            = $this->input->post('expire_date');
                $batch_no               = $this->input->post('batch_no');
                $quantity               = $this->input->post('quantity');
                $total_quantity         = $this->input->post('available_quantity');
                $amount                 = $this->input->post('amount');
                $sale_price             = $this->input->post('sale_price');
                $data                   = array();
                $i                      = 0;
                foreach ($medicine_category_id as $key => $value) {
                    if ($bill_id[$i] == 0) {
                        $add_data = array(
                            'pharmacy_bill_basic_id' => $id,
                            'medicine_category_id'   => $medicine_category_id[$i],
                            'medicine_name'          => $medicine_name[$i],
                            'expire_date'            => $expiry_date[$i],
                            'batch_no'               => $batch_no[$i],
                            'quantity'               => $quantity[$i],
                            'sale_price'             => $sale_price[$i],
                            'amount'                 => $amount[$i],
                        );
                        $data_array[]           = $add_data;
                        $available_quantity[$i] = $total_quantity[$i] - $quantity[$i];
                        $add_quantity           = array(
                            'id'                 => $medicine_batch_id[$i],
                            'available_quantity' => $available_quantity[$i],
                        );
                        $this->pharmacy_model->availableQty($add_quantity);
                    } else {
                        $detail = array(
                            'id'                     => $bill_detail_id[$i],
                            'pharmacy_bill_basic_id' => $id,
                            'medicine_category_id'   => $medicine_category_id[$i],
                            'medicine_name'          => $medicine_name[$i],
                            'expire_date'            => $expiry_date[$i],
                            'batch_no'               => $batch_no[$i],
                            'quantity'               => $quantity[$i],
                            'sale_price'             => $sale_price[$i],
                            'amount'                 => $amount[$i],
                        );
                        $this->pharmacy_model->updateBillDetail($detail);
                        $available_quantity[$i] = $total_quantity[$i] - $quantity[$i];
                        $update_quantity        = array(
                            'id'                 => $medicine_batch_id[$i],
                            'available_quantity' => $available_quantity[$i],
                        );
                        $this->pharmacy_model->availableQty($update_quantity);
                    }
                    $i++;
                }
            } else {

            }
            if (!empty($data_array)) {
                $this->pharmacy_model->addBillBatch($data_array);
            }
            if (!empty($delete_arr)) {
                $this->pharmacy_model->delete_bill_detail($delete_arr);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function addBillSupplier()
    {
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('discount', $this->lang->line('discount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('supplier_id', $this->lang->line('supplier'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('expiry_date[]', $this->lang->line('expiry_date'), 'required');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch_no'), 'required');
        $this->form_validation->set_rules('mrp[]', $this->lang->line('mrp'), 'required');
        $this->form_validation->set_rules('sale_rate[]', $this->lang->line('sale_price'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('purchase_price[]', $this->lang->line('purchase_price'), 'required|numeric');
        $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|xss_clean|trim');
        $this->form_validation->set_rules('tax', $this->lang->line('tax'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line("document"), 'callback_handle_doc_upload[file]');
        if ($this->input->post('payment_mode') == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'required');
            $this->form_validation->set_rules('document', $this->lang->line('document'), 'callback_handle_doc_upload[document]');
        }
        $this->form_validation->set_rules('file', '', 'callback_handle_doc_upload[file]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'supplier_id'          => form_error('supplier_id'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'batch_no'             => form_error('batch_no[]'),
                'mrp'                  => form_error('mrp[]'),
                'sale_rate'            => form_error('sale_rate[]'),
                'expiry_date'          => form_error('expiry_date[]'),
                'quantity'             => form_error('quantity[]'),
                'purchase_price'       => form_error('purchase_price[]'),
                'tax'                  => form_error('tax'),
                'discount'             => form_error('discount'),
                'total'                => form_error('total'),
                'amount'               => form_error('amount[]'),
                'document'             => form_error('file'),
                'payment_mode'         => form_error('payment_mode'),
                'cheque_no'            => form_error('cheque_no'),
                'cheque_date'          => form_error('cheque_date'),
                'file'                 => form_error('file'),
                'document'             => form_error('document'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $supplier_id = $this->input->post('supplier_id');
            $bill_date   = $this->input->post("date");

            $data = array(
                'date'         => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                'supplier_id'  => $supplier_id,
                'invoice_no'   => $this->input->post('invoice_no'),
                'total'        => $this->input->post('total'),
                'discount'     => $this->input->post('discount'),
                'tax'          => $this->input->post('tax'),
                'net_amount'   => $this->input->post('net_amount'),
                'note'         => $this->input->post('note'),
                'payment_mode' => $this->input->post('payment_mode'),
                'payment_date' => date('Y-m-d H:i:s'),
                'payment_note' => $this->input->post('payment_note'),
                'received_by'  => $this->customlib->getStaffID(),
            );
            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo        = pathinfo($_FILES["file"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["file"]["name"];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/payment_document/" . $attachment);
            }
            $cheque_date = $this->input->post("cheque_date");

            if ($this->input->post('payment_mode') == "Cheque") {
                $data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($cheque_date);
                $data['cheque_no']       = $this->input->post('cheque_no');                
            }

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $data['attachment']      = 'uploads/payment_document/' . $attachment;
                $data['attachment_name'] = $attachment_name;
            }

            $insert_id = $this->pharmacy_model->addBillSupplier($data);

            if ($insert_id) {
                $medicine_category_id = $this->input->post('medicine_category_id');
                $medicine_name        = $this->input->post('medicine_name');
                $expiry_date          = $this->input->post('expiry_date');
                $batch_no             = $this->input->post('batch_no');
                $batch_amount         = $this->input->post('batch_amount');
                $mrp                  = $this->input->post('mrp');
                $sale_rate            = $this->input->post('sale_rate');
                $packing_qty          = $this->input->post('packing_qty');
                $quantity             = $this->input->post('quantity');
                $purchase_price       = $this->input->post('purchase_price');
                $amount               = $this->input->post('amount');
                $tax                  = $this->input->post('purchase_tax');

                $data1 = array();
                $j     = 0;

                foreach ($medicine_name as $key => $mvalue) {

                    $expdate = $expiry_date[$j];
                    $explore = explode("/", $expdate);

                    $monthary = $explore[0];
                    $yearary  = $explore[1];
                    $month    = $monthary;

                    $month_number       = $this->convertMonthToNumber($month);
                    $last_date_of_month = date("Y-m-t", strtotime($yearary . "-" . $month_number . "-01"));
                    $insert_date        = $last_date_of_month;

                    $details = array(
                        'inward_date'            => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                        'pharmacy_id'            => $medicine_name[$j],
                        'supplier_bill_basic_id' => $insert_id,
                        'expiry'                 => $insert_date,
                        'batch_no'               => $batch_no[$j],
                        'batch_amount'           => $batch_amount[$j],
                        'mrp'                    => $mrp[$j],
                        'sale_rate'              => $sale_rate[$j],
                        'packing_qty'            => $packing_qty[$j],
                        'quantity'               => $quantity[$j],
                        'purchase_price'         => $purchase_price[$j],
                        'available_quantity'     => $quantity[$j],
                        'tax'                    => $tax[$j],
                        'amount'                 => $amount[$j],
                    );
                    $data1[] = $details;

                    $medicine_data         = $this->notificationsetting_model->getmedicineDetails($medicine_name[$j]);
                    $medicine_name_array[] = $medicine_data['medicine_name'] . ' (' . $batch_no[$j] . ')';

                    $j++;

                }
                $this->pharmacy_model->addBillMedicineBatchSupplier($data1);
            }

            if (!empty($medicine_name_array)) {
                $medicine_var = implode(",", $medicine_name_array);
            }

            $supplier_name = $this->patient_model->supplierDetails($supplier_id);
            $event_data = array(
                'supplier_name'    => $supplier_name['supplier'],
                'medicine_details' => $medicine_var,
                'purchase_date'    => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                'invoice_number'   => $this->input->post('invoiceno'),
                'total'            => $this->input->post('total'),
                'discount'         => number_format((float) $this->input->post('discount'), 2, '.', ''),
                'tax'              => $this->input->post('tax'),
                'net_amount'       => $this->input->post('net_amount'),
            );

            $this->system_notification->send_system_notification('purchase_medicine', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'), 'insert_id' => $insert_id);
        }
        echo json_encode($array);
    }

    public function updateSupplierBill()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('supplier_id', $this->lang->line('supplier'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_category_id[]', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('medicine_name[]', $this->lang->line('medicine_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('expiry_date[]', $this->lang->line('expiry_date'), 'required');
        $this->form_validation->set_rules('batch_no[]', $this->lang->line('batch_no'), 'required');
        $this->form_validation->set_rules('mrp[]', $this->lang->line('mrp'), 'required');
        $this->form_validation->set_rules('sale_rate[]', $this->lang->line('sale_price'), 'required');
        $this->form_validation->set_rules('quantity[]', $this->lang->line('quantity'), 'required|numeric');
        $this->form_validation->set_rules('purchase_price[]', $this->lang->line('purchase_price'), 'required|numeric');
        $this->form_validation->set_rules('total', $this->lang->line('total'), 'required|numeric');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'                 => form_error('date'),
                'supplier_id'          => form_error('supplier_id'),
                'medicine_category_id' => form_error('medicine_category_id[]'),
                'medicine_name'        => form_error('medicine_name[]'),
                'expiry_date'          => form_error('expiry_date[]'),
                'batch_no'             => form_error('batch_no[]'),
                'mrp'                  => form_error('mrp[]'),
                'sale_rate'            => form_error('sale_rate[]'),
                'quantity'             => form_error('quantity[]'),
                'purchase_price'       => form_error('purchase_price[]'),
                'total'                => form_error('total'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id               = $this->input->post('bill_basic_id');
            $bill_id          = $this->input->post("bill_detail_id[]");
            $previous_bill_id = $this->input->post("previous_bill_id[]");
            $supplier_id      = $this->input->post('supplier_id');
            $purchase_no      = $this->input->post('purchase_no');
            $data_array       = array();
            $delete_arr       = array();

            $bill_date = $this->input->post("date");
            $data      = array(
                'id'          => $id,
                'date'        => $this->customlib->dateFormatToYYYYMMDDHis($bill_date, $this->time_format),
                'invoice_no'  => $this->input->post('invoice_no'),
                'total'       => $this->input->post('total'),
                'discount'    => $this->input->post('discount'),
                'tax'         => $this->input->post('tax'),
                'note'        => $this->input->post('note'),
                'net_amount'  => $this->input->post('net_amount'),
            );

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/medicine_images/" . $img_name);
                $data_img = array('id' => $id, 'file' => 'uploads/medicine_images/' . $img_name);
                $this->pharmacy_model->addBillSupplier($data_img);
            }
            $this->pharmacy_model->addBillSupplier($data);

            if (!empty($id)) {

                $bill_detail_id       = $this->input->post('bill_detail_id');
                $medicine_batch_id    = $this->input->post('medicine_batch_id');
                $medicine_category_id = $this->input->post('medicine_category_id');
                $medicine_name        = $this->input->post('medicine_name');
                $expiry_date          = $this->input->post('expiry_date');
                $batch_no             = $this->input->post('batch_no');
                $batch_amount         = $this->input->post('batch_amount');
                $mrp                  = $this->input->post('mrp');
                $sale_rate            = $this->input->post('sale_rate');
                $packing_qty          = $this->input->post('packing_qty');
                $quantity             = $this->input->post('quantity');
                $total_quantity       = $this->input->post('available_quantity');
                $amount               = $this->input->post('amount');
                $purchase_price       = $this->input->post('purchase_price');
                $data_array1          = array();
                $bill_date1           = $this->input->post("date");
                $tax                  = $this->input->post("purchase_tax");
                $j                    = 0;
                foreach ($medicine_category_id as $key => $value) {
                    $expdate = $expiry_date[$j];
                    $explore = explode("/", $expdate);
                    $monthary = $explore[0];
                    $yearary  = $explore[1];
                    $month    = $monthary;

                    $month_number = $this->convertMonthToNumber($month);
                    $insert_date  = $yearary . "-" . $month_number . "-01";

                    if ($bill_id[$j] == 0) {
                        $add_data = array(
                            'supplier_bill_basic_id' => $id,
                            'pharmacy_id'            => $medicine_name[$j],
                            'inward_date'            => $this->customlib->dateFormatToYYYYMMDDHis($bill_date1, $this->time_format),
                            'expiry'                 => $insert_date,
                            'batch_no'               => $batch_no[$j],
                            'batch_amount'           => $batch_amount[$j],
                            'mrp'                    => $mrp[$j],
                            'sale_rate'              => $sale_rate[$j],
                            'packing_qty'            => $packing_qty[$j],
                            'quantity'               => $quantity[$j],
                            'available_quantity'     => $quantity[$j],
                            'purchase_price'         => $purchase_price[$j],
                            'amount'                 => $amount[$j],
                            'tax'                    => $tax[$j],
                        );
                        $data_array[] = $add_data;
                    } else {

                        $detail = array(
                            'id'                     => $bill_detail_id[$j],
                            'supplier_bill_basic_id' => $id,
                            'pharmacy_id'            => $medicine_name[$j],
                            'inward_date'            => $this->customlib->dateFormatToYYYYMMDDHis($bill_date1, $this->time_format),
                            'expiry'                 => $insert_date,
                            'batch_no'               => $batch_no[$j],
                            'batch_amount'           => $batch_amount[$j],
                            'mrp'                    => $mrp[$j],
                            'sale_rate'              => $sale_rate[$j],
                            'packing_qty'            => $packing_qty[$j],
                            'quantity'               => $quantity[$j],
                            'available_quantity'     => $quantity[$j],
                            'purchase_price'         => $purchase_price[$j],
                            'amount'                 => $amount[$j],
                            'tax'                    => $tax[$j],
                        );

                        $this->pharmacy_model->updateMedicineBatchDetail($detail);
                    }

                    $j++;
                }
            } else {

            }
            if (!empty($data_array)) {
                $this->pharmacy_model->addBillMedicineBatchSupplier($data_array);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function deletePharmacyBill()
    {
        $id = $this->input->post('id');
        if (!$this->rbac->hasPrivilege('pharmacy_bill', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->pharmacy_model->deletePharmacyBill($id);
            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 0, 'error' => '', 'message' => $this->lang->line('something_went_wrong'));
        }
        echo json_encode($array);
    }

    public function deleteSupplierBill($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_purchase', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->pharmacy_model->deleteSupplierBill($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function delete_medicine_batch($id)
    {       
        if (!empty($id)) {
            $this->pharmacy_model->delete_medicine_batch($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function getBillNo()
    {
        $result = $this->pharmacy_model->getBillNo();

        $id = $result["id"];
        if (!empty($result["id"])) {
            $bill_no = $id + 1;
        } else {
            $bill_no = 1;
        }
        echo json_encode($bill_no);
    }

    public function getExpiryDate()
    {
        $medicine_batch_detail_id = $this->input->get_post('medicine_batch_detail_id');
        $result                   = $this->pharmacy_model->getMedicineBatchByID($medicine_batch_detail_id);
        $result->expiry_date = $this->customlib->getMedicine_expire_month($result->expiry);
        echo json_encode($result);
    }

    public function getExpireDate()
    {        
        $batch_no         = $this->input->get_post('batch_no');
        $result           = $this->pharmacy_model->getExpireDate($batch_no);
        $result['expiry'] = $this->customlib->getMedicine_expire_month($result['expiry']);
        echo json_encode($result);
    }

    public function getBatchNoList()
    {
        $pharmacy_id = $this->input->get_post('pharmacy_id');
        $batch_id = $this->input->get_post('batch_id');
        $result      = $this->pharmacy_model->getBatchNoList($pharmacy_id,$batch_id);
        echo json_encode($result);
    }

    public function getmedicinedetails()
    {
        $pharmacy_id = $this->input->get_post('pharmacy_id');
        $result      = $this->pharmacy_model->getmedicinedetailsbyid($pharmacy_id);
        echo json_encode($result);
    }

    public function addBadStock()
    {
        if (!$this->rbac->hasPrivilege('medicine_bad_stock', 'can_view')) {
            access_denied();
        }
        $this->form_validation->set_rules('pharmacy_id', $this->lang->line('pharmacy_id'), 'required');
        $this->form_validation->set_rules('expiry_date', $this->lang->line('expiry_date'), 'required');
        $this->form_validation->set_rules('batch_no', $this->lang->line('batch_no'), 'required');
        $this->form_validation->set_rules('packing_qty', $this->lang->line('qty'), 'required|numeric');
        $this->form_validation->set_rules('inward_date', $this->lang->line('outward_date'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'pharmacy_id' => form_error('pharmacy_id'),
                'expiry_date' => form_error('expiry_date'),
                'batch_no'    => form_error('batch_no'),
                'packing_qty' => form_error('packing_qty'),
                'inward_date' => form_error('inward_date'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id          = $this->input->post('pharmacy_id');
            $inward_date = $this->input->post('inward_date');
            $expiry_date = $this->input->post('expiry_date');

            $explore = explode("/", $expiry_date);

            $monthary = $explore[0];
            $yearary  = $explore[1];
            $month    = $monthary;

            $month_number       = $this->convertMonthToNumber($month);
            $last_date_of_month = date("Y-m-t", strtotime($yearary . "-" . $month_number . "-01"));
            $insert_date        = $last_date_of_month;
            $medicine_batch     = array(
                'pharmacy_id'               => $id,
                'medicine_batch_details_id' => $this->input->post('medicine_batch_id'),
                'expiry_date'               => $insert_date,
                'outward_date'              => $this->customlib->dateFormatToYYYYMMDD($inward_date),
                'batch_no'                  => $this->input->post('batch_no'),
                'quantity'                  => $this->input->post('packing_qty'),
                'note'                      => $this->input->post('note'),
            );

            $batch_qty   = $this->input->post('available_quantity');
            $packing_qty = $this->input->post('packing_qty');

            if (!empty($batch_qty)) {
                $available_quantity = $batch_qty - $packing_qty;
            } else {
                $available_quantity = 0;
            }

            $update_data = array('id' => $this->input->post('medicine_batch_id'), 'available_quantity' => $available_quantity);

            $this->pharmacy_model->addBadStock($medicine_batch);
            $this->pharmacy_model->updateMedicineBatch($update_data);

            $event_data = array(
                'batch_no'     => $this->input->post('batch_no'),
                'expiry_date'  => $this->customlib->YYYYMMDDTodateFormat($insert_date),
                'outward_date' => $this->customlib->YYYYMMDDTodateFormat($inward_date),
                'qty'          => $this->input->post('packing_qty'),
            );

            $this->system_notification->send_system_notification('add_bad_stock', $event_data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function deleteBadStock($id, $medicine_batch_details_id)
    {
        if (!$this->rbac->hasPrivilege('medicine', 'can_view')) {
            access_denied();
        }
        if (!empty($id)) {
            $medicine_available_quantity               = $this->pharmacy_model->getsingleMedicineBatchdetails($medicine_batch_details_id);
            $bad_stock_quantity                        = $this->pharmacy_model->getsingleMedicineBadStock($id);
            $medicine_batch_data['id']                 = $medicine_batch_details_id;
            $medicine_batch_data['available_quantity'] = $medicine_available_quantity['available_quantity'] + $bad_stock_quantity['quantity'];
            $this->pharmacy_model->availableQty($medicine_batch_data);

            $this->pharmacy_model->deleteBadStock($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => '');
        }
        echo json_encode($array);
    }

    public function printTransaction()
    {
        $print_details         = $this->printing_model->get('', 'paymentreceipt');
        $id                    = $this->input->post('id');
        $charge                = array();
        $transaction           = $this->transaction_model->pharmacyPaymentByTransactionId($id);
        $data['print_details'] = $print_details;
        $data['transaction']   = $transaction;
        $page = $this->load->view('admin/pharmacy/_printTransaction', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
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
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('file_size_shoud_be_less_than') . " 2MB");
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

    public function downloadcheque($bill_id)
    {
        $result = $this->pharmacy_model->getSupplierDetails($bill_id);
        $this->load->helper('download');
        $filepath  = $result["attachment"];
        $file_name = $result["attachment_name"];
        $data      = file_get_contents($filepath);
        force_download($file_name, $data);
    }
	
    public function update_sale_rate(){
        $this->form_validation->set_rules('salerate[]', $this->lang->line('sale_price'), 'trim|required|xss_clean');       
        if ($this->form_validation->run() == false) {
            $msg   = array('sale_price' => form_error('salerate[]'));
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id         =   $this->input->post('id[]');
            $salerate   =   $this->input->post('salerate[]');
            $data1      =   array();
            $j          =   0;

            foreach ($id as $key => $value) {
                $details = array(
                'id'            => $id[$j],
                'sale_rate'     => $salerate[$j],
            );

            $data1[] = $details;
            $j++;
        }
        $this->pharmacy_model->update_sale_rate($data1);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }     
        echo json_encode($array);
    }

}