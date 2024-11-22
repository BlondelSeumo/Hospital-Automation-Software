<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Item extends Admin_Controller
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
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Item/index');
        $data['title']       = $this->lang->line('add_item');
        $data['title_list']  = $this->lang->line('recent_items');
        $item_result         = $this->item_model->get();
        $data['itemlist']    = $item_result;
        $itemcategory        = $this->itemcategory_model->get();
        $data['itemcatlist'] = $itemcategory;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/item/itemList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getitemdatatable()
    {
        $dt_response = $this->item_model->getAllitemRecord();

        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $link   = " <a href='#' class='detail_popover'  data-toggle='popover' title=''>";
                $div    = "<div class='fee_detail_popover' style='display: none'>";               

                if ($this->rbac->hasPrivilege('item', 'can_edit')) {
                    $action .= " <a href='#' onclick='get_data(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('edit') . "'> <i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('item', 'can_delete')) {
                    $action .= "<a href='#' onclick='delete_record(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }
                $action .= "</div>";
                $row[]     = $link . $value->name . '</a>' . $div . "</div>" . $action;
                $row[]     = $value->item_category;
                $row[]     = $value->unit;
                $row[]     = $value->added_stock - $value->issued;
                $row[]     = $value->description;
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

    public function getitemreportdatatable()
    {
        $start_date             = '';
        $end_date               = '';
        $search['search_type']  = $this->input->post('search_type');
        $search['userroletype'] = $this->input->post('userroletype');
        $search['date_from']    = $this->input->post('date_from');
        $search['date_to']      = $this->input->post('date_to');

        if ($search['search_type'] == 'period') {
            $start_date = $this->customlib->dateFormatToYYYYMMDD($search['date_from']);
            $end_date   = $this->customlib->dateFormatToYYYYMMDD($search['date_to']);
        } else {
            if (isset($search['search_type']) && $search['search_type'] != '') {
                $dates               = $this->customlib->get_betweendate($search['search_type']);
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
            }

            $start_date = $dates['from_date'];
            $end_date   = $dates['to_date'];
        }

        $dt_response = $this->itemstock_model->getAllitemreportRecord($start_date,$end_date);
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $row[] = $value->name;
                $row[] = $value->item_category;
                $row[] = $value->item_supplier;
                $row[] = $value->item_store;
                $row[] = $value->available_stock;
                $row[] = $value->total_issued;
                $row[] = ($value->available_stock - $value->total_issued);

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

    public function add()
    {
        $this->form_validation->set_rules('name', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'trim|required|xss_clean');
        $this->form_validation->set_rules(
            'item_category_id', $this->lang->line('item_category'), array(
                'required',
                array('check_exists', array($this->item_model, 'valid_check_exists')),
            )
        );

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'             => form_error('name'),
                'unit'             => form_error('unit'),
                'item_category_id' => form_error('item_category_id'),
            );

            $array = array('status' => 'fail', 'error' => $msg);
        } else {

            $data = array(
                'item_category_id' => $this->input->post('item_category_id'),
                'name'             => $this->input->post('name'),
                'unit'             => $this->input->post('unit'),
                'description'      => $this->input->post('description'),
            );

            $insert_id = $this->item_model->add($data);
            $array     = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('new_item_successfully_inserted'));
        }
        echo json_encode($array);
    }

    public function download($file)
    {
        $this->load->helper('download');
        $filepath = "./uploads/inventory_items/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment();
        force_download($name, $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('item', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('fees_master_list');
        $this->item_model->remove($id);
        redirect('admin/item/index');
    }

    public function getAvailQuantity()
    {
        $item_id   = $this->input->get('item_id');
        $data      = $this->item_model->getItemAvailable($item_id);
        $available = ($data['added_stock'] - $data['issued']);
        echo json_encode(array('available' => $available));
    }

    public function handle_upload()
    {
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('jpg', 'jpeg', 'png');
            $temp        = explode(".", $_FILES["file"]["name"]);
            $extension   = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if ($_FILES["file"]["type"] != 'image/gif' &&
                $_FILES["file"]["type"] != 'image/jpeg' &&
                $_FILES["file"]["type"] != 'image/png') {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array(strtolower($extension), $allowedExts)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_extension_not_allowed'));
                return false;
            }
            if ($_FILES["file"]["size"] > 10240000) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than'));
                return false;
            }
            if ($error == "") {
                return true;
            }
        } else {
            return true;
        }
    }

    public function get_data($id)
    {
        $item = $this->item_model->get_item_data($id);

        $data = array(
            'id'               => $item['id'],
            'item_category_id' => $item['item_category_id'],
            'name'             => $item['name'],
            'unit'             => $item['unit'],
            'description'      => $item['description'],
        );

        echo json_encode($data);
    }

    public function edit()
    {
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'trim|required|xss_clean');
        $this->form_validation->set_rules(
            'item_category_id', $this->lang->line('item_category'), array(
                'required',
                array('check_exists', array($this->item_model, 'valid_check_exists')),
            )
        );

        if ($this->form_validation->run() == false) {
            $msg = array(
                'name'             => form_error('name'),
                'unit'             => form_error('unit'),
                'item_category_id' => form_error('item_category_id'),
            );

            $array = array('status' => 'fail', 'error' => $msg);
        } else {
            $data = array(
                'id'               => $this->input->post('id'),
                'name'             => $this->input->post('name'),
                'item_category_id' => $this->input->post('item_category_id'),
                'unit'             => $this->input->post('unit'),
                'description'      => $this->input->post('description'),
            );

            $insert_id = $this->item_model->add($data);
            $array     = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('new_item_successfully_inserted'));
        }
        echo json_encode($array);
    }

    public function itemreport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/inventory');
        $this->session->set_userdata('subsub_menu', 'reports/inventory/itemreport');         
        $data["searchlist"] = $this->search_type;
        $this->load->view('layout/header');
        $this->load->view('admin/item/itemreport', $data);
        $this->load->view('layout/footer');
    }

    public function additemreport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/inventory');
        $this->session->set_userdata('subsub_menu', 'reports/inventory/additemreport');
        $data["searchlist"] = $this->search_type;
        $this->load->view('layout/header');
        $this->load->view('admin/item/additemreport', $data);
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

    public function additemreportrecords()
    {
        $search['search_type'] = $this->input->post('search_type');
        $search['date_from']   = $this->input->post('date_from');
        $search['date_to']     = $this->input->post('date_to');
        $start_date            = '';
        $end_date              = '';
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
        
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

        $reportdata   = $this->itemstock_model->additemreportRecord($start_date, $end_date);
        $reportdata   = json_decode($reportdata);
        $dt_data      = array();
        $total_charge = 0;
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $total_charge += $value->purchase_price;
                $row       = array();
                $row[]     = $value->name;
                $row[]     = $value->item_category;
                $row[]     = $value->item_supplier;
                $row[]     = $value->item_store;
                $row[]     = $this->customlib->YYYYMMDDTodateFormat($value->date);
                $row[]     = $value->quantity;
                $row[]     = $value->purchase_price;
                $dt_data[] = $row;
            }

            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . $currency_symbol . (number_format($total_charge, 2, '.', '')) . "<br/>";
            $footer_row[] = "";

            $dt_data[] = $footer_row;
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
