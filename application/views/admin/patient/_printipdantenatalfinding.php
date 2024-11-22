<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('prescription'); ?></title>
    </head>
        <div class="fixed-print-header"> 
                    <?php  if (!empty($print_details[0]['print_header'])) { ?>
                        <img src="<?php
                        if (!empty($print_details[0]['print_header'])) {
                            echo base_url() . $print_details[0]['print_header'].img_time();
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
    <div id="html-2-pdfwrapper" class="p1">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                
                <div>                    
                   <label id="printantehead"></label>                 
                   <table width="100%" class="printablea4">
                        <td class="text-right rtl-text-left"><b><?php echo $this->lang->line('date'); ?> : <?php
                                if (!empty($result->antenatal_date) && $result->antenatal_date!="") {
                                    echo $this->customlib->YYYYMMDDHisTodateFormat($result->antenatal_date);
                                }
                                ?></b>
                            </td>
                    </table>
                    <table width="100%" class="noborder_table">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("ipd_no"); ?></th>
                            <td width="25%"><?php echo $this->customlib->getSessionPrefixByType('ipd_no') .$result->ipdid ; ?></td> 
							<th width="25%"><?php echo $this->lang->line("patient_name"); ?></th>
                            <td width="25%"><?php echo $result->patient_name ?> (<?php echo $result->id ?>)</td>
                        </tr> 
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("age"); ?></th>
                            <td><?php
                                echo $this->customlib->getPatientAge($result->age,$result->month,$result->day);
                                ?></td>
                             <th width="25%"><?php echo $this->lang->line("blood_group"); ?></th>
                            <td><?php echo $result->blood_group; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("gender"); ?></th>
                            <td><?php echo $this->lang->line(strtolower($result->gender)) ?></td>
							<th width="25%"><?php echo $this->lang->line("phone"); ?></th>
                            <td width="25%"><?php echo $result->mobileno ?></td> 
                        </tr>
                        <tr>
                              
                            <th width="25%"><?php echo $this->lang->line("email"); ?></th>
                            <td width="25%"><?php echo $result->email ?></td>
                            <th width="25%"><?php echo $this->lang->line("known_allergies"); ?></th>
                            <td><?php echo $result->known_allergies; ?></td>                        
                        </tr>
                    </table>
                    <div class="divider"></div>
                     <h4><?php echo $this->lang->line('primary_examine'); ?></h4>
                     <table width="100%" class=" noborder_table">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('bleeding'); ?></th>
                            <td width="25%"><?php echo $result->bleeding ; ?></td>
                            <th width="25%"><?php echo $this->lang->line('headache'); ?></th>
                            <td width="25%"><?php echo $result->headache ; ?></td>                        
                        </tr>
                        <tr> 
                            <th width="25%"><?php echo $this->lang->line('pain'); ?></th>
                            <td width="25%"><?php echo $result->pain ?></td>
                            <th width="25%"><?php echo $this->lang->line('constipation'); ?></th>
                            <td width="25%"><?php echo $result->constipation; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('vomiting'); ?></th>
                            <td width="25%"><?php echo $result->vomiting; ?> </td>
                            <th width="25%"><?php echo $this->lang->line('cough'); ?></th>
                            <td><?php  echo $result->cough;  ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('vaginal'); ?></th>
                            <td><?php echo $result->vaginal ?></td>
                            <th width="25%"><?php echo $this->lang->line("weight"); ?></th>
                            <td><?php echo $result->antenatal_weight ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('height'); ?></th>
                            <td><?php echo $result->antenatal_height ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("discharge"); ?></th>
                            <td width="25%"><?php echo $result->discharge ?></td>   
                            <th width="25%"><?php echo $this->lang->line("oedema"); ?></th>
                            <td width="25%"><?php echo $result->oedema ?></td>
                        </tr> 
                        <tr>        
                            <th><?php echo $this->lang->line('condition'); ?></th><td><?php echo $result->general_condition ; ?></td>                       
                        </tr>
                        <tr><th><?php echo $this->lang->line('special_findings_and_remark'); ?></th><td><?php echo $result->finding_remark ; ?></td></tr>
                        <tr>        
                            <th><?php echo $this->lang->line('pelvic_examination'); ?></th><td><?php echo $result->pelvic_examination ; ?></td>
                        </tr> 
                        <tr>        
                            <th><?php echo $this->lang->line('sp'); ?></th><td><?php echo $result->sp ; ?></td>
                        </tr> 
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                    <h4><?php echo $this->lang->line('antenatal_examine'); ?></h4>
                     <table width="100%" class="printablea4">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('uter_size'); ?></th>
                            <td width="25%"><?php echo $result->uter_size ; ?></td>
                            <th width="25%"><?php echo $this->lang->line('uterus_size'); ?></th>
                            <td width="25%"><?php echo $result->uterus_size ; ?></td>                        
                        </tr>
                        <tr> 
                            <th width="25%"><?php echo $this->lang->line('presentation_position'); ?></th>
                            <td width="25%"><?php echo $result->presentation_position ?></td>
                            <th width="25%"><?php echo $this->lang->line('presenting_part_to_brim'); ?></th>
                            <td width="25%"><?php echo $result->brim_presentation; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('foeta_heart'); ?></th>
                            <td width="25%"><?php echo $result->foeta_heart; ?> </td>
                            <th width="25%"><?php echo $this->lang->line('blood_pressure'); ?></th>
                            <td><?php echo $result->blood_pressure; ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('vaginal'); ?></th>
                            <td><?php echo $result->vaginal ?></td>
                            <th width="25%"><?php echo $this->lang->line('antenatal_weight'); ?></th>
                            <td><?php echo $result->antenatal_weight ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('antenatal_oedema'); ?></th>
                            <td><?php echo $result->antenatal_oedema ?></td>
                            <th width="25%"><?php echo $this->lang->line('urine_sugar'); ?></th>
                            <td><?php echo $result->urine_sugar ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('urine_aaibumen'); ?></th>
                            <td><?php echo $result->urine; ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('remark') ; ?></th>
                            <td width="25%"><?php echo $result->remark ?></td>
                        </tr> 
                         <tr>
                            <th width="25%"><?php echo $this->lang->line('next_visit'); ?></th>
                            <td width="25%"><?php echo $result->next_visit ?></td>
                        </tr> 
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                </div>
            </div>
            <!--/.col (left) -->
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
</html>

<script type="text/javascript">
     $('#edit_deletefinding').html("<a href='#'' onclick='printipdantenatalprescription(<?php echo $id;?>)'  data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><a href='#'' onclick='edit_ipdantenatalprescription(<?php echo $id;?>)' data-target='#edit_prescription' data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><a onclick='delete_ipdantenatal(<?php echo $id;?>)'  href='#'  data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a>");
 
    function delete_ipdantenatal(id) {  
      
        var msg = '<?php echo $this->lang->line("are_you_sure"); ?>';
        if (confirm(msg)) {
            $.ajax({
            url: '<?php echo base_url(); ?>admin/antenatal/deleteipdantenatal/'+id,
                success: function (res) {
                      successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                  window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }    
</script>