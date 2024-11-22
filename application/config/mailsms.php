<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Config variables

$config['mailsms'] = array(
    'opd_patient_registration' => lang('opd_patient_registration'),
    'ipd_patient_registration' => lang('ipd_patient_registration'),
    'patient_discharged'       => lang('patient_discharged'),
    'patient_revisit'          => lang('patient_revisit'),
    'login_credential'         => lang('login_credential'),
    'appointment'              => lang('appointment'),
);

$config['notification'] = array(
    'appointment_created'  => lang('notificationurl_appointment_created') . " " . "<a href='<url>'><patient></a>",
    'appointment_approved' => lang('notificationurl_appointment_approved') . " " . "<a href='<url>'><patient></a>",
    'opd_created'          => lang('notificationurl_opd_created') . " " . "<a href='<url>'><opdno></a>",
    'opdpres_created'      => lang('notificationurl_opdpres_created') . " " . "<a href='<url>'><prescription_no></a>",
    'ipdpres_created'      => lang('notificationurl_ipdpres_created') . " " . "<a href='<url>'><prescription_no></a>",
    'ipd_created'          => lang('notificationurl_ipd_created') . " " . "<a href='<url>'><ipdno></a>",
    'ot_created'           => lang('notificationurl_ot_created') . " " . "<a href='<url>' onclick='<onchngfun>'><patient></a>",
    'salary_paid'          => lang('notificationurl_salary_amount') . " " . "<amount>" . " " . lang('notificationurl_has_been_paid_month') . " " . "<month>" . "<a href='<url>'><staffname></a>",
);

$config['patient_notification_url'] = array(
    'opd'         => "patient/dashboard/profile",
    'opdvisit'    => "patient/dashboard/visitdetails",
    'opdpres'     => "patient/dashboard/profile",
    'ipdpres'     => "patient/systemnotifications/moveipdpresnotification",
    'ipd'         => "patient/dashboard/ipdprofile",
    'appointment' => "patient/dashboard/appointment",
    'ot'          => "patient/dashboard/otsearch",
);

$config['notification_url'] = array(
    'opd'         => "admin/systemnotification/moveopdnotification",
    'opdpres'     => "admin/systemnotification/moveopdpresnotification",
    'ipd'         => "admin/systemnotification/moveipdnotification",
    'ipdpres'     => "admin/systemnotification/moveipdpresnotification",
    'appointment' => "admin/systemnotification/moveappointment",
    'ot'          => "admin/systemnotification/moveotpatient",
    'salary'      => "admin/systemnotification/movesalarypay",
);

$config['attendence'] = array(
    'present'          => 1,
    'late_with_excuse' => 2,
    'late'             => 3,
    'absent'           => 4,
    'holiday'          => 5,
    'half_day'         => 6,
);

$config['perm_category'] = array('can_view', 'can_add', 'can_edit', 'can_delete');

$config['bloodgroup'] = array('1' => 'O+', '2' => 'A+', '3' => 'B+', '4' => 'AB+', '5' => 'O-', '6' => 'A-', '7' => 'B-', '8' => 'AB-');

$config['smtp_encryption'] = array(
    ''    => 'OFF',
    'ssl' => 'SSL',
    'tls' => 'TLS',
);

$config['smtp_auth'] = array(
    'true'  => 'ON',
    'false' => 'OFF',
);
