<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
 ?>
 <div class="row">
    <div  class="col-lg-7 col-md-7 col-sm-7">
      <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8">
           <table class="table table-hover table-sm">
            <tr>
                <td><label><?php echo $this->lang->line('case_id'); ?></label></td>
                <td><?php echo $radio_billing->case_reference_id ?></td>
            </tr>
            <tr>    
                <td><label><?php echo $this->lang->line('patient_name'); ?></label></td>
                <td><?php echo $radio_billing->patient_name." (".$radio_billing->patient_id.")" ?></td>
            </tr>
            <tr>
                <td><label><?php echo $this->lang->line('doctor_name'); ?></label></td>
                <td><?php echo $radio_billing->doctor_name ?></td>
            </tr>
            <tr>    
                <td><label><?php echo $this->lang->line('generated_by'); ?></label></td>
                <td><?php echo composeStaffNameByString($radio_billing->name,$radio_billing->surname,$radio_billing->employee_id); ?></td>
            </tr>
            <tr>
                <td><label><?php echo $this->lang->line('age'); ?></label></td>
                <td><?php echo $this->customlib->getPatientAge($radio_billing->age,$radio_billing->month,$radio_billing->day); ?></td>
            </tr>
            <tr>     
                <td><label><?php echo $this->lang->line('gender'); ?></label></td>
                <td><?php echo $radio_billing->gender ?></td>
            </tr>
            <tr>
                <td><label><?php echo $this->lang->line('blood_group'); ?></label></td>
                <td><?php echo $radio_billing->blood_group_name; ?></td>
            </tr>
            <tr>    
                <td><label><?php echo $this->lang->line('mobile_no'); ?></label></td>
                <td><?php echo $radio_billing->mobileno ?></td>
            </tr>
            <tr>
                <td><label><?php echo $this->lang->line('email'); ?></label></td>
                <td><?php echo $radio_billing->email ?></td>
            </tr>
            <tr>    
                <td><label><?php echo $this->lang->line('address'); ?></label></td>
                <td><?php echo $radio_billing->address ?></td>
            </tr>
         </table>
      </div>   
     <div class="col-lg-4 col-md-4 col-sm-4">    
    <table class="table table-hover table-sm">       
            <tbody> 
                  <tr>
                    <td class="text-right"><label><?php echo $this->lang->line('total');?></label></td>
                    <td class="text-right"><?php echo $currency_symbol.$radio_billing->total; ?></td>
                </tr>  
                <tr>
                    <td class="text-right"><label><?php echo $this->lang->line('discount_percentage');?></label></td>
                    <td class="text-right"><?php echo "(".$radio_billing->discount_percentage ."%) ".$currency_symbol.$radio_billing->discount; ?></td>
                </tr>
                <tr>
                    <td class="text-right"><label><?php echo $this->lang->line('tax');?></label></td>
                    <td class="text-right"><?php echo $currency_symbol.$radio_billing->tax; ?></td>
                </tr>
                <tr>
                    <td class="text-right"><label><?php echo $this->lang->line('net_amount');?></label></td>
                    <td class="text-right"><?php echo $currency_symbol.$radio_billing->net_amount; ?></td>
                </tr>
            <tr>
                <td class="text-right"><label><?php echo $this->lang->line('paid_amount');?></label></td>
                <td class="text-right"><?php echo $currency_symbol.$radio_billing->total_deposit; ?></td>
            </tr>
            <tr>
                <td class="text-right"><label><?php echo $this->lang->line('due_amount');?></label></td>
                <td class="text-right"><?php echo $currency_symbol.amountFormat($radio_billing->net_amount-$radio_billing->total_deposit); ?></td>             
                    </tr>
                </tbody>
            </table>
      </div>  
      </div>
    </div>
        <div class="col-lg-5 col-md-5 col-sm-5">
             <?php  if ($this->rbac->hasPrivilege('radiology_billing_payment', 'can_add')) { ?>
                        <form id="<?php echo $form_id; ?>" action="<?php echo site_url('admin/bill/partial_radiology_bill')?>" accept-charset="utf-8" method="post" class="ptt10" >
                            <input type="hidden" name="radiology_billing_id" value="<?php echo $radiology_billing_id;?>">
                                 <input type="hidden" name="case_reference_id" value="<?php echo $radio_billing->case_reference_id ?>">
                                 <input type="hidden" name="patient_id" value="<?php echo $radio_billing->patient_id ?>">
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
                                        <input type="text" name="amount" id="amount" class="form-control">
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
                                        <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
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
                                <div class="row">                            
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('note'); ?></label> 
                                        <textarea  name="note" id="note" class="form-control"></textarea>                                      
                                    </div>
                                </div>
                            </div>
                           <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                        </form>
                    <?php } ?>
                    </div>
                </div>
                <h4><?php echo $this->lang->line('transaction_history'); ?></h4>
                <hr>
 <?php  
if(!empty($radiology_transaction)){
?>
<div class="table-responsive">
 <table class="table table-hover">	
	<thead>
		<tr>
			<th><?php echo $this->lang->line('transaction_id'); ?></th>
			<th><?php echo $this->lang->line('date'); ?></th>
			<th><?php echo $this->lang->line('mode'); ?></th>
            <th><?php echo $this->lang->line('note'); ?></th>
			<th class="text text-right"><?php echo $this->lang->line('amount'); ?></th>
			<th class="text text-right"><?php echo $this->lang->line('action'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
foreach ($radiology_transaction as $transaction_key => $transaction) {
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
    if($transaction->cheque_date!='' && $transaction->cheque_date!='0000-00-00'){
       echo $this->lang->line('cheque_date') .": ".$this->customlib->YYYYMMDDTodateFormat($transaction->cheque_date);
   }     

     }
                        ?></td>
     <td><?php echo $transaction->note; ?></td>
	<td class="text text-right"><?php echo  $currency_symbol.$transaction->amount; ?></td>
	<td class="text text-right">
    <?php 
    if ($transaction->payment_mode == "Cheque" && $transaction->attachment != "")  {
    ?>
    <a href='<?php echo site_url('admin/transaction/download/'.$transaction->id);?>' class='btn btn-default btn-xs'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
    <?php
} ?>
        <?php 
        if($is_bill){
            ?>
        <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id;?>" class='btn btn-default btn-xs print_radio_receipt'  data-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>
            <?php
        }else{ 
            ?>
                    <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id;?>" class='btn btn-default btn-xs print_receipt'  data-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>
<a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id;?>" class='btn btn-default btn-xs delete_trans'  data-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>
            <?php
        }
        ?>
    </td>	
</tr>
	<?php
}
		 ?>
	</tbody>
</table>
</div>
<?php
}else{
    ?>
<div class="alert alert-info">
    <?php echo $this->lang->line('no_record_found'); ?>
</div>
    <?php
}
  ?>
