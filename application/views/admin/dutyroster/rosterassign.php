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
      <h3 class="box-title titlefix"><?php echo $this->lang->line('assign_roster'); ?></h3>
      <div class="box-tools pull-right">
	  <?php if ($this->rbac->hasPrivilege('roster_assign', 'can_add')) {?>
       <a data-toggle="modal" onclick="holdModal('myModal')" id="add_assign_roster" class="btn btn-primary btn-sm vital"><i class="fa fa-plus"></i> <?php echo $this->lang->line('assign_roster'); ?></a>
	  <?php } ?>
      </div>
     </div><!-- /.box-header -->
     <div class="box-body">
      <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('assign_roster'); ?>">
       <thead>
        <tr>
		 <th><?php echo $this->lang->line('staff'); ?></th>
         <th><?php echo $this->lang->line('floor'); ?></th>
         <th><?php echo $this->lang->line('department'); ?></th>         
         <th><?php echo $this->lang->line('roster'); ?></th>
         <th><?php echo $this->lang->line('start_date'); ?> - <?php echo $this->lang->line('end_date'); ?></th>
         <th><?php echo $this->lang->line('shift_start'); ?> - <?php echo $this->lang->line('shift_end'); ?></th>
         <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
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
     <div class="form-group">
      <div class="row">
		<div class="col-sm-12">
			<div class="form-group">
			<label> <?php echo $this->lang->line('shift'); ?></label>
				<ul class="stepradiolist row gy-0">
				<?php foreach($shift_list as $key => $shift_value){ ?>
				<li class="col-sm-4"><label><input type="radio" id="radio_<?php echo $shift_value['id']; ?>" name="shift_id" value="<?php echo $shift_value['id']; ?>" /><div class="stepimage"><?php echo $shift_value['shift_name']; ?><br><?php echo $this->customlib->getHospitalTime_Format($shift_value['shift_start']); ?> - <?php echo $this->customlib->getHospitalTime_Format($shift_value['shift_end']); ?></div></label></li>
				<?php } ?>
				</ul>
			</div>
       </div>	  
	  
	  <div class="col-sm-12">
        <div class="form-group">
         <label> <?php echo $this->lang->line('shift_date'); ?></label>
         <small class="req"> *</small>
         <select class="form-control" id="duty_roster_list_id" name="duty_roster_list_id">
          <option value=""><?php echo $this->lang->line('select'); ?> </option>         
           
         </select>
        </div>
       </div>
       
       <div class="col-sm-12">
        <div class="form-group">
         <label> <?php echo $this->lang->line('staff'); ?></label>
         <small class="req"> *</small>
          <div class="p-2 select2-full-width">
          <select class="form-control select2" id="duty_roster_staff" name="duty_roster_staff" >
          <option value=""><?php echo $this->lang->line('select'); ?> </option>
          <?php foreach($resultlist as $key=>$value){ ?>
          <option value="<?php echo $value['id'];?>"><?php echo $value['name']." ".$value['surname']." (".$value['employee_id'].")";?></option>
          <?php } ?>
         </select>
         </div>
        </div>
       </div>
       
	   <div class="col-sm-6">
        <input type="hidden" name="edit_code" id="edit_code">
        <div class="form-group">
         <label> <?php echo $this->lang->line('floor'); ?></label>         
         <select class="form-control" id="duty_roster_floor" name="duty_roster_floor">
          <option value=""><?php echo $this->lang->line('select'); ?></option>
          <?php foreach($floor as $key=>$value){ ?>
          <option value="<?php echo $value['id'];?>"><?php echo $value['name'];?></option>
          <?php } ?>
         </select>
         </select>
        </div>
       </div>
	   
       <div class="col-sm-6">
        <div class="form-group">
         <label> <?php echo $this->lang->line('department'); ?></label>        
         <select class="form-control" id="duty_roster_department" name="duty_roster_department">
          <option value=""><?php echo $this->lang->line('select'); ?></option>
          <?php foreach($department_list as $key=>$department_list_value){ ?>
          <option value="<?php echo $department_list_value['id'];?>"><?php echo $department_list_value['department_name'];?></option>
          <?php } ?>
         </select>
         </select>
        </div>
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
	$(document).ready(function(){ 
		$("input[name=shift_id]").change(function() {
			var shift = $(this).val();
			$('#duty_roster_list_id').html("");
			var base_url = '<?php echo base_url() ?>';
			var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
			$.ajax({
				type: "GET",
				url: base_url + "admin/dutyroster/getrosterlistbyshift",
				data: {'shift': shift},
				dataType: "json",
				success: function (data) {
					$.each(data, function (i, obj)
					{
						 
						div_data += "<option value=" + obj.id + " > " + obj.duty_roster_start_date + " - " + obj.duty_roster_end_date + "</option>";
						
					});
					$('#duty_roster_list_id').append(div_data);
				}
			});  
		}); 
	});
	
	function getrosterlistbyshift(shift_id,duty_roster_list_id) {
		if (shift_id != "" ) {
			$('#duty_roster_list_id').html("");
			var base_url = '<?php echo base_url() ?>';
			var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
			$.ajax({
				type: "GET",
				url: base_url + "admin/dutyroster/getrosterlistbyshift",
				data: {'shift': shift_id},
				dataType: "json",
				success: function (data) {
					$.each(data, function (i, obj)
					{
						var sel = "";
						if (duty_roster_list_id == obj.id) {
							sel = "selected";
						} 
						div_data += "<option value=" + obj.id + " " + sel + "> Shift Date " + obj.duty_roster_start_date + " - " + obj.duty_roster_end_date + " (Shift Time - " + obj.shift_start + " - " + obj.shift_end + ")</option>";
						
					});
					$('#duty_roster_list_id').append(div_data);
				}
			});
		}
	}
</script>		  
		  
<script type="text/javascript">
    $(document).ready(function(){ 
		$("input[name=optradio]").change(function() {
			var test = $(this).val();
			$(".show-hide").hide();
			$("#"+test).show();
		}); 
	});
</script>

<script type="text/javascript">

	$(function () {       
        $('.select2').select2();    
    })
	
// $(document).ready(function (e) {    
    // $(".select2").select2({
        // placeholder: 'Select',
        // allowClear: false,
        // minimumResultsForSearch: 2
    // });
    
// });

function holdModal(modalId) {
     $('#' + modalId).modal({
         backdrop: 'static',
         keyboard: false,
         show: true
     });
 }
 
 $("#add_assign_roster").click(function(){
     $('#formadd').trigger("reset");
     $('#duty_roster_staff').trigger("change");
     $('#modal_title').text('<?php echo $this->lang->line('assign_roster'); ?>'); 
 });
 
 $(document).on('click','.edit_assign_roster',function(){
     $('#modal_title').text('');
     $('#formadd').trigger("reset");
     $('#duty_roster_staff').trigger("change");
     $('#modal_title').text('<?php echo $this->lang->line('edit'); ?>'); 
 }); 
 
 $(document).ready(function (e) {
         $("#formadd").on('submit', (function (e) {       
             e.preventDefault();
             $.ajax({
                 url: '<?php echo base_url(); ?>admin/dutyroster/add_roster_assign',
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
                     } else {
                         successMsg(data.message);
                       window.location.reload(true);
                     }
                     $("#formaddbtn").button('reset');
                 },
                 error: function () {
                 }
             });
         }));
     });
 
  function delete_roster_assign(code) {         
         if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this') ?>")) {
             $.ajax({
                 url: base_url + 'admin/dutyroster/delete_roster_assign/' + code,
                  
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
 
  $(document).on('click','.edit_assign_roster',function(){
      var record_id=$(this).data('record_id');
      var btn = $(this);
     
    $.ajax({
             url: base_url+'admin/dutyroster/edit_assign_roster',
             type: "POST",
             data: {id: record_id},
             dataType: 'json',
             beforeSend: function(){
                  btn.button('loading');
             },
             success: function (data) {
                      if (data.status == 0) {                     
                         errorMsg(message);
                     } else {               
                         $('#edit_code').val(data.result.code);
                         $('#duty_roster_floor').val(data.result.floor_id);
                         $('#duty_roster_department').val(data.result.department_id);						 
						 $("#radio_"+data.result.duty_roster_shift_id).attr('checked', 'checked'); 						 
						 getrosterlistbyshift(data.result.duty_roster_shift_id,data.result.duty_roster_list_id);
						 $('#duty_roster_staff').select2("val", data.result.staff_id);
						
                         $('#duty_roster_list_id').val(data.result.duty_roster_list_id);
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
         initDatatable('ajaxlist','admin/dutyroster/getdutyRoster_assignList',{},[],100,[{"aTargets": [ -1,-1 ] ,'sClass': 'dt-body-right dt-head-right'}]);        
     });
 } ( jQuery ) )
</script>
<!-- //========datatable end===== -->