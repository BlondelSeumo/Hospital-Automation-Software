<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Holiday extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("holiday_model");
		$this->sch_setting_detail  = $this->setting_model->getSetting();
    }

    public function index()
    {        
		if (!$this->rbac->hasPrivilege('annual_calendar', 'can_view')) {
            access_denied();
        }
		
		$this->session->set_userdata('top_menu', 'annual_calendar');
        $this->session->set_userdata('sub_menu', 'annual_calendar/index');
		
        $data['title']       	        =	$this->lang->line('select_criteria');
        $data["search_holiday_type"]	=	"";

        if (isset($_POST['search_holiday_type']) && $_POST['search_holiday_type'] != '') {
            $search_holiday_type = $_POST['search_holiday_type'];
			
			$data["search_holiday_type"]	=	$_POST['search_holiday_type'];
        }         
        $this->form_validation->set_rules('search_holiday_type', $this->lang->line('type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) { 
            $holidaylist   =   $this->holiday_model->get(null,null);
        } else {
            $holidaylist   =   $this->holiday_model->get(null, $search_holiday_type);
        }

        $data["holidaylist"]  	=	$holidaylist; 
		$data['superadmin_restriction']        = $this->sch_setting_detail->superadmin_restriction;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/holiday/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {       
		 
        $holiday_type=$this->input->post('holiday_type');

        if(isset($holiday_type) && $holiday_type==3){
           $this->form_validation->set_rules('from_date', $this->lang->line('from_date'), 'trim|required|xss_clean');
           $this->form_validation->set_rules('to_date', $this->lang->line('to_date'), 'trim|required|xss_clean'); 
        }else if(isset($holiday_type) && ( $holiday_type==1 ||  $holiday_type==2 )){
           $this->form_validation->set_rules('from_date', $this->lang->line('date'), 'trim|required|xss_clean');
        }else{
           $this->form_validation->set_rules('holiday_type', $this->lang->line('type'), 'trim|required|xss_clean');           
        }

        $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');     
        if ($this->form_validation->run() == false) {            
            $msg = array(
                'holiday_type'   =>     form_error('holiday_type'),
                'from_date'      =>     form_error('from_date'),
                'to_date'        =>     form_error('to_date'),
                'description'    =>     form_error('description')            
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            // IF THERE IS SINGLE DATE THEN IT WILL BE SAME FOR BOTH COLUMNS - FROM_DATE AND TO_DATE //
            if(isset($holiday_type) && $holiday_type==3 && $holiday_type!=''){                
                 $from_date   =    $this->input->post('from_date');
                 $to_date     =    $this->input->post('to_date');
            }else{                
                 $from_date   =   $this->input->post('from_date');
                 $to_date     =   $this->input->post('from_date');
            }  
			
			if($this->input->post('front_site')){
				$front_site	=	1;
			}else{
				$front_site	=	0;
			}           

            $data = array(
                'id'                 =>     $this->input->post('id'),
                'holiday_type'       =>     $this->input->post('holiday_type'),
                'from_date'          =>     date('Y-m-d', $this->customlib->datetostrtotime($from_date)),
                'to_date'            =>     date('Y-m-d 23:59:00', $this->customlib->datetostrtotime($to_date)),
                'description'        =>     $this->input->post('description'),
                'front_site'         =>     $front_site,
                'created_by'         =>     $this->customlib->getStaffID(),
                'holiday_color'      =>     '#008000'                
            );

            $edit_id= $this->input->post('id');
            if($edit_id>0){
                $data['updated_at']      =   date('Y-m-d') ;   
            }else{
                $data['created_at']      =   date('Y-m-d H:i:s') ;  
            }

            $this->holiday_model->add($data);      
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
    }     
        echo json_encode($array);
    }


    public function delete_holiday()
    {
		if (!$this->rbac->hasPrivilege('annual_calendar', 'can_delete')) {
            access_denied();
        }
		
        $this->holiday_model->delete_holiday($_POST['id']);
        $array = array('status' => 1, 'success' => $this->lang->line('delete_message'));
        echo json_encode($array);
    }
	
	public function getholiday()
    {
        $id           = $this->input->post("id");        
        $result       = $this->holiday_model->get($id);
		 
		$result['from_date'] = date($this->customlib->YYYYMMDDTodateFormat($result['from_date']));
		$result['to_date'] = date($this->customlib->YYYYMMDDTodateFormat($result['to_date']));
		 
        $json_array   = array('status' => '1', 'error' => '', 'result' => $result);
        echo json_encode($json_array);
    }

    
}

?>
