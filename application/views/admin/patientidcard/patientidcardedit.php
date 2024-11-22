<style type="text/css">
    @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
</style>
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>     <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('patient_id_card', 'can_add') || $this->rbac->hasPrivilege('patient_id_card', 'can_edit')) {
                ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('edit_patient_id_card'); ?></h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form id="form1" enctype="multipart/form-data" action="<?php echo site_url('admin/patientidcard/edit/') . $editidcard[0]->id ?>"  id="certificateform" name="certificateform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg');
                                    $this->session->unset_userdata('msg');
                                     ?>
                                <?php } ?>
                                <?php
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                }
                                ?>                                
                                <input type="hidden" name="id" value="<?php echo set_value('id', $editidcard[0]->id); ?>">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('background_image'); ?></label>
                                    <input id="documents" placeholder="" value="<?php echo $editidcard[0]->background; ?>" type="file" class="filestyle form-control" data-height="40"  name="background_image">
                                    <span class="text-danger"><?php echo form_error('background_image'); ?></span>                                    
                                                             
                                    <?php if (!empty($editidcard[0]->background)) { ?>
                                        <div class="remove_background_image d-flex pt5"> 
                                            <a class="uploadclosebtn" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o pe5 cursor-pointer" onclick="remove_background_image()"></i></a><?php echo $editidcard[0]->background; ?>
                                        </div>     
                                    <?php }?>
                                        
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('logo'); ?></label>
                                    <input id="logo_img" placeholder="" value="<?php echo $editidcard[0]->logo; ?>" type="file" class="filestyle form-control" data-height="40"  name="logo_img">
                                    <span class="text-danger"><?php echo form_error('logo_img'); ?></span>
                                    
                                    <?php if (!empty($editidcard[0]->logo)) { ?>
                                        <div class="remove_logo d-flex pt5"> 
                                            <a class="uploadclosebtn" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o pe5 cursor-pointer" onclick="remove_logo()"></i></a><?php echo $editidcard[0]->logo; ?>
                                        </div>     
                                    <?php }?>
                                    
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('signature'); ?></label>
                                    <input id="sign_image" placeholder="" value="<?php echo $editidcard[0]->sign_image; ?>" type="file" class="filestyle form-control" data-height="40"  name="sign_image">
                                    <span class="text-danger"><?php echo form_error('sign_image'); ?></span>
                                    
                                    <?php if (!empty($editidcard[0]->sign_image)) { ?>
                                        <div class="remove_sign_image d-flex pt5"> 
                                            <a class="uploadclosebtn" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o pe5 cursor-pointer" onclick="remove_sign_image()"></i></a><?php echo $editidcard[0]->sign_image; ?>
                                        </div>     
                                    <?php }?>
                                    
                                </div>                               
                                
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('hospital_name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="hospital_name" name="hospital_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('background_image', $editidcard[0]->hospital_name); ?>" />
                                    <span class="text-danger"><?php echo form_error('hospital_name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('address_phone_email'); ?></label>
                                    <textarea class="form-control" id="address" name="address" placeholder="" rows="3" placeholder=""><?php echo set_value('background_image', $editidcard[0]->hospital_address); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('address'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('id_card_title'); ?></label><small class="req"> *</small>
                                    <input id="title" name="title" placeholder="" type="text" class="form-control" value="<?php echo set_value('title', $editidcard[0]->title); ?>" />
                                    <span class="text-danger"><?php echo form_error('title'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('header_color'); ?></label>
                                    <input id="header_color" name="header_color" placeholder="" type="text" class="form-control my-colorpicker1" value="<?php echo set_value('background_image', $editidcard[0]->header_color); ?>" />
                                </div>
                               
                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('patient_name'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_patient_name" name="is_active_patient_name" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_patient_name', '1', (set_value('is_active_patient_name', $editidcard[0]->enable_patient_name) == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_patient_name" class="label-success"></label>
                                    </div>
                                </div>
                                  <div class="form-group switch-inline">
                                        <label><?php echo $this->lang->line('patient_id'); ?></label>
                                        <div class="material-switch switchcheck">
                                            <input id="enable_patient_unique_id" name="is_active_patient_unique_id" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_patient_unique_id', '1', (set_value('is_active_patient_unique_id', $editidcard[0]->enable_patient_unique_id) == 1) ? TRUE : FALSE); ?>>
                                            <label for="enable_patient_unique_id" class="label-success"></label>
                                        </div>
                                    </div>
                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('guardian_name'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_guardian_name" name="is_active_guardian_name" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_guardian_name', '1', (set_value('is_active_guardian_name', $editidcard[0]->enable_guardian_name) == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_guardian_name" class="label-success"></label>
                                    </div>
                                </div>
                                
                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('patient_address'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_address" name="is_active_address" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_address', '1', (set_value('is_active_address', $editidcard[0]->enable_address) == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_address" class="label-success"></label>
                                    </div>
                                </div>
                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('phone'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_phone" name="is_active_phone" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_phone', '1', (set_value('is_active_phone', $editidcard[0]->enable_phone) == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_phone" class="label-success"></label>
                                    </div>
                                </div>
                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('date_of_birth'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_dob" name="is_active_dob" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_dob', '1', (set_value('is_active_dob', $editidcard[0]->enable_dob) == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_dob" class="label-success"></label>
                                    </div>
                                </div>
                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('blood_group'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_blood_group" name="is_active_blood_group" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_blood_group', '1', (set_value('is_active_blood_group', $editidcard[0]->enable_blood_group) == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_blood_group" class="label-success"></label>
                                    </div>
                                </div>
                                 <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('barcode_qrcode'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_barcode" name="is_active_barcode" type="checkbox" class="chk" value="1" <?php echo set_checkbox('enable_barcode', '1', (set_value('enable_barcode', $editidcard[0]->enable_barcode) == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_barcode" class="label-success"></label>
                                    </div>
                                </div>   
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>

                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('patient_id_card', 'can_add') || $this->rbac->hasPrivilege('patient_id_card', 'can_edit')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary" id="hroom">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('patient_id_card_list'); ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('patient_id_card_list'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('id_card_title'); ?></th>
                                            <!-- <th>Certificate Text</th> -->
                                            <th class="noExport"><?php echo $this->lang->line('background_image'); ?></th>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($idcardlist)) {
                                            ?>

                                            <?php
                                        } else {
                                            $count = 1;
                                            foreach ($idcardlist as $idcard) {
                                                ?>
                                                <tr>
                                                    <td class="mailbox-name">
                                                        <a href="#" data-toggle="popover" class="detail_popover" ><?php echo $idcard->title; ?></a>
                                                    </td>
                                                    <td class="mailbox-name">
                                                        <?php if ($idcard->background != '' && !is_null($idcard->background)) { ?>
                                                            <img src="<?php echo base_url('uploads/patient_id_card/background/'.$idcard->background.img_time()) ?>" width="40">
                                                        <?php }  ?>                                                  
                                                      

                                                    </td>
                                                    <td class="mailbox-date pull-right no-print">
                                                        <a id="<?php echo $idcard->id ?>" class="btn btn-default btn-xs view_data" data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>">
                                                            <i class="fa fa-reorder"></i>
                                                        </a>
                                                        <?php if ($this->rbac->hasPrivilege('patient_id_card', 'can_edit')) { ?>
                                                            <a href="<?php echo base_url(); ?>admin/patientidcard/edit/<?php echo $idcard->id ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                            <?php
                                                        }
                                                        if ($this->rbac->hasPrivilege('patient_id_card', 'can_delete')) {
                                                            ?>
                                                            <a href="<?php echo base_url(); ?>admin/patientidcard/delete/<?php echo $idcard->id ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            $count++;
                                        }
                                        ?>
                                    </tbody>
                                </table><!-- /.table -->
                            </div>  
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('view_id_card'); ?></h4>
            </div>
            <div class="modal-body" id="certificate_detail">

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';
    function printDiv(elem) {
        Popup(jQuery(elem).html());
    }

    function Popup(data)
    {

        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({"position": "absolute", "top": "-1000000px"});
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/ionicons.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/skins/_all-skins.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/iCheck/flat/blue.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/morris/morris.css">');


        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/datepicker/datepicker3.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/daterangepicker/daterangepicker-bs3.css">');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);


        return true;
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
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });

        $("#header_color").colorpicker();
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.view_data').click(function () {
            var certificateid = $(this).attr("id");
            $.ajax({
                url: "<?php echo base_url('admin/patientidcard/view') ?>",
                method: "post",
                data: {certificateid: certificateid},
                success: function (data) {
                    $('#certificate_detail').html(data);
                    $('#myModal').modal("show");
                }
            });
        });
    });
</script>
<script type="text/javascript">
    function valueChanged()
    {
        if ($('#enable_patient_img').is(":checked"))
            $("#enableImageDiv").show();       
        else
            $("#enableImageDiv").hide();        
    }
    
    $(document).ready(function (e) {
        $('#myModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    }); 
    
    function remove_background_image(){
       var result = confirm("<?php echo $this->lang->line('delete_confirm') ?>");
        if (result) {
            $('.remove_background_image').html('<input type="hidden" name="remove_background_image" value="1">');         
        }
    }
    
    function remove_logo(){
       var result = confirm("<?php echo $this->lang->line('delete_confirm') ?>");
        if (result) {
            $('.remove_logo').html('<input type="hidden" name="remove_logo" value="1">');         
        }
    }
    
    function remove_sign_image(){
       var result = confirm("<?php echo $this->lang->line('delete_confirm') ?>");
        if (result) {
            $('.remove_sign_image').html('<input type="hidden" name="remove_sign_image" value="1">');         
        }
    }
</script>