<style type="text/css">
    *{ margin:0; padding: 0;}
    table{ font-family: 'arial'; margin:0; padding: 0;font-size: 12px; color: #000;}
    .tc-container{width: 100%;position: relative; text-align: center;margin-bottom:60px;padding-bottom: 10px;}
    .tcmybg {
        background: top center;
        background-size: contain;
        position: absolute;
        left: 0;
        bottom: 10px;
        width: 160px;
        height: 160x;
        margin-left: auto;
        margin-right: auto;
        right: 0;
        opacity: 0.10;
    }
    .patientmain{background: #efefef;width: 100%; margin-bottom: 30px;}
    .patienttop img{height:30px;vertical-align: initial;}
    .patienttop{background: #0796f5;padding:2px;color: #fff;overflow: hidden;
                position: relative;z-index: 1;}
    .sttext1{font-size: 24px;font-weight: bold;line-height: 30px;}
    .stgray{background: #efefef;padding-top: 5px; padding-bottom: 10px;}
    .staddress{margin-bottom: 0; padding-top: 2px;}
    .stdivider{border-bottom: 2px solid #000;margin-top: 5px; margin-bottom: 5px;}
    .stlist{padding: 0; margin:0; list-style: none;}
    .stlist li{text-align: left;display: inline-block;width: 100%;padding: 2px 5px;}
    .stlist li span{width:65%;float: right;}
    .stimg{
        width: 80px;
        height: auto;
    }
    .stimg img{width: 100%;height: auto;border-radius: 2px;display: block;}
    .staround{padding:3px 10px 3px 0;position: relative;overflow: hidden;}
    .staround2{position: relative; z-index: 9;}
    .stbottom{background: #0796f5;height: 20px;width: 100%;clear: both;margin-bottom: 5px;}
    .principal{margin-top: -40px;margin-right:10px; float:right;}
    .stred{color: #000;}
    .spanlr{padding-left: 5px; padding-right: 5px;}
    .cardleft{width: 20%;float: left;}
    .cardright{width: 77%;float: right; }
    .width32{width: 32.75%; padding: 3px; float: left;}
    .barcode{display:block; text-align: center;  margin-top: 1px;}
    .vertlist{padding: 0; margin:0; list-style: none;}
    .vertlist li{text-align: left;display: inline-block;width: 100%;color: #000;padding-bottom: 2px;}
    .vertlist li span{width:55%;float: right;}
    .barcodeimg{ width: 80px; height: auto; }
    .barcodeimg img{width: 100%;height: auto;border-radius: 2px;display: block;margin-top:2px;}
</style>

<?php
$hospital = $sch_setting[0];
$i = 0;
?>

<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <?php
        foreach ($patients as $patient) {
            $i++;
            ?>
            <td valign="top" class="width32">
                <table cellpadding="0" cellspacing="0" width="100%" class="tc-container" style="background: #f0f8fd;">
                    <tr>
                        <td valign="top">
                            <?php if(!empty($patient_id_card[0]->background)){ ?>
                            <img src="<?php echo base_url('uploads/patient_id_card/background/' . $patient_id_card[0]->background.img_time()); ?>" class="tcmybg" />
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div class="patienttop" style="background: <?php echo $patient_id_card[0]->header_color ?>">

                                <div class="sttext1">
                                <?php if(!empty($patient_id_card[0]->logo)){ ?>
                                <img src="<?php echo base_url('uploads/patient_id_card/logo/' . $patient_id_card[0]->logo.img_time()); ?>"  />
                                <?php } ?>
                                    <?php echo $patient_id_card[0]->hospital_name ?></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="text-center" style="padding:5px 0; position: relative; z-index: 1">
                            <p>  <?php echo $patient_id_card[0]->hospital_address ?></p>

                        </td>
                    </tr>
                    <tr>
                        <td valign="top" style="color: #fff;font-size: 16px; padding: 2px 0 0; position: relative; z-index: 1;background: <?php echo $patient_id_card[0]->header_color ?>;text-transform: uppercase;"><?php echo $patient_id_card[0]->title ?></td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div class="staround">
                                <div class="cardleft">
                                    <div class="stimg">
                                        <?php if(!empty($patient->image)){ ?>
                                        <img src="<?php echo base_url($patient->image.img_time()); ?>" class="img-responsive" />
                                        <?php } ?>
                                    </div>
                                    <?php if ($patient_id_card[0]->enable_barcode == 1) { ?>
                                    <div class="barcodeimg"><img src="<?php echo base_url($patient->barcode); ?>" /></div>
                                    <?php } ?>
                                </div><!--./cardleft-->
                                <div class="cardright">
                                    <ul class="stlist">
                                        <?php if ($patient_id_card[0]->enable_patient_name == 1) { ?><li><?php echo $this->lang->line('patient_name'); ?><span> <?php echo $patient->patient_name." (".$patient->id.")"; ?></span></li><?php } ?>
                                        
                                        <?php if ($patient_id_card[0]->enable_guardian_name == 1) { ?><li><?php echo $this->lang->line('guardian_name'); ?><span><?php echo $patient->guardian_name; ?></span></li><?php } ?>

                                        <?php if ($patient_id_card[0]->enable_address == 1) { ?><li><?php echo $this->lang->line('address')?><span><?php echo $patient->address; ?></span></li><?php } ?>

                                        <?php if ($patient_id_card[0]->enable_phone == 1) { ?><li>Phone<span><?php echo $patient->mobileno; ?></span></li><?php } ?>
                                        <?php
                                        if ($patient_id_card[0]->enable_dob == 1) {
                                            ?>
                                            <li><?php echo $this->lang->line('date_of_birth');?>
                                                <span>
                                                    <?php
                                                    echo $dob = "";
                                                    if ($patient->dob != "0000-00-00" && $patient->dob !="") {
                                                        $dob = date($this->customlib->getHospitalDateFormat(true, false), strtotime($patient->dob));
                                                    }
                                                    echo $dob;
                                                    ?>
                                                </span></li>
                                            <?php
                                        }
                                        ?>

                                        <?php if ($patient_id_card[0]->enable_blood_group == 1) { ?><li class="stred"><?php echo $this->lang->line('blood_group'); ?><span><?php echo $patient->blood_group; ?></span></li><?php } ?>
                                    </ul>
                                </div><!--./cardright-->
                            </div><!--./staround-->
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="text-right" class="principal">
                        <?php if(!empty($patient_id_card[0]->sign_image)){ ?>
                        <img src="<?php echo base_url('uploads/patient_id_card/signature/' . $patient_id_card[0]->sign_image.img_time()); ?>" width="66" height="40" />
                        <?php } ?>
                        </td>
                    </tr>
                </table>
            </td>

            <?php
            if ($i == 3) {
                // three items in a row. Edit this to get more or less items on a row
                ?></tr><tr><?php
                $i = 0;
            }
        }
        ?>
    </tr>
</table>