<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pathology_category_model extends MY_model
{
    public function valid_category_name($str)
    {
        $category_name = $this->input->post('category_name');
        $id            = $this->input->post('pathology_category_id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_category_exists($category_name, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function valid_unit_name($str)
    {
        $unit_name = $this->input->post('unit_name');
        $id        = $this->input->post('unit_id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_unit_exists($unit_name, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function valid_parameter_name($str)
    {
        $parameter_name = $this->input->post('parameter_name');
        $id             = $this->input->post('pathology_parameter_id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_parameter_exists($parameter_name, $id)) {
            $this->form_validation->set_message('check_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }

    public function getCategoryName($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('pathology_category');
            return $query->row_array();
        } else {
            $query = $this->db->get("pathology_category");
            return $query->result_array();
        }
    }

    public function getparameterDetailsforpatient($report_id)
    {
        $query = $this->db->select('pathology_report_parameterdetails.*,pathology_parameter.parameter_name,pathology_parameter.reference_range,pathology_parameter.unit,unit.unit_name')
            ->join('pathology_parameter', 'pathology_parameter.id = pathology_report_parameterdetails.parameter_id')
            ->join('unit', 'unit.id = pathology_parameter.unit')
            ->where("pathology_report_parameterdetails.pathology_report_id", $report_id)
            ->get('pathology_report_parameterdetails');
        return $query->result_array();
    }

    public function getparameterDetails($id, $value_id = '')
    {
        $check_query = $this->db->select('pathology_parameterdetails.*')
            ->where('pathology_parameterdetails.pathology_report_id', $value_id)
            ->get('pathology_parameterdetails');
        $num_rows = $check_query->num_rows();

        if ($num_rows > 0) {
            $query = $this->db->select('pathology_parameterdetails.*,pathology_parameter.parameter_name,pathology_parameter.reference_range,pathology_parameter.unit,unit.unit_name')
                ->join('pathology_parameter', 'pathology_parameter.id = pathology_parameterdetails.pathology_parameter_id')
                ->join('unit', 'unit.id = pathology_parameter.unit')
                ->where('pathology_parameterdetails.pathology_id', $id)
                ->where("pathology_parameterdetails.pathology_report_id", $value_id)
                ->get('pathology_parameterdetails');
        } else {
            $query = $this->db->select('pathology_parameterdetails.pathology_id,pathology_parameterdetails.pathology_parameter_id,pathology_parameterdetails.created_id,pathology_parameterdetails.pathology_report_id,pathology_parameter.parameter_name,pathology_parameter.reference_range,pathology_parameter.id as parid,pathology_parameter.unit,unit.unit_name')
                ->join('pathology_parameter', 'pathology_parameter.id = pathology_parameterdetails.pathology_parameter_id')
                ->join('unit', 'unit.id = pathology_parameter.unit')
                ->where('pathology_parameterdetails.pathology_id', $id)
                ->get('pathology_parameterdetails');
        }
        return $query->result_array();
    }

    public function getpathoparameter($id = null)
    {
        if (!empty($id)) {
            $this->db->select('pathology_parameter.*,unit.unit_name');
            $this->db->from('pathology_parameter');
            $this->db->join('unit', 'pathology_parameter.unit = unit.id', 'left');
            $this->db->where("pathology_parameter.id", $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->select('pathology_parameter.*,unit.unit_name');
            $this->db->from('pathology_parameter');
            $this->db->join('unit', 'pathology_parameter.unit = unit.id', 'left');           
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function getunit($id = null)
    {
        if (!empty($id)) {
            $this->db->select("unit.*");
            $this->db->where('id', $id);
            $this->db->where('unit_type', 'patho');
            $query = $this->db->get('unit');
            return $query->row_array();
        } else {
            $this->db->select("unit.*");
            $this->db->where('unit_type', 'patho');
            $query = $this->db->get('unit');
            return $query->result_array();
        }
    }

    public function check_category_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'category_name' => $name);
            $query = $this->db->where($data)->get('pathology_category');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('category_name', $name);
            $query = $this->db->get('pathology_category');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function check_unit_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'unit_name' => $name);
            $query = $this->db->where($data)->get('unit');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('unit_name', $name);
            $query = $this->db->get('unit');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function check_parameter_exists($name, $id)
    {
        if ($id != 0) {
            $data  = array('id != ' => $id, 'parameter_name' => $name);
            $query = $this->db->where($data)->get('pathology_parameter');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('parameter_name', $name);
            $query = $this->db->get('pathology_parameter');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function addCategoryName($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('pathology_category', $data);            
            $message = UPDATE_RECORD_CONSTANT . " On Pathology Category id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);            
        } else {
            $this->db->insert('pathology_category', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Pathology Category id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
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

    public function addunit($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('unit', $data);            
            $message = UPDATE_RECORD_CONSTANT . " On Unit id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);            
        } else {
            $this->db->insert('unit', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Unit id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
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

    public function addparameter($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('pathology_parameter', $data);            
            $message = UPDATE_RECORD_CONSTANT . " On Pathology Parameter id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);            
        } else {
            $this->db->insert('pathology_parameter', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Pathology Parameter id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
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

    public function getAllDetails($id)
    {
        $query = $this->db->select('pathology_parameterdetails.*')
            ->where('pathology_id', $id)
            ->get('pathology_parameterdetails');
        return $query->result_array();
    }

    public function getPathlogyParamsById($id)
    {
        $this->db->select('pathology_parameterdetails.*,pathology_parameter.parameter_name,pathology_parameter.reference_range,pathology_parameter.unit,unit.unit_name');
        $this->db->join('pathology_parameter', 'pathology_parameter.id = pathology_parameterdetails.pathology_parameter_id');
        $this->db->join('unit', 'unit.id = pathology_parameter.unit');
        $this->db->where('pathology_id', $id);
        $query = $this->db->get('pathology_parameterdetails');
        return $query->result();
    }

    public function getall()
    {
        $this->datatables->select('id,category_name');
        $this->datatables->from('pathology_category');
        $this->datatables->add_column('view', '<a href="' . site_url('admin/pathologycategory/edit/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Edit"> <i class="fa fa-pencil"></i></a><a href="' . site_url('admin/pathologycategory/delete/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Delete">
                                                        <i class="fa fa-remove"></i>
                                                    </a>', 'id');
        return $this->datatables->generate();
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("pathology_category");
        $message = DELETE_RECORD_CONSTANT . " On Pathology Category id " . $id;
        $action = "Delete";
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

    public function deleteunit($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("unit");
        $message = DELETE_RECORD_CONSTANT . " On Unit id " . $id;
        $action = "Delete";
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

    public function deleteparameter($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->delete("pathology_parameter");
        $message = DELETE_RECORD_CONSTANT . " On Pathology Parameter id " . $id;
        $action = "Delete";
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

}
