<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('birth_record'); ?></title>
    </head>
    <div class="fixed-print-header">
    <?php if (!empty($print_details[0]['print_header'])) {
    ?>
                        <div>
                            <img src="<?php
if (!empty($print_details[0]['print_header'])) {
        echo base_url() . $print_details[0]['print_header'].img_time();
    }
    ?>" class="img-responsive" style="height:100px; width: 100%;">
                        </div>
                    <?php }?>
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
    <div id="html-2-pdfwrapper" class="p-1">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="">
                    
                    <table width="100%" class="mb-0">
                        <tr>
                            <td><?php echo $this->lang->line('reference_no') . ": " . $this->customlib->getSessionPrefixByType('birth_record_reference_no').$result['id']; ?></td>
                            <td class="text-right"><?php echo $this->lang->line('birth_date') . " : "; ?><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['birth_date']); ?>
                            </td>
                        </tr>
                        
                    </table>
                    <div class="divider"></div>
                    <table cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <td>
                                <table cellspacing="0" cellpadding="0" width="100%" class="noborder_table"  class="mb-0">
                                    <tr>
                                        <th width="20%"><?php echo $this->lang->line('child_name'); ?></th>
                                        <td width="25%" ><?php echo $result["child_name"]; ?></td>
                                    </tr>
                                    <tr>
                                        <th valign="top" width="20%"><?php echo $this->lang->line('gender'); ?></th>
                                        <td valign="top" width="25%"><?php echo $result["gender"]; ?></td>
                                        <th valign="top" width="25%"><?php echo $this->lang->line('weight'); ?></th>
                                        <td valign="top" width="30%"><?php echo $result['weight']; ?></td>
                                    </tr>
                                    <tr>
                                        <th width="25%"><?php echo $this->lang->line('mother_name'); ?></th>
                                        <td width="30%"><?php echo $result["patient_name"]. ' ('.$result["patient_id"].')'; ?></td>
                                        <th width="20%"><?php echo $this->lang->line('case_id'); ?></th>
                                        <td width="25%"><?php echo $result["case_reference_id"]; ?></td>
                                    </tr>
                                    <tr>
                                        <th width="20%"><?php echo $this->lang->line('father_name'); ?></th>
                                        <td width="25%"><?php echo $result["father_name"]; ?></td>
                                        <th width="25%"><?php echo $this->lang->line('address'); ?></th>
                                        <td width="30%"><?php echo $result['address']; ?></td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top" width="20%">
                                <?php
$picdemo = "uploads/patient_images/no_image.png";
if ($result['child_pic'] !== $picdemo) {
    ?>
                                    <img  style="height: 60px;" class="" src="<?php echo base_url() . $result['child_pic'].img_time() ?>" id="image" alt="User profile picture">
                                <?php }?>
                            </td>
                        </tr>
                    </table>
                    <div class="divider0"></div>
                    <table cellspacing="0" cellpadding="0" class="printablea4">
                     <?php  if (!empty($fields)) {
                                foreach ($fields as $fields_key => $fields_value) {
                                    $display_field = $result["$fields_value->name"];
                                    if ($fields_value->type == "link") {
                                        $display_field = "<a href=" . $result["$fields_value->name"] . " target='_blank'>" . $result["$fields_value->name"] . "</a>";

                                    }
                                    ?>
                                <tr>
                                    <th width="20%"><?php echo $fields_value->name; ?></th> 
                                    <td width="80%"><?php echo $display_field; ?></td>
                                </tr>

                        <?php }
                    }?>
                    </table>
                    <div class="divider mt-10 mb-10"></div>

                    
                </div>
            </div>
            <!--/.col (left) -->
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
</html>