<div class="content-wrapper">  
    <!-- Main content -->
    <section class="content">
        <?php if ($this->session->flashdata('msg')) { ?>
            <?php echo $this->session->flashdata('msg') ?>
        <?php } ?>  
        <div class="row"> 
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> <?php echo $this->lang->line('select_criteria'); ?></h3>
                        <?php if (($this->rbac->hasPrivilege('staff_id_card', 'can_view'))) { ?>
                        <a class="btn btn-info btn-sm  pull-right" type="button" href="<?php echo base_url('admin/staffidcard/'); ?>" title="<?php echo $this->lang->line('id_card_template'); ?>"><i class="fa fa-newspaper-o ftlayer"></i> <?php echo $this->lang->line('id_card_template'); ?></a>
                    <?php } ?>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <form role="form" id="form1"  method="post" class="">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="col-sm-6">
                                    <div class="form-group"> 
                                        <label><?php echo $this->lang->line('role'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="role_id" name="role_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            if(!empty($staffRolelist)){
                                            foreach ($staffRolelist as $staffRolelist_value) {
                                                ?>
                                                <option value="<?php echo $staffRolelist_value['id'] ?>" <?php if (set_value('role_id') == $staffRolelist_value['id']) echo "selected=selected" ?>><?php echo $staffRolelist_value['type'] ?></option>
                                                <?php
                                            }}?>
                                        </select>
                                        <span class="text-danger" id="error_role_id"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                    <label><?php echo $this->lang->line('id_card_template'); ?></label>
                                    <small class="req"> *</small>
                                    <select id="id_card" name="id_card" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            if (isset($idcardlist)) {
                                                foreach ($idcardlist as $idcardlist_value) {
                                                    ?>
                                                    <option value="<?php echo $idcardlist_value->id ?>" <?php if (set_value('id_card') == $idcardlist_value->id) echo "selected=selected" ?>><?php echo $idcardlist_value->title ?></option>
                                                <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger" id="error_id_card"></span>
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
              
                    <form method="post" action="<?php echo base_url('admin/generatestaffidcard/generatemultiple') ?>">
                        <input type="hidden" name="staffrole" id="staffrolevalue">
                        <input type="hidden" name="idcard" id="idcard">
                        <div id="duefee">
                          <div class="box-header ptbnull"></div>   
                            <div class="box-header ptbnull">
                                <h3 class="box-title titlefix"><?php echo $this->lang->line('staff_list'); ?></h3>
                                <button class="btn btn-info btn-sm printSelected pull-right" type="button" name="generate" title="<?php echo $this->lang->line('generate_certificate'); ?>"><?php echo $this->lang->line('generate'); ?></button>
                            </div>
                            <div class="box-body table-responsive">
                                <div class="tab-pane active table-responsive no-padding" id="tab_1">  
                                    <div class="download_label"><?php echo $this->lang->line('staff_list'); ?></div>
                                    <table class="table table-striped table-bordered table-hover allajaxlist" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                            <th><input type="checkbox" id="select_all" /></th>
                                            <th><?php echo $this->lang->line('staff_id'); ?></th>
                                            <th><?php echo $this->lang->line('staff_name'); ?></th>
                                            <th><?php echo $this->lang->line('designation'); ?></th>
                                            <th><?php echo $this->lang->line('department'); ?></th>
                                            <th><?php echo $this->lang->line('father_name'); ?></th>
                                            <th><?php echo $this->lang->line('mother_name'); ?></th>
                                            <th><?php echo $this->lang->line('date_of_joining'); ?></th>
											<th><?php echo $this->lang->line('address'); ?></th>
                                            <th><?php echo $this->lang->line('phone'); ?></th>
                                            <th><?php echo $this->lang->line('date_of_birth'); ?></th>
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
<div class="response"> 
</div>
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
            var idCard = $("#idcard").val();
            $.each($("input[name='check']:checked"), function () {
                var staffId = $(this).data('staff_id');
                console.log(staffId);
                item = {}
                item ["staff_id"] = staffId;
                array_to_print.push(item);
            });
            if (array_to_print.length == 0) {
                alert("<?php echo $this->lang->line('no_record_selected');?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url("admin/generatestaffidcard/generatemultiple") ?>',
                    type: 'post',
                    dataType: "html",
                    data: {'data': JSON.stringify(array_to_print),'id_card': idCard },
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
</script>

<script type="text/javascript">

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
            url: '<?php echo base_url(); ?>admin/generatestaffidcard/checkvalidation',
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
                    $('#staffrolevalue').val(data.param.role_id);
                   $('#idcard').val(data.param.id_card);
                    $("#error_role_id").html('');
                    $("#error_id_card").html(''); 
                    initDatatable('allajaxlist', 'admin/generatestaffidcard/getstafflistdatatable/',data.param,[],100,[{ "bSortable": false, "aTargets": [ 0 ] ,'sClass': 'dt-body-right'}]);
                }
            }
        });
        }
       ));
   });

} ( jQuery ) ); 
</script>