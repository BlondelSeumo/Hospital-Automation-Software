
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="fixed-print-header">
                    <?php  if (!empty($print_details['print_header'])) { ?>
                        <img src="<?php
                        if (!empty($print_details['print_header'])) {
                            echo base_url() . $print_details['print_header'].img_time();
                        }
                        ?>" style="height:100px; width:100%;" class="img-responsive">
                    <?php }?>
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
            <!-- left column -->
            <div class="col-md-12">
                
                <div>
                    <?php
                    $date = $result->presdate;
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $this->lang->line('prescription'); ?>: <?php echo $this->customlib->getSessionPrefixByType('ipd_prescription').$result->prescription_id; ?>
                        </div>
                       <div class="col-md-6 text-right">
                            <?php echo $this->lang->line('date'); ?> : <?php
                                if (!empty($result->presdate)) {
                                    echo $this->customlib->YYYYMMDDTodateFormat($date);
                                }
                                ?>
                        </div>
                    </div>
                    <div class="divider mt-10 mb-10"></div>
                    <table class="noborder_table">
                        <tr>
                            <th><?php echo $this->lang->line("patient_name"); ?></th>
                            <td><?php echo composePatientName($result->patient_name,$result->id); ?></td>
                            <th><?php echo $this->lang->line("age"); ?></th>
                            <td><?php
                                echo $this->customlib->getPatientAge($result->age,$result->month,$result->day);
                                ?></td>
                        </tr>
                        <tr>                            
                            <th><?php echo $this->lang->line("gender"); ?></th>
                            <td><?php echo $result->gender ?></td>
                            <th><?php echo $this->lang->line("weight"); ?></th>
                            <td><?php echo $result->weight ?></td>
                        </tr>
                        <tr>                            
                            <th><?php echo $this->lang->line("bp"); ?></th>
                            <td><?php echo $result->bp ?></td>
                            <th><?php echo $this->lang->line("phone"); ?></th>
                            <td><?php echo $result->mobileno ?></td>
                        </tr>
                        <tr>        
                            <th><?php echo $this->lang->line("blood_group"); ?></th>
                            <td><?php echo $result->blood_group_name ?></td>
                            <th><?php echo $this->lang->line("pulse"); ?></th>
                            <td><?php echo $result->pulse; ?></td>
                        </tr>
                        <tr>        
                            <th><?php echo $this->lang->line("height"); ?></th>
                            <td><?php echo $result->height ?></td>
                            <th><?php echo $this->lang->line('temperature'); ?></th>
                            <td><?php echo $result->temperature; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line("email"); ?></th>
                            <td><?php echo $result->email ?></td>
                            <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                            <td><?php echo composeStaffNameByString($result->name,$result->surname,$result->employee_id); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line("generated_by"); ?></th>
                            <td><?php echo composeStaffNameByString($result->staff_name,$result->staff_surname,$result->staff_employee_id); ?></td>
                            <th><?php echo $this->lang->line("prescribe_by"); ?></th>
                            <td><?php echo composeStaffNameByString($result->priscribe_by_name,$result->priscribe_by_surname,$result->priscribe_by_employee_id); ?></td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <?php if($result->is_finding_print=='yes'){ $colspan = 6 ; $width = '50%'; }else{ $colspan = 12; $width = '100%';
                    
                    } ?>
                    <table width="100%" class="printablea4">
                        <tr>
						<?php if($result->symptoms !=''){ ?>
                            <td colspan="<?php echo $colspan; ?>" width="<?php echo $width; ?>"><b><?php echo $this->lang->line("symptoms"); ?></b>:<br><?php echo nl2br($result->symptoms)  ?></td>
						<?php } ?>
						
                        <?php if($result->is_finding_print=='yes'){ ?> <td>                      
                            <b><?php echo $this->lang->line("finding"); ?></b>:<br>
                            <?php echo nl2br($result->finding_description)  ?></td>
                        <?php }  ?>
                        </tr>
                    </table>       
                    <?php if($result->symptoms != '' || trim($result->finding_description) != ''){
                          if($result->is_finding_print == 'yes'){?>
                    <div class="divider mt-10 mb-10"></div>
					<?php } } ?>
                    <table width="100%" class="printablea4">
                        <tr>
                            <td style="margin-bottom: 0;"><?php echo $result->header_note ?></td>
                        </tr>
                    </table>               
                    <div class="divider mt-10 mb-10"></div>
                    <h4><?php echo $this->lang->line("medicines"); ?></h4>
                    <table class="table table-striped table-hover">                        
                            <tr>
                                <th width="2%" class="text text-left">#</th>
                                <th width="13%" class="text text-left"><?php echo $this->lang->line("medicine_category"); ?></th>
                                <th width="11%" class="text text-center"><?php echo $this->lang->line("medicine"); ?></th> 
                                <th width="13%" class="text text-center"><?php echo $this->lang->line("dosage"); ?></th>
                                <th width="13%" class="text text-center"><?php echo $this->lang->line("dose_interval"); ?></th>
                                <th width="13%" class="text text-center"><?php echo $this->lang->line("dose_duration"); ?></th> 
                                <th width="35%" class="text text-center"><?php echo $this->lang->line("instruction"); ?></th> 
                            </tr>
                        <?php $medsl =''; foreach ($result->medicines as $pkey => $pvalue) { $medsl++;
                              ?>
                            <tr>
                                <td class="text text-left"><?php echo $medsl; ?></td>
                                <td class="text text-left"><?php echo $pvalue->medicine_category; ?></td>
                                <td class="text text-center"><?php echo $pvalue->medicine_name; ?></td>
                                <td class="text text-center"><?php echo $pvalue->dosage." ".$pvalue->unit; ?></td>
                                <td class="text text-center"><?php echo $pvalue->dose_interval_name; ?></td>
                                <td class="text text-center"><?php echo $pvalue->dose_duration_name; ?></td>
                                <td class="text text-center"><?php echo $pvalue->instruction; ?></td>
                            </tr>  
                        <?php } ?>
                    </table>
                    
                    <?php if(!empty($result->tests)){ ?>    
                    <table class="table table-striped table-hover" width="100%">
                        <tr>
                            <th><?php echo $this->lang->line("pathology_test");  ?></th> 
                            <th><?php echo $this->lang->line("radiology_test"); ?></th>
                        </tr>
                        <tr> 
                            <td width="50%"><?php $sl=''; foreach ($result->tests as $test_key => $test_value) {  ?>
                                <table >   
                                    <?php if($test_value->test_name != ""){ $sl++;?> <tr>
                                    <td><?php echo $sl.'. '.$test_value->test_name." (".$test_value->short_name.")"; ?></td>   </tr>        
                                    <?php } ?>                             
                                </table>    
                                <?php } ?>
                            </td> 
                            <td><?php $slradiology=''; foreach ($result->tests as $test_key => $test_value) {  ?>
                                <table>   
                                    <?php if($test_value->test_name == ""){ $slradiology++;?> <tr>
                                    <td><?php echo $slradiology.'. '.$test_value->radio_test_name." (".$test_value->radio_short_name.")"; ?></td> </tr>                                 
                                    <?php } ?>                             
                                </table>   
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                    <?php } ?>                   
                  
                    <table width="100%" class="printablea4">
                        <tr>
                            <td><?php echo $result->footer_note; ?></td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                
                </div>
            </div>
        </div>
        </div>
    </td></tr></tbody>
    <tfoot><tr><td>

    <?php
                    if (!empty($print_details['print_footer'])) {
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
  
  <?php echo $print_details['print_footer'];?>
                
  </div>
  <?php
}
?>