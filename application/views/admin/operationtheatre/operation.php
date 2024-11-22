<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="box border0">
                    <ul class="tablists">				
												
						<?php if ($this->rbac->hasPrivilege('operation', 'can_view')) {?>
							<li><a href="<?php echo site_url('admin/operationtheatre') ?>" class="<?php echo set_sidebar_Submenu('admin/operationtheatre/index'); ?>"><?php echo $this->lang->line('operation'); ?></a></li>							
						<?php } if ($this->rbac->hasPrivilege('operation_category', 'can_view')) { ?>
							<li><a href="<?php echo site_url('admin/operationtheatre/category') ?>" class="<?php echo set_sidebar_Submenu('admin/operationtheatre/category'); ?>" ><?php echo $this->lang->line('operation_category'); ?></a></li>							
						<?php } ?>
						
                    </ul>    
                </div>
            </div><!--./col-md-3-->
			<?php if ($this->rbac->hasPrivilege('operation', 'can_view')) { ?>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('operation_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('operation', 'can_add')) {?>
                            <a onclick="addModal()" class="btn btn-primary btn-sm addoperation"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_operation'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('operation_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible">
							
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('category'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
if (!empty($operation_list)) {                                        
    foreach ($operation_list as $key => $value) {
        ?>
                                            <tr>
                                                <td class="mailbox-name"> <?php echo $value['operation']; ?></td>
                                                <td class="mailbox-name"> <?php echo $value['category']; ?></td>
                                                <td class="mailbox-date pull-right">
                                                    <?php if ($this->rbac->hasPrivilege('operation', 'can_edit')) {?>
                                                        <a data-target="#editmyModal" onclick="get(<?php echo $value['id']; ?>)" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php }?>
                                                    <?php if ($this->rbac->hasPrivilege('operation', 'can_delete')) {?>
                                                        <a  class="btn btn-default btn-xs" data-toggle="tooltip" title="" onclick="delete_operation('<?php echo $value['id']; ?>')" data-original-title="<?php echo $this->lang->line('delete'); ?>">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php }?>
                                                </td>

                                            </tr>
                                            <?php
}
}
?>
                                </tbody>
                            </table><!-- /.table -->							
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
			<?php } ?>
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- add multiple data modal -->
<div class="modal fade" id="addmultiplerow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header overflow-hidden">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_operation') ;?></h4> 
            </div>
           
                <form id="form_add_multiple" action="<?php echo site_url('admin/operationtheatre/add_multiple_addoperation') ?>" class="ptt10" method="post" accept-charset="utf-8">
                   <div class="modal-body pt0">    
                        <div class="row">
                            <div class="col-md-12">    
                                <div class="table-responsive overflow-visible mb0">
                                    <table class="table table-striped mb0 table-bordered table-hover tablefull12 tblProducts" id="tableID_vitals">
                                        <thead>
                                            <tr class="font13 white-space-nowrap">
                                                <th width="30"><?php echo $this->lang->line('operation_name'); ?><small class="req" style="color:red;"> *</small></th>
                                                <th width="60%"><?php echo $this->lang->line('category'); ?></th>
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

<div class="modal fade" id="editmyModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_operation'); ?></h4>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/operationtheatre/edit') ?>" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data" class="ptt10">
                <div class="modal-body pt0 pb0">
                    <div class="form-group">
                            <label for="pwd"><?php echo $this->lang->line('operation_name'); ?></label> <small class="req"> *</small>
                            <input class="form-control" id="edit_operation_name" name="edit_operation_name" value="<?php echo set_value('edit_operation_name'); ?>"/>
                             <span class="text-danger"><?php echo form_error('edit_operation_name'); ?></span>
                            <input class="form-control" id="id" name="id" value="" type="hidden" />                           
                        </div>
                        <div class="form-group">
                            <label for="pwd"><?php echo $this->lang->line('category'); ?></label> <small class="req"> *</small>
                            <select class="form-control select2 " id="edit_category" name="edit_category" style="width:100%">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach($category_list as $category){ ?>
                                         <option value="<?php echo $category['id']; ?>"><?php echo $category['category']; ?></option>
                                  <?php  } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('category'); ?></span>
                        </div>                        
                </div><!--./modal-body-->
                <div class="modal-footer">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="editformaddbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div><!--./row-->
    </div>
</div>

<script type="text/javascript">    
    $(document).ready(function (e) {
        $('.select2').select2();
    });
</script>
<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });

        $('#myModal,#editmyModal').modal({
            backdrop: 'static',
            keyboard: false,
            show:false
        });
    });
</script>
<script>
    $(document).ready(function (e) {
        $('#formadd').on('submit', (function (e) {
            e.preventDefault();
            $("#formaddbtn").button('loading');
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

    function get(id) {      
        $('#editmyModal').modal('show');
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/operationtheatre/getoperation/' + id,
            success: function (result) {
                $('#id').val(result.id);
                $('#edit_operation_name').val(result.operation);
                 $("#edit_category").select2("val", result.category_id);               
            }
        });
    }

    $(document).ready(function (e) {
        $('#editformadd').on('submit', (function (e) {
            e.preventDefault();
            $("#editformaddbtn").button('loading');
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
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#editformaddbtn").button('reset');
                },
                error: function () {

                }
            });
        }));
    });

$('#myModal').on('hidden.bs.modal', function(e) {     
    $("#category",$(this)).select2("val", "");
    $("#formadd",$(this)).find('input:text').val('');   
});
</script>
<script>
     function delete_operation(id) {
              
              if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
                    $.ajax({
                        url: '<?php echo base_url()."admin/operationtheatre/deleteoperation"; ?>',
                        data:{id:id},
                        type:"post",
                        success: function (res) {
                           toastr.success(
                            "<?php echo $this->lang->line('record_deleted') ?>",
                            '',
                            {
                              timeOut: 1000,
                              fadeOut: 1000,
                              onHidden: function () {
                                 window.location.reload(true);
                                }
                            }
                          );  
                        }
                    });
                }
            }
</script>
<script>
var category_option="";
<?php 
foreach($category_list as $category){ 
 $category_id=$category["id"];
 $category_name=$category["category"];
 ?>
 category_option+="<option value='<?php echo $category_id; ?>'><?php echo $category_name; ?></option>";   
<?php  } ?>

var total_rows=0;
addrow();
function addrow(){

    var id = total_rows+1;    
    var div = "<tr id='name_row_"+id+"'><td><input class='form-control' name='id_"+id+"' id='id_"+id+"' value='' type='hidden' /><input type='hidden' name='total_rows[]' value='" + id + "'><input name='operation_name_"+id+"' id='operation_name_"+id+"'  type='text' class='form-control'  /></td><td><select class='form-control select2' id='category_"+id+"' name='category_"+id+"'  style='width:100%'><option value=''><?php echo $this->lang->line('select'); ?>"+category_option+"</option></select></td><td><button type='button' data-rowid='"+id+"' class='closebtn delete_row'><i class='fa fa-remove'></i></button></td></tr>";
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

  function addModal(){
        $('#addmultiplerow').modal('show');
        $('#form_add_multiple').trigger("reset");
        $('#set_row').html('');
        addrow();
    }

</script>