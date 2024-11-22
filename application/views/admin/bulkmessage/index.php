<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
$genderList = $this->customlib->getGender();
?>
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title titlefix"> <?php echo $this->lang->line('send_patient_credential'); ?></h3>
          </div><!-- /.box-header -->
          <div class="box-body">		  
            <form action="<?php echo site_url('admin/bulkmessage/sendbulkmail') ?>" method="POST" id="bulkmail">	
				<div class="row">                     
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('select_all'); ?> </label><br>
                            <input type="checkbox" name="checkAll">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('credential_type'); ?> <small class="req"> *</small></label>
                            <select  id="notification_type" name="notification_type" class="form-control" >														
                                <?php foreach ($notificationtype as $key => $notificationtype_value) {?>
                                <option value="<?php echo $key; ?>"><?php echo $notificationtype_value; ?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>                    
                </div>				
				<div class="table-responsive">					
					<table class="table table-striped table-bordered table-hover ajaxlist" cellspacing="0" width="100%" data-export-title="<?php echo $this->lang->line('patient_login_credential'); ?>">
						<thead>					 
							<tr>
								<th><?php echo '#'; ?></th> 
								<th><?php echo $this->lang->line('patient_id'); ?></th>
								<th><?php echo $this->lang->line('patient_name'); ?></th>
								<th><?php echo $this->lang->line('email'); ?></th>
								<th><?php echo $this->lang->line('mobile_number'); ?></th>
								<th><?php echo $this->lang->line('username'); ?></th>
								<th class="text-right"><?php echo $this->lang->line('password'); ?></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>			   
				<button type="submit" class="btn btn-primary pull-right btn-sm mt15" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait') ?>"> <?php echo $this->lang->line('send') ?></button>	  
			</form>			
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- //========datatable start===== -->
<script type="text/javascript">
  ( function ( $ ) {      
      'use strict';
      $(document).ready(function () {
          initDatatable('ajaxlist','admin/bulkmessage/getcredentialdatatable',[],[],100);
      });
  } ( jQuery ) )  
    
</script>
<!-- //========datatable end===== -->

<script type="text/javascript">
    $("#bulkmail").submit(function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var checkCount = $("input[name='patient[]']:checked").length;

        if (checkCount == 0)
        {
            alert("<?php echo $this->lang->line('at_least_one_patient_should_be_selected');?>");
        } else {
                var form = $(this);
                var url = form.attr('action');
                var submit_button = form.find(':submit');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    dataType: "JSON", // serializes the form's elements.
                    beforeSend: function () {
                        submit_button.button('loading');
                    },
                    success: function (data)
                    {
                        var message = "";
                        if (!data.status) {
                            $.each(data.error, function (index, value) {
                                message += value;
                            });
                            errorMsg(message);
                        } else {
                            successMsg(data.message);
							window.location.reload(true);
                        }
                    },
                    error: function (xhr) { // if error occured
                        submit_button.button('reset');
                        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    },
                    complete: function () {
                        submit_button.button('reset');
                    }
                });
        }
    });

    $("input[name='checkAll']").click(function () {
        $("input[name='patient[]']").not(this).prop('checked', this.checked);
    });
</script>