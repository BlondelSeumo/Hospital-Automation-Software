<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
if($module=='radiology_billing_id'){
 $print='print_receipt';
}elseif($module=='pathology_billing_id'){
 $print='print_trans';
}else{
 $print='print_trans'; 
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover example">
                <thead>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th><?php echo $this->lang->line('note'); ?></th>
                    <th><?php echo $this->lang->line('payment_mode'); ?></th>
                    <th class="text-right"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                    <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                </thead>
                <tbody>
                    <?php					 
                            $total = 0;
							if (!empty($payment_details)) {                                                
                                                foreach ($payment_details as $payment) {                                                   
                                                    if (!empty($payment['amount'])) {
                                                        $total += $payment['amount'];
                                                    }
                                                    ?>
                <tr>
                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment['payment_date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                        <td><?php echo $payment["note"] ?></td>
                                                      <td><?php echo $this->lang->line(strtolower($payment["payment_mode"]))."<br>";
                                                        if($payment['payment_mode'] == "Cheque"){
                                                             if($payment['cheque_no']!=''){
                                       echo $this->lang->line("cheque_no"). ": ".$payment['cheque_no'];                                      
                                    echo "<br>";
                                }
                                    if($payment['cheque_date']!='' && $payment['cheque_date']!='0000-00-00'){
                                       echo $this->lang->line("cheque_date"). ": ".$this->customlib->YYYYMMDDTodateFormat($payment['cheque_date']);
                                   }                                     

                                     }
                                                        ?>
                    </td> 
                    <td class="text-right"><?php echo amountFormat($payment["amount"]) ?></td>
                    <td class="text-right">
                                                        <?php  if ($payment['payment_mode'] == "Cheque" && $payment['attachment'] != "")  {
                                                    ?>
                                                    <a href='<?php echo site_url('admin/transaction/download/'.$payment['id']);?>' class='btn btn-default btn-xs'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
                                                    <?php
                                                }
                                                         ?>
                                                         <a href="javascript:void(0);" class="btn btn-default btn-xs <?php echo $print; ?> " data-toggle="tooltip" title="" data-loading-text="<i class='fa fa-circle-o-notch fa-spi'></i>" data-record-id="<?php echo $payment['id']; ?>"  data-original-title="<?php echo $this->lang->line('print'); ?>">
                                                                    <i class="fa fa-print"></i>
                                                                </a> 
                    </td>
                </tr>
                                        <?php                                       
                                         } }?>  
                 <tr class="box box-solid total-bg">
                    <td></td>
                    <td></td>
                    <td class="text-right"><?php echo $this->lang->line('total'); ?></td>
                    <td class="text-right"><?php echo $currency_symbol.amountFormat($total); ?></td>
                    <td></td>                                 
                </tr>
                </tbody>
            </table>                        
        </div>
    </div>
</div>                                