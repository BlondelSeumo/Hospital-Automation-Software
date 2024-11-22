<div class="content-wrapper">  
    <section class="content">
        <div class="row">
            <?php $this->load->view('admin/pharmacy/pharmacyMasters') ?>
            <div class="col-md-10">              
                <div class="box box-primary" id="tachelist">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('medicine_dosage_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('medicine_dosage', 'can_add')) { ?>
                                <a onclick="addModal()" class="btn btn-primary btn-sm medicine"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_medicine_dosage'); ?></a> 
                            <?php } ?>    
                        </div>
                    </div> 
                    <div class="box-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('medicine_dosage_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example" >
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('category_name'); ?></th>
                                        <th><?php echo $this->lang->line('dosage'); ?></th>
                                        <th><?php echo $this->lang->line('unit'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    if (!empty($medicineDosage)) {
                                        foreach ($medicineDosage as $dosage) {
                                           $subcount = 1;
                                            foreach ($dosage as $key => $value) {                                           
                                            
                                            ?>
                                            <tr>
                                                <td><?php if($subcount==1){ echo $value['medicine_category']; } ?></td>
                                                <td><?php echo $value['dosage']; ?></td>
                                                <td><?php echo $value['unit']; ?></td>
                                                <td class="text-right">
                                                    <?php  if ($this->rbac->hasPrivilege('medicine_dosage', 'can_edit')) { ?>
                                                        <a data-target="#editmyModal" onclick="get(<?php echo $value['id'] ?>)" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <?php
                                                    }
                                                    if ($this->rbac->hasPrivilege('medicine_dosage', 'can_delete')) {
                                                        ?>
                                                        <a  class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_medicine_dosage('<?php echo $value['id'] ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } } ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $subcount++;                                            
                                        }
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
                <h4 class="modal-title"><?php echo $this->lang->line('add_medicine_dosage'); ?></h4> 
            </div>
            <form id="formadd" action="<?php echo site_url('admin/medicinedosage/add') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">               
                <div class="scroll-area">
                    <div class="modal-body pt0 pb0">
                        <div class="ptt10">
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('medicine_category'); ?></label><small class="req"> *</small>
                                <select name="medicine_category" placeholder=""   type="text" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($medicineCategory as $key => $catvalue) {
                                        ?>
                                        <option value="<?php echo $catvalue["id"] ?>"><?php echo $catvalue["medicine_category"] ?></option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('medicine_category'); ?></span>
                            </div>                            
                            <div id="dose_fields">                    
                                <div class="row">
                                    <div class="col-sm-5"> 
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('dose'); ?></label>
                                            <small class="req"> *</small>
                                            <input autofocus="" name="dosage[]" placeholder="" type="text" class="form-control"/>
                                            <span class="text-danger"><?php echo form_error('dosage'); ?></span>
                                        </div> 
                                    </div> 
                                    <div class="col-sm-6"> 
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('unit'); ?></label>
                                            <small class="req"> *</small>
                                            <select name="unit[]" class="form-control">
                                                <option value=""> <?php echo $this->lang->line('select'); ?></option>
                                                <?php 
                                                foreach ($unitname as $key => $value) {                                        
                                                ?>
                                                <option value="<?php echo $value['id'];?>"><?php echo $value['unit_name'];?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                        </div> 
                                    </div>                         
                                    <div class="col-sm-1">
                                        <div class="form-group">                           
                                        </div>
                                    </div>
                                </div>                       
                            </div> 
                            <div class="row">
                                <div class="col-sm-12"> 
                                    <div class="form-group">
                                        <label><a class="btn addplus-xs btn-primary add-record" data-added="0"><i class="fa fa-plus"></i>&nbsp;<?php echo $this->lang->line('add'); ?></a></label>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div><!--./modal--> 
                </div>
                <div class="modal-footer">
                    <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
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
                <h4 class="modal-title"><?php echo $this->lang->line('edit_medicine_dosage'); ?></h4> 
            </div>
            <form id="editformadd" action="<?php echo site_url('admin/medicinedosage/add') ?>" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="ptt10">
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('category_name'); ?></label><small class="req"> *</small>
                            <select name="medicine_category" id="medicine_category" placeholder=""  onchange="editMedicineName(this.value)" type="text" class="form-control">
                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                <?php foreach ($medicineCategory as $key => $catvalue) {
                                    ?>
                                    <option value="<?php echo $catvalue["id"] ?>"><?php echo $catvalue["medicine_category"] ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('medicine_category'); ?></span>
                            <input type="hidden" id="id" name="medicinecategoryid">
                        </div>                       
                        <div class="row">
                        <div class="col-sm-4"> 
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('dosage'); ?></label>
                                <small class="req"> *</small>
                                <input autofocus="" name="dosageid" id="dosageid" placeholder="" type="hidden" class="form-control" />
                                <input autofocus="" name="dosage[]" id="dosage" placeholder="" type="text" class="form-control" />
                            </div> 
                        </div> 
                        <div class="col-sm-4"> 
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('unit'); ?></label>
                                <small class="req"> *</small>
                                <select name="unit[]" id="unit" class="form-control" >
                                    <option value=""> <?php echo $this->lang->line('select');?></option>
                                    <?php 
                                    foreach ($unitname as $key => $value) {                                       
                                    ?>
                                    <option value="<?php echo $value['id'];?>"><?php echo $value['unit_name'];?></option>
                                    <?php
                                }
                                    ?>
                                </select>
                            </div> 
                        </div> 
                    </div>              
                    </div>
                </div><!--./modal-body-->         
                <div class="modal-footer">
                    <button type="submit" id="editformaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div><!--./row--> 
    </div>
</div> 

<script> 
    
    $(document).on('click','.add-record',function(){
       add_more();
    });

    $(document).on('click','.delete_row',function(){
       var record_id=$(this).data('row-id');
       $('#fields_data'+record_id).html('');
    });

    function add_more(){
        <?php 
        $unit_listval='<option value="">'.$this->lang->line('select').'</option>';
        foreach ($unitname as $key => $value) { 
           $unit_listval.='<option value="'.$value['id'].'" >'.$value['unit_name'].'</option>'; 
        }
        ?>
        var data_id = makeid(8);
        $('#dose_fields').append('<div class="row dosage_row" id="fields_data'+data_id+'"><div class="col-sm-5"><div class="form-group"><input autofocus="" name="dosage[]" placeholder="" type="text" class="form-control"/><span class="text-danger"><?php echo form_error('dosage'); ?></span></div></div><div class="col-sm-6"><div class="form-group"><select autofocus="" name="unit[]" placeholder="" type="text" class="form-control" ><?php echo $unit_listval; ?></select><span class="text-danger"><?php echo form_error('unit'); ?></span></div></div><div class="col-sm-1"><div class="form-group"><button type="button" class="closebtn delete_row" data-row-id="'+data_id+'" autocomplete="off"><i class="fa fa-remove"></i></button></div></div></div></div>');
    }

    function makeid(length) {
        var result = '';
        var characters = '0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    $(document).ready(function (e) {
        $(".select2").select2();
    });

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
                beforeSend: function() {
                },
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
        $.ajax({
            dataType: 'json',
            url: '<?php echo base_url(); ?>admin/medicinedosage/get_data/' + id,
            beforeSend: function() {
     
    },
    success: function(result) {
        console.log(result);
        $('#dosageid').val(result.id);
                $('#dosage').val(result.dosage); 
                $('#unit option[value="'+result.units_id+'"]').prop('selected', true);                
                $('#medicine_category').val(result.medicine_category_id); 
                $('#editmyModal').modal('show');    
    },
    error: function(xhr) { // if error occured
        alert("Error occured.please try again");  
    },
    complete: function() {
      
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
                        var message = "";
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
                error: function () {

                }
            });
        }));
    });	
	
	$(".medicine").click(function(){
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
<script>
    function addModal(){
        $("#myModal").modal("show");
        $("div").remove(".dosage_row");
    }

    function delete_medicine_dosage(id){
        delete_recordByIdReload('admin/medicinedosage/delete/'+id, '<?php echo $this->lang->line('delete_confirm'); ?>');
    }
</script>