<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 */
class Generatecertificate_model extends CI_Model {

    function __construct() {
        parent::__construct();
      
    }

    public function getcertificatebyid($certificate,$module = NULL) {
        $this->db->select('certificates.id,certificates.certificate_name,certificates.certificate_text,certificates.left_header,certificates.center_header,certificates.right_header,certificates.left_footer,certificates.right_footer,certificates.center_footer,certificates.background_image,certificates.header_height,certificates.content_height,certificates.footer_height,certificates.content_width,certificates.enable_patient_image,certificates.enable_image_height,certificates.created_at,certificates.updated_at,certificates.created_for,certificates.status,"'.$module.'" module');
        $this->db->from('certificates');
        $this->db->where('id', $certificate);
        $query = $this->db->get();
        return $query->result();
    }

}

?>