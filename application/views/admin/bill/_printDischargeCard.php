<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="fixed-print-header">
    <?php if (!empty($print_details[0]['print_header'])) { ?>
                        <div>
                            <img src="<?php
                            if (!empty($print_details[0]['print_header'])) {
                                echo base_url() . $print_details[0]['print_header'].img_time();
                            }
                            ?>" class="img-responsive" style="height:100px; width: 100%;">
                        </div>
    <?php } ?>
</div>    
<table class="table-print-full" width="100%">
    <thead>
        <tr>
            <td><div class="header-space">&nbsp;</div></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
      <div class="content-body">
<div class="print-area">
<div class="row">  
        <div class="col-12">
               
           <div class="card mt-1">
                <div class="card-body"> 
                    <div class="row">
                        <?php 
                            $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
                        ?> 
                <div class="box-body pb0">                   
                    <div class="col-md-12 col-lg-12 col-sm-9">
                            <table class="print-table">
                                <tbody>
                                    <tr>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <td><?php echo $case_id;?></td>                                       
                                        <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                        <td><?php if($result['appointment_date']!='' && $result['appointment_date']!='0000-00-00'){
                                            echo $this->customlib->YYYYMMDDHisTodateFormat($result['appointment_date']);
                                        } ?>
                                        </td>
                                    </tr> 
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <td><?php echo composePatientName($result['patient_name'],$result['patient_id']); ?></td>
                                        <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                        <td><?php echo $result['guardian_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <td><?php echo $result['gender']; ?></td>                                
                                        <th><?php echo $this->lang->line('age'); ?></th>
                                        <td>
                                            <?php
                                             if (!empty($result['dob'])) {
                                                echo $this->customlib->getPatientAge($result['age'],$result['month'],$result['day']);
                                             } 
                                            ?>   
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <td><?php echo $result['mobileno']; ?></td>
                                        <?php 
                                    if($result['opdid']!='' && $result['opdid']!=0){ ?>                
                                        <th ><?php echo $this->lang->line('opd_no'); ?></th>
                                        <td ><?php
                                            if($result['opdid']!='' && $result['opdid']!=0){
                                                echo $this->customlib->getSessionPrefixByType('opd_no').$result['opdid'];
                                            }                                   
                                            ?>
                                        </td>                                   
                                    <?php }  
                                    if($result['ipdid']!='' && $result['ipdid']!=0){?>                     
                                        <th><?php echo $this->lang->line('ipd_no'); ?></th>
                                        <td><?php
                                            if($result['ipdid']!='' && $result['ipdid']!=0){
                                                echo $this->customlib->getSessionPrefixByType('ipd_no').$result['ipdid'];
                                            }                                            
                                            ?>
                                        </td>                                   
                                    <?php } ?>                                   
                                </tr>
                                <tr>                            
                                    <th><?php echo $this->lang->line('admission_date');?></th>
                                    <td><?php if($result['date']!='' && $result['date']!='0000-00-00'){
                                        echo $this->customlib->YYYYMMDDHisTodateFormat($result['date']);
                                    } ?></td>
                                    <th><?php echo $this->lang->line('discharge_date'); ?></th>
                                    <td>
                                        <?php if((!empty($discharge_card)) && $discharge_card['discharge_date']!=''){ echo $this->customlib->YYYYMMDDHisTodateFormat($discharge_card['discharge_date']); }   ?>
                                    </td>
                                </tr>
                                <tr>                    
                                    <th><?php echo $this->lang->line('discharge_status'); ?></th>
                                    <td><?php  echo $this->customlib->discharge_status($discharge_card['discharge_status']);  ?></td> 
                                    <th></th>
                                    <td></td>
                                </tr> 
                                    <?php 
                                         if($discharge_card['discharge_status']==1){
                                    ?>
                                    <tr>                                    
                                        <th><?php echo $this->lang->line('death_date'); ?></th>
                                        <td><?php if((!empty($discharge_card)) && $discharge_card['death_date']!=''){ echo $this->customlib->YYYYMMDDHisTodateFormat($discharge_card['death_date']); } ?></td>                           
                                        <th ><?php echo $this->lang->line('death_report'); ?></th>
                                        <td ><?php if(!empty($deathrecord)){ echo $deathrecord['death_report'];  } ?></td>                                       
                                    </tr> 
                                    <?php
                                         }                                       
                                         if($discharge_card['discharge_status']==2){
                                    ?>
                                    <tr>                                   
                                        <th><?php echo $this->lang->line('referral_date'); ?></th>
                                        <td><?php if((!empty($discharge_card)) && $discharge_card['refer_date']!=''){ echo $this->customlib->YYYYMMDDHisTodateFormat($discharge_card['refer_date']); }   ?></td>                             
                                        <th><?php echo $this->lang->line('referral_hospital_name'); ?></th>
                                        <td><?php if((!empty($discharge_card)) && $discharge_card['refer_to_hospital']!=''){ echo $discharge_card['refer_to_hospital']; } ?></td>                                       
                                    </tr>
                                    <tr>                                   
                                        <th><?php echo $this->lang->line('reason_for_referral'); ?></th>
                                        <td><?php if((!empty($discharge_card)) && $discharge_card['reason_for_referral']!=''){ echo $discharge_card['reason_for_referral']; } ?></td>
                                    </tr>
                                <?php } ?>
                                <?php 
                                    if(!empty($discharge_card['field_data'])){
                                        foreach ($discharge_card['field_data'] as $key => $value) {
                                    	?>
                                        <tr><th><?php echo $value['name']; ?></th><td><?php echo $value['field_value']; ?></td></tr>
                                    	<?php
                                        }
                                    }
                                ?>
                                </tbody>
                            </table>                  
                            <table class="print-table" style="text-align: left; padding-top: 10px;">
                                <tbody>                                
                                    <tr>
                                        <td><b><?php echo $this->lang->line('operation'); ?></b><br><?php if(!empty($discharge_card)){ echo $discharge_card['operation'];  } ?></td>  
                                    </tr>
                                    <tr>
                                        <td><b><?php echo $this->lang->line('diagnosis'); ?></b><br><?php if(!empty($discharge_card)){ echo $discharge_card['diagnosis'];  } ?></td></tr>
                                    <tr>
                                        <td><b><?php echo $this->lang->line('investigation'); ?></b><br><?php if(!empty($discharge_card)){ echo $discharge_card['investigations'];  } ?></td>                                        
                                    </tr>
                                    <tr>
                                        <td><b><?php echo $this->lang->line('treatment_at_home'); ?></b><br><?php if(!empty($discharge_card)){ echo $discharge_card['treatment_home'];  } ?></td>
                                    </tr>
                                <tbody>
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
    </td></tr></tbody>
    <tfoot><tr><td>

<?php
                if (!empty($print_details[0]['print_footer'])) {
                    ?>
   <div class="footer-space">&nbsp;</div>
<?php
}
?>



</td></tr></tfoot>
</table>
<?php
                if (!empty($print_details[0]['print_footer'])) {
                    ?>
<div class="footer-fixed">

<?php   echo $print_details[0]['print_footer'];?>
            
</div>
<?php
}
?>