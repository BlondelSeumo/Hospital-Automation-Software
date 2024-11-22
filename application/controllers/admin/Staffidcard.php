<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Staffidcard extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Staffidcard_model'));
        $this->config->load('image_valid');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('staff_id_card', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/staffidcard');
        $data['staffidcardlist'] = $this->Staffidcard_model->staffidcardlist();
        $this->load->view('layout/header');
        $this->load->view('admin/staffidcard/staffidcardview', $data);
        $this->load->view('layout/footer');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('staff_id_card', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('hospital_name', $this->lang->line('hospital_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', $this->lang->line('address_phone_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('title', $this->lang->line('id_card_title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('background_image', $this->lang->line('background_image'), 'callback_background_handle_upload');
        $this->form_validation->set_rules('logo_img', $this->lang->line('logo_image'), 'callback_logo_handle_upload');
        $this->form_validation->set_rules('sign_image', $this->lang->line('signature_image'), 'callback_signature_handle_upload');

        if ($this->form_validation->run() == false) {
            $this->data['staffidcardlist'] = $this->Staffidcard_model->staffidcardlist();
            $this->load->view('layout/header');
            $this->load->view('admin/staffidcard/staffidcardview', $this->data);
            $this->load->view('layout/footer');
        } else {
            $staff_id          = 0;
            $department        = 0;
            $designation       = 0;
            $name              = 0;
            $fathername        = 0;
            $mothername        = 0;
            $date_of_joining   = 0;
            $permanent_address = 0;
            $phone             = 0;
            $dob               = 0;
            $barcode           = 0;
            if ($this->input->post('is_active_staff_id') == 1) {
                $staff_id = $this->input->post('is_active_staff_id');
            }
            if ($this->input->post('is_active_department') == 1) {
                $department = $this->input->post('is_active_department');
            }
            if ($this->input->post('is_active_designation') == 1) {
                $designation = $this->input->post('is_active_designation');
            }
            if ($this->input->post('is_active_staff_name') == 1) {
                $name = $this->input->post('is_active_staff_name');
            }
            if ($this->input->post('is_active_staff_father_name') == 1) {
                $fathername = $this->input->post('is_active_staff_father_name');
            }
            if ($this->input->post('is_active_staff_mother_name') == 1) {
                $mothername = $this->input->post('is_active_staff_mother_name');
            }
            if ($this->input->post('is_active_date_of_joining') == 1) {
                $date_of_joining = $this->input->post('is_active_date_of_joining');
            }
            if ($this->input->post('is_active_staff_permanent_address') == 1) {
                $permanent_address = $this->input->post('is_active_staff_permanent_address');
            }
            if ($this->input->post('is_active_staff_phone') == 1) {
                $phone = $this->input->post('is_active_staff_phone');
            }
            if ($this->input->post('is_active_staff_dob') == 1) {
                $dob = $this->input->post('is_active_staff_dob');
            }
            if ($this->input->post('is_active_staff_barcode') == 1) {
                $barcode = $this->input->post('is_active_staff_barcode');
            }
            $data = array(
                'title'                    => $this->input->post('title'),
                'hospital_name'            => $this->input->post('hospital_name'),
                'hospital_address'         => $this->input->post('address'),
                'header_color'             => $this->input->post('header_color'),
                'enable_staff_id'          => $staff_id,
                'enable_staff_department'  => $department,
                'enable_designation'       => $designation,
                'enable_name'              => $name,
                'enable_fathers_name'      => $fathername,
                'enable_mothers_name'      => $mothername,
                'enable_date_of_joining'   => $date_of_joining,
                'enable_permanent_address' => $permanent_address,
                'enable_staff_dob'         => $dob,
                'enable_staff_phone'       => $phone,
                'enable_staff_barcode'     => $barcode,
                'status'                   => 1,
            );
            $insert_id = $this->Staffidcard_model->addstaffidcard($data);
            if (isset($_FILES["background_image"]) && !empty($_FILES['background_image']['name'])) {
                $fileInfo = pathinfo($_FILES["background_image"]["name"]);
                $img_name = 'background' . $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["background_image"]["tmp_name"], "uploads/staff_id_card/background/" . $img_name);
                $background = $img_name;
            } else {
                $background = '';
            }

            if (isset($_FILES["logo_img"]) && !empty($_FILES['logo_img']['name'])) {
                $fileInfo = pathinfo($_FILES["logo_img"]["name"]);
                $img_name = 'logo' . $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["logo_img"]["tmp_name"], "uploads/staff_id_card/logo/" . $img_name);
                $logo_img = $img_name;
            } else {
                $logo_img = '';
            }

            if (isset($_FILES["sign_image"]) && !empty($_FILES['sign_image']['name'])) {
                $fileInfo = pathinfo($_FILES["sign_image"]["name"]);
                $img_name = 'signature' . $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["sign_image"]["tmp_name"], "uploads/staff_id_card/signature/" . $img_name);
                $sign_image = $img_name;
            } else {
                $sign_img = '';
            }

            $upload_data = array('id' => $insert_id, 'logo' => $logo_img, 'background' => $background, 'sign_image' => $sign_image);
            $this->Staffidcard_model->addstaffidcard($upload_data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/staffidcard/index');
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('staff_id_card', 'can_edit')) {
            access_denied();
        }
        $data['id']                    = $id;
        $editstaffidcard               = $this->Staffidcard_model->get($id);
        $this->data['editstaffidcard'] = $editstaffidcard;
        $this->form_validation->set_rules('hospital_name', $this->lang->line('hospital_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', $this->lang->line('address_phone_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('title', $this->lang->line('id_card_title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('background_image', $this->lang->line('background_image'), 'callback_background_handle_upload');
        $this->form_validation->set_rules('logo_img', $this->lang->line('logo_image'), 'callback_logo_handle_upload');
        $this->form_validation->set_rules('sign_image', $this->lang->line('signature_image'), 'callback_signature_handle_upload');

        if ($this->form_validation->run() == false) {
            $this->data['staffidcardlist'] = $this->Staffidcard_model->staffidcardlist();
            $this->load->view('layout/header');
            $this->load->view('admin/staffidcard/staffidcardedit', $this->data);
            $this->load->view('layout/footer');
        } else {
            $staff_id          = 0;
            $department        = 0;
            $designation       = 0;
            $name              = 0;
            $fathername        = 0;
            $mothername        = 0;
            $date_of_joining   = 0;
            $permanent_address = 0;
            $phone             = 0;
            $dob               = 0;
            $barcode           = 0;
            if ($this->input->post('is_active_staff_id') == 1) {
                $staff_id = $this->input->post('is_active_staff_id');
            }
            if ($this->input->post('is_active_department') == 1) {
                $department = $this->input->post('is_active_department');
            }
            if ($this->input->post('is_active_designation') == 1) {
                $designation = $this->input->post('is_active_designation');
            }
            if ($this->input->post('is_active_staff_name') == 1) {
                $name = $this->input->post('is_active_staff_name');
            }
            if ($this->input->post('is_active_staff_father_name') == 1) {
                $fathername = $this->input->post('is_active_staff_father_name');
            }
            if ($this->input->post('is_active_staff_mother_name') == 1) {
                $mothername = $this->input->post('is_active_staff_mother_name');
            }
            if ($this->input->post('is_active_date_of_joining') == 1) {
                $date_of_joining = $this->input->post('is_active_date_of_joining');
            }
            if ($this->input->post('is_active_staff_permanent_address') == 1) {
                $permanent_address = $this->input->post('is_active_staff_permanent_address');
            }
            if ($this->input->post('is_active_staff_phone') == 1) {
                $phone = $this->input->post('is_active_staff_phone');
            }
            if ($this->input->post('is_active_staff_dob') == 1) {
                $dob = $this->input->post('is_active_staff_dob');
            }
            if ($this->input->post('is_active_staff_barcode') == 1) {
                $barcode = $this->input->post('is_active_staff_barcode');
            }
            
            $remove_background_image    = $this->input->post('remove_background_image');
            $remove_logo                = $this->input->post('remove_logo');
            $remove_sign_image          = $this->input->post('remove_sign_image');

            if ($remove_background_image != '') {
                $data['background'] = '';
            }

            if ($remove_logo != '') {
                $data['logo'] = '';
            }

            if ($remove_sign_image != '') {
                $data['sign_image'] = '';
            }            

            if (isset($_FILES["background_image"]) && !empty($_FILES['background_image']['name'])) {
                $fileInfo = pathinfo($_FILES["background_image"]["name"]);
                $img_name = 'background' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["background_image"]["tmp_name"], "uploads/staff_id_card/background/" . $img_name);
                $data['background'] = $img_name;
            } 
           

            if (isset($_FILES["logo_img"]) && !empty($_FILES['logo_img']['name'])) {
                $fileInfo = pathinfo($_FILES["logo_img"]["name"]);
                $img_name = 'logo' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["logo_img"]["tmp_name"], "uploads/staff_id_card/logo/" . $img_name);
                $data['logo'] = $img_name;
            } 
           

            if (isset($_FILES["sign_image"]) && !empty($_FILES['sign_image']['name'])) {
                $fileInfo = pathinfo($_FILES["sign_image"]["name"]);
                $img_name = 'signature' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["sign_image"]["tmp_name"], "uploads/staff_id_card/signature/" . $img_name);
                $data['sign_image'] = $img_name;
            } 
            

            
                $data['id']                       = $this->input->post('id');
                $data['title']                     = $this->input->post('title');
                $data['hospital_name']             = $this->input->post('hospital_name');
                $data['hospital_address']          = $this->input->post('address');
                $data['header_color']              = $this->input->post('header_color');
                $data['enable_staff_id']           = $staff_id;
                $data['enable_staff_department']   = $department;
                $data['enable_designation']        = $designation;
                $data['enable_name']               = $name;
                $data['enable_fathers_name']       = $fathername;
                $data['enable_mothers_name']       = $mothername;
                $data['enable_date_of_joining']    = $date_of_joining;
                $data['enable_permanent_address']  = $permanent_address;
                $data['enable_staff_dob']          = $dob;
                $data['enable_staff_phone']        = $phone;
                $data['enable_staff_barcode']      = $barcode;
                $data['status']                    = 1;
           
            $this->Staffidcard_model->addstaffidcard($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/staffidcard');
        }
    }

    public function delete($id)
    {
        $data['title'] = 'Certificate List';
        $this->Staffidcard_model->remove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/staffidcard/index');
    }

    public function view()
    {
        $id                 = $this->input->post('certificateid');
        $data['idcard']     = $this->Staffidcard_model->idcardbyid($id);
        $data['scan_type']  = $this->sch_setting_detail->scan_code_type;
        $this->load->view('admin/staffidcard/staffidcardpreview', $data);
    }

    public function background_handle_upload()
    {

        $image_validate = $this->config->item('image_validate');

        if (isset($_FILES["background_image"]) && !empty($_FILES['background_image']['name'])) {

            $file_type         = $_FILES["background_image"]['type'];
            $file_size         = $_FILES["background_image"]["size"];
            $file_name         = $_FILES["background_image"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['background_image']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('background_handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('background_handle_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('background_handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('background_handle_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function logo_handle_upload()
    {

        $image_validate = $this->config->item('image_validate');

        if (isset($_FILES["logo_img"]) && !empty($_FILES['logo_img']['name'])) {

            $file_type         = $_FILES["logo_img"]['type'];
            $file_size         = $_FILES["logo_img"]["size"];
            $file_name         = $_FILES["logo_img"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['logo_img']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('logo_handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('logo_handle_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('logo_handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('logo_handle_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function signature_handle_upload()
    {

        $image_validate = $this->config->item('image_validate');

        if (isset($_FILES["sign_image"]) && !empty($_FILES['sign_image']['name'])) {

            $file_type         = $_FILES["sign_image"]['type'];
            $file_size         = $_FILES["sign_image"]["size"];
            $file_name         = $_FILES["sign_image"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES['sign_image']['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('signature_handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('signature_handle_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('signature_handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('signature_handle_upload', $this->lang->line('file_type_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }

}
