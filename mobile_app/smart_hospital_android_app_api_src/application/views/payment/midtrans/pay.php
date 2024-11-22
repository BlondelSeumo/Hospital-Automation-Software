	<form id="payment-form" method="post">
    <input type="hidden" name="result_type" id="result-type" value=""></div>
    <input type="hidden" name="result_data" id="result-data" value=""></div>
  </form>
  <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-2uDtZD3V5ZA_pNYW"></script> 
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> 
  <script type="text/javascript">
    var resultType = document.getElementById('result-type');
    var resultData = document.getElementById('result-data');
    function changeResult(type,data){
      $("#result-type").val(type);
      $("#result-data").val(JSON.stringify(data));
      //resultType.innerHTML = type;
      //resultData.innerHTML = JSON.stringify(data);
    }
      snap.pay('<?php echo $snap_Token; ?>',{ // store your snap token here
      onSuccess: function(result){ 
        changeResult('success', result); 
        $.ajax({
          url:  '<?php echo base_url(); ?>gateway/midtrans/success',
          type: 'POST',
          data: $('#payment-form').serialize(),
          dataType: "json",
          success: function (msg) { 
            window.location.href = "<?php echo base_url();?>payment/successinvoice/"+msg.insert_id;
          }
        });  
      },
      onPending: function(result){console.log('pending');console.log(result);},
      onError: function(result){console.log('error');console.log(result);},
      onClose: function(){console.log('customer closed the popup without finishing the payment');}
    })
  </script>