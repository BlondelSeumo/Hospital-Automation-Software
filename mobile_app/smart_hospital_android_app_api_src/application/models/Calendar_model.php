<?php

class Calendar_model extends CI_Model {

    public function getPublicEvents($patient_id, $date_from, $date_to)
    {
        $this->db->where("(event_type='public' OR (event_type='task' and event_for=" . $this->db->escape($patient_id) . "))", null, false);
        $this->db->where('start_date BETWEEN "' . $date_from . '" AND "' . $date_to . '" OR (event_type="public" OR (event_type="task" and event_for=' . $this->db->escape($patient_id) . ')) AND "' . $date_from . '" BETWEEN start_date AND end_date');
        $query = $this->db->get('events');
        return $query->result();
    }   

    public function getNotificationsThisMonth_old($id)
    {
        $this->db->select('system_notification.*');
        $this->db->from('system_notification');
        $this->db->where('system_notification.receiver_id', $id);
        $this->db->where('system_notification.notification_for', "Patient");
        $this->db->like('system_notification.date', date('Y-m'));
        $this->db->order_by('date', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function getNotificationsThisMonth($id)
    {
        $date = date('Y-m');
        $query        = $this->db->select("system_notification.*")
            ->where(array('role_id' => null, 'receiver_id' => $id))
            ->where("is_active", "yes")
            ->like("date", $date)
            ->get("system_notification");
        $result = $query->result_array();       
        $data = $result;
        return $data;
    }
    
    public function todaysTaskCount($id)
    {
        $this->db->select('events.*');
        $this->db->from('events');
        $this->db->where('events.event_for', $id);
        $this->db->where('events.event_type', 'task');
        $this->db->like('events.start_date', date("Y-m-d"));
        $query = $this->db->get();
        return $query->result_array();
    }

}

?>