<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('components_issue_details'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('issue_component', 'can_add')) {?>

                                 <button type="button" class="btn btn-primary btn-sm issuecomponent" id="load1" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('issue_component'); ?></button>
                             <?php } if ($this->rbac->hasPrivilege('blood_bank_components', 'can_view')) {?>
                             <a href="<?php echo base_url(); ?>admin/bloodbank/components" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('components'); ?></a>
                         <?php } ?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('components_issue_details'); ?></div>
                        <table class="table table-striped table-bordered table-hover ajaxlist" width="100%" data-export-title="<?php echo $this->lang->line('components_issue_details'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                                    <th><?php echo $this->lang->line('case_id'); ?></th>
                                    <th><?php echo $this->lang->line('issue_date'); ?></th>
                                    <th><?php echo $this->lang->line('received_to'); ?></th>
                                    <th><?php echo $this->lang->line('blood_group'); ?></th>
                                    <th><?php echo $this->lang->line('component'); ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('donor_name'); ?></th>
                                    <th><?php echo $this->lang->line('bags'); ?></th>
                                    <?php
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                                <th class="white-space-nowrap"><?php echo $fields_value->name; ?></th>
                                                <?php
                                            }
                                        }
                                      ?>
                                    <th class="text-right"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
 
<div class="modal fade" id="addPaymentModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pupclose" data-toggle="tooltip"  data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('payments'); ?></h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <form id="formadd" accept-charset="utf-8" method="post">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                <div class="row">
                     <div class="col-lg-5 col-md-5 col-sm-11">
                        <div class="row">
                           <div class="col-lg-9 col-md-7 col-sm-8 col-xs-8">                               
                            <div class="p-2 select2-full-width">
                              <select class="form-control patient_list_ajax"  name='patient_id' id="addpatient_id" onchange="get_PatientDetails(this.value)">
                                <option value=""><?php echo $this->lang->line('select_patient'); ?></option>
                                </select>
                            </div>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                            </div>
                            <div class="col-lg-3 col-md-5 col-sm-4 col-xs-2">
                             <div class="form-group15">
                              <?php if ($this->rbac->hasPrivilege('patient', 'can_add')) {?>
                               <a data-toggle="modal" id="add" onclick="holdModal('myModalpa')" class="modalbtnpatient  w-lg-100"><i class="fa fa-plus"></i>  <span><?php echo $this->lang->line('new_patient'); ?></span></a>
                               <?php }?>
                             </div> 
                            </div>                 
                        </div>
                     </div>
                     <div class="col-lg-5 col-md-5 col-sm-11">
                          <div class="row pt-sm-2"> 
                           <div class="col-lg-9 col-md-8 col-sm-8 col-xs-8">
                             <div class="p-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="case_reference_idd" placeholder="<?php echo $this->lang->line('case_id'); ?>" name="case_reference_id">
                                    <div class="input-group-btn">
                                    <button class="btn btn-default btn-group-custom" type="button" id="search_case_reference_id">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    </div>
                                </div>
                            </div> 
                           </div>
                         <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                          <div class="form-check pt5 pb-sm-5">
                           <input class="form-check-input" type="checkbox" value="1" id="is_tpa" name="is_tpa"  onclick="reset_all()">
                            <label class="form-check-label text-white" for="is_tpa">
                            <?php echo $this->lang->line('apply_tpa'); ?>
                            </label>
                           </div> 
                          </div>
                       </div>
                     </div>
                </div><!-- ./row -->
            </div>
            <div class="scroll-area">
                <div class="modal-body pb0">
                </div><!--./modal-body-->
            </div>
                <div class="modal-footer sticky-footer">
                     <div class="pull-right">
                        <input type="hidden" id="organisation_id" name="organisation_id" />
                        <input type="hidden" id="insurance_id" name="insurance_id" />
                        <input type="hidden" id="insurance_validity" name="insurance_validity" />
                    <button type="submit" id="formaddbtn" name="save" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle" ></i> <span><?php echo $this->lang->line('save'); ?></span></button>
                    </div>
                    <div class="pull-right" style="margin-right: 10px;">
                        <button type="submit"  data-loading-text="<?php echo $this->lang->line('processing') ?>" name="save_print" class="btn btn-info pull-right printsavebtn"><i class="fa fa-print"></i> <?php echo $this->lang->line('save_print'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- dd -->
<div class="modal fade" id="myModaledit" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close pt4" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="col-sm-4">
                        <div>
                            <select onchange="get_PatienteditDetails(this.value)"  style="width: 100%" class="form-control select2" id="erecieve_to" name='patient_id' >
                                <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('patient') ?></option>
                                        <?php foreach ($patients as $dkey => $dvalue) {
    ?>
                                    <option value="<?php echo $dvalue["id"]; ?>" <?php
if ((isset($patient_select)) && ($patient_select == $dvalue["id"])) {
        echo "selected";
    }
    ?>><?php echo $dvalue["patient_name"] . " ( " . $dvalue["patient_unique_id"] . ")" ?></option>
<?php }?>
                            </select>
                        </div>
                    </div><!--./col-sm-9-->
                </div><!--./row-->
            </div>
            <form  id="formedit" accept-charset="utf-8"  method="post" class="">
                <div class="scroll-area">
                    <div class="modal-body pt0 pb0">
                        <div class="row ptt10">
                            <input type="hidden" name="id" id="id" value="<?php echo set_value('id'); ?>">
                            <input type="hidden" name="recieve_to" id="patienteditid" value="<?php echo set_value('id'); ?>">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('issue') . " " . $this->lang->line('date'); ?></label>
                                    <small class="req"> *</small>
                                    <input type="text" name="date_of_issue" id="date_of_issue" value="" class="form-control datetime">
                                    <span class="text-danger"><?php echo form_error('date_of_issue'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="exampleInputFile">
                                            <?php echo $this->lang->line('hospital') . " " . $this->lang->line('doctor'); ?></label>
                                    <div>
                                        <select class="form-control select2" onchange="get_docEditname(this.value)" style="width: 100%" name='consultant_doctor' id="edit_consultant_doctor">
                                            <option value="<?php echo set_value('consultant_doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($doctors as $dkey => $dvalue) {
    ?>
                                                <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["name"] . " " . $dvalue["surname"] ?></option>
                                                <?php }?>
                                        </select>
                                    </div>
                                    <span class="text-danger"><?php echo form_error('refference'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('doctor') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                                    <input type="text" name="doctor" id="doctor" value="<?php echo set_value('doctor'); ?>" class="form-control">
                                </div>
                                <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('technician'); ?></label>
                                    <input type="text" name="technician" id="technician" value="<?php echo set_value('recieve_to'); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('donor') . " " . $this->lang->line('name'); ?></label><small class="req"> *</small>
                                    <select  style="width: 100%" class="form-control select2" onchange="getBloodGroup(this.value, 'blood_groupedit')" id="donorname" name='donor_name' >
                                        <option value=""><?php echo $this->lang->line('select') . " " . $this->lang->line('donor') ?>
                                        </option>
                                        <?php foreach ($blooddonar as $dkey => $dvalue) {
    ?>
                                            <option value="<?php echo $dvalue["id"]; ?>" <?php
if ((isset($blooddonar_select)) && ($blooddonar_select == $dvalue["id"])) {
        echo "selected";
    }
    ?>><?php echo $dvalue["donor_name"]; ?></option>
                                            <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('blood_group'); ?></label>
                                    <input type="text" name="blood_group" id="blood_groupedit" readonly="" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('lot'); ?></label>
                                    <input type="text" name="lot" class="form-control" id="lot" value="<?php echo set_value('lot'); ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('bag'); ?></label>
                                    <input type="text" name="bag_no" class="form-control" id="bag_no" value="<?php echo set_value('bag_no'); ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="amount"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label>
                                    <small class="req"> *</small>
                                    <input name="amount" type="text" id="amount" value="<?php echo set_value('amount'); ?>" class="form-control" />
                                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <div class="form-group">
                                    <label for="remark"><?php echo $this->lang->line('remarks'); ?></label>
                                    <textarea name="remark" id="remark" value="<?php echo set_value('remark'); ?>" class="form-control" ></textarea>
                                </div>
                            </div>
                        </div><!--./row-->
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right ">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="formeditbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_delete'>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('component_issue_details'); ?></h4>
            </div>
            <div class="modal-body min-h-3">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModalBill" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deletebill'>
                        <a href="#"  data-target="#edit_prescription"  data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('bill') . " " . $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('.select2').select2();
    })
</script>

<script type="text/javascript">
       var base_url = '<?php echo base_url() ?>';
     $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.filestyle','#addPaymentModal').dropify();
        $('.cheque_div').css("display", "block");
      }else{
        $('.cheque_div').css("display", "none");
      }
    });
	
      $(document).on('click','.add_payment',function(){
            var record_id=$(this).data('recordId');
            var $add_btn= $(this);
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading');
            $('.filestyle','#addPaymentModal').dropify();
            payment_modal.modal({backdrop:'static'});
            getPayments(record_id);
    });
	
      function getPayments(record_id){
         var payment_modal=$('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/bloodbank/getBloodbankTransaction',
            type: "POST",
            data: {'id': record_id,'transaction_type':'blood_component'},
            dataType:"JSON",
            beforeSend: function(){
            },
            success: function (data) {

           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading');
            },
             error: function () {
             payment_modal.removeClass('modal_loading');
            },  complete: function(){
             payment_modal.removeClass('modal_loading');
            }
        });
    }

    $(document).on('submit','#add_partial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var billing_id=$("input[name='billing_id']",'#add_partial_payment').val();

            var form = $(this);
            var btn = clicked_btn;
            btn.button('loading');
            $.ajax({
                url: form.attr('action'),
                type: "POST",
          data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
                dataType: 'JSON',
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                       table.ajax.reload();
                         getPayments(billing_id);
                        }
                     btn.button('reset');
                },
                error: function () {

                },
                complete: function(){
                 btn.button('reset');
   }
            });
        });

    $(document).on('click','.add_payment',function(e){
        $('#add_payment').trigger("reset");
        var record_id=$(this).data('recordId');
        var payment_module=$(this).data('module');
        var caseid =$(this).data('caseid');
        var amount =$(this).data('totalamount');
        $('#amount').val(amount);
        $('#module_id').val(record_id);
        $('#module_name').val(payment_module);
        $('#case_reference_idd').val(caseid);
        $('#myPaymentModal').modal('show');
     });

       $(document).ready(function (e) {

$('#myModal').modal({
    backdrop: 'static',
    keyboard: false,
    show:false
})

        $("#add_payment").on('submit', (function (e) {
            e.preventDefault();

            $.ajax({
                url: base_url+'admin/bill/makepayment',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                 beforeSend: function(){
                  $("#add_paymentbtn").button("loading");
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
                        $('#myPaymentModal').modal('hide');
                    }
                    $("#add_paymentbtn").button("reset");
                },
                 error: function () {
                 $("#add_paymentbtn").button('reset');
                },
                complete: function(){
                 $("#add_paymentbtn").button('reset');
                }
            });
        }));
    });

$('#myModal').on('hidden.bs.modal', function () {
  $('form#formadd #case_reference_idd').val("");
  $('.patient_list_ajax').empty().trigger("change");
  $("#organisation_id").val('');
  $("#insurance_id").val('');
  $("#insurance_validity").val(''); 
  $("input:checkbox[name=is_tpa]").prop('checked',false);
});

      $(document).on('click','.issuecomponent',function(){ 
       var issueModal=$('#myModal');
      var $this = $(this);
       $this.button('loading');
      $.ajax({
          url: base_url+'admin/bloodbank/issuecomponent',
          type: "POST",
          dataType: 'json',
           beforeSend: function() {
              $this.button('loading');
                issueModal.addClass('modal_loading');
          },
          success: function(res) {
          $('.modal-body',issueModal).html(res.page);
            $('.filestyle','#myModal').dropify();
          $("#qty").val(1);
         getcharge_category_module("blood_bank");

              $('.modal-body',issueModal).find('.select2').select2();
                 issueModal.modal('show');
                 issueModal.removeClass('modal_loading');
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.button('reset');
                issueModal.removeClass('modal_loading');
      },
      complete: function() {
            $this.button('reset');
               issueModal.removeClass('modal_loading');
      }
      });
  });
</script>
<script type="text/javascript">

    function get_PatientDetails(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#patientid').val(res.id);
                    $("#organisation_id").val(res.organisation_id);
                    $("#insurance_id").val(res.insurance_id);
                    $("#insurance_validity").val(res.insurance_validity); 
                }
            }
        });
    }

    function get_PatienteditDetails(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#patienteditid').val(res.id);
                    console.log(res.id);
                }
            }
        });
    }

    function get_Docname(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctName',
            type: "POST",
            data: {doctor: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#reference').val(res.name + " " + res.surname + " (" + res.employee_id + ")");
                } else {

                }
            }
        });
    }

    function get_docEditname(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/doctName',
            type: "POST",
            data: {doctor: id},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#doctor').val(res.name + " " + res.surname);
                } else {

                }
            }
        });
    }

    function printData(id) {
         $.ajax({
          url: base_url+'admin/bloodbank/printcomponentIssueBill',
          type: "POST",
          data:{'id':id},
          dataType: 'json',
           beforeSend: function() {
     
          },
          success: function(res) {
           popup(res.page);

          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

         },
              complete: function() {

             }
      });
    }     

    $(document).ready(function (e) {

    $("form#formadd button[type=submit]").click(function() {            
         $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

        $("#formadd").on('submit', (function (e) {
            var str = $("#formadd").serializeArray();
            var postData = new FormData();
             var file_data = $('.filestyle ').prop('files')[0];
            postData.append('document', file_data);
            var case_reference_id=$("input[name=case_reference_id]").val();
            $.each(str, function (i, val) {
                postData.append(val.name, val.value);
            });
            e.preventDefault();
             var sub_btn_clicked = $("button[type=submit][clicked=true]");      
             var sub_btn_clicked_name=sub_btn_clicked.attr('name');
             console.log(sub_btn_clicked_name);

            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/addIssuecomponent',
                type: "POST",
                data: postData,
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
              beforeSend: function() {
                  sub_btn_clicked.button('loading') ; 
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
                       
                          if(sub_btn_clicked_name === "save_print") {                            
                          printData(data.id);
                        }  
                        
                        table.ajax.reload();
                        $('#myModal').modal('hide');
                       
                    }
                    sub_btn_clicked.button('reset') ; 
                },
                error: function () {
                    sub_btn_clicked.button('reset') ; 
                },
                complete: function() {
                   sub_btn_clicked.button('reset') ; 

             }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#formedit").on('submit', (function (e) {
            $("#formeditbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bloodbank/updateIssue',
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
                    $("#formeditbtn").button('reset');
                },
                error: function () {

                }
            });
        }));
    });

    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/getIssueDetails',
            type: "POST",
            data: {bloodissue_id: id},
            dataType: 'json',
            success: function (data) {
                $("#id").val(data.id);
                $("#date_of_issue").val(data.date_of_issue);
                $("#patienteditid").val(data.recieve_to);
                $("#doctor").val(data.doctor);
                $("#technician").val(data.technician);
                $("#amount").val(data.amount);
                $("#lot").val(data.lot);
                $("#bag_no").val(data.bag_no);
                $("#remark").val(data.remark);
                $("#blood_groupedit").val(data.blood_group);
                $("#erecieve_to").select2().select2('val', data.recieve_to);
                $("#donorname").select2().select2('val', data.donor_name);
                $('select[id="edit_consultant_doctor"] option[value="' + data.consultant_doctor + '"]').attr("selected", "selected");
                $("#viewModal").modal('hide');
                $("#viewModalBill").modal('hide');
                holdModal('myModaledit');
            },
        })
    }

    function viewDetailBill(id) {
        $.ajax({
            url: '<?php echo base_url() ?>admin/bloodbank/getBillDetails/' + id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportdata').html(data);
                $('#edit_deletebill').html("<?php if ($this->rbac->hasPrivilege('bloodissue bill', 'can_view')) {?><a href='#' data-toggle='tooltip' onclick='printData(" + id + ")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php }?><?php if ($this->rbac->hasPrivilege('bloodissue bill', 'can_edit')) {?><a href='#'' onclick='getRecord(" + id + ")' data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php }?><?php if ($this->rbac->hasPrivilege('bloodissue bill', 'can_edit')) {?><a onclick='delete_bill(" + id + ")'  href='#'  data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php }?>");
                holdModal('viewModalBill');
            },
        });
    }

$('#viewModal').on('hidden.bs.modal', function () {
  $('.modal-body',$('#viewModal')).html("");
});

$(document).on('click','.viewDetail',function(){

     var $viewModal=$('#viewModal');
     $viewModal.addClass('modal_loading');
     $('#viewModal').modal({backdrop:'static'});
   $.ajax({
            url: base_url+'admin/bloodbank/getComponentIssueDetail',
            type: "POST",
            data: {'blood_issue_id': $(this).data('recordId')},
            dataType: 'json',
                beforeSend: function () {
                 $viewModal.addClass('modal_loading');
                },
                success: function (data) {
                $("#edit_delete",viewModal).html(data.action);
                $('.modal-body',viewModal).html(data.page);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                   $viewModal.removeClass('modal_loading');
                },
                complete: function (data) {
                   $viewModal.removeClass('modal_loading');
                }
        });
});


    $(document).on('click','.edit_component_issue',function(){

      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: base_url+'admin/bloodbank/editIssuecomponent',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
           $this.button('loading');
          },
          success: function(res) {
           var editIssueBloodModal=$('#myModal');
           $('.modal-body',editIssueBloodModal).html(res.page);
               var body_modal=$('.modal-body',editIssueBloodModal);
               body_modal.find('.select2').select2();
               $('#viewModal').modal('hide');
               var patient_id = body_modal.find("input.post_patient_id").val();
               var patient_name = body_modal.find("input.post_patient_name").val();
               var post_bloodgroup = body_modal.find("input.post_blood_group").val();
               var post_blood_donor_cycle_id = body_modal.find("input.post_blood_donor_cycle_id").val();
               var post_component_id = body_modal.find("input.post_component_id").val();
               var post_charge_type_id = body_modal.find("input.post_charge_type_id").val();
               var post_charge_category_id = body_modal.find("input.post_charge_category_id").val();
               var post_charge_id = body_modal.find("input.post_charge_id").val();
                var post_bag_no = body_modal.find("input.post_bag_no").val();
               var option = new Option(patient_name, patient_id, true, true);
                $("#formadd .patient_list_ajax").append(option).trigger('change');
                $("#formadd .patient_list_ajax").trigger({
                    type: 'select2:select',
                    params: {
                        data: res
                    }
                });

               get_components(post_bloodgroup,post_component_id);
               getComponentBagNosIssue(post_bloodgroup,post_blood_donor_cycle_id,post_bag_no);
               getcharge_category(post_charge_type_id,post_charge_category_id);
               getchargecode(post_charge_category_id,post_charge_id);

                if(res.organisation_id==null){
                    $("input:checkbox[name=is_tpa]").prop('checked',false);
               }else{
                    $("input:checkbox[name=is_tpa]").prop('checked',true);
                }   

                $("#qty").val(1);
                $('#myModal').modal('show');
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
        $this.button('reset');

         },
              complete: function() {
    $this.button('reset');
             }
      });
  }); 

    $(document).on('click','.printcomponentIssueBill',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: base_url+'admin/bloodbank/printcomponentIssueBill',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
           $this.button('loading');

          },
          success: function(res) {
     popup(res.page);

          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
        $this.button('reset');

         },
              complete: function() {
    $this.button('reset');

             }
      });
  });

    function deleterecord(id) {
        var url = 'admin/bloodbank/deleteIssue/' + id; 
        delete_recordById(url);
    }

        $(document).on('click','.delete_blood_issue',function(){
             if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            var $this = $(this);
            var recordId=$this.data('recordId');
            $this.button('loading');
            $.ajax({
                url: base_url+'admin/bloodbank/deleteIssue/'+recordId,
                type: "GET",
                data: {},
                dataType: 'json',
                 beforeSend: function() {
                    $this.button('loading');
                },
                success: function(res) {
                    if (res.status == "fail") {
                        errorMsg(res.msg);
                    } else {
                        successMsg(res.msg);
                        $('#viewModal').modal('hide');
                         table.ajax.reload();
                    }

                  $this.button('reset');
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                   $this.button('reset');

            },
            complete: function() {
                  $this.button('reset');
            }
            });
             }
        });

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

    function getBloodGroup(donorid, htmlid) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/getDonorBloodgroup',
            type: "POST",
            data: {donor_id: donorid},
            dataType: 'json',
            success: function (data) {

                $("#" + htmlid).val(data.blood_group);
            }
        });
    }

$(".issueblood").click(function(){
    $('#formadd').trigger("reset");
    $('#select2-addpatient_id-container').html('');
    $('#select2--container').html('');
});

$(".modalbtnpatient").click(function(){
    $('#formaddpa').trigger("reset");
    $(".dropify-clear").trigger("click");
});

    $(document).on('select2:select','.blood_group',function(){
        var bloodgroup=$(this).val();
       get_components(bloodgroup,'');
    });
	
    function get_components(bloodgroup,component){
        var div_data="";
          $.ajax({
            url: base_url+'admin/bloodbank/get_componentBybloodId',
            type: "POST",
            data: {id: bloodgroup},
            dataType: 'json',
            success: function (res) {
                console.log(res);
                $.each(res, function (i, obj)
                {
                    console.log(i);
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.component_issue').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.component_issue').append(div_data);
                $('.component_issue').select2("val", component);
            }
        });
    }
	
$(document).on('select2:select','.component_issue',function(){
        var bloodgroup=$(this).val();
      getComponentBagNosIssue(bloodgroup,"","");
    });
	
  function getComponentBagNosIssue(component_id,bagid,bag_no){
        var blood_group_id=$('#component_blood_group').val();
        var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
        $.ajax({
            url: '<?php echo base_url(); ?>admin/bloodbank/getComponentBagNosIssue',
            type: "POST",
            data:{'component_id':component_id,'blood_group_id':blood_group_id},
            dataType: 'json',
            beforeSend: function() {
            $('.bag_no_issue').html("");
            },
            success: function(res) {
                console.log(res.batch_list);
                if(bagid!=''){
                    div_data += "<option value='" + bagid + "' >" + bag_no  +" </option>";
                }
 
                $.each(res.batch_list, function (i, obj)
                    {
                        var sel = "";
                        let val_unit="";
                        let volume = obj.volume != null ? obj.volume : "" ;
                        let unit = obj.charge_unit != null ? obj.charge_unit : "" ;
                        if(volume !="" || unit !=""  ){
                         val_unit= " (" + volume + " " + unit + ")";
                        }
                        div_data += "<option value='" + obj.id + "' available_unit='" + obj.quantity + "'>" + obj.bag_no  + val_unit+" </option>";
                    });
                    $('.bag_no_issue').html(div_data);
                    $('.bag_no_issue').select2("val", bagid);
            },
        });
    }
	
    function getBloodGroupBagNos(bloodgroup,bagno){
        console.log(bagno);
    var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
    $.ajax({
          url: base_url+'admin/bloodbank/getbatchbybloodgroup',
          type: "POST",
          data:{'bloodgroup':bloodgroup},
          dataType: 'json',
           beforeSend: function() {
          $('.bag_no').html("");
          },
          success: function(res) {
            console.log(res.batch_list);
              $.each(res.batch_list, function (i, obj)
                {
                    var sel = "";
                    let val_unit="";
                    let volume = obj.volume != null ? obj.volume : "" ;
                    let unit = obj.charge_unit != null ? obj.charge_unit : "" ;
             if(volume !="" || unit !=""  ){
                         val_unit= " (" + volume + " " + unit + ")";
                        }

                         div_data += "<option value='" + obj.id + "' available_unit='" + obj.quantity + "'>" + obj.bag_no  + val_unit+" </option>";
                });
                $('.bag_no').html(div_data);
                $('.bag_no').select2("val", bagno);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");


         },
         complete: function() {

       }
      });
    }
</script>

<script type="text/javascript">
    function getcharge_category(charge_type,charge_category) {

           var div_data = "";
           if(charge_type != ""){

        $.ajax({
            url: base_url+'admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: charge_type},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category').append(div_data);
                $('.charge_category').select2("val", charge_category);
            }
        });
         }
    }
	
    function getcharge_category_module(module)
    {
        var div_data = "";
        $.ajax({
            url: base_url+'admin/charges/getchargebymodule',
            type: "POST",
            data: {module:module},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category').append(div_data);
                $('.charge_category').select2("val", charge_category);
            }
        });
    }

    $(document).on('click','.print_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bloodbank/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.button('reset');
         },
              complete: function() {
                   $this.button('reset');
             }
      });
  });

         $(document).on('click','.delete_trans', function(e){
            e.preventDefault();
        var record_id=$(this).data('recordId');
      var billing_id=$("input[name='billing_id']",'#add_partial_payment').val();

            var btn = $(this);
            btn.button('loading');
            $.ajax({
                url: base_url+'admin/transaction/deleteByID',
                type: "POST",
                data: {'id':record_id},
                dataType: 'JSON',
                success: function (data) {
                    successMsg(data.message);
                    getPayments(billing_id);
                    btn.button('reset');
                },
                error: function () {
                    btn.button('reset');
                },
                complete: function(){
                 btn.button('reset');
   }
            });
        });

 $(document).on('select2:select','.charge_category',function(){
       var charge_category=$(this).val();
       $('#tax_percentage').val(0);
        $('#code').val("").trigger("change");
        $("#addstandard_charge").val(0);
        $("#total").val(0);
        $("#discount").val(0);
        $("#tax").val(0);
        $("#net_amount").val(0);
        $("#payment_amount").val(0);
      $('.charge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
     $('.addcharge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
     getchargecode(charge_category,"");
 });
 
    function getchargecode(charge_category,charge_id) {

      var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
      if(charge_category != ""){
          $.ajax({
            url: base_url+'admin/charges/getchargeDetails',
            type: "POST",
            data: {charge_category: charge_category},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";

                });
                $('.addcharge').html(div_data);
                $(".addcharge").select2("val", charge_id);

            }
        });
      }
    }

   $(document).on('select2:select','.addcharge',function(e){
    var charge = $(this).val();       
       let modal_div= $(e.target).closest('div.modal')
       let is_tpa=  modal_div.find("input:checkbox[name=is_tpa]").prop('checked') ? 1 : 0;
        var patient_id= modal_div.find('.patient_list_ajax').val();
      $.ajax({
        url: '<?php echo base_url(); ?>admin/bloodbankstatus/getBloodBankChargeById',
            type: "POST",           
            data: {'charge_id': charge ,'patient_id':patient_id,'is_tpa':is_tpa},
            dataType: 'json',
            success: function (res) {              
                if(res.status == 0){
                    errorMsg(res.msg);
                }else{         
                    if(res.display_tpa_charge){
                        var charge_amt=res.result.org_charge;
                    }else{
                        var charge_amt=res.result.standard_charge;
                    }
                    var quantity=$('#qty').val();
                    quantity=  (quantity == "") ? 0 :quantity;
                    var total_amout=parseFloat(charge_amt) * quantity;
                    $('#total').val(total_amout.toFixed(2));
                    $('#addstandard_charge').val(res.result.standard_charge);
                    var discount_percent= $('#discount_percent').val();
                    $('#tax_percentage').val(res.result.percentage);
                    var discount_amount = parseFloat(total_amout*discount_percent/100);
                    var tax = $('#tax_percentage').val();
                    var tax_amount=  parseFloat((total_amout-discount_amount) * tax / 100);
                    $('#tax').val(tax_amount.toFixed(2));
                    var net_amount = (total_amout-discount_amount)+tax_amount;
                    $('#net_amount,#payment_amount').val(net_amount.toFixed(2));
                    if(res.status == 2){
                        errorMsg(res.msg);
                    }
                }
            }
        });
 });

    $(document).on('change keyup input paste','#discount',function(){
         calculateAmt(false);
    });

        $(document).on('change keyup input paste','#discount_percent',function(){
        calculateAmt(true);
        });

        function calculateAmt(is_percentage){
            console.log("Sdfsdrrrr");
        var tot_amt=parseFloat($('#total').val());
            if(is_percentage){
               var dis_per=$('#discount_percent').val();
               var dis_amt = isNaN((parseFloat(tot_amt*dis_per/100))) ? 0 : (parseFloat(tot_amt*dis_per/100));
               $('#discount').val(dis_amt.toFixed(2));
            }else{
                var dis_amt= parseFloat($('#discount').val());
                var dis_per=isNaN(((dis_amt*100)/tot_amt))?0:((dis_amt*100)/tot_amt);
                $('#discount_percent').val(dis_per.toFixed(2));
            }

        var tax_per= parseFloat($('#tax_percentage').val());
        var tax_amt = parseFloat((tot_amt-dis_amt)*tax_per/100);
        $("#tax").val(tax_amt.toFixed(2));
        var net_amt=isNaN(tax_amt+(tot_amt-dis_amt))?"" :(tax_amt+(tot_amt-dis_amt)).toFixed(2);
        $('#net_amount').val(net_amt);
        $('#payment_amount').val(net_amt);
        }

 $(document).on('change','.bag_no',function(){
 var available_unit = $(this).find('option:selected').attr("available_unit");
 $('#qty').val(available_unit);
 });

 $(document).on('click','#search_case_reference_id',function(){
    var case_reference_id=$("input[name=case_reference_id]").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getpatientBycaseId/'+case_reference_id,
            type: "POST",
            data: {case_reference_id: case_reference_id},
            dataType: 'json',
            success: function (res) {
                if(res.status==1){
                    var option = new Option(res.patient_name, res.patient_id, true, true);
                    $("#formadd .patient_list_ajax").append(option).trigger('change');
                    // manually trigger the `select2:select` event
                    $("#formadd .patient_list_ajax").trigger({
                        type: 'select2:select',
                        params: {
                            data: res
                        }
                    });
               }else{
                errorMsg('<?php echo $this->lang->line("patient_not_found"); ?>');
               }
            }
        });
});

$(document).on('change keyup input paste','#qty',function(){
        var quantity=$(this).val();
        var standard_charge=  $('#addstandard_charge').val();
        var tax_percent=$('#tax_percentage').val();
        var total_charge=(standard_charge == "" )? 0 :standard_charge;
        console.log(total_charge);
        var apply_charge=isNaN(parseFloat(total_charge)*parseFloat(quantity))? 0 : parseFloat(total_charge)*parseFloat(quantity);
         $('#total').val(apply_charge);
        var discount_percent= $('#discount_percent').val();
        var discount_amount= isNaN((apply_charge*discount_percent)/100) ? 0 : (apply_charge*discount_percent)/100;
        var final_amount=apply_charge-discount_amount;
        console.log(final_amount);
        $('#discount').val(discount_amount);
        $('#tax').val(((final_amount*tax_percent)/100));
        $('#net_amount,#payment_amount').val(final_amount+((final_amount*tax_percent)/100));
    });

    $(document).on('change keyup input paste','#addstandard_charge',function(){
        var standard_charge = $("#addstandard_charge").val();
        $("#total").val(standard_charge);
        calculateAmt(false);
    }); 

$('.patient_list_ajax').on('select2:select', function (e) { 
     var data = e.params.data;    
    $.ajax({ 
            url: base_url+'admin/patient/getpatientDetails',
            type: "POST",
            data: {id:data.id},
            dataType: 'json',
            success: function (res) {                
                $('.blood_group ').select2('val',res.blood_bank_product_id);
            }
        });
});
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {

 initDatatable('ajaxlist', 'admin/bloodbank/getcomponentissueDatatable',[],[],100,[
                     { 
                         
                         "sWidth": "60px", "aTargets": [ -2,-3 ] ,'sClass': 'dt-body-right',
                        
                     } , { 
                         
                        "sWidth": "60px", "bSortable": false,"aTargets": [-1 ] ,'sClass': 'dt-body-right',
                     },
                    ]);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal')?>

<script>
     function reset_all(){
    $('.addcharge').val('').trigger('change');
    $('.charge_category').val('').trigger('change');
    $('#tax_percentage').val(0);
    $('#code').val("").trigger("change");
    $("#addstandard_charge").val(0);
    $("#total").val(0);
    $("#discount").val(0);
    $("#tax").val(0);
    $("#net_amount").val(0);
    $("#payment_amount").val(0);
    }
    </script>