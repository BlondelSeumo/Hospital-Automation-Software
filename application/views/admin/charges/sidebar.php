<ul class="tablists">
    <?php if ($this->rbac->hasPrivilege('hospital_charges', 'can_view')) {?> <li>
            <a href="<?php echo base_url(); ?>admin/charges" class="<?php echo set_sidebar_Submenu('admin/charges/index'); ?>"><?php echo $this->lang->line('charges'); ?></a></li>
    <?php }?>
    <?php if ($this->rbac->hasPrivilege('charge_category', 'can_view')) {?>
        <li><a href="<?php echo base_url(); ?>admin/chargecategory/charges" class="<?php echo set_sidebar_Submenu('admin/chargecategory/charges'); ?>"><?php echo $this->lang->line('charge_category'); ?></a></li>
    <?php }?>
    
    <?php if ($this->rbac->hasPrivilege('charge_type', 'can_view')) {?>
        <li><a href="<?php echo base_url(); ?>admin/chargetype" class="<?php echo set_sidebar_Submenu('admin/chargetype/index'); ?>" ><?php echo $this->lang->line('charge_type'); ?></a></li>
    <?php }?>
    <?php if ($this->rbac->hasPrivilege('tax_category', 'can_view')) {?>
        <li><a href="<?php echo base_url(); ?>admin/taxcategory" class="<?php echo set_sidebar_Submenu('admin/taxcategory/index'); ?>" ><?php echo $this->lang->line('tax_category'); ?></a></li>
    <?php } ?>
    <?php if ($this->rbac->hasPrivilege('unit_type', 'can_view')) {?>
        <li><a class="<?php echo set_sidebar_Submenu('admin/unittype/index'); ?>" href="<?php echo site_url('admin/unittype'); ?>"> <?php echo $this->lang->line('unit_type'); ?></a></li>
    <?php } ?>
</ul>