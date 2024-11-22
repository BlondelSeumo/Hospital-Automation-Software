<style type="text/css">
    .relative label.text-danger{position: absolute; left:5px; bottom:0;}
</style>
<div class="row clearfix">
    <div class="col-md-12 column">    
        <form method="POST" id="form_<?php echo $day; ?>" class="commentForm autoscroll">
            <input type="hidden" name="day" value="<?php echo $day; ?>">
            <input type="hidden" name="doctor" value="<?php echo $doctor; ?>">
            <input type="hidden" name="shift" value="<?php echo $shift; ?>">
            <div>   
                <table class="table table-bordered table-hover order-list tablewidthRS" id="tab_logic">
                    <thead>
                        <tr>
                            <th><?php echo $this->lang->line('time_from'); ?></th>
                            <th><?php echo $this->lang->line('time_to'); ?></th>
                            <th class="text-right"><?php echo $this->lang->line('action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($prev_record)) {
                            $counter = 1;
                            foreach ($prev_record as $prev_rec_key => $prev_rec_value) {
                                ?>
                            <input type="hidden" name="prev_array[]" value="<?php echo $prev_rec_value->id; ?>">
                            <tr id='addr0'>
                                <td>
                                    <input type="hidden" name="total_row[]" value="<?php echo $counter; ?>">
                                    <input type="hidden" name="prev_id_<?php echo $counter; ?>" value="<?php echo $prev_rec_value->id; ?>">
                                    <div class="input-group">
                                        <input type="text" name="time_from_<?php echo $counter; ?>" class="form-control time_from time" id="time_from_<?php echo $counter; ?>" value="<?php echo ($prev_rec_value->start_time != "") ? $prev_rec_value->start_time :  $prev_rec_value->start_time;?>">
                                        <div class="input-group-addon">
                                            <span class="fa fa-clock-o"></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" name="time_to_<?php echo $counter; ?>" class="form-control time_to time" id="time_to_<?php echo $counter; ?>" value="<?php echo ($prev_rec_value->end_time != "") ? $prev_rec_value->end_time :  $prev_rec_value->end_time;?>">
                                        <div class="input-group-addon">
                                            <span class="fa fa-clock-o"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right" width="30">
                                <?php if ($this->rbac->hasPrivilege('online_appointment_slot', 'can_delete')) { ?>
                                    <button title="<?php echo $this->lang->line('delete'); ?>" class="ibtnDel btn btn-danger btn-sm btn-danger"> <i class="fa fa-trash"></i></button>
                                <?php } ?>
                                </td>
                            </tr>
                            <?php
                            $counter ++;
                        }
                    } else {
                        ?>
                        <tr id='addr0'>
                            <td>
                                <input type="hidden" name="total_row[]" value="<?php echo $total_count; ?>">
                                <input type="hidden" name="prev_id_<?php echo $total_count; ?>" value="0">
                                <div class="input-group">
                                    <input type="text" name="time_from_<?php echo $total_count; ?>" class="form-control time_from time" id="time_from_<?php echo $total_count; ?>" aria-invalid="false">
                                    <div class="input-group-addon">
                                        <span class="fa fa-clock-o"></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="time_to_<?php echo $total_count; ?>" class="form-control time_to time" id="time_to_<?php echo $total_count; ?>" aria-invalid="false">
                                    <div class="input-group-addon">
                                        <span class="fa fa-clock-o"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right" width="30">
                            <?php if ($this->rbac->hasPrivilege('online_appointment_slot', 'can_delete')) { ?>
                                <button class="ibtnDel btn btn-danger btn-sm btn-danger"> <i class="fa fa-trash"></i></button>
                            <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php if ($this->rbac->hasPrivilege('online_appointment_slot', 'can_add')) { ?>
        <a id="add_row" class="addrow addbtnright btn btn-primary btn-sm float-left rtl-float-right"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_new'); ?></a>
    <?php } ?>
            </div>
            <?php if ($this->rbac->hasPrivilege('online_appointment_slot', 'can_edit')) { ?>
                <button class="btn btn-primary btn-sm pull-right" data-loading-text="<?php echo $this->lang->line('processing'); ?>" type="submit"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            <?php } ?>
          </form>
    </div>
</div>
</div>

<script type="text/javascript">
    var form_id = "<?php echo $day ?>";
    $(function () {
        $('form#form_' + form_id).on('submit', function (event) {
        $('form#form_' + form_id).button('loading');

            $('input[id^="time_from_"]').each(function () {
                $(this).rules('add', {
                    required: true,
                    messages: {
                        required: "Required"
                    }
                });
            });

            $('input[id^="time_to_"]').each(function () {
                $(this).rules('add', {
                    required: true,
                    messages: {
                        required: "Required"
                    }
                });
            });

            // prevent default submit action         
            event.preventDefault();

            // test if form is valid 
            if ($('form#form_' + form_id).validate().form()) {
                var target = $('.nav-tabs .active a').attr("href");
                var target_id = $('.nav-tabs .active a').attr("id");
                var ajax_data = $('.nav-tabs .active a').data();

                $.ajax({
                    type: 'POST',
                    url: base_url + "admin/onlineappointment/saveDoctorShift",
                    data: $('#form_' + form_id).serialize(),
                    dataType: 'json',
                    beforeSend: function () {

                    },
                    success: function (data) {
                        $(target).html(data.html);
                        if (data.status == 1) {
                            successMsg(data.message);
                            $(target).html("");
                            getShiftdata(target, target_id, ajax_data);
                        } else if(data.status == 3) {
                             var message = '<?php echo $this->lang->line("shift_start_time_should_be_greater_than_end_time"); ?>';
                            alert(message);
                        } else if(data.status == 4){
                            var message = '<?php echo $this->lang->line("shift_timing_overlapping"); ?>';
                            alert(message);
                        } else if(data.status == 5){
                            var message = '<?php echo $this->lang->line("time_should_be_under_global_shift"); ?>';
                            alert(message);
                        } else {
                            var list = $('<ul/>');
                            $.each(data.error, function (key, value) {
                                if (value != "") {
                                    list.append(value);
                                }
                            });
                            errorMsg(list);
                        }
                        $('form#form_' + form_id).button('reset');
                    },
                    error: function (xhr) { // if error occured

                    },
                    complete: function () {

                    }
                });
            } else {
                var message = '<?php echo $this->lang->line("does_not_validate"); ?>';
                errorMsg('<?php echo $this->lang->line("invalid_input"); ?>')
                console.log(message);
                $('form#form_' + form_id).button('reset');
            }
        });

        // initialize the validator
        $('form#form_' + form_id).validate({
            errorClass: 'text-danger'
        });
    });
</script>