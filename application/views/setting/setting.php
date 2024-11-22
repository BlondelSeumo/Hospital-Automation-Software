<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row">
           <?php $this->load->view('setting/sidebar.php'); ?>
            
            <div class="col-md-10">
               <div class="box box-primary">
                    <div class="box-header ptbnull">
                    <h3 class="box-title titlefix"> <?php echo $this->lang->line('general_settings'); ?></h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="">
                        <form role="form" id="schsetting_form" action="<?php echo site_url('schsettings/ajax_schedit') ?>" class="" method="post" enctype="multipart/form-data">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('hospital') . " " . $this->lang->line('name'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="name" name="sch_name" value="<?php echo $settinglist[0]['name'] ?>">
                                                <span class="text-danger"><?php echo form_error('name'); ?></span> <input type="hidden" name="sch_id" value="<?php echo $settinglist[0]['id']; ?>">
                                            </div>
                                             <span class="text-danger"><?php echo form_error('sch_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('hospital') . " " . $this->lang->line('code') ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="dise_code" name="sch_dise_code"  value="<?php echo $settinglist[0]['dise_code'] ?>">
                                                <span class="text-danger"><?php echo form_error('dise_code'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--./row-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-sm-2"><?php echo $this->lang->line('address'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="address" name="sch_address" value="<?php echo $settinglist[0]['address'] ?>"> <span class="text-danger"><?php echo form_error('address'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--./row-->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('phone'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="phone" name="sch_phone" value="<?php echo $settinglist[0]['phone'] ?>"><span class="text-danger"><?php echo form_error('phone'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('email'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control"  id="email" name="sch_email" value="<?php echo $settinglist[0]['email'] ?>">
                                                <span class="text-danger"><?php echo form_error('email'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--./row-->
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('hospital')." ".$this->lang->line('logo') ?><small class="req"> *</small></label>
                                         <?php
                                            if ($settinglist[0]['image'] == "") {
                                                ?>
                                                <img src="<?php echo base_url("uploads/hospital_content/logo/images.png".img_time()) ?>" class="" alt="" style="height: 15px;">
                                                <?php
                                            } else {
                                                ?>
                                                <img src="<?php echo base_url('uploads/hospital_content/logo/'.$settinglist[0]['image'].img_time()) ?>" class="" alt="" style="height: 15px;margin-top: 5px;">
                                                <?php
                                            }
                                            ?>&nbsp;
                                           <?php if ($this->rbac->hasPrivilege('general_setting', 'can_edit')) { ?>
				                            <a href="#schsetting" role="button" class="btn btn-primary btn-sm upload_logo pull-right"   data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"><i class="fa fa-picture-o"></i> <?php echo $this->lang->line('edit_logo'); ?></a>
				                        <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                         <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('hospital')." ".$this->lang->line('small')." ".$this->lang->line('logo') ?><small class="req"> *</small></label>
                                            <?php
                                            if ($settinglist[0]['mini_logo'] == "") {
                                                ?>
                                                <img src="<?php echo base_url('uploads/hospital_content/logo/images.png'.img_time()) ?>" class="" alt="">
                                                <?php
                                            } else {
                                                ?>
                                                <img src="<?php echo base_url('uploads/hospital_content/logo/'.$settinglist[0]['mini_logo'].img_time()) ?>" class="" alt="">
                                                <?php
                                            }
                                            ?>
                                             &nbsp; <?php if ($this->rbac->hasPrivilege('general_setting', 'can_edit')) { ?>
					                            <a href="#" role="button" class="btn btn-primary btn-sm upload_minilogo pull-right"  data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"><i class="fa fa-picture-o"></i> <?php echo $this->lang->line('edit') . " " . $this->lang->line('small') . " " . $this->lang->line('logo'); ?></a>
					                        <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="settinghr"></div>
                                        <h4 class="session-head"><?php echo $this->lang->line('language'); ?></h4>
                                    </div><!--./col-md-12-->

                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('language'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <select  id="language_id" name="sch_lang_id" class="form-control" >
                                                    <option value="">--<?php echo $this->lang->line('select') ?>--</option>
                                                    <?php
                                                    foreach ($languagelist as $language) {
                                                        ?>
                                                        <option value="<?php echo $language['id']; ?>" <?php if($settinglist[0]['lang_id']==$language['id']){ echo 'selected' ; } ?> ><?php echo $language['language'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('language_id'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-6"><?php echo $this->lang->line('language_rtl_text_mode'); ?></label>
                                            <div class="col-sm-6">
                                               <label class="radio-inline">
                                                <input type="radio" name="sch_is_rtl" value="disabled" <?php if($settinglist[0]['is_rtl']=='disabled'){ echo 'checked' ; } ?> ><?php echo $this->lang->line('disabled'); ?>
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="sch_is_rtl" value="enabled" <?php if($settinglist[0]['is_rtl']=='enabled'){ echo 'checked' ; } ?>><?php echo $this->lang->line('enabled'); ?>
                                            </label>

                                            </div>
                                        </div>
                                    </div>
                                </div><!--./row-->

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="settinghr"></div>
                                        <h4 class="session-head"><?php echo $this->lang->line('date') . " " . $this->lang->line('time'); ?></h4>
                                    </div><!--./col-md-12-->
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('date_format'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <select  id="date_format" name="sch_date_format"  class="form-control" >
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php

                                                        foreach ($dateFormatList as $key => $dateformat) {
                                                            ?>
                                                            <option value="<?php echo $key ?>" <?php if($settinglist[0]['date_format']==$key){ echo 'selected' ; } ?> ><?php echo $dateformat; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('date_format'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('time_zone'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <select  id="language_id" name="sch_timezone" class="form-control" >
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                     <?php

                                                        foreach ($timezoneList as $key => $timezone) {
                                                            ?>
                                                            <option value="<?php echo $key ?>" <?php if($settinglist[0]['timezone']==$key){ echo 'selected' ; } ?> ><?php echo $timezone ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('timezone'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--./row-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="settinghr"></div>
                                        <h4 class="session-head"><?php echo $this->lang->line('currency') ?></h4>
                                    </div><!--./col-md-12-->
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('currency'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <select  id="currency" name="sch_currency" class="form-control" >
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                     <?php
                                                    foreach ($currencyList as $currency) {
                                                        ?>
                                                        <option value="<?php echo $currency ?>" <?php if($settinglist[0]['currency']==$currency){ echo 'selected' ; } ?>><?php echo $currency; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('currency'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('currency_symbol'); ?><small class="req"> *</small></label> 
                                            <div class="col-sm-8">
                                                  <input id="currency_symbol" name="sch_currency_symbol" placeholder="" type="text" class="form-control" value="<?php echo $settinglist[0]['currency_symbol'] ?>" />
                                                <span class="text-danger"><?php echo form_error('currency_symbol'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">

                                            <label class="col-sm-4"><?php echo $this->lang->line('credit_limit'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                  <input id="credit_limit" name="credit_limit" placeholder="" type="text" class="form-control" value="<?php echo $settinglist[0]['credit_limit']; ?>" />
                                                <span class="text-danger"><?php echo form_error('currency_symbol'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('time_format') ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                  <select  id="time_format" name="time_format" class="form-control" >
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php
                                                    foreach ($timeFormat as $time_k => $time_v) {
                                                        ?>
                                                        <option value="<?php echo $time_k ; ?>" <?php if($settinglist[0]['time_format']==$time_k){ echo 'selected' ; } ?> ><?php echo $time_v; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                 <span class="text-danger"><?php echo form_error('time_format'); ?></span>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div><!--./row-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="settinghr"></div>
                                        <div class="relative">     
                                            <h4 class="session-head"><?php echo $this->lang->line('mobile_app'); ?></h4>
                                            <?php if (!$app_ver) {
                                                ?>
                                                <button type="button" class="btn btn-info btn-sm impbtntitle3" data-toggle="modal" data-target="#andappModal">Register Your Android App</button>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div><!--./col-md-12-->
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-sm-2"> <?php echo $this->lang->line('mobile_app_api_url') ?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="mobile_api_url" id="mobile_api_url" class="form-control" value="<?php echo $settinglist[0]['mobile_api_url']; ?>">
                                                <span class="text-danger"><?php echo form_error('mobile_api_url'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    </div>                                  

                                    <div class="row">
                                        <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-5"><?php echo $this->lang->line('app_primary_color_code'); ?></label>
                                            <div class="col-sm-7">
                                                <input type="text" name="app_primary_color_code" id="app_primary_color_code" class="form-control" value="<?php echo $settinglist[0]['app_primary_color_code']; ?>">
                                                <span class="text-danger"><?php echo form_error('app_primary_color_code'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-6"> <?php echo $this->lang->line('app_secondary_color_code'); ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="app_secondary_color_code" id="app_secondary_color_code" class="form-control" value="<?php echo $settinglist[0]['app_secondary_color_code']; ?>">
                                                <span class="text-danger"><?php echo form_error('app_secondary_color_code'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                    <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                             <label class="col-sm-2"> <?php echo $this->lang->line('mobile_app')." ".$this->lang->line('logo') ?></label>
                                             <?php
						                        if ($settinglist[0]['app_logo'] == "") {
						                            ?>
						                            <img src="<?php echo base_url('uploads/hospital_content/logo/images.png'.img_time()) ?>" class="" alt="" style="height: 15px;">
						                            <?php
						                        } else {
						                            ?>
						                            <img src="<?php echo base_url('uploads/hospital_content/logo/'.$settinglist[0]['app_logo'].img_time()) ?>" class="" alt="" style="height: 15px;margin-top: 5px;">
						                            <?php
						                        }
						                        ?>
                                                <?php if ($this->rbac->hasPrivilege('general_setting', 'can_edit')) { ?>
					                            &nbsp;<a href="#" role="button" class="btn btn-primary btn-sm upload_applogo "   data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"><i class="fa fa-picture-o"></i> <?php echo $this->lang->line('edit')." ".$this->lang->line('app')." ".$this->lang->line('logo'); ?></a>
					                        <?php } ?>
                                        </div>
                                    </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="settinghr"></div>
                                                <div class="relative">     
                                                    <h4 class="session-head"><?php echo $this->lang->line('miscellaneous'); ?></h4>
                                                </div>
                                        </div><!--./col-md-12-->
                                    </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-5"><?php echo $this->lang->line('doctor_restriction_mode'); ?></label>
                                            <div class="col-sm-7">
                                               <label class="radio-inline">
                                                    <input type="radio" name="doctor_restriction_mode" value="disabled" <?php if($settinglist[0]['doctor_restriction']=='disabled'){ echo 'checked' ; } ?> ><?php echo $this->lang->line('disabled'); ?>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="doctor_restriction_mode" value="enabled" <?php if($settinglist[0]['doctor_restriction']=='enabled'){ echo 'checked' ; } ?> ><?php echo $this->lang->line('enabled'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-6"> <?php echo $this->lang->line('superadmin_restriction'); ?></label>
                                            <div class="col-sm-6">
                                                <label class="radio-inline">
                                                    <input type="radio" name="superadmin_restriction_mode" value="disabled" <?php if($settinglist[0]['superadmin_restriction']=='disabled'){ echo 'checked' ; } ?> ><?php echo $this->lang->line('disabled'); ?>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="superadmin_restriction_mode" value="enabled" <?php if($settinglist[0]['superadmin_restriction']=='enabled'){ echo 'checked' ; } ?>><?php echo $this->lang->line('enabled'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="settinghr"></div>
                                        <h4 class="session-head"><?php echo $this->lang->line('current_theme'); ?></h4>
                                    </div><!--./col-md-12-->
                                    <div class="col-sm-12">
                                        <div id="input-type">
                                            <div class="row">                                                
                                                <div class="col-sm-3 col-xs-6 col20">
                                                    <label class="radio-img">
                                                        <input name="theme" <?php
                                                        if ($settinglist[0]['theme'] == "default.jpg") {
                                                            echo "checked";
                                                        }
                                                        ?>  value="default.jpg" type="radio" />
                                                        <img src="<?php echo base_url('backend/images/default.jpg'.img_time()); ?>">
                                                    </label>
                                                </div>
                                                <div class="col-sm-3 col-xs-6 col20">
                                                    <label class="radio-img">
                                                        <input name="theme" <?php
                                                        if ($settinglist[0]['theme'] == "red.jpg") {
                                                            echo "checked";
                                                        }
                                                        ?> value="red.jpg" type="radio" />
                                                        <img src="<?php echo base_url('backend/images/red.jpg'.img_time()); ?>">
                                                    </label>
                                                </div>
                                                <div class="col-sm-3 col-xs-6 col20">
                                                    <label class="radio-img">
                                                        <input name="theme" <?php
                                                        if ($settinglist[0]['theme'] == "blue.jpg") {
                                                            echo "checked";
                                                        }
                                                        ?> value="blue.jpg" type="radio" />
                                                        <img src="<?php echo base_url('backend/images/blue.jpg'.img_time()); ?>">
                                                    </label>
                                                </div>
                                                <div class="col-sm-3 col-xs-6 col20">
                                                    <label class="radio-img">
                                                        <input name="theme" <?php
                                                        if ($settinglist[0]['theme'] == "gray.jpg") {
                                                            echo "checked";
                                                        }
                                                        ?> value="gray.jpg" type="radio" />
                                                        <img src="<?php echo base_url('backend/images/gray.jpg'.img_time()); ?>">
                                                    </label>
                                                </div>
                                            </div><!--./row-->
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <?php
                                if ($this->rbac->hasPrivilege('general_setting', 'can_edit')) {
                                    ?>
                                    <button type="button" class="btn btn-primary submit_schsetting pull-right edit_setting" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"> <?php echo $this->lang->line('save'); ?></button>
                                    <?php
                                }
                                ?>
                            </div>
                        </form>
                    </div><!-- /.box-body -->
                </div>
            </div><!--./col-md-9-->            
        </div> 
    </section>
</div>

<div class="modal fade" id="modal-uploadfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('edit_logo'); ?> <span id="imagesize">(182px X 18px)</span></h4>
            </div>
            <div class="modal-body">
                <form class="box_upload boxupload" id="ajaxlogo" method="post" action="<?php echo site_url('schsettings/ajax_editlogo') ?>" enctype="multipart/form-data">
                    <div class="box__input">
                        <i class="fa fa-download box__icon"></i>
                        <input class="box__file" type="file" name="file" id="file"/>
                        <input value="<?php echo $settinglist[0]['id'] ?>" type="hidden" name="id" id="id"/>
                        <label for="file"><strong>Choose a file</strong><span class="box__dragndrop"> or drag it here</span>.</label>
                        <button class="box__button" type="submit">Upload</button>
                    </div>
                    <div class="box__uploading">Uploading&hellip;</div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="andappModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Register your Android App purchase code</h4>
            </div>
            <form action="<?php echo site_url('admin/admin/updateandappCode') ?>" method="POST" id="andapp_code">
                <div class="modal-body andapp_modal-body">
                    <div class="error_message">

                    </div>
                    <div class="form-group">
                        <label class="ainline"><span>Envato Market Purchase Code for Smart Hospital Android App ( <a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"> How to find it?</a> )</span></label>
                        <input type="text" class="form-control" id="input-app-envato_market_purchase_code" name="app-envato_market_purchase_code">
                        <div id="error" class="input-error text text-danger"></div>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Your Email registered with Envato</label>
                        <input type="text" class="form-control" id="input-app-email" name="app-email">
                        <div id="error" class="input-error text text-danger"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Saving...">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var base_url = '<?php echo base_url(); ?>';
    $('.upload_logo').on('click', function (e) {
        e.preventDefault();
        $("#myModalLabel").html('<?php echo $this->lang->line('edit_logo') ?> (182px X 18px)');
        $("#imagesize").html(' (182px X 18px)');
        var $this = $(this);
        $("#ajaxlogo").attr('action', '<?php echo site_url('schsettings/ajax_editlogo') ?>');
        $this.button('loading');
        $('#modal-uploadfile').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    });

     var base_url = '<?php echo base_url(); ?>';
    $('.upload_applogo').on('click', function (e) {
        e.preventDefault();
        $("#myModalLabel").html('<?php echo $this->lang->line('edit')." ".$this->lang->line('app')." ".$this->lang->line('logo') ?> (152px X 18px)');
        $("#imagesize").html(' (152px X 18px)');
        var $this = $(this);
        $("#ajaxlogo").attr('action', '<?php echo site_url('schsettings/ajax_applogo') ?>');
        $('#modal-uploadfile').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    });
	
    $('.upload_minilogo').on('click', function (e) {
        e.preventDefault();
        $("#myModalLabel").html('<?php echo $this->lang->line('edit') . " " . $this->lang->line('small') . " " . $this->lang->line('logo') ?> (41px X 25px)');
        $("#imagesize").html(' (41px X 25px)');
        var $this = $(this);
        $("#ajaxlogo").attr('action', '<?php echo site_url('schsettings/ajax_minilogo') ?>');        
        $('#modal-uploadfile').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    });
	
// set focus when modal is opened
    $('#modal-uploadfile').on('shown.bs.modal', function () {
        $('.upload_logo').button('reset');
    });

     $('#modal-uploadappfile').on('shown.bs.modal', function () {
        $('.upload_applogo').button('reset');
    });
    $('#modal-minilogo').on('shown.bs.modal', function () {
        $('.upload_minilogo').button('reset');
    });

    $('.edit_setting').on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.button('loading');
        $.ajax({
            url: base_url + '/schsettings/getSchsetting',
            type: 'POST',
            data: {},
            dataType: "json",
            success: function (result) {
                $('input[name="sch_id"]').val(result.id);
                $('input[name="sch_name"]').val(result.name);
                $('input[name="sch_dise_code"]').val(result.dise_code);
                $('input[name="sch_phone"]').val(result.phone);
                $('input[name="credit_limit"]').val(result.credit_limit);
                $('#opd_record_month').val(result.opd_record_month);
                $('input[name="sch_email"]').val(result.email);
                $('input[name="fee_due_days"]').val(result.fee_due_days);
                $('input[name="sch_currency_symbol"]').val(result.currency_symbol);
                $('input[name="sch_mobile_api_url"]').val(result.mobile_api_url);
                $('input[name="sch_app_primary_color_code"]').val(result.app_primary_color_code);
                $('input[name="sch_app_secondary_color_code"]').val(result.app_secondary_color_code);
                $('textarea[name="sch_address"]').text(result.address);
                $("input[name=sch_is_rtl][value=" + result.is_rtl + "]").attr('checked', 'checked');
                $("input[name=doctor_restriction_mode][value=" + result.doctor_restriction + "]").attr('checked', 'checked');
                $("input[name=superadmin_restriction_mode][value=" + result.superadmin_restriction + "]").attr('checked', 'checked');
                $("input[name=theme][value='" + result.theme + "']").attr('checked', 'checked');
                $('select[name="sch_session_id"] option[value="' + result.session_id + '"]').attr("selected", "selected");
                $('select[name="sch_start_month"] option[value="' + result.start_month + '"]').attr("selected", "selected");
                $('select[name="sch_lang_id"] option[value="' + result.lang_id + '"]').attr("selected", "selected");
                $('select[name="sch_timezone"] option[value="' + result.timezone + '"]').attr("selected", "selected");
                $('select[name="sch_date_format"] option[value="' + result.date_format + '"]').attr("selected", "selected");
                $('select[name="time_format"] option[value="' + result.time_format + '"]').attr("selected", "selected");
                $('select[name="sch_currency"] option[value="' + result.currency + '"]').attr("selected", "selected");
                $('#schsetting').modal({
                    show: true,
                    backdrop: 'static',
                    keyboard: false
                });
            },
            error: function () {
                console.log("error on form");
            }

        }).done(function () {
            $this.button('reset');
        });
    });

    $(document).on('click', '.submit_schsetting', function (e) {
        var $this = $(this);
        $this.button('loading');
        $.ajax({
            url: '<?php echo site_url("schsettings/ajax_schedit") ?>',
            type: 'post',
            data: $('#schsetting_form').serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }

                $this.button('reset');
            }
        });
    });
</script>
<script type="text/javascript">
    // feature detection for drag&drop upload
    var isAdvancedUpload = function ()
    {
        var div = document.createElement('div');
        return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
    }();

    // applying the effect for every form   
    var forms = $('#ajaxlogo');
    Array.prototype.forEach.call(forms, function (form)
    {
        var input = form.querySelector('input[type="file"]'),
                label = form.querySelector('label'),
                errorMsg = form.querySelector('.box__error span'),
                restart = form.querySelectorAll('.box__restart'),
                droppedFiles = false,
                showFiles = function (files)
                {
                   
                },
                showErrors = function (files)
                {
                    toastr.error(files);
                },
                showSuccess = function (msg)
                {
                    toastr.success(msg);
                    setTimeout(function () {
                        window.location.reload(1);
                    }, 2000);
                },
                triggerFormSubmit = function ()
                {
                    var event = document.createEvent('HTMLEvents');
                    event.initEvent('submit', true, false);
                    form.dispatchEvent(event);
                };

        // letting the server side to know we are going to make an Ajax request
        var ajaxFlag = document.createElement('input');
        ajaxFlag.setAttribute('type', 'hidden');
        ajaxFlag.setAttribute('name', 'ajax');
        ajaxFlag.setAttribute('value', 1);
        form.appendChild(ajaxFlag);

        // automatically submit the form on file select
        input.addEventListener('change', function (e)
        {
            showFiles(e.target.files);
            triggerFormSubmit();
        });

        // drag&drop files if the feature is available
        if (isAdvancedUpload)
        {
            form.classList.add('has-advanced-upload'); // letting the CSS part to know drag&drop is supported by the browser

            ['drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop'].forEach(function (event)
            {
                form.addEventListener(event, function (e)
                {
                    // preventing the unwanted behaviours
                    e.preventDefault();
                    e.stopPropagation();
                });
            });
            ['dragover', 'dragenter'].forEach(function (event)
            {
                form.addEventListener(event, function ()
                {
                    form.classList.add('is-dragover');
                });
            });
            ['dragleave', 'dragend', 'drop'].forEach(function (event)
            {
                form.addEventListener(event, function ()
                {
                    form.classList.remove('is-dragover');
                });
            });
            form.addEventListener('drop', function (e)
            {
                droppedFiles = e.dataTransfer.files; // the files that were dropped
                showFiles(droppedFiles);
                triggerFormSubmit();
            });
        }

        // if the form was submitted
        form.addEventListener('submit', function (e)
        {
            // preventing the duplicate submissions if the current one is in progress
            if (form.classList.contains('is-uploading'))
                return false;

            form.classList.add('is-uploading');
            form.classList.remove('is-error');

            if (isAdvancedUpload) // ajax file upload for modern browsers
            {
                e.preventDefault();
                // gathering the form data
                var ajaxData = new FormData(form);
                if (droppedFiles)
                {
                    Array.prototype.forEach.call(droppedFiles, function (file)
                    {
                        ajaxData.append(input.getAttribute('name'), file);
                    });
                }

                // ajax request
                var ajax = new XMLHttpRequest();
                ajax.open(form.getAttribute('method'), form.getAttribute('action'), true);
                ajax.onload = function ()
                {
                    form.classList.remove('is-uploading');
                    if (ajax.status >= 200 && ajax.status < 400)
                    {
                        var data = JSON.parse(ajax.responseText);
                        if (data.success) {
                            var sucmsg = "Record updated Successfully";
                            showSuccess(sucmsg);
                        }                        
                        if (!data.success) {
                            var message = "";
                            $.each(data.error, function (index, value) {
                                message += value;
                            });
                            showErrors(message);
                        }
                        ;
                    } else
                        alert('Error. Please, contact the webmaster!');
                };

                ajax.onerror = function ()
                {
                    form.classList.remove('is-uploading');
                    alert('Error. Please, try again!');
                };

                ajax.send(ajaxData);
            } else // fallback Ajax solution upload for older browsers
            {
                var iframeName = 'uploadiframe' + new Date().getTime(),
                        iframe = document.createElement('iframe');

                $iframe = $('<iframe name="' + iframeName + '" style="display: none;"></iframe>');

                iframe.setAttribute('name', iframeName);
                iframe.style.display = 'none';

                document.body.appendChild(iframe);
                form.setAttribute('target', iframeName);

                iframe.addEventListener('load', function ()
                {
                    var data = JSON.parse(iframe.contentDocument.body.innerHTML);
                    form.classList.remove('is-uploading');
                    form.removeAttribute('target');
                    if (!data.success)
                        errorMsg.textContent = data.error;
                    iframe.parentNode.removeChild(iframe);
                });
            }
        });

        // restart the form if has a state of error/success
        Array.prototype.forEach.call(restart, function (entry)
        {
            entry.addEventListener('click', function (e)
            {
                e.preventDefault();
                input.click();
            });
        });

        // Firefox focus bug fix for file input
        input.addEventListener('focus', function () {
            input.classList.add('has-focus');
        });
        input.addEventListener('blur', function () {
            input.classList.remove('has-focus');
        });

    });
</script>