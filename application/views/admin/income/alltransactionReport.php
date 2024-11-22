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
                <div class="box box-primary">
                <?php $this->load->view('admin/report/_finance');?>  
                <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">                        
                        <h3 class="box-title"><?php echo $this->lang->line('transaction_report') ?></h3>
                        <div class="box-tools pull-right"></div>
                    </div>
                    <div class="box-body pb0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <form id="form1" action="" method="post">
                                        <div class="box-body row">
                                            <?php echo $this->customlib->getCSRF(); ?>
                                            <div class="col-sm-6 col-md-3" >
                                                <div class="form-group"> 
                                                    <label><?php echo $this->lang->line('search_type'); ?></label><small class="req"> *</small>
                                                    <select class="form-control" name="search_type"  id="search_type_select" onchange="showdate(this.value)">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php 
                                                        foreach ($searchlist as $key => $search) { ?>
                                                            <option value="<?php echo $key ?>" <?php
                                                            if ((isset($search_type)) && ($search_type == $key)) {
                                                                    echo "selected";
                                                                }
                                                                ?>><?php echo $search ?></option>
                                                            <?php }?>
                                                    </select>
                                                    <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                                </div>
                                            </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                            <label><?php echo $this->lang->line('collected_by'); ?></label>
                                                    <select class="form-control select2"  name="collect_staff" style="width: 100%" id="collect_staff_select">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($staffsearch as $dkey => $value) { ?>
                                                                <option value="<?php echo $value["staffid"] ?>"<?php
                                            if ((isset($staffsearch_select)) && ($staffsearch_select == $value["staffid"])) {
                                                               echo "selected";
                                                                    }
                                                            ?>><?php echo $value["staffname"] . " " . $value["staffsurname"] ." (". $value["employee_id"].")" ?></option>
                                                        <?php }?>
                                                    </select>
                                                <span class="text-danger" id="error_collect_staff"><?php echo form_error('collect_staff'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3" id="fromdate" style="display: none">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                                    <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3" id="todate" style="display: none">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                                    <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                                </div>
                                            </div> 
                                        <div class="col-sm-6 col-md-3" >
                                            <div class="form-group">
                                            <label><?php echo $this->lang->line('select_head'); ?></label>
                                                    <select class="form-control select2"  name="modules_select" style="width: 100%" id="modules_select">
                                                       <?php foreach ($modules as $key => $search) { ?>
                                                            <option value="<?php echo $key ?>" <?php
                                                            if ((isset($modules_type)) && ($modules_type == $key)) {
                                                                    echo "selected";
                                                                }
                                                                ?>><?php echo $search ?></option>
                                                            <?php }?>
                                                    </select>
                                                <span class="text-danger" id="error_modules_staff"><?php echo form_error('modules_staff'); ?></span>
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
                        <div class="tabsborderbg"></div>
                        <div class="nav-tabs-custom border0">
                            <div class="tab-content">
                                <div class="tab-pane active" id="all">
                                    <div class="download_label"><?php echo $this->lang->line('transaction_report'); ?></div>
                                    <div>
                                        <table class="table table-striped table-bordered table-hover allajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('transaction_report'); ?>">
                                            <thead>
                                            <th><?php echo $this->lang->line('transaction_id'); ?></th>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th id="clmname"></th>
                                            <th><?php echo $this->lang->line('reference'); ?></th>
                                            <th><?php echo $this->lang->line('category'); ?></th>
                                            <th id="collection-generated-clm"></th>
                                            <th><?php echo $this->lang->line('payment_type'); ?></th>
                                            <th><?php echo $this->lang->line('payment_mode'); ?></th>
                                            <th class="text-right"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                            </thead>
                                            <tbody>
                                            </tbody> 
                                        </table>
                                    </div>
                                </div>
							</div>
                        </div>
                    </div>
            </div>
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div>

    <script type="text/javascript">
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

<script>
( function ( $ ) {
    'use strict';

    $(document).ready(function () {
         emptyDatatable('allajaxlist', 'data');
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/income/checkvalidation',
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == "fail") {
                   $.each(data.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {
                    $("#error_search_type").html('');
                     $("#error_collect_staff").html('');

                     if(($("#modules_select").val())=='payroll_report'){
                        $("#clmname").html('<?php echo $this->lang->line('staff_name'); ?>');
                        $("#collection-generated-clm").html('<?php echo $this->lang->line('generated_by'); ?>');
                     }else{
                        $("#clmname").html('<?php echo $this->lang->line('patient_name'); ?>');
                        $("#collection-generated-clm").html('<?php echo $this->lang->line('collected_by'); ?>');
                     }
               
                  initDatatable('allajaxlist','admin/income/transactionreports/',data.param,[],100,
                        [                         
                     
                          { "sWidth": "90px", "aTargets": [ 0 ] ,'sClass': 'dt-body-left'},
                          { "sWidth": "150px", "aTargets": [ 1] ,'sClass': 'dt-body-left'},
                          { "sWidth": "150px", "aTargets": [ -1] ,'sClass': 'dt-body-right'}
                          
                        ]);
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );

</script>

<script type="text/javascript">
    $(document).ready(function () {
          $("#clmname").html('<?php echo $this->lang->line('patient_name'); ?>');
          $("#collection-generated-clm").html('<?php echo $this->lang->line('collected_by'); ?>');
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
		
        $(".date").datepicker({
            format: date_format,
            autoclose: true,
            todayHighlight: true

        });
    });
</script>
<script type="text/javascript">

    var base_url = '<?php echo base_url() ?>';

    function printDiv(elem) {
        popup(jQuery(elem).html());
    }     
</script>