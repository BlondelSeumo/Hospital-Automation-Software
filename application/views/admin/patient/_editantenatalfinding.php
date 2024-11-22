<input type="hidden" name="edit_antenatal_id" value="<?php echo $result['antenatal_id']; ?>">
<div>                    
    <div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
            <input type="hidden" name="ipd" id="ipd" value="">
            <input type="hidden" name="visit_detail_id" id="visit_detail_id" value="<?php echo $result['visit_details_id']; ?>">
            <input name="anteexam_id" id="anteexam_id" type="hidden" class="form-control" value="<?php echo $result['anteexam_id']; ?>" />
            <input name="antenatal_id" id="antenatal_id" type="hidden" class="form-control" value="<?php echo $result['antenatal_id']; ?>" />
                <div class="row row-eq">
                    <div class="col-lg-8 col-md-8 col-sm-8">
                        <h4 class="mb0"><?php echo $this->lang->line('history_of_present_pregnancy'); ?></h4> 
                        <div class="row">
                            <div class="col-md-12"> 
                                <div class="dividerhr"></div>
                            </div><!--./col-md-12-->
                        </div><!--./col-md-12-->
                            <div class="row">
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('bleeding'); ?></label> 
                                        <input name="bleeding" class="form-control" id="bleeding" value="<?php echo $result['bleeding']; ?>" />
                                    </div>
                                </div>                        
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('headache'); ?></label> 
                                        <input name="headache" class="form-control" id="headache" value="<?php echo $result['headache']; ?>" />
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('pain'); ?></label> 
                                        <input name="pain" class="form-control" id="pain" value="<?php echo $result['pain']; ?>" />
                                    </div>
                                </div>                                 
                                                        
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('constipation'); ?></label> 
                                        <input name="constipation" class="form-control" id="constipation" value="<?php echo $result['constipation']; ?>" />
                                    </div>
                                </div>                                
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('vomiting'); ?></label> 
                                        <input name="vomiting" class="form-control" id="vomiting" value="<?php echo $result['vomiting']; ?>" />
                                    </div>
                                </div>
                            </div><!--./col-md-12-->
                            <div class="row">                                 
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('cough'); ?></label> 
                                        <input name="cough" class="form-control" id="cough" value="<?php echo $result['cough']; ?>" />
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('haemoroids'); ?></label> 
                                        <input name="haemoroids" class="form-control" id="haemoroids" value="<?php  echo $result['haemoroids'] ; ?>" />
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('discharge'); ?></label> 
                                        <input name="discharge" class="form-control" id="discharge" value="<?php echo $result['discharge']; ?>" />
                                    </div>
                                </div> 
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('oedema'); ?></label> 
                                        <input name="oedema" class="form-control" id="oedema" value="<?php  echo $result['oedema'] ; ?>" />
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('urinary_symptoms'); ?></label> 
                                        <input name="urinary_symptoms" class="form-control" id="urinary_symptoms" value="<?php echo $result['urinary_symptoms']; ?>" />
                                    </div>
                                </div>
                            </div><!--./col-md-12-->
                            <div class="row">                                 
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('vaginal'); ?></label> 
                                        <input name="vaginal" class="form-control" id="vaginal" value="<?php echo $result['vaginal']; ?>" />
                                    </div>
                                </div>                       
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('weight'); ?></label> 
                                        <input name="weight" class="form-control" id="weight" value="<?php  echo $result['antenatal_weight'] ; ?>" />
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('height'); ?></label> 
                                        <input name="height" class="form-control" id="height" value="<?php echo $result['antenatal_height']; ?>" />
                                    </div>
                                </div>                                 
                                <div class="col-sm-2 col-xs-4 col20">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label> 
                                        <input name="antenatal_date" class="form-control datetime" id="antenatal_date" value="<?php  echo $this->customlib->YYYYMMDDHisTodateFormat($result['antenatal_date'],$time_format); ?>" />
                                    </div>
                                </div>                                
                            </div> 
                            <div class="row pt5">                               
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('condition'); ?></label>
                                        <textarea name="condition" id="condition" rows="3" class="form-control" value="<?php echo $result['general_condition']; ?>" ><?php echo $result['general_condition']; ?></textarea>
                                    </div> 
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('special_findings_and_remark'); ?></label>
                                        <textarea name="special_finding_remarks"  rows="3" id="special_finding_remarks" placeholder="" class="form-control" value="<?php echo $result['finding_remark']; ?>"  ><?php echo $result['finding_remark']; ?></textarea>
                                    </div> 
                                </div>                           
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('pelvic_examination'); ?></label>
                                        <textarea name="pelvic_examination" rows="3" id="pelvic_examination" value="<?php echo $result['pelvic_examination']; ?>" placeholder="" class="form-control" ><?php echo $result['pelvic_examination']; ?></textarea>
                                    </div> 
                                </div>                      
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="email"><?php echo $this->lang->line('sp'); ?></label>
                                        <textarea name="sp" value="<?php echo $result['sp']; ?>" rows="3" id="sp"  class="form-control"><?php echo $result['sp']; ?></textarea>
                                    </div> 
                                </div>                                             
                            </div><!--./row--> 
                    </div><!--./col-md-8--> 
                    <div class="col-lg-4 col-md-4 col-sm-4 col-eq ptt10">
                        <h4><label><?php echo $this->lang->line('antenatal_examination'); ?></label></h4>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('uter_size'); ?></label>
                                    <input name="uter_size" id="uter_size" type="text" class="form-control" value="<?php echo $result['uter_size']; ?>" />      
                                    <span class="text-danger"><?php echo form_error('appointment_date'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>
                                    <?php echo $this->lang->line('uterus_size'); ?></label>
                                    <div><input class="form-control" type='text'name="uterus_size" id="uterus_size" value="<?php echo $result['uterus_size']; ?>" />
                                    </div>
                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('presentation_position'); ?></label>
                                    <div>
                                    <input name="presentation_position" id="presentation_position" type="text" class="form-control" value="<?php echo $result['presentation_position']; ?>" />
                                    </div>
                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('presenting_part_to_brim'); ?></label>
                                    <div>
                                        <input  name="presentation_brim" id="presentation_brim" type="text" class="form-control" value="<?php echo $result['brim_presentation']; ?>" />
                                    </div>
                                    <span class="text-danger"><?php echo form_error('case'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('foeta_heart'); ?></label>
                                    <div>
                                        <input name="foeta_heart" id="foeta_heart" type="text" class="form-control" value="<?php echo $result['foeta_heart']; ?>" />
                                    </div>
                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('blood_pressure'); ?></label>
                                    <div><input class="form-control" type='text' name='blood_pressure' id='blood_pressure' value="<?php echo $result['blood_pressure']; ?>" />
                                    </div>
                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('antenatal_oedema'); ?></label>
                                    <input class="form-control" type='text' name='antenatal_oedema' value="<?php echo $result['antenatal_oedema']; ?>" />                                        
                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('urine_sugar'); ?></label>
                                            <input class="form-control" type='text' name='urine' value="<?php echo $result['urine_sugar']; ?>" />                     
                                            <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                        </div>
                                    </div>     
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('urine_albumen'); ?></label>
                                            <input class="form-control" type='text' name='urine_aaibumen' value="<?php echo $result['urine']; ?>" />
                                            <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group"> 
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('antenatal_weight'); ?></label>
                                            <input class="form-control" type='text' name='antenatal_weight' value="<?php echo $result['antenatal_weight']; ?>" />                                
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('remark') ; ?></label>
                                            <textarea name="remark" id="remark" class="form-control" value="<?php echo $result['remark']; ?>"><?php echo $result['remark']; ?> </textarea>
                                            <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                        </div>
                                    </div> 
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('next_visit'); ?></label>
                                            <textarea name="next_visit" id="next_visit" class="form-control" value="<?php echo $result['next_visit']; ?>"><?php echo $result['next_visit']; ?> </textarea>
                                            <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <?php echo display_custom_fields('antenatal',$result['anteexam_id']); ?>
                                    </div>
                            </div><!--./row-->    
                            </div><!--./col-md-4-->
                        </div><!--./row-->        
                    </div><!--./col-md-12-->       
                </div><!--./row--> 
            </div>
        </div>  
      