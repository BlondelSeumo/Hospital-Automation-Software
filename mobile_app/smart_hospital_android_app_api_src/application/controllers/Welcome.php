<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	    public function __construct()
    {
        parent::__construct();
       
        $this->load->model(array('staff_model','doctorshift_model','appointment_model')); 
    }


  	public function index()
  	{
  		
  		$this->load->view('welcome_message');
  	}


	 // public function getSpecialist()
  //   {

  // 		$specialist         = $this->webservice_model->getSpecialist();
  // 		$data['specialist'] = $specialist;
  // 		json_output(200, $data);
  //    }


	 // public function getSpecialistDoctor($specialist_id)
  //   {
  //       $doctors         = $this->staff_model->getSpecialistStaff($specialist_id);  		
  // 		$data['doctors'] = $doctors;
  // 		json_output(200, $data);
  //    }

	 // public function getDoctorShift($staff_id)
  //   {
  //       $doctor_shifts         = $this->doctorshift_model->getDoctorShift($staff_id);  		
  // 		$data['doctor_shifts'] = $doctor_shifts;
  // 		json_output(200, $data);
  //    }

	 // public function getDoctorTimeSlots($staff_id,$global_shift_id,$day)
  //   {
  //       $doctor_shifts         = $this->doctorshift_model->getDoctorTimeSlots($staff_id,$global_shift_id,$day);  		
  // 		$data['doctor_shifts'] = $doctor_shifts;
  // 		json_output(200, $data);
  //    }

	 // public function addNewAppointment()
  //   {
  //            $params            = json_decode(file_get_contents('php://input'), true);
  //            $data=array(
  //            	'patient_name'=>$params['patient_name'],
  //            	'mobileno'=>$params['mobileno'],
  //            	'email'=>$params['email'],
		//         'gender'=>$params['gender'],
  //            );

		// 	 $appointment_data=array(
		// 	     'patient_id'=>$params['patient_id'],
		// 	     'date'=>$params['date'],
		// 	     'doctor'=>$params['doctor'],
		// 	     'specialist'=>$params['specialist'],
		// 	     'message'=>$params['message'],
		// 	     'appointment_status'=>$params['appointment_status'],
		// 	     'source'=>$params['source'],
		// 	     'global_shift_id'=>$params['global_shift_id'],
		// 	     'shift_id'=>$params['shift_id'],
		// 	     'live_consult'=>$params['live_consult']
		// 	 );

		//     $this->appointment_model->add($data,$appointment_data);

  //    }

     
     

    
         
}
