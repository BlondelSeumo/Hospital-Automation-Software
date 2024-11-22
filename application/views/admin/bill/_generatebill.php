
<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
<div class="fixed-print-header">
  <?php if (!empty($print_details[0]['print_header'])) { ?>
                        <div>
                            <img src="<?php
                            if (!empty($print_details[0]['print_header'])) {
                                echo base_url() . $print_details[0]['print_header'].img_time();
                            }
                            ?>" class="img-responsive">
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
                              <p><?php echo $this->lang->line('patient'); ?> : <?php echo composePatientName($patient['patient_name'],$patient['patient_id']); ?></p>
                              <p><?php echo $this->lang->line('case_id'); ?> : <?php echo $case_id; ?></p>
                          </div>
                          <div class="col-md-6 text-right text-rtl-left">
                            <?php 
                            if($patient['date']!==''){
                              ?>
                              <p><span class="text-muted"><?php echo $this->lang->line('admission_date'); ?>:  </span> <?php echo $this->customlib->YYYYMMDDTodateFormat($patient['date']); ?></p>
                              <?php
                            }
                            ?>
                          </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="print-table">
                             <thead>
                                <tr class="line">
                                   <td>#</td>
                                   <td><?php echo $this->lang->line('description'); ?></td>
                                	 <td><?php echo $this->lang->line('qty'); ?></td>
                                	 <td><?php echo $this->lang->line('tax'); ?></td>
                                	 <td><?php echo $this->lang->line('discount'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('amount'). "(". $currency_symbol.")";?></td>
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
                                   <td ><?php echo amountFormat($tax).' ('.$value['tax'].'%)'; ?></td>
                                   <td ><?php echo (($value['apply_charge']*$value['discount_percentage'])/100).' ('.$value['discount_percentage'].'%)'; ?></td>
                                   <td class="text-right"><?php echo amountFormat($value['amount']) ?></td> 
                                </tr> 
                            <?php
                            $apply_charge+=$value['apply_charge'];
                            $amount+=$value['amount'];
                            $total_tax+=$tax;
                             } ?>
                              <tr>                                    
                                   <td colspan="5" class="text-right thick-line"><?php echo $this->lang->line('net_amount'); ?></td>
                                   <td class="text-right thick-line"><?php echo $currency_symbol.amountFormat($apply_charge);?></td>
                                </tr>                                
                                 <tr>
                                   <td colspan="5" class="text-right no-line"><?php echo $this->lang->line('tax'); ?></td>
                                   <td class="text-right no-line"><?php echo $currency_symbol.amountFormat($total_tax); ?></td>
                                </tr>
                                <tr>                                 
                                   <td colspan="5" class="text-right no-line"><?php echo $this->lang->line('total'); ?></td>
                                   <td class="text-right"><?php echo $currency_symbol.amountFormat($amount);?></td>
                                </tr>
                                 <tr> 
                                   <td colspan="5" class="text-right no-line"><?php echo $this->lang->line('paid'); ?></td>
                                   <td class="text-right no-line"><?php echo $currency_symbol.amountFormat($paid_amount['total_pay'])?></td>
                                </tr>
                                 <tr>
                                    <td colspan="5" class="text-right no-line"><?php echo $this->lang->line('due'); ?></td>
                                    <td class="text-right "><?php 
                                    if($refund>0){
                                      echo $currency_symbol.amountFormat(($amount-$paid_amount['total_pay'])-$refund);
                                    }else{
                                      echo $currency_symbol.amountFormat($amount-$paid_amount['total_pay']);
                                    }
                                    ?></td>
                                </tr>
                                <?php if(($paid_amount['total_pay']>$amount) || ($refund>0)){
                                  ?>
                                  <tr>
                                   <td colspan="5" class="text-right no-line"><?php echo $this->lang->line('refund'); ?>:</td>
                                   <td class="text-right"><?php if($refund>0){  echo $currency_symbol.amountFormat($refund); } ?></td>
                                </tr>
                              <?php } ?>
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