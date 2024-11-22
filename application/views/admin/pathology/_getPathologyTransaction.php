<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
 ?>
 <div class="row">
    <div  class="col-lg-7 col-md-7 col-sm-7">
     <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8">
               <table class="table table-hover table-sm">
                <tr>
                    <td class="col-lg-3"><label><?php echo $this->lang->line('bill_no'); ?></label></td>
                    <td class="col-lg-3"><?php echo $this->customlib->getSessionPrefixByType('pathology_billing').$pathology_billing->id ?></td>          
                </tr>
                <tr>
                    <td><label><?php echo $this->lang->line('case_id'); ?></label></td>
                    <td><?php echo $pathology_billing->case_reference_id ?></td>
                </tr>    
                <tr>    
                    <td><label><?php echo $this->lang->line('patient_name'); ?></label></td>
                    <td><?php echo $pathology_billing->patient_name." (".$pathology_billing->patient_id.")" ?></td>
                </tr>
                <tr>
                    <td><label><?php echo $this->lang->line('doctor_name'); ?></label></td>
                    <td><?php echo $pathology_billing->doctor_name ?></td>
                </tr>
                <tr>    
                    <td><label><?php echo $this->lang->line('generated_by'); ?></label></td>
                    <td><?php echo composeStaffNameByString($pathology_billing->name,$pathology_billing->surname,$pathology_billing->employee_id); ?></td>
                </tr>
                <tr>
                    <td><label><?php echo $this->lang->line('age'); ?></label></td>
                    <td><?php 
                        echo $this->customlib->getPatientAge($pathology_billing->age,$pathology_billing->month,$pathology_billing->day);
                    ?></td>
                </tr>
                <tr>    
                    <td><label><?php echo $this->lang->line('gender'); ?></label></td>
                    <td><?php echo $pathology_billing->gender ?></td>
                </tr>
                <tr>
                    <td><label><?php echo $this->lang->line('blood_group'); ?></label></td>
                    <td><?php echo $pathology_billing->blood_group_name; ?></td>
                </tr>
                <tr>    
                    <td><label><?php echo $this->lang->line('mobile_no'); ?></label></td>
                    <td><?php echo $pathology_billing->mobileno ?></td>
                </tr>
                <tr>
                    <td><label><?php echo $this->lang->line('email'); ?></label></td>
                    <td><?php echo $pathology_billing->email ?></td>
                </tr>
                <tr>    
                    <td><label><?php echo $this->lang->line('address'); ?></label></td>
                    <td><?php echo $pathology_billing->address ?></td>
                </tr>           
                <?php 
                    $balance_amount = $pathology_billing->total + $pathology_billing->tax - $pathology_billing->discount - $pathology_total_payment ;
                ?>
            </table>
        </div>        
        <div class="col-lg-4 col-md-4 col-sm-4">
            <table class="table table-hover table-sm">
                   <tr>     
                    <td class="text-right"> 
                         <label><?php echo $this->lang->line('total'); ?></label>
                    </td>
                    <td class="text-right">     
                         <?php if (!empty($pathology_billing->total)) {
                                echo $currency_symbol.$pathology_billing->total ;
                          }  ?> 
                    </td>      
                </tr> 
                <tr>                   
                    <td class="text-right"><label><?php echo $this->lang->line('total_discount'); ?></label></td>
                    <td class="text-right">
                        <?php if (!empty($pathology_billing->discount)) {
                            echo "(".$pathology_billing->discount_percentage."%) ".$currency_symbol.$pathology_billing->discount;
                       }  ?>                           
                    </td>
                </tr>                
                 <tr>
                    <td class="text-right"><label><?php echo $this->lang->line('total_tax'); ?></label>
                    </td> 
                    <td class="text-right">    
                        <?php if (!empty($pathology_billing->tax)) {
                            echo $currency_symbol.$pathology_billing->tax ;
                        }  ?> 
                    </td>    
                </tr>    
                 <tr>
                    <td class="text-right"><label><?php echo $this->lang->line('net_amount'); ?></label>
                    </td> 
                    <td class="text-right"> 
                   
                        <?php if (!empty($pathology_billing->net_amount)) {
                            echo $currency_symbol.$pathology_billing->net_amount ;
                        }  ?> 
                    </td>    
                </tr> 
                 <tr>
                    <td class="text-right"><label><?php echo $this->lang->line('paid_amount'); ?></label>
                    </td> 
                    <td class="text-right">    
                        <?php if (!empty($pathology_billing->total_deposit)) {
                            echo $currency_symbol.$pathology_billing->total_deposit ;
                        }  ?> 
                    </td>    
                </tr>   
                  <tr>
                    <td class="text-right"><label><?php echo $this->lang->line('due_amount'); ?></label>
                    </td> 
                    <td class="text-right">    
                        <?php if (!empty($pathology_billing->total_deposit)) {
                            echo $currency_symbol.amountFormat($pathology_billing->net_amount-$pathology_billing->total_deposit) ;
                        }  ?> 
                    </td>    
                </tr>         
            </table>
        </div>   
      </div><!--./row-->
    </div> 
        <div class="col-lg-5 col-md-5 col-sm-5">          
            <form id="<?php echo $form_id;?>" action="<?php echo site_url('admin/pathology/partialbill')?>" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="pathology_billing_id" value="<?php echo $pathology_billing_id;?>">
                            <input type="hidden" name="case_reference_id" value="<?php echo $pathology_billing->case_reference_id ?>">
                            <input type="hidden" name="patient_id" value="<?php echo $pathology_billing->patient_id; ?>">
                                  <?php  if ($this->rbac->hasPrivilege('pathology_partial_payment', 'can_add')) { ?>                      
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
                                        <input type="text" name="amount" value="<?= $balance_amount; ?>" id="amount" class="form-control">
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
                           <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                         <?php } ?>
                     </form>                
                    </div>
                </div>
                <h4><?php echo $this->lang->line('transaction_history'); ?></h4>
                <hr>
 <?php 
if(!empty($pathology_transaction)){
?>
<div class="table-responsive">
 <table class="table table-hover">
	<thead>
		<tr>
			<th><?php echo $this->lang->line('transaction_id'); ?></th>
			<th><?php echo $this->lang->line('date'); ?></th>
			<th><?php echo $this->lang->line('mode'); ?></th>
			<th><?php echo $this->lang->line('note'); ?></th>
			<th class="text text-center"><?php echo $this->lang->line('amount'); ?></th>
			<th class="text-right"><?php echo $this->lang->line('action'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
foreach ($pathology_transaction as $transaction_key => $transaction) {
	?>
<tr>
	<td><?php echo $this->customlib->getSessionPrefixByType('transaction_id').$transaction->id; ?></td>
	<td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction->payment_date,$this->customlib->getHospitalTimeFormat()); ?></td>
	<td>
        <?php echo $this->lang->line(strtolower($transaction->payment_mode))."<br>";                                                       
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
	<td class="text text-center"><?php echo $currency_symbol.$transaction->amount; ?></td>
	<td class="text-right">
     <?php  if ($transaction->payment_mode == "Cheque" && $transaction->attachment != "")  {
    ?>
    <a href='<?php echo site_url('admin/transaction/download/'.$transaction->id);?>' class='btn btn-default btn-xs'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
    <?php
}
         ?>
        <?php 
        if($is_bill){
            ?>
            <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id;?>" class='btn btn-default btn-xs print_patho_receipt'  data-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>          
            <?php
        }else{ 
            ?>
            <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id;?>" class='btn btn-default btn-xs print_receipt'  data-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>
                 <?php  if (($this->rbac->hasPrivilege('pathology_billing_payment', 'can_delete')) || (($this->rbac->hasPrivilege('pathology_partial_payment', 'can_delete')))) { ?>    
            <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id="<?php echo $transaction->id;?>" class='btn btn-default btn-xs delete_trans'  data-toggle='tooltip' title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>
            <?php }
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
}else{ ?>
    <br/>
    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
   <?php 
}
  ?>