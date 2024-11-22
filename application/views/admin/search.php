<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
$marital_status = $this->config->item('marital_status');
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" name="search_text" id="search_text" value="<?php echo $search_text; ?>">
                <div class="box box-info">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo form_error('Opd'); ?>
                            <?php
                            echo $this->lang->line('patient_list');
                            ?>
                        </h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) { ?>
                                <a data-toggle="modal" onclick="holdModal('myModalpa')" id="addp" class="btn btn-primary btn-sm newpatient"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_new_patient'); ?></a>
                            <?php
                            }
                            if ($this->rbac->hasPrivilege('patient_import', 'can_view')) {
                            ?>
                                <a data-toggle="" href="<?php echo base_url() ?>admin/patient/import" id="addp" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> <?php echo $this->lang->line('import_patient'); ?></a>
                            <?php }
                            if ($this->rbac->hasPrivilege('enabled_disabled', 'can_view')) {
                            ?>
                                <a href="<?php echo base_url() ?>admin/admin/disablepatient" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('disabled_patient_list'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="">
                            <?php if ($this->rbac->hasPrivilege('patient', 'can_delete')) {
                            ?>
                                <button type="submit" class="btn btn-primary pull-right btn-sm mt10 delete_selected" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> "><i class="fa fa-trash"></i> <?php echo $this->lang->line('delete_selected'); ?></button>
                            <?php } ?>
                        </div>

                        <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?= $this->lang->line('patient_list'); ?>">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="checkAll"> #</th>
                                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                                    <th><?php echo $this->lang->line('age'); ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                    <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                    <th><?php echo $this->lang->line('address'); ?></th>
                                    <th><?php echo $this->lang->line('dead'); ?></th>
                                    <?php if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                    ?>
                                            <th><?php echo ucfirst($fields_value->name); ?></th>
                                    <?php }
                                    } ?>
                                    <th class="noExport"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <!-- </form> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-placement="bottom" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>

                <div class="modalicon">

                    <div id='edit_delete'>

                        <?php if ($this->rbac->hasPrivilege('revisit', 'can_edit')) { ?>
                            <a href="#" data-placement="bottom" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <?php
                        }
                        if ($this->rbac->hasPrivilege('revisit', 'can_delete')) {
                        ?>
                            <a href="#" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                        <?php } ?>
                    </div>
                </div>
                <h4 class="modal-title" id="modal_head"></h4>
                <div class="row">
                    <div class="col-sm-4 col-xs-6">
                        <div class="form-group15">
                        </div>
                    </div><!--./col-sm-4-->
                </div><!-- ./row -->
            </div><!--./modal-header-->
            <div class="pup-scroll-area">
                <div class="modal-body pt0 pb0">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <form id="formadd" accept-charset="utf-8" action="<?php echo base_url() . "admin/patient" ?>" enctype="multipart/form-data" method="post">
                                <input name="id" type="hidden" id="patientid">
                                <div class="row row-eq h-vh-lg-100-100">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="row ptt10">
                                            <div class="col-lg-12">
                                                <div class="singlelist24bold pb10">
                                                    <span id="patient_name"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-9 col-sm-9 col-xs-9" id="Myinfo">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <ul class="singlelist">
                                                            <li>
                                                                <i class="fas fa-user-secret" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('guardian'); ?>"></i>
                                                                <span id="guardian"></span>
                                                            </li>
                                                        </ul>
                                                        <ul class="multilinelist">
                                                            <li>
                                                                <i class="fas fa-venus-mars" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('gender'); ?>"></i>
                                                                <span id="genders"></span>
                                                            </li>
                                                            <li>
                                                                <i class="fas fa-tint" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('blood_group'); ?>"></i>
                                                                <span id="blood_group"></span>
                                                            </li>
                                                            <li>
                                                                <i class="fas fa-ring" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('marital_status'); ?>"></i>
                                                                <span id="marital_status"></span>
                                                            </li>
                                                        </ul>
                                                        <ul class="singlelist">
                                                            <li>
                                                                <i class="fas fa-hourglass-half" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('age'); ?>"></i>
                                                                <span id="age"></span> <span id="as_of_date"></span>
                                                            </li>
                                                            <li>
                                                                <i class="fa fa-phone-square" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('phone'); ?>"></i>
                                                                <span id="contact"></span>
                                                            </li>
                                                            <li>
                                                                <i class="fa fa-envelope" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('email'); ?>"></i>
                                                                <span id="email"></span>
                                                            </li>
                                                            <li>
                                                                <i class="fas fa-street-view" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('address'); ?>"></i>
                                                                <span id="address"></span>
                                                            </li>
                                                            <li id="show_barcode" class="qr-d-flex align-items-center pb0">
                                                                <i class="fa fa-barcode" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('barcode'); ?>"></i>
                                                                <span>
                                                                    <a href="" id="getbarcode_link" target="_blank">
                                                                        <img src="" id="getbarcode" width="66" height="32" />
                                                                    </a>
                                                                </span>
                                                            </li>
                                                            <li id="show_qrcode"  class="qr-d-flex align-items-center pb0">
                                                                <i class="fa fa-qrcode" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('qrcode'); ?>"></i>
                                                                <span>
                                                                    <a href="" id="getqrcode_link" target="_blank">
                                                                        <img src="" id="getqrcode" width="60" height="60" />
                                                                    </a>
                                                                </span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <ul class="singlelist">
                                                            <li>
                                                                <b><?php echo $this->lang->line('any_known_allergies') ?> </b>
                                                                <span id="allergies"></span>
                                                            </li>
                                                            <li>
                                                                <b><?php echo $this->lang->line('remarks') ?> </b>
                                                                <span id="note"></span>
                                                            </li>
                                                            <li>
                                                                <b><?php echo $this->lang->line('tpa'); ?> </b>
                                                                <span id="organisation_name"></span>
                                                            </li>
                                                            <li>
                                                                <b><?php echo $this->lang->line('tpa_id'); ?> </b>
                                                                <span id="insurance_id"></span>
                                                            </li>
                                                            <li>
                                                                <b><?php echo $this->lang->line('tpa_validity'); ?> </b>
                                                                <span id="validity"></span>
                                                            </li>
                                                            <li>
                                                                <b><?php echo $this->lang->line('national_identification_number'); ?> </b>
                                                                <span id="identification_number"></span>
                                                            </li>
                                                            <li id="field_data">
                                                                <b><span id="vcustom_name"></span></b>
                                                                <span id="vcustom_value"></span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div><!-- ./col-md-9 -->
                                            <div class="col-lg-3 col-md-3 col-sm-12">
                                                <?php $file = "uploads/patient_images/no_image.png"; ?>
                                                <img class="profile-user-img img-responsive" src="<?php echo base_url() . $file . img_time() ?>" id="image" alt="User profile picture">
                                            </div><!-- ./col-md-3 -->
                                        </div>
                                        <div id="visit_report_id"></div>
                                    </div><!--./col-md-8-->
                                </div><!--./row-->
                            </form>
                        </div><!--./col-md-12-->
                    </div><!--./row-->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_patient_details'); ?></h4>
            </div><!--./modal-header-->
            <form id="formeditpa" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post" class="ptt10">
                <div class="modal-body pt0 pb0">
                    <input id="eupdateid" name="updateid" placeholder="" type="hidden" class="form-control" value="" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                <input id="ename" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('guardian_name') ?></label>
                                <input type="text" name="guardian_name" id="eguardian_name" placeholder="" value="" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label> <?php echo $this->lang->line('gender'); ?></label>
                                        <select class="form-control" name="gender" id="egenders">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($genderList as $key => $value) {
                                            ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="dob"><?php echo $this->lang->line('date_of_birth'); ?></label>
                                        <input type="text" name="dob" placeholder="" class="form-control date editpatient_dob" /><?php echo set_value('dob'); ?>
                                    </div>
                                </div>
                                <div class="col-sm-5" id="calculate">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('age') . ' (' . $this->lang->line('yy_mm_dd') . ')'; ?></label><small class="req"> *</small>
                                        <div style="clear: both;overflow: hidden;">
                                            <input type="text" placeholder="<?php echo $this->lang->line('year'); ?>" name="age[year]" id="age_year" value="" class="form-control patient_age_year" style="width: 30%; float: left;">

                                            <input type="text" id="age_month" placeholder="<?php echo $this->lang->line('month'); ?>" name="age[month]" value="" class="form-control patient_age_month" style="width: 36%;float: left; margin-left: 4px;">
                                            <input type="text" id="age_day" placeholder="<?php echo $this->lang->line('day'); ?>" name="age[day]" value="" class="form-control patient_age_day" style="width: 26%;float: left; margin-left: 4px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--./col-md-6-->
                        <div class="col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> <?php echo $this->lang->line('blood_group'); ?></label>
                                        <select class="form-control" id="blood_groups" name="blood_group">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($bloodgroup as $key => $value) {
                                            ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) {
                                                                                        echo "selected";
                                                                                    }
                                                                                    ?>><?php echo $value; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('marital_status'); ?></label>
                                        <select name="marital_status" id="marital_statuss" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($marital_status as $key => $value) {
                                            ?>
                                                <option value="<?php echo $value; ?>" <?php if (set_value('marital_status') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                            <?php echo $this->lang->line('patient_photo'); ?>
                                        </label>
                                        <div>
                                            <input class="filestyle form-control-file" type='file' name='file' id="exampleInputFile" size='20' data-height="26" data-default-file="<?php echo base_url() ?>uploads/patient_images/no_image.png">
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div><!--./col-md-6-->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                <input id="emobileno" autocomplete="off" name="contact" type="text" placeholder="" class="form-control" value="<?php echo set_value('mobileno'); ?>" />
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('email'); ?></label>
                                <input type="text" placeholder="" id="eemail" value="<?php echo set_value('email'); ?>" name="email" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="address"><?php echo $this->lang->line('address'); ?></label>
                                <input name="address" id="eaddress" placeholder="" class="form-control" /><?php echo set_value('address'); ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="pwd"><?php echo $this->lang->line('remarks'); ?></label>
                                <textarea name="note" id="enote" class="form-control"><?php echo set_value('note'); ?></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="email"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                <textarea name="known_allergies" id="eknown_allergies" placeholder="" class="form-control"><?php echo set_value('address'); ?></textarea>
                            </div>
                        </div>
                        <div class="col-sm-4">
                           <div class="form-group">
                               <label for="tpa"><?php echo $this->lang->line("tpa"); ?></label>
                               <select class="form-control" name='organisation_id' id="edit_organisation_id" >
                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                    <?php foreach ($organisation as $orgkey => $orgvalue) {  ?>
                                    <option value="<?php echo $orgvalue["id"]; ?>"><?php echo $orgvalue["organisation_name"] ?></option>
                                    <?php }   ?>
                                </select>
                           </div>
                         </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label><?php echo $this->lang->line("tpa_id"); ?></label>
                                <input name="insurance_id" id="edit_insurance_id" placeholder="" class="form-control" /><?php echo set_value('insurance_id'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label><?php echo $this->lang->line("tpa_validity"); ?></label>
                                <input name="validity" placeholder="" id="insurance_validity" class="form-control date" /><?php echo set_value('validity'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label><?php echo $this->lang->line("national_identification_number"); ?></label>
                                <input name="identification_number" placeholder="" id="edit_identification_number" class="form-control" /><?php echo set_value('identification_number'); ?>
                            </div>
                        </div>
                        <div class="" id="customfield"></div>
                    </div><!--./row-->
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" id="formeditpabtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- //========datatable start===== -->
<script type="text/javascript">
    (function($) {
        'use strict';
        $(document).ready(function() {
            var search_text = $('#search_text').val();
            initDatatable('ajaxlist', 'admin/admin/getpatientdatatable', {
                'search_text': search_text
            }, [], 100, [{
                "bSortable": false,
                "aTargets": [0, 8]
            }]);
        })
    }(jQuery))
</script>
<!-- //========datatable end===== -->
<script type="text/javascript">
    function showdate(value) {
        if (value == 'period') {
            $('#fromdate').show();
            $('#todate').show();
        } else {
            $('#fromdate').hide();
            $('#todate').hide();
        }
    }

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

    function getpatientData(id) {
        $('#modal_head').html("<?php echo $this->lang->line('patient_details'); ?>");
        $.ajax({
            url: baseurl + 'admin/patient/getpatientDetails',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {

                if (data.is_active == 'yes') {
                    var link = "<?php if ($this->rbac->hasPrivilege('enabled_disabled', 'can_view')) { ?><a href='#' data-toggle='tooltip' title='<?php echo $this->lang->line('disable'); ?>' onclick='patient_deactive(" + id + ")' data-placement='bottom' data-original-title='<?php echo $this->lang->line('disable'); ?>'><i class='fa fa-thumbs-o-down'></i></a><?php } ?>";
                } else {
                    var link = "<?php if ($this->rbac->hasPrivilege('enabled_disabled', 'can_view')) { ?><a href='#' data-toggle='tooltip' title='<?php echo $this->lang->line('enable'); ?>' onclick='patient_active(" + id + ")' data-placement='bottom' data-original-title='<?php echo $this->lang->line('enable'); ?>'><i class='fa fa-thumbs-o-up'></i></a> <?php }

                    if ($this->rbac->hasPrivilege('patient', 'can_delete')) { ?><a href='#' data-toggle='tooltip'  onclick='delete_record(" + id + ")' data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a> <?php } ?>";
                }

                var table_html = '';
                $.each(data.field_data, function(i, obj) {
                    if (obj.field_value == null) {
                        var field_value = "";
                    } else {
                        var field_value = obj.field_value;
                    }
                    var name = obj.name;
                    table_html += "<p><b><span id='vcustom_name'>" + capitalizeFirstLetter(name) + "</span></b> <span id='vcustom_value'>" + field_value + "</span></p>";
                });

                $("#field_data").html(table_html);
                $("patientid").val(data.id);
                $("#patient_name").html(data.patient_name + " (" + data.id + ")");
                $("#guardian").html(data.guardian_name);
                $("#patients_id").html(data.patient_unique_id);
                $("#genders").html(data.gender);
                $("#marital_status").html(data.marital_status);
                $("#contact").html(data.mobileno);
                $("#email").html(data.email);
                $("#address").html(data.address);
                $("#is_active").html(data.is_active);
                $('select[id="blood_groups"] option[value="' + data.blood_bank_product_id + '"]').attr("selected", "selected");
                $("#age").html(data.patient_age);
                $("#as_of_date").html(data.as_of_date);
                $("#allergies").html(data.known_allergies); 
                $("#insurance_id").html(data.insurance_id);
                $("#validity").html(data.insurance_validity);
                $("#organisation_name").html(data.organisation_name);
                $('select[id="edit_organisation_id"] option[value="' + data.organisation_id + '"]').attr("selected", "selected");
				
                $("#identification_number").html(data.identification_number);
                $("#blood_group").html(data.blood_group_name);
                $("#note").html(data.note);
                $("#image").attr("src", '<?php echo base_url() ?>' + data.image + '<?php echo img_time(); ?>');
                $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('patient', 'can_edit')) { ?><a href='#' onclick='editRecord(" + id + ")' data-toggle='tooltip' data-placement='bottom' title='<?php echo $this->lang->line('edit'); ?>' data-target='' data-toggle='modal'   data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } ?> " + link + "");
                if (data.getbarcode == null) {
                    $("#show_barcode").addClass('hide');
                    $("#getbarcode").attr("src", '');
                    $("#getbarcode_link").attr("href", '');
                } else {
                    $("#show_barcode").removeClass('hide');
                    $("#getbarcode").attr("src", data.getbarcode);
                    $("#getbarcode_link").attr("href", data.getbarcode);
                }
                if (data.getqrcode == null) {
                    $("#show_qrcode").addClass('hide');
                    $("#getqrcode").attr("src", '');
                    $("#getqrcode_link").attr("href", '');
                } else {
                    $("#show_qrcode").removeClass('hide');
                    $("#getqrcode").attr("src", data.getqrcode);
                    $("#getqrcode_link").attr("href", data.getqrcode);
                }
                holdModal('myModal');
                patientvisit(id);
            },
        });
    }

    function patientvisit(id) {
        $.ajax({
            url: baseurl + 'admin/patient/patientvisit',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                $('#visit_report_id').html(data);
            }
        });
    }

    function editRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientDetails',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {

                $("#eupdateid").val(data.id);
                $('#customfield').html(data.custom_fields_value);
                $("#ename").val(data.patient_name);
                $("#eguardian_name").val(data.guardian_name);
                $("#emobileno").val(data.mobileno);
                $("#eemail").val(data.email);
                $("#eaddress").val(data.address);
                $("#age_year").val(data.age);
                $("#age_month").val(data.month);
                $("#age_day").val(data.day);
                $(".editpatient_dob").val(data.dob);
                $("#enote").val(data.note);
                $("#exampleInputFile").attr("data-default-file", '<?php echo base_url() ?>' + data.image);
                $(".dropify-render").find("img").attr("src", '<?php echo base_url() ?>' + data.image);
                $("#eknown_allergies").val(data.known_allergies);
                $('select[id="blood_groups"] option[value="' + data.blood_bank_product_id + '"]').attr("selected", "selected");
                $('select[id="egenders"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_statuss"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                $("#edit_insurance_id").val(data.insurance_id);
                $("#insurance_validity").val(data.insurance_validity);
                $("#edit_identification_number").val(data.identification_number);
                $("#blood_group").html(data.blood_group_name);
                $("#myModal").modal('hide');
                holdModal('editModal');

            },
        });
    }

    $(document).ready(function(e) {
        $("#formeditpa").on('submit', (function(e) {
            $("#formeditpabtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/update',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formeditpabtn").button('reset');
                },
                error: function() {

                }
            });
        }));
    });

    function delete_record(id) {
        if (confirm(<?php echo "'" . $this->lang->line('patient_delete_alert_message') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deletePatient',
                type: "POST",
                data: {
                    delid: id
                },
                dataType: 'json',
                success: function(data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    $("#myModal").modal("hide");
                    table.ajax.reload();
                }
            })
        }
    }

    function patient_deactive(id) {
        if (confirm(<?php echo "'" . $this->lang->line('are_you_sure_to_deactivate_account') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deactivePatient',
                type: "POST",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == "fail") {
                        var message = (data.message);
                        errorMsg(message);
                    } else {
                        successMsg(<?php echo "'" . $this->lang->line('record_disable') . "'"; ?>);
                        window.getpatientData(id);
                    }
                }
            })
        }
    }

    function CalculateAgeInQCe(DOB, txtAge, Txndate) {
        if (DOB.value != '') {
            now = new Date(Txndate)
            var txtValue = DOB;
            if (txtValue != null)
                dob = txtValue.split('/');
            if (dob.length === 3) {
                born = new Date(dob[2], dob[1] * 1 - 1, dob[0]);
                if (now.getMonth() == born.getMonth() && now.getDate() == born.getDate()) {
                    age = now.getFullYear() - born.getFullYear();
                } else {
                    age = Math.floor((now.getTime() - born.getTime()) / (365.25 * 24 * 60 * 60 * 1000));
                }
                if (isNaN(age) || age < 0) {

                } else {
                    if (now.getMonth() > born.getMonth()) {
                        var calmonth = now.getMonth() - born.getMonth();
                    } else {
                        var calmonth = born.getMonth() - now.getMonth();
                    }
                    $("#eage_year").val(age);
                    $("#eage_month").val(calmonth);
                    return age;
                }
            }
        }
    }

    function patient_active(id) {
        if (confirm(<?php echo "'" . $this->lang->line('are_you_sure_to_active_account') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/activePatient',
                type: "POST",
                data: {
                    activeid: id
                },
                dataType: 'json',
                success: function(data) {
                    successMsg(<?php echo "'" . $this->lang->line('record_active') . "'"; ?>);
                    window.getpatientData(id);
                }
            })
        }
    }

    $(document).on('click', '.delete_selected', function() {
        var $this = $(this);
        let obj = [];
        $('input:checkbox.enable_delete').each(function() {
            (this.checked ? obj.push($(this).val()) : "");
        });
        if (confirm('<?php echo $this->lang->line('patient_delete_alert_message'); ?>')) {
            $.ajax({
                url: base_url + 'admin/patient/bulk_delete',
                type: "POST",
                dataType: 'json',
                data: {
                    'delete_id': obj
                },
                beforeSend: function() {
                    $this.button('loading');
                },
                success: function(res) {
                    $this.button('reset');
                    if (res.status == 1) {
                        successMsg(res.msg);
                        table.ajax.reload();
                    } else {
                        var message = "";
                        $.each(res.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    }
                },
                error: function(xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occured_please_try_again'); ?>");
                    $this.button('reset');
                },
                complete: function() {
                    $this.button('reset');

                }
            });
        }
    });
</script>
<script type="text/javascript">
    $(".newpatient").click(function() {
        $('#formaddpa').trigger("reset");
        $(".dropify-clear").trigger("click");
    });

    $(".modalbtnpatient").click(function() {
        $('#formaddpa').trigger("reset");
        $(".dropify-clear").trigger("click");
    });

    $("input[name='checkAll']").click(function() {
        $("input[name='patient[]']").not(this).prop('checked', this.checked);
    });

    $(".editpatient_dob").on('changeDate', function(event, date) {
        var birth_date = $(".editpatient_dob").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientage',
            type: "POST",
            dataType: "json",
            data: {
                birth_date: birth_date
            },
            success: function(data) {
                $('.patient_age_year').val(data.year);
                $('.patient_age_month').val(data.month);
                $('.patient_age_day').val(data.day);
            }
        });
    });
</script>
<?php
$genderList = $this->customlib->getGender_Patient();
$marital_status = $this->config->item('marital_status');
?>


<div class="modal fade" id="myModalpa" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_patient'); ?></h4>
            </div>
            <form id="formaddpa" accept-charset="utf-8" action="" enctype="multipart/form-data" method="post">
                <div class="scroll-area">
                    <div class="modal-body pt0 pb0">
                        <div class="ptt10">
                            <div class="row row-eq h-vh-lg-100-100-1">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                                <input id="name" name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('guardian_name') ?></label>
                                                <input type="text" name="guardian_name" placeholder="" value="" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label> <?php echo $this->lang->line('gender'); ?></label>
                                                        <select class="form-control" name="gender" id="addformgender">
                                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                            <?php
                                                            foreach ($genderList as $key => $value) {
                                                            ?>
                                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="dob"><?php echo $this->lang->line('date_of_birth'); ?></label>
                                                        <input type="text" name="dob" id="birth_date" placeholder="" class="form-control date patient_dob" /><?php echo set_value('dob'); ?>
                                                    </div>
                                                </div>
                                                <div class="col-sm-5" id="calculate">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('age') . ' (' . $this->lang->line('yy_mm_dd') . ')'; ?> </label><small class="req"> *</small>
                                                        <div style="clear: both;overflow: hidden;">
                                                            <input type="text" placeholder="<?php echo $this->lang->line('year'); ?>" name="age[year]" id="age_year" value="" class="form-control patient_age_year" style="width: 30%; float: left;">

                                                            <input type="text" id="age_month" placeholder="<?php echo $this->lang->line('month'); ?>" name="age[month]" value="" class="form-control patient_age_month" style="width: 36%;float: left; margin-left: 4px;">
                                                            <input type="text" id="age_day" placeholder="<?php echo $this->lang->line('day'); ?>" name="age[day]" value="" class="form-control patient_age_day" style="width: 26%;float: left; margin-left: 4px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--./col-md-6-->
                                        <div class="col-md-6 col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('blood_group'); ?></label>
                                                        <select name="blood_group" class="form-control">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php
                                                            foreach ($bloodgroup as $key => $value) {
                                                            ?>
                                                                <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) {
                                                                                                        echo "selected";
                                                                                                    }
                                                                                                    ?>><?php echo $value; ?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="pwd"><?php echo $this->lang->line('marital_status'); ?></label>
                                                        <select name="marital_status" class="form-control">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($marital_status as $mkey => $mvalue) {
                                                            ?>
                                                                <option value="<?php echo $mvalue; ?>" <?php if (set_value('marital_status') == $mkey) echo "selected"; ?>><?php echo $mvalue; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputFile">
                                                            <?php echo $this->lang->line('patient_photo'); ?>
                                                        </label>
                                                        <div><input class="filestyle form-control" type='file' name='file' id="file" size='20' data-height="26" />
                                                        </div>
                                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--./col-md-6-->
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                                <input id="number" autocomplete="off" name="mobileno" type="text" placeholder="" class="form-control" value="<?php echo set_value('mobileno'); ?>" />
                                                <span class="text-danger"><?php echo form_error('mobileno'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('email'); ?></label>
                                                <input type="text" placeholder="" id="addformemail" value="<?php echo set_value('email'); ?>" name="email" class="form-control">
                                                <span class="text-danger"><?php echo form_error('email'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="address"><?php echo $this->lang->line('address'); ?></label>
                                                <input name="address" placeholder="" class="form-control" /><?php echo set_value('address'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('remarks'); ?></label>
                                                <textarea name="note" id="note" class="form-control"><?php echo set_value('note'); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="email"><?php echo $this->lang->line('any_known_allergies'); ?></label>
                                                <textarea name="known_allergies" id="" placeholder="" class="form-control"><?php echo set_value('known_allergies'); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="tpa"><?php echo $this->lang->line("tpa"); ?></label>
                                                <select class="form-control" name='organisation_id'>
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($organisation as $orgkey => $orgvalue) {
                                                        ?>
                                                            <option value="<?php echo $orgvalue["id"]; ?>"><?php echo $orgvalue["organisation_name"] ?></option>
                                                        <?php } 
                                                        ?>
                                                    </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="insurance_id"><?php echo $this->lang->line("tpa_id"); ?></label>
                                                <input name="insurance_id" placeholder="" class="form-control" /><?php echo set_value('insurance_id'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="validity"><?php echo $this->lang->line("tpa_validity"); ?></label>
                                                <input name="validity" placeholder="" class="form-control date" /><?php echo set_value('validity'); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line("national_identification_number"); ?></label>
                                                <input name="identification_number" placeholder="" class="form-control" /><?php echo set_value('identification_number'); ?>
                                            </div>
                                        </div>
                                        <div>
                                            <?php
                                            echo display_custom_fields('patient');
                                            ?>
                                        </div>
                                    </div><!--./row-->
                                </div><!--./col-md-8-->
                            </div><!--./row-->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" id="formaddpabtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="viewDetailReportModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-placement="bottom" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='action_detail_report_modal'>

                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('bill_details'); ?></h4>
            </div>
            <div class="modal-body ptt10 pb0">
                <div id="reportbilldata"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="viewModalBill" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('bill ') . " " . $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="viewModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('bill_details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0 min-h-3">
                <div id="pharmacy_reportdata"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(e) {
        $("#formaddpa").on('submit', (function(e) {
            let clicked_submit_btn = $(this).closest('form').find(':submit');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/addpatient',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    clicked_submit_btn.button('loading');
                },
                success: function(data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    clicked_submit_btn.button('reset');
                },
                error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
                    clicked_submit_btn.button('reset');
                },
                complete: function() {
                    clicked_submit_btn.button('reset');
                }
            });
        }));
    });

    function addappointmentModal(patient_id = '', modalid) {
        var div_data = '';
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientDetails',
            type: "POST",
            data: {
                id: patient_id
            },
            dataType: 'json',
            success: function(data) {
                var option = new Option(data.patient_name + " (" + data.id + ")", data.id, true, true);
                $(".patient_list_ajax").append(option).trigger('change');
                $("#" + modalid).modal('show');
                holdModal(modalid);
            }
        })
    }
</script>
<script type="text/javascript">
    $(".patient_dob").on('changeDate', function(event, date) {
        var birth_date = $(".patient_dob").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientage',
            type: "POST",
            dataType: "json",
            data: {
                birth_date: birth_date
            },
            success: function(data) {
                $('.patient_age_year').val(data.year);
                $('.patient_age_month').val(data.month);
                $('.patient_age_day').val(data.day);
            }
        });
    });
</script>
<script>
    $(document).on('click', '.view_detail', function() {
        var id = $(this).data('recordId');
        var module_name = $(this).data('moduleType');
        PatientPathologyDetails(id, $(this), module_name);
    });

    function PatientPathologyDetails(id, btn_obj, module_name) {
        var modal_view = $('#viewDetailReportModal');
        var $this = btn_obj;
        $.ajax({
            url: base_url + 'admin/patient/getPatientPathologyDetails',
            type: "POST",
            data: {
                'id': id,
                'module_name': module_name
            },
            dataType: 'json',
            beforeSend: function() {
                $this.button('loading');
                modal_view.addClass('modal_loading');

            },
            success: function(data) {

                $('#viewDetailReportModal .modal-body').html(data.page);
                $('#viewDetailReportModal #action_detail_report_modal').html(data.actions);
                $('#viewDetailReportModal').modal('show');
                modal_view.removeClass('modal_loading');
            },

            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.button('reset');
                modal_view.removeClass('modal_loading');
            },
            complete: function() {
                $this.button('reset');
                modal_view.removeClass('modal_loading');

            }
        });
    }
</script>
<script>
    document.getElementById("headreport").style.display = "block";
    document.getElementById("print").style.display = "block";
    document.getElementById("btnExport").style.display = "block";
    document.getElementById("printhead").style.display = "none";

    function printDiv() {
        document.getElementById("print").style.display = "none";
        document.getElementById("btnExport").style.display = "none";
        var divElements = document.getElementById('visit_report').innerHTML;
        var oldPage = document.body.innerHTML;
        document.body.innerHTML =
            "<html><head><title>Patient Bill Report</title></head><body>" +
            divElements + "</body>";
        window.print();
        document.body.innerHTML = oldPage;
        location.reload(true);
    }
</script>
<script>
    var array1 = new Array();
    var array2 = new Array();
    var array3 = new Array();
    var array4 = new Array();
    var array5 = new Array();
    var array6 = new Array();
    var array7 = new Array();
    var n = 7; //Total table
    for (var x = 1; x <= n; x++) {
        array1[x - 1] = x;
        array2[x - 1] = x + 'th';
    }

    var tablesToExcel = (function() {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>',
            templateend = '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>',
            body = '<body>',
            tablevar = '<table>{table',
            tablevarend = '}</table>',
            bodyend = '</body></html>',
            worksheet = '<x:ExcelWorksheet><x:Name>',
            worksheetend = '</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>',
            worksheetvar = '{worksheet',
            worksheetvarend = '}',
            base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            },
            format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            },
            wstemplate = '',
            tabletemplate = '';

        return function(table, name, filename) {
            var tables = table;
            for (var i = 0; i < tables.length; ++i) {
                wstemplate += worksheet + worksheetvar + i + worksheetvarend + worksheetend;
                tabletemplate += tablevar + i + tablevarend;
            }

            var allTemplate = template + wstemplate + templateend;
            var allWorksheet = body + tabletemplate + bodyend;
            var allOfIt = allTemplate + allWorksheet;
            var ctx = {};
            for (var j = 0; j < tables.length; ++j) {
                ctx['worksheet' + j] = name[j];
            }

            for (var k = 0; k < tables.length; ++k) {
                var exceltable;
                if (!tables[k].nodeType) exceltable = document.getElementById(tables[k]);
                ctx['table' + k] = exceltable.innerHTML;
            }

            window.location.href = uri + base64(format(allOfIt, ctx));
        }
    })();
</script>