
<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
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
<div class="print-area">
<div class="row">
        <div class="col-md-12">
              
              <div class="card">
                <div class="card-body">  
                    <div class="row">
                            <div class="col-md-12">
                     <table class="noborder_table mb-0">
                           <tr>
                                <th><?php echo $this->lang->line("opd_id"); ?></th>
                                <td><?php echo $this->customlib->getPatientSessionPrefixByType('opd_no') .$result["opd_details_id"];?></td>                                
                                <th><?php echo $this->lang->line("appointment_date") ; ?></th>
                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result["appointment_date"]); ?></td>
                            </tr>
							<tr>	
								<th><?php echo $this->lang->line("checkup_id") ; ?></th>
                                <td><?php echo $this->customlib->getPatientSessionPrefixByType('checkup_id') .$result["id"] ?></td>
								<th><?php echo $this->lang->line('known_allergies');?></th>
								<td><?php echo $result["known_allergies"]; ?></td>
							</tr>
                             <?php if($result["appointment_no"]!="" || $result["appointment_serial_no"] ) { ?>
                            <tr>
                                <th><?php echo $this->lang->line("appointment_no"); ?></th>
                                <td><?php if($result["appointment_no"]!="") { echo $this->customlib->getpatientSessionPrefixByType('appointment').$result["appointment_no"];} ?></td>
                                <th><?php echo $this->lang->line("appointment_sno") ; ?></th>
                                <td><?php echo $result["appointment_serial_no"] ?></td>                               
                            </tr>
                             <?php } ?>
                        <tr>
                            <th><?php echo $this->lang->line("patient_name"); ?></th>
                            <td><?php echo $result["patient_name"].' ('. $result["patient_id"] .')' ?></td>
							<th><?php echo $this->lang->line("address"); ?></th>
                            <td><?php echo $result["address"] ?></td>
						</tr>
						<tr>
                            <th><?php echo $this->lang->line("age"); ?></th>                            
                            <td><?php echo $this->customlib->getPatientAge($result['age'],$result['month'],$result['day']); ?></td>
                            <th><?php echo $this->lang->line("gender"); ?></th>
                            <td><?php echo $result["gender"] ?></td>                            
                        </tr>
                                             
                        <tr>
                            <th><?php echo $this->lang->line("blood_group"); ?></th>
                            <td><?php echo $blood_group_name; ?></td>
                            <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                            <td><?php echo $result["name"] . " " . $result["surname"].' ('. $result["employee_id"] .')' ?></td>
                        </tr>
											
                        <tr>
                            <th><?php echo $this->lang->line('department'); ?></th>
                            <td><?php echo $result["department_name"]; ?></td>
                        </tr>						 
                        
                    </table>
                </div>
                    </div>
                <div class="divider mt-10 mb-10"></div>
                    <h4 class="font-bold"><?php echo $this->lang->line("payment_details"); ?></h4>
                    <?php  
if (!empty($charge)) {
   ?>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="print-table">
                             <thead>
                                <tr class="line">
                                   <td>#</td>
                                   <td><?php echo $this->lang->line('description');?></td>
                                   <td><?php echo $this->lang->line('tax').' ('.'%'.')';?></td>
                                   <td class="text-right"><?php echo $this->lang->line('amount').' ('.$currency_symbol.')';?></td>
                                </tr>
                             </thead>
                             <tbody>
                                <tr>
                                   <td>1</td>
                                   <td><?php   echo $charge->charge_name ?><br>
                                    <?php echo $charge->note.' ('.$charge->employee_id.')';?>
                                  </td>                                 
                                   <td><?php 
                                   if($charge->tax>0)
                                    { 
                                      $tax=(($charge->apply_charge*$charge->tax)/100);  
                                    }else{ $tax=0; 
                                    } echo amountFormat($tax)." (".$charge->tax."%)";?></td>
                                   <td class="text-right"><?php echo $charge->amount;?></td>
                                </tr>
                                <tr>                                   
                                   <td colspan="3" class="text-right"><?php echo $this->lang->line('net_amount');?></td>
                                   <td class="text-right"><?php echo $currency_symbol.$charge->apply_charge; ?></td>
                                </tr>
                                <tr>                                   
                                   <td colspan="3" class="text-right no-line"><?php echo $this->lang->line('tax');?></td>
                                   <td class="text-right no-line"><?php 
                                   if($charge->tax>0){
                                     $tax_amt = ($charge->apply_charge*$charge->tax/100);
                                   }else{
                                      $tax_amt = 0;
                                   }                                  
                                   $total = ($charge->amount);
                                   echo $currency_symbol.amountFormat($tax_amt); ?></td>
                                </tr>
                                <tr>
                                   <td colspan="3" class="text-right no-line"><?php echo $this->lang->line('total');?></td>
                                   <td class="text-right no-line"><?php 
                                   echo $currency_symbol.amountFormat($charge->apply_charge+$tax_amt); ?></td>
                                </tr>
                                   <tr>                                  
                                   <td colspan="3" class="text-right no-line">
                                    <?php echo $this->lang->line('paid_amount');?></td>
                                   <td class="text-right no-line"><?php 
                                $amount_paid=(!isset($transaction) || empty($transaction)) ? 0:  $transaction->amount;
                                   echo $currency_symbol.amountFormat($amount_paid); ?></td>
                                </tr>
                                    <tr>                                  
                                   <td colspan="3" class="text-right no-line">
                                    <?php echo $this->lang->line('balance_amount');?></td>
                                   <td class="text-right no-line"><?php 
                                $amount_paid=(!isset($transaction) || empty($transaction)) ? 0:  $transaction->amount;
                                   echo $currency_symbol.amountFormat(($total)-$amount_paid); ?></td>
                                </tr>
                             </tbody>
                          </table>
                        </div>
                    </div>
   <?php
}
                     ?>
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