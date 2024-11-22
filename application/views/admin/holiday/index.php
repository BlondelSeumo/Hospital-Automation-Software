<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
 
 <section class="content">
  <div class="box box-primary">
   <div class="box-header with-border">
    <h3 class="box-title">  <?php echo $this->lang->line('annual_calendar'); ?></h3>
		<div class="box-tools pull-right">
			<?php if ($this->rbac->hasPrivilege('annual_calendar', 'can_add')) {?>
			<button type="button" onclick="add_holiday()" class="btn btn-sm btn-primary" data-toggle="tooltip"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></button>  
			<?php } ?>
          </div>
   </div>
   <!-- Seaching section Start-->
   <form class="assign_teacher_form" action="<?php echo base_url(); ?>admin/holiday" method="post" enctype="multipart/form-data">
    <div class="box-body">
     <div class="row">
      <div class="col-md-12">
       <?php if ($this->session->flashdata('msg')) { ?>
       <?php  echo $this->session->flashdata('msg');
              $this->session->unset_userdata('msg');
        ?>
       <?php } ?>
       <?php echo $this->customlib->getCSRF(); ?>
      </div>
      <div class="col-md-3 col-lg-3 col-sm-6">
       <div class="form-group">
        <label><?php echo $this->lang->line('type'); ?>   </label><small class="req"> *</small>
        <select autofocus="" id="search_holiday_type" name="search_holiday_type" class="form-control">
         <option value=""><?php echo $this->lang->line('select'); ?> </option>
         <option value="1" <?php if($search_holiday_type == 1){ echo  "selected";} ?> ><?php echo $this->lang->line('holiday'); ?></option>
         <option value="2" <?php if($search_holiday_type == 2){ echo  "selected";} ?> ><?php echo $this->lang->line('activity'); ?></option>
         <option value="3" <?php if($search_holiday_type == 3){ echo  "selected";} ?> ><?php echo $this->lang->line('vacation'); ?></option>
        </select>
        <span class="class_id_error text-danger"><?php echo form_error('search_holiday_type'); ?></span>
       </div>
      </div>
     </div>
     <button type="submit" id="search_filter" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
    </div>
   </form>
   <!-- Seaching section End-->
   <div class="row">
    <div class="col-md-12">
     <div class="box-header with-border">
      <h3 class="box-title"> <?php echo $this->lang->line('holiday_list'); ?></h3>
      <div class="box-tools pull-right">
       
      </div>
     </div>
     <div class="box-body table-responsive overflow-visible">
      <div class="download_label"> <?php echo $this->lang->line('holiday_list'); ?></div>
      <div>
       <table class="table table-hover table-striped table-bordered example">
        <thead>
         <tr>
          <th width="15%"><?php echo $this->lang->line('date'); ?></th>
          <th><?php echo $this->lang->line('type'); ?></th>          
          <th><?php echo $this->lang->line('description'); ?></th>
          <th><?php echo $this->lang->line('created_by'); ?></th>
          <th width="5%"><?php echo $this->lang->line('front_site'); ?></th>
          <th class="text-right noExport" width="10%"><?php echo $this->lang->line('action'); ?></th>
         </tr>
        </thead>
        <tbody>
        <?php
        foreach ($holidaylist as $value) {     

            $from_date=date($this->customlib->YYYYMMDDTodateFormat($value['from_date']));
            $to_date=date($this->customlib->YYYYMMDDTodateFormat($value['to_date']));            
        ?>
         <tr>
          <td>
			<?php if($value['holiday_type']==3){
				echo date($this->customlib->YYYYMMDDTodateFormat($value['from_date'])) . ' '. $this->lang->line('to') .' '. date($this->customlib->YYYYMMDDTodateFormat($value['to_date']));
            }else{
				echo date($this->customlib->YYYYMMDDTodateFormat($value['from_date']));
            }  ?></td>
          <td><?php 
			if($value['holiday_type']==1){
				echo $this->lang->line('holiday');
			}else if($value['holiday_type']==2){
				echo $this->lang->line('activity');
			}else if($value['holiday_type']==3){
				echo $this->lang->line('vacation');
			}  ?></td>
          <td><?php echo $value['description']; ?></td>
          <td width="10%"> 
			<?php 		   
				if($superadmin_restriction == 'disabled' && $value['role_id'] == 7){
					echo ''; 
				}else{
					echo $value['name']." ".$value['surname']." (".$value['employee_id'].")"; 
				}		  
			?>
			</td>	  
		  <td><?php 
			if($value['front_site']==1){
				echo $this->lang->line('yes');			
			}else {
				echo $this->lang->line('no');
			}  ?></td>
			
          <td class="pull-right" > 
			<?php if ($this->rbac->hasPrivilege('annual_calendar', 'can_edit')) {?>
			
			<a onclick="get('<?php echo $value['id']; ?>')" class="btn btn-default btn-xs" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('edit') ?>"><i class="fa fa-pencil"></i> </a>
			
			<?php } if ($this->rbac->hasPrivilege('annual_calendar', 'can_delete')) {?>
			
			<a onclick="delete_holiday('<?php echo $value['id']; ?>');" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('delete') ?>" class="btn btn-default btn-xs"><i class="fa fa-trash"></i> </a>
			
			<?php } ?>
          </td>
         </tr>
         <?php } ?>
        </tbody>
       </table>
      </div>
     </div>
    </div>
   </div>
  </div>
 </section>
</div>

<div class="modal fade" id="holiday_modal" tabindex="-1" role="dialog" aria-labelledby="evaluation" style="padding-left: 0 !important">
 <div class="modal-dialog " role="document">
  <div class="modal-content modal-media-content">
   <div class="modal-header modal-media-header">
    <button type="button" class="close" data-dismiss="modal" onclick="refresh()">&times;</button>
    <h4 class="box-title" id="title"></h4>
   </div>
   <form role="form" id="add_holiday" method="post" enctype="multipart/form-data" action="">
    <div class="modal-body pb0">
     <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
       <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
         <div class="form-group">
          <input type="hidden" name="id" id="id">
          <label><?php echo $this->lang->line('type'); ?></label><small class="req"> *</small>	  
			
			<ul class="stepradiolist row gy-0">
				<li class="col-sm-4"><label><input type="radio" id="radio_1" name="holiday_type" value="1" onchange="showDate(this.value)"/><div class="stepimage"><?php echo $this->lang->line('holiday'); ?></div></label></li>
				<li class="col-sm-4"><label><input type="radio" id="radio_2" name="holiday_type" value="2" onchange="showDate(this.value)"/><div class="stepimage"><?php echo $this->lang->line('activity'); ?></div></label></li>
				<li class="col-sm-4"><label><input type="radio" id="radio_3" name="holiday_type" value="3" onchange="showDate(this.value)"/><div class="stepimage"><?php echo $this->lang->line('vacation'); ?></div></label></li>				 
			</ul>				
				
         </div>
        </div>
        <div class="col-md-4 col-lg-4 col-sm-6" id="date_one">
         <div class="form-group">
          <label id="date_text"><?php echo $this->lang->line('from_date'); ?> </label><small class="req"> *</small>
          <input id="from_date" name="from_date" placeholder="" type="text" class="form-control date" value="" />
         </div>
        </div>
        <div class="col-md-4 col-lg-4 col-sm-6" id="date_two">
         <div class="form-group">
          <label><?php echo $this->lang->line('to_date'); ?></label><small class="req"> *</small>
          <input id="to_date" name="to_date" placeholder="" type="text" class="form-control date" value="" />
         </div>
        </div>
        <div class="col-md-12 col-lg-12 col-sm-12">
         <div class="form-group">
          <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label><small class="req"> *</small>	  
          <textarea rows="5" id="description" name="description" placeholder="" type="text" class="form-control"></textarea>
         </div>
        </div>
        <div class="col-md-12 col-lg-12 col-sm-12">
         <div class="form-group">
          <label for="exampleInputEmail1"><?php echo $this->lang->line('front_site'); ?></label>	  
			<div class="material-switch ">
				<input id="front_site" name="front_site" type="checkbox" class="chk" value="checked" />
				<label for="front_site" class="label-success"></label>
            </div>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
    <div class="box-footer">
     <div class="pull-right paddA10">
      <button class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait" value=""><?php echo $this->lang->line('save'); ?></button>
     </div>
   </form>
  </div>
 </div>
</div>
</div>

<script type="text/javascript">
	
	 
	
  
 $("#add_holiday").on('submit', (function (e) {
      e.preventDefault();
      var $this = $(this).find("button[type=submit]:focus");
      $.ajax({
          
          url: "<?php echo site_url("admin/holiday/add") ?>",
          type: "POST",
          data: new FormData(this),
          dataType: 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function () {
              $this.button('loading'); 
          },
          success: function (res)
          {
             if (res.status == "fail") {                
                  var message = "";
                  $.each(res.error, function (index, value) {
                      message += value;
                  });
                  errorMsg(message);
  
              } else {
                  successMsg(res.message);
                  window.location.reload(true);
              }
          },
          error: function (xhr) { // if error occured
              alert("Error occured.please try again");
              $this.button('reset');
          },
          complete: function () {
              $this.button('reset');
          }
      });
 })); 
 
 function showDate(val){
     $("#from_date").val('');
     $("#to_date").val('');
     if(val==""){
         $("#date_one").hide();
         $("#date_two").hide();
     }else if(val==1 || val==2){
         $("#date_one").show();
         $("#date_two").hide();
         $("#date_text").html('<?php echo $this->lang->line('date'); ?>');
     }else if(val==3){
         $("#date_one").show();
         $("#date_two").show();
         $("#date_text").html('<?php echo $this->lang->line('from_date'); ?>');
     }
 }
 
 function add_holiday() {
      $('#add_holiday').trigger("reset");
      $('input[name="holiday_type"]').prop('checked', false);
      $("#front_site").attr("checked" , false );
      $('#title').html('<?php echo $this->lang->line('add_holiday'); ?>');
      $('#holiday_modal').modal({
          backdrop: 'static',
          keyboard: false,
          show: true
      });
      showDate('');
  }
 
		function get(id) {
			 
			 
			var base_url = '<?php echo base_url() ?>';
			$('#title').html('<?php echo $this->lang->line('edit_holiday'); ?>');
				$('#holiday_modal').modal({
							backdrop: 'static',
							keyboard: false,
							show: true
				});		 
				  
			$.ajax({
				url: base_url+'admin/holiday/getholiday',
				type: "POST",
				data: {id: id},
				dataType: 'json',
				beforeSend: function(){
					 
				},
				success: function (data) {
					if (data.status == 0) {                     
                         errorMsg(message);
                    } else { 
						showDate(data.result.holiday_type);
                        $('#from_date').val(data.result.from_date);
                        $('#id').val(data.result.id); 
                        $('#description').val(data.result.description); 
                        $('#to_date').val(data.result.to_date);
						 
						if(data.result.front_site == '1'){
							$('#front_site').attr('checked', 'checked'); 
                        }                         
						
						$("#radio_"+data.result.holiday_type).attr('checked', 'checked'); 
						$('#myModal').modal('show');						 
					}					 
				}, 
				error: function () {
					 
				},
				complete: function(){
                    
                }
			});			  
		}
 
 function delete_holiday(id) {
      var confirmation = confirm('<?php echo $this->lang->line('delete_confirm') ?>');
      if (confirmation == true) {
          $.ajax({
              url: "<?php echo site_url("admin/holiday/delete_holiday") ?>",
              type: "POST",
              data: {'id': id},
              dataType: "json",
              success: function (res)
              {
                  if (res.status == 0) {
                      errorMsg(res.error);
                  } else {
                      successMsg(res.success);
                      window.location.reload(true);
                  }
              }
          });
      }
  }
 
</script>


