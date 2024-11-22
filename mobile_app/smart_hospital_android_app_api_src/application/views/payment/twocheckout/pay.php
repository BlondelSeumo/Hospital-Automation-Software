<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Twocheckout Custom Integration</title>
<link href="style.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style type="text/css">
    .paybox{width: 460px;margin: 7% auto;}
    .paybox_bg{background: #fff;box-shadow: 0px 1px 15px rgba(0, 0, 0, 0.18);border-radius: 10px;}
    .bt_title{background: #f2f3f2; color: #000; padding: 20px 20px; border-bottom:1px solid #ccc;}
    .paybody{padding: 20px;}
    .paybox label{font-size: 13px; padding-top: 7px;}
    .submit_button{border-radius: 4px; padding: 10px 20px; border:0; background: #204d74; color: #fff; display: block;width: 100%; font-size: 16px; text-transform: uppercase; margin-top: 20px;}
    .submit_button:hover{background: #367fa9;}
     @media(max-width:767px){
        .paybox{width: 100%;margin: 1% auto;}
    }
</style> 
 
</head>
<body>
<div class="container">

    <div class="row">
        <div class="col-md-12">
            <div class="paybox">   
                <div class="paybox_bg"> 
                    <h3 class="bt_title"><img src="../backend/images/twocheckout.png" style="margin-bottom: 10px;"><br />Twocheckout Payment Gateway</h3>  
                      <?php if($api_error){
                                ?>
                                <div class="alert alert-danger"><?php foreach($api_error as $value){
                               // print_r($value);die;
                                foreach ($value as $key => $value1) {
                                    echo $value1.". ";
                                }
                                echo "<br>";
                            }?> </div>
                                <?php
                            }?>                       
                        <div class="paybody">
                            <form class="paddtlrb" action="<?php echo site_url('gateway/twocheckout/pay') ?>" method="POST">
                                <div class="form-group row">
                                  <label class="col-sm-4">Name</label>
                                  <div class="col-sm-8">
                                    <input class="form-control" readonly name="firstname" value="<?php echo set_value('firstname', $session_params['name']) ?>"/></div>
                                </div><!--./form-group-->
                                <div class="form-group row">
                                 <label class="col-sm-4">Email</label>
                                 <div class="col-sm-8">
                                    <input class="form-control"  name="email" value="<?php echo set_value('email', $session_params['email']) ?>"/></div>
                                </div><!--./form-group-->
                                <div class="alert-danger"> <?php echo form_error('email'); ?></div>
                                <div class="form-group row">
                                <label class="col-sm-4">Phone</label>
                                <div class="col-sm-8"><input class="form-control"  name="phone" value="<?php echo set_value('phone', $session_params['mobileno']) ?>"/></div>
                                </div><!--./form-group-->
                                <div class="alert-danger"> <?php echo form_error('phone'); ?></div>
                                <div class="form-group row">
                                 <label class="col-sm-4">Amount(<?php echo $session_params['invoice']->symbol;?>)</label>
                                  <div class="col-sm-8"><input class="form-control" name="amount" /></div>
                                </div><!--./form-group-->
                                <div class="alert-danger"> <?php echo form_error('amount'); ?></div>
                                <div class="form-group">
                                    <button type="submit" id="pay-button" class="submit_button"><i class="fa fa-money"></i> Pay Now </button> 
                                </div>
                            </form>
         </div><!--./paybody-->                       
        </div><!--./paybox_bg-->                        
       </div><!--./paybox-->
      </div><!--./col-md-12-->
     </div><!--./row-->
    </div><!--./container-->                           
   </body>
</html>

<script src="<?php echo base_url();?>backend/custom/jquery.min.js"></script>
         
         <script>
            (function(document, src, libName, config) {
                var script = document.createElement('script');
                script.src = src;
                script.async = true;
                var firstScriptElement = document.getElementsByTagName('script')[0];
                script.onload = function() {
                    for (var namespace in config) {
                        if (config.hasOwnProperty(namespace)) {
                            window[libName].setup.setConfig(namespace, config[namespace]);
                        }
                    }
                    window[libName].register();
                };

                firstScriptElement.parentNode.insertBefore(script, firstScriptElement);
            })(document, 'https://secure.2checkout.com/checkout/client/twoCoInlineCart.js', 'TwoCoInlineCart', {
                "app": {
                    "merchant": "<?php echo $api_config->api_publishable_key; ?>"
                },
                "cart": {
                    "host": "https:\/\/secure.2checkout.com"
                }
            }); 
        </script>
          <script type="text/javascript">
          	//$('#buy-button').trigger("click");
                window.document.getElementById('buy-button').addEventListener('click', function() {

                    TwoCoInlineCart.events.subscribe('cart:closed', function(e) {
                        alert();
                        //window.location.replace("");
                    });

                    TwoCoInlineCart.setup.setMerchant("<?php echo $api_config->api_publishable_key; ?>");
                    TwoCoInlineCart.setup.setMode('DYNAMIC'); // product type
                    TwoCoInlineCart.register();

                    TwoCoInlineCart.products.add({
                        name: "Patient Bill",
                        quantity: 1,
                        price: "<?php echo $amount;?>",
                    });

                    TwoCoInlineCart.cart.setOrderExternalRef("<?php echo md5(time()); ?>");
                    TwoCoInlineCart.cart.setExternalCustomerReference("<?php echo md5("1".time()); ?>"); // external customer reference 
                    TwoCoInlineCart.cart.setCurrency("<?php echo $currency; ?>");
                    TwoCoInlineCart.cart.setTest(false);
                    TwoCoInlineCart.cart.setReturnMethod({
                        type: 'redirect',
                        url: "<?php echo base_url() ?>gateway/twocheckout/success",
                    });

                    TwoCoInlineCart.cart.checkout(); // start checkout process
                });

                setTimeout(function() {
                    $('#buy-button').removeClass('disabled');
                }, 3000);
            </script>