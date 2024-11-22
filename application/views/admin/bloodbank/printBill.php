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
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <p><?php echo $this->lang->line('bill_no'); ?> : <?php echo $this->customlib->getSessionPrefixByType('blood_bank_billing').$result["id"]; ?></p>
                        </div>
                        <div class="col-md-6 text-right">                            
                            <p><span class="text-muted"><?php echo $this->lang->line('date'); ?>: </span>
                            <?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['date_of_issue'], $this->customlib->getHospitalTimeFormat()); ?>
                            </p>
                        </div>
                        </div>
                    </div>
<br><br>
                    <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <table class="print-table">
                                <tr>
                                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                                    <td><?php echo composePatientName($result["patient_name"],$result["patient_id"]);; ?></td>
                                    <th><?php echo $this->lang->line('donor_name'); ?></th>
                                    <td><?php echo $result["donor_name"]; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('blood_group'); ?></th>
                                    <td><?php echo $result["blood_group"]; ?></td>
                                    <th><?php echo $this->lang->line('bags'); ?></th>
                                    <td><?php echo $result['bag_no']; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <th><?php echo $this->lang->line('amount'); ?></th>
                                    <td><?php echo $currency_symbol . " " . $result["net_amount"]; ?></td>                                   
                                </tr>
                          </table>
                        </div>
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