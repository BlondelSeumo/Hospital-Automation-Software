<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
$load_blood_id = "";
if (!empty($bloodgroup)) {
    $load_blood_id = array_keys($bloodgroup)[0];
}
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" id="tachelist">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('blood_bank_status'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('blood_donor', 'can_view')) { ?>
                                <a href="<?php echo base_url(); ?>admin/bloodbank/search" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('donor_details'); ?></a>
                            <?php }
                            if ($this->rbac->hasPrivilege('blood_issue', 'can_view')) { ?>
                                <a href="<?php echo base_url() ?>admin/bloodbank/issue" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('blood_issue_details'); ?></a>
                            <?php }
                            if ($this->rbac->hasPrivilege('issue_component', 'can_view')) { ?>
                                <a href="<?php echo base_url(); ?>admin/bloodbank/component_issue" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('component_issue'); ?></a>
                            <?php } ?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="bloodbg">
                            <div class="row">
                                <div class="col-lg-1 col-md-1 col-sm-2">
                                    <ul class="nav nav-pills nav-stacked blood-stacked">
                                        <?php $i = 1;
                                        foreach ($bloodgroup as $group_key => $group_value) { ?>
                                            <li <?= $i == 1 ? " class='active'" : ""; ?>><a onclick="getBloodListTable(this.id)" id="<?= $group_key; ?>" data-toggle="tab" href="#menu<?= $group_key; ?>"><?= $group_value; ?></a></li>
                                        <?php $i++;
                                        } ?>
                                    </ul>
                                </div><!--./col-lg-3-->
                                <div class="col-lg-11 col-md-11 col-sm-10">
                                    <div class="tab-content">
                                        <div class="tab-pane fade in active">
                                            <div id="bloodGroupDiv" class="row">
                                            </div><!--./row-->
                                        </div><!--#/menu1-->
                                    </div><!--./tab-content-->
                                </div><!--./col-lg-9-->
                            </div><!--./row-->
                        </div><!--./bloodbg-->
                    </div><!--./box-body-->
                    <div class="">
                        <div class="mailbox-controls">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="editmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_blood_bank_status'); ?></h4>
            </div>
            <form id="bloodgroupstatus" action="<?php echo site_url('admin/bloodbankstatus/status') ?>" method="post" accept-charset="utf-8">
                <div class="modal-body pb0">
                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) { ?>
                            <?php echo $this->session->flashdata('msg') ?>
                        <?php } ?>
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="row">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('blood_group'); ?></label>
                                <input autofocus="" name="blood_group" readonly="readonly" type="text" id="blood_group" class="form-control" value="<?php
                                                                                                                                                    if (isset($result)) {
                                                                                                                                                        echo $result["blood_group"];
                                                                                                                                                    }
                                                                                                                                                    ?>" />
                                <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('status_in_bags'); ?></label><small class="req"> *</small>
                                <input autofocus="" id="status" name="status" placeholder="" type="text" class="form-control" value="<?php
                                                                                                                                        if (isset($result)) {
                                                                                                                                            echo $result["status"];
                                                                                                                                        }
                                                                                                                                        ?>" />
                                <span class="text-danger"><?php echo form_error('status'); ?></span>
                                <input autofocus="" id="id" name="id" placeholder="" type="hidden" class="form-control" value="<?php
                                                                                                                                if (isset($result)) {
                                                                                                                                    echo $result["id"];
                                                                                                                                }
                                                                                                                                ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="bloodgroupstatusbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div><!--./row-->
    </div>
</div>

<div class="modal fade" id="addBloodDetailModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close close_button pupclose" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('blood_donor_details'); ?></h4>
            </div>
            <form id="donorblood" accept-charset="utf-8" method="post" class="ptt10">
                <div class="pup-scroll-area">
                    <div class="modal-body pb0 pt0">
                        <input type="hidden" name="blood_bank_product_id" id="blood_group_id">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line("blood_donor"); ?></label>
                                    <small class="req"> *</small>
                                    <select name="blood_donor_id" id="blood_donor_id" style="width: 100%" class="form-control select2">
                                        <option value=""><?= $this->lang->line("select"); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('blood_donor_id'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('donate_date'); ?></label>
                                    <small class="req"> *</small>
                                    <input name="donate_date" type="text" class="form-control datetime" />
                                    <span class="text-danger"><?php echo form_error('quantity'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('bag'); ?> </label> <small class="req"> *</small>
                                    <input name="bag_no" type="text" class="form-control" />
                                    <span class="text-danger"><?php echo form_error('bag_no'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('volume'); ?></label>
                                    <input autofocus="" id="volume" name="volume" type="text" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('unit_type'); ?></label>
                                    <select name="unit" id="unit" class="form-control unit_type">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($unit_type as $unit_type_key => $unit_type_value) { ?>
                                            <option value="<?php echo $unit_type_value->id; ?>"><?php echo $unit_type_value->unit; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('unit_type'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('lot'); ?> </label>
                                    <input name="lot" type="text" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('charge_category'); ?></label><small class="req"> *</small>

                                    <select name="charge_category" id="charge_category" style="width: 100%" class="form-control select2 charge_category">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('charge_name'); ?></label> <small class="req"> *</small>
                                    <select name="charge_id" id="code" style="width: 100%" class="form-control addcharge select2 ">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label> <small class="req"> *</small>
                                    <input type="text" name="standard_charge" id="addstandard_charge" class="form-control" value="<?php echo set_value('standard_charge'); ?>">

                                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3 hide">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('qty'); ?></label><small class="req"> *</small>
                                    <input type="text" name="qty" id="qty" value="1" class="form-control">
                                    <span class="text-danger"><?php echo form_error('qty'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('institution'); ?></label>
                                    <input name="institution" type="text" class="form-control" />
                                </div>
                            </div>
                        </div><!--./row-->
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
                                        <th><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                        <td class="text-right ipdbilltable">
                                            <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                            <input type="text" placeholder="<?php echo $this->lang->line('discount'); ?>" name="discount_percent" id="discount_percent" class="form-control discount_percent" style="width: 70%; float: right;font-size: 12px;" />
                                        </td>
                                        <td class="text-right ipdbilltable">
                                            <input type="text" placeholder="<?php echo $this->lang->line('discount'); ?>" value="0" name="discount" id="discount" style="width: 50%; float: right" class="form-control discount" />
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
                                                <?php foreach ($payment_mode as $key => $value) {
                                                ?>
                                                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                <?php
                                                }
                                                ?>
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
                        </div><!--./row-->
                    </div>
                </div>
                <div class="modal-footer sticky-footer">
                    <div class="pull-right">
                        <button type="button" onclick="calculateAmt(true)" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-calculator"></i> <?php echo $this->lang->line('calculate'); ?></button>
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="donorbloodbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_components'); ?></h4>
            </div>
            <form id="componentsadd" accept-charset="utf-8" method="post" class="ptt10">
                <div class="scroll-area">
                    <div class="modal-body pb0 pt0">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('blood_group'); ?></label><small class="req"> *</small>
                                    <select style="width: 100%" class="form-control select2 blood_group" id="blood_bank_product_id" name="blood_bank_product_id">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($bloodgroup as $key => $value) {
                                        ?>
                                            <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) {
                                                                                    echo "selected";
                                                                                }
                                                                                ?>><?php echo $value; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6">
                                <div class="form-group select-100">
                                    <label><?php echo $this->lang->line('bag'); ?></label><small class="req"> *</small>
                                    <select style="width: 100%" class="form-control select2 bag_no" name="blood_donor_cycle_id">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div><!--./row-->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="white-space-nowrap"><?php echo $this->lang->line('components_name'); ?><small class="req"> *</small></th>
                                        <th><?php echo $this->lang->line('bag'); ?><small class="req"> *</small></th>
                                        <th><?php echo $this->lang->line('volume'); ?></th>
                                        <th><?php echo $this->lang->line('unit'); ?></th>
                                        <th><?php echo $this->lang->line('lot'); ?><small class="req"> *</small></th>
                                        <th><?php echo $this->lang->line('institution'); ?></th>
                                    </tr>
                                    <?php
                                    foreach ($components as $key => $value) {
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="checkbox"><label><input type="checkbox" name="select[]" value="<?php echo $key; ?>" /> <?php echo $value; ?></label></div>
                                            </td>
                                            <td><input type="text" class="form-control min-w-sm-160-px" name="bag_no_<?php echo $key ?>" value="" /></td>
                                            <td><input type="text" class="form-control min-w-sm-160-px" name="volume_<?php echo $key ?>" value="" /></td>
                                            <td><select type="text" class="form-control min-w-sm-160-px" name="unit_<?php echo $key ?>" value="">
                                                    <option value=""> <?php echo $this->lang->line('select') ?></option>
                                                    <?php
                                                    foreach ($unit_type as $typekey => $typevalue) {
                                                    ?>
                                                        <option value="<?php echo $typevalue->id; ?>"><?php echo $typevalue->unit; ?></option><?php
                                                                                                                                            }
                                                                                                                                                ?>
                                                </select></td>
                                            <td><input type="text" class="form-control min-w-sm-160-px" name="lot_<?php echo $key ?>" value="" /></td>
                                            <td><input type="text" class="form-control min-w-sm-160-px" name="institution_<?php echo $key ?>" value="" /></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div><!--./row-->
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bloodIssueModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <form id="formadd" accept-charset="utf-8" method="post">
                <div class="modal-header modal-media-header">
                    <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                    <div class="row">
                          <div class="col-lg-5 col-md-5 col-sm-11">                              
                            <div class="row">
                            <div class="col-lg-9 col-md-7 col-sm-8 col-xs-8">
                             <div class="p-2 select2-full-width">
                                <select class="form-control patient_list_ajax" name='patient_id' id="addpatient_id" onchange="get_PatientDetails(this.value)">
                                </select>
                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                             </div>
                            </div>
                            <div class="col-lg-3 col-md-5 col-sm-4 col-xs-2">
                             <div class="p-2">
                                <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <a data-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient  w-lg-100"><i class="fa fa-plus"></i> <span><?php echo $this->lang->line('new_patient'); ?></span></a>
                                <?php } ?>
                             </div>
                            </div>
                            </div>
                          </div>
                          <div class="col-lg-5 col-md-5 col-sm-11">
                            <div class="row pt-sm-2"> 
                             <div class="col-lg-9 col-md-8 col-sm-8 col-xs-8">
                              <div class="p-2">
                                <div class="input-group">
                                  <input type="text" class="form-control" id="case_reference_id" placeholder="<?php echo $this->lang->line('case_id'); ?>" name="case_reference_id">
                                    <div class="input-group-btn">
                                     <button class="btn btn-default btn-group-custom search_case_reference_id" type="button" id="search_case_reference_id"  data-modal-name="bloodIssueModal">
                                      <i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                              </div>
                             </div>
                              <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                               <div class="form-check pt5">
                                <input class="form-check-input" type="checkbox" value="1" id="is_tpa" name="is_tpa" onclick="reset_all()">
                                <label class="form-check-label text-white" for="is_tpa">
                                <?php echo $this->lang->line('apply_tpa'); ?>
                                </label>
                                </div> 
                              </div>
                            </div>
                          </div>
                    </div><!-- ./row -->
                </div>
                <div class="pup-scroll-area">
                    <div class="modal-body pb0 ptt10">
                    </div><!--./modal-body-->
                </div>
                <div class="box-footer sticky-footer">
                    <div class="pull-right">
                         <input type="hidden" id="organisation_id" name="organisation_id" />
                         <input type="hidden" id="insurance_id" name="insurance_id" />
                         <input type="hidden" id="insurance_validity" name="insurance_validity" />
                        <button type="button" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info printsavebtn_issue"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save_print'); ?></button>
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn_issue" class="btn btn-info mleft5"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="componentIssueModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <form id="formaddComponent" accept-charset="utf-8" method="post">
                <div class="modal-header modal-media-header">
                    <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                    <div class="row">
                        <div class="col-lg-5 col-md-5 col-sm-11">
                        <div class="row">
                         <div class="col-lg-9 col-md-7 col-sm-8 col-xs-8">
                          <div class="p-2 select2-full-width">
                           <select class="form-control patient_list_ajax" name='patient_id' id="addpatient_id" onchange="get_component_PatientDetails(this.value)"></select>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                          </div>
                         </div>
                         <div class="col-lg-3 col-md-5 col-sm-4 col-xs-2">                                
                          <div class="form-group15">
                           <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                            <a data-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient   w-lg-100"><i class="fa fa-plus"></i> <span><?php echo $this->lang->line('new_patient'); ?></span></a>
                            <?php } ?>
                           </div>
                        </div>
                        </div>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-11">                            
                        <div class="row pt-sm-2"> 
                         <div class="col-lg-9 col-md-8 col-sm-8 col-xs-8">
                          <div class="p-2">
                            <div class="input-group">
                             <input type="hidden" id="component_organisation_id" name="organisation_id" />
                             <input type="hidden" id="component_insurance_id" name="insurance_id" />
                             <input type="hidden" id="component_insurance_validity" name="insurance_validity" />
                             <input type="text" class="form-control" id="case_reference_id" placeholder="<?php echo $this->lang->line('case_id'); ?>" name="case_reference_id">
                             <div class="input-group-btn">
                             <button class="btn btn-default btn-group-custom search_case_reference_id" type="button" id="search_case_reference_id" data-modal-name="componentIssueModal" >
                             <i class="fa fa-search"></i>
                             </button>
                             </div>
                            </div>
                           </div>
                           </div>
                           <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <div class="form-check pt5">
                             <input class="form-check-input" type="checkbox" value="1" id="is_tpa" name="is_tpa" onclick="reset_all()">
                             <label class="form-check-label text-white" for="is_tpa">
                             <?php echo $this->lang->line('apply_tpa'); ?>
                             </label>
                             </div>                         
                           </div>
                           </div>
                        </div>
                    </div><!-- ./row -->
                </div>
                <div class="pup-scroll-area">
                    <div class="modal-body pb0 ptt10">
                    </div><!--./modal-body-->
                </div>
                <div class="modal-footer sticky-footer">
                    <div class="pull-right">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn_issue_component" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                    <div class="pull-right" style="margin-right:10px;">
                        <button type="button" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right printsavebtn_issue"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="availableBloodModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('available_bloods'); ?></h4>
            </div>
            <div class="modal-body pb0 ptt10">
            </div><!--./col-md-12-->
        </div><!--./row-->
    </div>
</div>

<script>
    function get(id) {
        $('#editmyModal').modal('show');
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/bloodbankstatus/edit/' + id,
            success: function(result) {
                $('#id').val(result.id);
                $('#status').val(result.status);
                $('#blood_group').val(result.blood_group);
            }
        });
    }

    $(document).on('change', '.payment_mode', function() {
        var mode = $(this).val();
        if (mode == "Cheque") {
            $('.filestyle', '#addBloodDetailModal').dropify();
            $('.cheque_div').css("display", "block");
        } else {
            $('.cheque_div').css("display", "none");
        }
    });
	
    $(document).ready(function(e) {
         
        $('.select2').select2();
        $('#editformadd').on('submit', (function(e) {
            $("#editformaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
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
                        window.location.reload(true);
                    }
                    $("#editformaddbtn").button('loading');
                },
                error: function() {}
            });
        }));

        $('#bloodgroupstatus').on('submit', (function(e) {
            $("#bloodgroupstatusbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
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
                        window.location.reload(true);
                    }
                    $("#bloodgroupstatusbtn").button('reset');
                },
                error: function() {}
            });
        }));
    });

    $(document).on('click', '.getbatchlist', function() {
        var createModal = $('#availableBloodModal');
        var $this = $(this);
        var bloodgroup = $(this).data('bloodGroup');

        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbankstatus/getAvailabelBloodByGroup',
            type: "POST",
            data: {
                'bloodgroup': bloodgroup
            },
            dataType: 'json',
            beforeSend: function() {
                this.button('loading');
                createModal.addClass('modal_loading');
            },
            success: function(res) {
                $('.modal-body', createModal).html(res.page);
                createModal.modal('show');

            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                this.button('reset');
                createModal.removeClass('modal_loading');
            },
            complete: function() {
                this.button('reset');
                createModal.removeClass('modal_loading');
            }
        });
    });

    function bloodDetailsModal(bloodgroupid) {
        $("#donorblood").trigger("reset");
        $("#addBloodDetailModal").modal({
            backdrop: "static"
        });
        div_data = "";
        $("#blood_group_id").val(bloodgroupid);
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/getchargebymodule',
            type: "POST",
            data: {
                module: "blood_bank"
            },
            dataType: 'json',
            success: function(res) {

                $.each(res, function(i, obj) {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category').append(div_data);
                $('.charge_category').select2("val", charge_category);
                $('#charge_category').val(null).trigger('change');
            }
        });

        get_donor_list(bloodgroupid);
    }

    function get_donor_list(bloodgroupid) {
        div_val = "";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/get_donor_list/' + bloodgroupid,
            type: "POST",
            dataType: 'json',
            beforeSend: function() {
                $('#blood_donor_id').html("").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
            },
            success: function(res) {

                $.each(res, function(i, obj) {
                    var sel = "";
                    var option = new Option(obj.donor_name + " (" + obj.blood_group + ")", obj.id, true, true);
                    $('#blood_donor_id').append(option).trigger('change');
                });

                $('#blood_donor_id').select2('val', ''); // select the new term
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function() {

            }
        });
    }

    function componentDetailsModal(bloodgroupid) {
        $("#blood_bank_product_id").val(bloodgroupid).trigger('change');
        $("#myModal").modal({
            backdrop: "static"
        });
        var bloodgroup = $("#blood_bank_product_id").val();
        getBloodGroupBagNos(bloodgroup, "");
    }

    $(document).ready(function(e) {
        $("#donorblood").on('submit', (function(e) {
            var button_loading = $("#donorbloodbtn");

            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/donorCycle',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    button_loading.button("loading");
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
                        window.location.reload(true);
                    }
                    $("#donorbloodbtn").button('reset');
                },
                error: function() {
                    button_loading.button('reset');
                },
                complete: function() {
                    button_loading.button('reset');
                }
            });
        }));
    });

    $(document).on('select2:select', '.charge_category', function() {
        var charge_category = $(this).val();
        $('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
        $('.addcharge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
        getchargecode(charge_category, "");
    });

    function getchargecode(charge_category, charge_id) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $('#tax_percentage').val(0);
        $('#code').val("").trigger("change");
        $("#addstandard_charge").val(0);
        $("#total").val(0);
        $("#discount").val(0);
        $("#tax").val(0);
        $("#net_amount").val(0);
        $("#payment_amount").val(0);
        if (charge_category != "") {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/charges/getchargeDetails',
                type: "POST",
                data: {
                    charge_category: charge_category
                },
                dataType: 'json',
                success: function(res) {                   
                    $.each(res, function(i, obj) {
                        var sel = "";
                        div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                    });
                    $('.addcharge').html(div_data);
                    $(".addcharge").select2("val", charge_id);
                }
            });
        }
    }

    $(document).on('select2:select', '.addcharge', function() {        
        var charge = $(this).val();
        var orgid = "";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {
                charge_id: charge,
                organisation_id: orgid
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    var quantity = $('#qty').val();
                    quantity = (quantity == "") ? 0 : quantity;
                    var total_amout = parseFloat(res.result.standard_charge) * quantity;

                    $('#addstandard_charge').val(res.result.standard_charge);
                    var discount_percent = $('#discount_percent').val();
                    $('#tax_percentage').val(res.result.percentage);
                    var discount_amount = parseFloat(total_amout * discount_percent / 100);
                    var tax = $('#tax_percentage').val();
                    var tax_amount = parseFloat((total_amout - discount_amount) * tax / 100)
                    var net_amount = (total_amout - discount_amount) + tax_amount;
                    $('#total').val(total_amout.toFixed(2));
                    $('#tax').val(tax_amount.toFixed(2));

                    $('#net_amount').val(net_amount.toFixed(2));
                    $('#payment_amount').val(net_amount.toFixed(2));
                }
            }
        });
    });

    $(document).on('change keyup input paste', '#qty', function() {
        var quantity = $(this).val();
        var standard_charge = $('#addstandard_charge').val();
        var tax_percent = $('#tax_percentage').val();
        var total_charge = (standard_charge == "") ? 0 : standard_charge;
        var apply_charge = isNaN(parseFloat(total_charge) * parseFloat(quantity)) ? 0 : parseFloat(total_charge) * parseFloat(quantity);
        $('#total').val(apply_charge);
        var discount_percent = $('#discount_percent').val();
        var discount_amount = isNaN((apply_charge * discount_percent) / 100) ? 0 : (apply_charge * discount_percent) / 100;
        var final_amount = apply_charge - discount_amount;
        var total_tax = ((final_amount * tax_percent) / 100);
        var total_payment_amount = final_amount + ((final_amount * tax_percent) / 100);
        $('#discount').val(discount_amount.toFixed(2));
        $('#tax').val(total_tax.toFixed(2));
        $('.payment_amount').val(total_payment_amount.toFixed(2));
        $('#net_amount').val(total_payment_amount.toFixed(2));
    });

    $(document).on('change keyup input paste', '#discount', function() {
        calculateAmt(false);
    });

    $(document).on('change keyup input paste', '#addstandard_charge', function() {
        var standard_charge = $("#addstandard_charge").val();
        var qty = $("#qty").val();
        $("#total").val(standard_charge * qty);
        calculateAmt(false);
    });

    $(document).on('change keyup input paste', '#discount_percent', function() {
        calculateAmt(true);
    });

    function calculateAmt(is_percentage) {
     
        var tot_amt = parseFloat($('#total').val());
        if (is_percentage) {
            var dis_per = $('#discount_percent').val();
            var dis_amt = parseFloat(tot_amt * dis_per / 100);
            $('#discount').val(dis_amt.toFixed(2));
        } else {
            var dis_amt = parseFloat($('#discount').val());
            var dis_per = isNaN(((dis_amt * 100) / tot_amt)) ? 0 : ((dis_amt * 100) / tot_amt);
            $('#discount_percent').val(dis_per.toFixed(2));
        }
        var tax_per = parseFloat($('#tax_percentage').val());
        var tax_amt = parseFloat((tot_amt - dis_amt) * tax_per / 100);
        $('#tax').val(tax_amt.toFixed(2));
        var net_amt = isNaN(tax_amt + (tot_amt - dis_amt)) ? "" : (tax_amt + (tot_amt - dis_amt)).toFixed(2);
        $('#net_amount').val(net_amt);
        $('#payment_amount').val(net_amt);
    }
    <?php
    if ($load_blood_id != '') { ?>
        getBloodListTable('<?php echo $load_blood_id; ?>');
    <?php
    }
    ?>

    function getBloodListTable(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbankstatus/getBloodListTable/' + id,
            type: "POST",
            data: {
                id: id
            },
            success: function(res) {                
                $("#bloodGroupDiv").html(res);
            }
        });
    }

    $(document).on('change keyup input paste', '#addstandard_charge_issue', function() {
        var standard_charge = $("#addstandard_charge_issue").val();
        var qty = $("#qty_issue").val();
        $("#total_issue").val(standard_charge * qty);
        calculateAmtIssue(false);
    });

    $(document).on('change keyup input paste', '#discount_percent_issue', function() {
        calculateAmtIssue(true);
    });

    function calculateAmtIssue(is_percentage) {
        let dis_amt=0;
        var tot_amt = parseFloat($('#total_issue').val());
        if (is_percentage) {
            var dis_per = $('#discount_percent_issue').val();
            //  dis_amt = parseFloat(tot_amt * dis_per / 100);
             dis_amt =  isNaN((tot_amt * dis_per / 100)) ? 0 : parseFloat(tot_amt * dis_per / 100);;
            $('#discount_issue').val(dis_amt.toFixed(2));
        } else {
             dis_amt = parseFloat($('#discount_issue').val());
            var dis_per = isNaN(((dis_amt * 100) / tot_amt)) ? 0 : ((dis_amt * 100) / tot_amt);
            $('#discount_percent_issue').val(dis_per.toFixed(2));
        }

        var tax_per = parseFloat($('#tax_percentage_issue').val());
        var tax_amt = parseFloat((tot_amt - dis_amt) * tax_per / 100);
        $('#tax_issue').val(tax_amt.toFixed(2));
        var net_amt = isNaN(tax_amt + (tot_amt - dis_amt)) ? "" : (tax_amt + (tot_amt - dis_amt)).toFixed(2);
        $('#net_amount_issue').val(net_amt);
        $('#payment_amount_issue').val(net_amt);
    }

    function getBloodGroupBagNos(bloodgroup, bagno) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $.ajax({
            url: '<?= base_url(); ?>admin/bloodbank/getbatchbybloodgroup',
            type: "POST",
            data: {
                'bloodgroup': bloodgroup
            },
            dataType: 'json',
            beforeSend: function() {
                $('.bag_no').html("");
            },
            success: function(res) {
                $.each(res.batch_list, function(i, obj) {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "' available_unit='" + obj.quantity + "'>" + obj.bag_no + " " + obj.volume + " " + obj.unit + "</option>";
                });
                $('.bag_no').html(div_data);
                $('.bag_no').select2("val", bagno);
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function() {

            }
        });
    }

    $(document).ready(function(e) {
        $("#componentsadd").on('submit', (function(e) {
            $("#formaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/addcomponents',
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
                        window.location.reload(true);
                    }
                    $("#formaddbtn").button('reset');
                },
                error: function() {}
            });
        }));
    });
</script>

<script>
    function componentIssueModal(blood_group_id, bag_no,bloodid) {
        $("#formaddComponent").trigger('reset');
        issueModal = $("#componentIssueModal");
        issueModal.modal("show");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/issuecomponentfront',
            type: "POST",
            dataType: 'json',
            success: function(res) {
                $('.modal-body', issueModal).html(res.page);
                getcharge_category_issue("blood_bank");
                $('.filestyle', '#componentIssueModal').dropify();
                $('.modal-body', issueModal).find('.select2').select2();
				
                $("#blood_group_issue_select").val(blood_group_id);
                $("#blood_group_issue").val(blood_group_id);
                $('#blood_group_issue_select').trigger('change');
                getComponentBagNosIssue(blood_group_id, bag_no,bloodid)
            },
        });
    }

    function bloodIssueModal(blood_group_id, bag_no) {
        $("#formadd").trigger('reset');
        issueModal = $("#bloodIssueModal");
        issueModal.modal("show");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/issuebloodFront',
            type: "POST",
            dataType: 'json',
            success: function(res) {
                $('.modal-body', issueModal).html(res.page);
                $('.datetime').trigger('change');
                getcharge_category_issue("blood_bank");
                $('.filestyle', '#bloodIssueModal').dropify();
                $('.modal-body', issueModal).find('.select2').select2();
				$("#blood_group_issue_select").val(blood_group_id);
                $("#blood_group_issue").val(blood_group_id);
                $('#blood_group_issue_select').trigger('change');
                getBloodGroupBagNosIssue(blood_group_id, bag_no)
            },
        });
    }

    // Bind consultant_doctor event
    $(document).on('select2:select', '#consultant_doctor', function(e) {
        var reference_name = $("#consultant_doctor option:selected").text();
        $('#reference').val(reference_name);
    });

    function get_Docname(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctName',
            type: "POST",
            data: {
                doctor: id
            },
            dataType: 'json',
            success: function(res) {
                if (res) {
                    $('#reference').val(res.name + " " + res.surname);
                } else {

                }
            }
        });
    }

    function getComponentBagNosIssue(component_id, bagno,bloodid) {
      
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/getComponentBagNosIssue',
            type: "POST",
            data: {
                'blood_group_id': bloodid,
                'component_id':component_id
            },
            dataType: 'json',
            beforeSend: function() {
                $('.bag_no_issue').html("");
            },
            success: function(res) {
                $.each(res.batch_list, function(i, obj) {
                    var sel = "";
                    let val_unit = "";
                    let volume = obj.volume != null ? obj.volume : "";
                    let unit = obj.charge_unit != null ? obj.charge_unit : "";
                    if (volume != "" || unit != "") {
                        val_unit = " (" + volume + " " + unit + ")";
                    }
                    div_data += "<option value='" + obj.id + "' available_unit='" + obj.quantity + "'>" + obj.bag_no + val_unit + " </option>";
                });
                $('.bag_no_issue').html(div_data);
                $('.bag_no_issue').select2("val", bagno);
            },
        });
    }

    function getBloodGroupBagNosIssue(bloodgroup, bagno) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/getbatchbybloodgroup',
            type: "POST",
            data: {
                'bloodgroup': bloodgroup
            },
            dataType: 'json',
            beforeSend: function() {
                $('.bag_no_issue').html("");
            },
            success: function(res) {
                $.each(res.batch_list, function(i, obj) {
                    var sel = "";
                    let val_unit = "";
                    let volume = obj.volume != null ? obj.volume : "";
                    let unit = obj.charge_unit != null ? obj.charge_unit : "";
                    if (volume != "" || unit != "") {
                        val_unit = " (" + volume + " " + unit + ")";
                    }
                    div_data += "<option value='" + obj.id + "' available_unit='" + obj.quantity + "'>" + obj.bag_no + val_unit + " </option>";
                });
                $('.bag_no_issue').html(div_data);
                $('.bag_no_issue').select2("val", bagno);
            },
        });
    }

    $(document).on('select2:select', '.blood_group_issue', function() {
        var bloodgroup = $(this).val();
        getBloodGroupBagNosIssue(bloodgroup, "");
    });
    $(document).on('select2:select', '.component_issue', function() {
        var bloodgroup = $(this).val();
        getComponentBagNosIssue(bloodgroup, "");
    });

    function getcharge_category_issue(module) {
        var div_data = "";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/getchargebymodule',
            type: "POST",
            data: {
                module: module
            },
            dataType: 'json',
            success: function(res) {
                $.each(res, function(i, obj) {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category_issue').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category_issue').append(div_data);
                $('.charge_category_issue').select2("val", charge_category);
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

    $(document).on('select2:select', '.charge_category_issue', function() {
        var charge_category = $(this).val();
        $('.addcharge_issue').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
        getchargecodeissue(charge_category, "");
    });

    function getchargecodeissue(charge_category, charge_id) {
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        if (charge_category != "") {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/charges/getchargeDetails',
                type: "POST",
                data: {
                    charge_category: charge_category
                },
                dataType: 'json',
                success: function(res) {                  
                    $.each(res, function(i, obj) {
                        var sel = "";
                        div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                    });
                    $('.addcharge_issue').html(div_data);
                    $(".addcharge_issue").select2("val", charge_id);
                }
            });
        }
    }

    $(document).on('select2:select', '.addcharge_issue', function(e) {
        var charge      = $(this).val();       
        let modal_div   = $(e.target).closest('div.modal')
        let is_tpa      = modal_div.find("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
        var patient_id  = modal_div.find('.patient_list_ajax').val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbankstatus/getBloodBankChargeById',
            type: "POST",           
            data: {'charge_id': charge ,'patient_id':patient_id,'is_tpa':is_tpa},
            dataType: 'json',
            success: function(res) {
                if(res.status == 0){
                        errorMsg(res.msg);
                    }else{

                    if(res.display_tpa_charge){
                        var charge_amt=res.result.org_charge;
                    }else{
                        var charge_amt=res.result.standard_charge;
                    }          

                    var quantity = $('#qty_issue').val();
                    quantity = (quantity == "") ? 0 : quantity;
                    var total_amout = parseFloat(charge_amt) * quantity;
                    $('#total_issue').val(total_amout);
                    $('#addstandard_charge_issue').val(res.result.standard_charge);
                    var discount_percent = $('#discount_percent_issue').val();
                    $('#tax_percentage_issue').val(res.result.percentage);
                    var discount_amount = parseFloat(total_amout * discount_percent / 100);
                    var tax = $('#tax_percentage_issue').val();
                    var tax_amount = parseFloat((total_amout - discount_amount) * tax / 100)
                    var net_amount_issue = (total_amout - discount_amount) + tax_amount;
                    var payment_amount_issue = (total_amout - discount_amount) + tax_amount;
                    $('#tax_issue').val(tax_amount.toFixed(2));
                    $('#net_amount_issue').val(net_amount_issue.toFixed(2));
                    $('#payment_amount_issue').val(payment_amount_issue.toFixed(2));
                    if(res.status == 2){
                            errorMsg(res.msg);
                        }
                }
            }
        });
    });

    $(document).on('change', '.bag_no_issue', function() {
        var available_unit = $(this).find('option:selected').attr("available_unit");
        $('#qty_issue').val(available_unit);
    });

    $(document).ready(function(e) {
        $(".printsavebtn_issue").on('click', (function(e) {
            var form = $(this).parents('form').attr('id');
            var str = $("#" + form).serializeArray();
            var postData = new FormData();
            $.each(str, function(i, val) {
                postData.append(val.name, val.value);
            });

            $("#printsavebtn_issue").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/addIssue',
                type: "POST",
                data: postData,
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
                        printData(data.id);
                    }
                    $("#printsavebtn_issue").button('reset');
                },
                error: function() {

                }
            });
        }));
    });

    function printData(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/getBillDetails/' + id,
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

    $(document).ready(function(e) {
        $("#formadd").on('submit', (function(e) {
            var str = $("#formadd").serializeArray();
            var postData = new FormData();
            var case_reference_id = $("input[name=case_reference_id]").val();
            $.each(str, function(i, val) {
                postData.append(val.name, val.value);
            });
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/addIssue',
                type: "POST",
                data: postData,
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $("#formaddbtn_issue").button('loading');
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
                        window.location.reload(true);
                    }
                    $("#formaddbtn_issue").button('reset');
                },
                error: function() {
                    $("#formaddbtn_issue").button('reset');
                },
                complete: function() {
                    $("#formaddbtn_issue").button('reset');
                }
            });
        }));
    });

    $(document).ready(function(e) {
        $("#formaddComponent").on('submit', (function(e) {
            var str = $("#formaddComponent").serializeArray();
            var postData = new FormData();
            var case_reference_id = $("input[name=case_reference_id]").val();
            $.each(str, function(i, val) {
                postData.append(val.name, val.value);
            });
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/addIssueComponent',
                type: "POST",
                data: postData,
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $("#formaddbtn_issue_component").button('loading');
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
                        window.location.reload(true);
                    }
                    $("#formaddbtn_issue_component").button('reset');
                },
                error: function() {
                    $("#formaddbtn_issue_component").button('reset');
                },
                complete: function() {
                    $("#formaddbtn_issue_component").button('reset');
                }
            });
        }));
    });

    $(document).on('click', '.search_case_reference_id', function() {        
         var case_reference_id = $(this).closest("div.modal").find("input[name='case_reference_id']").val();
         var modalname=$(this).data('modal-name');		 
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
                    $("#"+modalname).find(".patient_list_ajax").append(option).trigger('change');
                    $("#"+modalname).find(".patient_list_ajax").trigger({
                        type: 'select2:select',
                        params: {
                            data: res
                        }
                    });             
            

                } else {
                    errorMsg('<?php echo $this->lang->line("patient_not_found"); ?>');
                }
            }
        });
    });

    function reset_all(){
        $('#payment_amount_issue').val(0);
        $('#addstandard_charge_issue').val('');
        $('.addcharge_issue').val('').trigger('change');
        $('.charge_category_issue').val('').trigger('change');
        $(".printablea4").find('input').val(0);
    }

</script>

<script>
    function getcharge_category_module(module) {
        var div_data = "";
        $.ajax({
            url: base_url + 'admin/charges/getchargebymodule',
            type: "POST",
            data: {
                module: module
            },
            dataType: 'json',
            success: function(res) {
                $.each(res, function(i, obj) {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category').append(div_data);
                $('.charge_category').select2("val", charge_category);
            }
        });
    }

    $('.close_button').click(function() {
        $(".select2").select2().select2('val', '');
        $(".addcharge").select2().select2('val', '');
        $('.cheque_div').hide();
    })
</script>

<?php $this->load->view('admin/patient/patientaddmodal') ?>

<script>
	function get_PatientDetails(id) {   
        reset_all();
        $.ajax({
            url: base_url+'admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            beforeSend: function() {                
            },
            success: function (res) {                
                if (res) {
                    $("#organisation_id").val(res.organisation_id);
                    $("#insurance_id").val(res.insurance_id);
                    $("#insurance_validity").val(res.insurance_validity);                   
                } 
            }
        });
    }
	
    function get_component_PatientDetails(id) {   
        reset_all();
        $.ajax({
            url: base_url+'admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            beforeSend: function() {                
            },
            success: function (res) {                
                if (res) {
                    $("#component_organisation_id").val(res.organisation_id);
                    $("#component_insurance_id").val(res.insurance_id);
                    $("#component_insurance_validity").val(res.insurance_validity);                   
                } 
            }
        });
    }
</script>