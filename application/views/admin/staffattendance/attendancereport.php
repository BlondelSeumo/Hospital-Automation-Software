<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content">
        <div class="row">   
            <div class="col-md-12">
                <div class="box box-primary">
                <?php $this->load->view('admin/report/_human_resource');?>
                <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('staff_attendance_report'); ?> </h3>
                    </div>
                    <form id='form1' action="<?php echo site_url('admin/staffattendance/attendancereport') ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('role'); ?></label>
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
                                        <label><?php echo $this->lang->line('month'); ?></label><small class="req"> *</small>
                                        <select id="month" name="month" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($monthlist as $m_key => $month) {
                                                ?>
                                                <option value="<?php echo $m_key ?>" <?php
                                                if ($month_selected == $m_key) {
                                                    echo "selected =selected";
                                                }
                                                ?>><?php echo $month; ?></option>
                                                        <?php
                                                        $count++;
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('month'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('year'); ?></label>
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
                                        <span class="text-danger"><?php echo form_error('year'); ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div> 
                    </form>
                    <?php
                    if (isset($resultlist)) {
                        ?>
                        <div class="box border0 clear" id="attendencelist">
                            <div class="ptbnull"></div>
                            <div class="box-header">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                    </div>
                                    <div class="col-md-8 col-sm-8">
                                        <div class="pull-right">
                                            <?php
                                            foreach ($attendencetypeslist as $key_type => $value_type) {
                                                ?>
                                                &nbsp;&nbsp;
                                                <b>
                                                    <?php
                                                    $att_type = strtolower($value_type['type']);
                                                    echo $this->lang->line($att_type) . ": " . $value_type['key_value'] . "";
                                                    ?>
                                                </b>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div></div>
                            <div class="table-responsive box-body">
                                <?php
                                if (!empty($resultlist)) {
                                    ?>
                                    <div class="download_label"><?php echo $this->lang->line('staff_attendance_report'); ?></div>
                                    <table class="table table-striped table-bordered table-hover example">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <?php echo $this->lang->line('staff_date'); ?>
                                                </th>
                                                <th><br/><span data-toggle="tooltip" title="<?php echo $this->lang->line('gross_present_percentage'); ?>">%</span></th>
                                                <?php
                                                if (!empty($attendence_array)) {
                                                    foreach ($attendencetypeslist as $key => $value) {
                                                        ?>
                                                        <th><br/><span data-toggle="tooltip" title="<?php echo "Total " . $value["type"]; ?>"><?php echo strip_tags($value["key_value"]); ?>

                                                            </span></th>

                                                        <?php
                                                    }
                                                }
                                                ?>
                                                <?php
                                                foreach ($attendence_array as $at_key => $at_value) {
                                                    
                                                    $new_at_value =  $this->customlib->YYYYMMDDTodateFormat($at_value);                                                    
                                                    $weekday = date('D', strtotime($at_value));
                                                    
                                                    
                                                    if ($weekday == "Sun") {
                                                        ?>
                                                        <th class="tdcls text text-center bg-danger">
                                                            <?php                                                            
                                                            
                                                            echo date('d', strtotime($at_value));
                                                            echo "<br/>";
                                                            $dayweek = date('D', strtotime($at_value));             
                                                            echo $this->lang->line(strtolower($dayweek));
                                                           
                                                            ?>
                                                        </th>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <th class="tdcls text text-center">
                                                            <?php                                                             
                                                           
                                                            echo date('d', strtotime($at_value));
                                                            echo "<br/>";
                                                            $dayweek =  date('D', strtotime($at_value));
                                                            echo $this->lang->line(strtolower($dayweek));
                                                            
                                                            ?>
                                                        </th>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($staff_array)) {
                                                ?>
                                                <tr>
                                                    <td colspan="32" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>
                                                </tr>
                                                <?php
                                            } else {

                                                $row_count = 1;
                                                $i = 0;

                                                foreach ($staff_array as $staff_key => $staff_value) {

                                                    $total_present = ($monthAttendance[$i][$staff_value['id']]['present'] + $monthAttendance[$i][$staff_value['id']]['late'] + $monthAttendance[$i][$staff_value['id']]['half_day']);

                                                    $total_days = $monthAttendance[$i][$staff_value['id']]['present'] + $monthAttendance[$i][$staff_value['id']]['late'] + $monthAttendance[$i][$staff_value['id']]['absent'] + $monthAttendance[$i][$staff_value['id']]['half_day']; 

                                                    if ($total_days == 0) {
                                                        $percentage = -1;
                                                        $print_percentage = "-";
                                                    } else {

                                                        $percentage = ($total_present / $total_days) * 100;
                                                        $print_percentage = round($percentage, 0);
                                                    }

                                                    if (($percentage < 75) && ($percentage >= 0)) {
                                                        $label = "class='label label-danger'";
                                                    } else if ($percentage > 75) {
                                                        $label = "class='label label-success'";
                                                    } else {
                                                        $label = "class='label label-default'";
                                                    }
                                                    
                                                    ?>
                                                    <tr>
                                                        <th class="tdclsname"><span data-toggle="popover" class="detail_popover" data-original-title="" title=""><a href="#" style="color:#333"><?php echo $staff_value['name'] . " " . $staff_value['surname']; ?></a></span>
                                                            <div class="fee_detail_popover" style="display: none"><?php echo $this->lang->line('staff_id'); ?>: <?php echo $staff_value['employee_id']; ?></div>
                                                        </th>
                                                        <th><?php echo "<label $label>" . $print_percentage . "</label>";     ?></th>
                                                        <th><?php echo $monthAttendance[$i][$staff_value['id']]['present']; ?></th>
                                                        <th><?php echo $monthAttendance[$i][$staff_value['id']]['late']; ?></th>
                                                        <th><?php echo $monthAttendance[$i][$staff_value['id']]['absent']; ?></th>
                                                        <th><?php echo $monthAttendance[$i][$staff_value['id']]['half_day']; ?></th>
                                                        <th><?php echo $monthAttendance[$i][$staff_value['id']]['holiday']; ?></th>
                                                        <th><?php echo $monthAttendance[$i][$staff_value['id']]['half_day_second_shift']; ?></th>
                                                        <?php
                                                        foreach ($attendence_array as $at_key => $at_value) {
                                                            ?>
                                                            <th class="tdcls text text-center">
                                                                <span data-toggle="popover" class="detail_popover" data-original-title="" title=""><a href="#" style="color:#333"><?php
                                                                        print_r($resultlist[$at_value][$staff_value['id']]['key']);
                                                                        ?></a></span>
                                                                <div class="fee_detail_popover" style="display: none"><?php
                                                                    if (!empty($resultlist[$at_value][$staff_value['id']]['remark'])) {
                                                                        echo $resultlist[$at_value][$staff_value['id']]['remark'];
                                                                    }
                                                                    ?></div>
                                                            </th>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                }
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
					</div>
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