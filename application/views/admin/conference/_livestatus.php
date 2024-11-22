<?php
$url      = "";
$extracls = "";
if ($live->purpose == "consult") {
    $name = composeStaffNameByString($live->create_by_name, $live->create_by_surname, $live->create_by_employee_id);
}

if ($live->purpose == "meeting") {
    $name = composeStaffNameByString($live->create_by_name, $live->create_by_surname, $live->create_by_employee_id);
}
?>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="modal-header zoommodal-title">
            <h4 class="modal-title"><?php echo $live->title; ?></h4>
        </div>
    </div>
    <div class="col-lg-4 col-md-4">
        <label>
            <span class="labalblock"> <?php echo $this->lang->line('host'); ?></span> <span class="text-dark robo-normal"><?php echo $name; ?></span>
        </label>
    </div>
    <div class="col-lg-4 col-md-4">
        <label>
            <span class="labalblock"> <?php echo $this->lang->line('date'); ?></span> <span class="text-dark robo-normal"><?php echo date($this->customlib->getHospitalDateFormat(true, true), strtotime($live->date)); ?></span>
        </label>
    </div>
    <div class="col-lg-4 col-md-4">
        <label>
            <span class="labalblock"> <?php echo $this->lang->line('duration_in_minutes'); ?></span> <span class="text-dark robo-normal"><?php echo $live->duration; ?></span>
        </label>
    </div>
</div>

<?php
if ($live->status == 0) {
    if ($live->created_id == $logged_staff_id && ($live->purpose == "meeting")) {
        $label_display = $this->lang->line('start_now');
        $label_type    = 'label-success';
    }if ($live->created_id == $logged_staff_id && ($live->purpose == "consult")) {
        $label_display = $this->lang->line('start_now');
        $label_type    = 'label-success';
    }if ($live->created_id != $logged_staff_id && ($live->purpose == "meeting")) {
        $label_display = $this->lang->line('join_now');
        $label_type    = 'label-success';
    }if ($live->created_id != $logged_staff_id && ($live->purpose == "consult")) {
        $label_display = $this->lang->line('join_now');
        $label_type    = 'label-success';
    }
}

    $target = "";
    if ($conference_setting->use_zoom_app) {
   
        $target = "_blank";
        if ($live->purpose == "consult") {
            $extracls = "join-btn";
            $url      = $live_url->start_url;
        } elseif ($live->purpose == "meeting") {
            if ($live->created_id == $logged_staff_id) {
                $url = $live_url->start_url;
            } else {
                $extracls = "join-btn";
                $url      = $live_url->join_url;
            }
        }
    } else {
        if ($live->purpose == "consult") {
            $url = site_url('admin/zoom_conference/join/consult' . '/' . $live->id);
        } elseif ($live->purpose == "meeting") {
            $url = site_url('admin/zoom_conference/join/meeting' . '/' . $live->id);
        }
    }
?>

<?php
    if (($live->created_id == $logged_staff_id) && $live->purpose == "meeting") {
        ?>
    <div class="row">
     <div class="col-sm-12">
        <div class="zoommodal-border">           
            <a href="<?php echo $url; ?>" class="btn btn-outline-success btn-sm pull-right" target="<?php echo $target; ?>">
                <i class="fa fa-video-camera"></i> <?php echo $this->lang->line('join'); ?>
            </a>
        </div>
    </div>
</div>
        <?php

    } elseif (($live->created_id != $logged_staff_id) && $live->purpose == "meeting") {
        ?>
    <div class="row">
     <div class="col-sm-12">
      <div class="zoommodal-border">           
            <a href="<?php echo $url; ?>" class="btn btn-outline-success btn-sm pull-right <?php echo $extracls; ?>" data-id="<?php echo $live->id; ?>" target="<?php echo $target; ?>">
                <i class="fa fa-video-camera"></i> <?php echo $label_display; ?>
            </a>
        </div>
    </div>
    </div>
        <?php
} else if ($live->purpose == "consult") {
        ?>
         <div class="row">
     <div class="col-sm-12">
        <div class="zoommodal-border">           
            <a href="<?php echo $url; ?>" class="btn btn-outline-success btn-sm pull-right" target="<?php echo $target; ?>">
                <i class="fa fa-video-camera"></i> <?php echo $label_display; ?>
            </a>
        </div>
        </div>
    </div>
        <?php
}

?>