<?php
if (!empty($finding_list)) {
    foreach ($finding_list as $finding_key => $finding_value) {
        ?>
             <li class='option'><label class='checkbox'><input type='checkbox' data-record-id="<?php echo $finding_value->name; ?>" name='finding_title' value='<?php echo $finding_value->name."\n".$finding_value->description ; ?>'> <?php echo $finding_value->name ?></label></li>
        <?php
    }
}
?>
<script type="text/javascript">

$("input[name=addall]").change(function() {
  if (this.checked) {
    $("input[name=finding_title]").prop('checked', true).change();
  } else {
    $("input[name=finding_title]").prop('checked', false).change();
  }
});

function updateAllChecked() {
  $('#finding_description').val('');
  $("input[name=finding_title]").each(function() {
    if (this.checked) {
      let old_text = $('#finding_description').val() ? $('#finding_description').val() + '\n' : '';  
      $('#finding_description').val(old_text + $(this).val());
      $('#efinding_description').val(old_text + $(this).val());
    }
  })
}

var item_val ="" ;
$("input[name=finding_title]").change(function() {
    var check_val= $(this).val();
      if (this.checked) {
        let old_text = $('#finding_description').val() ? $('#finding_description').val() + '\n' : '';
       if(old_text.includes(check_val)){

       }else{
          $('#finding_description').val(old_text + $(this).val());
          $('#efinding_description').val(old_text + $(this).val());
       }
      }else{
        var desc = $('#finding_description').val();
         var new_text = desc.replace(check_val,'');         
         $('#finding_description').val(new_text);
      }  
  });
</script>