<style type="text/css">
    .modal-dialog2 {margin: 1% auto;}
    .color_box {
        float: left;
        width: 10px;
        height: 10px;
        margin: 5px;
        border: 1px solid rgba(0, 0, 0, .2);
    }
</style>

<style type="text/css">
        .attendance_section {
            color: #0d6efd;
            ;
            font-size: 15px;
            font-weight: bold;
            padding: 15px 15px 15px 15px;
            margin: 10px 0px 10px 0px;
            background-color: #f5f5f5;
            border-radius: .25rem !important;
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
            text-align: center;
            border-radius: .25rem !important;
            /* background-color: #fff !important; */
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }
</style>
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="content-wrapper">     
    <section class="content">
        <div class="row">
           <?php $this->load->view('setting/sidebar.php'); ?>	   
		   
            <div class="col-md-10">
               <div class="box box-primary">
                    <div class="box-header ptbnull">
                    <h3 class="box-title titlefix"> <?php echo $this->lang->line('biometric_attendance_setting'); ?></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                    <div>
                        <form role="form" id="attendancetype_form" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="sch_id" value="<?php echo $result->id; ?>">
                            <div class="box-body">                       
                                <div class="row">
                                    <div class="col-md-7 col-lg-6 col-sm-8">
                                        <div class="form-group row">
                                            <label class="col-sm-6 col-lg-6 col-md-6"><?php echo $this->lang->line('biometric_attendance'); ?></label>
                                            <div class="col-sm-6 col-lg-6 col-md-6">
                                                <label class="radio-inline">
                                                    <input type="radio" name="biometric" value="0" <?php
                                                    if (!$result->biometric) {
                                                        echo "checked";
                                                    }
                                                    ?> ><?php echo $this->lang->line('disabled'); ?>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="biometric" value="1" <?php
                                                    if ($result->biometric) {
                                                        echo "checked";
                                                    }
                                                    ?>><?php echo $this->lang->line('enabled'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-lg-12">
                                            <div class="form-group">
                                                <label class="col-sm-4 col-lg-3 col-md-4"><?php echo $this->lang->line('devices_separate_by_comma'); ?> </label>
                                                <div class="col-sm-8 col-lg-9 col-md-8">
                                                    <input type="text" class="form-control" id="name" name="biometric_device" value="<?php echo $result->biometric_device; ?>">
                                                    <span class="text-danger"><?php echo form_error('biometric_device'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                                                 
                                </div>
                            </div>
                            <div class="box-footer">
                                <?php
                                if ($this->rbac->hasPrivilege('attendance_setting', 'can_edit')) {   ?>
                                    <button type="button" class="btn btn-primary submit_schsetting pull-right edit_attendance" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"> <?php echo $this->lang->line('save'); ?></button>
                                <?php  }   ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>               

            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('attendance_setting'); ?></h3>
                    </div>
                    <div class="box-body">
                        <?php
                        if (!empty($list_attendance)) { ?>
                            <div class="row">
                                <?php  foreach ($list_attendance as $list_key => $list_value) {   ?>
                                    <div class="col-md-12">
                                        <form method="POST" action="<?php echo site_url('schsettings/savestaffsetting'); ?>" class="update">
                                            <div class="panel panel-info">
                                            <div class="panel-footer panel-fo border-0">
                                                <div class="row d-flex align-items-center justify-content-between">
                                                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
                                                            <strong>                                                                
                                                                <?php echo $this->lang->line('role'); ?>: <?php   echo $list_value['role'];  ?>
                                                            </strong>
                                                        </div>
                                                        <div class="col-lg-4 col-md-8 col-sm-6">
                                                            <?php if ($this->rbac->hasPrivilege('attendance_setting', 'can_edit')) { ?>
                                                                <button type="submit" class="btn btn-primary btn-sm pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i><?php echo $this->lang->line('update'); ?>"><?php echo $this->lang->line('update'); ?></button>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="panel-body panelheight pr-05 ps-5">
													<div class="row">
                                                        <div class="col-sm-12 col-lg-12 col-md-12">
                                                             <div class="col-sm-3 col-lg-3 col-md-3">                                                                                
																	<label for="email"><?php echo $this->lang->line('attendance_type'); ?></label>
                                                             </div>
                                                             <div class="col-sm-9 col-lg-9 col-md-9">
																<div class="row">
																	<div class="col-sm-4 col-lg-4 col-md-4">
																		<label for="email"><?php echo $this->lang->line('entry_from'); ?> (hh:mm:ss)</label>
																	</div>
																	<div class="col-sm-4 col-lg-4 col-md-4">
																		<label for="email"><?php echo $this->lang->line('entry_upto'); ?> (hh:mm:ss)</label>
																	</div>
																	<div class="col-sm-4 col-lg-4 col-md-4">
																		<label for="email"><?php echo $this->lang->line('total_hour'); ?></label>
																	</div>
																</div>
															</div>
                                                        </div>
                                                    </div>
													
                                                    <div class="append_row paddA10">
                                                        <?php
                                                        $row = 1;
                                                        if (!empty($list_value['schedule'])) {
                                                            $count = 1;
                                                        ?>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <?php
                                                                    if (!empty($attendance_type)) {
                                                                        foreach ($attendance_type as $att_type_key => $att_type_value) {
                                                                            $return_value = get_input_value($list_value['schedule'], $att_type_value->id);
                                                                          
                                                                    ?>
                                                                            <input type="hidden" name="row[]" value="<?php echo $row; ?>">
                                                                            <input type="hidden" name="attendance_type_id_<?php echo $row; ?>" value="<?php echo $att_type_value->id; ?>">
                                                                            <input type="hidden" name="role_id_<?php echo $row; ?>" value="<?php echo $list_value['role_id']; ?>">
                                                                            <div class="row">
                                                                                <div class="col-sm-3 col-lg-3 col-md-3">
                                                                                    <?php echo $this->lang->line($att_type_value->long_lang_name) . " (" . $att_type_value->key_value . ")"; ?>
                                                                                </div>
                                                                                <div class="col-sm-9 col-lg-9 col-md-9">
                                                                                    <div class="row">
                                                                                        <div class="col-sm-4 col-lg-4 col-md-4">
                                                                                            <div class="form-group">
                                                                                                
                                                                                                <div class="input-group">
                                                                                                    <input type="text" name="entry_time_from_<?php echo $row; ?>" class="form-control entry_time_from time valid" id="entry_time_from" value="<?php echo $return_value['entry_time_from'] ?>">
                                                                                                    <div class="input-group-addon">
                                                                                                        <span class="fa fa-clock-o"></span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-4 col-lg-4 col-md-4">
                                                                                            <div class="form-group">
                                                                                                
                                                                                                <div class="input-group">
                                                                                                    <input type="text" name="entry_time_to_<?php echo $row; ?>" class="form-control entry_time_to time valid" id="time_to" value="<?php echo $return_value['entry_time_to'] ?>">
                                                                                                    <div class="input-group-addon">
                                                                                                        <span class="fa fa-clock-o"></span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-4 col-lg-4 col-md-4">
                                                                                            <div class="form-group">
                                                                                                
                                                                                                <div class="input-group">
                                                                                                    <input type="text" name="total_institute_hour_<?php echo $row; ?>" class="form-control total_institute_hour time_hour valid" id="total_institute_hour" value="<?php echo $return_value['total_institute_hour'] ?>">
                                                                                                    <div class="input-group-addon">
                                                                                                        <span class="fa fa-clock-o"></span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>                                                                            
                                                                            </div>
                                                                    <?php
                                                                       $row++;
                                                                    }
                                                                }
                                                            ?>
                                                                </div>
                                                            </div>
                                                        <?php  $count++;   }  ?>
                                                    </div>
                                                </div>
                                             
                                            </div>
                                        </form>
                                    </div>
                                <?php   }      ?>
                            </div>
                        <?php   }   ?>
                    </div>
                </div>
            </div> 
			
       </div> 
   </section>
</div>     

<?php
function get_input_value($array, $find_time){
    if (!empty($array)) {
        foreach ($array as $array_key => $array_value) {
            if ($array_value->staff_attendence_type_id == $find_time) {
                return [
                    'entry_time_from' => $array_value->entry_time_from,
                    'entry_time_to' => $array_value->entry_time_to,
                    'total_institute_hour' => $array_value->total_institute_hour,                  
                ];
            }
        }
        return [
            'entry_time_from' => '',
            'entry_time_to' => '',
            'total_institute_hour' => '',          
        ];
    }
} ?>
 
<script type="text/javascript">
    var base_url = '<?php echo base_url(); ?>';
 
    $(".edit_attendance").on('click', function (e) {
        var $this = $(this);
        $this.button('loading');
        $.ajax({
            url: '<?php echo site_url("schsettings/saveattendance") ?>',
            type: 'POST',
            data: $('#attendancetype_form').serialize(),
            dataType: 'json',

            success: function (data) {

                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {

                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    location.reload();
                }

                $this.button('reset');
            }
        });
    });

</script>

<script type="text/javascript">
    $(function() {
        $('.time').datetimepicker({
            format: 'HH:mm:ss'
        });
    });
    $(function() {
        $('.time_hour').datetimepicker({
            format: 'HH:mm:ss'
        });
    });

    $(document).on('submit', '.update', function(e) {
        var submit_btn = $(this).find("button[type=submit]");
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            dataType: "json",
            beforeSend: function() {
                submit_btn.button('loading');
            },
            success: function(data) {
                if (data.status == 1) {
                    successMsg(data.message);
                } else {
                    var message = "";
                    $.each(data.error, function(index, value) {

                        message += value;
                    });
                    errorMsg(message);
                }
                submit_btn.button('reset');
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function() {
                submit_btn.button('reset');
            }
        });
    });
</script>

