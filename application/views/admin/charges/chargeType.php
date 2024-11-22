<div class="content-wrapper" style="min-height: 946px;">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="box border0">
                    <?php $this->load->view("admin/charges/sidebar");?>
                </div>
            </div>
            <div class="col-md-10">
                <div class="box box-primary" id="tachelist">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('charge_type_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('charge_type', 'can_add')) {?>
                                <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm charge_type"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_charge_type'); ?></a>
                            <?php }?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <div class="download_label"><?php echo $this->lang->line('charge_category_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                                        <?php foreach ($charge_type_modules as $module_shortcode => $module_name) {?>
                                            <th><?=$module_name;?></th>
                                        <?php }?>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($resultlist as $key => $chargetype) {
                                        ?>
                                         <tr>
                                            <td><?php  echo $chargetype['charge_type']; ?></td>                                            
                                            <?php foreach ($charge_type_modules as $module_shortcode => $module_name) {   ?>
                                            <td><input type="checkbox" <?php echo "onclick=updateChargeTypeModule(" . $chargetype['id'] . ",'" . $module_shortcode . "') ";  
        if (in_array($chargetype['id'], $module_data[$module_shortcode])) {
            echo "checked ";
        }
        ?> /></td>
                                            <?php }?>                                            
                                            <td class="text-right">
										<?php if ($this->rbac->hasPrivilege('charge_type', 'can_edit')) {?>
                                                <a  class="btn btn-default btn-xs editcharge" data-record-id='<?php echo $chargetype['id'] ?>' data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>" ;>
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
										<?php } ?>
                                                <?php
if ($chargetype['is_default'] != 'yes') {
        if ($this->rbac->hasPrivilege('charge_type', 'can_delete')) {?>
                                                    <a  class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="deleteChargeType('<?php echo $chargetype['id'] ?>')";>
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php }}?>
                                            </td>
                                        </tr>
                                        <?php
$count++;
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_charge_type'); ?></h4>
            </div>            
            <form id="formadd" class="ptt10" action="<?php echo site_url('admin/chargetype/add') ?>" id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                <div class="modal-body pt0 pb0">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('charge_type'); ?></label><small class="req"> *</small>
                            <input autofocus="" id="type"  name="charge_type"  type="text" class="form-control" value="<?php
if (isset($result)) {
    echo $result["name"];
}
?>" />
                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                        </div>
                        <hr>
                        <label><?= $this->lang->line("module"); ?></label><small class="req"> *</small>
                        <div class="form-group">
                            <?php foreach ($charge_type_modules as $module_shortcode => $module_name) {?>
                               <label class="checkbox-inline">
                                <input type="checkbox" name="charge_module[]" value="<?=$module_shortcode;?>" value=""><?php echo $module_name; ?>
                               </label>
                               <br>
                            <?php }?>
                        </div>
                    </div>
                        <div class="modal-footer">
                            <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle" ></i> <?php echo $this->lang->line('save'); ?></button>
                        </div>
                </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_charge_category'); ?></h4>
            </div>            
            <form id="editformadd" action="<?php echo site_url('admin/chargecategory/add') ?>" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data" class="ptt10">
                <div class="modal-body pt0 pb0">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                            <input  id="type1"  name="name"  type="text" class="form-control" value="<?php
if (isset($result)) {
    echo $result["name"];
}
?>" />
                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('description'); ?></label>
                            <small class="req"> *</small>
                            <textarea name="description" id="description1" class="form-control"><?php
if (isset($result)) {
    echo $result["description"];
}
?></textarea>
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>
                        <div class="form-group">
                            <label for="pwd"><?php echo $this->lang->line('charge_type'); ?></label>
                            <small class="req"> *</small>
                            <select name="charge_type" id="charge_type1" class="form-control">
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                <?php foreach ($charge_type as $charge_key => $charge_value) {
    ?>
                                    <option value="<?php echo $charge_key; ?>" <?php if ((isset($result['charge_type'])) && ($result['charge_type'] == $charge_key)) {
        echo "selected";
    }
    ?>><?php echo $charge_value; ?></option>
                                <?php }?>
                            </select>
                            <input type="hidden" id="chargecategory_id" name="id" >
                            <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                        </div>
                    </div>   
                        <div class="modal-footer">
                            <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="editformaddbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i>  <?php echo $this->lang->line('save'); ?></button>
                        </div>
                </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editchargeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_charge_category'); ?></h4>
            </div>
            
            <form id="editform" action="<?php echo site_url('admin/chargetype/edit') ?>" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data" class="ptt10">
                <div class="modal-body pt0 pb0">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                            <input  id="editchargetype"  name="editchargetype"  type="text" class="form-control" value="" />
                             <input  id="editchargeid"  name="editchargeid"  type="hidden" class="form-control" value="" />
                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                        </div>
                       
                        
                    </div>   
                        <div class="modal-footer">
                            <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="editformbtn" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                </form>
        </div>
    </div>
</div>
<script>
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

    function deleteChargeType(id) {
        var msg = '<?php echo $this->lang->line("delete_charge_category_message"); ?>';
        if(confirm(msg)){
             var url = 'admin/chargetype/delete/'+id;
              $.ajax({
                 url: baseurl+url,
                 dataType: 'json',
                 beforeSend: function() {

                },
                 success: function (res) {
                    successMsg(res.msg);

                    window.location.reload(true);
                        },
                        error: function(xhr) { // if error occured
                   alert("Something went wrong");

            },
            complete: function() {


            }
            })
        }
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

    $(".charge_type").click(function(){
        $('#formadd').trigger("reset");
    });

    function updateChargeTypeModule(charge_type, module_shortcode){
        $.ajax({
            url: "<?=base_url('admin/chargetype/updateChargeTypeModule');?>",
            type: "POST",
            data: {charge_type:charge_type,module_shortcode:module_shortcode},
            dataType: 'json',
            success: function (data) {
                if(data.status=="success"){
                    successMsg(data.message)
                }
            },
        });
    }

    $(document).ready(function (e) {
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false,
            show:false
        });
    });
</script>
<script>
    $(document).on('click','.editcharge',function(){    
            
            var $this = $(this);
            var recordId = $this.data('recordId');          
          
            $.ajax({
                url: base_url+'admin/chargetype/getchargetype',
                type: "POST",
                data: {'id':recordId},
                dataType: 'json',
                 beforeSend: function() {
                    $this.button('loading');
                    
                },
                success: function(res) {   
                    
                        $('#editchargeModal').modal();
                        $("#editchargetype").val(res.result.charge_type);
                        $("#editchargeid").val(res.result.id);
                  
                  $this.button('reset');
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                 
            },
            complete: function() {
                  $this.button('reset');
            
            }
            });
            
        });
</script>
<script>
    $(document).ready(function (e) {
        $('#editform').on('submit', (function (e) {
            $("#editformbtn").button('loading');
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
                    $("#editformbtn").button('reset');
                },
                error: function () {

                }
            });
        }));
    });
    </script>