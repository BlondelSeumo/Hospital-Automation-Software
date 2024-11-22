<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="fixed-print-header">
    <?php if (!empty($print_details[0]['print_header'])) { ?>
                    <div class="pprinta4">
                        <img src="<?php
                                    if (!empty($print_details[0]['print_header'])) {
                                        echo base_url() . $print_details[0]['print_header'] . img_time();
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
<div class="print-area">
    <div class="row">
        <div class="col-md-12">
               
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="noborder_table">
                                    <tr>
                                        <th width="20%"><?php echo $this->lang->line('patient_name'); ?>sss</th>
                                        <td width="30%"><span id='patient_name_view'><?php echo $result['patients_name'] ?></span></td>
                                        <th width="20%"><?php echo $this->lang->line('appointment_no'); ?>: </th>
                                        <td width="30%"><span id="appointmentno"><?php echo $result['appointment_no'] ?></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line("age"); ?></th>
                                        <td><?php echo $this->customlib->getPatientAge($result['age'], $result['month'], $result['day']); ?> </td>
                                        <th><?php echo $this->lang->line("appointment_sno"); ?>: </th>
                                        <td><?php echo $result['appointment_serial_no'] ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('email'); ?></th>
                                        <td><span id='emails_view'><?php echo $result['patient_email'] ?></span></td>
                                        <th><?php echo $this->lang->line("appointment_date"); ?>: </th>
                                        <td><?php echo $result["date"]; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <td><span id="phones_view"><?php echo $result['patient_mobileno'] ?></span></td>
                                        <th><?php echo $this->lang->line('appointment_priority'); ?></th>
                                        <td><?php echo $result['appoint_priority'] ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <td><span id="genders"><?php echo $this->lang->line(strtolower($result['patients_gender'])) ?></span></td>
                                        <th><?php echo $this->lang->line('shift'); ?></th>
                                        <td><span id='patient_name_view'><?php echo $result['global_shift_name'] ?></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('doctor'); ?></th>
                                        <td><?php echo composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']); ?></td>
                                        <th><?php echo $this->lang->line('slot'); ?></th>
                                        <td><span id="appointmentno"><?php echo $result['doctor_shift_name'] ?></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line("department"); ?></th>
                                        <td><?php echo $result["department_name"]; ?></td>
                                        <th><?php echo $this->lang->line('payment_mode'); ?></th>
                                        <td><?php if ($result['payment_mode']) {
                                                echo $this->lang->line(strtolower($result['payment_mode']));
                                            } ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('live_consultation'); ?></th>
                                        <td><?php echo $this->lang->line($result['live_consult']); ?></td>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <td><?php echo $this->lang->line($result['appointment_status']); ?></td>
                                    </tr>

                                    <?php if ($result['payment_mode'] == 'Cheque') {  ?>
                                        <tr id="payrow">
                                            <th><?php echo $this->lang->line('cheque_no'); ?></th>
                                            <td><span id='spn_chequeno'><?php echo $result['cheque_no'] ?></span></td>
                                            <th><?php echo $this->lang->line('cheque_date'); ?></th>
                                            <td><span id="spn_chequedate"><?php echo $this->customlib->YYYYMMDDTodateFormat($result['cheque_date'], $this->customlib->getHospitalTimeFormat()); ?></span></td>
                                        </tr>
                                    <?php } ?>

                                    <tr>                                       
                                        <th width="15%"><?php echo $this->lang->line('collected_by'); ?></th>
                                        <td width="35%"><span id="collected_by"><?php 
											if($result['received_by'] > 0){
												$staff_data = $this->staff_model->getstaff($result['received_by']);											
												echo $staff_name= $staff_data["name"] . " " . $staff_data["surname"] ." (". $staff_data["employee_id"].")" ;
											}
                                        ?></span></td>       
                                    </tr>   

                                     <tr>
                                        <th><?php echo $this->lang->line('message'); ?></th>
                                        <td colspan="4" class="lh-normal"><?php echo $result['message'] ?></td>                                            
                                    </tr>                               
                                    
                                    <?php  if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                            $display_field = $result["$fields_value->name"];
                                            if ($fields_value->type == "link") {
                                                $display_field = "<a href=" . $result["$fields_value->name"] . " target='_blank'>" . $result["$fields_value->name"] . "</a>";
                                            }
                                    ?>
                                    <tr>
                                        <th><?php echo $fields_value->name; ?></th> 
                                        <td colspan="4" class="lh-normal"><?php echo $display_field; ?></td>
                                    </tr>
                                    <?php }
                                        }?> 
                        
                                </table>
                            </div>
                            <div class="col-lg-12">
                                <div class="divider"></div>
                                    <h4 class="heading-title"><?php echo $this->lang->line("payment_details"); ?></h4>
                                    <table class="print-table">
                                        <thead>
                                            <tr class="line">
                                                <td style="padding-left:2px;"><?php echo $this->lang->line('transaction_id'); ?></td>
                                                <td><?php echo $this->lang->line('source'); ?></td>
                                                <td class="text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding-left:2px;"><?php echo $result['transaction_id']; ?></td>
                                                <td><?php echo  $this->lang->line(strtolower($result['source'])); ?></td>
                                                <td class="text-right"><?php if ($result['standard_amount'] != "") {
                                                                            echo  $currency_symbol . $result['standard_amount'];
                                                                        } else {
                                                                            echo $currency_symbol . '0.00';
                                                                        } ?> </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-right"><?php echo $this->lang->line('net_amount'); ?></td>
                                                <td class="text-right">
                                                    <?php if ($result['standard_amount'] != "") {
                                                        echo  $currency_symbol . $result['standard_amount'];
                                                    } else {
                                                        echo $currency_symbol . '0.00';
                                                    }
                                                    ?></td>
                                            </tr>
                                            <tr>                                  
                                                <td colspan="2" class="text-right no-line"><?php echo $this->lang->line('discount_percentage')." (".$result["discount_percentage"]." %)";?></td>
                                                <td class="text-right no-line"><?php echo  $currency_symbol.calculatePercent($result["standard_amount"], $result["discount_percentage"]); ?></td>
                                            </tr>
                                            <tr>                                  
                                                <td colspan="2" class="text-right no-line"><?php echo $this->lang->line('paid_amount');?></td>
                                                <td class="text-right no-line"><?php echo $currency_symbol.$result["paid_amount"]; ?></td>
                                            </tr>
                                            <tr>
                                                <th style="padding-left:2px;"><?php echo $this->lang->line('payment_note') ?>:</th>
                                                <td colspan="2"><?php echo $result['payment_note']; ?></td>
                                            </tr>
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