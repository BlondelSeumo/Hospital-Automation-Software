<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="box border0">
                    <ul class="tablists">
                          <?php
                        if ($this->rbac->hasPrivilege('symptoms_head', 'can_view')) { ?>
                            <li><a class="<?php echo set_sidebar_Submenu('setup/symptoms/symptoms_head'); ?>"  href="<?php echo base_url(); ?>admin/symptoms" ><?php echo $this->lang->line('symptoms_head'); ?></a></li>
                        <?php } ?> 
                        <?php if ($this->rbac->hasPrivilege('symptoms_type', 'can_view')) { ?>
                            <li><a  class="<?php echo set_sidebar_Submenu('setup/symptoms/symptoms_type'); ?>" href="<?php echo base_url(); ?>admin/symptoms/symptomstype" ><?php echo $this->lang->line('symptoms_type'); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
			
			<?php if ($this->rbac->hasPrivilege('symptoms_head', 'can_view')) { ?>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="box box-primary" id="exphead">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('symptoms_head_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('symptoms_head', 'can_add')) { ?>
                                <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm symptoms_head"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_symptoms_head'); ?></a>
                            <?php } ?>     
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('symptoms_head_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
						<?php if ($this->rbac->hasPrivilege('symptoms_head', 'can_view')) { ?>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('symptoms_head'); ?></th>
                                        <th><?php echo $this->lang->line('symptoms_type'); ?></th>
                                        <th><?php echo $this->lang->line('symptoms_description'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($symptomsresult)) {
                                        ?>

                                        <?php
                                    } else {
                                        foreach ($symptomsresult as $category) {
                                            ?>
                                            <tr>                                               
                                                <td class="mailbox-name"><?php echo $category['symptoms_title'] ?></td>
                                                <td class="mailbox-name"><?php echo $category['symptoms_type'] ?></td>
                                                <td class="mailbox-name"><?php echo nl2br($category['description']) ?></td>
                                                <td class="mailbox-date pull-right no-print">
                                                    <?php
                                                    if ($this->rbac->hasPrivilege('symptoms_head', 'can_edit')) {
                                                        ?>
                                                        <a data-target="#editmyModal" onclick="get(<?php echo $category['id'] ?>)" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <?php
                                                    }
                                                    if ($this->rbac->hasPrivilege('symptoms_head', 'can_delete')) {
                                                        ?>
                                                        <a class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/symptoms/delete/<?php echo $category['id'] ?>', '<?php echo $this->lang->line('delete_confirm'); ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table><!-- /.table -->
						<?php } ?>
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div>
			<?php } ?>
			
            <!-- right column -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_symptoms_head'); ?></h4> 
            </div>
            <form id="formadd" action="<?php echo site_url('admin/symptoms/add') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('symptoms_head'); ?></label><small class="req"> *</small>
                            <input autofocus="" id="symptoms_title" name="symptoms_title" placeholder="" type="text" class="form-control" value="<?php echo set_value('symptoms'); ?>" />
                            <span class="text-danger"><?php echo form_error('symptoms'); ?></span>
                        </div> 
                           <div class="form-group">
                                    <label><?php echo $this->lang->line('symptoms_type'); ?></label>
                                    <small class="req"> *</small> 
                                    <select name="type"  onchange="" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($symptomsresulttype as $value) {
                                                ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['symptoms_type']; ?></option>
                                            <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('type'); ?></span>
                                </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                            <textarea class="form-control" id="description" name="description" placeholder="" rows="3"><?php echo set_value('description'); ?></textarea>
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>              
                    </div>
                </div><!--./mpdal-->       
                <div class="modal-footer">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div><!--./row--> 
    </div>
</div>

<div class="modal fade" id="editmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_symptoms_head'); ?></h4> 
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/symptoms/edit') ?>" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('symptoms_head'); ?></label><small class="req"> *</small>
                            <input  id="symptomstitle" name="symptoms_title" placeholder="" type="text" class="form-control"  value="" />
                            <span class="text-danger"><?php echo form_error('symptoms'); ?></span>
                        </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('symptoms_type'); ?></label>
                                    <small class="req"> *</small> 
                                    <select name="type" id="symtype" onchange="" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($symptomsresulttype as $value) {
                                                ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['symptoms_type']; ?></option>
                                            <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                            </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                            <textarea class="form-control" id="description_edit" name="description" placeholder="" rows="3" ><?php echo set_value('description'); ?></textarea>
                            <input type="hidden" id="symptoms_id" name="symptoms_id">
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>                       

                    </div>
                </div><!--./modal-->        
                <div class="modal-footer">
                    <button type="submit" id="editformaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div><!--./row--> 
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
</script>
<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';     

    $("#print_div").click(function () {
        popup($('#exphead').html());
    });
</script>
<script>
    $(document).ready(function (e) {
        $('#formadd').on('submit', (function (e) {
            $("#formaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formaddbtn").button('reset');
                },
                error: function () {

                }
            });
        }));
    });

    function get(id) {
        $('#editmyModal').modal('show');
        $.ajax({
            dataType: 'Json',
            url: '<?php echo base_url(); ?>admin/symptoms/get_data/' + id,
            success: function (result) {
           console.log(result.symptoms_type)
                $('#symptoms_id').val(result.id);
                $('#symptomstitle').val(result.symptoms_title);
                $('#symtype').val(result.type);
                $('#description_edit').val(result.description);
            }
        });
    }

    $(document).ready(function (e) {
        $('#editformadd').on('submit', (function (e) {
            $("#editformaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'Json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#editformaddbtn").button('reset');
                },
                error: function () {

                }
            });
        }));
    });
	
	$(".symptoms_head").click(function(){
		$('#formadd').trigger("reset");
	});
    
    $(document).ready(function (e) {
        $('#myModal,#editmyModal').modal({
            backdrop: 'static',
            keyboard: false,
            show:false
        });
    });
</script>