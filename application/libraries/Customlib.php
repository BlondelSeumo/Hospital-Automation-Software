<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Customlib
{

    public $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('session');
        $this->CI->load->library('user_agent');
        $this->CI->load->model('Notification_model', '', true);
        $this->CI->load->model('Setting_model', '', true);
        $this->CI->load->model('Notificationsetting_model', '', true);
        $this->CI->load->model('Language_model', '', true);
    }

    public function getBaseUrl()
    {
        $admin    = $this->CI->session->userdata('hospitaladmin');

        $base_url = $admin['db_array']['base_url'];
        if ($base_url == "") {
            $base_url = base_url();
        }
        return $base_url;
    }

    public function getSessionPrefixByType($type = "")
    {
        $prefix_array = $this->CI->session->userdata('hospitaladmin')['prefix'];
        return $prefix_array[$type];
    }

    public function getPrefixnameByType($type = "")
    {
        $prefix_name = array(
            'ipd_no' => $this->CI->lang->line('ipd_no'),
            'opd_no' => $this->CI->lang->line('opd_no'),
            'ipd_prescription' => $this->CI->lang->line('ipd_prescription'),
            'opd_prescription' => $this->CI->lang->line('opd_prescription'),
            'appointment' => $this->CI->lang->line('appointment'),
            'pharmacy_billing' => $this->CI->lang->line('pharmacy_bill'),
            'operation_theater_reference_no' => $this->CI->lang->line('operation_reference_no'),
            'blood_bank_billing' => $this->CI->lang->line('blood_bank_bill'),
            'ambulance_call_billing' => $this->CI->lang->line('ambulance_call_bill'),
            'radiology_billing' => $this->CI->lang->line('radiology_bill'),
            'pathology_billing' => $this->CI->lang->line('pathology_bill'),
            'checkup_id' => $this->CI->lang->line('opd_checkup_id'),
            'purchase_no' => $this->CI->lang->line('pharmacy_purchase_no'),
            'transaction_id' => $this->CI->lang->line('transaction_id'),
            'birth_record_reference_no' => $this->CI->lang->line('birth_record_reference_no'),
            'death_record_reference_no' => $this->CI->lang->line('death_record_reference_no')
        );
        return $prefix_name[$type];
    }

    public function getPatientSessionPrefixByType($type = '')
    {
        $prefix_array = $this->CI->session->userdata('patient')['prefix'];
        return $prefix_array[$type];
    }

    public function getPatienttype()
    {
        $paitent_type               = array();
        $paitent_type['inpatient']  = 'Inpatient';
        $paitent_type['outpatient'] = 'Outpatient';
        return $paitent_type;
    }

    public function timeFormat()
    {
        $time_format            = array();
        $time_format['24-hour'] = '24 Hour';
        $time_format['12-hour'] = '12 Hour';
        return $time_format;
    }

    public function getCSRF()
    {
        $csrf_input = "<input type='hidden' ";
        $csrf_input .= "name='" . $this->CI->security->get_csrf_token_name() . "'";
        $csrf_input .= " value='" . $this->CI->security->get_csrf_hash() . "'/>";
        return $csrf_input;
    }

    public function contentAvailabelFor()
    {
        $content_for              = array();
        $role_array               = $this->getStaffRole();
        $role                     = json_decode($role_array);
        $content_for[$role->name] = "All " . $role->name;
        $content_for['student']   = 'Student';
        return $content_for;
    }

    public function getFolderPath()
    {
        $admin       = $this->CI->session->userdata('hospitaladmin');

        $folder_path = $admin['db_array']['folder_path'];
        if ($folder_path == "") {
            $folder_path = null;
        }
        return $folder_path;
    }

    public function getStaffFullName($firstname, $lastname, $employee_id)
    {
        $name = "";
        $name = ($lastname == "") ? $firstname : $firstname . " " . $lastname;
        return $name . " (" . $employee_id . ")";
    }
    public function getCalltype()
    {
        $call_type             = array();
        $call_type['Incoming'] = $this->CI->lang->line('incoming');
        $call_type['Outgoing'] = $this->CI->lang->line('outgoing');
        return $call_type;
    }

    public function getGender()
    {
        $gender           = array();
        $gender['Male']   = $this->CI->lang->line('male');
        $gender['Female'] = $this->CI->lang->line('female');
        return $gender;
    }

    public function getGender_Patient()
    {
        $gender           = array();
        $gender['Male']   = $this->CI->lang->line('male');
        $gender['Female'] = $this->CI->lang->line('female');
        return $gender;
    }

    public function getStatus()
    {
        $status             = array();
        $status['disabled'] = $this->CI->lang->line('disabled');
        $status['enabled']  = $this->CI->lang->line('enabled');
        return $status;
    }

    public function getdischargestatus()
    {
        $status             = array();
        $status['yes']      = $this->CI->lang->line('yes');
        $status['no']       = $this->CI->lang->line('no');
        return $status;
    }

    public function getDateFormat()
    {
        $dateFormat          = array();
        $dateFormat['d-m-Y'] = 'dd-mm-yyyy';
        $dateFormat['d-M-Y'] = 'dd-mmm-yyyy';
        $dateFormat['d/m/Y'] = 'dd/mm/yyyy';
        $dateFormat['d.m.Y'] = 'dd.mm.yyyy';
        $dateFormat['m-d-Y'] = 'mm-dd-yyyy';
        $dateFormat['m/d/Y'] = 'mm/dd/yyyy';
        $dateFormat['m.d.Y'] = 'mm.dd.yyyy';
        return $dateFormat;
    }

    public function getCurrency()
    {
        $currency        = array();
        $currency['AED'] = 'AED';
        $currency['AFN'] = 'AFN';
        $currency['ALL'] = 'ALL';
        $currency['AMD'] = 'AMD';
        $currency['ANG'] = 'ANG';
        $currency['AOA'] = 'AOA';
        $currency['ARS'] = 'ARS';
        $currency['AUD'] = 'AUD';
        $currency['AWG'] = 'AWG';
        $currency['AZN'] = 'AZN';
        $currency['BAM'] = 'BAM';
        $currency['BBD'] = 'BAM';
        $currency['BDT'] = 'BDT';
        $currency['BGN'] = 'BGN';
        $currency['BHD'] = 'BHD';
        $currency['BIF'] = 'BIF';
        $currency['BMD'] = 'BMD';
        $currency['BND'] = 'BND';
        $currency['BOB'] = 'BOB';
        $currency['BOV'] = 'BOV';
        $currency['BRL'] = 'BRL';
        $currency['BSD'] = 'BSD';
        $currency['BTN'] = 'BTN';
        $currency['BWP'] = 'BWP';
        $currency['BYN'] = 'BYN';
        $currency['BYR'] = 'BYR';
        $currency['BZD'] = 'BZD';
        $currency['CAD'] = 'CAD';
        $currency['CDF'] = 'CDF';
        $currency['CHE'] = 'CHE';
        $currency['CHF'] = 'CHF';
        $currency['CHW'] = 'CHW';
        $currency['CLF'] = 'CLF';
        $currency['CLP'] = 'CLP';
        $currency['CNY'] = 'CNY';
        $currency['COP'] = 'COP';
        $currency['COU'] = 'COU';
        $currency['CRC'] = 'CRC';
        $currency['CUC'] = 'CUC';
        $currency['CUP'] = 'CUP';
        $currency['CVE'] = 'CVE';
        $currency['CZK'] = 'CZK';
        $currency['DJF'] = 'DJF';
        $currency['DKK'] = 'DKK';
        $currency['DOP'] = 'DOP';
        $currency['DZD'] = 'DZD';
        $currency['EGP'] = 'EGP';
        $currency['ERN'] = 'ERN';
        $currency['ETB'] = 'ETB';
        $currency['EUR'] = 'EUR';
        $currency['FJD'] = 'FJD';
        $currency['FKP'] = 'FKP';
        $currency['GBP'] = 'GBP';
        $currency['GEL'] = 'GEL';
        $currency['GHS'] = 'GHS';
        $currency['GIP'] = 'GIP';
        $currency['GMD'] = 'GMD';
        $currency['GNF'] = 'GNF';
        $currency['GTQ'] = 'GTQ';
        $currency['GYD'] = 'GYD';
        $currency['HKD'] = 'HKD';
        $currency['HNL'] = 'HNL';
        $currency['HRK'] = 'HRK';
        $currency['HTG'] = 'HTG';
        $currency['HUF'] = 'HUF';
        $currency['IDR'] = 'IDR';
        $currency['ILS'] = 'ILS';
        $currency['INR'] = 'INR';
        $currency['IQD'] = 'IQD';
        $currency['IRR'] = 'IRR';
        $currency['ISK'] = 'ISK';
        $currency['JMD'] = 'JMD';
        $currency['JOD'] = 'JOD';
        $currency['JPY'] = 'JPY';
        $currency['KES'] = 'KES';
        $currency['KGS'] = 'KGS';
        $currency['KHR'] = 'KHR';
        $currency['KMF'] = 'KMF';
        $currency['KPW'] = 'KPW';
        $currency['KRW'] = 'KRW';
        $currency['KWD'] = 'KWD';
        $currency['KYD'] = 'KYD';
        $currency['KZT'] = 'KZT';
        $currency['LAK'] = 'LAK';
        $currency['LBP'] = 'LBP';
        $currency['LKR'] = 'LKR';
        $currency['LRD'] = 'LRD';
        $currency['LSL'] = 'LSL';
        $currency['LYD'] = 'LYD';
        $currency['MAD'] = 'MAD';
        $currency['MDL'] = 'MDL';
        $currency['MGA'] = 'MGA';
        $currency['MKD'] = 'MKD';
        $currency['MMK'] = 'MMK';
        $currency['MNT'] = 'MNT';
        $currency['MOP'] = 'MOP';
        $currency['MRO'] = 'MRO';
        $currency['MUR'] = 'MUR';
        $currency['MVR'] = 'MVR';
        $currency['MWK'] = 'MWK';
        $currency['MXN'] = 'MXN';
        $currency['MXV'] = 'MXV';
        $currency['MYR'] = 'MYR';
        $currency['MZN'] = 'MZN';
        $currency['NAD'] = 'NAD';
        $currency['NGN'] = 'NGN';
        $currency['NIO'] = 'NIO';
        $currency['NOK'] = 'NOK';
        $currency['NPR'] = 'NPR';
        $currency['NZD'] = 'NZD';
        $currency['OMR'] = 'OMR';
        $currency['PAB'] = 'PAB';
        $currency['PEN'] = 'PEN';
        $currency['PGK'] = 'PGK';
        $currency['PHP'] = 'PHP';
        $currency['PKR'] = 'PKR';
        $currency['PLN'] = 'PLN';
        $currency['PYG'] = 'PYG';
        $currency['QAR'] = 'QAR';
        $currency['RON'] = 'RON';
        $currency['RSD'] = 'RSD';
        $currency['RUB'] = 'RUB';
        $currency['RWF'] = 'RWF';
        $currency['SAR'] = 'SAR';
        $currency['SBD'] = 'SBD';
        $currency['SCR'] = 'SCR';
        $currency['SDG'] = 'SDG';
        $currency['SEK'] = 'SEK';
        $currency['SGD'] = 'SGD';
        $currency['SHP'] = 'SHP';
        $currency['SLL'] = 'SLL';
        $currency['SOS'] = 'SOS';
        $currency['SRD'] = 'SRD';
        $currency['SSP'] = 'SSP';
        $currency['STD'] = 'STD';
        $currency['SVC'] = 'SVC';
        $currency['SYP'] = 'SYP';
        $currency['SZL'] = 'SZL';
        $currency['THB'] = 'THB';
        $currency['TJS'] = 'TJS';
        $currency['TMT'] = 'TMT';
        $currency['TND'] = 'TND';
        $currency['TOP'] = 'TOP';
        $currency['TRY'] = 'TRY';
        $currency['TTD'] = 'TTD';
        $currency['TWD'] = 'TWD';
        $currency['TZS'] = 'TZS';
        $currency['UAH'] = 'UAH';
        $currency['UGX'] = 'UGX';
        $currency['USD'] = 'USD';
        $currency['USN'] = 'USN';
        $currency['UYI'] = 'UYI';
        $currency['UYU'] = 'UYU';
        $currency['UZS'] = 'UZS';
        $currency['VEF'] = 'VEF';
        $currency['VND'] = 'VND';
        $currency['VUV'] = 'VUV';
        $currency['WST'] = 'WST';
        $currency['XAF'] = 'XAF';
        $currency['XAG'] = 'XAG';
        $currency['XAU'] = 'XAU';
        $currency['XBA'] = 'XBA';
        $currency['XBB'] = 'XBB';
        $currency['XBC'] = 'XBC';
        $currency['XBD'] = 'XBD';
        $currency['XCD'] = 'XCD';
        $currency['XDR'] = 'XDR';
        $currency['XOF'] = 'XOF';
        $currency['XPD'] = 'XPD';
        $currency['XPF'] = 'XPF';
        $currency['XPT'] = 'XPT';
        $currency['XSU'] = 'XSU';
        $currency['XTS'] = 'XTS';
        $currency['XUA'] = 'XUA';
        $currency['XXX'] = 'XXX';
        $currency['YER'] = 'YER';
        $currency['ZAR'] = 'ZAR';
        $currency['ZMW'] = 'ZMW';
        $currency['ZWL'] = 'ZWL';
        return $currency;
    }

    public function getRteStatus()
    {
        $status        = array();
        $status['Yes'] = $this->CI->lang->line('yes');
        $status['No']  = $this->CI->lang->line('no');
        return $status;
    }

    public function getDaysname()
    {
        $status              = array();
        $status['Monday']    = 'Monday';
        $status['Tuesday']   = 'Tuesday';
        $status['Wednesday'] = 'Wednesday';
        $status['Thursday']  = 'Thursday';
        $status['Friday']    = 'Friday';
        $status['Saturday']  = 'Saturday';
        $status['Sunday']    = 'Sunday';
        return $status;
    }

    public function getcontenttype()
    {
        $status                   = array();
        $status['Assignments']    = 'Assignments';
        $status['Study_material'] = 'Study Material';
        $status['Syllabus']       = 'Syllabus';
        $status['Other_download'] = 'Other Download';
        return $status;
    }

    public function getPageContentCategory()
    {
        $category             = array();
        $category['standard'] = 'Standard';
        $category['events']   = 'Events';
        $category['notice']   = 'Notice';
        $category['gallery']  = 'Gallery';
        return $category;
    }

    public function getDatepickerFormat($date_only = true, $time = false)
    {
        $setting_result = $this->CI->setting_model->get();
        $time_format    = $setting_result[0]['time_format'];
        $hi_format = ' h:i A';
        $Hi_format = ' H:i';

        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            if ($date_only && !$time) {
                return $admin['date_format'];
            } elseif ($time_format == "24-hour") {
                return $admin['date_format'] . $Hi_format;
            } elseif ($time_format == "12-hour") {
                return $admin['date_format'] . $hi_format;
            }
        } else if ($this->CI->session->userdata('patient')) {
            $student = $this->CI->session->userdata('patient');
            if ($date_only && !$time) {
                return $student['date_format'];
            } elseif ($time_format == "24-hour") {
                return $student['date_format'] . $Hi_format;
            } elseif ($time_format == "12-hour") {
                return $student['date_format'] . $hi_format;
            }
        }
    }

    public function is_staff()
    {
        if ($this->CI->session->has_userdata('hospitaladmin')) {
            return true;
        }
        return false;
    }


    public function getTimePickerFormat()
    {
        $time_format = $this->getHospitalTimeFormat(); //24 true 12 false
        $hi_format = 'h:i A';
        $Hi_format = 'H:i';

        if ($this->CI->session->has_userdata('hospitaladmin')) {
           
           if ($time_format) {
                return  $Hi_format;
            } elseif (!$time_format) {
                return  $hi_format;
            }
        }
    }


    public function getHospitalDateFormat($date_only = true, $time = false)
    {
        $time_format = $this->getHospitalTimeFormat(); //24 true 12 false
        $hi_format = ' h:i A';
        $Hi_format = ' H:i';

        if ($this->CI->session->has_userdata('hospitaladmin')) {
            $admin = $this->CI->session->userdata('hospitaladmin');
            if ($date_only && !$time) {
                return $admin['date_format'];
            } elseif ($time_format) {
                return $admin['date_format'] . $Hi_format;
            } elseif (!$time_format) {
                return $admin['date_format'] . $hi_format;
            }
        } else if ($this->CI->session->has_userdata('patient')) {
            $patient = $this->CI->session->userdata('patient');
            if ($date_only && !$time) {
                return $patient['date_format'];
            } elseif ($time_format) {
                return $patient['date_format'] . $Hi_format;
            } elseif (!$time_format) {
                return $patient['date_format'] . $hi_format;
            }
        } else {
            $setting_result = $this->CI->setting_model->getSetting();
            if ($date_only && !$time) {
                return $setting_result->date_format;
            } elseif ($time_format) {
                return $setting_result->date_format . $Hi_format;
            } elseif (!$time_format) {
                return $setting_result->date_format . $hi_format;
            }
        }
    }

    public function getHospitalDateFormatFrontCMS($date_only = true, $time = false)
    {
        // to be used by session or sch_setting table
        $setting_result = $this->CI->setting_model->get();
        $time_format    = $setting_result[0]['time_format'];

        $hi_format = ' h:i A';
        $Hi_format = ' H:i';
        if ($date_only && !$time) {
            return $setting_result[0]['date_format'];
        } elseif ($time_format == "24-hour") {
            return $setting_result[0]['date_format'] . $Hi_format;
        } elseif ($time_format == "12-hour") {
            return $setting_result[0]['date_format'] . $hi_format;
        }
    }

    public function getHospitalTime_Format($time)
    {
        // to be used by session or sch_setting table      
        $setting_result = $this->CI->setting_model->get();
        $time_format    = $setting_result[0]['time_format'];

        $hi_format = ' h:i A';
        $Hi_format = ' H:i';

        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            if ($time_format == "24-hour") {
                return date($Hi_format, strtotime($time));
            } elseif ($time_format == "12-hour") {
                return date($hi_format, strtotime($time));
            }
        } else if ($this->CI->session->userdata('patient')) {

            $patient = $this->CI->session->userdata('patient');
            if ($time_format == "24-hour") {
                return date($Hi_format, strtotime($time));
            } elseif ($time_format == "12-hour") {
                return date($hi_format, strtotime($time));
            }
        }
    }

    public function getHospitalTime_FormatFrontCMS($time)
    {
        // to be used by session or sch_setting table

        $setting_result = $this->CI->setting_model->get();
        $time_format    = $setting_result[0]['time_format'];
        $hi_format = ' h:i A';
        $Hi_format = ' H:i';
        if ($time_format == "24-hour") {
            return date($Hi_format, strtotime($time));
        } elseif ($time_format == "12-hour") {
            return date($hi_format, strtotime($time));
        }
    }

    public function dateYYYYMMDDtoStrtotime($date = null)
    {
        if (strtotime($date) == 0 || trim($date) == "" || substr($date, 0, 10) == '0000-00-00') {
            return "";
        }
        $date_formated = date_parse_from_format('Y-m-d', $date);
        $year          = $date_formated['year'];
        $month         = $date_formated['month'];
        $day           = $date_formated['day'];
        $date = $year . "-" . $month . "-" . $day;
        return strtotime($date);
    }

    public function getTimeZone()
    {
        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            return $admin['timezone'];
        } else if ($this->CI->session->userdata('patient')) {
            $student = $this->CI->session->userdata('patient');
            return $student['timezone'];
        }
    }

    public function getHospitalTimeFormat()
    {
        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            return $admin['time_format'];
        } elseif ($this->CI->session->userdata('patient')) {
            $patient = $this->CI->session->userdata('patient');
            return $patient['time_format'];
        } else {
            $setting_result = $this->CI->setting_model->getSetting();
            return $setting_result->time_format;
        }
    }

    public function getHospitalCurrencyFormat()
    {
        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            return $admin['currency_symbol'];
        }
        if ($this->CI->session->userdata('patient')) {
            $patient = $this->CI->session->userdata('patient');
            return $patient['currency_symbol'];
        }
    }

    public function getLoggedInUserData()
    {
        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            return $admin;
        } else if ($this->CI->session->userdata('patient')) {
            $patient = $this->CI->session->userdata('patient');
            return $patient;
        }
    }

    public function getLoggedInUserID()
    {
        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            return $admin['id'];
        } else if ($this->CI->session->userdata('patient')) {
            $patient = $this->CI->session->userdata('patient');
            return $patient['id'];
        }
    }

    public function getCurrentTheme()
    {
        $theme = "default";
        $admin = $this->CI->session->userdata('hospitaladmin');

        if ($admin) {
            if (isset($admin['theme']) && $admin['theme'] != "") {
                $ext   = pathinfo($admin['theme'], PATHINFO_EXTENSION);
                $theme = basename($admin['theme'], "." . $ext);
            }
        } else if ($this->CI->session->userdata('patient')) {
            $patient = $this->CI->session->userdata('patient');
            if (isset($patient['theme']) && $patient['theme'] != "") {
                $ext   = pathinfo($patient['theme'], PATHINFO_EXTENSION);
                $theme = basename($patient['theme'], "." . $ext);
            }
        }
        return $theme;
    }

    public function getRTL()
    {
        $rtl   = "";
        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            if ($admin['is_rtl'] == "no") {
                $rtl = "no";
            } else {
                $rtl = "yes";
            }
        } else if ($this->CI->session->userdata('patient')) {
            $patient = $this->CI->session->userdata('patient');

            if ($patient['is_rtl'] == "no") {
                $rtl = "no";
            } else {
                $rtl = "yes";
            }
        }
        return $rtl;
    }

    public function getPatientSessionUserID()
    {

        $session_Array   = $this->CI->session->userdata('patient');
        $patientID       = $session_Array['patient_id'];
        return $patientID;
    }

    public function getUsersID()
    {
        // users table id of users
        $session_Array = $this->CI->session->userdata('patient');
        $user_id       = $session_Array['id'];
        return $user_id;
    }

    public function getStaffID()
    {
        // users table id of users
        if ($this->CI->session->has_userdata('hospitaladmin')) {
            $session_Array = $this->CI->session->userdata('hospitaladmin');

            $staff_id      = $session_Array['id'];
            return $staff_id;
        }


        return false;
    }

    public function getSessionLanguage()
    {
        $patient_session = $this->CI->session->userdata('hospitaladmin');
        $language        = $patient_session['language'];
        $lang_id         = $language['lang_id'];
        return $lang_id;
    }

    public function checkPaypalDisplay()
    {
        $payment_setting = $this->CI->paymentsetting_model->get();
        return $payment_setting;
    }

    public function getPatientSessionUserName()
    {
        $student_session = $this->CI->session->all_userdata();
        $session_Array   = $this->CI->session->userdata('patient');
        $studentUsername = $session_Array['name'];
        return $studentUsername;
    }

    public function getAdminSessionUserName()
    {
        $student_session = $this->CI->session->userdata('hospitaladmin');
        $username        = $student_session['username'];
        return $username;
    }

    public function getMonthDropdown()
    {
        $array = array();
        for ($m = 1; $m <= 12; $m++) {
            $month         = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
            $array[$month] = $month;
        }
        return $array;
    }

    public function getMonthList()
    {
        $months = array(1 => $this->CI->lang->line('january'), 2 => $this->CI->lang->line('february'), 3 => $this->CI->lang->line('march'), 4 => $this->CI->lang->line('april'), 5 => $this->CI->lang->line('may'), 6 => $this->CI->lang->line('june'), 7 => $this->CI->lang->line('july'), 8 => $this->CI->lang->line('august'), 9 => $this->CI->lang->line('september'), 10 => $this->CI->lang->line('october'), 11 => $this->CI->lang->line('november'), 12 => $this->CI->lang->line('december'));
        return $months;
    }

    public function geLangMonthList()
    {
        $months = array('january' => $this->CI->lang->line('january'), 'february' => $this->CI->lang->line('february'), 'march' => $this->CI->lang->line('march'), 'april' => $this->CI->lang->line('april'), 'may' => $this->CI->lang->line('may'), 'june' => $this->CI->lang->line('june'), 'july' => $this->CI->lang->line('july'), 'august' => $this->CI->lang->line('august'), 'september' => $this->CI->lang->line('september'), 'october' => $this->CI->lang->line('october'), 'november' => $this->CI->lang->line('november'), 'december' => $this->CI->lang->line('december'));
        return $months;
    }

    public function getAppName()
    {
        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            return $admin['sch_name'];
        } else if ($this->CI->session->userdata('patient')) {
            $patient = $this->CI->session->userdata('patient');
            return $patient['sch_name'];
        }
    }

    public function getStaffRole()
    {
        $admin = $this->CI->session->userdata('hospitaladmin');
        $roles = $admin['roles'];
        if ($admin) {
            $role_key = key($roles);
            return json_encode(array('id' => $roles[$role_key], 'name' => $role_key));
        }
    }

    public function getAppVersion()
    {
        //Build: 240830
        $appVersion = "5.0";
        return $appVersion;
    }

    public function datetostrtotime($date)
    {
        $format = $this->getHospitalDateFormat();
        if (!empty($date)) {
            if ($format == 'd-m-Y') {
                list($day, $month, $year) = explode('-', $date);
            }

            if ($format == 'd/m/Y') {
                list($day, $month, $year) = explode('/', $date);
            }

            if ($format == 'd-M-Y') {
                list($day, $month, $year) = explode('-', $date);
            }

            if ($format == 'd.m.Y') {
                list($day, $month, $year) = explode('.', $date);
            }

            if ($format == 'm-d-Y') {
                list($month, $day, $year) = explode('-', $date);
            }

            if ($format == 'm/d/Y') {
                list($month, $day, $year) = explode('/', $date);
            }

            if ($format == 'm.d.Y') {
                list($month, $day, $year) = explode('.', $date);
            }

            $dater = $day . "-" . $month . "-" . $year;
            return strtotime($dater);
        }
    }

    public function dateFront()
    {
        $admin = $this->CI->Setting_model->getSetting();
        return $admin->date_format;
    }

    public function chatDateTimeformat($date)
    {
        $date_formated = date_parse_from_format('d M Y, H:i:s', $date);
        $year          = $date_formated['year'];
        $month         = str_pad($date_formated['month'], 2, "0", STR_PAD_LEFT);
        $day           = str_pad($date_formated['day'], 2, "0", STR_PAD_LEFT);
        $hour          = str_pad($date_formated['hour'], 2, "0", STR_PAD_LEFT);
        $minute        = str_pad($date_formated['minute'], 2, "0", STR_PAD_LEFT);
        $second        = str_pad($date_formated['second'], 2, "0", STR_PAD_LEFT);
        $format_date   = $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute . ":" . $second;
        return $format_date;
    }

    public function dateyyyymmddTodateformatFront($date)
    {
        $format = $this->dateFront();
        if ($format == 'd-m-Y') {
            list($month, $day, $year) = explode('-', $date);
        }

        if ($format == 'd/m/Y') {
            list($month, $day, $year) = explode('-', $date);
        }

        if ($format == 'd-M-Y') {
            list($month, $day, $year) = explode('-', $date);
        }

        if ($format == 'd.m.Y') {
            list($month, $day, $year) = explode('-', $date);
        }

        if ($format == 'm-d-Y') {
            list($month, $day, $year) = explode('-', $date);
        }

        if ($format == 'm/d/Y') {
            list($month, $day, $year) = explode('-', $date);
        }

        if ($format == 'm.d.Y') {
            list($month, $day, $year) = explode('-', $date);
        }

        $date = $year . "-" . $day . "-" . $month;
        return strtotime($date);
    }

    public function timezone_list()
    {
        static $timezones = null;

        if ($timezones === null) {
            $timezones = [];
            $offsets   = [];
            $now       = new DateTime('now', new DateTimeZone('UTC'));

            foreach (DateTimeZone::listIdentifiers() as $timezone) {
                $now->setTimezone(new DateTimeZone($timezone));
                $offsets[]            = $offset            = $now->getOffset();
                $timezones[$timezone] = '(' . $this->format_GMT_offset($offset) . ') ' . $this->format_timezone_name($timezone);
            }

            array_multisort($offsets, $timezones);
        }
        return $timezones;
    }

    public function format_GMT_offset($offset)
    {
        $hours   = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));
        return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    public function format_timezone_name($name)
    {
        $name = str_replace('/', ', ', $name);
        $name = str_replace('_', ' ', $name);
        $name = str_replace('St ', 'St. ', $name);
        return $name;
    }

    public function getMailMethod()
    {
        $mail_method             = array();
        $mail_method['sendmail'] = 'SendMail';
        $mail_method['smtp']     = 'SMTP';
        return $mail_method;
    }

    public function getNotificationModes()
    {
        $notification = array();
        if ($this->CI->module_lib->hasActive('OPD')) {
            $notification['opd_patient_registration'] = $this->CI->lang->line('opd_patient_registration');
        }
        if ($this->CI->module_lib->hasActive('IPD')) {
            $notification['ipd_patient_registration'] = $this->CI->lang->line('ipd_patient_registration');
        }
        if ($this->CI->module_lib->hasActive('IPD')) {
            $notification['patient_discharged'] = $this->CI->lang->line('ipd') . " " . $this->CI->lang->line('patient') . " " . $this->CI->lang->line('discharged');
        }
        if ($this->CI->module_lib->hasActive('OPD')) {
            $notification['opd_patient_revisit'] = $this->CI->lang->line('opd') . " " . $this->CI->lang->line('patient') . " " . $this->CI->lang->line('revisit');
        }
        $notification['login_credential'] = $this->CI->lang->line('login_credential');
        if ($this->CI->module_lib->hasActive('front_office')) {
            $notification['appointment'] = $this->CI->lang->line('appointment') . " " . $this->CI->lang->line('approved');
        }
        if ($this->CI->module_lib->hasActive('live_consultation')) {
            $notification['live_meeting'] = $this->CI->lang->line('live_meeting');
        }
        if ($this->CI->module_lib->hasActive('live_consultation')) {
            $notification['live_consult'] = $this->CI->lang->line('live_consult');
        }
        return $notification;
    }

    public function sendMailSMS($find)
    {
        $notifications = $this->CI->notificationsetting_model->get();
        if (!empty($notifications)) {
            foreach ($notifications as $note_key => $note_value) {
                if ($note_value->type == $find) {
                    return array('mail' => $note_value->is_mail, 'sms' => $note_value->is_sms, 'mobileapp' => $note_value->is_notification, 'template' => $note_value->template, 'template_id' => $note_value->template_id, 'subject' => $note_value->subject);
                }
            }
        }
        return false;
    }

    public function setUserLog($username, $role)
    {
        if ($this->CI->agent->is_browser()) {
            $agent = $this->CI->agent->browser() . ' ' . $this->CI->agent->version();
        } elseif ($this->CI->agent->is_robot()) {
            $agent = $this->CI->agent->robot();
        } elseif ($this->CI->agent->is_mobile()) {
            $agent = $this->CI->agent->mobile();
        } else {
            $agent = 'Unidentified User Agent';
        }

        $data = array(
            'user'       => $username,
            'role'       => $role,
            'ipaddress'  => $this->CI->input->ip_address(),
            'user_agent' => $agent . ", " . $this->CI->agent->platform(),
        );
        $this->CI->userlog_model->add($data);
    }

    public function mediaType()
    {
        $media_type                             = array();
        $media_type['image/jpeg']               = "Image";
        $media_type['video']                    = "Video";
        $media_type['text/plain']               = "Text";
        $media_type['application/zip']          = "Zip";
        $media_type['application/x-rar']        = "Rar";
        $media_type['application/pdf']          = "Pdf";
        $media_type['application/msword']       = "Word";
        $media_type['application/vnd.ms-excel'] = "Excel";
        $media_type['other']                    = "Other";
        return $media_type;
    }

    public function getFormString($str, $start, $end)
    {
        $string  = false;
        $pattern = sprintf(
            '/%s(.+?)%s/ims',
            preg_quote($start, '/'),
            preg_quote($end, '/')
        );

        if (preg_match($pattern, $str, $matches)) {
            list(, $match) = $matches;
            $string        = trim($match);
        }
        return $string;
    }

    public function uniqueFileName($prefix = "", $name = "")
    {
        if (!empty($_FILES)) {
            $newFileName = uniqid($prefix, true) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
            return $newFileName;
        }
        return false;
    }

    public function getUserData()
    {
        $result = $this->getLoggedInUserData();
        $id     = $result["id"];
        $data   = $this->CI->staff_model->get($id);
        return $data;
    }

    public function countincompleteTask($id)
    {
        $result = $this->CI->calendar_model->countincompleteTask($id);
        return $result;
    }

    public function getincompleteTask($id)
    {
        $result = $this->CI->calendar_model->getincompleteTask($id);
        return $result;
    }

    public function getLogoImage()
    {
        $result = $this->CI->setting_model->getLogoImage();
        return $result;
    }

    public function getTitleName()
    {
        $result = $this->CI->setting_model->getTitleName();
        return $result;
    }

    public function getLanguage()
    {
        $result = $this->CI->setting_model->getLanguage();
        return $result;
    }

    public function getMyLanguage()
    {
        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            $language = $this->CI->language_model->get($admin["language"]['lang_id']);
            return $language;
        }

        if ($this->CI->session->userdata('patient')) {
            $patient = $this->CI->session->userdata('patient');
            $language = $this->CI->language_model->get($patient['language']['lang_id']);
            return $language;
        }
    }

    public function getChargeMaster()
    {
        $result      = $this->CI->setting_model->getChargeMaster();
        $arr         = array();

        foreach ($result as $key => $value) {

            if ($key == 3) {
                $arr[$value['id']] = $this->CI->lang->line('operation_theatre');
            } else

            if (($key <= 4) && ($key != 3)) {

                $arr[$value['id']] = $this->CI->lang->line(strtolower($value['charge_type']));
            } else {
                $arr[$value['id']] = $value['charge_type'];
            }
        }
        return $arr;
    }

    public function getLimitChar($string, $str_length = 50)
    {
        $string = strip_tags($string);
        if (strlen($string) > $str_length) {

            // truncate string
            $stringCut = substr($string, 0, $str_length);
            $endPoint  = strrpos($stringCut, ' ');

            //if the string doesn't contain any space then it will cut without word basis.
            $string = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
            $string .= '...';
        }
        return $string;
    }

    public function getappointment($patient_id)
    {
        $result = $this->CI->appointment_model->getappointbypat($patient_id);
        return $result;
    }

    public function getSlotByShift($id, $doctor_id, $global_shift)
    {
        $this->CI->load->model("onlineappointment_model");
        $global_shift      = $this->CI->onlineappointment_model->getGlobalShift($global_shift);
        $globalstarttime   = $global_shift["start_time"];
        $globalendtime     = $global_shift["end_time"];
        $shift             = $this->CI->onlineappointment_model->getShiftById($id);
        $shift_details     = $this->CI->onlineappointment_model->getShiftDetails($doctor_id);
        $starttime         = $shift["start_time"];
        $endtime           = $shift["end_time"];
        $start_time        = strtotime($starttime);
        $end_time          = strtotime($endtime);
        $global_end_time   = strtotime($globalendtime);
        $duration          = $shift_details['consult_duration'];
        $array_of_time     = array();
        $global_start_time = strtotime($globalstarttime);
        $global_end_time   = strtotime($globalendtime);
        $array_of_time     = array();
        $add_mins          = $duration * 60;
        while ($global_start_time < $global_end_time) {
            if ($global_start_time >= $start_time && $global_start_time < $end_time) {
                $array_of_time[] = date("h:i a", $global_start_time);
            }
            $global_start_time += $add_mins;
        }
        return $array_of_time;
    }

    public function getSlotByDoctorShift($doctor_id, $shift)
    {
        $this->CI->load->model("onlineappointment_model");
        $shift             = $this->CI->onlineappointment_model->getShiftById($shift);
        $starttime         = $shift["start_time"];
        $endtime           = $shift["end_time"];
        $shift_details     = $this->CI->onlineappointment_model->getShiftDetails($doctor_id);
        $duration          = $shift_details['consult_duration'];
        $array_of_time     = array();
        $start_time        = strtotime($starttime);
        $end_time          = strtotime($endtime);
        $add_mins          = $duration * 60;
        while ($start_time < $end_time) {
            $array_of_time[] = date("h:i a", $start_time);
            $start_time += $add_mins;
        }
        return $array_of_time;
    }

    public function strtotimeToDateFormat($strtotime = null)
    {
        if ($strtotime == "") {
            return "";
        }
        $format = $this->dateFormat($strtotime);
        return $format;
    }

    public function dateFormat($strtotime = null)
    {
        if ($strtotime == "") {
            return "";
        }
        $dateYmd = date('Y-m-d', $strtotime);
        $date = $this->YYYYMMDDTodateFormat($dateYmd);
        return $date;
    }

    public function dateFormatToYYYYMMDDHis($date, $twentyfour = false)
    {
        if ($date == "") {
            return NULL;
        }

        return date('Y-m-d H:i:s', $this->dateTimeformatTwentyfourhourStrtotime($date, $twentyfour));
    }

    public function YYYYMMDDHisTodateFormat($date, $twentyfour = false)
    {

        if ($date == "" || $date == NULL) {
            return NULL;
        }

        $format        = $this->getHospitalDateFormat();

        if ($twentyfour) {
            $date_formated = date_parse_from_format('Y-m-d H:i:s', $date);
            $time_format  = "";
        } else {
            $date_formated = date_parse_from_format('Y-m-d h:i:s', date('Y-m-d h:i:s', strtotime($date)));
            $time_format  = date('A', strtotime($date));
        }

        $year          = $date_formated['year'];
        $month         = str_pad($date_formated['month'], 2, "0", STR_PAD_LEFT);
        $day           = str_pad($date_formated['day'], 2, "0", STR_PAD_LEFT);
        $hour          = str_pad($date_formated['hour'], 2, "0", STR_PAD_LEFT);
        $minute        = str_pad($date_formated['minute'], 2, "0", STR_PAD_LEFT);
        $second        = str_pad($date_formated['second'], 2, "0", STR_PAD_LEFT);

        $format_date = "";
        if ($format == 'd-m-Y') {
            $format_date = $day . "-" . $month . "-" . $year . " " . $hour . ":" . $minute;
        }

        if ($format == 'd/m/Y') {
            $format_date = $day . "/" . $month . "/" . $year . " " . $hour . ":" . $minute;
        }

        if ($format == 'd-M-Y') {
            $format_date = date('d-M-Y', strtotime($day . "-" . $month . "-" . $year)) . " " . $hour . ":" . $minute;
        }

        if ($format == 'd.m.Y') {
            $format_date = $day . "." . $month . "." . $year . " " . $hour . ":" . $minute;
        }

        if ($format == 'm-d-Y') {
            $format_date = $month . "-" . $day . "-" . $year . " " . $hour . ":" . $minute;
        }

        if ($format == 'm/d/Y') {
            $format_date = $month . "/" . $day . "/" . $year . " " . $hour . ":" . $minute;
        }

        if ($format == 'm.d.Y') {
            $format_date = $month . "." . $day . "." . $year . " " . $hour . ":" . $minute;
        }

        if ($format == 'Y/m/d') {
            $format_date = $year . "/" . $month . "/" . $day . " " . $hour . ":" . $minute;
        }

        return $format_date . " " . $time_format;
    }

    public function dateFormatToYYYYMMDD($date = null)
    {

        if ($date == "") {
            return NULL;
        }
        $format = $this->getHospitalDateFormat();

        $date_formated = date_parse_from_format($format, $date);
        $year          = $date_formated['year'];
        $month         = str_pad($date_formated['month'], 2, "0", STR_PAD_LEFT);
        $day           = str_pad($date_formated['day'], 2, "0", STR_PAD_LEFT);
        $hour          = $date_formated['hour'];
        $minute        = $date_formated['minute'];
        $second        = $date_formated['second'];
        $date          = $year . "-" . $month . "-" . $day;

        return $date;
    }

    public function YYYYMMDDTodateFormat($date = null)
    {
        if (is_null($date) || trim($date) == "" || substr($date, 0, 10) == '0000-00-00') {
            return "";
        }

        $format        = $this->getHospitalDateFormat();
        $date_formated = date_parse_from_format('Y-m-d', $date);
        $year          = $date_formated['year'];
        $month         = str_pad($date_formated['month'], 2, "0", STR_PAD_LEFT);
        $day           = str_pad($date_formated['day'], 2, "0", STR_PAD_LEFT);
        $hour          = str_pad($date_formated['hour'], 2, "0", STR_PAD_LEFT);
        $minute        = str_pad($date_formated['minute'], 2, "0", STR_PAD_LEFT);
        $second        = str_pad($date_formated['second'], 2, "0", STR_PAD_LEFT);

        $format_date = "";
        if ($format == 'd-m-Y') {
            $format_date = $day . "-" . $month . "-" . $year;
        }

        if ($format == 'd/m/Y') {
            $format_date = $day . "/" . $month . "/" . $year;
        }

        if ($format == 'd-M-Y') {
            $format_date = date('d-M-Y', strtotime($day . "-" . $month . "-" . $year));
        }

        if ($format == 'd.m.Y') {
            $format_date = $day . "." . $month . "." . $year;
        }

        if ($format == 'm-d-Y') {
            $format_date = $month . "-" . $day . "-" . $year;
        }

        if ($format == 'm/d/Y') {
            $format_date = $month . "/" . $day . "/" . $year;
        }

        if ($format == 'm.d.Y') {
            $format_date = $month . "." . $day . "." . $year;
        }

        if ($format == 'Y/m/d') {
            $format_date = $year . "/" . $month . "/" . $day;
        }

        return $format_date;
    }


    public function dateTimeformatTwentyfourhourStrtotime($date, $twentyfour = false)
    {
        $format = $this->getHospitalDateFormat();
        if ($twentyfour) {
            $date_formated = date_parse_from_format($format . ' G:i:s', $date); // 18:00:00 or 24:00:00

        } else {
            $date_formated = date_parse_from_format($format . ' g:i A', $date); // 01:50 AM or PM

        }
        $year   = $date_formated['year'];
        $month  = $date_formated['month'];
        $day    = $date_formated['day'];
        $hour   = $date_formated['hour'];
        $minute = $date_formated['minute'];
        $second = $date_formated['second'];
        $date   = $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute . ":" . $second;

        return strtotime($date);
    }

    public function dateyyyymmddToDateTimeformat($date, $format_two_four = true)
    {
        if ($date == "") {
            return "";
        }
        $format = $this->getHospitalDateFormat();

        if ($format_two_four) {
            $date_formated = date_parse_from_format('Y-m-d H:i:s', $date);
        } else {
            $date_formated = date_parse_from_format('Y-m-d h:i:s', date('Y-m-d h:i:s', strtotime($date)));
        }

        $year   = $date_formated['year'];
        $month  = str_pad($date_formated['month'], 2, "0", STR_PAD_LEFT);
        $day    = str_pad($date_formated['day'], 2, "0", STR_PAD_LEFT);
        $hour   = str_pad($date_formated['hour'], 2, "0", STR_PAD_LEFT);
        $minute = str_pad($date_formated['minute'], 2, "0", STR_PAD_LEFT);
        $second = str_pad($date_formated['second'], 2, "0", STR_PAD_LEFT);
        $second = str_pad($date_formated['second'], 2, "0", STR_PAD_LEFT);
        $am_pm  = date('A', strtotime($date));

        $format_date = "";
        if ($format_two_four) {
            if ($format == 'd-m-Y') {
                $format_date = $day . "-" . $month . "-" . $year . " " . $hour . ":" . $minute . ":" . $second;
            }

            if ($format == 'd/m/Y') {
                $format_date = $day . "/" . $month . "/" . $year . " " . $hour . ":" . $minute . ":" . $second;
            }

            if ($format == 'd-M-Y') {
                $format_date = date('d-M-Y', strtotime($day . "-" . $month . "-" . $year)) . " " . $hour . ":" . $minute . ":" . $second;
            }

            if ($format == 'd.m.Y') {
                $format_date = $day . "." . $month . "." . $year . " " . $hour . ":" . $minute . ":" . $second;
            }

            if ($format == 'm-d-Y') {
                $format_date = $month . "-" . $day . "-" . $year . " " . $hour . ":" . $minute . ":" . $second;
            }

            if ($format == 'm/d/Y') {
                $format_date = $month . "/" . $day . "/" . $year . " " . $hour . ":" . $minute . ":" . $second;
            }

            if ($format == 'm.d.Y') {
                $format_date = $month . "." . $day . "." . $year . " " . $hour . ":" . $minute . ":" . $second;
            }

            if ($format == 'Y/m/d') {
                $format_date = $year . "/" . $month . "/" . $day . " " . $hour . ":" . $minute . ":" . $second;
            }
        } else {
            if ($format == 'd-m-Y') {
                $format_date = $day . "-" . $month . "-" . $year . " " . $hour . ":" . $minute . " " . $am_pm;
            }

            if ($format == 'd/m/Y') {
                $format_date = $day . "/" . $month . "/" . $year . " " . $hour . ":" . $minute . " " . $am_pm;
            }

            if ($format == 'd-M-Y') {
                $format_date = date('d-M-Y', strtotime($day . "-" . $month . "-" . $year)) . " " . $hour . ":" . $minute . " " . $am_pm;
            }

            if ($format == 'd.m.Y') {
                $format_date = $day . "." . $month . "." . $year . " " . $hour . ":" . $minute . " " . $am_pm;
            }

            if ($format == 'm-d-Y') {
                $format_date = $month . "-" . $day . "-" . $year . " " . $hour . ":" . $minute . " " . $am_pm;
            }

            if ($format == 'm/d/Y') {
                $format_date = $month . "/" . $day . "/" . $year . " " . $hour . ":" . $minute . " " . $am_pm;
            }

            if ($format == 'm.d.Y') {
                $format_date = $month . "." . $day . "." . $year . " " . $hour . ":" . $minute . " " . $am_pm;
            }

            if ($format == 'Y/m/d') {
                $format_date = $year . "/" . $month . "/" . $day . " " . $hour . ":" . $minute . " " . $am_pm;
            }
        }
        return $format_date;
    }

    public function get_betweendate($search_type)
    {
        if ($search_type == 'today') {

            $today      = strtotime('today 00:00:00');
            $first_date = date('Y-m-d', $today);
            $last_date  = date('Y-m-d', $today);
        } else if ($search_type == 'this_week') {
            $first_date = date("Y-m-d", strtotime("monday"));
            $last_date  = date("Y-m-d", strtotime("next sunday"));
            if (strtotime($first_date) > strtotime(date('Y-m-d'))) {
                $first_date = date("Y-m-d", strtotime("-1 week monday"));
                $last_date  = date("Y-m-d", strtotime("sunday"));
            }
        } else if ($search_type == 'last_week') {

            $last_week_start = strtotime('-2 week monday 00:00:00');
            $last_week_end   = strtotime('-1 week sunday 23:59:59');
            $first_date      = date('Y-m-d', $last_week_start);
            $last_date       = date('Y-m-d', $last_week_end);
        } else if ($search_type == 'this_month') {

            $first_date = date('Y-m-01');
            $last_date  = date('Y-m-t');
        } else if ($search_type == 'last_month') {
            $first_date = date('Y-m-01', strtotime("-1 month"));
            $last_date  = date('Y-m-t', strtotime("-1 month"));
        } else if ($search_type == 'last_6_month') {

            $month      = date("m", strtotime("-5 month"));
            $year      = date("Y", strtotime("-5 month"));

            $first_date = date($year . '-' . $month . '-01');
            $firstday   = date('Y-' . 'm' . '-01');
            $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)));
        } else if ($search_type == 'last_12_month') {

            $first_date = date('Y-m' . '-01', strtotime("-11 month"));
            $firstday   = date('Y-' . 'm' . '-01');
            $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)));
        } else if ($search_type == 'last_3_month') {

            $first_date = date('Y-m' . '-01', strtotime("-2 month"));
            $firstday   = date('Y-' . 'm' . '-01');
            $last_date  = date('Y-' . 'm' . '-' . date('t', strtotime($firstday)));
        } else if ($search_type == 'last_year') {

            $search_year = date('Y', strtotime("-1 year"));
            $first_date = $search_year . '-01-01';
            $last_date  = $search_year . '-12-31';
        } else if ($search_type == 'this_year') {

            $search_year = date('Y');

            $first_date = $search_year . '-01-01';
            $last_date  = $search_year . '-12-31';
        } else if ($search_type == 'all time') {
            $search_year = date('Y');
            $first_date  = '01-01-' . $search_year;
            $last_date   = '31-12-' . $search_year;
        } else if ($search_type == 'period') {

            $first_date = date('Y-m-d', $this->datetostrtotime($_POST['date_from']));
            $last_date  = date('Y-m-d', $this->datetostrtotime($_POST['date_to']));
        } else if ($search_type == 'next_month') {
            $first_date = date('Y-m-01', strtotime("+1 month"));
            $last_date  = date('Y-m-t', strtotime("+1 month"));
        } else if ($search_type == 'next_3_month') {

            $month      = date("m", strtotime("+2 month"));
            $year      = date("Y", strtotime("+2 month"));
            $first_date   = date('Y-' . 'm' . '-01');
            $lastday = date($year . '-' . $month . '-01');
            $last_date  = date($year . '-' . $month  . '-' . date('t', strtotime($lastday)));
        } else if ($search_type == 'next_6_month') {

            $month      = date("m", strtotime("+5 month"));
            $year      = date("Y", strtotime("+5 month"));
            $first_date   = date('Y-' . 'm' . '-01');
            $lastday = date($year . '-' . $month . '-01');
            $last_date  = date($year . '-' . $month  . '-' . date('t', strtotime($lastday)));
        } else if ($search_type == 'next_year') {

            $search_year = date('Y');
            $search_year = $search_year + 1;

            $first_date = $search_year . '-01-01';
            $last_date  = $search_year . '-12-31';
        }

        return $date = array('from_date' => $first_date, 'to_date' => $last_date);
    }

    public function get_searchtype()
    {
        $data = array(
            ''              => $this->CI->lang->line('select'),
            'today'         => $this->CI->lang->line('today'),
            'this_week'     => $this->CI->lang->line('this_week'),
            'last_week'     => $this->CI->lang->line('last_week'),
            'this_month'    => $this->CI->lang->line('this_month'),
            'last_month'    => $this->CI->lang->line('last_month'),
            'last_3_month'  => $this->CI->lang->line('last_3_month'),
            'last_6_month'  => $this->CI->lang->line('last_6_month'),
            'last_12_month' => $this->CI->lang->line('last_12_month'),
            'this_year'     => $this->CI->lang->line('this_year'),
            'last_year'     => $this->CI->lang->line('last_year'),
            'period'        => $this->CI->lang->line('period'),
        );

        return $data;
    }

    public function get_useroletype()
    {
        $data = array(
            'all'         => $this->CI->lang->line('all'),
            'staff'       => $this->CI->lang->line('staff'),
            'patient'     => $this->CI->lang->line('patient')
        );
        return $data;
    }

    public function get_modules()
    {
        $data = array(
            'all'                  => $this->CI->lang->line('all'),
            'opd_patient'       => $this->CI->lang->line('opd'),
            'ipd_patient'       => $this->CI->lang->line('ipd'),
            'pharmacy_bill'     => $this->CI->lang->line('pharmacy_bill'),
            'pathology_test'    => $this->CI->lang->line('pathology_test'),
            'radiology_test'    => $this->CI->lang->line('radiology_test'),
            'blood_issue'       => $this->CI->lang->line('blood_issue'),
            'ambulance_call'    => $this->CI->lang->line('ambulance_call'),
            'income'            => $this->CI->lang->line('income'),
            'expense'           => $this->CI->lang->line('expenses'),
            'payroll_report'    => $this->CI->lang->line('payroll_report'),
        );

        return $data;
    }

    public function dateFormatToStrtotime($date = null)
    {
        if ($date == "") {
            return "";
        }
        $format = $this->getHospitalDateFormat();

        $date_formated = date_parse_from_format($format, $date);
        $year          = $date_formated['year'];
        $month         = str_pad($date_formated['month'], 2, "0", STR_PAD_LEFT);
        $day           = str_pad($date_formated['day'], 2, "0", STR_PAD_LEFT);
        $hour          = $date_formated['hour'];
        $minute        = $date_formated['minute'];
        $second        = $date_formated['second'];
        $date          = $year . "-" . $month . "-" . $day;

        return strtotime($date);
    }

    function getAgeBydob($dob)
    {

        if (!empty($dob)) {
            $birthdate = new DateTime($dob);
            $today   = new DateTime('today');
            $age = "";

            if ($birthdate->diff($today)->y != 0) {
                $age .= $birthdate->diff($today)->y . ' ' . $this->CI->lang->line('year') . ' ';
            }
            if ($birthdate->diff($today)->m != 0) {
                $age .= $birthdate->diff($today)->m . ' ' . $this->CI->lang->line('month') . ' ';
            }
            if ($birthdate->diff($today)->d != 0) {
                $age .= $birthdate->diff($today)->d . ' ' . $this->CI->lang->line('days');
            }
            return $age;
        } else {
            return 0;
        }
    }

    function getPatientAge($year, $month, $day)
    {
        $age = "";

        if ($year != 0) {
            $age .= $year . ' ' . $this->CI->lang->line('year') . ' ';
        }

        if ($month != 0) {
            $age .= $month . ' ' . $this->CI->lang->line('month') . ' ';
        }

        if ($day != 0) {
            $age .= $day . ' ' . $this->CI->lang->line('days');
        }

        return $age;
    }

    function getblood_bank_type($id = null)
    {
        $type = array(
            '1' => 'Blood Group',
            '2' => 'Component'
        );
        if ($id == null) {
            return $type;
        } else {
            return $type[$id];
        }
    }

    function getMedicine_expire_month($date)
    {
        return date('M/Y', strtotime($date));
    }

    public function chargeTypeModule()
    {
        $charge_type_module                      = array();
        $charge_type_module["appointment"]       = $this->CI->lang->line("appointment");
        $charge_type_module["opd"]               = $this->CI->lang->line("opd");
        $charge_type_module["ipd"]               = $this->CI->lang->line("ipd");
        $charge_type_module["pathology"]         = $this->CI->lang->line("pathology");
        $charge_type_module["radiology"]         = $this->CI->lang->line("radiology");
        $charge_type_module["blood_bank"]        = $this->CI->lang->line("blood_bank");
        $charge_type_module["ambulance"]        = $this->CI->lang->line("ambulance");
        return $charge_type_module;
    }

    public function discharge_status($id = null)
    {

        $discharge_status = array(
            '1' => $this->CI->lang->line('death'),
            '2' => $this->CI->lang->line('referral'),
            '3' => $this->CI->lang->line('normal')
        );

        if ($id == null) {
            return $discharge_status;
        } else {
            return $discharge_status[$id];
        }
    }

    public function bag_string($bag, $volume, $unit)
    {
        if (($volume != '') || ($unit != '')) {
            return $bag . " (" . $volume . " " . $unit . ")";
        } else {
            return $bag;
        }
    }

    public function getCertificateVariables()
    {
        return "[patient_name] [patient_id] [dob] [age] [gender] [email] [phone] [address] [opd_ipd_no] [guardian_name] [opd_checkup_id] [consultant_doctor]";
    }

    public function patientpanel()
    {
        $admin = $this->CI->Setting_model->getSetting();
        return $admin->patient_panel;
    }

    public function checkDischargePatient($discharge)
    {
        if ($discharge == 'yes') {
            return false;
        } else {
            return true;
        }
    }

    public function notification_icon($notification_type)
    {

        $notification_icon_list = array(
            'appointment' => 'fa fa-calendar-check-o',
            'opd' => 'fas fa-stethoscope',
            'ipd' => 'fas fa-procedures',
            'pharmacy' => 'fas fa-mortar-pestle',
            'pathology' => 'fas fa-flask',
            'radiology' => 'fas fa-microscope',
            'blood_bank' => 'fas fa-tint',
            'live_consultation' => 'fa fa-video-camera ftlayer',
            'referral' => 'fas fa-users',
            'certificate' => 'fa fa-newspaper-o ftlayer',
            'ambulance' => 'fas fa-ambulance',
            'birth_death_record' => 'fa fa-birthday-cake',
            'human_resource' => 'fas fa-sitemap'
        );

        if ($notification_type != '') {
            return $notification_icon_list[$notification_type];
        } else {
            return '';
        }
    }

    public function isAppointmentBooked($appointment_id)
    {
        $data           = array();
        $this->CI->load->model('onlineappointment_model');
        $appointment_details   = $this->CI->onlineappointment_model->checkAppointmentExists($appointment_id);
        $current_time = date('Y-m-d H:i');

        if (!empty($appointment_details)) {
            if (strtotime($appointment_details->rejected_time) >= strtotime($current_time . ":00")) {
                $appointments   = $this->CI->onlineappointment_model->checkAppointmentsSlotBooked($appointment_details->doctor, $appointment_details->doctor_shift_time_id, $appointment_details->date, $current_time, $appointment_id);
                if (!empty($appointments)) {
                    return json_encode(['status'=>0,'msg'=>$this->CI->lang->line('appointment_slot_seems_booked_by_someone_else_please_select_any_other_appointment_slot')]);
                } else {
                    return json_encode(['status'=>1,'msg'=>'success.']);
                }
            }else{
                return json_encode(['status'=>0,'msg'=>
				$this->CI->lang->line('appointment_payment_session_has_been_expired_please_start_again_your_appointment_selection_and_payment')]);
            }
        } else {
            return json_encode(['status'=>0,'msg'=>$this->CI->lang->line('appointment_payment_session_has_been_expired_please_start_again_your_appointment_selection_and_payment')]);
        }
    }

    public function get_payment_bill($payment, $bill)
    {
        if (empty($payment)) {
            $payment = 0;
        }
        if (empty($bill)) {
            $bill = 0;
        }
        return $this->getHospitalCurrencyFormat() . $payment . "/" . $this->getHospitalCurrencyFormat() . $bill;
    }


    public function generatebarcode($admission_no, $default_return_code = 'barcode')
    {
        $data = [];
        $code = $admission_no;
        //load library
        $this->CI->load->library('zend');
        //load in folder Zend
        $this->CI->zend->load('Zend/Barcode');
        //generate barcode
        $imageResource = Zend_Barcode::factory('code128', 'image', array('text' => $code, 'barHeight' => 20), array())->draw();
        imagepng($imageResource, 'uploads/patient_id_card/barcodes/' . $code . '.png');
        $barcode = 'uploads/patient_id_card/barcodes/' . $code . '.png';

        //=============qrcode=================
        $this->CI->load->library('QR_Code');
        $qrcode =   $this->CI->qr_code->generate('uploads/patient_id_card/qrcode/', $code);

        if ($default_return_code == "barcode") {
            return $barcode;
        } elseif ($default_return_code == "qrcode") {
            return 'uploads/patient_id_card/qrcode/' . $code . '.png';
        }
    }

    public function generatestaffbarcode($employee_id, $default_return_code = 'barcode')
    {
        $data = [];
        $code = $employee_id;
        //load library
        $this->CI->load->library('zend');
        //load in folder Zend
        $this->CI->zend->load('Zend/Barcode');
        //generate barcode
        $imageResource = Zend_Barcode::factory('code128', 'image', array('text' => $code, 'barHeight' => 20), array())->draw();
        imagepng($imageResource, 'uploads/staff_id_card/barcodes/' . $code . '.png');
        $barcode = 'uploads/staff_id_card/barcodes/' . $code . '.png';
        //=============qrcode=================
        $this->CI->load->library('QR_Code');
        $qrcode =   $this->CI->qr_code->generate('uploads/staff_id_card/qrcode/', $code);

        if ($default_return_code == "barcode") {
            return $barcode;
        } elseif ($default_return_code == "qrcode") {
            return 'uploads/staff_id_card/qrcode/' . $code . '.png';
        }
    }


    public function bulkmailnotificationtype()
    {
        $notificationtype = array();
        $notificationtype['1'] = $this->CI->lang->line('login_credential');
        $notificationtype['2'] = $this->CI->lang->line('forgot_password');
        $notificationtype['3'] = $this->CI->lang->line('both');
        return $notificationtype;
    }
}
