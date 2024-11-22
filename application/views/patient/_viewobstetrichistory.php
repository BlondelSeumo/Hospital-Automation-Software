
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
<html lang="en">
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                
                <div>
                    <table width="100%" class="printablea4">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("patient_name"); ?></th>
                            <td width="25%"><?php echo $result['patient_name'] ?> (<?php echo $result['patient_unique_id'] ?>)</td>
                            <th width="25%"><?php echo $this->lang->line("age"); ?></th>
                            <td><?php
                                echo $this->customlib->getPatientAge($result['age'],$result['month'],$result['day']);
                                ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line("gender"); ?></th>
                            <td><?php echo $this->lang->line(strtolower($result['gender'])) ?></td>
                            <th width="25%"><?php echo $this->lang->line("blood_group"); ?></th>
                            <td><?php echo $result['blood_group']; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("phone"); ?></th>
                            <td width="25%"><?php echo $result['mobileno']; ?></td>   
                            <th width="25%"><?php echo $this->lang->line("email"); ?></th>
                            <td width="25%"><?php echo $result['email'] ?></td>
                        </tr>
                    </table>
                    <hr>
                     <h4><?php echo $this->lang->line('previous_obstetric_history'); ?> </h4>
                     <table width="100%" class="printablea4">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('place_of_delivery'); ?></th>
                            <td width="25%"><?php echo $result['place_of_delivery'] ; ?></td>
                            <th width="25%"><?php echo $this->lang->line('duration_of_pregnancy');; ?></th>
                            <td width="25%"><?php echo $result['pregnancy_duration'] ; ?></td>                        
                        </tr>
                        <tr> 
                            <th width="25%"><?php echo $this->lang->line('complication_in_pregnancy_or_puerperium'); ?></th>
                            <td width="25%"><?php echo $result['pregnancy_complications'] ?></td>
                            <th width="25%"><?php echo $this->lang->line('birth_weight'); ?></th>
                            <td width="25%"><?php echo $result['birth_weight']; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('gender'); ?></th>
                            <td width="25%"><?php echo $this->lang->line(strtolower($result['obstetric_gender'])); ?> </td>
                            <th width="25%"><?php echo $this->lang->line('infant_feeding'); ?></th>
                            <td><?php echo $result['infant_feeding'];?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('birth_status'); ?></th>
                            <td><?php echo $this->lang->line(strtolower($result['alive_dead'])) ?></td>
                        </tr>
                        <?php if($result['alive_dead']=='dead'){ ?>
                            <tr>
                                <th width="25%"><?php echo $this->lang->line('alive'); ?> / <?php echo $this->lang->line('dead'); ?> <?php echo $this->lang->line('date'); ?></th>
                                <td width="25%"><?php echo $this->customlib->YYYYMMDDTodateFormat($result['date']); ?> </td>
                                <th width="25%"><?php echo $this->lang->line('death_cause'); ?></th>
                                <td><?php echo $result['death_cause'];?></td>
                            </tr>
                       <?php } ?>
                        <tr><td colspan="4"><div class="divider mt-10 mb-10"></div></td></tr>
                        <tr>
                            <th><?php echo $this->lang->line('previous_medical_history'); ?></th>
                            <td colspan="3"><?php echo $result['previous_medical_history'] ?></td>
                        </tr>                        
                        <tr>
                            <th><?php echo $this->lang->line('special_instruction'); ?></th>
                            <td colspan="3"><?php echo $result['special_instruction'] ?></td>
                        </tr>
                        <tr><td></td></tr>
                    </table>                  
                </div>
            </div>
            <!--/.col (left) -->
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