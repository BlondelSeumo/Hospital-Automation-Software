<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('medicine_purchase_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('medicine_purchase', 'can_add')) { ?>
                                <a data-toggle="modal" onclick="holdModal('myModal')" class="btn btn-primary btn-sm addpurchase"><i class="fa fa-plus"></i> <?php echo $this->lang->line('purchase_medicine'); ?></a> 
                            <?php } ?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('medicine_purchase_list'); ?></div>
                        <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('medicine_purchase_list'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('purchase_no'); ?></th>
                                    <th><?php echo $this->lang->line('purchase_date'); ?></th>
                                    <th><?php echo $this->lang->line('bill_no');?></th>
                                    <th><?php echo $this->lang->line('supplier_name'); ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('total')." (".$currency_symbol.")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('tax')." (".$currency_symbol.")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('discount')." (".$currency_symbol.")"; ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('net_amount')." (".$currency_symbol.")"; ?></th>                                  
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
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
        <form id="bill" accept-charset="utf-8" method="post"> 
            <div class="modal-header modal-media-header">
                <button type="button" class="close pupclose" data-dismiss="modal">&times;</button>
                 <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-9">
                     <div class="p-2 select2-full-width">
                        <select name="supplier_id" class="form-control select2 supplier_select2" id="" name=''>
                                <option value=""><?php echo $this->lang->line('select_supplier'); ?></option>
                                <?php foreach ($supplierCategory as $dkey => $dvalue) {
                                    ?>
                                    <option value="<?php echo $dvalue["id"]; ?>" <?php
                                    if ((isset($supplier_select)) && ($supplier_select == $dvalue["id"])) {
                                        echo "selected";
                                    }
                                    ?>><?php echo $dvalue["supplier"]; ?></option>   
                            <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('refference'); ?></span>
                         </div>   
                       </div>
                 </div>
            </div><!--./modal-header-->   
            <div class="pup-scroll-area">
                <div class="tabinsetbottom pt5">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-5">
                                <label><?php echo $this->lang->line('bill_no'); ?> <input name="invoice_no" id="invoice_no" type="text" value="" class="active-border"/>
                                <span class="text-danger"><?php echo form_error('invoice_no'); ?></span></label>
                            </div>
                            <div class="col-lg-7 col-md-7 col-sm-7 text-right text-md-left">
                                <label><?php echo $this->lang->line('purchase_date'); ?> 
                                <input name="date" id="txtDate10" type="text"  class="transparentbg-border datetime"/>
                                <span class="text-danger"><?php echo form_error('date'); ?></span></label>
                            </div>
                        </div>
                    </div>
                </div>            
                <div class="modal-body pb0 ptt10">                    
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">
                                    <div class="col-sm-2" hidden>
                                        <div class="form-group">
                                            <label><th><?php echo $this->lang->line('supplier_person'); ?></th></label>
                                            <small class="req"> *</small> 
                                            <input name="supplier_name" readonly hidden id="supplier_name" type="text" class="form-control"/>
                                            <span class="text-danger"><?php echo form_error('supplier_name'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt10">
                                        <div class="table-responsive">
                                            <table class="table tableover table-striped table-bordered table-hover mb10 tablefull12 tblProducts" id="tableID">
                                                <thead>
                                                <tr class="white-space-nowrap">
                                                    <th width="10%"><?php echo $this->lang->line('medicine_category'); ?><small class="req"> *</small></th>
                                                    <th width="10%"><?php echo $this->lang->line('medicine_name'); ?><small class="req" style="color:red;"> *</small></th>
                                                    <th><?php echo $this->lang->line('batch_no'); ?><small class="req" > *</small></th>
                                                    <th><?php echo $this->lang->line('expiry_date'); ?><small class="req"> *</small></th>
                                                    <th><?php echo $this->lang->line('mrp') . " " . ' (' . $currency_symbol . ')'; ?><small class="req"> *</small></th>
                                                    <th><?php echo $this->lang->line('batch_amount') . " " . ' (' . $currency_symbol . ')';  ?></th>
                                                    <th><?php echo $this->lang->line('sale_price') . " " . ' (' . $currency_symbol . ')'; ?><small class="req"> *</small></th>
                                                    <th><?php echo $this->lang->line('packing_qty'); ?></th>
                                                    <th class="text-right;"><?php echo $this->lang->line('quantity'); ?><small class="req"> *</small> </th>
                                                    <th class="text-right"><?php echo $this->lang->line('purchase_price') . " " . ' (' . $currency_symbol . ')'; ?><small class="req"> *</small></th>
                                                    <th class="text-right"><?php echo $this->lang->line('tax'); ?><small class="req"> *</small></th>
                                                    <th class="text-right" ><?php echo $this->lang->line('amount') . " (" . $currency_symbol . ")"; ?><small class="req"> *</small></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr id="row0" class="white-space-nowrap">
                                                    <td>      
                                                        <select class="form-control" name='medicine_category_id[]'  onchange="getmedicine_name(this.value, '0')">
                                                            <option value="<?php echo set_value('medicine_category_id'); ?>"><?php echo $this->lang->line('select'); ?></option>
                                                            <?php foreach ($medicineCategory as $dkey => $dvalue) {  ?>
                                                            <option value="<?php echo $dvalue["id"]; ?>"><?php echo $dvalue["medicine_category"] ?></option>   
															<?php } ?>
                                                        </select>
                                                        <span class="text-danger"><?php echo form_error('medicine_category_id[]'); ?></span>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width:100%" onchange="getmedicinedetails(this.value, 0)" id="medicine_name0" name='medicine_name[]'>
                                                            <option value=""><?php echo $this->lang->line('select'); ?>
                                                            </option>
                                                        </select>
                                                        <span class="text-danger"><?php echo form_error('medicine_name[]'); ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="batch_no[]"  id="batchno" class="form-control">
                                                        <span class="text-danger"><?php echo form_error('batch_no[]'); ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="expiry_date[]"  id="expiry" class="form-control">
                                                        <span class="text-danger"><?php echo form_error('expiry_date[]'); ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="mrp[]"  id="mrp" class="form-control">
                                                        <span class="text-danger"><?php echo form_error('mrp[]'); ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="batch_amount[]" id="batch_amount" class="form-control">
                                                        <span class="text-danger"></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="sale_rate[]"  id="sale_price" class="form-control">
                                                        <span class="text-danger"><?php echo form_error('sale_rate[]'); ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="packing_qty[]"  id="packing_qty" class="form-control">
                                                        <span class="text-danger"><?php echo form_error('packing_qty[]'); ?></span>
                                                    </td> 
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="text" name="quantity[]" onchange="multiply(0)"  id="quantity0" class="form-control text-right quantity">
                                                        </div> 
                                                    </td>
                                                    <td class="text-right">
                                                        <input type="text" name="purchase_price[]" onchange="multiply(0)" id="purchase_price0" placeholder="" class="form-control text-right purchase_price">
                                                        <span class="text-danger"><?php echo form_error('purchase_price[]'); ?></span>
                                                    </td>
                                                    <td class="text-right">  
                                                                <div class="input-group">
                                                                <input type="text" change="multiply(0)" class="form-control right-border-none purchase_tax"  name="purchase_tax[]" id="purchase_tax0"  autocomplete="off">
                                                                <span class="input-group-addon "> %</span>
                                                                </div>                                                      
                                                        <span class="text-danger"><?php echo form_error('purchase_price[]'); ?></span>
                                                    </td>
                                                    <td class="text-right" width="10%">
                                                        <input type="text" name="amount[]" id="amount0" placeholder="" class="form-control text-right amount" readonly>
                                                        <span class="text-danger"><?php echo form_error('net_amount[]'); ?></span>
                                                    </td>
                                                    <td><button type="button" onclick="addMore()" style="color: #2196f3" class="closebtn"><i class="fa fa-plus"></i></button></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>  
                                        <div class="divider"></div> 
                                        <div class="row">  
                                            <div class="col-sm-5">
                                                <div class="form-group">  
                                                    <?php echo $this->lang->line('note'); ?>
                                                    <textarea name="note" rows="3" id="note" class="form-control"></textarea>
                                                </div> 
                                                <div class="form-group">
                                                    <label><?php echo $this->lang->line('attach_document') ?></label>
                                                    <input type="file" name="file" id="file" class="form-control filestyle" />
                                                    <span class="text-danger"><?php echo form_error('file'); ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-7">
                                                <table class="printablea4">
                                                    <tr>
                                                        <th width="40%"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")"; ?></th>
                                                        <td width="60%" colspan="2" class="text-right ipdbilltable"><input type="text" placeholder="Total" value="0" name="total" id="total" style="width: 50%; float: right" class="form-control"/></td>
                                                    </tr>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('discount') . " (" . $currency_symbol . ")"; ?></th>
                                                        <td class="text-right ipdbilltable"><h4 style="float: right;font-size: 12px; padding-left: 5px;"> %</h4><input type="text" placeholder="<?php echo $this->lang->line('discount'); ?>" value="" name="discount_percent" id="discount_percent" style="width: 70%; float: right;font-size: 12px;" class="form-control discount_percent"/></td>
                                                        <td class="text-right ipdbilltable"><input type="text" placeholder="Discount" value="0" name="discount" id="discount" style="width: 85%; float: right" class="form-control"/></td>
                                                    </tr>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")"; ?></th>
                                                        <td class="text-right ipdbilltable">
                                                            <h4 style="float: right;font-size: 12px;padding-left: 5px;"></h4>
                                                        </td>
                                                        <td class="text-right ipdbilltable">
                                                            <input type="text" placeholder="Tax amount" name="tax" value="0" id="tax" style="width: 85%; float: right" class="form-control"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")"; ?></th>
                                                        <td colspan="2" class="text-right ipdbilltable">
                                                            <input type="text" placeholder="Net Amount" value="0" name="net_amount" id="net_amount" style="width: 50%; float: right" class="form-control"/></td>
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
											<?php }   ?>
                                        </select>    
                                        <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('payment_amount') . " (" . $currency_symbol . ")"; ?></label>
                                        <input type="text" id="payment_amount" class="form-control payment_amount text-right" readonly="readonly">  
                                         <span class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('payment_note'); ?></label> 
                                     <textarea name="payment_note" class="form-control"></textarea>  
                                        <span class="text-danger"><?php echo form_error('payment_note'); ?></span>
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
                            </div>
                                            </div>
                                        </div><!--./row-->  
                                    </div><!--./col-md-12-->
                                </div><!--./row-->  
                            </div><!--./col-md-12-->    
                        </div><!--./row-->
                </div><!--./modal-body-->
            </div>
                <div class="box-footer sticky-footer">
                    <div class="pull-right">                       
                        <button type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" id="billsave" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div><!--./box-footer-->
            </form>
        </div>
    </div> 
</div>

<div class="modal fade" id="viewModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-placement="bottom" data-toggle="tooltip" title="" data-dismiss="modal" data-original-title="Close" autocomplete="off">&times;</button>
                <div class="modalicon"> 
                    <div id='edit_deletebill'>
                        <a href="#" data-toggle="tooltip" data-placement="bottom" data-target="#edit_prescription" data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="modal-title"><?php echo $this->lang->line('purchase_details'); ?></h4> 
            </div>
            <form id="savesalerate" action="" method="post" enctype="multipart/form-data">
            <div class="modal-body pt0 pb0">
                <div id="reportdata"></div>
            </div>
             <div class="box-footer sticky-footer">
                    <div class="pull-right">  
                                <?php echo $this->customlib->getCSRF(); ?>
                        <button type="submit" id="updatesalerate" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div><!--./box-footer-->
           </div>
        </form>  
    </div>    
</div>

<div class="modal fade" id="edit_bill" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog pup100" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                 <button type="button" data-toggle="tooltip" title="<?php echo $this->lang->line('close'); ?>" class="close" data-dismiss="modal">&times;</button>
                <div class="d-flex align-items-center align-content-center align-items-start flex-column label-0 mobile-p-2">
                    <div class="p-2 d-inline-flex mobile-p-2 form-group mb0">
                        <select style="width:200px;" onchange="get_SupplierDetails(this.value)" class="form-control select2" id="editsupplier" name='supplier'>
                            <option value=""><?php echo $this->lang->line('select_supplier'); ?></option>
                            <?php foreach ($supplierCategory as $dkey => $dvalue) { ?>
                                <option value="<?php echo $dvalue["id"]; ?>" <?php
                                        if ((isset($supplier_select)) && ($supplier_select == $dvalue["id"])) {
                                            echo "selected";
                                        }
                                        ?>><?php echo $dvalue["supplier_category"]; ?></option>   
                         <?php } ?>
                        </select>
                        <span class="text-danger"><?php echo form_error('refference'); ?></span>
                    </div>
                    <div class="p-2 mobile-p-2">
                        <label class="text-white"><?php echo $this->lang->line('purchase_no'); ?></label>
                    </div>
                    <div class="p-2 mobile-p-2">
                        <input name="purchase_no" id="purchaseno" readonly type="text" class="form-control" value="" />
                        <span class="text-danger"><?php echo form_error('purchase_no'); ?></span>
                    </div>
                    <div class="p-2 mobile-p-2">
                        <label class="text-white"><?php echo $this->lang->line('invoice_number'); ?></label>
                    </div>
                    <div class="p-2 mobile-p-2">
                       <input name="invoice_no" id="invoicenoup" type="text" class="form-control" value="" />
                        <span class="text-danger"><?php echo form_error('invoice_no'); ?></span>
                    </div>  
                    <div class="p-2 mobile-p-2">
                        <label class="text-white"><?php echo $this->lang->line('purchase_date'); ?></label>
                    </div> 
                    <div class="p-2 mobile-p-2">
                        <input name="date" id="dateedit_supplier" type="text" value="" class="form-control datetime"/>
                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                    </div>  
                </div><!-- ./ d-flex -->
             </div>                 
            <div class="modal-body pt0 pb0" id="edit_bill_details"></div>    
        </div>
    </div> 
</div>

<script type="text/javascript">
    var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';
	
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
	
    $(function () {
        $('#easySelectable').easySelectable();
    })

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

<script type="text/javascript">
            function holdModal(modalId) {
                $('#' + modalId).modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            }
</script>

<script>
            function getmedicine_name(id, rowid) {
                var div_data = "";
                $("#medicine_name" + rowid).html("<option value='l'><?php echo $this->lang->line('loading') ?></option>");
                $('#medicine_name' + rowid).select2("val", 'l');
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/get_medicine_name',
                    type: "POST",
                    data: {medicine_category_id: id},
                    dataType: 'json',
                    success: function (res) {
                        $.each(res, function (i, obj)
                        {
                            var sel = "";
                            div_data += "<option value=" + obj.id + ">" + obj.medicine_name + "</option>";
                        });
                        $("#medicine_name" + rowid).html("<option value=''>Select</option>");
                        $('#medicine_name' + rowid).append(div_data);
                        $('#medicine_name' + rowid).select2("val", '');
                    }
                });
            }          

            function get_SupplierDetails(id) {
                $("#supplier_name").html("supplier_name");
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/supplierDetails',
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    success: function (res) {
                        console.log(res);
                        if (res) {
                            $('#supplier_name').val(res.supplier_person);
                            $('#supplierid').val(res.id);
                        } else {
                            $('#supplier_name').val('Null');

                        }
                    }
                });
            }

            $(document).ready(function (e) {

                $('#expiry').datepicker({
                    format: "M/yyyy",
                    viewMode: "months",
                    minViewMode: "months",
                    autoclose: true
                });
            });
            function addMore() {
				
                var table = document.getElementById("tableID");
                var table_len = (table.rows.length);
                var id = parseInt(table_len - 1);
                var div = "<td><select class='form-control' name='medicine_category_id[]' onchange='getmedicine_name(this.value," + id + ")'><option value='<?php echo set_value('medicine_category_id'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($medicineCategory as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["medicine_category"] ?></option><?php } ?></select></td><td><select class='form-control select2' style='width:100%' name='medicine_name[]' onchange='getmedicinedetails(this.value," + id + ")' id='medicine_name" + id + "' ><option value='<?php echo set_value('medicine_name'); ?>'><?php echo $this->lang->line('select') ?></option></select></td><td><input type='text' name='batch_no[]' id='batchno" + id + "' class='form-control batch_no'></td><td><input type='text' name='expiry_date[]' id='expiry" + id + "' class='form-control expiry_date'></td><td><input type='text' name='mrp[]' id='mrp" + id + "' class='form-control mrp'></td><td><input type='text' name='batch_amount[]' id='batch_amount" + id + "' class='form-control mrp'></td><td><input type='text' name='sale_rate[]' id='salerate" + id + "' class='form-control sale_rate'></td><td><input type='text' name='packing_qty[]' id='packingqty" + id + "' class='form-control packing_qty'></td><td><div class='input-group'><input type='text' name='quantity[]' onchange='multiply(" + id + ")' onfocus='getQuantity(" + id + ")' id='quantity" + id + "' class='form-control text-right quantity'></div></td><td><input type='text' onchange='multiply(" + id + ")' name='purchase_price[]' id='purchase_price" + id + "'  class='form-control text-right purchase_price'></td><td><div class=''><div class='input-group'><input type='text' change='multiply(" + id + ")' class='form-control right-border-none purchase_tax'  name='purchase_tax[]' id='purchase_tax" + id + "'  autocomplete='off'><span class='input-group-addon '> %</span></div></div></td><td><input type='text' name='amount[]' id='amount" + id + "'  class='form-control text-right amount' readonly></td>";
                var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'>" + div + "<td><button type='button' onclick='delete_row(" + id + ")' class='closebtn'><i class='fa fa-remove'></i></button></td></tr>";
                $('.select2').select2();

                var expiry_date = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY',]) ?>';
                $('.expiry_date').datepicker({
                    format: "M/yyyy",
                    viewMode: "months",
                    minViewMode: "months",
                    autoclose: true
                });
            }
         
            function delete_row(id) {                
                var message = "<?php echo $this->lang->line('are_you_sure'); ?>";
                if(confirm(message)){
                    var table = document.getElementById("tableID");
                    var rowCount = table.rows.length;
                    $("#row" + id).remove();
                }                
            }
          
            $(document).ready(function (e) {
                $("#bill").on('submit', (function (e) {
                    e.preventDefault();
                    var btn = $("#billsave");
                    btn.button('loading');
                    var table = document.getElementById("tableID");
                    var rowCount = table.rows.length;
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/pharmacy/addBillSupplier',
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
                            $("#billsave").button('reset');
                        },
                        error: function () {}
                    }); 
                }));
            });

            $(document).ready(function (e) {
                var expiry_date = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY',]) ?>';
                $('.expiry_date').datepicker({
                    format: "M/yyyy",
                    viewMode: "months",
                    minViewMode: "months",
                    autoclose: true,
                });
            });

            function viewDetail(id) {
                $.ajax({
                    url: '<?php echo base_url() ?>admin/pharmacy/getSupplierDetails/' + id,
                    type: "GET",
                    data: {},
                    success: function (data) {
                        $('#reportdata').html(data);                        
                        $('#edit_deletebill').html("<?php if($this->rbac->hasPrivilege('medicine_purchase', 'can_view')) { ?><a href='#' data-toggle='tooltip' data-placement='bottom' onclick='printData(" + id + ")' data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> <?php } ?><?php if($this->rbac->hasPrivilege('medicine_purchase', 'can_delete')) { ?><a onclick='delete_bill(" + id + ")'  href='#'  data-toggle='tooltip' data-placement='bottom' data-original-title='<?php echo $this->lang->line('delete'); ?>'><i class='fa fa-trash'></i></a><?php } ?>");                        
                        holdModal('viewModal');
                    },
                });
            }             

            $(document).on('input paste keyup','.purchase_price,.quantity,.purchase_tax,.discount_percent', function(e){ 
                update_amount($(e.target).closest('div.modal'));
            });

            let update_amount=(__this)=>{
    
             var grandTotal = 0; 
            var total_tax_amount = 0;
            var $tblrows = __this.find(".tblProducts tbody tr");  
            var discount_percent=__this.find('#discount_percent').val();
     
            $tblrows.each(function (index) {
                    var $tblrow = $(this);  
                    let quantity = parseFloat($tblrow.find("td input.quantity").val());
                   let purchase_price = parseFloat($tblrow.find("td input.purchase_price").val());
                   let purchase_tax = parseFloat($tblrow.find("td input.purchase_tax").val());
                   let row_amount=(isNaN(quantity*purchase_price)) ? 0 : quantity*purchase_price;
                   $tblrow.find("td input.amount").val(row_amount)
                    grandTotal+=row_amount;
					
					 var discount_amt = (purchase_price*discount_percent)/100;
					 
                    total_tax_amount += (((purchase_price-discount_amt)*quantity)*purchase_tax)/100; 

					 
            });
           
                __this.find('#total').val(grandTotal.toFixed(2));
                discount=(grandTotal * discount_percent / 100 );
                discount = (isNaN(discount)) ? 0 : discount;
                __this.find('#discount').val(discount.toFixed(2));		
				 
                var net_amount=((grandTotal-discount)+total_tax_amount);  
				
               __this.find('#tax').val(total_tax_amount.toFixed(2));
               __this.find('#net_amount').val(net_amount.toFixed(2));
               __this.find('#payment_amount').val(net_amount.toFixed(2));       

   }

   function addTotal() {
                var total = 0;
                var tax_amount=0;
                var sale_price = document.getElementsByName('amount[]');
                var tax = document.getElementsByName('purchase_tax[]');
                for (var i = 0; i < sale_price.length; i++) {
                    var inp = sale_price[i];
                    var tax_inp = tax[i];
                    if (inp.value == '') {
                        var inpvalue = 0;
                    } else {
                        var inpvalue = inp.value;
                    }

                    if (tax_inp.value == '') {
                        var tax_inpvalue = 0;
                    } else {
                        var tax_inpvalue = tax_inp.value;
                    }

                    tax_amount +=parseFloat((inpvalue) * tax_inpvalue / 100);
                    total += parseFloat(inpvalue);
                }

                var discount_percent = $("#discount_percent").val();
                var tax_percent = $("#tax_percent").val();
                if (discount_percent != '') {
                    var discount = (total * discount_percent) / 100;
                    $("#discount").val(discount.toFixed(2));
                } else {
                    var discount = $("#discount").val();
                }
                
                $("#tax").val(tax_amount.toFixed(2));
                $("#total").val(total.toFixed(2));
                var tax = $("#tax").val();
                var net_amount = parseFloat(total) + parseFloat(tax) - parseFloat(discount); 
                var cnet_amount = net_amount.toFixed(2)
                $("#net_amount").val(cnet_amount);
                $("#payment_amount").val(cnet_amount);               
                var editdate = $("#txtDate10").val();
                $("#date_result").val(editdate);
                var invoiceno = $("#invoice_no").val();
                $("#invoiceno").val(invoiceno);
                $("#billsave").show();
                $(".printsavebtn").show();
            }           

            function getExpire(id) {
                var batch_no = $("#batch_no" + id).val();
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/pharmacy/getExpiryDate",
                    data: {'batch_no': batch_no},
                    dataType: 'json',
                    success: function (res) {
                        if (res != null) {
                            $('#expiry_date' + id).val(res.expiry_date);
                            getQuantity(id);
                        }
                    }
                });
            }            

            function getmedicinedetails(id, rowid) {
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/pharmacy/getmedicinedetails",
                    data: {'pharmacy_id': id},
                    dataType: 'json',
                    success: function (res) {
                         if (res) {
                            $('#purchase_tax'+ rowid).val(res.vat);                           
                        } 
                    }
                });
            }

            function get_PatientDetails(id) {
                $("#patient_name").html("patient_name");
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/pharmacy/patientDetails',
                    type: "POST",
                    data: {id: id},
                    dataType: 'json',
                    success: function (res) {
                        console.log(res);
                        if (res) {
                            $('#patient_name').val(res.patient_name);
                            $('#pharma_patientid').val(res.id);
                        } else {
                            $('#patient_name').val('Null');

                        }
                    }
                });
            } 

 $("#myModal").on('hidden.bs.modal', function(){
    $('.cheque_div').css("display", "none");
      $(".filestyle").next(".dropify-clear").trigger("click");
    $("#bill").find('input:text, input:password, input:file, select, textarea').val('');
    $("#bill").find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
    $('#tableID tbody tr:not(:first)','#bill').remove();
    $("select[id^='medicine_name']").select2("val", "");
    $(".supplier_select2").select2("val", "");

  });

  $(document).on('change','.payment_mode',function(){ 
      var mode=$(this).val();
      if(mode == "Cheque"){
        $('.cheque_div').css("display", "block");
      }else{
        $('.cheque_div').css("display", "none");
      }
    });
</script>

<!-- //========datatable start===== -->
<script type="text/javascript">
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('ajaxlist','admin/pharmacy/getpharmacypurchaseDatatable',[],[],100,
            [
               {  "sWidth": "90px", "bSortable": false, "aTargets": [ -1,-2,-3,-4 ] ,'sClass': 'dt-body-right'},
               
            ]);
    });
} ( jQuery ) )
</script>
<!-- //========datatable end===== -->
<script type="text/javascript">
    function delete_bill(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/pharmacy/deleteSupplierBill/' + id,
                success: function (res) {
                    successMsg('<?php echo $this->lang->line('delete_message'); ?>');
                    window.location.reload(true);
                },
                error: function () {
                    alert("Fail")
                }
            });
        }
    }
	
    function printData(id) {        
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/pharmacy/getSupplierDetails/' + id,
            type: 'POST',
            data: {id: id, print: 'yes'},
            success: function (result) {                
                popup(result);
            }
        });
    }    
</script>
<script>
    $('.addpurchase').click(function () {        
        $('#txtDate10').datetimepicker({
            format: datetime_format,
        });
        $('#txtDate10').data("DateTimePicker").date(new Date());
        $('#txtDate10').on('dp.change', dateChanged);
    }); 
    
    function dateChanged(ev) {            
            var $tblrows = $('.tblProducts').find("tbody tr");
            $tblrows.each(function (index) {
            var $tblrow = $(this);  
            var _row_day = $tblrow.find(".days").val();
            if(_row_day !=""){
           
            //==============
            var report_day =  parseInt(_row_day, 10);
            var selected_date=$("#txtDate10").data('DateTimePicker').date().toDate() ;
            var newdate = new Date(selected_date);
            newdate.setDate(newdate.getDate() + report_day);
            $tblrow.find(".report_date")
            .datepicker({
               format: datetime_format,
               autoclose: true,
               todayHighlight: true
               }).datepicker("update", newdate); 
            //================            
                
            }        
            });
        }
		
    $("#savesalerate").on('submit', (function (e) {
      e.preventDefault();
      $.ajax({
          url: "<?php echo site_url("admin/pharmacy/update_sale_rate") ?>",
          type: "POST",
          data: new FormData(this),
          dataType: 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function () {
          },
          success: function (res)
          {    
             if (res.status == "fail") {                
                  var message = "";
                  $.each(res.error, function (index, value) {
                      message += value;
                  });
                  errorMsg(message);
  
              } else {
                  successMsg(res.message);
              }
          },
          error: function (xhr) { // if error occured
              alert("Error occured.please try again");
          },
          complete: function () {
           
          }
      });
 }));                                            
</script>