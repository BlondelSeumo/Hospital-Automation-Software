<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
        <style type="text/css">
            .printablea4{width: 100%;}
            .printablea4>tbody>tr>th,
            .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 12px;}
        </style>
    </head>
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
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="">
                   
                    <table width="100%" class="printablea4">
                        <tr>
                            <td class="text-left"><?php echo $this->lang->line('bill') . " "; ?><?php echo $this->customlib->getSessionPrefixByType('operation_theater_billing').$result["id"] ?>
                            </td>
                            <td class="text-right"><?php echo $this->lang->line('date') . " : "; ?><?php echo date($this->customlib->getHospitalDateFormat(true, false), strtotime($result['date'])) ?>
                            </td>
                        </tr>
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                    <table class="printablea4" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('patient') . " " . $this->lang->line('name'); ?></th>
                            <td width="25%"><?php echo $result["patient_name"]; ?></td>
                            <th width="25%"><?php echo $this->lang->line('operation') . " " . $this->lang->line('name'); ?></th>
                            <td width="30%"><?php echo $result["operation_name"]; ?></td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('operation') . " " . $this->lang->line('type'); ?></th>
                            <td width="25%"><?php echo $result["operation_type"]; ?></td>
                            <th width="25%"><?php echo $this->lang->line('doctor'); ?></th>
                            <td width="30%" class="text-left"><?php echo $result['doctorname']." ".$result['doctorsurname']; ?></td> 
                        </tr> 
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                    <table class="printablea4" id="testreport" width="100%">
                        <tr>
                            <th><?php echo $this->lang->line('amount')." (".$currency_symbol.")"; ?></th>
                        </tr>
                        <?php
                        $j = 0;
                        foreach ($detail as $bill) {
                            ?>
                            <tr>
                                <td><?php echo $bill["apply_charge"]; ?></td>
                            </tr>
                            <?php
                            $j++;
                        }
                        ?>        
                    </table> 
                    <div class="divider mb-10 mt-10"></div>

                    <table class="printablea4" style="width: 30%; float: right;">
                        <?php if (!empty($result["total"])) { ?>
                            <tr>
                                <th><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                <td><?php echo $result["total"]; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($result["discount"])) { ?>
                            <tr>
                                <th><?php
                                    echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                <td><?php echo $result["discount"]; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($result["tax"])) { ?>
                            <tr>
                                <th><?php
                                    echo $this->lang->line('tax') . " (" . $currency_symbol . ")";  ?></th>
                                <td><?php echo $result["tax"]; ?></td>
                            </tr>
                        <?php } ?>
                        <?php
                        if ((!empty($result["discount"])) && (!empty($result["tax"]))) {
                            if (!empty($result["net_amount"])) {
                                ?>
                                <tr>
                                    <th><?php
                                        echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")";  ?></th>
                                    <td><?php echo $result["net_amount"]; ?></td>
                                </tr>
                                <?php
                            }
                        }   ?>
                        <?php if (!empty($result["note"])) { ?>
                            <tr>
                                <th><?php echo $this->lang->line('note'); ?></th>
                                <td><?php echo $result["note"]; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if (($print) != 'yes') { ?>
                            <tr id="generated_by">
                                <th><?php echo $this->lang->line('collected_by'); ?></th>
                                <td class="text-right"><?php echo $result["generated_byname"]; ?></td>
                            </tr>
                        <?php } ?>
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
    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/vehicle/deletePharmacyBill/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }
    
    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/operationtheatre/getBillDetails/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }     
</script>