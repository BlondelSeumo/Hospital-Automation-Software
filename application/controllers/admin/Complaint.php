<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Complaint extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('datatables');
        $this->config->load("image_valid");
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('complain', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/complaint');
        $this->form_validation->set_rules('name', $this->lang->line('complaint_by'), 'required');

        if ($this->form_validation->run() == false) {
            $data['complaint_type']  = $this->complaint_model->getComplaintType();
            $data['complaintsource'] = $this->complaint_model->getComplaintSource();
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/complaintview', $data);
            $this->load->view('layout/footer');
        } else {
            $date      = $this->input->post('date');
            $complaint = array(
                'complaint_type_id' => $this->input->post('complaint'),
                'source'            => $this->input->post('source'),
                'name'              => $this->input->post('name'),
                'contact'           => $this->input->post('contact'),
                'date'              => $this->customlib->dateFormatToYYYYMMDD($date),
                'description'       => $this->input->post('description'),
                'action_taken'      => $this->input->post('action_taken'),
                'assigned'          => $this->input->post('assigned'),
                'note'              => $this->input->post('note'),
            );

            $complaint_id = $this->complaint_model->add($complaint);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $complaint_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/complaints/" . $img_name);
                $this->complaint_model->image_add($complaint_id, $img_name);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('complaint_added_successfully') . ' </div>');
            redirect('admin/complaint');
        }
    }

    public function getcomplaintdatatable()
    {
        $dt_response = $this->complaint_model->getAllcomplaintRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();
                //====================================

                $action = "<a href='#' onclick=getRecord(" . $value->id . ") class='btn btn-default btn-xs'  data-toggle='tooltip' data-target='#complaintdetails' title=''  data-original-title=" . $this->lang->line('view') . "><i class='fa fa-reorder' aria-hidden='true'></i></a>";

                if ($value->image !== "") {
                    $action .= "<a href='" . base_url() . 'admin/complaint/download/' . $value->image . "' class='btn btn-default btn-xs' data-toggle='tooltip' title='' data-target='#editmyModal'  data-original-title=" . $this->lang->line('download') . "><i class='fa fa-download'></i></a>";
                }

                if ($this->rbac->hasPrivilege('complain', 'can_edit')) {
                    $action .= "<a href='#' onclick=get('" . $value->id . "') class='btn btn-default btn-xs pull-right' data-toggle='tooltip' data-target='#editmyModal' title='' data-target='#editmyModal'  data-original-title=" . $this->lang->line('edit') . "><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('complain', 'can_delete')) {
                    if ($value->image !== "") {
                        $action .= "<a href='#' onclick=delete_recordById('admin/complaint/imagedelete/" . $value->id . "/" . $value->image . "') class='btn btn-default btn-xs pull-right'  data-toggle='tooltip' title=''  data-original-title=" . $this->lang->line('delete') . "><i class='fa fa-trash' aria-hidden='true'></i></a>";
                    } else {

                        $action .= "<a href='#' onclick='delete_record(" . $value->id . ")' class='btn btn-default btn-xs pull-right'  data-toggle='tooltip' title='' data-original-title=" . $this->lang->line('delete') . "><i class='fa fa-trash' aria-hidden='true'></i></a>";
                    }

                }
                if($value->date){
                    $date = $this->customlib->YYYYMMDDTodateFormat($value->date);
                }else{
                    $date = '';
                }
				
				
                //==============================
                $row[] = $value->id;
                $row[] = $value->complaint_type;
                $row[] = $value->source;
                $row[] = $value->name;
                $row[] = $value->contact;
                $row[] = $date;
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
        $this->form_validation->set_rules('name', $this->lang->line('complain_by'), 'required');
        $this->form_validation->set_rules('contact', $this->lang->line('phone'), 'numeric');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'    => form_error('name'),
                'contact' => form_error('contact'),
                'file'    => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date      = $this->input->post('date');
            
            if($this->input->post('complaint')){
                $complaint = $this->input->post('complaint');
            }else{
                $complaint = null;
            }
            
            $complaint = array(
                'complaint_type_id' => $complaint,
                'source'            => $this->input->post('source'),
                'name'              => $this->input->post('name'),
                'contact'           => $this->input->post('contact'),
                'date'              => $this->customlib->dateFormatToYYYYMMDD($date),
                'description'       => $this->input->post('description'),
                'action_taken'      => $this->input->post('action_taken'),
                'assigned'          => $this->input->post('assigned'),
                'note'              => $this->input->post('note'),
            );

            $complaint_id = $this->complaint_model->add($complaint);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $complaint_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/complaints/" . $img_name);
                $this->complaint_model->image_add($complaint_id, $img_name);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function edit()
    {
        $id = $this->input->post('id');
        if (!$this->rbac->hasPrivilege('complain', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('complain_by'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('name'),
                'file' => form_error('file'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date      = $this->input->post('date');
            $complaint = array(
                'complaint_type_id' => $this->input->post('complaint'),
                'source'            => $this->input->post('source'),
                'name'              => $this->input->post('name'),
                'contact'           => $this->input->post('contact'),
                'date'              => $this->customlib->dateFormatToYYYYMMDD($date),
                'description'       => $this->input->post('description'),
                'action_taken'      => $this->input->post('action_taken'),
                'assigned'          => $this->input->post('assigned'),
                'note'              => $this->input->post('note'),
            );

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/complaints/" . $img_name);
                $this->complaint_model->image_add($id, $img_name);
            }
            $this->complaint_model->compalaint_update($id, $complaint);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function details($id)
    {
        if (!$this->rbac->hasPrivilege('complain', 'can_view')) {
            access_denied();
        }

        $data['complaint_data'] = $this->complaint_model->complaint_list($id);
        
        $this->load->view('admin/frontoffice/Complaintmodalview', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('complain', 'can_delete')) {
            access_denied();
        }

        $this->complaint_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('complaint_deleted_successfully') . ' </div>');
        redirect('admin/complaint');
    }

    public function download($image)
    {
        $this->load->helper('download');
        $filepath = "./uploads/front_office/complaints/" . $image;
        $data     = file_get_contents($filepath);
        $name     = $image;
        force_download($name, $data);
    }

    public function imagedelete($id, $image)
    {
        if (!$this->rbac->hasPrivilege('complain', 'can_delete')) {
            access_denied();
        }
        $this->complaint_model->image_delete($id, $image);
        echo json_encode(array("status" => 1, "msg" => $this->lang->line("delete_message")));
    }

    public function check_default($post_string)
    {
        return $post_string == "" ? false : true;
    }

    public function get_complaint($id)
    {
        $data = $this->complaint_model->complaint_list($id);
        $a    = array(
            'datedd' => $this->customlib->YYYYMMDDTodateFormat($data['date']),
        );
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

}
