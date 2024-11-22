<?php 

$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
 ?>
<div class="row">
    <div  class="col-lg-7 col-md-7 col-sm-7">
      <?php 

if(!empty($blood_issue_detail)){
   
    ?>  <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-8">
                <table class="table table-hover table-sm">                           
                    <tr>                                  
                        <th width="35%"><?php echo $this->lang->line('received_to'); ?></th>
                        <td><?php echo  composePatientName($blood_issue_detail['patient_name'],$blood_issue_detail['patient_id']); ?></td>
                    </tr>
                    <tr>    
                        <th><?php echo $this->lang->line('bags'); ?></th>
                        <td><?php echo $blood_issue_detail['bag_no']?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('issue_date'); ?></th>
                        <td><?php echo $this->customlib->YYYYMMDDTodateFormat($blood_issue_detail['date_of_issue'])?></td>
                    </tr>
                    <tr>    
                        <th><?php echo $this->lang->line('blood_group'); ?></th>
                        <td><?php echo $blood_issue_detail['blood_group']?></td>
                    </tr>            
                    <tr>
                        <th><?php echo $this->lang->line('reference'); ?></th>
                        <td><?php echo $blood_issue_detail['reference']?></td>
                    </tr>    
                    <tr>
                        <th><?php echo $this->lang->line('donor_name'); ?></th>
                        <td><?php echo $blood_issue_detail['donor_name']?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('technician'); ?></th>
                        <td colspan="3"><?php echo $blood_issue_detail['technician']?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('note'); ?></th>
                        <td width="85%" colspan="3"><?php echo $blood_issue_detail['remark']?></td>
                    </tr>
                </table>
            </div>    
            <div class="col-lg-4 col-md-4 col-sm-4">
                <table class="table table-hover table-sm">
                    <tr>
                        <th><?php echo $this->lang->line('amount'); ?></th>
                        <td class="text text-right"><?php echo $currency_symbol.$blood_issue_detail['amount']?></td>
                    </tr>
                    <tr>     
                        <th><?php echo $this->lang->line('discount'); ?> (<?php echo $blood_issue_detail['discount_percentage'];?>%)</th>
                        <td class="text text-right"> <?php $discount_amount = calculatePercent($blood_issue_detail['amount'],$blood_issue_detail['discount_percentage']);			
						echo $currency_symbol.$discount_amount; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('tax'); ?> (<?php echo $blood_issue_detail['tax_percentage']; ?>%)</th>
						<td class="text text-right"><?php  $amount = $blood_issue_detail['amount']-$discount_amount ; 					
					echo $currency_symbol.calculatePercent($amount,$blood_issue_detail['tax_percentage']) ?>
                        </td>
                    </tr> 
                    <tr>                   
                        <th><?php echo $this->lang->line('net_amount'); ?></th>
                        <td class="text text-right"><?php echo $currency_symbol.$blood_issue_detail['net_amount']?></td>      
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('paid_amount'); ?></th>
                        <td class="text text-right"><?php echo $currency_symbol.$blood_issue_detail['total_deposit'];?></td>
                    </tr>
                    <tr>    
                        <th><?php echo $this->lang->line('balance_amount'); ?></th>
                        <td class="text text-right"><?php echo $currency_symbol.amountFormat($blood_issue_detail['net_amount']-$blood_issue_detail['total_deposit']);?></td>
                    </tr>           
                </table>
            </div>    
    <?php  
}
 ?>
        </div>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5">
        <?php if ($this->rbac->hasPrivilege('blood_bank_billing_payment', 'can_add')  ) {?>
    <form id="add_partial_payment" action="<?php echo site_url('admin/bloodbank/partialbill')?>" accept-charset="utf-8" method="post">
            <input type="hidden" name="billing_id" value="<?php echo $billing_id;?>">
                            <div class="row">
                                   <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small> 
                                        <input type="text" name="payment_date" id="date" class="form-control datetime">
                                        <input type="hidden" name="patient_id" value="<?= $blood_issue_detail['patient_id']; ?>">
                                        <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small> 
                                        <input type="text" name="amount" value="<?php echo amountFormat($blood_issue_detail['net_amount']-$blood_issue_detail['total_deposit']); ?>" id="amount" class="form-control">
                                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                    </div>
                                </div>
                            </div>
                        <div class="row">                       
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('payment_mode'); ?></label> 
                                        <select class="form-control payment_mode" name="payment_mode">
                                            <?php foreach ($payment_mode as $key => $value) {
                                                ?>
                                                <option value="<?php echo $key ?>" <?php
                                                if ($key == 'cash') {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $value ?></option>
                                            <?php } ?>
                                        </select>    
                                        <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                    </div>
                            </div>
                        </div>
                            <div class="row cheque_div" style="display: none;">                           
                                     <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small> 
                                        <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                        <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                    </div>
                                </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small> 
                                        <input type="text" name="cheque_date" id="cheque_date" class="form-control date" readonly="readonly">
                                        <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                    </div>
                                </div>
                                     <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input type="file"  class=" form-control filestyle" name="document">
                                        <span class="text-danger"><?php echo form_error('document'); ?></span> 
                                    </div>
                                </div>
                                </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('note'); ?></label> 
                                        <textarea  name="note" id="note" class="form-control"></textarea>                                      
                                    </div>
                                </div>
                           </div>                        
                            <div class="modal-footer sticky-footer">
                                <div class="pull-right">
                                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                </div>    
                            </div>
                    </form>
                <?php } ?>
    </div>    
</div>
<div class="">
    <div>

<h4><?php echo $this->lang->line('transaction_history'); ?></h4>
                <hr>
<div class="table-responsive">
 <table class="table table-hover example">
    <thead>
        <tr>
            <th><?php echo $this->lang->line('transaction_id'); ?></th>
            <th><?php echo $this->lang->line('date'); ?></th>
            <th><?php echo $this->lang->line('mode'); ?></th>
            <th><?php echo $this->lang->line('note'); ?></th>
            <th><?php echo $this->lang->line('amount'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if(!empty($transaction)){        
foreach ($transaction as $transaction_key => $transaction) {
    ?>
<tr>
    <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id').$transaction->id; ?></td>
    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction->payment_date,$this->customlib->getHospitalTimeFormat()); ?></td>
    <td><?php echo $this->lang->line(strtolower($transaction->payment_mode))."<br>";

          if($transaction->payment_mode== "Cheque"){
                                   if($transaction->cheque_no!=''){
                                     echo $this->lang->line('cheque_no') . ": ".$transaction->cheque_no;
                                     echo "<br>";
                                    }

 if(!is_null($transaction->cheque_date) ){
    echo $this->lang->line('cheque_date') .": ".$this->customlib->YYYYMMDDTodateFormat($transaction->cheque_date);
  }                                       

                                     }
                                                        ?></td>
    <td><?php echo $transaction->note; ?></td>
    <td><?php echo $currency_symbol.$transaction->amount; ?></td>
    <td class="text-right">
        <?php 
if ($transaction->payment_mode == "Cheque" && $transaction->attachment != "")  {
    ?>
    <a href='<?php echo site_url('admin/transaction/download/'.$transaction->id);?>' class='btn btn-default btn-xs'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
    <?php
}
         ?>
      <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id;?>" class='btn btn-default btn-xs print_bloodbank_receipt'  data-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>
    </td>    
</tr>
    <?php
}
}else{
    ?>
   <tr><td colspan="6" class="text-center req"><?php echo $this->lang->line("no_record_found")?></td></tr>
    <?php
}
         ?>
    </tbody>
</table>
</div>
    </div>
</div>