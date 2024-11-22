<input type="hidden" name="obstetric_id" id="obstetric_id" class="form-control" value="<?php echo $obstetric_history_value['id'] ?>">
       <div class="row">
                    <div class="col-sm-12">
                    <div class="ptt10">
                        <div class="row">
                            <div class="col-sm-4">
                               <div class="form-group">
                                  <label><?php echo $this->lang->line('place_of_delivery'); ?></label><small class="req"> *</small>
                                 <input type="text" name="place_of_delivery" id="place_of_delivery" class="form-control" value="<?php echo $obstetric_history_value['place_of_delivery'] ?>">
                             </div>
                            </div>

                            <div class="col-sm-4">
                               <div class="form-group">
                                 <label for="filterinput"><?php echo $this->lang->line('duration_of_pregnancy'); ?></label>
                                    <input type="text" name='pragnancy_duration' id="pragnancy_duration" class="form-control" value="<?php echo $obstetric_history_value['pregnancy_duration'] ?>" > 
                             </div>
                            </div>
                            <div class="col-sm-4">
                               <div class="form-group">
                                <label><?php echo $this->lang->line('complication_in_pregnancy_or_puerperium'); ?></label>
                                    <textarea name='pragnancy_complications' id='pragnancy_complications' style="height: 28px;" class="form-control" value="<?php echo $obstetric_history_value['pregnancy_complications'] ?>" ><?php echo $obstetric_history_value['pregnancy_complications'] ?> </textarea> 
                                 </div>
                            </div>
                            </div>
                            <div class="row">
                                 <div class="col-sm-4">
                                   <div class="form-group">
                                     <label><?php echo $this->lang->line('birth_weight'); ?> </label>
                                         <input type="text" name='birth_weight' id='birth_weight' class='form-control' value="<?php echo $obstetric_history_value['birth_weight'] ?>" > 
                                     </div>
                                </div>
                                <div class="col-sm-4">
                                   <div class="form-group">
                                    <label><?php echo $this->lang->line('gender'); ?></label>
                                    <select class="form-control" name="gender" id="addformgender">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($genderlist as $key => $value) {
                                            ?>
                                            <option value="<?php echo $key; ?>" <?php if ($obstetric_history_value['obstetric_gender'] == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                     </div>
                                </div>
                                <div class="col-sm-4">
                                   <div class="form-group">
                                      <label><?php echo $this->lang->line('infant_feeding'); ?> </label>
                                         <textarea name="feeding" style="height: 28px;" class="form-control" value="<?php echo $obstetric_history_value['infant_feeding'] ?>" ><?php echo $obstetric_history_value['infant_feeding'] ?></textarea>
                                     </div>
                                </div>
                                <div class="col-sm-4">
                                   <div class="form-group">
                                     <label><?php echo $this->lang->line('birth_status'); ?> </label>
                                            <select name='alive_or_dead' class="form-control deathstatus">                       
                                                <option value='alive' <?php if($obstetric_history_value['alive_dead']=='alive'){ echo 'selected'; } ?>><?php echo $this->lang->line('alive'); ?></option>
                                                <option value='dead' <?php if($obstetric_history_value['alive_dead']=='dead'){ echo 'selected'; } ?> ><?php echo $this->lang->line('dead'); ?></option>
                                            </select>
                                     </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('alive'); ?> / <?php echo $this->lang->line('dead'); ?> <?php echo $this->lang->line('date'); ?> </label>
                                        <input type='text' name='date' class='form-control date' placeholder='' value="<?php if(!empty($obstetric_history_value['date'])){ echo $this->customlib->YYYYMMDDTodateFormat($obstetric_history_value['date']); } ?>">
                                     </div>
                                </div>
                                    
                                <div id="showdiv">                                   
                                    <div class="col-sm-4">
                                       <div class="form-group">
                                          <label><?php echo $this->lang->line('death_cause'); ?> </label>
                                           <input type='text' name='cause' class='form-control' placeholder='' value="<?php echo $obstetric_history_value['death_cause']; ?>">
                                     </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                   <div class="form-group">
                                      <label><?php echo $this->lang->line('previous_medical_history'); ?> </label>
                                       <textarea name='previous_history' class='form-control' rows="3" value="<?php echo $obstetric_history_value['previous_medical_history']; ?>" placeholder=''><?php echo $obstetric_history_value['previous_medical_history']; ?></textarea>
                                 </div>
                                </div>
                                <div class="col-sm-12">
                                   <div class="form-group">
                                      <label><?php echo $this->lang->line('special_instruction'); ?> </label>
                                       <textarea name='special_instruction' class='form-control' value="<?php echo $obstetric_history_value['special_instruction']; ?>" rows="3" placeholder=''><?php echo $obstetric_history_value['special_instruction']; ?></textarea>
                                 </div>
                                </div>
                           </div>
                            <br><hr>
                        </div>
                    </div> 
                </div>
        </div> 
<script>
    var tstatus = "<?php echo $obstetric_history_value['alive_dead'];  ?>" ;
   
    if(tstatus=='dead')
    {
         $("#showdiv").css("display","block");
    }else{
         $("#showdiv").css("display","none");
    }


$(".deathstatus").change(function(){

 var status= $(this).val();
   if(status=='dead')
    {
         $("#showdiv").css("display","block");
    }else{
         $("#showdiv").css("display","none");
    }
    
 });
</script>