<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content">
        <div class="row">   
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria') ?></h3>
                    </div>
                    <form id='form1' action="<?php echo site_url('admin/staffattendance/attendancereport') ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('role'); ?></label>
                                        <select  id="role" name="role" class="form-control" >
                                            <option value="select"><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($role as $role_key => $value) {
                                                ?>
                                                <option value="<?php echo $value["type"] ?>" <?php
                                                if ($role_selected == $value["type"]) {
                                                    echo "selected =selected";
                                                }
                                                ?>><?php echo $value["type"]; ?></option>
                                                        <?php
                                                        $count++;
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('role'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label>
                                        <select  id="empname" name="empname" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('month'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('year'); ?></label>
                                        <select  id="year" name="year" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($yearlist as $y_key => $year) {
                                                ?>
                                                <option value="<?php echo $year["year"] ?>" <?php
                                                if ($year["year"] == date("Y")) {
                                                    echo "selected";
                                                }
                                                ?> ><?php echo $year["year"]; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('month'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                        </div>
                    </form>
                </div>
                <?php
                if (isset($resultlist)) {
                    ?>
                    <div class="box box-info" id="attendencelist">
                        <div class="box-header with-border">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <h3 class="box-title"><i class="fa fa-users"></i> <?php echo $this->lang->line('staff'); ?><?php echo $this->lang->line('attendance'); ?> <?php echo $this->lang->line('report'); ?></h3>
                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <div class="pull-right">
                                        <!-- right content -->
                                    </div>
                                </div>
                            </div></div>
                        <div class="box-body table-responsive">
                            <?php
                            if (!empty($resultlist)) {
                                ?>
                                <div class="mailbox-controls">
                                    <div class="pull-right">
                                    </div>
                                </div>
                                <div class="download_label"><?php echo $this->lang->line('staff'); ?> <?php echo $this->lang->line('attendance'); ?> <?php echo $this->lang->line('report'); ?></div>
                                <table class="table table-striped table-bordered table-hover example xyz">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('date') . " | " . $this->lang->line('month'); ?> </th>
                                            <?php foreach ($monthlist as $monthkey => $monthvalue) {
                                                ?>
                                                <th><?php echo $monthvalue ?></th>
                                            <?php }
                                            ?>
                                            <th colspan=""><br/><!-- attendance value --></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $j = 0;
                                        for ($i = 1; $i <= 31; $i++) {
                                            ?>
                                            <tr>
                                                <td><?php echo $attendence_array[$j] ?></td>
                                                <?php
                                                foreach ($monthlist as $key => $value) {
                                                    $datemonth = date("m", strtotime($value));
                                                    $att_dates = date("Y") . "-" . $datemonth . "-" . sprintf("%02d", $i);
                                                    ?>
                                                    <td><?php echo $resultlist[$att_dates]["key"]; ?></td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                            $j++;
                                        }
                                        ?>   
                                    </tbody>
                                </table>
                                <?php
                            } else {
                                ?>
                                <div class="alert alert-info">
                                    <?php echo $this->lang->line('no_attendance_prepare'); ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
                </section>
            </div>
			
            <script type="text/javascript">
                $(document).ready(function () {

                    var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';
                    $('#date').datepicker({
                        format: date_format,
                        autoclose: true
                    });

                    $('.detail_popover').popover({
                        placement: 'right',
                        title: '',
                        trigger: 'hover',
                        container: 'body',
                        html: true,
                        content: function () {
                            return $(this).closest('th').find('.fee_detail_popover').html();
                        }
                    });
                });
            </script>			
            <script type="text/javascript">
                var base_url = '<?php echo base_url() ?>';
                function printDiv(elem) {
                    popup(jQuery(elem).html());
                }                
            </script>