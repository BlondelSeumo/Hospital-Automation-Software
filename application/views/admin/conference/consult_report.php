<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                <?php $this->load->view('admin/report/_live_consultation');?>
                <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"></i><?php echo $this->lang->line('live_consultation_report'); ?></h3>
                    </div>
                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) {?>
                            <?php echo $this->session->flashdata('msg');
                                $this->session->unset_userdata('msg');
                            ?>
                        <?php }?>
                        <form id="form1" action="" method="post" class="">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group"> 
                                     <label><?php echo $this->lang->line('search_type'); ?></label>
                                     <select class="form-control" name="search_type" onchange="showdate(this.value)"> 
                                        <option value=""><?php echo $this->lang->line('select'); ?></option> 
                                        <?php foreach ($searchlist as $key => $search) {
                                            ?>
                                            <option value="<?php echo $key ?>" <?php
                                            if ((isset($search_type)) && ($search_type == $key)) {
                                                echo "selected";
                                            }
                                            ?>><?php echo $search ?></option>
                                                <?php } ?>
                                    </select>
                                 </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group"> 
                                     <label><?php echo $this->lang->line('created_by'); ?></label>
                                     <select name="created_by" id="created_by" class="form-control select2">
                                         <option value=""><?php echo $this->lang->line('select') ?></option>
                                         <?php foreach($stafflist as $value){ ?>
                                                <option value="<?php echo $value['id']; ?>"><?php echo $value["name"] . " " . $value["surname"] ." (". $value["employee_id"].")" ?></option>
                                         <?php } ?>
                                     </select>
                                 </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('opd_ipd'); ?></label>
                                    <div>
                                        <select name='select_module' class="form-control module_type"  style="width:100%">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach($opd_ipd as $key => $opd_ipd_value){ ?>
                                                <option value="<?php echo $key; ?>"><?php echo $opd_ipd_value; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4" id="fromdate" style="display: none">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('date_from'); ?></label>
                                    <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                </div>
                            </div> 

                            <div class="col-sm-4" id="todate" style="display: none">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('date_to'); ?></label>
                                    <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                </div>
                            </div>
                           <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>

                        </div>
                        </form>
                    <div class="table-responsive">
                    <div class="download_label"><?php echo $this->lang->line("live_consultation_report"); ?></div>
                         <table class="table table-hover table-striped table-bordered allajaxlist" data-export-title="<?php echo $this->lang->line('live_consultation_report'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('module'); ?></th>
                                        <th><?php echo $this->lang->line('consultation_title'); ?></th>
                                        <th><?php echo $this->lang->line('patient'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('api_used'); ?></th>
                                        <th><?php echo $this->lang->line('created_by'); ?> </th>
                                        <th><?php echo $this->lang->line('total_join'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="viewerModal" class="modal fade modalmark" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('join_list'); ?></h4>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
     $(function () {
        
        $('.select2').select2()
    });

</script>
<script type="text/javascript">
    $(document).on('click', '.viewer-list', function () {
        var $this = $(this);
        var recordid = $this.data('recordid');
        $.ajax({
            type: 'POST',
            url: baseurl + "admin/zoom_conference/getViewerList",
            data: {'recordid': recordid,'type': 'patient'},
            dataType: 'JSON',
            beforeSend: function () {
                $this.button('loading');
            },
            success: function (data) {
                $('#viewerModal .modal-body').html(data.page);
                //===============
            $(".viewer-list-datatable").DataTable({

            dom: "Bfrtip",
            buttons: [

                {
                    extend: 'copyHtml5',
                    text: '<i class="fa fa-files-o"></i>',
                    titleAttr: 'Copy',
                    title: $('.downloadlabel').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',

                    title: $('.downloadlabel').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-text-o"></i>',
                    titleAttr: 'CSV',
                    title: $('.downloadlabel').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    title: $('.downloadlabel').html(),
                    exportOptions: {
                        columns: ':visible'

                    }
                },

                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    title: $('.downloadlabel').html(),
                    customize: function (win) {
                        $(win.document.body)
                                .css('font-size', '10pt');

                        $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                    },
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.downloadlabel').html(),
                    postfixButtons: ['colvisRestore']
                },
            ]
        });
                //==================
                $('#viewerModal').modal('show');
                $this.button('reset');
            },
            error: function (xhr) { // if error occured
                alert('<?php echo $this->lang->line('error_occurred_please_try_again'); ?>');
                $this.button('reset'); 
            },
            complete: function () {
                $this.button('reset');
            } 
        });
    });
</script>

<script>
   
( function ( $ ) {
    'use strict';

    $(document).ready(function () {
         emptyDatatable('allajaxlist', 'data');
       $('#form1').on('submit', (function (e) {
        e.preventDefault();    
      
        var formData = new FormData(this);
       
          $.ajax({
            url: '<?php echo base_url(); ?>admin/zoom_conference/checkvalidation',
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                console.log(data.param)
                if (data.status == "fail") {
                   $.each(data.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {                  
               
                initDatatable('allajaxlist','admin/zoom_conference/liveconsultationreport',data.param,[],100,[{"bSortable": false, "aTargets": [0,7] }]);
                }
            }
        });
      
        }
       ));
   });

} ( jQuery ) );

function showdate(value) {
    if (value == 'period') {
        $('#fromdate').show();
        $('#todate').show();
    } else {
        $('#fromdate').hide();
        $('#todate').hide();
    }
}
</script>
<!-- //========datatable end===== -->      