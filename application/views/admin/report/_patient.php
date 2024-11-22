<div class="box-header with-border">
    <h3 class="box-title"><?php echo $this->lang->line('patient'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists">
    <?php  
        if ($this->rbac->hasPrivilege('patient_visit_report', 'can_view')) {
        ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/patient/patientvisitreport'); ?>"><a href="<?php echo base_url('admin/patient/patientvisitreport'); ?>"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('patient_visit_report'); ?> </a></li>
    <?php
         }
        if($this->module_lib->hasActive('patient')){
            if ($this->rbac->hasPrivilege('patient_login_credential', 'can_view')) {
    ?>
                <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/patient/patientcredentialreport'); ?>"><a href="<?php echo base_url(); ?>admin/patient/patientcredentialreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('patient_login_credential'); ?></a></li>
    <?php
            }
        }
    ?>   
    </ul>
</div>