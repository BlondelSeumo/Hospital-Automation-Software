<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('approve_leave_request'); ?></h3>
                        <?php
                        if ($this->rbac->hasPrivilege('approve_leave_request', 'can_add')) {
                            ?>
                            <div class="box-tools pull-right"><a href="#addleave" onclick="addLeave()" role="button" class="btn btn-primary btn-sm checkbox-toggle edit_setting" /> <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_leave_request'); ?></a></div>
                        <?php } ?>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tab-pane active table-responsive no-padding">
                                    <div class="download_label"><?php echo $this->lang->line('approve_leave_request'); ?></div>
                                    <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('approve_leave_request'); ?>">
                                        <thead>
                                            <th><?php echo $this->lang->line('staff'); ?></th>
                                            <th><?php echo $this->lang->line('leave_type'); ?></th>
                                            <th><?php echo $this->lang->line('leave_date'); ?></th>
                                            <th><?php echo $this->lang->line('days'); ?></th>
                                            <th><?php echo $this->lang->line('apply_date'); ?></th>
                                            <th><?php echo $this->lang->line('status'); ?></th>
                                            <th><?php echo $this->lang->line('status_date'); ?></th>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>               
                </div>
            </div> 
        </div>
    </section>
</div>

<div id="leavedetails" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body">                
                <div class="row view_leave_detail">
                    <form role="form" id="leavedetails_form" action="">
                        <input type="hidden" name="staff_id" id="staff_id">
                        <div class="col-md-12">  
                            <div class="table-responsive">
                                <table class="table mb0 table-striped table-bordered">
                                    <tr>
                                        <th width="15%"><?php echo $this->lang->line('name'); ?></th>
                                        <td width="35%"><span id='name'></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('submitted_by'); ?></th>
                                        <td><span id="appliedby"></span></td>
                                        <th><?php echo $this->lang->line('leave_type'); ?></th>
                                        <td><span id="leave_type"></span>
                                            <input id="leave_request_id" name="leave_request_id" placeholder="" type="hidden" class="form-control" />
                                            <span class="text-danger"><?php echo form_error('leave_request_id'); ?></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('leave'); ?></th>
                                        <td><span id='leave_from'></span> - <label> </label><span id='leave_to'> </span> (<span id='days'></span>)
                                            <span class="text-danger"><?php echo form_error('leave_from'); ?></span></td>
                                        <th><?php echo $this->lang->line('apply'); ?> <?php echo $this->lang->line('date'); ?></th>
                                        <td><span id="applied_date"></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <td>
                                            <label class="radio-inline">
                                                <input type="radio" value="<?php echo "pending"; ?>" name="status" checked ><?php echo $status["pending"]; ?>
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" value="<?php echo "approve"; ?>" name="status"><?php echo $status["approve"]; ?>
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" value="<?php echo "disapprove"; ?>" name="status"><?php echo $status["disapprove"]; ?> 
                                            </label>
                                            <span class="text-danger"><?php echo form_error('status'); ?></span>
                                        </td>
                                        <th><?php echo $this->lang->line('reason'); ?></th>
                                        <td><span id="remark"></span></td>
                                    </tr> 
                                    <tr>
                                         <th><?php echo $this->lang->line('download'); ?></th>
                                    <td><span id="download_file"></span></td>
                                    </tr>
                                    <tr>
                                        <th colspan=" 4"><?php echo $this->lang->line('note'); ?> ( <?php echo $this->lang->line('only_display_for_admin'); ?> )</th>
                                    </tr>
                                    <tr>
                                        <td colspan=" 4">
                                            <div id="reason">
                                                <textarea class="form-control" style="resize: none;" rows="2" id="detailremark" name="detailremark" placeholder=""></textarea>
                                                <span class="text-danger"><?php echo form_error('address'); ?></span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <?php
                                        if ($this->rbac->hasPrivilege('approve_leave_request', 'can_edit')) {
                                            ?> 
                                            <td colspan="4">
                                                <button type="button" class="btn btn-primary submit_schsetting pull-right" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                </table>
                            </div>     
                        </div>
                    </form>         
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addleave" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_details'); ?></h4>
            </div>
            <form role="form" id="addleave_form" method="post" enctype="multipart/form-data" action="">
                 <div class="scroll-area">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label>
                                            <?php echo $this->lang->line('staff_role'); ?></label><small class="req"> *</small>
                                        <select name="role" class="form-control" onchange="getEmployeeName(this.value)">
                                            <option value="" selected><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($staffrole as $rolekey => $rolevalue) {
                                                ?>
                                                <option value="<?php echo $rolevalue["id"] ?>"><?php echo $rolevalue["type"] ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('role'); ?></span>
                                    </div>    
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('staff_name'); ?></label><small class="req"> *</small>
                                        <select name="empname" id="empname" value=""onchange="   getLeaveTypeDDL(this.value)"  class="form-control">
                                            <option value="" selected><?php echo $this->lang->line('select') ?></option>
                                        </select> 
                                        <span class="text-danger"><?php echo form_error('empname'); ?></span>
                                    </div>    
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('apply_date'); ?></label><small class="req"> *</small>
                                        <input type="text" id="applieddate" name="applieddate" value="<?php echo date($this->customlib->getHospitalDateFormat()) ?>" class="form-control date">
                                    </div>            
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label>
                                            <?php echo $this->lang->line('leave_type'); ?></label> <small class="req"> *</small>
                                        <div id="leavetypeddl">
                                            <select name="leave_type" id="leave_type" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php foreach ($leavetype as $leave_key => $leave_value) {
                                                    ?>
                                                    <option value="<?php echo $leave_value["id"] ?>"><?php echo $leave_value["type"] ?></option>
                                                <?php }
                                                ?>
                                            </select>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('leave_type'); ?></span>
                                    </div>    
                                </div>                            
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('leave_from_date'); ?></label><span class="req"> *</span>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" readonly name="leave_from_date" class="form-control pull-right date" id="leave_from_date">
                                                </div>
                                                <!-- /.input group -->
                                            </div>    
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('leave_to_date'); ?></label><span class="req"> *</span>
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" readonly name="leave_to_date" class="form-control pull-right date" id="leave_to_date">
                                                </div>
                                                <!-- /.input group -->
                                            </div>    
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('reason'); ?></label><br/>
                                        <textarea name="reason" id="reason" style="resize: none;" rows="4" class="form-control"></textarea>
                                        <input type="hidden" name="leaverequestid" id="leaverequestid">
                                    </div>     
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6" id="reason">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('staff_note'); ?></label>
                                        <textarea class="form-control" style="resize: none;" rows="4" id="remark" name="remark" placeholder=""></textarea>
                                        <span class="text-danger"><?php echo form_error('remark'); ?></span>
                                    </div> 
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input type="file" id="file" name="userfile" class="filestyle form-control">
                                        <input type="hidden" id="filename" name="filename">
                                    </div>         
                                </div>
                                <div class="form-group  col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('status'); ?> </label>
                                        <br/>
                                        <label class="radio-inline">
                                            <input type="radio" value="<?php echo "pending" ?>" name="addstatus" checked><?php echo $status["pending"] ?>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" value="<?php echo "approve" ?>" name="addstatus"><?php echo $status["approve"] ?></label>
                                        <label class="radio-inline">
                                            <input type="radio" value="<?php echo "disapprove" ?>" name="addstatus"><?php echo $status["disapprove"] ?></label>
                                        <span class="text-danger"><?php echo form_error('addstatus'); ?></span>
                                    </div>    
                                </div>
                                <div class="clearfix"></div>              
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="addleave_formbtn" class="btn btn-primary submit_addLeave pull-right mr3" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    <input type="reset" name="resetbutton" id="resetbutton" style="display:none">
                    <button type="button" style="display: none;" id="clearform" onclick="clearForm(this.form)" class="btn btn-primary submit_addLeave pull-right" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"> <?php echo $this->lang->line('clear'); ?></button>
                </div>
            </form>    
        </div>
    </div>
</div>

<script type="text/javascript">
    /*--dropify--*/
    $(document).ready(function () {
        // Basic
        $('.filestyle').dropify();
    });
    /*--end dropify--*/
</script>

<script type="text/javascript">

    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            title: '',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });    

    $(document).on('hidden.bs.modal', '#addleave', function(){
     $(".filestyle").next(".dropify-clear").trigger("click");   
     $('.modal #addleave_form').find('input:text, input:password, input:file, textarea').val('');
     $('.modal #addleave_form').find('select option:selected').removeAttr('selected');
     $('.modal #addleave_form').find('input:checkbox, input:radio').removeAttr('checked');
     $("#applieddate").datepicker("update", new Date());
     $('input:radio[name=addstatus]')[0].checked = true;
     $('.modal #addleave_form #empname').find('option').not(':first').remove();
    });

    function addLeave() {        

        $('#addleave').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    }

    function getRecord(id) {
        $('input:radio[name=status]').attr('checked', false);
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/leaverequest/leaveRecord',
            type: 'POST',
            data: {id: id},
            dataType: "json",
            success: function (result) {
                var leavedate_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy',]) ?>';
                $('input[name="leave_request_id"]').val(result.id);
                $('#employee_id').html(result.employee_id);
                $('#staff_id').val(result.employee_id);
                $('#name').html(result.staffname);               
                $('#leave_from').html(new Date(result.leave_from).toString(leavedate_format));
                $('#leave_to').html(new Date(result.leave_to).toString(leavedate_format));
                $('#leave_type').html(result.type);
                $('#days').html(result.leave_days + ' Days');
                $('#remark').html(result.employee_remark);
                $('#applied_date').html(new Date(result.date).toString(leavedate_format));
                if(result.applier_employee_id !=null){
                    $('#appliedby').html(result.applied_by);                    
                }
                
                $("#detailremark").text(result.admin_remark);
                if (result.status == 'Approved') {
                    $('input:radio[name=status]')[1].checked = true;
                } else if (result.status == 'Pending') {
                    $('input:radio[name=status]')[0].checked = true;
                } else if (result.status == 'Disapprove') {
                    $('input:radio[name=status]')[2].checked = true;
                }

                if (result.document_file != "") {
                    var cl = "<i class='fa fa-download'></i>";
                    $("#download_file").html('<a href=' + base_url + 'admin/staff/download/' + result.staff_id + '/' + result.document_file + ' class=btn btn-default btn-xs  data-toggle=tooltip >' + cl + '</a>');
                }
            }
        });

        $('#leavedetails').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    }
    ;

    $(document).on('click', '.submit_schsetting', function (e) {        
        var $this = $(this);
        $this.button('loading');
        $.ajax({
            url: '<?php echo site_url("admin/leaverequest/leaveStatus") ?>',
            type: 'post',
            data: $('#leavedetails_form').serialize(),
            dataType: 'json',
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

                $this.button('reset');
            }
        });
    });

    function checkStatus(status) {
        if (status == 'approve') {
            $("#reason").hide();
        } else if (status == 'pending') {
            $("#reason").hide();
        } else if (status == 'disapprove') {
            $("#reason").show();
        }
    }

    $(document).ready(function (e) {
        $("#addleave_form").on('submit', (function (e) {           
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/leaverequest/addLeave") ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data)
                {                   
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
                    $("#addleave_formbtn").button('reset');
                }
            });
        }));
    });

    function getEmployeeName(role) {
        var ne = "";
        var base_url = '<?php echo base_url() ?>';
        $("#empname").html('<option value=><?php echo $this->lang->line('select') ?></option>');
        var div_data = "";
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    div_data += "<option value='" + obj.id + "' >" + obj.name + " " + obj.surname + " " + "(" + obj.employee_id + ")</option>";
                });

                $('#empname').append(div_data);
            }
        });
    }

    function setEmployeeName(role, id = '') {
        var ne = "";
        var base_url = '<?php echo base_url() ?>';
        $("#empname").html("<option value=><?php echo $this->lang->line('select') ?></option>");
        var div_data = "";
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    if (obj.employee_id == id) {
                        ne = 'selected';
                    } else {
                        ne = "";
                    }

                    div_data += "<option value='" + obj.id + "' " + ne + " >" + obj.name + " " + obj.surname + " " + "(" + obj.employee_id + ")</option>";
                });

                $('#empname').append(div_data);
            }
        });
    }

    function getLeaveTypeDDL(id, lid = '') {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/leaverequest/countLeave/' + id,
            type: 'POST',
            data: {lid: lid},           
            success: function (result) {
                $("#leavetypeddl").html(result);
            }
        });
    }
    
    $("#leavedetails").on('hidden.bs.modal', function () {
     $('.view_leave_detail table span').html("");
     $(this).find("input,textarea,select").not("input[type=radio]")
     .val('')
     .end();
     $(this).find("input[type=checkbox], input[type=radio]")
     .prop('checked', false);
});

    function editRecord(id) {
        var leave_from = '05/01/2018';
        var leave_to = '05/10/2018';
        $("#resetbutton").click();
        $('textarea[name="reason"]').text('');
        $('textarea[name="remark"]').text('');
        $('input:radio[name=addstatus]').attr('checked', false);
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/leaverequest/leaveRecord',
            type: 'POST',
            data: {id: id},
            dataType: "json",
            success: function (result) {
                leave_from = result.leavefrom;
                leave_to = result.leaveto;
                var daterange_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
                var leavedate_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy',]) ?>';

                setEmployeeName(result.user_type, result.employee_id);
                getLeaveTypeDDL(result.staff_id, result.lid);
                $('select[name="role"] option[value="' + result.user_type + '"]').attr("selected", "selected");
                $('input[name="applieddate"]').val(new Date(result.date).toString(leavedate_format));
                $('input[name="leavefrom"]').val(new Date(result.leave_from).toString(leavedate_format));
                $('input[name="filename"]').val(result.document_file);
                $('input[name="leavedates"]').val(new Date(result.leave_from).toString(leavedate_format) + '-' + new Date(result.leave_to).toString(leavedate_format));
                $('#reservation').daterangepicker({
                    startDate: new Date(result.leave_from).toString(leavedate_format),
                    endDate: new Date(result.leave_to).toString(leavedate_format),
                    locale: {
                        format: daterange_format,
                    },
                });

                $('input[name="leaverequestid"]').val(id);
                $('textarea[name="reason"]').text(result.employee_remark);
                $('textarea[name="remark"]').text(result.admin_remark);
                if (result.status == 'approve') {
                    $('input:radio[name=addstatus]')[1].checked = true;
                } else if (result.status == 'pending') {
                    $('input:radio[name=addstatus]')[0].checked = true;
                } else if (result.status == 'disapprove') {
                    $('input:radio[name=addstatus]')[2].checked = true;
                }
            }
        });

        $('#addleave').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    }
    ;

    function clearForm(oForm) {
        var elements = oForm.elements;
        for (i = 0; i < elements.length; i++) {
            field_type = elements[i].type.toLowerCase();
            switch (field_type) {
                case "text":
                case "password":
                case "hidden":
                    elements[i].value = "";
                    break;
                case "select-one":
                case "select-multi":
                    elements[i].selectedIndex = "";
                    break;
                default:
                    break;
            }
        }
    }

</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/leaverequest/getleaverequestDatatable');
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->