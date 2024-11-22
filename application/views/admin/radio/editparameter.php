<?php 
	if(!empty($detail)){
		foreach ($detail as $key => $value) {
?>
			<input type="hidden" name="previous_parameter_id[]" value="<?php echo $value["id"] ?>">
<?php } } ?>  
 
    <table class="table table-striped table-bordered table-hover" id="edittableID">
        <thead>
            <tr style="font-size: 13px">
                <th><?php echo $this->lang->line('test') . " " .$this->lang->line('parameter') . " " . $this->lang->line('name'); ?><small class="req" style="color:red;"> *</small></th>
                <th><?php echo $this->lang->line('refference') . " " . $this->lang->line('range'); ?> </th>
                <th><?php echo $this->lang->line('unit') ; ?><small class="req" style="color:red;"> *</small></th>
            </tr>
        </thead>
        <?php 
           $i= 0 ;
           if(!empty($detail)){
			foreach ($detail as $key => $value) {
        ?> 
                <input type="hidden" name="previous_radiology_parameter_id[]" value="<?php echo $value['id'] ?>">
                <input type="hidden" name="previous_radiology_id" value="<?php echo $value['radiology_id'] ?>">
                <input type="hidden" name="pre_parameter_id[]" value="<?php echo $value['parameter_id'] ?>">

                <tr id="row<?php echo $i ?>">
                    <td width="40%">
                        <select class="form-control select2" style="width:100%" onchange="getparametereditdetails(this.value, <?php echo $i ?>)" id="parameter_edit_name<?php echo $i ?>" name='parameter_name[]'>
                            <option value="<?php echo set_value('radiology_parameter_id'); ?>"><?php echo $this->lang->line('select') ?></option>
                            <?php foreach ($parametername as $dkey => $dvalue) {  ?>
							<option value="<?php echo $dvalue["id"]; ?>" <?php if ($value["parameter_id"] == $dvalue["id"]) echo "selected"; ?> ><?php echo $dvalue["parameter_name"] ?></option> 
							<?php } ?> 
                                               
                        </select>
                        <input type="hidden" name="new_parameter_id[]" value="<?php echo $value["id"] ?>"  >
                        <span class="text-danger"><?php echo form_error('parameter_name[]'); ?></span>
                    </td>
                    <td width="30%">
                        <input type="text" readonly="" name="reference_range[]"  id="reference_edit_range<?php echo $i ?>" value="<?php echo $value['reference_range']; ?>" class="form-control">
                    </td>
                    <td width="30%">
                        <input type="text" readonly="" value="<?php echo $value['unit_name']; ?>" name="radio_unit[]"  id="radio_edit_unit<?php echo $i ?>" class="form-control">
                    </td>
					<?php if ($i != 0) { ?>
						<td><button type='button' onclick="delete_row('<?php echo $i ?>')" class='closebtn'><i class='fa fa-remove'></i></button></td>
					<?php } else { ?>
						<td><button type="button" onclick="editMore()" style="color:#2196f3" class="closebtn"><i class="fa fa-plus"></i></button></td>
					<?php } ?>
				</tr>
			<?php
            $i++;
            }}else{ ?>
				<tr>
					<td></td>
					<td></td>
					<td><button type="button" onclick="editMore()" style="color:#2196f3" class="closebtn"><i class="fa fa-plus"></i></button></td>
				</tr>
			<?php  }  ?> 
		</table>										
										
<script type="text/javascript">
    $(function () {
        $('#easySelectable').easySelectable();
    })
	
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()
    });
</script>

<script type="text/javascript">
    
    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }    

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
    function editMore() {
        var table = document.getElementById("edittableID");
        var table_len = (table.rows.length);
        var id = parseInt(table_len - 1);
      
        var div = "<td><select class='form-control' name='parameter_name[]' onchange='getparametereditdetails(this.value," + id + ")'><option value='<?php echo set_value('parameter_name'); ?>'><?php echo $this->lang->line('select') ?></option><?php foreach ($parametername as $dkey => $dvalue) { ?><option value='<?php echo $dvalue["id"]; ?>'><?php echo $dvalue["parameter_name"] ?></option><?php } ?></select></td><td><input type='text' reference_range[]' readonly id='reference_edit_range" + id + "' class='form-control'></td><td><input type='text' name='radio_unit[]' readonly id='radio_edit_unit" + id + "' class='form-control'></td>";
        var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'>" + div + "<td><button type='button' onclick='delete_row(" + id + ")' class='closebtn'><i class='fa fa-remove'></i></button></td></tr>";
        $('.select2').select2();
    }

    function delete_row(id) {
        var table = document.getElementById("edittableID");
        var rowCount = table.rows.length;
        $("#row" + id).remove();
    }
    
    function getparametereditdetails(parameter_id,id) {
        $.ajax({
            type: "POST",
            url: base_url + "admin/radio/getparameterdetails",
            data: {'id': parameter_id },
            dataType: 'json',
            success: function (res) {
                if (res != null) {
                    $('#reference_edit_range' + id).val(res.reference_range);
                    $('#radio_edit_unit' + id).val(res.unit_name);
                }
            }
        });
    }
 
</script>