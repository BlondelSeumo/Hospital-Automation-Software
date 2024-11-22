<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Holiday_model extends CI_Model
{

    public function getHolidaybyDate($date_from, $date_to)
    {
        $this->db->select('annual_calendar.*,staff.id as sid,staff.name,staff.surname');
        $this->db->join('staff', 'annual_calendar.created_by = staff.id', "LEFT");       
        $this->db->where('from_date >= ', $date_from);
        $this->db->where('to_date <= ', $date_to);
        $this->db->where('front_site ', 1);		
        $query  = $this->db->get('annual_calendar');
        $result = $query->result_array();
        foreach ($result as $key => $value) {
			
			if($value['holiday_type'] == 1){
				$result[$key]['title'] = "Holiday";
			}elseif($value['holiday_type'] == 2){
				$result[$key]['title'] = "Activity";
			}elseif($value['holiday_type'] == 3){
				$result[$key]['title'] = "Vacation";
			}			
			
			$holiday_date_list = array();
			$from_date = strtotime($value['from_date']);
            $to_date   = strtotime($value['to_date']);			
			for ($std = $from_date; $std <= $to_date; $std += 86400) {
				if ($std >= ($from_date) && $std <= ($to_date)) {                                 
					$holiday_date_list[] =  date('Y-m-d', $std);					
				}
			}
							
            $result[$key]['date_list'] = implode(",", $holiday_date_list) ;            
			
        }
        return $result;
    }

}
