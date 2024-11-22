<?php

class Notification_model extends CI_Model {

    public function getNotifications($patient_id)
    {
        $query = $this->db->select("system_notification.*,read_systemnotification.is_active as read")
            ->join('read_systemnotification', "system_notification.id = read_systemnotification.notification_id", "left")
            ->where(array('system_notification.receiver_id' => $patient_id))->order_by('id', 'desc')
            ->get("system_notification");

        return $query->result_array();
    }
    
    public function addSystemNotificationbatch($notification_data)
    {          
        $this->db->insert_batch('system_notification', $notification_data);
    }
    
}

?>