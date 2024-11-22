<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
<div class="print-area">
<div class="row"> 
        <div class="col-12">
              <?php if (!empty($print_details[0]['print_header'])) { ?>
                        <div class="pprinta4">
                            <img src="<?php
                            if (!empty($print_details[0]['print_header'])) {
                                echo base_url() . $print_details[0]['print_header'].img_time();
                            }
                            ?>" class="img-responsive" style="height:100px; width: 100%;">
                        </div>
                    <?php } ?>
              <div class="card">
                <div class="card-body">  
                    <div class="row">
                        <div class="col-md-6"> 
                            <p><?php echo $this->lang->line('patient'); ?> : <?php echo composePatientName($patient['patient_name'],$patient['patient_id']); ?></p>
                            <p><?php echo $this->lang->line('case_id'); ?> : <?php echo $case_id; ?></p>                            
                        </div>
                        <div class="col-md-6 text-right text-rtl-left">
                          <?php 
                          if($patient['date']!==''){
                            ?>
                            <p><span class="text-muted"><?php echo $this->lang->line('admission_date'); ?> :  </span> <?php echo $this->customlib->YYYYMMDDTodateFormat($patient['date']); ?></p>
                            <?php
                          }
                          ?> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="print-table">
                                 <thead>
                                    <tr class="line">
                                       <td>#</td>
                                       <td><?php echo $this->lang->line('description'); ?></td>
                                    	 <td><?php echo $this->lang->line('qty'); ?></td>
                                    	 <td width="12%" class="text-right"><?php echo $this->lang->line('tax'); ?></td>
                                    	 <td width="12%" class="text-right"><?php echo $this->lang->line('discount'); ?></td>
                                       <td class="text-right text-rtl-left"><?php echo $this->lang->line('amount'). "(".$currency_symbol.")"; ?></td>
                                    </tr>
                                 </thead>
                                 <tbody> 
                                 	<?php $total_tax=$apply_charge=$amount=0;$s=1; foreach($charge_details as $key=>$value){
                                    if($value['tax']>0){
                                      $tax=(($value['apply_charge']*$value['tax'])/100);
                                    }else{
                                      $tax=0;
                                    }                                    
                                 		?>                                 		
                                    <tr>
                                       <td><?php echo $s++;?></td>
                                       <td><?php echo $value['charge_name'] ?><br>
                                        <?php echo $value['note'];?>
                                      </td>
                                      <td ><?php echo $value['qty'] ?></td>
                                       <td class="text-right" ><?php echo amountFormat($tax).' ('.$value['tax'].'%)'; ?></td>
                                       <td class="text-right" ><?php echo ($value['apply_charge']* $value['discount_percentage'])/100 .' ('.$value['discount_percentage'].'%)'; ?></td>
                                       <td class="text-right text-rtl-left"><?php echo amountFormat($value['amount']) ?></td>
                                     </tr>
                                <?php
                                $apply_charge+=$value['apply_charge'];
                                $amount+=$value['amount'];
                                $total_tax+=$tax;
                                 } ?>
                                     <tr>
                                       <td colspan="5" class="text-right thick-line text-rtl-left"><?php echo $this->lang->line('net_amount'); ?></td>
                                       <td class="text-right thick-line text-rtl-left"><?php echo $currency_symbol.amountFormat($apply_charge);?></td>
                                    </tr>
                                     <tr> 
                                       <td colspan="5" class="text-right no-line text-rtl-left"><?php echo $this->lang->line('tax'); ?></td>
                                       <td class="text-right no-line text-rtl-left"> <?php echo $currency_symbol.amountFormat($total_tax); ?></td>
                                    </tr>
                                    <tr>
                                       <td colspan="5" class="text-right no-line text-rtl-left"><?php echo $this->lang->line('total'); ?></td>
                                       <td class="text-right text-rtl-left"> <?php echo $currency_symbol.amountFormat($amount);?></td>
                                    </tr>
                                     <tr>
                                        <td colspan="5" class="text-right no-line text-rtl-left"><?php echo $this->lang->line('paid'); ?></td>
                                       <td class="text-right no-line text-rtl-left"><?php echo $currency_symbol.amountFormat($paid_amount['total_pay'])?></td>
                                    </tr>                                    
                                    <tr>
                                        <td colspan="5" class="text-right no-line text-rtl-left"><?php echo $this->lang->line('due'); ?>:</td>
                                        <td class="text-right text-rtl-left"><?php 
                                        if($refund>0){                                        
                                         echo $balance=amountFormat($refund+($amount-$paid_amount['total_pay']));
                                        }else{
                                          echo $currency_symbol.amountFormat($amount-$paid_amount['total_pay']);
                                        }
                                        ?></td>
                                    </tr>
                                    <?php if(($paid_amount['total_pay']>$amount) || ($refund>0)){
                                      ?>
                                      <tr>
                                       <td colspan="5" class="text-right no-line text-rtl-left" ><?php echo $this->lang->line('refund'); ?>:</td>
                                       <td class="text-right text-rtl-left" id="replace_amount"><?php if($refund>0){  echo $currency_symbol.amountFormat($refund); } ?></td>
                                    </tr>
                                    <tr>
                                       <td colspan="5" class="text-right no-line text-rtl-left"></td>
                                       <td class="text-right text-rtl-left"><a href="javascript:void(0);" style="margin-right:2px;" data-loading-text='' class="btn btn-primary btn-sm payment_refund"  ><i class="fa fa-money"></i> <?php echo $this->lang->line('add_refund'); ?></a></td>
                                    </tr>
                                      <?php
                                    }
                                    if(!empty($discharge_card)){
                                      ?>
                                       <tr>
                                       <td colspan="5" class="text-right no-line text-rtl-left"></td>
                                       <td class="text-right text-rtl-left"><a href="javascript:void(0);" data-loading-text='' class="btn btn-primary btn-sm view_dischargecard" data-type="bill" data-recordId="<?php echo $discharge_card['id']; ?>" data-case_id="<?php echo $case_id; ?>"  ><i class="fa fa-money"></i> <?php echo $this->lang->line('generate_discharge_card'); ?></a></td>                                       
                                    </tr> 
                                      <?php
                                    }
                                    ?>                                    
                                 </tbody>
                              </table>
                            </div>  
                        </div>
                    </div>
              </div>
            </div>        
        </div>
    </div>
</div>