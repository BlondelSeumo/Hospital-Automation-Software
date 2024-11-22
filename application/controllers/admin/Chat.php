<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Chat extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function index()
    {
        if (!$this->module_lib->hasActive('chat')) {
            access_denied();
        }
        
        if (!$this->rbac->hasPrivilege('chat', 'can_view')) {
            access_denied();
        }
        
        $data = array();
        $this->session->set_userdata('top_menu', 'Communicate');
        $this->session->set_userdata('sub_menu', 'Communicate/chat');
        $this->load->view('layout/header');
        $this->load->view('admin/chat/chat', $data);
        $this->load->view('layout/footer');
    }

    public function dashbord()
    {
        $data['start'] = "0";
        $this->load->view('layout/header');
        $this->load->view('admin/chat/dashbord', $data);
        $this->load->view('layout/footer');
    }    

    public function chatdemo()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'audit/index');
        $data['title']      = $this->lang->line('audit_trail_report');
        $data['title_list'] = $this->lang->line('audit_trail_list');
        $listaudit          = $this->audit_model->get();
        $data['resultlist'] = $listaudit;
        $this->load->view('layout/header');
        $this->load->view('admin/chat/chatdemo', $data);
        $this->load->view('layout/footer');
    }

    public function searchuser()
    {
        $keyword      = $this->input->post('keyword');
        $staff_id     = $this->customlib->getStaffID();
        $chat_user    = $this->chatuser_model->getMyID($staff_id, 'staff');
        $chat_user_id = 0;
        if (!empty($chat_user)) {
            $chat_user_id = $chat_user->id;
        }
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['chat_user']   = $this->chatuser_model->searchForUser($keyword, $chat_user_id, $staff_id, 'staff');
        $userlist            = $this->load->view('admin/chat/_partialSearchUser', $data, true);
        $array               = array('status' => '1', 'error' => '', 'page' => $userlist);

        echo json_encode($array);
    }

    public function myuser()
    {
        $data                = array();
        $staff_id            = $this->customlib->getStaffID();
        $chat_user           = $this->chatuser_model->getMyID($staff_id, 'staff');
        $data['chat_user']   = array();
        $data['userList']    = array();
        $data['sch_setting'] = $this->sch_setting_detail;
        if (!empty($chat_user)) {
            $data['chat_user'] = $chat_user;
            $data['userList']  = $this->chatuser_model->myUser($staff_id, $chat_user->id);
        }
        $userlist = $this->load->view('admin/chat/_partialmyuser', $data, true);
        $array    = array('status' => '1', 'error' => '', 'page' => $userlist);
        echo json_encode($array);
    }

    public function getChatRecord()
    {
        $chat_user          = $this->chatuser_model->getMyID($this->customlib->getStaffID(), 'staff');
        $data['chat_user']  = $chat_user;
        $chat_connection_id = $this->input->post('chat_connection_id');
        $chat_to_user       = 0;
        $user_last_chat     = $this->chatuser_model->getLastMessages($chat_connection_id);
        $chat_connection    = $this->chatuser_model->getChatConnectionByID($chat_connection_id);
        if (!empty($chat_connection)) {
            $chat_to_user       = $chat_connection->chat_user_one;
            $chat_connection_id = $chat_connection->id;
            if ($chat_connection->chat_user_one == $chat_user->id) {
                $chat_to_user = $chat_connection->chat_user_two;
            }
        }

        $data['chatList'] = $this->chatuser_model->myChatAndUpdate($chat_connection_id, $chat_user->id);
        $userlist         = $this->load->view('admin/chat/_partialChatRecord', $data, true);
        $array            = array('status' => '1', 'error' => '', 'page' => $userlist, 'chat_to_user' => $chat_to_user, 'chat_connection_id' => $chat_connection_id, 'user_last_chat' => $user_last_chat);
        echo json_encode($array);
    }

    public function newMessage()
    {
        $chat_connection_id = $this->input->post('chat_connection_id');
        $chat_to_user       = $this->input->post('chat_to_user');
        $message            = $this->input->post('message');
        $time               = $this->input->post('time');
        $insert_record      = array(
            'chat_user_id'       => $chat_to_user,
            'message'            => trim($message),
            'chat_connection_id' => $chat_connection_id,
            'created_at'         => $this->customlib->dateFormatToYYYYMMDDHis($time, true),
        );
        $last_insert_id = $this->chatuser_model->addMessage($this->security->xss_clean($insert_record));
        $array          = array('status' => '1', 'last_insert_id' => $last_insert_id, 'error' => '', 'message' => $this->lang->line('inserted'));
        echo json_encode($array);
    }

    public function chatUpdate()
    {
        $chat_connection_id   = $this->input->post('chat_connection_id');
        $chat_user_id         = $this->input->post('chat_to_user');
        $last_chat_id         = $this->input->post('last_chat_id');
        $user_last_chat       = $this->chatuser_model->getLastMessages($chat_connection_id);
        $data['chat_user_id'] = $chat_user_id;
        $chat_user            = $this->chatuser_model->getMyID($this->customlib->getStaffID(), 'staff');
        $data['updated_chat'] = $this->chatuser_model->getUpdatedchat($chat_connection_id, $last_chat_id, $chat_user->id);
        $userlist             = $this->load->view('admin/chat/_chatupdate', $data, true);
        $array                = array('status' => '1', 'error' => '', 'page' => $userlist, 'user_last_chat' => $user_last_chat);
        echo json_encode($array);
    }

    public function adduser()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('user_id', $this->lang->line('contact_person'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('user_type', $this->lang->line('user_type'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $errors = array(
                'user_id' => form_error('user_id'),
            );
            $array = array('status' => 0, 'error' => $errors, 'msg' => $this->lang->line('something_went_wrong'));
            echo json_encode($array);
        } else {
            $user_type   = $this->input->post('user_type');
            $user_id     = $this->input->post('user_id');
            $staff_id    = $this->customlib->getStaffID();
            $first_entry = array(
                'user_type' => "staff",
                'staff_id'  => $staff_id,
            );
            $insert_data = array('user_type' => strtolower($user_type), 'create_staff_id' => null);

            if ($user_type == "Patient") {
                $insert_data['patient_id'] = $user_id;
            } elseif ($user_type == "Staff") {
                $insert_data['staff_id'] = $user_id;
            }
            $insert_message = array(
                'message'            => 'you are now connected on chat',
                'chat_user_id'       => 0,
                'is_first'           => 1,
                'is_read'           => 1,
                'chat_connection_id' => 0,
            );
            //===================
            $new_user_record = $this->chatuser_model->addNewUser($first_entry, $insert_data, $staff_id, $insert_message, 'staff');
            $json_record     = json_decode($new_user_record);
            //==================
            $new_user            = $this->chatuser_model->getChatUserDetail($json_record->new_user_id);
            $new_user->{'name'}  = ($new_user->patient_id != "") ? $new_user->patient_name : $new_user->name . " " . $new_user->surname;
            $new_user->{'image'} = ($new_user->image == "") ? "uploads/patient_images/no_image.png" : $new_user->image;

            $chat_user          = $this->chatuser_model->getMyID($this->customlib->getStaffID(), 'staff');
            $data['chat_user']  = $chat_user;
            $chat_connection_id = $json_record->new_user_chat_connection_id;
            $chat_to_user       = 0;
            $user_last_chat     = $this->chatuser_model->getLastMessages($chat_connection_id);
            $chat_connection    = $this->chatuser_model->getChatConnectionByID($chat_connection_id);
            if (!empty($chat_connection)) {
                $chat_to_user       = $chat_connection->chat_user_one;
                $chat_connection_id = $chat_connection->id;
                if ($chat_connection->chat_user_one == $chat_user->id) {
                    $chat_to_user = $chat_connection->chat_user_two;
                }
            }

            $data['chatList'] = $this->chatuser_model->myChatAndUpdate($chat_connection_id, $chat_user->id);
            $chat_records     = $this->load->view('admin/chat/_partialChatRecord', $data, true);
            $array            = array('status' => '1', 'error' => '', 'message' => $this->lang->line('success_message'), 'new_user' => $new_user, 'chat_connection_id' => $json_record->new_user_chat_connection_id, 'chat_records' => $chat_records, 'user_last_chat' => $user_last_chat);
            echo json_encode($array);
        }
    }

    public function mychatnotification()
    {
        $chat_user     = $this->chatuser_model->getMyID($this->customlib->getStaffID(), 'staff');
        $notifications = array();
        if (!empty($chat_user)) {
            $notifications = $this->chatuser_model->getChatNotification($chat_user->id);
        }
        $array = array('status' => '1', 'message' => $this->lang->line('success_message'), 'notifications' => $notifications);
        echo json_encode($array);
    }

    public function mynewuser()
    {
        $users_list            = $this->input->post('users');
        $chat_user             = $this->chatuser_model->getMyID($this->customlib->getStaffID(), 'staff');
        $data['chat_user']     = $chat_user;
        $data['new_user_list'] = array();
        if (!empty($chat_user)) {
            $data['new_user_list'] = $this->chatuser_model->mynewuser($chat_user->id, $users_list);
        }

        $chat_records = $this->load->view('admin/chat/_partialmynewuser', $data, true);
        $array        = array('status' => '1', 'error' => '', 'message' => $this->lang->line('success_message'), 'new_user_list' => $chat_records);
        echo json_encode($array);
    }

}
