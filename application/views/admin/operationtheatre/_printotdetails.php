<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<?php 
if($otdetails->opd_details_id != "")
{
    $patient_name = $otdetails->opd_patient_name;
    $patient_id   = $otdetails->opd_patient_id;
    $case_id      = $otdetails->opd_case_id ;
}else{
    $patient_name = $otdetails->ipd_patient_name;
    $patient_id   = $otdetails->ipd_patient_id;
    $case_id      = $otdetails->ipd_case_id ;
}
 ?>
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
<div class="print-area">
<div class="row">  
        <div class="col-12">
           <div class="card mt-1">
                <div class="card-body">  
                    <div class="row">
                        <div class="col-md-12">                           
                        <div class="col-md-6">                           
                            <p><?php echo $this->lang->line('patient') ;?>: <?php echo composePatientName($patient_name,$patient_id); ?></p>        
                        </div>
                        <div class="col-md-6 text-right">                            
                            <p><?php echo $this->lang->line('case_id') ;?>: <?php echo $case_id; ?></p>                 
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php  $currency_symbol = $this->customlib->getHospitalCurrencyFormat();  ?> 
                        <div class="box-body p-0">                   
                            <div class="col-md-12 col-lg-10 col-sm-9"> 
                            <table class="print-table" style="text-align: left; padding-top: 10px;">
                                <tbody>
                                    <tr>
                                        <th><?php echo $this->lang->line('reference_no'); ?></th>
                                        <td><?php echo $this->customlib->getSessionPrefixByType('operation_theater_reference_no'). $otdetails->id;?>  </td>                           
                                        <th><?php echo $this->lang->line('operation_name'); ?></th>
                                        <td><?php echo $otdetails->operation; ?></td>
                                    </tr> 
                                    <tr>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($otdetails->date,$this->customlib->getHospitalTimeFormat());?></td>
                                        <th><?php echo $this->lang->line('operation_category'); ?></th>
                                        <td><?php echo $otdetails->category; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                        <td><?php echo $otdetails->name.' '. $otdetails->surname. ' ('. $otdetails->employee_id.')'; ?></td>                                        
                                        <th><?php echo $this->lang->line('assistant_consultant').' 1'; ?></th>
                                        <td><?php echo $otdetails->ass_consultant_1; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('assistant_consultant').' 2'; ?></th>
                                        <td><?php echo $otdetails->ass_consultant_2; ?></td>
                                        <th><?php echo $this->lang->line('anesthetist'); ?></th>
                                        <td><?php echo $otdetails->anesthetist;?></td>
                                    </tr>
                                    <tr>                                    
                                        <th><?php echo $this->lang->line('anaethesia_type'); ?></th>
                                        <td><?php echo $otdetails->anaethesia_type;?></td>                                
                                        <th><?php echo $this->lang->line('ot_technician'); ?></th>
                                        <td><?php echo $otdetails->ot_technician;?></td>
                                    </tr>
                                    <tr>                                   
                                        <th><?php echo $this->lang->line('ot_assistant'); ?></th>
                                        <td><?php echo $otdetails->ot_assistant;?></td>
                                        <th><?php echo $this->lang->line('remark'); ?></th>
                                        <td><?php echo $otdetails->remark;?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('result'); ?></th>
                                        <td><?php echo $otdetails->result;?></td>
                                    </tr>                                    
                                    <?php  if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                            $display_field = $otdetails->{$fields_value->name};
                                            if ($fields_value->type == "link") {
                                                $display_field = "<a href=" . $otdetails->{$fields_value->name} . " target='_blank'>" . $otdetails->{$fields_value->name} . "</a>";
                                            }
                                    ?>
                                    <tr>
                                        <th><?php echo $fields_value->name; ?></th>
                                        <td colspan="3"><?php echo $display_field;?></td>
                                    <tr>                                   
                                <?php }
                            } ?>                                    
          
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