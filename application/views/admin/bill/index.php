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
                                <li><a href="<?php echo base_url('admin/bill/appointment');?>"><i class="fa fa-calendar-check-o"></i><?php echo $this->lang->line('appointment'); ?></a></li>
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
                    <div class="box-body">
                            <form id="formsearch" accept-charset="utf-8" method="post" class="form-inline align-top">
                                    <?php echo $this->customlib->getCSRF(); ?>
                                    <div class="form-group">
                                        <div> 
                                            <label><?php echo $this->lang->line('case_id'); ?></label><small class="req"> *</small>
                                            <input type="text" name="case_id" class="form-control" id="case_id" value="<?php echo $case_id; ?>" placeholder="<?php echo $this->lang->line('enter_case_id'); ?>">
                                            <div class="text-danger"><?php echo form_error('search_type'); ?></div>
                                        </div>    
                                    </div>
                                    <div class="form-group">
                                        <div> 
                                            <button type="submit" id="serach_btn" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>   
                                        </div>     
                                    </div>                                      
                            </form>                            
                         <div class="row" id="patient_details"></div>
                    </div>  
                        <div class="tabsborderbg mt0"></div>
                        <div class="nav-tabs-custom border0">
                            <ul class="nav nav-tabs navlistscroll">                             
                                <?php if ($this->rbac->hasPrivilege('opd_billing', 'can_view')) {?>                          
                                <li class="active"><a href="#opd" data-toggle="tab" aria-expanded="true" onclick="load_opd_data()"><?php echo $this->lang->line('opd') ?></a></li>
                                 <?php } if ($this->rbac->hasPrivilege('ipd_billing', 'can_view')) { ?>
                                <li ><a onclick="load_ipd_data()" href="#ipd" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('ipd') ?></a></li> 
                                <?php } if ($this->rbac->hasPrivilege('pharmacy_billing', 'can_view')) {?>                             
                                <li ><a href="#pharmacy" onclick="load_pharmacy_data()" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('pharmacy') ?></a></li>
                                 <?php } if ($this->rbac->hasPrivilege('pathology_billing', 'can_view')) {?>
                                <li ><a href="#pathology" onclick="load_pathology_data()" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('pathology') ?></a></li>
                                 <?php } if ($this->rbac->hasPrivilege('radiology_billing', 'can_view')) { ?>
                                <li ><a href="#radiology" onclick="load_radiology_data()" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('radiology') ?></a></li>
                                 <?php } if ($this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) { ?>
                                <li ><a href="#blood_bank" onclick="load_blood_bank_data()" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('blood_bank') ?></a></li>
                                 <?php } if ($this->rbac->hasPrivilege('ambulance_billing', 'can_view')) {?>
                                <li ><a href="#ambulance" onclick="load_ambulance_data()" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('ambulance') ?></a></li>
                                 <?php } ?>
                            </ul>
                            <div class="tab-content">
                                <?php if ($this->rbac->hasPrivilege('opd_billing', 'can_view')) {?>
                                <div class="tab-pane active" id="opd">
                                </div>
                                <?php } if ($this->rbac->hasPrivilege('ipd_billing', 'can_view')) {?>
                                <!-- end opd -->
                                <!-- start ipd -->
                                <div class="tab-pane " id="ipd">                                    
                                </div> 
                                <?php } if ($this->rbac->hasPrivilege('pharmacy_billing', 'can_view')) {?>                              
                                <!-- end ipd --> 
                                <!-- start pharmacy -->
                                <div class="tab-pane" id="pharmacy">                                   
                                    <div>
                                        <table class="table table-striped table-bordered table-hover load_pharmacy" data-export-title="<?php echo $this->lang->line('pharmacy_bill_details'); ?>">
											<thead>
												<tr>
													<th><?php echo $this->lang->line('bill_no'); ?></th>                                 
													<th><?php echo $this->lang->line('date'); ?></th>                                  
													<th><?php echo $this->lang->line('doctor_name'); ?></th>  
													<th class="text-right"><?php echo $this->lang->line('discount') . " " . '(' . $currency_symbol . ')'; ?></th>
													<th><?php echo $this->lang->line('amount') . " " . '(' . $currency_symbol . ')'; ?></th>
													<th><?php echo $this->lang->line("paid_amount") . " " . '(' . $currency_symbol . ')'; ?></th>
													<th class="text-right"><?php echo $this->lang->line("refund") . " " . '(' . $currency_symbol . ')'; ?></th>
                                                    <th class="text-right"><?php echo $this->lang->line("balance_amount") . " " . '(' . $currency_symbol . ')'; ?></th>
													<th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
												</tr>                                   
											</thead>
											<tbody>                                     
											</tbody>                                    
										</table>
                                    </div>
                                </div>  
                                <?php }?>       
            <!-- end pharmacy -->
            <!-- start pathology -->           
                                <div class="tab-pane" id="pathology">                                    
                                    <div>
                                        <table class="table table-striped table-bordered table-hover load_pathology" data-export-title="<?php echo $this->lang->line('pathology_bill_details'); ?>">
                                    <thead>
                                    <tr>
                            <th><?php echo $this->lang->line('bill_no'); ?></th>                            
                            <th><?php echo $this->lang->line('reporting_date'); ?></th>                            
                            <th><?php echo $this->lang->line('reference_doctor'); ?></th>
                            <th><?php echo $this->lang->line('note'); ?></th>                            
                            <th><?php echo $this->lang->line('tax'); ?>(%)</th>
							<th><?php echo $this->lang->line('discount'); ?>(%)</th>
                            <th><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                            <th><?php echo $this->lang->line('paid_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                            <th class="text-right" ><?php echo $this->lang->line('balance_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                            </tr>                                  
                                    </thead>
                                    <tbody> 
                                    </tbody>                                   
                                </table>
                                    </div>
                                </div>
            <!-- end pthology -->
            <!-- start radiology -->
            
                                <div class="tab-pane" id="radiology">
                                    <div >
                                        <table class="table table-striped table-bordered table-hover load_radiology" data-export-title="<?php echo $this->lang->line('radiology_bill_details'); ?>">
                                            <thead>
                                                <th><?php echo $this->lang->line('bill_no'); ?></th>                                
                                                <th><?php echo $this->lang->line('reporting_date'); ?></th>                                            
                                                <th><?php echo $this->lang->line('reference_doctor'); ?></th>
                                                <th><?php echo $this->lang->line('note'); ?></th>
                                                <th><?php echo $this->lang->line('discount'); ?>(%)</th>
                                                <th><?php echo $this->lang->line('tax'); ?>(%)</th>
                                                <th><?php echo $this->lang->line('amount') . ' (' . $currency_symbol . ')'; ?></th>
                                                <th><?php echo $this->lang->line("paid_amount") . " " . '(' . $currency_symbol . ')'; ?></th>
                                                <th class="text-right" ><?php echo $this->lang->line("balance_amount") . " " . '(' . $currency_symbol . ')'; ?></th>                                 
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="blood_bank">                                   
                                    <div >
                                        <table class="table table-striped table-bordered table-hover load_blood_bank" data-export-title="<?php echo $this->lang->line('blood_bank_bill_details'); ?>">
                                    <thead>
                                     <tr>
                                    <th><?php echo $this->lang->line('bill_no'); ?></th>
                                    <th><?php echo $this->lang->line('issue_date'); ?></th>
                                    <th><?php echo $this->lang->line('received_to'); ?></th>
                                    <th><?php echo $this->lang->line('blood_group'); ?></th>
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('donor_name'); ?></th>
                                    <th><?php echo $this->lang->line('bag_no'); ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('paid_amount') . " (" . $currency_symbol . ")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('balance_amount') . " (" . $currency_symbol . ")"; ?></th>
                                </tr>                                  
                                    </thead>
                                    <tbody>                                      

                                    </tbody>
                                </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="ambulance">                                    
                                    <div >
                                        <table class="table table-striped table-bordered table-hover load_ambulance" data-export-title="<?php echo $this->lang->line('ambulance_bill_details'); ?>">
                                            <thead>
                                                <th><?php echo $this->lang->line('ambulance_no'); ?></th>                               
                                                <th><?php echo $this->lang->line('vehicle_number'); ?></th>  
                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <th class="text-right"><?php echo $this->lang->line('discount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                                <th class="text-right"><?php echo $this->lang->line('amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                                <th class="text-right"><?php echo $this->lang->line('paid_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                                <th class="text-right"><?php echo $this->lang->line('balance_amount') . " " . '(' . $currency_symbol . ')'; ?></th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
            </div>
        </div>   <!-- /.row -->

<div class="modal fade" id="viewDetailReportModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='action_detail_report_modal'>
                   </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('bill_details'); ?></h4> 
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportbilldata"></div>
            </div>
        </div>
    </div>    
</div>
<div class="modal fade" id="viewModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deletebill'>
                        <a href="#" data-target="#edit_prescription"  data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('bill') . " " . $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0 min-h-3">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addPaymentModal" role="dialog" aria-labelledby="follow_up">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip"data-placement="bottom" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('payments'); ?></h4>
            </div>
            <div class="modal-body pt20 pb0 min-h-3 pup-scroll-area">
            </div>
        </div>
    </div>
</div>
 
<div class="modal fade" id="addrefundPaymentModal" style="z-index: 1600;"  role="dialog" aria-labelledby="follow_up">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" data-dismiss="modal">&times;</button>
            <div class="modalicon"> 
                     <div id='allpayments_print'>
                    </div>
                </div>
                <h4 class="modal-title" id="modal_title"><?php echo $this->lang->line('payments'); ?></h4>
            </div>
            <div class="modal-body pb0">
            </div>
        </div>
    </div>
</div> 

<div class="modal fade" id="allpayments" tabindex="-1" role="dialog" aria-labelledby="follow_up">   
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close"  data-dismiss="modal">&times;</button>
               <div class="modalicon"> 
                     <div id='allpayments_print'>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('payments'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0" id="allpayments_result">
            </div>
        </div>
    </div>
</div>
 
<div class="modal fade" style="z-index: 1400;" id="generate_bill"  role="dialog" aria-labelledby="follow_up">   
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon"> 
                    <div id='refund_print'>
                    </div>
                </div>                
                <h4 class="modal-title"><?php echo $this->lang->line('bill'); ?></h4>
            </div>
            <div class="scroll-area">
            <div class="modal-body pt0 pb0" id="bill_result">

            </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myPaymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-mid" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_payment'); ?></h4> 
            </div>
        <form id="add_payment" accept-charset="utf-8" method="post" class="ptt10">
            <div class="modal-body pb0 pt0">
                <input type="hidden" name="module_id" id="module_id" class="form-control" >
                <input type="hidden" name="module_name" id="module_name" class="form-control" >
                <input type="hidden" name="case_reference_id" id="case_reference_id" class="form-control">
                            <div class="row">
                              <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small> 
                                        <input type="text" name="payment_date" id="date" class="form-control datetime" autocomplete="off">
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                        <input type="text" name="amount" id="amount" class="form-control">  
                                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                    </div>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('payment_mode'); ?></label> 
                                        <select class="form-control payment_mode" name="payment_mode">
                                        <?php foreach ($payment_mode as $key => $value) {
                                            ?>
                                            <option value="<?php echo $key ?>" <?php
                                            if ($key == 'cash') {
                                                echo "selected";
                                            }
                                            ?>><?php echo $value ?></option>
                                        <?php } ?>
                                        </select>    
                                        <span class="text-danger"><?php echo form_error('apply_charge'); ?></span>
                                    </div>
                                </div>
                            </div>
                          <div class="row cheque_div" style="display: none;">                           
                                     <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small> 
                                        <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                        <span class="text-danger"><?php echo form_error('cheque_no'); ?></span>
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small> 
                                        <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                        <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                                        <input type="file" class="filestyle form-control"   name="document">
                                        <span class="text-danger"><?php echo form_error('document'); ?></span> 
                                    </div>
                                </div>
                                </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('note'); ?></label> 
                                        <input type="text" name="note" id="note" class="form-control"/>
                                    </div>
                                </div>
                            </div> 
                        </div>                 
                        <div class="modal-footer">
                            <button type="submit" id="add_paymentbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                        </div>        
                    </form>
                </div>
            </div> 
    </section><!-- /.content -->
</div>  

<div class="modal fade" id="billSummaryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon modal_action">                   
                </div>
                <h4 class="modal-title"><span id="patient_bill_summary"> </span> <?= $this->lang->line("bill_summary"); ?></h4>
            </div>           
                <div class="modal-body ptt10 pb0 pup-scroll-area">
                    <div id="billSummaryData">                        
                    </div>               
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
     $(document).on('change','.death_status',function(){
      var status=$(this).val();
      if(status == "1"){
         $('.filestyle','#addPaymentModal').dropify();
          $('.filestyle','#add_refund').dropify();
        $('.death_status_div').css("display", "block");
        $('.reffer_div').css("display", "none");
      }else if(status == "2"){
        $('.reffer_div').css("display", "block");
         $('.death_status_div').css("display", "none");
      }else{
        $('.reffer_div').css("display", "none");
         $('.death_status_div').css("display", "none");
      }
    });

     $(document).on('change','.payment_mode',function(){
      var mode=$(this).val();
      if(mode == "Cheque"){
         $('.filestyle','#addPaymentModal').dropify();
          $('.filestyle','#add_refund').dropify();
        $('.cheque_div').css("display", "block");
      }else{
        $('.cheque_div').css("display", "none");
      }
    });

    $(document).on('click','.add_payment',function(e){
        $('.cheque_div').css("display", "none");
        $('#add_payment').trigger("reset");
        var record_id=$(this).data('recordId');
        var payment_module=$(this).data('module');
        var caseid =$(this).data('caseid');
        var amount =$(this).data('totalamount');
        $('#amount').val(amount);
        $('#module_id').val(record_id);
        $('#module_name').val(payment_module);
        $('#case_reference_id').val(caseid);
        $('#myPaymentModal').modal({backdrop:'static'});
     });
	 
	   $(document).on('click','.add_bloodbankpayment',function(){  
            var record_id=$(this).data('recordId');
            var patient_id=$(this).data('patientId'); 
            var add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading'); 
            payment_modal.modal('show'); 
            getbloodbankPayments(record_id,patient_id);           

    });
	
   $(document).on('click','.payment_refund',function(){  
            if(get_case_id()!==0){               
               var payment_modal=$('#addrefundPaymentModal');
            payment_modal.addClass('modal_loading'); 
            payment_modal.modal('show'); 
            $.ajax({
            url: base_url+'admin/bill/getrefund/'+get_case_id(),
            type: "POST",           
            dataType: 'json',
               beforeSend: function() {
               
               }, 
            success: function (data) {             
                
                 $('.modal-body',payment_modal).html(data.page);
           $('.filestyle','#addrefundPaymentModal').dropify();
           $('.date','#addrefundPaymentModal').trigger("change");
              payment_modal.removeClass('modal_loading'); 
            },

             error: function(xhr) { // if error occured
          alert("Error occured.please try again");            
               
      },
      complete: function() {
            payment_modal.removeClass('modal_loading');      
      }
        }); 
        }else{
           errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>'); 
        }
    });

	  $(document).on('click','.patient_discharge',function(){  
            if(get_case_id()!==0){               
               var payment_modal=$('#addrefundPaymentModal');
            payment_modal.addClass('modal_loading'); 
            payment_modal.modal('show'); 
            $.ajax({ 
            url: base_url+'admin/bill/patient_discharge/'+get_case_id(),
            type: "POST",
            data:{'module_type':'bill'},
            dataType: 'json',
               beforeSend: function() {
               
               }, 
            success: function (data) {              
                
           $('.modal-body',payment_modal).html(data.page);
           $('.filestyle','#addrefundPaymentModal').dropify();
           $('.date','#addrefundPaymentModal').trigger("change");
              payment_modal.removeClass('modal_loading'); 
            },

             error: function(xhr) { // if error occured
          alert("Error occured.please try again");            
               
      },
      complete: function() {
            payment_modal.removeClass('modal_loading');      
      }
        }); 
        }else{
           errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>'); 
        }
    });

	function getbloodbankPayments(record_id,patient_id=null){
         var payment_modal=$('#addPaymentModal');
        
        $.ajax({
            url: '<?php echo base_url() ?>admin/bill/getBloodbankTransaction',
            type: "POST",
            data: {'id': record_id,'patient_id':patient_id},
            dataType:"JSON",
            beforeSend: function(){
   
            },          
            success: function (data) {
         
           $('.modal-body',payment_modal).html(data.page);
           $('.filestyle','#addPaymentModal').dropify();
            payment_modal.removeClass('modal_loading');               
          
            },
             error: function () {
           
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){
          
             payment_modal.removeClass('modal_loading'); 
            }
        });
    }
	
	 $(document).on('submit','#add_partial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var billing_id=$("input[name='billing_id']",'#add_partial_payment').val();
            var form = $(this);    
            var btn = clicked_btn;
            btn.button('loading');
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,              
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                         getbloodbankPayments(billing_id);
                        }
                     btn.button('reset');
                },
                error: function () {

                },
                complete: function(){
                 btn.button('reset');
   }
            }); 
        });

     $(document).on('submit','#add_partial_payment_ambulance', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var billing_id=$("input[name='billing_id']",'#add_partial_payment_ambulance').val();

            var form = $(this);    
            var btn = clicked_btn;
            btn.button('loading');
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,              
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);						 
                         getambulancePayments(billing_id);						 
                        }
                     btn.button('reset');
                },
                error: function () {

                },
                complete: function(){
                 btn.button('reset');
   }
            }); 
        });

      $(document).on('submit','#add_refund', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var form = $(this);    
            var btn = clicked_btn;
            btn.button('loading');			
			var amount = $('#amount').val();			 
			$('#replace_amount').html('<?php echo $currency_symbol; ?>' + amount );			
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                dataType: 'JSON',               
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        $("#addrefundPaymentModal").modal('hide');
                         
                        }
                     btn.button('reset');
                },
                error: function () {

                },
                complete: function(){
                 btn.button('reset');
   }
            }); 
        });

      $(document).on('click','.view_payment',function(e){        
        var record_id=$(this).data('recordId');
        var caseid =$(this).data('case_id');
         var module_type =$(this).data('module_type');
        $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/getpayment',
          type: "POST",
          data:{'case_id':caseid,'id':record_id,'module_type':module_type},
          dataType: 'json',
           beforeSend: function() {                 
             
          },
          success: function(res) {
            $('#allpayments_print').html(' <a href="javascript:void(0);"   class=" print_transactions" data-record-id="'+record_id+'" data-toggle="tooltip" title="" data-module_type="'+module_type+'" data-case_id="'+caseid+'"><i class="fa fa-print"></i> </a> ');
            $('#allpayments_result').html(res.page);
            $('#allpayments').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
          },
             error: function(xhr) { // if error occured
          alert("Error occured.please try again");                 
              
         },
              complete: function() {                  
                 
             }
      });        
     }); 

     $(document).ready(function (e) {
        $("#add_payment").on('submit', (function (e) {
            e.preventDefault();         
            $.ajax({
                url: base_url+'admin/bill/makepayment',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                 beforeSend: function(){
                  $("#add_paymentbtn").button("loading");
                 },
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        var module_name =$('#module_name').val();
                        if(module_name=='pharmacy'){
                            load_pharmacy_data();
                        }else if(module_name=='pathology'){
                            load_pathology_data();
                        }else if(module_name=='radiology'){
                            load_radiology_data();
                        }else if(module_name=='ipd'){
                            load_ipd_data();
                        }else if(module_name=='opd'){
                            load_opd_data();
                        }
                     
                        $('#myPaymentModal').modal('toggle');
                        $("#add_paymentbtn").button("reset");
                        $('#myPaymentModal').modal('hide');
                    }                    
                },
                error: function () {
                    $("#add_paymentbtn").button('reset');
                },
  
                complete: function(){
                    $("#add_paymentbtn").button('reset');                    
                }
            });
        }));
    }); 
 
    $(document).ready(function (e) {
            $('ul.nav-tabs a[href="#opd"]').tab('show');
            <?php if ($this->rbac->hasPrivilege('opd_billing', 'can_view')) {?>
                
                 $('[href="#opd"]').tab('show');
            load_opd_data();
            <?php  }elseif ($this->rbac->hasPrivilege('ipd_billing', 'can_view')) {?>
                $('[href="#ipd"]').tab('show');
                load_ipd_data();                
            <?php  }elseif ($this->rbac->hasPrivilege('pharmacy_billing', 'can_view')) {?>
                $('[href="#pharmacy"]').tab('show');                 
                load_pharmacy_data();
            <?php  }elseif($this->rbac->hasPrivilege('pathology_billing', 'can_view')) {?>
                $('[href="#pathology"]').tab('show');                
                load_pathology_data();
            <?php  }elseif ($this->rbac->hasPrivilege('radiology_billing', 'can_view')) {?>
                $('[href="#radiology"]').tab('show');                
                load_radiology_data();
            <?php  }elseif ($this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) {?>
                $('[href="#blood_bank"]').tab('show');                
                 load_blood_bank_data();
             <?php  }elseif ($this->rbac->hasPrivilege('ambulance_billing', 'can_view')) {?>
                $('[href="#ambulance"]').tab('show');               
                load_ambulance_data();
             <?php } ?>
            get_patientdetails('<?php echo $case_id; ?>');

        $("#formsearch").on('submit', (function (e) {
            $("#serach_btn").button('loading');
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/bill/get',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        $('ul.nav-tabs a[href="#opd"]').tab('show');
                        <?php if ($this->rbac->hasPrivilege('opd_billing', 'can_view')) {?>
                            
                             $('[href="#opd"]').tab('show');
                        load_opd_data();
                        <?php  }elseif ($this->rbac->hasPrivilege('ipd_billing', 'can_view')) {?>
                            $('[href="#ipd"]').tab('show');
                            load_ipd_data();
                            
                        <?php  }elseif ($this->rbac->hasPrivilege('pharmacy_billing', 'can_view')) {?>
                            $('[href="#pharmacy"]').tab('show');
                             
                            load_pharmacy_data();
                        <?php  }elseif($this->rbac->hasPrivilege('pathology_billing', 'can_view')) {?>
                            $('[href="#pathology"]').tab('show');
                            
                            load_pathology_data();
                        <?php  }elseif ($this->rbac->hasPrivilege('radiology_billing', 'can_view')) {?>
                            $('[href="#radiology"]').tab('show');
                            
                            load_radiology_data();
                        <?php  }elseif ($this->rbac->hasPrivilege('blood_bank_billing', 'can_view')) {?>
                            $('[href="#blood_bank"]').tab('show');
                            
                             load_blood_bank_data();
                         <?php  }elseif ($this->rbac->hasPrivilege('ambulance_billing', 'can_view')) {?>
                            $('[href="#ambulance"]').tab('show');
                           
                            load_ambulance_data();
                         <?php } ?>
                       get_patientdetails(data.case_id);
                    }
                    $("#serach_btn").button('reset');
                },
                error: function () {

                }
            });            
        }));
    });

    function get_patientdetails(case_id){
        $.ajax({
                url: base_url+'admin/bill/getDetailsByCaseId/'+case_id,
                type: "POST",
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if(data.status==1){
                        $('#patient_details').html(data.page);
                    }else{
                        errorMsg('<?php echo $this->lang->line("no_record_found"); ?>');
                        $('#patient_details').html('');
                    }
                },
                error: function () {

                }
            });
    }

    function get_case_id(){
      var case_id=$('#case_id').val();
       if (isNaN(case_id)) {
        errorMsg('<?php echo $this->lang->line("case_id_not_valid"); ?>');
        $('#case_id').val('');
        $('#patient_details').html('');
        return 0; 
        }else{
          if(case_id==''){ 
        return 0;
      }else{
        return case_id;
      }   
        } 
    }

    function load_opd_data(){
        if(get_case_id()!==0){            
            $.ajax({
            url: base_url+'admin/bill/getopd/'+get_case_id(),
            type: "POST",
           
            dataType: 'json',
               beforeSend: function() {
                // createModal.addClass('modal_loading');
               },
            success: function (data) {
                
                $('#opd').html(data.page);
            },

             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>"); 
      },
      complete: function() {            
     
      }
        }); 
        }else{
           errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>'); 
        }      
    }

    function load_ipd_data(){
           if(get_case_id()!==0){
       $.ajax({
            url: base_url+'admin/bill/getipd/'+get_case_id(),
            type: "POST",
           
            dataType: 'json',
               beforeSend: function() {
                // createModal.addClass('modal_loading');
               },
            success: function (data) {                
                $('#ipd').html(data.page);
            },

             error: function(xhr) { // if error occured
            alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");           
               
      },
      complete: function() {            
     
      }
        }); 
         }else{
           errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>'); 
        }
    }

     function load_pharmacy_data(){
          if(get_case_id()!==0){
        ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('load_pharmacy','admin/bill/getpharmacy/'+get_case_id(),[],[],100,
            [
        {  "sWidth": "105px", "aTargets": [ -1,-2,-3,-4,-5 ] ,'sClass': 'dt-body-right dt-head-right'},
     
            ]);
    });
} ( jQuery ) ) 
         }else{
           errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>');  
        }
    }

     function load_pathology_data(){
        if(get_case_id()!==0){
        ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('load_pathology','admin/bill/getpathology/'+get_case_id(),[],[],100,
            [
        {  "sWidth": "105px", "aTargets": [ -1,-2,-3,-4,-5 ] ,'sClass': 'dt-body-right dt-head-right'},
     
            ]);
    });
} ( jQuery ) ) 
        }else{
          errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>'); 
        }
    }

     function load_radiology_data(){
        if(get_case_id()!==0){
        ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('load_radiology','admin/bill/getradiology/'+get_case_id(),[],[],100,
            [
        {  "sWidth": "105px", "aTargets": [ -1,-2,-3,-4,-5 ] ,'sClass': 'dt-body-right dt-head-right'},
     
            ]);
    });
} ( jQuery ) )
}else{
          errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>');  
        } 
    }

    function load_blood_bank_data(){
        if(get_case_id()!==0){
        ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('load_blood_bank','admin/bill/getbloodbank/'+get_case_id(),[],[],100,
            [
{ "sWidth": "150px", "bSortable": false, "aTargets": [ -1,-2,-3] ,'sClass': 'dt-head-right dt-body-right'}
            ] );
    });
} ( jQuery ) )
}else{
          errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>');  
        } 
    }

    $(document).ready(function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        
        $(".date").datepicker({
            format: date_format,
            autoclose: true,
            todayHighlight: true
        });
    });

$(document).on('click','.print_charge',function(){    

      var $this = $(this);
         var record_id=$this.data('recordId');
         var moduletype=$this.data('moduletype');         
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/printCharge',
          type: "POST",
          data:{'id':record_id,'type':moduletype},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.button('reset');
              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });

$(document).on('click','.print_transactions',function(){
    
      var $this = $(this);
         var record_id=$this.data('recordId');
         var moduletype=$this.data('module_type');  
         var case_id=$this.data('case_id');       
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/printTransaction',
          type: "POST",
          data:{'id':record_id,'module_type':moduletype,'case_id':case_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });
    
    $(document).on('click','.view_generate_bill',function(){    
      var $this = $(this);
         var record_id=$this.data('recordId');
         var moduletype=$this.data('module_type');  
         var case_id=$this.data('case_id');       
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/generate_bill_result',
          type: "POST",
          data:{'id':record_id,'module_type':moduletype,'case_id':case_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
            $('#generate_bill').modal({backdrop:'static'});
            $('#refund_print').html(' <a href="javascript:void(0);"   class=" generate_bill" data-toggle="tooltip" title="Print" data-module_type="ipd_opd" data-case_id="'+case_id+'"><i class="fa fa-print"></i> </a> ');
           $('#bill_result').html(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });

    $(document).on('click','.generate_bill',function(){    
      var $this = $(this);
         var record_id=$this.data('recordId');
         var moduletype=$this.data('module_type');  
         var case_id=$this.data('case_id');       
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/generate_bill',
          type: "POST",
          data:{'id':record_id,'module_type':moduletype,'case_id':case_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });
    
     $(document).on('click','.print_bill',function(){
        let case_id=$(this).data('caseId');      
        var $this = $(this);
         $.ajax({
            url: base_url+'admin/bill/print_patient_bill',
            type: "POST",
            data: {'case_id': case_id},
            dataType: 'json',
            beforeSend: function() {
              $this.button('loading');
            },
            success: function (data) {                          
              popup(data.page);
              $this.button('reset');
            },
             error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                $this.button('reset');               
            },
            complete: function() {
            $this.button('reset');
            }
        });
});

 $(document).on('click','.print_radio_bill',function(){
            var $print_btn = $(this);
            var record_id=$(this).data('recordId');
            $.ajax({
            url: '<?php echo base_url() ?>admin/radio/getBillDetails/',
            type: "POST",
            data: {'id': record_id,'print':true},
            dataType:"JSON",
            beforeSend: function(){
                $print_btn.button('loading');
            },
            complete: function(){
                $print_btn.button('reset');
            },
            success: function (data) {
                popup(data.page);
                     $print_btn.button('reset');
            },
             error: function () {
                     $print_btn.button('reset');
                }
        });
    });

  $(document).on('click','.print_pharmacy_bill',function(){
            var $print_btn = $(this);
            var record_id=$(this).data('recordId');
            $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/',
            type: "GET",
            data: {'id': record_id,'print':true},
            dataType:"JSON",
            beforeSend: function(){
                $print_btn.button('loading');
            },
            complete: function(){
                $print_btn.button('reset');
            },
            success: function (data) {
                popup(data.page);
                     $print_btn.button('reset');
            },
             error: function () {
                     $print_btn.button('reset');
                }
        });
    });
	
  $(document).on('click','.print_bloodbank_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({ 
          url: '<?php echo base_url(); ?>admin/bloodbank/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });

    $(document).on('click','.print_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/radio/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });


 $(document).on('click','.print_trans',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/transaction/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });

    function printbloodbankData(id) {     
           let $this = $('.print_blood_issue');
         
       $this.button('loading');
      $.ajax({
          url: base_url+'admin/bloodbank/printBloodIssueBill',
          type: "POST",
          data:{'id':id},
          dataType: 'json',
           beforeSend: function() {
           $this.button('loading');      
          },
          success: function(res) {
     popup(res.page);       
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
        $this.button('reset');              
         },
              complete: function() {
    $this.button('reset');                 
             }
      })
    }

    function printAmbulanceData(id){
        $.ajax({
            url: base_url + 'admin/vehicle/getBillDetails/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {
                popup(result);
            }
        });
    }

    $(document).on('click','.add_ambulancecallpayment',function(){          
        var record_id=$(this).data('recordId'); 
        var $add_btn= $(this);  
        var payment_modal=$('#addPaymentModal');
        payment_modal.addClass('modal_loading'); 
        payment_modal.modal('show'); 
        getambulancePayments(record_id);
    });
 
    function getambulancePayments(record_id){
        var payment_modal=$('#addPaymentModal');
        var patient_referance_case_id="<?php echo $case_id; ?>";
        $.ajax({
            url: '<?php echo base_url() ?>admin/bill/getAmbulanceCallTransaction',
            type: "POST",
            data: {'id': record_id,'patient_referance_case_id':patient_referance_case_id},
            dataType:"JSON",
            beforeSend: function(){
            },          
            success: function (data) {
         
           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading'); 
            },
             error: function () {
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){
             payment_modal.removeClass('modal_loading'); 
            }
        });
    }

    $(document).on('click','.add_radio_payment',function(){          
            var record_id=$(this).data('recordId'); 
            var $add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading'); 
            payment_modal.modal('show'); 
            getradioPayments(record_id);
    });
  
        $(document).on('click','.print_radio_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/radio/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });
    
    function viewPharmacyDetail(id){
         var view_modal=$('#viewModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getBillDetails/',
            type: "GET",
            data: {'id': id,'is_bill': 'yes'},
            dataType:"JSON",
            beforeSend: function(){
                $('#reportdata,#edit_deletebill').html("");
           $('#viewModal').modal('show');
           view_modal.addClass('modal_loading');
           },
           complete: function(){
             view_modal.removeClass('modal_loading');
           },
            success: function (data) {
                $('#reportdata').html(data.page);
                $('#edit_deletebill').html(data.actions);
                view_modal.removeClass('modal_loading');
            },
        });
    }

     $(document).on('click','.add_pharmacypayment',function(){  
            var record_id=$(this).data('recordId'); 
            var $add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading'); 
            payment_modal.modal('show'); 
            getPharmacyPayments(record_id);
    });

     $(document).on('submit','#add_bill_partial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var pharmacy_bill_basic_id=$("input[name='pharmacy_bill_basic_id']",'#add_bill_partial_payment').val();
            var form = $(this);    
            var btn = clicked_btn;
            btn.button('loading');
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                dataType: 'JSON',               
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                         getPharmacyPayments(pharmacy_bill_basic_id);
                        }
                     btn.button('reset');
                },
                error: function () {

                },
                complete: function(){
                 btn.button('reset');
   }
            }); 
        });

      function getPharmacyPayments(record_id){
         var payment_modal=$('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/pharmacy/getPharmacyTransaction',
            type: "POST",
            data: {'id': record_id,'is_bill':'yes'},
            dataType:"JSON",
            beforeSend: function(){
            },          
            success: function (data) {         
           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading');
            },
             error: function () {
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){

             payment_modal.removeClass('modal_loading'); 
            }
        });
    }

       $(document).on('click','.print_pharmacyBillReceipt',function(){
      var $this = $(this);
      var record_id=$this.data('recordId')
      $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/pharmacy/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });

 $(document).on('click','.view_pathology_detail',function(){
         var id=$(this).data('recordId');
         PatientPathologyDetails(id,$(this));
       });
        function PatientPathologyDetails(id,btn_obj){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({
            url: base_url+'admin/pathology/getPatientPathologyDetails',
            type: "POST",
            data: {'id': id,'is_bill': 'yes'},
            dataType: 'json',
            beforeSend: function() {
              $this.button('loading');
                modal_view.addClass('modal_loading');                
               },
            success: function (data) {                      
             $('#viewDetailReportModal .modal-body').html(data.page);  
             $('#viewDetailReportModal #action_detail_report_modal').html(data.actions); 
             $('#viewDetailReportModal').modal('show');
              modal_view.removeClass('modal_loading');
            },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
             $this.button('reset');
                modal_view.removeClass('modal_loading');
           },
           complete: function() {
            $this.button('reset');
                modal_view.removeClass('modal_loading');          
           }
        });  
        }

         $(document).on('click','.print_pathology_bill',function(){
    var id=$(this).data('recordId');      
        var $this = $(this);   
        $.ajax({
            url: base_url+'admin/pathology/getBillDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.button('loading');               
               },
            success: function (data) {    
           popup(data.page);
            },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
             $this.button('reset');               
      },
      complete: function() {
            $this.button('reset');     
      }
        });
    }); 
          $(document).on('click','.add_pathology_payment',function(){  
            var record_id=$(this).data('recordId'); 
            var $add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading');               
            payment_modal.modal('show');
            getpathologyPayments(record_id);
    });

   function getpathologyPayments(record_id){
         var payment_modal=$('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/pathology/getPathologyTransaction',
            type: "POST",
            data: {'id': record_id,'is_bill':'yes'},
            dataType:"JSON",
            beforeSend: function(){
            },          
            success: function (data) {         
           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading');  
            },
             error: function () {
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){
             payment_modal.removeClass('modal_loading'); 
            }
        });
    }
	
      $(document).on('submit','#add_pathopartial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var pathology_billing_id=$("input[name='pathology_billing_id']",'#add_pathopartial_payment').val();
            var form = $(this);    
            var btn = clicked_btn;
            btn.button('loading');
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'JSON',               
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                         getpathologyPayments(pathology_billing_id);
                        }
                     btn.button('reset');
                },
                error: function () {

                },
                complete: function(){
                 btn.button('reset');
   }
            }); 
        });

      $(document).on('click','.print_patho_receipt',function(){
      var $this = $(this);
         var record_id=$this.data('recordId')
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/pathology/printTransaction',
          type: "POST",
          data:{'id':record_id},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });
    
    $(document).on('click','.print_report',function(){
       var id=$(this).data('recordId');  
       var $this = $(this);   
       $.ajax({
            url: base_url+'admin/radio/printPatientReportDetail',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.button('loading');
               },
            success: function (data) {       
          popup(data.page);
            },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
             $this.button('reset');               
      },
      complete: function() {
            $this.button('reset');     
      }
        });
    });
    
    $(document).on('click','.view_radio_detail',function(){
         var id=$(this).data('recordId');
         PatientRadiologyDetails(id,$(this));
       });
        function PatientRadiologyDetails(id,btn_obj){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({
            url: base_url+'admin/radio/getPatientRadiologyDetails',
            type: "POST",
            data: {'id': id,'is_bill':'yes'},
            dataType: 'json',
            beforeSend: function() {
              $this.button('loading');
                modal_view.addClass('modal_loading');                
               },
            success: function (data) {                      
             $('#viewDetailReportModal .modal-body').html(data.page);  
             $('#viewDetailReportModal #action_detail_report_modal').html(data.actions);
             $('#viewDetailReportModal').modal('show');
              modal_view.removeClass('modal_loading');
            },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
             $this.button('reset');
                modal_view.removeClass('modal_loading');
           },
           complete: function() {
            $this.button('reset');
                modal_view.removeClass('modal_loading');          
           }
        });  
        }

         $(document).on('submit','#add_radio_partial_payment', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var radiology_billing_id=$("input[name='radiology_billing_id']",'#add_radio_partial_payment').val();
            var form = $(this);    
            var btn = clicked_btn;
            btn.button('loading');
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,                 
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                         getradioPayments(radiology_billing_id);
                        }
                     btn.button('reset');
                },
                error: function () {

                },
                complete: function(){
                 btn.button('reset');
   }
            }); 
        });

         $(document).on('click','.add_radio_payment',function(){  
            var record_id=$(this).data('recordId'); 
            var $add_btn= $(this);  
            var payment_modal=$('#addPaymentModal');
            payment_modal.addClass('modal_loading'); 
            payment_modal.modal('show'); 
            getradioPayments(record_id);
    });

   function getradioPayments(record_id){
         var payment_modal=$('#addPaymentModal');
        $.ajax({
            url: '<?php echo base_url() ?>admin/radio/getRadiologyTransaction',
            type: "POST",
            data: {'id': record_id,'is_bill': 'yes'},
            dataType:"JSON",
            beforeSend: function(){
            },          
            success: function (data) {
         
           $('.modal-body',payment_modal).html(data.page);
            payment_modal.removeClass('modal_loading');      
            },
             error: function () {
             payment_modal.removeClass('modal_loading'); 
            },  complete: function(){
             payment_modal.removeClass('modal_loading'); 
            }
        });
    }    
    
    function load_ambulance_data(){
        if(get_case_id()!==0){
            ( function ( $ ) {
                'use strict';
                $(document).ready(function () {
                    initDatatable('load_ambulance','admin/bill/getambulance/'+get_case_id(),[],[],100,
            [
{ "sWidth": "150px", "bSortable": false, "aTargets": [ -1,-2,-3,-4] ,'sClass': 'dt-head-right dt-body-right'}
            ] );
                });
            } ( jQuery ) )
        }else{
          errorMsg('<?php echo $this->lang->line("the_case_id_field_required"); ?>');  
        } 
    }

    $(document).on('submit','#patient_discharge', function(e){
            e.preventDefault();
            var clicked_btn = $("button[type=submit]");
            var form = $(this);    
            var btn = clicked_btn;
            btn.button('loading');
            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                dataType: 'JSON',               
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        $('#addrefundPaymentModal').modal('hide');
                        }
                     btn.button('reset');
                },
                error: function () {

                },
                complete: function(){
                 btn.button('reset');
   }
            }); 
        });

     $(document).on('click','.print_dischargecard',function(){
         var $this = $(this);
         var record_id=$this.data('recordId');
         var case_id=$this.data('case_id');   
       $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/print_dischargecard',
          type: "POST",
          data:{'id':record_id,'case_id':case_id,'module_type':'bill'},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
           popup(res.page);
          },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });  

      $(document).on('click','.view_dischargecard',function(){
         var $this = $(this);
         var record_id=$this.data('recordId');
         var case_id=$this.data('case_id'); 
         var payment_modal=$('#addrefundPaymentModal');
            payment_modal.addClass('modal_loading'); 
            payment_modal.modal('show');  
         $this.button('loading');
      $.ajax({
          url: '<?php echo base_url(); ?>admin/bill/print_dischargecard',
          type: "POST",
          data:{'id':record_id,'case_id':case_id,'module_type':'bill'},
          dataType: 'json',
           beforeSend: function() {
                 $this.button('loading');      
          },
          success: function(res) {
            $('#modal_title').html('<?php echo $this->lang->line('discharge_card');?>');
            $('#allpayments_print').html(res.action);
            $('.modal-body',payment_modal).html(res.page);
             payment_modal.removeClass('modal_loading'); 
          },
             error: function(xhr) { // if error occured
              alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                  $this.button('reset');              
         },
              complete: function() {
                   $this.button('reset');                 
             }
      });
  });

      $(document).on('click','.view_radiodetail',function(){
         var id=$(this).data('recordId');
         PatientRadiologyDetails(id,$(this));
    });

    function PatientRadiologyDetails(id,btn_obj){
         var modal_view=$('#viewDetailReportModal');
         var $this = btn_obj;   
        $.ajax({ 
            url: base_url+'admin/radio/getPatientRadiologyDetails',
            type: "POST",
            data: {'id': id,'is_bill': 'yes'},
            dataType: 'json',
            beforeSend: function() {
                $this.button('loading');
                modal_view.addClass('modal_loading');
               },
            success: function (data) {                      
                 $('#viewDetailReportModal .modal-body').html(data.page);  
                 $('#viewDetailReportModal #action_detail_report_modal').html(data.actions);  
                 $('#viewDetailReportModal').modal('show');
                  modal_view.removeClass('modal_loading');
            },
            error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
             $this.button('reset');
                modal_view.removeClass('modal_loading');
           },
            complete: function() {
            $this.button('reset');
                modal_view.removeClass('modal_loading');          
           }
        });  
    }

       $(document).on('click','.print_radiology_bill',function(){
        var id=$(this).data('recordId');      
        var $this = $(this);   
        $.ajax({
            url: base_url+'admin/radio/getBillDetails',
            type: "POST",
            data: {'id': id},
            dataType: 'json',
               beforeSend: function() {
              $this.button('loading');
               },
            success: function (data) {     
           popup(data.page);

            },
             error: function(xhr) { // if error occured
             alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
             $this.button('reset');               
      },
      complete: function() {
            $this.button('reset');     
      }
        });
    });
</script>
<script>
    $(document).on('click','.showbill',function(){
        let $this=$(this);
        let case_id=$(this).data('caseId');
            $.ajax({
            type: 'POST',
            url: base_url+'admin/bill/patient_bill',
            data: {case_reference_id:case_id},
            dataType: 'json',
            beforeSend: function() {
              $this.button('loading');
             },
            success: function (result) {                
                $("#patient_bill_summary").html(result.patient_name);
                $("#billSummaryData").html(result.page);
                $('#billSummaryModal .modal_action').html(result.modal_action);
                $("#billSummaryModal").modal("show");
                $this.button('reset');
            },
             error: function(xhr) { // if error occured
                $this.button('reset');
              alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");    
            },
             complete: function() {
                $this.button('reset');
             }
        });
    }) 

    $(document).on('click','.print_ambulance_receipt',function(){
        var $this = $(this);
        var record_id=$this.data('recordId')
        $this.button('loading');
        $.ajax({ 
            url: '<?php echo base_url(); ?>admin/bill/print_ambulance_Transaction',
            type: "POST",
            data:{'id':record_id},
            dataType: 'json',
            beforeSend: function() {
                    $this.button('loading');        
            },
            success: function(res) {
            popup(res.page);
            },
            error: function(xhr) { // if error occured
            alert("<?php echo $this->lang->line("error_occurred_please_try_again"); ?>");
                    $this.button('reset');
            },
            complete: function() {
                $this.button('reset');                
            }
        });
    });
    
    $(document).ready(function (e) {
        $('#billSummaryModal,#addPaymentModal,#addrefundPaymentModal').modal({
        backdrop: 'static',
        keyboard: false,
        show:false
        });
    }); 

    $(document).on('click','.print_pathology_report',function(){
   var id=$(this).data('recordId');

   var $this = $(this);   
   $.ajax({
        url: base_url+'admin/pathology/printPatientReportDetail',
        type: "POST",
        data: {'id': id},
        dataType: 'json',
           beforeSend: function() {
          $this.button('loading');
           },
        success: function (data) {       
      popup(data.page);
        },

         error: function(xhr) { // if error occured
         alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
         $this.button('reset');           
  },
  complete: function() {
        $this.button('reset'); 
  }
    });
});

    $(document).ready(function() {
        $("#case_id").autocomplete({
            source:function(request, response) {
                $.ajax({
                    type:"GET",
                    url: base_url+'admin/bill/getcaseid',
                    data: { caseid: $("#case_id").val() },
                    dataType:"json",
                    contentType:"application/json; charset=utf-8",
                    success:function(data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.id,
                                value: item.id
                            };
                        }));
                    },
                    error:function(data) {
                        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    }
                });
            }
        });
    });
</script>
