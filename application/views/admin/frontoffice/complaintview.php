<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('complain_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <div class="box-tools pull-right">
                                <?php if ($this->rbac->hasPrivilege('complain', 'can_add')) {?>
                                    <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm complain"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_complain'); ?> </a>
                                <?php }?>
                            </div>
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"></div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped table-bordered ajaxlist" data-export-title="<?php echo $this->lang->line('complain_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('complain'); ?> #  </th>
                                        <th><?php echo $this->lang->line('complain_type'); ?></th>
                                        <th><?php echo $this->lang->line('source'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
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
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- new END -->
<div id="complaintdetails" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
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
                <h4 class="modal-title"><?php echo $this->lang->line('add_complain'); ?></h4>
            </div>
            <form id="formadd" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="scroll-area">
                    <div class="modal-body pt0 pb0">
                        <div class="ptt10">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('complain_type'); ?></label>
                                        <select name="complaint" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($complaint_type as $key => $value) {?>
                                                <option value="<?php print_r($value['id']);?>" <?php if (set_value('complaint') == $value['complaint_type']) {?>selected=""<?php }?>><?php print_r($value['complaint_type']);?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('complaint'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('source'); ?></label>
                                        <select name="source" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($complaintsource as $key => $value) {?>
                                                <option value="<?php echo $value['source']; ?>" <?php if (set_value('source') == $value['source']) {?>selected=""<?php }?>><?php echo $value['source']; ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('source'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('complain_by'); ?></label> <small class="req"> *</small>
                                        <input type="text" class="form-control" value="<?php echo set_value('name'); ?>"  name="name">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                </div>
								
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" class="form-control" value="<?php echo set_value('contact'); ?>"  name="contact">
                                        <span class="text-danger"><?php echo form_error('contact'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('date'); ?></label>
                                            <input type="text" class="form-control" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>"  name="date" id="date" readonly>
                                            <span class="text-danger"><?php echo form_error('date'); ?></span>
                                        </div>
                                    </div>
                                </div>
								
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control" id="description" name="description"rows="3"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('action_taken'); ?></label>
                                        <input type="text" class="form-control" value="<?php echo set_value('action_taken'); ?>"  name="action_taken">
                                        <span class="text-danger"><?php echo form_error('action_taken'); ?></span>
                                    </div>
                                </div>
								
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('assigned'); ?></label>
                                        <input type="text" class="form-control" value="<?php echo set_value('assigned'); ?>"  name="assigned">
                                        <span class="text-danger"><?php echo form_error('assigned'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control" id="description" name="note" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('note'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
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

<div id="editmyModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit'); ?> <?php echo $this->lang->line('complain'); ?></h4>
            </div>
         <form id="editformadd" method="post" accept-charset="utf-8" enctype="multipart/form-data" class="ptt10">    
            <div class="scroll-area">
                <div class="modal-body pb0 pt0">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('complain_type'); ?></label>
                                        <select name="complaint" id="ecomplaint" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($complaint_type as $key => $value) {?>
                                                <option value="<?php print_r($value['id']);?>" <?php if (set_value('complaint') == $value['id']) {?>selected=""<?php }?>><?php print_r($value['complaint_type']);?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('complaint'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('source'); ?></label>
                                        <select name="source" id="esource" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($complaintsource as $key => $value) {?>
                                                <option value="<?php echo $value['source']; ?>" <?php if (set_value('source') == $value['source']) {?>selected=""<?php }?>><?php echo $value['source']; ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('source'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('complain_by'); ?></label> <small class="req"> *</small>
                                        <input type="hidden" name="id" id="id">
                                        <input type="text" id="ename" class="form-control" value="<?php echo set_value('name'); ?>"  name="name">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" id="econtact" class="form-control" value="<?php echo set_value('contact'); ?>"  name="contact">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('date'); ?></label>
                                            <input type="text"  class="form-control" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>"  name="date" id="edate" readonly>
                                            <span class="text-danger"><?php echo form_error('date'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control" id="edescription" name="description"rows="3"><?php echo set_value('description'); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('action_taken'); ?></label>
                                        <input type="text" id="eaction_taken" class="form-control" value="<?php echo set_value('action_taken'); ?>"  name="action_taken">
                                        <span class="text-danger"><?php echo form_error('action_taken'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('assigned'); ?></label>
                                        <input type="text" id="eassigned" class="form-control" value="<?php echo set_value('assigned'); ?>"  name="assigned">
                                        <span class="text-danger"><?php echo form_error('assigned'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                        <textarea class="form-control" id="enote" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('note'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('attach_document'); ?></label>
                                        <div><input class="filestyle form-control" type='file' name='file'  />
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span></div>
                                </div>
                            </div>
                </div><!-- /.box-body -->
            </div><!-- scroll-area> -->
            <div class="modal-footer">
                <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="editformaddbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
        </form>
      </div>
    </div>
</div>

<!-- /.content-wrapper -->
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

    function getRecord(id) {       

        $('#complaintdetails').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/complaint/details/' + id,
            success: function (result) {                
                $('#getdetails').html(result);
            }
        });
    }

    function get(id) {
        $('#editmyModal').modal('show');
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/complaint/get_complaint/' + id,
            success: function (result) {
                $('#ecomplaint').val(result.complaint_type_id);
                $('#esource').val(result.source);
                $('#ename').val(result.name);
                $('#econtact').val(result.contact);
                $('#eedate').val(result.datedd);
                $('#edescription').val(result.description);
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
                url: '<?php echo base_url(); ?>admin/complaint/add',
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

    $(document).ready(function (e) {
        $("#editformadd").on('submit', (function (e) {
            $("#editformaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/complaint/edit',
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
                    //  alert("Fail")
                }
            });

        }));
    });
	
 $("#myModal").on('hidden.bs.modal', function (e) {
     $(".filestyle").next(".dropify-clear").trigger("click");    
     $('form#formadd').find('input:text, input:password, input:file, textarea').val('');
     $('form#formadd').find('select option:selected').removeAttr('selected');
     $('form#formadd').find('input:checkbox, input:radio').removeAttr('checked');
 });

function delete_record(id) {
    if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/complaint/delete/' + id,
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

    $(document).ready(function (e) {
        $('#myModal,#complaintdetails,#editmyModal').modal({
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
        initDatatable('ajaxlist','admin/complaint/getcomplaintdatatable');
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->