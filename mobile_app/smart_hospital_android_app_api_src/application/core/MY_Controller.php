<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Controller extends CI_Controller
{

    public function __construct()
    {

        parent::__construct();

    }

}

class Admin_Controller extends MY_Controller
{
    protected $aaaa = false;
    public function __construct()
    {
        parent::__construct();

        $this->load->model(array('paymentsetting_model', 'setting_model'));
        $this->setting        = $this->setting_model->get();
        date_default_timezone_set($this->setting[0]['timezone']);

    }

}
