<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <?php
$this->load->view('admin/onlineappointment/appointmentSidebar');
?>
            </div>
            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('shift'); ?></h3>
                        <div class="box-tools pull-right">
                        <?php if ($this->rbac->hasPrivilege('online_appointment_shift', 'can_add')){ ?>
                                <button onclick="addShiftModal()" class="btn btn-primary btn-sm addpayment"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_shift'); ?></button>
                        <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <div class="download_label"><?php echo $this->lang->line('shift'); ?></div>
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('time_from'); ?></th>
                                        <th><?php echo $this->lang->line('time_to'); ?></th>
                                        <?php if ($this->rbac->hasPrivilege('online_appointment_shift', 'can_edit') || $this->rbac->hasPrivilege('online_appointment_shift', 'can_delete')) { ?>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                        <?php } ?> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($shift)){  
                                        foreach ($shift as $shift_key => $shift_value) {
                                    ?>
                                    <tr>
                                        <td class="mailbox-name">
                                            <a href="#" data-toggle="popover" class="detail_popover"><?php echo $shift_value['name'] ?></a>
                                        </td>
                                        <td>
                                            <?php echo $shift_value['start_time'] ?>
                                        </td>
                                        <td>
                                            <?php echo $shift_value['end_time'] ?>
                                        </td>
                                        <td class="mailbox-date pull-right noExport">
                                        <?php if ($this->rbac->hasPrivilege('online_appointment_shift', 'can_edit')){ ?>
                                            <a href="#" onclick="getRecord('<?php echo $shift_value['id'] ?>')" class="btn btn-default btn-xs" data-target="#myModalEdit" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if ($this->rbac->hasPrivilege('online_appointment_shift', 'can_delete')){ ?>
                                            <a  class="btn btn-default btn-xs" data-toggle="tooltip" title="" onclick="delete_recordByIdReload('admin/onlineappointment/deleteglobalshift/<?php echo $shift_value['id']; ?>', '<?php echo $this->lang->line('delete_confirm') ?>')" data-original-title="<?php echo $this->lang->line('delete') ?>">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm400" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_shift'); ?></h4>
            </div>
            <form id="addshift" class="ptt10" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="name"><?php echo $this->lang->line('name'); ?></label>
                                    <span class="req"> *</span>
                                    <input name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="time_from"><?php echo $this->lang->line('time_from'); ?></label>
                                <span class="req"> *</span>
                                <div class="form-group input-group">
                                    <input type="text" name="time_from" class="form-control time_from time" id="time_from" value="">
                                    <div class="input-group-addon">
                                        <span class="fa fa-clock-o"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="time_to"><?php echo $this->lang->line('time_to'); ?></label>
                                <span class="req"> *</span>
                                <div class="form-group input-group">
                                    <input type="text" name="time_to" class="form-control time_to time" id="time_to" value="">
                                    <div class="input-group-addon">
                                        <span class="fa fa-clock-o"></span>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>   
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" id="addshiftbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>     
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm400" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_shift') ?></h4>
            </div>

            <form id="editshift" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10 row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label>
                                <span class="req"> *</span>
                                <input id="edit_name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                <input id="shiftid" name="shiftid" placeholder="" type="hidden" class="form-control"  />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="time_from"><?php echo $this->lang->line('time_from'); ?></label>
                            <span class="req"> *</span>
                            <div class="form-group input-group">
                                <input type="text" name="time_from" class="form-control time_from time" id="edit_time_from" value="">
                                <div class="input-group-addon">
                                    <span class="fa fa-clock-o"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="time_to"><?php echo $this->lang->line('time_to'); ?></label>
                            <span class="req"> *</span>
                            <div class="form-group input-group">
                                <input type="text" name="time_to" class="form-control time_to time" id="edit_time_to" value="">
                                <div class="input-group-addon">
                                    <span class="fa fa-clock-o"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clear">
                    <div class="pull-right">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="editshiftbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).on('focus', '.time', function () {
        var $this = $(this);
        $this.datetimepicker({
            format: 'LT'
        });
    });
    
    $(document).ready(function (e) {
        $('#addshift').on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/onlineappointment/addglobalshift',
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
                            $('.' + index).html(value);
                            message += value;
                        });

                        errorMsg(message);
                    }else if(data.status == "invalid"){
                        errorMsg(data.message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#addshiftbtn").button('reset');
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>")
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $('#editshift').on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/onlineappointment/updateglobalshift',
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
                    }else if(data.status == "invalid"){
                        errorMsg(data.message);
                    }  else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#editshiftbtn").button('reset');
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>")
                }
            });
        }));
    });

     function getRecord(id) {
        $('#myModalEdit').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/onlineappointment/getglobalshift/' + id,
            type: "POST",
            dataType: "json",
            success: function (data) {
                $("#edit_name").val(data.name);
                $("#shiftid").val(id);
                $("#edit_time_from").val(data.start_time);
                $("#edit_time_to").val(data.end_time);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        });
    }

    function addShiftModal(){
        $('#myModal form')[0].reset();
        $("#myModal").modal("show");
    }
    
    $(document).ready(function (e) {
        $('#myModal,#myModalEdit').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
</script>