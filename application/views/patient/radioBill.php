<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<style type="text/css">
    #easySelectable {/*display: flex; flex-wrap: wrap;*/}
    #easySelectable li {}
    #easySelectable li.es-selected {background: #2196F3; color: #fff;}
    .easySelectable {-webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;}
</style>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('radiology_test_reports'); ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('radiology_test_reports'); ?></div>
                        <table class="table table-striped table-bordered table-hover example" id="testreport" cellspacing="0" width="100%">
                            <thead >
                                <tr>
                                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                                    <th><?php echo $this->lang->line("case_id"); ?></th>
                                    <th><?php echo $this->lang->line('reporting_date'); ?></th>
                                    <th><?php echo $this->lang->line('reference_doctor'); ?></th>
                                    <th><?php echo $this->lang->line('note'); ?></th>
                                    <?php
                                    if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                            ?>
                                        <th><?php echo $fields_value->name; ?></th>
                                    <?php } } ?>
                                    
                                    <th class="text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line("paid_amount") . ' (' . $currency_symbol . ')'; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line("balance_amount") . ' (' . $currency_symbol . ')'; ?></th>
                                    <th class="text-right noExport"><?php echo $this->lang->line('action') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                            if (empty($result)) {
                                ?>

                                                                <?php
                            } else {
                                $count = 1;
                                foreach ($result as $detail) {
                                    $balance_amount = ($detail->net_amount - $detail->paid_amount);
                                    ?>
                                        <tr class="">

                                            <td ><?php echo $this->customlib->getPatientSessionPrefixByType('radiology_billing').$detail->id; ?></td>
                                            <td><?php echo $detail->case_reference_id ?></td>
                                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($detail->date);?> </td>
                                            <td><?php echo composeStaffNameByString($detail->name, $detail->surname, $detail->employee_id); ?></td>
                                            <td><?php echo $detail->note; ?></td>
                                            <?php
                                                if (!empty($fields)) {
                                                    foreach ($fields as $fields_key => $fields_value) {
                                                ?>       
                                                    <td><?php echo $detail->{"$fields_value->name"}; ?></td>
                                            <?php   
                                                    }  
                                                }
                                            ?>
                                            <td class="text-right"><?php echo number_format((float)$detail->net_amount, 2, '.', ''); ?> </td>
                                            <td class="text-right"><?php echo number_format((float)$detail->paid_amount, 2, '.', ''); ?></td>
                                            <td class="text-right"><?php echo number_format((float)$balance_amount, 2, '.', ''); ?></td>
                                            <td class="pull-right white-space-nowrap">
                                                <a href="#" 
                                                   onclick=""
                                                   class="btn btn-default view_payment btn-xs"  data-toggle="tooltip"
                                                   title="<?php echo $this->lang->line('view_payments'); ?>" data-record-id="<?php echo $detail->id; ?>" data-module_type="radiology" >
                                                    <i class="fa fa-money"></i>
                                                </a> 
                                                <a href='javascript:void(0)'  data-loading-text='<?php echo $this->lang->line('please_wait'); ?>' data-record-id='<?php echo $detail->id ?>' class='btn btn-default btn-xs view_detail' data-toggle='tooltip' title='<?php echo $this->lang->line("view_reports"); ?>'><i class='fa fa-reorder'></i></a>
                                                <button type="button" class="btn btn-primary btn-xs" onclick=payModal(<?= $detail->id; ?>,<?= $balance_amount ?>) ><?php echo $this->lang->line("pay"); ?></button>
                                            </td>
                                        </tr>
                                        <?php
$count++;
    }
}
?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
            <div class="modal-body pt0 pb0">
                <div id="reportbilldata"></div>
            </div>
        </div>
    </div>    
</div>

<div class="modal fade" id="editTestReportModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_test_report'); ?></h4>
            </div>
        <form id="updatetest" enctype="multipart/form-data" accept-charset="utf-8"  method="post" class="ptt10">    
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                            <input type="hidden" name="id" id="report_id">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                            <?php echo $this->lang->line('customer_type'); ?></label>
                                        <div>
                                            <input class="form-control" style="text-transform: capitalize;" type="text" name="customer_type" id='customer_types' readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('patient_name'); ?></label>
                                        <small class="req"> *</small>
                                        <input type="text" name="patient_name" class="form-control" id="edit_patient_name">
                                        <span class="text-danger"><?php echo form_error('patient_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('reporting_date'); ?></label>
                                        <input type="text" id="edit_report_date" name="reporting_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('reporting_date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                            <?php echo $this->lang->line('refferal') . " " . $this->lang->line('doctor'); ?></label>
                                        <div>
                                            <select class="form-control select2" style="width: 100%" name='consultant_doctor' id="edit_consultant_doctor">
                                                <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                <?php foreach ($doctors as $dkey => $dvalue) {
    ?>
                                                    <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('consultant_doctor'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="description"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea name="description" id="edit_description" class="form-control" ></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('test') . " " . $this->lang->line('report'); ?></label>
                                        <input type="file" class="filestyle form-control" data-height="40" name="radiology_report">
                                        <span class="text-danger"><?php echo form_error('pathology_report'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge') . " " . $this->lang->line('category'); ?></label>
                                        <input type="text" class="form-control" readonly="" id="charge_category_html" >
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('code'); ?></label>
                                        <input type="text" readonly="" class="form-control" id="code_html" >
                                        <span class="text-danger" ></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('standard') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></label>
                                        <input type="text" readonly="" class="form-control" id="charge_html" >
                                        <span class="text-danger" ></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('apply') . " " . $this->lang->line('charge') . ' (' . $currency_symbol . ')'; ?></label>
                                        <input type="text" name="apply_charge" class="form-control" id="apply_charge" >
                                    </div>
                                </div>
                            </div><!--./row-->
                    </div><!--./col-md-12-->
                </div><!--./row-->
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="submit" id="updatetestbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right" ><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deletebill'>
                        <a href="#"  data-target="#edit_prescription"  data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('bill_details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModalReport" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deletereport'>
                        <a href="#"  data-target="#edit_prescription" data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
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
 
<div id="payMoney" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('make_payment') ?></h4>
            </div>
            <form id="payment_form" class="form-horizontal modal_payment" method="POST">
                <div class="modal-body pb0">
                    <div class="form-group">
                        <label for="amount" class="col-sm-3 control-label"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small></label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" name="deposit_amount" id="amount_total_paid" >
                            <input type="hidden" class="form-control" name="net_amount" id="net_amount" >
                            <span id="deposit_amount_error" class="text text-danger"></span>
                            <input type="hidden" name="payment_for" value="radiology">
                            <input type="hidden" id="bill_id_modal" name="id" value="">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button id="pay_button" class="btn btn-info pull-right payment_radio"><?php echo $this->lang->line('add'); ?></button>
                </div>
            </form>    
        </div>
    </div>
</div>

<div class="modal fade" id="allpayments" tabindex="-1" role="dialog" aria-labelledby="follow_up">   
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close"  data-dismiss="modal">&times;</button>
               <div class="modalicon"> 
                     <div id='allpayments_print'>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('payments'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0" id="allpayments_result">

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

    function viewDetail(id,radiology_id) {
        $.ajax({
            url: '<?php echo base_url() ?>patient/dashboard/getBillDetailsRadio/' + id +'/'+ radiology_id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportdata').html(data);
                $('#edit_deletebill').html("<a href='#' data-toggle='tooltip' onclick='printData(" + id + ","+ radiology_id +")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>");
                holdModal('viewModal');
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
            url: base_url+'patient/dashboard/getPatientRadiologyDetails',
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
       
             $('#viewDetailReportModal').modal({backdrop:'static'});
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

    $(document).on('click','.print_report',function(){
    var id=$(this).data('recordId');
       var $this = $(this);   
       $.ajax({
            url: base_url+'patient/dashboard/printPatientRadiologyReportDetail',
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

    $(document).on('click','.print_bill',function(){
        var id=$(this).data('recordId');
        var $this = $(this);
        $.ajax({
            url: base_url+'patient/dashboard/PrintBillDetailsRadiology',
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

    function payModal(bill_id,balance_amount){
        $("#bill_id_modal").val(bill_id);
        $("#amount_total_paid").val(balance_amount.toFixed(2));
        $("#net_amount").val(balance_amount);
        $("#payMoney").modal({backdrop:'static'});
    } 

    function viewDetailReport(id,radiology_id) {
        $.ajax({
            url: '<?php echo base_url() ?>patient/dashboard/getReportDetailsRadio/' + id +'/'+ radiology_id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportdatareport').html(data);
                $('#edit_deletereport').html("<a href='#' data-toggle='tooltip' onclick='printData(" + id + ","+ radiology_id +")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>");
                holdModal('viewModalReport');
            },
        });
    }

    $('#pay_button').click(function(){
        var formdata = new FormData($('#payment_form')[0]);
        $.ajax({
            url: base_url+'patient/pay/checkvalidate',
            type: "POST",
            data: formdata,
            dataType: 'json',
            cache : false,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    window.location.replace(base_url+'patient/pay');
                }
            }
        })
    })
</script>

<script>
    $(document).on('click','.view_payment',function(){ 
        
            var record_id=$(this).data('recordId'); 
            var module_type =$(this).data('module_type');
            getPayments(record_id,module_type);

    });

    function getPayments(record_id,module_type){        
         $.ajax({
             url: '<?php echo base_url(); ?>patient/dashboard/getpayment',
             type: "POST",
             data: {'id': record_id,'module_type':module_type},
             dataType:"JSON",
             beforeSend: function(){

             },          
             success: function (data) {
          
                $('#allpayments_result').html(data.page);
                $('#allpayments').modal({
                backdrop: 'static',
                keyboard: false,
                show: true             
            
             });
         }
              
         });
    }

    $(document).on('click','.print_trans',function(){
      var $this = $(this);
      var record_id=$this.data('recordId');
      var module_type =$(this).data('moduleType');
     
      $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>patient/dashboard/printbilltransaction',
          type: "POST",
          data:{'id':record_id,'module_type':module_type},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("Error occured.please try again");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });
</script>
<script>
   $(document).on('click','.print_parameter',function(){
       var id=$(this).data('recordId');
  
       var $this = $(this);   
       $.ajax({
            url: base_url+'patient/dashboard/printradiotestparameterdetail',
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
</script>