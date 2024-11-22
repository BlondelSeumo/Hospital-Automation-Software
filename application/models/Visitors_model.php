<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Visitors_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('visitors_book', $data);
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On Visitors Book id " . $insert_id;
        $action    = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getPurpose()
    {
        $this->db->select('*');
        $this->db->from('visitors_purpose');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function visitors_list($id = null)
    {
        $this->db->select()->from('visitors_book');
        if ($id != null) {
            $this->db->where('visitors_book.id', $id);
        } else {
            $this->db->order_by('visitors_book.id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getAllvisitorsRecord()
    {
        $this->datatables
            ->select('visitors_book.*,staff.name as staff_name,staff.surname,staff.employee_id,opd_details.id as opd_no,patients.patient_name as opd_patient_name,patients.id as opd_patient_id, ipd_details.id as ipd_no,p.patient_name as ipd_patient_name,p.id as ipd_patient_id')
            ->join('staff', 'staff.id = visitors_book.ipd_opd_staff_id', 'left')
            ->join('opd_details', 'opd_details.id=visitors_book.ipd_opd_staff_id', 'left')
            ->join('patients', 'patients.id=opd_details.patient_id', 'left')
            ->join('ipd_details', 'ipd_details.id=visitors_book.ipd_opd_staff_id', 'left')
            ->join('patients as p', 'p.id=ipd_details.patient_id', 'left')
            ->searchable('visitors_book.purpose,visitors_book.name,visitors_book.visit_to,staff.surname,visitors_book.contact,visitors_book.date,visitors_book.in_time,visitors_book.out_time')
            ->orderable('visitors_book.purpose,visitors_book.name,visitors_book.visit_to,staff.surname,visitors_book.contact,visitors_book.date,visitors_book.in_time,visitors_book.out_time')
            ->sort('visitors_book.date', 'desc')
            ->from('visitors_book');
        return $this->datatables->generate('json');
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('visitors_book');
        redirect('admin/visitors');
    }

    public function update($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('visitors_book', $data);
        $message   = UPDATE_RECORD_CONSTANT . " On Visitors Book id " . $id;
        $action    = "Update";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function image_add($visitor_id, $image)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $array = array('id' => $visitor_id);
        $this->db->set('image', $image);
        $this->db->where($array);
        $this->db->update('visitors_book');

        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On Visitors Book id " . $insert_id;
        $action    = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function image_update($visitor_id, $image)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $array = array('id' => $visitor_id);
        $this->db->set('image', $image);
        $this->db->where($array);
        $this->db->update('visitors_book');
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On Visitors Book id " . $insert_id;
        $action    = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function image_delete($id, $img_name)
    {
        $file = "./uploads/front_office/visitors/" . $img_name;
        unlink($file);
        $this->db->where('id', $id);
        $this->db->delete('visitors_book');
        $controller_name = $this->uri->segment(2);
        $this->session->set_flashdata('msg', '<div class="alert alert-success"> ' . ucfirst($controller_name) . ' ' . $this->lang->line("delete_message") . '</div>');
        redirect('admin/' . $controller_name);
    }

    public function getstaff()
    {
        $this->db->select('id,name,surname,employee_id');
        $this->db->from('staff');
        $this->db->where('is_active', '1');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getipd()
    {
        $this->db->select('ipd_details.id,patients.patient_name,patients.id as patient_id');
        $this->db->from('ipd_details');
        $this->db->join('patients', 'patients.id=ipd_details.patient_id');
        $this->db->where('discharged', 'no');
        $this->db->where('is_active', 'yes');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getopd()
    {
        $this->db->select('opd_details.id,patients.patient_name,patients.id as patient_id');
        $this->db->from('opd_details');
        $this->db->join('patients', 'patients.id=opd_details.patient_id');
        $this->db->where('discharged', 'no');
        $this->db->where('is_active', 'yes');
        $query = $this->db->get();
        return $query->result_array();
    }

}
