<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function isJSON($string)
{
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

function findPrefixType($prefixes, $search_prefix)
{
    foreach ($prefixes as $prefix_key => $prefix_value) {
        if ($prefix_value->prefix == $search_prefix) {
            return $prefix_value->type;
        }
    }
    return false;
}

function splitPrefixID($search)
{
    $search_prefix = preg_replace('/[^0-9]/', '', $search);

    return $search_prefix;
}

function splitPrefixType($search)
{
    $search_prefix = preg_replace('/[^a-zA-Z]/', '', $search);

    return $search_prefix;
}

function chkDuplicate($arr)
{
    $dups = array();
    foreach (array_count_values($arr) as $val => $c) {
        if ($c > 1) {
            $dups[] = $val;
        }
    }

    return $dups;
}

function has_duplicate_array($array)
{
    return count($array) !== count(array_unique($array));
}

function amountFormat($amount)
{
    return number_format((float) $amount, 2, '.', '');
}

function uniqueFileName()
{
    return time() . uniqid(rand());
}

function composePatientName($patient_name, $patient_id)
{
    $name = "";
    if ($patient_name != "") {
        $name = ($patient_id != "") ? $patient_name . " (" . $patient_id . ")" : $patient_name;
    }

    return $name;
}

function composeStaffName($staff)
{
    $name = "";
    if (!empty($staff)) {
        $name = ($staff->surname == "") ? $staff->name : $staff->name . " " . $staff->surname;
    }

    return $name;
}

function composeStaffNameByString($staff_name, $staff_surname, $staff_employeid)
{
    $name = "";
    if ($staff_name != "") {
        $name = ($staff_surname == "") ? $staff_name . " (" . $staff_employeid . ")" : $staff_name . " " . $staff_surname . " (" . $staff_employeid . ")";
    }

    return $name;
}

function calculatePercent($amount, $percent)
{
    $ci = &get_instance();
    $ci->load->helper('custom');
    $percent_amt = 0;
    if ($amount != "") {
        $percent_amt = ($amount * $percent) / 100;
        $percent_amt = amountFormat($percent_amt);
    }
    return $percent_amt;
}

function chat_couter()
{
    $ci = &get_instance();
    return $ci->chatuser_model->getChatUnreadCount();
}

function cal_percentage($first_amount, $secound_amount)
{
    if ($secound_amount > 0) {
        $count1 = $first_amount / $secound_amount;
        $count2 = $count1 * 100;
        $count  = number_format($count2, 2);
    } else {
        $count = 0;
    }

    return $count;
}

function searchForKeyData($id, $array, $find_key)
{
    foreach ($array as $key => $val) {

        if ($val[$find_key] == $id) {
            return $key;
        }
    }
    return null;
}

function rand_color()
{
    $array = array(
        '#267278',
        '#50aed3',
        '#e46031',
        '#65228d',
        '#48b24f',
        '#e4B031',
        '#cad93f',
        '#d21f75',
        '#3b3989',
        '#58595b',
    );
    return $array;
}

function sortInnerData($a, $b)
{
    return $a['total_counts'] < $b['total_counts']?1:-1;
}


function img_time(){
   return "?".time();
}

function random_string($len = 5){
  $string = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, $len);
  return $string;
}

if (!function_exists('custom_url')) {

    function custom_url() {

        $CI = &get_instance();
        $session_data=$CI->session->userdata('admin');
        return $session_data['base_url'];
      
    }
}

if (!function_exists('dir_path')) {

    function dir_path() {

        $CI = &get_instance();
        $session_data=$CI->session->userdata('admin');
        return $session_data['folder_path'];
      
    }
}

if (!function_exists('IsNullOrEmptyString')) {

function IsNullOrEmptyString($str){
    return ($str === null || trim($str) === '');
}
}

if (!function_exists('check_report_level_exceed')) {

    function check_report_level_exceed($old_reference_range,$range_from,$range_to,$patient_range) {
        
    if($patient_range == ""){
        return false;
    }
    if(IsNullOrEmptyString($range_from)){
    
        $range=explode('-',$old_reference_range);
        $range_from=trim($range[0]);
        $range_to=trim($range[1]);
    }

    $range_to= IsNullOrEmptyString($range_to) ? $range_from : $range_to;

    if ($range_from <= $patient_range && $range_to >= $patient_range) {
      return false;
    }
      return true;
      
    }
}
