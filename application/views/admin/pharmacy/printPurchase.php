<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('bill'); ?></title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
    </head>
    <div id="html-2-pdfwrapper" class="p-1">
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
                    <table width="100%" class="">
                        <tr>
                            <td><?php echo $this->lang->line('purchase_no'); ?>  <?php echo $this->customlib->getSessionPrefixByType('purchase_no').$result["id"] ?></td>
                            <td><?php echo $this->lang->line('bill_no'); ?> <?php echo $result["invoice_no"] ?></td>
                            <td class="text-right rtl-text-left"><?php echo $this->lang->line('purchase_date')?> <?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->customlib->getHospitalTimeFormat());?></td>
                        </tr>
                    </table>
                    <div class="divider mb-10"></div>
                    <div class="table-responsive">
                        <table class="printablea4" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <th width="20%"><?php echo $this->lang->line('supplier_name'); ?></th>
                                <td width="25%"><?php echo $result["supplier"]; ?></td>
                                <th width="20%"><?php echo $this->lang->line('supplier_contact'); ?></th>
                                <td width="25%"><?php echo $result["contact"]; ?></td>
                            </tr>
                            <tr>
                                <th width="20%"><?php echo $this->lang->line('contact_person'); ?></th>
                                <td width="25%"><?php echo $result["supplier_person"]; ?></td>
                                <th width="20%"><?php echo $this->lang->line('contact_person_phone'); ?></th>
                                <td width="25%"><?php echo $result["supplier_person_contact"]; ?></td>                        
                            </tr>
                            <tr>
                                <th width="20%"><?php echo $this->lang->line('drug_license_number'); ?></th>
                                <td width="25%"><?php echo $result["supplier_drug_licence"]; ?></td>
                                <th width="20%"><?php echo $this->lang->line('address'); ?></th>
                                <td width="25%"><?php echo $result['address']; ?></td> 
                            </tr> 
                        </table>
                     </div>   
                     <div class="divider mb-10 mt-10"></div>
                    <div class="table-responsive">
                       <table id="testreport" width="100%" cellspacing="5">
                       <tr>
                            <th class="white-space-nowrap pe-10"><?php echo $this->lang->line('medicine_category'); ?></th> 
                            <th class="white-space-nowrap pe-10"><?php echo $this->lang->line('medicine_name'); ?></th>
                            <th class="white-space-nowrap pe-10"><?php echo $this->lang->line('batch_no'); ?></th>
                            <th class="white-space-nowrap pe-10"><?php echo $this->lang->line('expiry_date'); ?></th>
                            <th class="text-right pe-10"><?php echo $this->lang->line('mrp'). ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-right pe-10"><?php echo $this->lang->line('batch_amount'). ' (' . $currency_symbol . ')'; ?></th> 
                            <th class="text-right white-space-nowrap" style="padding-left:1rem;"><?php echo $this->lang->line('sale_price'). ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-right pe-10"><?php echo $this->lang->line('packing_qty'); ?></th>
                            <th class="text-right pe-10"><?php echo $this->lang->line('quantity'); ?></th>
                            <th class="text-right pe-10"><?php echo $this->lang->line('tax'); ?> (%)</th>
                            <th class="text-right pe-10"><?php echo $this->lang->line('purchase_price') . ' (' . $currency_symbol . ')'; ?></th>
                            <th class="text-right pe-10 rtl-text-left"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                        </tr>
                        <?php
                        $j = 0;
                        foreach ($detail as $bill) {
                            ?>
                            <tr class="white-space-nowrap">
                                <td class="vertical-align-middle"><?php echo $bill["medicine_category"]; ?></td>
                                <td class="vertical-align-middle"><?php echo $bill["medicine_name"]; ?></td>
                                <td class="vertical-align-middle"><?php echo $bill["batch_no"]; ?></td>
                                <td class="vertical-align-middle"><?php echo $this->customlib->getMedicine_expire_month($bill['expiry']); ?></td>
                                <td class="text-right vertical-align-middle"><?php echo $bill['mrp']; ?></td>
                                <td class="text-right vertical-align-middle"><?php echo $bill['batch_amount']; ?></td>
                                <td class="text-right white-space-nowrap pb5" style="width:80px;">
                                <input type="text" name="salerate[]" id="salerate" class="form-control" style="margin-left:1rem;width:80px;"
                                 value="<?php echo number_format($bill['sale_rate'],2); ?>">
                                <input type="hidden" name="id[]" id="id" value="<?php echo $bill["id"]; ?>">
                                </td>
                                <td class="text-right vertical-align-middle"><?php echo $bill['packing_qty']; ?></td>
                                <td class="text-right vertical-align-middle"><?php echo $bill["quantity"]; ?></td>
                                <td class="text-right vertical-align-middle"><?php echo $bill["tax"]; ?></td>
                                <td class="text-right vertical-align-middle"><?php echo number_format($bill['purchase_price'],2); ?></td>
                                <td class="text-right vertical-align-middle"><?php echo number_format($bill["amount"], 2); ?></td>
                            </tr>
                            <?php
                            $j++;
                        }
                        ?>
                    </table> 
                </div>
                <div class="divider mb-10 mt-10"></div>
                    <table id="testreport" width="100%">
                    <tr>
                        <td>                            
                            <?php if (!empty($result["note"])) { ?>
                            <p><label><?php echo $this->lang->line('note') ?></label> : <?php echo  $result["note"]; ?></p>
                        <?php } ?>
                        <p>
                            <label><?php echo $this->lang->line('payment_mode');?> </label> : 
                            <?php echo $this->lang->line(strtolower($result["payment_mode"]));  ?>
                        </p>
                        <?php 
                            if($result['payment_mode'] == "Cheque"){ ?>
                            <p><label><?php echo $this->lang->line('cheque_no');?> </label> : <?php echo $result["cheque_no"]; ?> <?php if($print == 'no'){ ?><span><a href="<?php echo site_url('admin/pharmacy/downloadcheque/'.$result["id"]); ?>" class='btn btn-default btn-xs' data-toggle='tooltip' title='<?php echo $this->lang->line("download"); ?>'><i class="fa fa-download"></i></a></span><?php } ?></p>
                            <p><label><?php echo $this->lang->line('date');?> </label> : <?php echo $this->customlib->YYYYMMDDTodateFormat($result["cheque_date"]); ?></p>
                        <?php } ?>
                        <?php  
                        if($result["payment_note"] != ""){ ?>
                        <p><label> <?php echo $this->lang->line('payment_note');?> </label>: <?php echo $result["payment_note"] ?></p>
                        <?php } ?>   
                          <?php   
                         if (!empty($result["attachment"])) { ?>    
                            <p><label> <?php echo $this->lang->line('attach_document');?></label> : <span><a class="defaults-c text-right" title="" href="<?php echo base_url().$result["attachment"]; ?>" data-original-title="<?php echo $this->lang->line('download'); ?>" download><i class="fa fa-download"></i></a></span>  </p>           
                         <?php } ?>                      
                        </td>
                        <td width="32%">
                        <table class="" width="100%" style="float:right;"> 
                        <?php if (!empty($result["total"])) { ?>
                            <tr>
                                <th width="35%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                <td class="text-right rtl-text-left" width="65%"><?php echo number_format($result["total"],2) ; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if (!empty($result["discount"])) { ?>
                            <tr>
                                <th><?php  echo $this->lang->line('discount') . " (" .  ($result["discount"]*100)/$result["total"] . "%)";   ?></th>
                                <td class="text-right rtl-text-left"><?php echo number_format($result["discount"],2) ; ?></td>
							</tr>
                        <?php } ?>
                        <?php if (!empty($result["tax"])) { ?>
                            <tr>
                                <th><?php  echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                <td class="text-right rtl-text-left"><?php echo number_format($result['tax'],2); ?></td>
                            </tr>
                        <?php } ?>
                        <?php
                        if ((!empty($result["discount"])) || (!empty($result["tax"]))) {
                            if (!empty($result["net_amount"])) {
                                ?>
                                <tr>
                                    <th><?php
                                        echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")";
                                        ;
                                        ?></th>
                                    <td class="text-right rtl-text-left"><?php echo number_format($result["net_amount"],2); ?></td>
                                </tr>
                                <?php
                            }
                        }   ?>                  
                       
                        </table>                    
                        </td>
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