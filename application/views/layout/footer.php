<script src="<?php echo base_url(); ?>backend/dist/js/moment.min.js"></script>
<footer class="main-footer">
    &copy;  <?php echo date('Y'); ?>
    <?php echo $this->customlib->getAppName(); ?> <!-- <?php echo $this->customlib->getAppVersion(); ?> -->
</footer>
<div class="control-sidebar-bg"></div>
</div>
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>

<link href="<?php echo base_url(); ?>backend/toast-alert/toastr.css" rel="stylesheet"/>
<script src="<?php echo base_url(); ?>backend/toast-alert/toastr.js"></script>
<script src="<?php echo base_url(); ?>backend/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/daterangepicker/daterangepicker.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>backend/dist/js/jquery.mCustomScrollbar.concat.min.js"></script>
<!--language js-->
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript">
    $(function(){    
		$('.languageselectpicker').selectpicker();
	});    
</script>
<script src="<?php echo base_url(); ?>backend/js/jquery.scrolling-tabs.js"></script>
<script>

$('.navlistscroll').scrollingTabs({
    enableRtlSupport: true,
    bootstrapVersion: 3,
    reverseScroll: true,
});

</script>

<script type="text/javascript">
     $("#myModalpa").on('hidden.bs.modal', function (e) {
    console.log("sdfsdfsf");
     $(".filestyle").next(".dropify-clear").trigger("click");
  
     $('form#formaddpa').find('input:text, input:password, input:file, textarea').val('');
     $('form#formaddpa').find('select option:selected').removeAttr('selected');
     $('form#formaddpa').find('input:checkbox, input:radio').removeAttr('checked');
 });
    $(document).ready(function () {
      
        $(".patientsidebar").mCustomScrollbar({
            theme: "minimal"
        });

        $('.patientsideclose, .overlay').on('click', function () {
            $('.patientsidebar').removeClass('active');
            $('.overlay').fadeOut();
        });

        $('#sidebarCollapse').on('click', function () {
            $('.patientsidebar').addClass('active');
            $('.overlay').fadeIn();
            $('.collapse.in').toggleClass('in');
            $('a[aria-expanded=true]').attr('aria-expanded', 'false');
        });
    });
</script>
<script src="<?php echo base_url(); ?>backend/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url(); ?>backend/dist/js/moment.min.js"></script>
<?php
$language      = $this->customlib->getMyLanguage();
$language_name = $language["short_code"];

if ($language_name != 'en') {
    ?>
    <script src="<?php echo base_url(); ?>backend/plugins/datepicker/locales/bootstrap-datepicker.<?php echo $language_name ?>.js"></script>
    
<?php }?>
<script src="<?php echo base_url(); ?>backend/datepicker/js/bootstrap-datetimepicker.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/fastclick/fastclick.min.js"></script>
<script src="<?php echo base_url(); ?>backend/dist/js/app.min.js"></script>
<!--nprogress-->
<script src="<?php echo base_url(); ?>backend/dist/js/nprogress.js"></script>
<!--file dropify-->
<script src="<?php echo base_url(); ?>backend/dist/js/dropify.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/datatables/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/datatables/js/jszip.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/datatables/js/pdfmake.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/datatables/js/vfs_fonts.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/datatables/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/datatables/js/buttons.print.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/datatables/js/buttons.colVis.min.js" ></script>
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/datatables/js/dataTables.responsive.min.js" ></script>
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/datatables/js/ss.custom.js" ></script>
<script src="<?php echo base_url(); ?>backend/dist/datatables/js/datetime-moment.js"></script>
<script src="<?php echo base_url() ?>backend/plugins/select2/select2.full.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
    $(".dt-body-right a").tooltip();
});
</script>
</body>
</html>
<!-- jQuery 3 -->

<script src="<?php echo base_url() ?>backend/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="<?php echo base_url() ?>backend/fullcalendar/dist/locale-all.js"></script>

<?php if ($language_name != 'en') {?>
    <script src="<?php echo base_url() ?>backend/fullcalendar/dist/locale/<?php echo $language_name ?>.js"></script>
<?php }?>

<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

<?php
if ($this->session->flashdata('success_msg')) {
    ?>
            successMsg("<?php echo $this->session->flashdata('success_msg'); ?>");
    <?php
} else if ($this->session->flashdata('error_msg')) {
    ?>
            errorMsg("<?php echo $this->session->flashdata('error_msg'); ?>");
    <?php
} else if ($this->session->flashdata('warning_msg')) {
    ?>
            infoMsg("<?php echo $this->session->flashdata('warning_msg'); ?>");
    <?php
} else if ($this->session->flashdata('info_msg')) {
    ?>
            warningMsg("<?php echo $this->session->flashdata('info_msg'); ?>");
    <?php
}
?>
    });

    function complete_event(id, status) {
        $.ajax({
            url: "<?php echo site_url("admin/calendar/markcomplete/") ?>" + id,
            type: "POST",
            data: {id: id, active: status},
            dataType: 'json',
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
                    window.location.reload(true);
                }
            }
        });
    }

    function markc(id) {
        $('#newcheck' + id).change(function () {
            if (this.checked) {
                complete_event(id, 'yes');
            } else {
                complete_event(id, 'no');
            }
        });
    }
</script>

<?php //$this->load->view('layout/routine_update');?>
<!-- Button trigger modal -->
<!-- Modal -->
<div class="row">
    <div class="modal fade" id="sessionModal" tabindex="-1" role="dialog" aria-labelledby="sessionModalLabel">
        <form action="<?php echo site_url('admin/admin/activeSession') ?>" id="form_modal_session" class="form-horizontal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="sessionModalLabel"><?php echo $this->lang->line('session'); ?></h4>
                    </div>
                    <div class="modal-body sessionmodal_body pb0">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary submit_session" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait.."><?php echo $this->lang->line('save'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
$floor_list    = $this->floor_model->floor_list();
$bedlist       = $this->bed_model->bed_list();
$bedgroup_list = $this->bedgroup_model->bedGroupFloor();
?>
<div id="bed" class="modal fade bedmodal" role="dialog">
    <div class="modal-dialog modal100per">
        <!-- Modal content-->
        <div class="modal-content fullshadow">
            <button type="button" class="ukclose" data-dismiss="modal">&times;</button>
            <div class="modal-body">
                <div id="ajaxbedstatus"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function savedata(eventData) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/calendar/saveevent',
            type: 'POST',
            data: eventData,
            dataType: "json",
            success: function (msg) {
                alert(msg);

            }
        });
    }
</script>
<script type="text/javascript">
    $calendar = $('#calendar');
    var base_url = '<?php echo base_url() ?>';
    today = new Date();
    y = today.getFullYear();
    m = today.getMonth();
    d = today.getDate();
    var viewtitle = 'month';
    var pagetitle = "<?php
if (isset($title)) {
    echo $title;
}
?>";

    if (pagetitle == "Dashboard") {
        viewtitle = 'agendaWeek';
    }

    $calendar.fullCalendar({
        viewRender: function (view, element) {
            // We make sure that we activate the perfect scrollbar when the view isn't on Month
        },

        header: {
            center: 'title',
            right: 'month,agendaWeek,agendaDay',
            left: 'prev,next,today'
        },
        defaultDate: today,
        defaultView: viewtitle,
        selectable: true,
        selectHelper: true,
        views: {
            month: {// name of view
                titleFormat: 'MMMM YYYY'
                        // other view-specific options here
            },
            week: {
                titleFormat: " MMMM D YYYY"
            },
            day: {
                titleFormat: 'D MMM, YYYY'
            }
        },
        timezone: 'UTC',
        draggable: false,
        // lang: '<?php echo $language_name ?>',
        editable: false,
        eventLimit: false, // allow "more" link when too many events
        // color classes: [ event-blue | event-azure | event-green | event-orange | event-red ]
        events: {
            url: base_url + 'admin/calendar/getevents'
        },

        eventRender: function (event, element) {
            element.attr('title', event.title);
            element.attr('onclick', event.onclick);
            element.attr('data-toggle', 'tooltip');
            if ((!event.url) && (event.event_type != 'task')) {
                element.attr('title', event.title + '-' + event.description);
                element.click(function () {
                    view_event(event.id);
                });
            }
        },
        dayClick: function (date, jsEvent, view) {

            var d = date.format();
            if (!$.fullCalendar.moment(d).hasTime()) {
                d += ' 05:30';
            }
           
		<?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_add')) {?>

             var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';

                $("#input-field").val('');
                $("#desc-field").text('');
                $("#date-field").daterangepicker({

                    startDate: date,
                    endDate: date,
                    timePicker: true, timePickerIncrement: 5, locale: {
                    format: datetime_format

                    }
                });

                $('#newEventModal').modal('show');

<?php }?>
            return false;
        }
    });

    $(document).ready(function () {
         var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';

        $("#date-field").daterangepicker({timePicker: true, timePickerIncrement: 5, locale: {
                format: datetime_format
            }});

        $('.patient_list_ajax').select2({
            ajax: { 
                url: "<?= base_url(); ?>admin/patient/getPatientListAjax",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                   $('#case_reference_idd').val('');
                    return {
                    searchTerm: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        }); 
    });

    function datepic() {
        var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';
        $("#date-field").daterangepicker({
            format: datetime_format
        });
    }

    function view_event(id) {
		if(id > '0'){ 
        $('.selectevent').find('.cpicker-big').removeClass('cpicker-big').addClass('cpicker-small');
        var base_url = '<?php echo base_url() ?>';
        if (typeof (id) == 'undefined') {
            return;
        } 

         var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';

        $.ajax({
            url: base_url + 'admin/calendar/view_event/' + id,
            type: 'POST',
            dataType: "json",
            success: function (msg) {
                $("#event_title").val(msg.event_title);
                $("#event_desc").text(msg.event_description);
                $('#eventfrom').val(msg.startdate);
                $('#eventto').val(msg.enddate);
                $('#eventid').val(id);
                if (msg.event_type == 'public') {
                    $('input:radio[name=eventtype]')[0].checked = true;
                } else if (msg.event_type == 'private') {
                    $('input:radio[name=eventtype]')[1].checked = true;
                } else if (msg.event_type == 'sameforall') {
                    $('input:radio[name=eventtype]')[2].checked = true;
                } else if (msg.event_type == 'protected') {
                    $('input:radio[name=eventtype]')[3].checked = true;
                }

                $("#eventdates").daterangepicker({
                    startDate: msg.startdate,
                    endDate: msg.enddate,
                    timePicker: true, timePickerIncrement: 5, locale: {
                        format: calendar_date_time_format+' hh:mm A'
                    }
                });

                $("#event_color").val(msg.event_color);
                $("#delete_event").attr("onclick", "deleteevent(" + id + ",'Event ')");
                $("#" + msg.colorid).removeClass('cpicker-small').addClass('cpicker-big');
                $('#viewEventModal').modal('show');
            }
        });
		}
    }

    var calendar_date_time_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY']) ?>';
    $(document).ready(function () {
        $("body").delegate(".color", "focusin", function () {       
			$(this).colorpicker();
        });

        $("body").delegate(".date", "focusin", function () {
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';

        $(this).datepicker({
                todayHighlight: false,
                format: date_format,
                autoclose: true,                
            });
        });

         var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'hh', 'i' => 'mm']) ?>';        
    });


    $(document).ready(function (e) {
         var datetime_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(true, true), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY', 'H' => 'H', 'i' => 'mm','h'=>'hh']) ?>';


         var time_format = '<?php echo $result = strtr($this->customlib->getTimePickerFormat(), ['H' => 'hh', 'i' => 'mm','h'=>'hh']) ?>';
       

       $(function() {
           $('.time').datetimepicker({
               format: time_format
           });
       });



        $("body").delegate(".datetime", "focusin", function () {
            $(this).datetimepicker({
                format: datetime_format,
            }).on('dp.show', function (e) {  
              return $(this).data('DateTimePicker').defaultDate(new Date());
            });
        });

        
        var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        var capital_date_format = date_format.toUpperCase();
        $.fn.dataTable.moment(capital_date_format);

        $("body").delegate(".date", "focusin", function () {
            $(this).datepicker({
                todayHighlight: false,
                format: date_format,
                autoclose: true,
            });
        });
        
        var daterange_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY']) ?>';
        $("body").delegate(".daterange", "focusin", function () {
            $(this).daterangepicker({
                locale: {
                    format: daterange_format,
                },
            });
        });
    });

		$(document).ready(function (e) {
         var date_format = '<?php echo $result = strtr($this->customlib->getHospitalDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
        $("body").on("focusin", ".billDateDisabled",function () {
            $(this).datepicker({
                    format: date_format,
                    setDate: new Date(),
                      autoclose: true,
                      todayHighlight: true,
                      endDate: '+0d',
                });
            });
        });

    $(document).ready(function (e) {
        $("#addevent_form").on('submit', (function (e) {
            $("#addevent_formbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/calendar/saveevent") ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
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
                        window.location.reload(true);
                    }
                    $("#addevent_formbtn").button('reset');
                }
            });
        }));
    });

    $(document).ready(function (e) {
        $("#updateevent_form").on('submit', (function (e) {
            $("#updateevent_formbtn").button('loading');
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/calendar/updateevent") ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
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
                        window.location.reload(true);
                    }
                    $("#updateevent_formbtn").button('reset');
                }
            });
        }));
    });

    function deleteevent(id, msg) {
        if (typeof (id) == 'undefined') {
            return;
        }
        
        if (confirm("<?php echo $this->lang->line('are_you_sure_to_delete_this') ?>")) {
            
            $.ajax({
                url: base_url + 'admin/calendar/delete_event/' + id,
                type: 'POST',
                dataType: "json",
                success: function (res) {
                    if (res.status == "fail") {
                        errorMsg(res.message);
                    } else {
                        successMsg(msg + "<?php echo $this->lang->line('delete_message') ?>");
                        window.location.reload(true);
                    }
                }
            })
        }
    }

    $("body").on('click', '.cpicker', function () {
        var color = $(this).data('color');
        // Clicked on the same selected color
        if ($(this).hasClass('cpicker-big')) {
            return false;
        }

        $(this).parents('.cpicker-wrapper').find('.cpicker-big').removeClass('cpicker-big').addClass('cpicker-small');
        $(this).removeClass('cpicker-small', 'fast').addClass('cpicker-big', 'fast');
        if ($(this).hasClass('kanban-cpicker')) {
            $(this).parents('.panel-heading-bg').css('background', color);
            $(this).parents('.panel-heading-bg').css('border', '1px solid ' + color);
        } else if ($(this).hasClass('calendar-cpicker')) {
            $("body").find('input[name="eventcolor"]').val(color);
        }
    });

<?php if (isset($bedid)) {?>
        add_inpatient('<?php echo $bedid ?>', '<?php echo $bedgroupid ?>');
<?php }?>

    function getbedstatus() {
        $("#beddata").button('loading');
        $.ajax({
            url: base_url + 'admin/patient/getBedStatus/',
            type: 'POST',
            data: '',
            success: function (res) {
                $("#ajaxbedstatus").html(res);
                $("#bed").modal('show');
                $("#beddata").button('reset');
            }
        })
    }
	
    function popup(data, winload = false)
    {  
        var newWin=window.open('', 'Print-Window');
        newWin.document.open();
        console.log(data);
        //Create a new HTML document.
        newWin.document.write('<html>');
        newWin.document.write('<head>');
        newWin.document.write('<title></title>');    
        newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/dist/css/font-awesome.min.css">');
        newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/dist/css/sh-print.css">');       
        newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/dist/css/ionicons.min.css">');
        newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/dist/css/AdminLTE.min.css">');
        newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/dist/css/skins/_all-skins.min.css">');
        newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/plugins/iCheck/flat/blue.css">');
        newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/plugins/morris/morris.css">');
        newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">');
        newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/plugins/datepicker/datepicker3.css">');
        newWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/plugins/daterangepicker/daterangepicker-bs3.css">');
        newWin.document.write('</head>');
        newWin.document.write('<body onload="window.print()">');
        newWin.document.write(data);
        newWin.document.write('</body>');
        newWin.document.write('</html>');
        newWin.document.close();
        setTimeout(function () {
            newWin.close();      
            if (winload) {
                window.location.reload(true);
            }
        }, 5000);

        return true;
    } 

	function delete_recordByIdReload(url) {

        if (confirm('<?php echo $this->lang->line('are_you_sure_want_to_delete');?>')) {
             $.ajax({
                 url: baseurl+url,
                 dataType: 'json',
                 beforeSend: function() {
                                   
                },
                success: function (res) {
                    if(res.status == 1){
                    successMsg(res.msg);
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);                    
                }else{
                   errorMsg(res.msg);  
                }
               
              },
                        error: function(xhr) { // if error occured
                   alert('Something Went Wrong');
                 
            },
            complete: function() {                
            
            }
            })
        }
    }
	
	function delete_recordById(url) {
        if (confirm('<?php echo $this->lang->line('are_you_sure_want_to_delete');?>')) {
             $.ajax({
                 url: baseurl+url,
                 dataType: 'json',
                 beforeSend: function() {
                                   
                },
                 success: function (res) {
                    if(res.status == 1){
                    successMsg(res.msg);
                    table.ajax.reload();

                }else{
                   errorMsg(res.msg);  
                }
               
                        },
                        error: function(xhr) { // if error occured
                   alert('Something Went Wrong');
                 
            },
            complete: function() {                
            
            }
            })
        }
    }
</script>