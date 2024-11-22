<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('live_consultation'); ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('live_consultation'); ?></div>
                        <table class="table table-striped table-bordered table-hover example">
                                    <thead>
                                        <th><?php echo $this->lang->line('consultation_title') ; ?></th>
                                        <th><?php echo $this->lang->line('description') ; ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('created_by'); ?> </th>
                                        <th><?php echo $this->lang->line('created_for'); ?></th>
                                        <th><?php echo $this->lang->line('patient'); ?></th>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </thead>
                                    <tbody>
                                        <?php
if (empty($conferences)) {
    ?>

                                        <?php
} else {
    foreach ($conferences as $conference_key => $conference_value) {

        $return_response = json_decode($conference_value->return_response);
        ?>
                                            <tr>
                                                <td class="mailbox-name"><?php echo $conference_value->title; ?></td>
                                                <td class="mailbox-name"><?php echo $conference_value->description; ?></td>
                                                <td class="mailbox-name"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($conference_value->date)) ?></td>
                                                 <td class="mailbox-name">
                                                    <?php
        $name = ($conference_value->create_by_surname == "") ? $conference_value->create_by_name : $conference_value->create_by_name . " " . $conference_value->create_by_surname;
       
        if ($name == 'Super Admin') {
            echo composeStaffNameByString($name, '', $conference_value->create_by_employee_id);
        } else {
            echo $name . " (" . $conference_value->create_by_role_name . ": " . $conference_value->create_by_employee_id . ")";
        }

        ?></td>
                                                <td class="mailbox-name">
                                                    <?php

        $name = ($conference_value->create_for_surname == "") ? $conference_value->create_for_name : $conference_value->create_for_name . " " . $conference_value->create_for_surname;
        echo $name . " (" . $conference_value->create_for_role_name . ": " . $conference_value->create_for_employee_id . ")";

        ?>
                                                </td>
                                                <td class="mailbox-name">
                                                     <?php
        $name = ($conference_value->patient_name == "") ? $conference_value->patient_name : $conference_value->patient_name;
        echo $name . " (" . $conference_value->patient_unique_id . ")";
        ?>
                                                </td>
                                            <td class="mailbox-name">
                                                    <?php if ($conference_value->status == 0) { ?>
                                                        <span class="label label-warning font-w-normal"><?php echo $this->lang->line('awaited'); ?></span>
                                                    <?php } ?>
                                                    <?php if ($conference_value->status == 1) { ?>
                                                        <span class="label label-danger font-w-normal"><?php echo $this->lang->line('cancelled'); ?></span>
                                                    <?php } ?>
                                                    <?php if ($conference_value->status == 2) { ?>
                                                        <span class="label label-success font-w-normal"><?php echo $this->lang->line('finished'); ?></span>
                                                    <?php } ?>
                                            </td>
                                            <td class="mailbox-date pull-right noExport">
                                                    <?php
if ($conference_value->status == 0) {
            ?>
                                           <a href="#" class="btn btn-xs label-success p0" data-toggle="modal" data-target="#modal-chkstatus" data-id="<?php echo $conference_value->id; ?>">
                                                      <span class="label" ><i class="fa fa-video-camera"></i> <?php echo $this->lang->line('join') ?></span>
                                            <?php
}
        ?>
                                                </td>
                                            </tr>
                                            <?php
}
}
?>
                                    </tbody>
                                </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="modal-chkstatus" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
    <form id="form-chkstatus" action="" method="POST">
        <div class="modal-content mx-2">
            <div class="">
                <button type="button" class="close modalclosezoom" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="zoom_details">
            </div>
        </div>
    </form>
    </div>
</div>

<div class="modal fade" id="viewModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mx-2">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-toggle="tooltip" title="<?php echo $this->lang->line('clase'); ?>" data-dismiss="modal">&times;</button>
                <div class="modalicon">
                    <div id='edit_deletebill'>
                        <a href="#"  data-target="#edit_prescription"  data-toggle="modal" title="" data-original-title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>
                        <a href="#" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                <h4 class="box-title"><?php echo $this->lang->line('bill_details'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0">
                <div id="reportdata"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        modal_click_disabled('modal-chkstatus');
    });
    function viewDetail(id) {
        $.ajax({
            url: '<?php echo base_url() ?>patient/dashboard/getBillDetailsBloodbank/' + id,
            type: "GET",
            data: {id: id},
            success: function (data) {
                $('#reportdata').html(data);
                $('#edit_deletebill').html("<a href='#' data-toggle='tooltip' onclick='printData(" + id + ")'   data-original-title='<?php echo $this->lang->line('print'); ?>'><i class='fa fa-print'></i></a> ");
                holdModal('viewModal');
            },
        });
    }

    function holdModal(modalId) {
        $('#' + modalId).modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

     $(document).on('click', 'a.join-btn', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = $(this).attr('href');
            $.ajax({
                url: "<?php echo site_url("patient/dashboard/add_history") ?>",
                type: "POST",
                data: {"id": id},
                dataType: 'json',
                beforeSend: function () {
                }, success: function (res)
                {
                    if (res.status == 0) {
                    } else if (res.status == 1) {
                        window.open(url, '_blank');
                    }
                },
                error: function (xhr) {
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                },
                complete: function () {
                }
            });
        });

		$('#modal-chkstatus').on('shown.bs.modal', function (e) {
            var $modalDiv = $(e.delegateTarget);
              var id=$(e.relatedTarget).data('id');
            $.ajax({
                type: "POST",
                url: '<?php echo site_url("patient/dashboard/getlivestatus") ?>',
                data: {'id':id},
                dataType: "JSON",
                beforeSend: function () {
                $('#zoom_details').html("");
                    $modalDiv.addClass('modal_loading');
                },
                success: function (data) {
                   $('#zoom_details').html(data.page);
                    $modalDiv.removeClass('modal_loading');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $modalDiv.removeClass('modal_loading');
                },
                complete: function (data) {
                    $modalDiv.removeClass('modal_loading');
                }
            });
        })
</script>