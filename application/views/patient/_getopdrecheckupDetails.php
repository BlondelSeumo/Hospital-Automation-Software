<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
 ?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="table-responsive">
            <table class="table mb0 table-striped table-bordered examples tablelr0space noborder">
                <tr>
                    <th width="15%"><?php echo $this->lang->line('case_id'); ?></th>
                    <td width="35%"><span id="opd_no"><?php if(!empty($result['case_reference_id'])){ echo $result['case_reference_id']; } ?></span></td>
                    <th width="15%"><?php echo $this->lang->line("recheckup_id"); ?></th>
                    <td width="35%"><span id="opd_no"><?php if(!empty($result['id'])) { echo $this->customlib->getPatientSessionPrefixByType('checkup_id').$result['id']; } ?></span></td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('opd_no'); ?></th>
                    <td width="35%"><span id="opd_no"><?php if(!empty($result['opd_details_id'])) { echo $this->customlib->getPatientSessionPrefixByType('opd_no').$result['opd_details_id']; } ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('old_patient'); ?></th>
                    <td width="35%"><span id="old_patient"><?php if(!empty($result['patient_old'])){ echo $this->lang->line($result['patient_old']); } ?></span></td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('patient_name'); ?></th>
                    <td width="35%"><span id="patient_name"><?php if(!empty($result['patient_id'])){ echo composePatientName($result['patient_name'],$result['patient_id']); }?></span></td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('guardian_name'); ?></th>
                    <td width="35%"><span id='guardian_name'><?php if(!empty($result['guardian_name'])){ echo $result['guardian_name']; } ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('gender'); ?></th>
                    <td width="35%"><span id='gen'><?php if(!empty($result['gender'])){ echo $result['gender']; } ?></span></td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('marital_status'); ?></th>
                    <td width="35%"><span id="marital_status"><?php if(!empty($result['marital_status'])){ echo $result['marital_status']; } ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('phone'); ?></th>
                    <td width="35%"><span id="contact"><?php if(!empty($result['mobileno'])){ echo $result['mobileno']; } ?></span></td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('email'); ?></th>
                    <td width="35%"><span id='email' style="text-transform: none"><?php if(!empty($result['email'])){ echo $result['email']; } ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('address'); ?></th>
                    <td width="35%"><span id='patient_address'><?php if(!empty($result['address'])){ echo $result['address']; } ?></span></td>
                </tr>
                <tr>  
                    <th width="15%"><?php echo $this->lang->line('age'); ?></th>
                    <td width="35%"><span id="age"><?php if(!empty($result['day'])){ echo $this->customlib->getPatientAge($result['age'],$result['month'],$result['day']); } ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('blood_group'); ?></th>
                    <td width="35%"><span id="blood_group"><?php if(!empty($result['blood_group_name'])){ echo $result['blood_group_name']; } ?></span></td>
                </tr>
                           
                <tr>
                    <th width="15%"><?php echo $this->lang->line('known_allergies'); ?></th>
                    <td width="35%"><span id="known_allergies"><?php if(!empty($result['known_allergies'])){ echo $result['known_allergies']; } ?></span>
                    </td>
                    <th width="15%"><?php echo $this->lang->line('appointment_date'); ?></th>
                    <td width="35%"><span id="appointment_date"><?php if(!empty($result['appointment_date'])){ echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['appointment_date'])); } ?></span>
                    </td>  
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('case'); ?></th>
                    <td width="35%"><span id='case'><?php if(!empty($result['case_type'])){ echo $result['case_type']; } ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('casualty'); ?></th>
                    <td width="35%"><span id="casualty"><?php if(!empty($result['casualty'])){ echo $this->lang->line($result['casualty']); } ?></span>
                    </td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('reference'); ?></th>
                    <td width="35%"><span id="refference"><?php if(!empty($result['refference'])){ echo $result['refference']; } ?></span></td>        
                    <th width="15%"><?php echo $this->lang->line('tpa'); ?></th>
                    <td width="35%"><span id="organisation"><?php if(!empty($result['organisation_name'])){ echo $result['organisation_name']; } ?></span></td>
                </tr>                                  
                <tr>
                    <th width="15%"><?php echo $this->lang->line('consultant_doctor'); ?></th>
                    <td width="35%"><span id='doc'><?php if(!empty($result['name'])){ echo composeStaffNameByString($result['name'], $result['surname'], $result['employee_id']); } ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('note'); ?></th>
                    <td width="35%"><span id='note'><?php if(!empty($result['note'])){ echo $result['note']; } ?></span></td>
                </tr>                                    
                <tr>
                    <th width="15%"><?php echo $this->lang->line('symptoms'); ?></th>
                    <td width="50%"><span id='symptoms'><?php if(!empty($result['symptoms'])){ echo nl2br($result['symptoms']) ; }?></span></td>    
                </tr> 

                <?php  if (!empty($fields)) {
                foreach ($fields as $fields_key => $fields_value) {                    
                    $display_field = '';
                    if(!empty($result["$fields_value->name"])){
                    $display_field = $result["$fields_value->name"];
                    }
                    if ($fields_value->type == "link") {
                        $display_field = "<a href=" . $result["$fields_value->name"] . " target='_blank'>" . $result["$fields_value->name"] . "</a>";
                    }
                ?>
                <tr>
                    <th width="10%"><?php echo $fields_value->name; ?></th> 
                    <td width="10%"><?php echo $display_field; ?></td>
                </tr>
                <?php }
                }?>                                
                                  
            </table>
        </div>                       
    </div>  
</div>