<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('visitor_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('visitor_book', 'can_add')) {?>
                                <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm addvisitor"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_visitor'); ?></a>
                            <?php }?>
                            <?php if ($this->rbac->hasPrivilege('phone_call_log', 'can_view')) {?>
                                <a href="<?php echo base_url(); ?>admin/generalcall" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('phone_call_log'); ?></a>
                            <?php }if (($this->rbac->hasPrivilege('postal_dispatch', 'can_view')) || ($this->rbac->hasPrivilege('postal_receive', 'can_view'))) {?>
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">
                                    <i class="fa fa-reorder"></i> <?php echo $this->lang->line('postal'); ?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu multi-level pull-right" role="menu" aria-labelledby="dropdownMenu1" id="easySelectable">
                                    <?php if ($this->rbac->hasPrivilege('postal_receive', 'can_view')) {?>
                                        <li><a href="<?php echo base_url(); ?>admin/receive"><?php echo $this->lang->line('receive'); ?></a></li>
                                    <?php }if ($this->rbac->hasPrivilege('postal_dispatch', 'can_view')) {?>
                                        <li><a href="<?php echo base_url(); ?>admin/dispatch"><?php echo $this->lang->line('dispatch'); ?></a></li>
                                    <?php }?>
                                </ul>
                            <?php }if ($this->rbac->hasPrivilege('complain', 'can_view')) {?>
                                <a href="<?php echo base_url(); ?>admin/complaint" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('complain'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('visitor_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <table class="table table-hover table-striped table-bordered ajaxlist" data-export-title="<?php echo $this->lang->line('visitor_list'); ?>"> <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('purpose'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('visit_to'); ?></th>
                                        <th><?php echo $this->lang->line('ipd_opd_staff'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('in_time'); ?></th>
                                        <th><?php echo $this->lang->line('out_time'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                              

                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) col-8 end-->
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<!-- new END -->
<div id="visitordetails" class="modal fade" role="dialog">
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

<div id="editmyModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_visitor'); ?></h4>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/visitors/edit') ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data" class="ptt10">
                <div class="scroll-area">
                    <div class="modal-body pt0 pb0">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('purpose'); ?></label><small class="req"> *</small>
                                        <select name="purpose" id="purpose" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($Purpose as $key => $value) {?>
                                                <option value="<?php print_r($value['visitors_purpose']);?>"<?php if (set_value('purpose') == $value['visitors_purpose']) {?>selected=""<?php }?>><?php print_r($value['visitors_purpose']);?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('purpose'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('name'); ?></label>  <small class="req"> *</small>
                                        <input type="text" class="form-control"  name="name"  id="name">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" class="form-control" id="contact"  name="contact">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('id_card'); ?></label>
                                        <input type="text" class="form-control" name="id_proof"  id="id_proof">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('visit_to'); ?></label>
                                        <select name="visit_to" id="edit_visit_to" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($visit_to as $visit_to_key => $visit_to_value) {
                                                $selected = '';
                                               if(set_value('visit_to') == $visit_to_value){
                                                $selected = 'selected';
                                               }
                                                ?>
                                                <option value="<?php echo $visit_to_key; ?>" <?php echo $selected; ?>><?php echo $visit_to_value; ?>
                                                </option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('visit_to'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('ipd_opd_staff'); ?></label>
                                        <select name="ipd_opd_staff" id="edit_ipd_opd" class="form-control select2" style="width:100%">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('ipd_opd_staff'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('related_to'); ?></label>
                                        <input type="text" class="form-control" id="edit_related_to" name="related_to" readonly >
                                    </div>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('number_of_person'); ?></label>
                                        <input type="text" class="form-control" name="pepples" id="pepples">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('date'); ?> <small class="req"> *</small></label><input type="text" id="edate" class="form-control"   name="date" readonly="">
                                            <span class="text-danger"><?php echo form_error('date'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('in_time'); ?></label>
                                        <div class="bootstrap-timepicker">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" name="time" class="form-control timepicker" id="in_time" value="<?php echo set_value('time'); ?>">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-clock-o"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('time'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('out_time'); ?></label>
                                        <div class="bootstrap-timepicker">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" name="out_time" class="form-control timepicker" id="out_time" value="<?php echo set_value('out_time'); ?>">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-clock-o"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('out_time'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" id="editid" name="id">
                                <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                <textarea class="form-control"  name="note" id="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile"><?php echo $this->lang->line('attach_document'); ?></label>
                                <div><input class="filestyle form-control" type='file' name='file'  />
                                </div>
                                <span class="text-danger"><?php echo form_error('file'); ?></span></div>
                        </div><!-- /.box-body -->
                    </div>
                <div class="modal-footer">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>"
                            id="editformaddbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_visitor'); ?></h4>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/visitors') ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data" class="ptt10">
                <div class="scroll-area">
                    <div class="modal-body pt0 pb0">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('purpose'); ?></label><small class="req"> *</small>
                                        <select name="purpose" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($Purpose as $key => $value) {?>
                                                <option value="<?php print_r($value['visitors_purpose']);?>"<?php if (set_value('purpose') == $value['visitors_purpose']) {?>selected=""<?php }?>><?php print_r($value['visitors_purpose']);?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('purpose'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('name'); ?></label>  <small class="req"> *</small>
                                        <input type="text" class="form-control" value="<?php echo set_value('name'); ?>" name="name">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" class="form-control" value="<?php echo set_value('contact'); ?>" name="contact">
                                        <span class="text-danger"><?php echo form_error('contact'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('id_card'); ?></label>
                                        <input type="text" class="form-control" value="<?php echo set_value('id_proof'); ?>" name="id_proof">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('visit_to'); ?></label>
                                        <select name="visit_to" id="visit_to" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($visit_to as $visit_to_key => $visit_to_value) {?>
                                                <option value="<?php echo $visit_to_key; ?>"><?php echo $visit_to_value; ?>
                                                </option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('visit_to'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('ipd_opd_staff'); ?></label>
                                        <select name="ipd_opd_staff" id="ipd_opd" class="form-control select2" style="width:100%">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('ipd_opd_staff'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('related_to'); ?></label>
                                        <input type="text" class="form-control" id="related_to" name="related_to" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('number_of_person'); ?></label>
                                        <input type="text" class="form-control" value="<?php echo set_value('pepples'); ?>" name="pepples">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('date'); ?> <small class="req"> *</small></label><input type="text" id="date" class="form-control" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>"  name="date" readonly="">
                                            <span class="text-danger"><?php echo form_error('date'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('in_time'); ?></label>
                                        <div class="bootstrap-timepicker">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" name="time" class="form-control timepicker" id="stime_" value="<?php echo set_value('time'); ?>">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-clock-o"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('time'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('out_time'); ?></label>
                                        <div class="bootstrap-timepicker">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" name="out_time" class="form-control timepicker" id="stime_" value="<?php echo set_value('out_time'); ?>">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-clock-o"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('out_time'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                <textarea class="form-control" id="description" name="note" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputFile"><?php echo $this->lang->line('attach_document'); ?></label>
                                <div><input class="filestyle form-control" type='file' name='file'  />
                                </div>
                                <span class="text-danger"><?php echo form_error('file'); ?></span></div>
                        </div><!-- /.box-body -->
                    </div>
                <div class="modal-footer">
                    <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script type="text/javascript">

$(function () {
    $('.select2').select2()
});

function get(id) {
    $('#editmyModal').modal('show');
   
    $.ajax({
        dataType: 'json',
        url: '<?php echo base_url(); ?>admin/visitors/get_visitor/' + id,
        success: function (result) {
            $('#purpose').val(result.purpose),
            $('#name').val(result.name);
            $('#contact').val(result.contact);
            $('#id_proof').val(result.id_proof);
            $('#edit_visit_to').val(result.visit_to);
            $('#pepples').val(result.no_of_pepple);
            $('#edate').val(result.datedd);
            $('#in_time').val(result.in_time);
            $('#out_time').val(result.out_time);
            $('#note').val(result.note);
            $('#editid').val(result.id);
            $('#edit_related_to').val(result.related_to);
            populatevisitto(result.visit_to,result.ipd_opd_staff_id);
        }
    });
}

$(document).ready(function (e) {
    $("#formadd").on('submit', (function (e) {
        $("#formaddbtn").button('loading');
        e.preventDefault();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/visitors/add',
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
            url: '<?php echo base_url(); ?>admin/visitors/edit',
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

$(function () {
    $('.select2').select2()
  });

$(function () {

    $(".timepicker").timepicker({

    });
});

function getRecord(id) {
$('#visitordetails').modal('show');
$.ajax({
    url: '<?php echo base_url(); ?>admin/visitors/details/' + id,
    success: function (result) {      
        $('#getdetails').html(result);
    }
});
}

function deletevisitor(id){
    var id = id;
    if(id !=''){
        if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
            $.ajax({
            url: '<?php echo base_url(); ?>admin/visitors/deletevisitor',
            type: 'post',
            data:{id:id},
            success: function (data) {
                successMsg("<?php echo $this->lang->line("delete_message") ?>");
                table.ajax.reload();
            }
            });
        }
    }else{
        alert("<?php echo $this->lang->line('something_went_wrong'); ?>");
    }   
}

function deletevisitorimage(id, $image){
    var id = id;
    var image = image;
    if(id !=''){
        if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
            $.ajax({
            url: '<?php echo base_url(); ?>admin/visitors/deletevisitorimage',
            type: 'post',
            data:{id:id,image:image},
            success: function (data) {
                successMsg("<?php echo $this->lang->line("delete_message") ?>");
                table.ajax.reload();
            }
            });
        }
    }else{
        alert("<?php echo $this->lang->line('something_went_wrong'); ?>");
    }   
}

</script>
<script type="text/javascript">
 $("#myModal").on('hidden.bs.modal', function (e) {
     $(".filestyle").next(".dropify-clear").trigger("click");
     $("#ipd_opd").select2("val", "");
    
     $('form#formadd').find('input:text, input:password, input:file, textarea').val('');
     $('form#formadd').find('select option:selected').removeAttr('selected');
     $('form#formadd').find('input:checkbox, input:radio').removeAttr('checked');
 });

    $(document).ready(function (e) {
        $('#myModal,#visitordetails,#editmyModal').modal({
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
        initDatatable('ajaxlist','admin/visitors/getvisitorsdatatable');
    });

    $(document).on('change', '#visit_to', function (e) {
        var visit_to = $('#visit_to').val();
        populatevisitto(visit_to,'');
    });

    $(document).on('change', '#edit_visit_to', function (e) {
        var visit_to = $('#edit_visit_to').val();
        populatevisitto(visit_to,'');
    });

    $(document).on('change', '#ipd_opd', function (e) {
        $('#related_to').val('');
        $('#related_to').val($("#ipd_opd option:selected" ).text());
    });

    $(document).on('change', '#edit_ipd_opd', function (e) {
        $('#edit_related_to').val('');
        $('#edit_related_to').val($("#edit_ipd_opd option:selected" ).text());
    });

} ( jQuery ) )

function populatevisitto(visit_to,ipd_opd_id) {
    var base_url = '<?php echo base_url() ?>';
    var div_data = '';
    $.ajax({
        type: "GET",
        url: base_url + "admin/visitors/get_ipd_opd_staff_list",
        data: {'visit_to': visit_to},
        dataType: "json",
        success: function (data) {
    
            $('#ipd_opd').empty();
            $('#edit_ipd_opd').empty();
            $('#ipd_opd').append('<option value=""><?php echo $this->lang->line('select'); ?></option>');
            $('#edit_ipd_opd').append('<option value=""><?php echo $this->lang->line('select'); ?></option>');

            $.each(data.data, function (i, obj)
            {
               
                var select = "";
                if (ipd_opd_id == obj.id) {
                    var select = "selected=selected";
                }

                if(data.visit_to == 'staff'){
                   div_data = "<option value=" + obj.id + " " + select + ">" + obj.name + ' '+ obj.surname+ ' ('+ obj.employee_id +')' + "</option>";
                }else if (data.visit_to == 'ipd_patient') {
                   div_data = "<option value=" + obj.id + " " + select + ">" + obj.patient_name + ' ('+ obj.patient_id +')' + ' (<?php echo $this->customlib->getSessionPrefixByType('ipd_no'); ?>'+ obj.id +')' + "</option>";
                }else if (data.visit_to == 'opd_patient') {
                   div_data = "<option value=" + obj.id + " " + select + ">" + obj.patient_name + ' ('+ obj.patient_id +')' + ' (<?php echo $this->customlib->getSessionPrefixByType('opd_no'); ?>'+ obj.id +')' + "</option>";
                }

                $('#edit_ipd_opd').append(div_data);
                $('#ipd_opd').append(div_data);
                $(".select2").select2().select2('val', ipd_opd_id);
            });            
        }

    });
}
</script>
<!-- //========datatable end===== -->
