<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="blood-body">
        <div class="blood-pull-left blood-title"><?= $this->lang->line("blood"); ?></div>
        <div class="blood-pull-right blood-title"><?= count($blood_data) ?> <?= $this->lang->line("bags"); ?> <?php if($this->rbac->hasPrivilege('blood_stock', 'can_add')) { ?><button type="button" onclick="bloodDetailsModal('<?php echo $blood_group_id; ?>')" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></button> <?php } ?></div> 
    </div>     
    <div class="tableFixHead">
        <table class="table table-hover table-bordered bloodtable">
            <thead>
                <tr class="active">
                    <th><?=$this->lang->line("bags"); ?></th>
                    <th><?=$this->lang->line("lot"); ?></th>
                    <th><?=$this->lang->line("institution"); ?></th>
                    <th class="text-right"><?=$this->lang->line("action") ?></th>
                </tr>
            </thead>   
            <tbody> 
            <?php foreach($blood_data as $value){ ?> 
                <tr>
                    <td><?= $this->customlib->bag_string($value["bag_no"],$value["volume"],$value["unit"]);?></td>
                    <td><?= $value["lot"]; ?></td>                   
                    <td><?= $value["institution"]; ?></td>
                    <td width="10%" class="text-right"><?php if($this->rbac->hasPrivilege('blood_issue', 'can_add')) { ?><button type="button" onclick=bloodIssueModal(<?= $value['product_id'] ?>,<?= $value['bag_id'] ?>) class="btn btn-sm btn-primary"><?= $this->lang->line("issue"); ?></button><?php } ?></td>
                </tr>
                <?php } ?>
            </tbody>     
        </table>
    </div><!--./table-responsive-->  
</div><!--./col-lg-6-->
<div class="col-lg-6 col-md-6 col-sm-12">
    <div class="blood-body">
        <div class="blood-pull-left blood-title"><?= $this->lang->line("components"); ?></div>
        <div class="blood-pull-right blood-title"><?= count($component_data) ?> <?= $this->lang->line("bags"); ?>  <?php if($this->rbac->hasPrivilege('blood_bank_components', 'can_add')) { ?><button type="button" onclick=componentDetailsModal(<?= $blood_group_id ?>) class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></button> <?php } ?></div>
    </div>     
    <div class="tableFixHead">
        <table class="table table-hover table-bordered bloodtable">
            <thead>
                <tr class="active">
                    <th><?=$this->lang->line("bags"); ?></th>
                    <th><?=$this->lang->line("lot"); ?></th>
                    <th><?= $this->lang->line("components"); ?></th>
                    <th width="10%"><?= $this->lang->line("action"); ?></th>
                </tr>
            </thead>   
            <tbody>
                <?php foreach($component_data as $value){ ?> 
                <tr>
                    <td><?= $this->customlib->bag_string($value["bag_no"],$value["volume"],$value["unit"]);?></td>
                    <td><?= $value["lot"]; ?></td>                    
                    <td><?= $value["name"]; ?></td>
                    <td><?php if($this->rbac->hasPrivilege('issue_component', 'can_add')) { ?><button type="button" onclick=componentIssueModal(<?= $value['product_id'] ?>,<?= $value['id'] ?>,<?php echo $value['bloodid'];?>) class="btn btn-sm btn-primary pull-right"><?= $this->lang->line("issue"); ?></button><?php }?></td>
                </tr>
                <?php } ?>
            </tbody>         
        </table>
    </div><!--./tableFixHead--> 
</div><!--./col-lg-6-->