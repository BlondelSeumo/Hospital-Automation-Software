<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

$config['custom_fields'] = array(
    "checkbox"         => "Checkbox",
    "colorpicker"      => "Color Picker",
    "date_picker"      => "Date Picker",
    "date_picker_time" => "Datetime Picker",
    "input"            => "Input",
    "link"             => "Hyperlink",
    "multiselect"      => "Multi Select",
    "number"           => "Number",
    "select"           => "Dropdown",
    "textarea"         => "Textarea",
);

$config['custom_field_table'] = array(
    "ambulance_call"        => lang('ambulance_call'),
    "antenatal"             => lang('antenatal'),
    "appointment"           => lang('appointment'),
    "birth_report"          => lang('birth_record'),
    "blood_issue"           => lang('blood_issue'),
    "component_issue"       => lang('component_issue'),
    "death_report"          => lang('death_record'),
    "donor"                 => lang('donor'),
    "expenses"              => lang('expenses'), 
    "income"                => lang('income'),
    "ipd"                   => lang('ipd'),
    "ipdconsultinstruction" => lang('consultant_register'),
    "ipdnursenote"          => lang('ipd_nurse_note'),
    "opd"                   => lang('opd'),
    "opdrecheckup"          => lang('opd_recheckup'),
    "operationtheatre"      => lang('operation'),
    "prescription"             => lang('prescription'),
    "pathology"             => lang('pathology'),
    "pathologytest"         => lang('pathology_test'),
    "patient"               => lang('patient'),
    "pharmacy"              => lang('pharmacy'),
    "radiology"             => lang('radiology'),
    "radiologytest"         => lang('radiology_test'),
    "staff"                 => lang('staff'),
);
