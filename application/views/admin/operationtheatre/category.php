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
			<?php if ($this->rbac->hasPrivilege('operation_category', 'can_view')) { ?>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('operation_category_list'); ?></h3>
                        <div class="box-tools pull-right">
							<?php if ($this->rbac->hasPrivilege('operation_category', 'can_add')) { ?>
                            <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm addcategory"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_category'); ?></a>
							<?php  } ?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('operation_category_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible">							
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
if (!empty($category_list)) {  
                                        
    foreach ($category_list as $key => $value) {
        ?>
                                            <tr>
                                                <td class="mailbox-name"> <?php echo $value['category'] ?></td>
                                                <td class="mailbox-date pull-right">
                                                    <?php if ($this->rbac->hasPrivilege('operation_category', 'can_edit')) {?>
                                                        <a data-target="#editmyModal" onclick="get(<?php echo $value['id']; ?>)" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php }?>
                                                    <?php if ($this->rbac->hasPrivilege('operation_category', 'can_delete')) {?>
                                                        <a  class="btn btn-default btn-xs" data-toggle="tooltip" title="" onclick="deletecategory('<?php echo $value['id']; ?>')" data-original-title="<?php echo $this->lang->line('delete'); ?>">
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header overflow-hidden">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><div id="modaltitle"></div></h4>
            </div>
                <form id="formadd" action="<?php echo site_url('admin/operationtheatre/addmultiplecategory') ?>" class="ptt10" method="post" accept-charset="utf-8">
                   <div class="modal-body pt0">    
                        <div class="row">
                            <div class="col-md-12">    
                                <div class="table-responsive overflow-visible mb0">
                                    <table class="table table-striped mb0 table-bordered table-hover  tablefull12 tblProducts " id="tableID_vitals">
                                        <thead>
                                            <tr class="font13 white-space-nowrap">
                                                <th width="90%"><?php echo $this->lang->line('operation_category'); ?><small class="req" style="color:red;"> *</small></th>
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
                            <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </div>  
                </form>    
        </div>
    </div>
</div>

<div class="modal fade" id="edit_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modaltitleedit"></h4>
            </div>
            <form id="formedit" action="<?php echo site_url('admin/operationtheatre/editcategory') ?>" method="post" accept-charset="utf-8">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label for="pwd"><?php echo $this->lang->line('operation_category'); ?></label> <small class="req"> *</small>
                            <input class="form-control" id="category_name" name="category_name" value="<?php echo set_value('operation_category'); ?>"/>
                            <span class="text-danger"><?php echo form_error('category_name'); ?></span>
                            <input class="form-control" id="id" name="id" value="" type="hidden" />
                        </div>
                    </div>
                </div><!--./col-md-12-->
                <div class="modal-footer">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formeditbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div><!--./row-->
    </div>
</div>

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
    //this function will save multiple row
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
                    }else if(data.status==2){

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

    //this function will edti single row
     $(document).ready(function (e) {
        $('#formedit').on('submit', (function (e) {
            e.preventDefault();
            $("#formeditbtn").button('loading');
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
                    $("#formeditbtn").button('reset');
                },
                error: function () {
                }
            });
        }));
    });

    function get(id) {
        $("#modaltitleedit").html('<?php echo $this->lang->line('edit_category'); ?>');
        $('#edit_category').modal('show');

        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/operationtheatre/getcategory/' + id,
            success: function (result) {
                $('#id').val(result.id);
                $('#category_name').val(result.category);
            }
        });
    }

    $(".addcategory").click(function(){
    $("#modaltitle").html('<?php echo $this->lang->line('add_category'); ?>');
    $('#formadd').trigger("reset");
    $('#set_row').html('');
    addrow();
});
</script>
<script>
function deletecategory(id) {              
    if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
        $.ajax({
            url: '<?php echo base_url()."admin/operationtheatre/deletecategory"; ?>',
            data:{id:id},
            type:"post",
            success: function (res) {
               toastr.success(
                "<?php echo $this->lang->line('record_deleted') ?>", '', {
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
var total_rows=0;
addrow();
function addrow(){
    var id = total_rows+1;   
    var div = "<tr id='category_row_"+id+"'><td><input class='form-control' name='id_"+id+"' id='id_"+id+"' value='' type='hidden' /><input type='hidden' name='total_rows[]' value='" + id + "'><input name='category_"+id+"' id='category_"+id+"'  type='text' class='form-control'  /></td><td><button type='button' data-rowid='"+id+"' class='closebtn delete_row'><i class='fa fa-remove'></i></button></td></tr>";
    $('#set_row').append(div);   
    total_rows++;      
}
    
$(document).on('click','.delete_row',function(e){
    if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
        var modal_=$(e.target).closest('div.modal');
        var del_row_id=$(this).data('rowid');
        $("#category_row_" + del_row_id).remove();             
    }        
});
</script>