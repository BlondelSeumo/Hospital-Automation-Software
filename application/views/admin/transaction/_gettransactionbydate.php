<?php $currency_symbol = $this->customlib->getHospitalCurrencyFormat(); ?>
<table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
    <thead>
        <tr>
        <th><?php echo $this->lang->line('transaction_id'); ?></th>
        <th><?php echo $this->lang->line('date'); ?></th>
        <th><?php echo $this->lang->line('payment_mode'); ?></th>
        <th><?php echo $this->lang->line('collected_by'); ?></th>
        <th class="text text-right"><?php echo $this->lang->line('amount').'('.$currency_symbol.')'; ?></th>      
    </tr>
</thead>
<tbody>
    <?php
        foreach ($result as $dt_key => $dt_value) {
    ?>
<tr>  
    <td><?php echo $this->customlib->getSessionPrefixByType('transaction_id').$dt_value->id; ?></td>
    <td><?php echo $this->customlib->YYYYmmddTodateformat($dt_value->payment_date) ?></td>
    <td><?php echo $this->lang->line(strtolower($dt_value->payment_mode)); ?></td>
    <td><?php echo composeStaffNameByString($dt_value->name,$dt_value->surname,$dt_value->employee_id); ?></td>
    <td class="text text-right"><?php echo amountFormat($dt_value->amount); ?></td>  
</tr>
    <?php
}
     ?>
</tbody>
</table>