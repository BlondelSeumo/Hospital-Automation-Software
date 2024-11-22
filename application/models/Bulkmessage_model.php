<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class bulkmessage_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllcredentialRecord()
    {
        $this->datatables
            ->select('patients.*,users.id as uid,users.user_id,users.username,users.password')
            ->join('users', 'patients.id = users.user_id')
            ->searchable('patients.id,patients.patient_name,users.username')
            ->orderable('patients.id,patients.patient_name,users.username')
            ->sort('patients.id', 'desc')
            ->from('patients');

        return $this->datatables->generate('json');

    }

 }   