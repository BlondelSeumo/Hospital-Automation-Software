<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?> 
<div class="box-body pb0">
    <div class="col-lg-2 col-md-2 col-sm-3 text-center">
        <?php
        $image = $result['image'];
        if (!empty($image)) {
            $file = $result['image'];
        } else {
            $file = "uploads/patient_images/no_image.png";
        }
        ?> 
 
        <img width="115" height="115" class="profile-user-img img-responsive img-circle" src="<?php echo base_url() . $file.img_time() ?>" alt="No Image">
        <div class="editviewdelete-icon pt8">           
            
        </div>  
    </div>
    <div class="col-md-10 col-lg-10 col-sm-9">
        <div class="table-responsive">
            <table class="table table-striped mb0 font13">
                <tbody>
                    <tr>
                    <?php if(isset($case_id)) { ?>
                        <th class="bozerotop"><?php echo $this->lang->line('case_id'); ?></th>
                        <td class="bozerotop"><?php echo $case_id;?>  </td>
                    <?php } 
                        if(isset($result['appointment_date'])) {
                    ?>
                        <th class="bozerotop"><?php echo $this->lang->line('appointment_date');?></th>
                        <td class="bozerotop"><?php if($result['appointment_date']!='' && $result['appointment_date']!='0000-00-00'){
                            echo $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date'],$this->customlib->getHospitalTimeFormat());
                        } ?>
                        </td>
                    <?php } ?>
                    </tr> 
                    <tr>
                        <th class="bozerotop"><?php echo $this->lang->line('name'); ?></th>
                        <td class="bozerotop"><?php echo composePatientName($result['patient_name'],$result['patient_id']); ?></td>
                        <th class="bozerotop"><?php echo $this->lang->line('guardian_name'); ?></th>
                        <td class="bozerotop"><?php echo $result['guardian_name']; ?></td>
                    </tr>
                    <tr>
                        <th class="bozerotop"><?php echo $this->lang->line('gender'); ?></th>
                        <td class="bozerotop"><?php echo $this->lang->line(strtolower($result['gender'])); ?></td>
                        <th class="bozerotop"><?php echo $this->lang->line('age'); ?></th>
                        <td class="bozerotop">
                            <?php                                 
                               echo $this->customlib->getPatientAge($result['age'],$result['month'],$result['day']);
                            ?>   
                        </td>
                    </tr>
                    <tr>
                        <th class="bozerotop"><?php echo $this->lang->line('phone'); ?></th>
                        <td class="bozerotop"><?php echo $result['mobileno']; ?></td>
                        <?php 
                          if(isset($result['opdid']) && $result['opdid']!='' && $result['opdid']!=0){?>
                            <th class="bozerotop"><?php echo $this->lang->line('opd_no'); ?></th>
                            <td class="bozerotop"><?php
                                if($result['opdid']!='' && $result['opdid']!=0){
                                    echo $this->customlib->getPatientSessionPrefixByType('opd_no').$result['opdid'];
                                } ?>
                            </td>                        
                        <?php } ?>
                    </tr>
                    <tr>
                    <?php 
                    if(isset($result['ipdid']) && $result['ipdid']!='' && $result['ipdid']!=0){?>                    
                        <th class="bozerotop"><?php echo $this->lang->line('ipd_no'); ?></th>
                        <td class="bozerotop"><?php
                            if($result['ipdid']!='' && $result['ipdid']!=0){
                                echo $this->customlib->getPatientSessionPrefixByType('ipd_no').$result['ipdid'];
                            }
                            
                                if ($result['discharged'] == 'yes') {
                                    echo " <span class='label label-warning'>" . $this->lang->line("discharged") . "</span>";
                                }
                            ?>
                        </td>
                    <?php } ?>
                    <?php if(isset($result['ipdid']) && $result['ipdid']!='' && $result['ipdid']!=0){
                            if(isset($result['credit_limit'])){ ?>
                            <th class="bozerotop"><?php
                                echo $this->lang->line('credit_limit') . " (" . $currency_symbol . ")";
                                ;
                                ?></th>
                            <td class="bozerotop"><?php echo $result['credit_limit']; ?></td>
                        <?php } }?>                    
                    </tr>
                    <tr>
                    <?php 
                    if(isset($result['ipdid']) && $result['ipdid']!='' && $result['ipdid']!=0){
                        if(isset($result['date'])){ ?>
                            <th class="bozerotop"><?php echo $this->lang->line('admission_date'); ?></th>
                            <td class="bozerotop"><?php if($result['date']!='' && $result['date']!='0000-00-00'){                               
                                echo $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->customlib->getHospitalTimeFormat());
                            } ?>
                            </td>
                        <?php } ?>
                        <?php if(isset($result['ipdid']) && $result['ipdid']!='' && $result['ipdid']!=0){
                            if(isset($result['bed_name'])){ ?>
                            <th class="bozerotop"><?php echo $this->lang->line('bed'); ?></th>
                            <td class="bozerotop"><?php echo $result['bed_name'] . " - " . $result['bedgroup_name'] . " - " . $result['floor_name'] ?></td>
                        <?php } } }?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>           
</div>