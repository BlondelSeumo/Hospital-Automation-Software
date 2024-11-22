<style type="text/css">
    @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">           
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary" id="hroom">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('certificate_template_list'); ?></h3>
                        <div class="box-tools addmeeting">
                             <?php
            if ($this->rbac->hasPrivilege('certificate', 'can_add')) {
                ?>
                <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm uploadcontent"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_certificate_template'); ?>
                            </a>
            <?php } ?>                            
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('certificate_template_list'); ?>; ?></div>
                            <table class="table table-striped table-bordered table-hover ajaxlist" data-export-title="<?php echo $this->lang->line('certificate_template_list'); ?>" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('certificate_template_name'); ?></th>
                                        <th class="noExport"><?php echo $this->lang->line('background_image'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                 
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
        <div class="row">
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<!-- Modal -->

<div class="modal fade" id="myModalview" role="dialog" style="width: 100%;">
    <div class="modal-dialog modal-sm-100" style="width: 90%;">
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('view_certificate_template'); ?></h4>
            </div>
            <div class="modal-body" id="certificate_detail">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_certificate_template'); ?></h4>
            </div>
            <form id="formadd" method="post" accept-charset="utf-8">
                   <div class="scroll-area">
                        <div class="modal-body ptt10 pb0">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('certificate_template_name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="certificate_name" name="certificate_name" placeholder="" type="text" class="form-control" />
                                    <span class="text-danger"><?php echo form_error('certificate_name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('header_left_text'); ?></label>
                                    <input id="left_header" name="left_header" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('header_center_text'); ?></label>
                                    <input id="center_header" name="center_header" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('header_right_text'); ?></label>
                                    <input id="right_header" name="right_header" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('body_text'); ?></label><small class="req"> *</small>
                                    <textarea class="form-control" id="certificate_text" name="certificate_text" placeholder="" rows="3" placeholder=""></textarea>
                                    <span class="text-primary"> <?php echo $this->customlib->getCertificateVariables()?>
                                    </span>
                                    <span class="text-danger"><?php echo form_error('certificate_text'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('footer_left_text'); ?></label>
                                    <input id="left_footer" name="left_footer" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('footer_center_text'); ?></label>
                                    <input id="center_footer" name="center_footer" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('footer_right_text'); ?></label>
                                    <input id="right_footer" name="right_footer" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="mediarow">
                                    <div class="row">
                                        <div class="img_div_modal"><label><?php echo $this->lang->line('certificate_design'); ?></label></div>
                                        <div class="col-md-6 col-sm-6 img_div_modal">
                                            <div class="form-group">
                                                <input id="header_height" name="header_height" placeholder="<?php echo $this->lang->line('header_height'); ?>" type="text" class="form-control" min="0" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 img_div_modal">
                                            <div class="form-group">
                                                <input id="footer_height" name="footer_height" placeholder="<?php echo $this->lang->line('footer_height'); ?>" type="text" class="form-control" min="0" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 img_div_modal">
                                            <div class="form-group">
                                                <input id="content_height" name="content_height" placeholder="<?php echo $this->lang->line('body_height'); ?>" type="text" class="form-control" min="0" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 img_div_modal">
                                            <div class="form-group">
                                                <input id="content_width" name="content_width" placeholder="<?php echo $this->lang->line('body_width'); ?>" type="text" class="form-control" min="0" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 img_div_modal minh45">
                                            <div class="form-group switch-inline">
                                                <label><?php echo $this->lang->line('patient_photo'); ?></label>
                                                <div class="material-switch switchcheck">
                                                    <input id="enable_patient_img" name="is_active_patient_img" type="checkbox" class="chk" value="1" onclick="valueChanged()">
                                                    <label for="enable_patient_img" class="label-success"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 img_div_modal">
                                            <div class="form-group" id="enableImageDiv" hidden>
                                                <input id="image_height" name="image_height" placeholder="<?php echo $this->lang->line('photo_height'); ?>" type="text" class="form-control" min="0" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('background_image'); ?></label>
                                    <input id="documents" placeholder="" type="file" class="filestyle form-control" data-height="40"  name="background_image">
                                </div>
                </div>
            </div>
            <div class="modal-footer">         
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn" class="btn btn-info pull-right"> <i class="fa fa-check-circle"></i>  <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="myModaledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_certificate_template'); ?></h4>
            </div>
            <form id="formedit" method="post" accept-charset="utf-8">
                  <div class="scroll-area">
                        <div class="modal-body ptt10 pb0">
                                <input autofocus="" id="ecertificate_id" name="id" placeholder="" type="hidden" class="form-control" />
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('certificate_template_name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="ecertificate_name" name="certificate_name" placeholder="" type="text" class="form-control" />
                                    <span class="text-danger"><?php echo form_error('certificate_name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('header_left_text'); ?></label>
                                    <input id="eleft_header" name="left_header" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('header_center_text'); ?></label>
                                    <input id="ecenter_header" name="center_header" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('header_right_text'); ?></label>
                                    <input id="eright_header" name="right_header" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('body_text'); ?></label><small class="req"> *</small>
                                    <textarea class="form-control" id="ecertificate_text" name="certificate_text" placeholder="" rows="3" placeholder=""></textarea>
                                    <span class="text-primary"> <?php echo $this->customlib->getCertificateVariables()?></span>
                                    <span class="text-danger"><?php echo form_error('certificate_text'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('footer_left_text'); ?></label>
                                    <input id="eleft_footer" name="left_footer" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('footer_center_text'); ?></label>
                                    <input id="ecenter_footer" name="center_footer" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('footer_right_text'); ?></label>
                                    <input id="eright_footer" name="right_footer" placeholder="" type="text" class="form-control" />
                                </div>
                                <div class="mediarow">
                                    <div class="row">
                                        <div class="img_div_modal"><label><?php echo $this->lang->line('certificate_design'); ?></label></div>
                                        <div class="col-md-6 col-sm-6 img_div_modal">
                                            <div class="form-group">
                                                <input id="eheader_height" name="header_height" placeholder="<?php echo $this->lang->line('header_height'); ?>" type="text" class="form-control" min="0" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 img_div_modal">
                                            <div class="form-group">
                                                <input id="efooter_height" name="footer_height" placeholder="<?php echo $this->lang->line('footer_height'); ?>" type="text" class="form-control" min="0" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 img_div_modal">
                                            <div class="form-group">
                                                <input id="econtent_height" name="content_height" placeholder="<?php echo $this->lang->line('body_height'); ?>" type="text" class="form-control" min="0" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 img_div_modal">
                                            <div class="form-group">
                                                <input id="econtent_width" name="content_width" placeholder="<?php echo $this->lang->line('body_width'); ?> " type="text" class="form-control" min="0" />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 img_div_modal minh45">
                                            <div class="form-group switch-inline">
                                                <label><?php echo $this->lang->line('patient_photo'); ?></label>
                                                <div class="material-switch switchcheck">
                                                    <input id="eenable_patient_img" name="is_active_patient_img" type="checkbox" class="chk" value="1" onclick="valueChangededit()">
                                                    <label for="eenable_patient_img" class="label-success"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 img_div_modal">
                                            <div class="form-group" id="eenableImageDiv" hidden>
                                                <input id="eimage_height" name="image_height" placeholder="<?php echo $this->lang->line('photo_height'); ?>" type="text" class="form-control" min="0" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('background_image'); ?></label>
                                    <input id="edocuments" placeholder="" type="file" class="filestyle form-control" data-height="40"  name="background_image">
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

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/certificate/getcertificatedatatable');
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->              
<script type="text/javascript">
    $(document).ready(function (e) {
                $("#formadd").on('submit', (function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/certificate/create',
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
                        url: '<?php echo base_url(); ?>admin/certificate/edit',
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

    $(document).ready(function () {
        $('#postdate').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
    });
    
</script>
<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';
    function printDiv(elem) {
        popup(jQuery(elem).html());
    }     
</script>
<script>
            $(document).ready(function () {
                $('.detail_popover').popover({
                    placement: 'right',
                    trigger: 'hover',
                    container: 'body',
                    html: true,
                    content: function () {
                    }
                });
            });

            function getRecord(id) {             
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/certificate/getcertificate',
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    success: function (data) {
                       $("#ecertificate_id").val(data.id);
                        $("#ecertificate_name").val(data.certificate_name);
                        $("#ecertificate_text").val(data.certificate_text);
                        $("#eleft_header").val(data.left_header);
                        $("#ecenter_header").val(data.center_header);
                        $("#eright_header").val(data.right_header);
                        $("#eleft_footer").val(data.left_footer);
                        $("#ecenter_footer").val(data.center_footer);
                        $("#eright_footer").val(data.right_footer);
                        $("#econtent_height").val(data.content_height);
                        $("#eheader_height").val(data.header_height);
                        $("#efooter_height").val(data.footer_height);
                        $("#econtent_width").val(data.content_width);
                        $("#eimage_height").val(data.enable_image_height);
                        $("#enote").text(data.note);
                        var enable_patient_img = data.enable_patient_image ;
                        if (enable_patient_img == 1) {
                            $( "#eenable_patient_img" ).prop( "checked", true );
                            $("#eenableImageDiv").show();                            
                        } else {
                            $( "#eenable_patient_img" ).prop( "checked", false );
                            $("#eenableImageDiv").hide();
                        }
                    },
                });
                 $('#myModaledit').modal('show');
            }

        function viewRecord(id) {          
            $.ajax({
                url: "<?php echo base_url('admin/certificate/view') ?>",
                method: "post",
                data: {certificateid: id},
                success: function (data) {
                    $('#certificate_detail').html(data);
                    $('#myModalview').modal("show");
                }
            });
        }

        function Delete_recored(id) {
            var txt;
               if (confirm("Are You Sure !")) {
                $.ajax({
                    url: "<?php echo base_url('admin/certificate/delete/') ?>"+id,
                    method: "post",
                    data: { },
                    success: function (data) {
                        window.location.reload(true);
                    }
                });
            }             
      }
</script>

<script type="text/javascript">
    function valueChanged()
    {
        if ($('#enable_patient_img').is(":checked"))
            $("#enableImageDiv").show();       
        else
            $("#enableImageDiv").hide();     
    }

    function valueChangededit()
    {
        if ($('#eenable_patient_img').is(":checked"))
            $("#eenableImageDiv").show();      
        else
            $("#eenableImageDiv").hide();        
    }
    
    $(document).ready(function (e) {
        $('#myModal,#myModaledit,#myModalview').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
</script>