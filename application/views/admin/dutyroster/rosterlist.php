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
      <h3 class="box-title titlefix"><?php echo $this->lang->line("roster_list"); ?></h3>
      <div class="box-tools pull-right">
	  <?php if ($this->rbac->hasPrivilege('roster_list', 'can_add')) {?>
       <a data-toggle="modal" onclick="holdModal('myModal')" id="add_roster_list" class="btn btn-primary btn-sm vital"><i class="fa fa-plus"></i> <?php echo $this->lang->line("add_roster"); ?></a>
	  <?php } ?>
      </div>
     </div><!-- /.box-header -->
     <div class="box-body">
      <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line("roster_list"); ?>">
       <thead>
        <tr>
         <th><?php echo $this->lang->line('shift_name'); ?></th>
         <th><?php echo $this->lang->line('start_date'); ?></th>
         <th><?php echo $this->lang->line('end_date'); ?></th>
         <th><?php echo $this->lang->line('shift_start'); ?></th>
         <th><?php echo $this->lang->line('shift_end'); ?></th>
         <th><?php echo $this->lang->line('shift_hour'); ?></th>
         <th><?php echo $this->lang->line('roster_days'); ?></th>
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
                <div class="form-group">
                    <input type="hidden" id="id" name="id">
                    <label> <?php echo $this->lang->line('shift_name'); ?></label>
                    <small class="req"> *</small>
                    <select class="form-control" id="duty_roster_shift_id" name="duty_roster_shift_id">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach($shift_list as $key=>$value){ ?>
                        <option value="<?php echo $value['id']; ?>"><?php echo $value['shift_name'].' ('.$value['shift_start'] .' - '. $value['shift_end'].')'; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
      
        <div class="row">
          <div class="col-sm-6 col-lg-6 col-md-6">
              <div class="form-group">
                  <label><?php echo $this->lang->line('start_date'); ?></label><small class="req"> *</small>
                  <input name="duty_roster_start_date" id="duty_roster_start_date" type="text" class="form-control date" />
              </div>
          </div>
          <div class="col-sm-6 col-lg-6 col-md-6">
              <div class="form-group">
                  <label><?php echo $this->lang->line('end_date'); ?></label><small class="req"> *</small>
                  <input name="duty_roster_end_date" id="duty_roster_end_date" type="text" class="form-control date" />
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
 
 $("#add_roster_list").click(function(){
     $('#formadd').trigger("reset");
     $('#modal_title').text('<?php echo $this->lang->line('add_roster'); ?>');  
 });
 
 $(document).on('click','.edit_roster_list_modal',function(){
     $('#modal_title').text('');
     $('#modal_title').text('<?php echo $this->lang->line('edit_roster'); ?>');
 });
 
 $(document).ready(function (e) {
         $("#formadd").on('submit', (function (e) {       
             e.preventDefault();
             $("#formaddbtn").button('loading');
             $.ajax({
                 url: '<?php echo base_url(); ?>admin/dutyroster/add_roster_list',
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
                     }else if(data.status == 2){
                         errorMsg(data.error);
                     }else {
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
 
  function delete_roster_list(id) {         
         if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this') ?>")) {
             $.ajax({
                 url: base_url + 'admin/dutyroster/delete_roster_list/' + id,
                  
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
 
 
     $(function() {
         $('.time').datetimepicker({
             format: 'HH:mm:ss'
         });
     });     
 
     function apply_to_all() {
         var standard_charge = $("#standard_charge").val();
         $('input.schedule_charge').val(standard_charge);
     }    
    
     
    $(document).on('click','.edit_record',function(){
        var record_id=$(this).data('record_id');
        var btn = $(this);
 
        $.ajax({
             url: base_url+'admin/dutyroster/getRosterListDetails',
             type: "POST",
             data: {record_id: record_id},
             dataType: 'json',
             beforeSend: function(){
                  btn.button('loading');
             },
             success: function (data) {
                if (data.status == 0) {                     
                     errorMsg(message);
                 } else {               
                     $('#id').val(data.result.id);
                     $('#duty_roster_shift_id').val(data.result.duty_roster_shift_id);
                     $('#duty_roster_start_date').val(data.result.roster_start_date);
                     $('#duty_roster_end_date').val(data.result.roster_end_date);
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
         initDatatable('ajaxlist','admin/dutyroster/getRosterListDatatable',{},[],100,[{"aTargets": [ -1,-1 ] ,'sClass': 'dt-body-right dt-head-right'}]);        
     });
 } ( jQuery ) )
</script>
<!-- //========datatable end===== -->