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
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('ambulance_bill'); ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('ambulance_bill'); ?></div>
                        <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                                    <th><?php echo $this->lang->line('vehicle_no'); ?></th>
                                    <th><?php echo $this->lang->line('vehicle_model'); ?></th>
                                    <th><?php echo $this->lang->line('driver_name'); ?></th>
                                    <th><?php echo $this->lang->line('driver_contact'); ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('tax') . " (%)"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?></th>
                                     <?php if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                         ?>
                                        <th><?php echo ucfirst($fields_value->name); ?></th>
                                    <?php } } ?>
                                    <th  width="8%" class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (!empty($resultlist))  {
                                    $count = 1;
                                    foreach ($resultlist as $bill) { $tax_amount = 0;
									?>
                                        <tr>
                                            <td ><?php echo $this->customlib->getPatientSessionPrefixByType('ambulance_call_billing').$bill['id']; ?></td>
                                            <td><?php echo $bill['vehicle_no']; ?></td>
                                            <td><?php echo $bill['vehicle_model']; ?></td>
                                            <td><?php echo $bill['driver']; ?></td>
                                            <td><?php echo $bill['driver_contact']; ?></td>                                            
                                            <td class="text-right"><?php echo $bill['amount']; ?></td>
											<td class="text-right"><?php echo $bill['discount'].' ('.$bill['discount_percentage'].'%)'; ?></td>
                                            <td class="text-right"><?php 										 
												$tax_amount = (($bill['amount'] - $bill['discount'])*$bill['tax_percentage'])/100;											
											echo $tax_amount.' ('.$bill['tax_percentage']."%)"; ?></td>
                                            <td class="text-right"><?php												
												echo $bill['amount']-$bill['discount']+$tax_amount; ?>
											</td>
                                            <td class="text-right"><?php echo $bill['paid_amount']; ?></td>
                                            <td class="text-right"><?php 											
											echo $balance_amount =  amountFormat($bill['net_amount'] - $bill['paid_amount']); ?></td>
                                            <?php 
                                                 if (!empty($fields)) {
                                                    foreach ($fields as $fields_key => $fields_value) {

                                                        $display_field = $bill[$fields_value->name];
                                                        if ($fields_value->type == "link") {
                                                            $display_field = "<a href=" . $bill[$fields_value->name] . " target='_blank'>" . $value->{"$fields_value->name"} . "</a>";

                                                        }
                                                        echo "<td>".$display_field."</td>";
                                                    }
                                                }
                                             ?>
                                            <td class="text-right">
                                                <a href="#" 
                                                   onclick=""
                                                   class="btn btn-default view_payment btn-xs"  data-toggle="tooltip"
                                                   title="<?php echo $this->lang->line('view_payments'); ?>" data-record-id="<?php echo $bill['id']; ?>"  data-module_type="ambulance" >
                                                    <i class="fa fa-money"></i>
                                                </a> 
                                                <a href="#"
                                                   onclick="viewDetail('<?php echo $bill['id'] ?>')"
                                                   class="btn btn-default btn-xs"  data-toggle="tooltip"
                                                   title="<?php echo $this->lang->line('show'); ?>" >
                                                    <i class="fa fa-reorder"></i>
                                                </a>
                                                <button type="button" class="btn btn-primary btn-xs" onclick="payModal('<?php echo $bill["id"]; ?>','<?php echo $balance_amount; ?>')"><?php echo $this->lang->line("pay"); ?></button>
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

<div class="modal fade" id="viewModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
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
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('make_payment') ?></h4>
            </div>
            <form id="payment_form" class="form-horizontal modal_payment" method="POST">
                <div class="modal-body pb0">
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
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
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