<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                <?php $this->load->view('admin/report/_tpa');?>
                <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">                        
                        <h3 class="box-title"><?php echo $this->lang->line('tpa_report'); ?></h3>
                    </div>
                    <form id="form1" action="" method="post">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-6 col-md-3" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search_type'); ?></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($searchlist as $key => $search) {
                                            ?>
                                            <option value="<?php echo $key ?>" <?php
                                            if ((isset($search_type)) && ($search_type == $key)) {
                                                echo "selected";
                                            }
                                            ?>><?php echo $search ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('doctor'); ?></label>
                                    <select name="constant_id" id="constant_id" class="form-control select2"  style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($doctorlist as $dkey => $value) {
                                            ?>
                                            <option value="<?php echo $value["id"] ?>" <?php
                                            if ((isset($doctor_select)) && ($doctor_select == $value["id"])) {
                                                echo "selected";
                                            }
                                            ?> ><?php echo $value["name"] . " " . $value["surname"]." (".$value["employee_id"].")"; ?></option> 
<?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                                </div>
                            </div> 
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('tpa'); ?></label>
                                    <select class="form-control select2" id="organisation"  name="organisation" style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($organisation as $orgkey => $orgvalue) {
                                            ?>
                                            <option value="<?php echo $orgvalue["id"] ?>" <?php
                                            if ((isset($tpa_select)) && ($tpa_select == $orgvalue["id"])) {
                                                echo "selected";
                                            }
                                            ?> ><?php echo $orgvalue["organisation_name"] ?></option> 
<?php } ?>
                                    </select>
                                    <span class="text-danger" id="error_organisation"><?php echo form_error('doctor'); ?></span>
                                </div>
                            </div> 
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('case_id'); ?></label>
                                    <input type="text" name="case_id" class="form-control">                                    
                                </div>
                            </div> 
                            <div class="col-sm-6 col-md-3" id="fromdate" style="display: none">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('date_from'); ?></label>
                                    <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                </div>
                            </div> 
                            <div class="col-sm-6 col-md-3" id="todate" style="display: none">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('date_to'); ?></label>
                                    <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('charge_category'); ?></label>
                                     <select name="charge_category" style="width: 100%" class="form-control charge_category select2">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($charge_category as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value['id']; ?>">
                                                <?php echo $value['name']; ?>
                                            </option>
                                        <?php } ?>
                                     </select>
                                     <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                </div>
                            </div> 
                            <div class="col-md-3">
                                 <div class="form-group">
                                    <label><?php echo $this->lang->line('charge'); ?></label>
                                    <select name="charge_id" style="width: 100%" class="form-control charge select2">
                                        <option value=""><?php echo $this->lang->line('select')?></option>
                                    </select>
                                     <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="box border0 clear">
                        <div class="box-header ptbnull"></div>
                        <div class="box-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('tpa_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('tpa_report'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line("checkup_ipd_no"); ?></th>
                                        <th><?php echo $this->lang->line("case_id"); ?></th>
                                        <th><?php echo $this->lang->line('head'); ?></th>
                                        <th><?php echo $this->lang->line("tpa_name"); ?></th>
                                        <th><?php echo $this->lang->line('patient_name');?></th>
                                        <th><?php echo $this->lang->line('appointment_date');?></th>
                                        <th><?php echo $this->lang->line('doctor');?></th>
                                        <th><?php echo $this->lang->line('charge_name');?></th>
                                        <th><?php echo $this->lang->line('charge_category');?></th>
                                        <th><?php echo $this->lang->line('charge_type');?></th>
                                        <th class="text text-right"><?php echo $this->lang->line('standard_charge');?> (<?php echo $currency_symbol; ?>)</th>
                                        <th class="text text-right"><?php echo $this->lang->line('applied_charge');?> (<?php echo $currency_symbol; ?>)</th>
                                        <th class="text text-right"><?php echo $this->lang->line('tpa_charge');?> (<?php echo $currency_symbol; ?>)</th>
                                        <th class="text text-right"><?php echo $this->lang->line('tax');?> </th>
                                        <th class="text text-right"><?php echo $this->lang->line('amount');?> (<?php echo $currency_symbol; ?>)</th>                                    
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
</section>
</div>
<script>
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2();
    });
</script>
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
    $(document).ready(function (e) {      
        emptyDatatable('allajaxlist', 'data');
    });
    
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/tpamanagement/checkvalidation',
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
                    $("#error_organisation").html('');
                initDatatable('allajaxlist', 'admin/tpamanagement/tpareports/',data.param,[],100,[
                          {  "sWidth": "100px", "bSortable": false, "aTargets": [ -1,-2,-3,-4,-5 ] ,'sClass': 'dt-body-right'},
                     
                          { "sWidth": "70px", "aTargets": [ 0 ] ,'sClass': 'dt-body-left'},
                          { "sWidth": "50px", "aTargets": [ 1,2 ] ,'sClass': 'dt-body-left'},
                          { "sWidth": "100px", "aTargets": [ 5,6 ] ,'sClass': 'dt-body-left'}
                        ]);
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );
 
</script>
<script>
     $(".charge_category" ).change(function() {
       var charge_category=$(this).val();     
      $('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
     getchargecode(charge_category,"");
 });

    function getchargecode(charge_category,charge_id) {     
      var div_data = "<option value=''>Select</option>";
      if(charge_category != ""){
          $.ajax({
            url: base_url+'admin/charges/getchargeDetails',
            type: "POST",
            data: {charge_category: charge_category},
            dataType: 'json',
            success: function (res) {              
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";

                });
                $('.charge').html(div_data);
                $(".charge").select2("val", charge_id);
             
            }
        });
      }
    }
</script>