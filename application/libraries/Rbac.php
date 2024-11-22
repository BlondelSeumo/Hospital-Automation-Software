<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rbac
{

    private $userRoles = array();
    protected $permissions;
    public $perm_category;

    public function __construct()
    {
        $this->CI          = &get_instance();
        $this->permissions = array();
        $this->CI->config->load('mailsms');
        $this->perm_category = $this->CI->config->item('perm_category');
        self::loadPermission(); //Initiate the userroles
    }

    public function loadPermission()
    {
        $admin_session = $this->CI->session->userdata('hospitaladmin');
        if(isset($admin_session)){
        foreach ($admin_session['roles'] as $key => $role) {
            $permissions            = $this->getPermission($role);
            $this->userRoles[$role] = $permissions;
        }
        }
    }

    public function getPermission($role_id)
    {

        $role_perm = $this->CI->rolepermission_model->getPermissionByRole($role_id);

        foreach ($role_perm as $key => $value) {
            foreach ($this->perm_category as $per_cat_key => $per_cat_value) {
                if ($value->$per_cat_value == 1) {

                    $this->permissions[$value->permission_category_code . "-" . $per_cat_value] = true;
                }
            }
        }

        return $role_perm;
    }

    public function hasPrivilege($category = null, $permission = null)
    {

        $perm             = trim($category) . "-" . trim($permission);
        $roles            = $this->CI->customlib->getStaffRole();
        $logged_user_role = json_decode($roles)->name;

        if ($logged_user_role == 'Super Admin') {
            return true;
        }

        foreach ($this->userRoles as $role) {
            if ($this->hasPermission($perm)) {
                return true;
            }
        }

        return false;
    }

    public function hasPermission($permission)
    {
        return isset($this->permissions[$permission]);
    }

    public function module_permission($module_name)
    {        
        $module_perm = $this->CI->Module_model->getPermissionByModulename($module_name);
        return $module_perm;
    }

    public function unautherized()
    {
        $this->CI->load->view('layout/header');
        $this->CI->load->view('unauthorized');
        $this->CI->load->view('layout/footer');
    }

}
