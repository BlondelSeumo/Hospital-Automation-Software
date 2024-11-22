<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="box border0">
                    <ul class="tablists">
                        <?php if($this->module_lib->hasActive('income')){ ?>
                        <?php if ($this->rbac->hasPrivilege('income_head', 'can_view')) {?>
                            <li><a href="<?php echo base_url(); ?>admin/incomehead" ><?php echo $this->lang->line('income_head'); ?></a></li>
                        <?php }?>
                        <?php }?>
                        <?php if($this->module_lib->hasActive('expense')){ ?>
                        <?php if ($this->rbac->hasPrivilege('expense_head', 'can_view')) {?>
                            <li><a href="<?php echo base_url(); ?>admin/expensehead" class="active"><?php echo $this->lang->line('expense_head'); ?></a></li>
                        <?php }?>
                        <?php }?>
                    </ul>
                </div>
            </div>
<?php if($this->module_lib->hasActive('expense')){ ?>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="box box-primary" id="exphead">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('expense_head_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('expense_head', 'can_add')) {?>
                                <a onclick="addModal()" class="btn btn-primary btn-sm expense_head"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_expense_head'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <div class="download_label"><?php echo $this->lang->line('expense_head_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('expense_head'); ?></th>
                                        <th class="text-right no-print noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($categorylist)) {
    ?>

                                        <?php
} else {
    $count = 1;
    foreach ($categorylist as $category) {
        ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-toggle="popover" class="detail_popover">
                                                        <?php echo $category['exp_category'] ?>
                                                    </a>

                                                    <div class="fee_detail_popover" style="display: none">
                                                        <?php
if ($category['description'] == "") {
            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
} else {
            ?>
                                                            <p class="text text-info"><?php echo $category['description']; ?></p>
                                                            <?php
}
        ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-date pull-right no-print">
                                                    <?php
if ($this->rbac->hasPrivilege('expense_head', 'can_edit')) {
            ?>
                                                        <a data-target="#editmyModal" onclick="get(<?php echo $category['id']; ?>)" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <?php
}
        if ($this->rbac->hasPrivilege('expense_head', 'can_delete')) {
            ?>
                                                        <a class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('admin/expensehead/delete/<?php echo $category['id'] ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                            <?php
}
    $count++;
}
?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
<?php } ?>
           

        </div>   
    </section>
</div>

<!-- add multiple data modal -->
<div class="modal fade" id="addmultiplerow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header overflow-hidden">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_expense_head') ;?></h4> 
            </div>
            
                <form id="form_add_multiple" action="<?php echo site_url('admin/expensehead/add_multiple_expense_head') ?>"  class="ptt10"  method="post" accept-charset="utf-8">
                    <div class="modal-body pt0">    
                        <div class="row">
                            <div class="col-md-12">    
                                <div class="table-responsive overflow-visible mb0">
                                    <table class="table table-striped mb0 table-bordered table-hover  tablefull12 tblProducts " id="tableID_vitals">
                                        <thead>
                                            <tr class="font13 white-space-nowrap">
                                                <th width="30"><?php echo $this->lang->line('expense_head'); ?><small class="req" style="color:red;"> *</small></th>
                                                <th width="60%"><?php echo $this->lang->line('description'); ?></th>
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

<div class="modal fade" id="editmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_expense_head'); ?></h4>
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/expensehead/edit') ?>" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('expense_head'); ?></label><small class="req"> *</small>
                            <input autofocus="" id="expensehead1" name="expensehead" placeholder="" type="text" class="form-control"  value="<?php echo set_value('expensehead'); ?>" />
                            <span class="text-danger"><?php echo form_error('expensehead'); ?></span>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                            <textarea class="form-control" id="description1" name="description" placeholder="" rows="3"></textarea>
                            <input type="hidden" id="exphead_id" name="exphead_id">
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="editformaddbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
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
    });
</script>

<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>'; 
	
$("#print_div").click(function () {
    popup($('#exphead').html());
});
</script>

<script>
   function get(id) {
        $('#editmyModal').modal();
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/expensehead/get_data/' + id,
            success: function (result) {
                $('#exphead_id').val(result.id);
                $('#expensehead1').val(result.exp_category);
                $('#description1').text(result.description);
            }
        });
    }

    $(document).ready(function (e) {
        $('#editformadd').on('submit', (function (e) {
            $("#editformaddbtn").button('loading');
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

    $(".expense_head").click(function(){
        $('#formadd').trigger("reset");
    });

    $(document).ready(function (e) {
        $('#addmultiplerow,#editmyModal').modal({
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
    var div = "<tr id='name_row_"+id+"'><td><input type='hidden' name='total_rows[]' value='" + id + "'><input name='expensehead_"+id+"' id='expensehead_"+id+"'  type='text' class='form-control'  /></td><td><input name='description_"+id+"' id='description_"+id+"'  type='text' class='form-control'  /></td><td><button type='button' data-rowid='"+id+"' class='closebtn delete_row'><i class='fa fa-remove'></i></button></td></tr>";
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
