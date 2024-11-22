<?php foreach($agerange as $key=>$value){ 
     if($from_age<$value){
    ?>
    <option value="<?php echo $key; ?>" <?php if(isset($_POST['to_age']) && ($_POST['to_age']==$key)){ echo "selected" ; } ?> ><?php echo $value; ?></option>
<?php } } ?>