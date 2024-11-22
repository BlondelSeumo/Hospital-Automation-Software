<?php 
    $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
	<div class="table-responsive">
                        <table class="table mb0 table-striped table-bordered examples">
                                    <tr>
                                        <th width="15%"><?php echo $this->lang->line('donor_name'); ?></th>
                                        <td width="35%"><?php echo $blood_donor['donor_name']; ?></td>
                                        <th width="15%"><?php echo $this->lang->line('age'); ?></th>
                                        <td width="35%"><span id="ages"><?php echo $this->customlib->getAgeBydob($blood_donor['date_of_birth']); ?></span></td>
                                    </tr>
									<tr>
										<th width="15%"><?php echo $this->lang->line('date_of_birth'); ?></th>
                                        <td width="35%"><?php echo $this->customlib->YYYYMMDDTodateFormat($blood_donor['date_of_birth']); ?></td>
										<th width="15%"><?php echo $this->lang->line('blood_group'); ?></th>
                                        <td width="35%"><?php echo $blood_donor['blood_group_name']; ?></td>
									</tr>
                                    <tr>                                        
                                        <th width="15%"><?php echo $this->lang->line('gender'); ?></th>
                                        <td width="35%"><?php echo $this->lang->line(strtolower($blood_donor['gender'])); ?></td>
										<th width="15%"><?php echo $this->lang->line('contact_no'); ?></th>
                                        <td width="35%"><?php echo $blood_donor['contact_no']; ?></td>										
                                    </tr>
                                    <tr>
                                        <th width="15%"><?php echo $this->lang->line('father_name'); ?></th>
                                        <td width="35%"><?php echo $blood_donor['father_name']; ?></td>
                                        <th width="15%"><?php echo $this->lang->line('address'); ?></th>
                                        <td width="35%"><?php echo $blood_donor['address']; ?></td>
                                     </tr>                                     

                                      <?php
                                        if (!empty($fields)) {
                                       foreach ($fields as $fields_key => $fields_value) {
                                        $display_field = $blood_donor[$fields_value->name];
                                    ?>
                                    <tr>
                                        <th width="15%"><?php echo $fields_value->name; ?></th> 
                                        <td colspan="3"><?php echo $display_field; ?></td>
                                         
                                    </tr>
                                <?php }
                            }?>
                        </table>
    </div>
                      
<div class="download_label"><?php echo $this->lang->line('donor_details'); ?></div>
<?php  if($this->rbac->hasPrivilege('blood_stock', 'can_view')) { ?>
<div class="table-responsive">
    <div class="pup-scroll-area">
        <table class="table table-striped table-bordered table-hover example" id="testreport">
            <thead>
                <tr class="table-heading-sticky">
                    <th><?php echo $this->lang->line('bags'); ?></th>
                    <th><?php echo $this->lang->line('institution'); ?></th>
                    <th><?php echo $this->lang->line('lot'); ?></th>
                    <th><?php echo $this->lang->line('donate_date'); ?></th>
                    <th><?php echo $this->lang->line('charge_category'); ?></th>
                    <th><?php echo $this->lang->line('charge_name'); ?></th>
                    <th class="text-right"><?php echo $this->lang->line('standard_charge'); ?> (<?php echo $currency_symbol; ?>)</th>
                    <th class="text-right"><?php echo $this->lang->line('apply_charge'); ?>  (<?php echo $currency_symbol; ?>)</th>
                    <th class="text-right"><?php echo $this->lang->line('discount').' (%)'; ?> </th>
                    <th class="text-right"><?php echo $this->lang->line('tax').' (%)'; ?> </th>
                    <th class="text-right"><?php echo $this->lang->line('net_amount'); ?>  (<?php echo $currency_symbol; ?>)</th>
                    <th><?php echo $this->lang->line('payment_date'); ?></th>
                    <th><?php echo $this->lang->line('note'); ?></th>
                    <th><?php echo $this->lang->line('payment_mode'); ?></th>
                    <th><?php echo $this->lang->line('paid_amount').' ('.$currency_symbol.')'; ?></th>
                    <th class="noExport"><?php echo $this->lang->line('action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
              
    $count = 1;
    foreach ($result as $detail) {
        ?> 
                    <tr>
                        <td><?php echo $this->customlib->bag_string($detail->bag_no,$detail->volume,$detail->unit_name); ?></td>
                        <td><?php echo $detail->institution; ?></td>
                        <td><?php echo $detail->lot; ?></td>
                        <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($detail->donate_date)) ?></td>
                        <td><?php echo $detail->charge_category_name; ?></td>
                        <td><?php echo $detail->charge_name; ?></td>
                        <td class="text-right"><?php echo $detail->standard_charge; ?></td>
                        <td class="text-right"><?php echo $detail->apply_charge; ?></td>
                        <td class="text-right">                            
                            <?php 
    $discount_amt=calculatePercent( $detail->apply_charge,$detail->discount_percentage);
                            echo calculatePercent( $detail->apply_charge,$detail->discount_percentage)." (".$detail->discount_percentage."%) "; ?>
                        </td>
                        <td class="text-right">                             
                            <?php echo calculatePercent(($detail->apply_charge-$discount_amt),$detail->tax_percentage)." (".$detail->tax_percentage."%) "; ?>
                        </td>
                        <td class="text-right"><?php $netamount =    $detail->standard_charge + calculatePercent(($detail->apply_charge-$discount_amt),$detail->tax_percentage) ; echo amountFormat($netamount); ?></td>
                        <td><?php echo $this->customlib->YYYYMMDDTodateFormat($detail->payment_date); ?></td>
                      <td><?php echo $detail->note ?></td>
                        <td><?php echo $this->lang->line(strtolower($detail->payment_mode))."<br>";
                                     if($detail->payment_mode == "Cheque"){
                                     if($detail->cheque_no!=''){
                                           echo $this->lang->line('cheque_no') . ": ".$detail->cheque_no;                                          
                                        echo "<br>";
                                    }
                                        if($detail->cheque_date!='' && $detail->cheque_date!='0000-00-00'){
                                           echo $this->lang->line('cheque_date') .": ".$this->customlib->YYYYMMDDTodateFormat($detail->cheque_date);
                                       } 
                                         }
                                                            ?> 
                                                            </td>
                                                            <td class="text-right"><?php echo $detail->amount ?></td>                                                          
                                                            <td class="text-right">
                <?php         if ($detail->payment_mode == "Cheque" && $detail->attachment != "")  {
        ?>
        <a href='<?php echo site_url('admin/transaction/download/'.$detail->tran_id);?>' class='btn btn-default btn-xs'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
        <?php
    }
             ?>
                        <a href="#" class="btn btn-default btn-xs print_donor_tran" data-toggle="tooltip" title="<?php echo $this->lang->line('print'); ?>"  data-recordId="<?php echo $detail->blood_donor_id; ?>" data-transation_id="<?php echo $detail->tran_id; ?>"><i class="fa fa-print"></i></a>
                        <?php  if($this->rbac->hasPrivilege('blood_stock', 'can_delete')) { ?>
                       <a href="#" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"  onclick="deleteRecord('<?php echo $detail->id ?>')"><i class="fa fa-trash"></i></a>
                   <?php } ?>
                   </td>
                    </tr>
                    <?php
    $count++;
    } 
    ?>
            </tbody>
        </table>
    </div>    
</div>
<?php }?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#testreport").DataTable({
            dom: "Bfrtip",
            buttons: [
                {
                    extend: 'copyHtml5',
                    text: '<i class="fa fa-files-o"></i>',
                    titleAttr: 'Copy',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ["thead th:not(.noExport)"]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ["thead th:not(.noExport)"]
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-text-o"></i>',
                    titleAttr: 'CSV',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ["thead th:not(.noExport)"]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ["thead th:not(.noExport)"]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    title: $('.download_label').html(),
                    customize: function (win) {
                        $(win.document.body)
                                .css('font-size', '10pt');

                        $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                    },
                    exportOptions: {
                        columns: ["thead th:not(.noExport)"]
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                },
            ]
        });
    });

    function deleteRecord(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/deleteDonorCycle/' + id,
                type: "POST",
                data: {id: id},
                dataType: 'json',
                success: function (data) {
                    successMsg('<?php echo $this->lang->line('delete_message') ?>');
                    viewDetail('<?php echo $id ?>');
                }
            })
        }
    }
</script>