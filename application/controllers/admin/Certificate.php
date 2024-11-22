<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Certificate extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->library('datatables');
        $this->load->model('certificate_model');
        $this->config->load('image_valid');
    }

    public function index()
    { 
        if (!$this->rbac->hasPrivilege('certificate', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatecertificate');
        $data['certificateList'] = $this->certificate_model->certificateList();
        $this->load->view('layout/header');
        $this->load->view('admin/certificate/createcertificate', $data);
        $this->load->view('layout/footer');
    } 

    public function getcertificatedatatable()
    {
        $dt_response = $this->certificate_model->getAllcertificateRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {
                $row = array();

                $certificate_action = "<a href='#' onclick='viewRecord(" . $value->id . ")' class='' data-toggle='' data-target=''  title=''>";

                //====================================

                if ($value->background_image != '' && !is_null($value->background_image)) {
                    $image = "<img src=" . base_url() . 'uploads/certificate/' . $value->background_image .img_time(). " width='40'>";
                } else {
                    $image = "<i class='fa fa-picture-o fa-3x'></i>";
                }

                $action = "<a href='#' onclick='viewRecord(" . $value->id . ")' class='btn btn-default btn-xs' data-toggle='tooltip' data-target='#myModalview'  title='" . $this->lang->line('view') . "'><i class='fa fa-reorder'></i></a>";

                if ($this->rbac->hasPrivilege('certificate', 'can_edit')) {
                    $action .= "<a href='#' onclick='getRecord(" . $value->id . "),refreshmodal()' class='btn btn-default btn-xs' data-toggle='tooltip' data-target='#myModaledit'  title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('certificate', 'can_delete')) {
                    $action .= "<a href='#' onclick='Delete_recored(" . $value->id . ")'  class='btn btn-default btn-xs' data-toggle='tooltip' data-target=''  title='" . $this->lang->line('delete') . "'><i class='fa fa-remove'></i></a>";
                }

                $row[] = $certificate_action . $value->certificate_name . "</a>";
                $row[] = $image;
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

    public function create()
    {        
        $this->form_validation->set_rules('certificate_name', $this->lang->line('certificate_template_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('certificate_text', $this->lang->line('body_text'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('background_image', $this->lang->line('background_image'), 'callback_handle_image_upload[background_image]');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'certificate_name' => form_error('certificate_name'),
                'certificate_text' => form_error('certificate_text'),
                'background_image' => form_error('background_image'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {
            $data['title'] = $this->lang->line('add_library');

            if (!empty($_FILES['background_image']['name'])) {
                $config['upload_path']   = 'uploads/certificate/';
                $config['file_name']     = $_FILES['background_image']['name'];
                //Load upload library and initialize configuration
                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('background_image')) {
                    $uploadData = $this->upload->data();
                    $picture    = $uploadData['file_name'];
                } else {
                    $picture = '';
                }
            } else {
                $picture = '';
            }

            if ($this->input->post('is_active_patient_img') == 1) {
                $enableimg = $this->input->post('is_active_patient_img');
                $imgHeight = $this->input->post('image_height');
            } else {
                $enableimg = 0;
                $imgHeight = 0;
            } 
            $data = array(
                'certificate_name'     => $this->input->post('certificate_name'),
                'certificate_text'     => $this->input->post('certificate_text'),
                'left_header'          => $this->input->post('left_header'),
                'center_header'        => $this->input->post('center_header'),
                'right_header'         => $this->input->post('right_header'),
                'left_footer'          => $this->input->post('left_footer'),
                'right_footer'         => $this->input->post('right_footer'),
                'center_footer'        => $this->input->post('center_footer'),
                'created_for'          => 2,
                'status'               => 1,
                'background_image'     => $picture,
                'header_height'        => $this->input->post('header_height'),
                'content_height'       => $this->input->post('content_height'),
                'footer_height'        => $this->input->post('footer_height'),
                'content_width'        => $this->input->post('content_width'),
                'enable_patient_image' => $enableimg,
                'enable_image_height'  => $imgHeight,
            );

            $insert_id = $this->certificate_model->addcertificate($data);
            $array     = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

        }
        echo json_encode($array);
    }

    public function getcertificate()
    {
        $id          = $this->input->post("id");
        $certificate = $this->certificate_model->get($id);        
        echo json_encode($certificate);
    }

    public function edit()
    {
        $this->form_validation->set_rules('certificate_name', $this->lang->line('certificate_template_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('certificate_text', $this->lang->line('body_text'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('background_image', $this->lang->line('background_image'), 'callback_handle_image_upload[background_image]');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'certificate_name' => form_error('certificate_name'),
                'certificate_text' => form_error('certificate_text'),
                'background_image' => form_error('background_image'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {

            if ($this->input->post('is_active_patient_img') == 1) {
                $enableimg = $this->input->post('is_active_patient_img');
                $imgHeight = $this->input->post('image_height');
            } else {
                $enableimg = 0;
                $imgHeight = 0;
            }

            if (!empty($_FILES['background_image']['name'])) {

                $config['upload_path']   = 'uploads/certificate/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['file_name']     = $_FILES['background_image']['name'];

                //Load upload library and initialize configuration
                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('background_image')) {
                    $uploadData = $this->upload->data();
                    $picture    = $uploadData['file_name'];
                    $data       = array(
                        'id'                   => $this->input->post('id'),
                        'certificate_name'     => $this->input->post('certificate_name'),
                        'certificate_text'     => $this->input->post('certificate_text'),
                        'left_header'          => $this->input->post('left_header'),
                        'center_header'        => $this->input->post('center_header'),
                        'right_header'         => $this->input->post('right_header'),
                        'left_footer'          => $this->input->post('left_footer'),
                        'right_footer'         => $this->input->post('right_footer'),
                        'center_footer'        => $this->input->post('center_footer'),
                        'created_for'          => 2,
                        'status'               => 1,
                        'background_image'     => $picture,
                        'header_height'        => $this->input->post('header_height'),
                        'content_height'       => $this->input->post('content_height'),
                        'footer_height'        => $this->input->post('footer_height'),
                        'content_width'        => $this->input->post('content_width'),
                        'enable_patient_image' => $enableimg,
                        'enable_image_height'  => $imgHeight,
                    );
                } else {
                    $picture = '';
                    $data    = array(
                        'id'                   => $this->input->post('id'),
                        'certificate_name'     => $this->input->post('certificate_name'),
                        'certificate_text'     => $this->input->post('certificate_text'),
                        'left_header'          => $this->input->post('left_header'),
                        'center_header'        => $this->input->post('center_header'),
                        'right_header'         => $this->input->post('right_header'),
                        'left_footer'          => $this->input->post('left_footer'),
                        'right_footer'         => $this->input->post('right_footer'),
                        'center_footer'        => $this->input->post('center_footer'),
                        'created_for'          => 2,
                        'status'               => 1,
                        'header_height'        => $this->input->post('header_height'),
                        'content_height'       => $this->input->post('content_height'),
                        'footer_height'        => $this->input->post('footer_height'),
                        'content_width'        => $this->input->post('content_width'),
                        'enable_patient_image' => $enableimg,
                        'enable_image_height'  => $imgHeight,
                    );
                }
            } else {
                $data = array(
                    'id'                   => $this->input->post('id'),
                    'certificate_name'     => $this->input->post('certificate_name'),
                    'certificate_text'     => $this->input->post('certificate_text'),
                    'left_header'          => $this->input->post('left_header'),
                    'center_header'        => $this->input->post('center_header'),
                    'right_header'         => $this->input->post('right_header'),
                    'left_footer'          => $this->input->post('left_footer'),
                    'right_footer'         => $this->input->post('right_footer'),
                    'center_footer'        => $this->input->post('center_footer'),
                    'created_for'          => 2,
                    'status'               => 1,
                    'header_height'        => $this->input->post('header_height'),
                    'content_height'       => $this->input->post('content_height'),
                    'footer_height'        => $this->input->post('footer_height'),
                    'content_width'        => $this->input->post('content_width'),
                    'enable_patient_image' => $enableimg,
                    'enable_image_height'  => $imgHeight,
                );
            }

            $this->certificate_model->addcertificate($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('certificate', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('certificate_list');
        $this->certificate_model->remove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
    }

    public function view()
    {
        $id                  = $this->input->post('certificateid');
        $output              = '';
        $data                = array();
        $data['certificate'] = $this->certificate_model->certifiatebyid($id);
        $preview             = $this->load->view('admin/certificate/preview_certificate', $data, true);
        echo $preview;
    }

    public function handle_image_upload($str, $var)
    {
        $image_validate = $this->config->item('image_validate');
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type         = $_FILES[$var]['type'];
            $file_size         = $_FILES[$var]["size"];
            $file_name         = $_FILES[$var]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_image_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

}