<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
    </head>
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div>
                    <div class="pprinta4" >
                        <?php if (!empty($print_details[0]['print_header'])) { ?>
                            <img style="height:100px ; width:100%;" class="img-responsive" src="<?php echo base_url() . $print_details[0]["print_header"] ?>">
                        <?php } ?>
                        <div style="height: 10px; clear: both;"></div>
                    </div>
                    <table width="100%" class="printablea4">
                        <tr>
                            <td class="text-left"><?php echo $this->lang->line('bill') . " #" ?><?php echo $this->customlib->getSessionPrefixByType('opd_billing').$result["opdid"] ?></td>
                            <td class="text-right"><?php echo $this->lang->line('date') . " : " ?><?php
                                    if (!empty($result['discharge_date'])) {
                                        echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['discharge_date']));
                                    }
                                    ?></td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <table class="printablea4" cellspacing="0" width="100%">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('name'); ?></th>
                            <td width="25%"><?php echo $result["patient_name"]; ?></td>
                            <th width="25%"><?php echo $this->lang->line('gender'); ?></th>
                            <td width="25%"><?php echo $result["gender"]; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('age'); ?></th>
                            <td width="25%"><?php echo $result["age"]; ?></td>
                            <th width="25%"><?php echo $this->lang->line('mobile'); ?></th>
                            <td width="25%"><?php echo $result["mobileno"]; ?></td>
                        </tr>
                         <tr>
                            <th width="25%"><?php echo $this->lang->line('patient_id'); ?></th>
                            <td width="25%"><?php echo $result["id"]; ?></td>
                            <th width="25%"><?php echo $this->lang->line('doctor'); ?></th>
                            <td width="25%"><?php echo $result["name"] . " " . $result["surname"]; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('appointment')." ".$this->lang->line('date'); ?></th>
                            <td width="25%"><?php  if (!empty($result['appointment_date'])) {
                                        echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['appointment_date'])); } ?></td>
                             <th><?php echo $this->lang->line('opd') . " " . $this->lang->line('no'); ?></th>
                            <td><?php echo $this->customlib->getSessionPrefixByType('opd_no').$result["opdid"]; ?></td>
                          
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('organisation'); ?></th>
                            <td class="text-left"><?php echo $result['organisation_name']; ?></td> 
                        </tr> 
                        <tr>
                        </tr> 
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <table class="printablea4" width="100%">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('charges') . ' (' . $currency_symbol . ')'; ?> </th>
                            <th width="25%"><?php echo $this->lang->line('category'); ?></th>
                            <th width="25%"><?php echo $this->lang->line('date'); ?></th> 
                            <th width="25%" class="pttright reborder text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?> </th>
                        </tr>
                        <?php
                        $j = 0;
                        $total = 0;
                        foreach ($charges as $key => $charge) {
                            ?>
                            <tr>
                                <td><?php echo $charge["charge_type"]; ?></td> 
                                <td><?php echo $charge["charge_category"]; ?></td>
                                <td><?php echo date($this->customlib->getHospitalDateFormat(), $this->customlib->dateyyyymmddTodateformat($charge['date'])); ?></td>
                                <td class="text-right"><?php echo $charge["apply_charge"]; ?></td>
                            </tr>
                            <?php
                            $total += $charge["apply_charge"];
                            ?>

                            <?php
                            $j++;
                        }
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right"><?php echo $this->lang->line('total') . " : " ?>  <?php echo $currency_symbol . number_format( $total, 2) ?></td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <table class="printablea4" width="100%">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('payment') . " " . $this->lang->line('mode'); ?></th>
                            <th width="25%"><?php echo $this->lang->line('payment') . " " . $this->lang->line('date'); ?></th>
                            <th width="50%" class="text-right"><?php echo $this->lang->line('paid') . " " . $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                        <?php
                        $k = 0;
                        $total_paid = 0;
                        if ($result['status'] != 'paid') {
                            $status = $this->lang->line('unpaid');
                        } else {
                            $status = $this->lang->line('paid');
                        }
                        foreach ($payment_details as $key => $payment) {
                            ?>
                            <tr>
                                <td width="25%"><?php echo $this->lang->line(strtolower($payment["payment_mode"])); ?></td>
                                <td width="25%"><?php echo $this->customlib->YYYYMMDDTodateFormat($payment['date']); ?></td>
                                <td width="50%" class="text-right"><?php echo number_format($payment["paid_amount"] , 2) ; ?></td>
                            </tr>
                            <?php
                            $total_paid += $payment["paid_amount"];
                        }
                        ?>
                        <tr>
                            <td width="25%"></td>
                            <td width="25%"></td>
                            <td width="50%" class="text-right"><?php echo $this->lang->line('total') . " : " ?> <?php echo $currency_symbol . number_format($total_paid , 2)?></td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div> 
                    <table class="printablea4" width="100%">
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('consultant') . " " . $this->lang->line('charges') . " (" . $this->lang->line('paid') . ")". " (" . $currency_symbol . ")" ?> </th> 
                            <td class="text-right" width=""><?php
                                if (empty($result['amount'])) {
                                    echo number_format($result['amount'], 2) ;
                                } else {
                                    echo  number_format($result['amount'] + $result['visitamount'], 2) ;
                                }
                                ?>
                                    
                            </td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('total') . " " . $this->lang->line('charges') . " (" . $currency_symbol . ")" ?> </th> 
                            <td class="text-right"><?php echo number_format($total, 2) ; ?></td>
                        </tr>
                         <tr>
                            <th width="30%"><?php echo $this->lang->line('any_other_charges') . " (" . $currency_symbol . ")"; ?></th> 
                            <td class="text-right"><?php
                                if (!empty($result["other_charge"]) || $result["other_charge"] == 0) {
                                    echo number_format($result["other_charge"], 2) ;
                                } else {
                                    echo number_format($other_charge, 2);
                                }
                                ?></td>
                        </tr>
                         <tr>
                            <th width="20%"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th> 
                            <td class="text-right">
                                <input type="hidden" name="patient_id" value="<?php echo $result["id"] ?>">
                                <?php
                                if (!empty($result["discount"]) || $result["discount"] == 0) {
                                    echo number_format($result["discount"], 2) ;
                                } else {
                                    echo number_format($discount, 2) ;
                                }
                                ?></td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")" ?></th> 
                            <td class="text-right"><?php
                                if (!empty($result['tax']) || $result["tax"] == 0) {
                                    echo number_format($result['tax'], 2) ;
                                } else {
                                    echo number_format($tax, 2) ;
                                }
                                ?></td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('gross') . " " . $this->lang->line('total') . " (" . $currency_symbol . ")" ?> </th> 
                            <td class="text-right" width=""><?php
                              
                                echo number_format($billstatus["gross_total"], 2) ;
                                ?></td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('total') . " " . $this->lang->line('payment') . " (" . $currency_symbol . ")" ?> </th> 
                            <td class="text-right" width=""><?php
                                if (empty($result['paid_amount'])) {
                                    echo number_format($paid_amount, 2) ;
                                } else {
                                    echo number_format($result['paid_amount'], 2) ;
                                }
                                ?></td>
                        <tr>
                         <tr>
                            <th width="50%"><?php echo $this->lang->line('payment_mode'); ?></th> 
                            <td class="text-right"><?php echo $result['bill_paymode']; ?> </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                              <div class="divider mt-10 mb-10"></div>
                            </td>
                        </tr>  
                        <tr>
                            <th width="50%"><?php echo $this->lang->line('net_payable') . " " . $this->lang->line('amount') . " (" . $status . ")" ?></th> 
                            <td class="text-right"><?php
                                if (empty($billstatus['net_amount'])) {
                                    echo  number_format($gross_total - $paid_amount, 2);
                                } else {
                                    echo  number_format($billstatus['net_amount'], 2);
                                }
                                ?></td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <div class="footer-fixed"> 
                        <?php if (!empty($print_details[0]['print_footer'])) { ?>    
                            <p class="ptt10"><?php echo $print_details[0]['print_footer']; ?></p>
                        <?php } ?>
                    </div>    
                </div>
            </div>
        </div>
    </div>
</html>