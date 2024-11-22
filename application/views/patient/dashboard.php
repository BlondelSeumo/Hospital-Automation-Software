<script src="<?php echo base_url('/') ?>backend/js/Chart.bundle.js"></script>
<script src="<?php echo base_url('/') ?>backend/js/utils.js"></script>

<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php 
            if(!IsNullOrEmptyString($insurance_validity) && strtotime(date('Y-m-d')) > strtotime($insurance_validity)){
                ?>
                <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="alert alert-danger">
                <?php echo $this->customlib->YYYYMMDDTodateFormat($insurance_validity);?> <?php echo $this->lang->line('was_your_tpa_validity_date_which_is_expired_now_please_renew_your_tpa_and_update_its_status_with_us'); ?>
                </div>
                </div>
                <?php
            }
            ?>
        </div>
      <div class="row">
                <?php if ($this->module_lib->hasPatientActive('opd')) { ?>
                    <div class="col-lg-2 col-md-3 col-sm-6 col20">
                        <div class="info-box" title="OPD">
                            <a href="<?php echo site_url('patient/dashboard/profile'); ?>">
                                <span class="info-box-icon bg-green"><i class="fas fa-stethoscope"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo $this->lang->line('opd'); ?></span>
                                    <span class="info-box-number"><?php echo $total_visits['total_visit']; ?></span>
                                </div>
                            </a>
                        </div>
                    </div><!--./col-lg-2-->
                <?php } ?>
                <?php  if ($this->module_lib->hasPatientActive('ipd')) { ?>
                    <div class="col-lg-2 col-md-3 col-sm-6 col20" title="IPD">
                        <div class="info-box">
                            <a href="<?php echo site_url('patient/dashboard/ipdprofile'); ?>">
                                <span class="info-box-icon bg-green"><i class="fas fa-procedures"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo $this->lang->line('ipd'); ?></span>
                                    <span class="info-box-number"><?php echo $total_ipd['total']; ?></span>
                                </div>
                            </a>
                        </div>
                    </div><!--./col-lg-2-->
                <?php } ?>
                <?php if ($this->module_lib->hasPatientActive('pharmacy')) { ?>
                     <div class="col-lg-2 col-md-3 col-sm-6 col20" title="Pharmacy">
                        <div class="info-box">
                            <a href="<?php echo site_url('patient/dashboard/bill'); ?>">
                                <span class="info-box-icon bg-green"><i class="fas fa-mortar-pestle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo $this->lang->line('pharmacy'); ?></span>
                                    <span class="info-box-number"><?php echo $total_pharmacy['total']; ?></span>
                                </div>
                            </a>
                        </div>
                    </div><!--./col-lg-2-->
                <?php } ?>
                <?php if ($this->module_lib->hasPatientActive('pathology')) { ?>
                    <div class="col-lg-2 col-md-3 col-sm-6 col20" title="Pathology">
                        <div class="info-box">
                            <a href="<?php echo site_url('patient/dashboard/search'); ?>">
                                <span class="info-box-icon bg-green"><i class="fas fa-flask"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo $this->lang->line('pathology'); ?></span>
                                    <span class="info-box-number"><?php echo $total_pathology['total']; ?></span>
                                </div>
                            </a>
                        </div>
                    </div><!--./col-lg-2-->
                <?php } ?>
                <?php if ($this->module_lib->hasPatientActive('radiology')) { ?>
                   <div class="col-lg-2 col-md-3 col-sm-6 col20" title="Radiology">
                        <div class="info-box">
                            <a href="<?php echo site_url('patient/dashboard/radioreport'); ?>">
                                <span class="info-box-icon bg-green"><i class="fas fa-microscope"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo $this->lang->line('radiology'); ?></span>
                                    <span class="info-box-number"><?php echo $total_radiology['total']; ?></span>
                                </div>
                            </a>
                        </div>
                    </div><!--./col-lg-2-->
                <?php } ?>
                <?php if ($this->module_lib->hasPatientActive('blood_bank')) { ?>
                    <div class="col-lg-2 col-md-3 col-sm-6 col20" title="Blood Bank _new">
                        <div class="info-box">
                            <a href="<?php echo site_url('patient/dashboard/bloodbank'); ?>">
                                <span class="info-box-icon bg-green"><i class="fas fa-tint"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo $this->lang->line('blood_bank'); ?></span>
                                    <span class="info-box-number"><?php echo $total_blood_issue['total']; ?></span>
                                </div>
                            </a>
                        </div>
                    </div><!--./col-lg-2-->
                <?php } ?>
                <?php if ($this->module_lib->hasPatientActive('ambulance')) { ?>
                    <div class="col-lg-2 col-md-3 col-sm-6 col20" title="Ambulance">
                        <div class="info-box">
                            <a href="<?php echo site_url('patient/dashboard/ambulance'); ?>">
                                <span class="info-box-icon bg-green"><i class="fas fa-ambulance"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?php echo $this->lang->line('ambulance'); ?></span>
                                    <span class="info-box-number"><?php echo $total_ambulance['total']; ?></span>
                                </div>
                            </a>
                        </div>
                    </div><!--./col-lg-2-->
                <?php } ?>
        </div>
        <div class="row">
        	      <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="box box-info">                      
                        <div class="box-body">
                            <div class="chart"> 
                                <canvas id="medical-history-chart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div><!--./col-lg-7-->
        </div>
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="box box-info">                      
                        <div class="box-body">
                            <div class="chart"> 
                              <canvas id="finding-bar-chart"  height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div><!--./col-lg-7-->       
                  <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="box box-info">                      
                        <div class="box-body">
                            <div class="chart"> 
                              <canvas id="symptom-bar-chart"  height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div><!--./col-lg-7-->
        </div>
    </section>
</div>
 
<script type="text/javascript">
    $(document).ready(function(){
     $.ajax({
    url: baseurl +"patient/dashboard/yearchart",
    type: 'POST',
    data: {},
    dataType: 'json',
    beforeSend: function() {
    
    },
    success: function(data) {
      var ctx = document.getElementById("medical-history-chart").getContext("2d");

        new Chart(ctx, {
  type: 'line',
  data: {
    labels:data.labels,
    datasets: data.dataset,
  },
  options: {
    title: {
      display: true,
      text: "<?php echo $this->lang->line('medical_history'); ?>"
    }
  }
});

    },
    error: function(xhr) { // if error occured
        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
      
    },
    complete: function() {
      
    }

});

$.ajax({
    url: baseurl +"patient/dashboard/findingchart",
    type: 'POST',
    data: {},
    dataType: 'json',
    beforeSend: function() {
    
    },
    success: function(data) {
      var ctx_1 = document.getElementById("finding-bar-chart").getContext("2d");

new Chart(ctx_1, {
  type: 'bar',
  data: {
    labels:data.labels,
    datasets: data.dataset,
  },
    options: {
      legend: { display: false },
      title: {
        display: true,
        text: "<?php echo $this->lang->line('top_ten_findings'); ?>"
      }
    }
});

    },
    error: function(xhr) { // if error occured
        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
      
    },
    complete: function() {
      
    }

});

$.ajax({
    url: baseurl +"patient/dashboard/symptomchart",
    type: 'POST',
    data: {},
    dataType: 'json',
    beforeSend: function() {
    
    },
    success: function(data) {
      var ctx_2 = document.getElementById("symptom-bar-chart").getContext("2d");

new Chart(ctx_2, {
  type: 'pie',
  data: {
    labels:data.labels,
    datasets: data.dataset,
  },
    options: {
      legend: { display: true },
      title: {
        display: true,
        text: "<?php echo $this->lang->line('top_ten_symptoms'); ?>"
      }
    }
});

    },
    error: function(xhr) { // if error occured
        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
      
    },
    complete: function() {
      
    }

});
    });
</script>