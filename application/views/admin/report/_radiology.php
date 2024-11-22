 <style type="text/css">
    .reportlists {
    margin: 0;
    padding: 0;
    list-style: none;
}</style> 
<div class="box-header with-border">
    <h3 class="box-title"><?php echo $this->lang->line('radiology'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists"> 
      <?php  if ($this->module_lib->hasActive('radiology')) {
        if ($this->rbac->hasPrivilege('radiology_patient_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/radio/radiologyreport'); ?>"><a href="<?php echo base_url(); ?>admin/radio/radiologyreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('radiology_patient_report'); ?></a></li>
    <?php
        }
        } ?>   
    </ul>
</div>