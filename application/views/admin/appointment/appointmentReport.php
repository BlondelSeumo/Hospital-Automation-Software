<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                <?php $this->load->view('admin/report/_appointment');?> 
                <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('appointment_report'); ?></h3>
                    </div>
                    <form id="form1" action="" method="post">
                        <div class="box-body row pb0">
                            <?php echo $this->customlib->getCSRF(); ?>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('search_type'); ?></label><small class="req"> *</small>
                                                <select class="form-control" name="search_type"  id="search_type_select" onchange="showdate(this.value)">
                                                <option value=""><?php echo $this->lang->line('select')?></option> 
                                                    <?php foreach ($searchlist as $key => $search) { ?>
                                                        <option value="<?php echo $key ?>" <?php
                                                        if ((isset($search_type)) && ($search_type == $key)) {
                                                                echo "selected";
                                                            }
                                                            ?>><?php echo $search ?></option>
                                                        <?php }?>
                                                </select>
                                                <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                            </div>
                                        </div> 

                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                            <label><?php echo $this->lang->line("doctor"); ?></label>
                                                    <select class="form-control select2" onchange="getDoctorShift()" name="collect_staff" style="width: 100%" id="collect_staff_select">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($doctorlist as $dkey => $value) { ?>
                                                            <option value="<?php echo $value["id"] ?>"<?php
                                                        if ((isset($doctorlist_select)) && ($doctorlist_select == $value["id"])) {
                                                                echo "selected";
                                                            }
                                                            ?>><?php echo $value["name"] . " " . $value["surname"] ." (". $value["employee_id"].")" ?></option>
                                                        <?php }?>
                                                    </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                            <label><?php echo $this->lang->line("shift"); ?></label>
                                                    <select class="form-control select2" name="shift" style="width: 100%" id="shift">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line("appointment_priority"); ?></label>
                                                <select class="form-control select2 appointment_priority_select2"  name='priority' style="width:100%">
                                                    <option value=""><?php echo $this->lang->line("select"); ?></option>
                                                    <?php foreach ($appoint_priority_list as $dkey => $dvalue) { ?>
                                                    <option value="<?php echo $dvalue["id"]; ?>"> 
                                                        <?php echo $dvalue["appoint_priority"]; ?>
                                                    </option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                            </div>
                            <div class="box-body row pt0">
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                            <label><?php echo $this->lang->line("source"); ?></label>
                                                    <select class="form-control select2" name="appointment_type" style="width: 100%" id="appointment_type">
                                                        <option value=""><?php echo $this->lang->line("select"); ?></option>
                                                    <?php foreach($appointment_type as $typekey => $typevalue){ ?>
                                                        <option value="<?php echo $typekey; ?>"><?php echo $typevalue; ?></option>
                                                   <?php } ?>
                                                    </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                            <label><?php echo $this->lang->line("status"); ?></label>
                                                <select class="form-control select2" name="appointment_status" style="width: 100%" id="appointment_status">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($appointment_status as $appointment_status_key => $appointment_status_value) {  ?>
                                                    <option value="<?php echo $appointment_status_key ?>"><?php echo $appointment_status_value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                          
                                        <div class="col-sm-6 col-md-3" id="fromdate" style="display: none">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                                <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                                <span class="text-danger" id="error_date_from"><?php echo form_error('date_from'); ?></span>
												 
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3" id="todate" style="display: none">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                                <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                                <span class="text-danger" id="error_date_to"><?php echo form_error('date_to'); ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                 <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                            </div>
                                        </div> 
                                    </div>
                    </form>

                    <div class="box border0 clear">
                        <div class="box-header ptbnull"></div>
                        <div class="box-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('appointment_report'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('appointment_report'); ?>">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                                            <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                            <th><?php echo $this->lang->line('phone'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <th><?php echo $this->lang->line('doctor'); ?></th>
                                            <th><?php echo $this->lang->line('source'); ?></th>
                                            <?php 
                                            if (!empty($fields)) {
                                                foreach ($fields as $fields_key => $fields_value) {
                                                    ?>
                                                    <th><?php echo $fields_value->name; ?></th>
                                                    <?php
                                                } 
                                             }
                                            ?> 
                                            <th class="text text-right"><?php echo $this->lang->line('discount').' (%)'; ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('fees').' ('.$currency_symbol.')'; ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('status'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     </tbody>  
                                </table>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
    </section>
</div>
<script type="text/javascript">
    function showdate(value) {
        if (value == 'period') {
            $('#fromdate').show();
            $('#todate').show();
        } else {
            $('#fromdate').hide();
            $('#todate').hide();
        }
    }
</script>
<script>
( function ( $ ) {
    'use strict';

    $(document).ready(function () {
         emptyDatatable('allajaxlist', 'data');
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/appointment/checkvalidation',
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == "fail") {
                   $.each(data.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {
                    $("#error_search_type").html('');
                     $("#error_collect_staff").html('');
                     $("#error_date_from").html('');
                     $("#error_date_to").html('');
                        initDatatable('allajaxlist', 'admin/appointment/appointmentreports/',data.param,[],100,[
                            { "aTargets": [ -3 ] ,'sClass': 'dt-body-right'},
                            { "aTargets": [ -2 ] ,'sClass': 'dt-body-right'},
                            { "aTargets": [ -1 ] ,'sClass': 'dt-body-right', "bSortable": false}
                        ]);
                        
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );

    function getDoctorShift(prev_val = 0){
        var doctor_id = $("#collect_staff_select").val();
        var select_box = "<option value=''><?php echo $this->lang->line('select'); ?></option> ";
        $.ajax({
            type: 'POST',
            url: base_url + "admin/onlineappointment/doctorshiftbyid",
            data: {doctor_id:doctor_id},
            dataType: 'json',
            success: function(res){
                $.each(res, function(i, list){
                    selected = list.id == prev_val ? "selected" : "";
                    select_box += "<option value='"+ list.id +"' "+selected+">"+ list.name +"</option>";
                });
                $("#shift").html(select_box);
           }
        });
    }
</script>