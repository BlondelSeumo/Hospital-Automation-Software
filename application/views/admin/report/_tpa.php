<div class="box-header with-border">
    <h3 class="box-title"><?php echo $this->lang->line('tpa'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists">
		<?php                         
        if ($this->module_lib->hasActive('tpa_management')) {
			if ($this->rbac->hasPrivilege('tpa_report', 'can_view')) {  ?>
			<li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/tpamanagement/tpareport'); ?>"><a href="<?php echo base_url(); ?>admin/tpamanagement/tpareport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('tpa_report'); ?></a></li>
    <?php
        }
        }
      ?>   
    </ul>
</div>