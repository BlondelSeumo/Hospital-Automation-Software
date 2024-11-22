<?php 
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>  
<form id="add_refund" accept-charset="utf-8" action="<?php echo base_url()?>admin/bill/add_refund" method="post">
        <input type="hidden" name="opd_id" value="<?php echo $opd_id;?>" class="form-control" >
        <input type="hidden" name="id" value="<?php echo $id;?>" class="form-control" >
        <input type="hidden" name="ipd_id" value="<?php echo $ipd_id;?>"  class="form-control" >
        <input type="hidden" name="case_reference_id" value="<?php echo $case_id; ?>" class="form-control">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="row">
                      <div class="col-md-6">
                            <div class="form-group" id="dp">
                                <label><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small> 
                                <input type="text" name="payment_date" id="daterefund" value="<?php if(!empty($refund['payment_date'])){  echo $this->customlib->YYYYMMDDTodateFormat($refund['payment_date']); } ?>" class="form-control date" autocomplete="off">
                                <span class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small> 
                                <input type="text" name="amount" value="<?php if(!empty($refund['amount'])){ echo $refund['amount']; } ?>" id="amount" class="form-control">  
                                <span class="text-danger"><?php echo form_error('amount'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('payment_mode') ; ?></label> 
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
                                <input type="text" value="<?php if(!empty($refund['cheque_date'])){ echo $this->customlib->YYYYMMDDTodateFormat($refund['cheque_date']); }?>" name="cheque_date" id="cheque_date" class="form-control date">
                                <span class="text-danger"><?php echo form_error('cheque_date'); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('attach_document'); ?></label>
                                <input type="file" class="filestyle form-control" name="document">
                                <span class="text-danger"><?php echo form_error('document'); ?></span> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('note'); ?></label> 
                                <input type="text" name="note" value="<?php if(!empty($refund['note'])){ echo $refund['note']; } ?>" id="note" class="form-control"/>
                            </div>
                        </div>
                    </div>
    <div class="row">                    
         <div class="box-footer col-md-12">
            <div class="pull-right">
              <button id="add_paymentbtn" type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" value="" class="btn btn-info pull-right printsavebtn" id="saveprint"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button> 
              <?php  if(!empty($refund)){
                ?>
                   <button type="button" style="margin-right:2px; margin-left: 2px" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="print_trans" class=" btn btn-info print_trans pull-right"  value="" data-loading-text="<i class='fa fa-circle-o-notch fa-spi'></i>" data-record-id="<?php echo $refund['id']; ?>"><i class="fa fa-print"></i> <?php echo $this->lang->line('print'); ?></button>
                <?php
              }?>
            </div>
        </div>
    </div>                    
  </div>
</form>