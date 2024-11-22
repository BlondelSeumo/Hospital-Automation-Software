<div class="divider mt-10 mb-10"></div>
<table class="printablea4" id="testreport" width="100%">
    <tr>
        <th width="20%"><?php echo $this->lang->line('parameter') . " " . $this->lang->line('name'); ?></th> 
        <th><?php echo $this->lang->line('reference') . " " . $this->lang->line('range'); ?></th>
        <th><?php echo $this->lang->line('unit'); ?></th>       
    </tr>
    <?php
    $j = 0;
    foreach ($detail as $value) {
        ?>
        <tr>
            <td width="20%"><?php echo $value["parameter_name"]; ?></td>
            <td><?php echo $value["reference_range"]; ?></td>
            <td><?php echo $value["unit_name"]; ?></td>
        </tr>
        <?php
        $j++;
    }
    ?>
</table> 
<div class="divider mt-10 mb-10"></div>
