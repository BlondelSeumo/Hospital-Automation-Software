<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
$patient_id = $id;
$case_reference_id=$result['case_reference_id'];
?>
<div class="content-wrapper">
    <section class="content">
        <div class="box box-primary">         
            <div class="box border0 mb0">
                <?php
                    if (!isset($result)) {
                        echo "<h4 class='text-center'>" . $this->lang->line("no_record_found") . "</h4>";
                    } else {
                ?>
                <div class="nav-tabs-custom border0 mb0" id="tabs"> 
                    <ul class="nav nav-tabs navheader navlistscroll">
                        <li class="active" ><a href="#overview"><i class="fa fa-th"></i> <?php echo $this->lang->line('overview');?></a></li>
                        <li ><a href="#nurse_note" data-toggle="tab" aria-expanded="true"><i class="fas fa-sticky-note"></i> <?php echo $this->lang->line('nurse_notes'); ?></a></li>
                        <li><a href="#medication" data-toggle="tab" aria-expanded="true"><i class="fa fa-medkit"></i> <?php echo $this->lang->line('medication'); ?></a></li>
                        <li><a href="#prescription" data-toggle="tab" aria-expanded="true"><i class="fas fa-file-prescription"></i> <?php echo $this->lang->line('prescription'); ?></a></li>
                        <li><a href="#consultant_register" data-toggle="tab" aria-expanded="true"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('consultant_register'); ?></a></li>
                        <li><a href="#labinvestigation" data-toggle="tab" aria-expanded="true"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('lab_investigation'); ?></a></li>
                        <li><a href="#operationtheatre" data-toggle="tab" aria-expanded="true"><i class="fas fa-cut"></i> <?php echo $this->lang->line('operations'); ?></a></li>
                        <li><a href="#charges" data-toggle="tab" aria-expanded="true"><i class="fas fa-donate"></i> <?php echo $this->lang->line('charges'); ?></a></li>
                        <li><a href="#payment" data-toggle="tab" aria-expanded="true"><i class="fas fa-hand-holding-usd"></i> <?php echo $this->lang->line('payment'); ?></a></li>
                        <?php if  ($this->module_lib->hasActive('live_consultation')) { ?>
                        <li><a href="#live_consult" class="" data-toggle="tab" aria-expanded="true"><i class="fa fa-video-camera" aria-hidden="true"></i> <?php echo $this->lang->line('live_consultation'); ?></a></li>
                        <?php } ?>
                        <li><a href="#bed_history" class="bed_history" data-toggle="tab" aria-expanded="true"><i class="fas fa-procedures"></i> <?php echo $this->lang->line("bed_history"); ?></a></li>
                        <li><a href="#timeline" data-toggle="tab" aria-expanded="true"><i class="far fa-calendar-check"></i> <?php echo $this->lang->line('timeline'); ?></a></li>
						<li><a href="#treatment_history" data-toggle="tab" aria-expanded="true"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('treatment_history'); ?></a></li>                                
                        <li><a href="#vitals" data-toggle="tab" aria-expanded="true"><i class="fa fa-blood-pressure">&#xf21e;</i> <?php echo $this->lang->line('vitals'); ?></a></li>
                        <?php if($result['is_antenatal']==1){ ?> 
                        <li><a href="#obstetric_history" role="tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('previous_obstetric_history'); ?></a></li> 
                        <li><a href="#post_antenatal" role="tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('postnatal_history'); ?></a></li>
                        <li><a href="#addantenatal" role="tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-diagnoses"></i> <?php echo $this->lang->line('antenatal'); ?></a></li>                                
                        <?php } ?> 
					</ul>
                            <div class="tab-content">
                                <?php
                                    $charge_total = 0;
                                        $bill_amount  = 0;
                                        foreach ($charges as $charge) {
                                            $charge_total += $charge["apply_charge"];
                                            $bill_amount = $charge_total - $paid_amount;
                                        }
                                        ?>   <?php if (($bill_amount != 0) && ($bill_amount >= $result["ipdcredit_limit"])) {?>
                                    <div class="alert alert-danger"><?php echo $this->lang->line('credit_limit_exeeded'); ?></div>
                                <?php }?>
                                
                              <!-- overview -->       
                             <div class="tab-pane tab-content-height active" id="overview">
                                 <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 border-r">
         <div class="box-header border-b mb10 pl-0 pt0">
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo composePatientName($result['patient_name'],$result['patient_id']); ?></h3>
            <div class="pull-right">
               <div class="editviewdelete-icon pt8">
                  <a class="" href="#" onclick="getRecord('<?php echo $ipdid ?>')" data-toggle="tooltip" title="<?php echo $this->lang->line('profile') ?>"><i class="fa fa-reorder"></i>
                  </a> 
                  <a class="" href="<?php echo base_url()?>patient/dashboard/patientipddetails"  data-toggle="tooltip" title="<?php echo $this->lang->line('previous_ipd') ?>">
                  <small class="label label-success"><i class="fa fa-hospital-o"></i> <?php echo $this->lang->line('previous_ipd') ?></small></a> 
               </div>
            </div> 
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
            </div>
            <!--./col-lg-5-->
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
                  <?php if (file_exists("./uploads/patient_id_card/barcodes/" . $id . ".png")) { ?>
                  <tr>
                    <td class="bolds"><?php echo $this->lang->line('barcode'); ?></td>
                    <td>
                        <a href="<?php echo base_url("./uploads/patient_id_card/barcodes/" . $id . ".png"); ?>"  target="_blank" >
                        <img src="<?php echo base_url("./uploads/patient_id_card/barcodes/" . $id . ".png"); ?>" width="100" height="30" />
                        </a>
                        </a>
                    </td>
                  </tr>
                  <?php } ?>
                  <?php
                  if (file_exists("./uploads/patient_id_card/qrcode/" . $id . ".png")) { ?>
                  <tr>
                    <td class="bolds"><?php echo $this->lang->line('qrcode'); ?></td>
                    <td>
                        <a href="<?php echo base_url("./uploads/patient_id_card/qrcode/" . $id . ".png"); ?>"  target="_blank" >
                        <img class="max-h-50" src="<?php echo base_url("./uploads/patient_id_card/qrcode/" . $id . ".png"); ?>"  width="70" height="70"  />
                        </a>
                    </td>
                  </tr>
                  <?php }  ?>                                                
               </table>
            </div>
            <!--./col-lg-7-->
         </div>
         <!--./row-->
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
                        <td class="bolds"><?php echo $this->lang->line('ipd_no'); ?></td>
                        <td><?php echo $this->customlib->getPatientSessionPrefixByType('ipd_no').$result['ipdid']; ?></td>
                     </tr>
                     <tr>
                        <td class="white-space-nowrap bolds" width="40%"><?php
                           echo $this->lang->line('admission_date');
                           ;
                           ?></td>
                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $time_format); ?></td>
                     </tr>
                     <tr>
                        <td class="bolds"><?php
                           echo $this->lang->line('bed');                          
                           ?></td>
                        <td><?php echo $result['bed_name'] . " - " . $result['bedgroup_name'] . " - " . $result['floor_name'] ?></td>
                     </tr>
                  </table>
               </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
               <div class="chart-responsive text-center">
                  <div class="chart" >
                     <canvas id="pieChart" style="height:150px"><span></span></canvas>
                  </div>
                  <p class="font12 mb0 font-medium"><?php echo $this->lang->line('credit_limit'); ?>: <?php echo $currency_symbol.$credit_limit; ?></p>
                  <p class="font12 mb0 font-medium text-danger"><?php echo $this->lang->line('used_credit_limit')?>: <?php echo $currency_symbol.$used_credit_limit; ?></p>
                  <p class="font12 mb0 font-medium text-success-xl"><?php echo $this->lang->line('balance_credit_limit')?>: <?php echo $currency_symbol.$balance_credit_limit; ?></p>
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
                                                        <td ><?php echo $this->customlib->YYYYMMDDHisTodateFormat($value['messure_date'],$this->customlib->getHospitalTimeFormat()); ?></td>
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
         <p><b><i class="fa fa-tag"></i> <?php echo $this->lang->line('known_allergies');?>:</b></p>
         <?php if(!empty($result['known_allergies'])){
            ?>
         <ul>
            <li>
               <div><?php echo $result['known_allergies']; ?></div>
            </li>
         </ul>
         <?php
            }?>
         <hr class="hr-panel-heading hr-10">
         <p><b><i class="fa fa-tag"></i> <?php echo $this->lang->line('finding');?>:</b></p>
         <?php if (!empty($prescription_detail)) { ?>
         <ul>
            <?php
               for ($i=0; $i <$recent_record_count; $i++) { 
                    if (!empty($prescription_detail[$i])) {
                  ?>  
            <li>
               <div><?php echo $prescription_detail[$i]['finding_description']; ?></div>
            </li>
            <?php
               }
               }
               ?>
         </ul>
         <?php } ?>
         <hr class="hr-panel-heading hr-10">
         <p><b><i class="fa fa-tag"></i> <?php echo $this->lang->line('symptoms');?>:</b></p>
         <?php if (!empty($result['symptoms'])) { ?>
         <ul>
            <li>
               <div><?php echo $result['symptoms']; ?></div>
            </li>
         </ul>
         <?php } ?>
         <hr class="hr-panel-heading hr-10">
         <div class="box-header mb10 pl-0">
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('consultant_doctor'); ?></h3>
            <div class="pull-right">
               <div class="editviewdelete-icon pt8">
               </div>
            </div>
         </div>
         <div class="staff-members">
            <div class="media">
               <div class="media-left">
                  <?php if($result['doctor_image']!=""){ ?>
                  <a href="javascript:void(0)">
                  <img src="<?php echo base_url("uploads/staff_images/".$result['doctor_image'].img_time()); ?>" class="member-profile-small media-object"></a>
                  <?php }else{ ?>
                  <img src="<?php echo base_url("uploads/staff_images/no_image.png".img_time()) ?>" class="member-profile-small media-object"></a>
                  <?php } ?>
               </div>
               <div class="media-body">
                  <h5 class="media-heading"><a href="javascript:void(0)"><?php echo $result['name']." ".$result['surname']." (".$result['employee_id'].")" ;?></a>
                  </h5>
               </div>
            </div>
            <!--./media-->
            <?php
               foreach ($doctors_ipd as $dkey => $dvalue) {?>
            <div class="media">
               <div class="media-left">
                  <?php if($dvalue['image']!=""){ ?>
                  <a href="javascript:void(0)">
                  <img src="<?php echo base_url("uploads/staff_images/".$dvalue['image'].img_time()); ?>" class="member-profile-small media-object"></a>
                  <?php }else{ ?>
                  <img src="<?php echo base_url("uploads/staff_images/no_image.png".img_time()) ?>" class="member-profile-small media-object"></a>
                  <?php } ?>
               </div>
               <div class="media-body">
                  <h5 class="media-heading"><a href="javascript:void(0)"><?php  echo  $dvalue['ipd_doctorname']." ".$dvalue['ipd_doctorsurname']." (".$dvalue['employee_id'].")" ; ?></a>
                  </h5>
               </div>
            </div>
            <!--./media-->
            <?php } ?>
         </div>
         <!--./staff-members--> 
         <div class="box-header mb10 pl-0">
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('nurse_notes'); ?></h3>
            <div class="pull-right">
            </div>
         </div>
         <div class="timeline-header no-border">
            <div id="timeline_list">
               <?php if (empty($nurse_note)) { ?>
               <?php } else { ?>
               <ul class="timeline timeline-inverse">
                  <?php
                     for ($i=0; $i <$recent_record_count; $i++) { 
                         if (!empty($nurse_note[$i])) { 
                         $id = $nurse_note[$i]['id'];                     
                     ?>      
                  <li class="time-label">
                     <span class="bg-blue">   
                     <?php echo $this->customlib->YYYYMMDDHisTodateFormat($nurse_note[$i]['date']); ?></span>
                  </li>
                  <li>
                     <i class="fa fa-list-alt bg-blue"></i>
                     <div class="timeline-item">
                        <h3 class="timeline-header text-aqua"> <?php echo $nurse_note[$i]['name'].' '.$nurse_note[$i]['surname']." ( ".$nurse_note[$i]['employee_id']." )" ; ?> </h3>
                        <div class="timeline-body">
                           <?php echo $this->lang->line('note') ."</br>". nl2br($nurse_note[$i]['note']); ?> 
                        </div>
                        <div class="timeline-body">
                           <?php echo $this->lang->line('comment') ."</br> ". nl2br($nurse_note[$i]['comment']); ?> 
                        </div>
                        <?php foreach ($nursenote[$id] as $ckey => $cvalue) { 
                           if (!empty($cvalue['staffname'])) {
                             $comment_by =  " (". $cvalue['staffname']." ".$cvalue['staffsurname'].": " .$cvalue['employee_id'].")";
                              $comment_date = $this->customlib->YYYYMMDDHisTodateFormat($cvalue['created_at'], $this->customlib->getHospitalTimeFormat());
                           }                                                                                            
                           ?>
                        <div class="timeline-body">
                           <?php echo nl2br($cvalue['comment_staff']);  
                              ?> 
                           <span class="pull-right"> <?php echo $comment_date." ". $comment_by ?></span>
                        </div>
                        <?php  } ?> 
                     </div>
                  </li>
                  <?php }} ?> 
                  <li><i class="fa fa-clock-o bg-gray"></i></li>
                  <?php } ?>  
               </ul>
            </div>
         </div>
         <hr>
         <div class="box-header mb10 pl-0">
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('timeline'); ?></h3>
            <div class="pull-right">
            </div>
         </div>
         <div class="timeline-header no-border">
            <div id="timeline_list">
               <?php 
                  if (empty($timeline_list)) { ?>
               <?php } else { ?>
               <ul class="timeline timeline-inverse">
                  <?php
                     for ($i=0; $i <$recent_record_count; $i++) { 
                         if (!empty($timeline_list[$i])) {
                     ?>      
                  <li class="time-label">
                     <span class="bg-blue">    
                     <?php echo $this->customlib->YYYYMMDDTodateFormat($timeline_list[$i]['timeline_date']); ?></span>
                  </li>
                  <li>
                     <i class="fa fa-list-alt bg-blue"></i>
                     <div class="timeline-item">
                        <?php if($is_discharge) {
                           if ($timeline_list[$i]['generated_users_type'] != 'patient') {
                           ?>
                        <span class="time"></span>
                        <?php }} ?>
                        <?php if($is_discharge) {
                           if ($timeline_list[$i]['generated_users_type'] != 'patient') {
                           ?><span class="time"></span> 
                        <?php }?>
                        <?php if (!empty($timeline_list[$i]["document"])) { ?>
                        <span class="time"><a class="defaults-c text-right" data-toggle="tooltip" title="" href="<?php echo base_url() . "admin/timeline/download_patient_timeline/" . $timeline_list[$i]["id"] . "/" . $timeline_list[$i]["document"] ?>" data-original-title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a></span>
                        <?php } ?>
                        <h3 class="timeline-header text-aqua"> <?php echo $timeline_list[$i]['title']; ?> </h3>
                        <div class="timeline-body">
                           <?php echo $timeline_list[$i]['description']; ?> 
                        </div>
                     </div>
                  </li>
                  <?php } } }?> 
                  <li><i class="fa fa-clock-o bg-gray"></i></li>
                  <?php } ?>  
               </ul>
            </div>
         </div>
      </div>
      <!--./col-lg-6-->

       <div class="col-lg-6 col-md-6 col-sm-12">
         <div class="row">
			<?php if ($this->module_lib->hasActive('ipd')) {  if ($this->module_lib->hasPatientActive('ipd')) { ?>
            <div class="col-md-6 project-progress-bars">
               <div class="row">
                  <div class="col-md-12 mtop5">
                     <div class="topprograssstart">
                        <h5 class="text-uppercase mt5 bolds"><?php echo $this->lang->line('ipd_billing_payment_graph'); ?>
                        </h5>
                        <p class="text-muted bolds"><?php echo $graph['ipd']['ipd_bill_payment_ratio'];?>%<span class="pull-right"> <?php echo $this->customlib->get_payment_bill($graph['ipd']['payment']['total_payment'],$graph['ipd']['bill']['total_bill']);?></span></p>
                        <div class="progress-group">
                           <div class="progress progress-minibar">
                              <div class="progress-bar progress-bar-aqua" style="width: <?php echo $graph['ipd']['ipd_bill_payment_ratio'];?>%"></div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!--./row-->
            </div>
			<?php } } 
			if ($this->module_lib->hasActive('pharmacy')) {  if ($this->module_lib->hasPatientActive('pharmacy')) { ?>			
            <!--./col-lg-6-->
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
            </div>
			<?php } } ?>		
            <!--./col-lg-6-->
         </div>
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
               </div>
               <!--./row-->
            </div>			  
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
            </div>
			<?php } } ?>
            <!--./col-lg-6-->
         </div>
         <!--./row-->
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
               </div>
               <!--./row-->
            </div>
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
            </div>
			<?php } } ?>
            <!--./col-lg-6-->
         </div>
         <!--./row-->
          <div class="box-header mb10 pl-0">
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('medication'); ?></h3>
            <div class="pull-right">
            </div>
         </div>
         <div class="box-header mb10 pl-0">            
            <div class="table-responsive">            
               <?php 
                  if (!empty($medicationreport_overview)) {
                  ?>
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
                        <td><?php echo $this->customlib->YYYYMMDDTodateFormat($medicationreport_overview[$i]['date']); ?></td>
                        <td><?php echo $medicationreport_overview[$i]['medicine_name']?></td>
                        <td><?php echo $medicationreport_overview[$i]['medicine_dosage']." (".$medicationreport_overview[$i]['unit'].")";?></td>
                        <td><?php echo $this->customlib->getHospitalTime_Format($medicationreport_overview[$i]['time']);?></td>
                        <td><?php echo $medicationreport_overview[$i]['remark'];?></td>
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
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('prescription'); ?></h3>
            <div class="pull-right">
            </div>
         </div>
         <div class="box-header mb10 pl-0">
            <div class="table-responsive">
               <?php  if (!empty($prescription_detail)) { ?>
               <table class="table table-striped table-bordered table-hover  ">
                  <thead>
                     <th><?php echo $this->lang->line('prescription_no'); ?></th>
                     <th><?php echo $this->lang->line('date'); ?></th>
                     <th><?php echo $this->lang->line('finding'); ?></th>
                  </thead>
                  <tbody>
                     <?php
                        for ($i=0; $i <$recent_record_count; $i++) { 
                             if (!empty($prescription_detail[$i])) {
                           ?>  
                     <tr>
                        <td><?php echo $this->customlib->getPatientSessionPrefixByType('ipd_prescription').$prescription_detail[$i]["id"] ?></td>
                        <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($prescription_detail[$i]['date'])); ?></td>
                        <td><?php echo $prescription_detail[$i]['finding_description']; ?></td>
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
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('consultant'); ?></h3>
            <div class="pull-right">
            </div>
         </div>
         <div class="box-header mb10 pl-0">
            <div class="table-responsive">
               <?php 
                  if (!empty($consultant_register)) {
                  ?>
               <table class="table table-striped table-bordered table-hover  ">
                  <thead>
                     <th><?php echo $this->lang->line('applied_date'); ?></th>
                     <th ><?php echo $this->lang->line('consultant_doctor'); ?></th>
                     <th width="50%"><?php echo $this->lang->line('instruction'); ?></th>
                     <th><?php echo $this->lang->line('instruction_date'); ?></th>
                  </thead>
                  <tbody>
                     <?php
                        for ($i=0; $i <$recent_record_count; $i++) { 
                        if (!empty($consultant_register[$i])) {
                            ?>  
                     <tr>
                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($consultant_register[$i]['date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                        <td><?php echo $consultant_register[$i]["name"] . " " . $consultant_register[$i]["surname"]." (".$consultant_register[$i]["employee_id"].")" ?></td>
                        <td><?php echo nl2br($consultant_register[$i]["instruction"]); ?></td>
                        <td><?php echo $this->customlib->YYYYMMDDTodateFormat($consultant_register[$i]['ins_date']); ?></td>
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
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('lab_investigations'); ?></h3>
            <div class="pull-right">
            </div>
         </div>
         <div class="box-header mb10 pl-0">
            <div class="table-responsive">
               <?php if(!empty($investigations)){ ?>
               <table class="table table-striped table-bordered table-hover " data-export-title="<?php echo $this->lang->line('lab_investigation'); ?>">
                  <thead>
                     <th><?php echo $this->lang->line('test_name'); ?></th>
                     <th><?php echo $this->lang->line('lab'); ?></th>
                     <th><?php echo $this->lang->line('sample_collected'); ?></th>
                     <td><strong><?php echo $this->lang->line('expected_date'); ?></strong></td>
                     <th><?php echo $this->lang->line('approved_by'); ?></th>
                  </thead>
                  <tbody id="">
                     <?php
                        for ($i=0; $i <$recent_record_count; $i++) { 
                            if(!empty($investigations[$i])){
                        ?>
                     <tr>
                        <td><?php echo $investigations[$i]['test_name']; ?><br/>
                           <?php echo "(".$investigations[$i]['short_name'].")"; ?>
                        </td>
                        <td><?php echo $this->lang->line($investigations[$i]['type']); ?></td>
                        <td><label>
                           <?php echo composeStaffNameByString($investigations[$i]['collection_specialist_staff_name'],$investigations[$i]['collection_specialist_staff_surname'],$investigations[$i]['collection_specialist_staff_employee_id']); ?>
                           </label>
                           <br/>
                           <label for=""><?php if($investigations[$i]['type']=='pathology'){ echo $this->lang->line('pathology');  }else{ echo $this->lang->line('radiology');
                              }  ?> : </label>
                           <?php
                              echo $investigations[$i]['test_center']; 
                               ?>
                           <br/>
                           <?php if($investigations[$i]['collection_date']){ echo $this->customlib->YYYYMMDDTodateFormat($investigations[$i]['collection_date']); } ?>
                        </td>
                        <td>
                           <?php
                              echo  $this->customlib->YYYYMMDDTodateFormat($investigations[$i]['reporting_date']); ?>
                        </td>
                        <td class="text-left">
                           <label for=""><?php echo $this->lang->line('approved_by'); ?> : </label>
                           <?php      
                              echo composeStaffNameByString($investigations[$i]['approved_by_staff_name'],$investigations[$i]['approved_by_staff_surname'],$investigations[$i]['approved_by_staff_employee_id']);
                               ?>
                           <br/>
                           <?php  if($investigations[$i]['parameter_update']){                              
                              echo  $this->customlib->YYYYMMDDTodateFormat($investigations[$i]['parameter_update']);
                           }
                               ?>                                         
                        </td>
                     </tr>
                     <?php } }?>
                  </tbody>
               </table>
               <?php } ?>  
            </div>
         </div>

         <div class="box-header mb10 pl-0">
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('operation'); ?></h3>
            <div class="pull-right">
            </div>
         </div>
         <div class="box-header mb10 pl-0">
            <div class="table-responsive">
               <?php 
                  if (!empty($operation_theatre)) {
                  ?>
               <table class="table table-striped table-bordered table-hover ">
                  <thead>
                     <th><?php echo $this->lang->line("reference_no"); ?></th>
                     <th><?php echo $this->lang->line("operation_date"); ?></th>
                     <th><?php echo $this->lang->line("operation_name"); ?></th>
                     <th><?php echo $this->lang->line("operation_category"); ?></th>
                     <th><?php echo $this->lang->line("ot_technician"); ?></th>
                  </thead>
                  <tbody>
                     <?php
                        for ($i=0; $i <$recent_record_count; $i++) { 
                        if (!empty($operation_theatre[$i])) {                           
                            ?>  
                     <tr>
                        <td><?php echo $this->customlib->getPatientSessionPrefixByType('operation_theater_reference_no').$operation_theatre[$i]["id"] ?></td>
                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($operation_theatre[$i]["date"],$this->customlib->getHospitalTimeFormat());
                           ?></td>
                        <td><?php echo $operation_theatre[$i]["operation"] ?></td>
                        <td><?php echo $operation_theatre[$i]["category"] ?></td>
                        <td><?php echo $operation_theatre[$i]['ot_technician'] ?></td>
                     </tr>
                     <?php } }?>
                  </tbody>
               </table>
               <?php } ?>
            </div>
         </div>

         <div class="box-header mb10 pl-0">
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('charges'); ?></h3>
            <div class="pull-right">
            </div>
         </div>

         <div class="box-header mb10 pl-0">
            <div class="table-responsive">
               <?php  if (!empty($charges)) {?>
               <table class="table table-striped table-bordered table-hover">                  
                  <thead class="white-space-nowrap">
                     <th><?php echo $this->lang->line('date'); ?></th>
                     <th><?php echo $this->lang->line('name'); ?></th>
                     <th><?php echo $this->lang->line('charge_type'); ?></th>
                     <th><?php echo $this->lang->line('charge_category'); ?></th>
                     <th><?php echo $this->lang->line('qty'); ?></th>
                     <th class="text-right"><?php echo $this->lang->line('amount') .' (' . $currency_symbol . ')'; ?></th>
                  </thead>
                  <tbody>
                     <?php
                        $total = 0;                    
                          
                            for ($i=0; $i <$recent_record_count; $i++) {
                             if (!empty($charges[$i])) { 
                             $total += $charges[$i]["amount"];                       
                            $tax_amount = calculatePercent($charges[$i]['apply_charge'],$charges[$i]['tax']);
                            $taxamount = amountFormat($tax_amount);
                                ?>
                     <tr>
                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($charges[$i]['date'],$this->customlib->getHospitalTimeFormat()); ?>
                        </td>
                        <td style="text-transform: capitalize;">
                           <?php echo $charges[$i]["name"] ?>
                           <div class="bill_item_footer text-muted"><label><?php if($charges[$i]["note"]!=''){ echo $this->lang->line('charge_note').': ';} ?> </label> <?php echo $charges[$i]["note"] ?></div>
                        </td>
                        <td style="text-transform: capitalize;"><?php echo $charges[$i]["charge_type"] ?></td>
                        <td style="text-transform: capitalize;"><?php echo $charges[$i]["charge_category_name"] ?></td>
                        <td style="text-transform: capitalize;"><?php echo $charges[$i]['qty']." ".$charges[$i]["unit"]; ?></td>
                        <td class="text-right"><?php echo number_format($charges[$i]["amount"], 2) ?></td>
                     </tr>
                     <?php } ?>  
                     <?php } ?>
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
               <?php if (!empty($payment_details)) { ?>
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
                        $total_payment=0;                       
                            
                            for ($i=0; $i <$recent_record_count; $i++) { 
                            if (!empty($payment_details[$i])) {
                                if (!empty($payment_details[$i]['amount'])) {
                                    $total_payment += $payment_details[$i]['amount'];
                                }
                                ?>
                     <tr>
                        <td><?php echo $this->customlib->getPatientSessionPrefixByType('transaction_id').$payment_details[$i]['id']; ?></td>
                        <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment_details[$i]['payment_date'],$this->customlib->getHospitalTimeFormat()); ?></td>
                        <td><?php echo $payment_details[$i]["note"] ?></td>
                        <td style="text-transform: capitalize;"><?php echo $this->lang->line(strtolower($payment_details[$i]["payment_mode"]))."<br>";
                           if($payment_details[$i]['payment_mode'] == "Cheque"){
                                if($payment_details[$i]['cheque_no']!=''){  echo $this->lang->line("cheque_no"). ": ".$payment_details[$i]['cheque_no']; echo "<br>";
                           }
                               if($payment_details[$i]['cheque_date']!='' && $payment_details[$i]['cheque_date']!='0000-00-00'){
                                  echo $this->lang->line("cheque_date") .": ".$this->customlib->YYYYMMDDTodateFormat($payment_details[$i]['cheque_date']);
                              }
                                }
                            ?>
                        </td>
                        <td class="text-right"><?php echo $payment_details[$i]["amount"] ?></td>
                     </tr>
                     <?php } ?>                                 
                  </tbody>
                  <?php } ?>
               </table>
               <?php } ?>
            </div>
            <!--./table-responsive--> 
         </div>

         <div class="box-header mb10 pl-0">
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('live_consultation'); ?></h3>
            <div class="pull-right"></div>
         </div>
         <div class="box-header mb10 pl-0">
            <div class="table-responsive">
               <?php  
                  if (!empty($ipdconferences)) {                  
                  ?>
               <table class="table table-striped table-bordered table-hover">
                  <thead>
                     <th><?php echo $this->lang->line('consultation_title'); ?></th>
                     <th><?php echo $this->lang->line('date'); ?></th>
                     <th><?php echo $this->lang->line('created_by'); ?> </th>
                     <th><?php echo $this->lang->line('created_for'); ?></th>
                     <th><?php echo $this->lang->line('patient'); ?></th>
                  </thead>
                  <tbody>
                     <?php
                        if (empty($ipdconferences)) {
                            ?>
                     <?php
                        } else {
                            for ($i=0; $i <$recent_record_count; $i++) { 
                                if(!empty($ipdconferences[$i])){
                        
                        $return_response = json_decode($ipdconferences[$i]->return_response);
                        ?>
                     <tr>
                        <td class="mailbox-name">
                           <?php echo $ipdconferences[$i]->title; ?>
                        </td>
                        <td class="mailbox-name">
                           <?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($ipdconferences[$i]->date))?>
                        <td class="mailbox-name">
                           <?php
                              $name= ($ipdconferences[$i]->create_by_surname == "") ? $ipdconferences[$i]->create_by_name : $ipdconferences[$i]->create_by_name . " " . $ipdconferences[$i]->create_by_surname;
                              echo  $name. " (".$ipdconferences[$i]->create_by_role_name.": ".$ipdconferences[$i]->create_by_employee_id.")";                              
                              ?>
                        </td>
                        <td class="mailbox-name">
                           <?php
                              $name= ($ipdconferences[$i]->create_for_surname == "") ? $ipdconferences[$i]->create_for_name : $ipdconferences[$i]->create_for_name . " " . $ipdconferences[$i]->create_for_surname;
                                echo  $name. " (".$ipdconferences[$i]->create_for_role_name.": ".$ipdconferences[$i]->create_for_employee_id.")";
                              ?>
                        </td>
                        <td class="mailbox-name">
                           <?php
                              $name= ($ipdconferences[$i]->patient_name == "") ? $ipdconferences[$i]->patient_name : $ipdconferences[$i]->patient_name ;
                              echo  $name. " (".$ipdconferences[$i]->patient_unique_id.")";
                              ?>
                        </td>
                     </tr>
                     <?php
                        } }
                        }
                        ?>
                  </tbody>
               </table>
               <?php } ?>
            </div>
         </div>

         <div class="box-header mb10 pl-0">
         </div>
         <div class="box-header mb10 pl-0">
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('treatment_history'); ?></h3>
            <div class="pull-right">
            </div>
         </div>
         <div class="box-header mb10 pl-0">
            <?php if(!empty($getipdoverviewtreatment)){?>
            <table class="table table-striped table-bordered table-hover "  data-export-title="<?php echo $this->lang->line('treatment_history'); ?>">
               <thead>
                  <th><?php echo $this->lang->line('ipd_no'); ?></th>
                  <th><?php echo $this->lang->line('symptoms'); ?></th>
                  <th><?php echo $this->lang->line('consultant'); ?></th>
                  <th class="text-left"><?php echo $this->lang->line('bed'); ?></th>
               </thead>
               <tbody>
                  <?php 
                     for ($i=0; $i <$recent_record_count; $i++) { 
                         if(!empty($getipdoverviewtreatment[$i])){
                     ?>
                  <tr>
                     <td><?php echo $this->customlib->getPatientSessionPrefixByType('ipd_no') . $getipdoverviewtreatment[$i]['ipdid']; ?></td>
                     <td><?php echo $getipdoverviewtreatment[$i]['symptoms']; ?></td>
                     <td><?php echo composeStaffNameByString($getipdoverviewtreatment[$i]['name'], $getipdoverviewtreatment[$i]['surname'], $getipdoverviewtreatment[$i]['employee_id']); ?></td>
                     <td><?php echo $getipdoverviewtreatment[$i]['bed_name'] . "-" . $getipdoverviewtreatment[$i]['bedgroup_name'] . "-" . $getipdoverviewtreatment[$i]['floor_name']; ?></td>
                  </tr>
                  <?php }} ?>
               </tbody>
            </table>
            <?php } ?>
         </div>
         <div class="box-header mb10 pl-0">
            <h3 class="text-uppercase bolds mt0 ptt10 pull-left font14"><?php echo $this->lang->line('bed_history'); ?></h3>
            <div class="pull-right">
            </div>
         </div>
         <div class="box-header mb10 pl-0">
            <div class="table-responsive">
               <?php 
                  if(!empty($bed_history)){
                  ?>
               <table class="table table-striped table-bordered table-hover">
                  <thead>
                     <th><?php echo $this->lang->line('bed_group'); ?></th>
                     <th><?php echo $this->lang->line('bed'); ?> </th>
                     <th><?php echo $this->lang->line('from_date'); ?></th>
                     <th><?php echo $this->lang->line('to_date'); ?></th>
                     <th><?php echo $this->lang->line("active_bed"); ?></th>
                  </thead>
                  <tbody>
                     <?php 
                        for ($i=0; $i <$recent_record_count; $i++) { 
                            if(!empty($bed_history[$i])){
                        ?>
                     <tr>
                        <td class="mailbox-name"><?php echo $bed_history[$i]->bed_group; ?></td>
                        <td class="mailbox-name"><?php echo $bed_history[$i]->bed; ?></td>
                        <td class="mailbox-name"><?php if($bed_history[$i]->from_date !=''){ echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($bed_history[$i]->from_date)); } ?></td>
                        <td class="mailbox-name"><?php if($bed_history[$i]->to_date !=''){ echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($bed_history[$i]->to_date)); } ?></td>
                        <td class="mailbox-name"><?php echo $this->lang->line($bed_history[$i]->is_active); ?></td>
                     </tr>
                     <?php } }?>
                  </tbody>
               </table>
               <?php } ?>
            </div>
         </div>
      </div> <!---col-md-6-->
                                 </div>
                              </div>
                              <!-- end overview --> 						  

                            <!-- Nurse Note -->                       
                            <div class="tab-pane " id="nurse_note">
                               <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('nurse_notes'); ?></h3>
                                </div>      
                                <div class="download_label"><?php echo $result['patient_name'] . "  (" . $this->customlib->getPatientSessionPrefixByType('ipd_no').$result['ipdid'] . ") " . $this->lang->line('ipd_details'); ?> </div>                                
                                <div id="">
                                <?php if (empty($nurse_note)) { ?>
                                            <br/>
                                            <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                            <?php } else { ?>
                                            <ul class="timeline timeline-inverse">
                                            <?php
                                            foreach ($nurse_note as $key => $value) { $id = $value['id'];                                            
                                            ?>      
                                                <li class="time-label">
                                                <span class="bg-blue">   
                                                <?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($value['date'])); ?></span>
                                                    </li> 
                                                    <li>
                                                        <i class="fa fa-list-alt bg-blue"></i>
                                                        <div class="timeline-item">
                                                            <h3 class="timeline-header text-aqua"> <?php echo $value['name']." ( ".$value['employee_id']." )" ; ?> </h3>
                                                            <div class="timeline-body">
                                                              <?php echo $this->lang->line('note') ."</br>". nl2br($value['note']); ?> 
                                                            </div>                                                             
                                                            <?php 
                                                            if (!empty($fields_nurse)) {
                                                                foreach ($fields_nurse as $fields_key => $fields_value) {
                                                                       if (!empty($fields_value->name)) {
                                                                          $display_field = $value[$fields_value->name];
                                                                          $fields = $fields_value->name;
                                                                       }else{
                                                                            $display_field = '';
                                                                            $fields = '';
                                                                       }                                                                    
                                                                    ?>
                                                                    <div class="timeline-body">
                                                                    <?php if ($fields !=null) {
                                                                        echo $fields."</br> ".$display_field ;
                                                                    } 
                                                                    ?>
                                                                    </div>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                           <div class="timeline-body">
                                                              <?php echo $this->lang->line('comment') ."</br> ".nl2br($value['comment']); ?> 
                                                            </div>
                                                            <?php foreach ($nursenote[$id] as $ckey => $cvalue) { 
                                                                if (!empty($cvalue['staffname'])) {
                                                                  $comment_by =  " (". $cvalue['staffname']." ".$cvalue['staffsurname'].": " .$cvalue['employee_id'].")";
                                                                   $comment_date =   date($this->customlib->getHospitalDateFormat(true, true), strtotime($cvalue['created_at']));
                                                                }
                                                                                                                                 
                                                                ?>
                                                                 <div class="timeline-body">
                                                                    <?php echo nl2br($cvalue['comment_staff']);   ?> 
                                                                    <span class="pull-right"> <?php echo $comment_date." ". $comment_by ?></span>
                                                                </div>
                                                            <?php  } ?> 
                                                        </div>
                                                    </li>
                                                <?php } ?> 
                                                <li><i class="fa fa-clock-o bg-gray"></i></li> 
                                                <?php } ?>  
                                        </ul>
                                    </div> 
                            </div>                           
                                <!-- Consultant Register -->
                                <div class="tab-pane" id="consultant_register">
                                    <div class="box-tab-header">
                                      <h3 class="box-tab-title"><?php echo $this->lang->line('consultant_register'); ?></h3>
                                   </div>
                                    <div class="download_label"><?php echo $this->lang->line('consultant_register'); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover example ">
                                            <thead>
                                            <th><?php echo $this->lang->line('applied_date'); ?></th>
                                            <th><?php echo $this->lang->line('consultant_doctor'); ?></th>
                                            <th><?php echo $this->lang->line('instruction'); ?></th>
                                            <th><?php echo $this->lang->line('instruction_date'); ?></th>
                                            <?php if (is_array($fields_consultant) || is_object($fields_consultant))
                                                {
                                                    foreach ($fields_consultant as $fields_key => $fields_value)
                                                    { ?>
                                                    <th><?php echo ucfirst($fields_value->name); ?></th>
                                                    <?php }
                                                }
                                            ?>
                                            </thead>
                                            <tbody>
                                                <?php
if (!empty($consultant_register)) {
        foreach ($consultant_register as $consultant_key => $consultant_value) {
            ?>
                                                        <tr>
                                                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($consultant_value["date"], $this->customlib->getHospitalTimeFormat()); ?></td>
                                                            <td><?php echo composeStaffNameByString($consultant_value['name'], $consultant_value['surname'], $consultant_value['employee_id']); ?></td>
                                                            <td><?php echo nl2br($consultant_value["instruction"]); ?></td>
                                                            <td><?php echo $this->customlib->YYYYMMDDTodateFormat($consultant_value['ins_date']); ?></td>
                                                            <?php if (is_array($fields_consultant) || is_object($fields_consultant))
                                                            {
                                                                foreach ($fields_consultant as $fields_key => $fields_value) {
                                                                    $display_field = $consultant_value[$fields_value->name];
                                                                  
                                                                        ?>
                                                                    <td>
                                                                        <?php echo $display_field; ?>                                                                                
                                                                    </td>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </tr>
                                                        <?php
}
    }
    ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Lab Investigation -->
                                <div class="tab-pane" id="labinvestigation">
                                    <div class="box-tab-header">
                                      <h3 class="box-tab-title"><?php echo $this->lang->line('lab_investigation'); ?></h3>
                                   </div>
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
                                        <?php      
                                    echo composeStaffNameByString($row['approved_by_staff_name'],$row['approved_by_staff_surname'],$row['approved_by_staff_employee_id']);
                                     ?>
                                     <br/>
                                    <?php if($row['parameter_update']){                               
                                    echo  $this->customlib->YYYYMMDDTodateFormat($row['parameter_update']);
                                    }?>                                         
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
                                      <div class="box-tab-tools">
                                      </div>
                                    </div>  
                                         
                                    <div class="timeline-header no-border">
                                        <div id="timeline_list">
                                            <?php
                                        if (empty($timeline_list)) { ?>
                                                <br/>
                                                <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                            <?php } else { ?>
                                                <ul class="timeline timeline-inverse">

                                                    <?php
                                                    foreach ($timeline_list as $key => $value) { ?>
                                                        <li class="time-label">
                                                            <span class="bg-blue"><?php  echo  date($this->customlib->getHospitalDateFormat(true, true), strtotime($value['timeline_date'])); ?>
                                                            </span>
                                                        </li>
                                                        <li>
                                                            <i class="fa fa-list-alt bg-blue"></i>
                                                            <div class="timeline-item">

                                                                <?php if (!empty($value["document"])) {?>
                                                                    <span class="time"><a class="defaults-c text-right" data-toggle="tooltip" title="" href="<?php echo base_url() . "patient/dashboard/download_patient_timeline/" . $value["id"] . "/" . $value["document"] ?>" data-original-title="<?php echo $this->lang->line('download'); ?>"><i class="fa fa-download"></i></a></span>
                                                                <?php }?>
                                                                 <?php if ($value["generated_users_type"] == 'patient') {?>
                                                                <span class="time"><a class="defaults-c text-right" data-toggle="tooltip" title="" onclick="delete_timeline('<?php echo $value['id']; ?>')" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                                                                </span>
                                                                <span class="time"><a onclick="editTimeline('<?php echo $value['id']; ?>')" class="defaults-c text-right" data-toggle="tooltip" title=""  data-original-title="<?php echo $this->lang->line('edit'); ?>">
                                                                        <i class="fa fa-pencil"></i>
                                                                    </a>
                                                                </span> 
                                                                <?php } ?>
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
                                
                                <!--Prescription -->
                                <div class="tab-pane" id="prescription">
                                    <div class="box-tab-header">
                                      <h3 class="box-tab-title"><?php echo $this->lang->line('prescription'); ?></h3>
                                   </div>
                                   <div class="download_label"><?php echo $this->lang->line('prescription'); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover example">
                                            <thead>                                           
                                            <th><?php echo $this->lang->line('prescription_no'); ?></th>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('finding'); ?></th>
                                                <?php 
                                        if (!empty($fields_prescription)) {
                                            foreach ($fields_prescription as $fields_key => $fields_value) {
                                                ?>
                                                <th><?php echo $fields_value->name; ?></th>
                                                <?php
                                            }  
                                        }
                                    ?> 
                                            <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                            </thead>
                                            <tbody>
                                                 <?php
if (!empty($prescription_detail)) {
        foreach ($prescription_detail as $prescription_key => $prescription_value) {
            ?>
                                                        <tr>
                                                            <td><?php echo $this->customlib->getPatientSessionPrefixByType("ipd_prescription"). $prescription_value["id"] ?></td>
                                                            <td><?php echo date($this->customlib->getHospitalDateFormat(), strtotime($prescription_value['date'])) ?></td>
                                                            <td><?php echo $prescription_value['finding_description']; ?></td>
                                                             <?php 
                                                             
                                                            if (!empty($fields_prescription)) {
                                                                $display_field = '';
                                                                foreach ($fields_prescription as $fields_key => $fields_value) {
                                                                    $display_field = $prescription_value[$fields_value->name];
                                                                ?>
                                                                    <td><?php echo $display_field; ?></td>
                                                                <?php
                                                                }
                                                            }
                                                            ?>
                                                            <td class="text-right">
                                                                <a href="#prescription" class="btn btn-default btn-xs" onclick="view_prescription('<?php echo $prescription_value["id"] ?>', '<?php echo $prescription_value["id"] ?>', '<?php echo "yes" ?>')"   data-toggle="tooltip" title="<?php echo $this->lang->line('view_prescription'); ?>">
                                                                    <i class="fas fa-file-prescription"></i>
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
                                
                                <!--Charges-->
                                <div class="tab-pane" id="charges">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('charges'); ?></h3>
                                </div>
                                    <div class="download_label"><?php echo $this->lang->line('charges'); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover example ">
                                            <thead>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('charge_name'); ?> <br> <?php echo $this->lang->line('charge_note'); ?></th>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                                        <th><?php echo $this->lang->line('charge_category'); ?></th>
                                        <th><?php echo $this->lang->line('qty'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('standard_charge') . ' (' . $currency_symbol . ')'; ?> </th>
                                        <th class="text-right"><?php echo $this->lang->line('tpa_charge') . ' (' . $currency_symbol . ')';  ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('discount'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('tax'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('applied_charge') . ' (' . $currency_symbol . ')'; ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('amount'); ?></th>
                                        <th class="noExport text-right"><?php echo $this->lang->line('action'); ?></th>
                                        </thead>
                                            <tbody>
                                                <?php
                                               $total = 0;
                                            if (!empty($charges)) {

                                                foreach ($charges as $charge) {

                                                    $discount_amount = amountFormat(($charge['apply_charge']*$charge['discount_percentage']/100)) ;
                                                    $tax_amount = (($charge['apply_charge']-$discount_amount) *$charge['tax']/100);
                                                    $taxamount = amountFormat($tax_amount);
                                                    $total += $charge["amount"];
                                                    ?>
                                                        <tr>
                                                        <td><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($charge['date'])); ?></td>
                                                        <td style="text-transform: capitalize;"><?php echo $charge["name"] ?>
                                                                 <div class="bill_item_footer text-muted"> <?php echo $charge["note"] ?></div>
                                                        </td>
                                                        <td style="text-transform: capitalize;"><?php echo $charge["charge_type"] ?></td>
                                                        <td style="text-transform: capitalize;">
                                                            <?php echo $charge["charge_category_name"] ?></td>
                                                        <td style="text-transform: capitalize;"><?php echo $charge['qty']//." ".$charge["unit"]; ?></td>
                                                        <td class="text-right"><?php echo $charge["standard_charge"] ?></td>
                                                        <td class="text-right"><?php echo number_format($charge["tpa_charge"], 2) ?></td>
                                                        <td class="text-right"><?php echo $discount_amount." (".$charge["discount_percentage"]."%) " ;?></td>             
                                                        <td class="text-right"><?php echo $taxamount."(".$charge["tax"]."%)"; ?></td>
                                                        <td class="text-right"><?php echo number_format($charge["apply_charge"], 2) ?></td>
                                                        <td class="text-right"><?php echo number_format($charge["amount"], 2) ?></td>
                                                        <td class="text-right"><a href="javascript:void(0);" class="btn btn-default btn-xs print_charge" data-toggle="tooltip" title="" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" data-record-id="<?php echo $charge['id']; ?>"  data-original-title="<?php echo $this->lang->line('print'); ?>" title="<?php echo $this->lang->line('print'); ?>">
                                                            <i class="fa fa-print"></i></a>
                                                        </td>
                                                        </tr>
                                                    <?php }?>
                                                <?php }?>
                                            </tbody>
                                            <tr class="box box-solid total-bg">
                                                <td colspan='12' class="text-right"><?php echo $this->lang->line('total') . ": " .$currency_symbol.''.$total; ?><input type="hidden" id="charge_total" name="charge_total" value="<?php echo $total ?>">
                                                </td>
                                                <td></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                        
                        <!--Live Consult-->
                        <div class="tab-pane" id="live_consult">
                            <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('live_consultation'); ?></h3>
                                </div>
                            <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                    <th><?php echo $this->lang->line('consultation_title'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('created_by'); ?> </th>
                                        <th><?php echo $this->lang->line('created_for'); ?></th>
                                        <th><?php echo $this->lang->line('patient'); ?></th>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody>
                                        <?php
if (empty($ipdconferences)) {
        ?>

                                        <?php
} else {
        foreach ($ipdconferences as $conference_key => $conference_value) {

            $return_response = json_decode($conference_value->return_response);
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
                echo $name. " (" . $conference_value->create_by_employee_id . ")";
                # code...
            } else {
                echo $name . " (" . $conference_value->create_by_employee_id . ")";
            }

            ?></td>

                                                <td class="mailbox-name">
                                                    <?php

            $name = ($conference_value->create_for_surname == "") ? $conference_value->create_for_name : $conference_value->create_for_name . " " . $conference_value->create_for_surname;
            
            echo $name . " (" . $conference_value->create_for_role_name . ": " . $conference_value->create_for_employee_id . ")";
            ?>
                                                </td>
                                                <td class="mailbox-name">
                                                     <?php

            $name = ($conference_value->patient_name == "") ? $conference_value->patient_name : $conference_value->patient_name;
            echo $name . " (" . $conference_value->patient_unique_id . ")";
            ?>

                                                </td>
                                              <td class="mailbox-name">
                                                <form class="chgstatus_form"  method="POST" action="<?php echo site_url('admin/conference/chgstatus') ?>">
                                                    <input type="hidden" name="conference_id"  value="<?php echo $conference_value->id; ?>">
                                                 <select class="form-control chgstatus_dropdown" disabled name="chg_status">
                                                     <option value="0" <?php if ($conference_value->status == 0) {
                echo "selected='selected'";
            }
            ?>><?php echo $this->lang->line('awaited'); ?></option>
                                                     <option value="1" <?php if ($conference_value->status == 1) {
                echo "selected='selected'";
            }
            ?>><?php echo $this->lang->line('cancelled'); ?> </option>
                                                     <option value="2" <?php if ($conference_value->status == 2) {
                echo "selected='selected'";
            }
            ?>><?php echo $this->lang->line('finished'); ?> </option>
                                                 </select>
                                                </form>
                                                </td>
                                                <td class="mailbox-date pull-right">
                                                    <?php
if ($conference_value->status == 0) {
                ?>
                                        <a href="<?php echo $return_response->start_url; ?>" class="btn label-success btn-xs"  target="_blank" >
                                        <i class="fa fa-sign-in"></i> <?php echo $this->lang->line('join'); ?> </a>
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
                                <!--Bed History-->
                                <div class="tab-pane tab-content-height" id="bed_history">
                                    <div class="box-tab-header">
                                        <h3 class="box-tab-title"><?php echo $this->lang->line("bed_history"); ?></h3>
                                        <div class="box-tab-tools">
                                        </div>
                                    </div>
                                    <div class="download_label"><?php echo $this->lang->line("bed_history"); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover example">
                                            <thead>
                                                <th><?php echo $this->lang->line('bed_group'); ?></th>
                                                <th><?php echo $this->lang->line('bed'); ?> </th>
                                                <th><?php echo $this->lang->line('from_date'); ?></th>
                                                <th><?php echo $this->lang->line('to_date'); ?></th>
                                                <th><?php echo $this->lang->line("active_bed"); ?></th>
                                            </thead>
                                            <tbody>
                                                <?php foreach($bed_history as $history){ ?>
                                                    <tr>
                                                        <td class="mailbox-name"><?php echo $history->bed_group; ?></td>
                                                        <td class="mailbox-name"><?php echo $history->bed; ?></td>
                                                        <td class="mailbox-name"><?php if($history->from_date !=''){ echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($history->from_date)); } ?></td>
                                                        <td class="mailbox-name"><?php if($history->to_date !=''){ echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($history->to_date)); } ?></td>
                                                        <td class="mailbox-name"><?php echo $this->lang->line($history->is_active); ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div> 
                                </div>
                                <!--Payment-->
                                <div class="tab-pane" id="payment">                                   
                                    <div class="box-tab-header">
                                      <h3 class="box-tab-title"><?php echo $this->lang->line('payment'); ?></h3>
                                      <div class="box-tab-tools">
                                           <?php if ($result["is_active"] == 'yes') {?>
                                                <button type="button" class="btn btn-info btn-sm" data-result_id="<?php echo $result['ipdid'] ?>" data-toggle="modal" data-target="#payMoney"><i class="fa fa-plus"></i> <?php echo $this->lang->line('make_payment'); ?></button>
                                    <?php }?>
                                      </div>
                                    </div>  
                                    <div class="download_label"><?php echo $this->lang->line('payment'); ?></div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover example">
                                            <thead>
                                                <th><?php echo $this->lang->line('transaction_id'); ?></th>
                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <th><?php echo $this->lang->line('note'); ?></th>
                                                <th><?php echo $this->lang->line('payment_mode'); ?></th>
                                                <th class="text-right"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                            </thead>
                                        <tbody>
                                    <?php
                                        $total_payment = 0;
                                        if (!empty($payment_details)) {
                                                $total_payment = 0;
                                                foreach ($payment_details as $payment) {
                                                    if (!empty($payment['amount'])) {
                                                        $total_payment += $payment['amount'];
                                                    }
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $this->customlib->getPatientSessionPrefixByType('transaction_id').$payment["id"] ?></td>
                                                            <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($payment['payment_date'], $this->customlib->getHospitalTimeFormat()); ?></td>
                                                            <td><?php echo $payment["note"] ?></td>
                                                            <td style="text-transform: capitalize;"><?php echo $this->lang->line(strtolower($payment["payment_mode"]))."<br>";
                                                                             if($payment['payment_mode'] == "Cheque"){
                                                                                  if($payment['cheque_no']!=''){
                                                            echo $this->lang->line("cheque_no"). ": ".$payment['cheque_no'];                                                           
                                                         echo "<br>";
                                                     }
                                                         if($payment['cheque_date']!='' && $payment['cheque_date']!='0000-00-00'){
                                                            echo $this->lang->line("cheque_date") .": ".$this->customlib->YYYYMMDDTodateFormat($payment['cheque_date']);
                                                        }                                                          

                                                          }
                                                         ?>
                                                        </td>
                                                           <td class="text-right"><?php echo $payment["amount"] ?></td> 
                                                           <td class="text-right"><?php if ($payment['payment_mode'] == "Cheque" && $payment['attachment'] != "")  {                                                       
                                                             ?>
                                                             <a href='<?php echo site_url('patient/dashboard/downloadreceipt/'.$payment['id']);?>' class='btn btn-default btn-xs'  title='<?php echo $this->lang->line('download'); ?>'><i class='fa fa-download'></i></a>
                                                             <?php
                                                         }
                                                                  ?>
         <a href="javascript:void(0)" class="btn btn-default btn-xs print_trans" data-record-id="<?php echo $payment['id'] ?>" data-loading-text="<i class='fa fa-circle-o-notch fa-spi'></i>" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('print'); ?>"><i class="fa fa-print"></i></a></td>
                                                        </tr>
                                                <?php }?>
                                                    
                                                </tbody>
                                                    <tr class="box box-solid total-bg">
                                                         <td  class="text-right" colspan="4"><?php echo $this->lang->line('total'); ?> : </td>                                                      
                                                        <td  class="text-right"><?php echo $currency_symbol . number_format($total_payment,2); ?>
                                                        </td>
                                                        <td></td>
                                                    </tr>
    <?php } ?>
                                        </table>
                                    </div>
                                </div>

                                <!--- Treatment history tab---->
                            <div class="tab-pane tab-content-height" id="treatment_history">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('treatment_history'); ?></h3>
                                    <div class="box-tab-tools">
                                          
                                    </div>    
                                </div><!--./box-tab-header-->                                
                                <div class="download_label"><?php echo $this->lang->line('treatment_history'); ?></div>
                                <div class="table-responsive">
                                 <table class="table table-striped table-bordered table-hover treatmentlist"  data-export-title="<?php echo $this->lang->line('treatment_history'); ?>">
                                    <thead>
                                        <th><?php echo $this->lang->line('ipd_no'); ?></th>
                                         <th><?php echo $this->lang->line('symptoms'); ?></th>
                                        <th><?php echo $this->lang->line('consultant'); ?></th>
                                        <th class="text-right" ><?php echo $this->lang->line('bed'); ?></th>                                        
                                    </thead>
                                    <tbody> 
                                    </tbody>
                                 </table>
                                </div><!--./table-responsive--> 
                            </div>
                            
                            <!--- Medication--> 
                            <div class="tab-pane" id="medication">
                                <div class="box-tab-header">
                                    <h3 class="box-tab-title"><?php echo $this->lang->line('medication'); ?></h3>
                                </div>
                                    <div class="table_inner">                                          
                                         <?php  if(!empty($medication))
                                       { ?>
                                        <table class="table table-striped table-bordered  mb0">
                                          <thead>
                                             <th class="hard_left"><?php echo $this->lang->line("date"); ?> </th>
                                             <th class="next_left table_inner_tdwidth"><?php echo $this->lang->line("medicine_name"); ?></th>
                                            <?php 
                                            if (!empty($max_dose)) {
                                                $dosage_count = $max_dose;
                                             } else{
                                                $dosage_count = 0;
                                             }                                            
                                            for ($x = 1; $x <= $dosage_count; $x++) { ?>
                                              
                                              <th class="table_inner_tdwidth"><?php echo $this->lang->line("dose") .$x  ;?></th>
                                           <?php }
                                            ?> 
                                    </thead>
                                    <tbody> 
                                       <?php
                                        $count = 1;
                                    foreach ($medication as $medication_key => $medication_value) 
                                    {
                                    $pharmacy_id = $medication_value['pharmacy_id'];
                                    $date = $medication_value['date']; ?>
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
                                                  { $medicine_id = $mvalue['dose_list'][$x]['pharmacy_id'];
                                                    $medicine_category_id = $mvalue['dose_list'][$x]['medicine_category_id'];
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
                                            } 
                                              ?>
                                  </tr>
                                <?php $subcount++; }
                                     }   ?>
                                    </tbody>                                
                                    </table>
                                <?php } else{ ?>
                                        <div class="alert alert-danger"><?php echo $this->lang->line('no_record_found'); ?></div>
                                     <?php } ?>                                  
                                </div> 
                            </div>
                        
                        <!--- Operation theatre--> 
                        <div class="tab-pane" id="operationtheatre">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('operation'); ?></h3>
                            </div>
                            <div class="download_label"><?php echo $result['patient_name'] . " " . $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <th><?php echo $this->lang->line("reference_no"); ?></th>
                                        <th><?php echo $this->lang->line("operation_date"); ?></th>
                                        <th><?php echo $this->lang->line("operation_name"); ?></th>
                                        <th><?php echo $this->lang->line("operation_category"); ?></th>
                                        <th><?php echo $this->lang->line("ot_technician"); ?></th>
                                         <?php if (is_array($fields_ot) || is_object($fields))
                                            {
                                                foreach ($fields_ot as $fields_key => $fields_value)
                                                { ?>
                                                   <th><?php echo ucfirst($fields_value->name); ?></th>
                                                <?php }
                                            }
                                        ?>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($operation_theatre)) {
                                            foreach ($operation_theatre as $ot_key => $ot_value) {
                                                ?>  
                                                <tr>    
                                                    <td><?php echo $this->customlib->getPatientSessionPrefixByType('operation_theater_reference_no').$ot_value["id"] ?></td>
                                                    <td><?php echo $this->customlib->YYYYMMDDHISTodateformat($ot_value["date"])?></td>
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
                                                        <a href='#' data-toggle='tooltip' title="<?php echo $this->lang->line('show'); ?>" class='btn btn-default btn-xs' data-target='#view_ot_modal' onclick='viewdetail("<?php echo $ot_value['id']; ?>")'>  <i class='fa fa-reorder'></i> </a>  
                                                    </td>
                                                </tr>                                            
                                            <?php } } ?>
                                    </tbody>
                                </table>
                            </div> 
                        </div> 

                            <!-- opstrical history  -->
                        <?php if($result['is_antenatal']==1){ ?>
                        <div class="tab-pane" id="obstetric_history">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('previous_obstetric_history'); ?></h3>                             
                            </div>                            
                            <div class="impbtnview-t9">
                                 
                            </div>                            
                            <div class="download_label"><?php echo $this->lang->line('previous_obstetric_history'); ?></div>
                            <div class="table-responsive">
                               <table class="table table-striped table-bordered table-hover example">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('place_of_delivery'); ?></th>
                                            <th><?php echo $this->lang->line('duration_of_pregnancy'); ?></th>
                                            <th><?php echo $this->lang->line('complication_in_pregnancy_or_puerperium'); ?></th>
                                            <th><?php echo $this->lang->line('birth_weight'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <th><?php echo $this->lang->line('infant_feeding') ?></th>
                                            <th><?php echo $this->lang->line('birth_status') ?></th>
                                            <th><?php echo $this->lang->line('alive'); ?> / <?php echo $this->lang->line('dead'); ?> <?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('death_cause') ?></th>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                             foreach($obstetric_history as $row){ ?>
                                                <tr>
                                                    <td><?php echo $row['place_of_delivery']; ?></td>
                                                    <td><?php echo $row['pregnancy_duration']; ?></td>
                                                    <td><?php echo $row['pregnancy_complications']; ?></td>
                                                    <td><?php echo $row['birth_weight']; ?></td>
                                                    <td><?php echo $this->lang->line(strtolower($row['gender'])); ?></td>
                                                    <td><?php echo $row['infant_feeding']; ?></td>
                                                    <td><?php echo $this->lang->line($row['alive_dead']); ?></td>
                                                    <td><?php if($row['date']){ echo $this->customlib->YYYYMMDDTodateFormat($row['date']); } ?></td>
                                                    <td><?php echo $row['death_cause']; ?></td>
                                                    <td class="text-right noExport">                                                    
                                                    <a href='javascript:void(0)' onclick="viewobstetric('<?php echo $row['id']; ?>')" data-record-id = "<?php echo $row['id']; ?>" class='btn btn-default btn-xs edit_obstetric'  data-toggle='tooltip' data-title="<?php echo $this->lang->line('view_history'); ?>" ><i class='fas fa fa-reorder'  ></i></a>                                                    
                                                    </td>
                                                </tr>

                                           <?php }  ?>
                                        </tbody>
                                </table>
                            </div> 
                        </div> 
                        <?php  }  ?>
                     <!-- opstrical history  -->

                     <!-- postnatal history -->
                      <div class="tab-pane" id="post_antenatal">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('postnatal_history'); ?></h3>                                
                            </div>
                            <div class="impbtnview-t9">                                 
                            </div>                            
                               <div class="download_label"><?php echo $this->lang->line('opd_details'); ?></div>
                            <div class="table-responsive overflow-visible">
                               <table class="table table-striped table-bordered table-hover example"  data-export-title="<?php echo $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('labor_time'); ?></th>
                                            <th><?php echo $this->lang->line('delivery_time'); ?></th>
                                            <th><?php echo $this->lang->line('routine_question'); ?></th>
                                            <th><?php echo $this->lang->line('general_remark'); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($postnatal_history as $row){ ?>
                                                <tr>
                                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($row['labor_time']); ?></td>
                                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($row['delivery_time']); ?></td>
                                                    <td><?php echo $row['routine_question']; ?></td>
                                                    <td><?php echo $row['general_remark']; ?></td>                                               
                                                </tr>
                                            <?php } ?>

                                        </tbody>
                                </table>
                            </div> 
                        </div>   
                        <!-- postnatal history -->

                        <!-- antinatal history -->
                          <div class="tab-pane" id="addantenatal">
                            <div class="box-tab-header">
                                <h3 class="box-tab-title"><?php echo $this->lang->line('antenatal'); ?></h3>                                
                            </div>                            
                            <div class="download_label"><?php echo $this->lang->line('opd_details'); ?></div>                            
                            <div class="table-responsive">
                               <table class="table table-striped table-bordered table-hover example"  data-export-title="<?php echo $this->lang->line('opd_details'); ?>">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('opd/ipd_no'); ?></th>
                                            <th><?php echo $this->lang->line('checkup_id'); ?></th>                                         
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($antenatallist as $row){ ?>
                                                <tr>
                                                    <?php if($row['status']=='ipd'){?>
                                                        <td><?php 
                                                        if($row['ipdid']!="" && $row['ipdid']!= null){ 
															echo $this->customlib->getPatientSessionPrefixByType('ipd_no').$row['ipdid'];
                                                        }  ?></td>
                                                     <?php }else{ ?>
                                                        <td><?php
                                                         if($row['opd_detail_id']!="" && $row['opd_detail_id']!= null){
                                                    echo $opd_id = $this->customlib->getPatientSessionPrefixByType('opd_no').$row['opd_detail_id'];
                                                        } ?></td>
                                                     <?php } ?>
                                                    <td><?php 
                                                    if($row['visit_details_id']!="" && $row['visit_details_id']!=null){
														echo $this->customlib->getPatientSessionPrefixByType('checkup_id').$row['visit_details_id'];
                                                    } ?></td>                                                    
                                                    <td><?php echo $this->customlib->YYYYMMDDHisTodateFormat($row['date']); ?></td>
                                                    <td class="text-right noExport">
                                                    <?php if($row['status']=='ipd'){ ?>                                                    
                                                    <a href='javascript:void(0)' onclick="viewipdantenatal('<?php echo $row['primary_id']; ?>')" data-record-id = "<?php echo $row['primary_id']; ?>" class='btn btn-default btn-xs edit_obstetric '  data-toggle='tooltip' data-title="<?php echo $this->lang->line('view_history'); ?>" ><i class='fas fa fa-reorder'   ></i></a>                                                         
                                                    <?php }else{ ?>
                                                    <a href='javascript:void(0)' onclick="viewantenatal('<?php echo $row['visit_details_id']; ?>')" data-record-id = "<?php echo $row['visit_details_id']; ?>" class='btn btn-default btn-xs edit_obstetric '  data-toggle='tooltip' data-title="<?php echo $this->lang->line('view_history'); ?>" ><i class='fas fa fa-reorder'   ></i></a>
                                                    <?php } ?>
                                                </td>
                                                </tr>
                                           <?php } ?>
                                        </tbody>
                                </table>
                            </div> 
                        </div>
                        <!-- antinatal history -->
						
						<!-- vitals -->
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
														<th><?php echo $vl["name"]; ?> <br> (<?php echo $vl["reference_range"]; ?> <?php echo $vl["unit"]; ?>) </th>
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
						<!-- vitals -->						
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div><!--./box box-primary-->
</section>
</div>

<div class="modal fade" id="patient_discharge" tabindex="-1" role="dialog" aria-labelledby="follow_up">   
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
               <div class="modalicon"> 
                     <div id='allpayments_print'>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('patient_discharge'); ?></h4>
            </div>
            <div class="modal-body pb0" id="patient_discharge_result">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('patient_details'); ?></h4>
            </div>
            <div id="ipd_patient_detail"></div>
        </div>
    </div>
</div>

<!-- Timeline -->
<div class="modal fade" id="myTimelineModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_timeline'); ?></h4> 
            </div>
            <form id="add_timeline" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="ptt10">    
                <div class="modal-body pt0 pb0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                                    <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id ;?>">
                                    <input id="timeline_title" name="timeline_title" placeholder="" type="text" class="form-control" />
                                    <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('date'); ?>
                                        <small class="req"> *</small>
                                    </label>
                                    <input id="timeline_date" name="timeline_date" value="<?php echo set_value('timeline_date', date($this->customlib->getHospitalDateFormat())); ?>" placeholder="" type="text" class="form-control date"  />
                                    <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea id="timeline_desc" name="timeline_desc" placeholder="" class="form-control"></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <div class="" style="margin-top:-5px; border:0; outline:none;">
                                        <input id="timeline_doc_id" name="timeline_doc" placeholder="" type="file" class="filestyle form-control" data-height="40"  value="<?php echo set_value('timeline_doc'); ?>" />
                                        <span class="text-danger">
<?php echo form_error('timeline_doc'); ?>
                                        </span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    
                </div>  
                <div class="modal-footer">   
                    <button type="submit" id="add_timelinebtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                </div>  
            </form>
        </div>
    </div> 
</div>

<!-- Edit Timeline -->
<div class="modal fade" id="myTimelineEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_timeline'); ?></h4> 
            </div>
            <form id="edit_timeline" accept-charset="utf-8" enctype="multipart/form-data" method="post" class="ptt10">
                <div class="modal-body pt0 pb0">
                        <div class="row">
                            <div class=" col-md-12">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                                    <input type="hidden" name="patient_id" id="epatientid" value="">
                                    <input type="hidden" name="timeline_id" id="etimelineid" value="">
                                    <input id="etimelinetitle" name="timeline_title" placeholder="" type="text" class="form-control"  />
                                    <span class="text-danger"><?php echo form_error('timeline_title'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>                                   
                                    <input type="text" name="timeline_date" class="form-control date" id="etimelinedate"/>
                                    <span class="text-danger"><?php echo form_error('timeline_date'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea id="timelineedesc" name="timeline_desc" placeholder=""  class="form-control"></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <div style="margin-top:-5px; border:0; outline:none;"><input id="etimeline_doc_id" name="timeline_doc" placeholder="" type="file"  class="filestyle form-control" data-height="40"  value="<?php echo set_value('timeline_doc'); ?>" />
                                        <span class="text-danger"><?php echo form_error('timeline_doc'); ?></span></div>
                                </div>                              
                            </div>
                        </div>                    
                </div> 
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="edit_timelinebtn" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
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
                <h4 class="modal-title"><?php echo $this->lang->line('discharged') . " " . $this->lang->line('summary'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>

<!-- -->
<div class="modal fade" id="prescriptionview" tabindex="-1" role="dialog" aria-labelledby="follow_up">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                 <div class="modalicon">
                    <div id='edit_deleteprescription'>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('prescription'); ?></h4>
            </div>
            <div class="modal-body pt0" id="getdetails_prescription"></div>
        </div>
    </div>
</div>

<!-- Modal -->
 <div id="payMoney" class="modal fade" role="dialog">
    <div class="modal-dialog">       
        <div class="modal-content mx-2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('make_payment') ?></h4>
            </div>
            <form id="payment_form" class="form-horizontal " method="POST">
                <div class="modal-body pb0">
                    <div class="form-group">
                        <label for="amount" class="col-sm-3 control-label"><?php echo $this->lang->line('payment_amount'); ?> (<?php echo $currency_symbol; ?>)  <small class="req">*</small></label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" value="<?= $total-$total_payment; ?>" name="deposit_amount" id="amount_total_paid" >
                            <input type="hidden" class="form-control" value="<?= $total-$total_payment; ?>" name="net_amount" id="net_amount" >
                            <span id="deposit_amount_error" class="text text-danger"></span>
                            <input type="hidden" name="payment_for" value="ipd">
                            <input type="hidden" name="id" value="<?php echo $ipdid;?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="pay_button" class="btn btn-info pull-right make_payment"><?php echo $this->lang->line('add') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="view_ot_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='action_detail_modal'>
                   </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('operation_details'); ?></h4>
            </div>
            <div class="modal-body min-h-3">
               <div id="show_ot_data"></div>
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

<!-- Obstetric prescription -->
<div class="modal fade" id="viewobstetrichistory" tabindex="-1" role="dialog" aria-labelledby="follow_up">   
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon">   
                    <spam id='edit_printhistory'></spam>
                    <spam id='edit_edithistory'></spam>
                    <spam id='edit_deletehistory'></spam>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('previous_obstetric_history'); ?></h4>
            </div>
            <div class="scroll-area">
            <div class="modal-body pt0 pb0">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Obstetric prescription -->

<!-- antinatal history modal -->
<div class="modal fade" id="findingview" tabindex="-1" role="dialog" aria-labelledby="follow_up">   
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <span id='edit_printfinding'></span>
                    <span id='edit_editfinding'></span>
                    <span id='edit_deletefinding'></span>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('antenatal_finding'); ?></h4>
            </div>
            <div class="scroll-area">
            <div class="modal-body pt0 pb0" id="getdetails_finding">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- antinatal history modal -->

<script type="text/javascript">
        $(document).on('click','.patient_discharge',function(){             
            var case_reference_id="<?php echo $case_reference_id;?>";
            var payment_modal=$('#patient_discharge');
            payment_modal.addClass('modal_loading'); 
            payment_modal.modal('show'); 
            $.ajax({
            url: base_url+'patient/dashboard/patient_discharge',
            type: "POST",
            data:{'module_type':'ipd','case_reference_id':case_reference_id},
            dataType: 'json',
        beforeSend: function() {
               }, 
        success: function (data) {
           $('.modal-body',payment_modal).html(data.page);
           $('.filestyle','#patient_discharge').dropify();
           $('.date','#patient_discharge').trigger("change");
              payment_modal.removeClass('modal_loading'); 
            },

        error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");           
               
          },
        complete: function() {
                payment_modal.removeClass('modal_loading');          
          }
        });       
    });

     $(document).on('click','.print_dischargecard',function(){
         var $this = $(this);
         var record_id=$this.data('recordId');
         var case_id=$this.data('case_id');   
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>patient/dashboard/print_dischargecard',
          type: "POST",
          data:{'id':record_id,'case_id':case_id,'module_type':'ipd'},
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

    function getRecord(ipdid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/getIpdDetails',
            type: "POST",
            data: {ipdid: ipdid},
            dataType: 'json',
            success: function (data) {
                $('#ipd_patient_detail').html(data.page); 
                holdModal('viewModal');
            },
        });
    }

   function getRecordsummary(id,ipdid) {
        $.ajax({
            url: '<?php echo base_url() ?>patient/dashboard/getsummaryDetails',
            type: "POST",
            data: {id: id,ipdid:ipdid},
            success: function (data) {
                $('#reportdata').html(data);
                $('#edit_deletebill').html("<a href='#' data-toggle='tooltip' onclick='printData(" + id + ","+ipdid+")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> ");
                holdModal('viewModalsummary');
            },
        });
    }

    $(document).ready(function (e) {
        $("#add_timeline").on('submit', (function (e) {
            var patient_id = $("#patient_id").val();
            e.preventDefault();
            $("#add_timelinebtn").button('loading');
            $.ajax({
                url: "<?php echo site_url("patient/dashboard/add_patient_timeline") ?>",
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
                    $("#add_timelinebtn").button('reset');
                },
                error: function (e) {
                    alert("Fail");
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#edit_timeline").on('submit', (function (e) {
            $("#edit_timelinebtn").button('loading');
            var patient_id = $("#patient_id").val();
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("patient/dashboard/edit_patient_timeline") ?>",
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
                    $("#edit_timelinebtn").button('reset');
                },
                error: function (e) {
                    alert("Fail");
                    console.log(e);
                }
            });
        }));
    });
    
    function editTimeline(id) {      
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/editTimeline',
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (data) {
                var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'MM', 'Y' => 'yyyy',]) ?>';
                var dt = new Date(data.timeline_date).toString(date_format);
                $("#etimelineid").val(data.id);
                $("#epatientid").val(data.patient_id);
                $("#etimelinetitle").val(data.title);
                $("#etimelinedate").val(dt);
                $("#timelineedesc").val(data.description);
                if (data.status == '') {

                } else
                {
                    $("#evisible_check").attr('checked', true);
                }
             
                holdModal('myTimelineEditModal');
                $('.filestyle').dropify();
            },
        });
    }

     function delete_timeline(id) {
       
        if (confirm('<?php echo $this->lang->line("delete_conform") ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>patient/dashboard/delete_patient_timeline/' + id,
                success: function (res) {
                    //successMsg(data.message);
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                }, error: function () {
                    alert("Fail")
                }
            });
        }
    }
	
     $(document).on('click','.print_charge',function(){
        var $this = $(this);
        var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>patient/dashboard/printCharge',
          type: "POST",
          data:{'id':record_id,'type':'ipd'},
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

    function view_prescription(id, ipdid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/prescription/getIPDPrescription/' + id + '/' + ipdid,
            success: function (res) {
                $("#getdetails_prescription").html(res);
            },
            error: function () {
                alert("Fail")
            }
        });

         $('#edit_deleteprescription').html("<a href='#prescription'' data-toggle='tooltip' data-original-title='Print'onclick='printprescription(" + id + "," + ipdid + ")'   data-original-title='<?php echo $this->lang->line('print'); ?>' ><i class='fa fa-print'></i></a>");
        holdModal('prescriptionview');
    }

  function printprescription(id, opdid) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/prescription/getIPDPrescription/' + id + '/' + opdid,
            type: 'POST',
            data: {payslipid: id, print: 'yes'},           
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }

    function getcharge_category(id) {
        var div_data = "";
        $("#charge_category").html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: id},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value=" + obj.name + ">" + obj.name + "</option>";
                });
                $('#charge_category').append(div_data);
            }
        });
    }

    $(document).on('click','.print_trans',function(){
        var $this = $(this);
        var record_id=$this.data('recordId')
       $this.button('loading');
        $.ajax({
          url: '<?php echo base_url(); ?>patient/dashboard/printTransaction',
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

    function get_Charges(charge_category, orgid) {
        $("#standard_charge").html("standard_charge");
        $("#schedule_charge").html("schedule_charge");
        $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/ipdCharge',
            type: "POST",
            data: {charge_category: charge_category, organisation_id: orgid},
            dataType: 'json',
            success: function (res) {
                if (res) {
                    $('#standard_charge').val(res.standard_charge);
                    $('#schedule_charge').val(res.org_charge);
                    $('#charge_id').val(res.id);
                    $('#org_id').val(res.org_charge_id);
                    if (res.org_charge == null) {
                        $('#apply_charge').val(res.standard_charge);
                    } else {
                        $('#apply_charge').val(res.org_charge);
                    }
                } else {
                    $('#standard_charge').val('0');
                    $('#schedule_charge').val('0');
                    $('#charge_id').val('0');
                    $('#org_id').val('0');
                }
            }
        });
    }

    function calculate() {
        var total_amount = $("#total_amount").val();
        var discount = $("#discount").val();
        var other_charge = $("#other_charge").val();
        var tax = $("#tax").val();
        var gross_total = parseInt(total_amount) + parseInt(other_charge) + parseInt(tax);
        var net_amount = parseInt(total_amount) + parseInt(other_charge) + parseInt(tax) - parseInt(discount);
        $("#gross_total").val(gross_total);
        $("#net_amount").val(net_amount);
        $("#save_button").show();
    }
</script>
<script type="text/javascript">
    function print(patientid, ipdid) {
        var total_amount = $("#total_amount").val();
        var discount = $("#discount").val();
        var other_charge = $("#other_charge").val();
        var gross_total = $("#gross_total").val();
        var tax = $("#tax").val();
        var net_amount = $("#net_amount").val();
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/dashboard/ipdBill/',
            type: 'POST',
            data: {patient_id: patientid, ipdid: ipdid, total_amount: total_amount, discount: discount, other_charge: other_charge, gross_total: gross_total, tax: tax, net_amount: net_amount},
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }
	
    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }
    ;

    $(document).ready(function (e) {
            $("#add_payment").on('submit', (function (e) {
                e.preventDefault();
            
            $.ajax({
                url: '<?php echo base_url(); ?>patient/pay/ipdpay',
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

    $('.addtimeline').click(function(){
      $('.filestyle').dropify();
    })

     $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.cheque_div').css("display", "block");
      }else{
        $('.cheque_div').css("display", "none");
      }
    });
    
    $(document).ready(function (e) {
        $('#payMoney').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
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

    function viewdetail(ot_id){
        $('#view_ot_modal').modal({backdrop:'static'});
        $.ajax({
            url: '<?php echo base_url(); ?>patient/dashboard/otdetails',
            type: "POST",
            data: {ot_id: ot_id},
            dataType: 'json',
            success: function (data) {
               $('#view_ot_modal').modal('show');
               $('#show_ot_data').html(data.page);     
               $('#action_detail_modal').html(data.actions);     
            },
        });
     }

    $(document).on('click','.make_payment',function(e){
    e.preventDefault(); // avoid to execute the actual submit of the form.
    var form = $(this).closest("form");
    var url = form.attr('action');    
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
});
   
    $('#payMoney').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    })   
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
( function ( $ ) {
     var id = "<?php echo $patient_id; ?>"; 
    
    'use strict';
    $(document).ready(function () {
       initDatatable('treatmentlist','patient/dashboard/getipdtreatmenthistory/'+id);
    });
} ( jQuery ) )
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
<script src="<?php echo base_url()?>backend/js/Chart.min.js"></script>
<script type="text/javascript">
         Chart.types.Doughnut.extend({
            name: "DoughnutTextInside",
            showTooltip: function () {
                this.chart.ctx.save();
                Chart.types.Doughnut.prototype.showTooltip.apply(this, arguments);
                this.chart.ctx.restore();
            },
            draw: function () {
                Chart.types.Doughnut.prototype.draw.apply(this, arguments);

                var width = this.chart.width,
                        height = this.chart.height;
                var fontSize = (height / 190).toFixed(2);
                this.chart.ctx.font = fontSize + "em Verdana";
                this.chart.ctx.textBaseline = "middle";
                var text = "<?php echo $donut_graph_percentage; ?>%",
                        textX = Math.round((width - this.chart.ctx.measureText(text).width) / 2),
                        textY = height / 2;
                this.chart.ctx.fillText(text, textX, textY);
            }
        });
    
       var data = [{
                lebel: 'complete',
                value: <?php echo round($used_credit_limit); ?>,
                color: "#f40000"
            }, {
                value: <?php echo round($balance_credit_limit); ?>,
                color: "#4CAF50"
            }
        ];

        var DoughnutTextInsideChart = new Chart($('#pieChart')[0].getContext('2d')).DoughnutTextInside(data, {
            responsive: true
        });
</script>

<script>
 function viewobstetric(id){
        $.ajax({
            url: base_url+'patient/antenatal/getobstetrichistory',
            dataType:'JSON',
            data:{'id':id} ,
            type:"POST",
            beforeSend: function() {
                  
            },
            success: function (res){               
                    $('#viewobstetrichistory').modal();                    
                    $('.modal-body',"#viewobstetrichistory").html(res.page);         
                    $('#edit_printhistory').html( "<a href='#'' data-toggle='tooltip' onclick='printobstetrichistory(" + id + ")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a>" );                    
               }
           });
    }

    $(document).ready(function (e) {
        $('#add_obstetric,#viewobstetrichistory,#add_obstetric').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    });

     function printobstetrichistory(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/antenatal/printobstetrichistory' ,
            type: 'post',
            data: { id: id },
            dataType:"JSON",
            success: function (result) {
                popup(result.page);
            }
        });
    }

    function viewipdantenatal(antenatal_id) { 
        $.ajax({
            url: '<?php echo base_url(); ?>patient/antenatal/getipdantenatalfindings/' + antenatal_id ,
            success: function (res) {
                $("#getdetails_finding").html(res);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        }); 

        holdModal('findingview');
    }

     function viewantenatal(visitid) {
        $.ajax({
            url: '<?php echo base_url(); ?>patient/antenatal/getantenatalprescription/' + visitid ,
            success: function (res) {
                console.log(res);
                $("#getdetails_finding").html(res);
            },
            error: function () {
                alert("<?php echo $this->lang->line('fail'); ?>")
            }
        }); 

        holdModal('findingview');
    }

</script>