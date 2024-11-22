<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Language extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('inner_menu', 'language/index');
        $data['title']         = $this->lang->line('language_list');
        $language_result       = $this->language_model->get();
        $selected_lang         = $this->setting_model->get();
        $json_languages        = json_decode($selected_lang[0]['languages']);
        $data['selected_lang'] = $json_languages;
        $data['languagelist']  = $language_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/language/languageList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function view($id)
    {
        $data['title']    = $this->lang->line('language_list');
        $language         = $this->language_model->get($id);
        $data['language'] = $language;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/language/sectionShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function editlanguage()
    {
        $recordid      = $this->input->post('recordid');
        $key_id        = $this->input->post('key_id');
        $languageid    = $this->input->post('langid');
        $pharses_value = $this->input->post('value');
        if ($recordid == 0 && $pharses_value == "") {

        } else if ($recordid > 0) {
            $d = array('id' => $recordid, 'pharses' => $pharses_value, 'lang_id' => $languageid);
            $this->langpharses_model->add($d);
        } else if ($recordid == 0 && $pharses_value != "") {
            $d = array('key_id' => $key_id, 'pharses' => $pharses_value, 'lang_id' => $languageid);
            $this->langpharses_model->add($d);
        }
        $arr = array('status' => 1, 'message' => $this->lang->line('update_message'));
        echo json_encode($arr);
    }

    public function delete($id)
    {
        $selected_lang = $this->customlib->getSessionLanguage();
        $language      = $this->language_model->get($id);
        $name          = $language['language'];
        $data['title'] = $this->lang->line('language_list');

        if ($language['is_deleted'] == "no") {
            $this->session->set_flashdata('msg', '<div class="alert alert-info">' . $this->lang->line('default_language_cannot_be_deleted') . ' </div>');
        } else {
            if ($selected_lang == $id) {
                $this->session->set_flashdata('msg', '<div class="alert alert-info">' . $this->lang->line('you_cannot_delete_your_current_selected_language') . ' </div>');
            } else {
                $this->language_model->remove($id);
                $setting_result = $this->setting_model->get();
                $setting_id     = $setting_result[0]["id"];
                $last_languages = json_decode($setting_result[0]['languages']);
                foreach ($last_languages as $value) {
                    if ($id != $value) {
                        $languages[] = $value;
                    }
                }

                $language_id = json_encode($languages);
                $data        = array('id' => $setting_id, 'languages' => $language_id);
                $this->setting_model->add($data);
                $directory_app_files = APPPATH . '/language/' . $name . '/app_files/';
                $directory_language  = APPPATH . '/language/' . $name . "/";
                if (is_dir($directory_app_files)) {

                    $files = glob($directory_app_files . '*', GLOB_MARK);

                    foreach ($files as $file) {

                        unlink($file);
                    }

                    rmdir($directory_app_files);
                }
                if (is_dir($directory_language)) {

                    $files = glob($directory_language . '*', GLOB_MARK);

                    foreach ($files as $file) {

                        unlink($file);
                    }

                    rmdir($directory_language);
                }
                $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('language_deleted_successfully') . '</div>');
            }
        }
        redirect('admin/language/index');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('languages', 'can_add')) {
            access_denied();
        }
        $data['title'] = 'Add Language';
        $this->form_validation->set_rules(
            'language', $this->lang->line('language'), array(
                'required',
                array('check_exists', array($this->language_model, 'valid_check_exists')),
            )
        );
        $this->form_validation->set_rules("short_code", $this->lang->line('short_code'), "trim|required|xss_clean");
        $this->form_validation->set_rules("country_code", $this->lang->line('country_code'), "trim|required|xss_clean");

        if ($this->form_validation->run() == false) {
            $msg        = array('language' => form_error('language'), 'short_code' => form_error('short_code'), 'country_code' => form_error('country_code'));
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $directory = APPPATH . '/language/' . $this->input->post('language');
            if (!is_dir($directory)) {
                mkdir($directory, 0777);
                $fromDir = APPPATH . '/language/English';
                $this->copydirr($fromDir, $directory, $chmod = 0757, $verbose = false);
            }
            $data = array(
                'language'     => $this->input->post('language'),
                'short_code'   => $this->input->post('short_code'),
                'country_code' => $this->input->post('country_code'),
            );
            $this->language_model->add($data);
            $json_array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function addPharses($id)
    {
        $language                 = $this->language_model->get($id);
        $data['title']            = $this->lang->line('edit_Phrases_for') . ' ' . $language['language'];
        $data['lang1']            = $language['language'];
        $language_pharses         = $this->langpharses_model->get($id);
        $data['language_pharses'] = $language_pharses;
        $data['id']               = $id;
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $ar = $this->input->post('i[]');
            foreach ($ar as $key => $a) {
                $pharsesid     = $this->input->post('pharsesid' . $a);
                $pharses_value = $this->input->post('pharses' . $a);
                $languageid    = $this->input->post('languageid');
                if ($pharsesid == 0 && $pharses_value == "") {

                } else if ($pharsesid > 0) {
                    $d = array('id' => $pharsesid, 'pharses' => $pharses_value, 'lang_id' => $languageid);
                    $this->langpharses_model->add($d);
                } else if ($pharsesid == 0 && $pharses_value != "") {
                    $d = array('key_id' => $a, 'pharses' => $pharses_value, 'lang_id' => $languageid);
                    $this->langpharses_model->add($d);
                }
            }
            redirect('admin/language/addPharses/' . $languageid);
        } else {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/language/addPharse', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    public function edit($id)
    {
        $data['title']   = $this->lang->line('edit_language');
        $data['id']      = $id;
        $section         = $this->language_model->get($id);
        $data['section'] = $section;
        $this->form_validation->set_rules('section', $this->lang->line('language'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/language/sectionEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'      => $id,
                'section' => $this->input->post('section'),
            );
            $this->language_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('update_message') . '</div>');
            redirect('sections/index');
        }
    }

    public function migratelang()
    {
        $data = array();
        $this->load->library('langconvert');
        $language_pharses = $this->langpharses_model->getByLangAfter(4, 691);
        $language_id      = 90; // change language id.
        $convert_from     = 'en'; //change from langauge
        $convert_to       = 'en'; //change to langauge
        $text             = "";
        end($language_pharses);
        $key_end = key($language_pharses);
        foreach ($language_pharses as $key => $value) {
            $string = $value['pharses'];

            if ($key_end != $key) {
                $text .= $value['id'] . " " . $string . "\n";
            } else {
                $text .= $value['id'] . " " . $string;
            }
        }
        $result      = $this->langconvert->yandexTranslate($convert_from, $convert_to, $text);
        $json_result = json_decode($result);
        $a           = explode("<br />", $json_result->text[0]);
        $array       = array();
        foreach ($a as $a_key => $a_value) {
            preg_match_all('/\d+/', $a_value, $matches);
            $text   = preg_replace('/\d+/u', '', $a_value);
            $key_id = $matches[0];
            $data   = array(
                'lang_id' => $language_id,
                'key_id'  => $key_id[0],
                'pharses' => mb_convert_case(ltrim($text), MB_CASE_TITLE, "UTF-8"),
            );
            $array[] = $data;
        }
        print_r($array);
        exit();
        $this->db->insert_batch('lang_pharses', $array);
        echo $this->lang->line('record_inserted_successfully');
        exit();
    }

    public function form_validation_trans()
    {
        $data         = array();
        $final_data   = array();
        $lang_pharses = array();
        $this->load->library('langconvert');
        $language_pharses = array(array('key' => 'create', 'pharses' => 'create'));
        if (file_exists(FCPATH . "application/language/English/form_validation_lang.php")) {

            $file_content = file(FCPATH . "application/language/English/form_validation_lang.php");
            $newdata      = $file_content;

            for ($i = 1; $i < 33; $i++) {

                $exp     = explode("=", $newdata[$i]);
                $key     = $exp[0];
                $pharses = '';
                if (isset($exp[1])) {
                    $pharses = $exp[1];
                }
                $lang_pharses[$key] = $pharses;
            }
        }

        $language_id  = 90; // change language id.
        $convert_from = 'en'; //change from langauge
        $convert_to   = 'hi'; //change to langauge
        $text         = "";
        end($lang_pharses);
        $key_end = key($lang_pharses);
        foreach ($lang_pharses as $key => $value) {
            $field_replace = str_replace('{field}', '0', $value);
            $param_replace = str_replace('{param}', '9', $field_replace);
            $string        = str_replace(';', '', $param_replace);
            $text .= "+" . $string;
        }
        $result      = $this->langconvert->yandexTranslate($convert_from, $convert_to, $text);
        $json_result = json_decode($result);
        $exp_json    = explode("+", $json_result->text[0]);
        $j           = 0;
        foreach ($lang_pharses as $lkey => $lvalue) {
            if (isset($exp_json[$j + 1])) {
				
                $field_revert = str_replace('0', '{field}', $exp_json[$j + 1]);
                $param_revert = str_replace('9', '{param}', $field_revert);

                $final_data[$lkey] = $param_revert;
            } else {
                $final_data[$lkey] = '';
            }

            $j++;
        }
    }

    public function languagetest()
    {
        $data         = array();
        $final_data   = array();
        $lang_pharses = array();
        $this->load->library('langconvert');
        if (file_exists(FCPATH . "application/language/English/app_files/system_lang.php")) {
            $file_content = file(FCPATH . "application/language/English/app_files/system_lang.php");
            $newdata      = $file_content;

            for ($i = 1; $i < 500; $i++) {

                $exp     = explode("=", $newdata[$i]);
                $key     = $exp[0];
                $pharses = '';
                if (isset($exp[1])) {
                    $pharses = $exp[1];
                }
                $lang_pharses[$key] = $pharses;
            }
        }
        $convert_from = 'en'; //change from langauge
        $convert_to   = 'yi'; //change to langauge
        $text         = "";
        end($lang_pharses);
        $key_end = key($lang_pharses);
        foreach ($lang_pharses as $key => $value) {

            $string = str_replace(';', '', $value);

            $text .= "+" . $string;
        }
        $result      = $this->langconvert->yandexTranslate($convert_from, $convert_to, $text);
        $json_result = json_decode($result);
        $exp_json    = explode("+", $json_result->text[0]);

        $j = 0;
        foreach ($lang_pharses as $lkey => $lvalue) {
            if (isset($exp_json[$j + 1])) {
                $final_data[$lkey] = $exp_json[$j + 1];
            } else {
                
            }

            $j++;
        }
        $this->writeTranslateText($language = 'Yiddish', $final_data);
    }

    public function languagetest2()
    {
        $data         = array();
        $final_data   = array();
        $lang_pharses = array();
        $this->load->library('langconvert');
        $language_pharses = array(array('key' => 'create', 'pharses' => 'create'));
        if (file_exists(FCPATH . "application/language/English/app_files/system_lang.php")) {

            $file_content = file(FCPATH . "application/language/English/app_files/system_lang.php");
            $newdata      = $file_content;
            for ($i = 501; $i < 961; $i++) {

                $exp     = explode("=", $newdata[$i]);
                $key     = $exp[0];
                $pharses = '';
                if (isset($exp[1])) {
                    $pharses = $exp[1];
                }
                $lang_pharses[$key] = $pharses;
            }
        }

        $language_id  = 90; // change language id.
        $convert_from = 'en'; //change from langauge
        $convert_to   = 'yi'; //change to langauge
        $text         = "";
        end($lang_pharses);
        $key_end = key($lang_pharses);
        foreach ($lang_pharses as $key => $value) {

            $string = str_replace(';', '', $value);

            $text .= "+" . $string;
        }
        $result      = $this->langconvert->yandexTranslate($convert_from, $convert_to, $text);
        $json_result = json_decode($result);
        $exp_json    = explode("+", $json_result->text[0]);

        $j = 0;
        foreach ($lang_pharses as $lkey => $lvalue) {
            if (isset($exp_json[$j + 1])) {
                $final_data[$lkey] = $exp_json[$j + 1];
            } else {
                
            }

            $j++;
        }
        $this->updateTranslateText($language = 'Yiddish', $final_data);
    }     

    public function copydirr($fromDir, $toDir, $chmod = 0757, $verbose = false)
    {
        $errors   = array();
        $messages = array();
        if (!is_writable($toDir)) {
            $errors[] = 'target ' . $toDir . ' is not writable';
        }

        if (!is_dir($toDir)) {
            $errors[] = 'target ' . $toDir . ' is not a directory';
        }

        if (!is_dir($fromDir)) {
            $errors[] = 'source ' . $fromDir . ' is not a directory';
        }

        if (!empty($errors)) {
            if ($verbose) {
                foreach ($errors as $err) {
                    echo '<strong>Error</strong>: ' . $err . '<br />';
                }
            }

            return false;
        }

        $exceptions = array('.', '..');
        $handle     = opendir($fromDir);
        while (false !== ($item = readdir($handle))) {
            if (!in_array($item, $exceptions)) {
                //* cleanup for trailing slashes in directories destinations
                $from = str_replace('//', '/', $fromDir . '/' . $item);
                $to   = str_replace('//', '/', $toDir . '/' . $item);
                //*/
                if (is_file($from)) {
                    if (@copy($from, $to)) {
                        chmod($to, $chmod);
                        touch($to, filemtime($from)); // to track last modified time
                        $messages[] = 'File copied from ' . $from . ' to ' . $to;
                    } else {
                        $errors[] = 'cannot copy file from ' . $from . ' to ' . $to;
                    }
                }
                if (is_dir($from)) {
                    if (@mkdir($to)) {
                        chmod($to, $chmod);
                        $messages[] = 'Directory created: ' . $to;
                    } else {
                        $errors[] = 'cannot create directory ' . $to;
                    }
                    $this->copydirr($from, $to, $chmod, $verbose);
                }
            }
        }

        closedir($handle);
        return true;
    }

    public function active_language($language_id)
    {
        $setting_result = $this->setting_model->get();
        $id             = $setting_result[0]["id"];
        $data           = array('id' => $id, 'lang_id' => $language_id);
        $this->setting_model->add($data);
        $this->load->helper('lang');
        set_language($language_id);
        redirect('admin/language');
    }

    public function write_lang_file($language, $writedata)
    {
        if (!is_dir(FCPATH . "application/language/" . $language)) {
            mkdir(FCPATH . "application/language/" . $language);
            mkdir(FCPATH . "application/language/" . $language . "/app_files");
            $my_file = FCPATH . "application/language/" . $language . "/app_files/system_lang.php";
            $handle  = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
            $sta     = '$lang[';
            fwrite($handle, "<?php" . "\n");
            $i  = 0;
            $tr = '"';
            foreach ($writedata as $sakey => $savalue) {
                $data = $sta . "'" . $savalue["key"] . "'] =  " . $tr . $savalue["pharses"] . $tr . ";";
                print_r($data . "<br/>");
                fwrite($handle, $data . "\n");
            }

            fwrite($handle, "?>" . "\n");
            $i++;
        }
        echo $i;
    }

    public function create_language_file()
    {
        $language_result = $this->language_model->get();
        foreach ($language_result as $langkey => $langvalue) {
            $lang_id   = $langvalue["id"];
            $language  = $langvalue["language"];
            $key_query = $this->db->select("*")->order_by("key", "asc")->get("lang_keys");
            $result    = $key_query->result_array();
            $lang_arr  = array();
            $i         = 0;
            foreach ($result as $key => $value) {
                $id                      = $value["id"];
                $key                     = $value["key"];
                $pharses_query           = $this->db->select("*")->where("key_id", $id)->where("lang_id", $lang_id)->get("lang_pharses");
                $lang_pharses_data       = $pharses_query->row_array();
                $pharses                 = $lang_pharses_data["pharses"];
                $lang_arr[$i]["key"]     = $key;
                $lang_arr[$i]["pharses"] = $pharses;
                $i++;
            }
            $this->write_lang_file($language, $lang_arr);
        }
    }

    public function select_language($language_id)
    {
        $this->load->model("setting_model");
        $setting_result = $this->setting_model->get();
        $id             = $setting_result[0]["id"];
        $last_languages = json_decode($setting_result[0]['languages']);
        foreach ($last_languages as $value) {
            $languages[] = $value;
        }
        $languages[] = $language_id;
        $language_id = json_encode($languages);
        $data        = array('id' => $id, 'languages' => $language_id);
        $this->setting_model->add($data);
        $this->load->view('admin/language/languageSwitcher');
    }

    public function unselect_language($language_id)
    {
        $this->load->model("setting_model");
        $setting_result = $this->setting_model->get();
        $id             = $setting_result[0]["id"];
        $last_languages = json_decode($setting_result[0]['languages']);

        foreach ($last_languages as $value) {
            if ($language_id != $value) {
                $languages[] = $value;
            }
        }

        $language_id = json_encode($languages);
        $data        = array('id' => $id, 'languages' => $language_id);
        $this->setting_model->add($data);
        $this->load->view('admin/language/languageSwitcher');
    }

    public function user_language($lang_id)
    {
        $session                                              = $this->session->userdata('hospitaladmin');        
        $id                                                   = $session['id'];
        $data['lang_id']                                      = $lang_id;
        $language_result                                      = $this->language_model->set_userlang($id, $data);
        $id                                                   = $session['id'];        
        $lang_data                                            = $this->language_model->get($lang_id);		
        $this->session->userdata['hospitaladmin']['language'] = array('lang_id' => $lang_id, 'language' => $lang_data['language']);         
        $this->session->userdata['hospitaladmin']['is_rtl']     = $lang_data['is_rtl'];      
        
        set_language($lang_id);
    }

    public function rtl()
    {
        $session= $this->session->userdata('hospitaladmin');      
        $id     = $this->input->post('id');
        $status = $this->input->post('status');
        
        if ($id != '') {
            $data['id']     = $id;
            
            if ($status == 'yes') {
                $is_rtl = "yes";
            } else {
                $is_rtl = "no";
            }
            
            $data['is_rtl'] = $status;
            $this->language_model->add($data);         
            
            if($session['language']['lang_id'] == $id){                      
                $this->session->userdata['hospitaladmin']['is_rtl']     = $is_rtl; 
            }          
           
            echo json_encode(array('status' => '1')); 
            
        } else {
           
            echo json_encode(array('status' => '0'));             
        }
    }

    public function update()
    {
        print_r($_SERVER);
    }
    
    public function defoult_language($lang_id)
    {
        $this->db->set('lang_id', $lang_id); 
        $this->db->update('sch_settings');
        
        $lang_data  = $this->language_model->get($lang_id);
        $this->session->userdata['hospitaladmin']['language'] = array('lang_id' => $lang_id, 'language' => $lang_data['language']);          
        $this->session->userdata['hospitaladmin']['is_rtl']     = $lang_data['is_rtl'];        
    }	
	
}
