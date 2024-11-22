<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('content_list'); ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('content_list'); ?></div>
                        <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                            <thead>
                                <tr>
									<th><?php echo $this->lang->line('title'); ?></th>
									<th><?php echo $this->lang->line('share_date'); ?></th>
									<th><?php echo $this->lang->line('valid_upto'); ?></th>
									<th><?php echo $this->lang->line('shared_by'); ?></th>
									<th class="noExport"><?php echo $this->lang->line('action'); ?></th>
								</tr>
                            </thead>
                            <tbody>
                                <?php  
                                if (!empty($contentlist))  {
                                    $count = 1;
                                    foreach ($contentlist as $contentlist_value) {
        ?>
                                        <tr class="">
                                            <td><?php echo $contentlist_value->title; ?></td>
                                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat($contentlist_value->share_date, $timeformat); ?></td>
                                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat($contentlist_value->valid_upto, $timeformat); ?></td>
                                            <td>											
											<?php  
												if($superadmin_restriction == 'disabled'){
														echo '';
												}else{
														echo $contentlist_value->name .' '. $contentlist_value->surname . ' (' . $contentlist_value->employee_id . ')';
												}   
											?>											
											</td>
                                            <td><?php echo "<a href='" . site_url('patient/dashboard/viewcontent/') . $contentlist_value->id . "'   class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('view') . "'><i class='fa fa-eye'></i></a>";  ?></td>                         
                                             
                                            
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

<div class="modal fade" id="viewModal"  role="dialog" aria-labelledby="myModalLabel">
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
                <h4 class="box-title"><?php echo $this->lang->line('bill_details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>

<div id="payMoney" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('make_payment') ?></h4>
            </div>
            <form id="payment_form" class="form-horizontal modal_payment" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="amount" class="col-sm-3 control-label"><?php echo $this->lang->line('payment_amount'). " (" . $currency_symbol . ")"; ?> <small class="req">*</small></label> 
                        <div class="col-sm-7">
                            <input type="text" class="form-control" name="deposit_amount" id="amount_total_paid" >
                            <input type="hidden" class="form-control" name="net_amount" id="net_amount" >
                            <span id="deposit_amount_error" class="text text-danger"></span>
                            <input type="hidden" name="payment_for" value="ambulance">
                            <input type="hidden" id="bill_id_modal" name="id" value="">
                        </div>
                    </div>
                </div>
            </form>
                <div class="modal-footer">
                    <button id="pay_button" class="btn btn-info pull-right"><?php echo $this->lang->line('add') ?></button>
                </div>
        </div>
    </div>
</div>

<div class="modal fade" id="allpayments" tabindex="-1" role="dialog" aria-labelledby="follow_up">   
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content">
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

    function viewDetail(id) {
        $.ajax({
            url: '<?php echo base_url() ?>patient/dashboard/getBillDetailsAmbulance/' + id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportdata').html(data);
                $('#edit_deletebill').html("<a href='#' data-toggle='tooltip' onclick='printData(" + id + ")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> ");
                holdModal('viewModal');
            },
        });
    }

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

    function payModal(bill_id,balance_amount){
        $("#bill_id_modal").val(bill_id);
        $("#amount_total_paid").val(balance_amount);
        $("#net_amount").val(balance_amount);
        $("#payMoney").modal({backdrop:'static'});
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
             data: {'id': record_id,module_type:module_type},
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