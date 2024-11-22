<?php
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

    <div id="html-2-pdfwrapper" class="p-1">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
            <div class="">
                <div class="">
                    
                    <table width="100%">
                        <tr>
                            <td><?php echo $this->lang->line('bill_no'); ?> <?php echo $this->customlib->getSessionPrefixByType('ambulance_call_billing').$result["id"] ?></td>
                            <td class="text-right rtl-text-left"><?php echo $this->lang->line('date') . " : "; ?><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])) ?>
                            </td>
                        </tr>
                    </table>
                    <div class="divider mb-10"></div>
                    <table class="printablea4" width="100%">
                        <tr> 
                            <th width="20%"><?php echo $this->lang->line('patient_name'); ?></th>
                            <td width="25%"><?php echo composePatientName($result['patientname'],$result['patient_id']); ?></td>
                            <th width="25%"><?php echo $this->lang->line('driver_name'); ?></th>
                            <td width="30%"><?php echo $result["driver"]; ?></td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('vehicle_number'); ?></th>
                            <td width="25%"><?php echo $result["vehicle_no"]; ?></td>
                            <th width="25%"><?php echo $this->lang->line('vehicle_model'); ?></th>
                            <td width="30%"><?php echo $result['vehicle_model']; ?></td> 
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('case_id'); ?></th>
                            <td width="25%"><?php echo $result["case_reference_id"]; ?></td>
                            <th width="20%"><?php echo $this->lang->line('charge_category'); ?></th>
                            <td width="25%"><?php echo $result["charge_category_name"]; ?></td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('charge_name'); ?></th>
                            <td width="25%"><?php echo $result["charge_name"]; ?></td>
                            <th width="20%"><?php echo $this->lang->line('collected_by'); ?></th>
                            <td width="25%"><?php echo composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']); ?></td>
                        </tr>
                        <?php
                        if($print=='no'){

                          if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $result["$fields_value->name"];
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $result["$fields_value->name"] . " target='_blank'>" . $result["$fields_value->name"] . "</a>";
                        }
                         ?>
                <tr>
                    <th width="10%"><?php echo $fields_value->name; ?></th> 
                    <td width="10%" colspan="3"><?php echo $display_field; ?></td>
                </tr>
                  <?php  }
                }
            }else{
                if (!empty($print_fields)) {
                    foreach ($print_fields as $fields_key => $fields_value) {
                        $display_field = $result["$fields_value->name"];
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $result["$fields_value->name"] . " target='_blank'>" . $result["$fields_value->name"] . "</a>";
                        }
                         ?>
                <tr>
                    <th width="10%"><?php echo $fields_value->name; ?></th> 
                    <td width="10%" colspan="3"><?php echo $display_field; ?></td>
                </tr>
                  <?php  }
                }
            }
                ?> 
                    </table>                    
                    <div class="divider mt-10 mb-10"></div>
                    <table class="printablea4" style="width: 40%; float: right; padding-bottom:10px;">
                        <?php if (!empty($result["standard_charge"])) { ?>
                            <tr>
                                <th width="60%" class="text-right rtl-text-left"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?></th>
                                <td width="40%" class="text-right rtl-text-left"><?php echo number_format((float)$result["standard_charge"], 2, '.', ''); ?></td>
                            </tr>
                        <?php } ?>
                        
                        <?php if (!empty($result["amount"])) { ?>
                            <tr>
                                <th width="60%" class="text-right rtl-text-left"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                <td width="40%" class="text-right rtl-text-left"><?php echo $result["amount"]; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($result["discount"])) { ?>                        
                                <tr>
                                    <th width="60%" class="text-right rtl-text-left"><?php
                                        echo $this->lang->line('discount') . " (" .$result["discount_percentage"] . "%)";
                                        ?></th>
                                    <td width="40%" class="text-right rtl-text-left"><?php echo $result["discount"]  ; ?></td>
                                </tr>                                
                        <?php } ?>        
                        <?php if (!empty($result["tax_percentage"])) { ?>
                            <tr>
                                <th width="60%" class="text-right rtl-text-left"><?php
                                    echo $this->lang->line('tax') . " (".$result["tax_percentage"]."%)"; 
									$final_amount = $result["standard_charge"]-$result["discount"];
									?></th>
                                <td width="40%" class="text-right rtl-text-left"><?php echo calculatePercent($final_amount, $result["tax_percentage"]) ; ?></td>
                            </tr>
                        <?php } ?>                        
                        <?php
                        if (!empty($result["net_amount"])) {                       
                                ?>
                                <tr>
                                    <th width="60%" class="text-right rtl-text-left"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <td width="40%" class="text-right rtl-text-left"><?php echo $result["net_amount"]; ?></td>
                                </tr>
                                <?php    
                        }
                        ?> 
                            <?php
                        if (!empty($result["total_paid"])) {
                       
                                ?>
                                <tr>
                                    <th width="60%" class="text-right rtl-text-left"><?php  echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")";  ?></th>
                                    <td width="40%" class="text-right rtl-text-left"><?php echo $result["total_paid"]; ?></td>
                                </tr>
                                <?php
    
                        }
                        ?> 
                                <tr>
                                    <th width="60%" class="text-right rtl-text-left"><?php echo $this->lang->line('due_amount') . " (" . $currency_symbol . ")";   ?></th>
                                    <td width="40%" class="text-right rtl-text-left"><?php echo number_format($result["net_amount"]-$result["total_paid"]); ?></td>
                                </tr>                     
                    </table>
                    
                    <?php if (!empty($result["note"])) { ?>   
                        <div class="divider mt-10 mb-10"></div> 
                         <div class="table-responsive">          
                            <table>           
                                <tr><td><?php echo $this->lang->line('note'); ?>: <?php echo $result["note"]; ?></td></tr>                                            
                            </table>
                        </div> 
					<?php } ?>
            
            <div class="divider mb-10"></div>
                </div>
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


<script type="text/javascript">
    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/vehicle/deleteCallAmbulance/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    } 
	
    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/vehicle/getBillDetails/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }    
</script>