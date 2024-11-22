<input type="hidden" name="patient_id" value="<?php echo $patient_id;?>">
<input type="hidden" name="action" value="add">
<input type="hidden" name="ipd_prescription_basic_id" value="0">
                <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="pt10">
                          <div class="row">
                            <div class="col-sm-4">
                               <div class="form-group">
                                 <label><?php echo $this->lang->line('place_of_delivery'); ?></label><small class="req"> *</small>
                                 <input type="text" name="place_of_delivery" id="place_of_delivery" class="form-control"> 
                             </div>
                            </div>
                            <div class="col-sm-4">
                               <div class="form-group">
                                  <label for="filterinput"> <?php echo $this->lang->line('duration_of_pregnancy'); ?></label>
                                  <input type="text" name="pragnancy_duration" id="pragnancy_duration" class="form-control"> 
                             </div>
                            </div>
                            <div class="col-sm-4">
                               <div class="form-group">
                                   <label><?php echo $this->lang->line('complication_in_pregnancy_or_puerperium'); ?></label>
                                  <textarea name='pragnancy_complications' id='pragnancy_complications' style="height: 28px;" class="form-control"></textarea>
                             </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                               <div class="form-group">
                                   <label><?php echo $this->lang->line('birth_weight'); ?></label>
                                   <input type="text" name='birth_weight' id='birth_weight' class='form-control'> 
                             </div>
                            </div>

                             <div class="col-sm-4">
                               <div class="form-group">
                                  <label> <?php echo $this->lang->line('gender'); ?></label>
                                    <select class="form-control" name="gender" id="addformgender">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($genderlist as $key => $value) {
                                            ?>
                                            <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                             </div>
                            </div>
                            <div class="col-sm-4">
                               <div class="form-group">
                                  <label><?php echo $this->lang->line('infant_feeding'); ?></label>
                                  <textarea name="feeding" style="height: 28px;" class="form-control"></textarea>
                             </div>
                            </div>
                             <div class="col-sm-4">
                               <div class="form-group">
                                  <label><?php echo $this->lang->line('birth_status'); ?> </label>
                                    <select name='alive_or_dead' class="form-control deathstatus " id="alive_or_dead_1" data-record-id="1">                                        
                                        <option value='alive'><?php echo $this->lang->line('alive'); ?></option>
                                        <option value='dead'><?php echo $this->lang->line('dead'); ?></option>
                                    </select>
                             </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('alive'); ?> / <?php echo $this->lang->line('dead'); ?> <?php echo $this->lang->line('date'); ?> </label>
                                    <input type='text' name='date' class='form-control date' placeholder='' >
                                </div>
                            </div>
                            <div id="showdiv" style="display:none">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('death_cause'); ?> </label>
                                       <input type='text' name='cause' id='cause' class='form-control' placeholder=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                               <div class="form-group">
                                  <label><?php echo $this->lang->line('previous_medical_history'); ?></label>
                                   <textarea name='previous_history' class='form-control' rows="3" placeholder=''></textarea>
                             </div>
                            </div>
                            <div class="col-sm-12">
                               <div class="form-group">
                                  <label><?php echo $this->lang->line('special_instruction'); ?></label>
                                   <textarea name='special_instruction' class='form-control' rows="3" placeholder=''></textarea>
                             </div>
                            </div>
                        </div>
                        </div>
                    </div> 
                </div>
				
<script>
   
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