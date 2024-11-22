<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<div class="content-wrapper">
    <section class="content">
        <div class="row">           
            <div class="col-md-12">
                <!-- Custom Tabs (Pulled to the right) -->
                <div class="nav-tabs-custom overflow-hidden">
                    <ul class="nav nav-tabs pull-right">
                        <li class="pull-left header"><?php echo $this->lang->line('send_sms'); ?></li>
                        <li><a href="#tab_perticular" data-toggle="tab"><?php echo $this->lang->line('individual'); ?></a></li>
                        <li class="active"><a href="#tab_group" data-toggle="tab"><?php echo $this->lang->line('group'); ?></a></li>
                    </ul>
                    <div class="tab-content pb0">
                        <div class="tab-pane active" id="tab_group">
                            <form action="<?php echo site_url('admin/mailsms/send_group_sms') ?>" method="post" id="group_form">
                                <!-- /.box-header -->
                                <div class="pb10">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                                                <input  class="form-control" name="group_title">
                                            </div>
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('template_id'); ?></label>&nbsp; (<small style="color:red"><?php echo $this->lang->line('this_field_require_for_smsgateway'); ?></small>)
                                                <input  class="form-control" name="group_template_id">
                                            </div>
                                            <div class="form-group">
                                                <label class="pr20"><?php echo $this->lang->line('send_through'); ?>
                                                <small class="req"> *</small>
                                                </label>                                              
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" value="sms" name="group_send_by[]"><?php echo $this->lang->line('sms'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" value="push" name="group_send_by[]"><?php echo $this->lang->line('mobile_app'); ?>
                                                </label>
                                                <span class="text-danger"><?php echo form_error('message'); ?></span>
                                            </div>                                          
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('message'); ?></label><small class="req"> *</small>
                                                <textarea id="group_msg_text" name="group_message" class="form-control compose-textarea" rows="12"><?php echo set_value('message'); ?></textarea>
                                                <span class="text-muted tot_count_group_msg_text pull-right word_counter"><?php echo $this->lang->line('character_count'); ?>: 0</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('message_to'); ?></label><small class="req"> *</small>
                                                <div class="well minheight303">
                                                    <div class="checkbox mt0">
                                                        <label><input type="checkbox" name="user[]" value="patient"> <b><?php echo $this->lang->line('patient'); ?></b> </label>
                                                    </div>
                                                    <?php
                                                    foreach ($roles as $role_key => $role_value) {
                                                        ?>
                                                        <div class="checkbox">
                                                            <label><input type="checkbox" name="user[]" value="<?php echo $role_value['id']; ?>"> <b><?php echo $role_value['name']; ?></b></label>
                                                        </div>

                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>       
                                        </div>
                                    </div>
                                </div>
                                <!-- /. -->
                                <div class="modal-footer row">
                                    <div class="pull-right">
                                        <button type="submit" class="btn btn-primary submit_group" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('sending'); ?>" ><i class="fa fa-envelope-o"></i> <?php echo $this->lang->line('send'); ?></button>
                                    </div>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_perticular">
                    <form action="<?php echo site_url('admin/mailsms/send_individual_sms') ?>" method="post" id="individual_form">
                                <!-- /.box-header -->
                                <div class="pb10">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('title'); ?></label>
                                                <small class="req"> *</small>
                                                <input class="form-control" name="individual_title">
                                            </div>
                                            <div class="form-group">
                                                <label class="pr20"><?php echo $this->lang->line('send_through'); ?><small class="req"> *</small></label>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="individual_send_by[]" value="sms"><?php echo $this->lang->line('sms'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="individual_send_by[]" value="push"><?php echo $this->lang->line('mobile_app'); ?>
                                                </label>
                                                <span class="text-danger"><?php echo form_error('message'); ?></span>
                                            </div>
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('template_id'); ?></label>&nbsp; (<small style="color:red"><?php echo $this->lang->line('this_field_require_for_smsgateway'); ?></small>)
                                                <input  class="form-control" name="individual_template_id">
                                            </div>                                          
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('message'); ?></label><small class="req"> *</small>
                                                <textarea id="individual_msg_text" name="individual_message" class="form-control compose-textarea" rows="12"><?php echo set_value('message'); ?></textarea>
                                                <span class="text-muted tot_count_individual_msg_text pull-right word_counter"><?php echo $this->lang->line('character_count'); ?>: 0</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="inpuFname"><?php echo $this->lang->line('message_to'); ?></label><small class="req"> *</small>
                                                <div class="input-group">
                                                    <div class="input-group-btn bs-dropdown-to-select-group">
                                                        <button type="button" class="btn btn-default btn-searchsm dropdown-toggle as-is bs-dropdown-to-select" data-toggle="dropdown">
                                                            <span data-bind="bs-drp-sel-label"><?php echo $this->lang->line('select'); ?></span>
                                                            <input type="hidden" name="selected_value" data-bind="bs-drp-sel-value" value="">
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu" style="">
                                                            <li data-value="Patient"><a href="#" ><?php echo $this->lang->line('patient'); ?></a></li>
                                                            <?php
                                                            foreach ($roles as $role_key => $role_value) {
                                                                ?>
                                                                <li data-value="Staff-<?php echo $role_value['id'] ?>"><a href="#"><?php echo $role_value['name']; ?></a></li>
                                                                <?php
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                    <input type="text" value="" data-record="" data-email="" data-mobileno="" data-app_key="" class="form-control" autocomplete="off" name="text" id="search-query">
                                                    <div id="suggesstion-box"></div>
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-primary btn-searchsm add-btn" type="button"><?php echo $this->lang->line('add'); ?></button>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="dual-list list-right">
                                                <div class="well minheight260">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="input-group">
                                                                <input type="text" name="SearchDualList" class="form-control" placeholder="<?php echo $this->lang->line('search'); ?>" />
                                                                <div class="input-group-btn"><span class="btn btn-default input-group-addon bright groupbtn btn-xs height28"><i class="fa fa-search"></i></span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="wellscroll">
                                                        <ul class="list-group send_list">
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="modal-footer row">
                                    <div class="pull-right">
                                        <button type="submit" class="btn btn-primary submit_individual" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('sending'); ?>" ><i class="fa fa-envelope-o"></i> <?php echo $this->lang->line('send'); ?></button>
                                    </div>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>                        
                    </div>
                </div>
            </div>
        </div> 
    </section>
</div>
<script>
    $(document).on('click', '.dropdown-menu li', function () {
        $("#suggesstion-box ul").empty();
        $("#suggesstion-box").hide();
    });
    $(document).ready(function (e) {
        $(document).on('click', '.bs-dropdown-to-select-group .dropdown-menu li', function (event) {
            var $target = $(event.currentTarget);
            $target.closest('.bs-dropdown-to-select-group')
                    .find('[data-bind="bs-drp-sel-value"]').val($target.attr('data-value'))
                    .end()
                    .children('.dropdown-toggle').dropdown('toggle');
            $target.closest('.bs-dropdown-to-select-group')
                    .find('[data-bind="bs-drp-sel-label"]').text($target.context.textContent);
            return false;
        });
    });
</script>

<script type="text/javascript">
    var attr = {};

    $(document).ready(function () {
        $("#search-query").keyup(function () {
            $("#search-query").attr('data-record', "");
            $("#search-query").attr('data-email', "");
            $("#search-query").attr('data-mobileno', "");
            $("#search-query").attr('data-app_key', "");
            $("#suggesstion-box").hide();
            var category_selected = $("input[name='selected_value']").val();
            var arr = category_selected.split('-');
            var category_set = arr[0];

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('admin/mailsms/search') ?>",
                data: {'keyword': $(this).val(), 'category': category_selected},
                dataType: 'JSON',
                beforeSend: function () {
                    $("#search-query").css("background", "#FFF url(../../backend/images/loading.gif) no-repeat 165px");
                },
                success: function (data) {

                    if (data.length > 0) {
                        setTimeout(function () {
                            $("#suggesstion-box").show();
                            var cList = $('<ul/>').addClass('selector-list');
                            $.each(data, function (i, obj)
                            {
                                 if (category_set == "Staff") {
                                    var email = obj.email;
                                    var contact = obj.contact_no;
                                    var name = obj.name + ' ' + obj.surname + ' (' + obj.employee_id + ')';
                                } else if (category_set == "Patient") {

                                    var email = obj.email;
                                    var contact = obj.mobileno;
                                    var app_key = obj.app_key;
                                    var name = obj.patient_name + ' (' + obj.id + ')';
                                }

                                var li = $('<li/>')
                                        .addClass('ui-menu-item')
                                        .attr('category', category_set)
                                        .attr('record_id', obj.id)
                                        .attr('email', email)
                                        .attr('mobileno', contact)
                                        .attr('app_key', app_key)
                                        .text(name)
                                        .appendTo(cList);
                            });
                            $("#suggesstion-box").html(cList);
                            $("#search-query").css("background", "#FFF");
                        }
                        , 1000);
                    } else {
                        $("#suggesstion-box").hide();
                        $("#search-query").css("background", "#FFF");
                    }
                }
            });
        });
    });

     $(document).on('click', '.selector-list li', function () {
        var val = $(this).text();
        var record_id = $(this).attr('record_id');
        var email = $(this).attr('email');
        var mobileno = $(this).attr('mobileno');
        var app_key = $(this).attr('app_key');

        $("#search-query").attr('value', val).val(val);
        $("#search-query").attr('data-record', record_id);
        $("#search-query").attr('data-email', email);
        $("#search-query").attr('data-app_key', app_key);
        $("#search-query").attr('data-mobileno', mobileno);
        $("#suggesstion-box").hide();
    });


    $(document).on('click', '.add-btn', function () {      
        var value = $("#search-query").val();
        var record_id = $("#search-query").attr('data-record');
        var email = $("#search-query").attr('data-email');
        var mobileno = $("#search-query").attr('data-mobileno');
        var app_key = $("#search-query").attr('data-app_key');        
        var category_selected = $("input[name='selected_value']").val();        
        const myArray = category_selected.split("-");
        let word = myArray[0];
        
        if (record_id != "" && category_selected != "") {
            var chkexists = checkRecordExists(category_selected + "-" + record_id);
            if (chkexists) {
                var arr = [];
                arr.push({
                    'category': category_selected,
                    'record_id': record_id,
                    'email': email,
                    'mobileno': mobileno,
                    'app_key':app_key
                });

                attr[category_selected + "-" + record_id] = arr;
                $("#search-query").attr('value', "").val("");
                $("#search-query").attr('data-record', "");
                $(".send_list").append('<li class="list-group-item" id="' + category_selected + '-' + record_id + '"><i class="fa fa-user"></i> ' + value + ' (' + word + ') <i class="glyphicon glyphicon-trash pull-right text-danger" title="<?php echo $this->lang->line('delete'); ?>" onclick="delete_record(' + "'" + category_selected + '-' + record_id + "'" + ')"></i></li>');
            } else {
                errorMsg('<?php echo $this->lang->line('record_already_exists') ?>');
            }
        } else {
            errorMsg("<?php echo $this->lang->line('message_to') . " field is required" ?>");
        }
        getTotalRecord();
    });
</script>

<script type="text/javascript">
    function getTotalRecord() {
        $.each(attr, function (key, value) {           

        });
    }
	
    function checkRecordExists(find) {
        if (find in attr) {
            return false;
        }
        return true;
    }

    $(function () {
        $('[name="SearchDualList"]').keyup(function (e) {
            var code = e.keyCode || e.which;
            if (code == '9')
                return;
            if (code == '27')
                $(this).val(null);
            var $rows = $(this).closest('.dual-list').find('.list-group li');
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
            $rows.show().filter(function () {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });
    });
	
    function delete_record(record) {
        delete attr[record];
        $('#' + record).remove();
        getTotalRecord();
        return false;
    }

    $("#individual_form").submit(function (event) {
        var $form = $(this),
                url = $form.attr('action');
        var formData = $(this).serializeArray();
        console.log(formData);
        var user_list = (!jQuery.isEmptyObject(attr)) ? JSON.stringify(attr) : "";
        formData.push({name: "user_list", value: user_list});
        event.preventDefault();
        var $this = $('.submit_individual');
        $this.button('loading');

        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
                    var message = "";
                    $.each(data.msg, function (index, value) {

                        message += value;
                    });
                    errorMsg(message);
                } else {
                    $('#individual_form')[0].reset();
                    $("ul.send_list").empty();
                    attr = {};
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }, complete: function (data) {
                $this.button('reset');
            }
        })
    });

    $("#group_form").submit(function (event) {
        var $form = $(this),
                url = $form.attr('action');
        var formData = $(this).serializeArray();

        event.preventDefault();
        var $this = $('.submit_group');
        $this.button('loading');

        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
                    var message = "";
                    $.each(data.msg, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    $('#group_form')[0].reset();
                    successMsg(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }, complete: function (data) {
                $this.button('reset');
            }
        })

    });

    $(document).on('keypress keyup keydown paste change focus blur','.compose-textarea',function(event){
    var total_length=checkTextAreaMaxLength(this,event);
    $(this).next('span.word_counter').html("Character Count: " + total_length)

     });
    function checkTextAreaMaxLength(textBox, e) {
        return textBox.value.length;
    }
</script>