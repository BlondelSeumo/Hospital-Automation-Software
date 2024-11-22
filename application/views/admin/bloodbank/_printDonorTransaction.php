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
<div class="print-area p-1">
<div class="row">
        <div class="col-12">
           
              <div class="card">
                <div class="card-body">  
                    <div class="row"><div class="col-md-12">
                        <div class="col-md-6">
                           <p><?php echo $this->lang->line('donor_name'); ?>: <?php echo $result['donor_name']; ?></p>
                           <p><?php echo $this->lang->line('age'); ?>: <?php echo $this->customlib->getAgeBydob($result['date_of_birth']); ?></p>
                           <p><?php echo $this->lang->line('blood_group'); ?>: <?php echo $result['blood_group_name']; ?></p>
                           <p><?php echo $this->lang->line('gender'); ?>: <?php echo $result['gender']; ?></p>
                           <p><?php echo $this->lang->line('father_name'); ?>: <?php echo $result['father_name']; ?></p>
                           <p><?php echo $this->lang->line('contact_no'); ?>: <?php echo $result['contact_no']; ?></p>
                           <p><?php echo $this->lang->line('address'); ?>: <?php echo $result['address']; ?></p>
                        </div>

                        <div class="col-md-6 text-right">
                            <p><span class="text-muted"><?php echo $this->lang->line('transaction_id'); ?>: </span> <?php echo $this->customlib->getSessionPrefixByType('transaction_id').$transaction->id; ?></p>
                            <p><span class="text-muted"><?php echo $this->lang->line('date'); ?>: </span> <?php echo $this->customlib->YYYYMMDDTodateFormat($transaction->payment_date); ?></p>
                        </div>
                    </div>
                    </div>

                    <div class="row">
                         <div class="col-md-12">
                              <table class="print-table">
                              <thead>
                                  <tr class="line">
                                    <td width="50">#</td>
                                    <td><?php echo $this->lang->line('description'); ?></td>
                                    <td class="text-right"><?php echo $this->lang->line('amount').' ('. $currency_symbol .')'; ?></td>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                    <td width="50">1</td>
                                    <td><?php echo $this->lang->line('payment_received'); ?><br>
                                      <?php

                                      echo $this->lang->line('by'). ": ".$this->lang->line(strtolower($transaction->payment_mode));
                                      if($transaction->payment_mode == "Cheque"){
                                        echo " ".$transaction->cheque_no;
                                      }
                                        if($transaction->payment_mode == "Cheque"){
                                      echo "<br>";
                                        echo $this->customlib->YYYYMMDDTodateFormat($transaction->cheque_date);
                                      }
                                        ?></td>
                                  
                                    <td class="text-right"><?php echo $transaction->amount ?></td>
                                  </tr>
                                  <tr>
                                    <td colspan="2" class="text-right rtl-text-left"><?php echo $this->lang->line('total'); ?></td>
                                    <td class="text-right rtl-text-left"><?php echo $currency_symbol.$transaction->amount ?></td>
                                  </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>

                   <div class="row">
                     <div class="col-md-12">
                       <?php echo $this->lang->line('received_by'); ?><br/>
                       <?php echo composeStaffNameByString($transaction->name, $transaction->surname, $transaction->employee_id); ?>
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