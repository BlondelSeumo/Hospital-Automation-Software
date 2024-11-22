<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Medicinedosage extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('datatables');
        $this->load->model('unittype_model');
        $this->load->helper('file');
        $this->load->model('pharmacy_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('medicine_dosage', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'medicine/index');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/medicinedosage');
        $medicinecategoryid = $this->input->post("medicinecategoryid");
        $data["title"]      = $this->lang->line('add_medicine_dosage');
        $medicineDosage     = $this->medicine_dosage_model->getMedicineDosage();
        $data['unitname']   = $this->pharmacy_model->getpharmacyunit();
        
        $dose_result       = array();
        foreach ($medicineDosage as $key => $value) {
            $dose_result[$value['medicine_category_id']][] = $value;
        }
        $data["medicineDosage"]   = $dose_result;
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $this->form_validation->set_rules('medicine_category', $this->lang->line('medicine_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('dosage', $this->lang->line('dosage_name'), 'trim|required|xss_clean');
        if ($this->form_validation->run()) {
            $medicineName     = $this->input->post("medicine_category");
            $medicinedosageid = $this->input->post("id");

            if (!empty($medicinedosageid)) {
                $data = array('medicine_category_id' => $medicineName, 'dosage' => $this->input->post('dosage'), 'id' => $medicinedosageid);
            } else {
                $data = array('medicine_category_id' => $medicineName, 'dosage' => $this->input->post('dosage'));
            }

            $insert_id = $this->medicine_dosage_model->addMedicineDosage($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('record_added_successfully') . '</div>');
            redirect("admin/medicinedosage/");
        } else {

            $this->load->view("layout/header");
            $this->load->view("admin/pharmacy/medicine_dosage", $data);
            $this->load->view("layout/footer");
        }
    }

    public function interval()
    {
        if (!$this->rbac->hasPrivilege('dosage_interval', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'medicine/index');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/medicinedosage/interval');
        $this->load->view("layout/header");
        $this->load->view("admin/pharmacy/dose_interval");
        $this->load->view("layout/footer");
    }

    public function duration()
    {
        if (!$this->rbac->hasPrivilege('dosage_duration', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'medicine/index');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/medicinedosage/duration');
        $this->load->view("layout/header");
        $this->load->view("admin/pharmacy/dose_duration");
        $this->load->view("layout/footer");
    }

    public function add_interval()
    {
        if (!$this->rbac->hasPrivilege('dosage_interval', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('interval'), 'trim|required|xss_clean');

        $interval_id = $this->input->post('id');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array('id' => $interval_id, 'name' => $this->input->post('name'));
            $this->medicine_dosage_model->add_interval($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }


    /* Function is used to add multiple interval name */
    public function add_multiple_interval() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('name_'.$row_value, $this->lang->line('interval'), 'trim|required');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array(
                        'name' => form_error('name_'.$row_value),
                    );
                    $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');  
                    echo json_encode($json_array);
                    return false;          
                }
            } 
            //validation to check each input keeps the value or not

            // save the multiple data 
            $total_rows   = $this->input->post('total_rows');  
            foreach ($total_rows as $row_key => $row_values) {                   
                $id   =  $this->input->post('id_' . $row_values) ;     
                $data = array('id' => $id, 'name' => $this->input->post('name_' . $row_values));
                $this->medicine_dosage_model->add_interval($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message=	$this->lang->line("please_add_at_least_one_interval");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}

 /* Function is used to add duration name */
    public function add_multiple_duration() {   
        $total_rows = $this->input->post('total_rows');
        if (isset($total_rows) && !empty($total_rows)) {          
          
          //validation to check each input keeps the value or not
            foreach ($total_rows as $row_key => $row_value) {  
                $this->form_validation->set_rules('name_'.$row_value, $this->lang->line('duration'), 'trim|required');        
                
                if ($this->form_validation->run() == false) {           
                    $msg = array(
                        'name' => form_error('name_'.$row_value),
                    );
                    $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');  
                    echo json_encode($json_array);
                    return false;          
                }
            } 
            //validation to check each input keeps the value or not

            // save the multiple data 
            $total_rows   = $this->input->post('total_rows');  
            foreach ($total_rows as $row_key => $row_values) {                   
                $id   =  $this->input->post('id_' . $row_values) ;     
                $data = array('id' => $id, 'name' => $this->input->post('name_' . $row_values));
                $this->medicine_dosage_model->add_duration($data);
            }
            // save the multiple data 

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));      
            echo json_encode($json_array);
    }else{
        //validation if user not added any row 
        $message=	$this->lang->line("please_add_at_least_one_duration");
        $json_array = array('status' => '2', 'error' => $message ,'message' => '');  
        //validation if user not added any row 
        echo json_encode($json_array);
    }
}

    public function add_duration()
    {
        if (!$this->rbac->hasPrivilege('dosage_duration', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('duration'), 'trim|required|xss_clean');

        $duration_id = $this->input->post('id');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array('id' => $duration_id, 'name' => $this->input->post('name'));
            $this->medicine_dosage_model->add_duration($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function get_doseintervalbyid($id)
    {
        $result = $this->medicine_dosage_model->get_intervalbyid($id);
        echo json_encode($result);
    }

    public function get_dosedurationbyid($id)
    {
        $result = $this->medicine_dosage_model->get_durationbyid($id);
        echo json_encode($result);
    }

    public function add()
    {
        $dosageid = $this->input->post("dosageid");
        foreach ($_POST['dosage'] as $key => $value) {
            $dose = $_POST['dosage'][$key];
            $unit = $_POST['unit'][$key];

            if ($dose == "") {
                $this->form_validation->set_rules('dosage', $this->lang->line('dose'), 'trim|required|xss_clean');
            }
            if ($unit == "") {
                $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'trim|required|xss_clean');
            }
        }

        $this->form_validation->set_rules('medicine_category', $this->lang->line('medicine_category'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'medicine_name' => form_error('medicine_category'),
                'dosage'        => form_error('dosage'),
                'unit'          => form_error('unit'),

            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            foreach ($_POST['dosage'] as $key => $value) {
                $data = array(
                    'medicine_category_id' => $_POST['medicine_category'],
                    'dosage'               => $_POST['dosage'][$key],
                    'units_id'             => $_POST['unit'][$key],
                );

                if (!empty($dosageid) && $dosageid != 0) {
                    $data['id'] = $dosageid;
                }
                $this->medicine_dosage_model->addMedicineDosage($data);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function get()
    {
        //get product data and encode to be JSON object
        header('Content-Type: application/json');
        echo $this->medicine_category_model->getall();
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_category', 'can_view')) {
            access_denied();
        }
        $result                   = $this->medicine_dosage_model->getMedicineDosage($id);
        $data["result"]           = $result;
        $data["title"]            = $this->lang->line('edit_category');
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $this->load->view("layout/header");
        $this->load->view("admin/pharmacy/medicine_dosage", $data);
        $this->load->view("layout/footer");
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_category', 'can_delete')) {
            access_denied();
        }
        $this->medicine_dosage_model->delete($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }

    public function get_data($id)
    {
        if (!$this->rbac->hasPrivilege('medicine_category', 'can_view')) {
            access_denied();
        }
        $result = $this->medicine_dosage_model->getMedicineDosage($id);
        echo json_encode($result);
    }

    public function getMedicineDosage()
    {
        $medicine = $this->input->post('medicine_id');
        $result   = $this->medicine_dosage_model->getDosageByMedicine($medicine);
        echo json_encode($result);
    }

    public function get_doseIntervallist()
    {
        $dt_response = $this->medicine_dosage_model->get_doseIntervallist();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = "<div class=''>";
                if ($this->rbac->hasPrivilege('dosage_interval', 'can_edit')) {
                    $action .= "<a  class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='get(" . $value->id . ")' data-original-title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('dosage_interval', 'can_delete')) {
                    $action .= "<a  class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='delete_intervalById(" . $value->id . ")' data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $action .= "</div>";
                //==============================
                $row[]     = $value->name;
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

    public function get_dosedurationlist()
    {
        $dt_response = $this->medicine_dosage_model->get_dosedurationlist();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = "<div class=''>";
                if ($this->rbac->hasPrivilege('dosage_duration', 'can_edit')) {
                    $action .= "<a  class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='get(" . $value->id . ")' data-original-title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('dosage_duration', 'can_delete')) {
                    $action .= "<a  class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='delete_durationById(" . $value->id . ")' data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                $action .= "</div>";
                //==============================
                $row[]     = $value->name;
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

    public function delete_doseInterval($id)
    {
        if (!$this->rbac->hasPrivilege('dosage_interval', 'can_delete')) {
            access_denied();
        }
        $this->medicine_dosage_model->delete_doseInterval($id);
    }

    public function delete_doseduration($id)
    {
        if (!$this->rbac->hasPrivilege('dosage_duration', 'can_delete')) {
            access_denied();
        }
        $this->medicine_dosage_model->delete_doseduration($id);
    }
}
