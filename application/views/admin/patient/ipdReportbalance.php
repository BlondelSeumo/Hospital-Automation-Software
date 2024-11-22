<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                 <?php $this->load->view('admin/report/_ipd');?>
                  <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('ipd_balance_report'); ?></h3>
                    </div>
                    <form id="form1" action="" method="post">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search_type'); ?></label>
                                    <small class="req"> *</small>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option> 
                                        <?php foreach ($searchlist as $key => $search) {
    ?>
                                            <option value="<?php echo $key ?>" <?php
if ((isset($search_type)) && ($search_type == $key)) {
        echo "selected";
    }
    ?>><?php echo $search ?></option>
<?php }?>
                                    </select>
                                   <span class="text-danger" id="error_search_type"></span>
                                </div>
                            </div>
                       
                             <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('patient_status'); ?></label>
                                    <select class="form-control "  name="patient_status" style="width: 100%">
                                        <option value="all" <?php if ((isset($patient_status)) && ($patient_status == 'all')) {
    echo "selected";
}
?> ><?php echo $this->lang->line('all') ?></option>
                                        <option value="no" <?php if ((isset($patient_status)) && ($patient_status == 'on_bed')) {
    echo "selected";
}
?>><?php echo $this->lang->line('on_bed'); ?></option>
                                        <option value="yes" <?php if ((isset($patient_status)) && ($patient_status == 'discharged')) {
    echo "selected";
}
?>><?php echo $this->lang->line('discharged') ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('patient_status'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('from_age'); ?></label>
                                    <select name="from_age" id="from_age" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach($agerange as $key=>$value){ ?>
                                            <option value="<?php echo $key; ?>"<?php
                                            if ((isset($from_age)) && ($from_age == $key)) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('to_age'); ?></label>
                                    <select name="to_age" id="to_age" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
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

                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('gender'); ?></label>
                                    <select class="form-control"  name="gender" style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach($gender as $key => $value ){ ?>
                                         <option value="<?php echo $key; ?>"><?php echo $value ; ?></option>
                                     <?php } ?>
                                          
                                    </select>
                                    <span class="text-danger" id="error_collect_staff"><?php echo form_error('doctor'); ?></span>
                                </div>
                            </div>
                             <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                   <label><?php echo $this->lang->line('miscellaneous'); ?></label>
                                    <select name="miscellaneous" id="miscellaneous" class="form-control">                                        
                                         <option value=""><?php echo $this->lang->line('select'); ?></option>
                                         <option value="is_antenatal"><?php echo $this->lang->line('antenatal'); ?></option>
                                  </select>
                               </div> 
                            </div>
                        </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>    
                            </div>
                        </div>
                    </form>
                    <div class="box border0 clear">
                        <div class="box-header ptbnull"></div>
                        <div class="box-body table-responsive">
                            <div class="download_label"></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('ipd_balance_report'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('ipd_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <th><?php echo $this->lang->line('age'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('antenatal'); ?></th>
                                        <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                        <th><?php echo $this->lang->line('guardian_name'); ?></th>                                  
                                        <th><?php echo $this->lang->line("discharged"); ?></th>
                                        <th><?php echo $this->lang->line("patient_active"); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('paid_amount') . '(' . $currency_symbol . ')'; ?></th>
                                       <th class="text-right"><?php echo $this->lang->line('balance_amount') . '(' . $currency_symbol . ')'; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<script type="text/javascript">
     $(document).ready(function (e) {
       emptyDatatable('allajaxlist', 'data');
       
    });
    
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
<script type="text/javascript">
    $("#from_age").change(function(){
            var dosage_opt="<option value=''><?php echo $this->lang->line('select') ?></option>";
        var from_age =$("#from_age").val();
        var sss='<?php echo json_encode($agerange); ?>';
        var aaa=JSON.parse(sss);
        
    $.each(aaa, function(key, item) 
         {      
        if(parseInt(from_age) < key){
         dosage_opt+="<option value='"+key+"'>"+item+"</option>";
        }
       });

        $("#to_age").html(dosage_opt);
     
    });
</script>
<script>
( function ( $ ) {
    'use strict';

    $(document).ready(function () {
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url: base_url+'admin/patient/checkvalidationipdbalance',
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                console.log(data.param);
                if (data.status == "fail") {
                   $.each(data.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {
                    $("#error_search_type").html('');
                    $("#error_collect_staff").html('');
                initDatatable('allajaxlist', 'admin/patient/ipdbalancereports/',data.param,[],100,[
                        {  "sWidth": "60px", "aTargets": [ -1,-2,-3 ] ,'sClass': 'dt-body-right'},
                        {  "sWidth": "60px", "aTargets": [ -4,-5 ] ,'sClass': 'dt-body-left'},
                        {  "sWidth": "140px", "aTargets": [ 3 ] ,'sClass': 'dt-body-left'},
                        {  "sWidth": "30px", "aTargets": [ 4 ] ,'sClass': 'dt-body-left'},
                        {"bSortable" : false, "aTargets":[-1]},
                    ]);
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );
</script>