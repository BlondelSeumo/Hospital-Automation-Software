
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();

if($charge->opd_id != "")
{
$patient_name=$charge->opd_patient_name;
$patient_id=$charge->opd_patient_id;
$case_reference_id=$charge->opd_case_reference_id;
}else{
$patient_name=$charge->ipd_patient_name;
$patient_id=$charge->ipd_patient_id;
$case_reference_id=$charge->ipd_case_reference_id;
}
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
        <div class="col-12">
           
            <div class="card">
                <div class="card-body">  
                    <div class="row">
                        <div class="col-md-6">
                            <p class="font-bold"><?php echo $patient_name; ?></p>
                            <p><?php echo $this->lang->line('checkup_id'); ?> : <?php echo $patient['id']; ?></p>
                            <p><?php echo $this->lang->line('patient_id'); ?> : <?php echo $patient_id; ?></p>
                            <p><?php echo $this->lang->line('case_id'); ?> : <?php echo $case_reference_id; ?></p>
                            
                        </div>
                        <div class="col-md-6 text-right">
                            <p><span class="font-bold"><?php echo $this->lang->line('date'); ?>: </span> <?php echo $this->customlib->YYYYMMDDTodateFormat($charge->date); ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                              <table class="print-table">
                             <thead>
                                <tr class="line">
                                   <td>#</td>
                                   <td class="text-left"><?php echo $this->lang->line('description'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('amount').' ('. $currency_symbol .')'; ?></td>
                                </tr>
                             </thead>
                             <tbody>
                                <tr>
                                   <td>1</td>
                                   <td><?php echo $charge->charge_name ?><br>
                                    <?php echo $charge->note;?>
                                  </td>
                                   <td class="text-right"><?php echo $charge->apply_charge ?></td>
                                </tr>                                
                                <tr>
                                   <td colspan="1" class="thick-line"></td>
                                   <td class="text-right thick-line"><?php echo $this->lang->line('total'); ?></td>
                                   <td class="text-right thick-line"><?php echo $currency_symbol . "" .$charge->apply_charge ?>
                                   </td>
                                </tr>
                                 <tr>
                                   <td colspan="1" class="no-line"></td>
                                   <td class="text-right no-line"><?php echo $this->lang->line('paid'); ?></td>
                                   <td class="text-right no-line"><?php echo $currency_symbol . "" .$transaction->amount; ?>
                                   </td>
                                </tr>
                                   <tr>
                                   <td colspan="1" class="no-line"></td>
                                   <td class="text-right no-line"><?php echo $this->lang->line('total_due'); ?></td>
                                   <td class="text-right no-line"><?php echo $currency_symbol . "" .amountFormat($charge->apply_charge-$transaction->amount); ?>
                                   </td>
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