<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat(); 
if(!empty($result)){
?>
  <div class="row">
    <div class="col-md-8">
      <table class="table table-striped table-hover">
        <tr>
          <th width="40%"><?php echo $this->lang->line('bill_no'); ?></th>
          <td width="60%"><?php echo $this->customlib->getPatientSessionPrefixByType('blood_bank_billing').$result['id'];?></td>     
        </tr>
         <tr>
          <th width="40%"><?php echo $this->lang->line('case_id'); ?></th>
          <td width="60%"><?php echo  $result['case_reference_id']; ?></td>     
        </tr>
		 <!--
        <?php if($result['transaction_id']!=""){ ?>
          <tr>
            <th width="40%"><?php echo $this->lang->line('transaction_id'); ?></th>
            <td width="60%"><?php echo  $this->customlib->getPatientSessionPrefixByType('transaction_id').$result['transaction_id']; ?></td>     
          </tr>
       <?php } ?> -->
        <tr>
          <th width="40%"><?php echo $this->lang->line('received_to'); ?></th>
          <td width="60%"><?php echo  $result['patient_name']." (".$result['patient_id'].")"?></td>     
        </tr>
        <tr>
          <th width="40%"><?php echo $this->lang->line('bags'); ?></th>
          <td width="60%"><?php echo $this->customlib->bag_string($result['bag_no'],$result['volume'],$result['unit'])?></td> 
        </tr>
        <tr>
          <th width="40%"><?php echo $this->lang->line('issue_date'); ?></th>
          <td width="60%"><?php echo $this->customlib->dateyyyymmddToDateTimeformat($result['date_of_issue'], false);?></td>
        </tr>  
        <tr>        
          <th width="40%"><?php echo $this->lang->line('blood_group'); ?></th>
          <td width="60%"><?php echo $result['blood_group']?></td>          
        </tr> 
        <tr>
          <th width="40%"><?php echo $this->lang->line('reference'); ?></th>
          <td width="60%"><?php echo $result['reference']?></td>
        </tr>    
        <tr>      
          <th width="40%"><?php echo $this->lang->line('donor_name'); ?></th>
          <td width="60%"><?php echo $result['donor_name']?></td>          
        </tr>  
        <tr>
          <th width="40%"><?php echo $this->lang->line('technician'); ?></th>
          <td width="60%"><?php echo $result['technician']?></td>
          <td colspan="2"></td>        
        </tr>
        <tr>
          <th width="40%"><?php echo $this->lang->line('note'); ?></th>
          <td width="60%"><?php echo $result['remark']?></td>  
          <td colspan="2"></td> 
        </tr>
        <tr>
          <th width="40%"><?php echo $this->lang->line('tpa'); ?></th>
          <td width="60%"><?php if(isset($result['organisation_name'])==true){ echo $result['organisation_name'];} ?></td>  
          <td colspan="2"></td> 
        </tr>
        <tr>          
          <th width="40%"><?php echo $this->lang->line('tpa_id'); ?></th>
          <td width="60%"><?php if(isset($result['insurance_id'])==true){ echo $result['insurance_id'];}?></td> 
          <td colspan="2"></td> 
        </tr>        
        <tr>          
          <th width="40%"><?php echo $this->lang->line('tpa_validity'); ?></th>
          <td width="60%"><?php if(isset($result['insurance_validity'])==true){ echo $this->customlib->YYYYMMDDTodateFormat($result['insurance_validity']);}?></td> 
          <td colspan="2"></td> 
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
    </div>
    <div class="col-md-4">
      <table class="table table-striped table-hover">
        <tr>
          <th width="40%"><?php echo $this->lang->line('amount'); ?></th>
          <td width="60%" class="text text-right"><?php echo $currency_symbol.$result['amount']?></td>
        </tr>  
        <tr>    
          <th width="40%"><?php echo $this->lang->line('discount'); ?> (%)</th>
          <td width="60%" class="text text-right"><?php echo "(".$result['discount_percentage'].") ".$currency_symbol.calculatePercent($result['amount'],$result['discount_percentage'])?></td>
        </tr>        
        <tr>
          <?php $total_tax_amount = $result['amount'] - calculatePercent($result['amount'],$result['discount_percentage']); ?>
          <th width="40%"><?php echo $this->lang->line('tax'); ?> (%)</th>
          <td width="60%" class="text text-right"><?php echo "(".$result['tax_percentage'].") ".$currency_symbol.calculatePercent($total_tax_amount,$result['tax_percentage'])?></td>
        </tr>
        <tr>        
          <th width="40%"><?php echo $this->lang->line('net_amount'); ?></th>
          <td width="60%" class="text text-right"><?php echo $currency_symbol.$result['net_amount']?></td>      
        </tr> 
        <tr>
          <th width="40%"><?php echo $this->lang->line('paid_amount'); ?></th>
          <td width="60%" class="text text-right"><?php echo $currency_symbol.$result['total_deposit'];?></td>
        </tr>    
        <tr>      
          <th width="40%"><?php echo $this->lang->line('balance_amount'); ?></th>
          <td width="60%" class="text text-right"><?php echo $currency_symbol.amountFormat($result['net_amount']-$result['total_deposit']);?></td>     
        </tr>    
      </table>
    </div>    
  </div> 
	<?php
}
 ?>