<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('vital_list'); ?></h3>
                        <div class="box-tools pull-right"> 
                            <?php if ($this->rbac->hasPrivilege('vital', 'can_add')) {?>
                            <a data-toggle="modal" onclick="holdModal('myModal')" id="add_vital_modal" class="btn btn-primary btn-sm vital"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_vital'); ?></a>                        
                            <?php } ?>
                        </div>                        
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('vital_list'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('reference_range'); ?></th>
                                    <th><?php echo $this->lang->line('unit'); ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><div id="modal_title"></div></h4>
            </div>            
                <form id="formadd" accept-charset="utf-8" method="post" class="ptt10">
                    <div class="modal-body pt0 pb0">
                            <input type="hidden" class="id" name="vital_id" id="vital_id">
                             <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('vital_name'); ?></label>
                                        <small class="req"> *</small>
                                       <input type="text" name="vital_name" id="vital_name" class="form-control">
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                </div> 
                                </div>                                
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12"><label><?php echo $this->lang->line('reference_range'); ?></label> (<?php echo $this->lang->line('if_vital_is_having_single_value_rather_than_range_then_enter_only_from_textbox_value'); ?>)</div>
                                        <div class="col-sm-6">
                                        
                                        <label>&nbsp;</label>
                                        <input autofocus="" name="from_reference_range" id="from_reference_range"  placeholder="<?php echo $this->lang->line('from'); ?>" type="text" class="form-control"  />
                                        <span class="text-danger"><?php echo form_error('from_reference_range'); ?></span>
                                    </div>        
                                    <div class="col-md-6">
                                        <label>&nbsp;</label>
                                     <input autofocus="" name="to_reference_range" id="to_reference_range" placeholder="<?php echo $this->lang->line('to'); ?>" type="text" class="form-control"  />
                                        <span class="text-danger"><?php echo form_error('to_reference_range'); ?></span>
                                    </div>
                                    </div>
                                 </div>                        
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('unit'); ?></label>
                                    <input type="text" name="unit" id="unit" class="form-control">
                                    <span class="text-danger"><?php echo form_error('unit'); ?></span>
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
<!-- dd -->

<script type="text/javascript">
    $(function () {
    $('.select2').select2();
});

    function apply_to_all() {
        var standard_charge = $("#standard_charge").val();
        $('input.schedule_charge').val(standard_charge);
    }    
    
    function delete_vital(id) {         
        if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this') ?>")) {
            $.ajax({
                url: base_url + 'admin/vital/delete/' + id,
                 
                type: 'POST',
                dataType: "json",
                success: function (res) {
                    if (res.status == "fail") {
                        errorMsg(res.message);
                    } else {
                        successMsg("<?php echo $this->lang->line('delete_message') ?>");                        
                        window.location.reload(true);
                    }
                }
            })
        }
    }
</script>

<script type="text/javascript"> 
    
    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {       
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/vital/add_vital',
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

    $(document).ready(function (e) {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
        $('#dates_of_birth , #date_of_birth').datepicker();
    });

 $(document).on('click','.edit_record',function(){
     var record_id=$(this).data('recordId');
     var btn = $(this);
    $('#unit').val('');     
 $.ajax({
            url: base_url+'admin/vital/getDetails',
            type: "POST",
            data: {vital_id: record_id},
            dataType: 'json',
              beforeSend: function(){
                 btn.button('loading');
                 },
            success: function (data) {
                     if (data.status == 0) {                     
                        errorMsg(message);
                    } else {               
                    $('#vital_id').val(data.result.id);
                    $('#vital_name').val(data.result.name);
                    $('#from_reference_range').val(data.result.min_range);
                    $('#to_reference_range').val(data.result.max_range);
                    $('#unit').val(data.result.unit);
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
 
    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }
</script>
<script type="text/javascript">
     $('#myModal').on('hidden.bs.modal', function (e) {
        $('#formadd').find('input:text').val(''); 
        $('#formadd input:checkbox').removeAttr('checked');       
        $('.charge_type option:selected').prop('selected', false);
        $('.unit_type option:selected').prop('selected', false);
        $("#formadd").find('input.id').val(0);
        $('#charge_category').html('').select2({data: [{id: '', text: 'Select'}]});
    });

    $('#add_vital_modal').click(function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('add_vital'); ?>');
    })

    $(document).on('click','.edit_vital_modal',function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('edit_vital'); ?>');
    })
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {        
        initDatatable('ajaxlist','admin/vital/getDatatable',{},[],100,[{"aTargets": [ -1,-1 ] ,'sClass': 'dt-body-right dt-head-right'}]);        
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->

