<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Visitors extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('datatables');
        $this->config->load("image_valid");
        $this->config->load("payroll");
        $this->visit_to = $this->config->item('visit_to');

    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/visitors');
        $this->form_validation->set_rules('purpose', $this->lang->line('purpose'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');

        if ($this->form_validation->run() == false) {
            $data['Purpose']  = $this->visitors_model->getPurpose();
            $data['visit_to'] = $this->visit_to;
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/visitorview', $data);
            $this->load->view('layout/footer');
        } else {
            $date     = $this->input->post('date');
            $visitors = array(
                'purpose'      => $this->input->post('purpose'),
                'name'         => $this->input->post('name'),
                'contact'      => $this->input->post('contact'),
                'id_proof'     => $this->input->post('id_proof'),
                'no_of_pepple' => $this->input->post('pepples'),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($date),
                'in_time'      => $this->input->post('time'),
                'out_time'     => $this->input->post('out_time'),
                'note'         => $this->input->post('note'),
            );
            $visitor_id = $this->visitors_model->add($visitors);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $visitor_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/visitors/" . $img_name);
                $this->visitors_model->image_add($visitor_id, $img_name);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('visitors_added_successfully') . '</div>');
            redirect('admin/visitors');
        }
    }

    public function getvisitorsdatatable()
    {
        $dt_response = $this->visitors_model->getAllvisitorsRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $action = '';
                $row = array();
                //====================================
                
               

                if ($value->image !== "") {
                    $action .= "<a href=" . base_url() . 'admin/visitors/download/' . $value->image . " class='btn btn-default btn-xs pull-right'  data-toggle='tooltip' title=''  data-original-title=" . $this->lang->line('download') . "><i class='fa fa-download' aria-hidden='true'></i></a>";
                }

                

                if ($this->rbac->hasPrivilege('visitor_book', 'can_delete')) {
                    $delete_message = $this->lang->line("delete_message");
                    $delete_message = str_replace(" ", "&nbsp;", $delete_message);
                    if ($value->image !== "") {
                        $action .= "<a href='#' onclick=deletevisitorimage('" . $value->id . ',' . $value->image . "') class='btn btn-default btn-xs pull-right'  data-toggle='tooltip' title=''  data-original-title=" . $this->lang->line('delete') . "><i class='fa fa-trash' aria-hidden='true'></i></a>";
                    } else {
                        $action .= "<a href='#' onclick=deletevisitor('" . $value->id . "') class='btn btn-default btn-xs pull-right'  data-toggle='tooltip' title=''  data-original-title=" . $this->lang->line('delete') . "><i class='fa fa-trash' aria-hidden='true'></i></a>";
                    }

                }
                
                if ($this->rbac->hasPrivilege('visitor_book', 'can_edit')) {
                    $action .= "<a href='#'  class='btn btn-default btn-xs pull-right'  data-toggle='tooltip' title=" . $this->lang->line('edit') . " onclick=get(" . $value->id . ")><i class='fa fa-pencil' aria-hidden='true'></i></a>";
                }
                
                $action .= "<a href='#' data-toggle='tooltip' class='btn btn-default btn-xs pull-right'  title='" . $this->lang->line('show') . "'    data-target='#visitordetails' data-original-title='" . $this->lang->line('view') . "' onclick='getRecord(" . $value->id . ")'>  <i class='fa fa-reorder'></i> </a>";
                
                if($value->visit_to){
                    $visit_to   =   $this->lang->line($value->visit_to);
                }else{
                    $visit_to   =   '';
                }

                //==============================
                $row[]         = $value->purpose;
                $row[]         = $value->name;
                $row[]         = $visit_to;
                $ipd_opd_staff = '';
                if ($value->visit_to == 'staff') {
                    $ipd_opd_staff = composeStaffNameByString($value->staff_name, $value->surname, $value->employee_id);
                } else if ($value->visit_to == 'opd_patient') {
                    $ipd_opd_staff = composePatientName($value->opd_patient_name, $value->opd_patient_id) . ' (' . $this->customlib->getSessionPrefixByType('opd_no') . $value->opd_no . ')';

                } else if ($value->visit_to == 'ipd_patient') {
                    $ipd_opd_staff = composePatientName($value->ipd_patient_name, $value->ipd_patient_id) . ' (' . $this->customlib->getSessionPrefixByType('ipd_no') . $value->ipd_no . ')';
                }
                $row[]     = $ipd_opd_staff;
                $row[]     = $value->contact;
                $row[]     = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[]     = $value->in_time;
                $row[]     = $value->out_time;
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
        $this->form_validation->set_rules('purpose', $this->lang->line('purpose'), 'required');
        $this->form_validation->set_rules('contact', $this->lang->line('phone'), 'numeric');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_message('check_default', $this->lang->line('purpose'));
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1' => form_error('purpose'),
                'e2' => form_error('name'),
                'e3' => form_error('date'),
                'e4' => form_error('check_default'),
                'e5' => form_error('file'),
                'e6' => form_error('contact'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date     = $this->input->post('date');
            $visitors = array(
                'purpose'          => $this->input->post('purpose'),
                'name'             => $this->input->post('name'),
                'contact'          => $this->input->post('contact'),
                'id_proof'         => $this->input->post('id_proof'),
                'visit_to'         => $this->input->post('visit_to'),
                'ipd_opd_staff_id' => $this->input->post('ipd_opd_staff'),
                'related_to'       => $this->input->post('related_to'),
                'no_of_pepple'     => $this->input->post('pepples'),
                'date'             => $this->customlib->dateFormatToYYYYMMDD($date),
                'in_time'          => $this->input->post('time'),
                'out_time'         => $this->input->post('out_time'),
                'note'             => $this->input->post('note'),
            );
            $visitor_id = $this->visitors_model->add($visitors);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $visitor_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/visitors/" . $img_name);
                $this->visitors_model->image_add($visitor_id, $img_name);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_delete')) {
            access_denied();
        }
        $this->visitors_model->delete($id);
    }

    public function deletevisitor()
    {
        $id = $this->input->post('id');
        $this->visitors_model->delete($id);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo 1;
    }

    public function deletevisitorimage()
    {
        $id    = $this->input->post('id');
        $image = $this->input->post('image');
        $this->visitors_model->image_delete($id, $image);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo 1;
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('id');
        $this->form_validation->set_rules('purpose', $this->lang->line('purpose'), 'required|callback_check_default');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_message('check_default', $this->lang->line('the_purpose_field_requred'));
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1' => form_error('purpose'),
                'e2' => form_error('name'),
                'e3' => form_error('date'),
                'e4' => form_error('check_default'),
                'e5' => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date     = $this->input->post('date');
            $visitors = array(
                'purpose'          => $this->input->post('purpose'),
                'name'             => $this->input->post('name'),
                'contact'          => $this->input->post('contact'),
                'id_proof'         => $this->input->post('id_proof'),
                'visit_to'         => $this->input->post('visit_to'),
                'ipd_opd_staff_id' => $this->input->post('ipd_opd_staff'),
                'related_to'       => $this->input->post('related_to'),
                'no_of_pepple'     => $this->input->post('pepples'),
                'date'             => $this->customlib->dateFormatToYYYYMMDD($date),
                'in_time'          => $this->input->post('time'),
                'out_time'         => $this->input->post('out_time'),
                'note'             => $this->input->post('note'),
            );
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/visitors/" . $img_name);
                $this->visitors_model->image_update($id, $img_name);
            }
            $this->visitors_model->update($id, $visitors);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function details($id)
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_view')) {
            access_denied();
        }

        $data['data'] = $this->visitors_model->visitors_list($id);
        $this->load->view('admin/frontoffice/Visitormodelview', $data);
    }

    public function download($documents)
    {
        $this->load->helper('download');
        $filepath = "./uploads/front_office/visitors/" . $documents;
        $data     = file_get_contents($filepath);
        $name     = $documents;
        force_download($name, $data);
    }

    public function imagedelete($id, $image)
    {
        if (!$this->rbac->hasPrivilege('visitor_book', 'can_delete')) {
            access_denied();
        }
        $this->visitors_model->image_delete($id, $image);
    }

    public function check_default($post_string)
    {
        return $post_string == "" ? false : true;
    }

    public function get_visitor($id)
    {
        $data   = $this->visitors_model->visitors_list($id);
        $a      = array('datedd' => $this->customlib->YYYYMMDDTodateFormat($data['date']));
        $result = array_merge($a, $data);
        echo json_encode($result);
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @filesize($_FILES['file']['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('error_file_uploading'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function get_ipd_opd_staff_list()
    {
        $visit_to = $this->input->get('visit_to');
        $result   = array();
        if ($visit_to == 'staff') {
            $result = $this->visitors_model->getstaff();
        } elseif ($visit_to == 'ipd_patient') {
            $result = $this->visitors_model->getipd();
        } elseif ($visit_to == 'opd_patient') {
            $result = $this->visitors_model->getopd();
        }
        echo json_encode(array('status' => 1, 'data' => $result, 'visit_to' => $visit_to));
    }

}
