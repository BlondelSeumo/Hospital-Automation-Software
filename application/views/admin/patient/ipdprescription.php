<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
<!-- <div class="print-area"> -->
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('prescription'); ?></title>
    </head>
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="pprinta4">
                <?php  if (!empty($print_details['print_header'])) { ?>
                    <img src="<?php
                    if (!empty($print_details['print_header'])) {
                        echo base_url() . $print_details['print_header'].img_time();
                    }
                    ?>" style="height:100px; width:100%;" class="img-responsive">
                <?php }?>
                    <div style="height: 10px; clear: both;"></div>
                </div> 
                <div>
                    <?php
                    $date = $result->presdate;
                    ?>
                    <table width="100%" class="printablea4">
                        <tr>
                            <th><?php echo $this->lang->line('prescription'); ?>: <?php echo $this->customlib->getSessionPrefixByType('ipd_prescription').$result->prescription_id; ?></th> <td></td>
                            <th class="text-right"></th>
                            <th class="text-right rtl-text-left"><?php echo $this->lang->line('date'); ?> : <?php
                                if (!empty($result->presdate)) {
                                    echo $this->customlib->YYYYMMDDTodateFormat($date);
                                }
                                ?>
                            </th>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <table width="100%" class="printablea4">
                        <tr>
                            <th><?php echo $this->lang->line("patient_name"); ?></th>
                            <td><?php echo composePatientName($result->patient_name,$result->id); ?></td>
                            <th><?php echo $this->lang->line("age"); ?></th>
                            <td><?php
                                echo $this->customlib->getPatientAge($result->age,$result->month,$result->day);
                                ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line("gender"); ?></th>
                            <td><?php echo $this->lang->line(strtolower($result->gender)) ?></td>
                            <th width="25%"><?php echo $this->lang->line("blood_group"); ?></th>
                            <td width="25%"><?php echo $result->blood_group_name ?></td>                 
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line("phone"); ?></th>
                            <td><?php echo $result->mobileno ?></td>
                            <th width="25%"><?php echo $this->lang->line("prescribe_by"); ?></th>
                            <td width="25%"><?php echo composeStaffNameByString($result->priscribe_by_name,$result->priscribe_by_surname,$result->priscribe_by_employee_id); ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("email"); ?></th>
                            <td width="25%"><?php echo $result->email ?></td>
                            <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                            <td><?php echo composeStaffNameByString($result->name,$result->surname,$result->employee_id); ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("generated_by"); ?></th>
                            <td width="25%"><?php echo composeStaffNameByString($result->staff_name,$result->staff_surname,$result->staff_employee_id); ?></td>
                        </tr>                      

                       <?php if($result->attachment!=""){ ?>
                        <tr>        
                            <th><?php echo $this->lang->line('document'); ?></th><td><a href="<?php echo site_url('admin/prescription/downloadprescription/'.$result->prescription_id);  ?>" class='btn btn-default btn-xs'  title="<?php echo $this->lang->line('download') ?>"><i class='fa fa-download'></i></a></td>
                            
                        </tr>
                        <?php } ?>                         
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
                    <hr> 
                    <?php if($result->is_finding_print=='yes'){ $colspan = 6 ; $width = '50%'; }else{ $colspan = 12; $width = '100%';                    
                    } ?>
 <?php
                    if($result->symptoms !='' && trim($result->finding_description) != ''){
                        
$width = '50%';
                    }else{
                        
$width = '100%';
                    }

                    ?>
                    <table width="100%" class="printablea4">
                        <tr>
                            <?php if($result->symptoms !=''){ ?>
                                <td width="<?php echo $width; ?>">
                                    <b><?php echo $this->lang->line("symptoms"); ?></b>:<br><?php echo nl2br($result->symptoms)  ?>
                                </td>
                            <?php } ?>

                            <?php if(trim($result->finding_description) != ''){ ?>
                           
                           <td width="<?php echo $width; ?>">                   
                                <b><?php echo $this->lang->line("finding"); ?></b>:<br>
                                <?php echo nl2br($result->finding_description); ?>
                            </td>
                            <?php 
                        }
                         ?>
                        </tr>
                    </table>  
                    
                    <?php if(trim($result->finding_description) !='' || $result->symptoms !=''){ ?>
                        <div class="divider mt-10 mb-10"></div>
                    <?php } ?>

                    <table width="100%" class="printablea4">
                        <tr>
                            <td style="margin-bottom: 0;"><?php echo $result->header_note ?></td>
                        </tr>
                    </table>

                    <?php if(!empty($result->medicines)){ ?>
               
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
                                <th width="20%" class="text text-center"><?php echo $this->lang->line("instruction"); ?></th> 
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
                            if($test_value->test_name == ""){
                                $r=1;
                            }
                        }
                    ?>    
                    <table class="table table-striped table-hover" width="100%">
                        <tr>
                            <?php 
                            if($p==1){
                                ?>
                                <th><?php echo $this->lang->line("pathology_test");  ?></th>
                                <?php
                            }
                            ?>
                            <?php 
                            if($r==1){
                                ?>
                                <th><?php echo $this->lang->line("radiology_test"); ?></th>
                                <?php
                            }
                            ?>
                           
                        </tr>
                        <tr>
                            <?php 
                            if($p==1){
                                ?>
                            <td ><?php $sl=''; foreach ($result->tests as $test_key => $test_value) {  ?>
                                <table >   
                                    <?php if($test_value->test_name != ""){ $sl++;?> <tr>
                                    <td><?php echo $sl.'. '.$test_value->test_name." (".$test_value->short_name.")"; ?></td>   </tr>        
                                    <?php } ?>                             
                                </table>    
                                <?php } ?>
                            </td>
                             <?php }
                            if($r==1){
                                ?>
                            <td><?php $slradiology=''; foreach ($result->tests as $test_key => $test_value) {  ?>
                                <table>   
                                    <?php if($test_value->test_name == ""){ $slradiology++; ?> <tr>
                                    <td><?php echo $slradiology.'. '.$test_value->radio_test_name." (".$test_value->radio_short_name.")"; ?></td> </tr>                                 
                                    <?php } ?>                             
                                </table>   
                                <?php } ?>
                            </td>
                        <?php } ?>
                        </tr>
                    </table>
                    <?php } ?>          
                    
                    <table width="100%" class="printablea4">
                        <tr>
                            <td><?php echo $result->footer_note; ?></td>
                        </tr>
                    </table>

                    <div class="divider mt-10 mb-10"></div>
                        <div class="footer-fixed printfooter">                
                            <?php
                                if (!empty($print_details['print_footer'])) {
                                    echo $print_details['print_footer'];
                                }
                                ?>
                        </div>        
                </div>
            </div>
            <!--/.col (left) -->
        </div>
    </div>
</html>