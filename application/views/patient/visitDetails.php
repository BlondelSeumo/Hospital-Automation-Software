<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
$total=0;
$total_payment=0;
?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs navlistscroll">
                        <li class="active"><a href="#overview"><i class="fa fa-th"></i> <?php echo $this->lang->line('overview'); ?></a></li>
                        <li><a href="#activity" data-toggle="tab" aria-expanded="true"><i class="far fa-caret-square-down"></i> <?php echo $this->lang->line('visits'); ?></a></li>
                        <li><a href="#medication" data-toggle="tab" aria-expanded="true"><i class="fa fa-medkit"></i> <?php echo $this->lang->line('medication'); ?></a></li>
                        <li><a href="#labinvestigation" data-toggle="tab" aria-expanded="true"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('lab_investigation'); ?></a></li>
                        <li><a href="#operationtheatre" data-toggle="tab" aria-expanded="true"><i class="fas fa-cut"></i> <?php echo $this->lang->line('operations'); ?></a></li>
                        <li><a href="#charges" data-toggle="tab" aria-expanded="true"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('charges'); ?></a></li>
                        <li><a href="#payment" data-toggle="tab" aria-expanded="true"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('payment'); ?></a></li>
                        <li><a href="#live_consult" data-toggle="tab" aria-expanded="true"><i class="fa fa-video-camera ftlayer"></i> <?php echo $this->lang->line('live_consultation'); ?></a></li>
                        <li><a href="#timeline" data-toggle="tab" aria-expanded="true"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('timeline'); ?></a></li>                        
						<li><a href="#vitals" data-toggle="tab" aria-expanded="true"><i class="fa fa-blood-pressure">&#xf21e;</i> <?php echo $this->lang->line('vitals'); ?></a></li>             
                    </ul>
                    <div class="tab-content">
                         <div class="tab-pane tab-content-height active" id="overview">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 border-r">
                                    <div class="box-header border-b mb10 pl-0 pt0">
                                        <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo composePatientName($result['patient_name'],$result['pid']); ?></h3>
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
                                                    if (file_exists("./uploads/patient_id_card/barcodes/" . $patient_id . ".png")) { ?>
                                                   <tr>
                                                        <td class="bolds"><?php echo $this->lang->line('barcode'); ?></td>
                                                        <td>
                                                            <a href="<?php echo base_url("./uploads/patient_id_card/barcodes/" . $patient_id . ".png"); ?>" target="_blank">
                                                            <img src="<?php echo base_url("./uploads/patient_id_card/barcodes/" . $patient_id . ".png"); ?>" width="100" height="30" />
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                    <?php
                                                    if (file_exists("./uploads/patient_id_card/qrcode/" . $patient_id . ".png")) { ?>
                                                    <tr>
                                                        <td class="bolds"><?php echo $this->lang->line('qrcode'); ?></td>
                                                        <td>
                                                            <a href="<?php echo base_url("./uploads/patient_id_card/qrcode/" . $patient_id . ".png"); ?>" target="_blank">
                                                            <img class="max-h-50" src="<?php echo base_url("./uploads/patient_id_card/qrcode/" . $patient_id . ".png"); ?>"  width="70" height="70"  />
                                                            </a>
                                                        </td>
                                                    </tr>   
                                                    <?php }  ?> 
                                                </table>
                                            </div><!--./col-lg-7-->
                                        </div><!--./row-->

                                        <hr class="hr-panel-heading hr-10">
                                            <div class="row">
                                            <div class="col-lg-8 col-md-8 col-sm-12">
                                                <div class="align-content-center pt25">
                                                    <table class="table table-bordered">
                                                         <tr>
                                                            <td class="bolds"><?php echo $this->lang->line('case_id') ?></td>
                                                             <td><?php echo $result['case_reference_id']?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bolds"><?php echo $this->lang->line('opd_no'); ?></td>
                                                             <td><?php echo $this->customlib->getPatientSessionPrefixByType('opd_no').$opd_details_id; ?></td>
                                                        </tr>
                                                    </table>
                                                </div>    
                                            </div>
                                        </div>
                                        
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
                                                        <td ><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['messure_date'],$this->customlib->getHospitalTimeFormat()); ?>
                                                        </td>
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
                                    <div class="box-tab-header">
                                        <h3 class="box-tab-title"><?php echo $this->lang->line('timeline'); ?></h3>
                                    </div>
                                    <div class="timeline-header no-border">
                                        <div id="timeline_list">
                                            <?php
                                        if (empty($timeline_list)) {
                                                ?>
                                               
                                            <?php } else {
        ?>
                                                <ul class="timeline timeline-inverse">
                                                    <?php $i=0 ;
                                                        foreach ($timeline_list as $key => $value) {

                                                             ++$i;
                                                            if($i <= $recent_record_count)
                                                            {
                                                            ?>
                                                        <li class="time-label">
                                                            <span class="bg-blue">    <?php if($value['timeline_date']){ echo $this->customlib->YYYYMMDDTodateFormat($value['timeline_date']); } ?>
                                                            </span>
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
                                                    <?php } } ?>
                                                    <li><i class="fa fa-clock-o bg-gray"></i></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>

                                </div><!--./col-lg-6-->
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                         <div class="row">
											<?php if ($this->module_lib->hasActive('opd')) { if ($this->module_lib->hasPatientActive('opd')) { ?>
                                            <div class="col-md-6 project-progress-bars">
                                                <div class="row">
                                                    <div class="col-md-12 mtop5">
                                                       <div class="topprograssstart">
                                                            <h5 class="text-uppercase mt5 bolds"><?php echo $this->lang->line('opd_billing_payment_graph'); ?>
                                                            </h5>
                                                            <p class="text-muted bolds"><?php echo $graph['opd']['opd_bill_payment_ratio'];?>%<span class="pull-right"> <?php echo $this->customlib->get_payment_bill($graph['opd']['payment']['total_payment'],$graph['opd']['bill']['total_bill']);?></span></p>
                                                            <div class="progress-group">
                                                                <div class="progress progress-minibar">
                                                                    <div class="progress-bar progress-bar-aqua" style="width: <?php echo $graph['opd']['opd_bill_payment_ratio'];?>%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!--./row-->
                                           </div><!--./col-lg-6-->
										   <?php } } 
										   if ($this->module_lib->hasActive('pharmacy')) {  if ($this->module_lib->hasPatientActive('pharmacy')) { ?>
                                           <div class="col-md-6 project-progress-bars">
                                                <div class="row">
                                                    <div class="col-md-12 mtop5">
                                                        <div class="topprograssstart">
                                                            <h5 class="text-uppercase mt5 bolds"><?php echo $this->lang->line('pharmacy_billing_payment_graph'); ?>
                                                            </h5>
                                                            <p class="text-muted bolds"><?php echo $graph['pharmacy']['pharmacy_bill_payment_ratio'];?>%<span class="pull-right"> <?php echo $this->customlib->get_payment_bill(($graph['pharmacy']['payment']['total_payment']-$graph['pharmacy']['payment_refund']['total_payment']),$graph['pharmacy']['bill']['total_bill']);?></span></p>
                                                            <div class="progress-group">
                                                                <div class="progress progress-minibar">
                                                                    <div class="progress-bar progress-bar-aqua" style="width: <?php echo $graph['pharmacy']['pharmacy_bill_payment_ratio'];?>%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>    
                                           </div><!--./col-lg-6-->
										   <?php } } ?>										   
                                        </div><!--./row-->										
										
                                        <div class="row">
											<?php if ($this->module_lib->hasActive('pathology')) { if ($this->module_lib->hasPatientActive('pathology')) { ?>
                                            <div class="col-md-6 project-progress-bars">
                                                <div class="row">
                                                    <div class="col-md-12 mtop5">
                                                       <div class="topprograssstart">
                                                            <h5 class="text-uppercase mt5 bolds"><?php echo $this->lang->line('pathology_billing_payment_graph'); ?>
                                                            </h5>
                                                            <p class="text-muted bolds"><?php echo $graph['pathology']['pathology_bill_payment_ratio'];?>%<span class="pull-right"> <?php echo $this->customlib->get_payment_bill($graph['pathology']['payment']['total_payment'],$graph['pathology']['bill']['total_bill']);?></span></p>
                                                            <div class="progress-group">
                                                                <div class="progress progress-minibar">
                                                                    <div class="progress-bar progress-bar-aqua" style="width: <?php echo $graph['pathology']['pathology_bill_payment_ratio'];?>%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!--./row-->
                                           </div><!--./col-lg-6-->
										   <?php } } 
										   if ($this->module_lib->hasActive('radiology')) {  if ($this->module_lib->hasPatientActive('radiology')) { ?>
                                           <div class="col-md-6 project-progress-bars">
                                                <div class="row">
                                                    <div class="col-md-12 mtop5">
                                                        <div class="topprograssstart">
                                                            <h5 class="text-uppercase mt5 bolds"><?php echo $this->lang->line('radiology_billing_payment_graph'); ?>
                                                            </h5>
                                                            <p class="text-muted bolds"><?php echo $graph['radiology']['radiology_bill_payment_ratio'];?>%<span class="pull-right"> <?php echo $this->customlib->get_payment_bill($graph['radiology']['payment']['total_payment'],$graph['radiology']['bill']['total_bill']);?></span></p>
                                                            <div class="progress-group">
                                                                <div class="progress progress-minibar">
                                                                    <div class="progress-bar progress-bar-aqua" style="width: <?php echo $graph['radiology']['radiology_bill_payment_ratio'];?>%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>    
                                           </div><!--./col-lg-6-->
										   <?php } } ?>
                                        </div><!--./row-->
                                        <div class="row">
											<?php if ($this->module_lib->hasActive('blood_bank')) {  if ($this->module_lib->hasPatientActive('blood_bank')) { ?>
                                            <div class="col-md-6 project-progress-bars">
                                                <div class="row">
                                                    <div class="col-md-12 mtop5">
                                                       <div class="topprograssstart">
                                                            <h5 class="text-uppercase mt5 bolds"><?php echo $this->lang->line('blood_bank_billing_payment_graph'); ?>
                                                            </h5>
                                                            <p class="text-muted bolds"><?php echo $graph['blood_bank']['blood_bank_bill_payment_ratio'];?>%<span class="pull-right"> <?php echo $this->customlib->get_payment_bill($graph['blood_bank']['payment']['total_payment'],$graph['blood_bank']['bill']['total_bill']);?></span></p>
                                                            <div class="progress-group">
                                                                <div class="progress progress-minibar">
                                                                    <div class="progress-bar progress-bar-aqua" style="width: <?php echo $graph['blood_bank']['blood_bank_bill_payment_ratio'];?>%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!--./row-->
                                           </div><!--./col-lg-6-->
										   <?php } } 
										   if ($this->module_lib->hasActive('ambulance')) { if ($this->module_lib->hasPatientActive('ambulance')) { ?>
                                           <div class="col-md-6 project-progress-bars">
                                                <div class="row">
                                                    <div class="col-md-12 mtop5">
                                                        <div class="topprograssstart">
                                                            <h5 class="text-uppercase mt5 bolds"><?php echo $this->lang->line('ambulance_billing_payment_graph'); ?>
                                                            </h5>
                                                            <p class="text-muted bolds"><?php echo $graph['ambulance']['ambulance_bill_payment_ratio'];?>%<span class="pull-right"> <?php echo $this->customlib->get_payment_bill($graph['ambulance']['payment']['total_payment'],$graph['ambulance']['bill']['total_bill']);?></span></p>
                                                            <div class="progress-group">
                                                                <div class="progress progress-minibar">
                                                                    <div class="progress-bar progress-bar-aqua" style="width: <?php echo $graph['ambulance']['ambulance_bill_payment_ratio'];?>%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>    
                                           </div><!--./col-lg-6-->
										   <?php } } ?>
                                        </div><!--./row-->                                       
                                        <div class="box-header mb10 pl-0">
                                           <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('medication'); ?></h3>
                                           <div class="pull-right">
                                               
                                          </div>
                                        </div>
                                        <div class="box-header mb10 pl-0">
                                            <div class="table-responsive">
                                                 <?php

                                                  if(!empty($medicationreport_overview)){ ?>
                                              <table class="table table-striped table-bordered table-hover " >
                                                        <thead>
                                                            <tr>
                                                            <th><?php echo $this->lang->line('date');?></th>
                                                            <th><?php echo $this->lang->line('medicine_name');?></th>
                                                            <th><?php echo $this->lang->line('dose');?></th>
                                                            <th><?php echo $this->lang->line('time');?></th>
                                                            <th><?php echo $this->lang->line('remark');?></th>
                                                          <tr>
                                                        </thead>
                                                        <tbody>
                                                             <?php 
                                                          for ($i=0; $i <$recent_record_count; $i++) { 
                                                            if(!empty($medicationreport_overview[$i])){
                                                   ?>
                                                   <tr>
                                                       <td><?php if($medicationreport_overview[$i]['date']){ echo $this->customlib->YYYYMMDDTodateFormat($medicationreport_overview[$i]['date']); } ?></td>
                                                       <td><?php echo $medicationreport_overview[$i]['medicine_name']?></td>
                                                        <td><?php echo $medicationreport_overview[$i]['medicine_dosage']." (".$medicationreport_overview[$i]['unit'].")";?></td>
                                                       <td><?php echo $this->customlib->getHospitalTime_Format($medicationreport_overview[$i]['time'])  ;?> </td>
                                                       <td><?php echo $medicationreport_overview[$i]['remark'];?></td>
                                                   </tr>
                                                   <?php                                               
                                                        }
                                                     }
                                                ?>                                                        
                                                        </tbody>                                              
                                                </table>
                                                <?php  }  ?>   
                                            </div>                                         
                                        </div>
                                         <!---lab investigation-->
                                        <div class="box-header mb10 pl-0">
                                           <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('lab_investigation'); ?></h3>
                                           <div class="pull-right">                                               
                                          </div>
                                        </div>
                                         <div class="box-header mb10 pl-0">
                                             <div class="table-responsive"> 
                                                <?php if(!empty($investigations)){ ?>
                                             <table class="table table-striped table-bordered table-hover" data-export-title="<?php echo $this->lang->line('lab_investigation'); ?>">
                                                <thead>
                                                    <th><?php echo $this->lang->line('test_name'); ?></th>
                                                    <th><?php echo $this->lang->line('lab'); ?></th>
                                                    <th><?php echo $this->lang->line('sample_collected'); ?></th>
                                                    <td><strong><?php echo $this->lang->line('expected_date'); ?></strong></td>
                                                    <th><?php echo $this->lang->line('approved_by'); ?></th>                                                   
                                                </thead>
                                                <tbody id="">
                                                    <?php
                                                    $i=0;
                                                     foreach($investigations as $row ){ 
                                                        ++$i;
                                                              if($i <= $recent_record_count){
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $row['test_name']; ?><br/>
                                                           <?php echo "(".$row['short_name'].")"; ?></td>
                                                            <td><?php echo $this->lang->line($row['type']); ?></td>
                                                            <td><label>
                                                             <?php echo composeStaffNameByString($row['collection_specialist_staff_name'],$row['collection_specialist_staff_surname'],$row['collection_specialist_staff_employee_id']); ?>
                                                             </label>                                                
                                                             <br/>
                                                             <label for=""><?php if($row['type']=='pathology'){ echo $this->lang->line('pathology');  }else{ echo $this->lang->line('radiology');
                                                             }  ?> : </label>                                                
                                                                    <?php
                                                               echo $row['test_center']; 
                                                                ?>
                                                                <br/>
                                                                 <?php if($row['collection_date']){ echo $this->customlib->YYYYMMDDTodateFormat($row['collection_date']); } ?></td>                                                                   
                                                             <td>
                                                            <?php
                                                            if($row['reporting_date']){
                                                            echo  $this->customlib->YYYYMMDDTodateFormat($row['reporting_date']); 
                                                            } ?>                                                                
                                                            </td>
                                                            <td class="text-left">
                                                                 <label for=""><?php echo $this->lang->line('approved_by'); ?> : </label>
                                                                <?php      
                                                            echo composeStaffNameByString($row['approved_by_staff_name'],$row['approved_by_staff_surname'],$row['approved_by_staff_employee_id']);
                                                             ?>
                                                             <br/>
                                                            <?php if($row['parameter_update']){                                
                                                            echo  $this->customlib->YYYYMMDDTodateFormat($row['parameter_update']);
                                                            } ?>                                         
                                                            </td>                                                           
                                                            </tr>
                                                             <?php } } ?>
                                                            </tbody>
                                                        </table>
                                                    <?php } ?>
                                            </div>                                         
                                        </div>
                                           <!---lab investigation-->

                                        <div class="box-header mb10 pl-0">
                                           <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('operation'); ?></h3>
                                           <div class="pull-right">
                                               
                                          </div>
                                        </div>
                                         <div class="box-header mb10 pl-0">
                                             <div class="table-responsive"> 
                                                <?php if (!empty($operation_theatre)) { ?>
                                                 <table class="table table-striped table-bordered table-hover" >
                                                    <thead>
                                                        <th><?php echo $this->lang->line("reference_no"); ?></th>
                                                        <th><?php echo $this->lang->line("operation_date"); ?></th>
                                                        <th><?php echo $this->lang->line("operation_name"); ?></th>
                                                        <th><?php echo $this->lang->line("operation_category"); ?></th>
                                                        <th><?php echo $this->lang->line("ot_technician"); ?></th>
                                                    </thead>
                                                        <tbody id="">
                                                                <?php
                                                                $i=0;
                                                            
                                                                foreach ($operation_theatre as $ot_key => $ot_value) {

                                                                    $i++;
                                                                     if($i <= $recent_record_count){
                                                                    ?>  
                                                                    <tr>    
                                                                        <td><?php echo $this->customlib->getPatientSessionPrefixByType('operation_theater_reference_no'). $ot_value["id"] ?></td>
                                                         <td><?php echo 
                                                            $this->customlib->YYYYMMDDHisTodateFormat($ot_value["date"],$this->customlib->getHospitalTimeFormat());
                                                            ?></td>
                                                                        <td><?php echo $ot_value["operation"]; ?></td>
                                                                        <td><?php echo $ot_value["category"] ?></td>
                                                                        <td><?php echo $ot_value['ot_technician'] ?></td>                     
                                                                        
                                                                    </tr>
                                                                
                                                                <?php } } ?>
                                                        </tbody>
                                                    </table>
                                                <?php }  ?>
                                            </div>                                          
                                        </div>                                        
                                         <div class="box-header mb10 pl-0">
                                           <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('charges'); ?></h3>
                                           <div class="pull-right">
                                               
                                          </div>
                                        </div>
                                        <div class="box-header mb10 pl-0">
                                             <div class="table-responsive"> 
                                                <?php if (!empty($charges_detail)) { ?>
                                         <table class="table table-striped table-bordered table-hover ">
                                    <thead>                                   
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th> 
                                        <th class=""><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?> </th>
                                        <th class=""><?php echo $this->lang->line('tax'); ?></th>
                                        <th class=""><?php echo $this->lang->line('applied_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                          <th class=""><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                                    </thead>
                                    <tbody>
                                       <?php 
                                            $total = 0; $i=0;                                            
                                                foreach ($charges_detail as $charges_key => $charges_value) {
                                                    ++$i;
                                                    if($i <= $recent_record_count)
                                                    {
                                                        $tax_amount = ($charges_value['apply_charge']*$charges_value['tax']/100) ;
                                                        $taxamount = amountFormat($tax_amount);
                                                        $total += $charges_value["amount"];
                                                        ?>  
                                                        <tr> 
                                                            <td class="">
                                                                <?php echo $charges_value["name"]; ?>
                                                                 <div class="bill_item_footer text-muted"> <?php echo $charges_value["note"]; ?></div>
                                                            </td>
                                                            <td style="text-transform: capitalize;"><?php echo $charges_value["charge_type"] ?></td>
                                                            <td class="text-right"><?php echo $charges_value["standard_charge"] ?></td>
                                                             <td class="text-right"><?php echo $taxamount."(".$charges_value["tax"]."%)" ;?></td>
                                                            <td class="text-right"><?php echo $charges_value["apply_charge"] ?></td>
                                                             <td class="text-right"><?php echo $charges_value["amount"] ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }                                            
                                            ?> 
                                    </tbody>
                                </table>
                            <?php } ?>
                                            </div>
                                        </div>
                                        <div class="box-header mb10 pl-0">
                                           <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('payment'); ?></h3>
                                           <div class="pull-right">                                               
                                          </div>
                                        </div>
                                         <div class="box-header mb10 pl-0">
                                             <div class="table-responsive"> 
                                                <?php                                              
                                                    if (!empty($payment_details)) { ?>
                                      <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <th><?php echo $this->lang->line('transaction_id'); ?></th>
                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <th><?php echo $this->lang->line('note'); ?></th>
                                                <th><?php echo $this->lang->line('payment_mode'); ?></th>
                                                <th class="text-right"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                                            </thead>
                                            <tbody>

                                                <?php
                                                $total_payment = 0;                                                 
                                                        $total_payment = 0; $i=0;
                                                        foreach ($payment_details as $payment) {
                                                             ++$i ;
                                                                 if($i <= $recent_record_count){
                                                                    if (!empty($payment['amount'])) {
                                                                        $total_payment += $payment['amount'];
                                                                    }
                                                            ?> 
                                                            <tr>
                                                                <td><?php echo $this->customlib->getPatientSessionPrefixByType('transaction_id').$payment['id']; ?></td>
                                                                <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment['payment_date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                                                                <td><?php echo $payment["note"] ?></td>
                                                                <td ><?php echo $this->lang->line(strtolower($payment["payment_mode"]))."<br>";

                                                                if($payment['payment_mode'] == "Cheque"){
                                                                     if($payment['cheque_no']!=''){
                                               echo $this->lang->line('cheque_no') . ": ".$payment['cheque_no'];
                                              
                                            echo "<br>";
                                        }
                                            if($payment['cheque_date']!='' && $payment['cheque_date']!='0000-00-00'){
                                               echo $this->lang->line('cheque_date') .": ".$this->customlib->YYYYMMDDTodateFormat($payment['cheque_date']); 
                                           } 
                                             }
                                                                ?>                                                                   

                                                                </td>
                                                                <td class="text-right"><?php echo $payment["amount"] ?></td>
                                                            </tr>
                                                <?php } 
                                            } ?> 
                                        </tbody>                                               
                                     </table>
                                 <?php } ?>
                                 </div>                                          
                                </div>                              

                                        <div class="box-header mb10 pl-0">
                                           <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('live_consultation'); ?></h3>
                                           <div class="pull-right">
                                               
                                          </div>
                                        </div>
                                         <div class="box-header mb10 pl-0">
                                             <div class="table-responsive"> 
                                                <?php if(!empty($visitconferences)){ ?>
                                            <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <th><?php echo $this->lang->line('consultation_title') ; ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('created_by'); ?> </th>
                                        <th><?php echo $this->lang->line('created_for'); ?></th>
                                        <th><?php echo $this->lang->line('patient'); ?></th>
                                    </thead>
                                    <tbody>
                                        <?php                                       
                                            
                                            foreach ($visitconferences as $conference_key => $conference_value) {
												if($conference_value->return_response){
													$return_response = json_decode($conference_value->return_response);
												}else{
													$return_response = '';
												}
                                                ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-toggle="popover" class="detail_popover"><?php echo $conference_value->title; ?></a>
                                                    <div class="fee_detail_popover" style="display: none">
                                                        <?php
                                                                if ($conference_value->description == "") {
                                                                            ?>
                                                                        <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                                        <?php
                                                                    } else {
                                                                                ?>
                                                                        <p class="text text-info"><?php echo $conference_value->description; ?></p>
                                                                        <?php
                                                        }
                                                                ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-name">
                                                <?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($conference_value->date)) ?>
                                                </td>
                                                 <td class="mailbox-name">
                                                    <?php
                                                        $name = ($conference_value->create_by_surname == "") ? $conference_value->create_by_name : $conference_value->create_by_name . " " . $conference_value->create_by_surname;
                                                       
                                                        if ($name == 'Super Admin') {
                                                            echo $name;
                                                            # code...
                                                        } else {
                                                            echo $name . " (" . $conference_value->create_by_role_name . ": " . $conference_value->for_create_employee_id . ")";
                                                        }

                                                        ?></td>
                                                <td class="mailbox-name">
                                                    <?php
                                                    $name = ($conference_value->create_for_surname == "") ? $conference_value->create_for_name : $conference_value->create_for_name . " " . $conference_value->create_for_surname;
                                                    echo $name . " (" . $conference_value->for_create_role_name . ": " . $conference_value->for_create_employee_id . ")";
                                                    ?>
                                                </td>
                                                <td class="mailbox-name">
                                                     <?php   $name = ($conference_value->patient_name == "") ? $conference_value->patient_name : $conference_value->patient_name;
                                                    echo $name . " (" . $conference_value->patientid . ")";
                                                    ?>
                                                </td> 
                                            </tr>
                                            <?php
                                        }                                  
                                 ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                                            </div>                                          
                                        </div>                                             
                                    </div><!--./col-lg-6-->
                            </div><!--./row-->  
                        </div><!--#/overview-->        
                        <div class="tab-pane " id="activity">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('checkups'); ?></h3>
                            </div>                            
                            <div class="table-responsive">
                                  <h5><?php echo $this->customlib->getPatientSessionPrefixByType('opd_no').$opd_details_id; ?></h5> 
                                <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%" >
                                    <thead>
                                    <th><?php echo $this->lang->line('checkup_id'); ?></th>
                                    <th><?php echo $this->lang->line('appointment_date'); ?></th>
                                    <th><?php echo $this->lang->line('consultant'); ?></th>
                                    <th><?php echo $this->lang->line('reference'); ?></th>
                                    <th><?php echo $this->lang->line('symptoms'); ?></th>
                                    <?php if (is_array($fields) || is_object($fields))
                                    {
                                        foreach ($fields as $fields_key => $fields_value)
                                        { ?>
                                        <th><?php echo ucfirst($fields_value->name); ?></th>
                                        <?php }
                                    }
                                    ?>   
                                    <th class="text-right noExport" ><?php echo $this->lang->line('action') ?></th>
                                    </thead>
                                    <tbody>                                    

                        <?php if (!empty($visit_details)) {
                            foreach ($visit_details as $key => $visit) {
                                ?>
                                                <tr>
                                                    <td><?php echo $this->customlib->getPatientSessionPrefixByType('checkup_id') .$visit["id"] ; ?></td>
                                                    <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($visit['appointment_date'])); ?>                                                    
                                                    </td>
                                                    <td><?php echo composeStaffNameByString($visit["name"],$visit["surname"],$visit['employee_id']); ?></td>
                                                    <td><?php echo $visit['refference']; ?></td>
                                                    <td><?php echo $visit['symptoms']; ?></td>
                                                    <?php if (is_array($fields) || is_object($fields))
                                                    {
                                                        foreach ($fields as $fields_key => $fields_value)
                                                        { 
                                                                $display_field = $visit[$fields_value->name];
                                                              
                                                                    ?>
                                                                <td>
                                                                    <?php echo $display_field; ?>
                                                                            
                                                                </td>
                                                                <?php
                                                        }
                                                    }
                                                    ?>
                                                    <td class="pull-right" >
                                                         <?php
                                                          if ($visit["prescription"] == 'yes') {
                                                             ?>    
                                                                    <a href="#" class="btn btn-default btn-xs" data-toggle='tooltip' onclick="view_prescription('<?php echo $visit["id"] ?>')" title="<?php echo $this->lang->line('view_prescription'); ?>">
                                                                        <i class="fas fa-file-prescription"></i>
                                                                    </a>
                                                            <?php 
                                                        }
                                                        ?> 
                                                    <?php if($result['gender'] == 'Female' && $visit["is_antenatal"] == 1 && $visit['visit_details_id']!=''){ ?>
                  
                                                        <a href='#' onclick='viewantenatal("<?php echo $visit["id"]; ?>")' class='btn btn-default btn-xs'  data-toggle='tooltip' title='<?php echo $this->lang->line('view_antenatal_finding'); ?>'><img width='15' src='<?php echo base_url().'uploads/patient_images/ultrasound-machine.png' ?> '></a>      
                                                        <?php } ?>                                                         
                                                        
                                                        <a href="#"  class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>" onclick="getRecord('<?php echo $visit["id"]; ?>')" >
                                                            <i class="fa fa-reorder"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php }
}
?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                         <!-- Charges -->
                        <div class="tab-pane" id="charges">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('charges'); ?></h3>
                            </div>
                            <div class="download_label"><?php echo composePatientName($result['patient_name'],$result['patient_id']) . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('charge_name'); ?> / <?php echo $this->lang->line('charge_note'); ?></th>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                                        <th><?php echo $this->lang->line('charge_category'); ?></th>
                                        <th><?php echo $this->lang->line('qty'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?> </th>
										<th class="text-right"><?php echo $this->lang->line('applied_charge') . ' (' . $currency_symbol . ')'; ?></th>
										<th class="text-right"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')'; ?></th>
										<th class="text-right"><?php echo $this->lang->line('discount'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('tax'); ?></th>                                        
                                        <th class="text-right"><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>                                  
                                    </thead>
                                    <tbody>
                                       <?php 
                                            $total = 0;
                                            if (!empty($charges_detail)) {
                                                foreach ($charges_detail as $charges_key => $charges_value) {
                                                    										
													$discount_amount = amountFormat(($charges_value['apply_charge']*$charges_value['discount_percentage']/100)) ;
                                                    $tax_amount = (($charges_value['apply_charge']-$discount_amount)*$charges_value['tax']/100) ;
                                                    $taxamount = amountFormat($tax_amount);
                                                    $total += $charges_value["amount"];													
													
                                                    ?>  
                                                    <tr>                                                       
                                                        <td>
                                                         <?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($charges_value['date'])); ?>
                                                         </td>
                                                        <td class="">
                                                            <?php echo $charges_value["name"]; ?>
                                                             <div class="bill_item_footer text-muted"> <?php echo $charges_value["note"]; ?></div>
                                                        </td>
                                                        <td style="text-transform: capitalize;"><?php echo $charges_value["charge_type"] ?></td>
                                                        <td style="text-transform: capitalize;"><?php echo $charges_value["charge_category_name"] ?></td>
                                                        <td style="text-transform: capitalize;"><?php echo $charges_value['qty']//." ".$charges_value["unit"]; ?></td>			
                                                        <td class="text-right"><?php echo $charges_value["standard_charge"] ?></td>
														<td class="text-right"><?php echo $charges_value["apply_charge"] ?></td>
														<td class="text-right"><?php if($charges_value["org_charge"]!="") { echo $charges_value["org_charge"]; }else{ echo '0.00' ; } ?></td>
														<td class="text-right"><?php echo $discount_amount." (".$charges_value["discount_percentage"]."%) " ;?></td> 
                                                        <td class="text-right"><?php echo $taxamount."(".$charges_value["tax"]."%)" ;?></td>
                                                        <td class="text-right"><?php echo $charges_value["amount"] ?></td>
                                                        <td class="text-right">  <a href="javascript:void(0);" class="btn btn-default btn-xs print_charge" data-toggle="tooltip" title="" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" data-record-id="<?php echo $charges_value['id']; ?>"  data-original-title="<?php echo $this->lang->line('print'); ?>">
    <i class="fa fa-print"></i>
    </a> </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?> 
                                    </tbody>
                                    <tr class="box box-solid total-bg">
                                        <td colspan='11' class="text-right rtl-text-left"><?php echo $this->lang->line('total') . " : " . $currency_symbol . amountFormat($total); ?> <input type="hidden" id="charge_total" name="charge_total" value="<?php echo $total ?>">
                                        </td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- -->
                        <!--payment -->
                         <div class="tab-pane" id="payment">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('payment'); ?></h3>
                                <div class="box-tab-tools">
                                    <button type="button" class="btn btn-info btn-sm" data-result_id="<?php echo $result['id'] ?>" data-backdrop="static" data-toggle="modal" data-target="#payMoney"><i class="fa fa-plus"></i> <?php echo $this->lang->line('make_payment'); ?></button>
                                </div>
                            </div>
                            <div class="download_label"><?php echo $this->lang->line('payment'); ?></div>                                    
                                <div class="table-responsive">                                    
                                        <table class="table table-striped table-bordered table-hover example">
                                            <thead>
                                            <th width="10%"><?php echo $this->lang->line('transaction_id'); ?></th>
                                            <th width="10%"><?php echo $this->lang->line('date'); ?></th>
                                            <th width="20%"><?php echo $this->lang->line('note'); ?></th>
                                            <th width="20%"><?php echo $this->lang->line('payment_mode'); ?></th>
                                            <th width="30%" class="text-right"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                                             <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                            </thead>
                                            <tbody>
                                            <?php
                                        if (!empty($payment_details)) {
                                                $total_payment = 0;
                                                foreach ($payment_details as $payment) {
                                                    if (!empty($payment['amount'])) {
                                                        $total_payment += $payment['amount'];
                                                    }
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('transaction_id').$payment["id"] ?></td>
                                                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment['payment_date'], $this->customlib->getHospitalTimeFormat());?></td>
                                                            <td><?php echo $payment["note"] ?></td>
                                                            <td><?php echo $this->lang->line(strtolower($payment["payment_mode"]))."<br />";
                                                                if($payment['payment_mode'] == "Cheque"){
                                                                    if($payment['cheque_no']!=''){
                                                                        echo $this->lang->line('cheque_no') . ": ".$payment['cheque_no'];
                                                                        echo "<br>";
                                                                    }
                                                                    if($payment['cheque_date']!='' && $payment['cheque_date']!='0000-00-00'){
                                                                       echo $this->lang->line('cheque_date') .": ".$this->customlib->YYYYMMDDTodateFormat($payment['cheque_date']);
                                                                   }
                                                                }
                                                            ?></td>
                                                           <td class="text-right"><?php echo $payment["amount"] ?></td> 
                                                           <td class="text-right">
                                                             <?php         if ($payment['payment_mode'] == "Cheque" && $payment['attachment'] != "")  {  ?>
                                                                 <a href='<?php echo site_url('patient/dashboard/downloadreceipt/'.$payment['id']);?>' class='btn btn-default btn-xs'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
                                                                <?php
                                                            }
                                                                     ?>
                                                            <a href="javascript:void(0);" class="btn btn-default btn-xs print_trans" data-toggle="tooltip" title="" data-loading-text="<i class='fa fa-circle-o-notch fa-spi'></i>" data-module-type="opd" data-record-id="<?php echo $payment['id']; ?>"  data-original-title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></td>
                                                        </tr>
                                                <?php }?>
                                                </tbody>
                                                    <tr class="box box-solid total-bg">
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td colspan="2" class="text-right rtl-text-left"><?php echo $this->lang->line('total') . " : " . $currency_symbol. amountFormat($total_payment); ?>
                                                        </td>
                                                        <td></td>                                                        
                                                    </tr>                                               

                                    <?php }?>
                                        </table>
                                </div>
                        </div>
                        <!-- -->
                        <!--Diagnosis -->
                        <div class="tab-pane" id="labinvestigation">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('lab_investigation'); ?></h3>
                            </div>
                            <div class="download_label"><?php echo $result['lab_investigation'] ; ?></div>
                            <div class="table-responsive">
                                 <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo $this->lang->line('opd_details'); ?>">
                                    <thead>
                                        <th><?php echo $this->lang->line('test_name'); ?></th>
                                         <th><?php echo $this->lang->line('lab'); ?></th>
                                        <th><?php echo $this->lang->line('sample_collected'); ?></th>
                                        <td><strong><?php echo $this->lang->line('expected_date'); ?></strong></td>
                                         <th><?php echo $this->lang->line('approved_by'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody id="">
                                        <?php foreach($investigations as $row ){ ?>
                                            <tr>
                                            <td><?php echo $row['test_name']; ?><br/>
                                           <?php echo "(".$row['short_name'].")"; ?></td>
                                            <td><?php echo $this->lang->line($row['type']); ?></td>
                                            <td><label>
                                 <?php echo composeStaffNameByString($row['collection_specialist_staff_name'],$row['collection_specialist_staff_surname'],$row['collection_specialist_staff_employee_id']); ?>
                                 </label>                                    
                                     <br/>
                                     <label for=""><?php if($row['type']=='pathology'){ echo $this->lang->line('pathology');  }else{ echo $this->lang->line('radiology');
                                     }  ?> : </label>                                    
                                        <?php
                                   echo $row['test_center']; 
                                    ?>
                                    <br/>
                                     <?php if($row['collection_date']){ echo $this->customlib->YYYYMMDDTodateFormat($row['collection_date']); } ?></td>                                       
                                       <td>
                                    <?php
                                    if($row['reporting_date']){
                                    echo  $this->customlib->YYYYMMDDTodateFormat($row['reporting_date']); } ?>
                                        
                                    </td>
                                    <td class="text-left">
                                         <label for=""><?php echo $this->lang->line('approved_by'); ?> : </label>
                                        <?php      
                                    echo composeStaffNameByString($row['approved_by_staff_name'],$row['approved_by_staff_surname'],$row['approved_by_staff_employee_id']);
                                     ?>
                                     <br/>
                                    <?php  if($row['parameter_update']){                              
                                    echo  $this->customlib->YYYYMMDDTodateFormat($row['parameter_update']);
                                    } ?>                                         
                                    </td>
                                    <td class="text-right"><a href='javascript:void(0)'  data-loading-text='<i class="fa fa-reorder"></i>' data-record-id='<?php echo $row['report_id'];?>' data-type-id='<?php echo $row['type'];?>' data-test-id='<?php echo $row['test_name']. " (".$row['short_name'].")"; ?>'  class='btn btn-default btn-xs view_report' data-toggle='tooltip' title='<?php echo $this->lang->line("show"); ?>'><i class='fa fa-reorder'></i></a></td>
                                    </tr>
                                     <?php } ?>
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
                                                            <span class="bg-blue">    
                                                            <?php 
                                                            if($value['timeline_date']){ 
                                                                echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($value['timeline_date'])); 
                                                            } ?>
                                                            </span>
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
                        <!-- -->                         
                        
                        <div class="tab-pane" id="vitals">
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
													<?php foreach($patient_vital_date as $pvd){ //$messure_date = $pvd['messure_date']?>
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
                                
                        <div class="tab-pane" id="medication">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('medication'); ?></h3>
                            </div>
                                    <div class="table_inner"> 
                                          <?php if(!empty($medication)){
                                            ?>
                                        <table class="table table-striped table-bordered table-hover">
                                          <thead>
                                             <th class="hard_left"><?php echo $this->lang->line("date"); ?> </th>
                                             <th class="next_left"><?php echo $this->lang->line("medicine_name"); ?></th>
                                            <?php 
                                            if (!empty($max_dose)) {
                                                $dosage_count = $max_dose;
                                             } else{
                                                $dosage_count = 0;
                                             }
                                            
                                            for ($x = 1; $x <= $dosage_count; $x++) { ?>                                              
                                              <th class="sticky-col" width="150"><?php echo $this->lang->line("dose").''.$x  ;?></th>
                                           <?php }
                                            ?> 
                                    </thead> 
                                    <tbody> 
                                        <?php 
                                         $count = 1;
                                    foreach ($medication as $medication_key => $medication_value) 
                                    {
                                    $pharmacy_id = $medication_value['pharmacy_id'];
                                    $medicine_category_id = $medication_value['medicine_category_id'];
                                    $date = $medication_value['date'];                                            
                                        ?>
                                    <tr>
                                        <?php $subcount = 1; foreach ($medication_value['dosage'][$date] as $mkey => $mvalue) { 
                                        $date = $this->customlib->YYYYMMDDTodateFormat($medication_value['date']);
                                            ?>
                                            <td class="hard_left"><?php if($subcount==1){ echo $date."<br>(".date('l', strtotime($medication_value['date'])).")"; }else{
                                                echo "<span class='fa-level-span'><i class='fa fa-level-up fa-level-roated' aria-hidden='true'></i></span>";
                                            } ?></td>
                                          <td class="next_left"><?php echo $mvalue['name'] ?></td>  
                                          <?php 
                                          for ($x = 0; $x <= $dosage_count; $x++){
                                            if (array_key_exists($x,$mvalue['dose_list']))
                                                  {
                                                    $add_index=$x;
                                                   
                                                  ?>
                                                   <td class="dosehover"><?php echo $this->lang->line('time').": ".$this->customlib->getHospitalTime_Format($mvalue['dose_list'][$x]['time'])."</a></br>". $mvalue['dose_list'][$x]['medicine_dosage']." ".$mvalue['dose_list'][$x]['unit']; if($mvalue['dose_list'][$x]['remark']!=''){ echo " <br>".$this->lang->line('remark').": ".$mvalue['dose_list'][$x]['remark'] ;}?>
                                                   <br>
                                                   <?php echo $this->lang->line('created_by').': '.$mvalue['dose_list'][$x]['staff_name'].' '.$mvalue['dose_list'][$x]['staff_surname'].'  ('.$mvalue['dose_list'][$x]['staff_employee_id'].")"; ?>
                                                   </td>
                                                  <?php
                                                  }
                                                else
                                                  {
                                                  ?>
                                                  <td class="dosehover"> <?php 
                                                  if($add_index+1==$x){
                                                    ?>
                                               
                                                    <?php
                                                  }
                                                  ?></td>
                                                  <?php
                                                  }
                                            ?>                                        
                                       <?php }   ?>                                       
                                   
                                    </tr> 
                                <?php $subcount++; }
                                          }
                                           ?>
                                    </tbody>
                                    </table>
                                            <?php
                                          }else{
                                            ?>
<div class="alert alert-info">
    <?php  echo $this->lang->line('no_record_found');?>
</div>
                                            <?php
                                          }
                                          ?>                                  
                                </div> 
                            </div>

                            <div class="tab-pane" id="operationtheatre">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('operation'); ?></h3>
                                </div>                           
                            <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example" data-export-title="<?php echo $this->lang->line('opd_visit_details'); ?>">
                                    <thead>
                                        <th><?php echo $this->lang->line("reference_no"); ?></th>
                                        <th><?php echo $this->lang->line("operation_date"); ?></th>
                                        <th><?php echo $this->lang->line("operation_name"); ?></th>
                                        <th><?php echo $this->lang->line("operation_category"); ?></th>
                                        <th><?php echo $this->lang->line("ot_technician"); ?></th>
                                        <?php if (is_array($fields_ot) || is_object($fields)){
                                                foreach ($fields_ot as $fields_key => $fields_value)
                                                { ?>
                                                   <th><?php echo ucfirst($fields_value->name); ?></th>
                                                <?php }
                                            }?>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody id="">
                                            <?php
                                        if (!empty($operation_theatre)) {
                                            foreach ($operation_theatre as $ot_key => $ot_value) {
                                                ?>  
                                                <tr>    
                                                    <td><?php echo $this->customlib->getPatientSessionPrefixByType('operation_theater_reference_no').$ot_value["id"] ?></td>
                                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($ot_value["date"],$this->customlib->getHospitalTimeFormat())?></td>
                                                    <td><?php echo $ot_value["operation"] ?></td>
                                                    <td><?php echo $ot_value["category"] ?></td>
                                                    <td><?php echo $ot_value['ot_technician'] ?></td>
                                                   <?php
                                                    if (!empty($fields_ot)) {
                                                        foreach ($fields_ot as $fields_key => $fields_value) {
                                                            $display_field = $ot_value[$fields_value->name];
                                                            if ($fields_value->type == "link") {
                                                                $display_field = "<a href=" . $ot_value[$fields_value->name] . " target='_blank'>" . $ot_value[$fields_value->name] . "</a>";
                                                            }
                                                            ?>
                                                            <td>
                                                                <?php echo $display_field; ?>
                                                            </td>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <td class="text-right">
                                                        <a href='javascript:void(0);' class='btn btn-default btn-xs viewot' data-backdrop="static" data-keyboard="false" data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-toggle='tooltip' data-record-id='<?php echo $ot_value['id']; ?>'  title="<?php echo  $this->lang->line('show')?>"><i class='fa fa-reorder'></i></a>   
                                                    </td>
                                                </tr>                                            
                                            <?php } }?>
                                    </tbody>
                                </table>
                            </div> 
                        </div> 

                         <div class="tab-pane" id="live_consult">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('live_consultation'); ?></h3>
                            </div>
                           <?php if (empty($visitconferences)) {
    ?>
    <div class="alert alert-danger"><?php echo $this->lang->line('no_record_found'); ?></div>
                                        <?php
} else { ?>
                            <div class="table-responsive">
                                 <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <th><?php echo $this->lang->line('consultation_title') ; ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('created_by'); ?> </th>
                                        <th><?php echo $this->lang->line('created_for'); ?></th>
                                        <th><?php echo $this->lang->line('patient'); ?></th>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody>
                                        <?php  

    foreach ($visitconferences as $conference_key => $conference_value) {
        $return_response = isJSON($conference_value->return_response) ? json_decode($conference_value->return_response):false;

    
       
        ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-toggle="popover" class="detail_popover"><?php echo $conference_value->title; ?></a>
                                                    <div class="fee_detail_popover" style="display: none">
                                                        <?php
if ($conference_value->description == "") {
            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
} else {
            ?>
                                                            <p class="text text-info"><?php echo $conference_value->description; ?></p>
                                                            <?php
}
        ?>
                                                    </div>
                                                </td>
                                                <td class="mailbox-name">
                                                <?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($conference_value->date)) ?>
                                                </td>
                                                 <td class="mailbox-name">
                                                    <?php
        $name = ($conference_value->create_by_surname == "") ? $conference_value->create_by_name : $conference_value->create_by_name . " " . $conference_value->create_by_surname ;       
        if ($conference_value->staff_create_by_role_id == 7  ) {
            if($superadmin_restriction == 'enabled'){
                echo  $name. " (" . $conference_value->create_by_employee_id.')';
            }
        } else {

            if(IsNullOrEmptyString($conference_value->create_by_name)){              
              echo $this->lang->line('patient');              
            }else{
                echo  $name . " (" . $conference_value->create_by_role_name . ": " . $conference_value->create_by_employee_id . ")";                 
            }           
        }
        ?>            
        </td>
                                                <td class="mailbox-name">
                                                    <?php

        $name = ($conference_value->create_for_surname == "") ? $conference_value->create_for_name : $conference_value->create_for_name . " " . $conference_value->create_for_surname;
        echo $name . " (" . $conference_value->for_create_role_name . ": " . $conference_value->for_create_employee_id . ")";

        ?>
                                                </td>
                                                <td class="mailbox-name">
                                                     <?php

        $name = ($conference_value->patient_name == "") ? $conference_value->patient_name : $conference_value->patient_name;
        echo $name . " (" . $conference_value->patientid . ")";

        ?>
                                                </td>
                                            <td class="mailbox-name">
                                                    <?php if ($conference_value->status == 0) { ?>
                                                        <span class="label label-warning font-w-normal"><?php echo $this->lang->line('awaited'); ?></span>
                                                    <?php } ?>
                                                    <?php if ($conference_value->status == 1) { ?>
                                                        <span class="label label-danger font-w-normal"><?php echo $this->lang->line('cancelled'); ?></span>
                                                    <?php } ?>
                                                    <?php if ($conference_value->status == 2) { ?>
                                                        <span class="label label-success font-w-normal"><?php echo $this->lang->line('finished'); ?></span>
                                                    <?php } ?>
                                            </td>
                                            <td class="mailbox-date pull-right">
                                                    <?php
 if($conference_value->status == 0 && $conference_value->live_consult_link){ 
            ?>
                                           <a href="<?php echo $return_response->join_url; ?>" class="btn btn-xs label-success p0"  data-id="<?php echo $conference_value->id; ?>">
                                                      <span class="label" ><i class="fa fa-video-camera"></i> <?php echo $this->lang->line('join') ?></span>
                                            <?php
}
        ?>
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
                    </div>
                </div>
            </div>
    </section>
</div>
<!-- Add Charges -->
<!-- -->
<!-- Add Diagnosis -->
<!-- -->
<div class="modal fade" id="addBillModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add') . " " . $this->lang->line('bill'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                        <form id="add_billform" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="ptt10">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('total_charges'); ?></label><small class="req"> *</small>
                                        <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id ?>" />
                                        <input  name="total_charges" id="totalopdcharges"  type="text" class="form-control"  />
                                        <span class="text-danger"><?php echo form_error('total_amount'); ?></span> 
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('total_payment'); ?></label><small class="req"> *</small>
                                        <input  name="total_payment" id="total_payment"  placeholder="" type="text" class="form-control "  />
                                        <input  name="opdidhide" id="opdidhide" value="" placeholder="" type="hidden" class="form-control "  />
                                        <span class="text-danger"><?php echo form_error('total_payment'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('gross_total'); ?></label>
                                        <input type="text" name="gross_total" id="gross_total" placeholder=""  class="form-control"/>
                                        <span class="text-danger"><?php echo form_error('gross_total'); ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('discount'); ?></label>
                                        <div class="" style="margin-top:-5px; border:0; outline:none;"><input  name="discount" id="discount" placeholder="" type="text"  class="form-control"   />
                                            <span class="text-danger"><?php echo form_error('discount'); ?></span></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('other') . " " . $this->lang->line('charge'); ?></label>
                                        <input   name="other_charge" id="other_charge" placeholder="" type="text" class="form-control"  />

                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('tax'); ?></label>
                                        <input   name="tax" id="tax"  placeholder="" type="text" class="form-control"  />
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('net_amount'); ?></label>
                                        <input   name="net_amount" id="net_amount"   placeholder="" type="text" class="form-control"  />
                                    </div>                                   
                                </div>
                            </div>
                        </form>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</div>
<!-- -->
<div class="modal fade" id="viewModal" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <button type="button" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('close'); ?>" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('visit_details'); ?></h4>
            </div>
            <div class="modal-body">               
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModalsummary"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deletebill'>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('discharged_summary'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>
<!-- -->
<div class="modal fade" id="prescriptionview" tabindex="-1" role="dialog" aria-labelledby="follow_up">
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close"  data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deleteprescription'>

                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('prescription'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0" id="getdetails_prescription">
            </div>
        </div>
    </div>
</div>
</form>
<!-- -->

<div id="payMoney" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('make_payment') ?></h4>
            </div>
            <form id="payment_form" class="form-horizontal modal_payment" method="POST">
                <div class="modal-body pb0">
                    <div class="form-group">
                        <label for="amount" class="col-sm-3 control-label"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>) <small class="req">*</small></label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" value="<?= amountFormat($total-$total_payment); ?>" name="deposit_amount" id="amount_total_paid" >
                            <input type="hidden" class="form-control" value="<?= amountFormat($total-$total_payment); ?>" name="net_amount"  >
                            <span id="deposit_amount_error" class="text text-danger"><?php echo form_error('deposit_amount'); ?></span>
                            <input type="hidden" name="payment_for" value="opd">
                            <input type="hidden" name="id" value="<?php echo $visit_details[0]["opd_details_id"]; ?>">
                        </div>
                    </div>
                </div>
            </form>
                <div class="modal-footer">
                    <button id="pay_button" class="btn btn-info pull-right"><?php echo $this->lang->line('add'); ?></button>
                </div>
        </div>
    </div>
</div>

<div class="modal fade" id="prescriptionview" tabindex="-1" role="dialog" aria-labelledby="follow_up">   
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close"  data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='edit_deleteprescription'>
                
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('prescription'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0" id="getdetails_prescription">
            </div>
        </div>
    </div>
</div>
<!--lab investigation modal-->
<div class="modal fade" id="viewDetailReportModal" role="dialog" aria-labelledby="myModalLabel" >
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
<!-- -->

<div class="modal fade" id="view_ot_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='action_detail_modal'></div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('operation_details'); ?></h4>
            </div>
            <div class="modal-body min-h-3">
               <div id="show_ot_data"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="findingview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='action_detail_modal'></div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('operation_details'); ?></h4>
            </div>
            <div class="modal-body min-h-3">
               <div id="getdetails_finding"></div>
            </div>
        </div>
    </div>
</div> 

<script type="text/javascript">
    function viewantenatal(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/antenatal/getantenatalprescription/' + visitid ,
            success: function (res) {
                $("#getdetails_finding").html(res);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        }); 

        holdModal('findingview');
    }
    
    $('#payMoney').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    })

    $(function () {
        //Initialize Select2 Elements
        $(function () {
            var hash = window.location.hash;
            hash && $('ul.nav-tabs a[href="' + hash + '"]').tab('show');

            $('.nav-tabs a').click(function (e) {
                $(this).tab('show');
                var scrollmem = $('body').scrollTop();
                window.location.hash = this.hash;
                $('html,body').scrollTop(scrollmem);
            });
        });
    });

    $(function () {
        $("#compose-textareas,#compose-textareanew").wysihtml5();
    });   

    $(document).on('click','.print_charge',function(){
        var $this = $(this);
        var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>patient/dashboard/printCharge',
          type: "POST",
          data:{'id':record_id,'type':'opd'},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
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

    $(document).on('click','.print_trans',function(){
        var $this = $(this);
        var record_id=$this.data('recordId');
        var module_type = $(this).attr('data-module-type');
        $this.button('loading');
        $.ajax({
          url: '<?php echo base_url(); ?>patient/dashboard/printTransaction',
          type: "POST",
          data:{'id':record_id,'module_type':module_type},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
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
    
    function getRecord(visitid) {      
     
       var $this = $(this);   
        $.ajax({
            url: base_url+'patient/dashboard/getopdrecheckupDetails',
            type: "POST",
            data: {visit_id: visitid},
            dataType: 'json',
               beforeSend: function() {             
               
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
}

    function delete_record(opdid) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_conform') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deleteOPD',
                type: "POST",
                data: {opdid: opdid},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }

    function delete_patient(id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_conform') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deleteOPDPatient',
                type: "POST",
                data: {id: id},
                dataType: 'json',
                success: function (data) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.href = '<?php echo base_url() ?>admin/patient/search';
                }
            })
        }
    }

    function getEditRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getDetails',
            type: "POST",
            data: {patient_id: id},
            dataType: 'json',
            success: function (data) {
                $("#patientids").val(data.patient_unique_id);
                $("#patient_names").val(data.patient_name);
                $("#contacts").val(data.mobileno);
                $("#emails").val(data.email);
                $("#ages").val(data.age);
                $("#address").text(data.address);
                $("#months").val(data.month);
                $("#guardian_names").val(data.guardian_name);
                $("#amounts").val(data.amount);
                $("#updateids").val(id);
                $('select[id="blood_groups"] option[value="' + data.blood_group + '"]').attr("selected", "selected");
                $('select[id="genders"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="marital_statuss"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                $('select[id="consultant_doctors"] option[value="' + data.cons_doctor + '"]').attr("selected", "selected");
                holdModal('myModaledit');
            },
        });
    }

    $(document).ready(function (e) {
        $("#formeditrecord").on('submit', (function (e) {
            $("#formeditrecordbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/update',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                    $("#formeditrecordbtn").button('reset');
                },
                error: function () {

                }
            });
        }));
    });
	
    function getRecord_id(id) {
        $('#prescription_id').val(id);
        $('#pres_patient_id').val(id);        
        holdModal('add_prescription');
    }

    function view_prescription(visitid) {

        $.ajax({
            url: '<?php echo base_url(); ?>patient/prescription/getPrescription/' + visitid,
            success: function (res) {
                $("#edit_deleteprescription").html("<a href='#' data-toggle='tooltip' data-original-title='Print' onclick='printprescription(" + visitid + ")' id='print_id' data-toggle='modal' ><i class='fa fa-print'></i></a>");
                $("#getdetails_prescription").html(res);
                holdModal('prescriptionview');
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>");
            }
        });
    }

    function getRevisitRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getDetails',
            type: "POST",
            data: {patient_id: id},
            dataType: 'json',
            success: function (data) {
                $("#revisit_id").val(data.patient_unique_id);
                $("#revisit_name").val(data.patient_name);
                $('#revisit_guardian').val(data.guardian_name);
                $("#revisit_contact").val(data.mobileno);
                $("#revisit_case").val(data.case_type);
                $("#revisit_organisation").val(data.orgid);
                $("#pid").val(id);
                $("#revisit_allergies").val(data.known_allergies);
                $("#revisit_refference").val(data.refference);
                $("#revisit_email").val(data.email);
                $("#revisit_amount").val(data.amount);
                $("#standard_chargevisit").val(data.standard_charge);
                $("#revisit_symptoms").val(data.symptoms);
                $("#revisit_age").val(data.age);
                $("#revisit_month").val(data.month);
                $("#revisit_height").val(data.height);
                $("#revisit_blood_group").val(data.blood_group);
                $("#revisi_tax").val(data.tax);
                $("#revisit_address").val(data.address);
                $('select[id="revisit_old_patient"] option[value="' + data.old_patient + '"]').attr("selected", "selected");
                $('select[id="revisit_doctor"] option[value="' + data.cons_doctor + '"]').attr("selected", "selected");
                $('select[id="revisit_gender"] option[value="' + data.gender + '"]').attr("selected", "selected");
                $('select[id="revisit_marital_status"] option[value="' + data.marital_status + '"]').attr("selected", "selected");
                holdModal('revisitModal');
            },
        })
    }

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

    function deleteOpdPatientDiagnosis(patient_id, id) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_confirm') . "'"; ?>)) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/patient/deleteOpdPatientDiagnosis/' + patient_id + '/' + id,
                success: function (res) {
                    successMsg(<?php echo "'" . $this->lang->line('delete_message') . "'"; ?>);
                    window.location.reload(true);
                }
            })
        }
    }

    function deleteOpdPatientDiagnosis1(url, Msg) {
        if (confirm(<?php echo "'" . $this->lang->line('delete_conform') . "'"; ?>)) {
            $.ajax({
                url: url,
                success: function (res) {
                    successMsg(Msg);
                    window.location.reload(true);
                }
            })
        }
    }

    var attr = {};

    function getMedicineName(id) {
        console.log(id);
        var category_selected = $("#medicine_cat" + id).val();
        var arr = category_selected.split('-');
        var category_set = arr[0];
        div_data = '';
        $("#search-query" + id).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
        $('#search-query' + id).select2("val", 'l');
        $.ajax({
            type: "POST",
            url: base_url + "admin/pharmacy/get_medicine_name",
            data: {'medicine_category_id': category_selected},
            dataType: 'json',
            success: function (res) {
                console.log(res);
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.medicine_name + "'>" + obj.medicine_name + "</option>";
                });
                $("#search-query" + id).html("<option value=''>Select</option>");
                $('#search-query' + id).append(div_data);
                $('#search-query' + id).select2("val", '');

            }
        });
    };

    function printprescription(visitid) {
        var base_url = '<?php echo  base_url() ?>';
        $.ajax({
            url: base_url + 'patient/prescription/getPrescription/' + visitid ,
            type: 'POST',
            data: {payslipid: visitid, print: 'yes'},
            success: function (result) {

                $("#testdata").html(result);
                popup(result);
                
            }
        });
    }

        $(document).ready(function (e) {
                $("#add_payment").on('submit', (function (e) {
                    e.preventDefault();
                
                $.ajax({
                    url: '<?php echo base_url(); ?>patient/pay/opdpay',
                    type: "POST",
                    data: new FormData(this),
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false,
               
                     beforeSend: function(){
                     $("#add_paymentbtn").button("loading");
                     },
                       success: function (data) {
                        if (data.status == "fail") {
                            var message = "";
                            $.each(data.error, function (index, value) {
                                message += value;
                            });
                            errorMsg(message);
                        } else {
                            successMsg(data.message);
                            window.location.reload(true);
                        }
                        $("#add_paymentbtn").button("reset");
                    },
                    error: function () {
                     $("#add_paymentbtn").button('reset');
                    },
      
                    complete: function(){
                     $("#add_paymentbtn").button('reset');
                    }
                });
            }));
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

    $('#pay_button').click(function(){
        var formdata = new FormData($('#payment_form')[0]);
        $.ajax({
            url: base_url+'patient/pay/checkvalidate',
            type: "POST",
            data: formdata,
            dataType: 'json',
            cache : false,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    window.location.replace(base_url+'patient/pay');
                }
            }
        })
    })

    $(document).on('click','.viewot',function(){
        var $this = $(this);
        var record_id=$this.data('recordId');
          
        $this.button('loading');
        $.ajax({
              url: base_url+'patient/dashboard/otdetails',
              type: "POST",
              data: {ot_id: record_id},
              dataType: 'json',
               beforeSend: function() {
                    $this.button('loading');
              },
              success: function(data) {
                   $('#view_ot_modal').modal({backdrop:'static'});
                   $('#show_ot_data').html(data.page);     
                   $('#action_detail_modal').html(data.actions); 
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

    $(document).on('click','.print_ot_bill',function(){
         var $this = $(this);
         var record_id=$this.data('recordId');
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>patient/dashboard/print_otdetails',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
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