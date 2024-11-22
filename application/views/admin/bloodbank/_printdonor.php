
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$amount=0;
?>
<div class="fixed-print-header">
    <?php if (!empty($print_details[0]['print_header'])) { ?>
            <div>
                <img src="<?php
                    if (!empty($print_details[0]['print_header'])) {
                        echo base_url() . $print_details[0]['print_header'].img_time();
                    }
                ?>" class="img-responsive" style="height:100px; width: 100%;">
            </div>
            <?php } ?>
</div> 
<table class="table-print-full" width="100%">
    <thead>
        <tr>
            <td><div class="header-space">&nbsp;</div></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
      <div class="content-body">            
<div class="print-area p-1">
    <div class="row">
        <div class="col-md-12">
            
            <div class="card">
                <div class="card-body">  
                    <div class="row">
                    <div class="col-md-12">
                        <table class="printablea4">
                            <tbody>
                                <tr> 
                                    <th width="25%"><?php echo $this->lang->line('donor_name'); ?></th>
                                    <td width="30%"><?php echo $result['donor_name']; ?></td>
                                    <th width="25%"><?php echo $this->lang->line('age'); ?></th>
                                    <td width="30%"><?php echo $this->customlib->getAgeBydob($result['date_of_birth']); ?></td>									
                                </tr> 
								<tr>
									<th width="25%"><?php echo $this->lang->line('date_of_birth'); ?></th>
                                    <td width="30%"><?php echo $this->customlib->YYYYMMDDTodateFormat($result['date_of_birth']); ?></td>
									<th width="25%"><?php echo $this->lang->line('blood_group'); ?></th>
                                    <td width="30%"><?php echo $result['blood_group_name']; ?></td>
								</tr>
                                <tr >                                    
                                    <th width="25%"><?php echo $this->lang->line('gender'); ?></th>
                                    <td width="30%"><?php echo $result['gender']; ?></td>
									<th width="25%"><?php echo $this->lang->line('contact_no'); ?></th>
                                    <td width="30%"><?php echo $result['contact_no']; ?></td>
                                </tr>
                                <tr > 
                                    <th width="25%"><?php echo $this->lang->line('father_name'); ?></th>
                                    <td width="30%"><?php echo $result['father_name']; ?></td>                                    
									<th width="25%"><?php echo $this->lang->line('address'); ?></th>
                                    <td width="30%"><?php echo $result['address']; ?></td>
                                </tr>                                
                                 
                                <?php
                                if (!empty($fields)) {
                                    foreach ($fields as $fields_key => $fields_value) { ?>
										<tr class="no-line">	
                                            <th width="25%"><?php echo $fields_value->name; ?></th>
                                            <td colspan="3"><?php echo $result[$fields_value->name]; ?></td>
										</tr>	
                                <?php } } ?>                                
                                
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <?php  if($this->rbac->hasPrivilege('blood_stock', 'can_view')) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="print-table">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('bags'); ?></th>
                                        <th><?php echo $this->lang->line('donate_date'); ?></th>             
                                        <th class="text-right rtl-text-left"><?php echo $this->lang->line('standard_charge').' ('.$currency_symbol.')'; ?></th>
                                        <th class="text-right rtl-text-left"><?php echo $this->lang->line('apply_charge').' ('.$currency_symbol.')'; ?></th>
                                        <th><?php echo $this->lang->line('discount').' (%)'; ?></th>
                                        <th><?php echo $this->lang->line('tax').' (%)'; ?></th>
                                        <th class="text-right rtl-text-left"><?php echo $this->lang->line('net_amount').' ('.$currency_symbol.')'; ?></th>
                                        <th><?php echo $this->lang->line('payment_date'); ?></th>  
                                        <th><?php echo $this->lang->line('note'); ?></th>                          
                                        <th class="text-right rtl-text-left"><?php echo $this->lang->line('payment_mode'); ?></th>
                                        <th class="text-right rtl-text-left"><?php echo $this->lang->line('paid_amount').' ('.$currency_symbol.')'; ?></th>              
                                    </tr>
                                </thead>
                                <tbody>
                                <?php          
                                    $count = 1;
                                    foreach ($bloodbatch as $detail) {
                                ?> 
                                    <tr>
                                        <td><?php echo $this->customlib->bag_string($detail->bag_no,$detail->volume,$detail->unit_name); ?></td>
                                        <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($detail->donate_date)) ?></td>                 
                                        <td class="text-right rtl-text-left"><?php echo $detail->standard_charge; ?></td>
                                        <td class="text-right rtl-text-left"><?php echo $detail->apply_charge; ?></td>
                                        <td>
                                            <?php 
                                                $discount_amt=calculatePercent( $detail->apply_charge,$detail->discount_percentage);
                                                echo calculatePercent( $detail->apply_charge,$detail->discount_percentage)." (".$detail->discount_percentage."%) "; 
                                            ?>
                                        </td>
                                        <td class="text-right rtl-text-left">
                                            <?php echo calculatePercent(($detail->apply_charge-$discount_amt),$detail->tax_percentage)." (".$detail->tax_percentage."%) "; ?>
                                        </td>                                        
                                        <td class="text-right"><?php $netamount =  $detail->standard_charge + calculatePercent(($detail->apply_charge-$discount_amt),$detail->tax_percentage) ; echo amountFormat($netamount); ?></td>
                                        <td><?php echo $this->customlib->YYYYMMDDTodateFormat($detail->payment_date); ?></td>
                                         <td><?php echo $detail->note ?></td>
                                        <td class="text-right rtl-text-left"><?php echo $this->lang->line(strtolower($detail->payment_mode))."<br>";
                                                if($detail->payment_mode == "Cheque"){
                                                    if($detail->cheque_no!=''){
                                                        echo $this->lang->line('cheque_no') . ": ".$detail->cheque_no;       
                                                        echo "<br>";
                                                    }
                                                    if($detail->cheque_date!='' && $detail->cheque_date!='0000-00-00'){
                                                        echo $this->lang->line('cheque_date') .": ".$this->customlib->YYYYMMDDTodateFormat($detail->cheque_date);
                                                    }
                                                }
                                            ?>             
                                        </td>
                                        <td class="text-right rtl-text-left"><?php echo $detail->amount ?></td>          
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php }?>
                </div>
            </div>
                          
        </div>
    </div>  
</div>
</div>
    </td></tr></tbody>
    <tfoot><tr><td>

<?php
                if (!empty($print_details[0]['print_footer'])) {
                    ?>
   <div class="footer-space">&nbsp;</div>
<?php
}
?>



</td></tr></tfoot>
</table>
<?php
                if (!empty($print_details[0]['print_footer'])) {
                    ?>
<div class="footer-fixed">

<?php   echo $print_details[0]['print_footer'];?>
            
</div>
<?php
}
?>