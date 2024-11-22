<div class="box border0">
        <?php if ($this->rbac->hasPrivilege('referral_commission', 'can_view')) { ?>
                <ul class="tablists">
                        <li><a href="<?php echo site_url('admin/referral/commission'); ?>" class="<?php echo set_sidebar_Submenu('admin/referral/commission'); ?>"><?php echo $this->lang->line('referral_commission'); ?></a></li>
                </ul>
        <?php } ?>
        <?php if ($this->rbac->hasPrivilege('referral_category', 'can_view')) { ?>
                <ul class="tablists">
                        <li><a href="<?php echo site_url('admin/referral/category'); ?>" class="<?php echo set_sidebar_Submenu('admin/referral/category'); ?>"><?php echo $this->lang->line('referral_category'); ?></a></li>
                </ul>
        <?php } ?>
</div>
