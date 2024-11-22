<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
    protected $CI;

    public function __construct() {
        parent::__construct();
            // reference to the CodeIgniter super object
        $this->CI =& get_instance();
    }


  public function valid_amount($str) {

      $CI = $this->CI =& get_instance(); // Get your CodeIgniter instance
      
      // if (!preg_match("/^\d+(\.\d{1,2})?$/", $str)) 
      if (!preg_match("/^(0*[0-9][0-9]*(\.[0-9]+)?|0+\.[0-9]*[0-9][0-9]*)$/", $str)) 
      {
          $this->CI->form_validation->set_message('valid_amount', 'Invalid {field}.');
                return FALSE;
      }
    
      return TRUE;
    }

  public function valid_integer($str) {
      $CI = $this->CI =& get_instance(); // Get your CodeIgniter instance
      if (!preg_match("/^(-?[1-9]+\d*([.]\d+)?)$|^(-?0[.]\d*[1-9]+)$|^0$|^0.0$/", $str)) 
      {
          $this->CI->form_validation->set_message('valid_integer', 'Invalid {field}.');
                return FALSE;
      }
    
      return TRUE;
    }


  public function valid_tax($str) {
      $CI = $this->CI =& get_instance(); // Get your CodeIgniter instance
      if (!preg_match("/^\d+(\.\d{1,2})?$/", $str)) 
      {
          $this->CI->form_validation->set_message('valid_tax', 'Invalid {field}.');
                return FALSE;
      }
    
      return TRUE;
    }


 

}