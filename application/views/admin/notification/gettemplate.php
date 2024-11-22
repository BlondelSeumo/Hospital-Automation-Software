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
                 <p class="lead_template"><?php echo $this->lang->line($record->type); ?></p>
                 <input type="hidden" name="temp_id" value="<?php echo $record->id; ?>">
                <div class="form-group">
                    <label for="form_message"><?php echo $this->lang->line('subject'); ?></label>
                    <input type="text" name="template_subject" id="template_subject" class="form-control"value="<?php echo $record->subject; ?>">
                     <div class="text text-danger template_subject_error"></div>
                </div>
                <div class="form-group">
                    <label for="form_message"><?php echo $this->lang->line('template_id'); ?></label>&nbsp; (<small style="color:red"><?php echo $this->lang->line('this_field_require_for_smsgateway'); ?></small>)
                    <input type="text" name="template_id" id="template_id" class="form-control"value="<?php echo $record->template_id; ?>" >
                     <div class="text text-danger template_id_error"></div>
                </div>
                    <div class="form-group">
                        <label for="form_message"><?php echo $this->lang->line('template'); ?></label>
                        <textarea id="form_message" name="template_message" class="form-control" rows="7"><?php echo $record->template; ?></textarea>
                        <div class="text text-danger template_message_error"></div>
                        <div class="hide_in_read">
                        <p class="lead_template_variable"><?php echo $this->lang->line('you_can_use_variables'); ?></p>
                            <b><?php echo $record->variables; ?></b>
                        </div>
                    </div>
            </div>

        </div>