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
  <div class="container">
    <div class="row">
      <div class="col-12">
        
        <div class="card">
          <div class="card-body">  
            <div class="row">
              <div class="col-md-6">
                <p><?php echo $this->lang->line('bill_no'); ?> : <?php echo$this->customlib->getPatientSessionPrefixByType('radiology_billing') .$result->radiology_bill_id; ?></p>
                <p><?php echo $this->lang->line('case_id'); ?> : <?php echo $result->case_reference_id; ?></p>
                <p><?php echo $this->lang->line('patient'); ?> : <?php echo composePatientName($result->patient_name,$result->patient_id); ?></p>
                <p><?php echo $this->lang->line('age'); ?> : <?php echo $this->customlib->getPatientAge($result->age,$result->month,$result->day); ?></p>
                <p><?php echo $this->lang->line('gender'); ?> : <?php echo $result->gender; ?></p>
                <p><?php echo $this->lang->line('collection_by'); ?> : <?php echo composeStaffNameByString($result->collection_specialist_staff_name,$result->collection_specialist_staff_surname,$result->collection_specialist_staff_employee_id); ?></p>
              <p><?php echo $this->lang->line('radiology_center'); ?> : <?php echo $result->radiology_center ?></p>  
              </div>
              <div class="col-md-6 text-right">                          
                <p><span class="text-muted"><?php echo $this->lang->line('approve_date'); ?>: </span> <?php echo $this->customlib->YYYYMMDDTodateFormat($result->parameter_update); ?></p>
                <p><span class="text-muted"><?php echo $this->lang->line('report_collection_date'); ?>: </span> <?php echo $this->customlib->YYYYMMDDTodateFormat($result->collection_date); ?></p>
                <p><span class="text-muted"><?php echo $this->lang->line('expected_date'); ?>: </span> <?php echo $this->customlib->YYYYMMDDTodateFormat($result->reporting_date); ?></p>   
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <h4 class="text-center">
                  <?php echo $result->test_name; ?>
                  <br/>
                  <?php echo "(".$result->short_name.")"; ?>
                </h4>
                <table class="print-table">
                  <thead>
                    <tr class="line">
                      <th>#</th>
                      <th class="text-left"><?php echo $this->lang->line('test_parameter_name'); ?> </th>
                      <th class="text-left"><?php echo $this->lang->line('reference_range'); ?></th>
                      <th class="text-left"><?php echo $this->lang->line('report_value'); ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $row_counter=1;
                    foreach ($result->radiology_parameter as $parameter_key=> $parameter_value) {
						
						$level_exceeded= check_report_level_exceed($parameter_value->reference_range,$parameter_value->range_from,$parameter_value->range_to,$parameter_value->radiology_report_value);
                ?>                        
							                       
                    <tr class="line">
                      <td><?php echo $row_counter; ?></td>
                      <td class="text-left">
                        <?php echo $parameter_value->parameter_name; ?><div class="bill_item_footer text-muted"><label><?php if($parameter_value->description !=''){ echo $this->lang->line('description').': ';} ?></label> <?php echo $parameter_value->description; ?></div>
                            
                        </td> 
                      <td class="text-left"> 
                      
                      <?php echo $parameter_value->reference_range." ".$parameter_value->unit_name; ?>
                                                        
                        </td>
                       <td class="text-left">    
                     <?php                      
                  echo  ($level_exceeded)? "<span class='text-danger'>".$parameter_value->radiology_report_value." ".$parameter_value->unit_name."</span>":"<span>".$parameter_value->radiology_report_value." ".$parameter_value->unit_name."</span>"; ?>
                       </td>                             
                      </tr>       
                       <?php 
                    $row_counter++;
                        }

                        if($parameter_value->radiology_result!=""){ ?> 
                          <tr> <td colspan="4"><p><b><?php echo $this->lang->line('result'); ?>: </b> <?php echo nl2br($parameter_value->radiology_result); ?></p></td></tr>                             
                        <?php
                        }


                        ?>              
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