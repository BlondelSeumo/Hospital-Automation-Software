<div class="box-header with-border">
    <h3 class="box-title"><?php echo $this->lang->line('birth_death_report'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists">
<?php
        if (($this->module_lib->hasActive('birth_death_report')) || ($this->module_lib->hasActive('birth_death_report'))) {
            if (($this->rbac->hasPrivilege('birth_report', 'can_view')) || ($this->rbac->hasPrivilege('death_report', 'can_view'))) {
                ?>                
                    <?php
                        if ($this->module_lib->hasActive('birth_death_report')) {
                            if ($this->rbac->hasPrivilege('birth_report', 'can_view')) {
                        ?>
                            <li class="col-lg-4 col-md-4 col-sm-6  <?php echo set_SubSubmenu('admin/birthordeath/birthreport'); ?>"><a href="<?php echo base_url(); ?>admin/birthordeath/birthreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('birth_report'); ?> </a></li>
                    <?php
                            }
                        }

                        if ($this->rbac->hasPrivilege('death_report', 'can_view')) {
                    ?>
                        <li class="col-lg-4 col-md-4 col-sm-6  <?php echo set_SubSubmenu('admin/birthordeath/deathreport'); ?>"><a href="<?php echo base_url(); ?>admin/birthordeath/deathreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('death_report'); ?></a></li>
                            <?php }?>                              
                           
    <?php
            }
        }
    ?>  
    </ul>
</div>