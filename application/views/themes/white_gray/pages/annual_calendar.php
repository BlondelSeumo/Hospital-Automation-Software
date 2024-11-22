<link rel="stylesheet" href="<?php echo base_url(); ?>backend/datepicker/css/bootstrap-datetimepicker.css">
<script src="<?php echo base_url(); ?>backend/datepicker/js/bootstrap-datetimepicker.js"></script>
<div class="row justify-content-center align-items-center flex-wrap d-flex pt20">
 <div class="col-md-6 col-lg-5 col-sm-5">
  <h3 class="entered mt0 pb10"><?php echo $this->lang->line('annual_calendar'); ?></h3>
 </div>
 <div class="col-md-6 col-lg-7 col-sm-7 text-lg-right">
 </div>
</div>

<?php if(!empty($getholidays)){ ?>
 <div class="calender-wrap">
  <div class="row">
  <div class="col-md-12"><h4><?php echo $this->lang->line('holiday'); ?></h4></div>
   <?php foreach($getholidays as $value){ ?>
   <div class="col-md-12">
    <div class="calender-inner">
        <p>
        <strong><?php echo date($this->customlib->YYYYMMDDTodateFormat($value['from_date'])); ?></strong>
          &nbsp; - &nbsp; <?php echo $value['description'];?>
      </p>
    </div>  
   </div>
   <?php  }  ?>
  </div>
 </div>
 <?php  }  ?>
 
 <?php if(!empty($getactivity)){ ?>
 <div class="calender-wrap">
  <div class="row">
  <div class="col-md-12"><h4><?php echo $this->lang->line('activity'); ?></h4></div>
   <?php foreach($getactivity as $value){ ?>
   <div class="col-md-12">
   <div class="calender-inner">
      <p>
      <strong><?php echo date($this->customlib->YYYYMMDDTodateFormat($value['from_date'])); ?></strong>
        &nbsp; - &nbsp; <?php echo $value['description'];?>
      </p>
    </div>  
   </div>
   <?php  }  ?>
  </div>
 </div>
 <?php  }  ?>
 
 <?php if(!empty($getvacation)){ ?>
 <div class="calender-wrap">
  <div class="row">
  <div class="col-md-12"><h4><?php echo $this->lang->line('vacation'); ?></h4></div>
   <?php foreach($getvacation as $value){ ?>
   <div class="col-md-12 calender-inner">
   <div class="calender-inner">
      <p>
      <strong>
        <?php echo date($this->customlib->YYYYMMDDTodateFormat($value['from_date'])); ?> <?php echo $this->lang->line('to'); ?>
        <?php echo date($this->customlib->YYYYMMDDTodateFormat($value['to_date'])); ?></strong>
        &nbsp; - &nbsp; <?php echo $value['description'];?>
      </p>
   </div>  
   </div>
   <?php  }  ?>
  </div>
 </div>
 <?php  }  ?>