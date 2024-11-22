<link rel="stylesheet" type="text/css" href="<?php echo base_url('backend/toast-alert/toastr.css'); ?>">
<style>
.badge-danger-soft {
    background-color: rgba(220,53,69,.1);
    color: #dc3545;
}
.badge {
    font-size: 13px;
    padding: 5px 20px;
    margin: 5px;
    line-height: 1.6;
}
.badge-success-soft {
    background-color: rgba(0, 128, 0,.1);
    color: #006622;
}

.user-slot-container .slot-details {
    padding: 10px 10px;
    border: 1px solid #f6f6f6;
    margin: 5px;
    cursor: pointer;
    vertical-align: middle;
    background: #fff;
    box-shadow: 0 2px 15px -10px rgba(102, 69, 142, 1);
}
.doctor-box img {
    width: 100px;
    position: relative;
    z-index: 1;
    background: #fff;
}
.display-inline{display: inline-block !important;}
.theme-modal-header{background-color: #39f;padding: 10px 15px; color: #fff;border-radius: 5px 5px 0px 0px;}
.close-white{    
    opacity: 100;
    text-shadow: none;
    padding-top: 5px !important;
    color: #fff;
}
.bg__lightgray{background: #f1f1f1;}
.req{
    color: #fc2d42;
}
</style>

<h2 class="text-center"><?php echo $this->lang->line('make_appointment') ?></h2>
<form class="form appointment" id="appointment_form" method="POST" autocomplete="off">
    <div class="row">
        <?php
if (($this->session->flashdata('msg'))) {
    $message = $this->session->flashdata('msg');
    ?>
                    <div class="<?php echo $message['class'] ?>"><?php echo $message['message']; ?></div>
                    <?php
}
?>
        <div class="col-md-6">
            <div class="row">
            <div class="col-md-6">
                <div class="form-group <?php if (form_error('specialist')) {echo 'has-error';}?>">
                    <label for="pwd"><?php echo $this->lang->line('specialist'); ?></label>
                    <select name="specialist" onchange="getdoctor(this.value)" class="form-control select2">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($specialist as $specialist_key => $specialist_value) {?>
                            <option value="<?php echo $specialist_value['id']; ?>"><?php echo $specialist_value['specialist_name']; ?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group <?php if (form_error('doctor')) {echo 'has-error';}?>">
                    <label for="pwd"><?php echo $this->lang->line('doctor'); ?></label>
                    <select name="doctor" onchange="getDoctorShift()" id="doctor" class="form-control select2">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group <?php if (form_error('global_shift')) {echo 'has-error';}?>">
                    <label for="pwd"><?php echo $this->lang->line("shift"); ?></label>
                    <select name="global_shift" onchange="getShift();" id="global_shift" class="form-control select2">
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($global_shift as $global_key => $global_value) {?>
                        <option value="<?php echo $global_value['id']; ?>"><?php echo $global_value['name']; ?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
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

            <div class="col-md-12">
                <div class="form-group formgroup <?php if (form_error('date')) {echo 'has-error';}?>">
                    <label for="pwd"><?php echo $this->lang->line("date"); ?></label>
                     
            <div class='input-group date' >
                <input type='text' class="form-control" id='datetimepicker1' name="date" autocomplete="off" />
                <span class="input-group-addon">
                <span class="fa fa-calendar"></span>
                </span>
            </div>

                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="message"><?php echo $this->lang->line('message'); ?></label>
                    <small class="req"> *</small>
                    <textarea name="message" id="message" class="form-control" ><?php echo set_value('message'); ?></textarea>
                </div>
            </div>
            
            <div>
              <?php echo display_custom_fields_patient('appointment'); ?>
            </div>
          </div>   
        </div>
        <input type="hidden" id="shift_id" name="shift" />
        <div class="col-md-6 pt25" id="shift">
            <div class="alert alert-danger" role="alert">
                <?php echo $this->lang->line('no_slot_available'); ?>
            </div>
        </div>        
    </div>
</form>

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
       <div class="modal-header theme-modal-header">
        <button type="button" class="close close-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title display-inline" id="exampleModalLabel"><?php echo $this->lang->line("slots_available"); ?></h4>
      </div>
      <div class="modal-body pt0 pb0">
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="doctor-box ptt10">
                        <img class="col-md-4" id="staff_image" src="<?php echo base_url("uploads/staff_images/no_image.png"); ?>">
                        <div class="col-md-8">
                            <div class="col-md-6"><?php echo $this->lang->line("doctor_name"); ?> </div>
                            <div id="doctor_name" class="col-md-6"></div>
                            <div class="col-md-6"><?php echo $this->lang->line("specialist"); ?> </div>
                            <div id="doctor_speciality" class="col-md-6"></div>
                            <div class="col-md-6"><?php echo $this->lang->line("consultation_fees"); ?> </div>
                            <div id="fees" class="col-md-6"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr>
                    </div>
                    <input type="hidden" id="slot_id" name="slot" form="appointment_form" />
                    <div class="col-md-12" id="slot"></div>
                </div>    
            </div>
            <div class="col-md-5 bg__lightgray">
                <?php if(empty($this->session->userdata("patient"))){ ?>
                <div class="col-md-12">
                    <h3 class="text-center pb10"><?php echo $this->lang->line("login_register"); ?></h3>
                    <div class="row allotment_error">

                    </div>
                    <div class="col-md-12">
                    <div class="form-group">
                        <label style="display: block;"><?php echo $this->lang->line('patient_appointment'); ?></label>

                        <label class="radio-inline">
                            <input form="appointment_form" type="radio" name="patient_type" value="new patient" <?php echo set_value('patient_type', 'new patient') == "new patient" ? "checked" : ""; ?>><?php echo $this->lang->line('new_patient'); ?>
                        </label>
                        <label class="radio-inline">
                            <input form="appointment_form" type="radio" name="patient_type" value="old patient" <?php echo set_value('patient_type') == "old patient" ? "checked" : ""; ?>><?php echo $this->lang->line('old_patient'); ?>
                        </label>
                     </div>
                    </div>
                    <div id="login_form" class="hide">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pwd"><?php echo $this->lang->line("username"); ?></label><small class="req"> *</small>
                                <input form="appointment_form" type="text" name="username" placeholder="<?php echo $this->lang->line("username"); ?>" class="form-username form-control" value="<?php echo set_value('useraname'); ?>" id="username"> <span class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="pwd"><?php echo $this->lang->line("password"); ?></label><small class="req"> *</small>
                            <div class="form-group">
                                <input form="appointment_form" type="password" name="password" placeholder="<?php echo $this->lang->line("password"); ?>" class="form-password form-control" id="password"> <span class="text-danger"></span>
                            </div>
                        </div>
                        <?php if($is_captcha){ ?>
                            <div class="">
                                <div class='col-md-6'>
                                    <div class="form-group"> 
                                        <span class="captcha_image" ><?php echo $captcha_image; ?></span>
                                        <span class="fa fa-refresh" title='Refresh Catpcha' onclick="refreshCaptcha()" style="cursor:pointer;"></span>
                                    </div>
                                </div>
                                
                                <div class='col-md-6'>
                                    <div class="form-group"> 
                                        <input form="appointment_form" type="text" name="captcha_login" placeholder="<?php echo $this->lang->line('enter_captcha'); ?>" class=" form-control " id="captcha_register"> 
                                        <span class="text-danger"><?php echo form_error('captcha'); ?></span>
                                    </div>
                                </div>
                            </div>    
                        <?php } ?>
                    </div>
                    <div id="signup_form">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label for="pwd"><?php echo $this->lang->line('patient_name'); ?></label><small class="req"> *</small>
                                <input form="appointment_form" type="text" name="patient_name" class="form-control"  id="patient_name" value="<?php echo set_value('patient_name') ?>" placeholder="<?php echo $this->lang->line('enter_patient_name'); ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pwd"><?php echo $this->lang->line('email') . " " ?></label><small class="req"> *</small>
                                <input form="appointment_form" type="email" name="email" class="form-control" id="email" value="<?php echo set_value('email') ?>" placeholder="<?php echo $this->lang->line('enter_email'); ?>">

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pwd"><?php echo $this->lang->line('gender'); ?></label><small class="req"> *</small>
                                <select form="appointment_form" name="gender" class="form-control" id="gender">
                                    <?php foreach ($gender as $gender_key => $gender_value) {?>
                                        <option value="<?php echo $gender_key; ?>"><?php echo $gender_value; ?></option>
                                        <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pwd"><?php echo $this->lang->line('phone'); ?></label><small class="req"> *</small>
                                <input form="appointment_form" type="text" name="phone" class="form-control" id="phone" value="<?php echo set_value('phone') ?>" placeholder="<?php echo $this->lang->line('enter_phone'); ?>">
                            </div>
                        </div>
                         <?php if($is_captcha){ ?>
                            <div class=""> 
                                <div class='col-md-6'>
                                    <div class="form-group">
                                        <span class="captcha_image" ><?php echo $captcha_image; ?></span>
                                        <span class="fa fa-refresh" title='Refresh Catpcha' onclick="refreshCaptcha()" style="cursor:pointer;"></span>
                                    </div>
                                </div>    

                                <div class='col-md-6'>
                                    <div class="form-group">
                                        <input form="appointment_form" type="text" name="captcha_register" placeholder="<?php echo $this->lang->line('enter_captcha'); ?>" class=" form-control " id="captcha"> 
                                        <span class="text-danger"><?php echo form_error('captcha'); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button form="appointment_form" type="submit" id="submitbtn" class="btn btn-primary theme-btn"><?php echo $this->lang->line('submit'); ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
      </div>
  </div>
  </div>
</div>

<link rel="stylesheet" href="<?php echo base_url() ?>backend/plugins/select2/select2.min.css">
<script src="<?php echo base_url() ?>backend/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo base_url('backend/toast-alert/toastr.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>backend/plugins/datepicker/bootstrap-datepicker.js"></script>
<script>
     var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormatFrontCMS(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2();
    });

    $(document).ready(function () {
       toastr.options = {
      "closeButton": true,
      
    };
    
    $(function () {   
        $('#exampleModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });      
    });
    
        $(function () { 
            var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormatFrontCMS(true), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';

             $('#datetimepicker1').datetimepicker({
                format: datetime_format,
            });
         });


        $(function () {
            $('input[type=radio][name=patient_type]').change(function () {
                updatePatientID(this.value);
            });

        $("#datetimepicker1").on("dp.change", function (e) {
                getShift();
        });

            
        });
    });

     function updatePatientID(patient_type) {
        if (patient_type == 'old patient') {
            $('#login_form').removeClass("hide");
            $('#signup_form').addClass("hide");
        } else if (patient_type == 'new patient') {
            $('#signup_form').removeClass("hide");
            $('#login_form').addClass("hide");
        }
    }

    function getdoctor(id, doc = '') {
        if(id!=''){
            var div_data = "";
            $('#doctor').html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
            $.ajax({
                url: '<?php echo base_url(); ?>site/getdoctor',
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
                        div_data += "<option value=" + obj.id + " " + sel + ">" + obj.name + "</option>";
                    });
                    $("#doctor").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                    $('#doctor').append(div_data);
                    $("#doctor").select2().select2('val', doc);
                }
            });
        }else{
            $("#doctor").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
        }
        $("#slot").html("");
    }

</script>

<script>
     function getSlotByShift(shift){
        $("#shift_id").val(shift);
        
        $("#slot_id").val("");
        var div_data = "";
        var date = $("#datetimepicker1").val();
        var doctor = $("#doctor").val();
        var global_shift = $("#global_shift").val();
        var message = $("#message").val();
        
        $.ajax({
                url: '<?php echo base_url(); ?>welcome/chackvalidation',
                type: "POST",
                data: $('#appointment_form').serialize(),
                dataType: 'json',
                success: function (data) {
                    
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
        
                        $("#exampleModal").modal("show");
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
                                    if(div_data == ""){
                                        div_data = '<div class="alert alert-danger" role="alert"><?php echo $this->lang->line('no_slot_available'); ?></div>';
                                    }
                                    $("#slot").html("");
                                    $('#slot').html(div_data);
                                    $("#doctor_name").html(res.doctor_name);
                                    let speciality = "";
                                    $.each(res.doctor_speciality, function(i, list){
                                        if(speciality!=""){
                                            speciality +=", ";
                                        }
                                        speciality += list.specialist_name;
                                    });
                                    $("#doctor_speciality").html(speciality);
                                    $("#fees").html(res.fees);
                                    $("#duration").html(res.duration);
                                    $("#imgdiv").attr("src", res.image);
                                    refreshCaptcha();
                                    if(res.image != ''){
                                        $("#staff_image").attr('src',res.image);;
                                    }
                                    
                                    validateTime(shift);
                                }
                            });
                        }
                    }       
                }
            });       
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
    function getShift(date = $("#datetimepicker1").val()){
        var div_data = "";
        var doctor = $("#doctor").val();
        $("#shift").html("<div class='alert alert-danger' role='alert'><?php echo $this->lang->line('no_slot_available'); ?></div>");
        var global_shift = $("#global_shift").val();
        if (date==''){
            return;
        }
        $.ajax({
            url: '<?php echo base_url(); ?>site/getShift',
            type: "POST",
            data: {doctor: doctor, date: date, global_shift:global_shift},
            dataType: 'json',
            success: function(res){
                if(res.length){
                    $("#shift").html("<center><h3><?php echo $this->lang->line('slot'); ?></h3></center>");
                    $.each(res, function (i, obj)
                    {
                        var elemm = document.createElement('div');   
                        elemm.className = 'user-slot-container';
                        var div = document.createElement('div');
                        div.className =  'slot-details each-slot-duration';
                        div.onclick = function() {getSlotByShift(obj.id);  };
                        var strong = document.createElement("strong");
                        div.appendChild(strong);
                        strong.appendChild(document.createTextNode(obj.start_time +" - "+ obj.end_time));
                        elemm.appendChild(div);
                        document.getElementById("shift").appendChild(elemm);
                    });
                }
            }
        });
    }
</script>
<script>


    function errorMsg(msg) {
        toastr.error(msg);
    }
    $("#appointment_form").submit(function(){
       
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>' + 'welcome/appointment',
            data: $('#appointment_form').serialize(),
            dataType: 'json',
            beforeSend: function () {
                $('.allotment_error').empty();
                $("#submitbtn").button('loading');
            },
            success: function (data) {
                // refreshCaptcha();
                if (data.status == 1) {
                 
                    window.location.replace("<?php echo site_url('patient/dashboard/appointment') ?>");
                } else if (data.status == 0) {
                     var list = $('<ul/>');
                        $.each(data.error, function (key, value) {

                            if (value != "") {
                                list.append(value);
                            }
                        });
                        errorMsg(list);
                }else if (data.status == 2) {
                    $('.allotment_error').append(
                        $('<div/>')    
                            .addClass("alert alert-info")
                            .text(data.msg)
                        );                    
                }
                $("#submitbtn").button('reset');
            },
            error: function (xhr) { // if error occured
                $("#submitbtn").button('reset');
            },
            complete: function () {
                $("#submitbtn").button('reset');
            }
        });
    });

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
                $("#global_shift").select2().select2('val', "");

           }
        });
    }
</script>
<script type="text/javascript">
    function refreshCaptcha(){
        $.ajax({
            type: "POST",
            url: "<?php echo base_url('site/refreshCaptcha'); ?>",
            data: {},
            success: function(captcha){
                $(".captcha_image").html(captcha);
                $("#captcha").val("");
                $("#captcha_register").val("");
            }
        });
    }   


    function validateTime(id){
        let date = $("#datetimepicker1").val();
        if(id){
            $.ajax({
                url: '<?php echo base_url(); ?>'+'welcome/getShiftById',
                type: "POST",
                data: {id:id,date:date},
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