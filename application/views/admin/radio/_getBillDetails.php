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
                        <div class="col-md-6">
							<table class="noborder_table mb-0">	
								<tr>
                                    <th width="25%"><?php echo $this->lang->line("bill_no"); ?></th>
                                    <td width="35%" ><?php echo $bill_prefix.$result->id; ?></td>
                                </tr>
								<tr>
                                    <th width="25%"><?php echo $this->lang->line('patient'); ?></th>
                                    <td width="35%" ><?php echo composePatientName($result->patient_name,$result->patient_id); ?></td>
                                </tr>
								<tr>
                                    <th width="25%"><?php echo $this->lang->line('prescription_no'); ?></th>
                                    <td width="35%" ><?php echo $prescription; ?></td>
                                </tr>
								<tr>
                                    <th width="25%"><?php echo $this->lang->line("gender"); ?></th>
                                    <td width="35%" ><?php echo $result->gender; ?></td>
                                </tr>								
							</table>                                                       
                        </div>						
                        <div class="col-md-6 text-left">   
							<table class="noborder_table mb-0">	
								<tr>
									<th width="25%"><?php echo $this->lang->line("date"); ?></th>
                                    <td width="35%"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result->date, $this->customlib->getHospitalTimeFormat()); ?></td>	
								</tr>
								<tr>
									<th width="25%"><?php echo $this->lang->line('case_id'); ?></th>
                                    <td width="35%"><?php echo $result->case_reference_id; ?></td>	
								</tr>
								<tr>
									<th width="25%"><?php echo $this->lang->line("age"); ?></th>
                                    <td width="35%"><?php echo $this->customlib->getPatientAge($result->age,$result->month,$result->day); ?></td>	
								</tr>								
							</table>
                        </div>					
                    </div>
					<div class="row">
                        <div class="col-md-12">
							<table class="noborder_table mb-0">	
					
							<?php  if (!empty($fields)) {
                                foreach ($fields as $fields_key => $fields_value) {
                                    $display_field = $result->{"$fields_value->name"};
                                    if ($fields_value->type == "link") {
                                        $display_field = "<a href=" . $result->{"$fields_value->name"} . " target='_blank'>" . $result->{"$fields_value->name"} . "</a>";
                                    }
                                    ?>
									<tr>
                                    <th width="20.5%"><?php echo $fields_value->name; ?></th>
									<td> <?php echo $display_field; ?></td>
									</tr>	
                                <?php }
                            }?> 
							
							</table>
						</div>
					</div>					
					
                    <div class="row">
                        <div class="col-md-12">
                            <table class="print-table mb-0">
                             <thead>
                                <tr class="line">
                                   <td>#</td>
                                   <td class="text-left"><?php echo $this->lang->line('test_name'); ?></td>
                                   <td><?php echo $this->lang->line('date'); ?></td>
                                   <td><?php echo $this->lang->line('tax'); ?> (%)</td>
                                   <td class="text-right"><?php echo $this->lang->line('amount').' ('.$currency_symbol.')'; ?></td>
                                </tr>
                             </thead>
                             <tbody>
                                <?php
                          $row_counter=1;
                          $tax_amt=0;                       
                        foreach ($result->radiology_report as $report_key=> $report_value) {
                            $amount+=$report_value->apply_charge;
                            if($report_value->tax_percentage>0){
                            $tax_amt +=($report_value->apply_charge*$report_value->tax_percentage/100);
                            }else{
                              $tax_amt +=0;
                            }                           
                            ?>
                        <tr>
                            <td><?php echo $row_counter; ?></td>
                            <td>
								<?php echo $report_value->test_name; ?> <?php echo "(".$report_value->short_name.")"; ?>
                            </td>                               
                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat($report_value->reporting_date); ?></td>
                            <td><?php echo calculatePercent($report_value->apply_charge,$report_value->tax_percentage)." (".$report_value->tax_percentage."%)"; ?></td>
                            <td class="text-right"><?php echo $report_value->apply_charge; ?></td>                        
                        </tr>                               
                        <?php
                    $row_counter++;
                        }
                        ?> 
                        <tr><td colspan="5" class="p-0" style="padding:0"></td></tr>                    
                        <tr>
                           <td colspan="3" class="no-line"></td>
                           <td class="text-right no-line"><?php echo $this->lang->line('total'); ?></td>
                           <td class="text-right no-line"><?php echo $currency_symbol . "" . amountFormat($amount); ?></td>
                        </tr>
                        <tr>
                           <td colspan="3" class="no-line"></td>
                           <td class="text-right no-line"><?php echo $this->lang->line('discount'); ?></td>
                           <td class="text-right no-line"><?php echo $currency_symbol .amountFormat($result->discount)." (".$result->discount_percentage."%) "; ?></td>
                        </tr>
                        <tr>
                           <td colspan="3" class="no-line"></td>
                           <td class="text-right no-line"><?php echo $this->lang->line('tax'); ?></td>
                           <td class="text-right no-line"><?php echo $currency_symbol . "" . amountFormat($tax_amt); ?></td>
                        </tr>
                           <tr>
                           <td colspan="3" class="no-line"></td>
                           <td class="text-right no-line"><?php echo $this->lang->line('net_amount'); ?></td>
                           <td class="text-right no-line"><?php echo $currency_symbol . "" . amountFormat($result->net_amount); ?></td>
                        </tr>
                        <tr>
                           <td colspan="3" class="no-line"></td>
                           <td class="text-right no-line"><?php echo $this->lang->line('paid'); ?></td>
                           <td class="text-right no-line"><?php echo $currency_symbol . "" . amountFormat($result->total_deposit); ?></td>
                        </tr>
                        <tr>
                           <td colspan="3" class="no-line"></td>
                           <td class="text-right no-line"><?php echo $this->lang->line('total_due'); ?></td>
                           <td class="text-right no-line"><?php echo $currency_symbol . "" . amountFormat($result->net_amount-$result->total_deposit); ?></td>
                        </tr>
                        <tr><td colspan="5"></td></tr>   
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