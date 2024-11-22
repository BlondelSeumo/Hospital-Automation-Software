<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/sh-print.css">
 
<div class="print-area">
    <div class="row">
        <div class="col-12">          
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">  
                            <?php if(!empty($result->pathology_parameter)){ ?>
                            <table class="print-table">
                                <thead>
                                    <tr class="line">
                                        <th>#</th>
                                        <th class="text-left"><?php echo $this->lang->line('test_parameter_name'); ?></th>  
                                        <th class="text-left"><?php echo $this->lang->line('reference_range'); ?></th>                               
                                        <th class="text-left"><?php echo $this->lang->line('report_value'); ?></th>
                                    </tr>
                             </thead>
                             <tbody>
                                <?php 
                                
                                $row_counter=1;
                                foreach ($result->pathology_parameter as $parameter_key=> $parameter_value) {
                                ?>                        
                                <tr>
                                    <td><?php echo $row_counter; ?></td>
                                    <td class="text-left"><?php echo $parameter_value->parameter_name; ?><div class="bill_item_footer text-muted"><label><?php if($parameter_value->description !=''){ echo $this->lang->line('description').': ';} ?></label> <?php echo $parameter_value->description; ?></div></td>                       
                                    <td class="text-left"><?php echo $parameter_value->reference_range." ".$parameter_value->unit_name; ?></td>
                                    <td class="text-left"><?php if($parameter_value->pathology_report_value!="" ) { echo $parameter_value->pathology_report_value." ".$parameter_value->unit_name; } ?></td>       
                                </tr>  
                            <?php 
                                $row_counter++;
                            }

                            if(!empty($parameter_value->pathology_result)){ ?> 
                            <tr> 
                                <td colspan="5"><p><b><?php echo $this->lang->line('result'); ?>: </b> <?php echo nl2br($parameter_value->pathology_result); ?></p></td>
                            </tr>                             
                        <?php
                                }  
                        ?>    
                            </tbody>
                          </table>
                            <?php } else { ?>
                          <div class="alert alert-danger"><?php echo $this->lang->line('no_record_found'); ?></div>
                                    
                                <?php } ?> 
                        </div>
                    </div>
                </div>
            </div>
          <div style="clear:both"></div>
           
        </div>
    </div>
</div>
<script>
   $(document).ready(function(){

  $("#modal_head").html("<?php echo $result->test_name.' ('.$result->short_name.' )'; ?>");

});
</script>