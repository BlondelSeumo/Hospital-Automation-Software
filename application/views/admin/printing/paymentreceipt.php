<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<div class="content-wrapper">  
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <?php
                $this->load->view('admin/printing/sidebar');
                ?>
            </div>
            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('payment_receipt_header_footer'); ?></h3>
                    </div>                  
                    <div class="box-body">
                        <form class="modal-text-white" enctype="multipart/form-data" action="<?php echo site_url('admin/printing/update') ?>" method="post">
                            <input type="hidden" name="id" value="<?php if(!empty($printing_list)){ echo $printing_list[0]['id'];} ?>">
                            <input type="hidden" name="function_name" value="<?php echo $function_name; ?>">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('header_image'); ?>(2230px X 300px)</label>
                                        <input data-default-file="<?php echo base_url() ?><?php if(!empty($printing_list)){ echo $printing_list[0]['print_header'];} ?>" type="file" class="filestyle form-control" data-height="180"  name="header_image">
                                        <input type="hidden" class="form-control" name="print_header">
                                        <span class="text-danger"><?php echo form_error('header_image'); ?></span>
                                        
                                        <?php if (!empty($printing_list[0]['print_header'])) { ?>
                                        <div class="header_image d-flex pt5"> 
                                            <a class="uploadclosebtn" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o pe5 cursor-pointer" onclick="removeheader_image()"></i></a><?php echo $printing_list[0]['print_header']; ?>
                                        </div>     
                                        <?php }?>
                                        
                                    </div>
                                    <div class="form-group"><label><?php echo $this->lang->line('footer_content'); ?></label>
                                        <textarea id="compose_textarea" name="footer_content" class="form-control h-250">
                                            <?php if(!empty($printing_list)){ echo $printing_list[0]['print_footer'];} ?>
                                        </textarea>
                                        <span class="text-danger"><?php echo form_error('footer_content'); ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">         
                                    <div class="pull-right">
                                        <button type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i> <?php echo $this->lang->line('save'); ?>"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>  
                            </div>
                        </form>
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
$(function () {
"use strict";
    $("#compose_textarea").wysihtml5({
        toolbar: {
            "image": false,
        }
    });
})(jQuery);

    function removeheader_image(){
       var result = confirm("<?php echo $this->lang->line('delete_confirm') ?>");
        if (result) {
            $('.header_image').html('<input type="hidden" name="removeheader_image" value="1">');            
            $(".dropify-clear").click();           
        }
    }
</script>