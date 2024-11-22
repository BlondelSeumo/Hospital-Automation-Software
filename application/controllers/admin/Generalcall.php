<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Generalcall extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('datatables');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('phone_call_log', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/generalcall');
        $this->form_validation->set_rules('call_type', $this->lang->line('call_type'), 'required');
        $this->form_validation->set_rules('contact', $this->lang->line('phone_number'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        if ($this->form_validation->run() == false) {

            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/generalcallview');
            $this->load->view('layout/footer');
        } else {
            $date           = $this->input->post('date');
            $follow_up_date = $this->input->post('follow_up_date');
            $calls          = array(
                'name'           => $this->input->post('name'),
                'contact'        => $this->input->post('contact'),
                'date'           => $this->customlib->dateFormatToYYYYMMDD($date),
                'description'    => $this->input->post('description'),
                'follow_up_date' => $this->customlib->dateFormatToYYYYMMDD($follow_up_date),
                'call_duration'  => $this->input->post('call_dureation'),
                'note'           => $this->input->post('note'),
                'call_type'      => $this->input->post('call_type'),
            );

            $this->general_call_model->add($calls);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('call_added_successfully') . ' </div>');
            redirect('admin/generalcall');
        }
    }

    public function getgeneralcalldatatable()
    {
        $dt_response = $this->general_call_model->getAllgeneralcallRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                if ($value->follow_up_date) {
                    $followupdate = $this->customlib->YYYYMMDDTodateFormat($value->follow_up_date);
                } else {
                    $followupdate = "";
                }

                $action = "<a href='#' onclick=getRecord('" . $value->id . "') class='btn btn-default btn-xs'  data-toggle='tooltip' data-target='#calldetails' title='' data-original-title=" . $this->lang->line('view') . "><i class='fa fa-reorder' aria-hidden='true'></i></a>";

                if ($this->rbac->hasPrivilege('phone_call_log', 'can_edit')) {
                    $action .= "<a href='#' onclick=get('" . $value->id . "') class='btn btn-default btn-xs pull-right' data-toggle='tooltip' title='' data-target='#editmyModal'  data-original-title=" . $this->lang->line('edit') . "><i class='fa fa-pencil'></i></a>";
                }

                $delete_message = $this->lang->line("delete_message");
                $delete_message = str_replace(" ", "&nbsp;", $delete_message);
                if ($this->rbac->hasPrivilege('phone_call_log', 'can_delete')) {
                    $action .= "<a href='#' onclick=delete_ById(" . $value->id . ") class='btn btn-default btn-xs pull-right' data-toggle='tooltip' title=''  data-target='#editmyModal' data-original-title=" . $this->lang->line('delete') . "><i class='fa fa-trash'></i></a>";
                }

                //==============================
                $row[]     = $value->name;
                $row[]     = $value->contact;
                $row[]     = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[]     = $followupdate;
                $row[]     = $value->call_type;
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

    public function add()
    {
        $date = "";
        $this->form_validation->set_rules('call_type', $this->lang->line('call_type'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('contact', $this->lang->line('phone'), 'numeric');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'call_type' => form_error('call_type'),
                'name'      => form_error('name'),
                'contact'   => form_error('contact'),
                'date'   => form_error('date'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $follow_up_date = $this->input->post('follow_up_date');
            $getdate        = $this->input->post('date');
 
            if (!empty($follow_up_date)) {
                $date = $this->customlib->dateFormatToYYYYMMDD($follow_up_date);
            } else {
                $date = null;
            }
 
            $calls = array(
                'name'           => $this->input->post('name'),
                'contact'        => $this->input->post('contact'),
                'date'           => $this->customlib->dateFormatToYYYYMMDD($getdate),
                'description'    => $this->input->post('description'),
                'follow_up_date' => $date,
                'call_duration'  => $this->input->post('call_dureation'),
                'note'           => $this->input->post('note'),
                'call_type'      => $this->input->post('call_type'),
            );
            $this->general_call_model->add($calls);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('phone_call_log', 'can_edit')) {
            access_denied();
        }

        $date = "";
        $id   = $this->input->post('id');
        $this->form_validation->set_rules('call_type', $this->lang->line('call_type'), 'required|callback_check_default');
        $this->form_validation->set_message('check_default', $this->lang->line('the_call_type_field_is_required'));
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'call_type'     => form_error('call_type'),
                'check_default' => form_error('check_default'),
                'date'          => form_error('date'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $follow_up_date = $this->input->post('follow_up_date');
            if (!empty($follow_up_date)) {
                $date = $this->customlib->dateFormatToYYYYMMDD($follow_up_date);
            }
            $getdate      = $this->input->post('date');
            $calls_update = array(
                'name'           => $this->input->post('name'),
                'contact'        => $this->input->post('contact'),
                'date'           => $this->customlib->dateFormatToYYYYMMDD($getdate),
                'description'    => $this->input->post('description'),
                'follow_up_date' => $date,
                'call_duration'  => $this->input->post('call_dureation'),
                'note'           => $this->input->post('note'),
                'call_type'      => $this->input->post('call_type'),
            );

            $this->general_call_model->call_update($id, $calls_update);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function details($id)
    {
        if (!$this->rbac->hasPrivilege('phone_call_log', 'can_view')) {
            access_denied();
        }

        $data['Call_data'] = $this->general_call_model->call_list($id);
        $this->load->view('admin/frontoffice/Generalmodelview', $data);
    }

    public function delete($id)
    {
        $this->general_call_model->delete($id);
        echo json_encode(array('msg' => $this->lang->line('delete_message')));
    }

    public function check_default($post_string)
    {
        return $post_string == '' ? false : true;
    }

    public function get_calls($id)
    {
        $data                   = $this->general_call_model->call_list($id);
        $data['date']           = $this->customlib->YYYYMMDDTodateFormat($data['date']);
        $data['follow_up_date'] = $this->customlib->YYYYMMDDTodateFormat($data['follow_up_date']);
        echo json_encode($data);
    }

}
