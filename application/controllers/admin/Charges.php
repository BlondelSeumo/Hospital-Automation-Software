<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Charges extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('unittype_model');
        $this->load->model('taxcategory_model');
        $this->load->library('datatables');
        $this->load->library('system_notification');
    }

    public function index()
    {        
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/charges/index');
        $this->session->set_userdata('sub_menu', 'charges/index');
        $this->config->load("payroll");
        $charge_type         = $this->chargetype_model->get();
        $data["charge_type"] = $charge_type;
        $data['unit_type']   = $this->unittype_model->get();
        $data['schedule']    = $this->organisation_model->get();
        $data['taxcategory'] = $this->taxcategory_model->get();

        $this->load->view("layout/header");
        $this->load->view("admin/charges/charge", $data);
        $this->load->view("layout/footer");
    }

    public function getDatatable()
    {
        $dt_response = $this->charge_model->getDatatableAllRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $charge_key => $charge_value) {

                $row    = array();
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $action .= "<a href='#' onclick='viewDetail(" . $charge_value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('show') . "' > <i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('hospital_charges', 'can_edit')) {
                    $action .= "<a  href='javascript:void(0)' class='btn btn-default btn-xs edit_record edit_charge_modal' data-loading-text='" . $this->lang->line('please_wait') . "' data-toggle='tooltip' data-record-id=" . $charge_value->id . "  title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('hospital_charges', 'can_delete')) {
                    $action .= "<a class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='delete_recordById(\"admin/charges/delete/" . $charge_value->id . "\", \"" . $this->lang->line('delete_message') . "\")' data-original-title='" . $this->lang->line('delete') . "'> <i class='fa fa-trash'></i></a>";
                }

                $action .= "</div>";

                $row[] = $charge_value->name . $action;
                $row[] = $charge_value->charge_category_name;
                $row[] = $charge_value->charge_type_name;
                $row[] = $charge_value->unit;
                $row[] = $charge_value->percentage;
                $row[] = $charge_value->standard_charge;

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

    public function add_charges()
    {
        if (!$this->rbac->hasPrivilege('hospital_charges', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('charge_type', $this->lang->line('charge_type'), 'required');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge_category'), 'required');
        $this->form_validation->set_rules('unit_type', $this->lang->line('unit_type'), 'required');
        $this->form_validation->set_rules('charge_name', $this->lang->line('charge_name'), 'required');
        $this->form_validation->set_rules('taxcategory', $this->lang->line('tax_category'), 'required');
        $this->form_validation->set_rules('standard_charge', $this->lang->line('standard_charge'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'charge_type'     => form_error('charge_type'),
                'charge_category' => form_error('charge_category'),
                'unit_type'       => form_error('unit_type'),
                'charge_name'     => form_error('charge_name'),
                'taxcategory'     => form_error('taxcategory'),
                'standard_charge' => form_error('standard_charge'),

            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $data = array(
                'id'                 => $this->input->post('id'),
                'charge_category_id' => $this->input->post('charge_category'),
                'name'               => $this->input->post('charge_name'),
                'description'        => $this->input->post('description'),
                'standard_charge'    => $this->input->post('standard_charge'),
                'charge_unit_id'     => $this->input->post('unit_type'),
                'tax_category_id'    => $this->input->post('taxcategory'),
            );

            $schedule_charge      = $this->input->post('schedule_charge_id');
            $i                    = 0;
            $organisation_charges = array();
            if (!empty($schedule_charge)) {
                foreach ($schedule_charge as $key => $value) {
                    $org_charge    = $this->input->post("schedule_charge_" . $value);
                    $schedule_data = array(
                        'charge_id'  => null,
                        'org_id'     => $value,
                        'org_charge' => $org_charge,
                    );

                    $organisation_charges[] = $schedule_data;
                }
            }
            $insert_id  = $this->charge_model->add($data, $organisation_charges);
            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($json_array);
    }

    public function get_charge_category()
    {
        $charge_type = $this->input->post("charge_type");
        $data        = $this->charge_model->getChargeCategory($charge_type);
        echo json_encode($data);
    }

    public function getChargeByModule()
    {
        $module_shortcode = $this->input->post("module");
        $charge_category  = $this->charge_category_model->getCategoryByModule($module_shortcode);
        echo json_encode($charge_category);
    }

    public function getDetails()
    {
        if (!$this->rbac->hasPrivilege('hospital_charges', 'can_view')) {
            access_denied();
        }
        $id           = $this->input->post("charges_id");
        $organisation = $this->input->post("organisation");
        $result       = $this->charge_model->getDetails($id, $organisation);
        $json_array   = array('status' => '1', 'error' => '', 'result' => $result);
        echo json_encode($json_array);
    }

    public function viewDetails()
    {
        if (!$this->rbac->hasPrivilege('hospital_charges', 'can_view')) {
            access_denied();
        }
        $id             = $this->input->post("charges_id");
        $data['result'] = $this->charge_model->getDetails($id, "");
        $page           = $this->load->view("admin/charges/_viewDetails", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function getScheduleChargeBatch()
    {
        $id                = $this->input->post("charges_id");
        $result            = $this->charge_model->getScheduleChargeBatch($id);
        $data["result"]    = $result;
        $allCharge         = $this->charge_model->getOrganisationCharges($id);
        $data["allCharge"] = $allCharge;
        $this->load->view('admin/charges/schedulechargeDetail', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('hospital_charges', 'can_delete')) {
            access_denied();
        }
        $result = $this->charge_model->delete($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

    public function scheduleChargeBatchGet()
    {
        $id                = $this->input->post("charges_id");
        $result            = $this->charge_model->getScheduleChargeBatch($id);
        $data["result"]    = $result;
        $allCharge         = $this->charge_model->getOrganisationCharges($id);
        $data["allCharge"] = $allCharge;
        $this->load->view('admin/charges/schedulechargeEdit', $data);
    }

    public function add_ipdcharges()
    {
        $add_type = $this->input->post('add_type');
        if($add_type == 'save'){  

            $total_rows = $this->input->post('pre_charge_id');

            if (!isset($total_rows)) {
                $msg        = array('no_records' => $this->lang->line('please_add_charge_details'));
                $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            } else {

                $is_tpa = $this->input->post('is_tpa');
                if($is_tpa=="" || $is_tpa==0){
                    $organisation_id = null;
                    $insurance_validity = null;
                    $insurance_id = null;
                }else{           
                    $organisation_id    = $this->input->post('organisation_id');
                    $insurance_id       = $this->input->post('insurance_id');
                    $insurance_validity = $this->input->post('insurance_validity') ;
                }
				
				if (empty($organisation_id)) {
						$organisation_id = null;
						$insurance_validity = null;
						$insurance_id = null;
				}
			
                $charge_data = $this->input->post('pre_charge_id');
                foreach ($charge_data as $key => $value) {
                    $date              = $this->input->post('pre_date')[$key];
                    $patient_charge_id = $this->input->post('patient_charge_id');
                    $insert_data       = array(
                        'date'                  => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->customlib->getHospitalTimeFormat()),
                        'charge_id'             => $this->input->post('pre_charge_id')[$key],
                        'qty'                   => $this->input->post('pre_qty')[$key],
                        'ipd_id'                => $this->input->post('ipdid'),
                        'tpa_charge'            => $this->input->post('pre_tpa_charges')[$key],
                        'apply_charge'          => $this->input->post('pre_apply_charge')[$key],
                        'standard_charge'       => $this->input->post('pre_standard_charge')[$key],
                        'amount'                => $this->input->post('pre_net_amount')[$key],
                        'discount_percentage'   => $this->input->post('pre_discount_percentage')[$key],
                        'created_at'            => date('Y-m-d'),
                        'note'                  => $this->input->post('pre_note')[$key],
                        'tax'                   => $this->input->post('pre_tax_percentage')[$key],
                        'organisation_id'       => $organisation_id,
                        'insurance_id'          => $insurance_id,
                        'insurance_validity'    => $insurance_validity
                    );

                    if ($patient_charge_id > 0) {
                        $insert_data['id'] = $patient_charge_id;
                    }

                $this->charge_model->add_charges($insert_data);                    
                $preview_data = $this->charge_model->getDetails($this->input->post('pre_charge_id')[$key], "");
                  
                $doctor_list       = $this->patient_model->getDoctorsipd($this->input->post('ipdid'));
                $consultant_doctor = $this->patient_model->get_patientidbyIpdId($this->input->post('ipdid'));
                $consultant_doctorarray[] = array('consult_doctor' => $consultant_doctor['cons_doctor'], 'name' =>$consultant_doctor['doctor_name'] . " " . $consultant_doctor['doctor_surname'] . "(" . $consultant_doctor['doctor_employee_id'] . ")");              
				 
                $event_data = array(
                    'patient_id'      => $consultant_doctor['patient_id'],
                    'ipd_no'          => $this->customlib->getSessionPrefixByType('ipd_no') . $this->input->post('ipdid'),
                    'charge_type'     => $preview_data->charge_type_name,
                    'charge_category' => $preview_data->charge_category_name,
                    'charge_name'     => $preview_data->name,
                    'qty'             => $this->input->post('pre_qty')[$key],
                    'net_amount'      => $this->input->post('pre_net_amount')[$key],
                    'edit_note'      => $this->input->post('pre_note')[$key],                    
                    'date'            => $date,
                );
                
                foreach ($doctor_list as $key1 => $value) {
                    $consultant_doctorarray[] = array('consult_doctor' => $value['consult_doctor'], 'name' => $value['ipd_doctorname'] . " " . $value['ipd_doctorsurname'] . "(" . $value['employee_id'] . ")");
                }                

                $this->system_notification->send_system_notification('add_ipd_patient_charge', $event_data, $consultant_doctorarray);                   
                    
                }
                $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
            }
        }else{

            $this->form_validation->set_rules('charge_type', $this->lang->line('charge_type'), 'required');
            $this->form_validation->set_rules('qty', $this->lang->line('qty'), 'required');
            $this->form_validation->set_rules('charge_category', $this->lang->line('charge_category'), 'required');
            $this->form_validation->set_rules('apply_charge', $this->lang->line('applied_charge'), 'required');
            $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required');
            $this->form_validation->set_rules('charge_id', $this->lang->line('charge_name'), 'required');
            $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
 
            if ($this->form_validation->run() == false) {
                $msg = array(
                    'qty'             => form_error('qty'),
                    'date'            => form_error('date'),
                    'charge_type'     => form_error('charge_type'),
                    'charge_category' => form_error('charge_category'),
                    'apply_charge'    => form_error('apply_charge'),
                    'amount'          => form_error('amount'),
                    'charge_id'       => form_error('charge_id'),
                );
                $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            } else {

                $preview_data = $this->charge_model->getDetails($_POST['charge_id'], "");
                $data   =  $this->input->post('date');
                $temp_data = array(
                    'charge_id'          => $preview_data->id,
                    'charge_name'        => $preview_data->name,
                    'charge_type_id'     => $preview_data->charge_type_master_id,
                    'charge_type_name'   => $preview_data->charge_type_name,
                    'charge_category'    => $preview_data->charge_category_name,
                    'charge_category_id' => $preview_data->charge_category_id,
                    'qty'                => $this->input->post('qty'),
                    'apply_charge'       => $this->input->post('apply_charge'),
                    'standard_charge'    => $this->input->post('standard_charge'),
                    'tpa_charge'         => $this->input->post('schedule_charge'),
                    'amount'             => $this->input->post('apply_charge'),
                    'tax'                => $this->input->post('tax'),
                    'net_amount'         => $this->input->post('amount'),
                    'tax_percentage'     => $this->input->post('charge_tax'),
                    'discount_percentage'               => $this->input->post('discount_percentage'),
                    'discount_percentage_amount'               => $this->input->post('discount_percentage_amount'),
                    'note'               => $this->input->post('note'),
                    'date'               => $data
                );
               
                $json_array = array('status' => 'new_charge', 'error' => '', 'data' => $temp_data);
            }
        }
        echo json_encode($json_array);
    }

    public function edit_ipdcharges()
    {
        $this->form_validation->set_rules('charge_type', $this->lang->line('charge_type'), 'required');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge_category'), 'required');
        $this->form_validation->set_rules('apply_charge', $this->lang->line('applied_charge'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required');
        $this->form_validation->set_rules('charge_id', $this->lang->line('charge_name'), 'required');
        $this->form_validation->set_rules('qty', $this->lang->line('qty'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('charge_tax', $this->lang->line('tax'), 'required');
        $this->form_validation->set_rules('editdiscount_percentage', $this->lang->line('discount'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'date'            => form_error('date'),
                'charge_type'     => form_error('charge_type'),
                'charge_category' => form_error('charge_category'),
                'apply_charge'    => form_error('apply_charge'),
                'amount'          => form_error('amount'),
                'qty'             => form_error('qty'),
                'charge_id'       => form_error('charge_id'),
                'charge_tax'      => form_error('charge_tax'),
                'editdiscount_percentage'      => form_error('editdiscount_percentage'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $is_tpa = $this->input->post('is_tpa');
                if($is_tpa=="" || $is_tpa==0){
                    $organisation_id = null;
                    $insurance_validity = null;
                    $insurance_id = null;
                }else{           
                    $organisation_id    = $this->input->post('organisation_id');
                    $insurance_id       = $this->input->post('insurance_id');
                    $insurance_validity = $this->input->post('insurance_validity') ;
                }
           
            $patient_charge_id = $this->input->post('patient_charge_id');
            $date              = $this->input->post('date');
            $insert_data       = array(
                'date'                  => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->customlib->getHospitalTimeFormat()),
                'charge_id'             => $this->input->post('charge_id'),
                'qty'                   => $this->input->post('qty'),
                'ipd_id'                => $this->input->post('ipdid'),
                'apply_charge'          => $this->input->post('apply_charge'),
                'amount'                => $this->input->post('amount'),
                'standard_charge'       => $this->input->post('standard_charge'),
                'tpa_charge'            => $this->input->post('schedule_charge'),
                'created_at'            => date('Y-m-d'),
                'note'                  => $this->input->post('note'),
                'tax'                   => $this->input->post('charge_tax'),
                'discount_percentage'   => $this->input->post('editdiscount_percentage'),
                'organisation_id'       => $organisation_id,
                'insurance_id'          => $insurance_id,
                'insurance_validity'    => $insurance_validity
            );
            if ($patient_charge_id > 0) {
                $insert_data['id'] = $patient_charge_id;
            }
            $this->charge_model->add_charges($insert_data);
            $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
        }
        echo json_encode($json_array);
    }

    public function edit_opdcharges()
    {
        $this->form_validation->set_rules('charge_type', $this->lang->line('charge_type'), 'required');
        $this->form_validation->set_rules('qty', $this->lang->line('qty'), 'required');
        $this->form_validation->set_rules('charge_category', $this->lang->line('charge_category'), 'required');
        $this->form_validation->set_rules('apply_charge', $this->lang->line('applied_charge'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required');
        $this->form_validation->set_rules('charge_id', $this->lang->line('charge_name'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('editdiscount_percentage', $this->lang->line('discount_percentage'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'qty'             => form_error('qty'),
                'date'            => form_error('date'),
                'charge_type'     => form_error('charge_type'),
                'charge_category' => form_error('charge_category'),
                'apply_charge'    => form_error('apply_charge'),
                'amount'          => form_error('amount'),
                'charge_id'       => form_error('charge_id'),
                'editdiscount_percentage'       => form_error('editdiscount_percentage'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
             $is_tpa = $this->input->post('edit_is_tpa');
                    if($is_tpa=="" || $is_tpa==0){
                        $organisation_id = null;
                        $insurance_validity = null;
                        $insurance_id = null;
                    }else{           
                        $organisation_id    = $this->input->post('organisation_id');
                        $insurance_id       = $this->input->post('insurance_id');
                        $insurance_validity = $this->input->post('insurance_validity') ;
                    }
            $date              = $this->input->post('date');
            $patient_charge_id = $this->input->post('patient_charge_id');
            $insert_data       = array(
                'date'                  => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->customlib->getHospitalTimeFormat()),
                'charge_id'             => $this->input->post('charge_id'),
                'qty'                   => $this->input->post('qty'),
                'opd_id'                => $this->input->post('opd_id'),
                'apply_charge'          => $this->input->post('apply_charge'),
                'standard_charge'       => $this->input->post('standard_charge'),
                'tpa_charge'            => $this->input->post('schedule_charge'),
                'amount'                => $this->input->post('amount'),
                'discount_percentage'   => $this->input->post('editdiscount_percentage'),
                'created_at'            => date('Y-m-d'),
                'note'                  => trim($this->input->post('note')),
                'tax'                   => $this->input->post('charge_tax'),
                'organisation_id'       => $organisation_id,
                'insurance_id'          => $insurance_id,
                'insurance_validity'    => $insurance_validity,
            );

            if ($patient_charge_id > 0) {
                $insert_data['id'] = $patient_charge_id;
            }

            $this->charge_model->add_charges($insert_data);
            $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
        }
        echo json_encode($json_array);
    }

    public function add_opdcharges()
    {
        $add_type = $this->input->post('add_type');
        if ($add_type == 'save') {
        
            $total_rows = $this->input->post('pre_charge_id');
            if (!isset($total_rows)) {
                $msg        = array('no_records' => $this->lang->line('please_add_charge_details'));
                $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            } else {
                  $is_tpa = $this->input->post('is_tpa');
				  
                    if($is_tpa=="" || $is_tpa==0){
                        $organisation_id = null;
                        $insurance_validity = null;
                        $insurance_id = null;
                    }else{           
                        $organisation_id    = $this->input->post('organisation_id');
                        $insurance_id       = $this->input->post('insurance_id');
                        $insurance_validity = $this->input->post('insurance_validity') ;
                    }
					
					if(empty($organisation_id)){
						$organisation_id = null;
					}
					
					
                $charge_data = $this->input->post('pre_charge_id');
                foreach ($charge_data as $key => $value) {
                    $date              = $this->input->post('pre_date')[$key];
                    $patient_charge_id = $this->input->post('patient_charge_id');		            
                    $insert_data       = array(
                        'date'            => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->customlib->getHospitalTimeFormat()),
                        'charge_id'       => $this->input->post('pre_charge_id')[$key],
                        'qty'             => $this->input->post('pre_qty')[$key],
                        'opd_id'          => $this->input->post('opd_id'),
                        'tpa_charge'      => $this->input->post('pre_tpa_charges')[$key],
                        'apply_charge'    => $this->input->post('pre_apply_charge')[$key],
                        'standard_charge' => $this->input->post('pre_standard_charge')[$key],
                        'amount'          => $this->input->post('pre_net_amount')[$key],
                        'created_at'      => date('Y-m-d'),
                        'discount_percentage'=>$this->input->post('pre_discount_percentage')[$key],
                        'note'            => $this->input->post('pre_note')[$key],
                        'tax'             => $this->input->post('pre_tax_percentage')[$key],
                        'organisation_id'    => $organisation_id,
                        'insurance_id'       => $insurance_id,
                        'insurance_validity' => $insurance_validity,
                    );

                    if ($patient_charge_id > 0) {
                        $insert_data['id'] = $patient_charge_id;
                    }
                    $preview_data   = $this->charge_model->getDetails($this->input->post('pre_charge_id')[$key], "");
                    $patient_data   = $this->patient_model->get_patientidbyopdid($this->input->post('opd_id'));
                    $doctor_details = $this->notificationsetting_model->getstaffDetails($patient_data['doctor_id']);

                    $event_data = array(
                        'patient_id'      => $patient_data['patient_id'],
                        'doctor_id'       => $patient_data['doctor_id'],
                        'doctor_name'     => composeStaffNameByString($doctor_details['name'], $doctor_details['surname'], $doctor_details['employee_id']),
                        'opd_no'          => $this->customlib->getSessionPrefixByType('opd_no') . $this->input->post('opd_id'),
                        'charge_type'     => $preview_data->charge_type_name,
                        'charge_category' => $preview_data->charge_category_name,
                        'charge_name'     => $preview_data->name,
                        'qty'             => $this->input->post('pre_qty')[$key],
                        'net_amount'      => $this->input->post('pre_net_amount')[$key],
                        'edit_note'       => $this->input->post('pre_note')[$key],
                        'date'            => $date,                       
                         
                    );

                    $this->system_notification->send_system_notification('add_opd_patient_charge', $event_data);
                    $this->charge_model->add_charges($insert_data);
                }
                $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('record_saved_successfully'));
            }
        } else {
            $this->form_validation->set_rules('charge_type', $this->lang->line('charge_type'), 'required');
            $this->form_validation->set_rules('qty', $this->lang->line('qty'), 'required');
            $this->form_validation->set_rules('charge_category', $this->lang->line('charge_category'), 'required');
            $this->form_validation->set_rules('apply_charge', $this->lang->line('applied_charge'), 'required');
            $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required');
            $this->form_validation->set_rules('charge_id', $this->lang->line('charge_name'), 'required');
            $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');

            if ($this->form_validation->run() == false) {
                $msg = array(
                    'qty'             => form_error('qty'),
                    'date'            => form_error('date'),
                    'charge_type'     => form_error('charge_type'),
                    'charge_category' => form_error('charge_category'),
                    'apply_charge'    => form_error('apply_charge'),
                    'amount'          => form_error('amount'),
                    'charge_id'       => form_error('charge_id'),
                );
                $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            } else {

                $preview_data = $this->charge_model->getDetails($_POST['charge_id'], "");
                $data   =  $this->input->post('date');
                $temp_data = array(
                    'charge_id'          => $preview_data->id,
                    'charge_name'        => $preview_data->name,
                    'charge_type_id'     => $preview_data->charge_type_master_id,
                    'charge_type_name'   => $preview_data->charge_type_name,
                    'charge_category'    => $preview_data->charge_category_name,
                    'charge_category_id' => $preview_data->charge_category_id,
                    'qty'                => $this->input->post('qty'),
                    'apply_charge'       => $this->input->post('apply_charge'),
                    'standard_charge'    => $this->input->post('standard_charge'),
                    'tpa_charge'         => $this->input->post('schedule_charge'),
                    'amount'             => $this->input->post('apply_charge'),
                    'tax'                => $this->input->post('tax'),
                    'tax_percentage'     => $this->input->post('charge_tax'),
                    'net_amount'         => $this->input->post('amount'),
                    'note'               => $this->input->post('note'),
                    'discount_percentage'      =>$this->input->post('discount_percentage'),
                    'discount_percentage_amount'      =>amountFormat($this->input->post('discount_percentage_amount')),
                    'date'               => $data
                );			 
 
                $json_array = array('status' => 'new_charge', 'error' => '', 'data' => $temp_data);
            }
        }
        echo json_encode($json_array);
    }

    public function getchargeDetails()
    {
        $charge_category = $this->input->post("charge_category");
        $result          = $this->charge_model->getchargeDetails($charge_category);
        echo json_encode($result);
    }

    public function deleteOpdPatientCharge($pateint_id, $id, $opdid)
    {
        if (!$this->rbac->hasPrivilege('charges', 'can_delete')) {
            access_denied();
        }
        $this->charge_model->deleteOpdPatientCharge($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Patient Charges deleted successfully</div>');
        redirect('admin/patient/visitDetails/' . $pateint_id . '/' . $opd_id . '#charges');
    }

}
