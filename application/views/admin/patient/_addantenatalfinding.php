<input type="hidden" name="edit_antenatal_id" value="0">                      
            <div>                    
                <div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">                            
                              <input type="hidden" name="visit_detail_id" id="visit_detail_id" value="<?php echo $visit_detail_id; ?>">   
                              <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>"> 
                                <div class="row row-eq">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                       <h4 class="mb0"><?php echo $this->lang->line('history_of_present_pregnancy'); ?></h4>
                                        <div class="row">
                                            <div class="col-md-12"> 
                                                <div class="dividerhr"></div>
                                            </div><!--./col-md-12-->                                            
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('bleeding'); ?></label> 
                                                    <input name="bleeding" id="bleeding" type="text" class="form-control" />
                                                </div>
                                            </div>                                            
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('headache'); ?></label> 
                                                    <input name="headache" id="headache" type="text" class="form-control" />
                                                </div>
                                            </div>                                            
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('pain'); ?></label> 
                                                    <input name="pain" id="pain" type="text" class="form-control" />
                                                </div>
                                            </div>                                            
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('constipation'); ?></label> 
                                                    <input name="constipation" id="constipation" type="text" class="form-control" />
                                                </div>
                                            </div>                                            
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('vomiting'); ?></label> 
                                                    <input name="vomiting" id="vomiting" type="text" class="form-control" />
                                                </div>
                                            </div>                                            
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('cough'); ?></label> 
                                                    <input name="cough" id="cough" type="text" class="form-control" />
                                                </div>
                                            </div>                                            
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('haemoroids'); ?></label> 
                                                    <input name="haemoroids" id="haemoroids" type="text" class="form-control" />
                                                </div>
                                            </div>                                            
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('discharge'); ?></label> 
                                                    <input name="discharge" id="discharge" type="text" class="form-control" />
                                                </div>
                                            </div>                                            
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('oedema'); ?></label> 
                                                    <input name="oedema" id="oedema" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('urinary_symptoms'); ?></label> 
                                                    <input name="urinary_symptoms" id="urinary_symptoms" type="text" class="form-control" />
                                                </div>
                                            </div>
                                           </div>
                                           <div class="row">
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('vaginal'); ?></label> 
                                                    <input name="vaginal" id="vaginal" type="text" class="form-control" />
                                                </div>
                                            </div>                                            
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('weight'); ?></label> 
                                                    <input name="weight" id="weight" type="text" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('height'); ?></label> 
                                                    <input name="height" id="height" type="text" class="form-control" />
                                                </div>
                                            </div>
                                             <div class="col-sm-2 col-xs-4 col20">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label> 
                                                    <input name="antenatal_date" id="antenatal_date" type="text" class="form-control datetime" />
                                                </div>
                                            </div>
                                            <br/>                                      
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="pwd"><?php echo $this->lang->line('condition'); ?></label>
                                                    <textarea name="condition" id="condition" rows="3" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="email"><?php echo $this->lang->line('special_findings_and_remark'); ?></label>
                                                    <textarea name="special_finding_remarks" rows="3" id="special_finding_remarks" placeholder="" class="form-control"><?php echo set_value('address'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="email"><?php echo $this->lang->line('pelvic_examination'); ?></label>
                                                    <textarea name="pelvic_examination" rows="3" id="pelvic_examination" placeholder="" class="form-control"><?php echo set_value('address'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="email"><?php echo $this->lang->line('sp'); ?></label>
                                                    <textarea name="sp" rows="3" id="sp" placeholder="" class="form-control"><?php echo set_value('address'); ?></textarea>
                                                </div> 
                                            </div>
                                            <div>
                                                <?php echo display_custom_fields('antenatal'); ?>
                                            </div>                                             
                                        </div><!--./row--> 
                                    </div><!--./col-md-8--> 
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-eq ptt10">
                                        <h4><label><?php echo $this->lang->line('antenatal_examination'); ?></label></h4>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('uter_size'); ?></label>
                                                    <input  name="uter_size" id="uter_size" type="text" class="form-control" />                                                    
                                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>
                                                    <?php echo $this->lang->line('uterus_size'); ?></label>
                                                    <div><input class="form-control" type='text'name="uterus_size" id="uterus_size" />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>
                                                    <?php echo $this->lang->line('presentation_position'); ?></label>
                                                    <div>
                                                   <input name="presentation_position" id="presentation_position" type="text" class="form-control" />                                                        
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>
                                                    <?php echo $this->lang->line('presenting_part_to_brim'); ?></label>
                                                    <div>
                                                         <input name="presentation_brim" id="presentation_brim" type="text" class="form-control" />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>
                                                            <?php echo $this->lang->line('foeta_heart'); ?></label>
                                                    <div>
                                                         <input name="foeta_heart" id="foeta_heart" type="text" class="form-control" />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div> 
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>
                                                    <?php echo $this->lang->line('blood_pressure'); ?></label>
                                                    <div><input class="form-control" type='text' name='blood_pressure' id='blood_pressure' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>
                                                       <?php echo $this->lang->line('antenatal_oedema'); ?></label>
                                                        <input class="form-control" type='text' name='antenatal_oedema' />
                                                    <div>
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6"></div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label> <?php echo $this->lang->line('urine_sugar'); ?></label>
                                                    <input class="form-control" type='text' name='urine' />
                                                    <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                                </div>
                                            </div>    
                                            <div class="col-md-6">
                                                <div class="form-group">
                                            <label> <?php echo $this->lang->line('urine_albumen'); ?></label>
                                            <input class="form-control" type='text' name='urine_aaibumen' />
                                                    <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                            <div class="form-group"> 
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('antenatal_weight'); ?></label>
                                                    <input class="form-control" type='text' name='antenatal_weight' />                                                  
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('remark') ; ?></label>
                                                    <textarea name="remark" id="remark" class="form-control" value="<?php echo set_value('remark'); ?>"></textarea>
                                                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('next_visit'); ?></label>
                                                    <textarea name="next_visit" id="next_visit" class="form-control" value="<?php echo set_value('remark'); ?>"></textarea>
                                                    <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                                </div>
                                            </div>                                        
                                        </div><!--./row-->    
                                    </div><!--./col-md-4-->
                                </div><!--./row-->        
                        </div><!--./col-md-12-->       
                    </div><!--./row--> 
                </div>
            </div>  
        </div>