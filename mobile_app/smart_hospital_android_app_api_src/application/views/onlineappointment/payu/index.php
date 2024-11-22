<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Payu Custom Integration Demo</title>
<link href="style.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style type="text/css">
  .paybox{width: 460px;margin: 7% auto;}
  .error{
    color: #c12626;
  }
  .paybox_bg{background: #fff;box-shadow: 0px 1px 15px rgba(0, 0, 0, 0.18);border-radius: 10px;}
    .bt_title{background: #f2f3f2; color: #000; padding: 20px 20px; border-bottom:1px solid #ccc;}
    .paybody{padding: 20px;}
    .paybox label{font-size: 13px; padding-top: 7px;}
    .submit_button{border-radius: 4px; padding: 10px 20px; border:0; background: #204d74; color: #fff; display: block;width: 100%; font-size: 16px; text-transform: uppercase; margin-top: 20px;}
    .submit_button:hover{background: #367fa9;}
    .payspan{position: absolute; right:0; top:5px; font-size: 18px;}
    .paybox h4{margin-top: 0px; padding-bottom: 20px}
    @media(max-width:767px){
      .paybox{width: 100%;margin: 1% auto;}
    }
</style>
  <script>

    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
  </script>
  </head>
  <body onLoad="submitPayuForm()">
    <div class="container">
      <div class="row">
       <div class="col-md-12">
        <div class="paybox">
          <div class="paybox_bg">
    <h3 class="bt_title">PayU Payment Gateway</h3>
    <div class="paybody">
    <?php if ($formError) {?>

      <span style="color:red">Please fill all mandatory fields.</span>
      <br/>
      <br/>
    <?php }?>
    <form action="<?php echo $action; ?>" method="post" name="payuForm" id="payuForm">
      <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
      <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
      <h5><span class="text text-danger">*</span> All fields are mandatory</h5>

      <div class="form-group row">
          <label class="col-sm-4">Amount <span class="text text-danger">*</span></label>
           <div class="col-sm-8"><input class="form-control" name="amount"  value="<?php echo set_value('amount', number_format((float)($this->session->userdata('payment_amount')), 2, '.', '')) ?>" readonly/></div>
      </div><!--./form-group-->
       <div class="form-group row">
          <label class="col-sm-4">Name <span class="text text-danger">*</span></label>
           <div class="col-sm-8"><input name="firstname" class="form-control" id="firstname" value="<?php echo set_value('firstname', $session_params['name']) ?>" readonly="readonly" /></div>
      </div><!--./form-group-->

      <div class="form-group row">
          <label class="col-sm-4">Email <span class="text text-danger">*</span></label>
           <div class="col-sm-8"><input name="email" class="form-control" id="email" value="<?php echo set_value('email', $session_params['email']) ?>" /></div>
      </div><!--./form-group-->

      <div class="form-group row">
          <label class="col-sm-4">Phone <span class="text text-danger">*</span></label>
           <div class="col-sm-8"><input name="phone" class="form-control" value="<?php echo set_value('phone', $session_params['mobileno']) ?>" /></div>
      </div>


      <input type="hidden" name="surl" value="<?php echo set_value('surl', $surl) ?>"  readonly="readonly"/>

      <input type="hidden" name="furl" value="<?php echo set_value('furl', $furl) ?>"  readonly="readonly"/>
      <input type="hidden" name="productinfo" value="<?php echo set_value('productinfo', $productinfo) ?>"  readonly="readonly"/>

       <div class="form-group row">

           <div class="col-sm-12"><input type="hidden" class="form-control" name="service_provider" value="payu_paisa" size="64" /></div>
      </div><!--./form-group-->
     <div class="form-group row">
      <?php 

      if (!$hash) {?>
            <div class="col-sm-12"><input class="submit_button" type="submit" value="Submit" /></div>
          <?php }?>
        </div><!--./form-group-->

    </form>
       </div><!--./paybody-->
      </div><!--./paybox-->
     </div><!--./paybox_bg-->
    </div><!--./col-md-12-->
   </div><!--./row-->
  </div><!--./container-->
  </body>
</html>


<script type="text/javascript">
  $(document).ready(function(){

$("#payuForm").validate(

{

rules:{

'firstname':{

required: true

},
'email':{

required: true,
email: true

},
'phone':{

required: true

}


},

messages:{

'firstname':{

required: "The Name is mandatory!"

},
'email':{

required: "The Email is mandatory!",
email:"The email is incorrect!"

},
'phone':{

required: "The Phone is mandatory!"

}

}

});

  });
</script>