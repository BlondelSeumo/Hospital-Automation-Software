<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row"> 
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('pathology_test'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('pathology_test', 'can_add')) {?>
                                <button  class="btn btn-primary btn-sm pathology addtest" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Please wait.."><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_pathology_test'); ?></button>
                            <?php }?>
                           
                        </div>
                    </div><!-- /.box-header -->
                    
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('pathology_test'); ?></div>
                        <div class="table-responsive overflow-visible-lg">
                                <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('pathology_test'); ?>">
                            <thead>
                                <tr class="white-space-nowrap">
                                    <th><?php echo $this->lang->line('test_name'); ?></th>
                                    <th><?php echo $this->lang->line('short_name'); ?></th>
                                    <th><?php echo $this->lang->line('test_type'); ?></th>
                                    <th><?php echo $this->lang->line('category'); ?></th>
                                    <th><?php echo $this->lang->line('sub_category'); ?></th>
                                    <th><?php echo $this->lang->line('method'); ?></th>
                                    <th><?php echo $this->lang->line('report_days'); ?></th>
                                    <?php
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) { ?>
                                            <th><?php echo $fields_value->name; ?></th>
                                    <?php   } 
                                        } 
                                    ?>                                  
                                    <th class="text-right"><?php echo $this->lang->line('tax'); ?> (%)</th>
                                    <th class="text-right"><?php echo $this->lang->line('charge') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
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

<!-- dd -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_delete'>
                        <?php if ($this->rbac->hasPrivilege('pathology_test', 'can_edit')) {?>
                        <a href="#" data-target="#editModal" data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                    <?php }
                    if ($this->rbac->hasPrivilege('pathology_test', 'can_delete')) {
                        ?>
                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    <?php }?>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('test_details'); ?></h4>
            </div>          
            <div class="modal-body ptt10 pb0">

            </div>           
        </div>
    </div>
</div>

<div class="modal fade" id="addTestReportModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modaltitle"></h4>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post" class="ptt10">
            <div class="scroll-area"> 
                <div class="modal-body pt0 pb0">
                </div>
            </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var pathology_parmeter_rows=1;
 
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
</script>


<script type="text/javascript">
   
    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }    

    function printData(id) {
        $.ajax({
            url: base_url + 'admin/pathology/printReport',
            type: 'POST',
            data: {pathology_report_id: id},
              dataType: 'json',
            success: function (result) {
                popup(result.page);
            }
        });
    }

    $(document).ready(function (e) {
        $("#formbatch").on('submit', (function (e) {
             e.preventDefault();             
            var form = $(this);    
        
            var clicked_btn = form.find("button[type=\"submit\"]:focus");
            var click_btn_id=clicked_btn.attr('id');
            var btn = clicked_btn;

           $.ajax({
               url: '<?php echo base_url(); ?>admin/pathology/testReportBatch',
               type: "POST",
               data: new FormData(this),
               dataType: 'json',
               contentType: false,
               cache: false,
               processData: false,
                 beforeSend: function() {
              btn.button('loading');
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
                        if(click_btn_id == "billsave"){
                        //============================
						$('#consultant_doctor').select2("val", "");
						$('#addpatient_id').select2("val", "");
						$('#patientDetails').css("display", "none");
						$('#patientDetails span').text("");
						$('#description,#report_date,#charge_category_html,#code_html,#charge_html,#apply_charge').val("");
                        //=============================
                        }else if (click_btn_id == "saveprint") {
                     

                          printData(data.id);  
                        }
                        
             }
              btn.button('reset');
         },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                btn.button('reset');
              
              },
              complete: function() {
                      btn.button('reset');
                 
             }
            });
        }));
    });
</script>
<script type="text/javascript">

    $(document).ready(function (e) {
        $("#formaddp").on('submit', (function (e) {
            e.preventDefault();
            $("#formaddbtnp").button('loading');
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/add',
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
                         $('.ajaxlist').DataTable().ajax.reload();

                    }
                    $("#formaddbtnp").button('reset');
                },
                error: function () {
                }
            });
        }));
    });
	
$(document).on('click','.add-record',function(){
   
        var table = document.getElementById("tableID");
        var table_len = (table.rows.length);
        pathology_parmeter_rows++;
        var div = "<td width='35%'><input type='hidden' name='total_rows[]' value='" + pathology_parmeter_rows + "'>  <input type='hidden' name='inserted_id_" + pathology_parmeter_rows + "' value='0'>  <select class='form-control select2 pathology_parmeter' name='parameter_name_"+ pathology_parmeter_rows +"' ><option value='<?php echo set_value('parameter_name'); ?>'><?php echo $this->lang->line('select'); ?></option><?php foreach ($parametername as $dkey => $dvalue) {?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["parameter_name"] ?></option><?php }?></select></td><td width='30%'><input type='text' name='reference_range_"+ pathology_parmeter_rows +"' readonly id='reference_range" + pathology_parmeter_rows + "' class='form-control reference_range'></td><td width='30%'><input type='text' name='patho_unit_"+ pathology_parmeter_rows +"' readonly id='patho_unit" + pathology_parmeter_rows + "' class='form-control patho_unit'></td>";
       
        var row = table.insertRow(table_len).outerHTML = "<tr id='row" + pathology_parmeter_rows + "'>" + div + "<td><button type='button'  class='closebtn delete_row'><i class='fa fa-remove'></i></button></td></tr>";
        $('.select2').select2();
    
});

 $(document).on('click','.delete_row',function(e){
    if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){

       var table_row= $(this).closest('table#tableID tr');
       table_row.remove();
   }
  });

 $(document).on('change','.pathology_parmeter',function(){

     var pathology_parmeter_id=$(this).val();
     getparameterdetails($(this),pathology_parmeter_id);
 });

    function getparameterdetails(pathology_parmeter_obj,parameter_id) {
     var medicine_colomn=pathology_parmeter_obj.closest('tr').find('.reference_range');
     var patho_unit=pathology_parmeter_obj.closest('tr').find('.patho_unit');
        $.ajax({
            type: "POST",
            url: base_url + "admin/pathology/getparameterdetails",
            data: {'id': parameter_id },
            dataType: 'json',
            success: function (res) {
                if (res != null) {
                   medicine_colomn.val(res.reference_range);
                   patho_unit.val(res.unit_name);
                }
            }
        });
    }


    function get_PatientDetails(id) {
       
        var base_url = "<?php echo base_url(); ?>backend/images/loading.gif";

        $("#ajax_load").html("<center><img src='" + base_url + "'/>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/patientDetails',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (res) { 
                if (res) {

                    $("#ajax_load").html("");
                    $("#patientDetails").show();
                    $('#patient_unique_id').html(res.patient_unique_id);
                    $('#patho_patientid').val(res.id);
                    $('#listname').html(res.patient_name);
                    $('#guardian').html(res.guardian_name);
                    $('#listnumber').html(res.mobileno);
                    $('#email').html(res.email);

                    if (res.age == "") {
                        $("#age").html("");
                    } else {
                        if (res.age) {
                            var age = res.age + " " + "Years";
                        } else {
                            var age = '';
                        }
                        if (res.month) {
                            var month = res.month + " " + "Month";
                        } else {
                            var month = '';
                        }
                        if (res.dob) {
                            var dob = "(" + res.dob + ")";
                        } else {
                            var dob = '';
                        }

                        $("#age").html(age + "," + month + " " + dob);
                    }

                    $('#doctname').val(res.name + " " + res.surname);
                    $("#bp").html(res.bp);
                    $("#symptoms").html(res.symptoms);
                    $("#address").html(res.address);
                    $("#note").html(res.note);
                    $("#height").html(res.height);
                    $("#weight").html(res.weight);
                    $("#genders").html(res.gender);
                    $("#marital_status").html(res.marital_status);
                    $("#blood_group").html(res.blood_group);
                    $("#allergies").html(res.known_allergies);
                    $("#image").attr("src", '<?php echo base_url() ?>' + res.image);

                } else {
                    $("#ajax_load").html("");
                    $("#patientDetails").hide();
                }
            }
        });
    }

 $(document).on('select2:select','.charge_category',function(){
       var charge_category=$(this).val();
      $('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
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
                            if (charge_id == obj.id) {
                                sel = "selected";
                            }
                            
                    div_data += "<option value=" + obj.id + " " + sel + ">" + obj.name + "</option>";

                });
                $
                $('.charge').html(div_data);
                $(".charge").select2("val", charge_id);
            }
        });
      }
    } 
	
    $(document).on('select2:select','.charge',function(){
      
        var charge=$(this).val();

      $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge},
            dataType: 'json',
            success: function (res) { 
                if (res) {
                    $('#standard_charge').val(res.result.standard_charge);
                    $('#amount').val((parseFloat((res.result.standard_charge) * res.result.percentage / 100)+(parseFloat(res.result.standard_charge))).toFixed(2));
                    $('#tax').val(res.result.percentage);
                } else {

                }
            }
        });
 });

    function getPatientIdName(opd_ipd_no) {
        $('#patient_id').val("");
        $('#patient_name').val("");
        var opd_ipd_patient_type = $("#customer_type").val();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getPatientType',
            type: "POST",
            data: {opd_ipd_patient_type: opd_ipd_patient_type, opd_ipd_no: opd_ipd_no},
            dataType: 'json',
            success: function (data) {
                $('#patient_id').val(data.patient_id);
                $('#patient_name').val(data.patient_name);
                
            }
        });
    }


    function viewDetail(id,value) {

    $.ajax({
            url: '<?php echo base_url(); ?>admin/pathology/pathologyDetails',
            type: "POST",
            data: {pathology_id: id},
            dataType: 'JSON',
            success: function (data) {
              
                $('#viewModal .modal-body').html(data.page);
                $('#viewModal').modal('show');             
                $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('pathology_test', 'can_edit')) {?><a href='#' class='edittest' data-record-id='"+id+"' data-loading-text='<i class= \"fa fa-circle-o-notch fa-spin\"></i>' data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php }if ($this->rbac->hasPrivilege('pathology_test', 'can_delete')) {
    ?><a onclick='delete_record(" + id + ")'  href='#'  data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php }?>");
               
            },
        });
    }

     $(document).on('click','.edittest',function(){
       var createModal=$('#addTestReportModal');
       $('#viewModal').modal('hide');
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/pathology/editPathologyTest',
          type: "GET",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
              $this.button('loading');
                createModal.addClass('modal_loading');
          }, 
          success: function(res) {
              pathology_parmeter_rows=res.total_rows;
              $('#modaltitle').html("<?php echo $this->lang->line('edit_test_details'); ?>");

              $('#addTestReportModal .modal-body').html(res.page);
            var post_charge_category_id=  $('#addTestReportModal .modal-body').find("input[name='post_charge_category_id']").val();
              var post_charge_id= $('#addTestReportModal .modal-body').find("input[name='post_charge_id']").val();
              $('.select2').select2();
               getchargecode(post_charge_category_id,post_charge_id);
            $('#tableID').find("tbody tr").each(function() {
             
            var pathology_parmeter_obj = $(this).find("td select.pathology_parmeter");
            var post_parameter_value = $(this).find("td input.post_parameter_id").val();
            getparameterdetails(pathology_parmeter_obj,post_parameter_value)

            });
              $('#addTestReportModal').modal('show');
                 createModal.removeClass('modal_loading');
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.button('reset');
                createModal.removeClass('modal_loading');
         },
              complete: function() {
                   $this.button('reset');
                   createModal.removeClass('modal_loading');
             }
      });
  });


    function delete_record(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/delete/',
                type: "POST",
                data: {id: id},
                dataType: 'json',
                success: function (data) {
                    successMsg('<?php echo $this->lang->line('success_message'); ?>');
                    window.location.reload(true);
                }
            })
        }
    }

 $(document).on('click','.addtest',function(){
       var createModal=$('#addTestReportModal');
      var $this = $(this);
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/pathology/createPathologyTest',
          type: "POST",
          dataType: 'json',
           beforeSend: function() {
              $this.button('loading');
                createModal.addClass('modal_loading');
          },
          success: function(res) {

              $('#modaltitle').html("<?php echo $this->lang->line('add_test_details'); ?>");

              $('#addTestReportModal .modal-body').html(res.page);
              $('.select2').select2();
              $('#addTestReportModal').modal('show');
                 createModal.removeClass('modal_loading');
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.button('reset');
                createModal.removeClass('modal_loading');
         },
              complete: function() {
                   $this.button('reset');
                   createModal.removeClass('modal_loading');
             }
      });
  });

    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {
            
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pathology/add',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                 beforeSend: function() {
            $("#formaddbtn").button('loading');
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
                        $('.ajaxlist').DataTable().ajax.reload();
                        $('#addTestReportModal').modal('hide');
                    }
                    $("#formaddbtn").button('reset');
                },
                error: function () {
                    $("#formaddbtn").button('reset');
                },
                complete: function() {
                 $("#formaddbtn").button('reset');
             }
            });
        }));
    });     
</script>

<script type="text/javascript">

    function showtextbox(value) {
        if (value != 'direct') {
            $("#opd_ipd_no").show();
        } else {
            $("#opd_ipd_no").hide();
        }
    }

    $(document).ready(function (e) {
        $('#addTestReportModal,#viewModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });

$(".modalbtnpatient").click(function(){
    $('#formaddpa').trigger("reset");
    $(".dropify-clear").trigger("click");
});
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/pathology/getpathologytestDatatable',[],[],100,
                        [
                          {  "sWidth": "100px", "bSortable": false, "aTargets": [ -1,-2,-3 ] ,'sClass': 'dt-body-right'},
                     
                         
                        ]);
                        
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal')?>