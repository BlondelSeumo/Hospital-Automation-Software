<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dutyroster_model extends MY_Model
{
   
    public function add_shift($data){        
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id']!="") {
            $this->db->where('id', $data['id']);
            $this->db->update('duty_roster_shift', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Duty Roster Shift id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('duty_roster_shift', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Duty Roster Shift id" . $insert_id;
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
    
    public function getDatatableAllRecord()
    {        
        $this->datatables
            ->select('duty_roster_shift.*')
            ->searchable('duty_roster_shift.id,duty_roster_shift.shift_name,duty_roster_shift.shift_start,duty_roster_shift.shift_end,duty_roster_shift.shift_hour')
            ->orderable('duty_roster_shift.id,duty_roster_shift.shift_name,duty_roster_shift.shift_start,duty_roster_shift.shift_end,duty_roster_shift.shift_hour')
            ->sort('duty_roster_shift.id', 'asc')       
            ->from('duty_roster_shift');
        return $this->datatables->generate('json');
    }
	
	public function delete_shift($id)
    {
        $query = $this->db->where('id', $id)
            ->delete('duty_roster_shift');
        $message   = DELETE_RECORD_CONSTANT . " On duty_roster_shift id " . $id;
    }
   
    public function getShiftList($id = null)
    {
        $this->db->select('duty_roster_shift.*');
		if($id > 0){
        $this->db->where('duty_roster_shift.id', $id);
		}
        $query = $this->db->get('duty_roster_shift');   
		if($id > 0){
        return $query->row_array();
		}else{
			return $query->result_array();
		}
    }

    public function check_shift_time($datetime_1,$datetime_2,$shift_id){		
		$this->db->select("duty_roster_shift.id");	 
		$this->db->where("date_format(`shift_start`,'%H:%i:%s')='$datetime_1' and date_format(`shift_end`,'%H:%i:%s')='$datetime_2' ");
		$query = $this->db->get('duty_roster_shift');  
		$result = $query->row_array();
		
		if (!empty($result['id'])){
			if($result['id'] == $shift_id) {
				return 1;
			} else {
				return 0;
			}
         } else {
            return 1;
         }        
    }

    public function check_roster_date($datetime_1,$datetime_2,$id){		
		$this->db->select("duty_roster_list.id"); 
		$this->db->where("date_format(`duty_roster_start_date`,'%Y-%m-%d')='$datetime_1' and date_format(`duty_roster_end_date`,'%Y-%m-%d')='$datetime_2' ");		         
		$query = $this->db->get('duty_roster_list');
		$result = $query->row_array();
		if (!empty($result['id'])){
			if($result['id'] == $id) {
				return 1;
			} else {
				return 0;
			}
         } else {
            return 1;
         }			
    }

    public function check_roster_assign($duty_roster_list_id,$staff_id,$code){		
		$this->db->select("duty_roster_assign.code");      
		$this->db->where("duty_roster_list_id=$duty_roster_list_id");   
		$this->db->where("staff_id =$staff_id");	   
		$this->db->group_by('staff_id');   
		$query = $this->db->get('duty_roster_assign'); 
		$result = $query->row_array();
		if (!empty($result['code'])){
			if($result['code'] == $code) {
				return 1;
			} else {
				return 0;
			}
         } else {
            return 1;
         }       		
    }

	public function getRosterListDetails($id = null){
        $this->db->select("duty_roster_list.* ");
        if($id > 0){
        $this->db->where('duty_roster_list.id', $id);
        }
        $query = $this->db->get('duty_roster_list');   
        if($id > 0){
        return $query->row_array();
        }else{
            return $query->result_array();
        }
    }   
    
	public function add_roster_list($data){        
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id']!="") {
            $this->db->where('id', $data['id']);
            $this->db->update('duty_roster_list', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Duty Roster Shift id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('duty_roster_list', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Duty Roster Shift id" . $insert_id;
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
    
    public function delete_roster_list($id){
        $query = $this->db->where('id', $id)
            ->delete('duty_roster_list');
        $message   = DELETE_RECORD_CONSTANT . " On duty_roster_list id " . $id;
    }

    public function add_roster_assign($data){        
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id']!="") {
            $this->db->where('id', $data['id']);
            $this->db->update('duty_roster_assign', $data);
            $message = UPDATE_RECORD_CONSTANT . " On duty_roster_assign id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('duty_roster_assign', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On duty_roster_assign id" . $insert_id;
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
	
	public function getRosterListDatatable()
    {  
        $this->datatables
            ->select('duty_roster_list.*,duty_roster_shift.shift_name,duty_roster_shift.shift_start,duty_roster_shift.shift_end,duty_roster_shift.shift_hour')
            ->searchable('duty_roster_shift.shift_name,duty_roster_list.duty_roster_start_date,duty_roster_list.duty_roster_end_date,duty_roster_shift.shift_start,duty_roster_shift.shift_end,duty_roster_shift.shift_hour,duty_roster_list.duty_roster_total_day,duty_roster_list.duty_roster_shift_id')           
            ->orderable('duty_roster_shift.shift_name,duty_roster_list.duty_roster_start_date,duty_roster_list.duty_roster_end_date,duty_roster_shift.shift_start,duty_roster_shift.shift_end,duty_roster_shift.shift_hour,duty_roster_list.duty_roster_total_day,duty_roster_list.duty_roster_shift_id')			
            ->join('duty_roster_shift', 'duty_roster_shift.id = duty_roster_list.duty_roster_shift_id')            
            ->sort('duty_roster_list.id', 'desc')
            ->from('duty_roster_list');
        return $this->datatables->generate('json');
    }

    public function getRosterList($id=null)
    {
        $this->db->select("duty_roster_list.*, duty_roster_shift.shift_name,duty_roster_shift.shift_end,duty_roster_shift.shift_start ");
        $this->db->join('duty_roster_shift', 'duty_roster_shift.id = duty_roster_list.duty_roster_shift_id') ; 
        if($id > 0){
            $this->db->where('duty_roster_list.id', $id);
        }
        $query = $this->db->get('duty_roster_list');  
        return $query->result_array(); 
    }
 
    public function getmaxroster_assign_id()
    {
        $this->db->select("duty_roster_assign.code");
        $this->db->order_by('duty_roster_assign.id', 'desc');        
        $this->db->limit(1);
        $query = $this->db->get('duty_roster_assign'); 
        $result = $query->row();
        return $result ;
    }    

    public function getdutyRoster_assignList(){

        $this->datatables
            ->select('duty_roster_assign.code,duty_roster_assign.roster_duty_date,duty_roster_assign.floor_id,duty_roster_assign.department_id,duty_roster_assign.staff_id,duty_roster_assign.duty_roster_list_id,duty_roster_shift.shift_name,duty_roster_shift.shift_start,duty_roster_shift.shift_end,duty_roster_shift.shift_hour,
                floor.name as floor_name,department.department_name,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,duty_roster_start_date,duty_roster_end_date')
             ->searchable('floor.name as floor_name,department.department_name,staff.name as staff_name,staff.surname as staff_surname,duty_roster_start_date,duty_roster_end_date,shift_name,shift_name,duty_roster_shift.shift_start,duty_roster_shift.shift_end,duty_roster_shift.shift_hour,duty_roster_list.duty_roster_total_day')
             ->orderable('floor.name as floor_name,department.department_name,staff.name as staff_name,staff.surname as staff_surname,duty_roster_start_date,duty_roster_end_date,shift_name,duty_roster_shift.shift_start,duty_roster_shift.shift_end,duty_roster_shift.shift_hour,duty_roster_list.duty_roster_total_day')
            ->join('duty_roster_list', 'duty_roster_list.id = duty_roster_assign.duty_roster_list_id') 
            ->join('duty_roster_shift', 'duty_roster_shift.id = duty_roster_list.duty_roster_shift_id')
            ->join('staff', 'staff.id = duty_roster_assign.staff_id') 
            ->join('floor', 'floor.id = duty_roster_assign.floor_id','left')  
            ->join('department', 'department.id = duty_roster_assign.department_id','left')  
            ->sort('staff.id', 'asc')
            ->group_by('duty_roster_assign.code')
            ->from('duty_roster_assign');                       

        return $this->datatables->generate('json');        
    }   

    public function getassignRosterDetails($id = null){ 

        $this->db->select("duty_roster_shift.shift_name,duty_roster_shift.shift_start,duty_roster_shift.shift_end,duty_roster_shift.shift_hour,duty_roster_assign.*,floor.name as floor_name,department.department_name,staff.name as staff_name,staff.surname as staff_surname,duty_roster_start_date,duty_roster_end_date,duty_roster_list.duty_roster_shift_id");
        $this->db->join('duty_roster_list', 'duty_roster_list.id = duty_roster_assign.duty_roster_list_id') ;
        $this->db->join('duty_roster_shift', 'duty_roster_shift.id = duty_roster_list.duty_roster_shift_id');
        $this->db->join('staff', 'staff.id = duty_roster_assign.staff_id') ;
        $this->db->join('floor', 'floor.id = duty_roster_assign.floor_id','left')  ;
        $this->db->join('department', 'department.id = duty_roster_assign.department_id','left')  ;

        if($id > 0){
        $this->db->where('duty_roster_assign.code', $id);
        }
        $this->db->group_by('duty_roster_assign.code');
        $query = $this->db->get('duty_roster_assign');   
        if($id > 0){
        return $query->row_array();
        }else{
            return $query->result_array();
        }
    }  
	
    public function delete_roster_assign($id){        
        $query = $this->db->where('code', $id)
            ->delete('duty_roster_assign');
        $message   = DELETE_RECORD_CONSTANT . " On duty_roster_assign id " . $id;
    }  
	
	public function getrosterassign_datails($staff=null,$from_date=null,$to_date=null){         

        $this->db->select("duty_roster_assign.staff_id"); 
		if (isset($staff) && $staff!="") {
			$this->db->where('duty_roster_assign.staff_id', $staff);
		}
		if (isset($from_date) && $from_date!=""  && isset($to_date) && $to_date!="" ) {
			$this->db->where(" date_format(duty_roster_assign.roster_duty_date,'%Y-%m-%d')  between '$from_date'  and '$to_date'");
		}
        $this->db->group_by('staff_id');        
        $this->db->order_by('staff_id', 'asc');        
        $query = $this->db->get('duty_roster_assign');       
		
		$result1 =  $query->result_array();
		if(!empty($result1)){
			foreach ($result1 as $key => $result_value){
				
				$this->db->select("duty_roster_list.*,duty_roster_shift.*,duty_roster_assign.*,floor.name as floor_name,department.department_name,staff.name as staff_name,staff.surname as staff_surname,duty_roster_start_date,duty_roster_end_date,staff.employee_id");
				
				$this->db->join('duty_roster_list', 'duty_roster_list.id = duty_roster_assign.duty_roster_list_id') ;
				$this->db->join('duty_roster_shift', 'duty_roster_shift.id = duty_roster_list.duty_roster_shift_id');
				$this->db->join('staff', 'staff.id = duty_roster_assign.staff_id') ;
				$this->db->join('floor', 'floor.id = duty_roster_assign.floor_id','left')  ;
				$this->db->join('department', 'department.id = duty_roster_assign.department_id','left')  ;				
				
				if (isset($staff) && $staff!="") {
					$this->db->where('duty_roster_assign.staff_id', $staff);
				}else{
					$this->db->where('duty_roster_assign.staff_id', $result_value['staff_id']);
				}			
			
				$query1 = $this->db->get('duty_roster_assign');  
				$results[$key] = $query1->result_array(); 
			}
			return $results;	
		}else{
			return '';		
		}       	

    }  

	public function getRosterListByShiftId($shift=null)
    {
        $this->db->select("duty_roster_list.*, duty_roster_shift.shift_name,duty_roster_shift.shift_end,duty_roster_shift.shift_start ");
        $this->db->join('duty_roster_shift', 'duty_roster_shift.id = duty_roster_list.duty_roster_shift_id') ;         
            $this->db->where('duty_roster_list.duty_roster_shift_id', $shift); 
        $query	= $this->db->get('duty_roster_list');  
		$result = $query->result_array(); 
		foreach ($result as $key => $result_value){				  
				$result[$key]['duty_roster_start_date'] = $this->customlib->YYYYMMDDTodateFormat($result_value['duty_roster_start_date']);
				$result[$key]['duty_roster_end_date'] = $this->customlib->YYYYMMDDTodateFormat($result_value['duty_roster_end_date']);
			}
			
        return $result;
    }

    
}
