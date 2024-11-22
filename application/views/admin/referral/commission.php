<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <?php $this->load->view('admin/referral/referralSidebar'); ?>
            </div>			
			<?php if ($this->rbac->hasPrivilege('referral_commission', 'can_view')) { ?>
            <div class="col-md-10">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line("referral_commission_list"); ?></h3>
                        <div class="box-tools pull-right">
                        <?php if ($this->rbac->hasPrivilege('referral_commission', 'can_add')) { ?>
                                <a onclick="addCommissionModal()" class="btn btn-primary btn-sm addcommission"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_referral_commission'); ?></a>
                        <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                             <div class="download_label"><?php echo $this->lang->line('referral_commission_list'); ?></div>
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('category'); ?></th>
                                        <th><?php echo $this->lang->line('module_commission'); ?></th>
                                        <?php if ($this->rbac->hasPrivilege('referral_commission', 'can_edit') || $this->rbac->hasPrivilege('referral_commission', 'can_add')) { ?>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
if (empty($commission)) {

} else {
    foreach ($commission as $commission_value) {
        if (empty($commission_value->referral_commission)) {
            continue;
        }
        ?>
                                            <tr>
                                                <td><?php echo $commission_value->name; ?></td>
                                                <td>
                                                <?php foreach ($commission_value->referral_commission as $key => $value) {?>
                                                   <div><?php echo $this->lang->line($value->name) . " - " . $value->commission . "%"; ?></div>
                                                <?php }?>
                                                </td>
                                                <td class="mailbox-date pull-right">
                                                    <?php if ($this->rbac->hasPrivilege('referral_commission', 'can_edit')) { ?>
                                                        <a href="#" onclick="getRecord('<?php echo $commission_value->id; ?>')" class="btn btn-default btn-xs" data-target="#myModalEdit" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } if ($this->rbac->hasPrivilege('referral_commission', 'can_delete')) { ?>
                                                        <a class="btn btn-default btn-xs" data-toggle="tooltip" title="" onclick="delete_recordByIdReload('admin/referralcommission/delete/<?php echo $commission_value->id; ?>', '<?php echo $this->lang->line('delete_confirm') ?>')" data-original-title="<?php echo $this->lang->line('delete') ?>">
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
			<?php } ?>			
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line("add_commission"); ?></h4>
            </div>
            <form id="addcommission" class="ptt10" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('category'); ?></label>
                                    <span class="req"> *</span>
                                    <select  name="category" id="category" onchange = "getRecordByCategory()" placeholder="" class="form-control">
                                        <option value=""><?php echo $this->lang->line("select_category"); ?></option>
                                        <?php foreach ($category as $value) {?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line("standard_commission"); ?> (%)</label>
                                    <input type="text" class="form-control" name='commission' id="commission">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <label><?php echo $this->lang->line("commission_for_modules"); ?></label>
                                <span class="req"> *</span>
                                <button type="button" class="plusign" onclick="apply_to_all()" autocomplete="off"><?php echo $this->lang->line("apply_to_all"); ?></button>
                                <div class="">
                                    <div class="form-group">
                                        <div class="row">
                                                <?php foreach ($type as $key => $value) {?>
                                                    <input type="hidden" name="referral_type_id[]" value="<?php echo $value['id']; ?>">
                                                    <div class="col-sm-6 col-xs-7">
                                                        <?php echo $this->lang->line($value['name']); ?>
                                                    </div>
                                                    <div class="col-sm-6 col-xs-5"><input class="commissionInput" type="text" name="module_commission[]" id="type_id_<?php echo $value['id']; ?>" class="form-control" autocomplete="off"></div>
                                                <?php }?>
                                        </div>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>        
                    <div class="modal-footer">
                        <div class="pull-right">
                            <button type="submit" id="addcommissionbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        </div>
                   </div>   
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line("edit_commission"); ?></h4>
            </div>
            <form id="editcommission" class="ptt10" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('category'); ?></label>
                        <select id="category_edit" class="form-control" disabled="disabled">
                            <?php foreach ($category as $value) {?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for=""><?php echo $this->lang->line("standard_commission"); ?> (%)</label>
                        <input type="text" class="form-control" name='commission' id="commissionEdit">
                        <input type="hidden" class="form-control" name='category' id="categoryEdit">
                    </div>
                <div class="">
                    <label><?php echo $this->lang->line("commission_for_modules"); ?></label>
                    <span class="req"> *</span>
                    <button type="button" class="plusign" onclick="apply_to_all_edit()" autocomplete="off"><?php echo $this->lang->line("apply_to_all"); ?></button>
                        <div class="form-group">
                            <div class="row">
                                <?php foreach ($type as $key => $value) {?>
                                <div id="module_commission">
                                        <input type="hidden" name="referral_type_id[]" value="<?php echo $value['id']; ?>">
                                        <div class="col-sm-6 col-xs-7">
                                            <?php echo $this->lang->line($value['name']); ?>
                                        </div>
                                        <div class="col-sm-6 col-xs-5"><input class="commissionInputEdit" type="text" name="module_commission[]" id="type_edit_id_<?php echo $value['id']; ?>" class="form-control" autocomplete="off"></div>
                                    </div>
                                    <?php }?>
                            </div>
                            <span class="text-danger"></span>
                        </div>
                </div>
            </div>    
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="editcommissionbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    $(document).ready(function (e) {
        $('#addcommission').on('submit', (function (e) {
            $("#addcommissionbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralcommission/add',
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
                            $('.' + index).html(value);
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#addcommissionbtn").button('reset');
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>")
                }
            });
        }));
    });

    function getRecord(id) {
        $('#myModalEdit').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralcommission/get_by_category/' + id,
            type: "POST",
            dataType: "json",
            success: function (data) {
                if(data != ""){
                    $.each(data, function(i,item){4
                        $("#type_edit_id_"+item.referral_type_id).val(item.commission);
                        $("#categoryEdit").val(item.referral_category_id);
                        $("#category_edit").val(item.referral_category_id);
                    });
                }else{
                    $(".commissionInputEdit").val("");
                }
                }
        });
    }

    function getRecordByCategory() {
        id = $("#category").val();
        if(id!=''){
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralcommission/get_by_category/' + id,
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if(data != ""){
                        $.each(data, function(i,item){
                            $("#type_id_"+item.referral_type_id).val(item.commission);
                        });
                    }else{
                        $(".commissionInput").val("");
                    }
                }
            });
        }else{
             $(".commissionInput").val("");
        }
    }

    $(document).ready(function (e) {
        $('#editcommission').on('submit', (function (e) {
            $("#editcommissionbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralcommission/add',
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
                    $("#editcommissionbtn").button('reset');
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });

    function apply_to_all(){
        commission = $("#commission").val();
        $(".commissionInput").val(commission);
    }

    function apply_to_all_edit(){
        commission = $("#commissionEdit").val();
        $(".commissionInputEdit").val(commission);
    }

    function addCommissionModal(){
        $('#myModal form')[0].reset();
        $("#myModal").modal("show");
    }
    
    $(document).ready(function (e) {
        $('#myModal,#myModalEdit').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
</script>