<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat(); 
?>
<input type="hidden" name="id" value="<?php echo set_value('id',$result->id); ?>">
<input type="hidden" value="<?php echo $result->charge_category_id?>" name="post_charge_category_id">
<input type="hidden" value="<?php echo $result->charge_id?>" name="post_charge_id">
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label><?php echo $this->lang->line('test_name'); ?></label>
                    <small class="req"> *</small> 
                    <input type="text" name="test_name" class="form-control" value="<?php echo set_value('test_name',$result->test_name); ?>">
                    <span class="text-danger"><?php echo form_error('test_name'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label><?php echo $this->lang->line('short_name'); ?></label>
                    <small class="req"> *</small> 
                    <input type="text" name="short_name" class="form-control" value="<?php echo set_value('short_name',$result->short_name); ?>">
                    <span class="text-danger"><?php echo form_error('short_name'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label><?php echo $this->lang->line('test_type'); ?></label>                   
                    <input type="text" name="test_type" class="form-control" value="<?php echo set_value('test_type',$result->test_type); ?>">
                    <span class="text-danger"><?php echo form_error('test_type'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputFile">
                        <?php echo $this->lang->line('category_name'); ?></label>
                    <small class="req"> *</small> 
                    <div>
                        <select class="form-control select2" style="width: 100%" name='radiology_category_id'>
                            <option value=""><?php echo $this->lang->line('select') ?></option>
                            <?php foreach ($categoryName as $dkey => $dvalue) {
                                ?>
                                <option value="<?php echo $dvalue["id"]; ?>" <?php echo set_select('radiology_category_id', $dvalue["id"],($result->radiology_category_id == $dvalue["id"]) ? true :false); ?>><?php echo $dvalue["lab_name"] ?></option>   
                            <?php } ?>
                        </select>
                    </div>
                    <span class="text-danger"><?php echo form_error('radio_category_id'); ?></span>
                </div>
            </div>  
        </div>
        <div class="row">    
            <div class="col-sm-3">
                <div class="form-group">
                    <label><?php echo $this->lang->line('sub_category'); ?></label>
                    <input type="text" name="sub_category" class="form-control" value="<?php echo set_value('sub_category',$result->sub_category); ?>">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label><?php echo $this->lang->line('report_days'); ?></label><small class="req"> *</small>
                    <input type="number" min="0" name="report_days" class="form-control" value="<?php echo set_value('report_days',$result->report_days); ?>">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputFile"><?php echo $this->lang->line('charge_category'); ?></label>
                    <small class="req">*</small> 
                    <div>
                        <select class="form-control charge_category select2" name='charge_category_id' style="width: 100%;">
                            <option value="<?php echo set_value('charge_category_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                            <?php foreach ($charge_category as $charge_cat_key => $charge_cat_value) {
                                ?>
                                <option value="<?php echo $charge_cat_value["id"]; ?>"  <?php echo set_select('charge_category_id', $charge_cat_value["id"],($result->charge_category_id == $charge_cat_value["id"]) ? true :false); ?>><?php echo $charge_cat_value["name"]; ?></option>  
                            <?php } ?>
                        </select>
                    </div>
                    <span class="text-danger"><?php echo form_error('charge_category_id'); ?></span>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputFile"><?php echo $this->lang->line('code'); ?></label>
                    <small class="req">*</small> 
                    <div>
                        <select class="form-control charge select2" name='code' style="width: 100%" id="code">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                        </select>
                    </div>
                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                </div>
            </div> 
             <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputFile"><?php echo $this->lang->line('tax'); ?> (%)<small class="req"> *</small></label>
                    <div>
                        <input class="form-control" name='tax' id="tax" value="<?php echo set_value('tax',$result->percentage); ?>" readonly="true">
                    </div>
                </div>
            </div> 
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputFile"><?php echo $this->lang->line('standard_charge'); ?></label><?php echo ' (' . $currency_symbol . ')'; ?>
                    <small class="req">*</small> 
                    <div>
                        <input class="form-control" name='standard_charge' id="standard_charge" value="<?php echo set_value('standard_charge',$result->standard_charge); ?>" readonly="true">
                    </div>  
                </div>
            </div> 
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="exampleInputFile"><?php echo $this->lang->line('amount').' ('.$currency_symbol.')'; ?></label>
                    <small class="req">*</small> 
                    <div>
                        <input readonly value="<?php echo set_value('standard_charge',amountFormat($result->standard_charge+calculatePercent($result->standard_charge,$result->percentage))); ?>" class="form-control" name='amount' id="amount" >
                    </div>
                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                </div>
            </div>
            <?php echo display_custom_fields('radiologytest', $result->id);?>
        </div><!--./row-->   
    </div><!--./col-md-12-->   
    <div class="divider"></div>
    <div class="col-md-12">        
        <table class="table table-striped table-bordered table-hover mb0" id="tableID">
            <thead>
                <tr class="font13">
                    <th><?php echo $this->lang->line('test_parameter_name'); ?><small class="req"> *</small></th>
                    <th><?php echo $this->lang->line('reference_range'); ?><small class="req"> *</small></th>
                    <th><?php echo $this->lang->line('unit') ; ?><small class="req"> *</small></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    if(!empty($result->radiology_parameter)){
                    $total_rows=1;
                    foreach ($result->radiology_parameter as $param_key => $param_value) {
                ?>
                <input type="hidden" name="prev_inserted[]" value="<?php echo $param_value->id;?>">
                <tr id="row<?php echo $total_rows;?>">
                    <td width="35%">
                        <input type="hidden" name="total_rows[]" value="<?php echo $total_rows;?>">
                        <input type="hidden" name="inserted_id_<?php echo $total_rows;?>" value="<?php echo $param_value->id;?>">
                        <input type="hidden" class="post_parameter_id" name="post_parameter_id" value="<?php echo $param_value->radiology_parameter_id;?>">
                        <select class="form-control select2 radiology_parmeter" style="width:100%"id="parameter_name_<?php echo $total_rows;?>" name='parameter_name_<?php echo $total_rows;?>' >
                               <option value="<?php echo set_value('radiology_parameter_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                                <?php foreach ($parametername as $dkey => $dvalue) {                                    ?>
                        <option value="<?php echo $dvalue["id"]; ?>" <?php echo set_select('parameter_name_'.$total_rows, $dvalue["id"],($param_value->radiology_parameter_id == $dvalue["id"]) ? true :false); ?>><?php echo $dvalue["parameter_name"] ?></option>   
                                <?php } ?> 
                        </select>
                    </td>
                    <td width="30%">
                        <input type="text" readonly="" name="reference_range_<?php echo $total_rows;?>"  id="reference_range_<?php echo $total_rows;?>" class="form-control reference_range">
                    </td>
                    <td width="30%">
                        <input type="text" readonly="" name="radio_unit_<?php echo $total_rows;?>"  id="radio_unit_" class="form-control radio_unit">
                    </td>
                    <td>
                          <?php if ($this->rbac->hasPrivilege('radiology_parameter', 'can_delete')) { ?>
                       <button type="button"  class="closebtn delete_row"><i class="fa fa-remove"></i></button>
                   <?php } ?>
                    </td>
                </tr>
            <?php
                $total_rows++;
                }
                }
            ?>
            </tbody>
        </table>
        <a class="btn btn-info addplus-xs btn-sm add-record mb10" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></a>
    </div><!--./col-md-12-->     
</div><!--./row-->