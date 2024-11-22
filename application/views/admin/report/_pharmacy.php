 <style type="text/css">
    .reportlists {
    margin: 0;
    padding: 0;
    list-style: none;
}</style>
<div class="box-header with-border">
    <h3 class="box-title"><?php echo $this->lang->line('pharmacy'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists">
    <?php
        if ($this->module_lib->hasActive('pharmacy')) {
        if ($this->rbac->hasPrivilege('pharmacy_bill_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/pharmacy/billreport'); ?>"><a href="<?php echo base_url(); ?>admin/pharmacy/billreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('pharmacy_balance_report'); ?></a></li>
    <?php
        }
        }

        if ($this->module_lib->hasActive('pharmacy')) {
        if ($this->rbac->hasPrivilege('expiry_medicine_report', 'can_view')) {
            ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/expmedicine/expmedicinereport'); ?>"><a href="<?php echo base_url(); ?>admin/expmedicine/expmedicinereport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('expiry_medicine_report'); ?></a></li>
    <?php
        }
        } ?>
    </ul>
</div>