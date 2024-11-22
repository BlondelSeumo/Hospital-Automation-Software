<div class="box-header with-border">
    <h3 class="box-title"><?php echo $this->lang->line('log'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists">               
    <?php                                   
        if ($this->rbac->hasPrivilege('user_log', 'can_view')) {
    ?>
        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/log/userlog'); ?>"><a href="<?php echo base_url(); ?>admin/userlog"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('user_log'); ?></a></li>
    <?php
     } if ($this->rbac->hasPrivilege('email_sms_log', 'can_view')) { ?>
            <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/log/mailsms'); ?>"><a href="<?php echo base_url(); ?>admin/mailsms"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('email_sms_log'); ?></a></li>
    <?php } if ($this->rbac->hasPrivilege('audit_trail_report', 'can_view')) { ?>
            <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/log/audit'); ?>"><a href="<?php echo base_url(); ?>admin/audit"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('audit_trail_report'); ?></a></li>
    <?php }  ?>                           
    </ul>
</div>