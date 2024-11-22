<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList      = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('components_list'); ?>
                        </h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('blood_bank_components', 'can_add')) {?>
                                <a data-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm addblood"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_components'); ?></a>
                            <?php }?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('components_list'); ?></div>
                        <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('components_list'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('blood_group'); ?></th>
                                    <th><?php echo $this->lang->line('bags'); ?></th>
                                    <th><?php echo $this->lang->line('lot'); ?></th>                                   
                                    <th class="text-right"><?php echo $this->lang->line('institution'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                     
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_components'); ?></h4>
            </div>
            <form id="componentsadd" accept-charset="utf-8" method="post" class="ptt10">
                <div class="scroll-area">   
                    <div class="modal-body pb0 pt0">
                        <div class="row">
                            <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('blood_group'); ?></label><small class="req"> *</small>
                                        <select style="width: 100%" class="form-control select2 blood_group"  name="blood_bank_product_id" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                        foreach ($bloodgroup as $key => $value) {
                                            ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('blood_group') == $key) {
                                            echo "selected";
                                        }
                                        ?>><?php echo $value; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>                                    
                                    </div>
                                </div>                            
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('bag'); ?></label><small class="req"> *</small>
                            <select  style="width: 100%" class="form-control select2 bag_no"  name="blood_donor_cycle_id" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                            </select>
                                </div>
                            </div>                                    
                            </div><!--./row-->
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-striped table-bordered table-hover " cellspacing="0">
                                     <thead>
                                        <tr>
                                            <th width="18%"><?php echo $this->lang->line('components_name'); ?><small class="req"> *</small></th>
                                            <th><?php echo $this->lang->line('bag'); ?><small class="req"> *</small></th>
                                            <th><?php echo $this->lang->line('volume'); ?></th>
                                            <th><?php echo $this->lang->line('unit'); ?></th>
                                            <th><?php echo $this->lang->line('lot'); ?><small class="req"> *</small></th>
                                            <th><?php echo $this->lang->line('institution'); ?></th>
                                        </tr>
                                        <?php
                                            foreach ($components as $key => $value) {
                                            ?>
                                        <tr>
                                            <td><input type="checkbox" name="select[]" value="<?php echo $key; ?>" /> <?php echo $value; ?></td>
                                            <td><input type="text" class="form-control" name="bag_no_<?php echo $key?>" value="" /></td>
                                            <td><input type="text" class="form-control" name="volume_<?php echo $key?>" value="" /></td>
                                            <td><select type="text" class="form-control" name="unit_<?php echo $key?>" value="" ><option value=""> <?php echo $this->lang->line('select')?></option>
                                                <?php 
                                                foreach ($unit_type as $typekey => $typevalue) {
                                                    ?>
                                                <option value="<?php echo $typevalue->id; ?>"><?php echo $typevalue->unit; ?></option><?php
                                                }
                                                ?>
                                             </select></td>
                                            <td><input type="text" class="form-control" name="lot_<?php echo $key?>" value="" /></td>
                                            <td><input type="text" class="form-control" name="institution_<?php echo $key?>" value="" /></td>
                                        </tr>
                                            <?php
                                            }
                                            ?>
                                    </thead>
                                    <tbody>                            
                                    </tbody>
                                </table>
                            </div>    
                        </div><!--./row-->
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" id="formaddbtn" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- dd -->
<div class="modal fade" id="myModaledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit') . " " . $this->lang->line('donor') . " " . $this->lang->line('information'); ?></h4>
            </div>
            <form id="formedit" accept-charset="utf-8"method="post" class="ptt10">
                <div class="scroll-area">
                        <div class="modal-body pt0 pb0">
                                <div class="row">
                                    <input type="hidden" name="id" id="id" value="<?php echo set_value('id'); ?>">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('blood_group'); ?></label><small class="req"> *</small>
                                            <select id="blood_group" name="blood_group"  class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php
                                                    foreach ($bloodgroup as $key => $value) {
                                                        ?>
                                                    <option value="<?php echo $value; ?>" <?php if (set_value('blood_group') == $key) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $value; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label> <?php echo $this->lang->line('gender'); ?></label>
                                            <select class="form-control" id="gender" name="gender">
                                                <option value="<?php echo set_value('gender'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                    foreach ($genderList as $key => $value) {
                                                        ?>
                                                    <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) {
                                                    echo "selected";
                                                }
                                                ?>><?php echo $value; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('father_name'); ?></label>
                                            <input type="text" name="father_name"  id="father_name" value="<?php echo set_value('father_name'); ?>" class="form-control">
                                            <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('contact_no'); ?></label>
                                            <input type="text" name="contact_no" id="contact_no" value="<?php echo set_value('contact_no'); ?>" class="form-control">
                                            <span class="text-danger"><?php echo form_error('contact_no'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="Address"><?php echo $this->lang->line('address'); ?></label>
                                            <textarea name="address"  id="address" value="<?php echo set_value('address'); ?>" class="form-control" ></textarea>
                                        </div>
                                    </div>
                                </div><!--./row-->                        
                    </div>
                </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="submit" id="formeditbtn" data-loading-text="<?php echo $this->lang->line('processing') ?>" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>                    
                </div>
            </div>
          </form>  
        </div>
    </div>
</div>

<div class="modal fade" id="addBloodDetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('donor') . " " . $this->lang->line('blood') . " " . $this->lang->line('details'); ?></h4>
            </div>            
                <form id="donorblood" accept-charset="utf-8" method="post" class="ptt10">
                    <div class="modal-body pb0">
                            <input type="hidden" name="blood_donor_id" id="donor_id">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('donate') . " " . $this->lang->line('date'); ?></label>
                                        <small class="req"> *</small>
                                        <input  name="donate_date" type="text" class="form-control date"/>
                                        <span class="text-danger"><?php echo form_error('quantity'); ?></span>
                                    </div>
                                </div>                              
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('bag'); ?> </label> <small class="req"> *</small>
                                        <input  name="bag_no" type="text" class="form-control"/>
                                        <span class="text-danger"><?php echo form_error('bag_no'); ?></span>
                                    </div>
                                </div>
                                  <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('lot'); ?> </label>
                                        <input  name="lot" type="text" class="form-control"/>
                                    </div>
                                </div>
                                  <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge') . " " . $this->lang->line('type') ?></label><small class="req"> *</small>
                                         <select name="charge_type" class="form-control charge_type">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($charge_type as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value->id; ?>">
                                                <?php echo $value->charge_type; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge_category') ?></label><small class="req"> *</small> 
                                        <select name="charge_category" id="charge_category" style="width: 100%" class="form-control select2 charge_category">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('charge_category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('charge_name') ?></label>      
                                            <select name="charge_id" id="code" style="width: 100%" class="form-control addcharge select2 " >
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")" ?></label>
                                        <input type="text" readonly name="standard_charge" id="addstandard_charge" class="form-control" value="<?php echo set_value('standard_charge'); ?>"> 
                                        <span class="text-danger"><?php echo form_error('standard_charge'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('qty'); ?></label><small class="req"> *</small> 
                                       <input type="text" name="qty" id="qty" class="form-control"> 
                                        <span class="text-danger"><?php echo form_error('qty'); ?></span>
                                    </div>
                                </div>                                 
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('institution'); ?></label>
                                        <input  name="institution" type="text" class="form-control" />
                                    </div>
                                </div>
                            </div><!--./row-->
                                 <div class="divider"></div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('note'); ?></label>
                                                        <textarea name="note" rows="3" id="note" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!--./col-sm-6-->
                                        <div class="col-sm-6">
                                            <table class="printablea4">
                                                <tr>
                                                    <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td width="60%" colspan="2" class="text-right ipdbilltable">
                                                    <input type="text" placeholder="Total" value="0" name="total" id="total" style="width: 30%; float: right" class="form-control total" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-right ipdbilltable">
                                                        <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="Discount" name="discount_percent" id="discount_percent" class="form-control discount_percent" style="width: 70%; float: right;font-size: 12px;"/></td>
                                                    <td class="text-right ipdbilltable">
                                        <input type="text" placeholder="Discount" value="0" name="discount" id="discount" style="width: 50%; float: right" class="form-control discount"/></td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td class="text-right ipdbilltable">
                                                        <h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4>
                                                <input type="text" placeholder="Tax" name="tax_percentage" id="tax_percentage" class="form-control tax_percentage" readonly style="width: 70%; float: right;font-size: 12px;"/></td>
                                                    <td class="text-right ipdbilltable">
                                                        <input type="text" placeholder="Tax" name="tax" value="0" id="tax" style="width: 50%; float: right" class="form-control tax" readonly/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                    <td colspan="2" class="text-right ipdbilltable">
                                                        <input type="text" placeholder="Net Amount" value="0" name="net_amount" id="net_amount" style="width: 30%; float: right" class="form-control net_amount" readonly/></td>
                                                </tr>
                                            </table>

                            <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('payment_mode'); ?></label>
                                        <select class="form-control payment_mode" name="payment_mode">
                                            <?php foreach ($payment_mode as $key => $value) {
    ?>
                                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
<?php
}
?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                                    </div>
                                </div>
                                   <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('payment_amount'). " (" . $currency_symbol . ")"; ?></label><small class="req"> *</small>
                                        <input type="text" name="payment_amount" id="payment_amount" class="form-control payment_amount text-right">
                                         <span class="text-danger"><?php echo form_error('payment_amount'); ?></span>
                                    </div>
                                </div>
                              <div class="cheque_div" style="display:none;">
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('cheque_no'); ?></label><small class="req"> *</small>
                                        <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('cheque_date'); ?></label><small class="req"> *</small>
                                        <input type="text" name="cheque_date" id="cheque_date" class="form-control date">
                                        <span class="text-danger"></span>
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
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('note'); ?></label>
                                        <textarea name="payment_note" id="note" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div><!--./row-->  
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('processing') ?>" id="donorbloodbtn" class="btn btn-info" ><?php echo $this->lang->line('save'); ?></button>
                    
                </div>
            </div>
           </form>  
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_delete'>

                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('donor') . " " . $this->lang->line('information'); ?></h4>
            </div>
            <div class="modal-body pb0">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="view" accept-charset="utf-8" method="get">
                            <div class="table-responsive">
                                <table class="table mb0 table-striped table-bordered examples">
                                    <tr>
                                        <th><?php echo $this->lang->line('donor') . " " . $this->lang->line('name'); ?></th>
                                        <td><span id='donor_names'></span></td>
                                        <th><?php echo $this->lang->line('age'); ?></th>
                                        <td><span id="ages"></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('blood_group'); ?></th>
                                        <td><span id='blood_groups'></span></td>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <td><span id="genders"></span></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('father_name'); ?></th>
                                        <td><span id="father_names"></span></td>
                                        <th><?php echo $this->lang->line('contact_no'); ?></th>
                                        <td><span id="contact_nos"></span>
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('address'); ?></th>
                                        <td><span id='addresss'></span></td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </div><!--./col-md-12-->
                </div><!--./row-->
                <div id="reportdata"></div>
            </div>
        </div>
    </div> 
</div>

<script type="text/javascript">
    $(function () {
        $('#easySelectable').easySelectable();
    })
        $(document).ready(function (e) {

        $('.select2').select2();
    });

         $(document).on('select2:select','.blood_group',function(){
        var bloodgroup=$(this).val();
        getBloodGroupBagNos(bloodgroup,"");
   
    });
     function getBloodGroupBagNos(bloodgroup,bagno){
        console.log(bagno);
    var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
    $.ajax({
          url: base_url+'admin/bloodbank/getbatchbybloodgroup',
          type: "POST",
          data:{'bloodgroup':bloodgroup},
          dataType: 'json',
           beforeSend: function() {
          $('.bag_no').html("");
          },
          success: function(res) {
            console.log(res.batch_list);
              $.each(res.batch_list, function (i, obj)
                {             
                    var sel = "";
                    let volume = obj.volume != null ? obj.volume : "" ;
                    let unit = obj.charge_unit != null ? obj.charge_unit : "" ;
                    
                    if(volume != '' && unit!= ''){var sfsdfsdf =  " (" + volume + " " + unit + ") " ; }else{var sfsdfsdf = '';}
                    
                    div_data += "<option value='" + obj.id + "' available_unit='" + obj.quantity + "'>" + obj.bag_no  + " " + sfsdfsdf + "</option>";

                });
                $('.bag_no').html(div_data);
                $('.bag_no').select2("val", bagno);
          },
             error: function(xhr) { // if error occured
          alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

         },
         complete: function() {

       }
      });
    }
</script>

<script type="text/javascript">
                    (function ($) {
                        //selectable html elements
                        $.fn.easySelectable = function (options) {
                            var el = $(this);
                            var options = $.extend({
                                'item': 'li',
                                'state': true,
                                onSelecting: function (el) {

                                },
                                onSelected: function (el) {

                                },
                                onUnSelected: function (el) {

                                }
                            }, options);
                            el.on('dragstart', function (event) {
                                event.preventDefault();
                            });
                            el.off('mouseover');
                            el.addClass('easySelectable');
                            if (options.state) {
                                el.find(options.item).addClass('es-selectable');
                                el.on('mousedown', options.item, function (e) {
                                    $(this).trigger('start_select');
                                    var offset = $(this).offset();
                                    var hasClass = $(this).hasClass('es-selected');
                                    var prev_el = false;
                                    el.on('mouseover', options.item, function (e) {
                                        if (prev_el == $(this).index())
                                            return true;
                                        prev_el = $(this).index();
                                        var hasClass2 = $(this).hasClass('es-selected');
                                        if (!hasClass2) {
                                            $(this).addClass('es-selected').trigger('selected');
                                            el.trigger('selected');
                                            options.onSelecting($(this));
                                            options.onSelected($(this));
                                        } else {
                                            $(this).removeClass('es-selected').trigger('unselected');
                                            el.trigger('unselected');
                                            options.onSelecting($(this))
                                            options.onUnSelected($(this));
                                        }
                                    });
                                    if (!hasClass) {
                                        $(this).addClass('es-selected').trigger('selected');
                                        el.trigger('selected');
                                        options.onSelecting($(this));
                                        options.onSelected($(this));
                                    } else {
                                        $(this).removeClass('es-selected').trigger('unselected');
                                        el.trigger('unselected');
                                        options.onSelecting($(this));
                                        options.onUnSelected($(this));
                                    }
                                    var relativeX = (e.pageX - offset.left);
                                    var relativeY = (e.pageY - offset.top);
                                });
                                $(document).on('mouseup', function () {
                                    el.off('mouseover');
                                });
                            } else {
                                el.off('mousedown');
                            }
                        };
                    })(jQuery);
</script>

<script>
            $(document).ready(function (e) {
                $("#formadd").on('submit', (function (e) {
                    $("#formaddbtn").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/bloodbank/add',
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
                                window.location.reload(true);
                            }
                            $("#formaddbtn").button('reset');
                        },
                        error: function () {
                        }
                    });
                }));
            });

            $(document).ready(function (e) {
                $("#componentsadd").on('submit', (function (e) {
                    $("#formaddbtn").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/bloodbank/addcomponents',
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
                                window.location.reload(true);
                            }
                            $("#formaddbtn").button('reset');
                        },
                        error: function () {
                        }
                    });
                }));
            });
            $(document).ready(function (e) {
                $("#formedit").on('submit', (function (e) {
                    $("#formeditbtn").button('loading');
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/bloodbank/update',
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
                                window.location.reload(true);
                            }
                            $("#formeditbtn").button('reset');
                        },
                        error: function () {

                        }
                    });
                }));
            });

            function getRecord(id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/bloodbank/getDetails',
                    type: "POST",
                    data: {blood_donor_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#id").val(data.id);
                        $("#donor_name").val(data.donor_name);
                        $("#age").val(data.age);
                        $("#month").val(data.month);
                        $("#blood_group").val(data.blood_group);
                        $("#gender").val(data.gender);
                        $("#father_name").val(data.father_name);
                        $("#address").val(data.address);
                        $("#city").val(data.city);
                        $("#state").val(data.state);
                        $("#contact_no").val(data.contact_no);
                        $("#institution").val(data.institution);
                        $("#lot").val(data.lot);
                        $("#bag_no").val(data.bag_no);
                        $("#quantity").val(data.quantity);
                        $("#updateid").val(id);
                        $('select[id="blood_group"] option[value="' + data.blood_group + '"]').attr("selected", "selected");
                        $('select[id="gender"] option[value="' + data.gender + '"]').attr("selected", "selected");
                        $("#viewModal").modal('hide');
                        holdModal('myModaledit');
                    },
                })
            }
            $(document).ready(function (e) {
                var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
                $('#dates_of_birth , #date_of_birth').datepicker();
            });
			
            function viewDetail(id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/bloodbank/getDetails',
                    type: "POST",
                    data: {blood_donor_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $.ajax({
                            url: '<?php echo base_url(); ?>admin/bloodbank/getDonorBloodBatch',
                            type: "POST",
                            data: {blood_donor_id: id},
                            success: function (data) {
                                $('#reportdata').html(data);
                            },
                        });
                        $("#donor_names").html(data.donor_name);
                        $("#ages").html(data.age + " Year " + data.month + " Month");
                        $("#blood_groups").html(data.blood_group);
                        $("#genders").html(data.gender);
                        $("#father_names").html(data.father_name);
                        $("#contact_nos").html(data.contact_no);
                        $("#addresss").html(data.address);
                        $("#edit_delete").html("<?php if ($this->rbac->hasPrivilege('blood_donor', 'can_edit')) {?><a href='#' onclick='getRecord(" + id + ")' data-toggle='tooltip' title='' data-original-title='Edit'><i class='fa fa-pencil'></i></a><?php }if ($this->rbac->hasPrivilege('blood_donor', 'can_delete')) {?><a onclick='delete_record(" + id + ")'  href='#'  data-toggle='tooltip'  data-original-title='Delete'><i class='fa fa-trash'></i></a><?php }?>");
                        holdModal('viewModal');
                    },
                });
            }
            
            function delete_record(id) {
                if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/bloodbank/delete/' + id,
                        type: "POST",
                        data: {id: ''},
                        dataType: 'json',
                        success: function (data) {
                            successMsg('<?php echo $this->lang->line('success_message'); ?>')
                            window.location.reload(true);
                        }
                    });
                }
            }

            function addDonorBlood(id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/bloodbank/getBloodBank',
                    type: "POST",
                    data: {blood_donor_id: id},
                    dataType: 'json',
                    success: function (data) {
                        $("#donor_id").val(id);
                        holdModal('addBloodDetailModal');
                    },
                })
            }
			
            $(document).ready(function (e) {
                $("#donorblood").on('submit', (function (e) {
                   var button_loading= $("#donorbloodbtn");

                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/bloodbank/donorCycle',
                        type: "POST",
                        data: new FormData(this),
                        dataType: 'json',
                        contentType: false,
                        cache: false,
                        processData: false,
                        beforeSend: function(){
                 button_loading.button("loading");
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
                                window.location.reload(true);
                            }
                            $("#donorbloodbtn").button('reset');
                        },
                        error: function () {
                 button_loading.button('reset');
                },
  
                complete: function(){
                 button_loading.button('reset');
                }
                    });
                }));
            });
            function holdModal(modalId) {
                $('#' + modalId).modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            }

 $("#myModal").on('hidden.bs.modal', function (e) {     
    $(".blood_group").select2("val", "");
    $(".bag_no").html('').select2({data: [{id: '', text: '<?php echo $this->lang->line('select'); ?>'}]});
     $('form#componentsadd').find('input:text, input:password, input:file, textarea').val('');
     $('form#componentsadd').find('select option:selected').removeAttr('selected');
     $('form#componentsadd').find('input:checkbox, input:radio').removeAttr('checked');
 });

$(".addDonorBlood").click(function(){
    $('#donorblood').trigger("reset");
});
</script>

<script type="text/javascript">
        $(document).on('change','.charge_type',function(){
        var charge_type=$(this).val();     
        $('.charge_category').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
     getcharge_category(charge_type,"");
    });

    function getcharge_category(charge_type,charge_category) {
           var div_data = "";
           if(charge_type != ""){

        $.ajax({
            url: base_url+'admin/charges/get_charge_category',
            type: "POST",
            data: {charge_type: charge_type},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";
                });
                $('.charge_category').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                $('.charge_category').append(div_data);
                $('.charge_category').select2("val", charge_category);
            }
        });
         }
    }

 $(document).on('select2:select','.charge_category',function(){
    var charge_category=$(this).val();      
    $('.charge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");   
    $('.addcharge').html("<option value=''><?php echo $this->lang->line('loading') ?></option>");
    getchargecode(charge_category,"");
 });

    function getchargecode(charge_category,charge_id) {      
      var div_data = "<option value=''><?php echo $this->lang->line('select'); ?></option>";
      if(charge_category != ""){
          $.ajax({
            url: base_url+'admin/charges/getchargeDetails',
            type: "POST",
            data: {charge_category: charge_category},
            dataType: 'json',
            success: function (res) {
                $.each(res, function (i, obj)
                {
                    var sel = "";
                    div_data += "<option value='" + obj.id + "'>" + obj.name + "</option>";

                });
                $('.addcharge').html(div_data);
                $(".addcharge").select2("val", charge_id);            
             
            }
        });
      }
    }

   $(document).on('select2:select','.addcharge',function(){
        var charge=$(this).val();
        var orgid="";       
     
      $.ajax({
            url: '<?php echo base_url(); ?>admin/patient/getChargeById',
            type: "POST",
            data: {charge_id: charge, organisation_id: orgid},
            dataType: 'json',
            success: function (res) {
                if (res) {
                         
                    var quantity=$('#qty').val();
                    quantity=  (quantity == "")? 0 :quantity;
                     var total_amout=parseFloat(res.standard_charge) * quantity;
                    $('#total').val(total_amout);
                    $('#addstandard_charge').val(res.standard_charge);
                     var discount_percent= $('#discount_percent').val();
                    $('#tax_percentage').val(res.percentage);
                     var discount_amount = parseFloat(total_amout*discount_percent/100);
                     var tax = $('#tax_percentage').val();
                    var tax_amount=  parseFloat((total_amout-discount_amount) * tax / 100)
                    console.log(total_amout-discount_amount);
                    $('#tax').val(tax_amount);
                    $('#net_amount').val((total_amout-discount_amount)+tax_amount);                  
                }
            }
        });
 }); 

   $(document).on('change keyup input paste','#qty',function(){
        var quantity=$(this).val();
        var standard_charge=  $('#addstandard_charge').val();       
        var tax_percent=$('#tax_percentage').val();
        var total_charge=(standard_charge == "" )? 0 :standard_charge;
        console.log(total_charge);
        var apply_charge=isNaN(parseFloat(total_charge)*parseFloat(quantity))? 0 : parseFloat(total_charge)*parseFloat(quantity); 
         $('#total').val(apply_charge);
        var discount_percent= $('#discount_percent').val();     
       
        var discount_amount= isNaN((apply_charge*discount_percent)/100) ? 0 : (apply_charge*discount_percent)/100;
        var final_amount=apply_charge-discount_amount;
console.log(final_amount);
        $('#discount').val(discount_amount);
        $('#tax').val(((final_amount*tax_percent)/100));
        $('#net_amount,#payment_amount').val(final_amount+((final_amount*tax_percent)/100));
    });

    $(document).on('change keyup input paste','#discount',function(){
         calculateAmt(false);
    });

        $(document).on('change keyup input paste','#discount_percent',function(){
        calculateAmt(true);
        });

        function calculateAmt(is_percentage){
        var tot_amt=parseFloat($('#total').val());
            if(is_percentage){
               var dis_per=$('#discount_percent').val();
               var dis_amt = parseFloat(tot_amt*dis_per/100);
               $('#discount').val(dis_amt.toFixed(2));
            }else{
        var dis_amt= parseFloat($('#discount').val());
         var dis_per=isNaN(((dis_amt*100)/tot_amt))?0:((dis_amt*100)/tot_amt);
         $('#discount_percent').val(dis_per.toFixed(2));
            }


        var tax_per= parseFloat($('#tax_percentage').val());
        var tax_amt = parseFloat((tot_amt-dis_amt)*tax_per/100);
        var net_amt=isNaN(tax_amt+(tot_amt-dis_amt))?"" :(tax_amt+(tot_amt-dis_amt)).toFixed(2);
        $('#net_amount').val(net_amt);
        $('#payment_amount').val(net_amt);
        }

    function deleterecord(id) { 
            if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/bloodbank/deleteComponent/' + id,
                    type: "POST",
                    data: {id: ''},
                    dataType: 'json',
                    success: function (data) {
                        successMsg('<?php echo $this->lang->line('delete_message'); ?>')
                        window.location.reload(true);
                    }
                });
            }
    }

</script> 

<script type="text/javascript">
    (function($){
        'use strict';
        $(document).ready(function(){
            initDatatable('ajaxlist','admin/bloodbank/getcomponets');
        });
    }(jQuery))
</script>
