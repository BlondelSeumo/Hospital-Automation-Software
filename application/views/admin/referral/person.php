<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat() ;?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line("referral_person_list"); ?></h3>
                        <div class="box-tools pull-right">
                        <?php if ($this->rbac->hasPrivilege('referral_person', 'can_add')) { ?>
                                <a onclick="addPersonModal()" class="btn btn-primary btn-sm addperson"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_referral_person'); ?></a>
                        <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('referral_person_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr> 
                                        <th><?php echo $this->lang->line('referrer_name'); ?></th>
                                        <th><?php echo $this->lang->line('category'); ?></th>
                                        <th><?php echo $this->lang->line('commission'); ?></th>
                                        <th><?php echo $this->lang->line('referrer_contact'); ?></th>
                                        <th><?php echo $this->lang->line('contact_person_name'); ?></th>
                                        <th><?php echo $this->lang->line('contact_person_phone'); ?></th>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <?php if ($this->rbac->hasPrivilege('referral_person', 'can_edit') || $this->rbac->hasPrivilege('referral_person', 'can_delete')) { ?>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
if (empty($person)) {

} else {
    foreach ($person as $person_key => $person_value) {
        ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-toggle="popover" class="detail_popover"><?php echo $person_value->name; ?></a>
                                                </td>
                                                <td>
                                                    <?php echo $person_value->category_name; ?>
                                                </td>
                                                <td>
                                                    <?php
foreach ($person_value->commission as $value) {?>
                                                            <div><?php echo $this->lang->line($value->name) . " - " . $value->commission . "%"; ?></div>
                                                    <?php }
        ?>
                                                </td>
                                                <td><?php echo $person_value->contact; ?></td>
                                                <td><?php echo $person_value->person_name; ?></td>
                                                <td><?php echo $person_value->person_phone; ?></td>
                                                <td><?php echo $person_value->address; ?></td>
                                                <td class="mailbox-date pull-right">
                                                    <?php if ($this->rbac->hasPrivilege('referral_person', 'can_edit')) { ?>
                                                        <a href="#" onclick="getRecord('<?php echo $person_value->person_id; ?>')" class="btn btn-default btn-xs" data-target="#myModalEdit" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } if ($this->rbac->hasPrivilege('referral_person', 'can_delete')) { ?>
                                                        <a  class="btn btn-default btn-xs" data-toggle="tooltip" title="" onclick="delete_personById('<?php echo $person_value->person_id; ?>')" data-original-title="<?php echo $this->lang->line('delete') ?>">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
}
}
?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line("add_person"); ?></h4>
            </div>
            <form id="addperson" class="ptt10" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label for="name"><?php echo $this->lang->line("referrer_name"); ?></label>
                                    <span class="req"> *</span>
                                    <input  name="name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label for="referrer_contact"><?php echo $this->lang->line("referrer_contact"); ?></label>
                                    <input name="referrer_contact" placeholder="" type="text" class="form-control" value="<?php echo set_value('referrer_contact'); ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label for="person_name"><?php echo $this->lang->line("contact_person_name"); ?></label>
                                    <input name="person_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('person_name'); ?>" />
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label for="person_phone"><?php echo $this->lang->line("contact_person_phone"); ?></label>
                                    <input name="person_phone" placeholder="" type="text" class="form-control" value="<?php echo set_value('person_phone'); ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label for="category"><?php echo $this->lang->line('category'); ?></label>
                                    <span class="req"> *</span>
                                    <select onchange="getDefaultCommission(this.value)" name="category" placeholder="" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select_category'); ?></option>
                                        <?php foreach ($category as $value) {?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for=""><?php echo $this->lang->line("standard_commission"). ' (%)'; ?></label>
                                        <input type="text" class="form-control" name='commission' id="commission">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label for="address"><?php echo $this->lang->line("address"); ?></label>
                                    <input name="address" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label><?php echo $this->lang->line("commission_for_modules"). ' (%)' ; ?></label>
                                <span class="req"> *</span>
                                <button type="button" class="plusign" onclick="apply_to_all()" autocomplete="off"><?php echo $this->lang->line("apply_to_all") ?></button>
                                <div class="">
                                    <div class="form-group">
                                        <div class="row">
                                                <?php foreach ($type as $key => $value) {?>
                                                <div id="module_commission">
                                                    <input type="hidden" name="referral_type_id[]" value="<?php echo $value['id']; ?>">
                                                    <div class="col-sm-7 col-xs-7">
                                                        <label class="apply-label"><?php echo $this->lang->line($value['name']); ?></label></div>
                                                    <div class="col-sm-5 col-xs-5"><input class="commissionInput" type="text" name="module_commission[]" id="type_id_<?php echo $value['id']; ?>" class="form-control" autocomplete="off"></div>
                                                </div>
                                                <?php }?>
                                        </div>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>        
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" id="addpersonbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div> 
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line("edit_person"); ?></h4>
            </div>
            <form id="editperson" class="ptt10" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                  <div class="modal-body pb0 pt0">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label for="name"><?php echo $this->lang->line("referrer_name"); ?></label>
                                    <span class="req"> *</span>
                                    <input name="name" id="referrer_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                    <input type="hidden" name="person_id" id="person_id" />
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label for="referrer_contact"><?php echo $this->lang->line("referrer_contact"); ?></label>
                                    <input  name="referrer_contact" id="referrer_contact" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label for="person_name"><?php echo $this->lang->line("contact_person_name"); ?></label>
                                    <input  name="person_name" id="person_name" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />

                                </div>
                                <div class="col-sm-6 form-group">
                                    <label for="person_phone"><?php echo $this->lang->line("contact_person_phone"); ?></label>
                                    <input  name="person_phone" id="person_phone" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label for="category"><?php echo $this->lang->line('category'); ?></label>
                                    <span class="req"> *</span>
                                    <select  name="category" id="referrer_category" placeholder="" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select_category'); ?></option>
                                        <?php foreach ($category as $value) {?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for=""><?php echo $this->lang->line('standard_commission'). ' (%)'; ?></label>
                                        <input type="text" class="form-control" name='commission' id="commissionEdit">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label for="address"><?php echo $this->lang->line("address"); ?></label>
                                    <input  name="address" id="address" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label><?php echo $this->lang->line("commission_for_modules"); ?></label>
                                <span class="req"> *</span>
                                <button type="button" class="plusign" onclick="apply_to_all_edit()" autocomplete="off"><?php echo $this->lang->line("apply_to_all"); ?> </button>
                                <div class="">
                                    <div class="form-group">
                                        <div class="row">
                                            <?php foreach ($type as $key => $value) {?>
                                                <div id="module_commission">
                                                    <input type="hidden" name="referral_type_id[]" value="<?php echo $value['id']; ?>">
                                                    <div class="col-sm-7 col-xs-7">
                                                        <label class="apply-label"><?php echo $this->lang->line($value['name']); ?></label></div>
                                                    <div class="col-sm-5 col-xs-5"><input class="commissionInputEdit" type="text" name="module_commission[]" id="type_edit_id_<?php echo $value['id']; ?>" class="form-control" autocomplete="off"></div>
                                                </div>
                                            <?php }?>
                                        </div>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                         </div>    
                    </div>     
                    <div class="modal-footer">
                        <div class="pull-right">
                            <button type="submit" id="editpersonbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </div>
                </form>
        </div>
    </div>
</div>

<script>

    $(document).ready(function (e) {
        $('#addperson').on('submit', (function (e) {
            $("#addpersonbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralperson/add',
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
                            $('.' + index).html(value);
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#addpersonbtn").button('reset');
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>")
                }
            });
        }));
    });

    function getRecord(id) {
        $('#myModalEdit').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralperson/get/' + id,
            type: "POST",
            dataType: "json",
            success: function (data) {
                $("#referrer_name").val(data.name);
                $("#person_id").val(id);
                $("#referrer_category").val(data.category_id);
                $("#referrer_contact").val(data.contact);
                $("#person_name").val(data.person_name);
                $("#person_phone").val(data.person_phone);
                $("#address").val(data.address);
                $("#commissionEdit").val(data.standard_commission);
                $.each(data.commission, function(i,item){
                    $("#type_edit_id_" + item.referral_type_id).val(item.commission);
                });
            },
            error: function () {
                alert("Fail")
            }
        });
    }

    $(document).ready(function (e) {
        $('#editperson').on('submit', (function (e) {
            $("#editpersonbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralperson/update',
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
                    $("#editpersonbtn").button('reset');
                },
                error: function () {
                    alert("Fail")
                }
            });
        }));
    });
 
</script>

<script>
    function apply_to_all(){
        commission = $("#commission").val();
        $(".commissionInput").val(commission);
    }
	
    function apply_to_all_edit(){
        commission = $("#commissionEdit").val();
        $(".commissionInputEdit").val(commission);
    }
</script>

<script type="text/javascript">
    function getDefaultCommission(id){
        if(id != ""){
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralcommission/get_by_category/' + id,
                type: "POST",
                dataType: "json",
                success: function (data) {
                    if(data.length === 0){
                        $(".commissionInput").val("");
                    }else{
                        $.each(data, function(i,item){
                            $("#type_id_" + item.referral_type_id).val(item.commission);
                        });
                    }
                },
                error: function () {
                    alert("Fail")
                }

            });
        }else{
            $(".commissionInput").val("");
        }
    }

    function addPersonModal(){
        $('#myModal form')[0].reset();
        $("#myModal").modal("show");
    }
    
    $(document).ready(function (e) {
        $('#myModal,#myModalEdit').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
 
    function delete_personById(id){
        var conf = confirm("<?php echo $this->lang->line('delete_confirm');?>");
		if(conf == true){
			$.ajax({
				url: '<?php echo base_url(); ?>admin/referralperson/delete/' + id,
				type: "POST",
				dataType: "json",
				success: function (data) {
				successMsg(data.msg);
				window.location.reload(true); 
				},
				error: function () {
					alert("Fail")
				}
			});
		}
    }
</script>