<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
        <style type="text/css">
            .printablea4{width: 100%;}
            /*.printablea4 p{margin-bottom: 0;}*/
            .printablea4>tbody>tr>th,
            .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 12px;}
        </style>
    </head>
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
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="">
                   
                     <table class="printablea4" cellspacing="0" width="100%">
                        <tr>
                            <td width="33%" class="text-left"><?php echo $this->lang->line('recept')."# ".$result["id"]; ?></td>
                            <td width="33%" class="text-center"><?php echo $this->lang->line('bill')." ".$this->lang->line('no')."# ".$this->customlib->getSessionPrefixByType('radiology_billing').$result["id"]; ?></td>
                            <td width="33%" class="text-right"><?php echo $this->lang->line('date').": ".date($this->customlib->getHospitalDateFormat(true, false), strtotime($result["date"])); ?></td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                     <table class="printablea4" cellspacing="0" width="100%">
                        <tr>
                            <th width="10%"><?php echo $this->lang->line('patient')." ".$this->lang->line('id'); ?></th>
                            <td width="10%"><?php echo $result["patient_unique_id"]; ?></td>
                            <th width="10%"><?php echo $this->lang->line('name'); ?></th>
                            <td width="10%"><?php echo $result["patient_name"]; ?></td>
                            <th width="10%"><?php echo $this->lang->line('gender'); ?></th>
                            <td width="10%" class=""><?php echo $result["gender"] ; ?></td>
                            <th width="10%"><?php echo $this->lang->line('age'); ?></th>
                            <td width="10%"><?php if (!empty($result['age'])) {
                               echo $result["age"]." ". $this->lang->line('years') ;
                            }  ?></td>
                        </tr>
                        <tr>
                            <th width="10%"><?php echo $this->lang->line('doctor'); ?></th>
                            <td width="10%"><?php echo $result["doctor_name"]; ?></td> 
                            <th width="10%"><?php echo $this->lang->line('phone'); ?></th>
                            <td width="10%"><?php echo $result["mobileno"] ; ?></td> 
                            <th width="10%"><?php echo $this->lang->line('email'); ?></th>
                            <td width="10%"><?php echo $result["email"] ; ?></td>
                            <th width="10%"><?php echo $this->lang->line('address'); ?></th>
                            <td width="10%"><?php echo $result["address"] ; ?></td> 
                        </tr> 
                        <tr>
                            <th width="10%"><?php echo $this->lang->line('blood_group'); ?></th>
                            <td width="10%"><?php echo $result["blood_group"] ; ?></td> 
                        </tr>                        
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <table class="printablea4" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></th> 
                            <th><?php echo $this->lang->line('short') . " " . $this->lang->line('name'); ?></th>
                            <th><?php echo $this->lang->line('report') . " " . $this->lang->line('date'); ?></th>
                            <th><?php echo $this->lang->line('amount'); ?></th>
                        </tr>                       
                            <?php
                            foreach ($detail as $bill) {
                                ?>
                                <tr>
                                    <td width="20%"><?php echo $bill["test_name"]; ?></td>
                                    <td><?php echo $bill["short_name"]; ?></td>
                                    <td><?php echo date($this->customlib->getHospitalDateFormat(true,false), strtotime($bill["reporting_date"])); ?></td>
                                    <td><?php echo $bill["apply_charge"]; ?></td>
                                </tr>
                            <?php
                            }
                            ?>                       
                    </table>
                    <div class="divider mt-10 mb-10"></div>
					<table class="printablea4" style="width: 40%; float: right;">                
                        <?php if (!empty($result["total"])) {?>
                            <tr>
                                <th style="width: 30%;"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                <td class="text-right"><?php echo $result["total"]; ?></td>
                            </tr>
                        <?php }?>
                        <?php if (!empty($result["discount"])) {  ?>
                            <tr>
                                <th><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")";  ?></th>
                                <td class="text-right"><?php echo $result["discount"]; ?></td>
                            </tr>
                        <?php }?>
                        <?php if (!empty($result["tax"])) {  ?>
                            <tr>
                                <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")";  ?></th>
                                <td class="text-right"><?php echo $result["tax"]; ?></td>
                            </tr>
                        <?php }?>
                        <?php
                        if ((!empty($result["discount"])) && (!empty($result["tax"]))) {
                            if (!empty($result["net_amount"])) {
                                ?>
                        <tr>
                            <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                            <td class="text-right"><?php echo $result["net_amount"]; ?></td>
                        </tr>
                                <?php
                        }
                        }
                        ?>
                        <?php if (!empty($result["total_deposit"])) {
                                ?>
                        <tr>
                            <th><?php echo $this->lang->line("paid_amount"); ?></th>
                            <td class="text-right"><?php echo $result["total_deposit"]; ?></td>
                        </tr>
                        <?php
                        } ?>
                        <tr>
                            <th><?php echo $this->lang->line("balance_amount"); ?></th>
                            <td class="text-right"><?php echo $result["net_amount"] - $result["total_deposit"] ; ?></td>
                        </tr>
                        <?php if (!empty($result["note"])) {?>                         
                        <?php }

if (!$print) {
    ?>
                            <tr id="generated_by">
                                <th><?php echo $this->lang->line('collected_by'); ?></th>
                                <td class="text-right"><?php echo $result["generated_byname"]; ?></td>
                            </tr>
                        <?php
}
?>
                    </table>                   
                    <div class="divider mt-10 mb-10"></div> 
                    <table class="printablea4" width="100%" style="width:30%; float: right;">                       
                        <?php if (($print) != 'yes') { ?>
                            <tr id="generated_by">
                                <th><?php echo $this->lang->line('collected_by'); ?></th>
                                <td class="text-right"><?php echo $result["generated_byname"]; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <div class="footer-fixed printfooter"> 
                        <p><?php
                            if (!empty($print_details[0]['print_footer'])) {
                                echo $print_details[0]['print_footer'];
                            }
                            ?></p>
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