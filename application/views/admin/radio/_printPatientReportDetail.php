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
</diV>      
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
    <div class="col-md-12">
      
      <div class="card">
        <div class="card-body">  
            <div class="row">
                <div class="col-md-6">
					<table class="noborder_table">	
						<tr>
                            <th width="20%"><?php echo $this->lang->line('bill_no'); ?></th>
                            <td width="30%"><?php echo $bill_prefix.$result->radiology_bill_id; ?></td>
                        </tr>
						<tr>
                            <th width="20%"><?php echo $this->lang->line('patient'); ?></th>
                            <td width="30%"><?php echo composePatientName($result->patient_name,$result->patient_id); ?></td>
                        </tr>
						<tr>
                            <th width="20%"><?php echo $this->lang->line('case_id'); ?></th>
                            <td width="30%"><?php echo $result->case_reference_id; ?></td>
                        </tr>
						<tr>
                            <th width="20%"><?php echo $this->lang->line('age'); ?></th>
                            <td width="30%"><?php echo $this->customlib->getPatientAge($result->age,$result->month,$result->day); ?></td>
                        </tr>
						<tr>
                            <th width="20%"><?php echo $this->lang->line('gender'); ?></th>
                            <td width="30%"><?php echo $result->gender; ?></td>
                        </tr>
						<?php if($result->doctor_name){ ?>
						<tr>
                            <th width="20%"><?php echo $this->lang->line('doctor_name'); ?></th>
                            <td width="30%"><?php echo $result->doctor_name; ?></td>
                        </tr>
						<?php } ?>						
					</table>                     
                </div>
                <div class="col-md-6 text-right">
					<table class="noborder_table">	
						<?php if($result->parameter_update){ ?>
						<tr>
                            <th width="25%"><?php echo $this->lang->line('approve_date'); ?></th>
                            <td width="25%"><?php echo $this->customlib->YYYYMMDDTodateFormat($result->parameter_update); ?></td>
                        </tr>
						<?php } if($result->collection_date){ ?>						
						<tr>
                            <th width="25%"><?php echo $this->lang->line('report_collection_date'); ?></th>
                            <td width="25%"><?php  echo $this->customlib->YYYYMMDDTodateFormat($result->collection_date); ?></td>
                        </tr>
						<?php } if($result->reporting_date){ ?>	
						<tr>
                            <th width="25%"><?php echo $this->lang->line('expected_date'); ?></th>
                            <td width="25%"><?php echo $this->customlib->YYYYMMDDTodateFormat($result->reporting_date);?></td>
                        </tr>
						<?php } ?>
						<tr>
                            <th width="25%"><?php echo $this->lang->line('collection_by'); ?></th>
                            <td width="25%"><?php echo composeStaffNameByString($result->collection_specialist_staff_name,$result->collection_specialist_staff_surname,$result->collection_specialist_staff_employee_id); ?></td>
                        </tr>
						<?php  if($result->radiology_center){ ?>
						<tr>
                            <th width="25%"><?php echo $this->lang->line('radiology_center'); ?></th>
                            <td width="25%"><?php echo $result->radiology_center ?></td>
                        </tr>
						<?php } ?>
					</table>                              
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                   <h4 class="text-center">
<?php echo $result->test_name; ?>
<?php echo "(".$result->short_name.")"; ?>
</h4>
                       <table class="print-table">
                     <thead>
                        <tr class="line">
                           <td>#</td>
                           <td class="text-left"><?php echo $this->lang->line('test_parameter_name'); ?></td>
                           <td class="text-center"><?php echo $this->lang->line('reference_range'); ?></td>
                           <td><?php echo $this->lang->line('report_value'); ?></td>
                        </tr>
                     </thead>
                     <tbody>
                        <?php
              $row_counter=1;
                foreach ($result->radiology_parameter as $parameter_key=> $parameter_value) {
                  $level_exceeded= check_report_level_exceed($parameter_value->reference_range,$parameter_value->range_from,$parameter_value->range_to,$parameter_value->radiology_report_value);
                ?>                        
                <tr >
                    <td><?php echo $row_counter; ?></td>
                    <td class="text-left">
                      <?php echo $parameter_value->parameter_name; ?><br/>
                              <div class="bill_item_footer text-muted"><label><?php if($parameter_value->description !=''){ echo $this->lang->line('description').': ';} ?></label> <?php echo $parameter_value->description; ?></div>
                      </td> 
                    <td class="text-center">                    
                  <?php echo $parameter_value->reference_range." ".$parameter_value->unit_name; ?>                            
                      </td>                    
                     <td><?php  
              
                  
                  echo  ($level_exceeded)? "<span class='text-danger'>".$parameter_value->radiology_report_value." ".$parameter_value->unit_name."</span>":(($parameter_value->radiology_report_value == "") ? "" :"<span>".$parameter_value->radiology_report_value." ".$parameter_value->unit_name."</span>");   

                  
                  ?>
                  
                
                
                
                </td>
                </tr>                       
                <?php
            $row_counter++;
                }
                ?>  
                    </tbody>
                  </table>
                </div>
            </div>
             <?php if($result->radiology_result!=""){ ?>
                    <div class="row">
                        <div class="col-md-12">
                             <p><span class="font-bold"><?php echo $this->lang->line('result'); ?>: </span> <?php echo nl2br($result->radiology_result); ?></p>                           
                        </div>
                    </div>
                <?php } ?>
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