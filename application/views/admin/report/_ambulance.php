<div class="box-header with-border">
	<h3 class="box-title"><?php echo $this->lang->line('ambulance'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists">               
		<?php  
		if ($this->module_lib->hasActive('ambulance')) {
			if ($this->rbac->hasPrivilege('ambulance_report', 'can_view')) {  ?>
			<li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/ambulance/ambulancereport'); ?>"><a href="<?php echo base_url(); ?>admin/vehicle/ambulancereport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('ambulance_report'); ?></a></li>
                                <?php
                                    }
                                    }
      ?>   
    </ul>
</div>