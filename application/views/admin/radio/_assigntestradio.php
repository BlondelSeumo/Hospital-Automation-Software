<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">  
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table tableover table-striped mb5 table-bordered table-hover tablefull12 tblProducts" id="tableID">
                        <thead>
                            <tr class="font13" class="white-space-nowrap">
                                <th width="15%"><?php echo $this->lang->line('test_name'); ?><small class="req"> *</small></th>
                                <th width="10%"><?php echo $this->lang->line('report_days'); ?></th>
                                <th width="15%"><?php echo $this->lang->line('report_date'); ?><small class="req" style="color:red;"> *</small></th>
                                 <th class="text-right" width="7%"><?php echo $this->lang->line('tax'); ?></th>
                                <th class="text-right" width="10%"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                <th class="text-right" width="2%"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr id="row1" class="white-space-nowrap">
                            <td>
                                <input type="hidden" id="" name="total_rows[]" value="1">
                                <input type="hidden" name="inserted_id_1" value="0">
                                <select class="form-control test_name select2" style="width:100%"  name='test_name_1'  id='1'>
                                    <option value="<?php echo set_value('test_name_id'); ?>"><?php echo $this->lang->line('select'); ?>
                                    </option>
                                <?php foreach ($testlist as $dkey => $dvalue) { ?>
                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["test_name"]." (".$dvalue["short_name"].")"; ?>
                                        </option>
                                    <?php }?>
                                </select>
                                <span class="text-danger"><?php echo form_error('test_name_id[]'); ?>
                                </span>
                            </td>
                            <td>    
                                <input type="text" name="reportday_1" id="reportday_1" placeholder="" class="form-control text-right days" readonly>
                                <span class="text-danger"><?php echo form_error('reportday[]'); ?></span>
                            </td>
                            <td>
                                <input type="text" name="reportdate_1" id="reportdate_1" placeholder="" class="form-control text-right report_date">
                                <span class="text-danger"><?php echo form_error('reportdate[]'); ?></span>
                            </td>
                            <td class="text-right">
                                <div class="input-group">
                                    <input type="text" class="form-control text-right right-border-none taxpercent" name="taxpercent_1" readonly id="taxpercent_1" autocomplete="off">
                                    <span class="input-group-addon"> %</span>
                                </div>
                              <input type="hidden" name="taxamount_1" id="taxamount_1" placeholder="" class="form-control text-right taxamount" readonly>
                            </td>
                            <td class="text-right">
                                <input type="text" name="amount_1" id="amount_1" placeholder="" class="form-control text-right amount" readonly>
                                <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                            </td>
                            <td>
                                <button type="button" class="closebtn delete_row" data-row-id="1" autocomplete="off"><i class="fa fa-remove"></i></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                 <a class="btn btn-info addplus-xs add-record mb10" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                </div>
                <div class="divider"></div>
                <div class="row">
                     <div class="col-sm-6">
                        <div class="row">
                            <div class="">
                                <div class="">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>
                                               <?php echo $this->lang->line('referral_doctor'); ?></label>
                                            <div>
                                                <select name='consultant_doctor' style="width:100%;" id="consultant_doctor" onchange="get_Docname(this.value)" class="form-control consultant_doctor select2" <?php
                                                if ($disable_option == true) {
                                                    echo "disabled";
                                                }
                                                ?> style="width:100%"  >
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($doctors as $dkey => $dvalue) { ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>" 
                                                    <?php if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
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
                                            <label><?php echo $this->lang->line('doctor_name'); ?></label>
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
                            <div><?php echo display_custom_fields('radiology'); ?></div>
                        </div>
                    </div>
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
                                <input type="text" placeholder="<?php echo $this->lang->line('discount'); ?>" name="discount_percent" id="discount_percent" class="form-control discount_percent" style="width: 70%; float: right;font-size: 12px;"  /></td>

                                <td class="text-right ipdbilltable">
                                <input type="text" placeholder="Discount" onkeyup="get_percentage(this.value)" value="0" name="discount" id="discount" style="width: 50%; float: right" class="form-control discount"/></td>
                            </tr>
                            <tr>
                                <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                <td></td>
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
                         <div class="row">
                        <div class="col-md-6">
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small> 
                            <input type="text" name="amount" id="payamount" class="form-control text-right">  
                            <span class="text-danger"></span>
                        </div>
                    </div>                   
                    </div>  
				<div class="row">                    
                    <div class="cheque_div" style="display: none;">
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
</div>