<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?> 

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('issue_date'); ?></label>
                                <small class="req"> *</small>
                                <input type="text" name="date_of_issue" id="dates_of_issue" class="form-control datetime">
                                <span class="text-danger"><?php echo form_error('date_of_issue'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="exampleInputFile">
                                    <?php echo $this->lang->line('hospital_doctor'); ?></label>
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
    ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"]. " (". $dvalue['employee_id'].")"; ?></option>
<?php }?>
                                    </select>
                                </div>
                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('reference_name'); ?></label>
                                <small class="req">*</small>
                                <input type="text" id="reference" name="reference" class="form-control">
                                <span class="text-danger"><?php echo form_error('reference'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label> <?php echo $this->lang->line('technician'); ?></label>
                                <input type="text" name="technician" class="form-control">
                            </div>
                        </div>
                           <div class="col-sm-3">
                            <div class="form-group">
                                <label> <?php echo $this->lang->line('blood_group'); ?></label>
                                <select  style="width: 100%" class="form-control select2 blood_group"  name="blood_group" id="component_blood_group">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
								foreach ($stockbloodgroup as $blood_grp_value) {
								    ?>
								    <option value="<?php echo $blood_grp_value['id']; ?>"><?php echo $blood_grp_value['name']; ?></option>
								<?php
								}
								?>
								</select>
                             </div>
                        </div> 
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('components'); ?></label>
                                <select style="width: 100%" class="form-control select2 component_issue" id="blood_component" name="blood_component">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>                                   
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('bag'); ?></label><small class="req"> *</small>
                                <select style="width: 100%" class="form-control select2 bag_no_issue"  name="bag_no">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                </select>
                            </div>
                        </div>


                       <!--  <div class="col-sm-3">
                            <div class="form-group">
                            <div class="form-check pt20">
                                <input class="form-check-input" type="checkbox" value="1" id="is_tpa" name="is_tpa"  onclick="reset_all()">
                                <label class="form-check-label" for="is_tpa">Apply TPA --r</label>
                            </div> 
                            </div>                            
                        </div> 


                        <div class="col-sm-1">
                            <div class="form-group">
                            </div>
                        </div>
                         -->
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
                                        <label><?php echo $this->lang->line('charge_name'); ?></label><small class="req"> *</small>
                                            <select name="charge_id" id="code" style="width: 100%" class="form-control addcharge select2 ">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                        <input type="text" name="standard_charge" id="addstandard_charge" class="form-control" value="<?php echo set_value('standard_charge'); ?>">
                                        <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3 hide">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('qty'); ?></label><small class="req"> *</small>
                                       <input type="text" name="qty" id="qty" class="form-control" >
                                        <span class="text-danger"><?php echo form_error('qty'); ?></span>
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
                                                <div>
                                                  <?php echo display_custom_fields('component_issue'); ?>
                                                </div>
                                            </div>                                            
                                        </div><!--./col-sm-6-->
                                        <div class="col-sm-6">
                                            <table class="printablea4">
                                                <tr>
                                                    <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td width="60%" colspan="2" class="text-right ipdbilltable">
                                                    <input type="text" placeholder="Total" value="0" name="total" id="total" style="width: 30%; float: right" class="form-control total" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-right ipdbilltable">
                                                        <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="Discount" name="discount_percent" id="discount_percent" value="0" class="form-control discount_percent" style="width: 70%; float: right;font-size: 12px;"/></td>
                                                    <td class="text-right ipdbilltable">
                                        <input type="text" placeholder="Discount" value="0" name="discount" id="discount" style="width: 50%; float: right" class="form-control discount"/></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                                      <td class="text-right ipdbilltable">
                                                        <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="tax_percentage" id="tax_percentage" class="form-control tax_percentage" readonly style="width: 70%; float: right;font-size: 12px;"/></td>
                                                    <td class="text-right ipdbilltable">
                                                        <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="tax" value="0" id="tax" style="width: 50%; float: right" class="form-control tax" readonly/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td colspan="2" class="text-right ipdbilltable">
                                                        <input type="text" placeholder="Net Amount" value="0" name="net_amount" id="net_amount" style="width: 30%; float: right" class="form-control net_amount" readonly/></td>
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
                                        <input type="text" name="cheque_date" id="cheque_date" class="form-control date" readonly="readonly">
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