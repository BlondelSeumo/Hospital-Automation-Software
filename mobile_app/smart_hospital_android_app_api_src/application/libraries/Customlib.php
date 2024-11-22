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
        $this->CI->load->model('Setting_model', '', TRUE);
    }

    public function getMonthList($month=0)
    {
        $months = array(
            0  => '',
            1  => 'january',
            2  => 'february',
            3  => 'march',
            4  => 'april',
            5  => 'may',
            6  => 'june',
            7  => 'july',
            8  => 'august',
            9  => 'september',
            10 => 'october',
            11 => 'november',
            12 => 'decmber');
     
        return $months[$month];
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
    

    public function getHospitalName()
    {
        $admin = $this->CI->Setting_model->getSetting();
        return $admin->name;
    }

     function datetostrtotime($date) {
        $format = $this->getSchoolDateFormat();
        if (!empty($date)) {
            if ($format == 'd-m-Y')
                list($day, $month, $year) = explode('-', $date);
            if ($format == 'd/m/Y')
                list($day, $month, $year) = explode('/', $date);
            if ($format == 'd-M-Y')
                list($day, $month, $year) = explode('-', $date);
            if ($format == 'd.m.Y')
                list($day, $month, $year) = explode('.', $date);
            if ($format == 'm-d-Y')
                list($month, $day, $year) = explode('-', $date);
            if ($format == 'm/d/Y')
                list($month, $day, $year) = explode('/', $date);
            if ($format == 'm.d.Y')
                list($month, $day, $year) = explode('.', $date);

            $dater = $day . "-" . $month . "-" . $year;
            return strtotime($dater);
        }
    }

     function getHospitalDateFormat($date_only = true, $time = false) {
        // to be used by session or sch_setting table

        $setting_result = $this->CI->setting_model->get();
        $time_format = $setting_result[0]['time_format'];

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


       function getTimeZone() {
        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            return $admin['timezone'];
        } else if ($this->CI->session->userdata('patient')) {
            $student = $this->CI->session->userdata('patient');
            return $student['timezone'];
        }
    }


   /*   function getMonthList() {
        $months = array(1 => $this->CI->lang->line('january'), 2 => $this->CI->lang->line('february'), 3 => $this->CI->lang->line('march'), 4 => $this->CI->lang->line('april'), 5 => $this->CI->lang->line('may'), 6 => $this->CI->lang->line('june'), 7 => $this->CI->lang->line('july'), 8 => $this->CI->lang->line('august'), 9 => $this->CI->lang->line('september'), 10 => $this->CI->lang->line('october'), 11 => $this->CI->lang->line('november'), 12 => $this->CI->lang->line('december'));
        return $months;
    }*/

     function getDateFormat() {
        $dateFormat = array();
        $dateFormat['d-m-Y'] = 'dd-mm-yyyy';
        $dateFormat['d-M-Y'] = 'dd-mmm-yyyy';
        $dateFormat['d/m/Y'] = 'dd/mm/yyyy';
        $dateFormat['d.m.Y'] = 'dd.mm.yyyy';
        $dateFormat['m-d-Y'] = 'mm-dd-yyyy';
        $dateFormat['m/d/Y'] = 'mm/dd/yyyy';
        $dateFormat['m.d.Y'] = 'mm.dd.yyyy';
        return $dateFormat;
    }


     function timezone_list() {
        static $timezones = null;

        if ($timezones === null) {
            $timezones = [];
            $offsets = [];
            $now = new DateTime('now', new DateTimeZone('UTC'));

            foreach (DateTimeZone::listIdentifiers() as $timezone) {

                $now->setTimezone(new DateTimeZone($timezone));
                $offsets[] = $offset = $now->getOffset();
                $timezones[$timezone] = '(' . $this->format_GMT_offset($offset) . ') ' . $this->format_timezone_name($timezone);
            }

            array_multisort($offsets, $timezones);
        }
        return $timezones;
    }

      function format_GMT_offset($offset) {
        $hours = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));
        return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    public function format_timezone_name($name) {
        $name = str_replace('/', ', ', $name);
        $name = str_replace('_', ' ', $name);
        $name = str_replace('St ', 'St. ', $name);
        return $name;
    }

     function timeFormat() {
        $time_format = array();
        $time_format['24-hour'] = '24 Hour';
        $time_format['12-hour'] = '12 Hour';
        return $time_format;
    }


     function getUserData() {
        $result = $this->getLoggedInUserData();
        $id = $result["id"];
        $data = $this->CI->staff_model->get($id);
        return $data;
    }

     function getLoggedInUserData() {
        $admin = $this->CI->session->userdata('hospitaladmin');
        if ($admin) {
            return $admin;
        } else if ($this->CI->session->userdata('patient')) {
            $student = $this->CI->session->userdata('patient');
            return $student;
        }
    }


     function getCurrency() {
        $currency = array();
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

   //  public function isAppointmentBooked($appointment_id){
   //      $data           = array();
   //      $appointment_details   = $this->CI->webservice_model->getAppointmentDetails($appointment_id);
   //      $appointments   = $this->CI->webservice_model->getAppointmentsBySlot($appointment_details->doctor, $appointment_details->shift_id, date("Y-m-d",strtotime($appointment_details->date)),$appointment_details->time);
   //      if(empty($appointments)){
   //          return false;
   //      }else{
   //          return true;
   //      }
   // }
        
   public function YYYYMMDDHisTodateFormat($date, $twentyfour = false){
        
        if($date == "" || $date == NULL ){
            return NULL;
        } 

        
        $setting_result = $this->CI->setting_model->getSetting(); 
        $format        = $setting_result->date_format;  
        
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

        return $format_date." ".$time_format;     
       
    }
    
    public function getHospitalTimeFormat()
    {        
            $setting_result = $this->CI->setting_model->getSetting();  
            return $setting_result->time_format;
        
    }

    public function isAppointmentBooked($appointment_id){
        $data           = array();
        $this->CI->load->model('webservice_model');
        $appointment_details   = $this->CI->webservice_model->getAppointmentDetails($appointment_id);
        
        $appointments   = $this->CI->webservice_model->getAppointmentsBySlot($appointment_details->doctor, $appointment_details->doctor_shift_time_id, date("Y-m-d",strtotime($appointment_details->date)),'');
        if(empty($appointments)){
            return false;
        }else{
            return true;
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
	
	public function generatebarcode($id, $default_return_code = 'barcode')
    {
        $data = [];
        $code = $id;
        //load library
        $this->CI->load->library('zend');
        //load in folder Zend
        $this->CI->zend->load('Zend/Barcode');
        //generate barcode
        $imageResource = Zend_Barcode::factory('code128', 'image', array('text' => $code, 'barHeight' => 20), array())->draw();
        imagepng($imageResource, '../uploads/patient_id_card/barcodes/' . $code . '.png');
        $barcode = '../uploads/patient_id_card/barcodes/' . $code . '.png';

        //=============qrcode=================
        $this->CI->load->library('QR_Code');
        $qrcode =   $this->CI->qr_code->generate('../uploads/patient_id_card/qrcode/', $code);

        if ($default_return_code == "barcode") {
            return $barcode;
        } elseif ($default_return_code == "qrcode") {
            return '../uploads/patient_id_card/qrcode/' . $code . '.png';
        }
    } 

}
