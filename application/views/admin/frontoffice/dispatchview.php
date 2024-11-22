<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('postal_dispatch_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <div class="box-tools pull-right">
                                <?php if ($this->rbac->hasPrivilege('postal_dispatch', 'can_add')) {?>
                                    <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm adddispatch"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_dispatch'); ?> </a>
                                <?php }?>
                            </div>
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('postal_dispatch_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <table class="table table-hover table-striped table-bordered ajaxlist" data-export-title="<?php echo $this->lang->line('postal_dispatch_list') ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('to_title'); ?></th>
                                        <th><?php echo $this->lang->line('reference_no'); ?></th>
                                        <th><?php echo $this->lang->line('from_title'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->

            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<!-- new END -->
<div id="receviedetails" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body" id="getdetails">
            </div>
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_dispatch'); ?></h4>
            </div>
            <form id="formadd" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="scroll-area">
                    <div class="modal-body pt0 pb0">
                        <div class="ptt10">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('to_title'); ?></label>   <small class="req"> *</small>
                                        <input type="text" class="form-control" value="<?php echo set_value('from_title'); ?>" name="to_title">
                                        <span class="text-danger"><?php echo form_error('from_title'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('reference_no'); ?></label>
                                        <input type="text" class="form-control" value="<?php echo set_value('ref_no'); ?>" name="ref_no">
                                        <span class="text-danger"><?php echo form_error('ref_no'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('address'); ?></label>
                                        <textarea class="form-control" id="description"  name="address" rows="3"><?php echo set_value('address'); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control" id="description" name="note" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('from_title'); ?></label>
                                        <input type="text" class="form-control" value="<?php echo set_value('to_title'); ?>"  name="from_title">
                                        <span class="text-danger"><?php echo form_error('to_title'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('date'); ?></label>
                                        <input id="date" name="date" placeholder="" type="text" class="form-control"  value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly" />
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <div><input class="filestyle form-control" type='file' name='file'  />
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span></div>
                                </div>
                            </div><!-- /.box-body -->
                        </div>
                    </div><!--./modal-body-->
                </div>
                <div class="modal-footer">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="formaddbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /.content-wrapper -->

<div id="editmyModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_dispatch'); ?></h4>
            </div>
        <form id="editformadd" method="post" accept-charset="utf-8" enctype="multipart/form-data" class="ptt10">    
            <div class="scroll-area">
                    <div class="modal-body pb0 pt0">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('to_title'); ?></label><small class="req"> *</small>
                                        <input type="text" id="efrom_title" class="form-control" value="<?php echo set_value('from_title'); ?>" name="to_title">
                                        <input type="hidden" name="id" id="id">
                                        <span class="text-danger"><?php echo form_error('from_title'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('reference_no'); ?></label>
                                        <input type="text" id="ref_no" class="form-control" value="<?php echo set_value('ref_no'); ?>" name="ref_no">
                                        <span class="text-danger"><?php echo form_error('ref_no'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('address'); ?></label>
                                        <textarea class="form-control" id="eaddress"  name="address" rows="3"><?php echo set_value('address'); ?></textarea>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control" id="enote"  name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('from_title'); ?></label>
                                        <input type="text" id="to_title" class="form-control" value="<?php echo set_value('to_title'); ?>"  name="from_title">
                                        <span class="text-danger"><?php echo form_error('to_title'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('date'); ?></label>
                                        <input id="edate" name="date" placeholder="" type="text" class="form-control"  value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly" />
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <div><input class="filestyle form-control" type='file' name='file'  />
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span></div>
                                </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="editformaddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';

        $('#date').datepicker({

            format: date_format,
            autoclose: true
        });
        $('#edate').datepicker({

            format: date_format,
            autoclose: true
        });
    });
    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';

        $('#date_of_call').datepicker({

            format: date_format,
            autoclose: true
        });
    });
    function getRecord(id) {
        $('#receviedetails').modal('show');

        $.ajax({
            url: '<?php echo base_url(); ?>admin/dispatch/details/' + id + '/dispatch',
            success: function (result) {

                $('#getdetails').html(result);
            }
        });
    }

   
    function get(id) {
        $('#editmyModal').modal({backdrop:"static"});
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/receive/get_receive/' + id,
            success: function (result) {

                $('#efrom_title').val(result.to_title);
                $('#ref_no').val(result.reference_no);
                $('#ename').val(result.address);
                $('#to_title').val(result.from_title);
                $('#edate').val(result.datedd);
                $('#eaddress').val(result.address);
                $('#enote').val(result.note);
                $('#id').val(result.id);
                $('#eaction_taken').val(result.action_taken);
                $('#eassigned').val(result.assigned);
            }
        });
    }

    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {
            $("#formaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/dispatch/add',
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
     function delete_ById(id){
        if (confirm('<?php echo $this->lang->line('delete_confirm')?>')) {
         $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/dispatch/imagedelete/' + id,
            success: function (result) {
                successMsg(result.msg);
                table.ajax.reload();
            }
        });
     }
     }
    $(document).ready(function (e) {
        $("#editformadd").on('submit', (function (e) {
            $("#editformaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/dispatch/editdispatch',
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
                    $("#editformaddbtn").button('reset');
                },
                error: function () {

                }
            });

        }));
    });

$(".adddispatch").click(function(){
    $('#formadd').trigger("reset");
    $(".dropify-clear").trigger("click");
});

    $(document).ready(function (e) {
        $('#myModal,#receviedetails').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
    
</script>
 
<!-- //========datatable start===== -->
 <script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/dispatch/getdispatchdatatable');
    });
} ( jQuery ) )
</script> 
<!-- //========datatable end===== -->

