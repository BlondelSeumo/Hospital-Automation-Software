<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('referral_payment_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('referral_payment', 'can_add')) { ?>
                                <a data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-sm addpayment"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_referral_payment'); ?></a>
                            <?php } ?>

							<?php if ($this->rbac->hasPrivilege('referral_person', 'can_view')) { ?>
                                <a href="<?php echo site_url('admin/referral/person'); ?>" class="btn btn-primary btn-sm addpayment"><?php echo $this->lang->line('referral_person'); ?></a>
							<?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                        <div class="download_label"><?php echo $this->lang->line('referral_payment_list'); ?></div>
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('payee'); ?></th>
                                        <th><?php echo $this->lang->line('patient_name'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('bill_no'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('bill_amount').' ('. $currency_symbol .')'; ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('commission_percentage'); ?> (%)</th>
                                        <th class="text-right"><?php echo $this->lang->line('commission_amount').' ('. $currency_symbol .')'; ?></th>
                                        <?php if ( ($this->rbac->hasPrivilege('referral_payment', 'can_edit')) || ($this->rbac->hasPrivilege('referral_payment', 'can_delete'))  ) { ?>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
										<?php } ?>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php  
                                        if (empty($payment)) {
                                            ?>
                                            <?php
                                        } else {
                                            foreach ($payment as $key => $value) {
                                                ?>
                                            <tr>
                                                <td class="mailbox-name"><a href="#" data-toggle="popover" class="detail_popover"><?php echo $value['name'] ?></a></td>
                                                <td><?php echo composePatientName($value["patient_name"],$value["patient_id"]); ?></td>
                                                <td class="text-right"><?php echo $value["prefix"].$value["billing_id"]; ?></td>
                                                <td class="text-right"><?php echo amountFormat($value["bill_amount"]); ?></td>
                                                <td class="text-right"><?php echo $value["percentage"]; ?></td>
                                                <td class="text-right"><?php echo $value["amount"]; ?></td>
                                                   <?php if ( ($this->rbac->hasPrivilege('referral_payment', 'can_edit')) || ($this->rbac->hasPrivilege('referral_payment', 'can_delete'))  ) { ?>
                                                <td class="mailbox-date pull-right noExport">
                                                    <?php if ($this->rbac->hasPrivilege('referral_payment', 'can_edit')) { ?>
                                                        <a href="#" onclick="getRecord('<?php echo $value['id'] ?>')" class="btn btn-default btn-xs" data-target="#myModalEdit" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if ($this->rbac->hasPrivilege('referral_payment', 'can_delete')) { ?>
                                                        <a  class="btn btn-default btn-xs" data-toggle="tooltip" title="" onclick="delete_recordByIdReload('admin/referralpayment/delete/<?php echo $value['id']; ?>', '<?php echo $this->lang->line('delete_confirm'); ?>')" data-original-title="<?php echo $this->lang->line('delete') ?>">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                                  <?php } ?>
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

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg modalfullmobile" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4 close_button" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group15">
                            <div>
                                <select class="form-control patient_list_ajax" style="width:100%" name='' id="addpatient_id">
                                    <option value="" selected><?php echo $this->lang->line('select_patient'); ?></option>
                                  
                                </select> 
                            </div>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                        </div>
                    </div><!--./col-sm-8-->
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-1">
                        <div class="p-2">
                        </div>    
                    </div> 
                </div><!-- ./row -->
            </div><!--./modal-header-->
            <form id="addpayment" accept-charset="utf-8" enctype="multipart/form-data" method="post">
            <div class="modal-body pt0 pb0">
                    <input name="patient_id" id="patient_id" type="hidden" class="form-control" />
                            <div class="row row-eq">
                                <div class="col-lg-8 col-md-8 col-sm-8">
                                    <div id="ajax_load" style="text-align: center;width: 100%;"></div>
                                    <div class="row ptt10" id="patientDetails" style="display:none">
                                        <div class="col-md-9 col-sm-9 col-xs-9">
                                            <ul class="singlelist">
                                                <li class="singlelist24bold">
                                                    <span id="listname"></span></li>
                                                <li>
                                                    <i class="fas fa-user-secret" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('guardian'); ?>"></i>
                                                    <span id="guardian"></span>
                                                </li>
                                            </ul>
                                            <ul class="multilinelist">
                                                <li>
                                                    <i class="fas fa-venus-mars" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('gender'); ?>"></i>
                                                    <span id="genders" ></span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-tint" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('blood_group'); ?>"></i>
                                                    <span id="blood_group"></span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-ring" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('marital_status'); ?>"></i>
                                                    <span id="marital_status"></span>
                                                </li>
                                            </ul>
                                            <ul class="singlelist">
                                                <li>
                                                    <i class="fas fa-hourglass-half" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('age'); ?>"></i>
                                                    <span id="age"></span>
                                                </li>
                                                <li>
                                                    <i class="fa fa-phone-square" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('phone'); ?>"></i>
                                                    <span id="listnumber"></span>
                                                </li>
                                                <li>
                                                    <i class="fa fa-envelope" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('email'); ?>"></i>
                                                    <span id="email"></span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-street-view" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line('address'); ?>"></i>
                                                    <span id="address" ></span>
                                                </li>
                                                <li>
                                                    <b><?php echo $this->lang->line('any_known_allergies') ?> </b>
                                                    <span id="allergies" ></span>
                                                </li>
                                                <li>
                                                    <b><?php echo $this->lang->line('remarks') ?> </b>
                                                    <span id="note"></span>
                                                </li>
                                                <li>
                                                    <b><?php echo $this->lang->line('national_identification_number') ?> </b>
                                                    <span id="identification_number"></span>
                                                </li>
                                                <li>
                                                    <b><?php echo $this->lang->line('tpa_id') ?> </b>
                                                    <span id="insurance_id"></span>
                                                </li>
                                                <li>
                                                    <b><?php echo $this->lang->line('tpa_validity') ?> </b>
                                                    <span id="insurance_validity"></span>
                                                </li>
                                            </ul>
                                        </div><!-- ./col-md-9 -->
                                        <div class="col-md-3 col-sm-3 col-xs-3">
                                            <div class="pull-right">                                            
                                                <?php  $file = "uploads/patient_images/no_image.png"; ?>
                                                <img class="modal-profile-user-img img-responsive" src="<?php echo base_url() . $file.img_time() ?>" id="image" alt="User profile picture">
                                            </div>
                                        </div><!-- ./col-md-3 -->
                                    </div>
                                </div><!--./col-md-8-->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-eq ptt10">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('patient_type'); ?></label>
                                                <small class="req"> *</small>
                                                <select style="width:100%" name="patient_type" id="patient_type" class="form-control select2">
                                                    <option value=""><?php echo $this->lang->line('select_type'); ?></option>
                                                    <?php foreach ($type as $key => $value) {?>
                                                        <option value="<?php echo $value['id']; ?>"><?php echo $this->lang->line($value['name']); ?></option>
                                                    <?php }?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error(); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                               <label><?php echo $this->lang->line('bill_no_case_id'); ?></label> <small class="req"> *</small>
                                               <select style="width:100%"  name="bill_no" id="bill_no_case_id" class="form-control select2">
                                                   <option value=''><?php echo $this->lang->line('select')?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('patient_bill_amount').' ('. $currency_symbol .')'; ?></label>
                                                <small class="req"> *</small>
                                                <input class="form-control" id="bill_amount" name="bill_amount" type="text" placeholder="<?php echo $this->lang->line('bill_amount'); ?>">
                                                <span class="text-danger"><?php echo form_error(); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('payee'); ?></label>
                                                <small class="req"> *</small>
                                                <select style="width:100%" name="payee" id="payee" class="form-control select2">
                                                    <option value=""><?php echo $this->lang->line('select_payee'); ?></option>
                                                    <?php foreach ($person as $key => $value) {?>
                                                        <option value="<?php echo $value->person_id; ?>"><?php echo $value->name; ?></option>
                                                    <?php }?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error(); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('commission_percentage'); ?> (%)</label>
                                                <small class="req"> *</small>
                                                <input oninput="calculate_commission()" class="form-control" id="percentage" name="percentage" type="text" placeholder="<?php echo $this->lang->line('percentage'); ?>" >
                                                <span class="text-danger"><?php echo form_error(); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('commission_amount').' ('. $currency_symbol .')'; ?></label>
                                                <small class="req"> *</small>
                                                <input class="form-control" id="commission_amount" name="commission_amount" type="text" placeholder="<?php echo $this->lang->line('commission_amount'); ?>">
                                                <span class="text-danger"><?php echo form_error(); ?></span>
                                            </div>
                                        </div>
                                    </div><!--./row-->
                                </div><!--./col-md-4-->
                            </div><!--./row-->
                        </div>    
                        <div class="modal-footer">
                            <div class="pull-right">
                                <button type="submit" id="addpaymentbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </div>
                </form>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm400" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_referral_payment'); ?></h4>
            </div>
            <form id="editpayment" class="ptt10" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body pt0 pb0">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('commission_percentage'); ?> (%)</label>
                        <span class="req"> *</span>
                        <input id="commission_percentage" name="commission_percentage" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />
                        <input id="paymentid" name="paymentid" placeholder="" type="hidden" class="form-control" />
                    </div>
                </div>
                <div class="modal-body pt0 pb0">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('commission_amount').' ('. $currency_symbol .')'; ?></label>
                        <span class="req"> *</span>
                        <input id="editcommission_amount" name="commission_amount" placeholder="" type="text" class="form-control" value="<?php echo set_value('name'); ?>" />                       
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="editpaymentbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $('.select2').select2();
    });
	
    $(document).ready(function (e) {
        $('#addpayment').on('submit', (function (e) {
            $("#addpaymentbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralpayment/add',
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
                    $("#addpaymentbtn").button('reset');
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>");
                }
            });
        }));
    });
 
    function getRecord(id) {
        $('#myModalEdit').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralpayment/get/' + id,
            type: "POST",
            dataType: "json",
            success: function (data) {
                $("#commission_percentage").val(data.percentage);
                $("#editcommission_amount").val(data.amount);
                $("#paymentid").val(id);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
    }

    $(document).ready(function (e) {
        
        $('#editpayment').on('submit', (function (e) {
            $("#editpaymentbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/referralpayment/update',
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
                    $("#editpaymentbtn").button('reset');
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>");
                }
            });
        }));
    });
</script>
<script>

	$('#addpatient_id').on('select2:select', function (e) {
     let id=$(this).val();
     $.ajax({
        url: base_url+'admin/patient/patientDetails',
        type: "POST",
        data: {id: id},
        dataType: 'json',
        beforeSend: function() {

       var base_url = "<?php echo base_url(); ?>backend/images/loading.gif";
        $("#ajax_load").html("<center><img src='" + base_url + "'/>");
        },
        success: function (res) {

            if (res) {
                $("#ajax_load").html("");
                $("#patientDetails").show();
                $('#patient_unique_id').html(res.patient_unique_id);
                $('#patient_id').val(res.id);
                $('#listname').html(res.patient_name+" ("+res.id+")");
                $('#guardian').html(res.guardian_name);
                $('#listnumber').html(res.mobileno);
                $('#email').html(res.email);
                $("#age").html(res.patient_age);
                $("#insurance_id").html(res.insurance_id);
                $("#insurance_validity").html(res.insurance_validity);
                $("#identification_number").html(res.identification_number);
                $("#address").html(res.address);
                $("#note").html(res.note);
                $("#genders").html(res.gender);
                $("#marital_status").html(res.marital_status);
                $("#blood_group").html(res.blood_group_name);
                $("#allergies").html(res.known_allergies);
                $("#image").attr("src", '<?php echo base_url() ?>' + res.image+'<?php echo img_time(); ?>');
            } else {
                $("#ajax_load").html("");
                $("#patientDetails").hide();
            }
        },
         error: function(xhr) { // if error occured
            alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

  $('#ajax_load').html("");
         },
        complete: function() {
     $('#ajax_load').html("");
        }
    });
});

</script>

<script>
    function getBillAmount(){
        type = $("#patient_type").val();
        patient_id = $("#patient_id").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralpayment/getBillAmount',
            type: "POST",
            data:{type:type,patient_id:patient_id},
            success: function (data) {
                $("#bill_amount").val(data);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
    }

    function calculate_commission(){
        bill_amount = $("#bill_amount").val();
        percentage = $("#percentage").val();
        amount = (bill_amount*percentage)/100;
        $("#commission_amount").val(amount.toFixed(2));
    }
    
    $(document).ready(function (e) {
        $('#myModal,#myModalEdit').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
 
 $('#myModal').on('hidden.bs.modal', function () {
    $(".patient_list_ajax").select2("val", "");  
    $("#patientDetails").css("display", "none");
    $("#patient_type").select2("val", "");   
    $("#bill_no_case_id").select2("val", "");
    $("#payee").select2("val", "");
    $('div #myModal #patientDetails').find('span').text("");
    $('form#addpayment').find('input:text, input:password, input:file, textarea').val('');
    $('form#addpayment').find('select option:selected').removeAttr('selected');
    $('form#addpayment').find('input:checkbox, input:radio').removeAttr('checked');   
});

     function holdModal(modalId) {
                $('#' + modalId).modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            }

    
      $('#patient_type').on('select2:select', function (e) {   
        var div_data = "";
        $('#operation_name').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        var patient_id = $("#patient_id").val();
        var type = $(this).val();
        
        if (type > '0'){
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralpayment/getBillNo',
            type: "POST",
             dataType: 'json',
            data:{type:$(this).val(),patient_id:patient_id},
            success: function (data) {
                $.each(data, function (i, obj)
                { 
                    var name='';
                    var sel = "";
                   if(obj.case_id==null){
                    name=obj.prefixe_name+obj.bill_no;
                   }else{
                    name=obj.prefixe_name+obj.bill_no+"/"+obj.case_id;
                   }
                   
                    div_data += "<option value=" + obj.bill_no + " " + sel + ">" + name+ "</option>";
                });
                $("#bill_no_case_id").html("<option value=''><?php echo $this->lang->line('select')?></option>");
                $('#bill_no_case_id').append(div_data);           
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });        
      }
});

		$('#bill_no_case_id').on('select2:select', function (e) {
			var type=$('#patient_type').val();
			$.ajax({
                url: base_url+'admin/referralpayment/getBillAmount',
                type: "POST",
                 dataType: 'json',
                data:{type:type,bill_no:$(this).val()},
                success: function (data) {
                   $('#bill_amount').val(data.total_bill);
                },
                error: function () {
                    alert("<?php echo $this->lang->line('fail'); ?>");
                }
            });
		});
</script> 

<script>
     $('#payee').on('select2:select', function (e) {
             let type = $("#patient_type").val();
       let  payee = $(this).val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/referralpayment/getCommission',
            type: "POST",
            data:{type:type,payee:payee},
            success: function (data) {               
				if(type != '' && payee != ''){
				  $("#percentage").val(data);
                calculate_commission();
				}else{
					 $("#percentage").val('');
				}				
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
     });
    function getPercentage(){
   
    }
</script>

<?php $this->load->view('admin/patient/patientaddmodal') ?>