<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$amount=0;
?>
<div class="fixed-print-header">
    <?php if (!empty($print_details[0]['print_header'])) { ?>
                        <div class="pprinta4">
                            <img src="<?php
                            if (!empty($print_details[0]['print_header'])) {
                                echo base_url() . $print_details[0]['print_header'].img_time();
                            }
                            ?>" class="img-responsive">
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
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">  
                    <div class="row">
                        <div class="col-md-6">
                            <?php  if (!empty($fields)) {
                                foreach ($fields as $fields_key => $fields_value) {
                                    $display_field = $result->{"$fields_value->name"};
                                    if ($fields_value->type == "link") {
                                        $display_field = "<a href=" . $result->{"$fields_value->name"} . " target='_blank'>" . $result->{"$fields_value->name"} . "</a>";
                                    }
                                    ?>
                                    <p><?php echo $fields_value->name .": ".$display_field ;?></p>
                                <?php }
                            }?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="print-table">
                             <thead>
                                <tr class="line">
                                   <td>#</td>
                                   <td class="text-left"><?php echo $this->lang->line('customer_type'); ?></td>
                                   <td class="text-center"><?php echo $this->lang->line('operation_name'); ?></td>
                                   <td class="text-center"><?php echo $this->lang->line('date'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('operation_type'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('assistant_consultant').' 1'; ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('assistant_consultant').' 2'; ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('anesthetist'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('anaethesia_type'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('ot_technician'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('ot_assistant'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('result'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('remark'); ?></td>
                                   <td class="text-right"><?php echo $this->lang->line('generated_by'); ?></td>
                                </tr>
                             </thead>
                             <tbody>
                                <?php 
                      $row_counter=1;
                      $tax_amt=0;
                        foreach ($ot_details as $ot_details_value) { ?>
                        <tr>
                                <td><?php echo $ot_details_value->customer_type; ?>
                                  <br/>
                                  <?php echo "(".$ot_details_value->operation_name.")"; ?>
                                </td>                               
                                <td class="text-center"><?php echo  $this->customlib->YYYYMMDDTodateFormat($ot_details_value->date); ?></td>
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