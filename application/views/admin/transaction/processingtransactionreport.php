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
                            <h3 class="box-title"><?php echo $this->lang->line('processing_transaction_report') ?></h3>
                            <div class="box-tools pull-right"></div>
                        </div>
                    <div class="box-body pb0">
                        <div class="row">
                             
                        </div>
                    </div>

               
    <div class="box-body">    
        <div class="table-responsive">  
            <div class="download_label"> <?php echo $this->lang->line('processing_transaction_report') ?></div>
            <table class="table table-striped table-bordered table-hover transaction-list" data-export-title="<?php echo $this->lang->line('processing_transaction_report') ?>">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('patient_name');?></th>
                        <th><?php echo $this->lang->line('date');?></th>
                        <th><?php echo $this->lang->line('case_reference_no');?></th>
                        <th><?php echo $this->lang->line('opd_no');?></th>
                        <th><?php echo $this->lang->line('ipd_no');?></th>
                        <th><?php echo $this->lang->line('pharmacy_bill_no');?></th>
                        <th><?php echo $this->lang->line('pathology_bill_no');?></th>
                        <th><?php echo $this->lang->line('radiology_bill_no');?></th>
                        <th><?php echo $this->lang->line('blood_donor_cycle_no');?></th>
                        <th><?php echo $this->lang->line('blood_issue_no');?></th>
                        <th><?php echo $this->lang->line('ambulance_call_no');?></th>
                        <th><?php echo $this->lang->line('appointment_no');?></th>
                        <th><?php echo $this->lang->line('amount');?>(<?php echo $currency_symbol; ?>)</th>
                        <th><?php echo $this->lang->line('payment_mode');?></th>
                        <th><?php echo $this->lang->line('note');?></th>
                        
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>

            </table>                
        </div>
    </div>                       
                   
                </div>
            </div>
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div>
<div class="modal fade" id="collectionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content MX-2">
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
$(document).ready(function() {
    initDatatable('transaction-list','admin/transaction/processingtransaction');

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