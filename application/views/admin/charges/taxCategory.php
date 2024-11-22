<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row"> 
            <div class="col-md-2">
                <div class="box border0">
                    <?php $this->load->view("admin/charges/sidebar"); ?>
                </div>
            </div>
            <div class="col-md-10">
                <div class="box box-primary" id="tachelist">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('tax_category_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('tax_category', 'can_add')) {?>
                                <a onclick="add()" class="btn btn-primary btn-sm charge_type"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_tax_category'); ?></a>
                            <?php }?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('tax_category_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('percentage'); ?>(%)</th>                                         
                                    </tr>
                                </thead>
                                <tbody>
                                   
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
                <h4 class="modal-title" id="title"><?php ?></h4>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/taxcategory/add') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8" class="ptt10">
                    <div class="modal-body pt0 pb0">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                            <input autofocus="" id="name"  name="name"  type="text" class="form-control"  />
                             <input autofocus="" id="id"  name="id"  type="hidden" class="form-control"  />
                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                        </div>
                      <div class="form-group"> 
                         <label><?php echo $this->lang->line('percentage'); ?></label><small class="req"> *</small>
                        <div class="input-group">                           
                            <input type="text" class="form-control right-border-none" name="percentage" id="percentage"  autocomplete="off">
                            <span class="input-group-addon "> %</span>
                        </div>
                    </div>
                </div>  
                        <div class="modal-footer">
                            <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        </div>
                </form>
        </div>
    </div>
</div>

<script>
	function add(){
		$('#title').html('<?php echo $this->lang->line('add_tax_category'); ?>');
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
                      $('#myModal').modal('hide');
                      $('.ajaxlist').DataTable().ajax.reload();
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
            url: base_url+'admin/taxcategory/getDetails',
            type: "POST",
            data: {tax_id: record_id},
            dataType: 'json',
              beforeSend: function(){
                 btn.button('loading');
                 },
            success: function (data) {
                     if (data.status == 0) {                     
                        errorMsg(message);
                    } else {
                    	$('#title').html('<?php echo $this->lang->line('edit_tax_category'); ?>');
                    	$('#id').val(data.id);
                    	$('#name').val(data.name);
                    	$('#percentage').val(data.percentage);
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
        initDatatable('ajaxlist','admin/taxcategory/getDatatable',{},[],100,
    [{"bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-right dt-head-right'}]);
    });
} ( jQuery ) )
</script>