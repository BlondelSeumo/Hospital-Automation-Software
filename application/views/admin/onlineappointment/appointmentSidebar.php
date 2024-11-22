<div class="box border0">
	<ul class="tablists">

		<?php if ($this->rbac->hasPrivilege('online_appointment_slot', 'can_view')) { ?>
    
			<li><a href="<?php echo site_url('admin/onlineappointment'); ?>" class="<?php echo set_sidebar_Submenu('admin/onlineappointment'); ?>"><?php echo $this->lang->line("slots"); ?></a></li>
    
		<?php } ?>
		<?php if ($this->rbac->hasPrivilege('online_appointment_doctor_shift', 'can_view')) { ?>
    
				<li><a href="<?php echo site_url('admin/onlineappointment/doctorglobalshift'); ?>" class="<?php echo set_sidebar_Submenu('admin/onlineappointment/doctorglobalshift'); ?>"><?php echo $this->lang->line("doctor_shift"); ?></a></li>
    
		<?php } ?>
		<?php if ($this->rbac->hasPrivilege('online_appointment_shift', 'can_view')) { ?>
     
				<li><a href="<?php echo site_url('admin/onlineappointment/globalshift'); ?>" class="<?php echo set_sidebar_Submenu('admin/onlineappointment/globalshift'); ?>"><?php echo $this->lang->line("shift"); ?></a></li>
    
		<?php } ?>
		<?php if ($this->rbac->hasPrivilege('appointment_priority', 'can_view')) { ?>
    
            <li><a href="<?php echo site_url('admin/appointpriority/index'); ?>" class="<?php echo set_sidebar_Submenu('admin/appointpriority/index'); ?>"><?php echo $this->lang->line("appointment_priority"); ?></a></li>
			
		<?php } ?>   
     
    </ul>
</div>
