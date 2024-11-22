
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="fixed-print-header">
    <?php if (!empty($print_details[0]['print_header'])) {
                ?>
                    <div>
                        <img src="<?php
                                    if (!empty($print_details[0]['print_header'])) {
                                        echo base_url() . $print_details[0]['print_header'] . img_time();
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
<div id="html-2-pdfwrapper" class="p-1">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <div class="">
                <table width="100%">
                    <tr>
                        <td width="77%" class="rtl-text-right text-left">
                            <?php echo $this->lang->line('bill_no') ?><?php echo $this->customlib->getSessionPrefixByType('pharmacy_billing') . $result["id"] ?>
                        </td>
                        <td width="23%" class="text-right text-rtl-left">
                            <?php echo $this->lang->line('date') . " : "; ?><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])) ?>
                        </td>
                    </tr>
				</table>              
                <div class="divider mb-10"></div>
                <table class="printablea4" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <th width="10%"><?php echo $this->lang->line('name'); ?></th>
                        <td width="10%"><?php echo $result["patient_name"] . " (" . $result["patient_unique_id"] . ")"; ?></td>
                        <th width="10%"><?php echo $this->lang->line('phone'); ?></th>
                        <td width="10%"><?php echo $result["mobileno"]; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('doctor'); ?></th>
                        <td><?php echo $result["doctor_name"]; ?></td> 
                        <th><?php echo $this->lang->line('case_id') ?></th>
                        <td><?php echo $result["case_reference_id"]; ?></td>            
                    </tr>
                    <tr>                        
                        <th><?php echo $this->lang->line('prescription') ?></th>
                        <td><?php echo $prescription; ?></td>                    
                    </tr>
					
                    <?php if (!empty($fields)) {
                        foreach ($fields as $fields_key => $fields_value) {
                    ?>
                            <tr>
                                <th><?php echo $fields_value->name; ?></th>
                                <td colspan="3"><?php echo $result[$fields_value->name]; ?></td>
                            </tr>
                    <?php }
                    } ?>
				
                </table>
                <div class="divider mb-10 mt-10"></div>
                <table id="testreport" width="100%">
                    <tr>
                        <th width="20%"><?php echo $this->lang->line('medicine_category'); ?></th>
                        <th width="20%"><?php echo $this->lang->line('medicine_name'); ?></th>
                        <th><?php echo $this->lang->line('batch_no'); ?></th>
                        <th><?php echo $this->lang->line('unit'); ?></th>
                        <th><?php echo $this->lang->line('expiry_date'); ?></th>
                        <th><?php echo $this->lang->line('quantity'); ?></th>
                        <th class="text-right rtl-text-left"><?php echo $this->lang->line('tax'); ?></th>
                        <th class="text-right rtl-text-left"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                    </tr>
                    <?php
                    $j = $total_tax = 0;
					
                    foreach ($detail as $bill) {
						 
                        if ($bill['tax'] > 0) {
                            $tax = ((($bill["sale_price"] - (($bill["sale_price"]*$result["discount_percentage"]) / 100))* $bill['tax'])/100)*$bill["quantity"] ;
                        } else {
                            $tax = 0;
                        }

                        $total_tax += $tax;
                    ?>
                        <tr>
                            <td width="20%"><?php echo $bill["medicine_category"]; ?></td>
                            <td width="20%"><?php echo $bill["medicine_name"]; ?></td>
                            <td><?php echo $bill["batch_no"]; ?></td>
                            <td><?php echo $bill["unit_name"]; ?></td>
                            <td><?php echo $this->customlib->getMedicine_expire_month($bill['expiry']); ?></td>
                            <td><?php echo $bill["quantity"]; ?></td>
                            <td class="text-right rtl-text-left"><?php echo $bill['tax'] . "%";	?></td>
                            <td class="text-right rtl-text-left"><?php echo amountFormat($bill["sale_price"] * $bill["quantity"]); ?></td>
                        </tr>
                    <?php
                        $j++;
                    }
                    ?>
                </table>
                <div class="divider mb-10 mt-10"></div>
                <div class="row">
                    <div class="col-sm-6">
                        <table class="printablea4" width="100%">
                            <?php if (!empty($result["note"])) { ?>
                                <tr>
                                    <th width="20%"><?php echo $this->lang->line('note'); ?></th>
                                    <td><?php echo $result["note"]; ?></td>
                                </tr>
                            <?php }

                            if (!$print) {
                            ?>
                                <tr id="generated_by">
                                    <th width="20%"><?php echo $this->lang->line('collected_by'); ?></th>
                                    <td><?php echo composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']); ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>
                    <div class="col-sm-6">  
                        <table class="printablea4" width="100%" cellpadding="0" cellspacing="0"> 
                            <?php if (!empty($result["total"])) { ?>
                                <tr>
                                    <th width="80%" class="text-right rtl-text-left"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                    <td width="20%" class="text-right rtl-text-left"><?php echo amountFormat($result["total"]); ?></td>
                                </tr>
                            <?php } ?>
                            <?php if (!empty($result["discount"])) {
                            ?>
                                <tr>
                                    <th class="text-right rtl-text-left"><?php echo $this->lang->line('discount') . " (" . $result["discount_percentage"] ."%)"; ?></th>
                                    <td class="text-right rtl-text-left"><?php echo amountFormat($result["discount"]); ?></td> 

                                </tr>
                            <?php } ?>
                            <?php if (!empty($total_tax)) {
                            ?>
                                <tr>
                                    <th class="text-right rtl-text-left"><?php  echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                    <td class="text-right rtl-text-left"><?php echo amountFormat($total_tax); ?></td>
                                </tr>
                            <?php } ?>

                            <?php
                            if ((!empty($result["discount"])) && (!empty($result["tax"]))) {
                                if (!empty($result["net_amount"])) {
                            ?>
                                    <tr>
                                        <th class="text-right rtl-text-left"><?php  echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")";  ?></th>
                                        <td class="text-right rtl-text-left"><?php echo amountFormat($result["net_amount"]); ?></td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                            <tr>
                                <th class="text-right rtl-text-left"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")";  ?></th>
                                <td class="text-right rtl-text-left"><?php echo amountFormat($result["paid_amount"]); ?></td>
                            </tr>
                            <tr>
                                <th class="text-right rtl-text-left"><?php echo $this->lang->line('refund_amount') . " (" . $currency_symbol . ")";  ?></th>
                                <td class="text-right rtl-text-left"><?php echo amountFormat($result["refund_amount"]); ?></td>
                            </tr>
                            <tr>
                                <th class="text-right rtl-text-left"><?php echo $this->lang->line('due_amount') . " (" . $currency_symbol . ")"; ?></th>
                                <td class="text-right rtl-text-left"><?php echo amountFormat(($result["net_amount"] + $result["refund_amount"]) - $result['paid_amount']);  ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="divider mb-10 mt-10"></div>
                    
                </div>
        </div>
        <!--/.col (left) -->
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