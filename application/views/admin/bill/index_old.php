<link rel="stylesheet" href="<?php echo base_url();?>backend\dist\css\jquery-ui.css">
<style type="text/css">
    @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
    } 
</style> 
<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('opd_ipd_billing_through_case_id'); ?></h3>
                        <div class="box-tools pull-right box-tools-md">
                            <div class="btn-group">
                              <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                             <i class="fas fa-bars"></i></button>
                              <ul class="dropdown-menu s-bill-list">
                                <h3 class="s-bill-title"><?php echo $this->lang->line('single_module_billing'); ?></h3> 
                                 <?php if ($this->rbac->hasPrivilege('appointment_billing', 'can_view')) {?>
                                <li><a href="<?php echo base_url('admin/bill/appointment');?>"><i class="fa fa-calendar-check-o"></i><?php echo $this->lang->line('appointment'); ?></a>
                                </li>
                                <?php } if ($this->rbac->hasPrivilege('opd_billing', 'can_view')) { ?>
                                <li><a href="<?php echo base_url('admin/bill/opd');?>"><i class="fas fa-stethoscope"></i> <?php echo $this->lang->line('opd'); ?></a></li>
                                <?php } if ($this->rbac->hasPrivilege('pathology_billing', 'can_view')) { ?>
                                <li><a href="<?php echo base_url('admin/bill/pathology');?>"><i class="fas fa-flask"></i>  <?php echo $this->lang->line('pathology'); ?></a>
                                </li>
                                <?php } if ($this->rbac->hasPrivilege('radiology_billing', 'can_view')) { ?>
                                <li><a href="<?php echo base_url('admin/bill/radiology');?>"><i class="fas fa-microscope"></i> <?php echo $this->lang->line('radiology'); ?></a>
                                </li> 
                                <?php } if ($this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) { ?>
                                <li><a href="<?php echo base_url('admin/bill/issueblood');?>"><i class="fas fa-tint"></i>  <?php echo $this->lang->line('blood_issue'); ?></a>
                                </li>
                                <?php } if ($this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) { ?>
                                <li><a href="<?php echo base_url('admin/bill/issuecomponent');?>"><i class="fas fa-burn"></i> <?php echo $this->lang->line('blood_component_issue'); ?></a>
                                </li>
                            <?php } ?>
                              </ul>
                            </div>
                        </div>
                    </div>
                    <div class="box-body minheight303">
                            <form id="formsearch" accept-charset="utf-8" class="form-inline align-top"  method="POST" action="<?php echo site_url('admin/bill/patient_details');?>">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <div class="form-group">
                                        <div class=""> 
                                            <label><?php echo $this->lang->line('case_id'); ?></label><small class="req"> *</small>
                                            <input type="text" name="case_id" class="form-control" id="case_id" value="<?php echo $case_id; ?>" placeholder="<?php echo $this->lang->line('enter_case_id'); ?>">
                                            <div class="text-danger"><?php echo form_error('search_type'); ?></div>
                                        </div>    
                                    </div>
                                    <div class="form-group">
                                        <div class=""> 
                                            <button type="submit" id="serach_btn" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>   
                                        </div>     
                                    </div>                                      
                            </form>                            
                         <div class="row" id="patient_details"></div>
                    </div>
  
        </div>
        </div>
        </div>
                      
</section>
</div>

<script type="text/javascript">
    $(document).on('submit','#formsearch',function(e){

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var url = form.attr('action');
    var data = $(form).serialize();
   $.ajax({
    type: 'POST',
    url: url,
    data: data,
    dataType:'JSON',
    beforeSend: function() {
        // setting a timeout
      // $('#patient_details').html("backend/images/gif/loading_modal.gif");
    },
    success: function(data) {
    $('#patient_details').html(data.page);
    },
    error: function(xhr) { // if error occured
        alert("Error occured.please try again");
        
    },
    complete: function() {
     
    }
});
    });
</script>