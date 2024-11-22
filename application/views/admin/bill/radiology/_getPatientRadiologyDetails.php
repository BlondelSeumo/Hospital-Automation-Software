<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat(); 
?>
<div class="row">
        <div class="table-responsive">
            <div class="col-md-9">
                  <table class="table table-hover table-sm">            
                <tr>
                    <th><label><?php echo $this->lang->line('bill_no'); ?></label></th>
                    <td><?php echo $bill_prefix.$result->id ?></td>
                    <th><label><?php echo $this->lang->line('case_id'); ?></label></th>
                    <td><?php echo $result->case_reference_id ?></td>
                    <th><label><?php echo $this->lang->line('patient_name'); ?></label></th>
                    <td><?php echo composePatientName($result->patient_name,$result->patient_id); ?></td>
                </tr> 
                <tr> 
                    <th><label><?php echo $this->lang->line('generated_by'); ?></label></th>
                    <td><?php echo composeStaffNameByString($result->name,$result->surname,$result->employee_id); ?></td>
                    <th><label><?php echo $this->lang->line('age'); ?></label></th>
                    <td><?php  echo $this->customlib->getPatientAge($result->age,$result->month,$result->day);?></td>
                    <th><label><?php echo $this->lang->line('gender'); ?></label></th>
                    <td><?php echo $result->gender ?></td>
                </tr>
                <tr>
                    <th><label><?php echo $this->lang->line('blood_group'); ?></label></th>
                    <td><?php echo $result->blood_group_name; ?></td>
                    <th><label><?php echo $this->lang->line('mobile_no'); ?></label></th>
                    <td><?php echo $result->mobileno ?></td>
                    <th><label><?php echo $this->lang->line('email'); ?></label></th>
                    <td><?php echo $result->email ?></td>                   
                </tr>
                <tr>
                    <th><label><?php echo $this->lang->line('doctor_name'); ?></label></th>
                    <td><?php echo $result->doctor_name; ?></td> 
                    <th><label><?php echo $this->lang->line('address'); ?></label></th>
                    <td><?php echo $result->address ?></td>
                    <th></th>
                    <td></td>
                </tr>
                    <?php
                        if (!empty($fields)) {
                            foreach ($fields as $fields_key => $fields_value) {
                                ?>
                            <tr>
                            <th><label><?php echo $fields_value->name; ?></label></th>
                            <td class="text text-right"><?php echo $result->{"$fields_value->name"} ; ?></td>
                               <th></th>
                    <td></td>
                    <th></th>
                    <td></td>
                            </tr>
                    <?php } } ?>
                <tr>
                    <th></th>
                    <td></td>
                    <th></th>
                    <td></td>
                    <th></th>
                    <td></td>                   
                </tr>             
            </table>
            </div>
             <div class="col-md-3">
                  <table class="table table-hover table-sm">                
                <tr>
                    <th><label><?php echo $this->lang->line('total'); ?></label></th>
                    <td class="text text-right"><?php if (!empty($result->total)) {
                        echo $currency_symbol.$result->total ;
                    } ?>  
                    </td>
                </tr> 
                <tr>           
                    <th><label><?php echo $this->lang->line('discount'); ?></label></th>
                    <td class="text text-right"><?php echo $currency_symbol.$result->discount ?></td>
                </tr>
                <tr>                  
                    <th><label><?php echo $this->lang->line('tax'); ?></label></th>
                    <td class="text text-right"><?php echo $currency_symbol.$result->tax ?></td>
                </tr>
                <tr>                 
                    <th><label><?php echo $this->lang->line('net_amount'); ?></label></th>
                    <td class="text text-right"><?php echo $currency_symbol.$result->net_amount ?></td>
                </tr>
                <tr>            
                    <th><label><?php echo $this->lang->line('total_deposit'); ?></label></th>
                    <td class="text text-right"><?php if (!empty($result->total_deposit)) {
                        echo $currency_symbol.$result->total_deposit ;
                    } ?></td>
                </tr>              
                <tr>
                <th>
                    <label><?php echo $this->lang->line('due_amount');?></label>
                </th>
                <td class="text-right">
                    <?php echo $currency_symbol.amountFormat($result->net_amount-$result->total_deposit); ?></td>  
                    </tr>
            </table>
            </div>          
        </div>
		
        <!-- //============= -->
    <div class="col-lg-12">    
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr class="line">
                        <td><strong>#</strong></td>
                        <td class="text-left"><strong><?php echo $this->lang->line('test_name'); ?></strong></td>
                        <td class="text-left"><strong><?php echo $this->lang->line('sample_collected'); ?></strong></td>
                        <td class="text-center"><strong><?php echo $this->lang->line('expected_date'); ?></strong></td>
                        <td class="text-left"><strong><?php echo  $this->lang->line('approved_by')." / ".$this->lang->line('update_date'); ?></strong></td>
                        <td class="text-right"><strong><?php echo $this->lang->line('tax'); ?></strong></td>
                        <td class="text-right"><strong><?php echo $this->lang->line('amount'); ?></strong></td>
                        <td class="text-right"><strong><?php echo $this->lang->line('action'); ?></strong></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $row_counter=1;
                        foreach ($result->radiology_report as $report_key=> $report_value) {
                            $tax_amount = ($report_value->apply_charge*$report_value->tax_percentage/100);
                            $taxamount  = amountFormat($tax_amount)
                    ?>
                    <tr>
                        <td><?php echo $row_counter; ?></td>
                        <td><strong><?php echo $report_value->test_name; ?></strong><br/><?php echo "(".$report_value->short_name.")"; ?></td>
                        <td class="text-left"><label>
                            <?php echo composeStaffNameByString($report_value->collection_specialist_staff_name,$report_value->collection_specialist_staff_surname,$report_value->collection_specialist_staff_employee_id); ?></label><br/><label for=""><?php echo $this->lang->line('radiology'); ?> : </label><?php 	echo $report_value->radiology_center; ?><br/><?php if($report_value->collection_date){ echo $this->customlib->YYYYMMDDTodateFormat($report_value->collection_date); } ?></td>
                            
                        <td class="text-center"><?php if($report_value->reporting_date){ echo $this->customlib->YYYYMMDDTodateFormat($report_value->reporting_date); } ?></td>
                        <td class="text-left"><label for=""><?php echo $this->lang->line('approved_by'); ?> : </label><?php  echo composeStaffNameByString($report_value->approved_by_staff_name,$report_value->approved_by_staff_surname,$report_value->approved_by_staff_employee_id);  	 ?> <br/>
                        <?php if($report_value->parameter_update){ echo  $this->customlib->YYYYMMDDTodateFormat($report_value->parameter_update); } ?>
                        
                        </td>
                        <td class="text-right"><?php echo "(".$report_value->tax_percentage."%) ".$currency_symbol.$taxamount; ?></td>
                        <td class="text-right"><?php echo $currency_symbol.$report_value->apply_charge; ?></td>
                        <td class="text-right">
                            <?php 
                                if($is_bill){ ?>
                               	<a href='javascript:void(0)'  data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id='<?php echo $report_value->id;?>' class='btn btn-default btn-xs add_collection' data-toggle='tooltip' title='<?php echo $this->lang->line('add_edit_collection_person'); ?>' ><i class='fa fa-user-plus'></i></a>
                                <?php 
if($report_value->collection_specialist_staff_employee_id != ""){
    ?>
      <a href='javascript:void(0)'  data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id='<?php echo $report_value->id;?>' class='btn btn-default btn-xs add_report' data-toggle='tooltip' title='<?php echo $this->lang->line('add_edit_report'); ?>' ><i class='fa fa-flask'></i></a><?php
}
                                 ?>                              
                            <?php } ?> 
                                <a href='javascript:void(0)' data-loading-text='<i class="fa fa-circle-o-notch fa-spin"></i>' data-record-id='<?php echo $report_value->id;?>' class='btn btn-default btn-xs print_report' data-toggle='tooltip' title='<?php echo $this->lang->line('print'); ?>' ><i class='fa fa-print'></i></a>
                            <?php if($report_value->radiology_report != ""){ ?>
                                <a href="<?php echo site_url('admin/pathology/downloadReport/'.$report_value->id) ?>" class='btn btn-default btn-xs'><i class="fa fa-download"></i></a>
                            <?php } ?>
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