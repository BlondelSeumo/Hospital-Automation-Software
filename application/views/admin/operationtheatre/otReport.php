<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<style type="text/css">
    /*REQUIRED*/
    .carousel-row {
        margin-bottom: 10px;
    }
    .slide-row {
        padding: 0;
        background-color: #ffffff;
        min-height: 150px;
        border: 1px solid #e7e7e7;
        overflow: hidden;
        height: auto;
        position: relative;
    }
    .slide-carousel {
        width: 20%;
        float: left;
        display: inline-block;
    }
    .slide-carousel .carousel-indicators {
        margin-bottom: 0;
        bottom: 0;
        background: rgba(0, 0, 0, .5);
    }
    .slide-carousel .carousel-indicators li {
        border-radius: 0;
        width: 20px;
        height: 6px;
    }
    .slide-carousel .carousel-indicators .active {
        margin: 1px;
    }
    .slide-content {
        position: absolute;
        top: 0;
        left: 20%;
        display: block;
        float: left;
        width: 80%;
        max-height: 76%;
        padding: 1.5% 2% 2% 2%;
        overflow-y: auto;
    }
    .slide-content h4 {
        margin-bottom: 3px;
        margin-top: 0;
    }
    .slide-footer {
        position: absolute;
        bottom: 0;
        left: 20%;
        width: 78%;
        height: 20%;
        margin: 1%;
    }
    /* Scrollbars */
    .slide-content::-webkit-scrollbar {
        width: 5px;
    }
    .slide-content::-webkit-scrollbar-thumb:vertical {
        margin: 5px;
        background-color: #999;
        -webkit-border-radius: 5px;
    }
    .slide-content::-webkit-scrollbar-button:start:decrement,
    .slide-content::-webkit-scrollbar-button:end:increment {
        height: 5px;
        display: block;
    }
</style>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                <?php $this->load->view('admin/report/_ot');?>  
                 <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">                        
                        <h3 class="box-title"><?php echo $this->lang->line('ot_report'); ?></h3>
                    </div>
                    <form id="form1" action="" method="post" class="">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-6 col-md-3" >
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
                                    <label><?php echo $this->lang->line('consultant_doctor'); ?></label>
                                    <select class="form-control select2" name="consultant_doctor" style="width: 100%">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($staffsearch as $dkey => $value) { ?>
                                            <option value="<?php echo $value["id"] ?>"<?php
                                        if ((isset($staffsearch_select)) && ($staffsearch_select == $value["id"])) {
                                                echo "selected";
                                            }
                                            ?>><?php echo composeStaffNameByString($value["name"] , $value["surname"],$value["employee_id"]); ?></option>
                                        <?php }?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('doctor'); ?></span>
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

                            <div class="col-sm-6 col-md-3">
                <div class="form-group">
                    <label><?php echo $this->lang->line('operation_category'); ?></label>
                  <select name="operation_category" id="operation_category" class="form-control select2"  style="width:100%">
                            <option value=""><?php echo $this->lang->line('select') ?></option>
                            <?php foreach($categorylist as $operation){ ?>
                            <option value="<?php echo $operation['id']; ?>"><?php echo $operation['category']; ?></option>
                        <?php } ?>
                        </select>
                        <span class="text-danger"><?php echo form_error('operation_category'); ?></span>
                </div>
            </div>
             <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="operation_name"><?php echo $this->lang->line('operation_name'); ?></label>                        
                       <div>
                        <select name="operation_name" id="operation_name" class="form-control" style="width:100%">
                            <option value=""><?php echo $this->lang->line('select') ?></option>
                        </select>
                    </div>
                        <span class="text-danger"><?php echo form_error('operation_name'); ?></span>
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
                            <div class="download_label"><?php echo $this->lang->line('ot_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('ot_report'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line("reference_no"); ?></th>
                                        <th><?php echo $this->lang->line("opd_no"); ?></th>
                                        <th><?php echo $this->lang->line("ipd_no"); ?></th>
                                        <th><?php echo $this->lang->line("consultant_doctor"); ?></th>
                                        <th><?php echo $this->lang->line("assistant_consultant")."1"; ?></th>
                                        <th><?php echo $this->lang->line('operation_name'); ?></th>
                                        <th><?php echo $this->lang->line('operation_category'); ?></th>
                                        <?php
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                            <th class="white-space-nowrap"><?php echo $fields_value->name; ?></th>
                                        <?php } } ?>
                                        <th class="text-right"><?php echo $this->lang->line('result'); ?></th>
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
        //Initialize Select2 Elements
        $('.select2').select2();      
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
            url: '<?php echo base_url(); ?>admin/operationtheatre/checkvalidation',
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
                initDatatable('allajaxlist', 'admin/operationtheatre/otreports/',data.param,[],100);
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );

</script>
<script>
    $("#operation_category" ).change(function() {
      div_data ="" ;
      id = this.value;
        $('#operation_name').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/operationtheatre/getoperationbycategory',
            type: "POST",
            data: {id:id},
            dataType: 'json',
            async: false,
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel= "";
                    div_data += "<option value=" + obj.id + " " + sel + ">" + obj.operation + "</option>";
                });

                $("#operation_name").html("<option value=''>Select</option>");
                $('#operation_name').append(div_data);
                $("#operation_name").select2();                
            }
        });  
 });
</script>