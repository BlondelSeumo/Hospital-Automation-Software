<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
if($view_delete){ 
    $form_id="add_partial_payment"; 
    $print_receipt="print_receipt";
}else{
    $form_id="add_bill_partial_payment"; 
    $print_receipt="print_pharmacyBillReceipt";
}
?>
 <div class="row">
    <div  class="col-lg-7 col-md-7 col-sm-7">      
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
           <table class="table table-hover table-sm">
            <tbody>
                <tr>
                    <td><?php echo $this->lang->line('bill_no');?></td>
                    <td><?php echo $this->customlib->getSessionPrefixByType('pharmacy_billing').$pharmacy_bill_detail['id']; ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('case_id');?></td>
                    <td><?php echo $pharmacy_bill_detail['case_reference_id']; ?></td>
                </tr>
                <tr>     
                    <td><?php echo $this->lang->line('patient');?></td>
                    <td><?php echo composePatientName($pharmacy_bill_detail['patient_name'],$pharmacy_bill_detail['patient_id']); ?></td>
                </tr>
                <tr>           
                    <td><?php echo $this->lang->line('generated_by');?></td>
                    <td><?php echo composeStaffNameByString($pharmacy_bill_detail['name'],$pharmacy_bill_detail['surname'],$pharmacy_bill_detail['employee_id']); ?></td>  
                </tr>
                <tr>    
                    <td><?php echo $this->lang->line('date');?></td>
                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($pharmacy_bill_detail['date']); ?></td>          
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('mobileno');?>Mobile</td>
                    <td><?php echo $pharmacy_bill_detail['mobileno']; ?></td>
                </tr>
            </tbody>
            </table>  
         </div>   
    <div class="col-lg-6 col-md-6 col-sm-6">
        <table class="table table-hover table-sm">
            <tbody> 
                  <tr>
                    <td class="text-right"><?php echo $this->lang->line('total'). " (" . $currency_symbol . ")";?></td>
                    <td class="text-right"><?php echo $pharmacy_bill_detail['total']; ?></td>
                </tr>  
                <tr>
                    <td class="text-right"><?php echo $this->lang->line('discount_percentage'). " (" . $currency_symbol . ")";?></td>
                    <td class="text-right"><?php echo "(".$pharmacy_bill_detail['discount_percentage'] ."%) ".$pharmacy_bill_detail['discount']; ?></td>
                </tr>
                <tr>
                    <td class="text-right"><?php echo $this->lang->line('tax'). " (" . $currency_symbol . ")";?></td>
                    <td class="text-right"><?php echo $pharmacy_bill_detail['tax']; ?></td>
                </tr>
                <tr>
                    <td class="text-right"><?php echo $this->lang->line('net_amount'). " (" . $currency_symbol . ")";?></td>
                    <td class="text-right"><?php echo $pharmacy_bill_detail['net_amount']; ?></td>
                </tr>               
            <tr>
                <td class="text-right"><?php echo $this->lang->line('paid_amount'). " (" . $currency_symbol . ")";?></td>
                <td class="text-right"><?php echo $pharmacy_bill_detail['paid_amount']; ?></td>
            </tr>
             <tr>
                <td class="text-right"><?php echo $this->lang->line('refund_amount'). " (" . $currency_symbol . ")";?></td>
                <td class="text-right"><?php echo $pharmacy_bill_detail['refund_amount']; ?></td>
            </tr>
            <tr>
                <td class="text-right"><?php echo $this->lang->line('due_amount'). " (" . $currency_symbol . ")";?></td>
                <td class="text-right"><?php echo amountFormat(($pharmacy_bill_detail['net_amount'] + $pharmacy_bill_detail['refund_amount'] )-$pharmacy_bill_detail['paid_amount']); ?></td>
                    </tr>
                </tbody>
            </table>
         </div> 
      </div>      
    </div> 
                    <div class="col-lg-5 col-md-5 col-sm-5">
                        <?php  if ($this->rbac->hasPrivilege('pharmacy_billing_payment', 'can_add') || $this->rbac->hasPrivilege('pharmacy_partial_payment', 'can_add')) { ?>
                        <form id="<?php echo $form_id; ?>" action="<?php echo site_url('admin/pharmacy/partialbill')?>" accept-charset="utf-8" method="post" class="pt5">
                            <input type="hidden" name="pharmacy_bill_basic_id" value="<?php echo $pharmacy_bill_basic_id;?>">
                            <div class="row">
                                   <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->lang->line('date'); ?><small class="req"> *</small> 
                                        <input type="text" name="payment_date" id="date" class="form-control datetime">
                                        <input type="hidden" name="patient_id" value="<?= $pharmacy_bill_detail['patient_id']; ?>">
                                        <input type="hidden" name="case_reference_id" value="<?= $pharmacy_bill_detail['case_reference_id'] ; ?>">
                                        <input type="hidden" name="refund_amount" value="<?= $pharmacy_bill_detail['refund_amount'] ; ?>">
                                        <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?><small class="req"> *</small> 
                                        <input type="text" name="amount" id="amount" class="form-control" value="<?php echo $balance_amount; ?>">
                                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                       
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->lang->line('payment_mode'); ?> 
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
                                        <?php echo $this->lang->line('cheque_no'); ?><small class="req"> *</small> 
                                        <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                        <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                    </div>
                                </div>
                                  <div class="col-md-3">
                                    <div class="form-group">
                                        <?php echo $this->lang->line('cheque_date'); ?><small class="req"> *</small> 
                                        <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                    </div>
                                </div>
                                     <div class="col-md-5">
                                    <div class="form-group">
                                        <?php echo $this->lang->line('attach_document'); ?>
                                        <input type="file" class="filestyle form-control" name="document">
                                        <span class="text-danger"><?php echo form_error('document'); ?></span> 
                                    </div>
                                </div>
                                </div>
                                <div class="row">                            
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->lang->line('note'); ?> 
                                        <textarea name="note" id="note" class="form-control"></textarea>                                      
                                    </div>
                                </div>
                            </div>
                           <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        </form>
                    <?php } ?>
                    </div>
                </div>
                <h4><?php echo $this->lang->line('transaction_history'); ?></h4>
                <hr>
 <?php 

if(!empty($pharmacy_transaction)){

?>
<div class="table-responsive">
<div class="pup-scroll-middle-70">
 <table class="table table-hover">	
	<thead>
		<tr>
			<th><?php echo $this->lang->line('transaction_id'); ?></th>
			<th><?php echo $this->lang->line('date'); ?></th>
            <th><?php echo $this->lang->line('payment_type'); ?></th>
			<th><?php echo $this->lang->line('mode'); ?></th>
			<th><?php echo $this->lang->line('note'); ?></th>
			<th class="text-right"><?php echo $this->lang->line('amount'). " (" . $currency_symbol . ")"; ?></th>
			<th class="text-right"><?php echo $this->lang->line('action'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
foreach ($pharmacy_transaction as $transaction_key => $transaction) {
	?>
<tr>
	<td><?php echo $this->customlib->getSessionPrefixByType('transaction_id').$transaction->id; ?></td>
	<td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction->payment_date, $this->customlib->getHospitalTimeFormat()); ?></td>
	<td><?php echo $this->lang->line($transaction->type); ?></td>
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
	<td ><?php echo $transaction->note; ?></td>
	<td class="text-right"><?php echo $transaction->amount; ?></td>
	<td class="text-right">
    <?php if ($transaction->payment_mode == "Cheque" && $transaction->attachment != "")  {
    ?>
    <a href='<?php echo site_url('admin/transaction/download/'.$transaction->id);?>' class='btn btn-default btn-xs'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
    <?php
}
         ?> 
    <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id;?>" class='btn btn-default btn-xs <?php echo $print_receipt; ?>'  data-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>
        <?php if($view_delete){
            if($this->rbac->hasPrivilege('pharmacy_partial_payment', 'can_delete')) { ?>
             <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id;?>" class='btn btn-default btn-xs delete_trans'  data-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>
            <?php
        } }?>   
    </td>	
</tr>
	<?php
}
		 ?>
	</tbody>
</table>
</div>
</div>
<?php
}else{
    ?>
    <div class="alert alert-info">
        <?php 
echo $this->lang->line('no_record_found');
         ?>
    </div>
    <?php
}
  ?>