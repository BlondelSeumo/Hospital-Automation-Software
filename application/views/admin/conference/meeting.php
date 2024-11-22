<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('live_meeting'); ?></h3>
                        <div class="box-tools addmeeting box-tools-md">
                            <?php if ($this->rbac->hasPrivilege('live_meeting', 'can_add')) {
    ?>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-online-timetable"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?> </button>
                                <?php
}?>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) {?>
                            <?php echo $this->session->flashdata('msg') ?>
                        <?php }?>

                        <div class="table-responsive">
                        <div class="download_label"><?php echo $this->lang->line('live_meeting'); ?></div>
                            <table class="table table-hover table-striped table-bordered ajaxlistmeeting" data-export-title="<?php echo $this->lang->line('live_meeting') ;?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('meeting_title'); ?></th>
                                        <th width="25%"><?php echo $this->lang->line('description'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('meeting_duration_minutes'); ?></th>
                                        <th><?php echo $this->lang->line('api_used'); ?></th>
                                        <th><?php echo $this->lang->line('created_by'); ?> </th>
                                        <th width="15%"><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-right noExport" width="80"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                 
                                </tbody>
                            </table><!-- /.table -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-online-timetable">
    <div class="modal-dialog modal-lg">
        <form id="form-addconference" action="<?php echo site_url('admin/zoom_conference/addMeeting'); ?>" method="POST">
            <div class="modal-content mx-2">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('add_live_meeting'); ?> </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-12">
                            <div class="row">
                                <input type="hidden" class="form-control" id="password" name="password">
                                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <label for="title"><?php echo $this->lang->line('meeting_title'); ?> <small class="req"> *</small></label>
                                    <input type="text" class="form-control" id="title" name="title">
                                    <span class="text text-danger" id="title_error"></span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <label for="date"><?php echo $this->lang->line('meeting_date'); ?> <small class="req"> *</small></label>
                                    <div class='input-group' id='meeting_date'>
                                        <input type='text' class="form-control datetime" name="date" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                    <span class="text text-danger" id="title_error"></span>
                                </div>
                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <label for="duration"><?php echo $this->lang->line('meeting_duration_minutes'); ?> <small class="req"> *</small></label>
                                    <input type="number" class="form-control" id="duration" name="duration">
                                    <span class="text text-danger" id="title_error"></span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <label for="class"><?php echo $this->lang->line('host_video'); ?><small class="req"> *</small></label>
                                    <label class="radio-inline"><input type="radio" name="host_video"  value="1" checked><?php echo $this->lang->line('enabled'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="host_video" value="0" ><?php echo $this->lang->line('disabled'); ?></label>
                                    <span class="text text-danger" id="class_error"></span>
                                </div>
                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <label for="class"><?php echo $this->lang->line('client_video'); ?><small class="req"> *</small></label>
                                    <label class="radio-inline"><input type="radio" name="client_video"  value="1" checked><?php echo $this->lang->line('enabled'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="client_video" value="0" ><?php echo $this->lang->line('disabled'); ?></label>
                                    <span class="text text-danger" id="class_error"></span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <label for="description"><?php echo $this->lang->line('description') ?></label>
                                    <textarea class="form-control" name="description" id="description"></textarea>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label class="label15"><?php echo $this->lang->line('staff_list'); ?> <small class="req"> *</small></label> <div class="staffmain">
                                <ul class="liststaff">
                                    <?php
foreach ($staffList as $staff_key => $staff_value) {

    if ($staff_value['id'] == $logged_staff_id) {
        continue;
    }

    ?>
                                        <li class="list-group-item">
                                            <div class="checkbox">
                                                <label for="staff_<?php echo $staff_value['id']; ?>">
                                                    <input type="checkbox" id="staff_<?php echo $staff_value['id']; ?>" value="<?php echo $staff_value['id']; ?>" name="staff[]">
                                                    <?php
$name = ($staff_value["surname"] == "") ? $staff_value["name"] : $staff_value["name"] . " " . $staff_value["surname"];
    echo $name . " (" . $staff_value['user_type'] . " : " . $staff_value['employee_id'] . ")";
    ?>
                                                </label>
                                            </div>
                                        </li>

                                    <?php }
?>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('processing'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>

                </div>
            </div>
        </form>
    </div>
</div>

<div id="modal-chkstatus" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
    <form id="form-chkstatus" action="" method="POST">
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>

            </div>
            <div class="modal-body" id="zoom_details">

            </div>
        </div>
    </form>
    </div>
</div>
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlistmeeting','admin/zoom_conference/getmeetingdatatable',[],[],100);
      
    });
} ( jQuery ) )
</script>
<script type="text/javascript">
      $('#modal-chkstatus').on('shown.bs.modal', function (e) {
            var $modalDiv = $(e.delegateTarget);
              var id=$(e.relatedTarget).data('id');

            $.ajax({
                type: "POST",
                url: base_url + 'admin/zoom_conference/getlivestatus',
                data: {'id':id},
                dataType: "JSON",
                beforeSend: function () {
             $('#zoom_details').html("");
                    $modalDiv.addClass('modal_loading');
                },
                success: function (data) {

                   $('#zoom_details').html(data.page);
                    $modalDiv.removeClass('modal_loading');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $modalDiv.removeClass('modal_loading');
                },
                complete: function (data) {
                    $modalDiv.removeClass('modal_loading');
                }
            });
        })

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

    $('#modal-online-timetable').on('shown.bs.modal', function (e) {
        var password = makeid(5);
        $('#password').val("").val(password);

    })

    //===========================form submit==========
    $("form#form-addconference").submit(function (event) {
        event.preventDefault();
        var $form = $(this),
                url = $form.attr('action');

        var $button = $form.find("button[type=submit]:focus");
        $.ajax({
            type: "POST",
            url: url,
            data: $form.serialize(),
            dataType: "JSON",
            beforeSend: function () {
                $button.button('loading');

            },
            success: function (data) {				
                if (data.status == 0) {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    $('#modal-online-timetable').modal('hide');
                    successMsg(data.message);
                    window.location.reload(true);
                }
                $button.button('reset');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $button.button('reset');
            },
            complete: function (data) {
                $button.button('reset');
            }
        });
    })
    //================================================
    function makeid(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }
    $('#modal-online-timetable').on('hidden.bs.modal', function () {

        $(this).find("input,textarea,select").not("input[type=radio]")
                .val('')
                .end();
        $(this).find("input[type=checkbox], input[type=radio]")
                .prop('checked', false);
        $('input:radio[name="host_video"][value="1"]').prop('checked', true);
        $('input:radio[name="client_video"][value="1"]').prop('checked', true);
    });
  
    $(document).on('change', '#role_id', function (e) {
        $('#staff_id').html("");
        var role_id = $(this).val();
        getEmployeeName(role_id)
    });

    function getEmployeeName(role) {
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "JSON",
            beforeSend: function () {
                $('#staff_id').html("");
                $('#staff_id').addClass('dropdownloading');
            },
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    div_data += "<option value='" + obj.id + "'>" + obj.name + " " + obj.surname + "</option>";
                });
                $('#staff_id').append(div_data);
            },
            complete: function () {
                $('#staff_id').removeClass('dropdownloading');
            }
        });
    }
</script>
<script type="text/javascript">
    $(document).on('change', '.chgstatus_dropdown', function () {
        $(this).parent('form.chgstatus_form').submit()

    });

    $("form.chgstatus_form").submit(function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');
        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            dataType: "JSON",
            success: function (data)
            {
                if (data.status == 0) {
                    var message = "";
                    $.each(data.error, function (index, value) {

                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }
            }
        });
    });
</script>
<script type="text/javascript">
     $(document).on('click', 'a.join-btn', function(e){
         e.preventDefault();
         var id=$(this).data('id');
         var url = $(this).attr('href');
        $.ajax({
            url: "<?php echo site_url("admin/zoom_conference/add_history") ?>",
            type: "POST",
            data: {"id":id},
            dataType: 'json',

            beforeSend: function () {

            },
            success: function (res)
            {
                if (res.status == 0) {

                } else if(res.status == 1) {
                window.open(url, '_blank');
                }
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");
            },
            complete: function () {

            }
        });
});

$(".addmeeting").click(function(){
    $('#form-addconference').trigger("reset");
}); 

    $(document).ready(function (e) {
        $('#modal-online-timetable,#modal-chkstatus').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
</script>