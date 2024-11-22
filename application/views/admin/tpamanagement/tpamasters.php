<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-3">                
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <?php 
                        $file = "no_image.png";
                        ?>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item listnoback">
                                <b><?php echo $this->lang->line('tpa_name'); ?></b> <a class="pull-right text-aqua"><?php echo $result['organisation_name']; ?></a>
                            </li>
                            <li class="list-group-item listnoback">
                                <b><?php echo $this->lang->line('code'); ?></b> <a class="pull-right text-aqua"><?php echo $result['code']; ?></a>
                            </li>
                            <li class="list-group-item listnoback">
                                <b><?php echo $this->lang->line('contact_no'); ?></b> <a class="pull-right text-aqua"><?php echo $result['contact_no']; ?></a>
                            </li>
                            <li class="list-group-item listnoback">
                                <b><?php echo $this->lang->line('address'); ?></b> <a class="pull-right text-aqua"><?php echo $result['address']; ?></a>
                            </li>
                            <li class="list-group-item listnoback">
                                <b><?php echo $this->lang->line('contact_person_name'); ?></b> <a class="pull-right text-aqua"><?php echo $result['contact_person_name']; ?></a>
                            </li>
                            <li class="list-group-item listnoback">
                                <b><?php echo $this->lang->line('contact_person_phone'); ?></b> <a class="pull-right text-aqua"><?php echo $result['contact_person_phone']; ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                       <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('tpa_details'); ?></h3>
                    </div> 
                   
                        <div class="box-body">
                             <div class="row">
                                 <div class="col-md-12">
                                      <form id="form1" action="" method="post" class="form-inline">
                            <?php echo $this->customlib->getCSRF(); ?>
                       
                            <div class="form-group">
                                <div class=""> 
                                    <label><?php echo $this->lang->line('charge_type'); ?> : </label>
                                        <select class=" form-control select2" name="charge_type" >
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                             <?php foreach ($charge_type as $ckey => $value) {
                                                ?> 
                                                <option value="<?php echo $value['id'];  ?>" ><?php echo $value['charge_type'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" value="<?php echo $result['id'];?>" name="charge_type_master_id" />
                                    <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                    
                                </div>    
                              </div>
                            <div class="form-group">
                                <div class=""> 
                                   <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>     
                            </div>       
                            </form>
                                 </div>
                             </div>
                        </div>
                    
                    <div class="box border0 clear">
                        <div class="box-header ptbnull"></div>
                        <div class="box-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('tpa_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('tpa_details'); ?>" cellspacing="0" width="100%">
                                <thead>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th> 
                                        <th><?php echo $this->lang->line('charge_category'); ?></th>
                                        <th><?php echo $this->lang->line('charge_name'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?></th>
                                        <th class="text-right"><?php echo $this->lang->line('tpa_charge') . " (" . $currency_symbol . ")"; ?></th>
                                </thead>    
                                <tbody>
                                         
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> 
        </div>  
            </div>
        </div>
    </section>
</div>
<div class="modal fade edit_org" tabindex="-1" role="dialog" aria-labelledby="follow_up">
    <div class="modal-dialog modal-mid modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close"  data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_tpa_charge'); ?></h4>
            </div>
            <form id="edit_org_form" method="POST" action="<?php echo base_url(); ?>admin/tpa/edit_org">
               <div class="modal-body pt0 pb0" >                
                           
                    <div class="table-responsive ptt10">
                        <table class="table table-striped table-bordered">                     
                            <thead>
                            <th><?php echo $this->lang->line('charge_type'); ?></th>
                            <th><?php echo $this->lang->line('charge_category'); ?></th>
                            <th><?php echo $this->lang->line('charge_name'); ?></th>
                            <th><?php echo $this->lang->line('description'); ?></th>
                            <th class="text-right"><?php echo $this->lang->line('standard_charge') . " (" . $currency_symbol . ")"; ?></th>
                            <th class="text-right white-space-nowrap" width="10"><?php echo $this->lang->line('tpa_charge') . " (" . $currency_symbol . ")"; ?><small class="req"> *</small></th>       
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="charge_type"></td>
                                    <td id="charge_category"></td>
                                    <td id="charge_name"></td>
                                    <td id="description"></td>
                                    <td class="text-right" id="standard_charge_data"></td>
                                    <td class="text-right" width="100">                                                
                                        <input type="text" name="org_charge" id="org_charge_data" class="form-control text-right">
                                        <input type="hidden" name="org_charge_id" id="org_charge_id_data" class="form-control">                                       
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>   
                </div>               
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="submit" id="edit_org_btn" data-loading-text="<?php echo $this->lang->line('processing') ?>"  class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            
            </div> 
         </form>
       
    </div>
</div>

<script type="text/javascript">
 $(function () {
    $('.select2').select2()
  });

    function delete_orgById(id){
		if(confirm('<?php echo $this->lang->line('are_you_sure'); ?>')){
        var delete_url='admin/tpa/delete/' + id;      
        $.ajax({
            url: base_url +delete_url,
            type: 'POST',
            dataType: "json",
            success: function (msg) {
                successMsg(msg.msg);
                table.ajax.reload();
            }
        });
		}
    }
    
    function get_org_charge(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/tpa/get_org_charge/' + id,
            dataType: 'json',
            success: function (res) {
                $('.edit_org').attr("id", 'edit_org_data');
                $('#edit_org_data').modal({backdrop:"static"});
                $('#org_charge_id_data').val(res.org_charge_id);
                $('#description').html('<label>' + res.description + '</label>');
                $('#charge_type').html('<label>' + res.charge_type + '</label>');
                $('#charge_name').html('<label>' + res.name + '</label>');
                $('#charge_category').html('<label>' + res.charge_category + '</label>');
                $('#standard_charge_data').html('<label>' + res.standard_charge + '</label>');
                $('#org_charge_data').val(res.org_charge);
            }
        });
    }

    $(document).ready(function (e) {
        $('#edit_org_form').on('submit', (function (e) {
            $("#edit_org_btn").button('loading');
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
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
                        table.ajax.reload();
                         $('#edit_org_data').modal('hide');
                    }
                    $("#edit_org_btn").button('reset');
                },
                error: function () {}
            });
        }));
    });

    $(document).ready(function (e) {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
        $('#timeline_date').datepicker({
            format: date_format,
            autoclose: true
        });
    });   

  ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        $.ajax({
            url: '<?php echo base_url(); ?>admin/tpa/checkvalidation',
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == "fail") {
                   $.each(data.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {

                initDatatable('allajaxlist', 'admin/tpa/tpadetails/',data.param,[],100,[
                       {  "sWidth": "120px", "aTargets": [ -1,-2 ] ,'sClass': 'dt-body-right'},
                       
                    ]);
                }
            }
        });
        }
       ));
   });
} ( jQuery ) );  
</script>