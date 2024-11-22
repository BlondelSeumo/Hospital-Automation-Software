<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Report extends Admin_Controller
{ 
    public function __construct()
    {
        parent::__construct();

    }

    public function finance(){
        $data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/finance');
        $this->session->set_userdata('subsub_menu', '');

    	$this->load->view('layout/header');
        $this->load->view('admin/report/finance', $data);
        $this->load->view('layout/footer');
    } 

    public function appointment(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/appointment');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/appointment', $data);
        $this->load->view('layout/footer');
    }

    public function opd(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/opd');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/opd', $data);
        $this->load->view('layout/footer');
    }

    public function ipd(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/ipd');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/ipd', $data);
        $this->load->view('layout/footer');
    }

    public function pharmacy(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pharmacy');
        $this->session->set_userdata('subsub_menu', '');
        
        $this->load->view('layout/header');
        $this->load->view('admin/report/pharmacy', $data);
        $this->load->view('layout/footer');
    }

    public function radiology(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/radiology');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/radiology', $data);
        $this->load->view('layout/footer');
    }

    public function pathology(){
        $data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pathology');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/pathology', $data);
        $this->load->view('layout/footer');
    }

    public function blood_bank(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/bloodbank');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/blood_bank', $data);
        $this->load->view('layout/footer');
    }
 
    public function ambulance(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/ambulance');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/ambulance', $data); 
        $this->load->view('layout/footer');
    }

    public function birth_death(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/birth_death');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/birth_death', $data); 
        $this->load->view('layout/footer');
    }
 
    public function ot(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/ot');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/ot', $data); 
        $this->load->view('layout/footer');
    }

    public function human_resource(){
        $data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/human_resource');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/human_resource', $data); 
        $this->load->view('layout/footer');
    }

    public function tpa(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/tpa');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/tpa', $data); 
        $this->load->view('layout/footer');
    }

    public function inventory(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/inventory');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/inventory', $data); 
        $this->load->view('layout/footer');
    }

    public function live_consultation(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/live_consultation');
        $this->session->set_userdata('subsub_menu', '');

        $this->load->view('layout/header');
        $this->load->view('admin/report/live_consultation', $data); 
        $this->load->view('layout/footer');
    }

    public function log(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/log');
        $this->session->set_userdata('subsub_menu', '');
        
        $this->load->view('layout/header');
        $this->load->view('admin/report/log', $data); 
        $this->load->view('layout/footer');
    }

    public function patient(){
    	$data=array();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/patient');
        $this->session->set_userdata('subsub_menu', '');
        
        $this->load->view('layout/header');
        $this->load->view('admin/report/patient', $data); 
        $this->load->view('layout/footer');
    }
}