<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Content Wrapper. Contains page content -->
<style type="text/css">
    @media (max-width: 767px) {
  .box-header.with-border {padding-bottom: 8px; }
}
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid1 box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('notice_board'); ?></h3>
                        <?php if ($this->rbac->hasPrivilege('notice_board', 'can_add')) { ?>
                            <div class="box-tools pull-right">
                                <a href="<?php echo base_url() ?>admin/notification/add" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?php echo $this->lang->line('post_new_message'); ?></a>
                                <?php
                            }
                            if ($this->rbac->hasPrivilege('email_sms', 'can_view')) {
                                ?>
                                <a href="<?php echo base_url(); ?>admin/mailsms/compose" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('send_sms'); ?></a>
                            <?php } if ($this->rbac->hasPrivilege('email_sms', 'can_view')) {
                                ?>
                                <a href="<?php echo base_url(); ?>admin/mailsms/composemail" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('send_email'); ?></a>
                            <?php } ?>
							
							<?php if($this->rbac->hasPrivilege('send_credential', 'can_view')){ ?>
								<a href="<?php echo base_url(); ?>admin/bulkmessage" class="btn btn-primary btn-sm"><i class="fa fa-key"></i> <?php echo $this->lang->line('send_credential'); ?></a>
							<?php } ?> 
							 
                        </div>

                    </div>                  
                    <div class="box-body">
                        <div class="box-group" id="accordion">                          
                            <?php if (empty($notificationlist)) {
                                ?>
                                <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                <?php
                            } else {
                                foreach ($notificationlist as $key => $notification) {
                                    $role_name = $notification["role_name"];
                                    ?>
                                                 
                                    <div class="panel box box-primary">
                                        <div class="box-header with-border">
                                            <h4 class="box-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $notification['id']; ?>" aria-expanded="false" class="collapsed" data-msgid="<?php echo $notification['id']; ?>">
                                                    <?php echo $notification['title']; ?>
                                                </a>
                                            </h4>
                                            <div class="pull-right pt5">
                                                <?php if (($this->rbac->hasPrivilege('notice_board', 'can_edit')) || ($notification["created_id"] == $user_id)) { ?>
                                                    <a href="<?php echo base_url() ?>admin/notification/edit/<?php echo $notification['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" data-original-title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    &nbsp; <?php } if (($this->rbac->hasPrivilege('notice_board', 'can_delete')) || ($notification["created_id"] == $user_id)) { ?>                                            
                                                    <a onclick="delete_record(<?php echo $notification['id'] ?>)"  class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" data-original-title="<?php echo $this->lang->line('delete'); ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                       
                                        <div id="collapse<?php echo $notification['id']; ?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                            <div class="box-body">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <?php echo $notification['message']; ?>
                                                    </div><!-- /.col -->
                                                    <div class="col-md-3">
                                                        <div class="">
                                                            <div class="box-body">
                                                                <ul class="nav nav-pills">
                                                                    <li><i class="fa fa-calendar-check-o"></i> <?php echo $this->lang->line('publish_date'); ?> : <?php echo $this->customlib->YYYYMMDDTodateFormat($notification['publish_date']); ?> </li>
                                                                    <li><i class="fa fa-calendar"></i> <?php echo $this->lang->line('notice_date'); ?> : <?php echo $this->customlib->YYYYMMDDTodateFormat($notification['date']); ?> </li>

                                                                </ul>
                                                                <h4 class="text text-primary ptt10"><?php echo $this->lang->line('message_to'); ?></h4>
                                                                <ul class="nav nav-pills nav-stacked">
                                                                    <?php foreach ($role_name as $key => $role_value) {
                                                                        ?>

                                                                        <li>
                                                                            <i class="fa fa-user" aria-hidden="true"></i>
                                                                            <?php echo $role_value['name']; ?>
                                                                        </li>
                                                                    <?php } ?>

                                                                    <?php ?> 
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                
                            }
                            }
                            ?>
                        </div>
                    </div>                   
                </div>
            </div>           
        </div>
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
        $('.date').datepicker({
            format: date_format,
            autoclose: true
        });

        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });

    });

</script>
<script>
    $(function () {

        $("#compose-textarea").wysihtml5();
    });
</script>
<script>
    $(document).on('click', '.collapsed', function () {
        var base_url = '<?php echo base_url() ?>';
        var notice = $(this).data('msgid');
        $.ajax({
            type: "POST",
            url: base_url + "admin/notification/read",
            data: {'notice': notice},
            dataType: "json",
            success: function (data) {
            }
        });
    });
    
    function delete_record(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/notification/delete/' + id,
                    success: function (res) {
                        successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                        window.location.reload(true);
                    },
                    error: function () {
                        alert("Fail")
                    }
                });
        }
    }      
</script>