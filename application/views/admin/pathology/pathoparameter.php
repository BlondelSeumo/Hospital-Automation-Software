<div class="content-wrapper">  
    <!-- Main content -->
    <section class="content">
        <div class="row">           
            <div class="col-md-2">
                <div class="box border0">
                      <ul class="tablists">
                        <?php if ($this->rbac->hasPrivilege('pathology_category', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/pathologycategory/addcategory"><?php echo $this->lang->line('pathology_category'); ?></a></li>
                    <?php } if ($this->rbac->hasPrivilege('pathology_unit', 'can_view')) { ?>
                        <li><a href="<?php echo base_url(); ?>admin/pathologycategory/unit" class=""><?php echo $this->lang->line('unit'); ?></a></li>
                         <?php } if ($this->rbac->hasPrivilege('pathology_parameter', 'can_view')) { ?>
                        <li><a class="active" href="<?php echo base_url(); ?>admin/pathologycategory/pathoparameter" class=""><?php echo $this->lang->line('pathology_parameter'); ?></a></li>
                         <?php }  ?>
                    </ul>
                </div>
            </div>            
            <div class="col-md-10">              
                <div class="box box-primary" id="tachelist">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('pathology_parameter_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('pathology_parameter', 'can_add')) { ?>
                                <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm pathology"><i class="fa fa-plus"></i>  <?php echo $this->lang->line('add_pathology_parameter'); ?></a> 
                            <?php } ?>    
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <div class="download_label"><?php echo $this->lang->line('pathology_parameter_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('parameter_name'); ?></th>
                                        <th><?php echo $this->lang->line('reference_range'); ?></th>
                                        <th><?php echo $this->lang->line('unit'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($parameterName as $lab) {
                                        ?>
                                        <tr>
                                            <td><?php echo $lab['parameter_name']; ?></td>
                                            <td><?php echo $lab['reference_range']; ?></td>
                                            <td><?php echo $lab['unit_name']; ?></td>
                                            <td><?php echo $lab['description']; ?></td>
                                            <td class="text-right">
                                                <?php if ($this->rbac->hasPrivilege('pathology_parameter', 'can_edit')) { ?>
                                                    <a data-target="#editmyModal" onclick="get(<?php echo $lab['id'] ?>)"  class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <?php
                                                }
                                                if ($this->rbac->hasPrivilege('pathology_parameter', 'can_delete')) {
                                                    ?>
                                                        <a href="<?php echo base_url(); ?>admin/pathologycategory/deleteparameter/<?php echo $lab['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>');">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                    
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                  
                    <div class="mailbox-controls"></div>

                </div>
            </div> 
        </div>
    </section>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_pathology_parameter'); ?></h4> 
            </div>
            <form id="formadd" action="<?php echo site_url('admin/pathologycategory/addparameter') ?>" id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('parameter_name'); ?></label><small class="req"> *</small>
                            <input autofocus="" name="parameter_name" placeholder="" type="text" class="form-control" />
                            <span class="text-danger"><?php echo form_error('parameter_name'); ?></span>
                        </div> 
                         <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                <label><?php echo $this->lang->line('reference_range'); ?></label><small class="req"> *</small>
                                <input autofocus="" name="from_reference_range" placeholder="<?php echo $this->lang->line('from'); ?>" type="text" class="form-control"  />
                                <span class="text-danger"><?php echo form_error('from_reference_range'); ?></span>
                            </div>
                            <div class="col-md-6">
                                <label><?php echo $this->lang->line('reference_range'); ?></label><small class="req"> *</small>
                             <input autofocus="" name="to_reference_range" placeholder="<?php echo $this->lang->line('to'); ?>" type="text" class="form-control" />
                                <span class="text-danger"><?php echo form_error('to_reference_range'); ?></span>
                            </div>
                            </div>
                        </div>                        
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('unit'); ?></label>
                                    <small class="req"> *</small> 
                                    <select name="unit" onchange="" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($unitname as $value) {
                                                ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['unit_name']; ?></option>
                                            <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                </div>                      
                         <div class="form-group">
                            <label><?php echo $this->lang->line('description'); ?></label>
                            <input autofocus="" name="description" placeholder="" type="text" class="form-control" />
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>          
                    </div>
                </div><!--./modal-->     
                <div class="modal-footer">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div><!--./row--> 
    </div>
</div>

<div class="modal fade" id="editmyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_pathology_category'); ?></h4> 
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/pathologycategory/addparameter') ?>" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('parameter_name'); ?></label><small class="req"> *</small>
                            <input autofocus="" id="parameter_name" name="parameter_name" placeholder="" type="text" class="form-control" value="" />
                            <span class="text-danger"><?php echo form_error('parameter_name'); ?></span>
                            <input type="hidden" id="id" name="pathology_parameter_id">
                        </div>                        
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                <label><?php echo $this->lang->line('reference_range'); ?></label><small class="req"> *</small>
                                <input autofocus="" name="from_reference_range" id="from_reference_range" placeholder="<?php echo $this->lang->line('from'); ?>" type="text" class="form-control"  />
                                <span class="text-danger"><?php echo form_error('from_reference_range'); ?></span>
                            </div>
                            <div class="col-md-6">
                                <label><?php echo $this->lang->line('reference_range'); ?></label><small class="req"> *</small>
                                <input autofocus="" name="to_reference_range" id="to_reference_range" placeholder="<?php echo $this->lang->line('to'); ?>" type="text" class="form-control"  />
                                <span class="text-danger"><?php echo form_error('to_reference_range'); ?></span>
                            </div>
                            </div>
                        </div>
                        <div class="form-group">
                                    <label><?php echo $this->lang->line('unit'); ?></label>
                                    <small class="req"> *</small> 
                                    <select name="unit" id="unit" onchange="" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($unitname as $value) {
                                                ?>
                                                <option value="<?php echo $value['id'] ?>"><?php echo $value['unit_name']; ?></option>
                                            <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('unit'); ?></span>
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('description') ; ?></label>
                            <input autofocus="" id="description" name="description" placeholder="" type="text" class="form-control" value="" />
                            <span class="text-danger"><?php echo form_error('description'); ?></span>                            
                        </div>                 
                    </div>
                </div><!--./modal-->        
                <div class="modal-footer">
                    <button type="submit" id="editformaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div><!--./row--> 
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $('#formadd').on('submit', (function (e) {
            $("#formaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
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
                    $("#formaddbtn").button('reset');
                },
                error: function () {

                }
            });
        }));
    });

    function get(id) {
        $('#editmyModal').modal('show');
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/pathologycategory/get_data_parameter/' + id,
            success: function (result) {
                var range = result.reference_range.split("-");
                $('#id').val(result.id);
                $('#parameter_name').val(result.parameter_name);
                $('#test_value').val(result.test_value);               
                $('#from_reference_range').val(range[0]);
                $('#to_reference_range').val(range[1]);
                $('#unit').val(result.unit);
                $('#description').val(result.description);
            }
        });
    }
    
    $(document).ready(function (e) {
        $('#editformadd').on('submit', (function (e) {
            $("#editformaddbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = " ";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#editformaddbtn").button('reset');
                },
                error: function () {}
            });
        }));
    });
	
	$(".pathology").click(function(){
		$('#formadd').trigger("reset");
	});

    $(document).ready(function (e) {
        $('#myModal,#editmyModal').modal({
            backdrop: 'static',
            keyboard: false,
            show:false
        });
    });
</script>