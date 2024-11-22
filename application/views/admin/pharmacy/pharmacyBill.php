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
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('pharmacy_bill'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('pharmacy_bill', 'can_add')) {?>  
                            <button type="button" class="btn btn-primary btn-sm generatebill" id="load1" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('generate_bill'); ?></button>
                            <?php }?>
                            <?php if ($this->rbac->hasPrivilege('medicine', 'can_view')) {?>
                                <a href="<?php echo base_url(); ?>admin/pharmacy/search" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('medicines'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">

                        <div class="download_label"><?php echo $this->lang->line('pharmacy_bill'); ?></div>
                     <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('pharmacy_bill'); ?>"> <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                                    <th><?php echo $this->lang->line('case_id'); ?></th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                                    <th><?php echo $this->lang->line('doctor_name'); ?></th>
                                    <?php 
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                                <th><?php echo $fields_value->name; ?></th>
                                                <?php
                                            }  
                                        }
                                    ?> 
                                    <th class="text-right"><?php echo $this->lang->line('discount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('net_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('paid_amount')  . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('refund_amount')  . " " . '(' . $currency_symbol . ')'; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line("balance_amount") . " " . '(' . $currency_symbol . ')'; ?></th>
                                </tr>
                            </thead>
                        </table>
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
                        <a href="#" data-target="#edit_prescription" data-placement="bottom" data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
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

<div class="modal fade" id="billModal" aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
               <form id="bill" accept-charset="utf-8" method="post" onkeydown="return event.key != 'Enter';">
            <div class="modal-header modal-media-header">
                 <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-9">
                        <div class="p-2 select2-full-width">
                            <select class="form-control patient_list_ajax" id="addpatient_id" name='patient_id'>
                            </select> 
                        </div>   
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-1">
                        <div class="p-2 pb-xs-5">     
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) {?>
                                <a data-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient"><i class="fa fa-plus"></i>  <span><?php echo $this->lang->line('new_patient'); ?></span></a>
                            <?php }?> 
                        </div>    
                    </div>    
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
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
                </div><!--./row-->
            </div><!--./modal-header-->            
            <div class="pup-scroll-area">
                <div class="tabinsetbottom pt5">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-4">
                                <label><?php echo $this->lang->line('bill_no'); ?>
                                    <input readonly name="bill_no" id="billno" type="text" class="transparentbg-border"/>
                                    <span class="text-danger"><?php echo form_error('bill_no'); ?></span>
                                </label>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-4">
                                <label><?php echo $this->lang->line('case_id'); ?><input readonly name="case_reference_id" id="case_reference_id" type="text" class="transparentbg-border"/>
                                <span class="text-danger"><?php echo form_error('case_reference_id'); ?></span></label>
                            </div>
                            <div class="col-lg-7 col-md-5 col-sm-4 text-right text-md-left">
                                <label><?php echo $this->lang->line('date'); ?>
                                    <input name="date" type="text"  id="txtDate10" value="" class="transparentbg-border "/>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>            
                <div class="modal-body pb0 ptt10">
                  <div id="load"></div>
                </div><!--./row-->
            </div>
                <div class="modal-footer sticky-footer">
                    <div class="pull-right">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="saveprint" class="btn btn-info printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>                        
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" style="" id="billsave" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div><!--./modal-body-->
    </div>
</div>

<div class="modal fade" id="addPaymentModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('payments'); ?></h4>
            </div>
            <div class="modal-body pt10 pb0 min-h-3">
             
            </div>
        </div>
    </div>
</div>

<script id="medcat-template" type="text/template">
   <?php
foreach ($medicineCategory as $dkey => $med_cat_value) {
    ?>
    <option value='<?php echo $med_cat_value["id"]; ?>'>
        <?php echo $med_cat_value["medicine_category"] ?>
    </option>
    <?php
     }
   ?>
</script>

<script type="text/javascript">
             var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';

                var total_rows=1;
                $(document).on('input paste keyup','.tax_percent,.discount_percent,.qty,.medicine_category,.medicine_name,.batch_no,.price', function(e){ 

                 update_amount($(e.target).closest('div.modal'));
                });
                
           function update_amount(__this){
 
            var discount_percent=__this.find('.discount_percent').val();            
            var tax_percent=__this.find('.tax_percent').val();
            var grandTotal = 0;      
            var discount = 0;      
            var tax = 0;
            var total_tax_amount=0;    
           
            var $tblrows = __this.find(".tblProducts tbody tr");
            $tblrows.each(function (index) {
                    var $tblrow = $(this);  

                    var qty = $tblrow.find(".qty").val();
                    var price = $tblrow.find(".price").val();
                    var tax_percentage = $tblrow.find(".medicine_tax").val();
                    var subTotal = parseInt(qty, 10) * parseFloat(price);                   
                    if (!isNaN(subTotal)) {
                            $tblrow.find('.subtot').val(subTotal.toFixed(2));
							
							discount = isNaN((subTotal * discount_percent / 100 )) ? 0 : (subTotal * discount_percent / 100 );							
                            var tax_amount=(((subTotal-discount)*tax_percentage) / 100 );							
							
                            total_tax_amount += isNaN(tax_amount) ? 0 : tax_amount;                       
                            var stval = parseFloat(subTotal.toFixed(2));
                            grandTotal += isNaN(stval) ? 0 : stval;                     
                    }else{
                        subTotal=0;
                         $tblrow.find('.subtot').val(subTotal.toFixed(2));     
                    }        
            });

                 discount=isNaN((grandTotal * discount_percent / 100 )) ? 0 : (grandTotal * discount_percent / 100 );                 
                var net_amount=((grandTotal-discount)+total_tax_amount);
               __this.find('.total').val(grandTotal.toFixed(2));
               __this.find('.discount').val(discount.toFixed(2));
               __this.find('.tax').val(total_tax_amount.toFixed(2));
               __this.find('.net_amount').val(net_amount.toFixed(2));
               __this.find('.payment_amount').val(net_amount.toFixed(2));
 }        
  
</script>
<script type="text/javascript">
$(document).on('keyup','#prescription_no', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
       getPrescriptionData();
    }
});

$(document).on('click','#search_prescription',function(){
 getPrescriptionData();
});

function getPrescriptionData(){
     var createModal=$('#billModal');
  $.ajax({
        url: '<?php echo base_url(); ?>admin/pharmacy/getPrescriptionById',
        type: "POST",
        data:{'prescription_no':$("input[name=prescription_no]").val()},
        dataType: 'json',
         beforeSend: function() {
             createModal.addClass('modal_loading');
        },
        success: function(res) {    
      if(res.status === 0){
errorMsg(res.msg);
      }else{
         $('#billModal .modal-body').html(res.page);
      $('#case_reference_id').val(res.case_reference_id);
    
      var option = new Option(res.patient_name+" ("+res.patient_id+")", res.patient_id, true, true);
      $("#bill .patient_list_ajax").append(option).trigger('change');
      // manually trigger the `select2:select` event
      $("#bill .patient_list_ajax").trigger({
          type: 'select2:select',
          params: {
              data: res
          }
      });
           total_rows=res.total_rows;
            if(res.total_rows <= 0){
                  errorMsg("No prescription found");
            }

            if(res.total_rows > 0){
              var medicineTable=$("#billModal .modal-body").find('table.tblProducts');
                //=============
              medicineTable.find("tbody tr").each(function() {
                var medicine_category_obj = $(this).find("td select.medicine_category");
                var medicine_obj = $(this).find("td select.medicine_name");
                var batch_obj = $(this).find("td select.batch_no");
                var post_medicine_category_id = $(this).find("td input.post_medicine_category_id").val();
                var post_medicine_id = $(this).find("td input.post_medicine_id").val();
                var post_medicine_batch_detail_id = $(this).find("td input.post_medicine_batch_detail_id").val();
                var post_medicine_sale_price = $(this).find("td input.sale_price").val();
                var post_medicine_quantity = $(this).find("td input.quantity").val();
                var medicine_array = {};
                medicine_array['quantity'] = post_medicine_quantity;
                medicine_array['sale_price'] = post_medicine_sale_price;
                $('.select3').select2();
                $('.filestyle','#billModal').dropify();
                getMedicine(medicine_category_obj,post_medicine_category_id,post_medicine_id);
                getBatchNo(medicine_obj,post_medicine_id,post_medicine_batch_detail_id);
              });
            }
      }     
     
    createModal.removeClass('modal_loading');

        },
           error: function(xhr) { // if error occured
        alert("Error occured.please try again");
              createModal.removeClass('modal_loading'); 
    },
    complete: function() {
            createModal.removeClass('modal_loading');
    }
    });
}

  $(document).on('click','.generatebill',function(){
       var createModal=$('#billModal');
       var $this = $(this);
       $this.button('loading');
       $("#prescription_no").removeAttr("readonly");
       $('#search_prescription').prop("disabled", false);	
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/createBill',
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $this.button('loading');
                            createModal.addClass('modal_loading');
                    },
                    success: function(res) {                       
                        $('#billModal #billno').val(res.bill_no);
                        $('#billModal .modal-body').html(res.page);
                        $('.filestyle','#billModal').dropify();
                        $('.select3').select2();
                        $('#billModal').modal('show');
                        createModal.removeClass('modal_loading');
                    },
                        error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
                        $this.button('reset');
                            createModal.removeClass('modal_loading');
                },
                complete: function() {
                        $this.button('reset');
                        createModal.removeClass('modal_loading');
                }
                });
  }); 

   $(document).ready(function(){
    
        $('#viewModal,#billModal').modal({
          backdrop: 'static',
          keyboard: true, 
          show: false
        });
          $('.datetime').datetimepicker({
             format: datetime_format,
        });
   });
   
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });

    function get_PatienteditDetails(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pharmacy/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#patienteditid').val(res.id);
                    $('#patienteditname').val(res.patient_name);
                }
            }
        });
    }

    $(document).on('select2:select','.medicine_category',function(){       
      getMedicine($(this),$(this).val(),0);
    });

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
            url: '<?php echo base_url() ?>admin/pharmacy/getPharmacyTransaction',
            type: "POST",
            data: {'id': record_id},
            dataType:"JSON",
            beforeSend: function(){

            },          
            success: function (data) {
         
           $('.modal-body',payment_modal).html(data.page);
             $('.filestyle','#addPaymentModal').dropify();
            payment_modal.removeClass('modal_loading');               
           
            },
             error: function () {

             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){

             payment_modal.removeClass('modal_loading'); 
            }
        });
    }

      $(document).on('click','.print_receipt',function(){
      var $this = $(this);
      var record_id=$this.data('recordId')
      $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/pharmacy/printTransaction',
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
          alert("Error occured.please try again");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });

    function getMedicine(med_cat_obj,val,medicine_id){
      var medicine_colomn=med_cat_obj.closest('tr').find('.medicine_name');
        medicine_colomn.html("");    
        $.ajax({
            url: '<?php echo base_url(); ?>admin/pharmacy/get_medicine_name',
            type: "POST",
            data: {medicine_category_id: val},
            dataType: 'json',
             async: false,
              beforeSend: function() {
              medicine_colomn.html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
            },
            success: function (res) {
                var div_data="<option value=''>Select</option>";
                $.each(res, function (i, obj)
                {
                    var sel = "";
                            if (medicine_id == obj.id) {
                                sel = "selected";
                            }
                            div_data += "<option value=" + obj.id + " " + sel + ">" + obj.medicine_name + "</option>";
                });
           
                medicine_colomn.html(div_data);
                medicine_colomn.select2("val", medicine_id);               
            }
        });
} 

 $(document).on('select2:select','.medicine_name',function(){
     getBatchNo($(this),$(this).val(),0);
    });

 function getBatchNo(med_obj,pharmacy_id,batch_id){       
        var batch_no=med_obj.closest('tr').find('.batch_no');      
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/getBatchNoList",
            data: {'pharmacy_id':pharmacy_id,'batch_id':batch_id},
            dataType: 'json',
             async: false,
               beforeSend: function() {
              batch_no.html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
            },
            success: function (res) { 
                var div_data="<option value=''>Select</option>";              
                $.each(res, function (i, obj)
                {
                            var sel = "";
                            if (batch_id == obj.id) {
                                sel = "selected";
                            }
                    div_data += "<option value=" + obj.id + " " + sel + ">" + obj.batch_no + "</option>";
                });
               batch_no.select2();
               batch_no.html(div_data);
               batch_no.select2("val", batch_id);
            }
        });
 }


 $(document).on('select2:select','.batch_no',function(){
    var medicine_details = {};
      getMedicineDetail($(this),$(this).val(),medicine_details);
    });

function getMedicineDetail(batch_obj,medicine_batch_detail_id,medicine_details){
      var current_row =batch_obj.closest('tr');
        $.ajax({
           type: "POST",
            url: base_url + "admin/pharmacy/getExpiryDate",
            data: {'medicine_batch_detail_id': medicine_batch_detail_id},
            dataType: 'JSON',
            asyn:true,
               beforeSend: function(res) {
            
            },
            success: function (res) { 
                    var quantity_remaining = parseInt(res.available_quantity) - parseInt(res.used_quantity);
                  current_row.find('.exp_date').val(res.expiry_date);
                  current_row.find('.available_qty').text(quantity_remaining);
                  current_row.find('.medicine_tax').val(res.tax);
               
                   if($.isEmptyObject(medicine_details)){
                     
                   current_row.find('.available_quantity').val(quantity_remaining);
                   current_row.find('.price').val(res.sale_rate);
                    update_amount(batch_obj.closest('div.modal'));
                    
                    }else{
                       var total_price=medicine_details.sale_price*medicine_details.quantity
                       current_row.find('.available_quantity').val(parseInt(quantity_remaining)+parseInt(medicine_details.quantity));
                       current_row.find('.qty').val(medicine_details.quantity);
                       current_row.find('.price').val(medicine_details.sale_price);
                       current_row.find('.subtot').val(total_price.toFixed(2));
                       
                    }
            }
        });
}

$(document).on('click','.add-record',function(){
var table = document.getElementById("tableID");
        var id = total_rows+1;
        var medcat_template=$("#medcat-template").html();
        var div = "<td><input type='hidden' name='total_rows[]' value='" + id + "'><select class='form-control medicine_category select3' style='width: 100%;' name='medicine_category_id_"+id+"' ><option value='<?php echo set_value('medicine_category_id'); ?>'><?php echo $this->lang->line('select') ?></option>"+medcat_template+"</select></td><td><select class='form-control select3 medicine_name' style='width:100%' name='medicine_name_id_"+id+"'  id='medicine_name" + id + "' ><option value='<?php echo set_value('medicine_name'); ?>'><?php echo $this->lang->line('select') ?></option></select></td><td><select name='batch_no_id_"+id+"' id='batch_no" + id + "' class='form-control batch_no select3' style='width: 100%;'><option value='<?php echo set_value('batch_no'); ?>'><?php echo $this->lang->line('select') ?></option></select></td><td><input type='text' name='expire_date_"+id+"' readonly id='expire_date" + id + "' class='form-control exp_date'></td><td><div class='input-group'><input type='text' name='quantity_"+id+"' id='quantity" + id + "' class='form-control text-right qty'><span class='input-group-addon text-danger available_qty' style='font-size:10pt'  id='totalqty" + id + "'>&nbsp;&nbsp;</span></div><input type='hidden' class='available_quantity' name='available_quantity_"+id+"' id='available_quantity" + id + "'><input type='hidden' name='id[]' id='id" + id + "'></td><td> <input type='text' name='sale_price_"+id+"' id='sale_price" + id + "'  class='form-control text-right price' readonly></td><td><div class=''><div class='input-group'><input type='text' class='form-control right-border-none medicine_tax'  name='tax_1' readonly id='tax0'  autocomplete='off'><span class='input-group-addon'> %</span></div></div></td><td><input type='text' name='amount_"+id+"' id='amount" + id + "' value='0.00' class='form-control text-right subtot' readonly></td>";
        var row =  "<tr id='row" + id + "'>" + div + "<td><button type='button' data-row-id='"+id+"' class='closebtn delete_row'><i class='fa fa-remove'></i></button></td></tr>";
        $('#tableID').append(row);
        $('.select3').select2();
        total_rows++;       
});
 
  $(document).on('click','.delete_row',function(e){
        if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
            var modal_=$(e.target).closest('div.modal');
            var del_row_id=$(this).data('rowId');
            $("#row" + del_row_id).remove();
            update_amount(modal_);
        }        
  });

	$(document).on('click','.editdelete_row',function(e){
        if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
            var modal_=$(e.target).closest('div.modal');
            var del_row_id=$(this).data('rowId');    
            $("#row" + del_row_id).remove();
            update_amount(modal_);
            calculate_refund();
        }
	});

    $(document).on('keyup','.edit_refund_qty',function(e){
        var modal_=$(e.target).closest('div.modal');
        var row_id=$(this).data('rowid');
        var qty=$(this).val();
        var count_qty=0;
        var refund_qty_amount=0;
        var max_quantity=$('#valid_reund_quantity'+row_id).val();
        var refund_amount=$('#payment_refund_amount').val(); 
        var row_amount=$('#sale_price'+row_id).val();   
        update_amount(modal_);
        if(parseInt(max_quantity) < parseInt(qty)){
				errorMsg("<?php echo $this->lang->line('update_quantity_should_be_less_than_previous_quantity'); ?>");
         }else if (parseInt(qty) <= 0) {
                errorMsg("<?php echo $this->lang->line('quantity_must_be_greater_than_zero'); ?>");
         }else{
          calculate_refund();
         }
  });

    function calculate_refund(){
         let net_amount_edit=$('#net_amount').val();
         let prev_paid=$('#payment_paid_amount').val();     
         let prev_refund_amount=$('#prev_refund_amount').val();     
         let refund_amt= (parseFloat(prev_paid-prev_refund_amount) > parseFloat(net_amount_edit) ) ? (parseFloat(prev_paid-prev_refund_amount)- parseFloat(net_amount_edit)) : 0;
        $('#payment_refund_amount').val(refund_amt.toFixed(2));
    }

    function get_percentage(discount_amount){
        var discount_amount=(discount_amount != "") ?discount_amount: 0;
      var total=$('#total').val();
      var  tax=$('#tax').val();
      var discount_percent=0;
      var net_amount=0;     
        discount_percent=((parseInt(discount_amount)/parseInt(total))*100);
        $('#discount_percent').val(discount_percent.toFixed(2));
        net_amount=((parseInt(total)-parseInt(discount_amount))+parseInt(tax));
        $('#net_amount').val(net_amount.toFixed(2));
        $('#payment_amount').val(net_amount.toFixed(2));
	}

    function amount_settlement(net_amount){
      var total=$('#total').val();
      var  tax=$('#tax').val();
      var total=(parseInt(total)+parseInt(tax));     
      var discount_amount=0;
      if(total>net_amount){
          discount_amount=(total-net_amount);
           $('#discount').val(discount_amount.toFixed(2));
      get_percentage(discount_amount);
      net_amount=((parseInt(total)-parseInt(discount_amount))+parseInt(tax));
        $('#net_amount').val(net_amount.toFixed(2));
        $('#payment_amount').val(net_amount.toFixed(2));
      }
     
    }

    $(document).ready(function (e) {
            $('#addPaymentModal').modal({
                backdrop: 'static',
                keyboard: false,
                show:false
            })

 $("form#bill button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

        $("#bill").on('submit', (function (e) {
            e.preventDefault();
            let submit_btn_clicked = $("button[type=submit][clicked=true]",$(this));
            let click_btn_id = submit_btn_clicked.attr('id');       
            var form = $(this);            
            $.ajax({
                url: baseurl +'admin/pharmacy/addBill',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData:false,
                  beforeSend: function() {
                  submit_btn_clicked.button('loading');
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
                        if(click_btn_id == "billsave"){
                       
                        }else if (click_btn_id == "saveprint") {
                          _aftersave(data.insert_id);  
                        }

                        $('#billModal').modal('hide');
                        $('#viewModal').modal('hide');
                        $('.ajaxlist').DataTable().ajax.reload();
                        
                    }
                     submit_btn_clicked.button('reset');
                },
                error: function () {
                 submit_btn_clicked.button('reset');
                },
                complete: function(){
                    submit_btn_clicked.button('reset');
   }
            });
        }));
    });

    $('#billModal').on('hidden.bs.modal', function () {
      $("#addpatient_id").select2("val", "");
      $("#billno").val("");
      $("#date_pharmacy").val("");
      $("#prescription_no").val("");
      $("#case_reference_id").val("");
    });

    $('#viewModal').on('hidden.bs.modal', function () {
     $('#reportdata,#edit_deletebill').html("");
    });

    $('#addPaymentModal').on('hidden.bs.modal', function () {
    table.ajax.reload(null, false);
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
                $('#edit_deletebill').html(data.actions);
                view_modal.removeClass('modal_loading');
            },
        });
    }
    
    $(document).on('click','.print_bill',function(){
            var $print_btn = $(this);
            var record_id=$(this).data('recordId');
            $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/',
            type: "GET",
            data: {'id': record_id,'print':true},
            dataType:"JSON",
            beforeSend: function(){
                $print_btn.button('loading');
            },
            complete: function(){
                $print_btn.button('reset');
            },
            success: function (data) {
                popup(data.page);
                     $print_btn.button('reset');
            },
             error: function () {
                     $print_btn.button('reset');
                }
        });
    });

      function _aftersave(id)
    {
            $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/',
            type: "GET",
            data: {'id': id,'print':true},
            dataType:"JSON",
            beforeSend: function(){
              
            },
            complete: function(){
               
            },
            success: function (data) {
                popup(data.page);                    
            },
             error: function () {                   

                }
        });
    };

    $(document).on('click','.edit_bill',function(){
          var editModal=$('#billModal');
          var record_id=$(this).data('recordId');
           var prescription_no=$(this).data('prescriptionId');

              $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/editBill',
            type: "GET",
            data: {'id': record_id},
            dataType:"JSON",
            beforeSend: function(){

           $('#billModal .modal-body').html("");          
            editModal.addClass('modal_loading');
           },
          
            success: function (data) {
        
            $("#prescription_no").val(prescription_no);
            $('#aaa #billno').val(data.paid_amount);
            $("#prescription_no").attr("readonly","readonly");
            $("#search_prescription").prop("disabled", true);
            $('#txtDate10',$('#billModal')).datetimepicker({
            format: datetime_format,
            });
            $('#txtDate10',$('#billModal')).data("DateTimePicker").date(new Date(data.date));           
            $('#billModal #billno').val(data.bill_no);
            $('#billModal .modal-body').html(data.page);
             $('.select3').select2();
            total_rows=data.total_rows;
            $('#case_reference_id').val(data.case_reference_id);        


            $('<input>').attr({
                type: 'hidden',
                id: 'prev_refund_amount',
                name: 'prev_refund_amount',
                value:data.refund_amount
            }).appendTo('form#bill .modal-body');


            $('<input>').attr({
                type: 'hidden',
                id: 'payment_paid_amount',
                name: 'payment_paid_amount',
                value:data.paid_amount
            }).appendTo('form#bill .modal-body');

            $('#payment_refund_amount',$('#billModal')).val('0.00');            
            var option = new Option(data.patient_name+" ("+data.patient_id+")", data.patient_id, true, true);
            $("#bill .patient_list_ajax").append(option).trigger('change');
            // manually trigger the `select2:select` event
            $("#bill .patient_list_ajax").trigger({
                type: 'select2:select',
                params: {
                    data: data
                }
            });
             $('.filestyle','#billModal').dropify();
               var medicineTable=$("#billModal .modal-body").find('table.tblProducts');
            //=============
            medicineTable.find("tbody tr").each(function() {

            var medicine_category_obj = $(this).find("td select.medicine_category");
            var medicine_obj = $(this).find("td select.medicine_name");
            var batch_obj = $(this).find("td select.batch_no");
            var post_medicine_category_id = $(this).find("td input.post_medicine_category_id").val();
            var post_medicine_id = $(this).find("td input.post_medicine_id").val();
            var post_medicine_batch_detail_id = $(this).find("td input.post_medicine_batch_detail_id").val();
            var post_medicine_sale_price = $(this).find("td input.sale_price").val();
            var post_medicine_quantity = $(this).find("td input.quantity").val();
            var medicine_array = {};
            medicine_array['quantity'] = post_medicine_quantity;
            medicine_array['sale_price'] = post_medicine_sale_price;
             getMedicine(medicine_category_obj,post_medicine_category_id,post_medicine_id);
             getBatchNo(medicine_obj,post_medicine_id,post_medicine_batch_detail_id);
             getMedicineDetail(batch_obj,post_medicine_batch_detail_id,medicine_array);

            });
     $('#billModal').modal('show');
       editModal.removeClass('');
            },
            complete: function(){
             editModal.removeClass('modal_loading');
           }
        });
    });
    
    $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.cheque_div').css("display", "block");
      }else{
        $('.cheque_div').css("display", "none");
      }
    });

$(document).on('select2:select', '#consultant_doctor',function (e) {   
      var reference_name = $("#consultant_doctor option:selected").text();
      $('#doctname').val(reference_name);

});
    function get_Docname(id) {
        $("#standard_charge").html("standard_charge");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctName',
            type: "POST",
            data: {doctor: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#doctname').val(res.name + " " + res.surname);
                } else {

                }
            }
        });
    }

    function multiply(id) {

        var quantity = $('#quantity' + id).val();
        var availquantity = $('#available_quantity' + id).val();
        if (parseInt(quantity) > parseInt(availquantity)) {
            errorMsg('Order quantity should not be greater than available quantity');
        } else {
            
        }
        var sale_price = $('#sale_price' + id).val();
        var amount = quantity * sale_price;
        $('#amount' + id).val(amount);
    }

    function holdModal(modalId) {
       $('#' + modalId).modal('show');
        var expire_date = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
        $('.expire_date').datepicker({
            format: "m/yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
        generateBillNo()
    }
</script>
<script type="text/javascript">
    $(function () {
        $('#easySelectable').easySelectable();
    })
</script>
<script type="text/javascript">
          
                    (function ($) {
                        //selectable html elements
                        $.fn.easySelectable = function (options) {
                            var el = $(this);
                            var options = $.extend({
                                'item': 'li',
                                'state': true,
                                onSelecting: function (el) {
                                },
                                onSelected: function (el) {
                                },
                                onUnSelected: function (el) {
                                }
                            }, options);
                            el.on('dragstart', function (event) {
                                event.preventDefault();
                            });
                            el.off('mouseover');
                            el.addClass('easySelectable');
                            if (options.state) {
                                el.find(options.item).addClass('es-selectable');
                                el.on('mousedown', options.item, function (e) {
                                    $(this).trigger('start_select');
                                    var offset = $(this).offset();
                                    var hasClass = $(this).hasClass('es-selected');
                                    var prev_el = false;
                                    el.on('mouseover', options.item, function (e) {
                                        if (prev_el == $(this).index())
                                            return true;
                                        prev_el = $(this).index();
                                        var hasClass2 = $(this).hasClass('es-selected');
                                        if (!hasClass2) {
                                            $(this).addClass('es-selected').trigger('selected');
                                            el.trigger('selected');
                                            options.onSelecting($(this));
                                            options.onSelected($(this));
                                        } else {
                                            $(this).removeClass('es-selected').trigger('unselected');
                                            el.trigger('unselected');
                                            options.onSelecting($(this))
                                            options.onUnSelected($(this));
                                        }
                                    });
                                    if (!hasClass) {
                                        $(this).addClass('es-selected').trigger('selected');
                                        el.trigger('selected');
                                        options.onSelecting($(this));
                                        options.onSelected($(this));
                                    } else {
                                        $(this).removeClass('es-selected').trigger('unselected');
                                        el.trigger('unselected');
                                        options.onSelecting($(this));
                                        options.onUnSelected($(this));
                                    }
                                    var relativeX = (e.pageX - offset.left);
                                    var relativeY = (e.pageY - offset.top);
                                });
                                $(document).on('mouseup', function () {
                                    el.off('mouseover');
                                });
                            } else {
                                el.off('mousedown');
                            }
                        };
                    })(jQuery);

            function showtextbox(value) {
                if (value != 'direct') {
                    $("#opd_ipd_no").show();
                } else {
                    $("#opd_ipd_no").hide();
                }
            }

$(".modalbtnpatient").click(function(){
    $('#formaddpa').trigger("reset");
    $(".dropify-clear").trigger("click");
});

 $(document).on('click','.delete-record',function(){
    var delete_id=$(this).data('recordId');
    var message = "<?php echo $this->lang->line('are_you_sure_you_want_to_delete_this'); ?>";
   if (confirm(message)) {
       $.ajax({
        url: base_url+'admin/pharmacy/deletePharmacyBill',
        type: "POST",
        data:{'id':delete_id},
        dataType: 'json',
         beforeSend: function() {
         
        },
        success: function(res) {     
       if(res.status == 1){
        window.location.reload(true);
       }else{

       }
        },
           error: function(xhr) { // if error occured
        alert("Error occured.please try again");          
    },
    complete: function() {
        
    }
    });
    } 
});  
</script>
 
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/pharmacy/getpharmacybillDatatable',[],[],100,
            [
            { "sWidth": "100px", "bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-head-center dt-body-right'},
            { "sWidth": "100px",  "aTargets": [ -2,-3 ,-4, -5 ] ,'sClass': 'dt-head-center dt-body-right'},
			 {  "sWidth": "200px","aTargets": [ -6, -7 ] ,'sClass': 'dt-body-center'},             
            {  "sWidth": "50px","aTargets": [ 1 ] ,'sClass': 'dt-body-center'}
            ] );
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
<script type="text/javascript">
    
        $(document).on('submit','#add_partial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var pharmacy_bill_basic_id=$("input[name='pharmacy_bill_basic_id']",'#add_partial_payment').val();
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
                         getPayments(pharmacy_bill_basic_id);
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
            if (confirm('<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>')){
            e.preventDefault();
            var record_id=$(this).data('recordId');         
            var pharmacy_bill_basic_id=$("input[name='pharmacy_bill_basic_id']",'#add_partial_payment').val();
            var btn = $(this);       
            btn.button('loading');
            $.ajax({
                url: base_url+'admin/transaction/deleteByID',
                type: "POST",
                data: {'id':record_id,'pharmacy_bill_basic_id':pharmacy_bill_basic_id},
                dataType: 'JSON',               
                success: function (data) {
                    successMsg(data.message);
                    getPayments(pharmacy_bill_basic_id);
                    btn.button('reset');
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
</script>
<script>   
    
    $(".generatebill").click(function(){		
		 var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'H', 'i' => 'mm','h'=>'hh']) ?>';
		 
        $('#txtDate10').datetimepicker({
         format: datetime_format,
        });
        $('#txtDate10').data("DateTimePicker").date(new Date());
        $('#txtDate10').on('dp.change', dateChanged);
    });
    
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
               format: datetime_format,
               autoclose: true,
               todayHighlight: true
               }).datepicker("update", newdate); 
            //================            
                
            }        
            });
        }
</script>
<?php $this->load->view('admin/patient/patientaddmodal')?>