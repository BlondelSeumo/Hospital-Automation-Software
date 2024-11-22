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
                                            <tr>
                                                <th><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                <td><?php echo number_format((float)$amount, 2, '.', ''); ?></td>
                                            </tr>
                                        </tbody></table>
                                </div>
                                <form id="payuForm" action="<?php echo $action; ?>" method="post" >
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary submit_button"><i class="fa fa fa-money"></i> <?php echo $this->lang->line('make_payment') ?></button>
                                        </div>
                                        <input type="hidden" name="key" value="<?php echo $mkey ?>" />
                                        <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
                                        <input type="hidden" name="txnid" value="<?php echo $tid ?>" />
                                        <input type="hidden" name="amount" value="<?php echo set_value('amount', $amount) ?>" />
                                        <input type="hidden" name="firstname" id="firstname" value="<?php echo set_value('firstname', $name); ?>" />
                                        <textarea name="productinfo" style="display:none"><?php echo set_value('productinfo', $productinfo); ?></textarea>
                                        <input type="hidden" name="surl" value="<?php echo set_value('surl', $sucess); ?>" size="64" />
                                        <input type="hidden" name="furl" value="<?php echo set_value('furl', $failure); ?>" size="64" />
                                        <input type="hidden" name="service_provider" value="payu_paisa" size="64" />
                                </form>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script type="text/javascript">
    $(document).ready(function () {
        $(".submit_button").click(function (e) {
            var url = "<?php echo site_url('patient/payment/payu/checkout') ?>";

            $.ajax({
                type: "POST",
                url: url,
                data: $("#payuForm").serialize(),
                dataType: "Json",
                success: function (response)
                {

                     if (response.status == "success") {
                         $('form#payuForm').submit();
                     } else if (response.status == "fail") {
                        $.each(response.error, function (index, value) {
                            var errorDiv = '.' + index + '_error';
                            $(errorDiv).empty().append(value);
                        });
                     }
                }
            });

            e.preventDefault();
        });
    });
</script>
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