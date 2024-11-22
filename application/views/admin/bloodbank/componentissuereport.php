<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                <?php $this->load->view('admin/report/_blood_bank');?>
                <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">                        
                        <h3 class="box-title"><?php echo $this->lang->line('component_issue_report'); ?></h3>
                    </div>
                    <form id="form1" action="" method="post" class="">
                        <div class="box-body row pb0">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-3 col-md-3" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search_type'); ?></label><small class="req"> *</small>
                                    <select class="form-control" name="search_type" id="search_type_select" onchange="showdate(this.value)">
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
                                    <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>
                           <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('component_collect_by'); ?></label>
                                    <select class="form-control select2"  name="component_collect_by" style="width: 100%">
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
                                    <label><?php echo $this->lang->line('amount_collect_by'); ?></label>
                                    <select class="form-control select2"  name="amount_collected_by" id="amount_collected_by" style="width: 100%">
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($staffsearch as $dkey => $value) { ?>
                                            <option value="<?php echo $value["staffid"] ?>"<?php
                                        if ((isset($staffsearch_select)) && ($staffsearch_select == $value["staffid"])) {
                                                echo "selected";
                                            }
                                            ?>><?php echo $value["staffname"] . " " . $value["staffsurname"] ." (". $value["employee_id"].")" ?></option>
                                        <?php }?>
                                    </select>                                   
                                    <span class="text-danger" id="test_payment_mode" style="display:none"><?php echo $this->lang->line('amount_collected_note') ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3">
								<div class="form-group">
									<label> <?php echo $this->lang->line('blood_group'); ?></label>
									<select  style="width: 100%" class="form-control select2 blood_group"  name="blood_group" >
										<option value=""><?php echo $this->lang->line('select'); ?></option>
										<?php
											foreach ($stockbloodgroup as $blood_grp_value) {
										?>
										<option value="<?php echo $blood_grp_value['id']; ?>"><?php echo $blood_grp_value['name']; ?></option>
										<?php
										}
										?>
									</select>
								</div>
							</div> 
                       
						</div>
						<div class="box-body row pt0">
							<div class="col-sm-3 col-sm-3 col-md-3">
								<div class="form-group">
									<label> <?php echo $this->lang->line('components'); ?></label>
									<select  style="width: 100%" class="form-control select2 component_issue" id="blood_component" name="blood_component" >
										<option value=""><?php echo $this->lang->line('select'); ?></option>
                                   
									</select>
								</div>
							</div>
							<div class="col-sm-3 col-md-3" id="fromdate" style="display: none">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('date_from'); ?></label>
                                    <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3" id="todate" style="display: none">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('date_to'); ?></label>
                                    <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                    <span class="text-danger"><?php echo form_error('date_to'); ?></span>
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
                        <div class="box-body">
                            <div class="download_label"><?php echo $this->lang->line('blood_issue_report'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('blood_issue_report'); ?>">
                                    <thead>
										<tr>
											<th><?php echo $this->lang->line('bill_no'); ?></th>
											<th><?php echo $this->lang->line('issue_date'); ?></th>
											<th><?php echo $this->lang->line('received_to'); ?></th>
											<th><?php echo $this->lang->line('blood_group'); ?></th> 
											<th><?php echo $this->lang->line('component'); ?></th> 
											<th><?php echo $this->lang->line('gender'); ?></th>
											<th><?php echo $this->lang->line('donor_name'); ?></th>
											<th><?php echo $this->lang->line('bags'); ?></th>
											<th><?php echo $this->lang->line('component_collect_by'); ?></th>
										
											<?php
												if (!empty($fields)) {
													foreach ($fields as $fields_key => $fields_value) {
														?>
													<th class="white-space-nowrap"><?php echo $fields_value->name; ?></th>
											<?php } } ?>
											<th class="text-right"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
											<th class="text-right"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
											<th class="text-right"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?></th>
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

<script type="text/javascript">    
 $(document).ready(function (e) {
        $('.select2').select2();
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
            url: '<?php echo base_url(); ?>admin/bloodbank/checkvalidation',
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
                 
                   initDatatable('allajaxlist','admin/bloodbank/getcomponentissuereportDatatable',data.param,[],100,
                        [
                          {  "sWidth": "100px", "bSortable": false, "aTargets": [ -1,-2,-3 ] ,'sClass': 'dt-body-right'},
                        ]);
                }
            }
        });
        }
       ));
   });

} ( jQuery ) );

	$(document).on('select2:select','.blood_group',function(){
        var bloodgroup=$(this).val();
		get_components(bloodgroup,'');
    });
 
    function get_components(bloodgroup,component){
        var div_data="";
          $.ajax({
            url: base_url+'admin/bloodbank/get_componentBybloodId',
            type: "POST",
            data: {id: bloodgroup},
            dataType: 'json',
            success: function (res) {
                console.log(res);
                $.each(res, function (i, obj)
                {
                    console.log(i);
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.component_issue').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.component_issue').append(div_data);
                $('.component_issue').select2("val", component);

            }
        });
    }
</script>

<script type="text/javascript">
    $("#amount_collected_by").change(function(){
        if( $("#amount_collected_by").val()!=""){
            $("#test_payment_mode").css('display','block');
        }else{
            $("#test_payment_mode").css('display','none');
        }        
    });
</script>