<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php $this->load->view('setting/sidebar'); ?>
            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('prefix_setting'); ?></h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="">
                        <form class="form-horizontal" id="form_prefix" method="POST" action="<?php echo site_url('admin\prefix\update') ?>">
                            <div class="box-body p-15">
                                <?php 
foreach ($prefix_result as $prefix_key => $prefix_value) {
    ?>
   <div class="form-group"> 
                                    <label for="<?php echo $prefix_value->type; ?>" class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo $this->customlib->getPrefixnameByType($prefix_value->type); ?></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" id="<?php echo $prefix_value->type; ?>" name="<?php echo $prefix_value->type; ?>" value="<?php echo set_value($prefix_value->type,$prefix_value->prefix);?>">
                                    </div>
                                </div>
    <?php
}
                                 ?>
                             
                            </div>
                            <div class="box-footer">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <?php if ($this->rbac->hasPrivilege('prefix_setting', 'can_edit')) { ?>
                                    <button type="submit" class="btn btn-info pull-left ml-nt-lg-5 ml-nt-sm-8" id="load1" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Please wait"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                <?php } ?>
                                </div>
                            </div>
                        </form>
                    </div><!-- /.box-body -->
                </div>
            </div>
        </div>  
    </section>
</div>

<script type="text/javascript">
     $(document).on('submit', '#form_prefix',function(e){
            e.preventDefault();
            var btn = $("button[type=submit]");           
            var form = $(this);
            btn.button('loading');
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                dataType: 'JSON',               
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);                        
                    }
                     btn.button('reset');
                },
                error: function () {

                },
                complete: function(){
					btn.button('reset');
				}
            });
        });
</script>