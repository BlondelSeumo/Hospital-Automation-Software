<style type="text/css">
    .lead_template {
   
    font-size: 16px;
    font-weight: 300;
    line-height: 1.4;
    padding: 0px;
    margin-bottom: 5px;
}
.lead_template_variable {
    font-size: 16px;
    font-weight: 300;
    line-height: 1.4;
    padding: 0px;
    margin-bottom: 5px;
}
</style>
 <div class="row">
            <div class="col-md-12">
                 <p class="lead_template"><?php echo $this->lang->line($record->event); ?></p>
                 <input type="hidden" name="temp_id" value="<?php echo $record->id; ?>">
                <div class="form-group">
                    <label for="form_message"><?php echo $this->lang->line('subject'); ?></label>
                    <input type="text" name="template_subject" id="template_subject" class="form-control"value="<?php echo $record->subject; ?>">
                     <div class="text text-danger template_subject_error"></div>
                </div>
                	<div class="form-group">
                        <label for="form_message"><?php echo $this->lang->line('staff_message'); ?></label>
                        <textarea id="form_message" name="staff_message" class="form-control" rows="7"><?php echo $record->staff_message; ?></textarea>
                        <div class="text text-danger staff_message_error"></div>
                        <div class="hide_in_read">
                        <p class="lead_template_variable"><?php echo $this->lang->line('you_can_use_variables'); ?></p>
                            <b><?php echo $record->variables; ?></b>
                        </div>
                    </div>
                    <?php if(!in_array($record->event,$is_patient_notification)){ ?>
                    <div class="form-group">
                        <label for="form_message"><?php echo $this->lang->line('patient_message'); ?></label>
                        <textarea id="form_message" name="patient_message" class="form-control" rows="7"><?php echo $record->patient_message; ?></textarea>
                        <div class="text text-danger patient_message_error"></div>
                        <div class="hide_in_read">
                        <p class="lead_template_variable"><?php echo $this->lang->line('you_can_use_variables'); ?></p>
                            <b><?php echo $record->variables; ?></b>
                        </div>
                    </div>
                <?php }?>
            </div>
        </div>