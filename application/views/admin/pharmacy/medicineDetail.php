<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="nav-tabs-custom border0" id="tabs">
    <ul class="nav nav-tabs navlistscroll">
         <?php if ($this->rbac->hasPrivilege('medicine', 'can_view')) { ?>
            <li class="active">
                <a href="#current_stock" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('stock'); ?></a>
            </li>
        <?php  } if ($this->rbac->hasPrivilege('medicine_bad_stock', 'can_view')) { ?>
            <li class="">
                <a href="#bad_stock" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('bad_stock'); ?></a>
            </li>
        <?php } ?>
    </ul>    
    <div class="tab-content">
        <?php if ($this->rbac->hasPrivilege('medicine', 'can_view')) { ?>
            <div class="tab-pane active" id="current_stock">   
                <div class="row">
                    <div class="table-responsive pup-scroll-area">
                        <table class="table table-striped table-bordered table-hover example" id="detail" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('inward_date'); ?></th>
                                    <th><?php echo $this->lang->line('batch_no'); ?></th>
                                    <th><?php echo $this->lang->line('purchase_no'); ?></th>
                                    <th><?php echo $this->lang->line('expiry_date'); ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('packing_qty') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('purchase_rate') . " (" . $currency_symbol . ")"; ?></th> 
                                    <th class="text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('quantity'); ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('mrp') . ' (' . $currency_symbol . ')'; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('sale_price') . ' (' . $currency_symbol . ')'; ?></th>
                                    <th class="noExport text-right"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                foreach ($result as $detail) {
                                    ?>
                                    <tr>
                                        <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($detail->inward_date)); ?></td>
                                        <td><?php echo $detail->batch_no ?></td>
                                        <td><?php echo $this->customlib->getSessionPrefixByType('purchase_no') . $detail->purchase_no ?></td>
                                        <td><?php echo $this->customlib->getMedicine_expire_month($detail->expiry); ?></td> 
                                        <td class="text-right"><?php echo $detail->packing_qty ?></td>    
                                        <td class="text-right"><?php echo $detail->purchase_price ?></td>  
                                        <td class="text-right"><?php echo $detail->amount; ?></td>        
                                        <td class="text-right"><?php echo $detail->quantity ?></td>       
                                        <td class="text-right"><?php echo $detail->mrp; ?> </td>
                                        <td class="text-right"><?php echo $detail->sale_rate; ?></td>
                                        <td class="text-right"><?php if ($this->rbac->hasPrivilege('medicine', 'can_delete')) { ?><a href="#" class="btn btn-default btn-xs" data-toggle="tootip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_batch('<?php echo $detail->id ?>', '<?php echo $detail->pharmacy_id ?>')"><i class="fa fa-trash"></i></a><?php } ?></td>
                                    </tr>
                                    <?php
                                    $count++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>  
                </div>       
            </div>
        <?php } if ($this->rbac->hasPrivilege('medicine_bad_stock', 'can_view')) { ?>
            <div class="tab-pane" id="bad_stock">   
                <div class="row">
                    <div class="table-responsive pup-scroll-area">
                        <table class="table table-striped table-bordered table-hover example" id="bad_stock_detail" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('outward_date'); ?></th>
                                <th><?php echo $this->lang->line('batch_no'); ?></th>
                                <th><?php echo $this->lang->line('expiry_date'); ?></th>
                                <th class="text-right"><?php echo $this->lang->line('quantity'); ?></th>
                                <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            foreach ($badstockresult as $stockdetail) {                                
                                ?>
                                <tr>
                                    <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($stockdetail->outward_date)); ?></td>
                                    <td><?php echo $stockdetail->batch_no ?></td>
                                    <td><?php echo $this->customlib->getMedicine_expire_month($stockdetail->expiry_date); ?></td> 
                                    <td class="text-right"><?php echo $stockdetail->quantity ?></td>
                                    <td class="text-right"><?php if ($this->rbac->hasPrivilege('medicine_bad_stock', 'can_delete')) { ?> <a href="#" class="btn btn-default btn-xs" data-toggle="tootip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_badstock('<?php echo $stockdetail->id ?>', '<?php echo $stockdetail->pharmacy_id ?>', '<?php echo $stockdetail->medicine_batch_details_id ?>')"><i class="fa fa-trash"></i></a><?php } ?></td>
                                </tr>
                                <?php
                            } 
                            ?>
                        </tbody>
                    </table>
                  </div>  
              </div>    
            </div>
        <?php } ?>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#detail").DataTable({
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

        $(document).ready(function () {
            $("#bad_stock_detail").DataTable({
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

        function delete_batch(id, pharmacy_id) {          
            if (confirm('<?php echo $this->lang->line('are_you_sure'); ?>')) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/delete_medicine_batch/' + id,
                    type: "POST",
                    data: {opdid: ''},
                    dataType: 'json',
                    success: function (data) {
                        if (data.status == 'success') {
                            viewDetail(pharmacy_id, id);
                        } else {

                        }
                    }
                })
            }
        }

        function delete_badstock(id, pharmacy_id, medicine_batch_details_id) {
            if (confirm('<?php echo $this->lang->line('are_you_sure'); ?>')) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/deleteBadStock/' + id + '/' + medicine_batch_details_id,
                    type: "POST",
                    data: {opdid: ''},
                    dataType: 'json',
                    success: function (data) {
                        if (data.status == 'success') {
                            viewDetail(pharmacy_id, id);
                            $('.ajaxlist').DataTable().ajax.reload();
                        } else {

                        }
                    }
                })
            }
        }
    </script>