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
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('ipd_discharged_patient'); ?></h3>
                        <div class="box-tools pull-right"></div>    
                    </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="download_label"> <?php echo $this->lang->line('ipd_discharged_patient'); ?></div>
                            <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('ipd_discharged_patient'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name') ?></th>
                                        <th><?php echo $this->lang->line('patient_id'); ?></th>
                                        <th><?php echo $this->lang->line('case_id'); ?></th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th><?php echo $this->lang->line('consultant_doctor') ?></th>
                                        <th><?php echo $this->lang->line('admission_date'); ?></th>
                                        <th><?php echo $this->lang->line('discharged_date'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('tax') . " (" . $currency_symbol . ")" ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('net_amount') . " (" . $currency_symbol . ")" ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('total') . " (" . $currency_symbol . ")" ?></th>
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
<script type="text/javascript">
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
( function ( $ ) {
    'use strict';
    $(document).ready(function () {
  initDatatable('ajaxlist', 'admin/patient/getdischargeddatatable/',{},[],100,
    [{"bSortable": false,"sWidth": "60px", "aTargets": [ -1,-2,-3 ] ,'sClass': 'dt-body-right'}]);
    });
} ( jQuery ) )
</script>