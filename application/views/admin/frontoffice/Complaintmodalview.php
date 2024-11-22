<div class="table-responsive">    
    <table class="table table-striped bordergray mb0">
        <tr>
            <th><?php echo $this->lang->line('complain'); ?> #</th>
            <td><?php if(!empty($complaint_data['id'])){ echo $complaint_data['id']; }?></td>
            <th><?php echo $this->lang->line('complain_type'); ?></th>
            <td><?php if(!empty($complaint_data['complaint_type'])){ echo $complaint_data['complaint_type']; }?></td>
        </tr>
        <tr>
            <th><?php echo $this->lang->line('source'); ?></th>
            <td><?php if(!empty($complaint_data['source'])){ echo $complaint_data['source']; }?></td>
            <th><?php echo $this->lang->line('name'); ?></th>
            <td><?php if(!empty($complaint_data['name'])){ echo $complaint_data['name']; }?></td>
        </tr>
        <tr>
            <th><?php echo $this->lang->line('phone'); ?></th>
            <td><?php if(!empty($complaint_data['contact'])){ echo $complaint_data['contact']; }?></td>
            <th><?php echo $this->lang->line('date'); ?></th>
            <td><?php if(!empty($complaint_data['date'])){ echo $this->customlib->YYYYMMDDTodateFormat($complaint_data['date']); }?></td>
        </tr>
        <tr>
            <th><?php echo $this->lang->line('description'); ?></th>
            <td><?php if(!empty($complaint_data['description'])){ echo $complaint_data['description']; }?></td>
            <th><?php echo $this->lang->line('action_taken'); ?></th>
            <td><?php if(!empty($complaint_data['action_taken'])){ echo $complaint_data['action_taken']; }?></td>
        </tr>
        <tr>
            <th><?php echo $this->lang->line('assigned'); ?></th>
            <td><?php if(!empty($complaint_data['assigned'])){ echo $complaint_data['assigned']; }?></td>
            <th><?php echo $this->lang->line('note'); ?></th>
            <td><?php if(!empty($complaint_data['note'])){ echo $complaint_data['note']; }?></td>
        </tr>
    </table>
</div>    