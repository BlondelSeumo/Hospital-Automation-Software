<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dispatch extends Admin_Controller
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
        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/dispatch');
        $this->form_validation->set_rules('to_title', $this->lang->line('to_title'), 'required');
        if ($this->form_validation->run() == false) {

            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/dispatchview');
            $this->load->view('layout/footer');
        } else {
            $date     = $this->input->post('date');
            $dispatch = array(
                'reference_no' => $this->input->post('ref_no'),
                'to_title'     => $this->input->post('to_title'),
                'address'      => $this->input->post('address'),
                'note'         => $this->input->post('note'),
                'from_title'   => $this->input->post('from'),
                'date'         => $this->customlib->dateFormatToYYYYMMDD($date),
                'type'         => 'dispatch',
            );

            $dispatch_id = $this->dispatch_model->insert('dispatch_receive', $dispatch);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $dispatch_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/dispatch_receive/" . $img_name);
                $this->dispatch_model->image_add('dispatch', $dispatch_id, $img_name);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success"> ' . $this->lang->line('dispatch_added_successfully') . '</div>');
            redirect('admin/dispatch');
        }
    }

    public function getdispatchdatatable()
    {
        $dt_response = $this->dispatch_model->getAlldispatchRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
              
                $row = array();
                //====================================
                
                $action ='';
                $action1 ='';
                $action2 ='';
                $action3 ='';
                
                  $action  = " <a href='#' data-toggle='tooltip' class='btn btn-default btn-xs pull-right'  title='" . $this->lang->line('show') . "'    data-target='#dispatchdetails' data-original-title='" . $this->lang->line('view') . "' onclick='getRecord(" . $value->id . ")'>  <i class='fa fa-reorder'></i> </a>";

                if ($value->image !== "") {
                    $action1  = "<a href=" . base_url() . 'admin/dispatch/download/' . $value->image . " class='btn btn-default btn-xs pull-right'  data-toggle='tooltip' title='' data-original-title=" . $this->lang->line('download') . "><i class='fa fa-download' aria-hidden='true'></i></a>";
                }

                if ($this->rbac->hasPrivilege('postal_dispatch', 'can_edit')) {
                    $action2  = "<a href='#' class='btn btn-default btn-xs pull-right'  data-toggle='tooltip' title=" . $this->lang->line('edit') . " data-target='#editmyModal' onclick='get(" . $value->id . ")' ><i class='fa fa-pencil' aria-hidden='true'></i></a>";
                }

                if ($this->rbac->hasPrivilege('postal_dispatch', 'can_delete')) {

                    $action3  = "<a href='#' onclick='delete_ById(" . $value->id . ")' class='btn btn-default btn-xs pull-right'  data-toggle='tooltip' title=''  data-original-title=" . $this->lang->line('delete') . "><i class='fa fa-trash' aria-hidden='true'></i></a>";

                }
                
              
                
                //==============================
                $row[] = $value->to_title;
                $row[] = $value->reference_no;
                $row[] = $value->from_title;
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[]     = $action3 . $action2 . $action1 . $action ;
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
        $this->form_validation->set_rules('to_title', $this->lang->line('to_title'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('to_title'),
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
                'type'         => 'dispatch',
            );

            $dispatch_id = $this->dispatch_model->insert('dispatch_receive', $dispatch);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $dispatch_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/dispatch_receive/" . $img_name);
                $this->dispatch_model->image_add('dispatch', $dispatch_id, $img_name);
            }
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function editdispatch()
    {
        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('id');
        $this->form_validation->set_rules('to_title', $this->lang->line('to_title'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('documents'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name' => form_error('to_title'),
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
                'type'         => 'dispatch',
            );

            $this->dispatch_model->update_dispatch('dispatch_receive', $id, 'dispatch', $dispatch);
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

    public function download($documents)
    {
        $this->load->helper('download');
        $filepath = "./uploads/front_office/dispatch_receive/" . $documents;
        $data     = file_get_contents($filepath);
        $name     = $documents;
        force_download($name, $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_delete')) {
            access_denied();
        }
        $this->dispatch_model->delete($id);
    }

    public function imagedelete($id)
    {
        $data = $this->dispatch_model->recevie_data($id);
        $this->dispatch_model->image_delete($data);
        echo json_encode(array('msg' => $this->lang->line('delete_message')));
    }

    public function details($id, $type)
    {
        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_view')) {
            access_denied();
        }
        $data['data'] = $this->dispatch_model->dis_rec_data($id, $type);
        $this->load->view('admin/frontoffice/dispatchreceivemodel', $data);
    }

    public function get_dispatch($id)
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
