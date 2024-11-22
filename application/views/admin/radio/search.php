<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary"> 
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('radiology_test'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('radiology_test', 'can_add')) { ?>   
                                 <button  class="btn btn-primary btn-sm radiology addtest" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('please_wait'); ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_radiology_test'); ?></button>
                            <?php } ?>                           
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('radiology_test'); ?></div>
                    <div class="table-responsive overflow-visible-lg">    
                        <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('radiology_test'); ?>">
                            <thead>
                                <tr class="white-space-nowrap">
                                    <th><?php echo $this->lang->line('test_name'); ?></th>
                                    <th><?php echo $this->lang->line('short_name'); ?></th>
                                    <th><?php echo $this->lang->line('test_type'); ?></th>
                                    <th><?php echo $this->lang->line('category_name'); ?></th>
                                    <th><?php echo $this->lang->line('sub_category'); ?></th>
                                    <th><?php echo $this->lang->line('report_days'); ?></th>
                                    <?php 
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                                <th><?php echo $fields_value->name; ?></th>
                                                <?php
                                            } 
                                        } 
                                    ?> 
                                    <th class="text-right" ><?php echo $this->lang->line('tax'); ?> (%)</th>
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

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='edit_delete'>
                        <a href="#" data-target="#editModal" data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('test_details'); ?></h4> 
            </div>
            <form id="view" accept-charset="utf-8" method="get">    
                <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table mb0 table-striped table-bordered examples">
                               
                            </table>
                            <div class="" id="parameterview">
                            </div>
                        </div>
                </div>
            </form>
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
                    <div class="modal-body pb0 pt0">
                    
                    </div>
                </div>
            <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="formaddbtn" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>                        
                    </div>
                </div>
            </form>
        </div>
    </div>    
</div>

<!-- dd -->
<script type="text/javascript">
			var radiology_parmeter_rows=1;
			function holdModal(modalId) {
                $('#' + modalId).modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            }
     
	$(document).on('click','.addtest',function(){
		var createModal=$('#addTestReportModal');
		var $this = $(this);
		$this.button('loading');
		$.ajax({
			url: '<?php echo base_url(); ?>admin/radio/createRadiologyTest',
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
</script>

<script type="text/javascript">
            $(document).ready(function (e) {
$('#addTestReportModal').modal({
    backdrop: 'static',
    keyboard: false,
    show:false
})
                $(".printsavebtn").on('click', (function (e) {
                    var form = $(this).parents('form').attr('id');
                    var str = $("#" + form).serializeArray();
                    var postData = new FormData();
                    $.each(str, function (i, val) {
                        postData.append(val.name, val.value);
                    });

                    var input = document.querySelector('input[type=file]'),
                      file = input.files[0];
                       postData.append("radiology_report", file);
                    $("#formbatchbtn").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/radio/testReportBatch',
                        type: "POST",
                        data: postData,
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
                                var radioid = $("#radio_id").val();
                                successMsg(data.message);
                                printData(data.id,radioid);
                            }
                            $("#formbatchbtn").button('reset');
                        },
                        error: function () {
                        }
                    });
                }));
            });

            function printData(id,radioid) {                
                var base_url = '<?php echo base_url() ?>';
                $.ajax({
                    url: base_url + 'admin/radio/getBillDetails/' + id +'/'+radioid,
                    type: 'POST',
                    data: {id: id, print: 'yes'},
                    success: function (result) {
                        popup(result);
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
                            $('#radio_patientid').val(res.id);
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

            $(document).ready(function (e) {
                $("#formadd").on('submit', (function (e) {
                    $("#formaddbtn").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/radio/add',
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

            $(document).ready(function (e) {
                $("#formedit").on('submit', (function (e) {
                    $("#formeditbtn").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/radio/update',
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
                    url: '<?php echo base_url(); ?>admin/radio/getDetails',
                    type: "POST",
                    data: {radiology_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#id").val(data.id);
                        $('#customfield').html(data.custom_fields_value);
                        $("#test_name").val(data.test_name);
                        $("#short_name").val(data.short_name);
                        $("#test_type").val(data.test_type);
                        $("#sub_category").val(data.sub_category);
                        $("#report_days").val(data.report_days);
                        $("#edit_charge_category").val(data.charge_category);
                        $("#edit_standard_charge").val(data.standard_charge);
                        editchargecode(data.charge_category, data.charge_id);
                        $("#updateid").val(id);                       
                        $('select[id="radiology_category_id"] option[value="' + data.radiology_category_id + '"]').attr("selected", "selected");
                        $('select[id="charge_category_id"] option[value="' + data.charge_category_id + '"]').attr("selected", "selected");
                        $("#viewModal").modal('hide');
                        $("#radiology_category_id").select2().select2('val', data.radiology_category_id);
                        holdModal('myModaledit');
                    },
                })
                $.ajax({
                url: '<?php echo base_url(); ?>admin/radio/editparameter/' + id,
                    success: function (res) {
                        
                        $("#edit_parameter_details").html(res);
                        holdModal('myModaledit');
                    },
                    error: function () {
                        alert("Fail")
                    }
                });
            }

            $(function () {
                //Initialize Select2 Elements
                $('.select2').select2();
            });
			
            function delete_record(id) {
                if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/radio/delete/',
                        type: "POST",
                        data: {id: id},
                        dataType: 'json',
                        success: function (data) {
                            successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                            table.ajax.reload();
                            $("#viewModal").modal('hide');
                        }
                    })
                }
            }

            $(document).on('select2:select','.charge_category',function(){

		var charge_category=$(this).val();

		$('.charge').html("<option value=''><?php echo $this->lang->line('loading'); ?></option>");
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

            function viewDetail(id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/radio/radiologyDetails',
                    type: "POST",
                    data: {radiology_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $('#viewModal .modal-body').html(data.page);
						$('#viewModal').modal('show');                     
                        $('#edit_delete').html("<?php if ($this->rbac->hasPrivilege('radiology_test', 'can_edit')) { ?><a href='#' class='edittest' data-record-id='"+id+"'  data-loading-text='<i class= \"fa fa-circle-o-notch fa-spin\"></i>' data-toggle='tooltip'   data-original-title='<?php echo $this->lang->line('edit'); ?>'><i class='fa fa-pencil'></i></a><?php } if ($this->rbac->hasPrivilege('radiology_test', 'can_delete')) { ?><a onclick='delete_record(" + id + ")'  href='#'  data-toggle='tooltip'  data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");
                        holdModal('viewModal');
                    },
                });             
            }
        
            $(document).ready(function (e) {
                $("#formbatch").on('submit', (function (e) {
                    $("#formbatchbtn").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/radio/testReportBatch',
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
                            $("#formbatchbtn").button('reset');
                        },
                        error: function () {

                        }
                    });
                }));
            });            

            function showtextbox(value) {
                if (value != 'direct') {
                    $("#opd_ipd_no").show();
                } else {
                    $("#opd_ipd_no").hide();
                }
            }
          
          $(document).on('click','.add-record',function(){
                var table = document.getElementById("tableID");
                var table_len = (table.rows.length);
                radiology_parmeter_rows++;
                var div = "<td width='35%'><input type='hidden' name='total_rows[]' value='"+radiology_parmeter_rows+"'><input type='hidden' name='inserted_id_' value='0'><select class='form-control select2 radiology_parmeter' name='parameter_name_"+ radiology_parmeter_rows +"'><option value='<?php echo set_value('parameter_name'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($parametername as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["parameter_name"] ?></option><?php } ?></select></td><td width='30%'><input type='text' name='reference_range_"+ radiology_parmeter_rows +"' readonly id='reference_range" + radiology_parmeter_rows + "' class='form-control reference_range'></td><td width='30%'><input type='text' name='radio_unit_"+ radiology_parmeter_rows +"' readonly id='radio_unit" + radiology_parmeter_rows + "' class='form-control radio_unit'></td>";                    
                var row = table.insertRow(table_len).outerHTML = "<tr id='row" + radiology_parmeter_rows + "'>" + div + "<td><button type='button' class='closebtn delete_row'><i class='fa fa-remove'></i></button></td></tr>";
                $('.select2').select2();
          });  
           
	$(document).on('click','.delete_row',function(e){
		if(confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this'); ?>")){
			var table_row= $(this).closest('table#tableID tr');
			table_row.remove();
		}
	});

	$(document).on('change','.radiology_parmeter',function(){
		var pathology_parmeter_id=$(this).val();
		getparameterdetails($(this),pathology_parmeter_id);
	});

    function getparameterdetails(pathology_parmeter_obj,parameter_id) {
		var medicine_colomn=pathology_parmeter_obj.closest('tr').find('.reference_range');
		var radio_unit=pathology_parmeter_obj.closest('tr').find('.radio_unit');
        $.ajax({
            type: "POST",
            url: base_url + "admin/radio/getparameterdetails",
            data: {'id': parameter_id },
            dataType: 'json',
            success: function (res) {
                if (res != null) {
                   medicine_colomn.val(res.reference_range);
                   radio_unit.val(res.unit_name);
                }
            }
        });
    }
           
    $(document).on('click','.edittest',function(){
		var createModal=$('#addTestReportModal');
		var $this = $(this);
        var record_id=$this.data('recordId')
		$this.button('loading');
			$.ajax({
			url: '<?php echo base_url(); ?>admin/radio/editRadiologyTest',
			type: "POST",
			data:{'id':record_id},
			dataType: 'json',
			beforeSend: function() {
				$this.button('loading');
                createModal.addClass('modal_loading');
			},
			success: function(res) {
				radiology_parmeter_rows=res.total_rows;
				$('#modaltitle').html("<?php echo $this->lang->line('edit_test_details'); ?>");
				$('#addTestReportModal .modal-body').html(res.page);
				var post_charge_category_id=  $('#addTestReportModal .modal-body').find("input[name='post_charge_category_id']").val();
				var post_charge_id= $('#addTestReportModal .modal-body').find("input[name='post_charge_id']").val();
				$('.select2').select2();
				getchargecode(post_charge_category_id,post_charge_id);
				$('#tableID').find("tbody tr").each(function() {             
					var pathology_parmeter_obj = $(this).find("td select.radiology_parmeter");
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
  
			function getchargeDetails(id, htmlid) {
                $('#' + htmlid).val("");
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/charges/getDetails',
                    type: "POST",
                    data: {charges_id: id, organisation: ''},
                    dataType: 'json',
                    success: function (res)
                    {
                        $('#' + htmlid).val(res.standard_charge);
                    }
                })
            }

    $(document).ready(function (e) {
        $('#viewModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });
</script>

<script type="text/javascript">

function addpatientreport(){	
	$('#formbatch').trigger("reset");
	$("#patientDetails").hide();
	$('#select2-addpatient_id-container').html("");
	$(".dropify-clear").trigger("click");
}

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
        initDatatable('ajaxlist','admin/radio/getradiologytestDatatable',[],[],100,
                        [
                          {  "sWidth": "100px", "bSortable": false, "aTargets": [ -1,-2,-3 ] ,'sClass': 'dt-body-right'},
                        ]);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
<?php $this->load->view('admin/patient/patientaddmodal') ?>