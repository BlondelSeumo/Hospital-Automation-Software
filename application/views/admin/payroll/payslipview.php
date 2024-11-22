<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('payslip'); ?></title>
    </head>
    <div class="fixed-print-header">
        <div>
                        <?php if (!empty($print_details[0]['print_header'])) { ?>
                            <img src="<?php
                            if (!empty($print_details[0]['print_header'])) {
                                echo base_url() . $print_details[0]['print_header'].img_time();
                            }
                            ?>" class="img-responsive" style="height:100px; width: 100%;">
                             <?php } ?>
        </div>
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
                    
                    <table width="100%">
                        <tr>
                            <td class="text-center"><h3 class="mb0 mt0"><?php echo $this->lang->line('payslip_for_the_period_of'); ?> <?php echo $this->lang->line($result["month"]); ?> <?php echo $result["year"] ?></h3></td>
                        </tr>
                    </table>
                    <table width="100%" class="paytable2">
                        <tr>
                            <th><?php echo $this->lang->line('payslip'); ?> #<?php echo $result["id"] ?></th> <td></td>
                            <th class="text-right"></th> 
                            <th class="text-right rtl-text-left"><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('date'); ?>: <?php
                                if (!empty($result['payment_date'])) {
                                    echo $this->customlib->YYYYMMDDTodateFormat($result['payment_date']);
                                }
                                ?></th>
                        </tr>
                    </table>
                    <hr/>
                    <table width="100%" class="paytable2">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('staff_id'); ?></th>
                            <td width="25%"><?php echo $result["employee_id"] ?></td>
                            <th width="25%"><?php echo $this->lang->line("name"); ?></th>
                            <td width="25%"><?php echo $result["name"] . " " . $result["surname"] ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('department'); ?></th>
                            <td><?php echo $result["department"] ?></td>
                            <th><?php echo $this->lang->line('designation'); ?></th>
                            <td><?php echo $result["designation"] ?></td>
                        </tr>
                    </table>
                    <br/>
                    <table width="100%" class="earntable table table-striped table-responsive">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('earning'); ?></th> 
                            <th width="25%" class="pttright reborder"><?php echo $this->lang->line('amount'); ?>(<?php echo $currency_symbol; ?>)</th>
                            <th width="25%" class="pttleft"><?php echo $this->lang->line('deduction'); ?></th>
                            <th width="25%" class="text-right"><?php echo $this->lang->line('amount'); ?>(<?php echo $currency_symbol; ?>)</th>
                        </tr>
                        <?php
                        $j = 0;
                        foreach ($allowance as $key => $value) {
                            ?>
                            <tr>
                                <?php if (array_key_exists($j, $positive_allowance)) { ?>
                                    <td><?php echo $positive_allowance[$j]["allowance_type"]; ?></td>
                                    <td class="pttright reborder"><?php echo amountFormat($positive_allowance[$j]["amount"]); ?></td>
                                <?php } ?>
                                <?php if (array_key_exists($j, $negative_allowance)) { ?>
                                    <td class="pttleft"><?php echo $negative_allowance[$j]["allowance_type"]; ?></td>
                                    <td class="text-right"><?php echo amountFormat($negative_allowance[$j]["amount"]); ?></td>
                                <?php } ?>
                            </tr>
                            <?php
                            $j++;
                        }
                        ?>
                        <tr>
                            <th><?php echo $this->lang->line('total'); ?> <?php echo $this->lang->line('earning'); ?></th>
                            <th class="pttright reborder"><?php echo $result["total_allowance"] ?></th>
                            <th class="pttleft"><?php echo $this->lang->line('total'); ?> <?php echo $this->lang->line('deduction'); ?></th>
                            <th class="text-right"><?php echo amountFormat($result["total_deduction"]); ?></th>
                        </tr>  
                    </table>
					<br>
                    <table class="noborder_table">
                        <tr>
                            <th width="70%" class="text-right"><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('mode'); ?></th> 
                            <td class="text-right">
                           <?php
                                if (!empty($result["payment_mode"])) {
                                    echo $payment_mode[$result["payment_mode"]];
                                }
                                ?>
                                <?php if($result["payment_mode"]=='Cheque'){ 
                                     echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ."<b>Cheque No.&nbsp; </b>" .$result["cheque_no"]; 
                                     echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;". "<b>Cheque Date.</b> &nbsp; " .$this->customlib->YYYYMMDDTodateFormat($result["cheque_date"])."&nbsp;"; 

                                    if ($result["attachment"]!="") { ?>
                                    <a  href="<?php echo base_url()."admin/payroll/download/".$result["id"]; ?>" class="btn btn-default btn-xs no-print " title="Download"><i class="fa fa-download"></i></a>
                                 <?php } ?>
                               <?php } ?>
                           </td>
                        </tr>
                        <tr>
                            <th width="70%" class="text-right"><?php echo $this->lang->line('basic_salary'); ?>(<?php echo $currency_symbol; ?>)</th> 
                            <td class="text-right"><?php echo $result["basic"] ?></td>
                        </tr>
                        <tr>
                            <th width="70%" class="text-right"><?php echo $this->lang->line('gross_salary'); ?>(<?php echo $currency_symbol; ?>)</th> 
                            <td class="text-right"><?php echo amountFormat($result["basic"] + $result["total_allowance"]-$result["total_deduction"]); ?></td>
                        </tr>
                        <?php if (!empty($result["tax"])) { ?>
                            <tr>
                                <th width="70%" class="text-right"><?php 
                                $gross = $result["basic"] + $result["total_allowance"]-$result["total_deduction"];
                                $tax_amount= ($gross * $result["tax"])/100 ;
                                echo $this->lang->line('tax'); ?>(<?php echo $currency_symbol; ?>)</th> 
                                <td class="text-right"><?php echo amountFormat($tax_amount)." (". $result["tax"]."% )"; ?></td>
                            </tr>
                        <?php }
                        ?>
                        <tr>
                            <th width="70%" class="text-right"><?php echo $this->lang->line('net_salary'); ?>(<?php echo $currency_symbol; ?>)</th> 
                            <td class="text-right"><?php echo $result["net_salary"] ?></td>
                        </tr>
                    </table> 
                 
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