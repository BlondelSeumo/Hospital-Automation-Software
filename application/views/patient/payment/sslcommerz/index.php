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
                            <div class="col-md-offset-6 col-xs-6">
                                <p class="lead"><?php echo $this->lang->line('amount'); ?></p>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            </tr>
                                            <tr>
                                                <th><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                <td><?php echo number_format((float)$amount, 2, '.', ''); ?></td>
                                            </tr>
                                        </tbody></table>
                                </div>
                                <form action="<?php echo base_url(); ?>patient/payment/sslcommerz/pay" method="post" >
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="email"><?php echo $this->lang->line('email'); ?> <small class="req"> *</small></label> 
                                                <input type="text" class="form-control" name="email" id="email" value="<?= $patient_data['email']; ?>" />
                                                <span class="text-danger"><?php echo form_error('email'); ?></span>
                                            </div>

                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('phone'); ?> <small class="req"> *</small></label>
                                                <input type="text" class="form-control" name="phone" value="<?= $patient_data['mobileno']; ?>" />
                                                <span class="text-danger"><?php echo form_error('phone'); ?></span>
                                            </div>

                                            <div class="form-group">
                                                <label for="address"><?php echo $this->lang->line('address'); ?> <small class="req"> *</small></label>
                                                <input type="text" class="form-control" name="address" value="<?= $patient_data['address']; ?>" />
                                                <span class="text-danger"><?php echo form_error('address'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary submit_button"><i class="fa fa fa-money"></i> <?php echo $this->lang->line('make_payment') ?></button>
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