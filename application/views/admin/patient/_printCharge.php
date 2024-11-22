
<?php 
if($charge->opd_id != "" && $charge->opd_id !=0)
{
$patient_name=$charge->opd_patient_name;
$patient_id=$charge->opd_patient_id;
$case_reference_id=$charge->opd_case_reference_id;
}else{
$patient_name=$charge->ipd_patient_name;
$patient_id=$charge->ipd_patient_id;
$case_reference_id=$charge->ipd_case_reference_id;
}
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="fixed-print-header"> 
    <?php if (!empty($print_details[0]['print_header'])) { ?>
                        <div class="pprinta4">
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
                        <div class="col-md-6">
                            <p><?php echo $this->lang->line('patient');?>: <?php echo composePatientName($patient_name,$patient_id); ?></p>
                            <p><?php echo $this->lang->line('case_id');?>: <?php echo $case_reference_id; ?></p>
                        </div>
                        <div class="col-md-6 text-right">                          
                            <p><span class="text-muted"><?php echo $this->lang->line('date');?>: </span> <?php echo $this->customlib->YYYYMMDDHisTodateFormat($charge->date,$this->customlib->getHospitalTimeFormat()); ?></p> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="print-table">
                             <thead>
                                <tr class="line">
                                   <td>#</td>
                                   <td><?php echo $this->lang->line('charge_name');?> / <?php echo $this->lang->line('charge_note');?></td>		   
                                   <td><?php echo $this->lang->line('discount').' ('.'%'.')';?></td>
                                   <td><?php echo $this->lang->line('tax').' ('.'%'.')';?></td>                                
                                   <td class="text-right"><?php echo $this->lang->line('amount').' ('.$currency_symbol.')';?></td>
                                </tr>
                             </thead>
                             <tbody>
                                <tr>
                                   <td>1</td>  
                                   <td><?php echo $charge->charge_name ?><br>
                                    <?php echo $charge->note;?>
                                  </td>                                 
                                   <td><?php											
									$discount_amount = calculatePercent($charge->apply_charge,$charge->discount_percentage);							   
                                    echo $discount_amount." (".$charge->discount_percentage."%)";?></td>								
									<td><?php 								
									
									$tax_amount = (($charge->apply_charge-$discount_amount)*$charge->tax/100) ;
                                                    $taxamount = amountFormat($tax_amount);												
													
                                   if($charge->tax>0)
                                    { 
										$tax=$taxamount;  
                                    }else{ 
										$tax=0; 
                                    } 
									
									echo amountFormat($tax)." (".$charge->tax."%)";?></td>
                                   <td class="text-right"><?php echo $charge->amount;?></td>
                                </tr>
                                <tr><td colspan="5" style="padding:0"></td></tr>
                                <tr>                                   
                                   <td colspan="4" class="text-right no-line"><?php echo $this->lang->line('net_amount');?></td>
                                   <td class="text-right no-line"><?php echo $currency_symbol.$charge->apply_charge; ?></td>
                                </tr>
								<tr>
                                    <td colspan="4" class="text-right no-line"><?php echo $this->lang->line('discount'); ?> </td>
                                    <td class="text-right no-line"><?php echo $currency_symbol . ($charge->apply_charge * $charge->discount_percentage / 100)." (".$charge->discount_percentage."%)"; ?></td>
                                </tr>                                      
                                <tr>                                   
                                   <td colspan="4" class="text-right no-line"><?php echo $this->lang->line('tax');?></td>
                                   <td class="text-right no-line"><?php 
								   
								   $discount_amount = calculatePercent($charge->apply_charge,$charge->discount_percentage);
								   $tax_amount = (($charge->apply_charge-$discount_amount)*$charge->tax/100) ;
                                                    $taxamount = amountFormat($tax_amount);
								   
                                   if($charge->tax>0){
                                     $tax_amt = $taxamount;
                                   }else{
                                      $tax_amt = 0;
                                   }
                                  
                                   $total = ($charge->amount);
                                   echo $currency_symbol.amountFormat($tax_amt)." (".$charge->tax."%)"; ?></td>
                                </tr>
                                <tr>
                                   <td colspan="4" class="text-right no-line"><?php echo $this->lang->line('total');?></td>
                                   <td class="text-right no-line"><?php 
                                   echo $currency_symbol.amountFormat($total); ?></td>
                                </tr>
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