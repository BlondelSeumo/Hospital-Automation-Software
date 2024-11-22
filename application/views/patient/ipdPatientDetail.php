<div class="modal-body pt0 pb0">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <tr>
                    <th width="15%"><?php echo $this->lang->line('patient_name'); ?></th>
                    <td width="35%"><span id="patient_name"><?php echo composePatientName($result['patient_name'],$result['patient_id']); ?></span>
                    </td>
                    <th width="15%"><?php echo $this->lang->line('gender'); ?></th>
                    <td width="35%"><span id='gen'><?php echo $result['gender'] ?></span></td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('guardian_name'); ?></th>
                    <td width="35%"><span id='guardian_name'><?php echo $result['guardian_name'] ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('phone'); ?></th>
                    <td width="35%"><span id="contact"><?php echo $result['mobileno'] ?></span>
                    </td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('marital_status'); ?></th>
                    <td width="35%"><span id="marital_status"><?php echo $result['marital_status'] ?></span>
                    </td>
                    <th width="15%"><?php echo $this->lang->line('address'); ?></th>
                    <td width="35%"><span id='patient_address'><?php echo $result['address'] ?></span></td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('email'); ?></th>
                    <td width="35%"><span id='email' style="text-transform: none"><?php echo $result['email'] ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('blood_group'); ?></th>
                    <td width="35%"><span id="blood_group"><?php echo $result['blood_group_name'] ?></span>
                    </td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('age'); ?></th>
                    <td width="35%"><span id="age"><?php echo $result['patient_age'] ?></span></td>
                   
                    <th width="15%"><?php echo $this->lang->line('symptoms'); ?></th>
                    <td width="35%"><span id='symptoms'><?php echo $result['symptoms'] ?></span></td>
                </tr>
                <tr>
                     <th width="15%"><?php echo $this->lang->line('bed_group'); ?></th>
                    <td width="35%"><span id="bed_group"><?php echo $result['bed_group'] ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('admission_date'); ?></th>
                    <td width="35%"><span id="admission_date"><?php echo $result['date'] ?></span>
                    </td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('known_allergies'); ?></th>
                    <td width="35%"><span id="known_allergies"><?php echo $result['known_allergies'] ?></span>
                    </td>
                    <th width="15%"><?php echo $this->lang->line('casualty'); ?></th>
                    <td width="35%"><span id="casualty"><?php echo $result['casualty'] ?></span>
                    </td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('case'); ?></th>
                    <td width="35%"><span id='case'><?php echo $result['case_type'] ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('tpa'); ?></th>
                    <td width="35%"><span id="organisation"><?php echo $result['organisation_name'] ?></span>
                    </td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('old_patient'); ?></th>
                    <td width="35%"><span id='old_patient'><?php echo $result['patient_old'] ?></span></td>
                    <th width="15%"><?php echo $this->lang->line('consultant_doctor'); ?></th>
                    <td width="35%"><span id='doc'><?php echo $result['doctor_name'] ?></span></td>
                </tr>
                <tr>
                    <th width="15%"><?php echo $this->lang->line('reference'); ?></th>
                    <td width="35%"><span id="refference"><?php echo $result['refference'] ?></span>
                    </td>
                    <th width="15%"><?php echo $this->lang->line('bed_number'); ?></th>
                    <td width="35%"><span id='bed_name'><?php echo $result['bed_name'] ?></span></td>
                </tr>
                
                <?php
                    if (!empty($fields)) {
                        foreach ($fields as $fields_key => $fields_value) {
                            ?>
                            <tr>
                                <th width="15%"><?php echo $fields_value->name; ?></th>
                                <td width="35%"><?php echo $result[$fields_value->name]; ?></td>
                            </tr>
                <?php } } ?>

            </table>
        </div>
</div>