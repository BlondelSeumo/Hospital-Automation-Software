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
  <?php 
$discont_amt=calculatePercent($blood_issues_detail['amount'], $blood_issues_detail['discount_percentage']);
$total_discount_amount = $blood_issues_detail['amount'] - $discont_amt;
$tax_amt=calculatePercent($total_discount_amount, $blood_issues_detail['tax_percentage']);
   ?>

<div class="row">
        <div class="col-12">
           
              <div class="card">
                <div class="card-body">  
                    <div class="row">						
						<div class="col-md-6">
							<table class="noborder_table mb-0">
                                <tr>
                                    <th width="15%"><?php echo $this->lang->line('bill_no'); ?></th>
                                    <td width="35%"><?php echo $prefix.$blood_issues_detail['id']; ?></td>
                                </tr>
								<tr>
                                    <th width="15%"><?php echo $this->lang->line('patient_name'); ?></th>
                                    <td width="35%"><?php echo $blood_issues_detail['patient_name']." (".$blood_issues_detail['patient_id'].")"; ?></td>
                                </tr>
								<tr>
                                    <th width="15%"><?php echo $this->lang->line('blood_group'); ?></th>
                                    <td width="35%"><?php echo $blood_issues_detail['blood_group']; ?></td>
                                </tr>
                             </table>
						</div>
						<div class="col-md-6">
							<table class="noborder_table mb-0">
                                <tr>
                                    <th width="15%"><?php echo $this->lang->line('issue_date'); ?></th>
                                    <td width="35%"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($blood_issues_detail['date_of_issue'], $this->customlib->getHospitalTimeFormat()); ?></td>
                                </tr>
								<tr>
                                    <th width="15%"><?php echo $this->lang->line('case_id'); ?></th>
                                    <td width="35%"><?php echo $blood_issues_detail['case_reference_id']; ?></td>
                                </tr>
								<tr>
                                    <th width="15%"><?php echo $this->lang->line('bag'); ?></th>
                                    <td width="35%"><?php echo $this->customlib->bag_string($blood_issues_detail['bag_no'],$blood_issues_detail['volume'],$blood_issues_detail['unit']); ?></td>
                                </tr>
                             </table>
						</div>					
                    </div>
					
					<div class="row">						
						<div class="col-md-12">
							<table class="noborder_table mb-0">
					
								<?php    if (!empty($fields)) {
								foreach ($fields as $fields_key => $fields_value) {
                                  $display_field = $blood_issues_detail["$fields_value->name"];
                                  if ($fields_value->type == "link") {
                                      $display_field = "<a href=" . $blood_issues_detail["$fields_value->name"] . " target='_blank'>" . $blood_issues_detail["$fields_value->name"] . "</a>";
                                  }
                                   ?>
									<tr>
										<th width="15%"><?php echo $fields_value->name; ?></th> 
										<td><?php echo $display_field; ?></td> 
									</tr>
                                <?php  }
								} ?>
							  
							</table>
						</div>					
                    </div>							  
							  
                    <div class="row">
                        <div class="col-md-12">
                            <table class="print-table mb-0">
                             <thead>
                                <tr class="line">
                                   <td>#</td>
                                   <td class="text-left"><?php echo $this->lang->line('description'); ?></td>                                 
                                   <td class="text-center"><?php echo $this->lang->line('tax'); ?> (%)</td>
                                   <td class="text-right"><?php echo $this->lang->line('amount').' ('. $currency_symbol .')'; ?></td>
                                </tr>
                             </thead>
                             <tbody>
                                <tr>
                                   <td>1</td>
                                   <td><?php echo $blood_issues_detail['charge_category_name'];?><br></td>
                                    <td class="text-center"><?php echo calculatePercent($blood_issues_detail['amount'], $blood_issues_detail['tax_percentage']);?><br>
                                    </td>                                 
                                   <td class="text-right"><?php echo $blood_issues_detail['amount'] ?></td>
                                </tr>
                                <tr><td colspan="4" class="p-0" style="padding:0"></td></tr>
                                <tr>
                                   <td colspan="2" class="no-line"></td>
                                   <td class="text-right no-line"><?php echo $this->lang->line('total'); ?></td>
                                   <td class="text-right no-line"><?php echo $currency_symbol . "" . amountFormat($blood_issues_detail['amount']); ?></td>
                                </tr>
                                <tr>
                                   <td colspan="2" class="no-line"></td>
                                   <td class="text-right no-line"><?php echo $this->lang->line('discount'); ?></td>
                                   <td class="text-right no-line"><?php echo $currency_symbol . "" . $discont_amt. " (".$blood_issues_detail['discount_percentage']."%) "; ?></td>
                                </tr>
                                <tr>
                                   <td colspan="2" class="no-line"></td>
                                   <td class="text-right no-line"><?php echo $this->lang->line('tax'); ?></td>
                                   <td class="text-right no-line"><?php echo $currency_symbol . "" . $tax_amt ." (".$blood_issues_detail['tax_percentage']."%) "; ?></td>
                                </tr>
                                <tr>
                                   <td colspan="2" class="no-line"></td>
                                   <td class="text-right no-line"><?php echo $this->lang->line('paid'); ?></td>
                                   <td class="text-right no-line"><?php echo $currency_symbol . "" . amountFormat($blood_issues_detail['total_deposit']); ?></td>
                                </tr>
                                  <tr>
                                   <td colspan="2" class="no-line"></td>
                                   <td class="text-right no-line"><?php echo $this->lang->line('total_due'); ?></td>
                                   <td class="text-right no-line"><?php echo $currency_symbol . "" . amountFormat(($blood_issues_detail['amount']-$discont_amt)+$tax_amt-$blood_issues_detail['total_deposit']); ?></td>
                                </tr>
                             </tbody>
                          </table>
                        </div>
                    </div>              
                </div>
            </div>
            <div class="divider"></div>
               
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