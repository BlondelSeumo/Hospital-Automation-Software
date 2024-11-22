<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs navlistscroll">
                        <li class="active"><a href="#overview" data-toggle="tab" aria-expanded="true"><i class="fa fa-th"></i> <?php echo $this->lang->line('overview'); ?></a></li>
                        <li ><a href="#activity" data-toggle="tab" aria-expanded="true"><i class="far fa-caret-square-down"></i> <?php echo $this->lang->line('visits'); ?></a></li>
                        <li><a href="#labinvestigation" data-toggle="tab" aria-expanded="true"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('lab_investigation'); ?></a></li>
                        <li><a href="#treatment_history" data-toggle="tab" aria-expanded="true"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('treatment_history'); ?></a></li>
                        <li><a href="#timeline" data-toggle="tab" aria-expanded="true"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('timeline'); ?></a></li>
                        <li><a href="#vital" data-toggle="tab" aria-expanded="true"><i class="fa fa-blood-pressure">&#xf21e;</i> <?php echo $this->lang->line('vital'); ?></a></li>
                    </ul>
					
                    <div class="impbtnview">
                    </div>
					
                    <div class="tab-content">
                         <div class="tab-pane tab-content-height active" id="overview">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 border-r">
                                    <div class="box-header border-b mb10 pl-0 pt0">
                                        <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo composePatientName($result['patient_name'],$result['id']); ?></h3>
                                    </div> 
                                       <div class="row">
                                            <div class="col-lg-3 col-md-4 col-sm-12 ptt10">                                                
                                                <?php

                                                    $image = $result['image'];
                                                    if (!empty($image)) {
                                                        $file = $result['image'];
                                                    } else {
                                                        $file = "uploads/patient_images/no_image.png";
                                                    }
                                                   
                                                    ?>
                                                    <img width="115" height="115" class="profile-user-img img-responsive img-rounded" src="<?php echo base_url(); ?><?php echo $file.img_time() ?>" >                                            
                                            </div><!--./col-lg-5-->
                                            <div class="col-lg-9 col-md-8 col-sm-12">
                                                <table class="table table-bordered mb0">
                                                   <tr>
                                                        <td class="bolds"><?php echo $this->lang->line('gender'); ?></td>
                                                        <td><?php echo $this->lang->line(strtolower($result['gender'])); ?></td>
                                                    </tr>
                                                     <tr>
                                                        <td class="bolds"><?php echo $this->lang->line('age'); ?></td>
                                                        <td><?php echo $this->customlib->getPatientAge($result['age'],$result['month'],$result['day']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bolds"><?php echo $this->lang->line('guardian_name')?></td>
                                                        <td><?php echo $result['guardian_name']; ?></td>
                                                    </tr>                                                     
                                                    <tr>
                                                        <td class="bolds"><?php echo $this->lang->line('phone'); ?></td>
                                                        <td><?php echo $result['mobileno']; ?></td>
                                                    </tr>
                                                     <?php
                                                    if (file_exists("./uploads/patient_id_card/barcodes/" . $id . ".png")) { ?>
                                                   <tr>
                                                        <td class="bolds"><?php echo $this->lang->line('barcode'); ?></td>
                                                        <td>
                                                            <a href="<?php echo base_url("./uploads/patient_id_card/barcodes/" . $id . ".png"); ?>" target="_blank">
                                                            <img src="<?php echo base_url("./uploads/patient_id_card/barcodes/" . $id . ".png"); ?>" width="100" height="30" />
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                    <?php
                                                    if (file_exists("./uploads/patient_id_card/qrcode/" . $id . ".png")) { ?>
                                                    <tr>
                                                        <td class="bolds"><?php echo $this->lang->line('qrcode'); ?></td>
                                                        <td>
                                                            <a href="<?php echo base_url("./uploads/patient_id_card/qrcode/" . $id . ".png"); ?>" target="_blank">
                                                            <img class="max-h-50" src="<?php echo base_url("./uploads/patient_id_card/qrcode/" . $id . ".png"); ?>"  width="70" height="70"  />
                                                            </a>
                                                        </td>
                                                    </tr>   
                                                    <?php }  ?>                                                    
                                                </table>
                                            </div><!--./col-lg-7-->
                                        </div><!--./row-->
                                        
                                        <?php if(!empty($patientcurrentvital)){ ?>
                                         <br/> <hr class="hr-panel-heading">
                                          <p><b><i class="fa fa-tag rtl-ms-0-7-5"></i> <?php echo $this->lang->line('current_vitals'); ?>:</b></p>                                        
                                            <div class="row">
                                            <div class="col-lg-8 col-md-8 col-sm-12">
                                                <div class="align-content-center ">
                                                    <table class="table table-bordered">                                                        
                                                    <?php  
                                                    $height=""; $weight="";
                                                      foreach($patientcurrentvital as $value){ 
                                                         $class= "label label-success";
                                                          $vitalrange=$this->lang->line('normal'); 
                                                          $bmi=""; 
                                                         $reference_range = $value['reference_range'];
                                                         
                                                         if(strpos($value['reference_range'], '-') !== false){
                                                             
                                                                $range = explode("-",$value['reference_range']);
                                                                $min_val = $range[0];
                                                                $max_val = $range[1];
                                                        
                                                            if(strpos($value['patient_range'], '-') !== false){
                                                            
                                                                $vital = explode("-",$value['patient_range']);
                                                                $min_vital = $vital[0];
                                                                $max_vital = $vital[1]; 
                                                                $vitalrange= $this->lang->line('normal');
                                                                $class= "label label-success";
                                                                if($min_vital < $min_val){
                                                                    $vitalrange = $this->lang->line('low'); 
                                                                    $class= "label label-danger";
                                                                }
                                                                if($max_vital > $max_val){
                                                                    $vitalrange = $this->lang->line('high');
                                                                    $class= "label label-danger";
                                                                }
                                                            }else{
																if($value['patient_range'] < $min_val){
                                                                    $vitalrange = $this->lang->line('low'); 
                                                                    $class= "label label-danger";
                                                                }
                                                                if($value['patient_range'] > $max_val){
                                                                    $vitalrange = $this->lang->line('high');
                                                                    $class= "label label-danger";
                                                                }
															}
                                                         }
                                                         
                                                        if( ($value['name']=='height' || $value['name']=='Height') && $value['patient_range']!="" ){
															if(strpos($value['patient_range'], '-') !== false){
																$range = explode("-",$value['patient_range']);
                                                                $height = $range[0];																 
															}else{
																$height = $value['patient_range'];
															}															
                                                        }
														
                                                        if( ($value['name']=='weight' || $value['name']=='Weight') && $value['patient_range']!="" ){ 
															if(strpos($value['patient_range'], '-') !== false){
																$range = explode("-",$value['patient_range']);
                                                                $weight = $range[0];																 
															}else{
																$weight = $value['patient_range'];
															}															
                                                        }
                                                         
														if($weight!="" && $height !="" ){															 
															$height1 = $height * 0.01;
															$bmiheight = ($height1 * $height1)  ;
															if($bmiheight > 0 ){
																$bmi = round( $weight / $bmiheight, 2 ) ;
															}else{
																$bmi = '';	
															}
                                                            $class1= "label label-success";															 
                                                        }                                                        
                                                        ?>                                                   
													
													<tr>
                                                        <td class="bolds"><?php echo $value['name']; ?></td>
                                                        <td><?php echo $value['patient_range'] ; ?> <?php echo $value['unit']; ?></td>
														<td><small class="<?php echo $class ; ?>"><?php echo $vitalrange ; ?></small></td>
                                                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['messure_date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                    </tr>
													
                                                    <?php }   ?>
                                                    <?php  if($weight!="" && $height !="" ){ ?>
                                                    <tr>
                                                        <td class="bolds"><?php echo $this->lang->line('bmi'); ?></td>
                                                        <td><small class="<?php echo $class1 ; ?>"><?php  echo $bmi ; ?></small></td>
                                                    </tr>
                                                    <?php } ?>                                                   
                                                    </table>
                                                </div>    
                                            </div>                                           
                                        </div>
                                        <?php } ?>
                                        
                                    <hr class="hr-panel-heading hr-10">
                                    <p><b><i class="fa fa-tag"></i> <?php echo $this->lang->line('known_allergies'); ?>:</b></p>  
                                    <ul>
                                    <?php 
                                        if(!empty($patientdetails['patient']['allergy'])){
                                    foreach($patientdetails['patient']['allergy'] as $row){ ?>
                                         <li><div ><?php echo $row['known_allergies']; ?></div></li>
                                    <?php } } ?>
                                </ul>
                                    <hr class="hr-panel-heading hr-10">
                                    <p><b><i class="fa fa-tag"></i> <?php echo $this->lang->line('findings'); ?>:</b></p>  
                                    <ul>
                                    <?php 
                                     if(!empty($patientdetails['patient']['findings'])){
                                    foreach($patientdetails['patient']['findings'] as $row){ ?>
                                         <li><div ><?php echo $row['finding_description']; ?></div></li>
                                    <?php } } ?>
                                </ul>
                                    <hr class="hr-panel-heading hr-10">
                                    <p><b><i class="fa fa-tag"></i> <?php echo $this->lang->line('symptoms'); ?>:</b></p> 
                                     <ul> 
                                    <?php if(!empty($patientdetails['patient']['symptoms'])){
                                    foreach($patientdetails['patient']['symptoms'] as $row){ ?>
                                          <li><div ><?php echo $row['symptoms']; ?></div></li>
                                    <?php } } ?>
                                </ul>
                                <?php  ?>                                
                                    <hr class="hr-panel-heading hr-10"> 
                                    <div class="box-header mb10 pl-0">
                                        <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('consultant_doctor'); ?></h3>
                                        <div class="pull-right">
                                            <div class="editviewdelete-icon pt8">
                                               
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="staff-members">
                                        <?php 
                                        if(!empty($patientdetails['patient']['doctor'])){
                                        foreach($patientdetails['patient']['doctor'] as $value ){  ?>
                                            <div class="media">
                                                <div class="media-left">
                                                    <?php if($value['image']!=""){ ?>
                                                        <a href="#">
                                                        <img src="<?php echo base_url("uploads/staff_images/".$value['image'].img_time()); ?>" class="member-profile-small media-object"></a>
                                                    <?php }else{ ?>
                                                          <img src="<?php echo base_url("uploads/staff_images/no_image.png".img_time()) ?>" class="member-profile-small media-object"></a>
                                                    <?php } ?>                                                     
                                                </div>
                                                <div class="media-body">
                                                    <a href="#" class="pull-right text-danger pt4" data-toggle="tooltip" data-placement="top" ></a>
                                                    <h5 class="media-heading"><a href="#"><?php echo $value["name"] . " " . $value["surname"]."  (".$value["employee_id"].")" ?></a>
                                                        
                                                    </h5>
                                                </div>
                                            </div><!--./media-->
                                             <?php } } ?>
                                     </div><!--./staff-members-->
                                      <hr class="hr-panel-heading hr-10"> 
                                    <div class="box-header mb10 pl-0">
                                        <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('timeline'); ?></h3>
                                        <div class="pull-right">
                                            <div class="editviewdelete-icon pt8">
                                                <a href="#" data-toggle="tooltip" data-placement="top" title="add-edit-members"></a>
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="staff-members">                                      

                            <div class="timeline-header no-border">
                                <div id="timeline_list">
                                    <?php
                                    if (empty($timeline_list)) {
                                        ?>       
                                        <br/>
                                    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                    <?php } else {
                                       ?>
                                        <ul class="timeline timeline-inverse">
                                            <?php
                                            $i=0;
                                            foreach ($timeline_list as $key => $value) {

                                                ++$i;
                                                if($i<=5){

                                                    ?>
                                                <li class="time-label">
                                                    <span class="bg-blue"> <?php echo $this->customlib->YYYYMMDDTodateFormat($value['timeline_date']);
                                              ?></span>
                                                </li>
                                                <li>
                                                    <i class="fa fa-list-alt bg-blue"></i>
                                                    <div class="timeline-item">
                                                        <?php if (!empty($value["document"])) {?>
                                                            <span class="time"><a class="defaults-c text-right" data-toggle="tooltip" title="" href="<?php echo base_url() . "patient/dashboard/download_patient_timeline/" . $value["id"] . "/" . $value["document"] ?>" data-original-title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a></span>
        <?php }?>
                                                        <h3 class="timeline-header text-aqua"> <?php echo $value['title']; ?> </h3>
                                                        <div class="timeline-body">
        <?php echo $value['description']; ?>

                                                        </div>
                                                    </div>
                                                </li>
                                            <?php }  }?>
                                            <li><i class="fa fa-clock-o bg-gray"></i></li>
<?php }?>

                                    </ul>
                                </div>
                            </div>
                                     </div><!--./staff-members-->
                                </div><!--./col-lg-6-->
                                <div class="col-lg-6 col-md-6 col-sm-12">                                       
                                       <div class="">
                                            <div class="">  
                                    <div class="box-header mb10 pl-0">
                                        <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('visit_details'); ?></h3>
                                        <div class="pull-right">
                                            <div class="editviewdelete-icon pt8">
                                                <a href="#" data-toggle="tooltip" data-placement="top" title="add-edit-members"></a>
                                            </div>  
                                        </div>
                                    </div> 
                                    <div class="staff-members">
                                        <div class="table-responsive">
                                            <?php 
                                                        if(!empty($patientdetails['patient']['visitdetails'])){ ?>
                                            <table class="table table-striped table-bordered table-hover mb0"  data-export-title="<?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?>">
                                                    <thead>
                                                        <th><?php echo $this->lang->line('opd_no'); ?></th>
                                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                                        <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                                        <th><?php echo $this->lang->line('consultant'); ?></th>
                                                        <th><?php echo $this->lang->line('reference'); ?></th>
                                                        <th><?php echo $this->lang->line('symptoms'); ?></th>                                                   
                                                    </thead>
                                                    <tbody>
                                                        <?php                                                         
                                                        foreach($patientdetails['patient']['visitdetails'] as $value) {
                                                              $opd_id = $this->customlib->getPatientSessionPrefixByType('opd_no') . $value['opd_id'];
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $opd_id ; ?></td>
                                                            <td><?php echo $value['case_reference_id']; ?></td>
                                                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['appointment_date'], $timeformat); ?></td>
                                                            <td><?php echo composeStaffNameByString($value['name'], $value['surname'], $value['employee_id']); ?></td>
                                                            <td><?php if($value['refference']){ echo  nl2br($value['refference']); } ?></td>
                                                            <td><?php if($value['symptoms']){ echo  nl2br($value['symptoms']); } ?></td>
                                                        </tr>
                                                    <?php }   ?>                                                        
                                                    </tbody>
                                            </table>
                                              <?php }   ?>
                                        </div> 
                                    </div><!--./staff-members-->
                                            </div> 
                                        </div>      

                                        <!--- lab investigation--->  

                                        <div class="">
                                            <div class="">    
                                             
                                            <div class="box-header mb10 pl-0">
                                                <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('lab_investigation'); ?></h3>
                                                <div class="pull-right">
                                                    <div class="editviewdelete-icon pt8">
                                                        <a href="#" data-toggle="tooltip" data-placement="top" title="add-edit-members"></a>
                                                    </div>  
                                                </div>
                                            </div>
											
                                                <div class="staff-members">
                                                    <div class="table-responsive">
                                                        <?php if(!empty($investigations)) { ?>
                                                        <table class="table table-striped table-bordered table-hover mb0">
                                                            <thead>
                                                                <th><?php echo $this->lang->line('test_name'); ?></th>
                                                                <th><?php echo $this->lang->line('case_id'); ?></th>
                                                                <th><?php echo $this->lang->line('lab'); ?></th>
                                                                <th><?php echo $this->lang->line('sample_collected'); ?></th>
                                                                <th><?php echo $this->lang->line('expected_date'); ?></th>
                                                                <th><?php echo $this->lang->line('approved_by'); ?></th>                                                              
                                                            </thead>
                                                            <tbody id="">
                                                                <?php $i=1;
                                                                foreach($investigations as $row ){ 
                                                                     if($i <= $recent_record_count){
                                                                        ++$i;
                                                                    ?>
                                                                <tr>
                                                                    <td><?php echo $row['test_name']; ?><br/>
                                                                    <?php echo "(".$row['short_name'].")"; ?></td>
                                                                    <td><?php echo $row['case_reference_id']; ?></td>
                                                                    <td><?php echo $this->lang->line($row['type']); ?></td>
                                                                    <td><label><?php echo composeStaffNameByString($row['collection_specialist_staff_name'],$row['collection_specialist_staff_surname'],$row['collection_specialist_staff_employee_id']); ?></label><br/>
                                                                        <?php  echo $row['test_center']; ?>
                                                                    <br/>
                                                                     <?php if($row['collection_date']){ echo $this->customlib->YYYYMMDDTodateFormat($row['collection_date']); } ?></td>                                                                       
                                                                    <td>
                                                                    <?php
                                                                    if($row['reporting_date']){
                                                                    echo  $this->customlib->YYYYMMDDTodateFormat($row['reporting_date']); }?>
                                                                    </td>
                                                                    <td class="text-left">
                                                                        <?php      
                                                                    echo composeStaffNameByString($row['approved_by_staff_name'],$row['approved_by_staff_surname'],$row['approved_by_staff_employee_id']);
                                                                     ?>
                                                                     <br/>
                                                                    <?php  if($row['parameter_update']){                              
                                                                    echo  $this->customlib->YYYYMMDDTodateFormat($row['parameter_update']);
                                                                    }
                                                                     ?>                                         
                                                                    </td>                                                                   
                                                                    </tr>
                                                                     <?php } } ?>
                                                                    </tbody>
                                                                </table>
                                                            <?php } ?>
                                                    </div> 
                                                </div><!--./staff-members-->
                                            </div> 
                                        </div>       
                                         <!--- end lab investigation--->      

                                        <div class="">
                                            <div >   
                                            
                                    <div class="box-header mb10 pl-0">
                                        <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('treatment_history'); ?></h3>
                                        <div class="pull-right">
                                            <div class="editviewdelete-icon pt8">
                                                <a href="#" data-toggle="tooltip" data-placement="top" title="add-edit-members"></a>
                                            </div>  
                                        </div>
                                    </div>

                                    <div class="staff-members">
                                        <div class="table-responsive">
                                            <?php if(!empty($patientdetails['patient']['history'])){ ?>
                                            <table class="table table-striped table-bordered table-hover mb0"  data-export-title="<?php echo composePatientName($result['patient_name'],$result['id']) . " " . $this->lang->line('opd_details'); ?>">
                                                    <thead>
                                                        <th><?php echo $this->lang->line('opd_no'); ?></th>
                                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                                        <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                                        <th><?php echo $this->lang->line('consultant'); ?></th>
                                                        <th><?php echo $this->lang->line('symptoms'); ?></th>                                                   
                                                    </thead>
                                                    <tbody>
                                                        <?php                                                       
                                                        foreach($patientdetails['patient']['history'] as $value) {
                                                            $opd_id = $this->customlib->getPatientSessionPrefixByType('opd_no') . $value['opd_id'];
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $opd_id ; ?></td>
                                                            <td><?php echo $value['case_reference_id']; ?></td>
                                                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['appointment_date'], $timeformat); ?></td>
                                                            <td><?php echo composeStaffNameByString($value['name'], $value['surname'], $value['employee_id']); ?></td>
                                                            <td><?php if($value['symptoms']){ echo  nl2br($value['symptoms']); } ?></td>
                                                        </tr>
                                                    <?php }   ?>                                                        
                                                    </tbody>
                                            </table>
                                              <?php }   ?>
                                        </div> 
                                    </div><!--./staff-members-->
                                            </div> 
                                        </div>                                         
                                    </div><!--./col-lg-6-->
                            </div><!--./row-->  
                        </div><!--#/overview-->
                        <div class="tab-pane " id="activity">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('visits'); ?></h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example mb0" cellspacing="0" width="100%">
                                    <thead>
                                      <tr class="white-space-nowrap">
                                        <th><?php echo $this->lang->line('opd_no'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                        <th><?php echo $this->lang->line('consultant'); ?></th>
                                        <th><?php echo $this->lang->line('reference'); ?></th>
                                        <th><?php echo $this->lang->line('symptoms'); ?></th>
                                        <?php 
                                            if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                        ?>
                                          <th><?php echo $fields_value->name; ?></th>
                                        <?php
                                          } } ?>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action') ?></th>
                                      </tr>  
                                    </thead>
                                    <tbody>
                                        <?php
                                if (!empty($opd_details)) {
                                    foreach ($opd_details as $key => $value) {
                                                                        
                                        ?>
                                                <tr>
                                                    <td><a href="<?php echo base_url() . "patient/dashboard/visitdetails/" . $value["opdid"] ?>"><?php echo $this->customlib->getPatientSessionPrefixByType("opd_no").$value["opdid"]; ?></a>
                                                    </td>
                                                    <td><?php echo $value['case_reference_id']; ?></td>
                                                    <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($value['appointment_date'])); ?></td>
                                                    <td>
                                                        <?php 
                                                        echo composeStaffNameByString($value['name'], $value['surname'], $value['employee_id']);
                                                        ?>                                                            
                                                        </td>
                                                    <td><?php echo $value['refference']; ?></td>
                                                    <td><?php if($value['symptoms']){ echo nl2br($value['symptoms']); }?></td>                                                   
                                                         <?php  if (!empty($fields)) {
                                                            foreach ($fields as $fields_key => $fields_value) {
                                                                $display_field = $value["$fields_value->name"];
                                                                    if ($fields_value->type == "link") {
                                                                        $display_field = "<a href=" . $value["$fields_value->name"] . " target='_blank'>" . $value["$fields_value->name"] . "</a>";
                                                            }
                                                        ?>
                                                                <td><?php echo $display_field?></td>

                                                        <?php  } } ?>
                                                    <td class="pull-right white-space-nowrap">
                                                        <a href='javascript:void(0)' data-loading-text='<i class=\"fa fa-circle-o-notch fa-spin\"></i>' data-opd-id=" <?php echo $value["opdid"]?> "  data-record-id="<?php echo $value["id"] ?>" class='btn btn-default btn-xs print_visit_bill' title="<?php echo $this->lang->line('print_bill'); ?>"  data-toggle='tooltip' title=''><i class='fa fa-print'></i></a>

                                                        <?php if ($value["prescription"] == 'yes') { ?>
                                                            
                                                                <span data-toggle="modal" data-target="#prescriptionview">
                                                                    <a href="#" class="btn btn-default btn-xs" data-toggle='tooltip' onclick="view_prescription('<?php echo $value["visit_id"] ?>')" title="<?php echo $this->lang->line('view_prescription'); ?>">
                                                                        <i class="fas fa-file-prescription"></i>
                                                                    </a></span>
                                                                    <?php
                                                    } ?>
                                                             
                                                        <a href="#" class="btn btn-default btn-xs get_opd_detail" data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>"  data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>"  data-record_id="<?php echo $value["id"]; ?>" >
                                                            <i class="fa fa-reorder"></i>
                                                        </a>

                                                    </td>
                                                </tr>
                                                <?php                                                   
                                                    }
                                                    }
                                                    ?>
                                       </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Diagnosis -->
                        <div class="tab-pane" id="labinvestigation">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('lab_investigation'); ?></h3>
                            </div>
                            <div class="table-responsive overflow-visible">
                                 <table class="table table-striped table-bordered table-hover example mb0" data-export-title="<?php echo $this->lang->line('opd_details'); ?>">
                                    <thead>
                                        <th><?php echo $this->lang->line('test_name'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('lab'); ?></th>
                                        <th><?php echo $this->lang->line('sample_collected'); ?></th>
                                        <th><?php echo $this->lang->line('expected_date'); ?></th>
                                        <th><?php echo $this->lang->line('approved_by'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody id="">
                                        <?php foreach($investigations as $row ){ ?>
                                        <tr>
                                            <td><?php echo $row['test_name']; ?><br/>
                                            <?php echo "(".$row['short_name'].")"; ?></td>
                                            <td><?php echo $row['case_reference_id']; ?></td>
                                            <td><?php echo $this->lang->line($row['type']); ?></td>
                                            <td><label><?php echo composeStaffNameByString($row['collection_specialist_staff_name'],$row['collection_specialist_staff_surname'],$row['collection_specialist_staff_employee_id']); ?></label><br/>                                    
                                        <?php
                                   echo $row['test_center']; 
                                    ?>
                                    <br/>
                                     <?php if($row['collection_date']){
                                     echo $this->customlib->YYYYMMDDTodateFormat($row['collection_date']); 
                                     } ?>
                                    </td>                                       
                                    <td><?php  echo  $this->customlib->YYYYMMDDTodateFormat($row['reporting_date']); ?></td>
                                    <td class="text-left"><?php echo composeStaffNameByString($row['approved_by_staff_name'],$row['approved_by_staff_surname'],$row['approved_by_staff_employee_id']); ?>
                                    <br/>
                                    <?php  if($row['parameter_update']){ echo  $this->customlib->YYYYMMDDTodateFormat($row['parameter_update']);  } ?>                                      
                                    </td>
                                    <td class="text-right"><a href='javascript:void(0)'  data-loading-text='<i class="fa fa-reorder"></i>' data-record-id='<?php echo $row['report_id'];?>' data-type-id='<?php echo $row['type'];?>' data-test-id='<?php echo $row['test_name']. " (".$row['short_name'].")"; ?>'  class='btn btn-default btn-xs view_report' data-toggle='tooltip' title='<?php echo $this->lang->line("show"); ?>'><i class='fa fa-reorder'></i></a></td>
                                    </tr>
                                     <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="treatment_history">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('treatment_history'); ?></h3>
                            </div>
                            <div class="impbtnview20">                                
                            </div>                            
                            <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['id']). " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive overflow-visible">
                                <table class="table table-striped table-bordered table-hover treatmentlist mb0" data-export-title="<?php echo $this->lang->line('treatment_history'); ?>">
                                        <thead>
                                            <th><?php echo $this->lang->line('opd_no'); ?></th>
                                            <th><?php echo $this->lang->line('case_id'); ?></th>
                                            <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                            <th><?php echo $this->lang->line('symptoms'); ?></th>
                                            <th><?php echo $this->lang->line('consultant'); ?></th>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action') ?></th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                            </div> 
                        </div> 
                        <!-- Timeline -->
                        <div class="tab-pane" id="timeline">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('timeline'); ?></h3>
                            </div>
                            <div class="timeline-header no-border">
                                <div id="timeline_list">
                                    <?php
                                    if (empty($timeline_list)) {
                                        ?>       
                                        <br/>
                                    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                    <?php } else {
                                       ?>
                                        <ul class="timeline timeline-inverse">
                                            <?php
                                            foreach ($timeline_list as $key => $value) {
                                                    ?>
                                                <li class="time-label">
                                                    <span class="bg-blue"> <?php 
                                                    echo  date($this->customlib->getHospitalDateFormat(true, true), strtotime($value['timeline_date'])); 
                                              ?></span>
                                                </li>
                                                <li>
                                                    <i class="fa fa-list-alt bg-blue"></i>
                                                    <div class="timeline-item">

                                                        <?php if (!empty($value["document"])) {?>
                                                            <span class="time"><a class="defaults-c text-right" data-toggle="tooltip" title="" href="<?php echo base_url() . "patient/dashboard/download_patient_timeline/" . $value["id"] . "/" . $value["document"] ?>" data-original-title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a></span>
        <?php }?>
                                                        <h3 class="timeline-header text-aqua"> <?php echo $value['title']; ?> </h3>
                                                        <div class="timeline-body">
        <?php echo $value['description']; ?>

                                                        </div>
                                                    </div>
                                                </li>
                                            <?php }?>
                                            <li><i class="fa fa-clock-o bg-gray"></i></li>
<?php }?>

                                    </ul>
                                </div>
                            </div>                       
						</div> 
                        <!-- Vital -->
                        <div class="tab-pane" id="vital">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('vitals'); ?></h3>
                            </div>
                            <div class="timeline-header no-border">
                                <div id="vital_list">
                                    <?php
                                    if (empty($patient_vital_date)) {
                                        ?>
                                        <br/>
                                        <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                    <?php } else {   ?>
										<div class="table_inner"> 
											<table class="table table-striped table-bordered ">												 
												<thead>
													<tr>
														<th><?php echo $this->lang->line("date"); ?> </th>													
														<?php foreach($vital_list as $vl){ ?>
														<th ><?php echo $vl["name"]; ?> <br> (<?php echo $vl["reference_range"]; ?> <?php echo $vl["unit"]; ?>) </th>
														<?php } ?>
													</tr>
												</thead>
												<tbody>													 
													<?php foreach($patient_vital_date as $pvd){ ?>
													<tr>
														<th><?php echo $date = $this->customlib->YYYYMMDDTodateFormat($pvd['messure_date']);
															$messure_date = date('Y-m-d', strtotime($pvd['messure_date']));
														?></th>														
														<?php foreach($vital_list as $vl){ 														
															$id = $vl["id"]; 														 
															  ?>														
																<td class="tablehovericon">
                                                                    <div class="relative">
																	<?php 
																	foreach($patientvital[$messure_date][$id] as $pmi){
																		
																		echo $pmi['patient_range'];																		 
																		$datetime = $this->customlib->YYYYMMDDHisTodateFormat($pmi['messure_date'],$this->customlib->getHospitalTimeFormat()); 
																		$str2 = substr($datetime, 10);
																		echo  " (".$str2 .")";																		
																		echo "<br>";																		
																	} 
																	?>	
																	</div>																
																</td>															 
														<?php }  ?>															
													</tr>
													<?php } ?>											
													 
												</tbody>								   
											</table>
										</div>
									<?php } ?> 
                                </div>
                            </div>                       
						</div>					
                </div>
            </div>
        </div>
    </section>
</div>

<div id="modal-chkstatus" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
    <form id="form-chkstatus" action="" method="POST">
        <div class="modal-content">
            <div class="">
                <button type="button" class="close modalclosezoom" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="zoom_details">

            </div>
        </div>
    </form>
    </div>
</div>

<div class="modal fade" id="prescriptionview" tabindex="-1" role="dialog" aria-labelledby="follow_up">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deleteprescription'>
                        <a href="#" id='print_id' data-toggle="modal"><i class="fa fa-print"></i></a>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('prescription'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0" id="getdetails_prescription">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('visit_details'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="view" accept-charset="utf-8" method="get">
                            <div class="table-responsive">
                                <table class="table mb0 table-striped table-bordered examples">
                                    <tr>                                       
                                        <th width="15%"><?php echo $this->lang->line('name'); ?></th>
                                        <td width="35%"><span id="patient_name"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="15%"><?php echo $this->lang->line('gender'); ?></th>
                                        <td width="35%"><span id='gender'></span></td>
                                        <th width="15%"><?php echo $this->lang->line('age'); ?></th>
                                        <td width="35%"><span id="age"></span><span id="month"></span></td>
                                    </tr>                                   
                                    <tr>
                                        <th width="15%"><?php echo $this->lang->line('email'); ?></th>
                                        <td width="35%"><span id='email'></span></td>
                                        <th width="15%"><?php echo $this->lang->line('appointment_date'); ?></th>
                                        <td width="35%"><span id="appointment_date"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="15%"><?php echo $this->lang->line('symptoms'); ?></th>
                                        <td width="35%"><span id='symptoms'></span></td>                                       
                                    </tr>
                                    <tr>
                                        <th width="15%"><?php echo $this->lang->line('case'); ?></th>
                                        <td width="35%"><span id='case'></span></td>
                                        <th width="15%"><?php echo $this->lang->line('casualty'); ?></th>
                                        <td width="35%"><span id="casualty"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="15%"><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                        <td width="35%"><span id='cons_doctor'></span></td>
                                        <th width="15%"><?php echo $this->lang->line('reference'); ?></th>
                                        <td width="35%"><span id="refference"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="15%"><?php echo $this->lang->line('payment_mode'); ?></th>
                                        <td width="35%"><span id="payment_mode"></span></td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </div><!--./col-md-12-->
                </div><!--./row-->
            </div>
        </div>
    </div>
</div>

<!--lab investigation modal-->
<div class="modal fade" id="viewDetailReportModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='action_detail_report_modal'>

                   </div>
                </div>
                <h4 class="modal-title" id="modal_head"></h4> 
            </div>
            <div class="modal-body ptt10 pb0">
                <div id="reportbilldata"></div>
            </div>
        </div>
    </div>    
</div>

<!-- end lab investigation modal-->
<script type="text/javascript">

     function view_prescription(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/prescription/getPrescription/' + visitid ,
            success: function (res) {
                $("#edit_deleteprescription").html("<a href='#' onclick='print(" + visitid + ")' id='print_id' data-toggle='modal' title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>");
                $("#getdetails_prescription").html(res);

                holdModal('prescriptionview');
            },
            error: function () {
                alert("Fail")
            }
        });
    }

    function getRecord(id, visitid) {
      
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getDetails',
            type: "POST",
            data: {patient_id: id, visitid: visitid},
            dataType: 'json',
            success: function (data) {
                $("#patient_name").html(data.patient_name);
                $("#gender").html(data.gender);
                $("#casualty").html(data.casualty);
                $("#contact").html(data.mobileno);
                $("#email").html(data.email);
                $("#age").html(data.age);
                $("#guardian_name").html(data.guardian_name);
                $("#appointment_date").html(data.appointment_date);
                $("#case").html(data.case_type);
                $("#symptoms").html(data.symptoms);
                $("#known_allergies").html(data.known_allergies);
                $("#refference").html(data.refference);
                $("#cons_doctor").html(data.doctor_name);
                $("#amount").html(data.amount);
                $("#tax").html(data.tax);
                $("#payment_mode").html(data.payment_mode);
                $("#opdid").val(data.opdid);
                $("#address").val(data.address);
                $("#note").val(data.note);
                $("#updateid").val(id);
                holdModal('viewModal');
            },
        });
    }
	
    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

		$('#modal-chkstatus').on('shown.bs.modal', function (e) {
            var $modalDiv = $(e.delegateTarget);
              var id=$(e.relatedTarget).data('id');
            $.ajax({
                type: "POST",
                url: '<?php echo site_url("patient/dashboard/getlivestatus") ?>',
                data: {'id':id},
                dataType: "JSON",
                beforeSend: function () {
                $('#zoom_details').html("");
                    $modalDiv.addClass('modal_loading');
                },
                success: function (data) {
                   $('#zoom_details').html(data.page);
                    $modalDiv.removeClass('modal_loading');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $modalDiv.removeClass('modal_loading');
                },
                complete: function (data) {
                    $modalDiv.removeClass('modal_loading');
                }
            });
        })     

    $('#print_id').show();
    function print(id, opdid) {
        var base_url = '<?php echo base_url() ?>';      
        
        $.ajax({
            url: base_url + 'patient/prescription/getPrescription/' + id + '/' + opdid,
            type: 'POST',
            data: {payslipid: id,print:'print'},
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }
    
    $(document).ready(function (e) {
        $('#prescriptionview,#viewModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });

    $(document).on('click','.get_opd_detail',function(){

    var visitid=$(this).data('record_id');
    var $this = $(this);
     
     $.ajax({
                url: base_url+'patient/dashboard/getopdDetails',
                type: "POST",
                data: {visit_id: visitid},
                dataType: 'json',
                   beforeSend: function() {
                  $this.button('loading');
                   },
                success: function (data) {
               
                  $('#viewModal .modal-body').html(data.page);
                  $('#viewModal').modal('show');

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
    
</script>
<script>
     $(document).on('click','.view_report',function(){
         var id=$(this).data('recordId');
         var lab=$(this).data('typeId');
         var test = $(this).data('testId');
         getinvestigationparameter(id,$(this),lab,test);
       });

        function getinvestigationparameter(id,btn_obj,lab,test){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({
            url: base_url+'patient/dashboard/getinvestigationparameter',
            type: "POST",
            data: {'id': id,'lab':lab},
            dataType: 'json',
            beforeSend: function() {
              $this.button('loading');
                modal_view.addClass('modal_loading');
                
               },
            success: function (data) {                      
             $('#viewDetailReportModal .modal-body').html(data.page);  
             $('#viewDetailReportModal #action_detail_report_modal').html(data.actions);  
             $('#viewDetailReportModal #modal_head').html(test);  
             $('#viewDetailReportModal').modal({backdrop:'static'});
              modal_view.removeClass('modal_loading');
            },

             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.button('reset');
                modal_view.removeClass('modal_loading');
           },
           complete: function() {
            $this.button('reset');
                modal_view.removeClass('modal_loading');
          
           }
        });  
        }
</script>
<script type="text/javascript">
    $(document).on('click','.print_bill',function(){
    var id=$(this).data('recordId');
      
        var $this = $(this);
        var lab   = $(this).data('typeId');
        $.ajax({
            url: base_url+'patient/dashboard/printpathoparameter',
            type: "POST",
            data: {'id': id,'lab':lab},
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
</script>

<script type="text/javascript">
     $(document).ready(function () {       
           $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable()
               .columns.adjust()
               .responsive.recalc();
            });
      });     
</script>

<script type="text/javascript">
( function ( $ ) {
    var id = "<?php echo $result['id']; ?>";
    'use strict';
    $(document).ready(function () {     
        initDatatable('treatmentlist','patient/dashboard/getopdtreatmenthistory/'+ id);      
    });

    $(document).on('click','.print_visit_bill',function(){       
    var opd_id=$(this).data('opdId');   
           var $this = $(this);     
     $.ajax({
                url: base_url+'patient/dashboard/printbill',
                type: "POST",
                data: {opd_id: opd_id},
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

} ( jQuery ) );

</script>