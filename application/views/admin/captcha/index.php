<style type="text/css">
    .material-switch > input[type="checkbox"] {
        display: none;   
    }

    .material-switch > label {
        cursor: pointer;
        height: 0px;
        position: relative; 
        width: 40px;  
    }

    .material-switch > label::before {
        background: rgb(0, 0, 0);
        box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
        border-radius: 8px;
        content: '';
        height: 16px;
        margin-top: -8px;
        position:absolute;
        opacity: 0.3;
        transition: all 0.4s ease-in-out;
        width: 40px;
    }
	
    .material-switch > label::after {
        background: rgb(255, 255, 255);
        border-radius: 16px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        content: '';
        height: 24px;
        left: -4px;
        margin-top: -8px;
        position: absolute;
        top: -4px;
        transition: all 0.3s ease-in-out;
        width: 24px;
    }
	
    .material-switch > input[type="checkbox"]:checked + label::before {
        background: inherit;
        opacity: 0.5;
    }
	
    .material-switch > input[type="checkbox"]:checked + label::after {
        background: inherit;
        left: 20px;
    }
	
    .table .pull-right {
		width: auto;
		text-align: initial;
	}
</style>

<div class="content-wrapper">  
    <!-- Main content -->
    <section class="content">
        <div class="row">   
        <?php $this->load->view('setting/sidebar.php'); ?>     
            <div class="col-md-10">            
                <div class="nav-tabs-custom theme-shadow">
                    <ul class="nav nav-tabs pull-right">
                        <li class="pull-left header"><?php echo $this->lang->line('captcha_settings'); ?></li>                        
                    </ul>
                    <div class="tab-content">
                    <div class="download_label"><?php echo $this->lang->line('captcha_settings'); ?></div>
                        <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <?php if ($this->rbac->hasPrivilege('captcha_setting', 'can_edit')) { ?>
    								    <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>									
    							<?php
                                if (!empty($fields)) {
                                    foreach ($fields as $fields_key => $fields_value) {
                                        ?>
                                 <tr>
                                            <td><?php echo $fields_value; ?></td>
                                            <?php if ($this->rbac->hasPrivilege('captcha_setting', 'can_edit')) { ?>
                                                <td class="relative noExport">
                                                <?php if ($this->rbac->hasPrivilege('captcha_setting', 'can_edit')) { ?>
                                                    <div class="material-switch pull-right">
                                                        <input id="field_<?php echo $fields_key ?>" name="<?php echo $fields_key; ?>" type="checkbox" data-role="field_<?php $fields_key?>" class="chk"  value="" <?php echo set_checkbox($fields_key, $fields_key, findSelected($inserted_fields,$fields_key)); ?> />
                                                        <label for="field_<?php echo $fields_key ?>" class="label-success"></label>
                                                    </div>
                                                <?php } ?>
                                                </td>
                                            <?php } ?>
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

<?php 

function findSelected($inserted_fields,$find){
    foreach ($inserted_fields as $inserted_key => $inserted_value) {
       if($find == $inserted_value->name && $inserted_value->status==1){
        return true;
       }
    }
    return false;
}

?>

<script type="text/javascript">
    $(document).ready(function () {

        $(document).on('click', '.chk', function(event) {
            var name=$(this).attr('name');
            var status=1;
            if(this.checked) {
             status=1;
            } else {
             status=0;
            }
             if(confirm("<?php echo $this->lang->line('confirm_status'); ?>")){              
                changeStatus(name, status);
            }
            else{
                event.preventDefault();
            }
        });
    });

    function changeStatus(name, status) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            type: "POST",
            url: base_url + "admin/captcha/changeStatus",
            data: {'name': name, 'status': status},
            dataType: "json",
            success: function (data) {
                successMsg(data.msg);
            }
        });
    }
</script>
