<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

function isJSON($string)
{
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

function searchForId($find, $array,$find_in_key) {
   foreach ($array as $key => $val) {

       if ($val[$find_in_key] === $find) {  //if ($val['pharmacy_id'] === $find) {
           return $key;
       }
   }
   return NULL;
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

function composeStaffNameByString($staff_name, $staff_surname, $staff_employeid)
{
    $name = "";
    if ($staff_name != "") {
        $name = ($staff_surname == "") ? $staff_name . " (" . $staff_employeid . ")" : $staff_name . " " . $staff_surname . " (" . $staff_employeid . ")";
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
