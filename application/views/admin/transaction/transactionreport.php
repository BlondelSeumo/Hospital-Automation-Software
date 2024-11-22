<style type="text/css">
    @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
</style>
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary border0 mb0">
                     <?php $this->load->view('admin/report/_finance');?>  
                     <div class="box-header ptbnull"></div>
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('daily_transaction_report') ?></h3>
                            <div class="box-tools pull-right"></div>
                        </div>
                    <div class="box-body pb0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <form id="transaction_form" action="" method="post">
                                        <div class="box-body row">
                                            <?php echo $this->customlib->getCSRF(); ?>
                                        <div class="col-sm-6 col-md-3" >
                                            <div class="form-group">
                                            <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                              <input id="date_from" name="date_from" placeholder="" type="text" class="form-control start_date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                                <span class="text-danger" id="error_date_from"><?php echo form_error('date_from'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                            <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                              <input id="date_to" name="date_to" placeholder="" type="text" class="form-control end_date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                                <span class="text-danger" id="error_date_to"><?php echo form_error('date_to'); ?></span>
                                            </div>
                                        </div>            
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>  
                        </div>
                    </div>
<?php 
if(isset($result)){
    ?>
  <div class="tabsborderbg"></div>                 
    <div class="box-body">    
        <div class="table-responsive">  
            <div class="download_label"><?php echo $this->lang->line('daily_transaction_report') ?></div>
            <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('date'); ?></th>
                        <th><?php echo $this->lang->line('total_transaction'); ?></th>
                        <th class="text-right"><?php echo $this->lang->line('online').'('.$currency_symbol.')'; ?></th>
                        <th class="text-right"><?php echo $this->lang->line('offline').'('.$currency_symbol.')'; ?></th>
                        <th class="text text-right"><?php echo $this->lang->line('amount').'('.$currency_symbol.')'; ?></th>
                        <th class="text text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                foreach ($result as $dt_key => $dt_value) {
                    ?>
                <tr>
                    <td><?php echo $this->customlib->YYYYmmddTodateformat($dt_value['date']) ?></td>
                    <td><?php echo $dt_value['total_transaction'] ?></td>
                    <td class="text text-right"><?php echo amountFormat($dt_value['online_transaction']); ?></td>
                    <td class="text text-right"><?php echo amountFormat($dt_value['offline_transaction']); ?></td>
                    <td class="text text-right"><?php echo amountFormat($dt_value['amount']); ?></td>
                    <td class="text text-right">
                        <button type="button" class="btn btn-default btn-xs daily_collection" id="load" data-toggle="tooltip" data-date="<?php echo $dt_value['date'];?>" data-deposite-id="" title="" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" data-original-title="<?php echo $this->lang->line('view_collection'); ?>" autocomplete="off"><i class="fa fa-list"></i></button>
                    </td>
                </tr>
                    <?php
                }
                     ?>
                </tbody>

            </table>                
        </div>
    </div>                       
    <?php
}
 ?>
                      
                </div>
            </div>
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div>
<div class="modal fade" id="collectionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl-lg-80 w-sm-100">
      <div class="modal-content mx-2">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?php echo $this->lang->line('collection_list'); ?></h4>
        </div>
        <div class="scroll-area">
            <div class="modal-body">
              ...
            </div>
        </div>        
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

<script type="text/javascript">
      var date_format_new = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
    $(document).ready(function(){

    $(".start_date").datepicker({
              format: date_format_new,
              setDate: new Date(),
              autoclose: true,
              todayHighlight: true
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('.end_date').datepicker('setStartDate', minDate);
    });

    $(".end_date").datepicker({
              format: date_format_new,
              setDate: new Date(),
              autoclose: true,
              todayHighlight: true
    }).on('changeDate', function (selected) {
            var maxDate = new Date(selected.date.valueOf());
            $('.start_date').datepicker('setEndDate', maxDate);
        });

});

    $(document).on('click','.daily_collection',function(e){
         var $btn = $(this);  
        e.preventDefault();
       var form = $(this);
        $.ajax({
            url: baseurl+'admin/transaction/gettransactionbydate',
            type: "POST",
            data: {'date': $(this).data('date')},
            dataType: 'json',
            beforeSend: function() {
          $btn.button('loading');
            },
            success: function (data) {
                  $btn.button('reset');
           $('#collectionModal .modal-body').html(data.page);
           $('#collectionModal .example').DataTable({ 
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
                 customize: function ( win ) {

                    $(win.document.body).find('th').addClass('display').css('text-align', 'left');
                    $(win.document.body).find('td').addClass('display').css('text-align', 'left');
                    $(win.document.body).find('table').addClass('display').css('font-size', '14px');
                    $(win.document.body).find('h1').css('text-align', 'center');
                },
                     exportOptions: {
                    columns: ["thead th:not(.noExport)"]
                  }
                }
            ]
        });

           $('#collectionModal').modal('show');
            },
    error: function(xhr) { // if error occured
        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
      $btn.button('reset');
    },
    complete: function() {
    $btn.button('reset');
    }
        });
    });
</script>