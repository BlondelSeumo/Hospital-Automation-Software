<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bill_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add($bill_data,$module_data,$opd_ipd_transaction,$discount_percentage)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert("bill", $bill_data);

         $bill_id = $this->db->insert_id();

         if($opd_ipd_transaction['amount'] > 0){
         $opd_ipd_transaction['bill_id']=$bill_id;
         $this->db->insert("transactions", $opd_ipd_transaction);
         }
        $this->db->where("id", $bill_data["case_id"])->update("case_references", array('bill_id'=>$bill_id,'discount_percentage'=>$discount_percentage));

        if (!empty($module_data)) {
            # code...
                 foreach ($module_data as $m_key => $m_value) {
                    $module_data[$m_key]['bill_id']=$bill_id;
                 }
               
                $this->db->insert_batch('transactions', $module_data); 
        } 

        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    } 


}
