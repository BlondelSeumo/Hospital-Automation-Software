<?php

$config['staffattendance'] = array(
    'present'  => 1,
    'half_day' => 4,
    'late'     => 2,
    'absent'   => 3,
    'holiday'  => 5,
    'half_day_second_shift'  => 6,
);

$config['payment_section'] = array(
    'opd'         => lang('opd'),
    'ipd'         => lang('ipd'),
    'pharmacy'    => lang('pharmacy'),
    'pathology'   => lang('pathology'),
    'radiology'   => lang('radiology'),
    'blood_bank'  => lang('blood_bank'),
    'ambulance'   => lang('ambulance'),
    'appointment' => lang('appointment'),
);

$config['visit_to'] = array(
    'staff'       => lang('staff'),
    'opd_patient' => lang('opd_patient'),
    'ipd_patient' => lang('ipd_patient'),
);

$config['contracttype'] = array(
    'permanent' => lang('permanent'),
    'probation' => lang('probation'),
);

$config['status'] = array(
    'pending'    => lang('pending'),
    'approve'    => lang('approved'),
    'disapprove' => lang('disapprove'),
);

$config['marital_status'] = array(
    'single'        => lang('single'),
    'married'       => lang('married'),
    'widowed'       => lang('widowed'),
    'separated'     => lang('separated'),
    'not_specified' => lang('not_specified'),
);
$config['staff_marital_status'] = array(
    'single'        => lang('single'),
    'married'       => lang('married'),
    'widowed'       => lang('widowed'),
    'separated'     => lang('separated'),
    'not_specified' => lang('not_specified'),
);

$config['staff_bloodgroup'] = array('1' => 'O+', '2' => 'A+', '3' => 'B+', '4' => 'AB+', '5' => 'O-', '6' => 'A-', '7' => 'B-', '8' => 'AB-');

$config['payroll_status'] = array(
    'generated'    => lang('generated'),
    'paid'         => lang('paid'),
    'unpaid'       => lang('unpaid'),
    'not_generate' => lang('not_generated'),
); 

$config['payment_mode'] = array(
    'Cash'                     => lang('cash'),
    'Cheque'                   => lang('cheque'),
    'transfer_to_bank_account' => lang('transfer_to_bank_account'),
    'UPI'                      => lang('upi'),
    'Online'                    => lang('online'),
    'Other'                    => lang('other'),    
);

$config['yesno_condition'] = array(
    'no'  => lang('no'),
    'yes' => lang('yes'),
);

$config['opd_ipd'] = array(
    'none' => lang('none'),
    'opd'  => lang('opd'),
    'ipd'  => lang('ipd'),
);

$config['enquiry_status'] = array(
    'active'  => lang('active'),
    'passive' => lang('passive'),
    'dead'    => lang('dead'),
    'won'     => lang('won'),
    'lost'    => lang('lost'),
);

$config['charge_type'] = array(
    'Procedures'        => lang('procedures'),
    'Investigations'    => lang('investigations'),
    'Supplier'          => lang('supplier'),
    'Operation Theatre' => lang('operation'),
    'Others'            => lang('others'),
); 

$config['appointment_status'] = array(
    'pending'  => lang('pending'),
    'approved' => lang('approved'),
    'cancel'   => lang('cancel'),
);

$config['appointment_type'] = array(
    'Online'  => lang('online'),
    'Offline' => lang('offline'),
);

$config['search_type'] = array(     
    'today'         => lang('today'),
    'this_week'     => lang('this_week'),
    'last_week'     => lang('last_week'),
    'this_month'    => lang('this_month'),
    'last_month'    => lang('last_month'),
    'last_3_month'  => lang('last_three_month'),
    'last_6_month'  => lang('last_six_month'),
    'last_12_month' => lang('last_twelve_month'),
    'this_year'     => lang('this_year'),
    'last_year'     => lang('last_year'),
    'period'        => lang('period'),
);

$config['search_type_expiry'] = array(
    'this_month'   => lang('this_month'),
    'last_month'   => lang('last_month'),
    'last_3_month' => lang('last_three_month'),
    'last_6_month' => lang('last_six_month'),
    'this_year'    => lang('this_year'),
    'last_year'    => lang('last_year'),
    'next_month'   => lang('next_month'),
    'next_3_month' => lang('next_three_month'),
    'next_6_month' => lang('next_six_month'),
    'next_year'    => lang('next_year'),
);

$config['agerange'] = array(
    '0'   => 0,
    '5'   => 5,
    '10'  => 10,
    '15'  => 15,
    '20'  => 20,
    '25'  => 25,
    '30'  => 30,
    '35'  => 35,
    '40'  => 40,
    '45'  => 45,
    '50'  => 50,
    '55'  => 55,
    '60'  => 60,
    '65'  => 65,
    '70'  => 70,
    '75'  => 75,
    '80'  => 80,
    '85'  => 85,
    '90'  => 90,
    '95'  => 95,
    '100' => 100,
);
