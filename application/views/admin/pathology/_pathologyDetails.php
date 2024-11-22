<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
 ?>
<div class="row">
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('test_name'); ?> : </strong> <?php echo $result->test_name;?></p></div>
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('short_name'); ?> : </strong><?php echo $result->short_name;?></p></div>
  </div>
<div class="row">
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('test_type'); ?> : </strong><?php echo $result->test_type;?></p></div>
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('sub_category'); ?> : </strong><?php echo $result->sub_category;?></p></div>
  </div>
<div class="row">
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('report_days'); ?> : </strong><?php echo $result->report_days;?></p></div>
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('method'); ?> : </strong><?php echo $result->method;?></p></div>
  </div>
<div class="row">
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('category_name'); ?> : </strong><?php echo $result->pathology_category_name;?></p></div>
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('charge_name'); ?> :  </strong><?php echo $result->charge_name;?></p></div>
  </div>
<div class="row">
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('charge_category'); ?> : </strong><?php echo $result->charge_category_name;?></p></div>
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('standard_charge'); ?> : </strong><?php echo $currency_symbol.$result->standard_charge;?></p></div>
  </div>
  <div class="row">
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('tax_category'); ?> : </strong><?php echo $result->apply_tax;?></p></div>
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('tax'); ?> (%) : </strong><?php echo $result->tax;?></p></div>
  </div>
      <div class="row">
      <div class="col-md-6"><p><strong></p></div>
      <div class="col-md-6"><p><strong><?php echo $this->lang->line('amount'); ?> : </strong><?php echo $currency_symbol.amountFormat($result->standard_charge+calculatePercent($result->standard_charge,$result->tax))?></p></div>
  </div>
<div class="row">      
      <div class="col-md-12"><p><strong><?php echo $this->lang->line('charge_description'); ?> : </strong><?php echo $result->description;?></p></div>
  </div>  
     <?php
                                            $cutom_fields_data = get_custom_table_values($result->id, 'pathologytest');
                                            if (!empty($cutom_fields_data)) {
                                              ?>
  <div class="row"> 
                                              <?php
                                                foreach ($cutom_fields_data as $field_key => $field_value) {
                                                    ?>                                                 
                                                       <div class="col-md-6">
                                                        <p><strong><?php echo $field_value->name; ?> : </strong>                                                      
                                                            <?php
                                                            if (is_string($field_value->field_value) && is_array(json_decode($field_value->field_value, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                                                                $field_array = json_decode($field_value->field_value);
                                                                echo "<ul class='patient_custom_field'>";
                                                                foreach ($field_array as $each_key => $each_value) {
                                                                    echo "<li>" . $each_value . "</li>";
                                                                }
                                                                echo "</ul>";
                                                            } else {
                                                                $display_field = $field_value->field_value;

                                                                if ($field_value->type == "link") {
                                                                    $display_field = "<a href=" . $field_value->field_value . " target='_blank'>" . $field_value->field_value . "</a>";
                                                                }
                                                                echo $display_field;
                                                            }
                                                            ?>
                                                        </p>
                                                        </div> 
                                                      <?php
                                                }
                                                ?>
</div> 
                                                <?php
                                            }
                                            ?>                                            
<?php 

if(!empty($result->pathology_parameter))
{
  ?>
<div class="table-responsive mt10 ml-mius-5">
<table class="table table-striped table-bordered table-hover mb10">
  <thead>
    <tr>
      <th><?php echo $this->lang->line('test_parameter_name'); ?></th>
      <th><?php echo $this->lang->line('reference_range'); ?></th>
      <th><?php echo $this->lang->line('unit'); ?></th>
    </tr>
  </thead>
  <tbody>
<?php 
foreach ($result->pathology_parameter as $pathology_key => $pathology_value) {
 ?>
<tr>
  <td><?php echo $pathology_value->parameter_name; ?></td>
  <td><?php echo $pathology_value->reference_range; ?></td>
  <td><?php echo $pathology_value->unit_name; ?></td>
</tr>
 <?php
}
 ?>
  </tbody>
</table>
</div>
  <?php 
}
 ?>
  