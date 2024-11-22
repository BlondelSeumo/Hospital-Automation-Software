<div class="box-header with-border">
    <h3 class="box-title"><?php echo $this->lang->line('live_consultation'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists"> 
    <?php  
        if ($this->module_lib->hasActive('live_consultation')) {
            if ($this->rbac->hasPrivilege('live_consultation_report', 'can_view')) {
                ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/live_consultation/consult_report'); ?>"><a href="<?php echo base_url('admin/zoom_conference/consult_report'); ?>"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('live_consultation_report'); ?></a></li>
    <?php
            }
        }
        if ($this->module_lib->hasActive('live_consultation')) {
            if ($this->rbac->hasPrivilege('live_meeting_report', 'can_view')) {
                ?><li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/live_consultation/meeting_report'); ?>"><a href="<?php echo base_url('admin/zoom_conference/meeting_report'); ?>"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('live_meeting_report'); ?></a></li>
    <?php
            }
        }
    ?>   
    </ul>
</div>