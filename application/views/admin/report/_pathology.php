<div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('pathology'); ?></h3>
                    </div>
<div class="box-body row">
    <ul class="reportlists">               
      <?php  if ($this->module_lib->hasActive('pathology')) {
        if ($this->rbac->hasPrivilege('pathology_patient_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6  <?php echo set_SubSubmenu('reports/pathology/pathologyreport'); ?>"><a href="<?php echo base_url(); ?>admin/pathology/pathologyreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('pathology_patient_report'); ?></a></li>
    <?php
        }
        }?>   
    </ul>
</div>