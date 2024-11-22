<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
<style type="text/css">

    .printablea4{width: 100%;}
    .printablea4>tbody>tr>th,
    .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 12px;}
</style>
<html lang="en">
    <head> 
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
    </head>
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="">
                    <div class="pprinta4">
                        <?php if (!empty($print_details[0]['print_header'])) { ?>
                            <img style="height:100px" class="img-responsive" src="<?php echo base_url() . $print_details[0]["print_header"].img_time() ?>">
                        <?php } ?>
                        <div style="height: 10px; clear: both;"></div>
                    </div>
                    <table width="100%" class="printablea4">
                        <tr>
                            <td class="text-left"><?php echo $this->lang->line('bill') . " " ?><?php echo $opd_prefix.$result["opdid"] ?></td>
                            <td class="text-right"><?php echo $this->lang->line('date') . " : " ?><?php
                                    if (!empty($result['appointment_date'])) {
                                        echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['appointment_date']));
                                    }
                                    ?></td>
                        </tr>
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                    <table class="printablea4" cellspacing="0" width="100%">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('name'); ?></th>
                            <td width="25%"><?php echo $result["patient_name"]; ?></td>
                            <th width="25%"><?php echo $this->lang->line('doctor'); ?></th>
                            <td width="25%"><?php echo $result["name"] . " " . $result["surname"]; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $this->lang->line('opd') . " " . $this->lang->line('no'); ?></th>
                            <td><?php echo $opd_prefix.$result['opdid']; ?></td> 
                            <th><?php echo $this->lang->line('organisation'); ?></th>
                            <td><?php echo $result['organisation_name']; ?></td> 
                        </tr>
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                    <table class="printablea4" width="100%">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('case') ?></th>
                            <td width="25%"><?php echo $result['case_type'] ?></td>
                            <th width="25%"><?php echo $this->lang->line('casualty') ?></th>
                            <td width="25%"><?php echo $result['casualty'] ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('symptoms') ?></th>
                            <td width="25%"><?php echo $result['symptoms'] ?></td> 
                            <th width="25%"><?php echo $this->lang->line('note') ?></th>
                            <td width="25%"><?php echo $result["note_remark"] ?></td> 
                        </tr>
                    </table>
                    <div class="divider mb-10 mt-10"></div> 
                    <table class="printablea4" width="100%">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('paid') . " " . $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <td width="25%"><?php echo $result["paid_amount"] ?></td> 
                            <th width="25%"></th>
                            <td width="25%"></td> 
                        </tr>
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                    <div class="footer-fixed printfooter">  
                        <?php if (!empty($print_details[0]['print_footer'])) { ?>    
                            <p class="ptt10"><?php echo $print_details[0]['print_footer']; ?></p>
                        <?php } ?>
                    </div>    
                </div>
            </div>
        </div>
    </div>
</html>