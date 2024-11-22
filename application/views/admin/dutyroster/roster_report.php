<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="content-wrapper">
 <!-- Main content -->
 <section class="content">
  <div class="row">
   <div class="col-md-12">
    <div class="box box-primary">
     <!-- <div class="box-header ptbnull"></div> -->
     <div class="box-header with-border">
      <h3 class="box-title"><?php echo $this->lang->line("duty_roster"); ?></h3>
	  
	   <div class="box-tools pull-right">  
			<?php if ($this->rbac->hasPrivilege('roster_shift', 'can_view')) {?>
				<a href="<?php echo base_url("admin/dutyroster/shift"); ?>" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('shift'); ?>  </a>
			<?php } if ($this->rbac->hasPrivilege('roster_list', 'can_view')) {?>
				<a href="<?php echo base_url("admin/dutyroster/rosterlist"); ?>" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('roster'); ?>  </a>
			<?php } if ($this->rbac->hasPrivilege('roster_assign', 'can_view')) {?>
				<a href="<?php echo base_url("admin/dutyroster/rosterassign"); ?>" class="btn btn-primary btn-sm"><i class="fa fa-reorder"></i> <?php echo $this->lang->line('assign_roster'); ?>  </a>	
			<?php } ?>
	   </div>
     </div>     
     <form id='form111' action="<?php echo site_url('admin/dutyroster/roster_report') ?>" method="post" accept-charset="utf-8">
      <?php echo $this->customlib->getCSRF(); ?>
      <div class="box-body row">
        
	   
	   <div class="col-sm-6 col-md-4">
        <div class="form-group"> 
          <label><?php echo $this->lang->line('search_type'); ?></label> 
          <select class="form-control" name="search_type"  id="search_type_select" onchange="showdate(this.value)">
              <option value=""><?php echo $this->lang->line('select') ?></option>
              <?php 
              foreach ($searchlist as $key => $search) { ?>
                  <option value="<?php echo $key ?>" <?php
                                                            if ((isset($search_type)) && ($search_type == $key)) {
                                                                    echo "selected";
                                                                }
                                                                ?>  ><?php echo $search ?></option>
                  <?php }?>
          </select>
          <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
		</div>
       </div>
	   
							<div class="col-sm-6 col-md-3" id="fromdate" style="display: none">
                              <div class="form-group">
                                  <label><?php echo $this->lang->line('date_from'); ?></label><small class="req"> *</small>
                                  <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                  <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                              </div>
							</div>
							<div class="col-sm-6 col-md-3" id="todate" style="display: none">
                              <div class="form-group">
                                  <label><?php echo $this->lang->line('date_to'); ?></label><small class="req"> *</small>
                                  <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to', date($this->customlib->getHospitalDateFormat())); ?>"  />
                                  <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                              </div>
							</div>
	   
	   
	   
       <div class="col-sm-6 col-md-4">
        <div class="form-group">  
         <label><?php echo $this->lang->line("staff"); ?></label><small class="req"> </small>
         <select class="form-control select2" name="staff" style="width: 100%" id="staff">
          <option value=""><?php echo $this->lang->line('select') ?></option>
          <?php foreach($staff_list as $key=>$value){ ?>
          <option value="<?php echo $value['id'];?>" <?php if($staff==$value['id']){ echo "Selected"; } ?>><?php echo $value['name']." ".$value['surname']." (".$value['employee_id'].")";?></option>
          <?php } ?>
         </select>
         <span class="text-danger" id="error_staff"><?php echo form_error('staff'); ?></span>
        </div>
       </div>
       <div class="col-sm-12">
        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
       </div>
      </div>
     </form>
     <div class="box border0 clear">
      
		<div class="pull-right">
			<button type="button" title="<?php echo $this->lang->line('export_to_excel') ?>" onclick="fnExcelReport();" class="btn btn-default dt-button buttons-excel buttons-html5"><i class="fa fa-file-excel-o"></i></button>
            <button type="button" title="<?php echo $this->lang->line('print') ?>" onclick="print_table('myTable');" class="btn btn-default dt-button buttons-print"><i class="fa fa-print"></i></button>
		</div> 
      <div class="box-body table-responsive">
		
					
						
       <div class="download_label"><?php echo $this->lang->line('roster_report'); ?><?php echo $this->lang->line('roster_report'); ?></div>
       <div class="table-responsive" id="myTable">
        <table id="headerTable" class="table table-striped table-bordered table-hover allajaxlist" data-export-title="<?php echo $this->lang->line('roster_report'); ?>">
         <thead>
          <tr>
           <th><?php echo $this->lang->line('staff') ?></th>
           <th><?php echo $this->lang->line('date') ?></th>
           <th><?php echo $this->lang->line('shift_start') ?></th>
           <th><?php echo $this->lang->line('shift_end') ?></th>           
           <th><?php echo $this->lang->line('shift_hour') ?></th>           
           <th><?php echo $this->lang->line('shift') ?></th>           
           <th><?php echo $this->lang->line('department') ?></th>           
           <th><?php echo $this->lang->line('floor') ?></th> 
          </tr>
         </thead>
         <tbody>
        <?php     if(!empty($roster_data)){    
			foreach($roster_data as $key => $value){
				$i = 0;
				 
					foreach($value as $val){					 
					$i++;
                    $duty_date = date('Y-m-d',strtotime($val['roster_duty_date'] ));         
                  ?>
          <tr> 
			<?php if($i == 1){ ?>
           <td valign="middle" >
            <?php echo $val['staff_name']." ".$val['staff_surname']." (".$val['employee_id'].")" ;?>
           </td> 
			<?php }else{ ?>
			<td valign="middle" ></td>
			<?php } ?>
           <td><?php echo $this->customlib->YYYYMMDDTodateFormat($duty_date);?></td>
           <td><?php echo $this->customlib->getHospitalTime_Format($val['shift_start']);?></td>
           <td><?php echo $this->customlib->getHospitalTime_Format($val['shift_end']);?></td>
           <td><?php echo $val['shift_hour'];?></td>
           <td><?php echo $val['shift_name'];?></td>
           <td><?php echo $val['department_name'];?></td>
           <td><?php echo $val['floor_name'];?></td>
          </tr>
          <?php 	  
				}
	   ?>
		 
					<?php  
					 
		
		
		}
		
		}else{
          ?>  
             <tr>
                <td colspan="8"><center><span class="text-danger"> <?php echo $this->lang->line('no_record_found'); ?></span></center></td>
             </tr>
            
		<?php } ?>  
         </tbody>
        </table>
       </div>
	   
      </div>
     </div>
    </div>
   </div>
  </div>
</div>
</section>
</div>

<script type="text/javascript">
    $(document).ready(function (e) {
        $(".select2").select2();
    });
</script>
<script type="text/javascript">
    function print_table(divID) {
        var oldPage = document.body.innerHTML;
        $("#table-heading").html("<?= $this->lang->line("roster_report"); ?>");  
        var divElements = document.getElementById(divID).innerHTML;
        document.body.innerHTML =
          "<html><head><title></title></head><body>" +
          divElements + "</body>";
        window.print();
        document.body.innerHTML = oldPage;
    }

    function fnExcelReport()
    {
        var tab_text = "<table border='2px'><tr >";
        var textRange;
        var j = 0;
        tab = document.getElementById('headerTable'); // id of table

        for (j = 0; j < tab.rows.length; j++)
        {
            tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";           
        }

        tab_text = tab_text + "</table>";
        tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
        tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
        tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");
        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
        {
            txtArea1.document.open("txt/html", "replace");
            txtArea1.document.write(tab_text);
            txtArea1.document.close();
            txtArea1.focus();
            sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
        } else                 //other browser not tested on IE 11
            sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
        return (sa);
    }
</script>
<script type="text/javascript">
	function showdate(value) {
		if (value == 'period') {
                $('#fromdate').show();
                $('#todate').show();
        } else {
                $('#fromdate').hide();
                $('#todate').hide();
		}
	}
		 
		 <?php if($search_type == 'period'){ ?>
			showdate('period');
		 <?php } ?>
</script>