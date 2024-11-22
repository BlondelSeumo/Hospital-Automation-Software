<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Itemstock extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('datatables');
        $this->config->load('image_valid');
    }

    public function index()
    {        
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Itemstock/index');
        $data['title']      = $this->input->post('add_item');
        $data['title_list'] = $this->input->post('recent_items');
        $this->form_validation->set_rules('item_id', $this->input->post('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('quantity', $this->input->post('quantity'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->input->post('item_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_photo', $this->lang->line('photo'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {

        } else {
            $date     = $this->input->post('date');
            $store_id = ($this->input->post('store_id')) ? $this->input->post('store_id') : null;
            $data     = array(
                'item_id'     => $this->input->post('item_id'),
                'symbol'      => $this->input->post('symbol'),
                'supplier_id' => $this->input->post('supplier_id'),
                'store_id'    => $store_id,
                'quantity'    => $this->input->post('symbol') . $this->input->post('quantity'),
                'date'        => $this->customlib->dateFormatToYYYYMMDD($date),
                'description' => $this->input->post('description'),
            );

            $insert_id = $this->itemstock_model->add($data);
            if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
                $fileInfo = pathinfo($_FILES["item_photo"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["item_photo"]["tmp_name"], "./uploads/inventory_items/" . $img_name);
                $data_img = array('id' => $insert_id, 'attachment' => 'uploads/inventory_items/' . $img_name);
                $this->itemstock_model->add($data_img);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('item_added_successfully') . '</div>');
            redirect('admin/itemstock/index');
        }
        $itemcategory         = $this->itemcategory_model->get();
        $data['itemcatlist']  = $itemcategory;
        $itemsupplier         = $this->itemsupplier_model->get();
        $data['itemsupplier'] = $itemsupplier;
        $itemstore            = $this->itemstore_model->get();
        $data['itemstore']    = $itemstore;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemstock/itemList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getitemstockDatatable()
    {
        $dt_response = $this->itemstock_model->getAllitemstockRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $key => $value) {

                $row = array();
                //====================================
                $action = "<div class='rowoptionview rowview-mt-19'>";
                $link   = "<a href='#' class='detail_popover'  data-toggle='popover' title=''>";
                $div    = "<div class='fee_detail_popover' style='display: none'>";
                

                if ($value->attachment) {
                    $action .= "<a href=" . base_url() . 'admin/itemstock/download/' . $value->attachment . " onclick='' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
                }

                if ($this->rbac->hasPrivilege('item_stock', 'can_edit')) {
                    $action .= "<a href='#' onclick='get_data(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('item_stock', 'can_delete')) {
                    $action .= "<a href='#' onclick='delete_record(" . $value->id . ")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('delete') . "'><i class='fa fa-trash'></i></a>";
                }

                //==============================
                $row[] = $link . $value->name . '</a>' . $div .  "</div>" . $action;
                $row[] = $value->item_category;
                $row[] = $value->item_supplier;
                $row[] = $value->item_store;
                if($value->date){
                $row[] = $this->customlib->YYYYMMDDTodateFormat($value->date);
                }else{
                $row[] = '';
                }
                $row[] = $value->description;
                $row[] = $value->quantity;

                $row[]     = $value->purchase_price;
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
        $this->form_validation->set_rules('item_id', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('purchase_price', $this->lang->line('purchase_price'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('quantity', $this->lang->line('quantity'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->lang->line('item_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('supplier_id', $this->lang->line('supplier'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_photo', $this->lang->line('item_photo'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1'             => form_error('item_id'),
                'e2'             => form_error('quantity'),
                'e3'             => form_error('item_category_id'),
                'item_photo'     => form_error('item_photo'),
                'purchase_price' => form_error('purchase_price'),
                'supplier_id'    => form_error('supplier_id'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date     = $this->customlib->dateFormatToYYYYMMDD($this->input->post('date'));
            $store_id = ($this->input->post('store_id')) ? $this->input->post('store_id') : null;
            $data     = array(
                'item_id'        => $this->input->post('item_id'),
                'symbol'         => $this->input->post('symbol'),
                'supplier_id'    => $this->input->post('supplier_id'),
                'purchase_price' => $this->input->post('purchase_price'),
                'store_id'       => $store_id,
                'quantity'       => $this->input->post('symbol') . $this->input->post('quantity'),
                'date'           => $date,
                'description'    => $this->input->post('description'),
            );

            $insert_id = $this->itemstock_model->add($data);
            if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
                $fileInfo = pathinfo($_FILES["item_photo"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["item_photo"]["tmp_name"], "./uploads/inventory_items/" . $img_name);
                $data_img = array('id' => $insert_id, 'attachment' => 'uploads/inventory_items/' . $img_name);
                $this->itemstock_model->add($data_img);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function update()
    {
        $this->form_validation->set_rules('item_id', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('quantity', $this->lang->line('quantity'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->lang->line('item_category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('supplier_id', $this->lang->line('supplier'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('purchase_price', $this->lang->line('purchase_price'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_photo', $this->lang->line('item_photo'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1'             => form_error('item_id'),
                'e2'             => form_error('quantity'),
                'e3'             => form_error('item_category_id'),
                'purchase_price' => form_error('purchase_price'),
                'item_photo'     => form_error('item_photo'),
                'supplier_id'    => form_error('supplier_id'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $store_id = ($this->input->post('store_id')) ? $this->input->post('store_id') : null;
            $updateid = $this->input->post("itemstockid");
            $date     = $this->input->post('date');
            $data     = array(
                'id'             => $updateid,
                'item_id'        => $this->input->post('item_id'),
                'symbol'         => $this->input->post('symbol'),
                'supplier_id'    => $this->input->post('supplier_id'),
                'store_id'       => $store_id,
                'quantity'       => $this->input->post('symbol') . $this->input->post('quantity'),
                'purchase_price' => $this->input->post('purchase_price'),
                'date'           => $this->customlib->dateFormatToYYYYMMDD($date),
                'description'    => $this->input->post('description'),
            );

            $insert_id = $this->itemstock_model->add($data);
            if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
                $fileInfo = pathinfo($_FILES["item_photo"]["name"]);
                $img_name = $updateid . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["item_photo"]["tmp_name"], "./uploads/inventory_items/" . $img_name);
                $data_img = array('id' => $updateid, 'attachment' => 'uploads/inventory_items/' . $img_name);
                $this->itemstock_model->add($data_img);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function download($file)
    {
        $this->load->helper('download');
        $filepath = "./uploads/inventory_items/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

    public function getItemByCategory()
    {
        $item_category_id = $this->input->get('item_category_id');
        $data             = $this->item_model->getItemByCategory($item_category_id);
        echo json_encode($data);
    }

    public function getItemunit()
    {
        $id   = $this->input->get('id');
        $data = $this->item_model->getItemunit($id);
        echo json_encode($data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('item_stock', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('fees_master_list');
        $this->itemstock_model->remove($id);
        redirect('admin/itemstock/index');
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
            $file_type         = $_FILES["item_photo"]['type'];
            $file_size         = $_FILES["item_photo"]["size"];
            $file_name         = $_FILES["item_photo"]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @filesize($_FILES['item_photo']['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array(strtolower($ext), $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_extension_not_allowed'));
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('error_file_uploading'));
                return false;
            }

            return true;
        }
        return true;
    }

    public function edit($id)
    {
        $data['id']           = $id;
        $item                 = $this->itemstock_model->get($id);
        $data['item']         = $item;
        $data['title_list']   = $this->lang->line('fees_master_list');
        $itemcategory         = $this->itemcategory_model->get();
        $data['itemcatlist']  = $itemcategory;
        $itemsupplier         = $this->itemsupplier_model->get();
        $data['itemsupplier'] = $itemsupplier;
        $itemstore            = $this->itemstore_model->get();
        $data['itemstore']    = $itemstore;
        $item["date"]         = $this->customlib->YYYYMMDDTodateFormat($item['date']);
        echo json_encode($item);
    }

    public function save_edit($id)
    {
        $this->form_validation->set_rules('quantity', $this->lang->line('quantity'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_id', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->lang->line('item_category'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'e1' => form_error('item_id'),
                'e2' => form_error('item_category_id'),
                'e3' => form_error('quantity'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $store_id = ($this->input->post('store_id')) ? $this->input->post('store_id') : null;
            $date     = $this->input->post('date');
            $data     = array(
                'id'          => $id,
                'item_id'     => $this->input->post('item_id'),
                'symbol'      => $this->input->post('symbol'),
                'supplier_id' => $this->input->post('supplier_id'),
                'store_id'    => $store_id,
                'quantity'    => $this->input->post('symbol') . $this->input->post('quantity'),
                'date'        => $this->customlib->dateFormatToYYYYMMDD($date),
                'description' => $this->input->post('description'),
            );

            $this->itemstock_model->add($data);

            if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
                $fileInfo = pathinfo($_FILES["item_photo"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["item_photo"]["tmp_name"], "./uploads/inventory_items/" . $img_name);
                $data_img = array('id' => $id, 'attachment' => 'uploads/inventory_items/' . $img_name);
                $this->itemstock_model->add($data_img);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function edit_new($id)
    {
        if (!$this->rbac->hasPrivilege('item_stock', 'can_edit')) {
            access_denied();
        }
        $data['title']        = $this->lang->line('edit_fees_master');
        $data['id']           = $id;
        $item                 = $this->itemstock_model->get($id);
        $data['item']         = $item;
        $data['title_list']   = $this->lang->line('fees_master_list');
        $item_result          = $this->itemstock_model->get();
        $data['itemlist']     = $item_result;
        $itemcategory         = $this->itemcategory_model->get();
        $data['itemcatlist']  = $itemcategory;
        $itemsupplier         = $this->itemsupplier_model->get();
        $data['itemsupplier'] = $itemsupplier;
        $itemstore            = $this->itemstore_model->get();
        $data['itemstore']    = $itemstore;
        $this->form_validation->set_rules('item_id', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('item_category_id', $this->lang->line('item_category'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/itemstock/itemEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $store_id = ($this->input->post('store_id')) ? $this->input->post('store_id') : null;
            $date     = $this->input->post('date');
            $data     = array(
                'id'          => $id,
                'item_id'     => $this->input->post('item_id'),
                'symbol'      => $this->input->post('symbol'),
                'supplier_id' => $this->input->post('supplier_id'),
                'store_id'    => $store_id,
                'quantity'    => $this->input->post('symbol') . $this->input->post('quantity'),
                'date'        => $this->customlib->dateFormatToYYYYMMDD($date),
                'description' => $this->input->post('description'),
            );

            $this->itemstock_model->add($data);

            if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
                $fileInfo = pathinfo($_FILES["item_photo"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["item_photo"]["tmp_name"], "./uploads/inventory_items/" . $img_name);
                $data_img = array('id' => $id, 'attachment' => 'uploads/inventory_items/' . $img_name);
                $this->itemstock_model->add($data_img);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('item_stock_updated_successfully') . '</div>');
            redirect('admin/itemstock/index');
        }
    }
}
