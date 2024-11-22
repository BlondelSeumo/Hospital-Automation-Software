<div class="wrapper">
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- /.col -->
                <div class="col-md-9 col-sm-9">
                    <div class="box box-primary">
                        <div class="box-body">
                            <!-- THE CALENDAR -->
                            <div id="calendar"></div> 
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /. box -->
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="box box-primary">
                        <div class="box-header ptbnull">
                            <h3 class="box-title"><?php echo $this->lang->line('to_do_list'); ?></h3>
                            <div class="box-tools pull-right">
                                <?php
                                if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_add')) {
                                    ?>
                                    <button class="btn btn-primary btn-sm pull-right" onclick="add_task()"><i class="fa fa-plus"></i></button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="">
                            <?php foreach ($tasklist as $taskkey => $taskvalue) { 
                                ?>
                                <div class="media mt5" style="padding:0 10px;">
                                    <div class="media-left">
                                        <input type="checkbox" <?php
                                        if ($taskvalue["is_active"] == 'yes') {
                                            echo "checked";
                                        }
                                        ?> id="check<?php echo $taskvalue["id"] ?>" onclick="markcomplete('<?php echo $taskvalue["id"] ?>')" name="eventcheck"  value="<?php echo $taskvalue["id"]; ?>">
                                    </div>
                                    <div class="media-body">
                                        <p class="tododesc" <?php if ($taskvalue["is_active"] == 'yes') {
                                            ?> style="text-decoration: line-through;color: #4f881d;" <?php } ?> ><?php echo $taskvalue["event_title"]; ?></p>
                                        <small class="tododate"><?php echo date($this->customlib->getHospitalDateFormat(true, false), strtotime($taskvalue["start_date"]));?>
                                            <?php
                                            if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_delete')) {
                                                ?><a title="<?php echo $this->lang->line('delete') ?>" href="#" onclick="deleteevent('<?php echo $taskvalue["id"]; ?>', '<?php echo $this->lang->line('task ') ?>'); return false;" class="pull-right text-muted"><i class="fa fa-remove"></i></a>
                                                <?php
                                            }
                                            if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_edit')) {
                                                ?>
                                                <a title="<?php echo $this->lang->line('edit') ?>" href="#" onclick="edit_todo_task('<?php echo $taskvalue["id"]; ?>'); return false;" class="pull-right text-muted mright5" style="margin-right: 5px"><i class="fa fa-pencil"></i></a>
                                            <?php } ?>
                                        </small>
                                    </div>
                                </div> 
                                <div class="todo_divider"></div>   
                            <?php } ?>
                            <div class="todopagination"><?php echo $this->pagination->create_links(); ?></div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>

        <div id="newTask" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-title" ></h4>
            </div>
            <form role="form" id="addtodo_form" method="post" enctype="multipart/form-data" action="">  
                <div class="modal-body pt0 pb0">
                    <div class="row ptt10">
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('title'); ?><small class="req"> *</small></label>
                            <input class="form-control" name="task_title"  id="task-title"> 
                            <span class="text-danger"><?php echo form_error('title'); ?></span>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                            <input class="form-control date" type="text" autocomplete="off"  name="task_date" placeholder="" id="task-date">
                            <input class="form-control" type="hidden" name="eventid" id="taskid">
                        </div>
                    </div>
                </div>
                <div class="modal-footer"> 
                    <div id="permission"><?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_add')) { ?>
                            <input type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>"  id="addtodo_formbtn" class="btn btn-primary submit_addtask pull-right" value="<?php echo $this->lang->line('save'); ?>">
                        <?php } ?>
                    </div>
                </div> 
            </form>
        </div>
    </div>
</div>  

<div id="newEventModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_new_event'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form"  id="addevent_form" method="post" enctype="multipart/form-data" action="">
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_title'); ?><small class="req"> *</small></label>
                            <input class="form-control" name="title" id="input-field"> 
                            <span class="text-danger"><?php echo form_error('title'); ?></span>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                            <textarea name="description" class="form-control" id="desc-field"></textarea></div>
                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_from'); ?><small class="req"> *</small></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" autocomplete="off" name="event_from" class="form-control pull-right event_from datetime">
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_to'); ?><small class="req"> *</small></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" autocomplete="off" name="event_to" class="form-control pull-right event_to datetime">
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_color'); ?></label>
                            <input type="hidden" name="eventcolor" autocomplete="off" id="eventcolor" class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <?php
                            $i = 0;
                            $colors = '';
                            foreach ($event_colors as $color) {
                                $color_selected_class = 'cpicker-small';
                                if ($i == 0) {
                                    $color_selected_class = 'cpicker-big';
                                }
                                $colors .= "<div class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "; border-radius:100px'></div>";
                                $i++;
                            }
                            echo '<div class="cpicker-wrapper">';
                            echo $colors;
                            echo '</div>';
                            ?>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_type'); ?></label>
                            <br/>
                            <label class="radio-inline">
                                <input type="radio" name="event_type" value="public" id="public"><?php echo $this->lang->line('public'); ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="event_type" value="private" checked id="private"><?php echo $this->lang->line('private'); ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="event_type" value="sameforall" id="public"><?php echo $this->lang->line('all'); ?> <?php echo $role; ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="event_type" value="protected" id="public"><?php echo $this->lang->line('protected'); ?>
                            </label> </div>
                        <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_add')) { ?>
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <button type="submit" id="addevent_formbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-primary submit_addevent pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                               
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
 
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>

<div id="viewEventModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('view_event'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form" method="post" id="updateevent_form"  enctype="multipart/form-data" action="" >
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_title'); ?></label><small class="req"> *</small>
                            <input class="form-control" name="title" placeholder="" id="event_title"> 
                        </div>
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_description'); ?></label>
                            <textarea name="description" class="form-control" placeholder="" id="event_desc"></textarea></div>                    

                         <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_from'); ?><small class="req"> *</small></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" autocomplete="off" id="eventfrom" name="eventfrom" class="form-control pull-right event_from datetime">
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_to'); ?><small class="req"> *</small></label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" autocomplete="off" id="eventto" name="eventto" class="form-control pull-right event_to datetime">
                            </div>
                        </div>

                        <input type="hidden" name="eventid" id="eventid">
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_color'); ?></label>
                            <input type="hidden" name="eventcolor" autocomplete="off" placeholder="" id="event_color" class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <?php
                            $i = 0;
                            $colors = '';
                            foreach ($event_colors as $color) {
                                $colorid = trim($color, "#");
                                $color_selected_class = 'cpicker-small';
                                if ($i == 0) {
                                    $color_selected_class = 'cpicker-big';
                                }
                                $colors .= "<div id=" . $colorid . " class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "; border-radius:100px'></div>";                             
                                $i++;
                            }
                            echo '<div class="cpicker-wrapper selectevent">';
                            echo $colors;
                            echo '</div>';
                            ?>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_type'); ?></label><br/>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="public" id="public"><?php echo $this->lang->line('public'); ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="private" id="private"><?php echo $this->lang->line('private'); ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="sameforall" id="public"><?php echo $this->lang->line('all'); ?> <?php echo $role; ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="protected" id="public"><?php echo $this->lang->line('protected'); ?> 
                            </label>
                        </div>
                        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                            <?php
                            if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_edit')) {
                                ?>
                                <button type="submit" id="updateevent_formbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>"  class="btn btn-primary submit_update pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                            <?php } ?>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                            <?php
                            if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_delete')) {
                                ?>
                                <input type="button" id="delete_event"  class="btn btn-primary submit_delete pull-right" value="<?php echo $this->lang->line('delete'); ?>">
                            <?php } ?>
                        </div>        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 

<!-- Page specific script -->
<script>

    function add_task() {
        $("#modal-title").html("<?php echo $this->lang->line('add_task'); ?>");
        $("#task-title").val('');
        $("#taskid").val('');
        $('#newTask').modal('show');
	}

    function edit_todo_task(eventid) {
        $.ajax({
            url: "<?php echo site_url("admin/calendar/gettaskbyid/") ?>" + eventid,
            type: "POST",
            data: {eventid: eventid},
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (res)
            {					
                $("#modal-title").html("<?php echo $this->lang->line('edit_task'); ?>");
                $("#task-title").val(res.event_title);
                $("#taskid").val(eventid);
                $("#task-date").val(res.edit_start_date);						
                $('#newTask').modal('show');
                $('#permission').html('<?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_edit')) { ?><input type="submit" id="addtodo_formbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>"  class="btn btn-primary submit_addtask pull-right" value="<?php echo $this->lang->line('save'); ?>"><?php } ?>');

                            }
                        });
                    }

                    $(document).ready(function (e) {
                        $("#addtodo_form").on('submit', (function (e) {
                            $("#addtodo_formbtn").button("loading");
                            e.preventDefault();
                            $.ajax({
                                url: "<?php echo site_url("admin/calendar/addtodo") ?>",
                                type: "POST",
                                data: new FormData(this),
                                dataType: 'json',
                                contentType: false,
                                cache: false,
                                processData: false,
                                success: function (res)
                                {
                                    if (res.status == "fail") {
                                        var message = "";
                                        $.each(res.error, function (index, value) {
                                            message += value;
                                        });
                                        errorMsg(message);
                                    } else {
                                        successMsg(res.message);
                                        window.location.reload(true);
                                    }
                                    $("#addtodo_formbtn").button("reset");
                                }
                            });
                        }));
                    });

                    function complete_event(id, status) {
                        $.ajax({
                            url: "<?php echo site_url("admin/calendar/markcomplete/") ?>" + id,
                            type: "POST",
                            data: {id: id, active: status},
                            dataType: 'json',
                            success: function (res)
                            {
                                if (res.status == "fail") {
                                    var message = "";
                                    $.each(res.error, function (index, value) {
                                        message += value;
                                    });
                                    errorMsg(message);
                                } else {
                                    successMsg(res.message);
                                    window.location.reload(true);
                                }
                            }
                        });
                    }

                    function markcomplete(id) {
                        $('#check' + id).change(function () {
                            if (this.checked) {
                                complete_event(id, 'yes');
                            } else {
                                complete_event(id, 'no');
                            }
                        });
                    }
                            
    $(document).ready(function (e) {
        $('#newTask,#newEventModal,#viewEventModal').modal({
            backdrop: 'static',
            keyboard: false,
            show:false
        });
    });
</script>