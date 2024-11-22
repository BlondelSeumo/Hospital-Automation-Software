<!-- Content Wrapper. Contains page content -->
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
 
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="row">

            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('payment_details'); ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                    <div class="row" id="patient_details"></div>
                        <hr>
                        <div class="row">
                            <div class="col-md-offset-6 col-md-6 col-xs-offset-0 col-xs-12">
                                <p class="lead mb10"><?php echo $this->lang->line('amount'); ?></p>
                                
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <th class="pl-0 pt15"><?php echo $this->lang->line('add_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                <td class="text-right pt15"><?php echo number_format((float)$amount, 2, '.', ''); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                 <form action="<?php echo base_url(); ?>patient/payment/walkingm/pay" method="post" >
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <label for="email"><?php echo $this->lang->line('walkingm_email'); ?> <small class="req">*</small></label> 
                                            <input type="text" class="form-control" name="email" value="" />
                                            <span class="text-danger"><?php echo form_error('email'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <label for="email"><?php echo $this->lang->line('walkingm_password'); ?> <small class="req">*</small></label> 
                                            <input type="password" class="form-control" name="password" value="" />
                                            <span class="text-danger"><?php echo form_error('password'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    if(isset($api_error)){
                                        if((!empty(validation_errors())) || ($api_error!='')){
                                           ?>
                                            <tr class="bordertoplightgray">
                                                <td  bgcolor="#fff" colspan="2"><div class="alert alert-danger"><?php echo validation_errors(); if($api_error!=''){ echo $api_error; }?></div></td>
                                            </tr>
                                           <?php 
                                        }
                                    }
                                ?>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary submit_button"><i class="fa fa fa-money"></i> <?php echo $this->lang->line('make_payment'); ?></button>
                                        </div> 
                                </form>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script>
get_patientdetails();
    function get_patientdetails(){
        $.ajax({
            url: '<?php echo base_url("patient/pay/getPatientDetail/$case_reference_id"); ?>',
            type: "POST",
            success: function (data) {
                $("#patient_details").html(data);
            },
            error: function () {
                
            }
        });
    }
</script>
