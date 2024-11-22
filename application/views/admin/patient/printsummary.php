<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
        <style type="text/css">
            .printablea4{width: 100%;}
            .printablea4>tbody>tr>th,
            .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 12px;}
        </style>
    </head>
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="">
                    <?php if (!empty($print_details[0]['print_header'])) { ?>
                        <div class="pprinta4">
                            <img src="<?php
                            if (!empty($print_details[0]['print_header'])) {
                                echo base_url() . $print_details[0]['print_header'].img_time();
                            }
                            ?>" class="img-responsive" style="height:100px; width: 100%;">
                        </div>
                    <?php } ?>
                    <div class="divider mb-10 mt-10"></div>
                    <table class="printablea4" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <th width="15%"><?php echo $this->lang->line('name'); ?></th>
                            <td width="20%"><?php echo $result["patient_name"]; ?></td>
                            <th width="15%"><?php echo $this->lang->line('age'); ?></th>
                            <td width="20%" class="text-left"><?php echo $result["age"]." Years, ".$result["month"]." Month"; ?></td>
                            <th width="10%"><?php echo $this->lang->line('gender'); ?></th>
                            <td width="20%" class="text-left"><?php echo $result["gender"]; ?></td>
                        </tr>
                         <tr>
                            <th width="20%"><?php echo $this->lang->line('admission') . " " . $this->lang->line('date'); ?></th>
                            <td width="25%"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['date'])); ?></td>
                            <th><?php echo $this->lang->line('discharged') . " " . $this->lang->line('date'); ?></th>
                            <td width="30%" class="text-left"><?php echo date($this->customlib->getHospitalDateFormat(true, false), strtotime($result['discharged_date'])); ?></td>
                        </tr>
                         <tr>
                            <th width="20%"><?php echo $this->lang->line('address') ; ?></th>
                            <td width="25%"><?php echo $result['address']; ?></td>
                        </tr>
                        <tr>
                            <th width="20%"><?php echo $this->lang->line('diagnosis') ; ?></th>
                            <td width="25%"><?php  echo $result["diagnosis"];  ?></td>
                        </tr>
                        <tr> <th><?php echo $this->lang->line('operation') ; ?></th>
                            <td width="30%" class="text-left"><?php echo $result["operation"];  ?></td>
                        </tr>
                          <tr>
                             <th><?php echo $this->lang->line('note') ; ?></th>
                            <td width="30%" class="text-left"><?php echo nl2br($result['note']); ?></td>
                        </tr>
                    </table>
                    <div class="divider mt-10 mb-10"></div>
                    <table class="printablea4" id="testreport" width="100%">
                    <tr>
                        <th width="40%"><?php echo $this->lang->line('investigations'); ?></th> 
                        <th><?php echo $this->lang->line('treatment_at_home'); ?></th>
                    </tr>
                    <tr>
                        <td width="40%"><?php echo nl2br($result["investigations"]); ?></td>
                        <td><?php echo nl2br($result["treatment_home"]); ?></td>
                    </tr>
                    </table> 
                    <div class="divider mb-10 mt-10"></div>  
                    <div class="footer-fixed printfooter"> 
                        <p><?php
                            if (!empty($print_details[0]['print_footer'])) {
                                echo $print_details[0]['print_footer'];
                            }
                            ?></p>
                    </div>        
                </div>
            </div>
            <!--/.col (left) -->
        </div>
    </div>
</html>
<script type="text/javascript">
    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pharmacy/deletePharmacyBill/' + id,
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
            url: base_url + 'admin/pharmacy/getBillDetails/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {               
                popup(result);
            }
        });
    }    
</script>