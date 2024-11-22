<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender_Patient();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                            
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('birth_record'); ?></h3>
                        <div class="box-tools addmeeting box-tools-md">
                            <?php
                            if ($this->rbac->hasPrivilege('birth_record', 'can_add')) {
                                ?>
                                <a data-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm birthrecord"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_birth_record'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('birth_record'); ?></div>
                        <table class="table table-striped table-bordered table-hover ajaxlist"  cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('birth_record'); ?>">
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line('reference_no'); ?></th>
                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                <th><?php echo $this->lang->line('child_name'); ?></th>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <th><?php echo $this->lang->line('birth_date'); ?></th>
                                <th><?php echo $this->lang->line('mother_name'); ?></th>
                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                    <?php 
                                    if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                            ?>
                                            <th><?php echo $fields_value->name; ?></th>
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
    </section>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_birth_record'); ?></h4>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post" class="ptt10">
                <div class="scroll-area">
                    <div class="modal-body pb0 pt0">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('child_name'); ?></label><small class="req"> *</small>
                                        <input type="text" name="child_name" id="child_name" class="form-control">
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label> <?php echo $this->lang->line('gender'); ?></label><small class="req"> *</small>
                                        <select class="form-control" name="gender">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                                foreach ($genderList as $key => $value) {?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $value; ?>
                                                </option>
                                                <?php } ?>
                                        </select> 
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('weight'); ?></label> <small class="req">*</small>
                                        <input type="text" name="weight" id="weight" class="form-control">
                                        <span class="text-danger"><?php echo form_error('weight'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                            <?php echo $this->lang->line('child_photo'); ?>
                                        </label>
                                        <div><input class="filestyle form-control" type='file' name='child_img' id="child_img" size='20' data-height="26" />
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('birth_date'); ?></label>
                                        <small class="req">*</small>
                                        <input id="datetimepicker" name="birth_date" type="text" class="form-control datetime"   />
                                        <span class="text-danger"><?php echo form_error('birth_date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('phone'); ?></label>
                                        <input type="text" name="contact" id="contact" class="form-control">
                                        <span class="text-danger"><?php echo form_error('contact'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('address'); ?></label>
                                        <input type="text" name="address" id="address" class="form-control">
                                        <span class="text-danger"><?php echo form_error('address'); ?></span>
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('case_id'); ?></label>
                                        <input type="text"  name="case_id" id="case_id" class="form-control">
                                        <span class="text-danger"><?php echo form_error('case_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for=""><?php echo $this->lang->line('mother_name'); ?>   </label><small class="req"> *</small>
                                        <div>
                                            <input type="text" class="form-control" name="mother" id="mother" readonly="" >
                                            <input type="hidden" id="mother_name" name="mother_name"  value="" class="form-control">
                                            <span class="text-danger"><?php echo form_error('mother'); ?></span>
                                        </div>
                                    </div>
                                </div>                               
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                        <?php echo $this->lang->line('mother_photo'); ?>
                                        </label>
                                        <div><input class="filestyle form-control" type='file' name='first_img' id="first_img" size='20' data-height="26" />
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('father_name'); ?></label>
                                        <input type="text" name="father_name" id="father_name" class="form-control">
                                        <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                            <?php echo $this->lang->line('father_photo'); ?>
                                        </label>
                                        <div><input class="filestyle form-control" type='file' name='second_img' id="second_img" size='20' data-height="26" />
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('report'); ?></label>
                                        <textarea name="birth_report" id="birth_report" class="form-control" ><?php echo set_value('birth_report'); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                        <?php echo $this->lang->line('attach_document_photo'); ?>
                                        </label>
                                        <div><input class="filestyle form-control" type='file' name='document' id="document" size='20' data-height="26" />
                                        </div>
                                        <span class="text-danger"><?php echo form_error('file'); ?></span>
                                    </div>
                                </div>
                                <div>
                                <?php
                                echo display_custom_fields('birth_report');
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
                <h4 class="modal-title"><?php echo $this->lang->line('birth_record_details'); ?></h4>
            </div>
            <form id="view" accept-charset="utf-8" method="get" class="ptt10">
                <div class="modal-body pb0 pt0">
                        <div class="table-responsive">
                                <table class="table mb0 table-striped table-bordered">
                                    <tr>
                                        <th><?php echo $this->lang->line('child_name'); ?></th>
                                        <th><?php echo $this->lang->line('mother_name'); ?></th>
                                        <th><?php echo $this->lang->line('father_name'); ?></th>
                                    </tr>
                                    <tr>
                                        <td><span id='vchild_name'></span></td>
                                        <td><span id="vmother_name"></span></td>
                                        <td><span id="vfather_name"></span></td>
                                    </tr>
                                    <tr>
                                        <td>
<?php
$file = "uploads/patient_images/no_image.png";
?>
                                            <div class="childimg">
                                                <img src="<?php echo base_url() . $file.img_time() ?>" id="image" alt="User profile picture">
                                            </div>
                                            
                                        </td>
                                        <td>
                                            <?php
$file = "uploads/patient_images/no_image.png";
?>
                                            <div class="childimg"><img src="<?php echo base_url() . $file.img_time() ?>" id="imagem" alt="User profile picture"></div>
                                           
                                        </td>
                                        <td>
                                            <?php
$file = "uploads/patient_images/no_image.png";
?>
                                            <div class="childimg"><img class="" src="<?php echo base_url() . $file.img_time() ?>" id="imagef" alt="User profile picture"></div>

                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line("case_id"); ?></th>
                                        <td><span id='vcase_id'></span></td>
                                        <th><?php echo $this->lang->line('birth_date'); ?></th>
                                        <td><span id="vbirth_date"></span>
                                        </td>
                                      </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('weight'); ?></th>
                                        <td><span id='vweight'></span></td>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <td><span id='vgender'></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <td><span id='vcontact'></span></td>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <td><span id='vaddress'></span></td>
                                        </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('document'); ?></th>
                                        <td><span id='download_document'></span> </td>
                                   
                                        <th><?php echo $this->lang->line('report'); ?></th>
                                        <td><span id='vreport'></span></td>
                                        
                                    </tr>
                                    
                                </table>
                                <div id="field_data">
                                        
                                </div>
                        </div>                   
                    <div id="tabledata"></div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right paddA10">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="myModaledit" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_birth_record'); ?></h4>
            </div>
             <form id="formedit" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="ptt10">
                   <div class="scroll-area">
                        <div class="modal-body pt0 pb0">
                                <div class="row">
                                    <input type="hidden" name="id" id="eid" value="<?php echo set_value('id'); ?>">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('child_name'); ?></label><small class="req"> *</small>
                                            <input type="text" value="<?php echo set_value('child_name'); ?>" name="child_name" id="echild_name" class="form-control">
                                            <span class="text-danger"><?php echo form_error('child_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label> <?php echo $this->lang->line('gender'); ?></label>
                                            <small class="req"> *</small>
                                            <select class="form-control" name="gender" id="gender">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                foreach ($genderList as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $value; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('weight'); ?></label><small class="req"> *</small>
                                            <input type="text" value="<?php echo set_value('weight'); ?>" name="weight" id="eweight" class="form-control">
                                            <span class="text-danger"><?php echo form_error('weight'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="exampleInputFile">
                                                <?php echo $this->lang->line('child_photo'); ?>
                                            </label>
                                            <div><input class="filestyle form-control" type='file' name='child_img' id="echild_img" value="<?php echo set_value('child_pic'); ?>" size='20' data-height="26" />
                                            </div>
                                            <span class="text-danger"><?php echo form_error('file'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('birth_date'); ?></label>
                                            <small class="req">*</small>
                                            <input value="<?php echo set_value('birth_date'); ?>" id="ebirth_date" name="birth_date" placeholder="" type="text" class="form-control datetime"   />
                                            <span class="text-danger"><?php echo form_error('birth_date'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('phone'); ?></label>
                                            <input type="text" value="<?php echo set_value('contact'); ?>" name="contact" id="econtact" class="form-control">
                                            <span class="text-danger"><?php echo form_error('contact'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('address'); ?></label>
                                            <input type="text" value="<?php echo set_value('address'); ?>" name="address" id="eaddress" class="form-control">
                                            <span class="text-danger"><?php echo form_error('address'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                                <label><?php echo $this->lang->line("case_id"); ?></label>
                                                <input type="text" onchange="case_reference()" name="case_id" value="<?php echo set_value('case_id'); ?>" id="ecase_id" class="form-control">
                                                <span class="text-danger"><?php echo form_error('ipd'); ?></span>
                                            </div>
                                    </div>        
                                    
                                    <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('mother_name'); ?></label><small class="req"> *</small>
                                        
                                        <div>
                                            <input type="text" class="form-control" name="mother" id="mother" >
                                            <input type="hidden" id="mother_name" name="mother_name" value="" class="form-control">
                                            <span class="text-danger"><?php echo form_error('mother'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleInputFile"><?php echo $this->lang->line('mother_photo'); ?>
                                            </label>
                                            <div><input class="filestyle form-control" type='file' name='mother_pic' id="emother_pic" value="<?php echo set_value('mother_pic'); ?>" size='20' data-height="26" />
                                            </div>
                                            <span class="text-danger"><?php echo form_error('file'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('father_name'); ?></label>
                                            <input type="text" value="<?php echo set_value('father_name'); ?>" name="father_name" id="efather_name" class="form-control">
                                            <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleInputFile">
                                            <?php echo $this->lang->line('father_photo'); ?>
                                            </label>
                                            <div><input class="filestyle form-control" type='file' name='father_pic' id="efather_pic" value="<?php echo set_value('father_pic'); ?>" size='20' data-height="26" />
                                            </div>
                                            <span class="text-danger"><?php echo form_error('file'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email"><?php echo $this->lang->line('report'); ?></label>
                                            <textarea name="birth_report" id="ebirth_report" class="form-control" ><?php echo set_value('birth_report'); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleInputFile">
                                                <?php echo $this->lang->line('attach_document_photo'); ?>
                                            </label>
                                            <div><input class="filestyle form-control" type='file' name='document' id="document" value="<?php echo set_value('document'); ?>" size='20' data-height="26" />
                                            </div>
                                            <span class="text-danger"><?php echo form_error('file'); ?></span>
                                        </div>
                                    </div>
                                    <div id="customfield">
                                    
                                    </div>
                                </div>                                
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
    });

    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
    $(function () {
        $('#easySelectable').easySelectable();
    });
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
    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {
           
            e.preventDefault();
            $.ajax({
                url: baseurl+'admin/birthordeath/addBirthdata',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                 beforeSend: function() {
        $("#formaddbtn").button('loading');
    },
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
    error: function(xhr) { // if error occured
      $("#formaddbtn").button('reset');
        alert("Error occured.please try again");
    },
    complete: function() {
       $("#formaddbtn").button('reset');
    }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#formedit").on('submit', (function (e) {
            $("#formeditbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: baseurl+'admin/birthordeath/update_birth',
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

    function get_PatientDetails(id) {
        $.ajax({
            url: baseurl+'admin/pharmacy/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#patient_name').val(res.patient_name);
                    $('#patient_id').val(res.id);
                } else {
                    $('#patient_name').val('Null');
                }
            }
        });
    }
$(".mother_name").on("select2:select", function (e) { 
  var select_val = $(e.currentTarget).val();

});

    function viewDetail(id) {
        $('#viewModal').modal('show');
        $.ajax({
            url: baseurl+'admin/birthordeath/getBirthdata',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#vid").html(data.id);
                $("#vchild_name").html(data.child_name);
                $("#vbirth_date").html(data.birth_date);
                $("#vweight").html(data.weight);
                $("#vmother_name").html(data.patient_name+' ('+data.patient_id+')');
                $("#vcontact").html(data.contact);
                $("#vaddress").html(data.address);
                $("#vreport").html(data.birth_report);
                $("#vcase_id").html(data.case_reference_id);
                $("#vgender").html(data.gender);
                $("#vfather_name").html(data.father_name);
                $("#vbirth_report").html(data.birth_report);
                $("#image").attr("src", '<?php echo base_url() ?>' + data.child_pic+'<?php echo img_time(); ?>');
                $("#imagem").attr("src", '<?php echo base_url() ?>' + data.mother_pic+'<?php echo img_time(); ?>');
                $("#imagef").attr("src", '<?php echo base_url() ?>' + data.father_pic+'<?php echo img_time(); ?>');
                var downloadid = data.document;
                var table_html = '';
                
                $.each(data.field_data, function (i, obj)
                {
                    if (obj.field_value == null) {
                        var field_value = "";
                    } else {
                        var field_value = obj.field_value;
                    }
                     var name = obj.name ;
                    table_html += "<table width='100%' class='table mb0 table-striped table-bordered'><tr><th width='17%'><span id='vcustom_name'>" + capitalizeFirstLetter(name) + "</span></th><td ><span id='vcustom_value'>" + field_value + "</span></td><tr></table>";
                });
                $("#field_data").html(table_html);
                $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('birth_record', 'can_view')) {?><a href='#' data-toggle='tooltip' onclick='printData(" + id + ")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php }?><?php if ($this->rbac->hasPrivilege('birth_record', 'can_edit')) {?><a href='#'' onclick='getRecord(" + id + ")' data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php }?><?php if ($this->rbac->hasPrivilege('birth_record', 'can_delete')) {?><a onclick='delete_bill(" + id + ")'  href='#'  data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php }?>");
                if (data.document) {
                    $('#download_document').html("<a href='<?php echo base_url(); ?>admin/birthordeath/download/" + downloadid + "' data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>");
                }
            },
        });
    }

    function getRecord(id) {
        $('#myModaledit').modal('show');
        $('#viewModal').modal('hide');
        $.ajax({
            url: baseurl+'admin/birthordeath/edit',
            type: "GET",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                $("#eid").val(data.id);
                $('#customfield').html(data.custom_fields_value);
                $("#ecase_id").val(data.case_reference_id);
                $("#echild_name").val(data.child_name);
                $("#ebirth_date").val(data.birth_date);
                $("#eweight").val(data.weight);
                $("#gender").val(data.gender);
                $("#econtact").val(data.contact);
                $("#eaddress").val(data.address);
                $("#efather_name").val(data.father_name);
                $("#ebirth_report").val(data.birth_report);
                getmothernamebycaseid(data.case_reference_id);
            },
        });
    }

    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: baseurl+'admin/birthordeath/delete/' + id,
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
            url: base_url + 'admin/birthordeath/getBirthprintDetails/' + id,
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
    }
</script>
<script type="text/javascript">
$(".birthrecord").click(function(){
        $('#formadd').trigger("reset");
        $('#select2-14fy-container').html('');
        $(".dropify-clear").trigger("click");
});
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/birthordeath/getbirthDatatable',[],[],100,[           
            { "sWidth": "200px",  "aTargets": [ -1, -2, -3 ,-4 ] }		 
            ]);
    });
} ( jQuery ) )
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

// Example usage:

$('input[name=case_id]').keyup(delay(function (e) {
    $("#formadd #mother").val('');
    $("#formedit #mother").val('');
    $("#formadd #mother_name").val('');
    $("#formedit #mother_name").val('');
    
    var case_reference_id=$("input[name=case_id]").val();
    var case_reference_id= this.value ;
    if (isNaN(case_reference_id)) {

        errorMsg('<?php echo $this->lang->line("case_id_not_valid"); ?>');
         
        }else{
          getmothernamebycaseid(case_reference_id);  
        }   

}, 500)); 

function getmothernamebycaseid(case_id) {
    if(case_id!=''){
       $.ajax({
            url: baseurl+'admin/birthordeath/getpatientBycaseId/'+case_id,
            type: "POST",
            data: {case_reference_id: case_id},
            dataType: 'json',
            success: function (res) {
                if(res.status==1){

                if(res.gender == 'Male'){
                        errorMsg(res.message);
                    }else{
                        $("#formadd #mother").val(res.patient_name).prop('readonly',true);
                        $("#formedit #mother").val(res.patient_name).prop('readonly',true);
                        $("#formadd #mother_name").val(res.patient_id);
                        $("#formedit #mother_name").val(res.patient_id);
                    }

               }else{
                errorMsg(res.message);
               }
            }
        }); 
   }else{
 
   }
}
</script>
<script type="text/javascript">  
    
    $(document).ready(function (e) {
        $('#viewModal,#myModaledit').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
</script>
<!-- //========datatable end===== -->