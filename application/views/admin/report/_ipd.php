 <style type="text/css">
    .reportlists {
    margin: 0;
    padding: 0;
    list-style: none;
}</style>
<div class="box-header with-border">
    <h3 class="box-title"><?php echo $this->lang->line('ipd'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists">
    <?php
         if ($this->module_lib->hasActive('ipd')) {
        if ($this->rbac->hasPrivilege('ipd_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/patient/ipdreport'); ?>"><a href="<?php echo base_url(); ?>admin/patient/ipdreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('ipd_report'); ?></a></li>
    <?php
        } 
        }

        if ($this->module_lib->hasActive('ipd')) {
        if ($this->rbac->hasPrivilege('ipd_balance_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/patient/ipdreportbalance'); ?>"><a href="<?php echo base_url(); ?>admin/patient/ipdreportbalance"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('ipd_balance_report'); ?></a></li>
    <?php
        }
        }
        
        if ($this->module_lib->hasActive('ipd')) {
        if ($this->rbac->hasPrivilege('discharge_patient_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/patient/dischargepatientreport'); ?>"><a href="<?php echo base_url(); ?>admin/patient/dischargepatientreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('ipd_discharged_patient'); ?></a></li>
    <?php
        }
        }
        
         ?>              
    </ul>
</div>