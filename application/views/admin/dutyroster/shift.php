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
      <h3 class="box-title titlefix"><?php echo $this->lang->line('shift'); ?></h3>
      <div class="box-tools pull-right">
	   <?php if ($this->rbac->hasPrivilege('roster_shift', 'can_add')) {?>
       <a data-toggle="modal" onclick="holdModal('myModal')" id="add_shift" class="btn btn-primary btn-sm vital"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_shift'); ?></a>
	   <?php } ?>
      </div>
     </div><!-- /.box-header -->
     <div class="box-body">
      <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('shift'); ?>">
       <thead>
        <tr>
         <th><?php echo $this->lang->line('shift_name'); ?></th>
         <th><?php echo $this->lang->line('shift_start'); ?></th>
         <th><?php echo $this->lang->line('shift_end'); ?></th>
         <th><?php echo $this->lang->line('shift_hour'); ?></th>
         <th class="text-right noExport "><?php echo $this->lang->line('action'); ?></th>
        </tr>
       </thead>
       <tbody>
       </tbody>
      </table>
     </div>
    </div>
   </div>
  </div>
 </section>
</div>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
 <div class="modal-dialog modal-mid" role="document">
  <div class="modal-content modal-media-content mx-2">
   <div class="modal-header modal-media-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">
     <div id="modal_title"></div>
    </h4>
   </div>
   <form id="formadd" accept-charset="utf-8" method="post" class="ptt10">
    <div class="modal-body pt0 pb0">
      <div class="row">
       <div class="col-sm-12">
        <input type="hidden" name="shift_id" id="shift_id" class="form-control">
        <div class="form-group">
         <label> <?php echo $this->lang->line('shift_name'); ?></label>
         <small class="req"> *</small>
         <input type="text" name="shift_name" id="shift_name" class="form-control">
         <span class="text-danger"><?php echo form_error('shift_name'); ?></span>
        </div>
       </div>
      </div>
      
       <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6">
            <div class="form-group">
                <label><?php echo $this->lang->line('shift_start'); ?> </label><small class="req"> *</small>
                <input name="shift_start" id="shift_start" type="text" class="form-control time valid" />
                <span class="text-danger"><?php echo form_error('shift_start'); ?></span>
            </div>    
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
            <div class="form-group"> 
                <label><?php echo $this->lang->line('shift_end'); ?></label><small class="req"> *</small>
                <input name="shift_end" id="shift_end" type="text" class="form-control time valid" />
                <span class="text-danger"><?php echo form_error('shift_end'); ?></span>
            </div>    
        </div>
     </div>
    </div>
    <div class="modal-footer">
     <div class="pull-right">
      <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
     </div>
    </div>
   </form>
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
 
$("#add_shift").click(function(){
	$('#formadd').trigger("reset");
	$('#modal_title').text('<?php echo $this->lang->line('add_shift'); ?>'); 
});
 
$(document).on('click','.edit_shift_modal',function(){
	$('#modal_title').text('');
	$('#modal_title').text('<?php echo $this->lang->line('edit_shift'); ?>');
});
 
$(document).ready(function (e) {
     $("#formadd").on('submit', (function (e) {       
         e.preventDefault();
         $("#formaddbtn").button('loading');
         $.ajax({
             url: '<?php echo base_url(); ?>admin/dutyroster/add_shift',
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
                 } else if(data.status == 2){
                    errorMsg(data.error);
                 }else{                    
                    successMsg(data.message);
                    window.location.reload(true);
                 }                 
             },
             error: function () {
             },
             complete: function(){
                $("#formaddbtn").button('reset');
            }
         });
     }));
 });
 
 function delete_shift(id) {         
     if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this') ?>")) {
         $.ajax({
             url: base_url + 'admin/dutyroster/delete_shift/' + id,
              
             type: 'POST',
             dataType: "json",
             success: function (res) {
                 if (res.status == "fail") {
                     errorMsg(res.message);
                 } else {
                     successMsg("<?php echo $this->lang->line('delete_message') ?>");                        
                     window.location.reload(true);
                 }
             }
         })
     }
 } 
 
 function apply_to_all() {
     var standard_charge = $("#standard_charge").val();
     $('input.schedule_charge').val(standard_charge);
 } 
    
  $(document).on('click','.edit_record',function(){
  var shift_id=$(this).data('recordId');
  var btn = $(this);
 
    $.ajax({
         url: base_url+'admin/dutyroster/getShiftDetails',
         type: "POST",
         data: {shift_id: shift_id},
         dataType: 'json',
         beforeSend: function(){
              btn.button('loading');
         },
         success: function (data) {
                  if (data.status == 0) {                     
                     errorMsg(message);
                 } else {               
                 $('#shift_id').val(data.result.id);
                 $('#shift_name').val(data.result.shift_name);
                 $('#shift_start').val(data.result.shift_start);
                 $('#shift_end').val(data.result.shift_end);                 
                 $('#myModal').modal('show');
                                     }
                          btn.button('reset');
                     }, 
                     error: function () {
                        btn.button('reset');
                         },
                         complete: function(){
                          btn.button('reset');
            }
                 });                    
                  });    
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
 ( function ( $ ) {
     'use strict';
     $(document).ready(function () {        
         initDatatable('ajaxlist','admin/dutyroster/getShiftDatatable',{},[],100,[{"aTargets": [ -1,-1 ] ,'sClass': 'dt-body-right dt-head-right'}]);        
     });
 } ( jQuery ) )
</script>
<!-- //========datatable end===== -->


<script type="text/javascript">
    // function


</script>