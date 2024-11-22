<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
 <input type="hidden" name="pathology_bill_id" value="<?php echo $result->pathology_bill_id ?>">  
 <input type="hidden" name="pathalogy_center" value="<?php echo $result->pathology_center ?>"> 
 <input type="hidden" name="collected_date" value="<?php echo $this->customlib->YYYYMMDDTodateFormat($result->collection_date); ?>"> 
 <input type="hidden" name="collected_by" value="<?php echo composeStaffNameByString($result->collection_specialist_staff_name,$result->collection_specialist_staff_surname,$result->collection_specialist_staff_employee_id); ?>">
 <input type="hidden" name="collected_id" value="<?php echo  $result->collection_specialist; ?>">
<div class="row">
	<div class="col-md-12">    
		 <div class="table-responsive">
	   <table class="table table-hover table-sm">
		<tr>
			<td><label><?php echo $this->lang->line('test_name'); ?></label></td>
      <td><?php echo $result->test_name.' ('.$result->short_name.')'; ?></td>
      <td><label><?php echo $this->lang->line('expected_date'); ?></label></td>
      <td><?php echo $this->customlib->YYYYMMDDTodateFormat($result->reporting_date); ?></td>
		</tr>
		<tr>
			<td><label><?php echo $this->lang->line('approve_date'); ?></label></td>
      <td><?php if($result->parameter_update){ echo $this->customlib->YYYYMMDDTodateFormat($result->parameter_update);} ?></td>
		  <td><label><?php echo $this->lang->line('date_of_collection'); ?></label></td>
      <td><?php echo $this->customlib->YYYYMMDDTodateFormat($result->collection_date); ?></td>
		</tr>
			<tr>
			<td><label><?php echo $this->lang->line('collection_by'); ?></label></td>
			<td><?php echo composeStaffNameByString($result->collection_specialist_staff_name,$result->collection_specialist_staff_surname,$result->collection_specialist_staff_employee_id); ?></td>
			<td><label><?php echo $this->lang->line('pathology_center'); ?></label></td>
      <td><?php echo $result->pathology_center ?></td>
		</tr>		
	</table>
</div>

  <div class="row">
    <div class="col-md-4">
         <div class="form-group">
    <label for="approved_by"><?php echo $this->lang->line('approved_by'); ?><small class="req"> *</small></label>  
         <select class="form-control select2" name="approved_by" id="approved_by">
         <option value=""><?php echo $this->lang->line('select') ?></option>
        <?php 
            foreach ($staff as $staff_key => $staff_value) {         ?>
         <option value="<?php echo $staff_value['id'] ?>" <?php echo set_select('approved_by', $staff_value['id'], (set_value('approved_by', $result->approved_by) == $staff_value['id'] ) ? TRUE : FALSE ); ?>><?php echo $staff_value['name']." ".$staff_value['surname']." (".$staff_value['employee_id'].")" ?></option>
         <?php
            }
         ?>
    </select>
  </div> 
    </div>
    <div class="col-md-4">
         <div class="form-group">
    <label for="approved_by"><?php echo $this->lang->line('approve_date'); ?><small class="req"> *</small></label>
         <input class="form-control date" value="<?php if($result->parameter_update!=''){ echo $this->customlib->YYYYMMDDTodateFormat($result->parameter_update); }else{ echo $this->customlib->YYYYMMDDTodateFormat(date('Y-m-d')); }  ?>" name="approve_date" type="text" id="approve_date">
  </div>
    </div>
     <div class="col-md-4">
         <div class="form-group">         
         <label for="approved_by"><?php echo $this->lang->line('upload_report'); ?></label>  
         <input class="filestyle form-control" type='file' name='file' id="attachment_report" size='20' /> 
    <?php 
if($result->pathology_report != ""){
  ?>
  <a href="<?php echo site_url('admin/pathology/downloadReport/'.$result->id) ?>" ><i class="fa fa-download"></i> <?php echo $result->report_name; ?></a>
  <?php
}
     ?>  
  </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">       
        <label for=""><?php echo $this->lang->line('result'); ?></label>  
        <textarea name ="test_result" id="test_result" rows="2" class="form-control" value="<?php echo $result->pathology_result ;  ?>"><?php echo $result->pathology_result ;  ?></textarea>
    </div>
  </div>
  <br>

<!-- //============= -->
 <div class="table-responsive">
  <input type="hidden" name="pathology_report_id" value="<?php echo $result->id;?>">
	   <table class="table table-hover table-sm">
        <thead>
                                <tr class="line">
                                   <td style="width: 5%">#</td>
                                   <td class="text-left"style="width: 45%"><?php echo $this->lang->line('test_parameter_name'); ?><small class="req"> *</small></td>
                                   <td class="text-left" style="width: 25%"><?php echo $this->lang->line('report_value'); ?></td>
                                   <td class="text-right" style="width: 25%"> <?php echo $this->lang->line('reference_range'); ?></td>
                                </tr>
                             </thead>
        <tbody>
                                <?php
                      $row_counter=1;
                        foreach ($result->pathology_parameter as $parameter_key=> $parameter_value) {
                            ?>
                           <input type="hidden" name="pathology_parameterdetails[]" value="<?php echo $parameter_value->id; ?>">
                            <input type="hidden" name="pathology_reference_range_<?php echo $parameter_value->id;?>" value="<?php echo $parameter_value->reference_range; ?>">
                        <tr>
                            <td><?php echo $row_counter; ?></td>
                            <td>
                              <?php echo $parameter_value->parameter_name; ?><br/>
                              <div class="bill_item_footer text-muted"><label><?php if($parameter_value->description !=''){ echo "<b>".$this->lang->line('description')."</b>".': ';} ?></label> <?php echo $parameter_value->description; ?></div>                                  
                              </td> 
                             <td>
      <div class="form-group"> 
    <div class="input-group">
        <input type="hidden" class="form-control left-border-none" name="prev_id_<?php echo $parameter_value->id;?>" value="<?php echo $parameter_value->pathology_report_parameterdetail_id;?>">
        <input type="text" class="form-control left-border-none" name="pathology_parameter_<?php echo $parameter_value->id;?>" value="<?php echo $parameter_value->pathology_report_value;?>">
        <span class="input-group-addon transparent" id="start-date" name="pathology_parameterdetails_<?php echo $parameter_value->id; ?>"> <?php echo $parameter_value->unit_name; ?></span>
    </div>
</div>                               
                             </td> 
                              <td class="text-right">
                            
                          <?php echo $parameter_value->reference_range." ".$parameter_value->unit_name; ?>
                                                              
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
</div>