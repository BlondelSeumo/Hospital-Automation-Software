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
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('radiology_single_billing'); ?></h3>
                         <div class="box-tools pull-right">
                           <button type="button" class="btn btn-primary btn-sm assigntest" id="load1" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_patient'); ?></button>              
                           
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('radiology_test_reports'); ?></div>
                <div class="">        
                    <table class="table table-striped table-bordered table-hover ajaxlist" id="testreport" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('radiology_single_billing'); ?>">
                            <thead class="white-space-nowrap">
                            <tr>
                                <th><?php echo $this->lang->line('bill_no'); ?></th>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <th><?php echo $this->lang->line('reporting_date'); ?></th> 
                                <th><?php echo $this->lang->line('patient_name'); ?></th>
                                <th><?php echo $this->lang->line('reference_doctor'); ?></th>
                                <th><?php echo $this->lang->line('note'); ?></th>
                                <?php 
                                if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                            ?>
                                            <th><?php echo $fields_value->name; ?></th>
                                             <?php
                                        } 
                                    }
                                ?> 
                                <th><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                                <th><?php echo $this->lang->line('paid_amount'). ' (' . $currency_symbol . ')'; ?></th>
                                <th class="text-right"><?php echo $this->lang->line('balance_amount'). ' (' . $currency_symbol . ')'; ?></th>
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
            <div class="modal-header modal-media-header">
                 <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                <div class="row">
                   <div class="col-lg-5 col-md-5 col-sm-11">
                    <div class="row">
                       <div class="col-lg-9 col-md-7 col-sm-8 col-xs-8">
                        <div class="p-2 select2-full-width">
                        <select name="patientid" class="form-control patient_list_ajax" id="addpatient_id" name='' onchange="get_PatientDetails(this.value)"></select>
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
                    <div class="row">
                       <div class="col-lg-9 col-md-8 col-sm-8 col-xs-8">
                           <div class="p-2">
                            <div class="input-group">
                                <input type="text" class="form-control border-0" id="prescription_no" placeholder="<?php echo $this->lang->line('prescription_no'); ?>" name="prescription_no">
                                <div class="input-group-btn">
                                    <button class="btn btn-default btn-group-custom" type="button" id="search_prescription"><i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                       </div>
                       <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                        <div class="form-check pt5">
                        <input class="form-check-input" type="checkbox" value="1" id="is_tpa" name="is_tpa">
                        <label class="form-check-label text-white" for="is_tpa">
                        <?php echo $this->lang->line('apply_tpa'); ?>
                        </label>
                        <input type="hidden" id="radiology_billing_id" name="radiology_billing_id" value="">
                       </div>
                       </div>
                    </div>
                   </div>
                </div><!--./d-flex-->
            </div><!--./modal-header-->
                <div class="pup-scroll-area">
                    <div class="tabinsetbottom pt5">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-2 col-md-3 col-sm-4">
                                    <label><?php echo $this->lang->line('bill_no'); ?><input readonly name="bill_no" id="billno" type="text" class="transparentbg-border"/>
                                    <span class="text-danger"><?php echo form_error('bill_no'); ?></span></label>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-4">
                                    <label><?php echo $this->lang->line('case_id'); ?>
                                        <input readonly name="case_reference_id" id="case_reference_id" type="text" class="transparentbg-border"/>
                                        <span class="text-danger"><?php echo form_error('case_reference_id'); ?></span>
                                    </label>
                                </div>
                                <div class="col-lg-7 col-md-5 col-sm-4 text-right text-md-left">
                                     <label><?php echo $this->lang->line('date'); ?>
                         <input name="date" type="text"  id="txtDate10"  class="transparentbg-border"/>
                                     </label>
                                </div>
                            </div>
                        </div>
                    </div>            
                    <div class="modal-body pb0">                        
                    </div><!--./row-->
                </div>
                <div class="modal-footer sticky-footer">
                    <div class="pull-right">                        
                        <p id="demo"></p>                        
                        <input type="hidden" id="organisation_id" name="organisation_id" />
                        <input type="hidden" id="insurance_id" name="insurance_id" />
                        <input type="hidden" id="insurance_validity" name="insurance_validity" />                      
						<button type="submit" name="save_print" style="display: none;" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                        <button type="submit" name="save" data-loading-text="<?php echo $this->lang->line('processing'); ?>" style="display: none" id="billsave" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div><!--./modal-body-->
    </div>
</div>

<div class="modal fade" id="viewDetailReportModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-placement="bottom" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='action_detail_report_modal'>
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

<div class="modal fade" id="collectionModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-media-content mx-2">
        <form action="" method="POST" id="form-sample-collected">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='collection_modal_header'>
                   </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('sample_collection'); ?></h4> 
            </div>
        <div class="modal-body">
          <input type="hidden" name="radiology_report_id" value="0">  
          <input type="hidden" name="radiology_bill_id" value="0">  
          <div class="form-group">
                <label><?php echo $this->lang->line('sample_collected_person_name'); ?></label><small class="req"> *</small>
                <select class="form-control" name="collected_by" id="collected_by">
                     <option value=""><?php echo $this->lang->line('select') ?></option>
                            <?php foreach ($radiologist as $dkey => $dvalue) {
                                                            ?>
                            <option value="<?php echo $dvalue["id"]; ?>" <?php
                                    if ((isset($radiologist_select)) && ($radiologist_select == $dvalue["id"])) {
                                        echo "selected";
                                     }
                                ?>><?php echo $dvalue["name"] . " " . $dvalue["surname"]." (".$dvalue["employee_id"].")" ?>
                            </option>   
                        <?php } ?>
                </select>          
            </div>
              <div class="form-group">
                <label><?php echo $this->lang->line('collected_date'); ?></label><small class="req"> *</small>
                <input type="text" class="form-control" name="collected_date" id="collected_date">
              </div>
               <div class="form-group">
                <label><?php echo $this->lang->line('radiology_center'); ?></label><small class="req"> *</small>
                <input type="text" class="form-control" name="radiology_center" id="radiology_center">
              </div>
              </div>
            <div class="modal-footer">
              <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
      </div>
      </form>
        </div>
    </div>    
</div>
    
<div class="modal fade" id="addReportModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
              <form action="" method="POST" id="form-report_param">
                <div class="modal-header modal-media-header">
                    <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                    <div class="modalicon"> 
                        <div id='collection_modal_header'>
                    </div>
                    </div>
                    <h4 class="modal-title"><?php echo $this->lang->line('add_edit_report'); ?></h4> 
                </div>
                <div class="scroll-area">
                    <div class="modal-body">         
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>    
</div>

<div class="modal fade" id="addPaymentModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-placement="bottom" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('payments'); ?></h4>
            </div>
            <div class="scroll-area">
                <div class="modal-body pb0 min-h-3">
                </div>
            </div>
        </div>
    </div>
</div>

<script id="testradio-template" type="text/template">
   <?php
foreach ($testlist as $dkey => $testlist_value) {
    ?>
    <option value='<?php echo $testlist_value["id"]; ?>'>
        <?php echo $testlist_value["test_name"]." (".$testlist_value["short_name"].")"; ?>
    </option>
    <?php
     }
   ?>
</script>

<script type="text/javascript">

    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2();
    });
    var  total_rows = 1;
    var date_format_new = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
    var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';

     $(document).ready(function(){
 
           $('input[name="collected_date"]').datepicker({
          format: date_format_new,
          autoclose: true,
          todayHighlight: true
          });
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
             $this.button('loading');
            $.ajax({
                url: '<?php echo base_url(); ?>admin/radio/assigntestradio',
                type: "POST",
                dataType: 'json',
                 beforeSend: function() {
                    $this.button('loading');
                      createModal.addClass('modal_loading');
                },
                success: function(res) {     
                    $('#assigntestModal #billno').val(res.bill_no);
                    $('#assigntestModal .modal-body').html(res.page);
                  
                     $('#assigntestModal .filestyle').dropify();
                     $(".test_name").select2();
                     updateDate();
                    $('#assigntestModal').modal('show');
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

        $(document).on('click','.edit_radiology',function(){
            var createModal=$('#assigntestModal');
            var $this = $(this);
            var recordId=$this.data('recordId');
             $this.button('loading');
            $.ajax({
                url: base_url+'admin/bill/editradiology',
                type: "POST",
                data: {'id':recordId},
                dataType: 'json',
                 beforeSend: function() {
                    $this.button('loading');
                      createModal.addClass('modal_loading');
                },
                success: function(res) { 
					
					$("#prescription_no").attr("disabled", "disabled");
					$("#search_prescription").attr("disabled", "disabled");
					
                    total_rows =res.total_rows; 
                    $('#assigntestModal #billno').val(res.bill_no);
                    $('#case_reference_id').val(res.case_reference_id);
                    var option = new Option(res.patient_name, res.patient_id, true, true);
                    $("#bill .patient_list_ajax").append(option).trigger('change');
                    $('#assigntestModal .modal-body').html(res.page);
                    $('#assigntestModal .modal-body').find('.test_name').select2();
                    $('#assigntestModal .filestyle').dropify();
                    $('#txtDate10').data("DateTimePicker").date(new Date(res.radiology_date));
                    updateDate();
                    $('#viewDetailReportModal').modal('hide');
                    $('#assigntestModal').modal('show');    

                    $("#radiology_billing_id").val(res.bill_no);              

                    if(res.tpa_apply_status==1){
                        $("#is_tpa").prop('checked',true) ;
                    }else if(res.tpa_apply_status==0){
                        $("#is_tpa").prop('checked',false) ;  
                    }   
                    createModal.removeClass('modal_loading');
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again') ?>");
                   $this.button('reset');
                    createModal.removeClass('modal_loading');
            },
            complete: function() {
                $this.button('reset');
                createModal.removeClass('modal_loading');
            }
            });
        });

       $(document).on('click','.delete_radiology',function(){ 
       if (confirm('<?php echo $this->lang->line("delete_confirm") ?>')) {             
            var $this = $(this);
            var recordId=$this.data('recordId');
            $this.button('loading');
            $.ajax({
                url: base_url+'admin/radio/deleteRadiologyBill',
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

        $(document).on('select2:select','.test_name',function(){
            var count = $(this).attr('id');
            // gettestradiodetails($(this).val(),count);

            gettestradiodetails($(this),$(this).val());


        });

        // function gettestradiodetails(testid,id) {
        //     let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
        //     var patient_id= $('.patient_list_ajax').val();            
        //     $.ajax({
        //         type: "POST",
        //         url: base_url + "admin/radio/gettestradiodetails",
        //         data: {'id': testid ,is_tpa:is_tpa,patient_id:patient_id},
        //         dataType: 'json',
        //         success: function (res) {       
        //             if (res.status == 0) {
        //                 errorMsg(res.msg);
        //             }else{
        //                 $('#reportday_' + id).val(res.result.report_days);
        //                 $('#amount_' + id).val(res.result.standard_charge);
        //                 $('#taxpercent_' + id).val(res.result.percentage);
        //                 var stnd_amt =  $('#amount_' + id).val();
        //                 var tax_per = $('#taxpercent_' + id).val();
        //                 var tax_amount = stnd_amt*tax_per/100;
        //                 $('#taxamount_' + id).val(tax_amount);
        //                 var day = $('#reportday_' + id).val()
        //                 getdate(day,id)
        //                 addTotal();
        //                 if(res.status == 2){
        //                     errorMsg(res.msg);
        //                 }
        //             }
        //         }
        //     });
        // }   


        function gettestradiodetails(batch_obj,testid) {
            var current_row =batch_obj.closest('tr');
            let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
            var patient_id= $('.patient_list_ajax').val();
            $.ajax({
                type: "POST",
                url: base_url + "admin/radio/gettestradiodetails",
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
                        current_row.find('.taxpercent').val(res.result.percentage);
                        current_row.find('.days').val(res.result.report_days);
                        current_row.find('.amount').val(res.result.standard_charge);
                        var stnd_amt =  res.result.standard_charge;
                        var tax_per = res.result.percentage;
                        var tax_amount = stnd_amt*tax_per/100;
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
       
       
        // function getdate(day,id) {
        //     var report_day =  parseInt(day, 10);
        //     var selected_date=$("#txtDate10").data('DateTimePicker').date().toDate(); 
        //     var newdate = new Date(selected_date);
        //     newdate.setDate(newdate.getDate() + report_day);
        //     $("#reportdate_"+id).datepicker("update", newdate);           
        // }

        function getdate(day,datepicker_obj) {      
            var report_day =  parseInt(day, 10);
            var selected_date=$("#txtDate10").data('DateTimePicker').date().toDate(); 
            var newdate = new Date(selected_date);
            newdate.setDate(newdate.getDate() + report_day);             
            datepicker_obj.datepicker("update", newdate); 
        }


      
        $(document).on('click','.add-record',function(){
        var table = document.getElementById("tableID");       
        total_rows=total_rows+1;
        var template=$("#testradio-template").html();
        var div = "<td><input type='hidden' id='total_rows' name='total_rows[]' value='" + total_rows + "'><input type='hidden' name='inserted_id_" + total_rows + "' value='0'><select class='form-control test_name select2' style='width:100%'   id='"+ total_rows + "' name='test_name_" + total_rows + "' ><option value='<?php echo set_value('test_name_id'); ?>'><?php echo $this->lang->line('select') ?></option>"+template+"</select></td><td><input type='text' name='reportday_" + total_rows + "' id='reportday_" + total_rows + "'  class='form-control text-right days' readonly></td><td><input type='text' name='reportdate_" + total_rows + "' id='reportdate_" + total_rows + "'  class='form-control text-right report_date'></td><td><div class='input-group'><input type='text' name='taxpercent_" + total_rows + "' id='taxpercent_" + total_rows + "'  class='form-control text-right right-border-none taxpercent' autocomplete='off' readonly><span class='input-group-addon'> %</span></div></td><td><input type='text' name='amount_" + total_rows + "' id='amount_" + total_rows + "'  class='form-control text-right amount' readonly><input type='hidden' name='taxamount_" + total_rows + "' id='taxamount_" + total_rows + "'  class='form-control text-right taxamount' readonly></td>";
        var row =  "<tr id='row" + total_rows + "'>" + div + "<td><button type='button' data-row-id='"+total_rows+"' class='closebtn delete_row'><i class='fa fa-remove'></i></button></td></tr>";
        $('#tableID').append(row);
        updateDate();
        $('.test_name').select2();
        total_rows++;
    });

    
      $(document).on('click','.delete_row',function(e){
        var modal_=$(e.target).closest('div.modal');
        var del_row_id=$(this).data('rowId');            
        if(confirm('<?php echo $this->lang->line('are_you_sure'); ?>')){                
            var del_row_id=$(this).data('rowId');
            $("#row" + del_row_id).remove();
            update_amount(modal_);
        }
    });


             
    $(document).on('input paste keyup','.tax_percent,.discount_percent,.qty,.medicine_category,.medicine_name,.batch_no,.price', function(e){ 
        update_amount($(e.target).closest('div.modal'));
    });


    let update_amount=(__this)=>{
    var discount_raw=0;
		var grandTotal = 0; 
        var total_tax_amount = 0;
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
                discount_raw=(grandTotal * discount_percent / 100 );
                discount=  isNaN(discount_raw) ? 0 : discount_raw;       
                __this.find('.discount').val(discount.toFixed(2));  
                var net_amount=((grandTotal-discount)+total_tax_amount);           
               __this.find('.tax').val(total_tax_amount.toFixed(2));
               __this.find('.net_amount').val(net_amount.toFixed(2));
               __this.find('.payment_amount').val(net_amount.toFixed(2));
               __this.find('#payamount').val(net_amount.toFixed(2));
               __this.find('#amount').val(net_amount.toFixed(2));
             
        $("#billsave").show();
        $(".printsavebtn").show();
	}  
    

	function addTotal1() {
        // var total = 0;
        // var total_taxamt = 0;
        // var discount = 0;
        // var medicineTable=$("#assigntestModal .modal-body").find('table.tblProducts');
		
        // medicineTable.find("tbody tr").each(function() {
		// 	total += parseFloat($(this).find("td input.amount").val());
		// 	total_taxamt += parseFloat($(this).find("td input.taxamount").val());			 
        // });
		
        // if(total>0){
			
		// 	var discount_percent = $("#discount_percent").val();
		// 	medicineTable.find("tbody tr").each(function() {
		// 		total_taxamt = 0;			 
		// 		total_amount_with_discount = $(this).find("td input.amount").val()-(($(this).find("td input.amount").val()*discount_percent)/100);
		// 		total_taxamt += parseFloat((total_amount_with_discount*$(this).find("td input.taxpercent").val())/100);				
		// 	});        
       
		// 	if (discount_percent != '') {
		// 		var discount = isNaN((total * discount_percent) / 100) ? 0 : ((total * discount_percent) / 100);
		// 		$("#discount").val(discount.toFixed(2));
		// 	} else {
		// 		$("#discount").val(discount.toFixed(2));             
		// 	}       
 
		// 	$("#total").val(total.toFixed(2));
		// 	var net_amount = parseFloat(total) + parseFloat(total_taxamt) - parseFloat(discount);      
		// 	var cnet_amount = net_amount.toFixed(2)
		// 	$("#net_amount").val(cnet_amount);
		// 	$("#tax").val(total_taxamt.toFixed(2));
		// 	$("#payamount").val(cnet_amount);
		// 	$("#amount").val(cnet_amount);
		// 	$("#billsave").show();
		// 	$(".printsavebtn").show();
		// }       
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
            $tblrow.find(".report_date").datepicker("update", newdate); 
            //================            
                
            }        
            });
        }

    $(document).ready(function (e) {
        $("form#bill button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

$(document).on('submit','#bill',function(e){  
            e.preventDefault();
            var btn = $("#billsave");
            let submit_button = $("button[type=submit][clicked=true]",this);
            let submit_button_name=submit_button.attr('name');           
            $.ajax({
                url: base_url+'admin/bill/add_radiology_bill',
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
                  alert("Error occured.please try again");
                  submit_button.button('reset');
                },
                complete: function() {
                  submit_button.button('reset');
                }
            });
        });
    });

   $(document).ready(function (e) {
    $('#txtDate10').datetimepicker({
         format: datetime_format,
}).on('dp.change',dateChanged);
$('#txtDate10').data("DateTimePicker").date(new Date());
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
                } else {

                }
            }
        });
    }

    function viewDetailReport(id,radiology_id) {
        $.ajax({
            url: '<?php echo base_url() ?>admin/radio/getReportDetails/'  + id +'/'+radiology_id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportdatareport').html(data);
                $('#edit_deletereport').html("<?php if ($this->rbac->hasPrivilege('add_radio_patient_test_reprt', 'can_view')) { ?><a href='#' data-toggle='tooltip' onclick='printData(" + id + ")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a><?php } ?>");
                holdModal('viewModalReport');
            },
        });
    }

    function printData(id) {
             $.ajax({
            url: base_url+'admin/radio/getBillDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
            
               },
            success: function (data) {     
                popup(data.page);
            },

             error: function(xhr) { // if error occured
            },
           complete: function() {           
        
          }
        });
    }

      $(document).on('click','.print_bill',function(){
        var id=$(this).data('recordId');      
        var $this = $(this);   
        $.ajax({
            url: base_url+'admin/radio/getBillDetails',
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

    $('#viewModalbill').on('hidden.bs.modal', function () {
     $('#reportdatabill,#edit_deletebill').html("");
    });

    function viewDetailbill(id) {
        var view_modal=$('#viewModalbill');
        $.ajax({
            url: '<?php echo base_url() ?>admin/radio/getradiobilldetails',
            type: "POST",
            data: {'id': id},
            dataType:"JSON",
            beforeSend: function(){
            $('#reportdatabill,#edit_deletebill').html("");
            $('#viewModalbill').modal('show');
           view_modal.addClass('modal_loading');
           },
           complete: function(){
             view_modal.removeClass('modal_loading');
           },
            success: function (data) {
                $('#reportdatabill').html(data.page);
                $('#edit_deletebill').html(data.actions);
                view_modal.removeClass('modal_loading');
            },
        });
    }

    function deleterecord(id) {
        var url = '<?php echo base_url() ?>admin/radio/deletetestbill/' + id;
        var msg = "<?php echo $this->lang->line('delete_message') ?>";
        delete_recordById(url, msg)
    }

    function editTestReport(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/radio/getRadiologyReport',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#report_id").val(data.id);
                $("#charge_category_html").val(data.charge_category);
                $("#code_html").val(data.code);
                $("#charge_html").val(data.standard_charge);
                if (data.apply_charge == "") {
                    $("#apply_charge").val(data.standard_charge);
                } else {
                    $("#apply_charge").val(data.apply_charge);
                }
                $("#customer_types").val(data.customer_type);
                $("#opdipd").val(data.opd_ipd_no);
                $("#edit_patient_name").val(data.patient_name);
                $("#edit_report_date").val(data.reporting_date);
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
            $("#updatetestbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/radio/updateTestReport',
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
    });

     $(document).on('submit','#form-sample-collected',function(e){
            e.preventDefault();
          var radiology_bill_id=$(this).find('input[name="radiology_bill_id"]').val();
            var clicked_btn =  $("button[type=submit]",$(this));
             var form = $(this);
             var url = form.attr('action');
            $.ajax({
                url: base_url+'admin/radio/updatecollection',
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
                           PatientRadiologyDetails(radiology_bill_id,clicked_btn);
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
        var radiology_bill_id=$(this).find('input[name="radiology_bill_id"]').val();
          var clicked_btn =  $("button[type=submit]",$(this));
             var form = $(this);
             var url = form.attr('action');
            $.ajax({
                url: base_url+'admin/radio/updatereportparam',
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
                         PatientRadiologyDetails(radiology_bill_id,clicked_btn);
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


function getPrescriptionData(modal_prescription_){		
		var createModal=$('#assigntestModal');
		let is_tpa= $("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;

		$.ajax({
			url: baseurl+'admin/radio/prescriptionBill',
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
					$('#assigntestModal .filestyle').dropify();
					$('#case_reference_id').val(res.case_reference_id);
					$('.test_name').select2();

					if(res.record_available == 1){        
						var option = new Option(res.patient_name+" ("+res.patient_id+")", res.patient_id, true, true);
						$("#bill .patient_list_ajax").append(option).trigger('change');				
					}
			
					updateDate();
					update_amount(modal_prescription_);
					total_rows=(res.total_rows <= 0) ? 1:res.total_rows;
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

</script>
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
            url: '<?php echo base_url() ?>admin/bill/getRadiologyTransactions',
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
            var radiology_billing_id=$("input[name='radiology_billing_id']",'#add_partial_payment').val();
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
                        getPayments(radiology_billing_id);
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

          $(document).on('click','.delete_trans', function(e){
            e.preventDefault();
        var record_id=$(this).data('recordId');         
        var radiology_billing_id=$("input[name='radiology_billing_id']",'#add_partial_payment').val();

            var btn = $(this);       
            btn.button('loading');
             var conf = confirm("<?php echo $this->lang->line('delete_confirm')?>");
    if(conf == true){        
    
            $.ajax({
                url: base_url+'admin/transaction/deleteByID',
                type: "POST",
                data: {'id':record_id,'radiology_billing_id':radiology_billing_id},
                dataType: 'JSON',               
                success: function (data) {
                    successMsg(data.message);
                    getPayments(radiology_billing_id);
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

         $(document).on('click','.print_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/radio/printTransaction',
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

    $(document).on('click','.view_detail',function(){
         var id=$(this).data('recordId');
         PatientRadiologyDetails(id,$(this));
    });

    function PatientRadiologyDetails(id,btn_obj){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({
            url: base_url+'admin/bill/getPatientRadiologyDetails',
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

    $(document).on('click','.add_report',function(){
        
           var id=$(this).data('recordId');
           var modal_view=$('#addReportModal');
           var $this = $(this);   
           $.ajax({
                url: base_url+'admin/radio/getRadiologyReportDetail',
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

    $(document).on('click','.print_report',function(){
       var id=$(this).data('recordId');  
       var $this = $(this);   
       $.ajax({
            url: base_url+'admin/radio/printPatientReportDetail',
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
 
    $(document).on('click','.add_collection',function(){
        $('#collected_by').val('');
       var id=$(this).data('recordId');
       var modal_view=$('#collectionModal');
       var $this = $(this);   
       $.ajax({
            url: base_url+'admin/radio/getReportCollectionDetail',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.button('loading');
               },

            success: function (data) {       
            
          $('#collected_by').val(data.report.collection_specialist);
            $("#collectionModal .modal-body").find('input[name="radiology_report_id"]').val(data.report.id);
            $("#collectionModal .modal-body").find('input[name="radiology_bill_id"]').val(data.report.radiology_bill_id);
            $("#collectionModal .modal-body").find('input[name="radiology_center"]').val(data.report.radiology_center);
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

    $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.filestyle','#addPaymentModal').dropify();
        $('.cheque_div').css("display", "block");
      }else{
        $('.cheque_div').css("display", "none");
      }
    });    

    $('#assigntestModal').on('hidden.bs.modal', function () {
        var assigntestModal= $('#assigntestModal');
    $('#addpatient_id').select2("val", "");
    $('#billno,#prescription_no,#case_reference_id',assigntestModal).val("");
      $("#assigntestModal").find('input[name="date"]').data("DateTimePicker").date(new Date());;
      $("input:checkbox[name=is_tpa]").prop('checked',false);
   });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/radio/getradiologybillDatatable',[],[],100,
            [
        {  "sWidth": "105px", "aTargets": [ -2,-3 ] ,'sClass': 'dt-body-right'},
        {  "sWidth": "105px", "bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-right'},
               {  "sWidth": "30px", "aTargets": [ 2 ] ,'sClass': 'dt-body-left'},
               {  "sWidth": "100px", "aTargets": [ 0 ] ,'sClass': 'dt-body-left'},
            ]);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>
<script type="text/javascript">
    
     $(document).ready(function(){
            $('#assigntestModal,#collectionModal,#addReportModal').modal({
              backdrop: 'static',
              keyboard: true, 
              show: false
            });
            $('.datetime').datetimepicker({
                 format: datetime_format,
            });           
       });
    
    $(document).ready(function (e) {
        $('#viewDetailReportModal,#addPaymentModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
    
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
                    $("#organisation_name").val(res.organisation_name);
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
