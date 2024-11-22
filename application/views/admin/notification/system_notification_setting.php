<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php $this->load->view('setting/sidebar'); ?>
            <div class="col-md-10">
                <!-- left column -->
                <form action="<?php echo site_url('admin/notification/notification_setting') ?>"  method="post" accept-charset="utf-8">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('system_notification_setting'); ?></h3>
                        </div>
                        <div class="around10">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg');
                                $this->session->unset_userdata('msg');
                                 ?>
                            <?php } ?> 
                        </div>
                        <!-- /.box-header --> 
                        <div class="box-body"> 
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <th><?php echo $this->lang->line('event'); ?></th>
                                    <th><?php echo $this->lang->line('subject'); ?></th>
                                    <th width="5%"><?php echo $this->lang->line('option'); ?></th>
                                    <th width="30%"><?php echo $this->lang->line('staff_message'); ?></th>
                                    <th width="30%"><?php echo $this->lang->line('patient_message'); ?></th>                  
                                </thead>                              
                                 <tbody>
                                    <?php
                                        $i        = 1;
                                        $last_key = count($notificationlist);
                                        foreach ($notificationlist as $note_key => $note_value) {
                                            
                                            ?>
                                        <tr>
                                            <td>
                                                <input type="hidden" name="ids[]" value="<?php echo $note_value->id; ?>">
                                                <?php echo $this->lang->line($note_value->event); ?>
                                            </td>
                                            <td><?php echo ($note_value->subject); ?></td>                                    
                                            <td>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="event_<?php echo $note_value->id; ?>" value="1" <?php echo set_checkbox('event_' . $note_value->id, 1, set_value('event_' . $note_value->id, $note_value->is_active) ? true : false); ?>>  <?php echo $this->lang->line('enabled'); ?>
                                                </label>
                                                <br>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="staff_<?php echo $note_value->id; ?>" value="1" <?php echo set_checkbox('sms_' . $note_value->id, 1, set_value('staff_' . $note_value->id, $note_value->is_staff) ? true : false); ?>>
                                                    <?php echo $this->lang->line('staff'); ?>
                                                </label>
                                                <br>
                                                <?php if(!in_array($note_value->event,$is_patient_notification)){
                                              	?> 
                                              	<label class="checkbox-inline">
                                                    <input type="checkbox" name="patient_<?php echo $note_value->id; ?>" value="1" <?php echo set_checkbox('notification_' . $note_value->id, 1, set_value('patient_' . $note_value->id, $note_value->is_patient) ? true : false); ?>>
                                                    <?php echo $this->lang->line('patient')?>
                                                </label>
                                              	<?php
                                              }?>                                    

                                            </td>
                                            <td><?php echo ($note_value->staff_message); ?></td>
                                            <td><?php echo ($note_value->patient_message); ?>   
                                            <br>
                                            <?php if ($this->rbac->hasPrivilege('system_notification_setting', 'can_edit')) { ?>
   											    <button type="button" class="button_template btn btn-primary btn-xs " id="load" data-record-id="<?php echo $note_value->id; ?>" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-pencil-square-o"></i></button>
                                            <?php } ?>
                                            </td> 
                                        </tr>
                                        <?php
                                    $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        </div> 
                      <?php if ($this->rbac->hasPrivilege('system_notification_setting', 'can_edit')) { ?>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('processing'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        </div> 
                       <?php } ?>                             
            </div>
            </form>   
        </div>
</div><!--./wrapper-->

</section><!-- /.content -->
</div>
<div class="modal fade" id="templateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content mx-2">
            <form action="<?php echo site_url('admin/notification/save_system_notification') ?>" method="post" id="templateForm">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"> <?php echo $this->lang->line('edit_notification'); ?></h4>
            </div>
            <div class="modal-body template_modal_body">
            </div>
            <div class="modal-footer">
                <?php if ($this->rbac->hasPrivilege('system_notification_setting', 'can_edit')) { ?>
               <button type="submit" class="template_update btn btn-primary" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('processing'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
           <?php } ?>

            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', '.button_template', function() {
            $('.template_message_error').html("");
             var $this = $(this);
             var id=$this.data('recordId');
            $this.button('loading');
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: baseurl+"admin/notification/getsystem_notification_setting",
                data: {'id':id}, 
                beforeSend: function() {
                },
                success: function(data) {
                   if(data.status){
                    $('#templateModal').modal('show');
                    $('.template_modal_body').html(data.template);

                   }
                },
                error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
 $this.button('reset');
                },
                complete: function() {
 $this.button('reset');
                }
            });
        });

    });

$("#templateForm").submit(function(e) {
	$('.template_message_error').html("");
	var submit_btn = $(this).find("button[type=submit]:focus" );
    var form = $(this);
    var url = form.attr('action');
    $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: form.serialize(), // serializes the form's elements.
               beforeSend: function() {
                 submit_btn.button('loading');
                },
                success: function(data) {
                   if(data.status){
                 successMsg(data.message);
                 window.location.reload(true);
                   }else{
                    $.each(data.error,function(key,val){
                    $('.'+key+'_error').html(val);

                    });
                   }
                },
                error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
     submit_btn.button('reset');
                },
                complete: function() {
    submit_btn.button('reset');
                }
         });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

</script>
<?php

function checkExists($notificationlist, $key) {
    foreach ($notificationlist as $not_key => $not_value) {
        if ($not_value->type == $key) {
            return array(
                'is_mail' => $not_value->is_mail,
                'is_sms' => $not_value->is_sms,
                'is_mobileapp' => $not_value->is_mobileapp
            );
        }
    }
    return false;
}
?>