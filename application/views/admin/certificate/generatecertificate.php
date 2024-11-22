<div class="content-wrapper"> 
  
    <!-- Main content -->
    <section class="content">
        <?php if ($this->session->flashdata('msg')) { ?>
            <?php echo $this->session->flashdata('msg');
                $this->session->unset_userdata('msg');
            ?>
        <?php } ?>  
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('select_criteria'); ?></h3>
                        <div class="box-tools addmeeting box-tools-md">
                            <?php
                        if ($this->rbac->hasPrivilege('certificate', 'can_add')) {
                        ?>
                            <a data-toggle="modal" href="<?php echo site_url('admin/certificate') ?>" data-target="" class="btn btn-primary btn-sm uploadcontent"><i class="fa fa-newspaper-o ftlayer"></i>  <?php echo $this->lang->line('certificate_template'); ?></a>
                        <?php } ?>                          
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <form role="form1" id="form1" action="<?php echo site_url('admin/generatecertificate/search') ?>" method="post" class="">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="col-sm-4">
                                     <div class="form-group"> 
                                        <label><?php echo $this->lang->line('module'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="module" name="module" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <option value="opd" ><?php echo $this->lang->line('opd') ?></option>
                                                <option value="ipd" ><?php echo $this->lang->line('ipd') ?></option>
                                        </select>   
                                        <span class="text-danger" id="error_module"></span>
                                    </div>                                    
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group"> 
                                            <label><?php echo $this->lang->line('patient_status'); ?></label>
                                            <select autofocus="" id="patient_status" name="patient_status" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <option value="no" ><?php echo $this->lang->line('not_discharged') ?></option>
                                                <option value="yes" ><?php echo $this->lang->line('discharged') ?></option>                                                
                                            </select>   
                                           <span class="text-danger" id="error_patient_status"></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('certificate_template'); ?></label><small class="req"> *</small>
                                        <select name="certificate_id" class="form-control" >
                                           <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            if (isset($certificateList)) {
                                                foreach ($certificateList as $list) {
                                                    ?>                                                    
                                                    <option value="<?php echo $list->id ?>" <?php if (set_value('certificate_id') == $list->id) echo "selected=selected" ?>><?php echo $list->certificate_name ?></option>
                                                <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger" id="error_certificate_id"></span>
                                    </div>   
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>  
                    </div>               
                
                    <form method="post" action="<?php echo base_url('admin/generatecertificate/generatemultiple') ?>">
                        <div  id="duefee">
                          <div class="box-header ptbnull"></div>  
                            <div class="box-header ptbnull">
                                <input type="hidden" name="module_status"  id="module_status">
                                <input type="hidden" name="certificate_id"  id="certificate_id">
                                <h3 class="box-title titlefix"><?php echo $this->lang->line('patient_list'); ?></h3>
                                    <?php
                        if ($this->rbac->hasPrivilege('generate_certificate', 'can_view')) {
                        ?>
                                <button class="btn btn-info btn-sm printSelected pull-right" type="button" name="generate" title="generate multiple certificate"><?php echo $this->lang->line('generate'); ?></button>
                            <?php } ?>
                            </div>
                            <div class="box-body table-responsive">                               
                                <div class="tab-pane active table-responsive no-padding" id="tab_1">
                                    <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('patient_list'); ?>" >
                                        <thead> 
                                            <tr>
                                                <th><input type="checkbox" id="select_all" /> #</th>
                                                <th><?php echo $this->lang->line('opd_ipd_no'); ?></th>
                                                <th><?php echo $this->lang->line('patient_name'); ?></th>
                                                <th><?php echo $this->lang->line('gender'); ?></th>
                                                <th class=""><?php echo $this->lang->line('mobile_number'); ?></th>
                                                <th class="text-right"><?php echo $this->lang->line('discharged'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                        </tbody>
                                    </table>
                                </div>                                                                           
                            </div>                                                         
                        </div>
                    </form>                    
              </div>  
            </div>  
        </div> 
    </section>
</div>
<!-- //========datatable start===== -->

<!-- //========datatable end===== -->   
<script type="text/javascript">
    $(document).ready(function () {
        $('#select_all').on('click', function () {
            if (this.checked) {
                $('.checkbox').each(function () {
                    this.checked = true;
                });
            } else {
                $('.checkbox').each(function () {
                    this.checked = false;
                });
            }
        });

        $('.checkbox').on('click', function () {
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.printSelected', function () {
            var array_to_print = [];
           
            var certificateId = $("#certificate_id").val();
            var module_status = $("#module_status").val();
            $.each($("input[name='check']:checked"), function () {
                var patientId = $(this).data('patient_id');
                item = {}
                item ["patient_id"] = patientId;
                array_to_print.push(item);
            });
            if (array_to_print.length == 0) {
                alert("<?php echo $this->lang->line('no_record_selected');?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url("admin/generatecertificate/generatemultiple") ?>',
                    type: 'post',
                    dataType: "html",
                    data: {'data': JSON.stringify(array_to_print),'module_status':module_status, 'certificate_id': certificateId, },
                    success: function (response) {
                        
                        popup(response);

                    }
                });
            }
        });
    });
</script>

<script type="text/javascript">
    
    var base_url = '<?php echo base_url() ?>';     
 
    ( function ( $ ) {
    'use strict';

    $(document).ready(function () {
               emptyDatatable('allajaxlist', 'data');
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/generatecertificate/checkvalidation',
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
                    $('#module_status').val(data.param.module);
                   $('#certificate_id').val(data.param.certificate_id);
                    $("#error_module").html('');
                    $("#error_certificate_id").html(''); 
                    initDatatable('allajaxlist', 'admin/generatecertificate/getgeneratedatatable/',data.param,[],100,[
                        { "sWidth": "20px", "bSortable": false, "aTargets": [ -1 ] ,'sClass': 'dt-body-right'},
                        { "bSortable": false, "aTargets": [ 0 ] ,'sClass': 'dt-body-right'}
                        ]);
                }
            }
        });
        }
       ));
   }); 

} ( jQuery ) ); 
</script>