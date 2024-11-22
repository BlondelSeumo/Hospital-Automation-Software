<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                <?php $this->load->view('admin/report/_finance');?>  
                <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line("patient_bill_report"); ?></h3>
                    </div>                
                 
                     <div class="box-body">
                        <form action="<?= base_url(); ?>admin/patient/patientbillreport" method="post" class="form-inline">
                            <input type="hidden" name="ci_csrf_token" value="">
                            <div class="form-group" id="patient_id">
                                <label><?php echo $this->lang->line('case_id'); ?></label><small class="req"> *</small>
                                <input id="case_reference_id" name="case_reference_id" placeholder="<?= $this->lang->line("case_id"); ?>" type="text" class="form-control" value="<?php echo set_value('case_reference_id'); ?>"  />                                
                            </div>                    
                            <div class="form-group">             
                                <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                            </div>
                        <span class="text-danger"><?php echo form_error('case_reference_id'); ?></span>
                        </form>
                    </div>                                   
                    <div class="box border0 clear">
                        <div class="ptbnull"></div>
                        <div class="box-body table-responsive" id="bill_report">
                            <div id="printhead"><h5 class="text-center"><?php echo $this->lang->line("patient_bill_report") . "<br>";?></h5></div>
                             <div>
                            <div class="box-header with-border pl0" id="headreport" style="display: none" >
                                <h3 class="box-title"><?php if(!empty($charge_payment_data)){ echo
                                    composePatientName($charge_payment_data[0]->patient_name,$charge_payment_data[0]->patient_id).' '.$this->lang->line("bill_report"); } ?></h3>
                          </div>
                        </div>
                            <div class="download_label"><?php echo $this->lang->line('ipd_report'); ?></div>
                            <div class="pull-right pt4">
                                <a class="btn btn-default btn-xs pull-right" id="btnExport" onclick="exportToExcel();"> <i class="fa fa-file-excel-o"></i></a>
                                <a class="btn btn-default btn-xs pull-right " id="print" onclick="printDiv()" ><i class="fa fa-print"></i></a> 
                            </div>    
                            <table class="table table-striped table-bordered table-hover allajaxlist" id="headerTable">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('module'); ?></th>
                                        <th><?php echo $this->lang->line('opd_no'); ?></th>
                                        <th><?php echo $this->lang->line('ipd_no'); ?></th>
                                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                                        <th><?php echo $this->lang->line('payment_mode'); ?></th>
                                        <th><?php echo $this->lang->line('payment_date'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('payment_amount')."(".$currency_symbol.")"; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        if(!empty($charge_payment_data))
                                        {
                                            $grand_total_charge = 0;
                                            $grand_total_payment = 0;
                                            
                                            foreach($charge_payment_data as $charge_payment)
                                            {
                                                $bill_prefix=""; 
                                                if($charge_payment->module=='Pathology'){
                                                    $bill_prefix = $this->customlib->getSessionPrefixByType('pathology_billing');
                                                }else if($charge_payment->module=='Pharmacy'){
                                                    $bill_prefix = $this->customlib->getSessionPrefixByType('pharmacy_billing');
                                                }
                                                else if($charge_payment->module=='Radiology'){
                                                    $bill_prefix = $this->customlib->getSessionPrefixByType('radiology_billing');
                                                }
                                                else if($charge_payment->module=='Ambulance'){
                                                    $bill_prefix = $this->customlib->getSessionPrefixByType('ambulance_call_billing');
                                                }
                                                else if($charge_payment->module=='Blood Bank'){
                                                    $bill_prefix = $this->customlib->getSessionPrefixByType('blood_bank_billing');
                                                }
                                    ?>
                                    <tr>
                                     
                                        <td><?php echo $charge_payment->module; ?></td>
                                        <td><?php 
                                            if(isset($charge_payment->opd_id)){
                                                echo $this->customlib->getSessionPrefixByType('opd_no').$charge_payment->opd_id; 
                                            }?>
                                        </td>
                                        <td><?php 
                                            if(isset($charge_payment->ipd_id)){
                                                echo $this->customlib->getSessionPrefixByType('ipd_no').$charge_payment->ipd_id; 
                                                }?>
                                        </td>
                                        <td><?php 
                                            if(isset($charge_payment->bill_no)){
                                                    echo $bill_prefix.$charge_payment->bill_no; 
                                                }?>
                                        </td>
                                        <td>
                                            <ul style="list-style-type:none;">
                                                <?php foreach ($charge_payment->payments as $payment) { ?>
                                                    <li><?php
                                                        echo $this->lang->line(strtolower($payment->payment_mode)); 
                                                        if( $payment->payment_mode == "Cheque"){
                                                            echo " No. ".$payment->cheque_no."<br />".$this->customlib->YYYYMMDDTodateFormat($payment->cheque_date);
                                                        }
                                                        ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </td>
                                        <td>
                                            <ul style="list-style-type:none">
                                                <?php foreach ($charge_payment->payments as $payment) { ?>
                                                    <li><?= $this->customlib->YYYYMMDDHisTodateFormat($payment->payment_date, $this->customlib->getHospitalTimeFormat()); 
                                                        if( $payment->payment_mode == "Cheque"){
                                                            echo "<br /><br />";
                                                        } ?>                                                   
                                                    
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </td>
                                        <td class="text-right">
                                            <ul style="list-style-type:none">                                           
                                            <?php 
                                            $total = 0;
                                            foreach($charge_payment->payments as $payment){ ?>
                                                <li>
                                                    <?php $total += $payment->amount;
                                                        echo $payment->amount; if( $payment->payment_mode == "Cheque"){
                                                            echo "<br /><br />";
                                                        }
                                                    ?>
                                                </li>
                                                <?php } ?>
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td><b><?php echo $this->lang->line("total_charge"); ?>: </b>
                                            <?php if($charge_payment->charge==''){ 
                                                    $charge = "0"; 
                                                    echo $currency_symbol.''.$charge;
                                                }else{
                                                    $charge = $charge_payment->charge; 
                                                    echo $currency_symbol.''.$charge;
                                                } ?>                                            
                                        </td>
                                        <td class="text-right" ><b><?= $this->lang->line("total_payment"); ?>: </b><?php echo $currency_symbol.''. amountFormat($total); ?></td>
                                        <?php 
                                        $grand_total_charge += $charge; 
                                        $grand_total_payment += $total; 
                                        ?>
                                    </tr>
                                    <?php 
                                            }                                      
                                    ?>
                                     <tr>
                                        <?php if(isset($grand_total_charge) && isset($grand_total_payment)){ ?>
                                            <td colspan="4"></td>
                                             <td><b><?php echo $this->lang->line("refund"); ?>: </b>
                                                <?php if(isset($total_refund_amount->payment_amount) && $total_refund_amount->payment_amount==''){
                                                    echo $currency_symbol."0";
                                                }else{
                                                    echo $currency_symbol.''. amountFormat($total_refund_amount->payment_amount);
                                                } ?>                                                
                                            </td>
                                            <td><b><?php echo $this->lang->line("grand_total_charge"); ?>: </b>
                                                <?php if($grand_total_charge==''){
                                                    echo $currency_symbol."0";
                                                }else{
                                                    echo $currency_symbol.''. amountFormat($grand_total_charge);
                                                } ?>                                                
                                            </td>
                                            <td class="text-right" ><b><?php echo $this->lang->line("grand_total_payment"); ?>: </b>
                                            
                                                <?php 
                                                    if($grand_total_payment==''){
                                                        echo $currency_symbol."0";
                                                    }else{
                                                        echo $currency_symbol.''. amountFormat($grand_total_payment);
                                                    }
                                                ?>
                                            </td>                                       
                                     </tr>
                                      <?php } } else{ ?>
                                       <tr><td colspan="7"><?php echo $this->session->flashdata('no_record'); ?></td></tr>
                                     <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
</div>

    
<script>    
    document.getElementById("print").style.display = "block";
    document.getElementById("btnExport").style.display = "block";
    document.getElementById("printhead").style.display = "none";
     document.getElementById("headreport").style.display = "block";

    function printDiv() {
        document.getElementById("print").style.display = "none";
        document.getElementById("btnExport").style.display = "none";
        document.getElementById("printhead").style.display = "block";
        document.getElementById("headreport").style.display = "none";
        var divElements = document.getElementById('bill_report').innerHTML;
        var oldPage = document.body.innerHTML;
        document.body.innerHTML =
                "<html><head><title>Patient Bill Report</title></head><body>" +
                divElements + "</body>";
        window.print();
        document.body.innerHTML = oldPage;
        document.getElementById("printhead").style.display = "none";
        location.reload(true);
    }

    function fnExcelReport()
    {
        exportToExcel();      
    }

    function exportToExcel(){
        var htmls = "";
        var uri = 'data:application/vnd.ms-excel;base64,';
        var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'; 
        var base64 = function(s) {
            return window.btoa(unescape(encodeURIComponent(s)))
        };

        var format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) {
                return c[p];
            })
        };
        var tab_text = "<tr >";
                     var textRange;
         var j = 0;
          var val="";
         tab = document.getElementById('headerTable'); // id of table

         for (j = 0; j < tab.rows.length; j++)
         {             
             tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
       }

            var ctx = {
                worksheet : 'Worksheet',
                table : tab_text
            }


            var link = document.createElement("a");
            link.download = "Patient Bill Report.xls";
            link.href = uri + base64(format(template, ctx));
            link.click();
    }
</script>