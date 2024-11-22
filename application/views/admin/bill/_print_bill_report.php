<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="table-responsive pl5 pr5" id="bill_report">
    <div id="printhead" style="display:none"><h5><?php echo $this->lang->line("patient_bill_report") . "<br>";?></h5></div>   
    <table class="table table-striped table-bordered table-hover allajaxlist" id="headerTable">
        <thead>
            <tr>
                <th><?php echo $this->lang->line('module'); ?></th>
                <th><?php echo $this->lang->line('opd_no'); ?></th>
                <th><?php echo $this->lang->line('ipd_no'); ?></th>
                <th><?php echo $this->lang->line('bill_no'); ?></th>
                <th><?php echo $this->lang->line('payment_mode'); ?></th>
                <th><?php echo $this->lang->line('payment_date'); ?></th>
                <th class="text-right"><?php echo $this->lang->line('payment_amount')."(".$currency_symbol.")"; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if(!empty($charge_payment_data)){
                    $grand_total_charge = 0;
                    $grand_total_payment = 0;
                    foreach($charge_payment_data as $charge_payment){
            ?>
            <tr>
                <td><?php echo $charge_payment->module; ?></td>
                <td><?php 
                    if(isset($charge_payment->opd_id)){
                        echo $this->customlib->getSessionPrefixByType('opd_no').$charge_payment->opd_id; 
                    }?>
                </td>
                <td><?php 
                    if(isset($charge_payment->ipd_id)){
                        echo $this->customlib->getSessionPrefixByType('ipd_no').$charge_payment->ipd_id; 
                        }?>
                </td>
                <td><?php 
                    if(isset($charge_payment->bill_no)){
                            echo $charge_payment->bill_no; 
                        }?>
                </td>
                <td>
                    <ul style="list-style-type:none;padding-left: 0;">
                        <?php foreach ($charge_payment->payments as $payment) { ?>
                            <li><?php
                                echo $this->lang->line(strtolower($payment->payment_mode)); 
                                if( $payment->payment_mode == "Cheque"){
                                    echo "<br />".$payment->cheque_no."<br />".$this->customlib->YYYYMMDDTodateFormat($payment->cheque_date);
                                }
                                ?>                                 
                            </li>
                        <?php } ?>
                    </ul>
                </td>
                <td>
                    <ul style="list-style-type:none; padding-left: 0;">
                        <?php foreach ($charge_payment->payments as $payment) { ?>
                            <li><?= $this->customlib->YYYYMMDDTodateFormat($payment->payment_date); ?></li>
                        <?php } ?>
                    </ul>
                </td>
                <td class="text-right">
                    <ul class="text-right" style="list-style-type:none;padding-left: 0;">                                           
                    <?php 
                    $total = 0;
                    foreach($charge_payment->payments as $payment){ ?>
                        <li>
                            <?php $total += $payment->amount;
                                echo $currency_symbol.amountFormat($payment->amount);
                            ?>
                        </li>
                        <?php } ?>
                    </ul>
                </td>
            <tr>
            <tr>
                <td colspan="5"></td>
                <td><b><?= $this->lang->line("total_charge"); ?> : </b><?php
                      $charge = $charge_payment->charge==''?0: $charge_payment->charge;
echo $currency_symbol.amountFormat($charge);
                  ?></td>
                <td class="text-right"><b><?= $this->lang->line("total_payment"); ?> : </b><?= $currency_symbol.amountFormat($total); ?></td>
                <?php 
                $grand_total_charge += $charge; 
                $grand_total_payment += $total; 
                ?>
            </tr>
            <?php 
                    }
                }
            ?>
                <tr class="box box-solid total-bg">
                <td colspan="5"></td>
                <td><b><?= $this->lang->line("grand_total_charge"); ?> : </b><?= $grand_total_charge==''?0:$currency_symbol.amountFormat($grand_total_charge); ?></td>
                <td class="text-right"><b><?= $this->lang->line("grand_total_payment"); ?> : </b><?= $currency_symbol.amountFormat($grand_total_payment); ?></td>
            </tr>
        </tbody>
    </table>
</div>