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
        <div class="col-12">
           <div class="card">
                <div class="card-body">  
                    <div class="row">
                        <div class="col-md-6">
                            <p><?php echo $patient['patient_name']; ?></p>
                            <p><?php echo $this->lang->line('patient_id'); ?> : <?php echo $patient['id']; ?></p>
                            <p><?php echo $this->lang->line('case_id'); ?> : <?php echo $case_id; ?></p>
                        </div>

                        <div class="col-md-6 text-right">
                             <?php 
                             if($module_type=='ipd_id'){
                              ?>
                              <p><span class="text-muted"><?php echo $this->lang->line('admission_date'); ?>: </span> <?php echo $this->customlib->YYYYMMDDTodateFormat($patient['admission_date']); ?></p>
                              <?php
                             }
                             ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                        	<?php $total=0;
                        	foreach ($all_paymets as $all_paymetskey => $all_paymetsvalue) { ?>
                              <table class="print-table">
                             <thead>
                                <tr class="line">
                                   <td><?php echo $this->lang->line('transaction_id'); ?></td>
                                   <td class="text-center"><?php echo $this->lang->line('description'); ?></td>
                                   <td><?php echo $this->lang->line('payment_date'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('amount'); ?>(<?php echo $currency_symbol;?>)</td>
                                </tr>
                             </thead>
                             <tbody>
                             	<?php 
                             	if(!empty($all_paymetsvalue['result'])){
                             	foreach ($all_paymetsvalue['result'] as $key => $transaction) { ?>
                              
                               <?php 
if($transaction['type'] == "payment"){
$payment_type= $this->lang->line("payment_received");
}elseif ($transaction['type'] == "refund") {
 $payment_type= $this->lang->line("payment_refund");
} ?>
                                  <tr>
                                   <td><?php echo $transaction['id'];?></td>
                                   <td><?php echo $payment_type; ?><br>
                                    <?php

                                     echo $this->lang->line("by"). ": ".$this->lang->line(strtolower($transaction['payment_mode']));
                                     if($transaction['payment_mode'] == "Cheque"){
                                       echo " ".$transaction['cheque_no'];
                                     }
                                      if($transaction['payment_mode'] == "Cheque"){
                                    echo "<br>";
                                       echo $this->customlib->YYYYMMDDTodateFormat($transaction['cheque_date']);
                                     }
                                      ?></td>
                                  <td><?php echo $this->customlib->YYYYMMDDTodateFormat($transaction['payment_date']); ?></td>
                                   <td class="text-right"><?php echo amountFormat($transaction['amount']) ?></td>
                                </tr>
                                
                                
                            <?php $total+=$transaction['amount']; } } ?>
                            
                                <?php if(!empty($charge_details)){?>
                                <tr>
                                   <td colspan="2"></td>
                                   <td class="text-right"><?php echo $this->lang->line('standard_charge'); ?></td>
                                   <td class="text-right"><?php echo $currency_symbol.amountFormat($charge_details->standard_charge); ?></td>
                                </tr>
                                <tr>
                                   <td colspan="2"></td>
                                   <td class="text-right"><?php echo $this->lang->line('apply_charge'); ?></td>
                                   <td class="text-right"><?php echo $currency_symbol.amountFormat($charge_details->apply_charge); ?></td>
                                </tr>
                              <?php }?>
                              <tr>
                                   <td colspan="2"></td>
                                   <td class="text-right"><?php echo $this->lang->line('total_paid'); ?></td>
                                   <td class="text-right"><?php echo $currency_symbol.amountFormat($total); ?></td>
                                </tr>
                             </tbody>
                          </table>
                      <?php  } ?>
                        </div>
                    </div>
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