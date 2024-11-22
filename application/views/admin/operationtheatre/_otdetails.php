<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>

<div class="row">
  <div class="col-md-6"><p><strong><?php echo $this->lang->line('reference_no'); ?> : </strong><?php echo $operation_theater_reference_no.$otdetails->id; ?></p></div>
  <div class="col-md-6"><p><strong><?php echo $this->lang->line('operation_name'); ?> : </strong><?php echo $otdetails->operation;?></p></div>
</div>
<div class="row">
      <div class="col-md-6"><p><strong> <?php echo $this->lang->line('date'); ?> : </strong>
      <?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($otdetails->date)); ?></p>
  </div>
  <div class="col-md-6"><p><strong><?php echo $this->lang->line('operation_category'); ?> :  </strong><?php echo $otdetails->category;?></p></div>
</div> 
<div class="row">
    <div class="col-md-6"><p><strong><?php echo $this->lang->line('consultant_doctor'); ?> : </strong><?php echo $otdetails->name.' '. $otdetails->surname. ' ('. $otdetails->employee_id.')';?></p></div>
    <div class="col-md-6"><p><strong><?php echo $this->lang->line('assistant_consultant').' 1'; ?> : </strong><?php echo $otdetails->ass_consultant_1;?></p></div>
</div>
<div class="row">
    <div class="col-md-6"><p><strong><?php echo $this->lang->line('assistant_consultant').' 2'; ?> : </strong><?php echo $otdetails->ass_consultant_2;?></p></div>
    <div class="col-md-6"><p><strong><?php echo $this->lang->line('anesthetist'); ?> : </strong><?php echo $otdetails->anesthetist;?></p></div>
</div>
<div class="row">
    <div class="col-md-6"><p><strong><?php echo $this->lang->line('anaethesia_type'); ?> : </strong><?php echo $otdetails->anaethesia_type;?></p></div>
    <div class="col-md-6"><p><strong><?php echo $this->lang->line('ot_technician'); ?> : </strong><?php echo $otdetails->ot_technician;?></p></div>
</div>
<div class="row">
    <div class="col-md-6"><p><strong><?php echo $this->lang->line('ot_assistant'); ?> : </strong><?php echo $otdetails->ot_assistant;?></p></div>
    <div class="col-md-6"><p><strong><?php echo $this->lang->line('remark'); ?> : </strong><?php echo $otdetails->remark;?></p></div>
</div>
<div class="row">
     <div class="col-md-6"><p><strong> <?php echo $this->lang->line('result'); ?> : </strong><?php echo $otdetails->result;?></p></div>
</div>
  <?php  if (!empty($fields)) {
                                foreach ($fields as $fields_key => $fields_value) {
                                    $display_field = $otdetails->{$fields_value->name};
                                    if ($fields_value->type == "link") {
                                        $display_field = "<a href=" . $otdetails->{$fields_value->name} . " target='_blank'>" . $otdetails->{$fields_value->name} . "</a>";

                                    }
                                    ?>
      <div class="row">
          <div class="col-md-12"><p><strong><?php echo $fields_value->name; ?> : </strong><?php echo $display_field; ?></div>
      </div>
<?php }
    }?> 
