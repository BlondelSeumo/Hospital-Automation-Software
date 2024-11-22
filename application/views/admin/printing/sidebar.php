<div class="box border0">
    <ul class="tablists">        
        
        <?php if ($this->rbac->hasPrivilege('print_appointment_header_footer', 'can_view')) { ?>
        
            <li> <a href="<?php echo site_url('admin/printing/index') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/appointmentprinting'); ?>"><?php echo $this->lang->line("appointment"); ?></a></li>
            
        <?php } if ($this->rbac->hasPrivilege('opd_prescription_print_header_footer', 'can_view')) { ?>
        
            <li><a href="<?php echo site_url('admin/printing/opdpresprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/opdpresprinting'); ?>"><?php echo $this->lang->line('opd_prescription'); ?></a></li>
            
        <?php } if ($this->rbac->hasPrivilege('opd_bill_print_header_footer', 'can_view')) { ?>
        
            <li><a href="<?php echo site_url('admin/printing/opdprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/opdprinting'); ?>"><?php echo $this->lang->line('opd_bill'); ?></a></li>
            
        <?php } if ($this->rbac->hasPrivilege('ipd_prescription_print_header_footer', 'can_view')) { ?>
        
            <li><a href="<?php echo site_url('admin/printing/ipdpresprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/ipdpresprinting'); ?>"><?php echo $this->lang->line('ipd_prescription'); ?> </a></li>
            
        <?php } if ($this->rbac->hasPrivilege('ipd_bill_print_header_footer', 'can_view')) { ?>
        
            <li><a href="<?php echo site_url('admin/printing/ipdprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/ipdprinting'); ?>"><?php echo $this->lang->line('ipd_bill'); ?></a></li>
            
        <?php }if ($this->rbac->hasPrivilege('ipd_bill_print_header_footer', 'can_view')) { ?>
        
            <li><a href="<?php echo site_url('admin/printing/billprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/billprinting'); ?>"><?php echo $this->lang->line('bill_summary'); ?></a></li>
            
        <?php }  if ($this->rbac->hasPrivilege('pharmacy_bill_print_header_footer', 'can_view')) { ?>
        
            <li><a href="<?php echo site_url('admin/printing/pharmacyprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/pharmacyprinting'); ?>"><?php echo $this->lang->line('pharmacy_bill'); ?></a></li>
            
        <?php } if ($this->rbac->hasPrivilege('print_payslip_header_footer', 'can_view')) { ?>
        
            <li><a href="<?php echo site_url('admin/printing/payslipprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/payslipprinting'); ?>"><?php echo $this->lang->line('payslip'); ?></a></li>
			
        <?php } if ($this->rbac->hasPrivilege('payment_receipt_header_footer', 'can_view')) { ?>		
        
            <li><a href="<?php echo site_url('admin/printing/paymentreceipt') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/paymentreceipt'); ?>"><?php echo $this->lang->line('payment_receipt'); ?></a></li>			
            
        <?php } if ($this->rbac->hasPrivilege('birth_print_header_footer', 'can_view')) { ?>
        
           <li> <a href="<?php echo site_url('admin/printing/birthprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/birthprinting'); ?>"><?php echo $this->lang->line('birth_record'); ?></a></li>
           
        <?php } if ($this->rbac->hasPrivilege('death_print_header_footer', 'can_view')) {  ?>
        
            <li><a href="<?php echo site_url('admin/printing/deathprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/deathprinting'); ?>"><?php echo $this->lang->line('death_record'); ?></a></li>
            
        <?php } if ($this->rbac->hasPrivilege('pathology_print_header_footer', 'can_view')) { ?>
        
            <li><a href="<?php echo site_url('admin/printing/pathologyprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/pathologyprinting'); ?>"><?php echo $this->lang->line('pathology'); ?></a></li> 
            
        <?php } if ($this->rbac->hasPrivilege('radiology_print_header_footer', 'can_view')) { ?>
        
            <li>  <a href="<?php echo site_url('admin/printing/radiologyprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/radiologyprinting'); ?>"><?php echo $this->lang->line('radiology'); ?></a></li>
            
        <?php } if ($this->rbac->hasPrivilege('ot_print_header_footer', 'can_view')) {?>
        
            <li> <a href="<?php echo site_url('admin/printing/otprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/otprinting'); ?>"><?php echo $this->lang->line('operation'); ?></a></li>
            
        <?php } if ($this->rbac->hasPrivilege('bloodbank_print_header_footer', 'can_view')) { ?>
        
            <li><a href="<?php echo site_url('admin/printing/bloodbankprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/bloodbankprinting'); ?>"><?php echo $this->lang->line('blood_bank'); ?></a></li> 
            
        <?php } if ($this->rbac->hasPrivilege('ambulance_print_header_footer', 'can_view')) { ?>
        
            <li> <a href="<?php echo site_url('admin/printing/ambulanceprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/ambulanceprinting'); ?>"><?php echo $this->lang->line('ambulance'); ?></a></li>
            
        <?php } if ($this->rbac->hasPrivilege('discharge_summary_print_header_footer', 'can_view')) { ?>
        
            <li> <a href="<?php echo site_url('admin/printing/summaryprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/summaryprinting'); ?>"><?php echo $this->lang->line("discharge_card"); ?></a></li>
            
		<?php } if ($this->rbac->hasPrivilege('opd_antenatal_finding_print_header_footer', 'can_view')) { ?>
		
            <li> <a href="<?php echo site_url('admin/printing/opdantenatalfindingprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/opdantenatalfindingprinting'); ?>"><?php echo $this->lang->line("opd_antenatal_finding"); ?></a></li>
			
        <?php } if ($this->rbac->hasPrivilege('ipd_obstetric_history_print_header_footer', 'can_view')) { ?>
		
            <li> <a href="<?php echo site_url('admin/printing/obstetrichistoryprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/obstetrichistoryprinting'); ?>"><?php echo $this->lang->line("obstetric_history"); ?></a></li>	
			
        <?php } if ($this->rbac->hasPrivilege('ipd_antenatal_finding_print_header_footer', 'can_view')) { ?>	
			
            <li> <a href="<?php echo site_url('admin/printing/ipdantenatalfindingprinting') ?>" class="<?php echo set_sidebar_Submenu('admin/printing/ipdantenatalfindingprinting'); ?>"><?php echo $this->lang->line("ipd_antenatal_finding"); ?></a></li>
			
		<?php } ?>			
        
    </ul>
</div>