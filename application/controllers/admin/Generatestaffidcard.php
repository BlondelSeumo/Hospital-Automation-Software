<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Generatestaffidcard extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array('Generatestaffidcard_model'));
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->load->library('Customlib');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('generate_staff_id_card', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatestaffidcard');
        $idcardlist            = $this->Generatestaffidcard_model->getstaffidcard();
        $data['idcardlist']    = $idcardlist;
        $staffRole             = $this->staff_model->getStaffRole();
        $data['staffRolelist'] = $staffRole;
        $this->load->view('layout/header');
        $this->load->view('admin/generatestaffidcard/generatestaffidcardview', $data);
        $this->load->view('layout/footer');
    }

    public function search()
    {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatestaffidcard');
        $staffRole             = $this->staff_model->getStaffRole();
        $data['staffRolelist'] = $staffRole;
        $idcardlist            = $this->Generatestaffidcard_model->getstaffidcard();
        $data['idcardlist']    = $idcardlist;
        $search                = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header');
            $this->load->view('admin/generatestaffidcard/generatestaffidcardview', $data);
            $this->load->view('layout/footer');
        } else {
            $role = $this->input->post('role_id');
            if (isset($search)) {
                $this->form_validation->set_rules('role_id', $this->lang->line('role'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('id_card', $this->lang->line('id_card_template'), 'trim|required|xss_clean');
                if ($this->form_validation->run() == false) {
                } else {
                    $data['staffrole'] = $this->input->post('role_id');
                    $data['idcard']    = $this->input->post('id_card');
                }
            }
            $this->load->view('layout/header');
            $this->load->view('admin/generatestaffidcard/generatestaffidcardview', $data);
            $this->load->view('layout/footer');
        }
    }

    public function checkvalidation()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('role_id', $this->lang->line('role'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('id_card', $this->lang->line('id_card_template'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'role_id' => form_error('role_id'),
                'id_card' => form_error('id_card'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'role_id' => $this->input->post('role_id'),
                'id_card' => $this->input->post('id_card'),

            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function getstafflistdatatable()
    {       

        $role   = $this->input->post('role_id');
        $idcard = $this->input->post('id_card');

        $idcardresult = $this->Generatestaffidcard_model->getidcardbyid($idcard);
        $dt_response  = $this->staff_model->getstaffbyroleidforidcard($role, 1);

        $dt_response = json_decode($dt_response);

        $dt_data = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================

                $action = "<input type='checkbox' class='checkbox center-block' data-staff_id='" . $value->id . "'  name='check' id='check' value='" . $value->id . "'>";

                $first_action = "<a href='" . base_url() . "admin/staff/profile/" . $value->id . "' >";

                //==============================
                $row[] = $action;
                $row[] = $value->employee_id;
                $row[] = $first_action . $value->name . ' ' . $value->surname . '</a>';
                $row[] = $value->designation;
                $row[] = $value->department;
                $row[] = $value->father_name;
                $row[] = $value->mother_name;
                
                if (!empty($value->date_of_joining) && $value->date_of_joining != '0000-00-00' && $value->date_of_joining != '1970-01-01') {
                    $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date_of_joining);                    
                } else {
                    $row[] = "";
                }
                
                $row[] = $value->local_address;
                $row[] = $value->contact_no;
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->dob);              
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

    public function generatemultiple()
    {
        $staffid                =   $this->input->post('data');
        $staff_array            =   json_decode($staffid);
        $idcard                 =   $this->input->post('id_card');
        $staffid_arr            =   array();
        $data['sch_setting']    =   $this->setting_model->get();
        $data['id_card']        =   $this->Generatestaffidcard_model->getidcardbyid($idcard);

        foreach ($staff_array as $key => $value) {
            $staffid_arr[] = $value->staff_id;
        }
        $staffs_arr = $this->Generatestaffidcard_model->getEmployee($staffid_arr, 1);
        $scan_type  = $this->sch_setting_detail->scan_code_type;
        foreach ($staffs_arr as $key => $staffs_value) {
             $staffs_arr[$key]->barcode = $this->customlib->generatestaffbarcode($staffs_value->employee_id,$scan_type);
        }

        $data['staffs']        = $staffs_arr;

        $id_cards = $this->load->view('admin/generatestaffidcard/generatemultiplestaffidcard', $data, true);

        $event_data = array(
            'role'             => $data['staffs'][0]->user_type,
            'staff_name'       => $data['staffs'][0]->name,
            'staff_surname'    => $data['staffs'][0]->surname,
            'employee_id'      => $data['staffs'][0]->employee_id,
            'id_card_template' => $data['id_card'][0]->title,
        );

        $this->system_notification->send_system_notification('generate_staff_id_card', $event_data);
        echo $id_cards;
    }

}
