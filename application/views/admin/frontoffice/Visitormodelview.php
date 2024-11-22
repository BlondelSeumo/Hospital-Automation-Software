<div class="table-responsive">    
    <table class="table table-striped bordergray">
        <tr>
            <th width="15%"><?php echo $this->lang->line('purpose'); ?></th>
            <td width="35%"><?php print_r($data['purpose']);?></td>
            <th width="15%"><?php echo $this->lang->line('name'); ?></th>
            <td width="35%"><?php print_r($data['name']);?></td>
        </tr>
        <tr>
            <th width="15%"><?php echo $this->lang->line('phone'); ?></th>
            <td width="35%"><?php print_r($data['contact']);?></td>
            <th width="15%"><?php echo $this->lang->line('number_of_person'); ?></th>
            <td width="35%"><?php print_r($data['no_of_pepple']);?></td>
        </tr>
        <tr>
            <th width="15%"><?php echo $this->lang->line('date'); ?></th>
            <td width="35%"><?php print_r( $this->customlib->YYYYMMDDTodateFormat($data['date'])); ?></td>
            <th></th>
            <td></td>
        </tr>
        <tr>
            <th width="15%"><?php echo $this->lang->line('in_time'); ?></th>
            <td width="35%"><?php print_r($data['in_time']);?></td>
            <th width="15%"><?php echo $this->lang->line('out_time'); ?></th>
            <td width="35%"><?php print_r($data['out_time']);?></td>
            
        </tr>
        <tr>
            <th width="15%"><?php echo $this->lang->line('note'); ?></th>
            <td width="85%" colspan="3"><?php print_r($data['note']);?></td>
            
        </tr>
    </table>
</div>    