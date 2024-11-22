<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(APPPATH.'third_party/PHPMailer/src/Exception.php');
require_once(APPPATH.'third_party/PHPMailer/src/PHPMailer.php');
require_once(APPPATH.'third_party/PHPMailer/src/SMTP.php');

class Mailer
{

    public $mail_config;
    private $sch_setting;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('emailconfig_model');
        $this->CI->mail_config = $this->CI->emailconfig_model->getActiveEmail();
        $this->CI->load->model('setting_model');
        $this->sch_setting = $this->CI->setting_model->get();
    }

    public function send_mail($toemail, $subject, $body, $FILES = array())
    {

        $mail           = new PHPMailer();
        $hospital_name  = $this->sch_setting[0]['name'];
        $hospital_email = $this->sch_setting[0]['email'];
        if ($this->CI->mail_config->email_type == "smtp") {
            $mail->IsSMTP();
            $mail->SMTPAuth   = ($this->CI->mail_config->smtp_auth != "") ? $this->CI->mail_config->smtp_auth : "";
            $mail->SMTPSecure = $this->CI->mail_config->ssl_tls;
            $mail->Host       = $this->CI->mail_config->smtp_server;
            $mail->Port       = $this->CI->mail_config->smtp_port;
            $mail->Username   = $this->CI->mail_config->smtp_username;
            $mail->Password   = $this->CI->mail_config->smtp_password;
            $mail->SetFrom($this->CI->mail_config->smtp_username, $hospital_name);
            $mail->AddReplyTo($this->CI->mail_config->smtp_username, $this->CI->mail_config->smtp_username);
        } else {
            $mail->isSMTP();
            $mail->Host        = 'localhost';
            $mail->SMTPAuth    = false;
            $mail->SMTPAutoTLS = false;
            $mail->Port        = 25;
            $mail->SetFrom($hospital_email, $hospital_name);
            $mail->AddReplyTo($hospital_email, $hospital_name);
        }
        if (!empty($FILES)) {
            if (isset($_FILES['files']) && !empty($_FILES['files'])) {
                $no_files = count($_FILES["files"]['name']);
                for ($i = 0; $i < $no_files; $i++) {
                    if ($_FILES["files"]["error"][$i] > 0) {
                        echo "Error: " . $_FILES["files"]["error"][$i] . "<br>";
                    } else {
                        $file_tmp  = $_FILES["files"]["tmp_name"][$i];
                        $file_name = $_FILES["files"]["name"][$i];
                        $mail->AddAttachment($file_tmp, $file_name);
                    }
                }
            }
        }
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $body;
        $mail->CharSet = 'UTF-8';
        $mail->AddAddress($toemail);
        if ($mail->Send()) {
            return true;
        } else {
            return false;
        }
    }

}
