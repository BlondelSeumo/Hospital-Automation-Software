<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('ambulance_call_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('ambulance_call', 'can_add')) { ?>
                                <a data-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm ambulancecall"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_ambulance_call'); ?></a>
                            <?php } ?>
                            <?php if ($this->rbac->hasPrivilege('ambulance', 'can_view')) { ?>
                                <a href="<?php echo base_url(); ?>admin/vehicle/search" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('ambulance_list'); ?></a>
                            <?php } ?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('ambulance_call_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('ambulance_call_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <th><?php echo $this->lang->line('vehicle_number'); ?></th>
                                        <th><?php echo $this->lang->line('vehicle_model'); ?></th>
                                        <th><?php echo $this->lang->line('driver_name'); ?></th>
                                        <th><?php echo $this->lang->line('driver_contact_no'); ?></th>
                                        <th><?php echo $this->lang->line('patient_address'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <?php if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                        ?>
                                                <th><?php echo ucfirst($fields_value->name); ?></th>
                                        <?php }
                                        } ?>
                                        <th class="text-right"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?>
                                        <th class="text-right"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?>
                                        <th class="text-right"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?>
                                        <th class="text-right"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?>
                                        </th>
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
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pupclose close_ambulance_call" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-9">
                        <div class="p-2 select2-full-width">
                            <select form="formcall" class="form-control patient_list_ajax" name='patient_id' id="addpatient_id">
                            </select>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-1">
                        <div class="p-2">
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <a data-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient"><i class="fa fa-plus"></i> <span><?php echo $this->lang->line('new_patient'); ?></span></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <div class="p-2 pt-xs-2">
                            <div class="input-group">
                                <input type="text" class="form-control" form="formcall" id="case_reference_id" placeholder="<?php echo $this->lang->line('case_id'); ?>" name="case_reference_id">
                                <div class="input-group-btn">
                                    <button class="btn btn-default btn-group-custom" type="button" id="search_case_reference_id">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form id="formcall" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body pt0 pb0">
                        <div class="ptt10">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('vehicle_model'); ?></label><small class="req"> *</small>
                                        <select name="vehicle_no" id="vehicle_no" class="form-control" onchange="getVehicleDetail(this.value)">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($vehiclelist as $key => $vehicle) {  ?>
                                                <option value="<?php echo $vehicle["id"] ?>"><?php echo $vehicle["vehicle_model"] . " - " . $vehicle["vehicle_no"] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('vehicle_no'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('driver_name'); ?></label>
                                        <input name="driver" id="driver_search" type="text" class="form-control" readonly />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input name="date" type="text" class="form-control datetime" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('charge_category'); ?></label>
                                        <small class="req">*</small>
                                        <div>
                                            <select class="form-control select2 charge_category" name='charge_category_id' style="width: 100%">
                                                <option value="<?php echo set_value('charge_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($charge_category as $charge_cat_key => $charge_cat_value) {   ?>
                                                <option value="<?php echo $charge_cat_value["id"]; ?>"><?php echo $charge_cat_value["name"]; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('charge_category_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('charge_name'); ?></label>
                                        <small class="req">*</small>
                                        <div>
                                            <select class="form-control select2 charge" style="width: 100%" name='code' id="code">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('standard_charge'); ?></label><?php echo ' (' . $currency_symbol . ')'; ?>
                                        <small class="req">*</small>
                                        <div>
                                            <input class="form-control standard_charge" name='standard_charge' id="standard_charge">
                                        </div>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="clear">
                                    <?php echo display_custom_fields('ambulance_call'); ?>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('note'); ?></label>
                                                <textarea name="note" rows="3" id="note" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--./col-sm-6-->
                                <div class="col-sm-6">
                                    <table class="printablea4">
                                        <tr>
                                            <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                            <td width="60%" colspan="2" class="text-right ipdbilltable">
                                                <input type="text" placeholder="<?php echo $this->lang->line('total'); ?>" value="0" name="total" id="total" style="width: 30%; float: right" class="form-control total" readonly />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="40%"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                            <td class="text-right ipdbilltable">
                                                <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="Discount" name="discount_percent" id="discount_percent" value="0" class="form-control discount_percent" style="width: 70%; float: right;font-size: 12px;" />
                                            </td>
                                            <td class="text-right ipdbilltable">
                                                <input type="text" placeholder="Discount" value="0" name="discount" id="discount" style="width: 50%; float: right" class="form-control discount" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                            <td class="text-right ipdbilltable">
                                                <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="tax_percentage" id="tax_percentage" class="form-control tax_percentage" readonly style="width: 70%; float: right;font-size: 12px;" />
                                            </td>
                                            <td class="text-right ipdbilltable">
                                                <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="tax" value="0" id="tax" style="width: 50%; float: right" class="form-control tax" readonly />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                            <td colspan="2" class="text-right ipdbilltable">
                                                <input type="text" placeholder="<?php echo $this->lang->line('net_amount'); ?>" value="0" name="net_amount" id="net_amount" style="width: 30%; float: right" class="form-control net_amount" readonly />
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('payment_mode'); ?></label>
                                                <select class="form-control payment_mode" name="payment_mode">
                                                    <?php foreach ($payment_mode as $key => $value) { ?>
                                                        <option value="<?php echo $key ?>" <?php if ($key == 'cash') {
                                                                                                echo "selected";
                                                                                            } ?>><?php echo $value ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                                <input type="text" name="payment_amount" id="payment_amount" class="form-control payment_amount text-right">
                                                <span class="text-danger"><?php echo form_error('payment_amount'); ?></span>
                                            </div>
                                        </div>
                                        <div class="cheque_div" style="display:none;">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                    <input type="file" class="filestyle form-control" name="document">
                                                    <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer sticky-footer">
                    <button type="submit" name="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formcallbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    <div class="pull-right" style="margin-right:10px;">
                        <button type="submit" name="save_print" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-9">
                        <div class="pb-xs-5">
                            <select style="width: 100%" form="formedit" class="form-control patient_list_ajax" id="addpatientid" name='patient_id'>
                                <option value=""><?php echo $this->lang->line('select_patient'); ?></option>
                            </select>
                        </div>
                    </div><!--./col-sm-9-->
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <div class="input-group">
                            <input type="text" class="form-control border-0" form="formedit" id="case_reference_id_edit" placeholder="<?php echo $this->lang->line('case_id'); ?>" name="case_reference_id">
                            <div class="input-group-btn">
                                <button class="btn btn-default btn-group-custom" type="button" id="search_case_reference_id_edit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div><!--./row-->
            </div>
            <form id="formedit" method="post" accept-charset="utf-8">
                <div class="pup-scroll-area">
                    <div class="modal-body pt0 pb0">
                        <div class="ptt10">
                            <div class="row">
                                <input name="patient_name" id="patienteditid" type="hidden" class="form-control" value="<?php echo set_value('patient_name'); ?>" />
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('vehicle_number'); ?></label><small class="req"> *</small>
                                        <select name="vehicle_no" style="width: 100%" id="vehicleno" class="form-control" onchange="getVehicleDetail(this.value, 'vehicle_model', 'driver_name')">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($vehiclelist as $key => $vehicle) {
                                            ?>
                                                <option value="<?php echo $vehicle["id"] ?>"><?php echo $vehicle["vehicle_model"] . " - " . $vehicle["vehicle_no"] ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" id="ambulance_call_edit_id" name="id" />
                                        <span class="text-danger"><?php echo form_error('vehicle_model'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('driver_name'); ?></label>
                                        <input name="driver_name" id="driver_name" type="text" class="form-control" value="<?php echo set_value('vehicle_model'); ?>" readonly />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input name="date" id="edit_date" type="text" class="form-control datetime" value="<?php echo set_value('amount'); ?>" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('charge_category'); ?></label>
                                        <small class="req">*</small>
                                        <div>
                                            <select class="form-control select2 charge_category" name='charge_category_id' id="charge_category_id_edit" style="width: 100%">
                                                <option value="<?php echo set_value('charge_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($charge_category as $charge_cat_key => $charge_cat_value) {
                                                ?>
                                                    <option value="<?php echo $charge_cat_value["id"]; ?>"><?php echo $charge_cat_value["name"]; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('charge_category_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('charge_name'); ?></label>
                                        <small class="req">*</small>
                                        <div>
                                            <select class="form-control select2 charge_edit" style="width: 100%" name='code' id="code_edit">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile"><?php echo $this->lang->line('standard_charge'); ?></label><?php echo ' (' . $currency_symbol . ')'; ?>
                                        <small class="req">*</small>
                                        <div><input class="form-control standard_charge" name='standard_charge' id="standard_charge_edit"></div>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div id="customfield"></div>
                            </div>
                            <div class="divider"></div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('note'); ?></label>
                                                <textarea name="note" rows="3" id="edit_note" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--./col-sm-6-->
                                <div class="col-sm-6">
                                    <table class="printablea4">
                                        <tr>
                                            <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                            <td width="60%" colspan="2" class="text-right ipdbilltable">
                                                <input type="text" placeholder="Total" value="0" name="total" id="total_edit" style="width: 30%; float: right" class="form-control total" readonly />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="40%"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                            <td class="text-right ipdbilltable">
                                                <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="Discount" name="discount_percent_edit" id="discount_percent_edit" value="0" class="form-control discount_percent" style="width: 70%; float: right;font-size: 12px;" />
                                            </td>
                                            <td class="text-right ipdbilltable">
                                                <input type="text" placeholder="Discount" value="0" name="discount_edit" id="discount_edit" style="width: 50%; float: right" class="form-control discount" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                            <td class="text-right ipdbilltable">
                                                <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="Tax" name="tax_percentage" id="tax_percentage_edit" class="form-control tax_percentage" readonly style="width: 70%; float: right;font-size: 12px;" />
                                            </td>
                                            <td class="text-right ipdbilltable">
                                                <input type="text" placeholder="Tax" name="tax" value="0" id="tax_edit" style="width: 50%; float: right" class="form-control tax" readonly />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                            <td colspan="2" class="text-right ipdbilltable">
                                                <input type="text" placeholder="Net Amount" value="0" name="net_amount" id="net_amount_edit" style="width: 30%; float: right" class="form-control net_amount" readonly />
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('payment_mode'); ?></label>
                                                <select class="form-control payment_mode" name="payment_mode">
                                                    <?php foreach ($payment_mode as $key => $value) { ?>
                                                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?></label>
                                                <small class="req">*</small>
                                                <input type="text" name="payment_amount" id="payment_amount_edit" class="form-control payment_amount text-right">
                                                <span class="text-danger"><?php echo form_error('payment_amount'); ?></span>
                                            </div>
                                        </div>
                                        <div class="cheque_div" style="display:none;">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                                    <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                                    <span class="text-danger"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('attach_document'); ?></label>
                                                    <input type="file" class="filestyle form-control" name="document">
                                                    <span class="text-danger"><?php echo form_error('document'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer sticky-footer">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formeditbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    <div class="pull-right" style="margin-right:10px;">
                        <button type="submit" name="save_print" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right printsavebtn"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModalBill" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deletebill'>
                        <a href="#" data-target="#edit_prescription" data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('bill_details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb10">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addPaymentModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pupclose" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('payments'); ?></h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
    $(function() {
        $('#easySelectable').easySelectable();
        $('.select2').select2()
    })
</script>
<script type="text/javascript">
    (function($) {
        //selectable html elements
        $.fn.easySelectable = function(options) {
            var el = $(this);
            var options = $.extend({
                'item': 'li',
                'state': true,
                onSelecting: function(el) {

                },
                onSelected: function(el) {

                },
                onUnSelected: function(el) {

                }
            }, options);
            el.on('dragstart', function(event) {
                event.preventDefault();
            });
            el.off('mouseover');
            el.addClass('easySelectable');
            if (options.state) {
                el.find(options.item).addClass('es-selectable');
                el.on('mousedown', options.item, function(e) {
                    $(this).trigger('start_select');
                    var offset = $(this).offset();
                    var hasClass = $(this).hasClass('es-selected');
                    var prev_el = false;
                    el.on('mouseover', options.item, function(e) {
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
                $(document).on('mouseup', function() {
                    el.off('mouseover');
                });
            } else {
                el.off('mousedown');
            }
        };
    })(jQuery);
</script>
<script type="text/javascript">
    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/vehicle/getBillDetails/' + id,
            type: 'POST',
            data: {
                id: id,
                print: 'yes'
            },
            success: function(result) {
                popup(result);
            }
        });
    }     

    $("form#formcall button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $(document).ready(function(e) {
           modal_click_disabled('addPaymentModal');
        $("#formcall").on('submit', (function(e) {
            let submit_btn_clicked = $("button[type=submit][clicked=true]", $(this));
            let submit_btn_clicked_name = submit_btn_clicked.attr('name');
            console.log(submit_btn_clicked_name);
            e.preventDefault();
            $.ajax({
                url: base_url + 'admin/vehicle/addCallAmbulance',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    submit_btn_clicked.button('loading');
                },
                success: function(data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        table.ajax.reload();
                        $('#myModal').modal('hide');
                        if (submit_btn_clicked_name == "save_print") {
                            printData(data.id);
                        }
                    }
                    submit_btn_clicked.button('reset');
                },
                error: function(xhr) {
                    alert("Error occured.please try again");
                    submit_btn_clicked.button('reset');
                },
                complete: function() {
                    submit_btn_clicked.button('reset');
                }
            });
        }));
    });

    $("form#formedit button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $(document).ready(function(e) {
        $("#formedit").on('submit', (function(e) {
            e.preventDefault();
            let submit_btn_clicked = $("button[type=submit][clicked=true]", $(this));
            let submit_btn_clicked_name = submit_btn_clicked.attr('name');

            $.ajax({
                url: base_url + 'admin/vehicle/updatecallambulance',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    submit_btn_clicked.button('loading');
                },
                success: function(data) {

                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        table.ajax.reload();
                        $('#editModal').modal('hide');
                        if (submit_btn_clicked_name == "save_print") {
                            printData(data.id);
                        }
                    }
                    submit_btn_clicked.button('reset');
                },
                error: function(xhr) {
                    alert("Error occured.please try again");
                    submit_btn_clicked.button('reset');
                },
                complete: function() {
                    submit_btn_clicked.button('reset');
                }
            });
        }));
    });

    function viewDetailBill(id) {
        $.ajax({
            url: '<?php echo base_url() ?>admin/vehicle/getBillDetails/' + id,
            type: "GET",
            data: {
                id: id
            },
            success: function(data) {
                $('#reportdata').html(data);
                $('#edit_deletebill').html("<?php if ($this->rbac->hasPrivilege('ambulance_call', 'can_view')) { ?><a href='#' data-toggle='tooltip' onclick='printData(" + id + ")'  data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?><?php if ($this->rbac->hasPrivilege('ambulance_call', 'can_edit')) { ?><a href='#'' onclick='getRecord(" + id + ")' data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?><?php if ($this->rbac->hasPrivilege('ambulance_call', 'can_delete')) { ?><a onclick='delete_bill(" + id + ")'  href='#'  data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
                holdModal('viewModalBill');
            },
        });
    }

    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/vehicle/editCall',
            type: "GET",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                $('#vehicleno').val(data.vehicle_id);
                $('#vehicleno').trigger('change');
                $('#charge_category_id_edit').val(data.charge_category_id);
                $('#charge_category_id_edit').trigger('change');
                getchargecode(data.charge_category_id, data.charge_id);
                $("#ambulance_call_edit_id").val(data.id);
                $("#edit_note").val(data.note);
                $('#customfield').html(data.custom_fields_value);
                $("#driver_name").val(data.driver);
                $("#patienteditid").val(data.patient_id);
                $("#contact_no").val(data.contact_no);
                $("#edit_date").val(data.date);
                $("#address").val(data.address);				
                $("#tax_percentage_edit").val(data.tax_percentage);				
                $("#standard_charge_edit").val(data.standard_charge);
                $("#discount_percent_edit").val(data.discount_percentage);
                $("#discount_edit").val(data.discount);
                $("#net_amount_edit").val(data.net_amount);
                $("#total_edit").val(data.amount);
                $("#viewModalBill").modal('hide');
                $('#editModal').modal('show');
                $('#code_edit').val(data.charge_id);
                $("#case_reference_id_edit").val(data.case_reference_id);
                $("#payment_amount_edit").val(data.payment_amount);
                $(".payment_mode").val(data.payment_mode);
                $("#cheque_no", $('#formedit')).val(data.cheque_no);
                if (data.payment_mode == "Cheque") {
                    $("#cheque_date", $('#formedit')).datepicker({
                        format: date_format,
                        autoclose: true
                    }).datepicker("update", new Date(data.cheque_date));
                    $('.cheque_div').css("display", "block");
                }
                $('#code_edit').trigger('change');
				
                var tax_amount = ((data.standard_charge-data.discount) * data.tax_percentage) / 100;
				
				
                $("#tax_edit").val(tax_amount);
                get_PatienteditDetails(data.patient_id);
            },
        });
    }

    function getVehicleDetail(id, vh = 'vehicle_model_search', dr = 'driver_search') {
        $("#" + dr).val("");
        $("#" + vh).val("");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/vehicle/getVehicleDetail',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                $("#" + dr).val(data.driver_name);
                $("#" + vh).val(data.vehicle_model);
            },
        });
    }

    function get_PatienteditDetails(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    var option = new Option(res.patient_name + " (" + res.id + ")", res.id, true, true);
                    $("#editModal .patient_list_ajax").append(option).trigger('change');
                    // manually trigger the `select2:select` event
                    $("#editModal .patient_list_ajax").trigger({
                        type: 'select2:select',
                        params: {
                            data: res
                        }
                    });
                }
            }
        });
    }

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/vehicle/deleteCallAmbulance/' + id,
                success: function(res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function() {
                    alert("Fail")
                }
            });
        }
    }

    $(".ambulancecall").click(function() {

    });

    $('#myModal').on('hidden.bs.modal', function(e) {
        let modal_ = e.delegateTarget;
        $('#case_reference_id').val('');
        $("form#formcall", modal_).find('input:text, input:password, input:file, select, textarea').val('');
        $("form#formcall", modal_).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
        $(".charge_category", modal_).select2("val", "");
        $(".charge ", modal_).html('').select2({
            data: [{
                id: '',
                text: ''
            }]
        });
    });

    $(".modalbtnpatient").click(function() {
        $('#formaddpa').trigger("reset");
        $(".dropify-clear").trigger("click");
    });

    $(document).on('select2:select', '.charge_category', function() {
        var charge_category = $(this).val();
        $('.charge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
        getchargecode(charge_category, "");
    });

    function getchargecode(charge_category, charge_id) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        if (charge_category != "") {
            $.ajax({
                url: base_url + 'admin/charges/getchargeDetails',
                type: "POST",
                data: {
                    charge_category: charge_category
                },
                dataType: 'json',
                success: function(res) {
                    $.each(res, function(i, obj) {
                        var sel = "";
                        if (charge_id == obj.id) {
                            sel = "selected";
                        }

                        div_data += "<option value=" + obj.id + " " + sel + ">" + obj.name + "</option>";

                    });
                    $
                    $('.charge').html(div_data);
                    $(".charge").select2("val", charge_id);
                    $('.charge_edit').html(div_data);
                    $(".charge_edit").select2("val", charge_id);
                }
            });
        }
    }
</script>
<script>
    $(document).on('select2:select', '.charge', function(e) {
        var charge = $(this).val();
        var object_model = $(e.target).closest('div.modal');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {
                charge_id: charge
            },
            dataType: 'json',
            success: function(ambulance_data) {
                if (ambulance_data) {
                    object_model.find('#standard_charge').val((ambulance_data.result.standard_charge));

                    object_model.find('#amount').val(parseFloat((ambulance_data.result.standard_charge) * ambulance_data.result.percentage / 100) + (parseFloat(ambulance_data.result.standard_charge)).toFixed(2));
                    var tax_percentage = ambulance_data.result.percentage;
                    var total_amount = ambulance_data.result.standard_charge;
                    object_model.find('#tax_percentage').val(tax_percentage);
                    object_model.find('#total').val(total_amount);
                    var tax_amount = parseFloat((total_amount) * tax_percentage / 100)
                    object_model.find('#tax').val(tax_amount.toFixed(2));
                    var final_amount = parseFloat(total_amount) + parseFloat(tax_amount);
                    object_model.find('#net_amount,#payment_amount').val(final_amount.toFixed(2));
                } else {

                }
            }
        });
    });

    $(document).on('input paste keyup','.tax_percent,.discount_percent,#standard_charge', function(e){ 
        calculate_amount($(e.target).closest('div.modal'));
        
    });
	
    let calculate_amount=(object_model)=>{        
        let standard_charge=object_model.find('.standard_charge').val();
        let  tax_percentage = object_model.find('.tax_percentage').val();
        let  discount_percent = object_model.find('.discount_percent').val();
		
        
		
        let discount=isNaN((standard_charge * discount_percent / 100 ))? 0 : (standard_charge * discount_percent / 100 );
		
		let  tax_amount = ((standard_charge-discount) * tax_percentage / 100);
		
        object_model.find('.discount').val(discount.toFixed(2));        
        object_model.find('.total').val(parseFloat(standard_charge).toFixed(2));
		
        object_model.find('.tax').val(tax_amount.toFixed(2));
        var final_amount = (parseFloat(standard_charge)- parseFloat(discount)) + parseFloat(tax_amount);                    
        object_model.find('.net_amount,.payment_amount').val(final_amount.toFixed(2));        
    }

</script>
<script>
    $(document).on('select2:select', '.charge_edit', function() {
        var charge = $(this).val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {
                charge_id: charge
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    $('#amount_edit').val(parseFloat((res.result.standard_charge) * res.result.percentage / 100) + (parseFloat(res.standard_charge)));
                    var tax_percentage = res.result.percentage;
                    var total_amount = res.result.standard_charge;
                    $('#tax_percentage_edit').val(tax_percentage);
                    $('#total_edit').val(total_amount);
                    var tax_amount = parseFloat((total_amount) * parseFloat(tax_percentage) / 100)
                    $('#standard_charge_edit').val(res.result.standard_charge);
                    $('#tax_edit').val(tax_amount);
                    $('#net_amount_edit,#payment_amount_edit').val(parseFloat(total_amount) + parseFloat(tax_amount));
                } else {

                }
            }
        });
    });
</script>
<script>
        
    $(document).on('click', '.add_payment', function() {
        var record_id = $(this).data('recordId');
        var patient_id = $(this).data('patientId');
        var case_id = $(this).attr('data-case-id');
        var balance_amount = $(this).data('balanceAmount');
        var $add_btn = $(this);
        var payment_modal = $('#addPaymentModal');        
        $('.filestyle', '#addPaymentModal').dropify();
        payment_modal.modal('show');
        getPayments(record_id, patient_id, balance_amount, case_id);
    });

    function getPayments(record_id, patient_id = null, balance_amount = null, case_id = null) {
        var payment_modal = $('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/vehicle/getAmbulanceCallTransaction',
            type: "POST",
            data: {
                'id': record_id,
                'patient_id': patient_id,
                'balance_amount': balance_amount,
                'case_id': case_id
            },
            dataType: "JSON",
            beforeSend: function() {
                payment_modal.addClass('modal_loading');
            },
            success: function(data) {
                $('.modal-body', payment_modal).html(data.page);
                payment_modal.removeClass('modal_loading');
            },
            error: function() {
                payment_modal.removeClass('modal_loading');
            },
            complete: function() {
                payment_modal.removeClass('modal_loading');
            }
        });
    }

    $('.close_ambulance_call').click(function() {
        $('.cheque_div').css("display", "none");
        $("#addpatient_id").select2("val", "");
    })

    $(document).on('submit', '#add_partial_payment', function(e) {
        e.preventDefault();
        var clicked_btn = $("button[type=submit]");
        let billing_id = $("input[name='billing_id']", '#add_partial_payment').val();
        let case_id = $("input[name='case_id']", '#add_partial_payment').val();
        let patient_id = $("input[name='patient_id']", '#add_partial_payment').val();		
        let net_amount = $("input[name='net_amount']", '#add_partial_payment').val();
        let amount = $("input[name='payment_amount']", '#add_partial_payment').val();	
	
		let replace_amount =  parseInt(net_amount) - parseInt(amount); 	 
		
        var form = $(this);
        var btn = clicked_btn;
        btn.button('loading');
        $.ajax({
            url: form.attr('action'),
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function(index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
					initDatatable('ajaxlist', 'admin/vehicle/getambulancecallDatatable', [], [],100);
                    getPayments(billing_id,patient_id,replace_amount,case_id);					 
                }
                btn.button('reset');
            },
            error: function() {

            },
            complete: function() {
                btn.button('reset');
            }
        });
    });
 
    $(document).on('input', '#standard_charge_edit', function() {
        var standard_charge = $("#standard_charge_edit").val();
        $("#total_edit").val(standard_charge);
        var tax_percentage = $("#tax_percentage_edit").val();
        var tax_amount = standard_charge * tax_percentage / 100
        $("#tax_edit").val(tax_amount);
        var net_amount = parseInt(tax_amount) + parseInt(standard_charge);
        $("#net_amount_edit").val(net_amount);
        $("#payment_amount_edit").val(net_amount);
    });

    $(document).on('click', '.print_receipt', function() {
        var $this = $(this);
        var record_id = $this.data('recordId')
        $this.button('loading');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/vehicle/printTransaction',
            type: "POST",
            data: {
                'id': record_id
            },
            dataType: 'json',
            beforeSend: function() {
                $this.button('loading');
            },
            success: function(res) {
                popup(res.page);
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

    $(document).on('click', '#search_case_reference_id', function() {
        var case_reference_id = $("input[name=case_reference_id]").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientBycaseId/' + case_reference_id,
            type: "POST",
            data: {
                case_reference_id: case_reference_id
            },
            dataType: 'json',
            success: function(res) {
                if (res.status == 1) {
                    var option = new Option(res.patient_name, res.patient_id, true, true);
                    $("#myModal .patient_list_ajax").append(option).trigger('change');
                    $("#myModal .patient_list_ajax").trigger({
                        type: 'select2:select',
                        params: {
                            data: res
                        }
                    });
                } else {
                    errorMsg("<?php echo $this->lang->line('patient_not_found'); ?>");
                }
            }
        });
    });

    $(document).on('click', '#search_case_reference_id_edit', function() {
        var case_reference_id = $("#case_reference_id_edit").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientBycaseId/' + case_reference_id,
            type: "POST",
            data: {
                case_reference_id: case_reference_id
            },
            dataType: 'json',
            success: function(res) {
                if (res.status == 1) {
                    var option = new Option(res.patient_name, res.patient_id, true, true);
                    $("#editModal .patient_list_ajax").append(option).trigger('change');
                    // manually trigger the `select2:select` event
                    $("#editModal .patient_list_ajax").trigger({
                        type: 'select2:select',
                        params: {
                            data: res
                        }
                    });
                } else {
                    errorMsg("<?php echo $this->lang->line('patient_not_found'); ?>");
                }
            }
        });
    });
</script>
<script>
    $(document).on('change', '.payment_mode', function() {
        var mode = $(this).val();
        if (mode == "Cheque") {
            $('.filestyle', '#addPaymentModal').dropify();
            $('.cheque_div').css("display", "block");
        } else {
            $('.cheque_div').css("display", "none");
        }
    });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
    (function($) {
        'use strict';
        $(document).ready(function() {

            initDatatable('ajaxlist', 'admin/vehicle/getambulancecallDatatable', [], [], 100, [{
                    "sWidth": "100px",
                    "orderable": false,
                    "aTargets": [-1, ],
                    'sClass': 'dt-body-right'
                },
                {
                    "sWidth": "100px",
                    "aTargets": [-2, -3, -4],
                    'sClass': 'dt-body-right'
                }

            ]);

        });
    }(jQuery))
</script>
<script type="text/javascript">
    $(document).on('click','.delete_trans',function(e){
let record_id=$(this).data('recordId');
let billing_id=$(this).data('billingId');
let patient_id=$(this).data('patientId');
let case_id=$(this).data('caseId');
let balance=$(this).data('balance');
let replace_amount=$(this).data('amount');
 
if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deletePayment/' + record_id,
                beforeSend: function() {               

            },
            success: function(res) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);                    
					initDatatable('ajaxlist', 'admin/vehicle/getambulancecallDatatable', [], [],100);					
					var replace_amount1 =  parseInt(balance) + parseInt(replace_amount);		
					getPayments(billing_id,patient_id,replace_amount1,case_id);	 
                  
                },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");              
            },
            complete: function() {                

            }

            })
        }
    });
	
    function deletePayment(id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deletePayment/' + id,
                success: function(res) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }
</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>