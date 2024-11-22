<div class="box-header with-border">
    <h3 class="box-title"><?php echo $this->lang->line('inventory'); ?></h3>
</div>
<div class="box-body row">
    <ul class="reportlists">
      <?php                                  
        if ($this->module_lib->hasActive('inventory')) {
            if ($this->rbac->hasPrivilege('inventory_stock_report', 'can_view')) {
        ?>
                <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/inventory/itemreport'); ?>"><a href="<?php echo base_url(); ?>admin/item/itemreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('inventory_stock_report'); ?></a></li>
    <?php
            }
        }

        if ($this->module_lib->hasActive('inventory')) {
            if ($this->rbac->hasPrivilege('add_item_report', 'can_view')) {
    ?>
                <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/inventory/additemreport'); ?>"><a href="<?php echo base_url(); ?>admin/item/additemreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('inventory_item_report'); ?></a></li>
    <?php
        }
        }

        if ($this->module_lib->hasActive('inventory')) {
            if ($this->rbac->hasPrivilege('issue_inventory_report', 'can_view')) {
    ?>
                <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/inventory/issueinventoryreport'); ?>"><a href="<?php echo base_url(); ?>admin/issueitem/issueinventoryreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('inventory_issue_report'); ?></a></li>
    <?php 
        }
        }
      ?>   
    </ul>
</div>