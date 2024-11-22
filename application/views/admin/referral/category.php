<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <?php $this->load->view('admin/referral/referralSidebar'); ?>
            </div>
            <div class="col-md-10"> 
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line("referral_category_list"); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('referral_category', 'can_add')) { ?>
                                <a onclick="addModal()" class="btn btn-primary btn-sm addcategory"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_referral_category'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <?php if ($this->rbac->hasPrivilege('referral_category', 'can_edit') || $this->rbac->hasPrivilege('referral_category', 'can_delete')) { ?>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if (empty($category)) {
                                    ?>
                                    <?php
                                        } else {
                                        foreach ($category as $key => $value) {
                                    ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-toggle="popover" class="detail_popover"><?php echo $value['name'] ?></a>
                                                </td>
                                                <td class="mailbox-date pull-right">
                                                    <?php if ($this->rbac->hasPrivilege('referral_category', 'can_edit')) { ?>
                                                        <a href="#" onclick="getRecord('<?php echo $value['id'] ?>')" class="btn btn-default btn-xs" data-target="#myModalEdit" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } if ($this->rbac->hasPrivilege('referral_category', 'can_delete')) { ?>
                                                        <a  class="btn btn-default btn-xs" data-toggle="tooltip" title="" onclick="delete_recordByIdReload('admin/referralcategory/delete/<?php echo $value['id']; ?>', '<?php echo $this->lang->line('delete_confirm') ?>')" data-original-title="<?php echo $this->lang->line('delete') ?>">
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
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- add multiple data modal -->
<div class="modal fade" id="addmultiplerow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm400" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header overflow-hidden">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_category') ;?></h4> 
            </div>
           
                <form id="form_add_multiple" action="<?php echo site_url('admin/referralcategory/add_multiple_category/') ?>"  method="post" accept-charset="utf-8">
                        <div class="modal-body pt0">     
                            <div class="row pt5">
                                <div class="col-md-12">    
                                    <div class="table-responsive overflow-visible mb0">
                                        <table class="table table-striped mb0 table-bordered table-hover tablefull12 tblProducts" id="tableID_vitals">
                                            <thead>
                                                <tr class="font13 white-space-nowrap">
                                                    <th width="90"><?php echo $this->lang->line('name'); ?><small class="req" style="color:red;"> *</small></th>
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

<div class="modal fade" id="myModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm400" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line("edit_category"); ?></h4>
            </div>
            <form id="editcategory" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10 row" id="">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label>
                                <span class="req"> *</span>
                                <input id="edit_name" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                <input id="categoryid" name="categoryid" placeholder="" type="hidden" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="editcategorybtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function getRecord(id) {
        $('#myModalEdit').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralcategory/get/' + id,
            type: "POST",
            dataType: "json",
            success: function (data) {
                $("#edit_name").val(data.name);
                $("#categoryid").val(id);
            },
            error: function () {
                alert("Fail")
            }
        });
    }

    $(document).ready(function (e) {
        $('#editcategory').on('submit', (function (e) {
            $("#editcategorybtn").button('loading');

            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralcategory/update',
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
                    $("#editcategorybtn").button('reset');
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });
    
    $(document).ready(function (e) {
        $('#addmultiplerow,#myModalEdit').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
</script>


<script>
var total_rows=0;
addrow();
function addrow(){
    var id = total_rows+1;    
    var div = "<tr id='name_row_"+id+"'><td><input class='form-control' name='categoryid_"+id+"' id='categoryid_"+id+"' value='' type='hidden' /><input type='hidden' name='total_rows[]' value='" + id + "'><input name='name_"+id+"' id='name_"+id+"'  type='text' class='form-control'  /></td><td><button type='button' data-rowid='"+id+"' class='closebtn delete_row'><i class='fa fa-remove'></i></button></td></tr>";
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