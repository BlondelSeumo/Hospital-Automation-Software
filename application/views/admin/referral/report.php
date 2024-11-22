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
                         
                        <h3 class="box-title"><?php echo $this->lang->line('referral_report'); ?></h3>
                    </div> 
                    <form id="form1" action="" method="post">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            
                            <div class="col-sm-3 col-md-3" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('date'); ?></label>
                                        <input id="date" name="date" placeholder="" type="text" class="form-control date" value="" />
                                    <span class="text-danger" id="error_date_from"><?php echo form_error('collect_staff'); ?></span>
                                </div>
                            </div>
                                        
                            <div class="col-sm-3 col-md-3" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('payee'); ?></label> 
                                    <select class="form-control select2" style="width: 100%" name="payee" id="payee">
                                    <option value=""><?php echo $this->lang->line('select')?></option> 
                                        <?php foreach ($person as $key => $value) { ?>
                                            <option value="<?php echo $value->person_id ?>" ><?php echo $value->name ?></option>
                                            <?php }?>
                                    </select>
                                    <span class="text-danger" id="error_payee"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div> 

                            <div class="col-sm-3 col-md-3">
                                <div class="form-group">
                                <label><?php echo $this->lang->line("patient_type"); ?></label>
                                        <select class="form-control select2"  name="patient_type" style="width: 100%" id="patient_type">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($type as $key => $value) { ?>
                                                <option value="<?php echo $value["id"] ?>"><?php echo $this->lang->line($value["name"]); ?></option>
                                            <?php }?>
                                        </select>
                                    <span class="text-danger" id="error_patient_type"><?php echo form_error('collect_staff'); ?></span>
                                </div>
                            </div> 
                                            
                          	<div class="col-sm-3 col-md-3" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line("patient"); ?></label> 
                                    <select class="form-control select2"  name="patient" style="width: 100%" id="patient">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($patients as $key => $value) { ?>
                                            <option value="<?php echo $value["id"] ?>"><?php echo $value['patient_name']."(".$value['id'].")"; ?></option>
                                        <?php }?>
                                    </select>
                                    <span class="text-danger" id="error_patient"></span>
                                </div>
                            </div>
                                        
                            <div class="form-group">
                                <div class="col-sm-12">
                                     <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div> 
                        </div>
                    </form >

                    <div class="box border0 clear">
                        <div class="box-header ptbnull"></div>
                        <div class="box-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('referral_report'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('referral_report'); ?>">
                                    <thead>
                                        <tr>
                                        <th><?php echo $this->lang->line('payee'); ?></th>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                                        <th class="text-right" width="15%"><?php echo $this->lang->line('commission_percentage'); ?> (%)</th>
                                        <th class="text-right" width="15%"><?php echo $this->lang->line('bill_amount').' ('. $currency_symbol .')'; ?></th>
                                        <th class="text-right" width="15%"><?php echo $this->lang->line('commission_amount').' ('. $currency_symbol .')'; ?></th> 
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
        </div>
</div>
</section>
</div>

<script>

( function ( $ ) {
    'use strict';

    $(document).ready(function () {
        $('.select2').select2();
         emptyDatatable('allajaxlist', 'data');
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referral/checkvalidation',
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
                    $("#error_payee").html('');
                    $("#error_patient_type").html('');
                    $("#error_patient").html('');
                    initDatatable('allajaxlist', 'admin/referral/referral_report/',data.param,[],100,[
                        {  "sWidth": "15%", "aTargets": [ -1,-2,-3 ] ,'sClass': 'dt-body-right'}
                         
                    ]);
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );
 
</script>