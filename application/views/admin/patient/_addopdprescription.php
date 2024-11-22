<script type="text/javascript">
    $('.filestyle').dropify();
</script>
<input type="hidden" name="visit_details_id" value="<?php echo $visit_details_id;?>">
<input type="hidden" name="action" value="add">
<input type="hidden" name="ipd_prescription_basic_id" value="0">
                <div class="row">
                    <div class="col-sm-9">
                    <div class="ptt10">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('header_note'); ?></label> 
                                    <textarea style="height:50px" name="header_note" class="form-control" id="compose-textareaneww"></textarea>
                                </div> 
                                <hr/>
                            </div>
                             <div class="col-sm-12">  
                                <table class="table table-striped table-bordered table-hover">
                                    <tr>
                                        <td width="30%"><div class="form-group">
                                            <label><?php echo $this->lang->line('finding_category'); ?></label>
                                            <select class="form-control multiselect2 findingtype" style="width: 100%; height: 28px;" name='finding_type[]' id="finding_type" multiple>
                                                <option value=""><?php echo $this->lang->line('select'); ?> </option>
                                                <?php
                                                foreach ($findingtype as $fvalue) {
                                                ?>
                                                <option value="<?php echo $fvalue["id"]; ?>"><?php echo $fvalue["category"] ?>
                                                            </option>   
                                                        <?php } ?>
                                             </select>
                                            </div>
                                        </td>
                                        <td>                                           
                                                <label for="filterinput"> 
                                                    <?php echo $this->lang->line('finding_list'); ?></label>
                                                <div id="dd" class="wrapper-dropdown-3">
                                                    <input class="form-control filterinput height-33" type="text">
                                                    <ul class="dropdown scroll150 section_ul">
                                                        <li><label class="checkbox"><?php echo $this->lang->line('select'); ?></label></li>
                                                    </ul>
                                                </div>                                           
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                 <label><?php echo $this->lang->line('finding_description'); ?></label>
                                                    <textarea name="finding_description" id="finding_description"  class="form-control" rows="3"></textarea> 
                                            </div>
                                        </td>
                                        <td>  <label><?php echo $this->lang->line('finding_print'); ?> </label><br/><input type="checkbox" name="finding_print" rows="15" value="yes" checked></td>
                                    </tr>
                                 <tr id="trid"></tr>
                                </table>
                                </div>
                              
                            <table class="table table-striped table-bordered table-hover mb0" id="tableID">
                            <tr id="row1">
                                    <td> 
                                    <input type="hidden" name="rows[]" value="1">          
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
                                            <div>
                                                <label>
                                            <?php echo $this->lang->line('medicine_category'); ?></label> 
                                            <select class="form-control select2 medicine_category" style="width: 100%" name='medicine_cat_1'>
                                            <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select'); ?>
                                                    </option>
                                            <?php
                                            foreach ($medicineCategory as $dkey => $dvalue) {
                                            ?>
                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?>
                                                        </option>   
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>                      
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
                                            <div>
                                                <label><?php echo $this->lang->line('medicine'); ?></label>
                                                <select class="form-control select2 medicine_name" data-rowid="1" style="width: 100%" name="medicine_1">
                                                    <option value=""><?php echo $this->lang->line('select');?></option>
                                                </select>
                                                <div id="suggesstion-box0"><small id="stock_info_1"> </small></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
                                            <div>
                                                <label><?php echo $this->lang->line("dose"); ?></label>
                                                <select class="form-control select2 medicine_dosage" style="width: 100%" name="dosage_1">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                </select>
                                            </div> 
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
                                            <div>
                                               <label><?php echo $this->lang->line("dose_interval"); ?></label> 
                                               <select class="form-control  select2 interval_dosage" style="width:100%" id="interval_dosage_id" name='interval_dosage_1'>
                                                    <option value="<?php echo set_value('interval_dosage_id'); ?>"><?php echo $this->lang->line('select') ?>
                                                    </option>
                                                        <?php foreach ($intervaldosage as $dkey => $dvalue) {
                                                        ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] ?>
                                                        </option>
                                                                <?php }?>
                                                    </select>   
                                                    <span class="text-danger"><?php echo form_error('interval_dosage_id'); ?></span>
                                            </div> 
                                        </div>                                        
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
                                            <div>
                                                <label><?php echo $this->lang->line("dose_duration"); ?></label> 
                                               <select class="form-control  select2" style="width:100%" id="interval_dosage_id" name='duration_dosage_1'>
                                                    <option value="<?php echo set_value('interval_dosage'); ?>"><?php echo $this->lang->line('select') ?>
                                                    </option>
                                                        <?php foreach ($durationdosage as $dkey => $dvalue) {
                                                        ?>
                                                        <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] ?>
                                                        </option>
                                                                <?php }?>
                                                    </select>   
                                                    <span class="text-danger"><?php echo form_error('interval_dosage_id'); ?></span>
                                            </div> 
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6">
                                            <div>
                                                <label><?php echo $this->lang->line('instruction'); ?></label> 
                                                <textarea name="instruction_1" style="height: 28px;" class="form-control" ></textarea>
                                            </div> 
                                        </div>
                                    </td>
                                    <td>    
                                        <button type="button" class="closebtn delete_row crossbtnfa" data-row-id="1" autocomplete="off"><i class="fa fa-remove"></i></button>
                                    </td>
                                </tr>
                            </table>           
                          
                            <div class="col-sm-12">
                                     <a class="btn btn-info add-record addplus-xs" data-added="0"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_medicine');?></a>
                                <hr>
                            </div>
                            <div class="col-sm-12">
                                      <label><?php echo $this->lang->line('attachment');?></label> 
                                             <input type="file" data-height="30" class="filestyle form-control" name="document" autocomplete="off">
                                <hr>
                            </div>
                            <div>
                                <?php echo display_custom_fields('prescription'); ?>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('footer_note'); ?></label> 
                                    <textarea style="height:50px" rows="1" name="footer_note" class="form-control" id="compose-textareass"></textarea>
                                </div> 
                            </div>
                        </div>
                    </div> 
                </div>

                <div class="col-sm-3">
                    <div class="ptt10">
                    <div class="col-sm-12">
                        <div class="form-group">
                        <label>
                             <?php echo $this->lang->line('pathology'); ?></label>
                            
                             <select class="form-control multiselect2" style="width: 100%" name='pathology[]' multiple id="pathologyOpt">
                             
                                <?php foreach ($pathology as $key => $value) { ?>
                                    <option value="<?php echo $value["id"]; ?>"><?php echo " (".$value["short_name"].") ".$value["test_name"]; ?>
                                     </option>   
                                <?php } ?>
                             </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                        <label>
                             <?php echo $this->lang->line('radiology'); ?></label>
                             <select class="form-control multiselect2" style="width: 100%" name='radiology[]' id="radiologyOpt" multiple>
                            
                                <?php foreach ($radiology as $key => $value) { ?>
                                    <option value="<?php echo $value["id"]; ?>"><?php echo " (".$value["short_name"].") ".$value["test_name"]; ?>
                                     </option>   
                                <?php } ?>
                             </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                    <div class="ptt10">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('notification_to'); ?></label>
                             <?php
                                foreach ($roles as $role_key => $role_value) {
                                            $userdata = $this->customlib->getUserData();
                                            $role_id = $userdata["role_id"];
                                            ?>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="visible[]" value="<?php echo $role_value['id']; ?>" <?php if ($role_value["id"] == $role_id) {
                                                 echo "checked onclick='return false;'";
                                                }
                                             ?> <?php echo set_checkbox('visible[]', $role_value['id'], false) ?> /> <b><?php echo $role_value['name']; ?></b> </label>
                                    </div>
                                    <?php
                                    }
                                    ?>
                     </div>
                    </div>
                </div>
            </div>
        </div> 