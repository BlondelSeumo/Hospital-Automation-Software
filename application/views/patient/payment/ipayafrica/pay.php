

<form id="pay_form" action="https://payments.ipayafrica.com/v3/ke">
<?php  
foreach ($fields as $key => $value) {
    
    echo ' <input name="'.$key.'" type="hidden" value="'.$value.'">';
}
?>
<INPUT name="hsh" type="hidden" value="<?php echo $generated_hash ?>">
</form>

<script>
window.onload = function(){
    document.getElementById("pay_form").submit(); 
}
</script>