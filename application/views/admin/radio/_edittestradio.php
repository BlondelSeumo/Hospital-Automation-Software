<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <input name="customer_name" id="patient_name" type="hidden" class="form-control"/>
        <input name="patient_id" id="patient_id" type="hidden" class="form-control"/>
        <input name="action_type" id="action_type" value="insert" type="hidden" class="form-control"/>
        <a class="btn btn-info pull-right add-record" data-added="0"><i class="fa fa-plus"></i><?php echo $this->lang->line('add') ?></a>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table tableover table-striped table-bordered table-hover tablefull12 tblProducts" id="tableID">
                                            <thead>
                                                <tr class="font13">
                                                    <th width="15%"><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?><small class="req" style="color:red;"> *</small></th>
                                                    <th width="10%"><?php echo $this->lang->line('report') . " " . $this->lang->line('days'); ?><small class="req" style="color:red;"> *</small></th>
                                                     <th width="15%"><?php echo $this->lang->line('report') . " " . $this->lang->line('date'); ?><small class="req" style="color:red;"> *</small></th>
                                                    <th class="text-right" width="10%"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?><small class="req" style="color:red;"> *</small></th>
                                                     <th class="text-right" width="2%"></th>
                                                </tr>
                                            </thead>
                                            <tr id="row1">
                                                <td>
                                                    <input type="hidden" id="" name="total_rows[]" value="1">
                                                    <select class="form-control test_name select2" style="width:100%" onchange="gettestradiodetails(this.value, 0)" name='test_name[]'>
                                                        <option value="<?php echo set_value('test_name_id'); ?>"><?php echo $this->lang->line('select') ?>
                                                        </option>
                                                    <?php foreach ($testlist as $dkey => $dvalue) { ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["test_name"] ?>
                                                            </option>
                                                        <?php }?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('test_name_id[]'); ?>
                                                    </span>
                                                </td>
                                                <td>    
                                                    <input type="text" name="reportday[]" id="reportday0" placeholder="" class="form-control text-right" readonly>
                                                    <span class="text-danger"><?php echo form_error('reportday[]'); ?></span>
                                                </td>
                                                <td>
                                                    <input type="text" name="reportdate[]" id="reportdate0" placeholder="" class="form-control text-right">
                                                    <span class="text-danger"><?php echo form_error('reportdate[]'); ?></span>
                                                </td>
                                                <td class="text-right">
                                                    <input type="text" name="amount[]" id="amount0" placeholder="" class="form-control text-right" readonly>
                                                    <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                                                </td>
                                                <td>
                                                     <button type="button"  class="closebtn delete_row" data-row-id="1" autocomplete="off"><i class="fa fa-remove"></i></button>
                                                </td>
                                            </tr>
                                        </table>     
                                    </div>
                                    <div class="divider"></div>
                                         <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputFile">
                                                                   <?php echo $this->lang->line('refferal') . " " . $this->lang->line('doctor'); ?></label>
                                                                <div><select name='consultant_doctor' style="width:100%;" id="consultant_doctor" onchange="get_Docname(this.value)" class="form-control select2" <?php
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
    ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"]." ( ".$dvalue["employee_id"]." )" ?></option>
<?php }?>
                                                                    </select>
                                                                </div>
                                                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label><?php echo $this->lang->line('doctor') . " " . $this->lang->line('name'); ?></label>
                                                                <input name="doctor_name" id="doctname" type="text" class="form-control"/>
                                                                <span class="text-danger"><?php echo form_error('doctor_name'); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
                                                    <input type="text" placeholder="Total" value="0" name="total" id="total" style="width: 30%; float: right" class="form-control total"/></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-right ipdbilltable">
                                                        <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="Discount" name="discount_percent" id="discount_percent" onchange="addTotal()" class="form-control discount_percent" style="width: 70%; float: right;font-size: 12px;"/></td>
                                                    <td class="text-right ipdbilltable">
                                            <input type="text" placeholder="Discount" value="0" name="discount" id="discount" style="width: 50%; float: right" class="form-control discount"/></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-right ipdbilltable">
                                                        <h4 style="float: right;font-size: 12px;padding-left: 5px;"> %</h4><input type="text" placeholder="Tax" onchange="addTotal()" name="tax_percent" value="" id="tax_percent" style="width: 70%; float: right;font-size: 12px;" class="form-control tax_percent"/>
                                                    </td>
                                                    <td class="text-right ipdbilltable">
                                                        <input type="text" placeholder="Tax" name="tax" value="0" id="tax" style="width: 50%; float: right" class="form-control tax"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td colspan="2" class="text-right ipdbilltable">
                                                        <input type="text" placeholder="Net Amount" value="0" name="net_amount" id="net_amount" style="width: 30%; float: right" class="form-control net_amount"/></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div><!--./row-->
                                </div><!--./col-md-12-->
                            </div><!--./row-->
                        </div><!--./box-footer-->
                    </div><!--./col-md-12-->