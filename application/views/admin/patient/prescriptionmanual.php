<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line("prescription"); ?></title>
    </head> 

    <div class="fixed-print-header">
                    <?php if (!empty($print_details['print_header'])) { ?>
                         <img src="<?php
                        if (!empty($print_details['print_header'])) {
                            echo base_url() . $print_details['print_header'].img_time();
                        }
                        ?>" style="height:100px; width:100%;" class="img-responsive">
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
    <div id="html-2-pdfwrapper" class="p-1">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
               
                <div class="">
                    <?php 
                    $date = date("Y-m-d");
                    ?>
                    <table width="100%">

                        <tr>
                            <td><?php echo $this->lang->line("opd_no"); ?><?php echo $opd_prefix.$result['opd_details_id']; ?></td> 
                            <td></td>
                            <th class="text-right"></th> 
                            <th class="text-right rtl-text-left"><?php echo $this->lang->line('date'); ?> : <?php
                                echo $this->customlib->YYYYMMDDTodateFormat($date);
                                ?></th>
                        </tr>
                         <tr>
                            <td><?php echo $this->lang->line("checkup_id"); ?><?php echo $this->customlib->getSessionPrefixByType('checkup_id').$visitid; ?></td> 
                            <td></td>                            
                        </tr>
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                    <table width="100%" class="printablea4">
                        <tr>
                            <th width="10%"><?php echo $this->lang->line("patient_name"); ?></th>
                            <td width="15%"><?php echo $result["patient_name"].' ('. $result["patientid"] .')' ?></td>
                            <th width="10%"><?php echo $this->lang->line("age"); ?></th>                  
                            <td>
                                <?php
                                   echo $this->customlib->getPatientAge($result['age'],$result['month'],$result['day'])." (".$this->lang->line('as_of_date').' '.$this->customlib->YYYYMMDDTodateFormat($result['as_of_date']).")";  

                                ?>
                            </td>
                             
                            <th width="10%"><?php echo $this->lang->line("gender"); ?></th>
                            <td><?php echo $result["gender"] ?></td>
                        </tr>
                        
                        <tr>
                            <th width="10%"><?php echo $this->lang->line('consultant_doctor'); ?></th>
                            <td width="10%"><?php echo $result["name"] . " " . $result["surname"].' ('. $result["employee_id"] .')' ?></td>
                            <th width="10%"><?php echo $this->lang->line("address"); ?></th>
                            <td width="10%"><?php echo $result["address"] ?></td>
                            <th width="10%"><?php echo $this->lang->line("blood_group"); ?></th>
                            <td width="10%"><?php echo $blood_group_name; ?></td>
                        </tr>
                         <tr>
                            <th width="10%"><?php echo $this->lang->line('known_allergies');?></th>
                            <td width="10%"><?php echo $result["known_allergies"]; ?></td>                        
                        </tr>
                    </table>
                    <div class="divider mb-10 mt-10"></div> 
                </div>
            </div>
            <!--/.col (left) -->
			
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
</html>

<script type="text/javascript">
    function delete_prescription(id, opdid) {
        var msg = '<?php echo $this->lang->line('are_you_sure'); ?>';
        if (confirm(msg)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/prescription/deletePrescription/' + id + '/' + opdid,
                success: function (res) {
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }
</script>