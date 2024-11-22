<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>

<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row"> 
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('pathology_bill'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('pathology_bill', 'can_add')) { ?>
                                <button type="button" class="btn btn-primary btn-sm assigntest" id="load1" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('generate_bill'); ?></button>
                            <?php } ?> 
                            <?php if ($this->rbac->hasPrivilege('pathology_test', 'can_view')) { ?>   
                                <a data-toggle=""href="<?php echo base_url(); ?>admin/pathology/search"  class="btn btn-primary btn-sm pathology"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('pathology_test'); ?></a> 
                            <?php } ?>                           
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover ajaxlist" id="testreport" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('pathology_test_reports'); ?>">
                            <thead>
                            <tr>
                                <th class="white-space-nowrap"><?php echo $this->lang->line('bill_no'); ?></th>
                                <th class="white-space-nowrap"><?php echo $this->lang->line('case_id'); ?></th>
                                <th class="white-space-nowrap"><?php echo $this->lang->line('reporting_date'); ?></th> 
                                <th class="white-space-nowrap"><?php echo $this->lang->line('patient_name'); ?></th>
                                <th class="white-space-nowrap"><?php echo $this->lang->line('reference_doctor'); ?></th>
                                <?php 
                                    if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                            ?>
                                            <th class="white-space-nowrap"><?php echo $fields_value->name; ?></th>
                                            <?php
                                        } 
                                    }
                                ?> 
                                <th class="white-space-nowrap"><?php echo $this->lang->line('discount') . ' (' . $currency_symbol . ')'; ?></th>
                                <th class="white-space-nowrap"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                                <th class="white-space-nowrap"><?php echo $this->lang->line('paid_amount'). ' (' . $currency_symbol . ')'; ?></th>
                                <th class="text-right white-space-nowrap"><?php echo $this->lang->line('balance_amount'). ' (' . $currency_symbol . ')'; ?></th>
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

<div class="modal fade" id="assigntestModal" aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <form id="bill" accept-charset="utf-8" method="post">
                <input type="hidden" name="doctor_id" id="doctorid">
            <div class="modal-header modal-media-header border-0">
                 <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                    <div class="row">                   
                    <div class="col-lg-5 col-md-5 col-sm-11">
                    <div class="row">
                    <div class="col-lg-9 col-md-7 col-sm-8 col-xs-8">
                        <div class="p-2 select2-full-width">
                            <select class="form-control patient_list_ajax" id="addpatient_id" name='patientid' onchange="get_PatientDetails(this.value)">
                            </select>
                        </div>
                    </div>
                     <div class="col-lg-3 col-md-5 col-sm-4 col-xs-2">
                        <div class="p-2">
                           <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) {?>
                            <a data-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient w-lg-100"><i class="fa fa-plus"></i>  <span><?php echo $this->lang->line('new_patient'); ?></span></a>
                            <?php }?>
                        </div>    
                    </div>   
                    </div>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-11">
                    <div class="row pt-sm-2">
                      <div class="col-lg-9 col-md-8 col-sm-8 col-xs-8">
                        <div class="p-2">
                            <div class="input-group">
                                <input type="text" class="form-control border-0" id="prescription_no" placeholder="<?php echo $this->lang->line('prescription_no'); ?>" name="prescription_no">
                                <div class="input-group-btn">
                                  <button class="btn btn-default btn-group-custom" type="button" id="search_prescription">
                                    <i class="fa fa-search"></i>
                                  </button>
                                </div>
                            </div>
                        </div>    
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                    <div class="form-check pt5 pb-sm-5">
                        <input class="form-check-input" type="checkbox" value="1" id="is_tpa" name="is_tpa">
                        <label class="form-check-label text-white" for="is_tpa">
                            <?php echo $this->lang->line('apply_tpa'); ?>
                        </label>
                    </div> 
                    </div>                
                    </div>
                    </div>               
                </div><!-- ./row -->
            </div><!--./modal-header-->

            <div class="pup-scroll-area">
                <div class="tabinsetbottom pt5">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-4">
                                <label><?php echo $this->lang->line('bill_no'); ?> <input readonly name="bill_no" class="transparentbg-border" id="billno" type="text" /></label>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-4">
                                <label><?php echo $this->lang->line('case_id'); ?> <input readonly name="case_reference_id" id="case_reference_id" type="text" class="transparentbg-border"/></label> 
                            </div>
                            <div class="col-lg-7 col-md-5 col-sm-4 text-right text-md-left">
                                <label><?php echo $this->lang->line('date'); ?>
                             <input name="date" id="txtDate10" type="text" class="transparentbg-border"/></label>
                            </div>
                        </div><!--./row-->
                    </div><!--./container-fluid-->    
                </div><!--./tabinsetbottom-->
                <div class="modal-body pb0">
            </div>
        </div><!--./row-->
           
                <div class="modal-footer sticky-footer">
                    <div class="pull-right">
                        <p id="demo"></p>                   
                        <input type="hidden" id="pathology_billing_id" name="pathology_billing_id" value="0" />
                        <input type="hidden" id="organisation_id" name="organisation_id" />
                        <input type="hidden" id="insurance_id" name="insurance_id" />
                        <input type="hidden" id="insurance_validity" name="insurance_validity" />                       
                        <button type="submit" name="save_print" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?>
                        </button>
                        <button type="submit" name="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="billsave" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div><!--./modal-body-->
    </div>
</div>

<div class="modal fade" id="viewModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='edit_delete'>
                        <a href="#" data-target="#edit_prescription" data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('report_details'); ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>    
</div>
 
<div class="modal fade" id="viewModalReport" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='edit_deletereport'>
                        <a href="#" data-target="#edit_prescription" data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('report_details'); ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdatareport"></div>
            </div>
        </div>
    </div>    
</div>

<div class="modal fade" id="viewDetailReportModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='action_detail_report_modal'>

                   </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('bill_details'); ?></h4> 
            </div>
            <div class="scroll-area">
                 <div class="modal-body ptt10 pb0">
                    <div id="reportbilldata"></div>
                </div>
           </div>
        </div>
    </div>    
</div>

<div class="modal fade" id="collectionModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-media-content mx-2">
              <form action="<?php echo site_url('admin/pathology/updatecollection'); ?>" method="POST" id="form-sample-collected">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='collection_modal_header'>

                   </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('sample_collection'); ?></h4> 
            </div>
            <div class="modal-body pb0 ptt10">
            <input type="hidden" name="pathology_report_id" value="0">  
            <input type="hidden" name="pathology_bill_id" value="0">  
                  <div class="form-group">
                    <label><?php echo $this->lang->line('sample_collected_person_name'); ?></label><small class="req"> *</small>
                    <select class="form-control" name="collected_by" id="collected_by">
                         <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($pathologist as $dkey => $dvalue) {
                                                                            ?>
                                            <option value="<?php echo $dvalue["id"]; ?>" ><?php echo $dvalue["name"] . " " . $dvalue["surname"]." (".$dvalue["employee_id"].")" ?>
                                            </option>   
                                        <?php } ?>
                    </select>
                  
                  </div>
                  <div class="form-group">
                    <label><?php echo $this->lang->line('collected_date'); ?></label><small class="req"> *</small>
                    <input type="text" class="form-control" name="collected_date" id="collected_date">
                  </div>
                   <div class="form-group">
                    <label><?php echo $this->lang->line('pathology_center'); ?></label><small class="req"> *</small>
                    <input type="text" class="form-control" name="pathology_center" id="pathology_center">
                  </div>
              </div>
            <div class="modal-footer">
                <?php if ($this->rbac->hasPrivilege('pathology_add_edit_collection_person', 'can_edit')) { ?>
              <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
          <?php } ?>
      </div>
      </form>
        </div>
    </div>    
</div>

<div class="modal fade" id="addReportModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <form action="<?php echo site_url('admin/pathology/updatereport'); ?>" method="POST" id="form-report_param">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='collection_modal_header'>

                   </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('add_edit_report'); ?></h4> 
            </div>
            <div class="scroll-area">
                <div class="modal-body pb0 ptt10">
              </div>
            </div>            
            <div class="modal-footer">
                <?php if ($this->rbac->hasPrivilege('pathology_add_edit_report', 'can_edit')) { ?>
                <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            <?php } ?>
      </div>
      </form>
        </div>
    </div>    
</div>

<div class="modal fade" id="viewModalbill" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='edit_deletebill'>

                   </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('bill_details'); ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportbilldata"></div>
            </div>
        </div>
    </div>    
</div>

<div class="modal fade" id="addPaymentModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pupclose" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('payments'); ?></h4>
            </div>
            <div class="pup-scroll-area">
                <div class="modal-body ptt10 pb10 min-h-3">
                </div>
            </div>    
        </div>
    </div>
</div>

<script id="testpatho-template" type="text/template">
   <?php
foreach ($testlist as $dkey => $testlist_value) {
    ?>
    <option value='<?php echo $testlist_value["id"]; ?>'>
        <?php echo $testlist_value["test_name"]." (".$testlist_value["short_name"].")"  ?>
    </option>
    <?php
     }
   ?>
</script>

<script type="text/javascript">
    var total_rows=1;
    var date_format_new = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
    var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';
    $(document).ready(function(){
 
           $('input[name="collected_date"]').datepicker({
          format: date_format_new,
          autoclose: true,
          todayHighlight: true
          });
    });
        
    $(function () {
        
        $('.select2').select2()
    });
    $('.patient_list_ajax').on('select2:select', function (e) {
            var createModal=$('#assigntestModal');
            var $this  =null;
        getInsertModalForm(createModal,$this);
      });     

            function holdModal(modalId) {
                $('#' + modalId).modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            }

            $(document).on('click','.assigntest',function(){
            var createModal=$('#assigntestModal');
            var $this = $(this);
            getInsertModalForm(createModal,$this);
        }); 

        let getInsertModalForm=(createModal,__this_obj=null)=>{
           
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/assigntestpatho',
                type: "POST",
                dataType: 'json',
                 beforeSend: function() {
                  (__this_obj === null)? "": __this_obj.button('loading');
                     createModal.addClass('modal_loading');
                },
                success: function(res) {   
                  total_rows=res.total_rows; 
                  $('#assigntestModal #billno').val(res.bill_no);
                  $('#assigntestModal .modal-body').html(res.page);
                   $('.filestyle','#assigntestModal').dropify();
                    updateDate();
                  $(".test_name").select2();
                  $(".consultant_doctor").select2();
               $('#assigntestModal').modal('show');
                  createModal.removeClass('modal_loading');
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                   (__this_obj === null)? "": __this_obj.button('reset');
                   createModal.removeClass('modal_loading');
            },
            complete: function() {
                (__this_obj === null)? "": __this_obj.button('reset');
                 createModal.removeClass('modal_loading');
            }
            });
        }            

            $(document).on('click','.delete_pathology',function(){    
             if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {       
            var $this = $(this);
            var recordId = $this.data('recordId');
            $this.button('loading');
            $.ajax({
                url: base_url+'admin/pathology/deletebill',
                type: "POST",
                data: {'id':recordId},
                dataType: 'json',
                 beforeSend: function() {
                    $this.button('loading');
                    
                },
                success: function(res) {   
                    if (res.status == "fail") {
                        
                        errorMsg(res.message);
                    } else {
                        successMsg(res.message);
                        $('#viewDetailReportModal').modal('hide');
                         table.ajax.reload();
                    }

                  $this.button('reset');
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                   $this.button('reset');
                    createModal.removeClass('modal_loading');
            },
            complete: function() {
                  $this.button('reset');            
            }
            });
             }
        });

        $(document).on('click','.edit_pathology',function(){
			
		var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'H', 'i' => 'mm','h'=>'hh']) ?>';
		 
        $('#txtDate10').datetimepicker({
         format: datetime_format,
        });	
		
            var createModal=$('#assigntestModal');
            var $this = $(this);
            var recordId=$this.data('recordId');
            $this.button('loading');
            $.ajax({
                url: base_url+'admin/pathology/editpathology',
                type: "POST",
                data: {'id':recordId},
                dataType: 'json',
                 beforeSend: function() {
                    $this.button('loading');
                      createModal.addClass('modal_loading');
                },
                success: function(res) { 
                   
                    total_rows=res.total_rows; 
                    $('#assigntestModal #billno').val(res.bill_prefix+res.bill_no); 
                    $('#case_reference_id').val(res.case_reference_id);
                    $('#assigntestModal .modal-body').html(res.page);
                    $('.filestyle','#assigntestModal').dropify();                    
                    $('#txtDate10').data("DateTimePicker").date(new Date(res.pathology_date));
                    updateDate();
                    $('#assigntestModal .modal-body').find('.test_name').select2();
                    $('#viewDetailReportModal').modal('hide');
                    $('#assigntestModal').modal('show');                   
                    var option = new Option(res.patient_name, res.patient_id, true, true);
                    $("#bill .patient_list_ajax").append(option).trigger('change'); 				  
					 
                    if(res.tpa_apply_status==1){
                        $("#is_tpa").prop('checked',true) ;
                    }else if(res.tpa_apply_status==0){
                        $("#is_tpa").prop('checked',false) ;  
                    }					
					 
					$("#prescription_no").attr("disabled", "disabled");
					$("#search_prescription").attr("disabled", "disabled");
					 
                    $("#pathology_billing_id").val(res.bill_no);                  

                    createModal.removeClass('modal_loading');
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                   $this.button('reset');
                    createModal.removeClass('modal_loading');
            },
            complete: function() {
                  $this.button('reset');
                     createModal.removeClass('modal_loading');
            }
            });
        });

        $('#load1').click(function(){
           $("#prescription_no").removeAttr("readonly");
        });

        function gettestpathodetails(batch_obj,testid) {
           
            var current_row =batch_obj.closest('tr');
            let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
      
        var patient_id= $('.patient_list_ajax').val();
            $.ajax({
                type: "POST",
                url: base_url + "admin/pathology/gettestpathodetails",
                data: {'id': testid ,'patient_id':patient_id,'is_tpa':is_tpa},
                dataType: 'json',
                beforeSend: function() {
                        current_row.find('.taxpercent').val("");
                        current_row.find('.report_date').val("");
                        current_row.find('.days').val("");
                        current_row.find('.amount').val("");
                        current_row.find('.taxamount').val("");
                        update_amount(batch_obj.closest('div.modal'));
                },
                success: function (res) {
                    if(res.status == 0){
                        errorMsg(res.msg);
                    }else{
                        current_row.find('.taxpercent').val(res.result.tax);
                        current_row.find('.days').val(res.result.report_days);
                        current_row.find('.amount').val(res.result.standard_charge);
                        var stnd_amt =  res.result.standard_charge;
                        var tax_per = res.result.tax;
                        var tax_amount = (stnd_amt*tax_per)/100;
                        current_row.find('.taxamount').val(tax_amount);
                        var day = res.result.report_days;
                        getdate(day,current_row.find('.report_date'));
                        update_amount(batch_obj.closest('div.modal'));
                        if(res.status == 2){
                            errorMsg(res.msg);
                        }
                    }
                }
            });
        }

        function getdate(day,datepicker_obj) {
            var report_day =  parseInt(day, 10);
            var selected_date=$("#txtDate10").data('DateTimePicker').date().toDate();
            var newdate = new Date(selected_date);
            newdate.setDate(newdate.getDate() + report_day);
            datepicker_obj.datepicker("update", newdate);           
        }        

        $(document).ready(function(){
            $('#assigntestModal').modal({
              backdrop: 'static',
              keyboard: true, 
              show: false
            });
              $('.datetime').datetimepicker({
                 format: datetime_format,
            });
       });
        
    $(document).on('click','.add-record',function(){
        var table = document.getElementById("tableID");
        
        total_rows=total_rows+1;
        var template=$("#testpatho-template").html();
        var div = "<td><input type='hidden' id='total_rows' name='total_rows[]' value='" + total_rows + "'><input type='hidden' name='inserted_id_" + total_rows + "' value='0'><select class='form-control test_name select2' style='width:100%'  name='test_name_"+total_rows+"' ><option value='<?php echo set_value('test_name_id'); ?>'><?php echo $this->lang->line('select') ?></option>"+template+"</select></td><td><input type='text' name='reportday_"+total_rows+"' id='reportday_" + total_rows + "'  class='form-control text-right days' readonly></td><td><input type='text' name='reportdate_"+total_rows+"' id='reportdate_" + total_rows + "'  class='form-control text-right report_date'></td><td><div class='input-group'><input type='text' name='taxpercent_" + total_rows + "' id='taxpercent_" + total_rows + "'  class='form-control text-right right-border-none taxpercent' autocomplete='off' readonly><span class='input-group-addon'> %</span></div></td><td><input type='text' name='amount_" + total_rows + "' id='amount_" + total_rows + "'  class='form-control text-right amount' readonly><input type='hidden' name='taxamount_" + total_rows + "' id='taxamount_" + total_rows + "'  class='form-control text-right taxamount' readonly></td>";
        var row =  "<tr id='row" + total_rows + "'>" + div + "<td><button type='button' data-row-id='"+total_rows+"' class='closebtn delete_rows'><i class='fa fa-remove'></i></button></td></tr>";
        $('#tableID').append(row);
        updateDate();
        $('.test_name').select2();
         total_rows++;
    });

    $(document).on('click','.delete_rows',function(e){          
            var modal_=$(e.target).closest('div.modal');            
            var message = "<?php echo $this->lang->line('are_you_sure'); ?>";            
            if(confirm(message)){ 
                var modal_=$(e.target).closest('div.modal');
				var del_row_id=$(this).data('rowId');
				$("#row" + del_row_id).remove();
				update_amount(modal_);
            }
    });

    $(document).on('select2:select','.test_name',function(){
        var medicine_details = {};
        gettestpathodetails($(this),$(this).val());
    });

    $(document).on('input paste keyup','.tax_percent,.discount_percent,.qty,.medicine_category,.medicine_name,.batch_no,.price', function(e){ 
        update_amount($(e.target).closest('div.modal'));
    });

	let update_amount=(__this)=>{
     
		var grandTotal = 0; 
        let total_tax_amount = 0;
        var $tblrows = __this.find(".tblProducts tbody tr");  
        var discount_percent=__this.find('.discount_percent').val();
      
        $tblrows.each(function (index) {
			var $tblrow = $(this);  
			grandTotal += parseFloat($tblrow.find("td input.amount").val());			       
			total_amount_with_discount = $tblrow.find("td input.amount").val()-(($tblrow.find("td input.amount").val()*discount_percent)/100);
			total_tax_amount += parseFloat((total_amount_with_discount*$tblrow.find("td input.taxpercent").val())/100);        
		});
   
        grandTotal=  isNaN(grandTotal) ? 0 : grandTotal;
        total_tax_amount=  isNaN(total_tax_amount) ? 0 : total_tax_amount;
		__this.find('.total').val(grandTotal.toFixed(2));
		discount=(grandTotal * discount_percent / 100 );
		let discount_amount= isNaN(discount) ? 0 : discount;
		__this.find('.discount').val(discount_amount.toFixed(2)); 
		var net_amount=((grandTotal-discount_amount)+total_tax_amount);           
		__this.find('.tax').val(total_tax_amount.toFixed(2));
		__this.find('.net_amount').val(net_amount.toFixed(2));
		__this.find('.payment_amount').val(net_amount.toFixed(2));
		__this.find('#payamount').val(net_amount.toFixed(2));
		__this.find('#amount').val(net_amount.toFixed(2));
      
		$("#billsave").show();
		$(".printsavebtn").show();
	}

    function addTotal1() {
        var total = 0;
        var total_taxamt = 0;        
        var discount_percent = $("#discount_percent").val();     
        var medicineTable=$("#assigntestModal .modal-body").find('table.tblProducts');
        medicineTable.find("tbody tr").each(function() {

           let row_amount= $(this).find("td input.amount").val();
           let row_tax_percentage= $(this).find("td input.taxpercent").val();

        total += parseFloat(row_amount);
        total_taxamt += parseFloat(((row_amount-(row_amount*discount_percent)/100)*row_tax_percentage)/100);

        });
       
         if(total>0){
        if (discount_percent != '') {
            var discount = (total * discount_percent) / 100;
            $("#discount").val(discount.toFixed(2));
        } else {
            var discount = $("#discount").val();          
        }       

        $("#total").val(total.toFixed(2));
        var net_amount = (parseFloat(total) - parseFloat(discount))+ parseFloat(total_taxamt);
        var cnet_amount = net_amount.toFixed(2)
        $("#net_amount").val(cnet_amount);
        $("#amount").val(cnet_amount);
        $("#tax").val(total_taxamt.toFixed(2));
        $("#payamount").val(cnet_amount);
        $("#billsave").show();
        $(".printsavebtn").show();
		}       
    }
	
    function dateChanged(ev) {            
            var $tblrows = $('.tblProducts').find("tbody tr");
            $tblrows.each(function (index) {
            var $tblrow = $(this);  
            var _row_day = $tblrow.find(".days").val();
            if(_row_day !=""){
           
            //==============
            var report_day =  parseInt(_row_day, 10);
            var selected_date=$("#txtDate10").data('DateTimePicker').date().toDate() ;
            var newdate = new Date(selected_date);
            newdate.setDate(newdate.getDate() + report_day);
            $tblrow.find(".report_date")
            .datepicker({
               format: date_format_new,
               autoclose: true,
               todayHighlight: true
               }).datepicker("update", newdate); 
            //================            
                
            }        
            });
    }    

	$("form#bill button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $(document).ready(function (e) {
    $(document).on('submit','#bill',function(e){
            e.preventDefault();
            let submit_button = $("button[type=submit][clicked=true]",this);
            let submit_button_name=submit_button.attr('name');
            console.log(submit_button_name);

            $.ajax({
                url: base_url+'admin/pathology/addBill',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
              beforeSend: function() {
                    submit_button.button('loading');
                },
                     success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        table.ajax.reload();
                        $('#assigntestModal').modal('hide');
                        console.log(submit_button_name);
                        if(submit_button_name == "save_print"){
                          printData(data.insert_id);  
                        }
                    }
                    submit_button.button('reset');
                },
                   error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  submit_button.button('reset');
            },
            complete: function() {
              submit_button.button('reset');
            }

            });
		});
    });
    
    $(".assigntest").click(function(){		
		var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'H', 'i' => 'mm','h'=>'hh']) ?>';
		 
        $('#txtDate10').datetimepicker({
			format: datetime_format,
        });
        $('#txtDate10').data("DateTimePicker").date(new Date());
        $('#txtDate10').on('dp.change', dateChanged);      
    });
    
    function get_Docname(id) {        
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctName',
            type: "POST",
            data: {doctor: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#doctname').val(res.name + " " + res.surname + " (" + res.employee_id + ")");
                    $('#doctorid').val(res.id);
                } else {

                }
            }
        });
    }  

    function viewDetailReport(id,pathology_id) {
        $.ajax({
            url: '<?php echo base_url() ?>admin/pathology/getReportDetails/' + id +'/'+pathology_id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportdatareport').html(data);
                $('#edit_deletereport').html("<?php if ($this->rbac->hasPrivilege('pathology_bill', 'can_view')) { ?><a href='#' data-toggle='tooltip' onclick='printData(" + id + "," + pathology_id + ")' data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?>");
                holdModal('viewModalReport');
            },
        });
    }
    
    $(document).on('click','.view_detail',function(){
         var id=$(this).data('recordId');
         PatientPathologyDetails(id,$(this));
    });

    function PatientPathologyDetails(id,btn_obj){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({
            url: base_url+'admin/pathology/getPatientPathologyDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
            beforeSend: function() {
              $this.button('loading');
                modal_view.addClass('modal_loading');
                
               },
            success: function (data) { 
                        
				$('#viewDetailReportModal .modal-body').html(data.page);  
				$('#viewDetailReportModal #action_detail_report_modal').html(data.actions);
				$('#viewDetailReportModal').modal('show');
				modal_view.removeClass('modal_loading');
            },

            error: function(xhr) { // if error occured
				alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
				$this.button('reset');
                modal_view.removeClass('modal_loading');
			},
			complete: function() {
				$this.button('reset');
                modal_view.removeClass('modal_loading');          
			}
        });  
    }
 
    $(document).on('click','.add_collection',function(){
        $('#collected_by').val('');
		var id=$(this).data('recordId');
		var modal_view=$('#collectionModal');
		var $this = $(this);   
		$.ajax({
            url: base_url+'admin/pathology/getReportCollectionDetail',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.button('loading');
               },
            success: function (data) {
            
            $("#collected_by").val(data.report.collection_specialist);
            $("#collectionModal .modal-body").find('input[name="pathology_report_id"]').val(data.report.id);
            $("#collectionModal .modal-body").find('input[name="pathology_bill_id"]').val(data.report.pathology_bill_id);
            $("#collectionModal .modal-body").find('input[name="pathology_center"]').val(data.report.pathology_center);
            $("#collectionModal .modal-body").find('input[name="collected_date"]').datepicker("update", new Date(data.report.collection_date));
            $('#collectionModal').modal('show');
            },

             error: function(xhr) { // if error occured
            alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.button('reset');               
      },
      complete: function() {
            $this.button('reset');     
      }
        });
});

  $(document).on('click','.add_report',function(){
       var id=$(this).data('recordId');
       var modal_view=$('#addReportModal');
       var $this = $(this);   
       $.ajax({
            url: base_url+'admin/pathology/getPathologyReportDetail',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.button('loading');
               },
            success: function (data) {       
            $('#addReportModal .modal-body').html(data.page);
            $('#addReportModal .filestyle').dropify();
            $('#addReportModal').modal('show');
            },

             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.button('reset');
               
      },
      complete: function() {
            $this.button('reset');     
      }
        });
});

$(document).on('click','.print_pathology_report',function(){
   var id=$(this).data('recordId');

   var $this = $(this);   
   $.ajax({
        url: base_url+'admin/pathology/printPatientReportDetail',
        type: "POST",
        data: {'id': id},
        dataType: 'json',
           beforeSend: function() {
          $this.button('loading');
           },
        success: function (data) {       
      popup(data.page);
        },

         error: function(xhr) { // if error occured
         alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
         $this.button('reset');
           
  },
  complete: function() {
        $this.button('reset'); 
  }
    });
});

    $(document).on('click','.view_bill',function(){
       var id=$(this).data('recordId');
       var modal_view=$('#viewModalbill');
       var $this = $(this);
   
 $.ajax({
            url: base_url+'admin/pathology/getBillDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.button('loading');
               },
            success: function (data) {       
               
            $('#viewModalbill .modal-body').html(data.page);
               $('#edit_deletebill').html("<?php if ($this->rbac->hasPrivilege('pathology_bill', 'can_view')) { ?><a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' class='print_bill' data-toggle='tooltip' data-record-id="+id+"  data-placement='bottom'  data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?>");
             $('#viewModalbill').modal('show');
            },

             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.button('reset');               
      },
      complete: function() {
            $this.button('reset');     
      }
        });
});

    $(document).on('click','.print_bill',function(){
    var id=$(this).data('recordId');      
        var $this = $(this);    
        $.ajax({
            url: base_url+'admin/pathology/getBillDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.button('loading');
               },
            success: function (data) {       
           popup(data.page);

            },

            error: function(xhr) { // if error occured
			alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            $this.button('reset');
               
      },
      complete: function() {
            $this.button('reset');     
      }
        });
    });

    function printData(id){
         $.ajax({
            url: base_url+'admin/pathology/getBillDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
               },
            success: function (data) {       
           popup(data.page);
            },

             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");      
               
      },
      complete: function() {           
     
      }
        });
    }

    function deleterecord(id) {
        var url = '<?php echo base_url() ?>admin/pathology/deleteTestReport/' + id;
        var msg = "<?php echo $this->lang->line('delete_message') ?>";
        delete_recordById(url, msg)
    }

    function editTestReport(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pathology/getPathologyReport',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
              
                $("#report_id").val(data.id);
                $("#charge_category_html").val(data.charge_category);
                $("#code_html").val(data.code);
                $("#charge_html").val(data.standard_charge);
                $("#customer_types").val(data.customer_type);
                $("#opdipd").val(data.opd_ipd_no);
                $("#edit_patient_name").val(data.patient_name);
                $("#edit_report_date").val(data.reporting_date);
                if (data.apply_charge == "") {
                    $("#apply_charge").val(data.standard_charge);
                } else {
                    $("#apply_charge").val(data.apply_charge);
                }
                $('select[id="edit_consultant_doctor"] option[value="' + data.consultant_doctor + '"]').attr("selected", "selected");
                $("#edit_description").val(data.description);
                $(".select2").select2().select2('val', data.patient_id);
                $("#viewModal").modal('hide');
                holdModal('editTestReportModal');
            },
        })
    }
     
    $(document).ready(function (e) {
        $("#updatetest").on('submit', (function (e) {
            e.preventDefault();
            $("#updatetestbtn").button('loading');
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/updateTestReport',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#updatetestbtn").button('reset');
                },
                error: function () {
                }
            });
        }));

        $(document).on('submit','#form-sample-collected',function(e){
            e.preventDefault();
          var pathology_bill_id=$(this).find('input[name="pathology_bill_id"]').val();
            var clicked_btn =  $("button[type=submit]",$(this));
             var form = $(this);
             var url = form.attr('action');
            $.ajax({
                url: base_url+'admin/pathology/updatecollection',
                type: "POST",
                data: form.serialize(), 
                dataType: 'json',
                  beforeSend: function() {
                  clicked_btn.button('loading');
                },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                           clicked_btn.button('reset');
                           $('#collectionModal').modal('hide');
                           PatientPathologyDetails(pathology_bill_id,clicked_btn);
                    }
                },
                error: function(xhr) { // if error occured
        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
           clicked_btn.button('reset');
    },
    complete: function() {
      clicked_btn.button('reset');
    }
            });
      }); 

    $(document).on('submit','#form-report_param',function(e){
        e.preventDefault();
        var pathology_bill_id=$(this).find('input[name="pathology_bill_id"]').val();
          var clicked_btn =  $("button[type=submit]",$(this));
             var form = $(this);
             var url = form.attr('action');
            $.ajax({
                url: base_url+'admin/pathology/updatereportparam',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
        processData: false,
        contentType: false,
                  beforeSend: function() {
                  clicked_btn.button('loading');
                },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        $('#addReportModal').modal('hide');
                         PatientPathologyDetails(pathology_bill_id,clicked_btn);
                    }
                   clicked_btn.button('reset');
                },
                error: function(xhr) { // if error occured
                  alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  clicked_btn.button('reset');
               },
               complete: function() {
                 clicked_btn.button('reset');
               }
            });
      });
    });

    $(document).ready(function (e) {
        $("#parameteradd").on('submit', (function (e) {
            e.preventDefault();
            $("#parameteraddbtn").button('loading');
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/parameteraddvalue',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#parameteraddbtn").button('reset');
                },
                error: function () {
                }
            });
        }));
    });
	
function updateDate(){
     $('#tableID').find('.report_date').datepicker({
      format: date_format_new,
      autoclose: true,
      todayHighlight: true
      });
}
</script>

<script type="text/javascript">
 $(document).on('click','#search_prescription',function(e){
    let modal_prescription_=$(e.target).closest('div.modal');
    getPrescriptionData(modal_prescription_);
	});
 
	function getPrescriptionData(modal_prescription_)
	{	
			let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
			var createModal=$('#assigntestModal');
			$.ajax({
				url: '<?php echo base_url(); ?>admin/pathology/prescriptionBill',
				type: "POST",
				data:{'prescription_no':$("input[name=prescription_no]").val(),'date':$('#txtDate10').val(),is_tpa:is_tpa},
				dataType: 'json',
				beforeSend: function() {
					createModal.addClass('modal_loading');
				},
				success: function(res) {	
                   
                    if(res.status == 0){
                    var message = "";
                        $.each(res.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
					}else{
                        if(res.patient_id == 0){
						errorMsg("<?php echo $this->lang->line('no_prescription_found'); ?>");
					}else{  
                        if(res.tpa_status==1){
                            errorMsg(res.msg);
                        }       
         
						$('#assigntestModal .modal-body').html(res.page);
						$('#case_reference_id').val(res.case_reference_id);
						$('.filestyle','#assigntestModal').dropify();
						$("#addpatient_id").select2("val", '');
						$(".test_name").select2();
						updateDate();
						update_amount(modal_prescription_);
						total_rows=(res.total_rows <= 0) ? 1:res.total_rows;
						total_rows=res.total_rows;
						addappointmentModal(res.patient_id, 'assigntestModal');
					}
                    }				
					
				},
				error: function(xhr) { // if error occured
					alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
					createModal.removeClass('modal_loading'); 
				},
				complete: function() {
					createModal.removeClass('modal_loading');
				}
			});
	}


    $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
         $('.filestyle','#addPaymentModal').dropify();
        $('.cheque_div').css("display", "block");
      }else{
        $('.cheque_div').css("display", "none");
      }
    });
    $(document).on('click','#addpatient_id',function(){
    
    });
$('#addpatient_id').on('select2:select', function (e) {
     $('#case_reference_id').val('');
     $('#prescription_no').val('');
});
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">

   $(document).on('click','.add_payment',function(){  
            var record_id=$(this).data('recordId'); 
            var $add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading');               
            payment_modal.modal('show'); 
            getPayments(record_id);
    });

   function getPayments(record_id){
         var payment_modal=$('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/pathology/getPathologyTransaction',
            type: "POST",
            data: {'id': record_id},
            dataType:"JSON",
            beforeSend: function(){
            },          
            success: function (data) {
         
           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading');  
            },
             error: function () {
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){
             payment_modal.removeClass('modal_loading'); 
            }
        });
    }

    $(document).on('submit','#add_partial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var pathology_billing_id=$("input[name='pathology_billing_id']",'#add_partial_payment').val();
            var form = $(this);    
            var btn = clicked_btn;
            btn.button('loading');
            $.ajax({
                url: form.attr('action'),
                type: "POST",
               data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,                       
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                         getPayments(pathology_billing_id);
                            table.ajax.reload();
                        }
                     btn.button('reset');
                },
                error: function () {

                },
                complete: function(){
                 btn.button('reset');
   }
            }); 
        });

         $(document).on('click','.print_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/pathology/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });

          $(document).on('click','.delete_trans', function(e){
                     if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {   
            e.preventDefault();
            var record_id=$(this).data('recordId');         
            var pathology_billing_id=$("input[name='pathology_billing_id']",'#add_partial_payment').val();
            var btn = $(this);       
            btn.button('loading');
            $.ajax({
                url: base_url+'admin/transaction/deleteByID',
                type: "POST",
                data: {'id':record_id,'pathology_billing_id':pathology_billing_id},
                dataType: 'JSON',               
                success: function (data) {
                    successMsg(data.message);
                    getPayments(pathology_billing_id);
                    btn.button('reset');
                    table.ajax.reload();
                },
                error: function () {
                    btn.button('reset');
                },
                complete: function(){
                 btn.button('reset');
            }
            }); 
        }
        });
 
    $('#assigntestModal').on('hidden.bs.modal', function () {
        var assigntestModal= $('#assigntestModal');
    $('#addpatient_id').select2("val", "");
    $('#billno,#prescription_no,#case_reference_id',assigntestModal).val("");
    $("input:checkbox[name=is_tpa]").prop('checked',false);
    $("#assigntestModal").find('input[name="date"]').data("DateTimePicker").date(new Date());;
    $("#organisation_id").val('');
    $("#insurance_id").val('');
    $("#insurance_validity").val(''); 
   });
   
    $(document).ready(function (e) {
        $('#viewDetailReportModal,#addPaymentModal,#collectionModal,#addReportModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
</script>
<script>
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/pathology/getpathologybillDatatable',[],[],100,
            [
            { "bSortable": false, "sWidth": "105px", "aTargets": [ -1 ] ,'sClass': 'dt-body-right'},
             {  "sWidth": "105px", "aTargets": [ -2,-3,-4 ] ,'sClass': 'dt-body-right'},
            { "aTargets": [ 1 ] ,'sClass': 'dt-body-center'}
            ]
            );
    });
} ( jQuery ) )
</script>
<script>
    $(document).on('click','.print_parameter',function(){
   var id=$(this).data('recordId');
   var $this = $(this);   
   $.ajax({
        url: base_url+'admin/pathology/printtestparameterdetail',
        type: "POST",
        data: {'id': id},
        dataType: 'json',
           beforeSend: function() {
          $this.button('loading');
           },
        success: function (data) {       
      popup(data.page);
        },

         error: function(xhr) { // if error occured
         alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
         $this.button('reset');
           
  },
  complete: function() {
        $this.button('reset'); 
  }
    });
});

    function get_percentage(discount_amount){
      var discount_amount=(discount_amount != "") ?discount_amount: 0;
      var total=$('#total').val();
      var  tax=$('#tax').val();
      var discount_percent=0;
      var net_amount=0;
      discount_percent=((parseInt(discount_amount)/parseInt(total))*100);
        $('#discount_percent').val(discount_percent.toFixed(2));
     }

</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>

<script type="text/javascript">   
    function get_PatientDetails(id) {       
        $.ajax({
            url: base_url+'admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            beforeSend: function() {                
            },
            success: function (res) {                
                if (res) {
                    $("#organisation_id").val(res.organisation_id);
                    $("#insurance_id").val(res.insurance_id);
                    $("#insurance_validity").val(res.insurance_validity);                   
                } 
            }
        });
    }

    $(document).on("click", "#is_tpa", function(){
		$(".assigntest").trigger("click");
		var addpatient_id=$("#addpatient_id").val();
		get_PatientDetails(addpatient_id);
    });
   
</script>