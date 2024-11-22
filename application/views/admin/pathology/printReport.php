<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
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
                    <table width="100%" class="printablea4">
                        <tr>
                            <td class="text-left"><?php echo $this->lang->line('report') . "# "; ?><?php echo $result["bill_no"] ?>
                            </td>
                            <td class="text-right"><?php echo $this->lang->line('date') . " : "; ?><?php echo date($this->customlib->getHospitalDateFormat(true, false), strtotime($result['reporting_date'])) ?>
                            </td>
                        </tr>
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                    <table class="printablea4" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <th><?php echo $this->lang->line('name'); ?></th>
                            <th><?php echo $this->lang->line('doctor'); ?></th>
                            <th><?php echo $this->lang->line('test') . " " . $this->lang->line('name'); ?></th> 
                            <th><?php echo $this->lang->line('short') . " " . $this->lang->line('name'); ?></th>
                            <th><?php echo $this->lang->line('pathology') . " " . $this->lang->line('report'); ?></th>
                        </tr>
                        <tr>
                           <td><?php echo $result["patient_name"]; ?></td>
                           <td><?php echo $result["doctorname"]." ".$result["doctorsurname"]; ?></td>
                        <?php
                        $j = 0;
                        foreach ($detail as $bill) {
                            ?>                           
                                <td><?php echo $bill["test_name"]; ?></td>
                                <td><?php echo $bill["short_name"]; ?></td>
                                <td><?php if (!empty($bill['pathology_report'])){ ?>
                                    <a  <?php if($print != 'yes'){ ?> href="<?php echo base_url().'admin/pathology/download/'. $bill["pathology_report"]; ?>" <?php } ?> class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a>
                                    <?php }  ?>
                                </td>                            
                            <?php
                            $j++;
                        }
                        ?>
                        </tr>
                    </table>
                    <div class="divider mb-10 mt-10"></div>
                    <table class="printablea4" id="testreport" width="100%">
                        <tr>                            
                            <th><?php echo $this->lang->line('description'); ?></th>
                        </tr>
                        <?php
                        $i = 0;
                        foreach ($detail as $bill) {
                            ?>
                            <tr>
                                <td><?php echo $bill['description']; ?></td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                    </table> 
                    <div class="divider mb-10 mt-10"></div>
                     <table class="printablea4" id="testreport" width="100%">
                        <tr>
                            <th><?php echo $this->lang->line('parameter') . " " . $this->lang->line('name'); ?></th> 
                            <th><?php echo $this->lang->line('reference') . " " . $this->lang->line('range'); ?></th>
                            <th><?php echo $this->lang->line('value'); ?></th>
                            <th><?php echo $this->lang->line('unit'); ?></th>                           
                        </tr>
                        <?php
                        $j = 0;
                        foreach ($parameterdetails as $value) {
                            ?>
                            <tr>
                                <td><?php echo $value["parameter_name"]; ?></td>
                                <td><?php echo $value["reference_range"]; ?></td>
                                <td><?php echo $value["pathology_report_value"]; ?></td>
                                <td><?php echo $value["unit_name"]; ?></td>
                            </tr>
                            <?php
                            $j++;
                        }
                        ?>
                    </table> 
                   <div class="divider mb-10 mt-10"></div> 
                    <table class="printablea4" width="100%" style="width:30%; float: right;">                     
                            <tr id="generated_by">
								<th><?php echo $this->lang->line('prepared_by'); ?></th>
							</tr>
                            <tr>
                                <td><?php echo $result["generated_byname"]; ?></td>
                            </tr>                     
                    </table>
                  <div class="divider mb-10 mt-10"></div> 
                  <div class="printfooter footer-fixed">
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
                url: '<?php echo base_url(); ?>admin/pathology/deletePharmacyBill/' + id,
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
	
    function printData(id,parameter_id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/pathology/getReportDetails/'  + id +'/'+parameter_id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {               
                popup(result);
            }
        });
    }     
</script>