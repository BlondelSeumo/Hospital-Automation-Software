<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payroll extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->config->load("mailsms");
        $this->notification            = $this->config->item('notification');
        $this->notificationurl         = $this->config->item('notification_url');
        $this->patient_notificationurl = $this->config->item('patient_notification_url');
        $this->config->load("payroll");
        $this->config->load('image_valid');
        $this->load->library('mailsmsconf');
        $this->config_attendance = $this->config->item('attendence');
        $this->staff_attendance  = $this->config->item('staffattendance');
        $this->payment_mode      = $this->config->item('payment_mode');
        $this->search_type       = $this->config->item('search_type');
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->payroll_status = $this->config->item('payroll_status');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/payroll');
        $data["staff_id"]      = "";
        $data["name"]          = "";
        $data["month"]         = date("F", strtotime("-1 month"));
        $data["year"]          = date("Y");
        $data["present"]       = 0;
        $data["absent"]        = 0;
        $data["late"]          = 0;
        $data["half_day"]      = 0;
        $data["holiday"]       = 0;
        $data["leave_count"]   = 0;
        $data["alloted_leave"] = 0;
        $data["basic"]         = 0;
        $data["payment_mode"]  = $this->payment_mode;
        $user_type             = $this->staff_model->getStaffRole();
        $data['classlist']     = $user_type;
        $data['monthlist']     = $this->customlib->getMonthDropdown();
        $submit                = $this->input->post("search");

        if (isset($submit) && $submit == "search") {

            $month    = $this->input->post("month");
            $year     = $this->input->post("year");
            $emp_name = $this->input->post("name");
            $role     = $this->input->post("role");
            $searchEmployee = $this->payroll_model->searchEmployee($month, $year, $emp_name, $role);
            $data["resultlist"] = $searchEmployee;
            $data["name"]       = $emp_name;
            $data["month"]      = $month;
            $data["year"]       = $year;
        }
 
        $data["payroll_status"] = $this->payroll_status;
        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/stafflist", $data);
        $this->load->view("layout/footer", $data);
    }

    public function create($month, $year, $id)
    {
        $data["staff_id"]        = "";
        $data["basic"]           = "";
        $data["name"]            = "";
        $data["month"]           = "";
        $data["year"]            = "";
        $data["present"]         = 0;
        $data["absent"]          = 0;
        $data["late"]            = 0;
        $data["half_day"]        = 0;
        $data["holiday"]         = 0;
        $data["leave_count"]     = 0;
        $data["alloted_leave"]   = 0;
        $user_type               = $this->staff_model->getStaffRole();
        $data['classlist']       = $user_type;
        $date                    = $year . "-" . $month;
        $searchEmployee          = $this->payroll_model->searchEmployeeById($id);
        $data['result']          = $searchEmployee;
        $data["month"]           = $month;
        $data["year"]            = $year;
        $alloted_leave           = $this->staff_model->alloted_leave($id);
        $newdate                 = date('Y-m-d', strtotime($date . " +1 month"));
        $data['monthAttendance'] = $this->monthAttendance($newdate, 3, $id);
        $data['monthLeaves']     = $this->monthLeaves($newdate, 3, $id);
        $data["attendanceType"]  = $this->staffattendancemodel->getStaffAttendanceType();
        $data["alloted_leave"]   = $alloted_leave[0]["alloted_leave"];
        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/create", $data);
        $this->load->view("layout/footer", $data);
    }

    public function edit($id)
    {
        $data["staff_id"]         = "";
        $data["basic"]            = "";
        $data["name"]             = "";
        $data["month"]            = "";
        $data["year"]             = "";
        $data["present"]          = 0;
        $data["absent"]           = 0;
        $data["late"]             = 0;
        $data["half_day"]         = 0;
        $data["holiday"]          = 0;
        $data["leave_count"]      = 0;
        $data["alloted_leave"]    = 0;
        $user_type                = $this->staff_model->getStaffRole();
        $employee_payroll         = $this->payroll_model->getPayslip($id);
        $data['employee_payroll'] = $employee_payroll;
        $data['classlist']        = $user_type;
        $searchEmployee           = $this->payroll_model->searchEmployeeById($employee_payroll['staff_id']);
        $date                     = $employee_payroll['year'] . "-" . $employee_payroll['month'];
        $data['result']           = $searchEmployee;
        $data["month"]            = $employee_payroll['month'];
        $data["year"]             = $employee_payroll['year'];

        $data["earnings"]   = $this->payroll_model->getAllowance($id, 'positive');
        $data["deductions"] = $this->payroll_model->getAllowance($id, 'negative');

        $alloted_leave           = $this->staff_model->alloted_leave($employee_payroll['staff_id']);
        $newdate                 = date('Y-m-d', strtotime($date . " +1 month"));
        $data['monthAttendance'] = $this->monthAttendance($newdate, 3, $employee_payroll['staff_id']);
        $data['monthLeaves']     = $this->monthLeaves($newdate, 3, $employee_payroll['staff_id']);
        $data["attendanceType"]  = $this->staffattendancemodel->getStaffAttendanceType();
        $data["alloted_leave"]   = $alloted_leave[0]["alloted_leave"];
        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/edit", $data);
        $this->load->view("layout/footer", $data);
    }

    public function monthAttendance($st_month, $no_of_months, $emp)
    {
        $record = array();
        for ($i = 1; $i <= $no_of_months; $i++) {

            $r     = array();
            $month = date('m', strtotime($st_month . " -$i month"));
            $year  = date('Y', strtotime($st_month . " -$i month"));

            foreach ($this->staff_attendance as $att_key => $att_value) {

                $s           = $this->payroll_model->count_attendance_obj($month, $year, $emp, $att_value);
                $r[$att_key] = $s;
            }

            $record['01-' . $month . '-' . $year] = $r;
        }
        return $record;
    }

    public function monthLeaves($st_month, $no_of_months, $emp)
    {
        $record = array();
        for ($i = 1; $i <= $no_of_months; $i++) {

            $r           = array();
            $month       = date('m', strtotime($st_month . " -$i month"));
            $year        = date('Y', strtotime($st_month . " -$i month"));
            $leave_count = $this->staff_model->count_leave($month, $year, $emp);
            if (!empty($leave_count["tl"])) {
                $l = $leave_count["tl"];
            } else {
                $l = "0";
            }

            $record[$month] = $l;
        }

        return $record;
    }

    public function editpayroll()
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_add')) {
            access_denied();
        }

        $id              = $this->input->post("id");
        $basic           = $this->input->post("basic");
        $total_allowance = $this->input->post("total_allowance");
        $total_deduction = $this->input->post("total_deduction");
        $net_salary      = $this->input->post("net_salary");
        $status          = $this->input->post("status");
        $staff_id        = $this->input->post("staff_id");
        $month           = $this->input->post("month");
        $name            = $this->input->post("name");
        $year            = $this->input->post("year");
        $tax             = $this->input->post("tax_percent");
        $leave_deduction = $this->input->post("leave_deduction");
        $this->form_validation->set_rules('net_salary', $this->lang->line('net_salary'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $this->create($month, $year, $staff_id);
        } else {

            $data = array(
                'id'              => $id,
                'staff_id'        => $staff_id,
                'basic'           => $basic,
                'total_allowance' => $total_allowance,
                'total_deduction' => $total_deduction,
                'net_salary'      => $net_salary,
                'payment_date'    => date("Y-m-d"),
                'status'          => $status,
                'month'           => $month,
                'year'            => $year,
                'tax'             => $tax,
                'leave_deduction' => '0',
                'generated_by'    => $this->customlib->getLoggedInUserID(),
            );

            $checkForUpdate = $this->payroll_model->checkPayslip($month, $year, $staff_id);
            if (!$checkForUpdate) {
                $insert_id         = $this->payroll_model->createPayslip($data);
                $payslipid         = $insert_id;
                $allowance_type    = $this->input->post("allowance_type");
                $deduction_type    = $this->input->post("deduction_type");
                $allowance_prev_id = $this->input->post("allowance_prev_id");
                $deduction_prev_id = $this->input->post("deduction_prev_id");
                $allowance_amount  = $this->input->post("allowance_amount");
                $deduction_amount  = $this->input->post("deduction_amount");

                if (!empty($allowance_type)) {

                    $i                        = 0;
                    $insert_payslip_allowance = array();
                    $update_payslip_allowance = array();
                    foreach ($allowance_type as $key => $all) {
                        if ($allowance_prev_id[$i] != 0) {
                            $update_payslip_allowance[] = array(
                                'id'               => $allowance_prev_id[$i],
                                'staff_payslip_id' => $payslipid,
                                'allowance_type'   => $allowance_type[$i],
                                'amount'           => $allowance_amount[$i],
                                'staff_id'         => $staff_id,
                                'cal_type'         => "positive",
                            );
                        } else {
                            $insert_payslip_allowance[] = array(
                                'staff_payslip_id' => $payslipid,
                                'allowance_type'   => $allowance_type[$i],
                                'amount'           => $allowance_amount[$i],
                                'staff_id'         => $staff_id,
                                'cal_type'         => "positive",
                            );
                        }

                        $i++;
                    }

                    $insert_payslip_allowance = $this->payroll_model->update_allowance($insert_payslip_allowance, $update_payslip_allowance, $allowance_prev_id, $payslipid, 'positive');
                } else {

                    $insert_payslip_allowance = $this->payroll_model->update_allowance([], [], [0], $payslipid, 'positive');
                }

                if (!empty($deduction_type)) {
                    $j                        = 0;
                    $insert_payslip_allowance = array();
                    $update_payslip_allowance = array();

                    foreach ($deduction_type as $key => $type) {
                        if ($deduction_prev_id[$j] != 0) {
                            $update_payslip_allowance[] = array(
                                'id'               => $deduction_prev_id[$j],
                                'staff_payslip_id' => $payslipid,
                                'allowance_type'   => $deduction_type[$j],
                                'amount'           => $deduction_amount[$j],
                                'staff_id'         => $staff_id,
                                'cal_type'         => "negative",
                            );
                        } else {
                            $insert_payslip_allowance[] = array(
                                'staff_payslip_id' => $payslipid,
                                'allowance_type'   => $deduction_type[$j],
                                'amount'           => $deduction_amount[$j],
                                'staff_id'         => $staff_id,
                                'cal_type'         => "negative",
                            );
                        }
                        $j++;
                    }

                    $insert_payslip_allowance = $this->payroll_model->update_allowance($insert_payslip_allowance, $update_payslip_allowance, $deduction_prev_id, $payslipid, 'negative');
                } else {
                    $insert_payslip_allowance = $this->payroll_model->update_allowance([], [], [0], $payslipid, 'negative');
                }

                $event_data = array(
                    'role'         => $this->input->post('role'),
                    'month'        => $month,
                    'year'         => $year,
                    'basic_salary' => number_format((float) $basic, 2, '.', ''),
                    'earning'      => $total_allowance,
                    'deduction'    => $total_deduction,
                    'gross_salary' => $this->input->post('gross_salary'),
                    'tax_amount'   => $tax,
                    'net_salary'   => $net_salary,
                );

                $this->system_notification->send_system_notification('staff_generate_payroll', $event_data);

                redirect('admin/payroll');
            } else {

                $this->session->set_flashdata("msg", "<div class='alert alert-warning'>" . $this->lang->line('payslip_not_generated') . "</div>");

                redirect('admin/payroll');
            }
        }
    }

    public function payslip()
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_add')) {
            access_denied();
        }

        $basic           = $this->input->post("basic");
        $total_allowance = $this->input->post("total_allowance");
        $total_deduction = $this->input->post("total_deduction");
        $net_salary      = $this->input->post("net_salary");
        $status          = $this->input->post("status");
        $staff_id        = $this->input->post("staff_id");
        $month           = $this->input->post("month");
        $name            = $this->input->post("name");
        $year            = $this->input->post("year");
        $tax             = $this->input->post("tax_percent");
        $leave_deduction = $this->input->post("leave_deduction");
        $this->form_validation->set_rules('net_salary', $this->lang->line('net_salary'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $this->create($month, $year, $staff_id);
        } else {

            $data = array('staff_id' => $staff_id,
                'basic'                  => $basic,
                'total_allowance'        => $total_allowance,
                'total_deduction'        => $total_deduction,
                'net_salary'             => $net_salary,
                'payment_date'           => date("Y-m-d"),
                'status'                 => $status,
                'month'                  => $month,
                'year'                   => $year,
                'tax'                    => $tax,
                'leave_deduction'        => '0',
                'generated_by'           => $this->customlib->getLoggedInUserID(),
            );
            $checkForUpdate = $this->payroll_model->checkPayslip($month, $year, $staff_id);
            if ($checkForUpdate == true) {
                $insert_id        = $this->payroll_model->createPayslip($data);
                $payslipid        = $insert_id;
                $allowance_type   = $this->input->post("allowance_type");
                $deduction_type   = $this->input->post("deduction_type");
                $allowance_amount = $this->input->post("allowance_amount");
                $deduction_amount = $this->input->post("deduction_amount");
                if (!empty($allowance_type)) {

                    $i = 0;
                    foreach ($allowance_type as $key => $all) {

                        $all_data = array('staff_payslip_id' => $payslipid,
                            'allowance_type'                     => $allowance_type[$i],
                            'amount'                             => $allowance_amount[$i],
                            'staff_id'                           => $staff_id,
                            'cal_type'                           => "positive",
                        );

                        $insert_payslip_allowance = $this->payroll_model->add_allowance($all_data);

                        $i++;
                    }
                }

                if (!empty($deduction_type)) {
                    $j = 0;
                    foreach ($deduction_type as $key => $type) {

                        $type_data = array('staff_payslip_id' => $payslipid,
                            'allowance_type'                      => $deduction_type[$j],
                            'amount'                              => $deduction_amount[$j],
                            'staff_id'                            => $staff_id,
                            'cal_type'                            => "negative",
                        );

                        $insert_payslip_allowance = $this->payroll_model->add_allowance($type_data);

                        $j++;
                    }
                }

                $event_data = array(
                    'role'         => $this->input->post('role'),
                    'month'        => $month,
                    'year'         => $year,
                    'basic_salary' => number_format((float) $basic, 2, '.', ''),
                    'earning'      => $total_allowance,
                    'deduction'    => $total_deduction,
                    'gross_salary' => $this->input->post('gross_salary'),
                    'tax_amount'   => $tax,
                    'net_salary'   => $net_salary,
                );

                $this->system_notification->send_system_notification('staff_generate_payroll', $event_data);

                redirect('admin/payroll');
            } else {

                $this->session->set_flashdata("msg", "<div class='alert alert-warning'>" . $this->lang->line('payslip_already_generated') . "</div>");
                redirect('admin/payroll');
            }
        }
    }

    public function search($month, $year, $role = '')
    {
        $user_type              = $this->staff_model->getStaffRole();
        $data['classlist']      = $user_type;
        $data['monthlist']      = $this->customlib->getMonthDropdown();
        $searchEmployee         = $this->payroll_model->searchEmployee($month, $year, $emp_name = '', $role);
        $data["resultlist"]     = $searchEmployee;
        $data["name"]           = $emp_name;
        $data["month"]          = $month;
        $data["year"]           = $year;
        $data["payroll_status"] = $this->payroll_status;
        $data["resultlist"]     = $searchEmployee;
        $data["payment_mode"]   = $this->payment_mode;
        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/stafflist", $data);
        $this->load->view("layout/footer", $data);
    }

    public function payrollNotification($staff_id, $role, $month, $amount, $staffname, $url)
    {
        $notification      = $this->notification;
        $notification_desc = $notification["salary_paid"];
        $desc              = str_replace(array('<amount>', '<month>', '<staffname>', '<url>'), array($amount, $month, $staffname, $url), $notification_desc);

        if (!empty($staff_id)) {

            $notification_data = array('notification_title' => $this->lang->line('notification_salary_paid'),
                'notification_desc'                             => $desc,
                'notification_for'                              => $role,
                'notification_type'                             => 'salary',
                'receiver_id'                                   => $staff_id,
                'date'                                          => date("Y-m-d H:i:s"),
                'is_active'                                     => 'yes',
            );
            $this->notification_model->addSystemNotification($notification_data);
            $admin_notification_data = array('notification_title' => $this->lang->line('notification_salary_paid'),
                'notification_desc'                                   => $desc,
                'notification_for'                                    => 'Super Admin',
                'notification_type'                                   => 'salary',
                'receiver_id'                                         => '',
                'date'                                                => date("Y-m-d H:i:s"),
                'is_active'                                           => 'yes',
            );
            $this->notification_model->addSystemNotification($admin_notification_data);
        }
    }

    public function paymentRecord()
    {
        $month          = $this->input->get_post("month");
        $year           = $this->input->get_post("year");
        $id             = $this->input->get_post("staffid");
        $searchEmployee = $this->payroll_model->searchPayment($id, $month, $year);
        $data['result'] = $searchEmployee;
        $data["month"]  = $month;
        $data["year"]   = $year;
        echo json_encode($data);
    }

    public function paymentStatus($status)
    {
        $id          = $this->input->get('id');
        $updateStaus = $this->payroll_model->updatePaymentStatus($status, $id);
        redirect("admin/payroll");
    }

    public function paymentSuccess()
    {
        $pay_id          = $this->input->post("paymentid");
        $payment_mode    = $this->input->post("payment_mode");
        $date            = $this->input->post("payment_date");
        $payment_date    = $this->customlib->dateFormatToYYYYMMDD($date);
        $remark          = $this->input->post("remarks");
        $paymentmonth    = $this->input->post("paymentmonth");
        $amount          = $this->input->post("amount");
        $status          = 'paid';
        $staff_id        = $this->input->post("staff_id");
        $notificationurl = $this->notificationurl;
        $url_link        = $notificationurl["salary"];
        $url             = base_url() . $url_link . '/' . $staff_id . '/' . $pay_id;
        $staffname       = '';
        if ($staff_id) {
            $result    = $this->staff_model->getstaff($staff_id);
            $staffname = $result['name'] . " " . $result['surname'];
        }

        $staff_role = $this->input->post("staff_role");
        $payslipid  = $this->input->post("paymentid");
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_date', $this->lang->line('payment_date'), 'trim|required|xss_clean');
        
        
        if ($this->input->post("payment_mode") == "Cheque") {
            $this->form_validation->set_rules('cheque_no', $this->lang->line('cheque_no'), 'trim|required');
            $this->form_validation->set_rules('cheque_date', $this->lang->line('cheque_date'), 'trim|required');
            $this->form_validation->set_rules('document', $this->lang->line("document"), 'callback_handle_doc_upload[document]');
        }
        if ($this->form_validation->run() == false) {

            $msg = array(
                'payment_date' => form_error('payment_date'),
                'payment_mode' => form_error('payment_mode'),
                'cheque_no'    => form_error('cheque_no'),
                'cheque_date'  => form_error('cheque_date'),
                'document'     => form_error('document'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data            = array('payment_mode' => $payment_mode, 'payment_date' => $payment_date, 'remark' => $remark, 'status' => $status);
            $attachment      = "";
            $attachment_name = "";
            if (isset($_FILES["document"]) && !empty($_FILES['document']['name'])) {
                $fileInfo        = pathinfo($_FILES["document"]["name"]);
                $attachment      = uniqueFileName() . '.' . $fileInfo['extension'];
                $attachment_name = $_FILES["document"]["name"];
                move_uploaded_file($_FILES["document"]["tmp_name"], "./uploads/payslip_document/" . $attachment);
            }

            if ($this->input->post('payment_mode') == "Cheque") {
                $data['cheque_date']     = $this->customlib->dateFormatToYYYYMMDD($this->input->post('cheque_date'));
                $data['cheque_no']       = $this->input->post('cheque_no');
                $data['attachment']      = $attachment;
                $data['attachment_name'] = $attachment_name;
            }

            $this->payroll_model->paymentSuccess($data, $payslipid);
            $this->payrollNotification($staff_id, $staff_role, $paymentmonth, $amount, $staffname, $url);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

            $event_data = array(
                'staff'          => $this->input->post('emp_name'),
                'payment_amount' => $this->input->post('amount'),
                'month'          => $this->input->post('paymentmonth'),
                'year'           => $this->input->post('paymentyear'),
                'payment_date'   => $this->customlib->YYYYMMDDTodateFormat($payment_date),
                'payment_mode'   => $this->input->post('payment_mode'),
            );

            $this->system_notification->send_system_notification('add_payroll_payment', $event_data);
        }
        echo json_encode($array);
    }

    public function payslipView()
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_view')) {
            access_denied();
        }
        $data["payment_mode"]       = $this->payment_mode;
        $setting_result             = $this->setting_model->get();
        $data['settinglist']        = $setting_result[0];
        $data['print_details']      = $this->printing_model->get('', 'payslip');
        $id                         = $this->input->post("payslipid");
        $result                     = $this->payroll_model->getPayslip($id);
        $allowance                  = $this->payroll_model->getAllowance($result["id"]);
        $data["allowance"]          = $allowance;
        $positive_allowance         = $this->payroll_model->getAllowance($result["id"], "positive");
        $data["positive_allowance"] = $positive_allowance;
        $negative_allowance         = $this->payroll_model->getAllowance($result["id"], "negative");
        $data["negative_allowance"] = $negative_allowance;
        $data["result"]             = $result;
        if (!empty($result)) {
            $this->load->view("admin/payroll/payslipview", $data);
        } else {
            echo $this->lang->line('no_record_found');
        }
    }

    public function payslippdf()
    {
        $setting_result             = $this->setting_model->get();
        $data['settinglist']        = $setting_result[0];
        $id                         = 15;
        $result                     = $this->payroll_model->getPayslip($id);
        $allowance                  = $this->payroll_model->getAllowance($result["id"]);
        $data["allowance"]          = $allowance;
        $positive_allowance         = $this->payroll_model->getAllowance($result["id"], "positive");
        $data["positive_allowance"] = $positive_allowance;
        $negative_allowance         = $this->payroll_model->getAllowance($result["id"], "negative");
        $data["negative_allowance"] = $negative_allowance;
        $data["result"]             = $result;
        $this->load->view("admin/payroll/payslippdf", $data);
    }

    public function payrollreport()
    {
        if (!$this->rbac->hasPrivilege('payroll_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/human_resource');
        $this->session->set_userdata('subsub_menu', 'reports/human_resource/payrollreport');

        $month                = $this->input->post("month");
        $year                 = $this->input->post("year");
        $role                 = $this->input->post("role");
        $data["month"]        = $month;
        $data["year"]         = $year;
        $data["role_select"]  = $role;
        $data['monthlist']    = $this->customlib->getMonthDropdown();
        $data['yearlist']     = $this->payroll_model->payrollYearCount();
        $staffRole            = $this->staff_model->getStaffRole();
        $data["role"]         = $staffRole;
        $data["payment_mode"] = $this->payment_mode;

        $this->form_validation->set_rules('year', 'Year', 'trim|required|xss_clean');
        if ($this->form_validation->run() == true) {

            $result         = $this->payroll_model->getpayrollReport($month, $year, $role);
            $data["result"] = $result;
        }
        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/payrollreport", $data);
        $this->load->view("layout/footer", $data);
    }

    public function deletepayroll($payslipid, $month, $year, $role = '')
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_delete')) {
            access_denied();
        }
        if (!empty($payslipid)) {
            $this->payroll_model->deletePayslip($payslipid);
        }
        redirect('admin/payroll/search/' . $month . "/" . $year . "/" . $role);
    }

    public function revertpayroll($payslipid, $month, $year, $role = '')
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_delete')) {
            access_denied();
        }

        if (!empty($payslipid)) {
            $this->payroll_model->revertPayslipStatus($payslipid);
        }

        redirect('admin/payroll/search/' . $month . "/" . $year . "/" . $role);
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

    public function payrollreports()
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
        $reportdata       = $this->report_model->payrollreportsRecord($start_date, $end_date);
        $reportdata       = json_decode($reportdata);
        $dt_data          = array();
        $total_basic      = 0;
        $total_allowance  = 0;
        $total_deduction  = 0;
        $total_gross      = 0;
        $total_tax        = 0;
        $total_net        = 0;
        $total_tax_amount = 0;

        if (!empty($reportdata->data)) {
            foreach ($reportdata->data as $key => $value) {

                $total_basic += $value->basic;
                $total_allowance += $value->total_allowance;
                $total_deduction += $value->total_deduction;
                $total_gross += ($value->basic + $value->total_allowance - $value->total_deduction);
                $total_tax += $value->tax;
                $gross_salery = $value->basic + $value->total_allowance - $value->total_deduction;
                $tax_amount   = calculatePercent($gross_salery, $value->tax);

                $total_tax_amount += $tax_amount;
                $total_net += $gross_salery - $tax_amount;

                $row       = array();
                $row[]     = composeStaffNameByString($value->name, $value->surname, $value->employee_id);
                $row[]     = $value->user_type;
                $row[]     = $value->designation;
                $row[]     = $this->lang->line($value->month);
                $row[]     = $value->year;
                $row[]     = $this->customlib->YYYYMMDDTodateFormat($value->payment_date);
                $row[]     = $value->id;
                $row[]     = $value->basic;
                $row[]     = $value->total_allowance;
                $row[]     = $value->total_deduction;
                $row[]     = (number_format($value->basic + $value->total_allowance - $value->total_deduction, 2, '.', ''));
                $row[]     = $tax_amount . ' (' . $value->tax . '%)';
                $row[]     = number_format($gross_salery - $tax_amount, 2, '.', '');
                $dt_data[] = $row;
            }
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('total_amount') . "</b>" . ':';
            $footer_row[] = "<b>" . $this->customlib->getHospitalCurrencyFormat() . (number_format($total_basic, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $this->customlib->getHospitalCurrencyFormat() . (number_format($total_allowance, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $this->customlib->getHospitalCurrencyFormat() . (number_format($total_deduction, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $this->customlib->getHospitalCurrencyFormat() . (number_format($total_gross, 2, '.', '')) . "<br/>";
            $footer_row[] = "<b>" . $this->customlib->getHospitalCurrencyFormat() . number_format($total_tax_amount, 2, '.', '') . " (" . (number_format($total_tax, 2, '.', '')) . "%)<br/>";
            $footer_row[] = "<b>" . $this->customlib->getHospitalCurrencyFormat() . (number_format($total_net, 2, '.', '')) . "<br/>";
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($reportdata->draw),
            "recordsTotal"    => intval($reportdata->recordsTotal),
            "recordsFiltered" => intval($reportdata->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function payrollsearch()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/human_resource');
        $this->session->set_userdata('subsub_menu', 'reports/human_resource/payrollsearch');

        $search_type         = "";
        $data["searchlist"]  = $this->search_type;
        $data["search_type"] = $search_type;
        $this->load->view('layout/header');
        $this->load->view('admin/payroll/payrollsearch', $data);
        $this->load->view('layout/footer');
    }

    public function download($payslip_id)
    {
        $payslip = $this->payroll_model->payslipdoc($payslip_id);
        $this->load->helper('download');
        $filepath    = "./uploads/payslip_document/" . $payslip->attachment;
        $report_name = $payslip->attachment_name;
        $data        = file_get_contents($filepath);
        force_download($report_name, $data);
    }

    /**
     * This function is used to validate document for upload
     **/
    public function handle_doc_upload($str, $var)
    {
        $image_validate = $this->config->item('file_validate');
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = $image_validate["allowed_extension"];
            $allowed_mime_type = $image_validate["allowed_mime_type"];
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if ($files = filesize($_FILES[$var]['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('file_type_extension_error_uploading_document'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('extension_error_while_uploading_document'));
                    return false;
                }
                if ($file_size > 2097152) {
                    $this->form_validation->set_message('handle_doc_upload', $this->lang->line('file_size_shoud_be_less_than') . "2MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_doc_upload', $this->lang->line('error_while_uploading_document'));
                return false;
            }

            return true;
        }
        return true;
    }
}
