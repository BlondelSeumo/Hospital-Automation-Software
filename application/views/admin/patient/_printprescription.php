
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="fixed-print-header">
    <div>
                <?php  if (!empty($print_details['print_header'])) { ?>
                    <img src="<?php
                    if (!empty($print_details['print_header'])) {
                        echo base_url() . $print_details['print_header'].img_time();
                    }
                    ?>" style="height:100px; width:100%;" class="img-responsive">
                <?php }?>
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
<div class="print-area">
<!-- <html lang="en"> -->
    <div id="html-2-pdfwrapper" class="p-1">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
               
                <div>
                    <?php
                    $date = $result->presdate;
                    ?>
                    <table width="100%" class="mb-0">
                        <tr>
                            <td><?php echo $this->lang->line('prescription'); ?> <?php echo $this->customlib->getSessionPrefixByType('opd_prescription').$result->prescription_id; ?></td> 
                            <td class="text-right rtl-text-left"><?php echo $this->lang->line('date'); ?> : <?php
                                if (!empty($result->presdate)) {
                                    echo $this->customlib->YYYYMMDDTodateFormat($date);
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                    <div class="divider"></div>
                    <table width="100%" class="noborder_table mb-0">
                             <tr>
                                <th width="25%"><?php echo $this->lang->line("opd_id"); ?></th>
                                <td width="25%"><?php echo $this->customlib->getSessionPrefixByType('opd_no') .$result->opd_detail_id ; ?></td>
                                <th width="25%"><?php echo $this->lang->line("checkup_id"); ?></th>
                                <td width="25%"><?php echo $this->customlib->getSessionPrefixByType('checkup_id') .$result->visitid ; ?></td>                        
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("patient_name"); ?></th>
                            <td width="25%"><?php echo $result->patient_name ?> (<?php echo $result->id ?>)</td>
                            <th width="25%"><?php echo $this->lang->line("age"); ?></th>
                            <td><?php
                                echo $this->customlib->getPatientAge($result->age,$result->month,$result->day)." (".$this->lang->line('as_of_date').' '.$this->customlib->YYYYMMDDTodateFormat($result->as_of_date).")";
                                ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line("gender"); ?></th>
                            <td><?php echo $result->gender ?></td>    
                            <th width="25%"><?php echo $this->lang->line("blood_group"); ?></th>
                            <td><?php echo $result->blood_group_name ?></td>                       
                        </tr>
                        
                        <tr>                       
                            <th width="25%"><?php echo $this->lang->line("phone"); ?></th>
                            <td width="25%"><?php echo $result->mobileno ?></td>        
                            <th width="25%"><?php echo $this->lang->line("known_allergies"); ?></th>
                            <td><?php echo $result->any_allergies ?></td>                        
                           
                        </tr>
                        <tr>                           
                            <th width="25%"><?php echo $this->lang->line("email"); ?></th>
                            <td width="25%"><?php echo $result->email ?></td>
                            <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                            <td><?php echo $result->name . " " . $result->surname ?> (<?php echo $result->doctor_id ?>)</td>
                        </tr>
                        <tr>        
                            <th><?php echo $this->lang->line('prescribe_by'); ?></th><td><?php echo $result->prescribe_by_name . " " . $result->prescribe_by_surname ?> (<?php echo $result->prescribe_by_employee_id ?>)</td>                            
                            <th><?php echo $this->lang->line('generated_by'); ?></th><td><?php echo $result->generated_by_name . " " . $result->generated_by_surname ?> (<?php echo $result->generated_by_employee_id ?>)</td>
                        </tr>
                         <?php 
                                                             
                        if (!empty($fields_prescription)) {
                            $display_field = '';
                            foreach ($fields_prescription as $fields_key => $fields_value) {
                            
                            ?>
                                <tr>
                                    <th><?php echo $fields_value->name; ?></th>
                                    <td colspan="3"><?php echo $result->{"$fields_value->name"};?></td>
                                </tr>
                            <?php
                            }
                        }
                        ?>                        
                    </table>
                    <?php 
                    if($result->is_finding_print=='yes'){ 
                        $colspan = 6 ; $width = '50%'; 
                    }else{ 
                        $colspan = 12; $width = '100%';
                    } ?>
                    <table width="100%">
                        <tr>
                        <?php if($result->symptoms !=''){ ?>
                            <td colspan="<?php echo $colspan; ?>" width="<?php echo $width; ?>">
                                <b><?php echo $this->lang->line("symptoms"); ?></b>:<br><?php echo nl2br($result->symptoms)  ?>
                            </td>
                        <?php } ?>
                        <?php if(trim($result->finding_description) != '' ){ ?>
                        <?php if($result->is_finding_print=='yes'){ ?> 
                            <td width="<?php echo $width; ?>">            
                                <b><?php echo $this->lang->line("finding"); ?></b>:<br><?php echo nl2br($result->finding_description)  ?>
                            </td>
                        <?php } }?>
                        </tr>
                    </table>
                    <?php if($result->symptoms != '' || trim($result->finding_description) != ''){
                          if($result->is_finding_print == 'yes'){?>
                        <div class="divider mb-10 mt-10"></div>
                    <?php } } ?>

                    <table width="100%">
                        <tr>
                            <td style="margin-bottom: 0;"><?php echo $result->header_note ?></td>
                        </tr>
                    </table>
                    <div class="divider mb-10 mt-10"></div>   
                    <?php if(!empty($result->medicines)){ ?>            
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
                    <?php } ?>
                    <?php if(!empty($result->tests)){ 

                        $r=$p=0;
                        foreach ($result->tests as $test_key => $test_value) {
                            if($test_value->test_name != ""){
                                $p=1;
                            }
                        }
                        foreach ($result->tests as $test_key => $test_value) {
                            if($test_value->radio_test_name != ""){
                                $r=1;
                            }
                        }

                        ?>    
                    <table width="100%">
                        <tr>
                           <?php 
                            if($p==1){  ?>
                                <th><?php echo $this->lang->line("pathology_test");  ?></th>
                                <?php  }   ?>
                            <?php  if($r==1){  ?>
                                <th><?php echo $this->lang->line("radiology_test"); ?></th>
                                <?php   }  ?>
                        </tr>
                        <tr>
                            <td width="50%"><?php $sl=''; foreach ($result->tests as $test_key => $test_value) {  ?>
                                <table >   
                                    <?php if($test_value->test_name != ""){ $sl++;?> <tr>
                                    <td><?php echo $sl.'. '.$test_value->test_name." (".$test_value->short_name.")"; ?></td></tr>        
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
                  
                    <table width="100%">
                        <tr>
                            <td><?php echo $result->footer_note; ?></td>
                        </tr>
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                   
                </div>
            </div>
            <!--/.col (left) -->
        </div>
    </div>
</div>
</div>
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