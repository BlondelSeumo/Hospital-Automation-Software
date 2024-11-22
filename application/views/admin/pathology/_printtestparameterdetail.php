<div class="fixed-print-header">    
    <?php if (!empty($print_details[0]['print_header'])) { ?>
            <div class="pprinta4">
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
<div class="print-area p-1">
	<div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">  
                    <div class="row">
                        <div class="col-md-12">                       
                        <div class="col-md-6">                       
                            <table class="noborder_table">	
								<tr>
									<th width="20%"><?php echo $this->lang->line('bill_no'); ?></th>
									<td width="30%"><?php echo $this->customlib->getSessionPrefixByType('pathology_billing').$head_result->id; ?></td>
								</tr>
								<tr>
									<th width="20%"><?php echo $this->lang->line('patient'); ?></th>
									<td width="30%"><?php echo composePatientName($head_result->patient_name,$head_result->patient_id); ?></td>
								</tr>								
								<tr>
									<th width="20%"><?php echo $this->lang->line('age'); ?></th>
									<td width="30%"><?php echo $this->customlib->getPatientAge($head_result->age,$head_result->month,$head_result->day); ?></td>
								</tr>								
								<tr>
									<th width="20%"><?php echo $this->lang->line('doctor_name'); ?></th>
									<td width="30%"><?php echo $head_result->doctor_name; ?></td>
								</tr>
							</table>                            
                        </div>
                        <div class="col-md-6 text-right"> 
							<table class="noborder_table">	
								<tr>
									<th width="20%"><?php echo $this->lang->line('date'); ?></th>
									<td width="30%"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($head_result->date, $this->customlib->getHospitalTimeFormat()); ?></td>
								</tr>
								<tr>
									<th width="20%"><?php echo $this->lang->line('case_id'); ?></th>
									<td width="30%"><?php echo $head_result->case_reference_id; ?></td>
								</tr>
								<tr>
									<th width="20%"><?php echo $this->lang->line('gender'); ?></th>
									<td width="30%"><?php echo $head_result->gender; ?></td>
								</tr>
							</table>					
							                            
                        </div>                       
                    </div>
                    </div>
					<div class="row">
                    <?php if(!empty($result)){ 
					foreach($result as $row){ ?>                    
                        <div class="col-md-12">
                        <div class="col-md-12">
                           <h4 class="text-center">
      <?php echo $row['test_name']; ?> <?php echo "(".$row['short_name'].")"; ?>
</h4>
                               <table class="print-table">
                             <thead>
                                <tr class="line">
                                   <td>#</td>
                                   <td class="text-left"><?php echo $this->lang->line('test_parameter_name'); ?></td>                               
                                   <td class="text-center"><?php echo $this->lang->line('reference_range'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('report_value'); ?></td>
                                </tr>
                             </thead>
                             <tbody>
                                <?php
                      $row_counter=1;
                        foreach ($result[$row['id']]['pathology_parameter'] as $parameter_key=> $parameter_value) {
                             $row_cls="";
                                $str= explode("-", $parameter_value->reference_range);
                                $min_range = $str[0];
                                $max_range = $str[1];
                                if(is_numeric($parameter_value->pathology_report_value) &&  ($parameter_value->pathology_report_value < $min_range || $parameter_value->pathology_report_value > $max_range ) )
                                {
                                  $row_cls="bold";
                                }
                            ?>                        
                         <tr class="<?php echo $row_cls;?>">
                            <td><?php echo $row_counter; ?></td>
                            <td class="text-left"><?php echo $parameter_value->parameter_name; ?><br/>
                              <div class="bill_item_footer text-muted"><label><?php if($parameter_value->description !=''){ echo $this->lang->line('description').': ';} ?></label> <?php echo $parameter_value->description; ?></div></td> 
                            <td class="text-center"><?php echo $parameter_value->reference_range." ".$parameter_value->unit_name; ?></td>
                            <td class="text-right"><?php if($parameter_value->pathology_report_value){ echo $parameter_value->pathology_report_value." ".$parameter_value->unit_name;} ?></td>                             
                        </tr>  
                       <?php
                    $row_counter++;
                        }
                        ?>
                         <?php if(!empty($parameter_value->pathology_result)){ ?> 
                        <tr> <td colspan="4"><p><span class="font-bold"><?php echo $this->lang->line('result'); ?>: </span> <?php echo nl2br($parameter_value->pathology_result); ?></p></td></tr>                             
                        <?php
                        } ?>
                                
                             </tbody>
                          </table>
                        </div>
                        </div>
                    
                  <?php } } ?>
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