<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sharecontent_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
	
	public function getShareContentDocumentsByID($share_content_id = null)
    {
        $result = array();
        $this->db->select('share_upload_contents.*,upload_contents.real_name,upload_contents.thumb_path,upload_contents.dir_path,upload_contents.img_name,upload_contents.thumb_name,upload_contents.file_type,upload_contents.mime_type,upload_contents.vid_url,upload_contents.vid_title')->from('share_upload_contents');
        $this->db->where('share_upload_contents.share_content_id', $share_content_id);
        $this->db->join('upload_contents', 'upload_contents.id = share_upload_contents.upload_content_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result();
            return $result;

        }
        return false;
    }
	
    public function getSharedUserBySharedID_old($share_content_id)
    {
        $sql= "SELECT share_content_for.*,staff.name,roles.name as role_name,staff.name as staff_first_name,staff.surname as staff_surname ,staff_roles.id as staff_role_id ,staff_role_alias.name as staff_role_name, staff.employee_id as staff_employee_id FROM `share_content_for` 
		LEFT JOIN roles on roles.id= share_content_for.group_id  
		LEFT join staff on staff.id = share_content_for.staff_id 
		LEFT JOIN staff_roles on staff_roles.staff_id =staff.id 
		LEFT JOIN roles as `staff_role_alias` on staff_role_alias.id = staff_roles.role_id  WHERE share_content_id=".$share_content_id;
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getPatientShareList($patient_id)
    {
        $sql="SELECT `share_contents`.*, `staff`.`name`, `staff`.`surname`, `staff`.`employee_id`, staff_roles.role_id FROM `share_contents` JOIN `staff` ON `share_contents`.`created_by` = `staff`.`id` JOIN `staff_roles` ON `staff_roles`.`staff_id` = `staff`.`id` WHERE share_contents.id in (SELECT share_content_id FROM `share_content_for` WHERE group_id ='patient' or patient_id='".$patient_id."')";      
        $query = $this->db->query($sql);
        return $query->result();
    } 

}