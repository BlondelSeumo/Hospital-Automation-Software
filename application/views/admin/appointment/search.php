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
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('appointment_details'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('appointment', 'can_add')) {?>
                                <a data-toggle="modal" data-backdrop="static" data-target="#myModal" class="btn btn-primary btn-sm addappointment"> <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_appointment'); ?></a>
                            <?php }?>
                            <?php if ($this->rbac->hasPrivilege('visitor_book', 'can_view')) {?>
                                <a  href="<?php echo base_url(); ?>admin/visitors" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('visitor_book'); ?></a>
                            <?php }?>
                            <?php if ($this->rbac->hasPrivilege('phone_call_log', 'can_view')) {?>
                                <a  href="<?php echo base_url(); ?>admin/generalcall" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('phone_call_log'); ?></a>
                            <?php }if (($this->rbac->hasPrivilege('postal_dispatch', 'can_view')) || ($this->rbac->hasPrivilege('postal_receive', 'can_view'))) {?>
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">
                                    <i class="fa fa-reorder"></i> <?php echo $this->lang->line('postal'); ?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu multi-level pull-right width300" role="menu" aria-labelledby="dropdownMenu1" id="easySelectable">
                                    <?php if ($this->rbac->hasPrivilege('postal_receive', 'can_view')) {?>
                                        <li><a href="<?php echo base_url(); ?>admin/receive"><?php echo $this->lang->line('receive'); ?></a></li>
                                    <?php }if ($this->rbac->hasPrivilege('postal_dispatch', 'can_view')) {?>
                                        <li><a href="<?php echo base_url(); ?>admin/dispatch"><?php echo $this->lang->line('dispatch'); ?></a></li>
                                    <?php }?>
                                </ul>
                            <?php }if ($this->rbac->hasPrivilege('complain', 'can_view')) {?>
                                <a  href="<?php echo base_url(); ?>admin/complaint" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('complain'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('appointed_patient_list'); ?></div>
                        <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('appointment')." ".$this->lang->line('details'); ?>" >
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('appointment') . " " . $this->lang->line('no'); ?></th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th width="10%"><?php echo $this->lang->line('phone'); ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('doctor'); ?></th>
                                    <th><?php echo $this->lang->line('source'); ?></th>
                                    <th><?php echo $this->lang->line('priority'); ?></th>
                  					<?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                                    <th><?php echo $this->lang->line('live') . " " . $this->lang->line('consultant'); ?></th>
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
    </section>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group15">
                                <div>
                                    <select onchange="get_PatientDetails(this.value)" style="width:100%" class="form-control select2" name='' id="add_patient_report_id" >
                                        <option value="" selected><?php echo $this->lang->line('select_patient'); ?></option>
                                        <?php foreach ($patients as $dkey => $dvalue) {
    ?>
                                        <option value="<?php echo $dvalue["id"]; ?>" <?php
if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
        echo "selected";
    }
    ?>><?php echo $dvalue["patient_name"] . " (" . $dvalue["id"] . ')' ?></option>
                                                                                                    <?php }?>
                                                                                        </select>
                                                                                    </div>
                                                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                                                </div>
                                                                            </div><!--./col-sm-8-->
                                                                        </div><!-- ./row -->
                                                                    </div>
                        <form id="formadd" accept-charset="utf-8" method="post" class="ptt10">
                            <div class="modal-body pt0 pb0">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                 <input type="hidden" name="patient_id" id="patientid" class="form-control">
                                    <div class="row">
                                        <div class="col-sm-3">
                                              <div class="form-group">
                                                  <label><?php echo $this->lang->line('date'); ?></label>
                                                  <small class="req"> *</small>
                                                  <input type="text" id="date" name="date" class="form-control datetime">
                                                   <span class="text-danger"><?php echo form_error('date'); ?></span>
                                              </div>
                                        </div>
                                       <div class="col-sm-3">
                                     <div class="form-group">
                             <label><?php echo $this->lang->line('patient_name'); ?></label>
                                     <small class="req">*</small>
                               <input type="text" name="patient_name" id="patient_name" class="form-control">
                                        <span class="text-danger"><?php echo form_error('patient_name'); ?></span>
                                               </div>
                                         </div>
                                             <div class="col-md-3">
                                         <div class="form-group">
                                       <label> <?php echo $this->lang->line('gender'); ?></label>
                                   <select class="form-control" style="width: 100%" id="gender" name="gender">
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                    <?php
                                        foreach ($genderList as $key => $value) {
                                            ?>
                                              <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) {
                                                echo "selected";
                                            }
                                            ?>><?php echo $value; ?></option>
                                                                                                    <?php
                                        }
                                        ?>
                                    </select>
                           <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                 </div>
                             </div>
                          <div class="col-sm-3">
                            <div class="form-group">
                         <label><?php echo $this->lang->line('email'); ?></label>
                          <input type="text" name="email" id="email"  class="form-control">
                                          </div>
                                     </div>
                               <div class="col-sm-3">
                               <div class="form-group">
                              <label><?php echo $this->lang->line('phone'); ?></label>
                            <small class="req">*</small>
                         <input type="text" name="mobileno" id="phone" class="form-control">
                         <span class="text-danger"><?php echo form_error('mobileno'); ?></span>
                              </div>
                                </div>
                              <div class="col-sm-3">
                                  <div class="form-group">
                                      <label><?php echo $this->lang->line('doctor'); ?></label>
                                      <small class="req"> *</small>
                                      <div>
                                          <select class="form-control select2" <?php
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
                                          <input type="hidden" name="doctorid" id="doctorinputid">
                                      </div>
                                      <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                                  </div>
                              </div>
                            <div class="col-sm-3">
                                 <div class="form-group">
                                 <label><?php echo $this->lang->line('appointment_priority'); ?></label>
                                    <div>
                                      <select class="form-control select2"  name='priority' style="width:100%" >
                                          <?php foreach ($appoint_priority_list as $dkey => $dvalue) { ?>
                                                <option value="<?php echo $dvalue["id"]; ?>"> <?php echo $dvalue["appoint_priority"]; ?></option>
                                        <?php }?>
                                        </select>
                                    </div><span class="text-danger"><?php echo form_error('doctor'); ?></span>
                                </div>
                            </div>
                          <div class="col-sm-3">
                            <div class="form-group">
                              <label for="appointment_status"><?php echo $this->lang->line('status'); ?></label>
                              <select name="appointment_status" class="form-control">
                              <?php foreach ($appointment_status as $appointment_status_key => $appointment_status_value) {
                                  ?><option value="<?php echo $appointment_status_key ?>"><?php echo $appointment_status_value ?>
                                    
                                  </option>
                              <?php }?>
                              </select>
                              </div>
                              <span class="text-danger"><?php echo form_error('appointment_status'); ?></span>
                        </div>
                          <div class="col-sm-8">
                            <div class="form-group">
                              <label for="message"><?php echo $this->lang->line('message'); ?></label>
                                <small class="req">*</small>
                              <textarea name="message" id="note" class="form-control" ></textarea>
                              <span class="text-danger"><?php echo form_error('message'); ?></span>
                            </div>
                          </div>
                           <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                            <div class="col-sm-4">
                                 <div class="form-group">
                                 <label><?php echo $this->lang->line('live') . " " . $this->lang->line('consultant') . " (" . $this->lang->line('on') . " " . $this->lang->line('video') . " " . $this->lang->line('conference') . ")"; ?></label>
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
                                    </div><span class="text-danger"><?php echo form_error('live_consult'); ?></span>
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
                        <div class="box-footer">
                            <div class="pull-right">
                            <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- dd -->
                                                        <div class="modal fade" id="myModaledit" role="dialog" aria-labelledby="myModalLabel">
                                                            <div class="modal-dialog modal-lg" role="document">
                                                                <div class="modal-content modal-media-content mx-2">
                                                                    <div class="modal-header modal-media-header">
                                                                        <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
                                                                        <div class="row">
                                                                            <div class="col-sm-6 col-xs-8">
                                                                                <div class="form-group15">
                                                                                            <div>
        <select onchange="get_ePatientDetails(this.value)" class="form-control select2"<?php
if ($disable_option == true) {
    echo "disabled";
}
?> style="width:100%" id="eaddpatient_id" name='' >
        <option value=""><?php echo $this->lang->line('select'); ?></option>
        <?php foreach ($patients as $dkey => $dvalue) {
    ?>
        <option value="<?php echo $dvalue["id"]; ?>" <?php
if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
        echo "selected";
    }
    ?>><?php echo $dvalue["patient_name"] . " (" . $dvalue["id"] . ")" ?>
        </option>
        <?php }?>
        </select>
        </div>
        <span class="text-danger"><?php echo form_error('refference'); ?></span>
        </div>
        </div><!--./col-sm-6 col-xs-8 -->
        </div><!--./row-->
        </div>
        <form id="formedit" accept-charset="utf-8"  method="post" class="ptt10">
        <div class="modal-body pt0 pb0">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="row">
                    <input type="hidden" name="id" id="id" value="<?php echo set_value('id'); ?>">
                    <div class="col-sm-3">
                        <div class="form-group">
                        <label><?php echo $this->lang->line('date') ?></label>
                        <small class="req"> *</small>
                        <input type="text" id="dates" name="date" class="form-control datetime" value="<?php echo set_value('dates'); ?>">
                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                        </div>
                    </div>
                <input type="hidden" name="patient_id" id="edit_patient_id" class="form-control" value="<?php echo set_value('patient_id'); ?>">
                <input type="hidden" name="appointment_no" id="edit_appointment_no" class="form-control" value="<?php echo set_value('patient_id'); ?>">
                <div class="col-sm-3">
                    <div class="form-group">
                    <label><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></label>
                    <small class="req"> *</small>
                    <input type="text" name="patient_name" id="edit_patient_name" class="form-control" value="<?php echo set_value('patient_name'); ?>">
                    <span class="text-danger"><?php echo form_error('patient_name'); ?></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                    <label> <?php echo $this->lang->line('gender'); ?></label>
                    <select class="form-control" id="edit_gender" name="gender">
                    <option value="<?php echo set_value('gender'); ?>"><?php echo $this->lang->line('select'); ?></option>
                    <?php
foreach ($genderList as $key => $value) {
    ?>
                    <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) {
        echo "selected";
    }
    ?>><?php echo $value; ?></option>
                        <?php
}
?>
                    </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                    <label><?php echo $this->lang->line('email'); ?></label>
                    <input type="text" name="email" id="edit_email" class="form-control" value="<?php echo set_value('email'); ?>">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                    <label><?php echo $this->lang->line('phone'); ?></label>
                    <small class="req">*</small>
                    <input type="text" name="mobileno" id="edit_phone" class="form-control" value="<?php echo set_value('mobileno'); ?>">
                    <span class="text-danger"><?php echo form_error('mobileno'); ?></span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                    <label>
                    <?php echo $this->lang->line('doctor'); ?></label>
                    <small class="req"> *</small>
                    <div>
                    <select class="form-control select2" name='doctor' style="width:100%" id="doctor" >
                    <option value="<?php echo set_value('doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                    <?php foreach ($doctors as $dkey => $dvalue) {
    ?>
                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] . " " . $dvalue["surname"]." (".$dvalue["employee_id"].")" ?></option>
                    <?php }?>
                    </select>
                    </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                    <label>
                    <?php echo $this->lang->line('appointment') . " " . $this->lang->line('priority'); ?></label>
                    
                        <div>
                            <select class="form-control select2" name='priority' style="width:100%" id="edit_appoint_priority" >
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
                                <label for="appointment_status"><?php echo $this->lang->line('status'); ?></label>
                                    <select name="appointment_status" class="form-control" id="appointment_status">
                                    <?php foreach ($appointment_status as $appointment_status_key => $appointment_status_value) {
    ?>
                                    <option value="<?php echo $appointment_status_key ?>"><?php echo $appointment_status_value ?></option>
                                    <?php } ?>
                                    </select>
                            </div>
                        </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label for="message"><?php echo $this->lang->line('message'); ?></label>
                            <small class="req"> *</small>
                            <textarea name="message" id="message" class="form-control"><?php echo set_value('message'); ?></textarea>
                            <span class="text-danger"><?php echo form_error('message'); ?></span>
                        </div>
                    </div>                        
                          <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('live') . " " . $this->lang->line('consultant') . " (" . $this->lang->line('on') . " " . $this->lang->line('video') . " " . $this->lang->line('conference') . ")"; ?></label>
                                      <select name="live_consult" id="edit_liveconsult" class="form-control">
                                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                        ?>
                                        <option value="<?php echo $yesno_key ?>" <?php 
                                        if ($yesno_key == 'no') {
                                            echo "selected";
                                        } ?> ><?php echo $yesno_value ?>
                                            
                                        </option>
                                    <?php } ?>
                                </select>  
                                   
                                </div>
                            </div>
                          <?php } ?>                          
                          <div id="customfield" ></div>                     
                      </div><!--./row-->
                      </div><!--./col-md-12-->
                      </div><!--./row-->

                  </div><!--./modal-body-->
                  <div class="modal-footer">
                  <div class="pull-right">
                  <button type="submit" id="formeditbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info" ><?php echo $this->lang->line('save'); ?></button>
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
                        <button type="button" class="close" data-toggle="tooltip" data-original-title="Close" data-dismiss="modal">&times;</button>
                        <div class="modalicon">
                        <div id="edit_delete"><a href="#" data-target="#editModal" data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-pencil"></i></a><a href="#" data-toggle="tooltip" onclick="delete_recordById('<?php echo base_url(); ?>admin/appointment/delete/#', '<?php echo $this->lang->line('success_message') ?>')" data-original-title="Delete"><i class="fa fa-trash" ></i></a></div>
                        </div>
                        <h4 class="modal-title"><?php echo $this->lang->line('appointment') . " " . $this->lang->line('information'); ?></h4>
                        </div>
                        <div class="modal-body pt0 pb0">
                        <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="view" accept-charset="utf-8" method="get" class="pt5 pb5">
                        <div class="table-responsive">
                        <table class="table mb0 table-striped table-bordered examples">
                          <tr>
                              <th width="15%"><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></th>
                              <td width="35%"><span id='patient_names'></span></td>
                              <th width="15%"><?php echo $this->lang->line('appointment') . " " . $this->lang->line('no'); ?></th>
                              <td width="35%"><span id="appointmentno"></span>
                              </td>
                          </tr>
                            <tr>
                                <th width="15%"><?php echo $this->lang->line('date'); ?></th>
                                <td width="35%"><span id='dating'></span></td>
                                <th width="15%"><?php echo $this->lang->line('gender'); ?></th>
                                <td width="35%"><span id="genders"></span>
                                </td>
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
                        <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                            <tr>
                                <th width="15%"><?php echo $this->lang->line('live_consult'); ?></th>
                                <td width="35%"><span id="liveconsult"></span>
                                </td>
                                <th width="15%"><?php echo $this->lang->line('status'); ?></th>
                                <td width="35%"><span id='status' style="text-transform: capitalize;"></span></td>
                            </tr>
                        <?php } ?>
                            <tr>
                              <th width="15%"><?php echo $this->lang->line('appointment')." ".$this->lang->line('priority'); ?></th>
                              <td width="35%"><span id='appointpriority'></span></td>
                            </tr>
                            <tr id="field_data">
                                  <th><span id="vcustom_name"></span></th>
                                  <td><span id="vcustom_value"></span></td>
                              </tr>
                        </table>
                        </div>
                        </form>
                        </div><!--./col-md-12-->
                        </div><!--./row-->
                        </div>
                        </div>
                    </div>
                  </div>
                                                        </div>
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
                                                                $("#formadd").on('submit', (function (e) {
                                                                  var did = $("#doctorid").val();
                                                                  $("#doctorinputid").val(did);
                                                                    $("#formaddbtn").button('loading');
                                                                    e.preventDefault();
                                                                    $.ajax({
                                                                        url: '<?php echo base_url(); ?>admin/appointment/add',
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
                                                                            $("#formaddbtn").button('reset');
                                                                        },
                                                                        error: function () {

                                                                        }
                                                                    });
                                                                }));
                                                            });
                                                            $(document).ready(function (e) {
                                                                $("#formedit").on('submit', (function (e) {
                                                                    $("#formeditbtn").button('loading');
                                                                    e.preventDefault();
                                                                    $.ajax({
                                                                        url: '<?php echo base_url(); ?>admin/appointment/update',
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
                                                            });

                                                            function get_PatientDetails(id) {
                                                                $("#patient_name").html("patient_name");
                                                                $('#gender option').removeAttr('selected');
                                                                $.ajax({
                                                                    url: '<?php echo base_url(); ?>admin/patient/patientDetails',
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

                                                            function getRecord(id) {
                                                                $("#viewModal").modal('hide');
                                                                $('#myModaledit').modal({backdrop:'static'});
                                                                $.ajax({
                                                                    url: '<?php echo base_url(); ?>admin/appointment/getDetailsAppointment',
                                                                    type: "POST",
                                                                    data: {appointment_id: id},
                                                                    dataType: 'json',
                                                                    success: function (data) {                                                                       
                                                                       $('#customfield').html(data.custom_fields_value);
                                                                        $("#id").val(data.id);
                                                                        $("#dates").val(data.date);
                                                                        $("#edit_patient_id").val(data.patient_id);
                                                                        $("#edit_patient_name").val(data.patient_name);
                                                                        $("#edit_appointment_no").val(data.appointment_no);
                                                                        $("#edit_appoint_priority").val(data.priority); 
                                                                        $("#edit_gender").val(data.gender);
                                                                        $("#edit_email").val(data.email);
                                                                        $("#edit_phone").val(data.mobileno);
                                                                        $("#doctor").val(data.doctor);
                                                                        $(".select2").select2().select2('val', data.doctor);
                                                                        $("#message").val(data.message);
                                                                        $("#appointment_status").val(data.appointment_status);
                                                                        $("#eaddpatient_id").select2().select2('val', data.patient_id);
                                                                        $('select[id="edit_gender"] option[value="' + data.gender + '"]').attr("selected", "selected");
                                                                        $('select[id="doctor"] option[value="' + data.doctor + '"]').attr("selected", "selected");
                                                                        $('select[id="appointment_status"] option[value="' + data.appointment_status + '"]').attr("selected", "selected");
                                                                        $('select[id="edit_appoint_priority"] option[value="' + data.priority + '"]').attr("selected", "selected");
                                                                        $('select[id="edit_liveconsult"] option[value="' + data.live_consult + '"]').attr("selected", "selected");
                                                                    },
                                                                })
                                                            }

                                                            function get_ePatientDetails(id) {
                                                                $.ajax({
                                                                    url: '<?php echo base_url(); ?>admin/patient/patientDetails',
                                                                    type: "POST",
                                                                    data: {id: id},
                                                                    dataType: 'json',
                                                                    success: function (res) {
                                                                        console.log(res);

                                                                        if (res['id']>0) {
                                                                            $("#edit_patient_name").val(res.patient_name);
                                                                            $("#edit_patient_id").val(res.id);
                                                                        } else {

                                                                        }
                                                                    }
                                                                });
                                                            }
                                                            
                                                            function viewDetail(id) {
                                                                $('#viewModal').modal({backdrop: 'static'});
                                                                $.ajax({
                                                                    url: '<?php echo base_url(); ?>admin/appointment/getDetailsAppointment',
                                                                    type: "POST",
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
                                                                        table_html += "<th><span id='vcustom_name'>" + capitalizeFirstLetter(name) + "</span></th> <td><span id='vcustom_value'>" + field_value + "</span></td>";
                                                                    });
                                                                        $("#field_data").html(table_html);
                                                                        $("#dating").html(data.date);
                                                                        $("#patient_ids").html(data.patient_id);
                                                                        $("#appointmentno").html(data.appointment_no);
                                                                        $("#patient_names").html(data.patient_name);
                                                                        $("#genders").html(data.gender);
                                                                        $("#emails").html(data.email);
                                                                        $("#appointpriority").html(data.appoint_priority);
                                                                        $("#phones").html(data.mobileno);
                                                                        $("#doctors").html(data.name + " " + data.surname+" ("+data.employee_id+")");
                                                                        $("#messages").html(data.message);
                                                                        $("#liveconsult").html(data.live_consult);

                                                                        var label = "";
                                                                        if (data.appointment_status == "approved") {
                                                                            var label = "class='label label-success'";
                                                                        } else if (data.appointment_status == "pending") {
                                                                            var label = "class='label label-warning'";
                                                                        } else if (data.appointment_status == "cancel") {
                                                                            var label = "class='label label-danger'";
                                                                        }

                                                                        $("#status").html("<small " + label + " >" + data.appointment_status + "</small>");
                                                                        $("#edit_delete").html("<?php if ($this->rbac->hasPrivilege('appointment', 'can_edit')) {?><a href='#'' onclick='getRecord(" + id + ")' data-target='#editModal' data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php }if ($this->rbac->hasPrivilege('appointment', 'can_delete')) {?><a href='#' data-toggle='tooltip'  onclick='delete_record(" + id + ")' data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php }?>");
                                                                    },
                                                                });
                                                            }

                                                            function delete_record(id) {
                                                                if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
                                                                    $.ajax({
                                                                        url: '<?php echo base_url(); ?>admin/appointment/delete/' + id,
                                                                        type: "POST",
                                                                        data: {patient_id: id},
                                                                        dataType: 'json',
                                                                        success: function (res) {
                                                                            if (res.status == 'success') {
                                                                                successMsg(res.message);
                                                                                window.location.reload(true);
                                                                            }
                                                                        }
                                                                    })
                                                                }
                                                            }

                                                        </script>
<script type="text/javascript">
    function askconfirm() {
        if (confirm("<?php echo $this->lang->line('approve') . ' ' . $this->lang->line('appointment'); ?>") ) {
           return true;
        } else {
            return false;
        }
    }

	$(".addappointment").click(function(){ 
		$('#doctorid').val(null).trigger('change');
		$('#select2-add_patient_report_id-container').html("");  
		$('#formadd').trigger("reset");
	});
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/appointment/getappointmentdatatable');
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->