<div class="content-wrapper">
 <section class="content">
  <div class="row">
   <?php $this->load->view('admin/pharmacy/pharmacyMasters') ?>
   <div class="col-md-10">
    <div class="box box-primary" id="tachelist">
     <div class="box-header ptbnull">
      <h3 class="box-title titlefix"><?php echo $this->lang->line('unit_list'); ?></h3>
      <div class="box-tools pull-right">
	  <?php if ($this->rbac->hasPrivilege('medicine_unit', 'can_add')) { ?>
       <a onclick="addModal()" class="btn btn-primary btn-sm medicine"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_unit'); ?></a>
	  <?php } ?>
      </div>
     </div>
     <div class="box-body">
      <div class="mailbox-controls">
      </div>
      <div class="table-responsive mailbox-messages overflow-visible">
      <div class="download_label"><?php echo $this->lang->line('unit_list'); ?></div>
       <table class="table table-striped table-bordered table-hover example">
        <thead>
         <tr>
          <th><?php echo $this->lang->line('database_id'); ?></th>
          <th><?php echo $this->lang->line('unit_name'); ?></th>
          <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
         </tr>
        </thead>
        <tbody>
         <?php
         if (!empty($unitname)) {
            foreach ($unitname as $value) { ?>
         <tr>
          <td><?php echo $value['id']; ?></td>
          <td><?php echo $value['unit_name']; ?></td>
          <td class="text-right">		  
			<?php if ($this->rbac->hasPrivilege('medicine_unit', 'can_edit')) { ?>
           <a onclick="editmyModal(<?php echo $value['id'] ?>,'<?php echo $value['unit_name']; ?>')" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
            <i class="fa fa-pencil"></i>
           </a>
			<?php } ?>			
		   <?php if ($this->rbac->hasPrivilege('medicine_unit', 'can_delete')) { ?>
           <a class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="deletepharmacyunit('<?php echo $value['id'] ?>')">
            <i class="fa fa-trash"></i>
           </a>
		   <?php } ?>		   
          </td>
         </tr>
            <?php     
                }
            }
        ?>
        </tbody>
       </table>
      </div>
     </div>
     <div class="">
      <div class="mailbox-controls">
      </div>
     </div>
    </div>
   </div>
  </div>
 </section>
</div>

<!-- add multiple data modal -->
<div class="modal fade" id="addmultiplerow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header overflow-hidden">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_unit') ?></h4> 
            </div>            
                <form id="form_add_multiple" action="<?php echo site_url('admin/medicineunit/add_multiple_pharmacyunit') ?>" class="ptt10" method="post" accept-charset="utf-8">
                    <div class="modal-body pt0">    
                        <div class="row">
                            <div class="col-md-12">    
                                <div class="table-responsive overflow-visible mb0">
                                    <table class="table table-striped mb0 table-bordered table-hover tablefull12 tblProducts" id="tableID_vitals">
                                        <thead>
                                            <tr class="font13 white-space-nowrap">
                                                <th width="90%"><?php echo $this->lang->line('unit_name'); ?><small class="req" style="color:red;"> *</small></th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody  id="set_row">                                       
                                        </tbody>
                                    </table>                                
                                    <a class="btn btn-info addplus-xs" onclick="addrow()" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add')?></a>
                                </div>                         
                            </div>
                        </div>
                    </div>        
                        <div class="modal-footer">
                            <div class="pull-right">
                                <button type="submit" id="formadd_multiple_btn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </div>  
                </form>                                                
        </div>
    </div>
</div>
<!-- add multiple data modal -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
 <div class="modal-dialog modal-mid" role="document">
  <div class="modal-content modal-media-content mx-2">
   <div class="modal-header modal-media-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title" id="unit_title"><?php echo $this->lang->line('add_unit'); ?></h4>
   </div>
   <form id="formadd" action="<?php echo site_url('admin/medicineunit/addunit') ?>" id="unit_form" name="unit_form" method="post" accept-charset="utf-8">
    <div class="scroll-area">
     <div class="modal-body pt0 pb0">
      <div class="ptt10">
       <div class="row">
        <div class="col-sm-12">
         <div class="form-group">
          <label for="exampleInputEmail1"><?php echo $this->lang->line('unit_name'); ?></label>
          <small class="req"> *</small>
          <input autofocus="" name="unit_name" id="unit_name" placeholder="" type="text" class="form-control" />
          <input name="id" id="id" type="hidden" />
         </div>
        </div>
       </div>
      </div>
     </div>
     <!--./modal-->
    </div>
    <div class="modal-footer">
     <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
    </div>
   </form>
  </div>
  <!--./row-->
 </div>
</div>

<script>
    function addModal(){
          $('#addmultiplerow').modal('show');
          $('#form_add_multiple').trigger("reset");
          $('#set_row').html('');
          addrow();
    }

      $(document).ready(function (e) {
        $('#formadd').on('submit', (function (e) {
            $("#formaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data){
                    if(data.status == "fail"){
                        var message = "";
                        $.each(data.error, function (index, value){
                            message += value;
                        });
                        errorMsg(message);
                    }else{
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

    function editmyModal(id,unit_name){
        $("#myModal").modal('show');
        $("#unit_title").text('<?php echo $this->lang->line('edit_unit'); ?>');
        $("#unit_name").val(String(unit_name));
        $("#id").val(id);
    }

    function deletepharmacyunit(id){
        delete_recordByIdReload('admin/medicineunit/deletepharmacyunit/'+id, '<?php echo $this->lang->line('delete_confirm'); ?>');
    }
</script>

<script>
var total_rows=0;
addrow();
function addrow(){
    var id = total_rows+1;   
    var div = "<tr id='name_row_"+id+"'><td><input class='form-control' name='id_"+id+"' id='id_"+id+"' value='' type='hidden' /><input type='hidden' name='total_rows[]' value='" + id + "'><input name='name_"+id+"' id='name_"+id+"'  type='text' class='form-control'  /></td><td><button type='button' data-rowid='"+id+"' class='closebtn delete_row'><i class='fa fa-remove'></i></button></td></tr>";
    $('#set_row').append(div);   
    total_rows++;      
}
    
$(document).on('click','.delete_row',function(e){
    if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
        var modal_=$(e.target).closest('div.modal');
        var del_row_id=$(this).data('rowid');
        $("#name_row_" + del_row_id).remove();             
    }        
});

$(document).ready(function (e) {
        $('#form_add_multiple').on('submit', (function (e) {
            $("#formadd_multiple_btn").button('loading');
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
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
                    }else if(data.status==2){

                        errorMsg(data.error);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formadd_multiple_btn").button('reset');
                },
                error: function () {

                }
            });
        }));
    });

 $(document).ready(function (e) {
         $('#myModal,#addmultiplerow').modal({
            backdrop: 'static',
            keyboard: false,
            show:false
        });
      });
</script>