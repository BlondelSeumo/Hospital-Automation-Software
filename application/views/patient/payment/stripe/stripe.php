<!-- Content Wrapper. Contains page content -->
<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/font-awesome.min.css"> 
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">  
    <link href="<?php echo base_url(); ?>backend/toast-alert/toastr.css" rel="stylesheet"/>
    <script src="<?php echo base_url(); ?>backend/toast-alert/toastr.js"></script>
    <script src="<?php echo base_url(); ?>backend/js/hospital-custom.js"></script>
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
                               
                                <div id="stripe-payment-message" class="hidden"></div>

<form id="stripe-payment-form" class="paddtlrb" action="<?php echo site_url('patient/payment/stripe/complete'); ?>" method="POST">

    <input type='hidden' id='publishable_key' value='<?php echo $api_publishable_key; ?>'>
    <input type='hidden' id='currency' value='<?php echo $currency_name; ?>'>
    <input type='hidden' id='description' value='<?php echo $this->lang->line("online_appointment_fees"); ?>'>


    <input type="hidden" name="total" id="amount" value="<?php echo number_format((float)($amount), 2, '.', ''); ?>">
      
            <div id="stripe-payment-element">
                <!--Stripe.js will inject the Payment Element here to get card details-->
            </div>
            <div class="button-between">
                <button type="button" onclick="window.history.go(-1); return false;" name="search" value="" class="btn btn-info"><i class="fa fa fa-chevron-left"></i> <?php echo $this->lang->line('back') ?></button>
                <button type="submit" class="pay btn btn-primary" id="submit-button"  data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"> <i class="fa fa-money"></i> <?php echo $this->lang->line('pay_now') ?> </button>
                <div id="payment-reinitiate" class="hidden" >
    <button class="btn btn-primary" type="button" onclick="reinitiateStripe()"> <i class="fa fa-money"></i>  <?php echo $this->lang->line('reinitiate_payment') ?> </button>
    </div>
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
 
    <script src="https://js.stripe.com/v3/"></script>
 <script src="<?php echo base_url('backend/js/stripe_patient-checkout.js') ?>" defer></script>
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