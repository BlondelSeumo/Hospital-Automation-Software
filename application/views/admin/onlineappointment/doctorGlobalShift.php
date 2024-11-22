<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <?php
                    $this->load->view('admin/onlineappointment/appointmentSidebar');
                ?>
            </div>
            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('doctor_shift'); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('doctor_shift'); ?></div>
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line("doctor_name"); ?></th>
                                        <?php foreach ($global_shift as $gkey => $gvalue) { ?>
                                            <th class="rtl-text-right"><?php echo $gvalue['name']; ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($shift)){ 
                                        foreach ($shift as $shift_key => $shift_value) {
                                    ?>
                                    <tr>
                                        <td class="mailbox-name">
                                            <a href="#" data-toggle="popover" class="detail_popover"><?php echo $shift_value['first_name']." ".$shift_value["surname"]; ?> (<?php echo $shift_value["employee_id"]; ?>)</a>
                                        </td>
                                        <?php foreach ($global_shift as $gkey => $gvalue) { ?>
                                            <td class="rtl-text-right">
                                               <?php  $doctor_shift = array_column($shift_value["doctor_shift"], "id"); ?>
                                               <input type="checkbox" 
                                               <?php if ($this->rbac->hasPrivilege('online_appointment_doctor_shift', 'can_edit')) { ?>
                                                    onclick="changeShift(<?php echo $shift_value['id']; ?>,<?php echo $gvalue['id']; ?>,this)" 
                                               <?php }else{
                                                   echo " disabled";
                                               } ?>
                                               id="global_shift_<?php echo $gvalue['id']; ?>" name="global_shift[]" value="<?php echo $gvalue['id']; ?>" data-id = <?= $shift_value['id'].$gvalue['id']; ?>
                                               <?php if(in_array($gvalue['id'], $doctor_shift)){echo "checked=checked";} ?>
                                               />
                                                <span class="hide" id="checkbox_print_<?= $shift_value['id'].$gvalue['id']; ?>">
                                                    <?php 
                                                        if(in_array($gvalue['id'], $doctor_shift)){
                                                            echo $this->lang->line("yes");
                                                        }else{
                                                            echo $this->lang->line("no");
                                                        }
                                                    ?>
                                                </span>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function changeShift(doctor_id,shift_id,checkbox){
        console.log("checkbox_print_"+checkbox.dataset.id);
        if(checkbox.checked){
            status = 1;
            document.querySelector("#checkbox_print_"+checkbox.dataset.id).innerHTML = "<?= $this->lang->line("yes"); ?>";
        }else{
            status = 0;
            document.querySelector("#checkbox_print_"+checkbox.dataset.id).innerHTML = "<?= $this->lang->line("no"); ?>";
        }
			$.ajax({
                url: '<?php echo base_url(); ?>admin/onlineappointment/editDoctorGlobalShfit',
                type: "POST",
                data: {doctor_id:doctor_id, shift_id :shift_id, status:status},
                dataType: 'json',
                success: function (data) {
                    if(data.status == "success"){
                        successMsg(data.message);
                    }
                },
                error: function () {
                    alert("Fail")
                }
            });
    }
</script>
<script >
    $(window).load(function() {
        var table = $('.example').DataTable();
        $('.example tbody td').on( 'click', 'input:checkbox', function () {
            $('.example').DataTable().destroy();
            $('.example').DataTable({
            "aaSorting": [],           
            rowReorder: {
            selector: 'td:nth-child(2)'
            },
            
            dom: "Bfrtip",
            buttons: [
                {
                    extend: 'copyHtml5',
                    text: '<i class="fa fa-files-o"></i>',
                    titleAttr: 'Copy',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',                   
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-text-o"></i>',
                    titleAttr: 'CSV',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'                        
                    }
                },

                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    title: $('.download_label').html(),
                        customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' );
 
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                },
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                },
            ]
        });
        } );
    });
</script>