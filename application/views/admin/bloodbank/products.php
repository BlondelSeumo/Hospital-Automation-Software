<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="box border0">
                    <ul class="tablists">
                      <li>
                        <a href="<?php echo base_url(); ?>admin/bloodbank/products" class="<?php echo set_sidebar_Submenu('admin/bloodbank/products'); ?>" ><?php echo $this->lang->line('products'); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-10">
                <div class="box box-primary" id="tachelist">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('product_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('blood_bank_product', 'can_add')) {?>
                                <a onclick="add()" class="btn btn-primary btn-sm charge_type"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_product'); ?></a>
                            <?php }?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?= $this->lang->line('product_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('type'); ?></th>                                       
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
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
                <h4 class="modal-title" id="title"></h4>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/bloodbank/add_product') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('type'); ?></label><small class="req"> *</small>
                            <select class="form-control" id="type" name="type">
                                <option value=""><?php echo $this->lang->line('select');?></option>
                                <?php 
                                foreach ($this->customlib->getblood_bank_type() as $key => $value) {
                                   ?>
                                   <option value="<?php echo $key;?>"> <?php echo $value; ?></option>
                                   <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                            <input autofocus="" id="name"  name="name"  type="text" class="form-control"  />
                             <input autofocus="" id="id"  name="id"  type="hidden" class="form-control"  />
                        </div>
                    </div>
                    </div>
                        <div class="modal-footer">
                            <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"> <i class="fa fa-check-circle" ></i> <?php echo $this->lang->line('save'); ?></button>
                        </div>
                </form>
        </div>
    </div>
</div>
<script>
	function add(){
		$('#title').html("<?php echo $this->lang->line('add_product'); ?>");
        $("#formadd").trigger('reset');
		$('#myModal').modal('show');
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

 $(document).on('click','.edit_record',function(){
     var record_id=$(this).data('recordId');
     var btn = $(this);
 $.ajax({ 
            url: base_url+'admin/bloodbank/getproductDetails',
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
                    	$('#title').html("<?php echo $this->lang->line('edit_product'); ?>");
                    	$('#id').val(data.id);
                    	$('#name').val(data.name);
                        $('#type').val(data.is_blood_group);
                        $('#volume').val(data.volume);
                        $('#unit').val(data.charge_unit_id);
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

    $(document).ready(function (e) {
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false,
            show:false
        });
    });
</script> 
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/bloodbank/getproductlist');
    });
} ( jQuery ) )
</script>