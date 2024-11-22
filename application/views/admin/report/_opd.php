<div class="box-header with-border">
    <h3 class="box-title"><?php echo $this->lang->line('opd'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists">
    <?php
        if ($this->module_lib->hasActive('opd')) {
        if ($this->rbac->hasPrivilege('opd_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/patient/opd_report'); ?>"><a href="<?php echo base_url(); ?>admin/patient/opd_report"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('opd_report'); ?></a></li>
    <?php
        }
        }
        if ($this->module_lib->hasActive('opd')) {
        if ($this->rbac->hasPrivilege('opd_balance_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6  <?php echo set_SubSubmenu('reports/patient/opdreportbalance'); ?>"><a href="<?php echo base_url(); ?>admin/patient/opdreportbalance"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('opd_balance_report'); ?></a></li>
    <?php
        }
        }
        if ($this->module_lib->hasActive('opd')) {
        if ($this->rbac->hasPrivilege('discharge_patient_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/patient/opddischargepatientReport'); ?>"><a href="<?php echo base_url(); ?>admin/patient/opddischargepatientreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('opd_discharged_patient'); ?></a></li>
    <?php
        }
        }
    ?>
    </ul>
</div>