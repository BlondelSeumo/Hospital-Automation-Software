<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Timeline extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('Customlib');
        $this->load->config('image_valid');
    }

    public function add()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $title = $this->input->post("timeline_title");

        if ($this->form_validation->run() == false) {
            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $visible_check = $this->input->post('visible_check');
            $timeline_date = $this->input->post('timeline_date');
            if (empty($visible_check)) {
                $visible = '';
            } else {
                $visible = $visible_check;
            }
            $timeline = array(
                'title'         => $this->input->post('timeline_title'),
                'description'   => $this->input->post('timeline_desc'),
                'timeline_date' => $this->customlib->dateFormatToYYYYMMDD($timeline_date),
                'status'        => $visible,
                'date'          => date('Y-m-d'));

            $id = $this->timeline_model->add($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = './uploads/patient_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }
                $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                $document = basename($_FILES['timeline_doc']['name']);

                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name);
            } else {
                $document = "";
                $img_name = "";
            }

            $upload_data = array('id' => $id, 'document' => $img_name);
            $this->timeline_model->add($upload_data);
            $msg   = $this->lang->line('timeline_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function add_staff_timeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('image'), 'callback_handle_upload[timeline_doc]');
        $title = $this->input->post("timeline_title");

        if ($this->form_validation->run() == false) {

            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $visible_check = $this->input->post('visible_check');
            $timeline_date = $this->input->post('timeline_date');
			
            if (empty($visible_check)) {
                $visible = '';
            } else {
                $visible = $visible_check;
            }
			
            $timeline = array(
                'title'         => $this->input->post('timeline_title'),
                'timeline_date' => $this->customlib->dateFormatToYYYYMMDD($timeline_date),
                'description'   => $this->input->post('timeline_desc'),
                'status'        => $visible,
                'date'          => date('Y-m-d'),
                'staff_id'      => $this->input->post('staff_id'));
				
            $id = $this->timeline_model->add_staff_timeline($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = './uploads/staff_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }
                $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                $document = basename($_FILES['timeline_doc']['name']);

                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name);
            } else {
                $document = "";
                $img_name = "";
            }

            $upload_data = array('id' => $id, 'document' => $img_name);
            $this->timeline_model->add_staff_timeline($upload_data);
            $msg   = $this->lang->line('timeline_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);

    }

    public function edit_staff_timeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('image'), 'callback_handle_upload[timeline_doc]');

        $title = $this->input->post("timeline_title");
        if ($this->form_validation->run() == false) {
            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $visible_check = $this->input->post('visible_check');
            $staffid       = $this->input->post('staff_id');
            $timelineid    = $this->input->post('timeline_id');
            $timeline_date = $this->input->post('timeline_date');
            $date          = $this->customlib->dateFormatToYYYYMMDD($timeline_date);
            if (empty($visible_check)) {
                $visible = '';
            } else {
                $visible = $visible_check;
            }
            $timeline = array(
                'id'            => $timelineid,
                'title'         => $this->input->post('timeline_title'),
                'timeline_date' => $this->customlib->dateFormatToYYYYMMDD($timeline_date),
                'description'   => $this->input->post('timeline_desc'),
                'status'        => $visible,
                'date'          => date('Y-m-d'),
                'staff_id'      => $staffid);

            $this->timeline_model->add_staff_timeline($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = './uploads/staff_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }
                $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                $document = basename($_FILES['timeline_doc']['name']);

                $img_name = $timelineid . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name);

                $upload_data = array('id' => $timelineid, 'document' => $img_name);
                $this->timeline_model->add_staff_timeline($upload_data);
            }

            $msg   = $this->lang->line('timeline_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function add_patient_timeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('document'), 'callback_handle_upload[timeline_doc]');      

        if ($this->form_validation->run() == false) {

            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $visible_check = $this->input->post('visible_check');
            $timeline_date = $this->input->post('timeline_date');
            $user_id       = $this->customlib->getStaffID();
			
            if (empty($visible_check)) {
                $visible = '';
            } else {
                $visible = $visible_check;
            }
			
            $timeline = array(
                'title'                => $this->input->post('timeline_title'),
                'timeline_date'        => $this->customlib->dateFormatToYYYYMMDDHis($timeline_date),
                'description'          => $this->input->post('timeline_desc'),
                'status'               => $visible,
                'date'                 => date('Y-m-d'),
                'patient_id'           => $this->input->post('patient_id'),
                'generated_users_type' => 'staff',
                'generated_users_id'   => $user_id,
            );

            $id = $this->timeline_model->add_patient_timeline($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = './uploads/patient_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }
                $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                $document = basename($_FILES['timeline_doc']['name']);

                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name);
				$upload_data = array('id' => $id, 'document' => $img_name);
				$this->timeline_model->add_patient_timeline($upload_data);
            }
            
            $msg   = $this->lang->line('timeline_added_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function edit_patient_timeline()
    {
        $this->form_validation->set_rules('timeline_title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('timeline_doc', $this->lang->line('document'), 'callback_handle_upload[timeline_doc]');

        $title = $this->input->post("timeline_title");

        if ($this->form_validation->run() == false) {

            $msg = array(
                'timeline_title' => form_error('timeline_title'),
                'timeline_date'  => form_error('timeline_date'),
                'timeline_doc'   => form_error('timeline_doc'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $visible_check = $this->input->post('visible_check');
            $patientid     = $this->input->post('patient_id');
            $timelineid    = $this->input->post('timeline_id');
            $timeline_date = $this->input->post('timeline_date');
            $date          = $this->customlib->dateFormatToYYYYMMDDHis($timeline_date);
            $user_id       = $this->customlib->getStaffID();
            if (empty($visible_check)) {
                $visible = '';
            } else {

                $visible = $visible_check;
            }
            $timeline = array(
                'id'                   => $timelineid,
                'title'                => $this->input->post('timeline_title'),
                'timeline_date'        => $date,
                'description'          => $this->input->post('timeline_desc'),
                'status'               => $visible,
                'date'                 => date('Y-m-d'),
                'patient_id'           => $patientid,
                'generated_users_type' => 'staff',
                'generated_users_id'   => $user_id,
            );

            $this->timeline_model->add_patient_timeline($timeline);

            if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                $uploaddir = './uploads/patient_timeline/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                    die("Error creating folder $uploaddir");
                }
                $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                $document = basename($_FILES['timeline_doc']['name']);
                $img_name = $timelineid . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $uploaddir . $img_name);

                $upload_data = array('id' => $timelineid, 'document' => $img_name);
                $this->timeline_model->add_patient_timeline($upload_data);
            }

            $msg   = $this->lang->line('timeline_edit_successfully');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }
        echo json_encode($array);
    }

    public function download($timeline_id, $doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/patient_timeline/" . $doc;
        $data     = file_get_contents($filepath);
        $name     = $doc;
        force_download($name, $data);
    }

    public function download_staff_timeline($timeline_id, $doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/staff_timeline/" . $doc;
        $data     = file_get_contents($filepath);
        $name     = $doc;
        force_download($name, $data);
    }

    public function download_patient_timeline($timeline_id, $doc)
    {
        $this->load->helper('download');
        $filepath = "./uploads/patient_timeline/" . $doc;
        $data     = file_get_contents($filepath);
        $name     = $doc;
        force_download($name, $data);
    }

    public function delete_timeline($id)
    {
        if (!empty($id)) {
            $this->timeline_model->delete_timeline($id);
        }
    }

    public function delete_staff_timeline($id)
    {
        if (!empty($id)) {
            $this->timeline_model->delete_staff_timeline($id);
        }
    }

    public function delete_patient_timeline($id)
    {
        if (!empty($id)) {
            $this->timeline_model->delete_patient_timeline($id);
        }
    }

    public function staff_timeline($id = 77)
    {
        $userdata = $this->customlib->getUserData();
        $userid   = $userdata['id'];
        $status   = '';
        if ($userid == $id) {
            $status = 'yes';
        }

        $result         = $this->timeline_model->getStaffTimeline($id, $status);
        $data["result"] = $result;
        $this->load->view("admin/staff_timeline", $data);
    }

    public function patient_timeline($id = 77)
    {
        $userdata = $this->customlib->getUserData();
        $userid   = $userdata['id'];
        $status   = '';
        if ($userid == $id) {
            $status = 'yes';
        }

        $result         = $this->timeline_model->getPatientTimeline($id, $status);
        $data["result"] = $result;
        $this->load->view("admin/patient_timeline", $data);
    }

    public function handle_upload($str, $var)
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {
            $file_type         = $_FILES[$var]['type'];
            $file_size         = $_FILES[$var]["size"];
            $file_name         = $_FILES[$var]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
          
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];          
        
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('error_while_uploading_document'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('extension_error_while_uploading_document'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }          

            return true;
        }
        return true;
    }

}
