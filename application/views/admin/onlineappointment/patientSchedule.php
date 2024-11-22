<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('doctor_wise_appointment'); ?></h3>
                    </div>
                    <div class="box-body">
                        <form action="<?php echo site_url("admin/onlineappointment/patientschedule"); ?>" method="post" accept-charset="utf-8">
                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-lg-4">
                                <div class="form-group">
                                    <label for="pwd"><?php echo $this->lang->line('doctor') ?></label>
                                    <span class="req"> *</span>
                                    <select name="doctor" id="doctor" class="form-control select2">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($doctors as $doctor_key => $doctor_value) {?>
                                                <option value="<?php echo $doctor_value['id']; ?>" <?php echo $doctor_value["id"]==set_value("doctor")?"selected":""; ?>><?php echo $doctor_value['name'] . " " . $doctor_value['surname']; ?> (<?php echo $doctor_value["employee_id"]; ?>)</option>
                                        <?php }?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-lg-4">
                                <div class="form-group">
                                    <label for="date"><?php echo $this->lang->line('date') . " " ?></label>
                                    <div class='input-group' >
                                        <input type='text' value="<?php echo set_value('date'); ?>" class="form-control date" name="date" /><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm pull-right"><?php echo $this->lang->line('search'); ?></button>
                        </form>
                    </div>
                   
                <?php if (isset($doctor_id)) {   ?>
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('doctor_wise_appointment'); ?></div>
                        <div class="table-responsive mailbox-messages">
                         <table class="table table-hover table-striped table-bordered dt-list" data-export-title="<?php echo $this->lang->line('doctor_wise_appointment');?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th><?php echo $this->lang->line('time'); ?></th>
                                        <th><?php echo $this->lang->line('email'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line("source"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        $(".select2").select2();
        initDatatable('dt-list','admin/onlineappointment/getpatientschedule/?doctor=<?php echo isset($doctor_id)?$doctor_id:""; ?>&date=<?php echo isset($date)?$date:""; ?>');
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
