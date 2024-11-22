<div class="box border0">
    <ul class="tablists">

        <?php if ($this->rbac->hasPrivilege('bed_status', 'can_view')) { ?>

            <li><a href="<?php echo site_url('admin/setup/bed/status') ?>" class="<?php echo set_sidebar_Submenu('admin/setup/bed/status'); ?>"><?php echo $this->lang->line('bed_status'); ?></a></li>

        <?php } if ($this->rbac->hasPrivilege('bed', 'can_view')) { ?>

            <li><a href="<?php echo site_url('admin/setup/bed') ?>" class="<?php echo set_sidebar_Submenu('admin/setup/bed'); ?>"><?php echo $this->lang->line('bed'); ?></a></li>

        <?php } if ($this->rbac->hasPrivilege('bed_type', 'can_view')) { ?>  

            <li><a href="<?php echo site_url('admin/setup/bedtype') ?>" class="<?php echo set_sidebar_Submenu('admin/setup/bedtype'); ?>"><?php echo $this->lang->line('bed_type'); ?></a></li>

        <?php } if ($this->rbac->hasPrivilege('bed_group', 'can_view')) { ?>

            <li><a href="<?php echo site_url('admin/setup/bedgroup') ?>" class="<?php echo set_sidebar_Submenu('admin/setup/bedgroup'); ?>"><?php echo $this->lang->line('bed_group'); ?></a></li>

        <?php } if ($this->rbac->hasPrivilege('floor', 'can_view')) { ?>

            <li><a href="<?php echo site_url('admin/setup/floor') ?>" class="<?php echo set_sidebar_Submenu('admin/setup/floor'); ?>"><?php echo $this->lang->line('floor'); ?></a></li>  

        <?php } ?>              
                                                          
    </ul>
</div>
