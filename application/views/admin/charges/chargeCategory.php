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
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('charge_category_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('charge_category', 'can_add')) {?>
                                <a data-toggle="modal" data-target="#myModal" id="add_charge_type_modal" class="btn btn-primary btn-sm charge_category"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_charge_category'); ?></a>
                            <?php }?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('charge_category_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                                        <th class=""><?php echo $this->lang->line('description'); ?></th>
                                        <th class="noExport"><?php echo $this->lang->line('action'); ?></th>
                                       
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
                <h4 class="modal-title"><div id="modal_title"></div></h4>
            </div>
            <form id="formadd" action="<?php echo site_url('admin/chargecategory/add') ?>" id="employeeform" name="employeeform" method="post" accept-charset="utf-8" class="ptt10">
                <div class="modal-body pt0 pb0">
                    <input type="hidden" name="id" value="0" class="id">
                           <div class="form-group">

                            <label for="pwd"><?php echo $this->lang->line('charge_type'); ?></label>
                            <small class="req"> *</small>
                            <select name="charge_type" class="form-control charge_type">

                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($charge_type as $charge_key => $charge_value) {
    ?>
                                    <option value="<?php echo $charge_value->id; ?>"><?php echo $charge_value->charge_type; ?></option>
                                <?php }?>
                            </select>
                            <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                            <input autofocus="" id="name"  name="name"  type="text" class="form-control name" value="" />
                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label><small class="req"> *</small>
                            <textarea name="description" class="form-control description"></textarea>
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>
                    </div>     
                    <div class="modal-footer">
                        <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i>  <?php echo $this->lang->line('save'); ?></button>
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
                      $('.ajaxlist').DataTable().ajax.reload();
                      $('#myModal').modal('hide');
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
            url: base_url+'admin/chargecategory/get_data',
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
                   $('.id').val(data.result.id);
                   $('.name').val(data.result.name);                   
                   $('.description').val(data.result.description);                
                   $('.charge_type option[value="'+data.result.charge_type_id+'"]').prop('selected', true);
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

</script>
<script type="text/javascript">
     $('#myModal').on('hidden.bs.modal', function (e) {    
        $('#formadd').find('input:text').val(''); 
        $('#formadd').find('.description').val('');        
        $('.charge_type option:selected').prop('selected', false);      
        $("#formadd").find('input.id').val(0);
       
});

    $('#add_charge_type_modal').click(function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('add_charge_category'); ?>');
    })

    $(document).on('click','.edit_charge_modal',function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('edit_charge_category'); ?>');
    })
    
    $(document).ready(function (e) {
        $('#myModal').modal({
            backdrop: 'static',
            keyboard: false,
            show:false
        });
    });
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/chargecategory/getDatatable');
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->