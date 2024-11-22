<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Expmedicine extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load("payroll");
        $this->search_type = $this->config->item('search_type_expiry');
        $this->load->library('datatables');
        $this->time_format = $this->customlib->getHospitalTimeFormat();
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Transport');
        $this->session->set_userdata('sub_menu', 'vehicle/index');
        $data['title']       = $this->lang->line('add_vehicle');
        $listVehicle         = $this->vehicle_model->get();
        $data['listVehicle'] = $listVehicle;
        $this->load->view('layout/header');
        $this->load->view('admin/vehicle/search', $data);
        $this->load->view('layout/footer');
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required');
        $this->form_validation->set_rules('vehicle_model', $this->lang->line('vehicle_model'), 'required');
        $this->form_validation->set_rules('vehicle_type', $this->lang->line('vehicle_type'), 'required');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'vehicle_no'    => form_error('vehicle_no'),
                'vehicle_model' => form_error('vehicle_model'),
                'vehicle_type'  => form_error('vehicle_type'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $manufacture_year = $this->input->post('manufacture_year');
            $data             = array(
                'vehicle_no'     => $this->input->post('vehicle_no'),
                'vehicle_model'  => $this->input->post('vehicle_model'),
                'driver_name'    => $this->input->post('driver_name'),
                'driver_licence' => $this->input->post('driver_licence'),
                'driver_contact' => $this->input->post('driver_contact'),
                'vehicle_type'   => $this->input->post('vehicle_type'),
                'note'           => $this->input->post('note'),
            );
            ($manufacture_year != "") ? $data['manufacture_year'] = $manufacture_year : '';
            $this->vehicle_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_view')) {
            access_denied();
        }
        $id          = $this->input->post("id");
        $listVehicle = $this->vehicle_model->getDetails($id);
        echo json_encode($listVehicle);
    }

    public function update()
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required');
        $this->form_validation->set_rules('vehicle_model', $this->lang->line('vehicle_model'), 'required');
        $this->form_validation->set_rules('vehicle_type', $this->lang->line('vehicle_type'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'vehicle_no'    => form_error('vehicle_no'),
                'vehicle_model' => form_error('vehicle_model'),
                'vehicle_type'  => form_error('vehicle_type'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id               = $this->input->post('id');
            $manufacture_year = $this->input->post('manufacture_year');
            $data             = array(
                'id'             => $id,
                'vehicle_no'     => $this->input->post('vehicle_no'),
                'vehicle_model'  => $this->input->post('vehicle_model'),
                'driver_name'    => $this->input->post('driver_name'),
                'driver_licence' => $this->input->post('driver_licence'),
                'driver_contact' => $this->input->post('driver_contact'),
                'vehicle_type'   => $this->input->post('vehicle_type'),
                'note'           => $this->input->post('note'),
            );
            ($manufacture_year != "") ? $data['manufacture_year'] = $manufacture_year : '';
            $this->vehicle_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('ambulance', 'can_delete')) {
            access_denied();
        }
        $this->vehicle_model->remove($id);
        redirect('admin/Vehicle/search');
    }

    public function addCallAmbulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('patient_name', $this->lang->line('patient_name'), 'required');
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_model'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'vehicle_no'   => form_error('vehicle_no'),
                'date'         => form_error('date'),
                'amount'       => form_error('amount'),
                'patient_name' => form_error('patient_name'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $date = $this->input->post("date");
            $data = array(
                'patient_name' => $this->input->post('patient_name'),
                'contact_no'   => $this->input->post('contact_no'),
                'address'      => $this->input->post('address'),
                'vehicle_no'   => $this->input->post('vehicle_no'),
                'driver'       => $this->input->post('driver'),
                'amount'       => $this->input->post('amount'),
                'date'         => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
            );
            $this->vehicle_model->addCallAmbulance($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function getCallAmbulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Transport');
        $this->session->set_userdata('sub_menu', 'vehicle/getcallambulance');
        $data['title']       = $this->lang->line('add_vehicle');
        $listCall            = $this->vehicle_model->getCallAmbulance();
        $vehiclelist         = $this->vehicle_model->get();
        $data['listCall']    = $listCall;
        $data['vehiclelist'] = $vehiclelist;
        $this->load->view('layout/header');
        $this->load->view('admin/vehicle/ambulance_call', $data);
        $this->load->view('layout/footer');
    }

    public function editCall()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_view')) {
            access_denied();
        }
        $id       = $this->input->post("id");
        $listCall = $this->vehicle_model->getCallDetails($id);
        $date     = $this->customlib->YYYYMMDDHisTodateFormat($listCall['date'], $this->time_format);
        $listCall["date"] = $date;
        echo json_encode($listCall);
    }

    public function updateCallAmbulance()
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('patient_name', $this->lang->line('patient_name'), 'required');
        $this->form_validation->set_rules('vehicle_no', $this->lang->line('vehicle_number'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'patient_name' => form_error('patient_name'),
                'vehicle_no'   => form_error('vehicle_no'),
                'date'         => form_error('date'),
                'amount'       => form_error('amount'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $id   = $this->input->post('id');
            $date = $this->input->post('date');

            $data = array(
                'id'           => $id,
                'patient_name' => $this->input->post('patient_name'),
                'contact_no'   => $this->input->post('contact_no'),
                'address'      => $this->input->post('address'),
                'vehicle_no'   => $this->input->post('vehicle_no'),
                'driver'       => $this->input->post('driver_name'),
                'amount'       => $this->input->post('amount'),
                'date'         => $this->customlib->dateFormatToYYYYMMDDHis($date, $this->time_format),
            );
            $this->vehicle_model->addCallAmbulance($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function deleteCallAmbulance($id)
    {
        if (!$this->rbac->hasPrivilege('ambulance_call', 'can_delete')) {
            access_denied();
        }
        $this->vehicle_model->delete($id);
        redirect('admin/Vehicle/getcallambulance');
    }

    public function getVehicleDetail()
    {
        $id     = $this->input->post('id');
        $result = $this->vehicle_model->getDetails($id);
        echo json_encode($result);
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
                'search_type'       => $this->input->post('search_type'),
                'date_from'         => $this->input->post('date_from'),
                'date_to'           => $this->input->post('date_to'),
                'supplier'          => $this->input->post('supplier'),
                'medicine_category' => $this->input->post('medicine_category'),
            );

            $json_array = array('status' => 'success', 'error' => '', 'param' => $param, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($json_array);
    }

    public function expmedicinereport()
    {
        if (!$this->rbac->hasPrivilege('expiry_medicine_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/pharmacy');
        $this->session->set_userdata('subsub_menu', 'reports/expmedicine/expmedicinereport');

        $data["searchlist"]       = $this->search_type;
        $supplierCategory         = $this->medicine_category_model->getSupplierCategory();
        $data["supplierCategory"] = $supplierCategory;
        $medicineCategory         = $this->medicine_category_model->getMedicineCategory();
        $data["medicineCategory"] = $medicineCategory;
        $this->load->view('layout/header');
        $this->load->view('admin/expmedicine/expmedicinereport', $data);
        $this->load->view('layout/footer');
    }

    public function expmedicinereports()
    {
        $search['search_type']   = $this->input->post('search_type');
        $search['collect_staff'] = $this->input->post('collect_staff');
        $search['date_from']     = $this->input->post('date_from');
        $search['date_to']       = $this->input->post('date_to');
        $condition['medicine_category'] = $this->input->post('medicine_category');
        $condition['supplier']          = $this->input->post('supplier');
        $start_date                     = '';
        $end_date                       = '';

        if (isset($search['search_type']) && $search['search_type'] != '') {
            $dates               = $this->customlib->get_betweendate($search['search_type']);
            $data['search_type'] = $search['search_type'];
        } else {
            $dates = $this->customlib->get_betweendate('this_year');
        }

        $start_date = $dates['from_date'];
        $end_date   = $dates['to_date'];
        $reportdata = $this->report_model->expmedicinereportsRecords($start_date, $end_date, $condition);

        $reportdata = json_decode($reportdata);

        $dt_data = array();
        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {
                $row   = array();
                $row[] = $value->medicine_name;
                $row[] = $value->batch_no;
                $row[] = $value->medicine_company;
                $row[] = $value->medicine_category;
                $row[] = $value->medicine_group;
                $row[] = $value->supplier;
                $row[]     = $this->customlib->YYYYMMDDTodateFormat($value->expiry);
                $row[]     = $value->available_quantity;
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
