<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
 ?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="table-responsive">
            <table class="table mb0 table-striped table-bordered examples tablelr0space noborder">
                <tr>
                    <th width="15%"><?php echo $this->lang->line('checkup_id'); ?></th>
                    <td width="35%"><span id="opd_no"><?php if($result["id"]!="") { echo $this->customlib->getSessionPrefixByType('checkup_id') .$result["id"] ;}  ?></span>
                    </td>
                    <th width="15%"><?php echo $this->lang->line('opd_id'); ?></th>
                    <td width="35%"><span id="opd_no"><?php if($result["opd_details_id"]!="") {echo $this->customlib->getSessionPrefixByType('opd_no') .$result["opd_details_id"] ; } ?></span>
                    </td>    
                </tr>
        	    <tr>
                    <th width="15%"><?php echo $this->lang->line('case_id'); ?></th>
                    <td width="35%"><span id="opd_no"><?php echo $result['case_reference_id'] ?></span>
                    </td>
                    <th width="15%"><?php echo $this->lang->line('patient_name'); ?></th>
                    <td width="35%"><span id="patient_name"><?php 
                    echo $result['patient_name']." (".$result['patient_id'].")" ;?></span>
                    </td>
                </tr>
                <tr> 
                    <th width="15%"><?php echo $this->lang->line('old_patient'); ?></th>
                    <td width="35%"><span id="old_patient"><?php echo $this->lang->line($result['patient_old']); ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('guardian_name'); ?></th>
                    <td width="35%"><span id='guardian_name'><?php echo $result['guardian_name'] ?></span></td>
                </tr>
                <tr>                    
                    <th width="15%"><?php echo $this->lang->line('gender'); ?></th>
                    <td width="35%"><span id='gen'><?php if($result['gender']){ echo $this->lang->line(strtolower($result['gender'])) ; } ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('marital_status'); ?></th>
                    <td width="35%"><span id="marital_status"><?php echo $result['marital_status'] ?></span>
                    </td>
                </tr>
                <tr>                    
                    <th width="15%"><?php echo $this->lang->line('phone'); ?></th>
                    <td width="35%"><span id="contact"><?php echo $result['mobileno'] ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('email'); ?></th>
                    <td width="35%"><span id='email' style="text-transform: none"><?php echo $result['email'] ?></span></td>                    
                </tr>
                <tr>                    
                    <th width="15%"><?php echo $this->lang->line('address'); ?></th>
                    <td width="35%"><span id='patient_address'><?php echo $result['address'] ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('age'); ?></th>
                    <td width="35%"><span id="age">
                        <?php 
                         echo $this->customlib->getPatientAge($result['age'],$result['month'],$result['day'])." (".$this->lang->line('as_of_date').' '.$this->customlib->YYYYMMDDTodateFormat($result['as_of_date']).")";;
                            ?></span>
                    </td>
                </tr>
                <tr>                      
                    <th width="15%"><?php echo $this->lang->line('blood_group'); ?></th>
                    <td width="35%"><span id="blood_group"><?php echo $result['blood_group_name'] ?></span></td>                   
                    <th width="15%"><?php echo $this->lang->line('known_allergies'); ?></th>
                    <td width="35%"><span id="known_allergies"><?php echo $result['known_allergies'] ?></span></td>
                </tr>
                <tr>                   
                    <th width="15%"><?php echo $this->lang->line('appointment_date'); ?></th>
                    <td width="35%"><span id="appointment_date"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($result['appointment_date']));  ?></span></td> 
                    <th width="15%"><?php echo $this->lang->line('case'); ?></th>
                    <td width="35%"><span id='case'><?php echo $result['case_type'] ?></span></td>
                </tr>
                <tr>                    
                    <th width="15%"><?php echo $this->lang->line('casualty'); ?></th>
                    <td width="35%"><span id="casualty"><?php echo $this->lang->line($result['casualty']); ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('reference'); ?></th>
                    <td width="35%"><span id="refference"><?php echo $result['refference'] ?></span>
                    </td>
                </tr>
                <tr>                    
                    <th width="15%"><?php echo $this->lang->line('tpa'); ?></th>
                    <td width="35%"><span id="organisation"><?php echo $result['organisation_name'] ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('consultant_doctor'); ?></th>
                    <td width="35%"><span id='doc'><?php echo composeStaffNameByString($result['name'],$result['surname'],$result['employee_id']); ?></span></td>                    
                </tr>
                <tr>  
                    <th width="15%"><?php echo $this->lang->line('is_antenatal'); ?></th>
                    <td width="35%"><span id="organisation"><?php if($result['is_antenatal'] != 1){ echo $this->lang->line('no'); }else{ echo $this->lang->line('yes'); } ?></span></td>  
                </tr>
                <tr>                   
                    <th width="15%"><?php echo $this->lang->line('note'); ?></th>
                    <td colspan="3"><span id='note'><?php echo $result['note'] ?></span></td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('symptoms'); ?></th>
                    <td colspan="3"><span id='symptoms'><?php if($result['symptoms']){ echo nl2br($result['symptoms']); } ?></span></td>    
                </tr> 
                 
                <?php  if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                    $display_field = $result["$fields_value->name"];
                    if ($fields_value->type == "link") {
                    $display_field = "<a href=" . $result["$fields_value->name"] . " target='_blank'>" . $result["$fields_value->name"] . "</a>";
                }
                ?>
                <tr>
                    <th width="15%"><?php echo $fields_value->name; ?></th> 
                    <td colspan="3"><?php echo $display_field; ?></td>
                </tr>

                <?php }
                }?>
            </table>
        </div>                 
    </div>  
</div>