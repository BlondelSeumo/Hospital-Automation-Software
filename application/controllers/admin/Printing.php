<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Printing extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
    }

    public function opdpresprinting()
    {		
		if (!$this->rbac->hasPrivilege('opd_prescription_print_header_footer', 'can_view')) { 
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/opdpresprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'opdpre');
        $data["function_name"] = 'index';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/opdpresprinting', $data);
        $this->load->view('layout/footer');
    }

    public function ipdprinting()
    {
        if (!$this->rbac->hasPrivilege('ipd_bill_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/ipdprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'ipd');
        $data["function_name"] = 'ipdprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/ipdprinting', $data);
        $this->load->view('layout/footer');
    }

    public function billprinting()
    {
        if (!$this->rbac->hasPrivilege('ipd_bill_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/billprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'bill');
        $data["function_name"] = 'billprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/billprinting', $data);
        $this->load->view('layout/footer');
    }

    public function summaryprinting()
    {
        if (!$this->rbac->hasPrivilege('discharge_summary_print_header_footer', 'can_view')) { 
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/summaryprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'discharge_card');
        $data["function_name"] = 'summaryprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/summaryprinting', $data);
        $this->load->view('layout/footer');
    }

    public function opdprinting()
    {
        if (!$this->rbac->hasPrivilege('opd_bill_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/opdprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'opd');
        $data["function_name"] = 'opdprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/opdprinting', $data);
        $this->load->view('layout/footer');
    }

    public function ipdpresprinting()
    {
        if (!$this->rbac->hasPrivilege('ipd_prescription_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/ipdpresprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'ipdpres');
        $data["function_name"] = 'ipdpresprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/ipdpresprinting', $data);
        $this->load->view('layout/footer');
    }

    public function birthprinting()
    {
        if (!$this->rbac->hasPrivilege('birth_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/birthprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'birth');
        $data["function_name"] = 'birthprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/birthprinting', $data);
        $this->load->view('layout/footer');
    }

    public function deathprinting()
    {
        if (!$this->rbac->hasPrivilege('death_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/deathprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'death');
        $data["function_name"] = 'deathprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/deathprinting', $data);
        $this->load->view('layout/footer');
    }

    public function pathologyprinting()
    {
        if (!$this->rbac->hasPrivilege('pathology_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/pathologyprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'pathology');
        $data["function_name"] = 'pathologyprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/pathologyprinting', $data);
        $this->load->view('layout/footer');
    }

    public function radiologyprinting()
    {
        if (!$this->rbac->hasPrivilege('radiology_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/radiologyprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'radiology');
        $data["function_name"] = 'radiologyprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/radiologyprinting', $data);
        $this->load->view('layout/footer');
    }

    public function otprinting()
    {
        if (!$this->rbac->hasPrivilege('ot_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/otprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'ot');
        $data["function_name"] = 'otprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/otprinting', $data);
        $this->load->view('layout/footer');
    }

    public function pharmacyprinting()
    {
        if (!$this->rbac->hasPrivilege('pharmacy_bill_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/pharmacyprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'pharmacy');
        $data["function_name"] = 'pharmacyprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/pharmacyprinting', $data);
        $this->load->view('layout/footer');
    }

    public function bloodbankprinting()
    {
        if (!$this->rbac->hasPrivilege('bloodbank_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/bloodbankprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'bloodbank');
        $data["function_name"] = 'bloodbankprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/bloodbankprinting', $data);
        $this->load->view('layout/footer');
    }

    public function ambulanceprinting()
    {
        if (!$this->rbac->hasPrivilege('ambulance_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/ambulanceprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'ambulance');
        $data["function_name"] = 'ambulanceprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/ambulanceprinting', $data);
        $this->load->view('layout/footer');
    }

    public function payslipprinting()
    {
        if (!$this->rbac->hasPrivilege('print_payslip_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/payslipprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'payslip');
        $data["function_name"] = 'payslipprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/payslipprinting', $data);
        $this->load->view('layout/footer');
    }

    public function paymentreceipt()
    {		
		if (!$this->rbac->hasPrivilege('payment_receipt_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/paymentreceipt');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'paymentreceipt');
        $data["function_name"] = 'paymentreceipt';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/paymentreceipt', $data);
        $this->load->view('layout/footer');
    }

    public function getRecord($id)
    {
        $result = $this->printing_model->get($id, '');
        echo json_encode($result);
    }

    public function update()
    {       
        $id            = $this->input->post('id');
        $result = $this->printing_model->get($id, '');        
        $function_name = $this->input->post('function_name');
        $this->form_validation->set_rules('header_image', 'header_image', 'callback_handle_upload');        
        
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header');
            if ($function_name == 'opdpresprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'opdpre');
                $data["function_name"] = 'opdpresprinting';
                $this->load->view('admin/printing/opdpresprinting', $data);
            } elseif ($function_name == 'opdprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'opd');
                $data["function_name"] = 'opdprinting';
                $this->load->view('admin/printing/opdprinting', $data);
            } elseif ($function_name == 'ipdpresprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'ipdpres');
                $data["function_name"] = 'ipdpresprinting';
                $this->load->view('admin/printing/ipdpresprinting', $data);
            } elseif ($function_name == 'ipdprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'ipd');
                $data["function_name"] = 'ipdprinting';
                $this->load->view('admin/printing/ipdprinting', $data);
            } elseif ($function_name == 'pharmacyprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'pharmacy');
                $data["function_name"] = 'pharmacyprinting';
                $this->load->view('admin/printing/pharmacyprinting', $data);
            } elseif ($function_name == 'payslipprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'payslip');
                $data["function_name"] = 'payslipprinting';
                $this->load->view('admin/printing/payslipprinting', $data);
            } elseif ($function_name == 'paymentreceipt') {
                $data["printing_list"] = $this->printing_model->get('', 'paymentreceipt');
                $data["function_name"] = 'paymentreceipt';
                $this->load->view('admin/printing/paymentreceipt', $data);
            } elseif ($function_name == 'birthprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'birth');
                $data["function_name"] = 'birthprinting';
                $this->load->view('admin/printing/birthprinting', $data);
            } elseif ($function_name == 'deathprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'death');
                $data["function_name"] = 'deathprinting';
                $this->load->view('admin/printing/deathprinting', $data);
            } elseif ($function_name == 'pathologyprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'pathology');
                $data["function_name"] = 'pathologyprinting';
                $this->load->view('admin/printing/pathologyprinting', $data);
            } elseif ($function_name == 'radiologyprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'radiology');
                $data["function_name"] = 'radiologyprinting';
                $this->load->view('admin/printing/radiologyprinting', $data);
            } elseif ($function_name == 'otprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'ot');
                $data["function_name"] = 'otprinting';
                $this->load->view('admin/printing/otprinting', $data);
            } elseif ($function_name == 'bloodbankprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'bloodbank');
                $data["function_name"] = 'bloodbankprinting';
                $this->load->view('admin/printing/bloodbankprinting', $data);
            } elseif ($function_name == 'ambulanceprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'ambulance');
                $data["function_name"] = 'ambulanceprinting';
                $this->load->view('admin/printing/ambulanceprinting', $data);
            } elseif ($function_name == 'summaryprinting') {
                $data["printing_list"] = $this->printing_model->get('', 'discharge_card');
                $data["function_name"] = 'summaryprinting';
                $this->load->view('admin/printing/summaryprinting', $data);
            } elseif ($function_name == 'billprinting') {               
                $data["printing_list"] = $this->printing_model->get('', 'bill');
                $data["function_name"] = 'billprinting';
                $this->load->view('admin/printing/billprinting', $data);
            } elseif ($function_name == 'index') {               
                $data["printing_list"] = $this->printing_model->get('', 'appointment');
                $data["function_name"] = 'index';
                $this->load->view('admin/printing/appointmentprinting', $data);
            } elseif ($function_name == 'obstetrichistoryprinting') {               
                $data["printing_list"] = $this->printing_model->get('', 'obstetrichistoryprinting');
                $data["function_name"] = 'obstetrichistoryprinting';
                $this->load->view('admin/printing/obstetrichistoryprinting', $data);
            } elseif ($function_name == 'opdantenatalfindingprinting') {               
                $data["printing_list"] = $this->printing_model->get('', 'opdantenatalfindingprinting');
                $data["function_name"] = 'opdantenatalfindingprinting';
                $this->load->view('admin/printing/opdantenatalfindingprinting', $data);
            } elseif ($function_name == 'ipdantenatalfindingprinting') {               
                $data["printing_list"] = $this->printing_model->get('', 'ipdantenatalfindingprinting');
                $data["function_name"] = 'ipdantenatalfindingprinting';
                $this->load->view('admin/printing/ipdantenatalfindingprinting', $data);
            }

            $this->load->view('layout/footer');
        } else {
                
                $removeheader_image   = $this->input->post('removeheader_image');  
                if ($removeheader_image != '') {                        
                    $insertData = array(
                        'id'           => $id,
                        'print_header'     => ''
                    );
                     $this->printing_model->add($insertData);
                     
                     $last_id   = str_replace('uploads/printing/', "", $result['print_header']);
                     
                     $this->media_storage->filedelete($last_id, "./uploads/printing/");
                }
                
            $insertData = array(
                'id'           => $id,
                'print_footer' => $this->input->post('footer_content'),
                'is_active'    => 'yes',
            );            
          
            $this->printing_model->add($insertData);            
            
            if (!empty($_FILES["header_image"]["name"])) {
                $fileInfo = pathinfo($_FILES["header_image"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["header_image"]["tmp_name"], "./uploads/printing/" . $img_name);
                $img_data = array('id' => $id, 'print_header' => 'uploads/printing/' . $img_name);
                $this->printing_model->add($img_data);
            }            
            
            if ($function_name == 'index') {
                redirect('admin/printing/opdpresprinting');
            } elseif ($function_name == 'opdprinting') {
                redirect('admin/printing/opdprinting');
            } elseif ($function_name == 'ipdpresprinting') {
                redirect('admin/printing/ipdpresprinting');
            } elseif ($function_name == 'ipdprinting') {
                redirect('admin/printing/ipdprinting');
            } elseif ($function_name == 'pharmacyprinting') {
                redirect('admin/printing/pharmacyprinting');
            } elseif ($function_name == 'payslipprinting') {
                redirect('admin/printing/payslipprinting');
            } elseif ($function_name == 'paymentreceipt') {
                redirect('admin/printing/paymentreceipt');
            } elseif ($function_name == 'birthprinting') {
                redirect('admin/printing/birthprinting');
            } elseif ($function_name == 'deathprinting') {
                redirect('admin/printing/deathprinting');
            } elseif ($function_name == 'pathologyprinting') {
                redirect('admin/printing/pathologyprinting');
            } elseif ($function_name == 'radiologyprinting') {
                redirect('admin/printing/radiologyprinting');
            } elseif ($function_name == 'otprinting') {
                redirect('admin/printing/otprinting');
            } elseif ($function_name == 'bloodbankprinting') {
                redirect('admin/printing/bloodbankprinting');
            } elseif ($function_name == 'ambulanceprinting') {
                redirect('admin/printing/ambulanceprinting');
            } elseif ($function_name == 'summaryprinting') {
                redirect('admin/printing/summaryprinting');
            } elseif ($function_name == 'billprinting') {
                redirect('admin/printing/billprinting');
            } elseif ($function_name == 'appointmentprinting') {
                redirect('admin/printing/index');
            } elseif ($function_name == 'obstetrichistoryprinting') {
                redirect('admin/printing/obstetrichistoryprinting');
            } elseif ($function_name == 'opdantenatalfindingprinting') {
                redirect('admin/printing/opdantenatalfindingprinting');
            } elseif ($function_name == 'ipdantenatalfindingprinting') {
                redirect('admin/printing/ipdantenatalfindingprinting');
            }
        }
    }

    public function handle_upload()
    {
        if (isset($_FILES["header_image"]) && !empty($_FILES['header_image']['name'])) {
            $allowedExts = array('jpg', 'jpeg', 'png', 'gif');
            $temp        = explode(".", $_FILES["header_image"]["name"]);
            $extension   = end($temp);
            if ($_FILES["header_image"]["error"] > 0) {
                $error .= $this->lang->line('error_opening_the_file') . "<br />";
            }
            if (($_FILES["header_image"]["type"] != "image/gif") && ($_FILES["header_image"]["type"] != "image/jpeg") && ($_FILES["header_image"]["type"] != "image/jpg") && ($_FILES["header_image"]["type"] != "image/png")) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }

            if (!in_array(strtolower($extension), $allowedExts)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_extension_not_allowed'));
                return false;
            }
            return true;
        }        
    }

    public function obstetrichistoryprinting()
    {
        if (!$this->rbac->hasPrivilege('ipd_obstetric_history_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/obstetrichistoryprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'obstetric_history');
        $data["function_name"] = 'obstetrichistoryprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/obstetrichistoryprinting', $data);
        $this->load->view('layout/footer');
    }

    public function opdantenatalfindingprinting()
    {
        if (!$this->rbac->hasPrivilege('opd_antenatal_finding_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/opdantenatalfindingprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'opd_antenatal_finding');
        $data["function_name"] = 'opdantenatalfindingprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/opdantenatalfindingprinting', $data);
        $this->load->view('layout/footer');
    }

    public function ipdantenatalfindingprinting()
    {
        if (!$this->rbac->hasPrivilege('ipd_antenatal_finding_print_header_footer', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/ipdantenatalfindingprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'ipd_antenatal_finding');
        $data["function_name"] = 'ipdantenatalfindingprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/ipdantenatalfindingprinting', $data);
        $this->load->view('layout/footer');
    }
    
    public function index()
    {        
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_sidebar_menu', 'admin/printing/appointmentprinting');
        $this->session->set_userdata('sub_menu', 'admin/printing');
        $data["printing_list"] = $this->printing_model->get('', 'appointment');
        $data["function_name"] = 'appointmentprinting';
        $this->load->view('layout/header');
        $this->load->view('admin/printing/appointmentprinting', $data);
        $this->load->view('layout/footer');
    }
}
