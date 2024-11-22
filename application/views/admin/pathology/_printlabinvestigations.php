
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
<div class="print-area p-1">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">                     
                    <div class="row">
                        <div class="col-md-12 pt5" >
                            <table class="noborder_table">
                                <tr>
                                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                                    <td><?php echo $this->customlib->getSessionPrefixByType('pathology_billing') .$result->pathology_bill_id; ?></td>
                                    <th><?php echo $this->lang->line('case_id'); ?></th>
                                    <td><?php echo $result->case_reference_id; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line("patient"); ?></th>
                                    <td><?php echo composePatientName($result->patient_name,$result->patient_id); ?></td>
                                    <th><?php echo $this->lang->line('approve_date'); ?></th>
                                    <td><?php if($result->parameter_update){ echo $this->customlib->YYYYMMDDTodateFormat($result->parameter_update); } ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('age'); ?></th>
                                    <td><?php echo $this->customlib->getPatientAge($result->age,$result->month,$result->day);?></td>
                                    <th><?php echo $this->lang->line('report_collection_date'); ?></th>
                                    <td><?php if($result->collection_date){ echo $this->customlib->YYYYMMDDTodateFormat($result->collection_date); } ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <td><?php echo $result->gender; ?></td>
                                    <th><?php echo $this->lang->line('expected_date'); ?></th>
                                    <td><?php if($result->reporting_date){ echo $this->customlib->YYYYMMDDTodateFormat($result->reporting_date); }?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('collection_by'); ?></th>
                                    <td><?php echo composeStaffNameByString($result->collection_specialist_staff_name,$result->collection_specialist_staff_surname,$result->collection_specialist_staff_employee_id); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $this->lang->line('pathology_center'); ?> </th>
                                    <td colspan="3"><?php echo $result->pathology_center ?></td>                                    
                                </tr>                                                      
                            </table>
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
                                            <th class="text-left"><?php echo $this->lang->line('test_parameter_name'); ?></th>                                  
                                            <th class="text-left"><?php echo $this->lang->line('report_value'); ?></th>
                                            <th class="text-left"><?php echo $this->lang->line('reference_range'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if(!empty($result->pathology_parameter)){
                                        $row_counter=1;
                                        foreach ($result->pathology_parameter as $parameter_key=> $parameter_value) {
                                            
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
                                            <td class="text-left"><?php echo $parameter_value->parameter_name; ?><div class="bill_item_footer text-muted"><label><?php if($parameter_value->description !=''){ echo $this->lang->line('description').': ';} ?></label> <?php echo $parameter_value->description; ?></div></td>                                             
                                            <td class="text-left"> <?php echo $parameter_value->pathology_report_value." ".$parameter_value->unit_name;?></td>
                                            <td class="text-left"><?php echo $parameter_value->reference_range." ".$parameter_value->unit_name; ?></td>                                           
                                        </tr>                               
                                        <?php 
                                        $row_counter++;
                                        }
                                        ?>
                                    <?php if($parameter_value->pathology_result!=""){ ?> 
                                    <tr> 
                                        <td colspan="4"><p><b><?php echo $this->lang->line('result'); ?>: </b> <?php echo nl2br($parameter_value->pathology_result); ?></p></td>
                                    </tr>                             
                                        <?php } } ?>                                
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