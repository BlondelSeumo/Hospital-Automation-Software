<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
 ?>
<div class="row">
    <div  class="col-lg-7 col-md-7 col-sm-7">
              <?php 
if(!empty($ambullance_call_detail)){
   
    ?>  <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-8">
                <table class="table table-hover table-sm">
                    <tr>                                  
                        <th width="35%"><?php echo $this->lang->line('bill_no'); ?></th>
                        <td><?php echo $this->customlib->getSessionPrefixByType('ambulance_call_billing').$billing_id; ?></td>
                    </tr>                         
                    <tr>                                  
                        <th width="35%"><?php echo $this->lang->line('received_to'); ?></th>
                        <td><?php echo  composePatientName($ambullance_call_detail['patient'],$ambullance_call_detail['patient_id']); ?></td>
                    </tr>
                    <tr>    
                        <th><?php echo $this->lang->line('vehicle_no'); ?></th>
                        <td><?php echo $ambullance_call_detail['vehicle_no']?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('vehicle_model'); ?></th>
                        <td><?php echo $ambullance_call_detail['vehicle_model']; ?></td>
                    </tr>
                    <tr>    
                        <th><?php echo $this->lang->line('driver_name'); ?></th>
                        <td><?php echo $ambullance_call_detail['driver']?></td>
                    </tr>            
                    <tr>
                        <th><?php echo $this->lang->line('driver_contact'); ?></th>
                        <td><?php echo $ambullance_call_detail['contact_no']?></td>
                    </tr>    
                    <tr>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <td><?php echo $this->customlib->YYYYMMDDTodateFormat($ambullance_call_detail['date'])?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('patient_address'); ?></th>
                        <td colspan="3"><?php echo $ambullance_call_detail['address']?></td>
                    </tr>    
                </table>
            </div>    
            <div class="col-lg-4 col-md-4 col-sm-4">
                <table class="table table-hover table-sm">
                    <tr>
                        <th><?php echo $this->lang->line('amount'); ?></th>
                        <td class="text text-right"><?php echo $currency_symbol.$ambullance_call_detail['amount']?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('discount'); ?> (<?php echo $ambullance_call_detail['discount_percentage'] ?>%)</th>
                        <td class="text text-right"><?php $dicount_amount=calculatePercent($ambullance_call_detail['amount'],$ambullance_call_detail['discount_percentage']);
						echo $currency_symbol.$dicount_amount; ?>
                        </td>
                    </tr> 
                    <tr>
                        <th><?php echo $this->lang->line('tax'); ?> (<?php echo $ambullance_call_detail['tax_percentage'] ?>%)</th>
                        <td class="text text-right"><?php 
						$final_amount = $ambullance_call_detail['amount']-$dicount_amount;
						echo $currency_symbol.calculatePercent($final_amount,$ambullance_call_detail['tax_percentage'])?>
                        </td>
                    </tr>                     
                    <tr>                   
                        <th><?php echo $this->lang->line('net_amount'); ?></th>
                        <td class="text text-right"><?php echo $currency_symbol.$ambullance_call_detail['net_amount']?></td>  
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('paid_amount'); ?></th>
                        <td class="text text-right"><?php echo $currency_symbol.$ambullance_call_detail['paid_amount'];?></td>
                    </tr>
                    <tr>    
                        <th><?php echo $this->lang->line('balance_amount'); ?></th>
                        <td class="text text-right"><?php echo $currency_symbol.amountFormat($ambullance_call_detail['net_amount']-$ambullance_call_detail['paid_amount']);?></td>
                    </tr>           
                </table>
            </div>
        </div>
    <?php  
}
 ?>
    </div>
    <?php if ($this->rbac->hasPrivilege('ambulance_partial_payment', 'can_add')) { ?>
        <div class="col-lg-5 col-md-5 col-sm-5">
            <form id="add_partial_payment" action="<?php echo site_url('admin/vehicle/partialbill')?>" accept-charset="utf-8" method="post" class="ptt10" >
                <input type="hidden" name="billing_id" value="<?php echo $billing_id;?>">
                <input type="hidden" name="case_id" value="<?php echo $case_id;?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small> 
                            <input type="text" name="payment_date" id="date" class="form-control datetime">
                            <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small> 
                            <input type="text" name="payment_amount" id="amount" class="form-control" value="<?php echo $balance_amount ; ?>" >
                             <input type="hidden" name="net_amount" id="net_amount" class="form-control" value="<?php echo $balance_amount ; ?>" >
                             <input type="hidden" name="patient_id" id="patient_id" class="form-control" value="<?php echo $patient_id ; ?>" >
                            <span class="text-danger"><?php echo form_error('amount'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="row">                       
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('payment_mode'); ?></label> 
                            <select class="form-control payment_mode" name="payment_mode">
                                <?php foreach ($payment_mode as $key => $value) { ?>
                                    <option value="<?php echo $key ?>" <?php
                                        if ($key == 'cash') {
                                            echo "selected";
                                        }
                                        ?>><?php echo $value ?>
                                    </option>
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
                            <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
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
                            <textarea name="note" id="note" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </form>
        </div>
    <?php } ?>
</div>
<div class="">
    <div>
    <h4><?php echo $this->lang->line('transaction_history'); ?></h4>
    <hr>
        <div class="table-responsive">
            <table class="table table-hover">            
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('transaction_id'); ?></th>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('mode'); ?></th>
                        <th><?php echo $this->lang->line('amount').' ('. $currency_symbol .')'; ?></th>
                        <th><?php echo $this->lang->line('note'); ?></th>
                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    
                    if(!empty($transaction)){ ?>
                    <?php foreach ($transaction as $transaction_key => $transaction) { ?>
                        <tr>
                            <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id').$transaction->id; ?></td>
                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction->payment_date,$this->customlib->getHospitalTimeFormat()); ?></td>
                            <td><?php if($transaction->payment_mode){ echo $this->lang->line(strtolower($transaction->payment_mode))."<br>"; }
                                     if($transaction->payment_mode== "Cheque"){
                                        if($transaction->cheque_no!=''){
                                        echo $this->lang->line('cheque_no') . ": ".$transaction->cheque_no;                   
                                        echo "<br>";
                                    }
                                        if($transaction->cheque_date!='' && $transaction->cheque_date!='0000-00-00'){
                                           echo $this->lang->line('cheque_date') .": ".$this->customlib->YYYYMMDDTodateFormat($transaction->cheque_date);
                                       }                                           
                                         }
                                                            ?></td>
                            <td><?php echo $transaction->amount; ?></td>
                            <td><?php echo $transaction->note; ?></td>
                            <td>
                                <div class="pull-right">                                    
                                 <?php if ($transaction->payment_mode == "Cheque" && $transaction->attachment != "")  {
        ?>
        <a href='<?php echo site_url('admin/transaction/download/'.$transaction->id);?>' class='btn btn-default btn-xs' title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
        <?php
    }
             ?>
                            <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id;?>" class='btn btn-default btn-xs print_receipt'  data-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>
                            <?php if ($this->rbac->hasPrivilege('ambulance_partial_payment', 'can_delete')) { ?>
                            <a href='javascript:void(0)'  data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id;?>" data-billing-id="<?php echo $billing_id;?>" data-patient-id="<?php echo $patient_id ; ?>" data-amount="<?php echo $transaction->amount; ?>" data-case-id="<?php echo $case_id;?>" data-balance="<?php echo $balance_amount ; ?>" class='btn btn-default btn-xs delete_trans'  data-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>
                        <?php } ?>
                                </div> 
                        </td>
                    </tr>
                    <?php }}else{
                        ?>
                        <tr><td colspan="6" class="text-center"><?php echo $this->lang->line('no_record_found')?></td></tr>
                        <?php
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>   