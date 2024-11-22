<table class="table table-striped table-bordered table-hover" id="tableID">
    <thead>
        <tr style="font-size: 13px">
            <th><?php echo $this->lang->line('test') . " " .$this->lang->line('parameter') . " " . $this->lang->line('name'); ?><small class="req" style="color:red;"> *</small></th>
			<th><?php echo $this->lang->line('refference') . " " . $this->lang->line('range'); ?></th>
			<th><?php echo $this->lang->line('value'); ?></th>
			<th><?php echo $this->lang->line('unit') ; ?><small class="req" style="color:red;"> *</small></th>
        </tr>
    </thead>
	<?php  foreach ($detail as $value) { ?>
    <tr id="row0">
		<input type="hidden" readonly="" name="parameter_id[]" value="<?php if(!empty($value["parameter_id"])) { echo $value["parameter_id"]; } ?>" id="" class="form-control">
        <input type="hidden" readonly="" name="preport_id[]" value="<?php if(!empty($value["pathology_report_id"])) { echo $value["pathology_report_id"]; } ?>" id="" class="form-control">
        <input type="hidden" readonly="" name="update_id[]" value="<?php if(!empty($value["id"])) { echo $value["id"]; } ?>" id="" class="form-control">
        <input type="hidden" readonly="" name="parid[]" value="<?php if(!empty($value["parid"])) { echo $value["parid"]; } ?>" id="" class="form-control">
        <input type="hidden" readonly="" name="pathology_id" value="<?php if(!empty($value["pathology_id"])) { echo $value["pathology_id"]; } ?>" id="" class="form-control">
                                             
        <td width="25%">
            <input type="text" readonly="" name="parameter_name[]" value="<?php echo $value["parameter_name"]; ?>" id="reference_range" class="form-control">
        </td>

        <td width="25%">
            <input type="text" readonly="" name="reference_range[]" value="<?php if(!empty($value["reference_range"])) { echo $value["reference_range"]; } ?>" id="reference_range" class="form-control">
        </td>

        <td width="25%">
            <input type="text" name="parameter_value[]" value="<?php if(!empty($value["pathology_report_value"])) { echo $value["pathology_report_value"]; } ?>" id="" class="form-control">
        </td>

        <td width="25%">
            <input type="text" readonly="" name="patho_unit[]" value="<?php echo $value["unit_name"]; ?>" id="patho_unit0" class="form-control">
        </td>

    </tr>

    <?php  } ?>
</table>