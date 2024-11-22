<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="box box-primary mb10">
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-md-12">
                            <div class="box-body box-profile mt15">
                                <?php
                                    $image = $result['image'];
                                    if (!empty($image)) {
                                        $file = $result['image'];
                                    } else {
                                        $file = "uploads/patient_images/no_image.png";
                                    }
                                ?>
                                <img class="profile-user-img img-responsive img-circle" src="<?php echo base_url() . $file.img_time() ?>" alt="User profile picture">
                                <h3 class="profile-username text-center"><?php echo $result['patient_name']; ?></h3>
                            </div>    
                        </div><!--./col-md-2-->
                        <div class="col-lg-10 col-md-10 col-sm-12">
                            <ul class="billing-item overflow-hidden row-billing">
                                <li class="col-bill-2">
                                    <h5><?php echo $this->lang->line('patient_id'); ?></h5>
                                    <a class="text-aqua"><?php echo $result['id']; ?></a>
                                </li>

                                <li class="col-bill-3">
                                    <h5><?php echo $this->lang->line('marital_status'); ?></h5>
                                    <a class="text-aqua"><?php echo $result['marital_status']; ?></a>
                                </li>

                                <li class="col-bill-4">
                                    <h5><?php echo $this->lang->line('email'); ?></h5>
                                    <a class="text-aqua"><?php echo $result['email']; ?></a>
                                </li>

                                <li class="col-bill-4">
                                    <h5><?php echo $this->lang->line('age'); ?></h5>
                                    <a class="text-aqua"><?php
                                      echo $this->customlib->getPatientAge($result['age'],$result['month'],$result['day'])
                                    ?></a>
                                </li>

                                <li class="border-t col-bill-2">
                                    <h5><?php echo $this->lang->line('gender'); ?></h5>
                                    <a class="text-aqua"><?php echo $this->lang->line(strtolower($result['gender'])); ?></a>
                                </li>
                                

                                <li class="border-t col-bill-3">
                                    <h5><?php echo $this->lang->line('phone'); ?></h5>
                                    <a class="text-aqua"><?php echo $result['mobileno']; ?></a>
                                </li>
                                
                                <li class="border-t col-bill-4">
                                    <h5><?php echo $this->lang->line('address'); ?></h5>
                                    <a class="text-aqua"><?php echo $result['address']; ?></a>
                                </li>
                               
                                 <li class="border-t col-bill-4">
                                    <h5><?php echo $this->lang->line('guardian_name'); ?></h5>
                                    <a class="text-aqua"><?php echo $result['guardian_name']; ?></a>
                                </li>
                            </ul>
                        </div><!--./col-md-10-->
                    </div><!--./row-->
                </div><!--./box-->
            </div><!--./col-lg-12-->
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('my_appointments'); ?></h3>
                        <div class="box-tools pull-right">
                            <a href="#" onclick="getRecord('<?php echo $result['id'] ?>', '<?php echo $result['is_active'] ?>')" class="btn btn-primary btn-sm" data-target="#myModal" data-backdrop="false" data-toggle="modal" >
                                <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_appointment'); ?>
                            </a>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                    <?php
                                     if ($this->session->flashdata('msg')) {
                                     echo $this->session->flashdata('msg');
                                     }?>
                        <div class="download_label"><?php echo $this->lang->line('my_appointments'); ?></div>
                     <div class="table-responsive overflow-visible-lg">   
                        <table class="table table-striped table-bordered table-hover example">
                            <thead>
                                <tr class="">
                                    <th><?php echo $this->lang->line('appointment_no'); ?></th>
                                    <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                    <th><?php echo $this->lang->line("priority"); ?></th>
                                    <th><?php echo $this->lang->line('specialist'); ?></th>
                                    <th><?php echo $this->lang->line('doctor'); ?></th>
                                    <th><?php echo $this->lang->line('status'); ?></th>
                                    <th><?php echo $this->lang->line('message'); ?></th>
                                    <?php 
                                        if (!empty($fields)) {
                                        foreach ($fields as $fields_key => $fields_value) {
                                    ?>
                                      <th><?php echo $fields_value->name; ?></th>
                                    <?php
                                      } } ?> 
                                      <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if (empty($resultlist)) {
                                        ?>
                                                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($resultlist as $appointment) {
                                            if ($appointment["appointment_status"] == "approved") {
                                                $label  = "class='label label-success'";
                                                $app_no = $this->customlib->getPatientSessionPrefixByType("appointment").$appointment["id"];
                                            } else if ($appointment["appointment_status"] == "pending") {
                                                $label  = "class='label label-warning'";
                                                $app_no = $this->lang->line($appointment['appointment_status']);
                                            } else if ($appointment["appointment_status"] == "cancel") {
                                                $label = "class='label label-danger'";
                                                $app_no = $this->lang->line($appointment['appointment_status']);
                                            } ?>
                                    <tr class=""> 
                                        <td><?php echo $app_no; ?></td>
                                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($appointment['date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                                        <td><?php echo $appointment["priorityname"]; ?></td>                          
                                       
                                        <td>
                                        <?php 
                                            foreach($appointment['staff_specialist_name'] as $key => $value){
                                                echo ' '.$value['specialist_name'];
                                            
                                                if(count($appointment['staff_specialist_name']) != $key+1){
                                                    echo ",";
                                                }
                                            }
                                        ?>
                                        </td>
                                        <td><?php echo $appointment['name'] . " " . $appointment['surname']." (".$appointment['employee_id'].")"; ?></td>
                                        <td><small <?php echo $label ?>><?php echo $this->lang->line($appointment['appointment_status']); ?></small></td>
                                        <td><?php echo $appointment['message']; ?></td>
                                         
                                        <?php  if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                $display_field = $appointment["$fields_value->name"];
                                                    if ($fields_value->type == "link") {
                                                        $display_field = "<a href=" . $appointment["$fields_value->name"] . " target='_blank'>" . $appointment["$fields_value->name"] . "</a>";

                                            }?>
                                                <td><?php echo $display_field?></td>

                                        <?php  } } ?>
                                          
                                        <td class="text-right white-space-nowrap">
                                            <?php if ($appointment["appointment_status"] == "pending"){
                                              if($payment_method){  
                                                if($appointment["source"] == "Online"){ ?>
                                                    <a href="<?php echo base_url(); ?>patient/onlineappointment/checkout/index/<?php echo $appointment['id'] ?>" class="btn btn-info btn-xs" data-toggle="tooltip" id="payment_link" title="" data-original-title=""><i class="fa fa-money"></i>  <?php echo $this->lang->line('pay'); ?></a>
                                              <?php  }
                                              } 
                                              ?>
                                            <?php }else{ 
                                                ?>
                                                <a href="javascript:void(0)" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" data-record-id="<?php echo $appointment['id']; ?>"  class="btn btn-default btn-xs print_appointment_bill" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('print_bill');?>"><i class="fa fa-print"></i></a>
                                                <?php
                                            }?>
                                                <a href='#' data-toggle='tooltip' title='<?php echo $this->lang->line('show') ?>' class='btn btn-default btn-xs'  onclick='viewDetail("<?php echo $appointment['id'] ?>")'>  <i class='fa fa-reorder'></i> </a>
                                            <?php if ($appointment["appointment_status"] == "pending"){ ?>
                                                <a href="#" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_recordByIdReload('deleteappointment/<?php echo $appointment['id'] ?>')" >
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
$count++;
    }
}
?>
                            </tbody>
                        </table>
                      </div>  
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_appointment'); ?></h4>
            </div>
            <form id="formadd" accept-charset="utf-8" method="post" class="ptt10">
                <div class="modal-body pt0 pb0">
                <div class="row allotment_error">

</div>
                <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('date'); ?></label>
                                        <small class="req"> *</small>
                                        <input type="text" id="dates" name="date" class="form-control date" value="<?php echo set_value('dates'); ?>">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                        <input type="hidden" name="patient_id" id="patient_ids" class="form-control" value="<?php echo $result['id']; ?>">
                                        <input type="hidden" name="patient_name" id="patient_names" class="form-control" value="<?php echo $result['patient_name']; ?>">
                                        <select class="form-control" id="gender" name="gender" style="display: none;">
                                            <option value="<?php echo set_value('gender'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                            <option value="<?php echo $result['gender']; ?>" ></option>
                                        </select>
                                        <input type="hidden" name="email" id="emails" class="form-control" value="<?php echo $result['email']; ?>">
                                        <input type="hidden" name="mobileno" id="phones" class="form-control" value="<?php echo $result['mobileno']; ?>">
                                        <input type="hidden" name="appointment_status" value="pending" class="form-control" >
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                            <?php echo $this->lang->line('specialist'); ?></label><small class="req"> *</small>
                                        <div>
                                            <select class="form-control" name='specialist' id="specialist"  onchange="getdoctor(this.value)" >
                                                <option value="<?php echo set_value('specialist'); ?>"><?php echo $this->lang->line('select') ?></option>
                                                <?php foreach ($specialist as $skey => $specialist_value) {
                                                ?>
                                                   <option value="<?php echo $specialist_value['id']; ?>"><?php echo $specialist_value['specialist_name']; ?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="exampleInputFile">
                                            <?php echo $this->lang->line('doctor'); ?></label><small class="req"> *</small>
                                        <div>
                                            <select class="form-control" name='doctor' id="doctor" onchange="reset_all(),getDoctorShift()">
                                                <option value="<?php echo set_value('doctor'); ?>"><?php echo $this->lang->line('select') ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line("shift"); ?></label><small class="req"> *</small>
                                        <select name="global_shift" onchange="getShift();" id="global_shift" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($global_shift as $global_key => $global_value) {?>
                                            <option value="<?php echo $global_value['id']; ?>"><?php echo $global_value['name']; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line("slot"); ?></label><small class="req"> *</small>
                                        <select name="shift" onchange="getSlotByShift();validateTime(this)" id="shift_id" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                    <label for="exampleInputFile"><?php echo $this->lang->line('appointment_priority'); ?></label>
                                    <div>
                                      <select class="form-control select2 appointment_priority_select2"  name='priority' style="width:100%" >
                                        <?php foreach ($appoint_priority_list as $dkey => $dvalue) { ?>
                                        <option value="<?php echo $dvalue["id"]; ?>"> <?php echo $dvalue["appoint_priority"]; ?></option>
                                        <?php }?>
                                      </select>
                                    </div>
                                    <span class="text-danger"><?php echo form_error('doctor'); ?></span>
                                  </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label for="message"><?php echo $this->lang->line('message'); ?></label>
                                        <small class="req"> *</small>
                                        <textarea name="message" id="message" class="form-control" ><?php echo set_value('message'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('message'); ?></span>
                                    </div>
                                </div>
                                <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                                <div class="col-sm-5 col-md-4 col-lg-4">
                                    <div class="form-group">
                                     <label for="exampleInputFile"><?php echo $this->lang->line('live_consultation_on_video_conference'); ?></label>
                                            <small class="req">*</small>
                                             <div>
                                                 <select name="live_consult" id="live_consult" class="form-control">
                                                    <?php foreach ($yesno_condition as $yesno_key => $yesno_value) {
                                                        ?>
                                                        <option value="<?php echo $yesno_key ?>" <?php
                                                                if ($yesno_key == 'no') {
                                                                    echo "selected";
                                                                }
                                                                ?> ><?php echo $yesno_value ?>
                                                        </option>
                                                        <?php } ?>
                                                </select>
                                        </div><span class="text-danger"><?php echo form_error('live_consult'); ?></span>
                                    </div>
                                </div>
                                <?php } ?>
                                <div>
                                  <?php echo display_custom_fields_patient('appointment'); ?>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <span id="slots_label"></span>
                                    </div>
                                </div>
                                <input type="hidden" id="slot_id" name="slot" />
                                    <div class="col-md-12">
                                    <div id="slot"></div>
                                    </div>
                                </div> 
                </div>
                <div class="modal-footer">
                            <button type="submit" id="formaddbtn" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        </div>
            </form>
        </div> 
    </div>       
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-media-content mx-2">
      <div class="modal-header modal-media-header">
        <button type="button" class="close" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
        <div class="modalicon">
          <div id="edit_delete">
           </div>
        </div>
        <h4 class="modal-title"><?php echo $this->lang->line('appointment_details'); ?></h4>
      </div>
      <div class="modal-body pt0 pb0">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <form id="view" accept-charset="utf-8" method="get" class="pt5 pb5">
              <div class="table-responsive">
                <table class="table mb0 table-striped table-bordered examples">
                 <tr>
                  <th width="15%"><?php echo $this->lang->line('patient_name'); ?></th>
                  <td width="35%"><span id='patient_name_view'></span></td>
                  <th width="15%"><?php echo $this->lang->line('appointment_no'); ?></th>
                    <td width="35%"><span id="appointmentno"></span></td>
                  
                  </td>
                </tr>
                <tr>
                  <th width="15%"><?php echo $this->lang->line('age'); ?></th>
                  <td width="35%"><span id='patient_age'></span></td>
                   <th width="15%"><?php echo $this->lang->line('appointment_sno')  ; ?></th>
                    <td width="35%"><span id="appointmentsno"></span></td>                  
                  
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('gender'); ?></th>
                    <td width="35%"><span id="genders"></span>
                     <th width="15%"><?php echo $this->lang->line('appointment_date'); ?></th>
                  <td width="35%"><span id='dating'></span></td>
                  
                  </td>
                </tr>
                <tr>
                     <th width="15%"><?php echo $this->lang->line('blood_group'); ?></th>
                  <td width="35%"><span id='blood_group'></span></td>
                  <th width="15%"><?php echo $this->lang->line('appointment_priority'); ?></th>
                  <td width="35%"><span id='appointpriority'></span></td>
                    
                </tr>
                <tr>  <th width="15%"><?php echo $this->lang->line('phone'); ?></th>
                  <td width="35%"><span id="phones_view"></span>
                    <th width="15%"><?php echo $this->lang->line('shift'); ?></th>
                  <td width="35%"><span id="global_shift_view"></span></td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('email'); ?></th>
                  <td width="35%"><span id='emails_view'></span></td>
                  <th width="15%"><?php echo $this->lang->line('slot'); ?></th>
                  <td width="35%"><span id='doctor_shift_view' style="text-transform: capitalize;"></span></td>
                </tr>
                 <tr>
                  <th width="15%"><?php echo $this->lang->line('doctor'); ?></th>
                  <td width="35%"><span id='doctors'></span></td>
                  <th width="15%"><?php echo $this->lang->line('amount'); ?></th>
                  <td width="35%"><span id='pay_amount'></span></td>
                   
                </tr>
                <tr>
                  <th width="15%"><?php echo $this->lang->line('department'); ?></th>
                  <td width="35%"><span id="department_name"></span></td>
                  
                   <th width="15%"><?php echo $this->lang->line('payment_mode'); ?></th>
                  <td width="35%"><span id="payment_mode"></span>
                </tr>
                <tr>
                   <th width="15%"><?php echo $this->lang->line('message'); ?></th>
                  <td width="35%"><span id="messages"></span></td>
                   <th width="15%"><?php echo $this->lang->line('status'); ?></th>
                  <td width="35%"><span id='status' style="text-transform: capitalize;"></span></td>
                  
                </tr>
                <?php if ($this->module_lib->hasActive('live_consultation')) { ?>
                <tr>
                  <th width="15%"><?php echo $this->lang->line('live_consultation'); ?></th>
                  <td width="35%"><span id="liveconsult"></span>
                  </td>
                 
                </tr>
                <?php } ?>
                
                <tr>
                  
                 
                  </td>
                </tr>
                 <tr  id="payrow" style="display:none">
                  <th width="15%"><?php echo $this->lang->line('cheque_no'); ?></th>
                  <td width="35%"><span id='spn_chequeno'></span></td>
                  <th width="15%"><?php echo $this->lang->line('cheque_date'); ?></th>
                  <td width="35%"><span id="spn_chequedate"></span>
                  </td>
                </tr>
                <tr id="paydocrow" style="display:none">
                   <th width="15%"><?php echo $this->lang->line('document'); ?></th>
                  <td width="35%" id='spn_doc'><span ></span></td>
                </tr>
                <tr>
                   <th width="15%"><?php echo $this->lang->line('payment_note'); ?></th>
                  <td width="35%"><span id="payment_note"></span></td>  
                  <th width="15%"><?php echo $this->lang->line('transaction_id'); ?></th>
                  <td width="35%"><span id="trans_id"></span></td>                  
                </tr>                
                <tr>
                   <th><td></td></th>
                   <th width="15%"><?php echo $this->lang->line('source'); ?></th>
                  <td width="35%"><span id="source"></span></td>

                </tr> 
                </table>
                  <table class="table mb0 table-striped table-bordered examples" id="field_data">
                  </table>                
              </div>
            </form>
          </div><!--./col-md-12-->
        </div><!--./row-->
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    function getRecord(id, active) {
        $("#formadd").button('reset');
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getDetails',
            type: "POST",
            data: {patient_id: id, active: active},
            dataType: 'json',
            success: function (data) {
                $("#patient_ids").val(id);
                $("#patient_names").val(data.patient_name);
                $("#emails").val(data.email);
                $("#phones").val(data.mobileno);
                $('select[id="gender"] option[value="' + data.gender + '"]').attr("selected", "selected");
            },
        })
    }
    
    $(document).ready(function (e) {
        $("#formadd").on('submit', (function (e) {
            
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>patient/dashboard/addappointment',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    $('.allotment_error').empty();
                $("#formaddbtn").button('loading');
            },
                success: function (data) {
                    if (data.status == 0 ) {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    }else if (data.status == 2) {
                    $('.allotment_error').append(
                        $('<div/>')    
                            .addClass("alert alert-info")
                            .text(data.msg)
                        );                    
                }else if (data.status == 1)  {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formaddbtn").button('reset');
                },
            error: function (xhr) { // if error occured
                $("#formaddbtn").button('reset');
            },
            complete: function () {
                $("#formaddbtn").button('reset');
            }
            });
        }));
    });

    function delete_recordByIdReload(url, Msg) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: url,
                success: function (res) {
                    successMsg(Msg);
                    window.location.reload(true);
                }
            })
        }
    }

    function getdoctor(id, doc = '') {
       
        var div_data = "";
        $('#doctor').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");

        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getdoctor',
            type: "POST",
            data: {id: id, active: 'yes'},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    if ((doc != '') && (doc == obj.id)) {
                        sel = "selected";
                    }
                    div_data += "<option value=" + obj.id + " " + sel + ">" + obj.name +" "+ obj.surname +" ("+ obj.employee_id +")</option>";
                });
                $("#doctor").html("<option value=''>Select</option>");
                $('#doctor').append(div_data);
            }
        });
    }

    function reset_all(){
        $("#slot").html("");
    }

    function getDoctorShift(prev_val = 0){
        var doctor_id = $("#doctor").val();
        var select_box = "<option value=''><?php echo $this->lang->line('select'); ?></option> ";
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url("site/doctorshiftbyid"); ?>",
            data: {doctor_id:doctor_id},
            dataType: 'json',
            success: function(res){
                $.each(res, function(i, list){
                    selected = list.id == prev_val ? "selected" : "";
                    select_box += "<option value='"+ list.id +"' "+selected+">"+ list.name +"</option>";
                });
                $("#global_shift").html(select_box);
			}
        });
    }
</script>
<script>
    function getShift(){
        let date = $("#dates").val();
        var div_data = "";
        var doctor = $("#doctor").val();        
        var global_shift = $("#global_shift").val();
        var select_box = "<option value=''><?php echo $this->lang->line('select'); ?></option> ";
        $.ajax({
            url: '<?php echo base_url(); ?>site/getShift',
            type: "POST",
            data: {doctor: doctor, date: date, global_shift:global_shift},
            dataType: 'json',
            success: function(res){
                if(res.length){
                    $.each(res, function(i, list){
                        select_box += "<option value='"+ list.id +"' "+selected+">"+ list.start_time +" - "+ list.end_time +"</option>";
                    });
                }else{
                    $("#slot").html("");
                }
                $("#shift_id").html(select_box);
            }
        });
    }

    function getSlotByShift(){
        shift = $("#shift_id").val();
        var div_data = "";
        var date = $("#dates").val();
        var doctor = $("#doctor").val();
        var global_shift = $("#global_shift").val();
        if(shift!=''){
            $.ajax({
                url: '<?php echo base_url(); ?>site/getSlotByShift',
                type: "POST",
                data: {shift:shift,doctor:doctor,date:date,global_shift:global_shift,shift:shift},
                dataType: 'json',
                success: function(res){
                    $.each(res.result, function (i, obj)
                    { 
                        div_data += "<span id='slot_"+ i +"'' onclick = 'setSlot("+ i +")' style='cursor:pointer;' class=' "+ obj.class +"' data-filled='"+ obj.filled +"' >"+ obj.time + "</span>";
                    });
                    
                    $("#slot").html("");
                    $("#slots_label").html("<label><b><?php echo $this->lang->line('available_slots'); ?></b><small class='req'> *</small></label>");
                    if(div_data == ""){
                        div_data = '<div class="alert alert-danger" role="alert"><?php echo $this->lang->line('no_slot_available'); ?></div>';
                    }
                    $('#slot').html(div_data);
                }
            });
        }else{
            $('#slot').html("");
        }
    }

    function setSlot(id){
        if($("#slot_"+id).data("filled") === "filled"){
            alert("<?php echo $this->lang->line('not_available'); ?>");
        }else{
            $("#slot_id").val(id);
            $(".bg-primary").addClass("badge-success-soft");
            $(".bg-primary").removeClass(".bg-primary");
            $("#slot_"+id).removeClass("badge-success-soft");
            $("#slot_"+id).addClass("bg-primary");
        }
    }
</script>

<script>
    function viewDetail(id) {
		$('#viewModal').modal({
			backdrop:"static",
        });
		$("#appointmentno").html(" ");
		$("#appointmentsno").html(" ");
		$.ajax({
			url: '<?php echo base_url(); ?>patient/dashboard/getDetailsAppointment',
			type: "POST",
			data: {appointment_id: id},
			dataType: 'json',
			success: function (data) {
				console.log(data);
				var table_html = '';
				$.each(data.field_data, function (i, obj)
				{
					if (obj.field_value == null) {
						var field_value = "";
					} else {
						var field_value = obj.field_value;
					}
					var name = obj.name ;
					if(obj.visible_on_patient_panel==1){
						table_html += "<tr><th width='15%'><span id='vcustom_name'>" + name + "</span></th> <td width='85%'><span id='vcustom_value'>" + field_value + "</span></td></tr><th></th><td></td>";
					}          
				}); 
	  
				$("#field_data").html(table_html);
				$("#dating").html(data.date);  
				$("#appointmentno").html(data.appointment_no);
				$("#appointmentsno").html(data.appointment_serial_no);
				$("#patient_name_view").html(data.patients_name);
				$("#genders").html(data.patients_gender);
				$("#emails_view").html(data.patient_email);      
				$("#appointpriority").html(data.appoint_priority);
				$("#phones_view").html(data.patient_mobileno);
				$("#doctors").html(data.name + " " + data.surname+" ("+data.employee_id+")");
				$("#messages").html(data.message);
				$("#liveconsult").html(data.edit_live_consult);
				$("#global_shift_view").html(data.global_shift_name);
				$("#doctor_shift_view").html(data.doctor_shift_name);
				$("#source").html(data.source);
				$("#priority").html(data.appoint_priority);
				$("#trans_id").html(data.transaction_id);
				$("#payment_note").html(data.payment_note);
				$("#patient_age").html(data.patient_age);
				$("#department_name").html(data.department_name);
				$("#blood_group").html(data.blood_group);
      
				if(data.paid_amount != null){
					$("#pay_amount").html('<?php echo $currency_symbol; ?>'+data.paid_amount);
				} else {
					$("#pay_amount").html('<?php echo $currency_symbol; ?>'+'0.00');
				}
    
				$("#payment_mode").html(data.payment_mode);

				if(data.payment_mode=="Cheque"){
					$("#payrow").show();
					$("#paydocrow").show();
					$("#spn_chequeno").html(data.cheque_no);
					$("#spn_chequedate").html(data.cheque_date);
					$("#spn_doc").html(data.doc);
				}else{
					$("#payrow").hide();
					$("#paydocrow").hide();
					$("#spn_chequeno").html("");
					$("#spn_chequedate").html("");
				}

				$("#edit_delete").html("<a href='#' data-toggle='tooltip'  onclick='printAppointment(" + id +")' data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> ");

				var label = "";
				if (data.appointment_status == "Approved") {
					var label = "class='label label-success'";
				} else if (data.appointment_status == "Pending") {
					var label = "class='label label-warning'";
				} else if (data.appointment_status == "Cancel") {
					var label = "class='label label-danger'";
				}
	
				$("#status").html("<small " + label + " >" + data.appointment_status + "</small>");
		
			},
		});
    }

    function validateTime(obj){
		let id = obj.value;
		let date = $("#dates").val();
		if(id){
			$.ajax({
				url: baseurl+'welcome/getshiftbyid',
				type: "POST",
				data: {id:id, date:date},
				dataType: 'json',
				success: function(res){
					if(res.status){
						alert("<?php echo $this->lang->line("appointment_time_is_expired"); ?>");
					}
				}
			});
		}
	}
</script>
<script type="text/javascript">
    $(document).on('click','.print_appointment_bill',function(){
		var id=$(this).data('recordId');      
        var $this = $(this);
       
        $.ajax({
            url: base_url+'patient/dashboard/printAppointmentBill',
            type: "POST",
            data: {'appointment_id': id},
            dataType: 'json',
            beforeSend: function() {
				$this.button('loading');               
            },
            success: function (data) {      
				popup(data.page);
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

    function printAppointment(id)
	{
		$.ajax({
            url: base_url+'patient/dashboard/printAppointmentBill',
            type: "POST",
            data: {'appointment_id': id},
            dataType: 'json',
            beforeSend: function() {
                           
            },
            success: function (data) {      
				popup(data.page);
            },

            error: function(xhr) { // if error occured
				alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");            
               
			},
			complete: function() {           
     
			}
        });
	}
     

</script>