<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">                      
                        <input name="pharmacy_bill_basic_id" id="pharmacy_bill_basic_id" type="hidden" class="form-control" value="<?php echo $bill['id']?>" />
                         <input name="case_reference_id" id="case_reference_id" type="hidden" class="form-control" value="<?php echo $bill['case_reference_id']?>" />   
 <input name="patient_id" id="patienteditid" type="hidden" class="form-control" value="<?php echo $bill['patient_id'] ?>" /> 
 <input name="customer_name" id="patienteditname" type="hidden" class="form-control" value="<?php echo $bill['customer_name'] ?>" />
 <input name="action_type" id="action_type" value="update" type="hidden" class="form-control"/>                         
                            <div class="row">
                                <div class="col-md-12">                                      
                                    <div class="table-responsive">
                                        <table class="table tableover table-striped table-bordered table-hover tablefull12 tblProducts" id="tableID">
                                            <thead>
                                            <tr class="font13">
                                                    <th width="12%"><?php echo $this->lang->line('medicine_category'); ?><small class="req" style="color:red;"> *</small></th>
                                                    <th width="12%"><?php echo $this->lang->line('medicine_name'); ?><small class="req" style="color:red;"> *</small></th>
                                                    <th width="10%"><?php echo $this->lang->line('batch_no'); ?> <small class="req" style="color:red;">*</small></th>
                                                    <th width="12%"><?php echo $this->lang->line('expiry_date'); ?><small class="req" style="color:red;"> *</small></th>
                                                    <th class="text-right" width="13%"><?php echo $this->lang->line('quantity'); ?><small class="req" style="color:red;"> *</small> <?php echo " | " . $this->lang->line('available_qty'); ?></th>
                                                    <th class="text-right" width="10%"><?php echo $this->lang->line('sale_price') . ' (' . $currency_symbol . ')'; ?><small class="req" style="color:red;"> *</small></th>
                                                    <th class="" width="7%"><?php echo $this->lang->line('tax'); ?></th>
                                                    <th class="text-right" width="15%"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?><small class="req" style="color:red;"> *</small></th>
                                                    <th class="text-right" width="2%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	<?php 

  $row_value=1;
foreach ($detail as $bill_detail_key => $bill_detail_value) {
	?>
        <input type="hidden" name="previous_ids[]" value="<?php echo $bill_detail_value['id'];?>">
<tr id="row<?php echo $row_value?>">
                                                <td width="160">
    <input type="hidden" name="insert_id_<?php echo $row_value;?>" value="<?php echo $bill_detail_value['id'];?>">    
    <input type="hidden" name="total_rows[]" value="<?php echo $row_value;?>">
    <input type="hidden" class="post_medicine_category_id" value="<?php echo $bill_detail_value['medicine_category_id'];?>">
    <input type="hidden" class="post_medicine_id" value="<?php echo $bill_detail_value['medicine_id'];?>">
    <input type="hidden" class="post_medicine_batch_detail_id" value="<?php echo $bill_detail_value['medicine_batch_detail_id'];?>">
    <input type="hidden" class="sale_price" value="<?php echo $bill_detail_value['sale_price'];?>">
    <input type="hidden" class="quantity" value="<?php echo $bill_detail_value['quantity'];?>">
         <select class="form-control medicine_category select3" style="width:100%" name='medicine_category_id_<?php echo $row_value;?>'>
         <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <?php foreach ($medicineCategory as $med_cat_key => $med_cat_value) {
                        ?>
         <option value="<?php echo $med_cat_value["id"]; ?>" <?php echo set_select('medicine_category_id_<?php echo $row_value;?>', $med_cat_value['id'], ($med_cat_value["id"] == $bill_detail_value['medicine_category_id']) ? TRUE :FALSE); ?>><?php echo $med_cat_value["medicine_category"] ?>
                          </option> 
                      <?php 
                  }
                      ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('medicine_category_id[]'); ?>
                                                    </span>
                                                </td>
                                                <td width="24%">
                                                    <select class="form-control select3 medicine_name" style="width:100%"  id="medicine_name<?php echo $row_value;?>" name='medicine_name_id_<?php echo $row_value;?>'>
                    <option value=""><?php echo $this->lang->line('select') ?>
                                                        </option>                                                       
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('medicine_name[]'); ?></span>
                                                </td>
                                                <td width="16%">
                                    <select class="form-control batch_no select3" id="batch_no<?php echo $row_value;?>" name="batch_no_id_<?php echo $row_value;?>" >
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                    </select>
                                                    <span class="text-danger"><?php echo form_error('batch_no[]'); ?></span>
                                                </td>
                                                <td width="8%">
                                                    <input type="text" readonly="" name="expire_date_<?php echo $row_value;?>"  id="expire_date<?php echo $row_value;?>" class="form-control exp_date"/>
                                                </td>
                                                <td>
                                                <div class="input-group">
                                                <input type="text" name="quantity_<?php echo $row_value;?>" id="quantity<?php echo $row_value;?>" data-rowid="<?php echo $row_value; ?>" class="form-control text-right edit_refund_qty qty">
                                                 <input type="hidden" id="valid_reund_quantity<?php echo $row_value;?>" class="form-control text-right  qty">
                                                <span class="input-group-addon text-danger available_qty" style="font-size: 10pt"  id="totalqty0">&nbsp;&nbsp;</span> 
                                                    </div>
                                                    <input type="hidden" class="available_quantity" name="available_quantity_<?php echo $row_value;?>" id="available_quantity0">
                                                </td>
                                                <td class="text-right">
                                                    <input type="text" name="sale_price_<?php echo $row_value;?>" id="sale_price<?php echo $row_value;?>"  class="form-control text-right price" readonly>
                                                    <span class="text-danger"><?php echo form_error('sale_price[]'); ?></span>
                                                </td>
                                                <td class="text-right">                                                    
                                                            <div class="input-group">
                                                            <input type="text" class="form-control right-border-none medicine_tax" name="tax_<?php echo $row_value;?>" readonly id="tax<?php echo $row_value;?>"  autocomplete="off">
                                                            <span class="input-group-addon "> %</span>
                                                            </div>                                                    
                                                </td>
                                                <td class="text-right">
                                                    <input type="text" name="amount_<?php echo $row_value;?>" id="amount<?php echo $row_value;?>" placeholder="" class="form-control text-right subtot" readonly>
                                                    <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                                                </td>
                                                <td><button type="button"  class="closebtn editdelete_row" data-row-id="<?php echo $row_value;?>" autocomplete="off"><i class="fa fa-remove"></i></button></td>
                                            </tr>
	<?php 
    $row_value++;
}
                                            	 ?>                                            
                                        </tbody>
                                        </table>
                                        <a class="btn btn-info addplus-xs add-record mb10" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add')?></a>  
                                    </div>
                                    <div class="divider"></div>                                
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputFile">
                                                                    <?php echo $this->lang->line('hospital_doctor'); ?></label>
                                                                <div>
    <select name='consultant_doctor' style="width:100%;" id="consultant_doctor" class="form-control select3" <?php
if ($disable_option == true) {
    echo "disabled";
}
?> style="width:100%"  >
                                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                                        <?php foreach ($doctors as $dkey => $dvalue) {
    ?>
                                                                            <option value="<?php echo $dvalue["id"]; ?>" <?php
if ((isset($doctor_select)) && ($doctor_select == $dvalue["id"])) {
        echo "selected";
    }
    ?>><?php echo composeStaffNameByString($dvalue["name"], $dvalue["surname"],$dvalue["employee_id"]); ?></option>
<?php }?>
                                                                    </select>
                                                                </div>
                                                                <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                <label><?php echo $this->lang->line('doctor_name'); ?></label>
                <input name="doctor_name" id="doctname" type="text" class="form-control" value="<?php echo $bill['doctor_name']?>" />
                                                                <span class="text-danger"><?php echo form_error('doctor_name'); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('note'); ?></label>
                                                        <textarea name="note" rows="3" id="note" class="form-control"><?php echo $bill['note']; ?></textarea>
                                                    </div>
                                                </div>
                                                 <div>
                                                <?php
                                                echo $custom_fields_value ;
                                                ?>
                                                </div> 
                                            </div>
                                        </div><!--./col-sm-6-->

                                        <div class="col-sm-6">
                                            <table class="printablea4">
                                                <tr>
                                                    <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                            <td width="60%" colspan="2" class="text-right ipdbilltable">
                <input type="text" placeholder="Total" value="<?php echo $bill['total'];?>" name="total" id="total" style="width: 30%; float: right" class="form-control total"/></td>
                                                </tr>
                                                <tr>
                                                 <th><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-right ipdbilltable">
                                                        <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="<?php echo $this->lang->line('discount'); ?>" name="discount_percent" id="discount_percent" class="form-control discount_percent" value="<?php echo $bill['discount_percentage'] ?>" style="width: 70%; float: right;font-size: 12px;"/></td>
                                         <td class="text-right ipdbilltable">
                                        <input type="text" placeholder="<?php echo $this->lang->line('discount'); ?>" onkeyup="get_percentage(this.value)" value="<?php echo $bill['discount'];?>" name="discount" id="discount" style="width: 50%; float: right" class="form-control discount"/></td>
                                                </tr> 
                                                <tr>
                                                    <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-right ipdbilltable"> </td>
                                                    <td class="text-right ipdbilltable">
                                                        <input type="text" placeholder="<?php echo $this->lang->line('tax'); ?>" name="tax" value="<?php echo $bill['tax'] ?>" id="tax" style="width: 50%; float: right" class="form-control tax"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td colspan="2" class="text-right ipdbilltable">
                                                        <input type="text" onchange="amount_settlement(this.value)" placeholder="<?php echo $this->lang->line('net_amount'); ?>" value="<?php echo $bill['net_amount'] ?>" name="net_amount" id="net_amount" style="width: 30%; float: right" class="form-control net_amount"/></td>
                                                </tr>
                                            </table>
                                            <hr>
                                            <h4><?php echo $this->lang->line('refund_amount'); ?></h4>                                            
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
                                        <label><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?></label> 
                                        <input type="text" name="payment_amount" id="payment_refund_amount" class="form-control payment_refund_amount text-right">  
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
                                        <input type="file" class="filestyle form-control"   name="document">
                                        <span class="text-danger"><?php echo form_error('document'); ?></span> 
                                    </div>
                                </div> 
                              </div>                                
                            </div>
                                        </div>
                                    </div><!--./row-->
                                </div><!--./col-md-12-->
                            </div><!--./row-->
                        </div><!--./box-footer-->
                    </div><!--./col-md-12-->