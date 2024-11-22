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
                                <form action="#" method="post" >
                                        <div class="text-right">
                                            <button type="button" onclick="pay()" class="btn btn-primary submit_button"><i class="fa fa fa-money"></i> <?php echo $this->lang->line('make_payment') ?></button>
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
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script> 
<script>
    var SITEURL = "<?php echo base_url() ?>";

    //pay();
    function pay(e) {
    var totalAmount = <?php echo $total; ?>;
    var product_id = <?php echo $merchant_order_id; ?>;
    var options = {
            "key": "<?php echo $key_id; ?>",
            "amount": "<?php echo $total; ?>", // 2000 paise = INR 20
            "name": "<?php echo $name; ?>",
            "description": "<?php echo $title; ?>",
            "currency": "<?php echo $currency; ?>",
            "image": "",
            "handler": function (response) {

                $.ajax({
                    url: '<?php echo $return_url; ?>',
                    type: 'post',
                    data: {
                        razorpay_payment_id: response.razorpay_payment_id, totalAmount: totalAmount, product_id: product_id,
                    },
                    success: function (msg) {

                        window.location.assign(SITEURL + 'patient/pay/successinvoice/')
                    }
                });

            },

            "theme": {
                "color": "#528FF0"
            }
        };
        console.log(options);
        var rzp1 = new Razorpay(options);
        rzp1.open();

    }
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