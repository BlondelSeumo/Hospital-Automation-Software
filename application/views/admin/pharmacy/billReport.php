<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                <?php $this->load->view('admin/report/_pharmacy');?>
                 <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">                        
                        <h3 class="box-title"><?php echo $this->lang->line('pharmacy_bill_report'); ?></h3>
                    </div>
                    <form id="form1" action="" method="post" class="">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search_type'); ?></label><small class="req"> *</small>
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
                                    <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('collected_by'); ?></label>
                                    <select class="form-control select2"  name="collect_staff" style="width: 100%">
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
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('doctor_name'); ?></label>
                                    <select class="form-control"  name="doctor_name" style="width: 100%">
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($pharmacydoctor as $dkey => $value) { if(!empty($value["doctor_name"])){ ?>
                                            <option value="<?php echo $value["doctor_name"] ?>" ><?php echo $value["doctor_name"]; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="text-danger" id="error_collect_staff"><?php echo form_error('doctor_name'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('gender'); ?></label>
                                    <select class="form-control" name="gender" style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                          <?php foreach($gender as $key => $value ){ ?>
                                         <option value="<?php echo $key; ?>"><?php echo $value ; ?></option>
                                     <?php } ?>
                                    </select>
                                    <span class="text-danger" id="error_collect_staff"><?php echo form_error('doctor'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
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
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('payment_mode'); ?></label>
                                    <select class="form-control" name="payment_mode" id="payment_mode" style="width: 100%">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                          <?php foreach($payment_mode as $key => $value ){ ?>
                                         <option value="<?php echo $key; ?>"><?php echo $value ; ?></option>
                                     <?php } ?>
                                    </select>
                                    <span class="text-danger" id="test_payment_mode" style="display:none"><?php echo $this->lang->line('payment_mode_note') ?></span>
                                    <span class="text-danger" id="error_collect_staff"><?php echo form_error('doctor'); ?></span>
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
                            <div class="download_label"><?php echo $this->lang->line('pharmacy_bill_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('pharmacy_bill_report'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('bill_no'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <th><?php echo $this->lang->line('age'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('prescription_no'); ?></th>             
                                        <th><?php echo $this->lang->line('doctor_name'); ?></th>
                                        <th><?php echo $this->lang->line('collected_by') ; ?></th>
                                        <?php 
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                                <th><?php echo $fields_value->name; ?></th>
                                                <?php
                                            } 
                                         }
                                        ?> 
                                        <th class="text-right"><?php echo $this->lang->line('net_amount') . ' (' . $currency_symbol . ')'; ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('paid_amount') . ' (' . $currency_symbol . ')'; ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('refund_amount') . ' (' . $currency_symbol . ')'; ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('balance_amount') . ' (' . $currency_symbol . ')'; ?></th> 
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

<div class="modal fade" id="viewModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deletebill'>
                        <a href="#"  data-target="#edit_prescription" data-placement="bottom" data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('bill_details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0 min-h-3">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
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
            url: '<?php echo base_url(); ?>admin/pharmacy/checkvalidation',
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
                    initDatatable('allajaxlist', 'admin/pharmacy/pharmacyreports/',data.param,[],100,
                        [
                            {  "sWidth": "100px", "aTargets": [ 2 ] ,'sClass': 'dt-body-left'},
                            {  "sWidth": "120px", "aTargets": [ 3 ] ,'sClass': 'dt-body-left'},
                            {  "sWidth": "50px", "aTargets": [ 4 ] ,'sClass': 'dt-body-left'},
                            {  "sWidth": "100px", "aTargets": [ 5 ] ,'sClass': 'dt-body-left'},
                            {  "sWidth": "100px", "aTargets": [ 6 ] ,'sClass': 'dt-body-left'},
                            {  "sWidth": "100px", "aTargets": [ 7 ] ,'sClass': 'dt-body-left'},
                            {  "sWidth": "60px", "aTargets": [ -4 ] ,'sClass': 'dt-body-right'},
                            {  "sWidth": "60px", "aTargets": [ -3 ] ,'sClass': 'dt-body-right'},
                            {  "sWidth": "60px", "aTargets": [ -2 ] ,'sClass': 'dt-body-right'},
                             {  "sWidth": "60px","bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-right'},                         
                          
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
<script type="text/javascript">
    $("#payment_mode").change(function(){
        if( $("#payment_mode").val()!=""){
            $("#test_payment_mode").css('display','block');
        }else{
            $("#test_payment_mode").css('display','none');
        }        
    });
    
    function viewDetail(id) {
        var view_modal=$('#viewModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/',
            type: "GET",
            data: {'id': id},
            dataType:"JSON",
            beforeSend: function(){
                $('#reportdata,#edit_deletebill').html("");
           $('#viewModal').modal('show');
           view_modal.addClass('modal_loading');
           },
           complete: function(){
             view_modal.removeClass('modal_loading');
           },
            success: function (data) {
                $('#reportdata').html(data.page);               
                view_modal.removeClass('modal_loading');
            },
        });
    }
</script>