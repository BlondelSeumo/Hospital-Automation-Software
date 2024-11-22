<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
        <style type="text/css">

            .printablea4{width: 100%;}
            /*.printablea4 p{margin-bottom: 0;}*/
            .printablea4>tbody>tr>th,
            .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 12px;}
        </style>
    </head>
    <div class="fixed-print-header">
        <?php if (!empty($print_details[0]['print_header'])) {
        ?>
                            <div class="pprinta4">
                                <img src="<?php
    if (!empty($print_details[0]['print_header'])) {
            echo base_url() . $print_details[0]['print_header'].img_time();
        }
        ?>" class="img-responsive" style="height:100px; width: 100%;">
                            </div>
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
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div>
                    

                    <div class="divider mt-10 mb-10"></div>
                    <table class="printablea4" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <th width="15%"><?php echo $this->lang->line('name'); ?></th>
                            <td width="20%"><?php echo $result["patient_name"]; ?></td>
                            <th width="15%"><?php echo $this->lang->line('age'); ?></th>
                            <td width="20%" class="text-left"><?php
if (!empty($result["age"])) {
    echo $result['age'] . "  Years";
}
if (!empty($result['month'])) {
    echo $result["month"] . " Month";
}?>

                            </td>
                            <th width="10%"><?php echo $this->lang->line('gender'); ?></th>
                            <td width="20%" class="text-left"><?php echo $result["gender"]; ?></td>
                        </tr>
                         <tr>
                            <th width="20%"><?php echo $this->lang->line('admission') . " " . $this->lang->line('date'); ?></th>
                            <td width="25%"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['appointment_date'])); ?></td>
                            <th><?php echo $this->lang->line('discharged') . " " . $this->lang->line('date'); ?></th>
                            <td width="30%" class="text-left"><?php echo date($this->customlib->getHospitalDateFormat(true, false), strtotime($result['discharge_date'])); ?></td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('diagnosis'); ?></th>
                            <td width="25%"><?php echo $result["disdiagnosis"]; ?></td>
                        </tr>
                         <tr>
                            <th width="20%"><?php echo $this->lang->line('address'); ?></th>
                            <td width="25%"><?php echo $result['address']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('note'); ?></th>
                            <td width="30%" class="text-left"><?php echo nl2br($result['summary_note']); ?></td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <table class="printablea4" id="testreport" width="100%">
                    <tr>
                        <th width="40%"><?php echo $this->lang->line('investigations') . " " . $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('treatment_at_home'); ?></th>
                    </tr>
                    <tr>
                        <td width="40%"><?php echo nl2br($result["summary_investigations"]); ?></td>
                        <td><?php echo nl2br($result["summary_treatment_home"]); ?></td>
                    </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                   
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

  function printData(insert_id,opdid) {
        $.ajax({
            url: '<?php echo base_url() ?>patient/dashboard/getsummaryopdDetails',
            type: 'POST',
            data: {id: insert_id,opdid: opdid,print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }
     
</script>