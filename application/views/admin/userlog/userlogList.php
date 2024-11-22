<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row"> 
			<div class="col-md-12">            
                <div class="box box-primary">
                <?php $this->load->view('admin/report/_log');?>
                <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('user_log'); ?></h3>
                        <div class="box-tools pull-right">    
                            <button class="btn btn-primary btn-sm checkbox-toggle delete_all"><i class="fa fa-trash"></i> <?php echo $this->lang->line('delete_all'); ?></button>
                        </div>    
                    </div>
                    <form id="form1" action="" method="post">
                        <div class="box-body">
                            <div class="row">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="col-lg-3 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('search_type'); ?></label><small class="req"> *</small>
                                        <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($searchlist as $key => $search) {
                                                ?>
                                                <option value="<?php echo $key ?>" <?php
                                                if ((isset($search_type)) && ($search_type == $key)) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $search ?></option>
                                                    <?php } ?>
                                        </select>
                                        <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line("user_role"); ?></label>
                                        <select class="form-control" name="userroletype" onchange="showdate(this.value)">                                           
                                            <?php foreach ($userroletype as $key => $role) {
                                                ?>
                                                <option value="<?php echo $key ?>" <?php
                                                if ((isset($search_type)) && ($search_type == $key)) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $role ?></option>
                                                    <?php } ?>
                                        </select>
                                        <span class="text-danger" id="error_userrole_type"><?php echo form_error('userroletype'); ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-md-3" id="fromdate" style="display: none">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('date_from'); ?></label>
                                        <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                        <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-lg-3 col-sm-6 col-md-3" id="todate" style="display: none">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('date_to'); ?></label>
                                        <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                        <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>    
                    </form>
                    <div class="tabsborderbg"></div>  
                    <div class="box-body">
                        <div class="table-responsive" id="tab_allusers">
                            <table class="table table-striped table-bordered table-hover allajaxlist"data-export-title="<?php echo $this->lang->line('user_log'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('users'); ?></th>
                                        <th><?php echo $this->lang->line('role'); ?></th>
                                        <th><?php echo $this->lang->line('ip_address'); ?></th>
                                        <th><?php echo $this->lang->line('login_time'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('user_agent'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> 
        </div> 
    </section>
</div>

<script type="text/javascript">  

    function showdate(value) {
        if (value == 'period') {
            $('#fromdate').show();
            $('#todate').show();
        } else {
            $('#fromdate').hide();
            $('#todate').hide();
        }
    }
</script>

<script>
    $(document).on('click','.delete_all',function(){
delete_recordByIdReload('admin/userlog/deleteall');
    });
	
( function ( $ ) {
    'use strict';

    $(document).ready(function () {
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/userlog/checkvalidation',
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == "fail") {
                   $.each(data.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {
                    $("#error_search_type").html('');
                    $("#error_userrole_type").html('');                  
                    initDatatable('allajaxlist', 'admin/userlog/userlogreports/',data.param,[],100);
                }
            }
        });
        }
       ));
   });
} ( jQuery ) );
</script>