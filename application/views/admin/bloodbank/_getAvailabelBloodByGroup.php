<?php 
if(!empty($result)){
?>
<table class="table table-hover">

	<thead>
		<tr>
			<th><?php echo $this->lang->line("donate_date"); ?></th>
			<th><?php echo $this->lang->line("bag_no"); ?></th>
			<th><?php echo $this->lang->line("lot"); ?></th>
			<th><?php echo $this->lang->line("quantity"); ?></th>
			<th><?php echo $this->lang->line("donate_by"); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
	foreach ($result as $blood_key => $blood_value) {
		?>
<tr>
			<td><?php echo $this->customlib->YYYYMMDDTodateFormat($blood_value->donate_date); ?></td>
			<td><?php echo $blood_value->bag_no; ?></td>
			<td><?php echo $blood_value->lot; ?></td>
			<td><?php echo $blood_value->quantity." ".$blood_value->charge_unit; ?></td>
			<td><?php echo $blood_value->donor_name; ?></td>
		</tr>
		<?php
	}
}else{
	?>
<div class="alert alert-info">
	<?php echo $this->lang->line("no_blood_available"); ?>
</div>
	<?php
}

 ?>
		
	</tbody>
</table>
