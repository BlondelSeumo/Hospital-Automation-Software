<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
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

                                <form action="<?php echo base_url(); ?>patient/payment/twocheckout/pay" method="post" >

                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <th><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                <td><?php echo number_format((float)$amount, 2, '.', ''); ?></td>
                                            </tr>

                                            <tr>
                                                <th><?php echo $this->lang->line('phone'); ?></th>
                                                <td>
                                                    <input type="text" class="form-control"  name="phone" value="<?php echo set_value('phone'); ?>" /> <span class="alert-danger"> <?php echo form_error('phone');?></span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th><?php echo $this->lang->line('email'); ?></th>
                                                <td>
                                                    <input type="text" class="form-control" name="email" value="<?php echo set_value('email'); ?>"> <span class="alert-danger"> <?php echo form_error('email');?></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <?php if (!empty($api_error)) { ?> 
                                    <div class="alert alert-danger">
                                        <?php echo "<pre>"; print_r($api_error); ?> 
                                    </div>
                                <?php } ?>
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