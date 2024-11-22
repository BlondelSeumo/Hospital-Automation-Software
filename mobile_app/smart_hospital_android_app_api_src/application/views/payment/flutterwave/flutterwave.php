<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Flutterwave  Custom Integration</title>
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
			        <h3 class="bt_title"><img src="<?php echo base_url('backend/images/flutterwave.png');?>" style="margin-bottom: 10px;"><br />Flutterwave Payment Gateway</h3>  
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
 
                                <form class="paddtlrb" action="<?php echo site_url('gateway/flutterwave/pay') ?>" method="POST" id="paypalForm">
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
										  <div class="col-sm-8"><input class="form-control" name="amount" value="<?php echo set_value('amount', number_format($session_params['total'],2,'.','')) ?>"/></div>

										
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
