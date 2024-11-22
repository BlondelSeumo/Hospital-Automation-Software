<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row"> 
            <div class="col-md-12">
                 <div class="nav-tabs-custom border0">
                    <div class="box box-primary"> 
                        <ul class="nav nav-tabs navlistscroll">
                            <li class="active"><a href="#tab_2" data-toggle="tab"><?php echo $this->lang->line('opd_view') ?></a></li>
                            <li><a href="#tab_1" data-toggle="tab"><?php echo $this->lang->line('patient_view') ?></a></li>
                        </ul>
                          <div class="tab-content">
                             <div class="tab-pane relative" id="tab_1">
                                <div class="topmius35">
                                        <?php if ($this->rbac->hasPrivilege('opd_patient', 'can_add')) { ?>                
                                            <a data-toggle="modal" id="add" onclick="holdModal('myModal')" class="btn btn-primary btn-sm addpatient"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_patient'); ?></a> 
                                        <?php } ?> 
                                    </div> 
                                <div class="box-body">
                                    <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('opd_view'); ?>">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('name') ?></th>
                                                <th><?php echo $this->lang->line('patient_id'); ?></th>
                                                <th><?php echo $this->lang->line('guardian_name') ?></th>
                                                <th><?php echo $this->lang->line('gender'); ?></th>
                                                <th><?php echo $this->lang->line('phone'); ?></th>
                                                <th><?php echo $this->lang->line('consultant'); ?></th>
                                                <th><?php echo $this->lang->line('last_visit'); ?></th> 
												<?php if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { ?>
                                                <th><?php echo $this->lang->line('is_antenatal'); ?></th> 
												<?php } ?>
                                                <th class="text-right"><?php echo $this->lang->line('total_recheckup'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                              </div>  
                               <div class="tab-pane active relative" id="tab_2">
                                    <div class="topmius35">
                                        <?php if ($this->rbac->hasPrivilege('opd_patient', 'can_add')) { ?>                
                                            <a data-toggle="modal" id="add" onclick="holdModal('myModal')" class="btn btn-primary btn-sm addpatient"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_patient'); ?></a> 
                                        <?php } ?> 
                                    </div>
                                   <div class="box-body table-responsive">                                   
                                   <table class="table table-striped table-bordered table-hover opd_ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('patient_view'); ?>">
                                        <thead>
                                            <th><?php echo $this->lang->line('opd_no'); ?></th>
                                            <th><?php echo $this->lang->line('patient_name'); ?></th>
                                            <th><?php echo $this->lang->line('case_id'); ?></th>
                                            <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                            <th><?php echo $this->lang->line('consultant'); ?></th>
                                            <th><?php echo $this->lang->line('reference'); ?></th>
                                            <th><?php echo $this->lang->line('symptoms'); ?></th>
											<?php if ($this->rbac->hasPrivilege('opd_antenatal', 'can_view')) { ?>
                                            <th><?php echo $this->lang->line('is_antenatal'); ?></th>
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
                                            <th class="text-right noExport white-space-nowrap"><?php echo $this->lang->line('action') ?></th>
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-9">
                        <div class="p-2 select2-full-width">
                            <select onchange="get_PatientDetails(this.value)" class="form-control patient_list_ajax" <?php
                                    if ($disable_option == true) {                                        
                                }
                                    ?> name='' id="addpatient_id">
                            </select>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>    
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-1"> 
                      <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <a data-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient"><i class="fa fa-plus"></i>  <span><?php echo $this->lang->line('new_patient'); ?></span></a> 
                            <?php } ?>   
                    </div>   
                </div>
            </div>             
            </div><!--./modal-header-->
        <form id="formadd" accept-charset="utf-8" action="<?php echo base_url() . "admin/patient" ?>" enctype="multipart/form-data" method="post">              
            <div class="pup-scroll-area">                    
                <div class="modal-body pt0 pb0">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">                            
                                <input name="patient_id" id="patient_id" type="hidden" class="form-control" />
                                <input name="email" id="pemail" type="hidden" class="form-control" />
                                <input name="mobileno" id="mobnumber" type="hidden" class="form-control" />
                                <input name="patient_name" id="patientname" type="hidden" class="form-control" />
                                <input name="password" id="password" type="hidden" class="form-control" />
                                <input name="organisation_id" id="organisation_id" type="hidden" class="form-control" />
                                <input name="insurance_validity" id="insurance_validity" type="hidden" class="form-control" />
                                <input name="insurance_id" id="insuranceid" type="hidden" class="form-control" />                                
                                <div class="row row-eq h-vh-lg-100-100">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <div id="ajax_load"></div>
                                        <div class="row ptt10" id="patientDetails" style="display:none">
                                            <div class="col-md-9 col-sm-9 col-xs-9">
                                                <ul class="singlelist">
                                                    <li class="singlelist24bold">
                                                        <span id="listname"></span></li>
                                                    <li>
                                                        <i class="fas fa-user-secret" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('guardian'); ?>"></i>
                                                        <span id="guardian"></span>
                                                    </li>
                                                </ul>   
                                                <ul class="multilinelist">   
                                                    <li>
                                                        <i class="fas fa-venus-mars" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('gender'); ?>"></i>
                                                        <span id="genders" ></span>
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-tint" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('blood_group'); ?>"></i>
                                                        <span id="blood_group"></span>
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-ring" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('marital_status'); ?>"></i>
                                                        <span id="marital_status"></span>
                                                    </li> 
                                                </ul>  
                                                <ul class="singlelist">  
                                                    <li>
                                                        <i class="fas fa-hourglass-half" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('age'); ?>"></i>
                                                        <span id="age"></span> <span id="as_of_date"></span>
                                                    </li>   

                                                    <li>
                                                        <i class="fa fa-phone-square" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('phone'); ?>"></i> 
                                                        <span id="listnumber"></span>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-envelope" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('email'); ?>"></i>
                                                        <span id="email"></span>
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-street-view" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('address'); ?>"></i>
                                                        <span id="address" ></span>
                                                    </li>
                                                    <li id="show_barcode">
                                                        <i class="fa fa-barcode" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('barcode'); ?>"></i>
                                                        <span><img src=""  id="getbarcode" width="100" height="35" />
                                                        </span>
                                                    </li>
                                                    <li id="show_qrcode">
                                                        <i class="fa fa-qrcode" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('qrcode'); ?>"></i>
                                                        <span>
                                                        <img src=""  id="getqrcode" width="60" height="60" />
                                                        </span>
                                                    </li>
                                                    <li>
                                                        <b><?php echo $this->lang->line('any_known_allergies') ?> </b> 
                                                        <span id="allergies" ></span>
                                                    </li>
                                                    <li>
                                                        <b><?php echo $this->lang->line('remarks') ?> </b> 
                                                        <span id="note"></span>
                                                    </li>  
                                                    <li>
                                                        <b><?php echo $this->lang->line("tpa"); ?> </b> 
                                                        <span id="organisation_name" ></span>
                                                    </li>
                                                    <li>
                                                        <b><?php echo $this->lang->line("tpa_id"); ?> </b> 
                                                        <span id="insurance_id" ></span>
                                                    </li>
                                                    <li>
                                                        <b><?php echo $this->lang->line("tpa_validity"); ?> </b> 
                                                        <span id="validity"></span>
                                                    </li>
                                                    <li>
                                                        <b><?php echo $this->lang->line("national_identification_number"); ?> </b> 
                                                        <span id="identification_number"></span>
                                                    </li>   
                                                </ul>                               
                                            </div><!-- ./col-md-9 -->
                                            <div class="col-md-3 col-sm-3 col-xs-3"> 
                                                <div class="pull-right">                                                 
                                                    <?php
                                                    $file = "uploads/patient_images/no_image.png";
                                                    ?>        
                                                    <img class="modal-profile-user-img img-responsive" src="<?php echo base_url() . $file.img_time() ?>" id="image" alt="User profile picture">
                                                </div>           
                                            </div><!-- ./col-md-3 --> 
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12"> 
                                                <div class="dividerhr"></div>
                                            </div><!--./col-md-12-->
                                            <div class="col-sm-3 col-xs-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('symptoms_type') ; ?></label>
                                                    <div><select  name='symptoms_type'  id="act"  class="form-control select2 act"  style="width:100%" multiple >
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($symptomsresulttype as $dkey => $dvalue) {
                                                                ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"] ;?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                                </div>
                                            </div>                                            
                                            <input name="rows[]" type="hidden" value="1">
                                                <div class="col-sm-3 col-xs-6">
                                                    <label for="exampleInputFile"> 
                                                        <?php echo $this->lang->line('symptoms_title'); ?></label>
                                                    <div id="dd" class="wrapper-dropdown-3">
                                                        <input class="form-control filterinput height-33" type="text">
                                                        <ul class="dropdown scroll150 section_ul">
                                                            <li><label class="checkbox"><?php echo $this->lang->line('select') ; ?></label></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                    <textarea class="form-control" id="esymptoms" name="symptoms" rows="3" ></textarea>
                                                </div> 
                                            </div>                                        
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                                    <textarea name="note" rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="email"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                    <textarea name="known_allergies"  rows="3" id="eknown_allergies" placeholder="" class="form-control"><?php echo set_value('address'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <?php
                                                        echo display_custom_fields('opd');
                                                        ?>
                                                    </div>
                                                </div>        
                                            </div>       
                                        </div><!--./row--> 
                                    </div><!--./col-md-8--> 
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-eq ptt10">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('appointment_date'); ?></label>
                                                    <small class="req"> *</small>
                                                    <input id="datetimepicker" name="appointment_date" type="text" class="form-control datetime" />
                                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('case'); ?></label>
                                                    <div><input class="form-control" type='text' name='case' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('casualty'); ?></label>
                                                    <div>
                                                    <select name="casualty" id="casualty" class="form-control">
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
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('old_patient'); ?></label>
                                                    <div>
                                                        <select name="old_patient" id="" class="form-control">
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
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile">
                                                    <?php echo $this->lang->line('reference'); ?></label>
                                                    <div><input class="form-control" type='text' name='refference' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('consultant_doctor'); ?></label><small class="req"> *</small>
                                                    <div><select name='consultant_doctor' id="consultant_doctor" class="form-control select2" <?php
                                                        if ($disable_option == true) {
                                                            echo "disabled";
                                                        }
                                                        ?> style="width:100%"  >
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($doctors as $dkey => $dvalue) {
                                                                ?>
                                                                <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                                        if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
                                                                            echo "selected";
                                                                        }
                                                                        ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"]." (".$dvalue["employee_id"].")" ?></option>   
                                                        <?php } ?>
                                                        </select>
                                                        <?php if ($disable_option == true) { ?>
                                                            <input type="hidden" name="consultant_doctor"  value="<?php echo $doctor_select ?>">
                                                        <?php } ?>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                             <div class="col-md-6">
                                            <div class="form-check pt5">
                                                <input class="form-check-input" type="checkbox" value="1" id="is_tpa" name="is_tpa">
                                                <label class="form-check-label " for="is_tpa">
                                                    <?php echo $this->lang->line('apply_tpa'); ?>
                                                </label>
                                            </div> 
                                            </div>
											<div class="col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('charge_category'); ?></label>
												<select name="charge_category" style="width: 100%" class="form-control charge_category select2">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($charge_category as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $value['id']; ?>">
                                                    <?php echo $value['name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                                    <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                                </div>
                                            </div>                                           
                                            <div class="col-md-6">
                                                <div class="form-group">
                                            <label><?php echo $this->lang->line('charge'); ?></label><small class="req"> *</small>
                                            <select name="charge_id" style="width: 100%" class="form-control charge select2">
                                    <option value=""><?php echo $this->lang->line('select')?></option>
                                            </select>
                                             <input type="hidden" class="form-control right-border-none" name="org_charge_amount" id="org_charge_amount" readonly autocomplete="off">
                                                    <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                                    <input type="text" readonly name="standard_charge" id="standard_charge" class="form-control" value="<?php echo set_value('standard_charge'); ?>"> 

                                                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                                </div>
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('applied_charge') . " (" . $currency_symbol . ")" ?></label><small class="req"> *</small><input type="text" name="amount" id="apply_charge" class="form-control apply_charge">    
                                                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                            <div class="form-group"> 
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('discount'); ?></label>
                                                    <div class="input-group">                                                        
                                                        <input type="text" class="form-control discount_percentage" name="discount_percentage" id="discount_percentage" value='0' autocomplete="off">
                                                        <span class="input-group-addon "> %</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                            <div class="form-group"> 
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('tax'); ?></label>
                                                    <div class="input-group">                                                        
                                                        <input type="text" class="form-control right-border-none" name="percentage" id="percentage" readonly autocomplete="off">
                                                        <span class="input-group-addon "> %</span>
                                                    </div>
                                                </div>
                                            </div>
											<div class="col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('amount'). " (" . $currency_symbol . ")" ?></label><small class="req"> *</small><input type="text" name="apply_amount" readonly id="apply_amount" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                    <select name="payment_mode" class="form-control payment_mode">
                                                        <?php foreach ($payment_mode as $payment_key => $payment_value) {
                                                            ?>
                                                            <option value="<?php echo $payment_key ?>" <?php
                                                                    if ($payment_key == 'cash') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $payment_value ?></option>
                                                            <?php } ?>
                                                    </select>
                                                </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")" ; ?></label><small class="req"> *</small> 
                                                <input type="text" name="paid_amount" id="paid_amount" class="form-control paid_amount">    
                                                <span class="text-danger"><?php echo form_error('paid_amount'); ?></span>
                                            </div>
                                        </div>
                                        <div class="cheque_div" style="display: none;">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small> 
                                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small> 
                                                    <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                <input type="file" class="filestyle form-control"   name="document">
                                                <span class="text-danger"><?php echo form_error('document'); ?></span> 
                                            </div>
                                    </div>
                                    </div>
                                        <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>
                                                    <?php echo $this->lang->line('live_consultation'); ?></label>
                                                    <select name="live_consult"  class="form-control">
                                                        <?php
                                                         foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                            ?>
                                                            <option value="<?php echo $yesno_key ?>" <?php
                                                                    if ($yesno_key == 'no') {
                                                                        echo "selected";
                                                                    }
                                                                    ?> ><?php echo $yesno_value; ?>
                                                            </option>
                                                            <?php } ?>
                                                    </select>                                        
                                                   
                                                    <span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                                                </div>
                                            </div> 
                                        <?php  } ?>

                                            <div class="col-sm-6" id="antenatal_div" style="display:none">
                                                <div class="form-group pt-lg-28">
                                                    <label>
                                                    <input type="checkbox" class="align-top" name="is_for_antenatal" id="is_for_antenatal" value="1" > Is For Antenatal</label>
                                                </div>
                                            </div>
                                            
                                        </div><!--./row-->    
                                    </div><!--./col-md-4-->
                                </div><!--./row-->        
                        </div><!--./col-md-12-->       
                    </div><!--./row--> 
                </div>
            </div>
                <div class="box-footer sticky-footer">
                    <div class="pull-right">
                    <button type="submit" id="formaddbtn" name="save" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle" ></i> <span><?php echo $this->lang->line('save'); ?></span></button>
                    </div>
                    <div class="pull-right" style="margin-right: 10px;">
                        <button type="submit"  data-loading-text="<?php echo $this->lang->line('processing') ?>" name="save_print" class="btn btn-info pull-right printsavebtn"><i class="fa fa-print" ></i> <?php echo $this->lang->line('save_print'); ?></button>
                    </div>
                </div>
            </form>                
        </div>
    </div>    
</div>

<div class="modal fade" id="myModaledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title">  <?php echo $this->lang->line('patient_details'); ?></h4> 
            </div>
              <form id="formedit" accept-charset="utf-8"  enctype="multipart/form-data" method="post"  class="ptt10">
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small> 
                                        <input id="patient_name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                        <input type="hidden" id="updateid" name="updateid">
                                        <input type="hidden" id="opdid" name="opdid">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('guardian_name'); ?></label>
                                        <input type="text" id="guardian_name" name="guardian_name" value="<?php echo set_value('guardian_name'); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('gender'); ?></label><small class="req"> *</small> 
                                        <select class="form-control" id="gender" name="gender">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($genderList as $key => $value) {
                                                ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
    <?php
}
?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('marital_status'); ?></label>
                                        <select name="marital_status" id="marital_status" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
<?php foreach ($marital_status as $mkey => $mvalue) {
    ?>
                                                <option value="<?php echo $mkey ?>"><?php echo $mvalue ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
<?php echo $this->lang->line('patient_photo'); ?></label>
                                        <div><input class="filestyle form-control" type='file' name='file' id="file" size='20' />
                                            <input type="hidden" name="patient_photo" id="patient_photo">
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>  
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('email'); ?></label>
                                        <input type="text" id="email" value="<?php echo set_value('email'); ?>" name="email" class="form-control">
                                    </div>
                                </div> 
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                        <input id="contact" autocomplete="off" name="contact" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contact'); ?>" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label> <?php echo $this->lang->line('blood_group'); ?></label><small class="req"> *</small> 
                                        <select class="form-control" id="blood_group" name="blood_group">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($bloodgroup as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value; ?>" <?php if (set_value('blood_group') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('age'); ?></label>
                                        <div style="clear: both;overflow: hidden;">
                                            <input type="text" placeholder="Age" id="age" name="age" value="<?php echo set_value('age'); ?>" class="form-control" style="width: 40%; float: left;">
                                            <input type="text" placeholder="Month" id="month" name="month" value="<?php echo set_value('month'); ?>" class="form-control" style="width: 56%;float: left; margin-left: 5px;">
                                        </div>
                                    </div>
                                </div>
                            </div><!--./row-->   
                            <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>                    
                    </div><!--./col-md-12-->       
                </div><!--./row--> 
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                </div>
            </div>
            </form>   
        </div>
    </div>    
</div>

<!-- Add Prescription -->
<div class="modal fade" id="add_prescription" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="prescription_title"></h4> 
            </div>
            <form id="form_prescription" class="modal-text-white" accept-charset="utf-8" enctype="multipart/form-data" method="post">
             <div class="pup-scroll-area">   
                <div class="modal-body pt0 pb0">
                </div> <!--./modal-body-->
            </div>     
                <div class="modal-footer sticky-footer">
                    <div class="pull-right">
                          <button type="submit" name="save_print" value="save_print" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?>
                        </button>
                        <button type="submit" name="save" value="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div> 
</div><!-- Add Prescription -->

<!-- -->
<div class="modal fade" id="prescriptionview" tabindex="-1" role="dialog" aria-labelledby="follow_up">   
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='edit_deleteprescription'>
                  
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('prescription'); ?></h4>
            </div>
            <div class="scroll-area">
            <div class="modal-body pt0 pb0" id="getdetails_prescription">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="prescriptionviewmanual" tabindex="-1" role="dialog" aria-labelledby="follow_up">   
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='edit_deleteprescriptionmanual'>
                 
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('prescription'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0" id="getdetails_prescriptionmanual">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" role="dialog">
    <div class="modal-dialog modal-dialog modal-lg" role="document">
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" data-toggle="tooltip" data-original-title="Close" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='edit_delete'>
                        <?php if ($this->rbac->hasPrivilege('revisit', 'can_edit')) { ?>
                            <a href="#"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" ><i class="fa fa-pencil"></i></a>
                            <?php
                        }
                        if ($this->rbac->hasPrivilege('revisit', 'can_delete')) {
                            ?>
                            <a href="#" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                        <?php } ?>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('visit_details'); ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
              
            </div> 
        </div>
    </div> 
</div>

<div class="modal fade" id="moveIPDModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100 modalfullmobile" role="document">
       <form  action="<?php echo site_url('admin/patient/moveopd') ?>" id="form_confirm-move" method="POST" accept-charset="utf-8">
            <div class="modal-content modal-media-content">
                <div class="modal-header modal-media-header">
                    <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('move_patient_to_ipd'); ?></h4> 
                </div>
                <div class="pup-scroll-area">
                    <div class="modal-body pt0 pb0">
                        <p><?php echo $this->lang->line('some_text_in_the_modal'); ?></p>
                    </div>
                </div>
                <div class="modal-footer sticky-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right btn-ok"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('move'); ?></button>
                </div>
            </div>
         </form>
    </div>    
</div>

<!--new edit modal-->
<div class="modal fade" id="editModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <form id="visitformedit" accept-charset="utf-8" enctype="multipart/form-data" method="post">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header modal-text-white">
                <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                 <h4 class="modal-title"> <?php echo $this->lang->line('edit_visit_details'); ?></h4>
            </div>          
            </div><!--./modal-header-->
            <div class="pup-scroll-area">                    
                <div class="modal-body pt0 pb0">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row row-eq h-vh-lg-100-100">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <div id="ajax_load"></div>
                                        <div class="row">
                                            <div class="col-md-12 ptt10"> 
                                            </div><!--./col-md-12-->
                                             <input type="hidden" name="visitid" id="visitid" class="form-control" />
                                             <input type="hidden" name="visit_transaction_id" id="visit_transaction_id" class="form-control" />
                                            <input type="hidden" name="type" id="type" value="opd" class="form-control" />                                         

                                            <div class="col-sm-4">
                                                 <div class="form-group">
                                                    <label for="exampleInputFile">
                                                        <?php echo $this->lang->line('symptoms_type'); ?></label>
                                                    <div><select name='symptoms_type[]'  id="act"  class="form-control select2 act"  multiple style="width:100%">
                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                            <?php foreach ($symptomsresulttype as $dkey => $dvalue) {
                                                                ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["symptoms_type"] ;?></option>

                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('symptoms_type'); ?></span>
                                                </div>
                                            </div>                                            
                                             <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="exampleInputFile"> 
                                                        <?php echo $this->lang->line('symptoms') ; ?></label>
                                                    <div id="dd" class="wrapper-dropdown-3">
                                                        <input class="form-control filterinput" type="text">
                                                        <ul class="dropdown scroll150 section_ul">
                                                            <li><label class="checkbox"><?php echo $this->lang->line('select'); ?></label></li>
                                                        </ul>
                                                    </div>
                                                </div>    
                                            </div>
                                            <div class="col-sm-4 col-xs-12">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('symptoms_description'); ?></label>
                                                    <textarea class="form-control" id="symptoms_description" name="symptoms"></textarea> 
                                                </div> 
                                            </div>                                        
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                                    <textarea rows="3" class="form-control" id="edit_revisit_note" name="revisit_note"></textarea>
                                                </div> 
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="email"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                    <textarea name="known_allergies" rows="3" id="editknown_allergies" placeholder="" class="form-control"><?php echo set_value('address'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="row">
                                             <div class="col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <div id="customfield"></div>
                                                </div>
                                            </div>
                                        </div>      
                                        </div><!--./row-->

                                    </div><!--./col-md-8--> 
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-eq ptt10">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('appointment_date'); ?></label>
                                                    <small class="req"> *</small>
                                                    <input name="appointment_date" class="form-control datetime" id="appointmentdate" placeholder="" type="text" class="form-control datetime" />
                                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label >
                                                    <?php echo $this->lang->line('case'); ?></label>
                                                    <div><input class="form-control" type='text' name="case" id="edit_case" />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>
                                                    <?php echo $this->lang->line('casualty'); ?></label>
                                                    <div>
                                                    <select name="casualty" id="edit_casualty" class="form-control">
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
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>
                                                    <?php echo $this->lang->line('old_patient'); ?></label>
                                                    <div>
                                                        <select name="old_patient" id="edit_oldpatient" class="form-control">
                                                            <?php foreach ($yesno_condition as $yesno_key => $yesno_value) { ?>
                                                                <option value="<?php echo $yesno_key ?>"  ><?php echo $yesno_value ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select> 
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>                                           
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label >
                                                    <?php echo $this->lang->line('reference'); ?></label>
                                                    <div><input type="text" name="refference" class="form-control" id="edit_refference" />  
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                             <input type="hidden" name="opdid" id="edit_opdid">

                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                   <label><?php echo $this->lang->line('consultant_doctor'); ?></label><small class="req"> *</small> 
                                                        <select  onchange="" name="consultant_doctor" <?php
                                                            if ($disable_option == true) {
                                                                echo "disabled";
                                                            }
                                                            ?> style="width:100%" class="form-control select2" id="edit_consdoctor">
                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>

                                                            <?php foreach ($doctors as $dkey => $dvvalue) { ?>
                                                                <option value="<?php echo $dvvalue["id"] ?>"><?php echo composeStaffNameByString($dvvalue["name"] , $dvvalue["surname"],$dvvalue["employee_id"]); ?></option>
                                                            <?php } ?>
                                                        </select>   
                                                        <?php if ($disable_option == true) { ?>
                                                            <input type="hidden" name="consultant_doctor"  value="<?php echo $doctor_select ?>">
                                                        <?php } ?>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div> 
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                   <label><?php echo $this->lang->line('payment_date'); ?></label><small class="req"> *</small>
                                        
                                            <input type="text" name="payment_date" id="edit_visit_payment_date" class="form-control datetime" autocomplete="off">
                                             <input type="hidden" class="form-control" id="edit_visit_payment_id" name="edit_payment_id" >
                                            <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('amount'). " (" . $currency_symbol . ")" ?></label><small class="req"> *</small> <input type="text" name="amount" id="edit_visit_payment" class="form-control" value="">   
                                                    
                                                </div>
                                                </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('payment_mode'); ?></label>
                                                        <select class="form-control visit_payment_mode" name="payment_mode" id="visit_payment_mode">

                                                            <?php foreach ($payment_mode as $key => $value) {
                                                                ?>
                                                                <option value="<?php echo $key ?>" <?php
                                                                if ($key == 'cash') {
                                                                    echo "selected";
                                                                }
                                                                ?>><?php echo $value ?></option>
                                                            <?php } ?>
                                                    </select>    
                                                </div>
                                           </div>                                      
                                        <div class="cheque_div" style="display: none;">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small> 
                                                    <input type="text" name="cheque_no" id="edit_visit_cheque_no" class="form-control">
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small> 
                                                    <input type="text" name="cheque_date" id="edit_visit_cheque_date" class="form-control date">
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                    <input type="file" class="filestyle form-control"   name="document">
                                                    <span class="text-danger"><?php echo form_error('document'); ?></span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('payment_note'); ?></label> 
                                                <input type="text" name="note" id="edit_payment_note" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="align-top"><?php echo $this->lang->line('is_antenatal'); ?></label> 
                                                <input type="checkbox" name="is_for_antenatal" value="1" id="is_antenatal">
                                            </div>
                                        </div>
                                        </div><!--./row-->    
                                    </div><!--./col-md-4-->
                                </div><!--./row-->        
                        </div><!--./col-md-12-->       
                    </div><!--./row--> 
                </div>
            </div>
                <div class="modal-footer sticky-footer">
                    <div class="pull-right">
                    <button type="submit" id="formeditbtn" name="save" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle" ></i> <span><?php echo $this->lang->line('save'); ?></span></button>
                    </div>
                </div>
            </form>                
        </div>
    </div>    
</div>

<!-- end new added modal-->
<!-- -->
<script type="text/javascript">
     var prescription_rows=2;
     $(document).on('change','.visit_payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){     
       
       $(".date").trigger("change");
        $('.cheque_div').css("display", "block");
       
      }else{

        $('.cheque_div').css("display", "none");
      }
    });
</script>

<script>
    $(document).on('change', '.act', function () {
        $this = $(this);
        var sys_val = $(this).val();
        var section_ul = $(this).closest('div.row').find('ul.section_ul');
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getPartialsymptoms',
            data: {'sys_id': sys_val},  
            dataType: 'JSON',
            beforeSend: function () {
                $('ul.section_ul').find('li:not(:first-child)').remove();
            },
            success: function (data) {
                section_ul.append(data.record);
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function () {
            }
        });
    });
</script>

<script type="text/javascript">

    $(document).on('click', '.remove_row', function () {
        $this = $(this);
        $this.closest('.row').remove();
    });

    $(document).mouseup(function (e)
    {
        var container = $(".wrapper-dropdown-3"); // YOUR CONTAINER SELECTOR
        if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            $("div.wrapper-dropdown-3").removeClass('active');
        }
    });

    $(document).on('click', '.filterinput', function () {
        if (!$(this).closest('.wrapper-dropdown-3').hasClass("active")) {
            $(".wrapper-dropdown-3").not($(this)).removeClass('active');
            $(this).closest("div.wrapper-dropdown-3").addClass('active');
        }
    });

    $(document).on('click', 'input[name="section[]"]', function () {
        $(this).closest('label').toggleClass('active_section');
    });

    $(document).on('keyup', '.filterinput', function () {
        var valThis = $(this).val().toLowerCase();
        var closer_section = $(this).closest('div').find('.section_ul > li');

        var noresult = 0;
        if (valThis == "") {
            closer_section.show();
            noresult = 1;
            $('.no-results-found').remove();
        } else {
            closer_section.each(function () {
                var text = $(this).text().toLowerCase();
                var match = text.indexOf(valThis);
                if (match >= 0) {
                    $(this).show();
                    noresult = 1;
                    $('.no-results-found').remove();
                } else {
                    $(this).hide();
                }
            });
        }
        ;
        if (noresult == 0) {
            closer_section.append('<li class="no-results-found"><?php echo $this->lang->line('no_result_found'); ?></li>');
        }
    });
</script>

<script type="text/javascript">   
    $('#myModal').on('hidden.bs.modal', function (e) {
        $(this).find('#formadd')[0].reset();
    });

    $('#myModalpa').on('hidden.bs.modal', function (e) {
        $(this).find('#formaddpa')[0].reset();
    });

    $(function () {
        $('#easySelectable').easySelectable();
        $('.select2').select2();    
    })

    function makeid(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function get_PatientDetails(id) { 

        var base_url = "<?php echo base_url(); ?>backend/images/loading.gif";
        $("#ajax_load").html("<center><img src='" + base_url + "'/>");
        var password = makeid(5)
       if(id==''){
            $("#ajax_load").html("");
             $("#patientDetails").hide();
       }else{    
           $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                
                if (res) {
                
                    $("#ajax_load").html("");
                    $("#patientDetails").show();
                    $('#patient_unique_id').html(res.id);
                    $('#patient_id').val(res.id);
                    $('#password').val(password);
                    $('#revisit_password').val(password);
                    $('#listname').html(res.patient_name+" ("+res.id+")");
                    $('#guardian').html(res.guardian_name);
                    $('#listnumber').html(res.mobileno);
                    $('#email').html(res.email);
                    $('#mobnumber').val(res.mobileno);
                    $('#pemail').val(res.email);
                    if(res.gender=='Female'){
                        $("#antenatal_div").css('display','block');
                    }
                    
                    $('#patientname').val(res.patient_name);                    
                    $('#age').html(res.patient_age);
                    $('#as_of_date').html(res.as_of_date);					
                    $('#doctname').val(res.name + " " + res.surname);
                    $("#bp").html(res.bp);
                    $("#symptoms").html(res.symptoms);
                    $("#known_allergies").html(res.known_allergies);
                    $("#insuranceid").val(res.insurance_id);
                    $("#insurance_id").html(res.insurance_id);
                    $("#insurance_validity").val(res.insurance_validity);
                    $("#organisation_id").val(res.organisation_id);
                    $("#organisation_name").html(res.organisation_name);
                    $("#validity").html(res.insurance_validity);
                    $("#identification_number").html(res.identification_number);
                    $("#address").html(res.address);
                    $("#note").html(res.note);
                    $("#height").html(res.height);
                    $("#weight").html(res.weight);
                    $("#genders").html(res.gender);
                    $("#marital_status").html(res.marital_status);                  
                    $("#blood_group").html(res.blood_group_name);
                    $("#allergies").html(res.known_allergies);
                    if(res.image !=null){
                        $("#image").attr("src", '<?php echo base_url() ?>' + res.image+ '<?php echo img_time(); ?>');
                    }else{
                        $("#image").attr("src", '<?php echo base_url() ?>uploads/patient_images/no_image.png');
                    }
                    if(res.getbarcode == null){
                        $("#show_barcode").hide();
                        $("#getbarcode").attr("src", '');
                    }else{
                        $("#show_barcode").show();
                        $("#getbarcode").attr("src", res.getbarcode);
                    }
                    if(res.getqrcode == null){
                        $("#show_qrcode").hide();
                        $("#getqrcode").attr("src", ''); 
                    }else{
                        $("#show_qrcode").show();
                        $("#getqrcode").attr("src", res.getqrcode);  
                    }    
                    
                } else {
                    $("#ajax_load").html("");
                    $("#patientDetails").hide();
                }
            }
        });
       }
    }
   
    function get_Charges() {
        var charge =$('.charge').val();
        $('#org_charge_amount').val('');
        var apply_amount=0;
        let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;

        if(is_tpa==1){
            var organisation_id=$("#organisation_id").val();
        }else if(is_tpa==0){
            var organisation_id=0;
        }

        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, organisation_id: organisation_id},
            dataType: 'json',
            success: function (res) {

                if (res) {                  
                    $('#percentage').val(res.percentage);
                    if (organisation_id) {
                        if(res.percentage ==null){                             
                            apply_amount=parseFloat(res.org_charge);  
                        }else{
                            apply_amount=(parseFloat(res.org_charge) * res.percentage/100)+(parseFloat(res.org_charge));
                        }
                       
                        $('#org_charge_amount').val(res.org_charge);
                        $('#apply_charge').val(res.org_charge);
                        $('#apply_amount').val(apply_amount);
                        $('#standard_charge').val(res.standard_charge);
                        $('#paid_amount').val(apply_amount);
                    } else {
                        if(res.percentage ==null){
                            apply_amount=parseFloat(res.standard_charge);
                        }else{
                            apply_amount=(parseFloat(res.standard_charge) * res.percentage/100)+(parseFloat(res.standard_charge));
                        }
                     
                        $('#standard_charge').val(res.standard_charge);
                        $('#apply_charge').val(res.standard_charge);
                        $('#apply_amount').val(apply_amount);
                        $('#paid_amount').val(apply_amount);
                    }
                } else {
                    $('#standard_charge').val('');
                    $('#apply_charge').val('');
                }
            }
        });
    }

    function get_Chargesrevisit(id) {
        $("#standard_chargerevisit").html("standard_charge");
        var orgid = $("#revisit_organisation").val();
        if (id == '') {
            id = $("#revisit_doctor").val();
        }

        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctCharge',
            type: "POST",
            data: {doctor: id, organisation: orgid},
            dataType: 'json',
            success: function (res) {
             
                if (res) {
                    if (orgid) {
                        $('#revisit_amount').val(res.org_charge);
                        $('#standard_chargerevisit').val(res.standard_charge);
                    } else {
                        $('#standard_chargerevisit').val(res.standard_charge);
                        $('#revisit_amount').val(res.standard_charge);
                    }
                 
                } else {
                    $('#standard_chargerevisit').val('');
                    $('#revisit_amount').val('');
                }
            }
        });
    }
   
	$(document).on('select2:select','.charge_category',function(){
       var charge_category=$(this).val();      
		$('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
        $('#percentage').val("");
        $('#apply_charge').val("");
        $('#standard_charge').val("");
        $('#schedule_charge').val("");                
        $('#org_id').val("");
        $('#org_charge_amount').val("");
        $('#apply_charge').val("");
        $('#apply_amount').val("");
        $('#paid_amount').val("");  
        $('#discount_percentage').val("") ;                     

		getchargecode(charge_category,"");

	});

    function getchargecode(charge_category,charge_id) {    
      var div_data = "<option value=''><?php echo $this->lang->line('select') ?></option>";
      if(charge_category != ""){
          $.ajax({
            url: base_url+'admin/charges/getchargeDetails',
            type: "POST",
            data: {charge_category: charge_category},
            dataType: 'json',
            success: function (res) {              
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";

                });
                $('.charge').html(div_data);
                $(".charge").select2("val", charge_id);             
            }
        });
      }
    }

    $(document).on('input paste keyup','.apply_charge,.discount_percentage', function(e){ 
		update_amount($(e.target).closest('div.modal'));
	});

	function update_amount(object_model){      
        
        let  apply_charge= object_model.find('.apply_charge').val();
        let  discount_percentage=object_model.find('#discount_percentage').val();
		let discount= (parseFloat(apply_charge) * discount_percentage/100);
		let price_with_discount=((parseFloat(apply_charge))-(parseFloat(apply_charge) * discount_percentage/100));
        let  tax_percentage=object_model.find('#percentage').val();        
            if(tax_percentage !='' && tax_percentage !=0){
                 apply_amount=(parseFloat(price_with_discount) * tax_percentage/100)+price_with_discount;      
                 object_model.find('#apply_amount').val(apply_amount);            
                 object_model.find('.paid_amount').val(apply_amount);            
            }
    }

    $(document).on('select2:select','.charge_category',function(){
		let closetst_div=$(this).closest('div.modal'); 
	});

    reset_form=(_modal_div)=>{
        _modal_div.find('.charge_category').val('').trigger('change.select2');
        _modal_div.find('.charge ').val('').empty().trigger('change');
        _modal_div.find('#percentage').val("");
        _modal_div.find('#apply_charge').val("");
        _modal_div.find('#standard_charge').val("");
        _modal_div.find('#schedule_charge').val("");                
        _modal_div.find('#org_id').val("");
        _modal_div.find('#org_charge_amount').val("");
        _modal_div.find('#apply_charge').val("");
        _modal_div.find('#apply_amount').val("");
        _modal_div.find('#paid_amount').val("");  
        _modal_div.find('#discount_percentage').val("") ;                          
	}

    $(document).on('change','#is_tpa,#addpatient_id',function(){
        let closetst_div=$(this).closest('div.modal');
        reset_form(closetst_div);
    });

    $(document).on('select2:select','.charge',function(){
        let closetst_div=$(this).closest('div.modal');
        var charge=$(this).val();
        var patient_id=$("#patient_id").val();
        var apply_amount=0;
        $('#org_charge_amount').val('');
         $('#discount_percentage').val('');   
        let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;

        if(charge==''){
            reset_form(closetst_div);
            return false;
        }
       $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, 'patient_id':patient_id,'is_tpa':is_tpa},
            dataType: 'json',
            beforeSend: function() {
                
          },
            success: function (res) {

                if(res.status == 0){
                        errorMsg(res.msg);
                    }else{
                        if(res.status == 2){
                            errorMsg(res.msg);
                        }
                    var tax=res.result.percentage;
                    var quantity=closetst_div.find('#qty').val();
                    console.log(quantity);
                    closetst_div.find('#percentage').val(tax);
                    closetst_div.find('#apply_charge').val(parseFloat(res.result.standard_charge) * quantity);
                    closetst_div.find('#standard_charge').val(res.result.standard_charge);
                    closetst_div.find('#schedule_charge').val(res.result.org_charge);                
                    closetst_div.find('#org_id').val(res.result.org_charge_id);

                    if(res.display_tpa_charge){
                        if(res.result.percentage ==null){
                            apply_amount=parseFloat(res.result.org_charge);
                        }else{
                            apply_amount=(parseFloat(res.result.org_charge) * res.result.percentage/100)+(parseFloat(res.result.org_charge));
                        }

                        closetst_div.find('#org_charge_amount').val(res.result.org_charge);
                        closetst_div.find('#apply_charge').val(res.result.org_charge);
                        closetst_div.find('#apply_amount').val(apply_amount.toFixed(2));
                        closetst_div.find('#paid_amount').val(apply_amount.toFixed(2));    
                    }else{
                        if(res.result.percentage ==null){
                            apply_amount=parseFloat(res.result.standard_charge);
                        }else{
                            apply_amount=(parseFloat(res.result.standard_charge) * res.result.percentage/100)+(parseFloat(res.result.standard_charge));
                        }
                        
                        closetst_div.find('#apply_charge').val(res.result.standard_charge);
                        closetst_div.find('#apply_amount').val(apply_amount.toFixed(2));
                        closetst_div.find('#paid_amount').val(apply_amount.toFixed(2));
                       
                    }
                } 
            }
        }); 

 });
</script>

<script type="text/javascript">    
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
      var form_valid=true;
             var amount_to_be_paid=parseInt($("form#formadd #apply_amount").val());
             var amount_paying= parseInt($("form#formadd #paid_amount").val());
             if(amount_to_be_paid < amount_paying){
                 errorMsg("Invalid Amount");
                return false;
             }

             var sub_btn_clicked = $("button[type=submit][clicked=true]");       
            
             var sub_btn_clicked_name=sub_btn_clicked.attr('name');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                 sub_btn_clicked.button('loading') ; 
                 },
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
                           printVisitBill(data.opd_id);
                        }  
                        table.ajax.reload( null, false );
                        $('#myModal').modal('hide');
                    }
                      sub_btn_clicked.button('reset') ; 

                },
                 error: function(xhr) { // if error occured
       alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
       sub_btn_clicked.button('reset')  ;
    },
    complete: function() {
        sub_btn_clicked.button('reset');  
    }
            }); 
        }));
    });    
    
    $(document).ready(function (e) {
        $(".printsavedata").on('click', (function (e) {            
            var form = $(this).parents('form').attr('id');
            var str = $("#" + form).serializeArray();
            var postData = new FormData();
            $.each(str, function (i, val) {
                postData.append(val.name, val.value);
            });           

            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_revisit',
                type: "POST",
                data: postData,
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
                        patientid = $("#pid").val();
                        printVisitBill(patientid, data.id);
                    }
                    $("#formrevisitbtn").button('reset');
                },
                error: function () {
                    
                }
            });            
        }));
    });

    function printVisitBill(opdid) {
    $.ajax({
                url: base_url+'admin/patient/printbill',
                type: "POST",
                data: {opd_id: opdid},
                dataType: 'json',
                   beforeSend: function() {
            
                   },
                success: function (data) {
                  popup(data.page);
                },

                 error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                 $this.button('reset');                   
          },
          complete: function() {
                $this.button('reset');         
          }
            });
    }

    $(document).ready(function (e) {
        $("#formedit").on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/update',
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
                },
                error: function () {
                   
                }
            });
        }));
    });

    /**/
    function getRecord(id) 
    {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getDetails',
            type: "POST",
            data: {recordid: id},
            dataType: 'json',
            success: function (data) {
                $("#patientid").val(data.id);
                $("#patient_name").val(data.patient_name);
                $("#contact").val(data.mobileno);
                $("#email").val(data.email);
                $("#age").val(data.age);
                $("#bp").val(data.bp);
                $("#month").val(data.month);
                $("#guardian_name").val(data.guardian_name);
                $("#appointment_date").val(data.appointment_date);
                $("#case").val(data.case_type);
                $("#symptoms").val(data.symptoms);
                $("#known_allergies").val(data.known_allergies);
                $("#refference").val(data.refference);
                $("#amount").val(data.amount);
                $("#tax").val(data.tax);
                $("#opdid").val(data.opdid);
                $("#address").val(data.address);
                $("#note").val(data.note);
                $("#height").val(data.height);
                $("#weight").val(data.weight);
                $("#updateid").val(id);
                $('select[id="blood_group"] option[value="' + data.blood_group + '"]').attr("selected", "selected");
                $('select[id="gender"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_status"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                $('select[id="consultant_doctor"] option[value="' + data.cons_doctor + '"]').attr("selected", "selected");
                $('select[id="payment_mode"] option[value="' + data.payment_mode + '"]').attr("selected", "selected");
                $('select[id="casualty"] option[value="' + data.casualty + '"]').attr("selected", "selected");
            },
        })
    }

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

</script>
<script type="text/javascript"> 

 $("#myModal").on('hidden.bs.modal', function (e) {
     $(".filestyle").next(".dropify-clear").trigger("click");
     $("#patientDetails").hide();
     $('.select2-selection__rendered').html("");
     $('.cheque_div').css("display", "none");
     $('#formadd').find('input:text, input:password, input:file, textarea').val('');
     $('#formadd').find('select option:selected').removeAttr('selected');
     $('#formadd').find('input:checkbox, input:radio').removeAttr('checked');
 });

$(".modalbtnpatient").click(function(){		
	$('#formaddpa').trigger("reset");
	$(".dropify-clear").trigger("click");
});

 $(document).on('change','.payment_mode',function(){
   var mode=$(this).val();
   if(mode == "Cheque"){
     $('.cheque_div').css("display", "block");
   }else{
     $('.cheque_div').css("display", "none");
   }
 });

</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/patient/getopddatatable',[],[],100);
        initDatatable('opd_ajaxlist','admin/patient/getvisitwiseopddatatable',[],[],100,
                        [                           
                             {"sWidth": "110px","bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-right'},                          
                          
                        ]);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
 <?php $this->load->view('admin/patient/patientaddmodal'); ?>

 <!-- //========opd tab js===== -->
 <script>
     $(document).on('click','.print_visit_bill',function(){
       
    var opd_id=$(this).data('opdId');   
           var $this = $(this);     
     $.ajax({
                url: base_url+'admin/patient/printbill',
                type: "POST",
                data: {opd_id: opd_id},
                dataType: 'json',
                   beforeSend: function() {
                  $this.button('loading');
                   },
                success: function (data) {
                  popup(data.page);
                },

                 error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                 $this.button('reset');                   
          },
          complete: function() {
                $this.button('reset');         
          }
            });
    });

     function getRecord_id(visitid) {        
        $('#prescription_title').html('<?php echo $this->lang->line('add_prescription'); ?>');
         $.ajax({
            url: base_url+'admin/prescription/addopdPrescription',
            dataType:'JSON',
            data:{'visit_detail_id':visitid},
            type:"POST",
             beforeSend: function() {
              },
               success: function (res) {
                $('.modal-body',"#add_prescription").html(res.page);
                $('.modal-body',"#add_prescription").find('table').find('.select2').select2();
                 $('.modal-body',"#add_prescription").find('.multiselect2').select2({   
    placeholder: 'Select',
    allowClear: false,
    minimumResultsForSearch: 2
});

                $('#add_prescription').modal('show');
             },

              complete: function() {
                  $("#compose-textareass,#compose-textareaneww").wysihtml5({
                        toolbar: {
                            "image": false,
                        }
                    });
             },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");              
         }                                                                                    
        });
    }

    function view_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/getPrescription/' + visitid ,
            success: function (res) {
                $("#getdetails_prescription").html(res);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        });            
       
        holdModal('prescriptionview');
    }

    function viewmanual_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/prescription/getPrescriptionmanual/' + visitid ,
            success: function (res) {
                $("#getdetails_prescriptionmanual").html(res);
                $('#edit_deleteprescriptionmanual').html("<?php if ($this->rbac->hasPrivilege('prescription', 'can_view')) { ?><a href='#'' data-toggle='tooltip' onclick='printprescriptionmanual(" + visitid + ")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php } ?>");
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        });
        holdModal('prescriptionviewmanual');
    }

     $(document).on('click','.move_opd',function(e){
            var data = $(this).data();
            var this_modal=$('#moveIPDModal');
            $('.title', this_modal).text(data.opdId);
            $('.btn-ok', this_modal).data('recordId', data.recordId);
        var btn= $(this);
             $.ajax({
                url: base_url+'admin/patient/moveIpdForm',
                type: "POST",
                data: {'visit_details_id':data.recordId},
                dataType: 'json',
               beforeSend: function () {
               btn.button('loading');

                },
                success: function (data) {
                    if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                    } else {
                   $('.modal-body',this_modal).html(data.page);
                   $('.modal-body',this_modal).find('.select2').select2();

                    }
                  btn.button('reset');

                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                     btn.button('reset');
                },
                complete: function () {
                     btn.button('reset');
                }
            });
            $('#moveIPDModal').modal({
                backdrop:"static",
            });
    });
    
$(document).on('click','.get_opd_detail',function(){
    var visitid=$(this).data('recordId');
    var opdid = $(this).data('opdId');
           var $this = $(this);
     $.ajax({
                url: base_url+'admin/patient/getopdDetails',
                type: "POST",
                data: {visit_id: visitid,opd_id:opdid},
                dataType: 'json',
                   beforeSend: function() {
                  $this.button('loading');
                   },
                success: function (data) {                   
                   
                  $('#viewModal').modal('show');
                  var patient_id = data.patient_id;
                    $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('visit', 'can_edit')) { ?><a href='#'' onclick='editRecord(" + visitid + ")' data-target='#editModal' data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('visit', 'can_delete')) { ?><a href='#' data-toggle='tooltip' data-patient_id="+patient_id+" data-record-id="+opdid+" class='delete_opd' data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
                            $('#viewModal .modal-body').html(data.page);
                },

                 error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                 $this.button('reset');
                   
          },
          complete: function() {
                $this.button('reset');         
          }
            });
    });
 </script>
 <script type="text/javascript">

    $(document).on('change', '.findinghead', function () {
        $this = $(this);
        var head_id = $(this).val();
        div_data="";
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getfinding',
            data: {'head_id': head_id},
            
            success: function (res) {              
                $("#finding_description").val(res);               
            },
            
        });
    });

    $(document).on('click','.delete_opd',function(){
     let patient_id=$(this).data('patient_id');
     let id=$(this).data('recordId');

      if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: baseurl+'admin/patient/deleteOPD',
                type: "POST",
                data: {opdid: id,'patient_id':patient_id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    if(data.total_remain <= 0){
                        window.location.href = baseurl+'admin/patient/search';
                    }else{

                    window.location.reload(true);
                    }
                }
            })
        }
    });

     function editRecord(visitid) {
      
        $.ajax({ 
            url: '<?php echo base_url(); ?>admin/patient/getopdvisitdetails',
            type: "GET",
            data: {visitid: visitid},
            dataType: 'json',
            success: function (data) {
             
                $('#visitid').val(visitid);
                $('#visit_transaction_id').val(data.transaction_id);
                $('#customfield').html(data.custom_fields_value);
                $("#patientid").val(data.patient_id);
                $("#patientname").val(data.patient_name);
                $("#appointmentdate").val(data.appointment_date);
                $("#edit_case").val(data.case_type);
                $("#symptoms_description").val(data.symptoms);
                $("#edit_casualty").val(data.casualty);                
                $("#edit_refference").val(data.refference);
                $("#edit_revisit_note").html(data.note);
                $("#edit_amount").val(data.apply_charge);
                $('select[id="edit_oldpatient"] option[value="' + data.patient_old + '"]').attr("selected", "selected");               
                $("#edit_opdid").val(data.opdid);
              
                if(data.is_antenatal==1){
                     $("#is_antenatal").prop('checked',true);
                }
                
                 $("#editknown_allergies").val(data.visit_known_allergies);
                 $("#edit_visit_payment_date").val(data.payment_date);
                 $("#edit_visit_payment").val(data.amount);
                 $("#visit_payment_mode").val(data.payment_mode).prop('selected');
                 $(".visit_payment_mode").trigger('change');
                 $("#edit_visit_cheque_no").val(data.cheque_no);
                 $("#edit_visit_cheque_date").val(data.cheque_date);
                 $("#edit_payment_note").val(data.payment_note);
                 $("#viewModal").modal('hide'); 
                $('select[id="edit_consdoctor"] option[value="'+data.cons_doctor+'"]').attr("selected","selected");
                $(".select2").select2().select2('val', data.cons_doctor);
                holdModal('editModal');
            },
        });
    }
 </script>
 <script>
    function edit_prescription(id) {
        $("#prescription_title").html('<?php echo $this->lang->line('edit_prescription'); ?>');
        $.ajax({
            url: base_url+'admin/prescription/editopdPrescription',
            dataType:'JSON',
            data:{'prescription_id':id} ,
            type:"POST",
             beforeSend: function() {
                  
              },
               success: function (res) {
                $('#prescriptionview').modal('hide');
                $('.modal-body',"#add_prescription").html(res.page);
                var medicineTable= $('.modal-body',"#add_prescription").find('table#tableID');
                medicineTable.find('.select2').select2();
                $('.modal-body',"#add_prescription").find('.multiselect2').select2({   
                    placeholder: 'Select',
                    allowClear: false,
                    minimumResultsForSearch: 2
                });
                prescription_rows=medicineTable.find('tr').length+1;

                medicineTable.find("tbody tr").each(function() {

                var medicine_category_obj = $(this).find("td select.medicine_category");
                var post_medicine_category_id = $(this).find("td input.post_medicine_category_id").val();
                var post_medicine_id = $(this).find("td input.post_medicine_id").val();
                var dosage_id = $(this).find("td input.post_dosage_id").val();
                var medicine_dosage=getDosages(post_medicine_category_id,dosage_id);

                $(this).find('.medicine_dosage').html(medicine_dosage);
                $(this).find('.medicine_dosage').select2().select2('val', dosage_id);
    
                getMedicine(medicine_category_obj,post_medicine_category_id,post_medicine_id);

                 });
                            $('#add_prescription').modal('show');
                         },

                          complete: function() {
                            $("#compose-textareas,#compose-textareanew").wysihtml5({
                                toolbar: {
                                    "image": false,
                                }
                            });                             
                         },
                         error: function(xhr) { // if error occured
                          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");                          
                     }                                                                                    
                    });
    }
 </script>
 <script>
    
    $(document).on('change', '.findingtype', function () {
        $this = $(this);

         var selected_id = $("#item_name").val();
        
        var section_ul = $(this).closest('div.row').find('ul.section_ul');
        var finding_id = $(this).val();        
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/findingbycategory',
            data: {'finding_id': finding_id,'selected_id':selected_id},
            dataType: 'JSON',
            
            beforeSend: function () {
                // setting a timeout
                $('ul.section_ul').find('li:not(:first-child)').remove();
            },
            success: function (data) {
                section_ul.append(data.record);

            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function () {

            }            
        });
    });

    $(document).on('change', '.findinghead', function () {

        $this = $(this);
        var head_id = $(this).val();       
        $.ajax({
            type: 'POST',
            url: base_url + 'admin/patient/getfinding',
            data: {'head_id': head_id},            
            success: function (res) {              
                $("#finding_description").val(res);               
            },            
        });
    });
</script>
<script>
    $(document).ready(function (e) {
        $("#visitformedit").on('submit', (function (e) {
            $("#formeditbtn").button("loading");
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/opd_detail_update',
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
                    $("#formeditbtn").button("reset");
                },
                error: function () {

                }
            });
        }));
    });
</script>
<script>
    $(document).ready(function (e) {
               $("form#form_prescription button[type=submit]").click(function() {            
         $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

        $("#form_prescription").on('submit', (function (e) {         
           let _this_form=$(this);
             var sub_btn_clicked = $("button[type=submit][clicked=true]",_this_form);   
            var sub_btn_clicked_name=sub_btn_clicked.attr('name');
        
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/add_opd_prescription',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                //cache: false,
                processData: false,
                  beforeSend: function() {
                 sub_btn_clicked.button('loading') ; 
                 },
                success: function (data) {
                    if (data.status == "0") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                       
                       if(sub_btn_clicked_name == "save_print") {  
                                                  
                            printprescription(data.visitid,true);
                        }
                        successMsg(data.message);
                        window.location.reload(true);
                        $('#add_prescription').modal('hide');
                        $('.ajaxlistvisit').DataTable().ajax.reload();
                    }
                      sub_btn_clicked.button('reset')  ;
                },
                 error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
                   sub_btn_clicked.button('reset')  ;
                },
                 complete: function() {
                     sub_btn_clicked.button('reset');  
                 }
            });
        }));
    });

    function printprescription(visitid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/prescription/printPrescription' ,
            type: 'GET',
            data: { visitid: visitid },
            dataType:"JSON",
            success: function (result) {
                popup(result.page);
            }
        });
    }   

    function printprescriptionmanual(visitid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/prescription/getPrescriptionmanual/' + visitid,
            type: 'POST',
            data: {payslipid: visitid, print: 'yes'},
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }
</script>

<script type="text/javascript">
   function getDosages(medicine_category_id){
    var dosage_opt="<option value=''><?php echo $this->lang->line('select') ?></option>";
   var sss='<?php echo json_encode($category_dosage); ?>';
   var aaa=JSON.parse(sss);
  
   if (aaa[medicine_category_id]){
    $.each(aaa[medicine_category_id], function(key, item) 
    {
      dosage_opt+="<option value='"+item.id+"'>"+item.dosage+" ("+item.unit+")</option>";
    });
}
return dosage_opt;
   }
</script> 

<script>
     $(document).on('select2:select','.medicine_category',function(){      
      getMedicine($(this),$(this).val(),0);
       selected_medicine_category_id =$(this).val();   
       var medicine_dosage=getDosages(selected_medicine_category_id);
       $(this).closest('tr').find('.medicine_dosage').html(medicine_dosage);
    }); 
	
    $(document).on('select2:select','.medicine_name',function(){   
                var row_id_val= $(this).data('rowid');
                $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_stockinfo",
            data: {'pharmacy_id': $(this).val()},
            dataType: 'json',
            success: function (res) {
                $('#stock_info_'+row_id_val).html(res);
            }
        });
    }); 

    function getMedicine(med_cat_obj,val,medicine_id){

      var medicine_colomn=med_cat_obj.closest('tr').find('.medicine_name');
        medicine_colomn.html("");    
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pharmacy/get_medicine_name',
            type: "POST",
            data: {medicine_category_id: val},
            dataType: 'json',
              beforeSend: function() {
              medicine_colomn.html("<option value=''><?php echo $this->lang->line('select') ?></option>");

            }, 
            success: function (res) {
                var div_data="<option value=''><?php echo $this->lang->line('select') ?></option>";
                $.each(res, function (i, obj)
                {
                    var sel = "";
                            if (medicine_id == obj.id) {
                                sel = "selected";
                            }
                            div_data += "<option value=" + obj.id + " " + sel + ">" + obj.medicine_name + "</option>";
                });
           
                medicine_colomn.html(div_data);
                medicine_colomn.select2("val", medicine_id);
               
            }
        });
}    

    function getMedicineDosage(id) {        
        var category_selected = $("#medicine_cat" + id).val();
        var arr = category_selected.split('-');
        var category_set = arr[0];       
        div_data = '';

        $("#search-dosage" + id).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $('#search-dosage' + id).select2("val", +id);

        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_dosage",
            data: {'medicine_category_id': category_selected},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.dosage + "'>" + obj.dosage +""+ obj.dosage +"</option>";
                });
                $("#search-dosage" + id).html("<option value=''><?php echo $this->lang->line('select') ?></option>");
                $('#search-dosage' + id).append(div_data); 
                $('#search-dosage' + id).select2("val", '');

            }
        });
    }    
</script>
<script>
 $(document).on('click','.add-record',function(){
       
         var rowCount = $('#tableID tr').length;
    
         var cat_row="" ;
         var medicine_row="";
         var dose_row="";
         var dose_interval_row="" ;
         var dose_duration_row="";
         var instruction_row="" ;
         var closebtn_row = "" ;
         
            if(rowCount==0){
               cat_row ="<label><?php echo $this->lang->line('medicine_category'); ?></label>";
              medicine_row ="<label><?php echo $this->lang->line('medicine'); ?></label>";
              dose_row =" <label><?php echo $this->lang->line("dose"); ?></label>";
              dose_interval_row =" <label><?php echo $this->lang->line("dose_interval"); ?></label>";
              dose_duration_row =" <label><?php echo $this->lang->line("dose_duration"); ?></label>";
              instruction_row =" <label><?php echo $this->lang->line("instruction"); ?></label>";
              closebtn_row =" <label>&nbsp;</label>";
            }
    
        var div = "<input type='hidden' name='rows[]' value='"+prescription_rows+"' autocomplete='off'><div id=row1><div class='col-lg-2 col-md-4 col-sm-6 col-xs-6'><div col-sm-2 col-xs-6 '>"+cat_row+" <select class='form-control select2 medicine_category'  name='medicine_cat_"+prescription_rows+"'  id='medicine_cat" + prescription_rows + "'><option value='<?php echo set_value('medicine_category_id'); ?>'><?php echo $this->lang->line('select'); ?></option><?php foreach ($medicineCategory as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["medicine_category"] ?></option><?php } ?></select></div></div><div class='col-lg-2 col-md-4 col-sm-6 col-xs-6'><div>"+medicine_row+" <select class='form-control select2 medicine_name' data-rowId='"+prescription_rows+"'  name='medicine_"+prescription_rows+"' id='search-query" + prescription_rows + "'><option value='l'><?php echo $this->lang->line('select') ?></option></select><small id='stock_info_"+prescription_rows+"''> </small></div></div><div class='col-lg-2 col-md-4 col-sm-6 col-xs-6'><div>"+dose_row+"<select  class='form-control select2 medicine_dosage' name='dosage_"+prescription_rows+"' id='search-dosage" + prescription_rows + "'><option value='l'><?php echo $this->lang->line('select'); ?></option></select></div></div><div class='col-lg-2 col-md-4 col-sm-6 col-xs-6'><div>"+dose_interval_row+"<select  class='form-control select2 interval_dosage' name='interval_dosage_"+prescription_rows+"' id='search-interval-dosage" + prescription_rows + "'><option value='<?php echo set_value('interval_dosage_id'); ?>'><?php echo $this->lang->line('select'); ?></option><?php foreach ($intervaldosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["name"] ?></option><?php } ?></select></div></div><div class='col-lg-2 col-md-4 col-sm-6 col-xs-6'><div> "+dose_duration_row+"<select class='form-control select2 duration_dosage' name='duration_dosage_"+prescription_rows+"' id='search-duration-dosage" + prescription_rows + "'><option value='<?php echo set_value('duration_dosage_id'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($durationdosage as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["name"] ?></option><?php } ?></select></div></div><div class='col-lg-2 col-md-4 col-sm-6 col-xs-6'><div>"+instruction_row+"<textarea style='height:28px' name='instruction_"+prescription_rows+"' class=form-control id=description></textarea></div></div></div>";

       var row = "<tr id='row" + prescription_rows + "'><td>" + div + "</td><td>"+closebtn_row+"<button type='button' onclick='delete_row("+prescription_rows+")' data-row-id='"+prescription_rows+"' class='closebtn delete_row'><i class='fa fa-remove'></i></button></td></tr>";
      $('#tableID').append(row).find('.select2').select2();
        prescription_rows++; 
    }); 

    function delete_row(id) {        
        var table = document.getElementById("tableID");
        var rowCount = table.rows.length;        
        var result = confirm("<?php echo $this->lang->line('delete_confirm')?>");         
            if (result == true) {                 
                 $("#row" + id).html("");
            }        
    }
    
    function getBed(bed_group, bed = '') {
        var div_data = "";
        $('#bed_no').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
      
        $.ajax({
            url: '<?php echo base_url(); ?>admin/setup/bed/getbedbybedgroup',
            type: "POST",
            data: {bed_group: bed_group, active: 'yes'},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    if ((bed != '') && (bed == obj.id)) {
                        sel = "selected";
                    }
                    div_data += "<option value=" + obj.id + " " + sel + ">" + obj.name + "</option>";
                });
                $("#bed_no").html("<option value=''>Select</option>");
                $('#bed_no').append(div_data);
                $("#bed_no").select2().select2('val', bed);
            }
        });
    }
    
    $("form#form_confirm-move").on('submit', (function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            if(confirm('<?php echo $this->lang->line('are_you_sure_want_to_move_patient'); ?>')) {
            var btn = $(this).find("button[type=submit]:focus" );
            var move_opd_id=btn.data('recordId');
            var form = $(this);
            var url = form.attr('action');
            $.ajax({
                url: url,
                type: "POST",
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function () {
                btn.button('loading');
                },
                success: function (data) {
                 
                    var move_id = data.move_id ;
                    if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                    } else {
                       $('.ajaxlistvisit').DataTable().ajax.reload();
                 window.location.assign("<?php echo base_url(); ?>admin/patient/ipdprofile/"+move_id); 
                    }
                  btn.button('reset');

                },
                error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                     btn.button('reset');
                },
                complete: function () {
                     btn.button('reset');
                }
            });
            }
        }));
</script>

 <!-- //========opd tab js===== -->