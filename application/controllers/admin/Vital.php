<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vital extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('unittype_model');
        $this->load->model('taxcategory_model');
        $this->load->library('datatables');
        $this->load->library('system_notification');
        $this->load->model('vital_model');
		$this->load->library('Customlib');
		$this->time_format         = $this->customlib->getHospitalTimeFormat();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('vital', 'can_view')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'setup');
        $this->session->set_userdata('sub_menu', 'vital/index');
        $this->config->load("payroll");
        $charge_type         = $this->chargetype_model->get();
        $data["charge_type"] = $charge_type;
        $data['unit_type']   = $this->unittype_model->get();
        $data['schedule']    = $this->organisation_model->get();
        $data['taxcategory'] = $this->taxcategory_model->get();

        $this->load->view("layout/header");
        $this->load->view("admin/vital/vital", $data);
        $this->load->view("layout/footer");
    }

    public function getDatatable()
    {
        $dt_response = $this->vital_model->getDatatableAllRecord();
        $dt_response = json_decode($dt_response);
        $dt_data     = array();
        if (!empty($dt_response->data)) {
            foreach ($dt_response->data as $charge_key => $charge_value) {

                $row    = array();
                $action = "<div class=''>";             
				
				if ($this->rbac->hasPrivilege('vital', 'can_edit')) {
						
					$action .= "<a  href='javascript:void(0)' class='btn btn-default btn-xs edit_record edit_vital_modal' data-loading-text='" . $this->lang->line('please_wait') . "' data-toggle='tooltip' data-record-id=" . $charge_value->id . "  title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
						
				}
					
				if($charge_value->is_system != 1){	
					if ($this->rbac->hasPrivilege('vital', 'can_delete')) {
					
						$action .= "<a class='btn btn-default btn-xs' data-toggle='tooltip' title='' onclick='delete_vital (" . $charge_value->id . ")' data-original-title='" . $this->lang->line('delete') . "'> <i class='fa fa-trash'></i></a>";
						
					}
				}
			  
                $action .= "</div>";

                $row[] = $charge_value->name;
                $row[] = $charge_value->reference_range;
                $row[] = $charge_value->unit;
                $row[] = $action;

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

    public function add_vital()
    {        
        $this->form_validation->set_rules('vital_name', $this->lang->line('vital_name'), 'required');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'vital_name'     => form_error('vital_name'),                
            );
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $range="";
             if($this->input->post('from_reference_range')!=""){
                $range = $this->input->post('from_reference_range');
            }            
            if($this->input->post('to_reference_range')!=""){
                $range = $this->input->post('to_reference_range');
            }
            if($this->input->post('from_reference_range')!="" && $this->input->post('to_reference_range')!="" ){
                $range = $this->input->post('from_reference_range').' - '.$this->input->post('to_reference_range');
            }
            $data = array(
                'id'                 => $this->input->post('vital_id'),
                'name'               => $this->input->post('vital_name'),
                'unit'               => $this->input->post('unit'),
                'reference_range'    => $range ,
            );
            
            $insert_id  = $this->vital_model->add($data);
            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($json_array);
    }
    
    public function getDetails()
    {      
        $id           = $this->input->post("vital_id");        
        $result       = $this->vital_model->getDetails($id);
        if(strpos($result['reference_range'],'-')!=false ){
            $str = explode("-",$result['reference_range']);
            $result['min_range'] = $str[0];
            $result['max_range'] = $str[1];
        }else{
            $result['min_range'] = $result['reference_range'];
        }       
        
        $json_array   = array('status' => '1', 'error' => '', 'result' => $result);
        echo json_encode($json_array);
    }

    public function viewDetails()
    {
        if (!$this->rbac->hasPrivilege('hospital_charges', 'can_view')) {
            access_denied();
        }
        $id             = $this->input->post("charges_id");
        $data['result'] = $this->charge_model->getDetails($id, "");
        $page           = $this->load->view("admin/charges/_viewDetails", $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }  

    public function delete($id)
    {      
        if (!$this->rbac->hasPrivilege('vital', 'can_delete')) {
            access_denied();
        }
        $result = $this->vital_model->delete($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));        
    }
    
    public function delete_patient_vital($id)
    {        
        $result = $this->vital_model->delete_patient_vital($id);
        echo json_encode(array('status' => 1, 'msg' => $this->lang->line('delete_message')));
    }
    
    public function addpatientvital()
	{  
		$total_rows = $this->input->post('total_rows_vitals');
		 
		if (isset($total_rows) && !empty($total_rows)) {
			
            foreach ($total_rows as $row_key => $row_value) {		 
				
                $vital_name_validation  				= $this->input->post('vital_name_' . $row_value);
                $from_reference_range_validation      	= $this->input->post('from_reference_range_' . $row_value);
                $vital_date_validation           		= $this->input->post('vital_date_' . $row_value);				
                
				$vital_name = 0;
                if ($vital_name_validation == "") {		
					$vital_name = 1;                     
                }				 
				
				$reference_range = 0;
                if ($from_reference_range_validation == "") {
					$reference_range = 1;
                }
				
				$vital_date = 0;
                if ($vital_date_validation == "") {
					$vital_date = 1;
                    $this->form_validation->set_rules('vital_date', $this->lang->line('date'), 'trim|required');        
                } 			
				
            } 
			 
        }	
		
		if($vital_name == 1 && $reference_range == 1 && $vital_date == 1){
			
			$msg   = array('vital_name' => '<p>'.$this->lang->line('the_vital_name_field_is_required').'</p>','from_reference_range' => '<p>'.$this->lang->line('the_vital_value_field_is_required').'</p>','vital_date' => '<p>'.$this->lang->line('the_vital_date_field_is_required').'</p>');			
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
			
		}elseif ($vital_name == 1 && $reference_range == 1) {
			
            $msg   = array('vital_name' => '<p>'.$this->lang->line('the_vital_name_field_is_required').'</p>','from_reference_range' => '<p>'.$this->lang->line('the_vital_value_field_is_required').'</p>');
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        }elseif($reference_range == 1 && $vital_date == 1){
			
			$msg   = array('from_reference_range' => '<p>'.$this->lang->line('the_vital_value_field_is_required').'</p>','vital_date' => '<p>'.$this->lang->line('the_vital_date_field_is_required').'</p>');
			$json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
			
		}elseif($vital_date == 1 && $vital_name == 1){
			
			$msg   = array('vital_name' => '<p>'.$this->lang->line('the_vital_name_field_is_required').'</p>','vital_date' => '<p>'.$this->lang->line('the_vital_date_field_is_required').'</p>');
			$json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
			
		}elseif($vital_name == 1){
			
			$msg   = array('vital_name' => '<p>'.$this->lang->line('the_vital_name_field_is_required').'</p>');
			$json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
			
		}elseif($reference_range == 1){
			
			$msg   = array('from_reference_range' => '<p>'.$this->lang->line('the_vital_value_field_is_required').'</p>');	
			$json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
			
		}elseif($vital_date == 1){
			
			$msg   = array('vital_date' => '<p>'.$this->lang->line('the_vital_date_field_is_required').'</p>');	
			$json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
			
		}else {
             
            $total_rows_vitals   = $this->input->post('total_rows_vitals');
                
            foreach ($total_rows_vitals as $row_key => $row_values) {
                        
				$data = array(								 
						'patient_id'         => $this->input->post('patient_id'),
						'vital_id'           => $this->input->post('vital_name_' . $row_values),
						'reference_range'    => $this->input->post('from_reference_range_' . $row_values) ,
						'messure_date'       => $this->customlib->dateFormatToYYYYMMDDHis($this->input->post('vital_date_' . $row_values), $this->time_format),                    
				);				 
							
				$this->vital_model->addpatientvital($data);

            }

            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));

        } 

        echo json_encode($json_array);
    }
    
    public function editvital()
    {
        $id     = $this->input->post("id");
        $result = $this->vital_model->getpatientvitalbyvitalid($id);
        $result['messure_date'] = $this->customlib->YYYYMMDDHisTodateFormat($result['messure_date'],$this->customlib->getHospitalTimeFormat());
        echo json_encode($result);
    }
    
    public function editpatientvital()
    {        
        $this->form_validation->set_rules('evital_name', $this->lang->line('vital_name'), 'required');        
        $this->form_validation->set_rules('evital_value', $this->lang->line('vital_value'), 'required');
        $this->form_validation->set_rules('emessure_date', $this->lang->line('vital_date'), 'required');    
        $emessure_date	=	$this->customlib->dateFormatToYYYYMMDDHis($this->input->post("emessure_date"), $this->time_format);             
        
        if ($this->form_validation->run() == false) {
            
            $msg = array(
                'vital_name'     => form_error('evital_name'),
                'vital_value'    => form_error('evital_value'),
                'emessure_date'   => form_error('emessure_date'),
            );            
             
            $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            
        } else {
            
            $data = array(
                'id'                 => $this->input->post('evital_id'),
                'patient_id'         => $this->input->post('patient_id'),
                'vital_id'           => $this->input->post('evital_name'),
                'reference_range'    => $this->input->post('evital_value') ,
                'messure_date'    	 => $emessure_date,
            );              
           
            $insert_id  = $this->vital_model->addpatientvital($data);
            $json_array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));           
            
        }

        echo json_encode($json_array);
    }
    
}
