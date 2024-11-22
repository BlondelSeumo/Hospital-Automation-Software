<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Receive extends Admin_Controller
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
        if (!$this->rbac->hasPrivilege('postal_receive', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/receive');
        $this->form_validation->set_rules('from_title', $this->lang->line('from_title'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/receiveview');
            $this->load->view('layout/footer');
        } else {
            $date     = $this->input->post('date');
            $dispatch = array(
                'reference_no' => $this->input->post('ref_no'),
                'to_title'     => $this->input->post('to_title'),
                'address'      => $this->input->post('address'),
                'note'         => $this->input->post('note'),
                'from_title'   => $this->input->post('from_title'),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($date),
                'type'         => 'receive',
            );
            $dispatch_id = $this->dispatch_model->insert('dispatch_receive', $dispatch);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $dispatch_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/dispatch_receive/" . $img_name);
                $this->dispatch_model->image_add('receive', $dispatch_id, $img_name);
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('receive_added_successfully') . '</div>');
            redirect('admin/receive');
        }
    }

    public function getreceivedatatable()
    {
        $dt_response = $this->dispatch_model->getAllreceiveRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = " <a href='#' data-toggle='tooltip' class='btn btn-default btn-xs'  title='" . $this->lang->line('show') . "'  data-target='#receviedetails' data-original-title='" . $this->lang->line('view') . "' onclick='getRecord(" . $value->id . ")'>  <i class='fa fa-reorder'></i> </a>";

                if ($value->image !== "") {
                    $action .= "<a href=" . base_url() . 'admin/receive/download/' . $value->image . " class='btn btn-default btn-xs' data-toggle='tooltip' title=''  data-original-title=" . $this->lang->line('download') . "><i class='fa fa-download' aria-hidden='true'></i></a>";
                }

                if ($this->rbac->hasPrivilege('postal_receive', 'can_edit')) {
                    $action .= "<a href='#'  class='btn btn-default btn-xs'  data-toggle='tooltip' title=" . $this->lang->line('edit') . " data-target='#editmyModal' onclick=get(" . $value->id . ")><i class='fa fa-pencil' aria-hidden='true'></i></a>";
                }

                if ($this->rbac->hasPrivilege('postal_receive', 'can_delete')) {
                    if ($value->image !== "") {
                        $action .= "<a href='#' onclick=delete_ById(" . $value->id . ") class='btn btn-default btn-xs' data-toggle='tooltip' title=''  data-original-title=" . $this->lang->line('delete') . "><i class='fa fa-trash' aria-hidden='true'></i></a>";
                    } else {

                        $action .= "<a href='#' onclick=delete_ById(" . $value->id . ") class='btn btn-default btn-xs' data-toggle='tooltip' title=''  data-original-title=" . $this->lang->line('delete') . "><i class='fa fa-trash' aria-hidden='true'></i></a>";
                    }

                }

                //==============================
                $row[]     = $value->from_title;
                $row[]     = $value->reference_no;
                $row[]     = $value->to_title;                
                $row[]     = $value->address;
                $row[]     = $value->note;
                if($value->date){
                $row[]     = $this->customlib->YYYYMMDDTodateFormat($value->date);
                }else{
                $row[]     = '';    
                }
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

    public function download($img)
    {
        $this->load->helper('download');
        $filepath = "./uploads/front_office/dispatch_receive/" . $img;
        $data     = file_get_contents($filepath);
        $name     = $img;
        force_download($name, $data);
    }

    public function add()
    {
        $this->form_validation->set_rules('from_title', $this->lang->line('from_title'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('from_title'),
                'file' => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date     = $this->input->post('date');
            $dispatch = array(
                'reference_no' => $this->input->post('ref_no'),
                'to_title'     => $this->input->post('to_title'),
                'address'      => $this->input->post('address'),
                'note'         => $this->input->post('note'),
                'from_title'   => $this->input->post('from_title'),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($date),
                'type'         => 'receive',
            );
            $dispatch_id = $this->dispatch_model->insert('dispatch_receive', $dispatch);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $dispatch_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/dispatch_receive/" . $img_name);
                $this->dispatch_model->image_add('receive', $dispatch_id, $img_name);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function editreceive()
    {
        if (!$this->rbac->hasPrivilege('postal_receive', 'can_view')) {
            access_denied();
        }
        $id = $this->input->post('id');
        $this->form_validation->set_rules('from_title', $this->lang->line('from_title'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('from_title'),
                'file' => form_error('file'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date    = $this->input->post('date');
            $receive = array(
                'reference_no' => $this->input->post('ref_no'),
                'from_title'   => $this->input->post('from_title'),
                'address'      => $this->input->post('address'),
                'note'         => $this->input->post('note'),
                'to_title'     => $this->input->post('to_title'),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($date),
                'type'         => 'receive',
            );
            $this->dispatch_model->update_dispatch('dispatch_receive', $id, 'receive', $receive);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/dispatch_receive/" . $img_name);
                $this->dispatch_model->image_update('dispatch', $id, $img_name);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('postal_receive', 'can_delete')) {
            access_denied();
        }
        $this->dispatch_model->delete($id);
        echo json_encode(array('msg' => $this->lang->line('delete_message')));
    }

    public function imagedelete($id, $image)
    {
        if (!$this->rbac->hasPrivilege('postal_receive', 'can_delete')) {
            access_denied();
        }
        $this->dispatch_model->image_delete($id, $image);
    }

    public function get_receive($id)
    {
        $data = $this->dispatch_model->recevie_data($id);
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
