<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
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
                 <table class="noborder_table" cellspacing="0" cellpadding="0">
                <tbody>
                    <tr>
                        <th><?php echo $this->lang->line('case_id'); ?></th>
                        <td><?php echo $case_id;?></td>
                        <th><?php echo $this->lang->line('appointment_date'); ?></th>
                        <td><?php if($patient['appointment_date'] !='' && $patient['appointment_date']!='0000-00-00'){
                            echo $this->customlib->YYYYMMDDHisTodateFormat($patient['appointment_date'],$this->customlib->getHospitalTimeFormat());  } ?>
                        </td>
                    </tr> 
                    <tr>
                        <th><?php echo $this->lang->line('name'); ?></th>
                        <td><?php echo composePatientName($patient['patient_name'],$patient['patient_id']); ?></td>
                        <th><?php echo $this->lang->line('guardian_name'); ?></th>
                        <td><?php echo $patient['guardian_name']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('gender'); ?></th>
                        <td><?php echo $patient['gender']; ?></td>
                        <th><?php echo $this->lang->line('age'); ?></th>
                        <td><?php echo $this->customlib->getPatientAge($patient['age'],$patient['month'],$patient['day'])." (".$this->lang->line('as_of_date').' '.$this->customlib->YYYYMMDDTodateFormat($patient['as_of_date']).")"; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('phone'); ?></th>
                        <td><?php echo $patient['mobileno']; ?></td>
                        <th><?php  echo $this->lang->line('credit_limit') . " (" . $currency_symbol . ")"; ?></th>
                        <td><?php if(!empty($patient['credit_limit'])){ echo $patient['credit_limit']; } ?></td>
                    </tr>
                      <tr>
                        <th><?php echo $this->lang->line('tpa'); ?></th>
                        <td><?php if(isset($patient['organisation_name'])==true){ echo $patient['organisation_name'];} ?></td>
                        <th><?php echo $this->lang->line('tpa_validity'); ?></th>
                        <td><?php if(isset($patient['insurance_validity'])==true){ echo $this->customlib->YYYYMMDDTodateFormat($patient['insurance_validity']); } ?></td>             
                    </tr>
                     <tr>
                        <th><?php echo $this->lang->line('tpa_id'); ?></th>
                        <td><?php if(isset($patient['insurance_id'])==true){ echo $patient['insurance_id'];} ?></td>
                    </tr>
                    <?php 
                    if($patient['ipdid']!='' && $patient['ipdid']!=0){?>
                    <tr>                      
                        <th><?php echo $this->lang->line('ipd_no'); ?></th>
                        <td><?php
                            if($patient['ipdid']!='' && $patient['ipdid']!=0){
                                echo $this->customlib->getSessionPrefixByType('ipd_no').$patient['ipdid'];
                            }                            
                             if ($patient['discharged'] == 'yes') {
                                 echo " <span class='label label-warning'>" . $this->lang->line("discharged") . "</span>";
                             }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                    <?php 
                    if($patient['opdid']!='' && $patient['opdid']!=0){?>
                    <tr>
                        <th><?php echo $this->lang->line('opd_no'); ?></th>
                        <td><?php
                            if($patient['opdid']!='' && $patient['opdid']!=0){
                                echo $this->customlib->getSessionPrefixByType('opd_no').$patient['opdid'];
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                 <?php if($patient['ipdid'] !='' && $patient['ipdid'] !=0){?>
                    <tr>
                        <th><?php
                            echo $this->lang->line('admission_date');                           
                            ?></th>
                        <td ><?php if($patient['date']!='' && $patient['date']!='0000-00-00'){
                            echo $this->customlib->YYYYMMDDTodateFormat($patient['date']);
                        } ?>
                        </td>
                        <th><?php
                            echo $this->lang->line('bed');                           
                            ?></th>
                        <td ><?php echo $patient['bed_name'] . " - " . $patient['bedgroup_name'] . " - " . $patient['floor_name'] ?>
                        </td>
                    </tr>                     
                     <?php } ?> 
                </tbody>
            </table>              
                       
<?php 
$total_amount=0;
$amount_paid=0;
if(!empty($opd_data)){
?>
    <h4 style="padding-left:2px"><?php echo $this->lang->line('opd_charges'); ?></h4>
    <table class="noborder_table">
    <thead>
        <tr class="border_top border_bottom">
        <th width="20%" class="ps-1" style="padding-left:0"><?php echo $this->lang->line('service'); ?></th>
        <th width="20%"><?php echo $this->lang->line('charge'); ?></th>
        <th width="10%"><?php echo $this->lang->line('qty'); ?></th>
        <th width="15%" class="text-right"><?php echo $this->lang->line('discount'); ?></th>
        <th class="text-right"><?php echo $this->lang->line('tax'); ?></th>
        <th class="text-right"><?php echo $this->lang->line('amount'); ?></th>
    </tr>
    </thead>
    <?php 
    foreach ($opd_data as $opd_key => $opd_value) {
        $total_amount+=$opd_value['amount'];
    ?>
    <tr>
        <th width="20%" class="ps-1" style="padding-left:0"><?php echo $opd_value['name'];?></th>
        <th width="20%"><?php echo $currency_symbol.$opd_value['apply_charge'];?></th>
        <th width="10%"><?php echo $opd_value['qty']." ".$opd_value['unit'];?></th>		
		<th width="15%" class=" text-right">
            <?php 
echo $currency_symbol.amountFormat($opd_value['apply_charge'] * ($opd_value['discount_percentage']/100))." (".$opd_value['discount_percentage']."%) ";
    ?></th>
        <th class=" text-right">
            <?php                   
$tax_raw=($opd_value["apply_charge"]-(($opd_value["apply_charge"]*$opd_value["discount_percentage"])/100));
$tax=(($tax_raw*$opd_value["tax"])/100);
echo $currency_symbol.amountFormat($tax)." (".$opd_value['tax']."%) ";
            ?>
            </th>	  
        <th class="text text-right"><?php echo $currency_symbol.$opd_value['amount'];?></th>
    </tr>
    <?php } ?>
</table>
    <?php 
}

if(!empty($ipd_data)){ ?>
    <h4 class="ps-2px"><?php echo $this->lang->line('ipd_charges'); ?></h4>
    <table class="noborder_table">
    <thead>
        <tr class="border_top border_bottom">
            <th width="20%" class="ps-1" style="padding-left:0"><?php echo $this->lang->line('service'); ?></th>
            <th width="20%"><?php echo $this->lang->line('charge'); ?></th>
            <th width="10%"><?php echo $this->lang->line('qty'); ?></th>
            <th width="15%" class="text-right"><?php echo $this->lang->line('discount'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('tax'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('amount'); ?></th>
        </tr>
    </thead>
    <?php 
    foreach ($ipd_data as $ipd_key => $ipd_value) {
        $total_amount+=$ipd_value['amount'];
    ?>
    <tr class="no-line">
        <th width="20%" class="ps-1" style="padding-left:0"><?php echo $ipd_value['name'];?></th>
        <th width="20%"><?php echo $currency_symbol.$ipd_value['apply_charge'];?></th>
        <th width="10%"><?php echo $ipd_value['qty']." ".$ipd_value['unit'];?></th>
        <th width="15%" class="text-right">
            <?php   echo $currency_symbol.amountFormat(($ipd_value['apply_charge'] * $ipd_value['discount_percentage'])/100)." (".$ipd_value['discount_percentage']."%) ";?>
        </th>
        <th class="text text-right">
            <?php
               $tax_raw=($ipd_value["apply_charge"]-(($ipd_value["apply_charge"]*$ipd_value["discount_percentage"])/100));
               $tax=(($tax_raw*$ipd_value["tax"])/100);
               echo $currency_symbol.amountFormat($tax)." (".$ipd_value['tax']."%) "; 
               
            ?></td>
        <th class="text text-right"><?php echo $currency_symbol.$ipd_value['amount'];?></th>
      </tr>
    <?php
    }
    ?>
</table>
    <?php 
}

//=========Pharmacy==========
if(!empty($pharmacy_data)){
    ?>
      <h4 class="ps-2px"><?php echo $this->lang->line('pharmacy_bill'); ?></h4>
    <table class="noborder_table">
    <thead>
        <tr class="border_top border_bottom">
            <th width="20%" class="ps-1" style="padding-left:0"><?php echo $this->lang->line('bill_no'); ?></th>
            <th width="20%"><?php echo $this->lang->line('charge'); ?></th>
            <th width="10%"><?php echo $this->lang->line('qty'); ?></th>
            <th width="15%" class="text text-right"><?php echo $this->lang->line('discount'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('tax'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('amount'); ?></th>
        </tr>
    </thead>
    <?php 
    foreach ($pharmacy_data as $pharmacy_key => $pharmacy_value) {
         $total_amount+=$pharmacy_value->net_amount;
    ?>
    <tr>
        <th width="20%" class="ps-1" style="padding-left:0"><?php echo $pharmacy_bill_prefix.$pharmacy_value->id;?></th>
        <th width="20%"><?php echo  $currency_symbol.$pharmacy_value->total;?></th>
        <th width="10%">1</th>
        <th width="15%" class="text text-right"><?php echo $currency_symbol.$pharmacy_value->discount." (".$pharmacy_value->discount_percentage."%) ";?></th>
        <th class="text text-right"><?php echo $currency_symbol.$pharmacy_value->tax." (".amountFormat(($pharmacy_value->tax *100)/($pharmacy_value->total-$pharmacy_value->discount))."%) ";?></th>
        <th class="text text-right"><?php echo  $currency_symbol.$pharmacy_value->net_amount;?></th>  
    </tr>       
    <?php
    }
    ?>
</table>
    <?php
}

//====================Pathology Billing================

if(!empty($pathology_data)){
    ?>
     <h4 class="ps-2px"><?php echo $this->lang->line('pathology_bill'); ?></h4>
    <table class="noborder_table">
    <thead>
          <tr class="border_top border_bottom">
            <th width="20%" class="ps-1" style="padding-left:0"><?php echo $this->lang->line('bill_no'); ?></th>
            <th width="20%"><?php echo $this->lang->line('charge'); ?></th>
            <th width="10%"><?php echo $this->lang->line('qty'); ?></th>
            <th width="15%" class="text-right"><?php echo $this->lang->line('discount'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('tax'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('amount'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        foreach ($pathology_data as $pathology_key => $pathology_value) {
          $total_amount+=$pathology_value->net_amount;
        ?>
        <tr>
            <th width="20%" class="ps-1" style="padding-left:0"><?php echo $pathology_bill_prefix.$pathology_value->id;?></th>
            <th width="20%"><?php echo  $currency_symbol.$pathology_value->total;?></th>
            <th width="10%">1</th>
            <th width="15%" class="text text-right"><?php  echo $currency_symbol.$pathology_value->discount. " (".$pathology_value->discount_percentage."%) ";?></th>
            <th class="text text-right"><?php if($pathology_value->total > 0){echo $currency_symbol.$pathology_value->tax." (".amountFormat(($pathology_value->tax *100)/$pathology_value->total)."%) ";}else { echo $currency_symbol.'0.00 (0.00%)'; } ?></th>
            <th class="text-right"><?php echo $currency_symbol.$pathology_value->net_amount;?></th>
        </tr>       
    <?php
    }
    ?>
    </tbody>
  </table>
    <?php
}      

//====================Radiology Billing================

if(!empty($radiology_data)){
    ?>
     <h4 class="ps-2px"><?php echo $this->lang->line('radiology_bill'); ?></h4>
    <table class="noborder_table">
    <thead>
        <tr class="border_top border_bottom">
            <th width="20%" class="ps-1" style="padding-left:0"><?php echo $this->lang->line('bill_no'); ?></th>
            <th width="20%"><?php echo $this->lang->line('charge'); ?></th>
            <th width="10%"><?php echo $this->lang->line('qty'); ?></th>
            <th width="15%" class="text-right"><?php echo $this->lang->line('discount'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('tax'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('amount'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        foreach ($radiology_data as $radiology_key => $radiology_value) {
           $total_amount+=$radiology_value->net_amount;
        ?>
        <tr>
            <th width="20%" class="ps-1" style="padding-left:0"><?php echo $radiology_bill_prefix.$radiology_value->id;?></th>
            <th width="20%"><?php echo  $currency_symbol.$radiology_value->total;?></th>
            <th width="10%">1</th>
            <th width="15%" class="text text-right"><?php  echo $currency_symbol.$radiology_value->discount." (".$radiology_value->discount_percentage."%) ";?></th>
        <th class="text text-right">    
        <?php echo $currency_symbol.$radiology_value->tax." (".amountFormat(($radiology_value->tax *100)/$radiology_value->total)."%) ";?>
    </th>
            <th class="text-right"><?php echo $currency_symbol.$radiology_value->net_amount;?></th>
        </tr>             
        <?php
        }
        ?>
    </tbody>
  </table>
    <?php
}      

//====================Blood Issue================

if(!empty($bloodissue_data)){
    ?>
     <h4 class="ps-2px"><?php echo $this->lang->line('blood_issue'); ?></h4>
    <table class="noborder_table">
    <thead>
        <tr class="border_top border_bottom">
            <th width="20%" class="ps-1" style="padding-left:0"><?php echo $this->lang->line('bill_no'); ?></th>
            <th width="20%"><?php echo $this->lang->line('charge'); ?></th>
            <th width="10%"><?php echo $this->lang->line('qty'); ?></th>
            <th width="15%" class="text text-right"><?php echo $this->lang->line('discount'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('tax'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('amount'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        foreach ($bloodissue_data as $blood_issue_key => $blood_issue_value) {
        $total_amount+=$blood_issue_value->net_amount;   
        $discount_amount=calculatePercent($blood_issue_value->standard_charge,$blood_issue_value->discount_percentage);
        ?>
        <tr>
            <th width="20%" class="ps-1" style="padding-left:0"><?php echo $blood_bank_bill_prefix.$blood_issue_value->id;?></th>
            <th width="20%"><?php echo  $currency_symbol.$blood_issue_value->standard_charge;?></th>
            <th width="10%">1</th>
            <th width="15%" class="text text-right">  <?php  echo $discount_amount . " (".$blood_issue_value->discount_percentage."%) ";?></th>
       <th class="text text-right">
        <?php 
           echo $currency_symbol.calculatePercent(($blood_issue_value->standard_charge-$discount_amount),$blood_issue_value->tax_percentage). " (".$blood_issue_value->tax_percentage."%) ";
           ?>
      </th>
            <th class="text-right"><?php echo $currency_symbol.$blood_issue_value->net_amount;?></th>
        </tr>
        <?php
        }
        ?>
    </tbody>
  </table>
    <?php
    }      
    if(!empty($transaction_data)){
    ?>
   <h4 class="ps-2px"><?php echo $this->lang->line('transactions'); ?></h4>
    <table class="noborder_table mb-0">
    <thead>
        <tr class="border_top border_bottom">
            <th style="padding-left:0"><?php echo $this->lang->line('transaction_id'); ?></th>
            <th><?php echo $this->lang->line('payment_date'); ?></th>
            <th><?php echo $this->lang->line('payment_mode'); ?></th>
            <th class="text text-right"><?php echo $this->lang->line('amount'); ?></th> 
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($transaction_data as $transaction_key => $transaction_value) {
        $amount_paid+=$transaction_value->amount;
        ?>
        <tr>
            <th class="ps-0"><?php echo $transaction_prefix.$transaction_value->id;?></th>
            <th><?php echo $this->customlib->YYYYMMDDHisTodateFormat($transaction_value->payment_date);?></th>
            <th><?php echo $this->lang->line(strtolower($transaction_value->payment_mode));?></th>
            <th class="text text-right"><?php echo $currency_symbol.$transaction_value->amount;?></th>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
    <?php
    }
    ?>
                <div class="divider"></div>
                <table class="noborder_table">
                    <tbody>
                        <tr>
                            <th style="width:70%;padding-left:0" class="text text-right"><?php echo $this->lang->line('grand_total'); ?>:</th>
                            <th class="text text-right"><?php echo $currency_symbol.amountFormat($total_amount); ?></th>
                        </tr>
                        <tr>
                            <th class="text text-right"><?php echo $this->lang->line('amount_paid'); ?>:</th>
                            <th class="text text-right"><?php echo $currency_symbol.amountFormat($amount_paid); ?></th>
                        </tr>
                        <tr>
                            <th class="text text-right"><?php echo $this->lang->line('balance_amount'); ?>:</th>
                            <th class="text text-right"><?php echo $currency_symbol.amountFormat(($total_amount-$amount_paid));?></th>
                        </tr>
                    </tbody>
                </table>
              
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