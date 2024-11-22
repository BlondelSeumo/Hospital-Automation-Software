<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('staff_attendance'); ?></h3>
                    </div>

                    <!-- searching tab -->
                    <form id='form1' action="<?php echo site_url('admin/staffattendance/index') ?>"  method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php  if ($this->session->flashdata('msg')) { ?> <div>  <?php echo $this->session->flashdata('msg') ?> </div> <?php $this->session->unset_userdata('msg'); }   ?>
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('staff_role'); ?></label>
                                        <select id="class_id" name="user_id" class="form-control" >
                                            <option value="select"><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $key => $class) {
                                                ?>
                                                <option value="<?php echo $class["type"] ?>"   <?php
                                                if ($class["type"] == $user_type_id) {
                                                    echo "selected =selected";
                                                }
                                                ?>><?php print_r($class["type"]) ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <?php echo $this->lang->line('staff_attendance_date'); ?>
                                        </label>
                                        <input id="date" name="date" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date', date($this->customlib->getHospitalDateFormat())); ?>" readonly="readonly"/>
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>                      
                            <div class="col-md-12">
                                <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                            </div>
                          </div>
                        </div>
                    </form>
                 <!-- searching tab -->

                    <?php
                    if (isset($resultlist)) {
                        ?>
                        <div class="ptbnull"></div>  
                        <div class="box border0 clear">
                            <div class="box-body">
                                <?php
                                $can_edit=0;

                                if (!empty($resultlist)) {
                                    $checked = "";

                                    if (!isset($msg)) {
                                        if ($resultlist[0]['staff_attendance_type_id'] != "") {
                                            if ($resultlist[0]['staff_attendance_type_id'] != 5) {
                                                 $can_edit=1; ?>
                                                <?php
                                            } else {
                                                $checked = "checked='checked'";                                               
                                            }
                                        }
                                    } else {                                        
                                   
                                    }  ?>
                                    <form action="<?php echo site_url('admin/staffattendance/index') ?>" method="post">
                                        <?php echo $this->customlib->getCSRF(); ?>
                                        <div class="mailbox-controls">

                                        <div class="row">
                                            <div class="col-md-9">                                           
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('set_attendance_for_all_staff_as') ; ?> &nbsp;</label>
                                                    <?php
                                                    foreach ($attendencetypeslist as $key => $type) {  ?>
                                                            <div class="radio radio-info radio-inline">
                                                                <input type="radio" name="attendencetype" data-record_id="<?php echo $type['id'] ?>"  class="default_radio" value="radio_<?php echo $type['id'] ?>" id="attendencetype<?php echo $type['id'] ?>"   onclick="getatten(<?php echo $type['id'] ?>)">
                                                                <label for="attendencetype<?php echo $type['id'] ?>">
                                                                        
																		<?php echo $this->lang->line($type['long_lang_name']) . " (" . $type['key_value'] . ")"; ?>
                                                                </label>
                                                                </div>
                                                            <?php  }  ?>
                                                        </div>                                                 
                                                </div>

                                                <div class="col-md-3">
                                                <div class="pull-right">
                                                <?php 
                                                if($can_edit==0){
                                                    if ($this->rbac->hasPrivilege('staff_attendance', 'can_add')) { ?>
                                                    <button type="submit" name="search" value="saveattendence" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-save"></i> <?php echo $this->lang->line('save_attendance'); ?> </button> <?php
                                              } 
                                                }else{
                                                    if ($this->rbac->hasPrivilege('staff_attendance', 'can_edit')) { ?>
                                                    <button type="submit" name="search" value="saveattendence" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-save"></i> <?php echo $this->lang->line('edit_attendance'); ?> </button> <?php
                                                } }
                                                ?>                                     
                                            </div>
                                           </div>
                                        </div>                                           
                                        </div>
                                        <input type="hidden" name="user_id" value="<?php echo $user_type_id; ?>">
                                        <input type="hidden" name="section_id" value="">
                                        <input type="hidden" name="date" value="<?php echo $date; ?>">
                                        <div class="table-responsive table-responsive-visible">
                                            <table class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><?php echo $this->lang->line('staff_id'); ?></th>
                                                        <th><?php echo $this->lang->line('staff_name'); ?></th>
                                                        <th><?php echo $this->lang->line('staff_role'); ?></th>
                                                        <th class=""><?php echo $this->lang->line('staff_attendance'); ?></th>
                                                        <?php
                                                    if ($sch_setting->biometric) {
                                                    ?>
                                                        <th width="10%"><?php echo $this->lang->line('date'); ?></th>
                                                    <?php
                                                    }
                                                    ?>
                                                    <th class="white-space-nowrap"><?php echo $this->lang->line('entry_time'); ?></th>
                                                    <th class="white-space-nowrap"><?php echo $this->lang->line('exit_time'); ?></th>
                                                    <th class="white-space-nowrap"><?php echo $this->lang->line('staff_note'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $row_count = 1;
                                                    foreach ($resultlist as $key => $value) {
                                                        $attendendence_id = $value["id"];
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="staff_role[]" id="staff_role_<?php echo $value['role_id']; ?>" value="<?php echo $value['role_id']; ?>">
                                                                <input type="hidden" name="patient_session[]" value="<?php echo $value['staff_id']; ?>">
                                                                <input type="hidden" value="<?php echo $attendendence_id ?>"  name="attendendence_id<?php echo $value["staff_id"]; ?>">
                                                                <?php echo $row_count; ?>
                                                            </td>
                                                            <td><?php echo $value['employee_id']; ?></td>
                                                            <td><?php echo $value['name'] . " " . $value['surname']; ?></td>
                                                            <td><?php echo $value['user_type']; ?></td>
                                                            <td>
                                                                <?php
                                                                $c = 1;
                                                                $count = 0;
                                                                foreach ($attendencetypeslist as $key => $type) {
                                                                    
                                                                        $att_type = str_replace(" ", "_", strtolower($type['type']));
                                                                        if ($value["date"] != "xxx") { ?>
                                                                            <div class="radio radio-info radio-inline">
                                                                                <input <?php if ($value['staff_attendance_type_id'] == $type['id']) echo "checked"; ?>  type="radio" id="attendencetype<?php echo $value['staff_id'] . "-" . $count; ?>" value="<?php echo $type['id'] ?>" name="attendencetype<?php echo $value['staff_id']; ?>"  class="radio_<?php echo $type['id'] ?>">
                                                                                <label for="attendencetype<?php echo $value['staff_id'] . "-" . $count; ?>">
                                                                                    <?php echo $this->lang->line($type['long_lang_name']); ?> 
                                                                                </label>
                                                                            </div>
                                                                            <?php
                                                                        }else { ?>
                                                                            <div class="radio radio-info radio-inline">
                                                                                <input <?php if (($c == 1) && ($resultlist[0]['staff_attendance_type_id'] != 5)) echo "checked"; ?> type="radio" id="attendencetype<?php echo $value['staff_id'] . "-" . $count; ?>" value="<?php echo $type['id'] ?>" name="attendencetype<?php echo $value['staff_id']; ?>"  class="radio_<?php echo $type['id'] ?>">
                                                                                <label for="attendencetype<?php echo $value['staff_id'] . "-" . $count; ?>"> 
                                                                                    <?php  echo $this->lang->line($type['long_lang_name']); ?> 
                                                                                </label>
                                                                            </div>
                                                                            <?php
                                                                        }
                                                                        $c++;
                                                                        $count++;                                                                    
                                                                }
                                                                ?>
                                                            </td>                                                           
                                                            
                                                        <?php if ($sch_setting->biometric) { ?>
                                                            <td>
                                                            <?php
                                                                if ($value['biometric_attendence'] ) {

                                                                    echo $this->customlib->dateyyyymmddToDateTimeformat($value['attendence_dt']);
                                                                }
                                                                ?>
                                                            </td>
                                                        <?php
                                                        }
                                                        ?>
                                                    <td>
                                                        <input type="text" value="<?php if($value["in_time"]!="00:00:00"){ echo $value["in_time"]; }else{ echo "";} ?>"  name="in_time_<?php echo $value["staff_id"] ?>" id="in_time_<?php echo $value["staff_id"] ?>" class="form-control datetime in_time time in_time_<?php echo $value['role_id']; ?>">
                                                    </td>                                                        
                                                    <td>
                                                        <input type="text" value="<?php if($value["out_time"]!="00:00:00"){ echo $value["out_time"]; }else{ echo "";} ?>"  name="out_time_<?php echo $value["staff_id"] ?>"  id="out_time_<?php echo $value["staff_id"] ?>" class="form-control datetime out_time time out_time_<?php echo $value['role_id']; ?>">
                                                    </td>                                                        
                                                        <?php if ($value["date"] == 'xxx') { ?> 
                                                        <td><input class="form-control" type="text" name="remark<?php echo $value["staff_id"] ?>"   ></td>
                                                        <?php } else { ?>
                                                        <td><input class="form-control" type="text" name="remark<?php echo $value["staff_id"] ?>" value="<?php echo $value["remark"]; ?>" ></td>
                                                        <?php } ?>
                                                        </tr>
                                                        <?php
                                                        $row_count++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
                                    <?php
                                } else {
                                    ?>
                                    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>   
                    <?php
                }
                ?>
                </section>
            </div>
			
            <script type="text/javascript">
                $(document).ready(function () {
                    $.extend($.fn.dataTable.defaults, {
                        searching: false,
                        ordering: true,
                        paging: false,
                        retrieve: true,
                        destroy: true,
                        info: false
                    });
                    var table = $('.example').DataTable();
                    table.buttons('.export').remove();
                });
            </script>       
            <script type="text/javascript">
                window.onload = function xy() {
                    var ch = '<?php
                if (!empty($resultlist)) {
                    echo $resultlist[0]['staff_attendance_type_id'];
                }
                ?>';

                    if (ch == 5) {
                        $("input[type=radio]").attr('disabled', true);
                    } else {
                        $("input[type=radio]").attr('disabled', false);
                    }
                };	

</script>            
         
<script type="text/javascript">
 $(function() {
     $('.time').datetimepicker({
            format: 'LT'
     });
 });

var attendance_setting = <?php echo json_encode($staff_settings) ?>;

$(document).ready(function() {
        $('.default_radio').click(function() {
            let radio_default = ($(this).val());
            let attendance_type_id = ($(this).data('record_id'));        
            var returnVal = confirm("<?php echo $this->lang->line('are_you_sure'); ?>");
            if (returnVal) {
            $("input[type=radio][class='" + radio_default + "']").prop("checked", returnVal);
            } else {
                return false;
            }
        });
});

function tConvert(time) {

if (time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/)) {
    const [timeWithoutPeriod, period] = time.split(" ");
    let [hours, minutes, seconds] = timeWithoutPeriod.split(":");
    let AM_PM = null;
    AM_PM = hours < 12 ? 'AM' : 'PM'; // Set AM/PM
    hours = hours % 12 || 12; // Adjust hours
    return `${hours}:${minutes} ${AM_PM}`;
} else {
    return time;
}
}

function getatten(atten_type){
    if(atten_type==3 || atten_type==5){
      $('.in_time').val('');
      $('.out_time').val('');  
      return false;
    }else{
        var role_id = $("input[name='staff_role[]']").map(function(){return $(this).val();}).get();
        let nm = (attendance_setting);     

        for(var i=0;i<role_id.length;i++){
        var returnValue = false;
        $.each(nm, function(key, value) {
            if (value.staff_attendence_type_id == atten_type  &&  value.role_id==role_id[i]) {                
                returnValue = [tConvert(value.entry_time_from), tConvert(value.entry_time_to)];
                $('.in_time_'+role_id[i]).val(returnValue[0]);
                $('.out_time_'+role_id[i]).val(returnValue[1]);                
            }else{
                            
            }
        });        
    }
    }
}
</script> 