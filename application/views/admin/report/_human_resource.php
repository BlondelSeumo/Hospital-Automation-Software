<div class="box-header with-border">
    <h3 class="box-title"><?php echo $this->lang->line('human_resource'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists">
    <?php  
      if ($this->rbac->hasPrivilege('payroll_report', 'can_view')) {
    ?>
        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/human_resource/payrollsearch'); ?>"><a href="<?php echo base_url(); ?>admin/payroll/payrollsearch"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('payroll_report'); ?></a></li>
    <?php
        } if ($this->rbac->hasPrivilege('payroll_month_report', 'can_view')) {
    ?>
        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/human_resource/payrollreport'); ?>"><a href="<?php echo base_url(); ?>admin/payroll/payrollreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('payroll_month_report'); ?></a></li>
    <?php
        }
        if ($this->rbac->hasPrivilege('staff_attendance_report', 'can_view')) {
        ?>
            <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/human_resource/attendancereport'); ?>"><a href="<?php echo base_url(); ?>admin/staffattendance/attendancereport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('staff_attendance_report'); ?></a></li>
    <?php
        } ?>
    </ul>
</div>