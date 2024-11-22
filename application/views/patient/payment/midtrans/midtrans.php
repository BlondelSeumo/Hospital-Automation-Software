<!-- Content Wrapper. Contains page content -->
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-2uDtZD3V5ZA_pNYW"></script> 
<script src="<?php echo base_url(); ?>backend/custom/jquery.min.js"></script>
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
                                <form id="payment-form" style="display:hidden" method="post" action="#">
                                    <input type="hidden" name="result_type" id="result-type" value=""></div>
                                    <input type="hidden" name="result_data" id="result-data" value=""></div>
                                </form>
                                <form id="checkoutform" action="#" method="post" >
                                        <div class="text-right">
                                            <button type="button" id="pay-button" class="btn btn-primary submit_button"><i class="fa fa fa-money"></i> <?php echo $this->lang->line('make_payment') ?></button>
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

<script type="text/javascript">
    var resultType = document.getElementById('result-type');
    var resultData = document.getElementById('result-data');

    function changeResult(type, data) {
        $("#result-type").val(type);
        $("#result-data").val(JSON.stringify(data));
    }
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        snap.pay('<?php echo $snap_Token; ?>', {// store your snap token here
            onSuccess: function (result) {
                changeResult('success', result);
                 document.getElementById("pay-button").disabled = true;
                $.ajax({
                    url: '<?php echo base_url(); ?>patient/payment/midtrans/success',
                    type: 'POST',
                    data: $('#payment-form').serialize(),
                    dataType: "json",
                    success: function (msg) {

                        window.location.href = "<?php echo base_url(); ?>patient/pay/successinvoice/";

                    } 
                });
            },
            onPending: function (result) {
                console.log('pending');
                console.log(result);
            },
            onError: function (result) {
                console.log('error');
                console.log(result);
            },
            onClose: function () {
                console.log('<?php echo $this->lang->line("customer_closed_the_popup_without_finishing_the_payment"); ?>');
            }
        })

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
