<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<input type="hidden" name="id" value="<?php echo $result['id'];?>">
<input type="hidden" name="post_patient_id" class="post_patient_id" value="<?php echo $result['patient_id'];?>">
<input type="hidden" name="post_patient_id" class="post_patient_name" value="<?php echo $result['patient_name'].' ('.$result['patient_id'].')';?>">
<input type="hidden" name="post_charge_type_id" class="post_charge_type_id" value="<?php echo $result['charge_type_id'];?>">
<input type="hidden" name="post_charge_category_id" class="post_charge_category_id" value="<?php echo $result['charge_category_id'];?>">
<input type="hidden" name="post_charge_id" class="post_charge_id" value="<?php echo $result['charge_id'];?>">
<input type="hidden" name="post_blood_donor_cycle_id" class="post_blood_donor_cycle_id"  value="<?php echo $result['blood_donor_cycle_id'];?>">
<input type="hidden" name="post_blood_group" class="post_blood_group" value="<?php echo $result['blood_group_id'];?>">
<input type="hidden" name="post_quantity" class="post_quantity" value="<?php echo $result['quantity'];?>">
<input type="hidden" name="post_bag_no" class="post_bag_no" value="<?php echo $this->customlib->bag_string($result['bag_no'],$result['volume'],$result['unit']);?>">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('issue_date'); ?></label>
                                <small class="req"> *</small>
                                <input type="text" name="date_of_issue" id="dates_of_issue" class="form-control datetime" value="<?php echo set_value('date_of_issue',$this->customlib->YYYYMMDDHisTodateFormat($result['date_of_issue'])); ?>">
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
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($doctors as $dkey => $dvalue) {
    ?>
                                            <option value="<?php echo $dvalue["id"]; ?>" <?php
                                        if ($result['hospital_doctor'] == $dvalue["id"]) {
                                            echo "selected";
                                        }
                                        ?>><?php echo composeStaffNameByString($dvalue["name"],$dvalue["surname"],$dvalue["employee_id"]); ?></option>
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
                                <input type="text" id="reference" name="reference" class="form-control" value="<?php echo $result['reference'];?>">
                                <span class="text-danger"><?php echo form_error('reference'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label> <?php echo $this->lang->line('technician'); ?></label>
                                <input type="text" name="technician" class="form-control" value="<?php echo $result['technician'];?>">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('blood_group'); ?></label>
                                <select style="width: 100%" class="form-control select2 blood_group" name="blood_group">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php
                                foreach ($stockbloodgroup as $blood_grp_value) {
                                    if($blood_grp_value['id'] == $result['blood_group_id']){
                                        $selected = 'selected';
                                    }else{
                                        $selected = '';
                                    } ?>
                                    <option value="<?php echo $blood_grp_value['id']; ?>" <?php echo $selected; ?>><?php echo $blood_grp_value['name']; ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('bag'); ?></label> <small class="req"> *</small>
                                <select style="width: 100%" class="form-control select2 bag_no" name="bag_no">
                                <option value=""><?php echo $this->lang->line('select'); ?> </option>
                                </select>
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
                                        <label><?php echo $this->lang->line('charge_name'); ?><small class="req"> *</small></label>
                                            <select name="charge_id" id="code" style="width: 100%" class="form-control addcharge select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                        <input type="text" readonly name="standard_charge" id="addstandard_charge" class="form-control" value="<?php echo set_value('standard_charge',$result['standard_charge']); ?>">
                                        <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3 hide">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('qty'); ?></label><small class="req"> *</small>
                                       <input type="text" name="qty" id="qty" class="form-control"  value="<?php echo set_value('qty',$result['quantity']); ?>" readonly>
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
                                                        <textarea name="note" rows="3" id="note" class="form-control"><?php echo set_value('note',$result['remark']) ?></textarea>
                                                    </div>
                                                </div>
                                                <div>
                                                    <?php
                                                    echo display_custom_fields('blood_issue', $result['id']);
                                                    ?>
                                                </div>
                                            </div>                                            
                                        </div><!--./col-sm-6-->
                                        <div class="col-sm-6">
                                            <table class="printablea4">
                                                <tr>
                                                    <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td width="60%" colspan="2" class="text-right ipdbilltable">
                                                    <input type="text" placeholder="Total"  name="total" value="<?php echo set_value('total',$result['amount']); ?>" id="total" style="width: 30%; float: right" class="form-control total" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-right ipdbilltable">
                                                        <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="<?php echo $this->lang->line('discount'); ?>" name="discount_percent" id="discount_percent" class="form-control discount_percent" value="<?php echo set_value('discount_percentage',$result['discount_percentage']); ?>" style="width: 70%; float: right;font-size: 12px;"/></td>
                                                    <td class="text-right ipdbilltable">
                                        <input type="text" placeholder="<?php echo $this->lang->line('discount'); ?>" value="<?php echo calculatePercent($result['amount'],$result['discount_percentage']); ?>" name="discount" id="discount" style="width: 50%; float: right" class="form-control discount"/></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                                      <td class="text-right ipdbilltable">
                                                        <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="tax_percentage" id="tax_percentage" value="<?php echo set_value('tax_percentage',$result['tax_percentage']); ?>" class="form-control tax_percentage" readonly style="width: 70%; float: right;font-size: 12px;"/></td>
                                                    <td class="text-right ipdbilltable">
                                                        <?php $total_tax_amount = $result['amount'] - calculatePercent($result['amount'],$result['discount_percentage']); ?>
                                                        <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="tax" value="<?php echo calculatePercent($total_tax_amount,$result['tax_percentage']);  ?>" id="tax" style="width: 50%; float: right" class="form-control tax" readonly/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td colspan="2" class="text-right ipdbilltable">
                                                        <input type="text" placeholder="Net Amount" value="<?php echo set_value('net_amount',$result['net_amount']); ?>" name="net_amount" id="net_amount" style="width: 30%; float: right" class="form-control net_amount" readonly/></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div><!--./row-->