<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender_Patient();
?> 
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
          <h3 class="box-title titlefix"><?php echo $this->lang->line('appointment_billing'); ?></h3>
          <div class="box-tools pull-right">           
            <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm addappointment"> <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_appointment'); ?></a>
          </div>
          </div><!-- /.box-header -->
          <div class="box-body">
            <div class="download_label"><?php echo $this->lang->line('appointment_billing'); ?></div>
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('appointment_billing'); ?>" >
                <thead>
                  <tr>
                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                    <th><?php echo $this->lang->line('appointment_no'); ?></th>
                    <th><?php echo $this->lang->line('appointment_date'); ?></th>
                    <th width="10%"><?php echo $this->lang->line('phone'); ?></th>
                    <th><?php echo $this->lang->line('gender'); ?></th>
                    <th><?php echo $this->lang->line('doctor'); ?></th>
                    <th><?php echo $this->lang->line('source'); ?></th>
                    <th><?php echo $this->lang->line('priority'); ?></th>
                    <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                    <th><?php echo $this->lang->line('live_consultant'); ?></th>                    
                    <?php } ?>
                    <?php 
                      if (!empty($fields)) {
                      foreach ($fields as $fields_key => $fields_value) {
                    ?>
                    <th><?php echo $fields_value->name; ?></th>
                    <?php
                    } 
                    }
                    ?>
                    <th><?php echo $this->lang->line('discount')." (%)"; ?></th>
                    <th><?php echo $this->lang->line('fees')." (".$currency_symbol.")"; ?></th>
                    <th class="text-right"><?php echo $this->lang->line('status'); ?></th>
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
  </section>
</div>

<div class="modal fade" id="myModal" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-media-content mx-2">
      <div class="modal-header modal-media-header">
        <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
        <div class="row">
          <div class="col-sm-6 col-xs-6">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-7 col-xs-9">
                    <div class="p-2 select2-full-width">
                        <select class="form-control patient_list_ajax" form="formadd" id="addpatient_id" name='patient_id'>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-5 col-xs-1">
                    <div class="p-2">
                       <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) {?>
                        <a data-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient"><i class="fa fa-plus"></i>  <span><?php echo $this->lang->line('new_patient'); ?></span></a>
                        <?php }?>
                    </div>    
                </div>     
            </div>
          </div><!--./col-sm-8-->
        </div><!-- ./row -->
      </div>
      <form id="formadd" accept-charset="utf-8" method="post">
        <div>
        <div class="modal-body pb0">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="exampleInputFile"><?php echo $this->lang->line('doctor'); ?></label>
                    <small class="req"> *</small>
                    <div>
                      <select class="form-control select2 doctor_select2" name="doctorid" onchange="getDoctorShift(this);getDoctorFees(this)" <?php
                        if ((isset($disable_option)) && ($disable_option == true)) {
                        echo 'disabled';
                        }
                        ?> name='doctor' id="doctorid" style="width:100%" >
                        <option value="<?php echo set_value('doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                        <?php foreach ($doctors as $dkey => $dvalue) {
                        ?>
                        <option value="<?php echo $dvalue["id"]; ?>" <?php
                        if ($doctor_select == $dvalue['id']) {
                        echo 'selected';
                        }
                        ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"] ." (". $dvalue["employee_id"].")" ?></option>
                        <?php }?>
                      </select>
                      <input type="hidden" name="charge_id" value="" id="charge_id" />
                    </div>
                    <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="doctor_fees"><?php echo $this->lang->line("doctor_fees")." (".$currency_symbol.")"; ?></label>
                    <small class="req"> *</small>
                    <div>   
                        <input type="text" name="amount" id="doctor_fees" class="form-control" readonly="readonly">
                    </div>
                    <span class="text-danger"><?php echo form_error('doctor_fees'); ?></span>
                  </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('shift'); ?></label><span class="req"> *</span>
                        <select name="global_shift" id="global_shift" class="select2" style="width:100%">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group" style="position: relative; overflow:visible !important">
                    <label><?php echo $this->lang->line('appointment_date'); ?></label>
                    <small class="req"> *</small>
                    <input type="text" id="datetimepicker" name="date" class="form-control datetime">
                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                  </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="slot"><?php echo $this->lang->line('slot'); ?></label>
                        <span class="req"> *</span>
                        <select name="slot" id="slot" onchange="validateTime(this)" class="form-control">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                        </select>
                        <span class="text-danger"><?php echo form_error('slot'); ?></span>
                    </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="exampleInputFile"><?php echo $this->lang->line('appointment_priority'); ?></label>
                    <div>
                      <select class="form-control select2 appointment_priority_select2"  name='priority' style="width:100%">
                        <?php foreach ($appoint_priority_list as $dkey => $dvalue) { ?>
                        <option value="<?php echo $dvalue["id"]; ?>"> <?php echo $dvalue["appoint_priority"]; ?></option>
                        <?php }?>
                      </select>
                    </div>
                    <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                      <label><?php echo $this->lang->line('payment_mode'); ?></label> 
                      <select class="form-control payment_mode" name="payment_mode">
                      <?php foreach ($payment_mode as $key => $value) { ?>
                          <option value="<?php echo $key ?>"><?php echo $value ?></option>
                      <?php } ?>
                      </select>    
                      <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                  </div>
                </div>
                <div class="col-sm-3">
                      <div class="form-group">
                        <label for="appointment_status"><?php echo $this->lang->line('status'); ?><small class="req"> *</small></label>
                        <select name="appointment_status" class="form-control" id="appointment_status">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($appointment_status as $appointment_status_key => $appointment_status_value) {  ?>
                            <option value="<?php echo $appointment_status_key ?>"><?php echo $appointment_status_value ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('discount_percentage'); ?></label><small class="req"> *</small> 
                            <input type="number" min="1" max="100" name="discount_percentage" id="discount_percentage" class="form-control">
                            <span class="text-danger"></span>
                        </div>
                </div>
                <div class="cheque_div" style="display: none;">                        
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small> 
                            <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                            <span class="text-danger"></span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small> 
                            <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                            <span class="text-danger"></span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('attach_document'); ?></label>
                            <input type="file" class="filestyle form-control" name="document">
                            <span class="text-danger"><?php echo form_error('document'); ?></span> 
                        </div>
                    </div>
                </div>
                <div class="col-sm-9">
                  <div class="form-group">
                    <label for="message"><?php echo $this->lang->line('message'); ?></label>
                    <textarea name="message" id="note" class="form-control" ></textarea>
                    <span class="text-danger"><?php echo form_error('message'); ?></span>
                  </div>
                </div>
                <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                <div class="col-sm-5 col-md-4 col-lg-4">
                  <div class="form-group">
                    <label for="exampleInputFile"><?php echo $this->lang->line('live_consultant_on_video_conference'); ?></label>
                    <small class="req">*</small>
                    <div>
                    <select name="live_consult" id="live_consult" class="form-control">
                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                            ?>
                            <option value="<?php echo $yesno_key ?>" <?php
                                    if ($yesno_key == 'no') {
                                        echo "selected";
                                    }
                                    ?> ><?php echo $yesno_value ?>
                            </option>
                            <?php } ?>
                    </select>
                    </div>
                    <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                  </div>
                </div>
                <?php } ?>
                <div>
                <?php echo display_custom_fields('appointment'); ?>
                </div>
              </div><!--./row-->
            </div><!--./col-md-12-->
          </div><!--./row-->
        </div><!--./modal-body-->
        </div>
        <div class="modal-footer">
          <div class="pull-right">
            <button type="submit" id="formaddbtn" name="save" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
          </div>
          <div class="pull-right mr-10">
              <button type="submit" id="formaddprintbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" name="save_print" class="btn btn-info pull-right printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- dd -->

<div class="modal fade" id="rescheduleModal" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-media-content mx-2">
      <div class="modal-header modal-media-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="model_title"></h4>
      </div>
      <form id="rescheduleform" accept-charset="utf-8" method="post">
        <div>
          <div class="modal-body pb0">
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="row">
                  <input type="hidden" name="appointment_id" id="appointment_id">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="exampleInputFile">
                      <?php echo $this->lang->line('doctor'); ?></label> 
                      <small class="req"> *</small>
                      <div>
                        <select class="form-control" style="width:100%" id="rdoctor" disabled>
                          <option value="<?php echo set_value('doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                          <?php foreach ($doctors as $dkey => $dvalue) {
                          ?>
                          <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] . " " . $dvalue["surname"]." (".$dvalue["employee_id"].")" ?></option>
                          <?php }?>
                        </select>
                        <span class="text-danger"><?php echo form_error('rdoctor'); ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="doctor_fees"><?php echo $this->lang->line("doctor_fees")." (".$currency_symbol.")"; ?></label>
                      <small class="req"> *</small>
                      <div>   
                          <input type="text" name="doctor_fees" id="rdoctor_fees_edit" class="form-control" readonly="readonly">
                      </div>
                      <span class="text-danger"><?php echo form_error('doctor_fees'); ?></span>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('shift'); ?></label><span class="req"> *</span>
                        <select name="rglobal_shift" id="rglobal_shift_edit" onchange="getreschsduleShift()" class="select2" style="width:100%">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                        </select>
                        <span class="text-danger"><?php echo form_error('rglobal_shift'); ?></span>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label><?php echo $this->lang->line('appointment_date') ?></label>
                      <small class="req"> *</small>
                      <input type="text" id="rdates" name="appointment_date" class="form-control datetime" value="<?php echo set_value('dates'); ?>">
                      <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                        <label for="slot"><?php echo $this->lang->line('slot'); ?></label>
                        <span class="req"> *</span>
                        <select name="rslot" id="rslot_edit" class="form-control">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                        </select>
                        <input type="hidden" id="rslot_edit_field" name="rslot_edit_field" />
                        <input type="hidden" id="rdoctor_id" name="rdoctor_id" />
                        <span class="text-danger"><?php echo form_error('rslot'); ?></span>
                    </div>
                  </div>
                   <div class="col-sm-3">
                    <div class="form-group">
                      <label for="exampleInputFile">
                      <?php echo $this->lang->line('appointment_priority'); ?></label>
                      <div>
                        <select class="form-control select2" name='priority' style="width:100%" id="edit_appoint_priority">
                        <?php foreach ($appoint_priority_list as $dkey => $dvalue) {
                        ?>
                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["appoint_priority"]; ?></option>
                        <?php }?>
                        </select>
                      </div>
                    </div>
                  </div>
				                  <div class="col-sm-3">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('discount_percentage'); ?></label><small class="req"> *</small> 
                            <input type="number" min="1" max="100" name="discount_percentage" id="rdiscount_percentage" class="form-control">
                            <span class="text-danger"></span>
                        </div>
                    </div>
                   <div class="col-sm-3">
                      <div class="form-group">
                        <label for="appointment_status"><?php echo $this->lang->line('status'); ?><small class="req"> *</small></label>
                        <select name="edit_appointment_status"  class="form-control" id="edit_appointment_status">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($appointment_status as $appointment_status_key => $appointment_status_value) {  ?>
                            <option value="<?php echo $appointment_status_key ?>" ><?php echo $appointment_status_value ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div> 
                  <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label><?php echo $this->lang->line('live_consultant_on_video_conference'); ?></label> <small class="req">*</small>
                      <select name="live_consult" id="edit_liveconsult" class="form-control">
                        <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                            ?>
                            <option value="<?php echo $yesno_key ?>" <?php
                                    if ($yesno_key == 'no') {
                                        echo "selected";
                                    }
                                    ?> ><?php echo $yesno_value ?>
                            </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <?php } ?>
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label for="message"><?php echo $this->lang->line('message'); ?></label>                      
                      <textarea name="message" id="message" class="form-control" ><?php echo set_value('message'); ?></textarea>
                      <span class="text-danger"><?php echo form_error('message'); ?></span>
                    </div>
                  </div>
                  <div class="" id="customfield" ></div> 
                  <!-- <div class="" id="customfield" ></div>  -->
                </div><!--./row-->
              </div><!--./col-md-12-->
            </div><!--./row-->
          </div><!--./modal-body-->
        </div>
        <div class="modal-footer">
          <div class="pull-right">
            <button type="submit" id="rescheduleformbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info" ><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-media-content mx-2">
      <div class="modal-header modal-media-header">
        <button type="button" class="close" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
        <div class="modalicon">
          <div id="edit_delete">
            <a href="#" data-target="#editModal" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a><a href="#" data-toggle="tooltip" onclick="delete_recordById('<?php echo base_url(); ?>admin/appointment/delete/#', '<?php echo $this->lang->line('success_message') ?>')" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash" ></i></a></div>
        </div>
        <h4 class="modal-title"><?php echo $this->lang->line('appointment_details'); ?></h4>
      </div>
      <div class="modal-body pt0 pb0">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <form id="view" accept-charset="utf-8" method="get" class="pt5 pb5">
              <div class="table-responsive">
                <table class="table mb0 table-striped table-bordered examples">
                <tr>
                  <th width="15%"><?php echo $this->lang->line('patient_name'); ?></th>
                  <td width="35%"><span id='patient_names'></span></td>
                  <th width="15%"><?php echo $this->lang->line('appointment_no'); ?></th>
                  <td width="35%"><span id="appointmentno"></span>
                  </td>
                </tr>
                <tr>
                  <th width="15%"><?php echo $this->lang->line('age'); ?></th>
                  <td width="35%"><span id='patient_age'></span></td>
                  <th width="15%"><?php echo $this->lang->line('appointment_s_no'); ?></th>
                  <td width="35%"><span id="appointment_s_no"></span>
                  </td>
                </tr>
                <tr>
                  <th width="15%"><?php echo $this->lang->line('gender'); ?></th>
                  <td width="35%"><span id="genders"></span></td>
                  <th width="15%"><?php echo $this->lang->line('appointment_date'); ?></th>
                  <td width="35%"><span id='dating'></span></td>                  
                </tr>
                <tr>
                  <th width="15%"><?php echo $this->lang->line('email'); ?></th>
                  <td width="35%"><span id='emails'></span></td>
                  <th width="15%"><?php echo $this->lang->line('phone'); ?></th>
                  <td width="35%"><span id="phones"></span>
                  </td>
                </tr>
                <tr>
                  <th width="15%"><?php echo $this->lang->line('doctor'); ?></th>
                  <td width="35%"><span id='doctors'></span></td>
                  <th width="15%"><?php echo $this->lang->line('message'); ?></th>
                  <td width="35%"><span id="messages"></span>
                  </td>
                </tr>
                <tr>  
                <th width="15%"><?php echo $this->lang->line('department'); ?></th>
                  <td width="35%"><span id="department_name"></span></td>                
                  <th width="15%"><?php echo $this->lang->line('appointment_priority'); ?></th>
                  <td width="35%"><span id='appointpriority'></span></td>
                </tr>                 
                <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                <tr>
                  <th width="15%"><?php echo $this->lang->line('live_consultation'); ?></th>
                  <td width="35%"><span id="liveconsult"></span></td>
                  <th width="15%"><?php echo $this->lang->line('status'); ?></th>
                  <td width="35%"><span id='status' style="text-transform: capitalize;"></span></td>
                </tr>
                <?php } ?>
                <tr>
                  <th width="15%"><?php echo $this->lang->line('shift'); ?></th>
                  <td width="35%"><span id="global_shift_view"></span></td>
                  <th width="15%"><?php echo $this->lang->line('slot'); ?></th>
                  <td width="35%"><span id='doctor_shift_view' style="text-transform: capitalize;"></span></td>
                </tr>
                <tr>
                  <th width="15%"><?php echo $this->lang->line('amount'); ?></th>
                  <td width="35%"><span id='pay_amount'></span></td>
                  <th width="15%"><?php echo $this->lang->line('payment_mode'); ?></th>
                  <td width="35%"><span id="payment_mode"></span>
                  </td>
                </tr>
                 <tr  id="payrow" style="display:none">
                  <th width="15%"><?php echo $this->lang->line('cheque_no'); ?></th>
                  <td width="35%"><span id='spn_chequeno'></span></td>
                  <th width="15%"><?php echo $this->lang->line('cheque_date'); ?></th>
                  <td width="35%"><span id="spn_chequedate"></span>
                  </td>
                </tr>
                <tr id="paydocrow" style="display:none">
                   <th width="15%"><?php echo $this->lang->line('document'); ?></th>
                  <td width="35%" id='spn_doc'><span ></span></td>
                </tr>
                <tr>
                  <th width="15%"><?php echo $this->lang->line('source'); ?></th>
                  <td width="35%"><span id="source"></span></td>
                   <th width="15%"><?php echo $this->lang->line('transaction_id'); ?></th>
                  <td width="35%"><span id="trans_id"></span></td>
                </tr>
                 <tr>
                   <th width="15%"><?php echo $this->lang->line('payment_note'); ?></th>
                  <td width="35%"><span id="payment_note"></span></td>                  
                </tr>
                </table>
                  <table class="table mb0 table-striped table-bordered examples" id="field_data">
                  </table>                
              </div>
            </form>
          </div><!--./col-md-12-->
        </div><!--./row-->
      </div>
    </div>
  </div>
</div>
<script>
	$("#appointment_status").change(function(){
  var appointment_status = $('#appointment_status').val();
      var doctor_id = $('#doctorid').val();    
      if(appointment_status == 'approved'){
        $.ajax({
            url: baseurl+'admin/appointment/getDoctorFees/',
            type: "POST",
            data: {doctor_id: doctor_id},
            dataType: 'json',
            success: function (res) {
              $("#doctor_fees").val(res.fees);
              $("#charge_id").val(res.charge_id);
          }
        });
      }else{
          $('#doctor_fees').val('0');
      }
});
  
  $("#edit_appointment_status").change(function(){

      var edit_appointment_status = $('#edit_appointment_status').val();
      var doctor_id = $('#rdoctor').val();  

      if(edit_appointment_status == 'approved'){
        $.ajax({
            url: baseurl+'admin/appointment/getDoctorFees/',
            type: "POST",
            data: {doctor_id: doctor_id},
            dataType: 'json',
            success: function (res) {
              $("#rdoctor_fees_edit").val(res.fees);              
          }
        });
      }else{
          $('#rdoctor_fees_edit').val('0');
      }
});


   $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.filestyle','#addPaymentModal').dropify();
        $('.cheque_div').css("display", "block");
      }else{
        $('.cheque_div').css("display", "none");
      }
    });
</script>

<script type="text/javascript">
  $(function () {
    $('#easySelectable').easySelectable();
  })
</script>
<script type="text/javascript">
  $(function () {
    $('.select2').select2()
  });

  function holdModal(modalId) {
    $('#' + modalId).modal({
      backdrop: 'static',
      keyboard: false,
      show: true
    });
  }

  (function ($) {
    //selectable html elements
    $.fn.easySelectable = function (options) {
      var el = $(this);
      var options = $.extend({
      'item': 'li',
      'state': true,
      onSelecting: function (el) {

      },
      onSelected: function (el) {

      },
      onUnSelected: function (el) {

      }
      }, options);
      el.on('dragstart', function (event) {
        event.preventDefault();
      });
        el.off('mouseover');
        el.addClass('easySelectable');
        if (options.state) {
        el.find(options.item).addClass('es-selectable');
        el.on('mousedown', options.item, function (e) {
        $(this).trigger('start_select');
        var offset = $(this).offset();
        var hasClass = $(this).hasClass('es-selected');
        var prev_el = false;
        el.on('mouseover', options.item, function (e) {
        if (prev_el == $(this).index())
        return true;
        prev_el = $(this).index();
        var hasClass2 = $(this).hasClass('es-selected');
      if (!hasClass2) {
        $(this).addClass('es-selected').trigger('selected');
        el.trigger('selected');
        options.onSelecting($(this));
        options.onSelected($(this));
      } else {
        $(this).removeClass('es-selected').trigger('unselected');
        el.trigger('unselected');
        options.onSelecting($(this))
        options.onUnSelected($(this));
      }
      });
      if (!hasClass) {
        $(this).addClass('es-selected').trigger('selected');
        el.trigger('selected');
        options.onSelecting($(this));
        options.onSelected($(this));
      } else {
        $(this).removeClass('es-selected').trigger('unselected');
        el.trigger('unselected');
        options.onSelecting($(this));
        options.onUnSelected($(this));
      }
      var relativeX = (e.pageX - offset.left);
      var relativeY = (e.pageY - offset.top);
      });
      $(document).on('mouseup', function () {
        el.off('mouseover');
      });
      } else {
        el.off('mousedown');
      }
    };
  })(jQuery);
</script>
<script type="text/javascript">
  $(document).ready(function (e) {
           $("form#formadd button[type=submit]").click(function() {            
         $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });
	
  $("#formadd").on('submit', (function (e) {
    var did = $("#doctorid").val();
    $("#doctorinputid").val(did);
    var sub_btn_clicked = $("button[type=submit][clicked=true]");                  
    var sub_btn_clicked_name=sub_btn_clicked.attr('name');
      if(sub_btn_clicked_name === "save_print") {                            
           $("#formaddprintbtn").button('loading');
       }else{
        $("#formaddbtn").button('loading');
       }  
      e.preventDefault();
      $.ajax({
        url: base_url+'admin/appointment/add',
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
          if(sub_btn_clicked_name === "save_print") {                            
           printAppointment(data.appointment_id);
         }else{
          successMsg(data.message);
          window.location.reload(true);
         }   
        }
        if(sub_btn_clicked_name === "save_print") {                            
           $("#formaddprintbtn").button('reset');
         }else{
            $("#formaddbtn").button('reset');
         }
        },
        error: function () {

      }
    });
  }));
});

function printAppointment(id){
    $.ajax({
            url: base_url+'admin/appointment/printAppointmentBill',
            type: "POST",
            data: {'appointment_id': id},
            dataType: 'json',
               beforeSend: function() {
                           
               },
            success: function (data) {      
           popup(data.page);
            },

             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");           
               
      },
      complete: function() {           
     
      }
        });
}	  
	
$(document).ready(function (e) {
$("#formedit").on('submit', (function (e) {
  $("#formeditbtn").button('loading');
  e.preventDefault();
    $.ajax({
      url: base_url+'admin/appointment/update',
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
        $("#formeditbtn").button('reset');
      },
      error: function () {

      }
    });
  }));

  $("#datetimepicker").on("dp.change", function (e) {
    if($("#global_shift").val() != ''){
        getShift();
    }
  });

  $("#dates").on("dp.change", function (e) {
    if($("#global_shift_edit").val() != ''){
        getShiftEdit();
    }
  });

  $("#rdates").on("dp.change", function (e) {
    if($("#rglobal_shift_edit").val() != ''){
        getreschsduleShift();
    }
  });

  $("#rescheduleform").on('submit', (function (e) {
      $("#rescheduleformbtn").button('loading');
      e.preventDefault();
        $.ajax({
          url: baseurl+'admin/appointment/reschedule',
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
            $("#rescheduleformbtn").button('reset');
          },
          error: function () {

          }
        });
  }));

});

function get_PatientDetails(id) {
  $("#patient_name").html("patient_name");
  $('#gender option').removeAttr('selected');
  $.ajax({
    url: base_url+'admin/patient/patientDetails',
    type: "POST",
    data: {id: id},
    dataType: 'json',
    success: function (res) {
      if (res) {
      $('#patient_name').val(res.patient_name);
      $('#patientid').val(res.id);      
      $('#guardian_name').html(res.guardian_name);
      $('#phone').val(res.mobileno);
      $('#email').val(res.email);
      $("#age").html(res.age);
      $("#bp").html(res.bp);
      $("#month").html(res.month);
      $("#symptoms").html(res.symptoms);
      $("#known_allergies").html(res.known_allergies);
      $("#address").html(res.address);
      $("#height").html(res.height);
      $("#weight").html(res.weight);
      $("#marital_status").html(res.marital_status);
      $('#gender option[value="'+res.gender+'"]').attr("selected","selected");
    } else {
      $('#patient_name').val('');
      $('#phone').val("");
      $('#email').val("");
      $("#note").val("");
    }
  }
  });
}

function getBed(bed_group, bed = '', active, htmlid = 'bed_no') {
        var div_data = "";
        $('#' + htmlid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $("#" + htmlid).select2("val", 'l');
        $.ajax({
            url: base_url+'admin/setup/bed/getbedbybedgroup',
            type: "POST",
            data: {bed_group: bed_group, bed_id: bed, active: active},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {                  
                    div_data += "<option value=" + obj.id + ">" + obj.name + "</option>";
                });
                $("#" + htmlid).html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                $('#' + htmlid).append(div_data);
                $("#" + htmlid).select2().select2('val', bed);
            }
        });
    }

    function viewreschedule(id,modeltype){
        
		if(modeltype == '1'){
			$('#model_title').html('<?php echo $this->lang->line('reschedule'); ?>');
		} else if (modeltype == '2'){
			$('#model_title').html('<?php echo $this->lang->line('approve_appointment'); ?>');  
		}
      
		$('#rescheduleModal').modal('show');
		$('#appointment_id').val(id);
		$.ajax({
			url: baseurl+'admin/appointment/getDetailsAppointment',
			type: "GET",
			data: {appointment_id: id},
			dataType: 'json',
			success: function (data) {	  
		  
				$('#customfield').html(data.custom_fields_value);
					$("#rdoctor").val(data.doctor).trigger("change");
				$("#rdates").val(data.date);         
				$("#rdoctor_id").val(data.doctor);		  
				$("#edit_appoint_priority").val(data.priority).trigger("change");
				$("#message").val(data.message); 
				$("#edit_appointment_status").val(data.appointment_status);  
				$("#rdiscount_percentage").val(data.discount_percentage); 
				getDoctorShift("",data.doctor,data.shift_id);
				$('select[id="rdoctor"] option[value="' + data.doctor + '"]').attr("selected", "selected");
				$('select[id="edit_liveconsult"] option[value="' + data.live_consult + '"]').attr("selected", "selected");		 
				$("#rslot_edit").val(data.slot_id);
				$("#rslot_edit_field").val(data.slot_id);         
				$("#rdoctor_fees_edit").val(data.standard_amount);         
          
			}
		});
    }

  function getRecord(id) {
    $("#viewModal").modal('hide');
    $('#myModaledit').modal('show');
    $.ajax({
      url: baseurl+'admin/bill/get_appointment_detail',
      type: "GET",
      data: {appointment_id: id},
      dataType: 'json',
      success: function (data) {
        $('#customfield').html(data.custom_fields_value);
        $("#id").val(data.id);
        $("#doctor").val(data.doctor).trigger("change");
        $("#dates").val(data.date); 
        $("#slot_edit_field").val(data.shift_id);
        getDoctorShift("",data.doctor,data.global_shift_id);
        $("#edit_appointment_no").val(data.appointment_no);
        $("#edit_appoint_priority").val(data.priority).trigger("change");
        $("#message").val(data.message);      
        if(data.patient_id == null){
          data.patient_id = ""
        }
        var option = new Option(data.patients_name, data.patient_id, true, true);
        $("#myModaledit .patient_list_ajax").append(option).trigger('change');
        $("#myModaledit .patient_list_ajax").trigger({
            type: 'select2:select',
            params: {
                data: data
            }
        });
        $('select[id="edit_gender"] option[value="' + data.patients_gender + '"]').attr("selected", "selected");
        $('select[id="doctor"] option[value="' + data.doctor + '"]').attr("selected", "selected");
        $('select[id="appointment_status"] option[value="' + data.appointment_status + '"]').attr("selected", "selected");
        $('select[id="edit_liveconsult"] option[value="' + data.live_consult + '"]').attr("selected", "selected");
        $('select[id="edit_appoint_priority"] option[value="' + data.priority + '"]').attr("selected", "selected");

      },
    })
  }

function viewDetail(id) {
  $('#viewModal').modal('show');
  $.ajax({
    url: baseurl+'admin/bill/get_appointment_detail',
    type: "GET",
    data: {appointment_id: id},
    dataType: 'json',
    success: function (data) {
      var table_html = '';
      $.each(data.field_data, function (i, obj)
      {
      if (obj.field_value == null) {
      var field_value = "";
      } else {
      var field_value = obj.field_value;
      }

      var name = obj.name ;
      var is_patient = obj.visible_on_patient_panel ;
      if(is_patient==1){
        table_html += "<tr><th width='15%'><span id='vcustom_name'>" + capitalizeFirstLetter(name) + "</span></th> <td width='85%'><span id='vcustom_value'>" + field_value + "</span></td></tr><th></th><td></td>";
      }
      
  });
  $("#field_data").html(table_html);
  $("#dating").html(data.date);  
  $("#appointmentno").html(data.appointment_no);
  $("#patient_names").html(data.patients_name);
  $("#genders").html(data.patients_gender);
  $("#emails").html(data.patient_email);
  $("#appointpriority").html(data.appoint_priority);
  $("#phones").html(data.patient_mobileno);
  $("#doctors").html(data.name + " " + data.surname+" ("+data.employee_id+")");
  $("#messages").html(data.message);
  $("#liveconsult").html(data.edit_live_consult);
  $("#global_shift_view").html(data.global_shift_name);
  $("#doctor_shift_view").html(data.doctor_shift_name);
  $("#source").html(data.source);
  $("#pay_amount").html('<?php echo $currency_symbol; ?>'+data.amount);
  $("#payment_mode").html(data.payment_mode);
  $("#trans_id").html(data.transaction_id);
  $("#payment_note").html(data.payment_note);
  $("#patient_age").html(data.patient_age);
  $("#appointment_s_no").html(data.appointment_serial_no);
  $("#department_name").html(data.department_name);

  if(data.payment_mode=="Cheque"){
    $("#payrow").show();
    $("#paydocrow").show();
    $("#spn_chequeno").html(data.cheque_no);
    $("#spn_chequedate").html(data.cheque_date);
    $("#spn_doc").html(data.doc);
  }else{
    $("#payrow").hide();
    $("#paydocrow").hide();
    $("#spn_chequeno").html("");
    $("#spn_chequedate").html("");
  }  

 
  var label = "";
   if (data.appointmentstatus == "Approved") {
    var label = "class='label label-success'";
  } else if (data.appointmentstatus == "Pending") {
    var label = "class='label label-warning'";
  } 
  else{
    var label = "class='label label-danger'";
  }
  
 
  

  $("#status").html("<small " + label + " >" + data.appointmentstatus + "</small>");
  
  $("#edit_delete").html("<a href='#' data-toggle='tooltip'  onclick='printAppointment(" + id +")' data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php if ($this->rbac->hasPrivilege('appointment', 'can_delete')) {?><a href='#' data-toggle='tooltip'  onclick='delete_record(" + id +")' data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php }?> ");

  },
  });
}

function delete_record(id) {
  if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
    $.ajax({
      url: base_url+'admin/appointment/delete/' + id,
      type: "POST",
      data: {patient_id: id},
      dataType: 'json',
      success: function (res) {
        if (res.status == 'success') {
        $('#viewModal').modal('hide');
        successMsg(res.message);
        table.ajax.reload();
      }
      }
    })
  }
}

</script>
<script type="text/javascript">
  function askconfirm() {

    if (confirm("<?php echo $this->lang->line('approve_appointment'); ?>") ) {
      return true;
    } else {
      return false;
    }

    } 
  
  $('#myModal').on('hidden.bs.modal', function () {
    $(".appointment_priority_select2").select2("val", "");
    $(".doctor_select2").select2("val", "");
    $("#addpatient_id").select2("val", "");
    $('#formadd').find('input:text, input:password, input:file, textarea').val('');
    $('#formadd').find('select option:selected').removeAttr('selected');
    $('#formadd').find('input:checkbox, input:radio').removeAttr('checked');
  });

  $(".modalbtnpatient").click(function(){   
    $('#formaddpa').trigger("reset");
    $(".dropify-clear").trigger("click");
  });
  
  $(document).ready(function (e) {
      $('#myModal,#viewModal,#myModaledit').modal({
          backdrop: 'static',
          keyboard: false,
          show:false
      });
  });
</script> 
<script type="text/javascript">
  function getDoctorFees(object){
    let doctor_id = object.value;
     $.ajax({
      url: baseurl+'admin/appointment/getDoctorFees/',
      type: "POST",
      data: {doctor_id: doctor_id},
      dataType: 'json',
      success: function (res) {
        $("#doctor_fees").val(res.fees);
        $("#charge_id").val(res.charge_id);
      }
    })
  }

  function getDoctorFeesEdit(object){
    let doctor_id = object.value;
     $.ajax({
      url: baseurl+'admin/appointment/getDoctorFees/',
      type: "POST",
      data: {doctor_id: doctor_id},
      dataType: 'json',
      success: function (res) {
        $("#doctor_fees_edit").val(res.fees);
        $("#rdoctor_fees_edit").val(res.fees);
        $("#charge_id_edit").val(res.charge_id);
      }
    })
  }
</script>
<script>
  function getShift(){

      var div_data = "";
      var date = $("#datetimepicker").val();
      var doctor = $("#doctorid").val();
      var global_shift = $("#global_shift").val();

      $.ajax({
          url: base_url+'admin/onlineappointment/getShift',
          type: "POST",
          data: {doctor: doctor, date: date, global_shift:global_shift},
          dataType: 'json',
          success: function(res){
              $.each(res, function (i, obj)
              {
                  div_data += "<option value=" + obj.id + ">" + obj.start_time +" - "+ obj.end_time +"</option>";
              });
              $("#slot").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
              $('#slot').append(div_data);
          }
      });
  }

  function getShiftEdit(){

      var div_data = "";
      var date = $("#dates").val();
      var doctor = $("#doctor").val();
      var global_shift = $("#global_shift_edit").val();

      $.ajax({
          url: base_url+'admin/onlineappointment/getShift',
          type: "POST",
          data: {doctor: doctor, date: date, global_shift:global_shift},
          dataType: 'json',
          success: function(res){
              $.each(res, function (i, obj)
              {
                  div_data += "<option value=" + obj.id + ">" + obj.start_time +" - "+ obj.end_time +"</option>";
              });
              $("#slot_edit").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
              $('#slot_edit').append(div_data);
              $("#slot_edit").val($("#slot_edit_field").val()).trigger('change');
          }
      });
  }

  function getreschsduleShift(){

      var div_data = "";
      var date = $("#rdates").val();
      var doctor = $("#rdoctor").val();
      var global_shift = $("#rglobal_shift_edit").val();
    
      $.ajax({
          url: baseurl+'admin/onlineappointment/getShift',
          type: "POST",
          data: {doctor: doctor, date: date, global_shift:global_shift},
          dataType: 'json',
          success: function(res){
              $.each(res, function (i, obj)
              {
                  div_data += "<option value=" + obj.id + ">" + obj.start_time +" - "+ obj.end_time +"</option>";
              });
              $("#rslot_edit").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
              $('#rslot_edit').append(div_data);
              $("#rslot_edit").val($("#rslot_edit_field").val()).trigger('change');
          }
      });
  }

  function getDoctorShift(obj,doctor_id = null,global_shift_id=null){
    if(doctor_id == null){
      var doctor_id = obj.value;
    }
    var select = "";
    var select_box = "<option value=''><?php echo $this->lang->line('select'); ?></option> ";
    $.ajax({
        type: 'POST',
        url: base_url + "admin/onlineappointment/doctorshiftbyid",
        data: {doctor_id:doctor_id},
        dataType: 'json',
        success: function(res){
            $.each(res, function(i, list){
                select_box += "<option value='"+ list.id +"'>"+ list.name +"</option>";
            });
            $("#global_shift").html(select_box);
            $("#global_shift_edit").html(select_box);
            $("#rglobal_shift_edit").html(select_box);
            if(global_shift_id!=null){
              $("#global_shift_edit").val(global_shift_id).trigger('change');
              $("#rglobal_shift_edit").val(global_shift_id).trigger('change');
            }
       }
    });
  }

  function validateTime(obj){
    let id = obj.value;
    let date = $("#datetimepicker").val();
    if(id){
      $.ajax({
          url: baseurl+'admin/onlineappointment/getshiftbyid',
          type: "POST",
          data: {id:id,date:date},
          dataType: 'json',
          success: function(res){
            if(res.status){
              alert("<?php echo $this->lang->line("appointment_time_is_expired"); ?>");
            }
          }
      });
    }
    
  }
</script>
<script type="text/javascript">
( function ( $ ) {
  'use strict';
  $(document).ready(function () {
    initDatatable('ajaxlist','admin/bill/getappointmentdatatable',[],[],100);
  });
} ( jQuery ) ) 
</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>