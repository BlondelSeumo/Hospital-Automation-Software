<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="box border0">
                    <?php $this->load->view("admin/charges/sidebar"); ?>
                </div>
            </div>
			
			<?php if ($this->rbac->hasPrivilege('hospital_charges', 'can_view')) {?>  
            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('charges_details_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php
                                if ($this->rbac->hasPrivilege('hospital_charges', 'can_add')) {
                            ?>
                                <a data-toggle="modal" onclick="holdModal('myModal')" id="add_charge_modal" class="btn btn-primary btn-sm charge"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_charges'); ?></a>
                            <?php }?>
                           
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('charges_details_list'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('charge_category'); ?></th>
                                    <th><?php echo $this->lang->line('charge_type'); ?></th>
                                    <th><?php echo $this->lang->line('unit'); ?></th>
                                    <th class=""><?php echo $this->lang->line('tax').'(%)'; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
			<?php } ?>
        </div>
    </section>
</div>
<div class="modal fade" id="myModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><div id="modal_title"></div></h4>
            </div>
            
                <form id="formadd" accept-charset="utf-8" method="post" class="ptt10">
                    <div class="modal-body pt0">
                            <input type="hidden" class="id" name="id">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge_type'); ?></label>
                                        <small class="req"> *</small>
                                        <select name="charge_type"  class="form-control charge_type">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($charge_type as $charge_key => $charge_value) {?>
                                                <option value="<?php echo $charge_value->id; ?>"><?php echo $charge_value->charge_type; ?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge_category'); ?></label>
                                        <small class="req"> *</small>
                                        <select name="charge_category" id="charge_category" class="form-control select2" style="width: 100%">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('unit_type') ?></label><small class="req"> *</small>
                                           <select name="unit_type" class="form-control unit_type">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($unit_type as $unit_type_key => $unit_type_value) {
    ?>
                                                <option value="<?php echo $unit_type_value->id; ?>"><?php echo $unit_type_value->unit; ?></option>
                                            <?php }?>
                                        </select>

                                        <span class="text-danger"><?php echo form_error('unit_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge_name') ?></label><small class="req"> *</small>
                                        <input type="text" name="charge_name" id="charge_name" class="form-control">
                                        <span class="text-danger"><?php echo form_error('charge_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                    <div class="col-sm-6">
                                         <div class="form-group">
                                        <label><?php echo $this->lang->line('tax_category'); ?></label><small class="req"> *</small>
                                          <select name="taxcategory" id="taxcategory" class="form-control tax_category">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($taxcategory as $taxcategory_key => $taxcategory_value) {
    ?>
                                                <option value="<?php echo $taxcategory_value['id']; ?>"><?php echo $taxcategory_value['name']; ?></option>
                                            <?php }?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('taxcategory'); ?></span>
                                    </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group"> 
                                         <label for="exampleInputFile"><?php echo $this->lang->line('tax'); ?></label>
                                            <div class="input-group">
                                            <input type="text" class="form-control right-border-none" readonly name="tax_percentage" id="tax_percentage"  autocomplete="off">
                                            <span class="input-group-addon "> %</span>
                                            </div>
                                    </div>
                                        
                                    </div>
                                </div>
                                   
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                        <input type="text" name="standard_charge" id="standard_charge" class="form-control">
                                        <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label> <?php echo $this->lang->line('description'); ?></label>
                                        <textarea name="description" class="form-control description"></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <label><?php echo $this->lang->line('scheduled_charges_for_tpa'); ?></label>
                                    <button type="button" class="plusign" onclick="apply_to_all()"><?php echo $this->lang->line('apply_to_all'); ?></button>
                                    <div class="chargesborbg">
                                        <div class="form-group">
                                            <table class="printablea4">
                                                <?php foreach ($schedule as $category) {?>
                                                    <tr id="schedule_charge">
                                                    <input type="hidden" name="schedule_charge_id[]" value="<?php echo $category['id']; ?>">
                                                    <td class="col-sm-8" style="vertical-align: bottom; text-align: left; padding-right: 20px;"><?php echo $category['organisation_name']; ?></td>
                                                    <td class="col-sm-4">
                                                <input type="text" name="schedule_charge_<?php echo $category['id']; ?>" id="schedule_charge_<?php echo $category['id']; ?>" class="form-control schedule_charge">
                                                   </td>
                                                    </tr>
                                                <?php }?>
                                            </table>
                                            <span class="text-danger"><?php echo form_error('schedule_charge'); ?></span>
                                        </div>
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
<!-- dd -->

<div class="modal fade" id="viewModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('charges_details'); ?></h4>
            </div>
            <div class="modal-body pt0 mb0 min-h-3">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
    $('.select2').select2();
});


    function apply_to_all() {
        var standard_charge = $("#standard_charge").val();
        $('input.schedule_charge').val(standard_charge);
    }
</script>

<script type="text/javascript">
  
    $(document).on('change','.charge_type',function(){
        var charge_type=$(this).val();
     
        $('#charge_category').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");

     getcharge_category(charge_type,"");

    });

    $(document).on('change','.tax_category',function(){
        $('#tax_percentage').val('');
        var taxcategory=$(this).val();
        $.ajax({
            url: base_url+'admin/taxcategory/getDetails',
            type: "POST",
            data: {tax_id: taxcategory},
            dataType: 'json',
              beforeSend: function(){
                
                 },
            success: function (data) {  
                $('#tax_percentage').val(data.percentage);
            }, 
            error: function () {
                },
                complete: function(){
                
   }
        });
      
    });

    function getcharge_category(charge_type_id, charge_category_id) {
        $("#charge_category").html("").html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $.ajax({
            url: base_url+'admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: charge_type_id},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
               
                $('#charge_category').html(div_data);
                  $('#charge_category').select2("val", charge_category_id);
            }
        });
    }

    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {
            
            $("#formaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/charges/add_charges',
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
                        window.location.reload();
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
 $.ajax({
            url: base_url+'admin/charges/getDetails',
            type: "POST",
            data: {charges_id: record_id},
            dataType: 'json',
              beforeSend: function(){
                 btn.button('loading');
                 },
            success: function (data) {
                     if (data.status == 0) {
                     
                        errorMsg(message);
                    } else {
               
                    $('.id').val(data.result.id);
                    $('#charge_name').val(data.result.name);
                    $('#taxcategory').val(data.result.tax_category_id);
                    $('#tax_percentage').val(data.result.percentage);
                    $('#standard_charge').val(data.result.standard_charge);                  
                    $('.charge_type option[value="'+data.result.charge_type_master_id+'"]').prop('selected', true);
                    $('.unit_type option[value="'+data.result.charge_unit_id+'"]').prop('selected', true);
                    $('.description').val(data.result.description);

$.each(data.result.organisation_charges, function(index, item) {
  
  $('#schedule_charge_'+item.org_id).val(item.org_charge);
});


getcharge_category(data.result.charge_type_master_id,data.result.charge_category_id);
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

    function viewDetail(id) {
      
         var view_modal=$('#viewModal');
        $.ajax({
            url: base_url+'admin/charges/viewDetails',
            type: "POST",
            data: {'charges_id': id},
            dataType:"JSON",
            beforeSend: function(){
               
           $('#viewModal').modal('show');
           view_modal.addClass('modal_loading');
           },
           complete: function(){
             view_modal.removeClass('modal_loading');
           },
            success: function (data) {
                 $("#viewModal").find('.modal-body').html(data.page);
                view_modal.removeClass('modal_loading');
            },
        });
    }
 
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

    $('#add_charge_modal').click(function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('add_charges'); ?>');
    })

    $(document).on('click','.edit_charge_modal',function(){
        $('#modal_title').empty();
        $('#modal_title').append('<?php echo $this->lang->line('edit_charges'); ?>');
    })

</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {        
        initDatatable('ajaxlist','admin/charges/getDatatable',{},[],100,[{"aTargets": [ -1,-2 ] ,'sClass': 'dt-body-right dt-head-right'}]);        
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->

