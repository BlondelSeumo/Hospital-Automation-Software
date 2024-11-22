<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Issueitem extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('datatables');
        $this->config->load("payroll");
        $this->search_type = $this->config->item('search_type');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('issue_item', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'issueitem/index');
        $data['title']       = $this->lang->line('add_issue_item');
        $data['title_list']  = $this->lang->line('recent_issue_items');
        $roles               = $this->role_model->get();
        $data['roles']       = $roles;
        $itemcategory        = $this->itemcategory_model->get();
        $data['itemcatlist'] = $itemcategory;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/issueitem/issueitemList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getissueitemdatatable()
    {
        $dt_response = $this->itemissue_model->getAllissueitemRecord();

        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================

                if ($value->return_date == "0000-00-00") {
                    $return_date = "";
                } else {
                    $return_date = $this->customlib->YYYYMMDDTodateFormat($value->return_date);
                }

                $action = "<div class='rowoptionview rowview-mt-19'>";
                $link   = "<a href='#' class='detail_popover'  data-toggle='popover' title=''>";
                $div    = "<div class='fee_detail_popover' style='display: none'>";

                

                if ($value->is_returned == 1) {
                    $status = "<span  class='label label-danger item_remove'  data-item='" . $value->id . "' data-category='" . $value->item_category . "' data-item_name='" . $value->item_name . "' data-quantity='" . $value->quantity . "' data-toggle='modal' data-target='#confirm-delete' title=''>" . $this->lang->line('click_to_return') . "</span>";
                } else {
                    $status = "<span class='label label-success'>" . $this->lang->line('returned') . "</span>";
                }

                if ($this->rbac->hasPrivilege('issue_item', 'can_delete')) {
                    $action .= "<a href='#' onclick='delete_record(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                //==============================
                $row[]     = $link . $value->item_name . '</a>' . $div . "</div>" . $action;
                $row[]     = $value->item_category;
                $row[]     = $this->customlib->YYYYMMDDTodateFormat($value->issue_date) . ' - ' . $this->customlib->YYYYMMDDTodateFormat($value->return_date);
                $row[]     = $value->name . " " . $value->docname . " " . $value->docsurname . " (" . $value->employee_id . ")";
                $row[]     = $value->issue_by;
                $row[]     = $value->quantity;
                $row[]     = $value->note;
                $row[]     = $status;                
                $row[]     = "";
                $dt_data[] = $row;
            }
        }
        $json_data = array(
            "draw"            => intval($dt_response->draw),
            "recordsTotal"    => intval($dt_response->recordsTotal),
            "recordsFiltered" => intval($dt_response->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function create()
    {
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'issueitem/index');
        $data['title']       = $this->lang->line('add_issue_item');
        $data['title_list']  = $this->lang->line('recent_issue_items');
        $roles               = $this->role_model->get();
        $data['roles']       = $roles;
        $itemcategory        = $this->itemcategory_model->get();
        $data['itemcatlist'] = $itemcategory;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/issueitem/issueitemCreate', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        $this->form_validation->set_rules('account_type', $this->lang->line('user_type'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('issue_to', $this->lang->line('issue_to'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('issue_by', $this->lang->line('issued_by'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('issue_date', $this->lang->line('issue_date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->lang->line('item_category'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('item_id', $this->lang->line('item'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('quantity', $this->lang->line('quantity'), 'trim|greater_than[0]|required|xss_clean|callback_check_available_quantity');
        if ($this->form_validation->run() == false) {
            $data = array(
                'account_type'     => form_error('account_type'),
                'issue_to'         => form_error('issue_to'),
                'issue_by'         => form_error('issue_by'),
                'issue_date'       => form_error('issue_date'),
                'quantity'         => form_error('quantity'),
                'item_category_id' => form_error('item_category_id'),
                'item_id'          => form_error('item_id'),
            );

            $array = array('status' => 'fail', 'error' => $data);
        } else {
            $return_date = "";
            $date        = $this->input->post('return_date');
            $issue_date  = $this->input->post('issue_date');
            if (($this->input->post('return_date')) != "") {
                $return_date = $this->customlib->dateFormatToYYYYMMDD($date);
            }
            $data = array(
                'issue_to'         => $this->input->post('issue_to'),
                'issue_by'         => $this->input->post('issue_by'),
                'issue_date'       => $this->customlib->dateFormatToYYYYMMDD($issue_date),
                'return_date'      => $return_date,
                'note'             => $this->input->post('note'),
                'quantity'         => $this->input->post('quantity'),
                'issue_type'       => $this->input->post('account_type'),
                'item_category_id' => $this->input->post('item_category_id'),
                'item_id'          => $this->input->post('item_id'),
            );

            $this->itemissue_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function check_available_quantity()
    {
        $item_category_id = $this->input->post('item_category_id');
        $item_id          = $this->input->post('item_id');
        $quantity         = $this->input->post('quantity');
        if ($quantity != "" && $item_category_id != "" && $item_id != "") {
            $data      = $this->item_model->getItemAvailable($item_id);
            $available = ($data['added_stock'] - $data['issued']);
            if ($quantity <= $available) {
                return true;
            }
            $this->form_validation->set_message('check_available_quantity', $this->lang->line('available_quantity') . " " . $available);
            return false;
        }
        return true;
    }

    public function delete($id)
    {
        $data['title'] = 'Delete';
        $this->itemissue_model->remove($id);
        redirect('admin/issueitem');
    }

    public function getUser()
    {

        $usertype     = $this->input->post('usertype');
        $result_final = array();
        $result       = array();
        if ($usertype != "") {
            $result = $this->staff_model->getEmployeeByRoleID($usertype);
        }

        $result_final = array('usertype' => $usertype, 'result' => $result);
        echo json_encode($result_final);
    }

    public function returnItem()
    {
        $issue_id = $this->input->post('item_issue_id');
        if ($issue_id != "") {
            $data = array(
                'id'          => $issue_id,
                'is_returned' => 0,
                'quantity'    => 0,
                'return_date' => date('Y-m-d'),
            );
            $this->itemissue_model->add($data);
        }

        $result_final = array('status' => 'pass', 'message' => "Item retrun successfully");
        echo json_encode($result_final);
    }

    public function issueinventoryreport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/inventory');
        $this->session->set_userdata('subsub_menu', 'reports/inventory/issueinventoryreport');

        $data["searchlist"] = $this->search_type;
        $this->load->view('layout/header');
        $this->load->view('admin/issueitem/issueinventoryreport', $data);
        $this->load->view('layout/footer');
    }

    public function checkvalidation()
    {
        $search = $this->input->post('search');
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'search_type' => form_error('search_type'),
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $param = array(
                'search_type' => $this->input->post('search_type'),

                'date_from'   => $this->input->post('date_from'),
                'date_to'     => $this->input->post('date_to'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function issueinventoryreports()
    {
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $start_date            = '';
        $end_date              = '';

        if ($search['search_type'] == 'period') {

            $start_date = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);

        } else {

            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
                $data['search_type'] = $search['search_type'];
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }

            $start_date = $dates['from_date'];
            $end_date   = $dates['to_date'];

        }

        $reportdata = $this->itemissue_model->issueinventoryreportRecord($start_date, $end_date);
        $reportdata = json_decode($reportdata);
        $dt_data    = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $row   = array();
                $row[] = $value->item_name;
                $row[] = $value->item_category;
                if ($value->return_date == "0000-00-00") {
                    $return_date = "";
                } else {
                    $return_date = $this->customlib->YYYYMMDDTodateFormat($value->return_date);
                }
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->issue_date) . " - " . $return_date;

                $row[] = $value->name . " " . $value->dname . " " . $value->surname . " (" . $value->employee_id . ")";
                $row[] = $value->issue_by;
                $row[] = $value->quantity;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

}
