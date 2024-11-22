<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bloodbankstatus_model extends CI_model
{

    public function getBloodGroup($id = null, $type = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('blood_bank_products');
            return $query->row_array();
        } else {
            if ($type != null) {
                $query = $this->db->where("is_blood_group", $type)->get("blood_bank_products");
            } else {
                $query = $this->db->get("blood_bank_products");
            }
            return $query->result_array();
        }
    }

    public function getBloodbank($patient_id)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('blood_issue', '', '', '', 1);

        $custom_field_column_array = array();
        $field_var_array           = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($custom_field_column_array, 'table_custom_' . $i . '.field_value');
                array_push($field_var_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->db->join('custom_field_values as ' . $tb_counter, 'blood_issue.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, "left");
                $i++;
            }
        }

        $field_variable      = (empty($field_var_array)) ? "" : "," . implode(',', $field_var_array);
        $custom_field_column = (empty($custom_field_column_array)) ? "" : "," . implode(',', $custom_field_column_array);

        $query = $this->db->select('blood_issue.*,sum(transactions.amount)as paid_amount,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no,blood_donor_cycle.unit' . $field_variable)
            ->join('patients', 'patients.id = blood_issue.patient_id')
            ->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id')
            ->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id')
            ->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id')
            ->join('transactions', 'transactions.blood_issue_id = blood_issue.id')
            ->group_by('transactions.blood_issue_id')
            ->where('blood_issue.patient_id', $patient_id)
            ->get('blood_issue');
        return $query->result_array();
    }

    public function getBloodbankStatusByid($blood_bank_product_id)
    {
        return $this->db->select('sum(blood_donor_cycle.quantity) as total')
            ->join('blood_donor', 'blood_donor.id=blood_donor_cycle.blood_donor_id')
            ->join('blood_bank_products', 'blood_donor.blood_bank_product_id=blood_bank_products.id')
            ->where('blood_donor.blood_bank_product_id', $blood_bank_product_id)
            ->where('blood_donor_cycle.available', 1)
            ->get('blood_donor_cycle')->row_array();
    }

    public function getBillDetailsBloodbank($id)
    {
        $query = $this->db->select('blood_issue.*,blood_bank_products.name as blood_group,patients.patient_name,patients.gender,blood_donor.donor_name,blood_donor_cycle.bag_no')
            ->join('patients', 'patients.id = blood_issue.patient_id')
            ->join('blood_donor_cycle', 'blood_donor_cycle.id = blood_issue.blood_donor_cycle_id')
            ->join('blood_donor', 'blood_donor_cycle.blood_donor_id = blood_donor.id')
            ->join('blood_bank_products', 'blood_bank_products.id = blood_donor.blood_bank_product_id')
            ->where('blood_issue.id', $id)
            ->get('blood_issue');
        return $query->row_array();
    }

    public function addBloodGroup($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('blood_bank_status', $data);
        } else {
            $this->db->insert('blood_bank_status', $data);
            return $this->db->insert_id();
        }
    }

    public function getall()
    {
        $this->datatables->select('id,blood_group,status');
        $this->datatables->from('blood_bank_status');
        $this->datatables->add_column('view', '<a href="' . site_url('admin/bloodbankstatuss/edit/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Edit"> <i class="fa fa-pencil"></i></a><a href="' . site_url('admin/bloodgroup/delete/$1') . '" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="Delete">
                                                        <i class="fa fa-remove"></i>
                                                    </a>', 'id,status');
        return $this->datatables->generate();
    }

    public function getDatatableAllproducts()
    {
        $this->datatables
            ->select('blood_bank_products.*')
            ->searchable('name')
            ->orderable('name,is_blood_group,volume')
            ->sort('id', 'desc')
            ->from('blood_bank_products');
        return $this->datatables->generate('json');
    }

    public function add_product($data)
    {
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('blood_bank_products', $data);
        } else {
            $this->db->insert('blood_bank_products', $data);
            return $this->db->insert_id();
        }

    }
    public function valid_product($str)
    {
        $name = $this->input->post('name');
        if ($this->check_name_exists($name)) {
            $this->form_validation->set_message('check_exists', 'Name already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_name_exists($name)
    {
        $id = $this->input->post("id");
        if ($id != 0) {
            $data  = array('name' => $name, 'id !=' => $id);
            $query = $this->db->where($data)->get('blood_bank_products');

            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('name', $name);
            $query = $this->db->get('blood_bank_products');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_product($id = null, $type = null)
    {
        if ($id != null) {

            $query = $this->db->where("id", $id)->get('blood_bank_products');
            return $query->row_array();

        } else {

            $query   = $this->db->get("blood_bank_products");
            $list    = $query->result_array();
            $product = array();
            foreach ($list as $key => $value) {
                if ($type == '') {
                    $product[$value['id']] = $value['name'];
                } else {
                    if ($type == $value['is_blood_group']) {
                        $product[$value['id']] = $value['name'];
                    }

                }

            }
            return $product;

        }
    }

    public function delete_product($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('blood_bank_products');
        return true;
    }

    public function deleteComponent($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('blood_donor_cycle');
        return true;
    }

    public function updatestockbyid($blood_donor_cycle_id)
    {
        $data = array('available' => 0);
        $this->db->where('id', $blood_donor_cycle_id);
        $this->db->update('blood_donor_cycle', $data);
    }

    public function get_stock_bloodgroup()
    {
        return $this->db->select('blood_bank_products.id,blood_bank_products.name ')->from('blood_donor')->join('blood_bank_products', 'blood_bank_products.id=blood_donor.blood_bank_product_id')->group_by('blood_donor.blood_bank_product_id')->get()->result_array();
    }

    public function validate_paymentamount()
    {
        $payment_amount = $this->input->post('payment_amount');
        $net_amount     = $this->input->post('net_amount');
        if ($payment_amount > $net_amount) {

            $this->form_validation->set_message('check_exists', 'Amount should not be greater than balance ' . $net_amount);
            return false;
        } else {
            return true;
        }

    }

}
