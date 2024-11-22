<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
if (!empty($charges_detail)) {
?> 
 <div class="box-tools pull-right box-miustop">
     <?php if ($this->rbac->hasPrivilege('ipd_billing_payment', 'can_add')) {?>
        <a href="javascript:void(0);" data-loading-text='' data-caseid='<?php echo $charges_detail[0]["case_reference_id"]; ?>' data-module='ipd' data-totalamount='' data-record-id='<?php echo $charges_detail[0]["ipd_id"]; ?>'  data-toggle='tooltip' title=''    class="btn btn-primary btn-sm add_payment"  ><i class="fa fa-money"></i> <?php echo $this->lang->line('add_payment'); ?></a>
         <?php } if ($this->rbac->hasPrivilege('ipd_billing_payment', 'can_view')) {?>
         <a href="javascript:void(0);" data-loading-text='' data-case_id='<?php echo $charges_detail[0]["case_reference_id"]; ?>' data-module_type='ipd_id' data-record-id='<?php echo $charges_detail[0]["ipd_id"]; ?>'  data-toggle='tooltip' title=''    class="btn btn-primary btn-sm view_payment"  ><i class="fa fa-money"></i> <?php echo $this->lang->line('view_payments'); ?></a> 
          <?php }if ($this->rbac->hasPrivilege('generate_bill', 'can_view')) {?>
        <a href="javascript:void(0);"   class="btn btn-primary btn-sm text-right view_generate_bill"  data-toggle="tooltip" title="" data-module_type='ipd_opd' data-case_id='<?php echo $charges_detail[0]["case_reference_id"]; ?>'><i class="fas fa-exchange-alt"></i> <?php echo $this->lang->line('generate_bill'); ?></a>
    <?php }?>
 </div><!--./impbtnview20-->
 <?php } ?>
                                
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover example"> 
                                        <thead>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                                        <th><?php echo $this->lang->line('charge_category'); ?></th>
                                        <th><?php echo $this->lang->line('qty'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('applied_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')';?></th>
                                        <th class="text-right"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')';?></th>
                                        <th class="text-right"><?php echo $this->lang->line('tax'); ?></th>                      
                                        <th class="text-right"><?php echo $this->lang->line('amount'). ' (' . $currency_symbol . ')'; ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('action') ?></th>
                                        </thead>
                                        <tbody>
                                         <?php 
                                            $total =$amount=$total_tax =0;$tax=0;
                                            if (!empty($charges_detail)) {
                                                foreach ($charges_detail as $charges_key => $charges_value) {
													$discount_amount = calculatePercent($charges_value['apply_charge'],$charges_value['discount_percentage']);
													$total += $charges_value["apply_charge"];
                                                    $amount += $charges_value["amount"];
                                                     $tax=0;
                                                    if($charges_value["tax"] > 0){                                                    

                                                        $tax_raw=($charges_value["apply_charge"]-(($charges_value["apply_charge"]*$charges_value["discount_percentage"])/100));
                                                        $tax=(($tax_raw*$charges_value["tax"])/100);
                                                    }
                                                    ?>  
                                                    <tr>                                                       
                                                        <td><?php if($charges_value['date']!='' && $charges_value['date']!='0000-00-00'){ echo $this->customlib->YYYYMMDDHisTodateFormat($charges_value['date'], $this->customlib->getHospitalTimeFormat()); } ?></td>
                                                        <td class="">
                                                            <?php echo $charges_value["name"]; ?>
                                                             <div class="bill_item_footer text-muted"> <?php echo $charges_value["note"]; ?></div>
                                                        </td>
                                                        <td class="text-capitalize"><?php echo $charges_value["charge_type"] ?></td>
                                                        <td class="text-capitalize"><?php echo $charges_value["charge_category_name"] ?></td>
                                                        <td class="text-capitalize"><?php echo $charges_value['qty']." ".$charges_value["unit"]; ?></td>		   
                                                        <td class="text-right"><?php echo $charges_value["standard_charge"] ?></td>
                                                        <td class="text-right"><?php echo number_format($charges_value["apply_charge"], 2) ?></td>
                                                        <td class="text-right"><?php echo $charges_value["tpa_charge"] ?></td>
                                                        <td class="text-right"><?php echo $discount_amount." (".$charges_value["discount_percentage"]."%) "; ?></td>				
                                                        <td class="text-right"><?php echo amountFormat($tax)." (".$charges_value["tax"]."%)"; ?></td>
                                                        <td class="text-right"><?php echo amountFormat($charges_value["amount"]) ?></td>
                                                        <td class="text-right"><a href="javascript:void(0);" class="btn btn-default btn-xs print_charge" data-toggle="tooltip" title="" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>"  data-record-id="<?php echo $charges_value['id']; ?>" data-moduletype='ipd'  data-original-title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></a>                                                    
                                                        </td>                                                      
                                                    </tr>
                                                    <?php
                                                } 
                                            }
                                             $total_tax+=$tax;
                                            ?> 

                                        </tbody>
                                         <?php if (!empty($charges_detail)) { ?>
                                        <tr class="box box-solid total-bg"> 
                                            <td colspan='8' class="text-right">                                  
                                            </td>                                           
                                            <td></td>                                
                                            <td></td>
                                            <td class="text-right"><?php echo $this->lang->line('total') . " : " . $currency_symbol . "" . amountFormat($amount) ?></td>
											<td></td>  
                                        </tr> 
                                        <?php }else{ ?>
                                            <tr > 
                                            <td colspan='12' class="text-center"><div class="alert alert-danger"><?php echo $this->lang->line('no_record_found'); ?></div> </td>
                                            </tr>
                                         <?php  } ?>
                                    </table>
                                </div>                                