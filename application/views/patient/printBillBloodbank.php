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
                <div >
                   
                    <table width="100%" class="printablea4">
                        <tr>
                            <td class="text-left"><?php echo $this->lang->line('bill_no').": ".$this->customlib->getPatientSessionPrefixByType('blood_bank_billing').$result["id"] ?>
                            </td>
                            <td class="text-right"><?php echo $this->lang->line('date') . " : "; ?><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date_of_issue'])) ?>
                            </td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <table class="printablea4" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('patient_name'); ?></th>
                            <td width="25%"><?php echo composePatientName($result['patient_name'],$result['patient_id']); ?></td>
                            <th width="25%"><?php echo $this->lang->line('donor_name'); ?></th>
                            <td width="30%" class="text-left"><?php echo $result["donor_name"]; ?></td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('blood_group'); ?></th>
                            <td width="25%"><?php echo $result["blood_group"]; ?></td>
                            <th width="25%"><?php echo $this->lang->line('bag_no'); ?></th>
                            <td width="30%" class="text-left"><?php echo $result['bag_no']; ?></td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <table class="printablea4" id="testreport" width="100%">
                        <tr>
                            <th class="text-right"><?php echo $this->lang->line('amount'); ?></th>
                        </tr>
                        <tr>
                            <td class="text-right"><?php echo $currency_symbol . " " . $result["net_amount"]; ?></td>
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
    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/dashboard/getBillDetailsBloodbank/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }
    
</script>