<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class patientidcard extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->config->load("image_valid");
        $this->load->library('upload');
        $this->load->model(array('Patient_id_card_model'));
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('patient_id_card', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/patientidcard');
        $this->data['idcardlist'] = $this->Patient_id_card_model->idcardlist();
        $this->load->view('layout/header');
        $this->load->view('admin/patientidcard/createpatientidcard', $this->data);
        $this->load->view('layout/footer');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('patient_id_card', 'can_add')) {
            access_denied();
        }

        $data['title'] = $this->lang->line('add_library');

        $this->form_validation->set_rules('hospital_name', $this->lang->line('hospital_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', $this->lang->line('address_phone_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('title', $this->lang->line('patient_id_card_title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('background_image', $this->lang->line('background_image'), 'callback_background_handle_upload');
        $this->form_validation->set_rules('logo_img', $this->lang->line('logo_image'), 'callback_logo_handle_upload');
        $this->form_validation->set_rules('sign_image', $this->lang->line('signature_image'), 'callback_signature_handle_upload');
        if ($this->form_validation->run() == false) {
            $this->data['idcardlist'] = $this->Patient_id_card_model->idcardlist();
            $this->load->view('layout/header');
            $this->load->view('admin/patientidcard/createpatientidcard', $this->data);
            $this->load->view('layout/footer');
        } else {
            $patientname     = 0;
            $guardianname    = 0;
            $mothername      = 0;
            $address         = 0;
            $phone           = 0;
            $dob             = 0;
            $bloodgroup      = 0;
            $patientuniqueid = 0;

            if ($this->input->post('is_active_patient_name') == 1) {
                $patientname = $this->input->post('is_active_patient_name');
            }
            if ($this->input->post('is_active_guardian_name') == 1) {
                $guardianname = $this->input->post('is_active_guardian_name');
            }
            if ($this->input->post('is_active_patient_unique_id') == 1) {
                $patientuniqueid = $this->input->post('is_active_patient_unique_id');
            }
            if ($this->input->post('is_active_address') == 1) {
                $address = $this->input->post('is_active_address');
            }
            if ($this->input->post('is_active_phone') == 1) {
                $phone = $this->input->post('is_active_phone');
            }
            if ($this->input->post('is_active_dob') == 1) {
                $dob = $this->input->post('is_active_dob');
            }
            if ($this->input->post('is_active_blood_group') == 1) {
                $bloodgroup = $this->input->post('is_active_blood_group');
            }

            if ($this->input->post('is_active_barcode') == 1) {
                $barcode = $this->input->post('is_active_barcode');
            }
            $data = array(
                'title'                    => $this->input->post('title'),
                'hospital_name'            => $this->input->post('hospital_name'),
                'hospital_address'         => $this->input->post('address'),
                'header_color'             => $this->input->post('header_color'),
                'enable_patient_name'      => $patientname,
                'enable_guardian_name'     => $guardianname,
                'enable_patient_unique_id' => $patientuniqueid,
                'enable_address'           => $address,
                'enable_phone'             => $phone,
                'enable_dob'               => $dob,
                'enable_blood_group'       => $bloodgroup,
                'enable_barcode'           => $barcode,
                'status'                   => 1,
            );
            $insert_id = $this->Patient_id_card_model->addidcard($data);

            if (isset($_FILES["background_image"]) && !empty($_FILES['background_image']['name'])) {
                $fileInfo = pathinfo($_FILES["background_image"]["name"]);
                $img_name = 'background' . $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["background_image"]["tmp_name"], "uploads/patient_id_card/background/" . $img_name);
                $background = $img_name;
            } else {
                $background = '';
            }

            if (isset($_FILES["logo_img"]) && !empty($_FILES['logo_img']['name'])) {
                $fileInfo = pathinfo($_FILES["logo_img"]["name"]);
                $img_name = 'logo' . $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["logo_img"]["tmp_name"], "uploads/patient_id_card/logo/" . $img_name);
                $logo_img = $img_name;
            } else {
                $logo_img = '';
            }

            if (isset($_FILES["sign_image"]) && !empty($_FILES['sign_image']['name'])) {
                $fileInfo = pathinfo($_FILES["sign_image"]["name"]);
                $img_name = 'signature' . $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["sign_image"]["tmp_name"], "uploads/patient_id_card/signature/" . $img_name);
                $sign_image = $img_name;
            } else {
                $sign_img = '';
            }

            $upload_data = array('id' => $insert_id, 'logo' => $logo_img, 'background' => $background, 'sign_image' => $sign_image);
            $this->Patient_id_card_model->addidcard($upload_data);

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/patientidcard/index');
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('patient_id_card', 'can_edit')) {
            access_denied();
        }

        $data['title']            = $this->lang->line('edit_id_card');
        $data['id']               = $id;
        $editidcard               = $this->Patient_id_card_model->get($id);
        $this->data['editidcard'] = $editidcard;
        $this->form_validation->set_rules('hospital_name', $this->lang->line('hospital_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('address', $this->lang->line('address_phone_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('title', $this->lang->line('id_card_title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('background_image', $this->lang->line('background_image'), 'callback_background_handle_upload');
        $this->form_validation->set_rules('logo_img', $this->lang->line('logo_image'), 'callback_logo_handle_upload');
        $this->form_validation->set_rules('sign_image', $this->lang->line('signature_image'), 'callback_signature_handle_upload');

        if ($this->form_validation->run() == false) {
            $this->data['idcardlist'] = $this->Patient_id_card_model->idcardlist();
            $this->load->view('layout/header');
            $this->load->view('admin/patientidcard/patientidcardedit', $this->data);
            $this->load->view('layout/footer');
        } else {

            $patientname     = 0;
            $guardianname    = 0;
            $patientuniqueid = 0;
            $address         = 0;
            $phone           = 0;
            $dob             = 0;
            $bloodgroup      = 0;
            $barcode         = 0;

            if ($this->input->post('is_active_patient_name') == 1) {
                $patientname = $this->input->post('is_active_patient_name');
            }
            if ($this->input->post('is_active_guardian_name') == 1) {
                $guardianname = $this->input->post('is_active_guardian_name');
            }
            if ($this->input->post('is_active_patient_unique_id') == 1) {
                $patientuniqueid = $this->input->post('is_active_patient_unique_id');
            }
            if ($this->input->post('is_active_address') == 1) {
                $address = $this->input->post('is_active_address');
            }
            if ($this->input->post('is_active_phone') == 1) {
                $phone = $this->input->post('is_active_phone');
            }
            if ($this->input->post('is_active_dob') == 1) {
                $dob = $this->input->post('is_active_dob');
            }
            if ($this->input->post('is_active_blood_group') == 1) {
                $bloodgroup = $this->input->post('is_active_blood_group');
            }
            if ($this->input->post('is_active_barcode') == 1) {
                $barcode = $this->input->post('is_active_barcode');
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
                move_uploaded_file($_FILES["background_image"]["tmp_name"], "uploads/patient_id_card/background/" . $img_name);
                $data['background'] = $img_name;
            } 
            if (isset($_FILES["logo_img"]) && !empty($_FILES['logo_img']['name'])) {
                $fileInfo = pathinfo($_FILES["logo_img"]["name"]);
                $img_name = 'logo' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["logo_img"]["tmp_name"], "uploads/patient_id_card/logo/" . $img_name);
                $data['logo'] = $img_name;
            } 
            if (isset($_FILES["sign_image"]) && !empty($_FILES['sign_image']['name'])) {
                $fileInfo = pathinfo($_FILES["sign_image"]["name"]);
                $img_name = 'signature' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["sign_image"]["tmp_name"], "uploads/patient_id_card/signature/" . $img_name);
                $data['sign_image'] = $img_name;
            } 
                $data['id']                       = $this->input->post('id');
                $data['title']                    = $this->input->post('title');
                $data['hospital_name']            = $this->input->post('hospital_name');
                $data['hospital_address']         = $this->input->post('address');
                $data['header_color']             = $this->input->post('header_color');
                $data['enable_patient_name']      = $patientname;
                $data['enable_guardian_name']     = $guardianname;
                $data['enable_patient_unique_id'] = $patientuniqueid;
                $data['enable_address']           = $address;
                $data['enable_phone']             = $phone;
                $data['enable_dob']               = $dob;
                $data['enable_blood_group']       = $bloodgroup;
                $data['enable_barcode']           = $barcode;
                $data['status']                   = 1;
            

           $this->Patient_id_card_model->addidcard($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/patientidcard');
        }
    }

    public function delete($id)
    {
        $this->Patient_id_card_model->remove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/patientidcard/index');
    }

    public function view()
    {
        $id                 = $this->input->post('certificateid');
        $data['idcard']     = $this->Patient_id_card_model->idcardbyid($id);
        $data['scan_type']  = $this->sch_setting_detail->scan_code_type;
        $this->load->view('admin/patientidcard/patientidcardpreview', $data);
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
