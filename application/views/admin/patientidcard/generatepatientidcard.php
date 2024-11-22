<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?> 
<div class="content-wrapper">   
    <!-- Main content -->
    <section class="content">
        <?php if ($this->session->flashdata('msg')) { ?>
            <?php echo $this->session->flashdata('msg');
            $this->session->unset_userdata('msg'); ?>
        <?php } ?>  
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('select_criteria'); ?></h3>
                        <?php if (($this->rbac->hasPrivilege('patient_id_card', 'can_view'))) { ?>
                        <a class="btn btn-info btn-sm  pull-right" type="button" href="<?php echo base_url()?>admin/patientidcard/" title="<?php echo $this->lang->line('id_card_template'); ?>"><i class="fa fa-newspaper-o ftlayer"> </i> <?php echo $this->lang->line('id_card_template'); ?></a>
                    <?php } ?>
                    </div>
                    <div class="box-body">
                      <div class="row">
                            <form id="form1" action="" method="post">
                                <?php echo $this->customlib->getCSRF(); ?>
                               <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line("patient"); ?></label>
                                        <select id="patient_id" name="patient_id" class="form-control select2" >
                                            <option value="all" selected><?php echo $this->lang->line('all'); ?></option>
                                            <?php
                                            if (isset($patients)) {
                                                foreach ($patients as $dkey => $dvalue) {
                                                    ?>
                                                    <option value="<?php echo $dvalue['id'] ?>" <?php if (set_value('patient_id') == $dvalue['id']) echo "selected=selected" ?>><?php echo $dvalue['patient_name']."(".$dvalue['id'].")" ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                         <span class="text-danger" id="error_patient_id"><?php echo form_error('search_type'); ?></span>
                                    </div>   
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line("id_card_template"); ?></label><small class="req"> *</small>
                                        <select  id="patient_id_card" name="patient_id_card" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            if (isset($idcardlist)) {
                                                foreach ($idcardlist as $list) {
                                                    ?>
                                                    <option value="<?php echo $list->id ?>" <?php if (set_value('patient_id_card') == $list->id) echo "selected=selected" ?>><?php echo $list->title ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                         <span class="text-danger" id="error_patient_id_card"><?php echo form_error('search_type'); ?></span>
                                    </div>   
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div> 
                          
                    </div>

                  
                        <form method="post" action="<?php echo base_url('admin/Generatepatientidcard/generatemultiple') ?>">
                            <div id="duefee">
                            <input type="hidden" name="patient" id="patientvalue" value="">
                            <input type="hidden" name="idcard" id="idcard" value="">
                                <div class="box-header ptbnull">
                                    <h3 class="box-title titlefix"> <?php echo $this->lang->line('patient_list'); ?></h3>
                                    <button class="btn btn-info btn-sm printSelected pull-right" type="button" name="generate" title="<?php echo $this->lang->line('generate_certificate'); ?>"><?php echo $this->lang->line('generate'); ?></button>
                                </div>
                                <div class="box-body table-responsive">
                                    <!-- <div class="download_label"><?php echo $title; ?></div> -->
                                    <div class="tab-pane active table-responsive no-padding" id="tab_1">
                                        <table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%">
                                            <thead>
                                                <tr> 
                                                    <th><input type="checkbox" id="select_all" /></th>
                                                    <th><?php echo $this->lang->line('patient_name'); ?></th>
                                                    <th><?php echo $this->lang->line('age'); ?></th>
                                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                                    <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                                    <th class="text text-right"><?php echo $this->lang->line('address'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            
                                            </tbody>
                                        </table>

                                    </div>                                                                           
                                </div> 

                            </div>
                        </form>
                </div>  
            </div>  
        </div> 
    </section>
</div>
<div class="response"> 
</div>

<script type="text/javascript">
     $(document).ready(function (e) {
      
        emptyDatatable('ajaxlist', 'data');
    });

     $(function () {
        $('.select2').select2()
    })
    $(document).ready(function () {
        $('#select_all').on('click', function () {
            if (this.checked) {
                $('.checkbox').each(function () {
                    this.checked = true;
                });
            } else {
                $('.checkbox').each(function () {
                    this.checked = false;
                });
            }
        });

        $('.checkbox').on('click', function () {
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.printSelected', function () {
            var array_to_print = [];
            var patientId = $("#patient_id").val();
            var idCard = $("#id_card_id").val();
            $.each($("input[name='check']:checked"), function () {
                var patientId = $(this).data('patient_id');
                item = {}
                item ["patient_id"] = patientId;
                array_to_print.push(item);
            });
            if (array_to_print.length == 0) {
                alert("<?php echo $this->lang->line('no_record_selected'); ?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url("admin/Generatepatientidcard/generatemultiple") ?>',
                    type: 'post',
                    dataType: "html",
                    data: {'data': JSON.stringify(array_to_print), 'patient_id': patientId, 'patient_id_card':idCard  },
                    success: function (response) {

                        popup(response);
                    }
                });
            }
        });
    });
</script>
<script type="text/javascript">

    var base_url = '<?php echo base_url() ?>'; 
 
</script>
<!-- //========datatable start===== -->
<script type="text/javascript">

        $(document).ready(function () {
       $('#form1').on('submit', (function (e) {
        e.preventDefault();
        var search= 'search_filter';
        var formData = new FormData(this);
        formData.append('search', 'search_filter');
        $.ajax({
            url: base_url+'admin/generatepatientidcard/checkpatientidcardvalidation',
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
       $('[id^=error_]').html("");
             },
            success: function (data) {
                if (data.status == "fail") {
                   $.each(data.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {

             initDatatable('ajaxlist','admin/generatepatientidcard/getpatientlistdatatable',data.param,[],100,[{"bSortable": false, "aTargets": [0] }]);
                 
                }
            },
           error: function(xhr) { // if error occured
     
            },
            complete: function() {
             
            }
        });
        }
       ));
   });
</script>
<!-- //========datatable end===== -->