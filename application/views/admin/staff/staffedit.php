<link href="<?php echo base_url(); ?>backend/multiselect/css/jquery.multiselect.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>backend/multiselect/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>backend/multiselect/js/jquery.multiselect.js"></script>

<div class="content-wrapper">  
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <form id="form1" action="<?php echo site_url('admin/staff/edit/' . $staff["id"]) ?>" id="employeeform" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="tshadow mb25 bozero">    
                                <h4 class="pagetitleh2"><?php echo $this->lang->line('staff_basic_information'); ?> </h4>
                                <div class="around10">
                                    <?php  if ($this->session->flashdata('msg')) { ?> <div>  <?php echo $this->session->flashdata('msg') ?> </div> <?php $this->session->unset_userdata('msg'); }   ?> 
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <div class="row">                                        
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_id'); ?></label><small class="req"> *</small>
                                                <input autofocus="" id="employee_id" name="employee_id" placeholder="" value="<?php echo $staff["employee_id"] ?>" type="text" class="form-control"  value="" />
                                                <span class="text-danger"><?php echo form_error('employee_id'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_role'); ?></label><small class="req"> *</small>
                                                <select  id="role" name="role" class="form-control" >
                                                    <option value=""   ><?php echo $this->lang->line('select'); ?></option>
                                                    <?php
                                                    foreach ($getStaffRole as $key => $role) {
                                                        ?>
                                                        <option value="<?php echo $role["id"] ?>" <?php
                                                        if ($staff["user_type"] == $role["type"]) {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo $role["type"] ?></option>
                                                            <?php }
                                                            ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('role'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_designation'); ?></label>
                                                <select id="designation" name="designation" placeholder="" type="text" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach ($designation as $key => $value) {
                                                        ?>
                                                        <option value="<?php echo $value["id"] ?>" <?php
                                                        if ($staff["staff_designation_id"] == $value["id"]) {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo $value["designation"] ?></option>
                                                            <?php }
                                                            ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('designation'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_department'); ?></label>
                                                <select id="department" name="department" placeholder="" type="text" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach ($department as $key => $value) {
                                                        ?>
                                                        <option value="<?php echo $value["id"] ?>" <?php
                                                        if ($staff["department_id"] == $value["id"]) {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo $value["department_name"] ?></option>
                                                            <?php }
                                                            ?>
                                                </select> 
                                                <span class="text-danger"><?php echo form_error('department'); ?></span>
                                            </div>
                                        </div>
										<div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_specialist'); ?></label>
                                                <?php 
                                                $specialistarray[]='';
                                                $specialist_array[]=''; 

                                                foreach($specialist_list as $specialist_list_value){
                                                    $specialist_array[] = $specialist_list_value;                                                    
                                                } $specialistarray[] = $specialist_array;  ?> 
                                                <select id="specialistOpt" name="specialist[]" placeholder="" type="text" class="form-control" multiple>
                                                    <?php  foreach ($specialist as $dkey => $dvalue) {   ?>
                                                    <option value="<?php echo $dvalue["id"]; ?>"<?php
                                                            if ((isset($specialist_list)) && ( in_array($dvalue["id"], $specialistarray[1])))                              
                                                            { echo "selected"; }?>>                                     
                                                            <?php echo $dvalue["specialist_name"]  ?>            
                                                    </option>   
                                                    <?php } ?> 
                                                </select> 
                                                <span class="text-danger"><?php echo form_error('specialist'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_first_name'); ?></label><small class="req"> *</small>
                                                <input id="firstname" name="name" placeholder="" type="text" class="form-control" value="<?php echo $staff["name"] ?>" />
                                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_last_name'); ?></label>
                                                <input id="surname" name="surname" placeholder="" type="text" class="form-control" value="<?php echo $staff["surname"] ?>" />
                                                <span class="text-danger"><?php echo form_error('surname'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_father_name'); ?></label>
                                                <input id="father_name" name="father_name" placeholder="" type="text" class="form-control" value="<?php echo $staff["father_name"] ?>" />
                                                <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_mother_name'); ?></label>
                                                <input id="mother_name" name="mother_name" placeholder="" type="text" class="form-control" value="<?php echo $staff["mother_name"] ?>" />
                                                <span class="text-danger"><?php echo form_error('mother_name'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputFile"> <?php echo $this->lang->line('staff_gender'); ?></label><small class="req"> *</small>
                                                <select class="form-control" name="gender">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php
                                                    foreach ($genderList as $key => $value) {
                                                        ?>
                                                        <option value="<?php echo $key; ?>" <?php if ($staff['gender'] == $key) echo "selected"; ?>><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_marital_status'); ?></label>
                                                <select class="form-control" name="marital_status">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($marital_status as $makey => $mavalue) {
                                                        ?>
                                                        <option <?php
                                                        if ($staff["marital_status"] == $mavalue) {
                                                            echo "selected";
                                                        }
                                                        ?> value="<?php echo $mavalue; ?>"><?php echo $mavalue; ?></option>
                                                        <?php } ?> 
                                                </select>
                                                <span class="text-danger"><?php echo form_error('marital_status'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_blood_group'); ?></label>
                                                <select class="form-control" name="blood_group">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($bloodgroup as $bgkey => $bgvalue) {
                                                        ?>
                                                        <option <?php
                                                        if ($staff["blood_group"] == $bgvalue) {
                                                            echo "selected";
                                                        }
                                                        ?> value="<?php echo $bgvalue ?>"><?php echo $bgvalue ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_date_of_birth'); ?></label><small class="req"> *</small>
                                                <input id="dob" name="dob" placeholder="" type="text" class="form-control date" value="<?php
                                                if (!empty($staff["dob"])) {
                                                    echo date($this->customlib->getHospitalDateFormat(), strtotime($staff["dob"]));
                                                }
                                                ?>" readonly="readonly"/>
                                                <span class="text-danger"><?php echo form_error('dob'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_date_of_joining'); ?></label>
                                                <input id="date_of_joining" name="date_of_joining" placeholder="" type="text" class="form-control date" value="<?php if ($staff["date_of_joining"] != '0000-00-00' && $staff["date_of_joining"]!="") {
                                                    echo date($this->customlib->getHospitalDateFormat(), strtotime($staff["date_of_joining"]));
                                                }
                                                ?>"  />
                                                <span class="text-danger"><?php echo form_error('date_of_joining'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_phone'); ?></label>
                                                <input id="mobileno" name="contactno" placeholder="" type="text" class="form-control" value="<?php echo $staff["contact_no"] ?>" />
                                                <input id="editid" name="editid" placeholder="" type="hidden" class="form-control" value="<?php echo $staff["id"]; ?>" />
                                                <span class="text-danger"><?php echo form_error('contactno'); ?></span>
                                            </div>
                                        </div> 
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_emergency_contact'); ?></label>
                                                <input id="emgmobileno" name="emgcontactno" placeholder="" type="text" class="form-control" value="<?php echo $staff["emergency_contact_no"] ?>" />                                                
                                                <span class="text-danger"><?php echo form_error('emgcontactno'); ?></span>
                                            </div>
                                        </div> 
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_email'); ?></label><small class="req"> *</small>
                                                <input id="email" name="email" placeholder="" type="text" class="form-control" value="<?php echo $staff["email"] ?>" />
                                                <span class="text-danger"><?php echo form_error('email'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('staff_photo'); ?></label>
                                                <div><input class="filestyle form-control" type='file' name='file' id="file" size='20' />
                                                </div>
                                                <span class="text-danger"><?php echo form_error('file'); ?></span>
                                            </div>
                                        </div>                          
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('staff_current_address'); ?></label>
                                                <div><textarea name="address" class="form-control"><?php echo $staff["local_address"] ?></textarea>
                                                </div>
                                                <span class="text-danger"></span></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('staff_permanent_address'); ?></label>
                                                <div><textarea name="permanent_address" class="form-control"><?php echo $staff["permanent_address"] ?></textarea>
                                                </div>
                                                <span class="text-danger"></span></div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_qualification'); ?></label>
                                                <textarea id="qualification" name="qualification" placeholder=""  class="form-control" ><?php echo $staff["qualification"] ?></textarea>
                                                <span class="text-danger"><?php echo form_error('qualification'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_work_experience'); ?></label>
                                                <textarea id="permanent_address" name="work_exp" placeholder="" class="form-control"><?php echo $staff["work_exp"] ?></textarea>
                                                <span class="text-danger"><?php echo form_error('work_exp'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('staff_specialization'); ?></label>
                                                <div><textarea name="specialization" class="form-control"><?php echo $staff["specialization"] ?></textarea>
                                                </div>
                                                <span class="text-danger"></span></div>
                                        </div>                          
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputFile"><?php echo $this->lang->line('staff_note'); ?></label>
                                                <div><textarea name="note" class="form-control"><?php echo $staff["note"] ?></textarea>
                                                </div>
                                                <span class="text-danger"></span></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('pan_number'); ?></label>
                                                <input id="pan_number" name="pan_number" placeholder="" type="text" class="form-control" value="<?php echo $staff['pan_number']; ?>" />
                                                <span class="text-danger"></span></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('national_identification_number'); ?></label>
                                                <input id="identification_number" name="identification_number" placeholder="" type="text" class="form-control"  value="<?php echo $staff['identification_number']; ?>" />
                                                <span class="text-danger"></span></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('local_identification_number'); ?></label>
                                                <input id="local_identification_number" name="local_identification_number" placeholder="" type="text" class="form-control"  value="<?php echo $staff['local_identification_number']; ?>" />
                                                <span class="text-danger"></span></div>
                                        </div>
                                        <div class="">
                                            <?php
                                             echo display_custom_fields('staff',$staff['id']);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-group collapsed-box">                      
                                <div class="panel box box-success collapsed-box">
                                    <div class="box-header with-border">
                                        <a data-widget="collapse" data-original-title="Collapse" aria-expanded="false" class="collapsed btn boxplus">
                                            <i class="fa fa-fw fa-plus"></i><?php echo $this->lang->line('add_more_details'); ?>
                                        </a>
                                    </div>
                                    <div class="box-body" style="padding: 0;">
                                        <div class="tshadow-new">    
                                            <h4 class="pagetitleh2"><?php echo $this->lang->line('staff_payroll'); ?>
                                            </h4>
                                            <div class="row around10">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_epf_no'); ?></label>
                                                        <input id="epf_no" name="epf_no" placeholder="" type="text" class="form-control" value="<?php echo $staff["epf_no"] ?>"  />
                                                        <span class="text-danger"><?php echo form_error('epf_no'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_contract_type'); ?></label>
                                                        <select class="form-control" name="contract_type">
                                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                                            <?php foreach ($contract_type as $key => $value) { ?>
                                                                <option value="<?php echo $key ?>" <?php
                                                                if ($staff["contract_type"] == $key) {
                                                                    echo "selected";
                                                                }
                                                                ?>><?php echo $value ?></option>
                                                            <?php } ?>     
                                                        </select>
                                                        <span class="text-danger"><?php echo form_error('contract_type'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_basic_salary'); ?></label>
                                                        <input type="text" value="<?php echo $staff["basic_salary"] ?>" class="form-control" name="basic_salary">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_work_shift'); ?></label>
                                                        <input id="shift" name="shift" placeholder="" type="text" class="form-control" value="<?php echo $staff["shift"] ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_work_location'); ?></label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control" value="<?php echo $staff["location"] ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_date_of_leaving'); ?></label>
                                                        <input id="date_of_leaving" name="date_of_leaving" placeholder="" type="text" class="form-control date" value="<?php
                                                        if ($staff["date_of_leaving"] != '0000-00-00' && $staff["date_of_leaving"] != '') {
                                                            echo date($this->customlib->getHospitalDateFormat(), strtotime($staff["date_of_leaving"]));
                                                        }
                                                        ?>" />
                                                        <span class="text-danger"><?php echo form_error('date_of_leaving'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tshadow-new">    
                                            <h4 class="pagetitleh2"><?php echo $this->lang->line('staff_leaves'); ?>
                                            </h4>
                                            <div class="row around10" >
                                                <?php
                                                $j = 0;
                                                foreach ($leavetypeList as $key => $leave) {
                                                    # code...
                                                    ?>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $leave["type"]; ?></label>
                                                            <input id="ifsc_code" name="alloted_leave[]" placeholder="<?php echo $this->lang->line('staff_number_of_leaves'); ?>" type="text" class="form-control"  value="<?php
                                                            if (array_key_exists($j, $staffLeaveDetails)) {
                                                                echo $staffLeaveDetails[$j]["alloted_leave"];
                                                            }
                                                            ?>" />
                                                            <input  name="leave_type[]" placeholder="" type="hidden" readonly class="form-control" value="<?php echo $leave["type"] ?>" />
                                                            <input  name="altid[]" placeholder="" type="hidden" readonly class="form-control" value="<?php
                                                            if (array_key_exists($j, $staffLeaveDetails)) {
                                                                echo $staffLeaveDetails[$j]["altid"];
                                                            }
                                                            ?>" />
                                                            <input  name="leave_type_id[]" placeholder="" type="hidden" class="form-control" value="<?php echo $leave["id"]; ?>" />
                                                            <span class="text-danger"><?php echo form_error('ifsc_code'); ?></span>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $j++;
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="tshadow-new">    
                                            <h4 class="pagetitleh2"><?php echo $this->lang->line('staff_bank_account_details'); ?>
                                            </h4>
                                            <div class="row around10">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_account_title'); ?></label>
                                                        <input id="account_title" name="account_title" placeholder="" type="text" class="form-control" value="<?php echo $staff["account_title"] ?>" />
                                                        <span class="text-danger"><?php echo form_error('staff_bank_account_number'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_bank_account_number'); ?></label>
                                                        <input id="bank_account_no" name="bank_account_no" placeholder="" type="text" class="form-control" value="<?php echo $staff["bank_account_no"] ?>" />
                                                        <span class="text-danger"><?php echo form_error('bank_account_no'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_bank_name'); ?></label>
                                                        <input id="bank_name" name="bank_name" placeholder="" type="text" class="form-control" value="<?php echo $staff["bank_name"] ?>" />
                                                        <span class="text-danger"><?php echo form_error('bank_name'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_ifsc_code'); ?></label>
                                                        <input id="ifsc_code" name="ifsc_code" placeholder="" type="text" class="form-control" value="<?php echo $staff["ifsc_code"] ?>" />
                                                        <span class="text-danger"><?php echo form_error('ifsc_code'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_bank_branch_name'); ?></label>
                                                        <input id="bank_branch" name="bank_branch" placeholder="" type="text" class="form-control" value="<?php echo $staff["bank_branch"] ?>" />
                                                        <span class="text-danger"><?php echo form_error('bank_branch'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="tshadow-new">    
                                            <h4 class="pagetitleh2"><?php echo $this->lang->line('staff_social_media_link'); ?>
                                            </h4>
                                            <div class="row around10">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_facebook_url'); ?></label>
                                                        <input id="bank_account_no" name="facebook" placeholder="" type="text" class="form-control" value="<?php echo $staff["facebook"] ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_twitter_url'); ?></label>
                                                        <input id="bank_account_no" name="twitter" placeholder="" type="text" class="form-control" value="<?php echo $staff["twitter"] ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_linkedin_url'); ?></label>
                                                        <input id="bank_name" name="linkedin" placeholder="" type="text" class="form-control" value="<?php echo $staff["linkedin"] ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_instagram_url'); ?></label>
                                                        <input id="instagram" name="instagram" placeholder="" type="text" class="form-control" value="<?php echo $staff["instagram"] ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>   
                                        <div id='upload_documents_hide_show'>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="tshadow-new">
                                                        <h4 class="pagetitleh2"><?php echo $this->lang->line('staff_upload_documents'); ?></h4>
                                                        <div class="row around10">   
                                                            <div class="col-md-6">
                                                                <table class="table">
                                                                    <tbody><tr>
                                                                            <th style="width: 10px">#</th>
                                                                            <th><?php echo $this->lang->line('staff_title'); ?></th>
                                                                            <th><?php echo $this->lang->line('staff_documents'); ?></th>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>1.</td>
                                                                            <td><?php echo $this->lang->line('staff_resume'); ?></td>
                                                                            <td>
                                                                                <input class="filestyle form-control" type='file' name='first_doc' id="doc1" >
                                                                                <input class=" form-control" type='hidden' name='resume' value="<?php echo $staff["resume"] ?>" >
                                                                                <span class="text-danger"><?php echo form_error('first_doc'); ?></span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>3.</td>
                                                                            <td><?php echo $this->lang->line('staff_resignation_letter'); ?></td>
                                                                            <td>
                                                                                <input class="filestyle form-control" type='file' name='third_doc' id="doc3" >
                                                                                <input class=" form-control" type='hidden' name='resignation_letter' value="<?php echo $staff["resignation_letter"] ?>" >
                                                                                <span class="text-danger"><?php echo form_error('third_doc'); ?></span>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
																</table>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <table class="table">
                                                                    <tbody><tr>
                                                                            <th style="width: 10px">#</th>
                                                                            <th><?php echo $this->lang->line('staff_title'); ?></th>
                                                                            <th><?php echo $this->lang->line('staff_documents'); ?></th>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>2.</td>
                                                                            <td><?php echo $this->lang->line('staff_joining_letter'); ?></td>
                                                                            <td>
                                                                                <input class="filestyle form-control" type='file' name='second_doc' id="doc2" >
                                                                                <input class=" form-control" type='hidden' name='joining_letter' value="<?php echo $staff["joining_letter"] ?>" >
                                                                                <span class="text-danger"><?php echo form_error('second_doc'); ?></span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>4.</td>
                                                                            <td><?php echo $this->lang->line('staff_other_documents'); ?><input type="hidden" name='fourth_title' value="<?php echo $staff["other_document_file"] ?>" class="form-control" placeholder="Other Documents"></td>
                                                                            <td>
                                                                                <input class="filestyle form-control" type='file' name='fourth_doc' id="doc4">
                                                                                <input class=" form-control" type='hidden' name='other_document_file' value="<?php echo $staff["other_document_file"] ?>" >
                                                                                <span class="text-danger"><?php echo form_error('fourth_doc'); ?></span>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
																</table>
                                                            </div>
                                                        </div>
                                                    </div>    
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>               
            </div>
        </div> 
	</div>
</section>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/js/savemode.js"></script>    
<script>
$('#specialistOpt').multiselect({
    columns: 1,
    placeholder: 'Select Specialist',
    search: true  
});    
</script> 