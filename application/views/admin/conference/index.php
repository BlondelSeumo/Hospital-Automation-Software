<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('settings') ?></h3>
                    </div>
                    <form id="form1" action="<?php echo base_url() ?>admin/zoom_conference" name="employeeform" class="form-horizontal form-label-left" method="post" accept-charset="utf-8">

                        <div class="box-body">
                            <?php if ($this->session->flashdata('msg')) {
                                echo $this->session->flashdata('msg');
                                $this->session->unset_userdata('msg');
                            } ?>
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div>
                                <?php 
                                 if(!$this->session->has_userdata('zoom_access_token')){
                                   ?>
                                   <div class="alert alert-info"><?php echo $this->lang->line('access_token_not_generated_please_authenticate_your_account'); ?></div>
                                   <?php
                                }
                                
                                ?>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="hidden" name="id" value="<?php echo set_value('id', $setting->id); ?>">

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                            <?php echo $this->lang->line('zoom_api_key'); ?>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" name="zoom_api_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('zoom_api_key', $setting->zoom_api_key); ?>" />
                                            <span class="text-danger"><?php echo form_error('zoom_api_key'); ?></span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                            <?php echo $this->lang->line('zoom_api_secret'); ?>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" name="zoom_api_secret" placeholder="" type="text" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('zoom_api_secret', $setting->zoom_api_secret); ?>" />
                                            <span class="text-danger"><?php echo form_error('zoom_api_secret'); ?></span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                            <?php echo $this->lang->line('doctor_api_credential'); ?>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <label class="radio-inline">
                                                <input type="radio" name="use_doctor_api" value="0" <?php
                                                                                                    if (!$setting->use_doctor_api) {
                                                                                                        echo "checked";
                                                                                                    }
                                                                                                    ?>><?php echo $this->lang->line('disabled'); ?>
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="use_doctor_api" value="1" <?php
                                                                                                    if ($setting->use_doctor_api) {
                                                                                                        echo "checked";
                                                                                                    }
                                                                                                    ?>><?php echo $this->lang->line('enabled'); ?>
                                            </label>
                                            <span class="text-danger"><?php echo form_error('use_teacher_api'); ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                            <?php echo $this->lang->line('use_zoom_client_app'); ?>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <label class="radio-inline">
                                                <input type="radio" name="use_zoom_app" value="0" <?php
                                                                                                    if (!$setting->use_zoom_app) {
                                                                                                        echo "checked";
                                                                                                    }
                                                                                                    ?>><?php echo $this->lang->line('disabled'); ?>
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="use_zoom_app" value="1" <?php
                                                                                                    if ($setting->use_zoom_app) {
                                                                                                        echo "checked";
                                                                                                    }
                                                                                                    ?>><?php echo $this->lang->line('enabled'); ?>
                                            </label>

                                            <span class="text-danger"><?php echo form_error('use_zoom_app'); ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                            <?php echo $this->lang->line('default_opd_duration_in_minutes'); ?>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="number" class="form-control" value="<?php echo set_value('opd_duration', $setting->opd_duration); ?>" id="opdduration" name="opd_duration">
                                            <span class="text text-danger" id="title_error"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                            <?php echo $this->lang->line('default_ipd_duration_in_minutes'); ?>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="number" class="form-control" value="<?php echo set_value('ipd_duration', $setting->ipd_duration); ?>" id="ipdduration" name="ipd_duration">
                                            <span class="text text-danger" id="title_error"></span>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-4">

                                <div class="mb10"><img src="<?php echo base_url(); ?>backend/images/zoom-icon.png" /></div>
        <p><?php echo $this->lang->line('to_set_zoom_api'); ?> <a class="display-inline" href="https://marketplace.zoom.us/"> <?php echo $this->lang->line('click_here'); ?></a></p>
        <p class="pb0 mb0"><?php echo $this->lang->line('set_zoom_redirect_url'); ?>:</p>
        <p  class="word-break-all"><?php echo  site_url('admin/zoom_conference/generatetoken');?></p>
        <a href='<?php echo $oAuthURL;?>' class="btn btn-primary"> <?php echo $this->lang->line('get_access_token'); ?> </a>
                                </div>
                            </div>

                        </div><!--./box-body-->
                        <div class="box-footer">
                            <div class="col-md-6 col-sm-6 col-xs-6 col-md-offset-3 col-sm-offset-3">
                                <?php
                                if ($this->rbac->hasPrivilege('setting', 'can_edit')) {
                                ?>

                                    <button type="submit" class="btn btn-info pull-left sc-save-md"><?php echo $this->lang->line('save'); ?></button>
                                <?php

                                }
                                ?>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
</div>