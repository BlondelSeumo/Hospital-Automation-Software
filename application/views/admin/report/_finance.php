<div class="box-header with-border">
   <h3 class="box-title"><?php echo $this->lang->line('finance'); ?></h3>
</div>    
<div class="box-body row">
    <ul class="reportlists">
                
 <?php if ($this->rbac->hasPrivilege('daily_transaction_report', 'can_view')) {?>
        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/transaction/dailytransactionreport'); ?>"><a href="<?php echo base_url(); ?>admin/transaction/transactionreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line("daily_transaction_report"); ?></a>
        </li>

        <?php } if ($this->rbac->hasPrivilege('all_transaction_report', 'can_view')) {?>
          
        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/income/alltransactionreport'); ?>"><a href="<?php echo base_url(); ?>admin/income/alltransactionreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line("all_transaction_report"); ?></a>
            </li>
        <?php } 
         if ($this->module_lib->hasActive('income')) {
            if ($this->rbac->hasPrivilege('income_report', 'can_view')) {
        ?>
                <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/income/incomesearch'); ?>"><a href="<?php echo base_url(); ?>admin/income/incomesearch"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('income_report'); ?></a></li>
        <?php
            }
            }

         if ($this->module_lib->hasActive('income')) {
            if ($this->rbac->hasPrivilege('income_group_report', 'can_view')) {
                ?>
                <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/income/incomegroup'); ?>"><a href="<?php echo base_url(); ?>admin/income/incomegroup"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('income_group_report'); ?></a></li>
        <?php
            }
            }

            if ($this->module_lib->hasActive('expense')) {
            if ($this->rbac->hasPrivilege('expense_report', 'can_view')) {
                ?>
                <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/expense/expensesearch'); ?>"><a href="<?php echo base_url(); ?>admin/expense/expensesearch"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('expense_report'); ?></a></li>
        <?php
            }
            }

            if ($this->module_lib->hasActive('expense')) {
            if ($this->rbac->hasPrivilege('expense_group_report', 'can_view')) {
                ?>
                <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/expense/expensegroup'); ?>"><a href="<?php echo base_url(); ?>admin/expense/expensegroup"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('expense_group_report'); ?></a></li>
        <?php
            }
            }

            if($this->module_lib->hasActive('bill')){
                    if ($this->rbac->hasPrivilege('patient_bill_report', 'can_view')) {
        ?>
                <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/patient/patientbillreport'); ?>"><a href="<?php echo base_url(); ?>admin/patient/patientbillreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line("patient_bill_report"); ?></a></li>
                <?php } } 
                if($this->module_lib->hasActive('referral')){
                    if ($this->rbac->hasPrivilege('referral_report', 'can_view')) {
        ?>
                <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/referral/report'); ?>"><a href="<?php echo base_url(); ?>admin/referral/report"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line("referral_report"); ?></a></li>
        <?php } }
        ?>
 <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('reports/transaction/processingtransactionreport'); ?>"><a href="<?php echo base_url(); ?>admin/transaction/processingtransactionreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('processing_transaction_report') ?></a></li>
        </ul>
    </div>
    