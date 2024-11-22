<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>     
    </head>
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
                <div>
                    
                    <table width="100%">
                        <tr>
                            <td class="text-left"><b><?php echo $this->lang->line('bill'); ?></b>: <?php echo $this->customlib->getPatientSessionPrefixByType('ambulance_call_billing').$id ?>
                            </td>
                            <td class="text-right"><b><?php echo $this->lang->line('date'); ?> <?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])) ?>
                            </td>
                        </tr>
                    </table>
                    <div class="divider mb-10"></div>
                    <table class="printablea4" cellspacing="0" cellpadding="0" width="100%">
                        <tr> 
                            <th width="20%"><?php echo $this->lang->line('patient_name'); ?></th>
                            <td width="25%"><?php echo composePatientName($result['patient'],$result['patient_id']); ?></td>
                            <th width="25%"><?php echo $this->lang->line('driver_name'); ?></th>
                            <td width="30%" class="text-left"><?php echo $result["driver"]; ?></td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('vehicle_number'); ?></th>
                            <td width="25%"><?php echo $result["vehicle_no"]; ?></td>
                            <th width="25%"><?php echo $this->lang->line('vehicle_model'); ?></th>
                            <td width="30%" class="text-left"><?php echo $result['vehicle_model']; ?></td> 
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
                            <td width="25%">
                                <?php
                                
                                 if($superadmin_restriction == 'disabled' && $result['staff_roles_id'] == 7){
                  
                                 }else{
                                    echo composeStaffNameByString($result['staff_name'], $result['staff_surname'], $result['staff_employee_id']);
                                 }
                                 
                                 ?>
                            </td>
                        </tr>
                        <?php

                        if (!empty($fields)) {
                          foreach ($fields as $fields_key => $fields_value) {

                        $display_field = $result["$fields_value->name"];
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $result["$fields_value->name"] . " target='_blank'>" . $result["$fields_value->name"] . "</a>";
                        }
                         ?>
                <tr>
                    <th width="10%"><?php echo $fields_value->name; ?></th> 
                    <td width="10%"><?php echo $display_field; ?></td>
                </tr>
                  <?php  }
                } 
                ?> 
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                    <table class="printablea4 mb-10" style="width: 50%; float: right;">
                        <?php if (!empty($result["amount"])) { ?>
                            <tr>
                                <th class="mb10 text-right"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                <td class="text-right mb10"><?php echo $result["amount"]; ?></td>
                            </tr>
                        <?php } ?>      
                        
                        <?php if (!empty($result["discount"])) { ?>                        
                                <tr>
                                    <th class="mb10 text-right"><?php
                                        echo $this->lang->line('discount') . " (" .$result["discount_percentage"] . "%)" . " (" . $currency_symbol . ")";
                                        ?></th>
                                    <td class="mb10 text-right"><?php echo $result["discount"]  ; ?></td>
                                </tr>                                
                        <?php } ?>   

                        <?php if (!empty($result["tax_percentage"])) { ?>
                            <tr>
                                <th  class="mb10 text-right"><?php
                                    echo $this->lang->line('tax') . " (".$result["tax_percentage"]."%)"  . " (" . $currency_symbol . ")"; 
									$final_amount = $result["standard_charge"]-$result["discount"];
									?></th>
                                <td  class="mb10 text-right"><?php echo calculatePercent($final_amount, $result["tax_percentage"]) ; ?></td>
                            </tr>
                        <?php } ?>  
                              <?php
                        if (!empty($result["net_amount"])) { 
                                ?>
                                <tr>
                                    <th class="mb10 text-right"><?php
                                        echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")";
                                        ;
                                        ?></th>
                                    <td class="mb10 text-right"><?php echo $result["net_amount"]; ?></td>
                                </tr>
                                <?php    
                        }
                        ?>
                              <?php if (!empty($result["paid_amount"])) { ?>
                            <tr>
                                <th class="mb10 text-right"><?php
                                    echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")";
                                    ;
                                    ?></th>
                                <td class="mb10 text-right"><?php echo  $result["paid_amount"]; ?></td>
                            </tr>
                        <?php } ?>
                     <tr>
                                <th class="mb10 text-right"><?php

                               
                                    echo $this->lang->line('due_amount') . " (" . $currency_symbol . ")";
                                    ;
                                    ?></th>
                                <td class="mb10 text-right"><?php echo  amountFormat($result["net_amount"]-$result["paid_amount"]); ?></td>
                            </tr>                     
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                     <div class="table-responsive">       
                    <table style="width: 100%;">
                        <tr>
                        <?php if (!empty($result["note"])) { ?>
                             <td width="60%"><?php echo $this->lang->line('note') ." : ".$result["note"]; ?></td>                         
                        <?php } ?>                       
                        </tr>
                    </table>
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
</html>

<script type="text/javascript">
    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/dashboard/getBillDetailsAmbulance/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }
    
</script>