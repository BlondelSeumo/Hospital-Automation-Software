<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat(); 
?>
<div class="row">
  <div class="col-md-12">
    <div class="row">
	      <div class="table-responsive">
            <div class="col-md-5">
              <table class="table table-hover table-sm">
                <tr>
                  <td class="col-lg-3"><label><?php echo $this->lang->line('bill_no'); ?></label></td>
                  <td class="col-lg-3"><?php echo $bill_prefix.$result->id ?></td>          
                </tr>        
                <tr>
                  <td><label><?php echo $this->lang->line('doctor_name'); ?></label></td>
                  <td><?php echo $result->doctor_name ?></td>         
                </tr>   
                <tr>    
                  <td><label><?php echo $this->lang->line('blood_group'); ?></label></td>
                  <td><?php echo $result->blood_group_name ?></td>
                </tr>    
                <tr>        
                  <td><label><?php echo $this->lang->line('generated_by'); ?></label></td>
                  <td><?php echo composeStaffNameByString($result->name,$result->surname,$result->employee_id); ?></td>
                </tr>
                <tr>
                  <td><label><?php echo $this->lang->line('note'); ?></label></td>
                  <td><?php echo $result->note ?></td>
                </tr>
      
                <?php  if (!empty($fields)) {
                    foreach ($fields as $fields_key => $fields_value) {
                        $display_field = $result->{"$fields_value->name"};
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $result->{"$fields_value->name"} . " target='_blank'>" . $result->{"$fields_value->name"} . "</a>";
                        }
                        ?>
                        <tr>
                          <td><label><?php echo $fields_value->name ;?></label></td><td><?php echo $display_field ; ?></td>
                        </tr>
                    <?php }
                }?>
              </table>
          </div>      
          <div class="col-md-2">
            <table class="table table-hover table-sm">
              <tr>
                <td class="col-lg-3"><label><?php echo $this->lang->line('case_id'); ?></label></td>
                <td class="col-lg-3"><?php echo $result->case_reference_id ?></td>          
              </tr>
              <tr>
                <td><label><?php echo $this->lang->line('age'); ?></label></td>
                <td><?php echo $this->customlib->getPatientAge($result->age,$result->month,$result->day);  ?> </td>          
              </tr>
              <tr>
                <td><label><?php echo $this->lang->line('mobile_no'); ?></label></td>
                <td><?php echo $result->mobileno ?></td>
              </tr>
              <tr>
                <td><label><?php echo $this->lang->line('address'); ?></label></td>
                <td><?php echo $result->address ?></td>
              </tr>
            </table>
        </div>
        <div class="col-md-2">
          <table class="table table-hover table-sm">       
            <tr>
              <td><label><?php echo $this->lang->line('patient_name'); ?></label></td>
              <td><?php echo $result->patient_name." (".$result->patient_id.")" ?></td>         
            </tr>
            <tr>
              <td><label><?php echo $this->lang->line('gender'); ?></label></td>
              <td><?php echo $result->gender ?></td>      
            </tr>            
            <tr>
              <td><label><?php echo $this->lang->line('email'); ?></label></td>
              <td><?php echo $result->email ?></td>
            </tr>
          </table>
        </div>      
        <div class="col-md-3">
          <table class="table table-hover table-sm">
            <tr>      
              <td><label><?php echo $this->lang->line('total'); ?></label></td>
              <td class="col-lg-3 text text-right"><?php if (!empty($result->total)) {
                echo $currency_symbol.$result->total ;
              }  ?></td>
            </tr>
          <tr>        
            <td class="col-lg-3"><label><?php echo $this->lang->line('total_discount'); ?> <?php if (!empty($result->discount)) {
              echo "(".$result->discount_percentage."%) " ;
            }  ?></label></td>
            <td class="col-lg-3 text text-right"><?php if (!empty($result->discount)) {
              echo $currency_symbol.$result->discount ;
            }  ?></td>
          </tr>
          <tr>         
            <td><label><?php echo $this->lang->line('total_tax'); ?></label></td>
            <td class="col-lg-3 text text-right"><?php if (!empty($result->tax)) {
              echo $currency_symbol.$result->tax ;
            }  ?></td>
          </tr>        
          <tr>   
            <td><label><?php echo $this->lang->line('net_amount'); ?></label></td>
            <td class="col-lg-3 text text-right"><?php if (!empty($result->net_amount)) {
              echo $currency_symbol.$result->net_amount ;
            }  ?></td>          
          </tr>
          <tr>    
            <td><label><?php echo $this->lang->line('total_deposit'); ?></label></td>
            <td class="col-lg-3 text text-right"><?php if (!empty($result->total_deposit)) {
              echo $currency_symbol.$result->total_deposit ;
            }  ?></td>          
          </tr>      
          <tr>    
            <td><label><?php echo $this->lang->line('balance_amount'); ?></label></td>
            <td class="col-lg-3 text text-right"><?php 
              echo $currency_symbol.amountFormat((($result->total-$result->discount) + $result->tax)-$result->total_deposit) ;
            ?></td>          
          </tr>  
        </table>
      </div>
     </div> 	
    </div>
    <!-- //============= -->
    <div class="table-responsive">
      <div class="">
        <table class="table table-hover table-sm">
                             <thead>
                                <tr class="line">
                                   <td><strong>#</strong></td>
                                   <td><strong><?php echo $this->lang->line('test_name'); ?></strong></td>
                                   <td width="25%"><strong><?php echo $this->lang->line('sample_collected'); ?></strong></td>
                                   <td><strong><?php echo $this->lang->line('expected_date'); ?></strong></td>
                                   <td><strong><?php echo $this->lang->line('approved_by'); ?></strong></td>
                                   <td class="text-right"><strong><?php echo $this->lang->line('tax'); ?></strong></td>
                                   <td class="text-right"><strong><?php echo $this->lang->line('amount'); ?></strong></td>
                                   <td class="text-right"><strong><?php echo $this->lang->line('action'); ?></strong></td>
                                </tr>
                             </thead>
                             <tbody>
                                <?php
                           $row_counter=1;
                        foreach ($result->pathology_report as $report_key=> $report_value) {
                          
                              $tax_amount = ($report_value->apply_charge*$report_value->tax_percentage/100);
                              $taxamount  = amountFormat($tax_amount)
                            ?>
                            <tr>
                                <td><?php echo $row_counter; ?></td>
                                <td><strong><?php echo $report_value->test_name; ?></strong>
                                  <br/>
                                  <?php echo "(".$report_value->short_name.")"; ?>
                                </td>                               
                                 <td class="text-left">                                 	
                                 	<label>
                                 <?php echo composeStaffNameByString($report_value->collection_specialist_staff_name,$report_value->collection_specialist_staff_surname,$report_value->collection_specialist_staff_employee_id); ?>
                                 </label>                                 	
                                 	 <br/>
                                 	 <label for=""><?php echo $this->lang->line('pathology'); ?> : </label>                                 	
                                 		<?php
                                 	echo $report_value->pathology_center; 
                                 	?>
                                 	<br/>
                                 	 <?php if($report_value->collection_date){ echo $this->customlib->YYYYMMDDTodateFormat($report_value->collection_date); } ?>
                                 	</td>
                                 <td>
                                 	<?php
                                 	if($report_value->reporting_date){
                                 	echo  $this->customlib->YYYYMMDDTodateFormat($report_value->reporting_date); } ?>
                                 	
                                 	</td>
                                 	   <td class="text-left">
                                 	 	 <label for=""><?php echo $this->lang->line('approved_by'); ?> : </label>
                                 	 	<?php      
                                 	echo composeStaffNameByString($report_value->approved_by_staff_name,$report_value->approved_by_staff_surname,$report_value->approved_by_staff_employee_id);
                                 	 ?>
                                 	 <br/>
                                 	<?php  if($report_value->parameter_update){                              
                                 	echo  $this->customlib->YYYYMMDDTodateFormat($report_value->parameter_update);
                                 	} ?>                                 		
                                 	</td>
                                <td class="text-right"><?php echo $currency_symbol.$taxamount." (".$report_value->tax_percentage."%)"; ?></td>
                                 <td class="text-right"><?php echo $currency_symbol.$report_value->apply_charge; ?></td>
                               <td class="text-right white-space-nowrap">
                                <?php 
                                if($is_bill){ ?>
                                    <a href='javascript:void(0)'  data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id='<?php echo $report_value->id;?>' class='btn btn-default btn-xs add_collection' data-toggle='tooltip' title='<?php echo $this->lang->line("add_edit_collection_person"); ?>'><i class='fa fa-user-plus'></i></a>
<?php 
if($report_value->collection_specialist_staff_employee_id != ""){
    ?>
                                  <a href='javascript:void(0)'  data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id='<?php echo $report_value->id;?>' class='btn btn-default btn-xs add_report' data-toggle='tooltip' title='<?php echo $this->lang->line("add_edit_report"); ?>'><i class='fa fa-flask'></i></a>
                                  <?php
                                }
                                ?>
                              <?php  }
                                ?>                               
                               		<a href='javascript:void(0)'  data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id='<?php echo $report_value->id;?>' class='btn btn-default btn-xs print_pathology_report' data-toggle='tooltip' title='<?php echo $this->lang->line("print"); ?>'><i class='fa fa-print'></i></a>
    <?php 
if($report_value->pathology_report != ""){
  ?>
  <a href="<?php echo site_url('admin/pathology/downloadReport/'.$report_value->id) ?>" class='btn btn-default btn-xs' data-toggle='tooltip' title='<?php echo $this->lang->line("download"); ?>'><i class="fa fa-download"></i></a>
  <?php
}
     ?> </td>                             
                        </tr>
                               
                        <?php
                    $row_counter++;
                        }
                        ?>                        
                              
             </tbody>
          </table>
      </div>
    </div>
  </div>
</div>