<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class System_notification
{

    private $_CI;
    private $hospital_setting;
    public function __construct()
    {
        $this->_CI = &get_instance();
        $this->_CI->load->model('webservice_model');
        $this->_CI->load->model('notification_model');
    } 
   
    public function send_system_notification($event,$event_variables,$notification_array=array()){
        $notification_data=array();
    	$event_data=$this->_CI->webservice_model->getSystemNotification_byevent($event);
        
         if(array_key_exists('patient_id',$event_variables)){
            $patient_data=$this->_CI->webservice_model->getpatientDetails($event_variables['patient_id']);
            $event_variables['patient_name']=$patient_data['patient_name'];
         }        
 
         if(array_key_exists('mother_id',$event_variables)){
            $patient_data=$this->_CI->webservice_model->getpatientDetails($event_variables['mother_id']);
            $event_variables['mother_name']=$patient_data['patient_name'];
         }
        
        $patient_message=$this->get_template_message($event_variables,$event_data['patient_message']);       
        
        if($event=='notification_appointment_created' && $event_data['is_active']==1){          

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'role_id'                                       => null,
                'receiver_id'                                   => $event_variables['patient_id'],
                'notification_type'                             => $event_data['notification_type'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);  
        }elseif($event=='appointment_approved' && $event_data['is_active']==1){
            
           

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'  => $patient_message,
                'role_id'                                       => null,
                'notification_type'  => $event_data['notification_type'],
                'receiver_id'        => $event_variables['patient_id'],
                'date'               => $date('Y-m-d H:i:s'),
                'is_active'          => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }
    }

    public function get_template_message($variables,$template_message){
        foreach ($variables as $key => $value) { 
            $template_message = str_replace('{{' . $key . '}}', $value, $template_message);
        }
        return $template_message;
    }
}