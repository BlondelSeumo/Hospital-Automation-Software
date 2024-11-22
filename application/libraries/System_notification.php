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
        $this->_CI->load->model('setting_model');
        $this->_CI->load->model('notification_model');
        $this->_CI->load->model('notificationsetting_model');
        $this->_CI->load->model('role_model');
        $this->_CI->load->library('Customlib');
        $this->hospital_setting = $this->_CI->setting_model->get();
        $this->notification            = $this->_CI->config->item('notification');
        $this->notificationurl         = $this->_CI->config->item('notification_url');
        $this->patient_notificationurl = $this->_CI->config->item('patient_notification_url'); 
    } 
   
    public function send_system_notification($event,$event_variables,$notification_array=array()){
        $notification_data=array();
    	$event_data=$this->_CI->notificationsetting_model->getSystemNotification_byevent($event); 

         if(array_key_exists('patient_id',$event_variables)){
            $patient_data=$this->_CI->notificationsetting_model->getpatientDetails($event_variables['patient_id']);
            $event_variables['patient_name']=$patient_data['patient_name'];
         }

        if(array_key_exists('mother_id',$event_variables)){
            $patient_data=$this->_CI->notificationsetting_model->getpatientDetails($event_variables['mother_id']);
            $event_variables['mother_name']=$patient_data['patient_name'];
         }

        $staff_message=$this->get_template_message($event_variables,$event_data['staff_message']);
        
        if($event_data['patient_message']){
            $patient_message=$this->get_template_message($event_variables,$event_data['patient_message']);
        }else{
            $patient_message= '';
        }
        
        $adminid = $this->_CI->notificationsetting_model->getstaffidByID(1);
        $supperadminid = $this->_CI->notificationsetting_model->getstaffidByID(7);        

        if($event!='notification_opd_prescription_created'){
        if(!empty($adminid)){  
            foreach($adminid as $adminid_value){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $staff_message,
                    'role_id'                                       => 1,
                    'receiver_id'                                   => $adminid_value['staff_id'],
                    'notification_type'                             => $event_data['notification_type'],
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
        }

        if(!empty($supperadminid)){
            foreach($supperadminid as $supperadminid_value){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $staff_message,
                    'role_id'                                       => 7,
                    'receiver_id'                                   => $supperadminid_value['staff_id'],
                    'notification_type'                             => $event_data['notification_type'],
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
        }
    }
        
        if($event=='notification_appointment_created' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                    => $staff_message,
                'role_id'                              => 3,
                'receiver_id'                          => $event_variables['doctor_id'],
                'notification_type'                    => $event_data['notification_type'],
                'date'                                 => date('Y-m-d H:i:s'),
                'is_active'                            => 'yes',
                );          
            }

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
            
            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'  => $staff_message,
                'role_id'            => 3,
                'receiver_id'        => $event_variables['doctor_id'],
                'notification_type'  => $event_data['notification_type'],
                'date'               => date('Y-m-d H:i:s'),
                'is_active'          => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'  => $patient_message,
                'role_id'                                       => null,
                'notification_type'  => $event_data['notification_type'],
                'receiver_id'        => $event_variables['patient_id'],
                'date'               => date('Y-m-d H:i:s'),
                'is_active'          => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='opd_visit_created' && $event_data['is_active']==1){
             
            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $staff_message,
                'role_id'                                       =>3,
                'receiver_id'                                   => $event_variables['doctor_id'],
                'notification_type'                             => $event_data['notification_type'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'role_id'                                       => null,
                'notification_type'                             => $event_data['notification_type'],
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_ipd_antenatal' && $event_data['is_active']==1){
             
            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title'                            => $event_data['subject'],
                'notification_desc'                             => $staff_message,
                'role_id'                                       => 3,
                'receiver_id'                                   => $event_variables['doctor_id'],
                'notification_type'                             => $event_data['notification_type'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title'                            => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'role_id'                                       => null,
                'notification_type'                             => $event_data['notification_type'],
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_opd_antenatal' && $event_data['is_active']==1){
             
            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title'                            => $event_data['subject'],
                'notification_desc'                             => $staff_message,
                'role_id'                                       => 3,
                'receiver_id'                                   => null,
                'notification_type'                             => $event_data['notification_type'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title'                            => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'role_id'                                       => null,
                'notification_type'                             => $event_data['notification_type'],
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_ipd_postnatal_history' && $event_data['is_active']==1){
             
            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title'                            => $event_data['subject'],
                'notification_desc'                             => $staff_message,
                'role_id'                                       => 3,
                'receiver_id'                                   => null,
                'notification_type'                             => $event_data['notification_type'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title'                            => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'role_id'                                       => null,
                'notification_type'                             => $event_data['notification_type'],
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_ipd_previous_obstetric_history' && $event_data['is_active']==1){
             
            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title'                            => $event_data['subject'],
                'notification_desc'                             => $staff_message,
                'role_id'                                       => 3,
                'receiver_id'                                   => $event_variables['doctor_id'],
                'notification_type'                             => $event_data['notification_type'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title'                            => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'role_id'                                       => null,
                'notification_type'                             => $event_data['notification_type'],
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='opd_new_checkup_created' && $event_data['is_active']==1){
             
            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title'                            => $event_data['subject'],
                'notification_desc'                             => $staff_message,
                'role_id'                                       => 3,
                'receiver_id'                                   => $event_variables['doctor_id'],
                'notification_type'                             => $event_data['notification_type'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'role_id'                                       => null,
                'notification_type'                             => $event_data['notification_type'],
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='notification_opd_prescription_created' && $event_data['is_active']==1){

            foreach ($notification_array['visible_module'] as $key => $visible_value) {
                $role_id = $visible_value;

                $role_data = $this->_CI->role_model->getRolefromid($role_id);
                foreach ($role_data as $key => $role_value) {
                    
                    $notification_data[] = array(
                        'notification_title' => $event_data['subject'],
                        'notification_desc'  => $staff_message,
                        'role_id'            => $role_id,
                        'notification_type'  => $event_data['notification_type'],
                        'receiver_id'        => $role_value["staff_id"],
                        'date'               => date("Y-m-d H:i:s"),
                        'is_active'          => 'yes',
                    );
                }
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_opd_patient_charge' && $event_data['is_active']==1){
            
            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'  => $staff_message,
                'role_id'            =>3,
                'receiver_id'        => $event_variables['doctor_id'],
                'notification_type'  => $event_data['notification_type'],
                'date'               => date('Y-m-d H:i:s'),
                'is_active'          => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'  => $patient_message,
                'role_id'                                       => null,
                'notification_type'  => $event_data['notification_type'],
                'receiver_id'        => $event_variables['patient_id'],
                'date'               => date('Y-m-d H:i:s'),
                'is_active'          => 'yes',
                );
           
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_opd_payment' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $staff_message,
                'role_id'                                       => 3,
                'receiver_id'                                   => $event_variables['doctor_id'],
                'notification_type'                             => $event_data['notification_type'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'notification_type'                             => $event_data['notification_type'],
                'role_id'                                       => null,
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);
          
        }elseif($event=='add_nurse_note' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                foreach($notification_array as $notification_array_value){

                    $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => 3,
                    'receiver_id'        => $notification_array_value['consult_doctor'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                    );
                }
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'notification_type'                             => $event_data['notification_type'],
                'role_id'                                       => null,
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
            );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='move_in_ipd_from_opd' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'  => $staff_message,
                'role_id'            => 3,
                'receiver_id'        => $event_variables['doctor_id'],
                'notification_type'  => $event_data['notification_type'],
                'date'               => date('Y-m-d H:i:s'),
                'is_active'          => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'notification_type'                             => $event_data['notification_type'],
                'role_id'                                       => null,
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
            );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_opd_operation' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'  => $staff_message,
                'role_id'            =>3,
                'receiver_id'        => $event_variables['doctor_id'],
                'notification_type'  => $event_data['notification_type'],
                'date'               => date('Y-m-d H:i:s'),
                'is_active'          => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'notification_type'                             => $event_data['notification_type'],
                'role_id'                                       => null,
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
            );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='ipd_visit_created' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $staff_message,
                'role_id'                                       =>3,
                'receiver_id'                                   => $event_variables['doctor_id'],
                'notification_type'                             => $event_data['notification_type'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'notification_type'                             => $event_data['notification_type'],
                'role_id'                                       => null,
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='notification_ipd_prescription_created' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                foreach($notification_array as $notification_array_value){
                    $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => $notification_array_value['role_id'],
                    'receiver_id'        => $notification_array_value['consult_doctor'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                    );
                }
            }
            
            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'notification_type'                             => $event_data['notification_type'],
                'receiver_id'                                   => $event_variables['patient_id'],
                'role_id'                                       => null,
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
            );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_consultant_register' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                foreach($notification_array as $notification_array_value){
                    $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => 3,
                    'receiver_id'        => $notification_array_value['consult_doctor'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                    );
                }
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'notification_type'                             => $event_data['notification_type'],
                'role_id'                                       => null,
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
            );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_ipd_operation' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                foreach($notification_array as $notification_array_value){

                    $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            =>3,
                    'receiver_id'        => $notification_array_value['consult_doctor'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                    );
                }
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'notification_type'                             => $event_data['notification_type'],
                'role_id'                                       => null,
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
            );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_ipd_payment' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                foreach($notification_array as $notification_array_value){
                    $notification_data[] = array(
                        'notification_title' => $event_data['subject'],
                        'notification_desc'  => $staff_message,
                        'role_id'            => 3,
                        'receiver_id'        => $notification_array_value['consult_doctor'],
                        'notification_type'  => $event_data['notification_type'],
                        'date'               => date('Y-m-d H:i:s'),
                        'is_active'          => 'yes',
                    );
                }
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'notification_type'                             => $event_data['notification_type'],
                'role_id'                                       => null,
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
            );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_opd_discharge_patient' && $event_data['is_active']==1){

            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'                             => $patient_message,
                'notification_type'                             => $event_data['notification_type'],
                'role_id'                                       => null,
                'receiver_id'                                   => $event_variables['patient_id'],
                'date'                                          => date('Y-m-d H:i:s'),
                'is_active'                                     => 'yes',
            );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_ipd_medication_dose' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                foreach($notification_array as $notification_array_value){
                    $notification_data[] = array(
                        'notification_title' => $event_data['subject'],
                        'notification_desc'  => $staff_message,
                        'role_id'            =>3,
                        'receiver_id'        => $notification_array_value['consult_doctor'],
                        'notification_type'  => $event_data['notification_type'],
                        'date'               => date('Y-m-d H:i:s'),
                        'is_active'          => 'yes',
                    );
                }
            }
 
            if($event_data['is_patient']){
                $notification_data[] = array(
                'notification_title' => $event_data['subject'],
                'notification_desc'  => $patient_message,
                'notification_type'  => $event_data['notification_type'],
                'role_id'                                       => null,
                'receiver_id'        => $event_variables['patient_id'],
                'date'               => date('Y-m-d H:i:s'),
                'is_active'          => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_ipd_patient_charge' && $event_data['is_active']==1){
            
            if($event_data['is_staff']){
                foreach($notification_array as $notification_array_value){
                    $notification_data[] = array(
                        'notification_title' => $event_data['subject'],
                        'notification_desc'  => $staff_message,
                        'role_id'            => 3,
                        'receiver_id'        => $notification_array_value['consult_doctor'],
                        'notification_type'  => $event_data['notification_type'],
                        'date'               => date('Y-m-d H:i:s'),
                        'is_active'          => 'yes',
                    );
                }
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_ipd_discharge_patient' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                foreach($notification_array as $notification_array_value){
                    $notification_data[] = array(
                        'notification_title' => $event_data['subject'],
                        'notification_desc'  => $staff_message,
                        'role_id'            => 3,
                        'receiver_id'        => $notification_array_value['consult_doctor'],
                        'notification_type'  => $event_data['notification_type'],
                        'date'               => date('Y-m-d H:i:s'),
                        'is_active'          => 'yes',
                    );
                }
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'role_id'                                       => null,
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_ipd_generate_bill' && $event_data['is_active']==1){

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_opd_generate_bill' && $event_data['is_active']==1){

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='pharmacy_generate_bill' && $event_data['is_active']==1){

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'role_id'                                       => null,
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_medicine' && $event_data['is_active']==1){

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_bad_stock' && $event_data['is_active']==1){

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='purchase_medicine' && $event_data['is_active']==1){

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='pathology_investigation' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            =>3,
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'role_id'                                       => null,
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='pathology_sample_collection' && $event_data['is_active']==1){
          
            if($event_data['is_staff']){

                if($event_variables['doctor_id']!=""){
                    $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => $event_variables['role_id'],
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );

                }               
          
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'role_id'                                       => null,
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='pathology_test_report' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            =>3,
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );          
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'role_id'                                       => null,
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);


        }elseif($event=='radiology_investigation' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => 3,
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );
            }
            
            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='radiology_sample_collection' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                if($event_variables['doctor_id']){

                    $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => $event_variables['role_id'],
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                    );
                }                
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);


        }elseif($event=='radiology_test_report' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                if($event_variables['doctor_id']!=""){

                    $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => 3,
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                    );
                }                
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_bag_stock' && $event_data['is_active']==1){

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='blood_issue' && $event_data['is_active']==1){

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'role_id'                              =>0,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_component_of_blood' && $event_data['is_active']==1){
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='component_issue' && $event_data['is_active']==1){

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'role_id'                                       => null,
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='live_opd_consultation_add' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => 3,
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='live_ipd_consultation_add' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => 3,
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='patient_consultation_add' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => 3,
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='live_opd_consultation_start' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => 3,
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='live_ipd_consultation_start' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => 3,
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );
            }

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='live_meeting_start' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                foreach($notification_array as $notification_array_value){

                    $notification_data[] = array(
                        'notification_title' => $event_data['subject'],
                        'notification_desc'  => $staff_message,
                        'role_id'            => $notification_array_value['role_id'],
                        'receiver_id'        => $notification_array_value['consult_doctor'],
                        'notification_type'  => $event_data['notification_type'],
                        'date'               => date('Y-m-d H:i:s'),
                        'is_active'          => 'yes',
                    );
                }
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='live_meeting_add' && $event_data['is_active']==1){

            if($event_data['is_staff']){
                foreach($notification_array as $notification_array_value){

                    $notification_data[] = array(
                        'notification_title' => $event_data['subject'],
                        'notification_desc'  => $staff_message,
                        'role_id'            => $notification_array_value->role_id,
                        'receiver_id'        => $notification_array_value->id,
                        'notification_type'  => $event_data['notification_type'],
                        'date'               => date('Y-m-d H:i:s'),
                        'is_active'          => 'yes',
                    );
                }
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_referral_payment' && $event_data['is_active']==1){

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='patient_certificate_generate' && $event_data['is_active']==1){
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='patient_id_card_generate'){

        }elseif($event=='generate_staff_id_card' && $event_data['is_active']==1){

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='create_ambulance_call' && $event_data['is_active']==1){

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_birth_record' && $event_data['is_active']==1){

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                    'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['mother_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_death_record' && $event_data['is_active']==1){

            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $patient_message,
                     'notification_type'                             => $event_data['notification_type'],
                    'receiver_id'                                   => $event_variables['patient_id'],
                    'role_id'                                       => null,
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='staff_enabale_disable' && $event_data['is_active']==1){

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='staff_generate_payroll' && $event_data['is_active']==1){
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='staff_leave' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $staff_message,
                    'role_id'                                       => $event_variables['role_id'],
                    'receiver_id'                                   => $event_variables['staff_id'],
                    'notification_type'                             => $event_data['notification_type'],
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_payroll_payment' && $event_data['is_active']==1){
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='staff_leave_status' && $event_data['is_active']==1){

            if($event_data['is_staff']){

                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'                             => $staff_message,
                    'role_id'                                       => $event_variables['role_id'],
                    'receiver_id'                                   => $event_variables['staff_id'],
                    'notification_type'                             => $event_data['notification_type'],
                    'date'                                          => date('Y-m-d H:i:s'),
                    'is_active'                                     => 'yes',
                );
            }
            
            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);

        }elseif($event=='add_opd_medication_dose' && $event_data['is_active']==1){
            
            if($event_data['is_staff']){

                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $staff_message,
                    'role_id'            => 3,
                    'receiver_id'        => $event_variables['doctor_id'],
                    'notification_type'  => $event_data['notification_type'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );
            }
 
            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $patient_message,
                    'notification_type'  => $event_data['notification_type'],
                    'role_id'                                       => null,
                    'receiver_id'        => $event_variables['patient_id'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);
        }elseif($event=='ipd_patient_discharge_revert' && $event_data['is_active']==1){
 
            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $patient_message,
                    'notification_type'  => $event_data['notification_type'],
                    'role_id'            => null,
                    'receiver_id'        => $event_variables['patient_id'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);
            
        }elseif($event=='opd_patient_discharge_revert' && $event_data['is_active']==1){
            
            if($event_data['is_patient']){
                $notification_data[] = array(
                    'notification_title' => $event_data['subject'],
                    'notification_desc'  => $patient_message,
                    'notification_type'  => $event_data['notification_type'],
                    'role_id'            => null,
                    'receiver_id'        => $event_variables['patient_id'],
                    'date'               => date('Y-m-d H:i:s'),
                    'is_active'          => 'yes',
                );
            }

            $this->_CI->notification_model->addSystemNotificationbatch($notification_data);
        }
    }

    public function get_template_message($variables,$template_message){

        foreach ($variables as $key => $value) { 
            if($value){
                $template_message = str_replace('{{' . $key . '}}', $value, $template_message);
            }
        }
       
        return $template_message;
    }
}