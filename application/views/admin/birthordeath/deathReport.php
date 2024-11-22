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
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('death_record'); ?></h3>
                        <div class="box-tools pull-right box-tools-md mt-md-0">
                            <?php
                            if ($this->rbac->hasPrivilege('death_record', 'can_add')) {
                            ?>
                                <a data-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm deathrecord"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_death_record'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('death_record'); ?></div>
                        <div class="table-responsive overflow-visible-lg">
                            <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('death_record'); ?>">
                                <thead>
                                    <tr>
                                    <th><?php echo $this->lang->line('reference_no') ; ?></th>
                                    <th><?php echo $this->lang->line('case_id') ; ?></th>
                                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                                    <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('death_date'); ?></th>
                                    <?php
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                                <th class="white-space-nowrap"><?php echo $fields_value->name; ?></th>
                                    <?php
                                    }
                                    }
                                    ?>
                                    <th class="text-right"><?php echo $this->lang->line('report'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_death_record'); ?></h4>
            </div>
         
        <form id="formadd" accept-charset="utf-8" method="post" class="ptt10" enctype="multipart/form-data">
            <div>
                <div class="modal-body pb0 pt0 scroll-area">
                    <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line("case_id"); ?></label><small class="req"> *</small>
                                        <input  type="text" name="case_id" id="case_id" class="form-control">
                                        <span class="text-danger"><?php echo form_error('case_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="">
                                            <?php echo $this->lang->line('patient_name'); ?>
                                        </label>
                                        <small class="req"> *</small>
                                        <input type="text" id="patient_name" readonly name="patient_name" class="form-control">
                                        <input type="hidden" id="patient" name="patient" >
                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('death_date'); ?></label>
                                        <small class="req">*</small>
                                        <input id="datetimepicker" name="death_date" type="text" class="form-control datetime" />
                                        <span class="text-danger"><?php echo form_error('death_date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('guardian_name'); ?></label><small class="req"> *</small>
                                        <input type="text" name="guardian_name" id="guardian_name" class="form-control">
                                        <span class="text-danger"><?php echo form_error('guardian_name'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('attachment'); ?></label>
                                        <input type="file" name="document" id="document" class="form-control filestyle">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('report'); ?></label>
                                        <textarea name="death_report" id="death_report" class="form-control" ><?php echo set_value('death_report'); ?></textarea>
                                    </div>
                                </div>
                            <div>
                                <?php
                                    echo display_custom_fields('death_report');
                                ?>
                            </div>
                        </div><!--./row-->
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
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_delete'>

                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('death_record_details'); ?></h4>
            </div>
                <form id="view" accept-charset="utf-8" method="get" class="ptt10">
                    <div class="modal-body pt0">
                        <div class="table-responsive">
                            <table class="table mb0 table-striped table-bordered examples tablelr0space">
                                    <tr>
                                        <th><?php echo $this->lang->line("reference_no"); ?></th>
                                        <td><span id='vreference_no'></span></td>
                                        <th></th>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line("case_id"); ?></th>
                                        <td><span id='vcase_id'></span></td>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <td><span id='vpatient'></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <td><span id="vgender"></span>
                                        </td>
                                        <th><?php echo $this->lang->line('death_date'); ?></th>
                                        <td><span id='vdeath_date'></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <td><span id="vaddress"></span>
                                        </td>
                                        <th><?php echo $this->lang->line('death_report'); ?></th>
                                        <td><span id="vdeath_report"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                        <td><span id="vguardian_name"></span>
                                    </tr>
                                    <tr id="field_data">
                                        <th><span id="vcustom_name"></span></th>
                                        <td><span id="vcustom_value"></span></td>
                                    </tr>
                                </table>
                            </div>
                    </div>        
                <div id="tabledata"></div>
            </form>
        </div>
    </div>
</div>
</div>

<div class="modal fade ddd" id="myModaledit" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_death_record'); ?></h4>
            </div>
            
                <form id="formedit" accept-charset="utf-8" method="post" class="ptt10" enctype="multipart/form-data">
                    <div class="modal-body pb0 pt0 scroll-area">
                        <div class="row">
                            <input type="hidden" name="id" id="eid" value="<?php echo set_value('id'); ?>">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line("case_id") ; ?></label><small class="req">*</small>
                                        <input id="ecase_id" value="<?php echo set_value('case_id'); ?>" name="case_id" placeholder="" type="text" class="form-control" />
                                        <span class="text-danger"><?php echo form_error('case_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="">
                                            <?php echo $this->lang->line('patient_name'); ?>
                                            </label>
                                            <small class="req">*</small>
                                            <input type="text" id="epatient_name" name="epatient_name" class="form-control" readonly="">
                                            <input type="hidden" id="epatient" name="epatient">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('death_date'); ?></label>
                                            <small class="req">*</small>
                                            <input id="edeath_date" value="<?php echo set_value('death_date'); ?>" name="death_date" placeholder="" type="text" class="form-control datetime"   />
                                            <span class="text-danger"><?php echo form_error('death_date'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('guardian_name'); ?></label><small class="req"> *</small>
                                            <input type="text" value="<?php echo set_value('guardian_name'); ?>" name="guardian_name" id="eguardian_name" class="form-control">
                                            <span class="text-danger"><?php echo form_error('guardian_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email"><?php echo $this->lang->line('attachment'); ?></label>
                                            <input type="file" name="document" id="document" class="form-control filestyle">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email"><?php echo $this->lang->line('report'); ?></label>
                                            <textarea name="death_report" id="edeath_report" class="form-control" ><?php echo set_value('death_report'); ?></textarea>
                                        </div>
                                    </div>
                                    <div id="customfield"></div>
                        </div>        
                    </div>            
                    <div class="modal-footer">
                        <button type="submit" id="formeditbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </form>
        </div>
    </div>
</div>

<script type="text/javascript">
     
    $('#myModal').on('hidden.bs.modal', function (e) {
        $(this).find('#formadd')[0].reset();
        $(".dropify-clear").trigger("click");
        $("#selectdata").val("").change();
    });

    $(function () {
        $('#easySelectable').easySelectable();
        $('.select2').select2()
    })

    function apply_to_all() {
        var standard_charge = $("#standard_charge").val();
        $('input name=schedule_charge_id').val(standard_charge);
    }
</script>
<script type="text/javascript">
    (function ($) {
        //selectable html elements
        $.fn.easySelectable = function (options) {
            var el = $(this);
            var options = $.extend({
                'item': 'li',
                'state': true,
                onSelecting: function (el) {

                },
                onSelected: function (el) {

                },
                onUnSelected: function (el) {

                }
            }, options);
            el.on('dragstart', function (event) {
                event.preventDefault();
            });
            el.off('mouseover');
            el.addClass('easySelectable');
            if (options.state) {
                el.find(options.item).addClass('es-selectable');
                el.on('mousedown', options.item, function (e) {
                    $(this).trigger('start_select');
                    var offset = $(this).offset();
                    var hasClass = $(this).hasClass('es-selected');
                    var prev_el = false;
                    el.on('mouseover', options.item, function (e) {
                        if (prev_el == $(this).index())
                            return true;
                        prev_el = $(this).index();
                        var hasClass2 = $(this).hasClass('es-selected');
                        if (!hasClass2) {
                            $(this).addClass('es-selected').trigger('selected');
                            el.trigger('selected');
                            options.onSelecting($(this));
                            options.onSelected($(this));
                        } else {
                            $(this).removeClass('es-selected').trigger('unselected');
                            el.trigger('unselected');
                            options.onSelecting($(this))
                            options.onUnSelected($(this));
                        }
                    });
                    if (!hasClass) {
                        $(this).addClass('es-selected').trigger('selected');
                        el.trigger('selected');
                        options.onSelecting($(this));
                        options.onSelected($(this));
                    } else {
                        $(this).removeClass('es-selected').trigger('unselected');
                        el.trigger('unselected');
                        options.onSelecting($(this));
                        options.onUnSelected($(this));
                    }
                    var relativeX = (e.pageX - offset.left);
                    var relativeY = (e.pageY - offset.top);
                });
                $(document).on('mouseup', function () {
                    el.off('mouseover');
                });
            } else {
                el.off('mousedown');
            }
        };
    })(jQuery);
</script>
<script type="text/javascript">
    function getChargeCategory(charge_type, charge_category) {
        $('#edit_charge_category').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
        var base_url = '<?php echo base_url() ?>';
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>admin/charges/get_charge_category',
            data: {'charge_type': charge_type},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj) {
                    var sel = "";
                    if (charge_category == obj.name) {
                        sel = "selected";
                    }
                    div_data += "<option value='" + obj.name + "'  " + sel + ">" + obj.name + "</option>";
                });
                $('#edit_charge_category').append(div_data);
            }
        });
    }

    function getcharge_category(id, htmlid) {
        var div_data = "";
        $("#" + htmlid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: id},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.name + "'>" + obj.name + "</option>";
                });
                $("#" + htmlid).html("<option value=''>Select</option>");
                $('#' + htmlid).append(div_data);
            }
        });
    }

    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {
            $("#formaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/birthordeath/addDeathdata',
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
        $("#formedit").on('submit', (function (e) {
            $("#formeditbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/birthordeath/update_death',
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

    $(document).ready(function (e) {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
        $('#dates_of_birth , #date_of_birth').datepicker();
    });

    function viewDetail(id) {
        $('#viewModal').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/birthordeath/getDeathdata',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#vid").html(data.id);
                $("#vreference_no").html(data.prefix+data.id);
                $("#vopdipd_no").html(data.opdipd_no);
                $("#vpatient").html(data.patient_name+" ("+data.patient_id+")");
                $("#vgender").html(data.gender);
                $("#vcase_id").html(data.case_reference_id);   
                $("#vimage").html(data.image);
                $("#vdeath_date").html(data.death_date);
                $("#vguardian_name").html(data.guardian_name);
                $("#vcontact").html(data.contact);
                $("#vaddress").html(data.address);
                $("#vdeath_report").html(data.death_report);
                var table_html = '';
                $.each(data.field_data, function (i, obj)
                {
                    if (obj.field_value == null) {
                        var field_value = "";
                    } else {
                        var field_value = obj.field_value;
                    }
                    table_html += "<th><span id='vcustom_name'>" + obj.name + "</span></th><td><span id='vcustom_value'>" + field_value + "</span></td>";
                });
                $("#field_data").html(table_html);
                $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('death_record', 'can_view')) {?><a href='#' data-toggle='tooltip' onclick='printData(" + id + ")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php }?><?php if ($this->rbac->hasPrivilege('death_record', 'can_edit')) {?><a href='#'' onclick='getRecord(" + id + ")' data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php }?><?php if ($this->rbac->hasPrivilege('death_record', 'can_delete')) {?><a onclick='delete_bill(" + id + ")'  href='#'  data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php }?>");
            },
        });
    }

  

    function getRecord(id) {
        $('#myModaledit').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/birthordeath/editDeath',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#eid").val(data.id);
                $("#eopdipd_no").val(data.opdipd_no);
                $('#customfield').html(data.custom_fields_value);
                $("#edeath_date").val(data.death_date);
                $("#ecase_id").val(data.case_reference_id);
                $("#eguardian_name").val(data.guardian_name);
                $("#edeath_report").val(data.death_report);
               
                $("#formedit #epatient_name").val(data.patient_name+" ("+data.patient_id+")").prop("readonly", true);
                $("#formedit #epatient").val(data.patient_id);
            },
        });
    }

    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/birthordeath/deletedeath/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }

    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/birthordeath/getDeathprintDetails/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }     

    function apply_to_all() {
        var total = 0;
        var standard_charge = $("#standard_charge").val();
        var schedule_charge = document.getElementsByName('schedule_charge[]');
        for (var i = 0; i < schedule_charge.length; i++) {
            var inp = schedule_charge[i];
            inp.value = standard_charge;
        }
    }

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
        
        $('.filestyle','#' + modalId).dropify();
    }
</script>
<script type="text/javascript">
   function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}

$(document).on('input','#case_id',function(){
    $('#patient_name').val('');
     $('#guardian_name').val('');
      $("#patient").val('');
    var case_reference_id= $(this).val();
     if (isNaN(case_reference_id)) {

        errorMsg('<?php echo $this->lang->line("case_id_not_valid"); ?>');
         
        }else{
            getmothernamebycaseid(case_reference_id);  
        }
  

});

$(document).on('input','#ecase_id',function(){
    $('#epatient_name').val('');
    $('#eguardian_name').val('');
    var case_reference_id= $(this).val() ;
    
    if (isNaN(case_reference_id)) {

        errorMsg('<?php echo $this->lang->line("case_id_not_valid"); ?>');
         
        }else{
            getmothernamebycaseid(case_reference_id);  
        }

});

    function getmothernamebycaseid(case_id) {
        if(case_id != ''){
       $.ajax({
            url: '<?php echo base_url(); ?>admin/birthordeath/getdeathpatientBycaseId/'+case_id,
            type: "POST",
            data: {case_reference_id: case_id},
            dataType: 'json',
            success: function (res) {
               
                if(res.status == 1){                  
                 
                    $("#formadd #patient_name").val(res.patient_name).prop('readonly',true);
                    $("#formadd #patient").val(res.patient_id);
                    $("#formadd #guardian_name").val(res.guardian_name);

                    $("#formedit #epatient").val(res.patient_id);
                    $("#formedit #epatient_name").val(res.patient_name).prop('readonly',true);
                    $("#formedit #eguardian_name").val(res.guardian_name);

               }else{
                errorMsg(res.message);
               }
            }
        }); 
   }

    }

    function case_reference(case_reference_id){
   
    if(case_reference_id!=''){
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientBycaseId/'+case_reference_id,
            type: "POST",
            data: {case_reference_id: case_reference_id},
            dataType: 'json',
            success: function (res) {
                if(res.status==1){
                   $("#selectdata").select2("val", res.patient_id);
                   $("#epatient_name").select2("val", res.patient_id);
                   
               }else{
                errorMsg('<?php echo $this->lang->line("patient_not_found"); ?>');
               }
            }
        });
   }else{

    $('#selectdata').val(null).trigger('change');
   }
        
      
};
    
    $(document).ready(function (e) {
        $('#viewModal,#myModaledit').modal({
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
        initDatatable('ajaxlist','admin/birthordeath/getdeathDatatable',[],[],100,[           
            { "sWidth": "200px",  "aTargets": [ -1, -2, -3 ,-4 ] }		 
            ]);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->