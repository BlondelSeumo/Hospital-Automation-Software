 <style type="text/css">
    .reportlists {
    margin: 0;
    padding: 0;
    list-style: none;
}</style> 
<div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('blood_bank'); ?></h3>
                    </div>
<div class="box-body row">
    <ul class="reportlists">
               
      <?php 
      	if ($this->module_lib->hasActive('blood_bank')) {
            if ($this->rbac->hasPrivilege('blood_issue_report', 'can_view')) {
                ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/bloodbank/bloodissuereport'); ?>"><a href="<?php echo base_url(); ?>admin/bloodbank/bloodissuereport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('blood_issue_report'); ?></a></li>
        <?php
            } 
            } 

            if ($this->module_lib->hasActive('blood_bank')) {
            if ($this->rbac->hasPrivilege('component_issue_report', 'can_view')) {
                ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/bloodbank/componentissuereport'); ?>"><a href="<?php echo base_url(); ?>admin/bloodbank/componentissuereport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('component_issue_report'); ?></a></li>
        <?php 
            }
            }
            
            if ($this->module_lib->hasActive('blood_bank')) {
            if ($this->rbac->hasPrivilege('blood_donor_report', 'can_view')) {
                ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/bloodbank/blooddonorreport'); ?>"><a href="<?php echo base_url(); ?>admin/bloodbank/blooddonorreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('blood_donor_report'); ?></a></li>
        <?php
            }
            }
       ?>   
    </ul>
</div>