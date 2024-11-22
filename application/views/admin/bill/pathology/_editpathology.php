<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
  <div class="row">        
    <div class="col-lg-12 col-md-12 col-sm-12">       
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table tableover table-striped table-bordered table-hover tablefull12 tblProducts mb5" id="tableID">
                                        <thead>
                                            <tr class="font13">
                                                <th width="15%"><?php echo $this->lang->line('test_name'); ?><small class="req" style="color:red;"> *</small></th>
                                                <th width="10%"><?php echo $this->lang->line('report_days'); ?></th>
                                                <th width="15%"><?php echo $this->lang->line('report_date'); ?><small class="req" style="color:red;"> *</small></th>
                                                <th class="text-right" width="7%"><?php echo $this->lang->line('tax'); ?>(%)</th>
                                                <th class="text-right" width="10%"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                                <th class="text-right" width="2%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="addrows">
                                        <?php 
                                        if(!empty($pathology_data->pathology_report)){
                                            $total_rows=1;
                                            foreach ($pathology_data->pathology_report as $report_key => $report_value) {
                                            ?>
                                            <input type="hidden" name="prev_reports[]" value="<?php echo  $report_value->id;?>">
                                            <?php
                                            ?>
                                            <tr id="row<?php echo $total_rows;?>">
                                           <td>
                                            <input type="hidden" name="total_rows[]" value="<?php echo $total_rows;?>">
                                            <input type="hidden" name="inserted_id_<?php echo $total_rows;?>" value="<?php echo  $report_value->id;?>">
                                                <select class="form-control test_name select2" style="width:100%"  name='test_name_<?php echo $total_rows;?>'>
                                                    <option value="<?php echo set_value('test_name_id'); ?>"><?php echo $this->lang->line('select') ?>
                                                    </option>
                                                <?php foreach ($testlist as $dkey => $dvalue) { ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>" <?php echo set_select('test_name_'.$total_rows,$dvalue["id"], ($report_value->pathology_id == $dvalue["id"]) ? TRUE : FALSE); ?>><?php echo $dvalue["test_name"]." (".$dvalue["short_name"].")"; ?>
                                                        </option>
                                                    <?php }?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('test_name_id[]'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <input type="text" name="reportday_<?php echo $total_rows;?>" id="reportday_<?php echo $total_rows;?>" value="<?php echo $report_value->report_days; ?>"  class="form-control text-right days" readonly>
                                                <span class="text-danger"><?php echo form_error('reportday[]'); ?></span>
                                            </td>
                                            <td>
                                                <input type="text" name="reportdate_<?php echo $total_rows;?>" id="reportdate_<?php echo $total_rows;?>" value="<?php echo $this->customlib->YYYYMMDDTodateFormat($report_value->reporting_date); ?>"  class="form-control text-right report_date" >
                                                <span class="text-danger"><?php echo form_error('reportdate[]'); ?></span>
                                            </td>
                                            <td class="text-right">
                                                        <div class="input-group">
                                                        <input type="text" class="form-control text-right right-border-none taxpercent"  name="taxpercent_<?php echo $total_rows;?>" readonly value="<?php echo $report_value->tax_percentage; ?>" id="taxpercent_<?php echo $total_rows;?>"  autocomplete="off">
                                                        <span class="input-group-addon "> %</span>                                                    
                                                </div>
                                              <input type="hidden" name="taxamount_<?php echo $total_rows;?>" id="taxamount_<?php echo $total_rows;?>" value="<?php echo $report_value->apply_charge*$report_value->tax_percentage/100 ;?>" placeholder="" class="form-control text-right taxamount" readonly>
                                            </td>
                                            <td class="text-right">
                                                <input type="text" name="amount_<?php echo $total_rows;?>" id="amount_<?php echo $total_rows;?>"  value="<?php echo $report_value->apply_charge; ?>" class="form-control text-right amount" readonly>
                                                <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                                            </td>
                                            <td>
                                                 <button type="button"  class="closebtn delete_rows" data-row-id="<?php echo $total_rows;?>" autocomplete="off"><i class="fa fa-remove"></i></button>
                                            </td>
                                        </tr>
                                        <?php 
                                        $total_rows++;
                                        }
                                        }
                                         ?>                                       
                                        </tbody>                                        
                                    </table>                                   
                                     <a class="btn btn-info addplus-xs add-record mb10" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                </div>
                                <div class="divider"></div>
                                     <div class="col-sm-6">
                                        <div class="row">
                                            <div class="">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputFile">
                                                               <?php echo $this->lang->line('referral_doctor'); ?></label>
                                                            <div>
                                                                <select name='consultant_doctor' style="width:100%;" id="consultant_doctor" onchange="get_Docname(this.value)" class="form-control select2" style="width:100%">
                                                         <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($doctors as $dkey => $dvalue) {
                                                        ?>
                                                                <option value="<?php echo $dvalue["id"]; ?>" <?php
                                                        if ((isset($pathology_data->doctor_id)) && ($pathology_data->doctor_id == $dvalue["id"])) {
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
                                                            <input name="doctor_name" id="doctname" type="text" value="<?php echo $pathology_data->doctor_name;?>" class="form-control"/>
                                                            <span class="text-danger"><?php echo form_error('doctor_name'); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('note'); ?></label>
                                                    <textarea name="note" rows="3" id="note" class="form-control"><?= $pathology_data->note; ?></textarea>
                                                </div>
                                            </div>
                                             <div class="row">
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
                                                <input type="text" placeholder="Total" value="<?php echo $pathology_data->total; ?>" name="total" id="total" style="width: 30%; float: right" class="form-control total"/></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                                <td class="text-right ipdbilltable">
                                                    <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                            <input type="text" placeholder="Discount" name="discount_percent" id="discount_percent" value="<?php echo $pathology_data->discount_percentage; ?>" onchange="addTotal()" class="form-control discount_percent" style="width: 70%; float: right;font-size: 12px;"/></td>
                                                <td class="text-right ipdbilltable">
                                        <input type="text" placeholder="Discount" value="<?php echo $pathology_data->discount; ?>" name="discount" id="discount" style="width: 50%; float: right" class="form-control discount"/></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                                <td></td>
                                                <td class="text-right ipdbilltable">
                                                    <input type="text" placeholder="Tax" name="tax" value="<?php echo $pathology_data->tax; ?>" id="tax" style="width: 50%; float: right" class="form-control tax"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                <td colspan="2" class="text-right ipdbilltable">
                                                    <input type="text" placeholder="Net Amount" value="<?php echo $pathology_data->net_amount; ?>" name="net_amount" id="net_amount" style="width: 30%; float: right" class="form-control net_amount"/></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div><!--./row-->
                            </div><!--./col-md-12-->
                        </div><!--./row-->
                    </div><!--./box-footer-->