<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<form id="patient_discharge" accept-charset="utf-8" action="<?php echo base_url() ?>admin/bill/add_discharge" method="post" enctype="multipart/form-data">
    <input type="hidden" name="opd_id" value="<?php echo $opd_id; ?>" class="form-control" >
    <input type="hidden" name="id" value="<?php if (!empty($discharge_card)) {echo $discharge_card['id'];}?>" class="form-control">
    <input type="hidden" name="ipd_id" value="<?php echo $ipd_id; ?>"  class="form-control" >
    <input type="hidden" name="case_reference_id" value="<?php echo $case_id; ?>" class="form-control">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class=" alert alert-warning"><?php echo $this->lang->line("please_note_that_before_discharging_check_patient_bill"); ?></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label><?php echo $this->lang->line('discharge_date'); ?></label><small class="req"> *</small>
                <input type="text" name="discharge_date" value="<?php if ((!empty($discharge_card)) && $discharge_card['discharge_date'] != '') {echo $this->customlib->YYYYMMDDHisTodateFormat($discharge_card['discharge_date'], $this->time_format);}?>" class="form-control datetime" autocomplete="off">
                <span class="text-danger"></span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><?php echo $this->lang->line('discharge_status'); ?><small class="req"> *</small> </label>
                <select class="form-control death_status" name="death_status">
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <?php foreach ($death_status as $key => $value) {   ?>
                    <option <?php if ((!empty($discharge_card)) && $discharge_card['discharge_status'] == $key) {echo "selected";}?> value="<?php echo $key ?>" ><?php echo $value ?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label for="email"><?php echo $this->lang->line('note'); ?></label>
                <textarea name="note" id="note" class="form-control" ><?php if (!empty($discharge_card)) {echo $discharge_card['note'];}?></textarea>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="email"><?php echo $this->lang->line('operation'); ?></label>
                <textarea name="operation" id="operation" class="form-control" ><?php if (!empty($discharge_card)) {echo $discharge_card['operation'];}?></textarea>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="email"><?php echo $this->lang->line('diagnosis'); ?></label>
                <textarea name="diagnosis" id="diagnosis" class="form-control" ><?php if (!empty($discharge_card)) {echo $discharge_card['diagnosis'];}?></textarea>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="email"><?php echo $this->lang->line('investigation'); ?></label>
                <textarea name="investigations" id="investigations" class="form-control" ><?php if (!empty($discharge_card)) {echo $discharge_card['investigations'];}?></textarea>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="email"><?php echo $this->lang->line('treatment_home'); ?></label>
                <textarea name="treatment_home" id="treatment_home" class="form-control" ><?php if (!empty($discharge_card)) {echo $discharge_card['treatment_home'];}?></textarea>
            </div>
        </div>
    </div>
    <div class="row death_status_div" style="display: none;">
        <div class="col-md-6">
            <div class="form-group">
                <label><?php echo $this->lang->line('death_date'); ?></label><small class="req"> *</small>
                <input type="text" value="<?php if ((!empty($discharge_card)) && $discharge_card['death_date'] != '') {echo $this->customlib->YYYYMMDDHisTodateFormat($discharge_card['death_date'], $this->time_format);}?>" style="z-index: 1700;" name="death_date" id="death_date" class="form-control datetime">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label><?php echo $this->lang->line('guardian_name'); ?></label><small class="req"> *</small>
                <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                <input type="hidden" name="deathrecord_id" value="<?php if (!empty($deathrecord)) {echo $deathrecord['id'];}?>">
                <input type="text" value="<?php echo $guardian_name; ?>" name="guardian_name" id="guardian_name" class="form-control">
                <span class="text-danger"><?php echo form_error('guardian_name'); ?></span>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="email"><?php echo $this->lang->line('attachment'); ?></label>
                <input type="file" name="document" id="document" class="form-control filestyle" >
                <span class="text-danger"><?php echo form_error('document'); ?></span>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="email"><?php echo $this->lang->line('report'); ?></label>
                <textarea name="death_report" id="death_report" class="form-control" ><?php if (!empty($deathrecord)) {echo $deathrecord['death_report'];}?></textarea>
            </div>
        </div>
        <div>
            <?php
            echo display_custom_fields('death_report');
            ?>
        </div>
    </div>
    <div class="row reffer_div" style="display: none;">
        <div class="col-md-4">
            <div class="form-group">
                <label><?php echo $this->lang->line('referral_date'); ?></label><small class="req"> *</small>
                <input type="text" value="<?php if ((!empty($discharge_card)) && $discharge_card['refer_date'] != '') {echo $this->customlib->YYYYMMDDHisTodateFormat($discharge_card['refer_date'], $this->time_format);}?>" name="referral_date" id="referral_date" class="form-control datetime">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label><?php echo $this->lang->line('referral_hospital_name'); ?> </label><small class="req"> *</small>
                <input type="text" value="<?php if ((!empty($discharge_card)) && $discharge_card['refer_to_hospital'] != '') {echo $discharge_card['refer_to_hospital'];}?>" name="hospital_name" id="hospital_name" class="form-control ">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label><?php echo $this->lang->line('reason_for_referral'); ?></label>
                <input type="text" value="<?php if ((!empty($discharge_card)) && $discharge_card['reason_for_referral'] != '') {echo $discharge_card['reason_for_referral'];}?>" name="reason_for_referral" id="reason_for_referral" class="form-control ">
            </div>
        </div>
    </div>
    <?php if ($this->rbac->hasPrivilege('opd_patient_discharge', 'can_edit')) {?>
    <div class="row">
        <div class="box-footer col-md-12">
            <div class="pull-right">
                <button id="add_paymentbtn" type="submit" data-loading-text="<?php echo $this->lang->line('processing'); ?>" class="btn btn-info pull-right printsavebtn"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
            </div>
        </div>
    </div>
    <?php }?>
</form>
            
<script type="text/javascript">
    $('.death_status').trigger("change");
    var download = "";
    <?php if ((!empty($deathrecord)) && $deathrecord['attachment'] != "") {?>

        var download = ' <a title="<?php echo $this->lang->line('download'); ?>" href="<?php echo site_url('admin/birthordeath/download_deathrecord/' . $deathrecord['id']); ?> "   data-recordId="" ><i class="fa fa-download"></i> </a>&nbsp; ' ;
        <?php }?>
    <?php if ((!empty($discharge_card))) {?>

$('#allpayments_print').html(' <a href="javascript:void(0);" title="<?php echo $this->lang->line('print'); ?>"  class="print_dischargecard" data-recordId="<?php echo $discharge_card['id']; ?>" data-case_id="<?php echo $case_id; ?>" ><i class="fa fa-print"></i> </a>&nbsp; '+download);

<?php }?>
</script>