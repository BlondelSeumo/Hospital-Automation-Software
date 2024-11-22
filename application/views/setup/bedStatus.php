<div class="content-wrapper" style="min-height: 348px;">  
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <?php   $this->load->view('setup/bedsidebar');     ?>
            </div>
            <div class="col-md-10">
                <!-- general form elements -->
                 <?php if ($this->rbac->hasPrivilege('bed_status', 'can_view')) { ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('bed_status'); ?></h3>
                    </div>                  
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('bed_status'); ?></div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>                                    
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('bed_type'); ?></th>
                                        <th><?php echo $this->lang->line('bed_group') ; ?></th>
                                        <th><?php echo $this->lang->line('floor'); ?></th>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (empty($bed_list)) {
                                        ?>
                                        <?php
                                    } else {
                                        foreach ($bed_list as $key => $value) {
                                            if ($value['is_active'] == 'no') {
                                                $color = "danger";
                                            } elseif ($value['is_active'] == 'yes') {
                                                $color = "success";
                                            } elseif ($value['is_active'] == 'unused') {
                                                $color = "bed-unused";
                                            }
                                            ?>
                                            <tr class="<?php echo $color ?>">
                                                <td class="mailbox-name">
                                                    <?php echo $value['name'] ?>
                                                </td>
                                                <td><?php echo $value['bed_type_name']; ?></td>
                                                <td><?php echo $value['bedgroup']; ?></td>
                                                <td><?php echo $value['floor_name']; ?></td>
                                                <td class="mailbox-name">
                                                    <?php
                                                    if ($value['is_active'] == 'yes') {
                                                        echo $this->lang->line("available");
                                                    } elseif($value['is_active'] == 'no'){
                                                        echo $this->lang->line("allotted");
                                                    } elseif($value['is_active'] == 'unused'){
                                                        echo $this->lang->line("unused");
                                                    }
                                                    ?>         
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            <?php }?>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<!-- new END -->
</div><!-- /.content-wrapper -->

<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });

</script>